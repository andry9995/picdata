<?php

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneVente;
use AppBundle\Entity\OneArticleVente;
use AppBundle\Entity\OneInvoiceCommande;
use One\VenteBundle\Service\VenteService;
use One\VenteBundle\Service\ArticleService;
use One\ProspectBundle\Service\FichierService;
use One\VenteBundle\Service\DocumentService;

class CommandeController extends Controller
{
    /**
     * Liste des commandes clients
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request) {
        if ($request->isMethod('GET')) {
            $service = new VenteService($this->getDoctrine()->getManager());
            $stat = $request->query->get('stat');
            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');
            $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);


            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $exercice = $request->query->get('exercice');

            if(null === $dossier){
                return new Response('');
            }

            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);


            $uninvoiced = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVenteByStatus($clientProspects, $exercice,'commande', 'uninvoiced');
            $invoiced = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVenteByStatus($clientProspects, $exercice,'commande', 'invoiced');
            $unshipped = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVenteByStatus($clientProspects, $exercice,'commande', 'unshipped');
            $shipped = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVenteByStatus($clientProspects, $exercice, 'commande', 'shipped');
            /** @var OneVente[] $commandes */
            $commandes = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVentes($clientProspects, $exercice, 'commande', $sort, $sortorder, $q, $period, $startperiod, $endperiod, $stat);
            return $this->render('OneVenteBundle:Commande:list.html.twig', array(
                'uninvoiced' => $uninvoiced,
                'invoiced' => $invoiced,
                'unshipped' => $unshipped,
                'shipped' => $shipped,
                'commandes' => $commandes,
                'stat' => $stat,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
//                'invoiced' => $service->getInvoicedCommande(),
                'venteDetails' => $service->getVenteDetails($clientProspects, $exercice,'commande'),
            ));
        }
    }
    
    /**
     * Ajout d'une nouvelle commande
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
            if ($parent === 'client') {
                /** @var Tiers[] $clientProspects */
                $clientProspects = $this->getDoctrine()
                    ->getRepository('AppBundle:Tiers')
                    ->getClientProspects($dossier);

                $reglements = $this->getDoctrine()
                    ->getRepository('AppBundle:OneReglement')
                    ->getReglements();

                $contacts = $this->getDoctrine()
                    ->getRepository('AppBundle:OneContactClient')
                    ->getContacts($parentid);
                return $this->render('OneVenteBundle:Commande:new.html.twig', array(
                    'clientProspects' => $clientProspects,
                    'reglements' => $reglements,
                    'parent' => $parent,
                    'parentid' => $parentid,
                    'contacts' => $contacts,
                    'exercice' => $exercice,
                    'exercices' => $exercices
                ));
            } 
            
            //Aucun parent
            /** @var Tiers[] $clientProspects */
            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);

            $reglements = $this->getDoctrine()
                ->getRepository('AppBundle:OneReglement')
                ->getReglements();
            return $this->render('OneVenteBundle:Commande:new.html.twig', array(
                'clientProspects' => $clientProspects,
                'reglements' => $reglements,
                'parent' => $parent,
                'parentid' => $parentid,
                'contacts' => array(),
                'exercice' => $exercice,
                'exercices' => $exercices
            ));
        }
    }
    
    public function editAction(Request $request, $id) {

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);


        $exercices = Boost::getExercices(6,1);
        //fin lesexperts.biz

        /** @var Tiers[] $clientProspects */
        $clientProspects = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->getClients($dossier);

        $service = new VenteService($this->getDoctrine()->getManager());
