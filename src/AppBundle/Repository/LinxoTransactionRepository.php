<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 26/04/2018
 * Time: 09:57
 */

namespace AppBundle\Repository;

use AppBundle\Entity\LinxoDossier;
use AppBundle\Entity\LinxoTransaction;
use Doctrine\ORM\EntityRepository;

class LinxoTransactionRepository extends EntityRepository
{
    /**
     * @param LinxoDossier $linxoDossier
     * @return mixed
     */
    public function getLast(LinxoDossier $linxoDossier)
    {
        return $this->createQueryBuilder('lt')
            ->where('lt.linxoDossier = :linxoDossier')
            ->setParameter('linxoDossier',$linxoDossier)
            ->setMaxResults(1)
            ->orderBy('lt.id','DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getRecuperationParametres(LinxoDossier $linxoDossier)
    {
        $last = $this->getLast($linxoDossier);
        if (is_null($last)) return null;

        $startDateLast = $last->getDateFin();
        $startDate = clone  $startDateLast;

        $startDate = $startDate->sub(new \DateInterval('P1D'));
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', (new \DateTime())->format('Y-m-d').' 00:00:00');
        $endDate->sub(new \DateInterval('P2D'));

        $startTimeStamp = $startDate->getTimestamp();
        $endTimeStamp = $endDate->getTimestamp();

        //$last = new LinxoTransaction();
        return (object)
        [
            'startDateRecup' => $startDate,
            'startTimeRecup' => $startTimeStamp,
            'endDateRecup' => $endDate,
            'endTimeRecup' => $endTimeStamp,
            'soldeDebut' => $last->getSoldeFin(),
            'startDate' => $startDateLast,
            'lastRecup' => $last->getDateRecuperation()
        ];
    }
}