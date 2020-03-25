<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 15/12/2016
 * Time: 09:28
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FactAnneeRepository extends EntityRepository
{
    public function getAllAnnee()
    {
        $annee = $this->getEntityManager()
            ->getRepository('AppBundle:FactAnnee')
            ->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.annee')
            ->getQuery()
            ->getResult();
        return $annee;
    }
}