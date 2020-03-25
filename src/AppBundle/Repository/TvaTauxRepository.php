<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 15/11/2017
 * Time: 14:06
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TvaTauxRepository extends EntityRepository
{
    public function getTaxes() {
        $taxes = $this->getEntityManager()
            ->getRepository('AppBundle:TvaTaux')
            ->createQueryBuilder('taxe')
            ->where('taxe.actif = :actif')
            ->setParameter('actif', 1)
            ->orderBy('taxe.taux')
            ->getQuery()
            ->getResult();
        return $taxes;
    }
}