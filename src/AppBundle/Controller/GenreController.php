<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GenreController extends Controller
{
    public function showAction($id)
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Genre');
        $genre     = $genreRepo->find($id);

        return $this->render('AppBundle:Genre:show.html.twig', [
            'genre' => $genre,
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
