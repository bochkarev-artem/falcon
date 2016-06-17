<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BookController extends Controller
{
    public function showAction($id)
    {
        return $this->render('AppBundle:Book:show.html.twig', [

        ]);
    }

    public function listAction()
    {
        return $this->render('AppBundle:Book:list.html.twig', [

        ]);
    }
}
