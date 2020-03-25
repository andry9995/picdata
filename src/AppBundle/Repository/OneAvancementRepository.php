<?php

/**
 * Created by Netbeans
 * Created on : 6 juil. 2017, 13:00:53
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneAvancementRepository extends EntityRepository
{
    /**
     * Récupération des avancements
     * @return array
     */
    public function getAvancements() {
        $avancements = $this
                ->createQueryBuilder('avancement')
                ->getQuery()
                ->getResult();
        return $avancements;
    }
}