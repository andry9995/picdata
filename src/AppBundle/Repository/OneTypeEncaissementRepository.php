<?php

/**
 * Created by Netbeans
 * Created on : 23 août 2017, 19:13:22
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneTypeEncaissementRepository extends EntityRepository
{
    public function getTypeEncaissements() {
        $types = $this
                ->createQueryBuilder('type')
                ->orderBy('type.nom')
                ->getQuery()
                ->getResult();
        return $types;
    }
}