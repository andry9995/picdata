<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 07/11/2019
 * Time: 12:00
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveJson;
use Doctrine\ORM\EntityRepository;

class ReleveJsonRepository extends EntityRepository
{
    /**
     * @param Releve $releve
     * @return ReleveJson
     */
    public function getByReleve(Releve $releve)
    {
        return $this->createQueryBuilder('rj')
            ->where('rj.releve = :releve')
            ->setParameter('releve',$releve)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}