<?php

namespace AppBundle\Controller;

use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthorController extends Controller
{
    public function showAction($id)
    {
        $queryParams = new QueryParams();
        $queryParams->setFilterAuthors($id);

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();

        return $this->render('AppBundle:Author:show.html.twig', [
            'books' => $books,
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
