<?php

/**
 * Created by Netbeans
 * Created on : 6 juil. 2017, 11:40:06
 * Author : Mamy Rakotonirina
 */

namespace One\ProspectBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\OneAppelTelephonique;
use AppBundle\Entity\OneStatusOpp;
use AppBundle\Entity\OneTache;
use AppBundle\Entity\Tiers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneOpportunite;
use One\VenteBundle\Service\ArticleService;
use Symfony\Component\HttpFoundation\Response;

class OpportuniteController extends Controller
{
    /**
     * Liste des opportunités
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

            $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);
//            $statouvert = $this->getDoctrine()->getRepository('AppBundle:OneStatusOpp')->find(1);
//            $statattente = $this->getDoctrine()->getRepository('AppBundle:OneStatusOpp')->find(2);
//            $statgagne = $this->getDoctrine()->getRepository('AppBundle:OneStatusOpp')->find(3);
//            $statperdu = $this->getDoctrine()->getRepository('AppBundle:OneStatusOpp')->find(4);
//            $oppouverts = $this->getDoctrine()
//                ->getRepository('AppBundle:OneOpportunite')
//                ->getOpportunitesByClientProspectListStatus($clientProspects ,$statouvert);
//            $oppattentes = $this->getDoctrine()
//                ->getRepository('AppBundle:OneOpportunite')
//                ->getOpportunitesByClientProspectListStatus($clientProspects ,$statattente);
//            $oppgagnes = $this->getDoctrine()
//                ->getRepository('AppBundle:OneOpportunite')
//                ->getOpportunitesByClientProspectListStatus($clientProspects , $statgagne);
//            $oppperdus = $this->getDoctrine()
//                ->getRepository('AppBundle:OneOpportunite')
//                ->getOpportunitesByClientProspectListStatus($clientProspects, $statperdu);
            /** @var OneOpportunite[] $opportunites */
            $opportunites = $this->getDoctrine()
                ->getRepository('AppBundle:OneOpportunite')
                ->getOpportunites($clientProspects, $sort, $sortorder, $q, $period, $startperiod, $endperiod, $stat);
            /** @var OneStatusOpp[] $status */
            $status = $this->getDoctrine()->getRepository('AppBundle:OneStatusOpp')->getStatus($dossier);
            /** @var OneAppelTelephonique[] $actions */
            $actions = $this->getDoctrine()
                ->getRepository('AppBundle:OneAppelTelephonique')
                ->getAppels($clientProspects, 'echeance', 'ASC', '', '', '', '', 'todo');
            $oppActions = array();
            foreach ($actions as $action) {
                if(null !== $action->getOpportunite()) {
                    $oppActions[$action->getOpportunite()->getId()][] = $action;
                }
            }

            $chartLabels = $chartDatas = array();
            foreach ($status as $stat) {
                $chartLabels[$stat->getOrdre()] = $stat->getNom();
                foreach ($opportunites as $opportunite) {
                    if (!isset($chartDatas[$stat->getOrdre()])) $chartDatas[$stat->getOrdre()] = 0;
                    if ($opportunite->getOneStatusOpp()->getOrdre() == $stat->getOrdre())
                        $chartDatas[$stat->getOrdre()] += (float)$opportunite->getMontant();
                }
            }

            $newChartLabels = array();
            foreach ($chartLabels as $chartLabel){
                $newChartLabels[]  = $chartLabel;
            }

            $newChartDatas = array();
            foreach ($chartDatas as $chartData){
                $newChartDatas[] = $chartData;
            }

            $now = new \DateTime('now');
            $mesureDatas = array();
            $mesureDatasTotal['revenu'] = 0;
            $mesureDatasTotal['comptage'] = 0;
            $mesureDatasTotal['probabilite'] = 0;
            $mesureDatasTotal['cloture'] = 0;

            foreach ($opportunites as $opportunite) {
                if (!isset($mesureDatas['revenu'][$opportunite->getOneStatusOpp()->getId()])) $mesureDatas['revenu'][$opportunite->getOneStatusOpp()->getId()] = 0;
                if (!isset($mesureDatas['comptage'][$opportunite->getOneStatusOpp()->getId()])) $mesureDatas['comptage'][$opportunite->getOneStatusOpp()->getId()] = 0;
                if (!isset($mesureDatas['probabilite'][$opportunite->getOneStatusOpp()->getId()])) $mesureDatas['probabilite'][$opportunite->getOneStatusOpp()->getId()] = 0;
                if (!isset($mesureDatas['cloture'][$opportunite->getOneStatusOpp()->getId()])) $mesureDatas['cloture'][$opportunite->getOneStatusOpp()->getId()] = 0;

                $mesureDatas['revenu'][$opportunite->getOneStatusOpp()->getId()] += (float)$opportunite->getMontant();
                $mesureDatas['comptage'][$opportunite->getOneStatusOpp()->getId()] += 1;
                if ($opportunite->getOneProbabilite()) {
                    $mesureDatas['probabilite'][$opportunite->getOneStatusOpp()->getId()] += $opportunite->getOneProbabilite()->getPourcentage();
                    $mesureDatasTotal['probabilite'] += $opportunite->getOneProbabilite()->getPourcentage();
                }

                $interval = $now->diff($opportunite->getCloture());
                $jour = (int)$interval->format('%R%a');
                $mesureDatas['cloture'][$opportunite->getOneStatusOpp()->getId()] += $jour;

                $mesureDatasTotal['revenu'] += (float)$opportunite->getMontant();
                $mesureDatasTotal['comptage'] += 1;
                $mesureDatasTotal['cloture'] += $jour;
            }

