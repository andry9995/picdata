<?php

namespace InfoPerdosBundle\Controller;

use AppBundle\Entity\ActiviteComCat1;
use AppBundle\Entity\ActiviteComCat2;
use AppBundle\Entity\ActiviteComCat3;
use AppBundle\Entity\AgaCga;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\FormeActivite;
use AppBundle\Entity\FormeJuridique;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageATraiter;
use AppBundle\Entity\InstructionDossier;
use AppBundle\Entity\InstructionSaisie;
use AppBundle\Entity\InstructionTexte;
use AppBundle\Entity\LibelleDossier;
use AppBundle\Entity\LibelleModele;
use AppBundle\Entity\LogInfoperdos;
use AppBundle\Entity\LotGroup;
use AppBundle\Entity\Mandataire;
use AppBundle\Entity\MethodeComptable;
use AppBundle\Entity\PrestationDemande;
use AppBundle\Entity\PrestationFiscale;
use AppBundle\Entity\PrestationGestion;
use AppBundle\Entity\PrestationJuridique;
use AppBundle\Entity\ProfessionLiberale;
use AppBundle\Entity\RegimeFiscal;
use AppBundle\Entity\RegimeImposition;
use AppBundle\Entity\RegimeTva;
use AppBundle\Entity\ReglePaiementClient;
use AppBundle\Entity\ReglePaiementDossier;
use AppBundle\Entity\ReglePaiementTiers;
use AppBundle\Entity\RemarqueDossier;
use AppBundle\Entity\ResponsableCsd;
use AppBundle\Entity\Separation;
use AppBundle\Entity\Siren;
use AppBundle\Entity\TvaTauxDossier;
use AppBundle\Entity\TypeVente;
use AppBundle\Entity\Utilisateur;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Types\TextType;
use PHPExcel_Shared_Font;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\CssSelector\Parser\Token;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\DateTime;



class InfoCaracteristiqueController extends Controller
{
    public function tvaTauxDossierAction(Request $request)
    {

        $post = $request->request;
        $idDossier = Boost::deboost($post->get('dossier'), $this);

        $tvaTauxDossiers = $this->getDoctrine()
            ->getRepository('AppBundle:TvaTauxDossier')
            ->findBy(array('dossier' => $idDossier));

        $resulat = array();

        foreach ($tvaTauxDossiers as $tvaTauxDossier) {
            $resulat[] = $tvaTauxDossier->getTvaTaux()->getId();
            $resulat[] = $tvaTauxDossier->getTvaTaux()->getId();
        }


        return new JsonResponse($resulat);

    }


