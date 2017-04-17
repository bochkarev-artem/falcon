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

    const STATUS_PENDING  = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

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
     * @var integer $status
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var string $rejectReason
     *
     * @ORM\Column(name="reject_reason", type="string", nullable=true)
     */
    private $rejectReason;

    /**
     * Initialize fields
     */
    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
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
     * @return integer
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param integer $status
     *
     * @return BookReview
     */
    public function setStatus($status)
    {
        $this->status = $status;

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

    /**
     * @return string
     */
    public function getRejectReason()
    {
        return $this->rejectReason;
    }

    /**
     * @param string $rejectReason
     *
     * @return BookReview
     */
    public function setRejectReason($rejectReason)
    {
        $this->rejectReason = $rejectReason;

        return $this;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return (string) $this->getId();
    }
}
