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
    private $batchSize = 50;

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
            case 'genres':
                return $this->getGenresData($endpoint);
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
        try {
            $xml = new \SimpleXMLElement($endpoint, 0, true);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->log(
                    LogLevel::ERROR,
                    sprintf('Message: %s. Endpoint: %s', $e->getMessage(), $endpoint)
                );
            }
            throw new \ErrorException;
        }

        foreach ($xml->{'catalit-genres'}->{'genre'} as $genreNode) {
            $genre = new Genre();
            $genre
                ->setTitle($genreNode->title)
                ->setType('root')
            ;
            $this->em->persist($genre);
            foreach ($genreNode as $node) {
                $genre = new Genre();
                $genre
                    ->setLitresId($node->id)
                    ->setTitle($node->title)
                    ->setToken($node->token)
                    ->setType('child')
                ;
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
     * @return Author
     */
    public function getAuthorData($documentId, $endpoint = 'http://robot.litres.ru/pages/catalit_persons/')
    {
        try {
            $xml = new \SimpleXMLElement($endpoint . '?' . $documentId, 0, true);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->log(
                    LogLevel::ERROR,
                    sprintf('Message: %s. Endpoint: %s', $e->getMessage(), $endpoint)
                );
            }
            throw new \ErrorException;
        }

        $author  = new Author();
        $subject = $xml->{'catalit-persons'}->{'subject'};
        $author
            ->setLitresHubId($subject->{'hub_id'})
            ->setLevel($subject->level)
            ->setArtsCount($subject->{'arts-count'})
            ->setFirstName($subject->{'first-name'})
            ->setMiddleName($subject->{'middle-name'})
            ->setLastName($subject->{'last-name'})
            ->setDescription($subject->{'text_descr_html'}->hidden)
            ->setPhoto($subject->{'photo'})
            ->setRecensesCount($subject->{'recenses-count'})
        ;

        return $author;
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
            $this->em->flush();
            $this->em->clear();
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
        $author = $this->authorRepo->findByDocumentID($authorId);
        if (!$author) {
            $author = $this->getAuthorData($authorId);
            $this->em->persist($author);
            $this->em->flush();
            $this->em->clear();
        }

        $authors->add($author);
    }

    /**
     * @param string          $genreToken
     * @param ArrayCollection $genres
     */
    public function saveGenre($genreToken, ArrayCollection $genres)
    {
        /** @var Genre $genre */
        $genre = $this->genreRepo->findByToken($genreToken);
        if (!$genre) {
            $genre = new Genre();
            $genre->setToken($genreToken);
            $this->em->persist($genre);
            $this->em->flush();
            $this->em->clear();
        }

        $genres->add($genre);
    }

    /**
     * @param string          $tagId
     * @param ArrayCollection $tags
     */
    public function saveTag($tagId, ArrayCollection $tags)
    {
        /** @var Tag $tag */
        $tag = $this->tagRepo->findByLitresId($tagId);
        if (!$tag) {
            $tag = new Tag();
            $tag->setLitresId($tagId);
            $this->em->persist($tag);
            $this->em->flush();
            $this->em->clear();
        }

        $tags->add($tag);
    }

    /**
     * @param string $endpoint
     *
     * @throws \ErrorException
     * @return bool
     */
    public function getBooksData($endpoint = 'http://robot.litres.ru/pages/catalit_browser/')
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
            throw new \ErrorException;
        }

        $processed = 1;
        foreach ($xml->{'catalit-fb2-books'}->{'fb2-book'} as $data) {
            $book               = new Book;
            $genres             = new ArrayCollection();
            $tags               = new ArrayCollection();
            $authors            = new ArrayCollection();
            $titleInfo          = $data->{'text_description'}->hidden->{'title-info'};
            $documentInfo       = $data->{'text_description'}->hidden->{'document-info'};
            $publishInfo        = $data->{'text_description'}->hidden->{'publish-info'};
            $this->authorRepo   = $this->em->getRepository('AppBundle:Author');
            $this->genreRepo    = $this->em->getRepository('AppBundle:Genre');
            $this->tagRepo      = $this->em->getRepository('AppBundle:Tag');
            $this->sequenceRepo = $this->em->getRepository('AppBundle:Sequence');

            $sequence           = $this->saveSequence($titleInfo->sequences->sequence['id']);
            foreach ($titleInfo->author as $author) {
                $this->saveAuthor($author->id, $authors);
            }
            foreach ($titleInfo->genre as $genre) {
                $this->saveGenre($genre, $genres);
            }
            foreach ($titleInfo->{'art_tags'}->tag as $tag) {
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
                ->setTitle($titleInfo->{'book-title'})
                ->setAnnotation($titleInfo->annotation)
                ->setDate($titleInfo->date['value'])
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
                $this->em->flush();
                $this->em->clear();
                $processed++;
            }
        }

        $this->em->flush();
        $this->em->clear();

        return true;
    }
}