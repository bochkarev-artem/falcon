<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use AppBundle\Entity\Book;
use AppBundle\Service\ImageUploadService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteBookImagesCommand
 * @package AppBundle\Command
 */
class DeleteBookImagesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:delete-book-images')
            ->setDescription('Update Litres images.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Delete book images started.</info>');
        $startTime = time();
        $container = $this->getContainer();
        $em        = $container->get('doctrine.orm.entity_manager');
        $qb        = $em->createQueryBuilder();
        $qb
            ->select('b')
            ->from('AppBundle:Book', 'b')
            ->where($qb->expr()->eq('b.lang', ':lang'))
            ->andWhere($qb->expr()->isNotNull('b.coverPath'))
            ->setParameter('lang', 'en')
        ;

        $result    = $qb->getQuery()->iterate();
        $batchSize = 100;
        $i         = 0;
        $imageUploadService = $container->get(ImageUploadService::class);
        foreach ($result as $row) {
            /** @var Book $book */
            $book = $row[0];
            $imageUploadService->removeImage($book);
            if ((++$i % $batchSize) === 0) {
                echo $i . "\n";
            }
        }

        $endTime   = time();
        $totalTime = $endTime - $startTime;

        $output->writeln("<info>Delete images finished. Total time: $totalTime seconds</info>");
    }
}
