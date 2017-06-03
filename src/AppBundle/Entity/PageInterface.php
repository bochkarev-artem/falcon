<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

interface PageInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getPathPrefix();

    /**
     * @return string
     */
    public function getPath();
}
