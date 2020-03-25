<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 19/12/2017
 * Time: 13:50
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;

class SmtpRepository extends EntityRepository
{
    public function getSmtpByClient(Client $client)
    {
        try {
            $smtp = $this->getEntityManager()
                ->getRepository('AppBundle:Smtp')
                ->createQueryBuilder('smtp')
                ->select('smtp')
                ->innerJoin('smtp.client', 'client')
                ->addSelect('client')
                ->where('client = :client')
                ->setParameters([
                    'client' => $client,
                ])
                ->getQuery()
                ->getOneOrNullResult();
            return $smtp;
        } catch (NonUniqueResultException $ex) {
            $smtp = $this->getEntityManager()
                ->getRepository('AppBundle:Smtp')
                ->createQueryBuilder('smtp')
                ->select('smtp')
                ->innerJoin('smtp.client', 'client')
                ->addSelect('client')
                ->where('client = :client')
                ->setParameters([
                    'client' => $client,
                ])
                ->getQuery()
                ->getResult();
            $em = $this->getEntityManager();
            /** @var \AppBundle\Entity\Smtp $item */
            $i = 0;
            foreach ($smtp as $item) {
                if ($i > 0) {
                    $em->remove($item);
                    try {
                        $em->flush();
                    } catch (OptimisticLockException $ol) {

                    }
                }
                $i++;
            }
            return $this->getSmtpByClient($client);
        }

    }
}