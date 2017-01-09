<?php

namespace AppBundle\Controller;

use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BookController extends Controller
{
    public function showAction($id)
    {
        $queryParams = new QueryParams();
        $queryParams->setFilterId($id);

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();

        if (!$book = array_shift($books)) {
            throw $this->createNotFoundException();
        } else {
            $book = $book->getSource();
        }

        return $this->render('AppBundle:Book:show.html.twig', [
            'book' => $book
        ]);
    }

    public function listAction()
    {
        $bookRepo = $this->getDoctrine()->getRepository('AppBundle:Book');
        $books    = $bookRepo->findAll();

        return $this->render('AppBundle:Book:list.html.twig', [
            'books' => $books
        ]);
    }
}
