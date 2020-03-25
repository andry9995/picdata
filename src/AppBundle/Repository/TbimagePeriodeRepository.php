<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 11/04/2017
 * Time: 11:12
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\TbimagePeriode;
use Doctrine\ORM\EntityRepository;

class TbimagePeriodeRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @param int $exercice
     * @return object
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAnneeMoisExercices(Dossier $dossier,$exercice = 2014)
    {
        $mois = $dossier->getCloture();
        $annee = $exercice;
        $mois++;
        if ($annee == 1)
        {
            $mois = 1;
            $annee++;
        }
        $dateCloture = \DateTime::createFromFormat('Y-m-d',$annee.'-'.(($mois < 10) ? '0' : '').$mois.'-01');
        $dateCloture->sub(new \DateInterval('P1D'));

        /** @var \DateTime $datePremiereCloture */
        $datePremiereCloture = null;
        /** @var \DateTime $dateDemarrage */
        $dateDemarrage = null;

        /** @var TbimagePeriode $tbImagePeriode */
        $tbImagePeriode = $this->createQueryBuilder('ti')
            ->where('ti.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!is_null($tbImagePeriode))
        {
            if (!is_null($tbImagePeriode->getPremiereCloture())) $datePremiereCloture = $tbImagePeriode->getPremiereCloture();
            if (!is_null($tbImagePeriode->getDemarrage())) $dateDemarrage = $tbImagePeriode->getDemarrage();
        }

        if (is_null($datePremiereCloture) || is_null($dateDemarrage))
        {
            if (!is_null($dossier->getDebutActivite()) && is_null($dateDemarrage)) $dateDemarrage = $dossier->getDebutActivite();
            if (!is_null($dossier->getDateCloture()) && is_null($datePremiereCloture)) $datePremiereCloture = $dossier->getDateCloture();
        }

        if (is_null($datePremiereCloture)) $datePremiereCloture = clone $dateCloture;
        if (is_null($dateDemarrage)) $dateDemarrage = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateDebut($dossier,$exercice);

        if ($dateCloture < $datePremiereCloture) $dateCloture = $datePremiereCloture;
        else $dateDemarrage = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateDebut($dossier,$exercice);

        $dateDemarrageSave = clone $dateDemarrage;
        $dateClotureSave = clone $dateCloture;
        $endDate = clone $dateCloture;
        $start = $dateDemarrage->modify('first day of this month');
        $end = $dateCloture->modify('first day of this month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($start, $interval, $end);

        $results = [];
        foreach ($period as $dt)
            $results[] = $dt->format('Y-m');

        if (count($results) == 11)
        {
            $end = $endDate->modify('first day of next month');
            $period = new \DatePeriod($start, $interval, $end);

            $results = [];
            foreach ($period as $dt)
                $results[] = $dt->format('Y-m');
        }

        return (object)
        [
            'd' => $dateDemarrageSave,
            'c' => $dateClotureSave,
            'ms' => $results,
            'm_0' => ($dateClotureSave->format('Ymd') < (new \DateTime())->format('Ymd')) ? $dateClotureSave : new \DateTime()
        ];
    }


    /**
     * @param Dossier $dossier
     * @return null
     */
    public function getTbimagePeriodeByDossier(Dossier $dossier){
        $results = $this->createQueryBuilder('ip')
            ->where('ip.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();

        if(count($results) > 0){
            return $results[0];
        }
        return null;
    }
}