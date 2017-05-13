<?php

namespace AppBundle\Controller;

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

        $page        = $request->get('page', 1);
        $pageService = $this->get('book_page_service');
        $paginator   = $pageService->getUserRatings($user->getId(), $page);
        $seoManager  = $this->get('seo_manager');
        $seoManager->setUserProfileRatingsSeoData();

        return $this->render('@App/User/profile.html.twig', [
            'view'  => 'ratings',
            'books' => $paginator,
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

        $page        = $request->get('page', 1);
        $pageService = $this->get('book_page_service');
        $paginator   = $pageService->getUserReviews($user->getId(), $page);
        $seoManager = $this->get('seo_manager');
        $seoManager->setUserProfileReviewsSeoData();

        return $this->render('@App/User/profile.html.twig', [
            'view'  => 'reviews',
            'books' => $paginator,
        ]);
    }
}
