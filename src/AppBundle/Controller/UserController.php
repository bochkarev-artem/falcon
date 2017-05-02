<?php

namespace AppBundle\Controller;

use AppBundle\Model\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function ratingsAction(Request $request)
    {
        if (!$user = $this->getUser()) {
            return new RedirectResponse('/');
        }

        $perPage     = $this->getParameter('default_per_page');
        $page        = $request->get('page', 1);
        $offset      = ($page - 1) * $perPage;
        $pageService = $this->get('book_page_service');
        $bookData    = $pageService->getUserRatings($user->getId());
        $bookIds     = array_keys($bookData);
        $bookRepo    = $this->get('doctrine')->getRepository('AppBundle:Book');
        $books       = $bookRepo->findBy(['id' => $bookIds], ['updatedOn' => 'DESC'], $perPage, $offset);
        $pagination  = new Pagination($page, $perPage);

        $seoManager = $this->get('seo_manager');
        $seoManager->setUserProfileRatingsSeoData();

        return $this->render('@App/User/profile.html.twig', [
            'view'           => 'ratings',
            'books'          => $books,
            'bookRatings'    => $bookData,
            'pagination'     => $pagination->paginate(count($bookIds)),
            'pagination_url' => '/user-profile/ratings/page/',
            'show_author'    => true,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function reviewsAction(Request $request)
    {
        if (!$user = $this->getUser()) {
            return new RedirectResponse('/');
        }

        $perPage     = $this->getParameter('default_per_page');
        $page        = $request->get('page', 1);
        $offset      = ($page - 1) * $perPage;
        $pageService = $this->get('book_page_service');
        $bookData    = $pageService->getUserReviews($user->getId());
        $bookIds     = array_keys($bookData);
        $bookRepo    = $this->get('doctrine')->getRepository('AppBundle:Book');
        $books       = $bookRepo->findBy(['id' => $bookIds], ['updatedOn' => 'DESC'], $perPage, $offset);
        $pagination  = new Pagination($page, $perPage);

        $seoManager = $this->get('seo_manager');
        $seoManager->setUserProfileReviewsSeoData();

        return $this->render('@App/User/profile.html.twig', [
            'view'           => 'reviews',
            'books'          => $books,
            'bookReviews'    => $bookData,
            'pagination'     => $pagination->paginate(count($bookIds)),
            'pagination_url' => '/user-profile/reviews/page/',
            'show_author'    => true,
        ]);
    }
}
