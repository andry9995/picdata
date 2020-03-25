<?php

/**
 * Created by Netbeans
 * Created on : 6 juil. 2017, 13:07:08
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use AppBundle\Entity\OneStatusOpp;
use Doctrine\ORM\EntityRepository;

class OneStatusOppRepository extends EntityRepository
{
    /**
     * Récupération des statuts d'opportunité
     * @return array
     */
    public function getStatus($dossier)
    {
        $status = $this
            ->createQueryBuilder('status')
            ->where('status.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->orderBy('status.ordre', 'ASC')
            ->getQuery()
            ->getResult();
        return $status;
    }

    public function getLastPosition($dossier)
    {
        /** @var OneStatusOpp[] $statuss */
        $statuss = $this->createQueryBuilder('status')
            ->where('status.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->orderBy('status.ordre', 'DESC')
            ->getQuery()
            ->getResult();

        if(count($statuss) > 0){
            $status = $statuss[0];
            return $status->getOrdre();
        }

        return -1;
    }
}