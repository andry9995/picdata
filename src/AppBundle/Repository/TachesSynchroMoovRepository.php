<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 17/01/2019
 * Time: 11:38
 */

namespace AppBundle\Repository;


use AppBundle\Entity\TachesSynchro;
use AppBundle\Entity\TachesSynchroMoov;
use Doctrine\ORM\EntityRepository;

class TachesSynchroMoovRepository extends EntityRepository
{
    /**
     * @param TachesSynchro $tachesSynchro
     */
    public function removeAllItem(TachesSynchro $tachesSynchro)
    {
        /** @var TachesSynchroMoov[] $items */
        $items = $this->createQueryBuilder('tsm')
            ->where('tsm.tachesSynchro = :tachesSynchro')
            ->setParameter('tachesSynchro',$tachesSynchro)
            ->getQuery()
            ->getResult();

        $em = $this->getEntityManager();
        foreach ($items as $item) $em->remove($item);
        $em->flush();
    }

    /**
     * @param TachesSynchro $tachesSynchro
     * @return TachesSynchroMoov
     */
    public function getLastMoov(TachesSynchro $tachesSynchro)
    {
        return $this->createQueryBuilder('tsm')
            ->where('tsm.tachesSynchro = :tachesSynchro')
            ->setParameter('tachesSynchro',$tachesSynchro)
            ->orderBy('tsm.id','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}