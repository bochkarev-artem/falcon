<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Ads;
use Doctrine\ORM\Event;
use Doctrine\ORM\UnitOfWork;
use Uecode\Bundle\QPushBundle\Provider\AwsProvider;

class AdsListener
{
    /**
     * @var AwsProvider
     */
    protected $awsProducer;

    /**
     * @var UnitOfWork
     */
    protected $uow;

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
     * @param Event\PostFlushEventArgs $eventArgs
     */
    public function postFlush(Event\PostFlushEventArgs $eventArgs)
    {
        if ($this->isMessageQueueOn) {
            $em        = $eventArgs->getEntityManager();
            $this->uow = $em->getUnitOfWork();

            $this->processEntityChanges($this->uow->getScheduledEntityUpdates());
            $this->processEntityChanges($this->uow->getScheduledEntityDeletions());
        }
    }

    /**
     * @param array $entities
     */
    protected function processEntityChanges(array $entities)
    {
        foreach ($entities as $entity) {
            if ($entity instanceof Ads) {
                $this->awsProducer->publish([
                    'command' => 'resetAdCache',
                ]);
                break;
            }
        }
    }
}
