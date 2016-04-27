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
 * @ORM\Table(name="book")
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
     * @ORM\Column(name="price", type="string")
     */
    private $price;

    /**
     * @var string $cover
     *
     * @ORM\Column(name="cover", type="string")
     */
    private $cover;

    /**
     * @var string $coverPreview
     *
     * @ORM\Column(name="cover_preview", type="string")
     */
    private $coverPreview;

    /**
     * @var string $filename
     *
     * @ORM\Column(name="filename", type="integer")
     */
    private $filename;

    /**
     * @var integer $type
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var boolean $hasTrial
     *
     * @ORM\Column(name="has_trial", type="boolean")
     */
    private $hasTrial;

    /**
     * @var string $reader
     *
     * @ORM\Column(name="reader", type="string")
     */
    private $reader;

    /**
     * @var ArrayCollection $genre
     *
     * @ORM\ManyToMany(targetEntity="Genre")
     * @ORM\JoinTable(name="book_genres",
     *      joinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="genre_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")}
     * )
     */
    private $genre;

    /**
     * @var ArrayCollection $author
     *
     * @ORM\ManyToMany(targetEntity="Author")
     * @ORM\JoinTable(name="book_authors",
     *      joinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="author_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")}
     * )
     */
    private $author;

    /**
     * @var ArrayCollection $tag
     *
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="book_authors",
     *      joinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="tag_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")}
     * )
     */
    private $tag;

    /**
     * @var Sequence $sequence
     *
     * @ORM\ManyToOne(targetEntity="Sequence")
     * @ORM\JoinColumn(name="sequence_id", referencedColumnName="sequence_id")
     */
    private $sequence;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string $annotation
     *
     * @ORM\Column(name="annotation", type="text")
     */
    private $annotation;

    /**
     * @var string $date
     *
     * @ORM\Column(name="date", type="string")
     */
    private $date;

    /**
     * @var string $lang
     *
     * @ORM\Column(name="lang", type="string")
     */
    private $lang;

    /**
     * @var string $documentUrl
     *
     * @ORM\Column(name="document_url", type="string")
     */
    private $documentUrl;

    /**
     * @var string $documentId
     *
     * @ORM\Column(name="document_id", type="integer")
     */
    private $documentId;

    /**
     * @var string $publisher
     *
     * @ORM\Column(name="publisher", type="string")
     */
    private $publisher;

    /**
     * @var string $cityPublished
     *
     * @ORM\Column(name="city_published", type="string")
     */
    private $cityPublished;

    /**
     * @var string $yearPublished
     *
     * @ORM\Column(name="year_published", type="string", length=4)
     */
    private $yearPublished;

    /**
     * @var string $isbn
     *
     * @ORM\Column(name="isbn", type="string")
     */
    private $isbn;

    /**
     * @var float $rating
     *
     * @ORM\Column(name="rating", type="float")
     */
    private $rating;

    /**
     * @var integer $recensesCount
     *
     * @ORM\Column(name="recenses_count", type="integer")
     */
    private $recensesCount;

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
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param ArrayCollection $genre
     *
     * @return Book
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param ArrayCollection $author
     *
     * @return Book
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Sequence
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param Sequence $sequence
     *
     * @return Book
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

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
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param ArrayCollection $tag
     *
     * @return Book
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

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

}