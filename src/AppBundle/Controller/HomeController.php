<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeController
 * @package AppBundle\Controller
 */
class HomeController extends Controller
{
    const FEATURED_HOME_COUNT = 9;

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $bookIds  = [];
        $bookRepo = $this->getDoctrine()->getRepository('AppBundle:Book');
        $books    = $bookRepo->findBy(['featuredHome' => true], [], self::FEATURED_HOME_COUNT);

        foreach ($books as $book) {
            $bookIds[] = $book->getId();
        }

        $homePageService = $this->get('home_page_service');
        $featuresBooks   = $homePageService->getFeaturedBooks($bookIds);

        $seoManager = $this->get('seo_manager');
        $seoManager->setIndexSeo();

        return $this->render('AppBundle:Home:index.html.twig', [
            'show_genres_in_menu' => true,
            'featured_books'      => $featuresBooks
        ]);
    }
}