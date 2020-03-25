<?php

namespace UtilisateurBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Client;
use AppBundle\Entity\LogActivite;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;
use UtilisateurBundle\Form\PasswordChangeType;
use UtilisateurBundle\Form\PasswordRequestType;

class SecurityController extends Controller
{
    /**
     * Connexion principale par email + mot de passe
     *
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        return $this->render('UtilisateurBundle:Security:login.html.twig', array(
            "last_username" => $session->get(Security::LAST_USERNAME),
            "error" => $error,
        ));
    }

    /**
     * Modification mot de passe
     * Lors de la première connexion d'un utilisateur
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function firstLoginAction(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $default_data = array();
            $form = $this->createForm(PasswordChangeType::class, $default_data);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $password = $_POST['password_change']['password']['first'];
                $em = $this->getDoctrine()
                    ->getManager();
                /* @var Utilisateur $user */
                $user = $this->getUser();
                $encoder = $this->get('security.password_encoder');

                /* Mise à jour du mot de passe */
                $encoded_password = $encoder->encodePassword($user, $password);
                $user
                    ->setPassword($encoded_password)
                    ->setLastLogin(new \DateTime());
                $em->flush();

                /* Déconnecter l'utilisateur et effacer les cookies */
                $this->get('security.token_storage')->setToken(null);
                $this->get('session')->invalidate();

                /* Rediriger vers la page succès */
                $response = $this->render('@Utilisateur/Security/first-password-changed.html.twig');

                /* Supprimer les cookies de connexion */
                $user_cookies = [
                    $this->getParameter('session.name'),
                    $this->getParameter('session.remember_me.name'),
                ];
                foreach ($user_cookies as $user_cookie) {
                    $response->headers->clearCookie($user_cookie);
                }

