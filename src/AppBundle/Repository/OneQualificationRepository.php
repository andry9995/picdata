<?php

/**
 * Created by Netbeans
 * Created on : 23 juin 2017, 22:28:43
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneQualificationRepository extends EntityRepository
{
    public function getQualifications() {
        $qualifications = $this
                ->createQueryBuilder('qualification')
                ->orderBy('qualification.nom')
                ->getQuery()
                ->getResult();
        return $qualifications;
    }
}