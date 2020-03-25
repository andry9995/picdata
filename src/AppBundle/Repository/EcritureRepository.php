<?php
namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Ecriture;
use AppBundle\Entity\Image;
use AppBundle\Entity\IndicateurTbCle;
use AppBundle\Entity\JournalDossier;
use AppBundle\Entity\Rubrique;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;
use AppBundle\Controller\Balance;
use EtatBaseBundle\Controller\GrandLivre;

class EcritureRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @param $exercices
     * @param $mois
     * @param $avecLettre
     * @param $compteDe
     * @param $compteA
     * @return Balance
     */
    public function getBalance(Dossier $dossier,$exercices,$mois,$avecLettre,$compteDe = null,$compteA = null)
    {
        $comptes = array();
        $debits = array();
        $credits = array();
        $soldes_debit = array();
        $soldes_credit = array();
        $comptes_str = array();

        //pcc
        $queryPcc = $this->createQueryBuilder('e')
            ->select('e as balance,ROUND(SUM(e.debit),2) as db, ROUND(SUM(e.credit),2) as cr')
            ->leftJoin('e.pcc','pcc')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercice)')
            ->setParameter('exercice',$exercices)
            ->andWhere('e.pcc IS NOT NULL');
        if (!$avecLettre) $queryPcc = $queryPcc->andWhere("(e.lettrage = '' OR e.lettrage IS NULL)");

        //tiers
        $queryTiers = $this->createQueryBuilder('e')
            ->select('e as balance,ROUND(SUM(e.debit),2) as db, ROUND(SUM(e.credit),2) as cr, j.id as an_id')
            ->leftJoin('e.tiers','tiers')
            ->leftJoin('tiers.pcc','pcc')
            ->leftJoin('e.journalDossier','jd')
            ->leftJoin('jd.journal','j')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercice)')
            ->setParameter('exercice',$exercices)
            ->andWhere('e.tiers IS NOT NULL');
        if (!$avecLettre) $queryTiers = $queryTiers->andWhere("(e.lettrage = '' OR e.lettrage IS NULL)");

        /*$queryPcc = $queryPcc
            ->andWhere('pcc.compte >= :compteDe')
            ->setParameter('compteDe','2');*/

        if(!is_bool($mois))
        {
            $queryPcc = $queryPcc->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")->setParameter('mois',$mois);
            $queryTiers = $queryTiers->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")->setParameter('mois',$mois);
        }
        $montantsPccs = $queryPcc->groupBy('pcc')
            ->addGroupBy('e.exercice')
            ->orderBy('pcc.compte','ASC')
            ->getQuery()
            ->getResult();
        foreach ($montantsPccs as $montantsPcc)
        {
            $pcc = $montantsPcc['balance']->getPcc();
            $debit = $montantsPcc['db'];
            $credit = $montantsPcc['cr'];
            $num_compte = $pcc->getCompte();
            $exercice = $montantsPcc['balance']->getExercice();

            $debits[$num_compte][$exercice] = $credits[$num_compte][$exercice] = 0;
            $debits[$num_compte][$exercice] = $debit;
            $credits[$num_compte][$exercice] = $credit;
            $solde = $debit - $credit;
            $soldes_debit[$num_compte][$exercice] = ($solde >= 0) ? $solde : 0;
            $soldes_credit[$num_compte][$exercice] = ($solde < 0) ? abs($solde) : 0;

            if(!in_array($num_compte,$comptes_str))
            {
                $comptes_str[] = $num_compte;
                $comptes[$num_compte] = $pcc;
            }
        }

        $montantsTiers = $queryTiers->groupBy('tiers')
            ->addGroupBy('e.exercice')
            ->addGroupBy('j.id')
            ->orderBy('tiers.compteStr','ASC')
            ->getQuery()
            ->getResult();
        $debitsTiers = array();
        $creditsTiers = array();
        $soldeTiers = array();
        $tiers = array();
        foreach ($montantsTiers as $montantsTier)
        {
            $isAn = (intval($montantsTier['an_id']) == 1);
            $pcc = $montantsTier['balance']->getTiers()->getPcc();
            $idTier = $montantsTier['balance']->getTiers()->getId();

            $debit = $montantsTier['db'];
            $credit = $montantsTier['cr'];
            $solde = $debit - $credit;
            $num_compte = $pcc->getCompte();
            $exercice = $montantsTier['balance']->getExercice();

            if(isset($soldeTiers[$num_compte][$exercice][$idTier])) $soldeTiers[$num_compte][$exercice][$idTier] += $solde;
            else $soldeTiers[$num_compte][$exercice][$idTier] = $solde;

            if(!$isAn)
            {
                if(isset($debits[$num_compte][$exercice])) $debits[$num_compte][$exercice] += $debit;
                else $debits[$num_compte][$exercice] = $debit;

                if(isset($credits[$num_compte][$exercice])) $credits[$num_compte][$exercice] += $credit;
                else $credits[$num_compte][$exercice] = $credit;
            }
            else
            {
                if($solde >= 0)
                {
                    if(isset($debitsTiers[$num_compte][$exercice][$idTier])) $debitsTiers[$num_compte][$exercice][$idTier] += $solde;
                    else $debitsTiers[$num_compte][$exercice][$idTier] = $solde;
                }
                else
                {
                    if(isset($creditsTiers[$num_compte][$exercice][$idTier])) $creditsTiers[$num_compte][$exercice][$idTier] += abs($solde);
                    else $creditsTiers[$num_compte][$exercice][$idTier] = abs($solde);
                }
            }

            if(!in_array($idTier,$tiers)) $tiers[] = $idTier;

            if(!in_array($num_compte,$comptes_str))
            {
                $comptes_str[] = $num_compte;
                $comptes[$num_compte] = $pcc;
            }
        }

        foreach ($comptes_str as $item)
        {
            foreach ($exercices as $exercice)
            {
                foreach ($tiers as $tier)
                {
                    $debit = 0;
                    $credit = 0;
                    if(isset($debitsTiers[$item][$exercice][$tier])) $debit = $debitsTiers[$item][$exercice][$tier];
                    if(isset($creditsTiers[$item][$exercice][$tier])) $credit = $creditsTiers[$item][$exercice][$tier];

                    $solde = $debit - $credit;
                    if($solde >= 0)
                    {
                        if(isset($debits[$item][$exercice])) $debits[$item][$exercice] += $solde;
                        else $debits[$item][$exercice] = $solde;
                    }
                    else
                    {
                        if(isset($credits[$item][$exercice])) $credits[$item][$exercice] += abs($solde);
                        else $credits[$item][$exercice] = abs($solde);
                    }

                    //solde
                    $soldeDCTiers = (isset($soldeTiers[$item][$exercice][$tier])) ? $soldeTiers[$item][$exercice][$tier] : 0;
                    if($soldeDCTiers >= 0)
                    {
                        if(isset($soldes_debit[$item][$exercice])) $soldes_debit[$item][$exercice] += $soldeDCTiers;
                        else $soldes_debit[$item][$exercice] = $soldeDCTiers;
                    }
                    else
                    {
                        if(isset($soldes_credit[$item][$exercice])) $soldes_credit[$item][$exercice] += abs($soldeDCTiers);
                        else $soldes_credit[$item][$exercice] = abs($soldeDCTiers);
                    }
                }
            }
        }

        sort($comptes_str);

        $comptes_str_filtrer = [];
        foreach ($comptes_str as $item)
        {
            if (!is_null($compteDe))
            {
                if ('_'.$item < '_'.$compteDe->getCompte()) continue;
            }
            if (!is_null($compteA))
            {
                if ('_'.$item > '_'.$compteA->getCompte()) continue;
            }

            $comptes_str_filtrer[] = $item;
        }

        return new Balance($comptes,$debits,$credits,$soldes_debit,$soldes_credit,$exercices,$comptes_str_filtrer);
    }

    /**
     * @param Dossier $dossier
     * @param $exercices
     * @param $mois
     * @param $type
     * @param $avec_solde
     * @param null $compteDe
     * @param null $compteA
     * @return Balance
     */
    public function getBalanceTier(Dossier $dossier,$exercices,$mois,$type,$avec_solde,$compteDe = null,$compteA = null)
    {
        $donnees = $this->createQueryBuilder('e')
                        ->select('e as balance,ROUND(SUM(e.debit),2) as db,ROUND(SUM(e.credit),2) as cr')
                        ->leftJoin('e.tiers','tiers')
                        ->leftJoin('e.journalDossier','jd')
                        ->leftJoin('jd.journal','j')
                        ->where('e.dossier = :dossier')
                        ->andWhere('e.tiers IS NOT NULL')
                        ->setParameter('dossier',$dossier)
                        ->andWhere('e.exercice IN (:exercice)')
                        ->setParameter('exercice',$exercices)
                        ->andWhere('tiers.type = :type')
                        ->setParameter('type',$type);
        if(!is_bool($mois))
            $donnees = $donnees->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois',$mois);
        $donnees = $donnees->groupBy('tiers')
                        ->addGroupBy('e.exercice')
                        ->orderBy('tiers.compteStr','ASC')
                        ->andWhere('j.id <> :id_journal')->setParameter('id_journal',1)->getQuery()->getResult();

        $donnees_an = $this->createQueryBuilder('e')
                        ->select('e as balance,ROUND(SUM(e.debit),2) as db,ROUND(SUM(e.credit),2) as cr')
                        ->leftJoin('e.tiers','tiers')
                        ->leftJoin('e.journalDossier','jd')
                        ->leftJoin('jd.journal','j')
                        ->where('e.dossier = :dossier')
                        ->andWhere('e.tiers IS NOT NULL')
                        ->setParameter('dossier',$dossier)
                        ->andWhere('e.exercice IN (:exercice)')
                        ->setParameter('exercice',$exercices)
                        ->andWhere('tiers.type = :type')
                        ->setParameter('type',$type);
        if(!is_bool($mois))
            $donnees_an = $donnees_an->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois',$mois);
        $donnees_an = $donnees_an->groupBy('tiers')
                        ->addGroupBy('e.exercice')
                        ->orderBy('tiers.compteStr','ASC')
                        ->andWhere('j.id = :id_journal')->setParameter('id_journal',1)->getQuery()->getResult();

        $comptes_str = array();
        $comptes = array();
        $debits = array();
        $credits = array();
        $soldes_debit = array();
        $soldes_credit = array();

        foreach($donnees as $balance)
        {
            $compte = $balance['balance']->getTiers();
            $exercice = $balance['balance']->getExercice();
            $debit = $balance['db'];
            $credit = $balance['cr'];
            $num_compte =$compte->getCompteStr();

            $debits[$num_compte][$exercice] = $debit;
            $credits[$num_compte][$exercice] = $credit;

            $solde = $debit - $credit;
            $soldes_debit[$num_compte][$exercice] = $soldes_credit[$num_compte][$exercice] = 0;
            if($solde > 0)
            {
                $soldes_debit[$num_compte][$exercice] = $solde;
            }
            else
            {
                $soldes_credit[$num_compte][$exercice] = abs($solde);
            }

            if(!in_array($num_compte,$comptes_str))
            {
                $comptes[$num_compte] = $compte;
                $comptes_str[] = $num_compte;
            }
        }

        foreach($donnees_an as $balance_an)
        {
            $compte = $balance_an['balance']->getTiers();
            $exercice = $balance_an['balance']->getExercice();
            $solde = $balance_an['db'] - $balance_an['cr'];
            $num_compte = $compte->getCompteStr();

            $debit = $credit = 0;
            if($solde > 0) $debit = $solde;
            else $credit = abs($solde);

            if(!in_array($num_compte,$comptes_str))
            {
                $debits[$num_compte][$exercice] = $soldes_debit[$num_compte][$exercice] = $debit;
                $credits[$num_compte][$exercice] = $soldes_credit[$num_compte][$exercice] = $credit;

                $comptes_str[] = $num_compte;
                $comptes[$num_compte] = $compte;
            }
            else
            {
                if($solde > 0) $debits[$num_compte][$exercice] += $solde;
                else $credits[$num_compte][$exercice] += abs($solde);

                $solde = $debits[$num_compte][$exercice] - $credits[$num_compte][$exercice];
                $soldes_debit[$num_compte][$exercice] = $soldes_credit[$num_compte][$exercice] = 0;
                if($solde > 0) $soldes_debit[$num_compte][$exercice] = $solde;
                else $soldes_credit[$num_compte][$exercice] = abs($solde);
            }
        }

        $delete_index = array();
        if($avec_solde == 0)
        {

            $index = 0;
            foreach($comptes_str as $compte)
            {
                //$compte = $compte_item->getCompteStr();
                $count_solde = 0;
                foreach($exercices as $exercice)
                    try
                    {
                        if(isset($debits[$compte][$exercice]))
                        {
                            if(round($debits[$compte][$exercice],2) == round($credits[$compte][$exercice],2)) $count_solde ++;
                        }
                        else $count_solde++;
                } catch (\Exception $ex) {
                    $count_solde++;
                }

                if($count_solde == count($exercices)) $delete_index[] = $index;
                $index++;
            }
        }

        for($i=count($delete_index)-1; $i>=0 ;$i--)
        {
            unset($comptes_str[$delete_index[$i]]);
        }

        sort($comptes_str);

        $comptes_str_filtrer = [];
        foreach ($comptes_str as $item)
        {
            if (!is_null($compteDe))
            {
                if ('_'.$item < '_'.$compteDe->getCompteStr()) continue;
            }
            if (!is_null($compteA))
            {
                if ('_'.$item > '_'.$compteA->getCompteStr()) continue;
            }

            $comptes_str_filtrer[] = $item;
        }

        return new Balance($comptes,$debits,$credits,$soldes_debit,$soldes_credit,$exercices,$comptes_str_filtrer);
    }

    /**
     * @param Dossier $dossier
     * @param $exercices
     * @param $mois
     * @param $journal
     * @param null $compteDe
     * @param null $compteA
     * @return array
     */
    public function getJournaux(Dossier $dossier,$exercices,$mois,$journal,$compteDe = null,$compteA = null)
    {
        $journal = $this->getEntityManager()->getRepository('AppBundle:JournalDossier')
            ->createQueryBuilder('jd')
            ->where('jd.id = :id')
            ->setParameter('id',$journal)
            ->getQuery()
            ->getOneOrNullResult();

        $query = $this->createQueryBuilder('e')
            ->leftJoin('e.journalDossier','jd')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercice)')
            ->setParameter('exercice',$exercices);

        if(!is_bool($mois))
            $query = $query->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois',$mois);

        if($journal != null)
            $query = $query->andWhere('e.journalDossier = :journalDossier')
                            ->setParameter('journalDossier',$journal);

        $temps = $query->orderBy('e.dateEcr','ASC')
                     ->addOrderBy('jd.codeStr','ASC')
                     ->getQuery()->getResult();

        if (is_null($compteDe) && is_null($compteA)) return $temps;
        $results = [];
        foreach ($temps as $temp)
        {
            $pcc = null;
            if (!is_null($temp->getPcc())) $pcc = $temp->getPcc();
            elseif (!is_null($temp->getTiers()->getPcc())) $pcc = $temp->getTiers()->getPcc();

            if (is_null($pcc)) continue;

            if (!is_null($compteDe))
            {
                if ('_'.$pcc->getCompte() < '_'.$compteDe->getCompte()) continue;
            }
            if (!is_null($compteA))
            {
                if ('_'.$pcc->getCompte() > '_'.$compteA->getCompte()) continue;
            }

            $results[] = $temp;
        }
        return $results;
    }

    /**
     * @param Dossier $dossier
     * @param $exercices
     * @param $mois
     * @param $avecLettre
     * @param int $compte
     * @param null $compteDe
     * @param null $compteA
     * @param bool $regroupeLettre
     * @param bool $anDet
     * @param bool $colSolde
     * @return array
     */
    public function getGrandLivre(Dossier $dossier,$exercices,$mois,$avecLettre,$compte = 0,$compteDe = null,$compteA = null,$regroupeLettre = false,$anDet = false,$colSolde = false)
    {
        $pcc_spes = $this->getEntityManager()->getRepository('AppBundle:Pcc')->find($compte);

        $results = array();
        $comptes = array();
        $comptesObject = array();
        $resultsPccs = array();
        $resultsPccsADs = array();
        $resultsTiers = array();
        $journalADs = $this->getEntityManager()->getRepository('AppBundle:JournalDossier')->getJournalADs($dossier);
        $debutExercice = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateDebut($dossier,$exercices[0]);

        //AD
        $ecrituresADs = $this->createQueryBuilder('e')
            ->leftJoin('e.tiers','tiers')
            ->leftJoin('e.pcc','pcc')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN(:exercice)')
            ->setParameter('exercice',$exercices)
            ->andWhere('e.journalDossier IN (:journalADs)')
            ->setParameter('journalADs',$journalADs);

        //PCC
        $ecrituresPccs = $this->createQueryBuilder('e')
            ->leftJoin('e.pcc','pcc')
            ->leftJoin('e.journalDossier','jd')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercice)')
            ->setParameter('exercice',$exercices)
            ->andWhere('e.journalDossier NOT IN (:journalADs)')
            ->setParameter('journalADs',$journalADs)
            ->andWhere('e.pcc IS NOT NULL');
        //Tiers
        $ecrituresTiers = $this->createQueryBuilder('e')
            ->select('ROUND(SUM(e.debit),2) as db, ROUND(SUM(e.credit),2) as cr,e as ecr')
            ->leftJoin('e.tiers','tiers')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercice)')
            ->setParameter('exercice',$exercices)
            ->andWhere('e.journalDossier NOT IN (:journalADs)')
            ->setParameter('journalADs',$journalADs)
            ->andWhere('e.tiers IS NOT NULL');

        if($pcc_spes != null)
        {
            $ecrituresADs = $ecrituresADs->andWhere('(e.tiers IS NOT NULL AND tiers.pcc = :pcc) OR (e.pcc IS NOT NULL AND pcc = :pcc)')
                ->setParameter('pcc',$pcc_spes);
            $ecrituresPccs = $ecrituresPccs->andWhere('e.pcc = :pcc')
                ->setParameter('pcc',$pcc_spes);
            $ecrituresTiers = $ecrituresTiers->andWhere('tiers.pcc = :pcc')
                ->setParameter('pcc',$pcc_spes);
        }

        if(!is_bool($mois))
        {
            $ecrituresADs = $ecrituresADs->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois',$mois);
            $ecrituresPccs = $ecrituresPccs->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois',$mois);
            $ecrituresTiers = $ecrituresTiers->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois',$mois);
        }

        if (!$avecLettre)
        {
            $ecrituresADs = $ecrituresADs->andWhere("(e.lettrage = '' OR e.lettrage IS NULL)");
            $ecrituresPccs = $ecrituresPccs->andWhere("(e.lettrage = '' OR e.lettrage IS NULL)");
            $ecrituresTiers = $ecrituresTiers->andWhere("(e.lettrage = '' OR e.lettrage IS NULL)");
        }

        if (!$anDet)
            $ecrituresADs = $ecrituresADs
                ->select('ROUND(SUM(e.debit),2) - ROUND(SUM(e.credit),2) as solde,e as ecr')
                ->groupBy('pcc.compte')
                ->addGroupBy('tiers.compteStr');

        //AD
        $ecrituresADs = $ecrituresADs
            //->addSelect('e.image')
            ->orderBy('pcc.compte')
            ->addOrderBy('tiers.compteStr')
            ->getQuery()
            ->getResult();
        //PCC
        $ecrituresPccs = $ecrituresPccs
            ->orderBy('pcc.compte')
            ->addOrderBy('e.dateEcr')
            ->addOrderBy('jd.codeStr')
            ->getQuery()
            ->getResult();
        //TIERS
        $ecrituresTiers = $ecrituresTiers
            ->groupBy('tiers.compteStr')
            ->getQuery()
            ->getResult();

        //AD
        foreach ($ecrituresADs as $AD)
        {
            if (!$anDet)
            {
                $ecrituresAD = $AD['ecr'];
                $solde = $AD['solde'];
            }
            else
            {
                $ecrituresAD = $AD;
                $solde = $AD->getDebit() - $AD->getCredit();
            }
            $pcc = null;
            //pcc
            if($ecrituresAD->getPcc() != null)
            {
                $pcc = $ecrituresAD->getPcc();
                $compte = $pcc->getCompte();
                $grandLivre = new \stdClass();
                $grandLivre->d = ($solde >= 0) ? $solde : 0;
                $grandLivre->c = ($solde < 0) ? abs($solde) : 0;
                $grandLivre->de = $debutExercice->format('d/m/Y');
                $grandLivre->j = $ecrituresAD->getJournalDossier()->getCodeStr();
                $grandLivre->lt = $ecrituresAD->getLettrage();
                $grandLivre->l =  ($anDet) ? $ecrituresAD->getLibelle() : 'SOLDE DES A NOUVEAUX';
                $grandLivre->cp = $compte . ' - ' . $pcc->getIntitule();

                $pi = 'A Nouv';
                if ($anDet)
                {
                    $pi = (!is_null($ecrituresAD->getImage())) ? $ecrituresAD->getImage()->getNom() : $ecrituresAD->getImageStr();
                }
                $grandLivre->pi = $pi;
                if ($anDet && !is_null($ecrituresAD->getImage())) $grandLivre->ip = Boost::boost($ecrituresAD->getImage()->getId());
                $grandLivre->sc = 0;
                $grandLivre->sd = 0;

                if (!$anDet) $resultsPccsADs[$compte] = $grandLivre;
                else
                {
                    if (!array_key_exists($compte,$resultsPccsADs)) $resultsPccsADs[$compte] = [];
                    $resultsPccsADs[$compte][] = $grandLivre;
                }
            }
            else
            {
                $tiers = $ecrituresAD->getTiers();
                $pcc = $tiers->getPcc();
                $compte = $pcc->getCompte();
                $tiersId = $tiers->getId();
                $grandLivre = new \stdClass();
                $grandLivre->d = ($solde >= 0) ? $solde : 0;
                $grandLivre->c = ($solde < 0) ? abs($solde) : 0;
                $grandLivre->de = $debutExercice->format('d/m/Y');
                $grandLivre->j = '';
                $grandLivre->lt = '';
                $grandLivre->l = $tiers->getIntitule() . ' avec solde des AN';
                $grandLivre->cp = $compte . ' - ' . $pcc->getIntitule();
                $grandLivre->pi = '';
                $grandLivre->sc = ($solde < 0) ? abs($solde) : 0;
                $grandLivre->sd = ($solde >= 0) ? $solde : 0;

                if (!array_key_exists($compte,$resultsTiers)) $resultsTiers[$compte] = [];

                if (array_key_exists($tiersId,$resultsTiers[$compte]))
                {
                    $s = $resultsTiers[$compte][$tiersId]->sd - $resultsTiers[$compte][$tiersId]->sc;
                    $s += $solde;

                    $resultsTiers[$compte][$tiersId]->d = ($s >= 0) ? $s : 0;
                    $resultsTiers[$compte][$tiersId]->c = ($s < 0) ? abs($s) : 0;
                    $resultsTiers[$compte][$tiersId]->sd = ($s >= 0) ? $s : 0;
                    $resultsTiers[$compte][$tiersId]->sc = ($s < 0) ? abs($s) : 0;
                    //$resultsTiers[$compte][$tiersId] = $grandLivre;
                }
                else $resultsTiers[$compte][$tiersId] = $grandLivre;
            }

            if(!in_array($compte,$comptes))
            {
                $comptes[] = $compte;
                $comptesObject[$compte] = $pcc;
            }
        }

        $indexRegroupe = [];
        //pcc sans AN
        foreach ($ecrituresPccs as $ecrituresPcc)
        {
            $pcc = $ecrituresPcc->getPcc();
            $compte = $pcc->getCompte();

            $grandLivre = new \stdClass();
            $grandLivre->c = $ecrituresPcc->getCredit();
            $grandLivre->de = $ecrituresPcc->getDateEcr()->format('d/m/Y');
            $grandLivre->d = $ecrituresPcc->getDebit();
            $grandLivre->j = $ecrituresPcc->getJournalDossier()->getCodeStr();
            $grandLivre->lt = $ecrituresPcc->getLettrage();
            $grandLivre->l = $ecrituresPcc->getLibelle();
            $grandLivre->cp = $compte . ' - ' . $pcc->getIntitule();
            $grandLivre->pi = ($ecrituresPcc->getImage() != null) ? $ecrituresPcc->getImage()->getNom() : $ecrituresPcc->getImageStr();

            if($ecrituresPcc->getImage() != null)
                $grandLivre->ip = Boost::boost($ecrituresPcc->getImage()->getId());

            $grandLivre->sc = 0;
            $grandLivre->sd = 0;

            if(!in_array($compte,$comptes))
            {
                $resultsPccs[$compte] = [];
                $comptes[] = $compte;
                $comptesObject[$compte] = $pcc;
            }

            if ($regroupeLettre && $ecrituresPcc->getLettrage() != '')
            {
                if (array_key_exists($compte,$indexRegroupe))
                {
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->c += $grandLivre->c;
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->de = $grandLivre->de;
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->d += $grandLivre->d;
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->j = "";
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->lt = "";
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->l = "Total des lignes lettres";
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->cp = $grandLivre->cp;
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->pi = "";
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->ip = "";
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->sc += 0;
                    $resultsPccs[$compte][$indexRegroupe[$compte]]->sd += 0;
                }
                else
                {
                    $resultsPccs[$compte][] = $grandLivre;
                    $indexRegroupe[$compte] = count($resultsPccs[$compte]) - 1;
                }
            }
            else $resultsPccs[$compte][] = $grandLivre;
        }

        foreach ($ecrituresTiers as $ecrituresTier)
        {
            $ecr = $ecrituresTier['ecr'];
            $tiers = $ecr->getTiers();
            $tiersId = $tiers->getId();
            $pcc = $tiers->getPcc();

            $compte = $pcc->getCompte();

            $debit = $ecrituresTier['db'];
            $credit = $ecrituresTier['cr'];

            if(array_key_exists($compte,$resultsTiers) && array_key_exists($tiersId,$resultsTiers[$compte]))
            {
                $debit += $resultsTiers[$compte][$tiersId]->d;
                $credit += $resultsTiers[$compte][$tiersId]->c;
                $solde = $debit - $credit;

                $resultsTiers[$compte][$tiersId]->d = $debit;
                $resultsTiers[$compte][$tiersId]->c = $credit;

                $resultsTiers[$compte][$tiersId]->sd = ($solde >= 0) ? $solde : 0;
                $resultsTiers[$compte][$tiersId]->sc = ($solde < 0) ? abs($solde) : 0;
            }
            else
            {
                $grandLivre = new \stdClass();
                $grandLivre->de = $ecr->getDateEcr()->format('d/m/Y');
                $grandLivre->j = '';
                $grandLivre->lt = '';
                $grandLivre->l = $tiers->getIntitule();
                $grandLivre->cp = $compte . ' - ' . $pcc->getIntitule();
                $grandLivre->pi = '';
                $grandLivre->d = $debit;
                $grandLivre->c = $credit;
                $solde = $debit - $credit;
                $grandLivre->sd = ($solde >= 0) ? $solde : 0;
                $grandLivre->sc = ($solde < 0) ? abs($solde) : 0;
                $resultsTiers[$compte][$tiersId] = $grandLivre;
            }

            if(!in_array($compte,$comptes))
            {
                $resultsTiers[$compte] = array();
                $comptes[] = $compte;
                $comptesObject[$compte] = $pcc;
            }
        }

        sort($comptes);

        $totalGeneralD = 0;
        $totalGeneralC = 0;
        $totalGeneralSD = 0;
        $totalGeneralSC = 0;

        foreach ($comptes as $compte)
        {
            if (!is_null($compteDe))
            {
                if ('_'.$compte < '_'.$compteDe->getCompte()) continue;
            }
            if (!is_null($compteA))
            {
                if ('_'.$compte > '_'.$compteA->getCompte()) continue;
            }

            $solde = 0;
            $position = 0;
            $sTDebit = 0;
            $sTCredit = 0;
            $sTSoldeDebit = 0;
            $stSoldeCredit = 0;

            if(array_key_exists($compte,$resultsPccsADs))
            {
                if ($anDet)
                {
                    foreach ($resultsPccsADs[$compte] as $resultsPccsAD)
                    {
                        $resultsPccs[$compte][] = $resultsPccsAD;
                    }
                }
                else
                {
                    $debit = $resultsPccsADs[$compte]->d;
                    $credit = $resultsPccsADs[$compte]->c;
                    $solde = $debit - $credit;
                    if($solde >= 0) $resultsPccsADs[$compte]->sd = $solde;
                    else $resultsPccsADs[$compte]->sc = abs($solde);
                    $resultsPccsADs[$compte]->p = $position;
                    $position++;
                    $resultsPccsADs[$compte]->s = $resultsPccsADs[$compte]->sd - $resultsPccsADs[$compte]->sc;
                    $results[] = $resultsPccsADs[$compte];
                    $sTDebit += $debit;
                    $sTCredit += $credit;

                    $sTSoldeDebit = ($solde >= 0) ? $solde : 0;
                    $stSoldeCredit = ($solde < 0) ? abs($solde) : 0;
                }
            }
            if(array_key_exists($compte,$resultsPccs))
            {
                foreach ($resultsPccs[$compte] as &$resultsPcc)
                {
                    $solde += $resultsPcc->d - $resultsPcc->c;
                    if($solde >= 0) $resultsPcc->sd = $solde;
                    else $resultsPcc->sc = abs($solde);
                    $resultsPcc->p = $position;
                    $position++;
                    $resultsPcc->s = $resultsPcc->sd - $resultsPcc->sc;
                    $results[] = $resultsPcc;
                    $sTDebit += $resultsPcc->d;
                    $sTCredit += $resultsPcc->c;

                    $sTSoldeDebit = ($solde >= 0) ? $solde : 0;
                    $stSoldeCredit = ($solde < 0) ? abs($solde) : 0;
                }
            }
            if(array_key_exists($compte,$resultsTiers))
            {
                foreach ($resultsTiers[$compte] as &$resultsTier)
                {
                    $resultsTier->s = $resultsTier->sd - $resultsTier->sc;
                    $results[] = $resultsTier;
                    $sTDebit += $resultsTier->d;
                    $sTCredit += $resultsTier->c;
                    $resultsTier->p = $position;
                    $position++;

                    $sTSoldeDebit += $resultsTier->sd;
                    $stSoldeCredit += $resultsTier->sc;
                }
            }

            $grandLivre = new \stdClass();
            $grandLivre->c = $sTCredit;
            $grandLivre->de = '';
            $grandLivre->d = $sTDebit;
            $grandLivre->j = '';
            $grandLivre->lt = '';
            $grandLivre->l = 'Totaux du compte ' . $compte;
            $grandLivre->cp = $compte . ' - ' .$comptesObject[$compte]->getIntitule();
            $grandLivre->pi = '';
            $grandLivre->sc = $stSoldeCredit;
            $grandLivre->sd = $sTSoldeDebit;
            $grandLivre->s = $grandLivre->sd - $grandLivre->sc;
            //$results[] = $grandLivre;

            $totalGeneralD += $sTDebit;
            $totalGeneralC += $sTCredit;
            $totalGeneralSD += $sTSoldeDebit;
            $totalGeneralSC += $stSoldeCredit;
        }

        $grandLivre = new \stdClass();
        $grandLivre->c = $totalGeneralC;
        $grandLivre->de = '';
        $grandLivre->d = $totalGeneralD;
        $grandLivre->j = '';
        $grandLivre->lt = '';
        $grandLivre->l = '';
        $grandLivre->cp = 'TOTAUX DE LA SELECTION';
        $grandLivre->pi = '';
        $grandLivre->sc = $totalGeneralSC;
        $grandLivre->sd = $totalGeneralSD;
        $grandLivre->s = $grandLivre->sd - $grandLivre->sc;

        $results[] = $grandLivre;
        return $results;
    }

    /**
     * @param Dossier $dossier
     * @param $exercices
     * @param $mois
     * @param $type
     * @param $avecLettre
     * @param int $compte
     * @param null $compteDe
     * @param null $compteA
     * @param bool $regroupeLettre
     * @param bool $anDet
     * @param bool $colSolde
     * @return array
     */
    public function getGrandLivreTiers(Dossier $dossier,$exercices,$mois,$type,$avecLettre,$compte = 0,$compteDe = null,$compteA = null,$regroupeLettre = false,$anDet = false,$colSolde = false)
    {
        $tiersSpes = $this->getEntityManager()->getRepository('AppBundle:Tiers')->find($compte);
        $results = array();
        $comptes = array();
        $comptesObject = array();
        $journalAds = $this->getEntityManager()->getRepository('AppBundle:JournalDossier')->getJournalADs($dossier);
        $debutExercice = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateDebut($dossier,$exercices[0]);
        $montantsADs = $this->createQueryBuilder('e')
            ->leftJoin('e.tiers','tiers')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercice)')
            ->setParameter('exercice',$exercices)
            ->andWhere('e.journalDossier IN (:journalDossiers)')
            ->setParameter('journalDossiers',$journalAds)
            ->andWhere('e.tiers IS NOT NULL')
            ->andWhere('tiers.type = :type')
            ->setParameter('type',$type);

        if (!$anDet)
        {
            $montantsADs = $montantsADs
                ->select('e as gl, SUM(e.debit) as db, SUM(e.credit) as cr')
                ->groupBy('tiers');
        }

        $montants = $this->createQueryBuilder('e')
            ->leftJoin('e.tiers','tiers')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercice)')
            ->setParameter('exercice',$exercices)
            ->andWhere('e.journalDossier NOT IN (:journalDossiers)')
            ->setParameter('journalDossiers',$journalAds)
            ->andWhere('e.tiers IS NOT NULL')
            ->andWhere('tiers.type = :type')
            ->setParameter('type',$type)
            ->orderBy('e.tiers')
            ->addOrderBy('e.dateEcr');

        if (!$avecLettre)
        {
            $montantsADs = $montantsADs->andWhere("(e.lettrage = '' OR e.lettrage IS NULL)");
            $montants = $montants->andWhere("(e.lettrage = '' OR e.lettrage IS NULL)");
        }

        if($tiersSpes != null)
        {
            $montantsADs = $montantsADs
                ->andWhere('e.tiers = :tiersSpec')
                ->setParameter('tiersSpec',$tiersSpes);
            $montants = $montants
                ->andWhere('e.tiers = :tiersSpec')
                ->setParameter('tiersSpec',$tiersSpes);
        }


        if(!is_bool($mois))
        {
            $montantsADs = $montantsADs->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois',$mois);
            $montants = $montants->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois',$mois);
        }
        $montantsADs = $montantsADs->getQuery()->getResult();
        $montants = $montants->getQuery()->getResult();

        $position = 0;
        //AD
        foreach ($montantsADs as $montantsAD)
        {
            $ecriture = (!$anDet) ? $montantsAD['gl'] : $montantsAD;
            $debit = (!$anDet) ? $montantsAD['db'] : $ecriture->getDebit();
            $credit = (!$anDet) ? $montantsAD['cr'] : $ecriture->getCredit();

            $tiers = $ecriture->getTiers();
            $compte = $tiers->getCompteStr();
            $solde = $debit - $credit;
            $grandLivre = new \stdClass();
            $grandLivre->de = $debutExercice->format('d/m/Y');
            $grandLivre->d = ($solde >= 0) ? $solde : 0;
            $grandLivre->c = ($solde < 0) ? abs($solde) : 0;
            $grandLivre->j = $ecriture->getJournalDossier()->getCodeStr();
            $grandLivre->lt = $ecriture->getLettrage();
            $grandLivre->l = (!$anDet) ? 'SOLDE DES A NOUVEAUX' : $ecriture->getLibelle();
            $grandLivre->cp = $tiers->getCompteStr() . ' - ' . $tiers->getIntitule();

            $pi = '';
            if ($anDet)
            {
                $pi = (!is_null($ecriture->getImage())) ? $ecriture->getImage()->getNom() : $ecriture->getImageStr();
            }

            $grandLivre->pi = $pi;
            if ($anDet && !is_null($ecriture->getImage())) $grandLivre->ip = Boost::boost($ecriture->getImage()->getId());
            $grandLivre->p = $position;
            $position++;
            if(!in_array($compte,$comptes))
            {
                $comptes[] = $compte;
                $comptesObject[$compte] = $tiers;
                $results[$compte] = array();
            }
            $results[$compte][] = $grandLivre;
        }

        $indexRegroupe = [];
        //tiers
        foreach ($montants as $montant)
        {
            $tiers = $montant->getTiers();
            $compte = $tiers->getCompteStr();
            $debit = $montant->getDebit();
            $credit = $montant->getCredit();
            $grandLivre = new \stdClass();
            $grandLivre->d = $debit;
            $grandLivre->c = $credit;
            $grandLivre->de = $montant->getDateEcr()->format('d/m/Y');
            $grandLivre->j = $montant->getJournalDossier()->getCodeStr();
            $grandLivre->lt = $montant->getLettrage();
            $grandLivre->l = $montant->getLibelle();
            $grandLivre->cp = $tiers->getCompteStr() . ' - ' . $tiers->getIntitule();
            $grandLivre->pi = ($montant->getImage() != null) ? $montant->getImage()->getNom() : $montant->getImageStr();
            $grandLivre->p = $position;
            if($montant->getImage() != null)
                $grandLivre->ip = Boost::boost($montant->getImage()->getId());
            $position++;

            if(!in_array($compte,$comptes))
            {
                $comptes[] = $compte;
                $comptesObject[$compte] = $tiers;
                $results[$compte] = array();
            }

            if ($regroupeLettre && $montant->getLettrage() != '')
            {
                if (array_key_exists($compte,$indexRegroupe))
                {
                    $results[$compte][$indexRegroupe[$compte]]->c += $grandLivre->c;
                    $results[$compte][$indexRegroupe[$compte]]->de = $grandLivre->de;
                    $results[$compte][$indexRegroupe[$compte]]->d += $grandLivre->d;
                    $results[$compte][$indexRegroupe[$compte]]->j = "";
                    $results[$compte][$indexRegroupe[$compte]]->lt = "";
                    $results[$compte][$indexRegroupe[$compte]]->l = "Total des lignes lettres";
                    $results[$compte][$indexRegroupe[$compte]]->cp = $grandLivre->cp;
                    $results[$compte][$indexRegroupe[$compte]]->pi = "";
                    $results[$compte][$indexRegroupe[$compte]]->ip = "";
                    /*$results[$compte][$indexRegroupe[$compte]]->sc += 0;
                    $results[$compte][$indexRegroupe[$compte]]->sd += 0;*/
                }
                else
                {
                    $results[$compte][] = $grandLivre;
                    $indexRegroupe[$compte] = count($results[$compte]) - 1;
                }
            }
            else $results[$compte][] = $grandLivre;
        }

        sort($comptes);
        $resultsTiers = array();
        $totalGenDebit = 0;
        $totalGenCredit = 0;
        $totalGenSDebit = 0;
        $totalGenSCredit = 0;
        foreach ($comptes as $compte)
        {
            if (!is_null($compteDe))
            {
                if ('_'.$compte < '_'.$compteDe->getCompteStr()) continue;
            }
            if (!is_null($compteA))
            {
                if ('_'.$compte > '_'.$compteA->getCompteStr()) continue;
            }

            $tiers = $comptesObject[$compte];
            $solde = 0;
            $sTotalDebit = 0;
            $sTotalCredit = 0 ;
            foreach ($results[$compte] as &$gl)
            {
                $debit = $gl->d;
                $credit = $gl->c;
                $sTotalDebit += $debit;
                $sTotalCredit += $credit;
                $totalGenDebit += $debit;
                $totalGenCredit += $credit;

                $solde += $debit - $credit;
                $gl->sd = ($solde >= 0) ? $solde : 0;
                $gl->sc = ($solde < 0) ? abs($solde) : 0;
                $gl->s = $solde;
                $resultsTiers[] = $gl;
            }

            $grandLivre = new \stdClass();
            $grandLivre->d = $sTotalDebit;
            $grandLivre->c = $sTotalCredit;
            $grandLivre->de = '';
            $grandLivre->j = '';
            $grandLivre->lt = '';
            $grandLivre->l = 'Totaux du compte '.$compte;
            $grandLivre->cp = $tiers->getCompteStr() . ' - ' . $tiers->getIntitule();
            $grandLivre->pi = '';
            $grandLivre->p = $position;
            $grandLivre->sd = ($solde >= 0) ? $solde : 0;
            $grandLivre->sc = ($solde < 0) ? abs($solde) : 0;
            $grandLivre->s = $solde;

            $soldeGen = $sTotalDebit - $sTotalCredit;
            if($soldeGen >= 0) $totalGenSDebit += $soldeGen;
            else $totalGenSCredit += abs($soldeGen);

            $resultsTiers[] = $grandLivre;
        }

        $grandLivre = new \stdClass();
        $grandLivre->d = $totalGenDebit;
        $grandLivre->c = $totalGenCredit;
        $grandLivre->sd = $totalGenSDebit;
        $grandLivre->sc = $totalGenSCredit;
        $grandLivre->s = $totalGenSDebit - $totalGenSCredit;
        $grandLivre->de = '';
        $grandLivre->j = '';
        $grandLivre->lt = '';
        $grandLivre->l = '';
        $grandLivre->cp = 'TOTAUX DE LA SELECTION';
        $grandLivre->pi = '';
        $grandLivre->p = $position;
        $resultsTiers[] = $grandLivre;

        return $resultsTiers;
    }

    /**
     * @param Dossier $dossier
     * @param $exercices
     * @param $mois
     * @param null $compteDe
     * @param null $compteA
     * @return Balance
     */
    public function getJournalCentralisateur(Dossier $dossier,$exercices,$mois,$compteDe = null,$compteA = null)
    {
        $date_cloture = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateCloture($dossier,$exercices[0]);
        $date_cloture->add(new \DateInterval('P1D'));
        $date_cloture = new \DateTime(($exercices[0] - 1).'-'.$date_cloture->format('m').'-01');

        $donnees = $this->createQueryBuilder('e')
            ->select("e as jnl, SUM(e.debit) as db, SUM(e.credit) as cr, CASE WHEN j.id = 1 THEN '".$date_cloture->format('Ym')."' ELSE DATE_FORMAT(e.dateEcr,'%Y%m') END as ma")
            ->leftJoin('e.journalDossier', 'jd')
            ->leftJoin('jd.journal','j')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->andWhere('e.exercice IN (:exercice)')
            ->setParameter('exercice', $exercices);

        if (!is_bool($mois))
            $donnees = $donnees->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois', $mois);

        $donnees = $donnees->groupBy('ma')
            ->addGroupBy('jd')
            ->orderBy('ma', 'ASC')
            ->addOrderBy('jd.codeStr', 'ASC')
            ->getQuery()->getResult();

        return new Balance($donnees, null, null, null, null, null);
    }

    /**
     * @param Dossier $dossier
     * @param $exercices
     * @param $mois
     * @param $type
     * @param $anciennetes
     * @param $dateAnciennete
     * @param null $compteDe
     * @param null $compteA
     * @return Balance
     */
    public function getBalanceAgeeTier(Dossier $dossier,$exercices,$mois,$type,$anciennetes,$dateAnciennete,$compteDe = null,$compteA = null)
    {
        $max = 10000;
        $anciennetes[] = $max;
        usort($anciennetes, array($this, 'cmp'));
        //$anciennetes = array_reverse($anciennetes, true); //interval trie decroissant

        $ecritures = $this->createQueryBuilder('e')
            ->leftJoin('e.tiers','tiers')
            ->select("
                        e as ecriture,
                        DATE_FORMAT(e.dateEcr,'%Y%m%d') AS jour,    
                        DATE_FORMAT(e.dateEcr,'%m') AS mm,
                        e.exercice ex
                     ")
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN(:exercice)')
            ->setParameter('exercice',$exercices)
            ->andWhere('e.tiers IS NOT NULL')
            ->andWhere('tiers.type = :type')
            ->andWhere("(e.lettrage = '' OR e.lettrage IS NULL)")
            ->setParameter('type',$type);

        if(!is_bool($mois))
            $ecritures = $ecritures->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois',$mois);

        $ecritures = $ecritures->orderBy('e.id','ASC')->getQuery()->getResult();

        $tiersObjects = [];
        $montantsTempsTiers = [];
        $montantsTotalTiers = [];
        $soldes = [];
        //calcul montants tiers
        foreach ($ecritures as $ecriture)
        {
            $ecr = $ecriture['ecriture'];
            $debit = $ecr->getDebit();
            $credit = $ecr->getCredit();
            $tiersId = $ecr->getTiers()->getId();
            $dateEcriture = Boost::getDateByString($ecriture['jour'],'-',3);
            $difference = abs(intval($dateAnciennete->diff($dateEcriture)->days));

            if(!array_key_exists($tiersId,$tiersObjects)) $tiersObjects[$tiersId] = $ecr->getTiers();
            for($i = 0; $i < count($anciennetes) ; $i++)
            {
                if($difference <= intval($anciennetes[$i]))
                {
                    $key = $anciennetes[$i].'-'.$tiersId;
                    $m = $debit - $credit;
                    if(array_key_exists($key,$montantsTempsTiers)) $montantsTempsTiers[$key] += $m;
                    else $montantsTempsTiers[$key] = $m;

                    $soldes[$tiersId][$anciennetes[$i]] = $montantsTempsTiers[$key];
                    break;
                }
            }

            $keyTotal = $tiersId;
            if(array_key_exists($keyTotal,$montantsTotalTiers))
            {
                $montantsTotalTiers[$keyTotal] = round($montantsTotalTiers[$keyTotal] + $debit - $credit,2);
            }
            else $montantsTotalTiers[$keyTotal] = round($debit - $credit,2);
        } //$montantsTotalTiers[$tiersId] et $montantsTempsTiers[$anciennetes[$i].'-'.$tiersId]

        $debiteurs = [];
        $crediteurs = [];
        $libelles = [];
        foreach ($tiersObjects as $key => $tiersObject)
        {
            if (!is_null($compteDe))
            {
                if ('_'.$tiersObject->getCompteStr() < '_'.$compteDe->getCompteStr()) continue;
            }
            if (!is_null($compteA))
            {
                if ('_'.$tiersObject->getCompteStr() > '_'.$compteA->getCompteStr()) continue;
            }

            if ($montantsTotalTiers[$key] > 0) $debiteurs[] = $key;
            else if($montantsTotalTiers[$key] <= 0) $crediteurs[] = $key;
            $libelles[$key] = $tiersObject->getIntitule();
        }

        sort($debiteurs);
        sort($crediteurs);
        return new Balance($debiteurs,$crediteurs,$libelles,$soldes,$anciennetes,$montantsTotalTiers,$tiersObjects);
    }
    //FIN ETAT DE BASE

    /**
     * @param Dossier $dossier
     * @param $exercices
     * @param $mois
     * @return Balance
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getControle(Dossier $dossier,$exercices,$mois)
    {
        $resultats = array();
        $erreur = false;

        $req_1_5 = $this->createQueryBuilder('e')
            ->select('ROUND(SUM(e.debit),2) - ROUND(SUM(e.credit),2) as resultat')
            ->leftJoin('e.pcc','pcc')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercice)')
            ->setParameter('exercice',$exercices);
        if(!is_bool($mois))
            $req_1_5 = $req_1_5->andWhere("DATE_FORMAT(e.dateEcr,'%m') IN (:mois)")
                ->setParameter('mois',$mois);

        $req_6_7 = clone $req_1_5;

        $r_1_5 = $req_1_5->andWhere('e.tiers IS NOT NULL OR SUBSTRING(pcc.compte,1,1) < 6')->getQuery()->getOneOrNullResult()['resultat'];
        $r_6_7 = $req_6_7->andWhere('e.tiers IS NULL')->andWhere('SUBSTRING(pcc.compte,1,1) > 5')->getQuery()->getOneOrNullResult()['resultat'];

        if($r_1_5 + $r_6_7 != 0) $erreur = true;

        $resultats['Balance'] = $r_1_5;
        $resultats['Grand livre'] = $r_1_5;

        return new Balance($resultats,$erreur,null,null,null,null);
    }

    /**
     * @param $exercice
     * @param $dossier
     * @return \DateTime|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDerniereMAJ($exercice,$dossier)
    {
        $dossier = $this->getEntityManager()->getRepository('AppBundle:Dossier')
            ->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id', $dossier)
            ->getQuery()
            ->getOneOrNullResult();

        $date = $this->createQueryBuilder('e')
            ->leftJoin('e.historiqueUpload','h')
            ->select('MAX(h.dateUpload) as date')
            ->where('e.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->andWhere('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->groupBy('e.dossier')
            ->getQuery()
            ->getOneOrNullResult();

        return ($date != null) ? (new \DateTime(explode(' ',$date['date'])[0])) : null;
    }

    /**
     * @param Dossier $dossier
     * @param $pccs
     * @param $exercices
     * @param $moiss
     * @param Rubrique $rubrique
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getEcritureOperandes(Dossier $dossier,$pccs,$exercices,$moiss,Rubrique $rubrique)
    {
        $date_debut = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->getDateDebut($dossier,$exercices[0]);

        $debut_m_d = $date_debut->format('-m-d');
        $debut_m = $date_debut->format('m');

        $tiersInPcc = array();
        foreach ($pccs as $pcc)
        {
            if(substr($pcc->getCompte(), 0, 4) == '4010') $tiersInPcc[] = 0;
            if(substr($pcc->getCompte(), 0, 4) == '4110') $tiersInPcc[] = 1;
        }

        $ecritures = $this->createQueryBuilder('e')
            ->leftJoin('e.journalDossier','jd')
            ->leftJoin('jd.journal','j')
            ->leftJoin('e.tiers','t')
            ->select("  e as ecr,
                        ROUND(SUM(e.debit),2) AS db,
                        ROUND(SUM(e.credit),2) AS cr,
                        CASE WHEN (e.dateEcr > CONCAT(CASE WHEN ($debut_m = 1) THEN e.exercice ELSE CAST((e.exercice - 1) AS integer) END,'$debut_m_d')) 
                            THEN DATE_FORMAT(e.dateEcr,'%Y%m') 
                            ELSE CONCAT(CASE WHEN ($debut_m = 1) THEN e.exercice ELSE CAST((e.exercice - 1) AS integer) END,'$debut_m') END AS ma,
                        CASE WHEN j.id = 1 THEN 1 ELSE 0 END AS an,
                        CASE WHEN (e.dateEcr > CONCAT(CASE WHEN ($debut_m = 1) THEN e.exercice ELSE CAST((e.exercice - 1) AS integer) END,'$debut_m_d')) 
                            THEN DATE_FORMAT(e.dateEcr,'%m') 
                            ELSE '$debut_m' END AS mm,
                        CASE WHEN e.tiers IS NULL THEN 0 ELSE t.id END AS ts,
                        e.exercice ex
                     ")
            ->where('e.dossier = :dossier')
            ->andWhere('e.exercice IN (:exercices)')
            ->setParameter('exercices',$exercices)
            ->having("mm IN(:moiss)")
            ->setParameter('moiss',$moiss);

        if(count($tiersInPcc) > 0) $ecritures = $ecritures->andWhere('(e.pcc IN (:pccs) OR (t.type IN (:types) AND t.dossier = :dossier))')->setParameter('types',$tiersInPcc);
        else $ecritures = $ecritures->andWhere('e.pcc IN (:pccs)');

        //comptes auxilliaires
        if($rubrique->getTypeCompte() == 1)
            $ecritures = $ecritures->andWhere('e.tiers IS NOT NULL');
        elseif($rubrique->getTypeCompte() == 2)
            $ecritures = $ecritures->andWhere('e.tiers IS NOT NULL')->andWhere("(e.lettrage IS NULL OR e.lettrage = '')");

        $ecritures = $ecritures->setParameter('dossier',$dossier)->setParameter('pccs',$pccs)
            ->groupBy('e.exercice')->addGroupBy('ma')
            ->addGroupBy('e.pcc')->addGroupBy('e.tiers')->addGroupBy('an')
            ->getQuery()->getResult();

        return $ecritures;
    }

    /**
     * @param Dossier $dossier
     * @param $pccs
     * @param $exercices
     * @param $mois
     * @param int $typeCompte
     * @return array
     */
    public function getEcrituresPcc(Dossier $dossier,$pccs,$exercices,$mois,$typeCompte = 0)
    {
        $ecritures = $this->createQueryBuilder('e')
            ->leftJoin('e.tiers','t')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('(e.pcc IN (:pccs) OR t.pcc IN(:pccs))')
            ->setParameter('pccs',$pccs)
            ->andWhere('e.exercice IN (:exercices)')
            ->setParameter('exercices',$exercices);

        //0 : compte collectif; 1 : compte auxilliare ; 2 : factures non payes
        if($typeCompte == 1) $ecritures = $ecritures->andWhere('e.tiers IS NOT NULL');
        else if($typeCompte == 2) $ecritures = $ecritures->andWhere('e.lettrage IS NULL');

        return $ecritures->orderBy('e.pcc')->addOrderBy('e.tiers')->getQuery()->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param $pccs
     * @param $exercices
     * @param $mois
     * @param int $typeCompte
     * @return array
     */
    public function getEcrituresPccGrouped(Dossier $dossier,$pccs,$exercices,$mois,$typeCompte = 0)
    {
        $ecritures = $this->createQueryBuilder('e')
            ->select("  ROUND(SUM(e.debit),2) AS db,
                        e.exercice AS exe,
                        ROUND(SUM(e.credit),2) AS cr,
                        DATE_FORMAT(e.dateEcr,'%Y%m') AS ym,
                        CASE WHEN j.id = 1 THEN 1 ELSE 0 END AS an,
                        CASE WHEN (e.lettrage = '' OR e.lettrage IS NULL) THEN '' ELSE 'A' END AS lettre,
                        pcc.compte AS cpt,
                        t.compteStr AS cpt_tiers,
                        e as ecr")
            ->leftJoin('e.tiers','t')
            ->leftJoin('e.pcc','pcc')
            ->leftJoin('e.journalDossier','jd')
            ->leftJoin('jd.journal','j')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercices)')
            ->setParameter('exercices',$exercices)
            ->andWhere('(e.pcc IN (:pccs) AND e.pcc IS NOT NULL OR t.pcc IN(:pccs) AND e.tiers IS NOT NULL)')
            ->setParameter('pccs',$pccs);

        //0 : compte collectif; 1 : compte auxilliare ; 2 : factures non payes
        if($typeCompte == 1) $ecritures = $ecritures->andWhere('e.tiers IS NOT NULL');
        else if($typeCompte == 2) $ecritures = $ecritures->andWhere('e.lettrage IS NULL');

        return $ecritures
            ->groupBy('e.exercice')
            ->addGroupBy('e.pcc')
            ->addGroupBy('e.tiers')
            ->addGroupBy('ym')
            ->addGroupBy('an')
            ->addGroupBy('lettre')
            ->orderBy('e.pcc')
            ->addOrderBy('e.tiers')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param $pccs
     * @param $exercices
     * @param $mois
     * @param int $typeCompte
     * @param $anneeMoisLimit
     * @return array
     */
    public function getEcrituresPccGroupedLimited(Dossier $dossier,$pccs,$exercices,$mois,$typeCompte = 0,$anneeMoisLimit = null)
    {
        $ecritures = $this->createQueryBuilder('e')
            ->select("  ROUND(SUM(e.debit),2) AS db,
                        ROUND(SUM(e.credit),2) AS cr,
                        e.exercice AS exe,
                        DATE_FORMAT(e.dateEcr,'%Y%m') AS ym,
                        CASE WHEN j.id = 1 THEN 1 ELSE 0 END AS an,
                        CASE WHEN (e.lettrage = '' OR e.lettrage IS NULL) THEN '' ELSE 'A' END AS lettre,
                        CASE WHEN (e.pcc IS NULL) THEN SUBSTRING(pcct.compte,1,1) ELSE SUBSTRING(pcc.compte,1,1) END AS c,
                        CASE WHEN (e.pcc IS NULL) THEN CONCAT(pcct.id,'-',t.id) ELSE CONCAT(pcc.id,'-0') END AS pccTiers,
                        e as ecr")
            //->addSelect('CASE WHEN (e.pcc IS NULL) THEN pcct ELSE pcc END AS pccObj')
            ->leftJoin('e.tiers','t')
            ->leftJoin('e.pcc','pcc')
            ->leftJoin('t.pcc','pcct')
            ->leftJoin('e.journalDossier','jd')
            ->leftJoin('jd.journal','j')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercices)')
            ->setParameter('exercices',$exercices)
            ->andWhere('(e.pcc IN (:pccs) OR t.pcc IN(:pccs))')
            //->andWhere('e.pcc IS NOT NULL')
            ->setParameter('pccs',$pccs);

        if ($anneeMoisLimit != null)
            $ecritures = $ecritures->having("ym <= :anneeMoisLimit")->setParameter('anneeMoisLimit',$anneeMoisLimit);

            /*$ecritures = $ecritures->having("(
                    (ym <= :anneeMoisLimit AND c < '6') OR
                    (ym <= :anneeMoisLimit AND c >= '6')
            )")->setParameter('anneeMoisLimit',$anneeMoisLimit);*/

        //0 : compte collectif; 1 : compte auxilliare ; 2 : factures non payes
        if($typeCompte == 1) $ecritures = $ecritures->andWhere('e.tiers IS NOT NULL');
        else if($typeCompte == 2) $ecritures = $ecritures->andWhere('e.lettrage IS NULL');

        return $ecritures
            ->groupBy('e.exercice')
            ->addGroupBy('e.pcc')
            ->addGroupBy('e.tiers')
            ->addGroupBy('ym')
            ->addGroupBy('an')
            ->addGroupBy('lettre')
            ->orderBy('e.pcc')
            ->addOrderBy('e.tiers')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param $pccs
     * @param $exercices
     * @param $moiss
     * @param Rubrique $rubrique
     * @return array
     */
    public function getEcrituresOperandesParDate(Dossier $dossier,$pccs,$exercices,$moiss,Rubrique $rubrique)
    {
        $date_debut = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->getDateDebut($dossier,$exercices[0]);

        $debut_m_d = $date_debut->format('-m-d');
        $debut_m = $date_debut->format('m');
        $tiersInPcc = array();
        foreach ($pccs as $pcc)
        {
            if(substr($pcc->getCompte(), 0, 4) == '4010') $tiersInPcc[] = 0;
            if(substr($pcc->getCompte(), 0, 4) == '4110') $tiersInPcc[] = 1;
        }

        /**
        e as ecriture,
        CASE WHEN (e.dateEcr > CONCAT(CASE WHEN ($debut_m = 1) THEN e.exercice ELSE CAST((e.exercice - 1) AS integer) END,'$debut_m_d'))
        THEN DATE_FORMAT(e.dateEcr,'%Y%m%d')
        ELSE CONCAT(CASE WHEN ($debut_m = 1) THEN e.exercice ELSE CAST((e.exercice - 1) AS integer) END,'$debut_m','01') END AS jour,
        CASE WHEN (e.dateEcr > CONCAT(CASE WHEN ($debut_m = 1) THEN e.exercice ELSE CAST((e.exercice - 1) AS integer) END,'$debut_m_d'))
        THEN DATE_FORMAT(e.dateEcr,'%m')
        ELSE '$debut_m' END AS mm,
        e.exercice ex
         */


        $ecritures = $this->createQueryBuilder('e')
            ->leftJoin('e.tiers','tiers')
            ->select("
                        e as ecriture,
                        DATE_FORMAT(e.dateEcr,'%Y%m%d') AS jour,    
                        DATE_FORMAT(e.dateEcr,'%m') AS mm,
                        e.exercice ex
                     ")
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN (:exercices)')
            ->setParameter('exercices',$exercices)
            ->andWhere('e.tiers IS NOT NULL')
            ->andWhere('tiers.type IN (:types)')
            ->setParameter('types',$tiersInPcc)
            ->having("mm IN(:moiss)")
            ->setParameter('moiss',$moiss)
            ->orderBy('e.id','ASC');

        //comptes auxilliaires
        if($rubrique->getTypeCompte() == 1)
            $ecritures = $ecritures->andWhere('e.tiers IS NOT NULL');
        elseif($rubrique->getTypeCompte() == 2)
            $ecritures = $ecritures->andWhere('e.tiers IS NOT NULL')->andWhere("(e.lettrage IS NULL OR e.lettrage = '')");

        return $ecritures->getQuery()->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param $pccs
     * @param $exercices
     * @param $moiss
     * @param bool $withLettre
     * @return array
     */
    public function getEcrituresOperandesParDateV2(Dossier $dossier,$pccs,$exercices,$moiss,$withLettre = true)
    {
        $ecritures = $this->createQueryBuilder('e')
            ->leftJoin('e.tiers','tiers')
            ->select("
                        e as ecriture,
                        DATE_FORMAT(e.dateEcr,'%Y%m%d') AS jour,    
                        DATE_FORMAT(e.dateEcr,'%m') AS mm,
                        e.exercice ex
                     ")
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice IN(:exercice)')
            ->setParameter('exercice',$exercices)
            ->andWhere('e.tiers IS NOT NULL')
            ->andWhere('tiers.pcc in (:pccs)')
            ->setParameter('pccs',$pccs)
            ->having("mm IN(:moiss)")
            ->setParameter('moiss',$moiss)
            ->orderBy('e.id','ASC');

        if (!$withLettre)
            $ecritures = $ecritures->andWhere("(e.lettrage = '' OR e.lettrage IS NULL)");

        return $ecritures->getQuery()->getResult();
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public function cmp($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }


    public function getEcrituresByImage(Image $image, &$typeEcriture){
        /** @var Ecriture[] $ecritures */
        $ecritures = $this->createQueryBuilder('e')
            ->where('e.image = :image')
            ->setParameter('image', $image)
            ->getQuery()
            ->getResult();

        $sommeDebit = 0;
        $sommeCredit = 0;

        $typeEcriture = 0;

        if(count($ecritures) > 0) {
            foreach ($ecritures as $ecriture) {
                $sommeDebit += $ecriture->getDebit();
                $sommeCredit += $ecriture->getCredit();
            }

            if (abs($sommeCredit - $sommeDebit) <= 0.1 ) {
                $typeEcriture = 1;
                return $ecritures;
            }
            else{
                $typeEcriture = -1;
            }

        }
        return [];
    }

    /**
     * @param IndicateurTbCle $indicateurTbCle
     * @param Dossier $dossier
     * @return array
     */
    public function countsIndicateurTbCle(IndicateurTbCle $indicateurTbCle, Dossier $dossier)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $req = '        
            SELECT COUNT(e.id) AS ISA,e.dossier_id, e.exercice 
            FROM ecriture e 
            JOIN pcc p ON (p.id = e.pcc_id) 
            WHERE e.dossier_id = :DOSSIER_ID AND 
                e.libelle LIKE :CLE_LIKE  
        '; // AND p.compte LIKE :COMPTE_512 AND e.pcc_id IS NOT NULL

        if (intval($indicateurTbCle->getSens() == 1))
            $req .= ' AND e.debit <> 0 ';
        elseif (intval($indicateurTbCle->getSens() == 2))
            $req .= ' AND e.credit <> 0 ';

        $req .= 'GROUP BY e.dossier_id, e.exercice';

        $prep = $pdo->prepare($req);
        $prep->execute([
            'DOSSIER_ID' => $dossier->getId(),
            'CLE_LIKE' => '%' . $indicateurTbCle->getCle() . '%'
            //,'COMPTE_512' => '512%'
        ]);

        $res = [];
        foreach ($prep->fetchAll() as $re)
        {
            $res[$re->exercice] = $re->ISA;
        }

        return $res;
    }

    /**
     * @param IndicateurTbCle $indicateurTbCle
     * @param Dossier $dossier
     * @param $exercice
     * @return Ecriture[]
     */
    public function occurenceDetails(IndicateurTbCle $indicateurTbCle, Dossier $dossier, $exercice)
    {
        $res = $this->createQueryBuilder('e')
            ->leftJoin('e.pcc', 'p')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->andWhere('e.libelle LIKE :cle')
            ->setParameter('cle','%'.$indicateurTbCle->getCle().'%');

        if ($indicateurTbCle->getSens() == 1)
            $res = $res->andWhere('e.debit <> 0');
        elseif ($indicateurTbCle->getSens() == 2)
            $res = $res->andWhere('e.credit <> 0');

        return $res->getQuery()->getResult();
    }


    public function getInfoJournal($dossier, $journalDossierId, $exercice, $interval){
        $dossierE = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->find($dossier);

        $journalDossierEs = [];

        if (intval($journalDossierId) !== 0) {
            $journalDossierEs [] = $this->getEntityManager()
                ->getRepository('AppBundle:JournalDossier')
                ->find($journalDossierId);
        }
        else{
            $journalDossierEs = $this->getEntityManager()
                ->getRepository('AppBundle:JournalDossier')
                ->findBy(['dossier' => $dossierE]);
        }

        $ecritures = [];
        $con = new CustomPdoConnection();
        $pdo = $con->connect();



        $id = 0;


        /** @var JournalDossier $journalDossier */
        foreach ($journalDossierEs as $journalDossier) {

            if(strpos(strtolower($journalDossier->getCodeStr()), 'bq') !== FALSE){
                continue;
            }

            $query = "select i.nom as image_nom, i.id as image_id, e.libelle, e.lettrage as lettre, e.date_ecr,
                             debit, credit, p.id as pcc_id, p.compte as pcc_compte, 
                            t.compte_str as tiers_compte , t.id as tiers_id  
                            from ecriture e 
                            left join image i on i.id = e.image_id 
                            left join pcc p on p.id = e.pcc_id
                            left join tiers t on t.id = e.tiers_id";
            $where = " where 
                            e.exercice = :exercice and e.dossier_id = :dossier and journal_dossier_id = :journaldossier ";

            $params =[];
            if (count($interval) != 12) {
                $conditions = '';
                $iteration = 0;
                foreach ($interval as $i) {
                    $conditions .= 'e.date_ecr >= :min_' . $iteration . ' AND e.date_ecr < :max_' . $iteration . ' ';
                    if ($iteration != count($interval) - 1) $conditions .= ' OR ';

                    /** @var \DateTime $min */
                    $min = $i->min;
                    $min->setTime(0, 0, 0);
                    /** @var \DateTime $max */
                    $max = $i->max;
                    $max->setTime(23, 59, 59);

                    $params ['min_' . $iteration] = $min->format('Y-m-d');
                    $params['max_' . $iteration] = $max->format('Y-m-d');

                    $iteration++;
                }

                if ($conditions !== '')
                    $where .= ' AND (' . $conditions . ')';
            }

            $query .= $where." order by e.journal_dossier_id,e.date_ecr,e.id";

            $tmp = [
                'exercice' => $exercice,
                'dossier' => $dossier,
                'journaldossier' => $journalDossier->getId()
            ];

            $params = array_merge($tmp, $params);

            $prep = $pdo->prepare($query);

            $prep->execute($params);

            $ecritureTmps = $prep->fetchAll();

            $compta = [];
            $balanced = false;

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($ecritureTmps as $ecr) {

                if($ecr->image_nom == 'ESZ000K2J'){
                    $izy = true;
                }

                if ($ecr->pcc_id !== null) {
                    $typecompte = 'pcc';
                    $compte_id = $ecr->pcc_id;
                    $compte = $ecr->pcc_compte;
                } else {
                    $typecompte = 'tiers';
                    $compte_id = $ecr->tiers_id;
                    $compte = $ecr->tiers_compte;
                }

                $comptaDebit = [];
                $comptaCredit = [];

                if ($ecr->debit != 0 || $ecr->credit != 0) {
                    if($ecr->debit == 0) {
                        $comptaCredit = [  'montant' => $ecr->credit,
                            'compte' => $compte,
                            'compte_id' => $compte_id,
                            'type_compte' => $typecompte];
                        $totalCredit += $ecr->credit;
                    }
                    elseif ($ecr->credit == 0) {
                        $comptaDebit = [ 'montant' => $ecr->debit,
                            'compte' => $compte,
                            'compte_id' => $compte_id,
                            'type_compte' => $typecompte
                        ];
                        $totalDebit += $ecr->debit;
                    }
                }

                if(abs((float)$totalCredit - (float) $totalDebit) <= 0.1 ) {
                    $balanced = true;
                }

                if(count($comptaDebit) > 0)
                    $compta['debit'][] = $comptaDebit;

                if(count($comptaCredit) > 0)
                    $compta['credit'][] = $comptaCredit;

                $ecritures[$id] = [
                    'libelle' => $ecr->libelle,
                    'journal' => $journalDossier->getCodeStr(),
                    'lettre' => $ecr->lettre,
                    'devise' => '',
                    'compta' => $compta,
                    'image' => $ecr->image_nom,
                    'date' => $ecr->date_ecr,
                    'remarque' => '',
                    'journal_dossier' => ($journalDossier === null) ? '' : $journalDossier->getId()
                ];

                if($balanced){
                    $totalDebit=0;
                    $totalCredit = 0;
                    $balanced = false;

                    $compta = [];


                    $id++;
                }
            }
        }
        return $ecritures;
    }

    public function getInfoCentralisateur($dossierId, $exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "select round(sum(e.debit),2) as debit, round(sum(e.credit),2) as credit, 
                        case when jd.journal_id = 1 then ".$exercice."-01  else DATE_FORMAT(e.date_ecr, '%Y-%m')  end as mois, 
                        jd.code_str , jd.libelle, jd.id as journal_dossier_id
                        from ecriture e 
                        inner join journal_dossier jd on jd.id = e.journal_dossier_id
                        inner join dossier d on d.id = e.dossier_id                         
                      ";

        $where = " where e.exercice = :exercice and e.dossier_id = :dossier ";

        $groupBy = "  group by mois, jd.id order by mois, jd.code_str;";

        $params = [];


        $query .= $where ." ". $groupBy;
		
		$prep = $pdo->prepare($query);

        $tmp = [
            'exercice' => $exercice,
            'dossier' => $dossierId
        ];

        $params = array_merge($tmp, $params);

        $prep->execute($params);

        return $prep->fetchAll();
    }

    public function getMoisTraites($client,$dossier,$exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $client  = Boost::deboost($client,$this);
        $dossier  = Boost::deboost($dossier,$this);

        if ($dossier == "") {
            $query = " select date_format(e.date_ecr,'%Y-%m') as date_ecr, date_format(e.date_ecr,'%m') as mois, date_format(date_ecr,'%Y') as annee, d.id as dossier_id, d.cloture as cloture
                       from ecriture e
                       inner join dossier d on (e.dossier_id=d.id)
                       inner join site s on (d.site_id=s.id)
                       inner join client c on (s.client_id=c.id)
                       where c.id = :client
                       and e.exercice = :exercice";

            $query .= "   and (
                            (d.status = 1 and d.active = 1)
                            or (d.status <> 1 
                                AND d.status is not null 
                                AND d.status_debut > " . $exercice . " 
                                AND d.active = 1
                            )
                        )";

            $prep = $pdo->prepare($query);


            $params = array(
                'exercice' => $exercice,
                'client' => $client
            );
            

        } else {
            $query = " select date_format(e.date_ecr,'%Y-%m') as date_ecr, date_format(e.date_ecr,'%m') as mois, date_format(e.date_ecr,'%Y') as annee, d.id as dossier_id, d.cloture as cloture
                       from ecriture e
                       inner join dossier d on (e.dossier_id=d.id)
                       where dossier_id = :dossier
                       and exercice = :exercice";

            $prep = $pdo->prepare($query);


            $params = array(
                'dossier' => $dossier,
                'exercice' => $exercice
            );
            
        }


        $prep->execute($params);

        $results = $prep->fetchAll();

        $mois = array();

        foreach ($results as $value) {

            $debutFin = $this->beginEnd($exercice,$value->cloture);

            $moisCloture = $this->getBetweenDate($debutFin);

            if (!in_array($value->date_ecr, $mois)) {
                array_push($mois, $value->date_ecr);
            }

        }

        return count($mois);

    }

    public function beginEnd($exercice, $cloture)
    {
        if ($cloture < 9) {
            $debutMois = ($exercice - 1) . '-0' . ($cloture + 1) . '-01';
        } else if ($cloture >= 9 and $cloture < 12) {
            $debutMois = ($exercice - 1) . '-' . ($cloture + 1) . '-01';
        } else {
            $debutMois = ($exercice) . '-01-01';
        }
        if ($cloture < 10) {
            $finMois = ($exercice) . '-0' . ($cloture) . '-01';
        } else {
            $finMois = ($exercice) . '-' . ($cloture) . '-01';
        }

        $result          = array();
        $result['start'] = $debutMois;
        $result['end']   = $finMois;

        return $result;

    }

    protected function getBetweenDate($debutFin)
    {
        $time1  = strtotime($debutFin['start']);
        $time2  = strtotime($debutFin['end']);
        $my     = date('mY', $time2);
        $months = array(date('Y-m', $time1));
        while ($time1 < $time2) {
            $time1 = strtotime(date('Y-m', $time1) . ' +1 month');
            if (date('mY', $time1) != $my && ($time1 < $time2))
                $months[] = date('Y-m', $time1);
        }
        $months[] = date('Y-m', $time2);
        return $months;
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @param $type
     * @param array $inteval
     * @param int $index
     * @param int $dateType
     * @return array
    */
    public function getFournisseurClientNonPayees(Dossier $dossier, $exercice, $type, $inteval = [90,100000],$index = 0,$dateType = 0)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $results = [];
        $donnees = $this->createQueryBuilder('e')
                        ->select('e as balance,ROUND(SUM(e.debit),2) as db,ROUND(SUM(e.credit),2) as cr')
                        ->leftJoin('e.tiers','tiers')
                        ->leftJoin('e.journalDossier','jd')
                        ->leftJoin('jd.journal','j')
                        ->where('e.dossier = :dossier')
                        ->andWhere('e.tiers IS NOT NULL')
                        ->setParameter('dossier',$dossier)
                        ->andWhere('e.exercice IN (:exercice)')
                        ->setParameter('exercice',$exercice)
                        ->andWhere('tiers.type = :type')
                        ->setParameter('type',$type);
        $donnees = $donnees->groupBy('tiers')
                        ->addGroupBy('e.exercice')
                        ->orderBy('tiers.compteStr','ASC')
                        ->andWhere('j.id <> :id_journal')->setParameter('id_journal',1)->getQuery()->getResult();

        $donnees_an = $this->createQueryBuilder('e')
                        ->select('e as balance,ROUND(SUM(e.debit),2) as db,ROUND(SUM(e.credit),2) as cr')
                        ->leftJoin('e.tiers','tiers')
                        ->leftJoin('e.journalDossier','jd')
                        ->leftJoin('jd.journal','j')
                        ->where('e.dossier = :dossier')
                        ->andWhere('e.tiers IS NOT NULL')
                        ->setParameter('dossier',$dossier)
                        ->andWhere('e.exercice IN (:exercice)')
                        ->setParameter('exercice',$exercice)
                        ->andWhere('tiers.type = :type')
                        ->setParameter('type',$type);
        $donnees_an = $donnees_an->groupBy('tiers')
                        ->addGroupBy('e.exercice')
                        ->orderBy('tiers.compteStr','ASC')
                        ->andWhere('j.id = :id_journal')->setParameter('id_journal',1)->getQuery()->getResult();

        $comptes_str = array();
        $comptes = array();
        $debits = array();
        $credits = array();

        foreach($donnees as $balance)
        {
            $compte = $balance['balance']->getTiers();
            $exercice = $balance['balance']->getExercice();
            $debit = $balance['db'];
            $credit = $balance['cr'];
            $num_compte =$compte->getCompteStr();

            $debits[$num_compte][$exercice] = $debit;
            $credits[$num_compte][$exercice] = $credit;

            $solde = $debit - $credit;

            if(!in_array($num_compte,$comptes_str))
            {
                $comptes[$num_compte] = $compte;
                $comptes_str[] = $num_compte;
            }
        }
        
        foreach($donnees_an as $balance_an)
        {
            $compte = $balance_an['balance']->getTiers();
            $exercice = $balance_an['balance']->getExercice();
            $solde = $balance_an['db'] - $balance_an['cr'];
            $num_compte = $compte->getCompteStr();

            $debit = $credit = 0;
            if($solde > 0) $debit = $solde;
            else $credit = abs($solde);

            if(!in_array($num_compte,$comptes_str))
            {
                $debits[$num_compte][$exercice] = $soldes_debit[$num_compte][$exercice] = $debit;
                $credits[$num_compte][$exercice] = $soldes_credit[$num_compte][$exercice] = $credit;

                $comptes_str[] = $num_compte;
                $comptes[$num_compte] = $compte;
            }
            else
            {
                if($solde > 0) $debits[$num_compte][$exercice] += $solde;
                else $credits[$num_compte][$exercice] += abs($solde);
            }
        }

        $delete_index = array();
        $indexCpt = 0;
        foreach($comptes_str as $compte)
        {
            //$compte = $compte_item->getCompteStr();
            $count_solde = 0;
            try
            {
                if(isset($debits[$compte][$exercice]))
                {
                    if(round($debits[$compte][$exercice],2) == round($credits[$compte][$exercice],2)) $count_solde ++;
                }
                else $count_solde++;
            } catch (\Exception $ex) {
                $count_solde++;
            }

            if($count_solde == count($exercice)) $delete_index[] = $indexCpt;
            $indexCpt++;
        }

        for($i=count($delete_index)-1; $i>=0 ;$i--)
        {
            unset($comptes_str[$delete_index[$i]]);
        }

        sort($comptes_str);

        $req = 'select e.date_ecr, e.libelle, ROUND((e.debit - e.credit),2) as mtt, i.id as image_id, e.tiers_id, e.pcc_id, ic.date_facture, ic.rs AS ic_rs, i.nom as i_nom
                    from ecriture e
                    inner join image i ON (i.id = e.image_id) 
                    left join imputation_controle ic ON (e.image_id = ic.image_id)
                    left join separation sep ON (sep.image_id = i.id) 
                    left join souscategorie sc ON (sc.id = sep.souscategorie_id) 
                    where e.dossier_id = :DOSSIER_ID 
                    and e.exercice = :EXERCICE
                    and DATEDIFF(:NOW_1, e.date_ecr) > :MIN_INTERVAL   
                    and DATEDIFF(:NOW_2, e.date_ecr) <= :MAX_INTERVAL
                    and i.supprimer <> 1 
                    and sc.libelle_new <> :DOUBLON ';
        $prep = $pdo->prepare($req);
        $prep = $pdo->prepare($req);
        $params = [
            'DOSSIER_ID' => $dossier->getId(),
            'MIN_INTERVAL' => $inteval[0],
            'MAX_INTERVAL' => $inteval[1],
            'EXERCICE' => $exercice,
            'DOUBLON' => 'DOUBLON',
            'NOW_1' => (new \DateTime())->format('Y-m-d'),
            'NOW_2' => (new \DateTime())->format('Y-m-d')
        ];
        $prep->execute($params);
        foreach ($prep->fetchAll() as $item)
        {
            if($this->getResult($item,$index,$type,$comptes_str))
                $results[] = $this->getResult($item,$index,$type,$comptes_str);
        }
        return $results;
    }

    /**
     * @param $item
     * @param int $index
     * @param array $compteStr
     * @return object
     */
    private function getResult($item,$index = 0,$type,$compteStr)
    {
        $bilan = null;
        $tva = null;
        $resultat = null;
        $isOk = false;
        if ($item->tiers_id)
        {
            if ($item->tiers_id)
            {
                $tiers = $this->getEntityManager()->getRepository('AppBundle:Tiers')
                    ->find($item->tiers_id);
                if(in_array($tiers->getCompteStr(), $compteStr)){
                    if($tiers->getType() == 0 || $tiers->getType() == 1){
                        $bilan = (object)
                        [
                            'id' => Boost::boost($tiers->getId()),
                            'l' => $tiers->getCompteStr(),
                            't' => 1
                        ];
                        $isOk = true;
                    }
                }
            }
        }

        if($isOk){
            $image = $this->getEntityManager()->getRepository('AppBundle:Image')
                ->find($item->image_id);
            $imageComment = $this->getEntityManager()->getRepository('AppBundle:ImageComment')
                ->getByImage($image);

            /** @var \DateTime $dateEcheance */
            $dateEcheance = null;

            if ($item->date_ecr && trim($item->date_ecr) != '')
                $dateEcheance = \DateTime::createFromFormat('Y-m-d',$item->date_ecr);

            if (!$dateEcheance)
            {
                if ($item->date_facture)
                    $dateEcheance = \DateTime::createFromFormat('Y-m-d',$item->date_facture);
                else
                    $dateEcheance = new \DateTime();

                $dateEcheance->add(new \DateInterval('P45D'));

                $dateEcheance = DateExt::getNextOuvrable($date_ecr);
            }

            if ($index == -1) return null;

            return (object)
            [
                'id' => Boost::boost($item->image_id),
                'i' => (object)
                [
                    'id' => Boost::boost($item->image_id),
                    'n' => $item->i_nom,
                ],
                'd' => $item->date_facture,
                'de' => $dateEcheance->format('Y-m-d'),
                'l' => ($item->ic_rs) ? $item->ic_rs : $item->libelle,
                'b' => $bilan,
                'tva' => $tva,
                'r' => $resultat,
                'm_'.$index => $item->mtt,
                'st' => $imageComment ? $imageComment->getStatus() : 0,
                'cm' => $imageComment ? $imageComment->getCommentaire() : ''
            ];
        }
    }
}