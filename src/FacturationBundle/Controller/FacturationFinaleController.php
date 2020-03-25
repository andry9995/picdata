<?php

namespace FacturationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\JsonResponse;

class FacturationFinaleController extends Controller
{
    public function indexAction()
    {
        return $this->render('FacturationBundle:FacturationFinale:index.html.twig', array(
                // ...
            ));    
    }

    public function billingFinalListAction(Request $request)
    {

        $post     = $request->request;
        $client   = $post->get('client_id');
        $exercice = $post->get('exercice');
        $mois     = $post->get('mois');
        $annee    = $post->get('annee');

        $param = array(
            'client' => $client,
            'exercice' => $exercice,
            'mois' => $mois,
            'annee' => $annee
        );

        $data = $this->getDoctrine()
                    ->getRepository('AppBundle:Image')
                    ->factFinalList($param);		

     	return new JsonResponse($data);

    }

}
