<?php

/**
 * Created by Netbeans
 * Created on : 17 juil. 2017, 12:46:32
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\OneTache;

class OneFichierRepository extends EntityRepository
{
    /**
     * Récupération des taches
     * @return array
     */
    public function getFiles($ids) {
        $files = $this
                ->createQueryBuilder('fichier')
                ->where('fichier.id IN (:ids)')
                ->setParameter('ids', $ids)
                ->getQuery()
                ->getResult();
        return $files;
    }
}