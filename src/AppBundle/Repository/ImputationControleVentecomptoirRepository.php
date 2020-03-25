<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 11/10/2018
 * Time: 17:06
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class ImputationControleVentecomptoirRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @param int $montant
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getByDossierMontant(Dossier $dossier, $montant = 123456789)
    {
        return $this->createQueryBuilder('icvc')
            ->leftJoin('icvc.image','i')
            ->leftJoin('i.lot','l')
            ->where('l.dossier = :dossier')
            ->andWhere('icvc.totalTtc = :ttc')
            ->setParameters([
                'dossier' => $dossier,
                'ttc' => $montant
            ]);
    }
}