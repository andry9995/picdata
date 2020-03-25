<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 17/03/2017
 * Time: 16:32
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\LogActivite;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Intercepter chaque connexion: Formulaire ou Remember_me
 *
 * Class LoginHandler
 * @package UtilisateurBundle\Handler
 */
class LoginHandler implements EventSubscriberInterface
{

    /** @var TokenStorage */
    private $tokenStorage;

    /** @var  AuthorizationChecker */
    private $authChecker;

    /** @var  EntityManager */
    private $em;

    /** @var AuthenticationUtils */
    private $authenticationUtils;

    public function __construct(TokenStorage $storage, AuthorizationChecker $checker,
                                EntityManager $em, AuthenticationUtils $authenticationUtils)
    {
        $this->tokenStorage = $storage;
        $this->authChecker = $checker;
        $this->em = $em;
        $this->authenticationUtils = $authenticationUtils;
    }

    public static function getSubscribedEvents()
    {
        return array(
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        );
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $now = new \DateTime();
        $username = $this->authenticationUtils->getLastUsername();
        $existingUser = $this->em->getRepository('AppBundle:Utilisateur')
            ->findOneBy(array(
                'email' => $username,
            ));
        $logActivite = new LogActivite();
        $logActivite->setDate($now)
            ->setType(1);

        if ($existingUser) {
            $message = 'AUTHENTICATION_FAILURE_WRONG_PASSWORD';
            $logActivite->setUtilisateur($existingUser);
        } else {
            $message = 'AUTHENTICATION_FAILURE_USER_DOESNT_EXIST';
        }
        $logActivite->setDescription($username)
            ->setMessage($message);

        $this->em->persist($logActivite);
        try {
            $this->em->flush();
        } catch (OptimisticLockException $e) {
        }
    }

    /**
     * Handler pour succès connection
     *
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $now = new \DateTime();

        /** @var Utilisateur $user */
        $user = $event->getAuthenticationToken()
            ->getUser();
        $logActivite = new LogActivite();
        $logActivite->setDate($now)
            ->setType(1)
            ->setUtilisateur($user);

        $message = 'CONNEXION_FULL';

        if ($this->authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            //Si authentification par formulaire
            $message = 'CONNEXION_FULL';
        } elseif ($this->authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            //Si authentification par cookies
            $message = 'CONNEXION_REMEMBER_ME';
        }
        $logActivite->setMessage($message);

        $this->em->persist($logActivite);

        if ($user->getLastLogin()) {
            $user->setLastLogin($now);
        }

        try {
            $this->em->flush();
        } catch (OptimisticLockException $e) {
        }
    }
}