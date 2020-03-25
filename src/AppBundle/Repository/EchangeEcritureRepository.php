<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/03/2019
 * Time: 14:01
 */

namespace AppBundle\Repository;


use AppBundle\Controller\StringExt;
use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\EchangeEcriture;
use AppBundle\Entity\EchangeItem;
use AppBundle\Entity\EchangeType;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\TvaImputationControle;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;

class EchangeEcritureRepository extends EntityRepository
{
    /**
     * @param EchangeItem $echangeItem
     * @param $file
     * @param bool $create
     * @param bool $calculLettrage
     * @return EchangeEcriture[]
     */
    public function getEcritures(EchangeItem $echangeItem, $file, $create = true, $calculLettrage = false)
    {
        if ($create) $this->deleteOldEchangeEcriture($echangeItem);
        /** @var EchangeEcriture[] $echangeEcritures */
        $echangeEcritures = $this->createQueryBuilder('ee')
            ->where('ee.echangeItem = :echangeItem')
            ->setParameter('echangeItem',$echangeItem)
            ->getQuery()
            ->getResult();

        $em = $this->getEntityManager();
        if ($create && count($echangeEcritures) == 0)
        {
            $fichiersAccepter = ['XLS','XLSX'];
            $colonnesJnls = ['JNL','JRN','JRNL'];
            $colonneComptes = ['COMPTE','IMPUTATION ACTUELLE'];
            $colonneLibelles = ['LIBELLE','LIBELLES'];
            $colonnePieces = ['PIECE','PIECES'];

            $expodes = explode('.',$echangeItem->getNomFichier());
            if (in_array(strtoupper(trim($expodes[count($expodes) - 1])),$fichiersAccepter))
            {
                //$file = $controller->get('kernel')->getRootDir()."/../web/echange/".$echangeItem->getNomFichier();
                if (file_exists($file))
                {
                    $objPHPExcel = \PHPExcel_IOFactory::load($file);
                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
                    {
                        if ($worksheet->getSheetState() != \PHPExcel_Worksheet::SHEETSTATE_HIDDEN)
                        {
                            $colonneDate = null;
                            $colonneJnl = null;
                            $colonneCompte = null;
                            $colonnePiece = null;
                            $colonneLibelle = null;
                            $colonneDebit = null;
                            $colonneCredit = null;
                            $colonneSolde = null;

                            foreach ($worksheet->getRowIterator() as $row)
                            {
                                $cellIterator = $row->getCellIterator();
                                $cellIterator->setIterateOnlyExistingCells(false);

                                $date = null;
                                $jnl = null;
                                $compte = null;
                                $piece = null;
                                $libelle = null;
                                $debit = 0;
                                $credit = 0;
                                $solde = 0;

                                foreach ($cellIterator as $cell)
                                {
                                    if ($cell)
                                    {
                                        $colonne = $cell->getCoordinate();
                                        $valeur = trim($cell->getCalculatedValue());
                                        $colName = substr($colonne,0,1);

                                        if (
                                            in_array(strtoupper(StringExt::sansAccent($valeur)),['DEBIT','CREDIT','DATE','SOLDE']) ||
                                            in_array(strtoupper(StringExt::sansAccent($valeur)), $colonneLibelles) ||
                                            in_array(strtoupper(StringExt::sansAccent($valeur)), $colonnesJnls) ||
                                            in_array(strtoupper(StringExt::sansAccent($valeur)), $colonneComptes) ||
                                            substr(strtoupper(StringExt::sansAccent(trim($valeur))),0,5) == 'PIECE' && $colonneDate
                                        ){
                                            if (strtoupper(StringExt::sansAccent($valeur)) == 'DEBIT' && !$colonneDebit)
                                                $colonneDebit = $colName;
                                            elseif (strtoupper(StringExt::sansAccent($valeur)) == 'CREDIT' && !$colonneCredit)
                                                $colonneCredit = $colName;
                                            elseif (strtoupper(StringExt::sansAccent($valeur)) == 'DATE' && !$colonneDate)
                                                $colonneDate = $colName;
                                            elseif (in_array(strtoupper(StringExt::sansAccent($valeur)), $colonneLibelles) && !$colonneLibelle)
                                                $colonneLibelle = $colName;
                                            elseif (strtoupper(StringExt::sansAccent($valeur)) == 'SOLDE' && !$colonneSolde)
                                                $colonneSolde = $colName;
                                            elseif (in_array(strtoupper(StringExt::sansAccent($valeur)), $colonnesJnls) && !$colonneJnl)
                                                $colonneJnl = $colName;
                                            elseif (in_array(strtoupper(StringExt::sansAccent($valeur)), $colonneComptes) && !$colonneCompte)
                                                $colonneCompte = $colName;
                                            elseif (!$colonnePiece)
                                                $colonnePiece = $colName;
                                        }
                                        elseif ($colonneDebit && $colonneCredit && $colonneDate)
                                        {
                                            if (
                                                $colonneDate && $colonneDate == $colName ||
                                                $colonneJnl && $colonneJnl == $colName ||
                                                $colonneCompte && $colonneCompte == $colName ||
                                                $colonnePiece && $colonnePiece == $colName ||
                                                $colonneLibelle && $colonneLibelle == $colName ||
                                                $colonneDebit && $colonneDebit == $colName ||
                                                $colonneCredit && $colonneCredit == $colName ||
                                                $colonneSolde && $colonneSolde == $colName
                                            ){
                                                if ($colonneDate && $colonneDate == $colName)
                                                    $date = \PHPExcel_Shared_Date::ExcelToPHPObject($cell->getCalculatedValue());
                                                elseif ($colonneJnl && $colonneJnl == $colName)
                                                    $jnl = $valeur;
                                                elseif ($colonneCompte && $colonneCompte == $colName)
                                                    $compte = $valeur;
                                                elseif ($colonnePiece && $colonnePiece == $colName)
                                                    $piece = $valeur;
                                                elseif ($colonneLibelle && $colonneLibelle == $colName)
                                                    $libelle = $valeur;
                                                elseif ($colonneDebit && $colonneDebit == $colName)
                                                    $debit = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$valeur))));
                                                elseif ($colonneCredit && $colonneCredit == $colName)
                                                    $credit = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$valeur))));
                                                elseif ($colonneSolde && $colonneSolde == $colName)
                                                    $solde = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,-]/','',$valeur))));
                                            }
                                        }
                                    }
                                }

                                if (($debit - $credit != 0) && $date)
                                {
                                    $echangeEcriture = new EchangeEcriture();
                                    $echangeEcriture
                                        ->setDate($date)
                                        ->setCompte($compte)
                                        ->setCredit($credit)
                                        ->setDebit($debit)
                                        ->setEchangeItem($echangeItem)
                                        ->setJournal($jnl)
                                        ->setLibelle($libelle)
                                        ->setPage($worksheet->getTitle())
                                        ->setSolde($solde)
                                        ->setPiece($piece);
                                    $em->persist($echangeEcriture);

                                    $echangeEcritures[] = $echangeEcriture;
                                }
                            }
                        }
                    }
                }
            }

            $em->flush();
        }

        if ($calculLettrage)
        {
            foreach ($echangeEcritures as &$echangeEcriture)
            {
                /** @var \DateTime $dateLettrage */
                $dateLettrage = null;
                if ($echangeEcriture->getPasPiece() == 0 && $echangeEcriture->getStatus() == 0 && !$echangeEcriture->getImage())
                {
                    /** @var Tiers $tiers */
                    $tiers = null;
                    if ($echangeEcriture->getCompte() && intval(substr($echangeEcriture->getCompte(),0,2)) != 47)
                    {
                        $tiers = $this->getEntityManager()->getRepository('AppBundle:Tiers')
                            ->getOneByCompte($echangeItem->getEchange()->getDossier(), $echangeEcriture->getCompte(), 10);
                    }

                    $tvaImputationControles = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                        ->getTvaImputationControleByMontant($echangeEcriture->getEchangeItem()->getEchange()->getDossier(),$echangeEcriture->getDebit() - $echangeEcriture->getCredit(),$echangeItem->getEchange()->getExercice(), $tiers);

                    if (count($tvaImputationControles) > 0)
                        $dateLettrage = new \DateTime();
                }

                $echangeEcriture->setDateCalculALettrer($dateLettrage);
            }
            $em->flush();
        }

        return $echangeEcritures;
    }

    /**
     * @param EchangeType $echangeType
     * @param $exercice
     * @param Client $client
     * @param Dossier|null $dossier
     * @return object
     */
    public function getStats(EchangeType $echangeType, $exercice, Client $client, Dossier $dossier = null)
    {
        /**
         * status
         * 0: non lettre, non repondue
         * 1: repondue
         * 2: lettre
         * 3: image a lettre
         */
        $lettres = $this->getStatItem($echangeType, $exercice, $client, $dossier,2);
        $repondues = $this->getStatItem($echangeType, $exercice, $client, $dossier,1);
        $imagesALettres = $this->getStatItem($echangeType, $exercice, $client, $dossier,3);
        $idOuts = [];
        foreach ($imagesALettres as $imagesALettre)
            $idOuts[] = $imagesALettre->id;
        $pms = $this->getStatItem($echangeType, $exercice, $client, $dossier,0,$idOuts);

        return (object)
        [
            'lettres' => $lettres,
            'repondues' => $repondues,
            'imagesALettres' => $imagesALettres,
            'pms' => $pms
        ];
    }

    /**
     * @param EchangeType $echangeType
     * @param $exercice
     * @param Client $client
     * @param Dossier|null $dossier
     * @param int $status
     * @param array $idOuts
     * @return array
     */
    private function getStatItem(EchangeType $echangeType, $exercice, Client $client, Dossier $dossier = null, $status = 0, $idOuts = [])
    {
        $echangeEcritures = $this->createQueryBuilder('ee')
            ->join('ee.echangeItem','ei')
            ->join('ei.echange','e')
            ->where('e.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->andWhere('e.echangeType = :echangeType')
            ->setParameter('echangeType', $echangeType);

        if (count($idOuts) > 0)
            $echangeEcritures = $echangeEcritures
                ->andWhere('ee.id NOT IN (:idsOut)')
                ->setParameter('idsOut', $idOuts);

        if ($dossier)
            $echangeEcritures = $echangeEcritures
                ->andWhere('e.dossier = :dossier')
                ->setParameter('dossier',$dossier);
        else
            $echangeEcritures = $echangeEcritures
                ->join('e.dossier','d')
                ->join('d.site','s')
                ->andWhere('s.client = :client')
                ->setParameter('client',$client);

        /**
         * status
         * 0: non lettre, non repondue
         * 1: repondue
         * 2: lettre
         * 3: image a lettre
         */
        if ($status == 0 || $status == 3)
            $echangeEcritures = $echangeEcritures
                ->andWhere('ee.image IS NULL')
                ->andWhere('ee.status = 0');
        elseif ($status == 1)
            $echangeEcritures = $echangeEcritures
                ->andWhere('ee.image IS NULL')
                ->andWhere('ee.status = 1');
        elseif ($status == 2)
            $echangeEcritures = $echangeEcritures
                ->andWhere('ee.image IS NOT NULL');

        /** @var EchangeEcriture[] $echangeEcritures */
        $echangeEcritures = $echangeEcritures
            ->getQuery()
            ->getResult();

        $echEcrs = [];
        foreach ($echangeEcritures as $echangeEcriture)
        {
            $idsImages = [];
            if ($status == 3)
            {
                if ($dossier)
                {
                    $tiers = null;
                    if ($echangeEcriture->getCompte() && intval(substr($echangeEcriture->getCompte(),0,2)) != 47)
                    {
                        $tiers = $this->getEntityManager()->getRepository('AppBundle:Tiers')
                            ->getOneByCompte($echangeEcriture->getEchangeItem()->getEchange()->getDossier(), $echangeEcriture->getCompte(), 10);
                    }

                    $tvaImputationControles = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                        ->getTvaImputationControleByMontant($echangeEcriture->getEchangeItem()->getEchange()->getDossier(),$echangeEcriture->getDebit() - $echangeEcriture->getCredit(),$echangeEcriture->getEchangeItem()->getEchange()->getExercice(), $tiers);
                    foreach ($tvaImputationControles as $imputationControle)
                    {
                        /** @var TvaImputationControle $tvaImputationControle */
                        $tvaImputationControle = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                            ->find($imputationControle['tvaic']);
                        $idsImages[] = $tvaImputationControle->getImage()->getId();
                    }

                    if (count($idsImages) > 0) $echangeEcriture->setDateCalculALettrer(new \DateTime());
                    else $echangeEcriture->setDateCalculALettrer(null);
                }
                elseif ($echangeEcriture->getDateCalculALettrer()) $idsImages[] = 5;
            }

            if (count($idsImages) > 0 && $status == 3 || $status != 3)
                $echEcrs[] = (object)
                [
                    'id' => $echangeEcriture->getId()
                ];
        }

        $this->getEntityManager()->flush();

        return $echEcrs;
    }

    private function deleteOldEchangeEcriture(EchangeItem $echangeItem)
    {
        $olds = $this->createQueryBuilder('ee')
            ->join('ee.echangeItem','ei')
            ->where('ei.echange = :echange')
            ->andWhere('ei.id <> :id')
            ->setParameters([
                'echange' => $echangeItem->getEchange(),
                'id' => $echangeItem
            ])
            ->getQuery()
            ->getResult();

        $em = $this->getEntityManager();
        foreach ($olds as $old)
            $em->remove($old);

        $em->flush();
    }
}