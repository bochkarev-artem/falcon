<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Twig;

use AppBundle\Service\MenuBuilder;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var MenuBuilder
     */
    protected $menuBuilder;

    /**
     * @param MenuBuilder $menuBuilder
     */
    public function __construct(MenuBuilder $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('main_menu', [$this, 'getMainMenu']),
            new \Twig_SimpleFunction('side_menu', [$this, 'getSideMenu']),
            new \Twig_SimpleFunction('mobile_menu', [$this, 'getMobileMenu']),
        ];
    }

    /**
     * @return string
     */
    public function getMainMenu()
    {
        return $this->menuBuilder->getMenu('main');
    }

    /**
     * @return string
     */
    public function getMobileMenu()
    {
        return $this->menuBuilder->getMenu('mobile');
    }

    /**
     * @return string
     */
    public function getSideMenu()
    {
        return $this->menuBuilder->getMenu('side');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'menu';
    }
}
