<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Sequence;
use AppBundle\Entity\Tag;
use AppBundle\Provider\BookProvider;
use AppBundle\Provider\RouteProvider;
use Doctrine\ORM\Event;
use Doctrine\ORM\UnitOfWork;
use Uecode\Bundle\QPushBundle\Provider\AwsProvider;

class BookIndexListener
{
    /**
     * @var AwsProvider
     */
    protected $awsProducer;

    /**
     * @var array
     */
    protected $bookUpdateQueue = [];

    /**
     * @var array
     */
    protected $authorUpdateQueue = [];

    /**
     * @var array
     */
    protected $tagUpdateQueue = [];

    /**
     * @var array
     */
    protected $sequenceUpdateQueue = [];

    /**
     * @var array
     */
    protected $genreUpdateQueue = [];

    /**
     * @var UnitOfWork
     */
    protected $uow;

    /**
     * @var boolean
     */
    protected $resetMenuCache = false;

    /**
     * @var boolean
     */
    protected $isMessageQueueOn;

    protected $bookProvider;
    protected $routeProvider;

    public function __construct(
        AwsProvider $awsProducer,
        BookProvider $bookProvider,
        RouteProvider $routeProvider,
        $messageQueueOn
    ) {
        $this->awsProducer = $awsProducer;
        $this->isMessageQueueOn = $messageQueueOn;
        $this->bookProvider = $bookProvider;
        $this->routeProvider = $routeProvider;
    }

    /**
     * @param Event\OnFlushEventArgs $eventArgs
     */
    public function onFlush(Event\OnFlushEventArgs $eventArgs)
    {
        if ($this->isMessageQueueOn) {
            $em        = $eventArgs->getEntityManager();
            $this->uow = $em->getUnitOfWork();

            $this->processEntityChanges($this->uow->getScheduledEntityUpdates(), true);
            $this->processEntityChanges($this->uow->getScheduledEntityDeletions());
        }
    }

    public function postFlush()
    {
        if ($this->isMessageQueueOn) {
            $this->processIndexUpdateQueue();
        }
    }

    /**
     * @param array $entities
     * @param bool  $isUpdate
     */
    protected function processEntityChanges(array $entities, $isUpdate = false)
    {
        foreach ($entities as $entity) {
            if ($entity instanceof Book) {
                $changeSet = $this->uow->getEntityChangeSet($entity);

                if ($isUpdate) {
                    $this->processMenuAffectingChanges($entity, $changeSet);
                }

                if (isset($changeSet['enabled'])) {
                    $this->bookProvider->updateBook($entity->getId());
                    $this->routeProvider->updateBook($entity->getId());
                } else {
                    $this->scheduleBookIndexUpdate($entity);
                }
            }

            if ($entity instanceof Author) {
                $this->scheduleAuthorIndexUpdate($entity);
            }

            if ($entity instanceof Genre) {
                $this->scheduleGenreIndexUpdate($entity);
            }

            if ($entity instanceof Sequence) {
                $this->scheduleSequenceIndexUpdate($entity);
            }

            if ($entity instanceof Tag) {
                $this->scheduleTagIndexUpdate($entity);
            }
        }
    }

    protected function processMenuAffectingChanges(Book $book, array $changeSet)
    {
        if ($this->resetMenuCache) {
            return;
        }

        if ($book->isFeaturedMenu()) {
            $this->resetMenuCache = true;

            return;
        }

        if (isset($changeSet['featuredMenu']) && $changeSet['featuredMenu'][1] == true) {
            $this->resetMenuCache = true;

            return;
        }

        if (
            isset($changeSet['title']) && isset($changeSet['featuredMenu']) &&
            $changeSet['featuredMenu'][1] != $changeSet['featuredMenu'][0]
        ) {
            $this->resetMenuCache |= $changeSet['title'][0] != $changeSet['title'][1];
        }
    }

    /**
     * @param Book $book
     */
    protected function scheduleBookIndexUpdate(Book $book)
    {
        array_push($this->bookUpdateQueue, $book->getId());
    }

    /**
     * @param Author $author
     */
    protected function scheduleAuthorIndexUpdate(Author $author)
    {
        array_push($this->authorUpdateQueue, $author->getId());
    }

    /**
     * @param Genre $genre
     */
    protected function scheduleGenreIndexUpdate(Genre $genre)
    {
        array_push($this->genreUpdateQueue, $genre->getId());
        $this->resetMenuCache = true;
    }

    /**
     * @param Sequence $sequence
     */
    protected function scheduleSequenceIndexUpdate(Sequence $sequence)
    {
        array_push($this->sequenceUpdateQueue, $sequence->getId());
    }

    /**
     * @param Tag $tag
     */
    protected function scheduleTagIndexUpdate(Tag $tag)
    {
        array_push($this->tagUpdateQueue, $tag->getId());
    }

    protected function processIndexUpdateQueue()
    {
        $messagesToSend = [];

        $this->bookUpdateQueue     = array_unique($this->bookUpdateQueue);
        $this->authorUpdateQueue   = array_unique($this->authorUpdateQueue);
        $this->genreUpdateQueue    = array_unique($this->genreUpdateQueue);
        $this->sequenceUpdateQueue = array_unique($this->sequenceUpdateQueue);
        $this->tagUpdateQueue      = array_unique($this->tagUpdateQueue);

        foreach ($this->bookUpdateQueue as $bookId) {
            array_push($messagesToSend, [
                'command' => 'updateBook',
                'bookId'  => $bookId,
            ]);
        }

        foreach ($this->authorUpdateQueue as $authorId) {
            array_push($messagesToSend, [
                'command'  => 'updateAuthor',
                'authorId' => $authorId,
            ]);
        }

        foreach ($this->genreUpdateQueue as $genreId) {
            array_push($messagesToSend, [
                'command' => 'updateGenre',
                'genreId' => $genreId,
            ]);
        }

        foreach ($this->sequenceUpdateQueue as $sequenceId) {
            array_push($messagesToSend, [
                'command'    => 'updateSequence',
                'sequenceId' => $sequenceId,
            ]);
        }

        foreach ($this->tagUpdateQueue as $tagId) {
            array_push($messagesToSend, [
                'command' => 'updateTag',
                'tagId'   => $tagId,
            ]);
        }

        if ($messagesToSend && $this->resetMenuCache) {
            $lastIndex = count($messagesToSend) - 1;
            $messagesToSend[$lastIndex]['reset_menu_cache'] = true;
        }

        foreach ($messagesToSend as $message) {
            $this->awsProducer->publish($message);
        }

        $this->bookUpdateQueue     = [];
        $this->authorUpdateQueue   = [];
        $this->genreUpdateQueue    = [];
        $this->sequenceUpdateQueue = [];
        $this->tagUpdateQueue      = [];

        $this->resetMenuCache = false;
    }
}
