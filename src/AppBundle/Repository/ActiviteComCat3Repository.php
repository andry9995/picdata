<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ActiviteComCat3Repository extends EntityRepository
{
    public function getActiviteComCat3ByActiviteComCat2($activiteComCat2)
    {
        $qb = $this->getEntityManager()->getRepository('AppBundle:ActiviteComCat3')->createQueryBuilder('ac');

        $qb->where('ac.activiteComCat2 = :activiteComCat2')
            ->setParameter('activiteComCat2', $activiteComCat2)
            ->orderBy('ac.codeApe','ASC');

        return $qb
            ->getQuery()
            ->getResult()
            ;

    }

    public function getActivieComCat3ByCodeApe($codeApe){

        $codeApeLike = str_replace(" ","", $codeApe);

        $qb = $this->getEntityManager()->getRepository('AppBundle:ActiviteComCat3')->createQueryBuilder('ac');

        $qb->where('ac.codeApe = :codeApe')
            ->setParameter('codeApe', $codeApeLike)
        ->getQuery()->getResult();

        return $qb
            ->getQuery()
            ->getResult();
    }
}
?>