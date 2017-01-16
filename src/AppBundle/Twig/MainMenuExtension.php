<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Twig;

use AppBundle\Service\MenuBuilder;

class MainMenuExtension extends \Twig_Extension
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
            'main_menu' => new \Twig_Function_Method($this, 'getMainMenu'),
            'side_menu' => new \Twig_Function_Method($this, 'getSideMenu'),
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
        return 'main_menu';
    }
}
