<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Model\QueryParams;

class BookPageService
{
    const FEATURED_ASIDE_COUNT  = 4;
    const FEATURED_BOTTOM_COUNT = 16;

    /**
     * @var QueryService
     */
    protected $queryService;

    /**
     * @param QueryService $queryService
     */
    public function __construct(QueryService $queryService)
    {
        $this->queryService = $queryService;
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
}
