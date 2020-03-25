<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 31/03/2017
 * Time: 10:05
 */

namespace AppBundle\Repository;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Etat;
use AppBundle\Entity\EtatRegimeFiscal;
use AppBundle\Entity\IndicateurCell;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use stdClass;

class EtatRegimeFiscalRepository extends EntityRepository
{
    /**
     * @param Etat $etat
     * @param null $regimeFiscal
     * @param null $client
     * @param null $dossier
     * @return array
     */
    public function getEtatRegimes(Etat $etat,$regimeFiscal = null,$client = null,$dossier = null)
    {
        /*if ($etat->getEtat() != 0)
        {
            return $this->getEtatRegimeFiscals($etat,$client,$dossier);
        }*/

        $results = [];
        $req = $this->createQueryBuilder('er')
            ->where('er.etat = :etat')
            ->setParameter('etat',$etat)
            /*->andWhere('er.typeSociete = :type')
            ->setParameter('type',$type);*/;

        /**
         * model specifique
         */
        if($client != null || $dossier != null)
        {
            $etatRegimeSepcs = clone $req;
            if($client != null)
                $etatRegimeSepcs = $etatRegimeSepcs->andWhere('er.client = :client')->setParameter('client',$client);
            else
                $etatRegimeSepcs = $req->andWhere('(er.client = :client OR er.dossier = :dossier)')
                    ->setParameter('client',$dossier->getSite()->getClient())
                    ->setParameter('dossier',$dossier);
            $etatRegimeSepcs->getQuery()->getResult();

            foreach ($etatRegimeSepcs as $etatRegimeSepc)
            {
                $result = new stdClass();
                $result->niveau = ($etatRegimeSepc->getClient() != null) ? 1 : 2;
                $result->etatRegime = $etatRegimeSepc;

                $results[] = $result;
            }

            if ($dossier != null && $dossier->getRegimeFiscal() != null)
            {
                $regimeFiscal = $dossier->getRegimeFiscal();
            }
        }

        if ($regimeFiscal == null)
        {
            $regimeFiscal = $this->getEntityManager()->getRepository('AppBundle:RegimeFiscal')->getDefault();
        }

        /**
         * model general
         */
        $etatRegimeGen = clone $req;
        $etatRegimeGen = $etatRegimeGen
            ->andWhere('er.regimeFiscal = :regimeFiscal')
            ->setParameter('regimeFiscal',$regimeFiscal)
            ->getQuery()
            ->getOneOrNullResult();

        if($etatRegimeGen == null)
        {
            $em = $this->getEntityManager();
            $etatRegimeGen = new EtatRegimeFiscal();
            $etatRegimeGen->setEtat($etat);
            $etatRegimeGen->setTypeSociete(8);
            $etatRegimeGen->setRegimeFiscal($regimeFiscal);
            $em->persist($etatRegimeGen);
            $em->flush();
        }

        $result = new stdClass();
        $result->niveau = 0;
        $result->etatRegime = $etatRegimeGen;
        $results[] = $result;

        /**
         * model specifique
         */
        /*if($client != null || $dossier != null)
        {
            $etatRegimeSepcs = clone $req;
            if($client != null)
                $etatRegimeSepcs = $etatRegimeSepcs->andWhere('er.client = :client')->setParameter('client',$client);
            else
                $etatRegimeSepcs = $req->andWhere('(er.client = :client OR er.dossier = :dossier)')
                    ->setParameter('client',$dossier->getSite()->getClient())
                    ->setParameter('dossier',$dossier);
            $etatRegimeSepcs->getQuery()->getResult();

            foreach ($etatRegimeSepcs as $etatRegimeSepc)
            {
                $result = new stdClass();
                $result->niveau = ($etatRegimeSepc->getClient() != null) ? 1 : 2;
                $result->etatRegime = $etatRegimeSepc;

                $results[] = $result;
            }
        }*/

        foreach ($results as &$result)
        {
            //set cell, format columns, td to graphe
            $cells = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCellsEtats($result->etatRegime);
            $result->cells = $cells;

            $colsFormats = $this->getEntityManager()->getRepository('AppBundle:IndicateurFormatCol')->getColFormats($result->etatRegime,true);
            $result->colsFormats = $colsFormats;

            /*$tdsToGraphes = $this->getEntityManager()->getRepository('AppBundle:IndicateurFormatCol')->getTdToGraphe($item);
            $item->setTdsToGraphes($tdsToGraphes);*/
        }

        return $results;
        /**
         * niveau: 0:general, 1:client, 2:dossier
         */
    }

