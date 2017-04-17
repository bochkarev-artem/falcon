<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BookRating;
use AppBundle\Entity\BookReview;
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
        $em         = $this->getDoctrine()->getManager();
        $rating     = $request->request->get('rating');
        $bookId     = $request->request->get('book_id');
        $bookRepo   = $this->getDoctrine()->getRepository('AppBundle:Book');
        $ratingRepo = $this->getDoctrine()->getRepository('AppBundle:BookRating');
        $user       = $this->getUser();
        $book       = $bookRepo->find($bookId);
        $bookRating = $ratingRepo->findOneBy(['user' => $user, 'book' => $book]);
        if ($bookRating) {
            $bookRating->setRating($rating);
        } else {
            $bookRating = new BookRating();
            $bookRating
                ->setBook($book)
                ->setUser($user)
                ->setRating($rating)
            ;
            $em->persist($bookRating);
        }
        $em->flush();

        $bookPageService = $this->get('book_page_service');
        $ratingData      = $bookPageService->getBookRatingData($bookId);

        return new JsonResponse(['rating' => $ratingData['rating'], 'total' => $ratingData['total']]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addBookReviewAction(Request $request)
    {
        $reviewText = $request->request->get('review');
        $response   = ['status' => false];

        if (strlen($reviewText) >= 500) {
            $em         = $this->getDoctrine()->getManager();
            $bookId     = $request->request->get('book_id');
            $bookRepo   = $this->getDoctrine()->getRepository('AppBundle:Book');
            $user       = $this->getUser();
            $book       = $bookRepo->find($bookId);
            $bookReview = new BookReview();
            $bookReview
                ->setBook($book)
                ->setUser($user)
                ->setText($reviewText)
            ;
            $em->persist($bookReview);
            $em->flush();

            $response = ['status' => true];
        }

        return new JsonResponse($response);
    }
}