            foreach ($status as $stat) {
                if (isset($mesureDatas['comptage'][$stat->getId()]) && $mesureDatas['comptage'][$stat->getId()] > 0) {
                    $mesureDatas['probabilite'][$stat->getId()] = $mesureDatas['probabilite'][$stat->getId()] / $mesureDatas['comptage'][$stat->getId()];
                    $mesureDatas['cloture'][$stat->getId()] = $mesureDatas['cloture'][$stat->getId()] / $mesureDatas['comptage'][$stat->getId()];
                }
            }

            if ($mesureDatasTotal['comptage'] > 0) {
                $mesureDatasTotal['probabilite'] = $mesureDatasTotal['probabilite'] / $mesureDatasTotal['comptage'];
                $mesureDatasTotal['cloture'] = $mesureDatasTotal['cloture'] / $mesureDatasTotal['comptage'];
            }


//            $mesureDatas = array();
//            $mesureDatasTotal['revenu'] = 0;
//            $mesureDatasTotal['comptage'] = 0;
//            $mesureDatasTotal['probabilite'] = 0;
//            foreach ($status as $stat) {
//                foreach ($opportunites as $opportunite) {
//                    if (!isset($mesureDatas['revenu'][$stat->getId()])) $mesureDatas['revenu'][$stat->getId()] = 0;
//                    if (!isset($mesureDatas['comptage'][$stat->getId()])) $mesureDatas['comptage'][$stat->getId()] = 0;
//                    if (!isset($mesureDatas['probabilite'][$stat->getId()])) $mesureDatas['probabilite'][$stat->getId()] = 0;
//
//                    if ($opportunite->getOneStatusOpp()->getId() == $stat->getId()) {
//                        $mesureDatas['revenu'][$stat->getId()] += floatval($opportunite->getMontant());
//                        $mesureDatas['comptage'][$stat->getId()] += 1;
//                        if ($opportunite->getOneProbabilite())
//                            $mesureDatas['probabilite'][$stat->getId()] += $opportunite->getOneProbabilite()->getPourcentage();
//                    }
//                }
//
//                if ($mesureDatas['comptage'][$stat->getId()] > 0)
//                    $mesureDatas['probabilite'][$stat->getId()] = $mesureDatas['probabilite'][$stat->getId()] / $mesureDatas['comptage'][$stat->getId()];
//
//                $mesureDatasTotal['revenu'] += $mesureDatas['revenu'][$stat->getId()];
//                $mesureDatasTotal['comptage'] += $mesureDatas['comptage'][$stat->getId()];
//                $mesureDatasTotal['probabilite'] += $mesureDatas['probabilite'][$stat->getId()];
//            }
//            $mesureDatasTotal['probabilite'] = $mesureDatasTotal['probabilite'] / $mesureDatasTotal['comptage'];








