<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BookRating;
use AppBundle\Entity\BookReview;
use AppBundle\Service\BookPageService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BookController extends Controller
{
    /**
     * @param Request         $request
     * @param BookPageService $bookPageService
     *
     * @return JsonResponse
     */
    public function addBookRatingAction(Request $request, BookPageService $bookPageService)
    {
        $response = ['status' => false];
        if ($user = $this->getUser()) {
            $em         = $this->getDoctrine()->getManager();
            $rating     = $request->request->get('rating');
            $bookId     = $request->request->get('book_id');
            $bookRepo   = $this->getDoctrine()->getRepository('AppBundle:Book');
            $ratingRepo = $this->getDoctrine()->getRepository('AppBundle:BookRating');
            $book       = $bookRepo->find($bookId);
            $bookRating = $ratingRepo->findOneBy(['user' => $user, 'book' => $book]);
            if ($bookRating) {
                $bookRating->setRating($rating);
            } else {
                $bookRating = new BookRating();
                $bookRating
                    ->setBook($book)
                    ->setUser($user)
                    ->setRating($rating);
                $em->persist($bookRating);
            }
            $em->flush();

            $ratingData = $bookPageService->getBookRatingData($bookId);
            $response   = [
                'rating' => $ratingData['rating'],
                'total'  => $ratingData['total'],
                'status' => true,
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addBookReviewAction(Request $request)
    {
        $response = ['status' => false];
        if ($user = $this->getUser()) {
            $reviewText = $request->request->get('review');
            if (strlen($reviewText) >= $this->getParameter('review_minimum_char')) {
                $em         = $this->getDoctrine()->getManager();
                $bookId     = $request->request->get('book_id');
                $bookRepo   = $this->getDoctrine()->getRepository('AppBundle:Book');
                $book       = $bookRepo->find($bookId);
                $bookReview = new BookReview();
                $bookReview
                    ->setBook($book)
                    ->setUser($user)
                    ->setText($reviewText);
                $em->persist($bookReview);
                $em->flush();

                $response = ['status' => true];
            }
        }

        return new JsonResponse($response);
    }
}
