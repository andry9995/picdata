<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 09/12/2016
 * Time: 09:23
 */

namespace AppBundle\Repository;

use AppBundle\Entity\FactAnnee;
use Doctrine\ORM\EntityRepository;

class FactIndiceRepository extends EntityRepository
{
    function getAllIndice() {
        $indices = $this->getEntityManager()
            ->getRepository('AppBundle:FactIndice')
            ->createQueryBuilder('fi')
            ->select('fi')
            ->orderBy('fi.code')
            ->getQuery()
            ->getResult();
        return $indices;
    }

    /**
     * @param FactAnnee $annee
     * @return mixed
     */
    function getIndiceByAnnee(FactAnnee $annee)
    {
        $indice = $this->getEntityManager()
            ->getRepository('AppBundle:FactIndice')
            ->createQueryBuilder('fi')
            ->select('fi')
            ->where("DATE_FORMAT(fi.dateIndice, '%Y') = :annee")
            ->setParameter('annee', $annee->getAnnee())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (!$indice) {
            $indice = $this->getEntityManager()
                ->getRepository('AppBundle:FactIndice')
                ->createQueryBuilder('fi')
                ->select('fi')
                ->orderBy('fi.dateIndice', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $indice;
    }


}