<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurDossier;
use AppBundle\Entity\UtilisateurSite;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class SiteRepository extends EntityRepository
{
    /**
     * @param $site
     * @param Utilisateur $user
     * @param $role
     * @return array
     */
    public function getDossiers($site, Utilisateur $user, $role)
    {
        $query = $this->getEntityManager();
        if ($site == null) {
            if ($role->isGranted('ROLE_ADMIN'))
                $query = $query
                    ->createQuery(
                        "SELECT d FROM AppBundle:Dossier d
                              WHERE d.nom <> '' AND d.status = 1
                            ORDER BY d.nom");
            elseif ($role->isGranted('ROLE_CLIENT'))
                $query = $query
                    ->createQuery(
                        "SELECT d,s FROM AppBundle:Dossier d
                            JOIN d.site s
                            WHERE s.client = :client AND d.nom <> '' AND s.status = 1
                            ORDER BY d.nom"
                    )->setParameter('client', $user->getClient());
        } else {
            $query = $query->getRepository('AppBundle:Dossier')
                ->createQueryBuilder("d")
                ->where("d.site = :site")
                ->andWhere("d.nom <> ''")
                ->andWhere("d.status = 1")
                ->setParameter('site', $site)
                ->orderBy('d.nom', 'ASC')
                ->getQuery();
        }
        return $query->getResult();
    }

    /**
     * @param $client
     * @param $security
     * @param $user
     * @return array
     */
    public function getSites($client, AuthorizationChecker $security, Utilisateur $user)
    {
        $sites = null;

        if ($client == null) {
            $query = $this->createQueryBuilder('s')->where('s.status = 1');
            if ($security->isGranted('ROLE_CLIENT_SCAN')) {
                if (!$security->isGranted('ROLE_SCRIPTURA_ADMIN')) {
                    $client = $user->getClient();
                    $query = $query->andWhere('s.client = :client')->setParameter('client', $client);
                }

                $sites = $query->orderBy('s.nom', 'ASC')->getQuery()->getResult();
            } elseif ($security->isGranted('ROLE_SITE_SCAN')) {
                $query = $this->getEntityManager()->getRepository('AppBundle:UtilisateurSite')
                    ->createQueryBuilder('us')
                    ->where('us.utilisateur = :utilisateur')
                    ->setParameter('utilisateur', $user)
                    ->orderBy('us.nom', 'ASC')
                    ->getQuery();

                foreach ($query->getResult() as $user_site) {
                    $site = $user_site->getSite();
                    if ($site->getStatus() == 1)
                        $sites[] = $user_site->getSite();
                }
            }
        } else {
            $sites = $this->createQueryBuilder('s')
                ->where('s.client = :client')
                ->andWhere('s.status = 1')
                ->setParameter('client', $client)
                ->orderBy('s.nom', 'ASC')
                ->getQuery()->getResult();
        }

        return $sites;
    }

    public function getAllSitesByClient(Client $client)
    {
        $sites = $this->getEntityManager()
            ->getRepository('AppBundle:Site')
            ->createQueryBuilder('site')
            ->select('site')
            ->innerJoin('site.client', 'client')
            ->addSelect('client')
            ->where('client = :client')
            ->andWhere('site.status = :status')
            ->setParameters(array(
                'client' => $client,
                'status' => 1
            ))
            ->orderBy('site.nom', 'ASC')
            ->getQuery()
            ->getResult();
        return $sites;
    }


    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getById($id)
    {
        return $this->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get Liste site par utilisateur
     *
     * @param Utilisateur $user
     * @param $client
     * @return array
     */
    public function getUserSites(Utilisateur $user, $client)
    {
        $user_type = $user->getAccesUtilisateur()
            ->getType();

        $sites = [];
        if ($user_type == 2 || $user_type == 3 || $user_type == 7) {
            //Utilisateur scriptura ou client
            $sites = $this->getEntityManager()
                ->getRepository('AppBundle:Site')
                ->createQueryBuilder('site')
                ->select('site')
                ->innerJoin('site.client', 'client')
                ->addSelect('client')
                ->where('site.status = :status')
                ->andWhere('client = :client')
                ->setParameters(array(
                    'status' => 1,
                    'client' => $client,
                ))
                ->orderBy('site.nom', 'ASC')
                ->getQuery()
                ->getResult();
        } elseif ($user_type == 4) {
            //Utilisateur site
            $id_site = [0];
            $user_sites = $this->getEntityManager()
                ->getRepository('AppBundle:UtilisateurSite')
                ->createQueryBuilder('utilisateurSite')
                ->select('utilisateurSite')
                ->innerJoin('utilisateurSite.utilisateur', 'utilisateur')
                ->addSelect('utilisateur')
                ->innerJoin('utilisateurSite.site', 'site')
                ->addSelect('site')
                ->where('utilisateur = :utilisateur')
                ->setParameters(array(
                    'utilisateur' => $user
                ))
                ->getQuery()
                ->getResult();
            if (count($user_sites) > 0) {
                /** @var UtilisateurSite $user_site */
                foreach ($user_sites as $user_site) {
                    $id_site[] = $user_site->getSite()->getId();
                }
            }

            $qb = $this->getEntityManager()
                ->getRepository('AppBundle:Site')
                ->createQueryBuilder('site');
            $sites = $qb
                ->select('site')
                ->where('site.status = :status')
                ->andWhere($qb->expr()->in('site.id', ':id_site'))
                ->setParameters(array(
                    'status' => 1,
                    'id_site' => $id_site,
                ))
                ->orderBy('site.nom')
                ->getQuery()
                ->getResult();
        } elseif ($user_type == 5 || $user_type == 6) {
            //Utilisateur dossier ou client final
            $id_site = [0];
            $dossiers = [];

            $user_dossiers = $this->getEntityManager()
                ->getRepository('AppBundle:UtilisateurDossier')
                ->createQueryBuilder('utilisateurDossier')
                ->select('utilisateurDossier')
                ->innerJoin('utilisateurDossier.utilisateur', 'utilisateur')
                ->addSelect('utilisateur')
                ->innerJoin('utilisateurDossier.dossier', 'dossier')
                ->addSelect('dossier')
                ->where('utilisateur = :utilisateur')
                ->setParameters(array(
                    'utilisateur' => $user,
                ))
                ->getQuery()
                ->getResult();
            if (count($user_dossiers) > 0) {
                /** @var UtilisateurDossier $user_dossier */
                foreach ($user_dossiers as $user_dossier) {
                    $dossiers[] = $user_dossier->getDossier();
                }
            }

            /** @var Dossier $dossier */
            foreach ($dossiers as $dossier) {
                if (!in_array($dossier->getSite()->getId(), $id_site)) {
                    $id_site[] = $dossier->getSite()->getId();
                }
            }

            $qb = $this->getEntityManager()
                ->getRepository('AppBundle:Site')
                ->createQueryBuilder('site');
            $sites = $qb
                ->select('site')
                ->where('site.status = :status')
                ->andWhere($qb->expr()->in('site.id', ':id_site'))
                ->setParameters(array(
                    'status' => 1,
                    'id_site' => $id_site,
                ))
                ->orderBy('site.nom')
                ->getQuery()
                ->getResult();
        } else {
            $sites = [];
        }

        return $sites;
    }
}