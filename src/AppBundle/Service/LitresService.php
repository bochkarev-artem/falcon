<?php
/**
 * @author Artem Bochkarev
 */
namespace AppBundle\Service;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Sequence;
use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Monolog\Logger;
use Psr\Log\LogLevel;

/**
 * Class LitresService
 * @package AppBundle\Service
 */
class LitresService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $authorRepo;

    /**
     * @var EntityRepository
     */
    private $genreRepo;

    /**
     * @var EntityRepository
     */
    private $tagRepo;

    /**
     * @var EntityRepository
     */
    private $sequenceRepo;

    /**
     * @var EntityRepository
     */
    private $bookRepo;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var int $perPage
     */
    private $perPage = 100;

    /**
     * @var int $batchSize
     */
    private $batchSize = 10;

    /**
     * @var int $bookExistedCount
     */
    private $bookExistedCount = 300;

    /**
     * @var bool $debug
     */
    private $debug;

    /**
     * @var integer $skipped
     */
    private $skipped;

    /**
     * @var integer $step
     */
    private $step;

    /**
     * @var array $locales
     */
    private $locales;

    /**
     * @var ImageUploadService $imageUploadService
     */
    private $imageUploadService;

    /**
     * @param EntityManager      $em
     * @param Logger             $logger
     * @param ImageUploadService $imageUploadService
     * @param array              $locales
     */
    public function __construct
    (
        EntityManager $em,
        Logger $logger,
        ImageUploadService $imageUploadService,
        $locales
    ) {
        $this->em = $em;
        $this->logger = $logger;
        $this->authorRepo = $this->em->getRepository('AppBundle:Author');
        $this->genreRepo = $this->em->getRepository('AppBundle:Genre');
        $this->sequenceRepo = $this->em->getRepository('AppBundle:Sequence');
        $this->bookRepo = $this->em->getRepository('AppBundle:Book');
        $this->tagRepo = $this->em->getRepository('AppBundle:Tag');
        $this->locales = $locales;
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * @param string  $param
     * @param string  $debug
     *
     * @return bool
     */
    public function getData($param, $debug)
    {
        $this->debug = $debug == 'y' ? true : false;

        switch ($param) {
            case 'books':
                return $this->getBooksData();
                break;
            case 'genres':
                return $this->getGenresData();
                break;
            default:
                return false;
        }
    }

    /**
     * @param string $endpoint
     *
     * @throws \ErrorException
     * @return bool
     */
    public function getGenresData($endpoint = 'http://robot.litres.ru/pages/catalit_genres/')
    {
        $xml    = $this->getXml($endpoint);
        $genres = [];
        foreach ($xml->genre as $genreNode) {
            $parentGenre = new Genre();
            $parentTitle = $this->mbUcfirstOnly($genreNode['title']);
            $parentGenre->setTitleRu($parentTitle);
            $parentGenre->setLitresId(0);
            $this->em->persist($parentGenre);
            $this->em->flush();
            foreach ($genreNode as $node) {
                $id    = (integer) $node['id'];
                $token = (string) $node['token'];
                $title = $this->mbUcfirstOnly($node['title']);
                if (!is_null($id)) {
                    /** @var Genre $genre */
                    if ($genre = $this->genreRepo->findOneBy(['token' => $token])) {
                        if (!$genre->getTitleRu()) {
                            $genre->setTitleRu($title);
                        }

                        if (!$genre->getLitresId()) {
                            $genre->setLitresId($id);
                        }
                    } else {
                        $genre = new Genre();
                        $genre
                            ->setLitresId($id)
                            ->setTitleRu($title)
                            ->setToken($token)
                            ->setParent($parentGenre)
                        ;
                    }
                    $genres[$token] = $genre;
                }
            }
        }

        foreach ($genres as $genre) {
            if (is_null($genre->getId())) {
                $this->em->persist($genre);
            }
        }

        $this->em->flush();
        $this->em->clear();

        return true;
    }

    /**
     * @param \SimpleXMLElement $sequence
     *
     * @return Sequence|null
     */
    public function getSequence($sequence)
    {
        $sequenceId   = (integer) $sequence['id'];
        $sequenceName = (string) $sequence['name'];
        $sequence     = $this->sequenceRepo->findOneBy(['litresId' => $sequenceId]);

        if (!$sequence && $sequenceId) {
            $sequence = new Sequence();
            $sequence->setLitresId($sequenceId);
            $sequence->setName($sequenceName);
            if (preg_match("/[у|е|ы|а|о|э|я|и|ю]/", $sequenceName)) {
                $sequence->setLang('ru');
            } else {
                $sequence->setLang('en');
            }

            $this->em->persist($sequence);
            $this->em->flush();
        }

        return $sequence;
    }

    /**
     * @param string $authorId
     * @param \SimpleXMLElement $subject
     *
     * @return Author|false
     */
    public function getAuthor($authorId, $subject)
    {
        $author = $this->authorRepo->findOneBy(['documentId' => $authorId]);
        if (!$author) {
            $fName = (string) $subject->{'first-name'};
            $mName = (string) $subject->{'middle-name'};
            $lName = (string) $subject->{'last-name'};

            if (!($fName || $mName || $lName)) { // no real name
                return false;
            }

            if (strlen($fName) > 250 || strlen($lName) > 250) {
                return false;
            }

            $author = new Author;
            $author
                ->setDocumentId((string) $authorId)
                ->setFirstName($fName)
                ->setMiddleName($mName)
                ->setLastName($lName)
            ;

            if (preg_match("/[у|е|ы|а|о|э|я|и|ю]/", $fName . $lName)) {
                $author->setLang('ru');
            } else {
                $author->setLang('en');
            }

            $this->em->persist($author);
            $this->em->flush();
        }

        return $author;
    }

    /**
     * @param string $genreToken
     *
     * @return Genre|null
     */
    public function getGenre($genreToken)
    {
        $genre = $this->genreRepo->findOneBy(['token' => $genreToken]);

        return $genre;
    }

    /**
     * @param \SimpleXMLElement $tag
     *
     * @return Tag|null
     */
    public function getTag($tag)
    {
        $tagId    = $tag['id'];
        $tagTitle = $this->mbUcfirst($tag['tag_title']);
        $tag      = $this->tagRepo->findOneBy(['litresId' => $tagId]);
        if (!$tag && $tagId) {
            $tag = new Tag();
            $tag->setLitresId($tagId);
            $tag->setTitle($tagTitle);

            $this->em->persist($tag);
            $this->em->flush();
        }

        return $tag;
    }

    /**
     * @param string $endpoint
     *
     * @throws \ErrorException
     * @return bool
     */
    public function getBooksData($endpoint = 'http://robot.litres.ru/pages/catalit_browser/')
    {
        for ($i = 0; $i < 1000; $i++) {
            $start = $i * $this->perPage + 1;
            $xml   = $this->getXml($endpoint . "?limit=$start,$this->perPage");
            $this->iterateBooks($xml->{'fb2-book'});

            if ($this->skipped >= $this->bookExistedCount) {
                break;
            }
        }

        $numberProcessed = $i * $this->perPage - $this->skipped;
        if ($this->logger && $this->debug) {
            $this->logger->log(
                LogLevel::INFO,
                sprintf('%s books flushed', $numberProcessed)
            );
        }
        echo ">>> $numberProcessed books flushed, $this->skipped skipped\n";

        $this->em->flush();
        $this->em->clear();

        return true;
    }

    /**
     * @param \SimpleXMLElement[] $books
     */
    public function iterateBooks($books)
    {
        foreach ($books as $data) {
            if (++$this->step % $this->batchSize === 0) {
                $this->em->flush();
                $this->em->clear();
                if ($this->debug) {
                    echo ">>> books processed ($this->step), skipped ($this->skipped)\n";
                }
            }
            $hubId = (string)$data['hub_id'];
            if ($book = $this->bookRepo->findOneBy(['litresHubId' => $hubId])) {
                $this->skipped++;
                continue;
            }

            $annotation = '';
            $book = new Book;
            $titleInfo = $data->{'text_description'}->hidden->{'title-info'};
            $title = (string)$titleInfo->{'book-title'};
            if (strlen($title) > 120) {
                $this->skipped++;
                continue;
            }

            $lang = (string)$titleInfo->lang;
            if (strlen($lang) != 0 && !in_array($lang, $this->locales)) {
                $this->skipped++;
                continue;
            }
            $documentInfo = $data->{'text_description'}->hidden->{'document-info'};
            $publishInfo = $data->{'text_description'}->hidden->{'publish-info'};

            $author = null;
            foreach ($titleInfo->author as $author) {
                $authorId = $author->id;
                $author = $this->getAuthor($authorId, $author);
                if ($author) {
                    $book->addAuthor($author);
                } else {
                    if ($this->logger && $this->debug) {
                        $this->logger->log(
                            LogLevel::CRITICAL,
                            sprintf('Author %s not found', $authorId)
                        );
                    }

                    $this->skipped++;
                    continue 2;
                }
            }

            if ($author === null) {
                continue;
            }

            $genres = [];
            foreach ($titleInfo->genre as $token) {
                $token = (string)$token;
                $genres[$token] = $token; // To exclude duplicated
            }

            foreach ($genres as $token) {
                $genre = $this->getGenre($token);
                if ($genre) {
                    $book->addGenre($genre);
                } else {
                    if ($this->logger && $this->debug) {
                        $this->logger->log(
                            LogLevel::CRITICAL,
                            sprintf('Genre %s not found', $genre)
                        );
                    }

                    $this->skipped++;
                    continue 2;
                }
            }

            foreach ($data->{'art_tags'}->tag as $tag) {
                $tag = $this->getTag($tag);
                if ($tag) {
                    $book->addTag($tag);
                }
            }

            if ($data->{'sequences'}) {
                foreach ($data->{'sequences'}->sequence as $sequence) {
                    $sequenceNumber = (integer)$sequence['number'];
                    $sequence = $this->getSequence($sequence);
                    if ($sequence) {
                        $book->setSequence($sequence);
                        $book->setSequenceNumber($sequenceNumber);
                        break;
                    }
                }
            }

            if ($titleInfo->annotation) {
                $annotation = strip_tags($titleInfo->annotation->asXML(), '<p><br>');
            }

            /** @var Author $mainAuthor */
            $mainAuthor = $book->getAuthors()->first();
            $title = substr((string)$titleInfo->{'book-title'}, 0, 254);
            if (preg_match("/[у|е|ы|а|о|э|я|и|ю]/", $title)) {
                $bookLocale = 'ru';
            } else {
                $bookLocale = 'en';
            }

            $sequence = $book->getSequence();
            if ($sequence && $sequence->getLang() == 'ru' || $mainAuthor->getLang() == 'ru' || $bookLocale == 'ru') {
                $book->setLang('ru');
            } else {
                $book->setLang('en');
            }

            $book
                ->setLitresHubId($hubId)
                ->setCover((string)$data['cover'])
                ->setPrice((string)$data['base_price'])
                ->setHasTrial((string)$data['has_trial'])
                ->setTitle($title)
                ->setAnnotation($annotation)
                ->setDocumentId((string)$documentInfo->id)
                ->setPublisher((string)$publishInfo->publisher)
                ->setCityPublished((string)$publishInfo->city)
                ->setIsbn((string)$publishInfo->isbn)
                ->setMainAuthorSlug($mainAuthor->getSlug());

            $this->imageUploadService->updateBookCover($book);

            $yearPublished = (string)$publishInfo->year;
            if (strlen($yearPublished) < 5) {
                $book->setYearPublished($yearPublished);
            }

            $date = (string)$titleInfo->date['value'];
            if ($date && $date != '0000-00-00') {
                $date = new \DateTime($date);
                $book->setDate($date);
            }

            $this->em->persist($book);
            if ($this->debug) {
                echo ">>> book persisted ($this->step)\n";
            }
        }
    }

    /**
     * @param string $endpoint
     *
     * @return \SimpleXMLElement
     */
    private function getXml($endpoint)
    {
        return simplexml_load_string(mb_convert_encoding(gzdecode(file_get_contents($endpoint)), 'utf-8'));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function mbUcfirstOnly($string)
    {
        $string = mb_strtolower($string);

        return $this->mbUcfirst($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function mbUcfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }
}
