<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="front_user")
 */
class FrontUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $facebookId
     * @ORM\Column(type="string", name="facebook_id", unique=true, nullable=true)
     */
    protected $facebookId;

    /**
     * @var string $googleId
     * @ORM\Column(type="string", name="google_id", unique=true, nullable=true)
     */
    protected $googleId;

    /**
     * @var string $username
     * @ORM\Column(type="string", name="username", unique=true)
     */
    protected $username;

    /**
     * @var string $email
     * @ORM\Column(type="string", name="email", unique=true)
     */
    protected $email;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookId
     *
     * @return FrontUser
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * @param string $googleId
     *
     * @return FrontUser
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return FrontUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return FrontUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {

    }

    public function getSalt()
    {

    }

    public function eraseCredentials()
    {

    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }
}