    public function indexAction(Request $request, $json)
    {
        $regimeFiscals = $this->getDoctrine()
            ->getRepository('AppBundle:RegimeFiscal')
            ->findBy(array('status' => 1), array('libelle' => 'asc'));

        $regimeImpositions = $this->getDoctrine()
            ->getRepository('AppBundle:RegimeImposition')
            ->findBy(array(), array('libelle' => 'asc'));

        $typeVentes = $this->getDoctrine()
            ->getRepository('AppBundle:TypeVente')
            ->findAll();

        $conventionComptables = $this->getDoctrine()
            ->getRepository('AppBundle:ConventionComptable')
            ->findBy(array(), array('libelle' => 'asc'));

        $notesFrais = $this->getDoctrine()
            ->getRepository('AppBundle:NoteDeFrais')
            ->findBy(array(), array('libelle' => 'asc'));

        $regimeTvas = $this->getDoctrine()
            ->getRepository('AppBundle:RegimeTva')
            ->findBy(array(), array('libelle' => 'asc'));

        $tvaTauxs = $this->getDoctrine()
            ->getRepository('AppBundle:TvaTaux')
            ->findBy(array('actif' => 1), array('taux' => 'asc'));

//        $formeJuridiques = $this->getDoctrine()
//            ->getRepository('AppBundle:FormeJuridique')
//            ->findBy(array(), array('libelle' => 'asc'));

        $formeJuridiques = $this->getDoctrine()
            ->getRepository('AppBundle:FormeJuridique')
            ->findBy(array('code' => array('CODE_AUTRE', 'CODE_CE', 'CODE_INDIVISION')),
                array('libelle' => 'asc'));

        $modeVentes = $this->getDoctrine()
            ->getRepository('AppBundle:ModeVente')
            ->findBy(array(), array('libelle' => 'asc'));

        $natureActivites = $this->getDoctrine()
            ->getRepository('AppBundle:NatureActivite')
            ->findBy(array(), array('libelle' => 'asc'));

        $formeActivites = $this->getDoctrine()
            ->getRepository('AppBundle:FormeActivite')
            ->findAll();


        //Instruction dossier
        $methodeSuiviCheques = $this->getDoctrine()
            ->getRepository('AppBundle:MethodeSuiviCheque')
            ->findBy(array(), array('id' => 'asc'));

        $gestionDateEcritures = $this->getDoctrine()
            ->getRepository('AppBundle:GestionDateEcriture')
            ->findBy(array(), array('libelle' => 'asc'));

        $instructionTypes = $this->getDoctrine()
            ->getRepository('AppBundle:InstructionType')
            ->findAll();

        $logiciels = $this->getDoctrine()
            ->getRepository('AppBundle:Logiciel')
            ->findBy(array(), array('rang' => 'asc'));


        $mandataires = $this->getDoctrine()
            ->getRepository('AppBundle:Mandataire')
            ->findBy(array(), array('libelle' => 'asc'));


        $indicateurGroups = $this->getDoctrine()
            ->getRepository('AppBundle:IndicateurGroup')
            ->findBy(array(), array('libelle' => 'asc'));

        $trancheEffectifs = $this->getDoctrine()
            ->getRepository('AppBundle:TrancheEffectif')
            ->findBy(array(), array('id' => 'asc'));

        $typePrestations = $this->getDoctrine()
            ->getRepository('AppBundle:TypePrestation')
            ->findBy(array(), array('id' => 'asc'));


        $idClient = Boost::deboost($request->request->get('client'), $this);


        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($idClient);


        /** @var  $instructionDossier InstructionDossier */

        $instructionDossier = $this->getDoctrine()
            ->getRepository('AppBundle:InstructionDossier')
            ->getInstructionDossierByClient($client);


        $reglePaiementClientFournisseurs = $this->getDoctrine()
            ->getRepository('AppBundle:ReglePaiementClient')
            ->findBy(array('client' => $client, 'typeTiers' => 0));

        $reglePaiementClientClients = $this->getDoctrine()
            ->getRepository('AppBundle:ReglePaiementClient')
            ->findBy(array('client' => $client, 'typeTiers' => 1));

        /** @var  $reglePaiementClientFournisseur  ReglePaiementClient */
        $reglePaiementClientFournisseur = null;

        /** @var ReglePaiementClient $reglePaiementClientClient */
        $reglePaiementClientClient = null;


        if ($reglePaiementClientFournisseurs != null) {
            $reglePaiementClientFournisseur = $reglePaiementClientFournisseurs[0];
        }

        if ($reglePaiementClientClients != null) {
            $reglePaiementClientClient = $reglePaiementClientClients[0];
        }


        $isAdmin = false;

        if ($this->isGranted("ROLE_SCRIPTURA_ADMIN")) {
            $isAdmin = true;
        }


        //Affichage @ voalohany
        if ($json == 0) {

            return $this->render('InfoPerdosBundle:Default:index.html.twig', array(
                'regimeFiscals' => $regimeFiscals,
                'regimeImpositions' => $regimeImpositions,
                'typeVentes' => $typeVentes,
                'conventionCompables' => $conventionComptables,
                'notesFrais' => $notesFrais,
                'regimeTvas' => $regimeTvas,
                'tvaTauxs' => $tvaTauxs,
                'formeJuridiques' => $formeJuridiques,
                'modeVentes' => $modeVentes,
                'natureActivites' => $natureActivites,
                'formeActivites' => $formeActivites,
                'dossier' => null,
                'methodeComptable' => null,
                'prestationDemande' => null,
                'prestationFiscale' => null,
                'prestationGestion' => null,
                'prestationJuridique' => null,
                'tvaTauxDossiers' => null,
                'mandataires' => $mandataires,
                'responsableCsd' => null,

                'methodeSuiviCheques' => $methodeSuiviCheques,
                'gestionDateEcritures' => $gestionDateEcritures,
                'instrucitonTypes' => $instructionTypes,
                'logiciels' => $logiciels,


                'remarqueDossier' => null,

                'indicateurGroups' => $indicateurGroups,
                'indicateurSpecGroup' => null,

                'reglePaiementDossierFournisseur' => null,
                'reglePaiementDossierClient' => null,

                'reglePaiementClientFournisseur' => $reglePaiementClientFournisseur,
                'reglePaiementClientClient' => $reglePaiementClientClient,


                'trancheEffectifs' => $trancheEffectifs,

                'agaCga' => null,

                'instructionDossier' => $instructionDossier,

                'typePrestations' => $typePrestations,

                'isAdmin' => $isAdmin,

                'instructionSaisie' => null,

                'libelleModeleAchats' => null,
                'libelleAchats' => null,

                'libelleModeleVentes' => null,
                'libelleVentes' => null,

                'libelleModeleBanques' => null,
                'libelleBanques' => null

            ));


        } //Affichage rehefa avy mi-selectionner dossier
        else {

            if ($request->isXmlHttpRequest()) {

                $post = $request->request;
                $idDossier = Boost::deboost($post->get('dossier'), $this);

                /** @var Dossier $dossier */
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);


                /* @var $methodeComptable MethodeComptable */

                $methodeComptable = $this->getDoctrine()
                    ->getRepository('AppBundle:MethodeComptable')
                    ->getMethodeComptableByDossier($dossier);

                /* @var $prestationDemande PrestationDemande */
                $prestationDemande = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationDemande')
                    ->getPrestationDemandeByDossier($dossier);


                /* @var $prestationFiscale PrestationFiscale */
                $prestationFiscale = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationFiscale')
                    ->getPrestaitonFiscaleByDossier($dossier);


                /* @var $prestationGestion PrestationGestion */
                $prestationGestion = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationGestion')
                    ->getPrestationGestionByDossier($dossier);


                /* @var $prestationJuridique PrestationJuridique */
                $prestationJuridique = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationJuridique')
                    ->getPrestationJurique($dossier);


                $mandataires = $this->getDoctrine()
                    ->getRepository('AppBundle:Mandataire')
                    ->findBy(array(), array('libelle' => 'asc'));

                $resCsd = null;
                if (!is_null($dossier)) {
                    $resCsd = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findBy(array('typeResponsable' => 0, 'dossier' => $dossier));
                }

                /** @var  $responsableCsd  ResponsableCsd */
                $responsableCsd = null;

                if ($resCsd != null) {
                    $responsableCsd = $resCsd[0];
                }

                $remarqueDossier = $this->getDoctrine()
                    ->getRepository('AppBundle:RemarqueDossier')
                    ->findOneBy(array('dossier' => $dossier));


                $indicateurSpecGroup = $this->getDoctrine()
                    ->getRepository('AppBundle:IndicateurSpecGroup')
                    ->findOneBy(array('dossier' => $dossier));


                $indicateurGroups = $this->getDoctrine()
                    ->getRepository('AppBundle:IndicateurGroup')
                    ->findBy(array(), array('libelle' => 'asc'));

                $reglePaiementDossierFournisseurs = $this->getDoctrine()
                    ->getRepository('AppBundle:ReglePaiementDossier')
                    ->findBy(array('dossier' => $dossier, 'typeTiers' => 0));

                $reglePaiementDossierClients = $this->getDoctrine()
                    ->getRepository('AppBundle:ReglePaiementDossier')
                    ->findBy(array('dossier' => $dossier, 'typeTiers' => 1));


                /** @var  $reglePaiementDossierFournisseur ReglePaiementDossier */
                $reglePaiementDossierFournisseur = null;

                /** @var  $reglePaiementDossierClient ReglePaiementDossier */
                $reglePaiementDossierClient = null;


                if ($reglePaiementDossierFournisseurs != null) {
                    $reglePaiementDossierFournisseur = $reglePaiementDossierFournisseurs[0];
                }

                if ($reglePaiementDossierClients != null) {
                    $reglePaiementDossierClient = $reglePaiementDossierClients[0];
                }


                /** @var  $agaCga AgaCga */
                $agaCga = null;
                if (!is_null($dossier)) {


                    $liasseFiscal = null;


                    if (!is_null($prestationFiscale)) {
                        $liasseFiscal = $prestationFiscale->getLiasse();
                    }

                    /** @var RegimeFiscal $regimeFiscal */
                    $regimeFiscal = $dossier->getRegimeFiscal();

                    if (!is_null($regimeFiscal)) {

                        $regimeFiscalAccepete = array('CODE_BIC_IR', 'CODE_BNC', 'CODE_BA');

                        //Raha ao anaty liste ny forme juridique & liasse fiscal = oui izay vao mijery AGA
                        if (in_array($regimeFiscal->getCode(), $regimeFiscalAccepete) && $liasseFiscal == 1) {

                            $agaCgas = $this->getDoctrine()
                                ->getRepository('AppBundle:AgaCga')
                                ->findBy(array('dossier' => $dossier));

                            if (count($agaCgas) > 0) {
                                $agaCga = $agaCgas[0];
                            }
                        }
                    }
                }

                $formeJuridiques = $this->getDoctrine()
                    ->getRepository('AppBundle:FormeJuridique')
                    ->findBy(array('code' => array('CODE_AUTRE', 'CODE_CE', 'CODE_INDIVISION')),
                        array('libelle' => 'asc'));


                if (!is_null($dossier)) {

                    if (!is_null($dossier->getSirenSte())) {

                        if ($dossier->getSirenSte() != '') {
                            $formeJuridiques = $this->getDoctrine()
                                ->getRepository('AppBundle:FormeJuridique')
                                ->findBy(array(), array('libelle' => 'asc'));
                        }
                    }
                }

                $instructionSaisies = $this->getDoctrine()
                    ->getRepository('AppBundle:InstructionSaisie')
                    ->findBy(array('dossier' => $dossier));

                $instructionSaisie = null;
                if(count($instructionSaisies)){
                    $instructionSaisie = $instructionSaisies[0];
                }


                $libelleTypeAchat = $this->getDoctrine()
                    ->getRepository('AppBundle:LibelleType')
                    ->find(1);

                /** @var LibelleModele[] $libelleModeleAchats */
                $libelleModeleAchats = $this->getDoctrine()
                    ->getRepository('AppBundle:LibelleModele')
                    ->getLibelleModeleByDossier($dossier, $libelleTypeAchat);

                /** @var LibelleDossier[] $libelleAchats */
                $libelleAchats = $this->getDoctrine()
                    ->getRepository('AppBundle:LibelleDossier')
                    ->getLibelleDossierByType($dossier, $libelleTypeAchat);


                $libelleTypeVente = $this->getDoctrine()
                    ->getRepository('AppBundle:LibelleType')
                    ->find(3);

                /** @var LibelleModele[] $libelleModeleVentes */
                $libelleModeleVentes = $this->getDoctrine()
                    ->getRepository('AppBundle:LibelleModele')
                    ->getLibelleModeleByDossier($dossier, $libelleTypeVente);

                /** @var LibelleDossier[] $libelleVentes */
                $libelleVentes = $this->getDoctrine()
                    ->getRepository('AppBundle:LibelleDossier')
                    ->getLibelleDossierByType($dossier, $libelleTypeVente);



                $libelleTypeBanque = $this->getDoctrine()
                    ->getRepository('AppBundle:LibelleType')
                    ->find(5);

                /** @var LibelleModele[] $libelleModeleBanques */
                $libelleModeleBanques = $this->getDoctrine()
                    ->getRepository('AppBundle:LibelleModele')
                    ->getLibelleModeleByDossier($dossier, $libelleTypeBanque);

                /** @var LibelleDossier[] $libelleBanques */
                $libelleBanques = $this->getDoctrine()
                    ->getRepository('AppBundle:LibelleDossier')
                    ->getLibelleDossierByType($dossier, $libelleTypeBanque);


                return $this->render('InfoPerdosBundle:Default:contenuTabs.html.twig', array(
                    'regimeFiscals' => $regimeFiscals,
                    'regimeImpositions' => $regimeImpositions,
                    'typeVentes' => $typeVentes,
                    'conventionCompables' => $conventionComptables,
                    'notesFrais' => $notesFrais,
                    'regimeTvas' => $regimeTvas,
                    'tvaTauxs' => $tvaTauxs,
                    'formeJuridiques' => $formeJuridiques,
                    'modeVentes' => $modeVentes,
                    'natureActivites' => $natureActivites,
                    'formeActivites' => $formeActivites,
                    'dossier' => $dossier,
                    'methodeComptable' => $methodeComptable,
                    'prestationDemande' => $prestationDemande,
                    'prestationFiscale' => $prestationFiscale,
                    'prestationGestion' => $prestationGestion,
                    'prestationJuridique' => $prestationJuridique,
                    'mandataires' => $mandataires,
                    'responsableCsd' => $responsableCsd,

                    'methodeSuiviCheques' => $methodeSuiviCheques,
                    'gestionDateEcritures' => $gestionDateEcritures,
                    'instrucitonTypes' => $instructionTypes,
                    'logiciels' => $logiciels,

                    'remarqueDossier' => $remarqueDossier,

                    'indicateurGroups' => $indicateurGroups,
                    'indicateurSpecGroup' => $indicateurSpecGroup,

                    'reglePaiementDossierFournisseur' => $reglePaiementDossierFournisseur,
                    'reglePaiementDossierClient' => $reglePaiementDossierClient,

                    'reglePaiementClientFournisseur' => $reglePaiementClientFournisseur,
                    'reglePaiementClientClient' => $reglePaiementClientClient,

                    'trancheEffectifs' => $trancheEffectifs,

                    'agaCga' => $agaCga,

                    'instructionDossier' => $instructionDossier,

                    'typePrestations' => $typePrestations,

                    'isAdmin' => $isAdmin,

                    'instructionSaisie' => $instructionSaisie,

                    'libelleModeleAchats' => $libelleModeleAchats,
                    'libelleAchats' => $libelleAchats,

                    'libelleModeleVentes' => $libelleModeleVentes,
                    'libelleVentes' => $libelleVentes,

                    'libelleModeleBanques' => $libelleModeleBanques,
                    'libelleBanques' => $libelleBanques


                ));
            } else {
                throw new AccessDeniedHttpException("Accès refusé");
            }
        }
    }

    public function editInfoPerdosAction(Request $request)
    {
//        if($request->isXmlHttpRequest())
        {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $dossierId = $post->get('dossierId');

            //Identification dossier
            $nomDossier = $post->get('nomDossier');

            $nomDossier = trim($nomDossier, ' ');
            $nomDossier = preg_replace("/[^[:alnum:]-_]/", '_', $nomDossier);


            //$nomDossier = 'dossier Test';
            $raisonSocial = $post->get('raisonSocial');

            $sirenSiret = $post->get('sirenSiret');
            $moisCloture = $post->get('moisCloture');

            $premierExercice = $post->get('premierExercice');
            if ($premierExercice == '') {
                $premierExercice = null;
            }


            $dateDebutActivite = $post->get('dateDebutActivite');

            $newDateDebutActivite = '';

            if ($dateDebutActivite != '') {

                $date_array = explode("/", $dateDebutActivite);
                $var_day = $date_array[0];
                $var_month = $date_array[1];
                $var_year = $date_array[2];
                $newDateDebutActivite = "$var_year-$var_month-$var_day";
            }


            //Caracteristique dossier
            $formeJuridiqueId = $post->get('formeJuridique');
            $formeJuridique = null;
            if ($formeJuridiqueId != '') {
                $formeJuridique = $this->getDoctrine()
                    ->getRepository('AppBundle:FormeJuridique')
                    ->find($formeJuridiqueId);
            }

            $regimeFiscalId = $post->get('regimeFiscal');
            $regimeFiscal = null;
            if ($regimeFiscalId != '') {
                $regimeFiscal = $this->getDoctrine()
                    ->getRepository('AppBundle:RegimeFiscal')
                    ->find($regimeFiscalId);
            }

            $regimeImpositionId = $post->get('regimeImposition');
            $regimeImposition = null;
            if ($regimeImpositionId != '') {
                $regimeImposition = $this->getDoctrine()
                    ->getRepository('AppBundle:RegimeImposition')
                    ->find($regimeImpositionId);
            }

            $natureActiviteId = $post->get('natureActivite');
            $natureActivite = null;
            if ($natureActiviteId != '') {
                $natureActivite = $this->getDoctrine()
                    ->getRepository('AppBundle:NatureActivite')
                    ->find($natureActiviteId);
            }


            $formeActiviteId = $post->get('formeActivite');
            $formeActivite = null;

            if ($formeActiviteId != '') {
                $formeActivite = $this->getDoctrine()
                    ->getRepository('AppBundle:FormeActivite')
                    ->find($formeActiviteId);
            }


            $activiteComCat3Id = $post->get('activiteComCat3');
            $activiteComCat3 = null;

            if ($activiteComCat3Id != '') {
                $activiteComCat3 = $this->getDoctrine()
                    ->getRepository('AppBundle:ActiviteComCat3')
                    ->find($activiteComCat3Id);


            }


            $professionLiberaleId = $post->get('professionLiberale');
            $professionLiberale = null;
            if ($professionLiberaleId != '') {
//
                $professionLiberale = $this->getDoctrine()
                    ->getRepository('AppBundle:ProfessionLiberale')
                    ->find($professionLiberaleId);
            }


            $modeVenteId = $post->get('modeVente');
            $modeVente = null;
            if ($modeVenteId != '') {
                $modeVente = $this->getDoctrine()
                    ->getRepository('AppBundle:ModeVente')
                    ->find($modeVenteId);
            }


            $tvaRegimeId = $post->get('tvaRegime');
            $tvaRegime = null;
            if ($tvaRegimeId != '') {
                $tvaRegime = $this->getDoctrine()
                    ->getRepository('AppBundle:RegimeTva')
                    ->find($tvaRegimeId);
            }

            $taxeSalaire = $post->get('taxeSalaire');
//
//            //Information comptable et fiscale
            $comptaSurServeur = $post->get('comptaSurServeur');

            if ($comptaSurServeur == '') {
                $comptaSurServeur = null;
            }

            $planComptable = $post->get('planComptable');
            if ($planComptable == '') {
                $planComptable = null;
            }

            $archiveComptable = $post->get('archiveComptable');
            if ($archiveComptable == '') {
                $archiveComptable = null;
            }

            $grandLivre = $post->get('grandLivre');
            if ($grandLivre == '') {
                $grandLivre = null;
            }

            $journauxN1 = $post->get('journauxN1');
            if ($journauxN1 == '') {
                $journauxN1 = null;
            }

            $rapprochementBanqueN1 = $post->get('rapprochementBanqueN1');
            if ($rapprochementBanqueN1 == '') {
                $rapprochementBanqueN1 = null;
            }

            $etatImmobilisation = $post->get('etatImmobilisation');
            if ($etatImmobilisation == '') {
                $etatImmobilisation = null;
            }

            $liasseN1 = $post->get('liasseN1');
            if ($liasseN1 == '') {
                $liasseN1 = null;
            }

            $tvaDerniereCa3 = $post->get('tvaDerniereCa3');
            if ($tvaDerniereCa3 == '') {
                $tvaDerniereCa3 = null;
            }
            $tvaTaux = $post->get('tvaTaux');
            if ($tvaTaux == '') {
                $tvaTaux = null;
            }

            $dateTva = $post->get('dateTva');
            if ($dateTva == '') {
                $dateTva = null;
            }
//
//            //Documents juridiques
            $statut = $post->get('statut');
            if ($statut == '') {
                $statut = null;
            }

            $kbis = $post->get('kbis');
            if ($kbis == '') {
                $kbis = null;
            }

            $baux = $post->get('baux');
            if ($baux == '') {
                $baux = null;
            }

            $assurance = $post->get('assurance');
            $autre = $post->get('autre');
            $emprunt = $post->get('emprunt');
            $leasing = $post->get('leasing');


            $idDossier = Boost::deboost($post->get('dossierId'), $this);
            $idSite = Boost::deboost($post->get('site'), $this);
//
            //Nouveau dossier
            if ($idDossier == 0) {


                try {
                    $newDossier = new Dossier();

                    $convetion = $this->getDoctrine()
                        ->getRepository('AppBundle:ConventionComptable')
                        ->find(1);

                    $newDossier->setConventionComptable($convetion);


                    $site = $this->getDoctrine()
                        ->getRepository('AppBundle:Site')
                        ->find($idSite);


                    $newDossier->setSite($site);


                    //Identification dossier
                    $newDossier->setNom($nomDossier);
                    $newDossier->setRsSte($raisonSocial);
                    //Supprimer-na ireto rehefa voaova ny base
                    $newDossier->setEntreprise($raisonSocial);
                    $newDossier->setSirenSte($sirenSiret);
                    $newDossier->setCloture($moisCloture);
                    $newDossier->setPremierExercice($premierExercice);

                    if ($newDateDebutActivite != '') {
                        $newDossier->setDebutActivite(new \DateTime($newDateDebutActivite));
                    } else {
                        $newDossier->setDebutActivite(new \DateTime());
                    }

//
//                //caracteristique dossier
                    $newDossier->setFormeJuridique($formeJuridique);
                    $newDossier->setRegimeFiscal($regimeFiscal);
                    $newDossier->setRegimeImposition($regimeImposition);
                    $newDossier->setNatureActivite($natureActivite);
                    $newDossier->setFormeActivite2($formeActivite);
                    $newDossier->setActiviteComCat3($activiteComCat3);
                    $newDossier->setProfessionLiberale($professionLiberale);
                    $newDossier->setModeVente($modeVente);
                    $newDossier->setRegimeTva($tvaRegime);
                    $newDossier->setTaxeSalaire($taxeSalaire);

                    //Information comptable et fiscale
                    $newDossier->setComptaSurServeur($comptaSurServeur);
                    $newDossier->setPlanComptable($planComptable);
                    $newDossier->setArchiveComptable($archiveComptable);
                    $newDossier->setGrandLivre($grandLivre);
                    $newDossier->setJournauxN1($journauxN1);
                    $newDossier->setDernierRapprochementBanqueN1($rapprochementBanqueN1);
                    $newDossier->setEtatImmobilisationN1($etatImmobilisation);
                    $newDossier->setLiasseFiscaleN1($liasseN1);
                    $newDossier->setTvaDerniereCa3($tvaDerniereCa3);
                    $newDossier->setTvaTauxId($tvaTaux);
                    $newDossier->setTvaDate($dateTva);

//                //Documents juridiques
                    $newDossier->setStatut($statut);
                    $newDossier->setKbis($kbis);
                    $newDossier->setBaux($baux);
                    $newDossier->setAssurance($assurance);
                    $newDossier->setAutre($autre);
                    $newDossier->setEmprunt($emprunt);
                    $newDossier->setLeasing($leasing);

                    $em->persist($newDossier);
                    $em->flush();

                    //1:insertion
                    return new Response(1);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            } //Mise à jour dossier
            else {

                try {


                    $newDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($idDossier);

                    $convetion = $this->getDoctrine()
                        ->getRepository('AppBundle:ConventionComptable')
                        ->find(1);

                    $newDossier->setConventionComptable($convetion);

                    //Identification dossier
                    $newDossier->setNom($nomDossier);
                    $newDossier->setRsSte($raisonSocial);
                    //Supprimer-na ireto rehefa voaova ny base
                    $newDossier->setEntreprise($raisonSocial);
                    $newDossier->setSirenSte($sirenSiret);
                    $newDossier->setCloture($moisCloture);
                    $newDossier->setPremierExercice($premierExercice);

                    if ($newDateDebutActivite != '') {
                        $newDossier->setDebutActivite(new \DateTime($newDateDebutActivite));
                    } else {
                        $newDossier->setDebutActivite(new \DateTime());
                    }
//
//                //caracteristique dossier
                    $newDossier->setFormeJuridique($formeJuridique);
                    $newDossier->setRegimeFiscal($regimeFiscal);
                    $newDossier->setRegimeImposition($regimeImposition);
                    $newDossier->setNatureActivite($natureActivite);
                    $newDossier->setFormeActivite2($formeActivite);
                    $newDossier->setActiviteComCat3($activiteComCat3);
                    $newDossier->setProfessionLiberale($professionLiberale);
                    $newDossier->setModeVente($modeVente);
                    $newDossier->setRegimeTva($tvaRegime);
                    $newDossier->setTaxeSalaire($taxeSalaire);

                    //Information comptable et fiscale
                    $newDossier->setComptaSurServeur($comptaSurServeur);
                    $newDossier->setPlanComptable($planComptable);
                    $newDossier->setArchiveComptable($archiveComptable);
                    $newDossier->setGrandLivre($grandLivre);
                    $newDossier->setJournauxN1($journauxN1);
                    $newDossier->setDernierRapprochementBanqueN1($rapprochementBanqueN1);
                    $newDossier->setEtatImmobilisationN1($etatImmobilisation);
                    $newDossier->setLiasseFiscaleN1($liasseN1);
                    $newDossier->setTvaDerniereCa3($tvaDerniereCa3);
                    $newDossier->setTvaTauxId($tvaTaux);
                    $newDossier->setTvaDate($dateTva);

//                //Documents juridiques
                    $newDossier->setStatut($statut);
                    $newDossier->setKbis($kbis);
                    $newDossier->setBaux($baux);
                    $newDossier->setAssurance($assurance);
                    $newDossier->setAutre($autre);
                    $newDossier->setEmprunt($emprunt);
                    $newDossier->setLeasing($leasing);

                    $em->persist($newDossier);
                    $em->flush();

                    //2: mise à jour
                    return new Response(2);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            }


        }
//        else
        {
//            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editInfoPerdosCaracteristiqueAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            //Nouveau dossier
            if ($idDossier == 0) {
                $res = array('estInsere' => -1, 'message' => 'Dossier');
                return new JsonResponse($res);
            } //Mise à jour dossier
            else {

                try {

                    $newDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($idDossier);

                    $formeJuridiqueId = $post->get('formeJuridique');
                    $formeJuridique = null;

                    $tvaRegime = null;
                    $tvaMode = null;
                    $taxeSalaire = null;

                    $tvaTaux = null;
                    $dateTva = null;


                    if ($formeJuridiqueId != '') {

                        $formeJuridique = $this->getDoctrine()
                            ->getRepository('AppBundle:FormeJuridique')
                            ->find($formeJuridiqueId);

                        //Forme juridique == Auto entreprise => TVA regime non soumis, TVA mode = null
                        if ($formeJuridique->getId() == 15) {

                            $tvaRegime = $this->getDoctrine()
                                ->getRepository('AppBundle:RegimeTva')
                                ->findOneBy(array('code' => 'CODE_NON_SOUMIS'));

                            $tvaMode = null;
                            $tvaFaitGenerateur = null;

                        } //Forme juridique != Auto entreprise => TVA regime (si TVA regime non soumis, TVA mode = null)
                        else {
                            $tvaRegimeId = $post->get('tvaRegime');

                            if ($tvaRegimeId != '') {
                                $tvaRegime = $this->getDoctrine()
                                    ->getRepository('AppBundle:RegimeTva')
                                    ->find($tvaRegimeId);

                                if ($tvaRegime->getCode() === 'CODE_NON_SOUMIS' ||
                                    $tvaRegime->getCode() === 'CODE_FRANCHISE') {

                                    $tvaMode = null;
                                    $tvaFaitGenerateur = null;

                                    $taxeSalaire = $post->get('taxeSalaire');
                                    if ($taxeSalaire == '') {
//                                        $res = array('estInsere' => 0, 'message' => 'Taxe sur salaire');
//                                        return new JsonResponse($res);
                                        $taxeSalaire = null;
                                    }

                                } //Si tva regime != 13 non soumis => Taxe sur salaire = null
                                else {
                                    $taxeSalaire = null;
                                    $tvaMode = $post->get('tvaMode');


                                    $tvaTaux = $post->get('tvaTaux');

                                    if (count($tvaTaux) > 0) {
                                        $tvaTauxDossier = $em->getRepository('AppBundle:TvaTauxDossier')
                                            ->findBy(array('dossier' => $idDossier));

                                        /** @var  $tvaTauxDoss TvaTauxDossier */
                                        foreach ($tvaTauxDossier as $tvaTauxDoss) {
                                            $em->remove($tvaTauxDoss);
                                        }

                                        if ($tvaTaux != null) {
                                            foreach ($tvaTaux as $taux) {

                                                if ($taux != '') {

                                                    $newTvaTauxDossier = $this->getDoctrine()
                                                        ->getRepository('AppBundle:TvaTaux')
                                                        ->find(intval($taux));

                                                    $tvaTauxDossier = new TvaTauxDossier();
                                                    $tvaTauxDossier->setDossier($newDossier);
                                                    $tvaTauxDossier->setTvaTaux($newTvaTauxDossier);

                                                    $em->persist($tvaTauxDossier);
                                                }
                                            }
                                        }
                                    }
//                                    else {
//                                        $res = array('estInsere' => 0, 'message' => 'TVA Taux');
//                                        return new JsonResponse($res);
//                                    }


                                    $dateTva = $post->get('dateTva');
                                    if ($dateTva == '') {
//                                        $res = array('estInsere' => 0, 'message' => 'Date TVA');
//                                        return new JsonResponse($res);
                                        $dateTva = null;
                                    }

                                }
                            }
//                            else {
//                                $res = array('estInsere' => 0, 'message' => 'TVA Régime');
//                                return new JsonResponse($res);
//                            }

                            else {
                                $taxeSalaire = null;
                                $tvaMode = $post->get('tvaMode');


                                $tvaTaux = $post->get('tvaTaux');

                                if (count($tvaTaux) > 0) {
                                    $tvaTauxDossier = $em->getRepository('AppBundle:TvaTauxDossier')
                                        ->findBy(array('dossier' => $idDossier));

                                    /** @var  $tvaTauxDoss TvaTauxDossier */
                                    foreach ($tvaTauxDossier as $tvaTauxDoss) {
                                        $em->remove($tvaTauxDoss);
                                    }

                                    if ($tvaTaux != null) {
                                        foreach ($tvaTaux as $taux) {

                                            if ($taux != '') {

                                                $newTvaTauxDossier = $this->getDoctrine()
                                                    ->getRepository('AppBundle:TvaTaux')
                                                    ->find(intval($taux));

                                                $tvaTauxDossier = new TvaTauxDossier();
                                                $tvaTauxDossier->setDossier($newDossier);
                                                $tvaTauxDossier->setTvaTaux($newTvaTauxDossier);

                                                $em->persist($tvaTauxDossier);
                                            }
                                        }
                                    }
                                }
//                                    else {
//                                        $res = array('estInsere' => 0, 'message' => 'TVA Taux');
//                                        return new JsonResponse($res);
//                                    }


                                $dateTva = $post->get('dateTva');
//                                    if ($dateTva == '') {
//                                        $res = array('estInsere' => 0, 'message' => 'Date TVA');
//                                        return new JsonResponse($res);
//                                    }

                            }

                        }
                    }
//                    else {
//                        $res = array('estInsere' => 0, 'message' => 'Forme');
//                        return new JsonResponse($res);
//                    }

                    $regimeFiscalId = $post->get('regimeFiscal');
                    $regimeFiscal = null;
                    if ($regimeFiscalId != '') {
                        $regimeFiscal = $this->getDoctrine()
                            ->getRepository('AppBundle:RegimeFiscal')
                            ->find($regimeFiscalId);
                    } else {
//                        $res = array('estInsere' => 0, 'message' => 'Régime Fiscal');
//                        return new JsonResponse($res);
                        $regimeFiscal = null;
                    }

                    $regimeImpositionId = $post->get('regimeImposition');
                    $regimeImposition = null;
                    if ($regimeImpositionId != '') {
                        $regimeImposition = $this->getDoctrine()
                            ->getRepository('AppBundle:RegimeImposition')
                            ->find($regimeImpositionId);
                    } else {
//                        $res = array('estInsere' => 0, 'message' => 'Régime Imposition');
//                        return new JsonResponse($res);
                    }

                    $natureActiviteId = $post->get('natureActivite');
                    $natureActivite = null;
                    if ($natureActiviteId != '') {
                        $natureActivite = $this->getDoctrine()
                            ->getRepository('AppBundle:NatureActivite')
                            ->find($natureActiviteId);
                    } else {
//                        $res = array('estInsere' => 0, 'message' => 'Type Activité');
//                        return new JsonResponse($res);
                    }

                    $formeActiviteId = $post->get('formeActivite');
                    $formeActivite = null;

                    $professionLiberaleId = $post->get('professionLiberale');
                    $professionLiberale = null;

                    if ($formeActiviteId != '') {
                        $formeActivite = $this->getDoctrine()
                            ->getRepository('AppBundle:FormeActivite')
                            ->find($formeActiviteId);

                        //Forme activite == profession liberale => profession liberale = obligatoire
//                        if ($formeActivite->getId() == 1) {
                        if ($formeActivite->getCode() == 'CODE_PROFESSION_LIBERALE') {
                            if ($professionLiberaleId != '') {

                                $professionLiberale = $this->getDoctrine()
                                    ->getRepository('AppBundle:ProfessionLiberale')
                                    ->find($professionLiberaleId);
                            } else {
//                                $res = array('estInsere' => 0, 'message' => 'Profession libérale');
//                                return new JsonResponse($res);

                            }

                        } //Forme activite != profession liberale => profession liberale = null
                        else {
                            $professionLiberale = null;
                        }
                    } else {
//                        $res = array('estInsere' => 0, 'message' => 'Forme activité');
//                        return new JsonResponse($res);
                    }

                    $modeVenteId = $post->get('modeVente');
                    $modeVente = null;
                    if ($modeVenteId != '') {
                        $modeVente = $this->getDoctrine()
                            ->getRepository('AppBundle:ModeVente')
                            ->find($modeVenteId);
                    } else {
//                        $res = array('estInsere' => 0, 'message' => 'Type de ventes');
//                        return new JsonResponse($res);
                    }


                    $tvaFaitGenerateur = $post->get('tvaFaitGenerateur');
                    if ($tvaFaitGenerateur == '') {
                        $tvaFaitGenerateur = null;
                    }


                    if ($formeJuridiqueId == '') {
                        $tvaRegimeId = $post->get('tvaRegime');

                        if ($tvaRegimeId != '') {
                            $tvaRegime = $this->getDoctrine()
                                ->getRepository('AppBundle:RegimeTva')
                                ->find($tvaRegimeId);

                            if ($tvaRegime->getCode() === 'CODE_NON_SOUMIS' ||
                                $tvaRegime->getCode() === 'CODE_FRANCHISE') {
                                $tvaMode = null;
                                $taxeSalaire = $post->get('taxeSalaire');
//                                    if ($taxeSalaire == '') {
//                                        $res = array('estInsere' => 0, 'message' => 'Taxe sur salaire');
//                                        return new JsonResponse($res);
//                                    }

                            } //Si tva regime != 13 non soumis => Taxe sur salaire = null
                            else {
                                $taxeSalaire = null;
                                $tvaMode = $post->get('tvaMode');


                                $tvaTaux = $post->get('tvaTaux');

                                if (count($tvaTaux) > 0) {
                                    $tvaTauxDossier = $em->getRepository('AppBundle:TvaTauxDossier')
                                        ->findBy(array('dossier' => $idDossier));

                                    /** @var  $tvaTauxDoss TvaTauxDossier */
                                    foreach ($tvaTauxDossier as $tvaTauxDoss) {
                                        $em->remove($tvaTauxDoss);
                                    }

                                    if ($tvaTaux != null) {
                                        foreach ($tvaTaux as $taux) {

                                            if ($taux != '') {

                                                $newTvaTauxDossier = $this->getDoctrine()
                                                    ->getRepository('AppBundle:TvaTaux')
                                                    ->find(intval($taux));

                                                $tvaTauxDossier = new TvaTauxDossier();
                                                $tvaTauxDossier->setDossier($newDossier);
                                                $tvaTauxDossier->setTvaTaux($newTvaTauxDossier);

                                                $em->persist($tvaTauxDossier);
                                            }
                                        }
                                    }
                                }
//                                    else {
//                                        $res = array('estInsere' => 0, 'message' => 'TVA Taux');
//                                        return new JsonResponse($res);
//                                    }


                                $dateTva = $post->get('dateTva');
//                                    if ($dateTva == '') {
//                                        $res = array('estInsere' => 0, 'message' => 'Date TVA');
//                                        return new JsonResponse($res);
//                                    }

                            }
                        }
//                            else {
//                                $res = array('estInsere' => 0, 'message' => 'TVA Régime');
//                                return new JsonResponse($res);
//                            }

                        else {
                            $taxeSalaire = null;
                            $tvaMode = $post->get('tvaMode');

                            $tvaTaux = $post->get('tvaTaux');

                            if (count($tvaTaux) > 0) {
                                $tvaTauxDossier = $em->getRepository('AppBundle:TvaTauxDossier')
                                    ->findBy(array('dossier' => $idDossier));

                                /** @var  $tvaTauxDoss TvaTauxDossier */
                                foreach ($tvaTauxDossier as $tvaTauxDoss) {
                                    $em->remove($tvaTauxDoss);
                                }

                                if ($tvaTaux != null) {
                                    foreach ($tvaTaux as $taux) {

                                        if ($taux != '') {

                                            $newTvaTauxDossier = $this->getDoctrine()
                                                ->getRepository('AppBundle:TvaTaux')
                                                ->find(intval($taux));

                                            $tvaTauxDossier = new TvaTauxDossier();
                                            $tvaTauxDossier->setDossier($newDossier);
                                            $tvaTauxDossier->setTvaTaux($newTvaTauxDossier);

                                            $em->persist($tvaTauxDossier);
                                        }
                                    }
                                }
                            }
//                                    else {
//                                        $res = array('estInsere' => 0, 'message' => 'TVA Taux');
//                                        return new JsonResponse($res);
//                                    }


                            $dateTva = $post->get('dateTva');
//                                    if ($dateTva == '') {
//                                        $res = array('estInsere' => 0, 'message' => 'Date TVA');
//                                        return new JsonResponse($res);
//                                    }

                        }
                    }

                    /** @var $regimeFiscal RegimeFiscal */
                    if ($regimeFiscal != null) {
                        if (!($regimeFiscal->getCode() == "CODE_BNC" || $regimeFiscal->getCode() == "CODE_BIC_IR")) {
                            $agaCgas = $this->getDoctrine()
                                ->getRepository('AppBundle:AgaCga')
                                ->findBy(array('dossier' => $newDossier));

                            foreach ($agaCgas as $agaCga) {
                                $em->remove($agaCga);
                            }
                            $em->flush();
                        }
                    }


                    //***************ENREGISTREMENT LOG***************\\

                    if ($newDossier->getAccuseCreation() >= 1) {

                        $utilisateur = $this->getUser();

                        if ($newDossier->getRegimeFiscal() != $regimeFiscal) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(2);

                            $oldVal = "";
                            if (!is_null($newDossier->getRegimeFiscal())) {
                                $oldVal = $newDossier->getRegimeFiscal()->getLibelle();
                            }

                            $newVal = "";
                            if (!is_null($regimeFiscal)) {
                                $newVal = $regimeFiscal->getLibelle();
                            }

                            $log->setChamp('Régime Fiscal');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getRegimeImposition() != $regimeImposition) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(2);

                            $oldVal = "";
                            if (!is_null($newDossier->getRegimeImposition())) {
                                $oldVal = $newDossier->getRegimeImposition()->getLibelle();
                            }

                            $newVal = "";
                            if (!is_null($regimeImposition)) {
                                $newVal = $regimeImposition->getLibelle();
                            }

                            $log->setChamp('Régime Imposition');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getFormeActivite() != $formeActivite) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(2);

                            $oldVal = "";
                            if (!is_null($newDossier->getFormeActivite())) {
                                $oldVal = $newDossier->getFormeActivite()->getLibelle();
                            }

                            $newVal = "";
                            if (!is_null($formeActivite)) {
                                $newVal = $formeActivite->getLibelle();
                            }

                            $log->setChamp('Forme activité');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getNatureActivite() != $natureActivite) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(2);

                            $oldVal = "";
                            if (!is_null($newDossier->getNatureActivite())) {
                                $oldVal = $newDossier->getNatureActivite()->getLibelle();
                            }

                            $newVal = "";
                            if (!is_null($natureActivite)) {
                                $newVal = $natureActivite->getLibelle();
                            }

                            $log->setChamp('Nature activité');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getModeVente() != $modeVente) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(2);

                            $oldVal = "";
                            if (!is_null($newDossier->getModeVente())) {
                                $oldVal = $newDossier->getModeVente()->getLibelle();
                            }

                            $newVal = "";
                            if (!is_null($modeVente)) {
                                $newVal = $modeVente->getLibelle();
                            }

                            $log->setChamp('Type de vente');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getRegimeTva() != $tvaRegime) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(2);

                            $oldVal = "";
                            if (!is_null($newDossier->getRegimeTva())) {
                                $oldVal = $newDossier->getRegimeTva()->getLibelle();
                            }

                            $newVal = "";
                            if (!is_null($tvaRegime)) {
                                $newVal = $tvaRegime->getLibelle();
                            }

                            $log->setChamp('TVA Régime');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getTaxeSalaire() != $taxeSalaire) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(2);

                            $oldVal = "";
                            switch ($newDossier->getTaxeSalaire()) {
                                case 1:
                                    $oldVal = "Oui";
                                    break;
                                case 0:
                                    $oldVal = "Non";
                                    break;
                            }

                            $newVal = "";
                            switch ($taxeSalaire) {
                                case 1:
                                    $newVal = "Oui";
                                    break;
                                case 0:
                                    $newVal = "Non";
                                    break;
                            }


                            $log->setChamp('Taxe sur Salaire');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getTvaMode() != $tvaMode) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(2);

                            $oldVal = "";
                            switch ($newDossier->getTvaMode()) {
                                case 0:
                                    $oldVal = "Accomptes semestriels";
                                    break;
                                case 1:
                                    $oldVal = "Accomptes trimestriels";
                                    break;
                                case 2:
                                    $oldVal = "Paiement mensuels";
                                    break;
                                case 3:
                                    $oldVal = "Paiement trimestriels";
                                    break;
                            }

                            $newVal = "";
                            switch ($tvaMode) {
                                case 0:
                                    $newVal = "Accomptes semestriels";
                                    break;
                                case 1:
                                    $newVal = "Accomptes trimestriels";
                                    break;
                                case 2:
                                    $newVal = "Paiement mensuels";
                                    break;
                                case 3:
                                    $newVal = "Paiement trimestriels";
                                    break;
                            }


                            $log->setChamp('TVA paiements');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getTvaDate() != $dateTva) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(2);

                            $oldVal = "";
                            if(!is_null($newDossier->getTvaDate())){

                                if($newDossier->getTvaDate() == 55){
                                    $oldVal = '5ème jour du 5ème mois';
                                }
                                else {
                                    $oldVal = $newDossier->getTvaDate();
                                }
                            }

                            $newVal = "";
                            if(!is_null($dateTva)){

                                if($dateTva == 55){
                                    $newVal = '5ème jour du 5ème mois';
                                }
                                else {
                                    $newVal = $dateTva;
                                }
                            }

                            $log->setChamp('Date declaration');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getTaxeSalaire() != $taxeSalaire) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(2);

                            $oldVal = "";
                            switch ($newDossier->getTaxeSalaire()) {
                                case 1:
                                    $oldVal = "Encaissement";
                                    break;
                                case 0:
                                    $oldVal = "Débit";
                                    break;
                                case 2:
                                    $oldVal = "Mixte";
                                    break;
                            }

                            $newVal = "";
                            switch ($taxeSalaire) {
                                case 1:
                                    $newVal = "Encaissement";
                                    break;
                                case 0:
                                    $newVal = "Débit";
                                    break;
                                case 2:
                                    $newVal = "Mixte";
                                    break;
                            }


                            $log->setChamp('Tva fait générateur');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }


                    }

                    //***************FIN LOG***************\\


                    //caracteristique dossier
                    $newDossier->setRegimeFiscal($regimeFiscal);
                    $newDossier->setRegimeImposition($regimeImposition);
                    $newDossier->setNatureActivite($natureActivite);
                    $newDossier->setFormeActivite2($formeActivite);
                    $newDossier->setProfessionLiberale($professionLiberale);
                    $newDossier->setModeVente($modeVente);
                    $newDossier->setRegimeTva($tvaRegime);
                    $newDossier->setTaxeSalaire($taxeSalaire);
                    $newDossier->setTvaMode($tvaMode);
                    $newDossier->setTvaDate($dateTva);
                    $newDossier->setTvaFaitGenerateur($tvaFaitGenerateur);

                    $em->persist($newDossier);
                    $em->flush();


                    //2: mise à jour
                    return new Response(2);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            }


        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function editInfoPerdosTvaTauxAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            //Nouveau dossier
            if ($idDossier == 0) {
                $res = array('estInsere' => -1, 'message' => 'Dossier');
                return new JsonResponse($res);
            } //Mise à jour dossier
            else {

                try {

                    $newDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($idDossier);

                    $tvaTaux = $post->get('tvaTaux');

                    if (count($tvaTaux) > 0) {
                        $tvaTauxDossier = $em->getRepository('AppBundle:TvaTauxDossier')
                            ->findBy(array('dossier' => $idDossier));

                        /** @var  $tvaTauxDoss TvaTauxDossier */
                        foreach ($tvaTauxDossier as $tvaTauxDoss) {
                            $em->remove($tvaTauxDoss);
                        }

                        foreach ($tvaTaux as $taux) {

                            if ($taux != '') {

                                $newTvaTauxDossier = $this->getDoctrine()
                                    ->getRepository('AppBundle:TvaTaux')
                                    ->find(intval($taux));

                                $tvaTauxDossier = new TvaTauxDossier();
                                $tvaTauxDossier->setDossier($newDossier);
                                $tvaTauxDossier->setTvaTaux($newTvaTauxDossier);

                                $em->persist($tvaTauxDossier);
                            }
                        }
                    } else {
                        $res = array('estInsere' => 0, 'message' => 'TVA Taux');
                        return new JsonResponse($res);
                    }

                    $em->flush();

                    //2: mise à jour
                    return new Response(2);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            }
        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editInfoPerdosDocComptableFiscAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $idDossier = Boost::deboost($post->get('dossierId'), $this);


            //Nouveau dossier
            if ($idDossier == 0) {
                $res = array('estInsere' => -1, 'message' => 'Dossier');
                return new JsonResponse($res);
            } //Mise à jour dossier
            else {

                try {

                    $newDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($idDossier);

                    $comptaSurServeur = $post->get('comptaSurServeur');


                    $balanceN1 = null;
                    $grandLivre = null;
                    $journauxN1 = null;
                    $rapprochementBanqueN1 = null;
                    $etatImmobilisation = null;
                    $liasseN1 = null;
                    $tvaDerniereCa3 = null;

//                    if ($comptaSurServeur == '') {
//                        $res = array('estInsere' => 0, 'message' => "Mode d'obtention");
//                        return new JsonResponse($res);
//                    }
                    //Accès non autorisé => tsy grisé
//                    else

                    if ($comptaSurServeur == 0 || $comptaSurServeur == 4) {

                        $balanceN1 = $post->get('balanceN1');
                        if ($balanceN1 == '') {
                            $balanceN1 = null;
//                            $res = array('estInsere' => 0, 'message' => 'Balance N-1');
                        }

                        $grandLivre = $post->get('grandLivre');
                        if ($grandLivre == '') {
                            $grandLivre = null;
//                            $res = array('estInsere' => 0, 'message' => 'Grand Livre');
//                            return new JsonResponse($res);
                        }

                        $rapprochementBanqueN1 = $post->get('rapprochementBanqueN1');
                        if ($rapprochementBanqueN1 == '') {
                            $rapprochementBanqueN1 = null;
                        }

                        $etatImmobilisation = $post->get('etatImmobilisation');
                        if ($etatImmobilisation == '') {
                            $etatImmobilisation = null;
                        }

                        $liasseN1 = $post->get('liasseN1');
                        if ($liasseN1 == '') {
                            $liasseN1 = null;
                        }

                        $tvaDerniereCa3 = $post->get('tvaDerniereCa3');
                        if ($tvaDerniereCa3 == '') {
                            $tvaDerniereCa3 = null;
                        }
                    }

                    //Dossier dejà traité
                    if ($comptaSurServeur == 3) {
                        $newDossier->setStatut(null);
                        $newDossier->setKbis(null);
                        $newDossier->setBaux(null);
                        $newDossier->setAssurance(null);
                        $newDossier->setAutre(null);
                        $newDossier->setEmprunt(null);
                        $newDossier->setLeasing(null);
                    }


                    //******************ENREGISTREMENT LOG******************\\


                    if ($newDossier->getAccuseCreation() >= 1) {
                        $utilisateur = $this->getUser();

                        if ($newDossier->getComptaSurServeur() != $comptaSurServeur) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(3);

                            $oldVal = "";
                            switch ($newDossier->getComptaSurServeur()) {
                                case 1:
                                    $oldVal = "Accès au serveur";
                                    break;
                                case 4:
                                    $oldVal = "Accès au serveur, nouveau dossier à créer";
                                    break;
                                case 2:
                                    $oldVal = "Echange d'archives electroniques";
                                    break;
                                case 0:
                                    $oldVal = "Envoi de documents excel ou PDF";
                                    break;
                                case 3:
                                    $oldVal = "Dossier dejà traité";
                                    break;
                            }


                            $newVal = "";
                            switch ($comptaSurServeur) {
                                case 1:
                                    $newVal = "Accès au serveur";
                                    break;
                                case 4:
                                    $newVal = "Accès au serveur, nouveau dossier à créer";
                                    break;
                                case 2:
                                    $newVal = "Echange d'archives electroniques";
                                    break;
                                case 0:
                                    $newVal = "Envoi de documents excel ou PDF";
                                    break;
                                case 3:
                                    $newVal = "Dossier dejà traité";
                                    break;
                            }

                            $log->setChamp('Mode d\'obtention');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getBalanceN1() != $balanceN1) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(3);

                            $oldVal = "";
                            switch ($newDossier->getBalanceN1()) {
                                case 1:
                                    $oldVal = "A envoyer";
                                    break;
                                case 0:
                                    $oldVal = "Ne pas envoyer";
                                    break;

                            }


                            $newVal = "";
                            switch ($balanceN1) {
                                case 1:
                                    $newVal = "A envoyer";
                                    break;
                                case 0:
                                    $newVal = "Ne pas envoyer";
                                    break;
                            }

                            $log->setChamp('Balance N-1');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getGrandLivre() != $grandLivre) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(3);

                            $oldVal = "";
                            switch ($newDossier->getGrandLivre()) {
                                case 1:
                                    $oldVal = "A envoyer";
                                    break;
                                case 0:
                                    $oldVal = "Ne pas envoyer";
                                    break;

                            }


                            $newVal = "";
                            switch ($grandLivre) {
                                case 1:
                                    $newVal = "A envoyer";
                                    break;
                                case 0:
                                    $newVal = "Ne pas envoyer";
                                    break;
                            }

                            $log->setChamp('Grand livre');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getDernierRapprochementBanqueN1() != $rapprochementBanqueN1) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(3);

                            $oldVal = "";
                            switch ($newDossier->getDernierRapprochementBanqueN1()) {
                                case 1:
                                    $oldVal = "A envoyer";
                                    break;
                                case 0:
                                    $oldVal = "Ne pas envoyer";
                                    break;

                            }


                            $newVal = "";
                            switch ($rapprochementBanqueN1) {
                                case 1:
                                    $newVal = "A envoyer";
                                    break;
                                case 0:
                                    $newVal = "Ne pas envoyer";
                                    break;
                            }

                            $log->setChamp('Dernier rappro N-1');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getEtatImmobilisationN1() != $etatImmobilisation) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(3);

                            $oldVal = "";
                            switch ($newDossier->getEtatImmobilisationN1()) {
                                case 1:
                                    $oldVal = "A envoyer";
                                    break;
                                case 0:
                                    $oldVal = "Ne pas envoyer";
                                    break;

                            }


                            $newVal = "";
                            switch ($etatImmobilisation) {
                                case 1:
                                    $newVal = "A envoyer";
                                    break;
                                case 0:
                                    $newVal = "Ne pas envoyer";
                                    break;
                            }

                            $log->setChamp('Etat des immobilisations');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getLiasseFiscaleN1() != $liasseN1) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(3);

                            $oldVal = "";
                            switch ($newDossier->getLiasseFiscaleN1()) {
                                case 1:
                                    $oldVal = "A envoyer";
                                    break;
                                case 0:
                                    $oldVal = "Ne pas envoyer";
                                    break;

                            }


                            $newVal = "";
                            switch ($liasseN1) {
                                case 1:
                                    $newVal = "A envoyer";
                                    break;
                                case 0:
                                    $newVal = "Ne pas envoyer";
                                    break;
                            }

                            $log->setChamp('Liasse fiscale N-1');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getTvaDerniereCa3() != $tvaDerniereCa3) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(3);

                            $oldVal = "";
                            switch ($newDossier->getTvaDerniereCa3()) {
                                case 1:
                                    $oldVal = "A envoyer";
                                    break;
                                case 0:
                                    $oldVal = "Ne pas envoyer";
                                    break;
                            }

                            $newVal = "";
                            switch ($tvaDerniereCa3) {
                                case 1:
                                    $newVal = "A envoyer";
                                    break;
                                case 0:
                                    $newVal = "Ne pas envoyer";
                                    break;
                            }

                            $log->setChamp('Dernière CA3');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }
                    }

                    //******************FIN LOG******************\\

                    //Information comptable et fiscale
                    $newDossier->setComptaSurServeur($comptaSurServeur);
                    $newDossier->setBalanceN1($balanceN1);
                    $newDossier->setGrandLivre($grandLivre);
                    $newDossier->setDernierRapprochementBanqueN1($rapprochementBanqueN1);
                    $newDossier->setEtatImmobilisationN1($etatImmobilisation);
                    $newDossier->setLiasseFiscaleN1($liasseN1);
                    $newDossier->setTvaDerniereCa3($tvaDerniereCa3);

                    $em->persist($newDossier);
                    $em->flush();

                    //2: mise à jour
                    return new Response(2);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            }
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    public function editInfoPerdosDocJuridiqueAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;
            $em = $this->getDoctrine()->getEntityManager();

            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            //Nouveau dossier
            if ($idDossier == 0) {
                $res = array('estInsere' => -1, 'message' => 'Dossier');
                return new JsonResponse($res);
            } //Mise à jour dossier
            else {

                try {

                    $newDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($idDossier);

                    $statut = $post->get('statut');
                    $kbis = $post->get('kbis');


                    if ($newDossier->getFormeJuridique() != null) {

                        //Entreprise individuelle -> null ny statut
                        if ($newDossier->getFormeJuridique()->getCode() == 'CODE_ENTREPRISE_INDIVIDUELLE' || $newDossier->getFormeJuridique()->getCode() == 'CODE_INDIVIDUELLE') {
                            $statut = null;
                        } elseif ($statut == '') {
//                        $res = array('estInsere' => 0, 'message' => 'Statut');
//                        return new JsonResponse($res);
                            $statut = null;

                        }
                    } elseif ($statut == '') {
//                        $res = array('estInsere' => 0, 'message' => 'Statut');
//                        return new JsonResponse($res);
                        $statut = null;

                    }

                    if ($newDossier->getFormeJuridique() != null) {

                        //Association -> null ny kbis
                        if ($newDossier->getFormeJuridique()->getId() == 55) {
                            if($kbis != 2)
                                $kbis = null;
                        } elseif ($kbis == '') {
//                        $res = array('estInsere' => 0, 'message' => 'KBis');
//                        return new JsonResponse($res);
                            $kbis = null;
                        }
                    } elseif ($kbis == '') {
//                        $res = array('estInsere' => 0, 'message' => 'KBis');
//                        return new JsonResponse($res);
                        $kbis = null;
                    }

                    $emprunt = $post->get('emprunt');
                    $leasing = $post->get('leasing');

                    //Rehefa prestation = Bilan izay vao obligatoire ny emprut & leasing
                    if ($newDossier->getTypePrestation() == 1) {
                        if ($emprunt == '') {
//                            $res = array('estInsere' => 0, 'message' => 'Emprunt');
//                            return new JsonResponse($res);
                            $emprunt = null;
                        }
//
                        if ($leasing == '') {
//                            $res = array('estInsere' => 0, 'message' => 'Leasing');
//                            return new JsonResponse($res);
                            $emprunt = null;
                        }
                    } else if ($newDossier->getTypePrestation() == 0) {
                        if ($emprunt == '') {
                            $emprunt = null;
                        }

                        if ($leasing == '') {
                            $leasing = null;
                        }
                    }

                    $baux = $post->get('baux');
                    if ($baux == '') {
                        $baux = null;
                    }

                    $assurance = $post->get('assurance');
                    if ($assurance == '') {
                        $assurance = null;
                    }

                    $autre = $post->get('autre');
                    if ($autre == '') {
                        $autre = null;
                    }


                    //********************ENREGISTREMENT LOG********************\\

                    if ($newDossier->getAccuseCreation() >= 1) {

                        $utilisateur = $this->getUser();

                        if ($newDossier->getStatut() != $statut) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(5);

                            $oldVal = '';
                            switch ($newDossier->getStatut()) {
                                case 1:
                                    $oldVal = 'A envoyer';
                                    break;

                                case 0:
                                    $oldVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $oldVal = 'Non applicable';
                                    break;
                            }

                            $newVal = '';
                            switch ($statut) {
                                case 1:
                                    $newVal = 'A envoyer';
                                    break;

                                case 0:
                                    $newVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $newVal = 'Non applicable';
                                    break;
                            }

                            $log->setChamp('Statuts');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getKbis() != $kbis) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(5);

                            $oldVal = '';
                            switch ($newDossier->getKbis()) {
                                case 1:
                                    $oldVal = 'A envoyer';
                                    break;

                                case 0:
                                    $oldVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $oldVal = 'Non applicable';
                                    break;
                            }

                            $newVal = '';
                            switch ($kbis) {
                                case 1:
                                    $newVal = 'A envoyer';
                                    break;

                                case 0:
                                    $newVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $newVal = 'Non applicable';
                                    break;
                            }

                            $log->setChamp('KBis');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getBaux() != $baux) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(5);

                            $oldVal = '';
                            switch ($newDossier->getBaux()) {
                                case 1:
                                    $oldVal = 'A envoyer';
                                    break;

                                case 0:
                                    $oldVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $oldVal = 'Non applicable';
                                    break;
                            }

                            $newVal = '';
                            switch ($baux) {
                                case 1:
                                    $newVal = 'A envoyer';
                                    break;

                                case 0:
                                    $newVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $newVal = 'Non applicable';
                                    break;
                            }

                            $log->setChamp('Baux');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getAssurance() != $assurance) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(5);

                            $oldVal = '';
                            switch ($newDossier->getAssurance()) {
                                case 1:
                                    $oldVal = 'A envoyer';
                                    break;

                                case 0:
                                    $oldVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $oldVal = 'Non applicable';
                                    break;
                            }

                            $newVal = '';
                            switch ($assurance) {
                                case 1:
                                    $newVal = 'A envoyer';
                                    break;

                                case 0:
                                    $newVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $newVal = 'Non applicable';
                                    break;
                            }

                            $log->setChamp('Assurance');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getAutre() != $autre) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(5);

                            $oldVal = '';
                            switch ($newDossier->getAutre()) {
                                case 1:
                                    $oldVal = 'A envoyer';
                                    break;

                                case 0:
                                    $oldVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $oldVal = 'Non applicable';
                                    break;
                            }

                            $newVal = '';
                            switch ($kbis) {
                                case 1:
                                    $newVal = 'A envoyer';
                                    break;

                                case 0:
                                    $newVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $newVal = 'Non applicable';
                                    break;
                            }

                            $log->setChamp('Autres');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getEmprunt() != $emprunt) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(5);

                            $oldVal = '';
                            switch ($newDossier->getEmprunt()) {
                                case 1:
                                    $oldVal = 'A envoyer';
                                    break;

                                case 0:
                                    $oldVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $oldVal = 'Non applicable';
                                    break;
                            }

                            $newVal = '';
                            switch ($emprunt) {
                                case 1:
                                    $newVal = 'A envoyer';
                                    break;

                                case 0:
                                    $newVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $newVal = 'Non applicable';
                                    break;
                            }

                            $log->setChamp('Emprunt');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getLeasing() != $leasing) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(5);

                            $oldVal = '';
                            switch ($newDossier->getLeasing()) {
                                case 1:
                                    $oldVal = 'A envoyer';
                                    break;

                                case 0:
                                    $oldVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $oldVal = 'Non applicable';
                                    break;
                            }

                            $newVal = '';
                            switch ($leasing) {
                                case 1:
                                    $newVal = 'A envoyer';
                                    break;

                                case 0:
                                    $newVal = 'Ne pas envoyer';
                                    break;

                                case 2:
                                    $newVal = 'Non applicable';
                                    break;
                            }

                            $log->setChamp('Leasing');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                    }
                    //******************FIN LOG******************\\


