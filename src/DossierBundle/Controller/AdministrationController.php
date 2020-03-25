<?php

namespace DossierBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use TableauImageBundle\Form\ParamSmtpType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\NotificationImage;
use AppBundle\Entity\NotificationPm;
use AppBundle\Entity\NotificationDossier;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\ResponsableCsd;
use AppBundle\Entity\ListeMailEnvoiAutoPm;
use AppBundle\Entity\Site;
use AppBundle\Security\RandomPassword;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Controller\Boost;

class AdministrationController extends Controller
{
    public function indexAction()
    {
        return $this->render('DossierBundle:Administration:index.html.twig');
    }

    public function smtpAction()
    {
        $smtp_form = $this->createForm(ParamSmtpType::class);
        return $this->render('@InfoPerdos/Client/smtp.html.twig', array(
            'smtp_form' => $smtp_form->createView(),
        ));
    }

    public function logAction()
    {
        return $this->render('DossierBundle:Administration:index-log.html.twig');
    }

    public function listeEtParametrageMailAction()
    {
    	$tabs =
            [
                '0' => 'Configuration général',
                '1' => 'Configuration',
                '2' => 'Configuration dossier',
                '3' => 'Rappels images',
                '4' => 'Banque manquante',
                '5' => 'Autres PM',
            ];
        return $this->render('DossierBundle:Administration:index-liste-parametrage.html.twig',['tabs'=>$tabs]);
    }

    public function getResponsable($v)
    {
    	$responsable = '';
    	if($v->getTypeCsd()){
			switch (intval($v->getTypeCsd())) {
				case 0:
					$responsable = 'Client';
					break;
				case 1:
					$responsable = 'EC site';
					break;
				case 2:
					$responsable = 'EC dossier';
					break;
				case 3:
					$responsable = 'supervision';
					break;
				case 4:
					$responsable = 'administratif';
					break;
				case 5:
					$responsable = 'scriptura';
					break;
				
				default:
					$responsable = 'Client final';
					break;
			}
		}
	    return $responsable;
    }

    public function listeRappelPmAction(Request $request)
    {
    	if ($request->isXmlHttpRequest()) {
            if ($request->getMethod() == 'POST') {
            	$post = $request->request;
		        $client = $post->get('client');
		        $clientId = Boost::deboost($client, $this);
		        $dossier = $post->get('dossier');
		        $dossierId = Boost::deboost($dossier, $this);
		        $exercice = $post->get('exercice');
		        $rows = [];
		        $liste = [];

		        if(intval($dossierId) == 0){
		        	$client = $this->getDoctrine()
		                            ->getRepository('AppBundle:Client')
		                            ->find($clientId);
		        	$dossiers = $this->getDoctrine()
	                            	 ->getRepository('AppBundle:Dossier')
	                            	 ->getDossiersClient($client, $exercice);
	                foreach ($dossiers as $k => $d) {
				    	$mailShow = '';
			        	$statut = '';
		        		$notificationPm = $this->getDoctrine()
					                           ->getRepository('AppBundle:NotificationPm')
					                           ->findBy(array('dossier' => $d->getId()));

					    if(count($notificationPm) > 0){
					    	foreach ($notificationPm as $key => $value) {
				        		if( $mailShow == '' ){
		                            $mailShow = $value->getMail();
		                        }else{
		                            $mailShow = $mailShow.'; '. $value->getMail();
		                        }
				        	}
					    }else{
					    	$em = $this->getDoctrine()->getManager();
					    	$mail = [];
		                	$responsables = $this->getDoctrine()
				                             	 ->getRepository('AppBundle:ResponsableCsd')
				                             	 ->getResponsableParDossier($d);
				            foreach ($responsables as $k => $v) {
				            	if( !in_array($v->getEmail(), $mail) && $v->getEmail() != '') {
				            		$mail[] = $v->getEmail();
						        	if( $mailShow == '' ){
			                            $mailShow = $v->getEmail();
			                        }else{
			                            $mailShow = $mailShow.'; '. $v->getEmail();
			                        }
				        		
			                        $notificationPmEntity = new NotificationPm();
	        						$notificationPmEntity->setDossier($d)
	            						 				 ->setNom($v->getNom())
	            						 				 ->setPrenom($v->getPrenom())
	            						 				 ->setResponsable($v->getTypeCsd())
	            						 				 ->setMail($v->getEmail());
                            		$em->persist($notificationPmEntity);
                            	}
		                    }

		                    $responsables = $this->getDoctrine()
		                                         ->getRepository('AppBundle:ResponsableCsd')
		                                         ->getResponsableSiteOuClient($d);
                         	foreach ($responsables as $k => $v) {
					        	if( !in_array($v->getEmail(), $mail) && $v->getEmail() != '') {
				            		$mail[] = $v->getEmail();
						        	if( $mailShow == '' ){
			                            $mailShow = $v->getEmail();
			                        }else{
			                            $mailShow = $mailShow.'; '. $v->getEmail();
			                        }
				        		
			                        $notificationPmEntity = new NotificationPm();
	        						$notificationPmEntity->setDossier($d)
	            						 				 ->setNom($v->getNom())
	            						 				 ->setPrenom($v->getPrenom())
	            						 				 ->setResponsable($v->getTypeCsd())
	            						 				 ->setMail($v->getEmail());
                            		$em->persist($notificationPmEntity);
                            	}
		                    }

		                    $responsables = $this->getDoctrine()
	                                         ->getRepository('AppBundle:ResponsableCsd')
	                                         ->getResponsableClient($d);
	                        foreach ($responsables as $k => $v) {
					        	if( !in_array($v->getEmail(), $mail) && $v->getEmail() != '') {
				            		$mail[] = $v->getEmail();
						        	if( $mailShow == '' ){
			                            $mailShow = $v->getEmail();
			                        }else{
			                            $mailShow = $mailShow.'; '. $v->getEmail();
			                        }
				        		
			                        $notificationPmEntity = new NotificationPm();
	        						$notificationPmEntity->setDossier($d)
	            						 				 ->setNom($v->getNom())
	            						 				 ->setPrenom($v->getPrenom())
	            						 				 ->setResponsable($v->getTypeCsd())
	            						 				 ->setMail($v->getEmail());
                            		$em->persist($notificationPmEntity);
                            	}
		                    }

		                    $responsables = $this->getDoctrine()
	                                         ->getRepository('AppBundle:ResponsableCsd')
	                                         ->getResponsableClientScriptura($d->getSite()->getClient());

	                        foreach ($responsables as $k => $v) {
					        	if( !in_array($v->getEmail(), $mail) && $v->getEmail() != '') {
				            		$mail[] = $v->getEmail();
						        	if( $mailShow == '' ){
			                            $mailShow = $v->getEmail();
			                        }else{
			                            $mailShow = $mailShow.'; '. $v->getEmail();
			                        }
				        		
			                        $notificationPmEntity = new NotificationPm();
	        						$notificationPmEntity->setDossier($d)
	            						 				 ->setNom($v->getNom())
	            						 				 ->setPrenom($v->getPrenom())
	            						 				 ->setResponsable($v->getTypeCsd())
	            						 				 ->setMail($v->getEmail());
                            		$em->persist($notificationPmEntity);
                            	}
		                    }

		                    $responsables = $this->getDoctrine()
	                                     ->getRepository('AppBundle:NotificationImage')
	                                     ->getEmailPmUsersDossier($d);

			                foreach ( $responsables as $user ) {
			                    if ( $user->getUtilisateur() && $user->getUtilisateur()->getEmail()  ) {
			                        if( !in_array($user->getUtilisateur()->getEmail(), $mail) &&  $user->getUtilisateur()->getEmail() != '') {
			                        	$mail[] = $user->getUtilisateur()->getEmail();
			                            if( $mailShow == '' ){
				                            $mailShow = $user->getUtilisateur()->getEmail();
				                        }else{
				                            $mailShow = $mailShow.'; '. $user->getUtilisateur()->getEmail();
				                        }

				                        $notificationPmEntity = new NotificationPm();
	            						$notificationPmEntity->setDossier($d)
	                						 				 ->setNom($user->getUtilisateur()->getNom())
	                						 				 ->setPrenom($user->getUtilisateur()->getPrenom())
	                						 				 ->setResponsable(6)
	                						 				 ->setMail($user->getUtilisateur()->getEmail());
	                                	$em->persist($notificationPmEntity);
			                        }
			                    }
			                }

            				$em->flush();
				        }
			        	$rows[] = [
		                    'id' => $d->getId(),
		                    'cell' => [
		                        'rappel-pm-dossier' => $d->getNom(),
		                        'rappel-pm-statut' => ($d->getStatus()) ? 'Actif' : 'Désactivé',
		                        'rappel-pm-mail' => $mailShow,
	                        	'rappel-pm-action' => '<i class="fa fa-cog pointer gerer-mail-pm"></i>'
		                    ],
		                ];
	                }
		        }else{
			        $mailShow = '';
			        $statut = '';
		        	$dossier = $this->getDoctrine()
		                            ->getRepository('AppBundle:Dossier')
		                            ->find($dossierId);
		        	$notificationPm = $this->getDoctrine()
				                           ->getRepository('AppBundle:NotificationPm')
				                           ->findBy(array('dossier' => $dossier->getId()));
			        if(count($notificationPm) > 0){
			        	$notExist = false;
			        	foreach ($notificationPm as $key => $value) {
			        		$statut = (intval($value->getStatut()) == 1) ? 'Actif' : 'Desactivé';
			        		if( $mailShow == '' ){
	                            $mailShow = $value->getMail();
	                        }else{
	                            $mailShow = $mailShow.'; '. $value->getMail();
	                        }
			        	}

			        	$rows[] = [
		                    'id' => $dossier->getId(),
		                    'cell' => [
		                        'rappel-pm-dossier' => $dossier->getNom(),
		                        'rappel-pm-statut' => $statut,
		                        'rappel-pm-mail' => $mailShow,
		                        'rappel-pm-action' => '<i class="fa fa-cog pointer gerer-mail-pm"></i>'
		                    ],
		                ];
			        }else{
				        $responsables = $this->getDoctrine()
				                             ->getRepository('AppBundle:ResponsableCsd')
				                             ->getResponsableParDossier($dossier);
				        $mail = [];
				        $em = $this->getDoctrine()->getManager();
				        foreach ( $responsables as $responsable ) {
	                        if ( $responsable->getEmail() && $responsable->getEmail() != '' ) {
	                            if ( $responsable->getEnvoiMail() === 1 ) {
	                                if( !in_array($responsable->getEmail(), $mail) ) {
	                                	$mail[] = $responsable->getEmail();
	                                    if( $mailShow == '' ){
				                            $mailShow = $responsable->getEmail();
				                        }else{
				                            $mailShow = $mailShow.'; '. $responsable->getEmail();
				                        }
	                	
				        				$notificationPmEntity = new NotificationPm();
                						$notificationPmEntity->setDossier($dossier)
	                						 				 ->setNom($responsable->getNom())
	                						 				 ->setPrenom($responsable->getPrenom())
	                						 				 ->setResponsable($responsable->getTypeCsd())
	                						 				 ->setMail($responsable->getEmail());
	                                	$em->persist($notificationPmEntity);
	                                }
	                            }
	                        }
	                    }

				        /** Responsables Site ou Client */
				        $responsables = $this->getDoctrine()
	                                         ->getRepository('AppBundle:ResponsableCsd')
	                                         ->getResponsableSiteOuClient($dossier);
	                    foreach ($responsables as $responsable) {
	                        if ($responsable->getEmail() && $responsable->getEmail() != '') {
	                            if($responsable->getEnvoiMail() === 1){
	                                if( !in_array($responsable->getEmail(), $mail) ) {
	                                	$mail[] = $responsable->getEmail();
	                                    if( $mailShow == '' ){
				                            $mailShow = $responsable->getEmail();
				                        }else{
				                            $mailShow = $mailShow.'; '. $responsable->getEmail();
				                        }
	                	
				        				$notificationPmEntity = new NotificationPm();
                						$notificationPmEntity->setDossier($dossier)
	                						 				 ->setNom($responsable->getNom())
	                						 				 ->setPrenom($responsable->getPrenom())
	                						 				 ->setResponsable($responsable->getTypeCsd())
	                						 				 ->setMail($responsable->getEmail());
	                                	$em->persist($notificationPmEntity);
	                                }
	                            }
	                        }
	                    }

	                    /** Responsables Client */
	                    $responsables = $this->getDoctrine()
	                                         ->getRepository('AppBundle:ResponsableCsd')
	                                         ->getResponsableClient($dossier);
	                    foreach ($responsables as $responsable) {
	                        if ($responsable->getEmail() && $responsable->getEmail() != '') {
	                            if($responsable->getEnvoiMail() === 1){
	                                if( !in_array($responsable->getEmail(), $mail) ) {
	                                	$mail[] = $responsable->getEmail();
	                                    if( $mailShow == '' ){
				                            $mailShow = $responsable->getEmail();
				                        }else{
				                            $mailShow = $mailShow.'; '. $responsable->getEmail();
				                        }
	                	
				        				$notificationPmEntity = new NotificationPm();
                						$notificationPmEntity->setDossier($dossier)
	                						 				 ->setNom($responsable->getNom())
	                						 				 ->setPrenom($responsable->getPrenom())
	                						 				 ->setResponsable($responsable->getTypeCsd())
	                						 				 ->setMail($responsable->getEmail());
	                                	$em->persist($notificationPmEntity);
	                                }
	                            }
	                        }
	                    }

	                    /** Responsables Scriptura */
	                    $responsables = $this->getDoctrine()
	                                         ->getRepository('AppBundle:ResponsableCsd')
	                                         ->getResponsableClientScriptura($dossier->getSite()->getClient());

	                    foreach ( $responsables as $responsable ) {
	                        if ( $responsable->getEmail() && $responsable->getEmail() != '' ) {
	                            if( !in_array($responsable->getEmail(), $mail) ) {
	                            	$mail[] = $responsable->getEmail();
	                                if( $mailShow == '' ){
			                            $mailShow = $responsable->getEmail();
			                        }else{
			                            $mailShow = $mailShow.'; '. $responsable->getEmail();
			                        }
	                	
			        				$notificationPmEntity = new NotificationPm();
            						$notificationPmEntity->setDossier($dossier)
                						 				 ->setNom($responsable->getNom())
                						 				 ->setPrenom($responsable->getPrenom())
                						 				 ->setResponsable($responsable->getTypeCsd())
                						 				 ->setMail($responsable->getEmail());
                                	$em->persist($notificationPmEntity);
	                            }
	                        }
	                    }
                		$responsables = $this->getDoctrine()
	                                     ->getRepository('AppBundle:NotificationImage')
	                                     ->getEmailPmUsersDossier($dossier);

		                foreach ( $responsables as $user ) {
		                    if ( $user->getUtilisateur() && $user->getUtilisateur()->getEmail()  ) {
		                        if( !in_array($user->getUtilisateur()->getEmail(), $mail) &&  $user->getUtilisateur()->getEmail() != '') {
		                        	$mail[] = $user->getUtilisateur()->getEmail();
		                            if( $mailShow == '' ){
			                            $mailShow = $user->getUtilisateur()->getEmail();
			                        }else{
			                            $mailShow = $mailShow.'; '. $user->getUtilisateur()->getEmail();
			                        }

			                        $notificationPmEntity = new NotificationPm();
            						$notificationPmEntity->setDossier($dossier)
                						 				 ->setNom($user->getUtilisateur()->getNom())
                						 				 ->setPrenom($user->getUtilisateur()->getPrenom())
                						 				 ->setResponsable(6)
                						 				 ->setMail($user->getUtilisateur()->getEmail());
                                	$em->persist($notificationPmEntity);
		                        }
		                    }
		                }
                		$em->flush();

		                $rows[] = [
		                    'id' => $dossier->getId(),
		                    'cell' => [
		                        'rappel-pm-dossier' => $dossier->getNom(),
		                        'rappel-pm-statut' => ($dossier->getStatus()) ? 'Actif' : 'Désactivé',
		                        'rappel-pm-mail' => $mailShow,
		                        'rappel-pm-action' => '<i class="fa fa-cog pointer gerer-mail-pm"></i>'
		                    ],
		                ];
			        }
	            }

		        $liste = [
                	'rows' => $rows,
                ];
                return new JsonResponse($liste);
            }
        }
    }

