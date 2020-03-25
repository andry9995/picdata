<?php

namespace One\ProspectBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\FormeJuridique;
use AppBundle\Entity\OneActivite;
use AppBundle\Entity\OneAppelTelephonique;
use AppBundle\Entity\OneOpportunite;
use AppBundle\Entity\OneProspectOrigine;
use AppBundle\Entity\OneProspectOrigineContact;
use AppBundle\Entity\OneTache;
use AppBundle\Entity\Tiers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use One\ProspectBundle\Service\ClientProspectService;
use One\ProspectBundle\Service\ContactClientService;

class ProspectController extends Controller
{
    /**
     * Liste des prospects
     */
    public function indexAction() {
        return $this->render('OneProspectBundle:Prospect:index.html.twig');
    }
    
    /**
     * Liste des prospects
     */
    public function listAction(Request $request) {
        if ($request->isMethod('GET')) {
            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');

            $archive = $request->query->get('archive');



            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $activityId = $request->query->get('activityId');
            if($activityId === 'all'){
                $activity = null;
            }
            else{
                $activity = $this->getDoctrine()
                    ->getRepository('AppBundle:OneActivite')
                    ->find((int)$activityId);
            }

            //fin lesexperts.biz

            if(null === $dossier){
                return new Response('');
            }

            /** @var Tiers[] $prospects */
            $prospects = $this->getDoctrine()->getRepository('AppBundle:Tiers')
                ->getProspects($dossier, $sort, $sortorder, $q, $period, $startperiod, $endperiod, $activity, $archive);


            /** @var OneActivite[] $activities */
            $activities = array();

            /** @var Tiers $prospect */
            $unfileteredProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getProspects($dossier);

            foreach ($unfileteredProspects as $prospect){
                if(!in_array($prospect->getOneActivite(), $activities)) {
                    $activities[] = $prospect->getOneActivite();
                }
            }

            $activitiesDetails = array();
            foreach ($activities as $act){
                $pros = $this->getDoctrine()
                    ->getRepository('AppBundle:Tiers')
                    ->getTiersByDossierActiviteType($dossier, $act, 4, $archive);
                $activitiesDetails[] = array('activity' => $act, 'nbre' => count($pros));
            }

            return $this->render('OneProspectBundle:Prospect:list.html.twig', array(
                'prospects' => $prospects,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'archive' => $archive,
                'activitiesDetails' => $activitiesDetails,
                'activityId' => $activityId
            ));
        }

        throw new AccessDeniedException('Accès refusé');
    }
    
    /**
     * Création d'un prospect
     */
    public function newAction() {
        $service = new ClientProspectService($this->getDoctrine()->getManager());
//        $countries = $this->getDoctrine()->getRepository('AppBundle:OnePays')->getCountries();

        $countries = $this->getDoctrine()
            ->getRepository('AppBundle:Pays')
            ->findBy(array(), array('nom'=>'ASC'));

        $qualifications = $this->getDoctrine()->getRepository('AppBundle:OneQualification')->getQualifications();
        $reglements = $this->getDoctrine()->getRepository('AppBundle:OneReglement')->getReglements();
        $pricefamilies = $this->getDoctrine()->getRepository('AppBundle:OneFamillePrix')->getPriceFamilies();
        $taxes = $this->getDoctrine()->getRepository('AppBundle:OneTva')->getTva();
//        $legalforms = $this->getDoctrine()->getRepository('AppBundle:OneFormeJuridique')->getLegalForms();

        /** @var FormeJuridique[] $legalforms */
        $legalforms = $this->getDoctrine()
            ->getRepository('AppBundle:FormeJuridique')
            ->findBy(array(), array('libelle' => 'ASC'));

        $activites = $this->getDoctrine()->getRepository('AppBundle:OneActivite')->getActivites();

        $origines = $this->getDoctrine()
            ->getRepository('AppBundle:OneProspectOrigine')
            ->findBy(array(),array('libelle' => 'ASC'));


        return $this->render('OneProspectBundle:Prospect:new.html.twig', array(
            'countries' => $countries,
            'qualifications' => $qualifications,
            'reglements' => $reglements,
            'pricefamilies' => $pricefamilies,
            'taxes' => $taxes,
            'legalforms' => $legalforms,
            'activites' => $activites,
            'mycountry' => $service->getMyCountry(),
            'origines' => $origines
        ));
    }
    
