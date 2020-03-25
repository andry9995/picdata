<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 12/12/2016
 * Time: 16:13
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FactModeleRepository extends EntityRepository
{
    function getAllModele()
    {
        $modeles = $this->getEntityManager()
            ->getRepository('AppBundle:FactModele')
            ->createQueryBuilder('fm')
            ->select('fm')
            ->orderBy('fm.code')
            ->getQuery()
            ->getResult();
        return $modeles;
    }
}