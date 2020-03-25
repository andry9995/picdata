<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 22/02/2019
 * Time: 08:39
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Dossier;
use AppBundle\Entity\ImportParam;
use Doctrine\ORM\EntityRepository;

class ImportParamRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @return ImportParam
     */
    public function getNotPonctuelForDossier(Dossier $dossier)
    {
        return $this->createQueryBuilder('ip')
            ->where('ip.dossier = :dossier')
            ->andWhere('ip.periode <> 0')
            ->setParameter('dossier',$dossier)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Dossier $dossier
     * @return ImportParam[]
     */
    public function getPonctuels(Dossier $dossier)
    {
        return $this->createQueryBuilder('ip')
            ->where('ip.dossier = :dossier')
            ->andWhere('ip.periode = 0')
            ->setParameter('dossier',$dossier)
            ->orderBy('ip.date')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @return ImportParam
     */
    public function getNextPonctuel(Dossier $dossier)
    {
        return $this->createQueryBuilder('ip')
            ->where('ip.dossier = :dossier')
            ->andWhere('ip.periode = :periode')
            ->andWhere('ip.date >= :dateNow')
            ->setParameters([
                'dossier' => $dossier,
                'dateNow' => (new \DateTime())->format('Y-m-d'),
                'periode' => 0
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}