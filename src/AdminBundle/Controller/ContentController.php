<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\MenuUtilisateur;

class ContentController extends Controller
{
    //liste clients
    public function clientsAction()
    {
        return $this->render('AdminBundle:Content:clients.html.twig', array());
    }

    //liste utilisateur
    public function utilisateursAction()
    {
        return $this->render('AdminBundle:Content:utilisateurs.html.twig');
    }

    //liste utilisateur
    public function menusAction($utilisateur)
    {
        $user;
        if($utilisateur != '')
            $user = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->createQueryBuilder('u')
                            ->where('u.id = :id')
                            ->setParameter('id',$utilisateur)
                            ->getQuery()
                            ->getOneOrNullResult();

        $em = $this->getDoctrine()->getManager();

        $menu_parents = $em->getRepository('AppBundle:Menu')->getAllParent($user);
        foreach($menu_parents as &$menu_parent)
        {
            $menu_childs = $em->getRepository('AppBundle:Menu')->getAllChild($menu_parent,$user);
            $menu_parent->setChild($menu_childs);

            if($menu_parent->getChild() != null)
            {
                $array_temp = $menu_parent->getChild();
                foreach ($array_temp as &$menu_child) {
                    $menu_childss = $em->getRepository('AppBundle:Menu')->getAllChild($menu_child, $user);
                    $menu_child->setChild($menu_childss);
                }
            }
        }

        return $this->render('AdminBundle:Content:menus.html.twig', array('menus'=>$menu_parents));
    }

    //remove or add menu disable utlisateur
    public function editMenuDisabledAction($menu,$utilisateur,$menu_utilisateur)
    {
        $user = $this->getUser();
        $acces_utilisateur = $user->getAccesUtilisateur()->getCode();
        $em = $this->getDoctrine()->getManager();

        if($this->get('security.authorization_checker')->isGranted('ROLE_CLIENT'))
        {  
            if($menu_utilisateur == 0)
            {
                $utilisateur = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->createQueryBuilder('u')
                                ->where('u.id = :id')
                                ->setParameter('id',$utilisateur)
                                ->getQuery()
                                ->getOneOrNullResult();
                $menu = $this->getDoctrine()->getRepository('AppBundle:Menu')->createQueryBuilder('m')
                                ->where('m.id = :id')
                                ->setParameter('id',$menu)
                                ->getQuery()
                                ->getOneOrNullResult();

                $menu_utilisateur = new MenuUtilisateur();
                $menu_utilisateur->setMenu($menu)->setUtilisateur($utilisateur);

                $em->persist($menu_utilisateur);
                $em->flush();

                return new Response($menu_utilisateur->getId());
            }
            else
            {
                $menu_utilisateur = $this->getDoctrine()->getRepository('AppBundle:MenuUtilisateur')->createQueryBuilder('mu')
                                ->where('mu.id = :id')
                                ->setParameter('id',$menu_utilisateur)
                                ->getQuery()
                                ->getOneOrNullResult();

                $em->remove($menu_utilisateur);
                $em->flush();
            }
        }

        return new Response(0);
    }
}