<?php

namespace AppBundle\Controller;

use AppBundle\Model\Pagination;
use AppBundle\Model\QueryParams;
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
            'show_genre'     => true,
            'query'          => $query,
            'pagination_url' => $this->generateUrl('search') . '/page/',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setSearchSeo();

        $response = $this->render('AppBundle:Search:show.html.twig', $data);

        return $response;
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

        $queryParams = new QueryParams();
        $queryParams
            ->setFilterGenres($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
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
            'pagination_url' => '/' . $genre->getPath() . '/page/',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setGenreSeo($genre);

        return $this->render('AppBundle:Genre:show.html.twig', $data);
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

        $queryParams = new QueryParams();
        $queryParams
            ->setFilterAuthors($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
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
            'pagination_url' => '/' . $author->getPath() . '/page/',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setAuthorSeo($author);

        return $this->render('AppBundle:Author:show.html.twig', $data);
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

        $queryParams = new QueryParams();
        $queryParams
            ->setFilterSequences($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
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
            'pagination_url' => '/' . $sequence->getPath() . '/page/',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setSequenceSeo($sequence);

        return $this->render('AppBundle:Sequence:show.html.twig', $data);
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

        $queryParams = new QueryParams();
        $queryParams
            ->setFilterTags($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
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
            'pagination_url' => '/' . $tag->getPath() . '/page/',
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->prepareJsonResponse($data);
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setTagSeo($tag);

        return $this->render('AppBundle:Tag:show.html.twig', $data);
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

        $data = [
            'books'       => $books,
            'page'        => $page,
            'view'        => $view,
            'current_url' => $request->getPathInfo(),
            'pagination'  => $pagination->paginate($queryResult->getTotalHits()),
        ];

        return $data;
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
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function showBookAction(Request $request, $id)
    {
        $queryParams = new QueryParams();
        $queryParams->setFilterId($id);

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();

        if (!$book = array_shift($books)) {
            throw $this->createNotFoundException();
        } else {
            $book = $book->getSource();
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setBookSeo($book);

        return $this->render('AppBundle:Book:show.html.twig', [
            'book' => $book
        ]);
    }

    /**
     * @return Response
     */
    public function listGenreAction()
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Genre');
        $genres    = $genreRepo->findAll();

        return $this->render('AppBundle:Genre:list.html.twig', [
            'genres' => $genres,
        ]);
    }
}
