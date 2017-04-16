<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AppBundle\Entity\BookCard
 *
 * @ORM\Entity
 * @ORM\Table(name="book_card")
 */
class BookCard
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="book_card_id", type="integer")
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
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="bookCards", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="book_id")
     */
    private $book;

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bookCards", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var ArrayCollection $reviews
     *
     * @ORM\OneToMany(targetEntity="BookReview", mappedBy="bookCard", fetch="EXTRA_LAZY")
     */
    private $reviews;

    /**
     * Initialize fields
     */
    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

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
     * @return BookCard
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
     * @return BookCard
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
     * @return BookCard
     */
    public function setUser($user)
    {
        $this->user = $user;

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
     * @return BookCard
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;

        return $this;
    }

    /**
     * @param BookReview $bookReview
     *
     * @return BookCard
     */
    public function addBookReview($bookReview)
    {
        if (!$this->reviews->contains($bookReview)) {
            $this->reviews->add($bookReview);
        }

        return $this;
    }

    /**
     * @param BookReview $bookReview
     *
     * @return BookCard
     */
    public function removeBookReview($bookReview)
    {
        if ($this->reviews->contains($bookReview)) {
            $this->reviews->remove($bookReview);
        }

        return $this;
    }
}
