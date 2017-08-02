<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use AppBundle\Model\Timestampable\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="author",
 *     uniqueConstraints={
 *           @ORM\UniqueConstraint(name="author_ids", columns={"document_id"})
 *     }
 * )
 */
class Author implements PageInterface
{
    use TimestampableTrait;

    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="author_id", type="integer")
     */
    private $id;

    /**
     * @var string $documentId
     *
     * @ORM\Column(name="document_id", type="string")
     */
    private $documentId;

    /**
     * @var ArrayCollection $books
     *
     * @ORM\ManyToMany(targetEntity="Book", mappedBy="authors", fetch="EXTRA_LAZY")
     */
    private $books;

    /**
     * @var string $firstName
     *
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string $lastName
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var string $middleName
     *
     * @ORM\Column(name="middle_name", type="string", nullable=true)
     */
    private $middleName;

    /**
     * @var string $slug
     *
     * @Gedmo\Slug(fields={"firstName", "lastName"}, unique=true)
     * @ORM\Column(name="slug", type="string", nullable=true)
     */
    private $slug;

    /**
     * @var string $lang
     *
     * @ORM\Column(name="lang", type="string", nullable=true)
     */
    private $lang;

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
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @param string $documentId
     *
     * @return Author
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;

        return $this;
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
     * @return Author
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
     * @return Author
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     *
     * @return Author
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $middleName = $this->getMiddleName() ? ' ' . $this->getMiddleName() . ' ' : ' ';

        return $this->getFirstName() . $middleName . $this->getLastName();
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
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
     * @return Author
     */
    public function setBooks($books)
    {
        $this->books = $books;

        return $this;
    }

    /**
     * @param Book $book
     *
     * @return Author
     */
    public function addBook(Book $book)
    {
        if (!$this->books->contains($book)) {
            $book->addAuthor($this);
        };

        return $this;
    }

    /**
     * @param Book $book
     *
     * @return Author
     */
    public function removeBook(Book $book)
    {
        if ($this->books->contains($book)) {
            $book->removeAuthor($this);
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
     * @return Author
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
        return (string) $this->getFullName();
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->getId();
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     *
     * @return Author
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return string
     */
    public function getPathPrefix()
    {
        return 'author';
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->getPathPrefix() . '/' . $this->getSlug();
    }
}
