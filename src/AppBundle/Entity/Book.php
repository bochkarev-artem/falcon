<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AppBundle\Entity\Book
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="book",
 *     uniqueConstraints={
 *           @ORM\UniqueConstraint(name="book_ids", columns={"litres_hub_id"})
 *     }
 * )
 */
class Book
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="book_id", type="integer")
     */
    private $id;

    /**
     * @var $litresHubId
     *
     * @ORM\Column(name="litres_hub_id", type="integer")
     */
    private $litresHubId;

    /**
     * @var string $price
     *
     * @ORM\Column(name="price", type="string", nullable=true)
     */
    private $price;

    /**
     * @var string $cover
     *
     * @ORM\Column(name="cover", type="string", nullable=true)
     */
    private $cover;

    /**
     * @var string $coverPreview
     *
     * @ORM\Column(name="cover_preview", type="string", nullable=true)
     */
    private $coverPreview;

    /**
     * @var string $filename
     *
     * @ORM\Column(name="filename", type="string", nullable=true)
     */
    private $filename;

    /**
     * @var integer $type
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var boolean $hasTrial
     *
     * @ORM\Column(name="has_trial", type="boolean", nullable=true)
     */
    private $hasTrial;

    /**
     * @var string $reader
     *
     * @ORM\Column(name="reader", type="string", nullable=true)
     */
    private $reader;

    /**
     * @var ArrayCollection $genres
     *
     * @ORM\ManyToMany(targetEntity="Genre")
     * @ORM\JoinTable(name="book_genre",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="genre_id")}
     * )
     */
    private $genres;

    /**
     * @var ArrayCollection $authors
     *
     * @ORM\ManyToMany(targetEntity="Author")
     * @ORM\JoinTable(name="book_author",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="author_id")}
     * )
     */
    private $authors;

    /**
     * @var ArrayCollection $tags
     *
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="book_tag",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="tag_id")}
     * )
     */
    private $tags;

    /**
     * @var ArrayCollection $sequences
     *
     * @ORM\ManyToMany(targetEntity="Sequence")
     * @ORM\JoinTable(name="book_sequence",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sequence_id", referencedColumnName="sequence_id")}
     * )
     */
    private $sequences;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string $annotation
     *
     * @ORM\Column(name="annotation", type="text", nullable=true)
     */
    private $annotation;

    /**
     * @var string $date
     *
     * @ORM\Column(name="date", type="string", nullable=true)
     */
    private $date;

    /**
     * @var string $lang
     *
     * @ORM\Column(name="lang", type="string", nullable=true)
     */
    private $lang;

    /**
     * @var string $documentUrl
     *
     * @ORM\Column(name="document_url", type="string", nullable=true)
     */
    private $documentUrl;

    /**
     * @var string $documentId
     *
     * @ORM\Column(name="document_id", type="string", nullable=true)
     */
    private $documentId;

    /**
     * @var string $publisher
     *
     * @ORM\Column(name="publisher", type="string", nullable=true)
     */
    private $publisher;

    /**
     * @var string $cityPublished
     *
     * @ORM\Column(name="city_published", type="string", nullable=true)
     */
    private $cityPublished;

    /**
     * @var string $yearPublished
     *
     * @ORM\Column(name="year_published", type="string", length=4, nullable=true)
     */
    private $yearPublished;

    /**
     * @var string $isbn
     *
     * @ORM\Column(name="isbn", type="string", nullable=true)
     */
    private $isbn;

    /**
     * @var float $rating
     *
     * @ORM\Column(name="rating", type="float", nullable=true)
     */
    private $rating;

    /**
     * @var integer $recensesCount
     *
     * @ORM\Column(name="recenses_count", type="integer", nullable=true)
     */
    private $recensesCount;

    /**
     * Initialize fields
     */
    public function __construct()
    {
        $this->authors   = new ArrayCollection();
        $this->genres    = new ArrayCollection();
        $this->sequences = new ArrayCollection();
        $this->tags      = new ArrayCollection();
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
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     *
     * @return Book
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @param string $cover
     *
     * @return Book
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param integer $type
     *
     * @return Book
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Genre
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @param Genre $genre
     *
     * @return Book
     */
    public function addGenre($genre)
    {
        $this->genres->add($genre);

        return $this;
    }

    /**
     * @return Author
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param Author $author
     *
     * @return Book
     */
    public function addAuthor($author)
    {
        $this->authors->add($author);

        return $this;
    }

    /**
     * @return Sequence
     */
    public function getSequences()
    {
        return $this->sequences;
    }

    /**
     * @param Sequence $sequence
     *
     * @return Book
     */
    public function addSequence($sequence)
    {
        $this->sequences->add($sequence);

        return $this;
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
     * @return Book
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * @param string $annotation
     *
     * @return Book
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;

        return $this;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     *
     * @return Book
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
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
     * @return Book
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @param string $publisher
     *
     * @return Book
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return string
     */
    public function getCityPublished()
    {
        return $this->cityPublished;
    }

    /**
     * @param string $cityPublished
     *
     * @return Book
     */
    public function setCityPublished($cityPublished)
    {
        $this->cityPublished = $cityPublished;

        return $this;
    }

    /**
     * @return string
     */
    public function getYearPublished()
    {
        return $this->yearPublished;
    }

    /**
     * @param string $yearPublished
     *
     * @return Book
     */
    public function setYearPublished($yearPublished)
    {
        $this->yearPublished = $yearPublished;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     *
     * @return Book
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return string
     */
    public function getLitresHubId()
    {
        return $this->litresHubId;
    }

    /**
     * @param string $litresHubId
     *
     * @return Book
     */
    public function setLitresHubId($litresHubId)
    {
        $this->litresHubId = $litresHubId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCoverPreview()
    {
        return $this->coverPreview;
    }

    /**
     * @param string $coverPreview
     *
     * @return Book
     */
    public function setCoverPreview($coverPreview)
    {
        $this->coverPreview = $coverPreview;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return Book
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentUrl()
    {
        return $this->documentUrl;
    }

    /**
     * @param string $documentUrl
     *
     * @return Book
     */
    public function setDocumentUrl($documentUrl)
    {
        $this->documentUrl = $documentUrl;

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
     * @return Book
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;

        return $this;
    }

    /**
     * @return string
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @param string $reader
     *
     * @return Book
     */
    public function setReader($reader)
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isHasTrial()
    {
        return $this->hasTrial;
    }

    /**
     * @param boolean $hasTrial
     *
     * @return Book
     */
    public function setHasTrial($hasTrial)
    {
        $this->hasTrial = $hasTrial;

        return $this;
    }

    /**
     * @return Tag
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     *
     * @return Book
     */
    public function addTag($tag)
    {
        $this->tags->add($tag);

        return $this;
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
     * @return Book
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

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
     * @return Book
     */
    public function setRecensesCount($recensesCount)
    {
        $this->recensesCount = $recensesCount;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getLitresHubId();
    }
}