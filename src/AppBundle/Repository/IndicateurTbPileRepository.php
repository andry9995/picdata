<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 17/05/2019
 * Time: 14:01
 */

namespace AppBundle\Repository;


use AppBundle\Entity\HistoriqueUpload;
use AppBundle\Entity\IndicateurTb;
use AppBundle\Entity\IndicateurTbPile;
use Doctrine\ORM\EntityRepository;

class IndicateurTbPileRepository extends EntityRepository
{
    /**
     * @param IndicateurTb $indicateurTb
     * @param HistoriqueUpload $historiqueUpload
     * @return IndicateurTbPile
     */
    public function getIndicateurTbPile(IndicateurTb $indicateurTb,HistoriqueUpload $historiqueUpload)
    {
        return $this->createQueryBuilder('ip')
            ->where('ip.historiqueUpload = :historiqueUpload')
            ->andWhere('ip.indicateurTb = :indicateurTb')
            ->setParameters([
                'historiqueUpload' => $historiqueUpload,
                'indicateurTb' => $indicateurTb
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}