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
            ->addArgument('type', InputArgument::REQUIRED, 'Type of entity to process. Allowed: "books", "genres"', 'books')
            ->addArgument('endpoint', InputArgument::OPTIONAL, 'Endpoint')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nowDateTime = new \DateTime();
        $startTime   = $nowDateTime->format('Y-m-d H:i:s');
        $output->writeln("<info>Import data started. Start at $startTime</info>");
        $type        = $input->getArgument('type');
        $endpoint    = $input->getArgument('endpoint');
        $litres      = $this->getContainer()->get('litres_service');
        $nowDateTime = new \DateTime();
        $endTime     = $nowDateTime->format('Y-m-d H:i:s');
        if ($litres->getData($type, $endpoint)) {
            $output->writeln("<info>Import data finished at $endTime. Total time: $endTime - $startTime</info>");
        } else {
            $output->writeln("<info>Import data errored. Total time: $endTime - $startTime</info>");
        }
    }
}
