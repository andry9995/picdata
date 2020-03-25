<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 10/12/2018
 * Time: 09:44
 */

namespace AppBundle\Repository;


use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\RelevePiece;
use Doctrine\ORM\EntityRepository;

class RelevePieceRepository extends EntityRepository
{
    /**
     * @param BanqueCompte $banqueCompte
     * @param $exercice
     * @param $mois
     * @return RelevePiece
     */
    public function getByMonth(BanqueCompte $banqueCompte,$exercice,$mois)
    {
        return $this->createQueryBuilder('rp')
            ->where('rp.banqueCompte = :banqueCompte')
            ->andWhere('rp.exercice = :exercice')
            ->andWhere('rp.mois = :mois')
            ->setParameters([
                'banqueCompte' => $banqueCompte,
                'exercice' => $exercice,
                'mois' => $mois
            ])
            ->orderBy('rp.id','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}