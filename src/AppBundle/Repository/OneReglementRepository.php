<?php

/**
 * Created by Netbeans
 * Created on : 23 juin 2017, 23:00:52
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneReglementRepository extends EntityRepository
{
    public function getReglements(){
        $reglements = $this
                ->createQueryBuilder('reglement')
                ->orderBy('reglement.nom')
                ->getQuery()
                ->getResult();
        return $reglements;
    }
}