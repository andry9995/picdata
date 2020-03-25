<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 29/10/2018
 * Time: 16:07
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Categorie;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\LibelleType;
use Doctrine\ORM\EntityRepository;

class LibelleDossierRepository extends EntityRepository
{
    /**
     * @param Dossier|null $dossier
     * @param LibelleType $type
     * @return array
     */
    public function getLibelleDossierByType(Dossier $dossier = null, LibelleType $type)
    {
        if ($dossier !== null)
            return $this->createQueryBuilder('ld')
                ->where('ld.dossier = :dossier')
                ->andWhere('ld.libelleType = :libelletype')
                ->setParameter('dossier', $dossier)
                ->setParameter('libelletype', $type)
                ->orderBy('ld.rang')
                ->getQuery()
                ->getResult();

        return [];
    }

    public function getLibelleDossierByDossierCategorie(Dossier $dossier, Categorie $categorie){
        return $this->createQueryBuilder('libelleDossier')
            ->join('libelleDossier.libelleType', 'libelleType')
            ->where('libelleType.categorie = :categorie')
            ->andWhere('libelleDossier.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->setParameter('categorie', $categorie)
            ->orderBy('libelleDossier.rang')
            ->select('libelleDossier')
            ->getQuery()
            ->getResult();
    }
}