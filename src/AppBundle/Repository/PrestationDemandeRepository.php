<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 07/02/2017
 * Time: 14:20
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class PrestationDemandeRepository extends EntityRepository
{
    public function getPrestationDemandeByDossier($dossier)
    {
        $prestations = $this->getEntityManager()
            ->getRepository('AppBundle:PrestationDemande')
            ->createQueryBuilder('pd')
            ->where('pd.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();

        if(count($prestations) > 0){
            return $prestations[0];
        }

        return null;
    }

}