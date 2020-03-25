<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 01/02/2018
 * Time: 14:00
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\BanqueType;
use AppBundle\Entity\BanqueTypePcg;
use Doctrine\ORM\EntityRepository;

class BanqueTypeRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getBanqueTypeParametres()
    {
        $banqueTypeTemps = $this->createQueryBuilder('bt')
            ->orderBy('bt.libelle')
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($banqueTypeTemps as $banqueTypeTemp)
        {
            $id = $banqueTypeTemp->getId();
            $results[$id] = (object)
            [
                'id' => Boost::boost($id),
                'l' => $banqueTypeTemp->getLibelle(),
                'c' => $this->getEntityManager()->getRepository('AppBundle:BanqueTypePcg')->getBanqueTypeCounts($banqueTypeTemp)
            ];
        }

        return $results;
    }

    /**
     * @return BanqueType[]
     */
    public function getBanqueTypes()
    {
        return $this->createQueryBuilder('bt')
            ->orderBy('bt.libelle','ASC')
            ->getQuery()
            ->getResult();
    }
}