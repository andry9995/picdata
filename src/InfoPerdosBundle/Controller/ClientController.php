<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 01/06/2017
 * Time: 16:42
 */

namespace InfoPerdosBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Client;
use AppBundle\Entity\ClientLogiciel;
use AppBundle\Entity\ClientSupport;
use AppBundle\Entity\ResponsableCsd;
use AppBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use TableauImageBundle\Form\ParamSmtpType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class ClientController extends Controller
{

    public function uploadContratAction(Request $request, $filename)
    {

        $folder = "CLIENTS";

        $fs = new Filesystem();

        try {
            $fs->mkdir($folder, 0777);
        } catch (IOExceptionInterface $e) {
        }

        $file = $request->files->get('pdf_contrat');

        $pdf = $filename . "." . $file->guessExtension();

        $file->move($folder, $pdf);

        return new JsonResponse('200');

    }

    public function ShowEditClientAction(Request $request, $clientId)
    {


//        if (!$request->isXmlHttpRequest()) {
//            throw new AccessDeniedHttpException("Accès refusé");
//        }

        if ($clientId != "0") {
            $clientId = Boost::deboost($clientId, $this);
        }


        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($clientId);

        $mandataire = null;
        $secretaire = null;
        $sites = array();

        $receptionImage = null;
        $chefMission = null;
        $manager = null;

        $clientSupport = null;

        $clientLogiciel = null;


        $formeJuridiques = $this->getDoctrine()
            ->getRepository('AppBundle:FormeJuridique')
            ->findBy(array(), array('libelle' => 'asc'));


        $clients = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->findBy(array(), array('nom' => 'asc'));

        $mandataires = $this->getDoctrine()
            ->getRepository('AppBundle:Mandataire')
            ->findBy(array(), array('libelle' => 'asc'));

        $logiciels = $this->getDoctrine()
            ->getRepository('AppBundle:Logiciel')
            ->findBy(array(), array('libelle' => 'asc'));


        if (!is_null($client)) {

            $sites = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->findBy(array('client' => $client));

            $mandataireList = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('typeResponsable' => 0, 'typeCsd' => 0, 'client' => $client));

            if (count($mandataireList) > 0) {
                $mandataire = $mandataireList[0];
            }

            $secretaireList = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('typeResponsable' => 2, 'typeCsd' => 0, 'client' => $client));

            $secretaire = null;
            if (count($secretaireList) > 0) {
                $secretaire = $secretaireList[0];
            }


            $chefMissionList = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('typeResponsable' => 1, 'typeCsd' => 5, 'client' => $client));

            if (count($chefMissionList) > 0) {
                $chefMission = $chefMissionList[0];
            }

            $receptionImageList = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('typeResponsable' => 3, 'typeCsd' => 5, 'client' => $client));


            if (count($receptionImageList) > 0) {
                $receptionImage = $receptionImageList[0];
            }

            $managerList = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('typeResponsable' => 4, 'typeCsd' => 5, 'client' => $client));

            if (count($managerList) > 0) {
                $manager = $managerList[0];
            }


            $clientSupportList = $this->getDoctrine()
                ->getRepository('AppBundle:ClientSupport')
                ->findBy(array('client' => $client));

            /** @var ClientSupport $clientSupport */
            if (count($clientSupportList) > 0) {
                $clientSupport = $clientSupportList[0];
            }


            $clientLogicielList = $this->getDoctrine()
                ->getRepository('AppBundle:ClientLogiciel')
                ->findBy(array('client' => $client));

            if (count($clientLogicielList) > 0) {
                $clientLogiciel = $clientLogicielList[0];
            }

        }

        return $this->render('InfoPerdosBundle:Client:client-edit.html.twig', array(
            'clients' => $clients,
            'formeJuridiques' => $formeJuridiques,
            'client' => $client,
            'mandataires' => $mandataires,
            'mandataire' => $mandataire,
            'secretaire' => $secretaire,
            'sites' => $sites,
            'chefMission' => $chefMission,
            'receptionImage' => $receptionImage,
            'manager' => $manager,

            'clientSupport' => $clientSupport,
            'clientLogiciel' => $clientLogiciel,
            'logiciels' => $logiciels
        ));

    }

    public function indexAction(Request $request, $json){

        $formeJuridiques = $this->getDoctrine()
            ->getRepository('AppBundle:FormeJuridique')
            ->findBy(array(), array('libelle' => 'asc'));


        $clients = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->findBy(array(), array('nom' => 'asc'));

        $mandataires = $this->getDoctrine()
            ->getRepository('AppBundle:Mandataire')
            ->findBy(array(), array('libelle'=> 'asc'));

        $logiciels = $this->getDoctrine()
            ->getRepository('AppBundle:Logiciel')
            ->findBy(array(), array('libelle' => 'asc'));



        //Affichage @ voalohany
        if($json == 0) {

            return $this->render('InfoPerdosBundle:Client:index.html.twig', array(
                'clients' => $clients,
                'formeJuridiques' => $formeJuridiques,
                'client' => null,
                'mandataires' =>$mandataires,
                'mandataire' => null,
                'secretaire' => null,
                'sites' => null,
                'chefMission' => null,
                'receptionImage' => null,
                'manager' => null,
                'clientSupport' =>  null,
                'clientLogiciel' => null,
                'logiciels' => $logiciels
            ));
        }

        //Affichage editClient
        elseif($json == 1){

//            if($request->isXmlHttpRequest()){

                $post = $request->request;

                $clientId = Boost::deboost($post->get('clientId'), $this);

                $client = $this->getDoctrine()
                    ->getRepository('AppBundle:Client')
                    ->find($clientId);

                $mandataire = null;
                $secretaire = null;
                $sites = array();

                $receptionImage = null;
                $chefMission = null;
                $manager = null;

                $clientSupport = null;

                $clientLogiciel = null;

                if(!is_null($client)) {

                    $sites = $this->getDoctrine()
                        ->getRepository('AppBundle:Site')
                        ->findBy(array('client' => $client));

                    $mandataireList = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findBy(array('typeResponsable' => 0, 'typeCsd' => 0 ,'client' => $client));

                    if (count($mandataireList) > 0) {
                        $mandataire = $mandataireList[0];
                    }

                    $secretaireList = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findBy(array('typeResponsable' => 2, 'typeCsd' => 0, 'client' => $client));

                    $secretaire = null;
                    if (count($mandataireList) > 0) {
                        $secretaire = $secretaireList[0];
                    }


                    $chefMissionList = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findBy(array('typeResponsable' => 1 , 'typeCsd' => 5, 'client' => $client));

                    if(count($chefMissionList) > 0){
                        $chefMission = $chefMissionList[0];
                    }

                    $receptionImageList = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findBy(array('typeResponsable' => 3, 'typeCsd' => 5, 'client' => $client));


                    if(count($receptionImageList) > 0){
                        $receptionImage = $receptionImageList[0];
                    }

                    $managerList = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findBy(array('typeResponsable' => 4, 'typeCsd' => 5, 'client' => $client));

                    if(count($managerList) > 0){
                        $manager = $managerList[0];
                    }


                    $clientSupportList = $this->getDoctrine()
                        ->getRepository('AppBundle:ClientSupport')
                        ->findBy(array('client' => $client));

                    /** @var ClientSupport $clientSupport */
                    if(count($clientSupportList) > 0){
                        $clientSupport = $clientSupportList[0];
                    }



                    $clientLogicielList = $this->getDoctrine()
                        ->getRepository('AppBundle:ClientLogiciel')
                        ->findBy(array('client' => $client));

                    if(count($clientLogicielList) > 0){
                        $clientLogiciel = $clientLogicielList[0];
                    }

                }

                return $this->render('InfoPerdosBundle:Client:client-edit.html.twig', array(
                    'clients' => $clients,
                    'formeJuridiques' => $formeJuridiques,
                    'client' => $client,
                    'mandataires' => $mandataires,
                    'mandataire' => $mandataire,
                    'secretaire' => $secretaire,
                    'sites' =>$sites,
                    'chefMission' => $chefMission,
                    'receptionImage' => $receptionImage,
                    'manager' => $manager,

                    'clientSupport' => $clientSupport,
                    'clientLogiciel' => $clientLogiciel,
                    'logiciels' => $logiciels
                ));
//            }
//
//            else{
//                throw new AccessDeniedHttpException("Accès refusé");
//            }

        }

        //Affichage listClient
        else {
            if ($request->isXmlHttpRequest()) {

                return $this->render('InfoPerdosBundle:Client:client-list.html.twig', array(
                    'clients' => $clients
                ));

            } else {
                throw new AccessDeniedHttpException("Accès refusé");
            }
        }
    }

    public function clientEditAction(Request $request){

        if($request->isXmlHttpRequest()){

            $post = $request->request;
            $em = $this->getDoctrine()->getManager();
            $clientId = Boost::deboost($post->get('clientId'), $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($clientId);

            $nom = $post->get('nom');
            $status = $post->get('status');
            $nom = trim($nom, ' ');


            if($nom == ''){
                return new JsonResponse(array('estInsere' => 0, 'message' => 'Nom obligatoire'));
            }

            $nom = strtoupper($nom);
            $nom = preg_replace("/[^[:alnum:]-_]/", '_', $nom);

            $typeClient = $post->get('typeClient');

            if($typeClient != ''){
                $typeClient = intval($typeClient);
            }
            else{
                $typeClient = null;
            }


            $siren = $post->get('siren');

            if($siren == ''){
                $siren = null;
            }

            $formeJuridiqueId = $post->get('formeJuridique');

            $formeJuridique = null;
            if($formeJuridiqueId != ''){
                $formeJuridique = $this->getDoctrine()
                    ->getRepository('AppBundle:FormeJuridique')
                    ->find(intval($formeJuridiqueId));
            }

            $rsSte = $post->get('rsSte');
            if($rsSte == ''){
                $rsSte = null;
            }

            $numRue = $post->get('numRue');
            if($numRue == ''){
                $numRue = null;
            }

            $codePostal = $post->get('codePostal');
            if($codePostal == ''){
                $code = null;
            }

            $ville = $post->get('ville');
            if($ville == ''){
                $ville = null;
            }

            $pays = $post->get('pays');
            if($pays == ''){
                $pays = null;
            }



            $adresse = $post->get('adresse');
            if($adresse == ''){
                $adresse = null;
            }

            $tel = $post->get('tel');
            if($tel == ''){
                $tel = null;
            }

            $siteWeb = $post->get('siteWeb');
            if($siteWeb == ''){
                $siteWeb = null;
            }


            $nbCaractere = $post->get('nbCaractere');
            if($nbCaractere == ''){
                $nbCaractere = 9;
            }
            else{
                if(!ctype_digit($nbCaractere)) {
                    return new JsonResponse(array('estInsere' => 0, 'message' => 'Nombre de caractère invalide'));
                }


            }

            $clients = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->findAll();

            $clientCodes = array();

            foreach ($clients as $cl){
                if($cl != $client) {
                    $clientCodes[] = $cl->getCode();
                }
            }


            $siteCodes = array();

            $sites = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->findAll();

            foreach ($sites as $st){

               if(!in_array($st->getCode(), $siteCodes)){
                   $siteCodes[] = $st->getCode();
               }
            }


            $code = $post->get('code');
            if($code == ''){
                return new JsonResponse(array('estInsere' => 0, 'message' => 'Code obligatoire'));
            }
            else{

                if(in_array($code, $clientCodes)){
                    return new JsonResponse(array('estInsere' => 0, 'message' => 'Code déjà pris (client)'));
                }


                if(in_array($code, $siteCodes)){
                    return new JsonResponse(array('estInsere' => 0, 'message' => 'Code déjà pris (site)'));
                }

                if(strlen($code) > 2){
                    return new JsonResponse(array('estInsere' => 0, 'message' => 'Code doit être inférieur à 2 caractères'));
                }
            }


            $instruction = $post->get('instruction');
            if($instruction == ''){
                $instruction = null;
            }

            $commenataire = $post->get('commentaire');
            if($commenataire == ''){
                $commenataire = null;
            }

            $comment = $post->get('comment');
            if ($comment == '') {
                $comment = null;
            }

            $signature = $post->get('signature');
            if ($signature == '') {
                // return new JsonResponse(array('estInsere' => 0, 'message' => 'Date de signature obligatoire'));
            }

            $contrat = $post->get('contrat');

            if ($contrat == '' || !$contrat || $contrat == null) {
                // return new JsonResponse(array('estInsere' => 0, 'message' => 'Contrat obligatoire'));
                $contrat = '';
            }

            //Mandataire
            $mandataireId = $post->get('mandataire');
            $mandataire = null;
            if ($mandataireId != '') {
                $mandataire = $this->getDoctrine()
                    ->getRepository('AppBundle:Mandataire')
                    ->find($mandataireId);
            }

            $nomPrenomMandataire = $post->get('nomPrenomMandataire');

            $nomMandataire = '';
            $prenomMandataire = '';

            if ($nomPrenomMandataire != '') {
                $nomPrenomMandataire = explode(";", $nomPrenomMandataire);
                $nomMandataire = trim($nomPrenomMandataire[0]);
                if($nomMandataire == ''){
                    $nomMandataire = null;
                }
                $prenomMandataire = trim($nomPrenomMandataire[1]);
                if($prenomMandataire == ''){
                    $prenomMandataire = null;
                }
            }

            $mailMandataire = $post->get('mailMandataire');
            if($mailMandataire == ''){
                $mailMandataire = null;
            }

            $telMandataire = $post->get('telMandataire');
            if($telMandataire == ''){
                $telMandataire = null;
            }

            $skypeMandataire = $post->get('skypeMandataire');
            if($skypeMandataire == ''){
                $skypeMandataire = null;
            }

            $nomPrenomSecretaire = $post->get('nomPrenomSecretaire');
            $nomSecretaire = '';
            $prenomSecretaire = '';

            if($nomPrenomSecretaire != ''){
                $nomPrenomSecretaire = explode(";", $nomPrenomSecretaire);
                $nomSecretaire = trim($nomPrenomSecretaire[0]);
                if($nomSecretaire == ''){
                    $nomSecretaire = null;
                }

                $prenomSecretaire = trim($nomPrenomSecretaire[1]);
                if($prenomSecretaire == ''){
                    $prenomSecretaire = null;
                }
            }

            $mailSecretaire = $post->get('mailSecretaire');
            if($mailSecretaire== ''){
                $mailSecretaire = null;
            }

            $telSecretaire = $post->get('telSecretaire');
            if($telSecretaire == ''){
                $telSecretaire = null;
            }


            $nomPrenomManager = $post->get('nomPrenomManager');
            $nomManager = '';
            $prenomManager = '';

            if($nomPrenomManager != ''){
                $nomPrenomManager = explode(";", $nomPrenomManager);
                $nomManager = trim($nomPrenomManager[0]);
                if($nomManager == ''){
                    $nomManager = null;
                }

                if(count($nomPrenomManager)>= 2) {
                    $prenomManager = trim($nomPrenomManager[1]);
                    if ($prenomManager == '') {
                        $prenomManager = null;
                    }
                }
            }

            $mailManager = $post->get('mailManager');
            if($mailManager == ''){
                $mailManager = null;
            }



            $nomPrenomChefMission = $post->get('nomPrenomChefMission');
            $nomChefMission = '';
            $prenomChefMission = '';

            if($nomPrenomChefMission != ''){
                $nomPrenomChefMission = explode(";", $nomPrenomChefMission);
                $nomChefMission = trim($nomPrenomChefMission[0]);
                if($nomChefMission == ''){
                    $nomChefMission = null;
                }

                if(count($nomPrenomChefMission) >= 2){
                    $prenomChefMission = trim($nomPrenomChefMission[1]);
                    if($prenomChefMission == ''){
                        $prenomChefMission = null;
                    }
                }

            }

            $mailChefMission = $post->get('mailChefMission');
            if($mailChefMission == ''){
                $mailChefMission = null;
            }

            $nomPrenomReceptionImage = $post->get('nomPrenomReceptionImage');
            $nomReceptionImage = '';
            $prenomReceptionImage = '';

            if($nomPrenomReceptionImage != ''){
                $nomPrenomReceptionImage = explode(";", $nomPrenomReceptionImage);
                $nomReceptionImage = trim($nomPrenomReceptionImage[0]);
                if($nomReceptionImage == ''){
                    $nomReceptionImage = null;
                }

                if(count($nomPrenomReceptionImage)>= 2) {
                    $prenomReceptionImage = trim($nomPrenomReceptionImage[1]);
                    if ($prenomReceptionImage == '') {
                        $prenomReceptionImage = null;
                    }
                }
            }

            $mailReceptionImage = $post->get('mailReceptionImage');
            if($mailReceptionImage == ''){
                $mailReceptionImage = null;
            }


            $nomPrenomSupport = $post->get('nomPrenomSupport');
            $nomSupport = '';
            $prenomSupport = '';

            if($nomPrenomSupport != ''){
                $nomPrenomSupport = explode(";", $nomPrenomSupport);
                $nomSupport = trim($nomPrenomSupport[0]);
                if($nomSupport == ''){
                    $nomSupport = null;
                }

                if(count($nomPrenomSupport)>= 2) {
                    $prenomSupport = trim($nomPrenomSupport[1]);
                    if ($prenomSupport == '') {
                        $prenomSupport = null;
                    }
                }
            }

            $mailSupport = $post->get('mailSupport');
            if($mailSupport == ''){
                $mailSupport = null;
            }

            $telSupport = $post->get('telSupport');
            if($telSupport == ''){
                $telSupport = null;
            }

            $societeSupport = $post->get('societeSupport');
            if($societeSupport == ''){
                $societeSupport = null;
            }

            $logicielId = $post->get('logicielId');
            $logiciel = null;
            if($logicielId != ""){
                $logiciel = $this->getDoctrine()
                    ->getRepository('AppBundle:Logiciel')
                    ->find($logicielId);
            }

            $modeTravail = $post->get('modeTravail');
            if($modeTravail == ""){
                $modeTravail = null;
            }

            $ip = $post->get('ip');
            if($ip == ""){
                $ip = null;
            }

            $implantation = $post->get('implantation');
            if($implantation == ""){
                $implantation = null;
            }

            $login = $post->get('login');
            if($login == ""){
                $login = null;
            }

            $password = $post->get('password');
            if($password == ""){
                $password = null;
            }



            //Insertion raha mbola tsy misy
            if($client == null){

                $client = new Client();

                $client->setNom($nom);
                $client->setTypeClient($typeClient);
                $client->setSiren($siren);
                $client->setFormeJuridique($formeJuridique);
                $client->setAdresseSiege($adresse);
                $client->setSiteWeb($siteWeb);
                $client->setTelFixe($tel);
                $client->setNbCaractere($nbCaractere);
                $client->setCode($code);
                $client->setInstruction($instruction);
                $client->setCommentaire($commenataire);
                $client->setRsSte($rsSte);

                $client->setNumRue($numRue);
                $client->setCodePostal($codePostal);
                $client->setVille($ville);
                $client->setPays($pays);

                $client->setComment($comment);

                if ($signature != '') {
                    $dateSignature = \DateTime::createFromFormat('d/m/Y', $signature);
                    $client->setSignature($dateSignature);
                }


                $client->setContrat($contrat);

                // $client->setDernierNum(0);
                // $client->setDernierNumLocal(0);
                // $client->setImageFtpSeparator('.');
                // $client->setSendNotificationImage(0);
                $client->setStatus(intval($status));

                $em->persist($client);
                $em->flush();


                if($nomMandataire != '' || $prenomMandataire != '') {
                    $mandataireClient = new ResponsableCsd();
                    $mandataireClient->setNom($nomMandataire);
                    $mandataireClient->setPrenom($prenomMandataire);
                    $mandataireClient->setTypeResponsable(0);
                    $mandataireClient->setTypeCsd(0);
                    $mandataireClient->setClient($client);
                    $mandataireClient->setMandataire($mandataire);
                    $mandataireClient->setEmail($mailMandataire);
                    $mandataireClient->setTelPortable($telMandataire);
                    $mandataireClient->setSkype($skypeMandataire);

                    $em->persist($mandataireClient);
                    $em->flush();
                }


                if($nomSecretaire != '' || $prenomSecretaire != '') {
                    $secretaire = new ResponsableCsd();
                    $secretaire->setNom($nomSecretaire);
                    $secretaire->setPrenom($prenomSecretaire);
                    $secretaire->setTypeResponsable(2);
                    $secretaire->setTypeCsd(0);
                    $secretaire->setClient($client);
                    $secretaire->setEmail($mailSecretaire);
                    $secretaire->setTelPortable($telSecretaire);

                    $em->persist($secretaire);
                    $em->flush();
                }


                $manager= new ResponsableCsd();
                $manager->setNom($nomManager);
                $manager->setPrenom($prenomManager);
                $manager->setTypeResponsable(4);
                $manager->setTypeCsd(5);
                $manager->setClient($client);
                $manager->setEmail($mailManager);
                if($mailManager != '') {
                    $manager->setEnvoiMail(1);
                }

                $em->persist($manager);
                $em->flush();


                $chefMission= new ResponsableCsd();
                $chefMission->setNom($nomChefMission);
                $chefMission->setPrenom($prenomChefMission);
                $chefMission->setTypeResponsable(1);
                $chefMission->setTypeCsd(5);
                $chefMission->setClient($client);
                $chefMission->setEmail($mailChefMission);
                if($mailChefMission != '') {
                    $chefMission->setEnvoiMail(1);
                }

                $em->persist($chefMission);
                $em->flush();


                $receptionImage= new ResponsableCsd();
                $receptionImage->setNom($nomReceptionImage);
                $receptionImage->setPrenom($prenomReceptionImage);
                $receptionImage->setTypeResponsable(3);
                $receptionImage->setTypeCsd(5);
                $receptionImage->setClient($client);
                $receptionImage->setEmail($mailReceptionImage);
                if($mailReceptionImage != '') {
                    $receptionImage->setEnvoiMail(1);
                }

                $em->persist($receptionImage);
                $em->flush();


                $clientSupport = new ClientSupport();
                $clientSupport->setSociete($societeSupport);
                $clientSupport->setNom($nomSupport);
                $clientSupport->setPrenom($prenomSupport);
                $clientSupport->setClient($client);
                $clientSupport->setEmail($mailSupport);
                $clientSupport->setTelephone($telSupport);

                $em->persist($clientSupport);
                $em->flush();


                $clientLogiciel = new ClientLogiciel();
                $clientLogiciel->setClient($client);
                $clientLogiciel->setLogiciel($logiciel);
                $clientLogiciel->setModeTravail($modeTravail);
                $clientLogiciel->setIp($ip);
                $clientLogiciel->setImplantation($implantation);
                $clientLogiciel->setLogin($login);
                $clientLogiciel->setPassword($password);

                $em->persist($clientLogiciel);
                $em->flush();

                $res = 1;
            }
            //Mise à jour raha efa misy
            else{

                $client->setNom($nom);
                $client->setTypeClient($typeClient);
                $client->setSiren($siren);
                $client->setFormeJuridique($formeJuridique);
                $client->setAdresseSiege($adresse);
                $client->setSiteWeb($siteWeb);
                $client->setTelFixe($tel);
                $client->setNbCaractere($nbCaractere);
                $client->setCode($code);
                $client->setInstruction($instruction);
                $client->setCommentaire($commenataire);
                $client->setRsSte($rsSte);

                $client->setNumRue($numRue);
                $client->setCodePostal($codePostal);
                $client->setVille($ville);
                $client->setPays($pays);

                $client->setComment($comment);


                if ($signature != '') {
                    $dateSignature = \DateTime::createFromFormat('d/m/Y', $signature);
                    $client->setSignature($dateSignature);
                }

                
                $client->setContrat($contrat);

                $client->setStatus(intval($status));


                $em->persist($client);
                $em->flush();

                $mandataires = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('typeResponsable' => 0, 'client' => $client));

                //Mise à jour
                if(count($mandataires) > 0){
                    $mandataireClient = $mandataires[0];
                    $mandataireClient->setNom($nomMandataire);
                    $mandataireClient->setPrenom($prenomMandataire);
                    $mandataireClient->setMandataire($mandataire);
                    $mandataireClient->setEmail($mailMandataire);
                    $mandataireClient->setTelPortable($telMandataire);
                    $mandataireClient->setSkype($skypeMandataire);

                    $em->flush();
                }
                //Insertion
                else{
                    if($nomMandataire != '' || $prenomMandataire != '') {
                        $mandataireClient = new ResponsableCsd();
                        $mandataireClient->setClient($client);
                        $mandataireClient->setTypeResponsable(0);
                        $mandataireClient->setTypeCsd(0);
                        $mandataireClient->setNom($nomMandataire);
                        $mandataireClient->setPrenom($prenomMandataire);
                        $mandataireClient->setMandataire($mandataire);
                        $mandataireClient->setEmail($mailMandataire);
                        $mandataireClient->setTelPortable($telMandataire);
                        $mandataireClient->setSkype($skypeMandataire);

                        $em->persist($mandataireClient);
                        $em->flush();
                    }
                }




                $secretaires = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('typeResponsable' => 2, 'client' => $client));

                //Mise à jour
                if(count($secretaires) > 0){
                    $secretaire = $secretaires[0];
                    $secretaire->setNom($nomSecretaire);
                    $secretaire->setPrenom($prenomSecretaire);
                    $secretaire->setEmail($mailSecretaire);
                    $secretaire->setTelPortable($telSecretaire);


                    $em->flush();
                }
                //Insertion
                else{
                    if($nomSecretaire != '' || $prenomSecretaire != '') {
                        $secretaire = new ResponsableCsd();
                        $secretaire->setTypeResponsable(2);
                        $secretaire->setClient($client);
                        $secretaire->setNom($nomSecretaire);
                        $secretaire->setPrenom($prenomSecretaire);
                        $secretaire->setEmail($mailSecretaire);
                        $secretaire->setTelPortable($telSecretaire);

                        $em->persist($secretaire);
                        $em->flush();
                    }
                }




                //Manager
                $managers = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('typeResponsable' => 4,'typeCsd' => 5 , 'client' => $client));

                //Mise à jour
                if(count($managers) > 0){
                    $manager = $managers[0];
                }
                //Insertion
                else{
                    $manager= new ResponsableCsd();
                    $manager->setTypeResponsable(4);
                    $manager->setTypeCsd(5);
                    $manager->setClient($client);
                }


                $manager->setNom($nomManager);
                $manager->setPrenom($prenomManager);
                $manager->setEmail($mailManager);

                $em->persist($manager);
                $em->flush();


                //Chef de mission
                $chefMissions = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('typeResponsable' => 1,'typeCsd' => 5 , 'client' => $client));

                //Mise à jour
                if(count($chefMissions) > 0){
                    $chefMission = $chefMissions[0];
                }
                //Insertion
                else{
                    $chefMission= new ResponsableCsd();
                    $chefMission->setTypeResponsable(1);
                    $chefMission->setTypeCsd(5);
                    $chefMission->setClient($client);
                }

                $chefMission->setNom($nomChefMission);
                $chefMission->setPrenom($prenomChefMission);
                $chefMission->setEmail($mailChefMission);

                $em->persist($chefMission);
                $em->flush();

                //Reception images
                $receptionImages = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('typeResponsable' => 3,'typeCsd' => 5,'client' => $client));

                //Mise à jour
                if(count($receptionImages) > 0){
                    $receptionImage = $receptionImages[0];
                }
                //Insertion
                else{
                    $receptionImage= new ResponsableCsd();
                    $receptionImage->setTypeResponsable(3);
                    $receptionImage->setTypeCsd(5);
                    $receptionImage->setClient($client);
                }

                $receptionImage->setNom($nomReceptionImage);
                $receptionImage->setPrenom($prenomReceptionImage);
                $receptionImage->setEmail($mailReceptionImage);

                $em->persist($receptionImage);
                $em->flush();


                $clientSupports = $this->getDoctrine()
                    ->getRepository('AppBundle:ClientSupport')
                    ->findBy(array('client' =>  $client));

                //Mise à jour
                if(count($clientSupports) > 0){
                    $clientSupport = $clientSupports[0];
                }
                else{
                  $clientSupport = new ClientSupport();
                  $clientSupport->setClient($client);
                }

                $clientSupport->setSociete($societeSupport);
                $clientSupport->setNom($nomSupport);
                $clientSupport->setPrenom($prenomSupport);
                $clientSupport->setEmail($mailSupport);
                $clientSupport->setTelephone($telSupport);

                $em->persist($clientSupport);
                $em->flush();

                $clientLogiciels = $this->getDoctrine()
                    ->getRepository('AppBundle:ClientLogiciel')
                    ->findBy(array('client' => $client));

                //Mise à jour
                if(count($clientLogiciels) > 0){
                    $clientLogiciel = $clientLogiciels[0];
                }
                else{
                    $clientLogiciel = new ClientLogiciel();
                    $clientLogiciel->setClient($client);
                }

                $clientLogiciel->setLogiciel($logiciel);
                $clientLogiciel->setModeTravail($modeTravail);
                $clientLogiciel->setIp($ip);
                $clientLogiciel->setImplantation($implantation);
                $clientLogiciel->setLogin($login);
                $clientLogiciel->setPassword($password);

                $em->persist($clientLogiciel);
                $em->flush();

                $res = 2;
            }

            return new JsonResponse($res);


        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }

    }

    public function listeSiteAction(Request $request)
    {
        if($request->isXmlHttpRequest()){
            $post = $request->request;

            $clientId = Boost::deboost($post->get('clientId'), $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($clientId);


            $sites  = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->findBy(array('client'=>$client), array('nom' => 'asc'));

            $res = '';

            /** @var  $site Site*/
            foreach ($sites as $site){
               if($res == ''){
                   $res = $site->getNom();
               }
               else{
                   $res = $res.'; '.$site->getNom();
               }
           }

           return new JsonResponse($res);


        }
        else{
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    public function smtpAction()
    {
        $smtp_form = $this->createForm(ParamSmtpType::class);
        return $this->render('@InfoPerdos/Client/smtp.html.twig', array(
            'smtp_form' => $smtp_form->createView(),
        ));
    }
}