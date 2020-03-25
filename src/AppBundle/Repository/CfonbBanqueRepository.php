<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/05/2019
 * Time: 14:42
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Banque;
use AppBundle\Entity\CfonbBanque;
use AppBundle\Entity\CfonbCode;
use Doctrine\ORM\EntityRepository;

class CfonbBanqueRepository extends EntityRepository
{
    /**
     * @param Banque $banque
     * @param bool $withKey
     * @return CfonbCode[]
     */
    public function cfonbActiveInBanque(Banque $banque,$withKey = false)
    {
        /** @var CfonbCode[] $cfonbCodes */
        $cfonbCodes = [];

        /** @var CfonbBanque[] $cfonbBanques */
        $cfonbBanques = $this->createQueryBuilder('cb')
            ->where('cb.banque = :banque')
            ->setParameter('banque',$banque)
            ->getQuery()
            ->getResult();

        foreach ($cfonbBanques as $cfonbBanque)
        {
            if ($withKey) $cfonbCodes[$cfonbBanque->getCfonbCode()->getId()] = $cfonbBanque->getCfonbCode();
            else $cfonbCodes[] = $cfonbBanque->getCfonbCode();
        }
        return $cfonbCodes;
    }

    /**
     * @param Banque $banque
     * @param bool $withKey
     * @return CfonbBanque[]
     */
    public function cfonbBanques(Banque $banque, $withKey = false)
    {
        /** @var CfonbBanque[] $cfonbBanques */
        $cfonbBanques = [];

        /** @var CfonbBanque[] $cfonbBanqueTemps */
        $cfonbBanqueTemps = $this->createQueryBuilder('cb')
            ->where('cb.banque = :banque')
            ->setParameter('banque',$banque)
            ->getQuery()
            ->getResult();

        if ($withKey)
            foreach ($cfonbBanqueTemps as $cfonbBanqueTemp)
                $cfonbBanques[$cfonbBanqueTemp->getCfonbCode()->getId()] = $cfonbBanqueTemp;
        else $cfonbBanques = $cfonbBanqueTemps;

        return $cfonbBanques;
    }
}