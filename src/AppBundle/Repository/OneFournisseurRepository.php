<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 03/04/2018
 * Time: 17:34
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class OneFournisseurRepository extends EntityRepository
{
    /**
     * Récupération de tous les comptes
     * @return array
     */
    public function getAccounts(Dossier $dossier) {
        $qb = $this->createQueryBuilder('fournisseur')
            ->where('fournisseur.dossier = :dossier')
            ->setParameter('dossier', $dossier);

        $qb->orderBy('fournisseur.creeLe', 'DESC');

        return $qb->getQuery()
            ->getResult();
    }




    /**
     * @param Dossier $dossier
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param string $startperiod
     * @param string $endperiod
     * @return array
     */
    public function getFournisseurs(Dossier $dossier, $sort='name', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('fournisseur');

        $qb->where('fournisseur.dossier = :dossier')
            ->setParameter('dossier', $dossier);

        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('fournisseur.email', ':q'),
                $qb->expr()->like('fournisseur.siteWeb', ':q'),
                $qb->expr()->like('fournisseur.numeroFournisseur', ':q'),
                $qb->expr()->like('fournisseur.nom', ':q')
            ))
                ->setParameter(':q', '%'.$q.'%');
        }

        //Période
        if ($period !== 'all') {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('fournisseur.creeLe  >= :startperiod');
                    $qb->andWhere('fournisseur.creeLe <= :endperiod');

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
        }

        //Tri
        if ($sort === 'name') {
            $qb->orderBy('fournisseur.nom', $sortOrder);
        } elseif ($sort === 'datecreation') {
            $qb->orderBy('fournisseur.creeLe', $sortOrder);
        } elseif ($sort === 'codefournisseur') {
            $qb->orderBy('fournisseur.numeroFournisseur', $sortOrder);
        } elseif ($sort == '') {
            $qb->orderBy('fournisseur.nom', $sortOrder);
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Récupère le dernier Fournisseur
     * @return string
     */
    public function getLastCode(Dossier $dossier) {
        try {
            $qb = $this->createQueryBuilder('fournisseur');
            $qb->select('fournisseur.numeroFournisseur')
                ->where($qb->expr()->like('fournisseur.numeroFournisseur', ':fou'))
                ->andWhere('fournisseur.dossier = :dossier')
                ->setParameter(':fou', 'FOU-%')
                ->setParameter('dossier', $dossier)
                ->setMaxResults(1)
                ->orderBy('fournisseur.id', 'DESC');
            $lastCode = $qb
                ->getQuery()
                ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            return 'FOU-000';
        }
    }

    public function getLastCustomCode(Dossier $dossier, $prefixe) {
        try {
            $qb = $this->createQueryBuilder('fournisseur');
            $qb->select('fournisseur.numeroFournisseur')
                ->where($qb->expr()->like('fournisseur.numeroFournisseur', ':fou'))
                ->setParameter(':fou', $prefixe.'%')
                ->andWhere('fournisseur.dossier = :dossier')
                ->setParameter('dossier', $dossier)
                ->setMaxResults(1)
                ->orderBy('fournisseur.id', 'DESC');
            $lastCode = $qb
                ->getQuery()
                ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            return $prefixe.'000';
        }
    }


}