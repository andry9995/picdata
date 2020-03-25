<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        /*$user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $menu_parents = $em->getRepository('AppBundle:Menu')->getMenuParent($user);
        foreach($menu_parents as &$menu_parent)
        {
            $menu_clilds = $em->getRepository('AppBundle:Menu')->getMenuChild($menu_parent,$user);
            $menu_parent->setChild($menu_clilds);
        }

        return new Response(var_dump($menu_parents));

        return new Response(var_dump($em->getRepository('AppBundle:Menu')->getMenuParent($user)));*/
        //return $this->redirectToRoute('app_sites');
        return $this->redirectToRoute('admin_content_clients');
        //return $this->forward('AdminBundle:Content:clients');
        //return $this->render('AdminBundle:Default:index.html.twig', array('name' => 'Admin'));
    }
}
