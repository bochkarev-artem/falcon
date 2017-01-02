<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="author",
 *     uniqueConstraints={
 *           @ORM\UniqueConstraint(name="author_ids", columns={"document_id"})
 *     }
 * )
 */
class Author
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="author_id", type="integer")
     */
    private $id;

    /**
     * @var integer $litresHubId
     *
     * @ORM\Column(name="litres_hub_id", type="integer", nullable=true)
     */
    private $litresHubId;

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
     * @var integer $level
     *
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    private $level;

    /**
     * @var integer $recensesCount
     *
     * @ORM\Column(name="recenses_count", type="integer", nullable=true)
     */
    private $recensesCount;

    /**
     * @var integer $artsCount
     *
     * @ORM\Column(name="arts_count", type="integer", nullable=true)
     */
    private $artsCount;

    /**
     * @var string $photo
     *
     * @ORM\Column(name="photo", type="string", nullable=true)
     */
    private $photo;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     * @return int
     */
    public function getLitresHubId()
    {
        return $this->litresHubId;
    }

    /**
     * @param int $litresHubId
     *
     * @return Author
     */
    public function setLitresHubId($litresHubId)
    {
        $this->litresHubId = $litresHubId;

        return $this;
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
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     *
     * @return Author
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return int
     */
    public function getRecensesCount()
    {
        return $this->recensesCount;
    }

    /**
     * @param int $recensesCount
     *
     * @return Author
     */
    public function setRecensesCount($recensesCount)
    {
        $this->recensesCount = $recensesCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getArtsCount()
    {
        return $this->artsCount;
    }

    /**
     * @param int $artsCount
     *
     * @return Author
     */
    public function setArtsCount($artsCount)
    {
        $this->artsCount = $artsCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     *
     * @return Author
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Author
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
    public function __toString()
    {
        return (string) $this->getFullName();
    }
}