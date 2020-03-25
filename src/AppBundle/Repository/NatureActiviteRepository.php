<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 30/01/2018
 * Time: 09:39
 */

namespace AppBundle\Repository;


use AppBundle\Entity\NatureActivite;
use Doctrine\ORM\EntityRepository;

class NatureActiviteRepository extends EntityRepository
{
    public function getNatureActiviteByRegimeFiscal($regimeFiscal)
    {
        if ($regimeFiscal == "CODE_BA") {
            return $this->getEntityManager()
                ->getRepository('AppBundle:NatureActivite')
                ->createQueryBuilder('fa')
                ->orderBy('fa.libelle', 'ASC')
                ->getQuery()
                ->getResult();

        } else {


            return $this->getEntityManager()
                ->getRepository('AppBundle:NatureActivite')
                ->createQueryBuilder('fa')
                ->where('fa.code != :code')
                ->orWhere('fa.code is NULL')
                ->setParameter(':code', 'CODE_AGRICOLE')
                ->orderBy('fa.libelle', 'ASC')
                ->getQuery()
                ->getResult();

        }
    }
}