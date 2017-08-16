<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AppBundle\Entity\Genre
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="genre",
 *     uniqueConstraints={
 *           @ORM\UniqueConstraint(name="genre_ids", columns={"token"})
 *     }
 * )
 */
class Genre implements LocalePageInterface
{
    /**
     * @var int $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="genre_id", type="integer")
     */
    private $id;

    /**
     * @var int $litresId
     *
     * @ORM\Column(name="litres_id", type="integer", nullable=true)
     */
    private $litresId;

    /**
     * @var ArrayCollection $children
     *
     * @ORM\OneToMany(targetEntity="Genre", mappedBy="parent")
     */
    private $children;

    /**
     * @var Genre $parent
     *
     * @ORM\ManyToOne(targetEntity="Genre", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="genre_id")
     */
    private $parent;

    /**
     * @var string $titleEn
     *
     * @ORM\Column(name="title_en", type="string", nullable=true)
     */
    private $titleEn;

    /**
     * @var string $titleRu
     *
     * @ORM\Column(name="title_ru", type="string", nullable=true)
     */
    private $titleRu;

    /**
     * @var string $descriptionEn
     *
     * @ORM\Column(name="description_en", type="text", nullable=true)
     */
    private $descriptionEn;

    /**
     * @var string $descriptionRu
     *
     * @ORM\Column(name="description_ru", type="text", nullable=true)
     */
    private $descriptionRu;

    /**
     * @var string $token
     *
     * @ORM\Column(name="token", type="string", nullable=true)
     */
    private $token;

    /**
     * @var string $slugEn
     *
     * @Gedmo\Slug(fields={"titleEn"}, unique=true)
     * @ORM\Column(name="slug_en", type="string", nullable=true)
     */
    private $slugEn;

    /**
     * @var string $slugRu
     *
     * @Gedmo\Slug(fields={"titleRu"}, unique=true)
     * @ORM\Column(name="slug_ru", type="string", nullable=true)
     */
    private $slugRu;

    /**
     * @var ArrayCollection $books
     *
     * @ORM\ManyToMany(targetEntity="Book", mappedBy="genres", fetch="EXTRA_LAZY")
     */
    private $books;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->books    = new ArrayCollection();
        $this->parent   = null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return Genre
     */
    public function setBooks($books)
    {
        $this->books = $books;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return Genre
     */
    public function setToken($token)
    {
        $this->token = $token;

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
     * @return Genre
     */
    public function setLitresId($litresId)
    {
        $this->litresId = $litresId;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     *
     * @return Genre
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return Genre
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Genre $parent
     *
     * @return Genre
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleEn()
    {
        return $this->titleEn;
    }

    /**
     * @param string $titleEn
     *
     * @return Genre
     */
    public function setTitleEn($titleEn)
    {
        $this->titleEn = $titleEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleRu()
    {
        return $this->titleRu;
    }

    /**
     * @param string $titleRu
     *
     * @return Genre
     */
    public function setTitleRu($titleRu)
    {
        $this->titleRu = $titleRu;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    /**
     * @param string $descriptionEn
     *
     * @return Genre
     */
    public function setDescriptionEn($descriptionEn)
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionRu()
    {
        return $this->descriptionRu;
    }

    /**
     * @param string $descriptionRu
     *
     * @return Genre
     */
    public function setDescriptionRu($descriptionRu)
    {
        $this->descriptionRu = $descriptionRu;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlugEn()
    {
        return $this->slugEn;
    }

    /**
     * @param string $slugEn
     *
     * @return Genre
     */
    public function setSlugEn($slugEn)
    {
        $this->slugEn = $slugEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlugRu()
    {
        return $this->slugRu;
    }

    /**
     * @param string $slugRu
     *
     * @return Genre
     */
    public function setSlugRu($slugRu)
    {
        $this->slugRu = $slugRu;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getTitleRu();
    }

    /**
     * @return int
     */
    public function getGenreId()
    {
        return $this->getId();
    }

    /**
     * @return string
     */
    public function getPathPrefix()
    {
        return 'genre';
    }

    /**
     * @return string
     */
    public function getPathRu()
    {
        return $this->getPathPrefix() . '/' . $this->getSlugRu();
    }

    /**
     * @return string
     */
    public function getPathEn()
    {
        return $this->getPathPrefix() . '/' . $this->getSlugEn();
    }
}