    /**
     * @param Etat $etat
     * @param null $client
     * @param null $dossier
     * @param int $type
     * @return array
     */
    public function getEtatRegimeFiscals(Etat $etat,$client = null,$dossier = null,$type = 0)
    {
        $results = [];
        $req = $this->createQueryBuilder('er')
            ->where('er.etat = :etat')
            ->setParameter('etat',$etat)
            ->andWhere('er.typeSociete = :type')
            ->setParameter('type',$type);

        /**
         * model general
         */
        $etatRegimeGen = clone $req;

        $etatRegimeGen = $etatRegimeGen
            ->getQuery()
            ->getOneOrNullResult();

        if($etatRegimeGen == null)
        {
            $em = $this->getEntityManager();
            $etatRegimeGen = new EtatRegimeFiscal();
            $etatRegimeGen->setEtat($etat);
            $etatRegimeGen->setTypeSociete($type);
            $em->persist($etatRegimeGen);
            $em->flush();
        }

        $result = new stdClass();
        $result->niveau = 0;
        $result->etatRegime = $etatRegimeGen;
        $results[] = $result;

        /**
         * model specifique
         */
        if($client != null || $dossier != null)
        {
            $etatRegimeSepcs = clone $req;
            if($client != null)
                $etatRegimeSepcs = $etatRegimeSepcs->andWhere('er.client = :client')->setParameter('client',$client);
            else
                $etatRegimeSepcs = $req->andWhere('(er.client = :client OR er.dossier = :dossier)')
                    ->setParameter('client',$dossier->getSite()->getClient())
                    ->setParameter('dossier',$dossier);
            $etatRegimeSepcs->getQuery()->getResult();

            foreach ($etatRegimeSepcs as $etatRegimeSepc)
            {
                $result = new stdClass();
                $result->niveau = ($etatRegimeSepc->getClient() != null) ? 1 : 2;
                $result->etatRegime = $etatRegimeSepc;

                $results[] = $result;
            }
        }

        foreach ($results as &$result)
        {
            //set cell, format columns, td to graphe
            $cells = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCellsEtats($result->etatRegime);
            $result->cells = $cells;

            $colsFormats = $this->getEntityManager()->getRepository('AppBundle:IndicateurFormatCol')->getColFormats($result->etatRegime,true);
            $result->colsFormats = $colsFormats;

            /*$tdsToGraphes = $this->getEntityManager()->getRepository('AppBundle:IndicateurFormatCol')->getTdToGraphe($item);
            $item->setTdsToGraphes($tdsToGraphes);*/
        }

        return $results;
        /**
         * niveau: 0:general, 1:client, 2:dossier
         */
    }

    /**
     * @param Etat $etat
     * @param Dossier $dossier
     * @return \Doctrine\ORM\QueryBuilder|mixed
     */
    public function getEtatRegimeFiscal(Etat $etat,Dossier $dossier)
    {
        $req = $this->createQueryBuilder('et')
            ->where('et.etat = :etat')
            ->setParameter('etat',$etat);

        $result = clone $req;
        $result = $result
            ->andWhere('et.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->getQuery()
            ->getOneOrNullResult();
        if($result != null) return $result;

        $result = clone $req;
        $result = $result
            ->andWhere('et.client = :client')
            ->setParameter('client',$dossier->getSite()->getClient())
            ->getQuery()
            ->getOneOrNullResult();
        if($result != null) return $result;

        $result = clone $req;

        $regimeFiscal = ($dossier->getRegimeFiscal() != null) ? $dossier->getRegimeFiscal() :
            $this->getEntityManager()->getRepository('AppBundle:RegimeFiscal')->getDefault();

        if ($regimeFiscal->getStatus() <> 1) $this->getEntityManager()->getRepository('AppBundle:RegimeFiscal')->getDefault();

        //default Regime FISCAL
        $regimeFiscal = $this->getEntityManager()->getRepository('AppBundle:RegimeFiscal')->getDefault();

        $e = $result
            ->andWhere('et.regimeFiscal = :regimeFiscal')
            ->setParameter('regimeFiscal',$regimeFiscal)
            ->getQuery()
            ->getOneOrNullResult();
        if ($e != null) return $e;

        $em = $this->getEntityManager();
        $etatRegimeGen = new EtatRegimeFiscal();
        $etatRegimeGen->setEtat($etat);
        $etatRegimeGen->setTypeSociete(8);
        $etatRegimeGen->setRegimeFiscal($regimeFiscal);
        $em->persist($etatRegimeGen);
        $em->flush();

        return $etatRegimeGen;
    }

    /**
     * @param Dossier $dossier
     * @param $etatIds
     * @param Utilisateur $user
     * @return array
     */
    public function getEtatRegimeStatus(Dossier $dossier,$etatIds, Utilisateur $user)
    {
        $etatSs = [];
        foreach ($etatIds as $etatId)
        {
            $etat = $this->getEntityManager()->getRepository('AppBundle:Etat')->find($etatId);
            $etatRegime = $this->getEtatRegimeFiscal($etat,$dossier);
            $status = new stdClass();
            $status->etat = $etat->getId();
            $status->status = ($user->getAccesUtilisateur()->getType() == 2) ? 1 : $etatRegime->getValider();

            $etatSs[] = $status;
        }

        return $etatSs;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->createQueryBuilder('sp')
            ->where('sp.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $indicateur
     * @param bool $isEtat
     * @param $styles
     * @param $cellsIndexs
     * @return int
     */
    public function changeStyles($indicateur,$isEtat = false,$styles,$cellsIndexs)
    {
        $em = $this->getEntityManager();
        $cells = [];
        foreach ($cellsIndexs as $cellsIndex)
        {
            $row = intval($cellsIndex->row);
            $col = intval($cellsIndex->col);
            $indicateurCell = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')
                ->getByRowCol($indicateur,$row,$col,$isEtat);

            if($indicateurCell == null)
            {
                $indicateurCell = new IndicateurCell();
                if($isEtat) $indicateurCell->setEtatRegimeFiscal($indicateur);
                else $indicateurCell->setIndicateur($indicateur);

                $indicateurCell->setCol($col);
                $indicateurCell->setRow($row);
                $indicateurCell->setFormule('');
                $indicateurCell->setIsFormule(0);
                $em->persist($indicateurCell);
            }

            $indicateurCell->setStyles(json_encode($styles));
            $em->flush();
        }

        return 1;
    }
}