<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 12/01/2018
 * Time: 15:42
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;

class EmailsRepository extends EntityRepository
{
    public function getNonEnvoye($type = null)
    {
        if ($type && $type != '') {
            $emails = $this->getEntityManager()
                ->getRepository('AppBundle:Emails')
                ->createQueryBuilder('emails')
                ->select('emails')
                ->where('emails.typeEmail = :type')
                ->andWhere('emails.status = :status')
                ->setParameters(array(
                    'type' => $type,
                    'status' => 0,
                ))
                ->orderBy('emails.dateCreation')
                ->getQuery()
                ->getResult();
        } else {
            $emails = $this->getEntityManager()
                ->getRepository('AppBundle:Emails')
                ->createQueryBuilder('emails')
                ->select('emails')
                ->andWhere('emails.status = :status')
                ->setParameters(array(
                    'status' => 0,
                ))
                ->orderBy('emails.dateCreation')
                ->getQuery()
                ->getResult();
        }
        return $emails;
    }

    public function getNotificationImage(Utilisateur $user, Client $client, $site, $dossier)
    {
        $exercice = date('Y');
        $dossiers = $this->getEntityManager()
            ->getRepository('AppBundle:Tbimage')
            ->getDossierListe($user, $client, $site, $dossier, $exercice);
        $dossier_ids = [0];
        /** @var \AppBundle\Entity\Dossier $dossier */
        foreach ($dossiers as $dossier) {
            $dossier_ids[] = $dossier->getId();
        }

        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:Emails')
            ->createQueryBuilder('e');
        $emails = $qb->select('e')
            ->innerJoin('e.dossier', 'dossier')
            ->addSelect('dossier')
            ->where($qb->expr()->in('dossier.id', $dossier_ids))
            ->andWhere('e.typeEmail = :type')
            ->setParameters(array(
                'type' => 'RAPPEL_IMAGE',
            ))
            ->orderBy('dossier.nom')
            ->addOrderBy('e.dateCreation')
            ->getQuery()
            ->getResult();
        return $emails;
    }

    public function getAllNotification(Utilisateur $user, Client $client, $site, $dossier)
    {
        $exercice = date('Y');
        $type = ['RAPPEL_IMAGE', 'BANQUE_MANQUANTE', 'RAPPEL_DRT'];
        $dossiers = $this->getEntityManager()
            ->getRepository('AppBundle:Tbimage')
            ->getDossierListe($user, $client, $site, $dossier, $exercice);
        $dossier_ids = [0];
        /** @var \AppBundle\Entity\Dossier $dossier */
        foreach ($dossiers as $dossier) {
            $dossier_ids[] = $dossier->getId();
        }

        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:Emails')
            ->createQueryBuilder('e');
        $emails = $qb->select('e')
            ->innerJoin('e.dossier', 'dossier')
            ->addSelect('dossier')
            ->where($qb->expr()->in('dossier.id', $dossier_ids))
            ->andWhere($qb->expr()->in('e.typeEmail', $type))
            ->orderBy('dossier.nom')
            ->addOrderBy('e.dateCreation')
            ->getQuery()
            ->getResult();
        return $emails;
    }
}