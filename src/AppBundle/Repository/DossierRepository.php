<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Site;
use AppBundle\Entity\UtilisateurDossier;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Utilisateur;
use \DateTime;
use \DateInterval;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use AppBundle\Functions\CustomPdoConnection;
use AppBundle\Controller\Boost;


class DossierRepository extends EntityRepository
{

    public function findD($dossier)
    {
        $dossier= Boost::deboost($dossier,$this);

        return $this->find($dossier);
    }


    /**
     * @param Utilisateur $user
     * @param $security
     * @param $site
     * @param $client
     * @return array
     */
    public function getDossiers(Utilisateur $user, AuthorizationChecker $security, $site, $client)
    {
        $site = $this->getEntityManager()->getRepository('AppBundle:Site')
            ->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id',$site)
            ->getQuery()
            ->getOneOrNullResult();
        $dossiers = array();

        if($site == null)
        {
            if($security->isGranted('ROLE_SITE_SCAN'))
            {
                $query = $this->createQueryBuilder('d');

                if($security->isGranted('ROLE_CLIENT_SCAN'))
                {
                    $client_select = null;

                    if(!$security->isGranted('ROLE_SCRIPTURA_ADMIN'))
                    {
                        $client_select = $user->getClient();
                    }
                    else
                    {
                        $client_select = $this->getEntityManager()->getRepository('AppBundle:Client')
                            ->createQueryBuilder('c')
                            ->where('c.id = :id')
                            ->setParameter('id',$client)
                            ->getQuery()
                            ->getOneOrNullResult();
                    }
                    if($client_select != null)
                        $query = $query->innerJoin('d.site','s')
                            ->where('s.client = :client')
                            ->setParameter('client',$client_select);
                    $dossiers = $query->andWhere("d.nom <> ''")->andWhere('d.status = 1')->orderBy('d.nom','ASC')->getQuery()->getResult();
                }
                elseif($security->isGranted('ROLE_SITE'))
                {
                    $dossiers = array();
                    $site_user = $this->getEntityManager()->getRepository('AppBundle:UtilisateurSite')
                        ->createQueryBuilder('us')
                        ->where('us.utilisateur = :utilisateur')
                        ->setParameter('utilisateur',$user)
                        ->getQuery()
                        ->getResult();
                    foreach($site_user as $site)
                    {
                        $dossiers_temp = $this->getEntityManager()->getRepository('AppBundle:Site')
                            ->getDossiers($site,$user,$security);
                        foreach($dossiers_temp as $dossier)
                        {
                            $dossiers[] = $dossier;
                        }
                    }
                }
            }
        }
        else
        {
            $dossiers = $this->getEntityManager()->getRepository('AppBundle:Site')
                ->getDossiers($site,$user,$security);
        }

        return $dossiers;
    }

