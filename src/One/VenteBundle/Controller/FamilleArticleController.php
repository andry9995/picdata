<?php

/**
 * Project: oneup
 * Author : Mamy Rakotonirina
 * Created on : 9 nov. 2017 11:41:14
 */

namespace One\VenteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneFamilleArticle;

/**
 * Description of FamilleArticleController
 *
 */
class FamilleArticleController extends Controller {
    /**
     * Création d'une famille d'article depuis un modal
     * @return type
     */
    public function newinmodalAction(Request $request) {
        $oldID = $request->query->get('oldID');
        return $this->render('OneVenteBundle:FamilleArticle:newinmodal.html.twig', array(
            'oldID' => $oldID,
        ));
    }
    
    /**
     * Liste des familles sous forme d'options pour un champ select
     * @return type
     */
    public function listoptionsAction(Request $request) {
        $selectedValue = $request->query->get('selectedValue');
        $familles = $this->getDoctrine()->getRepository('AppBundle:OneFamilleArticle')->findAll();
        return $this->render('OneVenteBundle:FamilleArticle:listoptions.html.twig', array(
            'familles' => $familles,
            'selectedValue' => $selectedValue,
        ));
    }
    
    /**
     * Sauvegarde d'une famille d'arcticle
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request) {
        if ($request->isMethod('POST')) {
            try {
                $nom = $request->request->get('nom');
                $famille = new OneFamilleArticle();
                $famille->setNom($nom);
                $em = $this->getDoctrine()->getManager();
                $em->persist($famille);
                $em->flush();
                
                $response = array('type' => 'success', 'action' => 'add', 'id' => $famille->getId());
                return new JsonResponse($response);
            } catch (Exception $ex) {
                $response = array('type' => 'error', 'action' => 'add');
                return new JsonResponse($response);
            }
            
        }
    }
}
