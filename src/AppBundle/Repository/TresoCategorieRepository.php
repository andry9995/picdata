<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 23/10/2019
 * Time: 15:03
 */

namespace AppBundle\Repository;


use AppBundle\Entity\TresoCategorie;
use Doctrine\ORM\EntityRepository;

class TresoCategorieRepository extends EntityRepository
{
    /**
     * @param int $type
     * @return TresoCategorie[]
     */
    public function getAll($type = -1)
    {
        $tresoCategories = $this
            ->createQueryBuilder('tc');

        if ($type != -1)
            $tresoCategories = $tresoCategories
                ->where('tc.type = :type')
                ->setParameter('type', $type);

        return $tresoCategories
            ->orderBy('tc.type')
            ->addOrderBy('tc.libelle')
            ->getQuery()
            ->getResult();
    }
}