<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 03/04/2018
 * Time: 16:32
 */

namespace One\AchatBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\OneAchat;
use AppBundle\Entity\OneContactFournisseur;
use AppBundle\Entity\OneFournisseur;
use AppBundle\Entity\OneReglement;
use AppBundle\Entity\OneTypeImpot;
use AppBundle\Entity\Pays;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Tiers;
use One\AchatBundle\Service\AchatService;
use One\AchatBundle\Service\ContactFournisseurService;
use One\AchatBundle\Service\FournisseurService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FournisseurController extends Controller
{
    /**
     * @param $id
     * @return JsonResponse
     */
    public function deleteAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $fournisseur = $this->getDoctrine()
                ->getRepository('AppBundle:OneFournisseur')
                ->find($id);

            //Suppression des contacts correspondants
            $contacts = $this->getDoctrine()
                ->getRepository('AppBundle:OneContactFournisseur')
                ->findBy(array('oneFournisseur'=> $fournisseur));
            foreach ($contacts as $contact) {
                $em->remove($contact);
            }

            $em->remove($fournisseur);
            $em->flush();

            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
    }



    public function duplicateAction(Request $request) {
        $service = new FournisseurService($this->getDoctrine()->getManager());
        if ($request->isMethod('GET')) {
            return $this->render('OneAchatBundle:Fournisseur:duplicate.html.twig');
        }

        if ($request->isMethod('POST')) {
            try {
                $posted = $request->request->all();


                foreach ($posted['duplicated'] as $account) {

                    $fournisseur = new OneFournisseur();

                    $fields = $service->parseData($account);
                    $account = $this->getDoctrine()
                        ->getRepository('AppBundle:OneFournisseur')
                        ->find($fields['id']);

                    if ($fields['code'] == '') {
                        $fournisseur->setNumeroFournisseur($service->getNextCode($account->getDossier()));
                    } else {
                        $fournisseur->setNumeroFournisseur($fields['code']);
                    }

                    if ($account->getPays()) {
                        $fournisseur->setPays($account->getPays());
                    }

                    if ($account->getOneReglement()) {
                        $fournisseur->setOneReglement($account->getOneReglement());
                    }

                    if($account->getPcc()){
                        $fournisseur->setPcc($account->getPcc());
                    }

                    if($account->getOneTypeImpot()){
                        $fournisseur->setOneTypeImpot($account->getOneTypeImpot());
                    }

                    $fournisseur->setType($account->getType());
                    $fournisseur->setEmail($account->getEmail());
                    $fournisseur->setTelephone($account->getTelephone());
                    $fournisseur->setAdresse($account->getAdresse());
                    $fournisseur->setVille($account->getVille());
                    $fournisseur->setCodePostal($account->getCodePostal());
                    $fournisseur->setSiteWeb($account->getSiteWeb());
                    $fournisseur->setNote($account->getNote());
                    $fournisseur->setDossier($account->getDossier());
                    $fournisseur->setNomEntreprise($account->getNomEntreprise());
                    $fournisseur->setSiret($account->getSiret());
                    $fournisseur->setNom($account->getNom());
                    $fournisseur->setPrenom($account->getPrenom());
                    $fournisseur->setNomVisible($account->getNomVisible());
                    $fournisseur->setCreeLe(new \DateTime('now'));

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($fournisseur);
                    $em->flush();
                }
                $response = array('type' => 'success', 'action' => 'duplicate');
                return new JsonResponse($response);
            } catch (\Exception $ex) {
                $response = array('type' => 'error', 'action' => 'duplicate');
                return new JsonResponse($response);
            }
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    /**
     * Edition d'un Fournisseur
     * @return Response
     */
    public function editAction(Request $request, $id) {

        $post = $request->query;
        $dossierId = Boost::deboost($post->get('dossierId'), $this);

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $countries = $this->getDoctrine()
            ->getRepository('AppBundle:Pays')
            ->findBy(array(), array('nom' => 'ASC'));

        /** @var OneReglement[] $reglements */
        $reglements = $this->getDoctrine()
            ->getRepository('AppBundle:OneReglement')
            ->getReglements();

        /** @var OneTypeImpot[] $typeImpots */
        $typeImpots = $this->getDoctrine()
            ->getRepository('AppBundle:OneTypeImpot')
            ->findBy(array(), array('libelle' => 'ASC'));

        $fournisseur = $this->getDoctrine()
            ->getRepository('AppBundle:OneFournisseur')
            ->find($id);

        /** @var Pcc[] $pccs */
        $pccs = $this->getDoctrine()
            ->getRepository('AppBundle:Pcc')
            ->getPccByDossierLike($dossier, array('40'));

        /** @var OneContactFournisseur[] $contacts */
        $contacts = $this->getDoctrine()
            ->getRepository('AppBundle:OneContactFournisseur')
            ->findBy(array('oneFournisseur' => $fournisseur));
        return $this->render('OneAchatBundle:Fournisseur:edit.html.twig', array(
            'countries' => $countries,
            'reglements' => $reglements,
            'typeImpots' => $typeImpots,
            'pccs' => $pccs,
            'fournisseur' => $fournisseur,
            'contacts' => $contacts
        ));


    }

    /**
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request){
        if ($request->isMethod('GET')) {

            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');

            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);


            /** @var Tiers[] $tiers */
            $tiers = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getTiersByDossier($dossier, array(2), '401' );
            //fin lesexperts.biz

            if(null === $dossier){
                return new Response('');
            }


            /** @var OneFournisseur[] $fournisseurs */
            $fournisseurs = $this->getDoctrine()
                ->getRepository('AppBundle:OneFournisseur')
                ->getFournisseurs($dossier, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            return $this->render('OneAchatBundle:Fournisseur:list.html.twig', array(
                'fournisseurs' => $fournisseurs,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'tiers' => $tiers
            ));
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }




    public function listinmodalAction(Request $request) {

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->request->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);
        //fin lesexperts.biz

        /** @var OneFournisseur[] $fournisseurs */
        $fournisseurs = $this->getDoctrine()
            ->getRepository('AppBundle:OneFournisseur')
            ->findBy(array('dossier' => $dossier));

        return $this->render('OneAchatBundle:Fournisseur:listinmodal.html.twig', array(
            'fournisseurs' => $fournisseurs,
        ));
    }



    /**
     * Création d'un client
     * @return Response
     */
    public function newAction(Request $request) {

        $post = $request->request;
        $dossierId = Boost::deboost($post->get('dossierId'), $this);

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $countries = $this->getDoctrine()
            ->getRepository('AppBundle:Pays')
            ->findBy(array(), array('nom' => 'ASC'));

        $reglements = $this->getDoctrine()->getRepository('AppBundle:OneReglement')->getReglements();

        $typeImpots = $this->getDoctrine()
            ->getRepository('AppBundle:OneTypeImpot')
            ->findBy(array(), array('libelle' => 'ASC'));

        /** @var Pcc[] $pccs */
        $pccs = $this->getDoctrine()
            ->getRepository('AppBundle:Pcc')
            ->getPccByDossierLike($dossier, array('40'));

        return $this->render('OneAchatBundle:Fournisseur:new.html.twig', array(
            'countries' => $countries,
            'reglements' => $reglements,
            'typeImpots' => $typeImpots,
            'pccs' => $pccs


        ));
    }

    /**
     * Sauvegarde l'ajout ou la modification d'un fournisseur
     */
    public function saveAction(Request $request) {
        if ($request->getMethod() === 'POST') {

            $service = new FournisseurService($this->getDoctrine()->getEntityManager());
            $contactFournisseurService = new ContactFournisseurService($this->getDoctrine()->getEntityManager());

            $posted = $request->request->all();

            $dossierId = Boost::deboost($posted['id-dossier'], $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if(null === $dossier){
                $response = array('type' => 'error', 'action' => 'edit', 'id' => $posted['id']);
                return new JsonResponse($response);
            }


            $facCountry = $this->getDoctrine()
                ->getRepository('AppBundle:Pays')
                ->find($posted['pays-facturation']);

            $reglement = $this->getDoctrine()
                ->getRepository('AppBundle:OneReglement')
                ->find($posted['reglement']);

            $pcc = $this->getDoctrine()
                ->getRepository('AppBundle:Pcc')
                ->find($posted['pcc']);

//            $typeImpot = $this->getDoctrine()
//                ->getRepository('AppBundle:OneTypeImpot')
//                ->find($posted['type-impot']);


            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $fournisseur = new OneFournisseur();


                    $fournisseur->setType($posted['fournisseur-type']);
                    $fournisseur->setEmail($posted['email']);
                    $fournisseur->setTelephone($posted['telephone']);
                    $fournisseur->setPays($facCountry);
                    $fournisseur->setSiteWeb($posted['site-web']);
                    $fournisseur->setOneReglement($reglement);
                    $fournisseur->setDossier($dossier);
                    $fournisseur->setPcc($pcc);
//                    $fournisseur->setOneTypeImpot($typeImpot);


                    $fournisseur->setVille($posted['ville']);
                    $fournisseur->setTelephone2($posted['ligne-2']);
                    $fournisseur->setCodePostal($posted['code-postal']);
                    $fournisseur->setAdresse($posted['adresse-facturation']);
                    $fournisseur->setNote($posted['note']);


                    $fournisseur->setCreeLe(new \DateTime('now'));
                    if ($posted['numero-fournisseur'] == '') {
                        $fournisseur->setNumeroFournisseur($service->getNextCode($dossier));
                    } else {
                        $fournisseur->setNumeroFournisseur($service->getNextCustomCode($dossier, $posted['numero-fournisseur']));
                    }

                    //Entreprise
                    if ((int)$posted['fournisseur-type'] === 2) {
                        $fournisseur->setNomEntreprise($posted['nom-entreprise']);
                        $fournisseur->setSiret($posted['siret']);
                        $fournisseur->setNomVisible($posted['nom-entreprise']);

                        //Réinitialisation des champs particulier
                        $fournisseur->setNom('');
                        $fournisseur->setPrenom('');
                    }
                    //Particulier
                    else {
                        $fournisseur->setNom($posted['nom']);
                        $fournisseur->setPrenom($posted['prenom']);
                        $fournisseur->setNomVisible($posted['nom']);

                        if ($posted['prenom'] != '')
                            $fournisseur->setNomVisible($posted['prenom'].' '.$posted['nom']);

                        //Réinitialisation des champs entreprise
                        $fournisseur->setNomEntreprise('');
                        $fournisseur->setSiret('');
                    }

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($fournisseur);
                    $em->flush();

                    //Ajout d'un contact
                    if (isset($posted['contacts'])) {
                        foreach ($posted['contacts'] as $contact) {
                            $data = $contactFournisseurService->parseData($contact);
                            $data['fournisseur'] = (int)($fournisseur->getId());
                            $contactFournisseurService->saveData($data);
                        }
                    }

                    $response = array('type' => 'success', 'action' => 'add', 'id' => $fournisseur->getId());
                    return new JsonResponse($response);
                } catch (Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            }

            //Edition
            else {
                try {
                    $em = $this->getDoctrine()->getManager();

                    $fournisseur = $this->getDoctrine()
                        ->getRepository('AppBundle:OneFournisseur')
                        ->find($posted['id']);

                    $fournisseur->setType($posted['fournisseur-type']);
                    $fournisseur->setEmail($posted['email']);
                    $fournisseur->setTelephone($posted['telephone']);
                    $fournisseur->setPays($facCountry);
                    $fournisseur->setSiteWeb($posted['site-web']);
                    $fournisseur->setOneReglement($reglement);
                    $fournisseur->setDossier($dossier);
                    $fournisseur->setPcc($pcc);

                    $fournisseur->setVille($posted['ville']);
                    $fournisseur->setTelephone2($posted['ligne-2']);
                    $fournisseur->setCodePostal($posted['code-postal']);
                    $fournisseur->setAdresse($posted['adresse-facturation']);
                    $fournisseur->setNote($posted['note']);

                    $fournisseur->setCreeLe(new \DateTime('now'));
                    if ($posted['numero-fournisseur'] == '') {
                        $fournisseur->setNumeroFournisseur($service->getNextCode($dossier));
                    } else {
                        $fournisseur->setNumeroFournisseur($service->getNextCustomCode($dossier, $posted['numero-fournisseur']));
                    }

                    //Entreprise
                    if ((int)$posted['fournisseur-type'] === 2) {
                        $fournisseur->setNomEntreprise($posted['nom-entreprise']);
                        $fournisseur->setSiret($posted['siret']);
                        $fournisseur->setNomVisible($posted['nom-entreprise']);

                        //Réinitialisation des champs particulier
                        $fournisseur->setNom('');
                        $fournisseur->setPrenom('');
                    }
                    //Particulier
                    else {
                        $fournisseur->setNom($posted['nom']);
                        $fournisseur->setPrenom($posted['prenom']);
                        $fournisseur->setNomVisible($posted['nom']);

                        if ($posted['prenom'] != '')
                            $fournisseur->setNomVisible($posted['prenom'].' '.$posted['nom']);

                        //Réinitialisation des champs entreprise
                        $fournisseur->setNomEntreprise('');
                        $fournisseur->setSiret('');
                    }

                    $em->flush();



                    //Ajout & édition d'un contact
                    $left_contact_id = [];
                    if (isset($posted['contacts'])) {
                        foreach ($posted['contacts'] as $contact) {
                            $data = $contactFournisseurService->parseData($contact);
                            $data['fournisseur'] = (int)($fournisseur->getId());
                            $contactFournisseurID = $contactFournisseurService->saveData($data);
                            $left_contact_id[] = $contactFournisseurID;
                        }
                    }

                    //Suppression des contacts supprimés
                    $rem = $this->getDoctrine()->getManager();
                    $all = count($left_contact_id) == 0;
                    $contactsToRemove = $this->getDoctrine()
                        ->getRepository('AppBundle:OneContactFournisseur')
                        ->getContactsToRemove($left_contact_id, $fournisseur->getId(), $all);

                    foreach($contactsToRemove as $contact) {
                        $rem->remove($contact);
                    }
                    $rem->flush();

                    $response = array('type' => 'success', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                }
                catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                }
            }
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    /**
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function showAction(Request $request, $id, $one) {
        if ($request->isMethod('GET')) {
//            $venteService = new VenteService($this->getDoctrine()->getManager());
//            $encaissementService = new EncaissementService($this->getDoctrine()->getManager());

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

            $fournisseurs = $this->getDoctrine()
                ->getRepository('AppBundle:OneFournisseur')
                ->getAccounts($dossier);
            //fin lesexperts.biz


            $achatService = new AchatService($this->getDoctrine()->getEntityManager());


            $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);


            $fournisseur = null;
            $contacts = null;
            $imputations = null;
            $tiers = null;
            $factures = array();
            /** @var OneAchat[] $commandes */
            $commandes = array();

            if ((int)$one === 1) {
                $fournisseur = $this->getDoctrine()
                    ->getRepository('AppBundle:OneFournisseur')
                    ->find($id);

                /** @var OneContactFournisseur[] $contacts */
                $contacts = $this->getDoctrine()
                    ->getRepository('AppBundle:OneContactFournisseur')
                    ->getContacts($id);

                /** @var OneAchat[] $factures */
                $factures = $this->getDoctrine()
                    ->getRepository('AppBundle:OneAchat')
                    ->getAchatsByFournisseur($fournisseur, 'facture', $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);


                $commandes = $this->getDoctrine()
                    ->getRepository('AppBundle:OneAchat')
                    ->getAchatsByFournisseur($fournisseur, 'commande', $type, $sort, $sortorder, $q, $period, $startperiod, $endperiod);


            } else {
                $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($id);

                $imputations = $this->getDoctrine()
                    ->getRepository('AppBundle:ImputationControle')
                    ->getFactureClientsByTiers($tiers, $q);
            }
            return $this->render('OneAchatBundle:Fournisseur:show.html.twig', array(
                'fournisseur' => $fournisseur,
                'contacts' => $contacts,
                'factures' => $factures,
                'commandes' => $commandes,
                'imputations' => $imputations,
                'factureDetails' => $achatService->getAchatDetails($fournisseurs, 'facture'),
                'type' => $type,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
                'tiers' => $tiers,
                'commandeDetails' => $achatService->getAchatDetails($fournisseurs, 'commande')
            ));
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function listcontactAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('GET')) {
                $fournisseurID = $request->query->get('fournisseur');
                $contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactFournisseur')->getContacts($fournisseurID);
                return $this->render('OneProspectBundle:Opportunite:listcontact.html.twig', array(
                    'contacts' => $contacts,
                ));
            }
        }
        throw  new AccessDeniedHttpException('Accès refusé');
    }



}