<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ads;
use AppBundle\Model\QueryParams;
use AppBundle\Service\BookPageService;
use AppBundle\Service\HomePageService;
use AppBundle\Service\LitresBookManager;
use AppBundle\Service\LocaleService;
use AppBundle\Service\QueryService;
use AppBundle\Service\SeoManager;
use Doctrine\ORM\QueryBuilder;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends Controller
{
    /**
     * @param HomePageService $homePageService
     * @param SeoManager      $seoManager
     *
     * @return Response
     */
    public function indexAction(HomePageService $homePageService, SeoManager $seoManager)
    {
        $seoManager->setHomeSeoData();

        return $this->render(
            '@App/Home/index.html.twig',
            [
                'show_genres_in_menu' => true,
                'featured_books'      => $homePageService->getFeaturedBooks(),
                'new_arrivals_books'  => $homePageService->getNewArrivalsBooks(),
                'popular_books'       => $homePageService->getPopularBooks(),
                'ad_main'             => $seoManager->getAdByPosition(Ads::POSITION_INDEX)
            ]
        );
    }

    /**
     * @param integer    $page
     * @param Request    $request
     * @param SeoManager $seoManager
     *
     * @return JsonResponse|Response
     */
    public function searchAction($page, Request $request, SeoManager $seoManager)
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

        $seoManager->setSearchSeoData();

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data,
                [
                    'ad_side' => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_SIDE),
                    'ad_top'  => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_TOP),
                ]
            )
        );
    }

    /**
     * @param integer    $page
     * @param Request    $request
     * @param SeoManager $seoManager
     *
     * @return JsonResponse|Response
     */
    public function newBooksAction($page, Request $request, SeoManager $seoManager)
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

        $seoManager->setNewBooksSeoData($page);

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data,
                [
                    'ad_side' => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_SIDE),
                    'ad_top'  => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_TOP),
                ]
            )
        );
    }

    /**
     * @param integer    $page
     * @param Request    $request
     * @param SeoManager $seoManager
     *
     * @return JsonResponse|Response
     */
    public function popularBooksAction($page, Request $request, SeoManager $seoManager)
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

        $seoManager->setPopularBooksSeoData($page);

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data,
                [
                    'ad_side' => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_SIDE),
                    'ad_top'  => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_TOP),
                ]
            )
        );
    }

    /**
     * @param Request       $request
     * @param integer       $id
     * @param integer       $page
     * @param SeoManager    $seoManager
     * @param LocaleService $localeService
     *
     * @return Response|JsonResponse
     */
    public function showGenreAction(Request $request, $id, $page, SeoManager $seoManager, LocaleService $localeService)
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
                'slug'   => $localeService->getLocaleField($genre, 'slug', $request->getLocale()),
                'prefix' => $genre->getPathPrefix()
            ],
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager->setGenreSeoData($genre, $page);

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data,
                [
                    'breadcrumbs' => $seoManager->buildBreadcrumbs($genre),
                    'ad_side'     => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_SIDE),
                    'ad_top'      => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_TOP),                ]
            )
        );
    }

    /**
     * @param Request    $request
     * @param integer    $id
     * @param integer    $page
     * @param SeoManager $seoManager
     *
     * @return Response|JsonResponse
     */
    public function showAuthorAction(Request $request, $id, $page, SeoManager $seoManager)
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

        $seoManager->setAuthorSeoData($author, $page);

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data,
                [
                    'breadcrumbs' => $seoManager->buildBreadcrumbs($author),
                    'ad_side'     => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_SIDE),
                    'ad_top'      => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_TOP),
                ]
            )
        );
    }

    /**
     * @param Request    $request
     * @param integer    $id
     * @param integer    $page
     * @param SeoManager $seoManager
     *
     * @return Response|JsonResponse
     */
    public function showSequenceAction(Request $request, $id, $page, SeoManager $seoManager)
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

        $seoManager->setSequenceSeoData($sequence, $page);

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data,
                [
                    'breadcrumbs' => $seoManager->buildBreadcrumbs($sequence),
                    'ad_side'     => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_SIDE),
                    'ad_top'      => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_TOP),
                ]
            )
        );
    }

    /**
     * @param Request    $request
     * @param integer    $id
     * @param integer    $page
     * @param SeoManager $seoManager
     *
     * @return Response|JsonResponse
     */
    public function showTagAction(Request $request, $id, $page, SeoManager $seoManager)
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

        $seoManager->setTagSeoData($tag, $page);

        return $this->render(
            '@App/Site/list_page.html.twig',
            array_merge(
                $data, [
                    'breadcrumbs' => $seoManager->buildBreadcrumbs($tag),
                    'ad_side'     => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_SIDE),
                    'ad_top'      => $seoManager->getAdByPosition(Ads::POSITION_CATALOG_TOP),
                ]
            )
        );
    }

    /**
     * @param Request     $request
     * @param QueryParams $queryParams
     *
     * @return JsonResponse|array
     */
    protected function prepareViewData($request, $queryParams)
    {
        $defaultView = $this->getParameter('default_page_view');
        $cookieName  = $this->getParameter('cookie.page_view_name');
        $cookieView  = $request->cookies->get($cookieName, $defaultView);

        return [
            'books' => $this->get('AppBundle\Service\QueryService')->find($queryParams),
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
     * @param QueryService $queryService
     * @param BookPageService $bookPageService
     * @param LitresBookManager $litresBookManager
     * @param SeoManager $seoManager
     *
     * @return Response
     */
    public function showBookAction(
        int $id,
        QueryService $queryService,
        BookPageService $bookPageService,
        LitresBookManager $litresBookManager,
        SeoManager $seoManager
    ) {
        $queryParams = new QueryParams();
        $queryParams
            ->setFilterId($id)
            ->setSize(1);

        $books = $queryService->find($queryParams);
        if (!$bookData = $books->getIterator()->current()) {
            throw $this->createNotFoundException();
        }

        $book = $bookData->getSource();
        $seoManager->setBookSeoData($book);

        if ($user = $this->getUser()) {
            $userRating = $bookPageService->getUserBookRating($user->getId(), $id);
        }

        $asideFeaturedBooks = $bookPageService->getAsideFeaturedBooks($book);
        $sliderFeaturedBooks = $bookPageService->getSliderFeaturedBooks($book);

        return $this->render(
            '@App/Site/book.html.twig',
            [
                'book'                  => $book,
                'aside_featured_books'  => $asideFeaturedBooks ?? $asideFeaturedBooks->getCurrentPageResults(),
                'slider_featured_books' => $sliderFeaturedBooks ?? $sliderFeaturedBooks->getCurrentPageResults(),
                'book_rating_data'      => $bookPageService->getBookRatingData($id),
                'user_book_rating'      => $userRating ?? null,
                'reviews'               => $bookPageService->getBookReviews($id),
                'breadcrumbs'           => $seoManager->buildBreadcrumbs($book),
                'download_links'        => $litresBookManager->getDownloadLinks($book),
                'read_link'             => $litresBookManager->getReadOnlineLink($book),
                'show_genres_in_menu'   => true,
                'ad_top'                => $seoManager->getAdByPosition(Ads::POSITION_BOOK_TOP),
                'ad_mobile'             => $seoManager->getAdByPosition(Ads::POSITION_BOOK_MOBILE),
                'ad_bottom'             => $seoManager->getAdByPosition(Ads::POSITION_BOOK_BOTTOM),
            ]
        );
    }

    /**
     * @param SeoManager $seoManager
     *
     * @return Response
     */
    public function tagsAction(SeoManager $seoManager)
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

        $seoManager->setTagsSeoData();

        return $this->render(
            '@App/Site/tags.html.twig',
            [
                'tags' => $tags,
            ]
        );
    }

    /**
     * @param SeoManager $seoManager
     *
     * @return Response
     */
    public function searchPageAction(SeoManager $seoManager)
    {
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
