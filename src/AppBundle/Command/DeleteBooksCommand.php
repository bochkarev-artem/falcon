<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Sequence;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteBooksCommand
 * @package AppBundle\Command
 */
class DeleteBooksCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:delete-books')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Delete books started.</info>');
        $startTime = time();
        $container = $this->getContainer();
        $em        = $container->get('doctrine.orm.entity_manager');
        $qb        = $em->createQueryBuilder();
        $qb
            ->select('b')
            ->from('AppBundle:Book', 'b')
            ->where($qb->expr()->eq('b.lang', ':lang'))
            ->setParameter('lang', 'en')
        ;

        $result    = $qb->getQuery()->iterate();
        $batchSize = 100;
        $i         = 0;
        foreach ($result as $row) {
            /** @var Book $book */
            $book = $row[0];
            $em->remove($book);
            if ((++$i % $batchSize) === 0) {
                $em->flush();
                $em->clear();
                echo $i . "\n";
            }
        }
        $em->flush();
        $em->clear();

        $qb = $em->createQueryBuilder();
        $qb
            ->select('b')
            ->from('AppBundle:Author', 'b')
            ->where($qb->expr()->eq('b.lang', ':lang'))
            ->setParameter('lang', 'en')
        ;

        $result    = $qb->getQuery()->iterate();
        $batchSize = 100;
        $i         = 0;
        foreach ($result as $row) {
            /** @var Author $author */
            $author = $row[0];
            $em->remove($author);
            if ((++$i % $batchSize) === 0) {
                $em->flush();
                $em->clear();
                echo $i . "\n";
            }
        }
        $em->flush();
        $em->clear();

        $qb = $em->createQueryBuilder();
        $qb
            ->select('b')
            ->from('AppBundle:Sequence', 'b')
            ->where($qb->expr()->eq('b.lang', ':lang'))
            ->setParameter('lang', 'en')
        ;

        $result    = $qb->getQuery()->iterate();
        $batchSize = 100;
        $i         = 0;
        foreach ($result as $row) {
            /** @var Sequence $sequence */
            $sequence = $row[0];
            $em->remove($sequence);
            if ((++$i % $batchSize) === 0) {
                $em->flush();
                $em->clear();
                echo $i . "\n";
            }
        }
        $em->flush();
        $em->clear();

        $endTime   = time();
        $totalTime = $endTime - $startTime;

        $output->writeln("<info>Delete books finished. Total time: $totalTime seconds</info>");
    }
}
