<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BookCard;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BookController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addBookRatingAction(Request $request)
    {
        $em           = $this->getDoctrine()->getManager();
        $rating       = $request->request->get('rating');
        $bookId       = $request->request->get('book_id');
        $bookRepo     = $this->getDoctrine()->getRepository('AppBundle:Book');
        $bookCardRepo = $this->getDoctrine()->getRepository('AppBundle:BookCard');
        $user         = $this->getUser();
        $book         = $bookRepo->find($bookId);
        $bookCard     = $bookCardRepo->findOneBy(['user' => $user, 'book' => $book]);
        if ($bookCard) {
            $bookCard->setRating($rating);
        } else {
            $bookCard = new BookCard();
            $bookCard
                ->setBook($book)
                ->setUser($user)
                ->setRating($rating)
            ;
            $em->persist($bookCard);
        }
        $em->flush();

        $bookPageService = $this->get('book_page_service');
        $ratingData      = $bookPageService->getBookRatingData($bookId);

        return new JsonResponse(['rating' => $ratingData['rating'], 'total' => $ratingData['total']]);
    }
}
