<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 16/07/2019
 * Time: 16:49
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\ReglePaiementClient;
use Doctrine\ORM\EntityRepository;

class ReglePaiementClientRepository extends EntityRepository
{
    /**
     * @param Client $client
     * @param int $type
     * @return ReglePaiementClient
     */
    public function getForClient(Client $client, $type = 0)
    {
        return $this->createQueryBuilder('rpc')
            ->where('rpc.client = :client')
            ->andWhere('rpc.typeTiers = :type')
            ->setParameters([
                'client' => $client,
                'type' => $type
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}