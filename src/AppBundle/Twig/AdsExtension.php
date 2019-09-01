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
            new \Twig_SimpleFunction('init_sape', [$this, 'initSape']),
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

    public function initSape(int $number)
    {
        if (!defined('_SAPE_USER')){
            define('_SAPE_USER', '840de354d9079d45da2f27ecfb3445f0');
        }

        $sape = new \SAPE_client();

        return $sape->return_links($number);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ad_manager';
    }
}
