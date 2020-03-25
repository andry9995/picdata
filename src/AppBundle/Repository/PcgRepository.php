<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/06/2016
 * Time: 08:32
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Pcg;
use Doctrine\ORM\EntityRepository;


class PcgRepository extends EntityRepository
{
    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getById($id)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Pcg $pcg
     * @return array
     */
    public function getOtherLikeCompte(Pcg $pcg)
    {
        return $this->createQueryBuilder('p')
            ->where('p.compte LIKE :compte')
            ->setParameter('compte',$pcg->getCompte().'%')
            ->andWhere('p <> :pcg')
            ->setParameter('pcg',$pcg)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $compte
     * @return Pcg
     */
    public function getByCompte($compte)
    {
        if (abs($compte) == 0) return null;
        return $this->createQueryBuilder('p')
            ->where('p.compte = :compte')
            ->setParameter('compte',$compte)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Pcg $pcg
     * @param string $type
     * @return object
     */
    public function getStdClass(Pcg $pcg,$type = '')
    {
        return (object)
        [
            'id' => Boost::boost($pcg->getId()),
            'c' => $pcg->getCompte(),
            'i' => $pcg->getIntitule(),
            't' => $type
        ];
    }
}