<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Service\LocaleService;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var LocaleService
     */
    protected $localeService;

    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $session = $request->getSession();
            $locale = $this->localeService->getLocale();
            if ($session) {
                $request->setLocale($session->get('_locale', $locale));
            } else {
                $request->setLocale($locale);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 15]],
        ];
    }
}
