<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 13/01/2020
 * Time: 11:30
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Dossier;
use AppBundle\Entity\Souscategorie;
use AppBundle\Entity\SouscategoriePasSaisir;
use Doctrine\ORM\EntityRepository;

class SouscategoriePasSaisirRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     */
    public function initialise(Dossier $dossier)
    {
        $sousCategoriePasSaisirs = $this->createQueryBuilder('scps')
            ->where('scps.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$sousCategoriePasSaisirs)
        {
            $em = $this->getEntityManager();
            $sousCategories = $this->getEntityManager()->getRepository('AppBundle:Souscategorie')
                ->getObs();

            $sousCategorie = $this->getEntityManager()->getRepository('AppBundle:Souscategorie')
                ->find(771);

            $sousCategoriePasSaisir = new SouscategoriePasSaisir();
            $sousCategoriePasSaisir
                ->setDossier($dossier)
                ->setSouscategorie($sousCategorie);
            $em->persist($sousCategoriePasSaisir);

            $idsASaisirs = [1];
            foreach ($sousCategories as $souscategorie)
            {
                if (!in_array($souscategorie->getId(),$idsASaisirs))
                {
                    $sousCategoriePasSaisir = new SouscategoriePasSaisir();
                    $sousCategoriePasSaisir
                        ->setDossier($dossier)
                        ->setSouscategorie($souscategorie);

                    $em->persist($sousCategoriePasSaisir);
                }
            }

            $em->flush();
        }
    }

    /**
     * @param Dossier $dossier
     * @param Souscategorie $souscategorie
     * @return bool
     */
    public function aSaisir(Dossier $dossier, Souscategorie $souscategorie)
    {
        $sousCategoriePasSaisir = $this->getSousCategoriePasSaisir($dossier,$souscategorie);
        return !$sousCategoriePasSaisir;
    }

    /**
     * @param Dossier $dossier
     * @param Souscategorie $souscategorie
     * @return SouscategoriePasSaisir
     */
    public function getSousCategoriePasSaisir(Dossier $dossier, Souscategorie $souscategorie)
    {
        return $this->createQueryBuilder('scps')
            ->where('scps.dossier = :dossier')
            ->andWhere('scps.souscategorie = :sousCategorie')
            ->setParameters([
                'dossier' => $dossier,
                'sousCategorie' => $souscategorie
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Dossier $dossier
     * @param bool $returnSousCategorie
     * @return array
     */
    public function getForDossier(Dossier $dossier, $returnSousCategorie = false)
    {
        /** @var SouscategoriePasSaisir[] $sousCategoriePasSaisir */
        $sousCategoriePasSaisir = $this->createQueryBuilder('scps')
            ->where('scps.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->getQuery()
            ->getResult();

        if (count($sousCategoriePasSaisir) == 0)
        {
            $this->initialise($dossier);

            $sousCategoriePasSaisir = $this->createQueryBuilder('scps')
                ->where('scps.dossier = :dossier')
                ->setParameter('dossier',$dossier)
                ->getQuery()
                ->getResult();
        }

        if (!$returnSousCategorie)
            return $sousCategoriePasSaisir;

        /** @var Souscategorie[] $sousCategories */
        $sousCategories = [];

        foreach ($sousCategoriePasSaisir as $item)
            $sousCategories[] = $item->getSouscategorie();

        return $sousCategories;
    }
}