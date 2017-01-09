<?php

namespace AppBundle\Controller;

use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SequenceController extends Controller
{
    /**
     * @param integer $id
     * @param integer $page
     *
     * @return Response
     */
    public function showAction($id, $page)
    {
        $defaultPerPage = $this->getParameter('default_per_page');

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

        return $this->render('AppBundle:Sequence:show.html.twig', [
            'books' => $books,
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
