<?php

/**
 * Created by Netbeans
 * Created on : 10 juil. 2017, 13:21:57
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneUniteArticleRepository extends EntityRepository
{
    /**
     * Récupération des unités
     * @return array
     */
    public function getUnits() {
        $units = $this
                ->createQueryBuilder('unit')
                ->orderBy('unit.nom')
                ->getQuery()
                ->getResult();
        return $units;
    }
}