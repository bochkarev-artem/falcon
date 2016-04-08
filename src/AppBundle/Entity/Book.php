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
     * @ORM\Column(name="is_saleale", type="boolean")
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
     */
    public function setPrice($price)
    {
        $this->price = $price;
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
     */
    public function setCover($cover)
    {
        $this->cover = $cover;
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
     */
    public function setIsSale($isSale)
    {
        $this->isSale = $isSale;
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
     */
    public function setFileIntId($fileIntId)
    {
        $this->fileIntId = $fileIntId;
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
     */
    public function setType($type)
    {
        $this->type = $type;
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
     */
    public function setIsShowPreview($isShowPreview)
    {
        $this->isShowPreview = $isShowPreview;
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
     */
    public function setIsAllowRead($isAllowRead)
    {
        $this->isAllowRead = $isAllowRead;
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
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
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
     */
    public function setAuthor($author)
    {
        $this->author = $author;
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
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
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
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;
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
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
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
     */
    public function setLitresIntId($litresIntId)
    {
        $this->litresIntId = $litresIntId;
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
     */
    public function setLitresUrl($litresUrl)
    {
        $this->litresUrl = $litresUrl;
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
     */
    public function setLitresId($litresId)
    {
        $this->litresId = $litresId;
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
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;
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
     */
    public function setCityPublished($cityPublished)
    {
        $this->cityPublished = $cityPublished;
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
     */
    public function setYearPublished($yearPublished)
    {
        $this->yearPublished = $yearPublished;
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
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;
    }
}