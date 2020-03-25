<?php

namespace UtilisateurBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\AccesUtilisateur;
use AppBundle\Entity\Client;
use AppBundle\Entity\MenuParRole;
use AppBundle\Entity\MenuUtilisateur;
use AppBundle\Entity\ResponsableCsd;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurClient;
use AppBundle\Entity\UtilisateurDossier;
use AppBundle\Entity\UtilisateurSite;
use AppBundle\Functions\FormUtility;
use AppBundle\Security\RandomPassword;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Email;
use UtilisateurBundle\Form\RegisterType;
use UtilisateurBundle\Form\RegisterUpdateType;

class GestionController extends Controller
{
    /**
     * Création d'un utilisateur: index
     *
     * @return Response
     */
    public function creationAction()
    {

        $default_data = array();
        $form = $this->createForm(RegisterType::class, $default_data, array(
            'action' => $this->generateUrl('user_register_add'),
            'attr' => array('id' => 'user-register-form'),
        ));

        if ($this->isGranted('ROLE_CLIENT_ADMIN')) {
            if ($this->isGranted('ROLE_SCRIPTURA_ADMIN')) {
                $acces = $this->getDoctrine()
                    ->getRepository('AppBundle:AccesUtilisateur')
                    ->getAllAcces();
            } else {
                $acces = $this->getDoctrine()
                    ->getRepository('AppBundle:AccesUtilisateur')
                    ->getAllAccesForAll();
            }

            return $this->render('@Utilisateur/Gestion/creation.html.twig', array(
                'acces' => $acces,
                'form' => $form->createView(),
            ));
        } else {
            throw new AccessDeniedHttpException('Accès refusé.');
        }

    }

    /**
     * Ajouter un utilisateur
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $form = $this->createForm(RegisterType::class);

            $form->handleRequest($request);

            $error_data = array();
            $user_role = $request->request->get('user_role', '');
            $client_id = $request->request->get('client', '');
            $client_multi = $request->request->get('client_multi', '');
            $sites = $request->request->get('site', '');
            $dossiers = $request->request->get('dossier', '');
            $supprimer = $request->request->get('user_actif') == '0' ? true : false;
            $show_demo = $request->request->get('user_show_demo') == '1' ? true : false;

            $user_type = $request->request->get('user_type', '');


            if ($user_role == '' || !$form->isValid()) {
                if ($user_role == '') {
                    $error_data = ['user_role' => ['Choisissez un rôle']];
                }

                if (!$form->isValid()) {
                    $error_data = array_merge($error_data, FormUtility::getErrorMessages($form));
                    $data = array(
                        'is_form_valid' => false,
                        'message' => 'Formulaire invalide',
                        'error_data' => $error_data,
                        'client' => $client_id,
                    );

                    return new JsonResponse(json_encode($data));
                }
            } else {
                $em = $this->getDoctrine()
                    ->getManager();
                $utilisateur = new Utilisateur();
                $raw_password = RandomPassword::generate();
                $encoder = $this->get('security.password_encoder');
                $password = $encoder->encodePassword($utilisateur, $raw_password);

                $nom = $form["nom"]->getData();
                $prenom = $form["prenom"]->getData();
                $email = $form["email"]->getData();
                $societe = $form["societe"]->getData();
                $telephone = $form["telephone"]->getData();
                $skype = $form["skype"]->getData();
                $acces_utilisateur = $this->getDoctrine()
                    ->getRepository('AppBundle:AccesUtilisateur')
                    ->find($user_role);
                if ($acces_utilisateur) {
                    $role_type = $acces_utilisateur->getType();

                    /* Enregistrement de l'utilisateur */
                    $utilisateur
                        ->setNom($nom)
                        ->setPrenom($prenom)
                        ->setEmail($email)
                        ->setLogin($email)
                        ->setSociete($societe)
                        ->setTel($telephone)
                        ->setSkype($skype)
                        ->setPassword($password)
                        ->setAccesUtilisateur($acces_utilisateur)
                        ->setSupprimer($supprimer)
                        ->setShowDossierDemo($show_demo)
                        ->setType($user_type);

