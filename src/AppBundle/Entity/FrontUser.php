<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="front_user")
 */
class FrontUser extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="api_key", unique=true)
     */
    protected $apiKey;

    /**
     * @var integer $facebookId
     * @ORM\Column(type="integer", name="facebook_id", unique=true)
     */
    protected $facebookId;

    /**
     * @var integer $googleId
     * @ORM\Column(type="integer", name="google_id", unique=true)
     */
    protected $googleId;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     *
     * @return FrontUser
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * @return integer
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param integer $facebookId
     *
     * @return FrontUser
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * @return integer
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * @param integer $googleId
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
    public function __toString()
    {
        return $this->getUsername();
    }
}