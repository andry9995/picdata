<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 10/11/2016
 * Time: 14:07
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\EtatRegimeFiscal;
use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurCell;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use stdClass;

class IndicateurCellRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function getDefaultStyles()
    {
        return json_decode('{"font":"\"open sans\", \"Helvetica Neue\", Helvetica, Arial, sans-serif","style":"normal","weight":"400","size":"13px","color":"rgb(103, 106, 108)","bg":"rgba(0, 0, 0, 0)","align":"center","bt":"1px solid rgb(231, 234, 236)","bl":"1px solid rgb(231, 231, 231)","br":"1px solid rgb(231, 231, 231)","bb":"1px solid rgb(231, 231, 231)","dec":0,"f":0}');
    }

    /**
     * @param $indicateur
     * @param $row
     * @param $col
     * @param bool $isEtat
     * @return mixed
     */
    public function getByRowCol($indicateur,$row,$col,$isEtat = false)
    {
        $req = $this->createQueryBuilder('ic');

        if($isEtat)
        {
            $req = $req
                ->where('ic.etatRegimeFiscal = :etatRegimeFiscal')
                ->setParameter('etatRegimeFiscal',$indicateur);
        }
        else
        {
            $req = $req
                ->where('ic.indicateur = :indicateur')
                ->setParameter('indicateur',$indicateur);
        }

        return $req->andWhere('ic.row = :row')
            ->setParameter('row',$row)
            ->andWhere('ic.col = :col')
            ->setParameter('col',$col)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Indicateur $indicateur
     * @return array
     */
    public function getCells(Indicateur $indicateur)
    {
        $indicateurCells = $this->createQueryBuilder('ic')
            ->where('ic.indicateur = :indicateur')
            ->setParameter('indicateur',$indicateur)
            /*->orderBy('ic.row')
            ->addOrderBy('ic.col')*/
            ->getQuery()
            ->getResult();

        foreach ($indicateurCells as &$indicateurCell)
        {
            $operandes = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandesCell($indicateurCell);
            $indicateurCell->setOperandes($operandes)->setBorderBinary()->setStylesObject();
        }

        return $indicateurCells;
    }

    /**
     * @param EtatRegimeFiscal $etatRegimeFiscal
     * @return array
     */
    public function getCellsEtats(EtatRegimeFiscal $etatRegimeFiscal)
    {
        $indicateurCells = $this->createQueryBuilder('ic')
            ->where('ic.etatRegimeFiscal = :etatRegimeFiscal')
            ->setParameter('etatRegimeFiscal',$etatRegimeFiscal)
            /*->orderBy('ic.row')
            ->addOrderBy('ic.col')*/
            ->getQuery()
            ->getResult();

        foreach ($indicateurCells as &$indicateurCell)
        {
            $operandes = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandesCell($indicateurCell);
            $indicateurCell->setOperandes($operandes)->setBorderBinary()->setStylesObject();
        }

        return $indicateurCells;
    }

    /**
     * @param $indicateur
     * @param int $rowDeleted
     * @param int $colDeleted
     * @param bool $isEtat
     * @param int $decale
     */
    public function decaleRowCol($indicateur,$rowDeleted = -1,$colDeleted = -1,$isEtat = false,$decale = 1)
    {
        $indicateurCells = ($isEtat) ?
            $this->createQueryBuilder('c')
                ->where('c.etatRegimeFiscal = :etatRegimeFiscal')
                ->setParameter('etatRegimeFiscal',$indicateur) :
            $this->createQueryBuilder('c')
                ->where('c.indicateur = :indicateur')
                ->setParameter('indicateur',$indicateur);

        if($rowDeleted != -1) $indicateurCells = $indicateurCells->andWhere('c.row >= :row')->setParameter('row',$rowDeleted);
        elseif ($colDeleted != -1) $indicateurCells = $indicateurCells->andWhere('c.col >= :col')->setParameter('col',$colDeleted);

        $em = $this->getEntityManager();

        if($rowDeleted != -1) $indicateurCells = $indicateurCells->orderBy('c.row',($decale == -1) ? 'ASC':'DESC');
        elseif($colDeleted != -1) $indicateurCells = $indicateurCells->orderBy('c.col',($decale == -1) ? 'ASC':'DESC');

        $indicateurCells = $indicateurCells->getQuery()->getResult();
        foreach ($indicateurCells as $indicateurCell)
        {
            if($rowDeleted != -1)
            {
                $row = $indicateurCell->getRow();
                if($rowDeleted == $row)
                {
                    if($decale == -1) $em->remove($indicateurCell);
                    else $indicateurCell->setRow($row + 1);
                    $em->flush();
                }
                else $indicateurCell->setRow($row + $decale);
            }
            elseif($colDeleted != -1)
            {
                $col = $indicateurCell->getCol();
                if($colDeleted == $col)
                {
                    if($decale == -1) $em->remove($indicateurCell);
                    else $indicateurCell->setCol($col + 1);
                    $em->flush();
                }
                else $indicateurCell->setCol($col + $decale);
            }
        }
        $em->flush();
    }

    /**
     * @param $rowCol
     * @return stdClass
     */
    public function getRowColByString($rowCol)
    {
        $colString = '';
        $rowString = '';
        $chars = str_split($rowCol);

        foreach ($chars as $char)
        {
            if (is_numeric($char)) $rowString .= $char;
            else $colString .= $char;
        }

        $result = new stdClass();
        $result->row = $rowString - 2;
        $result->col = ord(strtoupper($colString)) - ord('A');
        return $result;
    }

    /**
     * @param IndicateurCell $indicateurCell
     * @param $rowCol
     * @return mixed
     */
    public function getCellByRowColString(IndicateurCell $indicateurCell, $rowCol)
    {
        $colString = '';
        $rowString = '';
        $chars = str_split($rowCol);

        foreach ($chars as $char)
        {
            if (is_numeric($char)) $rowString .= $char;
            else $colString .= $char;
        }
        return $this->getByRowCol($indicateurCell->getIndicateur(),intval($rowString) - 1,ord(strtolower($colString)) - 97);
    }

    /**
     * @param Dossier $dossier
     * @param IndicateurCell $indicateurCell
     * @param $exercices
     * @param $moiss
     * @return float
     */
    public function getValue(Dossier $dossier,IndicateurCell $indicateurCell,$exercices,$moiss)
    {
        //$indicateurOperandes = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandesCell($indicateurCell);
        $variations = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getRubriquesCellVariation($indicateurCell);
        $rubriques = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getRubriquesCells($indicateurCell);

        $chars = str_split($this->getFormuleEval($indicateurCell));

        $exercice = $exercices[0];
        $indexOperande = 0;
        $montants = array();
        foreach ($rubriques as $rubrique)
        {
            //$pcgs = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getPcgs($indicateurOperande);
            $pcgs = $this->getEntityManager()->getRepository('AppBundle:PcgRubrique')->getPcgs($rubrique);
            $pccs = $this->getEntityManager()->getRepository('AppBundle:Pcc')->getPCCByPCG($pcgs,$dossier);

            $variation = intval($variations[$indexOperande]);
            $exercice_current = $exercice + $variation;

            $ecritures = $this->getEntityManager()->getRepository('AppBundle:Ecriture')->getEcritureOperandes($dossier,$pccs,array(0=>$exercice_current),$moiss,$rubrique);
            $typeSolde = intval($rubrique->getSolde());
            $aNouveaux = array();
            $montantsTiers = array();
            $tiersIds = array();
            $montants[$indexOperande] = 0;
            foreach ($ecritures as $ecriture)
            {
                $debit = $ecriture['db'];
                $credit = $ecriture['cr'];
                $tiersId = $ecriture['ts'];
                $isAn = (intval($ecriture['an']) == 1);

                if(!in_array($tiersId,$tiersIds)) $tiersIds[$tiersId] = $tiersId;
                if(!array_key_exists($tiersId,$montantsTiers)) $montantsTiers[$tiersId] = 0;
                if(!array_key_exists($tiersId,$aNouveaux)) $aNouveaux[$tiersId] = 0;

                /**
                 * calcul montant temps
                 * */
                //solde
                if($typeSolde == 0)
                {
                    if($tiersId != 0 && $isAn) $aNouveaux[$tiersId] += $debit - $credit;
                    else $montantsTiers[$tiersId] += $debit - $credit;
                }
                //solde debit
                elseif ($typeSolde == 1)
                {
                    if($tiersId != 0 && $isAn) $aNouveaux[$tiersId] += $debit - $credit;
                    else
                    {
                        $solde = $debit - $credit;
                        if($solde > 0) $montantsTiers[$tiersId] += $solde;
                    }
                }
                //solde credit
                elseif ($typeSolde == 2)
                {
                    if($tiersId != 0 && $isAn) $aNouveaux[$tiersId] += $debit - $credit;
                    else
                    {
                        $solde = $debit - $credit;
                        if($solde < 0) $montantsTiers[$tiersId] += $solde;
                    }
                }
                //debit
                elseif ($typeSolde == 3) $montantsTiers[$tiersId] += $debit;
                //credit
                elseif ($typeSolde == 4) $montantsTiers[$tiersId] -= $credit;
            }

            //reglage solde tiers
            foreach ($tiersIds as $tiersId)
            {
                //solde
                if($typeSolde == 0) $montantsTiers[$tiersId] += $aNouveaux[$tiersId];
                //solde debit
                elseif ($typeSolde == 1)
                {
                    if($aNouveaux[$tiersId] > 0) $montantsTiers[$tiersId] += $aNouveaux[$tiersId];
                }
                //solde credit
                elseif ($typeSolde == 2)
                {
                    if($aNouveaux[$tiersId] < 0) $montantsTiers[$tiersId] += $aNouveaux[$tiersId];
                }

                $montants[$indexOperande] += $montantsTiers[$tiersId];
            }
            $indexOperande++;
        } //$montants[indxOperande]

        //formule a evaluer
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

        //return count($rubriques);

        //return $formuleEval;

        //calcul formule
        $indexOperande = 0;
        $listVal = [];
        foreach ($rubriques as $rubrique)
        {
            $listVal['_'.$indexOperande] = $montants[$indexOperande];
            $indexOperande++;
        }
        $langage = new ExpressionLanguage();
        $eval = $langage->evaluate($formuleEval,$listVal);

        return round($eval,2);
    }

    /**
     * @param IndicateurCell $indicateurCell
     * @return string
     */
    public function getFormuleEval(IndicateurCell $indicateurCell)
    {
        $oldFormule = $indicateurCell->getFormule();
        //formule a evaluer
        $formule_eval = '';
        $chars = str_split($oldFormule);
        $index_operande = 0;

        $rubriques = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandesCell($indicateurCell);
        $continue = false;
        $indexChar = 0;
        foreach($chars as $char)
        {
            $indexChar++;
            if($continue)
            {
                if($char == ']') $continue = false;
                continue;
            }
            if($char == '#')
            {
                $rubrique = $rubriques[$index_operande]->getRubrique();
                if(trim($rubrique->getFormule()) != "") $formule_eval .= "(".$rubrique->getFormule().")";
                else $formule_eval .= "#";
                $index_operande++;
            }
            elseif($char == '[')
            {
                $rowCol = '';
                for($i = $indexChar; $i < count($chars); $i++)
                {
                    if($chars[$i] != ']') $rowCol .= $chars[$i];
                    else break;
                }

                $indicateurCellTemp = $this->getCellByRowColString($indicateurCell,$rowCol);
                $formule_eval .= "(".$this->getFormuleEval($indicateurCellTemp).")";
                $continue = true;
            }
            else
            {
                $formule_eval .= $char;
            }
        }
        return $formule_eval;
    }

    /**
     * @param IndicateurCell $indicateurCell
     * @param Dossier $dossier
     * @return stdClass
     */
    public function getCellsCompeted(IndicateurCell $indicateurCell,Dossier $dossier)
    {
        $cell = new stdClass();
        $cell->isFormule = ($indicateurCell->getIsFormule() == 1);
        $cell->formule = $indicateurCell->getFormule();

        $operandesRubriques = array();
        if($cell->isFormule)
        {
            $operandes = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandesCell($indicateurCell);
            foreach ($operandes as $operande)
            {
                $operandesRubriques[] = $this->getEntityManager()->getRepository('AppBundle:Rubrique')->getRubriquesInOperande($operande,$dossier,false);
            }
        }
        $cell->operandesRubriques = $operandesRubriques;

        return $cell;
    }

    /**
     * @param IndicateurCell $indicateurCell
     * @param Dossier $dossier
     * @return stdClass
     */
    public function getCellsCompletedV2(IndicateurCell $indicateurCell,Dossier $dossier)
    {
        $cell = new stdClass();
        $cell->isFormule = ($indicateurCell->getIsFormule() == 1);
        $cell->formule = $indicateurCell->getFormule();

        $operandesRubriques = array();
        if($cell->isFormule)
        {
            $operandes = $this->getEntityManager()->getRepository('AppBundle:IndicateurOperande')->getOperandesCell($indicateurCell);
            foreach ($operandes as $operande)
            {
                $operandesRubriques[] = $this->getEntityManager()->getRepository('AppBundle:Rubrique')->getRubriquesInOperandesV2($operande,$dossier);
            }
        }
        $cell->operandesRubriques = $operandesRubriques;

        return $cell;
    }

    /**
     * @param $indicateur
     * @param $row
     * @param $col
     * @param bool $isEtat
     * @return mixed
     */
    public function getCell($indicateur,$row,$col,$isEtat = false)
    {
        $result = $this->createQueryBuilder('c')
            ->where('c.col = :col')
            ->setParameter('col',$col)
            ->andWhere('c.row = :row')
            ->setParameter('row',$row);

        if($isEtat) $result = $result->andWhere('c.etatRegimeFiscal = :indicateur');
        else $result = $result->andWhere('c.indicateur = :indicateur');

        return $result->setParameter('indicateur',$indicateur)->getQuery()->getOneOrNullResult();
    }
}