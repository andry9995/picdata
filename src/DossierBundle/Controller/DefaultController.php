<?php

namespace DossierBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DossierBundle:Default:index.html.twig', array());
    }

    //activation dossier
    public function dossierAction($site)
    {
        $user = $this->getUser();
        $role = $this->get('security.authorization_checker');
        $site = $this->getDoctrine()->getRepository('AppBundle:Dossier')->createQueryBuilder('d')
                        ->where('d.id = :id')->setParameter('id',$site)
                        ->getQuery()->getOneOrNullResult();
        $dossiers = $this->getDoctrine()->getManager()->getRepository('AppBundle:Site')->getDossiers($site,$user,$role);
        
        return $this->render('DossierBundle:Default:activation.html.twig',array('dossiers'=>$dossiers));
        return new Response(var_dump($dossiers));
    }
}
