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
 * Class UpdateBooksWithAuthorSlugCommand
 * @package AppBundle\Command
 */
class UpdateBooksWithAuthorSlugCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update-books')
            ->setDescription('Update Litres images.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Update started.</info>");
        $startTime = time();
        $container = $this->getContainer();
        $em        = $container->get('doctrine.orm.entity_manager');
        $qb        = $em->createQueryBuilder();
        $qb
            ->select('b')
            ->from('AppBundle:Book', 'b')
            ->where($qb->expr()->isNull('b.mainAuthorSlug'))
        ;

        $result    = $qb->getQuery()->iterate();
        $batchSize = 100;
        $i         = 0;
        foreach ($result as $row) {
            /** @var Book $book */
            $book   = $row[0];
            $author = $book->getAuthors()->first();
            echo $book->getId() . "\n";
            $book->setMainAuthorSlug($author->getSlug());
            if ((++$i % $batchSize) === 0) {
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();
        $em->clear();
        $endTime   = time();
        $totalTime = $endTime - $startTime;

        $output->writeln("<info>Update finished. Total time: $totalTime seconds</info>");
    }
}
