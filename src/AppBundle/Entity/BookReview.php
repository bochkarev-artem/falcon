<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use AppBundle\Model\Timestampable\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * AppBundle\Entity\BookReview
 *
 * @ORM\Entity
 * @ORM\Table(name="book_review")
 */
class BookReview
{
    use TimestampableTrait;

    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="book_review_id", type="integer")
     */
    private $id;

    /**
     * @var Book $book
     *
     * @ORM\ManyToOne(targetEntity="Book", inversedBy="reviews", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="book_id")
     */
    private $book;

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reviews", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string $text
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var boolean $approved
     *
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved;

    /**
     * Initialize fields
     */
    public function __construct()
    {
        $this->approved = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return BookReview
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     *
     * @return BookReview
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

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
     * @return BookReview
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
     * @return BookReview
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
