<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 14/11/2016
 * Time: 08:28
 */

namespace AppBundle\Repository;

use AppBundle\Entity\FactDomaine;
use Doctrine\ORM\EntityRepository;

class FactDomaineRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    function getListe()
    {
        $liste = $this->createQueryBuilder('p')->getQuery()->getResult();
        foreach ($liste as &$domaine)
        {
            $prestationGens = $this->getEntityManager()->getRepository('AppBundle:FactPrestationGenerale')->prestationGens($domaine);
            $domaine->setPrestationGens($prestationGens);
        }

        return $liste;
    }

    function getAllDomaine() {
        $domaines = $this->getEntityManager()
            ->getRepository('AppBundle:FactDomaine')
            ->createQueryBuilder('fd')
            ->select('fd')
            ->orderBy('fd.code')
            ->getQuery()
            ->getResult();
        return $domaines;
    }


}