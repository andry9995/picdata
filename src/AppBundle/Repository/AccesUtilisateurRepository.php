<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 09/02/2017
 * Time: 14:41
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AccesUtilisateurRepository extends EntityRepository
{
    public function getAllAcces()
    {
        $acces = $this->getEntityManager()
            ->getRepository('AppBundle:AccesUtilisateur')
            ->createQueryBuilder('acces')
            ->select('acces')
            ->where('acces.type > :type')
            ->setParameters(array(
                'type' => 1
            ))
            ->orderBy('acces.type')
            ->addOrderBy('acces.libelle')
            ->getQuery()
            ->getResult();
        return $acces;
    }

    public function getAllAccesForAll()
    {
        $acces = $this->getEntityManager()
            ->getRepository('AppBundle:AccesUtilisateur')
            ->createQueryBuilder('acces')
            ->select('acces')
            ->where('acces.type > :type')
            ->setParameters(array(
                'type' => 2
            ))
            ->orderBy('acces.type')
            ->addOrderBy('acces.libelle')
            ->getQuery()
            ->getResult();
        return $acces;
    }
}