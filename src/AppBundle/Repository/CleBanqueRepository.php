<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 15/02/2018
 * Time: 09:20
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Banque;
use AppBundle\Entity\Cle;
use Doctrine\ORM\EntityRepository;

class CleBanqueRepository extends EntityRepository
{
    /**
     * @param Cle $cle
     * @param Banque $banque
     * @return bool
     */
    public function isInBanque(Cle $cle,Banque $banque)
    {
        $banques = $this->createQueryBuilder('cb')
            ->where('cb.cle = :cle')
            ->setParameter('cle',$cle)
            ->getQuery()
            ->getResult();

        if (count($banques) == 0) return true;
        else
        {
            foreach ($banques as $b)
            {
                if ($banque->getId() == $b->getBanque()->getId()) return true;
            }
            return false;
        }
    }
}