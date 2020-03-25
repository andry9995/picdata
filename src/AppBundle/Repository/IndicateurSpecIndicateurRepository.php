<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/11/2016
 * Time: 10:28
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurPack;
use AppBundle\Entity\IndicateurSpecIndicateur;
use Doctrine\ORM\EntityRepository;

class IndicateurSpecIndicateurRepository extends EntityRepository
{
    /**
     * @param Indicateur $indicateur
     * @param $client
     * @param $dossier
     * @param $oldStatus
     */
    public function changeEnabledTo(Indicateur $indicateur,$client,$dossier,$oldStatus)
    {
        $em = $this->getEntityManager();
        if($oldStatus)
        {
            $indicateurSpecIndicateur = new IndicateurSpecIndicateur();
            $indicateurSpecIndicateur->setDossier($dossier);
            $indicateurSpecIndicateur->setClient($client);
            $indicateurSpecIndicateur->setIndicateur($indicateur);
            $em->persist($indicateurSpecIndicateur);
        }
        else
        {
            $req = $this->createQueryBuilder('isp')
                ->where('isp.indicateur = :indicateur')
                ->setParameter('indicateur',$indicateur);
            if($dossier != null) $req = $req->andWhere('isp.dossier = :dossier')->setParameter('dossier',$dossier);
            else $req = $req->andWhere('isp.client = :client')->setParameter('client',$client);

            $indicateurSpecIndicateur = $req->getQuery()->getOneOrNullResult();
            if($indicateurSpecIndicateur != null) $em->remove($indicateurSpecIndicateur);
        }

        $em->flush();
    }

    /**
     * @param Indicateur $indicateur
     * @param $client
     * @param $dossier
     * @return Indicateur
     */
    public function setEnabled(Indicateur $indicateur,$client,$dossier)
    {
        //return $indicateur->setEnabled(false);
        $enabledQuery = $this->createQueryBuilder('isp')
            ->where('isp.indicateur = :indicateur')
            ->setParameter('indicateur',$indicateur);

        if($client != null)
        {
            $enabledQuery = $enabledQuery->andWhere('isp.client = :client')->setParameter('client',$client);
        }
        else
        {
            $enabledQuery = $enabledQuery
                ->andWhere('(isp.dossier = :dossier OR isp.client = :client)')
                ->setParameter('dossier', $dossier)
                ->setParameter('client', $dossier->getSite()->getClient());
        }
        return $indicateur->setEnabled($enabledQuery->getQuery()->getOneOrNullResult() == null);
    }
}