<?php

namespace AppBundle\Controller;

use AppBundle\Model\Pagination;
use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends Controller
{
    /**
     * @param Request $request
     * @param integer $id
     * @param integer $page
     *
     * @return Response
     */
    public function showAction(Request $request, $id, $page)
    {
        $defaultPerPage = $this->getParameter('default_per_page');
        $defaultView    = $this->getParameter('default_page_view');
        $cookieName     = $this->getParameter('cookie.page_view_name');
        $cookieView     = $request->cookies->get($cookieName, $defaultView);
        $view           = $request->get('view', $cookieView);

        $queryParams = new QueryParams();
        $queryParams
            ->setFilterAuthors($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
        ;

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();

        $pagination   = new Pagination($page, $defaultPerPage);
        $authorRepo   = $this->getDoctrine()->getRepository('AppBundle:Author');
        $author       = $authorRepo->find($id);

        $data = [
            'show_genre'  => true,
            'books'       => $books,
            'view'        => $view,
            'current_url' => $request->getPathInfo(),
            'pagination'  => $pagination->paginate($queryResult->getTotalHits()),
            'author'      => $author,
            'url_page'    => '/' . $author->getPath() . '/page/',
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

        return $this->render('AppBundle:Author:show.html.twig', $data);
    }

    public function listAction()
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Author');
        $genres    = $genreRepo->findAll();

        return $this->render('AppBundle:Author:list.html.twig', [
            'genres' => $genres,
        ]);
    }
}
