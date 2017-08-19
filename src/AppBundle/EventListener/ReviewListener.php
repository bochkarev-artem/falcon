<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\BookReview;
use Doctrine\ORM\Event;
use Doctrine\ORM\UnitOfWork;

class ReviewListener
{
    /**
     * @var UnitOfWork
     */
    protected $uow;

    /**
     * @var string
     */
    protected $emailFrom;

    /**
     * @var string
     */
    protected $emailFromName;


    /**
     * @var string
     */
    protected $emailTo;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @param \Swift_Mailer $mailer
     * @param string $emailFrom
     * @param string $emailFromName
     * @param string $emailTo
     */
    public function __construct(
        \Swift_Mailer $mailer,
        $emailFrom,
        $emailFromName,
        $emailTo
    ) {
        $this->mailer = $mailer;
        $this->emailFrom = $emailFrom;
        $this->emailFromName = $emailFromName;
        $this->emailTo = $emailTo;
    }

    /**
     * @param Event\OnFlushEventArgs $eventArgs
     */
    public function onFlush(Event\OnFlushEventArgs $eventArgs)
    {
        $em        = $eventArgs->getEntityManager();
        $this->uow = $em->getUnitOfWork();
        $this->processEntityChanges($this->uow->getScheduledEntityInsertions());
    }

    /**
     * @param array $entities
     */
    protected function processEntityChanges(array $entities)
    {
        foreach ($entities as $entity) {
            if ($entity instanceof BookReview) {
                $body = $entity->getBook()->getTitle() . '<br><br>' . $entity->getText();
                $message = \Swift_Message::newInstance(
                    'New review pending moderation',
                    $body,
                    'text/html')
                    ->setFrom($this->emailFrom, $this->emailFromName)
                    ->setTo($this->emailTo)
                ;
                $this->mailer->send($message);
            }
        }
    }
}