//        $venteDetails = $service->getVenteDetails($clientProspects, $exercice,'commande')[$id];
        $vente = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
        $venteDetails = $service->getVenteDetailsByVente($vente);
        $articles = $this->getDoctrine()->getRepository('AppBundle:OneArticleVente')->getArticlesVente($id);
        $contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->getContacts($vente->getTiers()->getId());


        $reglements = $this->getDoctrine()->getRepository('AppBundle:OneReglement')->getReglements();
        $fichiers = $this->getDoctrine()->getRepository('AppBundle:OneFichier')->getFiles(unserialize($vente->getFichier()));
        $taxes = $this->getDoctrine()->getRepository('AppBundle:OneTva')->getTva();
        return $this->render('OneVenteBundle:Commande:edit.html.twig', array(
            'vente' => $vente,
            'venteDetails' => $venteDetails,
            'articles' => $articles,
            'contacts' => $contacts,
            'clientProspects' => $clientProspects,
            'reglements' => $reglements,
            'fichiers' => $fichiers,
            'taxes' => $taxes,
            'exercices' => $exercices
        ));
    }
    
    /**
     * Visualisation d'une commande
     * @param type $id
     * @return Response
     */
    public function showAction(Request $request, $id) {

        $service = new VenteService($this->getDoctrine()->getManager());

        /** @var OneVente $vente */
        $vente = $this->getDoctrine()
            ->getRepository('AppBundle:OneVente')
            ->find($id);
        $venteDetails = $service->getVenteDetailsByVente($vente);
        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OneArticleVente')
            ->findBy(array('vente'  => $vente));

        $contacts = $this->getDoctrine()
            ->getRepository('AppBundle:OneContactClient')
            ->getContacts($vente->getTiers()->getId());

        $modele = $this->getDoctrine()
            ->getRepository('AppBundle:OneDocumentModele')
            ->findOneByVente($vente);


        $dossierId = $request->query->get('dossierId');

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($dossierId, $this));

        $modeles = $this->getDoctrine()
            ->getRepository('AppBundle:OneModele')
            ->getModelesByDossier($dossier);

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();



        $filename = $this->getParameter('one_documents_dir').'commande'.DIRECTORY_SEPARATOR.'Commande-N°'.$vente->getCode().'.pdf';
        $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);
        return $this->render('OneVenteBundle:Commande:show.html.twig', array(
            'vente' => $vente,
            'venteDetails' => $venteDetails,
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
    
    /**
     * Visualisation d'un bon de livraison
     * @param type $id
     * @return Response
     */
    public function showshippedAction(Request $request, $id) {

        $service = new VenteService($this->getDoctrine()->getManager());
//        $venteDetails = $service->getVenteDetails($clientProspects, $exercice, 'commande')[$id];
        $vente = $this->getDoctrine()
            ->getRepository('AppBundle:OneVente')
            ->find($id);
        $venteDetails = $service->getVenteDetailsByVente($vente);
        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OneArticleVente')
            ->findBy(array('vente' => $vente));

        $contacts = $this->getDoctrine()
            ->getRepository('AppBundle:OneContactClient')
            ->getContacts($vente->getTiers()->getId());
        $modele = $this->getDoctrine()
            ->getRepository('AppBundle:OneDocumentModele')
            ->findOneByVente($vente);

        $dossierId = $request->query->get('dossierId');
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($dossierId, $this));

        $modeles = $this->getDoctrine()
            ->getRepository('AppBundle:OneModele')
            ->getModelesByDossier($dossier);

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        $filename = $this->getParameter('one_documents_dir').'livraison'.DIRECTORY_SEPARATOR.'BonLivraison-N°'.$vente->getCode().'.pdf';
        $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);
        return $this->render('OneVenteBundle:Commande:showshipped.html.twig', array(
            'vente' => $vente,
            'venteDetails' => $venteDetails,
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
        
        //Récupération de la commande actuelle
        $commande = $em->getRepository('AppBundle:OneVente')->find($id);
        $articles = $this->getDoctrine()->getRepository('AppBundle:OneArticleVente')->getArticlesVente($id);
        $commande->setStatusBonCommande(1);
        $em->flush();
        
        //Ajout de la facture
        $vente = new OneVente();
        $vente->setType(2);
        $vente->setTiers($commande->getTiers());
        $vente->setOneReglement($commande->getOneReglement());
        $vente->setStatusFacture(0);
        $vente->setRemise($commande->getRemise());
        $vente->setNote($commande->getNote());
        $vente->setCode($venteService->getNextCodeVente('facture'));
        $vente->setCreeLe(new \DateTime('now'));
        $vente->setDateFacture(new \DateTime('now'));
        $vente->setFichier(serialize(array()));
        $vente->setExercice($commande->getExercice());
        
        if ($commande->getContact()) {
            $vente->setContact($commande->getContact());
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
        $link = new OneInvoiceCommande();
        $link->setOneCommande($commande);
        $link->setOneVente($vente);
        
        //Changement du statut prospect à un client
        $cem = $this->getDoctrine()->getManager();
        $prospect = $cem->getRepository('AppBundle:Tiers')
            ->find($commande->getTiers()->getId());
        if($prospect->getType() === 4) {
            $prospect->setType(1);
        }
        $cem->flush();
        
        $em->persist($link);
        $em->flush();
        
        $response = array('type' => 'success', 'id' => $vente->getId());
        return new JsonResponse($response);
    }
    
    public function shipAction($id) {
        $em = $this->getDoctrine()->getManager();
        
        //Récupération de la commande actuelle
        $commande = $em->getRepository('AppBundle:OneVente')->find($id);
        $commande->setStatusBonCommande(2);
        $em->flush();
        
        $response = array('type' => 'success', 'id' => $id);
        return new JsonResponse($response);
    }
    
    /**
     * Sauvegarde d'une facture
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $service = new VenteService($this->getDoctrine()->getManager());
            $articleService = new ArticleService($this->getDoctrine()->getManager());
            $fichierService = new FichierService($this->getDoctrine()->getManager());
            $documentService = new DocumentService($this->getDoctrine()->getManager());
            $posted = $request->request->all();

            $exercice = $posted['exercice-rattachement'];
            if($exercice === ""){
                $exercice = null;
            }
            
            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $vente = new OneVente();
                    
                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);
                    $reglement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneReglement')
                        ->find($posted['reglement']);
                    
                    $vente->setType(1);
                    $vente->setTiers($clientProspect);
                    $vente->setOneReglement($reglement);
                    $vente->setStatusBonCommande($posted['status']);
                    $vente->setRemise($posted['remise-ht']);
                    $vente->setNote($posted['note']);
                    $vente->setCode($service->getNextCodeVente('commande'));
                    $vente->setCreeLe(new \DateTime('now'));
                    $vente->setExercice($exercice);
                    
                    if ($posted['date-facture'] != '')
                        $vente->setDateFacture(\DateTime::createFromFormat('d/m/Y', $posted['date-facture']));
                    else
                        $vente->setDateFacture(new \DateTime('now'));
                    
                    if ($posted['date-expedition'] != '')
                        $vente->setDateExpedition(\DateTime::createFromFormat('d/m/Y', $posted['date-expedition']));
                    else
                        $vente->setDateExpedition(new \DateTime('0000-00-00'));
                    
                    if ((int)$posted['contact-client'] > 0) {
                        $contactClient = $this->getDoctrine()
                            ->getRepository('AppBundle:OneContactClient')
                            ->find($posted['contact-client']);
                        $vente->setContact($contactClient);
                    }
                    
                    if ((int)$posted['contact-livraison'] > 0) {
                        $contactLivraison = $this->getDoctrine()
                            ->getRepository('AppBundle:OneContactClient')
                            ->find($posted['contact-livraison']);
                        $vente->setContactLivraison($contactLivraison);
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
                    
                    $vente->setFichier(serialize($filesID));
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($vente);
                    $em->flush();
                    
                    //Sauvegarde des articles
                    if (isset($posted['articles'])) {
                        foreach ($posted['articles'] as $article) {
                            $data = $articleService->parseArticleData($article);
                            $data['vente-id'] = (int)$vente->getId();
                            $articleService->saveArticleVente($data);
                        }
                    }
                    
                    //Ajout d'un modèle de document standard
                    $documentService->addDocumentModele('vente', $vente);
                    
                    $response = array('type' => 'success', 'action' => 'add', 'id' => $vente->getId());
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            } else {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $vente = $em->getRepository('AppBundle:OneVente')->find($posted['id']);
                    
                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);

                    $reglement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneReglement')
                        ->find($posted['reglement']);
                    
                    $vente->setType(1);
                    $vente->setTiers($clientProspect);

                    $vente->setOneReglement($reglement);
                    $vente->setStatusBonCommande($posted['status']);
                    $vente->setRemise($posted['remise-ht']);
                    $vente->setNote($posted['note']);
                    $vente->setModifieLe(new \DateTime('now'));
                    $vente->setExercice($exercice);
                    
                    if ($posted['date-facture'] != '')
                        $vente->setDateFacture(\DateTime::createFromFormat('d/m/Y', $posted['date-facture']));
                    else
                        $vente->setDateFacture(new \DateTime('now'));
                    
                    if ($posted['date-expedition'] != '')
                        $vente->setDateExpedition(\DateTime::createFromFormat('d/m/Y', $posted['date-expedition']));
                    else
                        $vente->setDateExpedition(new \DateTime('0000-00-00'));
                    
                    if ((int)$posted['contact-client'] > 0) {
                        $contactClient = $this->getDoctrine()
                            ->getRepository('AppBundle:OneContactClient')
                            ->find($posted['contact-client']);
                        $vente->setContact($contactClient);
                    }
                    
                    if ((int)$posted['contact-livraison'] > 0) {
                        $contactLivraison = $this->getDoctrine()
                            ->getRepository('AppBundle:OneContactClient')
                            ->find($posted['contact-livraison']);
                        $vente->setContactLivraison($contactLivraison);
                    }
                    
                    $em->flush();
                    
                    //Sauvegarde des articles
                    if (isset($posted['articles'])) {
                        foreach ($posted['articles'] as $article) {
                            $data = $articleService->parseArticleData($article);
                            $data['vente-id'] = (int)$vente->getId();
                            $articleService->saveArticleVente($data);
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
     * Suppression d'une commande
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $vente = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
            $em->remove($vente);
            
            //Suppression des articles correspondants
            $articles = $this->getDoctrine()->getRepository('AppBundle:OneArticleVente')->findByVente($vente);
            foreach ($articles as $article) {
                $em->remove($article);
            }
            
            //Suppression des fichiers correspondants
            $filesID = unserialize($vente->getFichier());
            if (count($filesID) > 0) {
                foreach ($filesID as $fileID) {
                    $file = $this->getDoctrine()->getRepository('AppBundle:OneFichier')->find($fileID);
                    unlink($this->getParameter('one_upload_dir').$file->getNom());
                    $em->remove($file);
                }
            }
            
            //Suppression des personnalisation de document
            $document = $this->getDoctrine()->getRepository('AppBundle:OneDocumentModele')->findOneByVente($vente);
            if ($document)
                $em->remove($document);
            
            //Suppression des liens de devis
            $link = $this->getDoctrine()->getRepository('AppBundle:OneCommandeDevis')->findOneByOneVente($vente);
            if ($link)
                $em->remove($link);
            
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
                $id = intval($request->query->get('id'));
                $service = new VenteService($this->getDoctrine()->getManager());
                $address = $service->getAddress($type, $id);
                return new Response($address);
            }
        }
    }
}
