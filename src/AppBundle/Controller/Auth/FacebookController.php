<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Controller\Auth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class FacebookController
 * @package AppBundle\Controller
 */
class FacebookController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectAction()
    {
        return $this->get('oauth2.registry')
            ->getClient('facebook')
            ->redirect();
    }

    /**
     * @param Request $request
     */
    public function connectCheckAction(Request $request)
    {

    }
}