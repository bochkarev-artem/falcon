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
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $seoManager = $this->get('seo_manager');
        $seoManager->setIndexSeo();

        return $this->render('AppBundle:Home:index.html.twig');
    }
}