//                //Documents juridiques
                    $newDossier->setStatut($statut);
                    $newDossier->setKbis($kbis);
                    $newDossier->setBaux($baux);
                    $newDossier->setAssurance($assurance);
                    $newDossier->setAutre($autre);
                    $newDossier->setEmprunt($emprunt);
                    $newDossier->setLeasing($leasing);

                    $em->persist($newDossier);
                    $em->flush();

                    //2: mise à jour
                    return new Response(2);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            }
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }

    }

    public function editInfoPerdosIdentificationAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $nomDossier = $post->get('nomDossier');

            if ($nomDossier == '') {
                $res = array('estInsere' => 0, 'message' => 'Nom dossier');
                return new JsonResponse($res);
            }

            $nomDossier = strtoupper($nomDossier);


            $nomDossier = trim($nomDossier, ' ');
            $nomDossier = preg_replace("/[^[:alnum:]-_]/", '_', $nomDossier);


            $raisonSocial = $post->get('raisonSocial');
//            if ($raisonSocial == '') {
//                $res = array('estInsere' => 0, 'message' => 'Raison Social');
//                return new JsonResponse($res);
//            }

            $sirenSiret = $post->get('sirenSiret');
//            if ($sirenSiret == '') {
//                $res = array('estInsere' => 0, 'message' => 'SIRET/SIREN');
//                return new JsonResponse($res);
//            }

            $activiteComCat3Id = $post->get('activiteComCat3');
//            if ($activiteComCat3Id == '') {
//                $res = array('estInsere' => 0, 'message' => 'Code APE');
//                return new JsonResponse($res);
//            }

            $activiteComCat3 = null;

            if ($activiteComCat3Id != '') {
                $activiteComCat3 = $this->getDoctrine()
                    ->getRepository('AppBundle:ActiviteComCat3')
                    ->find($activiteComCat3Id);
            }

            $formeJuridiqueId = $post->get('formeJuridique');
//            if ($formeJuridiqueId == '') {
//                $res = array('estInsere' => 0, 'message' => 'Forme Juridique');
//                return new JsonResponse($res);
//            }

            $formeJuridique = null;

            if ($formeJuridiqueId != '') {
                $formeJuridique = $this->getDoctrine()
                    ->getRepository('AppBundle:FormeJuridique')
                    ->find($formeJuridiqueId);
            }

            $newDateDebutActivite = null;
            $dateDebutActivite = $post->get('dateDebutActivite');

            if ($dateDebutActivite != '') {

                $dateDebutActivite = str_replace("-", "/", $dateDebutActivite);

                $date_array = explode("/", $dateDebutActivite);
                try {
                    $var_day = $date_array[0];
                    $var_month = $date_array[1];
                    $var_year = $date_array[2];

                    $newDateDebutActivite = "$var_year-$var_month-$var_day";
                } catch (Exception $e) {

                }

            } else {
//                $res = array('estInsere' => 0, 'message' => 'Date Début Actvitié');
//                return new JsonResponse($res);
                $newDateDebutActivite = null;
            }

            $newDateCloture = null;
            $dateCloture = $post->get('dateCloture');
            if ($dateCloture != '') {

                $dateCloture = str_replace("-", "/", $dateCloture);

                $date_array = explode("/", $dateCloture);
                try {
                    $var_day = $date_array[0];
                    $var_month = $date_array[1];
                    $var_year = $date_array[2];

                    $newDateCloture = "$var_year-$var_month-$var_day";
                } catch (Exception $e) {

                }

            } else {
//                $res = array('estInsere' => 0, 'message' => 'Date première Clôture');
//                return new JsonResponse($res);
                $newDateCloture = null;
            }

            $cloture = $post->get('cloture');
            if ($cloture == '') {
//                $res = array('estInsere' => 0, 'message' => 'Date Clôture');
//                return new JsonResponse($res);
                $cloture = null;
            }

            $enseigne = $post->get('enseigne');
            if ($enseigne == '') {
                $enseigne = null;
            }

//            $trancheEffectif = $post->get('trancheEffectif');
//            if($trancheEffectif == ''){
//                $trancheEffectif = null;
//            }

            $numRue = $post->get('numRue');
            if ($numRue == '') {
                $numRue = null;
            }

            $codePostal = $post->get('codePostal');
            if ($codePostal == '') {
                $codePostal = null;
            }

            $pays = $post->get('pays');
            if ($pays == '') {
                $pays = null;
            }

            $ville = $post->get('ville');
            if ($ville == '') {
                $ville = null;
            }


//            $typePrestation = $post->get('typePrestation');
//            if ($typePrestation == '') {
//                $res = array('estInsere' => 0, 'message' => 'Préstation');
//                return new JsonResponse($res);
//            }


            //Mandataire
            $mandataireId = $post->get('mandataire');
            $mandataire = null;
            if ($mandataireId != '') {
                $mandataire = $this->getDoctrine()
                    ->getRepository('AppBundle:Mandataire')
                    ->find($mandataireId);
            }
            $nomMandataire = $post->get('nomMandataire');

            $nom = '';
            $prenom = '';

//            $nomPrenomList = array();
//
//            $mandataireList = array();

            if ($nomMandataire != '') {
//              TEST MANDATAIRE > 1
//                $nomPrenomList = explode(".", $nomMandataire);
//
//                foreach ($nomPrenomList as $nomMandataire) {
//                    $nomPrenom = explode(";", $nomMandataire);
//                    $nom = trim($nomPrenom[0]);
//                    $prenom = trim($nomPrenom[1]);
//
//                    $mandataireList[] = array('nom'=>$nom, 'prenom'=>$prenom);
//                }

                $nomPrenom = explode(";", $nomMandataire);
                $nom = trim($nomPrenom[0]);
                $prenom = trim($nomPrenom[1]);
            }


            $formeJuridiqueId = $post->get('formeJuridique');
//            if ($formeJuridiqueId == '') {
//                $res = array('estInsere' => 0, 'message' => 'Forme Juridique');
//                return new JsonResponse($res);
//            }

            $trancheEffectifId = $post->get('trancheEffectif');

            $trancheEffectif = null;

            if ($trancheEffectifId != '') {
                $trancheEffectif = $this->getDoctrine()
                    ->getRepository('AppBundle:TrancheEffectif')
                    ->find($trancheEffectifId);
            }

            $cegid = $post->get('cegid');
            if ($cegid == '') {
                $cegid = null;
            }


            $idDossier = Boost::deboost($post->get('dossierId'), $this);
            $idSite = Boost::deboost($post->get('site'), $this);

            //Nouveau dossier
            if ($idDossier == 0) {



                try {
                    $newDossier = new Dossier();

                    $site = $this->getDoctrine()
                        ->getRepository('AppBundle:Site')
                        ->find($idSite);

                    $newDossier->setSite($site);

                    //Identification dossier
                    $newDossier->setNom($nomDossier);
                    $newDossier->setRsSte($raisonSocial);
                    $newDossier->setEntreprise($raisonSocial);
                    $newDossier->setSirenSte($sirenSiret);

                    $newDossier->setFormeJuridique($formeJuridique);
                    $newDossier->setActiviteComCat3($activiteComCat3);

                    $newDossier->setEnseigne($enseigne);
                    $newDossier->setTrancheEffectif($trancheEffectif);
                    $newDossier->setNumRue($numRue);
                    $newDossier->setCodePostal($codePostal);
                    $newDossier->setPays($pays);
                    $newDossier->setVille($ville);

                    $newDossier->setCegid($cegid);

                    $newDossier->setActive(0);

//                    $newDossier->setTypePrestation($typePrestation);

                    if ($newDateDebutActivite != '') {
                        $newDossier->setDebutActivite(new \DateTime($newDateDebutActivite));
                    } else {
                        $newDossier->setDebutActivite(null);
                    }

                    if ($newDateCloture != '') {
                        $newDossier->setDateCloture(new \DateTime($newDateCloture));
                    } else {
                        $newDossier->setDateCloture(null);
                    }

                    if ($cloture != '') {
                        $newDossier->setCloture($cloture);
                    } else {
                        $newDossier->setCloture(12);
                    }

                    $em->persist($newDossier);
                    $em->flush();



                    $newDossierId = $newDossier->getId();
                    $newDossierId = Boost::boost($newDossierId);

                    $responsableCsd = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findOneBy(array('dossier' => $newDossier, 'typeResponsable' => 0));

                    if ($responsableCsd != null) {
                        $responsableCsd->setNom($nom);
                        $responsableCsd->setPrenom($prenom);
                        $responsableCsd->setMandataire($mandataire);
                    } else {
                        $responsableCsd = new ResponsableCsd();

                        $responsableCsd->setDossier($newDossier);
                        $responsableCsd->setTypeResponsable(0);
                        $responsableCsd->setNom($nom);
                        $responsableCsd->setPrenom($prenom);
                        $responsableCsd->setMandataire($mandataire);
                    }

                    $em->persist($responsableCsd);
                    $em->flush();



                    #LOG CREATION

                    $logInfoPerdos = new LogInfoperdos();
                    $logInfoPerdos->setDate(new \DateTime());
                    $logInfoPerdos->setDossier($newDossier);
                    $logInfoPerdos->setUtilisateur($this->getUser());
                    $logInfoPerdos->setBloc(1);
                    $logInfoPerdos->setTab(1);
                    $logInfoPerdos->setValeurAncien("");
                    $logInfoPerdos->setValeurNouveau("");
                    $logInfoPerdos->setChamp("Création");
                    $logInfoPerdos->setMail(2);

                    $em->persist($logInfoPerdos);
                    $em->flush();

                    #END LOG


//                    $res = array('estInsere'=> 1 , 'idDossier'=>$newDossierId);
                    $res = array('estInsere' => 1, 'idDossier' => $newDossierId, 'id' => $newDossier->getId());


                    //1:insertion
                    return new JsonResponse($res);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            } //Mise à jour dossier
            else {

                try {
                    $newDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($idDossier);

                    $utilisateur = $this->getUser();


                    //*****************ENREGISTREMENT LOG*****************\\

                    if ($newDossier->getAccuseCreation() >= 1) {

                        if ($newDossier->getNom() != $nomDossier) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);

                            $log->setChamp('Nom dossier');
                            $log->setValeurAncien($newDossier->getNom());
                            $log->setValeurNouveau($nomDossier);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getRsSte() != $raisonSocial) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);

                            $log->setChamp('Raison Social');
                            $log->setValeurAncien($newDossier->getRsSte());
                            $log->setValeurNouveau($raisonSocial);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getSirenSte() != $sirenSiret) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);

                            $log->setChamp('SIREN');
                            $log->setValeurAncien($newDossier->getSirenSte());
                            $log->setValeurNouveau($sirenSiret);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getFormeJuridique() != $formeJuridique) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);

                            $log->setChamp('Forme');
                            $oldVal = '';
                            if (!is_null($newDossier->getFormeJuridique())) {
                                $oldVal = $newDossier->getFormeJuridique()
                                    ->getLibelle();
                            }

                            $newVal = '';
                            if (!is_null($formeJuridique)) {
                                $newVal = $formeJuridique->getLibelle();
                            }

                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getActiviteComCat3() != $activiteComCat3) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);

                            $oldVal = '';
                            if (!is_null($newDossier->getActiviteComCat3())) {
                                $oldVal = $newDossier->getActiviteComCat3()->getLibelle();
                            }

                            $newVal = '';
                            if (!is_null($activiteComCat3)) {
                                $newVal = $activiteComCat3->getLibelle();
                            }

                            $log->setChamp('Code APE');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getEnseigne() != $enseigne) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);


                            $log->setChamp('Enseigne');
                            $log->setValeurAncien($newDossier->getEnseigne());
                            $log->setValeurNouveau($$enseigne);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getTrancheEffectif() != $trancheEffectif) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);


                            $oldVal = '';
                            if (!is_null($newDossier->getTrancheEffectif())) {
                                $oldVal = $newDossier->getTrancheEffectif()
                                    ->getLibelle();
                            }

                            $newVal = '';
                            if (!is_null($trancheEffectif)) {
                                $newVal = $trancheEffectif->getLibelle();
                            }

                            $log->setChamp('Tranche effectif');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getEnseigne() != $enseigne) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);


                            $log->setChamp('Num et rue');
                            $log->setValeurAncien($newDossier->getNumRue());
                            $log->setValeurNouveau($numRue);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getCodePostal() != $codePostal) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);


                            $log->setChamp('Code postal');
                            $log->setValeurAncien($newDossier->getCodePostal());
                            $log->setValeurNouveau($codePostal);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getPays() != $pays) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);


                            $log->setChamp('Pays');
                            $log->setValeurAncien($newDossier->getPays());
                            $log->setValeurNouveau($pays);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getVille() != $ville) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);


                            $log->setChamp('Ville');
                            $log->setValeurAncien($newDossier->getVille());
                            $log->setValeurNouveau($ville);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getCegid() != $cegid) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);


                            $log->setChamp('Id Dossier');
                            $log->setValeurAncien($newDossier->getCegid());
                            $log->setValeurNouveau($cegid);

                            $em->persist($log);
                            $em->flush();
                        }

                        if ($newDossier->getCloture() != $cloture) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(1);
                            $log->setBloc(1);


                            $log->setChamp('Date clôture');
                            $log->setValeurAncien($newDossier->getCloture());
                            $log->setValeurNouveau($cloture);

                            $em->persist($log);
                            $em->flush();
                        }

//                        if (!is_null($newDateDebutActivite) && !is_null($newDossier->getDebutActivite())) {
//                            if ($newDossier->getDebutActivite() != new \DateTime($newDateDebutActivite)) {
//                                $log = new LogInfoperdos();
//                                $log->setDate(new \DateTime());
//                                $log->setDossier($newDossier);
//                                $log->setUtilisateur($utilisateur);
//                                $log->setTab(1);
//                                $log->setBloc(1);
//
//
//                                $log->setChamp('Date début activité');
//                                $log->setValeurAncien($newDossier->getDebutActivite()->format("d/m/Y"));
//                                $log->setValeurNouveau((new \DateTime($newDateDebutActivite))->format("d/m/Y"));
//
//                                $em->persist($log);
//                                $em->flush();
//                            }
//                        }
//                        else if ($newDateDebutActivite == '' && !is_null($newDossier->getDebutActivite())) {
//                            $log = new LogInfoperdos();
//                            $log->setDate(new \DateTime());
//                            $log->setDossier($newDossier);
//                            $log->setUtilisateur($utilisateur);
//                            $log->setTab(1);
//                            $log->setBloc(1);
//
//
//                            $log->setChamp('Date début activité');
//                            $log->setValeurAncien($newDossier->getDebutActivite()->format("d/m/Y"));
//                            $log->setValeurNouveau('');
//
//                            $em->persist($log);
//                            $em->flush();
//                        }
//                        else if ($newDateDebutActivite != '' && is_null($newDossier->getDebutActivite())) {
//                            $log = new LogInfoperdos();
//                            $log->setDate(new \DateTime());
//                            $log->setDossier($newDossier);
//                            $log->setUtilisateur($utilisateur);
//                            $log->setTab(1);
//                            $log->setBloc(1);
//
//
//                            $log->setChamp('Date début activité');
//                            $log->setValeurAncien('');
//                            $log->setValeurNouveau((new \DateTime($newDateDebutActivite))->format("d/m/Y"));
//
//                            $em->persist($log);
//                            $em->flush();
//                        }

