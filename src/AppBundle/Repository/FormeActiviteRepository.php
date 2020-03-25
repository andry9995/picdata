<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 30/01/2018
 * Time: 08:58
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class FormeActiviteRepository extends EntityRepository
{
    public function getFormeActiviteByRegimeFiscal($regimeFiscal)
    {
        if ($regimeFiscal == "CODE_BA") {
            return $this->getEntityManager()
                ->getRepository('AppBundle:FormeActivite')
                ->createQueryBuilder('fa')
                ->orderBy('fa.libelle', 'ASC')
                ->getQuery()
                ->getResult();

        } else {

            if($regimeFiscal == "CODE_BNC"){
                return $this->getEntityManager()
                    ->getRepository('AppBundle:FormeActivite')
                    ->createQueryBuilder('fa')
                    ->where('fa.code = :code')
                    ->setParameter(':code', 'CODE_PROFESSION_LIBERALE')
                    ->orderBy('fa.libelle', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
            else {
                return $this->getEntityManager()
                    ->getRepository('AppBundle:FormeActivite')
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

}