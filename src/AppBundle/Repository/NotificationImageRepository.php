<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 21/12/2017
 * Time: 09:19
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\NotificationImage;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurDossier;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;

class NotificationImageRepository extends EntityRepository
{
    public function getByClient(Client $client, Utilisateur $user, $site)
    {
        $em = $this->getEntityManager();
        $now = new \DateTime();
        $current_exercice = $now->format('Y');
        $dossiers = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->getUserDossier($user, $client, $site, $current_exercice, false);
        $dossier_ids = [0];
        /** @var \AppBundle\Entity\Dossier $dossier */
        foreach ($dossiers as $dossier) {
            $dossier_ids[] = $dossier->getId();
            $notification = $this->getEntityManager()
                ->getRepository('AppBundle:NotificationImage')
                ->findBy(array(
                    'dossier' => $dossier,
                ));
            if (count($notification) == 0) {
                $notification = new NotificationImage();
                $notification->setDossier($dossier);
                $em->persist($notification);
            }
        }

        try {
            $em->flush();
        } catch (OptimisticLockException $ole) {

        }

        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:NotificationImage')
            ->createQueryBuilder('ni');
        $notifications = $qb->select('ni')
            ->innerJoin('ni.dossier', 'd')
            ->addSelect('d')
            ->where($qb->expr()->in('d.id', ':dossier_ids'))
            ->setParameters(array(
                'dossier_ids' => $dossier_ids
            ))
            ->orderBy('d.nom')
            ->getQuery()
            ->getResult();

        $listes = array_map(function(NotificationImage $notification) {
            $destinataires = $this->getEmailDestinataire($notification);
            $notification->setDestinataire(trim(implode(";", $destinataires), ";"));
            $nom_contact = $this->getNomContactDossier($notification);
            $notification->setNomContact($nom_contact);
            return $notification;
        }, $notifications);

        return $listes;
    }

    public function getNomContactDossier(NotificationImage $notification)
    {
        if ($notification->getNomContact() && $notification->getNomContact() != "")
        {
            return $notification->getNomContact();
        }

        //RECHERCHE DES UTILISATEURS DU DOSSIER
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurDossier')
            ->createQueryBuilder('ud');
        $user_dossiers = $qb->select('ud')
            ->innerJoin('ud.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->innerJoin('ud.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('dossier = :dossier')
            ->andWhere('utilisateur.supprimer = :supprimer')
            ->setParameters(array(
                'dossier' => $notification->getDossier(),
                'supprimer' => 0
            ))
            ->getQuery()
            ->getResult();
        $contact = "";
        /** @var UtilisateurDossier $user_dossier */
        foreach ($user_dossiers as $user_dossier) {
            $nom = $user_dossier->getUtilisateur()->getNom();
            $prenom = $user_dossier->getUtilisateur()->getPrenom();
            if (($nom && $nom != "") || ($prenom && $prenom != ""))
            {
                $contact = $prenom . " " . $nom;
                break;
            }
        }

        return $contact;
    }

    public function getEmailUsersDossier(Dossier $dossier)
    {
        //RECHERCHE EMAILS DES UTILISATEURS DU DOSSIER
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurDossier')
            ->createQueryBuilder('ud');
        $user_dossiers = $qb->select('ud')
            ->innerJoin('ud.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->innerJoin('ud.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('dossier = :dossier')
            ->andWhere('utilisateur.supprimer = :supprimer')
            ->setParameters(array(
                'dossier' => $dossier,
                'supprimer' => 0
            ))
            ->getQuery()
            ->getResult();
        $emails = array_map(function(UtilisateurDossier $user_dossier) {
            return $user_dossier->getUtilisateur()->getEmail();
        }, $user_dossiers);

        return $emails;
    }

    public function getEmailDestinataire(NotificationImage $notification)
    {

        $emails = array_merge(explode(";", str_replace(" ", "", $notification->getDestinataire())));

        //RECHERCHE EMAILS DES UTILISATEURS DU DOSSIER
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurDossier')
            ->createQueryBuilder('ud');
        $user_dossiers = $qb->select('ud')
            ->innerJoin('ud.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->innerJoin('ud.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('dossier = :dossier')
            ->andWhere('utilisateur.supprimer = :supprimer')
            ->andWhere($qb->expr()->notIn('utilisateur.email', $emails))
            ->setParameters(array(
                'dossier' => $notification->getDossier(),
                'supprimer' => 0
            ))
            ->getQuery()
            ->getResult();
        $destinataires = explode(";", $notification->getDestinataire());
        /** @var UtilisateurDossier $user_dossier */
        foreach ($user_dossiers as $user_dossier) {
            $destinataires[] = $user_dossier->getUtilisateur()->getEmail();
        }

        return $destinataires;
    }

    /**
     * @param Dossier $dossier
     * @return NotificationImage|mixed
     * @throws OptimisticLockException
     */
    public function getByDossier(Dossier $dossier)
    {
        try {
            $notification = $this->getEntityManager()
                ->getRepository('AppBundle:NotificationImage')
                ->createQueryBuilder('ni')
                ->select('ni')
                ->innerJoin('ni.dossier', 'dossier')
                ->where('dossier = :dossier')
                ->setParameters([
                    'dossier' => $dossier
                ])
                ->getQuery()
                ->getOneOrNullResult();

            if (!$notification) {
                $em = $this->getEntityManager();
                $notification = new NotificationImage();
                $notification->setDossier($dossier);
                $em->persist($notification);
                $em->flush();
            }

            $destinataires = $this->getEmailDestinataire($notification);
            $notification->setDestinataire(trim(implode(";", $destinataires), ";"));
            $nom_contact = $this->getNomContactDossier($notification);
            $notification->setNomContact($nom_contact);

            return $notification;

        } catch (NonUniqueResultException $ex) {
            $notifications = $this->getEntityManager()
                ->getRepository('AppBundle:NotificationImage')
                ->createQueryBuilder('ni')
                ->select('ni')
                ->innerJoin('ni.dossier', 'dossier')
                ->where('dossier = :dossier')
                ->setParameters([
                    'dossier' => $dossier
                ])
                ->getQuery()
                ->getResult();
            $i = 0;
            foreach ($notifications as $notification) {
                $em = $this->getEntityManager();
                if ($i > 0) {
                    $em->remove($notification);
                }
                $em->flush();
            }
            return $this->getByDossier($dossier);
        }
    }

    public function getEmailPmUsersDossier(Dossier $dossier)
    {
        return   $this->getEntityManager()
                        ->getRepository('AppBundle:UtilisateurDossier')
                        ->createQueryBuilder('ud')
                        ->select('ud')
                        ->innerJoin('ud.utilisateur', 'utilisateur')
                        ->addSelect('utilisateur')
                        ->innerJoin('ud.dossier', 'dossier')
                        ->addSelect('dossier')
                        ->where('dossier = :dossier')
                        ->andWhere('utilisateur.supprimer = :supprimer')
                        ->setParameters(array(
                            'dossier' => $dossier,
                            'supprimer' => 0
                        ))
                        ->getQuery()
                        ->getResult();
    }
}