<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 24/01/2017
 * Time: 10:33
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use Doctrine\ORM\EntityRepository;

class FactClientAssocieRepository extends EntityRepository
{
    public function getClientAssocie(Client $client)
    {
        $clients = $this->getEntityManager()
            ->getRepository('AppBundle:FactClientAssocie')
            ->createQueryBuilder('client')
            ->select('client')
            ->innerJoin('client.clientPrincipal', 'clientPrincipal')
            ->addSelect('clientPrincipal')
            ->where('clientPrincipal = :clientPrincipal')
            ->innerJoin('client.clientAutre', 'clientAutre')
            ->addSelect('clientAutre')
            ->setParameters(array(
                'clientPrincipal' => $client
            ))
            ->orderBy('clientAutre.nom')
            ->getQuery()
            ->getResult();
        return $clients;
    }
}