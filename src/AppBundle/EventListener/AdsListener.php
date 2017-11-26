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
     * @var bool
     */
    protected $isMessageQueueOn;

    /**
     * @var string
     */
    protected $command = '';

    /**
     * @param AwsProvider $awsProducer
     * @param bool        $messageQueueOn
     */
    public function __construct(AwsProvider $awsProducer, $messageQueueOn)
    {
        $this->awsProducer      = $awsProducer;
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

            $this->prepareCommand($this->uow->getScheduledEntityUpdates());
            if (!$this->command) {
                $this->prepareCommand($this->uow->getScheduledEntityDeletions());
            }
        }
    }

    /**
     * @param Event\PostFlushEventArgs $eventArgs
     */
    public function postFlush(Event\PostFlushEventArgs $eventArgs)
    {
        $this->processEntityChanges();
    }

    protected function processEntityChanges()
    {
        if ($this->command) {
            $this->awsProducer->publish([
                'command' => $this->command,
            ]);
        }
    }

    /**
     * @param array $entities
     */
    protected function prepareCommand(array $entities)
    {
        foreach ($entities as $entity) {
            if ($entity instanceof Ads) {
                $this->command = 'resetAdCache';

                break;
            }
        }
    }
}
