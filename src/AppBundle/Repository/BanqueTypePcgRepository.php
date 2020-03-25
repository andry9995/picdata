<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 16/02/2018
 * Time: 08:47
 */

namespace AppBundle\Repository;


use AppBundle\Entity\BanqueType;
use AppBundle\Entity\BanqueTypePcg;
use Doctrine\ORM\EntityRepository;

class BanqueTypePcgRepository extends EntityRepository
{
    /**
     * @param BanqueType $banqueType
     * @return array
     */
    public function getBanqueTypeCounts(BanqueType $banqueType)
    {
        $resultat = 0;
        $tva = 0;
        $bilan = 0;
        $banqueTypeTemps = $this->createQueryBuilder('btp')
            ->where('btp.banqueType = :banqueType')
            ->setParameter('banqueType',$banqueType)
            ->getQuery()
            ->getResult();

        foreach ($banqueTypeTemps as $banqueTypeTemp)
        {
            //$banqueTypeTemp = new BanqueTypePcg();
            //0: resultat; 1:tva; 2 : bilan
            if ($banqueTypeTemp->getType() == 0) $resultat++;
            elseif ($banqueTypeTemp->getType() == 1) $tva++;
            elseif ($banqueTypeTemp->getType() == 2) $bilan++;
        }

        return
        [
            $resultat,
            $tva,
            $bilan
        ];
    }

    /**
     * @param BanqueType $banqueType
     * @param int $type
     * @return BanqueTypePcg[]
     */
    public function getForBanqueType(BanqueType $banqueType, $type = 10)
    {
        $banqueTypePcgs = $this->createQueryBuilder('btp')
            ->leftJoin('btp.pcg','pcg')
            ->where('btp.banqueType = :banqueType')
            ->setParameter('banqueType',$banqueType)
            ->orderBy('btp.type')
            ->addOrderBy('pcg.compte');

        if ($type != 10)
            $banqueTypePcgs
                ->andWhere('btp.type = :type')
                ->setParameter('type',$type);

        return $banqueTypePcgs->getQuery()->getResult();
    }
}