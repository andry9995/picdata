<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 13/12/2016
 * Time: 09:13
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\FactPrestation;
use AppBundle\Entity\FactPrestationClient;
use AppBundle\Entity\FactPrestationDossier;
use Doctrine\ORM\EntityRepository;

class FactPrestationRepository extends EntityRepository
{
    public function getAllPrestation()
    {
        $prestations = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestation')
            ->createQueryBuilder('fp')
            ->select('fp')
            ->leftJoin('fp.factDomaine', 'factDomaine')
            ->addSelect('factDomaine')
            ->leftJoin('fp.factUnite', 'factUnite')
            ->addSelect('factUnite')
            ->orderBy('factDomaine.code')
            ->addOrderBy('fp.code')
            ->addOrderBy('fp.libelle')
            ->getQuery()
            ->getResult();
        return $prestations;
    }

    public function getPrestationClientManquant(Client $client)
    {
        $prest_clients = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestationClient')
            ->createQueryBuilder('p')
            ->select('p')
            ->where('p.client = :client')
            ->setParameter('client', $client)
            ->getQuery()
            ->getResult();
        $exists = array(0);
        /* @var FactPrestationClient $prest_client */
        foreach ($prest_clients as $prest_client) {
            $exists[] = $prest_client->getFactPrestation()->getId();
        }

        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestation')
            ->createQueryBuilder('p');
        $prestations = $qb->select('p')
            ->add('where', $qb->expr()->notIn('p.id', ':exists'))
            ->setParameter('exists', $exists)
            ->orderBy('p.code')
            ->getQuery()
            ->getResult();

        return $prestations;

    }

    public function getPrestationDossierManquant(Dossier $dossier)
    {
        $prest_dossiers = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestationDossier')
            ->createQueryBuilder('p')
            ->select('p')
            ->where('p.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();
        $exists = array(0);
        /* @var FactPrestationDossier $prest_dossier */
        foreach ($prest_dossiers as $prest_dossier) {
            $exists[] = $prest_dossier->getFactPrestation()->getId();
        }

        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestationClient')
            ->createQueryBuilder('p');
        $prestations = $qb->select('p')
            ->innerJoin('p.factPrestation', 'factPrestation')
            ->add('where', $qb->expr()->notIn('factPrestation.id', ':exists'))
            ->andWhere('p.client = :client')
            ->setParameter('client', $dossier->getSite()->getClient())
            ->setParameter('exists', $exists)
            ->orderBy('factPrestation.code')
            ->getQuery()
            ->getResult();

        return $prestations;

    }


    public function completePrestationClient(Client $client)
    {
        $em = $this->getEntityManager();
        $manquants = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestation')
            ->getPrestationClientManquant($client);
        /* @var FactPrestation $manquant */
        foreach ($manquants as $manquant) {
            $prest_client = new FactPrestationClient();
            $prest_client
                ->setClient($client)
                ->setFactPrestation($manquant)
                ->setRemise($manquant->getRemise())
                ->setIndice($manquant->getIndice());
            $em->persist($prest_client);
        }
        $em->flush();
        return true;
    }

    public function completePrestationDossier(Dossier $dossier)
    {
        $em = $this->getEntityManager();

        $manquants = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestation')
            ->getPrestationDossierManquant($dossier);
        /* @var FactPrestationClient $manquant */
        foreach ($manquants as $manquant) {
            $prest_dossier = new FactPrestationDossier();
            $prest_dossier
                ->setDossier($dossier)
                ->setFactPrestation($manquant->getFactPrestation())
                ->setRemise($manquant->getRemise())
                ->setIndice($manquant->getIndice());
            $em->persist($prest_dossier);
        }
        $em->flush();

        return true;
    }
}