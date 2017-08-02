<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Sequence;
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
        $batchSize = 300;

        $qb = $this->buildAuthorQuery($em);
        $authors = $qb->getQuery()->iterate();

        $i = 0;
        /** @var Author $author */
        foreach ($authors as $row) {
            $author = $row[0];
            $name = $author->getShortName();
            $this->setLang($author, $name);
            $i++;
            if ($i % $batchSize == 0) {
                echo $i . "\n";
                $em->flush();
                $em->clear();
            }
        }
        $em->flush();
        $em->clear();

        $qb = $this->buildSequenceQuery($em);
        $sequences = $qb->getQuery()->iterate();

        $i = 0;
        /** @var Sequence $sequence */
        foreach ($sequences as $row) {
            $sequence = $row[0];
            $name = $sequence->getName();
            $this->setLang($sequence, $name);
            $i++;
            if ($i % $batchSize == 0) {
                echo $i . "\n";
                $em->flush();
                $em->clear();
            }
        }
        $em->flush();
        $em->clear();

        $qb = $this->buildAuthorQuery($em);
        $authors = $qb->getQuery()->iterate();

        $batchSize = 100;
        $i = 0;
        $count = 0;
        /** @var Author $author */
        foreach ($authors as $row) {
            $author = $row[0];
            /** @var Book $book */
            foreach ($author->getBooks() as $book) {
                if ($this->setBookLang($book, $author->getLang())) {
                    $count++;
                }
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

        $qb = $this->buildSequenceQuery($em);
        $sequences = $qb->getQuery()->iterate();

        $i = 0;
        /** @var Sequence $sequence */
        foreach ($sequences as $row) {
            $sequence = $row[0];
            /** @var Book $book */
            foreach ($sequence->getBooks() as $book) {
                if ($this->setBookLang($book, $sequence->getLang())) {
                    $count++;
                }
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
    protected function buildAuthorQuery($em)
    {
        $qb = $em->createQueryBuilder();

        return $qb
            ->select('a')
            ->from('AppBundle:Author', 'a')
        ;
    }

    /**
     * @param EntityManagerInterface $em
     *
     * @return QueryBuilder
     */
    protected function buildSequenceQuery($em)
    {
        $qb = $em->createQueryBuilder();

        return $qb
            ->select('a')
            ->from('AppBundle:Sequence', 'a')
        ;
    }

    /**
     * @param Book $book
     * @param string $lang
     *
     * @return bool
     */
    protected function setBookLang($book, $lang)
    {
        if ($book->getLang() != $lang) {
            $book->setLang($lang);

            return true;
        }

        return false;
    }

    /**
     * @param Sequence|Author $entity
     * @param string $name
     */
    protected function setLang($entity, $name)
    {
        if (preg_match("/[у|е|ы|а|о|э|я|и|ю]/", $name)) {
            $entity->setLang('ru');
        } else {
            $entity->setLang('en');
        }
    }
}
