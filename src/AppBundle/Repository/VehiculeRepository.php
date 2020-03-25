<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VehiculeRepository extends EntityRepository
{
    public function getVehiculeByDossierId($dossier)
    {
        return $this->createQueryBuilder('v')
            ->where('v.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->getQuery()->getResult();
    }
}
?>