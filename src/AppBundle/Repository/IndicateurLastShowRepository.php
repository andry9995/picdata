<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 27/01/2017
 * Time: 10:58
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurLastShow;
use AppBundle\Entity\TypeGraphe;
use Doctrine\ORM\EntityRepository;


class IndicateurLastShowRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @param Indicateur $indicateur
     * @return mixed
     */
    public function getLast(Dossier $dossier,Indicateur $indicateur)
    {
        return $this->createQueryBuilder('l')
            ->where('l.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('l.indicateur = :indicateur')
            ->setParameter('indicateur',$indicateur)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Dossier $dossier
     * @param Indicateur $indicateur
     * @return array|null
     */
    public function getArrayLast(Dossier $dossier,Indicateur $indicateur)
    {
        $indicateurLastShow = $this->getLast($dossier,$indicateur);

        $codeGraphe = '';
        $exercices = '';
        $analyse = '';
        $periodes = [];

        if($indicateurLastShow != null)
        {
            $codeGraphe = $indicateurLastShow->getTypeGraphe()->getCode();
            $exercices = explode(';',$indicateurLastShow->getExercices());
            $analyse = intval($indicateurLastShow->getAnalyse());
            if($indicateurLastShow->getPeriode() != null && trim($indicateurLastShow->getPeriode()) != '') $periodes = json_decode($indicateurLastShow->getPeriode());
        }
        return ($indicateurLastShow != null) ? array('codeGraphe'=>$codeGraphe,'exercices'=>$exercices,'analyse'=>$analyse,'periodes'=>$periodes) : null;
    }

    /**
     * @param Dossier $dossier
     * @param Indicateur $indicateur
     * @param TypeGraphe $typeGraphe
     * @param $exercices
     * @param $analyse
     * @param $periodes
     */
    public function setLast(Dossier $dossier,Indicateur $indicateur,TypeGraphe $typeGraphe,$exercices,$analyse,$periodes)
    {
        $em = $this->getEntityManager();
        $oldLast = $this->getLast($dossier,$indicateur);
        $new = false;
        if($oldLast == null)
        {
            $oldLast = new IndicateurLastShow();
            $oldLast->setDossier($dossier);
            $oldLast->setIndicateur($indicateur);
            $new = true;
        }
        $oldLast->setTypeGraphe($typeGraphe);
        $oldLast->setAnalyse($analyse);
        $oldLast->setExercices(implode(';',$exercices));
        $oldLast->setPeriode(json_encode($periodes));

        if($new) $em->persist($oldLast);
        $em->flush();
    }
}