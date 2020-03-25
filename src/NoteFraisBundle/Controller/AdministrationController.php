<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 04/01/2018
 * Time: 15:25
 */

namespace NoteFraisBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\NdfAffaire;
use AppBundle\Entity\NdfCategorieDossier;
use AppBundle\Entity\NdfContact;
use AppBundle\Entity\NdfFraisKilometrique;
use AppBundle\Entity\NdfSouscategorie;
use AppBundle\Entity\NdfSouscategorieCharge;
use AppBundle\Entity\NdfSouscategorieDossier;
use AppBundle\Entity\NdfSouscategorieTva;
use AppBundle\Entity\NdfUtilisateur;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Vehicule;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdministrationController extends Controller
{

    /**
     * Mi-afficher ny combo exercice
     */
    public function exerciceAction()
    {
        $exercices = Boost::getExercices(2,0);

        return $this->render('NoteFraisBundle:Default:exerciceValue.html.twig', array('exercices' => $exercices));
    }

    /********************/
    public function indexCategorieV2Action(Request $request,$json){

        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $ndfCategories = $this->getDoctrine()
                ->getRepository('AppBundle:NdfCategorie')
                ->getNdfCategorieByDossier($dossier);

            $ndfSousCategoriesDossier = array();

            $ndfSouscategoriePcg = array();
            if (null !== $dossier) {
                $ndfSousCategoriesDossier = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfSouscategorieDossier')
                    ->findBy(array('dossier' => $dossier), array('libelle' => 'ASC'));

                foreach ($ndfSousCategoriesDossier as $scat) {

                    if (null === $scat) {
                        continue;
                    }

                    $ndfScatPcgCharges = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfSouscategorieCharge')
                        ->findBy(array('ndfSouscategorie' => $scat->getNdfSouscategorie()));
                    $pcgCharge = '';
                    foreach ($ndfScatPcgCharges as $ndfScatPcgCharge) {
                        $temp = $ndfScatPcgCharge->getCompte();

                        $tempLength = strlen($temp);

                        if ($tempLength < 6) {
                            for ($i = $tempLength; $i < 6; $i++) {
                                $temp = $temp . 'X';
                            }
                        }
                        if (str_replace(' ', '', $pcgCharge) === '') {
                            $pcgCharge = $temp;
                        } else {
                            $pcgCharge .= ', ' . $temp;
                        }
                    }

                    $ndfScatPcgTvas = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfSouscategorieTva')
                        ->findBy(array('ndfSouscategorie' => $scat->getNdfSouscategorie()));

                    $pcgTva = '';
                    foreach ($ndfScatPcgTvas as $ndfScatPcgCharge) {
                        $temp = $ndfScatPcgCharge->getCompte();

                        $tempLength = strlen($temp);

                        if ($tempLength < 6) {
                            for ($i = $tempLength; $i < 6; $i++) {
                                $temp = $temp . 'X';
                            }
                        }
                        if ($pcgTva === '') {
                            $pcgTva = $temp;
                        } else {
                            $pcgTva .= ', ' . $temp;
                        }
                    }

                    $ndfSouscategoriePcg[] = array(
                        'ndfSouscategorie' => $scat,
                        'pcgCharge' => $pcgCharge,
                        'pcgTva' => $pcgTva
                    );
                }
            }

            if ($json == 0) {

                return $this->render('NoteFraisBundle:Administration/Categorie:index.html.twig', array(
                    'ndfCategories' => $ndfCategories,
                    'ndfSousCategoriesDossier' => $ndfSousCategoriesDossier,
                    'ndfSouscategoriesDossierPcg' => $ndfSouscategoriePcg
                ));
            }

            return $this->render('NoteFraisBundle:Administration/Categorie:souscategorieTable.html.twig', array(
                'ndfCategories' => $ndfCategories,
                'ndfSousCategoriesDossier' => $ndfSousCategoriesDossier,
                'ndfSouscategoriesDossierPcg' => $ndfSouscategoriePcg
            ));
        }
        throw new AccessDeniedHttpException('Accès refusé');

    }

    public function sousCategorieStatusAction(Request $request){

        $post = $request->request;

        $sCategorieDossierId = Boost::deboost($post->get('id'), $this);
        $active = $post->get('status');

        $sCategorieDossier = $this->getDoctrine()
            ->getRepository('AppBundle:NdfSouscategorieDossier')
            ->find($sCategorieDossierId);

        $em = $this->getDoctrine()
            ->getManager();

        if(is_numeric($active)){
            $sCategorieDossier->setStatus($active);

            $em->flush();
        }

        return new JsonResponse('status modifié');
    }


    public function sousCategorieEditAction(Request $request, $json){

        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);
            $ndfCategorieId = Boost::deboost($post->get('sousCategorieId'), $this);

            $sCatDoss = $this->getDoctrine()
                ->getRepository('AppBundle:NdfSouscategorieDossier')
                ->find($ndfCategorieId);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            //Affichage modal
            if ($json == 0) {

                $ndfcharges = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfSouscategorieCharge')
                    ->findBy(array('ndfSouscategorie' => $sCatDoss->getNdfSouscategorie()));

                $likeCharges = array();

                foreach ($ndfcharges as $ndfcharge) {
                    if (!in_array($ndfcharge->getCompte(), $likeCharges)) {
                        $likeCharges[] = $ndfcharge->getCompte();
                    }
                }

                if (count($likeCharges) > 0) {
                    $pccCharges = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->getPccByDossierLike($dossier, $likeCharges);
                } else {
                    $pccCharges = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->getPccByDossierLike($dossier, array('6'));
                }

                $ndfTvas = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfSouscategorieTva')
                    ->findBy(array('ndfSouscategorie' => $sCatDoss->getNdfSouscategorie()));

                $likeTvas = array();

                foreach ($ndfTvas as $ndfTva) {
                    if (!in_array($ndfTva->getCompte(), $likeTvas)) {
                        $likeTvas[] = $ndfTva->getCompte();
                    }
                }

                if (count($likeTvas) == 0) {
                    $pccTvas = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->getPccByDossierLike($dossier, array('445'));
                } else {
                    $pccTvas = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->getPccByDossierLike($dossier, $likeTvas);
                }

                $tvaTauxs = $this->getDoctrine()
                    ->getRepository('AppBundle:TvaTaux')
                    ->findBy(array('actif' => 1), array('taux' => 'ASC'));

                return $this->render('NoteFraisBundle:Administration/Categorie:sousCategorieEdit.html.twig', array(
                    'sCatDoss' => $sCatDoss,
                    'tvaTauxs' => $tvaTauxs,
                    'pccCharges' => $pccCharges,
                    'pccTvas' => $pccTvas,
                    'likeCharges' => $likeCharges,
                    'likeTvas' => $likeTvas
                ));
            }

            //Sauvegarde
            $em = $this->getDoctrine()
                ->getEntityManager();

            $libelle = $post->get('libelle');

            $pccCharge = $this->getDoctrine()
                ->getRepository('AppBundle:Pcc')
                ->find(Boost::deboost($post->get('pccCharge'), $this));

            $pccTva = $this->getDoctrine()
                ->getRepository('AppBundle:Pcc')
                ->find(Boost::deboost($post->get('pccTva'), $this));

            $tvaTaux = $this->getDoctrine()
                ->getRepository('AppBundle:TvaTaux')
                ->find($post->get('tvaTaux'));


            $tvaRec = $post->get('tvaRec');

            $tvaRec2 = $post->get('tvaRec2');

            $status = ($post->get('status') === 'true') ? 1 : 0;


            //Mise à jour
            if (null !== $sCatDoss) {

                $sCatDoss->setLibelle($libelle);

                $sCatDoss->setPccCharge($pccCharge);
                $sCatDoss->setPccTva($pccTva);

                $sCatDoss->setTvaTaux($tvaTaux);

                if (is_numeric($tvaRec)) {
                    $sCatDoss->setTvaRec($tvaRec);
                } else {
                    $sCatDoss->setTvaRec(null);
                }

                if (is_numeric($tvaRec2)) {
                    $sCatDoss->setTvaRec2($tvaRec2);
                } else {
                    $sCatDoss->setTvaRec2(null);
                }

                $sCatDoss->setStatus($status);

                $em->flush();

                return new Response(2);

            }


            //insertion
            $sCatDoss = new NdfSouscategorieDossier();
            $sCatDoss->setPccCharge($pccCharge);
            $sCatDoss->setPccTva($pccTva);

            $sCatDoss->setTvaTaux($tvaTaux);

            if (is_numeric($tvaRec)) {
                $sCatDoss->setTvaRec($tvaRec);
            } else {
                $sCatDoss->setTvaRec(null);
            }

            if (is_numeric($tvaRec2)) {
                $sCatDoss->setTvaRec2($tvaRec2);
            } else {
                $sCatDoss->setTvaRec2(null);
            }

            $sCatDoss->setStatus($status);

            $em->flush();

            return new Response(2);
        }

        throw new AccessDeniedHttpException('Accès refusé');

    }

    /********************/
    public function indexVehiculeAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $post = $request->request;
            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $vehicules = array();

            if (null !== $dossier) {
                $vehicules = $this->getDoctrine()
                    ->getRepository('AppBundle:Vehicule')
                    ->findBy(array('dossier' => $dossier));
            }

            return $this->render('NoteFraisBundle:Administration/Vehicule:index.html.twig', array(
                'vehicules' => $vehicules
            ));
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    public function vehiculeEditAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest()) {
            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);
            $vehiculeId = Boost::deboost($post->get('vehiculeId'), $this);

            $vehicule = $this->getDoctrine()
                ->getRepository('AppBundle:Vehicule')
                ->find($vehiculeId);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            //Affichage modal
            if ($json == 0) {

                $marques = $this->getDoctrine()
                    ->getRepository('AppBundle:VehiculeMarque')
                    ->findBy(array(), array('libelle' => 'ASC'));


                $typeVehicules = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfTypeVehicule')
                    ->findBy(array(), array('libelle' => 'ASC'));

                $typeRemboursements = $this->getDoctrine()
                    ->getRepository('AppBundle:TypeVehicule')
                    ->findBy(array(), array('libelle' => 'ASC'));

                $carburants = $this->getDoctrine()
                    ->getRepository('AppBundle:Carburant')
                    ->findBy(array(), array('libelle' => 'ASC'));

                return $this->render('NoteFraisBundle:Administration/Vehicule:vehiculeEdit.html.twig', array(
                    'vehicule' => $vehicule,
                    'marques' => $marques,
                    'typeRemboursements' => $typeRemboursements,
                    'typeVehicules' => $typeVehicules,
                    'carburants' => $carburants
                ));
            }

            //Sauvegarde
            $em = $this->getDoctrine()
                ->getEntityManager();

            $isUpdate = false;

            $retMarque = '';

            $vehiculeMarqueId = $post->get('marque');
            if ($vehiculeMarqueId == '') {
                $vehiculeMarque = null;
            } else {
                $vehiculeMarque = $this->getDoctrine()
                    ->getRepository('AppBundle:VehiculeMarque')
                    ->find($vehiculeMarqueId);
            }
            if (null !== $vehiculeMarque) {
                $retMarque = $vehiculeMarque->getLibelle();
            }

            $modele = $post->get('modele');
            if ($modele == '') {
                $modele = null;
            }

            $immatricule = $post->get('immatricule');
            if ($immatricule == '') {
                $immatricule = null;
            }

            $retTypeVehicule = '';
            $typeVehiculeId = $post->get('typeVehicule');
            $typeVehicule = null;
            if ($typeVehiculeId != '') {
                $typeVehicule = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfTypeVehicule')
                    ->find($typeVehiculeId);
            }

            if (null !== $typeVehicule) {
                $retTypeVehicule = $typeVehicule->getLibelle();
            }

            $typeRemboursementId = $post->get('typeRemboursement');
            $typeRembourssement = null;
            if ($typeRemboursementId != '') {
                $typeRembourssement = $this->getDoctrine()
                    ->getRepository('AppBundle:TypeVehicule')
                    ->find($typeRemboursementId);
            }

            $puissanceFiscal = $post->get('puissanceFiscal');
            if ($puissanceFiscal == '') {
                $puissanceFiscal = null;
            }

            $retCarburant = '';
            $carburantId = $post->get('carburant');
            $carburant = null;
            if ($carburantId != '') {
                $carburant = $this->getDoctrine()
                    ->getRepository('AppBundle:Carburant')
                    ->find($carburantId);
            }

            if (null !== $carburant) {
                $retCarburant = $carburant->getLibelle();
            }


            //Mise à jour
            if (null !== $vehicule) {

                $id = Boost::boost($vehicule->getId());

                $vehicule->setVehiculeMarque($vehiculeMarque);
                $vehicule->setModele($modele);
                $vehicule->setImmatricule($immatricule);
                $vehicule->setNdfTypeVehicule($typeVehicule);
                $vehicule->setNbCv($puissanceFiscal);

                $vehicule->setTypeVehicule($typeRembourssement);

                $vehicule->setCarburant($carburant);

                $em->persist($vehicule);
                $em->flush();

                $isUpdate = true;
            } //insertion
            else {

                $vehicule = new Vehicule();

                $vehicule->setDossier($dossier);
                $vehicule->setVehiculeMarque($vehiculeMarque);
                $vehicule->setModele($modele);
                $vehicule->setImmatricule($immatricule);
                $vehicule->setNdfTypeVehicule($typeVehicule);
                $vehicule->setNbCv($puissanceFiscal);

                $vehicule->setTypeVehicule($typeRembourssement);

                $vehicule->setCarburant($carburant);

                $vehicule->setVehiculeProprietaire($this->getDoctrine()
                    ->getRepository('AppBundle:VehiculeProprietaire')
                    ->find(3));

                $em->persist($vehicule);
                $em->flush();

                $id = Boost::boost($vehicule->getId());
            }

            return new JsonResponse(array(
                'id' => $id,
                'marque' => $retMarque,
                'modele' => $modele,
                'immatricule' => $immatricule,
                'typeVehicule' => $retTypeVehicule,
                'carburant' => $retCarburant,
                'isUpdate' => $isUpdate,
                'puissance' => $puissanceFiscal,
            ));
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }

    function vehiculeTableauAction(Request $request){

        if($request->isXmlHttpRequest()) {

            $post = $request->request;
            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $vehicules = $this->getDoctrine()
                ->getRepository('AppBundle:Vehicule')
                ->findBy(array('dossier' => $dossier));

            return $this->render('NoteFraisBundle:Administration/Vehicule:vehiculeTable.html.twig', array(
                'vehicules' => $vehicules));
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }


    /********************/
    public function indexFraisKilometriqueAction(Request $request)
    {
        $typeVehicules = $this->getDoctrine()
            ->getRepository('AppBundle:NdfTypeVehicule')
            ->findAll();

        return $this->render('NoteFraisBundle:Administration/FraisKilometrique:index.html.twig', array('typeVehicules' => $typeVehicules));
    }

    public function fraisKilometriqueTableAction(Request $request){
        $post = $request->request;
        $annee =$post->get('annee');
        $typeVehiculeId = $post->get('typeVehicule');

        $typeVehicule = null;
        if($typeVehiculeId != ''){
            $typeVehicule = $this->getDoctrine()
                ->getRepository('AppBundle:NdfTypeVehicule')
                ->find($typeVehiculeId);
        }

        $fraisKms = array();
        /** @var NdfFraisKilometrique fraisKms */
        if($annee != '' && $typeVehicule != null){
            $fraisKms = $this->getDoctrine()
                ->getRepository('AppBundle:NdfFraisKilometrique')
                ->getFraisKmByTypeVehiculeAnnee($annee, $typeVehicule);
        }

        return $this->render('NoteFraisBundle:Administration/FraisKilometrique:fraisKilometriqueTable.html.twig', array(
            'fraisKms' => $fraisKms
        ));
    }


    /********************/
    public function contactEditAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest()) {
            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);
            $contactId = Boost::deboost($post->get('contactId'), $this);

            $contact = $this->getDoctrine()
                ->getRepository('AppBundle:NdfContact')
                ->find($contactId);

            $contactTypes = $this->getDoctrine()
                ->getRepository('AppBundle:NdfContactType')
                ->findBy(array(), array('libelle' => 'ASC'));

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            //Affichage modal
            if ($json == 0) {


                return $this->render('NoteFraisBundle:Administration/Contact:contactEdit.html.twig', array(
                    'contact' => $contact,
                    'contactTypes' => $contactTypes
                ));
            }

            //Sauvegarde
            $em = $this->getDoctrine()
                ->getEntityManager();


            $nom = $post->get('nom');
            if ($nom == '') {
                $nom = null;
            }

            $prenom = $post->get('prenom');
            if ($prenom == '') {
                $prenom = null;
            }

            $mail = $post->get('mail');
            if ($mail == '') {
                $mail = null;
            }

            $telephone = $post->get('telephone');
            if ($telephone == '') {
                $telephone = null;
            }

            $fonction = $post->get('fonction');
            if ($fonction == '') {
                $fonction = null;
            }

            $contactTypeId = $post->get('contactType');

            $contactType = $this->getDoctrine()
                ->getRepository('AppBundle:NdfContactType')
                ->find($contactTypeId);


            //Mise à jour
            if (null !== $contact) {
                $contact->setNom($nom);
                $contact->setPrenom($prenom);
                $contact->setFonction($fonction);
                $contact->setMail($mail);
                $contact->setFonction($fonction);
                $contact->setNdfContactType($contactType);

                $em->flush();

                return new JsonResponse(2);
            }

            //insertion
            $contact = new NdfContact();

            $contact->setDossier($dossier);
            $contact->setNom($nom);
            $contact->setPrenom($prenom);
            $contact->setFonction($fonction);
            $contact->setMail($mail);
            $contact->setFonction($fonction);
            $contact->setNdfContactType($contactType);

            $em->persist($contact);
            $em->flush();

            return new JsonResponse(1);
        }

        throw new AccessDeniedHttpException('Accès refusé');

    }

    /** Mi-initialiser ny ndf_categorie_dossier raha mbola tsy misy */
    /** @var Dossier  $dossier*/
    public function initialiseCategorieDossier($dossier){

        $ndfCategorieDossiers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:NdfCategorieDossier')
            ->findBy(array('dossier' => $dossier));


        if(count($ndfCategorieDossiers) == 0){
            $ndfCategories = $this->getDoctrine()
                ->getRepository('AppBundle:NdfCategorie')
                ->findAll();

            $em = $this->getDoctrine()
                ->getEntityManager();

            foreach ($ndfCategories as $ndfCategorie){
                $ndfCategorieDossier = new NdfCategorieDossier();

                $ndfCategorieDossier->setDossier($dossier);
                $ndfCategorieDossier->setLibelle($ndfCategorie->getLibelle());

                $em->persist($ndfCategorieDossier);
                $em->flush();
            }

        }
    }


    public function initialiseSousCategorieDossier($dossier){
        $ndfSousCategoriesDossier = $this
            ->getDoctrine()
            ->getRepository('AppBundle:NdfSouscategorieDossier')
            ->findBy(array('dossier' => $dossier));


        if(count($ndfSousCategoriesDossier) == 0){
            $ndfSousCategories = $this->getDoctrine()
                ->getRepository('AppBundle:NdfSousCategorie')
                ->findAll();

            $em = $this->getDoctrine()
                ->getEntityManager();

            foreach ($ndfSousCategories as $ndfSousCategorie){
                $ndfCategorieDossier = new NdfSouscategorieDossier();

                $ndfCategorieDossier->setDossier($dossier);
                $ndfCategorieDossier->setTvaRec($ndfSousCategorie->getTvaRec());
                $ndfCategorieDossier->setTvaTaux($ndfSousCategorie->getTvaTaux());


                $em->persist($ndfCategorieDossier);
                $em->flush();
            }

        }
    }

    function contactTableauAction(Request $request){

        if($request->isXmlHttpRequest()) {

            $post = $request->request;
            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $contacts = $this->getDoctrine()
                ->getRepository('AppBundle:NdfContact')
                ->findBy(array('dossier' => $dossier));

            return $this->render('NoteFraisBundle:Administration/Contact:contactTable.html.twig', array(
                'contacts' => $contacts));
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    /********************/


    public function affaireEditAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest()) {
            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);
            $affaireId = Boost::deboost($post->get('affaireId'), $this);

            $affaire = $this->getDoctrine()
                ->getRepository('AppBundle:NdfAffaire')
                ->find($affaireId);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            //Affichage modal
            if ($json == 0) {

                return $this->render('NoteFraisBundle:Administration/Affaire:affaireEdit.html.twig', array(
                    'affaire' => $affaire
                ));
            }

            //Sauvegarde
            $em = $this->getDoctrine()
                ->getEntityManager();


            $libelle = $post->get('libelle');
            if ($libelle == '') {
                $libelle = null;
            }

            $reference = $post->get('reference');
            if ($reference == '') {
                $reference = null;
            }

            $nomClient = $post->get('nomClient');
            if ($nomClient == '') {
                $nomClient = null;
            }


            $periodeDu = $post->get('dateDeb');
            $newPeriodeDu = '';

            if ($periodeDu != '') {

                $date_array = explode("/", $periodeDu);
                $var_day = $date_array[0];
                $var_month = $date_array[1];
                $var_year = $date_array[2];
                $newPeriodeDu = "$var_year-$var_month-$var_day";
            }

            if ($newPeriodeDu != '') {
                $newPeriodeDu = (new \DateTime($newPeriodeDu));
            } else {
                $newPeriodeDu = null;
            }

            $periodeAu = $post->get('dateFin');
            $newPeriodeAu = '';

            if ($periodeAu != '') {

                $date_array = explode("/", $periodeAu);
                $var_day = $date_array[0];
                $var_month = $date_array[1];
                $var_year = $date_array[2];
                $newPeriodeAu = "$var_year-$var_month-$var_day";
            }

            if ($newPeriodeAu != '') {
                $newPeriodeAu = (new \DateTime($newPeriodeAu));
            } else {
                $newPeriodeAu = null;
            }

            $facturable = ($post->get('facturable') === 'true' || $post->get('facturable') === 'on') ? 1 : 0;

            $status = ($post->get('status') === 'true' || $post->get('status') === 'on') ? 1 : 0;

            //Mise à jour
            /** @var NdfAffaire $affaire */
            if (null !== $affaire) {

                $affaire->setLibelle($libelle);
                $affaire->setReference($reference);
                $affaire->setNomClient($nomClient);
                $affaire->setDateDeb($newPeriodeDu);
                $affaire->setDateFin($newPeriodeAu);
                $affaire->setFacturable($facturable);
                $affaire->setStatus($status);

                $em->flush();

                return new JsonResponse(2);
            }

            //insertion
            $affaire = new NdfAffaire();

            $affaire->setDossier($dossier);

            $affaire->setLibelle($libelle);
            $affaire->setReference($reference);
            $affaire->setNomClient($nomClient);
            $affaire->setDateDeb($newPeriodeDu);
            $affaire->setDateFin($newPeriodeAu);
            $affaire->setFacturable($facturable);
            $affaire->setStatus($status);

            $em->persist($affaire);
            $em->flush();

            return new JsonResponse(1);
        }

        throw new AccessDeniedHttpException('Accès refusé');

    }

    public function affaireTableauAction(Request $request){

        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $affaires = $this->getDoctrine()
                ->getRepository('AppBundle:NdfAffaire')
                ->findBy(array('dossier' => $dossier));

            return $this->render('NoteFraisBundle:Administration/Affaire:affaireTable.html.twig', array(
                'affaires' => $affaires));
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }

    public function indexUtilisateurAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $ndfUtilisateurs = $this->getDoctrine()
                ->getRepository('AppBundle:NdfUtilisateur')
                ->findBy(array('dossier' => $dossier));

            return $this->render('NoteFraisBundle:Administration/Utilisateur:utilisateurTable.html.twig', array(
                'utilisateurs' => $ndfUtilisateurs
            ));
        }

        throw new AccessDeniedHttpException('Accès refusé');

    }

    public function utilisateurEditAction(Request $request, $json){

        if($request->isXmlHttpRequest()) {
            $post = $request->request;

            $utilisateurId = Boost::deboost($post->get('utilisateurId'), $this);
            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $utilisateur = $this->getDoctrine()
                ->getRepository('AppBundle:NdfUtilisateur')
                ->find($utilisateurId);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            //Affichage modal
            if ($json == 0) {

                return $this->render('NoteFraisBundle:Administration/Utilisateur:utilisateurEdit.html.twig', array(
                    'utilisateur' => $utilisateur
                ));
            }

            //Sauvegarde

            $em = $this->getDoctrine()
                ->getEntityManager();

            $nom = $post->get('nom');
            $prenom = $post->get('prenom');
            $mail = $post->get('mail');
            $matricule = $post->get('matricule');

            $admin = ($post->get('administrateur') === 'true') ? 1 : 0;
            $status = ($post->get('status') === 'true') ? 1 : 0;


            //Mise à jour
            if (null !== $utilisateur) {

                $utilisateur->setNom($nom);
                $utilisateur->setPrenom($prenom);
                $utilisateur->setMail($mail);
                $utilisateur->setMatricule($matricule);
                $utilisateur->setStatus($status);
                $utilisateur->setIsManager($admin);

                $em->flush();

                return new Response(2);

            }

            //insertion
            $utilisateur = new NdfUtilisateur();

            $utilisateur->setDossier($dossier);

            $utilisateur->setNom($nom);
            $utilisateur->setPrenom($prenom);
            $utilisateur->setMail($mail);
            $utilisateur->setMatricule($matricule);
            $utilisateur->setStatus($status);
            $utilisateur->setIsManager($admin);

            $em->persist($utilisateur);
            $em->flush();

            return new Response(1);
        }

        throw new AccessDeniedHttpException('Accès refusé');

    }

    public function utilisateurStatusAction(Request $request){

        if($request->isXmlHttpRequest()) {
            $post = $request->request;

            $utilisateurId = Boost::deboost($post->get('utilisateurId'), $this);
            $active = $post->get('status');

            $utilisateur = $this->getDoctrine()
                ->getRepository('AppBundle:NdfUtilisateur')
                ->find($utilisateurId);

            $em = $this->getDoctrine()
                ->getManager();

            if (is_numeric($active)) {
                $utilisateur->setStatus($active);

                $em->flush();
            }

            return new JsonResponse('status modifié');
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }



    public function pccEditAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest()) {
            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);
            $pccId = Boost::deboost($post->get('pccId'), $this);

            $pcc = $this->getDoctrine()
                ->getRepository('AppBundle:Pcc')
                ->find($pccId);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            //Affichage modal
            if ($json == 0) {

                return $this->render('NoteFraisBundle:Administration/Categorie:pccEdit.html.twig', array(
                    'pcc' => $pcc
                ));
            }

            //Sauvegarde
            $em = $this->getDoctrine()
                ->getEntityManager();

            $compte = $post->get('compte');
            if ($compte == '') {
                $compte = null;
            }

            $intitule = $post->get('intitule');
            if ($intitule == '') {
                $intitule = null;
            }

            $isUpdate = false;

            //Mise à jour
            if (null !== $pcc) {

                $pcc->setIntitule(strtoupper($intitule));
                $pcc->setCompte($compte);
                $em->flush();

                $isUpdate = true;
                $id = Boost::boost($pcc->getId());
            } //insertion
            else {
                //verifier-na aloha hoe efa misy anaty pcc ve ilay compte sa tsia
                $pccBase = $this->getDoctrine()
                    ->getRepository('AppBundle:Pcc')
                    ->findBy(array('dossier' => $dossier, 'compte' => $compte));

                if (count($pccBase) > 0) {
                    return new JsonResponse(array(
                        'id' => -1,
                        'isUpdate' => '',
                        'retCompte' => $compte,
                        'retIntitule' => $intitule
                    ));
                }

                $pcc = new Pcc();

                $pcc->setDossier($dossier);
                $pcc->setIntitule(strtoupper($intitule));
                $pcc->setCompte($compte);
                $pcc->setStatus(1);

                $em->persist($pcc);
                $em->flush();

                $id = Boost::boost($pcc->getId());

            }

            return new JsonResponse(array(
                'id' => $id,
                'isUpdate' => $isUpdate,
                'retCompte' => $compte,
                'retIntitule' => $intitule
            ));
        }

        throw new AccessDeniedHttpException('Accès refusé');

    }

    function comboPccAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $post = $request->request;

            $like = $post->get('likes');
            $likes = array();

            $typePcc = $post->get('typePcc');

            if ($like != "") {
                $like = str_replace(array(' ', 'x', 'X'), '', $like);
                $likes = explode(',', $like);
            }

            if (count($likes) > 0) {
                $pccs = $this->getDoctrine()
                    ->getRepository('AppBundle:Pcc')
                    ->getPccByDossierLike($dossier, $likes);
            } else {
                if ($typePcc === "pcc_charge") {
                    $pccs = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->getPccByDossierLike($dossier, array('6'));
                } else {
                    $pccs = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->getPccByDossierLike($dossier, array('445'));
                }
            }
            $ret = '<option></option>';

            /** @var Pcc $pcc */
            foreach ($pccs as $pcc) {

                $cpte = $pcc->getCompte() . ' - ' . $pcc->getIntitule();

                $ret .= '<option value="' . Boost::boost($pcc->getId()) . '">' . $cpte . '</option>';
            }

            return new Response($ret);
        }

        throw new AccessDeniedHttpException('Accès refusé');

    }

    function pcgAction(Request $request, $type, $json){
        if($request->isXmlHttpRequest()){

            $ndfSousCategorie = $this->getDoctrine()
                ->getRepository('AppBundle:NdfSouscategorieDossier')
                ->find(Boost::deboost($json , $this));

            if($type === "pcg_charge") {
                $pcgs = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfSouscategorieCharge')
                    ->findBy(array('ndfSouscategorie' => $ndfSousCategorie->getNdfSouscategorie()));
            }
            else{
                $pcgs = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfSouscategorieTva')
                    ->findBy(array('ndfSouscategorie' => $ndfSousCategorie->getNdfSouscategorie()));
            }

            $rows = array();

            if(is_array($pcgs)) {
                foreach ($pcgs as $pcg)
                {
                    $rows[] = array(
                        'id' => $pcg->getId(),
                        'cell' => array(
                            $pcg->getCompte(),
                            '<i class="fa fa-save icon-action js_save_pcg" title="Enregistrer"></i>
                             <i class="fa fa-trash icon-action js_delete_pcg" title="Supprimer"></i>'
                        )
                    );
                }
            }

            $liste = array('rows'=>$rows);

            return new JsonResponse($liste);

        }

        throw new AccessDeniedHttpException("Accès refusé");
    }

    function pcgEditAction(Request $request, $type, $json){
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');

            $compte = $request->request->get('pcg-compte');

            $em = $this->getDoctrine()->getManager();

            if($id !== '')
            {
                if($id !== 'new_row')
                {
                    if($type === 'pcg_charge') {
                        $ndfSousCategorieCharge = $this->getDoctrine()
                            ->getRepository('AppBundle:NdfSouscategorieCharge')
                            ->find($id);

                        if ($ndfSousCategorieCharge) {
                            $ndfSousCategorieCharge->setCompte($compte);
                            $em->flush();
                        }
                    }
                    else{
                        $ndfSousCategorieTva = $this->getDoctrine()
                            ->getRepository('AppBundle:NdfSouscategorieTva')
                            ->find($id);

                        if ($ndfSousCategorieTva) {
                            $ndfSousCategorieTva->setCompte($compte);
                            $em->flush();
                        }

                    }
                }
                else {
                    $ndfSoucategorieDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfSouscategorieDossier')
                        ->find(Boost::deboost($json, $this));


                    if (null !== $ndfSoucategorieDossier) {
                        if($type === 'pcg_charge') {
                            $ndfSousCategorieCharge = new NdfSouscategorieCharge();
                            $ndfSousCategorieCharge->setNdfSouscategorie($ndfSoucategorieDossier->getNdfSouscategorie());
                            $ndfSousCategorieCharge->setCompte($compte);
                            $em->persist($ndfSousCategorieCharge);
                            $em->flush();
                        }
                        else{
                            $ndfSousCategorieTva = new NdfSouscategorieTva();
                            $ndfSousCategorieTva->setNdfSouscategorie($ndfSoucategorieDossier->getNdfSouscategorie());
                            $ndfSousCategorieTva->setCompte($compte);
                            $em->persist($ndfSousCategorieTva);
                            $em->flush();
                        }
                    } else {
                        throw new NotFoundHttpException("NdfSousCategorieDossier introuveable.");
                    }


                }

                $data = array('erreur' => false);
                return new JsonResponse(json_encode($data));
            }

            throw new NotFoundHttpException("NdfPcgCharge introuvable");


        }

        throw new AccessDeniedHttpException("Accès refusé.");
    }

    function pcgDeleteAction(Request $request, $type){
        if($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');

            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();

                if($type === 'pcg_charge') {
                    $ndfSouscategorieCharge = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfSouscategorieCharge')
                        ->find($id);

                    if ($ndfSouscategorieCharge) {
                        $em->remove($ndfSouscategorieCharge);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    }
                }

                else{
                    $ndfSouscategorieTva = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfSouscategorieTva')
                        ->find($id);

                    if($ndfSouscategorieTva){
                        $em->remove($ndfSouscategorieTva);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    }
                }

                $data = array(
                    'erreur' => true,
                    'erreur_text' => "NdfSousCategorieCharge introuvable",
                );
                return new JsonResponse(json_encode($data), 404);
            }

            throw new NotFoundHttpException("NdfSousCategorieCharge introuvable.");
        }

        throw new AccessDeniedHttpException("Accès refusé");
    }

}