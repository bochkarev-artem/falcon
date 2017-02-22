<?php
namespace AppBundle\Security;

use AppBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthFOSUserProvider extends BaseFOSUBProvider
{
    /**
     * @var array
     */
    protected $properties = [
        'identifier' => 'id',
    ];

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        // get property from provider configuration by provider name
        // , it will return `facebook_id` in that case (see service definition below)
        $property = $this->getProperty($response);
        $username = $response->getUsername(); // get the unique user identifier
        //we "disconnect" previously connected users
        $existingUser = $this->userManager->findUserBy([$property => $username]);
        if (null !== $existingUser) {
            // set current user id and token to null for disconnect
            // ...

            $this->userManager->updateUser($existingUser);
        }
        // we connect current user, set current user id and token
        // ...
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $userEmail = $response->getEmail();
        $property  = $this->getProperty($response);
        $user      = $this->userManager->findUserBy([$property => $userEmail]);

        if (null === $user) {
            $user = new User();
            $user->setUsername($response->getRealName());
            $user->setEmail($userEmail);
            $user->setPassword('');
            $user->setEnabled(true);

            $providerSetter = 'set' . ucfirst($property);
            $user->$providerSetter($userEmail);

            $this->userManager->updateUser($user);

            return $user;
        }

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        $user->$setter($response->getAccessToken());

        return $user;
    }
}