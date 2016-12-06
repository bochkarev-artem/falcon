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
    private $perPage = 50;

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
            $parentTitle = (string) $genreNode['title'];
            $parentGenre->setTitle($parentTitle);
            $this->em->persist($parentGenre);
            $this->em->flush();
            $parentId = $parentGenre->getId();
            foreach ($genreNode as $node) {
                $id    = (integer) $node['id'];
                $token = str_replace('_', '-', (string) $node['token']);
                $title = (string) $node['title'];
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
                            ->setParentId($parentId)
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
            foreach ($subject->{'text_descr_html'}->hidden->p as $p) {
                $description .= '<p>' . (string) $p . '</p>';
            }
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
            ->setRecensesCount((integer) $subject->{'recenses-count'})
        ;

        $this->em->persist($author);
        $this->em->flush();

        return $author;
    }

    /**
     * @param \SimpleXMLElement $sequence
     *
     * @return Sequence
     */
    public function getSequence($sequence)
    {
        $sequenceId     = (integer) $sequence['id'];
        $sequenceName   = (string) $sequence['name'];
        $sequenceNumber = (integer) $sequence['number'];
        $sequence       = $this->sequenceRepo->findOneByLitresId($sequenceId);
        if (!$sequence) {
            $sequence = new Sequence();
            $sequence->setLitresId($sequenceId);
            $sequence->setName($sequenceName);
            $sequence->setNumber($sequenceNumber);

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
     * @return Genre
     */
    public function getGenre($genreToken)
    {
        $genre = $this->genreRepo->findOneByToken($genreToken);
        if (!$genre) {
            $genre = new Genre();
            $genre->setToken($genreToken);

            $this->em->persist($genre);
            $this->em->flush();
        }

       return $genre;
    }

    /**
     * @param \SimpleXMLElement $tag
     *
     * @return Tag
     */
    public function getTag($tag)
    {
        $tagId    = $tag['id'];
        $tagTitle = $tag['tag_title'];
        $tag      = $this->tagRepo->findOneByLitresId($tagId);
        if (!$tag) {
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
        for ($i = 0; $i < 10; $i++) {
            $start = $i * $this->perPage + 1;
            $xml   = $this->getXml($endpoint . "?limit=$start,$this->perPage");

            foreach ($xml->{'fb2-book'} as $data) {
                $step++;
                $hubId = (string) $data['hub_id'];
                if ($book = $this->bookRepo->findOneByLitresHubId($hubId)) {
                    /** @var Book $book */
                    if ($this->debug) {
                        echo ">>> " . $book->getId() . " book already exists ($step)\n";
                    }
                    $skipped++;

                    continue;
                }
                $annotation   = '';
                $book         = new Book;
                $titleInfo    = $data->{'text_description'}->hidden->{'title-info'};
                $documentInfo = $data->{'text_description'}->hidden->{'document-info'};
                $publishInfo  = $data->{'text_description'}->hidden->{'publish-info'};

                foreach ($titleInfo->author as $author) {
                    $authorId = $author->id;
                    $author   = $this->getAuthor($authorId);
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
                    $token = (string) $token;
                    $genres[$token] = $token; // To exclude duplicated
                }
                foreach ($genres as $token) {
                    $genre = $this->getGenre($token);
                    $book->addGenre($genre);
                }
                foreach ($data->{'art_tags'}->tag as $tag) {
                    $tag = $this->getTag($tag);
                    $book->addTag($tag);
                }
                if ($data->{'sequences'}) {
                    foreach ($data->{'sequences'}->sequence as $sequence) {
                        $sequence = $this->getSequence($sequence);
                        $book->addSequence($sequence);
                    }
                }
                if ($titleInfo->annotation) {
                    foreach ($titleInfo->annotation->p as $p) {
                        $annotation .= '<p>' . (string) $p . '</p>';
                    }
                }
                if ($titleInfo->reader) {
                    $book->setReader((string) $titleInfo->reader->nickname);
                }

                $book
                    ->setLitresHubId($hubId)
                    ->setType((string) $data['type'])
                    ->setCover((string) $data['cover'])
                    ->setCoverPreview((string) $data['cover_preview'])
                    ->setFilename((string) $data['filename'])
                    ->setPrice((string) $data['base_price'])
                    ->setRating((string) $data['rating'])
                    ->setRecensesCount((string) $data['recenses'])
                    ->setHasTrial((string) $data['has_trial'])
                    ->setType((string) $data['type'])
                    ->setTitle(substr((string) $titleInfo->{'book-title'}, 0, 254))
                    ->setAnnotation($annotation)
                    ->setLang((string) $titleInfo->lang)
                    ->setDate((string) $titleInfo->date['value'])
                    ->setDocumentId((string) $documentInfo->id)
                    ->setPublisher((string) $publishInfo->publisher)
                    ->setYearPublished((string) $publishInfo->year)
                    ->setCityPublished((string) $publishInfo->city)
                    ->setIsbn((string) $publishInfo->isbn)
                ;

                $this->em->persist($book);
                if ($this->debug) {
                    echo ">>> " . $book->getId() . " book persisted ($step)\n";
                }
                $this->em->flush();
                $this->em->clear();
            }
        }

        if ($this->logger && $this->debug) {
            $numberProcessed = $i * $this->perPage - $skipped;
            $this->logger->log(
                LogLevel::INFO,
                sprintf('%s books flushed', $numberProcessed)
            );
            echo ">>> $numberProcessed books flushed\n";
        }
        $this->em->flush();
        $this->em->clear();

        return true;
    }

    /**
     * @param string $endpoint
     *
     * @return \SimpleXMLElement
     * @throws \ErrorException
     */
    private function getXml($endpoint)
    {
        $content = file_get_contents($endpoint);
        try {
            $xml = simplexml_load_string(mb_convert_encoding(gzdecode($content), 'utf-8'));
        } catch (\Exception $e) {
            if ($this->logger && $this->debug) {
                $this->logger->log(
                    LogLevel::ERROR,
                    sprintf('Message: %s. Endpoint: %s', $e->getMessage(), $endpoint)
                );
            }
            throw new \ErrorException;
        }

        return $xml;
    }
}