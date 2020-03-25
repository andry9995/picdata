<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 23/02/2018
 * Time: 14:28
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Dossier;
use AppBundle\Entity\NdfSouscategorieDossier;
use Doctrine\ORM\EntityRepository;

class NdfCategorieRepository extends EntityRepository
{
    /**
     * @param $dossier Dossier
     * @return array
     */
    public function getNdfCategorieActifByDossier($dossier){


        $souscategoriesDossier = $this->getEntityManager()
            ->getRepository('AppBundle:NdfSouscategorieDossier')
            ->findBy(array('dossier' => $dossier, 'status' => 1));


        $categories = array();

        /** @var NdfSouscategorieDossier $souscategorieDossier */
        foreach ($souscategoriesDossier as $souscategorieDossier){

            $categorie = $souscategorieDossier->getNdfSouscategorie()->getNdfCategorie();

            if(!is_null($categorie)){
                if(!in_array($categorie, $categories)){
                    $categories[] = $categorie;
                }
            }
        }

        return $categories;
    }


    public function getNdfCategorieByDossier($dossier)
    {
        $souscategoriesDossier = $this->getEntityManager()
            ->getRepository('AppBundle:NdfSouscategorieDossier')
            ->findBy(array('dossier' => $dossier));


        $categories = array();

        /** @var NdfSouscategorieDossier $souscategorieDossier */
        foreach ($souscategoriesDossier as $souscategorieDossier) {

            $categorie = $souscategorieDossier->getNdfSouscategorie()->getNdfCategorie();

            if (!is_null($categorie)) {
                if (!in_array($categorie, $categories)) {
                    $categories[] = $categorie;
                }
            }
        }

        return $categories;
    }

}