    public function configMailPmAction($id = null)
    {
    	$dossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($id);
        $listes = $this->getDoctrine()
	                     ->getRepository('AppBundle:NotificationPm')
	                     ->findBy(array('dossier' => $dossier));
        return $this->render('DossierBundle:Administration:tableau-config.html.twig', array('listes' => $listes));
    }

    public function statutMailPmAction($id = null, $value)
    {
    	if($id){
    		$em = $this->getDoctrine()->getManager();
	        $notificationPmEntity = $this->getDoctrine()
					                     ->getRepository('AppBundle:NotificationPm')
					                     ->find($id);
			$notificationPmEntity->setStatut($value);
        	$em->persist($notificationPmEntity);
        	$em->flush();
		    return new JsonResponse('ok');
		}else{
			return new JsonResponse('error');
		}
    }

    public function addNewMailPmAction(Request $request)
    {
    	$post = $request->request;
        $nom = $post->get('nom');
        $prenom = $post->get('prenom');
        $dossierId = $post->get('dossier');
        $mail = $post->get('mail');
        $dossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($dossierId);
        $em = $this->getDoctrine()->getManager();
        $notificationPmEntity = new NotificationPm();
		$notificationPmEntity->setDossier($dossier)
			 				 ->setNom($nom)
			 				 ->setPrenom($prenom)
			 				 ->setResponsable(6)
			 				 ->setMail($mail);
    	$em->persist($notificationPmEntity);
		$em->flush();

		$id = $notificationPmEntity->getId();
    	return new JsonResponse($id);
    }

    public function removeUserMailAction($id = null)
    {
    	$em = $this->getDoctrine()->getManager();
        $notificationPmEntity = $this->getDoctrine()
				                     ->getRepository('AppBundle:NotificationPm')
				                     ->find($id);
		$em->remove($notificationPmEntity);
    	$em->flush();
    	return new JsonResponse('ok');
    }

