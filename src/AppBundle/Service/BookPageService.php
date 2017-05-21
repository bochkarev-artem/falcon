<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\BookReview;
use AppBundle\Model\QueryParams;
use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

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
     * @var integer
     */
    protected $perPage;

    /**
     * @param QueryService  $queryService
     * @param EntityManager $em
     * @param integer $perPage
     */
    public function __construct(QueryService $queryService, EntityManager $em, $perPage)
    {
        $this->queryService = $queryService;
        $this->em           = $em;
        $this->perPage      = $perPage;
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

            $books = $this->queryService->find($queryParams);
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

            $books = $this->queryService->find($queryParams);
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
            ->from('AppBundle:BookRating', 'bc')
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
            ->select('AVG(book_rating.rating)')
            ->from('AppBundle:BookRating', 'book_rating')
            ->leftJoin('book_rating.book', 'b')
            ->leftJoin('book_rating.user', 'u')
            ->andWhere($qb->expr()->eq('b.id', ':book_id'))
            ->andWhere($qb->expr()->eq('u.id', ':user_id'))
            ->setParameter('book_id', $bookId)
            ->setParameter('user_id', $userId)
        ;

        $rating = $qb->getQuery()->getSingleScalarResult() ?: 0;

        return $rating;
    }

    /**
     * @param int $bookId
     *
     * @return array
     */
    public function getBookReviews($bookId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('book_review')
            ->from('AppBundle:BookReview', 'book_review')
            ->leftJoin('book_review.book', 'b')
            ->andWhere($qb->expr()->eq('b.id', ':book_id'))
            ->andWhere($qb->expr()->eq('book_review.status', ':status'))
            ->setParameter('book_id', $bookId)
            ->setParameter('status', BookReview::STATUS_APPROVED)
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @param int $userId
     * @param int $page
     * @param int $perPage
     *
     * @return Pagerfanta
     */
    public function getUserReviews($userId, $page, $perPage = null)
    {
        $perPage = $perPage ?? $this->perPage;
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('book, reviews, authors')
            ->from('AppBundle:BookReview', 'reviews')
            ->leftJoin('reviews.book', 'book')
            ->leftJoin('reviews.user', 'user')
            ->leftJoin('book.authors', 'authors')
            ->andWhere($qb->expr()->eq('user.id', ':user_id'))
            ->setParameter('user_id', $userId)
            ->orderBy('reviews.updatedOn', 'DESC')
            ->setMaxResults($perPage)
        ;

        $adapter   = new DoctrineORMAdapter($qb, false);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage($perPage);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @param int $userId
     * @param int $page
     * @param int $perPage
     *
     * @return Pagerfanta
     */
    public function getUserRatings($userId, $page, $perPage = null)
    {
        $perPage = $perPage ?? $this->perPage;
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('book, ratings, authors')
            ->from('AppBundle:BookRating', 'ratings')
            ->leftJoin('ratings.book', 'book')
            ->leftJoin('ratings.user', 'user')
            ->leftJoin('book.authors', 'authors')
            ->andWhere($qb->expr()->eq('user.id', ':user_id'))
            ->setParameter('user_id', $userId)
            ->orderBy('ratings.updatedOn', 'DESC')
            ->setMaxResults($perPage)
        ;

        $adapter   = new DoctrineORMAdapter($qb, false);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage($perPage);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @param int $userId
     *
     * @return int
     */
    public function getUserReviewsStatistic($userId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('COUNT(reviews)')
            ->from('AppBundle:BookReview', 'reviews')
            ->leftJoin('reviews.user', 'user')
            ->andWhere($qb->expr()->eq('user.id', ':user_id'))
            ->andWhere($qb->expr()->gte('reviews.createdOn', ':date'))
            ->andWhere($qb->expr()->eq('reviews.status', ':status'))
            ->setParameter('user_id', $userId)
            ->setParameter('date', strtotime('-7 days'))
            ->setParameter('status', BookReview::STATUS_APPROVED)
        ;

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result;
    }

    /**
     * @param int $maxUsers
     *
     * @return array
     */
    public function getReviewsStatistic($maxUsers = 20)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('u as user, COUNT(reviews) as reviews_count')
            ->from('AppBundle:User', 'u')
            ->leftJoin('u.reviews', 'reviews')
            ->andWhere($qb->expr()->gte('reviews.createdOn', ':date'))
            ->andWhere($qb->expr()->eq('reviews.status', ':status'))
            ->setParameter('date', strtotime('-7 days'))
            ->setParameter('status', BookReview::STATUS_APPROVED)
            ->groupBy('u.id')
            ->setMaxResults($maxUsers)
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
