<?php
/**
 * @author Artem Bochkarev
 */

namespace AdminBundle\EventListener;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use Doctrine\ORM\Event\OnFlushEventArgs;

class AuthorListener
{
    use ComputingListenerTrait;

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $this->em  = $args->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();

        foreach ($this->uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Author) {
                $this->updateBooksPath($entity);
            }
        }

        foreach ($this->uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Author) {
                $this->updateBooksPath($entity);
            }
        }

        $this->processComputingQueue();
    }

    /**
     * @param Author $author
     */
    public function updateBooksPath(Author $author)
    {
        $changeSet = $this->uow->getEntityChangeSet($author);
        if (isset($changeSet['slug'])) {
            $authorId = $author->getId();
            /** @var Book $book */
            foreach ($author->getBooks() as $book) {
                if ($book->getAuthors()->first()->getId() == $authorId) {
                    $book->setMainAuthorSlug($author->getSlug());
                    $this->addToComputingQueue($book);
                }
            }
        }
    }
}