    /**
     * @param Utilisateur $user
     * @param $role
     * @param $site
     * @return array
     */
    public function getUserDossierOld(Utilisateur $user, $role, $site)
    {
        $liste_site = array();
        $dossiers = array();

        if ($site == 0) {
            if(in_array('ROLE_ADMIN', $role)){
                $dossiers = $this->getEntityManager()->getRepository('AppBundle:Dossier')
                    ->createQueryBuilder("d")
                    ->where("d.nom <> ''")
                    ->andWhere("d.status = 1")
                    ->orderBy('d.nom', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
            elseif (in_array('ROLE_CLIENT', $role)) {
                $user_client = $user->getClient();

                $sites = $this->getEntityManager()->getRepository('AppBundle:Site')
                    ->findBy(array('client' => $user_client));

                foreach ($sites as $site) {
                    $liste_site[] = $site->getId();
                }

                $dossiers = $this->getEntityManager()->getRepository('AppBundle:Dossier')
                    ->createQueryBuilder("d")
                    ->where("d.site IN(:liste_site)")
                    ->andWhere("d.nom <> ''")
                    ->andWhere("d.status = 1")
                    ->setParameter('liste_site', array_values($liste_site))
                    ->orderBy('d.nom', 'ASC')
                    ->getQuery()
                    ->getResult();
            } elseif (in_array('ROLE_SITE', $role)) {
                $users_sites = $this->getEntityManager()->getRepository('AppBundle:UtilisateurSite')
                    ->findBy(array('utilisateur' => $user));


                foreach ($users_sites as $user_site) {
                    $liste_site[] = $user_site->getSite()->getId();
                }

                $dossiers = $this->getEntityManager()->getRepository('AppBundle:Dossier')
                    ->createQueryBuilder("d")
                    ->where("d.site IN(:liste_site)")
                    ->andWhere("d.nom <> ''")
                    ->andWhere("d.status = 1")
                    ->setParameter('liste_site', array_values($liste_site))
                    ->orderBy('d.nom', 'ASC')
                    ->getQuery()
                    ->getResult();
            } elseif (in_array('ROLE_DOSSIER', $role)) {
                $users_dossiers = $this->getEntityManager()->getRepository('AppBundle:UtilisateurDossier')
                    ->findBy(array('utilisateur' => $user));

                $liste_dossier = array();

                foreach ($users_dossiers as $user_dossier) {
                    $liste_dossier[] = $user_dossier->getDossier()->getId();
                }

                $dossiers = $this->getEntityManager()->getRepository('AppBundle:Dossier')
                    ->createQueryBuilder("d")
                    ->where("d.id IN(:liste_dossier)")
                    ->andWhere("d.nom <> ''")
                    ->andWhere("d.status = 1")
                    ->setParameter('liste_dossier', array_values($liste_dossier))
                    ->orderBy('d.nom', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
        } else {
            $site = $this->getEntityManager()->getRepository('AppBundle:Site')
                ->createQueryBuilder('s')
                ->where('s.id = :id')
                ->setParameter('id',$site)
                ->getQuery()->getOneOrNullResult();

            $dossiers = $this->getEntityManager()->getRepository('AppBundle:Dossier')
                ->createQueryBuilder("d")
                ->where("d.site = :site")
                ->andWhere("d.nom <> ''")
                ->andWhere("d.status = 1")
                ->setParameter('site', $site)
                ->orderBy('d.nom', 'ASC')
                ->getQuery()
                ->getResult();
        }

        return $dossiers;
    }

    /**
     * @param Utilisateur $user
     * @param Client|null $client
     * @param  Site|null $site
     * @param null $exercice
     * @return array
     */
    public function getUserDossierOldInfoperdos(Utilisateur $user, $client, $site = null, $exercice = null)
    {
        $user_type = $user->getAccesUtilisateur()->getType();
        $dossiers = [];

        if ($user->getShowDossierDemo()) {
            $dossiers_demo = $this->getEntityManager()
                ->getRepository('AppBundle:Dossier')
                ->createQueryBuilder('dossier')
                ->select('dossier')
                ->where('dossier.showInDemo = :show')
                ->orderBy('dossier.nom')
                ->setParameters(array(
                    'show' => TRUE
                ))
                ->getQuery()
                ->getResult();
        }

        if ($user_type <= 4) {
            //Si utilisateur site ou supérieur
            if ($site) {
                if ($exercice) {
                    $dossiers = $this->getEntityManager()
                        ->getRepository('AppBundle:Dossier')
                        ->createQueryBuilder('dossier')
                        ->select('dossier')
                        ->innerJoin('dossier.site', 'site')
                        ->addSelect('site')
                        ->where('site = :site')
                        ->andWhere('(dossier.status = 1 OR (dossier.status != 1 AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice))')
                        ->setParameters(array(
                            'site' => $site,
                            'exercice' => $exercice,
                        ))
                        ->orderBy('dossier.nom')
                        ->getQuery()
                        ->getResult();

                    if (isset($dossiers_demo) && count($dossiers_demo)) {
                        foreach ($dossiers_demo as $item) {
                            $dossiers[]  = $item;
                        }
                    }
                } else {
                    $dossiers = $this->getEntityManager()
                        ->getRepository('AppBundle:Dossier')
                        ->createQueryBuilder('dossier')
                        ->select('dossier')
                        ->innerJoin('dossier.site', 'site')
                        ->addSelect('site')
                        ->where('site = :site')
                        ->andWhere('dossier.status = :status')
                        ->setParameters(array(
                            'site' => $site,
                            'status' => 1,
                        ))
                        ->orderBy('dossier.nom')
                        ->getQuery()
                        ->getResult();

                    if (isset($dossiers_demo) && count($dossiers_demo)) {
                        foreach ($dossiers_demo as $item) {
                            $dossiers[]  = $item;
                        }
                    }
                }
            } else {
                if ($user_type <= 3) {
                    $dossiers = $this->getEntityManager()
                        ->getRepository('AppBundle:Dossier')
                        ->getDossiersClient($client, $exercice);
                    if (isset($dossiers_demo) && count($dossiers_demo)) {
                        foreach ($dossiers_demo as $item) {
                            $dossiers[] = $item;
                        }
                    }
                } else {
                    $sites = $this->getEntityManager()
                        ->getRepository('AppBundle:Site')
                        ->getUserSites($user, $client);
                    $site_id = [0];

                    /** @var Site $site */
                    foreach ($sites as $site) {
                        $site_id[] = $site->getId();
                    }

                    $qb = $this->getEntityManager()
                        ->getRepository('AppBundle:Dossier')
                        ->createQueryBuilder('dossier');
                    if ($exercice) {
                        $dossiers = $qb
                            ->select('dossier')
                            ->innerJoin('dossier.site', 'site')
                            ->addSelect('site')
                            ->where($qb->expr()->in('site.id', ':site_id'))
                            ->andWhere('(dossier.status = 1 OR (dossier.status != 1  AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice))')
                            ->setParameters(array(
                                'site_id' => $site_id,
                                'exercice' => $exercice,
                            ))
                            ->orderBy('dossier.nom')
                            ->getQuery()
                            ->getResult();

                        if (isset($dossiers_demo) && count($dossiers_demo)) {
                            foreach ($dossiers_demo as $item) {
                                $dossiers[] = $item;
                            }
                        }
                    } else {
                        if ($exercice) {

                        } else {
                            $dossiers = $qb
                                ->select('dossier')
                                ->innerJoin('dossier.site', 'site')
                                ->addSelect('site')
                                ->where($qb->expr()->in('site.id', ':site_id'))
                                ->andWhere('(dossier.status = 1 OR (dossier.status != 1  AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice))')
                                ->setParameters(array(
                                    'site_id' => $site_id,
                                    'exercice' => $exercice,
                                ))
                                ->orderBy('dossier.nom')
                                ->getQuery()
                                ->getResult();

                            if (isset($dossiers_demo) && count($dossiers_demo)) {
                                foreach ($dossiers_demo as $item) {
                                    $dossiers[] = $item;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $user_dossiers = $this->getEntityManager()
                ->getRepository('AppBundle:UtilisateurDossier')
                ->createQueryBuilder('utilisateurDossier')
                ->select('utilisateurDossier')
                ->innerJoin('utilisateurDossier.utilisateur', 'utilisateur')
                ->addSelect('utilisateur')
                ->innerJoin('utilisateurDossier.dossier', 'dossier')
                ->addSelect('dossier')
                ->where('utilisateur = :utilisateur')
                ->andWhere('(dossier.status = 1 OR (dossier.status != 1  AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice))')
                ->setParameters(array(
                    'utilisateur' => $user,
                    'exercice' => $exercice,
                ))
                ->orderBy('dossier.nom')
                ->getQuery()
                ->getResult();

            /** @var UtilisateurDossier $user_dossier */
            foreach ($user_dossiers as $user_dossier) {
                $dossiers[] = $user_dossier->getDossier();
            }

            if (isset($dossiers_demo) && count($dossiers_demo)) {
                foreach ($dossiers_demo as $item) {
                    $dossiers[] = $item;
                }
            }
        }

        return $dossiers;
    }


    /**
     * @param Utilisateur $user
     * @param Client|null $client
     * @param  Site|null $site
     * @param null $exercice
     * @param bool $infoperdos
     *
     * @return array
     */
    public function getUserDossier(Utilisateur $user, $client, $site = null, $exercice = null, $infoperdos = false)
    {
        $user_type = $user->getAccesUtilisateur()->getType();
        $dossiers = [];

        if ($user->getShowDossierDemo()) {

            var_dump("expression");die();

            $dossiers_demo = $this->getEntityManager()
                ->getRepository('AppBundle:Dossier')
                ->createQueryBuilder('dossier')
                ->select('dossier')
                ->where('dossier.showInDemo = :show')
                ->orderBy('dossier.nom')
                ->setParameters(array(
                    'show' => TRUE
                ))
                ->getQuery()
                ->getResult();
        }

        if ($user_type <= 4 || $user_type == 7) {
            //Si utilisateur site ou supérieur
            if ($site) {
                if ($exercice) {

                    //Raha infoperdos dia afficher-na na 'En création' ary
                    if($infoperdos) {
                        $dossiers = $this->getEntityManager()
                            ->getRepository('AppBundle:Dossier')
                            ->createQueryBuilder('dossier')
                            ->select('dossier')
                            ->innerJoin('dossier.site', 'site')
                            ->addSelect('site')
                            ->where('site = :site')
                            ->andWhere('(dossier.status = 1 OR (dossier.status != 1 AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice))')
                            ->setParameters(array(
                                'site' => $site,
                                'exercice' => $exercice,
                            ))
                            ->orderBy('dossier.nom')
                            ->getQuery()
                            ->getResult();
                    }
                    //Raha any @page hafa dia izay activé ihany no afficher-na
                    else {
                        $dossiers = $this->getEntityManager()
                            ->getRepository('AppBundle:Dossier')
                            ->createQueryBuilder('dossier')
                            ->select('dossier')
                            ->innerJoin('dossier.site', 'site')
                            ->addSelect('site')
                            ->where('site = :site')
                            ->andWhere('( (dossier.status = 1 and dossier.active = 1) OR (dossier.status != 1 AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice AND dossier.active = 1))')
                            ->setParameters(array(
                                'site' => $site,
                                'exercice' => $exercice,
                            ))
                            ->orderBy('dossier.nom')
                            ->getQuery()
                            ->getResult();
                    }

                    if (isset($dossiers_demo) && count($dossiers_demo)) {
                        foreach ($dossiers_demo as $item) {
                            $dossiers[]  = $item;
                        }
                    }
                } else {
                    //Raha infoperdos dia afficher-na na 'En création' ary
                    if($infoperdos){
                        $dossiers = $this->getEntityManager()
                            ->getRepository('AppBundle:Dossier')
                            ->createQueryBuilder('dossier')
                            ->select('dossier')
                            ->innerJoin('dossier.site', 'site')
                            ->addSelect('site')
                            ->where('site = :site')
                            ->andWhere('dossier.status = :status')
                            ->setParameters(array(
                                'site' => $site,
                                'status' => 1,
                            ))
                            ->orderBy('dossier.nom')
                            ->getQuery()
                            ->getResult();

                        if (isset($dossiers_demo) && count($dossiers_demo)) {
                            foreach ($dossiers_demo as $item) {
                                $dossiers[] = $item;
                            }
                        }
                    }

                    //Raha any @page hafa dia izay activé ihany no afficher-na
                    else {
                        $dossiers = $this->getEntityManager()
                            ->getRepository('AppBundle:Dossier')
                            ->createQueryBuilder('dossier')
                            ->select('dossier')
                            ->innerJoin('dossier.site', 'site')
                            ->addSelect('site')
                            ->where('site = :site')
                            ->andWhere('dossier.status = :status')
                            ->andWhere('dossier.active = :active')
                            ->setParameters(array(
                                'site' => $site,
                                'status' => 1,
                                'active' => 1
                            ))
                            ->orderBy('dossier.nom')
                            ->getQuery()
                            ->getResult();

                        if (isset($dossiers_demo) && count($dossiers_demo)) {
                            foreach ($dossiers_demo as $item) {
                                $dossiers[] = $item;
                            }
                        }
                    }
                }
            } else {
                if ($user_type <= 3 || $user_type == 7) {
                    $dossiers = $this->getEntityManager()
                        ->getRepository('AppBundle:Dossier')
                        ->getDossiersClient($client, $exercice, $infoperdos);
                    if (isset($dossiers_demo) && count($dossiers_demo)) {
                        foreach ($dossiers_demo as $item) {
                            $dossiers[] = $item;
                        }
                    }
                } else {
                    $sites = $this->getEntityManager()
                        ->getRepository('AppBundle:Site')
                        ->getUserSites($user, $client);
                    $site_id = [0];

                    /** @var Site $site */
                    foreach ($sites as $site) {
                        $site_id[] = $site->getId();
                    }

                    $qb = $this->getEntityManager()
                        ->getRepository('AppBundle:Dossier')
                        ->createQueryBuilder('dossier');
                    if ($exercice) {
                        //Raha infoperdos dia afficher-na na 'En création' ary
                        if($infoperdos) {
                            $dossiers = $qb
                                ->select('dossier')
                                ->innerJoin('dossier.site', 'site')
                                ->addSelect('site')
                                ->where($qb->expr()->in('site.id', ':site_id'))
                                ->andWhere('(dossier.status = 1 OR (dossier.status != 1  AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice))')
                                ->setParameters(array(
                                    'site_id' => $site_id,
                                    'exercice' => $exercice,
                                ))
                                ->orderBy('dossier.nom')
                                ->getQuery()
                                ->getResult();

                            if (isset($dossiers_demo) && count($dossiers_demo)) {
                                foreach ($dossiers_demo as $item) {
                                    $dossiers[] = $item;
                                }
                            }
                        }

                        //Raha any @page hafa dia izay activé ihany no afficher-na
                        else{

                            $dossiers = $qb
                                ->select('dossier')
                                ->innerJoin('dossier.site', 'site')
                                ->addSelect('site')
                                ->where($qb->expr()->in('site.id', ':site_id'))
                                ->andWhere('((dossier.status = 1 and dossier.active = 1) OR (dossier.status != 1  AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice AND dossier.active = 1))')
                                ->setParameters(array(
                                    'site_id' => $site_id,
                                    'exercice' => $exercice,
                                ))
                                ->orderBy('dossier.nom')
                                ->getQuery()
                                ->getResult();

                            if (isset($dossiers_demo) && count($dossiers_demo)) {
                                foreach ($dossiers_demo as $item) {
                                    $dossiers[] = $item;
                                }
                            }

                        }
                    } else {
                        if ($exercice) {

                        } else {
                            //Raha infoperdos dia afficher-na na 'En création' ary
                            if($infoperdos) {
                                $dossiers = $qb
                                    ->select('dossier')
                                    ->innerJoin('dossier.site', 'site')
                                    ->addSelect('site')
                                    ->where($qb->expr()->in('site.id', ':site_id'))
                                    ->andWhere('(dossier.status = 1 OR (dossier.status != 1  AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice))')
                                    ->setParameters(array(
                                        'site_id' => $site_id,
                                        'exercice' => $exercice,
                                    ))
                                    ->orderBy('dossier.nom')
                                    ->getQuery()
                                    ->getResult();

                                if (isset($dossiers_demo) && count($dossiers_demo)) {
                                    foreach ($dossiers_demo as $item) {
                                        $dossiers[] = $item;
                                    }
                                }
                            }
                            //Raha any @page hafa dia izay activé ihany no afficher-na
                            else{

                                $dossiers = $qb
                                    ->select('dossier')
                                    ->innerJoin('dossier.site', 'site')
                                    ->addSelect('site')
                                    ->where($qb->expr()->in('site.id', ':site_id'))
                                    ->andWhere('((dossier.status = 1 AND dossier.active = 1) OR (dossier.status != 1  AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice AND dossier.active = 1 ))')
                                    ->setParameters(array(
                                        'site_id' => $site_id,
                                        'exercice' => $exercice,
                                    ))
                                    ->orderBy('dossier.nom')
                                    ->getQuery()
                                    ->getResult();

                                if (isset($dossiers_demo) && count($dossiers_demo)) {
                                    foreach ($dossiers_demo as $item) {
                                        $dossiers[] = $item;
                                    }
                                }

                            }
                        }
                    }
                }
            }
        } else {
            //Raha infoperdos dia afficher-na na 'En création' ary
            if($infoperdos) {
                $user_dossiers = $this->getEntityManager()
                    ->getRepository('AppBundle:UtilisateurDossier')
                    ->createQueryBuilder('utilisateurDossier')
                    ->select('utilisateurDossier')
                    ->innerJoin('utilisateurDossier.utilisateur', 'utilisateur')
                    ->addSelect('utilisateur')
                    ->innerJoin('utilisateurDossier.dossier', 'dossier')
                    ->addSelect('dossier')
                    ->where('utilisateur = :utilisateur')
                    ->andWhere('(dossier.status = 1 OR (dossier.status != 1  AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice))')
                    ->setParameters(array(
                        'utilisateur' => $user,
                        'exercice' => $exercice,
                    ))
                    ->orderBy('dossier.nom')
                    ->getQuery()
                    ->getResult();
            }

            //Raha any @page hafa dia izay activé ihany no afficher-na
            else{

                $user_dossiers = $this->getEntityManager()
                    ->getRepository('AppBundle:UtilisateurDossier')
                    ->createQueryBuilder('utilisateurDossier')
                    ->select('utilisateurDossier')
                    ->innerJoin('utilisateurDossier.utilisateur', 'utilisateur')
                    ->addSelect('utilisateur')
                    ->innerJoin('utilisateurDossier.dossier', 'dossier')
                    ->addSelect('dossier')
                    ->where('utilisateur = :utilisateur')
                    ->andWhere('((dossier.status = 1 AND dossier.active = 1) OR (dossier.status != 1  AND dossier.statusDebut IS NOT NULL AND dossier.statusDebut > :exercice AND dossier.active = 1))')
                    ->setParameters(array(
                        'utilisateur' => $user,
                        'exercice' => $exercice,
                    ))
                    ->orderBy('dossier.nom')
                    ->getQuery()
                    ->getResult();

            }
            /** @var UtilisateurDossier $user_dossier */
            foreach ($user_dossiers as $user_dossier) {
                $dossiers[] = $user_dossier->getDossier();
            }

            if (isset($dossiers_demo) && count($dossiers_demo)) {
                foreach ($dossiers_demo as $item) {
                    $dossiers[] = $item;
                }
            }
        }

        return $dossiers;
    }



    public function getUserDossierTmp(Utilisateur $user, $client, $site = null)
    {
        $user_type = $user->getAccesUtilisateur()->getType();

        $dossiers = [];

        if ($user->getShowDossierDemo()) {
            $dossiers_demo = $this->getEntityManager()
                ->getRepository('AppBundle:Dossier')
                ->createQueryBuilder('dossier')
                ->select('dossier')
                ->where('dossier.showInDemo = :show')
                ->orderBy('dossier.nom')
                ->setParameters(array(
                    'show' => TRUE
                ))
                ->getQuery()
                ->getResult();
        }

        if ($user_type <= 4) {
            //Si utilisateur site ou supérieur
            if ($site) {
                $dossiers = $this->getEntityManager()
                    ->getRepository('AppBundle:Dossier')
                    ->createQueryBuilder('dossier')
                    ->select('dossier')
                    ->innerJoin('dossier.site', 'site')
                    ->addSelect('site')
                    ->where('site = :site')
                    ->setParameters(array(
                        'site' => $site,
                    ))
                    ->orderBy('dossier.nom')
                    ->getQuery()
                    ->getResult();

                if (isset($dossiers_demo) && count($dossiers_demo)) {
                    foreach ($dossiers_demo as $item) {
                        $dossiers[] = $item;
                    }
                }
            } else {
                if ($user_type <= 3) {
                    $dossiers = $this->getEntityManager()
                        ->getRepository('AppBundle:Dossier')
                        ->getDossiersClientTmp($client);
                } else {
                    $sites = $this->getEntityManager()
                        ->getRepository('AppBundle:Site')
                        ->getUserSites($user, $client);
                    $site_id = [0];

                    /** @var Site $site */
                    foreach ($sites as $site) {
                        $site_id[] = $site->getId();
                    }

                    $qb = $this->getEntityManager()
                        ->getRepository('AppBundle:Dossier')
                        ->createQueryBuilder('dossier');

                    $dossiers = $qb
                        ->select('dossier')
                        ->innerJoin('dossier.site', 'site')
                        ->addSelect('site')
                        ->where($qb->expr()->in('site.id', ':site_id'))
                        ->setParameters(array(
                            'site_id' => $site_id
                        ))
                        ->orderBy('dossier.nom')
                        ->getQuery()
                        ->getResult();
                    if (isset($dossiers_demo) && count($dossiers_demo)) {
                        foreach ($dossiers_demo as $item) {
                            $dossiers[] = $item;
                        }
                    }
                }
            }
        } else {
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
                ->orderBy('dossier.nom')
                ->getQuery()
                ->getResult();

            /** @var UtilisateurDossier $user_dossier */
            foreach ($user_dossiers as $user_dossier) {
                $dossiers[] = $user_dossier->getDossier();
            }

            if (isset($dossiers_demo) && count($dossiers_demo)) {
                foreach ($dossiers_demo as $item) {
                    $dossiers[] = $item;
                }
            }
        }

        return $dossiers;
    }

    /**
     * Tous les dossiers d'un client
     * Tous sites confondus
     *
     * @param Client $client
     * @param $exercice
     * @param bool $infoperdos
     *
     * @return array
     */
    public function getDossiersClient(Client $client, $exercice = null, $infoperdos = false)
    {
        if ($exercice) {
            if ($infoperdos) {
                $dossiers = $this->getEntityManager()
                    ->getRepository('AppBundle:Dossier')
                    ->createQueryBuilder('dossier')
                    ->select('dossier')
                    ->innerJoin('dossier.site', 'site')
                    ->addSelect('site')
                    ->innerJoin('site.client', 'client')
                    ->addSelect('client')
                    ->where('client = :client')
                    ->andWhere('(dossier.status = 1 OR (dossier.status != 1 AND dossier.statusDebut > :exercice))')
                    ->setParameters([
                        'client' => $client,
                        'exercice' => $exercice,
                    ])
                    ->orderBy('dossier.nom')
                    ->getQuery()
                    ->getResult();
            } else {
                $dossiers = $this->getEntityManager()
                    ->getRepository('AppBundle:Dossier')
                    ->createQueryBuilder('dossier')
                    ->select('dossier')
                    ->innerJoin('dossier.site', 'site')
                    ->addSelect('site')
                    ->innerJoin('site.client', 'client')
                    ->addSelect('client')
                    ->where('client = :client')
                    ->andWhere('((dossier.status = 1 AND dossier.active = 1) OR (dossier.status != 1 AND dossier.statusDebut > :exercice AND dossier.active = 1))')
                    ->setParameters([
                        'client' => $client,
                        'exercice' => $exercice,
                    ])
                    ->orderBy('dossier.nom')
                    ->getQuery()
                    ->getResult();
            }
        } else {
            if ($infoperdos) {
                $dossiers = $this->getEntityManager()
                    ->getRepository('AppBundle:Dossier')
                    ->createQueryBuilder('dossier')
                    ->select('dossier')
                    ->innerJoin('dossier.site', 'site')
                    ->addSelect('site')
                    ->innerJoin('site.client', 'client')
                    ->addSelect('client')
                    ->where('client = :client')
                    ->andWhere('dossier.status = :status')
                    ->setParameters([
                        'client' => $client,
                        'status' => 1,
                    ])
                    ->orderBy('dossier.nom')
                    ->getQuery()
                    ->getResult();
            } else {
                $dossiers = $this->getEntityManager()
                    ->getRepository('AppBundle:Dossier')
                    ->createQueryBuilder('dossier')
                    ->select('dossier')
                    ->innerJoin('dossier.site', 'site')
                    ->addSelect('site')
                    ->innerJoin('site.client', 'client')
                    ->addSelect('client')
                    ->where('client = :client')
                    ->andWhere('dossier.status = :status')
                    ->andWhere('dossier.active = :active')
                    ->setParameters([
                        'client' => $client,
                        'status' => 1,
                        'active' => 1
                    ])
                    ->orderBy('dossier.nom')
                    ->getQuery()
                    ->getResult();
            }
        }
        return $dossiers;
    }

    public function getDossiersClientTmp(Client $client)
    {
        $dossiers = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->createQueryBuilder('dossier')
            ->select('dossier')
            ->innerJoin('dossier.site', 'site')
            ->addSelect('site')
            ->innerJoin('site.client', 'client')
            ->addSelect('client')
            ->where('client = :client')
            ->setParameters(array(
                'client' => $client,
            ))
            ->orderBy('dossier.nom')
            ->getQuery()
            ->getResult();

        return $dossiers;
    }

    /**
     * @param $dossier
     * @return array
     */
    function getJournaux($dossier)
    {
        $dossier = $this->createQueryBuilder('d')
            ->where('d.id = :dossier')
            ->setParameter('dossier',$dossier)
            ->getQuery()->getOneOrNullResult();

        return $this->getEntityManager()->getRepository('AppBundle:JournalDossier')
            ->createQueryBuilder("jd")
            ->where("jd.dossier = :dossier")
            ->setParameter('dossier', $dossier)
            ->orderBy('jd.codeStr', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    function getDateCloture(Dossier $dossier,$exercice)
//    {
//        $mois_cloture = $dossier->getCloture();
//        $mois_cloture++;
//        if($mois_cloture == 13)
//        {
//            $mois_cloture = 1;
//            $exercice++;
//        }
//        if($mois_cloture < 10) $mois_cloture = '0'.$mois_cloture;
//        $date_temp = new DateTime($exercice.'-'.$mois_cloture.'-01');
//        $date_temp->sub(new DateInterval('P1D'));
//        return $date_temp;
//    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @return DateTime
     */
    function getDateCloture(Dossier $dossier, $exercice)
    {
        $mois_cloture = $dossier->getCloture();
        if (!$mois_cloture || trim($mois_cloture) == '' || $mois_cloture > 12 || $mois_cloture < 1) {
            $mois_cloture = '12';
        }
        $fin_mois = array('31', '28', '31', '30', '31', '30', '31', '31', '30', '31', '30', '31');
        return new \DateTime($exercice . '-' . str_pad($mois_cloture, 2, '0', STR_PAD_LEFT) . '-' . $fin_mois[intval($mois_cloture) - 1]);
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @return DateTime
     */
    function getDateDebut(Dossier $dossier,$exercice)
    {
        $cloture = $this->getDateCloture($dossier,$exercice);
        $year = intval($cloture->format('Y') - 1);
        $cloture_1 = new DateTime($year.$cloture->format('-m-d'));
        return $cloture_1->add(new DateInterval('P1D'));
    }

    /**
     * @param $id
     * @return mixed
     */
    function getDossierById($id)
    {
        return $this->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    function getDossierClient($client, $dossier){
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $dossierQuery = '';
        if($dossier != 0){
            $dossierQuery = ' AND D.id = ';
            $dossierQuery .= $dossier.' ';
        }

        $query = "SELECT D.id,D.nom FROM dossier D,site S,client  C 
					WHERE S.id = D.site_id
					AND C.id = S.client_id
					AND C.id = ".$client."
					".$dossierQuery."
					AND D.status = 1 
					AND D.nom <>''
					ORDER BY D.nom";

        $prep = $pdo->query($query);
        return $prep->fetchAll(\PDO::FETCH_ASSOC);
    }

    function getById($dossierId){
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $query = "SELECT D.id,D.nom FROM dossier D
					WHERE D.id = ".$dossierId."
					AND D.status = 1 
					AND D.nom <>''
					ORDER BY D.nom";

        $prep = $pdo->query($query);
        return $prep->fetchAll(\PDO::FETCH_ASSOC);
    }


    function getDossierByName(Client $client, $name){
        $name = str_replace('_', '%', str_replace('-', '%', $name));

        $dossiers = $this->createQueryBuilder('d')
            ->innerJoin('d.site', 's')
            ->where('d.nom like :name')
            ->andWhere('s.client = :client')
            ->setParameter('name', $name)
            ->setParameter('client', $client)
            ->select('d')
            ->getQuery()
            ->getResult();

        if(count($dossiers) > 0){
            return $dossiers[0];
        }

        return null;
    }

    public function etatCompta($client, $exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $client= Boost::deboost($client,$this);

        $query = "  select d.nom as dossier, d.id as dossier_id
                    from dossier d
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.id = :client
                    and (
                        (d.status = 1 and d.active = 1)
                        or (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )
                    group by d.id
                    order by d.nom";

        $prep = $pdo->prepare($query);

        $prep->execute(array(
            'client'  => $client,
            'exercice'   => $exercice
        ));

        $resultat = $prep->fetchAll();

        $data = array();

        foreach ($resultat as $key => $value) {
            $item = array(
                'dossier'   => $value->dossier,
                'did'       => $value->dossier_id,
                'exercice'  => $exercice,
                'status'    => '<span class="simple_tag" id="id_import_historique" data-hasqtip="296" aria-describedby="qtip-296">Imports</span>',
                'ref-piece' => "<a href='#'> ref. </a>",
                'journaux'  => $this->journauxInconnu($value->dossier_id),
                'import'    => $this->statusImport($value->dossier_id,$exercice)
            );

            array_push($data, $item);
        }

        return $data;

    }

    public function statusImport($dossier,$exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "  select hu.cloture, date_format(hu.date_verification,'%d-%m-%Y') as date_verification, date_format(hu.date_upload,'%d-%m-%Y') as date_upload
                    from historique_upload hu
                    inner join dossier d on (hu.dossier_id=d.id)
                    where d.id = :dossier
                    and hu.type = 3
                    and hu.exercice = :exercice
                    order by hu.id desc";

        $prep = $pdo->prepare($query);

        $prep->execute(array(
            'dossier'  => $dossier,
            'exercice' => $exercice
        ));

        $resultat = $prep->fetchAll();

        $date_upload = "-";

        $date_verification = "cloturé";

        if (!empty($resultat)) {
            $date_upload = $resultat[0]->date_upload;

            if ($resultat[0]->cloture == 0) {
                $date_verification = "Projet " . $resultat[0]->date_verification;
            }

        } else {
            $date_verification = "Pas d'Import";
        }


        $d = $this->getEntityManager()
                  ->getRepository('AppBundle:Dossier')
                  ->find($dossier);


        $cloture = $this->getDateCloture($d,$exercice)->format('d-m-Y');

        // N moins 1

        $query = "  select hu.cloture, date_format(hu.date_verification,'%d-%m-%Y') as date_verification, date_format(hu.date_upload,'%d-%m-%Y') as date_upload
                    from historique_upload hu
                    inner join dossier d on (hu.dossier_id=d.id)
                    where d.id = :dossier
                    and hu.type = 3
                    and hu.exercice = :exercice
                    order by hu.id desc";

        $prep = $pdo->prepare($query);

        $prep->execute(array(
            'dossier'  => $dossier,
            'exercice' => $exercice - 1
        ));

        $resultat = $prep->fetchAll();

        $date_upload_moins_un = "-";

        $date_verification_moins_un = "cloturé";

        if (!empty($resultat)) {
            $date_upload_moins_un = $resultat[0]->date_upload;

            if ($resultat[0]->cloture == 0) {
                $date_verification_moins_un = "Projet " . $resultat[0]->date_verification;
            }

        } else {
            $date_verification_moins_un = "Pas d'Import";
        }


        $clotureNMoinsUn = $this->getDateCloture($d,($exercice - 1))->format('d-m-Y');

        $table = '<table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Ex.</th>
                      <th scope="col">Clôture</th>
                      <th scope="col">Import</th>
                      <th scope="col">Statut</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row">'. $exercice .'</th>
                      <td>'. $cloture .'</td>
                      <td>'. $date_upload .'</td>
                      <td>'. $date_verification .'</td>
                    </tr>
                    <tr>
                      <th scope="row">' . ($exercice - 1) . '</th>
                      <td>'. $clotureNMoinsUn .'</td>
                      <td>'. $date_upload_moins_un .'</td>
                      <td>'. $date_verification_moins_un .'</td>
                    </tr>
                  </tbody>
                </table>';

        return $table;



    }

    public function journauxInconnu($dossier)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "  SELECT jd.journal_id
                    FROM journal_dossier jd
                    INNER JOIN  dossier d ON (jd.dossier_id=d.id)
                    INNER JOIN site s ON (d.site_id=s.id)
                    INNER JOIN client c ON (s.client_id=c.id)
                    INNER JOIN journal j on(jd.journal_id=j.id)
                    WHERE d.id = :dossier";

        $prep = $pdo->prepare($query);

        $prep->execute(array(
            'dossier'   => $dossier
        ));

        $resultat = $prep->fetchAll();

        // $ok = 0;
        $inconnus = 0;

        if (empty($resultat)) {
            return '<span style="color:#f8ac59;"><i class="fa fa-warning"></i> Pas de journal</span>';
        } else {
            foreach ($resultat as $item) {
                if ($item->journal_id == 2) {
                    $inconnus += 1;
                }
            }
        }

        if ($inconnus == 0) {
            return '<span style="color:#009688;"><i class="fa fa-check-circle-o"></i> OK</span>';
        } else {
            return '<span style="color:#f85959;"><i class="fa fa-question-circle"></i> ' . $inconnus . ' inconnus</span>';
        }


        // if ($resultat == 0) {
        //     return '<span style="color:#009688;"><i class="fa fa-check-circle-o"></i> OK</span>';
        // } else {
        //     return '<span style="color:#f85959;"><i class="fa fa-question-circle"></i> ' . $resultat . ' inconnus</span>';
        // }

    }

    public function journauxParam($dossier)
    {
        $con = new CustomPdoConnection();

        $pdo = $con->connect();

        $req = '
            SELECT jd.id, d.nom as dossier, jd.code_str, jd.libelle as journal_dossier, j.libelle as type_journal, "<i class=\'fa fa-save icon-action js-save-button save-dnp\'></i>" as action
            FROM journal_dossier jd
            INNER JOIN  dossier d ON (jd.dossier_id=d.id)
            INNER JOIN journal j on(jd.journal_id=j.id)
            WHERE d.id = :dossier
            -- AND jd.journal_id = 2
        ';

        $prep = $pdo->prepare($req);

        $prep->execute(array(
            'dossier'   => $dossier
        ));

        $resultat = $prep->fetchAll();

        return $resultat;

    }
}