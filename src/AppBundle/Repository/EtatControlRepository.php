<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 12/05/2017
 * Time: 15:49
 */

namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;


class EtatControlRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getEtatControls()
    {
        return $this->createQueryBuilder('ec')
            ->orderBy('ec.nom')
            ->getQuery()
            ->getResult();
    }
}