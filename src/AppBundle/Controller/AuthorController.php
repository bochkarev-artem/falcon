<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthorController extends Controller
{
    public function showAction($id)
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Author');
        $genre     = $genreRepo->find($id);

        return $this->render('AppBundle:Author:show.html.twig', [
            'genre' => $genre,
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
