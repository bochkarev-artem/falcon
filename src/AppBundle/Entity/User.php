<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    private $facebookId;

    /**
     * @var string
     */
    private $facebookAccessToken;

    /**
     * @ORM\Column(name="yandex_id", type="string", length=255, nullable=true)
     */
    private $yandexId;

    /**
     * @var string
     */
    private $yandexAccessToken;

    /**
     * @ORM\Column(name="vkontakte_id", type="string", length=255, nullable=true)
     */
    private $vkontakteId;

    /**
     * @var string
     */
    private $vkontakteAccessToken;

    /**
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true)
     */
    private $googleId;

    /**
     * @var string
     */
    private $googleAccessToken;

    /**
     * @var string $firstName
     */
    protected $firstName;

    /**
     * @var string $lastName
     */
    protected $lastName;

    /**
     * @var ArrayCollection $ratings
     *
     * @ORM\OneToMany(targetEntity="BookRating", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $ratings;

    /**
     * @var ArrayCollection $reviews
     *
     * @ORM\OneToMany(targetEntity="BookReview", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $reviews;

    /**
     * Initialize fields
     */
    public function __construct()
    {
        parent::__construct();

        $this->ratings = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if (!empty($this->firstName) && !empty($this->lastName))
        {
            return $this->firstName . ' ' . $this->lastName;
        }

        return $this->username;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getFullName() ?: '-';
    }

    /**
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookAccessToken
     *
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebookAccessToken = $facebookAccessToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebookAccessToken;
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
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleAccessToken()
    {
        return $this->googleAccessToken;
    }

    /**
     * @param string $googleAccessToken
     *
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->googleAccessToken = $googleAccessToken;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getYandexId()
    {
        return $this->yandexId;
    }

    /**
     * @param mixed $yandexId
     *
     * @return User
     */
    public function setYandexId($yandexId)
    {
        $this->yandexId = $yandexId;

        return $this;
    }

    /**
     * @return string
     */
    public function getYandexAccessToken()
    {
        return $this->yandexAccessToken;
    }

    /**
     * @param string $yandexAccessToken
     *
     * @return User
     */
    public function setYandexAccessToken($yandexAccessToken)
    {
        $this->yandexAccessToken = $yandexAccessToken;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVkontakteId()
    {
        return $this->vkontakteId;
    }

    /**
     * @param mixed $vkontakteId
     *
     * @return User
     */
    public function setVkontakteId($vkontakteId)
    {
        $this->vkontakteId = $vkontakteId;

        return $this;
    }

    /**
     * @return string
     */
    public function getVkontakteAccessToken()
    {
        return $this->vkontakteAccessToken;
    }

    /**
     * @param string $vkontakteAccessToken
     *
     * @return User
     */
    public function setVkontakteAccessToken($vkontakteAccessToken)
    {
        $this->vkontakteAccessToken = $vkontakteAccessToken;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param ArrayCollection $ratings
     *
     * @return User
     */
    public function setRatings($ratings)
    {
        $this->ratings = $ratings;

        return $this;
    }

    /**
     * @param BookRating $rating
     *
     * @return User
     */
    public function addRating($rating)
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
        }

        return $this;
    }

    /**
     * @param BookRating $rating
     *
     * @return User
     */
    public function removeRating($rating)
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->remove($rating);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param ArrayCollection $reviews
     *
     * @return User
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;

        return $this;
    }

    /**
     * @param BookReview $review
     *
     * @return User
     */
    public function addReview($review)
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
        }

        return $this;
    }

    /**
     * @param BookReview $review
     *
     * @return User
     */
    public function removeReview($review)
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->remove($review);
        }

        return $this;
    }
}