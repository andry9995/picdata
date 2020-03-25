<?php

/**
 * Created by Netbeans
 * Created on : 13 août 2017, 15:15:42
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\OneContactClient;
use AppBundle\Entity\OneProbabilite;
use AppBundle\Entity\OneReglement;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\Utilisateur;
use One\ProspectBundle\Service\FichierService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneDevis;
use AppBundle\Entity\OneVente;
use AppBundle\Entity\OneArticleVente;
use AppBundle\Entity\OneInvoiceDevis;
use AppBundle\Entity\OneCommandeDevis;
use One\VenteBundle\Service\DevisService;
use One\VenteBundle\Service\VenteService;
use One\VenteBundle\Service\ArticleService;
use One\VenteBundle\Service\DocumentService;

class DevisController extends Controller
{
    /**
     * Liste des devis
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request) {
        if ($request->isMethod('GET')) {
            $service = new DevisService($this->getDoctrine()->getManager());
            $stat = $request->query->get('stat');
            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');
            $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);
//            $ouverts = $this->getDoctrine()->getRepository('AppBundle:OneDevis')->findByStatus(1);
//            $gagnes = $this->getDoctrine()->getRepository('AppBundle:OneDevis')->findByStatus(2);
//            $perdus = $this->getDoctrine()->getRepository('AppBundle:OneDevis')->findByStatus(3);

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);


            if(null === $dossier){
                return new Response('');
            }

            $exercice = $request->query->get('exercice');


            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);


            $ouverts = $this->getDoctrine()
                ->getRepository('AppBundle:OneDevis')
                ->getDevisByClientProspects($clientProspects, $exercice, 1);
            $gagnes =$this->getDoctrine()
                ->getRepository('AppBundle:OneDevis')
                ->getDevisByClientProspects($clientProspects, $exercice,2);
            $perdus = $this->getDoctrine()
                ->getRepository('AppBundle:OneDevis')
                ->getDevisByClientProspects($clientProspects, $exercice, 3);

            //fin lesexperts.biz
            /** @var OneDevis[] $deviss */
            $deviss = $this->getDoctrine()
                ->getRepository('AppBundle:OneDevis')
                ->getDevis($clientProspects, $exercice, $sort, $sortorder, $q, $period, $startperiod, $endperiod, $stat);
            return $this->render('OneVenteBundle:Devis:list.html.twig', array(
                'ouverts' => $ouverts,
                'gagnes' => $gagnes,
                'perdus' => $perdus,
                'deviss' => $deviss,
                'stat' => $stat,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
                'invoiced' => $service->getInvoicedDevis(),
                'commanded' => $service->getCommandedDevis(),
            ));
        }
    }
    
    /**
     * Ajout d'un nouveau devis
     * @param Request $request
     * @return Response
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

            $exercice = $request->query->get('exercice');

            $exercices = Boost::getExercices(6,1);

            //fin lesexperts.biz


            
            //Parent: ClientProspect
            if ($parent === 'prospect' || $parent === 'client') {
                /** @var Tiers[] $clientProspects */
                $clientProspects = $this->getDoctrine()
                    ->getRepository('AppBundle:Tiers')
                    ->getClientProspects($dossier);
                /** @var OneReglement[] $reglements */
                $reglements = $this->getDoctrine()
                    ->getRepository('AppBundle:OneReglement')
                    ->getReglements();
                /** @var OneProbabilite[] $probabilites */
                $probabilites = $this->getDoctrine()
                    ->getRepository('AppBundle:OneProbabilite')
                    ->getProbabilites();
                /** @var OneContactClient[] $contacts */
                $contacts = $this->getDoctrine()
                    ->getRepository('AppBundle:OneContactClient')
                    ->getContacts($parentid);
                return $this->render('OneVenteBundle:Devis:new.html.twig', array(
                    'clientProspects' => $clientProspects,
                    'reglements' => $reglements,
                    'probabilites' => $probabilites,
                    'parent' => $parent,
                    'parentid' => $parentid,
                    'contacts' => $contacts,
                    'exercices' => $exercices,
                    'exercice' => $exercice
                ));
            } 
            
            //Aucun parent
            /** @var Tiers[] $clientProspects */
            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);
            /** @var OneReglement[] $reglements */
            $reglements = $this->getDoctrine()
                ->getRepository('AppBundle:OneReglement')
                ->getReglements();
            /** @var OneProbabilite[] $probabilites */
            $probabilites = $this->getDoctrine()
                ->getRepository('AppBundle:OneProbabilite')
                ->getProbabilites();
            return $this->render('OneVenteBundle:Devis:new.html.twig', array(
                'clientProspects' => $clientProspects,
                'reglements' => $reglements,
                'probabilites' => $probabilites,
                'parent' => $parent,
                'parentid' => $parentid,
                'contacts' => array(),
                'exercices' => $exercices,
                'exercice' => $exercice
            ));
        }
    }

    /**
     * Edition d'un devis
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editAction(Request $request, $id) {

        $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $exercice = $request->query->get('exercice');


        /** @var Tiers[] $clientProspects */
        $clientProspects = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->getClientProspects($dossier);


        $service = new DevisService($this->getDoctrine()->getManager());
