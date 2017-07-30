<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Twig;

use AppBundle\Service\LocaleService;

class LocaleExtension extends \Twig_Extension
{
    /**
     * @var LocaleService
     */
    protected $localeService;

    /**
     * @param LocaleService $localeService
     */
    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('locale_field', [$this, 'getLocaleField']),
        ];
    }

    /**
     * @param mixed  $object
     * @param string $propertyPath
     * @param string $locale
     *
     * @return mixed
     */
    public function getLocaleField($object, $propertyPath, $locale = null)
    {
        return $this->localeService->getLocaleField($object, $propertyPath, $locale);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'locale';
    }
}
