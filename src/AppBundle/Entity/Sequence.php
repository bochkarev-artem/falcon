<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AppBundle\Entity\Sequence
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="sequence",
 *     uniqueConstraints={
 *           @ORM\UniqueConstraint(name="sequence_ids", columns={"litres_id"})
 *     }
 * )
 */
class Sequence
{
    /**
     * @var int $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="sequence_id", type="integer")
     */
    private $id;

    /**
     * @var int $litresId
     *
     * @ORM\Column(name="litres_id", type="integer")
     */
    private $litresId;

    /**
     * @var ArrayCollection $books
     *
     * @ORM\ManyToMany(targetEntity="Book", cascade={"persist", "remove"}, mappedBy="sequences", fetch="EXTRA_LAZY")
     */
    private $books;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var string $slug
     *
     * @Gedmo\Slug(fields={"name"}, unique=true)
     * @ORM\Column(name="slug", type="string", nullable=true)
     */
    private $slug;

    /**
     * @var int $number
     *
     * @ORM\Column(name="number", type="integer", nullable=true)
     */
    private $number;

    /**
     * Initialize fields
     */
    public function __construct()
    {
        $this->books = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Sequence
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return Sequence
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return integer
     */
    public function getLitresId()
    {
        return $this->litresId;
    }

    /**
     * @param integer $litresId
     *
     * @return Sequence
     */
    public function setLitresId($litresId)
    {
        $this->litresId = $litresId;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * @param ArrayCollection $books
     *
     * @return Sequence
     */
    public function setBooks($books)
    {
        $this->books = $books;

        return $this;
    }

    /**
     * @param Book $book
     *
     * @return self
     */
    public function addBook(Book $book)
    {
        if (!$this->books->contains($book)) {
            $book->addSequence($this);
        };

        return $this;
    }

    /**
     * @param Book $book
     *
     * @return self
     */
    public function removeBook(Book $book)
    {
        if ($this->books->contains($book)) {
            $book->removeSequence($this);
        };

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return Sequence
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}