//                        if ($newDateCloture != '' && !is_null($newDossier->getDateCloture())) {
//                            if ($newDossier->getDateCloture() != new \DateTime($newDateCloture)) {
//                                $log = new LogInfoperdos();
//                                $log->setDate(new \DateTime());
//                                $log->setDossier($newDossier);
//                                $log->setUtilisateur($utilisateur);
//                                $log->setTab(1);
//                                $log->setBloc(1);
//
//
//                                $log->setChamp('Date cloture');
//                                $log->setValeurAncien($newDossier->getDateCloture()->format("d/m/Y"));
//                                $log->setValeurNouveau((new \DateTime($newDateCloture))->format("d/m/Y"));
//
//                                $em->persist($log);
//                                $em->flush();
//                            }
//                        }
//                        else if ($newDateCloture == '' && !is_null($newDossier->getDateCloture())) {
//                            $log = new LogInfoperdos();
//                            $log->setDate(new \DateTime());
//                            $log->setDossier($newDossier);
//                            $log->setUtilisateur($utilisateur);
//                            $log->setTab(1);
//                            $log->setBloc(1);
//
//
//                            $log->setChamp('Date cloture');
//                            $log->setValeurAncien($newDossier->getDateCloture()->format("d/m/Y"));
//                            $log->setValeurNouveau('');
//
//                            $em->persist($log);
//                            $em->flush();
//                        } else if ($newDateCloture != '' && is_null($newDossier->getDateCloture())) {
//                            $log = new LogInfoperdos();
//                            $log->setDate(new \DateTime());
//                            $log->setDossier($newDossier);
//                            $log->setUtilisateur($utilisateur);
//                            $log->setTab(1);
//                            $log->setBloc(1);
//
//
//                            $log->setChamp('Date Cloture');
//                            $log->setValeurAncien('');
//                            $log->setValeurNouveau((new \DateTime($newDateCloture))->format("d/m/Y"));
//
//                            $em->persist($log);
//                            $em->flush();
//                        }
                    }

                    //*****************FIN LOG*****************\\

                    $newDossier->setNom($nomDossier);

                    $newDossier->setRsSte($raisonSocial);
                    $newDossier->setEntreprise($raisonSocial);
                    $newDossier->setSirenSte($sirenSiret);
                    $newDossier->setFormeJuridique($formeJuridique);

                    if (!is_null($formeJuridique)) {
                        if ($formeJuridique->getExtension() === 'Auto entreprise') {
                            $regimeTva = $this->getDoctrine()
                                ->getRepository('AppBundle:RegimeTva')
                                ->findOneBy(array('code' => 'CODE_NON_SOUMIS'));

                            $newDossier->setRegimeTva($regimeTva);

                            $tvaTaux = $this->getDoctrine()
                                ->getRepository('AppBundle:TvaTauxDossier')
                                ->findBy(array('dossier' => $newDossier));

                            foreach ($tvaTaux as $tva) {
                                $em->remove($tva);
                            }

                            $newDossier->setTvaDate(null);
                            $newDossier->setTvaMode(null);

                        }

                    }

                    $newDossier->setActiviteComCat3($activiteComCat3);
                    $newDossier->setEnseigne($enseigne);
                    $newDossier->setTrancheEffectif($trancheEffectif);
                    $newDossier->setNumRue($numRue);
                    $newDossier->setCodePostal($codePostal);
                    $newDossier->setPays($pays);
                    $newDossier->setVille($ville);
                    $newDossier->setCegid($cegid);

                    if ($newDateDebutActivite != '') {
                        $newDossier->setDebutActivite(new \DateTime($newDateDebutActivite));
                    } else {
                        $newDossier->setDebutActivite(null);
                    }

                    if ($newDateCloture != '') {
                        $newDossier->setDateCloture(new \DateTime($newDateCloture));
                    } else {
                        $newDossier->setDateCloture(null);
                    }


                    $newDossier->setCloture($cloture);

                    $em->persist($newDossier);
                    $em->flush();

