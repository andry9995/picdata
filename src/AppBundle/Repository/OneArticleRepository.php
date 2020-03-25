<?php

/**
 * Created by Netbeans
 * Created on : 10 juil. 2017, 09:48:15
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class OneArticleRepository extends EntityRepository
{
    /**
     * Récupération des articles
     * @return array
     */
    public function getArticles(Dossier $dossier, $sort='name', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('article');

        $qb->andWhere('article.dossier = :dossier')
            ->setParameter('dossier', $dossier);
        
        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('article.nom', ':q'),
                    $qb->expr()->like('article.description', ':q'),
                    $qb->expr()->like('article.code', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Période
        if ($period != 'all') {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort == 'datecreation') {
//                    $qb->andWhere($qb->expr()->gte('CAST(article.creeLe AS DATE)', ':startperiod'));
//                    $qb->andWhere($qb->expr()->lte('CAST(article.creeLe AS DATE)', ':endperiod'));

                    $qb->andWhere('article.creeLe >= :startperiod');
                    $qb->andWhere('article.creeLe <= :startperiod');


                    $dateStartArray = explode('/', $startperiod);
                    $dateStartPeriode = null;
                    if(count($dateStartArray) === 3) {
                        $dateStartPeriode = new \DateTime("$dateStartArray[2]-$dateStartArray[1]-$dateStartArray[0]");
                    }

                    $dateEndArray = explode('/', $endperiod);
                    $dateEndPeriode = null;
                    if(count($dateEndArray) === 3) {
                        $dateEndPeriode = new \DateTime("$dateEndArray[2]-$dateEndArray[1]-$dateEndArray[0]");
                    }

                    $qb->setParameter(':startperiod',$dateStartPeriode);
                    $qb->setParameter(':endperiod', $dateEndPeriode);

                }
//                $qb->setParameter(':startperiod', \DateTime::createFromFormat('j/m/Y', $startperiod)->format('Y-m-d'));
//                $qb->setParameter(':endperiod', \DateTime::createFromFormat('j/m/Y', $endperiod)->format('Y-m-d'));
            }
        }
        
        //Tri
        if ($sort == 'name') {
            $qb->orderBy('article.nom', $sortOrder);
        } elseif ($sort == 'datecreation') {
            $qb->orderBy('article.creeLe', $sortOrder);
        } elseif ($sort == 'codearticle') {
            $qb->orderBy('article.code', $sortOrder);
        } elseif ($sort == '') {
            $qb->orderBy('article.nom', $sortOrder);
        }
        
        $articles = $qb->getQuery()
                ->getResult();
        return $articles;
    }
    
    /**
     * Récupère le dernier article
     * @return type
     */
    public function getLastCode(Dossier $dossier) {
        try {
            $qb = $this->createQueryBuilder('article')
                ->where('article.dossier = :dossier')
                ->setParameter('dossier', $dossier);

            $qb->select('article.code')
                    ->andWhere($qb->expr()->like('article.code', ':code'))
                    ->setParameter(':code', 'ART-%')
                    ->setMaxResults(1)
                    ->orderBy('article.id', 'DESC');
            $lastCode = $qb
                    ->getQuery()
                    ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            return 'ART-000';
        }
    }
    
    public function getLastCustomCode($prefixe, Dossier $dossier) {
        try {
            $qb = $this->createQueryBuilder('article');
            $qb->select('article.code')
                    ->where($qb->expr()->like('article.code', ':code'))
                    ->setParameter(':code', $prefixe.'%')
                    ->andWhere('article.dossier = :dossier')
                    ->setParameter('dossier', $dossier)
                    ->setMaxResults(1)
                    ->orderBy('article.id', 'DESC');
            $lastCode = $qb
                    ->getQuery()
                    ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            return $prefixe.'000';
        }
    }
}