<?php

namespace AppBundle\Service;

use AppBundle\Provider\BookProvider;
use AppBundle\Provider\RouteProvider;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Uecode\Bundle\QPushBundle\Event\MessageEvent;

class BookConsumer
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var BookProvider
     */
    private $bookProvider;

    /**
     * @var RouteProvider
     */
    private $routeProvider;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EntityManagerInterface   $em
     * @param BookProvider             $bookProvider
     * @param RouteProvider            $routeProvider
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface   $em,
        BookProvider             $bookProvider,
        RouteProvider            $routeProvider,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em              = $em;
        $this->bookProvider    = $bookProvider;
        $this->routeProvider   = $routeProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param MessageEvent $event
     *
     * @throws DBALException
     */
    public function onMessageReceived(MessageEvent $event)
    {
        $message = $event->getMessage();
        $body = $message->getBody();
        $this->em->clear();

        if (isset($body['command'])) {
            try {
                switch ($body['command']) {
                    case 'updateBook':
                        $this->bookProvider->updateBook($body['bookId']);
                        $this->routeProvider->updateBook($body['bookId']);
                        break;

                    case 'updateAllBooks':
                        $this->bookProvider->updateAllBooks();
                        break;

                    case 'resetAdCache':
                        $this->eventDispatcher->dispatch('reset_ads_cache');
                        break;

                    default:
                }
            } catch (DBALException $e) {
                throw $e; // TODO handle exception
            }
        }

        if (isset($body['reset_menu_cache']) && $body['reset_menu_cache']) {
            $this->eventDispatcher->dispatch('reset_menu_cache');
        }
    }
}
