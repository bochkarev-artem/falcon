<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use AppBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ScheduleBookIndexCommand
 * @package AppBundle\Command
 */
class ScheduleBookIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:schedule-book-index')
            ->setDescription('Update book index.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = time();
        $output->writeln("<info>Update book index started</info>");
        $container = $this->getContainer();
        $em        = $container->get('doctrine.orm.entity_manager');
        $qb        = $em->createQueryBuilder();
        $qb
            ->select('b')
            ->from('AppBundle:Book', 'b')
            ->where($qb->expr()->eq('b.enabled', ':enabled'))
            ->setParameter('enabled', false);
        $result    = $qb->getQuery()->iterate();
        $batchSize = 50;
        $i         = 0;
        foreach ($result as $row) {
            /** @var Book $book */
            $book = $row[0];
            $i++;
            $book->setEnabled(true);
            $output->writeln(sprintf("<info>Book %s reindexed</info>", $book->getId()));

            if ($i % $batchSize == 0) {
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();
        $em->clear();
        $endTime   = time();
        $totalTime = $endTime - $startTime;
        $output->writeln("<info>Update book index finished. Total time: $totalTime seconds</info>");
    }
}
