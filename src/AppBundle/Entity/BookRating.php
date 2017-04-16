<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppBundle\Entity\BookRating
 *
 * @ORM\Entity
 * @ORM\Table(name="book_rating")
 */
class BookRating
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="book_rating_id", type="integer")
     */
    private $id;

    /**
     * @var float $rating
     *
     * @ORM\Column(name="rating", type="float", nullable=true)
     */
    private $rating;

    /**
     * @var Book $book
     *
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="ratings", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="book_id")
     */
    private $book;

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ratings", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     *
     * @return BookRating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Book
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * @param Book $book
     *
     * @return BookRating
     */
    public function setBook($book)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return BookRating
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
