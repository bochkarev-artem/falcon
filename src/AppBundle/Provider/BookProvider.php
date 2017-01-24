<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Provider;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Sequence;
use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Elastica\Document;
use Elastica\Exception\NotFoundException;
use Elastica\Type;
use FOS\ElasticaBundle\Provider\ProviderInterface;

class BookProvider implements ProviderInterface
{
    /**
     * @var Type
     */
    private $bookType;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var integer
     */
    private $batchSize;

    /**
     * @param Type          $bookType
     * @param EntityManager $em
     * @param integer       $batchSize
     */
    public function __construct(Type $bookType, EntityManager $em, $batchSize)
    {
        $this->bookType  = $bookType;
        $this->em        = $em;
        $this->batchSize = $batchSize;
    }

    /**
     * {@inheritdoc}
     */
    public function populate(\Closure $loggerClosure = null, array $options = [])
    {
        $this->updateDocumentsByQuery($this->createQueryBuilder(), $loggerClosure);
        $this->em->clear();
    }

    /**
     * @return QueryBuilder
     */
    private function createQueryBuilder()
    {
        /* @var QueryBuilder $queryBuilder */
        $qb = $this->em->createQueryBuilder();

        return $qb
            ->select('b')
            ->from('AppBundle:Book', 'b')
        ;
    }

    /**
     * @param Book $book
     *
     * @return Document|boolean
     */
    private function prepareDocument(Book $book)
    {
        $bookData = $this->collectData($book);
        if (empty($bookData)) {
            return false;
        }

        return new Document($book->getId(), $bookData, 'book', 'books');
    }

    /**
     * @param Book $book
     *
     * @return array
     */
    private function collectData(Book $book)
    {
        $bookData = [
            'book_id'           => $book->getId(),
            'title'             => $book->getTitle(),
            'rating'            => $book->getRating(),
            'book_type'         => $book->getBookType(),
            'annotation'        => $book->getAnnotation(),
            'cover_path'        => $book->getCoverPath(),
            'price'             => $book->getPrice(),
            'filename'          => $book->getFilename(),
            'has_trial'         => $book->isHasTrial(),
            'reader'            => $book->getReader(),
            'date'              => $book->getDate(),
            'lang'              => $book->getLang(),
            'sequence_number'   => $book->getSequenceNumber(),
            'litres_id'         => $book->getLitresHubId(),
            'document_id'       => $book->getDocumentId(),
            'publisher'         => $book->getPublisher(),
            'city_published'    => $book->getCityPublished(),
            'year_published'    => $book->getYearPublished(),
            'isbn'              => $book->getIsbn(),
            'review_count'      => $book->getReviewCount(),
            'path'              => $book->getPath(),
        ];

        $bookData = $this->collectAuthorsData($book, $bookData);
        $bookData = $this->collectTagsData($book, $bookData);
        $bookData = $this->collectSequencesData($book, $bookData);
        $bookData = $this->collectGenresData($book, $bookData);

        return $bookData;
    }

    /**
     * @param Book  $book
     * @param array $bookData
     *
     * @return array
     */
    private function collectGenresData(Book $book, $bookData)
    {
        $genresData = [];
        /** @var Genre $genre */
        foreach ($book->getGenres() as $genre) {
            $genreData = [
                'genre_id'    => $genre->getId(),
                'title'       => $genre->getTitle(),
                'description' => $genre->getDescription(),
                'litres_id'   => $genre->getLitresId(),
                'path'        => $genre->getPath(),
            ];
            $genresData[] = $genreData;
        }

        $bookData['genres'] = $genresData;

        return $bookData;
    }

    /**
     * @param Book  $book
     * @param array $bookData
     *
     * @return array
     */
    private function collectAuthorsData(Book $book, $bookData)
    {
        $authorsData = [];
        /** @var Author $author */
        foreach ($book->getAuthors() as $author) {
            $authorData = [
                'author_id'    => $author->getId(),
                'first_name'   => $author->getFirstName(),
                'last_name'    => $author->getLastName(),
                'middle_name'  => $author->getMiddleName(),
                'full_name'    => $author->getFullName(),
                'short_name'   => $author->getShortName(),
                'level'        => $author->getLevel(),
                'description'  => $author->getDescription(),
                'review_count' => $author->getReviewCount(),
                'arts_count'   => $author->getArtsCount(),
                'photo_path'   => $author->getPhotoPath(),
                'litres_id'    => $author->getLitresHubId(),
                'document_id'  => $author->getDocumentId(),
                'path'         => $author->getPath(),
            ];
            $authorsData[] = $authorData;
        }

        $bookData['authors'] = $authorsData;

        return $bookData;
    }

    /**
     * @param Book  $book
     * @param array $bookData
     *
     * @return array
     */
    private function collectTagsData(Book $book, $bookData)
    {
        $tagsData = [];
        /** @var Tag $tag */
        foreach ($book->getTags() as $tag) {
            $tagData = [
                'tag_id'    => $tag->getId(),
                'title'     => $tag->getTitle(),
                'litres_id' => $tag->getLitresId(),
                'path'      => $tag->getPath(),
            ];
            $tagsData[] = $tagData;
        }

        $bookData['tags'] = $tagsData;

        return $bookData;
    }

