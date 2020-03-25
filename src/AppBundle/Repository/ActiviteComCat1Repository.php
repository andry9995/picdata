<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ActiviteComCat1Repository extends EntityRepository
{
    public function getActiviteComCat1ByActiviteComCat($activiteComCat)
    {
        $qb = $this->getEntityManager()->getRepository('AppBundle:ActiviteComCat1')->createQueryBuilder('bc');

        $qb->where('bc.activiteComCat = :activiteComCat')
            ->setParameter('activiteComCat', $activiteComCat);

        return $qb
            ->getQuery()
            ->getResult()
            ;

    }
}
?>