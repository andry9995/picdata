<?php
/**
 * Created by PhpStorm.
 * User: Dinoh
 * Date: 20/03/2019
 * Time: 16:53
 */
namespace InfoPerdosBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\NotificationEmail;
use AppBundle\Entity\NotificationEntity;
use AppBundle\Entity\NotificationParametre;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class NotificationController extends Controller
{
    public function indexAction(){
        return $this->render('InfoPerdosBundle:Notification:index.html.twig');
    }

    public function addNotificationFormAction(Request $request){
        if($request->isXmlHttpRequest())
        {
            $responsableTitres = $this->getDoctrine()
                                      ->getRepository('AppBundle:ResponsableCsdTitre')
                                      ->findBy(array(), array('libelle' => 'asc'));

            $notifications = $this->getDoctrine()
                                      ->getRepository('AppBundle:Notification')
                                      ->findBy(array(), array('libelle' => 'asc'));

            return $this->render('InfoPerdosBundle:Notification:notification-form-ajout.html.twig', array(
                'responsableTitres' => $responsableTitres,
                'notifications' => $notifications
            ));
        }
        return false;
    }

    public function addNotificationAction(Request $request){
        if($request->isXmlHttpRequest())
        {
            $emailExist = false;
            $post = $request->request;
            $clientId = $post->get('client');
            $clientId = Boost::deboost($clientId, $this);
            $dossierId = $post->get('dossier');
            $dossierId = Boost::deboost($dossierId, $this);
            $notificationId = $post->get('notification');
            $responsableCsdTitreId = $post->get('responsable');
            $email = $post->get('email');
            $isAdd = $post->get('isAdd');

            $dossier = $this->getDoctrine()
                            ->getRepository('AppBundle:Dossier')
                            ->find($dossierId);

            $notification = $this->getDoctrine()
                            ->getRepository('AppBundle:Notification')
                            ->find($notificationId);

            $responsableCsdTitre = $this->getDoctrine()
                                        ->getRepository('AppBundle:ResponsableCsdTitre')
                                        ->find($responsableCsdTitreId);

            $client = $this->getDoctrine()
                            ->getRepository('AppBundle:Client')
                            ->find($clientId);

            $site = $dossier->getSite();

            $notificationEntityExist = $this->getDoctrine()
                                            ->getRepository('AppBundle:NotificationEntity')
                                            ->findBy(array(
                                               'dossier' => $dossier,
                                               'client'  => $client,
                                               'site'    => $site
                                            ));
            foreach ($notificationEntityExist as $notif) {
                $mail_exist = $notif->getNotificationEmail()->getMail();
                $emailExist = false;
                if($mail_exist === $email){
                    $emailExist = true;
                    break;
                }
            }

            if($isAdd) {
                if( !$emailExist ) {
                    $em = $this->getDoctrine()
                               ->getManager();

                    $notificationEmail = new NotificationEmail();
                    $notificationEmail
                        ->setMail($email)
                        ->setResponsableCsdTitre($responsableCsdTitre);
                    $em->persist($notificationEmail);
                    $em->flush();

                    $notificationEntity = new NotificationEntity();
                    $notificationEntity
                        ->setDossier($dossier)
                        ->setClient($client)
                        ->setSite($site)
                        ->setNotificationEmail($notificationEmail);
                    $em->persist($notificationEntity);
                    $em->flush();

                    $notificationParametre = new NotificationParametre();
                    $notificationParametre
                        ->setNotification($notification)
                        ->setNotificationEntity($notificationEntity);
                    $em->persist($notificationParametre);
                    $em->flush();
                    return new JsonResponse('SUCCES');
                }
                return new JsonResponse('MAIL_EXIST');
            }
            return new JsonResponse('ERROR');
        }
        return new JsonResponse('ERROR');
    }

    public function getListNotificationAction(Request $request){
        if($request->isXmlHttpRequest()) {
            $post = $request->request;
            $clientId = $post->get('client');
            $clientId = Boost::deboost($clientId, $this);
            $dossierId = $post->get('dossier');
            $dossierId = Boost::deboost($dossierId, $this);
            $rows = [];
            $liste_data = [];

            $dossier = $this->getDoctrine()
                            ->getRepository('AppBundle:Dossier')
                            ->find($dossierId);

            $site = $dossier->getSite();

            $client = $this->getDoctrine()
                           ->getRepository('AppBundle:Client')
                           ->find($clientId);

            $notificationEntity = $this->getDoctrine()
                                       ->getRepository('AppBundle:NotificationEntity')
                                       ->findBy(array(
                                           'dossier' => $dossierId,
                                           'site' => $site,
                                           'client' => $client
                                       ));

            foreach ( $notificationEntity as $notif_entity ) {
                $list_notifications = $this->getDoctrine()
                                             ->getRepository('AppBundle:Notification')
                                             ->getListNotification($notif_entity);

                if( !empty($list_notifications) ) {
                    $rows[] = [
                        'id' => $list_notifications[0]->getNotificationEntity()->getId(),
                        'cell' => [
                            't-libelle' => $list_notifications[0]->getNotification()->getLibelle(),
                            't-code' => $list_notifications[0]->getNotification()->getCode(),
                            't-email' => $list_notifications[0]->getNotificationEntity()->getNotificationEmail()->getMail(),
                            't-responsable' => $list_notifications[0]->getNotificationEntity()->getNotificationEmail()->getResponsableCsdTitre()->getLibelle(),
                            't-actions' => '<i class="fa fa-save icon-action js-save-modif-notification" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-notification" title="Supprimer"></i>'
                        ],
                    ];
                }
            }
            $liste_data = [
                'rows' => $rows,
            ];
            return new JsonResponse($liste_data);
        }
        return new JsonResponse('ERROR');
    }

    public function deleteNotificationAction($id){
        $em = $this->getDoctrine()
                   ->getManager();

        $notification_entity = $this->getDoctrine()
                                    ->getRepository('AppBundle:NotificationEntity')
                                    ->find($id);

        $notification_param = $this->getDoctrine()
                                   ->getRepository('AppBundle:NotificationParametre')
                                   ->findBy(array(
                                      'notificationEntity' => $notification_entity
                                   ));

        if(!empty($notification_entity)){
            $notification_email = $notification_entity->getNotificationEmail();
            $em->remove($notification_param[0]);
            $em->remove($notification_entity);
            $em->remove($notification_email);
            $em->flush();
            return new JsonResponse('SUCCES');
        }else{
            return new JsonResponse('ERROR');
        }
    }

    public function responsableTitreAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else
            {
                $options = '<select>';
                $responsableTitres = $this->getDoctrine()
                                          ->getRepository('AppBundle:ResponsableCsdTitre')
                                          ->findBy(array(), array('libelle' => 'asc'));
                foreach ($responsableTitres as $responsableTitre)
                {
                    $options .='<option value="'.$responsableTitre->getId().'">'.$responsableTitre->getLibelle().'</option>';
                }

                $options .= '</select>';

                return new Response($options);
            }
        }
        else
        {
            throw  new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function codeTitreAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else
            {
                $options = '<select>';
                $notifications = $this->getDoctrine()
                                          ->getRepository('AppBundle:Notification')
                                          ->findBy(array(), array('libelle' => 'asc'));
                foreach ($notifications as $codeTitre)
                {
                    $options .='<option value="'.$codeTitre->getId().'" data-libelle="'.$codeTitre->getLibelle().'">'.$codeTitre->getCode().'</option>';
                }

                $options .= '</select>';

                return new Response($options);
            }
        }
        else
        {
            throw  new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editNotificationAction(Request $request){
        $em = $this->getDoctrine()
                   ->getManager();
        $notificationEntityId = $request->request->get('id');
        $responsableId = $request->request->get('t-responsable');
        $codeId = $request->request->get('t-code');
        $email = $request->request->get('t-email');

        $notificationEntity = $this->getDoctrine()
                                    ->getRepository('AppBundle:NotificationEntity')
                                    ->find($notificationEntityId);

        if(!empty($notificationEntity)){
            $responsableCsdTitre = $this->getDoctrine()
                                        ->getRepository('AppBundle:ResponsableCsdTitre')
                                        ->find($responsableId);

            $notificationEmail = $notificationEntity->getNotificationEmail();

            $notification = $this->getDoctrine()
                                 ->getRepository('AppBundle:Notification')
                                 ->find($codeId);

            $notificationParam = $this->getDoctrine()
                                      ->getRepository('AppBundle:NotificationParametre')
                                      ->findBy(array(
                                          'notificationEntity' => $notificationEntity
                                      ));

            $notificationParam = $notificationParam[0];

            $notificationEmail->setMail($email)
                              ->setResponsableCsdTitre($responsableCsdTitre);
            $em->flush();

            $notificationParam->setNotification($notification)
                              ->setNotificationEntity($notificationEntity);
            $em->flush();

            return new JsonResponse('SUCCES');
        }else{
            return new JsonResponse('ERROR');
        }
    }
}