            return $this->render('OneProspectBundle:Opportunite:list.html.twig', array(
//                'oppouverts' => $oppouverts,
//                'oppattentes' => $oppattentes,
//                'oppgagnes' => $oppgagnes,
//                'oppperdus' => $oppperdus,
                'opportunites' => $opportunites,
                'stat' => $stat,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
                'status' => $status,
                'actions' => $actions,
                'chartLabels' => $newChartLabels,
                'chartDatas' => $newChartDatas,
                'mesureDatas' => $mesureDatas,
                'mesureDatasTotal' => $mesureDatasTotal,
            ));
        }
    }
    
    /**
     * Création d'une opportunité
     */
    public function newAction(Request $request) {
        if ($request->isMethod('GET')) {
            $parent = $request->query->get('parent');
            $parentid = (int)$request->query->get('parentid');

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

                $avancements = $this->getDoctrine()
                    ->getRepository('AppBundle:OneAvancement')
                    ->getAvancements();

                $status = $this->getDoctrine()
                    ->getRepository('AppBundle:OneStatusOpp')
                    ->getStatus($dossier);

                $probabilites = $this->getDoctrine()
                    ->getRepository('AppBundle:OneProbabilite')
                    ->getProbabilites();

                $contacts = $this->getDoctrine()
                    ->getRepository('AppBundle:OneContactClient')
                    ->getContacts($parentid);

                return $this->render('OneProspectBundle:Opportunite:new.html.twig', array(
                    'clientProspects' => $clientProspects,
                    'avancements' => $avancements,
                    'status' => $status,
                    'probabilites' => $probabilites,
                    'parent' => $parent,
                    'parentid' => $parentid,
                    'contacts' => $contacts,
                ));
            } 
            
            //Aucun parent
            /** @var Tiers[] $clientProspects */
            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getAccounts($dossier);

            $avancements = $this->getDoctrine()
                ->getRepository('AppBundle:OneAvancement')
                ->getAvancements();

            $status = $this->getDoctrine()
                ->getRepository('AppBundle:OneStatusOpp')
                ->getStatus($dossier);

            $probabilites = $this->getDoctrine()
                ->getRepository('AppBundle:OneProbabilite')
                ->getProbabilites();
            return $this->render('OneProspectBundle:Opportunite:new.html.twig', array(
                'clientProspects' => $clientProspects,
                'avancements' => $avancements,
                'status' => $status,
                'probabilites' => $probabilites,
                'parent' => $parent,
                'parentid' => $parentid,
                'contacts' => array(),
            ));
        }
    }

    /**
     *  Affichage détail d'une opportunité
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function showAction(Request $request, $id) {
        if ($request->isMethod('GET')) {
            $type = $request->query->get('type');
            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');
            $params = $this->getDoctrine()
                ->getRepository('AppBundle:OneParametre')
                ->find(1);
            $opportunite = $this->getDoctrine()
                ->getRepository('AppBundle:OneOpportunite')->find($id);
            /** @var OneTache[] $taches */
            $taches = $this->getDoctrine()
                ->getRepository('AppBundle:OneTache')
                ->getTachesByOpportunite($opportunite, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneAppelTelephonique[] $appels */
            $appels = $this->getDoctrine()
                ->getRepository('AppBundle:OneAppelTelephonique')
                ->getAppelsByOpportunite($opportunite, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            return $this->render('OneProspectBundle:Opportunite:show.html.twig', array(
                'opportunite' => $opportunite,
                'taches' => $taches,
                'appels' => $appels,
                'type' => $type,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
            ));
        }
    }

    /**
     * Edition d'une opportunité
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editAction(Request $request, $id) {
        $opportunite = $this->getDoctrine()
            ->getRepository('AppBundle:OneOpportunite')
            ->find($id);
        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OneArticleOpp')
            ->getArticles($id);
        $contacts = array();
        if($opportunite->getTiers() !== null) {
            $contacts = $this->getDoctrine()
                ->getRepository('AppBundle:OneContactClient')
                ->getContacts($opportunite->getTiers()->getId());
        }
        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);
        //fin lesexperts.biz

        /** @var Tiers[] $clientProspects */
        $clientProspects = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->getAccounts($dossier);

        $avancements = $this->getDoctrine()
            ->getRepository('AppBundle:OneAvancement')
            ->getAvancements();

        $status = $this->getDoctrine()
            ->getRepository('AppBundle:OneStatusOpp')
            ->getStatus($dossier);

        $probabilites = $this->getDoctrine()
            ->getRepository('AppBundle:OneProbabilite')
            ->getProbabilites();

        return $this->render('OneProspectBundle:Opportunite:edit.html.twig', array(
            'opportunite' => $opportunite,
            'articles' => $articles,
            'contacts' => $contacts,
            'clientProspects' => $clientProspects,
            'avancements' => $avancements,
            'status' => $status,
            'probabilites' => $probabilites,
        ));
    }
    
    /**
     * Enregistrement d'ajout ou modification
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request) {
        if ($request->getMethod() === 'POST') {
            $articleService = new ArticleService($this->getDoctrine()->getManager());
            $posted = $request->request->all();
            
            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {


                    //Création d'un statut s'il n'y en a pas
                    $status = $request->request->get('status', 0);
                    if (!$status) {
                        $statusOpp = new OneStatusOpp();
                        $statusOpp->setNom('Ouverte');
                        $statusOpp->setCreeLe(new \DateTime('now'));
                        $statusOpp->setOrdre(0);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($statusOpp);
                        $em->flush();
                        $status = $statusOpp->getId();
                    }


                    $opportunite = new OneOpportunite();
                    
                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);
//                    $avancement = $this->getDoctrine()->getRepository('AppBundle:OneAvancement')->find($posted['avancement']);
                    $statut = $this->getDoctrine()
                        ->getRepository('AppBundle:OneStatusOpp')
                        ->find($status);
                    
                    $opportunite->setTiers($clientProspect);
//                    $opportunite->setOneAvancement($avancement);
                    $opportunite->setOneStatusOpp($statut);
                    $opportunite->setNom($posted['nom']);
                    $opportunite->setMontant(str_replace(' ', '', $posted['montant']));
                    $opportunite->setNote($posted['note']);
                    $opportunite->setCreeLe(new \DateTime('now'));
                    
                    if ($posted['cloture'] != '')
                        $opportunite->setCloture(\DateTime::createFromFormat('j/m/Y', $posted['cloture']));
                    else
                        $opportunite->setCloture(new \DateTime('now'));
                    
                    if ((int)$posted['contact-client'] > 0) {
                        $contactClient = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->find($posted['contact-client']);
                        $opportunite->setOneContactClient($contactClient);
                    }
                    if ((int)$posted['probabilite'] > 0) {
                        $probabilite = $this->getDoctrine()->getRepository('AppBundle:OneProbabilite')->find($posted['probabilite']);
                        $opportunite->setOneProbabilite($probabilite);
                    }
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($opportunite);
                    $em->flush();
                    
                    //Sauvegarde des articles
                    if (isset($posted['articles'])) {
                        foreach ($posted['articles'] as $article) {
                            $data = $articleService->parseArticleData($article);
                            $data['opportunite-id'] = (int)$opportunite->getId();
                            $articleService->saveArticleOppData($data);
                        }
                    }
                    
                    $response = array('type' => 'success', 'action' => 'add', 'id' => $opportunite->getId());
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            } else {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $opportunite = $em->getRepository('AppBundle:OneOpportunite')->find($posted['id']);
                    
                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);
//                    $avancement = $this->getDoctrine()->getRepository('AppBundle:OneAvancement')->find($posted['avancement']);
                    $statut = $this->getDoctrine()
                        ->getRepository('AppBundle:OneStatusOpp')
                        ->find($posted['status']);
                    
                    $opportunite->setTiers($clientProspect);
//                    $opportunite->setOneAvancement($avancement);
                    $opportunite->setOneStatusOpp($statut);
                    $opportunite->setNom($posted['nom']);
                    $opportunite->setMontant(str_replace(' ', '', $posted['montant']));
                    $opportunite->setNote($posted['note']);
                    $opportunite->setModifieLe(new \DateTime('now'));
                    
                    if ($posted['cloture'] != '')
                        $opportunite->setCloture(\DateTime::createFromFormat('j/m/Y', $posted['cloture']));
                    else
                        $opportunite->setCloture(new \DateTime('now'));
                    
                    if ((int)$posted['contact-client'] > 0) {
                        $contactClient = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->find($posted['contact-client']);
                        $opportunite->setOneContactClient($contactClient);
                    }
                    if ((int)$posted['probabilite'] > 0) {
                        $probabilite = $this->getDoctrine()->getRepository('AppBundle:OneProbabilite')->find($posted['probabilite']);
                        $opportunite->setOneProbabilite($probabilite);
                    }
                    
                    $em->flush();
                    
                    //Sauvegarde des articles
                    if (isset($posted['articles'])) {
                        foreach ($posted['articles'] as $article) {
                            $data = $articleService->parseArticleData($article);
                            $data['opportunite-id'] = (int)$opportunite->getId();
                            $articleService->saveArticleOppData($data);
                        }
                    }
                    
                    //Suppression des articles supprimés
                    if (isset($posted['deleted-articles'])) {
                        $rem = $this->getDoctrine()->getManager();
                        foreach ($posted['deleted-articles'] as $artid) {
                            $article = $this->getDoctrine()->getRepository('AppBundle:OneArticleOpp')->find($artid);
                            $rem->remove($article);
                        }
                        $rem->flush();
                    }
                    
                    $response = array('type' => 'success', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                }
            }
        }
    }

    /**
     * Récupération des contacts d'un prospect
     * @param Request $request
     * @return Response
     */
    public function listcontactAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('GET')) {
                $clientProspectID = $request->query->get('client-prospect');
                $contacts = $this->getDoctrine()
                    ->getRepository('AppBundle:OneContactClient')
                    ->getContacts($clientProspectID);
                return $this->render('OneProspectBundle:Opportunite:listcontact.html.twig', array(
                    'contacts' => $contacts,
                ));
            }
        }
    }
    
    /**
     * Suppression d'une opportunité
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $opportunite = $this->getDoctrine()
                ->getRepository('AppBundle:OneOpportunite')
                ->find($id);

            
            //Suppression des articles correspondants
            $articles = $this->getDoctrine()
                ->getRepository('AppBundle:OneArticleOpp')
                ->findBy(array('opportunite' => $opportunite));
            foreach ($articles as $article) {
                $em->remove($article);
            }

            $em->remove($opportunite);
            $em->flush();
            
            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
    }
}