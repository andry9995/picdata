<?php

/**
 * Created by Netbeans
 * Created on : 12 juil. 2017, 20:12:20
 * Author : Mamy Rakotonirina
 */

namespace One\ProspectBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Tache;
use AppBundle\Entity\Tiers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneTache;
use One\ProspectBundle\Service\FichierService;

class TacheController extends Controller
{
    /**
     * Liste des taches
     * @return Response
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
                return new Response('');
            }

            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);
            //fin lesexperts.biz




//            $todo = $this->getDoctrine()->getRepository('AppBundle:OneTache')->findByStatus(0);
//            $done = $this->getDoctrine()->getRepository('AppBundle:OneTache')->findByStatus(1);

            $todo = $this->getDoctrine()
                ->getRepository('AppBundle:OneTache')
                ->getTachesByClientProspectListStatus($clientProspects, 0);
            $done = $this->getDoctrine()
                ->getRepository('AppBundle:OneTache')
                ->getTachesByClientProspectListStatus($clientProspects, 1);
            /** @var OneTache[] $taches */
            $taches = $this->getDoctrine()
                ->getRepository('AppBundle:OneTache')
                ->getTaches($clientProspects, $sort, $sortorder, $q, $period, $startperiod, $endperiod, $stat);
            return $this->render('OneProspectBundle:Tache:list.html.twig', array(
                'todo' => $todo,
                'done' => $done,
                'taches' => $taches,
                'stat' => $stat,
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
     * Création d'une tache
     * @return Response
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
                $clientProspects = $this->getDoctrine()->getRepository('AppBundle:Tiers')->getAccounts($dossier);
                $opportunites = $this->getDoctrine()->getRepository('AppBundle:OneOpportunite')->getOpportunites($clientProspects);
                $contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->getContacts($parentid);
                $projets = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->getProjets();
                return $this->render('OneProspectBundle:Tache:new.html.twig', array(
                    'clientProspects' => $clientProspects,
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
                $clientProspects = $this->getDoctrine()->getRepository('AppBundle:Tiers')->getAccounts($dossier);
                $opportunites = $this->getDoctrine()->getRepository('AppBundle:OneOpportunite')->getOpportunites($clientProspects);
                $opp = $this->getDoctrine()->getRepository('AppBundle:OneOpportunite')->find($parentid);
                /** @var Tiers $cp */
                $cp = $opp->getTiers();
                $contacts = array();
                if($cp !== null) {
                    $contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->getContacts($cp->getId());
                }

                $projets = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->getProjets();
                return $this->render('OneProspectBundle:Tache:new.html.twig', array(
                    'clientProspects' => $clientProspects,
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

            $clientProspects = $this->getDoctrine()->getRepository('AppBundle:Tiers')->getAccounts($dossier);
            $opportunites = $this->getDoctrine()->getRepository('AppBundle:OneOpportunite')->getOpportunites($clientProspects);
            $projets = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->getProjets();
            return $this->render('OneProspectBundle:Tache:new.html.twig', array(
                'clientProspects' => $clientProspects,
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
     * Edition d'une tache
     * @return Response
     */
    public function editAction(Request $request, $id) {
        $tache = $this->getDoctrine()->getRepository('AppBundle:OneTache')->find($id);

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $clientProspects = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->findBy(array('dossier' => $dossier));
        //fin lesexperts.biz



        $opportunites = $this->getDoctrine()->getRepository('AppBundle:OneOpportunite')->getOpportunites($clientProspects);
        $fichiers = $this->getDoctrine()->getRepository('AppBundle:OneFichier')->getFiles(unserialize($tache->getFichier()));
        $projets = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->getProjets();
        if ($tache->getTiers()) {
            $contacts = $this->getDoctrine()
                ->getRepository('AppBundle:OneContactClient')
                ->getContacts($tache->getTiers()->getId());
        } else {
            $contacts = array();
        }
        return $this->render('OneProspectBundle:Tache:edit.html.twig', array(
            'tache' => $tache,
            'clientProspects' => $clientProspects,
            'contacts' => $contacts,
            'opportunites' => $opportunites,
            'fichiers' => $fichiers,
            'projets' => $projets,
        ));
    }
    
    public function saveAction(Request $request) {
        if ($request->isMethod('POST')) {
            $fichierService = new FichierService($this->getDoctrine()->getManager());
            $posted = $request->request->all();
            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $tache = new OneTache();
                    $tache->setSujet($posted['sujet']);
                    $tache->setMemo($posted['memo']);
                    $tache->setStatus($posted['tache-status']);
                    $tache->setCreeLe(new \DateTime('now'));
                    
                    if ($posted['echeance'] != '')
                        $tache->setEcheance(\DateTime::createFromFormat('d/m/Y', $posted['echeance']));
                    else
                        $tache->setEcheance(new \DateTime('now'));
                    
                    if ((int)$posted['client-prospect'] > 0) {
                        $clientProspect = $this->getDoctrine()
                            ->getRepository('AppBundle:Tiers')
                            ->find($posted['client-prospect']);
                        $tache->setTiers($clientProspect);
                    }
                    
                    if ((int)$posted['contact-client'] > 0) {
                        $contactClient = $this->getDoctrine()
                            ->getRepository('AppBundle:OneContactClient')
                            ->find($posted['contact-client']);
                        $tache->setOneContactClient($contactClient);
                    }
                    
                    if ((int)$posted['opportunite'] > 0) {
                        $opportunite = $this->getDoctrine()
                            ->getRepository('AppBundle:OneOpportunite')
                            ->find($posted['opportunite']);
                        $tache->setOpportunite($opportunite);
                    }
                    
//                    if (intval($posted['projet']) > 0) {
//                        $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($posted['projet']);
//                        $tache->setOneProjet($projet);
//                    }
                    
                    //Sauvegarde des fichiers
                    $filesID = array();
                    if (isset($posted['uploaded-files'])) {
                        foreach ($posted['uploaded-files'] as $file) {
                            $data = $fichierService->parseFile($file);
                            $filesID[] = $fichierService->saveData($data);
                        }
                    }
                    
                    //Suppression des fichiers uploadés
                    if (isset($posted['deleted-files'])) {
                        foreach ($posted['deleted-files'] as $file) {
                            unlink($this->getParameter('one_upload_dir').$file);
                        }
                    }
                    
                    $tache->setFichier(serialize($filesID));
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($tache);
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
                    $tache = $em->getRepository('AppBundle:OneTache')->find($posted['id']);
                    
                    $tache->setSujet($posted['sujet']);
                    $tache->setMemo($posted['memo']);
                    $tache->setStatus($posted['tache-status']);
                    $tache->setModifieLe(new \DateTime('now'));
                    
                    if ($posted['echeance'] != '')
                        $tache->setEcheance(\DateTime::createFromFormat('j/m/Y', $posted['echeance']));
                    else
                        $tache->setEcheance(new \DateTime('now'));
                    
                    if ((int)$posted['client-prospect'] > 0) {
                        $clientProspect = $this->getDoctrine()
                            ->getRepository('AppBundle:Tiers')
                            ->find($posted['client-prospect']);
                        $tache->setTiers($clientProspect);
                    }
                    
                    if ((int)$posted['contact-client'] > 0) {
                        $contactClient = $this->getDoctrine()
                            ->getRepository('AppBundle:OneContactClient')
                            ->find($posted['contact-client']);
                        $tache->setOneContactClient($contactClient);
                    }
                    
                    if ((int)$posted['opportunite'] > 0) {
                        $opportunite = $this->getDoctrine()->getRepository('AppBundle:OneOpportunite')->find($posted['opportunite']);
                        $tache->setOpportunite($opportunite);
                    }
                    
//                    if (intval($posted['projet']) > 0) {
//                        $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($posted['projet']);
//                        $tache->setOneProjet($projet);
//                    }
                    
                    //Sauvegarde des fichiers
                    $filesID = array();
                    if (isset($posted['uploaded-files'])) {
                        foreach ($posted['uploaded-files'] as $file) {
                            $data = $fichierService->parseFile($file);
                            $filesID[] = $fichierService->saveData($data);
                        }
                    }
                    
                    //Suppression des fichiers uploadés
                    if (isset($posted['deleted-files'])) {
                        $rem = $this->getDoctrine()->getManager();
                        foreach ($posted['deleted-files'] as $file) {
                            $fichier = $this->getDoctrine()->getRepository('AppBundle:OneFichier')->findOneByNom($file);
                            $rem->remove($fichier);
                            unlink($this->getParameter('one_upload_dir').$file);
                        }
                        $rem->flush();
                    }
                    
                    $tache->setFichier(serialize($filesID));
                    
                    $em->flush();
                
                    $response = array('type' => 'success', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                }
            }
        }
    }
    
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $tache = $this->getDoctrine()->getRepository('AppBundle:OneTache')->find($id);
            $em->remove($tache);

            //Suppression des fichiers correspondants
            $filesID = unserialize($tache->getFichier());
            if(is_array($filesID)) {
                if (count($filesID) > 0) {
                    foreach ($filesID as $fileID) {
                        $file = $this->getDoctrine()->getRepository('AppBundle:OneFichier')->find($fileID);
                        unlink($this->getParameter('one_upload_dir') . $file->getNom());
                        $em->remove($file);
                    }
                }
            }
            
            $em->flush();
            
            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
    }
}