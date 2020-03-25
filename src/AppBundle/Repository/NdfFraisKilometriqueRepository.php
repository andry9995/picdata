<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 08/01/2018
 * Time: 17:38
 */

namespace AppBundle\Repository;


use AppBundle\Entity\NdfDepenseFraisKm;
use AppBundle\Entity\NdfFraisKilometrique;
use AppBundle\Entity\NdfNote;
use AppBundle\Entity\Vehicule;
use Doctrine\ORM\EntityRepository;

class NdfFraisKilometriqueRepository extends EntityRepository
{
    /**  */
    function getFraisKmByTypeVehiculeAnnee($annee, $typeVehicule){
        return $this
            ->getEntityManager()
            ->getRepository('AppBundle:NdfFraisKilometrique')
            ->createQueryBuilder('fk')
            ->where('fk.annee = :annee')
            ->andWhere('fk.ndfTypeVehicule = :typeVehicule')
            ->setParameter('annee', $annee)
            ->setParameter('typeVehicule', $typeVehicule)
            ->getQuery()
            ->getResult();
    }

    function getFraisKmByVehicule($annee, $typeVehicule, $puissance){

        if($typeVehicule == 3 || $typeVehicule == 9){
            return $this
                ->getEntityManager()
                ->getRepository('AppBundle:NdfFraisKilometrique')
                ->createQueryBuilder('fk')
                ->where('fk.annee = :annee')
                ->andWhere('fk.ndfTypeVehicule = :typeVehicule')
                ->setParameter('annee', $annee)
                ->setParameter('typeVehicule', $typeVehicule)
                ->getQuery()
                ->getResult();
        }

        return $this
            ->getEntityManager()
            ->getRepository('AppBundle:NdfFraisKilometrique')
            ->createQueryBuilder('fk')
            ->where('fk.annee = :annee')
            ->andWhere('fk.ndfTypeVehicule = :typeVehicule')
            ->andWhere('fk.puissanceMin <= :puissance')
            ->andWhere('fk.puissanceMax >= :puissance')
            ->setParameter('annee', $annee)
            ->setParameter('typeVehicule', $typeVehicule)
            ->setParameter('puissance', $puissance)
            ->getQuery()
            ->getResult();
    }

    function calculFraisKm($annee, $typeVehicule, $puissance, $trajet){

        $tarif = 0;

        if($typeVehicule == 3 || $typeVehicule == 9){
            /** @var NdfDepenseFraisKm[] $fks */
            $fks =  $this
                ->getEntityManager()
                ->getRepository('AppBundle:NdfFraisKilometrique')
                ->createQueryBuilder('fk')
                ->where('fk.annee = :annee')
                ->andWhere('fk.ndfTypeVehicule = :typeVehicule')
                ->setParameter('annee', $annee)
                ->setParameter('typeVehicule', $typeVehicule)
                ->getQuery()
                ->getResult();
        }
        else {
            /** @var NdfDepenseFraisKm[] $fks */
            $fks =  $this
                ->getEntityManager()
                ->getRepository('AppBundle:NdfFraisKilometrique')
                ->createQueryBuilder('fk')
                ->where('fk.annee = :annee')
                ->andWhere('fk.ndfTypeVehicule = :typeVehicule')
                ->andWhere('fk.puissanceMin <= :puissance')
                ->andWhere('fk.puissanceMax >= :puissance')
                ->setParameter('annee', $annee)
                ->setParameter('typeVehicule', $typeVehicule)
                ->setParameter('puissance', $puissance)
                ->getQuery()
                ->getResult();
        }

        if (count($fks) > 0) {
            /** @var NdfFraisKilometrique $fk */
            $fk = $fks[0];

            $plus = 0;
            $fois = 0;
            if (!is_null($fk)) {
                if ($trajet > 0 && $trajet <= 5000) {
                    $plus = $fk->getPlus1();
                    $fois = $fk->getFois1();
                } else if ($trajet > 5000 && $trajet < 20000) {
                    $plus = $fk->getPlus2();
                    $fois = $fk->getFois2();
                } else if ($trajet > 20000) {
                    $plus = $fk->getPlus3();
                    $fois = $fk->getFois3();
                }
            }

            if (is_null($plus)) {
                $plus = 0;
            }

            $tarif = round(($trajet * $fois) + $plus, 2);
        }

        return $tarif;
    }




}