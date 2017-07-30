<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use Elastica\Result;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class LocaleService
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @var array
     */
    protected $hosts;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @param RequestStack $requestStack
     * @param array        $hosts
     * @param array        $locales
     * @param string       $defaultLocale
     */
    public function __construct(RequestStack $requestStack, $hosts, $locales, $defaultLocale)
    {
        $this->requestStack     = $requestStack;
        $this->hosts            = $hosts;
        $this->locales          = $locales;
        $this->propertyAccessor = new PropertyAccessor();
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        $request = $this->requestStack->getMasterRequest();
        $host = $request ? $request->getHost() : false;
        $this->locale = $host ? array_search($host, $this->hosts) : $this->getDefaultLocale();

        return $this->locale;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        $request = $this->requestStack->getMasterRequest();
        $host = $request ? $request->getHost() : false;

        return $host;
    }

    /**
     * @param mixed       $object
     * @param string      $propertyPath
     * @param string|null $locale
     *
     * @return mixed
     */
    public function getLocaleField($object, $propertyPath, $locale = null)
    {
        $value = '';
        $locale = $locale ?? $this->getLocale();
        if ($object instanceof Result) {
            $object = $object->getSource();
        }
        $propertyPath = is_array($object) ? '['.$propertyPath.'_'.$locale.']' : $propertyPath.ucfirst($locale);
        if ($this->propertyAccessor->isReadable($object, $propertyPath)) {
            $value = $this->propertyAccessor->getValue($object, $propertyPath);
        }

        return $value;
    }

    /**
     * @param mixed  $object
     * @param string $propertyPath
     * @param string $locale
     * @param mixed  $value
     */
    public function setLocaleField($object, $propertyPath, $locale, $value)
    {
        $propertyPath = is_array($object) ? '['.$propertyPath.'_'.$locale.']' : $propertyPath.ucfirst($locale);
        if ($this->propertyAccessor->isWritable($object, $propertyPath)) {
            $this->propertyAccessor->setValue($object, $propertyPath, $value);
        }
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }
}
