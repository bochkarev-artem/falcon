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
            ->addArgument('type', InputArgument::REQUIRED, 'Type of entity to process. Allowed: "books", "genres"')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = time();
        $output->writeln("<info>Import data started.</info>");
        $type      = $input->getArgument('type');
        $litres    = $this->getContainer()->get('litres_service');
        $result    = $litres->getData($type);
        $endTime   = time();
        $totalTime = $endTime - $startTime;
        if ($result) {
            $output->writeln("<info>Import data finished. Total time: $totalTime seconds</info>");
        } else {
            $output->writeln("<info>Import data errored. Total time: $totalTime seconds</info>");
        }
    }
}
