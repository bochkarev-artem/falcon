<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Twig;

use AppBundle\Service\AdsManager;
use AppBundle\Service\Sape\SAPE_client;

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

    public function initSape()
    {
        if (!defined('_SAPE_USER')){
            define('_SAPE_USER', '840de354d9079d45da2f27ecfb3445f0');
        }
        $options['charset'] = 'UTF-8';
        $sape = new SAPE_client($options);

        return $sape->return_links();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ad_manager';
    }
}
