<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 18/10/2017
 * Time: 16:27
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use Doctrine\ORM\EntityRepository;

class CreationCompteEmailRepository extends EntityRepository
{
    public function getEmailByClient(Client $client)
    {
        $emails = $this->getEntityManager()
            ->getRepository('AppBundle:CreationCompteEmail')
            ->createQueryBuilder('email')
            ->select('email')
            ->where('email.client = :client')
            ->setParameters(array(
                'client' => $client,
            ))
            ->orderBy('email.email')
            ->getQuery()
            ->getResult();

        return $emails;
    }
}