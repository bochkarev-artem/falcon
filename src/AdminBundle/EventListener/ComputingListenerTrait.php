<?php
/**
 * @author Artem Bochkarev
 */

namespace AdminBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use SplObjectStorage;

trait ComputingListenerTrait
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var UnitOfWork
     */
    protected $uow;

    /**
     * @var SplObjectStorage
     */
    protected $computingQueue;

    /**
     * @param object $object
     */
    protected function addToComputingQueue($object)
    {
        if (!$this->computingQueue instanceof SplObjectStorage) {
            $this->computingQueue = new SplObjectStorage();
        }

        $this->computingQueue->attach($object);
    }

    /**
     * Recalculate change sets for queued objects
     */
    protected function processComputingQueue()
    {
        if (!$this->computingQueue instanceof SplObjectStorage) {
            return;
        }

        foreach ($this->computingQueue as $entity) {
            $this->computeOrRecomputeEntityChangeSet($entity);
        }
        $this->computingQueue->removeAll($this->computingQueue);
    }

    /**
     * @param object $entity
     */
    protected function computeOrRecomputeEntityChangeSet($entity)
    {
        $classMetadata = $this->em->getClassMetadata(get_class($entity));
        if ($this->em->contains($entity)) {
            $this->uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
        } else {
            $this->uow->computeChangeSet($classMetadata, $entity);
        }
    }
}