                    if ($client_id != '') {
                        $client = $this->getDoctrine()
                            ->getRepository('AppBundle:Client')
                            ->find($client_id);
                        if ($client) {
                            $utilisateur->setClient($client);
                        }
                    }
                    $em->persist($utilisateur);

                    if ($role_type == 2) {
                        /* Si utilisateur scriptura */
                        $scriptura = $this->getDoctrine()
                            ->getRepository('AppBundle:Client')
                            ->findOneBy(array(
                                'nom' => 'SCRIPTURA',
                            ));
                        if ($scriptura) {
                            $utilisateur->setClient($scriptura);
                        }

                        /* Enregistrer liste Clients de l'utilisateur */
                        $clients_multi_id = explode(',', $client_multi);

                        foreach ($clients_multi_id as $item) {
                            /* Chercher le client */
                            $selected_client = $this->getDoctrine()
                                ->getRepository('AppBundle:Client')
                                ->find($item);
                            if ($selected_client) {

                                $utilisateur_client = new UtilisateurClient();
                                $utilisateur_client
                                    ->setClient($selected_client)
                                    ->setUtilisateur($utilisateur);
                                $em->persist($utilisateur_client);

                            }
                        }
                    } elseif ($role_type == 3) {
                        /* Si utilisateur client */

                    } elseif ($role_type == 4) {
                        /* Si utilisateur site */
                        $sites_multi_id = explode(',', $sites);
                        foreach ($sites_multi_id as $item) {
                            /* Chercher le site */
                            $selected_site = $this->getDoctrine()
                                ->getRepository('AppBundle:Site')
                                ->find($item);
                            if ($selected_site) {
                                $utilisateur_site = new UtilisateurSite();
                                $utilisateur_site
                                    ->setSite($selected_site)
                                    ->setUtilisateur($utilisateur);
                                $em->persist($utilisateur_site);
                            }
                        }
                    } elseif ($role_type == 5 || $role_type == 6) {
                        /* Si utilisateur dossier ou client final */
                        $dossiers_multi_id = explode(',', $dossiers);
                        foreach ($dossiers_multi_id as $item) {
                            /* Chercher le dossier */
                            $selected_dossier = $this->getDoctrine()
                                ->getRepository('AppBundle:Dossier')
                                ->find($item);
                            if ($selected_dossier) {
                                $utilisateur_dossier = new UtilisateurDossier();
                                $utilisateur_dossier
                                    ->setDossier($selected_dossier)
                                    ->setUtilisateur($utilisateur);
                                $em->persist($utilisateur_dossier);

                                if (!$supprimer) {
                                    /* Ajouter responsable dossier */
                                    $this->getDoctrine()
                                        ->getRepository('AppBundle:ResponsableCsd')
                                        ->addResponsableDossier($selected_dossier, $utilisateur);
                                }
                            }
                        }

                    }

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
                        ->setSubject("Connexion au site")
                        ->setFrom($from_details['address'], $from_details['label'])
                        ->setTo($email)
                        ->setBcc('support@scriptura.biz')
                        ->addBcc('arq@scriptura.biz')
                        ->addBcc('philcastellan@gmail.com')
                        ->addBcc('pjlcastellan@gmail.com');

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
                            $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email-esseca.html.twig', array(
                                'utilisateur' => $utilisateur,
                                'token' => Boost::boost($utilisateur->getEmail()),
                                'reply_to' => $this->getReplyTo($utilisateur->getClient()),
                                'client' => $utilisateur->getClient(),
                                'raw_password' => $raw_password,
                            ))
                            , 'text/html');
//                        $message->addBcc('v.sarhadian@esseca.com');
                    } elseif ($utilisateur->getClient()->getNom() == 'EXPERTS_EXPANSION') {
                        $message->setBody(
                            $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email-experts_expansion.html.twig', array(
                                'utilisateur' => $utilisateur,
                                'token' => Boost::boost($utilisateur->getEmail()),
                                'reply_to' => $this->getReplyTo($utilisateur->getClient()),
                                'client' => $utilisateur->getClient(),
                                'raw_password' => $raw_password,
                                'support_email' => 'mailto:support@expertcontact.fr'
                            ))
                            , 'text/html');
