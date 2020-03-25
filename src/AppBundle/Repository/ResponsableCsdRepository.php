<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 18/01/2017
 * Time: 14:29
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\ResponsableCsd;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;

class ResponsableCsdRepository extends EntityRepository
{
    public function getResponsableClientScriptura(Client $client)
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:ResponsableCsd')
            ->findBy(array(
                'client' => $client,
                'typeCsd' => 5
            ));
    }

    public function getResponsableSiteOuClient(Dossier $dossier)
    {
        $responsables = $this->getEntityManager()
            ->getRepository('AppBundle:ResponsableCsd')
            ->findBy(array(
                'site' => $dossier->getSite(),
                'typeCsd' => 1
            ));
        if ($responsables && count($responsables) > 0) {
            return $responsables;
        }
        $responsables = $this->getEntityManager()
            ->getRepository('AppBundle:ResponsableCsd')
            ->findBy(array(
                'client' => $dossier->getSite()->getClient(),
                'typeCsd' => 0
            ));
        if ($responsables && count($responsables) > 0) {
            return $responsables;
        }
        return [];
    }

    /**
     *  Responsable d'un dossier | Responsable du Site | Responsable du Client
     *
     * @param Dossier $dossier
     * @return \AppBundle\Entity\ImageFtp[]|\AppBundle\Entity\ResponsableCsd[]|array
     */
    public function getResponsableParDossier(Dossier $dossier)
    {
        $responsables = $this->getEntityManager()
            ->getRepository('AppBundle:ResponsableCsd')
            ->findBy(array(
                'dossier' => $dossier,
                'typeCsd' => 2
            ));
        if ($responsables && count($responsables) > 0) {
            return $responsables;
        }
        $responsables = $this->getEntityManager()
            ->getRepository('AppBundle:ResponsableCsd')
            ->findBy(array(
                'site' => $dossier->getSite(),
                'typeCsd' => 1
            ));
        if ($responsables && count($responsables) > 0) {
            return $responsables;
        }
        $responsables = $this->getEntityManager()
            ->getRepository('AppBundle:ResponsableCsd')
            ->findBy(array(
                'client' => $dossier->getSite()->getClient(),
                'typeCsd' => 0
            ));
        if ($responsables && count($responsables) > 0) {
            return $responsables;
        }
        return [];
    }

    function getResponsableDossier($typeResponsable, $dossier)
    {
        $qb = $this->getEntityManager()->getRepository('AppBundle:ResponsableCsd')->createQueryBuilder('rd');

        $qb
            ->where('rd.typeResponsable = :typeResponsable')
            ->setParameter('typeResponsable', $typeResponsable)
            ->andWhere('rd.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->orderBy('rd.nom', 'DESC');

        return $qb
            ->getQuery()
            ->getResult();


    }

    function getResponsableSite($site)
    {
        $qb = $this->getEntityManager()->getRepository('AppBundle:ResponsableCsd')->createQueryBuilder('rd');

        $qb
            ->where('rs.site = :site')
            ->setParameter('site', $site)
            ->orderBy('rd.nom', 'DESC');

        return $qb
            ->getQuery()
            ->getResult();


    }

    function getMandataire($dossier)
    {
        $qb = $this->getEntityManager()->getRepository('AppBundle:ResponsableCsd')->createQueryBuilder('rd');

        $qb->where('rd.typeResponsable = :typeResponsable')
            ->setParameter('typeResponsable', 0)
            ->andWhere('rd.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->orderBy('rd.nom', 'DESC');

        return $qb
            ->getQuery()
            ->getResult();


    }

    /**
     * @param Dossier $dossier
     * @param Utilisateur $utilisateur
     */
    function addResponsableDossier(Dossier $dossier, Utilisateur $utilisateur)
    {
        try {
            $em = $this->getEntityManager();
            $test = $this->getEntityManager()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array(
                    'dossier' => $dossier,
                    'email' => $utilisateur->getEmail(),
                ));
            if (count($test) == 0) {
                $titre = $this->getEntityManager()
                    ->getRepository('AppBundle:ResponsableCsdTitre')
                    ->findBy(array(
                        'libelle' => 'Dirigeant'
                    ));
                $responsableCsd = new ResponsableCsd();
                $responsableCsd->setDossier($dossier)
                    ->setEmail($utilisateur->getEmail())
                    ->setNom($utilisateur->getNom())
                    ->setPrenom($utilisateur->getPrenom())
                    ->setTypeResponsable(1)
                    ->setTypeCsd(2);
                if (count($titre) > 0) {
                    $responsableCsd->setResponsableCsdTitre($titre[0]);
                }
                $em->persist($responsableCsd);
                $em->flush();
            }
        } catch (\Exception $ex) {
        }
    }

    /**
     * @param Dossier $dossier
     * @param Utilisateur $utilisateur
     */
    function removeResponsableDossier(Dossier $dossier, Utilisateur $utilisateur)
    {
        try {
            $em = $this->getEntityManager();
            $responsables = $this->getEntityManager()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array(
                    'dossier' => $dossier,
                    'email' => $utilisateur->getEmail(),
                ));
            if (count($responsables) > 0) {
                /** @var ResponsableCsd $responsable */
                foreach ($responsables as $responsable) {
                    $em->remove($responsable);
                }
                $em->flush();
            }
        } catch (\Exception $ex) {
        }
    }

    /** Responsables Client */
    function getResponsableClient(Dossier $dossier){
        return $this->getEntityManager()
                             ->getRepository('AppBundle:ResponsableCsd')
                             ->findBy(array(
                                 'client' => $dossier->getSite()->getClient(),
                                 'typeCsd' => 0
                             ));
    }
}