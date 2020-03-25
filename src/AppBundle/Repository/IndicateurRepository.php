<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/11/2016
 * Time: 10:25
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurCell;
use AppBundle\Entity\IndicateurFormatCol;
use AppBundle\Entity\IndicateurGroup;
use AppBundle\Entity\IndicateurOperande;
use AppBundle\Entity\IndicateurPack;
use AppBundle\Entity\IndicateurTypeGraphe;
use AppBundle\Entity\Utilisateur;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityRepository;
use ChartBundle\Controller\ChartArea;
use IndicateurBundle\Controller\ModelIndicateurClass;
use IndicateurBundle\Controller\StyleCell;
use stdClass;
use DateTime;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class IndicateurRepository extends EntityRepository
{
    /**
     * @param IndicateurPack $pack
     * @param $dossier
     * @return array
     */
    public function packItems(IndicateurPack $pack,$dossier)
    {
        if($dossier == null)
        {
            $liste = $this->createQueryBuilder('p')
                ->where('p.indicateurPack = :pack')
                ->setParameter('pack',$pack)
                ->andWhere('p.dossier IS NULL')
                ->orderBy('p.rang')
                ->addOrderBy('p.libelle')
                ->getQuery()
                ->getResult();
        }
        else
        {
            $liste = $this->createQueryBuilder('p')
                ->where('p.indicateurPack = :pack')
                ->setParameter('pack',$pack)
                ->andWhere('p.dossier IS NULL OR p.dossier = :dossier')
                ->setParameter('dossier',$dossier)
                ->orderBy('p.rang')
                ->addOrderBy('p.libelle')
                ->getQuery()
                ->getResult();
        }

        if($dossier != null)
        {
            foreach ($liste as &$item)
            {
                if($item->getDossier() == null)
                {
                    $indPackItemSpecDossier = $this->getEntityManager()->getRepository('AppBundle:IndicateurSpecIndicateur')
                        ->createQueryBuilder('pid')
                        ->where('pid.dossier = :dossier')
                        ->setParameter('dossier', $dossier)
                        ->andWhere('pid.indicateur = :indPackItem')
                        ->setParameter('indPackItem', $item)
                        ->getQuery()
                        ->getOneOrNullResult();
                    if ($indPackItemSpecDossier != null) $item->setEnabled(false);
                }
            }
        }

        foreach ($liste as &$item)
        {
            $graphes = $this->getEntityManager()->getRepository('AppBundle:IndicateurTypeGraphe')->getGraphes($item);
            $item->setGraphes($graphes);
            $oldShow = null;
            if($dossier != null) $oldShow = $this->getEntityManager()->getRepository('AppBundle:IndicateurLastShow')->getArrayLast($dossier,$item);
            $item->setLastShow(Boost::serialize($oldShow));

            //set cell, format columns
            if($item->getIsTable() == 1)
            {
                $cells = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCells($item);
                $item->setCells($cells);

                $colsFormats = $this->getEntityManager()->getRepository('AppBundle:IndicateurFormatCol')->getColFormats($item);
                $item->setColsFormats($colsFormats);
            }
        }
        return $liste;
    }

    /**
     * @param IndicateurPack $indicateurPack
     * @param $client
     * @param $dossier
     * @param bool $withNotValidate
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getIndicateurs(IndicateurPack $indicateurPack, $client, $dossier,$withNotValidate = true)
    {
        $liste = $this->createQueryBuilder('p');
        if($client == null && $dossier == null)
        {
            $liste = $liste->where('p.dossier IS NULL')->andWhere('p.client IS NULL');
        }
        else
        {
            if($client != null) $liste = $liste->where('(p.client = :client OR (p.client IS NULL AND p.dossier IS NULL))')->setParameter('client',$client);
            else $liste = $liste
                ->where('(p.dossier = :dossier OR p.client = :client OR (p.dossier IS NULL AND p.client IS NULL))')
                ->setParameter('dossier',$dossier)
                ->setParameter('client',$dossier->getSite()->getClient());
        }

        if(!$withNotValidate)
            $liste = $liste->andWhere('p.valider = 1');

        $liste = $liste
            ->andWhere('p.indicateurPack = :indicateurPack')
            ->setParameter('indicateurPack',$indicateurPack)
            ->orderBy('p.rang')->addOrderBy('p.libelle')
            ->getQuery()
            ->getResult();

        foreach ($liste as &$item)
        {
            $graphes = $this->getEntityManager()->getRepository('AppBundle:IndicateurTypeGraphe')->getGraphes($item);
            $item->setGraphes($graphes);
            $oldShow = null;
            if($dossier != null) $oldShow = $this->getEntityManager()->getRepository('AppBundle:IndicateurLastShow')->getArrayLast($dossier,$item);
            $item->setLastShow(Boost::serialize($oldShow));

            //set cell, format columns, td to graphe
            if($item->getIsTable() == 1)
            {
                $cells = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCells($item);
                $item->setCells($cells);

                $colsFormats = $this->getEntityManager()->getRepository('AppBundle:IndicateurFormatCol')->getColFormats($item);
                $item->setColsFormats($colsFormats);

                $tdsToGraphes = $this->getEntityManager()->getRepository('AppBundle:IndicateurFormatCol')->getTdToGraphe($item);
                $item->setTdsToGraphes($tdsToGraphes);
            }

            if($client != null || $dossier != null)
            {
                $item = $this->getEntityManager()->getRepository('AppBundle:IndicateurSpecIndicateur')->setEnabled($item,$client,$dossier);
            }
        }
        return $liste;
    }

    /**
     * @param IndicateurPack $indicateurPack
     * @param $client
     * @param $dossier
     * @return array
     */
    public function getIndicateursADupliquer(IndicateurPack $indicateurPack,$client,$dossier)
    {
        $query = $this->createQueryBuilder('ip')
            ->where('ip.indicateurPack = :indicateurPack')
            ->setParameter('indicateurPack',$indicateurPack);

        if($client != null)
            $query = $query->andWhere('(ip.client = :client OR (ip.client IS NULL AND ip.dossier IS NULL))')
                ->setParameter('client',$client);
        elseif($dossier != null)
            $query = $query->andWhere('(ip.dossier = :dossier OR ip.client = :client OR (ip.client IS NULL AND ip.dossier IS NULL))')
                ->setParameter('dossier',$dossier)
                ->setParameter('client',$dossier->getSite()->getClient());
        else
            $query = $query->andWhere('ip.client IS NULL')
                ->andWhere('ip.dossier IS NULL');

        return $query->getQuery()->getResult();

    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @param Indicateur $indicateur
     * @return Indicateur
     */
    public function getComplete(Indicateur $indicateur)
    {
        $graphes = $this->getEntityManager()->getRepository('AppBundle:IndicateurTypeGraphe')->getGraphes($indicateur);
        $operandes = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandes($indicateur);
        $indicateur->setGraphes($graphes);
        $indicateur->setOperandes($operandes);
        //$indicateur->setAnalyseBinary();
        //$indicateur->setPeriodeBinary();
        //$indicateur->setCells($cells);
        return $indicateur;
    }

    /**
     * @param $indicateur
     * @param $action
     * @param int $rowDeleted
     * @param int $colDeleted
     * @param bool $isEtat
     * @return int
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editRowCol($indicateur,$action,$rowDeleted = -1,$colDeleted = -1,$isEtat = false)
    {
        $decale = 1;
        //add row
        if($action == 4)
        {
            $indicateur->setRowNumber($indicateur->getRowNumber() + 1);
        }
        //add col
        elseif($action == 6)
        {
            $indicateur->setColNumber($indicateur->getColNumber() + 1);
        }
        //delete
        if($action == 5)
        {
            //delete row
            if($rowDeleted != -1) $indicateur->setRowNumber($indicateur->getRowNumber() - 1);
            //delete col
            if($colDeleted != -1) $indicateur->setColNumber($indicateur->getColNumber() - 1);

            $decale = -1;
        }
        //decale row or col
        if ( $action == 5 || ($action != 5 && ($rowDeleted <> -1 || $colDeleted <> -1) && $rowDeleted <> -10 && $colDeleted <> -10) )
            $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->decaleRowCol($indicateur,$rowDeleted,$colDeleted,$isEtat,$decale);
        $this->getEntityManager()->flush();
        return 1;
    }

    /**
     * @param Dossier $dossier
     * @param Indicateur $indicateur
     * @param $exercices
     * @param $moiss
     * @param $analyse
     * @param $montantsTempsParams
     * @param $debitsTiersParams
     * @param $creditsTiersParams
     * @param $typeSoldes
     * @param $tiersObjects
     * @param $indicateurOperandes
     * @param $code_graphe
     * @param $periodes
     * @return array
     */
    public function getResultTiers(Dossier $dossier,Indicateur $indicateur,$exercices,$moiss,$analyse,$montantsTempsParams,$debitsTiersParams,$creditsTiersParams,$typeSoldes,$tiersObjects,$indicateurOperandes,$code_graphe,$periodes)
    {
        $moisCloture = intval($dossier->getCloture());
        $indexOperande = 0;
        $montantsTemps =
        $debitsTiers =
        $creditsTiers = array();
        //change mois annee en periode
        foreach ($indicateurOperandes as $indicateurOperande)
        {
            foreach ($exercices as $exercice)
            {
                foreach ($tiersObjects as $keyTier => $tiersObject)
                {
                    $annee_current = ($moisCloture == 12) ? $exercice : $exercice - 1;
                    foreach ($periodes as $periode)
                    {
                        $annee_current_ = $annee_current;
                        $montant = 0;
                        $montantDebit = 0;
                        $montantCredit = 0;

                        foreach ($periode->moiss as $mois)
                        {
                            if(intval($moiss[0]) > intval($mois)) $annee_current_++;
                            $key = $indexOperande.'-'.$exercice.'-'.$keyTier.'-'.$annee_current_.$mois;

                            if(array_key_exists($key,$montantsTempsParams)) $montant += $montantsTempsParams[$key];
                            if(array_key_exists($key,$debitsTiersParams)) $montantDebit += $debitsTiersParams[$key];
                            if(array_key_exists($key,$creditsTiersParams)) $montantCredit += $creditsTiersParams[$key];
                        }

                        $keyNew = $indexOperande.'-'.$exercice.'-'.$keyTier.'-'.$periode->libelle;
                        $montantsTemps[$keyNew] = $montant;
                        $debitsTiers[$keyNew] = $montantDebit;
                        $creditsTiers[$keyNew] = $montantCredit;
                    }
                }
            }
            $indexOperande++;
        } //$montantsTemps[$indexOperande-$exercice-$keyTier-periode] sans AN

        //reglage solde avec AN
        $indexOperande = 0;
        foreach ($indicateurOperandes as $indicateurOperande)
        {
            foreach ($exercices as $exercice)
            {
                foreach ($tiersObjects as $keyTier => $tiersObject)
                {
                    foreach ($periodes as $periode)
                    {
                        $key = $indexOperande.'-'.$exercice.'-'.$keyTier.'-'.$periode->libelle;
                        $debit_credit = 0;
                        if(array_key_exists($key, $debitsTiers))
                        {
                            $debit_credit = $debitsTiers[$key] - $creditsTiers[$key];
                        }
                        $debit_t = ($debit_credit > 0) ? $debit_credit : 0;
                        $credit_t = abs(($debit_credit < 0) ? $debit_credit : 0);

                        /**
                         * calcul montant temps
                         * */
                        if(!array_key_exists($key, $montantsTemps))
                        {
                            //solde debit
                            if ($typeSoldes[$indexOperande] == 1) $montantsTemps[$key] = $debit_t;
                            //solde credit
                            elseif ($typeSoldes[$indexOperande] == 2) $montantsTemps[$key] = -$credit_t;
                            //debit credit, solde
                            else $montantsTemps[$key] = $debit_t - $credit_t;
                        }
                        else
                        {
                            //solde debit
                            if ($typeSoldes[$indexOperande] == 1) $montantsTemps[$key] += $debit_t;
                            //solde credit
                            elseif ($typeSoldes[$indexOperande] == 2) $montantsTemps[$key] -= $credit_t;
                            //debit credit, solde
                            else $montantsTemps[$key] += $debit_t - $credit_t;
                        }
                    }
                }
            }
            $indexOperande++;
        } ////$montantsTemps[$indexOperande-$exercice-$keyTier-periode] avec AN

        //suppresion operande et non tiers
        $montantsTiers = array();
        foreach($montantsTemps as $key => $montantsTemp)
        {
            $keySpilter = explode('-',$key);
            $indexOperande = $keySpilter[0];
            $exercice = $keySpilter[1];
            $keyTier = $keySpilter[2];
            $periode = $keySpilter[3];
            if(intval($keyTier) != 0) $montantsTiers[$exercice.'-'.$keyTier.'-'.$periode] = $montantsTemps[$indexOperande.'-'.$exercice.'-'.$keyTier.'-'.$periode];
        }//$montantsTiers[$exercice-$keyTier-$periode]

        //$montantsTiers par periode
        $montantsPeriodesTiers = array();
        foreach ($exercices as $exercice)
        {
            foreach ($periodes as $periode)
            {
                $arrayTiers = array();
                foreach ($tiersObjects as $keyTier => $tiersObject)
                {
                    if(intval($keyTier) != 0)
                    {
                        $key = $exercice.'-'.$keyTier.'-'.$periode->libelle;
                        $value = $montantsTiers[$key];
                        if(array_key_exists($keyTier,$arrayTiers)) $arrayTiers[$keyTier] += $value;
                        else $arrayTiers[$keyTier] = $value;
                    }
                }
                $montantsPeriodesTiers[$exercice.'-'.$periode->libelle] = $arrayTiers;
            }
        } //$montantsPeriodesTiers[$exercice.'-'.$periode->libelle]

        //tri des tiers par valeurs
        foreach ($montantsPeriodesTiers as &$montantsPeriodesTier)
        {
            uasort($montantsPeriodesTier, array($this, 'cmp'));
            $montantsPeriodesTier = array_reverse($montantsPeriodesTier, true);//$montantsPeriodesTier[id_tiers] trie decroissant
        } //$montantsPeriodesTiers[$exercice.'-'.$periode->libelle] trie



        $charts = array();
        $categories = array();
        $resultat = array();
        $arrondirA = ($indicateur->getIsDecimal() == 1) ? 2 : 0;
        $titre = $indicateur->getDescription();
        $sousTitre = '';
        $limitAffichage = $indicateur->getMax() - 1;

        //comparaison
        if($analyse == 1)
        {
            foreach ($montantsPeriodesTiers as $key => $montantsPeriodesTier)
            {
                $categories[] = $key;
                $data = array();



                //$index++;
            }

            $resultat = array('categories'=>$categories,'series'=>$charts,'arrondirA'=>$arrondirA,'titre'=>$titre,'sousTitre'=>$sousTitre);

            foreach ($exercices as $exercice)
            {
                $data = array();
                foreach ($periodes as $key => $periode)
                {
                    if($exercice == $exercices[0]) $categories[] = $periode->libelle;
                    //$data[] = $montantPeriodes[$exercice.'-'.$periode->libelle];
                }

                $chart = new ChartArea();
                $chart->data = $data;
                $chart->name = $exercice;
                $charts[] = $chart;
            }
        }

        return $resultat;
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

    /**
     * @param Indicateur $indicateur
     * @return string
     */
    public function getFormuleEval(Indicateur $indicateur)
    {
        $oldFormule = $indicateur->getFormule();
        //formule a evaluer
        $formule_eval = '';
        $chars = str_split($oldFormule);
        $index_operande = 0;

        $rubriques = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandes($indicateur);
        foreach($chars as $char)
        {
            if($char == '#')
            {
                $rubrique = $rubriques[$index_operande]->getRubrique();
                if(trim($rubrique->getFormule()) != "") $formule_eval .= "(".$rubrique->getFormule().")";
                else $formule_eval .= "#";
                $index_operande++;
            }
            else $formule_eval .= $char;
        }
        return $formule_eval;
    }

    /**
     * @param $indicateurs
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function arrangeRang($indicateurs)
    {
        $em = $this->getEntityManager();
        foreach ($indicateurs as $key => $indicateur) $indicateur->setRang($key);
        $em->flush();
    }

    /**
     * @param Indicateur $indicateur
     * @param $client
     * @param $dossier
     * @param null $indicateurPack
     * @return int
     */
    public function dupliquer(Indicateur $indicateur,$client,$dossier,$indicateurPack = null)
    {
        $em = $this->getEntityManager();
        if($indicateurPack == null) $indicateurPack = $indicateur->getIndicateurPack();
        $indicateurDupliquer = new Indicateur();

        $indicateurDupliquer = $indicateurDupliquer
            ->setClient($client)
            ->setDossier($dossier)
            ->setIndicateurPack($indicateurPack)
            ->setLibelleAffiche($indicateur->getLibelleAffiche())
            ->setAnalyse($indicateur->getAnalyse())
            ->setColNumber($indicateur->getColNumber())
            ->setDescription($indicateur->getDescription())
            ->setFormule($indicateur->getFormule())
            ->setIsDecimal($indicateur->getIsDecimal())
            ->setIsTable($indicateur->getIsTable())
            ->setMax($indicateur->getMax())
            ->setLibelleAffiche($indicateur->getLibelleAffiche())
            ->setPeriode($indicateur->getPeriode())
            ->setRang($indicateur->getRang())
            ->setRowNumber($indicateur->getRowNumber())
            ->setTypeOperation($indicateur->getTypeOperation())
            ->setUnite($indicateur->getUnite())
            ->setLibelle($indicateur->getLibelle())
            ->setKeyDupliquer(Boost::getUuid(25));

        $em->persist($indicateurDupliquer);
        try
        {
            $em->flush();

            //graphe
            $graphes = $this->getEntityManager()->getRepository('AppBundle:IndicateurTypeGraphe')->getGraphes($indicateur);
            foreach ($graphes as $graphe)
            {
                $indicateurTypeGrapheDupliquer = new IndicateurTypeGraphe();
                $indicateurTypeGrapheDupliquer
                    ->setIndicateur($indicateurDupliquer)
                    ->setTypeGraphe($graphe);
                $em->persist($indicateurTypeGrapheDupliquer);
            }
            $em->flush();

            //operandes
            $operandesIndicateurs = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandes($indicateur);
            foreach ($operandesIndicateurs as $operandesIndicateur)
            {
                $indicateurOperandeDupliquer = new IndicateurOperande();
                $indicateurOperandeDupliquer->setVariationN($operandesIndicateur->getVariationN())
                    ->setRubrique($operandesIndicateur->getRubrique())
                    ->setIndicateur($indicateurDupliquer);
                $em->persist($indicateurOperandeDupliquer);
            }
            $em->flush();

            //formats
            $formats = $this->getEntityManager()->getRepository('AppBundle:IndicateurFormatCol')->getColFormats($indicateur);
            foreach ($formats as $format)
            {
                $indicateurFormatColDupliquer = new IndicateurFormatCol();
                $indicateurFormatColDupliquer
                    ->setIndicateur($indicateurDupliquer)
                    ->setAvecDecimal($format->getAvecDecimal())
                    ->setCol($format->getCol())
                    ->setFormat($format->getFormat());
                $em->persist($indicateurFormatColDupliquer);
            }
            $em->flush();

            //indicateur cell
            $indicateurCells = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCells($indicateur);
            foreach ($indicateurCells as $indicateurCell)
            {
                $indicateurCellDupliquer = new IndicateurCell();
                $indicateurCellDupliquer
                    ->setIndicateur($indicateurDupliquer)
                    ->setBgColor($indicateurCell->getBgColor())
                    ->setBorder($indicateurCell->getBorder())
                    ->setCol($indicateurCell->getCol())
                    ->setColor($indicateurCell->getColor())
                    ->setFontBold($indicateurCell->getFontBold())
                    ->setFontFamily($indicateurCell->getFontFamily())
                    ->setFontItalic($indicateurCell->getFontItalic())
                    ->setFormule($indicateurCell->getFormule())
                    ->setIndent($indicateurCell->getIndent())
                    ->setIsFormule($indicateurCell->getIsFormule())
                    ->setRow($indicateurCell->getRow())
                    ->setTextAlign($indicateurCell->getTextAlign());
                $em->persist($indicateurCellDupliquer);
                $em->flush();

                //operandes cells
                $operandesCells = $indicateurCell->getOperandes();
                foreach ($operandesCells as $operandesCell)
                {
                    $indicateurOperandeDupliquer = new IndicateurOperande();
                    $indicateurOperandeDupliquer
                        ->setIndicateurCell($indicateurCellDupliquer)
                        ->setRubrique($operandesCell->getRubrique())
                        ->setVariationN($operandesCell->getVariationN());
                    $em->persist($indicateurOperandeDupliquer);
                }
                $em->flush();
            }

            return 1;
        }
        catch (UniqueConstraintViolationException $violationException)
        {
            return 0;
        }
    }

    /**
     * @param Dossier $dossier
     * @param $indicateur
     * @param $exercices
     * @param $moiss
     * @param $code_graphe
     * @param $analyse
     * @param $periodes
     * @param $dateAnciennete
     * @param $anciennetes
     * @param $category
     * @param $name
     * @param $isTd
     * @param $row
     * @param $col
     * @param bool $isEtat
     * @return stdClass
     */
    public function getDetailsTdV4(Dossier $dossier,$indicateur,$exercices,$moiss,$code_graphe,$analyse,$periodes,$dateAnciennete,$anciennetes,$category,$name,$isTd,$row,$col,$isEtat = false)
    {
        $titre = (($isEtat) ? $indicateur->getEtat()->getLibelle() : $indicateur->getLibelle()).' ';
        $row++;
        $indicateurCells = ($isEtat) ?
            $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCellsEtats($indicateur) :
            $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCells($indicateur);
        $indicateursHeaders = [];
        $rowNumber = $indicateur->getRowNumber();
        $colNumber = $indicateur->getColNumber();

        foreach ($indicateurCells as $indicateurCell)
        {
            $rowCurrent = $indicateurCell->getRow();
            $colCurrent = $indicateurCell->getCol();
            if($rowCurrent != 0)
            {
                if($colCurrent == 0 && $rowCurrent == $row && trim($indicateurCell->getFormule())) $titre .= ', '.$indicateurCell->getFormule().' ';
                continue;
            }
            $indicateursHeaders[$colCurrent] = $indicateurCell->getFormule();
        }

        for ($j = 0; $j < $colNumber; $j++)
        {
            if (!array_key_exists($j,$indicateursHeaders)) $indicateursHeaders[$j] = null;
        }
        ksort($indicateursHeaders);

        $titre .= '('.$category.')';

        $colsExplodes = [];
        foreach ($indicateursHeaders as $key => $indicateursHeader)
        {
            if($key != 0 && $indicateursHeader != null && $indicateursHeader != '') $colsExplodes[$key] = count($periodes);
            else $colsExplodes[$key] = 1;
        }
        /**
         * $colsExplodes[col] = nbr de colonne + ajout
         */

        $colSelected = 0;
        $colMax = 0;
        foreach ($colsExplodes as $key => $colsExplode)
        {
            $colMax += $colsExplode;
            if($col < $colMax)
            {
                $colSelected = $key;
                break;
            }
        }

        $indicateurCellSelected = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getByRowCol($indicateur,$row,$colSelected,$isEtat);

        $reponses = [];
        $result = new stdClass();
        $formule = $indicateurCellSelected->getFormule();
        if(strpos($formule,'[') !== false)
        {
            $result->formule = $formule;
        }
        else
        {
            $result->formule = '';
            $indicateurCell = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCellsCompletedV2($indicateurCellSelected,$dossier);

            $exerciceSel = $exercices[0];
            $exercices = [];
            $categorySpliter = explode('-',$category);
            //$exercice = intval($categorySpliter[0]);
            $per = $categorySpliter[1];

            $moisInPeriodes = [];
            foreach ($periodes as $periode)
            {
                if(trim($per) != trim($periode->libelle)) continue;

                foreach ($periode->moiss as $mois)
                {
                    $moisInPeriodes[$mois] = $periode->libelle;
                }
            }
            /**
             * $moisInPeriodes[mois] = periode
             */

            $pccs = array();
            foreach ($indicateurCell->operandesRubriques as $operandesRubrique)
            {
                foreach ($operandesRubrique->rubriques as $rubrique)
                {
                    foreach ($rubrique->pccsInRubriques as $pccsInRubrique)
                    {
                        $pcc = $pccsInRubrique->pcc;
                        $compte = $pcc->getCompte();
                        if(!array_key_exists($compte,$pccs)) $pccs[$compte] = $pcc;
                    }
                }

                $variation = $operandesRubrique->variation;
                $exercice_ = $exerciceSel + $variation;
                if (!array_key_exists($variation,$exercices)) $exercices[$variation] = $exercice_;
            }
            /**
             * pccs[compte] = pcc
             * exercices[variation] = exercice
             */

            $ecritures = $this->getEntityManager()->getRepository('AppBundle:Ecriture')->getEcrituresPccGrouped($dossier,$pccs,$exercices,$moiss);
            /**
             * ecritures : all ecriture
             */

            $debutsExercices = array();
            $exercicesMois = array();
            foreach ($exercices as $exercice)
            {
                $debutsExercices[$exercice] = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateDebut($dossier,$exercice);
                $exercicesMois[$exercice] = array();
                foreach ($moiss as $mois)
                {
                    $annee = $exercice;
                    if($dossier->getCloture() != 12)
                    {
                        if(intval($mois) > $dossier->getCloture()) $annee--;
                    }
                    $exercicesMois[$exercice][] = $annee.$mois;
                }
            }
            /**
             * $exercicesMois[exercice][anneeMois]
             * $debutsExercices[exercice] = dateDebutExercice
             */

            $journalDossiers = $this->getEntityManager()->getRepository('AppBundle:JournalDossier')->getJournalADs($dossier);
            $tiersEachs = array();
            $tiersObjects = array();
            $ecrituresMoisPcs = array();
            foreach ($ecritures as $ecr)
            {
                $ecriture = $ecr['ecr'];
                $exercice = $ecriture->getExercice();
                $debutExercice = $debutsExercices[$exercice];
                $dateEcr = $ecriture->getDateEcr();
                if($dateEcr < $debutExercice) $dateEcr = $debutExercice;
                $tiers = $ecriture->getTiers();
                $pcc = ($ecriture->getPcc() != null) ? $ecriture->getPcc() : $tiers->getPcc();
                $tiersId = ($tiers != null) ? $tiers->getId() : 0;
                $pccId = $pcc->getCompte();
                $anneeMois = $dateEcr->format('Ym');

                $stdEcriture = new stdClass();
                $stdEcriture->isAn = (in_array($ecriture->getJournalDossier(),$journalDossiers));
                $stdEcriture->debit = $ecr['db'];
                $stdEcriture->credit = $ecr['cr'];
                $stdEcriture->pcc = $pcc;
                $stdEcriture->tiers = $tiers;
                $stdEcriture->lettrage = $ecr['lettre'];
                $ecrituresMoisPcs[$exercice.'-'.$anneeMois.'-'.$pccId.'-'.$tiersId][] = $stdEcriture;

                if(!array_key_exists($pccId.'-'.$tiersId,$tiersEachs))
                {
                    $tiersEachs[$pccId.'-'.$tiersId] = $tiers;
                    $tiersObjects[$tiersId] = $tiers;
                }
            }
            unset($ecritures);
            /**
             * $ecrituresMoisPcs[$exercice.'-'.$anneeMois.'-'.$pccId.'-'.$tiersId][] = stdClass: isAn,debit,credit,pcc,tiers;lettrage
             */

            foreach ($ecrituresMoisPcs as $keyExerciceMoisPcc => $ecrituresMoisPc)
            {
                $keySpliter = explode('-',$keyExerciceMoisPcc);
                $exercice = $keySpliter[0];
                $anneeMois = $keySpliter[1];
                $pccKey = $keySpliter[2];
                $tiersKey = $keySpliter[3];

                foreach ($ecrituresMoisPc as $montant)
                {
                    $debit = $montant->debit;
                    $credit = $montant->credit;
                    $isAn = $montant->isAn;
                    $lettrage = $montant->lettrage;

                    foreach ($indicateurCell->operandesRubriques as &$operandesRubrique)
                    {
                        foreach ($operandesRubrique->rubriques as &$rubrique)
                        {
                            if(!property_exists($rubrique,'ecritures')) $rubrique->ecritures = [];
                            if(array_key_exists($pccKey,$rubrique->pccsInRubriques))
                            {
                                //elimination par type de compte
                                // 0 : compte collectif; 1 : compte auxilliare ; 2 : factures non payes
                                $pcc = $rubrique->pccsInRubriques[$pccKey];
                                if($pcc->typeCompte == 1 && intval($tiersKey) == 0 ||
                                    $pcc->typeCompte == 2 && $lettrage == '') continue;

                                $newEcriture = new stdClass();
                                $newEcriture->debit = $debit;
                                $newEcriture->credit = $credit;
                                $newEcriture->isAn = $isAn;
                                $newEcriture->lettrage = $lettrage;
                                $rubrique->ecritures[$pccKey][$tiersKey][$exercice.'-'.$anneeMois][] = $newEcriture;
                            }
                        }
                    }

                }
            }
            /**
             *  indicateurCell
             *      ->isFormule;
             *      ->formule;
             *      ->operandesRubriques : [stdClass]
             *          ->variation;
             *          ->formule;
             *          ->rubriques: pccs,solde,typeCompte,ecritures[exercice-AnneeMois][pcc][tiers] = array(ecritures) sans AN
             */

            $montantsPccs = [];
            foreach ($indicateurCell->operandesRubriques as &$operandesRubrique)
            {
                foreach ($operandesRubrique->rubriques as &$rubrique)
                {
                    $montants = [];
                    if(!property_exists($rubrique,'ecritures')) $rubrique->ecritures = [];
                    foreach ($rubrique->ecritures as $keyPcc => &$ecrituresTiers)
                    {
                        foreach ($ecrituresTiers as $keyTiers => &$ecrituresAnneesMois)
                        {
                            ksort($ecrituresAnneesMois);
                            foreach ($ecrituresAnneesMois as $keyExerciceMois => &$ecrituresAnneesMoi)
                            {
                                $debitS = 0;
                                $creditS = 0;
                                $debitAnS = 0;
                                $creditAnS = 0;
                                foreach ($ecrituresAnneesMoi as $ecriture)
                                {
                                    if($ecriture->isAn)
                                    {
                                        $debitAnS += $ecriture->debit;
                                        $creditAnS += $ecriture->credit;
                                    }
                                    else
                                    {
                                        $debitS += $ecriture->debit;
                                        $creditS += $ecriture->credit;
                                    }
                                }

                                if($keyTiers != 0)
                                {
                                    $soldeAn = $debitAnS - $creditAnS;
                                    if($soldeAn >= 0) $debitS += $soldeAn;
                                    else $creditS += abs($soldeAn);
                                }
                                else
                                {
                                    $debitS += $debitAnS;
                                    $creditS += $creditAnS;
                                }

                                $newMontant = new stdClass();
                                $newMontant->debit = $debitS;
                                $newMontant->credit = $creditS;
                                $ecrituresAnneesMoi = $newMontant;
                            }
                            //montant avec AN

                            //completer le tableau
                            foreach ($exercices as $exercice)
                            {
                                if($exercice != $exerciceSel + $operandesRubrique->variation) continue;
                                for ($i = 0; $i < count($exercicesMois[$exercice]); $i++)
                                {
                                    $old = ($i == 0) ? null : $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i - 1]];
                                    if(!array_key_exists($exercice.'-'.$exercicesMois[$exercice][$i],$ecrituresAnneesMois))
                                    {
                                        $newMontant = new stdClass();
                                        $newDebit = 0;
                                        $newCredit = 0;

                                        /**
                                         * Modif 2017-11-06
                                         */
                                        if($old != null)
                                        {
                                            $newDebit = $old->debit;
                                            $newCredit = $old->credit;
                                        }
                                        /*if(intval(substr($keyPcc,0,1)) < 6 && $old != null)
                                        {
                                            $newDebit = $old->debit;
                                            $newCredit = $old->credit;
                                        }*/



                                        $solde = $newDebit - $newCredit;
                                        $newMontant->debit = $newDebit;
                                        $newMontant->credit = $newCredit;
                                        $newMontant->solde = $solde;
                                        $newMontant->soldeDebit= ($solde >= 0) ? $solde : 0;
                                        $newMontant->soldeCredit = ($solde < 0) ? abs($solde) : 0;
                                        $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]] = $newMontant;
                                    }
                                    else
                                    {
                                        $newMontant = $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]];
                                        $newDebit = $newMontant->debit;
                                        $newCredit = $newMontant->credit;

                                        /**
                                         * Modif 2017-11-06
                                         */
                                        if($old != null)
                                        {
                                            $newDebit += $old->debit;
                                            $newCredit += $old->credit;
                                        }
                                        /*if(intval(substr($keyPcc,0,1)) < 6 && $old != null)
                                        {
                                            $newDebit += $old->debit;
                                            $newCredit += $old->credit;
                                        }*/


                                        $solde = $newDebit - $newCredit;
                                        $newMontant->debit = $newDebit;
                                        $newMontant->credit = $newCredit;
                                        $newMontant->solde = $solde;
                                        $newMontant->soldeDebit= ($solde >= 0) ? $solde : 0;
                                        $newMontant->soldeCredit = ($solde < 0) ? abs($solde) : 0;

                                        $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]] = $newMontant;
                                    }
                                    $montants[$exercice.'-'.$exercicesMois[$exercice][$i].'-'.$keyPcc.'-'.$keyTiers] = $newMontant;
                                }
                            }
                        }
                    }
                    ksort($montants);
                    //$rubrique->montants = $montants;
                    /**
                     * montants[exercice-AnneeMois-pcc-tiers] = stdClass: debit,credit,soldeDebit,soldeCredit,solde
                     */

                    $montantsMois = [];
                    //eliminer tiers
                    foreach ($montants as $key => $montant)
                    {
                        $keySpliter = explode('-',$key);
                        $exercice_ = $keySpliter[0];
                        $anneeMois = $keySpliter[1];
                        $pcc = $keySpliter[2];

                        $newKey = $exercice_.'-'.$anneeMois.'-'.$pcc;
                        if(array_key_exists($newKey,$montantsMois))
                        {
                            $montantsMois[$newKey]->debit += $montant->debit;
                            $montantsMois[$newKey]->credit += $montant->credit;
                            $montantsMois[$newKey]->solde += $montant->solde;
                            $montantsMois[$newKey]->soldeDebit += $montant->soldeDebit;
                            $montantsMois[$newKey]->soldeCredit += $montant->soldeCredit;
                        }
                        else
                        {
                            $montantsMois[$newKey] = $montant;
                        }
                    }
                    $rubrique->montantsMois = $montantsMois;

                    $pos = 0;
                    foreach ($montantsMois as $key => &$montantsMoi)
                    {
                        $keySpliter = explode('-',$key);
                        $exercice = $keySpliter[0];
                        $anneeMois = $keySpliter[1];
                        $mois = substr($anneeMois,4,2);
                        $pcc = $keySpliter[2];
                        $soldePcc = $rubrique->pccsInRubriques[$pcc]->solde;

                        if(!array_key_exists($mois,$moisInPeriodes)) continue;

                        $key_array = $rubrique->libelle.'_-_'.$exercice.'_-_'.$pcc;

                        /**
                         * Modif 2017-11-06
                         */
                        if(array_key_exists($key_array,$montantsPccs) && intval(substr($pcc,0,1)) > 5 && false)
                        {
                            $montantsPccs[$key_array]->debit += $montantsMoi->debit;
                            $montantsPccs[$key_array]->credit += $montantsMoi->credit;
                            $montantsPccs[$key_array]->solde += $montantsMoi->solde;
                            $montantsPccs[$key_array]->soldeDebit += $montantsMoi->soldeDebit;
                            $montantsPccs[$key_array]->soldeCredit += $montantsMoi->soldeCredit;

                            //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
                            $soldeCalcule = 0;
                            $caractere = '';
                            if ($soldePcc == 0)
                            {
                                $soldeCalcule = $montantsPccs[$key_array]->solde;
                            }
                            elseif ($soldePcc == 1)
                            {
                                $soldeCalcule = $montantsPccs[$key_array]->soldeDebit;
                                $caractere = 'sd';
                            }
                            elseif ($soldePcc == 2)
                            {
                                $soldeCalcule = $montantsPccs[$key_array]->soldeCredit;
                                $caractere = 'sc';
                            }
                            elseif ($soldePcc == 3)
                            {
                                $soldeCalcule = $montantsPccs[$key_array]->debit;
                                $caractere = 'd';
                            }
                            elseif ($soldePcc == 4)
                            {
                                $soldeCalcule = $montantsPccs[$key_array]->credit;
                                $caractere = 'c';
                            }
                            $montantsPccs[$key_array]->soldeCalcule = $soldeCalcule * (($caractere == 'c' || $caractere == 'sc') ? -1 : 1);
                            $montantsPccs[$key_array]->car = $caractere;
                        }
                        else
                        {
                            $montantsMoi->p = $pos;
                            $montantsMoi->compte = $pcc;
                            $montantsMoi->intitule = $rubrique->pccsInRubriques[$pcc]->pcc->getIntitule();
                            $montantsMoi->rubrique = $rubrique->libelle;
                            //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
                            $soldeCalcule = 0;
                            $caractere = '';
                            if ($soldePcc == 0)
                            {
                                $soldeCalcule = $montantsMoi->solde;
                            }
                            elseif ($soldePcc == 1)
                            {
                                $soldeCalcule = $montantsMoi->soldeDebit;
                                $caractere = 'sd';
                            }
                            elseif ($soldePcc == 2)
                            {
                                $soldeCalcule = $montantsMoi->soldeCredit;
                                $caractere = 'sc';
                            }
                            elseif ($soldePcc == 3)
                            {
                                $soldeCalcule = $montantsMoi->debit;
                                $caractere = 'd';
                            }
                            elseif ($soldePcc == 4)
                            {
                                $soldeCalcule = $montantsMoi->credit;
                                $caractere = 'c';
                            }
                            $montantsMoi->soldeCalcule = $soldeCalcule * (($caractere == 'c' || $caractere == 'sc') ? -1 : 1);
                            $montantsMoi->car = $caractere;
                            $montantsPccs[$key_array] = $montantsMoi;
                            $pos++;
                        }
                    }
                }
            }

            $pos = 0;
            foreach ($montantsPccs as $key => $montantsPcc)
            {
                $keySpliter = explode('_-_',$key);
                $rb = $keySpliter[0];
                $exercice = $keySpliter[1];
                $compte = $keySpliter[2];

                $key = $rb.'_-_'.$compte;
                if(!array_key_exists($key,$reponses))
                {
                    foreach ($exercices as $exercice_)
                    {
                        $property = 'debit_'.$exercice_;
                        $montantsPcc->$property = 0;
                        $property = 'credit_'.$exercice_;
                        $montantsPcc->$property = 0;
                        $property = 'soldeDebit_'.$exercice_;
                        $montantsPcc->$property = 0;
                        $property = 'soldeCredit_'.$exercice_;
                        $montantsPcc->$property = 0;
                        $property = 'solde_'.$exercice_;
                        $montantsPcc->$property = 0;
                        $property = 'soldeCalcule_'.$exercice_;
                        $montantsPcc->$property = 0;
                        $property = 'car_'.$exercice_;
                        $montantsPcc->$property = '';
                    }

                    $property = 'debit_'.$exercice;
                    $montantsPcc->$property = $montantsPcc->debit;
                    $property = 'credit_'.$exercice;
                    $montantsPcc->$property = $montantsPcc->credit;
                    $property = 'soldeDebit_'.$exercice;
                    $montantsPcc->$property  = $montantsPcc->soldeDebit;
                    $property = 'soldeCredit_'.$exercice;
                    $montantsPcc->$property = $montantsPcc->soldeCredit;
                    $property = 'solde_'.$exercice;
                    $montantsPcc->$property = $montantsPcc->solde;
                    $property = 'soldeCalcule_'.$exercice;
                    $montantsPcc->$property = $montantsPcc->soldeCalcule;
                    $property = 'car_'.$exercice;
                    $montantsPcc->$property = $montantsPcc->car;

                    $reponses[$key] = $montantsPcc;
                }
                else
                {
                    $montantsPcc->p = $pos;
                    $property = 'debit_'.$exercice;
                    $reponses[$key]->$property = $montantsPcc->debit;
                    $property = 'credit_'.$exercice;
                    $reponses[$key]->$property = $montantsPcc->credit;
                    $property = 'soldeDebit_'.$exercice;
                    $reponses[$key]->$property  = $montantsPcc->soldeDebit;
                    $property = 'soldeCredit_'.$exercice;
                    $reponses[$key]->$property = $montantsPcc->soldeCredit;
                    $property = 'solde_'.$exercice;
                    $reponses[$key]->$property = $montantsPcc->solde;
                    $property = 'soldeCalcule_'.$exercice;
                    $reponses[$key]->$property = $montantsPcc->soldeCalcule;
                    $property = 'car_'.$exercice;
                    $reponses[$key]->$property = $montantsPcc->car;
                    $pos++;
                }
            }

            $datas = [];
            foreach ($reponses as $reponse) $datas[] = $reponse;
            $reponses = $datas;
        }

        $result->titre = $titre;
        $result->datas = $reponses;
        $result->exercices = $exercices;
        $result->userData = [];
        $result->arrondir = ($isEtat) ? 0 : (($indicateur->getIsDecimal() == 1) ? 2 : 0);
        return $result;
    }

    /**
     * @param Dossier $dossier
     * @param $indicateur
     * @param $exercices
     * @param $moiss
     * @param $code_graphe
     * @param $analyse
     * @param $periodes
     * @param $dateAnciennete
     * @param $anciennetes
     * @param $category
     * @param $name
     * @param $isTd
     * @param $row
     * @param $col
     * @param bool $isEtat
     * @return stdClass
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDetailsV4(Dossier $dossier,$indicateur,$exercices,$moiss,$code_graphe,$analyse,$periodes,$dateAnciennete,$anciennetes,$category,$name,$isTd,$row,$col,$isEtat = false)
    {
        if($isTd) return $this->getDetailsTdV4($dossier,$indicateur,$exercices,$moiss,$code_graphe,$analyse,$periodes,$dateAnciennete,$anciennetes,$category,$name,$isTd,$row,$col,$isEtat);

        $titre = $indicateur->getLibelle() . ' (';
        $per = '';
        $moisInPeriodes = [];
        foreach ($periodes as $periode)
        {
            foreach ($periode->moiss as $mois)
            {
                $moisInPeriodes[$mois] = $periode->libelle;
            }
        }
        /**
         * $moisInPeriodes[mois] = periode
         */

        $nomsAffichers = explode(';',$indicateur->getLibelleAffiche());
        $exercice = 0;
        if($indicateur->getTypeOperation() == 0)
        {
            if($analyse == 1 && count($nomsAffichers) <= 1)
            {
                $exercice = intval($name);
                $per = $category;
            }
            else
            {
                $categorySpliter = explode('-',$category);
                $exercice = intval($categorySpliter[0]);
                $per = $categorySpliter[1];
            }

            $moisInPeriodes = [];
            foreach ($periodes as $periode)
            {
                if(trim($per) != trim($periode->libelle)) continue;

                foreach ($periode->moiss as $mois)
                {
                    $moisInPeriodes[$mois] = $periode->libelle;
                }
            }
        }

        $operandes = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandes($indicateur);

        if($exercice != 0) $exercices = array(0 => $exercice);
        $nbRubriquesInOperandes = [];
        $debut = 0;
        $fin = count($operandes);
        $formulesSpliters = explode(';',$indicateur->getFormule());

        if(count($nomsAffichers) > 1)
        {
            foreach ($nomsAffichers as $key => $nomsAfficher)
            {
                $nbRubriquesInOperandes[$key] = substr_count($formulesSpliters[$key], '#');

                if(strtoupper(trim($nomsAfficher)) == strtoupper(trim($name)))
                {
                    $debut = 0;
                    for($i = 0; $i < count($nbRubriquesInOperandes) - 1; $i++)
                    {
                        $debut += $nbRubriquesInOperandes[$i];
                    }
                    $fin = $debut + $nbRubriquesInOperandes[count($nbRubriquesInOperandes) - 1];
                    $titre .= $nomsAfficher;
                    break;
                }
            }
        }

        $titre .= ' '.$exercice.'-'.$per.' )';
        $operandesRubriques = array();

        $index = -1;
        foreach ($operandes as $operande)
        {
            $index++;
            if($index < $debut || $index >= $fin) continue;
            $operandesRubriques[] = $this->getEntityManager()->getRepository('AppBundle:Rubrique')->getRubriquesInOperandesV2($operande,$dossier);
        }
        /**
         *  $operandesRubriques[stdClass]
         * ->variation;
         * ->formule;
         * ->rubriques[]: pccsInRubriques[compte]: pcc,solde,typeCompte
         */

        $pccs = array();
        foreach ($operandesRubriques as $operandesRubrique)
        {
            foreach ($operandesRubrique->rubriques as $rubrique)
            {
                foreach ($rubrique->pccsInRubriques as $pccsInRubrique)
                {
                    $pcc = $pccsInRubrique->pcc;
                    $compte = $pcc->getCompte();
                    if(!array_key_exists($compte,$pccs)) $pccs[$compte] = $pcc;
                }
            }
        }
        /**
         * pccs : all pcc
         */
        $ecritures = $this->getEntityManager()->getRepository('AppBundle:Ecriture')->getEcrituresPccGrouped($dossier,$pccs,$exercices,$moiss);
        /**
         * ecritures : ecriture,db,cr,ym,an
         */

        $debutsExercices = array();
        $exercicesMois = array();
        foreach ($exercices as $exercice)
        {
            $debutsExercices[$exercice] = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateDebut($dossier,$exercice);
            $exercicesMois[$exercice] = array();
            foreach ($moiss as $mois)
            {
                $annee = $exercice;
                if($dossier->getCloture() != 12)
                {
                    if(intval($mois) > $dossier->getCloture()) $annee--;
                }
                $exercicesMois[$exercice][] = $annee.$mois;
            }
        }
        /**
         * $exercicesMois[exercice][anneeMois]
         * $debutsExercices[exercice] = dateDebutExercice
         */

        $journalDossiers = $this->getEntityManager()->getRepository('AppBundle:JournalDossier')->getJournalADs($dossier);
        $tiersEachs = array();
        $tiersObjects = array();
        $ecrituresMoisPcs = array();
        foreach ($ecritures as $ecr)
        {
            $ecriture = $ecr['ecr'];
            $exercice = $ecriture->getExercice();
            $debutExercice = $debutsExercices[$exercice];
            $dateEcr = $ecriture->getDateEcr();
            if($dateEcr < $debutExercice) $dateEcr = $debutExercice;
            $tiers = $ecriture->getTiers();
            $pcc = ($ecriture->getPcc() != null) ? $ecriture->getPcc() : $tiers->getPcc();
            $tiersId = ($tiers != null) ? $tiers->getId() : 0;
            $pccId = $pcc->getCompte();
            $anneeMois = $dateEcr->format('Ym');

            $stdEcriture = new stdClass();
            $stdEcriture->isAn = (in_array($ecriture->getJournalDossier(),$journalDossiers));
            $stdEcriture->debit = $ecr['db'];
            $stdEcriture->credit = $ecr['cr'];
            $stdEcriture->pcc = $pcc;
            $stdEcriture->tiers = $tiers;
            $stdEcriture->lettrage = trim($ecr['lettre']);
            $ecrituresMoisPcs[$exercice.'-'.$anneeMois.'-'.$pccId.'-'.$tiersId][] = $stdEcriture;

            if(!array_key_exists($pccId.'-'.$tiersId,$tiersEachs))
            {
                $tiersEachs[$pccId.'-'.$tiersId] = $tiers;
                $tiersObjects[$tiersId] = $tiers;
            }
        }
        unset($ecritures);
        /**
         * $ecrituresMoisPcs[$exercice.'-'.$anneeMois.'-'.$pccId.'-'.$tiersId][] = stdClass: isAn,debit,credit,pcc,tiers;lettrage
         */

        foreach ($ecrituresMoisPcs as $keyExerciceMoisPcc => $ecrituresMoisPc)
        {
            $keySpliter = explode('-',$keyExerciceMoisPcc);
            $exercice = $keySpliter[0];
            $anneeMois = $keySpliter[1];
            $pccKey = $keySpliter[2];
            $tiersKey = $keySpliter[3];

            foreach ($ecrituresMoisPc as $montant)
            {
                $debit = $montant->debit;
                $credit = $montant->credit;
                $isAn = $montant->isAn;
                $lettrage = trim($montant->lettrage);

                foreach ($operandesRubriques as &$operandesRubrique)
                {
                    foreach ($operandesRubrique->rubriques as &$rubrique)
                    {
                        if(!property_exists($rubrique,'ecritures')) $rubrique->ecritures = [];
                        if(array_key_exists($pccKey,$rubrique->pccsInRubriques))
                        {
                            //elimination par type de compte
                            // 0 : compte collectif; 1 : compte auxilliare ; 2 : factures non payes
                            $pcc = $rubrique->pccsInRubriques[$pccKey];
                            if(!(($pcc->typeCompte == 1 && intval($tiersKey) == 0) ||
                                ($pcc->typeCompte == 2 && $lettrage != '')))
                            {
                                $newEcriture = new stdClass();
                                $newEcriture->debit = $debit;
                                $newEcriture->credit = $credit;
                                $newEcriture->isAn = $isAn;
                                $newEcriture->lettrage = $lettrage;
                                $rubrique->ecritures[$pccKey][$tiersKey][$exercice.'-'.$anneeMois][] = $newEcriture;
                            }
                        }
                    }
                }
            }
        }
        /**
         *  $operandesRubriques[]
         *  isFormule,
         *  formule,
         *  operandesRubriques : [stdClass]
         *      ->variation;
         *      ->rubriques: pccs,solde,typeCompte,ecritures[exercice-AnneeMois][pcc][tiers] = array(ecritures) sans AN
         *      ->formule;
         */

        $res = [];
        foreach ($operandesRubriques as &$operandesRubrique)
        {
            foreach ($operandesRubrique->rubriques as &$rubrique)
            {
                $montants = [];
                if(!property_exists($rubrique,'ecritures')) $rubrique->ecritures = [];
                foreach ($rubrique->ecritures as $keyPcc => &$ecrituresTiers)
                {
                    foreach ($ecrituresTiers as $keyTiers => &$ecrituresAnneesMois)
                    {
                        ksort($ecrituresAnneesMois);
                        foreach ($ecrituresAnneesMois as $keyExerciceMois => &$ecrituresAnneesMoi)
                        {
                            $debitS = 0;
                            $creditS = 0;
                            $debitAnS = 0;
                            $creditAnS = 0;
                            foreach ($ecrituresAnneesMoi as $ecriture)
                            {
                                if($ecriture->isAn)
                                {
                                    $debitAnS += $ecriture->debit;
                                    $creditAnS += $ecriture->credit;
                                }
                                else
                                {
                                    $debitS += $ecriture->debit;
                                    $creditS += $ecriture->credit;
                                }
                            }

                            if($keyTiers != 0)
                            {
                                $soldeAn = $debitAnS - $creditAnS;
                                if($soldeAn >= 0) $debitS += $soldeAn;
                                else $creditS += abs($soldeAn);
                            }
                            else
                            {
                                $debitS += $debitAnS;
                                $creditS += $creditAnS;
                            }

                            $newMontant = new stdClass();
                            $newMontant->debit = $debitS;
                            $newMontant->credit = $creditS;
                            $ecrituresAnneesMoi = $newMontant;
                        }
                        //montant avec AN

                        //completer le tableau
                        foreach ($exercices as $exercice)
                        {
                            for ($i = 0; $i < count($exercicesMois[$exercice]); $i++)
                            {
                                $old = ($i == 0) ? null : $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i - 1]];
                                if(!array_key_exists($exercice.'-'.$exercicesMois[$exercice][$i],$ecrituresAnneesMois))
                                {
                                    $newMontant = new stdClass();
                                    $newDebit = 0;
                                    $newCredit = 0;
                                    if(intval(substr($keyPcc,0,1)) < 6 && $old != null)
                                    {
                                        $newDebit = $old->debit;
                                        $newCredit = $old->credit;
                                    }
                                    $solde = $newDebit - $newCredit;
                                    $newMontant->debit = $newDebit;
                                    $newMontant->credit = $newCredit;
                                    $newMontant->solde = $solde;
                                    $newMontant->soldeDebit= ($solde >= 0) ? $solde : 0;
                                    $newMontant->soldeCredit = ($solde < 0) ? abs($solde) : 0;
                                    $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]] = $newMontant;
                                }
                                else
                                {
                                    $newMontant = $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]];
                                    $newDebit = $newMontant->debit;
                                    $newCredit = $newMontant->credit;
                                    if(intval(substr($keyPcc,0,1)) < 6 && $old != null)
                                    {
                                        $newDebit += $old->debit;
                                        $newCredit += $old->credit;
                                    }
                                    $solde = $newDebit - $newCredit;
                                    $newMontant->debit = $newDebit;
                                    $newMontant->credit = $newCredit;
                                    $newMontant->solde = $solde;
                                    $newMontant->soldeDebit= ($solde >= 0) ? $solde : 0;
                                    $newMontant->soldeCredit = ($solde < 0) ? abs($solde) : 0;

                                    $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]] = $newMontant;
                                }
                                $montants[$exercice.'-'.$exercicesMois[$exercice][$i].'-'.$keyPcc.'-'.$keyTiers] = $newMontant;
                            }
                        }
                    }
                }
                ksort($montants);
                /**
                 * montants[exercice-AnneeMois-pcc-tiers] = stdClass: debit,credit,soldeDebit,soldeCredit,solde
                 */

                if($indicateur->getTypeOperation() == 0)
                {
                    $montantsMois = [];
                    //eliminer tiers
                    foreach ($montants as $key => $montant)
                    {
                        $keySpliter = explode('-',$key);
                        $exercice = $keySpliter[0];
                        $anneeMois = $keySpliter[1];
                        $pcc = $keySpliter[2];

                        $newKey = $exercice.'-'.$anneeMois.'-'.$pcc;
                        if(array_key_exists($newKey,$montantsMois))
                        {
                            $montantsMois[$newKey]->debit += $montant->debit;
                            $montantsMois[$newKey]->credit += $montant->credit;
                            $montantsMois[$newKey]->solde += $montant->solde;
                            $montantsMois[$newKey]->soldeDebit += $montant->soldeDebit;
                            $montantsMois[$newKey]->soldeCredit += $montant->soldeCredit;
                        }
                        else
                        {
                            $montantsMois[$newKey] = $montant;
                        }
                    }
                    //$rubrique->montantsMois = $montantsMois;

                    $montantsPccs = [];
                    $pos = 0;
                    foreach ($montantsMois as $key => &$montantsMoi)
                    {
                        $keySpliter = explode('-',$key);
                        $anneeMois = $keySpliter[1];
                        $mois = substr($anneeMois,4,2);
                        $pcc = $keySpliter[2];
                        $soldePcc = $rubrique->pccsInRubriques[$pcc]->solde;

                        if(!array_key_exists($mois,$moisInPeriodes)) continue;

                        $key_array = '_'.$pcc;
                        if(array_key_exists($key_array,$montantsPccs) && intval(substr($pcc,0,1)) > 5)
                        {
                            $montantsPccs[$key_array]->debit += $montantsMoi->debit;
                            $montantsPccs[$key_array]->credit += $montantsMoi->credit;
                            $montantsPccs[$key_array]->solde += $montantsMoi->solde;
                            $montantsPccs[$key_array]->soldeDebit += $montantsMoi->soldeDebit;
                            $montantsPccs[$key_array]->soldeCredit += $montantsMoi->soldeCredit;

                            //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
                            $soldeCalcule = 0;
                            $caractere = '';
                            if ($soldePcc == 0)
                            {
                                $soldeCalcule = $montantsPccs[$key_array]->solde;
                            }
                            elseif ($soldePcc == 1)
                            {
                                $soldeCalcule = $montantsPccs[$key_array]->soldeDebit;
                                $caractere = 'sd';
                            }
                            elseif ($soldePcc == 2)
                            {
                                $soldeCalcule = $montantsPccs[$key_array]->soldeCredit;
                                $caractere = 'sc';
                            }
                            elseif ($soldePcc == 3)
                            {
                                $soldeCalcule = $montantsPccs[$key_array]->debit;
                                $caractere = 'd';
                            }
                            elseif ($soldePcc == 4)
                            {
                                $soldeCalcule = $montantsPccs[$key_array]->credit;
                                $caractere = 'c';
                            }
                            $montantsPccs[$key_array]->soldeCalcule = $soldeCalcule * (($caractere == 'c' || $caractere == 'sc') ? -1 : 1);
                            $montantsPccs[$key_array]->car = $caractere;
                        }
                        else
                        {
                            $montantsMoi->p = $pos;
                            $montantsMoi->compte = $pcc;
                            $montantsMoi->intitule = $rubrique->pccsInRubriques[$pcc]->pcc->getIntitule();
                            $montantsMoi->rubrique = $rubrique->libelle;
                            //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
                            $soldeCalcule = 0;
                            $caractere = '';
                            if ($soldePcc == 0)
                            {
                                $soldeCalcule = $montantsMoi->solde;
                            }
                            elseif ($soldePcc == 1)
                            {
                                $soldeCalcule = $montantsMoi->soldeDebit;
                                $caractere = 'sd';
                            }
                            elseif ($soldePcc == 2)
                            {
                                $soldeCalcule = $montantsMoi->soldeCredit;
                                $caractere = 'sc';
                            }
                            elseif ($soldePcc == 3)
                            {
                                $soldeCalcule = $montantsMoi->debit;
                                $caractere = 'd';
                            }
                            elseif ($soldePcc == 4)
                            {
                                $soldeCalcule = $montantsMoi->credit;
                                $caractere = 'c';
                            }
                            $montantsMoi->soldeCalcule = $soldeCalcule * (($caractere == 'c' || $caractere == 'sc') ? -1 : 1);
                            $montantsMoi->car = $caractere;
                            $montantsPccs[$key_array] = $montantsMoi;
                            $pos++;
                        }
                    }
                    $res = array_merge($res,$montantsPccs);
                }
                elseif($indicateur->getTypeOperation() == 1)
                {
                    $tiersSelecteds = $this->getEntityManager()->getRepository('AppBundle:Tiers')->getTiersByIntitule($dossier,$category,false);

                    $tiersSelected = (count($tiersSelecteds) > 0) ? $tiersSelecteds[0] : null;
                    //$tiersSelected = $this->getEntityManager()->getRepository('AppBundle:Tiers')->getTiersByIntitule($dossier,$category);
                    $tiersIdSelected = ($tiersSelected != null) ? $tiersSelected->getId() : -1;
                    $montantsTiers = [];

                    foreach ($montants as $key => &$montant)
                    {
                        $keySpliter = explode('-',$key);
                        $keyTiers = $keySpliter[3];

                        $intituleTier = strtoupper(trim($tiersObjects[$keyTiers]->getIntitule()));
                        if($intituleTier != strtoupper(trim($category))) continue;

                        $pcc = $keySpliter[2];
                        //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
                        $soldePcc = $soldePcc = $rubrique->pccsInRubriques[$pcc]->solde;
                        $montant->p = 0;
                        $montant->compte = $tiersSelected->getCompteStr();
                        $montant->intitule = $tiersSelected->getIntitule();
                        $montant->rubrique = $rubrique->libelle;

                        $soldeCalcule = 0;
                        $caractere = '';
                        if ($soldePcc == 0)
                        {
                            $soldeCalcule = $montant->solde;
                        }
                        elseif ($soldePcc == 1)
                        {
                            $soldeCalcule = $montant->soldeDebit;
                            $caractere = 'sd';
                        }
                        elseif ($soldePcc == 2)
                        {
                            $soldeCalcule = $montant->soldeCredit;
                            $caractere = 'sc';
                        }
                        elseif ($soldePcc == 3)
                        {
                            $soldeCalcule = $montant->debit;
                            $caractere = 'd';
                        }
                        elseif ($soldePcc == 4)
                        {
                            $soldeCalcule = $montant->credit;
                            $caractere = 'c';
                        }

                        $montant->soldeCalcule = $soldeCalcule * (($caractere == 'c' || $caractere == 'sc') ? -1 : 1);
                        $montant->car = $caractere;
                        $montantsTiers[0] = $montant;
                    }
                    $res = $montantsTiers;
                    $titre = $indicateur->getLibelle().' ('.$tiersSelected->getIntitule().')';
                }
            }
        }
        /**
         *  $operandesRubriques[]
         *  isFormule,
         *  formule,
         *  operandesRubriques : [stdClass]
         *      ->variation;
         *      ->rubriques: pccs,solde,typeCompte,montants[exercice-Periode-pcc-tiers] = montant
         *      ->formule;
         */

        $userData = new stdClass();
        $userData->p = 0;
        $userData->compte = '';
        $userData->intitule = '';
        $userData->rubrique = 'Totals';
        $userData->debit = 0;
        $userData->credit = 0;
        $userData->soldeDebit = 0;
        $userData->soldeCredit = 0;
        $userData->solde = 0;
        $userData->soldeCalcule = 0;

        $results = [];
        foreach ($res as $re)
        {
            $results[] = $re;
            $userData->debit += $re->debit;
            $userData->credit += $re->credit;
            $userData->soldeDebit += $re->soldeDebit;
            $userData->soldeCredit += $re->soldeCredit;
            $userData->solde += $re->solde;
            $userData->soldeCalcule += $re->soldeCalcule;
        }

        $result = new stdClass();
        $result->titre = $titre;
        $result->datas = $results;
        $result->arrondir = ($indicateur->getIsDecimal() == 1) ? 2 : 0;
        $result->userData = $userData;
        return $result;
    }

    /**
     * @param Dossier $dossier
     * @param Indicateur $indicateur
     * @param $exercices
     * @param $moiss
     * @param $code_graphe
     * @param $analyse
     * @param $periodes
     * @param DateTime $dateAnciennete
     * @param $anciennetes
     * @param $operandesRubriques
     * @param $moisInPeriodes
     * @return stdClass
     */
    public function getResultAncienneteV4(Dossier $dossier,Indicateur $indicateur,$exercices,$moiss,$code_graphe,$analyse,$periodes,DateTime $dateAnciennete,$anciennetes,$operandesRubriques,$moisInPeriodes)
    {
        $max = 10000;
        $anciennetes[] = $max;
        usort($anciennetes, array($this, 'cmp'));
        $anciennetes = array_reverse($anciennetes, true); //$tiersMontants[id_tiers] trie decroissant

        $montantsTempsTiers = array();
        $montantsTotalTiers = array();
        $tiersObjects = array();
        $categories = array();
        $arrondirA = ($indicateur->getIsDecimal() == 1) ? 2 : 0;
        $titre = $indicateur->getDescription();
        $sousTitre = '';

        $pccs = [];
        foreach ($operandesRubriques as $operandesRubrique)
        {
            foreach ($operandesRubrique->rubriques as $rubrique)
            {
                foreach ($rubrique->pccsInRubriques as $pccsInRubrique)
                {
                    $pcc = $pccsInRubrique->pcc;
                    if (!in_array($pcc,$pccs)) $pccs[] = $pcc;
                }
            }
        }

        $ecritures = $this->getEntityManager()->getRepository('AppBundle:Ecriture')->getEcrituresOperandesParDateV2($dossier,$pccs,$exercices,$moiss,false);

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
                    if(array_key_exists($key,$montantsTempsTiers)) $montantsTempsTiers[$key] += $debit - $credit;
                    else $montantsTempsTiers[$key] = $debit - $credit;
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

        //calcul montant
        $montants = array(0=>[],1=>[]);
        foreach ($tiersObjects as $keyTier => $tiersObject)
        {
            $index = ($montantsTotalTiers[$keyTier] > 0) ? 0 : 1;
            foreach ($anciennetes as $anciennete)
            {
                $key = $anciennete.'-'.$keyTier;
                $montant = (array_key_exists($key,$montantsTempsTiers)) ? $montantsTempsTiers[$key] : 0;

                if(array_key_exists($anciennete,$montants[$index])) $montants[$index][$anciennete] += $montant;
                else $montants[$index][$anciennete] = $montant;
            }
        }

        $courbes = $this->getEntityManager()->getRepository('AppBundle:TypeGraphe')->getArrayGraphes();
        $datas = [];
        //$anciennetes = array_reverse($anciennetes, true);
        if($courbes[$code_graphe] < 3)
        {
            $signe = ($indicateur->getId() == 16) ? -1 : 1;

            foreach ($montants as $keyMontant => $montant)
            {
                $data = [];
                for($i = 0; $i < count($anciennetes) ;$i++)
                {
                    $anciennete = $anciennetes[$i];
                    if($keyMontant == 0 && count($anciennetes) > 1)
                    {
                        if($i == 0) $categories[] = 'Moins de '.$anciennetes[$i];
                        elseif($i == count($anciennetes) - 1) $categories[] = 'Plus de '.$anciennetes[$i - 1];
                        else $categories[] = $anciennetes[$i - 1].' à '.$anciennetes[$i];
                    }
                    $data[] = (array_key_exists($anciennete,$montant)) ? $signe * $montant[$anciennete] : 0;
                }

                $chart = new stdClass();
                $chart->data = $data;
                $chart->name = ($keyMontant == 0) ? 'Débiteur' : 'Créditeur';
                $datas[] = $chart;
            }

            if(count($anciennetes) == 1) $categories = array(0=>'Tous');
        }

        $result = new stdClass();
        $result->datas = $datas;
        $result->categories = $categories;
        return $result;
    }

    /**
     * @param Dossier $dossier
     * @param $indicateur
     * @param $exercices
     * @param $moiss
     * @param $code_graphe
     * @param $analyse
     * @param $periodes
     * @param $dateAnciennete
     * @param $anciennetes
     * @param bool $isEtat
     * @param Utilisateur|null $user
     * @return stdClass
     */
    public function getResultV4(Dossier $dossier,$indicateur,$exercices,$moiss,$code_graphe,$analyse,$periodes,$dateAnciennete,$anciennetes,$isEtat = false,Utilisateur $user = null)
    {
        error_reporting(E_ERROR);
        $exercices = array_reverse($exercices);
        if($isEtat || $this->getEntityManager()->getRepository('AppBundle:IndicateurTypeGraphe')->getIfHasTable($indicateur))
            return $this->getTableResultV4($dossier,$indicateur,$exercices,$moiss,$analyse,$code_graphe,$periodes,$isEtat,$user);

        $moisInPeriodes = array();
        foreach ($periodes as $periode)
        {
            foreach ($periode->moiss as $mois)
            {
                $moisInPeriodes[$mois] = $periode->libelle;
            }
        }
        /**
         * $moisInPeriodes[mois] = periode
         */

        $operandes = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandes($indicateur);
        $operandesRubriques = array();
        foreach ($operandes as $operande)
        {
            $operandesRubriques[] = $this->getEntityManager()->getRepository('AppBundle:Rubrique')->getRubriquesInOperandesV2($operande,$dossier);
        }
        /**
         *  $operandesRubriques[stdClass]
         * ->variation;
         * ->formule;
         * ->rubriques[]: pccsInRubriques[compte]: pcc,solde,typeCompte
         */

        /**
         * affichage par anciennete
         */
        if($indicateur->getTypeOperation() == 3)
        {
            return $this->getResultAncienneteV4($dossier,$indicateur,$exercices,$moiss,$code_graphe,$analyse,$periodes,$dateAnciennete,$anciennetes,$operandesRubriques,$moisInPeriodes);
        }

        $pccs = array();
        foreach ($operandesRubriques as $operandesRubrique)
        {
            foreach ($operandesRubrique->rubriques as $rubrique)
            {
                foreach ($rubrique->pccsInRubriques as $pccsInRubrique)
                {
                    $pcc = $pccsInRubrique->pcc;
                    $compte = $pcc->getCompte();
                    if(!array_key_exists($compte,$pccs))
                    {
                        $pccs[$compte] = $pcc;
                    }
                }
            }
        }

        /**
         * pccs : all pcc
         */
        $ecritures = $this->getEntityManager()->getRepository('AppBundle:Ecriture')->getEcrituresPccGrouped($dossier,$pccs,$exercices,$moiss);
        /**
         * ecritures : ecriture,db,cr,ym,an
         */

        $debutsExercices = array();
        $exercicesMois = array();
        foreach ($exercices as $exercice)
        {
            $debutsExercices[$exercice] = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateDebut($dossier,$exercice);
            $exercicesMois[$exercice] = array();
            foreach ($moiss as $mois)
            {
                $annee = $exercice;
                if($dossier->getCloture() != 12)
                {
                    if(intval($mois) > $dossier->getCloture()) $annee--;
                }
                $exercicesMois[$exercice][] = $annee.$mois;
            }
        }
        /**
         * $exercicesMois[exercice][anneeMois]
         * $debutsExercices[exercice] = dateDebutExercice
         */

        $journalDossiers = $this->getEntityManager()->getRepository('AppBundle:JournalDossier')->getJournalADs($dossier);
        $tiersEachs = array();
        $tiersObjects = array();
        $ecrituresMoisPcs = array();
        foreach ($ecritures as $ecr)
        {
            $ecriture = $ecr['ecr'];
            $exercice = $ecriture->getExercice();
            $debutExercice = $debutsExercices[$exercice];
            $dateEcr = $ecriture->getDateEcr();
            if($dateEcr < $debutExercice) $dateEcr = $debutExercice;
            $tiers = $ecriture->getTiers();
            $pcc = ($ecriture->getPcc() != null) ? $ecriture->getPcc() : $tiers->getPcc();
            $tiersId = ($tiers != null) ? $tiers->getId() : 0;
            $pccId = $pcc->getCompte();
            $anneeMois = $dateEcr->format('Ym');

            $stdEcriture = new stdClass();
            $stdEcriture->isAn = (in_array($ecriture->getJournalDossier(),$journalDossiers));
            $stdEcriture->debit = $ecr['db'];
            $stdEcriture->credit = $ecr['cr'];
            $stdEcriture->pcc = $pcc;
            $stdEcriture->tiers = $tiers;
            $stdEcriture->lettrage = trim($ecr['lettre']);
            $ecrituresMoisPcs[$exercice.'-'.$anneeMois.'-'.$pccId.'-'.$tiersId][] = $stdEcriture;

            if(!array_key_exists($pccId.'-'.$tiersId,$tiersEachs))
            {
                $tiersEachs[$pccId.'-'.$tiersId] = $tiers;
                $tiersObjects[$tiersId] = $tiers;
            }
        }
        unset($ecritures);
        /**
         * $ecrituresMoisPcs[$exercice.'-'.$anneeMois.'-'.$pccId.'-'.$tiersId][] = stdClass: isAn,debit,credit,pcc,tiers;lettrage
         */

        foreach ($ecrituresMoisPcs as $keyExerciceMoisPcc => $ecrituresMoisPc)
        {
            $keySpliter = explode('-',$keyExerciceMoisPcc);
            $exercice = $keySpliter[0];
            $anneeMois = $keySpliter[1];
            $pccKey = $keySpliter[2];
            $tiersKey = $keySpliter[3];

            foreach ($ecrituresMoisPc as $montant)
            {
                $debit = $montant->debit;
                $credit = $montant->credit;
                $isAn = $montant->isAn;
                $lettrage = trim($montant->lettrage);

                foreach ($operandesRubriques as &$operandesRubrique)
                {
                    foreach ($operandesRubrique->rubriques as &$rubrique)
                    {
                        if(!property_exists($rubrique,'ecritures')) $rubrique->ecritures = [];
                        if(array_key_exists($pccKey,$rubrique->pccsInRubriques))
                        {
                            //elimination par type de compte
                            // 0 : compte collectif; 1 : compte auxilliare ; 2 : factures non payes
                            $pcc = $rubrique->pccsInRubriques[$pccKey];
                            if(!(($pcc->typeCompte == 1 && intval($tiersKey) == 0) ||
                                ($pcc->typeCompte == 2 && $lettrage != '')))
                            {
                                $newEcriture = new stdClass();
                                $newEcriture->debit = $debit;
                                $newEcriture->credit = $credit;
                                $newEcriture->isAn = $isAn;
                                $newEcriture->lettrage = $lettrage;
                                $rubrique->ecritures[$pccKey][$tiersKey][$exercice.'-'.$anneeMois][] = $newEcriture;
                            }
                        }
                    }
                }
            }
        }
        /**
         *  $operandesRubriques[]
         *  isFormule,
         *  formule,
         *  operandesRubriques : [stdClass]
         *      ->variation;
         *      ->rubriques: pccs,solde,typeCompte,ecritures[exercice-AnneeMois][pcc][tiers] = array(ecritures) sans AN
         *      ->formule;
         */

        foreach ($operandesRubriques as &$operandesRubrique)
        {
            foreach ($operandesRubrique->rubriques as &$rubrique)
            {
                $montants = [];
                if(!property_exists($rubrique,'ecritures')) $rubrique->ecritures = [];
                foreach ($rubrique->ecritures as $keyPcc => &$ecrituresTiers)
                {
                    foreach ($ecrituresTiers as $keyTiers => &$ecrituresAnneesMois)
                    {
                        ksort($ecrituresAnneesMois);
                        foreach ($ecrituresAnneesMois as $keyExerciceMois => &$ecrituresAnneesMoi)
                        {
                            $debitS = 0;
                            $creditS = 0;
                            $debitAnS = 0;
                            $creditAnS = 0;
                            foreach ($ecrituresAnneesMoi as $ecriture)
                            {
                                if($ecriture->isAn)
                                {
                                    $debitAnS += $ecriture->debit;
                                    $creditAnS += $ecriture->credit;
                                }
                                else
                                {
                                    $debitS += $ecriture->debit;
                                    $creditS += $ecriture->credit;
                                }
                            }

                            if($keyTiers != 0)
                            {
                                $soldeAn = $debitAnS - $creditAnS;
                                if($soldeAn >= 0) $debitS += $soldeAn;
                                else $creditS += abs($soldeAn);
                            }
                            else
                            {
                                $debitS += $debitAnS;
                                $creditS += $creditAnS;
                            }

                            $newMontant = new stdClass();
                            $newMontant->debit = $debitS;
                            $newMontant->credit = $creditS;
                            $ecrituresAnneesMoi = $newMontant;
                        }
                        //montant avec AN

                        //completer le tableau
                        foreach ($exercices as $exercice)
                        {
                            for ($i = 0; $i < count($exercicesMois[$exercice]); $i++)
                            {
                                $old = ($i == 0) ? null : $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i - 1]];
                                if(!array_key_exists($exercice.'-'.$exercicesMois[$exercice][$i],$ecrituresAnneesMois))
                                {
                                    $newMontant = new stdClass();
                                    $newDebit = 0;
                                    $newCredit = 0;
                                    if(intval(substr($keyPcc,0,1)) < 6 && $old != null)
                                    {
                                        $newDebit = $old->debit;
                                        $newCredit = $old->credit;
                                    }
                                    $solde = $newDebit - $newCredit;
                                    $newMontant->debit = $newDebit;
                                    $newMontant->credit = $newCredit;
                                    $newMontant->solde = $solde;
                                    $newMontant->soldeDebit= ($solde >= 0) ? $solde : 0;
                                    $newMontant->soldeCredit = ($solde < 0) ? abs($solde) : 0;
                                    $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]] = $newMontant;
                                }
                                else
                                {
                                    $newMontant = $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]];
                                    $newDebit = $newMontant->debit;
                                    $newCredit = $newMontant->credit;
                                    /**
                                     * Modif 2017-11-06 < 6 => < 9
                                     */
                                    if(intval(substr($keyPcc,0,1)) < 6 && $old != null)
                                    {
                                        $newDebit += $old->debit;
                                        $newCredit += $old->credit;
                                    }
                                    $solde = $newDebit - $newCredit;
                                    $newMontant->debit = $newDebit;
                                    $newMontant->credit = $newCredit;
                                    $newMontant->solde = $solde;
                                    $newMontant->soldeDebit= ($solde >= 0) ? $solde : 0;
                                    $newMontant->soldeCredit = ($solde < 0) ? abs($solde) : 0;

                                    $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]] = $newMontant;
                                }
                                $montants[$exercice.'-'.$exercicesMois[$exercice][$i].'-'.$keyPcc.'-'.$keyTiers] = $newMontant;
                            }
                        }
                    }
                }
                ksort($montants);
                /**
                 * montants[exercice-AnneeMois-pcc-tiers] = stdClass: debit,credit,soldeDebit,soldeCredit,solde
                 */

                $montantsMois = [];
                foreach ($montants as $keyTemp => $montant)
                {
                    $keySpliter = explode('-',$keyTemp);
                    $keyPcc = $keySpliter[2];
                    $soldePcc = $rubrique->pccsInRubriques[$keyPcc]->solde;
                    //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
                    if ($soldePcc == 1) $m = $montant->soldeDebit;
                    elseif ($soldePcc == 2) $m = $montant->soldeCredit;
                    elseif ($soldePcc == 3) $m = $montant->debit;
                    elseif ($soldePcc == 4) $m = $montant->credit;
                    else $m = $montant->solde;
                    $montantsMois[$keyTemp] = $m;
                }
                /**
                 * $montantsMois[exercice-AnneeMois-pcc-tiers] = montant
                 */

                $montantsPeriodes = [];
                foreach ($montantsMois as $keyTemp => $montantsMoi)
                {
                    $keySpliter = explode('-',$keyTemp);
                    $exercice = $keySpliter[0];
                    $anneeMois = $keySpliter[1];
                    $annee = substr($anneeMois,0,4);
                    $mois = substr($anneeMois,4,2);
                    $keyPcc = $keySpliter[2];
                    $keyTiers = $keySpliter[3];

                    if(!array_key_exists($mois,$moisInPeriodes)) continue;
                    $keyPeriode = $moisInPeriodes[$mois];
                    $keyNew = $exercice.'-'.$keyPeriode.'-'.$keyPcc.'-'.$keyTiers;

                    /**
                     * Modif 2017-11-06 >=6 => >= 0
                     */
                    if(array_key_exists($keyNew,$montantsPeriodes) && intval(substr($keyPcc,0,1)) >= 6)
                    {
                        $montantsPeriodes[$keyNew] += $montantsMoi;
                    }
                    else $montantsPeriodes[$keyNew] = $montantsMoi;
                }
                /**
                 * $montantsPeriodes[exercice-Periode-pcc-tiers] = montant
                 */
                $rubrique->montants = $montantsPeriodes;
            }
        }
        /**
         *  $operandesRubriques[]
         *  isFormule,
         *  formule,
         *  operandesRubriques : [stdClass]
         *      ->variation;
         *      ->rubriques: pccs,solde,typeCompte,montants[exercice-Periode-pcc-tiers] = montant
         *      ->formule;
         */

        $datas = array();
        $categories = array();
        $langage = new ExpressionLanguage();
        $courbes = $this->getEntityManager()->getRepository('AppBundle:TypeGraphe')->getArrayGraphes();

        /**
         * AFFICHAGE PAR MOIS
         */
        if($indicateur->getTypeOperation() == 0)
        {
            //elimination tiers pcc
            foreach ($operandesRubriques as &$operandesRubrique)
            {
                foreach ($operandesRubrique->rubriques as &$rubrique)
                {
                    unset($rubrique->ecritures);
                    $montantsPeriodes = [];
                    foreach ($rubrique->montants as $keyTemp => $montant)
                    {
                        $keySpliter = explode('-',$keyTemp);
                        $exercice = $keySpliter[0];
                        $keyPeriode = $keySpliter[1];
                        $keyPcc = $keySpliter[2];
                        $keyNew = $exercice.'-'.$keyPeriode;
                        $pccSolde = $rubrique->pccsInRubriques[$keyPcc]->solde;
                        //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
                        $m = ($pccSolde == 2 || $pccSolde == 4) ? -$montant : $montant;
                        if(array_key_exists($keyNew,$montantsPeriodes))
                        {
                            $montantsPeriodes[$keyNew] += $m;
                        }
                        else $montantsPeriodes[$keyNew] = $m;
                    }
                    $rubrique->montantsPeriodes = $montantsPeriodes;
                    unset($rubrique->montants);
                }

                $formule = $operandesRubrique->formule;
                $formuleEval = '';
                $chars = str_split($formule);
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

                $montantsRubriques = [];
                foreach ($exercices as $exercice)
                {
                    foreach ($periodes as $periode)
                    {
                        $keyNew = $exercice.'-'.$periode->libelle;
                        $indexOperande = 0;
                        $listVal = [];
                        foreach ($operandesRubrique->rubriques as &$rubrique)
                        {
                            $valeur = (array_key_exists($keyNew,$rubrique->montantsPeriodes)) ? $rubrique->montantsPeriodes[$keyNew] : 0;
                            $listVal['_'.$indexOperande] = $valeur;
                            $indexOperande++;
                        }

                        try
                        {
                            $eval = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$formuleEval))),$listVal);
                        }
                        catch (\ErrorException $s)
                        {
                            $eval = 0;
                        }

                        if (is_infinite($eval) || is_nan($eval)) $eval = 0;
                        //$eval = $langage->evaluate($formuleEval,$listVal);
                        $montantsRubriques[$exercice.'-'.$periode->libelle] = $eval;
                    }
                }

                $operandesRubrique->montantsPeriodes = $montantsRubriques;
            }
            /**
             *  $operandesRubriques[]
             *  isFormule,
             *  formule,
             *  operandesRubriques : [stdClass]
             *      ->variation;
             *      ->rubriques: pccs,solde,typeCompte,montants[exercice-Periode-pcc-tiers] = montant
             *      ->formule;
             *      ->montantsPeriodes[exercice-periode]
             */

            $formule = $indicateur->getFormule();
            $formulesSpliters = explode(';',$formule);
            //pas de comparaison
            if(count($formulesSpliters) == 1)
            {
                $formuleEval = '';
                $chars = str_split($formule);
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

                $montantsAffichers = array();
                foreach ($exercices as $exercice)
                {
                    foreach ($periodes as $periode)
                    {
                        $indexOperande = 0;
                        $listVal = array();
                        foreach ($operandesRubriques as &$operandesRubrique)
                        {
                            $montantObject = $operandesRubrique->montantsPeriodes;
                            $montant = $montantObject[$exercice.'-'.$periode->libelle];
                            $listVal['_'.$indexOperande] = $montant;
                            $indexOperande++;
                        }

                        try
                        {
                            $eval = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$formuleEval))),$listVal);
                        }
                        catch (\ErrorException $s)
                        {
                            $eval = 0;
                        }

                        if (is_infinite($eval) || is_nan($eval)) $eval = 0;
                        //$eval = $langage->evaluate($formuleEval,$listVal);
                        $montantsAffichers[$exercice.'-'.$periode->libelle] = $eval;
                    }
                }
                /**
                 * $montantsAffichers[exercice-periode] = montant
                 */

                if($courbes[$code_graphe] < 4)
                {
                    if($analyse == 0) //evolution
                    {
                        $data = array();
                        foreach ($montantsAffichers as $key => $montantPeriode)
                        {
                            $categories[] = $key;
                            $data[] = $montantPeriode;
                        }

                        $chart = new stdClass();
                        $chart->data = $data;
                        $chart->name = implode(' , ',$exercices);
                        $datas[] = $chart;
                    }
                    if($analyse == 1) //comparaison
                    {
                        foreach ($exercices as $exercice)
                        {
                            $data = array();
                            foreach ($periodes as $periode)
                            {
                                if($exercice == $exercices[0]) $categories[] = $periode->libelle;
                                $data[] = $montantsAffichers[$exercice.'-'.$periode->libelle];
                            }

                            $chart = new stdClass();
                            $chart->data = $data;
                            $chart->name = $exercice;
                            $datas[] = $chart;
                        }
                    }
                }
                elseif($courbes[$code_graphe] == 4)
                {
                    $data = array();
                    $first = true;
                    foreach ($exercices as $exercice)
                    {
                        foreach ($periodes as $periode)
                        {
                            $l = $exercice.'-'.$periode->libelle;
                            $value = $montantsAffichers[$exercice.'-'.$periode->libelle];
                            if($first)
                            {
                                $sliced = new stdClass();
                                $sliced->name = $l;
                                $sliced->y = $value;
                                $sliced->sliced = true;
                                $sliced->selected = true;
                                $data[] = $sliced;
                            }
                            else $data[] = array($l,$value);
                            $first = false;
                        }
                    }

                    $serie = new stdClass();
                    $serie->type = 'pie';
                    $serie->name = 'Valeur';
                    $serie->data = $data;
                    $datas[] = $serie;
                }
            }
            else
            {
                $montantsAffichers = array();
                $indexOperande = 0;
                $index = 0;
                foreach ($formulesSpliters as &$formulesSpliter)
                {
                    $formule = $formulesSpliter;
                    $chars = str_split($formule);
                    $formuleEval = '';
                    $indexOperande_ = 0;
                    foreach($chars as $char)
                    {
                        if($char == '#')
                        {
                            $formuleEval .= '_'.$indexOperande_;
                            $indexOperande_++;
                        }
                        else $formuleEval .= $char;
                    }

                    foreach ($exercices as $exercice)
                    {
                        foreach ($periodes as $periode)
                        {
                            $listVal = array();
                            for($i = $indexOperande; $i < ($indexOperande + $indexOperande_); $i++)
                            {
                                $montant = $operandesRubriques[$i]->montantsPeriodes[$exercice.'-'.$periode->libelle];
                                $listVal['_'.($i-$indexOperande)] = $montant;
                            }

                            try
                            {
                                $eval = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$formuleEval))),$listVal);
                            }
                            catch (\ErrorException $s)
                            {
                                $eval = 0;
                            }

                            if (is_infinite($eval) || is_nan($eval)) $eval = 0;
                            //$eval = $langage->evaluate($formuleEval,$listVal);
                            $montantsAffichers[$index][$exercice.'-'.$periode->libelle] = $eval;
                        }
                    }
                    $indexOperande += $indexOperande_;
                    $index++;
                }
                /**
                 * $montantsAffichers[indexOperande][exercice-periode] = montant
                 */

                $nomAffichers = explode(';',$indicateur->getLibelleAffiche());
                if($courbes[$code_graphe] < 3)
                {
                    $index = 0;
                    foreach ($montantsAffichers as $keyOperande => $montantsAfficher)
                    {
                        $data = array();
                        foreach ($montantsAfficher as $keyPeriode => $montant)
                        {
                            $categories[] = $keyPeriode;
                            $data[] = $montant;
                        }

                        $chart = new stdClass();
                        $chart->data = $data;
                        $chart->name = (trim($nomAffichers[$index]) != '') ? trim($nomAffichers[$index]) : 'Non parametré';
                        $index++;
                        $datas[] = $chart;
                    }
                }
                elseif($courbes[$code_graphe] == 4)
                {
                    $data = array();
                    $first = true;
                    $index = 0;

                    foreach ($montantsAffichers as $keyOperande => $montantsAfficher)
                    {
                        foreach ($montantsAfficher as $keyPeriode => $montant)
                        {
                            $l = $keyPeriode.':'.((trim($nomAffichers[$index]) != '') ? trim($nomAffichers[$index]) : 'Non parametré');
                            $value = $montant;
                            if($first)
                            {
                                $sliced = new stdClass();
                                $sliced->name = $l;
                                $sliced->y = $value;
                                $sliced->sliced = true;
                                $sliced->selected = true;
                                $data[] = $sliced;
                            }
                            else $data[] = array($l,$value);
                            $first = false;
                        }
                        $index++;
                    }

                    $serie = new stdClass();
                    $serie->type = 'pie';
                    $serie->name = 'Valeur';
                    $serie->data = $data;
                    $datas[] = $serie;
                }
            }
        }
        /**
         * AFFICHAGE PAR TIERS
         */
        elseif($indicateur->getTypeOperation() == 1)
        {
            //$signe = 1;
            $signe = ($indicateur->getId() == 19) ? -1 : 1;

            $limite = $indicateur->getMax();
            $montantsPeriodes = array();
            foreach($operandesRubriques as &$operandesRubrique)
            {
                foreach ($operandesRubrique->rubriques as &$rubrique)
                {
                    unset($rubrique->ecritures);
                    //elimination compte et transformation mois en periode et non collectif
                    foreach ($rubrique->montants as $keyMontant => &$montant)
                    {
                        $keySpliter = explode('-',$keyMontant);
                        $tiersId = intval($keySpliter[3]);

                        if($tiersId == 0) continue;
                        $key = $tiersId;
                        $montantsPeriodes[$key] = $montant * $signe;
                    }
                }
            }
            /**
             * $montantsPeriodes[idTier] = montant
             */
            uasort($montantsPeriodes, array($this, 'cmp'));
            $montantsPeriodes = array_reverse($montantsPeriodes, true);//$tiersMontants[id_tiers] trie decroissant

            if($courbes[$code_graphe] < 3)
            {
                $nbr = 0;
                $data = array();
                foreach ($montantsPeriodes as $tiersId => $montantsPeriode)
                {
                    $categories[] = $tiersObjects[$tiersId]->getIntitule();
                    $data[] = $montantsPeriode;
                    $nbr++;
                    if($nbr == $limite) break;
                }
                $chart = new stdClass();
                $chart->data = $data;
                $chart->name = $exercices[count($exercices) - 1];
                $datas[] = $chart;
            }
            elseif($courbes[$code_graphe] == 4)
            {
                $data = array();
                $first = true;
                $nbr = 0;

                foreach ($montantsPeriodes as $tiersId => $montantsPeriode)
                {
                    $l = $tiersObjects[$tiersId]->getIntitule();
                    $value = $montantsPeriode;
                    if($first)
                    {
                        $sliced = new stdClass();
                        $sliced->name = $l;
                        $sliced->y = $value;
                        $sliced->sliced = true;
                        $sliced->selected = true;
                        $data[] = $sliced;
                    }
                    else $data[] = array($l,$value);
                    $first = false;
                    $nbr++;
                    if($nbr == $limite) break;
                }

                $serie = new stdClass();
                $serie->type = 'pie';
                $serie->name = 'Valeur';
                $serie->data = $data;
                $datas[] = $serie;
            }
        }

        $result = new stdClass();
        $result->datas = $datas;
        $result->id = Boost::boost($indicateur->getId());
        $result->categories = $categories;
        $result->analyse = $analyse;
        $result->code_graphe = $code_graphe;
        $result->titre = $indicateur->getDescription();
        $result->sousTitre = '';
        $result->unite = $indicateur->getUnite();
        $result->arrondirA = ($indicateur->getIsDecimal() == 1) ? 2 : 0;
        return $result;
    }

    /**
     * @param Dossier $dossier
     * @param $indicateur
     * @param $exercices
     * @param $moiss
     * @param $analyse
     * @param $code_graphe
     * @param $periodes
     * @param bool $isEtat
     * @param Utilisateur|null $user
     * @return stdClass
     */
    public function getTableResultV4(Dossier $dossier,$indicateur,$exercices,$moiss,$analyse,$code_graphe,$periodes,$isEtat = false,Utilisateur $user = null)
    {
        $erreurTableau = [];
        $exerciceSel = $exercices[0];
        $rowNumber = $indicateur->getRowNumber();
        $colNumber = $indicateur->getColNumber();
        $indicateurCells = ($isEtat) ?
            $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCellsEtats($indicateur) :
            $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCells($indicateur);

        $moisInPeriodes[] = array();
        foreach ($periodes as $periode)
        {
            foreach ($periode->moiss as $mois)
            {
                $moisInPeriodes[$mois] = $periode->libelle;
            }
        }
        /**
         * $moisInPeriodes[mois] = periode
         */

        $cells = [];
        $cellsStyles = [];
        $exercices = [];
        $exercicesCols = [];
        $newStyles = [];
        foreach ($indicateurCells as &$indicateurCell)
        {
            $row = $indicateurCell->getRow();
            $col = $indicateurCell->getCol();

            $cell = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCellsCompletedV2($indicateurCell,$dossier);
            $formule = str_replace(' ','',$indicateurCell->getFormule());
            if($formule == 'N')
            {
                $exercicesCols[$col] = intval($exerciceSel);
                $cell->formule = intval($exerciceSel);
            }
            elseif (substr($formule,0,1) == 'N')
            {
                $variation = intval(substr($formule,1,strlen($formule) - 1));
                $exercicesCols[$col] = $exerciceSel + $variation;
                $cell->formule = $exerciceSel + $variation;
            }
            $cells[$row.'-'.$col] = $cell;

            $newStyles[$row.'-'.$col] = $indicateurCell->getStylesObject();
        }
        /**
         * $cells[row-col] = stClass: isFormule, formule, operandesRubriques
         */

        foreach ($cells as $cell)
        {
            foreach ($cell->operandesRubriques as $operandesRubrique)
            {
                $variation = $operandesRubrique->variation;
                $exercice_ = $exerciceSel + $variation;
                if (!array_key_exists($variation,$exercices)) $exercices[$variation] = $exercice_;
            }
        }
        /**
         * $exercices[variation] = exercice
         */

        $pccs = array();
        foreach ($cells as $cell)
        {
            foreach ($cell->operandesRubriques as $operandesRubrique)
            {
                foreach ($operandesRubrique->rubriques as $rubrique)
                {
                    foreach ($rubrique->pccsInRubriques as $pccsInRubrique)
                    {
                        $pcc = $pccsInRubrique->pcc;
                        $compte = $pcc->getCompte();
                        if(!array_key_exists($compte,$pccs)) $pccs[$compte] = $pcc;
                    }
                }
            }
        }
        /**
         * pccs : all pcc
         */

        $ecritures = $this->getEntityManager()->getRepository('AppBundle:Ecriture')->getEcrituresPccGrouped($dossier,$pccs,$exercices,$moiss);
        /**
         * ecritures : all ecriture
         */

        //return $ecritures;

        $debutsExercices = array();
        $exercicesMois = array();
        foreach ($exercices as $exercice)
        {
            $debutsExercices[$exercice] = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateDebut($dossier,$exercice);
            $exercicesMois[$exercice] = array();
            foreach ($moiss as $mois)
            {
                $annee = $exercice;
                if($dossier->getCloture() != 12)
                {
                    if(intval($mois) > $dossier->getCloture()) $annee--;
                }
                $exercicesMois[$exercice][] = $annee.$mois;
            }
        }
        /**
         * $exercicesMois[exercice][anneeMois]
         * $debutsExercices[exercice] = dateDebutExercice
         */

        $journalDossiers = $this->getEntityManager()->getRepository('AppBundle:JournalDossier')->getJournalADs($dossier);
        $tiersEachs = array();
        $tiersObjects = array();
        $ecrituresMoisPcs = array();
        foreach ($ecritures as $ecr)
        {
            $ecriture = $ecr['ecr'];
            $exercice = $ecriture->getExercice();
            $debutExercice = $debutsExercices[$exercice];
            $dateEcr = $ecriture->getDateEcr();
            if($dateEcr < $debutExercice) $dateEcr = $debutExercice;
            $tiers = $ecriture->getTiers();
            $pcc = ($ecriture->getPcc() != null) ? $ecriture->getPcc() : $tiers->getPcc();
            $tiersId = ($tiers != null) ? $tiers->getId() : 0;
            $pccId = $pcc->getCompte();
            $anneeMois = $dateEcr->format('Ym');

            $stdEcriture = new stdClass();
            $stdEcriture->isAn = (in_array($ecriture->getJournalDossier(),$journalDossiers));
            $stdEcriture->debit = $ecr['db'];
            $stdEcriture->credit = $ecr['cr'];
            $stdEcriture->pcc = $pcc;
            $stdEcriture->tiers = $tiers;
            $stdEcriture->lettrage = $ecr['lettre'];
            $ecrituresMoisPcs[$exercice.'-'.$anneeMois.'-'.$pccId.'-'.$tiersId][] = $stdEcriture;

            if(!array_key_exists($pccId.'-'.$tiersId,$tiersEachs))
            {
                $tiersEachs[$pccId.'-'.$tiersId] = $tiers;
                $tiersObjects[$tiersId] = $tiers;
            }
        }
        unset($ecritures);
        /**
         * $ecrituresMoisPcs[$exercice.'-'.$anneeMois.'-'.$pccId.'-'.$tiersId][] = stdClass: isAn,debit,credit,pcc,tiers;lettrage
         */

        foreach ($ecrituresMoisPcs as $keyExerciceMoisPcc => $ecrituresMoisPc)
        {
            $keySpliter = explode('-',$keyExerciceMoisPcc);
            $exercice = $keySpliter[0];
            $anneeMois = $keySpliter[1];
            $pccKey = $keySpliter[2];
            $tiersKey = $keySpliter[3];

            foreach ($ecrituresMoisPc as $montant)
            {
                $debit = $montant->debit;
                $credit = $montant->credit;
                $isAn = $montant->isAn;
                $lettrage = $montant->lettrage;

                foreach ($cells as &$cell)
                {
                    if(!$cell->isFormule) continue;
                    foreach ($cell->operandesRubriques as &$operandesRubrique)
                    {
                        foreach ($operandesRubrique->rubriques as &$rubrique)
                        {
                            if(!property_exists($rubrique,'ecritures')) $rubrique->ecritures = [];
                            if(array_key_exists($pccKey,$rubrique->pccsInRubriques))
                            {
                                //elimination par type de compte
                                // 0 : compte collectif; 1 : compte auxilliare ; 2 : factures non payes
                                $pcc = $rubrique->pccsInRubriques[$pccKey];
                                if($pcc->typeCompte == 1 && intval($tiersKey) == 0 ||
                                    $pcc->typeCompte == 2 && $lettrage == '') continue;

                                $newEcriture = new stdClass();
                                $newEcriture->debit = $debit;
                                $newEcriture->credit = $credit;
                                $newEcriture->isAn = $isAn;
                                $newEcriture->lettrage = $lettrage;
                                $rubrique->ecritures[$pccKey][$tiersKey][$exercice.'-'.$anneeMois][] = $newEcriture;
                            }
                        }
                    }
                }
            }
        }
        /**
         *  $cells[row-col] = stdClass
         *      ->isFormule;
         *      ->formule;
         *      ->operandesRubriques : [stdClass]
         *          ->variation;
         *          ->formule;
         *          ->rubriques: pccs,solde,typeCompte,ecritures[exercice-AnneeMois][pcc][tiers] = array(ecritures) sans AN
         */

        foreach ($cells as &$cell)
        {
            if(!$cell->isFormule) continue;
            foreach ($cell->operandesRubriques as &$operandesRubrique)
            {
                foreach ($operandesRubrique->rubriques as &$rubrique)
                {
                    $montants = [];
                    if(!property_exists($rubrique,'ecritures')) $rubrique->ecritures = [];
                    foreach ($rubrique->ecritures as $keyPcc => &$ecrituresTiers)
                    {
                        foreach ($ecrituresTiers as $keyTiers => &$ecrituresAnneesMois)
                        {
                            ksort($ecrituresAnneesMois);
                            foreach ($ecrituresAnneesMois as $keyExerciceMois => &$ecrituresAnneesMoi)
                            {
                                $debitS = 0;
                                $creditS = 0;
                                $debitAnS = 0;
                                $creditAnS = 0;
                                foreach ($ecrituresAnneesMoi as $ecriture)
                                {
                                    if($ecriture->isAn)
                                    {
                                        $debitAnS += $ecriture->debit;
                                        $creditAnS += $ecriture->credit;
                                    }
                                    else
                                    {
                                        $debitS += $ecriture->debit;
                                        $creditS += $ecriture->credit;
                                    }
                                }

                                if($keyTiers != 0)
                                {
                                    $soldeAn = $debitAnS - $creditAnS;
                                    if($soldeAn >= 0) $debitS += $soldeAn;
                                    else $creditS += abs($soldeAn);
                                }
                                else
                                {
                                    $debitS += $debitAnS;
                                    $creditS += $creditAnS;
                                }

                                $newMontant = new stdClass();
                                $newMontant->debit = $debitS;
                                $newMontant->credit = $creditS;
                                $ecrituresAnneesMoi = $newMontant;
                            }
                            //montant avec AN

                            //completer le tableau
                            foreach ($exercices as $exercice)
                            {
                                for ($i = 0; $i < count($exercicesMois[$exercice]); $i++)
                                {
                                    $old = ($i == 0) ? null : $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i - 1]];
                                    if(!array_key_exists($exercice.'-'.$exercicesMois[$exercice][$i],$ecrituresAnneesMois))
                                    {
                                        $newMontant = new stdClass();
                                        $newDebit = 0;
                                        $newCredit = 0;

                                        /**
                                         * Modif 2017-11-06
                                         */
                                        if ($old != null)
                                        {
                                            $newDebit = $old->debit;
                                            $newCredit = $old->credit;
                                        }
                                        /*if(intval(substr($keyPcc,0,1)) < 6 && $old != null)
                                        {
                                            $newDebit = $old->debit;
                                            $newCredit = $old->credit;
                                        }*/


                                        $solde = $newDebit - $newCredit;
                                        $newMontant->debit = $newDebit;
                                        $newMontant->credit = $newCredit;
                                        $newMontant->solde = $solde;
                                        $newMontant->soldeDebit= ($solde >= 0) ? $solde : 0;
                                        $newMontant->soldeCredit = ($solde < 0) ? abs($solde) : 0;
                                        $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]] = $newMontant;
                                    }
                                    else
                                    {
                                        $newMontant = $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]];
                                        $newDebit = $newMontant->debit;
                                        $newCredit = $newMontant->credit;
                                        /**
                                         * Modif 2017-11-06
                                         */
                                        if($old != null)
                                        {
                                            $newDebit += $old->debit;
                                            $newCredit += $old->credit;
                                        }
                                        /*if(intval(substr($keyPcc,0,1)) < 6 && $old != null)
                                        {
                                            $newDebit += $old->debit;
                                            $newCredit += $old->credit;
                                        }*/



                                        $solde = $newDebit - $newCredit;
                                        $newMontant->debit = $newDebit;
                                        $newMontant->credit = $newCredit;
                                        $newMontant->solde = $solde;
                                        $newMontant->soldeDebit= ($solde >= 0) ? $solde : 0;
                                        $newMontant->soldeCredit = ($solde < 0) ? abs($solde) : 0;

                                        $ecrituresAnneesMois[$exercice.'-'.$exercicesMois[$exercice][$i]] = $newMontant;
                                    }
                                    $montants[$exercice.'-'.$exercicesMois[$exercice][$i].'-'.$keyPcc.'-'.$keyTiers] = $newMontant;
                                }
                            }
                        }
                    }
                    ksort($montants);
                    /**
                     * montants[exercice-AnneeMois-pcc-tiers] = stdClass: debit,credit,soldeDebit,soldeCredit,solde
                     */

                    $montantsMois = [];
                    foreach ($montants as $keyTemp => $montant)
                    {
                        $keySpliter = explode('-',$keyTemp);
                        $keyPcc = $keySpliter[2];
                        $soldePcc = $rubrique->pccsInRubriques[$keyPcc]->solde;
                        //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
                        if ($soldePcc == 1) $m = $montant->soldeDebit;
                        elseif ($soldePcc == 2) $m = $montant->soldeCredit;
                        elseif ($soldePcc == 3) $m = $montant->debit;
                        elseif ($soldePcc == 4) $m = $montant->credit;
                        else $m = $montant->solde;

                        $montantsMois[$keyTemp] = $m;
                    }
                    /**
                     * $montantsMois[exercice-AnneeMois-pcc-tiers] = montant
                     */

                    $montantsPeriodes = [];
                    foreach ($montantsMois as $keyTemp => $montantsMoi)
                    {
                        $keySpliter = explode('-',$keyTemp);
                        $exercice = $keySpliter[0];
                        $anneeMois = $keySpliter[1];
                        $annee = substr($anneeMois,0,4);
                        $mois = substr($anneeMois,4,2);
                        $keyPcc = $keySpliter[2];
                        $keyTiers = $keySpliter[3];

                        if(!array_key_exists($mois,$moisInPeriodes)) continue;

                        $keyPeriode = $moisInPeriodes[$mois];
                        $keyNew = $exercice.'-'.$keyPeriode.'-'.$keyPcc.'-'.$keyTiers;

                        /**
                         * Modif 2017-11-06
                         */
                        $montantsPeriodes[$keyNew] = $montantsMoi;
                        /*if(array_key_exists($keyNew,$montantsPeriodes) && intval(substr($keyPcc,0,1)) >= 6)
                        {
                            $montantsPeriodes[$keyNew] += $montantsMoi;
                        }
                        else $montantsPeriodes[$keyNew] = $montantsMoi;*/
                    }
                    /**
                     * $montantsPeriodes[exercice-Periode-pcc-tiers] = montant
                     */
                    $rubrique->montants = $montantsPeriodes;
                }
            }
        }
        /**
         *  $cells[row-col] = stdClass
         *      ->isFormule;
         *      ->formule;
         *      ->operandesRubriques : [stdClass]
         *          ->variation;
         *          ->formule;
         *          ->rubriques: pccs,solde,typeCompte,montants[exercice-Periode-pcc-tiers] = montant
         */

        $error_texte = 'error_';
        $error_texte_head = 'ER-';
        $langage = new ExpressionLanguage();
        foreach ($cells as &$cell)
        {
            if(!$cell->isFormule) continue;
            foreach ($cell->operandesRubriques as &$operandesRubrique)
            {
                foreach ($operandesRubrique->rubriques as &$rubrique)
                {
                    unset($rubrique->ecritures);
                    $montantsPeriodes = [];
                    foreach ($rubrique->montants as $keyTemp => $montant)
                    {
                        $keySpliter = explode('-',$keyTemp);
                        $exercice = $keySpliter[0];
                        $keyPeriode = $keySpliter[1];
                        $keyPcc = $keySpliter[2];
                        $keyNew = $exercice.'-'.$keyPeriode;
                        $pccSolde = $rubrique->pccsInRubriques[$keyPcc]->solde;
                        //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
                        $m = ($pccSolde == 2 || $pccSolde == 4) ? -$montant : $montant;
                        if(array_key_exists($keyNew,$montantsPeriodes))
                        {
                            $montantsPeriodes[$keyNew] += $m;
                        }
                        else $montantsPeriodes[$keyNew] = $m;
                    }
                    $rubrique->montantsPeriodes = $montantsPeriodes;
                    unset($rubrique->montants);
                }

                $formule = $operandesRubrique->formule;
                $formuleEval = '';
                $chars = str_split($formule);
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

                $montantsRubriques = [];
                foreach ($exercices as $exercice)
                {
                    foreach ($periodes as $periode)
                    {
                        $keyNew = $exercice.'-'.$periode->libelle;
                        $indexOperande = 0;
                        $listVal = [];
                        foreach ($operandesRubrique->rubriques as &$rubrique)
                        {
                            $valeur = (array_key_exists($keyNew,$rubrique->montantsPeriodes)) ? $rubrique->montantsPeriodes[$keyNew] : 0;
                            $listVal['_'.$indexOperande] = round($valeur,2);
                            $indexOperande++;
                        }

                        if(strpos($formuleEval,'[') !== false || strpos($formuleEval,'=') !== false)
                        {
                            $eval = $formuleEval;
                            $indexOperande = 0;
                            foreach ($operandesRubrique->rubriques as &$rubrique)
                            {
                                $eval = str_replace('_'.$indexOperande,'('.$listVal['_'.$indexOperande].')',$eval);
                                $indexOperande++;
                            }
                        }
                        else
                        {
                            try
                            {
                                $eval = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$formuleEval))),$listVal);
                            }
                            catch (SyntaxError $s)
                            {
                                $errorNumber = 1;
                                $errorHead = $error_texte_head.$errorNumber;
                                if(!in_array($errorHead,$erreurTableau)) $erreurTableau[] = $errorHead;
                                $eval = $error_texte.$errorNumber;
                            }
                        }

                        $montantsRubriques[$exercice.'-'.$periode->libelle] = $eval;
                    }
                }
                $operandesRubrique->montantsPeriodes = $montantsRubriques;
            }
            /**
             *  $cells[row-col] = stdClass
             *      ->isFormule;
             *      ->formule;
             *      ->operandesRubriques : [stdClass]
             *          ->variation;
             *          ->formule;
             *          ->rubriques: pccs,solde,typeCompte,montants[exercice-Periode-pcc-tiers] = montant
             *          ->montantsPeriodes[exercice-periode]
             */

            $formule = $cell->formule;
            $formuleEval = '';
            $chars = str_split($formule);
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

            $montantsPeriodes = [];
            foreach ($periodes as $periode)
            {
                $listVal = [];
                $indexOperande = 0;
                foreach ($cell->operandesRubriques as &$operandesRubrique)
                {
                    $exerciceCurrent = $exerciceSel + $operandesRubrique->variation;
                    $listVal['_'.$indexOperande] = round($operandesRubrique->montantsPeriodes[$exerciceCurrent.'-'.$periode->libelle],2);
                    $indexOperande++;
                }

                if(strpos($formuleEval,'[') !== false || strpos($formuleEval,'=') !== false)
                {
                    $eval = $formuleEval;
                    $indexOperande = 0;
                    foreach ($cell->operandesRubriques as &$operandesRubrique)
                    {
                        $eval = str_replace('_'.$indexOperande,'('.$listVal['_'.$indexOperande].')',$eval);
                        $indexOperande++;
                    }
                }
                else
                {
                    try
                    {
                        $eval = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$formuleEval))),$listVal);
                    }
                    catch (SyntaxError $s)
                    {
                        $errorNumber = 2;
                        $errorHead = $error_texte_head.$errorNumber;
                        if(!in_array($errorHead,$erreurTableau)) $erreurTableau[] = $errorHead;
                        $eval = $error_texte.$errorNumber;
                    }
                }

                $montantsPeriodes[$periode->libelle] = $eval;
            }
            $cell->montantsPeriodes = $montantsPeriodes;
        }

        /**
         * Completer le tableau
         */
        for ($i = 0;$i < $rowNumber; $i++)
        {
            for($j = 0; $j < $colNumber ; $j++)
            {
                if(!array_key_exists($i.'-'.$j,$cells))
                {
                    $cells[$i.'-'.$j] = null;
                    $cellsStyles[$i.'-'.$j] = null;
                }
            }
        }
        /**
         * $cells[row-col]
         */

        $entetes = array();
        $models = array();
        $colNum = 0;
        $colsExplodes = [];
        $colsAdds = [];
        for($j = 0; $j < $colNumber; $j++)
        {
            if($j != 0 && $cells['0-'.$j] != null && $cells['0-'.$j]->formule != '')
            {
                foreach ($periodes as $periode)
                {
                    $entetes[] = $cells['0-'.$j]->formule.' - '.$periode->libelle;
                    $model = new stdClass();
                    $model->name = 'col_'.$colNum;
                    $model->width = 10;
                    $model->classes = '';
                    $model->align = 'right';
                    $models[] = $model;
                    $colNum++;
                }
                $colsExplodes[$j] = count($periodes);
            }
            else
            {
                $entetes[] = ($cells['0-'.$j] != null) ? $cells['0-'.$j]->formule : '';
                $model = new stdClass();
                $model->name = 'col_'.$colNum;
                $model->width = 10;
                $model->classes = '';
                $model->align = 'left';
                $models[] = $model;
                $colsExplodes[$j] = 1;
                $colNum++;
            }
        }
        /**
         * $entetes[] = texte
         * $models[] = stdClass : name,width,classes,align
         * $colsExplodes[col] = nbr de colonne + ajout
         */

        $datas = [];
        //construction datas
        for($i = 1; $i < $rowNumber; $i++)
        {
            $temp = array();
            foreach ($cells as $rowCol => &$cell)
            {
                $keySpliter = explode('-',$rowCol);
                $row = intval($keySpliter[0]);
                $col = intval($keySpliter[1]);

                if($row != $i || $cell == null) continue;
                $colDepart = 0;
                for($c = 0;$c < $col; $c++) $colDepart += $colsExplodes[$c];

                if($cell->isFormule)
                {
                    $keyTemp = 0;
                    foreach ($cell->montantsPeriodes as $keyPeriode => $montantPeriode)
                    {
                        $variationCol = $colDepart - $col + $keyTemp;
                        $formuleRubrique = $montantPeriode;
                        if(strpos($formuleRubrique,'[') !== false && strpos($formuleRubrique,']') !== false)
                        {
                            $chars = str_split($formuleRubrique);
                            $debuts = [];
                            $fins = [];
                            foreach ($chars as $keyChar => &$char)
                            {
                                if($char == '[') $debuts[] = $keyChar;
                                elseif($char == ']') $fins[] = $keyChar;
                            }

                            $searchs = [];
                            $replaces = [];
                            foreach ($debuts as $keyDebut => &$debut)
                            {
                                $codeCell = '';
                                for($j = $debut + 1; $j < $fins[$keyDebut]; $j++)
                                {
                                    $codeCell .= $chars[$j];
                                }

                                $searchs[] = $codeCell;
                                $chars_ = str_split($codeCell);
                                $replace = '';
                                foreach ($chars_ as $indexChar => &$char)
                                {
                                    if(strtoupper($char) >= 'A' && strtoupper($char) <= 'Z')
                                    {
                                        $replace .= ord($char) - ord('A');
                                        if(!(strtoupper($chars_[$indexChar + 1]) >= 'A' && strtoupper($chars_[$indexChar + 1]) <= 'Z')
                                            && $indexChar != count($chars_) - 1)
                                        {
                                            $replace = intval($replace) + $variationCol;
                                            $replace .= ';';
                                        }
                                    }
                                    else
                                    {
                                        $replace .= $char;
                                    }
                                }
                                $replaces[] = $replace;
                            }

                            foreach ($replaces as $indexReplace => &$replace)
                            {
                                $formuleRubrique = str_replace($searchs[$indexReplace],$replace,$formuleRubrique);
                            }
                        }
                        $temp[$colDepart + $keyTemp] = $formuleRubrique;
                        $keyTemp++;
                    }
                }
                else
                {
                    if(strpos($cell->formule,'[') !== false && strpos($cell->formule,']') !== false)
                    {
                        $colsAjouter = 0;
                        for($c = 0;$c < $col; $c++) $colsAjouter += $colsExplodes[$c] - 1;

                        $chars = str_split($cell->formule);
                        $index = 0;
                        foreach ($periodes as $periode)
                        {
                            $newFormule = '';
                            foreach ($chars as $indexChar => $char)
                            {
                                if(strtoupper($char) >= 'A' && strtoupper($char) <= 'Z')
                                {
                                    $newFormule .= ord($char) + $colsAjouter + $index - ord('A');
                                    if(!(strtoupper($chars[$indexChar + 1]) >= 'A' && strtoupper($chars[$indexChar + 1]) <= 'Z')
                                        && $indexChar != count($chars) - 1)
                                        $newFormule .= ';';
                                }
                                else
                                {
                                    $newFormule .= $char;
                                }
                            }
                            $temp[$colDepart + $index] = $newFormule;
                            $index++;
                        }
                    }
                    else
                    {
                        $temp[$colDepart] = $cell->formule;
                    }
                }
            }
            $datas[$i - 1] = $temp;
        }
        /**
         * $datas[row] = array[col] = montant
         */

        $cellsCalculers = [];
        //completer column of table
        foreach ($datas as $row => &$data)
        {
            for($j = 0;$j < $colNum; $j++)
            {
                if(!array_key_exists($j,$data)) $data[$j] = '';
                elseif(strpos($data[$j],'[') !== false && strpos($data[$j],']') !== false)
                {
                    $cellsCalculer = new stdClass();
                    $cellsCalculer->formule = $data[$j];
                    $cellsCalculers[$row.'-'.$j] = $cellsCalculer;
                }
            }
            ksort($data);
        }
        /**
         * $datas[row] = array[col] = montant
         */

        //detection boucle infinie
        $result = new stdClass();
        $maxBoucle = 50;
        //calcul des formules

        $errorsControls = [];
        foreach($cellsCalculers as $key => &$cellsCalculer)
        {
            $boucle = 0;
            while(strpos($cellsCalculer->formule,'[') !== false)
            {
                $boucle++;
                if($boucle > $maxBoucle)
                {
                    $errorNumber = 3;
                    $errorHead = $error_texte_head.$errorNumber;
                    if(!in_array($errorHead,$erreurTableau)) $erreurTableau[] = $errorHead;
                    $cellsCalculer->formule = $error_texte.$errorNumber;
                    break;
                }
                $chars = str_split($cellsCalculer->formule);
                $debuts = [];
                $fins = [];
                foreach ($chars as $keyChar => $char)
                {
                    if($char == '[') $debuts[] = $keyChar;
                    elseif($char == ']') $fins[] = $keyChar;
                }

                //test fermeture []
                if(count($debuts) != count($fins))
                {
                    $rowColSpliterErrors = explode('-',$key);
                    $rowError = intval($rowColSpliterErrors[0]) + 2;
                    $colError = intval($rowColSpliterErrors[1]) + 1;

                    $errorNumber = 4;
                    $errorHead = $error_texte_head.$errorNumber;
                    if(!in_array($errorHead,$erreurTableau)) $erreurTableau[] = $errorHead;
                    $cellsCalculer->formule = $error_texte.$errorNumber;
                    break;
                }

                $operandesCells = [];
                foreach ($debuts as $keyDebut => &$debut)
                {
                    $codeCell = '';
                    for($i = $debut + 1; $i < $fins[$keyDebut]; $i++)
                    {
                        $codeCell .= $chars[$i];
                    }
                    $operandesCells[] = $codeCell;
                }

                $newsVals = [];
                foreach ($operandesCells as $operandesCell)
                {
                    $rowCol = explode(';',$operandesCell);
                    $col = intval($rowCol[0]);
                    $row = intval($rowCol[1]) - 2;
                    $newVal = $datas[$row][$col];
                    $newVal = (is_float($newVal)) ? round($newVal,2) : $newVal;
                    $newsVals[] = $newVal;
                }
                $newFormule = $cellsCalculer->formule;
                foreach ($newsVals as $keyNewVal => &$newsVal)
                {
                    $newFormule = str_replace('['.$operandesCells[$keyNewVal].']','('.((trim($newsVal) == '') ? 0 : $newsVal).')',$newFormule);
                }
                $cellsCalculer->formule = $newFormule;
            }

            $listVal = [];
            try
            {
                $f = $cellsCalculer->formule;
                $f = trim(str_replace(' ','',$f));
                if(strpos($f,'=') !== false)
                {
                    $controls = explode('=',$f);
                    $base = round($langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$controls[0])))),2);
                    $error = false;
                    $index = 0;
                    $ecart = 0;
                    foreach ($controls as &$control)
                    {
                        $e = round($langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$control)))),2);
                        $ecart = abs($e - $base);
                        if($ecart > 1)
                        {
                            $error = true;
                            break;
                        }
                        $index++;
                    }
                    if($error)
                    {
                        $rowColSpliterErrors = explode('-',$key);
                        $rowError = intval($rowColSpliterErrors[0]) + 2;
                        $colError = intval($rowColSpliterErrors[1]) + 1;

                        $errorNumber = 5;
                        $errorHead = $error_texte_head.$errorNumber;
                        if(!in_array($errorHead,$erreurTableau)) $erreurTableau[] = $errorHead;
                        $eval = 'KOKO-'.$ecart;
                    }
                    else
                    {
                        $eval = 'OK';
                    }
                }
                else
                {
                    $eval = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$f))),$listVal);
                }
            }
            catch (SyntaxError $s)
            {
                $errorNumber = 6;
                $errorHead = $error_texte_head.$errorNumber;
                if(!in_array($errorHead,$erreurTableau)) $erreurTableau[] = $errorHead;
                $eval = $error_texte.$errorNumber;
            }
            $cellsCalculer->montant = $eval;
        }
        /**
         * $cellsCalculer[row-col] = stdClass(formule,montant)
         */

        foreach ($cellsCalculers as $rowCol => &$cellsCalculer)
        {
            $rowColSpliter = explode('-',$rowCol);
            $row = intval($rowColSpliter[0]);
            $col = intval($rowColSpliter[1]);
            $datas[$row][$col] = $cellsCalculer->montant;
        }
        /**
         * $datas[row] = array[col] = montant
         */
        unset($cells);

        $result->analyse = $analyse;
        $result->titre = ($isEtat) ? '' : $indicateur->getDescription();
        $result->sousTitre = '';
        $result->unite = ($isEtat) ? '' : $indicateur->getUnite();
        $result->arrondirA = ($isEtat) ? 0 : ($indicateur->getIsDecimal() == 1) ? 2 : 0;
        $result->code_graphe = $code_graphe;

        $errsPosPeriodes = array(
            'A',
            'S1','S2',
            'T1','T2','T3','T4',
            'JAN','FEV','MAR','AVR','MAI','JUI','JUL','AOU','SEP','OCT','NOV','DEC'
        );
        $entetesObjects = [];
        $indicateurSel = $etatSel = null;
        if($isEtat) $etatSel = $indicateur;
        else $indicateurSel = $indicateur;
        //remove last error
        foreach ($entetes as $keyEntete => $entete)
        {
            $erExercicePeriode = explode('-',$entete);
            if(count($erExercicePeriode) == 2)
            {
                $erExercice = intval(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$erExercicePeriode[0]))));
                $erPeriode = strtoupper(trim(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','',$erExercicePeriode[1])))));
                if(abs($exerciceSel - $erExercice) < 10 && in_array($erPeriode,$errsPosPeriodes))
                {
                    $this->getEntityManager()->getRepository('AppBundle:EtatError')
                        ->deleteErrors($dossier,$indicateurSel,$etatSel,$exerciceSel,$erExercice,$erPeriode);
                    $entetesObject = new stdClass();
                    $entetesObject->exercice = $erExercice;
                    $entetesObject->periode = $erPeriode;
                    $entetesObjects[$keyEntete] = $entetesObject;
                }
            }
        }
        /**
         * $entetesObjects[col]: stdClass(exercice,periode)
         */
        $etatErrorCells = [];
        $colsErros = [];
        foreach ($datas as $row => &$data)
        {
            foreach ($data as $col => &$montant)
            {
                if(substr($montant,0,strlen($error_texte)) == $error_texte || substr($montant,0,strlen('KOKO-')) == 'KOKO-')
                {
                    $entetesObject = $entetesObjects[$col];
                    $etatError = $this->getEntityManager()->getRepository('AppBundle:EtatError')
                        ->getErrors($dossier,$indicateurSel,$etatSel,$exerciceSel,$entetesObject->exercice,$entetesObject->periode,true);

                    $etatErrorCells[] = $this->getEntityManager()->getRepository('AppBundle:EtatErrorCell')
                        ->newEtatErrorCell($etatError,$row,$col);

                    if(!in_array($col,$colsErros)) $colsErros[] = $col;
                }
            }
        }

        $result->colsErrors = $colsErros;

        $courbes = $this->getEntityManager()->getRepository('AppBundle:TypeGraphe')->getArrayGraphes();
        //tableau
        if($courbes[$code_graphe] == 5)
        {
            $newCellsStyles = [];
            $dStyles = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getDefaultStyles();
            for($i = 0; $i < $rowNumber; $i++)
            {
                foreach ($colsExplodes as $col => $colsExplode)
                {
                    $colDepart = 0;
                    for($j = 0;$j < $col;$j++) $colDepart += $colsExplodes[$j];
                    for($k = 0;$k < $colsExplodes[$col];$k++)
                    {
                        $st = (array_key_exists($i.'-'.$col,$newStyles) && $newStyles[$i.'-'.$col] != null) ?
                            $newStyles[$i.'-'.$col] : $dStyles;
                        $newCellsStyles[$i.'-'.($colDepart + $k)] = $st;
                    }
                }
            }

            $formats = array('','%','€');
            foreach ($datas as $row => &$data)
            {
                foreach ($data as $col => &$montant)
                {
                    if(!is_numeric($montant))
                    {
                        continue;
                    }
                    $montant = (round($montant,2) == 0) ? '..' : number_format($montant,$newCellsStyles[$row.'-'.$col]->dec,',',' ').' '.$formats[$newCellsStyles[$row.'-'.$col]->f];
                }
            }

            $result->models = $models;
            $result->styles = $newCellsStyles;
            $result->entetes = $entetes;
            $result->datas = $datas;
        }
        //courbe
        else
        {
            $tdsToGraphes = $this->getEntityManager()->getRepository('AppBundle:IndicateurFormatCol')->getTdToGraphe($indicateur);
            $indexTdsToGraphes = [];
            foreach ($tdsToGraphes as $tdsToGraphe)
            {
                if(!in_array(-$tdsToGraphe->getCol(),$indexTdsToGraphes)) $indexTdsToGraphes[] = -$tdsToGraphe->getCol();
            }

            $donnees = [];
            $categories = [];

            if($courbes[$code_graphe] < 4)
            {
                $categorieComplete = false;
                $cats = [];
                foreach ($datas as $row => $data)
                {
                    $ds = [];
                    if(!in_array($row + 1,$indexTdsToGraphes)) continue;
                    foreach ($data as $col => $montant)
                    {
                        if($col == 0) continue;
                        if(!$categorieComplete)
                        {
                            $cats[$entetes[$col]] = trim($entetes[$col]);
                        }
                        $ds[$entetes[$col]] = $montant;
                    }
                    ksort($ds);
                    $dss = [];
                    foreach ($ds as $d) $dss[] = $d;
                    $categorieComplete = true;

                    $chart = new stdClass();
                    $chart->data = $dss;
                    $chart->name = $data[0];
                    $donnees[] = $chart;
                }
                ksort($cats);
                foreach ($cats as $cat) $categories[] = $cat;
            }

            $result->datas = $donnees;
            $result->categories = $categories;
        }

        $result->error = $erreurTableau;
        $result->typeU = $user->getAccesUtilisateur()->getType();
        $result->id = Boost::boost($indicateur->getId());
        return $result;
    }

    /**
     * @param IndicateurGroup $indicateurGroup
     * @param bool $withNotTable
     * @return array
     */
    public function getIndicateursInGroup(IndicateurGroup $indicateurGroup,$withNotTable = true)
    {
        $results = $this->createQueryBuilder('i')
            ->leftJoin('i.indicateurPack','ip')
            ->where('ip.indicateurGroup = :indicateurGroup')
            ->setParameter('indicateurGroup',$indicateurGroup)
            ->andWhere('ip.valider = 1')
            ->andWhere('i.valider = 1');
        if (!$withNotTable) $results = $results->andWhere('i.isTable = 1');
        return $results->getQuery()->getResult();
    }
}