<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 16/10/2018
 * Time: 13:38
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TvaImputationRepository extends EntityRepository
{
    public function getGroupedTvaImputationByImage($image){

        return $this->createQueryBuilder('ti')
            ->where('ti.image = :image')
            ->setParameter('image', $image)
            ->groupBy('ti.pcc')
            ->addGroupBy('ti.tiers')
            ->addGroupBy('ti.pccTva')
            ->select('ti')
            ->addSelect('SUM(ti.montantTtc) AS ttc')
            ->addSelect('SUM(ti.montantHt) AS ht')
            ->addSelect('SUM(ROUND(ti.montantTtc - ti.montantHt, 2)) AS tva')
            ->getQuery()
            ->getResult();

    }
}