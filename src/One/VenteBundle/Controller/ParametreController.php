<?php

/**
 * Project: oneup
 * Author : Mamy Rakotonirina
 * Created on : 11 oct. 2017 12:53:08
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\OneNotification;
use AppBundle\Entity\ReglePaiementClient;
use AppBundle\Entity\ReglePaiementDossier;
use AppBundle\Entity\ResponsableCsd;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneParametre;

/**
 * Description of ParametreController
 *
 */
class ParametreController extends Controller
{

    public function indexAction(Request $request)
    {
        return $this->render('@OneVente/Parametre/index.html.twig');
    }

    public function showAction(Request $request)
    {
        $dossierId = $request->query
            ->get('dossierId');

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($dossierId, $this));

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

        $indicateurGroups = $this->getDoctrine()
            ->getRepository('AppBundle:IndicateurGroup')
            ->findBy(array(), array('libelle' => 'asc'));

        $trancheEffectifs = $this->getDoctrine()
            ->getRepository('AppBundle:TrancheEffectif')
            ->findBy(array(), array('id' => 'asc'));

        $typePrestations = $this->getDoctrine()
            ->getRepository('AppBundle:TypePrestation')
            ->findBy(array(), array('id' => 'asc'));


        $idClient = Boost::deboost($request->request->get('clientId'), $this);


        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($idClient);

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


        $mandataires = $this->getDoctrine()
            ->getRepository('AppBundle:Mandataire')
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


        $formeJuridiques = $this->getDoctrine()
            ->getRepository('AppBundle:FormeJuridique')
            ->findBy(array(), array('libelle' => 'asc'));