                return $response;
            }
            return $this->render('@Utilisateur/Security/first-login.html.twig', array(
                'form' => $form->createView(),
                'utilisateur' => $this->getUser(),
                'client' => $this->getUser()->getClient(),
            ));
        } else {
            return $this->redirectToRoute('login');
        }
    }

    /**
     * Demande de changement de mot de passe
     *
     * @param Request $request
     * @return Response
     */
    public function passwordRequestAction(Request $request)
    {
        $default_data = array();
        $form = $this->createForm(PasswordRequestType::class, $default_data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $_POST['password_request']['email'];
            if ($this->isEmailExists($email)) {
                /* Si l'email existe envoyer un email */
                /** @var Utilisateur $utilisateur */
                $utilisateur = $this->getDoctrine()
                    ->getRepository('AppBundle:Utilisateur')
                    ->findOneBy(array(
                        'email' => $email
                    ));
                $em = $this->getDoctrine()
                    ->getManager();

                $now = new \DateTime();
                $request_token = Boost::boost($email . strval($now->getTimestamp()));
                $encoded_email = Boost::boost($email);

                /* Enregistrer la date et token de la demande */
                $utilisateur
                    ->setPasswordRequestDate($now)
                    ->setPasswordRequestToken($request_token);
                $em->flush();
                $support_email = '#';
                if ($utilisateur->getClient()->getNom() == 'EXPERTS_EXPANSION') {
                    $support_email = 'mailto:support@expertcontact.fr';
                }

                $from_details = $this->getFromDetails($utilisateur->getClient());
                $message = \Swift_Message::newInstance()
                    ->setSubject("Réinitialisation de votre mot de passe")
                    ->setFrom($from_details['address'], $from_details['label'])
                    ->setTo($email)
                    ->setBcc('support@scriptura.biz')
                    ->setBody(
                        $this->renderView('UtilisateurBundle:Emails:password-request-email.html.twig', array(
                            'utilisateur' => $utilisateur,
                            'client' => $utilisateur->getClient(),
                            'encoded_email' => $encoded_email,
                            'request_token' => $request_token,
                            'support_email' => $support_email,
                        ))
                        , 'text/html');
                $this->get('app.mailer_par_client')
                    ->getMailer($utilisateur->getClient())
                    ->send($message);

                $logActivite = new LogActivite();
                $logActivite->setDate($now)
                    ->setType(1);

                if ($utilisateur) {
                    $message = 'PASSWORD_REQUEST';
                    $logActivite->setUtilisateur($utilisateur);
                } else {
                    $message = 'PASSWORD_REQUEST';
                }
                $logActivite->setDescription($utilisateur->getEmail())
                    ->setMessage($message);

                $em->persist($logActivite);
                $em->flush();

                return $this->render('@Utilisateur/Security/password-request-email-sent.html.twig', array(
                   'user_email' => $email,
                ));
            } else {
                /* Si l'email n'existe pas envoyer une erreur */
                $form
                    ->get('email')
                    ->addError(new FormError("Aucun utilisateur n'est associé à cet email."));
                return $this->render('@Utilisateur/Security/password-request.html.twig', array(
                    'form' => $form->createView(),
                ));
            }
        }
        return $this->render('@Utilisateur/Security/password-request.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Confirmer la modification de mot de passe
     * En cliquant le lien dans email
     *
     * @param $email
     * @param $token
     * @return Response
     */
    public function passwordRequestConfirmAction($email, $token)
    {
        $now = new \DateTime();
        $user_email = Boost::deboostWithoutController($email);
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:Utilisateur')
            ->findOneBy(array(
                'passwordRequestToken' => $token
            ));
        if ($user_email && $user && $user->getEmail() == $user_email) {
            $em = $this->getDoctrine()->getManager();

            $logActivite = new LogActivite();
            $logActivite->setDate($now)
                ->setType(1);

            if ($user) {
                $message = 'PASSWORD_REQUEST_CLIQUED';
                $logActivite->setUtilisateur($user);
            } else {
                $message = 'PASSWORD_REQUEST_CLIQUED';
            }
            $logActivite->setDescription($user->getEmail())
                ->setMessage($message);

            $em->persist($logActivite);
            $em->flush();

            return $this->redirectToRoute('user_password_reset', array(
                'email' => $email,
                'token' => $token,
            ));
        } else {
            throw new AccessDeniedHttpException('Token invalide');
        }

    }


    /**
     * Afficher le formulaire
     * pour modifier mot de passe
     *
     * @param Request $request
     * @param $email
     * @param $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function passwordResetAction(Request $request, $email, $token)
    {
        $user_email = Boost::deboostWithoutController($email);
        /** @var Utilisateur $user */
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:Utilisateur')
            ->findOneBy(array(
                'passwordRequestToken' => $token,
                'email' => $user_email,
            ));
        if ($user_email && $user && $user->getEmail() == $user_email) {
            $default_data = array();
            $form = $this->createForm(PasswordChangeType::class, $default_data);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $now = new \DateTime();

                $password = $_POST['password_change']['password']['first'];
                $em = $this->getDoctrine()
                    ->getManager();
                $encoder = $this->get('security.password_encoder');

                /** Mise à jour du mot de passe */
                $encoded_password = $encoder->encodePassword($user, $password);
                $user
                    ->setPassword($encoded_password)
                    ->setPasswordRequestDate(null)
                    ->setPasswordRequestToken(null);

                /** Délai mot de passe de 3 jours pour le démo */
                if ($user_email == 'demo@scriptura.biz') {
                    $date_expire = new \DateTime();
                    $date_expire->add(new \DateInterval('P3D'));
                    $user->setCredentialsExpiredOn($date_expire);
                }

                $logActivite = new LogActivite();
                $logActivite->setDate($now)
                    ->setType(1);

                if ($user) {
                    $message = 'PASSWORD_CHANGED';
                    $logActivite->setUtilisateur($user);
                } else {
                    $message = 'PASSWORD_CHANGED';
                }
                $logActivite->setDescription($user->getEmail())
                    ->setMessage($message);

                $em->persist($logActivite);

                $em->flush();

                /* Déconnecter l'utilisateur et effacer les cookies */
                $this->get('security.token_storage')->setToken(null);
                $this->get('session')->invalidate();

                /* Rediriger vers la page succès */
                $response = $this->render('@Utilisateur/Security/password-changed.html.twig');

                /* Supprimer les cookies de connexion */
                $user_cookies = [
                    $this->getParameter('session.name'),
                    $this->getParameter('session.remember_me.name'),
                ];
                foreach ($user_cookies as $user_cookie) {
                    $response->headers->clearCookie($user_cookie);
                }

                return $response;
            }
            return $this->render('@Utilisateur/Security/password-reset.html.twig', array(
                'form' => $form->createView(),
                'utilisateur' => $user,
            ));
        } else {
            throw new AccessDeniedHttpException("Token invalide.");
        }
    }

    /**
     * Tester si un email
     * correspond à un utilisateur
     *
     * @param $email
     * @return bool
     */
    public function isEmailExists($email)
    {
        $exists = $this->getDoctrine()
            ->getRepository('AppBundle:Utilisateur')
            ->findOneBy(array(
                'email' => $email
            ));
        if ($exists) {
            return true;
        }
        return false;
    }

    private function getFromDetails(Client $client)
    {
        $smtp = $this->getDoctrine()
            ->getRepository('AppBundle:Smtp')
            ->findOneBy(array(
                'client' => $client
            ));

        $nomclient = ($client ? str_replace('_', '', strtolower($client->getNom())) : 'support');
        $nomclient = str_replace("'", '', $nomclient);
        $nomclient = str_replace("-", '', $nomclient);
        $address = $nomclient . '@lesexperts.biz';
        $label = "Equipe Support";

        //REVITEC
        if($client->getId() === 810){
            $address = $nomclient."@comptaetgestion.fr";
        }

        if ($smtp) {
            $address = $smtp->getLogin();
        }

        return ['address' => $address, 'label' => $label];
    }


    public function connexionHelpAction($token)
    {
        $email = Boost::deboostWithoutController($token);
        if ($email !== false) {
            $utilisateur = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')
                ->findOneBy(array(
                    'email' => $email,
                ));
            if ($utilisateur) {
                return $this->render('@Aide/Default/jivo_chat.html.twig');
            }
        }
        throw new AccessDeniedHttpException("Vous n'avez pas accès à cette ressource.");
    }
}
