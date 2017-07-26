<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportLitresDataCommand
 * @package AppBundle\Command
 */
class ImportLitresDataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update-litres-data')
            ->setDescription('Update Litres Data.')
            ->addArgument('type', InputArgument::OPTIONAL, 'Type of entity to process. Allowed: "books", "genres"')
            ->addArgument('file', InputArgument::OPTIONAL, 'Source from file. Allowed: "file, stream", "stream"')
            ->addArgument('debug', InputArgument::OPTIONAL, 'Debug flag. Allowed: "y", "n"', 'y')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Import data started.</info>");
        $startTime = time();
        $type      = $input->getArgument('type');
        $type      = $type ?: 'books';
        $debug     = $input->getArgument('debug');
        $file      = $input->getArgument('file');
        if ('file' == $file) {
            $litres = $this->getContainer()->get('AppBundle\Service\LitresFileService');
        } else {
            $litres = $this->getContainer()->get('AppBundle\Service\LitresService');
        }
        $result    = $litres->getData($type, $debug);
        $endTime   = time();
        $totalTime = $endTime - $startTime;
        if ($result) {
            $output->writeln("<info>Import data finished. Total time: $totalTime seconds</info>");
        } else {
            $output->writeln("<info>Import data errored. Total time: $totalTime seconds</info>");
        }
    }
}
