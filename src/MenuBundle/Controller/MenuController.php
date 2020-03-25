<?php

namespace MenuBundle\Controller;

use AppBundle\Entity\Menu;
use AppBundle\Entity\MenuParRole;
use AppBundle\Entity\MenuUtilisateur;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\VisudataVersion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
{
    /**
     *  Liste des menus à gauches pour
     *  l'utilisateur connecté
     *
     * @return Response
     */
    public function menuLeftAction()
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        $menus = $this->getDoctrine()
            ->getRepository('AppBundle:MenuUtilisateur')
            ->getMenuUtilisateurEx($utilisateur, $menus_id);
        return $this->render('@Menu/Default/menu-left.html.twig', array(
            'utilisateur' => $utilisateur,
            'menus' => $menus,
            'menus_id' => $menus_id
        ));

    }


    /**
     *  Liste des menus en haut
     *  pour l'utilisateur connecté
     *
     * @return Response
     */
    public function menuTopAction()
    {
        /** @var VisudataVersion $visudataVersion */
        $visudataVersion = $this->getDoctrine()->getRepository('AppBundle:VisudataVersion')
            ->getLastVersion();

        return $this->render('MenuBundle:Default:menu-top.html.twig', [
            'visudataVersion' => $visudataVersion
        ]);
    }

    /**
     * Liste des menus complets
     * A utiliser pour paramétrage accès
     *
     * @return Response
     */
    public function listeMenuAction()
    {
        $menus_id = [];

        if ($this->isGranted('ROLE_SCRIPTURA_ADMIN')) {
            $menus = $this->getDoctrine()
                ->getRepository('AppBundle:Menu')
                ->findAll();
            /** @var Menu $menu */
            foreach ($menus as $menu) {
                $menus_id[] = $menu->getId();
            }
            $menus_complet = $this->getDoctrine()
                ->getRepository('AppBundle:Menu')
                ->getAllMenu();
        } else {
            $menus = $this->getDoctrine()
                ->getRepository('AppBundle:MenuUtilisateur')
                ->getMenuUtilisateur($this->getUser());
            /** @var MenuParRole|MenuUtilisateur $menu */
            foreach ($menus as $menu) {
                $menus_id[] = $menu->getMenu()->getId();
            }

            $menus_complet = $this->getDoctrine()
                ->getRepository('AppBundle:MenuUtilisateur')
                ->getMenuParentUtilisateur($this->getUser());
        }

        return $this->render('@Menu/Default/menu-liste.html.twig', array(
            'menus_complet' => $menus_complet,
            'menus_id' => $menus_id,
        ));
    }

    public function encodePasswordAction()
    {
//        $em = $this->getDoctrine()
//            ->getEntityManager();
//        $encoder = $this->get('security.password_encoder');
//        $users = $this->getDoctrine()
//            ->getRepository('AppBundle:Utilisateur')
//            ->findBy(array(
//                'photo' => null
//            ));
//
//        /** @var Utilisateur $user */
//        foreach ($users as $user) {
//            $encoded = $encoder->encodePassword($user, $user->getPassword());
//            $user->setPassword($encoded);
//        }
//
//        $em->flush();

        return new Response('ok');
    }

}
