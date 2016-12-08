<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BookController extends Controller
{
    public function showAction($id)
    {
        $bookRepo = $this->getDoctrine()->getRepository('AppBundle:Book');
        $book     = $bookRepo->find($id);

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
