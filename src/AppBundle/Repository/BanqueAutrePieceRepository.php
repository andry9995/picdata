<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 08/07/2019
 * Time: 16:30
 */

namespace AppBundle\Repository;

use AppBundle\Entity\BanqueAutrePiece;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Souscategorie;
use Doctrine\ORM\EntityRepository;

class BanqueAutrePieceRepository extends EntityRepository
{
    /**
     * @param BanqueCompte $banqueCompte
     * @param Souscategorie $souscategorie
     * @param $exercice
     * @param $mois
     * @return BanqueAutrePiece
     */
    public function getByMonth(BanqueCompte $banqueCompte, Souscategorie $souscategorie, $exercice, $mois)
    {
        return $this->createQueryBuilder('bap')
            ->where('bap.banqueCompte = :banqueCompte')
            ->andWhere('bap.sousCategorie = :sousCategorie')
            ->andWhere('bap.exercice = :exercice')
            ->andWhere('bap.mois = :mois')
            ->setParameters([
                'banqueCompte' => $banqueCompte,
                'sousCategorie' => $souscategorie,
                'exercice' => $exercice,
                'mois' => $mois
            ])
            ->orderBy('bap.id','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}