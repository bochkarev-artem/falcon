<?php

namespace AppBundle\Service;

use AppBundle\Entity\Ads;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class AdsManager
{
    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param integer $position
     *
     * @return Ads|null
     */
    public function findOneByPosition($position)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('ads')
            ->from('AppBundle:Ads', 'ads')
            ->andWhere($qb->expr()->eq('ads.position', ':position'))
            ->andWhere($qb->expr()->eq('ads.active', ':active'))
            ->setParameter('position', $position)
            ->setParameter('active', true)
            ->orderBy('ads.priority', Criteria::ASC);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
