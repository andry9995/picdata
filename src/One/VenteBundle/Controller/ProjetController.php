<?php

/**
 * Created by Netbeans
 * Created on : 21 août 2017, 20:18:48
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneProjet;
use One\VenteBundle\Service\VenteService;
use One\VenteBundle\Service\EncaissementService;

class ProjetController extends Controller
{
    /**
     * Liste des projets
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request) {
        if ($request->isMethod('GET')) {
            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');
            $projets = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->getProjets($sort, $sortorder, $q, $period, $startperiod, $endperiod);
            return $this->render('OneVenteBundle:Projet:list.html.twig', array(
                'projets' => $projets,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
            ));
        }
    }
    
    /**
     * Création d'un projet
     * @return Response
     */
    public function newAction(Request $request) {
        if ($request->isMethod('GET')) {
            $parent = $request->query->get('parent');
            $parentid = intval($request->query->get('parentid'));
            $parent2 = $request->query->get('parent2');
            $parentid2 = intval($request->query->get('parentid2'));

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);
            //fin lesexperts.biz
            
            //Parent: ClientProspect
            if ($parent == 'client') {
                $clientProspects = $this->getDoctrine()->getRepository('AppBundle:OneClientProspect')->getAccounts($dossier);
                return $this->render('OneVenteBundle:Projet:new.html.twig', array(
                    'clientProspects' => $clientProspects,
                    'parent' => $parent,
                    'parentid' => $parentid,
                    'parent2' => $parent2,
                    'parentid2' => $parentid2,
                ));
            }
            
            //Aucun parent
            else {
                $clientProspects = $this->getDoctrine()->getRepository('AppBundle:OneClientProspect')->getAccounts($dossier);
                return $this->render('OneVenteBundle:Projet:new.html.twig', array(
                    'clientProspects' => $clientProspects,
                    'parent' => $parent,
                    'parentid' => $parentid,
                    'parent2' => $parent2,
                    'parentid2' => $parentid2,
                ));
            }
        }
    }
    
    /**
     * Edition d'un projet
     * @return Response
     */
    public function editAction($id) {
        $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($id);
        return $this->render('OneVenteBundle:Projet:edit.html.twig', array(
            'projet' => $projet,
        ));
    }
    
    /**
     * 
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function showAction(Request $request, $id) {
        if ($request->isMethod('GET')) {

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);
            $exercice = $request->query->get('exercice');

            $clientProspects = $this->getDoctrine()->getRepository('AppBundle:OneClientProspect')->getAccounts($dossier);
            //fin lesexperts.biz


            $venteService = new VenteService($this->getDoctrine()->getManager());
            $encaissementService = new EncaissementService($this->getDoctrine()->getManager());
            $type = $request->query->get('type');
            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');
            $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($id);
            $taches = $this->getDoctrine()->getRepository('AppBundle:OneTache')->getTachesByProjet($projet, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            $appels = $this->getDoctrine()->getRepository('AppBundle:OneAppelTelephonique')->getAppelsByProjet($projet, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            $factures = $this->getDoctrine()->getRepository('AppBundle:OneVente')->getVentesByProjet($projet, 'facture', $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            $encaissements = $this->getDoctrine()->getRepository('AppBundle:OneEncaissement')->getEncaissementsByProjet($projet, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            $avoirs = $this->getDoctrine()->getRepository('AppBundle:OneVente')->getVentesByProjet($projet, 'avoir', $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            return $this->render('OneVenteBundle:Projet:show.html.twig', array(
                'projet' => $projet,
                'taches' => $taches,
                'appels' => $appels,
                'factures' => $factures,
                'encaissements' => $encaissements,
                'avoirs' => $avoirs,
                'type' => $type,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'factureAmounts' => $venteService->getVenteAmounts($clientProspects, $exercice,'facture'),
                'encaissementAmounts' => $encaissementService->getEncaissementAmounts($clientProspects, $exercice),
                'avoirAmounts' => $venteService->getVenteAmounts($clientProspects, $exercice,'avoir'),
            ));
        }
    }
    
    public function saveAction(Request $request) {
        if ($request->isMethod('POST')) {
            $posted = $request->request->all();
            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $projet = new OneProjet();
                    $projet->setNom($posted['nom']);
                    $projet->setDescription($posted['description']);
                    $projet->setCreeLe(new \DateTime('now'));
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($projet);
                    $em->flush();
                    
                    $response = array('type' => 'success', 'action' => 'add');
                    return new JsonResponse($response);
                } catch (Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            } else {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $projet = $em->getRepository('AppBundle:OneProjet')->find($posted['id']);
                    
                    $projet->setNom($posted['nom']);
                    $projet->setDescription($posted['description']);
                    $projet->setModifieLe(new \DateTime('now'));
                    
                    $em->flush();
                
                    $response = array('type' => 'success', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                } catch (Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                }
            }
        }
    }
    
    /**
     * Suppresion d'un projet
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($id);
            
            $em->remove($projet);
            $em->flush();
            
            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
        
    }
}