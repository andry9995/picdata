<?php

/**
 * Created by Netbeans
 * Created on : 10 juil. 2017, 15:36:31
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneFamilleArticleRepository extends EntityRepository
{
    /**
     * Récupération des famille d'article
     * @return array
     */
    public function getFamilies() {
        $articles = $this
                ->createQueryBuilder('famille')
                ->getQuery()
                ->getResult();
        return $articles;
    }
}