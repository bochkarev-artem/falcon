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
        ];
    }

    /**
     * @return string
     */
    public function getMainMenu()
    {
        return $this->menuBuilder->getMainMenu();
    }

    /**
     * @return string
     */
    public function getSideMenu()
    {
        return $this->menuBuilder->getSideMenu();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'menu';
    }
}
