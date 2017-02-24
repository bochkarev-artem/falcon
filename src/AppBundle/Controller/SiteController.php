<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Model\Pagination;
use AppBundle\Model\QueryParams;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends Controller
{
    /**
     * @param integer $page
     * @param Request $request
     *
     * @return Response|JsonResponse
     */
    public function searchAction($page = 1, Request $request)
    {
        $defaultPerPage = $this->getParameter('default_per_page');
        $query          = $request->get('query');

        $queryParams = new QueryParams();
        $queryParams
            ->setSearchQuery($query)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
        ;

        $data = $this->prepareViewData($request, $queryParams, [
            'page'     => $page,
            'per_page' => $defaultPerPage,
        ]);

        $data = array_merge($data, [
            'show_author'    => true,
            'query'          => $query,
            'pagination_url' => $this->generateUrl('search') . '/page/',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setSearchSeoData();

        return $this->render('AppBundle:Site:list_page.html.twig', $data);
    }

    /**
     * @param integer $page
     * @param Request $request
     *
     * @return Response|JsonResponse
     */
    public function newBooksAction($page = 1, Request $request)
    {
        $defaultPerPage = $this->getParameter('default_per_page');
        $isPageIndexed  = $page === 1;

        $queryParams = new QueryParams();
        $queryParams
            ->setSort(QueryParams::SORT_DATE_DESC)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
        ;

        $data = $this->prepareViewData($request, $queryParams, [
            'page'     => $page,
            'per_page' => $defaultPerPage,
        ]);

        $data = array_merge($data, [
            'show_author'    => true,
            'pagination_url' => $this->generateUrl('new_books') . '/page/',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setNewBooksSeoData($isPageIndexed);

        return $this->render('AppBundle:Site:list_page.html.twig', $data);
    }

    /**
     * @param integer $page
     * @param Request $request
     *
     * @return Response|JsonResponse
     */
    public function popularBooksAction($page = 1, Request $request)
    {
        $defaultPerPage = $this->getParameter('default_per_page');
        $isPageIndexed  = $page === 1;

        $queryParams = new QueryParams();
        $queryParams
            ->setSort(QueryParams::SORT_RATING_DESC)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
        ;

        $data = $this->prepareViewData($request, $queryParams, [
            'page'     => $page,
            'per_page' => $defaultPerPage,
        ]);

        $data = array_merge($data, [
            'show_author'    => true,
            'pagination_url' => $this->generateUrl('popular_books') . '/page/',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setPopularBooksSeoData($isPageIndexed);

        return $this->render('AppBundle:Site:list_page.html.twig', $data);
    }

    /**
     * @param integer $page
     * @param Request $request
     *
     * @return Response|JsonResponse
     */
    public function audioBooksAction($page = 1, Request $request)
    {
        $defaultPerPage = $this->getParameter('default_per_page');
        $isPageIndexed  = $page === 1;

        $queryParams = new QueryParams();
        $queryParams
            ->setSort(QueryParams::SORT_RATING_DESC)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
            ->setBookType(Book::TYPE_AUDIO)
        ;

        $data = $this->prepareViewData($request, $queryParams, [
            'page'     => $page,
            'per_page' => $defaultPerPage,
        ]);

        $data = array_merge($data, [
            'show_author'       => true,
            'hide_audio_ribbon' => true,
            'pagination_url'    => $this->generateUrl('audio_books') . '/page/',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setAudioBooksSeoData($isPageIndexed);

        return $this->render('AppBundle:Site:list_page.html.twig', $data);
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
        $defaultPerPage = $this->getParameter('default_per_page');
        $isPageIndexed  = $page === 1;
        $sortOrder      = $request->get('sort', QueryParams::SORT_NO);

        $queryParams = new QueryParams();
        $queryParams
            ->setFilterGenres($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
            ->setSort($sortOrder)
        ;

        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Genre');
        $genre     = $genreRepo->find($id);

        $data = $this->prepareViewData($request, $queryParams, [
            'page'     => $page,
            'per_page' => $defaultPerPage,
        ]);

        $data = array_merge($data, [
            'show_author'    => true,
            'genre'          => $genre,
            'pagination_url' => $this->buildPaginationUrl($genre->getPath()),
            'sort_order'     => $sortOrder
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setGenreSeoData($genre, $isPageIndexed);

        return $this->render('AppBundle:Site:list_page.html.twig', array_merge($data, [
            'breadcrumbs' => $seoManager->buildBreadcrumbs($genre)
        ]));
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
        $defaultPerPage = $this->getParameter('default_per_page');
        $isPageIndexed  = $page === 1;
        $sortOrder      = $request->get('sort', QueryParams::SORT_NO);

        $queryParams = new QueryParams();
        $queryParams
            ->setFilterAuthors($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
            ->setSort($sortOrder)
        ;

        $authorRepo = $this->getDoctrine()->getRepository('AppBundle:Author');
        $author     = $authorRepo->find($id);

        $data = $this->prepareViewData($request, $queryParams, [
            'page'     => $page,
            'per_page' => $defaultPerPage,
        ]);

        $data = array_merge($data, [
            'show_genre'     => true,
            'author'         => $author,
            'pagination_url' => $this->buildPaginationUrl($author->getPath()),
            'sort_order'     => $sortOrder
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setAuthorSeoData($author, $isPageIndexed);

        return $this->render('AppBundle:Site:list_page.html.twig', array_merge($data, [
            'breadcrumbs' => $seoManager->buildBreadcrumbs($author)
        ]));
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
        $defaultPerPage = $this->getParameter('default_per_page');
        $isPageIndexed  = $page === 1;
        $sortOrder      = $request->get('sort', QueryParams::SORT_NO);

        $queryParams = new QueryParams();
        $queryParams
            ->setFilterSequences($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
            ->setSort($sortOrder)
        ;

        $sequenceRepo = $this->getDoctrine()->getRepository('AppBundle:Sequence');
        $sequence     = $sequenceRepo->find($id);

        $data = $this->prepareViewData($request, $queryParams, [
            'page'     => $page,
            'per_page' => $defaultPerPage,
        ]);

        $data = array_merge($data, [
            'show_author'    => true,
            'sequence'       => $sequence,
            'pagination_url' => $this->buildPaginationUrl($sequence->getPath()),
            'sort_order'     => $sortOrder
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setSequenceSeoData($sequence, $isPageIndexed);

        return $this->render('AppBundle:Site:list_page.html.twig', array_merge($data, [
            'breadcrumbs' => $seoManager->buildBreadcrumbs($sequence)
        ]));
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
        $defaultPerPage = $this->getParameter('default_per_page');
        $isPageIndexed  = $page === 1;
        $sortOrder      = $request->get('sort', QueryParams::SORT_NO);

        $queryParams = new QueryParams();
        $queryParams
            ->setFilterTags($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
            ->setSort($sortOrder)
        ;

        $tagRepo = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $tag     = $tagRepo->find($id);

        $data = $this->prepareViewData($request, $queryParams, [
            'page'     => $page,
            'per_page' => $defaultPerPage,
        ]);

        $data = array_merge($data, [
            'show_author'    => true,
            'tag'            => $tag,
            'pagination_url' => $this->buildPaginationUrl($tag->getPath()),
            'sort_order'     => $sortOrder
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setTagSeoData($tag, $isPageIndexed);

        return $this->render('AppBundle:Site:list_page.html.twig', array_merge($data, [
            'breadcrumbs' => $seoManager->buildBreadcrumbs($tag)
        ]));
    }

    /**
     * @param Request     $request
     * @param QueryParams $queryParams
     * @param array       $params
     *
     * @return JsonResponse|array
     */
    protected function prepareViewData($request, $queryParams, $params)
    {
        $defaultView = $this->getParameter('default_page_view');
        $cookieName  = $this->getParameter('cookie.page_view_name');
        $cookieView  = $request->cookies->get($cookieName, $defaultView);
        $view        = $request->get('view', $cookieView);
        $perPage     = $params['per_page'];
        $page        = $params['page'];

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();
        $pagination   = new Pagination($page, $perPage);

        return [
            'books'       => $books,
            'page'        => $page,
            'view'        => $view,
            'current_url' => $request->getPathInfo(),
            'pagination'  => $pagination->paginate($queryResult->getTotalHits()),
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

        $template = isset($templates[$view]) ? $templates[$view] : 'AppBundle:Elements/View:column.html.twig';

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
        $queryParams->setFilterId($id);
        $queryParams->setSize(1);

        $queryService      = $this->get('query_service');
        $bookPageService   = $this->get('book_page_service');
        $litresBookManager = $this->get('litres_book_manager');
        $queryResult       = $queryService->query($queryParams);
        $books             = $queryResult->getResults();

        if (!$book = array_shift($books)) {
            throw $this->createNotFoundException();
        } else {
            $book = $book->getSource();
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setBookSeoData($book);

        return $this->render('AppBundle:Site:book.html.twig', [
            'book'                  => $book,
            'aside_featured_books'  => $bookPageService->getAsideFeaturedBooks($book),
            'slider_featured_books' => $bookPageService->getSliderFeaturedBooks($book),
            'breadcrumbs'           => $seoManager->buildBreadcrumbs($book),
            'download_links'        => $litresBookManager->getDownloadLinks($book),
            'read_link'             => $litresBookManager->getReadOnlineLink($book),
            'show_genres_in_menu'   => true,
        ]);
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
            ->getResult()
        ;

        $seoManager = $this->get('seo_manager');
        $seoManager->setTagsSeoData();

        return $this->render('AppBundle:Site:tags.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * @return Response
     */
    public function searchPageAction()
    {
        $seoManager = $this->get('seo_manager');
        $seoManager->setSearchSeoData();

        return $this->render('AppBundle:Site:search.html.twig');
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function buildPaginationUrl($url)
    {
        return '/' . $url . '/page/';
    }
}
