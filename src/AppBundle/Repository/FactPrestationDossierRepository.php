<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 13/12/2016
 * Time: 09:36
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class FactPrestationDossierRepository extends EntityRepository
{
    public function getAllPrestationByDossier(Dossier $dossier)
    {
        $prestations = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestationDossier')
            ->createQueryBuilder('fp')
            ->select('fp')
            ->innerJoin('fp.dossier', 'dossier')
            ->where('fp.dossier = :the_dossier')
            ->addSelect('dossier')
            ->innerJoin('fp.factPrestation', 'factPrestation')
            ->addSelect('factPrestation')
            ->leftJoin('factPrestation.factDomaine', 'factDomaine')
            ->addSelect('factDomaine')
            ->leftJoin('factPrestation.factUnite', 'factUnite')
            ->addSelect('factUnite')
            ->setParameter('the_dossier', $dossier)
            ->orderBy('factDomaine.code')
            ->addOrderBy('factPrestation.code')
            ->addOrderBy('factPrestation.libelle')
            ->getQuery()
            ->getResult();
        return $prestations;
    }
}