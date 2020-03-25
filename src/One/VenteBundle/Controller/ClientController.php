<?php

/**
 * Created by Netbeans
 * Created on : 3 août 2017, 10:53:44
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\FormeJuridique;
use AppBundle\Entity\OneActivite;
use AppBundle\Entity\OneAppelTelephonique;
use AppBundle\Entity\OneContactClient;
use AppBundle\Entity\OneDevis;
use AppBundle\Entity\OneEncaissement;
use AppBundle\Entity\OneFamillePrix;
use AppBundle\Entity\OneOpportunite;
use AppBundle\Entity\OnePaiement;
use AppBundle\Entity\OneQualification;
use AppBundle\Entity\OneReglement;
use AppBundle\Entity\OneTache;
use AppBundle\Entity\OneTva;
use AppBundle\Entity\OneVente;
use AppBundle\Entity\Pays;
use AppBundle\Entity\Tiers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use One\ProspectBundle\Service\ClientProspectService;
use One\ProspectBundle\Service\ContactClientService;
use One\VenteBundle\Service\VenteService;
use One\VenteBundle\Service\EncaissementService;

class ClientController extends Controller
{
    /**
     * Liste des clients
     */
    public function listAction(Request $request) {
        if ($request->isMethod('GET')) {
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


            /** @var Tiers[] $tiers */
            $tiers = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getTiersByDossier($dossier, array(1, 3), '411' );
            //fin lesexperts.biz

            if(null === $dossier){
                return new Response('');
            }

            /** @var Tiers[] $clients */
            $clients = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClients($dossier, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            return $this->render('OneVenteBundle:Client:list.html.twig', array(
                'clients' => $clients,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'tiers' => $tiers
            ));
        }

        throw new AccessDeniedException('Accès refusé');
    }

    /**
     * Création d'un client
     * @return Response
     */
    public function newAction() {
        $service = new ClientProspectService($this->getDoctrine()->getManager());
//        $countries = $this->getDoctrine()->getRepository('AppBundle:OnePays')->getCountries();
        $countries = $this->getDoctrine()
            ->getRepository('AppBundle:Pays')
            ->findBy(array(), array('nom' => 'ASC'));

        $qualifications = $this->getDoctrine()->getRepository('AppBundle:OneQualification')->getQualifications();
        $reglements = $this->getDoctrine()->getRepository('AppBundle:OneReglement')->getReglements();
        $pricefamilies = $this->getDoctrine()->getRepository('AppBundle:OneFamillePrix')->getPriceFamilies();
        $taxes = $this->getDoctrine()->getRepository('AppBundle:OneTva')->getTva();
        $legalforms = $this->getDoctrine()->getRepository('AppBundle:OneFormeJuridique')->getLegalForms();
        $activites = $this->getDoctrine()->getRepository('AppBundle:OneActivite')->getActivites();
        return $this->render('OneVenteBundle:Client:new.html.twig', array(
            'countries' => $countries,
            'qualifications' => $qualifications,
            'reglements' => $reglements,
            'pricefamilies' => $pricefamilies,
            'taxes' => $taxes,
            'legalforms' => $legalforms,
            'activites' => $activites,
            'mycountry' => $service->getMyCountry(),
        ));
    }

    /**
     * Edition d'un client
     * @return Response
     */
    public function editAction($id) {
        $service = new ClientProspectService($this->getDoctrine()->getManager());
//        $countries = $this->getDoctrine()->getRepository('AppBundle:OnePays')->getCountries();

        /** @var Pays $countries */
        $countries = $this->getDoctrine()
            ->getRepository('AppBundle:Pays')
            ->findBy(array(), array('nom' => 'ASC'));

        /** @var OneQualification[] $qualifications */
        $qualifications = $this->getDoctrine()
            ->getRepository('AppBundle:OneQualification')
            ->getQualifications();
        /** @var OneReglement[] $reglements */
        $reglements = $this->getDoctrine()
            ->getRepository('AppBundle:OneReglement')
            ->getReglements();
        /** @var OneFamillePrix[] $pricefamilies */
        $pricefamilies = $this->getDoctrine()
            ->getRepository('AppBundle:OneFamillePrix')
            ->getPriceFamilies();
        /** @var OneTva[] $taxes */
        $taxes = $this->getDoctrine()
            ->getRepository('AppBundle:OneTva')
            ->getTva();
        /** @var FormeJuridique[] $legalforms */
        $legalforms = $this->getDoctrine()
            ->getRepository('AppBundle:FormeJuridique')
            ->findBy(array(), array('code'=>'ASC'));
        /** @var OneActivite[] $activites */
        $activites = $this->getDoctrine()
            ->getRepository('AppBundle:OneActivite')
            ->getActivites();

        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->find($id);

        /** @var OneContactClient[] $contacts */
        $contacts = $this->getDoctrine()
            ->getRepository('AppBundle:OneContactClient')
            ->getContacts($id);

        return $this->render('OneVenteBundle:Client:edit.html.twig', array(
            'countries' => $countries,
            'qualifications' => $qualifications,
            'reglements' => $reglements,
            'pricefamilies' => $pricefamilies,
            'taxes' => $taxes,
            'legalforms' => $legalforms,
            'activites' => $activites,
            'mycountry' => $service->getMyCountry(),
            'client' => $client,
            'contacts' => $contacts,
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
            $venteService = new VenteService($this->getDoctrine()->getManager());
            $encaissementService = new EncaissementService($this->getDoctrine()->getManager());
            $type = $request->query->get('type');
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

            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);
            //fin lesexperts.biz


            $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);

            $client = null;
            $contacts = null;
            $factures = null;
            $devis = null;
            $commandes = null;
            $encaissements = null;
            $avoirs = null;
            $paiements = null;
            $opportunites = null;
            $taches = null;
            $appels = null;

            $imputations = null;

            $client = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($id);

            /** @var OneContactClient[] $contacts */
            $contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->getContacts($id);
            /** @var OneVente[] $factures */
            $factures = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVentesByClient($client, $exercice,'facture', $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneDevis[] $devis */
            $devis = $this->getDoctrine()
                ->getRepository('AppBundle:OneDevis')
                ->getDevisByClientProspect($client, $exercice, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneVente[] $commandes */
            $commandes = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVentesByClient($client, $exercice,'commande', $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneEncaissement[] $encaissements */
            $encaissements = $this->getDoctrine()
                ->getRepository('AppBundle:OneEncaissement')
                ->getEncaissementsByClient($client, $exercice, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OnePaiement[] $paiements */
            $paiements = $this->getDoctrine()
                ->getRepository('AppBundle:OnePaiement')
                ->getPaiementsByClient($client, $exercice,$sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneVente[] $avoirs */
            $avoirs = $this->getDoctrine()
                ->getRepository('AppBundle:OneVente')
                ->getVentesByClient($client, $exercice, 'avoir', $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneOpportunite[] $opportunites */
            $opportunites = $this->getDoctrine()
                ->getRepository('AppBundle:OneOpportunite')
                ->getOpportunitesByProspect($client, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneTache[] $taches */
            $taches = $this->getDoctrine()
                ->getRepository('AppBundle:OneTache')
                ->getTachesByClientProspect($client, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneAppelTelephonique[] $appels */
            $appels = $this->getDoctrine()
                ->getRepository('AppBundle:OneAppelTelephonique')
                ->getAppelsByClientProspect($client, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);



            $imputations = $this->getDoctrine()
                ->getRepository('AppBundle:ImputationControle')
                ->getFactureClientsByTiers($client, $exercice, $q);

            return $this->render('OneVenteBundle:Client:show.html.twig', array(
                'client' => $client,
                'contacts' => $contacts,
                'factures' => $factures,
                'deviss' => $devis,
                'commandes' => $commandes,
                'encaissements' => $encaissements,
                'paiements' => $paiements,
                'avoirs' => $avoirs,
                'opportunites' => $opportunites,
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
                'factureDetails' => $venteService->getVenteDetails($clientProspects, $exercice,'facture'),
                'commandeDetails' => $venteService->getVenteDetails($clientProspects, $exercice, 'commande'),
                'encaissementAmounts' => $encaissementService->getEncaissementAmounts($clientProspects, $exercice),
                'avoirDetails' => $venteService->getVenteDetails($clientProspects, $exercice,'avoir'),
                'imputations' => $imputations
            ));
        }
    }

    /**
     * Sauvegarde l'ajout ou la modification d'un client
     */
    public function saveAction(Request $request) {
        if ($request->getMethod() === 'POST') {
            $service = new ClientProspectService($this->getDoctrine()->getManager());
            $contactClientService = new ContactClientService($this->getDoctrine()->getManager());
            $posted = $request->request->all();

            $dossierId = Boost::deboost($posted['id-dossier'], $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if(null === $dossier){
                $response = array('type' => 'success', 'action' => 'edit', 'id' => $posted['id']);
                return new JsonResponse($response);
            }

            $skype = $posted['skype'];

            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $client = new Tiers();

                    //Récupération des tables liées

                    $facCountry = $this->getDoctrine()
                        ->getRepository('AppBundle:Pays')
                        ->find($posted['pays-facturation']);
                    $livCountry = $this->getDoctrine()
                        ->getRepository('AppBundle:Pays')
                        ->find($posted['pays-livraison']);

                    $qualification = $this->getDoctrine()->getRepository('AppBundle:OneQualification')->find($posted['qualification']);
                    $reglement = $this->getDoctrine()->getRepository('AppBundle:OneReglement')->find($posted['reglement']);
                    $priceFamily = $this->getDoctrine()->getRepository('AppBundle:OneFamillePrix')->find($posted['famille-prix']);
                    $legalForm = $this->getDoctrine()->getRepository('AppBundle:FormeJuridique')->find($posted['forme-juridique']);
                    $activite = $this->getDoctrine()->getRepository('AppBundle:OneActivite')->find($posted['activite']);

                    $client->setType(1);
                    $client->setCompteStr('A créer');
                    $client->setParticulierEntreprise($posted['client-type']);
                    $client->setEmail($posted['email']);
                    $client->setTelephone($posted['telephone']);
                    $client->setSkype($skype);
                    $client->setAdresseFacturation1($posted['adresse-facturation-1']);
                    $client->setAdresseFacturation2($posted['adresse-facturation-2']);
                    $client->setVilleFacturation($posted['ville-facturation']);
                    $client->setCodePostalFacturation($posted['code-postal-facturation']);
                    $client->setPaysFacturation($facCountry);
                    $client->setSiteWeb($posted['site-web']);
                    $client->setOneQualification($qualification);
                    $client->setOneReglement($reglement);
                    $client->setOneFamillePrix($priceFamily);
                    $client->setNote($posted['note']);
                    $client->setDossier($dossier);
                    $client->setCreeLe(new \DateTime('now'));
                    if ($posted['numero-client'] == '') {
                        $client->setNumeroClient($service->getNextCode($dossier));
                    } else {
                        $client->setNumeroClient($service->getNextCustomCode($dossier, $posted['numero-client']));
                    }

                    //Emailing autorisé
                    if (isset($posted['emailing-autorise']))
                        $client->setEmailingAutorise(1);
                    else
                        $client->setEmailingAutorise(0);


                    //Adresse itendique
                    if (isset($posted['adresse-livraison-identique'])) {
                        $client->setAdresseLivraison1($posted['adresse-facturation-1']);
                        $client->setAdresseLivraison2($posted['adresse-facturation-2']);
                        $client->setVilleLivraison($posted['ville-facturation']);
                        $client->setCodePostalLivraison($posted['code-postal-facturation']);
                        $client->setPaysLivraison($facCountry);
                    }
                    //Adresse differente
                    else {
                        $client->setAdresseLivraison1($posted['adresse-livraison-1']);
                        $client->setAdresseLivraison2($posted['adresse-livraison-2']);
                        $client->setVilleLivraison($posted['ville-livraison']);
                        $client->setCodePostalLivraison($posted['code-postal-livraison']);
                        $client->setPaysLivraison($livCountry);
                    }

                    //Entreprise
                    if ($posted['client-type'] == 2) {
                        $client->setNomEntreprise($posted['nom-entreprise']);
                        $client->setNbSalarie($posted['nb-salarie']);
                        $client->setOneActivite($activite);
                        $client->setFormeJuridique($legalForm);
                        $client->setSiret($posted['siret']);
                        $client->setTvaIntracom($posted['tva-intracom']);
                        $client->setIntitule(strtoupper($posted['nom-entreprise']));

                        //Réinitialisation des champs particulier
                        $client->setNom('');
                        $client->setPrenom('');
                    }
                    //Particulier
                    else {
                        $client->setNom($posted['nom']);
                        $client->setPrenom($posted['prenom']);
                        $client->setIntitule(strtoupper($posted['nom']));
                        if ($posted['prenom'] != '')
                            $client->setIntitule(strtoupper($posted['prenom'].' '.$posted['nom']));

                        //Réinitialisation des champs entreprise
                        $client->setNomEntreprise('');
                        $client->setNbSalarie('');
                        //$client->setActivite('');
                        $client->setSiret('');
                        $client->setTvaIntracom('');
                    }

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($client);
                    $em->flush();

                    //Ajout d'un contact
                    if (isset($posted['contacts'])) {
                        foreach ($posted['contacts'] as $contact) {
                            $data = $contactClientService->parseData($contact);
                            $data['client-prospect'] = (int)$client->getId();
                            $contactClientService->saveData($data);
                        }
                    }

                    $response = array('type' => 'success', 'action' => 'add', 'id' => $client->getId());
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            }

            //Edition
            else {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $client = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['id']);

                    //Récupération des tables liées

                    $facCountry = $this->getDoctrine()
                        ->getRepository('AppBundle:Pays')
                        ->find($posted['pays-facturation']);
                    $livCountry = $this->getDoctrine()
                        ->getRepository('AppBundle:Pays')
                        ->find($posted['pays-livraison']);

                    $qualification = $this->getDoctrine()
                        ->getRepository('AppBundle:OneQualification')
                        ->find($posted['qualification']);

                    $reglement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneReglement')
                        ->find($posted['reglement']);

                    $priceFamily = $this->getDoctrine()
                        ->getRepository('AppBundle:OneFamillePrix')
                        ->find($posted['famille-prix']);

                    $legalForm = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeJuridique')
                        ->find($posted['forme-juridique']);

                    $activite = $this->getDoctrine()
                        ->getRepository('AppBundle:OneActivite')
                        ->find($posted['activite']);

                    $client->setParticulierEntreprise($posted['client-type']);
                    $client->setEmail($posted['email']);
                    $client->setTelephone($posted['telephone']);
                    $client->setSkype($skype);
                    $client->setAdresseFacturation1($posted['adresse-facturation-1']);
                    $client->setAdresseFacturation2($posted['adresse-facturation-2']);
                    $client->setVilleFacturation($posted['ville-facturation']);
                    $client->setCodePostalFacturation($posted['code-postal-facturation']);
                    $client->setPaysFacturation($facCountry);
                    $client->setSiteWeb($posted['site-web']);
                    $client->setOneQualification($qualification);
                    $client->setOneReglement($reglement);
                    $client->setOneFamillePrix($priceFamily);
                    $client->setNote($posted['note']);
                    $client->setDossier($dossier);
                    $client->setModifieLe(new \DateTime('now'));
                    if ($posted['numero-client'] == '') {
                        $client->setNumeroClient($service->getNextCode($dossier));
                    } elseif($client->getNumeroClient() != $posted['numero-client']) {
                        $client->setNumeroClient($service->getNextCustomCode($dossier, $posted['numero-client']));
                    }

                    //Emailing autorisé
                    if (isset($posted['emailing-autorise']))
                        $client->setEmailingAutorise(1);
                    else
                        $client->setEmailingAutorise(0);


                    //Adresse itendique
                    if (isset($posted['adresse-livraison-identique'])) {
                        $client->setAdresseLivraison1($posted['adresse-facturation-1']);
                        $client->setAdresseLivraison2($posted['adresse-facturation-2']);
                        $client->setVilleLivraison($posted['ville-facturation']);
                        $client->setCodePostalLivraison($posted['code-postal-facturation']);
                        $client->setPaysLivraison($facCountry);
                    }
                    //Adresse differente
                    else {
                        $client->setAdresseLivraison1($posted['adresse-livraison-1']);
                        $client->setAdresseLivraison2($posted['adresse-livraison-2']);
                        $client->setVilleLivraison($posted['ville-livraison']);
                        $client->setCodePostalLivraison($posted['code-postal-livraison']);
                        $client->setPaysLivraison($livCountry);
                    }

                    //Entreprise
                    if ($posted['client-type'] == 2) {
                        $client->setNomEntreprise($posted['nom-entreprise']);
                        $client->setNbSalarie($posted['nb-salarie']);
                        $client->setOneActivite($activite);
                        $client->setFormeJuridique($legalForm);
                        $client->setSiret($posted['siret']);
                        $client->setTvaIntracom($posted['tva-intracom']);
                        $client->setIntitule(strtoupper($posted['nom-entreprise']));

                        //Réinitialisation des champs particulier
                        $client->setNom('');
                        $client->setPrenom('');
                    }
                    //Particulier
                    else {
                        $client->setNom($posted['nom']);
                        $client->setPrenom($posted['prenom']);
                        $client->setIntitule(strtoupper($posted['nom']));
                        if ($posted['prenom'] != '')
                            $client->setIntitule(strtoupper($posted['prenom'].' '.$posted['nom']));

                        //Réinitialisation des champs entreprise
                        $client->setNomEntreprise('');
                        $client->setNbSalarie('');
                        //$client->setActivite('');
                        $client->setSiret('');
                        $client->setTvaIntracom('');
                    }

                    $em->flush();

                    //Ajout & édition d'un contact
                    $left_contact_id = [];
                    if (isset($posted['contacts'])) {
                        foreach ($posted['contacts'] as $contact) {
                            $data = $contactClientService->parseData($contact);
                            $data['client-prospect'] = (int)$client->getId();
                            $contactClientID = $contactClientService->saveData($data);
                            $left_contact_id[] = $contactClientID;
                        }
                    }

                    //Suppression des contacts supprimés
                    $rem = $this->getDoctrine()->getManager();
                    $all = count($left_contact_id) == 0;
                    $contactsToRemove = $this->getDoctrine()
                        ->getRepository('AppBundle:OneContactClient')
                        ->getContactsToRemove($left_contact_id, $client->getId(), $all);
                    foreach($contactsToRemove as $contact) {
                        $rem->remove($contact);
                    }
                    $rem->flush();

                    $response = array('type' => 'success', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                }
            }
        }
        throw new AccessDeniedException('Accès refusé');
    }
    
    /**
     * Suppresion d'un client
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $clientProspect = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($id);
            
            //Suppression des contacts correspondants
            $contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')
                ->findBy(array('tiers' => $clientProspect));
            foreach ($contacts as $contact) {
                $em->remove($contact);
            }
            
            $em->remove($clientProspect);
            $em->flush();
            
            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
    }
    
    public function duplicateAction(Request $request) {
        $service = new ClientProspectService($this->getDoctrine()->getManager());
        if ($request->isMethod('GET')) {
            return $this->render('OneVenteBundle:Client:duplicate.html.twig');
        }

        if ($request->isMethod('POST')) {
            try {
                $posted = $request->request->all();

                //debut lesexperts.biz
                $dossierId = Boost::deboost($posted['id-dossier'], $this);
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($dossierId);

                if(null === $dossier){
                    $response = array('type' => 'error', 'action' => 'duplicate');
                    return new JsonResponse($response);
                }

                //fin lesexperts.biz


                foreach ($posted['duplicated'] as $account) {
                    $client = new Tiers();
                    $fields = $service->parseData($account);
                    $account = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($fields['id']);

                    if ($fields['code'] == '') {
                        $client->setNumeroClient($service->getNextCode($dossier));
                    } else {
                        $client->setNumeroClient($fields['code']);
                    }

                    if ($account->getPaysFacturation()) {
                        $facCountry = $this->getDoctrine()
                            ->getRepository('AppBundle:Pays')
                            ->find($account->getPaysFacturation()->getId());
                        $client->setPaysFacturation($facCountry);
                    }
                    if ($account->getPaysLivraison()) {
                        $livCountry = $this->getDoctrine()
                            ->getRepository('AppBundle:Pays')
                            ->find($account->getPaysLivraison()->getId());
                        $client->setPaysLivraison($livCountry);
                    }
                    if ($account->getOneQualification()) {
                        $qualification = $this->getDoctrine()
                            ->getRepository('AppBundle:OneQualification')
                            ->find($account->getOneQualification()->getId());
                        $client->setOneQualification($qualification);
                    }
                    if ($account->getOneReglement()) {
                        $reglement = $this->getDoctrine()
                            ->getRepository('AppBundle:OneReglement')
                            ->find($account->getOneReglement()->getId());
                        $client->setOneReglement($reglement);
                    }
                    if ($account->getOneFamillePrix()) {
                        $priceFamily = $this->getDoctrine()
                            ->getRepository('AppBundle:OneFamillePrix')
                            ->find($account->getOneFamillePrix()->getId());
                        $client->setOneFamillePrix($priceFamily);
                    }
                    if ($account->getTvaTaux()) {
                        $tva = $this->getDoctrine()
                            ->getRepository('AppBundle:TvaTaux')
                            ->find($account->getTvaTaux()->getId());
                        $client->setTvaTaux($tva);
                    }
                    if ($account->getFormeJuridique()) {
                        $legalForm = $this->getDoctrine()
                            ->getRepository('AppBundle:FormeJuridique')
                            ->find($account->getFormeJuridique()->getId());
                        $client->setFormeJuridique($legalForm);
                    }

//                    $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById(9100);

                    $client->setType($account->getType());
                    $client->setCompteStr($account->getCompteStr());
                    $client->setParticulierEntreprise($account->getParticulierEntreprise());
                    $client->setEmail($account->getEmail());
                    $client->setTelephone($account->getTelephone());
                    $client->setAdresseFacturation1($account->getAdresseFacturation1());
                    $client->setAdresseFacturation2($account->getAdresseFacturation2());
                    $client->setVilleFacturation($account->getVilleFacturation());
                    $client->setCodePostalFacturation($account->getCodePostalFacturation());
                    $client->setAdresseLivraisonIdentique($account->getAdresseLivraisonIdentique());
                    $client->setAdresseLivraison1($account->getAdresseLivraison1());
                    $client->setAdresseLivraison2($account->getAdresseLivraison2());
                    $client->setVilleLivraison($account->getVilleLivraison());
                    $client->setCodePostalLivraison($account->getCodePostalLivraison());
                    $client->setSiteWeb($account->getSiteWeb());
                    $client->setNote($account->getNote());
                    $client->setDossier($dossier);
                    $client->setTvaPrioritaire($account->getTvaPrioritaire());
                    $client->setEmailingAutorise($account->getEmailingAutorise());
                    $client->setNomEntreprise($account->getNomEntreprise());
                    $client->setNbSalarie($account->getNbSalarie());
                    $client->setOneActivite($account->getOneActivite());
                    $client->setSiret($account->getSiret());
                    $client->setTvaIntracom($account->getTvaIntracom());
                    $client->setNom($account->getNom());
                    $client->setPrenom($account->getPrenom());
                    $client->setIntitule($account->getIntitule());
                    $client->setCompteStr($account->getCompteStr());
                    $client->setCreeLe(new \DateTime('now'));

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($client);
                    $em->flush();


                }
                $response = array('type' => 'success', 'action' => 'duplicate');
                return new JsonResponse($response);
            } catch (\Exception $ex) {
                $response = array('type' => 'error', 'action' => 'duplicate');
                return new JsonResponse($response);
            }
        }
    }
    
    public function listinmodalAction(Request $request) {

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->request->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);
        //fin lesexperts.biz

        /** @var Tiers[] $clients */
        $clients = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->getClients($dossier);

        return $this->render('OneVenteBundle:Client:listinmodal.html.twig', array(
            'clients' => $clients,
        ));
    }
    
    public function prospecttoclientAction(Request $request) {
        if ($request->isMethod('GET')) {

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);
            //fin lesexperts.biz

            /** @var Tiers[] $prospects */
            $prospects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getProspects($dossier);

            return $this->render('OneVenteBundle:Client:prospecttoclient.html.twig', array(
                'prospects' => $prospects,
            ));
        }

        if ($request->isMethod('POST')) {
            try {
                $id = $request->request->get('id');
                $em = $this->getDoctrine()->getManager();
                $prospect = $this->getDoctrine()
                    ->getRepository('AppBundle:Tiers')
                    ->find($id);

                $prospect->setType(1);
                $em->flush();

                $response = array('type' => 'success', 'action' => 'transform');
                return new JsonResponse($response);
            } catch (\Exception $ex) {
                $response = array('type' => 'error', 'action' => 'transform');
                return new JsonResponse($response);
            }
        }
        throw new AccessDeniedException('Accès refusé');
    }
    
    public function balanceAction(Request $request, $id) {
        $elements = array();
        $serviceEncaissement = new EncaissementService($this->getDoctrine()->getManager());
        $serviceVente = new VenteService($this->getDoctrine()->getManager());
        
        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->find($id);

        $firstDate = $client->getCreeLe();


        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $exercice = $request->query->get('exercice');

        $clientProspects = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->getClientProspects($dossier);
        //fin lesexperts.biz

        /** @var OneEncaissement[] $encaissements */
        $encaissements = $this->getDoctrine()
            ->getRepository('AppBundle:OneEncaissement')
            ->getNextByDate($client, $firstDate, $exercice);

        foreach($encaissements as $encaissement) {
            $item = array();
            $item['type'] = 'encaissement';
            $item['id'] = $encaissement->getId();
            $item['code'] = $encaissement->getCode();
            $item['date'] = $encaissement->getDateEncaissement();
            $item['amount'] = $serviceEncaissement
                ->getEncaissementAmounts($clientProspects, $exercice)[$encaissement->getId()];

            $elements[] = $item;
        }

        /** @var OneVente[] $factures */
        $factures = $this->getDoctrine()
            ->getRepository('AppBundle:OneVente')
            ->getNextByDate($client, $exercice, $firstDate);

        foreach($factures as $facture) {
            $item = array();
            $item['type'] = 'facture';
            $item['id'] = $facture->getId();
            $item['code'] = $facture->getCode();
            $item['date'] = $facture->getDateFacture();
//            $item['amount'] = $serviceVente
//                ->getVenteDetails($clientProspects, $exercice,'facture')[$facture->getId()]['ttc'];

            $item['amount'] = $serviceVente
                ->getVenteDetailsByVente($facture)['ttc'];

            $elements[] = $item;
        }

        /** @var OnePaiement[] $paiements */
        $paiements = $this->getDoctrine()
            ->getRepository('AppBundle:OnePaiement')
            ->getNextByDate($client, $firstDate);

        foreach($paiements as $paiement) {
            $item = array();
            $item['type'] = 'paiement';
            $item['id'] = $paiement->getId();
            $item['code'] = $paiement->getCode();
            $item['date'] = $paiement->getDateReception();
            $item['amount'] = $paiement->getMontant();
            $item['facture'] = $paiement->getOneVente();
            $elements[] = $item;
        }
        
        //tri par date
        $sortArray = array();
        foreach($elements as $element){
            foreach($element as $key=>$value){
                if(!isset($sortArray[$key])){
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        } 
        $orderby = "date";
        if(count($sortArray) > 0)
        array_multisort($sortArray[$orderby], SORT_ASC, $elements);
        
        return $this->render('OneVenteBundle:Client:balance.html.twig', array(
            'client' => $client,
            'elements' => $elements,
            'firstDate' => $firstDate,
        ));
    }
}