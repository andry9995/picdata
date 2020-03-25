<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/06/2016
 * Time: 16:46
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\Lot;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use \DateTime;

class LotRepository extends EntityRepository
{

    /**
     * @param Dossier $dossier
     * @param Utilisateur $utilisateur
     * @param string $message_urgent
     * @param null $codeAnalytique
     * @param null $date_scan
     * @param bool $cumuler
     * @return Lot
     */
    public function getNewLot(Dossier $dossier,Utilisateur $utilisateur,$message_urgent = '',$codeAnalytique = null, $date_scan = null,$cumuler = false)
    {
        if (!$date_scan)
            $date_scan = new \DateTime();

        $dateNow = new \DateTime();

        if ($date_scan->format('Ymd') > $dateNow->format('Ymd'))
            $date_scan = new \DateTime();

        $lotLast = $this->createQueryBuilder('l')
            ->where('l.dateScan = :date_scan')
            ->setParameter('date_scan',$date_scan->format('Y-m-d'))
            ->andWhere('l.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->orderBy('l.id','DESC')
            ->setMaxResults(1);

        if ($cumuler)
        {
            $lotLast = $lotLast
                ->andWhere('l.dateTelechargement = :dateTeleChargement')
                ->setParameter('dateTeleChargement', $date_scan->format('Y-m-d'));
        }

        /** @var Lot $lotLast */
        $lotLast = $lotLast
            ->getQuery()
            ->getOneOrNullResult();

        $lot_num = 1;

        if ($lotLast)
        {
            if ($cumuler) return $lotLast;
            $lot_num = intval($lotLast->getLot()) + 1;
        }
        elseif ($cumuler)
        {
            $lotLast = $this->createQueryBuilder('l')
                ->where('l.dateScan = :date_scan')
                ->setParameter('date_scan',$date_scan->format('Y-m-d'))
                ->andWhere('l.dossier = :dossier')
                ->setParameter('dossier',$dossier)
                ->orderBy('l.id','DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($lotLast) $lot_num = intval($lotLast->getLot()) + 1;
        }

        $lot = new Lot();

        $lot
            ->setDateScan($date_scan)
            ->setDossier($dossier)
            ->setLot($lot_num)
            ->setUtilisateur($utilisateur);

        if ($cumuler) $lot
            ->setDateTelechargement($date_scan);

        if($message_urgent != '')
        {
            $lot->setMessageUrgent($message_urgent);
            $lot->setPriorite(1);
        }
        if($codeAnalytique != null) $lot->setCodeAnalytique($codeAnalytique);

        $em = $this->getEntityManager();
        $em->persist($lot);
        $em->flush();

        return $lot;
    }

    /**
     * @param Lot $lot
     * @param null $image
     * @param int $status
     * @return array|\Doctrine\ORM\QueryBuilder
     */
    public function getImagesInLot(Lot $lot,$image = null,$status = -2)
    {
        $images = [];
        if($image != null) $images[] = $image;
        else
        {
            $images = $this->getEntityManager()->getRepository('AppBundle:Image')
                ->createQueryBuilder('i')
                ->where('i.lot = :lot')
                ->setParameter('lot',$lot);

            if($status != -2) $images = $images->andWhere('i.status = :status')->setParameter('status',$status);
            $images = $images->orderBy('i.id')->getQuery()->getResult();
        }

        return $images;
    }
}