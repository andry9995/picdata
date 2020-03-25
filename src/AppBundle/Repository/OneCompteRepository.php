<?php

/**
 * Created by Netbeans
 * Created on : 24 août 2017, 10:06:47
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneCompteRepository extends EntityRepository
{
    public function getComptes() {
        $types = $this
                ->createQueryBuilder('compte')
                ->orderBy('compte.compte')
                ->getQuery()
                ->getResult();
        return $types;
    }
}