//                        $message->addBcc('vreboul@expertcontact.fr');
                    }
                    elseif ($utilisateur->getClient()->getNom() == 'NAULIER_ASSOCIES'){

                        $message->setBody(
                            $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email-naulier.html.twig', array(
                                'utilisateur' => $utilisateur,
                                'token' => Boost::boost($utilisateur->getEmail()),
                                'reply_to' => $this->getReplyTo($utilisateur->getClient()),
                                'client' => $utilisateur->getClient(),
                                'raw_password' => $raw_password,
                                'support_email' => 'mailto:support@lesexperts.biz'
                            ))
                            , 'text/html');
                    }
                    elseif ($utilisateur->getClient()->getNom() == 'AVEC' ||
                        $utilisateur->getClient()->getId() === 776){

                        $message->setBody(
                            $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email-avec.html.twig', array(
                                'utilisateur' => $utilisateur,
                                'token' => Boost::boost($utilisateur->getEmail()),
                                'reply_to' => $this->getReplyTo($utilisateur->getClient()),
                                'client' => $utilisateur->getClient(),
                                'raw_password' => $raw_password,
                                'support_email' => 'mailto:support@lesexperts.biz'
                            ))
                            , 'text/html');
                    }
                    elseif ($utilisateur->getClient()->getNom() == 'BHN'){
                        $message->setBody(
                            $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email-bhn.hmtl.twig', array(
                                'utilisateur' => $utilisateur,
                                'token' => Boost::boost($utilisateur->getEmail()),
                                'reply_to' => $this->getReplyTo($utilisateur->getClient()),
                                'client' => $utilisateur->getClient(),
                                'raw_password' => $raw_password,
                                'support_email' => 'mailto:support.bhn@bhn-expertise.com'
                            ))
                            , 'text/html');
                    }
                    else {
                        $message->setBody(
                            $this->renderView('UtilisateurBundle:Emails:nouvel-utilisateur-email.html.twig', array(
                                'utilisateur' => $utilisateur,
                                'token' => Boost::boost($utilisateur->getEmail()),
                                'reply_to' => $this->getReplyTo($utilisateur->getClient()),
                                'client' => $utilisateur->getClient(),
                                'raw_password' => $raw_password,
                                'support_email' => 'mailto:support@lesexperts.biz'
                            ))
                            , 'text/html');
                    }

                    /** GET MAILER PAR CLIENT */
                    $this->get('app.mailer_par_client')
                        ->getMailer($utilisateur->getClient())
                        ->send($message);

                    $data = array(
                        'is_form_valid' => true,
                        'message' => "Utilisateur créé avec succès. Un email a été envoyé à <strong>$email</strong> qui contient les étapes à suivre pour activer le nouveau compte.",
                    );

                    return new JsonResponse(json_encode($data));
                } else {
                    throw new NotFoundHttpException("Rôle introuvable");
                }
            }
        } else {
            throw new AccessDeniedHttpException('Accès refusé.');
        }
        throw new AccessDeniedHttpException('Accès refusé.');
    }

    public function resendMailCreationAction($user)
    {
        $user_id = Boost::deboost($user, $this);
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
                ->setSubject("Connexion au site")
                ->setFrom($from_details['address'], $from_details['label'])
                ->setTo($utilisateur->getEmail())
                ->setBcc('support@scriptura.biz')
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

            $data = [
                'message' => "L'email a été re-envoyé à <strong>" . $utilisateur->getEmail() . "</strong> qui contient les étapes à suivre pour activer le nouveau compte.",
            ];

            return new JsonResponse(json_encode($data));
        } else {
            throw new NotFoundHttpException("Utilisateur introuvable.");
        }
    }


    /**
     * Niveau d'accès des utilisateurs: index
     *
     * @return Response
     */
    public function NiveauAccesAction()
    {
        if ($this->isGranted('ROLE_CLIENT_RESP')) {
            if ($this->isGranted('ROLE_SCRIPTURA_ADMIN')) {
                $acces = $this->getDoctrine()
                    ->getRepository('AppBundle:AccesUtilisateur')
                    ->getAllAcces();
            } else {
                $acces = $this->getDoctrine()
                    ->getRepository('AppBundle:AccesUtilisateur')
                    ->getAllAccesForAll();
            }

            $default_data = array();
            $form = $this->createForm(RegisterUpdateType::class, $default_data, array(
                'attr' => array('id' => 'user-register-form'),
            ));

            $user = $this->getUser();

            return $this->render('@Utilisateur/Gestion/niveau-acces.html.twig', array(
                'acces' => $acces,
                'form' => $form->createView(),
                'user' => $user
            ));
        } else {
            throw new AccessDeniedHttpException('Accès refusé.');
        }
    }

    /**
     * Liste des utilisateurs d'un client selectionné
     *
     * @param $client
     * @return JsonResponse
     */
    public function clientUsersAction($client)
    {
        if ($this->isGranted('ROLE_SCRIPTURA_ADMIN')) {
            $client_id = Boost::deboost($client, $this);
            $the_client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
            if ($the_client) {
                $users = $this->getDoctrine()
                    ->getRepository('AppBundle:Utilisateur')
                    ->getClientUsers($the_client);

                foreach ($users as &$item) {
                    /** @var \AppBundle\Entity\Utilisateur $user */
                    $user = $item[0];
                    $user->setClient(null);
                    $user->setLastLogin(null);
                    $user->setPassword(null);
                    if ($user->getClients()) {
                        foreach ($user->getClients() as $tmp_client) {
                            $user->removeClient($tmp_client);
                        }
                    }
                    if ($user->getSites()) {
                        foreach ($user->getSites() as $tmp_site) {
                            $user->removeSite($tmp_site);
                        }
                    }
                    if ($user->getDossiers()) {
                        foreach ($user->getDossiers() as $tmp_dossier) {
                            $user->removeDossier($tmp_dossier);
                        }
                    }
                }

                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();

                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });

                $serializer = new Serializer(array($normalizer), array($encoder));

                return new JsonResponse($serializer->serialize($users, 'json'));
            } else {
                throw new NotFoundHttpException('Client introuvable.');
            }
        } else {
            /** @var Utilisateur $user */
            $user = $this->getUser();
            $the_client = $user->getClient();
            if ($the_client) {
                $users = $this->getDoctrine()
                    ->getRepository('AppBundle:Utilisateur')
                    ->getClientUsers($the_client);

                foreach ($users as &$item) {
                    /** @var \AppBundle\Entity\Utilisateur $user */
                    $user = $item[0];
                    $user->setClient(null);
                    $user->setLastLogin(null);
                    $user->setPassword(null);
                    if ($user->getClients()) {
                        foreach ($user->getClients() as $tmp_client) {
                            $user->removeClient($tmp_client);
                        }
                    }
                    if ($user->getSites()) {
                        foreach ($user->getSites() as $tmp_site) {
                            $user->removeSite($tmp_site);
                        }
                    }
                    if ($user->getDossiers()) {
                        foreach ($user->getDossiers() as $tmp_dossier) {
                            $user->removeDossier($tmp_dossier);
                        }
                    }
                }

                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();

                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });

                $serializer = new Serializer(array($normalizer), array($encoder));

                return new JsonResponse($serializer->serialize($users, 'json'));
            } else {
                throw new NotFoundHttpException('Client introuvable.');
            }
        }
    }

    /**
     * Rôle et niveau d'accès d'un utilisateur
     *
     * @param Request $request
     * @param $user
     * @return JsonResponse
     */
    public function userRoleAndAccesAction(Request $request, $user)
    {
        if ($request->isXmlHttpRequest()) {
            $user_id = Boost::deboost($user, $this);
            $utilisateur = $this->getDoctrine()
                ->getRepository('AppBundle:Utilisateur')
                ->find($user_id);
            if ($utilisateur) {
                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();

                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });
                $type_user = $this->getUser()->getAccesUtilisateur()->getType();
                $data = [
                    'currentUserType' => $type_user,
                    'utilisateur' => $utilisateur
                ];
                $serializer = new Serializer(array($normalizer), array($encoder));
                return new JsonResponse($serializer->serialize($data, 'json'));
            } else {
                throw new NotFoundHttpException("Utilisateur introuvable.");
            }
        } else {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    /**
     * Modification du role et Accès d'un utilisateur
     *
     * @param Request $request
     * @param $user
     * @return JsonResponse
     */
    public function userRoleAndAccesEditAction(Request $request, $user)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $form = $this->createForm(RegisterUpdateType::class);

            $form->handleRequest($request);

            $error_data = array();
            $user_role = $request->request->get('user_role', '');
            $client_id = $request->request->get('client', '');

            if ($user_role == '' || !$form->isValid()) {
                if ($user_role == '') {
                    $error_data = ['user_role' => ['Choisissez un rôle']];
                }

                if (!$form->isValid()) {
                    $error_data = array_merge($error_data, FormUtility::getErrorMessages($form));
                    $data = array(
                        'is_form_valid' => false,
                        'message' => 'Formulaire invalide',
                        'error_data' => $error_data,
                        'client' => $client_id,
                    );

                    return new JsonResponse(json_encode($data));
                }
            } else {
                try {
                    $user_id = Boost::deboost($user, $this);
                    /** @var Utilisateur $utilisateur */
                    $utilisateur = $this->getDoctrine()
                        ->getRepository('AppBundle:Utilisateur')
                        ->find($user_id);
                    if ($utilisateur) {
                        $em = $this->getDoctrine()
                            ->getManager();
                        $nom = $form["nom"]->getData();
                        $prenom = $form["prenom"]->getData();
                        $email = $form["email"]->getData();
                        $societe = $form["societe"]->getData();
                        $telephone = $form["telephone"]->getData();
                        $skype = $form["skype"]->getData();

                        $user_role = $request->request->get('user_role', '');
                        $client_id = $request->request->get('client', '');
                        $client_multi = $request->request->get('client_multi', NULL);
                        $sites = $request->request->get('sites', NULL);
                        $dossiers = $request->request->get('dossiers', NULL);
                        $supprimer = $request->request->get('user_actif') == '0' ? TRUE : FALSE;
                        $show_demo = $request->request->get('user_show_demo') == '1' ? TRUE : FALSE;

                        $user_type = $request->request->get('user_type', '');

                        $acces_utilisateur = $this->getDoctrine()
                            ->getRepository('AppBundle:AccesUtilisateur')
                            ->find($user_role);
                        if ($acces_utilisateur) {
                            $role_type = $acces_utilisateur->getType();

                            /* Enregistrement de l'utilisateur */
                            $utilisateur
                                ->setNom($nom)
                                ->setPrenom($prenom)
                                ->setEmail($email)
                                ->setLogin($email)
                                ->setSociete($societe)
                                ->setTel($telephone)
                                ->setSkype($skype)
                                ->setAccesUtilisateur($acces_utilisateur)
                                ->setSupprimer($supprimer)
                                ->setShowDossierDemo($show_demo)
                                ->setType($user_type);

                            if ($client_id != '') {
                                $client = $this->getDoctrine()
                                    ->getRepository('AppBundle:Client')
                                    ->find($client_id);
                                if ($client) {
                                    $utilisateur->setClient($client);
                                }
                            }
                            $em->persist($utilisateur);

                            $this->getDoctrine()
                                ->getRepository('AppBundle:UtilisateurClient')
                                ->removeUserClients($utilisateur);
                            $this->getDoctrine()
                                ->getRepository('AppBundle:UtilisateurSite')
                                ->removeUserSites($utilisateur);
                            $this->getDoctrine()
                                ->getRepository('AppBundle:UtilisateurDossier')
                                ->removeUserDossiers($utilisateur);
                            if ($role_type == 2) {
                                /* Si utilisateur scriptura */
                                $scriptura = $this->getDoctrine()
                                    ->getRepository('AppBundle:Client')
                                    ->findOneBy(array(
                                        'nom' => 'SCRIPTURA',
                                    ));
                                if ($scriptura) {
                                    $utilisateur->setClient($scriptura);
                                }
                                /* Enregistrer Clients de l'utilisateur */
                                if ($client_multi && $client_multi != '') {
                                    $clients_multi_id = explode(',', $client_multi);
                                    foreach ($clients_multi_id as $item) {
                                        /* Chercher le client */
                                        $selected_client = $this->getDoctrine()
                                            ->getRepository('AppBundle:Client')
                                            ->find($item);
                                        if ($selected_client) {

                                            $utilisateur_client = new UtilisateurClient();
                                            $utilisateur_client
                                                ->setClient($selected_client)
                                                ->setUtilisateur($utilisateur);
                                            $em->persist($utilisateur_client);
                                        }
                                    }
                                }
                            } elseif ($role_type == 3) {
                                /* Si utilisateur client */

                            } elseif ($role_type == 4) {
                                /* Si utilisateur site */
                                if ($sites && $sites != '') {
                                    $sites_multi_id = explode(',', $sites);
                                    foreach ($sites_multi_id as $item) {
                                        /* Chercher le site */
                                        $selected_site = $this->getDoctrine()
                                            ->getRepository('AppBundle:Site')
                                            ->find($item);
                                        if ($selected_site) {
                                            $utilisateur_site = new UtilisateurSite();
                                            $utilisateur_site
                                                ->setSite($selected_site)
                                                ->setUtilisateur($utilisateur);
                                            $em->persist($utilisateur_site);
                                        }
                                    }
                                }
                            } elseif ($role_type == 5 || $role_type == 6) {
                                /* Si utilisateur dossier ou client final */
                                if ($dossiers && $dossiers != '') {
                                    $dossiers_multi_id = explode(',', $dossiers);
                                    foreach ($dossiers_multi_id as $item) {
                                        /* Chercher le dossier */
                                        $selected_dossier = $this->getDoctrine()
                                            ->getRepository('AppBundle:Dossier')
                                            ->find($item);
                                        if ($selected_dossier) {
                                            $utilisateur_dossier = new UtilisateurDossier();
                                            $utilisateur_dossier
                                                ->setDossier($selected_dossier)
                                                ->setUtilisateur($utilisateur);
                                            $em->persist($utilisateur_dossier);

                                            /* Ajouter ou supprimer ResponsableCsd */
                                            if (!$supprimer) {
                                                $this->getDoctrine()
                                                    ->getRepository('AppBundle:ResponsableCsd')
                                                    ->addResponsableDossier($selected_dossier, $utilisateur);
                                            } else {
                                                $this->getDoctrine()
                                                    ->getRepository('AppBundle:ResponsableCsd')
                                                    ->removeResponsableDossier($selected_dossier, $utilisateur);
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $em->flush();

                        $encoder = new JsonEncoder();
                        $normalizer = new ObjectNormalizer();
                        $normalizer->setCircularReferenceHandler(function ($object) {
                            return $object->getId();
                        });
                        $serializer = new Serializer([$normalizer], [$encoder]);
                        $type_user = $this->getUser()->getAccesUtilisateur()->getType();

                        $data = [
                            'erreur' => FALSE,
                            'is_form_valid' => TRUE,
                            'utilisateur' => $utilisateur,
                            'currentUserType' => $type_user,
                        ];
                        return new JsonResponse($serializer->serialize($data, 'json'));
                    } else {
                        throw new NotFoundHttpException('Utilisateur introuvable.');
                    }
                } catch (\Exception $ex) {
                    $encoder = new JsonEncoder();
                    $normalizer = new ObjectNormalizer();
                    $normalizer->setCircularReferenceHandler(function ($object) {
                        return $object->getId();
                    });
                    $serializer = new Serializer([$normalizer], [$encoder]);

                    $pos = strpos($ex->getMessage(), 'email_UNIQUE');
                    if ($pos && $pos >= 0) {
                        $error_data = ['email' => ['Cet email est déjà utilisé.']];
                        $data = array(
                            'is_form_valid' => false,
                            'message' => 'Formulaire invalide',
                            'error_data' => $error_data,
                            'client' => $client_id,
                        );

                        return new JsonResponse(json_encode($data));
                    }
                    $erreur_text = "Une erreur est survenue lors de la tentative de mise à jour.";
                    $data = [
                        'erreur' => TRUE,
                        'is_form_valid' => TRUE,
                        'erreur_text' => $erreur_text,
                    ];
                    return new JsonResponse($serializer->serialize($data, 'json'));
                }
            }
        }
        throw new AccessDeniedHttpException('Accès refusé.');
    }

    /**
     * Accès aux menu par rôle et par utilisateur: index
     *
     * @return Response
     */
    public function AccesMenuAction()
    {
        $roles = $this->getDoctrine()
            ->getRepository('AppBundle:AccesUtilisateur')
            ->getAllAcces();
        $users = [];
        if ($this->isGranted('ROLE_CLIENT_ADMIN')) {
            /** @var Utilisateur $utilisateur */
            $utilisateur = $this->getUser();
            $users = $this->getDoctrine()
                ->getRepository('AppBundle:Utilisateur')
                ->getClientUsers($utilisateur->getClient());
        }
        return $this->render('@Utilisateur/Gestion/acces-menu.html.twig', array(
            'utilisateur' => $this->getUser(),
            'roles' => $roles,
            'users' => $users,
        ));
    }

    /**
     * Liste menu pour un rôle selectionné
     *
     * @param Request $request
     * @param AccesUtilisateur $role
     * @return Response
     */
    public function roleMenuAction(Request $request, AccesUtilisateur $role)
    {
        if ($request->isXmlHttpRequest()) {
            $menus = $this->getDoctrine()
                ->getRepository('AppBundle:MenuParRole')
                ->getMenuParRole($role);

            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });
            $serializer = new Serializer(array($normalizer), array($encoder));
            return new Response($serializer->serialize($menus, 'json'));
        } else {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    /**
     * Modification liste menu pour un rôle
     *
     * @param Request $request
     * @param AccesUtilisateur $role
     * @return JsonResponse
     */
    public function roleMenuEditAction(Request $request, AccesUtilisateur $role)
    {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('POST')) {
                try {
                    $menus_id = $request->request->get('menus');
                    $this->getDoctrine()
                        ->getRepository('AppBundle:MenuParRole')
                        ->removeRoleMenus($role);
                    if ($menus_id && is_array($menus_id)) {
                        $em = $this->getDoctrine()
                            ->getManager();
                        foreach ($menus_id as $menu_id) {
                            $menu = $this->getDoctrine()
                                ->getRepository('AppBundle:Menu')
                                ->find($menu_id['menu']);
                            if ($menu) {
                                $role_menu = new MenuParRole();
                                $role_menu
                                    ->setAccesUtilisateur($role)
                                    ->setMenu($menu)
                                    ->setCanEdit($menu_id['can_edit'] == '1' ? true : false);
                                $em->persist($role_menu);
                            }
                        }
                        $em->flush();
                    }
                    $menus = $this->getDoctrine()
                        ->getRepository('AppBundle:MenuParRole')
                        ->getMenuParRole($role);

                    $encoder = new JsonEncoder();
                    $normalizer = new ObjectNormalizer();
                    $normalizer->setCircularReferenceHandler(function ($object) {
                        return $object->getId();
                    });
                    $serializer = new Serializer(array($normalizer), array($encoder));

                    $data = [
                        'erreur' => false,
                        'menus' => $menus,
                    ];
                    return new JsonResponse($serializer->serialize($data, 'json'));
                } catch (\Exception $ex) {
                    $data = [
                        'erreur' => true,
                        'erreur_text' => "Une erreur est survenue.",
                    ];
                    return new JsonResponse(json_encode($data));
                }
            } else {
                throw new AccessDeniedHttpException('Accès refusé.');
            }
        } else {
            throw new AccessDeniedHttpException('Accès refusé.');
        }
    }

    /**
     * Liste menu pour un utilisateur
     *
     * @param Request $request
     * @param $user
     * @return Response
     */
    public function userMenuAction(Request $request, $user)
    {
        if ($request->isXmlHttpRequest()) {
            $user_id = Boost::deboost($user, $this);
            $utilisateur = $this->getDoctrine()
                ->getRepository('AppBundle:Utilisateur')
                ->find($user_id);
            if ($utilisateur) {
                $menus = $this->getDoctrine()
                    ->getRepository('AppBundle:MenuUtilisateur')
                    ->getMenuUtilisateur($utilisateur);

                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();
                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });
                $serializer = new Serializer(array($normalizer), array($encoder));
                $data = $serializer->serialize($menus, 'json');
                return new Response($data);
            } else {
                throw new NotFoundHttpException("Utilisateur introuvable.");
            }
        } else {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    /**
     * Modification liste menu pour un utilisateur
     *
     * @param Request $request
     * @param $user
     * @param $default : utiliser les paramètres pour le role de l'utilisateur
     * @return JsonResponse
     */
    public function userMenuEditAction(Request $request, $user, $default)
    {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('POST')) {
                try {
                    $user_id = Boost::deboost($user, $this);
                    $utilisateur = $this->getDoctrine()
                        ->getRepository('AppBundle:Utilisateur')
                        ->find($user_id);
                    if ($utilisateur) {
                        $menus_id = $request->request->get('menus');
                        $this->getDoctrine()
                            ->getRepository('AppBundle:MenuUtilisateur')
                            ->removeMenuUtilisateur($utilisateur);
                        $use_default = $default == 1 ? true : false;
                        if (!$use_default) {
                            if ($menus_id && is_array($menus_id)) {
                                $em = $this->getDoctrine()
                                    ->getManager();
                                foreach ($menus_id as $menu_id) {
                                    $menu = $this->getDoctrine()
                                        ->getRepository('AppBundle:Menu')
                                        ->find($menu_id['menu']);
                                    if ($menu) {
                                        $utilisateur_menu = new MenuUtilisateur();
                                        $utilisateur_menu
                                            ->setUtilisateur($utilisateur)
                                            ->setMenu($menu)
                                            ->setCanEdit($menu_id['can_edit'] == '1' ? true : false);
                                        $em->persist($utilisateur_menu);
                                    }
                                }
                                $em->flush();
                            }
                        }
                        $menus = $this->getDoctrine()
                            ->getRepository('AppBundle:MenuUtilisateur')
                            ->getMenuUtilisateur($utilisateur);

                        $encoder = new JsonEncoder();
                        $normalizer = new ObjectNormalizer();
                        $normalizer->setCircularReferenceHandler(function ($object) {
                            return $object->getId();
                        });
                        $serializer = new Serializer(array($normalizer), array($encoder));

                        $data = [
                            'erreur' => false,
                            'menus' => $menus,
                        ];
                        return new JsonResponse($serializer->serialize($data, 'json'));
                    } else {
                        throw new NotFoundHttpException("Utilisateur introuvable.");
                    }
                } catch (\Exception $ex) {
                    $data = [
                        'erreur' => true,
                        'erreur_text' => "Une erreur est survenue.",
                    ];
                    return new JsonResponse(json_encode($data));
                }
            } else {
                throw new AccessDeniedHttpException('Accès refusé.');
            }
        } else {
            throw new AccessDeniedHttpException('Accès refusé.');
        }
    }

    private function getFromDetails(Client $client)
    {
        $label = "Pour vous connecter";

        return ['address' => $this->getReplyTo($client, true), 'label' => $label];
    }

    private function getReplyTo(Client $client, $isFrom = false)
    {
        if ($isFrom) {
            $nomclient = ($client ? str_replace('_', '', strtolower($client->getNom())) : 'support');
            $nomclient = str_replace("'", '', $nomclient);
            $nomclient = str_replace("-", '', $nomclient);
            $reply_to = $nomclient . '@lesexperts.biz';

            //REVITEC
            if($client->getId() === 810){
                $reply_to = $nomclient . '@comptaetgestion.fr';
            }

        } else {
            $reply_to = "support@lesexperts.biz";
        }

        $smtp = $this->getDoctrine()
            ->getRepository('AppBundle:Smtp')
            ->findOneBy(array(
                'client' => $client
            ));
        if ($smtp) {
            $reply_to = $smtp->getLogin();
        }
        return $reply_to;
    }
}
