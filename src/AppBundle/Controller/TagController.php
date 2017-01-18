<?php

namespace AppBundle\Controller;

use AppBundle\Model\Pagination;
use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController extends Controller
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
            ->setFilterTags($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
        ;
        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();

        $data = [
            'show_author' => true,
            'books'       => $books,
            'view'        => $view,
            'current_url' => $request->getPathInfo(),
        ];

        if ($request->isXmlHttpRequest()) {
            if ($view == 'column') {
                $template = 'AppBundle:Elements/View:column.html.twig';
            } else {
                $template = 'AppBundle:Elements/View:list.html.twig';
            }

            $responseData = [
                'page'   => $this->renderView($template, $data),
                'status' => true,
            ];

            $response = new JsonResponse($responseData);
            $cookie   = new Cookie($cookieName, $view);

            $response->headers->setCookie($cookie);

            return $response;
        }

        $tagRepo      = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $tag          = $tagRepo->find($id);
        $pagination   = new Pagination($page, $defaultPerPage);

        $data = array_merge($data, [
            'tag'        => $tag,
            'url_page'   => '/' . $tag->getPath() . '/page/',
            'pagination' => $pagination->paginate($queryResult->getTotalHits()),
        ]);

        return $this->render('AppBundle:Tag:show.html.twig', $data);
    }

    public function listAction()
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $genres    = $genreRepo->findAll();

        return $this->render('AppBundle:Tag:list.html.twig', [
            'genres' => $genres,
        ]);
    }
}
