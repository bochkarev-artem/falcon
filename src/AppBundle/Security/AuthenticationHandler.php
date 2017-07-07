<?php
namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, LogoutSuccessHandlerInterface
{
    /**
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $referer = $request->headers->get('referer');
        $referer = $referer ?? $request->getPathInfo();

        return new RedirectResponse($referer);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function onLogoutSuccess(Request $request)
    {
        $referer = $request->headers->get('referer');
        $referer = $referer ?? $request->getPathInfo();

        return new RedirectResponse($referer);
    }
}
