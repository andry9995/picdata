<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 13/02/2017
 * Time: 09:11
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class PrestationJuridiqueRepository extends EntityRepository
{
    public function getPrestationJurique($dossier)
    {
        $prestations = $this->getEntityManager()
            ->getRepository('AppBundle:PrestationJuridique')
            ->createQueryBuilder('pj')
            ->where('pj.dossier= :dossier')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();

        if(count($prestations) > 0)
            return $prestations[0];

        return null;
    }

}