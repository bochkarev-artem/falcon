<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Sequence;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateLocaleCommand
 * @package AppBundle\Command
 */
class UpdateLocaleCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update-locale')
            ->setDescription('Update locale for entities.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = time();
        $output->writeln("<info>Update started</info>");
        $container = $this->getContainer();
        $em        = $container->get('doctrine.orm.entity_manager');
        $batchSize = 150;

        $qb = $this->buildQuery($em);
        $books = $qb->getQuery()->iterate();

        $i = 0;
        $count = 0;
        /** @var Book $book */
        foreach ($books as $row) {
            $book = $row[0];
            if ($book->getAuthors()->isEmpty()) {
                $em->remove($book);
                $count++;
            }
            if (++$i % $batchSize == 0) {
                echo $i . "\n";
                $em->flush();
                $em->clear();
            }
        }
        $em->flush();
        $em->clear();

        $qb = $this->buildAuthorQuery($em);
        $authors = $qb->getQuery()->iterate();

        $i = 0;
        foreach ($authors as $row) {
            $author = $row[0];
            /** @var Author $author */
            if ($author->getBooks()->isEmpty()) {
                $em->remove($author);
                $count++;
            }
            if (++$i % $batchSize == 0) {
                echo $i . "\n";
                $em->flush();
                $em->clear();
            }
        }
        $em->flush();
        $em->clear();

        $qb = $this->buildSequenceQuery($em);
        $sequences = $qb->getQuery()->iterate();

        $i = 0;
        foreach ($sequences as $row) {
            $sequence = $row[0];
            /** @var Sequence $sequence */
            if ($sequence->getBooks()->isEmpty()) {
                $em->remove($sequence);
                $count++;
            }
            if (++$i % $batchSize == 0) {
                echo $i . "\n";
                $em->flush();
                $em->clear();
            }
        }
        $em->flush();
        $em->clear();

        $endTime   = time();
        $totalTime = $endTime - $startTime;
        $output->writeln("<info>Update finished, deleted $count. Total time: $totalTime seconds</info>");
    }

    /**
     * @param EntityManagerInterface $em
     *
     * @return QueryBuilder
     */
    protected function buildQuery($em)
    {
        $qb = $em->createQueryBuilder();

        return $qb
            ->select('a')
            ->from('AppBundle:Book', 'a')
        ;
    }

    /**
     * @param EntityManagerInterface $em
     *
     * @return QueryBuilder
     */
    protected function buildAuthorQuery($em)
    {
        $qb = $em->createQueryBuilder();

        return $qb
            ->select('a')
            ->from('AppBundle:Author', 'a')
        ;
    }

    /**
     * @param EntityManagerInterface $em
     *
     * @return QueryBuilder
     */
    protected function buildSequenceQuery($em)
    {
        $qb = $em->createQueryBuilder();

        return $qb
            ->select('a')
            ->from('AppBundle:Sequence', 'a')
        ;
    }
}
