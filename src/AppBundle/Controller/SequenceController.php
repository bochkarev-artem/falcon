<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SequenceController extends Controller
{
    public function showAction($id)
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Sequence');
        $genre     = $genreRepo->find($id);

        return $this->render('AppBundle:Sequence:show.html.twig', [
            'genre' => $genre,
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