        $resCsd = null;
        if (null !== $dossier) {
            $resCsd = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('typeResponsable' => 0, 'dossier' => $dossier));
        }

        /** @var  $responsableCsd  ResponsableCsd */
        $responsableCsd = null;

        if ($resCsd != null) {
            $responsableCsd = $resCsd[0];
        }


        /** @var OneNotification[] $notifications */
        $notifications = $this->getDoctrine()
            ->getRepository('AppBundle:OneNotification')
            ->findBy(array('dossier' => $dossier));
        $notification = null;
        if (count($notifications) > 0) {
            $notification = $notifications[0];
        }

        return $this->render('@OneVente/Parametre/edit.html.twig', array(
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
            'mandataires' => $mandataires,
            'responsableCsd' => $responsableCsd,
            'methodeSuiviCheques' => $methodeSuiviCheques,
            'gestionDateEcritures' => $gestionDateEcritures,
            'instrucitonTypes' => $instructionTypes,
            'logiciels' => $logiciels,
            'indicateurGroups' => $indicateurGroups,
            'reglePaiementDossierFournisseur' => $reglePaiementDossierFournisseur,
            'reglePaiementDossierClient' => $reglePaiementDossierClient,
            'reglePaiementClientFournisseur' => $reglePaiementClientFournisseur,
            'reglePaiementClientClient' => $reglePaiementClientClient,
            'trancheEffectifs' => $trancheEffectifs,
            'typePrestations' => $typePrestations,
            'notification' => $notification
        ));


    }

    public function saveNotificationAction(Request $request, $dossierId)
    {

        if ($dossierId === -1) {

            $response = array('type' => 'error', 'action' => 'edit');
            return new JsonResponse($response);
        }

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($dossierId, $this));

        if (null !== $dossier) {

            $posted = $request->request->all();

            $notifications = $this->getDoctrine()
                ->getRepository('AppBundle:OneNotification')
                ->findBy(array('dossier' => $dossier));

            $notification = null;

            if (count($notifications) > 0) {
                $notification = $notifications[0];
            }


            $status = 0;

            $opportuniteStatus = 0;
            $tacheStatus = 0;
            $appelStatus = 0;
            $paiementStatus = 0;

            $opportuniteAvant = 1;
            $tacheAvant = 1;
            $appelAvant = 1;
            $paiementAvant = 1;

            $opportuniteDelais = 0;
            $tacheDelais = 0;
            $appelDelais = 0;
            $paiementDelais = 0;

            $opportuniteDelaisType = 0;
            $tacheDelaisType = 0;
            $appelDelaisType = 0;
            $paiementDelaisType = 0;

            if (isset($posted['notification'])) {
                if ($posted['notification'] === 'on') {
                    $status = 1;
                }
            }

            if ($status !== 0) {

                if (isset($posted['opportunite'])) {
                    if ($posted['opportunite'] === 'on') {
                        $opportuniteStatus = 1;
                    }
                }

                if ($opportuniteStatus !== 0) {
                    $opportuniteAvant = $posted['opportunite-avant'];
                    $opportuniteDelais = $posted['opportunite-details'];
                    $opportuniteDelaisType = $posted['opportunite-details-type'];
                }

                if (isset($posted['tache'])) {

                    if ($posted['tache'] === 'on') {
                        $tacheStatus = 1;
                    }
                }

                if ($tacheStatus !== 0) {
                    $tacheAvant = $posted['tache-avant'];
                    $tacheDelais = $posted['tache-details'];
                    $tacheDelaisType = $posted['tache-details-type'];
                }

                if (isset($posted['appel'])) {
                    if ($posted['appel'] === 'on') {
                        $appelStatus = 1;
                    }
                }

                if ($appelStatus !== 0) {
                    $appelAvant = $posted['appel-avant'];
                    $appelDelais = $posted['appel-details'];
                    $appelDelaisType = $posted['appel-details-type'];
                }


                if (isset($posted['paiement'])) {
                    if ($posted['paiement'] === 'on') {
                        $paiementStatus = 1;
                    }
                }

                if ($paiementStatus !== 0) {
                    $paiementAvant = $posted['paiement-avant'];
                    $paiementDelais = $posted['paiement-details'];
                    $paiementDelaisType = $posted['paiement-details-type'];
                }

            }


            $em = $this->getDoctrine()->getEntityManager();

            if (null === $notification) {
                $notification = new OneNotification();
                $notification->setDossier($dossier);

                $notification->setStatus($status);

                $notification->setOpportunite($opportuniteStatus);
                $notification->setOpportuniteAvant($opportuniteAvant);
                $notification->setOpportuniteDelais($opportuniteDelais);
                $notification->setOpportuniteDelaisType($opportuniteDelaisType);

                $notification->setTache($tacheStatus);
                $notification->setTacheAvant($tacheAvant);
                $notification->setTacheDelais($tacheDelais);
                $notification->setTacheDelaisType($tacheDelaisType);

                $notification->setAppel($appelStatus);
                $notification->setAppelAvant($appelAvant);
                $notification->setAppelDelais($appelDelais);
                $notification->setAppelDelaisType($appelDelaisType);

                $notification->setPaiement($paiementStatus);
                $notification->setPaiementAvant($paiementAvant);
                $notification->setPaiementDelais($paiementDelais);
                $notification->setPaiementDelaisType($paiementDelaisType);

                $notification->setUtilisateur($this->getUser());

                $em->persist($notification);
                $em->flush();


                $response = array('type' => 'success', 'action' => 'add');

                return new JsonResponse($response);

            }

            $notification->setStatus($status);

            $notification->setOpportunite($opportuniteStatus);
            $notification->setOpportuniteAvant($opportuniteAvant);
            $notification->setOpportuniteDelais($opportuniteDelais);
            $notification->setOpportuniteDelaisType($opportuniteDelaisType);

            $notification->setTache($tacheStatus);
            $notification->setTacheAvant($tacheAvant);
            $notification->setTacheDelais($tacheDelais);
            $notification->setTacheDelaisType($tacheDelaisType);

            $notification->setAppel($appelStatus);
            $notification->setAppelAvant($appelAvant);
            $notification->setAppelDelais($appelDelais);
            $notification->setAppelDelaisType($appelDelaisType);

            $notification->setPaiement($paiementStatus);
            $notification->setPaiementAvant($paiementAvant);
            $notification->setPaiementDelais($paiementDelais);
            $notification->setPaiementDelaisType($paiementDelaisType);

            $notification->setUtilisateur($this->getUser());

            $em->flush();

            $response = array('type' => 'success', 'action' => 'edit');

            return new JsonResponse($response);
        }

        $response = array('type' => 'error', 'action' => 'add');

        return new JsonResponse($response);

    }
}
