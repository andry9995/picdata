<?php
/**
 * Created by PhpStorm.
 * User: Dinoh
 * Date: 21/03/2019
 * Time: 16:42
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\NotificationEntity;

class NotificationRepository extends EntityRepository
{
    public function getListNotification(NotificationEntity $notificationEntity){
        return $this->getEntityManager()
                    ->getRepository('AppBundle:NotificationParametre')
                    ->createQueryBuilder('notif_param')
                    ->select('notif_param')
                    ->innerJoin('notif_param.notification', 'notif')
                    ->addSelect('notif')
                    ->innerJoin('notif_param.notificationEntity', 'notif_entity')
                    ->addSelect('notif_entity')
                    ->where('notif_param.notificationEntity = :notification_entity')
                    ->setParameters(array(
                        'notification_entity' => $notificationEntity
                    ))
                    ->getQuery()
                    ->getResult();
    }
}