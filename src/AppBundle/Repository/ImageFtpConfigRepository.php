<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 05/01/2018
 * Time: 08:51
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class ImageFtpConfigRepository extends EntityRepository
{
    public function isClientMultiple(Client $client)
    {
        try {
            $test = $this->getEntityManager()
                ->getRepository('AppBundle:ImageFtpConfig')
                ->createQueryBuilder('ifc')
                ->select('ifc')
                ->innerJoin('ifc.client', 'client')
                ->where('client = :client')
                ->andWhere('ifc.multi = :multi')
                ->setParameters([
                    'client' => $client,
                    'multi' => TRUE,
                ])
                ->getQuery()
                ->getOneOrNullResult();
            if ($test) {
                return true;
            }
        } catch (NonUniqueResultException $ex) {
            return true;
        }
        return false;
    }
}