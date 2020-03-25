<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 12/09/2017
 * Time: 10:37
 */

namespace AppBundle\Repository;

use AppBundle\Entity\IndicateurTb;
use Doctrine\ORM\EntityRepository;

class IndicateurTbDecisionRepository extends EntityRepository
{
    /**
     * @param IndicateurTb $indicateurTb
     * @return array
     */
    public function getIndicateurTbDecisions(IndicateurTb $indicateurTb)
    {
        return $this->createQueryBuilder('itd')
            ->where('itd.indicateurTb = :indicateurTb')
            ->setParameter('indicateurTb',$indicateurTb)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return string
     */
    public function getNaCode()
    {
        return 'NA';
    }
}