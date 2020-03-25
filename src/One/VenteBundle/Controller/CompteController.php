<?php

/**
 * Created by Netbeans
 * Created on : 24 août 2017, 15:30:01
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CompteController extends Controller
{
    public function listoptionAction(Request $request) {
//        $comptes = $this->getDoctrine()->getRepository('AppBundle:OneCompte')->getComptes();

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->request->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);
        //fin lesexperts.biz

        $pccs = $this->getDoctrine()
            ->getRepository('AppBundle:Pcc')
            ->getPccByDossierLike($dossier, array('41'));
        return $this->render('OneVenteBundle:Compte:listoption.html.twig', array(
//                'comptes' => $comptes,
            'pccs' => $pccs
            ));
    }
}