//                    $newDossier->setTypePrestation($typePrestation);

                    $responsableCsd = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findOneBy(array('dossier' => $newDossier, 'typeResponsable' => 0));

                    if ($responsableCsd != null) {
                        if ($newDossier->getAccuseCreation() >= 1) {

                            if ($responsableCsd->getNom() != $nom) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($newDossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(1);
                                $log->setBloc(1);


                                $log->setChamp('Nom mandataire');
                                $log->setValeurAncien($responsableCsd->getNom());
                                $log->setValeurNouveau($nom);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($responsableCsd->getPrenom() != $prenom) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($newDossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(1);
                                $log->setBloc(1);


                                $log->setChamp('Prénom mandataire');
                                $log->setValeurAncien($responsableCsd->getPrenom());
                                $log->setValeurNouveau($prenom);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($responsableCsd->getMandataire() != $mandataire) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($newDossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(1);
                                $log->setBloc(1);


                                $oldVal = "";
                                if (!is_null($responsableCsd->getMandataire())) {
                                    $responsableCsd->getMandataire()->getLibelle();
                                }

                                $newVal = "";
                                if (!is_null($mandataire)) {
                                    $newVal = $mandataire->getLibelle();
                                }

                                $log->setChamp('Type Mandataire');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                        }
                        $responsableCsd->setNom($nom);
                        $responsableCsd->setPrenom($prenom);
                        $responsableCsd->setMandataire($mandataire);


                    } else {


                        if($newDossier->getAccuseCreation() >= 1) {
                            if ($nom != '') {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($newDossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(1);
                                $log->setBloc(1);


                                $log->setChamp('Nom mandataire');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($nom);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prenom != '') {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($newDossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(1);
                                $log->setBloc(1);


                                $log->setChamp('Prénom mandataire');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($prenom);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($mandataire)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($newDossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(1);
                                $log->setBloc(1);


                                $newVal = "";
                                if (!is_null($mandataire)) {
                                    $newVal = $mandataire->getLibelle();
                                }

                                $log->setChamp('Type Mandataire');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }
                        }


                        $responsableCsd = new ResponsableCsd();
                        $responsableCsd->setTypeResponsable(0);
                        $responsableCsd->setDossier($newDossier);
                        $responsableCsd->setNom($nom);
                        $responsableCsd->setPrenom($prenom);
                        $responsableCsd->setMandataire($mandataire);
                    }


                    $em->persist($responsableCsd);
                    $em->flush();

                    $newDossierId = $newDossier->getId();
                    $newDossierId = Boost::boost($newDossierId);
                    //2: mise à jour
                    $res = array('estInsere' => 2, 'idDossier' => $newDossierId, 'id' => $newDossier->getId());


                    return new JsonResponse($res);

                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            }

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editInfoPerdosInformationDossierV2Action(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();


            $val = $post->get('value');
            $field = $post->get('field');

            switch ($field) {

                case 'Nom':
                    $value = strtoupper($val);
                    $value = trim($value, ' ');
                    $value = str_replace(' ', '_', $value);
                    break;

                case 'FormeJuridique':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeJuridique')
                        ->find($val);
                    break;

                case 'ActiviteComCat3':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:ActiviteComCat3')
                        ->find($val);
                    break;

                case 'DebutActivite':
                case 'DateCloture':
//                    $value = str_replace("-", "/", $val);
//
//                    $date_array = explode("/", $val);
//                    try {
//                        $var_day = $date_array[0];
//                        $var_month = $date_array[1];
//                        $var_year = $date_array[2];
//                        $value = new \DateTime($var_year."-".$var_month."-".$var_day);
//                    } catch (Exception $e) {
//                    }

                    $value = \DateTime::createFromFormat("d/m/Y", $val);
                    if (!$value) {
                        throw new \UnexpectedValueException("Could not parse the date: $val");
                    }


                    break;

                case 'Mandataire':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:Mandataire')
                        ->find($val);
                    break;

                case 'RegimeFiscal':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:RegimeFiscal')
                        ->find($val);
                    break;

                case 'RegimeImposition':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:RegimeImposition')
                        ->find($val);
                    break;

                case 'NatureActivite':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:NatureActivite')
                        ->find($val);
                    break;

                case 'FormeActivite2':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeActivite')
                        ->find($val);
                    break;

                case 'ProfessionLiberale':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:ProfessionLiberale')
                        ->find($val);
                    break;

                case 'ModeVente':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:ModeVente')
                        ->find($val);
                    break;

                case 'RegimeTva':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:RegimeTva')
                        ->find($val);
                    break;

                case 'TrancheEffectif':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:TrancheEffectif')
                        ->find($val);
                    break;


                case 'TypePrestation2':
                    $value = $this->getDoctrine()
                        ->getRepository('AppBundle:TypePrestation')
                        ->find($val);
                    break;

                default:
                    $value = $val;

            }

            $idDossier = Boost::deboost($post->get('dossierId'), $this);
            $idSite = Boost::deboost($post->get('site'), $this);

            //Nouveau dossier
            if ($idDossier == 0) {

                try {
                    $newDossier = new Dossier();

                    $site = $this->getDoctrine()
                        ->getRepository('AppBundle:Site')
                        ->find($idSite);


                    switch ($field) {
                        case 'NomPrenom':
                        case 'Mandataire':

                            $responsableCsd = $this->getDoctrine()
                                ->getRepository('AppBundle:ResponsableCsd')
                                ->findOneBy(array('dossier' => $newDossier, 'typeResponsable' => 0));

                            if ($responsableCsd != null) {
                                if ($field == 'NomPrenom') {
                                    $value = explode(";", $val);

                                    $nom = trim($value[0]);
                                    $prenom = trim($value[1]);

                                    $responsableCsd->setNom($nom);
                                    $responsableCsd->setPrenom($prenom);
                                } else {
                                    $responsableCsd->setMandataire($value);
                                    $responsableCsd->setNom('');
                                }

                            } else {
                                $responsableCsd = new ResponsableCsd();
                                $responsableCsd->setDossier($newDossier);
                                $responsableCsd->setTypeResponsable(0);
                                if ($field == 'NomPrenom') {
                                    $value = explode(";", $val);

                                    $nom = trim($value[0]);
                                    $prenom = trim($value[1]);

                                    $responsableCsd->setNom($nom);
                                    $responsableCsd->setPrenom($prenom);
                                } else {
                                    $responsableCsd->setMandataire($value);
                                    $responsableCsd->setNom(' ');
                                }

                            }
                            $em->persist($responsableCsd);
                            break;

                        case 'FormeActivite2':

                            $newDossier->setSite($site);
                            $newDossier->{"set$field"}($value);

                            if (!is_null($value)) {
                                if ($value->getCode() != "CODE_PROFESSION_LIBERALE") {
                                    $newDossier->setProfessionLiberale(null);
                                }
                            }

                            $newDossier->setActive(0);
                            $em->persist($newDossier);

                            break;

                        case 'RegimeTva':

                            $newDossier->setSite($site);
                            $newDossier->{"set$field"}($value);
                            //Non soumis
                            if (!is_null($value)) {
                                if ($value->getCode() === 'CODE_NON_SOUMIS' ||
                                    $value->getCode() === 'CODE_FRANCHISE') {
                                    $newDossier->setTvaMode(null);
                                    $newDossier->setTvaDate(null);
                                } else {
                                    $newDossier->setTaxeSalaire(null);
                                }
                            }

                            $newDossier->setActive(0);
                            $em->persist($newDossier);
                            break;

                        case 'ComptaSurServeur':
                            $newDossier->setSite($site);
                            $newDossier->{"set$field"}($value);
                            //
                            if ($val != 0) {
                                $newDossier->setBalanceN1(null);
                                $newDossier->setGrandLivre(null);
                                $newDossier->setDernierRapprochementBanqueN1(null);
                                $newDossier->setEtatImmobilisationN1(null);
                                $newDossier->setLiasseFiscaleN1(null);
                                $newDossier->setTvaDerniereCa3(null);
                            }

                            $newDossier->setActive(0);
                            $em->persist($newDossier);
                            break;


                        default:

                            $newDossier->setSite($site);
                            $newDossier->{"set$field"}($value);

                            $newDossier->setEntreprise('');


                            $em->persist($newDossier);

                            break;

                    }


                    $em->flush();

                    $newDossierId = $newDossier->getId();
                    $newDossierId = Boost::boost($newDossierId);

                    $res = array('estInsere' => 1, 'idDossier' => $newDossierId, 'id' => $newDossier->getId());


                    //1:insertion
                    return new JsonResponse($res);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            } //Mise à jour dossier
            else {

                try {
                    $newDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($idDossier);

                    switch ($field) {

                        case 'NomPrenom':
                        case 'Mandataire':

                            $responsableCsd = $this->getDoctrine()
                                ->getRepository('AppBundle:ResponsableCsd')
                                ->findOneBy(array('dossier' => $newDossier, 'typeResponsable' => 0));

                            if ($responsableCsd != null) {
                                if ($field == 'NomPrenom') {
                                    $value = explode(";", $val);

                                    $nom = trim($value[0]);
                                    $prenom = trim($value[1]);

                                    $responsableCsd->setNom($nom);
                                    $responsableCsd->setPrenom($prenom);
                                } else {
                                    $responsableCsd->setMandataire($value);
                                    $responsableCsd->setNom('');
                                }

                            } else {
                                $responsableCsd = new ResponsableCsd();
                                $responsableCsd->setDossier($newDossier);
                                $responsableCsd->setTypeResponsable(0);
                                if ($field == 'NomPrenom') {
                                    $value = explode(";", $val);

                                    $nom = trim($value[0]);
                                    $prenom = trim($value[1]);

                                    $responsableCsd->setNom($nom);
                                    $responsableCsd->setPrenom($prenom);
                                } else {
                                    $responsableCsd->setMandataire($value);
                                    $responsableCsd->setNom(' ');
                                }

                            }
                            $em->persist($responsableCsd);
                            break;

                        case 'FormeActivite2':


                            $newDossier->{"set$field"}($value);

                            if (!is_null($value)) {
                                if ($value->getCode() != "CODE_PROFESSION_LIBERALE") {
                                    $newDossier->setProfessionLiberale(null);
                                }
                            }

                            $em->persist($newDossier);

                            break;

                        case 'RegimeTva':


                            $newDossier->{"set$field"}($value);
                            //Non soumis
                            if (!is_null($value)) {
                                if ($value->getCode() === 'CODE_NON_SOUMIS' ||
                                    $value->getCode() === 'CODE_FRANCHISE') {
                                    $newDossier->setTvaMode(null);
                                    $newDossier->setTvaDate(null);

                                    $tvaTauxDossier = $this->getDoctrine()
                                        ->getRepository('AppBundle:TvaTauxDossier')
                                        ->findBy(array('dossier' => $newDossier));

                                    foreach ($tvaTauxDossier as $tvaTauxDoss) {
                                        $em->remove($tvaTauxDoss);
                                    }

                                } else {
                                    $newDossier->setTaxeSalaire(null);
                                }
                            }

                            $em->persist($newDossier);
                            break;

                        case 'ComptaSurServeur':

                            $newDossier->{"set$field"}($value);
                            //Raha envoi de document excel ou pdf
                            if ($val != 0) {
                                $newDossier->setBalanceN1(null);
                                $newDossier->setGrandLivre(null);
                                $newDossier->setDernierRapprochementBanqueN1(null);
                                $newDossier->setEtatImmobilisationN1(null);
                                $newDossier->setLiasseFiscaleN1(null);
                                $newDossier->setTvaDerniereCa3(null);
                            }

                            $em->persist($newDossier);
                            break;

                        case 'TypePrestation':
                            $newDossier->{"set$field"}($value);
                            //Raha tenue propre
                            if ($val == 0) {
                                $prestationFiscal = $this->getDoctrine()
                                    ->getRepository('AppBundle:PrestationFiscale')
                                    ->findOneBy(array('dossier' => $newDossier));

                                if (!is_null($prestationFiscal)) {
                                    $prestationFiscal->setLiasse(null);
                                    $prestationFiscal->setCice(null);
                                }
                            }

                            $em->persist($newDossier);

                            break;

                        case 'FormeJuridique':
                            $newDossier->{"set$field"}($value);

                            if (!is_null($value)) {

                                if ($value->getExtension() == "Auto entreprise") {
                                    $regimeTva = $this->getDoctrine()
                                        ->getRepository('AppBundle:RegimeTva')
                                        ->findOneBy(array('code' => 'CODE_NON_SOUMIS'));

                                    $newDossier->setRegimeTva($regimeTva);

                                    $tvaTaux = $this->getDoctrine()
                                        ->getRepository('AppBundle:TvaTauxDossier')
                                        ->findBy(array('dossier' => $newDossier));

                                    foreach ($tvaTaux as $tva) {
                                        $em->remove($tva);
                                    }

                                    $newDossier->setTvaDate(null);
                                    $newDossier->setTvaMode(null);

                                }

                            }

                            $em->persist($newDossier);

                            break;

                        default:

                            $newDossier->{"set$field"}($value);
//                            $newDossier->setEntreprise('');
                            $em->persist($newDossier);

                            break;

                    }

                    $em->flush();

                    $newDossierId = $newDossier->getId();
                    $newDossierId = Boost::boost($newDossierId);
                    //2: mise à jour
                    $res = array('estInsere' => 2, 'idDossier' => $newDossierId, 'id' => $newDossier->getId());


                    return new JsonResponse($res);

                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            }

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editAgaCgaSirenAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $numRue = $post->get('numRue');
            if ($numRue == '') {
                $numRue = null;
            }

            $codePostal = $post->get('codePostal');
            if ($codePostal == '') {
                $codePostal = null;
            }

            $pays = $post->get('pays');
            if ($pays == '') {
                $pays = null;
            }

            $ville = $post->get('ville');
            if ($ville == '') {
                $ville = null;
            }

            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            if ($idDossier == 0) {

                return new JsonResponse(-1);
            } else {

                try {
                    $newDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($idDossier);

                    $agaCgas = $this->getDoctrine()
                        ->getRepository('AppBundle:AgaCga')
                        ->findBy(array('dossier' => $newDossier));

                    if (count($agaCgas) > 0) {
                        $agaCga = $agaCgas[0];

                        $agaCga->setNumRue($numRue);
                        $agaCga->setCodePostal($codePostal);
                        $agaCga->setVille($ville);
                        $agaCga->setPays($pays);

                        $em->persist($agaCga);
                        $em->flush();

                        return new JsonResponse(2);

                    } else {
                        return new JsonResponse(-2);
                    }

                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            }

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editInfoPerdosInformationDossierSirenAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $raisonSocial = $post->get('raisonSocial');
            $formeJuridiqueId = $post->get('formeJuridiqueId');
            $codeApeId = $post->get('codeApeId');
            $dateDeb = $post->get('dateDeb');

            if ($dateDeb != -'1') {
                $value = str_replace("-", "/", $dateDeb);
                $date_array = explode("/", $value);
                try {
                    $var_day = $date_array[0];
                    $var_month = $date_array[1];
                    $var_year = $date_array[2];
                    $dateDeb = new \DateTime("$var_year-$var_month-$var_day");
                } catch (Exception $e) {
                    $dateDeb = null;
                }
            } else {
                $dateDeb = null;
            }

            if ($raisonSocial == '') {
                $raisonSocial = null;
            }

            $codeApe = $this->getDoctrine()
                ->getRepository('AppBundle:ActiviteComCat3')
                ->find($codeApeId);

            $formeJuridique = $this->getDoctrine()
                ->getRepository('AppBundle:FormeJuridique')
                ->find($formeJuridiqueId);


            $enseigne = $post->get('enseigne');
            if ($enseigne == '') {
                $enseigne = null;
            }

            $trancheEffectif = null;
            $trancheEffectifId = $post->get('trancheEffectif');

            if ($trancheEffectifId == '') {
                $trancheEffectif = $this->getDoctrine()
                    ->getRepository('AppBundle:TrancheEffectif')
                    ->find($trancheEffectifId);
            }

            $numRue = $post->get('numRue');
            if ($numRue == '') {
                $numRue = null;
            }

            $codePostal = $post->get('codePostal');
            if ($codePostal == '') {
                $codePostal = null;
            }

            $pays = $post->get('pays');
            if ($pays == '') {
                $pays = null;
            }

            $ville = $post->get('ville');
            if ($ville == '') {
                $ville = null;
            }

            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            //Nouveau dossier
            if ($idDossier == 0) {
                $res = array('estInsere' => -1, 'idDossier' => 0, 'id' => 0);
                return new JsonResponse($res);
            } //Mise à jour dossier
            else {

                try {
                    $newDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($idDossier);

                    if ($formeJuridique != null) {
                        $newDossier->setFormeJuridique($formeJuridique);
                    } else {
                        $newDossier->setFormeJuridique(null);
                    }

                    if ($codeApe != null) {
                        $newDossier->setActiviteComCat3($codeApe);
                    }
                    if (!is_null($dateDeb)) {
                        $newDossier->setDebutActivite($dateDeb);
                    } else {
                        $newDossier->setDebutActivite(null);
                    }

                    if ($raisonSocial != '') {
                        $newDossier->setRsSte($raisonSocial);
                        $newDossier->setEntreprise($raisonSocial);
                    }

                    $newDossier->setEnseigne($enseigne);
                    $newDossier->setTrancheEffectif($trancheEffectif);
                    $newDossier->setNumRue($numRue);
                    $newDossier->setCodePostal($codePostal);
                    $newDossier->setPays($pays);
                    $newDossier->setVille($ville);

                    $em->persist($newDossier);

                    $em->flush();

                    $newDossierId = $newDossier->getId();
                    $newDossierId = Boost::boost($newDossierId);
                    //2: mise à jour
                    $res = array('estInsere' => 2, 'idDossier' => $newDossierId, 'id' => $newDossier->getId());


                    return new JsonResponse($res);

                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }
            }

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editInfoPerdosPrestationAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();


            $typePrestation = null;
            $typePrestationId = $post->get('typePrestation');
            if ($typePrestationId != '') {
//                $res = array('estInsere' => 0, 'message' => 'Prestations');
//                return new JsonResponse($res);
                $typePrestation = $this->getDoctrine()
                    ->getRepository('AppBundle:TypePrestation')
                    ->find($typePrestationId);
            }

            $autrePrestation = $post->get('autrePrestation');
            if ($autrePrestation == '') {
                $autrePrestation = null;
            }

            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            //Nouveau dossier
            if ($idDossier == 0) {

                return new Response(-1);

            } //Mise à jour dossier
            else {

                try {
                    $newDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($idDossier);

                    $utilisateur = $this->getUser();
                    //**************ENREGISTREMENT LOG**************\\
                    if ($newDossier->getAccuseCreation() >= 1) {

                        if ($newDossier->getTypePrestation2() != $typePrestation) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(3);
                            $log->setBloc(1);

                            $oldVal = "";
                            if (!is_null($newDossier->getTypePrestation2())) {
                                $oldVal = $newDossier->getTypePrestation2()
                                    ->getLibelle();
                            }

                            $newVal = "";
                            if (!is_null($typePrestation)) {
                                $newVal = $typePrestation->getLibelle();
                            }

                            $log->setChamp('Prestations');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();

                        }

                        if ($newDossier->getAutrePrestation() != $autrePrestation) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($newDossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(3);
                            $log->setBloc(1);


                            $oldVal = '';

                            if (!is_null($newDossier->getAutrePrestation())) {
                                $oldVal = $newDossier->getAutrePrestation();
                            }

                            $newVal = '';

                            if (!is_null($autrePrestation)) {
                                $newVal = $autrePrestation;
                            }

                            $log->setChamp('Autres');

                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }
                    }


                    $newDossier->setTypePrestation2($typePrestation);
                    $newDossier->setAutrePrestation($autrePrestation);

                    $em->persist($newDossier);
                    $em->flush();

                    //2: mise à jour
                    return new Response(2);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            }


        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editInfoPerdosReglePaiementAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $post = $request->request;


            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            //Nouveau dossier
            if ($idDossier == 0) {
                $res = array('estInsere' => -1, 'message' => 'Dossier');
                return new JsonResponse($res);
            } else {

                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);

                $em = $this->getDoctrine()
                    ->getEntityManager();

                $fDateLe = $post->get('fDateLe');

                if ($fDateLe != '') {
                    try {
                        $fDateLe = intval($fDateLe);
                    } catch (\Exception $ex) {
                    }
                } else {
                    $fDateLe = null;
                }

                $fNbreJour = $post->get('fNbreJour');

                try {
                    $fNbreJour = intval($fNbreJour);
                } catch (\Exception $ex) {
                    $fNbreJour = 30;
                }

                $fTypeDate = $post->get('fTypeDate');

                try {
                    $fTypeDate = intval($fTypeDate);
                } catch (\Exception $ex) {
                    $fTypeDate = 30;
                }


                $reglePaiementFournisseurs = $this->getDoctrine()
                    ->getRepository('AppBundle:ReglePaiementDossier')
                    ->findBy(array('dossier' => $dossier, 'typeTiers' => 0));

                //Mise à jour regle paiement fournisseur
                if ($reglePaiementFournisseurs != null) {
                    /** @var  $reglePaiementFournisseur ReglePaiementDossier */
                    $reglePaiementFournisseur = $reglePaiementFournisseurs[0];

                    $reglePaiementFournisseur->setNbreJour($fNbreJour);
                    $reglePaiementFournisseur->setTypeDate($fTypeDate);
                    $reglePaiementFournisseur->setDateLe($fDateLe);
                } //Insertion regle paiement fournisseur
                else {

                    $reglePaiementFournisseur = new ReglePaiementDossier();

                    $reglePaiementFournisseur->setDossier($dossier);
                    $reglePaiementFournisseur->setTypeTiers(0);
                    $reglePaiementFournisseur->setNbreJour($fNbreJour);
                    $reglePaiementFournisseur->setTypeDate($fTypeDate);
                    $reglePaiementFournisseur->setDateLe($fDateLe);

                }

                $em->persist($reglePaiementFournisseur);
                $em->flush();


                $cDateLe = $post->get('cDateLe');

                if ($cDateLe != '') {
                    try {
                        $cDateLe = intval($cDateLe);
                    } catch (\Exception $ex) {
                        $cDateLe = null;
                    }
                } else {
                    $cDateLe = null;
                }

                $cNbreJour = $post->get('cNbreJour');

                try {
                    $cNbreJour = intval($cNbreJour);
                } catch (\Exception $ex) {
                    $cNbreJour = 30;
                }

                $cTypeDate = $post->get('cTypeDate');

                try {
                    $cTypeDate = intval($cTypeDate);
                } catch (\Exception $ex) {
                    $cTypeDate = 30;
                }

                $reglePaiementClients = $this->getDoctrine()
                    ->getRepository('AppBundle:ReglePaiementDossier')
                    ->findBy(array('dossier' => $dossier, 'typeTiers' => 1));

                //Mise à jour regle paiement client
                if ($reglePaiementClients != null) {
                    /** @var  $reglePaiementClient ReglePaiementDossier */
                    $reglePaiementClient = $reglePaiementClients[0];

                    $reglePaiementClient->setNbreJour($cNbreJour);
                    $reglePaiementClient->setTypeDate($cTypeDate);
                    $reglePaiementClient->setDateLe($cDateLe);
                } //Insertion regle paiement client
                else {

                    $reglePaiementClient = new ReglePaiementDossier();

                    $reglePaiementClient->setTypeTiers(1);
                    $reglePaiementClient->setDossier($dossier);
                    $reglePaiementClient->setNbreJour($cNbreJour);
                    $reglePaiementClient->setTypeDate($cTypeDate);
                    $reglePaiementClient->setDateLe($cDateLe);

                }

                $em->persist($reglePaiementClient);
                $em->flush();

                return new Response(2);
            }

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editInfoPerdosReglePaiementClientAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $post = $request->request;


            $idClient = Boost::deboost($post->get('clientId'), $this);

            //Nouveau dossier
            if ($idClient == 0) {
                $res = array('estInsere' => -1, 'message' => 'Client');
                return new JsonResponse($res);
            } else {

                $client = $this->getDoctrine()
                    ->getRepository('AppBundle:Client')
                    ->find($idClient);

                $em = $this->getDoctrine()
                    ->getEntityManager();

                $fDateLe = $post->get('fDateLe');

                if ($fDateLe != '') {
                    try {
                        $fDateLe = intval($fDateLe);
                    } catch (\Exception $ex) {
                    }
                } else {
                    $fDateLe = null;
                }

                $fNbreJour = $post->get('fNbreJour');

                try {
                    $fNbreJour = intval($fNbreJour);
                } catch (\Exception $ex) {
                    $fNbreJour = 30;
                }

                $fTypeDate = $post->get('fTypeDate');

                try {
                    $fTypeDate = intval($fTypeDate);
                } catch (\Exception $ex) {
                    $fTypeDate = 30;
                }


                $reglePaiementClientFournisseurs = $this->getDoctrine()
                    ->getRepository('AppBundle:ReglePaiementClient')
                    ->findBy(array('client' => $client, 'typeTiers' => 0));

                //Mise à jour regle paiement fournisseur
                if ($reglePaiementClientFournisseurs != null) {
                    /** @var  $reglePaiementClientFournisseur ReglePaiementDossier */
                    $reglePaiementClientFournisseur = $reglePaiementClientFournisseurs[0];

                    $reglePaiementClientFournisseur->setNbreJour($fNbreJour);
                    $reglePaiementClientFournisseur->setTypeDate($fTypeDate);
                    $reglePaiementClientFournisseur->setDateLe($fDateLe);
                } //Insertion regle paiement fournisseur
                else {

                    $reglePaiementClientFournisseur = new ReglePaiementClient();

                    $reglePaiementClientFournisseur->setClient($client);
                    $reglePaiementClientFournisseur->setTypeTiers(0);
                    $reglePaiementClientFournisseur->setNbreJour($fNbreJour);
                    $reglePaiementClientFournisseur->setTypeDate($fTypeDate);
                    $reglePaiementClientFournisseur->setDateLe($fDateLe);

                }

                $em->persist($reglePaiementClientFournisseur);
                $em->flush();


                $cDateLe = $post->get('cDateLe');

                if ($cDateLe != '') {
                    try {
                        $cDateLe = intval($cDateLe);
                    } catch (\Exception $ex) {
                        $cDateLe = null;
                    }
                } else {
                    $cDateLe = null;
                }

                $cNbreJour = $post->get('cNbreJour');

                try {
                    $cNbreJour = intval($cNbreJour);
                } catch (\Exception $ex) {
                    $cNbreJour = 30;
                }

                $cTypeDate = $post->get('cTypeDate');

                try {
                    $cTypeDate = intval($cTypeDate);
                } catch (\Exception $ex) {
                    $cTypeDate = 30;
                }

                $reglePaiementClientClients = $this->getDoctrine()
                    ->getRepository('AppBundle:ReglePaiementClient')
                    ->findBy(array('client' => $client, 'typeTiers' => 1));

                //Mise à jour regle paiement client
                if ($reglePaiementClientClients != null) {
                    /** @var  $reglePaiementClientClient ReglePaiementClient */
                    $reglePaiementClientClient = $reglePaiementClientClients[0];

                    $reglePaiementClientClient->setNbreJour($cNbreJour);
                    $reglePaiementClientClient->setTypeDate($cTypeDate);
                    $reglePaiementClientClient->setDateLe($cDateLe);
                } //Insertion regle paiement client
                else {

                    $reglePaiementClientClient = new ReglePaiementClient();

                    $reglePaiementClientClient->setTypeTiers(1);
                    $reglePaiementClientClient->setClient($client);
                    $reglePaiementClientClient->setNbreJour($cNbreJour);
                    $reglePaiementClientClient->setTypeDate($cTypeDate);
                    $reglePaiementClientClient->setDateLe($cDateLe);

                }

                $em->persist($reglePaiementClientClient);
                $em->flush();

                return new Response(2);
            }

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function editInfoPerdosReglePaiementV2Action(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;
            $em = $this->getDoctrine()->getEntityManager();

            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            $value = $post->get('value');
            $field = $post->get('field');
            $type = $post->get('type');

            if ($field == 'DateLe') {
                if ($value == '') {
                    $value = null;
                }
            }

            if ($idDossier == 0) {
                //Erreur: le dossier n'existe pas encore
                $res = array('estInsere' => -1, 'message' => 'Dossier');
                return new JsonResponse($res);
            } else {
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);

                $reglePaiements = $this->getDoctrine()
                    ->getRepository('AppBundle:ReglePaiementDossier')
                    ->findBy(array('dossier' => $dossier, 'typeTiers' => $type));

                //Nouveau regle paiement
                if ($reglePaiements == null) {
                    try {
                        $reglePaiement = new ReglePaiementDossier();

                        $reglePaiement->setDossier($dossier);
                        $reglePaiement->setTypeTiers($type);
                        $reglePaiement->{"set$field"}($value);

                        $em->persist($reglePaiement);
                        $em->flush();

                        return new Response(1);
                    } catch (Exception $e) {
                        return new Response($e->getMessage());
                    }
                } //Mise à jour
                else {
                    try {

                        $reglePaiement = $reglePaiements[0];

                        $reglePaiement->{"set$field"}($value);

                        $em->persist($reglePaiement);
                        $em->flush();

                        return new Response(2);
                    } catch (Exception $e) {
                        return new Response($e->getMessage());
                    }
                }
            }
        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function editInfoPerdosReglePaiementTiersAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()
                ->getEntityManager();

            $post = $request->request;

            $imageId = Boost::deboost($post->get('imageId'), $this);
            $tiersId = Boost::deboost($post->get('tiersId'), $this);


            $dateLe = $post->get('dateLe');

            if ($dateLe != '') {
                try {
                    $dateLe = (int)$dateLe;
                } catch (\Exception $ex) {
                    $dateLe = null;
                }
            } else {
                $dateLe = null;
            }

            $nbreJour = $post->get('nbreJour');

            try {
                $nbreJour = (int)$nbreJour;
            } catch (\Exception $ex) {
                $nbreJour = 30;
            }

            $typeDate = $post->get('typeDate');

            try {
                $typeDate = (int)$typeDate;
            } catch (\Exception $ex) {
                $typeDate = null;
            }

            if (is_bool($imageId)) return new Response('security');

            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find($imageId);

            $dossier = $image->getLot()->getDossier();

            $tiers = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->find($tiersId);

            $reglePaiementTiers = $this->getDoctrine()
                ->getRepository('AppBundle:ReglePaiementTiers')
                ->findBy(array('dossier' => $dossier, 'tiers' => $tiers));

            $reglePaiementDossiers = $this->getDoctrine()
                ->getRepository('AppBundle:ReglePaiementDossier')
                ->findBy(array('dossier' => $dossier));


            if (null !== $typeDate) {


                if($tiers !== null) {
                    //Mise à jour regle paiement tiers

                    /**@var $newReglePaiementTiers ReglePaiementTiers */
                    if (count($reglePaiementTiers) > 0) {
                        $newReglePaiementTiers = $reglePaiementTiers[0];

                        $newReglePaiementTiers->setNbreJour($nbreJour);
                        $newReglePaiementTiers->setDateLe($dateLe);
                        $newReglePaiementTiers->setTypeDate($typeDate);

                        $em->persist($newReglePaiementTiers);
                        $em->flush();

                        return new Response(1);
                    } //Insertion regle de paiement tiers
                    else {

                        $newReglePaiementTiers = new ReglePaiementTiers();

                        $newReglePaiementTiers->setDossier($dossier);
                        $newReglePaiementTiers->setTiers($tiers);
                        $newReglePaiementTiers->setNbreJour($nbreJour);
                        $newReglePaiementTiers->setDateLe($dateLe);
                        $newReglePaiementTiers->setTypeDate($typeDate);

                        $em->persist($newReglePaiementTiers);
                        $em->flush();

                        return new Response(2);
                    }
                }

                else{


                    /**@var $newReglePaiementTiers ReglePaiementTiers */
                    if (count($reglePaiementDossiers) > 0) {
                        $newReglePaiementDossier = $reglePaiementDossiers[0];

                        $newReglePaiementDossier->setNbreJour($nbreJour);
                        $newReglePaiementDossier->setDateLe($dateLe);
                        $newReglePaiementDossier->setTypeDate($typeDate);

                        $em->persist($newReglePaiementDossier);
                        $em->flush();

                        return new Response(1);
                    } //Insertion regle de paiement tiers
                    else {

                        $newReglePaiementDossier = new ReglePaiementDossier();

                        $newReglePaiementDossier->setDossier($dossier);
                        $newReglePaiementDossier->setNbreJour($nbreJour);
                        $newReglePaiementDossier->setDateLe($dateLe);
                        $newReglePaiementDossier->setTypeDate($typeDate);

                        $em->persist($newReglePaiementDossier);
                        $em->flush();

                        return new Response(2);
                    }

                }
            }

            return new Response(-1);

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function codeApeShowTreeAction()
    {
        return $this->render('InfoPerdosBundle:InfoCaracteristique:codeApeTree.html.twig');
    }

    public function codeApeAction($json)
    {
        $activite_com_cats = $this->getDoctrine()
            ->getRepository('AppBundle:ActiviteComCat')
            ->findAll();


        if ($json == 1) {

            $rows = array();


            //Activités commericales categories
            foreach ($activite_com_cats as $activite_com_cat) {
                $rows[] = array(
                    'id' => 'ac' . $activite_com_cat->getId(),
                    'parent' => '#',
                    'text' => $activite_com_cat->getLibelle()
                );


                $activite_com_cat1s = $this->getDoctrine()
                    ->getRepository('AppBundle:ActiviteComCat1')
                    ->getActiviteComCat1ByActiviteComCat($activite_com_cat);
                //->findAll();


                //Activités commerciales cat1

                /* @var ActiviteComCat1 $activite_com_cat1 */
                foreach ($activite_com_cat1s as $activite_com_cat1) {
                    $rows[] = array(
                        'id' => 'a1c' . $activite_com_cat1->getId(),
                        'parent' => 'ac' . $activite_com_cat->getId(),
                        'text' => $activite_com_cat1->getAlpha() . ' ' . $activite_com_cat1->getLibelle()
                    );

                    $activite_com_cat2s = $this->getDoctrine()
                        ->getRepository('AppBundle:ActiviteComCat2')//->findAll();
                        ->getActiviteComCat2ByActiviteComCat1($activite_com_cat1);

                    //Activités commerciales cat2

                    /* @var ActiviteComCat2 $activite_com_cat2 */
                    foreach ($activite_com_cat2s as $activite_com_cat2) {
                        $rows[] = array(
                            'id' => 'a2c' . $activite_com_cat2->getId(),
                            'parent' => 'a1c' . $activite_com_cat1->getId(),
                            'text' => $activite_com_cat2->getCode() . ' ' . $activite_com_cat2->getLibelle()
                        );

                        $activite_com_cat3s = $this->getDoctrine()
                            ->getRepository('AppBundle:ActiviteComCat3')//->findAll();
                            ->getActiviteComCat3ByActiviteComCat2($activite_com_cat2);

                        //Activités commerciales cat2

                        /* @var ActiviteComCat3 $activite_com_cat3 */
                        foreach ($activite_com_cat3s as $activite_com_cat3) {
                            $rows[] = array(
                                'id' => $activite_com_cat3->getId(),
                                'parent' => 'a2c' . $activite_com_cat2->getId(),
                                'text' => $activite_com_cat3->getCodeApe() . ' ' . $activite_com_cat3->getLibelle()
                            );
                        }
                    }
                }


            }

            $liste = array('data' => $rows);

            return (new JsonResponse($liste));
        } else {

            $activite_com_cat3s = $this->getDoctrine()
                ->getRepository('AppBundle:ActiviteComCat3')
                ->findBy(array(), array('id' => 'ASC'));

            $options = '<select>';

            $options .= '<option></option>';

            foreach ($activite_com_cat3s as $act) {
                $options .= '<option value="' . $act->getId() . '">' . $act->getCodeApe() . '</option>';
            }

            $options .= '</select>';

            return new Response($options);
        }
    }

    public function professtionLiberaleShowTreeAction()
    {
        return $this->render('InfoPerdosBundle:InfoCaracteristique:professionLiberaleTree.html.twig');
    }

    public function professionLiberaleAction()
    {
        $professionLiberaleCats = $this->getDoctrine()
            ->getRepository('AppBundle:ProfessionLiberaleCat')
            ->findAll();

        $rows = array();

        //Parents
        foreach ($professionLiberaleCats as $professionLiberaleCat) {
            $rows[] = array(
                'id' => 'pc' . $professionLiberaleCat->getId(),
                'parent' => '#',
                'text' => $professionLiberaleCat->getLibelle()
            );

            $professionLiberales = $this->getDoctrine()
                ->getRepository('AppBundle:ProfessionLiberale')
                ->getProfessionLiberaleByCat($professionLiberaleCat);

            //Proffession liberale
            foreach ($professionLiberales as $professionLiberale) {
                /* @var  $professionLiberale ProfessionLiberale */
                $rows[] = array(
                    'id' => $professionLiberale->getId(),
                    'parent' => 'pc' . $professionLiberaleCat->getId(),
                    'text' => $professionLiberale->getLibelle()
                );
            }
        }
        $liste = array('data' => $rows);

        return (new JsonResponse($liste));
    }

    function stringInsert($str, $insertstr, $pos)
    {
        $str = substr($str, 0, $pos) . $insertstr . substr($str, $pos);
        return $str;
    }

    public function firmApiAction(Request $request, $formeJuridique, $activite, $dateDebutActivite)
    {
        if ($request->isXmlHttpRequest()) {
            $formeJuridiqueId = null;
            $codeApe = null;
            $codeApeLib = null;
            $codeApeId = null;
            $dateDeb = null;

            $formeJuridiques = $this->getDoctrine()->getRepository('AppBundle:FormeJuridique')
                ->findAll();

            $activite_com_cat3s = $this->getDoctrine()->getRepository('AppBundle:ActiviteComCat3')
                ->findAll();

            if ($formeJuridique != -1) {
                foreach ($formeJuridiques as $forme) {
                    if (strtoupper($forme->getLibelle()) == strtoupper($formeJuridique)) {
                        $formeJuridiqueId = $forme->getId();
                        break;
                    }
                }
            }

            $shortest = -1;
            $closest = "";

            if ($activite != -1) {
                foreach ($activite_com_cat3s as $activite_com_cat3) {
                    $lev = levenshtein(strtoupper($activite), strtoupper(strtoupper($activite_com_cat3->getLibelle())));

                    if ($lev == 0) {
                        $closest = $activite_com_cat3->getLibelle();
                        break;
                    }
                    if ($lev <= $shortest || $shortest < 0) {
                        $closest = $activite_com_cat3->getLibelle();
                        $shortest = $lev;
                    }
                }

                $resActiviteComCat = $this->getDoctrine()->getRepository('AppBundle:ActiviteComCat3')
                    ->findOneBy(array('libelle' => $closest));

                $codeApeLib = $resActiviteComCat->getLibelle();
                $codeApe = $resActiviteComCat->getCodeApe();
                $codeApeId = $resActiviteComCat->getId();
            }

            if ($dateDebutActivite != -1) {
                $date = new \DateTime($dateDebutActivite);
                $dateDeb = $date->format('d/m/Y');
            }

            $row = array('formeJuridiqueId' => $formeJuridiqueId,
                'codeApeId' => $codeApeId,
                'codeApe' => $codeApe,
                'codeApeLib' => $codeApeLib,
                'dateDeb' => $dateDeb);

            return new JsonResponse($row);

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function verifierCodeApeAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            $post = $request->request;
            $codeApe = $post->get('codeApe');

            $activiteComCat3s = $this->getDoctrine()
                ->getRepository('AppBundle:ActiviteComCat3')
                ->getActivieComCat3ByCodeApe($codeApe);


            /** @var ActiviteComCat3 activiteComCat3 */
            if (count($activiteComCat3s) > 0) {
                $activiteComCat3 = $activiteComCat3s[0];
            } else {
                $activiteComCat3 = $this->getDoctrine()
                    ->getRepository('AppBundle:ActiviteComCat3')
                    ->find(734);
            }

            $res = array('codeApe' => $activiteComCat3->getCodeApe(),
                'id' => $activiteComCat3->getId(),
                'intitule' => $activiteComCat3->getLibelle());


            return new JsonResponse($res);


        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }

    }

    public function verifierSirenAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $siren = $post->get('siren');
            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $siteId = Boost::deboost($post->get('siteId'), $this);

            $site = null;

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if (!is_null($dossier)) {
                $site = $dossier->getSite();
            } else {
                $site = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->find($siteId);
            }


            if (!is_null($site)) {

                $dossierSirens = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->findBy(array('sirenSte' => $siren, 'site' => $site));
            } else {
                $dossierSirens = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->findBy(array('sirenSte' => $siren));
            }


            if (!is_null($dossierSirens) && count($dossierSirens) > 0) {

                if (!is_null($dossier)) {
                    foreach ($dossierSirens as $dossierSiren) {
                        //Raha efa mi-existe ilay dossier, siren ihany ilay izy => valide ny siren
                        if ($dossierSiren->getSirenSte() == $dossier->getSirenSte()) {
                            return new JsonResponse(1);
                        }
                    }
                }

                return new JsonResponse(-1);
            } else {
                return new JsonResponse(1);
            }
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }

    }

    public function distanceAction(Request $request, $site, $nomDossier, $idDossier)
    {

        if ($request->isXmlHttpRequest()) {

            $site = Boost::deboost($site, $this);

            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->findBy(array('site' => $site));

            $dossierUpdate = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find(Boost::deboost($idDossier, $this));

            $row = array();

            foreach ($dossiers as $dossier) {

                if(null !== $dossierUpdate){
                    if($dossier === $dossierUpdate){
                        continue;
                    }
                }

                /* @var \AppBundle\Entity\Dossier $dossier */
                $dossierNom = $dossier->getNom();
                $nomDossier = strtolower($nomDossier);
                $nomDossier = preg_replace('/[^[:alnum:]-_]/','_', $nomDossier);
                $nomDossier = str_replace(array('-', 'consulting', 'madame', 'monsieur', 'conseil'),
                    array('_', '', '', '', ''), $nomDossier);
                $dossierNom = strtolower($dossierNom);
                $dossierNom = str_replace(array('-', 'consulting', 'madame', 'monsieur', 'conseil'),
                    array('_', '', '', '', ''), $dossierNom);
                similar_text($nomDossier, $dossierNom, $percent);

                if ($percent >= 80) {
                    $row[] = array('nom' => $dossier->getNom(), 'pourcentage' => $percent);
                }
            }

            return new JsonResponse($row);
        }

        throw new AccessDeniedHttpException("Accès refusé");
    }

    public function pieceAEnvoyerAction(Request $request, $selecteur)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            //dossier
            $dossier = $post->get('dossier');
            $dossier = Boost::deboost($dossier, $this);
            if (is_bool($dossier)) return new Response('security');

            //directory
            $directory = "IMAGES";
            $fs = new Filesystem();
            try {
                $fs->mkdir($directory, 0777);
            } catch (IOExceptionInterface $e) {
            }


            /* @var $dossier \AppBundle\Entity\Dossier */
            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->createQueryBuilder('d')
                ->where('d.id = :id')
                ->setParameter('id', $dossier)
                ->getQuery()
                ->getOneOrNullResult();

            if ($dossier == null) {
                return new Response(-1);
            }

            //exercice

            $exercice = date('Y');

            //creation dossier dateScan
            $dateNow = new \DateTime();
            $directory .= '/'.$dateNow->format('Ymd');
            try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }


            $files = $request->files->get('envoi_piece');

            $source_image = null;

            $lot = $lot_urgent = null;

            $em = $this->getDoctrine()->getManager();

            $lotGroup = new LotGroup();
            $lotGroup->setDossier($dossier);
            $lotGroup->setStatus(2);
            $lotGroup->setUtilisateur($this->getUser());

            $em->persist($lotGroup);
            $em->flush();

            if (count($files) > 0) {
                $lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $this->getUser(), '');
                $lot->setLotGroup($lotGroup);
                $em->flush();
            }

            $index = 0;

            if ($files != null) {
                foreach ($files as $file) {
                    $file_name = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $name = basename($file_name, '.' . $extension);
                    $file->move($directory, $file_name);
                    $newName = Boost::getUuid();
                    $fs->rename($directory . '/' . $file_name, $directory . '/' . $newName . '.' . $extension);

                    $image = new Image();
//                    $image->setDownload(new \DateTime());
                    $image->setLot($lot);
                    $image->setExercice($exercice);
                    $image->setExtImage($extension);
                    $image->setNbpage(1);
                    $image->setNomTemp($newName);
                    $image->setOriginale($name);

                    $image->setSourceImage($this->getDoctrine()->getRepository('AppBundle:SourceImage')->getBySource('PICDATA'));


                    $em->persist($image);

                    $em->flush();


                    $imageATraiter = new ImageATraiter();
                    $imageATraiter->setImage($image);
                    $em->persist($imageATraiter);
                    $em->flush();

//                    $operateur = $this->getDoctrine()
//                        ->getRepository('AppBundle:Operateur')
//                        ->findOneBy(array('login' => 'robot'));

//                    $categorie = $this->getDoctrine()
//                        ->getRepository('AppBundle:Categorie')
//                        ->findOneBy(array('code' => 'CODE_JURIDIQUE'));

//                    $separation = new Separation();
//
//                    $separation->setImage($image);
//                    $separation->setOperateur($operateur);
//                    $separation->setCategorie($categorie);
//
//                    $em->persist($separation);
//                    $em->flush();

                    switch ($selecteur) {
                        case 'js_envoi_plan_comptable':
//                            $dossier->setPlanComptable(2);
                            if ($dossier->getPlanComptable() == 1) {
                                $dossier->setPlanComptable(3);
                            }
//                            else if ($dossier->getPlanComptable() == 2) {
//                                $dossier->setPlanComptable(4);
//                            }

                            break;
                        case 'js_envoi_archive_comptable':
                            $dossier->setArchiveComptable(3);
                            break;

                        case 'js_envoi_balance_n1':
                            $dossier->setBalanceN1(3);
                            break;
                        case 'js_envoi_grand_livre':
                            $dossier->setGrandLivre(3);
                            break;
                        case 'js_envoi_journaux_n1':
                            $dossier->setJournauxN1(3);
                            break;
                        case 'js_envoi_dernier_rapprochement_banque':
                            $dossier->setDernierRapprochementBanqueN1(3);
                            break;
                        case 'js_envoi_etat_immobilisation':
                            $dossier->setEtatImmobilisationN1(3);
                            break;
                        case 'js_envoi_liasse_fisacle_n1':
                            $dossier->setLiasseFiscaleN1(3);
                            break;
                        case 'js_envoi_tva_derniere_ca3':
                            $dossier->setTvaDerniereCa3(3);
                            break;
                        case 'js_envoi_statut':
                            if ($dossier->getStatut() == 1) {
                                $dossier->setStatut(3);
                            }
//                            else if ($dossier->getStatut() == 2) {
//                                $dossier->setStatut(4);
//                            }
                            break;
                        case 'js_envoi_kbis':
                            if ($dossier->getKbis() == 1) {
                                $dossier->setKbis(3);
                            }
//                            else if ($dossier->getKbis() == 2) {
//                                $dossier->setKbis(4);
//                            }
                            break;
                        case 'js_envoi_baux':
                            if ($dossier->getBaux() == 1) {
                                $dossier->setBaux(3);
                            }
//                            else if ($dossier->getBaux() == 2) {
//                                $dossier->setBaux(4);
//                            }
                            break;
                        case 'js_envoi_assurance':
                            if ($dossier->getAssurance() == 1) {
                                $dossier->setAssurance(3);
                            }
//                            else if ($dossier->getAssurance() == 2) {
//                                $dossier->setAssurance(4);
//                            }
                            break;
                        case 'js_envoi_autre':
                            if ($dossier->getAutre() == 1) {
                                $dossier->setAutre(3);
                            }
//                            else if ($dossier->getAutre() == 2) {
//                                $dossier->setAutre(4);
//                            }
                            break;
                        case 'js_envoi_emprunt':
                            if ($dossier->getEmprunt() == 1) {
                                $dossier->setEmprunt(3);
                            }
//                            else if ($dossier->getEmprunt() == 2) {
//                                $dossier->setEmprunt(4);
//                            }
                            break;
                        case 'js_envoi_leasing':
                            if ($dossier->getLeasing() == 1) {
                                $dossier->setLeasing(3);
                            }
//                            else if ($dossier->getAutre() == 2) {
//                                $dossier->setAutre(4);
//                            }
                            break;
                    }

                    $em->persist($dossier);
                    $em->flush();


                    $index++;
                }

                return new JsonResponse(array('filecount' => count($files)));
            } else {
                return new JsonResponse(array('filecount' => -1));
            }


        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }

    }

    public function infogreffeDataAction(Request $request, $siren)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $opts = array('https' => array('method' => "GET"));
                $context = stream_context_create($opts);
//            Token tena izy
                $file = file_get_contents('https://api.datainfogreffe.fr/api/v1/Entreprise/FicheIdentite/' . $siren . '?token=QfekVj0wlg9tEeicApHDJrHc7gdLyJ072726248rAWxEVBqBbtkrDwHFp5Pl666', false, $context);

//            Token test (siren:123456788)
//                $file = file_get_contents('https://api.datainfogreffe.fr/api/v1/Entreprise/FicheIdentite/123456788?token=AXfBH56aWQcdCKRHkUAd9UcT1zsydYfQajOGeDfjmE3ztMBNbwWxxfu0xWi2', false, $context);

                return new Response($file);
            } catch (\Exception $e) {
                return new Response(-1);
            }
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }


    public function openDatasoftAction(Request $request, $siren)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $opts = array('https' => array('method' => "GET"));
                $context = stream_context_create($opts);

                $sir = $siren;

                $nic = "-1";

                if (strlen($siren) >= 14) {
                    $sir = substr($siren, 0, 9);
                    $nic = substr($siren, 9, 14);
                }

                $file = file_get_contents('https://data.opendatasoft.com/api/records/1.0/search/?dataset=sirene%40public&q=' . $sir, false, $context);

                $sirenDataSoft = json_decode($file);

                $sirenEntities = $sirenDataSoft->records;

                if (!is_null($sirenEntities)) {
                    $sirenEntity = $sirenEntities[0];
                    if (count($sirenEntities) > 1) {
                        if ($nic != "-1") {
                            foreach ($sirenEntities as $sirenEnt) {

                                try {

                                    if ($sirenEnt->fields->nic == $nic) {
                                        $sirenEntity = $sirenEnt;
                                        break;
                                    }

                                } catch (\Exception $e) {
                                }
                            }
                        }
                    }


                    $raisonSocial = null;
                    try {
                        $raisonSocial = $sirenEntity->fields->nomen_long;

                        $raisonSocial = str_replace('*', ' ', $raisonSocial);
                        $raisonSocial = str_replace('/', ' ', $raisonSocial);
                    } catch (\Exception $e) {
                    }

                    $formeJuridiqueId = null;
                    try {
                        $nj = $sirenEntity->fields->nj;
                        $correspondance = $this->getDoctrine()
                            ->getRepository('AppBundle:CorrespSirenFormeJuridique')
                            ->findOneBy(array('nj' => $nj));

                        if (!is_null($correspondance)) {
                            $formeJuridiqueId = $this->getDoctrine()
                                ->getRepository('AppBundle:FormeJuridique')
                                ->findOneBy(array('extension' => $correspondance->getExtension()))
                                ->getId();
                        };
                    } catch (\Exception $e) {
                    }


                    $codeApeId = null;
                    $codeApeLib = null;
                    $codeApe = null;
                    try {

                        $activite = $sirenEntity->fields->apen700;

                        if ($activite != "" && !is_null($activite)) {
                            //Tokony ho 5 foana ny halavan'ny codeape avy @ inforgreffe
                            if (strlen($activite) == 5) {

//                                $activite = $this->stringInsert($activite, '.', 2);

                                $resActiviteComCat = $this->getDoctrine()->getRepository('AppBundle:ActiviteComCat3')
                                    ->findOneBy(array('codeApe' => $activite));


                                if (!is_null($resActiviteComCat)) {

                                    $codeApeLib = $resActiviteComCat->getLibelle();
                                    $codeApe = $resActiviteComCat->getCodeApe();
                                    $codeApeId = $resActiviteComCat->getId();
                                }
                            }
                        }
                    } catch (\Exception $e) {
                    }


                    $dateDeb = null;
                    try {
                        $dateDebutAct = $sirenEntity->fields->ddebact;

                        if ($dateDebutAct != "" && !is_null($dateDebutAct)) {
                            $dateDeb = date("d/m/Y", strtotime($dateDebutAct));
                        }
                    } catch (\Exception $e) {
                    }

                    $numRue = null;
                    try {
                        $numRue = $sirenEntity->fields->l4_normalisee;
                        if ($numRue == '') {
                            $numRue = null;
                        }
                    } catch (\Exception $e) {
                    }

                    $codePostal = null;
                    try {
                        $codePostal = $sirenEntity->fields->codpos;
                        if ($codePostal == '') {
                            $codePostal = null;
                        }
                    } catch (\Exception $e) {
                    }

                    $pays = $sirenEntity->fields->l7_normalisee;
                    if ($pays == '') {
                        $pays = null;
                    }

                    $ville = $sirenEntity->fields->libcom;
                    if ($ville == '') {
                        $ville = null;
                    }

                    $enseigne = null;
//                    $enseigne = $sirenEntity[0]->fields->enseigne;
//                    if($enseigne == ''){
//                        $enseigne = null;
//                    }


                    $trancheEffectifId = null;
                    try {
                        $effectif = $sirenEntity->fields->libtefet;
                        if ($effectif == '') {
                            $effectif = null;
                        } else {
//                    $effectif = str_replace('salari?s','salariés');
                            $effectif = str_replace('salari?', 'salarié', $effectif);
                            $effectif = str_replace('?', 'à', $effectif);
                            $effectif = str_replace('Unitàs', 'Unités', $effectif);

                        }


                        $trancheEffectifs = $this->getDoctrine()
                            ->getRepository('AppBundle:TrancheEffectif')
                            ->findBy(array('libelle' => $effectif));
                        $trancheEffectifId = -1;
                        if (count($trancheEffectifs) > 0) {
                            $trancheEffectifId = $trancheEffectifs[0]->getId();
                        }
                    } catch (\Exception $e) {
                    }


                    $row = array('formeJuridiqueId' => $formeJuridiqueId,
                        'codeApeId' => $codeApeId,
                        'codeApe' => $codeApe,
                        'codeApeLib' => $codeApeLib,
                        'dateDeb' => $dateDeb,
                        'raisonSocial' => $raisonSocial,
                        'numRue' => $numRue,
                        'codePostal' => $codePostal,
                        'pays' => $pays,
                        'ville' => $ville,
                        'enseigne' => $enseigne,
                        'trancheEffectif' => $trancheEffectifId
                    );

                    return new JsonResponse($row);

                }

                return new Response(-1);
            } catch (\Exception $e) {
                return new Response($e->getMessage());
            }
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }


    public function inseeAction(Request $request, $siren)
    {
        if ($request->isXmlHttpRequest()) {

            /** @var  $sirenEntity Siren */
            $sirenEntity = $this->getDoctrine()
                ->getRepository('AppBundle:Siren')
                ->find($siren);

            if (!is_null($sirenEntity)) {
                $raisonSocial = $sirenEntity->getNomenLong();
                $raisonSocial = str_replace('*', ' ', $raisonSocial);
                $raisonSocial = str_replace('/', ' ', $raisonSocial);


                $nj = $sirenEntity->getNj();
                $formeJuridiqueId = null;
                $correspondance = $this->getDoctrine()
                    ->getRepository('AppBundle:CorrespSirenFormeJuridique')
                    ->findOneBy(array('nj' => $nj));

                if (!is_null($correspondance)) {
                    $formeJuridiqueId = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeJuridique')
                        ->findOneBy(array('extension' => $correspondance->getExtension()))
                        ->getId();
                };

                $activite = $sirenEntity->getApen700();
                $codeApeId = null;
                $codeApeLib = null;
                $codeApe = null;
                if ($activite != "" && !is_null($activite)) {
                    //Tokony ho 5 foana ny halavan'ny codeape avy @ inforgreffe
                    if (strlen($activite) == 5) {

//                        $activite = $this->stringInsert($activite, '.', 2);

                        $resActiviteComCat = $this->getDoctrine()->getRepository('AppBundle:ActiviteComCat3')
                            ->findOneBy(array('codeApe' => $activite));

                        if (!is_null($resActiviteComCat)) {

                            $codeApeLib = $resActiviteComCat->getLibelle();
                            $codeApe = $resActiviteComCat->getCodeApe();
                            $codeApeId = $resActiviteComCat->getId();
                        }
                    }
                }


                $dateDebutAct = $sirenEntity->getDdebact();
                $dateDeb = null;

                if ($dateDebutAct != "" && !is_null($dateDebutAct)) {
                    $dateDeb = date("d/m/Y", strtotime($dateDebutAct));
                }

                $numRue = $sirenEntity->getL4Normalisee();
                if ($numRue == '') {
                    $numRue = null;
                }

                $codePostal = $sirenEntity->getCodpos();
                if ($codePostal == '') {
                    $codePostal = null;
                }

                $pays = $sirenEntity->getL7Normalisee();
                if ($pays == '') {
                    $pays = null;
                }

                $enseigne = $sirenEntity->getEnseigne();
                if ($enseigne == '') {
                    $enseigne = null;
                }

                $effectif = $sirenEntity->getLibtefet();
                if ($effectif == '') {
                    $effectif = null;
                } else {
//                    $effectif = str_replace('salari?s','salariés');
                    $effectif = str_replace('salari?', 'salarié', $effectif);
                    $effectif = str_replace('?', 'à', $effectif);
                    $effectif = str_replace('Unitàs', 'Unités', $effectif);

                }

                $row = array('formeJuridiqueId' => $formeJuridiqueId,
                    'codeApeId' => $codeApeId,
                    'codeApe' => $codeApe,
                    'codeApeLib' => $codeApeLib,
                    'dateDeb' => $dateDeb,
                    'raisonSocial' => $raisonSocial,
                    'numRue' => $numRue,
                    'codePostal' => $codePostal,
                    'pays' => $pays,
                    'enseigne' => $enseigne,
                    'trancheEffectif' => $effectif
                );

                return new JsonResponse($row);
            } else {
                return new JsonResponse(-1);
            }

        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }


    public function inseeV2Action(Request $request, $siren)
    {

        if ($request->isXmlHttpRequest()) {

            $con = new CustomPdoConnection();
            $pdo = $con->sirenConnect();

            $query = "SELECT NOMEN_LONG, NJ,APEN700, DDEBACT, L4_NORMALISEE, CODPOS, L7_NORMALISEE ,ENSEIGNE, LIBTEFET, LIBCOM 
                      FROM siren where siren =:siren LIMIT 1";

            $sir = $siren;
            if (strlen($siren) >= 14) {
                $sir = substr($siren, 0, 9);
            }

            $prep = $pdo->prepare($query);
            $prep->execute(array(
                'siren' => $sir
            ));

            $sirenEntity = $prep->fetchAll();

            if (count($sirenEntity) > 0) {
                $raisonSocial = $sirenEntity[0]->NOMEN_LONG;
                $raisonSocial = str_replace('*', ' ', $raisonSocial);
                $raisonSocial = str_replace('/', ' ', $raisonSocial);


                $nj = $sirenEntity[0]->NJ;
                $formeJuridiqueId = null;
                $correspondance = $this->getDoctrine()
                    ->getRepository('AppBundle:CorrespSirenFormeJuridique')
                    ->findOneBy(array('nj' => $nj));

                if (!is_null($correspondance)) {
                    $formeJuridiqueId = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeJuridique')
                        ->findOneBy(array('extension' => $correspondance->getExtension()))
                        ->getId();
                };

                $activite = $sirenEntity[0]->APEN700;
                $codeApeId = null;
                $codeApeLib = null;
                $codeApe = null;
                if ($activite != "" && !is_null($activite)) {
                    //Tokony ho 5 foana ny halavan'ny codeape avy @ inforgreffe
                    if (strlen($activite) == 5) {

                        $activite = $this->stringInsert($activite, '.', 2);

                        $resActiviteComCat = $this->getDoctrine()->getRepository('AppBundle:ActiviteComCat3')
                            ->findOneBy(array('codeApe' => $activite));

                        $codeApeLib = $resActiviteComCat->getLibelle();
                        $codeApe = $resActiviteComCat->getCodeApe();
                        $codeApeId = $resActiviteComCat->getId();
                    }
                }


                $dateDebutAct = $sirenEntity[0]->DDEBACT;
                $dateDeb = null;

                if ($dateDebutAct != "" && !is_null($dateDebutAct)) {
                    $dateDeb = date("d/m/Y", strtotime($dateDebutAct));
                }

                $numRue = $sirenEntity[0]->L4_NORMALISEE;
                if ($numRue == '') {
                    $numRue = null;
                }

                $codePostal = $sirenEntity[0]->CODPOS;
                if ($codePostal == '') {
                    $codePostal = null;
                }

                $pays = $sirenEntity[0]->L7_NORMALISEE;
                if ($pays == '') {
                    $pays = null;
                }

                $ville = $sirenEntity[0]->LIBCOM;
                if ($ville == '') {
                    $ville = null;
                }

                $enseigne = $sirenEntity[0]->ENSEIGNE;
                if ($enseigne == '') {
                    $enseigne = null;
                }

                $effectif = $sirenEntity[0]->LIBTEFET;
                if ($effectif == '') {
                    $effectif = null;
                } else {
//                    $effectif = str_replace('salari?s','salariés');
                    $effectif = str_replace('salari?', 'salarié', $effectif);
                    $effectif = str_replace('?', 'à', $effectif);
                    $effectif = str_replace('Unitàs', 'Unités', $effectif);

                }


                $trancheEffectifs = $this->getDoctrine()
                    ->getRepository('AppBundle:TrancheEffectif')
                    ->findBy(array('libelleInsee' => $sirenEntity[0]->LIBTEFET));
                $trancheEffectifId = -1;
                if (count($trancheEffectifs) > 0) {
                    $trancheEffectifId = $trancheEffectifs[0]->getId();
                }

                $row = array('formeJuridiqueId' => $formeJuridiqueId,
                    'codeApeId' => $codeApeId,
                    'codeApe' => $codeApe,
                    'codeApeLib' => $codeApeLib,
                    'dateDeb' => $dateDeb,
                    'raisonSocial' => $raisonSocial,
                    'numRue' => $numRue,
                    'codePostal' => $codePostal,
                    'pays' => $pays,
                    'ville' => $ville,
                    'enseigne' => $enseigne,
                    'trancheEffectif' => $trancheEffectifId
                );

                return new JsonResponse($row);
            } else {
                return new JsonResponse(-1);
            }
        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }


    }

    public function infoGreffeAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $post = $request->request;

            $formeJuridique = $post->get('formeJuridique');

            $activite = $post->get('activite');
            $dateDebutActivite = $post->get('dateDebutActivite');


            $formeJuridiqueId = null;
            $codeApe = null;
            $codeApeLib = null;
            $codeApeId = null;
            $dateDeb = null;

            $formeJuridiques = $this->getDoctrine()->getRepository('AppBundle:FormeJuridique')
                ->findAll();
            $shortest = -1;
            $closest = "";

            if ($formeJuridique != -1 & $formeJuridique != "") {

                foreach ($formeJuridiques as $frmJuridique) {

                    if ($frmJuridique->getExtension() != "" && $frmJuridique->getExtension() != null) {
                        $lev = levenshtein(strtoupper($formeJuridique), (strtoupper($frmJuridique->getExtension())));

                        if ($lev == 0) {
                            $closest = $frmJuridique->getExtension();
                            break;
                        }
                        if ($lev <= $shortest || $shortest < 0) {
                            $closest = $frmJuridique->getExtension();
                            $shortest = $lev;
                        }
                    }
                }

                if (($shortest != -1 && $shortest < 18) || $lev == 0) {
                    $formeJuridiqueId = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeJuridique')
                        ->findOneBy(array('extension' => $closest))
                        ->getId();
                }
            }

            if ($activite != -1) {
                //Tokony ho 5 foana ny halavan'ny codeape avy @ inforgreffe
                if (strlen($activite) == 5) {

                    $activite = $this->stringInsert($activite, '.', 2);

                    $resActiviteComCat = $this->getDoctrine()->getRepository('AppBundle:ActiviteComCat3')
                        ->findOneBy(array('codeApe' => $activite));

                    $codeApeLib = $resActiviteComCat->getLibelle();
                    $codeApe = $resActiviteComCat->getCodeApe();
                    $codeApeId = $resActiviteComCat->getId();
                }
            }

            if ($dateDebutActivite != -1) {
                $date = new \DateTime($dateDebutActivite);
                $dateDeb = $date->format('d/m/Y');
            }

            $row = array('formeJuridiqueId' => $formeJuridiqueId,
                'codeApeId' => $codeApeId,
                'codeApe' => $codeApe,
                'codeApeLib' => $codeApeLib,
                'dateDeb' => $dateDeb);

            return new JsonResponse($row);

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function withRappBanqueAction(Request $request)
    {
        $post = $request->request;

        $dossierId = Boost::deboost($post->get('dossier'), $this);

        if ($dossierId != 0) {

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $methodeComptables = $this->getDoctrine()
                ->getRepository('AppBundle:MethodeComptable')
                ->findBy(array('dossier' => $dossier));

            if (count($methodeComptables) > 0) {
                $methodeComptable = $methodeComptables[0];

                if (!is_null($methodeComptable->getRapprochementBanque())) {
                    return new JsonResponse(1);
                } else {
                    return new JsonResponse(-1);
                }
            } else {
                return new JsonResponse(-1);
            }

        } else {
            return new JsonResponse(-1);
        }
    }

    public function withReglePaimentAction(Request $request)
    {
        $post = $request->request;

        $dossierId = Boost::deboost($post->get('dossier'), $this);

        if ($dossierId != 0) {

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $reglePaiementDossiers = $this->getDoctrine()
                ->getRepository('AppBundle:ReglePaiementDossier')
                ->findBy(array('dossier' => $dossier));

            if (count($reglePaiementDossiers) > 0) {
                return new JsonResponse(1);
            } else {
                return new JsonResponse(-1);
            }

        } else {
            return new JsonResponse(-1);
        }
    }

    public function withResponsableAction(Request $request)
    {
        $post = $request->request;

        $dossierId = Boost::deboost($post->get('dossier'), $this);

        if ($dossierId != 0) {

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $responsableCsd = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('dossier' => $dossier, 'typeResponsable' => 1));


            $responsableSite = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('site' => $dossier->getSite(), 'typeResponsable' => 1));

            if ($responsableCsd != null || $responsableSite != null) {

                return new JsonResponse(1);
            } else {
                return new JsonResponse(-1);
            }

        } else {
            return new JsonResponse(-1);
        }
    }


    public function editRemarqueDossierAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $post = $request->request;

        $dossierId = Boost::deboost($post->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $typeRemarque = intval($post->get('typeRemarque'));

        $remarqueDossier = $this->getDoctrine()
            ->getRepository('AppBundle:RemarqueDossier')
            ->findOneBy(array('dossier' => $dossier));

        $remarqueInformation = $post->get('remarqueInformation');
        if ($remarqueInformation == '') {
            $remarqueInformation = null;
        }

        $remarqueMethodeComptable = $post->get('remarqueMethodeComptable');
        if ($remarqueMethodeComptable == '') {

        }
        $remarquePrestationDemande = $post->get('remarquePrestationDemande');
        if ($remarquePrestationDemande == '') {
            $remarquePrestationDemande = null;
        }
        $remarquePrestationComptable = $post->get('remarquePrestationComptable');
        if ($remarquePrestationComptable == '') {
            $remarquePrestationComptable = null;
        }
        $remarquePieceAEnvoyer = $post->get('remarquePieceAEnvoyer');
        if ($remarquePieceAEnvoyer == '') {
            $remarquePieceAEnvoyer = null;
        }

        $res = -1;

        if (is_null($remarqueDossier)) {
            $remarqueDossier = new RemarqueDossier();
            $remarqueDossier->setDossier($dossier);
            switch ($typeRemarque) {
                case 2:
                    $remarqueDossier->setInformationDossier($remarqueInformation);
                    break;
                case 3:
                    $remarqueDossier->setMethodeComptable($remarqueMethodeComptable);
                    break;
                case 41:
                    $remarqueDossier->setPrestationDemande($remarquePrestationDemande);
                    break;
                case 42:
                    $remarqueDossier->setPrestationComptable($remarquePrestationComptable);
                    break;
                case 5:
                    $remarqueDossier->setPieceAEnvoyer($remarquePieceAEnvoyer);
                    break;
            }
            $res = 1;
        } else {
            switch ($typeRemarque) {
                case 2:
                    $remarqueDossier->setInformationDossier($remarqueInformation);
                    break;
                case 3:
                    $remarqueDossier->setMethodeComptable($remarqueMethodeComptable);
                    break;
                case 41:
                    $remarqueDossier->setPrestationDemande($remarquePrestationDemande);
                    break;
                case 42:
                    $remarqueDossier->setPrestationComptable($remarquePrestationComptable);
                    break;
                case 5:
                    $remarqueDossier->setPieceAEnvoyer($remarquePieceAEnvoyer);
                    break;
            }

            $res = 2;
        }

        /** @var  $utilisateur Utilisateur */
        $utilisateur = $this->getUser();

        if ($utilisateur->getAccesUtilisateur()->getId() == 7) {
            $em->persist($remarqueDossier);
            $em->flush();
        } else {
            $res = -2;
        }

        return new JsonResponse($res);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PHPExcel_Exception
     */
    public function exportExcelAction(Request $request)
    {
        /* Titre des colonnes */
//        if ($request->isXmlHttpRequest()) {

        $colNames = json_decode($request->request->get('colNames'));
        /* Nom des colonnes */
        $colModels = json_decode($request->request->get('colModel'));
        /* Valeur des colonnes */
        $rowDatas = json_decode($request->request->get('rowData'));
        /* Total des colonnes */
        $footerDatas = json_decode($request->request->get('footerData'));

        /* Header */
        $groupHead = json_decode($request->request->get('groupHeader'), true);


        $groupHeaders = $groupHead['groupHeaders'];


        setlocale(LC_TIME, 'fr-FR', 'fr');

        /* Création de l'Excel */
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        /* Titre */

        $title = "Recap_Dossier";

        $phpExcelObject->getProperties()->setCreator("picdata")
            ->setLastModifiedBy($this->getUser()->getPrenom() . ' ' . $this->getUser()->getNom())
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords($title)
            ->setCategory("Recap_Dossier");
        $title_ = 'RECAP DOSSIERS';

        $phpExcelObject->setActiveSheetIndex(0);

        /* Titre */
        $phpExcelObject
            ->getActiveSheet()
            ->setCellValue('A1', $title_);


        //Contenu du fichier Excel

        /* Titre */
//        $address = 'B3';
//        foreach ($groupHeaders as $colName) {
//
//            $oldAdress = $address;
//
//            if (trim($colName['titleText']) != "")
//            {
//                $phpExcelObject->getActiveSheet()
//                    ->setCellValue($address, str_replace('<br>', ' ', $colName['titleText']));
//                $phpExcelObject->getActiveSheet()
//                    ->getStyle($address)
//                    ->getBorders()
//                    ->getAllBorders()
//                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//
//                $phpExcelObject->getActiveSheet()
//                    ->getStyle($address)
//                    ->applyFromArray(
//                        array(
//                            'fill' => array(
//                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                                'color' => array('rgb' => 'c0c0c0')
//                            )
//                        )
//                    );
//                $phpExcelObject
//                    ->getActiveSheet()
//                    ->getStyle($address)
//                    ->getAlignment()
//                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                $split = \PHPExcel_Cell::coordinateFromString($address);
//
//                $nbCol = $colName['numberOfColumns'];
//
//                for ($i = 0; $i < $nbCol; $i++) {
//                    ++$split[0];
//                }
//
//                $splitMerge = $split;
//                $splitMerge[0] = decrementLetter($splitMerge[0]);
//
//
//                $adressMerge = implode($splitMerge);
//
//                $address = implode($split);
//
//                $phpExcelObject->getActiveSheet()->mergeCells("$oldAdress:$adressMerge");
//            }
//
//
//        }


        /* Liste des colonnes */
        $address = 'A4';

        $lastIndex = 0;
        $incrIndex = 0;
        $addChar = false;

        foreach ($colNames as $colName) {
            if (trim($colName) != "" && $colName != "<span class=\"fa fa-bookmark-o \" style=\"display:inline-block;\"/> Action") {
                $phpExcelObject->getActiveSheet()
                    ->setCellValue($address, str_replace('<br>', ' ', $colName));
                $phpExcelObject->getActiveSheet()
                    ->getStyle($address)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $phpExcelObject->getActiveSheet()
                    ->getStyle($address)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'c0c0c0')
                            )
                        )
                    );
                $phpExcelObject
                    ->getActiveSheet()
                    ->getStyle($address)
                    ->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $split = \PHPExcel_Cell::coordinateFromString($address);

                if ($split[0] == "Z" || $split[0] == "AZ") {
                    $lastIndex++;
                    $addChar = true;
                }
                ++$split[0];
                $address = implode($split);

                if ($lastIndex == 1 && $addChar == true) {
                    $address = "AA4";
                    $addChar = false;
                }
                if ($lastIndex == 2 && $addChar == true) {
                    $address = "AB4";
                    $addChar = false;
                }
            }
        }

        $col_models = [];

        /* Hauteur de la ligne au dessus des titres de colonnes */
        foreach ($colModels as $colModel) {
            if ($colModel->name != "rn" && $colModel->name != "recap-action") {
                $col_models[] = $colModel->name;
            }
        }

        $addressX = 'A';
        $addressY = 5;

        foreach ($rowDatas as $rowData) {
            foreach ($col_models as $col_model) {
                $address = $addressX . $addressY;
                if (property_exists($rowData, $col_model)) {

                    $val = $rowData->{$col_model};
                    if ($val == "." || $val == "NaN/NaN/NaN") {
                        $val = "";
                    }

                    $phpExcelObject->getActiveSheet()
                        ->setCellValue($address, $val);
                }
                $phpExcelObject->getActiveSheet()
                    ->getStyle($address)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                if ($addressX == "Z") {
                    $addressX = "AA";
                } else {
                    $addressX++;
                }

            }
            $addressY++;
            $addressX = 'A';
        }


        $addressX = 'A';

        foreach ($col_models as $col_model) {
            $address = $addressX . $addressY;
            if (property_exists($footerDatas, $col_model)) {
                if ($footerDatas->{$col_model} != '&nbsp;') {
                    $phpExcelObject->getActiveSheet()
                        ->setCellValue($address, str_replace(' ', '', $footerDatas->{$col_model}));
                }
                $phpExcelObject->getActiveSheet()
                    ->getStyle($address)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $phpExcelObject->getActiveSheet()
                    ->getStyle($address)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'f2dcdb')
                            )
                        )
                    );
            }

            if ($addressX == "Z") {
                $addressX = "AA";
            } else {
                $addressX++;
            }
        }

