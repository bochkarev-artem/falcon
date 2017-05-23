<?php
namespace AppBundle\Security;

use AppBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\UserInterface as FOSUserInterface;

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
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        $existingUser = $this->userManager->findUserBy([$property => $username]);

        if (null !== $existingUser) {
            $this->userManager->updateUser($existingUser);
        }

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = $this->prepareUser($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        $user->$setter($response->getAccessToken());

        return $user;
    }

    /**
     * @param UserResponseInterface $response
     *
     * @return User
     */
    protected function createUser(UserResponseInterface $response)
    {
        $user          = new User();
        $userEmail     = $response->getEmail();
        $property      = $this->getProperty($response);
        $userFirstName = $response->getFirstName();
        $userLastName  = $response->getLastName();
        $responseArray = $response->getResponse();
        $userGender    = $responseArray['gender'] ?? '';
        $userPicture   = $response->getProfilePicture();
        $user->setUsername($response->getRealName());
        $user->setFirstName($userFirstName);
        $user->setLastName($userLastName);
        $user->setGender($userGender);
        $user->setPicture($userPicture);
        $user->setEmail($userEmail);
        $user->setPassword('');
        $user->setEnabled(true);

        $providerSetter = 'set' . ucfirst($property);
        $user->$providerSetter($userEmail);

        $this->userManager->updateUser($user);

        return $user;
    }

    /**
     * @param UserResponseInterface $response
     *
     * @return FOSUserInterface|User
     */
    protected function prepareUser(UserResponseInterface $response)
    {
        $property  = $this->getProperty($response);
        $userEmail = $response->getEmail();

        if ($user = $this->userManager->findUserBy([$property => $userEmail])) {
            return $user;
        }

        $user = $this->userManager->findUserByEmail($userEmail);

        if ($user) {
            $providerSetter = 'set' . ucfirst($property);
            $user->$providerSetter($userEmail);
            $this->userManager->updateUser($user);
        } else {
            $user = $this->createUser($response);
        }

        return $user;
    }
}