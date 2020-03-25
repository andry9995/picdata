<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 12/07/2016
 * Time: 16:55
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\IndIndicateur;

class TypeGrapheRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getAll()
    {
        return $this->createQueryBuilder('tg')
            ->orderBy('tg.libelle')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getByIds($ids = array())
    {
        return $this->createQueryBuilder('tg')
            ->where('tg.id IN (:ids)')
            ->setParameter('ids',$ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getByCode($code)
    {
        return $this->createQueryBuilder('t')
            ->where('t.code = :code')
            ->setParameter('code',$code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function getArrayGraphes()
    {
        return array(
            'COURBE'=>0,
            'HISTO'=>1,
            'LINE'=>2,
            'VAL'=>3,
            'CAME'=>4,
            'TAB'=>5
        );
    }
}