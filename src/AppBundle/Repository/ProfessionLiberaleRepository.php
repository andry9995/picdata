<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 18/01/2017
 * Time: 14:29
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProfessionLiberaleRepository extends EntityRepository
{
    function getProfessionLiberaleByCat($professionLiberaleCat)
    {
        $qb= $this->getEntityManager()
            ->getRepository('AppBundle:ProfessionLiberale')
            ->createQueryBuilder('pl');

        $qb->where('pl.professionLiberaleCat = :professionLiberaleCat')
            ->setParameter('professionLiberaleCat',$professionLiberaleCat)
        ->orderBy('pl.alpha','ASC');

        return $qb->getQuery()->getResult();

    }
//
//    function getProfessionLiberaleListeAlphaByCat($professionLiberaleCat)
//    {
//        $qb= $this->getEntityManager()
//            ->getRepository('AppBundle:ProfessionLiberale')
//            ->createQueryBuilder('pl');
//
//        $qb->where('pl.professionLiberaleCat = :professionLiberaleCat')
//            ->setParameter('professionLiberaleCat',$professionLiberaleCat)
//            ->select('pl.alpha')
//            ->distinct();
//
//        return $qb->getQuery()->getResult();
//    }
//
//    function getProfessionLiberaleByAlpha($professionLiberaleCat,$alpha)
//    {
//        $qb= $this->getEntityManager()
//            ->getRepository('AppBundle:ProfessionLiberale')
//            ->createQueryBuilder('pl');
//
//        $qb->where('pl.professionLiberaleCat = :professionLiberaleCat')
//            ->setParameter('professionLiberaleCat',$professionLiberaleCat)
//            ->andWhere('pl.alpha = :alpha')
//            ->setParameter('alpha',$alpha)
//        ;
//
//        return $qb->getQuery()->getResult();
//    }



}