<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AppBundle\Entity\Tag
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="tag",
 *     uniqueConstraints={
 *           @ORM\UniqueConstraint(name="tag_ids", columns={"litres_id"})
 *     }
 * )
 */
class Tag implements EntityInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="tag_id", type="integer")
     */
    private $id;

    /**
     * @var integer $litresId
     *
     * @ORM\Column(name="litres_id", type="integer")
     */
    private $litresId;

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
     * @return integer
     */
    public function getLitresId()
    {
        return $this->litresId;
    }

    /**
     * @param integer $litresId
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
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getTitle();
    }

    /**
     * @return int
     */
    public function getTagId()
    {
        return $this->getId();
    }

    /**
     * @return string
     */
    public function getEntityPathPrefix()
    {
        return 'tag';
    }
}