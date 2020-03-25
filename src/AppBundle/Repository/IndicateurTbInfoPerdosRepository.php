<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 16/09/2019
 * Time: 11:43
 */

namespace AppBundle\Repository;


use AppBundle\Entity\IndicateurInfoPerdos;
use AppBundle\Entity\IndicateurTbInfoPerdos;
use Doctrine\ORM\EntityRepository;

class IndicateurTbInfoPerdosRepository extends EntityRepository
{
    /**
     * @return IndicateurInfoPerdos[]
     */
    public function nonAffecter()
    {
        /** @var IndicateurInfoPerdos[] $indicateurInfoPerdos */
        $indicateurInfoPerdos = $this->getEntityManager()->getRepository('AppBundle:IndicateurInfoPerdos')
            ->all();

        /** @var IndicateurInfoPerdos[] $results */
        $results = [];

        /** @var IndicateurTbInfoPerdos[] $indicateurTbInfoPerdos */
        $indicateurTbInfoPerdos = $this->findAll();
        $affecters = [];

        foreach ($indicateurTbInfoPerdos as $indicateurTbInfoPerdo)
        {
            if (!in_array($indicateurTbInfoPerdo->getIndicateurInfoPerdos()->getId(),$affecters))
                $affecters[] = $indicateurTbInfoPerdo->getIndicateurInfoPerdos()->getId();
        }

        foreach ($indicateurInfoPerdos as $indicateurInfoPerdo)
        {
            if (!in_array($indicateurInfoPerdo->getId(),$affecters))
                $results[] = $indicateurInfoPerdo;
        }

        return $results;
    }

    /**
     * @param IndicateurInfoPerdos $indicateurInfoPerdos
     * @return IndicateurTbInfoPerdos
     */
    public function getByIndicateurInfoPerdos(IndicateurInfoPerdos $indicateurInfoPerdos)
    {
        return $this->createQueryBuilder('itip')
            ->where('itip.indicateurInfoPerdos = :indicateurInfoPerdos')
            ->setParameter('indicateurInfoPerdos',$indicateurInfoPerdos)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return IndicateurTbInfoPerdos[]
     */
    public function all()
    {
        return $this->createQueryBuilder('itip')
            ->leftJoin('itip.indicateurInfoPerdos','ii')
            ->orderBy('ii.header')
            ->getQuery()
            ->getResult();
    }
}