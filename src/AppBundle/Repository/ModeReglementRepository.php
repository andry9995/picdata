<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 23/10/2017
 * Time: 09:26
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;


class ModeReglementRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getListe()
    {
        return $this->createQueryBuilder('mp')
            ->orderBy('mp.rang')
            ->addOrderBy('mp.libelle')
            ->getQuery()
            ->getResult();
    }
}