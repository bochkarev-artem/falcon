<?php
namespace AppBundle\Security;

use AppBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use Symfony\Component\PropertyAccess\PropertyAccessor;
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
     * @throws \Exception
     */
    protected function createUser(UserResponseInterface $response)
    {
        $user           = new User();
        $email          = $response->getEmail();
        $responseId     = isset($response->getResponse()['id']) ? $response->getResponse()['id'] : false;
        $id             = $email ?? $responseId;
        $property       = $this->getProperty($response);
        $providerSetter = 'set' . ucfirst($property);

        if (!$id) {
            throw new \Exception(sprintf("id is not set for %s and email %s", $property, $email), 500);
        }

        $firstName   = $response->getFirstName();
        $lastName    = $response->getLastName();
        $pictureData = $response->getPath('profilepicture');
        $picture     = $response->getProfilePicture();
        $picture     = is_array($pictureData) ? preg_replace('/%/', $picture, $pictureData[0]) : $picture;
        $user->setUsername($id);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setPicture($picture);
        $user->setEmail($id);
        $user->setPassword('');
        $user->setEnabled(true);
        $user->$providerSetter($id);

        $this->userManager->updateUser($user);

        return $user;
    }

    /**
     * @param UserResponseInterface $response
     *
     * @return FOSUserInterface|User
     * @throws \Exception
     */
    protected function prepareUser(UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $id       = $response->getEmail() ?? $response->getResponse()['id'];

        if (!$id) {
            throw new \Exception('Not allowed auth service');
        }

        if ($user = $this->userManager->findUserBy([$property => $id])) {
            return $user;
        }

        $user = $this->userManager->findUserByEmail($id);

        if ($user) {
            $providerSetter = 'set' . ucfirst($property);
            $user->$providerSetter($id);
            $this->userManager->updateUser($user);
        } else {
            $user = $this->createUser($response);
        }

        return $user;
    }
}
