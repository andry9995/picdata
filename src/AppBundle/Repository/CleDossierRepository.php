<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 14/02/2018
 * Time: 15:47
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Cle;
use AppBundle\Entity\CleCompte;
use AppBundle\Entity\CleDossier;
use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Acl\Exception\Exception;

class CleDossierRepository extends EntityRepository
{
    /**
     * @param Cle $cle
     * @param Dossier $dossier
     * @return array
     */
    public function getCleDossiers(Cle $cle,Dossier $dossier)
    {
        $cleDossiers = $this->getEntityManager()->getRepository('AppBundle:CleDossier')
            ->createQueryBuilder('cd')
            ->where('cd.dossier = :dossier')
            ->andWhere('cd.cle  = :cle')
            ->setParameters(array('dossier'=>$dossier, 'cle'=>$cle))
            ->orderBy('cd.occurence','DESC')
            ->getQuery()
            ->getResult();

        $cleRepository = $this->getEntityManager()->getRepository('AppBundle:Cle');

        $cles = [];
        foreach ($cleDossiers as $cleDossier)
        {
            //$cleDossier = new CleDossier();
            /**
             * bilan
             */
            $bilans = [];
            if (!is_null($cleDossier->getBilanPcc()))
                $bilans[] = $cleRepository->getCompteObject($cleDossier->getBilanPcc());
            elseif (!is_null($cleDossier->getBilanTiers()))
                $bilans[] = $cleRepository->getCompteObject($cleDossier->getBilanTiers());

            /**
             * tva
             */
            $tvas = [];
            if (!is_null($cleDossier->getTva()))
                $tvas[] = $cleRepository->getCompteObject($cleDossier->getTva());

            /**
             * resultat
             */
            $resultats = [];
            if (!is_null($cleDossier->getResultat()))
                $resultats[] = $cleRepository->getCompteObject($cleDossier->getResultat());

            $cles[] = $cleRepository->getCleObject($cle,$bilans,$tvas,$resultats,$cleDossier->getOccurence(),2);
        }

        return $cles;
    }

    /**
     * @param Cle $cle
     * @param Dossier $dossier
     * @param $bilan
     * @param $tva
     * @param $resultat
     * @param int $bilanType
     * @param int $tauxTva
     * @param int $typeCompta
     * @return \Doctrine\ORM\QueryBuilder|mixed
     */
    public function setCleDossier(Cle $cle,Dossier $dossier,$bilan,$tva,$resultat,$bilanType = 0,$tauxTva = 0,$typeCompta = 0)
    {
        $cleDossier = $this->createQueryBuilder('cd')
            ->where('cd.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('cd.cle = :cle')
            ->setParameter('cle',$cle);

        //bilan
        if (!is_null($bilan))
        {
            if ($bilanType == 0) $cleDossier = $cleDossier->andWhere('cd.bilanPcc = :bilan');
            else $cleDossier = $cleDossier->andWhere('cd.bilanTiers = :bilan');

            $cleDossier = $cleDossier->setParameter('bilan',$bilan);
        }
        //resultat
        if (!is_null($resultat))
            $cleDossier = $cleDossier->andWhere('cd.resultat = :resultat')->setParameter('resultat',$resultat);
        //tva
        if (!is_null($tva))
            $cleDossier = $cleDossier->andWhere('cd.tva = :tva')->setParameter('tva',$tva);

        $cleDossier = $cleDossier->getQuery()->getOneOrNullResult();

        $em = $this->getEntityManager();
        if ($cleDossier == null)
        {
            $cleDossier = new CleDossier();

            $cleDossier->setDossier($dossier);
            $cleDossier->setCle($cle);
            $cleDossier->setTva($tva);
            if (!is_null($bilan))
                if ($bilanType == 0) $cleDossier->setBilanPcc($bilan);
                else $cleDossier->setBilanTiers($bilan);
            $cleDossier->setResultat($resultat);
            $cleDossier->setTauxTva($tauxTva);
            $cleDossier->setOccurence(0);
            $cleDossier->setTypeCompta($typeCompta);
            $em->persist($cleDossier);
        }
        else $cleDossier->setTypeCompta($typeCompta);

        $em->flush();
        return $cleDossier;
    }

    /**
     * @param Dossier $dossier
     * @return array
     */
    public function getClesDossiers(Dossier $dossier)
    {
        return $this->createQueryBuilder('cd')
            ->leftJoin('cd.cle','c')
            ->select('cd AS cleD,COUNT(cd)')
            ->where('cd.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Cle $cle
     * @param Dossier $dossier
     * @return CleDossier
     */
    public function getCleDossierByCle(Cle $cle, Dossier $dossier)
    {
        return $this->createQueryBuilder('cd')
            ->where('cd.cle = :cle')
            ->andWhere('cd.dossier = :dossier')
            ->setParameters([
                'cle' => $cle,
                'dossier' => $dossier
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Dossier $dossier
     * @return Cle[]
     */
    public function getClePasPiece(Dossier $dossier)
    {
        /** @var CleDossier[] $cleDossiers */
        $cleDossiers = $this->createQueryBuilder('cd')
            ->where('cd.pasPiece = 1')
            ->andWhere('cd.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->getQuery()
            ->getResult();

        /** @var Cle[] $cles */
        $cles = [];

        foreach ($cleDossiers as $cleDossier)
            $cles[] = $cleDossier->getCle();

        return $cles;
    }

    /**
     * @param Dossier $dossier
     * @return CleDossier[]
     */
    public function getCleAvecPieceForDossier(Dossier $dossier)
    {
        return $this->createQueryBuilder('cd')
            //->where('cd.pasPiece = 0')
            ->Where('cd.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();
    }
}