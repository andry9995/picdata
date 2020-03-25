<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 13/02/2017
 * Time: 08:58
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\LogActivite;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    private $entityManager;
    private $authChecker;
    private $router;

    public function __construct(HttpUtils $httpUtils, array $options = array(), EntityManager $em,
                                RouterInterface $the_route, AuthorizationChecker $authChecker)
    {
        $this->entityManager = $em;
        $this->router = $the_route;
        $this->authChecker = $authChecker;

        parent::__construct($httpUtils, $options);
    }

    /**
     * Rediriger vers la modification de mot de passe
     * Si last_login null
     *
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        /* @var Utilisateur $user */
        $user = $token->getUser();

        if (!$user->getLastLogin()) {
            $logActivite = new LogActivite();
            $logActivite->setDate(new \DateTime())
                ->setMessage('FIRST_CONNEXION')
                ->setType(1)
                ->setUtilisateur($user);

            $this->entityManager->persist($logActivite);
            $this->entityManager->flush();
            return new RedirectResponse($this->router->generate('user_first_login'));
        } else {
            return parent::onAuthenticationSuccess($request, $token);
        }
    }
}