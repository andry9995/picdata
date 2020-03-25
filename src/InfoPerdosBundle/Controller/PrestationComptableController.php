<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 17/01/2017
 * Time: 10:55
 */
namespace InfoPerdosBundle\Controller;

use AppBundle\Entity\IndicateurSpecGroup;
use AppBundle\Entity\LogInfoperdos;
use AppBundle\Entity\PrestationDemande;
use AppBundle\Entity\PrestationFiscale;
use AppBundle\Entity\PrestationGestion;
use AppBundle\Entity\PrestationJuridique;
use Proxies\__CG__\AppBundle\Entity\Dossier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class PrestationComptableController extends Controller
{
    public function editInfoPerdosPrestFiscalAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $tva = $post->get('tva');
            if ($tva == '') {
                $tva = null;
            }

            $accompteIsSolde = $post->get('accompteIsSolde');
            if ($accompteIsSolde == '') {
                $accompteIsSolde = null;
            }

            $liasseFiscale = $post->get('liasseFiscale');
            if ($liasseFiscale == '') {
                $liasseFiscale = null;
            }

            $cice = $post->get('cice');
            if ($cice == '') {
                $cice = null;
            }

            $cvae = $post->get('cvae');
            if ($cvae == '') {
                $cvae = null;
            }

            $tvts = $post->get('tvts');
            if ($tvts == '') {
                $tvts = null;
            }

            $das2 = $post->get('das2');
            if ($das2 == '') {
                $das2 = null;
            }

            $cfe = $post->get('cfe');
            if ($cfe == '') {
                $cfe = null;
            }

            $dividende = $post->get('dividende');
            if ($dividende == '') {
                $dividende = null;
            }

            $teledeclarationLiasse = $post->get('teledeclarationLiasse');
            if ($teledeclarationLiasse == '') {
                $teledeclarationLiasse = null;
            }

            $teledeclarationAutre = $post->get('teledeclarationAutre');
            if ($teledeclarationAutre == '') {
                $teledeclarationAutre = null;
            }

            $autres = $post->get('autres');
            if ($autres == '') {
                $autres = null;
            }

            $deb = $post->get('deb');
            if($deb == ''){
                $deb = null;
            }

            $dej = $post->get('dej');
            if($dej == ''){
                $dej = null;
            }

            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            if ($idDossier == 0) {
                return new Response(-1);
            } else {
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);

                //Rehefa Tenue propre dia descativer ny Liasse fiscal & Cice
                if (!is_null($dossier->getTypePrestation2())) {

                    if($dossier->getTypePrestation2()->getCode() == "CODE_TENUE_COURANTE") {
                        $liasseFiscale = null;
                        $cice = null;
                    }
                }

                /* @var $prestationDemande PrestationFiscale */
                $prestationDemande = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationFiscale')
                    ->getPrestaitonFiscaleByDossier($dossier);

                //Nouveau prestation fiscale
                if ($prestationDemande == null) {
                    try {

                        //**************ENREGISTREMENT LOG**************\\

                        $utilisateur = $this->getUser();

                        if ($dossier->getAccuseCreation()>=1) {
                            if (!is_null($tva)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($tva) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('TVA ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($liasseFiscale)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($liasseFiscale) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('Liasse fiscale ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($accompteIsSolde)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($accompteIsSolde) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('Accomptes IS et solde ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($cice)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($cice) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = 'Si nécessaire';
                                }

                                $log->setChamp('Cice ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($tvts)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($tvts) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('TVTS ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($das2)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($das2) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('DAS2 ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($cfe)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($cfe) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('CFE ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($dividende)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($dividende) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = 'Si nécessaire';
                                        break;
                                }

                                $log->setChamp('Dividendes ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($teledeclarationLiasse)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($teledeclarationLiasse) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('Télédeclarations liasse ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($teledeclarationAutre)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($teledeclarationAutre) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('CVAE ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($autres)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';

                                if(!is_null($autres)){
                                    $newVal = $autres;
                                }

                                $log->setChamp('Autres ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($deb)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($deb) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('DEB ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }


                            if (!is_null($dej)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $newVal = '';
                                switch ($dej) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('DEJ ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }


                        }
                        //***********FIN LOG***********\\



                        $prestationDemande = new PrestationFiscale();

                        $prestationDemande->setDossier($dossier);

                        $prestationDemande->setAcompteIs($accompteIsSolde);
                        $prestationDemande->setLiasse($liasseFiscale);
                        $prestationDemande->setCice($cice);
                        $prestationDemande->setCvae($cvae);
                        $prestationDemande->setTvts($tvts);
                        $prestationDemande->setDas2($das2);
                        $prestationDemande->setCfe($cfe);
                        $prestationDemande->setDividende($dividende);
                        $prestationDemande->setTva($tva);

                        $prestationDemande->setTeledeclarationAutre($teledeclarationAutre);
                        $prestationDemande->setTeledeclarationLiasse($teledeclarationLiasse);
                        $prestationDemande->setAutres($autres);

                        $prestationDemande->setDeb($deb);
                        $prestationDemande->setDej($dej);

                        $em->persist($prestationDemande);
                        $em->flush();

                        return new Response(1);
                    } catch (Exception $e) {
                        return new Response($e->getMessage());
                    }
                } //Mise à jour
                else {
                    try {


                        //**************ENREGISTREMENT LOG**************\\

                        $utilisateur = $this->getUser();

                        if ($dossier->getAccuseCreation()>=1) {

                            if ($prestationDemande->getTva() != $tva) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $oldVal = '';
                                switch ($prestationDemande->getTva()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                }

                                $newVal = '';
                                switch ($tva) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('TVA ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getLiasse() != $liasseFiscale) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $oldVal = '';
                                switch ($prestationDemande->getLiasse()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                }

                                $newVal = '';
                                switch ($liasseFiscale) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('Liasse fiscale ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getAcompteIs() != $accompteIsSolde) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $oldVal = '';
                                switch ($prestationDemande->getAcompteIs()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                }


                                $newVal = '';
                                switch ($accompteIsSolde) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('Accomptes IS et solde ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getCice() != $cice) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $oldVal = '';
                                switch ($prestationDemande->getCice()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                    case 2:
                                        $oldVal = 'Si nécessaire';
                                }

                                $newVal = '';
                                switch ($cice) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = 'Si nécessaire';
                                }

                                $log->setChamp('Cice ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getTvts() != $tvts) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $oldVal = '';
                                switch ($prestationDemande->getTvts()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                }

                                $newVal = '';
                                switch ($tvts) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('TVTS ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getDas2() != $das2) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);


                                $oldVal = '';
                                switch ($prestationDemande->getDas2()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                }

                                $newVal = '';
                                switch ($das2) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('DAS2 ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getCice() != $cfe) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $oldVal = '';
                                switch ($prestationDemande->getCfe()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                }


                                $newVal = '';
                                switch ($cfe) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('CFE ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getDividende() != $dividende) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $oldVal = '';
                                switch ($prestationDemande->getDividende()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                    case 2:
                                        $oldVal = 'Si nécessaire';
                                        break;
                                }

                                $newVal = '';
                                switch ($dividende) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = 'Si nécessaire';
                                        break;
                                }

                                $log->setChamp('Dividendes ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getTeledeclarationLiasse() != $teledeclarationLiasse) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $oldVal = '';
                                switch ($prestationDemande->getTeledeclarationLiasse()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                }

                                $newVal = '';
                                switch ($teledeclarationLiasse) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('Télédeclarations liasse ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getTeledeclarationAutre() != $teledeclarationAutre) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);


                                $oldVal = '';
                                switch ($prestationDemande->getTeledeclarationAutre()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                }


                                $newVal = '';
                                switch ($teledeclarationAutre) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('CVAE ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getAutres() != $autres) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $oldVal = '';
                                if(!is_null($prestationDemande->getAutres())){
                                    $oldVal = $autres;
                                }

                                $newVal = '';
                                if(!is_null($autres)) {
                                    $newVal = $autres;
                                }

                                $log->setChamp('Autres ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }


                            if ($prestationDemande->getDeb() != $deb) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(2);

                                $oldVal = '';
                                switch ($prestationDemande->getDeb()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                }

                                $newVal = '';
                                switch ($deb) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('DEB ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }
                        }


                        if ($prestationDemande->getDej() != $dej) {
                            $log = new LogInfoperdos();
                            $log->setDate(new \DateTime());
                            $log->setDossier($dossier);
                            $log->setUtilisateur($utilisateur);
                            $log->setTab(3);
                            $log->setBloc(2);

                            $oldVal = '';
                            switch ($prestationDemande->getDej()) {
                                case 1:
                                    $oldVal = 'Oui';
                                    break;
                                case 0:
                                    $oldVal = 'Non';
                                    break;
                            }

                            $newVal = '';
                            switch ($dej) {
                                case 1:
                                    $newVal = 'Oui';
                                    break;
                                case 0:
                                    $newVal = 'Non';
                                    break;
                            }

                            $log->setChamp('DEJ ');
                            $log->setValeurAncien($oldVal);
                            $log->setValeurNouveau($newVal);

                            $em->persist($log);
                            $em->flush();
                        }

                        //***********FIN LOG***********\\




                        $prestationDemande->setAcompteIs($accompteIsSolde);
                        $prestationDemande->setLiasse($liasseFiscale);
                        $prestationDemande->setCice($cice);
                        $prestationDemande->setCvae($cvae);
                        $prestationDemande->setTvts($tvts);
                        $prestationDemande->setDas2($das2);
                        $prestationDemande->setCfe($cfe);
                        $prestationDemande->setDividende($dividende);
                        $prestationDemande->setTva($tva);

                        $prestationDemande->setTeledeclarationAutre($teledeclarationAutre);
                        $prestationDemande->setTeledeclarationLiasse($teledeclarationLiasse);
                        $prestationDemande->setAutres($autres);

                        $prestationDemande->setDej($dej);
                        $prestationDemande->setDeb($deb);

                        $em->persist($prestationDemande);
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

    public function editInfoPerdosPrestFiscalV2Action(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            if ($idDossier == 0) {
                return new Response(-1);
            } else {
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);

                $value = $post->get('value');

                if ($value == '') {
                    $value = null;
                }

                $field = $post->get('field');


                /* @var $prestationDemande PrestationFiscale */
                $prestationDemande = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationFiscale')
                    ->getPrestaitonFiscaleByDossier($dossier);

                //Nouveau prestation fiscale
                if ($prestationDemande == null) {
                    try {
                        $prestationDemande = new PrestationFiscale();
                        $prestationDemande->setDossier($dossier);
                        $prestationDemande->{"set$field"}($value);

                        $em->persist($prestationDemande);
                        $em->flush();

                        return new Response(1);
                    } catch (Exception $e) {
                        return new Response($e->getMessage());
                    }
                } //Mise à jour
                else {
                    try {
                        $prestationDemande->{"set$field"}($value);

                        $em->persist($prestationDemande);
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

    public function editInfoPerdosPrestGestionAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $situation = $post->get('situation');
            if ($situation == '') {
                $situation = null;
            }

            $indicateur = $post->get('indicateur');
            if ($indicateur == '') {
                $indicateur = null;
            }

            $budget = $post->get('budget');
            if ($budget == '') {
                $budget = null;
            }

            $tableauBord = $post->get('tableauBord');
            if ($tableauBord == '') {
                $tableauBord = null;
            }

            $idDossier = Boost::deboost($post->get('dossierId'), $this);

            if ($idDossier == 0) {
                return new Response(-1);
            } else {
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);


                $indicateurSpecGroup = $this->getDoctrine()
                    ->getRepository('AppBundle:IndicateurSpecGroup')
                    ->findOneBy(array('dossier'=>$dossier));


                if($indicateur != ''){

                    $indicateurGroup = $this->getDoctrine()
                        ->getRepository('AppBundle:IndicateurGroup')
                        ->find($indicateur);

                    //Ajouter-na any @IndicateurSpecGroup raha mbola tsy misy
                    if(is_null($indicateurSpecGroup)){
                       $indicateurSpecGroup = new IndicateurSpecGroup();
                       $indicateurSpecGroup->setDossier($dossier);
                       $indicateurSpecGroup->setIndicateurGroup($indicateurGroup);
                    }
                    //Atao mise à jour raha efa misy
                    else{
                        $indicateurSpecGroup->setIndicateurGroup($indicateurGroup);

                    }

                    $em->persist($indicateurSpecGroup);
                }
                //Supprimer-na any @ IndicateurSpecGroup raha vide
                else{
                    if($indicateurSpecGroup != null) {
                        $em->remove($indicateurSpecGroup);
                    }
                }

                $em->flush();




                /* @var $prestationDemande PrestationGestion */
                $prestationDemande = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationGestion')
                    ->getPrestationGestionByDossier($dossier);

                //Nouveau methode comptable
                if ($prestationDemande == null) {
                    try {

                        $utilisateur = $this->getUser();

                        //**************ENREGISTREMENT LOG**************\\
                        if ($dossier->getAccuseCreation()>=1) {

                            if (!is_null($situation)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(3);

                                $newVal = '';
                                switch ($situation) {
                                    case 1:
                                        $newVal = 'Mois';
                                        break;
                                    case 2:
                                        $newVal = 'Trimestre';
                                        break;
                                    case 3:
                                        $newVal = 'Semestre';
                                        break;
                                    case 4:
                                        $newVal = 'Non applicable';
                                        break;
                                }

                                $log->setChamp('Situation ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }
                        }
                        //***********FIN LOG***********\\






                        $prestationDemande = new PrestationGestion();

                        $prestationDemande->setDossier($dossier);

                        $prestationDemande->setSituation($situation);
//                        $prestationDemande->setIndicateur($indicateur);
                        $prestationDemande->setBudget($budget);
                        $prestationDemande->setTableauBord($tableauBord);


                        $em->persist($prestationDemande);
                        $em->flush();

                        return new Response(1);
                    } catch (Exception $e) {
                        return new Response($e->getMessage());
                    }
                } //Mise à jour
                else {
                    try {



                        //**************ENREGISTREMENT LOG**************\\
                        $utilisateur = $this->getUser();
                        if ($dossier->getAccuseCreation()>=1) {

                            if ($prestationDemande->getSituation() != $situation) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(3);

                                $oldVal = '';
                                switch ($prestationDemande->getSituation()) {
                                    case 1:
                                        $oldVal = 'Mois';
                                        break;
                                    case 2:
                                        $oldVal = 'Trimestre';
                                        break;
                                    case 3:
                                        $oldVal = 'Semestre';
                                        break;
                                    case 4:
                                        $oldVal = 'Non applicable';
                                        break;
                                }

                                $newVal = '';
                                switch ($situation) {
                                    case 1:
                                        $newVal = 'Mois';
                                        break;
                                    case 2:
                                        $newVal = 'Trimestre';
                                        break;
                                    case 3:
                                        $newVal = 'Semestre';
                                        break;
                                    case 4:
                                        $newVal = 'Non applicable';
                                        break;
                                }

                                $log->setChamp('Situation ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }


                        }
                        //***********FIN LOG***********\\








                        $prestationDemande->setSituation($situation);
//                        $prestationDemande->setIndicateur($indicateur);
                        $prestationDemande->setBudget($budget);
                        $prestationDemande->setTableauBord($tableauBord);

                        $em->persist($prestationDemande);
                        $em->flush();

                        $em->persist($prestationDemande);
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

    public function editInfoPerdosPrestGestionV2Action(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $idDossier = Boost::deboost($post->get('dossierId'), $this);


            if ($idDossier == 0) {
                return new Response(-1);
            } else {

                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);

                /* @var $prestationDemande PrestationGestion */
                $prestationDemande = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationGestion')
                    ->getPrestationGestionByDossier($dossier);

                $field = $post->get('field');
                $value = $post->get('value');

                if ($value == '') {
                    $value = null;
                }

                if ($field != 'Indicateur') {

                    //Nouveau methode comptable
                    if ($prestationDemande == null) {
                        try {
                            $prestationDemande = new PrestationGestion();

                            $prestationDemande->setDossier($dossier);

                            $prestationDemande->{"set$field"}($value);

                            $em->persist($prestationDemande);
                            $em->flush();

                            return new Response(1);
                        } catch (Exception $e) {
                            return new Response($e->getMessage());
                        }
                    } //Mise à jour
                    else {
                        try {
                            $prestationDemande->{"set$field"}($value);

                            $em->persist($prestationDemande);
                            $em->flush();

                            $em->persist($prestationDemande);
                            $em->flush();

                            return new Response(2);
                        } catch (Exception $e) {
                            return new Response($e->getMessage());
                        }
                    }
                }
                else{
                    $indicateurSpecGroup = $this->getDoctrine()
                        ->getRepository('AppBundle:IndicateurSpecGroup')
                        ->findOneBy(array('dossier'=>$dossier));

                    $resp = 0;

                    if($value != null){

                        $indicateurGroup = $this->getDoctrine()
                            ->getRepository('AppBundle:IndicateurGroup')
                            ->find($value);

                        //Ajouter-na any @IndicateurSpecGroup raha mbola tsy misy
                        if(is_null($indicateurSpecGroup)){
                            $indicateurSpecGroup = new IndicateurSpecGroup();
                            $indicateurSpecGroup->setDossier($dossier);
                            $indicateurSpecGroup->setIndicateurGroup($indicateurGroup);

                            $resp = 1;
                        }
                        //Atao mise à jour raha efa misy
                        else{
                            $indicateurSpecGroup->setIndicateurGroup($indicateurGroup);

                            $resp = 2;
                        }

                        $em->persist($indicateurSpecGroup);
                    }
                    //Supprimer-na any @ IndicateurSpecGroup raha vide
                    else{
                        $em->remove($indicateurSpecGroup);

                        $resp = 3;
                    }

                    $em->flush();

                    return new Response($resp);
                }
            }

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editInfoPerdosPrestJuridiqueAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $rapportGestion = $post->get('rapportGestion');
            if ($rapportGestion == '') {
                $rapportGestion = null;
            }

            $assembleeOrdinaire = $post->get('assembleeOrdinaire');
            if ($assembleeOrdinaire == '') {
                $assembleeOrdinaire = null;
            }


            $idDossier = Boost::deboost($post->get('dossierId'), $this);


            if ($idDossier == 0) {
                return new Response(-1);
            } else {
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);

                /* @var $prestationDemande PrestationJuridique */
                $prestationDemande = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationJuridique')
                    ->getPrestationJurique($dossier);

                //Nouveau methode comptable
                if ($prestationDemande == null) {
                    try {

                        $utilisateur = $this->getUser();

                        //**************ENREGISTREMENT LOG**************\\
                        if ($dossier->getAccuseCreation()>=1) {

                            if (!is_null($rapportGestion)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(4);



                                $newVal = '';
                                switch ($rapportGestion) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('Rapport de gestion ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if (!is_null($assembleeOrdinaire)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(4);



                                $newVal = '';
                                switch ($assembleeOrdinaire) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('Assemblée générale ordinaire');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                        }
                        //***********FIN LOG***********\\



                        $prestationDemande = new PrestationJuridique();

                        $prestationDemande->setDossier($dossier);

                        $prestationDemande->setRapportGestion($rapportGestion);
                        $prestationDemande->setAssOrdAnnuelle($assembleeOrdinaire);

                        $em->persist($prestationDemande);
                        $em->flush();

                        return new Response(1);
                    } catch (Exception $e) {
                        return new Response($e->getMessage());
                    }
                } //Mise à jour
                else {
                    try {


                        //**************ENREGISTREMENT LOG**************\\
                        $utilisateur = $this->getUser();

                        if ($dossier->getAccuseCreation()>=1) {

                            if ($prestationDemande->getRapportGestion() != $rapportGestion) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(4);



                                $oldVal = '';
                                switch ($prestationDemande->getRapportGestion()) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $newVal = '';
                                switch ($rapportGestion) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('Rapport de gestion ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if ($prestationDemande->getAssOrdAnnuelle() != $assembleeOrdinaire) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(3);
                                $log->setBloc(4);


                                $oldVal = '';
                                switch ($prestationDemande->getAssOrdAnnuelle()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                }

                                $newVal = '';
                                switch ($assembleeOrdinaire) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                }

                                $log->setChamp('Assemblée générale ordinaire');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                        }
                        //***********FIN LOG***********\\



                        $prestationDemande->setRapportGestion($rapportGestion);
                        $prestationDemande->setAssOrdAnnuelle($assembleeOrdinaire);

                        $em->persist($prestationDemande);
                        $em->flush();

                        $em->persist($prestationDemande);
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


    public function editInfoPerdosPrestJuridiqueV2Action(Request $request){
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $idDossier = Boost::deboost($post->get('dossierId'), $this);


            if ($idDossier == 0) {
                return new Response(-1);
            } else {
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);

                /* @var $prestationDemande PrestationJuridique */
                $prestationDemande = $this->getDoctrine()
                    ->getRepository('AppBundle:PrestationJuridique')
                    ->getPrestationJurique($dossier);

                $field = $post->get('field');
                $value = $post->get('value');

                if($value == ''){
                    $value = null;
                }

                //Nouveau methode comptable
                if ($prestationDemande == null) {
                    try {
                        $prestationDemande = new PrestationJuridique();

                        $prestationDemande->setDossier($dossier);

                        $prestationDemande->{"set$field"}($value);

                        $em->persist($prestationDemande);
                        $em->flush();

                        return new Response(1);
                    } catch (Exception $e) {
                        return new Response($e->getMessage());
                    }
                } //Mise à jour
                else {
                    try {
                        $prestationDemande->{"set$field"}($value);

                        $em->persist($prestationDemande);
                        $em->flush();

                        $em->persist($prestationDemande);
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
}