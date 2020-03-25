<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 09/12/2016
 * Time: 11:26
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FactUniteRepository extends EntityRepository
{
    function getAllUnite() {
        $unites = $this->getEntityManager()
            ->getRepository('AppBundle:FactUnite')
            ->createQueryBuilder('fu')
            ->select('fu')
            ->orderBy('fu.code')
            ->getQuery()
            ->getResult();
        return $unites;
    }


}