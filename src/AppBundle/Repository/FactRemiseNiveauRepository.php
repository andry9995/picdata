<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 12/12/2016
 * Time: 08:33
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FactRemiseNiveauRepository extends EntityRepository
{
    function getAllNiveau()
    {
        $niveaux = $this->getEntityManager()
            ->getRepository('AppBundle:FactRemiseNiveau')
            ->createQueryBuilder('fn')
            ->select('fn')
            ->orderBy('fn.niveau')
            ->getQuery()
            ->getResult();
        return $niveaux;
    }
}