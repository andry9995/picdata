<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 29/10/2018
 * Time: 16:03
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Categorie;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\LibelleDossier;
use AppBundle\Entity\LibelleType;
use Doctrine\ORM\EntityRepository;

class LibelleModeleRepository extends EntityRepository
{
    public function getLibelleModeleByDossier(Dossier $dossier = null, LibelleType $libelleType){

        if($dossier === null)
           $libelleDossiers = [];

        else {

            //Jerena aloha raha efa manana misy
            $libelleDossiers = $this->getEntityManager()
                ->getRepository('AppBundle:LibelleDossier')
                ->getLibelleDossierByType($dossier, $libelleType);
        }

        $itemDossiers = [];
        if(count($libelleDossiers) > 0){
            /** @var LibelleDossier $libelleDossier */
            foreach ($libelleDossiers as $libelleDossier){
                $itemDossiers[] = $libelleDossier->getLibelleItem();
            }
        }

        $libelleModeles = $this->createQueryBuilder('lm')
            ->where('lm.libelleType = :libelletype')
            ->setParameter('libelletype', $libelleType);

        if(count($itemDossiers) > 0){
            $libelleModeles->andWhere('lm.libelleItem NOT IN (:item)')
                ->setParameter('item', $itemDossiers);
        }

        return $libelleModeles->orderBy('lm.rang')
            ->getQuery()
            ->getResult();

    }

    public function getLibelleModeleByCategorie(Categorie $categorie)
    {
        return $this->createQueryBuilder('libelleModele')
            ->join('libelleModele.libelleType', 'libelleType')
            ->where('libelleType.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->orderBy('libelleModele.rang')
            ->select('libelleModele')
            ->getQuery()
            ->getResult();
    }
}