<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 12/09/2017
 * Time: 10:38
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurTb;
use AppBundle\Entity\IndicateurTbCle;
use AppBundle\Entity\IndicateurTbDecision;
use AppBundle\Entity\IndicateurTbDomaine;
use AppBundle\Entity\IndicateurTbInfoPerdos;
use AppBundle\Entity\IndicateurTbPile;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\Validator\Constraints\Count;

class IndicateurTbRepository extends EntityRepository
{
    /**
     * @param int $affichage
     * @param IndicateurTbDomaine|null $indicateurTbDomaine
     * @return IndicateurTb[]
     */
    public function getIndicateurTb($affichage = 0, IndicateurTbDomaine $indicateurTbDomaine = null)
    {
        $results = $this->createQueryBuilder('it')
            ->where('it.affichage = :affichage')
            ->setParameter('affichage',$affichage);

        if ($indicateurTbDomaine)
            $results = $results
                ->andWhere('it.indicateurTbDomaine = :indicateurTbDomaine')
                ->setParameter('indicateurTbDomaine',$indicateurTbDomaine);

        return $results
            ->orderBy('it.rang')
            ->addOrderBy('it.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $affichage
     * @return array
     */
    public function getAllGrouped($affichage = 1)
    {
        $indicateurTbDomaines = $this->getEntityManager()->getRepository('AppBundle:IndicateurTbDomaine')
            ->getAll($affichage);

        $results = [];
        foreach ($indicateurTbDomaines as $indicateurTbDomaine)
        {
            $results[] = (object)
            [
                'itd' => $indicateurTbDomaine,
                'its' => $this->getEntityManager()->getRepository('AppBundle:IndicateurTb')
                    ->getIndicateurTb($affichage,$indicateurTbDomaine)
            ];
        }

        return $results;
    }

    /**
     * @param Dossier[] $dossiers
     * @param $periodes
     * @param bool $var
     * @param int $affichage
     * @param array $exos
     * @return \stdClass
     */
    public function getTb($dossiers,$periodes,$var = false,$affichage = 0, $exos = [])
    {
        $entetesGoupeds = [];
        $indicateurTbGroupeds = $this->getAllGrouped($affichage);

        /** @var IndicateurTb[] $indicateurTbs */
        $indicateurTbs = [];

        foreach ($indicateurTbGroupeds as $indicateurTbGrouped)
        {
            $indicateurTbs = array_merge($indicateurTbs,$indicateurTbGrouped->its);
            /** @var IndicateurTbDomaine $indicateurTbDomaine */
            $indicateurTbDomaine = $indicateurTbGrouped->itd;

            $entetesGoupeds[] = (object)
            [
                'n' => $indicateurTbDomaine->getNom(),
                'nb' => count($indicateurTbGrouped->its)
            ];
        }

        //$indicateurTbs = $this->getIndicateurTb($affichage);

        $dateNow = new \DateTime();
        $annee = intval($dateNow->format('Y'));
        $exercices = [];
        for ($i = 0; $i < 7; $i++)
            $exercices[] = $annee + 1 -$i;

        $tbs = [];
        $p1 = $periodes->p1;
        $p2 = $periodes->p2;
        $codeNa = $this->getEntityManager()->getRepository('AppBundle:IndicateurTbDecision')->getNaCode();

        /** @var IndicateurTbCle[] $indicateurTbCles */
        $indicateurTbCles = [];

        /** @var IndicateurTbInfoPerdos[] $indicateurTbInfoperdos */
        $indicateurTbInfoperdos = [];

        if ($affichage == 1)
        {
            $indicateurTbCles = $this->getEntityManager()->getRepository('AppBundle:IndicateurTbCle')
                ->getAll();

            $indicateurTbInfoperdos = $this->getEntityManager()->getRepository('AppBundle:IndicateurTbInfoPerdos')
                ->all();
        }

        $entetes = [];
        $allKey = [];
        $infoBullesCles = [];
        foreach ($indicateurTbCles as $indicateurTbCle)
        {
            $infoBullesCles[] = $indicateurTbCle->getCle();
        }

        $entetes[] = count($indicateurTbInfoperdos);
        foreach ($indicateurTbInfoperdos as $indicateurTbInfoperdo)
        {
            $entetes[] = $indicateurTbInfoperdo->getIndicateurInfoPerdos()->getHeader();
        }

        if (count($indicateurTbCles) > 0)
        {
            $entetes[] = (object)
            [
                'l' => 'Clés',
                'd' => '',
                'n' => ' ',
                'p' => 0,
                'infoBulles' => (object)
                [
                    'exos' => $exercices,
                    'cles' => $infoBullesCles
                ]
            ];
        }

        //return $periodes;
        foreach ($indicateurTbs as $indicateurTb)
        {
            /**
             * decisions
             */
            $indicateurDecisions = $this->getEntityManager()->getRepository('AppBundle:IndicateurTbDecision')
                ->getIndicateurTbDecisions($indicateurTb);

            /**
             * indicateur operandes
             */
            $operandes = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandesIndicateurTbs($indicateurTb);
            $operandesRubriques = [];
            foreach ($operandes as $operande)
            {
                $operandesRubriques[] = $this->getEntityManager()->getRepository('AppBundle:Rubrique')->getRubriquesInOperandes($operande);
            }

            $tbs[] = (object)
            [
                'indicateurTb' => $indicateurTb,
                'indicateurDecisions' => $indicateurDecisions,
                'operandesRubriques' => $operandesRubriques
            ];

            $entetes[] = (object)
            [
                'l' => $indicateurTb->getLibelle(),
                'd' => $indicateurTb->getDescription(),
                'n' => $indicateurTb->getNorme(),
                'p' => $indicateurTb->getPonderation()
            ];
        }

        $langage = new ExpressionLanguage();
        $dossiersResults = [];
        $n = 1;

        $stats = [];
        $cles = [];
        $keyNotEmpty = [];
        if ($affichage == 1)
        {
            $res = $this->getTbParCompta($dossiers,$tbs,$periodes,$indicateurTbCles,$indicateurTbInfoperdos,$exos);

            $dossiersResults = $res->dossiersResults;
            $stats = $res->stats;
            $cles = $res->cles;
        }
        else
            foreach ($dossiers as &$dossier)
            {
                $dossiersResult = new \stdClass();
                $dossiersResult->dossier = (object)
                [
                    'id' => Boost::boost($dossier->getId()),
                    'd' => $dossier->getNom(),
                    'ex' => 2017
                ];
                $dossiersResult->n = $n;
                $n++;
                $clotureDossier = $dossier->getCloture();
                $moisDebut = $clotureDossier + 1;
                if ($moisDebut == 13) $moisDebut = 1;
                $moisP1 = $moisDebut;
                $k = (3 - $p1->niveau) * (4 - $p1->niveau) + (($p1->niveau == 3) ? 1 : 0);
                $moisP1 += ($k * $p1->val) - 1;
                while ($moisP1 > 12) $moisP1 -= 12;

                $moisP2 = $moisDebut;
                $k = (3 - $p2->niveau) * (4 - $p2->niveau) + (($p2->niveau == 3) ? 1 : 0);
                $moisP2 += ($k * $p2->val) - 1;
                while ($moisP2 > 12) $moisP2 -= 12;

                foreach ($tbs as $keyTb => &$tb)
                {
                    $mP1 = $this->getMontantPeriode($tb,$dossier,$moisDebut,$periodes->p1,$moisP1);

                    $isNa = false;
                    $variation = 0;
                    if ($mP1 == $codeNa) $isNa = true;

                    if (($tb->indicateurTb->getType() == 0 || $var) && !$isNa)
                    {
                        $mP1 = round($mP1,2);
                        $mP2 = $this->getMontantPeriode($tb,$dossier,$moisDebut,$periodes->p2,$moisP2);

                        if ($mP2 == $codeNa) $isNa = true;
                        else $mP2 = round($mP2,2);

                        if ($mP1 != 0 && !$isNa) $variation = ($mP2 - $mP1) / $mP1 * 100;
                        else $isNa = true;
                    }
                    else $variation = $mP1;
                    /** @var IndicateurTbDecision $decision */
                    $decision = null;

                    if ($isNa)
                    {
                        $dec = new \stdClass();
                        $dec->p = $codeNa;
                        $dossiersResult->$keyTb = $dec;
                        continue;
                    }

                    /** @var IndicateurTbDecision[] $indicateurTbDecisions */
                    $indicateurTbDecisions = $tb->indicateurDecisions;

                    foreach ($indicateurTbDecisions as $indicateurDecision)
                    {
                        $formuleDecision = $indicateurDecision->getConditionTb();
                        $chars = str_split($formuleDecision);
                        $formuleEvalDecsion = '';
                        $i = 0;
                        $listVal = [];
                        foreach($chars as $char)
                        {
                            if($char == '#')
                            {
                                $formuleEvalDecsion .= '_'.$i;
                                $listVal['_'.$i] = $variation;
                                $i++;
                            }
                            else $formuleEvalDecsion .= $char;
                        }

                        try
                        {
                            $eval = @$langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$formuleEvalDecsion))),$listVal);
                        }
                        catch (SyntaxError $s)
                        {
                            $eval = false;
                        }

                        if ($eval)
                        {
                            $decision = $indicateurDecision;
                            break;
                        }
                    }

                    $dec = new \stdClass();
                    $dec->p = ($decision != null) ? $decision->getPoint() : ' - ';
                    $dec->ic = ($decision != null) ? $decision->getIcon() : 'fa fa-question-circle-o';
                    $dec->v = round($variation,2);
                    $dec->u = $tb->indicateurTb->getUnite();
                    $dec->r = $tb->indicateurTb->getNbDecimal();
                    $dec->type = 0;

                    if ($dec->ic == $codeNa) $dec->p = $codeNa;
                    $dossiersResult->$keyTb = $dec;
                }

