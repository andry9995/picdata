<?php

/**
 * Created by Netbeans
 * Created on : 12 juil. 2017, 20:53:25
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Tiers;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\OneClientProspect;
use AppBundle\Entity\OneOpportunite;
use AppBundle\Entity\OneProjet;

class OneTacheRepository extends EntityRepository
{
    /**
     * Récupération des taches
     * @return array
     */
    public function getTaches(array $clientProspects, $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='', $stat='all') {
        $qb = $this->createQueryBuilder('tache');

        $qb->innerJoin('tache.tiers', 'tiers');

        $qb->leftJoin('tache.opportunite', 'opportunite');

        $qb->where('tache.tiers in (:clientProspects)')
            ->setParameter('clientProspects', array_values($clientProspects));

        //Recherche mot clé
        if ($q !== '') {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('tache.sujet', ':q'),
                    $qb->expr()->like('tache.memo', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Stat
        if ($stat === 'done') {
            $status = 1;
            $qb->andWhere($qb->expr()->eq('tache.status', ':status'));
            $qb->setParameter(':status', $status);
        } elseif ($stat === 'todo') {
            $status = 0;
            $qb->andWhere($qb->expr()->eq('tache.status', ':status'));
            $qb->setParameter(':status', $status);
        }
        
        //Période
        if ($period !== 'all') {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort === 'datecreation') {

                    $qb->andWhere('tache.creeLe >= :startperiod');
                    $qb->andWhere('tache.creeLe <= :endperiod');

                } else {

                    $qb->andWhere('tache.echeance >= :startperiod');
                    $qb->andWhere('tache.echeance <= :endperiod');

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

        switch($sort){
            case 'echeance':
                $qb->orderBy('tache.echeance', $sortOrder);
                break;
            case 'datecreation':
                $qb->orderBy('tache.creeLe', $sortOrder);
                break;
            case 'opportunite':
                $qb->orderBy('opportunite.nom', $sortOrder);
                break;
            case 'clientprospect':
                $qb->orderBy('tiers.intitule', $sortOrder);
                break;
            case 'status':
                $qb->orderBy('tache.status', $sortOrder)
                    ->addOrderBy('tache.echeance', $sortOrder);
                break;
            default:
                $qb->orderBy('tache.echeance', $sortOrder);
                break;
        }


        return $qb
            ->select('tache')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Récupération des taches d'un client/prospect
     * @param Tiers $clientProspect
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @return array
     */
    public function getTachesByClientProspect(Tiers $clientProspect, $type, $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('tache');

        $qb->leftJoin('tache.tiers', 'tiers')
            ->leftJoin('tache.opportunite', 'opportunite');


        $qb->where('tache.tiers = :cpid')
                ->setParameter('cpid', $clientProspect->getId());
        
        //Recherche mot clé
        if ($q != '' && ($type === 'all' || $type === 'tache')) {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('tache.sujet', ':q'),
                    $qb->expr()->like('tache.memo', ':q')
                    ))
                    ->setParameter('q', '%'.$q.'%');
        }
        
        //Période
        if ($period !== 'all' && ($type === 'all' || $type === 'tache')) {
            if ($startperiod != '' && $endperiod != '') {

                if ($sort === 'datecreation') {
                    $qb->andWhere('tache.creeLe >= :startperiod');
                    $qb->andWhere('tache.creeLe <= :endperiod');
                } else {
                    $qb->andWhere('tache.echeance >= :startperiod');
                    $qb->andWhere('tache.echeance <= :endperiod');
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
        switch($sort){
            case 'echeance':
                $qb->orderBy('tache.echeance', $sortOrder);
                break;
            case 'datecreation':
                $qb->orderBy('tache.creeLe', $sortOrder);
                break;
            case 'opportunite':
                $qb->orderBy('opportunite.nom', $sortOrder);
                break;
            case 'clientprospect':
                $qb->orderBy('tiers.intitule', $sortOrder);
                break;
            case 'status':
                $qb->orderBy('tache.status', $sortOrder)
                    ->addOrderBy('tache.echeance', $sortOrder);
                break;
            default:
                $qb->orderBy('tache.echeance', $sortOrder);
                break;
        }

        return $qb->getQuery()
                ->getResult();
    }
    
    /**
     * Récupération des taches d'un projet
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
    public function getTachesByProjet(OneProjet $projet, $type, $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('tache');
        $qb->where('tache.oneProjet = :pid')
                ->setParameter('pid', $projet->getId());
        
        //Recherche mot clé
        if ($q != '' && ($type == 'all' || $type == 'tache')) {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('tache.sujet', ':q'),
                    $qb->expr()->like('tache.memo', ':q')
                    ))
                    ->setParameter('q', '%'.$q.'%');
        }
        
        //Période
        if ($period != 'all' && ($type == 'all' || $type == 'tache')) {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort == 'datecreation') {
                    $qb->andWhere($qb->expr()->gte('CAST(tache.creeLe AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(tache.creeLe AS DATE)', ':endperiod'));
                } else {
                    $qb->andWhere($qb->expr()->gte('CAST(tache.echeance AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(tache.echeance AS DATE)', ':endperiod'));
                }
                $qb->setParameter(':startperiod', \DateTime::createFromFormat('j/m/Y', $startperiod)->format('Y-m-d'));
                $qb->setParameter(':endperiod', \DateTime::createFromFormat('j/m/Y', $endperiod)->format('Y-m-d'));
            }
        }
        
        //Tri
        if ($sort == 'echeance') {
            $qb->orderBy('tache.echeance', $sortOrder);
        } elseif ($sort == 'datecreation') {
            $qb->orderBy('tache.creeLe', $sortOrder);
        } elseif ($sort == '') {
            $qb->orderBy('tache.echeance', $sortOrder);
        }
        
        $taches = $qb->getQuery()
                ->getResult();
        
        return $taches;
    }
    
    /**
     * Récupération des taches d'une opportunité
     * @param OneOpportunite $opportunite
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @return array
     */
    public function getTachesByOpportunite(OneOpportunite $opportunite, $type, $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('tache');
        $qb->where('tache.opportunite = :oid')
                ->setParameter('oid', $opportunite->getId());
        
        //Recherche mot clé
        if ($q != '' && ($type == 'all' || $type == 'tache')) {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('tache.sujet', ':q'),
                    $qb->expr()->like('tache.memo', ':q')
                    ))
                    ->setParameter('q', '%'.$q.'%');
        }
        
        //Période
        if ($period != 'all' && ($type == 'all' || $type == 'tache')) {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort == 'datecreation') {
                    $qb->andWhere($qb->expr()->gte('CAST(tache.creeLe AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(tache.creeLe AS DATE)', ':endperiod'));
                } else {
                    $qb->andWhere($qb->expr()->gte('CAST(tache.echeance AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(tache.echeance AS DATE)', ':endperiod'));
                }
                $qb->setParameter(':startperiod', \DateTime::createFromFormat('j/m/Y', $startperiod)->format('Y-m-d'));
                $qb->setParameter(':endperiod', \DateTime::createFromFormat('j/m/Y', $endperiod)->format('Y-m-d'));
            }
        }
        
        //Tri
        if ($sort == 'echeance') {
            $qb->orderBy('tache.echeance', $sortOrder);
        } elseif ($sort == 'datecreation') {
            $qb->orderBy('tache.creeLe', $sortOrder);
        } elseif ($sort == '') {
            $qb->orderBy('tache.echeance', $sortOrder);
        }
        
        $taches = $qb->getQuery()
                ->getResult();
        
        return $taches;
    }

    public function getTachesByClientProspectListStatus(array $clientProspects, $status){
        return $this->createQueryBuilder('t')
            ->where('t.tiers in (:clientsProspects)')
            ->setParameter('clientsProspects', array_values($clientProspects))
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
}