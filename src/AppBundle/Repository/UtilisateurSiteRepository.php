<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 21/02/2017
 * Time: 17:05
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;

class UtilisateurSiteRepository extends EntityRepository
{
    /**
     * Liste des sites d'un utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return array
     */
    public function getUserSites(Utilisateur $utilisateur)
    {
        $sites = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurSite')
            ->createQueryBuilder('utilisateurSite')
            ->select('utilisateurSite')
            ->innerJoin('utilisateurSite.utilisateur', 'utilisateur')
            ->innerJoin('utilisateurSite.site', 'site')
            ->addSelect('site')
            ->innerJoin('site.client', 'client')
            ->addSelect('client')
            ->where('utilisateur = :utilisateur')
            ->andWhere('site.status = :status')
            ->setParameters(array(
                'utilisateur' => $utilisateur,
                'status' => 1,
            ))
            ->orderBy('site.nom')
            ->getQuery()
            ->getResult();
        return $sites;
    }

    /**
     * Supprimer les sites d'un utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return bool
     */
    public function removeUserSites(Utilisateur $utilisateur)
    {
        $sites = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurSite')
            ->getUserSites($utilisateur);
        if ($sites) {
            $em = $this->getEntityManager();
            foreach ($sites as $site) {
                $em->remove($site);
            }
            $em->flush();
        }
        return true;
    }
}