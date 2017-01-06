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
 * Class ImportLitresDataCommand
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
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = time();
        $output->writeln("<info>Import book images started.</info>");

        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();
        $qb
            ->select('b')
            ->from('AppBundle:Book', 'b')
        ;
        $result = $qb->getQuery()->iterate();
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
        $endTime   = time();
        $totalTime = $endTime - $startTime;

        $output->writeln("<info>Import images finished. Total time: $totalTime seconds</info>");
    }
}
