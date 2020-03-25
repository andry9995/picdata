<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 13/12/2016
 * Time: 09:29
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use Doctrine\ORM\EntityRepository;

class FactPrestationClientRepository extends EntityRepository
{
    public function getAllPrestationByClient(Client $client)
    {
        $prestations = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestationClient')
            ->createQueryBuilder('fp')
            ->select('fp')
            ->innerJoin('fp.client', 'client')
            ->andWhere('fp.client = :the_client')
            ->addSelect('client')
            ->innerJoin('fp.factPrestation', 'factPrestation')
            ->addSelect('factPrestation')
            ->leftJoin('factPrestation.factDomaine', 'factDomaine')
            ->addSelect('factDomaine')
            ->leftJoin('factPrestation.factUnite', 'factUnite')
            ->addSelect('factUnite')
            ->setParameter('the_client', $client)
            ->orderBy('factDomaine.code')
            ->addOrderBy('factPrestation.code')
            ->addOrderBy('factPrestation.libelle')
            ->getQuery()
            ->getResult();
        return $prestations;
    }
}