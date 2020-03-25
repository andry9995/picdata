<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 04/04/2018
 * Time: 09:03
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class OneContactFournisseurRepository extends EntityRepository
{

    /**
     * Récupération des contacts d'un fournisseur
     * @param int $fournisseurID
     * @return array $contacts
     */
    public function getContacts($fournisseurID) {
        $contacts = $this
            ->createQueryBuilder('contact')
            ->where('contact.oneFournisseur = :id')
            ->setParameter('id', $fournisseurID)
            ->getQuery()
            ->getResult();
        return $contacts;
    }

    /**
     * Récupération des contacts à supprimer
     * @param array $ids
     * @param int $fournisseurId
     * @param boolean $all
     * @return array
     */
    public function getContactsToRemove($ids, $fournisseurId, $all=false) {
        if ($all) {
            $contactsToRemove = $this
                ->createQueryBuilder('contact')
                ->where('contact.oneFournisseur = :fournisseurId')
                ->setParameter('fournisseurId', $fournisseurId)
                ->getQuery()
                ->getResult();
        } else {
            $contactsToRemove = $this
                ->createQueryBuilder('contact')
                ->where('contact.oneFournisseur = :fournisseurId')
                ->andWhere('contact.id NOT IN (:ids)')
                ->setParameter('fournisseurId', $fournisseurId)
                ->setParameter('ids', $ids)
                ->getQuery()
                ->getResult();
        }
        return $contactsToRemove;
    }

}