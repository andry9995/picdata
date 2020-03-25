<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 29/08/2019
 * Time: 14:29
 */

namespace AppBundle\Repository;

use AppBundle\Entity\CodeAnalytiqueSection;
use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class CodeAnalytiqueSectionRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @return CodeAnalytiqueSection[]
     */
    public function getAllForDossier(Dossier $dossier)
    {
        return $this->createQueryBuilder('cas')
            ->where('cas.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->orderBy('cas.libelle')
            ->getQuery()
            ->getResult();
    }
}