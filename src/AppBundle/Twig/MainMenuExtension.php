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
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'main_menu';
    }
}
