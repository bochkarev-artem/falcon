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
     * @param Request $request
     * @param integer $id
     * @param integer $page
     *
     * @return Response
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
            'entity'   => $genre
        ]);

        if ($request->isXmlHttpRequest()) {
            return $data;
        }

        return $this->render('AppBundle:Genre:show.html.twig', $data);
    }

    /**
     * @param Request $request
     * @param integer $id
     * @param integer $page
     *
     * @return Response
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

        $authorRepo   = $this->getDoctrine()->getRepository('AppBundle:Author');
        $author       = $authorRepo->find($id);

        $data = $this->prepareViewData($request, $queryParams, [
            'page'     => $page,
            'per_page' => $defaultPerPage,
            'entity'   => $author
        ]);

        if ($request->isXmlHttpRequest()) {
            return $data;
        }

        return $this->render('AppBundle:Author:show.html.twig', $data);
    }

    /**
     * @param Request $request
     * @param integer $id
     * @param integer $page
     *
     * @return Response
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
            'entity'   => $sequence
        ]);

        if ($request->isXmlHttpRequest()) {
            return $data;
        }

        return $this->render('AppBundle:Sequence:show.html.twig', $data);
    }

    /**
     * @param Request $request
     * @param integer $id
     * @param integer $page
     *
     * @return Response
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
            'entity'   => $tag
        ]);

        if ($request->isXmlHttpRequest()) {
            return $data;
        }

        return $this->render('AppBundle:Tag:show.html.twig', $data);
    }

    /**
     * @param Request     $request
     * @param QueryParams $queryParams
     * @param array       $params
     *
     * @return JsonResponse|array
     */
    protected function prepareViewData($request, $queryParams, $params) {
        $defaultView  = $this->getParameter('default_page_view');
        $cookieName   = $this->getParameter('cookie.page_view_name');
        $cookieView   = $request->cookies->get($cookieName, $defaultView);
        $view         = $request->get('view', $cookieView);
        $perPage      = $params['per_page'];
        $entity       = $params['entity'];
        $page         = $params['page'];

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();
        $pagination   = new Pagination($page, $perPage);

        $data = [
            'show_author' => true,
            'books'       => $books,
            'view'        => $view,
            'current_url' => $request->getPathInfo(),
            'pagination'  => $pagination->paginate($queryResult->getTotalHits()),
            'entity'      => $entity,
            'url_page'    => '/' . $entity->getPath() . '/page/',
        ];

        if ($request->isXmlHttpRequest()) {
            $templates = [
                'column' => 'AppBundle:Elements/View:column.html.twig',
                'list'   => 'AppBundle:Elements/View:list.html.twig',
                'grid'   => 'AppBundle:Elements/View:grid.html.twig',
            ];

            $template = isset($templates[$view]) ? $templates[$view] : 'AppBundle:Elements/View:column.html.twig';

            $responseData = [
                'page'   => $this->renderView($template, $data),
                'status' => true,
            ];

            $response = new JsonResponse($responseData);
            $cookie   = new Cookie($cookieName, $view);

            $response->headers->setCookie($cookie);

            return $response;
        }

        return $data;
    }

    public function listGenreAction()
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Genre');
        $genres    = $genreRepo->findAll();

        return $this->render('AppBundle:Genre:list.html.twig', [
            'genres' => $genres,
        ]);
    }
}
