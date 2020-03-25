<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 18/06/2018
 * Time: 15:49
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class FactCritereRepository extends EntityRepository
{
    public function getAllCritere()
    {
        $criteres = $this->getEntityManager()
            ->getRepository('AppBundle:FactCritere')
            ->createQueryBuilder('f')
            ->select('f')
            ->orderBy('f.nom')
            ->getQuery()
            ->getResult();
        return $criteres;
    }
}