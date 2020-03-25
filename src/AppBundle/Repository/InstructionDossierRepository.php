<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 08/02/2017
 * Time: 13:27
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class InstructionDossierRepository extends EntityRepository
{
    public function getInstructionDossierByClient($client)
    {
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:InstructionDossier')->createQueryBuilder('id');
        
        $qb->where('id.client = :client')
            ->setParameter('client', $client);
        
        return $qb->getQuery()->getOneOrNullResult();
            
    }

}