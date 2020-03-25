<?php

/**
 * Created by Netbeans
 * Created on : 18 juil. 2017, 14:42:08
 * Author : Mamy Rakotonirina
 */

namespace One\ProspectBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Tiers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneAppelTelephonique;

class AppelTelephoniqueController extends Controller
{
    /**
     * Liste des appels
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request) {
        if ($request->isMethod('GET')) {
            $stat = $request->query->get('stat');
            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if(null === $dossier){

                return new \Symfony\Component\HttpFoundation\Response('');
            }

            /** @var Tiers[] $clientProspects */
            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);
            //fin lesexperts.biz


            $todo = $this->getDoctrine()->getRepository('AppBundle:OneAppelTelephonique')
                ->getAppelsByClientProspectListStatus($clientProspects, 0);

            $done = $this->getDoctrine()->getRepository('AppBundle:OneAppelTelephonique')
                ->getAppelsByClientProspectListStatus($clientProspects, 1);

//            $todo = $this->getDoctrine()->getRepository('AppBundle:OneAppelTelephonique')->findByStatus(0);
//            $done = $this->getDoctrine()->getRepository('AppBundle:OneAppelTelephonique')->findByStatus(1);

            /** @var OneAppelTelephonique[] $appels */
            $appels = $this->getDoctrine()
                ->getRepository('AppBundle:OneAppelTelephonique')
                ->getAppels($clientProspects, $sort, $sortorder, $q, $period, $startperiod, $endperiod, $stat);
            return $this->render('OneProspectBundle:AppelTelephonique:list.html.twig', array(
                'todo' => $todo,
                'done' => $done,
                'appels' => $appels,
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
     * Nouvel appel
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request) {
        if ($request->isMethod('GET')) {
            $parent = $request->query->get('parent');
            $parentid = (int)$request->query->get('parentid');
            $parent2 = $request->query->get('parent2');
            $parentid2 = (int)$request->query->get('parentid2');


            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);
            //fin lesexperts.biz
            
            //Parent: ClientProspect
            if ($parent === 'prospect' || $parent === 'client') {
                /** @var Tiers[] $clientProspects */
                $clientProspects = $this->getDoctrine()
                    ->getRepository('AppBundle:Tiers')
                    ->getAccounts($dossier);
                $qualifications = $this->getDoctrine()
                    ->getRepository('AppBundle:OneQualificationAppel')
                    ->getQualifications();
                $opportunites = $this->getDoctrine()
                    ->getRepository('AppBundle:OneOpportunite')
                    ->getOpportunites($clientProspects);
                $contacts = $this->getDoctrine()
                    ->getRepository('AppBundle:OneContactClient')
                    ->getContacts($parentid);
                $projets = $this->getDoctrine()
                    ->getRepository('AppBundle:OneProjet')
                    ->getProjets();
                return $this->render('OneProspectBundle:AppelTelephonique:new.html.twig', array(
                    'clientProspects' => $clientProspects,
                    'qualifications' => $qualifications,
                    'opportunites' => $opportunites,
                    'parent' => $parent,
                    'parentid' => $parentid,
                    'parent2' => $parent2,
                    'parentid2' => $parentid2,
                    'contacts' => $contacts,
                    'projets' => $projets,
                ));
            }

            if ($parent === 'opportunite') {
                /** @var Tiers[] $clientProspects */
                $clientProspects = $this->getDoctrine()
                    ->getRepository('AppBundle:Tiers')
                    ->getAccounts($dossier);
                $qualifications = $this->getDoctrine()
                    ->getRepository('AppBundle:OneQualificationAppel')
                    ->getQualifications();
                $opportunites = $this->getDoctrine()
                    ->getRepository('AppBundle:OneOpportunite')
                    ->getOpportunites($clientProspects);
                $opp = $this->getDoctrine()
                    ->getRepository('AppBundle:OneOpportunite')
                    ->find($parentid);
                /** @var Tiers $cp */
                $cp = $opp->getTiers();
                $contacts = array();
                if($cp !== null) {
                    $contacts = $this->getDoctrine()
                        ->getRepository('AppBundle:OneContactClient')
                        ->getContacts($cp->getId());
                }

                $projets = $this->getDoctrine()
                    ->getRepository('AppBundle:OneProjet')
                    ->getProjets();

                return $this->render('OneProspectBundle:AppelTelephonique:new.html.twig', array(
                    'clientProspects' => $clientProspects,
                    'qualifications' => $qualifications,
                    'opportunites' => $opportunites,
                    'parent' => $parent,
                    'parentid' => $parentid,
                    'parent2' => $parent2,
                    'parentid2' => $parentid2,
                    'opp' => $opp,
                    'cp' => $cp,
                    'contacts' => $contacts,
                    'projets' => $projets,
                ));
            }

            //Parent: Opportunite

            //Aucun parent

            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getAccounts($dossier);
            $qualifications = $this->getDoctrine()
                ->getRepository('AppBundle:OneQualificationAppel')
                ->getQualifications();
            $opportunites = $this->getDoctrine()
                ->getRepository('AppBundle:OneOpportunite')
                ->getOpportunites($clientProspects);
            $projets = $this->getDoctrine()
                ->getRepository('AppBundle:OneProjet')
                ->getProjets();
            return $this->render('OneProspectBundle:AppelTelephonique:new.html.twig', array(
                'clientProspects' => $clientProspects,
                'qualifications' => $qualifications,
                'opportunites' => $opportunites,
                'parent' => $parent,
                'parentid' => $parentid,
                'parent2' => $parent2,
                'parentid2' => $parentid2,
                'contacts' => array(),
                'projets' => $projets,
            ));
        }
    }

    /**
     *  Edition appel
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request,$id) {
        $appel = $this->getDoctrine()
            ->getRepository('AppBundle:OneAppelTelephonique')
            ->find($id);

        $contacts = array();

        if($appel->getTiers() !== null) {
            $contacts = $this->getDoctrine()
                ->getRepository('AppBundle:OneContactClient')
                ->getContacts($appel->getTiers()->getId());
        }

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $clientProspects = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->getClientProspects($dossier);
        //fin lesexperts.biz


        $qualifications = $this->getDoctrine()->getRepository('AppBundle:OneQualificationAppel')->getQualifications();

        $opportunites = $this->getDoctrine()->getRepository('AppBundle:OneOpportunite')->getOpportunites($clientProspects);
        $projets = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->getProjets();
        return $this->render('OneProspectBundle:AppelTelephonique:edit.html.twig', array(
            'appel' => $appel,
            'clientProspects' => $clientProspects,
            'contacts' => $contacts,
            'qualifications' => $qualifications,
            'opportunites' => $opportunites,
            'projets' => $projets,
        ));
    }
    
    /**
     * Sauvegarde d'un appel
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request) {
        if ($request->isMethod('POST')) {
            $posted = $request->request->all();
            
            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $appel = new OneAppelTelephonique();
                    
                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);
                    $qualification = $this->getDoctrine()
                        ->getRepository('AppBundle:OneQualificationAppel')
                        ->find($posted['qualification']);
                    
                    $appel->setSujet($posted['sujet']);
                    $appel->setTiers($clientProspect);
                    $appel->setNote($posted['note']);
                    $appel->setOneQualification($qualification);
                    $appel->setStatus($posted['appel-status']);
                    $appel->setCreeLe(new \DateTime('now'));
                    
                    if ($posted['echeance'] != '')
                        $appel->setEcheance(\DateTime::createFromFormat('d/m/Y', $posted['echeance']));
                    else
                        $appel->setEcheance(new \DateTime('now'));
                    
                    if ((int)$posted['contact-client'] > 0) {
                        $contactClient = $this->getDoctrine()
                            ->getRepository('AppBundle:OneContactClient')
                            ->find($posted['contact-client']);
                        $appel->setOneContactClient($contactClient);
                    }
                    if ((int)$posted['opportunite'] > 0) {
                        $opportunite = $this->getDoctrine()
                            ->getRepository('AppBundle:OneOpportunite')
                            ->find($posted['opportunite']);
                        $appel->setOpportunite($opportunite);
                    }
                    
//                    if (intval($posted['projet']) > 0) {
//                        $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($posted['projet']);
//                        $appel->setOneProjet($projet);
//                    }
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($appel);
                    $em->flush();
                    
                    $response = array('type' => 'success', 'action' => 'add');
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            } else {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $appel = $em->getRepository('AppBundle:OneAppelTelephonique')->find($posted['id']);
                    
                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);
                    $qualification = $this->getDoctrine()
                        ->getRepository('AppBundle:OneQualificationAppel')
                        ->find($posted['qualification']);
                    
                    $appel->setSujet($posted['sujet']);
                    $appel->setTiers($clientProspect);
                    $appel->setNote($posted['note']);
                    $appel->setOneQualification($qualification);
                    $appel->setStatus($posted['appel-status']);
                    $appel->setModifieLe(new \DateTime('now'));
                    
                    if ($posted['echeance'] != '')
                        $appel->setEcheance(\DateTime::createFromFormat('j/m/Y', $posted['echeance']));
                    else
                        $appel->setEcheance(new \DateTime('now'));
                    
                    if (intval($posted['contact-client']) > 0) {
                        $contactClient = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->find($posted['contact-client']);
                        $appel->setOneContactClient($contactClient);
                    }
                    if (intval($posted['opportunite']) > 0) {
                        $opportunite = $this->getDoctrine()->getRepository('AppBundle:OneOpportunite')->find($posted['opportunite']);
                        $appel->setOpportunite($opportunite);
                    }
                    
//                    if (intval($posted['projet']) > 0) {
//                        $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($posted['projet']);
//                        $appel->setOneProjet($projet);
//                    }
                    
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
     * Suppresion d'un appel
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $appel = $this->getDoctrine()->getRepository('AppBundle:OneAppelTelephonique')->find($id);
            $em->remove($appel);
            $em->flush();
            
            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
        
    }
}