<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use AppBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportBookImagesCommand
 * @package AppBundle\Command
 */
class ImportBookImagesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update-book-images')
            ->setDescription('Update Litres images.')
            ->addArgument('force', InputArgument::OPTIONAL, 'Force update. Allowed: "force", "n"', 'n')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Import book images started.</info>");
        $startTime = time();
        $force     = $input->getArgument('force') === 'n' ? false : true;
        $container = $this->getContainer();
        $em        = $container->get('doctrine.orm.entity_manager');
        $qb        = $em->createQueryBuilder();
        $qb
            ->select('b')
            ->from('AppBundle:Book', 'b')
        ;

        if (!$force) {
            $qb->andWhere($qb->expr()->isNull('b.coverPath'));
        }

        $result    = $qb->getQuery()->iterate();
        $batchSize = 100;
        $i         = 0;
        $imageUploadService = $container->get('image_upload_service');
        foreach ($result as $row) {
            /** @var Book $book */
            $book = $row[0];
            $imageUploadService->updateBookCover($book);
            if ((++$i % $batchSize) === 0) {
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();
        $em->clear();
        $endTime   = time();
        $totalTime = $endTime - $startTime;

        $output->writeln("<info>Import images finished. Total time: $totalTime seconds</info>");
    }
}
