<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 25/04/2019
 * Time: 08:59
 */

namespace AppBundle\Repository;

use AppBundle\Entity\IndicateurTbCle;
use Doctrine\ORM\EntityRepository;

class IndicateurTbCleRepository extends EntityRepository
{
    /**
     * @return IndicateurTbCle[]
     */
    public function getAll()
    {
        return $this->createQueryBuilder('itb')
            ->orderBy('itb.cle')
            ->getQuery()
            ->getResult();
    }
}