<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppBundle\Entity\Sequence
 *
 * @ORM\Entity
 * @ORM\Table(name="sequence")
 */
class Sequence
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="sequence_id", type="integer")
     */
    private $id;

    /**
     * @var integer $litresId
     *
     * @ORM\Column(name="litres_id", type="integer")
     */
    private $litresId;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Sequence
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return Sequence
     */
    public function setLitresId($litresId)
    {
        $this->litresId = $litresId;

        return $this;
    }
}