//
//        foreach (range('A', $phpExcelObject->getActiveSheet()->getHighestDataColumn()) as $col) {
//            $phpExcelObject->getActiveSheet()
//                ->getColumnDimension($col)
//                ->setAutoSize(true);
//        }

// Auto size columns for each worksheet
        foreach ($phpExcelObject->getWorksheetIterator() as $worksheet) {

            $phpExcelObject->setActiveSheetIndex($phpExcelObject->getIndex($worksheet));

            $sheet = $phpExcelObject->getActiveSheet();
            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            /** @var PHPExcel_Cell $cell */
            foreach ($cellIterator as $cell) {
                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
            }
        }


        $phpExcelObject->getActiveSheet()->setTitle("Recap");
        // Activet le premier onglet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $title . '.xlsx'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
//        }
//        else {
//            throw new AccessDeniedException('Accès refusé');
//        }
    }

    public function deboostAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            $dossier_id = Boost::deboost($request->request->get('idDossier'), $this);
            return new Response($dossier_id);
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    public function recapGridEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $dossierId = $request->request->get('id');

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);


            if (!is_null($dossier)) {

                $em = $this->getDoctrine()->getManager();

                $formeJuridiqueId = $request->request->get('recap-forme-juridique');
                $formeJuridique = null;
                if ($formeJuridiqueId != '') {
                    $formeJuridique = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeJuridique')
                        ->find($formeJuridiqueId);
                }
                $dossier->setFormeJuridique($formeJuridique);

                $activiteComCat3Id = $request->request->get('recap-code-ape');
                $activiteComCat3 = null;
                if ($activiteComCat3Id != '') {
                    $activiteComCat3 = $this->getDoctrine()
                        ->getRepository('AppBundle:ActiviteComCat3')
                        ->find($activiteComCat3Id);
                }
                $dossier->setActiviteComCat3($activiteComCat3);

                $dateCloture = $request->request->get('recap-date-premiere-cloture');
                if ($dateCloture != "" && $dateCloture != "NaN/NaN/NaN") {
                    $newDateCloture = \DateTime::createFromFormat("d/m/Y", $dateCloture);
                    if (!$newDateCloture) {
                        throw new \UnexpectedValueException("Could not parse the date: $dateCloture");
                    }

                    $dossier->setDateCloture($newDateCloture);
                } else {
                    $dossier->setDateCloture(null);
                }

                $dateDemarrage = $request->request->get('recap-date-demarrage');
                if ($dateDemarrage != "") {
                    $newDateDemarrage = \DateTime::createFromFormat("d/m/Y", $dateDemarrage);
                    if (!$newDateDemarrage) {
                        throw new \UnexpectedValueException("Could not parse the date: $newDateDemarrage");
                    }

                    $dossier->setDebutActivite($newDateDemarrage);
                } else {
                    $dossier->setDebutActivite(null);
                }

                $dateCloture = $request->request->get('recap-date-cloture');
                if ($dateCloture != "") {
                    $dossier->setCloture($dateCloture);
                } else {
                    $dossier->setDateCloture(null);
                }

                $mandataireId = $request->request->get('recap-type-mandataire');
                $responsableCsds = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('typeResponsable' => 0, 'dossier' => $dossier));
                if ($mandataireId != '') {

                    /** @var $responsableCsd ResponsableCsd */
                    if (count($responsableCsds) > 0) {

                        $mandataire = $this->getDoctrine()
                            ->getRepository('AppBundle:Mandataire')
                            ->find($mandataireId);

                        $responsableCsd = $responsableCsds[0];
                        $responsableCsd->setMandataire($mandataire);

                        $em->persist($responsableCsd);
                        $em->flush();

                    }
                }

                $nomMandataire = $request->request->get('recap-nom-mandataire');
                if ($nomMandataire != '') {
                    if (count($responsableCsds) > 0) {

                        $responsableCsd = $responsableCsds[0];

                        $nomPrenom = explode(";", $nomMandataire);
                        $nom = trim($nomPrenom[0]);
                        $prenom = trim($nomPrenom[1]);

                        $responsableCsd->setNom($nom);
                        $responsableCsd->setPrenom($prenom);

                        $em->persist($responsableCsd);
                        $em->flush();
                    }
                }

                $regimeFiscalId = $request->request->get('recap-regime-fiscal');
                $regimeFiscal = null;
                if ($regimeFiscalId != '') {
                    $regimeFiscal = $this->getDoctrine()
                        ->getRepository('AppBundle:RegimeFiscal')
                        ->find($regimeFiscalId);
                }
                $dossier->setRegimeFiscal($regimeFiscal);

                $regimeImpositionId = $request->request->get('recap-regime-imposition');
                $regimeImposition = null;
                if ($regimeImpositionId != '') {
                    $regimeImposition = $this->getDoctrine()
                        ->getRepository('AppBundle:RegimeImposition')
                        ->find($regimeImpositionId);
                }
                $dossier->setRegimeImposition($regimeImposition);

                $natureActiviteId = $request->request->get('recap-type-activite');
                $natureActivite = null;
                if ($natureActiviteId != '') {
                    $natureActivite = $this->getDoctrine()
                        ->getRepository('AppBundle:NatureActivite')
                        ->find($natureActiviteId);
                }
                $dossier->setNatureActivite($natureActivite);

                $formeActiviteId = $request->request->get('recap-forme-activite');
                $formeActivite = null;
                if ($formeActiviteId != '') {
                    $formeActivite = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeActivite')
                        ->find($formeActiviteId);

                    if ($formeActivite->getCode() != 'CODE_PROFESSION_LIBERALE') {
                        $dossier->setProfessionLiberale(null);
                    }
                }
                $dossier->setFormeActivite2($formeActivite);


                $professionLiberaleId = $request->request->get('recap-forme-activite');
                $professionLiberale = null;
                if ($professionLiberaleId != '' && $professionLiberale != ".") {
                    $professionLiberale = $this->getDoctrine()
                        ->getRepository('AppBundle:ProfessionLiberale')
                        ->find($professionLiberaleId);

                    if (!is_null($dossier->getFormeActivite())) {
                        if ($dossier->getFormeActivite()->getCode() == 'CODE_PROFESSION_LIBERALE') {
                            $dossier->setProfessionLiberale($professionLiberale);
                        }
                    } else {
                        $dossier->setProfessionLiberale($professionLiberale);
                    }


                }

                $modeVenteId = $request->request->get('recap-type-vente');
                $modeVente = null;
                if ($modeVenteId != '') {
                    $modeVente = $this->getDoctrine()
                        ->getRepository('AppBundle:ModeVente')
                        ->find($modeVenteId);
                }
                $dossier->setModeVente($modeVente);

                $regimeTvaId = $request->request->get('recap-tva-regime');
                /** @var  $regimeTva RegimeTva */
                $regimeTva = null;
                if ($regimeTvaId != '') {
                    $regimeTva = $this->getDoctrine()
                        ->getRepository('AppBundle:RegimeTva')
                        ->find($regimeTvaId);

                    if ($dossier->getRegimeTva() != $regimeTva) {
                        $dossier->setRegimeTva($regimeTva);

                        if (!is_null($dossier->getRegimeTva())) {
                            if ($regimeTva->getCode() === 'CODE_NON_SOUMIS' ||
                                $regimeTva->getCode() === 'CODE_FRANCHISE') {
                                $dossier->setTvaMode(null);
                                $dossier->setTvaFaitGenerateur(null);
                                $dossier->setTvaDate(null);

                                $tvaTauxDossier = $this->getDoctrine()
                                    ->getRepository('AppBundle:TvaTauxDossier')
                                    ->findBy(array('dossier' => $dossier));

                                /** @var  $tvaTauxDoss TvaTauxDossier */
                                foreach ($tvaTauxDossier as $tvaTauxDoss) {
                                    $em->remove($tvaTauxDoss);
                                }


                            } else {
                                $dossier->setTaxeSalaire(null);
                            }
                        }

                    }
                }

                $tvaMode = $request->request->get('recap-tva-mode');
                $tvaFaiGenerateur = $request->request->get('recap-tva-fait-generateur');
                $tvaDate = $request->request->get('recap-tva-date');
                $taxeSurSalaire = $request->request->get('recap-taxe-salaire');
                if (!is_null($dossier->getRegimeTva())) {

                    if ($dossier->getRegimeTva()->getCode() !== 'CODE_NON_SOUMIS' &&
                        $dossier->getRegimeTva()->getCode() !== 'CODE_FRANCHISE') {

                        if ($tvaMode != '' && $tvaMode != ".") {
                            $dossier->setTvaMode($tvaMode);
                        } else {
                            $dossier->setTvaMode(null);
                        }

                        if ($tvaFaiGenerateur != '' && $tvaFaiGenerateur != ".") {
                            $dossier->setTvaFaitGenerateur($tvaFaiGenerateur);
                        } else {
                            $dossier->setTvaFaitGenerateur(null);
                        }

                        if ($tvaDate != '' && $tvaDate != ".") {
                            $dossier->setTvaDate($tvaDate);
                        } else {
                            $dossier->setTvaDate(null);
                        }

                        $dossier->setTaxeSalaire(null);


                    } else {
                        $dossier->setTvaMode(null);
                        $dossier->setTvaFaitGenerateur(null);
                        $tvaTaux = $this->getDoctrine()
                            ->getRepository('AppBundle:TvaTauxDossier')
                            ->findBy(array('dossier' => $dossier));

                        foreach ($tvaTaux as $tva) {
                            $em->remove($tva);
                        }
                        $em->flush();

                        $dossier->setTvaDate(null);

                        if ($taxeSurSalaire != '') {
                            $dossier->setTaxeSalaire($taxeSurSalaire);
                        } else {
                            $dossier->setTaxeSalaire(null);
                        }

                    }
                } else {
                    if ($tvaMode != '' && $tvaMode != ".") {
                        $dossier->setTvaMode($tvaMode);
                    } else {
                        $dossier->setTvaMode(null);
                    }
                    if ($tvaFaiGenerateur != '' && $tvaFaiGenerateur != ".") {
                        $dossier->setTvaFaitGenerateur($tvaFaiGenerateur);
                    } else {
                        $dossier->setTvaFaitGenerateur(null);
                    }
                    if ($tvaDate != '' && $tvaDate != ".") {
                        $dossier->setTvaDate($tvaDate);
                    } else {
                        $dossier->setTvaDate(null);
                    }
                    if ($taxeSurSalaire != '' && $taxeSurSalaire != ".") {
                        $dossier->setTaxeSalaire($taxeSurSalaire);
                    } else {
                        $dossier->setTaxeSalaire(null);
                    }
                }


                $tvaTaux = $request->request->get('recap-tva-taux');
                if ($tvaTaux == '') {

                } else {

                }


                $methodeComptables = $this->getDoctrine()
                    ->getRepository('AppBundle:MethodeComptable')
                    ->findBy(array('dossier' => $dossier));

                $conventionComptableId = $request->request->get('recap-convention-comptable');
                $periodicite = $request->request->get('recap-periodicite');
                $vente = $request->request->get('recap-vente');
                $achat = $request->request->get('recap-achat');
                $banque = $request->request->get('recap-banque');
                $saisieOdPaye = $request->request->get('recap-saisie-od');
                $rapprochementBanque = $request->request->get('recap-rapp-banque');

                if (count($methodeComptables) > 0) {

                    $methodeComptable = $methodeComptables[0];

                    $conventionComptable = null;
                    if ($conventionComptableId != '') {
                        $conventionComptable = $this->getDoctrine()
                            ->getRepository('AppBundle:ConventionComptable')
                            ->find($conventionComptableId);
                    }
                    $methodeComptable->setConventionComptable($conventionComptable);

                    if ($periodicite != '') {
                        $methodeComptable->setTenueComptablilite($periodicite);
                    } else {
                        $methodeComptable->setTenueComptablilite(null);
                    }
                    if ($vente != '') {
                        $methodeComptable->setVente($vente);
                    } else {
                        $methodeComptable->setVente(null);
                    }

                    if ($achat != '') {
                        $methodeComptable->setAchat($achat);
                    } else {
                        $methodeComptable->setAchat(null);
                    }

                    if ($banque != '') {
                        $methodeComptable->setBanque($banque);
                    } else {
                        $methodeComptable->setBanque(null);
                    }

                    if ($saisieOdPaye != '') {
                        $methodeComptable->setSaisieOdPaye($saisieOdPaye);
                    } else {
                        $methodeComptable->setSaisieOdPaye(null);
                    }

                    if ($rapprochementBanque != '') {
                        $methodeComptable->setRapprochementBanque($rapprochementBanque);
                    } else {
                        $methodeComptable->setRapprochementBanque(null);
                    }


                    $em->persist($methodeComptable);
                    $em->flush();

                } else {

                    if ($vente != '' || $achat != '' || $banque != '' || $saisieOdPaye != '' || $rapprochementBanque != '' ||
                        $periodicite != '' || $conventionComptableId != ''
                    ) {
                        $methodeComptable = new MethodeComptable();

                        $conventionComptable = null;
                        if ($conventionComptableId != '') {
                            $conventionComptable = $this->getDoctrine()
                                ->getRepository('AppBundle:ConventionComptable')
                                ->find($conventionComptableId);
                        }
                        $methodeComptable->setConventionComptable($conventionComptable);

                        if ($periodicite != '') {
                            $methodeComptable->setTenueComptablilite($periodicite);
                        } else {
                            $methodeComptable->setTenueComptablilite(null);
                        }
                        if ($vente != '') {
                            $methodeComptable->setVente($vente);
                        } else {
                            $methodeComptable->setVente(null);
                        }

                        if ($achat != '') {
                            $methodeComptable->setAchat($achat);
                        } else {
                            $methodeComptable->setAchat(null);
                        }

                        if ($banque != '') {
                            $methodeComptable->setBanque($banque);
                        } else {
                            $methodeComptable->setBanque(null);
                        }

                        if ($saisieOdPaye != '') {
                            $methodeComptable->setSaisieOdPaye($saisieOdPaye);
                        } else {
                            $methodeComptable->setSaisieOdPaye(null);
                        }

                        if ($rapprochementBanque != '') {
                            $methodeComptable->setRapprochementBanque($rapprochementBanque);
                        } else {
                            $methodeComptable->setRapprochementBanque(null);
                        }

                        $methodeComptable->setDossier($dossier);

                        $em->persist($methodeComptable);
                        $em->flush();
                    }

                }


                $typePrestationId = $request->request->get('recap-type-prestation');
                $tva = $request->request->get('recap-tva');
                $liasseFiscal = $request->request->get('recap-liasse-fiscal');
                $accompteIs = $request->request->get('recap-accompte-is');
                $cice = $request->request->get('recap-cice');
                $cvae = $request->request->get('recap-cvae');
                $tvts = $request->request->get('recap-tvts');
                $das2 = $request->request->get('recap-das2');
                $cfe = $request->request->get('recap-cfe');
                $dividende = $request->request->get('recap-dividende');

                $declarationLiasse = $request->request->get('recap-tele-declaration-liasse');
                $teleDeclarationAutre = $request->request->get('recap-tele-declaration-autre');

                if ($typePrestationId != '') {
                    $typePrestation = $this->getDoctrine()
                        ->getRepository('AppBundle:TypePrestation')
                        ->find($typePrestationId);
                    $dossier->setTypePrestation2($typePrestation);
                } else {
                    $dossier->setTypePrestation2(null);
                }

                $em->persist($dossier);
                $em->flush();

                $prestationFiscales = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationFiscale')
                    ->findBy(array('dossier' => $dossier));


                if (count($prestationFiscales) > 0) {
                    /** @var PrestationFiscale $prestationFiscale */
                    $prestationFiscale = $prestationFiscales[0];

                    if ($tva != '' && $tva != ".") {
                        $prestationFiscale->setTva($tva);
                    } else {
                        $prestationFiscale->setTva(null);
                    }

                    if (!is_null($dossier->getTypePrestation2())) {
                        if ($dossier->getTypePrestation2()->getCode() != 'CODE_TENUE_COURANTE') {
                            if ($liasseFiscal != '' && $liasseFiscal != ".") {
                                $prestationFiscale->setLiasse($liasseFiscal);
                            } else {
                                $prestationFiscale->setLiasse(null);
                            }

                            if ($cice != '' && $cice != ".") {
                                $prestationFiscale->setCice($cice);
                            } else {
                                $prestationFiscale->setCice(null);
                            }

                            if ($declarationLiasse != '' && $declarationLiasse != ".") {
                                $prestationFiscale->setTeledeclarationLiasse($declarationLiasse);
                            } else {
                                $prestationFiscale->setTeledeclarationLiasse(null);
                            }

                        } else {
                            $prestationFiscale->setLiasse(null);
                            $prestationFiscale->setCice(null);
                            $prestationFiscale->setTeledeclarationLiasse(null);
                        }
                    } else {
                        if ($liasseFiscal != '' && $liasseFiscal != ".") {
                            $prestationFiscale->setLiasse($liasseFiscal);
                        } else {
                            $prestationFiscale->setLiasse(null);
                        }
                        if ($cice != '' && $cice != ".") {
                            $prestationFiscale->setCice($cice);
                        } else {
                            $prestationFiscale->setCice(null);
                        }
                        if ($declarationLiasse != '' && $declarationLiasse != ".") {
                            $prestationFiscale->setTeledeclarationLiasse($declarationLiasse);
                        } else {
                            $prestationFiscale->setTeledeclarationLiasse(null);
                        }
                    }

                    if ($accompteIs != '' && $accompteIs != ".") {
                        $prestationFiscale->setAcompteIs($accompteIs);
                    } else {
                        $prestationFiscale->setAcompteIs(null);
                    }


                    if ($cvae != '' && $accompteIs != ".") {
                        $prestationFiscale->setCvae($cvae);
                    } else {
                        $prestationFiscale->setCvae(null);
                    }

                    if ($tvts != '' && $tvts != ".") {
                        $prestationFiscale->setTvts($cvae);
                    } else {
                        $prestationFiscale->setTvts(null);
                    }

                    if ($das2 != '' && $das2 != ".") {
                        $prestationFiscale->setDas2($das2);
                    } else {
                        $prestationFiscale->setDas2(null);
                    }

                    if ($cfe != '' && $cfe != ".") {
                        $prestationFiscale->setCfe($cfe);
                    } else {
                        $prestationFiscale->setCfe(null);
                    }

                    if ($dividende != '' && $dividende != ".") {
                        $prestationFiscale->setDividende($dividende);
                    } else {
                        $prestationFiscale->setDividende(null);
                    }


                    if ($teleDeclarationAutre != '' && $teleDeclarationAutre != ".") {
                        $prestationFiscale->setTeledeclarationAutre($teleDeclarationAutre);
                    } else {
                        $prestationFiscale->setTeledeclarationAutre(null);
                    }

                    $em->persist($prestationFiscale);
                    $em->flush();
                } else {
                    if ($tva != '' || ($liasseFiscal != '' && $liasseFiscal != '.') || ($accompteIs != '' && $accompteIs != '.') ||
                        ($liasseFiscal != '' && $liasseFiscal != '.') || ($cice != '' && $cice != '.') ||
                        ($cvae != '' && $cvae != '.') || ($tvts != '' && $tvts != '.') || ($das2 != '' && $das2 != '.') ||
                        ($cfe != '' && $cfe != '.') || ($dividende != '' && $dividende != '.') || ($dividende != '' && $dividende != '.') ||
                        ($declarationLiasse != '' && $declarationLiasse != '.') || ($teleDeclarationAutre != '' && $teleDeclarationAutre != '.')
                    ) {
                        $prestationFiscale = new PrestationFiscale();

                        if ($tva != '') {
                            $prestationFiscale->setTva($tva);
                        } else {
                            $prestationFiscale->setTva(null);
                        }

                        if (!is_null($dossier->getTypePrestation2())) {
                            if ($dossier->getTypePrestation2()->getCode() != 'CODE_TENUE_COURANTE') {
                                if ($liasseFiscal != '') {
                                    $prestationFiscale->setLiasse($liasseFiscal);
                                } else {
                                    $prestationFiscale->setLiasse(null);
                                }

                                if ($cice != '' && $cice != ".") {
                                    $prestationFiscale->setCice($cice);
                                } else {
                                    $prestationFiscale->setCice(null);
                                }

                                if ($declarationLiasse != '') {
                                    $prestationFiscale->setTeledeclarationLiasse($declarationLiasse);
                                } else {
                                    $prestationFiscale->setTeledeclarationLiasse(null);
                                }

                            } else {
                                $prestationFiscale->setLiasse(null);
                                $prestationFiscale->setCice(null);
                                $prestationFiscale->setTeledeclarationLiasse(null);
                            }
                        } else {
                            if ($liasseFiscal != '' && $liasseFiscal != ".") {
                                $prestationFiscale->setLiasse($liasseFiscal);
                            } else {
                                $prestationFiscale->setLiasse(null);
                            }
                            if ($cice != '' && $cice != ".") {
                                $prestationFiscale->setCice($cice);
                            } else {
                                $prestationFiscale->setCice(null);
                            }
                            if ($declarationLiasse != '' && $declarationLiasse != ".") {
                                $prestationFiscale->setTeledeclarationLiasse($declarationLiasse);
                            } else {
                                $prestationFiscale->setTeledeclarationLiasse(null);
                            }
                        }

                        if ($accompteIs != '' && $accompteIs != ".") {
                            $prestationFiscale->setAcompteIs($accompteIs);
                        } else {
                            $prestationFiscale->setAcompteIs(null);
                        }

                        if ($cvae != '' && $cvae != ".") {
                            $prestationFiscale->setCvae($cvae);
                        } else {
                            $prestationFiscale->setCvae(null);
                        }

                        if ($tvts != '' && $tvts != ".") {
                            $prestationFiscale->setTvts($cvae);
                        } else {
                            $prestationFiscale->setTvts(null);
                        }

                        if ($das2 != '' && $das2 != ".") {
                            $prestationFiscale->setDas2($das2);
                        } else {
                            $prestationFiscale->setDas2(null);
                        }

                        if ($cfe != '' && $cfe != ".") {
                            $prestationFiscale->setCfe($cfe);
                        } else {
                            $prestationFiscale->setCfe(null);
                        }

                        if ($dividende != '' && $dividende != ".") {
                            $prestationFiscale->setDividende($dividende);
                        } else {
                            $prestationFiscale->setDividende(null);
                        }


                        if ($teleDeclarationAutre != '' && $teleDeclarationAutre != ".") {
                            $prestationFiscale->setTeledeclarationAutre($teleDeclarationAutre);
                        } else {
                            $prestationFiscale->setTeledeclarationAutre(null);
                        }

                        $prestationFiscale->setDossier($dossier);

                        $em->persist($prestationFiscale);
                        $em->flush();
                    }
                }


                if (!is_null($dossier->getRegimeFiscal())) {
                    if ($dossier->getRegimeFiscal()->getCode() == 'CODE_BA') {
                        $prestationFiscales = $this->getDoctrine()
                            ->getRepository('AppBundle:PrestationFiscale')
                            ->findBy(array('dossier' => $dossier));

                        if (count($prestationFiscales) > 0) {
                            $prestationFiscale = $prestationFiscales[0];

                            $prestationFiscale->setCvae(null);
                            $prestationFiscale->setTvts(null);
                            $prestationFiscale->setDas2(null);
                            $prestationFiscale->setCfe(null);
                            $prestationFiscale->setDividende(null);
                            $prestationFiscale->setTeledeclarationAutre(null);

                            if ($prestationFiscale->getLiasse() == 0) {
                                $prestationFiscale->setAcompteIs(null);
                                $prestationFiscale->setTeledeclarationLiasse(null);
                            }

                            $em->persist($prestationFiscale);
                            $em->flush();


                        }
                    }
                }


            }


            return new Response(2);


        } else {
            throw new AccessDeniedException('Accès refusé');
        }
    }

    public function checkDossierAction(Request $request, $json)
    {


        if ($request->isXmlHttpRequest()) {

            $instructionErreur = array();
            $informationErreur = array();
            $methodeComptableErreur = array();
            $prestationErreur = array();

            $post = $request->request;

            if (is_numeric($post->get('dossierId'))) {
                $dossierId = $post->get('dossierId');
            } else {
                $dossierId = Boost::deboost($post->get('dossierId'), $this);
            }

            $em = $this->getDoctrine()->getManager();


            /** @var  $dossier \AppBundle\Entity\Dossier */
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);


            //Identification dossier

            if ($dossier != null) {

                //Raha dossier efa lasa ny accusé-ny
                if ($dossier->getAccuseCreation() == 1 && $json == 1) {
                    return new Response('Accusé dejà envoyé');
                }

                //Instruction dossier
                $client = $dossier->getSite()->getClient();

                $instructionDossiers = $this->getDoctrine()
                    ->getRepository('AppBundle:InstructionDossier')
                    ->findBy(array('client' => $client));

                if (count($instructionDossiers) < 1) {
                    $instructionErreur[] = 'Rapprochement banque';
                    $instructionErreur[] = 'Suivi des chèques émis';
                    $instructionErreur[] = 'Gestion des dates d\'écritures';
//                    $instructionErreur[] = 'Logiciel utilisé';
                } else {
                    $instructionDossier = $instructionDossiers[0];

                    if (null === $instructionDossier->getRapprochementBanque()) {
                        $instructionErreur[] = 'Rapprochement banque';
                    } else {
                        if ($instructionDossier->getRapprochementBanque() != 0) {
                            if (null === $instructionDossier->getMethodeSuiviCheque()) {
                                $instructionErreur[] = 'Suivi des chèques émis';
                            }
                        }
                    }
                    if (null === $instructionDossier->getGestionDateEcriture()) {
                        $instructionErreur[] = 'Gestion des dates d\'ecritures';
                    }

//                    if(is_null($instructionDossier->getLogiciel())){
//                        $instructionErreur[] = 'Logiciel utilisé';
//                    }
                }

                //End instruction dossier


                if ($dossier->getSirenSte() == '' || null === $dossier->getSirenSte()) {

                    if (null !== $dossier->getFormeJuridique()) {
                        $codeJuridique = $dossier->getFormeJuridique()->getCode();

                        if ($codeJuridique !== 'CODE_CE' && $codeJuridique !== 'CODE_AUTRE' &&
                            $codeJuridique !== 'CODE_INDIVISION') {
                            $informationErreur[] = 'SIREN';
                        }
                    } else {
                        $informationErreur[] = 'SIREN';
                    }
                }

                if ($dossier->getRsSte() == '' || null === $dossier->getRsSte()) {
                    $informationErreur[] = 'Raison sociale';
                }

                if (null === $dossier->getFormeJuridique()) {
                    $informationErreur[] = 'Forme';
                }

                if (null === $dossier->getActiviteComCat3()) {
                    $informationErreur[] = 'Code APE';
                }

                if (null === $dossier->getDebutActivite()) {
                    $informationErreur[] = 'Date de début d\'activité';
                }

                if ($dossier->getCloture() == 0 || null === $dossier->getCloture()) {
                    $informationErreur[] = 'Date clôture';
                }

                //Date permière cloture
                if (null === $dossier->getDateCloture()) {

                    if (null !== $dossier->getDebutActivite() && null !== $dossier->getCloture()) {

                        try {

                            $dateCloture = $this->getDoctrine()
                                ->getRepository('AppBundle:Dossier')
                                ->getDateCloture($dossier, date('Y'));


                            $dayDiff = $dateCloture->diff($dossier->getDebutActivite())->days;

                            $monthDiff = $dayDiff / 30;


                            if ($monthDiff <= 23) {
                                $informationErreur[] = 'Date première clôture';
                            }
                        } catch (\Exception $e) {
                            $informationErreur[] = 'Date première clôture';
                        }
                    }
                }

                //Mandataire
                $mandataire = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('dossier' => $dossier, 'typeResponsable' => 0));

                if (count($mandataire) < 1) {
                    $informationErreur[] = 'Mandataire, Nom et prénom';
                } else {
                    /** @var  $mand ResponsableCsd */
                    $mand = $mandataire[0];


                    //Raha CODE_ASSOCIATION, CODE_INDIVIDUELLE, CODE_INDIVISION dia ts obligatoire ny mandataire

                    if (null !== $dossier->getFormeJuridique()) {
                        $codeFormeJuridique = $dossier->getFormeJuridique()->getCode();

                        if ($codeFormeJuridique !== 'CODE_INDIVISION'
//                        if ($codeFormeJuridique != 'CODE_ASSOCIATION' && $codeFormeJuridique != 'CODE_INDIVIDUELLE' &&
//                            $codeFormeJuridique != 'CODE_INDIVISION' && $codeFormeJuridique != 'CODE_ENTREPRISE_INDIVIDUELLE'
                        ) {
                            if (null === $mand->getMandataire()) {
                                $informationErreur[] = 'Mandataire';
                            }

                            if (($mand->getNom() == "" || null === $mand->getNom()) && ($mand->getPrenom() == "" || null === $mand->getPrenom())) {
                                $informationErreur[] = 'Nom; Prénom dirigeant';
                            }
                        }
                    } else {
                        $informationErreur[] = 'Forme Juridique';
                    }

                }

                if (null === $dossier->getRegimeFiscal()) {
                    $informationErreur[] = 'Régime Fiscal';
                }

                if (null === $dossier->getRegimeImposition()) {

                    if ($dossier->getRegimeFiscal() !== null) {
                        if ($dossier->getRegimeFiscal()->getCode() !== 'CODE_NS') {
                            $informationErreur[] = 'Régime d\'imposition';
                        }
                    } else {
                        $informationErreur[] = 'Régime d\'imposition';
                    }

                }

                if (null === $dossier->getNatureActivite()) {

                    if (null !== $dossier->getRegimeFiscal()) {
                        if ($dossier->getRegimeFiscal()->getCode() !== "CODE_BNC") {
                            $informationErreur[] = 'Type d\'activités';
                        }
                    } else {
                        $informationErreur[] = 'Type d\'activités';
                    }
                }

                if (null === $dossier->getFormeActivite()) {
                    $informationErreur[] = 'Forme activité';
                } else {
                    /** @var  $formeActivite FormeActivite */
                    $formeActivite = $dossier->getFormeActivite();

                    if ($formeActivite->getCode() === "CODE_PROFESSION_LIBERALE") {
                        if (null === $dossier->getProfessionLiberale()) {
                            $informationErreur[] = 'Profession libérale';
                        }
                    }
                }


                if (null === $dossier->getModeVente()) {
                    $informationErreur[] = 'Type de ventes';
                }

                if (null === $dossier->getRegimeTva()) {
                    $informationErreur[] = 'TVA Régime';
                } else {
                    //Non soumis
                    if ($dossier->getRegimeTva()->getCode() === 'CODE_NON_SOUMIS' ||
                        $dossier->getRegimeTva()->getCode() === 'CODE_FRANCHISE') {
                        if (null === $dossier->getTaxeSalaire()) {
                            $informationErreur[] = 'Taxe sur les salaires';
                        }
                    } else {
                        if (null === $dossier->getTvaFaitGenerateur()) {
                            $informationErreur[] = 'Tva fait générateur';
                        }

                        $tvaTaux = $this->getDoctrine()
                            ->getRepository('AppBundle:TvaTauxDossier')
                            ->findBy(array('dossier' => $dossier));

                        if (count($tvaTaux) < 1) {
                            $informationErreur[] = 'TVA taux';
                        }

                        if (null === $dossier->getTvaMode()) {
                            $informationErreur[] = 'TVA paiements';
                        }
                        if (null === $dossier->getTvaDate()) {
                            $informationErreur[] = 'Date déclaration';
                        }
                    }

                }


                $responsableDossier = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('dossier' => $dossier, 'typeResponsable' => 1));

                $responsableSite = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('site' => $dossier->getSite(), 'typeResponsable' => 1));


                if (count($responsableDossier) == 0 && count($responsableSite) == 0) {
                    $informationErreur[] = 'Responsable Dossier';
                }


                if (null === $dossier->getComptaSurServeur()) {
                    $informationErreur[] = 'Mode d\'obtention';
                } else {
                    if ($dossier->getComptaSurServeur() == 0 || $dossier->getComptaSurServeur() == 4) {
                        if (null === $dossier->getBalanceN1()) {
                            $informationErreur[] = 'Balance N-1';
                        }
                        if (null === $dossier->getGrandLivre()) {
                            $informationErreur[] = 'Grand livre N-1';
                        }
                        if (null === $dossier->getDernierRapprochementBanqueN1()) {
                            $informationErreur[] = 'Dernier rappro N–1';
                        }
                        if (null === $dossier->getEtatImmobilisationN1()) {
                            $informationErreur[] = 'Etat des immobilisations';
                        }

                        if (null === $dossier->getLiasseFiscaleN1()) {
                            $informationErreur[] = 'Liasse fiscale N-1';
                        }
                    }

                }

                if (null === $dossier->getComptaSurServeur() || $dossier->getComptaSurServeur() != 3) {
                    if (null === $dossier->getStatut()) {


                        if (null !== $dossier->getFormeJuridique()) {

                            if (!($dossier->getFormeJuridique()->getCode() === 'CODE_ENTREPRISE_INDIVIDUELLE' || $dossier->getFormeJuridique()->getCode() === 'CODE_INDIVIDUELLE')) {
                                $informationErreur[] = 'Statuts';
                            }
                        } else {
                            $informationErreur[] = 'Statuts';
                        }

                    }
                    if (null === $dossier->getKbis()) {

                        if (null !== $dossier->getFormeJuridique()) {

                            if (!($dossier->getFormeJuridique()->getCode() === 'CODE_ENTREPRISE_INDIVIDUELLE' || $dossier->getFormeJuridique()->getCode() === 'CODE_INDIVIDUELLE')) {
                                $informationErreur[] = 'Kbis';
                            }
                        } else {
                            $informationErreur[] = 'KBis';
                        }
                    }
//                    if (is_null($dossier->getBaux())) {
//                        $informationErreur[] = 'Baux';
//                    }
//                    if (is_null($dossier->getAssurance())) {
//                        $informationErreur[] = 'Assurances';
//                    }
//                    if (is_null($dossier->getAutre())) {
//                        $informationErreur[] = 'Autres ';
//                    }
//                    if (is_null($dossier->getEmprunt())) {
//                        $informationErreur[] = 'Emprunts';
//                    }
//                    if (is_null($dossier->getLeasing())) {
//                        $informationErreur[] = 'Leasing';
//                    }

                }

                //Mehtodes comptables
                $methodeComptable = $this->getDoctrine()
                    ->getRepository('AppBundle:MethodeComptable')
                    ->findBy(array('dossier' => $dossier));


                if (count($methodeComptable) < 1) {
                    $methodeComptableErreur[] = 'Convention comptable';
                    $methodeComptableErreur[] = 'Tenue de la comptabilité';
                    $methodeComptableErreur[] = 'Ventes';
                    $methodeComptableErreur[] = 'Achat';
                    $methodeComptableErreur[] = 'Banque';
                    $methodeComptableErreur[] = 'Saisie des OD de paye';
//                    $methodeComptableErreur[] = 'Analytique';
                } else {
                    $methCompt = $methodeComptable[0];

                    if (null === $methCompt->getConventionComptable()) {
                        $methodeComptableErreur[] = 'Convention comptable';
                    }
                    if (null === $methCompt->getTenueComptablilite()) {
                        $methodeComptableErreur[] = 'Tenue de la comptabilité';
                    }
                    if (null === $methCompt->getVente()) {
                        $methodeComptableErreur[] = 'Ventes';
                    }

                    if (null === $methCompt->getAchat()) {
                        $methodeComptableErreur[] = 'Achat';
                    }
                    if (null === $methCompt->getBanque()) {
                        $methodeComptableErreur[] = 'Banque';
                    }
                    if (null === $methCompt->getSaisieOdPaye()) {
                        $methodeComptableErreur[] = 'Saisie des OD de paye';
                    }
//                    if (is_null($methCompt->getAnalytique())) {
//                        $methodeComptableErreur[] = 'Analytique';
//                    }
                }


                //Prestation demandés
                if (null === $dossier->getTypePrestation2()) {
                    $prestationErreur[] = 'Prestations comptables';
                }

                $prestation = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationFiscale')
                    ->findBy(array('dossier' => $dossier));

                $tenuecourante = false;

                if ($dossier->getTypePrestation2() !== null) {
                    if ($dossier->getTypePrestation2()->getCode() === 'CODE_TENUE_COURANTE') {
                        $tenuecourante = true;
                    }
                }

                if (!$tenuecourante) {

                    if (count($prestation) < 1) {
                        $prestationErreur[] = 'TVA';
                        $prestationErreur[] = 'Accomptes IS et solde';
                        $prestationErreur[] = 'CVAE';
                        $prestationErreur[] = 'TVTS';
                        $prestationErreur[] = 'DAS 2';
                        $prestationErreur[] = 'CFE';
                        $prestationErreur[] = 'Dividendes';
                        $prestationErreur[] = 'Liasse Fiscale';
                        $prestationErreur[] = 'CICE';
//                        $prestationErreur[] = 'DEJ';
                        $prestationErreur[] = 'DECLOYER';
                        $prestationErreur[] = 'DEB';

                    } else {

                        /** @var  $prest PrestationFiscale */
                        $prest = $prestation[0];

                        $tvaNonSoumis = false;
                        if (null !== $dossier->getRegimeTva()) {
                            if ($dossier->getRegimeTva()->getCode() === 'CODE_NON_SOUMIS' ||
                                $dossier->getRegimeTva()->getCode() === 'CODE_FRANCHISE') {
                                $tvaNonSoumis = true;
                            }
                        }
                        if (null === $prest->getTva()) {
                            if (!$tvaNonSoumis) {
                                $prestationErreur[] = 'TVA';
                            }
                        }

                        $codePrestation = "";
                        if (null !== $dossier->getTypePrestation2()) {
                            $codePrestation = $codePrestation = $dossier->getTypePrestation2()->getCode();
                        }

                        if ($codePrestation !== "CODE_TENUE_COURANTE") {
                            $codeRegimeFiscalBa = false;
                            if (null !== $dossier->getRegimeFiscal()) {

                                if ($dossier->getRegimeFiscal()->getCode() === "CODE_BA") {
                                    $liasseOui = -1;

                                    if (null === $prest->getLiasse()) {
                                        $prestationErreur[] = 'Liasse Fiscale';
                                    } else {
                                        $liasseOui = $prest->getLiasse();
                                    }

                                    if ($liasseOui != 0) {
                                        if (null === $prest->getTeledeclarationLiasse()) {
                                            $prestationErreur[] = 'Télédéclarations liasse';
                                        }
                                        if (null === $prest->getAcompteIs()) {
                                            $prestationErreur[] = 'Accomptes IS et solde';
                                        }
                                    }

//                                if (is_null($prest->getAutres()) || $prest->getAutres() == "") {
//                                    $prestationErreur[] = 'Autres';
//                                }
                                    $codeRegimeFiscalBa = true;
                                }

                            }
                            if (!$codeRegimeFiscalBa) {

                                $codeRegimeFiscalBnc = false;
                                $codeRegimeFiscalBicIr = false;
                                $codeLmpLmnp = false;
                                $codeNs = false;

                                if (null !== $dossier->getRegimeFiscal()) {
                                    if ($dossier->getRegimeFiscal()->getCode() === 'CODE_BNC') {
                                        $codeRegimeFiscalBnc = true;
                                    }

                                    if ($dossier->getRegimeFiscal()->getCode() === 'CODE_BIC_IR') {
                                        $codeRegimeFiscalBicIr = true;
                                    }

                                    if ($dossier->getRegimeFiscal()->getCode() === 'CODE_LMP_LMNP') {
                                        $codeLmpLmnp = true;
                                    }

                                    if ($dossier->getRegimeFiscal()->getCode() === 'CODE_NS') {
                                        $codeNs = true;
                                    }
                                }

                                if (!$codeNs) {
                                    if (null === $prest->getLiasse()) {
                                        $prestationErreur[] = 'Liasse Fiscale';
                                    }
                                }

                                //Raha tsy BNC, BICIR, LMPN_LMNP dia obligatoire
                                if (!$codeRegimeFiscalBnc && !$codeRegimeFiscalBicIr && !$codeLmpLmnp) {
                                    if (null === $prest->getAcompteIs()) {
                                        if ($prest->getLiasse() === 1)
                                            $prestationErreur[] = 'Accomptes IS et solde';
                                    }
                                }

                                if (!$codeNs) {
                                    if (null === $prest->getCice()) {
                                        $prestationErreur[] = 'CICE';
                                    }
                                }

                                if (!$codeNs) {
                                    if (null === $prest->getCvae()) {
                                        $prestationErreur[] = 'CVAE';
                                    }
                                }

                                if (!$codeNs) {
                                    if (null === $prest->getTvts()) {
                                        $prestationErreur[] = 'TVTS';
                                    }
                                }

                                if (null === $prest->getDas2()) {
                                    $prestationErreur[] = 'DAS 2';
                                }

                                if (!$codeNs) {
                                    if (null === $prest->getCfe()) {
                                        $prestationErreur[] = 'CFE';
                                    }
                                }

                                //Raha tsy BNC, BICIR, LMPN_LMNP dia obligatoire
                                if (!$codeRegimeFiscalBnc && !$codeRegimeFiscalBicIr && !$codeLmpLmnp && !$codeNs) {
                                    if (null === $prest->getDividende()) {
                                        $prestationErreur[] = 'Dividendes';
                                    }
                                }

                                if (null === $prest->getTeledeclarationLiasse()) {
                                    if ($prest->getLiasse() === 1) {
                                        $prestationErreur[] = 'Télédéclarations liasse';
                                    }
                                }
                                if (null === $prest->getTeledeclarationAutre()) {
                                    $prestationErreur[] = 'Télédéclarations autres';
                                }
//                            if (is_null($prest->getAutres())) {
//                                $prestationErreur[] = 'Autres';
//                            }
                            }
                        }
                    }
                }

