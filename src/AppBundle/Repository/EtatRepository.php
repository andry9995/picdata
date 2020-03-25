<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use stdClass;

class EtatRepository extends EntityRepository
{
    /**
     * @param int $etat
     * @param null $client
     * @param null $dossier
     * @param int $type
     * @param bool $withChild
     * @return array
     */
    public function getEtats($etat = 0,$client = null,$dossier = null,$type = 0,$withChild = true)
    {
        $results = [];
        $etats = $this->createQueryBuilder('e')
            ->where('e.etat = :etat')
            ->setParameter('etat',$etat)
            ->orderBy('e.rang')
            ->getQuery()
            ->getResult();

        foreach ($etats as $etat)
        {
            $etatStd = new stdClass();
            $etatStd->etat = $etat;

            if($withChild)
            {
                $etatsRegimeFiscals = $this->getEntityManager()->getRepository('AppBundle:EtatRegimeFiscal')->getEtatRegimeFiscals($etat,$client,$dossier,$type);
                $etatStd->etatsRegimeFiscals = $etatsRegimeFiscals;
            }
            $etatStd->dStyles = $this->getEntityManager()->getRepository('AppBundle:IndicateurCell')->getDefaultStyles();
            $results[] = $etatStd;
        }
        return $results;
    }

    /**
     * @param int $etat
     * @return array
     */
    public function getEtatsListe($etat = 0)
    {
        return $this->createQueryBuilder('e')
            ->where('e.etat = :etat')
            ->setParameter('etat',$etat)
            ->orderBy('e.rang')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->createQueryBuilder('sp')
            ->where('sp.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}