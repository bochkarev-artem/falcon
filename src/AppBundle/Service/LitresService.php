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
        $this->em           = $em;
        $this->logger       = $logger;
        $this->authorRepo   = $this->em->getRepository('AppBundle:Author');
        $this->genreRepo    = $this->em->getRepository('AppBundle:Genre');
        $this->tagRepo      = $this->em->getRepository('AppBundle:Tag');
        $this->sequenceRepo = $this->em->getRepository('AppBundle:Sequence');
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
     * @param ArrayCollection   $sequences
     */
    public function saveSequence($sequence, $sequences)
    {
        $sequenceId   = (integer) $sequence['id'];
        $sequenceName = (string) $sequence['name'];
        /** @var Sequence $sequence */
        $sequence = $this->sequenceRepo->findByLitresId($sequenceId);
        if (!$sequence) {
            $sequence->setLitresId($sequenceId);
            $sequence->setName($sequenceName);
            $this->em->persist($sequence);
            $this->em->flush();
            $this->em->clear();
        }

        $sequences->add($sequence);
    }

    /**
     * @param string          $authorId
     * @param ArrayCollection $authors
     */
    public function saveAuthor($authorId, ArrayCollection $authors)
    {
        /** @var Author $author */
        $author = $this->authorRepo->findByDocumentId($authorId);
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
        $xml       = $this->getXml($endpoint);
        $processed = 1;
//        ld((string) $xml['pages']);
//        ld((string) $xml['records']);

        foreach ($xml->{'fb2-book'} as $data) {
            $book         = new Book;
            $genres       = new ArrayCollection();
            $tags         = new ArrayCollection();
            $authors      = new ArrayCollection();
            $sequences    = new ArrayCollection();
            $titleInfo    = $data->{'text_description'}->hidden->{'title-info'};
            $documentInfo = $data->{'text_description'}->hidden->{'document-info'};
            $publishInfo  = $data->{'text_description'}->hidden->{'publish-info'};

            foreach ($titleInfo->author as $author) {
                $this->saveAuthor((string) $author->id, $authors);
            }
            foreach ($titleInfo->genre as $genreToken) {
                $this->saveGenre((string) $genreToken, $genres);
            }
            foreach ($data->{'art_tags'}->tag as $tag) {
                $this->saveTag((string) $tag['id'], $tags);
            }
            foreach ($data->sequence as $sequence) {
                $this->saveSequence($sequence, $sequences);
            }

            $book
                ->setLitresHubId((string) $data['hub_id'])
                ->setType((string) $data['type'])
                ->setCover((string) $data['cover'])
                ->setCoverPreview((string) $data['cover_preview'])
                ->setFilename((string) $data['file_id'])
                ->setPrice((string) $data['price'])
                ->setRating((string) $data['rating'])
                ->setRecensesCount((string) $data['recenses'])
                ->setPrice((string) $data['price'])
                ->setHasTrial((string) $data['has_trial'])
                ->setType((string) $data['type'])
                ->setTitle((string) $titleInfo->{'book-title'})
                ->setAnnotation((string) $titleInfo->annotation)
                ->setDate((string) $titleInfo->date['value'])
                ->setDocumentUrl((string) $documentInfo->{'src-url'})
                ->setDocumentId((string) $documentInfo->id)
                ->setPublisher((string) $publishInfo->annotation)
                ->setYearPublished((string) $publishInfo->annotation)
                ->setCityPublished((string) $publishInfo->annotation)
                ->setIsbn((string) $publishInfo->annotation)
                ->setAuthor($authors)
                ->setGenre($genres)
                ->setTag($tags)
                ->setSequence($sequences)
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