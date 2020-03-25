<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 30/03/2017
 * Time: 17:20
 */

namespace AppBundle\EventListener;


use AppBundle\Entity\Menu;
use AppBundle\Entity\MenuParRole;
use AppBundle\Entity\MenuUtilisateur;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class MenuAllowed
{
    private $entity_manager;
    private $token_storage;
    private $authorization_checker;

    public function __construct(TokenStorage $storage, AuthorizationChecker $checker, EntityManager $em)
    {
        $this->token_storage = $storage;
        $this->authorization_checker = $checker;
        $this->entity_manager = $em;
    }

    /**
     * Tester si l'utilisateur a un accès au menu concerné
     * pour chaque requête
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $token = $this->token_storage
            ->getToken();
        if ($token) {
            /** @var Utilisateur $utilisateur */
            $utilisateur = $token->getUser();
            if ($utilisateur instanceof Utilisateur) {
                $request = $event->getRequest();
                $route_name = $request->attributes->get('_route');
                $menus = [];
                $user_menus = [];
                $liste_menus = $this->entity_manager
                    ->getRepository('AppBundle:Menu')
                    ->createQueryBuilder('menu')
                    ->select('menu')
                    ->getQuery()
                    ->getResult();
                /** @var Menu $menu */
                foreach ($liste_menus as $menu) {
                    $menus[] = $menu->getLien();
                }

                $liste_user_menus = $this->entity_manager
                    ->getRepository('AppBundle:MenuUtilisateur')
                    ->getMenuUtilisateur($utilisateur);
                /** @var MenuParRole|MenuUtilisateur $menu */
                foreach ($liste_user_menus as $menu) {
                    $user_menus[] = $menu->getMenu()->getLien();
                }

                if (in_array($route_name, $menus)) {
                    if (!in_array($route_name, $user_menus)) {
                        throw new AccessDeniedHttpException("Vous n'avez pas accès à cette page/resource.");
                    }

                }
            }
        }
    }
}