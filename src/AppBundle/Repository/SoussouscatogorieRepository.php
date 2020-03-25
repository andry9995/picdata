<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 02/03/2017
 * Time: 14:01
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class SoussouscatogorieRepository extends EntityRepository
{
    public function getListeSoussouscategoriesByCategorie($categorie)
    {
        $soussouscategories = $this
            ->createQueryBuilder('ssc')
            ->innerJoin('ssc.souscategorie', 'souscategorie')
            ->addSelect('souscategorie')
            ->innerJoin('souscategorie.categorie', 'categorie')
            ->where('categorie.id = :categorie_id')
            ->setParameter('categorie_id', $categorie)
            ->getQuery()
            ->getResult();

        return $soussouscategories;
    }

    public function getListeSoussouscategorieBySouscategorie($souscategorie){
        $soussouscategories = $this
            ->createQueryBuilder('ssc')
            ->innerJoin('ssc.souscategorie','souscategorie')
            ->where('souscategorie.id = :souscategorie_id')
            ->setParameter('souscategorie_id', $souscategorie)
            ->getQuery()
            ->getResult();

        return $soussouscategories;
    }

}