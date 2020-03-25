<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 18/06/2018
 * Time: 15:49
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Dossier;
use AppBundle\Entity\FactPrestationClient;
use Doctrine\ORM\EntityRepository;

class FactCritereEcritureRepository extends EntityRepository
{
    public function getByPrestationClient(FactPrestationClient $prestation)
    {
        $criteres = $this->getEntityManager()
            ->getRepository('AppBundle:FactCritereEcriture')
            ->createQueryBuilder('f')
            ->select('f')
            ->innerJoin('f.factCritere', 'c')
            ->addSelect('c')
            ->innerJoin('f.factPrestationClient', 'fp')
            ->addSelect('fp')
            ->where('fp.id = :prestation')
            ->setParameters(array(
                'prestation' => $prestation->getId()
            ))
            ->orderBy('f.id')
            ->getQuery()
            ->getResult();

        return $criteres;
    }

    public function getImageBeginsWith(Dossier $dossier, $begin, $exercice, \DateTime $date_limit = null)
    {
        $ecritures = $this->getEntityManager()
            ->getRepository('AppBundle:Ecriture')
            ->createQueryBuilder('ecriture')
            ->select('ecriture')
            ->innerJoin('ecriture.dossier', 'dossier')
            ->leftJoin('ecriture.image', 'image')
            ->where(("(image.nom LIKE :begin) OR (ecriture.imageStr LIKE :begin)"))
            ->andWhere("ecriture.exercice = :exercice")
            ->andWhere("dossier.id = :dossier")
            ->setParameters(array(
                'exercice' => $exercice,
                'begin' => "$begin%",
                'dossier' => $dossier->getId()
            ));

        if ($date_limit) {
            $ecritures->andWhere("ecriture.dateEcr <= :date_limit")
                ->setParameter('date_limit', $date_limit->format('Y-m-d'));
        }

        $ecritures = $ecritures->getQuery()->getResult();

        return count($ecritures);

    }

    public function getImageEndsWith(Dossier $dossier, $end, $exercice, \DateTime $date_limit = null)
    {
        $ecritures = $this->getEntityManager()
            ->getRepository('AppBundle:Ecriture')
            ->createQueryBuilder('ecriture')
            ->select('ecriture')
            ->innerJoin('ecriture.dossier', 'dossier')
            ->leftJoin('ecriture.image', 'image')
            ->where(("image.nom LIKE :end OR ecriture.imageStr LIKE :end"))
            ->andWhere("ecriture.exercice = :exercice")
            ->andWhere("dossier.id = :dossier")
            ->setParameters(array(
                'exercice' => $exercice,
                'end' => "%$end",
                'dossier' => $dossier->getId()
            ));

        if ($date_limit) {
            $ecritures->andWhere("ecriture.dateEcr <= :date_limit")
                ->setParameter('date_limit', $date_limit->format('Y-m-d'));
        }

        $ecritures = $ecritures->getQuery()->getResult();

        return count($ecritures);
    }

    public function getImageContains(Dossier $dossier, $str, $exercice, \DateTime $date_limit = null)
    {
        $ecritures = $this->getEntityManager()
            ->getRepository('AppBundle:Ecriture')
            ->createQueryBuilder('ecriture')
            ->select('ecriture')
            ->innerJoin('ecriture.dossier', 'dossier')
            ->leftJoin('ecriture.image', 'image')
            ->where(("image.nom LIKE :str OR ecriture.imageStr LIKE :str"))
            ->andWhere("ecriture.exercice = :exercice")
            ->andWhere("dossier.id = :dossier")
            ->setParameters(array(
                'exercice' => $exercice,
                'str' => "%$str%",
                'dossier' => $dossier->getId()
            ));

        if ($date_limit) {
            $ecritures->andWhere("ecriture.dateEcr <= :date_limit")
                ->setParameter('date_limit', $date_limit->format('Y-m-d'));
        }

        $ecritures = $ecritures->getQuery()->getResult();

        return count($ecritures);
    }
}