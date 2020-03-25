<?php

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\OneContactClient;
use AppBundle\Entity\OneProjet;
use AppBundle\Entity\OneReglement;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneVente;
use AppBundle\Entity\OnePaiement;
use AppBundle\Entity\OnePaiementDetail;
use One\VenteBundle\Service\VenteService;
use One\VenteBundle\Service\ArticleService;
use One\ProspectBundle\Service\FichierService;
use One\VenteBundle\Service\PaiementService;
use One\VenteBundle\Service\DocumentService;

class VenteController extends Controller
{
    public function indexAction() {
        return $this->render('OneVenteBundle:Vente:index.html.twig');
    }
    
    /**
     * Liste des factures
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
            /** @var Tiers[] $clientProspects */
            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);

            $imputations = $this->getDoctrine()
                ->getRepository('AppBundle:ImputationControle')
                ->getFactureClientsByDossier($dossier, $exercice, $q, $period, $startperiod, $endperiod );


            /** @var OneVente[] $paid */
            $paid = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVenteByStatus($clientProspects, $exercice,'facture', 'paid');
            /** @var OneVente[] $unpaid */
            $unpaid = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVenteByStatus($clientProspects, $exercice, 'facture', 'unpaid');
            /** @var OneVente[] $factures */
            $factures = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVentes($clientProspects, $exercice,'facture', $sort, $sortorder, $q, $period, $startperiod, $endperiod, $stat);

            return $this->render('OneVenteBundle:Vente:list.html.twig', array(
                'paid' => $paid,
                'unpaid' => $unpaid,
                'factures' => $factures,
                'stat' => $stat,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
                'venteDetails' => $service->getVenteDetails($clientProspects, $exercice,'facture'),
                'imputations' => $imputations
            ));
        }
    }
    
    /**
     * Ajout d'une nouvelle facture
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
                /** @var OneReglement[] $reglements */
                $reglements = $this->getDoctrine()
                    ->getRepository('AppBundle:OneReglement')
                    ->getReglements();
                /** @var OneContactClient[] $contacts */
                $contacts = $this->getDoctrine()
                    ->getRepository('AppBundle:OneContactClient')
                    ->getContacts($parentid);
                /** @var OneProjet[] $projets */
                $projets = $this->getDoctrine()
                    ->getRepository('AppBundle:OneProjet')
                    ->getProjets();

                $exercices = Boost::getExercices(6,1);

                return $this->render('OneVenteBundle:Vente:new.html.twig', array(
                    'clientProspects' => $clientProspects,
                    'reglements' => $reglements,
                    'parent' => $parent,
                    'parentid' => $parentid,
                    'contacts' => $contacts,
                    'projets' => $projets,
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
            /** @var OneProjet[] $projets */
            $projets = $this->getDoctrine()
                ->getRepository('AppBundle:OneProjet')
                ->getProjets();

            return $this->render('OneVenteBundle:Vente:new.html.twig', array(
                'clientProspects' => $clientProspects,
                'reglements' => $reglements,
                'parent' => $parent,
                'parentid' => $parentid,
                'contacts' => array(),
                'projets' => $projets,
                'exercices' => $exercices,
                'exercice' => $exercice
            ));
        }
    }
    
    public function editAction(Request $request, $id, $one) {

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $exercice = $request->query->get('exercice');

        $exercices = Boost::getExercices(6,1);
        //fin lesexperts.biz


        if((int)$one === 1) {
            /** @var Tiers[] $clientProspects */
            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);

            $service = new VenteService($this->getDoctrine()->getManager());
//            $venteDetails = $service->getVenteDetails($clientProspects, $exercice, 'facture')[$id];

            $paiementService = new PaiementService($this->getDoctrine()->getManager());
            $vente = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->find($id);
            $venteDetails = $service->getVenteDetailsByVente($vente);

            $articles = $this->getDoctrine()
                ->getRepository('AppBundle:OneArticleVente')
                ->getArticlesVente($id);
            $contacts = array();

            if($vente->getTiers() !== null) {
                $contacts = $this->getDoctrine()
                    ->getRepository('AppBundle:OneContactClient')
                    ->getContacts($vente->getTiers()->getId());
            }

            $reglements = $this->getDoctrine()
                ->getRepository('AppBundle:OneReglement')
                ->getReglements();

            $fichiers = $this->getDoctrine()
                ->getRepository('AppBundle:OneFichier')
                ->getFiles(unserialize($vente->getFichier()));

            $projets = $this->getDoctrine()
                ->getRepository('AppBundle:OneProjet')
                ->getProjets();

            $moyenpaiements = $this->getDoctrine()
                ->getRepository('AppBundle:OneMoyenPaiement')
                ->getMoyenPaiements();

            $banquecomptes = $this->getDoctrine()
                ->getRepository('AppBundle:BanqueCompte')
                ->findBy(array('dossier'=> $dossier));

            $taxes = $this->getDoctrine()
                ->getRepository('AppBundle:OneTva')->getTva();

            $paiements = $this->getDoctrine()
                ->getRepository('AppBundle:OnePaiement')
                ->findBy(array('oneVente' => $vente));

            $totalpaid = 0;
            foreach ($paiements as $paiement) {
                $totalpaid += $paiement->getMontant();
            }
            return $this->render('OneVenteBundle:Vente:edit.html.twig', array(
                'vente' => $vente,
                'venteDetails' => $venteDetails,
                'articles' => $articles,
                'contacts' => $contacts,
                'clientProspects' => $clientProspects,
                'reglements' => $reglements,
                'fichiers' => $fichiers,
                'projets' => $projets,
                'moyenpaiements' => $moyenpaiements,
                'banquecomptes' => $banquecomptes,
                'paiements' => $paiements,
                'totalpaid' => $totalpaid,
                'paiementType' => $paiementService->getPaiementType(),
                'taxes' => $taxes,
                'exercices' => $exercices
            ));
        }

        $imputation = $this->getDoctrine()
            ->getRepository('AppBundle:ImputationControle')
            ->find($id);

        $tvaImputations = null;
        $tvaImputation = null;
        $tiers = null;
        $totalHt = 0;
        $totalTtc = 0;
        $totalTva = 0;

        if(null !== $imputation) {
            $tvaImputations = $this->getDoctrine()
                ->getRepository('AppBundle:TvaImputationControle')
                ->findBy(array('image' => $imputation->getImage()));

            if (count($tvaImputations) > 0) {
                $tvaImputation = $tvaImputations[0];
                $tiers = $tvaImputation->getTiers();
            }

            foreach ($tvaImputations as $tvaImp){
                $ht =$tvaImp->getMontantHt();
                $taux = ($tvaImp->getTvaTaux() !== null) ? $tvaImp->getTvaTaux()->getTaux() : 0;
                $tva = ($ht * $taux / 100);
                $ttc = $tva + $ht;
                $totalTva += $tva;
                $totalHt += $ht;
                $totalTtc += $ttc;
            }

            return $this->render('OneVenteBundle:Vente:editPicDoc.html.twig', array(
                'imputation' => $imputation,
                'tvaImputations' => $tvaImputations,
                'tiers' => $tiers,
                'totalHt' => $totalHt,
                'totalTtc' => $totalTtc,
                'totalTva' => $totalTva
            ));
        }
    }
    
    /**
     * Visualisation d'une facture
     * @param type $id
     * @return Response
     */
    public function showAction(Request $request, $id) {

        $service = new VenteService($this->getDoctrine()->getManager());
//        $venteDetails = $service->getVenteDetails($clientProspects, $exercice, 'facture')[$id];
        /** @var OneVente $vente */
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

        $filename = $this->getParameter('one_documents_dir').'facture'.DIRECTORY_SEPARATOR.'Facture-N°'.$vente->getCode().'.pdf';
        $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);
        return $this->render('OneVenteBundle:Vente:show.html.twig', array(
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
     * Sauvegarde d'une facture
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request) {
        if ($request->getMethod() === 'POST') {
            $service = new VenteService($this->getDoctrine()->getManager());
            $articleService = new ArticleService($this->getDoctrine()->getManager());
            $fichierService = new FichierService($this->getDoctrine()->getManager());
            $documentService = new DocumentService($this->getDoctrine()->getManager());
            $em = $this->getDoctrine()->getManager();
            $posted = $request->request->all();


            //debut lesexperts.biz
            $dossierId = Boost::deboost($posted['id-dossier'], $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $exercice = $posted['exercice-rattachement'];
            if($exercice === ""){
                $exercice = null;
            }

            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);
            //fin lesexperts.biz


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
                    
                    $vente->setType(2);
                    $vente->setTiers($clientProspect);
                    $vente->setOneReglement($reglement);
                    $vente->setStatusFacture($posted['status']);
                    $vente->setRemise($posted['remise-ht']);
                    $vente->setNote($posted['note']);
                    $vente->setCode($service->getNextCodeVente('facture'));
                    $vente->setCreeLe(new \DateTime('now'));
                    $vente->setExercice($exercice);
                    
                    if ($posted['date-facture'] != '')
                        $vente->setDateFacture(\DateTime::createFromFormat('j/m/Y', $posted['date-facture']));
                    else
                        $vente->setDateFacture(new \DateTime('now'));
                    
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
                    
//                    if (intval($posted['projet']) > 0) {
//                        $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($posted['projet']);
//                        $vente->setOneProjet($projet);
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
                    
                    $vente->setFichier(serialize($filesID));
                    
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
                    
                    //Sauvegarde des paiements
                    if (isset($posted['paiement'])) {
                        $pids = array();
                        $paiementService = new PaiementService($this->getDoctrine()->getManager());
                        foreach ($posted['paiement'] as $item) {
                            $data = explode(';', $item);
                            
                            //Ajout
                            if ((int)$data[0] == 0) {
                                $moyenpaiement = $this->getDoctrine()
                                    ->getRepository('AppBundle:OneMoyenPaiement')
                                    ->find($data[4]);

                                $paiement = new OnePaiement();
                                $paiement->setCode($paiementService->getNextCode());
                                $paiement->setOneVente($vente);
                                $paiement->setOneMoyenPaiement($moyenpaiement);
                                $paiement->setMontant((float)$data[3]);
                                $paiement->setRefBanque($data[5]);
                                $paiement->setRetard($data[6]);

                                if ($data[2] != '')
                                    $paiement->setDateReception(\DateTime::createFromFormat('j/m/Y', $data[2]));
                                else
                                    $paiement->setDateReception(new \DateTime('now'));
                                
                                if ($data[7] != '') {
                                    $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($data[7]);
                                    $paiement->setBanqueCompte($banqueCompte);
                                }
                                
                                $em->persist($paiement);
                                $em->flush();
                                $pids[] = $paiement->getId();
                                
                                //Ajout détail paiement par encaissement
                                if((int)$data[8] > 0) {
                                    $paiementDetail = new OnePaiementDetail();
                                    $encaissement = $this->getDoctrine()
                                        ->getRepository('AppBundle:OneEncaissement')
                                        ->find($data[8]);
                                    $paiementDetail->setOnePaiement($paiement);
                                    $paiementDetail->setOneEncaissement($encaissement);
                                    $em->persist($paiementDetail);
                                    $em->flush();
                                }

                                //Ajout détail paiement par avoir
                                if((int)$data[9] > 0) {
                                    $paiementDetail = new OnePaiementDetail();
                                    $avoir = $this->getDoctrine()
                                        ->getRepository('AppBundle:OneVente')
                                        ->find($data[9]);
                                    $paiementDetail->setOnePaiement($paiement);
                                    $paiementDetail->setOneAvoir($avoir);
                                    $em->persist($paiementDetail);
                                    $em->flush();
                                }
                                
                                //Mise à jour du statut de la facture
                                if($paiementService->checkFacturePaiement($vente, $service->getVenteAmounts($clientProspects, $exercice,'facture')[$vente->getId()])) {
                                    $vente->setStatusFacture(1);
                                    $em->flush();
                                }
                            }
                        }
                    }
                    
                    //Ajout d'un modèle de document standard
                    $documentService->addDocumentModele('vente', $vente);
                    
                    if (!isset($pids)) $pids = array();
                    $response = array('type' => 'success', 'action' => 'add', 'id' => $vente->getId(), 'pids' => $pids);
                    return new JsonResponse($response);
                } catch (Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            }
            
            //Edition
            else {
                try {
                    $vem = $this->getDoctrine()->getManager();
                    $vente = $vem->getRepository('AppBundle:OneVente')->find($posted['id']);
                    
                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);
                    $reglement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneReglement')
                        ->find($posted['reglement']);
                    
                    $vente->setType(2);
                    $vente->setTiers($clientProspect);
                    $vente->setOneReglement($reglement);
                    $vente->setStatusFacture($posted['status']);
                    $vente->setRemise($posted['remise-ht']);
                    $vente->setNote($posted['note']);
                    $vente->setModifieLe(new \DateTime('now'));
                    $vente->setExercice($exercice);
                    
                    if ($posted['date-facture'] != '')
                        $vente->setDateFacture(\DateTime::createFromFormat('d/m/Y', $posted['date-facture']));
                    else
                        $vente->setDateFacture(new \DateTime('now'));
                    
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
                    
//                    if (intval($posted['projet']) > 0) {
//                        $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($posted['projet']);
//                        $vente->setOneProjet($projet);
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
                    
                    $vente->setFichier(serialize($filesID));
                    
                    //$vem->flush();
                    
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
                        $aem = $this->getDoctrine()->getManager();
                        foreach ($posted['deleted-articles'] as $artid) {
                            $article = $this->getDoctrine()
                                ->getRepository('AppBundle:OneArticleVente')
                                ->find($artid);
                            $aem->remove($article);
                        }
                        $aem->flush();
                    }
                    
                    //Sauvegarde des paiements
                    if (isset($posted['paiement'])) {
                        $pids = array();
                        $pem = $this->getDoctrine()->getManager();
                        $paiementService = new PaiementService($this->getDoctrine()->getManager());
                        foreach ($posted['paiement'] as $item) {
                            $data = explode(';', $item);
                            //Ajout
                            if ((int)$data[1] == 0) {
                                $moyenpaiement = $this->getDoctrine()
                                    ->getRepository('AppBundle:OneMoyenPaiement')
                                    ->find($data[4]);
                                $paiement = new OnePaiement();
                                $paiement->setCode($paiementService->getNextCode());
                                $paiement->setOneVente($vente);
                                $paiement->setOneMoyenPaiement($moyenpaiement);
                                $paiement->setMontant((float)$data[3]);
                                $paiement->setRefBanque($data[5]);
                                $paiement->setRetard($data[6]);
                                
                                if ($data[2] != '')
                                    $paiement->setDateReception(\DateTime::createFromFormat('d/m/Y', $data[2]));
                                else
                                    $paiement->setDateReception(new \DateTime('now'));
                                
                                if ($data[7] != '') {
                                    $banqueCompte = $this->getDoctrine()
                                        ->getRepository('AppBundle:BanqueCompte')
                                        ->find($data[7]);
                                    $paiement->setBanqueCompte($banqueCompte);
                                }
                                
                                $pem->persist($paiement);
                                $pem->flush();
                                $pids[] = $paiement->getId();
                                
                                //Ajout détail paiement par encaissement
                                if((int)$data[8] > 0) {
                                    $paiementDetail = new OnePaiementDetail();
                                    $encaissement = $this->getDoctrine()
                                        ->getRepository('AppBundle:OneEncaissement')
                                        ->find($data[8]);
                                    $paiementDetail->setOnePaiement($paiement);
                                    $paiementDetail->setOneEncaissement($encaissement);
                                    $pem->persist($paiementDetail);
                                    $pem->flush();
                                }

                                //Ajout détail paiement par avoir
                                if((int)$data[9] > 0) {
                                    $paiementDetail = new OnePaiementDetail();
                                    $avoir = $this->getDoctrine()
                                        ->getRepository('AppBundle:OneVente')
                                        ->find($data[9]);
                                    $paiementDetail->setOnePaiement($paiement);
                                    $paiementDetail->setOneAvoir($avoir);
                                    $pem->persist($paiementDetail);
                                    $pem->flush();
                                }
                                
                                //Ajout d'un modèle de document standard
                                $documentService->addDocumentModele('paiement', $paiement);
                                
                                //Mise à jour du statut de la facture
//                                if($paiementService->checkFacturePaiement($vente, $service->getVenteDetails($clientProspects, $exercice,'facture')[$vente->getId()]['ttc'])) {
//                                    $vente->setStatusFacture(1);
//                                }

                                if($paiementService->checkFacturePaiement($vente, $service->getVenteDetailsByVente($vente)['ttc'])) {
                                    $vente->setStatusFacture(1);
                                }

                            }
                            
                            //Edition
                            else {
                                $paiement = $em->getRepository('AppBundle:OnePaiement')->find($data[1]);
                                $moyenpaiement = $this->getDoctrine()
                                    ->getRepository('AppBundle:OneMoyenPaiement')
                                    ->find($data[4]);
                                $paiement->setOneVente($vente);
                                $paiement->setOneMoyenPaiement($moyenpaiement);
                                $paiement->setMontant((float)$data[3]);
                                $paiement->setRefBanque($data[5]);
                                $paiement->setRetard($data[6]);
                                
                                if ($data[2] != '')
                                    $paiement->setDateReception(\DateTime::createFromFormat('d/m/Y', $data[2]));
                                else
                                    $paiement->setDateReception(new \DateTime('now'));
                                
                                if ($data[7] != '') {
                                    $banqueCompte = $this->getDoctrine()
                                        ->getRepository('AppBundle:BanqueCompte')
                                        ->find($data[7]);
                                    $paiement->setBanqueCompte($banqueCompte);
                                }
                                $pem->flush();
                                $pids[] = $paiement->getId();
                                
                                //Mise à jour du statut de la facture
//                                if($paiementService->checkFacturePaiement($vente, $service->getVenteDetails($clientProspects, $exercice,'facture')[$vente->getId()]['ttc'])) {
//                                    $vente->setStatusFacture(1);
//                                }
                                if($paiementService->checkFacturePaiement($vente, $service->getVenteDetailsByVente($vente)['ttc'])) {
                                    $vente->setStatusFacture(1);
                                }

                            }
                        }
                    }
                    
                    //Suppression des paiements supprimés
                    if (isset($posted['deleted-paiement'])) {
                        $pem = $this->getDoctrine()->getManager();
                        $paiementService = new PaiementService($this->getDoctrine()->getManager());
                        foreach($posted['deleted-paiement'] as $pid) {
                            $paiement = $this->getDoctrine()
                                ->getRepository('AppBundle:OnePaiement')
                                ->find($pid);
                            //Suppression des détails du paiement
                            $details = $this->getDoctrine()
                                ->getRepository('AppBundle:OnePaiementDetail')
                                ->findByOnePaiement($paiement);
                            foreach ($details as $detail) {
                                $pem->remove($detail);
                            }
                            //Suppression des personnalisation de document
                            $document = $this->getDoctrine()
                                ->getRepository('AppBundle:OneDocumentModele')
                                ->findOneByPaiement($paiement);
                            if ($document)
                                $pem->remove($document);
                            //Suppression du paiement
                            $pem->remove($paiement);
                        }
                        $pem->flush();
                        
                        //Mise à jour du statut de la facture
//                        if(!$paiementService->checkFacturePaiement($vente, $service->getVenteDetails($clientProspects, $exercice,'facture')[$vente->getId()]['ttc'])) {
//                            $vente->setStatusFacture(0);
//                        }

                        if(!$paiementService->checkFacturePaiement($vente, $service->getVenteDetailsByVente($vente)[$vente->getId()]['ttc'])) {
                            $vente->setStatusFacture(0);
                        }

                    }
                    
                    $vem->flush();
                    
                    if (!isset($pids)) $pids = array();
                    $response = array('type' => 'success', 'action' => 'edit', 'id' => $posted['id'], 'pids' => $pids);
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                }
            }
        }
    }
    
    /**
     * Suppression d'une facture
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
            
            //Suppression des paiements correspondants
            $paiements = $this->getDoctrine()->getRepository('AppBundle:OnePaiement')->findByOneVente($vente);
            foreach ($paiements as $paiement) {
                $details = $this->getDoctrine()->getRepository('AppBundle:OnePaiementDetail')->findByOnePaiement($paiement);
                foreach ($details as $detail) {
                    $em->remove($detail);
                }
                $paiement_document = $this->getDoctrine()->getRepository('AppBundle:OneDocumentModele')->findOneByPaiement($paiement);
                $em->remove($paiement_document);
                $em->remove($paiement);
            }
            
            //Suppression des personnalisation de document
            $document = $this->getDoctrine()->getRepository('AppBundle:OneDocumentModele')->findOneByVente($vente);
            if ($document)
                $em->remove($document);
            
            //Suppression des liens de devis
            $linkDevis = $this->getDoctrine()->getRepository('AppBundle:OneInvoiceDevis')->findOneByOneVente($vente);
            if ($linkDevis)
                $em->remove($linkDevis);
            
            //Suppression des liens de commande
            $linkCommande = $this->getDoctrine()->getRepository('AppBundle:OneInvoiceCommande')->findOneByOneVente($vente);
            if ($linkCommande)
                $em->remove($linkCommande);
            
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
    
    /**
     * Liste des factures non payées
     * @param Request $request
     * @return Response
     */
    public function unpaidAction(Request $request) {
        if ($request->isMethod('GET')) {
            $service = new VenteService($this->getDoctrine()->getManager());
            $paiementService = new PaiementService($this->getDoctrine()->getManager());
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

            $exercice = $request->query->get('exercice');

            /** @var Tiers[] $clientProspects */
            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);
            //fin lesexperts.biz

            /** @var OneVente[] $factures */
            $factures = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVentesForPaiement($clientProspects, $exercice ,$sort, $sortorder, $q, $period, $startperiod, $endperiod);
            return $this->render('OneVenteBundle:Vente:unpaid.html.twig', array(
                'factures' => $factures,
                'venteDetails' => $service->getVenteDetails($clientProspects, $exercice,'facture'),
                'paidAmounts' => $paiementService->getPaidAmounts($clientProspects, $exercice),
            ));
        }
    }

}
