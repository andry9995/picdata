<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 07/02/2017
 * Time: 11:12
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class MethodeComptableRepository extends EntityRepository
{
    /**
     * @param Dossier|null $dossier
     * @return mixed
     */
    public function getMethodeComptableByDossier(Dossier $dossier = null)
    {
        $methodeComptables =  $this->createQueryBuilder('mc')
            ->where('mc.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if(count($methodeComptables) >= 1)
            return $methodeComptables[0];

        return null;
    }

    /**
     * @param Dossier $dossier
     * @return int
     */
    public function getMethodeDossier(Dossier $dossier)
    {
        $methodeComptable = $this->getMethodeComptableByDossier($dossier);

        $methode = 0;
        if ($methodeComptable && $methodeComptable->getConventionComptable())
            $methode = (intval($methodeComptable->getConventionComptable()->getId()) <= 2) ? 0 : 1;

        return $methode;
    }
}