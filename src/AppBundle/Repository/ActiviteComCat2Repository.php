<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ActiviteComCat2Repository extends EntityRepository
{
    public function getActiviteComCat2ByActiviteComCat1($activiteComCat1)
    {
        $qb = $this->getEntityManager()->getRepository('AppBundle:ActiviteComCat2')->createQueryBuilder('ac');

        $qb->where('ac.activiteComCat1 = :activiteComCat1')
            ->setParameter('activiteComCat1', $activiteComCat1);

        return $qb
            ->getQuery()
            ->getResult()
            ;

    }
}
?>