                $dossiersResults[] = $dossiersResult;
            }

        return (object)
        [
            'entetes' => $entetes,
            'datas' => $dossiersResults,
            'keyNotEmpty' => $keyNotEmpty,
            'allKeys' => $allKey,
            'stats' => $stats,
            'cles' => $cles,
            'entetesGoupeds' => $entetesGoupeds
        ];
    }

    /**
     * @param Dossier[] $dossiers
     * @param $tbs
     * @param $periodes
     * @param IndicateurTbCle[] $indicateurTbCles
     * @param IndicateurTbInfoPerdos[] $indicateurTbsInfoperdos
     * @param array $exos
     * @return \stdClass
     */
    public function getTbParCompta($dossiers,$tbs,$periodes,$indicateurTbCles,$indicateurTbsInfoperdos = [], $exos = [])
    {
        $em = $this->getEntityManager();

        $stats = [];
        $cles = [];
        $dossiersResults = [];
        $p1 = $periodes->p1;
        $p2 = $periodes->p2;
        $n = 1;
        $codeNa = $this->getEntityManager()->getRepository('AppBundle:IndicateurTbDecision')->getNaCode();
        foreach ($dossiers as $dossier)
        {
            $n++;
            $clotureDossier = $dossier->getCloture();
            $moisDebut = $clotureDossier + 1;
            if ($moisDebut == 13) $moisDebut = 1;
            $moisP1 = $moisDebut;
            $k = (3 - $p1->niveau) * (4 - $p1->niveau) + (($p1->niveau == 3) ? 1 : 0);
            $moisP1 += ($k * $p1->val) - 1;
            while ($moisP1 > 12) $moisP1 -= 12;

            $moisP2 = $moisDebut;
            $k = (3 - $p2->niveau) * (4 - $p2->niveau) + (($p2->niveau == 3) ? 1 : 0);
            $moisP2 += ($k * $p2->val) - 1;
            while ($moisP2 > 12) $moisP2 -= 12;

            $historiqueUploads = $this->getEntityManager()->getRepository('AppBundle:HistoriqueUpload')
                ->getHistoriqueUploaClosed($dossier);

            $exercicesCloseds = [];
            foreach ($historiqueUploads as $historiqueUpload)
            {
                $exercicesCloseds[] = intval($historiqueUpload->getExercice());
            }

            foreach ($historiqueUploads as $exercicesClosed => $historiqueUpload)
            {
                $dossiersResult = new \stdClass();
                $dossiersResult->id = Boost::boost($dossier->getId());
                $dossiersResult->dossier = (object)
                [
                    'id' => Boost::boost($dossier->getId()),
                    'd' => $dossier->getNom(),
                    'ex' => $exercicesClosed
                ];
                $dossiersResult->n = $n;
                $dossiersResult->exo = $exercicesClosed;

                if (count($exos) > 1 && !in_array($exercicesClosed,$exos)) continue;

                $occurence = 0;
                $infoBullesDatas = [];
                foreach ($indicateurTbCles as $indicateurTbCle)
                {
                    $countIndicateurTbCles = $this->getEntityManager()->getRepository('AppBundle:Ecriture')
                        ->countsIndicateurTbCle($indicateurTbCle,$dossier);

                    $value = array_key_exists($exercicesClosed,$countIndicateurTbCles) ? intval($countIndicateurTbCles[$exercicesClosed]) : 0;
                    $occurence += $value;

                    if ($value != 0)
                    {
                        if ($value != 0)
                            $infoBullesDatas[] = (object)
                            [
                                'exo' => $exercicesClosed,
                                'occ' => $value,
                                'cle' => $indicateurTbCle->getCle()
                            ];
                    }

                    if (!array_key_exists($dossier->getId(),$stats))
                        $stats[$dossier->getId()] = [];
                    if (!array_key_exists($exercicesClosed,$stats[$dossier->getId()]))
                        $stats[$dossier->getId()][$exercicesClosed] = [];

                    $stats[$dossier->getId()][$exercicesClosed][$indicateurTbCle->getId()] = $value;
                    if (!array_key_exists($indicateurTbCle->getId(),$cles))
                        $cles[$indicateurTbCle->getId()] = (object)
                        [
                            'cle' => $indicateurTbCle->getCle(),
                            'occ' => $value
                        ];
                    else $cles[$indicateurTbCle->getId()]->occ = $cles[$indicateurTbCle->getId()]->occ + $value;
                }

                $k = 1;
                foreach ($indicateurTbsInfoperdos as $kk => $indicateurTbsInfoperdo)
                {
                    $va = $this->getEntityManager()->getRepository('AppBundle:IndicateurInfoPerdos')
                        ->getVal($dossier,$indicateurTbsInfoperdo->getIndicateurInfoPerdos());
                    $keyTb_ = $kk + 1;
                    $dec = new \stdClass();
                    $dec->ic = 'fa fa-question-circle-o';
                    $dec->v = $va;
                    $dec->u = '';
                    $dec->r = 2;
                    $dec->type = 3;
                    $dec->p = '';
                    $dossiersResult->$keyTb_ = $dec;
                }
                $k += count($indicateurTbsInfoperdos);

                $dossiersResult->$k = (object)
                [
                    'type' => 1,
                    'v' => $occurence,
                    'infoBulles' => $infoBullesDatas
                ];

                $k++;
                foreach ($tbs as $keyTb => &$tb)
                {
                    $mP1 = $this->getMontantPeriode(
                        $tb, $dossier, $moisDebut,
                        (object)
                        [
                            'exercice' => $exercicesClosed,
                            'niveau' => 0,
                            'val' => 1
                        ], $moisP1
                    );

                    $isNa = false;
                    $variation = 0;
                    if ($mP1 == $codeNa) $isNa = true;
                    if (intval($tb->indicateurTb->getType()) == 0 && !$isNa)
                    {
                        $mP1 = round($mP1,2);

                        $n_1IsClosed = (in_array(intval($exercicesClosed) - 1,$exercicesCloseds));
                        if ($n_1IsClosed)
                        {
                            $mP2 = $this->getMontantPeriode(
                                $tb, $dossier, $moisDebut,
                                (object)
                                [
                                    'exercice' => intval($exercicesClosed) - 1,
                                    'niveau' => 0,
                                    'val' => 1
                                ], $moisP1
                            );

                            if ($mP2 == $codeNa) $isNa = true;
                            else $mP2 = round($mP2,2);

                            if ($mP1 != 0 && !$isNa) $variation = ($mP1 - $mP2) / $mP2;
                        }
                        else $isNa = true;
                    }
                    else $variation = $mP1;
                    if ($isNa) $variation = $codeNa;

                    $dec = new \stdClass();
                    $dec->ic = 'fa fa-question-circle-o';
                    $dec->v = $variation;
                    $dec->u = $tb->indicateurTb->getUnite();
                    $dec->r = $tb->indicateurTb->getNbDecimal();
                    $dec->type = 0;
                    $dec->p = '';

                    $keyTb_ = $keyTb + $k;
                    $dossiersResult->$keyTb_ = $dec;
                }

                $dossiersResults[] = $dossiersResult;

                //return $dossiersResult;
            }
        }

        return (object)
        [
            'dossiersResults' => $dossiersResults,
            'stats' => array_values($stats),
            'cles' => array_values($cles)
        ];
    }

    /**
     * @param $tb
     * @param Dossier $dossier
     * @param $moisDebut
     * @param $p
     * @param $moisP1
     * @return string
     */
    public function getMontantPeriode($tb,Dossier $dossier,$moisDebut,$p,$moisP1)
    {
        $codeNa = $this->getEntityManager()->getRepository('AppBundle:IndicateurTbDecision')->getNaCode();

        $clotureDossier = $dossier->getCloture();
        $langage = new ExpressionLanguage();
        $mOperandesRubriques = [];
        $i = 0;

        foreach ($tb->operandesRubriques as &$operandesRubrique)
        {
            $variation = $operandesRubrique->variation;
            $exerciceP1 = $p->exercice + $variation;
            $anneeMaxP1 = $exerciceP1;
            if ($clotureDossier != 12 && $moisDebut < $moisP1) $anneeMaxP1--;

            $formule = $operandesRubrique->formule;
            $chars = str_split($formule);
            $formuleEval = '';
            $indexOperande = 0;
            foreach($chars as $char)
            {
                if($char == '#')
                {
                    $formuleEval .= '_'.$indexOperande;
                    $indexOperande++;
                }
                else $formuleEval .= $char;
            }


            $listVal = [];
            $indexOperande = 0;
            foreach ($operandesRubrique->rubriques as &$rubrique)
            {
                $pccs = $this->getEntityManager()->getRepository('AppBundle:Pcc')->getPCCByPCG($rubrique->pcgsIns,$dossier,$rubrique->pcgsOuts);

                $pccTypeComptes = [];
                foreach ($pccs as $pcc)
                {
                    if (!array_key_exists($pcc->getId(),$pccTypeComptes))
                    {
                        foreach ($rubrique->pcgsIns as $pcgsIn)
                        {
                            if (strtoupper(substr($pcc->getCompte(),0,strlen($pcgsIn->getCompte()))) == strtoupper($pcgsIn->getCompte()))
                            {
                                $pccTypeCompte = new \stdClass();
                                $pccTypeCompte->typeCompte = $pcgsIn->getIdEtatCompte();
                                $pccTypeCompte->solde = $pcgsIn->getCochage();
                                $pccTypeComptes[$pcc->getId()] = $pccTypeCompte;
                                break;
                            }
                        }
                    }
                }

                $ecrituresP1s = $this->getEntityManager()->getRepository('AppBundle:Ecriture')->getEcrituresPccGroupedLimited($dossier,$pccs,[$exerciceP1],[],0,$anneeMaxP1.(($moisP1 < 10) ? '0' : '').$moisP1);

                $montantsP1s = [];
                $montantsAdsP1s = [];

                foreach ($ecrituresP1s as $ecrituresP1)
                {
                    $pccTiers = $ecrituresP1['pccTiers'];
                    $pccTiersSpliters = explode('-',$pccTiers);
                    $keyPcc = intval($pccTiersSpliters[0]);
                    $keyTiers = intval($pccTiersSpliters[1]);
                    $isAn = (intval($ecrituresP1['an']) == 1);
                    $debit = $ecrituresP1['db'];
                    $credit = $ecrituresP1['cr'];
                    $solde = $debit - $credit;

                    $pccTypeCompte = $pccTypeComptes[$keyPcc];
                    if ($pccTypeCompte->typeCompte == 1 && $keyTiers == 0 || $pccTypeCompte->typeCompte == 2 && $ecrituresP1['lettre'] == '') continue;

                    if ($isAn && $keyTiers != 0)
                    {
                        if (!array_key_exists($pccTiers,$montantsAdsP1s))
                        {
                            $m = new \stdClass();
                            $m->debit = $debit;
                            $m->credit = $credit;
                            $m->solde = $solde;
                            $montantsAdsP1s[$pccTiers] = $m;
                        }
                        else
                        {
                            $montantsAdsP1s[$pccTiers]->debit += $debit;
                            $montantsAdsP1s[$pccTiers]->credit += $credit;
                            $montantsAdsP1s[$pccTiers]->solde += $solde;
                        }
                    }
                    else
                    {
                        if (!array_key_exists($pccTiers,$montantsP1s))
                        {
                            $m = new \stdClass();
                            $m->debit = $debit;
                            $m->credit = $credit;
                            $m->solde = $solde;
                            $m->soldeDebit = ($solde > 0) ? $solde : 0;
                            $m->soldeCredit = ($solde < 0) ? abs($solde) : 0;
                            $montantsP1s[$pccTiers] = $m;
                        }
                        else
                        {
                            $montantsP1s[$pccTiers]->debit += $debit;
                            $montantsP1s[$pccTiers]->credit += $credit;
                            $montantsP1s[$pccTiers]->solde += $solde;
                            $montantsP1s[$pccTiers]->soldeDebit = ($montantsP1s[$pccTiers]->solde > 0) ? $montantsP1s[$pccTiers]->solde : 0;
                            $montantsP1s[$pccTiers]->soldeCredit = ($montantsP1s[$pccTiers]->solde < 0) ? abs($montantsP1s[$pccTiers]->solde) : 0;
                        }
                    }
                }
                /**
                 * montantsP1s[pcc_tiers] = {debit,credit,solde,soldeDebit,soldeCredit,solde}
                 * montantsAdsP1s[pcc_tiers] = {debit,credit,solde}
                 */

                foreach ($montantsAdsP1s as $key => $montantsAdsP1)
                {
                    if (!array_key_exists($key,$montantsP1s))
                    {
                        $montantsP1s[$key] = new \stdClass();
                        $montantsP1s[$key]->debit = $montantsAdsP1->debit;
                        $montantsP1s[$key]->credit = $montantsAdsP1->credit;
                        $montantsP1s[$key]->solde = $montantsAdsP1->solde;
                        $montantsP1s[$key]->soldeDebit = ($montantsP1s[$key]->solde > 0) ? $montantsP1s[$key]->solde : 0;
                        $montantsP1s[$key]->soldeCredit = ($montantsP1s[$key]->solde < 0) ? abs($montantsP1s[$key]->solde) : 0;
                    }
                    else
                    {
                        $montantsP1s[$key]->debit += $montantsAdsP1->debit;
                        $montantsP1s[$key]->credit += $montantsAdsP1->credit;
                        $montantsP1s[$key]->solde += $montantsAdsP1->solde;
                        $montantsP1s[$key]->soldeDebit = ($montantsP1s[$key]->solde > 0) ? $montantsP1s[$key]->solde : 0;
                        $montantsP1s[$key]->soldeCredit = ($montantsP1s[$key]->solde < 0) ? abs($montantsP1s[$key]->solde) : 0;
                    }
                }
                /**
                 * montantsP1s[pcc_tiers] = {debit,credit,solde,soldeDebit,soldeCredit,solde}
                 */

                $m = 0;
                foreach ($montantsP1s as $key => $montantsP1)
                {
                    $pccTiersSpliters = explode('-',$key);
                    $keyPcc = intval($pccTiersSpliters[0]);
                    $s = $pccTypeComptes[$keyPcc]->solde;
                    //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
                    if ($s == 1) $m += $montantsP1->soldeDebit;
                    elseif ($s == 2) $m -= $montantsP1->soldeCredit;
                    elseif ($s == 3) $m += $montantsP1->debit;
                    elseif ($s == 4) $m -= $montantsP1->credit;
                    else $m += $montantsP1->solde;
                }

                $listVal['_'.$indexOperande] = round($m,2);
                $indexOperande += 1;
            }

            try
            {
                $eval = @$langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$formuleEval))),$listVal);
                if (is_bool($eval) || is_nan($eval) || is_infinite($eval)) return $codeNa;
            }
            catch (SyntaxError $s)
            {
                return $codeNa;
            }

            $mOperandesRubriques['_'.$i] = $eval;
            $i++;
        }

        //return $test;

        $formuleTb = $tb->indicateurTb->getFormule();
        $chars = str_split($formuleTb);
        $formuleEvalTb = '';
        $i = 0;
        foreach($chars as $char)
        {
            if($char == '#')
            {
                $formuleEvalTb .= '_'.$i;
                $i++;
            }
            else $formuleEvalTb .= $char;
        }

        try
        {
            $eval = @$langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$formuleEvalTb))),$mOperandesRubriques);
            if (is_bool($eval) || is_nan($eval) || is_infinite($eval)) return $codeNa;
        }
        catch (SyntaxError $s)
        {
            return $codeNa;
        }

        return $eval;
    }
}