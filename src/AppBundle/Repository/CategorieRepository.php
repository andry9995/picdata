<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 02/03/2017
 * Time: 13:50
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CategorieRepository extends EntityRepository
{
    /**
     * Liste categories à afficher dans parametre
     * Tableau images
     *
     * @return array
     */
    public function getForTableauImage()
    {
        $categories = $this->getEntityManager()
            ->getRepository('AppBundle:Categorie')
            ->createQueryBuilder('categorie')
            ->select('categorie')
            ->where('categorie.afficher = :afficher')
            ->setParameters(array(
                'afficher' => true
            ))
            ->orderBy('categorie.id')
            ->getQuery()
            ->getResult();
        return $categories;
    }

    /**
     * Liste des categories par codes
     * @param array $codes
     * @return array
     */
    public function getCategoriesByCodes(array $codes)
    {
        return $this->createQueryBuilder('categorie')
            ->where('categorie.code in (:codes)')
            ->andWhere('categorie.actif = 1')
            ->setParameter('codes', array_values($codes))
            ->getQuery()
            ->getResult();
    }

    public function getDefaultCategories()
    {
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:Categorie')
            ->createQueryBuilder('cat');
        $default_cats = $qb->select('cat')
            ->where($qb->expr()->in('cat.code', ['CODE_BANQUE', 'CODE_CLIENT', 'CODE_FRNS']))
            ->getQuery()
            ->getResult();
        return $default_cats;
    }

}