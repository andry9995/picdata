<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/11/2016
 * Time: 10:27
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurCell;
use AppBundle\Entity\IndicateurOperande;
use AppBundle\Entity\IndicateurTb;
use AppBundle\Entity\Rubrique;
use Doctrine\ORM\EntityRepository;

class IndicateurOperandeRepository extends EntityRepository
{
    /**
     * @param Indicateur $indicateur
     * @return array
     */
    public function getOperandes(Indicateur $indicateur)
    {
        return $this->createQueryBuilder('o')
            ->where('o.indicateur = :indicateur')
            ->setParameter('indicateur',$indicateur)
            ->orderBy('o.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param IndicateurTb $indicateurTb
     * @return array
     */
    public function getOperandesIndicateurTbs(IndicateurTb $indicateurTb)
    {
        return $this->createQueryBuilder('io')
            ->where('io.indicateurTb = :indicateurTb')
            ->setParameter('indicateurTb',$indicateurTb)
            ->orderBy('io.id','ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Indicateur $indicateur
     * @return array
     */
    public function getRubriques(Indicateur $indicateur)
    {
        $indicateurOperandes = $this->getOperandes($indicateur);
        $results = array();
        foreach ($indicateurOperandes as $indicateurOperande)
        {
            $rubrique = $indicateurOperande->getRubrique();
            if($rubrique->getFormule() == "") $results[] = $rubrique;
            else
            {
                $rubriques = $this->getEntityManager()->getRepository('AppBundle:Rubrique')->getFillesObject($rubrique);
                foreach ($rubriques as $r) $results[] = $r;
            }
        }
        return $results;
    }

    /**
     * @param IndicateurCell $indicateurCell
     * @return array
     */
    public function getRubriquesCells(IndicateurCell $indicateurCell)
    {
        $indicateurOperandes = $this->getOperandesIndicateurCell($indicateurCell);
        $results = array();

        foreach ($indicateurOperandes as $indicateurOperande)
        {
            $rubrique = $indicateurOperande->getRubrique();
            if($rubrique->getFormule() == "") $results[] = $rubrique;
            else
            {
                $rubriques = $this->getEntityManager()->getRepository('AppBundle:Rubrique')->getFillesObject($rubrique);
                foreach ($rubriques as $r) $results[] = $r;
            }
        }
        return $results;
    }

    /**
     * @param IndicateurCell $indicateurCell
     * @return array
     */
    public function getOperandesIndicateurCell(IndicateurCell $indicateurCell)
    {
        $indicateurOperandes = array();
        $indicateurOperandesTemps =  $this->createQueryBuilder('o')
            ->where('o.indicateurCell = :indicateurCell')
            ->setParameter('indicateurCell',$indicateurCell)
            ->getQuery()
            ->getResult();

        $formule = $indicateurCell->getFormule();
        $chars = str_split($formule);
        $indexOperande = 0;
        $indexChar = 0;
        foreach($chars as $key => $char)
        {
            if($char == '#')
            {
                $indicateurOperandes[] = $indicateurOperandesTemps[$indexOperande];
                $indexOperande++;
            }
            elseif($char == '[')
            {
                $rowCol = '';
                for($i = $indexChar + 1; $i < count($chars); $i++)
                {
                    if($chars[$i] != ']') $rowCol .= $chars[$i];
                    else break;
                }

                $indicateurCell = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getCellByRowColString($indicateurCell,$rowCol);
                $indicateurOperandesCells = $this->getOperandesIndicateurCell($indicateurCell);
                foreach ($indicateurOperandesCells as $indicateurOperandesCell)  $indicateurOperandes[] = $indicateurOperandesCell;
            }
            $indexChar++;
        }
        return $indicateurOperandes;
    }

    /**
     * @param IndicateurCell $indicateurCell
     * @return array
     */
    public function getRubriquesCellVariation(IndicateurCell $indicateurCell)
    {
        $indicateurOperandes =  $this->getOperandesIndicateurCell($indicateurCell);
        $results = array();
        foreach ($indicateurOperandes as &$indicateurOperande)
        {
            $rubrique = $indicateurOperande->getRubrique();
            if($rubrique->getFormule() == "") $results[] = $indicateurOperande->getVariationN();
            else
            {
                $rubriques = $this->getEntityManager()->getRepository('AppBundle:Rubrique')->getFillesObject($rubrique);
                foreach ($rubriques as $r) $results[] = $indicateurOperande->getVariationN();
            }
        }
        return $results;
    }

    /**
     * @param IndicateurCell $indicateurCell
     * @return array
     */
    public function getOperandesCell(IndicateurCell $indicateurCell)
    {
        return $this->createQueryBuilder('o')
            ->where('o.indicateurCell = :indicateurCell')
            ->setParameter('indicateurCell',$indicateurCell)
            ->getQuery()
            ->getResult();
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
     * @param Indicateur $indicateur
     */
    public function deleteOldOperande(Indicateur $indicateur)
    {
        $olds = $this->createQueryBuilder('io')
            ->where('io.indicateur = :indicateur')
            ->setParameter('indicateur',$indicateur)
            ->getQuery()
            ->getResult();

        if(count($olds))
        {
            $em = $this->getEntityManager();
            foreach ($olds as $old) $em->remove($old);
            $em->flush();
        }
    }

    /**
     * @param IndicateurCell $indicateurCell
     */
    public function deleteOldOperandeCell(IndicateurCell $indicateurCell)
    {
        $olds = $this->createQueryBuilder('io')
            ->where('io.indicateurCell = :indicateurCell')
            ->setParameter('indicateurCell',$indicateurCell)
            ->getQuery()
            ->getResult();

        if(count($olds))
        {
            $em = $this->getEntityManager();
            foreach ($olds as $old) $em->remove($old);
            $em->flush();
        }
    }

    /**
     * @param IndicateurOperande $indicateurOperande
     * @return array
     */
    public function getPcgs(IndicateurOperande $indicateurOperande)
    {
        $results = array();
        $pcgRubriques = $this->getEntityManager()->getRepository('AppBundle:PcgRubrique')
            ->createQueryBuilder('pr')
            ->where('pr.rubrique = :rubrique')
            ->setParameter('rubrique',$indicateurOperande->getRubrique())
            ->getQuery()
            ->getResult();
        foreach ($pcgRubriques as $pcgRubrique) $results[] = $pcgRubrique->getPcg();
        return $results;
    }

    /**
     * @param IndicateurTb $indicateurTb
     * @return int
     */
    public function deleteOldOperandeTb(IndicateurTb $indicateurTb)
    {
        $this->createQueryBuilder('io')
            ->delete()
            ->where('io.indicateurTb = :indicateurTb')
            ->setParameter('indicateurTb',$indicateurTb)
            ->getQuery()
            ->execute();

        $this->getEntityManager()->flush();
        return 1;
    }

    /**
     * @param IndicateurTb $indicateurTb
     * @param $rubriques
     * @return int
     */
    public function setNewOperandesTb(IndicateurTb $indicateurTb, $rubriques)
    {
        $em = $this->getEntityManager();
        if ($this->deleteOldOperandeTb($indicateurTb) == 1)
        {
            foreach ($rubriques as $rubrique)
            {
                $indicateurOperande = new IndicateurOperande();
                $indicateurOperande->setRubrique($rubrique->r);
                $indicateurOperande->setVariationN($rubrique->v);
                $indicateurOperande->setIndicateurTb($indicateurTb);
                $em->persist($indicateurOperande);
            }
        }

        $em->flush();
        return 1;
    }
}