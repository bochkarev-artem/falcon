<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use AppBundle\Entity\Book;
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
            /** @var Book $book */
            if ($this->setBookLang($book)) {
                $count++;
            }
            $i++;
            if ($i % $batchSize == 0) {
                echo $i . "\n";
                $em->flush();
                $em->clear();
            }
        }
        $em->flush();
        $em->clear();

        $endTime   = time();
        $totalTime = $endTime - $startTime;
        $output->writeln("<info>Update finished, mismatch $count. Total time: $totalTime seconds</info>");
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
            ->where($qb->expr()->eq('a.lang', ':locale'))
            ->setParameter('locale', 'en')
        ;
    }

    /**
     * @param Book $book
     *
     * @return bool
     */
    protected function setBookLang($book)
    {
        if (preg_match("/[у|е|ы|а|о|э|я|и|ю]/", $book)) {
            $book->setLang('ru');
            return true;
        }

        return false;
    }
}