//                if(!is_null($dossier->getRegimeFiscal())){
//                    $regimeFiscalAga = array('CODE_BIC_IR','CODE_BNC','CODE_BA');
//
//                    if(count($prestation)> 0){
//                        $prest = $prestation[0];
//
//                        if(!is_null($prest->getLiasse())) {
//
//                            if ($prest->getLiasse() == 1 && in_array($dossier->getRegimeFiscal()->getCode(), $regimeFiscalAga)) {
//
//                                $agas = $this->getDoctrine()
//                                    ->getRepository('AppBundle:AgaCga')
//                                    ->findBy(array('dossier' => $dossier));
//
//                                if (count($agas) > 0) {
//                                    $aga = $agas[0];
//
//                                    if ($aga->getAdherant() == 1) {
//                                        if ($aga->getNom() == '' || is_null($aga->getNom())) {
//                                            $prestationErreur[] = 'Nom AGA/CGA';
//                                        }
//
//                                        if ($aga->getSiren() == '' || is_null($aga->getSiren())) {
//                                            $prestationErreur[] = 'Siren AGA';
//                                        }
//
//                                        if ($aga->getNumeroAdhesion() == '' || is_null($aga->getNumeroAdhesion())) {
//                                            $prestationErreur[] = 'Numero adhésion';
//                                        }
//                                    }
//                                } //Lister-na daholo ny champ AGA
//                                else {
//                                    $prestationErreur[] = 'Adhérant AGA/CGA';
//                                    $prestationErreur[] = 'Nom AGA/CGA';
//                                    $prestationErreur[] = 'Siren AGA';
//                                    $prestationErreur[] = 'Numero adhésion';
//                                }
//
//                            }
//                        }
//                    }
//
//                }


                $estDossierValide = false;
                if (count($informationErreur) == 0 && count($methodeComptableErreur) == 0 && count($prestationErreur) == 0 && count($instructionErreur) == 0) {
                    $estDossierValide = true;
                }


                $destinataires = array();
                $destinataireCcies = array();
                $destinataireCopies = array();

                //Scriptura
                $destinataireCcies[] = 'pjlcastellan@gmail.com';
                $destinataireCcies[] = 'mgarciaballesteros@gmail.com';
                $destinataireCcies[] = 'maharo@scriptura.biz';

                $destinataireCopies[] = 'arq@scriptura.biz';

                $from = strtolower($dossier->getSite()->getClient()->getNom()) . "." . strtolower($dossier->getSite()->getNom()) . "@notification.biz";

                $from = str_replace(" ", "", $from);

                $client = $dossier->getSite()->getClient();

                $responsableScriptura = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('client' => $client, 'typeCsd' => 5, 'envoiMail' => 1));

                /** @var  $resp ResponsableCsd */
                foreach ($responsableScriptura as $resp) {
                    if($resp->getEnvoiMail() === 1) {
                        if (!in_array($resp->getEmail(), $destinataireCcies)) {

                            if ($resp->getEmail() != null && $resp->getEmail() != '') {

                                $destinataireCcies[] = $resp->getEmail();
                            }
                        }
                    }
                }


                if (count($responsableDossier) > 0) {
                    /** @var  $rDossier ResponsableCsd */
                    foreach ($responsableDossier as $rDossier) {
                        if ($rDossier->getEnvoiMail() == 1) {

                            if (!in_array($rDossier->getEmail(), $destinataires)) {
                                if ($rDossier->getEmail() != null && $rDossier->getEmail() != '') {
                                    $destinataires[] = $rDossier->getEmail();
                                }
                            }
                        }
                    }
                }

                if (count($responsableSite) > 0) {
                    /** @var  $rSite ResponsableCsd */
                    foreach ($responsableSite as $rSite) {
                        if ($rSite->getEnvoiMail() == 1) {
                            if (!in_array($rSite->getEmail(), $destinataires)) {
                                if ($rSite->getEmail() != null && $rSite->getEmail() != '') {
                                    $destinataires[] = $rSite->getEmail();
                                }
                            }
                        }
                    }
                }

                /** @var Utilisateur $utilisateur */
                $utilisateur = $this->getUser();

                if (count($destinataires) == 0) {
                    $destinataires[] = $utilisateur->getEmail();
                }

