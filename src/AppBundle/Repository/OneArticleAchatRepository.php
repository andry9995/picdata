<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 25/04/2018
 * Time: 17:37
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class OneArticleAchatRepository extends EntityRepository
{
    public function getArticlesAchat($achatID) {
        $articles = $this
            ->createQueryBuilder('article')
            ->where('article.achat = :achatID')
            ->setParameter('achatID', $achatID)
            ->getQuery()
            ->getResult();
        return $articles;
    }


}