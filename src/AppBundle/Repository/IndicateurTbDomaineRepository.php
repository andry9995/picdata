<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 19/09/2019
 * Time: 11:30
 */

namespace AppBundle\Repository;


use AppBundle\Entity\IndicateurTbDomaine;
use Doctrine\ORM\EntityRepository;

class IndicateurTbDomaineRepository extends EntityRepository
{
    /**
     * @param int $affichage
     * @return IndicateurTbDomaine[]
     */
    public function getAll($affichage = 10)
    {
        $results = $this->createQueryBuilder('itd');

        if ($affichage != 10)
            $results = $results
                ->where('itd.affichage = :affichage')
                ->setParameter('affichage',$affichage);

        return $results
            ->orderBy('itd.affichage')
            ->addOrderBy('itd.nom')
            ->getQuery()
            ->getResult();
    }
}