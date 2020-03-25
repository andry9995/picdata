<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 16/01/2017
 * Time: 15:47
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use Doctrine\ORM\EntityRepository;

class FactContratRepository extends EntityRepository
{
    public function getAllContrat()
    {
        $contrats = $this->getEntityManager()
            ->getRepository('AppBundle:FactContrat')
            ->createQueryBuilder('fc')
            ->select('fc')
            ->innerJoin('fc.client', 'client')
            ->addSelect('client')
            ->orderBy('client.nom')
            ->getQuery()
            ->getResult();
        return $contrats;
    }

    /**
     * Tarif modifiable ou non
     *
     * @param Client $client
     * @return bool
     */
    public function isEditEnabled(Client $client)
    {
        $test = $this->getEntityManager()
            ->getRepository('AppBundle:FactContrat')
            ->createQueryBuilder('contrat')
            ->select('contrat')
            ->innerJoin('contrat.client', 'client')
            ->addSelect('client')
            ->where('client = :client')
            ->andWhere('contrat.autoriserModif = :autoriser')
            ->setParameters(array(
                'client' => $client,
                'autoriser' => true,
            ))
            ->getQuery()
            ->getResult();

        return (count($test) > 0);
    }

}