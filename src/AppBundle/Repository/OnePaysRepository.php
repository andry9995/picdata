<?php

/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OnePaysRepository extends EntityRepository
{
    public function getCountries()
    {
        $countries = $this
                ->createQueryBuilder('country')
                ->orderBy('country.nom')
                ->getQuery()
                ->getResult();
        return $countries;
    }
}
