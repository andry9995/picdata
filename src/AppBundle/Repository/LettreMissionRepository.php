<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 24/08/2018
 * Time: 15:22
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Client;
use AppBundle\Entity\LettreMission;
use Doctrine\ORM\EntityRepository;

class LettreMissionRepository extends EntityRepository
{
    /**
     * @param Client $client
     * @return array
     */
    public function getLettreMissionByClient(Client $client){
        return  $this->createQueryBuilder('ldm')
            ->innerJoin('ldm.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->innerJoin('site.client', 'client')
            ->where('client = :client')
            ->setParameter('client', $client)
            ->getQuery()
            ->getResult();
    }




}