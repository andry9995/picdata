<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 18/02/2020
 * Time: 11:13
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Releve;
use Doctrine\ORM\EntityRepository;

class ReleveExtRepository extends EntityRepository
{
    /**
     * @param Releve $releve
     * @return bool
     */
    public function isFlaguer(Releve $releve)
    {
        $releveExtFlaguer = $this->createQueryBuilder('re')
            ->where('re.releve = :releve')
            ->andWhere('re.imageFlague IS NOT NULL')
            ->setParameter('releve', $releve)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $releveExtFlaguer ? true : false;
    }
}