    public function resendMailCreationAction(Request $request)
    {
    	$post = $request->request;
        $user = $post->get('user');
    	$users = explode(', ', $user);
    	$data = [
            'message' => "L'email a été re-envoyé, qui contient les étapes à suivre pour activer le nouveau compte.",
        ];
    	foreach ($users as $key => $user_id) {
	        $user_id = Boost::deboost($user_id, $this);
	        /** @var Utilisateur $utilisateur */
	        $utilisateur = $this->getDoctrine()
	            ->getRepository('AppBundle:Utilisateur')
	            ->find($user_id);
	        if ($utilisateur) {
	            $em = $this->getDoctrine()
	                ->getManager();
	            $utilisateur
	                ->setLastLogin(NULL)
	                ->setSupprimer(false);
	            $em->flush();

	            $email_copies = $this->getDoctrine()
	                ->getRepository('AppBundle:CreationCompteEmail')
	                ->getEmailByClient($utilisateur->getClient());

	            $responsable_scripturas = $this->getDoctrine()
	                ->getRepository('AppBundle:ResponsableCsd')
	                ->getResponsableClientScriptura($utilisateur->getClient());

	            $email_scripturas = [];
	            if(count($responsable_scripturas) > 0){
	                /** @var ResponsableCsd $responsable_scriptura */
	                foreach ($responsable_scripturas as $responsable_scriptura){
	                    if($responsable_scriptura->getEmail() !== null && $responsable_scriptura->getEnvoiMail() === 1) {
	                        $email_scripturas[] = $responsable_scriptura->getEmail();
	                    }
	                }
	            }

	            $from_details = $this->getFromDetails($utilisateur->getClient());



	            $message = \Swift_Message::newInstance()
	                ->setSubject("Création de votre compte")
	                ->setFrom($from_details['address'], $from_details['label'])
	                ->setTo($utilisateur->getEmail())
	                ->setBcc('support@scriptura.biz')
	                ->addBcc('francia@lesexperts.biz')
	                ->addBcc('arq@scriptura.biz')
	                ->addBcc('philcastellan@gmail.com')
	                ->addBcc('pjlcastellan@gmail.com');



	//            $message = \Swift_Message::newInstance()
	//                ->setSubject("Création de votre compte")
	//                ->setFrom($from_details['address'], $from_details['label'])
	//                ->setTo('philcastellan@gmail.com');
	            if ($email_copies) {
	                /** @var \AppBundle\Entity\CreationCompteEmail $copy */
	                foreach ($email_copies as $copy) {
	                    if ($copy->getEmail() && $copy->getEmail() != '') {
	                        $message->addBcc($copy->getEmail());
	                    }
	                }
	            }

	            if(count($email_scripturas) > 0){
	                foreach ($email_scripturas as $email_scriptura){
	                    if($email_scriptura != ''){
	                        $message->addBcc($email_scriptura);
	                    }
	                }
	            }

	            if ($utilisateur->getClient()->getNom() == 'ESSECA') {
	                $message->setBody(
	                    $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email-esseca.html.twig', [
	                        'utilisateur' => $utilisateur,
	                        'token' => Boost::boost($utilisateur->getEmail()),
	                        'reply_to' => $this->getReplyTo($utilisateur->getClient()),
	                        'client' => $utilisateur->getClient(),
	                        'raw_password' => Boost::deboost($utilisateur->getPassword(), $this),
	                    ])
	                    , 'text/html');
	//                $message->addBcc('v.sarhadian@esseca.com');
	            } elseif ($utilisateur->getClient()->getNom() == 'EXPERTS_EXPANSION') {
	                $message->setBody(
	                    $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email-experts_expansion.html.twig', array(
	                        'utilisateur' => $utilisateur,
	                        'token' => Boost::boost($utilisateur->getEmail()),
	                        'reply_to' => $this->getReplyTo($utilisateur->getClient()),
	                        'client' => $utilisateur->getClient(),
	                        'raw_password' => Boost::deboost($utilisateur->getPassword(), $this),
	                        'support_email' => 'mailto:support@expertcontact.fr'
	                    ))
	                    , 'text/html');
	//                $message->addBcc('vreboul@expertcontact.fr');

	            }
	            elseif ($utilisateur->getClient()->getNom() == 'NAULIER_ASSOCIES'){

	                $message->setBody(
	                    $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email-naulier.html.twig', array(
	                        'utilisateur' => $utilisateur,
	                        'token' => Boost::boost($utilisateur->getEmail()),
	                        'reply_to' => $this->getReplyTo($utilisateur->getClient()),
	                        'client' => $utilisateur->getClient(),
	                        'raw_password' => Boost::deboost($utilisateur->getPassword(), $this),
	                        'support_email' => 'mailto:support@lesexperts.biz'
	                    ))
	                    , 'text/html');
	            }
	            elseif ($utilisateur->getClient()->getNom() == 'AVEC' ||
	                $utilisateur->getClient()->getId() == 776){

	                $message->setBody(
	                    $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email-avec.html.twig', array(
	                        'utilisateur' => $utilisateur,
	                        'token' => Boost::boost($utilisateur->getEmail()),
	                        'reply_to' => $this->getReplyTo($utilisateur->getClient()),
	                        'client' => $utilisateur->getClient(),
	                        'raw_password' => Boost::deboost($utilisateur->getPassword(), $this),
	                        'support_email' => 'mailto:support@lesexperts.biz'
	                    ))
	                    , 'text/html');
	            }
	            elseif ($utilisateur->getClient()->getNom() == 'BHN'){
	                $message->setBody
	                ($this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email-bhn.hmtl.twig', array(
	                    'utilisateur' => $utilisateur,
	                    'token' => Boost::boost($utilisateur->getEmail()),
	                    'reply_to' => $this->getReplyTo($utilisateur->getClient()),
	                    'client' => $utilisateur->getClient(),
	                    'raw_password' => Boost::deboost($utilisateur->getPassword(), $this),
	                    'support_email' => 'support.bhn@bhn-expertise.com'
	                ))
	                    , 'text/html');
	            }
	            else {
	                $message->setBody(
	                    $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email.html.twig', [
	                        'utilisateur' => $utilisateur,
	                        'token' => Boost::boost($utilisateur->getEmail()),
	                        'reply_to' => $this->getReplyTo($utilisateur->getClient()),
	                        'client' => $utilisateur->getClient(),
	                        'raw_password' => Boost::deboost($utilisateur->getPassword(), $this),
	                        'support_email' => 'mailto:support@lesexperts.biz'
	                    ])
	                    , 'text/html');
	            }
	            /** GET MAILER PAR CLIENT */
	            $this->get('app.mailer_par_client')
	                ->getMailer($utilisateur->getClient())
	                ->send($message);
	        } 
	    }
		return new JsonResponse(json_encode($data));
    }

    public function getConfigNotifAction($id)
    {
    	$notifications = $this->getDoctrine()
				              ->getRepository('AppBundle:Notification')
				              ->findAll();

    	$dossier = $this->getDoctrine()
			            ->getRepository('AppBundle:Dossier')
			            ->find($id);

		$listeNotif = null;
		if($dossier)
			$listeNotif = $this->getDoctrine()
					           ->getRepository('AppBundle:NotificationDossier')
					           ->findBy(array('dossier'=> $dossier->getId()));
		return $this->render('DossierBundle:Administration:config-dossier-notif.html.twig', array(
			'notifications' => $notifications,
			'listeNotif'		=> $listeNotif,
			'dossier'		=> $dossier
		));
    }

    public function setConfigNotifAction(Request $request)
    {
    	$post = $request->request;
        $listeCheck = $post->get('notifCheck');
        $liste = $post->get('notif');
        $dossierId = $post->get('dossierId');
        $dossier = $this->getDoctrine()
			            ->getRepository('AppBundle:Dossier')
			            ->find($dossierId);
        $em = $this->getDoctrine()->getManager();
        if($listeCheck){
	        foreach ($listeCheck as $k=>$l) {
	        	$notifId = $l['item'];
				$notif =   $this->getDoctrine()
				            	->getRepository('AppBundle:Notification')
				                ->find($notifId);

				$hasNotifDossier = $this->getDoctrine()
				            			->getRepository('AppBundle:NotificationDossier')
				                		->findByDossierAndNotif($dossier, $notif);
				if(count($hasNotifDossier) > 0){
					$hasNotifDossier[0]->setDossier($dossier)
									   ->setNotification($notif);
				}else{
					$notifDossier = new NotificationDossier();
		            $notifDossier->setDossier($dossier)
		                         ->setNotification($notif);
	           		$em->persist($notifDossier);
				}
	        }
	        $em->flush();
	    }
	    if($liste){
	        foreach ($liste as $key => $li) {
	        	$notifId = $li['item'];
				$notif =   $this->getDoctrine()
				            	->getRepository('AppBundle:Notification')
				                ->find($notifId);

				$hasNotifDossier = $this->getDoctrine()
				            			->getRepository('AppBundle:NotificationDossier')
				                		->findByDossierAndNotif($dossier, $notif);
				if(count($hasNotifDossier) > 0){
					$em->remove($hasNotifDossier[0]);
				}
	        }
	        $em->flush();
	    }
		return new JsonResponse('succes');
    }

    public function updateDestinatairePmAction(Request $request, NotificationPm $notification)
    {
        $em = $this->getDoctrine()->getManager();
        $titre = $request->request->get('titre', 1);
        $nom = $request->request->get('nom', '');
        $destinataire = $request->request->get('destinataire', '');
        $copie = $request->request->get('copie');

        $user_emails = $this->getDoctrine()
            ->getRepository('AppBundle:NotificationPm')
            ->getEmailUsersDossier($notification->getDossier());
        $array_destinataires = explode(";", $destinataire);

        $new_destinataires = array_filter($array_destinataires, function($dest) use ($user_emails) {
            return !in_array($dest, $user_emails);
        });

        if ($nom != '' && $destinataire != '') {
            $notification
                ->setTitreContact($titre)
                ->setNomContact($nom)
                ->setCopie($copie);
            if (count($new_destinataires) > 0) {
                $notification->setDestinataire(trim(implode(";", $new_destinataires), ";"));
            }
            $em->flush();

            $data = [
                'erreur' => FALSE,
            ];
            return new JsonResponse($data);
        } else {
            throw new BadRequestHttpException('Données invalides.');
        }
    }

    public function updateDestinataireAutresPmAction(Request $request, NotificationAutresPm $notification)
    {
        $em = $this->getDoctrine()->getManager();
        $titre = $request->request->get('titre', 1);
        $nom = $request->request->get('nom', '');
        $destinataire = $request->request->get('destinataire', '');
        $copie = $request->request->get('copie');

        $user_emails = $this->getDoctrine()
            ->getRepository('AppBundle:NotificationAutresPm')
            ->getEmailUsersDossier($notification->getDossier());
        $array_destinataires = explode(";", $destinataire);

        $new_destinataires = array_filter($array_destinataires, function($dest) use ($user_emails) {
            return !in_array($dest, $user_emails);
        });

        if ($nom != '' && $destinataire != '') {
            $notification
                ->setTitreContact($titre)
                ->setNomContact($nom)
                ->setCopie($copie);
            if (count($new_destinataires) > 0) {
                $notification->setDestinataire(trim(implode(";", $new_destinataires), ";"));
            }
            $em->flush();

            $data = [
                'erreur' => FALSE,
            ];
            return new JsonResponse($data);
        } else {
            throw new BadRequestHttpException('Données invalides.');
        }
    }

    public function listParametreAction($client, $site)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $rows = [];
        if ($the_client) {
            if ($site == '0') {
                $the_site = NULL;
            } else {
                $site_id = Boost::deboost($site, $this);
                $the_site = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->find($site_id);
            }
            $notifications = $this->getDoctrine()
                ->getRepository('AppBundle:NotificationPm')
                ->getByClient($the_client, $this->getUser(), $the_site);

            /** @var \AppBundle\Entity\NotificationPm $notification */
            foreach ($notifications as $notification) {
                $contenu = "";
                if ($notification->getContenu()) {
                    if (strlen($notification->getContenu()) >= 50) {
                        $contenu = substr($notification->getContenu(), 0, 50) . ' ...';
                    } else {
                        $contenu = $notification->getContenu();
                    }
                }

                $dossier = $notification->getDossier();

                $status = '';
                switch ($dossier->getStatus()){
                    case 1:
                        $status = 'Actif';
                        break;
                    case 2:
                        $status = 'Suspendu';
                        if($dossier->getStatusDebut() !== null){
                            $status .= ' - '.$dossier->getStatusDebut();
                        }
                        break;
                    case 3:
                        $status = 'Radié';
                        if($dossier->getStatusDebut() !== null){
                            $status .= ' - '.$dossier->getStatusDebut();
                        }
                        break;
                }

                $notifDossier = $this->getDoctrine()
					            	 ->getRepository('AppBundle:NotificationDossier')
					                 ->findBy(array('dossier' => $dossier->getId()));
				$code = 'Manuel';
				if(count($notifDossier) > 0){
					foreach ($notifDossier as $notifD) {
						if($notifD->getNotification()->getCode() == 'BANQUE')
							$code = 'Automatique';
					}
				}

                $rows[] = [
                    'id' => $dossier->getId().'-'.$notification->getId(),
                    'cell' => [
                        $dossier->getNom(),
                        $status,
                        $notification->getDestinataire(),
                        $notification->getCopie(),
                        $notification->getTitreContact(),
                        $notification->getNomContact(),
                        $contenu,
                        $code,
                        $notification->getContenu(),
                        $notification->getObjet(),
                        $dossier->getStatusDebut()
                    ],
                ];
            }
        }

        $liste = [
            'rows' => $rows,
        ];

        return new JsonResponse($liste);
    }  
    
    public function emailDefaultContentAction()
    {
        /** @var \AppBundle\Entity\EmailTemplate $template */
        $template = $this->getDoctrine()
            ->getRepository('AppBundle:EmailTemplate')
            ->getByCode('email_pm_default');
        $default_content = '';
        if ($template) {
            $default_content = $template->getContenu();
        }
        return new Response($default_content);
    } 
    
    public function emailDefaultContentAutresPmAction()
    {
        /** @var \AppBundle\Entity\EmailTemplate $template */
        $template = $this->getDoctrine()
            ->getRepository('AppBundle:EmailTemplate')
            ->getByCode('email_autres_pm_default');
        $default_content = '';
        if ($template) {
            $default_content = $template->getContenu();
        }
        return new Response($default_content);
    } 

    public function editEmailContenuAction(Request $request, $tous)
    {
        $em = $this->getDoctrine()->getManager();
        $notification_id = $request->request->get('notification', '');
        $contenu = $request->request->get('contenu');
        $objet = $request->request->get('objet');

        if(trim($objet) === ''){
            $objet = null;
        }

        $client_id = Boost::deboost($request->request->get('client'), '');
        $site_id = Boost::deboost($request->request->get('site'), '');

        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $site = NULL;
        if ($site_id != '0') {
            $site = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->find($site_id);
        }

        if ($tous == 1) {
            $notifications = $this->getDoctrine()
                ->getRepository('AppBundle:NotificationPm')
                ->getByClient($client, $this->getUser(), $site);

            /** @var NotificationImage $notification */
            foreach ($notifications as $notification) {
                $notification->setContenu($contenu);
                $notification->setObjet($objet);
            }
        } else {
            /** @var NotificationImage $notification */
            $notification = $this->getDoctrine()
                ->getRepository('AppBundle:NotificationPm')
                ->find($notification_id);
            if ($notification) {
                $notification->setContenu($contenu);
                $notification->setObjet($objet);
            }
        }

        $em->flush();

        $data = [
            'erreur' => FALSE,
        ];
        return new JsonResponse($data);
    }

    public function editEmailContenuAutresPmAction(Request $request, $tous)
    {
        $em = $this->getDoctrine()->getManager();
        $notification_id = $request->request->get('notification', '');
        $contenu = $request->request->get('contenu');
        $objet = $request->request->get('objet');

        if(trim($objet) === ''){
            $objet = null;
        }

        $client_id = Boost::deboost($request->request->get('client'), '');
        $site_id = Boost::deboost($request->request->get('site'), '');

        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $site = NULL;
        if ($site_id != '0') {
            $site = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->find($site_id);
        }

        if ($tous == 1) {
            $notifications = $this->getDoctrine()
                ->getRepository('AppBundle:NotificationAutresPm')
                ->getByClient($client, $this->getUser(), $site);

            /** @var NotificationImage $notification */
            foreach ($notifications as $notification) {
                $notification->setContenu($contenu);
                $notification->setObjet($objet);
            }
        } else {
            /** @var NotificationImage $notification */
            $notification = $this->getDoctrine()
                ->getRepository('AppBundle:NotificationAutresPm')
                ->find($notification_id);
            if ($notification) {
                $notification->setContenu($contenu);
                $notification->setObjet($objet);
            }
        }

        $em->flush();

        $data = [
            'erreur' => FALSE,
        ];
        return new JsonResponse($data);
    }

    public function getConfigEnvoiAutoAction(Request $request)
    {
    	$dossierId = $request->request->get('dossier');
    	$notificationId = $request->request->get('notification');
    	$typeEmail = $request->request->get('typeEmail');
    	$typeNotif = $request->request->get('typeNotif');
    	$typeEmail = ($typeEmail == 'Automatique') ? 1 : 0;
    	if($typeNotif == 'banque'){
	        $notification = $this->getDoctrine()
	            				 ->getRepository('AppBundle:NotificationPm')
	                			 ->find($notificationId);
		}else{
	        $notification = $this->getDoctrine()
	            				 ->getRepository('AppBundle:NotificationAutresPm')
	                			 ->find($notificationId);
		}

    	if(!$notification) return new JsonResponse('erreur');

        $param = json_decode($notification->getParamEnvoiAuto(), true);
        $now = new \DateTime();
        $listesTachesDispo = [];
        $listesTachesExist = [];
        $tachesExist = [];
        $listesTaches = [];
        $tachesExists = [];
        $valuePrim = '';
        $valueSec = '';
        $periode = '';
        $value = '';
        $fin = -1;
        $type = -1;
        $recur = -1;
        $status = 0;
        $valueTousLes = -1;
        $stateJourFixe = -1;
       	if(count($param) > 0){
       		foreach ($param['taches'] as $key => $val) {
           		$tachesExist[] = $val['tache'];
           		//$tachesExist[$key]['date'] = $value['date_echeance'];
           	}
           	if(intval($param['typeEmail'])){
	           	$valuePrim = $param['valuePrim'];
	           	$valueSec = $param['valueSec'];
	           	$fin = $param['fin'];
	           	$type = $param['type'];
	           	$recur = $param['recur'];
	           	$status = $param['status'];
	        	$stateJourFixe = $param['stateJourFixe'];
           	}else{
           		$value = $param['value'];
           	}
       	}

        $taches = $this->getDoctrine()
        			   ->getRepository('AppBundle:Tache')
                       ->getTachesPourGestionTaches($dossierId, $now, true, true, true,
                                                true, true, null);
       
        if(array_key_exists($dossierId, $taches['taches'])){
        	foreach ($taches['taches'][$dossierId] as $k => $t) {
	           	$abrevTache = explode('*', $t['titre2']);
	           	$datetimetache = $t['datetime'];
	           	$periode = '';
    			if($abrevTache[0] == 'MAJ'){
    				$tacheLibre = $this->getDoctrine()
				        			   ->getRepository('AppBundle:TachesLibre')
				                       ->findBy(array('dossier' => $t['dossierId']));
                   if(count($tacheLibre) > 0){
        				$tachesLibreDate = $this->getDoctrine()
					        			   ->getRepository('AppBundle:TachesLibreDate')
					                       ->findBy(array('tachesLibre' => $tacheLibre[0]->getId()));
					    $periode = (count($tachesLibreDate) > 0) ? $tachesLibreDate[0]->getPeriode() : '';
					    switch (intval($periode)) {
					    	case 0:
					    		$periode = 'P-';
					    		break;
					    	case 1:
					    		$periode = 'A-';
					    		break;
					    	case 2:
					    		$periode = 'S-';
					    		break;
					    	case 3:
					    		$periode = 'Q-';
					    		break;
					    	case 4:
					    		$periode = 'T-';
					    		break;
					    	case 6:
					    		$periode = 'B-';
					    		break;
					    	case 12:
					    		$periode = 'M-';
					    		break;
					    	
					    	default:
					    		$periode = '';
					    		break;
					    }
                   }
    			}
	           	if(in_array($abrevTache[0], $tachesExist) && !in_array($abrevTache[0], $tachesExists)){
           			$listesTaches[$k]['tache'] = $abrevTache[0];
           			$listesTaches[$k]['date'] = $periode.$datetimetache->format('d/m/Y');
           			$tachesExists[] = $abrevTache[0];
	           	}
	           	if(!in_array($abrevTache[0], $listesTachesExist) && !in_array($abrevTache[0], $tachesExist)){
           			$listesTachesDispo[$k]['tache'] = $abrevTache[0];
           			$listesTachesDispo[$k]['date'] = $periode.$datetimetache->format('d/m/Y');
           			$listesTachesExist[] = $abrevTache[0];
	           	}
	        }
	    	return $this->render('DossierBundle:Administration:edit-action-rappel-pm.html.twig', array(
				'taches' => $listesTachesDispo,
				'notificationId' => $notificationId,
				'tachesExist' => $listesTaches,
				'valuePrim' => $valuePrim,
				'valueSec' => $valueSec,
				'fin' => $fin,
				'type' => $type,
				'recur' => $recur,
				'status' => $status,
				'stateJourFixe' => $stateJourFixe,
				'typeEmail' => $typeEmail,
				'value' => $value,
				'typeNotif' => $typeNotif
			));
        }else{
        	return new JsonResponse('erreur');
        }
    }

    public function setConfigEnvoiAutoAction(Request $request)
    {
    	$notificationId = $request->request->get('notification');
    	$typeEmail = intval($request->request->get('typeEmail'));
    	$typeNotif = $request->request->get('typeNotif');
    	$taches = $request->request->get('taches');
    	$em = $this->getDoctrine()->getManager();
    	$mois = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre','Decembre'];

		$repository = $this->getDoctrine()->getRepository('AppBundle:NotificationAutresPm');
    	if($typeNotif == 'banque')
    		$repository = $this->getDoctrine()->getRepository('AppBundle:NotificationPm');

    	if($typeEmail){
    		$status = $request->request->get('choixRegle');
	    	$fin = $request->request->get('fin');
	    	$type = $request->request->get('type');
	    	$recur = $request->request->get('recur');
	    	$valuePrim = $request->request->get('valPrim');
	    	$valueSec = $request->request->get('valSec');
	    	$stateJourFixe = $request->request->get('stateJourFixe');
	        $tab = [
	        	'taches' => $taches,
	        	'valuePrim' => $valuePrim,
	        	'valueSec'  => $valueSec,
	        	'status' => $status,
	        	'typeEmail' => $typeEmail,
	        	'fin'	 => $fin,
	        	'type'	 => $type,
	        	'recur'	 => $recur,
				'stateJourFixe' => $stateJourFixe
	        ];
    	}else{
    		$value = $request->request->get('value');
	        $tab = [
	        	'taches' => $taches,
	        	'value' => $value,
	        	'typeEmail' => $typeEmail
	        ];
    	}
    	
    	//var_dump($fin);

        $notification = $repository->find($notificationId);

        $tab = json_encode($tab);
        $notification->setParamEnvoiAuto($tab);
        $em->flush();

        //insertion donnée dans table envoi mail pm auto
        $dossier = $notification->getDossier();

        $now = new \DateTime();
        $tacheDossier = $this->getDoctrine()
	        			     ->getRepository('AppBundle:Tache')
	                         ->getTachesPourGestionTaches($dossier->getId(), $now, true, true, true,
                                                true, true, null);
        foreach ($taches as $key => $tach) {
        	$liste = $this->getDoctrine()
	    				  ->getRepository('AppBundle:ListeMailEnvoiAutoPm')
	        			  ->findBy(array(
				                'dossier' => $dossier->getId(),
				                'tache'   => $tach['tache'],
				                'typeNotif' => $typeNotif
				            ));
	        $dateEnvoi = null;
	        if(count($liste) == 0){
	        	//recherche date pour envoi mail auto
	        	$tachesTaites = [];
	        	$em = $this->getDoctrine()->getManager();
	        	if($typeEmail){
		        	if($status == 3 || $status == 2 || $status == 1){
		        		foreach ($tacheDossier['taches'][$dossier->getId()] as $k => $t) {
		                    $abrevTache = explode('*', $t['titre2']);
		                    $abrevTache = $abrevTache[0];
		                    $datetimetache = null;
		                    if($abrevTache == $tach['tache'] && !in_array($tach['tache'], $tachesTaites)){
		                        $datetimetache = $t['datetime'];
		                        $tachesTaites[] = $tach['tache'];
		                        if($datetimetache){
			        				if($status != 2){
			                        	$dateEnvoi = $datetimetache->sub(new \DateInterval('P'.$valuePrim.'D'));
			                        	$newListePmPrim = new ListeMailEnvoiAutoPm();
							        	$newListePmPrim->setDossier($dossier);
							        	$newListePmPrim->setTache($tach['tache']);
							        	$newListePmPrim->setDateEcheance($datetimetache);
						        		$newListePmPrim->setDate($dateEnvoi);
	        							$newListePmPrim->setTypeNotif($typeNotif);
						        		$em->persist($newListePmPrim);
						        		$em->flush();
			                        }

			                        if($status != 1){
										$dateEnvoi = \DateTime::createFromFormat('d/m/Y',$valueSec);
			                        	$newListePmSec = new ListeMailEnvoiAutoPm();
							        	$newListePmSec->setDossier($dossier);
							        	$newListePmSec->setTache($tach['tache']);
							        	$newListePmSec->setDateEcheance($datetimetache);
						        		$newListePmSec->setDate($dateEnvoi);
	        							$newListePmSec->setTypeNotif($typeNotif);
							        	if($stateJourFixe == 2){
							        		if($fin != -1 && $type != -1 && $recur != -1){
						        				$newListePmSec->setRecurrence($recur);
							        			switch ($type) {
							        				case 'le':
							        					$date_fin = \DateTime::createFromFormat('d/m/Y',$fin);
								        				$newListePmSec->setDateFin($date_fin);
							        					break;

							        				case 'apres':
								        				$newListePmSec->setTerminer($fin);
							        					break;
							        				
							        				default:
								        				$newListePmSec->setTerminer(-1);
							        					break;
							        			}
							        		}elseif($type == 'jamais' && $recur != -1 && $fin == -1){
						        				$newListePmSec->setRecurrence($recur);
							        			$newListePmSec->setTerminer(-1);
							        		}
							        	}
						        		$em->persist($newListePmSec);
						        		$em->flush();
						        	}
							    }
							}
						}
					}
				}else{
					$dateEnvoi = \DateTime::createFromFormat('d/m/Y',$value);
                	$newListePmManuel = new ListeMailEnvoiAutoPm();
		        	$newListePmManuel->setDossier($dossier);
		        	$newListePmManuel->setTache($tach['tache']);
	        		$newListePmManuel->setDate($dateEnvoi);
	        		$newListePmManuel->setTypeNotif($typeNotif);
	        		$em->persist($newListePmManuel);
	        		$em->flush();
				}
	        } 
        }
        
        return new JsonResponse('ok');
    }

    public function itemAction(Request $request)
    {
    	$client = $request->request->get('client');
    	$site = $request->request->get('site');
    	$type = $request->request->get('type');
    	$configAdmin = $this->getConfigItem($client, $site, $type);
    	if (in_array($type,[0,1,2,3,4,5]))
            return $configAdmin;
    }

    public function getConfigItem($client, $site, $type)
    {
    	if($type == 0) return $this->listConfigGeneral($client, $site);
    	//if($type == 4) return $this->listParametre($client, $site);
    }

    public function listConfigGeneral($client, $site)
    {
    	$client_id = Boost::deboost($client, $this);
        $site_id = Boost::deboost($site, $this);
        if (is_bool($client_id) || is_bool($site_id)) {
            throw new AccessDeniedHttpException('security');
        }

        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $the_site = $this->getDoctrine()
            ->getRepository('AppBundle:Site')
            ->find($site_id);

        $dossiers = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->getUserDossierTmp($this->getUser(), $the_client, $the_site);
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $serializer = new Serializer(array($normalizer), array($encoder));
        $data = [];
        /** @var Dossier $dossier */
        foreach ($dossiers as $dossier) {
            $dossierUsers = $this->getDoctrine()
                ->getRepository('AppBundle:UtilisateurDossier')
                ->getDossierUsers($dossier);
            $users = [];
            $sbs = [];

            /** @var BanqueCompte[] $banqueComptes */
            $banqueComptes = $this->getDoctrine()
                ->getRepository('AppBundle:BanqueCompte')
                ->getBanqueCompteByDossier($dossier);

            if (count($dossierUsers) > 0) {
                /** @var UtilisateurDossier $dossierUser */
                foreach ($dossierUsers as $dossierUser) {
                    $users[] = [
                        'user_id' => Boost::boost($dossierUser->getUtilisateur()->getId()),
                        'user' => $dossierUser->getUtilisateur()->getNomComplet(),
                        'email' => $dossierUser->getUtilisateur()->getEmail(),
                        'actif' => $dossierUser->getUtilisateur()->getSupprimer() ? 0 : 1,
                        'last_login' => $dossierUser->getUtilisateur()->getLastLogin() ? $dossierUser->getUtilisateur()->getLastLogin()->format('d/m/Y') : '',
                    ];
                }
            }

            if(count($banqueComptes) > 0){
                foreach ($banqueComptes as $banqueCompte){
                    if($banqueCompte->getSourceImage() !== null){
                        if($banqueCompte->getSourceImage()->getId() === 3){
                            $sbs[] = [
                                'banque' => $banqueCompte->getBanque()->getNom(),
                                'numcompte' =>$banqueCompte->getNumcompte()
                            ];
                        }
                    }
                }
            }
            $data[] = [
                'id' => $dossier->getId(),
                'idCrypter' => $dossier->getIdCrypter(),
                'nom' => $dossier->getNom(),
                'indicateurGroup' => $dossier->getIndicateurGroup(),
                'cloture' => $dossier->getCloture(),
                'site' => $dossier->getSite()->getNom(),
                'site_id' => $dossier->getSite()->getId(),
                'status' => $dossier->getStatus(),
                'statusDebut' => $dossier->getStatusDebut(),
                'client' => $dossier->getSite()->getClient()->getNom(),
                'client_id' => $dossier->getSite()->getClient()->getId(),
                'active' => $dossier->getActive(),
                'dateStopSaisie' => $dossier->getDateStopSaisie() ? $dossier->getDateStopSaisie()->format('d/m/Y') : null,
                'users' => $users,
                'sbs' =>$sbs
            ];
        }

        $json = $serializer->serialize($data, 'json');
        return new JsonResponse($json);
    }

    public function configCabinetAction($client, $site)
    {
    	$client_id = Boost::deboost($client, $this);
    	$the_client = $this->getDoctrine()
			                ->getRepository('AppBundle:Client')
			                ->find($client_id);
        $rows = [];
        if ($the_client) {
        	if($site == 0){
        		$site = $this->getDoctrine()
			                  ->getRepository('AppBundle:Site')
				              ->getAllSitesByClient($the_client);
        	}else{
        		$site = [];
        		$siteId = Boost::deboost($site, $this);
        		$siteEntity = $this->getDoctrine()
			                  ->getRepository('AppBundle:Site')
				              ->find($siteId);
				$site[] = $siteEntity;
        	}

        	foreach ($site as $key => $s) {
				$responsables = $this->getDoctrine()
		                     ->getRepository('AppBundle:ResponsableCsd')
		                     ->findBy(array(
				                'site' => $s,
				                'typeCsd' => 1
				             ));

	            /*$dossiers = $this->getDoctrine()
				                 ->getRepository('AppBundle:Dossier')
				             	 ->findBy(array(
					                'site' => $s,
					             ));
				foreach ($dossiers as $d) {
		            $responsDossier = $this->getDoctrine()
					                 ->getRepository('AppBundle:ResponsableCsd')
					             	 ->findBy(array(
						                'dossier' => $d,
						                'typeCsd' => 3
						             ));
					if(count($responsDossier) > 0)
						$responsables[] = $responsDossier[0];
				}*/
				$emailExist = [];

				foreach ($responsables as $key => $responsable) {
					$email = $responsable->getEmail();
					if(strpos($email, 'scriptura.biz') === false && !in_array($email, $emailExist)){
						$user = $this->getDoctrine()
				                     ->getRepository('AppBundle:Utilisateur')
				                     ->findBy(array(
				                     	'email'=>$email
				                     ));
				        $type = '';
				        $acces = '';
				        $id = 'new_row';
				        $site = '';
				        if(count($user) > 0){
				        	if($user[0]->getTypeUtilisateur())
				        		$type = $user[0]->getTypeUtilisateur()->getType();
				        	if($user[0]->getAccesUtilisateur())
				        		$acces = $user[0]->getAccesUtilisateur()->getLibelle();
				        	$societe = $user[0]->getSociete();
				        	$id = $user[0]->getId();
				        }
				        
						$rows[] = [
			                'id' => $id,
			                'cell' => [
			                    $responsable->getNom(),
			                    $responsable->getPrenom(),
			                    ($responsable->getSite()) ? $responsable->getSite()->getNom() : '' ,
			                    $type,
			                    $acces,
			                    $responsable->getTelPortable(),
			                    $email,
			                    '',
			                    '<i class="fa fa-save icon-action js-save-config-client" title="Enregistrer"></i>'
		                	],
		                ];
		                $emailExist[] = $email;
		            }
				}
			}
        	/*foreach ($users as $key => $user) {
        		$rows[] = [
	                'id' => $user->id,
	                'cell' => [
	                    $user->nom,
	                    $user->prenom,
	                    $user->site,
	                    $user->type_user,
	                    $user->acces,
	                    $user->societe,
	                    $user->tel,
	                    $user->email,
	                    '',
	                    '<i class="fa fa-save icon-action js-save-config-client" title="Enregistrer"></i>'
	                ],
	            ];
        	}*/
        }

        $liste = [
            'rows' => $rows,
        ];
        return new JsonResponse($liste);
    }

    public function configDossierAction($client, $site)
    {
    	$client_id = Boost::deboost($client, $this);
    	$the_client = $this->getDoctrine()
			                ->getRepository('AppBundle:Client')
			                ->find($client_id);
        $rows = [];
        if ($the_client) {
        	if($site == 0){
            	$utilisateur = $this->getUser();
            	$dossiers = $this->getDoctrine()
			                    ->getRepository('AppBundle:Dossier')
			                    ->getUserDossier($utilisateur,$the_client,null,null,true);
        	}else{
        		$site = Boost::deboost($site, $this);
            	$site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($site);
                $dossiers = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->findBy(array('site' => $site, 'status' => 1));
        	}
        	foreach ($dossiers as $dossier) {
        		$siteNom = $dossier->getSite()->getNom();
        		$status = $dossier->getActive();
        		$statut = '';
                switch ($status){
                    case 1:
                        $statut = 'Créé';
                        break;

                    default:
                        $statut = 'En création';
                        break;
                }

                $datecloture = "";
                if(!is_null($dossier->getCloture())){
                    $datecloture = $dossier->getCloture();
                    switch ($datecloture){
                        case 1:
                            $datecloture = 'Janvier';
                            break;
                        case 2:
                            $datecloture = 'Fevrier';
                            break;
                        case 3:
                            $datecloture = 'Mars';
                            break;
                        case 4:
                            $datecloture = 'Avril';
                            break;
                        case 5:
                            $datecloture = 'Mai';
                            break;
                        case 6:
                            $datecloture = 'Juin';
                            break;
                        case 7:
                            $datecloture = 'Juillet';
                            break;
                        case 8:
                            $datecloture = 'Août';
                            break;
                        case 9:
                            $datecloture = 'Septembre';
                            break;
                        case 10:
                            $datecloture = 'Octobre';
                            break;
                        case 11:
                            $datecloture = 'Novembre';
                            break;
                        case 12:
                            $datecloture ='Decembre';
                            break;
                    }
                }

                $formeJuridique = '';
                if (!is_null($dossier->getFormeJuridique())) {
                    $formeJuridique = $dossier->getFormeJuridique()->getLibelle();
                }

                $activite = '';
                if (!is_null($dossier->getNatureActivite())) {
                    $activite = $dossier->getNatureActivite()->getLibelle();
                }

                $regimeFiscal = '';
                if (!is_null($dossier->getRegimeFiscal())) {
                    $regimeFiscal = $dossier->getRegimeFiscal()->getLibelle();
                }

                $tvaRegime = '';
                if (!is_null($dossier->getRegimeTva())) {
                    $tvaRegime = $dossier->getRegimeTva()->getLibelle();
                }

                $tvaFaitGenerateur = '';
                if(!is_null($dossier->getTvaFaitGenerateur())){
                    $tvaFaitGenerateur = $dossier->getTvaFaitGenerateur();

                    switch ($tvaFaitGenerateur){

                        case 0:
                            $tvaFaitGenerateur = "Débit";
                            break;

                        case 1:
                            $tvaFaitGenerateur = "Encaissement";
                            break;

                        case 2:
                            $tvaFaitGenerateur = "Mixte";
                            break;
                    }
                }

                $tvaPaiement = '';
                if (!is_null($dossier->getTvaMode())) {
                    $tvaPaiement = $dossier->getTvaMode();

                    switch ($tvaPaiement) {
                        case 0 :
                            $tvaPaiement = 'Accomptes semestriels';
                            break;

                        case 1 :
                            $tvaPaiement = 'Accomptes trimestriels';
                            break;

                        case 2:
                            $tvaPaiement = 'Paiement mensuels';
                            break;

                        case 3:
                            $tvaPaiement = 'Paiement trimestriels';
                            break;
                    }
                }

                $prestation = '';
                if(!is_null($dossier->getTypePrestation2())){
                    $prestation = $dossier->getTypePrestation2()->getLibelle();
                }

                $nom = '';
                $prenom = '';
                $fonction = '';
				$tel = '';
				$role = '';
                $respMandataire = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findOneBy(array('typeResponsable' => 0, 'dossier' => $dossier));

                if (!is_null($respMandataire)) {
                    if (!is_null($respMandataire->getMandataire())) {
                        $fonction = $respMandataire->getMandataire()->getLibelle();
                    }

                    if($respMandataire->getNom() != '' || $respMandataire->getPrenom() != ''){
                        $nom = $respMandataire->getNom();

                        if($respMandataire->getPrenom() != ''){
                            $prenom = $respMandataire->getPrenom();
                        }

                        if($respMandataire->getTelPortable() != ''){
                            $tel = $respMandataire->getTelPortable();
                        }
                    }
                    if(!is_null($respMandataire->getTypeResponsable())){
						switch ($respMandataire->getTypeResponsable()) {
							case 0:
								$role = 'Mandataire';
								break;
							case 1:
								$role = 'Responsable';
								break;
							case 2:
								$role = 'Secretaire, ';
								break;
							case 3:
								$role = 'Reception Image';
								break;
							case 4:
								$role = 'Manager';
								break;
							case 5:
								$role = 'Support';
								break;
						}              	
	                }
                }

                
                $mail1 = '';

                $dateCreation = '';
                if(!is_null($dossier->getDebutActivite())){
                	$dateCreation = $dossier->getDebutActivite()->format('d/m/Y');
                }


                
                $notifImage = $this->getDoctrine()
			            	       ->getRepository('AppBundle:NotificationImage')
			                       ->findBy(array('dossier' => $dossier->getId()));

			    if(count($notifImage) > 0){
			    	$email = explode(";", $notifImage[0]->getDestinataire());
			    }else{
			    	$email = [];
			    }

				$mail1 = '';
				$mail2 = '';
				if(count($email) > 0){
					$mail1 = $email[0];
					$mail2 = (count($email) > 1) ? $email[1] : '';
				}
                
                $notifDossier = $this->getDoctrine()
					            	 ->getRepository('AppBundle:NotificationDossier')
					                 ->findBy(array('dossier' => $dossier->getId()));

				$connexion = 'Non';
				$creation = 'Non';
				$envoiImg = 'Non';
				$relImg = 'Non';
				$relBq = 'Manuel';
				$relPm = 'Manuel';
				foreach ($notifDossier as $notifD) {
					$code = $notifD->getNotification()->getCode();
					switch ($code) {
						case 'CONNEXION':
							$connexion = 'Oui';
							break;
						case 'CREATION':
							$creation = 'Oui';
							break;
						case 'ENVOIE IMAGE':
							$envoiImg = 'Oui';
							break;
						case 'RELANCE IMAGE':
							$relImg = 'Oui';
							break;
						case 'BANQUE':
							$relBq = 'Automatique';
							break;
						case 'ENVOIE PM':
							$relPm = 'Automatique';
							break;
					}
				}

				$colorUser = ($mail1 == '') ? '#e95443' : '#008000';


        		$rows[] = [
	                'id' => $dossier->getId(),
	                'cell' => [
	                    $dossier->getNom(),
	                    $statut,
	                    $dateCreation,
	                    $siteNom,
	                    '<i class="fa fa-info icon-action js-caract-config-dossier"></i>',
	                    '<i class="fa fa-user icon-action js-user-config-dossier" style = "color: '.$colorUser.'"></i>',
	                    $formeJuridique,
	                    $datecloture,
	                    $activite,
	                    $regimeFiscal,
	                    $tvaRegime,
	                    $tvaFaitGenerateur,
	                    $tvaPaiement,
	                    $prestation,
	                    '',
	                    $nom,
	                    $prenom,
	                    $role,
	                    $fonction,
						$tel,
	                    $mail1,
	                    $mail2,
	                    $connexion,
	                    $creation,
	                    $envoiImg,
	                    $relImg,
	                    $relBq,
	                    $relPm,
	                    '<i class="fa fa-save icon-action js-save-config-dossier" title="Enregistrer"></i>'
	                ],
	            ];
        	}
        }
        $liste = [
            'rows' => $rows,
        ];
        return new JsonResponse($liste);
    }

    public function manuelAutoAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            $options = '<select>';
            $options .='<option></option>';
            $options .='<option value="0">Manuel</option>';
            $options .='<option value="1">Automatique</option>';
            $options .='</select>';

            return new Response($options);
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function configDossierGridEditAction(Request $request)
    {
    	if ($request->isXmlHttpRequest()) {

            $dossierId = $request->request->get('id');

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if (!is_null($dossier)) {
                $em = $this->getDoctrine()->getManager();
                $connex = $request->request->get('config-do-conex');
                $creat = $request->request->get('config-do-creat');
                $envoimg = $request->request->get('config-do-envoimg');
                $relimg = $request->request->get('config-do-relimg');
                $relbq = $request->request->get('config-do-relbq');
                $relpm = $request->request->get('config-do-relpm');

                $notifDossier = $this->getDoctrine()
					            	 ->getRepository('AppBundle:NotificationDossier')
					                 ->findBy(array('dossier' => $dossierId));

				if(count($notifDossier > 1))
				{
					foreach ($notifDossier as $notifD) {
						$em->remove($notifD);
					}
				}
				$em->flush();

                if(intval($connex)){
	                $notifConnex = $this->getDoctrine()
						            	->getRepository('AppBundle:Notification')
						                ->findBy(array('code' => 'CONNEXION'));

					$notifDossierConnex = new NotificationDossier();
		            $notifDossierConnex->setDossier($dossier)
		                         ->setNotification($notifConnex[0]);
	           		$em->persist($notifDossierConnex);
                }
				if(intval($creat)){
                	$notifCreat =  $this->getDoctrine()
						            	->getRepository('AppBundle:Notification')
						                ->findBy(array('code' => 'CREATION'));
              
					$notifDossierCreat = new NotificationDossier();
		            $notifDossierCreat->setDossier($dossier)
		                         ->setNotification($notifCreat[0]);
	           		$em->persist($notifDossierCreat);
				}
				if(intval($envoimg)){
	                $notifEnvoiImg = $this->getDoctrine()
						            	->getRepository('AppBundle:Notification')
						                ->findBy(array('code' => 'ENVOIE IMAGE'));
              
					$notifDossierEnvoiImg = new NotificationDossier();
		            $notifDossierEnvoiImg->setDossier($dossier)
		                         		->setNotification($notifEnvoiImg[0]);
	           		$em->persist($notifDossierEnvoiImg);
				}
				if(intval($relimg)){
	                $notifRelImg = $this->getDoctrine()
						            	->getRepository('AppBundle:Notification')
						                ->findBy(array('code' => 'RELANCE IMAGE'));
              
					$notifDossierRelImg = new NotificationDossier();
		            $notifDossierRelImg->setDossier($dossier)
		                         		->setNotification($notifRelImg[0]);
	           		$em->persist($notifDossierRelImg);
	           	}
				if(intval($relbq)){
	                $notifBq = $this->getDoctrine()
						            	->getRepository('AppBundle:Notification')
						                ->findBy(array('code' => 'BANQUE'));
              
					$notifDossierBq = new NotificationDossier();
		            $notifDossierBq->setDossier($dossier)
		                         		->setNotification($notifBq[0]);
	           		$em->persist($notifDossierBq);
	           	}
				if(intval($relpm)){
	                $notifEnvoiPm = $this->getDoctrine()
						            	->getRepository('AppBundle:Notification')
						                ->findBy(array('code' => 'ENVOIE PM'));
              
					$notifDossierEnvoiPm = new NotificationDossier();
		            $notifDossierEnvoiPm->setDossier($dossier)
		                         		->setNotification($notifEnvoiPm[0]);
	           		$em->persist($notifDossierEnvoiPm);
				}
				$em->flush();
            }
            return new Response(2);
        }else {
            throw new AccessDeniedException('Accès refusé');
        }
    }

    public function clientStatListAction(Request $request, $json, $client)
    {
    	if($request->isXmlHttpRequest())
        {
        	if($json){
        		$userId = $request->request->get('id');
        		$stat = $request->request->get('config-cli-stat');
                $role = $request->request->get('config-cli-role');
                $tel = $request->request->get('config-cli-tel');
                $site = $request->request->get('config-cli-site');
                $email = $request->request->get('config-cli-mail1');
                $nom = $request->request->get('config-cli-nom');
                $prenom = $request->request->get('config-cli-prnm');
	        	$typeUser = $this->getDoctrine()
				            	->getRepository('AppBundle:TypeUtilisateur')
				                ->find($stat);
	        	$roleUser = $this->getDoctrine()
				            	->getRepository('AppBundle:AccesUtilisateur')
				                ->find($role);
	        	$site = $this->getDoctrine()
			            	->getRepository('AppBundle:Site')
			                ->find($site);
		    	$em = $this->getDoctrine()->getManager();
        		if($userId != 'new_row')
                {
	        		$user = $this->getDoctrine()
				            	->getRepository('AppBundle:Utilisateur')
				                ->find($userId);
	                
				    if($user){
				    	$user->setTypeUtilisateur($typeUser);
				    	$user->setAccesUtilisateur($roleUser);
				    	$user->setTel($tel);
				    	$user->setEmail($email);
				    	$user->setLogin($email);
				    	$user->setNom($nom);
				    	$user->setPrenom($prenom);
		           		$em->persist($user);
				    }

				    $responsableCsd = $this->getDoctrine()
			                    			->getRepository('AppBundle:ResponsableCsd')
						                    ->findBy(array(
						                        'email' => $email,
						                        'typeCsd'=> 1
						                    ));
					if (count($responsableCsd) > 0) {
						$responsableCsd = $responsableCsd[0];
	                    $responsableCsd->setSite($site)
				                       ->setDossier(null)
				                       ->setEmail($email)
				                       ->setNom($nom)
				                       ->setPrenom($prenom);
		           		$em->persist($responsableCsd);
                		$em->flush();
						return new Response(2);
	                }
				}else{
					$user = $this->getDoctrine()
			                     ->getRepository('AppBundle:Utilisateur')
			                     ->findBy(array(
			                     	'email'=>$email
			                     ));

				    $responsableCsd = $this->getDoctrine()
			                    			->getRepository('AppBundle:ResponsableCsd')
						                    ->findBy(array(
						                        'email' => $email,
						                        'typeCsd'=> 1
						                    ));
			        if((count($user) > 0) && (count($responsableCsd) > 0)){
			        	return new Response(0);
			        }else if(count($user) == 0){
			        	$utilisateur = new Utilisateur();
		                $raw_password = RandomPassword::generate();
		                $encoder = $this->get('security.password_encoder');
		                $password = $encoder->encodePassword($utilisateur, $raw_password);
		                $clientId = Boost::deboost($client, $this);
				        $client = $this->getDoctrine()
			                            ->getRepository('AppBundle:Client')
			                            ->find($clientId);
		                $utilisateur
	                        ->setNom($nom)
	                        ->setPrenom($prenom)
	                        ->setEmail($email)
	                        ->setLogin($email)
	                        ->setTel($tel)
	                        ->setPassword($password)
	                        ->setAccesUtilisateur($roleUser)
	                        ->setSupprimer(false)
	                        ->setShowDossierDemo(false)
	                        ->setTypeUtilisateur($typeUser)
	                    	->setClient($client);
	                    $em->persist($utilisateur);
	                }else if(count($responsableCsd) > 0){
	                	return new Response(0);
	                }
					$titre = $this->getDoctrine()
                    			->getRepository('AppBundle:ResponsableCsdTitre')
			                    ->findBy(array(
			                        'libelle' => 'EC Associé'
			                    ));
	                $responsableCsd = new ResponsableCsd();
	                $responsableCsd->setSite($site)
	                    ->setDossier(null)
	                    ->setEmail($email)
	                    ->setNom($nom)
	                    ->setPrenom($prenom)
	                    ->setTypeResponsable(1)
	                    ->setTypeCsd(1);
	                if (count($titre) > 0) {
	                    $responsableCsd->setResponsableCsdTitre($titre[0]);
	                }
	                $em->persist($responsableCsd);
       	 			$em->flush();
					return new Response(2);
				}
        	}else{
	        	$listes = $this->getDoctrine()
			            	->getRepository('AppBundle:TypeUtilisateur')
			                ->findAll();
	            $options = '<select>';
	            $options .='<option></option>';
			    foreach ($listes as $key => $list) {
		            $options .='<option value="'.$list->getId().'">'.$list->getType().'</option>';
			    }
	            $options .='</select>';

	            return new Response($options);
        	}
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function clientSiteListAction(Request $request, $client)
    {
    	if($request->isXmlHttpRequest())
        {
	        $clientId = Boost::deboost($client, $this);
	        $client = $this->getDoctrine()
                            ->getRepository('AppBundle:Client')
                            ->find($clientId);
            $listes = $this->getDoctrine()
			            	->getRepository('AppBundle:Site')
			                ->getAllSitesByClient($client);
            $options = '<select>';
            $options .='<option></option>';
		    foreach ($listes as $key => $list) {
	            $options .='<option value="'.$list->getId().'">'.$list->getNom().'</option>';
		    }
            $options .='</select>';

            return new Response($options);
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function clientRoleListAction(Request $request)
    {
    	if($request->isXmlHttpRequest())
        {
            $listes = $this->getDoctrine()
			            	->getRepository('AppBundle:AccesUtilisateur')
			                ->findAll();
            $options = '<select>';
            $options .='<option></option>';
		    foreach ($listes as $key => $list) {
		    	if($list->getGroupe() == 'CLIENT' || $list->getGroupe() == 'SITE' || $list->getGroupe() == 'DOSSIER')
	            	$options .='<option value="'.$list->getId().'">'.$list->getLibelle().'</option>';
		    }
            $options .='</select>';

            return new Response($options);
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function getListCaractDossierAction(Request $request)
    {
    	return $this->render('DossierBundle:Banque:tableau-rapprochement.html.twig', ['datas' => $datas]);
    }

    public function updateParametreAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $client_id = Boost::deboost($request->request->get('client'), '');
        $site_id = Boost::deboost($request->request->get('site'), '');
        $field = $request->request->get('field', '');
        $value = $request->request->get('value');

        if ($field == 'DebutEnvoi') {
            if (trim($value) == '') {
                $value = NULL;
            } else {
                $value = \DateTime::createFromFormat('d/m/Y', $value);
            }
        }

        if ($field != '' && method_exists('AppBundle\Entity\NotificationImage', 'set' . $field)) {
            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
            $site = NULL;
            if ($site_id != '0') {
                $site = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->find($site_id);
            }
            $notifications = $this->getDoctrine()
                ->getRepository('AppBundle:NotificationImage')
                ->getByClient($client, $this->getUser(), $site);
            /** @var NotificationImage $notification */
            foreach ($notifications as $notification) {
                $notification->{'set' . $field}($value);
            }
        }
        $em->flush();

        $data = [
            'erreur' => FALSE,
        ];
        return new JsonResponse($data);
    }

    public function updateParametreAllEnvoiMailAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $client_id = Boost::deboost($request->request->get('client'), '');
        $site_id = Boost::deboost($request->request->get('site'), '');
        $field = $request->request->get('field', '');
        $value = $request->request->get('value');
        
        if($site_id == ''){
        	$client = $this->getDoctrine()
		                    ->getRepository('AppBundle:Client')
		                    ->find($client_id);
        	$utilisateur = $this->getUser();
        	$dossiers = $this->getDoctrine()
		                    ->getRepository('AppBundle:Dossier')
		                    ->getUserDossier($utilisateur,$client,null,null,true);
    	}else{
        	$site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($site_id);
            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->findBy(array('site' => $site, 'status' => 1));
    	}

    	$notifId = null;
    	switch ($field) {
    		case 'EnvoiConnexion':
    			$notifId = 11;
    			break;
    		case 'EnvoiCreation':
    			$notifId = 9;
    			break;
    		case 'EnvoiImage':
    			$notifId = 3;
    			break;
    		case 'EnvoiRelImage':
    			$notifId = 7;
    			break;
    		case 'EnvoiRelPm':
    			$notifId = 5;
    			break;
    		case 'EnvoiRelBq':
    			$notifId = 13;
    			break;
    	}
    	foreach ($dossiers as $dossier) {
    		$notifDossier = $this->getDoctrine()
				            	 ->getRepository('AppBundle:NotificationDossier')
				                 ->findBy(array(
					                 	'dossier' => $dossier->getId(),
					                    'notification' => $notifId
				                	));
			if(!$value) {
				foreach ($notifDossier as $notifD) {
					$em->remove($notifD);
				}
			}else{
				if(count($notifDossier) == 0) {
					$notifEnvoiImg = $this->getDoctrine()
					            	  ->getRepository('AppBundle:Notification')
					                  ->find($notifId);
              
					$notifDossierEnvoiImg = new NotificationDossier();
		            $notifDossierEnvoiImg->setDossier($dossier)
		                         		 ->setNotification($notifEnvoiImg);
	           		$em->persist($notifDossierEnvoiImg);
				}
			}
			$em->flush();
    	}
		$data = [
            'erreur' => FALSE,
        ];
        return new JsonResponse($data);
    }

    public function getHtmlPersoJourFixeAction(Request $request)
    {
    	$index = $request->request->get('index');
    	$value = $request->request->get('value');
    	$type = $request->request->get('type');
    	$fin = $request->request->get('fin');
    	$recur = $request->request->get('recur');
    	return $this->render('DossierBundle:Administration:form-personnalisation-jour-fixe.html.twig', array(
			'index' => $index,
			'valueTousLes' => $value,
			'type' => $type,
			'fin' => $fin,
			'recur' => $recur
		));
    }

    public function listParametreAutresPmAction($client, $site)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $rows = [];
        if ($the_client) {
            if ($site == '0') {
                $the_site = NULL;
            } else {
                $site_id = Boost::deboost($site, $this);
                $the_site = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->find($site_id);
            }
            $notifications = $this->getDoctrine()
				                  ->getRepository('AppBundle:NotificationAutresPm')
				                  ->getByClient($the_client, $this->getUser(), $the_site);

            /** @var \AppBundle\Entity\NotificationPm $notification */
            foreach ($notifications as $notification) {
                $contenu = "";
                if ($notification->getContenu()) {
                    if (strlen($notification->getContenu()) >= 50) {
                        $contenu = substr($notification->getContenu(), 0, 50) . ' ...';
                    } else {
                        $contenu = $notification->getContenu();
                    }
                }

                $dossier = $notification->getDossier();

                $status = '';
                switch ($dossier->getStatus()){
                    case 1:
                        $status = 'Actif';
                        break;
                    case 2:
                        $status = 'Suspendu';
                        if($dossier->getStatusDebut() !== null){
                            $status .= ' - '.$dossier->getStatusDebut();
                        }
                        break;
                    case 3:
                        $status = 'Radié';
                        if($dossier->getStatusDebut() !== null){
                            $status .= ' - '.$dossier->getStatusDebut();
                        }
                        break;
                }

                $notifDossier = $this->getDoctrine()
					            	 ->getRepository('AppBundle:NotificationDossier')
					                 ->findBy(array('dossier' => $dossier->getId()));
				$code = 'Manuel';
				if(count($notifDossier) > 0){
					foreach ($notifDossier as $notifD) {
						if($notifD->getNotification()->getCode() == 'ENVOIE PM')
							$code = 'Automatique';
					}
				}

                $rows[] = [
                    'id' => $dossier->getId().'-'.$notification->getId(),
                    'cell' => [
                        $dossier->getNom(),
                        $status,
                        $notification->getDestinataire(),
                        $notification->getCopie(),
                        $notification->getTitreContact(),
                        $notification->getNomContact(),
                        $contenu,
                        $code,
                        $notification->getContenu(),
                        $notification->getObjet(),
                        $dossier->getStatusDebut()
                    ],
                ];
            }
        }

        $liste = [
            'rows' => $rows,
        ];

        return new JsonResponse($liste);
    }
}