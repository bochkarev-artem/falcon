<?php
namespace AppBundle\Security;

use AppBundle\Entity\FrontUser;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthFOSUserProvider extends BaseFOSUBProvider
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $properties = array(
        'identifier' => 'id',
    );

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager fOSUB user provider
     * @param array                $properties  property mapping
     * @param EntityManager        $em
     */
    public function __construct(UserManagerInterface $userManager, array $properties, EntityManager $em)
    {
        parent::__construct($userManager, $properties);

        $this->em = $em;
    }

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
        $user      = $this->em->getRepository('AppBundle:FrontUser')->findOneByEmail($userEmail);

        if (null === $user) {
            $user = new FrontUser();
            $user->setUsername($response->getRealName());
            $user->setEmail($response->getEmail());
            $user->setPassword('');
            $user->setEnabled(true);

            $this->em->persist($user);
            $this->em->flush($user);

            return $user;
        }

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        $user->$setter($response->getAccessToken());

        return $user;
    }
}