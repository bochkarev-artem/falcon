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
use Doctrine\Common\Collections\ArrayCollection;
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
//    CONST DETAILED_DATA_FILE = 'http://www.litres.ru/static/ds/detailed_data.xml.gz';

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
     * @var Logger
     */
    private $logger;


    /**
     * @var int $batchSize
     */
    private $batchSize = 20;

    /**
     * @param EntityManager $em
     * @param Logger        $logger
     */
    public function __construct(EntityManager $em, Logger $logger)
    {
        $this->em     = $em;
        $this->logger = $logger;
    }

    /**
     * @param $file
     * @return resource|bool
     */
    public function getFile($file)
    {
        $filename = 'detailed_data.xml.gz';
        if (file_put_contents($filename, fopen($file, 'r'))) {
            return gzopen($filename, 'r');
        }

        return false;
    }

    /**
     * @param string $param
     * @param string $endpoint
     *
     * @return bool
     */
    public function getData($param, $endpoint)
    {
        switch ($param) {
            case 'books':
                return $this->getBooksData($endpoint);
                break;
            case 'authors':
                break;
            default:
                return false;
        }
    }

    /**
     * @param string $sequenceId
     *
     * @return Sequence
     */
    public function saveSequence($sequenceId)
    {
        /** @var Sequence $sequence */
        $sequence = $this->sequenceRepo->findByLitresId($sequenceId);
        if (!$sequence) {
            $sequence = new Sequence();
            $sequence->setLitresId($sequenceId);
            $this->em->persist($sequence);
            $this->em->flush($sequence);
        }

        return $sequence;
    }

    /**
     * @param string          $authorId
     * @param ArrayCollection $authors
     */
    public function saveAuthor($authorId, ArrayCollection $authors)
    {
        /** @var Author $author */
        if ($author = $this->authorRepo->findByDocumentID($authorId)) {
            $authors->add($author);
        } else {
            $author = new Author();
            $author->setDocumentId($authorId);
            $this->em->persist($author);
            $this->em->flush($author);
        }
    }

    /**
     * @param string          $genreToken
     * @param ArrayCollection $genres
     */
    public function saveGenre($genreToken, ArrayCollection $genres)
    {
        /** @var Genre $genre */
        if ($genre = $this->genreRepo->findByToken($genreToken)) {
            $genres->add($genre);
        } else {
            $genre = new Genre();
            $genre->setToken($genreToken);
            $this->em->persist($genre);
            $this->em->flush($genre);
        }
    }

    /**
     * @param string          $tagId
     * @param ArrayCollection $tags
     */
    public function saveTag($tagId, ArrayCollection $tags)
    {
        /** @var Tag $tag */
        if ($tag = $this->tagRepo->findByLitresId($tagId)) {
            $tags->add($tag);
        } else {
            $tag = new Tag();
            $tag->setLitresId($tagId);
            $this->em->persist($tag);
            $this->em->flush($tag);
        }
    }

    /**
     * @param string $endpoint
     *
     * @throws \ErrorException
     * @return bool
     */
    public function getBooksData($endpoint = 'http://www.litres.ru/pages/catalit_browser/')
    {
        try {
            $xml = new \SimpleXMLElement($endpoint, 0, true);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->log(
                    LogLevel::ERROR,
                    sprintf('Message: %s. Endpoint: %s', $e->getMessage(), $endpoint)
                );
            }
            throw new \ErrorException();
        }

        $processed = 1;
        foreach ($xml->{'catalit-fb2-books'}->{'fb2-book'} as $data) {
            $book               = new Book;
            $genres             = new ArrayCollection();
            $tags               = new ArrayCollection();
            $authors            = new ArrayCollection();
            $bookInfo           = $data->{'text_description'}->hidden->{'title-info'};
            $documentInfo       = $data->{'text_description'}->hidden->{'document-info'};
            $publishInfo        = $data->{'text_description'}->hidden->{'publish-info'};
            $this->authorRepo   = $this->em->getRepository('AppBundle:Author');
            $this->genreRepo    = $this->em->getRepository('AppBundle:Genre');
            $this->tagRepo      = $this->em->getRepository('AppBundle:Tag');
            $this->sequenceRepo = $this->em->getRepository('AppBundle:Tag');

            $sequence           = $this->saveSequence($bookInfo->sequences->sequence['id']);
            foreach ($bookInfo->author as $author) {
                $this->saveAuthor($author->id, $authors);
            }
            foreach ($bookInfo->genre as $genre) {
                $this->saveGenre($genre, $genres);
            }
            foreach ($bookInfo->{'art_tags'}->tag as $tag) {
                $this->saveTag($tag['id'], $tags);
            }

            $book
                ->setLitresHubId($data['hub_id'])
                ->setType($data['type'])
                ->setCover($data['cover'])
                ->setCoverPreview($data['cover_preview'])
                ->setFilename($data['file_id'])
                ->setPrice($data['price'])
                ->setRating($data['rating'])
                ->setRecensesCount($data['recenses'])
                ->setPrice($data['price'])
                ->setHasTrial($data['has_trial'])
                ->setType($data['type'])
                ->setTitle($bookInfo->{'book-title'})
                ->setAnnotation($bookInfo->annotation)
                ->setDate($bookInfo->date['value'])
                ->setDocumentUrl($documentInfo->{'src-url'})
                ->setDocumentId($documentInfo->id)
                ->setPublisher($publishInfo->annotation)
                ->setYearPublished($publishInfo->annotation)
                ->setCityPublished($publishInfo->annotation)
                ->setIsbn($publishInfo->annotation)
                ->setAuthor($authors)
                ->setGenre($genres)
                ->setTag($tags)
                ->setSequence($sequence)
            ;

            $this->em->persist($book);
            if (($processed % $this->batchSize) === 0) {
                $this->em->flush($book);
                $this->em->clear();
                $processed++;
            }
        }

        $this->em->flush();
        $this->em->clear();

        return true;
    }
}