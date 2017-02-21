<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Controller\Auth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class GoogleController
 * @package AppBundle\Controller
 */
class GoogleController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectAction()
    {
        return $this->get('oauth2.registry')
            ->getClient('google')
            ->redirect();
    }

    /**
     * @param Request $request
     */
    public function connectCheckAction(Request $request)
    {

    }
}