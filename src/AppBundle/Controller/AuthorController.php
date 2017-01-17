<?php

namespace AppBundle\Controller;

use AppBundle\Model\Pagination;
use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $view           = $request->get('view', $defaultView);

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

        $authorRepo   = $this->getDoctrine()->getRepository('AppBundle:Author');
        $author       = $authorRepo->find($id);
        $pagination   = new Pagination($page, $defaultPerPage);

        return $this->render('AppBundle:Author:show.html.twig', [
            'books'      => $books,
            'author'     => $author,
            'url_page'   => $author->getPath() . '/page/',
            'pagination' => $pagination->paginate($queryResult->getTotalHits()),
            'view'       => $view,
        ]);
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
