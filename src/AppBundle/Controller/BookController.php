<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BookController extends Controller
{
    /**
     * @param integer $userId
     * @param integer $bookId
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addBookRatingAction($userId, $bookId, Request $request)
    {
        $rating   = $request->request->get('rating');
        $bookRepo = $this->getDoctrine()->getRepository('AppBundle:Book');
        $book     = $bookRepo->find($bookId);
        // TODO set and get real rating

        return new JsonResponse(['rating' => $rating]);
    }
}
