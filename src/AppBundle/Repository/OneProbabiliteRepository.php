<?php

/**
 * Created by Netbeans
 * Created on : 6 juil. 2017, 13:17:18
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneProbabiliteRepository extends EntityRepository
{
    /**
     * Récupération des probabilités
     * @return array
     */
    public function getProbabilites() {
        $probabilites = $this
                ->createQueryBuilder('probabilite')
                ->getQuery()
                ->getResult();
        return $probabilites;
    }
}