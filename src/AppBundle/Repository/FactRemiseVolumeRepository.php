<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 12/12/2016
 * Time: 08:37
 */

namespace AppBundle\Repository;

use AppBundle\Entity\FactRemiseNiveau;
use Doctrine\ORM\EntityRepository;

class FactRemiseVolumeRepository extends EntityRepository
{
    function getAllRemise()
    {
        $remises = $this->getEntityManager()
            ->getRepository('AppBundle:FactRemiseVolume')
            ->createQueryBuilder('fv')
            ->select('fv')
            ->innerJoin('fv.factRemiseNiveau', 'niveau')
            ->addSelect('niveau')
            ->orderBy('fv.factRemiseNiveau')
            ->addOrderBy('fv.code')
            ->getQuery()
            ->getResult();
        return $remises;
    }

    function getByNiveau(FactRemiseNiveau $niveau)
    {
        $remises = $this->getEntityManager()
            ->getRepository('AppBundle:FactRemiseVolume')
            ->createQueryBuilder('fv')
            ->select('fv')
            ->innerJoin('fv.factRemiseNiveau', 'niveau')
            ->addSelect('niveau')
            ->where('fv.factRemiseNiveau = :niveau')
            ->setParameter('niveau', $niveau)
            ->orderBy('fv.factRemiseNiveau')
            ->addOrderBy('fv.code')
            ->getQuery()
            ->getResult();
        return $remises;
    }

    function getPercentageByVolume(FactRemiseNiveau $type, $volume)
    {
        $remise = $this->getEntityManager()
            ->getRepository('AppBundle:FactRemiseVolume')
            ->createQueryBuilder('factRemiseVolume')
            ->where('factRemiseVolume.tranche2 >= :volume')
            ->innerJoin('factRemiseVolume.factRemiseNiveau', 'factRemiseNiveau')
            ->andWhere('factRemiseNiveau = :type')
            ->orderBy('factRemiseVolume.tranche1')
            ->setMaxResults(1)
            ->select('factRemiseVolume.pourcentage')
            ->setParameters(array(
                'volume' => $volume,
                'type' => $type,
            ))
            ->getQuery()
            ->getResult();
        if (count($remise) > 0) {
            return $remise[0]['pourcentage'];
        } else {
            return 0;
        }
    }
}