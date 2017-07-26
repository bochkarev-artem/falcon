<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Entity;

interface LocalePageInterface
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
    public function getPathEn();

    /**
     * @return string
     */
    public function getPathRu();
}
