<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 16/07/2019
 * Time: 16:50
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\ReglePaiementDossier;
use Doctrine\ORM\EntityRepository;

class ReglePaiementDossierRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @param int $type
     * @return ReglePaiementDossier
     */
    public function getForDossier(Dossier $dossier, $type = 0)
    {
        return $this->createQueryBuilder('rpd')
            ->where('rpd.dossier = :dossier')
            ->andWhere('rpd.typeTiers = :type')
            ->setParameters([
                'dossier' => $dossier,
                'type' => $type
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}