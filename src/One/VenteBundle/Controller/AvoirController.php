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
use One\VenteBundle\Service\VenteService;
use One\VenteBundle\Service\AvoirService;
use One\VenteBundle\Service\ArticleService;
use One\ProspectBundle\Service\FichierService;
use One\VenteBundle\Service\DocumentService;

class AvoirController extends Controller
{
    /**
     * Liste des avoirs
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

            //debut lesexperts.biz
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
            //fin lesexperts.biz

            $paid = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVenteByStatus($clientProspects, $exercice, 'avoir', 'paid');

            $unpaid = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVenteByStatus($clientProspects, $exercice,'avoir', 'unpaid');
            /** @var OneVente[] $avoirs */
            $avoirs = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVentes($clientProspects, $exercice, 'avoir', $sort, $sortorder, $q, $period, $startperiod, $endperiod, $stat);

            return $this->render('OneVenteBundle:Avoir:list.html.twig', array(
                'paid' => $paid,
                'unpaid' => $unpaid,
                'avoirs' => $avoirs,
                'stat' => $stat,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
                'venteDetails' => $service->getVenteDetails($clientProspects, $exercice, 'avoir'),
            ));
        }
    }
    
    /**
     * Ajout d'un nouvel avoir
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
                    ->getClients($dossier);

                $reglements = $this->getDoctrine()
                    ->getRepository('AppBundle:OneReglement')
                    ->getReglements();

                $contacts = $this->getDoctrine()
                    ->getRepository('AppBundle:OneContactClient')
                    ->getContacts($parentid);

                $projets = $this->getDoctrine()
                    ->getRepository('AppBundle:OneProjet')
                    ->getProjets();

                return $this->render('OneVenteBundle:Avoir:new.html.twig', array(
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
                ->getClients($dossier);

            $reglements = $this->getDoctrine()
                ->getRepository('AppBundle:OneReglement')
                ->getReglements();

            $projets = $this->getDoctrine()
                ->getRepository('AppBundle:OneProjet')
                ->getProjets();

            return $this->render('OneVenteBundle:Avoir:new.html.twig', array(
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
    
    public function editAction(Request $request, $id) {
        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);


        //fin lesexperts.biz

        /** @var Tiers[] $clientProspects */
        $clientProspects = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->getClients($dossier);


        $service = new VenteService($this->getDoctrine()->getManager());

        $vente = $this->getDoctrine()
            ->getRepository('AppBundle:OneVente')
            ->find($id);

        $exercices = Boost::getExercices(6,1);

        $venteDetails = $service->getVenteDetailsByVente($vente);

        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OneArticleVente')
            ->getArticlesVente($id);
        $contacts = $this->getDoctrine()
            ->getRepository('AppBundle:OneContactClient')
            ->getContacts($vente->getTiers()->getId());

        $reglements = $this->getDoctrine()
            ->getRepository('AppBundle:OneReglement')
            ->getReglements();
        $fichiers = $this->getDoctrine()
            ->getRepository('AppBundle:OneFichier')
            ->getFiles(unserialize($vente->getFichier()));
        $projets = $this->getDoctrine()
            ->getRepository('AppBundle:OneProjet')->getProjets();
        $taxes = $this->getDoctrine()
            ->getRepository('AppBundle:OneTva')->getTva();
        return $this->render('OneVenteBundle:Avoir:edit.html.twig', array(
            'vente' => $vente,
            'venteDetails' => $venteDetails,
            'articles' => $articles,
            'contacts' => $contacts,
            'clientProspects' => $clientProspects,
            'reglements' => $reglements,
            'fichiers' => $fichiers,
            'projets' => $projets,
            'taxes' => $taxes,
            'exercices' => $exercices
        ));
    }
    
    /**
     * Visualisation d'une facture
     * @param type $id
     * @return Response
     */
    public function showAction(Request $request, $id) {

        $service = new VenteService($this->getDoctrine()->getManager());
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



        $filename = $this->getParameter('one_documents_dir').'avoir'.DIRECTORY_SEPARATOR.'Avoir-N°'.$vente->getCode().'.pdf';
        $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);
        return $this->render('OneVenteBundle:Avoir:show.html.twig', array(
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
     * Sauvegarde d'un avoir
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
                    /** @var Tiers $clientProspect */
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);

                    $reglement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneReglement')
                        ->find($posted['reglement']);
                    
                    $vente->setType(3);
                    $vente->setTiers($clientProspect);
                    $vente->setOneReglement($reglement);
                    $vente->setStatusFacture(0);
                    $vente->setRemise($posted['remise-ht']);
                    $vente->setNote($posted['note']);
                    $vente->setCode($service->getNextCodeVente('avoir'));
                    $vente->setCreeLe(new \DateTime('now'));
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
                    
                    $vente->setType(3);
                    $vente->setTiers($clientProspect);
                    $vente->setOneReglement($reglement);
                    $vente->setStatusFacture(0);
                    $vente->setRemise($posted['remise-ht']);
                    $vente->setNote($posted['note']);
                    $vente->setModifieLe(new \DateTime('now'));
                    $vente->setExercice($exercice);
                    
                    if ($posted['date-facture'] !== '')
                        $vente->setDateFacture(\DateTime::createFromFormat('j/m/Y', $posted['date-facture']));
                    else
                        $vente->setDateFacture(new \DateTime('now'));
                    
                    if ((int)$posted['contact-client'] > 0) {
                        $contactClient = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->find($posted['contact-client']);
                        $vente->setContact($contactClient);
                    }
                    
                    if ((int)$posted['contact-livraison'] > 0) {
                        $contactLivraison = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->find($posted['contact-livraison']);
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
                        $rem = $this->getDoctrine()->getManager();
                        foreach ($posted['deleted-files'] as $file) {
                            $fichier = $this->getDoctrine()->getRepository('AppBundle:OneFichier')->findOneByNom($file);
                            $rem->remove($fichier);
                            unlink($this->getParameter('one_upload_dir').$file);
                        }
                        $rem->flush();
                    }
                    
                    $vente->setFichier(serialize($filesID));
                    
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
                            $article = $this->getDoctrine()->getRepository('AppBundle:OneArticleVente')->find($artid);
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
     * Suppression d'un avoir
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $vente = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
            $em->remove($vente);
            
            //Suppression des articles correspondants
            $articles = $this->getDoctrine()
                ->getRepository('AppBundle:OneArticleVente')
                ->findByVente($vente);

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
                $service = new VenteService($this->getDoctrine()->getManager());
                $address = $service->getAddress($type, $id);
                return new Response($address);
            }
        }
    }
    
    /**
     * Liste avoirs d'un client dans un modal
     * @param Request $request
     * @return Response
     */
    public function listmodalAction(Request $request) {
        if ($request->isMethod('GET')) {
            $facid = $request->query->get('facid');
            $clientid = (int)$request->query->get('clientid');
            $excludeids = $request->query->get('excludeids');

            //debut lesexperts.biz
//            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
//            $dossier = $this->getDoctrine()
//                ->getRepository('AppBundle:Dossier')
//                ->find($dossierId);

            $exercice = $request->query->get('exercice');

//            $clientProspects = $this->getDoctrine()
//                ->getRepository('AppBundle:Tiers')
//                ->getClientProspects($dossier);

            $avoirModal = $request->query->get('avoirModal');
            //fin lesexperts.biz


            $client = null;

            if ($clientid > 0) {
                $client = $this->getDoctrine()
                    ->getRepository('AppBundle:Tiers')
                    ->find($clientid);
            } elseif ($facid > 0) {
                $facture = $this->getDoctrine()
                    ->getRepository('AppBundle:OneVente')
                    ->find($facid);

                $client = $facture->getTiers();
            }
            
            $service = new VenteService($this->getDoctrine()->getManager());
            $avoirService = new AvoirService($this->getDoctrine()->getManager());
            $avoirs = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getAvoirsPaiement($client, $exercice, $excludeids);
            $params = $this->getDoctrine()
                ->getRepository('AppBundle:OneParametre')
                ->find(1);

//            $avoirAmounts = $service->getVenteAmounts($clientProspects, $exercice,'avoir');
//            $avoirLeftAmounts = $avoirService->getLeftAmounts($clientProspects, $exercice);


            $avoirAmounts = $service->getVenteAmounts(array($client), $exercice,'avoir');
            $avoirLeftAmounts = $avoirService->getLeftAmounts(array($client), $exercice);

            return $this->render('OneVenteBundle:Avoir:listmodal.html.twig', array(
                'facid' => $facid,
                'avoirs' => $avoirs,
                'avoirAmounts' => $avoirAmounts,
                'avoirLeftAmounts' =>$avoirLeftAmounts ,
                'params' => $params,
                'avoirModal' => $avoirModal
            ));
        }
    }
}
