<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

/**
 * Interface EntityInterface
 * @package AppBundle\Entity
 */
interface EntityInterface
{
    /**
     * @return string
     */
    public function getEntityPathPrefix();

    /**
     * @return string
     */
    public function getPath();
}