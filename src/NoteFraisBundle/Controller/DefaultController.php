<?php

namespace NoteFraisBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NoteFraisBundle:Default:index.html.twig', array('name' => $name));
    }

    public function testAction(){
        return $this->render('NoteFraisBundle:Default:dashboardTpe.html.twig');

//        $x = $this->getDoctrine()
//            ->getRepository('AppBundle:ImputationControle')
//            ->getFactureClients($this->getDoctrine()->getRepository('AppBundle:Tiers')->find(76));
//
//        return new JsonResponse($x);
    }
}
