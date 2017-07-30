<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Provider;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Tag;
use AppBundle\Service\BookPageService;
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
     * @var BookPageService
     */
    private $bookPageService;

    /**
     * @param Type            $bookType
     * @param EntityManager   $em
     * @param BookPageService $bookPageService
     * @param integer         $batchSize
     */
    public function __construct(Type $bookType, EntityManager $em, BookPageService $bookPageService, $batchSize)
    {
        $this->bookType        = $bookType;
        $this->em              = $em;
        $this->bookPageService = $bookPageService;
        $this->batchSize       = $batchSize;
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
        $ratingData = $this->bookPageService->getBookRatingData($book->getId());
        $lang       = $book->getLang();
        $bookData   = [
            'book_id'         => $book->getId(),
            'annotation'      => $book->getAnnotation(),
            'cover_path'      => $book->getCoverPath(),
            'price'           => $book->getPrice(),
            'has_trial'       => $book->isHasTrial(),
            'featured_home'   => $book->isFeaturedHome(),
            'sequence_number' => $book->getSequenceNumber(),
            'litres_hub_id'   => $book->getLitresHubId(),
            'document_id'     => $book->getDocumentId(),
            'publisher'       => $book->getPublisher(),
            'city_published'  => $book->getCityPublished(),
            'year_published'  => $book->getYearPublished(),
            'isbn'            => $book->getIsbn(),
            'lang'            => $lang,
            'rating'          => $ratingData['rating'],
            'review_count'    => $book->getReviews()->count(),
            'path'            => $book->getPath(),
        ];

        $bookData['title_' . $lang] = $book->getTitle();

        if ($book->getDate()) {
            $bookData['date'] = $book->getDate()->format('Y-m-d');
        }

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
                'genre_id'       => $genre->getId(),
                'title_en'       => $genre->getTitleEn(),
                'title_ru'       => $genre->getTitleRu(),
                'description_en' => $genre->getDescriptionEn(),
                'description_ru' => $genre->getDescriptionRu(),
                'litres_id'      => $genre->getLitresId(),
                'path_en'        => $genre->getPathEn(),
                'path_ru'        => $genre->getPathRu(),
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
                'author_id'     => $author->getId(),
                'first_name'    => $author->getFirstName(),
                'last_name'     => $author->getLastName(),
                'middle_name'   => $author->getMiddleName(),
                'short_name'    => $author->getShortName(),
                'document_id'   => $author->getDocumentId(),
                'path'          => $author->getPath(),
            ];
            $authorData['full_name_' . $book->getLang()] = $author->getFullName();
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
        $sequenceData = [];
        $sequence     = $book->getSequence();

        if ($sequence) {
            $sequenceData = [
                'sequence_id' => $sequence->getId(),
                'litres_id'   => $sequence->getLitresId(),
                'path'        => $sequence->getPath(),
            ];
            $sequenceData['name_' . $book->getLang()] = $sequence->getName();
        }

        $bookData['sequence'] = $sequenceData;

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
                        ) . "\n"
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
                    ) . "\n"
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
        } catch (QueryException $e) {
            $aliases  = $queryBuilder->getRootAliases();
            $entities = $queryBuilder->getRootEntities();

            $idQb = clone $queryBuilder;
            $res  = $idQb
                ->select($aliases[0] . '.id')
                ->add(
                    'from',
                    new Expr\From($entities[0], $aliases[0], $aliases[0] . '.id'),
                    false
                )
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
