<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Sequence;
use AppBundle\Entity\Tag;
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

    /**
     * @param AwsProvider $awsProducer
     * @param boolean     $messageQueueOn
     */
    public function __construct(AwsProvider $awsProducer, $messageQueueOn)
    {
        $this->awsProducer = $awsProducer;
        $this->isMessageQueueOn = $messageQueueOn;
    }

    /**
     * @param Event\OnFlushEventArgs $eventArgs
     */
    public function onFlush(Event\OnFlushEventArgs $eventArgs)
    {
        if ($this->isMessageQueueOn) {
            $em        = $eventArgs->getEntityManager();
            $this->uow = $em->getUnitOfWork();

            $this->processEntityChanges($this->uow->getScheduledEntityInsertions());
            $this->processEntityChanges($this->uow->getScheduledEntityUpdates(), true);
            $this->processEntityChanges($this->uow->getScheduledEntityDeletions());
        }
    }

    /**
     * @param Event\PostFlushEventArgs $eventArgs
     */
    public function postFlush(Event\PostFlushEventArgs $eventArgs)
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
            $this->processBook($entity, $isUpdate);

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

    /**
     * @param object $entity
     * @param int    $isUpdate
     */
    protected function processBook($entity, $isUpdate)
    {
        if ($entity instanceof Book) {
            if ($isUpdate) {
                $this->processMenuAffectingChanges($entity);
            }

            $this->scheduleBookIndexUpdate($entity);
        }
    }

    /**
     * @param Book $book
     */
    protected function processMenuAffectingChanges(Book $book)
    {
        if ($this->resetMenuCache) {
            return;
        }

        if ($book->isFeaturedMenu()) {
            $this->resetMenuCache = true;

            return;
        }

        $changeSet = $this->uow->getEntityChangeSet($book);
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
        $this->bookUpdateQueue[$book->getId()] = $book->getId();
    }

    /**
     * @param Author $author
     */
    protected function scheduleAuthorIndexUpdate(Author $author)
    {
        foreach ($author->getBooks() as $book) {
            $this->bookUpdateQueue[$book->getId()] = $book->getId();
        }
    }

    /**
     * @param Genre $genre
     */
    protected function scheduleGenreIndexUpdate(Genre $genre)
    {
        foreach ($genre->getBooks() as $book) {
            $this->bookUpdateQueue[$book->getId()] = $book->getId();
        }
        $this->resetMenuCache = true;
    }

    /**
     * @param Sequence $sequence
     */
    protected function scheduleSequenceIndexUpdate(Sequence $sequence)
    {
        foreach ($sequence->getBooks() as $book) {
            $this->bookUpdateQueue[$book->getId()] = $book->getId();
        }
    }

    /**
     * @param Tag $tag
     */
    protected function scheduleTagIndexUpdate(Tag $tag)
    {
        foreach ($tag->getBooks() as $book) {
            $this->bookUpdateQueue[$book->getId()] = $book->getId();
        }
    }

    /**
     * Processes index update queue
     */
    protected function processIndexUpdateQueue()
    {
        $messagesToSend = [];

        foreach ($this->bookUpdateQueue as $bookId) {
            array_push($messagesToSend, [
                'command' => 'updateBook',
                'bookId'  => $bookId,
            ]);
        }

        if ($messagesToSend && $this->resetMenuCache) {
            $lastIndex = count($messagesToSend) - 1;
            $messagesToSend[$lastIndex]['reset_menu_cache'] = true;
        }

        foreach ($messagesToSend as $message) {
            $this->awsProducer->publish($message);
        }

        $this->bookUpdateQueue = [];
        $this->resetMenuCache = false;
    }
}
