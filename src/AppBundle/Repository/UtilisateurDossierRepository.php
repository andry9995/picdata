<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 21/02/2017
 * Time: 17:05
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;

class UtilisateurDossierRepository extends EntityRepository
{
    /**
     * Liste des dossiers d'un utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return array
     */
    public function getUserDossiers(Utilisateur $utilisateur)
    {
        $dossiers = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurDossier')
            ->createQueryBuilder('utilisateurDossier')
            ->select('utilisateurDossier')
            ->innerJoin('utilisateurDossier.utilisateur', 'utilisateur')
            ->innerJoin('utilisateurDossier.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('utilisateur = :utilisateur')
            ->innerJoin('dossier.site', 'site')
            ->addSelect('site')
            ->innerJoin('site.client', 'client')
            ->addSelect('client')
            ->setParameters(array(
                'utilisateur' => $utilisateur,
            ))
            ->orderBy('dossier.nom')
            ->getQuery()
            ->getResult();
        return $dossiers;
    }

    public function getDossierUsers(Dossier $dossier)
    {
        $users = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurDossier')
            ->createQueryBuilder('utilisateurDossier')
            ->select('utilisateurDossier')
            ->innerJoin('utilisateurDossier.utilisateur', 'utilisateur')
            ->innerJoin('utilisateurDossier.dossier', 'dossier')
            ->addSelect('utilisateur')
            ->addSelect('dossier')
            ->where('dossier = :dossier')
            ->innerJoin('dossier.site', 'site')
            ->addSelect('site')
            ->innerJoin('site.client', 'client')
            ->addSelect('client')
            ->setParameters(array(
                'dossier' => $dossier,
            ))
            ->orderBy('utilisateur.nom')
            ->getQuery()
            ->getResult();
        return $users;
    }

    /**
     * Supprimer les dossiers d'un utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeUserDossiers(Utilisateur $utilisateur)
    {
        $userDossiers = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurDossier')
            ->getUserDossiers($utilisateur);
        if ($userDossiers) {
            $em = $this->getEntityManager();
            foreach ($userDossiers as $userDossier) {
                $em->remove($userDossier);
            }
            $em->flush();
        }
        return true;
    }
}