<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Command;

use AppBundle\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateFeaturedMenuCommand
 * @package AppBundle\Command
 */
class UpdateFeaturedMenuCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update-featured-menu')
            ->setDescription('Update featured books in menu.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = time();
        $output->writeln("<info>Update featured menu started</info>");
        $container = $this->getContainer();
        $em        = $container->get('doctrine.orm.entity_manager');
        $qb        = $em->createQueryBuilder();
        $qb
            ->update('AppBundle:Book', 'b')
            ->set('b.featuredMenu', ':featured_menu')
            ->setParameter('featured_menu', false)
        ;
        $qb->getQuery()->execute();

        $qb = $this->buildQuery($em, 'ru');
        $booksRu = $qb->getQuery()->getResult();

        $qb = $this->buildQuery($em, 'en');
        $booksEn = $qb->getQuery()->getResult();

        $books = array_merge($booksEn, $booksRu);

        /** @var Book $book */
        foreach ($books as $book) {
            $book->setFeaturedMenu(true);
            echo $book->getId() . "\n";
        }

        $em->flush();
        $em->clear();
        $endTime   = time();
        $totalTime = $endTime - $startTime;
        $output->writeln("<info>Update featured menu finished. Total time: $totalTime seconds</info>");
    }

    /**
     * @param EntityManagerInterface $em
     * @param string $locale
     *
     * @return QueryBuilder
     */
    protected function buildQuery($em, $locale)
    {
        $qb = $em->createQueryBuilder();

        return $qb
            ->select('b, g')
            ->from('AppBundle:Book', 'b')
            ->leftJoin('b.genres', 'g')
            ->leftJoin('b.ratings', 'rating')
            ->andWhere($qb->expr()->eq('b.lang', ':locale'))
            ->andWhere($qb->expr()->gt('rating.rating', 0))
            ->setParameter('locale', $locale)
            ->addOrderBy('rating.rating', 'DESC')
            ->addGroupBy('g.id')
            ->addGroupBy('b.id')
            ->addGroupBy('rating.id')
            ->setMaxResults(250)
        ;
    }
}
