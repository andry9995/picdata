<?php

/**
 * Created by Netbeans
 * Created on : 23 juin 2017, 23:14:32
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneFamillePrixRepository extends EntityRepository
{
    public function getPriceFamilies()
    {
        $families = $this
                ->createQueryBuilder('family')
                ->orderBy('family.nom')
                ->getQuery()
                ->getResult();
        return $families;
    }
}