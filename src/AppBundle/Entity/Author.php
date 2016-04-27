<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="author")
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
     * @ORM\Column(name="litres_hub_id", type="integer")
     */
    private $litresHubId;

    /**
     * @var integer $documentId
     *
     * @ORM\Column(name="document_id", type="integer")
     */
    private $documentId;

    /**
     * @var string $firstName
     *
     * @ORM\Column(name="first_name", type="string")
     */
    private $firstName;

    /**
     * @var string $lastName
     *
     * @ORM\Column(name="last_name", type="string")
     */
    private $lastName;

    /**
     * @var string $middleName
     *
     * @ORM\Column(name="middle_name", type="string")
     */
    private $middleName;

    /**
     * @var integer $level
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * @var integer $recensesCount
     *
     * @ORM\Column(name="recenses_count", type="integer")
     */
    private $recensesCount;

    /**
     * @var integer $artsCount
     *
     * @ORM\Column(name="arts_count", type="integer")
     */
    private $artsCount;

    /**
     * @var string $photo
     *
     * @ORM\Column(name="photo", type="string")
     */
    private $photo;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

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
     * @return int
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @param int $documentId
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
}