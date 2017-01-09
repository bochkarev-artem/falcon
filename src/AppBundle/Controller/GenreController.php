<?php

namespace AppBundle\Controller;

use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GenreController extends Controller
{
    public function showAction($id)
    {
        $queryParams = new QueryParams();
        $queryParams->setFilterGenres($id);

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();

        return $this->render('AppBundle:Genre:show.html.twig', [
            'books' => $books,
        ]);
    }

    public function listAction()
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Genre');
        $genres    = $genreRepo->findAll();

        return $this->render('AppBundle:Genre:list.html.twig', [
            'genres' => $genres,
        ]);
    }
}
