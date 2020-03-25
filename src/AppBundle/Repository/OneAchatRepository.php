<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 25/04/2018
 * Time: 14:17
 */

namespace AppBundle\Repository;


use AppBundle\Entity\OneFournisseur;
use Doctrine\ORM\EntityRepository;

class OneAchatRepository extends EntityRepository
{

    public function getAchats(array $fournisseurs, $type='facture', $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='', $stat='all')
    {
        $qb = $this->createQueryBuilder('achat');

        $qb->where('achat.oneFournisseur in (:fournisseurs)')
            ->setParameter('fournisseurs', array_values($fournisseurs));

        switch ($type) {
            case 'facture':
                $qb->andWhere('achat.type = 2');
                break;
            case 'commande':
                $qb->andWhere('achat.type = 1');
                break;
            default:
                break;
        }

        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('achat.code', ':q')
            ))
                ->setParameter(':q', '%' . $q . '%');
        }

        //Stat
        if ($stat === 'paid') {
            $status = 1;
            $qb->andWhere($qb->expr()->eq('achat.statusFacture', ':status'));
            $qb->setParameter(':status', $status);
        } elseif ($stat === 'unpaid') {
            $status = 0;
            $qb->andWhere($qb->expr()->eq('achat.statusFacture', ':status'));
            $qb->setParameter(':status', $status);
        }

        //Période
        if ($period !== 'all') {

            $addPeriodParameter = false;

            if ($startperiod != '' && $endperiod != '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('achat.creeLe >= :startperiod');
                    $qb->andWhere('achat.creeLe <= :endperiod');
                    $addPeriodParameter = true;
                } elseif ($sort === 'dateachat') {
                    $qb->andWhere('achat.dateFacture >= :startperiod');
                    $qb->andWhere('achat.dateFacture <= :endperiod');
                    $addPeriodParameter = true;

                } else {
                    if ($type === 'facture') {
                        $qb->andWhere('achat.dateFacture >= :startperiod');
                        $qb->andWhere('achat.dateFacture <= :endperiod');
                        $addPeriodParameter = true;

                    } elseif ($type === 'commande') {
                        $qb->andWhere('achat.dateLivraison >= :startperiod');
                        $qb->andWhere('achat.dateLivraison <= :endperiod');
                        $addPeriodParameter = true;
                    }
                }

                $dateStartArray = explode('/', $startperiod);
                $dateStartPeriode = null;
                if (count($dateStartArray) === 3) {
                    $dateStartPeriode = new \DateTime("$dateStartArray[2]-$dateStartArray[1]-$dateStartArray[0]");
                }

                $dateEndArray = explode('/', $endperiod);
                $dateEndPeriode = null;
                if (count($dateEndArray) === 3) {
                    $dateEndPeriode = new \DateTime("$dateEndArray[2]-$dateEndArray[1]-$dateEndArray[0]");
                }

                if ($addPeriodParameter) {
                    $qb->setParameter(':startperiod', $dateStartPeriode);
                    $qb->setParameter(':endperiod', $dateEndPeriode);
                }

            }
        }

        //Tri
        if ($sort === 'echeance') {
            if ($type === 'facture')
                $qb->orderBy('achat.dateFacture', $sortOrder);
            elseif ($type === 'commande')
                $qb->orderBy('achat.dateLivraison', $sortOrder);
        } elseif ($sort === 'datecreation') {
            $qb->orderBy('achat.creeLe', $sortOrder);
        } elseif ($sort === 'dateachat') {
            $qb->orderBy('achat.dateFacture', $sortOrder);
        } elseif ($sort == '') {
            if ($type === 'facture')
                $qb->orderBy('achat.dateFacture', $sortOrder);
            elseif ($type === 'commande')
                $qb->orderBy('achat.dateLivraison', $sortOrder);
        }


        return $qb->getQuery()
            ->getResult();
    }



    public function getAchatsByFournisseur(OneFournisseur $fournisseur, $achattype='facture', $type='facture', $sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='')
    {
        $qb = $this->createQueryBuilder('achat');

        switch ($achattype) {
            case 'facture':
                $qb->where('achat.type = 2');
                break;
            case 'commande':
                $qb->where('achat.type = 1');
                break;
        }

        $qb->andWhere('achat.oneFournisseur = :fid')
            ->setParameter('fid', $fournisseur->getId());

        //Recherche mot clé
        if ($q != '' && ($type === 'all' || $type === 'facture' || $type === 'commande')) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('achat.code', ':q')
            ))
                ->setParameter(':q', '%'.$q.'%');
        }

        //Période
        if ($period !== 'all' && ($type === 'all' || $type === 'facture' || $type === 'commande')) {
            if ($startperiod != '' && $endperiod != '') {

                $allowDateParam = false;

                if ($sort === 'datecreation') {
                    $qb->andWhere('achat.creeLe >= :startperiod');
                    $qb->andWhere('achat.creeLe <= :endperiod');
                    $allowDateParam = true;

                } elseif ($sort === 'dateachat') {
                    $qb->andWhere('achat.dateFacture >= :startperiod');
                    $qb->andWhere('achat.dateFacture <= :endperiod');
                    $allowDateParam = true;
                } else {
                    if ($achattype === 'facture') {
                        $qb->andWhere('achat.dateFacture >= :startperiod');
                        $qb->andWhere('achat.dateFacture <= :endperiod');
                        $allowDateParam = true;

                    } elseif ($type === 'commande') {
                        $qb->andWhere('achat.dateLivraison >= :startperiod');
                        $qb->andWhere('achat.dateLivraison <= :endperiod');
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
        if ($sort === 'echeance') {
            if ($achattype === 'facture')
                $qb->orderBy('achat.dateFacture', $sortOrder);
            elseif ($type === 'commande')
                $qb->orderBy('achat.dateLivraison', $sortOrder);
        } elseif ($sort === 'datecreation') {
            $qb->orderBy('achat.creeLe', $sortOrder);
        } elseif ($sort === 'dateachat') {
            $qb->orderBy('achat.dateFacture', $sortOrder);
        } elseif ($sort == '') {
            if ($achattype === 'facture')
                $qb->orderBy('achat.dateFacture', $sortOrder);
            elseif ($type === 'commande')
                $qb->orderBy('achat.dateLivraison', $sortOrder);
        }

        $achats = $qb->getQuery()
            ->getResult();

        return $achats;
    }

    /**
     * @param string $type
     * @return mixed|string
     */
    public function getLastCodeAchat($type='facture') {
        try {
            $startcode = '';

            if ($type === 'facture')
                $startcode = 'FAC-%';
            elseif ($type === 'commande')
                $startcode = 'COM-%';

            $qb = $this->createQueryBuilder('achat');
            $qb->select('achat.code')
                ->where($qb->expr()->like('achat.code', ':code'))
                ->setParameter(':code', $startcode)
                ->setMaxResults(1)
                ->orderBy('achat.id', 'DESC');
            $lastCode = $qb
                ->getQuery()
                ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            if ($type === 'facture')
                return 'FAC-000';
            elseif ($type === 'commande')
                return 'COM-000';

            return 'AVO-000';
        }
    }


    public function getAchatByStatus(array $fournisseurs, $type='facture', $stat='unpaid') {
        $qb = $this->createQueryBuilder('achat');

        $qb->where('achat.oneFournisseur in (:fournisseurs)')
            ->setParameter('fournisseurs', array_values($fournisseurs));

        switch ($type){
            case 'facture':
                $qb->andWhere('achat.type = 2');
                break;

            case 'commande':
                $qb->andWhere('achat.type = 1');
                break;
            default:
                break;
        }


        //Stat
        if ($stat === 'paid') {
            $status = 1;
            $qb->andWhere($qb->expr()->eq('achat.statusFacture', ':status'));
            $qb->setParameter(':status', $status);
        } elseif ($stat === 'unpaid') {
            $status = 0;
            $qb->andWhere($qb->expr()->eq('achat.statusFacture', ':status'));
            $qb->setParameter(':status', $status);
        }


        return $qb->getQuery()
            ->getResult();
    }

}