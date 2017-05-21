<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @return Response
     */
    public function profileAction()
    {
        if (!$user = $this->getUser()) {
            return new RedirectResponse('/');
        }

        $seoManager = $this->get('seo_manager');
        $seoManager->setUserProfileSeoData();

        return $this->render('@App/User/profile.html.twig');
    }

    /**
     * @return Response
     */
    public function statsAction()
    {
        if (!$user = $this->getUser()) {
            return new RedirectResponse('/');
        }

        $pageService = $this->get('book_page_service');
        $userReviewCount = $pageService->getUserReviewsStatistic($user->getId());
        $reviewStats = $pageService->getReviewsStatistic(20);
        $seoManager  = $this->get('seo_manager');
        $seoManager->setUserProfileStatsSeoData();

        return $this->render(
            '@App/User/stats.html.twig',
            [
                'user_review_count' => $userReviewCount,
                'review_stats'      => $reviewStats
            ]
        );
    }

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
        $paginator   = $pageService->getUserRatings($user->getId(), $page, 10);
        $seoManager  = $this->get('seo_manager');
        $seoManager->setUserProfileRatingsSeoData();

        return $this->render(
            '@App/User/ratings-reviews.html.twig',
            [
                'view'       => 'ratings',
                'route_name' => 'user_profile_ratings',
                'books'      => $paginator,
            ]
        );
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
        $paginator   = $pageService->getUserReviews($user->getId(), $page, 10);
        $seoManager  = $this->get('seo_manager');
        $seoManager->setUserProfileReviewsSeoData();

        return $this->render(
            '@App/User/ratings-reviews.html.twig',
            [
                'view'       => 'reviews',
                'route_name' => 'user_profile_reviews',
                'books'      => $paginator,
            ]
        );
    }
}
