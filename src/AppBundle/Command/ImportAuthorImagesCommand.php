<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use AppBundle\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportAuthorImagesCommand
 * @package AppBundle\Command
 */
class ImportAuthorImagesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update-author-images')
            ->setDescription('Update Litres images.')
            ->addArgument('force', InputArgument::OPTIONAL, 'Force update. Allowed: "y", "n"', 'n')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Import author images started.</info>");
        $startTime = time();
        $force     = $input->getArgument('force') === 'n' ? false : true;
        $container = $this->getContainer();
        $em        = $container->get('doctrine.orm.entity_manager');
        $qb        = $em->createQueryBuilder();
        $qb
            ->select('a')
            ->from('AppBundle:Author', 'a')
        ;

        if (!$force) {
            $qb->andWhere($qb->expr()->isNull('a.photoPath'));
        }

        $result    = $qb->getQuery()->iterate();
        $batchSize = 100;
        $i         = 0;
        $imageUploadService = $container->get('AppBundle\Service\ImageUploadService');
        foreach ($result as $row) {
            /** @var Author $author */
            $author = $row[0];
            $imageUploadService->updateAuthorPhoto($author);
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
