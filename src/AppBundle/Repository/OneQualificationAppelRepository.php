<?php

/**
 * Created by Netbeans
 * Created on : 18 juil. 2017, 15:29:40
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneQualificationAppelRepository extends EntityRepository
{
    /**
     * Récupération des qualifications des appels
     * @return array
     */
    public function getQualifications() {
        $qualifications = $this
                ->createQueryBuilder('qualification')
                ->orderBy('qualification.nom')
                ->getQuery()
                ->getResult();
        return $qualifications;
    }
}