//        $devisDetails = $service->getDevisDetails($clientProspects, $exercice)[$id];

        $devis = $this->getDoctrine()
            ->getRepository('AppBundle:OneDevis')
            ->find($id);

        $devisDetails = $service->getDevisDetailsByDevis($devis);

        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OneArticleVente')
            ->getArticlesDevis($id);
        $contacts = $this->getDoctrine()
            ->getRepository('AppBundle:OneContactClient')
            ->getContacts($devis->getTiers()->getId());


        $reglements = $this->getDoctrine()
            ->getRepository('AppBundle:OneReglement')
            ->getReglements();
        $status = $this->getDoctrine()
            ->getRepository('AppBundle:OneStatusOpp')
            ->getStatus($dossier);
        $taxes = $this->getDoctrine()
            ->getRepository('AppBundle:OneTva')
            ->getTva();

        $exercices = Boost::getExercices(6,1);


        $fichiers = $this->getDoctrine()
            ->getRepository('AppBundle:OneFichier')
            ->getFiles(unserialize($devis->getFichier()));

        return $this->render('OneVenteBundle:Devis:edit.html.twig', array(
            'devis' => $devis,
            'devisDetails' => $devisDetails,
            'articles' => $articles,
            'contacts' => $contacts,
            'clientProspects' => $clientProspects,
            'reglements' => $reglements,
            'status' => $status,
            'taxes' => $taxes,
            'exercices' => $exercices,
            'fichiers' => $fichiers
        ));
    }
    
    /**
     * Visualisation d'un devis
     * @param type $id
     * @return Response
     */
    public function showAction(Request $request, $id) {
        $service = new DevisService($this->getDoctrine()->getManager());

        $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $devis = $this->getDoctrine()
            ->getRepository('AppBundle:OneDevis')
            ->find($id);

        $devisDetails =  $service->getDevisDetailsByDevis($devis);

        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OneArticleVente'
            )->findBy(array('devis' => $devis));

        $contacts = $this->getDoctrine()
            ->getRepository('AppBundle:OneContactClient')
            ->getContacts($devis->getTiers()->getId());

        $modele = $this->getDoctrine()
            ->getRepository('AppBundle:OneDocumentModele')
            ->findOneByDevis($devis);

        $modeles = $this->getDoctrine()
            ->getRepository('AppBundle:OneModele')
            ->getModelesByDossier($dossier);

        $filename = $this->getParameter('one_documents_dir').'devis'.DIRECTORY_SEPARATOR.'Devis-N°'.$devis->getCode().'.pdf';

        $params = $this->getDoctrine()
            ->getRepository('AppBundle:OneParametre')
            ->find(1);

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        return $this->render('OneVenteBundle:Devis:show.html.twig', array(
            'devis' => $devis,
            'devisDetails' => $devisDetails,
            'modele' => $modele,
            'modeles' => $modeles,
            'articles' => $articles,
            'contacts' => $contacts,
            'filename' => $filename,
            'params' => $params,
            'dossier' => $dossier,
            'utilisateur' => $utilisateur
        ));
    }
    
    public function invoiceAction($id) {
        $em = $this->getDoctrine()->getManager();
        $venteService = new VenteService($this->getDoctrine()->getManager());
        $documentService = new DocumentService($this->getDoctrine()->getManager());
        
        //Récupération du devis actuel
        $devis = $em->getRepository('AppBundle:OneDevis')
            ->find($id);
        /** @var OneArticleVente[] $articles */
        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OneArticleVente')
            ->getArticlesDevis($id);
        $devis->setStatus(2);
        $em->flush();
        
        //Ajout de la facture
        $vente = new OneVente();
        $vente->setType(2);
        $vente->setTiers($devis->getTiers());
        $vente->setOneReglement($devis->getOneReglement());
        $vente->setStatusFacture(0);
        $vente->setRemise($devis->getRemise());
        $vente->setNote($devis->getNote());
        $vente->setCode($venteService->getNextCodeVente('facture'));
        $vente->setCreeLe(new \DateTime('now'));
        $vente->setDateFacture(new \DateTime('now'));
        $vente->setFichier(serialize(array()));
        
        if ($devis->getOneContactClient()) {
            $vente->setContact($devis->getOneContactClient());
        }
        
        $em->persist($vente);
        $em->flush();
        
        foreach($articles as $article) {
            $art = new OneArticleVente();
            $art->setVente($vente);
            $art->setDescription($article->getDescription());
            $art->setOneArticle($article->getOneArticle());
            $art->setPrix($article->getPrix());
            $art->setQuantite($article->getQuantite());
            $art->setRemise($article->getRemise());
            
            if ($article->getTvaTaux()) {
                $art->setTvaTaux($article->getTvaTaux());
            }
            
            $em->persist($art);
            $em->flush();
        }
        
        //Ajout d'un modèle de document standard
        $documentService->addDocumentModele('vente', $vente);
        
        //Ajout de la relation devis-facture
        $link = new OneInvoiceDevis();
        $link->setOneDevis($devis);
        $link->setOneVente($vente);
        
        //Changement du statut prospect à un client
        $cem = $this->getDoctrine()->getManager();
        $prospect = $cem->getRepository('AppBundle:Tiers')
            ->find($devis->getTiers()->getId());
        if($prospect->getType() === 4) {
            $prospect->setType(1);
        }
        $cem->flush();
        
        $em->persist($link);
        $em->flush();
        
        $response = array('type' => 'success', 'id' => $vente->getId());
        return new JsonResponse($response);
    }
    
    public function commandeAction($id) {
        $em = $this->getDoctrine()->getManager();
        $venteService = new VenteService($this->getDoctrine()->getManager());
        $documentService = new DocumentService($this->getDoctrine()->getManager());
        
        //Récupération du devis actuel
        $devis = $em->getRepository('AppBundle:OneDevis')->find($id);
        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OneArticleVente')
            ->getArticlesDevis($id);
        $devis->setStatus(2);
        $em->flush();
        
        //Ajout de la commande
        $vente = new OneVente();
        $vente->setType(1);
        $vente->setTiers($devis->getTiers());
        $vente->setOneReglement($devis->getOneReglement());
        $vente->setStatusBonCommande(0);
        $vente->setRemise($devis->getRemise());
        $vente->setNote($devis->getNote());
        $vente->setCode($venteService->getNextCodeVente('commande'));
        $vente->setCreeLe(new \DateTime('now'));
        $vente->setDateFacture(new \DateTime('now'));
        $vente->setFichier(serialize(array()));
        $vente->setExercice($devis->getExercice());

        if ($devis->getOneContactClient()) {
            $vente->setContact($devis->getOneContactClient());
        }
        
        $em->persist($vente);
        $em->flush();
        
        foreach($articles as $article) {
            $art = new OneArticleVente();
            $art->setVente($vente);
            $art->setDescription($article->getDescription());
            $art->setOneArticle($article->getOneArticle());
            $art->setPrix($article->getPrix());
            $art->setQuantite($article->getQuantite());
            $art->setRemise($article->getRemise());
            
            if ($article->getTvaTaux()) {
                $art->setTvaTaux($article->getTvaTaux());
            }
            
            $em->persist($art);
            $em->flush();
        }
        
        //Ajout d'un modèle de document standard
        $documentService->addDocumentModele('vente', $vente);
        
        //Ajout de la relation devis-commande
        $link = new OneCommandeDevis();
        $link->setOneDevis($devis);
        $link->setOneVente($vente);
        
        $em->persist($link);
        $em->flush();
        
        $response = array('type' => 'success', 'id' => $vente->getId());
        return new JsonResponse($response);
    }
    
    /**
     * Sauvegarde d'un devis
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request) {
        if ($request->getMethod() === 'POST') {
            $service = new DevisService($this->getDoctrine()->getManager());
            $articleService = new ArticleService($this->getDoctrine()->getManager());
            $documentService = new DocumentService($this->getDoctrine()->getManager());
            $fichierService = new FichierService($this->getDoctrine()->getManager());

            $posted = $request->request->all();

            $exercice = $posted['exercice-rattachement'];

            if($exercice === ""){
                $exercice = null;
            }

            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $devis = new OneDevis();
                    
                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);

                    $reglement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneReglement')
                        ->find($posted['reglement']);
                    
                    $devis->setTiers($clientProspect);
                    $devis->setOneReglement($reglement);
                    $devis->setStatus($posted['status']);
                    $devis->setRemise($posted['remise-ht']);
                    $devis->setMontant(str_replace(' ', '', $posted['montant-ttc']));
                    $devis->setNote($posted['note']);
                    $devis->setCode($service->getNextCode());
                    $devis->setCreeLe(new \DateTime('now'));
                    $devis->setDateDevis(\DateTime::createFromFormat('d/m/Y', $posted['date-devis']));
                    $devis->setFinValidite(\DateTime::createFromFormat('d/m/Y', $posted['fin-validite']));
                    $devis->setExercice((int) $exercice);
                    
                    if ((int)$posted['contact-client'] > 0) {
                        $contactClient = $this->getDoctrine()
                            ->getRepository('AppBundle:OneContactClient')
                            ->find($posted['contact-client']);
                        $devis->setOneContactClient($contactClient);
                    }


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

                    $devis->setFichier(serialize($filesID));
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($devis);
                    $em->flush();
                    
                    //Sauvegarde des articles
                    if (isset($posted['articles'])) {
                        foreach ($posted['articles'] as $article) {
                            $data = $articleService->parseArticleData($article);
                            $data['devis-id'] = (int)$devis->getId();
                            $articleService->saveArticleDevis($data);
                        }
                    }
                    
                    //Ajout d'un modèle de document standard
                    $documentService->addDocumentModele('devis', $devis);
                    
                    $response = array('type' => 'success', 'action' => 'add', 'id' => $devis->getId());
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            } else {
                $em = $this->getDoctrine()->getManager();
                $devis = $em->getRepository('AppBundle:OneDevis')->find($posted['id']);

//                if($devis->getImage() !== null){
//                    $response = array('type' => 'error', 'action' => 'edit', 'message' => 'Document déjà imprimé');
//                    return  new JsonResponse($response);
//                }

                try {

                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);
                    $reglement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneReglement')
                        ->find($posted['reglement']);
                    
                    $devis->setTiers($clientProspect);
                    $devis->setOneReglement($reglement);
                    $devis->setStatus($posted['status']);
                    $devis->setRemise($posted['remise-ht']);
                    $devis->setMontant(str_replace(' ', '', $posted['montant-ttc']));
                    $devis->setNote($posted['note']);
                    $devis->setDateDevis(\DateTime::createFromFormat('d/m/Y', $posted['date-devis']));
                    $devis->setFinValidite(\DateTime::createFromFormat('d/m/Y', $posted['fin-validite']));
                    $devis->setModifieLe(new \DateTime('now'));
                    $devis->setExercice((int) $exercice);
                    
                    if ((int)$posted['contact-client'] > 0) {
                        $contactClient = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->find($posted['contact-client']);
                        $devis->setOneContactClient($contactClient);
                    }


                    $filesID = array();
                    if (isset($posted['uploaded-files'])) {
                        foreach ($posted['uploaded-files'] as $file) {
                            $data = $fichierService->parseFile($file);
                            $filesID[] = $fichierService->saveData($data);
                        }
                    }

                    //Suppression des fichiers uploadés
                    if (isset($posted['deleted-files'])) {
                        $fem = $this->getDoctrine()->getManager();
                        foreach ($posted['deleted-files'] as $file) {
                            $fichier = $this->getDoctrine()
                                ->getRepository('AppBundle:OneFichier')
                                ->findOneByNom($file);
                            $fem->remove($fichier);
                            unlink($this->getParameter('one_upload_dir').$file);
                        }
                        $fem->flush();
                    }

                    $devis->setFichier(serialize($filesID));
                    
                    $em->flush();
                    
                    //Sauvegarde des articles
                    if (isset($posted['articles'])) {
                        foreach ($posted['articles'] as $article) {
                            $data = $articleService->parseArticleData($article);
                            $data['devis-id'] = (int)$devis->getId();
                            $articleService->saveArticleDevis($data);
                        }
                    }
                    
                    //Suppression des articles supprimés
                    if (isset($posted['deleted-articles'])) {
                        $rem = $this->getDoctrine()->getManager();
                        foreach ($posted['deleted-articles'] as $artid) {
                            $article = $this->getDoctrine()
                                ->getRepository('AppBundle:OneArticleVente')
                                ->find($artid);
                            $rem->remove($article);
                        }
                        $rem->flush();
                    }
                    
                    $response = array('type' => 'success', 'action' => 'edit', 'id' => $devis->getId());
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error',
                        'action' => 'edit',
                        'id' => $devis->getId(),
                        'message' => 'Modification non sauvegardée');
                    return new JsonResponse($response);
                }
            }
        }
    }
    
    /**
     * Suppression d'un devis
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $devis = $this->getDoctrine()->getRepository('AppBundle:OneDevis')->find($id);
            
            //Suppression des articles correspondants
            $articles = $this->getDoctrine()
                ->getRepository('AppBundle:OneArticleVente')
                ->findBy(array('devis' => $devis));
            foreach ($articles as $article) {
                $em->remove($article);
            }

            $documentModeles = $this->getDoctrine()
                ->getRepository('AppBundle:OneDocumentModele')
                ->findBy(array('devis' => $devis));
            foreach ($documentModeles as $documentModele){
                $em->remove($documentModele);
            }

            $em->remove($devis);
            $em->flush();


            //Suppression des personnalisation de document
            $document = $this->getDoctrine()->getRepository('AppBundle:OneDocumentModele')->findOneByDevis($devis);
            if ($document)
                $em->remove($document);
            
            $em->flush();
            
            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
    }
    
    /**
     * Adresse du devis en fonction d'un clientProspect ou d'un contact
     * @param Request $request
     * @return Response
     */
    public function addressAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('GET')) {
                $type = $request->query->get('type');
                $id = (int)$request->query->get('id');
                $service = new DevisService($this->getDoctrine()->getManager());
                $address = $service->getAddress($type, $id);
                return new Response($address);
            }
        }
    }
}