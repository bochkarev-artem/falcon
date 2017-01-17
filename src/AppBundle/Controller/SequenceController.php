<?php

namespace AppBundle\Controller;

use AppBundle\Model\Pagination;
use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SequenceController extends Controller
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
            ->setFilterSequences($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
        ;

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();

        $sequenceRepo = $this->getDoctrine()->getRepository('AppBundle:Sequence');
        $sequence     = $sequenceRepo->find($id);
        $pagination   = new Pagination($page, $defaultPerPage);

        return $this->render('AppBundle:Sequence:show.html.twig', [
            'books'      => $books,
            'sequence'   => $sequence,
            'url_page'   => $sequence->getPath() . '/page/',
            'pagination' => $pagination->paginate($queryResult->getTotalHits()),
            'view'       => $view,
        ]);
    }

    public function listAction()
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Sequence');
        $genres    = $genreRepo->findAll();

        return $this->render('AppBundle:Sequence:list.html.twig', [
            'genres' => $genres,
        ]);
    }
}
