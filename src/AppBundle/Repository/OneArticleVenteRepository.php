<?php

/**
 * Created by Netbeans
 * Created on : 16 août 2017, 16:01:39
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneArticleVenteRepository extends EntityRepository
{
    /**
     * Récupération des articles d'un devis
     * @param $devisID
     * @return array
     */
    public function getArticlesDevis($devisID) {
        $articles = $this
                ->createQueryBuilder('article')
                ->where('article.devis = :devisID')
                ->setParameter('devisID', $devisID)
                ->getQuery()
                ->getResult();
        return $articles;
    }
    
    /**
     * Récupération des articles d'une vente
     * @param int $venteID
     * @return type
     */
    public function getArticlesVente($venteID) {
        $articles = $this
                ->createQueryBuilder('article')
                ->where('article.vente = :venteID')
                ->setParameter('venteID', $venteID)
                ->getQuery()
                ->getResult();
        return $articles;
    }
    
    /**
     * Récupération des articles devis à supprimer
     * @param array $ids
     * @param id $devisId
     * @param boolean $all
     * @return array
     */
    public function getArticlesToRemove($ids, $devisId, $all=false) {
        if ($all) {
            $articlesToRemove = $this
                    ->createQueryBuilder('article')
                    ->where('article.devis = :devisId')
                    ->setParameter('devisId', $devisId)
                    ->getQuery()
                    ->getResult();
        } else {
            $articlesToRemove = $this
                ->createQueryBuilder('article')
                ->where('article.devis = :devisId')
                ->andWhere('article.id NOT IN (:ids)')
                ->setParameter('devisId', $devisId)
                ->setParameter('ids', $ids)
                ->getQuery()
                ->getResult();
        }
        return $articlesToRemove;
    }
}