<?php

/**
 * Created by Netbeans
 * Created on : 15 août 2017, 17:40:10
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Tiers;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\OneDevis;
use AppBundle\Entity\OneClientProspect;

class OneDevisRepository extends EntityRepository
{
    /**
     * Récupération des devis
     * @param array $clientProspects
     * @param $exercice
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param string $startperiod
     * @param string $endperiod
     * @param string $stat
     * @return array
     */
    public function getDevis(array $clientProspects, $exercice,$sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='', $stat='all') {
        $qb = $this->createQueryBuilder('devis')
            ->innerJoin('devis.tiers', 'tiers');


        $qb->andWhere('devis.tiers in (:clientProspects)')
            ->setParameter('clientProspects', array_values($clientProspects));

        $qb->andWhere('devis.exercice = :exercice')
            ->setParameter('exercice', $exercice);
        
        //Recherche mot clé
        if ($q !== '') {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('devis.code', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Stat
        if ($stat === 'open') {
            $status = 1;
            $qb->andWhere($qb->expr()->eq('devis.status', ':status'));
            $qb->setParameter(':status', $status);
        } elseif ($stat === 'won') {
            $status = 2;
            $qb->andWhere($qb->expr()->eq('devis.status', ':status'));
            $qb->setParameter(':status', $status);
        } elseif ($stat === 'lost') {
            $status = 3;
            $qb->andWhere($qb->expr()->eq('devis.status', ':status'));
            $qb->setParameter(':status', $status);
        }
        
        //Période
        if ($period !== 'all') {
            if ($startperiod !== '' && $endperiod !== '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('devis.creeLe >= :startperiod');
                    $qb->andWhere('devis.creeLe <= :endperiod');
                } elseif ($sort === 'datedevis') {
                    $qb->andWhere('devis.dateDevis >= :startperiod');
                    $qb->andWhere('devis.dateDevis <= :endperiod');
                } else {
                    $qb->andWhere('devis.finValidite >= :startperiod');
                    $qb->andWhere('devis.finValidite <= :endperiod');
                }


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
        }
        
        //Tri
        switch ($sort){
            case 'echeance':
                $qb->orderBy('devis.finValidite', $sortOrder);
                break;
            case 'datecreation':
                $qb->orderBy('devis.creeLe', $sortOrder);
                break;
            case 'datedevis':
                $qb->orderBy('devis.dateDevis', $sortOrder);
                break;
            case 'clientprospect':
                $qb->orderBy('tiers.intitule',  $sortOrder);
                break;
            case 'status':
                $qb->orderBy('devis.status', $sortOrder)
                    ->addOrderBy('devis.finValidite', $sortOrder);
                break;
            case 'montant':
                $qb->orderBy('devis.montant', $sortOrder);
                break;
            default:
                $qb->orderBy('devis.finValidite', $sortOrder);
                break;
        }

        return $qb->getQuery()
                ->getResult();
    }

    /**
     * @param Tiers $clientProspect
     * @param $exercice
     * @param $type
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param string $startperiod
     * @param string $endperiod
     * @return array
     */
    public function getDevisByClientProspect(Tiers $clientProspect,  $exercice, $type, $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('devis');
        $qb->where('devis.tiers = :cpid')
                ->setParameter('cpid', $clientProspect->getId());

        if($exercice !== null) {
            $qb->andWhere('devis.exercice = :exercice')
                ->setParameter('exercice', $exercice);
        }
        
        //Recherche mot clé
        if ($q !== '' && ($type === 'all' || $type === 'devis')) {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('devis.code', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Période
        if ($period !== 'all' && ($type === 'all' || $type === 'devis')) {
            if ($startperiod !== '' && $endperiod !== '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('devis.creeLe >= :startperiod');
                    $qb->andWhere('devis.creeLe <= :endperiod');
                } elseif ($sort === 'datedevis') {
                    $qb->andWhere('devis.dateDevis >= :startperiod');
                    $qb->andWhere('devis.dateDevis <= :endperiod');
                } else {
                    $qb->andWhere('devis.finValidite >= :startperiod');
                    $qb->andWhere('devis.finValidite <= :endperiod');
                }

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
        }
        
        //Tri
        switch ($sort){
            case 'echeance':
                $qb->orderBy('devis.finValidite', $sortOrder);
                break;
            case 'datecreation':
                $qb->orderBy('devis.creeLe', $sortOrder);
                break;
            case 'datedevis':
                $qb->orderBy('devis.dateDevis', $sortOrder);
                break;
            case 'status':
                $qb->orderBy('devis.status', $sortOrder)
                    ->addOrderBy('devis.finValidite', $sortOrder);
                break;
            case 'montant':
                $qb->orderBy('devis.montant', $sortOrder);
                break;
            default:
                $qb->orderBy('devis.finValidite', $sortOrder);
                break;

        }

        return $qb->getQuery()
                ->getResult();
    }
    
    /**
     * Récupère le dernier Devis
     * @return type
     */
    public function getLastCode() {
        try {
            $qb = $this->createQueryBuilder('devis');
            $qb->select('devis.code')
                    ->where($qb->expr()->like('devis.code', ':code'))
                    ->setParameter(':code', 'DEV-%')
                    ->setMaxResults(1)
                    ->orderBy('devis.id', 'DESC');
            $lastCode = $qb
                    ->getQuery()
                    ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            return 'DEV-000';
        }
        
    }

    /**
     * @param array $clientProspects
     * @param $exercice
     * @param $status
     * @return array
     */
    public function getDevisByClientProspects(array $clientProspects, $exercice, $status){
        return $this->createQueryBuilder('one_devis')
            ->where('one_devis.tiers IN (:clientProspects)')
            ->andWhere('one_devis.exercice = :exercice')
            ->setParameter('exercice', $exercice)
            ->andWhere('one_devis.status = :status')
            ->setParameter('status', $status)
            ->setParameter('clientProspects', array_values($clientProspects))
            ->getQuery()
            ->getResult();
    }
}