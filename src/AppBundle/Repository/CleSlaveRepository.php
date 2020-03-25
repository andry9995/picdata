<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/08/2019
 * Time: 14:56
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Cle;
use AppBundle\Entity\CleSlave;
use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class CleSlaveRepository extends EntityRepository
{
    /**
     * @param Cle $cleSlave
     * @param Dossier $dossier
     * @return CleSlave[]
     */
    public function getAllMasters(Cle $cleSlave, Dossier $dossier)
    {
        return $this->createQueryBuilder('cs')
            ->where('cs.cle <> :cle')
            ->andWhere('cs.cleSlave = :cle')
            ->andWhere('cs.dossier = :dossier')
            ->setParameters([
                'cle' => $cleSlave,
                'dossier' => $dossier
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Cle $cleSlave
     * @param Dossier $dossier
     * @return array
     */
    public function getIdsAllMasters(Cle $cleSlave, Dossier $dossier)
    {
        $ids = [];
        $clesMasters = $this->getAllMasters($cleSlave,$dossier);

        foreach ($clesMasters as $clesMaster)
            $ids[] = intval($clesMaster->getCle()->getId());

        return $ids;
    }
}