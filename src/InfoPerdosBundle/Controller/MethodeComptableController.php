<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 17/01/2017
 * Time: 09:32
 */


namespace InfoPerdosBundle\Controller;


use AppBundle\Entity\LogInfoperdos;
use AppBundle\Entity\MethodeComptable;
use Proxies\__CG__\AppBundle\Entity\Dossier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MethodeComptableController extends Controller
{
    public function editInfoPerdosConventionAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $post = $request->request;

            $em = $this->getDoctrine()->getEntityManager();

            $conventionComptableId = $post->get('conventionComptable');
            $conventionComptable = null;

            if($conventionComptableId!='') {
                $conventionComptable = $this->getDoctrine()
                    ->getRepository('AppBundle:ConventionComptable')
                    ->find($conventionComptableId);
            }
            else {
//                $res = array('estInsere' => 0, 'message' => 'Convention comptable');
//                return new JsonResponse($res);
            }

            $idDossier =  Boost::deboost($post->get('dossierId'),$this);


            if($idDossier == 0)
            {
                //Erreur: le dossier n'existe pas encore
                $res = array('estInsere' => -1, 'message' => 'Dossier');
                return new JsonResponse($res);
            }

            else
            {
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);
                
                /* @var $methodeComptable MethodeComptable */
                $methodeComptable = $this->getDoctrine()
                    ->getRepository('AppBundle:MethodeComptable')
                    ->getMethodeComptableByDossier($dossier) ;

                //Nouveau methode comptable
                if($methodeComptable == null)
                {
                    try
                    {

                        //**************ENREGISTREMENT LOG**************\\

                        $utilisateur = $this->getUser();

                        if ($dossier->getAccuseCreation()>=1 && !is_null($conventionComptable)){

                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(1);

                                $log->setChamp('Convention ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($conventionComptable->getLibelle());

                                $em->persist($log);
                                $em->flush();
                        }

                        //***********FIN LOG***********\\

                        $methodeComptable = new MethodeComptable();

                        $methodeComptable->setDossier($dossier);
                        $methodeComptable->setConventionComptable($conventionComptable);

                        $em->persist($methodeComptable);
                        $em->flush();

                        return new Response(1);
                    }

                    catch (Exception $e)
                    {
                        return new Response($e->getMessage());
                    }
                }

                //Mise à jour
                else
                {
                    try
                    {


                        $utilisateur = $this->getUser();

                        if ($dossier->getAccuseCreation()>=1) {

                            if ($methodeComptable->getConventionComptable() != $conventionComptable) {

                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(1);

                                $newVal = "";
                                if(!is_null($conventionComptable)){
                                    $newVal = $conventionComptable->getLibelle();
                                }

                                $oldVal = "";
                                if(!is_null($methodeComptable->getConventionComptable())){
                                    $oldVal = $methodeComptable->getConventionComptable()
                                        ->getLibelle();
                                }

                                $log->setChamp('Convention ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }
                        }

                        $methodeComptable->setConventionComptable($conventionComptable);

                        $em->persist($methodeComptable);
                        $em->flush();

                        return new Response(2);
                    }
                    catch (Exception $e)
                    {
                        return new Response($e->getMessage());
                    }
                }
            }


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editMethodeComptableAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $post = $request->request;
            $em = $this->getDoctrine()->getEntityManager();

            $idDossier =  Boost::deboost($post->get('dossierId'),$this);

            if($idDossier == 0)
            {
                //Erreur: le dossier n'existe pas encore
                $res = array('estInsere' => -1, 'message' => 'Dossier');
                return new JsonResponse($res);
            }

            else
            {
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);

                $vente = $post->get('vente');
                if($vente =='')
                {
//                    $res = array('estInsere' => 0, 'message' => 'Vente');
//                    return new JsonResponse($res);
                    $vente = null;
                }

                $achat = $post->get('achat');
                if($achat == '')
                {
//                    $res = array('estInsere' => 0, 'message' => 'Achat');
//                    return new JsonResponse($res);
                    $achat = null;
                }

                $banque = $post->get('banque');
                if($banque =='')
                {
//                    $res = array('estInsere' => 0, 'message' => 'Banque');
//                    return new JsonResponse($res);
                    $banque = null;
                }

//                $nbBanque = $post->get('nbBanque');
//                if($nbBanque =='')
//                {
//                    $res = array('estInsere' => 0, 'message' => 'Nombre de banque');
//                    return new JsonResponse($res);
//                }

                $saisieOdPaie = $post->get('saisieOdPaie');
                if($saisieOdPaie =='')
                {
//                    $res = array('estInsere' => 0, 'message' => 'Saisie des OD de paye');
//                    return new JsonResponse($res);
                    $saisieOdPaie = null;
                }

                $analytique = $post->get('analytique');
                if($analytique == '')
                {
//                    $res = array('estInsere' => 0, 'message' => 'Analytique');
//                    return new JsonResponse($res);
                    $analytique = null;
                }

//                $venteComptoir = null;
//                if ($post->get('venteComptoir') != '') {
//                    $venteComptoir = $post->get('venteComptoir');
//                };
//                $venteFacture = null;

//                if(!is_null($dossier->getModeVente())) {
//                    if($dossier->getModeVente()->getId() != 1)
//                    $venteComptoir = null;
//                }

                $rapprochementBanque = $post->get('rapprochementBanque');
                if($rapprochementBanque == ''){
                    $rapprochementBanque = null;
                }

                $suiviChequeEmisId = $post->get('suiviChequeEmis');
                $suiviChequeEmis = null;
                if($suiviChequeEmisId != ''){
                    $suiviChequeEmis = $this->getDoctrine()
                        ->getRepository('AppBundle:MethodeSuiviCheque')
                        ->find($suiviChequeEmisId);
                }


                /* @var $methodeComptable MethodeComptable */
                $methodeComptable = $this->getDoctrine()
                    ->getRepository('AppBundle:MethodeComptable')
                    ->getMethodeComptableByDossier($dossier);

                $instructionDossiers = $this->getDoctrine()
                    ->getRepository('AppBundle:InstructionDossier')
                    ->findBy(array('client' => $dossier->getSite()->getClient()));

                if(count($instructionDossiers) > 0){
                    $instructionDossier = $instructionDossiers[0];


                    if($instructionDossier->getRapprochementBanque() == $rapprochementBanque &&
                        $instructionDossier->getMethodeSuiviCheque() == $suiviChequeEmis){

                        $suiviChequeEmis = null;
                        $rapprochementBanque = null;
                    }

                }

                //Nouveau methode comptable
                if($methodeComptable == null)
                {
                    try
                    {


                        $utilisateur = $this->getUser();
                        //**************ENREGISTREMENT LOG**************\\
                        if ($dossier->getAccuseCreation()>=1){

                            if(!is_null($vente)) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $newVal = '';
                                switch ($vente) {
                                    case 1:
                                        $newVal = 'Saisie factures';
                                        break;
                                    case 0:
                                        $newVal = 'Import excel';
                                        break;
                                    case 3:
                                        $newVal = 'Caisse';
                                        break;
                                    case 2:
                                        $newVal = 'Autre';
                                        break;
                                }

                                $log->setChamp('Vente ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if(!is_null($achat)){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $newVal = '';
                                switch ($achat) {
                                    case 1:
                                        $newVal = 'Saisie sur factures';
                                        break;
                                    case 0:
                                        $newVal = 'Import excel';
                                        break;
                                    case 2:
                                        $newVal = 'Autre';
                                        break;
                                }

                                $log->setChamp('Achat ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if(!is_null($banque)){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $newVal = '';
                                switch ($banque) {
                                    case 1:
                                        $newVal = 'Saisie';
                                        break;
                                    case 0:
                                        $newVal = 'Import ecritures';
                                        break;
                                    case 2:
                                        $newVal = 'Déjà importé dans l\'archive';
                                        break;
                                }

                                $log->setChamp('Banques ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if(!is_null($saisieOdPaie)){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $newVal = '';
                                switch ($saisieOdPaie) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = 'Import';
                                        break;
                                }

                                $log->setChamp('Saisie des OD de paye ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if(!is_null($analytique)){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $newVal = '';
                                switch ($analytique) {
                                    case 1:
                                        $newVal = '1 axe';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = '2 axes';
                                        break;
                                }

                                $log->setChamp('Analytique ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if(!is_null($rapprochementBanque)){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $newVal = '';
                                switch ($rapprochementBanque) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = 'Indifférent';
                                        break;
                                }

                                $log->setChamp('Rapprochement banque ');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if(!is_null($suiviChequeEmis)){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $log->setChamp('Suivi des chèques émis');
                                $log->setValeurAncien('');
                                $log->setValeurNouveau($suiviChequeEmis->getLibelle());

                                $em->persist($log);
                                $em->flush();
                            }

                        }
                        //***********FIN LOG***********\\


                        $methodeComptable = new MethodeComptable();

                        $methodeComptable->setDossier($dossier);
                        $methodeComptable->setVente($vente);
                        $methodeComptable->setAchat($achat);
                        $methodeComptable->setBanque($banque);
//                        $methodeComptable->setNbBanque($nbBanque);
                        $methodeComptable->setSaisieOdPaye($saisieOdPaie);
                        $methodeComptable->setAnalytique($analytique);

//                        $methodeComptable->setVenteComptoir($venteComptoir);
//                        $methodeComptable->setVenteFacture($venteFacture);

                        $methodeComptable->setRapprochementBanque($rapprochementBanque);
                        $methodeComptable->setMethodeSuiviCheque($suiviChequeEmis);

                        $em->persist($methodeComptable);
                        $em->flush();

                        return new Response(1);
                    }

                    catch (Exception $e)
                    {
                        return new Response($e->getMessage());
                    }
                }

                //Mise à jour
                else
                {
                    try
                    {

                        $utilisateur = $this->getUser();
                        //**************ENREGISTREMENT LOG**************\\
                        if ($dossier->getAccuseCreation()>=1){

                            if($methodeComptable->getVente() != $vente) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $oldVal = '';
                                switch ($methodeComptable->getVente()) {
                                    case 1:
                                        $oldVal = 'Saisie factures';
                                        break;
                                    case 0:
                                        $oldVal = 'Import excel';
                                        break;
                                    case 3:
                                        $oldVal = 'Caisse';
                                        break;
                                    case 2:
                                        $oldVal = 'Autre';
                                        break;
                                }

                                $newVal = '';
                                switch ($vente) {
                                    case 1:
                                        $newVal = 'Saisie factures';
                                        break;
                                    case 0:
                                        $newVal = 'Import excel';
                                        break;
                                    case 3:
                                        $newVal = 'Caisse';
                                        break;
                                    case 2:
                                        $newVal = 'Autre';
                                        break;
                                }

                                $log->setChamp('Vente ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if($methodeComptable->getAchat() != $achat){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $oldVal = '';
                                switch ($methodeComptable->getAchat()) {
                                    case 1:
                                        $oldVal = 'Saisie sur factures';
                                        break;
                                    case 0:
                                        $oldVal = 'Import excel';
                                        break;
                                    case 2:
                                        $oldVal = 'Autre';
                                        break;
                                }

                                $newVal = '';
                                switch ($achat) {
                                    case 1:
                                        $newVal = 'Saisie sur factures';
                                        break;
                                    case 0:
                                        $newVal = 'Import excel';
                                        break;
                                    case 2:
                                        $newVal = 'Autre';
                                        break;
                                }

                                $log->setChamp('Achat ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if($methodeComptable->getBanque() != $banque){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $oldVal = '';
                                switch ($methodeComptable->getBanque()) {
                                    case 1:
                                        $newVal = 'Saisie';
                                        break;
                                    case 0:
                                        $newVal = 'Import ecritures';
                                        break;
                                    case 2:
                                        $newVal = 'Déjà importé dans l\'archive';
                                        break;
                                }

                                $newVal = '';
                                switch ($banque) {
                                    case 1:
                                        $newVal = 'Saisie';
                                        break;
                                    case 0:
                                        $newVal = 'Import ecritures';
                                        break;
                                    case 2:
                                        $newVal = 'Déjà importé dans l\'archive';
                                        break;
                                }

                                $log->setChamp('Banques ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if($methodeComptable->getSaisieOdPaye() != $saisieOdPaie){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);


                                $oldVal = '';
                                switch ($methodeComptable->getSaisieOdPaye()) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = 'Import';
                                        break;
                                }

                                $newVal = '';
                                switch ($saisieOdPaie) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = 'Import';
                                        break;
                                }

                                $log->setChamp('Saisie des OD de paye ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if($methodeComptable->getAnalytique() != $analytique){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $oldVal = '';
                                switch ($methodeComptable->getAnalytique() != $analytique) {
                                    case 1:
                                        $oldVal = '1 axe';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                    case 2:
                                        $oldVal = '2 axes';
                                        break;
                                }

                                $newVal = '';
                                switch ($analytique) {
                                    case 1:
                                        $newVal = '1 axe';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = '2 axes';
                                        break;
                                }

                                $log->setChamp('Analytique ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if($methodeComptable->getRapprochementBanque() != $rapprochementBanque){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $oldVal = '';
                                switch ($methodeComptable->getRapprochementBanque()) {
                                    case 1:
                                        $oldVal = 'Oui';
                                        break;
                                    case 0:
                                        $oldVal = 'Non';
                                        break;
                                    case 2:
                                        $oldVal = 'Indifférent';
                                        break;
                                }

                                $newVal = '';
                                switch ($rapprochementBanque) {
                                    case 1:
                                        $newVal = 'Oui';
                                        break;
                                    case 0:
                                        $newVal = 'Non';
                                        break;
                                    case 2:
                                        $newVal = 'Indifférent';
                                        break;
                                }

                                $log->setChamp('Rapprochement banque ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                            if($methodeComptable->getMethodeSuiviCheque() != $suiviChequeEmis){
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(3);

                                $oldVal  = "";
                                if(!is_null($methodeComptable->getMethodeSuiviCheque())) {
                                    $oldVal = $methodeComptable->getMethodeSuiviCheque()
                                        ->getLibelle();
                                }

                                $newVal = "";
                                if(!is_null($suiviChequeEmis)){
                                    $newVal = $suiviChequeEmis->getLibelle();
                                }

                                $log->setChamp('Suivi des chèques émis');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }

                        }
                        //***********FIN LOG***********\\




                        $methodeComptable->setDossier($dossier);
                        $methodeComptable->setVente($vente);
                        $methodeComptable->setAchat($achat);
                        $methodeComptable->setBanque($banque);
//                        $methodeComptable->setNbBanque($nbBanque);
                        $methodeComptable->setSaisieOdPaye($saisieOdPaie);
                        $methodeComptable->setAnalytique($analytique);

//                        $methodeComptable->setVenteComptoir($venteComptoir);
//                        $methodeComptable->setVenteFacture($venteFacture);

                        $methodeComptable->setRapprochementBanque($rapprochementBanque);
                        $methodeComptable->setMethodeSuiviCheque($suiviChequeEmis);

                        $em->persist($methodeComptable);
                        $em->flush();

                        return new Response(2);
                    }
                    catch (Exception $e)
                    {
                        return new Response($e->getMessage());
                    }
                }
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function editMethodeComptableV2Action(Request $request)
       {
           if($request->isXmlHttpRequest())
           {
               $post = $request->request;
               $em = $this->getDoctrine()->getEntityManager();

               $idDossier =  Boost::deboost($post->get('dossierId'),$this);

               $val = $post->get('value');
               $field = $post->get('field');


               switch ($field){
                   case 'ConventionComptable':
                       $value = $this->getDoctrine()
                           ->getRepository('AppBundle:ConventionComptable')
                           ->find($val);
                       break;

                   default:
                       $value = $val;
                       break;
               }

               if($idDossier == 0)
               {
                   //Erreur: le dossier n'existe pas encore
                   $res = array('estInsere' => -1, 'message' => 'Dossier');
                   return new JsonResponse($res);
               }

               else
               {
                   $dossier = $this->getDoctrine()
                       ->getRepository('AppBundle:Dossier')
                       ->find($idDossier);

                   /* @var $methodeComptable MethodeComptable */
                   $methodeComptable = $this->getDoctrine()
                       ->getRepository('AppBundle:MethodeComptable')
                       ->getMethodeComptableByDossier($dossier);

                   //Nouveau methode comptable
                   if($methodeComptable == null)
                   {
                       try
                       {
                           $methodeComptable = new MethodeComptable();

                           $methodeComptable->setDossier($dossier);
                           $methodeComptable->{"set$field"}($value);

                           $em->persist($methodeComptable);
                           $em->flush();

                           return new Response(1);
                       }

                       catch (Exception $e)
                       {
                           return new Response($e->getMessage());
                       }
                   }

                   //Mise à jour
                   else
                   {
                       try
                       {
                           $methodeComptable->setDossier($dossier);
                           $methodeComptable->{"set$field"}($value);
                           
                           $em->persist($methodeComptable);
                           $em->flush();

                           return new Response(2);
                       }
                       catch (Exception $e)
                       {
                           return new Response($e->getMessage());
                       }
                   }
               }
           }
           else
           {
               throw new AccessDeniedHttpException("Accès refusé");
           }
       }

    public function editPeriodiciteAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $post = $request->request;
            $em = $this->getDoctrine()->getEntityManager();



            $idDossier =  Boost::deboost($post->get('dossierId'),$this);


            if($idDossier == 0)
            {
                //Erreur: le dossier n'existe pas encore
                $res = array('estInsere' => -1, 'message' => 'Dossier');
                return new JsonResponse($res);
            }

            else
            {
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);


                $utilisateur = $this->getUser();


                $tenueComptabilite = $post->get('tenueComptabilite');
                if($tenueComptabilite == '')
                {
//                    $res = array('estInsere' => 0, 'message' => 'Tenue de la comptabilité');
//                    return new JsonResponse($res);
                    $tenueComptabilite = null;
                }

                /* @var $methodeComptable MethodeComptable */
                $methodeComptable = $this->getDoctrine()
                    ->getRepository('AppBundle:MethodeComptable')
                    ->getMethodeComptableByDossier($dossier) ;

                //Nouveau methode comptable
                if($methodeComptable == null)
                {


                    //**************ENREGISTREMENT LOG**************\\
                    if ($dossier->getAccuseCreation()>=1){

                        $log = new LogInfoperdos();
                        $log->setDate(new \DateTime());
                        $log->setDossier($dossier);
                        $log->setUtilisateur($utilisateur);
                        $log->setTab(2);
                        $log->setBloc(2);

                        $newVal = '';
                        switch ($tenueComptabilite){
                            case 1:
                                $newVal = 'Mensuelle';
                                break;
                            case 2:
                                $newVal = 'Trimestrielle';
                                break;
                            case 3:
                                $newVal = 'Semestrielle';
                                break;
                            case 4:
                                $newVal = 'Annuelle';
                                break;
                            case 5:
                                $newVal = 'Ponctuelle';
                                break;
                        }

                        $log->setChamp('Periodicité ');
                        $log->setValeurAncien('');
                        $log->setValeurNouveau($newVal);

                        $em->persist($log);
                        $em->flush();
                    }
                    //***********FIN LOG***********\\



                    try
                    {
                        $methodeComptable = new MethodeComptable();

                        $methodeComptable->setDossier($dossier);
                        $methodeComptable->setTenueComptablilite($tenueComptabilite);

                        $em->persist($methodeComptable);
                        $em->flush();

                        return new Response(1);
                    }

                    catch (Exception $e)
                    {
                        return new Response($e->getMessage());
                    }
                }

                //Mise à jour
                else
                {
                    try
                    {


                        //**************ENREGISTREMENT LOG**************\\
                        if ($dossier->getAccuseCreation()>=1){
                            if($methodeComptable->getTenueComptablilite() != $tenueComptabilite) {
                                $log = new LogInfoperdos();
                                $log->setDate(new \DateTime());
                                $log->setDossier($dossier);
                                $log->setUtilisateur($utilisateur);
                                $log->setTab(2);
                                $log->setBloc(2);


                                $oldVal = '';
                                switch ($methodeComptable->getTenueComptablilite()) {
                                    case 1:
                                        $oldVal = 'Mensuelle';
                                        break;
                                    case 2:
                                        $oldVal = 'Trimestrielle';
                                        break;
                                    case 3:
                                        $oldVal = 'Semestrielle';
                                        break;
                                    case 4:
                                        $oldVal = 'Annuelle';
                                        break;
                                    case 5:
                                        $oldVal = 'Ponctuelle';
                                        break;
                                }

                                $newVal = '';
                                switch ($tenueComptabilite) {
                                    case 1:
                                        $newVal = 'Mensuelle';
                                        break;
                                    case 2:
                                        $newVal = 'Trimestrielle';
                                        break;
                                    case 3:
                                        $newVal = 'Semestrielle';
                                        break;
                                    case 4:
                                        $newVal = 'Annuelle';
                                        break;
                                    case 5:
                                        $newVal = 'Ponctuelle';
                                        break;
                                }

                                $log->setChamp('Periodicité ');
                                $log->setValeurAncien($oldVal);
                                $log->setValeurNouveau($newVal);

                                $em->persist($log);
                                $em->flush();
                            }
                        }
                        //***********FIN LOG***********\\



                        $methodeComptable->setDossier($dossier);
                        $methodeComptable->setTenueComptablilite($tenueComptabilite);
                      
                        $em->persist($methodeComptable);

                        $em->flush();

                        return new Response(2);
                    }
                    catch (Exception $e)
                    {
                        return new Response($e->getMessage());
                    }
                }
            }


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


}
