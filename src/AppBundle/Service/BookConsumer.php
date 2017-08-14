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
        if (!isset($data['command'])) {
            return;
        }

        $this->em->clear();

        try {
            switch ($body['command']) {
                case 'updateBook':
                    if (!isset($data['productId'])) {
                        break;
                    }
                    $this->bookProvider->updateBook($body['productId']);
                    $this->routeProvider->updateBook($body['productId']);
                    break;

                case 'updateAllBooks':
                    $this->bookProvider->updateAllBooks();
                    break;
//
//                case 'updateAuthor':
//                    $this->bookProvider->updateBrand($body['brandId']);
//                    $this->routeProvider->updateBrand($body['brandId']);
//                    break;
//
//                case 'updateGenre':
//                    $this->bookProvider->updateCategory($body['categoryId']);
//                    $this->routeProvider->updateCategory($body['categoryId'], $body['recursive']);
//                    break;
//
//                case 'updateTag':
//                    $this->bookProvider->updateCategory($body['categoryId']);
//                    $this->routeProvider->updateCategory($body['categoryId'], $body['recursive']);
//                    break;
//
//                case 'updateSequence':
//                    $this->bookProvider->updateCategory($body['categoryId']);
//                    $this->routeProvider->updateCategory($body['categoryId'], $body['recursive']);
//                    break;

                default:
                    //nothing
            }
        } catch (DBALException $e) {
            $msg = $e->getMessage();
            $isTimeoutException = strpos($msg, '2006') !== false || strpos($msg, '2013') !== false;

            if ($isTimeoutException) {
                exit;
            } else {
                throw $e;
            }
        }

        if (isset($body['reset_menu_cache']) && $body['reset_menu_cache']) {
            $this->eventDispatcher->dispatch('reset_menu_cache');
        }
    }
}
