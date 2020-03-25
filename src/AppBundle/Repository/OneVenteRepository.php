<?php

/**
 * Created by Netbeans
 * Created on : 15 août 2017, 17:40:10
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Tiers;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\OneProjet;

class OneVenteRepository extends EntityRepository
{
    /**
     * Récupération des ventes
     * @param array $clientProspects
     * @param $exercice
     * @param string $type
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param string $startperiod
     * @param string $endperiod
     * @param string $stat
     * @return array
     */
    public function getVentes(array $clientProspects, $exercice, $type='facture', $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='', $stat='all') {
        $qb = $this->createQueryBuilder('vente')
            ->leftJoin('vente.tiers', 'tiers');


        $qb->where('vente.tiers in (:clientProspects)')
            ->setParameter('clientProspects', array_values($clientProspects));

        $qb->andWhere('vente.exercice = :exercice')
            ->setParameter('exercice', $exercice);

        switch ($type){
            case 'facture':
                $qb->andWhere('vente.type = 2');
                break;
            case 'commande':
                $qb->andWhere('vente.type = 1');
                break;
            case 'avoir':
                $qb->andWhere('vente.type = 3');
                break;
            default:
                break;
        }

        
        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('vente.code', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Stat
        if ($stat === 'paid') {
            $status = 1;
            $qb->andWhere($qb->expr()->eq('vente.statusFacture', ':status'));
            $qb->setParameter(':status', $status);
        } elseif ($stat === 'unpaid') {
            $status = 0;
            $qb->andWhere($qb->expr()->eq('vente.statusFacture', ':status'));
            $qb->setParameter(':status', $status);
        }
        if ($type === 'commande') {
            if ($stat === 'uninvoiced') {
                $status = 0;
                $qb->andWhere($qb->expr()->eq('vente.statusBonCommande', ':status'));
                $qb->setParameter(':status', $status);
            } elseif ($stat === 'invoiced') {
                $status = 1;
                $qb->andWhere($qb->expr()->eq('vente.statusBonCommande', ':status'));
                $qb->setParameter(':status', $status);
            } elseif ($stat === 'unshipped') {
                $status = 1;
                $qb->andWhere($qb->expr()->eq('vente.statusBonCommande', ':status'));
                $qb->setParameter(':status', $status);
            } elseif ($stat === 'shipped') {
                $status = 2;
                $qb->andWhere($qb->expr()->eq('vente.statusBonCommande', ':status'));
                $qb->setParameter(':status', $status);
            }
        }
        
        //Période
        if ($period !== 'all') {

            $addPeriodParameter = false;

            if ($startperiod != '' && $endperiod != '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('vente.creeLe >= :startperiod');
                    $qb->andWhere('vente.creeLe <= :endperiod');
                    $addPeriodParameter = true;
                } elseif ($sort === 'datevente') {
                    $qb->andWhere('vente.dateFacture >= :startperiod');
                    $qb->andWhere('vente.dateFacture <= :endperiod');
                    $addPeriodParameter = true;
                } else {
                    if ($type === 'facture' || $type === 'avoir') {
                        $qb->andWhere('vente.dateFacture >= :startperiod');
                        $qb->andWhere('vente.dateFacture <= :endperiod');
                        $addPeriodParameter = true;
                    } elseif ($type === 'commande') {
                        $qb->andWhere('vente.dateExpedition >= :startperiod');
                        $qb->andWhere('vente.dateExpedition <= :endperiod');
                        $addPeriodParameter = true;
                    }
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

                if($addPeriodParameter) {
                    $qb->setParameter(':startperiod', $dateStartPeriode);
                    $qb->setParameter(':endperiod', $dateEndPeriode);
                }
            }
        }
        
        //Tri

        switch ($sort){
            case 'echeance':
                if($type === 'facture' || $type === 'avoir')
                    $qb->orderBy('vente.dateFacture', $sortOrder);
                elseif ($type === 'commande')
                    $qb->orderBy('vente.dateExpedition', $sortOrder);
                break;
            case 'datecreation':
                $qb->orderBy('vente.creeLe', $sortOrder);
                break;
            case 'datevente':
                $qb->orderBy('vente.dateFacture', $sortOrder);
            break;
            case 'clientprospect':
                $qb->orderBy('tiers.intitule', $sortOrder);
                break;
            default:
                if($type === 'facture' || $type === 'avoir')
                    $qb->orderBy('vente.dateFacture', $sortOrder);
                elseif ($type === 'commande')
                    $qb->orderBy('vente.dateExpedition', $sortOrder);

            break;

        }

        return $qb->getQuery()
                ->getResult();
    }

    /**
     * @param Tiers $clientProspect
     * @param $exercice
     * @param string $ventetype
     * @param string $type
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param string $startperiod
     * @param string $endperiod
     * @return array
     */
    public function getVentesByClient(Tiers $clientProspect,$exercice,$ventetype='facture', $type='facture', $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('vente')
            ->leftJoin('vente.tiers', 'tiers');

        switch ($ventetype){
            case 'facture':
                $qb->where('vente.type = 2');
                break;
            case 'commande':
                $qb->where('vente.type = 1');
                break;
            case 'avoir':
                $qb->where('vente.type = 3');
                break;

        }

        $qb->andWhere('vente.exercice = :exercice')
            ->setParameter('exercice', $exercice);
        
        $qb->andWhere('vente.tiers = :cpid')
                ->setParameter('cpid', $clientProspect->getId());
        
        //Recherche mot clé
        if ($q !== '' && ($type === 'all' || $type === 'facture' || $type === 'commande' || $type === 'avoir')) {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('vente.code', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Période
        if ($period !== 'all' && ($type === 'all' || $type === 'facture' || $type === 'commande' || $type === 'avoir')) {
            if ($startperiod != '' && $endperiod != '') {

                $allowDateParam = false;

                if ($sort === 'datecreation') {
                    $qb->andWhere('vente.creeLe >= :startperiod');
                    $qb->andWhere('vente.creeLe <= :endperiod');
                    $allowDateParam = true;
                } elseif ($sort === 'datevente') {
                    $qb->andWhere('vente.dateFacture >= :startperiod');
                    $qb->andWhere('vente.dateFacture <= :endperiod');
                    $allowDateParam = true;
                } else {
                    if ($ventetype === 'facture' || $ventetype === 'avoir') {
                        $qb->andWhere('vente.dateFacture >= :startperiod');
                        $qb->andWhere('vente.dateFacture <= :endperiod');
                        $allowDateParam = true;
                    } elseif ($type === 'commande') {
                        $qb->andWhere('vente.dateExpedition >= :startperiod');
                        $qb->andWhere('vente.dateExpedition <= :endperiod');
                        $allowDateParam = true;
                    }
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

                if($allowDateParam) {
                    $qb->setParameter(':startperiod', $dateStartPeriode);
                    $qb->setParameter(':endperiod', $dateEndPeriode);
                }

            }
        }
        
        //Tri
        switch ($sort) {
            case 'echeance':
                if ($type === 'facture' || $type === 'avoir')
                    $qb->orderBy('vente.dateFacture', $sortOrder);
                elseif ($type === 'commande')
                    $qb->orderBy('vente.dateExpedition', $sortOrder);
                break;
            case 'datecreation':
                $qb->orderBy('vente.creeLe', $sortOrder);
                break;
            case 'datevente':
                $qb->orderBy('vente.dateFacture', $sortOrder);
                break;
            case 'clientprospect':
                $qb->orderBy('tiers.intitule', $sortOrder);
                break;
            default:
                if ($type === 'facture' || $type === 'avoir')
                    $qb->orderBy('vente.dateFacture', $sortOrder);
                elseif ($type === 'commande')
                    $qb->orderBy('vente.dateExpedition', $sortOrder);

                break;
        }

        return $qb->getQuery()
                ->getResult();
    }
    
    public function getVentesByProjet(OneProjet $projet, $ventetype='facture', $type='facture', $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('vente');
        if ($ventetype == 'facture')
            $qb->where('vente.type = 2');
        if ($ventetype == 'commande')
            $qb->where('vente.type = 1');
        if ($ventetype == 'avoir')
            $qb->where('vente.type = 3');
        
        $qb->andWhere('vente.oneProjet = :pid')
                ->setParameter('pid', $projet->getId());
        
        //Recherche mot clé
        if ($q != '' && ($type == 'all' || $type == 'facture' || $type == 'commande' || $type == 'avoir')) {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('vente.code', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Période
        if ($period != 'all' && ($type == 'all' || $type == 'facture' || $type == 'commande' || $type == 'avoir')) {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort == 'datecreation') {
                    $qb->andWhere($qb->expr()->gte('CAST(vente.creeLe AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(vente.creeLe AS DATE)', ':endperiod'));
                } elseif ($sort == 'datevente') {
                    $qb->andWhere($qb->expr()->gte('CAST(vente.dateFacture AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(vente.dateFacture AS DATE)', ':endperiod'));
                } else {
                    if ($ventetype == 'facture' || $ventetype == 'avoir') {
                        $qb->andWhere($qb->expr()->gte('CAST(vente.dateFacture AS DATE)', ':startperiod'));
                        $qb->andWhere($qb->expr()->lte('CAST(vente.dateFacture AS DATE)', ':endperiod'));
                    } elseif ($type == 'commande') {
                        $qb->andWhere($qb->expr()->gte('CAST(vente.dateExpedition AS DATE)', ':startperiod'));
                        $qb->andWhere($qb->expr()->lte('CAST(vente.dateExpedition AS DATE)', ':endperiod'));
                    }
                }
                $qb->setParameter(':startperiod', \DateTime::createFromFormat('j/m/Y', $startperiod)->format('Y-m-d'));
                $qb->setParameter(':endperiod', \DateTime::createFromFormat('j/m/Y', $endperiod)->format('Y-m-d'));
            }
        }
        
        //Tri
        if ($sort == 'echeance') {
            if ($ventetype == 'facture' || $ventetype == 'avoir')
                $qb->orderBy('vente.dateFacture', $sortOrder);
            elseif ($type == 'commande')
                $qb->orderBy('vente.dateExpedition', $sortOrder);
        } elseif ($sort == 'datecreation') {
            $qb->orderBy('vente.creeLe', $sortOrder);
        } elseif ($sort == 'datevente') {
            $qb->orderBy('vente.dateFacture', $sortOrder);
        } elseif ($sort == '') {
            if ($ventetype == 'facture' || $ventetype == 'avoir')
                $qb->orderBy('vente.dateFacture', $sortOrder);
            elseif ($type == 'commande')
                $qb->orderBy('vente.dateExpedition', $sortOrder);
        }
        
        $ventes = $qb->getQuery()
                ->getResult();
        
        return $ventes;
    }

    /**
     * @param array $clientProspects
     * @param $exercice
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param string $startperiod
     * @param string $endperiod
     * @return array
     */
    public function getVentesForPaiement(array $clientProspects, $exercice, $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('vente');
        $qb->where('vente.type = 2');
        $qb->andWhere('vente.statusFacture = 0');

        $qb->andWhere('vente.exercice = :exercice')
            ->setParameter('exercice', $exercice);

        $qb->andWhere('vente.tiers in (:clientProspects)')
            ->setParameter('clientProspects', array_values($clientProspects));

        //Recherche mot clé
        if ($q !== '') {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('vente.code', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Période
        if ($period !== 'all') {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('vente.creeLe >= :startperiod');
                    $qb->andWhere('vente.creeLe <= :endperiod');
                } elseif ($sort === 'datevente') {
                    $qb->andWhere('vente.dateFacture >= :startperiod');
                    $qb->andWhere('vente.dateFacture <= :endperiod');
                } else {
                    $qb->andWhere('vente.dateFacture >= :startperiod');
                    $qb->andWhere('vente.dateFacture <= :endperiod');
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
            $qb->orderBy('vente.dateFacture', $sortOrder);
        } elseif ($sort === 'datecreation') {
            $qb->orderBy('vente.creeLe', $sortOrder);
        } elseif ($sort === 'datevente') {
            $qb->orderBy('vente.dateFacture', $sortOrder);
        } elseif ($sort === '') {
            $qb->orderBy('vente.dateFacture', $sortOrder);
        }

        return $qb->getQuery()
                ->getResult();
    }
    
    public function getAvoirsPaiement(Tiers $clientProspect, $exercice, $excludeIds) {
        $qb = $this->createQueryBuilder('avo');
        $qb->where('avo.type = 3');
        $qb->andWhere('avo.tiers = :cpid')
                ->setParameter('cpid', $clientProspect->getId());
        $qb->andWhere('avo.exercice = :exercice')
            ->setParameter('exercice', $exercice);
        
        //Exclue les avoirs déjà choisis
        if($excludeIds !== null && $excludeIds !== '') {
            $qb->andWhere($qb->expr()->notIn('avo.id', $excludeIds));
        }

        return $qb->getQuery()
                ->getResult();
    }

    /**
     * @param string $type
     * @return mixed|string
     */
    public function getLastCodeVente($type='facture') {
        try {
            if ($type === 'facture')
                $startcode = 'FAC-%';
            elseif ($type === 'commande')
                $startcode = 'COM-%';
                elseif ($type === 'avoir')
                $startcode = 'AVO-%';
            
            $qb = $this->createQueryBuilder('vente');
            $qb->select('vente.code')
                    ->where($qb->expr()->like('vente.code', ':code'))
                    ->setParameter(':code', $startcode)
                    ->setMaxResults(1)
                    ->orderBy('vente.id', 'DESC');
            $lastCode = $qb
                    ->getQuery()
                    ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            if ($type === 'facture')
                return 'FAC-000';
            elseif ($type === 'commande')
                return 'COM-000';
            elseif ($type === 'avoir')
                return 'AVO-000';
        }
    }

    /**
     * @param array $clientProspects
     * @param $exercice
     * @param string $type
     * @param string $stat
     * @return array
     */
    public function getVenteByStatus(array $clientProspects, $exercice, $type='facture', $stat='unpaid') {
        $qb = $this->createQueryBuilder('vente');

        $qb->where('vente.tiers in (:clientProspects)')
            ->setParameter('clientProspects', array_values($clientProspects));

        $qb->andWhere('vente.exercice = :exercice')
            ->setParameter('exercice', $exercice);

        switch ($type){
            case 'facture':
                $qb->andWhere('vente.type = 2');
                break;
            case 'commande':
                $qb->andWhere('vente.type = 1');
                break;
            case 'avoir':
                $qb->andWhere('vente.type = 3');
                break;
            default:
                break;
        }

        
        //Stat
        if ($stat === 'paid') {
            $status = 1;
            $qb->andWhere($qb->expr()->eq('vente.statusFacture', ':status'));
            $qb->setParameter(':status', $status);
        } elseif ($stat === 'unpaid') {
            $status = 0;
            $qb->andWhere($qb->expr()->eq('vente.statusFacture', ':status'));
            $qb->setParameter(':status', $status);
        }

        if ($type === 'commande') {
            if ($stat === 'uninvoiced') {
                $status = 0;
                $qb->andWhere($qb->expr()->eq('vente.statusBonCommande', ':status'));
                $qb->setParameter(':status', $status);
            } elseif ($stat === 'invoiced') {
                $status = 1;
                $qb->andWhere($qb->expr()->eq('vente.statusBonCommande', ':status'));
                $qb->setParameter(':status', $status);
            } elseif ($stat === 'unshipped') {
                $status = 1;
                $qb->andWhere($qb->expr()->eq('vente.statusBonCommande', ':status'));
                $qb->setParameter(':status', $status);
            } elseif ($stat === 'shipped') {
                $status = 2;
                $qb->andWhere($qb->expr()->eq('vente.statusBonCommande', ':status'));
                $qb->setParameter(':status', $status);
            }
        }

        return $qb->getQuery()
                ->getResult();
    }

    /**
     * @param $cid
     * @param $exercice
     * @param $date
     * @return array|bool
     */
    public function getNextByDate($cid, $exercice, $date) {
        try {
            $qb = $this->createQueryBuilder('vente');
            $qb->where($qb->expr()->eq('vente.tiers', ':cid'))
                ->andWhere('vente.exercice = :exercice')
                ->andWhere($qb->expr()->gte('vente.creeLe', ':date'))
                ->andWhere($qb->expr()->eq('vente.type', 2))
                ->setParameter('exercice', $exercice)
                ->setParameter(':cid', $cid)
                ->setParameter(':date', $date)
                ->orderBy('vente.id');
            return $qb
                ->getQuery()
                ->getResult();

        } catch (\Doctrine\ORM\NoResultException $ex) {
            return false;
        }
    }
}