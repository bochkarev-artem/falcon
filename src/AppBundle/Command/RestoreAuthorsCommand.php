<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportBookImagesCommand
 * @package AppBundle\Command
 */
class RestoreAuthorsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:restore-authors')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>started.</info>");
        $startTime = time();
        $container = $this->getContainer();
        $em        = $container->get('doctrine.orm.entity_manager');
        $qs        = $container->get('AppBundle\Service\QueryService');
        $authorRepo = $em->getRepository('AppBundle:Author');
        $qb        = $em->createQueryBuilder();
        $qb
            ->select('b')
            ->from('AppBundle:Book', 'b')
        ;

        $result    = $qb->getQuery()->iterate();
        $batchSize = 300;
        $i         = 0;
        $count     = 0;
        $processed = 0;
        foreach ($result as $row) {
            $book = $row[0];
            /** @var Book $book */
            if (!$book->getAuthors()->count()) {
                $queryParams = new QueryParams();
                $queryParams
                    ->setFilterId($book->getId())
                    ->setSize(1)
                ;

                if (!$books = $qs->find($queryParams)) {
                    continue;
                }
                $current = $books->getIterator()->current();
                if (gettype($current) != "boolean") {
                    $bookData = $current->getSource();
                } else {
                    continue;
                }

                $authors = $bookData['authors'];
                foreach ($authors as $authorData) {
                    $author = $authorRepo->findOneBy(['documentId' => $authorData['document_id']]);
                    if ($author) {
                        $author->addBook($book);
                        $em->flush();
                    } else {
                        $author = new Author();
                        $author
                            ->setDocumentId($authorData['document_id'])
                            ->setFirstName($authorData['first_name'])
                            ->setLastName($authorData['last_name'])
                            ->setMiddleName($authorData['middle_name'])
                            ->addBook($book)
                        ;
                        $em->persist($author);
                        $em->flush();
                    }
                }
            }
            if ((++$i % $batchSize) === 0) {
                $em->clear();
                $processed += $batchSize;
                echo $processed . "\n";
            }
        }

        $em->flush();
        $em->clear();
        $endTime   = time();
        $totalTime = $endTime - $startTime;

        $output->writeln("<info>$count missing. Total time: $totalTime seconds</info>");
    }
}
