<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 09/01/2018
 * Time: 13:34
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class NdfCategorieDossierRepository extends EntityRepository
{
    public function getNdfCategorieDossierByDossier($dossier){
        return $this
            ->getEntityManager()
            ->getRepository('AppBundle:NdfCategorieDossier')
            ->createQueryBuilder('cd')
            ->where('cd.dossier = :dossier')
            ->andWhere('cd.status = 1')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();
    }
}