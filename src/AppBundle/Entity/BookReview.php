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
     * @var BookCard $bookCard
     *
     * @ORM\ManyToOne(targetEntity="BookCard", inversedBy="reviews", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="book_card_id", referencedColumnName="book_card_id")
     */
    private $bookCard;

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
     * @return BookCard
     */
    public function getBookCard()
    {
        return $this->bookCard;
    }

    /**
     * @param BookCard $bookCard
     *
     * @return BookReview
     */
    public function setBookCard($bookCard)
    {
        $this->bookCard = $bookCard;

        return $this;
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
}
