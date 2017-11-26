<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AppBundle\Entity\Tag
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="tag",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="tag_ids", columns={"litres_id"})
 *     }
 * )
 */
class Tag implements PageInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="tag_id", type="integer")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="litres_id", type="integer")
     */
    private $litresId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"}, unique=true)
     * @ORM\Column(name="slug", type="string", nullable=true)
     */
    private $slug;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Book", mappedBy="tags", fetch="EXTRA_LAZY")
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getTitle();
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Tag
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int
     */
    public function getLitresId()
    {
        return $this->litresId;
    }

    /**
     * @param int $litresId
     *
     * @return Tag
     */
    public function setLitresId($litresId)
    {
        $this->litresId = $litresId;

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
     * @return Tag
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return int
     */
    public function getTagId()
    {
        return $this->getId();
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
     * @return Tag
     */
    public function setBooks($books)
    {
        $this->books = $books;

        return $this;
    }

    /**
     * @param Book $book
     *
     * @return Tag
     */
    public function addBook($book)
    {
        if (!$this->books->contains($book)) {
            $book->addTag($this);
        };

        return $this;
    }

    /**
     * @param Book $book
     *
     * @return Tag
     */
    public function removeBook($book)
    {
        if (!$this->books->contains($book)) {
            $book->removeTag($this);
        };

        return $this;
    }

    /**
     * @return string
     */
    public function getPathPrefix()
    {
        return 'tag';
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->getPathPrefix() . '/' . $this->getSlug();
    }
}
