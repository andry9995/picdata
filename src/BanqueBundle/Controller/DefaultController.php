<?php

namespace BanqueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BanqueBundle:Default:index.html.twig', array('name' => $name));
    }


    public function piloteAction(){
        return $this->render('BanqueBundle:Default:pilote.html.twig');
    }

    public function gestionBanqueAction(){
        return $this->render('BanqueBundle:Default:gestion-banque.html.twig');
    }

    public function controleReleveAction(){
        return $this->render('BanqueBundle:Default:controle-releve.html.twig');
    }

    public function releveBanqueAction()
    {
        return $this->render('BanqueBundle:ReleveBanque:index.html.twig',array('action'=>0,'bts'=>json_encode($this->getDoctrine()->getRepository('AppBundle:BanqueType')->getBanqueTypeParametres())));
    }

    public function pieceManquanteAction(){
        return $this->render('BanqueBundle:ReleveBanque:index.html.twig',array('action'=>1));
        //return $this->render('BanqueBundle:Default:piece-manquante.html.twig');
    }

    public function preparationTvaAction(){
        return $this->render('BanqueBundle:Default:preparation-tva.html.twig');
    }

    public function testAction(){

        $banqueCompte = $this->getDoctrine()
            ->getRepository('AppBundle:BanqueCompte')
            ->find(116);

        $releves = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->getReleves($banqueCompte->getDossier(),2017,null,$banqueCompte, true);


        return new JsonResponse(1);


    }
}
