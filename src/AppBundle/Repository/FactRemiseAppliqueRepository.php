<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 27/01/2017
 * Time: 11:00
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use Doctrine\ORM\EntityRepository;

class FactRemiseAppliqueRepository extends EntityRepository
{
    public function getRemiseAppliqueByClient(Client $client)
    {
        $remise = $this->getEntityManager()
            ->getRepository('AppBundle:FactRemiseApplique')
            ->createQueryBuilder('factRemiseApplique')
            ->select('factRemiseApplique')
            ->innerJoin('factRemiseApplique.client', 'client')
            ->addSelect('client')
            ->where('client = :client')
            ->setParameters(array(
                'client' => $client
            ))
            ->getQuery()
            ->getOneOrNullResult();

        return $remise;
    }
}