    /**
     * @param Book  $book
     * @param array $bookData
     *
     * @return array
     */
    private function collectSequencesData(Book $book, $bookData)
    {
        $sequence = $book->getSequence();
        if ($sequence) {
            $sequenceData = [
                'sequence_id' => $sequence->getId(),
                'name'        => $sequence->getName(),
                'litres_id'   => $sequence->getLitresId(),
                'path'        => $sequence->getPath(),
            ];

            $bookData['sequence'] = $sequenceData;
        }

        return $bookData;
    }

    /**
     * @param QueryBuilder  $queryBuilder
     * @param \Closure|null $loggerClosure
     *
     * @return bool
     */
    private function updateDocumentsByQuery(QueryBuilder $queryBuilder, \Closure $loggerClosure = null)
    {
        $nbObjects     = $this->countObjects($queryBuilder);
        $books         = $this->getQueryIterator($queryBuilder);
        $documents     = [];
        $processed     = 0;
        $lastCount     = 0;
        $stepStartTime = microtime(true);

        foreach ($books as $row) {
            /** @var Book $book */
            $book = array_shift($row);
            if ($document = $this->prepareDocument($book)) {
                array_push($documents, $document);
            } else {
                try {
                    $this->bookType->deleteById($book->getId());
                }
                catch (NotFoundException $e) {}
            }

            $processed++;
            if ($processed % $this->batchSize === 0) {
                if ($loggerClosure) {
                    $stepNbObjects    = $processed - $lastCount;
                    $stepCount        = $processed;
                    $percentComplete  = 100 * $stepCount / $nbObjects;
                    $objectsPerSecond = $stepNbObjects / (microtime(true) - $stepStartTime);
                    $active           = round(memory_get_usage(true) / 1024 / 1024, 1);
                    $peak             = round(memory_get_peak_usage(true) / 1024 / 1024, 1);
                    $loggerClosure(
                        $stepCount,
                        $nbObjects,
                        "\n" . sprintf(
                            '%0.1f%% (%d/%d), %d objects/s %0.1fMb/%0.1fMb',
                            $percentComplete,
                            $stepCount,
                            $nbObjects,
                            $objectsPerSecond,
                            $active,
                            $peak
                        )
                    );
                }

                $this->bookType->addDocuments($documents);
                $this->em->clear();

                $documents = [];
                $lastCount      = $processed;
                $stepStartTime  = microtime(true);
            }
        }

        if ($documents) {
            if ($loggerClosure) {
                $stepNbObjects    = $processed - $lastCount;
                $stepCount        = $processed;
                $percentComplete  = 100 * $stepCount / $nbObjects;
                $objectsPerSecond = $stepNbObjects / (microtime(true) - $stepStartTime);
                $active           = round(memory_get_usage(true) / 1024 / 1024, 1);
                $peak             = round(memory_get_peak_usage(true) / 1024 / 1024, 1);
                $loggerClosure(
                    $stepCount,
                    $nbObjects,
                    "\n" . sprintf(
                        '%0.1f%% (%d/%d), %d objects/s %0.1fMb/%0.1fMb',
                        $percentComplete,
                        $stepCount,
                        $nbObjects,
                        $objectsPerSecond,
                        $active,
                        $peak
                    )
                );
            }

            $this->bookType->addDocuments($documents);
            $this->em->clear();
        }

        return true;
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return IterableResult|array
     */
    protected function getQueryIterator(QueryBuilder $queryBuilder)
    {
        try {
            $objects = $queryBuilder->getQuery()->iterate();
        }
        catch (QueryException $e) {
            $aliases  = $queryBuilder->getRootAliases();
            $entities = $queryBuilder->getRootEntities();

            $idQb = clone $queryBuilder;
            $res  = $idQb
                ->select($aliases[0] . '.id')
                ->add('from', new Expr\From($entities[0], $aliases[0], $aliases[0] . '.id'), false)
                ->getQuery()
                ->getResult()
            ;

            $ids = array_keys($res);
            if (!$ids) {
                return [];
            }

            $newQb   = $this->em->createQueryBuilder();
            $objects = $newQb
                ->select($aliases[0])
                ->from($entities[0], $aliases[0], $aliases[0] . '.id')
                ->where($queryBuilder->expr()->in($aliases[0] . '.id', $ids))
                ->getQuery()
                ->iterate()
            ;
        }

        return $objects;
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return int
     */
    private function countObjects(QueryBuilder $queryBuilder)
    {
        $qb = clone $queryBuilder;

        $aliases = $qb->getRootAliases();
        $qb->select('COUNT(' . $aliases[0] . '.id)');

        return (integer) $qb->getQuery()->getSingleScalarResult();
    }
}
