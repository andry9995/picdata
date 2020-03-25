<?php

/**
 * Created by Netbeans
 * Created on : 29 juin 2017, 13:42:01
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneContactClientRepository extends EntityRepository
{
    /**
     * Récupération des contacts d'un clientProspect
     * @param int $clientProspectID
     * @return array $contacts
     */
    public function getContacts($clientProspectID) {
        $contacts = $this
                ->createQueryBuilder('contact')
                ->where('contact.tiers = :id')
                ->setParameter('id', $clientProspectID)
                ->getQuery()
                ->getResult();
        return $contacts;
    }
    
    /**
     * Récupération des contacts à supprimer
     * @param array $ids
     * @param int $prospectClientId
     * @param boolean $all
     * @return array
     */
    public function getContactsToRemove($ids, $prospectClientId, $all=false) {
        if ($all) {
            $contactsToRemove = $this
                    ->createQueryBuilder('contact')
                    ->where('contact.tiers = :prospectClientId')
                    ->setParameter('prospectClientId', $prospectClientId)
                    ->getQuery()
                    ->getResult();
        } else {
            $contactsToRemove = $this
                ->createQueryBuilder('contact')
                ->where('contact.tiers = :prospectClientId')
                ->andWhere('contact.id NOT IN (:ids)')
                ->setParameter('prospectClientId', $prospectClientId)
                ->setParameter('ids', $ids)
                ->getQuery()
                ->getResult();
        }
        return $contactsToRemove;
    }
}