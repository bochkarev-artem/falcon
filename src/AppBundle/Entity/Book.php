<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
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
     * @var $price
     *
     * @ORM\Column(name="price", type="string")
     */
    private $price;

    /**
     * @var $cover
     *
     * @ORM\Column(name="cover", type="string")
     */
    private $cover;

    /**
     * @var $isSale
     *
     * @ORM\Column(name="is_sale", type="boolean")
     */
    private $isSale;

    /**
     * @var $fileIntId
     *
     * @ORM\Column(name="file_int_id", type="integer")
     */
    private $fileIntId;

    /**
     * @var $type
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var $isShowPreview
     *
     * @ORM\Column(name="is_show_preview", type="boolean")
     */
    private $isShowPreview;

    /**
     * @var $isAllowRead
     *
     * @ORM\Column(name="is_allow_read", type="boolean")
     */
    private $isAllowRead;

    /**
     * @var Genre $genre
     *
     * @ORM\ManyToOne(targetEntity="Genre")
     * @ORM\JoinColumn(name="genre_id", referencedColumnName="genre_id")
     */
    private $genre;

    /**
     * @var Author $author
     *
     * @ORM\ManyToOne(targetEntity="Author")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="author_id")
     */
    private $author;

    /**
     * @var Sequence $sequence
     *
     * @ORM\ManyToOne(targetEntity="Sequence")
     * @ORM\JoinColumn(name="sequence_id", referencedColumnName="sequence_id")
     */
    private $sequence;

    /**
     * @var $title
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var $annotation
     *
     * @ORM\Column(name="annotation", type="string")
     */
    private $annotation;

    /**
     * @var $date
     *
     * @ORM\Column(name="date", type="string")
     */
    private $date;

    /**
     * @var $lang
     *
     * @ORM\Column(name="lang", type="string")
     */
    private $lang;

    /**
     * @var $litresIntId
     *
     * @ORM\Column(name="litres_int_id", type="integer")
     */
    private $litresIntId;

    /**
     * @var $litresUrl
     *
     * @ORM\Column(name="litres_url", type="string")
     */
    private $litresUrl;

    /**
     * @var $litresId
     *
     * @ORM\Column(name="litres_id", type="integer")
     */
    private $litresId;

    /**
     * @var $publisher
     *
     * @ORM\Column(name="publisher", type="string")
     */
    private $publisher;

    /**
     * @var $cityPublished
     *
     * @ORM\Column(name="city_published", type="string")
     */
    private $cityPublished;

    /**
     * @var $yearPublished
     *
     * @ORM\Column(name="year_published", type="string")
     */
    private $yearPublished;

    /**
     * @var $isbn
     *
     * @ORM\Column(name="isbn", type="string")
     */
    private $isbn;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     *
     * @return Book
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @param mixed $cover
     *
     * @return Book
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsSale()
    {
        return $this->isSale;
    }

    /**
     * @param mixed $isSale
     *
     * @return Book
     */
    public function setIsSale($isSale)
    {
        $this->isSale = $isSale;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFileIntId()
    {
        return $this->fileIntId;
    }

    /**
     * @param mixed $fileIntId
     *
     * @return Book
     */
    public function setFileIntId($fileIntId)
    {
        $this->fileIntId = $fileIntId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return Book
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsShowPreview()
    {
        return $this->isShowPreview;
    }

    /**
     * @param mixed $isShowPreview
     *
     * @return Book
     */
    public function setIsShowPreview($isShowPreview)
    {
        $this->isShowPreview = $isShowPreview;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsAllowRead()
    {
        return $this->isAllowRead;
    }

    /**
     * @param mixed $isAllowRead
     *
     * @return Book
     */
    public function setIsAllowRead($isAllowRead)
    {
        $this->isAllowRead = $isAllowRead;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $genre
     *
     * @return Book
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     *
     * @return Book
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param mixed $sequence
     *
     * @return Book
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return Book
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * @param mixed $annotation
     *
     * @return Book
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     *
     * @return Book
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     *
     * @return Book
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLitresIntId()
    {
        return $this->litresIntId;
    }

    /**
     * @param mixed $litresIntId
     *
     * @return Book
     */
    public function setLitresIntId($litresIntId)
    {
        $this->litresIntId = $litresIntId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLitresUrl()
    {
        return $this->litresUrl;
    }

    /**
     * @param mixed $litresUrl
     *
     * @return Book
     */
    public function setLitresUrl($litresUrl)
    {
        $this->litresUrl = $litresUrl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLitresId()
    {
        return $this->litresId;
    }

    /**
     * @param mixed $litresId
     *
     * @return Book
     */
    public function setLitresId($litresId)
    {
        $this->litresId = $litresId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @param mixed $publisher
     *
     * @return Book
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCityPublished()
    {
        return $this->cityPublished;
    }

    /**
     * @param mixed $cityPublished
     *
     * @return Book
     */
    public function setCityPublished($cityPublished)
    {
        $this->cityPublished = $cityPublished;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getYearPublished()
    {
        return $this->yearPublished;
    }

    /**
     * @param mixed $yearPublished
     *
     * @return Book
     */
    public function setYearPublished($yearPublished)
    {
        $this->yearPublished = $yearPublished;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param mixed $isbn
     *
     * @return Book
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }
}