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
 * AppBundle\Entity\Book
 *
 * @ORM\Entity
 * @ORM\Table(name="book")
 */
class Book implements PageInterface
{
    use TimestampableTrait;

    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="book_id", type="integer")
     */
    private $id;

    /**
     * @var integer $litresHubId
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
     * @var string $coverPath
     *
     * @ORM\Column(name="cover_path", type="string", length=255, nullable=true)
     */
    private $coverPath;

    /**
     * @var boolean $hasTrial
     *
     * @ORM\Column(name="has_trial", type="boolean", nullable=true)
     */
    private $hasTrial;

    /**
     * @var boolean $enabled
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @var boolean $featuredHome
     *
     * @ORM\Column(name="featured_home", type="boolean", nullable=true)
     */
    private $featuredHome;

    /**
     * @var boolean $featuredMenu
     *
     * @ORM\Column(name="featured_menu", type="boolean", nullable=true)
     */
    private $featuredMenu;

    /**
     * @var string $mainAuthorSlug
     *
     * @ORM\Column(name="main_author_slug", type="string", nullable=true)
     */
    private $mainAuthorSlug;

    /**
     * @var Genre[]|ArrayCollection $genres
     *
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="books", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="book_genre",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="genre_id")}
     * )
     */
    private $genres;

    /**
     * @var Author[]|ArrayCollection $authors
     *
     * @ORM\ManyToMany(targetEntity="Author", inversedBy="books", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="book_author",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="author_id")}
     * )
     */
    private $authors;

    /**
     * @var Tag[]|ArrayCollection $tags
     *
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="books", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="book_tag",
     *      joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="book_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="tag_id")}
     * )
     */
    private $tags;

    /**
     * @var Sequence $sequence
     *
     * @ORM\ManyToOne(targetEntity="Sequence", inversedBy="books", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="sequence_id", referencedColumnName="sequence_id")
     */
    private $sequence;

    /**
     * @var BookRating[]|ArrayCollection $ratings
     *
     * @ORM\OneToMany(targetEntity="BookRating", mappedBy="book", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $ratings;

    /**
     * @var BookReview[]|ArrayCollection $reviews
     *
     * @ORM\OneToMany(targetEntity="BookReview", mappedBy="book", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $reviews;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string $slug
     *
     * @Gedmo\Slug(fields={"title"}, unique=true)
     * @ORM\Column(name="slug", type="string", nullable=true)
     */
    private $slug;

    /**
     * @var string $annotation
     *
     * @ORM\Column(name="annotation", type="text", nullable=true)
     */
    private $annotation;

    /**
     * @var \DateTime $date
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string $lang
     *
     * @ORM\Column(name="lang", type="string", nullable=true)
     */
    private $lang;

    /**
     * @var int $sequenceNumber
     *
     * @ORM\Column(name="sequence_number", type="integer", nullable=true)
     */
    private $sequenceNumber;

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
     * Initialize fields
     */
    public function __construct()
    {
        $this->authors      = new ArrayCollection();
        $this->genres       = new ArrayCollection();
        $this->tags         = new ArrayCollection();
        $this->reviews      = new ArrayCollection();
        $this->ratings      = new ArrayCollection();
        $this->date         = null;
        $this->enabled      = false;
        $this->featuredHome = false;
        $this->featuredMenu = false;
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
     * @return ArrayCollection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @param ArrayCollection $genres
     *
     * @return Book
     */
    public function setGenres(ArrayCollection $genres)
    {
        $this->genres = $genres;

        return $this;
    }

    /**
     * @param ArrayCollection $authors
     *
     * @return Book
     */
    public function setAuthors(ArrayCollection $authors)
    {
        $this->authors = $authors;

        return $this;
    }

    /**
     * @param ArrayCollection $tags
     *
     * @return Book
     */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;

        return $this;
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
     * @return ArrayCollection
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
    public function addAuthor(Author $author)
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        };

        return $this;
    }

    /**
     * @param Author $author
     *
     * @return Book
     */
    public function removeAuthor(Author $author)
    {
        if ($this->authors->contains($author)) {
            $this->authors->removeElement($author);
        };

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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
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
     * @return integer
     */
    public function getLitresHubId()
    {
        return $this->litresHubId;
    }

    /**
     * @param integer $litresHubId
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
     * @return ArrayCollection
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
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return Book
     */
    public function removeTag($tag)
    {
        if ($this->tags->contains($tag)) {
            $this->tags->remove($tag);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCoverPath()
    {
        return $this->coverPath;
    }

    /**
     * @param string $coverPath
     *
     * @return Book
     */
    public function setCoverPath($coverPath)
    {
        $this->coverPath = $coverPath;

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
     * @return Book
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @param string $sequenceNumber
     *
     * @return Book
     */
    public function setSequenceNumber($sequenceNumber)
    {
        $this->sequenceNumber = $sequenceNumber;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFeaturedHome()
    {
        return $this->featuredHome;
    }

    /**
     * @param bool $featuredHome
     *
     * @return Book
     */
    public function setFeaturedHome($featuredHome)
    {
        $this->featuredHome = $featuredHome;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFeaturedMenu()
    {
        return $this->featuredMenu;
    }

    /**
     * @param bool $featuredMenu
     *
     * @return Book
     */
    public function setFeaturedMenu($featuredMenu)
    {
        $this->featuredMenu = $featuredMenu;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getTitle();
    }

    /**
     * @return int
     */
    public function getBookId()
    {
        return $this->getId();
    }

    /**
     * @return ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param ArrayCollection $ratings
     *
     * @return Book
     */
    public function setRatings($ratings)
    {
        $this->ratings = $ratings;

        return $this;
    }

    /**
     * @param BookRating $rating
     *
     * @return Book
     */
    public function addRating($rating)
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
        }

        return $this;
    }

    /**
     * @param BookRating $rating
     *
     * @return Book
     */
    public function removeRating($rating)
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->remove($rating);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param ArrayCollection $reviews
     *
     * @return Book
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;

        return $this;
    }

    /**
     * @param BookReview $review
     *
     * @return Book
     */
    public function addReview($review)
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
        }

        return $this;
    }

    /**
     * @param BookReview $review
     *
     * @return Book
     */
    public function removeReview($review)
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->remove($review);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPathPrefix()
    {
        return 'book';
    }

    /**
     * @return string
     */
    public function getMainAuthorSlug()
    {
        return $this->mainAuthorSlug;
    }

    /**
     * @param string $mainAuthorSlug
     *
     * @return Book
     */
    public function setMainAuthorSlug($mainAuthorSlug)
    {
        $this->mainAuthorSlug = $mainAuthorSlug;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->getMainAuthorSlug() . '/' . $this->getSlug();
    }
}
