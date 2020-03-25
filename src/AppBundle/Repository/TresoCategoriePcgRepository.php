<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 23/10/2019
 * Time: 15:03
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Pcg;
use AppBundle\Entity\TresoCategorie;
use AppBundle\Entity\TresoCategoriePcg;
use Doctrine\ORM\EntityRepository;

class TresoCategoriePcgRepository extends EntityRepository
{
    /**
     * @param TresoCategorie $tresoCategorie
     * @return TresoCategoriePcg[]
     */
    public function getForTresoCategories(TresoCategorie $tresoCategorie)
    {
        return $this->createQueryBuilder('tcp')
            ->leftJoin('tcp.pcg','p')
            ->where('tcp.tresoCategorie = :tresoCategorie')
            ->setParameter('tresoCategorie', $tresoCategorie)
            ->orderBy('p.compte')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param TresoCategorie $tresoCategorie
     * @param Pcg $pcg
     * @return TresoCategoriePcg
     */
    public function getTresoCategoriePcg(TresoCategorie $tresoCategorie,Pcg $pcg)
    {
        return $this->createQueryBuilder('tcp')
            ->where('tcp.tresoCategorie = :tresoCategorie')
            ->andWhere('tcp.pcg = :pcg')
            ->setParameters([
                'tresoCategorie' => $tresoCategorie,
                'pcg' => $pcg
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}