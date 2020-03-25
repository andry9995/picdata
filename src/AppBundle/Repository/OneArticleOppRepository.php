<?php

/**
 * Created by Netbeans
 * Created on : 11 juil. 2017, 21:08:34
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneArticleOppRepository extends EntityRepository
{
    /**
     * Récupération des articles
     * @return array
     */
    public function getArticles($oppID) {
        $articles = $this
                ->createQueryBuilder('article')
                ->where('article.opportunite = :oppID')
                ->setParameter('oppID', $oppID)
                ->getQuery()
                ->getResult();
        return $articles;
    }
    
    /**
     * Récupération des articles opportunité à supprimer
     * @param array $ids
     * @param id $opportuniteId
     * @param boolean $all
     * @return array
     */
    public function getArticlesToRemove($ids, $opportuniteId, $all=false) {
        if ($all) {
            $articlesToRemove = $this
                    ->createQueryBuilder('article')
                    ->where('article.opportunite = :opportuniteId')
                    ->setParameter('opportuniteId', $opportuniteId)
                    ->getQuery()
                    ->getResult();
        } else {
            $articlesToRemove = $this
                ->createQueryBuilder('article')
                ->where('article.opportunite = :opportuniteId')
                ->andWhere('article.id NOT IN (:ids)')
                ->setParameter('opportuniteId', $opportuniteId)
                ->setParameter('ids', $ids)
                ->getQuery()
                ->getResult();
        }
        return $articlesToRemove;
    }
}