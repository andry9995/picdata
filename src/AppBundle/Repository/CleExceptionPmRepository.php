<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 17/06/2019
 * Time: 15:27
 */

namespace AppBundle\Repository;


use AppBundle\Entity\CleDossier;
use AppBundle\Entity\CleExceptionPm;
use Doctrine\ORM\EntityRepository;

class CleExceptionPmRepository extends EntityRepository
{
    /**
     * @param CleDossier $cleDossier
     * @return CleExceptionPm
     */
    public function cleExceptionForCleDossier(CleDossier $cleDossier)
    {
        return $this->createQueryBuilder('ce')
            ->where('ce.cleDossier = :cleDossier')
            ->setParameter('cleDossier',$cleDossier)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}