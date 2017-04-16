<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Model\QueryParams;
use Doctrine\ORM\EntityManager;

class BookPageService
{
    const FEATURED_ASIDE_COUNT  = 4;
    const FEATURED_BOTTOM_COUNT = 16;

    /**
     * @var QueryService
     */
    protected $queryService;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param QueryService  $queryService
     * @param EntityManager $em
     */
    public function __construct(QueryService $queryService, EntityManager $em)
    {
        $this->queryService = $queryService;
        $this->em           = $em;
    }

    /**
     * @param array $book
     *
     * @return array
     */
    public function getSliderFeaturedBooks(array $book)
    {
        $genreIds  = [];
        $authorIds = [];
        $books     = [];
        $genres    = $book['genres'];
        $authors   = $book['authors'];

        foreach ($genres as $genre) {
            $genreIds = $genre['genre_id'];
        }

        foreach ($authors as $author) {
            $authorIds = $author['author_id'];
        }

        if ($genreIds) {
            $queryParams = new QueryParams();
            $queryParams
                ->setFilterGenres($genreIds)
                ->setSize(self::FEATURED_BOTTOM_COUNT)
                ->setFilterExcludeAuthors($authorIds)
                ->setSort(QueryParams::SORT_RATING_DESC)
            ;

            $queryResult = $this->queryService->query($queryParams);
            $books       = $queryResult->getResults();
        }

        return $books;
    }

    /**
     * @param array $book
     *
     * @return array
     */
    public function getAsideFeaturedBooks(array $book)
    {
        $authorIds = [];
        $books     = [];
        $bookId    = $book['book_id'];
        $authors   = $book['authors'];

        foreach ($authors as $author) {
            $authorIds = $author['author_id'];
        }

        if ($authorIds) {
            $queryParams = new QueryParams();
            $queryParams
                ->setFilterAuthors($authorIds)
                ->setSize(self::FEATURED_ASIDE_COUNT)
                ->setFilterExcludeBooks($bookId)
            ;

            $queryResult = $this->queryService->query($queryParams);
            $books       = $queryResult->getResults();
        }

        return $books;
    }

    /**
     * @param int $bookId
     *
     * @return array
     */
    public function getBookRatingData($bookId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('AVG(bc.rating) as avg_rating, COUNT(bc.rating) as total_rating')
            ->from('AppBundle:BookCard', 'bc')
            ->leftJoin('bc.book', 'b')
            ->andWhere($qb->expr()->eq('b.id', ':book_id'))
            ->setParameter('book_id', $bookId)
        ;

        $result = $qb->getQuery()->getSingleResult();

        return ['rating' => $result['avg_rating'] ?: 0, 'total' => $result['total_rating'] ?: 0];
    }

    /**
     * @param int $userId
     * @param int $bookId
     *
     * @return float
     */
    public function getUserBookRating($userId, $bookId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('AVG(bc.rating)')
            ->from('AppBundle:BookCard', 'bc')
            ->leftJoin('bc.book', 'b')
            ->leftJoin('bc.user', 'u')
            ->andWhere($qb->expr()->eq('b.id', ':book_id'))
            ->andWhere($qb->expr()->eq('u.id', ':user_id'))
            ->setParameter('book_id', $bookId)
            ->setParameter('user_id', $userId)
        ;

        $rating = $qb->getQuery()->getSingleScalarResult() ?: 0;

        return $rating;
    }
}
