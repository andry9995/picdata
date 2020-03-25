<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 24/08/2018
 * Time: 15:36
 */

namespace AppBundle\Repository;


use AppBundle\Entity\LettreMission;
use Doctrine\ORM\EntityRepository;

class LettreMissionFichierRepository extends EntityRepository
{

    public function getFichierByLettreMission(LettreMission $lettreMission){
        return  $this->createQueryBuilder('ldm')
           ->where('ldm.lettreMission = :lettreMission')
            ->setParameter('lettreMission', $lettreMission)
            ->getQuery()
            ->getResult();
    }
}