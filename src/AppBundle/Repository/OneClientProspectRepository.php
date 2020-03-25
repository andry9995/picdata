<?php

/**
 * Created by Netbeans
 * Created on : 25 juin 2017, 13:44:24
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class OneClientProspectRepository extends EntityRepository
{
    /**
     * Récupère les prospects
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param date|string $startperiod
     * @param date|string $endperiod
     * @param Dossier|null $dossier
     * @return array
     */
    public function getProspects(Dossier $dossier = null, $sort='name', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('prospect');

        $qb->where('prospect.isProspect = 1');

        $qb->andWhere('prospect.dossier = :dossier')
            ->setParameter('dossier', $dossier);


        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('prospect.email', ':q'),
                    $qb->expr()->like('prospect.siteWeb', ':q'),
                    $qb->expr()->like('prospect.numeroClient', ':q'),
                    $qb->expr()->like('prospect.note', ':q'),
                    $qb->expr()->like('prospect.nomVisible', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Période
        if ($period != 'all') {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort == 'datecreation') {
                    $qb->andWhere($qb->expr()->gte('CAST(prospect.creeLe AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(prospect.creeLe AS DATE)', ':endperiod'));
                }
                $qb->setParameter(':startperiod', \DateTime::createFromFormat('j/m/Y', $startperiod)->format('Y-m-d'));
                $qb->setParameter(':endperiod', \DateTime::createFromFormat('j/m/Y', $endperiod)->format('Y-m-d'));
            }
        }
        
        //Tri
        if ($sort == 'name') {
            $qb->orderBy('prospect.nomVisible', $sortOrder);
        } elseif ($sort == 'datecreation') {
            $qb->orderBy('prospect.creeLe', $sortOrder);
        } elseif ($sort == 'codeclient') {
            $qb->orderBy('prospect.numeroClient', $sortOrder);
        } elseif ($sort == '') {
            $qb->orderBy('prospect.nomVisible', $sortOrder);
        }
        
        $prospects = $qb->getQuery()
                ->getResult();
        return $prospects;
    }
    
    /**
     * Récupère les clients
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param date $startperiod
     * @param date $endperiod
     * @return array
     */
    public function getClients(Dossier $dossier, $sort='name', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('client');
        $qb->where('client.isProspect = 0');

        $qb->andWhere('client.dossier = :dossier')
            ->setParameter('dossier', $dossier);
        
        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('client.email', ':q'),
                    $qb->expr()->like('client.siteWeb', ':q'),
                    $qb->expr()->like('client.numeroClient', ':q'),
                    $qb->expr()->like('client.note', ':q'),
                    $qb->expr()->like('client.nomVisible', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Période
        if ($period != 'all') {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort == 'datecreation') {
                    $qb->andWhere('client.creeLe >= :startperiod');
                    $qb->andWhere('client.creeLe <= :endperiod');


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

//                    $qb->andWhere($qb->expr()->gte('CAST(client.creeLe AS DATE)', ':startperiod'));
//                    $qb->andWhere($qb->expr()->lte('CAST(client.creeLe AS DATE)', ':endperiod'));
                }
//                $qb->setParameter(':startperiod', \DateTime::createFromFormat('j/m/Y', $startperiod)->format('Y-m-d'));
//                $qb->setParameter(':endperiod', \DateTime::createFromFormat('j/m/Y', $endperiod)->format('Y-m-d'));
            }
        }
        
        //Tri
        if ($sort == 'name') {
            $qb->orderBy('client.nomVisible', $sortOrder);
        } elseif ($sort == 'datecreation') {
            $qb->orderBy('client.creeLe', $sortOrder);
        } elseif ($sort == 'codeclient') {
            $qb->orderBy('client.numeroClient', $sortOrder);
        } elseif ($sort == '') {
            $qb->orderBy('client.nomVisible', $sortOrder);
        }
        
        $clients = $qb->getQuery()
                ->getResult();
        return $clients;
    }
    
    /**
     * Récupération de tous les comptes
     * @return array
     */
    public function getAccounts(Dossier $dossier) {
        $qb = $this->createQueryBuilder('account')
            ->where('account.dossier = :dossier')
            ->setParameter('dossier', $dossier);
        
        $qb->orderBy('account.creeLe', 'DESC');
        
        $accounts = $qb->getQuery()
                ->getResult();
        return $accounts;
    }
    
    /**
     * Récupère le dernier ClientProspect
     * @return type
     */
    public function getLastCode(Dossier $dossier) {
        try {
            $qb = $this->createQueryBuilder('clientProspect');
            $qb->select('clientProspect.numeroClient')
                    ->where($qb->expr()->like('clientProspect.numeroClient', ':cli'))
                ->andWhere('clientProspect.dossier = :dossier')
                    ->setParameter(':cli', 'CLI-%')
                ->setParameter('dossier', $dossier)
                    ->setMaxResults(1)
                    ->orderBy('clientProspect.id', 'DESC');
            $lastCode = $qb
                    ->getQuery()
                    ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            return 'CLI-000';
        }
    }
    
    public function getLastCustomCode(Dossier $dossier, $prefixe) {
        try {
            $qb = $this->createQueryBuilder('clientProspect');
            $qb->select('clientProspect.numeroClient')
                    ->where($qb->expr()->like('clientProspect.numeroClient', ':cli'))
                    ->setParameter(':cli', $prefixe.'%')
                ->andWhere('clientProspect.dossier = :dossier')
                ->setParameter('dossier', $dossier)
                    ->setMaxResults(1)
                    ->orderBy('clientProspect.id', 'DESC');
            $lastCode = $qb
                    ->getQuery()
                    ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            return $prefixe.'000';
        }
    }
}