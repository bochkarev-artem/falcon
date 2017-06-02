<?php
/**
 * @author Artem Bochkarev
 */
namespace AppBundle\Model\Pagerfanta;

use Pagerfanta\View\Template\Template;
use Symfony\Component\Translation\Translator;

class PaginationTemplate extends Template
{
    /**
     * @var Translator
     */
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;

        parent::__construct();
    }

    static protected $defaultOptions = [
        'dots_message'        => '&hellip;',
        'css_container_class' => 'pagination',
        'css_prev_class'      => 'fa fa-angle-left',
        'css_next_class'      => 'fa fa-angle-right',
        'css_active_class'    => 'active pagination-link',
        'css_link_class'      => 'pagination-link',
        'rel_previous'        => 'prev',
        'rel_next'            => 'next'
    ];

    /**
     * @return string
     */
    public function container()
    {
        return sprintf('<ul class="%s">%%pages%%</ul>',
            $this->option('css_container_class')
        );
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function page($page)
    {
        $text = $page;

        return $this->pageWithText($page, $text);
    }

    /**
     * @param int $page
     * @param string $text
     *
     * @return string
     */
    public function pageWithText($page, $text)
    {
        return $this->pageWithTextAndClass($page, $text);
    }

    /**
     * @param string $page
     * @param string $text
     * @param string $class
     * @param string $rel
     *
     * @return string
     */
    private function pageWithTextAndClass($page, $text, $class = null, $rel = null)
    {
        $href = $this->generateRoute($page);

        return $this->linkLi($class, $href, $text, $rel);
    }

    /**
     * @return string
     */
    public function previousDisabled()
    {
        return '<li></li>';
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function previousEnabled($page)
    {
        $text   = $this->translator->trans('front.pagination.previous');
        $class  = $this->option('css_prev_class');
        $rel    = $this->option('rel_previous');
        $href   = $this->generateRoute($page);
        $aClass = sprintf(' class="%s"', $this->option('css_link_class'));
        $rel    = $rel ? sprintf(' rel="%s"', $rel) : '';

        return sprintf('<li><a href="%s" rel="%s" data-page="%s" class="%s" aria-label="%s"><span aria-hidden="true"><i class="%s"></i></span><small class="page-label">%s</small></a></li>', $href, $rel, $page, $aClass, $text, $class, $text);
    }

    /**
     * @return string
     */
    public function nextDisabled()
    {
        return '<li></li>';
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function nextEnabled($page)
    {
        $text   = $this->translator->trans('front.pagination.next');
        $class  = $this->option('css_next_class');
        $rel    = $this->option('rel_next');
        $href   = $this->generateRoute($page);
        $aClass = sprintf(' class="%s"', $this->option('css_link_class'));
        $rel    = $rel ? sprintf(' rel="%s"', $rel) : '';

        return sprintf('<li><a href="%s" rel="%s" data-page="%s" class="%s" aria-label="%s"><span aria-hidden="true"><i class="%s"></i></span><small class="page-label">%s</small></a></li>', $href, $rel, $page, $aClass, $text, $class, $text);
    }

    /**
     * @return string
     */
    public function first()
    {
        return $this->page(1);
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function last($page)
    {
        return $this->page($page);
    }

    /**
     * @param int $page
     *
     * @return string
     */
    public function current($page)
    {
        $href   = $this->generateRoute($page);
        $aClass = $this->option('css_active_class');

        return $this->linkLi($aClass, $href, $page);
    }

    /**
     * @return string
     */
    public function separator()
    {
        $text = $this->option('dots_message');

        return $this->spanLi($text);
    }

    /**
     * @param string $aClass
     * @param string $href
     * @param string $text
     * @param string $rel
     *
     * @return string
     */
    protected function linkLi($aClass, $href, $text, $rel = null)
    {
        $aClass = $aClass ? sprintf(' class="%s"', $this->option('css_active_class')) : sprintf(' class="%s"', $this->option('css_link_class'));
        $rel    = $rel ? sprintf(' rel="%s"', $rel) : '';

        return sprintf('<li><a data-page="%s" href="%s"%s%s>%s</a></li>', $text, $href, $aClass, $rel, $text);
    }

    /**
     * @param string $text
     *
     * @return string
     */
    protected function spanLi($text)
    {
        return sprintf('<li><span>%s</span></li>', $text);
    }
}
