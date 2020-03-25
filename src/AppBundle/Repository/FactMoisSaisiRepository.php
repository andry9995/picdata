<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 20/01/2017
 * Time: 11:56
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class FactMoisSaisiRepository extends EntityRepository
{
    public function getAllMoisSaisi(Dossier $dossier, $exercice)
    {
        $mois = $this->getEntityManager()
            ->getRepository('AppBundle:FactMoisSaisi')
            ->createQueryBuilder('m')
            ->select('m')
            ->addSelect("STR_TO_DATE(CONCAT('01-',m.mois), '%d-%m-%Y') as mois_saisi")
            ->innerJoin('m.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('dossier = :dossier')
            ->andWhere('m.exercice = :exercice')
            ->setParameters(array(
                'dossier' => $dossier,
                'exercice' => $exercice,
            ))
            ->orderBy('mois_saisi', 'DESC')
            ->getQuery()
            ->getResult();
        return $mois;
    }

    public function getMoisSaisiWithSaisieDossier(Dossier $dossier, $exercice)
    {
        $mois = $this->getEntityManager()
            ->getRepository('AppBundle:FactSaisie')
            ->createQueryBuilder('saisie')
            ->where('saisie.dossier = :dossier')
            ->andWhere('saisie.exercice = :exercice')
            ->innerJoin('saisie.factMoisSaisi', 'factMoisSaisi')
            ->select("DISTINCT(STR_TO_DATE(CONCAT('01-',factMoisSaisi.mois), '%d-%m-%Y')) AS mois_saisi")
            ->setParameters(array(
                'dossier' => $dossier,
                'exercice' => $exercice,
            ))
            ->orderBy('mois_saisi', 'DESC')
            ->getQuery()
            ->getResult();
        return $mois;
    }

    public function getMoisSaisiWithSaisieClient(Client $client, $exercice)
    {
        $mois = $this->getEntityManager()
            ->getRepository('AppBundle:FactSaisie')
            ->createQueryBuilder('saisie')
            ->innerJoin('saisie.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->innerJoin('site.client', 'client')
            ->where('client = :client')
            ->andWhere('saisie.exercice = :exercice')
            ->innerJoin('saisie.factMoisSaisi', 'factMoisSaisi')
            ->select("DISTINCT(STR_TO_DATE(CONCAT('01-',factMoisSaisi.mois), '%d-%m-%Y')) AS mois_saisi")
            ->setParameters(array(
                'client' => $client,
                'exercice' => $exercice,
            ))
            ->orderBy('mois_saisi', 'DESC')
            ->getQuery()
            ->getResult();
        return $mois;
    }
}