    /**
     * Edition d'un prospect
     */
    public function editAction($id) {
        $service = new ClientProspectService($this->getDoctrine()->getManager());
//        $countries = $this->getDoctrine()->getRepository('AppBundle:OnePays')->getCountries();
        $countries = $this->getDoctrine()
            ->getRepository('AppBundle:Pays')
            ->findBy(array(), array('nom'=>'ASC'));



        $qualifications = $this->getDoctrine()->getRepository('AppBundle:OneQualification')->getQualifications();
        $reglements = $this->getDoctrine()->getRepository('AppBundle:OneReglement')->getReglements();
        $pricefamilies = $this->getDoctrine()->getRepository('AppBundle:OneFamillePrix')->getPriceFamilies();
        $taxes = $this->getDoctrine()->getRepository('AppBundle:OneTva')->getTva();
//        $legalforms = $this->getDoctrine()->getRepository('AppBundle:OneFormeJuridique')->getLegalForms();

        /** @var FormeJuridique[] $legalforms */
        $legalforms = $this->getDoctrine()
            ->getRepository('AppBundle:FormeJuridique')
            ->findBy(array(), array('libelle' => 'ASC'));

        $activites = $this->getDoctrine()->getRepository('AppBundle:OneActivite')->getActivites();
        $prospect = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($id);
        $contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->getContacts($id);


        $origines = $this->getDoctrine()
            ->getRepository('AppBundle:OneProspectOrigine')
            ->findBy(array(),array('libelle' => 'ASC'));


        /** @var OneProspectOrigineContact[] $recommandations */
        $recommandations = $this->getDoctrine()
            ->getRepository('AppBundle:OneProspectOrigineContact')
            ->findBy(array('tiers' => $prospect));

        $recommandation = null;
        if(count($recommandations) > 0){
            $recommandation = $recommandations[0];
        }

        return $this->render('OneProspectBundle:Prospect:edit.html.twig', array(
            'countries' => $countries,
            'qualifications' => $qualifications,
            'reglements' => $reglements,
            'pricefamilies' => $pricefamilies,
            'taxes' => $taxes,
            'legalforms' => $legalforms,
            'activites' => $activites,
            'mycountry' => $service->getMyCountry(),
            'prospect' => $prospect,
            'contacts' => $contacts,
            'origines' => $origines,
            'recommandation' =>$recommandation
        ));
    }
    
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
            $prospect = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->find($id);
            $contacts = $this->getDoctrine()
                ->getRepository('AppBundle:OneContactClient')
                ->getContacts($id);
            $devis = $this->getDoctrine()
                ->getRepository('AppBundle:OneDevis')
                ->getDevisByClientProspect($prospect, null, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneOpportunite[] $opportunites */
            $opportunites = $this->getDoctrine()
                ->getRepository('AppBundle:OneOpportunite')
                ->getOpportunitesByProspect($prospect, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneTache[] $taches */
            $taches = $this->getDoctrine()
                ->getRepository('AppBundle:OneTache')
                ->getTachesByClientProspect($prospect, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            /** @var OneAppelTelephonique[] $appels */
            $appels = $this->getDoctrine()
                ->getRepository('AppBundle:OneAppelTelephonique')
                ->getAppelsByClientProspect($prospect, $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            return $this->render('OneProspectBundle:Prospect:show.html.twig', array(
                'prospect' => $prospect,
                'contacts' => $contacts,
                'deviss' => $devis,
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
            ));
        }
    }
    
    /**
     * Sauvegarde l'ajout ou la modification d'un prospect
     */
    public function saveAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $service = new ClientProspectService($this->getDoctrine()->getManager());
            $contactClientService = new ContactClientService($this->getDoctrine()->getManager());
            $posted = $request->request->all();

            $dossierId = Boost::deboost($posted['id-dossier'], $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if(null === $dossier){
                $response = array('type' => 'error', 'action' => 'add');
                return new JsonResponse($response);
            }

            $skype = $posted['skype'];

            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $prospect = new Tiers();

                    $prospect->setCompteStr("A créer");
                    $prospect->setStatus(1);
                    $prospect->setArchive(0);

                    //Récupération des tables liées
                    $facCountry = $this->getDoctrine()->getRepository('AppBundle:Pays')->find($posted['pays-facturation']);
                    $livCountry = $this->getDoctrine()->getRepository('AppBundle:Pays')->find($posted['pays-livraison']);
                    $qualification = $this->getDoctrine()->getRepository('AppBundle:OneQualification')->find($posted['qualification']);
                    $reglement = $this->getDoctrine()->getRepository('AppBundle:OneReglement')->find($posted['reglement']);
                    $priceFamily = $this->getDoctrine()->getRepository('AppBundle:OneFamillePrix')->find($posted['famille-prix']);
                    $legalForm = $this->getDoctrine()->getRepository('AppBundle:FormeJuridique')->find($posted['forme-juridique']);
                    $activite = $this->getDoctrine()->getRepository('AppBundle:OneActivite')->find($posted['activite']);
//                    $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById(9100);


                    $origine = $this->getDoctrine()
                        ->getRepository('AppBundle:OneProspectOrigine')
                        ->find($posted['origine']);

                    if(isset($posted['isprospect'])){
                        if((int)$posted['isprospect'] === 1){
                            $prospect->setType(4);
                        }
                        else if((int)$posted['isprospect'] === 0){
                            $prospect->setType(1);
                        }
                    }
                    else {
                        $prospect->setType(4);
                    }



                    $prospect->setParticulierEntreprise($posted['prospect-type']);
                    $prospect->setEmail($posted['email']);
                    $prospect->setTelephone($posted['telephone']);
                    $prospect->setSkype($skype);
                    $prospect->setAdresseFacturation1($posted['adresse-facturation-1']);
                    $prospect->setAdresseFacturation2($posted['adresse-facturation-2']);
                    $prospect->setVilleFacturation($posted['ville-facturation']);
                    $prospect->setCodePostalFacturation($posted['code-postal-facturation']);
                    $prospect->setPaysFacturation($facCountry);
                    $prospect->setSiteWeb($posted['site-web']);
                    $prospect->setOneQualification($qualification);
                    $prospect->setOneReglement($reglement);
                    $prospect->setOneFamillePrix($priceFamily);
                    $prospect->setNote($posted['note']);
                    $prospect->setDossier($dossier);
                    $prospect->setCreeLe(new \DateTime('now'));
                    if ($posted['numero-client'] == '') {
                        $prospect->setNumeroClient($service->getNextCode($dossier));
                    } else {
                        $prospect->setNumeroClient($service->getNextCustomCode($dossier, $posted['numero-client']));
                    }

                    $prospect->setOneProspectOrigine($origine);

                    //Emailing autorisé
                    if (isset($posted['emailing-autorise']))
                        $prospect->setEmailingAutorise(1);
                    else
                        $prospect->setEmailingAutorise(0);


                    //Adresse itendique
                    if (isset($posted['adresse-livraison-identique'])) {


                        if($posted['adresse-livraison-identique'] === 'on') {
                            $prospect->setAdresseLivraison1($posted['adresse-facturation-1']);
                            $prospect->setAdresseLivraison2($posted['adresse-facturation-2']);
                            $prospect->setVilleLivraison($posted['ville-facturation']);
                            $prospect->setCodePostalLivraison($posted['code-postal-facturation']);
                            $prospect->setPaysLivraison($facCountry);
                            $prospect->setAdresseLivraisonIdentique(1);
                        }
                        else{
                            $prospect->setAdresseLivraison1($posted['adresse-livraison-1']);
                            $prospect->setAdresseLivraison2($posted['adresse-livraison-2']);
                            $prospect->setVilleLivraison($posted['ville-livraison']);
                            $prospect->setCodePostalLivraison($posted['code-postal-livraison']);
                            $prospect->setPaysLivraison($livCountry);
                            $prospect->setAdresseLivraisonIdentique(0);
                        }


                    }                    
                    //Adresse differente
                    else {
                        $prospect->setAdresseLivraison1($posted['adresse-livraison-1']);
                        $prospect->setAdresseLivraison2($posted['adresse-livraison-2']);
                        $prospect->setVilleLivraison($posted['ville-livraison']);
                        $prospect->setCodePostalLivraison($posted['code-postal-livraison']);
                        $prospect->setPaysLivraison($livCountry);
                        $prospect->setAdresseLivraisonIdentique(0);
                    }

                    //Entreprise
                    if ($posted['prospect-type'] == 2) {
                        $prospect->setNomEntreprise($posted['nom-entreprise']);
                        $prospect->setNbSalarie($posted['nb-salarie']);
                        $prospect->setOneActivite($activite);
                        $prospect->setFormeJuridique($legalForm);
                        $prospect->setSiret($posted['siret']);
                        $prospect->setTvaIntracom($posted['tva-intracom']);
                        $prospect->setIntitule(strtoupper($posted['nom-entreprise']));

                        //Réinitialisation des champs particulier
                        $prospect->setNom('');
                        $prospect->setPrenom('');
                    }
                    //Particulier
                    else {
                        $prospect->setNom($posted['nom']);
                        $prospect->setPrenom($posted['prenom']);
                        $prospect->setIntitule(strtoupper($posted['nom']));
                        if ($posted['prenom'] != '')
                            $prospect->setIntitule(strtoupper($posted['prenom'].' '.$posted['nom']));

                        //Réinitialisation des champs entreprise
                        $prospect->setNomEntreprise('');
                        $prospect->setNbSalarie('');
                        //$prospect->setActivite('');
                        $prospect->setSiret('');
                        $prospect->setTvaIntracom('');
                    }


                    $newDate = null;
                    if($posted['date-premier-contact'] !== ''){
                        $dateArray = explode('/', $posted['date-premier-contact']);

                        if(count($dateArray) === 3){
                            $newDate = new \DateTime("$dateArray[2]-$dateArray[1]-$dateArray[0]");
                        }
                    }

                    $prospect->setPremierContact($newDate);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($prospect);
                    $em->flush();
                    
                    //Ajout d'un contact
                    if (isset($posted['contacts'])) {
                        foreach ($posted['contacts'] as $contact) {
                            $data = $contactClientService->parseData($contact);
                            $data['client-prospect'] = intval($prospect->getId());
                            $contactClientService->saveData($data);
                        }
                    }


                    //Ajout recommandation
                    if(isset($posted['nom-recommandation'])){
                        $recommandation = new OneProspectOrigineContact();

                        $recommandation->setTiers($prospect);

                        $recommandation->setNom($posted['nom-recommandation']);

                        $prenom = $posted['prenom-recommandation'];
                        if($prenom !== ''){
                           $recommandation->setPrenom($prenom);
                        }

                        $societe = $posted['societe-recommandation'];
                        if($societe !== ''){
                            $recommandation->setSociete($societe);
                        }



                        $em->persist($recommandation);

                        $em->flush();
                    }

                    $response = array('type' => 'success', 'action' => 'add', 'id' => $prospect->getId());
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
                    $prospect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['id']);

                    if(isset($posted['isprospect'])){
                        if((int)$posted['isprospect'] === 1){
                            $prospect->setType(4);
                        }
                        else if((int)$posted['isprospect'] === 0){
                            $prospect->setType(1);
                        }
                    }
                    else {
                        $prospect->setType(4);
                    }
                    
                    //Récupération des tables liées
                    $facCountry = $this->getDoctrine()->getRepository('AppBundle:Pays')->find($posted['pays-facturation']);
                    $livCountry = $this->getDoctrine()->getRepository('AppBundle:Pays')->find($posted['pays-livraison']);
                    $qualification = $this->getDoctrine()->getRepository('AppBundle:OneQualification')->find($posted['qualification']);
                    $reglement = $this->getDoctrine()->getRepository('AppBundle:OneReglement')->find($posted['reglement']);
                    $priceFamily = $this->getDoctrine()->getRepository('AppBundle:OneFamillePrix')->find($posted['famille-prix']);
                    $legalForm = $this->getDoctrine()->getRepository('AppBundle:FormeJuridique')->find($posted['forme-juridique']);
                    $activite = $this->getDoctrine()->getRepository('AppBundle:OneActivite')->find($posted['activite']);
//                    $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById(9100);

                    $origine = $this->getDoctrine()
                        ->getRepository('AppBundle:OneProspectOrigine')
                        ->find($posted['origine']);
                    
                    $prospect->setParticulierEntreprise($posted['prospect-type']);
                    $prospect->setEmail($posted['email']);
                    $prospect->setTelephone($posted['telephone']);
                    $prospect->setSkype($skype);
                    $prospect->setAdresseFacturation1($posted['adresse-facturation-1']);
                    $prospect->setAdresseFacturation2($posted['adresse-facturation-2']);
                    $prospect->setVilleFacturation($posted['ville-facturation']);
                    $prospect->setCodePostalFacturation($posted['code-postal-facturation']);
                    $prospect->setPaysFacturation($facCountry);
                    $prospect->setSiteWeb($posted['site-web']);
                    $prospect->setOneQualification($qualification);
                    $prospect->setOneReglement($reglement);
                    $prospect->setOneFamillePrix($priceFamily);
                    $prospect->setNote($posted['note']);
                    $prospect->setDossier($dossier);

                    $prospect->setOneProspectOrigine($origine);

                    $prospect->setModifieLe(new \DateTime('now'));
                    if ($posted['numero-client'] == '') {
                        $prospect->setNumeroClient($service->getNextCode($dossier));
                    } elseif($prospect->getNumeroClient() != $posted['numero-client']) {
                        $prospect->setNumeroClient($service->getNextCustomCode($dossier, $posted['numero-client']));
                    }

                    //Emailing autorisé
                    if (isset($posted['emailing-autorise']))
                        $prospect->setEmailingAutorise(1);
                    else
                        $prospect->setEmailingAutorise(0);


                    //Adresse itendique
                    if (isset($posted['adresse-livraison-identique'])) {
                        $prospect->setAdresseLivraison1($posted['adresse-facturation-1']);
                        $prospect->setAdresseLivraison2($posted['adresse-facturation-2']);
                        $prospect->setVilleLivraison($posted['ville-facturation']);
                        $prospect->setCodePostalLivraison($posted['code-postal-facturation']);
                        $prospect->setPaysLivraison($facCountry);
                    }                    
                    //Adresse differente
                    else {
                        $prospect->setAdresseLivraison1($posted['adresse-livraison-1']);
                        $prospect->setAdresseLivraison2($posted['adresse-livraison-2']);
                        $prospect->setVilleLivraison($posted['ville-livraison']);
                        $prospect->setCodePostalLivraison($posted['code-postal-livraison']);
                        $prospect->setPaysLivraison($livCountry);
                    }

                    //Entreprise
                    if ($posted['prospect-type'] == 2) {
                        $prospect->setNomEntreprise($posted['nom-entreprise']);
                        $prospect->setNbSalarie($posted['nb-salarie']);
                        $prospect->setOneActivite($activite);
                        $prospect->setFormeJuridique($legalForm);
                        $prospect->setSiret($posted['siret']);
                        $prospect->setTvaIntracom($posted['tva-intracom']);
                        $prospect->setIntitule(strtoupper($posted['nom-entreprise']));

                        //Réinitialisation des champs particulier
                        $prospect->setNom('');
                        $prospect->setPrenom('');
                    }
                    //Particulier
                    else {
                        $prospect->setNom($posted['nom']);
                        $prospect->setPrenom($posted['prenom']);
                        $prospect->setIntitule(strtoupper($posted['nom']));
                        if ($posted['prenom'] != '')
                            $prospect->setIntitule(strtoupper($posted['prenom'].' '.$posted['nom']));

                        //Réinitialisation des champs entreprise
                        $prospect->setNomEntreprise('');
                        $prospect->setNbSalarie('');
                        //$prospect->setActivite('');
                        $prospect->setSiret('');
                        $prospect->setTvaIntracom('');
                    }

                    $newDate = null;
                    if($posted['date-premier-contact'] !== ''){
                        $dateArray = explode('/', $posted['date-premier-contact']);

                        if(count($dateArray) === 3){
                            $newDate = new \DateTime("$dateArray[2]-$dateArray[1]-$dateArray[0]");
                        }
                    }

                    $prospect->setPremierContact($newDate);
                    
                    $em->flush();
                    
                    //Ajout & édition d'un contact
                    $left_contact_id = [];
                    if (isset($posted['contacts'])) {
                        foreach ($posted['contacts'] as $contact) {
                            $data = $contactClientService->parseData($contact);
                            $data['client-prospect'] = (int)$prospect->getId();
                            $contactClientID = $contactClientService->saveData($data);
                            $left_contact_id[] = $contactClientID;
                        }
                    }
                    
                    //Suppression des contacts supprimés
                    $rem = $this->getDoctrine()->getManager();
                    $all = count($left_contact_id) == 0;
                    $contactsToRemove = $this->getDoctrine()
                        ->getRepository('AppBundle:OneContactClient')
                        ->getContactsToRemove($left_contact_id, $prospect->getId(), $all);
                    foreach($contactsToRemove as $contact) {
                        $rem->remove($contact);
                    }
                    $rem->flush();


                    //Ajout recommandation
                    if(isset($posted['nom-recommandation'])){

                        $recommandations = $this->getDoctrine()
                            ->getRepository('AppBundle:OneProspectOrigineContact')
                            ->findBy(array('tiers' => $prospect));

                        if(count($recommandations) > 0){
                            $recommandation = $recommandations[0];
                        }
                        else {
                            $recommandation = new OneProspectOrigineContact();
                            $recommandation->setTiers($prospect);
                        }


                        $recommandation->setNom($posted['nom-recommandation']);

                        $prenom = $posted['prenom-recommandation'];
                        if($prenom !== ''){
                            $recommandation->setPrenom($prenom);
                        }

                        $societe = $posted['societe-recommandation'];
                        if($societe !== ''){
                            $recommandation->setSociete($societe);
                        }

                        $em->persist($recommandation);

                        $em->flush();
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
     * Suppresion d'un prospect
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $prospect = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($id);
            
            //Suppression des contacts correspondants
            $contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')
                ->findBy(array('tiers' => $prospect));
            foreach ($contacts as $contact) {
                $em->remove($contact);
            }
            
            $em->remove($prospect);
            $em->flush();
            
            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
        
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function archiveAction(Request $request, $id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $prospect = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($id);


            $archive = $request->query
                ->get('archive');

            if((int)$archive === 1){
                $prospect->setArchive(0);
                $em->flush();
            }
            elseif((int) $archive === 0 ) {
                $prospect->setArchive(1);
                $em->flush();
            }

            $response = array('type' => 'success', 'action' => 'archive');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'archive');
            return new JsonResponse($response);
        }

    }
    
    public function duplicateAction(Request $request, $dossierId) {
        $service = new ClientProspectService($this->getDoctrine()->getManager());
        if ($request->isMethod('GET')) {
            return $this->render('OneProspectBundle:Prospect:duplicate.html.twig');
        }

        if ($request->isMethod('POST')) {
            try {
                $em = $this->getDoctrine()->getManager();
                $posted = $request->request->all();
                foreach ($posted['duplicated'] as $account) {
                    $prospect = new Tiers();
                    $fields = $service->parseData($account);
                    $account = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($fields['id']);

                    $dossier = $account->getDossier();

                    if ($fields['code'] == '') {
                        $prospect->setNumeroClient($service->getNextCode($dossier));
                    } else {
                        $prospect->setNumeroClient($fields['code']);
                    }

                    if ($account->getPaysFacturation()) {
                        $facCountry = $this->getDoctrine()
                            ->getRepository('AppBundle:Pays')
                            ->find($account->getPaysFacturation()->getId());
                        $prospect->setPaysFacturation($facCountry);
                    }
                    if ($account->getPaysLivraison()) {
                        $livCountry = $this->getDoctrine()
                            ->getRepository('AppBundle:Pays')
                            ->find($account->getPaysLivraison()->getId());
                        $prospect->setPaysLivraison($livCountry);
                    }
                    if ($account->getOneQualification()) {
                        $qualification = $this->getDoctrine()
                            ->getRepository('AppBundle:OneQualification')
                            ->find($account->getOneQualification()->getId());
                        $prospect->setOneQualification($qualification);
                    }
                    if ($account->getOneReglement()) {
                        $reglement = $this->getDoctrine()
                            ->getRepository('AppBundle:OneReglement')
                            ->find($account->getOneReglement()->getId());
                        $prospect->setOneReglement($reglement);
                    }
                    if ($account->getOneFamillePrix()) {
                        $priceFamily = $this->getDoctrine()
                            ->getRepository('AppBundle:OneFamillePrix')
                            ->find($account->getOneFamillePrix()->getId());
                        $prospect->setOneFamillePrix($priceFamily);
                    }
                    if ($account->getTvaTaux()) {
                        $tva = $this->getDoctrine()
                            ->getRepository('AppBundle:OneTva')
                            ->find($account->getTvaTaux()->getId());
                        $prospect->setTvaTaux($tva);
                    }
                    if ($account->getFormeJuridique()) {
                        $legalForm = $this->getDoctrine()
                            ->getRepository('AppBundle:FormeJuridique')
                            ->find($account->getFormeJuridique()->getId());
                        $prospect->setFormeJuridique($legalForm);
                    }

                    if($account->getPremierContact()){
                        $prospect->setPremierContact($account->getPremierContact());
                    }

                    $dossierId = Boost::deboost($dossierId, $this);

                    $dossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($dossierId);

                    if(null === $dossier) {
                        $response = array('type' => 'error', 'action' => 'duplicate');
                        return new JsonResponse($response);
                    }

                    $prospect->setStatus($account->getStatus());
                    $prospect->setCompteStr($account->getCompteStr());
                    $prospect->setType($account->getType());
                    $prospect->setEmail($account->getEmail());
                    $prospect->setTelephone($account->getTelephone());
                    $prospect->setAdresseFacturation1($account->getAdresseFacturation1());
                    $prospect->setAdresseFacturation2($account->getAdresseFacturation2());
                    $prospect->setVilleFacturation($account->getVilleFacturation());
                    $prospect->setCodePostalFacturation($account->getCodePostalFacturation());
                    $prospect->setAdresseLivraisonIdentique($account->getAdresseLivraisonIdentique());
                    $prospect->setAdresseLivraison1($account->getAdresseLivraison1());
                    $prospect->setAdresseLivraison2($account->getAdresseLivraison2());
                    $prospect->setVilleLivraison($account->getVilleLivraison());
                    $prospect->setCodePostalLivraison($account->getCodePostalLivraison());
                    $prospect->setSiteWeb($account->getSiteWeb());
                    $prospect->setNote($account->getNote());
                    $prospect->setDossier($dossier);
                    $prospect->setTvaPrioritaire($account->getTvaPrioritaire());
                    $prospect->setEmailingAutorise($account->getEmailingAutorise());
                    $prospect->setNomEntreprise($account->getNomEntreprise());
                    $prospect->setNbSalarie($account->getNbSalarie());
                    $prospect->setOneActivite($account->getOneActivite());
                    $prospect->setSiret($account->getSiret());
                    $prospect->setTvaIntracom($account->getTvaIntracom());
                    $prospect->setNom($account->getNom());
                    $prospect->setPrenom($account->getPrenom());
                    $prospect->setIntitule($account->getIntitule());
                    $prospect->setParticulierEntreprise($account->getParticulierEntreprise());
                    $prospect->setCreeLe(new \DateTime('now'));


                    $em->persist($prospect);
                    $em->flush();



                    if($account->getOneProspectOrigine()){
                        $prospect->setOneProspectOrigine($account->getOneProspectOrigine());

                        /** @var OneProspectOrigine $origine */
                        $origine = $account->getOneProspectOrigine();

                        if($origine->getCode() === "CODE_RECOMMANDATION"){
                            $oldProspectOrigines = $this->getDoctrine()
                                ->getRepository('AppBundle:OneProspectOrigineContact')
                                ->findBy(array('tiers' => $account));

                            if(count($oldProspectOrigines) > 0){
                                $oldProspectOrigine = $oldProspectOrigines[0];
                                $prospectOrigne = new OneProspectOrigineContact();

                                $prospectOrigne->setTiers($account);
                                $prospectOrigne->setSociete($oldProspectOrigine->getSociete());
                                $prospectOrigne->setNom($oldProspectOrigine->getNom());
                                $prospectOrigne->setPrenom($oldProspectOrigine->getPrenom());

                                $em->persist($prospectOrigne);
                                $em->flush();
                            }
                        }
                    }
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

        $dossierId = $request->request->get('dossierId');

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($dossierId, $this));
        /** @var Tiers[] $prospects */
        $prospects = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->getProspects($dossier);

        return $this->render('OneProspectBundle:Prospect:listinmodal.html.twig', array(
            'prospects' => $prospects,
        ));
    }
}
