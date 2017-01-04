<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TagController extends Controller
{
    public function showAction($id)
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $genre     = $genreRepo->find($id);

        return $this->render('AppBundle:Tag:show.html.twig', [
            'genre' => $genre,
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
