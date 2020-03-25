<?php

/**
 * Created by Netbeans
 * Created on : 6 juil. 2017, 12:00:25
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Tiers;
use Doctrine\ORM\EntityRepository;

class OneOpportuniteRepository extends EntityRepository
{
    /**
     * Récupération des opportunités
     * @return array
     */
    public function getOpportunites(array $clientProspects, $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='', $stat='all') {

        $qb = $this->createQueryBuilder('opportunite')
            ->innerJoin('opportunite.tiers', 'tiers')
            ->leftJoin('opportunite.oneStatusOpp', 'onestatusopp')
            ->leftJoin('opportunite.oneProbabilite', 'oneprobabilite');


        $qb->Where('opportunite.tiers IN (:clientProspects)')
            ->setParameter('clientProspects', array_values($clientProspects));

        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere('opportunite.nom like :q or opportunite.note like :q')
                ->setParameter('q', '%' . $q . '%');
        }
        
        //Stat
        if ($stat === 'open') {
            $statusopp = $this->getEntityManager()->getRepository('AppBundle:OneStatusOpp')->find(1);
            $qb->andWhere($qb->expr()->eq('opportunite.oneStatusOpp', ':statusopp'));
            $qb->setParameter(':statusopp', $statusopp);
        } elseif ($stat === 'waiting') {
            $statusopp = $this->getEntityManager()->getRepository('AppBundle:OneStatusOpp')->find(2);
            $qb->andWhere($qb->expr()->eq('opportunite.oneStatusOpp', ':statusopp'));
            $qb->setParameter(':statusopp', $statusopp);
        } elseif ($stat === 'won') {
            $statusopp = $this->getEntityManager()->getRepository('AppBundle:OneStatusOpp')->find(3);
            $qb->andWhere($qb->expr()->eq('opportunite.oneStatusOpp', ':statusopp'));
            $qb->setParameter(':statusopp', $statusopp);
        } elseif ($stat === 'lost') {
            $statusopp = $this->getEntityManager()->getRepository('AppBundle:OneStatusOpp')->find(4);
            $qb->andWhere($qb->expr()->eq('opportunite.oneStatusOpp', ':statusopp'));
            $qb->setParameter(':statusopp', $statusopp);
        }
        
        //Période
        if ($period !== 'all') {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('opportunite.creeLe >= :startperiod');
                    $qb->andWhere('opportunite.creeLe <= :endperiod');
                } else {
                    $qb->andWhere('opportunite.cloture >= :startperiod');
                    $qb->andWhere('opportunite.cloture <= :endperiod');
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
                $qb->orderBy('opportunite.cloture', $sortOrder);
                break;

            case 'datecreation':
                $qb->orderBy('opportunite.creeLe', $sortOrder);
                break;

            case 'opportunite':
                $qb->orderBy('opportunite.nom', $sortOrder);
                break;

            case 'clientprospect':
                $qb->orderBy('tiers.intitule', $sortOrder);
                break;

            case 'etape':
                $qb->orderBy('onestatusopp.ordre', $sortOrder);
                break;

            case 'revenu':
                $qb->orderBy('opportunite.montant', $sortOrder);
                break;

            case 'probabilite':
                $qb->orderBy('oneprobabilite.pourcentage', $sortOrder);
                break;

            default:
                $qb->orderBy('opportunite.creeLe', $sortOrder);
                break;
        }

        return $qb->getQuery()
                ->getResult();
    }
    
    /**
     * Récupération  des opportunités d'un client/prospect
     * @param Tiers $clientProspect
     * @return array
     */
    public function getOpportunitesByProspect(Tiers $clientProspect, $type , $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('opportunite')
            ->innerJoin('opportunite.tiers', 'tiers')
            ->leftJoin('opportunite.oneStatusOpp', 'onestatusopp')
            ->leftJoin('opportunite.oneProbabilite', 'oneprobabilite');

        $qb->where('opportunite.tiers = :cpid')
                ->setParameter('cpid', $clientProspect->getId());
        
        //Recherche mot clé
        if ($q != '' && ($type === 'all' || $type === 'opportunite')) {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('opportunite.nom', ':q'),
                    $qb->expr()->like('opportunite.note', ':q')
                    ))
                    ->setParameter('q', '%'.$q.'%');
        }
        
        //Période
        if ($period !== 'all' && ($type === 'all' || $type === 'opportunite')) {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('opportunite.creeLe >= :startperiod');
                    $qb->andWhere('opportunite.creeLe <= :endperiod');
                } else {
                    $qb->andWhere('opportunite.cloture >= :startperiod');
                    $qb->andWhere('opportunite.cloture <= :endperiod');
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
                $qb->orderBy('opportunite.cloture', $sortOrder);
                break;

            case 'datecreation':
                $qb->orderBy('opportunite.creeLe', $sortOrder);
                break;

            case 'opportunite':
                $qb->orderBy('opportunite.nom', $sortOrder);
                break;

            case 'clientprospect':
                $qb->orderBy('tiers.intitule', $sortOrder);
                break;

            case 'etape':
                $qb->orderBy('onestatusopp.ordre', $sortOrder);
                break;

            case 'revenu':
                $qb->orderBy('opportunite.montant', $sortOrder);
                break;

            case 'probabilite':
                $qb->orderBy('oneprobabilite.pourcentage', $sortOrder);
                break;

            default:
                $qb->orderBy('opportunite.creeLe', $sortOrder);
                break;
        }

        return $qb->getQuery()
                ->getResult();
    }



    public function getOpportunitesByClientProspectListStatus(array $clientProspects, $status){
        return $this->createQueryBuilder('p')
            ->where('p.tiers in (:clientsProspects)')
            ->setParameter('clientsProspects', array_values($clientProspects))
            ->andWhere('p.oneStatusOpp = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }


    public function getNextStep($stepPosition)
    {
        $nextStepPosition = (int)$stepPosition + 1;
        return $this->getEntityManager()
            ->createQueryBuilder('status')
            ->where('status.order = :order')
            ->setParameter('order', $nextStepPosition)
            ->getQuery()
            ->getSingleScalarResult();
    }
}