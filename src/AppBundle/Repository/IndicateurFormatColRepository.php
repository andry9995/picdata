<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/12/2016
 * Time: 11:30
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurFormatCol;
use Doctrine\ORM\EntityRepository;

class IndicateurFormatColRepository extends EntityRepository
{
    /**
     * @param $indicateur
     * @param bool $isEtat
     * @return array
     */
    public function getColFormats($indicateur,$isEtat = false)
    {
        $result = $this->createQueryBuilder('ifr');

        if($isEtat) $result = $result->where('ifr.etatRegimeFiscal = :etatRegimeFiscal')->setParameter('etatRegimeFiscal',$indicateur);
        else $result = $result->where('ifr.indicateur = :indicateur')->setParameter('indicateur',$indicateur);

        return $result->andWhere('ifr.col > 0')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $indicateur
     * @param $format
     * @param $decimal
     * @param $col
     * @param bool $isEtat
     * @return int
     */
    public function changeFormat($indicateur,$format,$decimal,$col,$isEtat = false)
    {
        $indicateurFormatCol = $this->createQueryBuilder('ifc');

        if($isEtat)
        {
            $indicateurFormatCol = $indicateurFormatCol
                ->where('ifc.etatRegimeFiscal = :etatRegimeFiscal')
                ->setParameter('etatRegimeFiscal',$indicateur);
        }
        else
        {
            $indicateurFormatCol = $indicateurFormatCol
                ->where('ifc.indicateur = :indicateur')
                ->setParameter('indicateur',$indicateur);
        }

        $indicateurFormatCol = $indicateurFormatCol
            ->andWhere('ifc.col = :col')
            ->setParameter('col',$col)
            ->getQuery()
            ->getOneOrNullResult();

        $em = $this->getEntityManager();

        //non existant (create)
        if($indicateurFormatCol == null && ($format != 0 || $decimal != 0))
        {
            $indicateurFormatCol = new IndicateurFormatCol();

            if($isEtat) $indicateurFormatCol->setEtatRegimeFiscal($indicateur);
            else $indicateurFormatCol->setIndicateur($indicateur);

            $indicateurFormatCol->setAvecDecimal($decimal);
            $indicateurFormatCol->setCol($col);
            $indicateurFormatCol->setFormat($format);
            $em->persist($indicateurFormatCol);
        }
        //upate
        elseif ($indicateurFormatCol != null && ($format != 0 || $decimal != 0))
        {
            $indicateurFormatCol->setAvecDecimal($decimal);
            $indicateurFormatCol->setFormat($format);
        }
        //delete
        elseif($indicateurFormatCol != null && $format == 0 && $decimal == 0) $em->remove($indicateurFormatCol);
        $em->flush();
        return 1;
    }

    /**
     * @param $indicateur
     * @param $col
     * @param bool $isEtat
     * @return mixed
     */
    public function getColFormatByCol($indicateur,$col,$isEtat = false)
    {
        $req = $this->createQueryBuilder('if');

        if($isEtat)
        {
            $req = $req->where('if.etatRegimeFiscal = :etatRegimeFiscal')
                ->setParameter('etatRegimeFiscal',$indicateur);
        }
        else
        {
            $req = $req->where('if.indicateur = :indicateur')
                ->setParameter('indicateur',$indicateur);
        }

        return $req->andWhere('if.col = :col')
            ->setParameter('col',$col)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Indicateur $indicateur
     * @param $row
     * @param $val
     * @return int
     */
    public function changeTdToGraphe(Indicateur $indicateur,$row,$val)
    {
        $em = $this->getEntityManager();
        //suppression
        if($val == 0)
        {
            $indicateurFormatCol = $this->createQueryBuilder('ifr')
                ->where('ifr.indicateur = :indicateur')
                ->setParameter('indicateur',$indicateur)
                ->andWhere('ifr.col = :row')
                ->setParameter('row',-$row)
                ->getQuery()
                ->getOneOrNullResult();

            $em->remove($indicateurFormatCol);
        }
        //ajout
        else
        {
            $indicateurFormatCol = new IndicateurFormatCol();
            $indicateurFormatCol->setCol(-$row);
            $indicateurFormatCol->setIndicateur($indicateur);
            $em->persist($indicateurFormatCol);
        }
        $em->flush();
        return 1;
    }

    /**
     * @param Indicateur $indicateur
     * @return array
     */
    public function getTdToGraphe(Indicateur $indicateur)
    {
        return $this->createQueryBuilder('ifr')
            ->where('ifr.indicateur = :indicateur')
            ->setParameter('indicateur',$indicateur)
            ->andWhere('ifr.col < 0')
            //->orderBy('ifr.col','DESC')
            ->getQuery()
            ->getResult();
    }
}