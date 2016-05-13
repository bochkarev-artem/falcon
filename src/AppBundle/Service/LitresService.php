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
     * @var int $batchSize
     */
    private $batchSize = 50;

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
     *
     * @return bool
     */
    public function getData($param)
    {
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
            foreach ($genreNode as $node) {
                $id    = (integer) $node['id'];
                $token = (string) $node['token'];
                $title = (string) $node['title'];
                if(!is_null($token) && !$this->genreRepo->findByToken($token)) {
                    $genre = new Genre();
                    $genre
                        ->setLitresId($id)
                        ->setTitle($title)
                        ->setToken($token)
                    ;
                    $genres[$token] = $genre;
                }
            }
        }

        foreach ($genres as $genre) {
            $this->em->persist($genre);
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
     * @return Author
     */
    public function getAuthorData($documentId, $endpoint = 'http://robot.litres.ru/pages/catalit_persons/')
    {
        $endpoint = $endpoint . '?person=' . $documentId;
        $xml      = $this->getXml($endpoint);
        $author   = new Author();
        $subject  = $xml->{'subject'};
        $author
            ->setDocumentId((string)$subject['id'])
            ->setLitresHubId((integer)$subject['hub_id'])
            ->setLevel((integer) $subject->{'level'})
            ->setArtsCount((integer) $subject->{'arts-count'})
            ->setFirstName((string) $subject->{'first-name'})
            ->setMiddleName((string) $subject->{'middle-name'})
            ->setLastName((string) $subject->{'last-name'})
            ->setDescription((string) $subject->{'text_descr_html'}->hidden)
            ->setPhoto((string) $subject->{'photo'})
            ->setRecensesCount((integer) $subject->{'recenses-count'})
        ;

        return $author;
    }

    /**
     * @param \SimpleXMLElement $sequence
     *
     * @return Sequence
     */
    public function getSequence($sequence)
    {
        $sequenceId   = (integer) $sequence['id'];
        $sequenceName = (string) $sequence['name'];
        $sequence = $this->sequenceRepo->findOneByLitresId($sequenceId);
        if (!$sequence) {
            $sequence = new Sequence();
            $sequence->setLitresId($sequenceId);
            $sequence->setName($sequenceName);
            $this->em->persist($sequence);
            $this->em->flush();
        }

        return $sequence;
    }

    /**
     * @param string $authorId
     *
     * @return Author
     */
    public function getAuthor($authorId)
    {
        $author = $this->authorRepo->findOneByDocumentId($authorId);
        if (!$author) {
            $author = $this->getAuthorData($authorId);
            $this->em->persist($author);
            $this->em->flush();
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
     * @param string $tagId
     *
     * @return Tag
     */
    public function getTag($tagId)
    {
        $tag = $this->tagRepo->findOneByLitresId($tagId);
        if (!$tag) {
            $tag = new Tag();
            $tag->setLitresId($tagId);
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
        $endpoint  = $endpoint . '?limit=0,1';
        $xml       = $this->getXml($endpoint);
        $processed = 1;

        foreach ($xml->{'fb2-book'} as $data) {
            $annotation   = '';
            $book         = new Book;
            $titleInfo    = $data->{'text_description'}->hidden->{'title-info'};
            $documentInfo = $data->{'text_description'}->hidden->{'document-info'};
            $publishInfo  = $data->{'text_description'}->hidden->{'publish-info'};

            foreach ($titleInfo->author as $author) {
                $author = $this->getAuthor((string) $author->id);
                $book->addAuthor($author);
            }
            foreach ($titleInfo->genre as $genreToken) {
                $genre = $this->getGenre((string) $genreToken);
                $book->addGenre($genre);
            }
            foreach ($data->{'art_tags'}->tag as $tag) {
                $tag = $this->getTag((string) $tag['id']);
                $book->addTag($tag);
            }
            foreach ($data->sequence as $sequence) {
                $sequence = $this->getSequence($sequence);
                $book->addSequence($sequence);
            }
            foreach ($titleInfo->annotation->p as $p) {
                $annotation .= '<p>' . (string) $p . '</p>';
            }

            if($this->bookRepo->findOneByLitresHubId((string) $data['hub_id'])) {
                return true;
            }

            $book
                ->setLitresHubId((string) $data['hub_id'])
                ->setType((string) $data['type'])
                ->setCover((string) $data['cover'])
                ->setCoverPreview((string) $data['cover_preview'])
                ->setFilename((string) $data['filename'])
                ->setPrice((string) $data['price'])
                ->setRating((string) $data['rating'])
                ->setRecensesCount((string) $data['recenses'])
                ->setPrice((string) $data['price'])
                ->setHasTrial((string) $data['has_trial'])
                ->setType((string) $data['type'])
                ->setTitle((string) $titleInfo->{'book-title'})
                ->setAnnotation($annotation)
                ->setLang((string) $titleInfo->lang)
                ->setDate((string) $titleInfo->date['value'])
                ->setDocumentUrl((string) $documentInfo->{'src-url'})
                ->setDocumentId((string) $documentInfo->id)
                ->setPublisher((string) $publishInfo->publisher)
                ->setYearPublished((string) $publishInfo->year)
                ->setCityPublished((string) $publishInfo->city)
                ->setIsbn((string) $publishInfo->isbn)
            ;

            $this->em->persist($book);
            if (($processed % $this->batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
                $processed++;
            }
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
            if ($this->logger) {
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