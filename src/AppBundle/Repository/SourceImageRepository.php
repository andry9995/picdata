<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 22/06/2016
 * Time: 08:30
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SourceImageRepository extends EntityRepository
{
    public function getBySource($source)
    {
        return $this->createQueryBuilder('s')
            ->where('s.source = :source')->setParameter('source',$source)
            ->getQuery()->getOneOrNullResult();
    }

    public function getById($id)
    {
        return $this->createQueryBuilder('s')
            ->where('s.id = :id')->setParameter('id',$id)
            ->getQuery()->getOneOrNullResult();
    }
}