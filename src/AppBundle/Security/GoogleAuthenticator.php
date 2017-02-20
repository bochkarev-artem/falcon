<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Security;

use Doctrine\ORM\EntityManager;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class GoogleAuthenticator
 * @package AppBundle\Security
 */
class GoogleAuthenticator extends SocialAuthenticator
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param ClientRegistry  $clientRegistry
     * @param EntityManager   $em
     * @param RouterInterface $router
     * @param Translator      $translator
     */
    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManager $em,
        RouterInterface $router,
        Translator $translator
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->em             = $em;
        $this->router         = $router;
        $this->translator     = $translator;
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
             $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * @param Request $request
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() == $this->router->generate('connect_google_check')) {
            return $this->fetchAccessToken($this->getGoogleClient());
        }

        return;
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return \AppBundle\Entity\User|null|object
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser   = $this->getGoogleClient()->fetchUserFromToken($credentials);
        $userRepo     = $this->em->getRepository('AppBundle:FrontUser');
        $existingUser = $userRepo->findOneBy(['googleId' => $googleUser->getId()]);
        if ($existingUser) {
            return $existingUser;
        }

        $email = $googleUser->getEmail();
        var_dump($email);
        die();
        $user  = $userRepo->findOneBy(['email' => $email]);

        $user->setGoogleId($googleUser->getId());
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @return GoogleClient|OAuth2Client
     */
    private function getGoogleClient()
    {
        return $this->clientRegistry->getClient('google');
    }

    /**
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => $this->translator->trans('front.auth.auth_required')
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}