<?php

namespace AppBundle\Controller;

use AppBundle\Model\QueryParams;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $homePageService = $this->get('home_page_service');
        $seoManager      = $this->get('seo_manager');
        $seoManager->setHomeSeoData();

        return $this->render(
            '@App/Home/index.html.twig',
            [
                'show_genres_in_menu' => true,
                'featured_books'      => $homePageService->getFeaturedBooks(),
                'new_arrivals_books'  => $homePageService->getNewArrivalsBooks(),
                'popular_books'       => $homePageService->getPopularBooks(),
            ]
        );
    }

    /**
     * @param integer $page
     * @param Request $request
     *
     * @return Response|JsonResponse
     */
    public function searchAction($page, Request $request)
    {
        $query       = $request->get('query');
        $queryParams = new QueryParams();
        $queryParams
            ->setSearchQuery($query)
            ->setPage($page);

        $data = $this->prepareViewData($request, $queryParams);
        $data = array_merge($data, [
            'show_author'  => true,
            'query'        => $query,
            'route_name'   => 'search',
            'route_params' => ['query' => $query],
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setSearchSeoData();

        return $this->render('@App/Site/list_page.html.twig', $data);
    }

    /**
     * @param integer $page
     * @param Request $request
     *
     * @return Response|JsonResponse
     */
    public function newBooksAction($page, Request $request)
    {
        $queryParams = new QueryParams();
        $queryParams
            ->setSort(QueryParams::SORT_DATE_DESC)
            ->setPage($page);

        $data = $this->prepareViewData($request, $queryParams);
        $data = array_merge($data, [
            'show_author' => true,
            'route_name'  => 'new_books',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setNewBooksSeoData($page);

        return $this->render('@App/Site/list_page.html.twig', $data);
    }

    /**
     * @param integer $page
     * @param Request $request
     *
     * @return Response|JsonResponse
     */
    public function popularBooksAction($page, Request $request)
    {
        $queryParams = new QueryParams();
        $queryParams
            ->setSort(QueryParams::SORT_RATING_DESC)
            ->setPage($page);

        $data = $this->prepareViewData($request, $queryParams);
        $data = array_merge($data, [
            'show_author' => true,
            'route_name'  => 'popular_books',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setPopularBooksSeoData($page);

        return $this->render('@App/Site/list_page.html.twig', $data);
    }

    /**
     * @param Request $request
     * @param integer $id
     * @param integer $page
     *
     * @return Response|JsonResponse
     */
    public function showGenreAction(Request $request, $id, $page)
    {
        $sortOrder   = $request->get('sort', QueryParams::SORT_NO);
        $queryParams = new QueryParams();
        $queryParams
            ->setFilterGenres($id)
            ->setPage($page)
            ->setSort($sortOrder);

        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Genre');
        $genre     = $genreRepo->find($id);

        $data = $this->prepareViewData($request, $queryParams);
        $data = array_merge($data, [
            'show_author'  => true,
            'genre'        => $genre,
            'route_name'   => 'custom_route',
            'sort_order'   => $sortOrder,
            'route_params' => [
                'slug'   => $genre->getSlug(),
                'prefix' => $genre->getPathPrefix()
            ],
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setGenreSeoData($genre, $page);

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data,
                [
                    'breadcrumbs' => $seoManager->buildBreadcrumbs($genre)
                ]
            )
        );
    }

    /**
     * @param Request $request
     * @param integer $id
     * @param integer $page
     *
     * @return Response|JsonResponse
     */
    public function showAuthorAction(Request $request, $id, $page)
    {
        $sortOrder   = $request->get('sort', QueryParams::SORT_NO);
        $queryParams = new QueryParams();
        $queryParams
            ->setFilterAuthors($id)
            ->setPage($page)
            ->setSort($sortOrder);

        $authorRepo = $this->getDoctrine()->getRepository('AppBundle:Author');
        $author     = $authorRepo->find($id);

        $data = $this->prepareViewData($request, $queryParams);
        $data = array_merge($data, [
            'show_genre'   => true,
            'author'       => $author,
            'route_name'   => 'custom_route',
            'sort_order'   => $sortOrder,
            'route_params' => [
                'slug'   => $author->getSlug(),
                'prefix' => $author->getPathPrefix()
            ],
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setAuthorSeoData($author, $page);

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data,
                [
                    'breadcrumbs' => $seoManager->buildBreadcrumbs($author)
                ]
            )
        );
    }

    /**
     * @param Request $request
     * @param integer $id
     * @param integer $page
     *
     * @return Response|JsonResponse
     */
    public function showSequenceAction(Request $request, $id, $page)
    {
        $sortOrder   = $request->get('sort', QueryParams::SORT_NO);
        $queryParams = new QueryParams();
        $queryParams
            ->setFilterSequences($id)
            ->setPage($page)
            ->setSort($sortOrder);

        $sequenceRepo = $this->getDoctrine()->getRepository('AppBundle:Sequence');
        $sequence     = $sequenceRepo->find($id);

        $data = $this->prepareViewData($request, $queryParams);
        $data = array_merge(
            $data,
            [
                'show_author'  => true,
                'sequence'     => $sequence,
                'route_name'   => 'custom_route',
                'sort_order'   => $sortOrder,
                'route_params' => [
                    'slug'   => $sequence->getSlug(),
                    'prefix' => $sequence->getPathPrefix()
                ],
            ]
        );

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setSequenceSeoData($sequence, $page);

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data,
                [

                    'breadcrumbs' => $seoManager->buildBreadcrumbs($sequence)
                ]
            )
        );
    }

    /**
     * @param Request $request
     * @param integer $id
     * @param integer $page
     *
     * @return Response|JsonResponse
     */
    public function showTagAction(Request $request, $id, $page)
    {
        $sortOrder   = $request->get('sort', QueryParams::SORT_NO);
        $queryParams = new QueryParams();
        $queryParams
            ->setFilterTags($id)
            ->setPage($page)
            ->setSort($sortOrder);

        $tagRepo = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $tag     = $tagRepo->find($id);

        $data = $this->prepareViewData($request, $queryParams);
        $data = array_merge($data, [
            'show_author'  => true,
            'tag'          => $tag,
            'route_name'   => 'custom_route',
            'sort_order'   => $sortOrder,
            'route_params' => [
                'slug'   => $tag->getSlug(),
                'prefix' => $tag->getPathPrefix()
            ],
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setTagSeoData($tag, $page);

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data, [
                    'breadcrumbs' => $seoManager->buildBreadcrumbs($tag)
                ]
            )
        );
    }

    /**
     * @param Request      $request
     * @param QueryParams  $queryParams
     *
     * @return JsonResponse|array
     */
    protected function prepareViewData($request, $queryParams)
    {
        $defaultView = $this->getParameter('default_page_view');
        $cookieName  = $this->getParameter('cookie.page_view_name');
        $cookieView  = $request->cookies->get($cookieName, $defaultView);

        return [
            'books' => $this->get('query_service')->find($queryParams),
            'view'  => $request->get('view', $cookieView),
        ];
    }

    /**
     * @param array $data
     *
     * @return JsonResponse
     */
    protected function prepareJsonResponse($data)
    {
        $cookieName = $this->getParameter('cookie.page_view_name');
        $view       = $data['view'];
        $templates  = [
            'column' => 'AppBundle:Elements/View:column.html.twig',
            'list'   => 'AppBundle:Elements/View:list.html.twig',
            'grid'   => 'AppBundle:Elements/View:grid.html.twig',
        ];

        $template = $templates[$view] ?? 'AppBundle:Elements/View:column.html.twig';

        $responseData = [
            'page'   => $this->renderView($template, $data),
            'status' => true,
        ];

        $timeToExpire = time() + 3600 * 24 * 30;
        $response     = new JsonResponse($responseData);
        $cookie       = new Cookie($cookieName, $view, $timeToExpire);

        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * @param integer $id
     *
     * @return Response
     */
    public function showBookAction($id)
    {
        $queryParams = new QueryParams();
        $queryParams
            ->setFilterId($id)
            ->setSize(1);

        $queryService      = $this->get('query_service');
        $bookPageService   = $this->get('book_page_service');
        $litresBookManager = $this->get('litres_book_manager');

        if (!$books = $queryService->find($queryParams)) {
            throw $this->createNotFoundException();
        }

        $book = $books->getIterator()->current()->getSource();

        $seoManager = $this->get('seo_manager');
        $seoManager->setBookSeoData($book);

        if ($user = $this->getUser()) {
            $userRating = $bookPageService->getUserBookRating($user->getId(), $id);
        }

        return $this->render(
            '@App/Site/book.html.twig',
            [
                'book'                  => $book,
                'aside_featured_books'  => $bookPageService->getAsideFeaturedBooks($book),
                'slider_featured_books' => $bookPageService->getSliderFeaturedBooks($book),
                'book_rating_data'      => $bookPageService->getBookRatingData($id),
                'user_book_rating'      => $userRating ?? null,
                'reviews'               => $bookPageService->getBookReviews($id),
                'breadcrumbs'           => $seoManager->buildBreadcrumbs($book),
                'download_links'        => $litresBookManager->getDownloadLinks($book),
                'read_link'             => $litresBookManager->getReadOnlineLink($book),
                'show_genres_in_menu'   => true,
            ]
        );
    }

    /**
     * @return Response
     */
    public function tagsAction()
    {
        $em   = $this->getDoctrine()->getManager();
        /** @var QueryBuilder $qb */
        $qb   = $em->createQueryBuilder();
        $tags = $qb
            ->select('t, COUNT(b.id) as count_books')
            ->from('AppBundle:Tag', 't')
            ->leftJoin('t.books', 'b')
            ->orderBy('count_books', 'DESC')
            ->addGroupBy('t.id')
            ->setMaxResults(150)
            ->getQuery()
            ->getResult();

        $seoManager = $this->get('seo_manager');
        $seoManager->setTagsSeoData();

        return $this->render(
            '@App/Site/tags.html.twig',
            [
                'tags' => $tags,
            ]
        );
    }

    /**
     * @return Response
     */
    public function searchPageAction()
    {
        $seoManager = $this->get('seo_manager');
        $seoManager->setSearchSeoData();

        return $this->render('@App/Site/search.html.twig');
    }

    /**
     * @return RedirectResponse
     */
    public function generateRouteAction()
    {
        return new RedirectResponse('/');
    }
}
