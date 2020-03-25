<?php
namespace AppBundle\Repository;


use AppBundle\Entity\NotificationDossier;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Notification;
use Doctrine\ORM\EntityRepository;

class NotificationDossierRepository extends EntityRepository
{
    public function findByDossierAndNotif(Dossier $dossier, Notification $notification){
    	$notifDossier = $this->getEntityManager()
					      ->getRepository('AppBundle:NotificationDossier')
					      ->createQueryBuilder('nd')
					      ->select('nd')
					      ->where('nd.dossier = :dossier')
					      ->andWhere('nd.notification = :notification')
					      ->setParameter('dossier', $dossier)
					      ->setParameter('notification', $notification)
					      ->orderBy('nd.id')
					      ->getQuery();
		return $notifDossier->getResult();
    }
}