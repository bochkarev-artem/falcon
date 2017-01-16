<?php

namespace AppBundle\Controller;

use AppBundle\Model\Pagination;
use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        $tagRepo      = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $tag          = $tagRepo->find($id);
        $pagination   = new Pagination($page, $defaultPerPage);

        return $this->render('AppBundle:Tag:show.html.twig', [
            'books'      => $books,
            'tag'        => $tag,
            'url_page'   => $tag->getPath() . '/page/',
            'pagination' => $pagination->paginate($queryResult->getTotalHits())
        ]);
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
