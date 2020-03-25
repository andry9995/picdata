<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 03/12/2019
 * Time: 08:50
 */

namespace AppBundle\Repository;


use AppBundle\Entity\VisudataVersion;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityRepository;

class VisudataVersionRepository extends EntityRepository
{
    /**
     * @return VisudataVersion
     */
    public function getLastVersion()
    {
        try
        {
            return $this->createQueryBuilder('vd')
                ->where('vd.status = :status')
                ->setParameter('status',1)
                ->setMaxResults(1)
                ->orderBy('vd.id','DESC')
                ->getQuery()
                ->getOneOrNullResult();
        }
        catch (TableNotFoundException $ex)
        {
            return null;
        }
    }
}