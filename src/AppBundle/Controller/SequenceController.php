<?php

namespace AppBundle\Controller;

use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SequenceController extends Controller
{
    public function showAction($id)
    {
        $queryParams = new QueryParams();
        $queryParams->setFilterSequences($id);

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
