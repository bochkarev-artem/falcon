<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 * @package AppBundle\Controller
 */
class HomeController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $homePageService = $this->get('home_page_service');
        $seoManager      = $this->get('seo_manager');
        $seoManager->setIndexSeoData();

        return $this->render('AppBundle:Home:index.html.twig', [
            'show_genres_in_menu' => true,
            'featured_books'      => $homePageService->getFeaturedBooks(),
            'new_arrivals_books'  => $homePageService->getNewArrivalsBooks(),
        ]);
    }
}