//                    $destinataires = array();
//                    $destinataireCcies = array();
//                    $destinataireCopies = array();
//                    $destinataires[] = 'maharoarijaona@gmail.com';

                //0: mi-check ny dosser efa misy
                if ($json == 0) {
                    if ($estDossierValide) {
                        $dossier->setActive(1);
                        //Raha efa valide ilay izy dia mila mandefa mail hoe efa créé ilay dossier

                        if ($dossier->getAccuseCreation() < 2) {


                            $message = \Swift_Message::newInstance()
                                ->setSubject("Dossier: " . $dossier->getNom() . " créé")
                                ->setFrom($from, $from)
                                ->setTo($destinataires);

                            foreach ($destinataireCcies as $cci) {
                                $message->addBCc($cci);
                            }

                            foreach ($destinataireCopies as $copy) {
                                $message->addCc($copy);
                            }

                            $message->setBody(
                                $this->renderView('InfoPerdosBundle:Emails:notificationValidationDossier.html.twig', array(
                                    'dossier' => $dossier,
                                    'client' => $client,
                                    'embed' => $message->embed(\Swift_Image::fromPath('img/scriptura/logo.png'))
                                ))
                                , 'text/html');

                            $this->get('mailer')
                                ->send($message);

                            $dossier->setAccuseCreation(2);
                        }


                        $res = 1;
                    } else {
                        //Administrateur
                        if ($utilisateur->getAccesUtilisateur()->getType() <= 2) {
                            $dossier->setActive(1);
                            $res = 1;
                        } else {
                            //Raha mbola tsy nisy image dia desactiver-na
                            if(!$this->getDoctrine()->getRepository('AppBundle:Image')->imageInDossier($dossier)){
                                $dossier->setActive(0);
                                $res = 0;
                            }
                            //Raha efa misy dia tsy kitihana
                            else{
                                $res = 1;
                            }

                        }
                    }

                    $em->persist($dossier);

                    $em->flush();

                    return new JsonResponse($res);

                } //Json 1: notification création
                else {

                    if (!$estDossierValide) {


                        $message = \Swift_Message::newInstance()
                            ->setSubject("Dossier: " . $dossier->getNom() . " en création")
                            ->setFrom($from, $from)
                            ->setTo($destinataires);

                        foreach ($destinataireCcies as $cci) {
                            $message->addBCc($cci);
                        }

                        foreach ($destinataireCopies as $copy) {
                            $message->addCc($copy);
                        }

                        $message->setBody(
                            $this->renderView('InfoPerdosBundle:Emails:notificationCreationDossier.html.twig', array(
                                'instruction' => $instructionErreur,
                                'information' => $informationErreur,
                                'methodes' => $methodeComptableErreur,
                                'prestation' => $prestationErreur,
                                'dossier' => $dossier,
                                'client' => $client,
                                'utilisateur' => $utilisateur,
                                'estValide' => $estDossierValide,
                                'embed' => $message->embed(\Swift_Image::fromPath('img/scriptura/logo.png'))
                            ))
                            , 'text/html');


                        $dossier->setActive(0);

                        if ($utilisateur->getAccesUtilisateur()->getType() <= 2) {
                            $dossier->setActive(1);
                        }

                        if ($dossier->getAccuseCreation() < 1) {

                            $this->get('mailer')
                                ->send($message);

                            //Atao mise à jour ilay accusé rehefa lasa ilay mail de création
                            $dossier->setAccuseCreation(1);
                            $em->persist($dossier);
                            $em->flush();

                            return new Response($this->render('InfoPerdosBundle:Emails:notificationCreationDossier.html.twig', array(
                                'instruction' => $instructionErreur,
                                'information' => $informationErreur,
                                'methodes' => $methodeComptableErreur,
                                'prestation' => $prestationErreur,
                                'dossier' => $dossier,
                                'client' => $client,
                                'utilisateur' => $utilisateur,
                                'estValide' => $estDossierValide,
                                'embed' => $message->embed(\Swift_Image::fromPath('img/scriptura/logo.png'))
                            )));

                        } else {
                            return new Response('Accusé création dejà envoyé');
                        }


                    } else {

                        if ($dossier->getAccuseCreation() < 2) {

                            $message = \Swift_Message::newInstance()
                                ->setSubject("Dossier: " . $dossier->getNom() . " créé")
//                                ->setFrom('noreply@newpicdata.fr', 'noreply@newpicdata.fr')
                                ->setFrom($from, $from)
                                ->setTo($destinataires);

                            foreach ($destinataireCcies as $cci) {
                                $message->addBCc($cci);
                            }

                            foreach ($destinataireCopies as $copy) {
                                $message->addCc($copy);
                            }

                            $message->setBody(
                                $this->renderView('InfoPerdosBundle:Emails:notificationValidationDossier.html.twig', array(
                                    'dossier' => $dossier,
                                    'client' => $client,
                                    'embed' => $message->embed(\Swift_Image::fromPath('img/scriptura/logo.png'))
                                ))
                                , 'text/html');


                            $this->get('mailer')
                                ->send($message);


                            $dossier->setAccuseCreation(2);
                            $em->persist($dossier);
                            $em->flush();

                            return new Response($this->render('InfoPerdosBundle:Emails:notificationValidationDossier.html.twig', array(
                                'dossier' => $dossier,
                                'client' => $client,
                                'embed' => $message->embed(\Swift_Image::fromPath('img/scriptura/logo.png'))
                            )));

                        } else {
                            return new Response('Accusé validation dejà envoyé');
                        }

                    }

                }


            } else {
                return new JsonResponse(-1);
            }


        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }

    }


    public function checkModiDossierAction(Request $request)
    {

        $post = $request->request;

        if (is_numeric($post->get('dossierId'))) {
            $dossierId = $post->get('dossierId');
        } else {
            $dossierId = Boost::deboost($post->get('dossierId'), $this);
        }


        /** @var  $dossier \AppBundle\Entity\Dossier */
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        if (null !== $dossier) {


            $destinataires = array();
            $destinataireCcies = array();
            $destinataireCopies = array();

            //Scriptura
            $destinataireCcies[] = 'pjlcastellan@gmail.com';
//            $destinataireCcies[] = 'mgarciaballesteros@gmail.com';
            $destinataireCcies[] = 'maharo@scriptura.biz';

            $destinataireCopies[] = 'arq@scriptura.biz';

            $from = strtolower($dossier->getSite()->getClient()->getNom()) . "." . strtolower($dossier->getSite()->getNom()) . "@notification.biz";

            $from = str_replace(" ", "", $from);

            $client = $dossier->getSite()->getClient();


            $responsableDossier = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('dossier' => $dossier, 'typeResponsable' => 1, 'envoiMail' => 1));

            $responsableSite = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('site' => $dossier->getSite(), 'typeResponsable' => 1, 'envoiMail' => 1));

            $responsableScriptura = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('client' => $client, 'typeCsd' => 5, 'envoiMail' => 1));

            /** @var  $resp ResponsableCsd */
            foreach ($responsableScriptura as $resp) {

                if($resp->getEnvoiMail() === 1) {
                    if (!in_array($resp->getEmail(), $destinataireCcies)) {

                        if ($resp->getEmail() != null && $resp->getEmail() != '') {
                            $destinataireCcies[] = $resp->getEmail();
                        }
                    }
                }
            }


//            if (count($responsableDossier) > 0) {
//                /** @var  $rDossier ResponsableCsd */
//                foreach ($responsableDossier as $rDossier) {
//                    if ($rDossier->getEnvoiMail() == 1) {
//
//                        if (!in_array($rDossier->getEmail(), $destinataires)) {
//                            if ($rDossier->getEmail() != null && $rDossier->getEmail() != '') {
//                                $destinataires[] = $rDossier->getEmail();
//                            }
//                        }
//                    }
//                }
//            }

//            if (count($responsableSite) > 0) {
//                /** @var  $rSite ResponsableCsd */
//                foreach ($responsableSite as $rSite) {
//                    if ($rSite->getEnvoiMail() == 1) {
//                        if (!in_array($rSite->getEmail(), $destinataires)) {
//                            if ($rSite->getEmail() != null && $rSite->getEmail() != '') {
//                                $destinataires[] = $rSite->getEmail();
//                            }
//                        }
//                    }
//                }
//            }

            /** @var Utilisateur $utilisateur */
            $utilisateur = $this->getUser();

            if (count($destinataires) == 0) {
                $destinataires[] = $utilisateur->getEmail();
            }


            $em = $this->getDoctrine()->getManager();



            /** @var LogInfoperdos[] $logInfoPerdos */
            $logInfoPerdos = $this->getDoctrine()
                ->getRepository('AppBundle:LogInfoperdos')
                ->findBy(
                    array(
                        'dossier' => $dossier,
                        'utilisateur' => $utilisateur,
                        'mail' => 0
                    ),
                    array(
                        'tab' => 'ASC',
                        'bloc' => 'ASC',
                        'date' => 'ASC'
                    )
                );

            $dateModif = "";

            if(count($logInfoPerdos) == 0){
                return new Response('En cours de création, pas de modification');
            }

            else{
                if(null !== $logInfoPerdos[0]->getDate()){
                    $dateModif = $logInfoPerdos[0]->getDate()->format("d/m/Y");
                }


                $ancienneInstruction = "";
                $nouvelleInstruction = "";

                foreach ($logInfoPerdos as $log){
                    if($log->getTab() == 3 && $log->getBloc() == 5){
                        $ancienneInstruction = $log->getValeurAncien();
                        $nouvelleInstruction = $log->getValeurNouveau();
                    }
                }
            }

            $client = $dossier->getSite()
                ->getClient();


//            $destinataires = array();
//            $destinataireCcies = array();
//            $destinataireCopies = array();
//            $destinataires[] = 'maharoarijaona@gmail.com';


            $message = \Swift_Message::newInstance()
                ->setSubject("Dossier: " . $dossier->getNom() . " modifié")
                ->setFrom($from, $from)
                ->setTo($destinataires);

            foreach ($destinataireCcies as $cci) {
                $message->addBCc($cci);
            }

//            foreach ($destinataireCopies as $copy) {
//                $message->addCc($copy);
//            }

            $message->setBody(
                $this->renderView('InfoPerdosBundle:Emails:notificationModification.html.twig', array(
                    'utilisateur' => $utilisateur,
                    'logInfoPerdos' => $logInfoPerdos,
                    'dossier' => $dossier,
                    'client' => $client,
                    'dateModif' => $dateModif,
                    'nouvelleInstruction' => $nouvelleInstruction,
                    'ancienneInstruction' => $ancienneInstruction,
                    'embed' => $message->embed(\Swift_Image::fromPath('img/scriptura/logo.png'))
                ))
                , 'text/html');

            $this->get('mailer')
                ->send($message);


            foreach ($logInfoPerdos as $log) {
                $log->setMail(1);
                $em->flush();
            }


            return new Response('Mail envoyé');
        } else {
            return new Response('1');
        }

    }


    public function instructionSaisieEditAction(Request $request){


        $post = $request->request;

        $dossierId = Boost::deboost($post->get('dossierId'), $this);

        $instructionTxt = preg_replace('/\<[\/]{0,1}div[^\>]*\>/i', '', $post->get('instruction'));

        $instructionTxtComp = strip_tags($instructionTxt);
        $instructionTxtComp = preg_replace("/\r\n|\r|\n/",'',$instructionTxtComp);
        $instructionTxtComp = str_replace(" ", "", $instructionTxtComp);

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        if(!is_null($dossier)){

            $em =$this->getDoctrine()
                ->getEntityManager();

            $instructionSaisies = $this->getDoctrine()
                ->getRepository('AppBundle:InstructionSaisie')
                ->findBy(array('dossier'=>$dossier));

            if(count($instructionSaisies)> 0){

                $instructionSaisie = $instructionSaisies[0];

                $utilisateur = $this->getUser();

                if($dossier->getAccuseCreation() >= 1){
                    $oldVal = "";
                    $oldValComp = "";

                    if(!is_null($instructionSaisie->getInstruction())){
                        $oldVal = preg_replace('/\<[\/]{0,1}div[^\>]*\>/i', '', $instructionSaisie->getInstruction());

                        $oldValComp = strip_tags($oldVal);
                        $oldValComp = preg_replace("/\r\n|\r|\n/",'',$oldValComp);
                        $oldValComp = str_replace(" ", "", $oldValComp);
                    }

                    if( $oldValComp != $instructionTxtComp){
                        $log = new LogInfoperdos();
                        $log->setDate(new \DateTime());
                        $log->setDossier($dossier);
                        $log->setUtilisateur($utilisateur);
                        $log->setTab(3);
                        $log->setBloc(5);

                        $log->setChamp('Instruction Saisie');


                        $log->setValeurNouveau($instructionTxt);
                        $log->setValeurAncien($oldVal);

                        $em->persist($log);
                        $em->flush();

                    }
                }

                if($instructionTxtComp == ''){
                    $em->remove($instructionSaisie);
                    $em->flush();
                }

                else {
                    $instructionSaisie->setInstruction($instructionTxt);
                    $em->flush();
                }


                return new Response(2);
            }
            else{

                if($instructionTxtComp != "") {

                    if($dossier->getAccuseCreation() >= 1) {

                        $utilisateur = $this->getUser();

                        $log = new LogInfoperdos();
                        $log->setDate(new \DateTime());
                        $log->setDossier($dossier);
                        $log->setUtilisateur($utilisateur);
                        $log->setTab(3);
                        $log->setBloc(5);

                        $log->setChamp('Instruction Saisie');

                        $log->setValeurNouveau($instructionTxt);
                        $log->setValeurAncien("");

                        $em->persist($log);
                        $em->flush();


                    }


                    $instructionSaisie = new InstructionSaisie();
                    $instructionSaisie->setDossier($dossier);
                    $instructionSaisie->setInstruction($instructionTxt);

                    $em->persist($instructionSaisie);
                    $em->flush();
                }

                return new Response(1);
            }



        }
        else{
            return new Response('Dossier introuvable');
        }


    }


}


function decrementLetter($l) {
    return chr(ord($l) - 1);
}