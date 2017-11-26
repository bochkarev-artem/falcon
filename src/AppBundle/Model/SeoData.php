<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Model;

class SeoData
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $metaDescription;

    /**
     * @var string
     */
    private $metaKeywords;

    /**
     * @var bool
     */
    private $indexPage;

    /**
     * SeoData constructor.
     */
    public function __construct()
    {
        $this->indexPage = true;
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
     * @return SeoData
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     *
     * @return SeoData
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaKeywords
     *
     * @return SeoData
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIndexPage()
    {
        return $this->indexPage;
    }

    /**
     * @param bool $indexPage
     *
     * @return SeoData
     */
    public function setIndexPage($indexPage)
    {
        $this->indexPage = $indexPage;

        return $this;
    }
}
