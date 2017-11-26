<?php

namespace AppBundle\Controller;

use AppBundle\Service\BookPageService;
use AppBundle\Service\SeoManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @param SeoManager $seoManager
     *
     * @return Response
     */
    public function profileAction(SeoManager $seoManager)
    {
        if (!$user = $this->getUser()) {
            return new RedirectResponse('/');
        }

        $seoManager->setUserProfileSeoData();

        return $this->render('@App/User/profile.html.twig');
    }

    /**
     * @param BookPageService $pageService
     * @param SeoManager      $seoManager
     *
     * @return Response
     */
    public function statsAction(BookPageService $pageService, SeoManager $seoManager)
    {
        if (!$user = $this->getUser()) {
            return new RedirectResponse('/');
        }

        $userReviewCount = $pageService->getUserReviewsStatistic($user->getId());
        $reviewStats     = $pageService->getReviewsStatistic(20);
        $seoManager->setUserProfileStatsSeoData();

        return $this->render(
            '@App/User/stats.html.twig',
            [
                'user_review_count' => $userReviewCount,
                'review_stats'      => $reviewStats,
            ]
        );
    }

    /**
     * @param Request         $request
     * @param BookPageService $pageService
     * @param SeoManager      $seoManager
     *
     * @return Response
     */
    public function ratingsAction(Request $request, BookPageService $pageService, SeoManager $seoManager)
    {
        if (!$user = $this->getUser()) {
            return new RedirectResponse('/');
        }

        $page      = $request->get('page', 1);
        $paginator = $pageService->getUserRatings($user->getId(), $page, 10);
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
     * @param Request         $request
     * @param BookPageService $pageService
     * @param SeoManager      $seoManager
     *
     * @return Response
     */
    public function reviewsAction(Request $request, BookPageService $pageService, SeoManager $seoManager)
    {
        if (!$user = $this->getUser()) {
            return new RedirectResponse('/');
        }

        $page      = $request->get('page', 1);
        $paginator = $pageService->getUserReviews($user->getId(), $page, 10);
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
