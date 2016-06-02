<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SiteController
 * @package AppBundle\Controller
 */
class SiteController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
//        $litres = $this->get('litres_service');
//        $litres->getBooksData();

        return $this->render('site/index.html.twig');
    }
}
