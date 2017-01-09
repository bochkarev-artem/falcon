<?php

namespace AppBundle\Controller;

use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TagController extends Controller
{
    public function showAction($id)
    {
        $queryParams = new QueryParams();
        $queryParams->setFilterTags($id);

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();

        return $this->render('AppBundle:Tag:show.html.twig', [
            'books' => $books,
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
