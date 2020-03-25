<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 07/02/2017
 * Time: 08:16
 */

namespace InfoPerdosBundle\Controller;


use AppBundle\Entity\ClientFichier;
use AppBundle\Entity\ClientLogiciel;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\DossierFichier;
use AppBundle\Entity\InstructionDossier;
use AppBundle\Entity\InstructionTexte;
use AppBundle\Entity\Logiciel;
use AppBundle\Entity\ReglePaiementClient;
use AppBundle\Entity\RemarqueClient;
use AppBundle\Entity\RemarqueDossier;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class InstructionDossierController extends Controller
{
    public function indexAction(Request $request, $json)
    {
        $methodeSuiviCheques = $this->getDoctrine()
            ->getRepository('AppBundle:MethodeSuiviCheque')
            ->findBy(array(), array('id' => 'asc'));

        $gestionDateEcritures = $this->getDoctrine()
            ->getRepository('AppBundle:GestionDateEcriture')
            ->findBy(array(), array('libelle' => 'asc'));

        $instructionTypes = $this->getDoctrine()
            ->getRepository('AppBundle:InstructionType')
            ->findAll();

        /** @var Logiciel[] $logiciels */
        $logiciels = $this->getDoctrine()
            ->getRepository('AppBundle:Logiciel')
            ->findAll();



        //Affichage @ voalohany
        if ($json == 0) {

            return $this->render('InfoPerdosBundle:InstructionDossier:index.html.twig', array(
                'methodeSuiviCheques' => $methodeSuiviCheques,
                'gestionDateEcritures' => $gestionDateEcritures,
                'instructionDossier' => null,
                'instrucitonTypes' => $instructionTypes,
                'remarque' => null,
                'reglePaiementClientFournisseur' =>  null,
                'reglePaiementClientClient' => null,
                'logiciels' => $logiciels
            ));
        }

        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $idClient = Boost::deboost($post->get('clientId'), $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($idClient);

            /* @var $instructionDossier InstructionDossier */
            $instructionDossier = $this->getDoctrine()
                ->getRepository('AppBundle:InstructionDossier')
                ->getInstructionDossierByClient($client);

            $rapprochementBanque = null;
            $suiviChequeEmis = null;
            $gestionDateEcriture = null;
            $noteFrais = null;

            $instructionVal = array();

            $remarque = '';

            $remarqueClient = $this->getDoctrine()
                ->getRepository('AppBundle:RemarqueClient')
                ->findOneBy(array('client'=>$client));

            if(null !== $remarqueClient){
                $remarque = $remarqueClient->getRemarque();
            }

            $reglePaiementClientClient = array('typeDate'=>null, 'nbreJour'=>null, 'dateLe'=>null);

            $reglePaiementClientClients = $this->getDoctrine()
                ->getRepository('AppBundle:ReglePaiementClient')
                ->findBy(array('client' => $client, 'typeTiers' => 1));

            if(count($reglePaiementClientClients) > 0){
                /** @var ReglePaiementClient $temp */
                $temp = $reglePaiementClientClients[0];

                $reglePaiementClientClient = array(
                    'typeDate' => $temp->getTypeDate(),
                    'nbreJour' =>$temp->getNbreJour(),
                    'dateLe' => $temp->getDateLe());
            }

            $reglePaiementClientFournisseur = array('typeDate'=>null, 'nbreJour'=>null, 'dateLe'=>null);;

            $reglePaiementClientFournisseurs = $this->getDoctrine()
                ->getRepository('AppBundle:ReglePaiementClient')
                ->findBy(array('client' => $client, 'typeTiers' => 0));

            if(count($reglePaiementClientFournisseurs) > 0){
                /** @var ReglePaiementClient $temp */
                $temp = $reglePaiementClientFournisseurs[0];

                $reglePaiementClientFournisseur = array(
                    'typeDate' => $temp->getTypeDate(),
                    'nbreJour' =>$temp->getNbreJour(),
                    'dateLe' => $temp->getDateLe());

            }


            $findInstruction = false;

            if ($instructionDossier != null) {

                $findInstruction = true;

                if ($instructionDossier->getMethodeSuiviCheque() != null) {
                    $suiviChequeEmis = $instructionDossier->getMethodeSuiviCheque()->getId();
                }

                if ($instructionDossier->getGestionDateEcriture() != null) {
                    $gestionDateEcriture = $instructionDossier->getGestionDateEcriture()->getId();
                }

                $instructionVal = array('1' => $instructionDossier->getPetiteDepense(),
                    '2' => $instructionDossier->getImmobilisation(),
                    '3' => $instructionDossier->getTva(),
                    '4' => $instructionDossier->getCaisse(),
                    '5' => $instructionDossier->getBanque(),
                    '6' => $instructionDossier->getVehicule(),
                    '7' => $instructionDossier->getFraisRepresentation(),
                    '8' => $instructionDossier->getRestaurant(),
                    '9' => $instructionDossier->getHebergement(),
                    '10' => $instructionDossier->getDeplacement(),
                    '11' => $instructionDossier->getCadeauEntreprise(),
                    '12' => $instructionDossier->getLogement(),
                    '13' => $instructionDossier->getCreationTiers()
                );

                $rapprochementBanque = $instructionDossier->getRapprochementBanque();
            }

            $res = array('rapprochementBanque' => $rapprochementBanque,
                'suiviChequeEmis' => $suiviChequeEmis,
                'gestionDateEcriture' => $gestionDateEcriture,
                'instructionVal' => $instructionVal,
                'instructionDossier' => $instructionDossier,
                'remarque'=>$remarque,
                'findInstruction' => $findInstruction,
                'reglePaiementClientClient' => $reglePaiementClientClient,
                'reglePaiementClientFournisseur' => $reglePaiementClientFournisseur,
            );


            return new JsonResponse($res);
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    public function editInstrInstructionSaisieAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $post = $request->request;

            $instructionVal = $post->get('instructionVal');

            $clientId = Boost::deboost($post->get('clientId'), $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($clientId);

            /* @var $instructionDossier InstructionDossier */
            $newInstructionDossier = $this->getDoctrine()
                ->getRepository('AppBundle:InstructionDossier')
                ->getInstructionDossierByClient($client);

            //Ajout
            if ($newInstructionDossier == null) {
                try {
                    $newInstructionDossier = new InstructionDossier();

                    $newInstructionDossier->setClient($client);
                    $newInstructionDossier->setPetiteDepense($instructionVal[1]);
                    $newInstructionDossier->setImmobilisation($instructionVal[2]);
                    $newInstructionDossier->setTva($instructionVal[3]);
                    $newInstructionDossier->setCaisse($instructionVal[4]);
                    $newInstructionDossier->setBanque($instructionVal[5]);
                    $newInstructionDossier->setVehicule($instructionVal[6]);
                    $newInstructionDossier->setFraisRepresentation($instructionVal[7]);
                    $newInstructionDossier->setRestaurant($instructionVal[8]);
                    $newInstructionDossier->setHebergement($instructionVal[9]);
                    $newInstructionDossier->setDeplacement($instructionVal[10]);
                    $newInstructionDossier->setCadeauEntreprise($instructionVal[11]);
                    $newInstructionDossier->setLogement($instructionVal[12]);
                    $newInstructionDossier->setCreationTiers($instructionVal[13]);

                    $em->flush();

                    return new Response(1);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            } //Mise à jour
            else {
                try {
                    $newInstructionDossier->setPetiteDepense($instructionVal[1]);
                    $newInstructionDossier->setImmobilisation($instructionVal[2]);
                    $newInstructionDossier->setTva($instructionVal[3]);
                    $newInstructionDossier->setCaisse($instructionVal[4]);
                    $newInstructionDossier->setBanque($instructionVal[5]);
                    $newInstructionDossier->setVehicule($instructionVal[6]);
                    $newInstructionDossier->setFraisRepresentation($instructionVal[7]);

                    $newInstructionDossier->setRestaurant($instructionVal[8]);
                    $newInstructionDossier->setHebergement($instructionVal[9]);
                    $newInstructionDossier->setDeplacement($instructionVal[10]);
                    $newInstructionDossier->setCadeauEntreprise($instructionVal[11]);
                    $newInstructionDossier->setLogement($instructionVal[12]);
                    $newInstructionDossier->setCreationTiers($instructionVal[13]);

                    $em->persist($newInstructionDossier);
                    $em->flush();

                    return new Response(2);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            }

        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    public function editInstrInstructionTexteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $post = $request->request;

            $instructionTypeId = $post->get('instructionType');
            $instructionType = null;
            if ($instructionTypeId != '-1') {
                try {
                    $instructionType = $this->getDoctrine()
                        ->getRepository('AppBundle:InstructionType')
                        ->find($instructionTypeId);

                    /* @var $instructionTexte InstructionTexte */
                    $instructionTexte = $this->getDoctrine()
                        ->getRepository('AppBundle:InstructionTexte')
                        ->getInstructionTexteByInstructionType($instructionType);

//                    $instructionTexte->setContenu($post->get('instructionTexte'));
                    //Manala <div class="xxx"> </div>
                    $instructionTxt = preg_replace('/\<[\/]{0,1}div[^\>]*\>/i', '', $post->get('instructionTexte'));

                    $instructionTexte->setContenu($instructionTxt);
                    $em->flush();
                    return new Response(1);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            } else {
                return new Response(-1);
            }
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function editInstructionAction(Request $request){
        if($request->isXmlHttpRequest()){
            $post = $request->request;

            $idClient = Boost::deboost($post->get('clientId'), $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($idClient);

            /* @var $instructionDossier InstructionDossier */
            $instructionDossier = $this->getDoctrine()
                ->getRepository('AppBundle:InstructionDossier')
                ->getInstructionDossierByClient($client);

            $instructionTxt = preg_replace('/\<[\/]{0,1}div[^\>]*\>/i', '', $post->get('instruction'));

            $em = $this->getDoctrine()
                ->getEntityManager();


            if(is_null($instructionDossier)){
                try {
                    $newInstructionDossier = new InstructionDossier();

                    $newInstructionDossier->setClient($client);

                    $newInstructionDossier->setInstruction($instructionTxt);

                    $em->persist($newInstructionDossier);
                    $em->flush();

                    return new Response(1);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            }
            else{
                $instructionDossier->setInstruction($instructionTxt);
                $em->flush();

                return new Response(2);
            }
        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }
    public function listePieceJointeAction(Request $request){
        if($request->isXmlHttpRequest()){


            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

            if($request->query->get('clientId') == -1){
                $dossierId = Boost::deboost($request->query->get('dossierId'), $this);

                /** @var Dossier $dossier */
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($dossierId);

                $piecesJointes = $this->getDoctrine()
                    ->getRepository('AppBundle:DossierFichier')
                    ->findBy(array('dossier' => $dossier));

                $res = [];

                foreach ($piecesJointes as $piecesJointe){
                    $temp = [];
                    $temp['piecejointe'] = $piecesJointe;
                    $temp['chemin'] = $baseurl. "/INSTRUCTION/". $dossier->getSite()->getClient()->getId(). "/".$dossier->getId()."/".$piecesJointe->getFichier();

                    $res[] = $temp;
                }

                return $this->render('InfoPerdosBundle:PrestationComptable:dossierFichierList.html.twig',
                    array('piecesJointes' => $res));

            }
            else{
                $clientId = Boost::deboost($request->query->get('clientId'), $this);

                $client = $this->getDoctrine()
                    ->getRepository('AppBundle:Client')
                    ->find($clientId);

                $piecesJointes = $this->getDoctrine()
                    ->getRepository('AppBundle:ClientFichier')
                    ->findBy(array('client' => $client));

                $res = [];

                foreach ($piecesJointes as $piecesJointe){
                    $temp = [];
                    $temp['piecejointe'] = $piecesJointe;
                    $temp['chemin'] = $baseurl. "/INSTRUCTION/". $client->getId(). "/".$piecesJointe->getFichier();

                    $res[] = $temp;
                }

                return $this->render('InfoPerdosBundle:PrestationComptable:dossierFichierList.html.twig',
                    array('piecesJointes' => $res));
            }

        }
        throw new AccessDeniedHttpException('Accès refusé');
    }

    public function uploadPieceJointeAction(Request $request, $clientId, $dossierId){
        if ($request->isXmlHttpRequest()) {

            $client = null;
            $dossier = null;

            if($clientId == -1){
                $dossierId = Boost::deboost($dossierId, $this);

                /** @var Dossier $dossier */
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($dossierId);
            }
            else {

                $clientId = Boost::deboost($clientId, $this);

                $client = $this->getDoctrine()
                    ->getRepository('AppBundle:Client')
                    ->find($clientId);
            }

            if($dossier === null) {
                $directory = "INSTRUCTION/" . $client->getId() . "/";
            }
            else{
                $directory = "INSTRUCTION/". $dossier->getSite()->getClient()->getId(). "/".$dossier->getId()."/";
            }

            $fs = new Filesystem();
            try {
                $fs->mkdir($directory, 0777);
            } catch (IOExceptionInterface $e) {
            }

            $file = $request->files->get('pdf_dossier');

            $file_name = $file->getClientOriginalName();
            $file->move($directory, $file_name);


//PICDATA
            $chemin = '/' . $directory . $file_name;

//LOCAL
//                $chemin = '/picdata/web/' . $directory . $file_name;

//192.168.0.5
//            $chemin = '/newpicdata/web/' . $directory. $file_name;



            $em = $this->getDoctrine()->getManager();

            if($client !== null) {

                $clientFichier = new ClientFichier();

                $clientFichier->setClient($client);
                $clientFichier->setFichier($file_name);

                $em->persist($clientFichier);

                $em->flush();

            }
            else{


                if(null !== $dossier) {

                    $dossierFichier = new DossierFichier();

                    $dossierFichier->setDossier($dossier);
                    $dossierFichier->setFichier($file_name);

                    $em->persist($dossierFichier);

                    $em->flush();

                }
            }

            return new JsonResponse($chemin);


        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function deletePieceJointeAction(Request $request){
        if ($request->isXmlHttpRequest()) {

            $client = null;
            $dossier = null;
            $em = $this->getDoctrine()->getManager();

            if($request->request->get('clientId') == -1){

                $dossierFichier = $this->getDoctrine()
                    ->getRepository('AppBundle:DossierFichier')
                    ->find($request->request->get('fichierId'));

                $em->remove($dossierFichier);
                $em->flush();

                return new Response('Supression dossier fichier');
            }
            else {

                $clientFichier = $this->getDoctrine()
                    ->getRepository('AppBundle:ClientFichier')
                    ->find($request->request->get('fichierId'));

                $em->remove($clientFichier);
                $em->flush();

                return new Response('Supression client fichier');
            }

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editInstrMethodeComptableAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $post = $request->request;

            $methodeSuiviChequeId = $post->get('methodeSuiviCheque');
            $methodeSuiviCheque = null;


            $rapprochementBanque = $post->get('rapprochementBanque');

            if ($rapprochementBanque == '') {
                $res = array('estInsere' => 0, 'message' => 'Rapprochement banque');
                return new JsonResponse($res);
            } else if ($rapprochementBanque == 0) {
                $methodeSuiviCheque = null;
            } else {
                if ($methodeSuiviChequeId != '') {
                    $methodeSuiviCheque = $this->getDoctrine()
                        ->getRepository('AppBundle:MethodeSuiviCheque')
                        ->find($methodeSuiviChequeId);
                }
//                else {
//                    $res = array('estInsere' => 0, 'message' => 'Suivi des chèques émis');
//                    return new JsonResponse($res);
//                }
            }


            $gestionDateEcritureId = $post->get('gestionDateEcriture');
            $gestionDateEcriture = null;
            if ($gestionDateEcritureId != '') {
                $gestionDateEcriture = $this->getDoctrine()
                    ->getRepository('AppBundle:GestionDateEcriture')
                    ->find($gestionDateEcritureId);
            }
//            else {
//                $res = array('estInsere' => 0, 'message' => "Gestion des dates d'ecritures");
//                return new JsonResponse($res);
//            }

            $logicielId = $post->get('logiciel');
            $logiciel = null;
            if($logicielId != '') {

                if ($logicielId == -1) {
                    $logicielLib = $post->get('logicielLib');

                    if ($logicielLib != '') {

                        $logiciel = new Logiciel();
                        $logiciel->setLibelle($logicielLib);

                        $em->persist($logiciel);

                        $em->flush();
                    }
                } else {
                    $logiciel = $this->getDoctrine()
                        ->getRepository('AppBundle:Logiciel')
                        ->find($logicielId);
                }
            }
//            else{
//                $res = array('estInsere' => 0, 'message' => 'Logiciel');
//                return new JsonResponse($res);
//            }

            $clientId = Boost::deboost($post->get('clientId'), $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($clientId);

            /* @var $instructionDossier InstructionDossier */
            $newInstructionDossier = $this->getDoctrine()
                ->getRepository('AppBundle:InstructionDossier')
                ->getInstructionDossierByClient($client);


            if(!is_null($logiciel)) {

                $clientLogiciels = $this->getDoctrine()
                    ->getRepository('AppBundle:ClientLogiciel')
                    ->findBy(array('client' => $client, 'logiciel' => $logiciel));

                $clientLogiciel = null;
                if (count($clientLogiciel)) {
                    $clientLogiciel = $clientLogiciels[0];
                }


                if ($clientLogiciel == null) {
                    $clientLogiciel = new ClientLogiciel();
                    $clientLogiciel->setClient($client);
                    $clientLogiciel->setLogiciel($logiciel);

                    $em->persist($clientLogiciel);
                    $em->flush();

                }
            }

            //Ajout
            if ($newInstructionDossier == null) {
                try {
                    $newInstructionDossier = new InstructionDossier();

                    $newInstructionDossier->setClient($client);
                    $newInstructionDossier->setMethodeSuiviCheque($methodeSuiviCheque);
                    $newInstructionDossier->setRapprochementBanque($rapprochementBanque);
                    $newInstructionDossier->setGestionDateEcriture($gestionDateEcriture);

                    $newInstructionDossier->setLogiciel($logiciel);

                    $em->persist($newInstructionDossier);
                    $em->flush();

                    return new Response(1);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            } //Mise à jour
            else {
                try {

                    $newInstructionDossier->setMethodeSuiviCheque($methodeSuiviCheque);
                    $newInstructionDossier->setRapprochementBanque($rapprochementBanque);
                    $newInstructionDossier->setGestionDateEcriture($gestionDateEcriture);

                    $newInstructionDossier->setLogiciel($logiciel);

                    $em->persist($newInstructionDossier);
                    $em->flush();

                    return new Response(2);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            }

        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }




    public function editInstrMethodeComptableV2Action(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $post = $request->request;

            $val = $post->get('value');
            $field = $post->get('field');

            if($field == 'GestionDateEcriture'){
                $value = $this->getDoctrine()
                    ->getRepository('AppBundle:GestionDateEcriture')
                    ->find($val);
            }elseif($field == 'MethodeSuiviCheque'){
                $value = $this->getDoctrine()
                    ->getRepository('AppBundle:MethodeSuiviCheque')
                    ->find($val);
            }else{
                $value = $val;
            }

            $clientId = Boost::deboost($post->get('clientId'), $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($clientId);

            /* @var $instructionDossier InstructionDossier */
            $newInstructionDossier = $this->getDoctrine()
                ->getRepository('AppBundle:InstructionDossier')
                ->getInstructionDossierByClient($client);

            //Ajout
            if ($newInstructionDossier == null) {
                try {
                    $newInstructionDossier = new InstructionDossier();

                    $newInstructionDossier->setClient($client);
                    $newInstructionDossier->{"set$field"}($value);

                    $em->persist($newInstructionDossier);
                    $em->flush();

                    return new Response(1);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            } //Mise à jour
            else {
                try {
                    $newInstructionDossier->{"set$field"}($value);
                    $em->persist($newInstructionDossier);
                    $em->flush();

                    return new Response(2);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            }

        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }









    public function showInstructionAction(Request $request, $json)
    {
        if ($request->isXmlHttpRequest()) {

            if ($json == 'petite-depense') {
                return $this->render('InfoPerdosBundle:InstructionDossier:petiteDepense.html.twig');
            } else if ($json == 'immobilisation') {
                return $this->render('InfoPerdosBundle:InstructionDossier:immobilisation.html.twig');
            } else if ($json == 'tva') {
                return $this->render('InfoPerdosBundle:InstructionDossier:tva.html.twig');
            } else if ($json == 'caisse') {
                return $this->render('InfoPerdosBundle:InstructionDossier:caisse.html.twig');
            } else if ($json == 'banque') {
                return $this->render('InfoPerdosBundle:InstructionDossier:banque.html.twig');
            } else if ($json == 'vehicule') {
                return $this->render('InfoPerdosBundle:InstructionDossier:vehicule.html.twig');
            } else if ($json == 'frais') {
                return $this->render('InfoPerdosBundle:InstructionDossier:fraisRepresentationDivers.html.twig');
            }

        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }

    }

    public function showInstructionTexteAction(Request $request, $json)
    {

        if ($request->isXmlHttpRequest()) {

            $instructionType = $this->getDoctrine()
                ->getRepository('AppBundle:InstructionType')
                ->find($json);

            if ($instructionType != null) {
                /* @var $instructionTexte InstructionTexte */
                $instructionTexte = $this->getDoctrine()
                    ->getRepository('AppBundle:InstructionTexte')
                    ->getInstructionTexteByInstructionType($instructionType);
                return $this->render('InfoPerdosBundle:InstructionDossier:instructionTexte.html.twig', array('instructionTexte' => $instructionTexte));
            } else {
                return new Response('-1');
            }

        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    public function showInstructionDeclineAction(Request $request, $json)
    {
        if ($request->isXmlHttpRequest()) {

            $instructionType = $this->getDoctrine()
                ->getRepository('AppBundle:InstructionType')
                ->find($json);

            if ($instructionType != null) {

                return $this->render('InfoPerdosBundle:InstructionDossier:instructionDecline.html.twig');

            } else {
                return new Response('-1');
            }

        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }



    public function showAutreLogicielAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('InfoPerdosBundle:InstructionDossier:autreLogiciel.html.twig');

        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }




    public function editeurAction(Request $request, $json)
    {
        //@ Voalohany
        if ($json == 0) {
            $instructionTypes = $this->getDoctrine()
                ->getRepository('AppBundle:InstructionType')
                ->findAll();


            //Temp
//            $libelleEcritures = $this->getDoctrine()
//                ->getRepository('AppBundle:LibelleEcriture')
//                ->findBy(array(),array('rang'=>'ASC'));
//
//            $client = $this->getDoctrine()
//                ->getRepository('AppBundle:Client')
//                ->find(553);

//            $libelleClients = $this->getDoctrine()
//                ->getRepository('AppBundle:LibelleEcritureClient')
//                ->findBy(array('client' => $client), array('rang' => 'ASC'));

            $filtredLibelleEcr  = array();

            // 1 2 3 4
//            foreach ($libelleClients as $libelleClient){
//                if(!in_array($libelleClient->getLibelleEcriture(), $libelleEcritures)){
//                    $filtredLibelleEcr[] = $libelleClient;
//                }
//            }


            return $this->render('InfoPerdosBundle:EditionInstruction:index.html.twig', array(
                'instructionTypes' => $instructionTypes,
                'instructionTexte' => null
            ));
        } else {
            if ($request->isXmlHttpRequest()) {
                switch ($json) {
                    case 1:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(1);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));

                        break;
                    case 2:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(2);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));


                        break;

                    case 3:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(3);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));
                        break;
                    case 4:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(4);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));
                        break;

                    case 5:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(5);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));
                        break;

                    case 6:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(6);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));
                        break;

                    case 7:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(7);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));
                        break;

                    case 8:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(8);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));
                        break;

                    case 9:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(9);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));
                        break;


                    case 10:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(10);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));
                        break;


                    case 11:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(11);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));
                        break;


                    case 12:
                        $instructionType = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionType')
                            ->find(12);
                        /* @var $instructionTexte InstructionTexte */
                        $instructionTexte = $this->getDoctrine()
                            ->getRepository('AppBundle:InstructionTexte')
                            ->getInstructionTexteByInstructionType($instructionType);

                        return $this->render('InfoPerdosBundle:EditionInstruction:contenu.html.twig', array('instructionTexte' => $instructionTexte));
                        break;

                    default:
                        break;
                }
            } else {
                throw new AccessDeniedHttpException('Accès Refusé');
            }

        }

    }

    public function verifierInstructionDossierAction(Request $request)
    {

        $post = $request->request;

        $clientId = Boost::deboost($post->get('clientId'), $this);

        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($clientId);

        $instructionDossiers = $this->getDoctrine()
            ->getRepository('AppBundle:InstructionDossier')
            ->findBy(array('client' => $client));

        if (count($instructionDossiers) > 0) {

            $instructionDossier = $instructionDossiers[0];

            if(is_null($instructionDossier->getRapprochementBanque()) /*|| is_null($instructionDossier->getLogiciel())*/ ||
                is_null($instructionDossier->getGestionDateEcriture())){
                return new JsonResponse(0);
            }
            else{

                if(!is_null($instructionDossier->getRapprochementBanque())){
                    if($instructionDossier->getRapprochementBanque() == 1 && is_null($instructionDossier->getMethodeSuiviCheque())){
                        return new JsonResponse(0);
                    }
                }

            }


        } else {

            return new JsonResponse(0);
        }

        return new JsonResponse(1);
    }

    public function notificationInstructionDossierAction(Request $request)
    {
        $post = $request->request;

        $json = $post->get('typeInstruction');

        $clientId = Boost::deboost($post->get('clientId'), $this);
        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($clientId);

        $instructionType = $this->getDoctrine()
            ->getRepository('AppBundle:InstructionType')
            ->find($json);

        $newInstruction = $post->get('newInstruction');

        /** @var  $utilisateur Utilisateur*/
        $utilisateur = $this->getUser();

        if ($instructionType != null) {

            return $this->render('InfoPerdosBundle:Emails:notificationInstructionDossier.html.twig', array(
                'instructionType' => $instructionType,
                'newInstruction' => $newInstruction,
                'utilisateur' => $utilisateur,
                'client' => $client
            ));

        } else {
            return new Response('-1');
        }

    }

    public function editRemarqueClientAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $post = $request->request;

        $clientId = Boost::deboost($post->get('clientId'), $this);

        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($clientId);

        $remarqueClient = $this->getDoctrine()
            ->getRepository('AppBundle:RemarqueClient')
            ->findOneBy(array('client' => $client));

        $remarque = $post->get('remarque');
        if ($remarque == '') {
            $remarque = null;
        }

        $res = -1;

        if (is_null($remarqueClient)) {
            if(!is_null($remarque)) {
                $remarqueClient = new RemarqueClient();
                $remarqueClient->setClient($client);
                $remarqueClient->setRemarque($remarque);
                $res = 1;
            }

        } else {
            $remarqueClient->setRemarque($remarque);
            $res = 2;
        }

        if(!is_null($remarqueClient)) {

            /** @var  $utilisateur Utilisateur*/
            $utilisateur = $this->getUser();
            if(!is_null($utilisateur->getAccesUtilisateur())) {

                if ($utilisateur->getAccesUtilisateur()->getId() == 7) {

                    $em->persist($remarqueClient);
                    $em->flush();
                }
                else{
                    $res = -2;
                }
            }
            else{
                $res = -2;
            }
        }

        return new JsonResponse($res);
    }
}