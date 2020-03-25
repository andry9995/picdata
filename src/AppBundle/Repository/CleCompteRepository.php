<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 14/02/2018
 * Time: 16:42
 */

namespace AppBundle\Repository;

use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Cle;
use AppBundle\Entity\CleCompte;
use Doctrine\ORM\EntityRepository;

class CleCompteRepository extends EntityRepository
{
    /**
     * @param Cle $cle
     * @param BanqueCompte $banqueCompte
     * @return array
     */
    public function getCles(Cle $cle,BanqueCompte $banqueCompte)
    {
        //0: resultat; 1:tva; 2 : bilan
        $cleRepository = $this->getEntityManager()->getRepository('AppBundle:Cle');
        //Dossier
        $bilans = [];
        $tvas = [];
        $resultats = [];
        $cleComptes = $this->getEntityManager()->getRepository('AppBundle:CleCompte')
            ->createQueryBuilder('cc')
            ->where('cc.dossier = :dossier')
            ->andWhere('cc.cle = :cle')
            ->setParameters(array('dossier' => $banqueCompte->getDossier(), 'cle' => $cle))
            ->getQuery()
            ->getResult();
        foreach ($cleComptes as $cleCompte)
        {
            //$cleCompte = new CleCompte();
            if (!is_null($cleCompte->getPcc()))
            {
                //resultat
                if ($cleCompte->getType() == 0)
                    $resultats[] = $cleRepository->getCompteObject($cleCompte->getPcc());
                //tva
                if ($cleCompte->getType() == 1)
                    $tvas[] = $cleRepository->getCompteObject($cleCompte->getPcc());
                //bilan
                if ($cleCompte->getType() == 2)
                {
                    if ($cleCompte->getPcc()->getCollectifTiers() != -1)
                    {
                        $tierss = $this->getEntityManager()->getRepository('AppBundle:Tiers')
                            ->createQueryBuilder('t')
                            ->where('t.pcc = :pcc')
                            ->setParameter('pcc',$cleCompte->getPcc());
                        foreach ($tierss as $tiers)
                            $bilans[] = $cleRepository->getCompteObject($tiers);
                    }
                    else $bilans[] = $cleRepository->getCompteObject($cleCompte->getPcc());
                }
            }
            elseif (!is_null($cleCompte->getTier()))
            {
                //resultat
                if ($cleCompte->getType() == 0)
                    $resultats[] = $cleRepository->getCompteObject($cleCompte->getTier());
                //tva
                if ($cleCompte->getType() == 1)
                    $tvas[] = $cleRepository->getCompteObject($cleCompte->getTier());
                //bilan
                if ($cleCompte->getType() == 2)
                    $bilans[] = $cleRepository->getCompteObject($cleCompte->getTier());
            }
        }

        $cles = [];
        if (!(count($bilans) == 0 && count($tvas) == 0 && count($resultats) ==  0))
            $cles[] = $cleRepository->getCleObject($cle,$bilans,$tvas,$resultats,0,1);
        else
        {
            //GEN
            $bilans = [];
            $tvas = [];
            $resultats = [];

            $bilansPcgs = [];
            $tvasPcgs = [];
            $resultatsPcgs = [];
            $cleComptes = $this->getEntityManager()->getRepository('AppBundle:CleCompte')
                ->createQueryBuilder('cc')
                ->where('cc.dossier IS NULL')
                ->andWhere('cc.cle = :cle')
                ->andWhere('cc.pcg IS NOT NULL')
                ->setParameter('cle',$cle)
                ->getQuery()
                ->getResult();
            foreach ($cleComptes as $cleCompte)
            {
                //$cleCompte = new CleCompte();

                //resultat
                if ($cleCompte->getType() == 0)
                    $resultatsPcgs[] = $cleCompte->getPcg();
                //tva
                if ($cleCompte->getType() == 1)
                    $tvasPcgs[] = $cleCompte->getPcg();
                //bilan
                if ($cleCompte->getType() == 2)
                    $bilansPcgs[] = $cleCompte->getPcg();
            }

            $resultatsPccs = $this->getEntityManager()->getRepository('AppBundle:Pcc')->getPCCByPCG($resultatsPcgs,$banqueCompte->getDossier());
            foreach ($resultatsPccs as $resultatsPcc)
                $resultats[] = $cleRepository->getCompteObject($resultatsPcc);
            $tvasPccs = $this->getEntityManager()->getRepository('AppBundle:Pcc')->getPCCByPCG($tvasPcgs,$banqueCompte->getDossier());
            foreach ($tvasPccs as $tvasPcc)
                $tvas[] = $cleRepository->getCompteObject($tvasPcc);
            $bilansPccs = $this->getEntityManager()->getRepository('AppBundle:Pcc')->getPCCByPCG($bilansPcgs,$banqueCompte->getDossier());
            foreach ($bilansPccs as $bilansPcc)
            {
                if ($bilansPcc->getCollectifTiers() != -1)
                {
                    $tierss = $this->getEntityManager()->getRepository('AppBundle:Tiers')
                        ->createQueryBuilder('t')
                        ->where('t.pcc = :pcc')
                        ->setParameter('pcc',$bilansPcc)
                        ->orderBy('t.intitule')
                        ->getQuery()->getResult();
                    foreach ($tierss as $tiers)
                        $bilans[] = $cleRepository->getCompteObject($tiers);
                }
                else $bilans[] = $cleRepository->getCompteObject($bilansPcc);
            }

            if (!(count($bilans) == 0 && count($tvas) == 0 && count($resultats) ==  0))
                $cles[] = $cleRepository->getCleObject($cle,$bilans,$tvas,$resultats,0,0);
        }

        return $cles;
    }
}