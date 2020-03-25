<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 13/02/2017
 * Time: 09:09
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class PrestationGestionRepository extends EntityRepository
{
    public function getPrestationGestionByDossier($dossier)
    {
        $prestations = $this->getEntityManager()
            ->getRepository('AppBundle:PrestationGestion')
            ->createQueryBuilder('pg')
            ->where('pg.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();

        if(count($prestations) > 0)
            return $prestations[0];

        return null;
    }
}