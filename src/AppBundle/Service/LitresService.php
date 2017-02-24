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
    private $perPage = 125;

    /**
     * @var int $batchSize
     */
    private $batchSize = 100;

    /**
     * @var int $bookExistedCount
     */
    private $bookExistedCount = 500;

    /**
     * @var bool $debug
     */
    private $debug;

    /**
     * @param EntityManager $em
     * @param Logger        $logger
     */
    public function __construct(EntityManager $em, Logger $logger)
    {
        $this->em           = $em;
        $this->logger       = $logger;
        $this->authorRepo   = $this->em->getRepository('AppBundle:Author');
        $this->genreRepo    = $this->em->getRepository('AppBundle:Genre');
        $this->tagRepo      = $this->em->getRepository('AppBundle:Tag');
        $this->sequenceRepo = $this->em->getRepository('AppBundle:Sequence');
        $this->bookRepo     = $this->em->getRepository('AppBundle:Book');
    }

    /**
     * @param string $param
     * @param string $debug
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
            $parentGenre->setTitle($parentTitle);
            $parentGenre->setLitresId(0);
            $this->em->persist($parentGenre);
            $this->em->flush();
            foreach ($genreNode as $node) {
                $id    = (integer) $node['id'];
                $token = (string) $node['token'];
                $title = $this->mbUcfirstOnly($node['title']);
                if (!is_null($id)) {
                    /** @var Genre $genre */
                    if ($genre = $this->genreRepo->findOneByToken($token)) {
                        if (!$genre->getTitle()) {
                            $genre->setTitle($title);
                        }

                        if (!$genre->getLitresId()) {
                            $genre->setLitresId($id);
                        }
                    } else {
                        $genre = new Genre();
                        $genre
                            ->setLitresId($id)
                            ->setTitle($title)
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
     * @param string $documentId
     * @param string $endpoint
     *
     * @throws \ErrorException
     * @return Author|boolean
     */
    public function getAuthorData($documentId, $endpoint = 'http://robot.litres.ru/pages/catalit_persons/')
    {
        $description = '';
        $endpoint    = $endpoint . '?person=' . $documentId;
        $xml         = $this->getXml($endpoint);
        $author      = new Author();
        $subject     = $xml->{'subject'};

        if (!$xml->{'subject'}) {
            return false;
        }

        $litresId = (integer) $subject['hub_id'];
        if (!$litresId) { // no author
            return false;
        }

        $fName = (string) $subject->{'first-name'};
        $mName = (string) $subject->{'middle-name'};
        $lName = (string) $subject->{'last-name'};

        if (!($fName || $mName || $lName)) { // no real name
            return false;
        }

        if ($subject->{'text_descr_html'}->hidden) {
            $description = strip_tags($subject->{'text_descr_html'}->hidden->asXML(), '<p><br>');
        }

        $author
            ->setDocumentId((string) $subject['id'])
            ->setLitresHubId($litresId)
            ->setLevel((integer) $subject->{'level'})
            ->setArtsCount((integer) $subject->{'arts-count'})
            ->setFirstName($fName)
            ->setMiddleName($mName)
            ->setLastName($lName)
            ->setDescription($description)
            ->setPhoto((string) $subject->{'photo'})
            ->setReviewCount((integer) $subject->{'recenses-count'})
        ;

        $this->em->persist($author);
        $this->em->flush();

        return $author;
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
        $sequence     = $this->sequenceRepo->findOneByLitresId($sequenceId);

        if (!$sequence && $sequenceId) {
            $sequence = new Sequence();
            $sequence->setLitresId($sequenceId);
            $sequence->setName($sequenceName);

            $this->em->persist($sequence);
            $this->em->flush();
        }

        return $sequence;
    }

    /**
     * Let`s not create Author from Book data
     * @param string $authorId
     *
     * @return Author|false
     */
    public function getAuthor($authorId)
    {
        $author = $this->authorRepo->findOneByDocumentId($authorId);
        if (!$author) {
            $author = $this->getAuthorData($authorId);
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
        $genre = $this->genreRepo->findOneByToken($genreToken);
        if (!$genre && $genreToken) {
            $genre = new Genre();
            $genre->setToken($genreToken);
            $genre->setTitle($genreToken);

            $this->em->persist($genre);
            $this->em->flush();
        }

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
        $tag      = $this->tagRepo->findOneByLitresId($tagId);
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
        $skipped = 0;
        $step    = 0;
        for ($i = 0; $i < 830; $i++) {
            $start = $i * $this->perPage + 1;
            $xml   = $this->getXml($endpoint . "?limit=$start,$this->perPage");
            foreach ($xml->{'fb2-book'} as $data) {
                $step++;
                $hubId = (string)$data['hub_id'];
                if ($book = $this->bookRepo->findOneByLitresHubId($hubId)) {
                    /** @var Book $book */
                    if ($this->debug) {
                        echo ">>> " . $book->getId() . " book id already exists ($step)\n";
                    }
                    $skipped++;

                    continue;
                }

                $annotation = '';
                $book = new Book;
                $titleInfo = $data->{'text_description'}->hidden->{'title-info'};
                $documentInfo = $data->{'text_description'}->hidden->{'document-info'};
                $publishInfo = $data->{'text_description'}->hidden->{'publish-info'};

                foreach ($titleInfo->author as $author) {
                    $authorId = $author->id;
                    $author = $this->getAuthor($authorId);
                    if ($author) {
                        $book->addAuthor($author);
                    } else {
                        if ($this->logger && $this->debug) {
                            $this->logger->log(
                                LogLevel::CRITICAL,
                                sprintf('Author %s not found', $authorId)
                            );
                        }

                        $skipped++;
                        continue 2;
                    }
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

                $book
                    ->setLitresHubId($hubId)
                    ->setCover((string)$data['cover'])
                    ->setPrice((string)$data['base_price'])
                    ->setRating((string)$data['rating'])
                    ->setReviewCount((string)$data['recenses'])
                    ->setHasTrial((string)$data['has_trial'])
                    ->setTitle(substr((string)$titleInfo->{'book-title'}, 0, 254))
                    ->setAnnotation($annotation)
                    ->setLang((string)$titleInfo->lang)
                    ->setDocumentId((string)$documentInfo->id)
                    ->setPublisher((string)$publishInfo->publisher)
                    ->setYearPublished((string)$publishInfo->year)
                    ->setCityPublished((string)$publishInfo->city)
                    ->setIsbn((string)$publishInfo->isbn)
                    ->setMainAuthorSlug($mainAuthor->getSlug())
                ;

                $date = (string)$titleInfo->date['value'];
                if ($date && $date != '0000-00-00') {
                    $date = new \DateTime($date);
                    $book->setDate($date);
                }

                $this->em->persist($book);
                if ($this->debug) {
                    echo ">>> book persisted ($step)\n";
                }

                if ($step % $this->batchSize === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }
            }

            if ($skipped >= $this->bookExistedCount) {
                break;
            }
        }

        $numberProcessed = $i * $this->perPage - $skipped;
        if ($this->logger && $this->debug) {
            $this->logger->log(
                LogLevel::INFO,
                sprintf('%s books flushed', $numberProcessed)
            );
        }
        echo ">>> $numberProcessed books flushed, $skipped skipped\n";

        $this->em->flush();
        $this->em->clear();

        return true;
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