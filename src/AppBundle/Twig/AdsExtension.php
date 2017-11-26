<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Twig;

use AppBundle\Service\AdsManager;

class AdsExtension extends \Twig_Extension
{
    /**
     * @var AdsManager
     */
    protected $adsManager;

    /**
     * @param AdsManager $adsManager
     */
    public function __construct(AdsManager $adsManager)
    {
        $this->adsManager = $adsManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('ad_by_position', [$this, 'getAdByPosition']),
        ];
    }

    /**
     * @param int $position
     * @param bool $showToAll
     *
     * @return mixed
     */
    public function getAdByPosition($position, $showToAll = false)
    {
        return $this->adsManager->getAdByPosition($position, $showToAll);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ad_manager';
    }
}
