<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/11/2016
 * Time: 10:28
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\IndicateurPack;
use AppBundle\Entity\IndicateurSpecPack;
use Doctrine\ORM\EntityRepository;

class IndicateurSpecPackRepository extends EntityRepository
{
    /**
     * @param IndicateurPack $indicateurPack
     * @param $client
     * @param $dossier
     * @param $oldStatus
     */
    public function changeEnabledTo(IndicateurPack $indicateurPack,$client,$dossier,$oldStatus)
    {
        $em = $this->getEntityManager();
        if($oldStatus)
        {
            $indicateurSpecPack = new IndicateurSpecPack();
            $indicateurSpecPack->setDossier($dossier);
            $indicateurSpecPack->setClient($client);
            $indicateurSpecPack->setIndicateurPack($indicateurPack);
            $em->persist($indicateurSpecPack);
        }
        else
        {
            $req = $this->createQueryBuilder('isp')
                ->where('isp.indicateurPack = :indicateurPack')
                ->setParameter('indicateurPack',$indicateurPack);
            if($dossier != null) $req = $req->andWhere('isp.dossier = :dossier')->setParameter('dossier',$dossier);
            else $req = $req->andWhere('isp.client = :client')->setParameter('client',$client);

            $indicateurSpecPack = $req->getQuery()->getOneOrNullResult();
            if($indicateurSpecPack != null) $em->remove($indicateurSpecPack);
        }

        $em->flush();
    }

    /**
     * @param IndicateurPack $indicateurPack
     * @param $client
     * @param $dossier
     * @return IndicateurPack
     */
    public function setEnabled(IndicateurPack $indicateurPack,$client,$dossier)
    {
        $enabledQuery = $this->createQueryBuilder('ps')
            ->where('ps.indicateurPack = :indicateurPack')
            ->setParameter('indicateurPack',$indicateurPack);

        if($client != null) $enabledQuery = $enabledQuery->andWhere('ps.client = :client')->setParameter('client',$client);
        else
            $enabledQuery = $enabledQuery
                ->andWhere('(ps.dossier = :dossier OR ps.client = :client)')
                ->setParameter('dossier',$dossier)
                ->setParameter('client',$dossier->getSite()->getClient());
        return $indicateurPack->setEnabled($enabledQuery->getQuery()->getOneOrNullResult() == null);
    }
}