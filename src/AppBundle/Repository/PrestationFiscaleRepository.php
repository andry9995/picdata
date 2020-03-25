<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 13/02/2017
 * Time: 08:37
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class PrestationFiscaleRepository extends EntityRepository
{
    public function getPrestaitonFiscaleByDossier($dossier)
    {
        $prestations = $this->getEntityManager()
            ->getRepository('AppBundle:PrestationFiscale')
            ->createQueryBuilder('pf')
            ->where('pf.dossier= :dossier')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult()
        ;

        if(count($prestations) > 0)
            return $prestations[0];

        return null;
    }

}