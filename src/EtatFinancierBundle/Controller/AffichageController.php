<?php

namespace EtatFinancierBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AffichageController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('EtatFinancierBundle:Affichage:index.html.twig', array());
    }

    /**
     * @param $etat
     * @param Request $request
     * @return Response
     */
    public function showAction($etat, Request $request)
    {
        $post = $request->request;
        $dossier = $post->get('dossier');
        $exercices = json_decode($post->get('exercice'));
        $mois = ($post->get('mois') != 'Tous') ? json_decode($post->get('mois')) : true;

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id', $dossier)
            ->getQuery()
            ->getOneOrNullResult();

        $etats = $this->getDoctrine()->getRepository('AppBundle:Etat')->getEtatParent($etat,$dossier,0,$exercices,$mois);

        return $this->render('EtatFinancierBundle:Affichage:etat.html.twig', array('etats'=>$etats,'etat'=>$etat,'exercices'=>$exercices));
    }
}