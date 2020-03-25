<?php

/**
 * Created by Netbeans
 * Created on : 18 juil. 2017, 15:06:45
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Tiers;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\OneClientProspect;
use AppBundle\Entity\OneOpportunite;
use AppBundle\Entity\OneProjet;

class OneAppelTelephoniqueRepository extends EntityRepository
{
    /**
     * Récupération des apples téléphoniques
     * @param array $clientProspects
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param string $startperiod
     * @param string $endperiod
     * @param string $stat
     * @return array
     */
    public function getAppels(array $clientProspects, $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='', $stat='all') {
        $qb = $this->createQueryBuilder('appel');

        $qb->innerJoin('appel.tiers', 'tiers');


        $qb->where('appel.tiers in (:clientProspects)')
            ->setParameter('clientProspects', array_values($clientProspects));

        
        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('appel.sujet', ':q'),
                    $qb->expr()->like('appel.note', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Stat
        if ($stat === 'done') {
            $status = 1;
            $qb->andWhere($qb->expr()->eq('appel.status', ':status'));
            $qb->setParameter(':status', $status);
        } elseif ($stat === 'todo') {
            $status = 0;
            $qb->andWhere($qb->expr()->eq('appel.status', ':status'));
            $qb->setParameter(':status', $status);
        }
        
        //Période
        if ($period !== 'all') {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('appel.creeLe >= :startperiod');
                    $qb->andWhere('appel.creeLe <= :endperiod');
                } else {
                    $qb->andWhere('appel.echeance >= :startperiod');
                    $qb->andWhere('appel.echeance <= :endperiod');
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
        if ($sort === 'echeance') {
            $qb->orderBy('appel.echeance', $sortOrder);
        } elseif ($sort === 'datecreation') {
            $qb->orderBy('appel.creeLe', $sortOrder);
        } elseif ($sort === '') {
            $qb->orderBy('appel.echeance', $sortOrder);
        }

        switch ($sort){
            case 'echeance':
                $qb->orderBy('appel.echeance', $sortOrder);
                break;
            case 'datecreation':
                $qb->orderBy('appel.creeLe', $sortOrder);
                break;
            case 'clientprospect':
                $qb->orderBy('tiers.intitule', $sortOrder);
                break;
            case 'status':
                $qb->orderBy('appel.status', $sortOrder)
                    ->addOrderBy('appel.echeance', $sortOrder);
                break;
            default:
                $qb->orderBy('appel.echeance', $sortOrder);
                break;
        }

        return $qb->getQuery()
                ->getResult();
    }
    
    /**
     * Récupération des appels d'un client/prospect
     * @param Tiers $clientProspect
     * @param string $type
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @return array
     */
    public function getAppelsByClientProspect(Tiers $clientProspect, $type , $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('appel');
        $qb->where('appel.tiers = :cpid')
                ->setParameter('cpid', $clientProspect->getId());
        
        //Recherche mot clé
        if ($q != '' && ($type === 'all' || $type === 'appel')) {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('appel.sujet', ':q'),
                    $qb->expr()->like('appel.note', ':q')
                    ))
                    ->setParameter('q', '%'.$q.'%');
        }
        
        //Période
        if ($period !== 'all' && ($type === 'all' || $type === 'appel')) {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('appel.creeLe >= :startperiod');
                    $qb->andWhere('appel.creeLe <= :endperiod');
                } else {
                    $qb->andWhere('appel.echeance >= :startperiod');
                    $qb->andWhere('appel.echeance <= :endperiod');
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
                $qb->orderBy('appel.echeance', $sortOrder);
                break;
            case 'datecreation':
                $qb->orderBy('appel.creeLe', $sortOrder);
                break;
            case 'status':
                $qb->orderBy('appel.status', $sortOrder)
                    ->addOrderBy('appel.echeance', $sortOrder);
                break;
            default:
                $qb->orderBy('appel.echeance', $sortOrder);
                break;
        }

        return $qb->getQuery()
                ->getResult();
    }
    
    /**
     * Récupération des appels d'une opportunité
     * @param OneOpportunite $opportunite
     * @param string $type
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @return array
     */
    public function getAppelsByOpportunite(OneOpportunite $opportunite, $type , $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('appel');
        $qb->where('appel.opportunite = :oid')
                ->setParameter('oid', $opportunite->getId());
        
        //Recherche mot clé
        if ($q != '' && ($type == 'all' || $type == 'appel')) {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('appel.sujet', ':q'),
                    $qb->expr()->like('appel.note', ':q')
                    ))
                    ->setParameter('q', '%'.$q.'%');
        }
        
        //Période
        if ($period != 'all' && ($type == 'all' || $type == 'appel')) {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort == 'datecreation') {
                    $qb->andWhere($qb->expr()->gte('CAST(appel.creeLe AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(appel.creeLe AS DATE)', ':endperiod'));
                } else {
                    $qb->andWhere($qb->expr()->gte('CAST(appel.echeance AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(appel.echeance AS DATE)', ':endperiod'));
                }
                $qb->setParameter(':startperiod', \DateTime::createFromFormat('j/m/Y', $startperiod)->format('Y-m-d'));
                $qb->setParameter(':endperiod', \DateTime::createFromFormat('j/m/Y', $endperiod)->format('Y-m-d'));
            }
        }
        
        //Tri
        if ($sort == 'echeance') {
            $qb->orderBy('appel.echeance', $sortOrder);
        } elseif ($sort == 'datecreation') {
            $qb->orderBy('appel.creeLe', $sortOrder);
        } elseif ($sort == '') {
            $qb->orderBy('appel.echeance', $sortOrder);
        }
        
        $appels = $qb->getQuery()
                ->getResult();
        
        return $appels;
    }
    
    /**
     * Récupération des appels d'un projet
     * @param OneProjet $projet
     * @param type $type
     * @param type $sort
     * @param type $sortOrder
     * @param type $q
     * @param type $period
     * @param type $startperiod
     * @param type $endperiod
     * @return type
     */
    public function getAppelsByProjet(OneProjet $projet, $type , $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('appel');
        $qb->where('appel.oneProjet = :pid')
                ->setParameter('pid', $projet->getId());
        
        //Recherche mot clé
        if ($q != '' && ($type == 'all' || $type == 'appel')) {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('appel.sujet', ':q'),
                    $qb->expr()->like('appel.note', ':q')
                    ))
                    ->setParameter('q', '%'.$q.'%');
        }
        
        //Période
        if ($period != 'all' && ($type == 'all' || $type == 'appel')) {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort == 'datecreation') {
                    $qb->andWhere($qb->expr()->gte('CAST(appel.creeLe AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(appel.creeLe AS DATE)', ':endperiod'));
                } else {
                    $qb->andWhere($qb->expr()->gte('CAST(appel.echeance AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(appel.echeance AS DATE)', ':endperiod'));
                }
                $qb->setParameter(':startperiod', \DateTime::createFromFormat('j/m/Y', $startperiod)->format('Y-m-d'));
                $qb->setParameter(':endperiod', \DateTime::createFromFormat('j/m/Y', $endperiod)->format('Y-m-d'));
            }
        }
        
        //Tri
        if ($sort == 'echeance') {
            $qb->orderBy('appel.echeance', $sortOrder);
        } elseif ($sort == 'datecreation') {
            $qb->orderBy('appel.creeLe', $sortOrder);
        } elseif ($sort == '') {
            $qb->orderBy('appel.echeance', $sortOrder);
        }
        
        $appels = $qb->getQuery()
                ->getResult();
        
        return $appels;
    }


    public function getAppelsByClientProspectListStatus(array $clientProspects, $status){
        return $this->createQueryBuilder('at')
            ->where('at.tiers in (:clientsProspects)')
            ->setParameter('clientsProspects', array_values($clientProspects))
            ->andWhere('at.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
}