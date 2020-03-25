<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 12/10/2017
 * Time: 11:25
 */

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\HistoriqueCategorie;
use AppBundle\Entity\Separation;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ControleReleveController extends Controller
{
    /**
     * Mi-comparer ny nom an'ny dossier, ilaina any @ usort
     * @param $a
     * @param $b
     * @return int
     */
    function compNomDossier($a, $b)
    {
        return strcmp($a->getNom(), $b->getNom());
    }

    /**
     * Affichage-n'ny tableau relevé manquant
     * @param Request $request
     * @return JsonResponse
     */
    public function tableauReleveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $rows = array();

            $post = $request->request;

            $clientId = Boost::deboost($post->get('clientId'), $this);
            $siteId = Boost::deboost($post->get('siteId'), $this);
            $dossierId = Boost::deboost($post->get('dossierId'), $this);
            $banqueId = Boost::deboost($post->get('banqueId'), $this);
            $exercice = $post->get('exercice');
            $numCompte = Boost::deboost($post->get('numCompte'), $this);

            $dossiers = array();

            $isDossier = -1;


            if($dossierId != 0){
                $dossiers[] = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($dossierId);

                $isDossier = 1;
            }

            elseif ($siteId != 0) {

                $site = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->find($siteId);

                $dossierSites = array();

                $dossierSites[] = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->findBy(array('site' => $site));


                foreach ($dossierSites as $dossierSite) {

                    foreach ($dossierSite as $ds) {
                        $dossiers[] = $ds;
                    }
                }

            }
            elseif ($clientId != 0){
                $client = $this->getDoctrine()
                    ->getRepository('AppBundle:Client')
                    ->find($clientId);

                $sites = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->findBy(array('client'=>$client));

                $dossierSites = array();


                foreach ($sites as $site){
                    $dossierSites[] = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->findBy(array('site'=>$site));
                }

                foreach ($dossierSites as $dossierSite){

                    foreach ($dossierSite as $ds){
                        $dossiers[] = $ds;
                    }
                }

            }

            usort($dossiers, array($this, 'compNomDossier'));

            /** @var  $dossier Dossier*/
            foreach ($dossiers as $dossier) {

                $em = $this->getDoctrine()
                    ->getManager();

                if ($banqueId != 0) {

                    $banque = $this->getDoctrine()
                        ->getRepository('AppBundle:Banque')
                        ->find($banqueId);

//                    $banqueComptes = $this->getDoctrine()
//                        ->getRepository('AppBundle:BanqueCompte')
//                        ->findBy(array('banque' => $banque, 'dossier' => $dossier));

                    $banqueComptes = $this->getDoctrine()
                        ->getRepository('AppBundle:BanqueCompte')
                        ->getBanquesComptes($dossier, $banque);

                } else {
//                    $banqueComptes = $this->getDoctrine()
//                        ->getRepository('AppBundle:BanqueCompte')
//                        ->getBanqueCompteByDossier($dossier);

                    $banqueComptes = $this->getDoctrine()
                        ->getRepository('AppBundle:BanqueCompte')
                        ->getBanquesComptes($dossier);
                }

                if ($numCompte != 0) {

                    $banqueCompte = $this->getDoctrine()
                        ->getRepository('AppBundle:BanqueCompte')
                        ->find($numCompte);

                    $resReleves = $this->getDoctrine()
                        ->getRepository('AppBundle:Image')
                        ->getInfoReleveByDossier($banqueCompte->getId(), $exercice);

                    $infoReleves = $this->getDoctrine()
                        ->getRepository('AppBundle:ReleveManquant')
                        ->InitializeInfoReleves($resReleves, $dossier, $exercice);

                    //Raha tsy misy images mihitsy dia atao relevé manquant katr@ mois farany
                    if(count($infoReleves) == 0){
                        $infoReleves = $this->getDoctrine()
                            ->getRepository('AppBundle:ReleveManquant')
                            ->InitializePasImageInfoReleves($dossier,$exercice,$banqueCompte);
                    }

                    $moisList = array();


                    //Raha tsy misy releve mihitsy
                    $statusReleve = count($resReleves) !== 0;


                    $trouveReleveManquant = false;
                    $moisOk = "";

                    //Affichage
                    for ($i = 0, $iMax = count($infoReleves); $i < $iMax; $i++) {


                        $releve = $infoReleves[$i];

                        $anneePlus = false;

                        if($releve->periode_deb != '') {
                            $mois = (int)date("m", strtotime($releve->periode_deb));

                            if (date("d", strtotime($releve->periode_deb)) >= 25) {

                                if($releve->periode_fin != ""){
                                    $moisFin = (int)date("m", strtotime($releve->periode_fin));

                                    if($moisFin != $mois){
                                        $mois = $mois + 1;
                                    }
                                }
                            }
                        }

                        else{
                               $mois = $this->SetMoisManquant($infoReleves, $i);
                        }

                        if($mois == 13){
                            $mois = 1;
                            $anneePlus = true;
                        }

                        if ($releve->controle === 'Relevé Manquant') {


                            if($releve->periode_deb != ""){
                                $year = date("Y", strtotime($releve->periode_deb));
                            }
                            else{

                                $trouveYear = false;
                                //Jerena aloha raha misy periode fin ilay releve ao aloha
                                if($i > 0){
                                    for ($j = $i; $j >= 0; $j--) {
                                        $relevePrec = $infoReleves[$j];
                                        if ($relevePrec->periode_fin != "") {
                                            $year = date("Y", strtotime($relevePrec->periode_fin));
                                            $trouveYear = true;
                                            break;
                                        } else if ($relevePrec->periode_deb != "") {
                                            $year = date("Y", strtotime($relevePrec->periode_deb));
                                            $trouveYear = true;
                                            break;
                                        }
                                    }
                                }

                                //Jerena indray ny releve suivant
                                if(!$trouveYear){
                                    for ($j = $i+1, $jMax = count($infoReleves) -1; $j < $jMax; $j++){
                                        $releveSuiv = $infoReleves[$j];

                                        if ($releveSuiv->periode_deb != "") {
                                            $year = date("Y", strtotime($releveSuiv->periode_deb));
                                            $trouveYear = true;
                                            break;
                                        }
                                        else if ($releveSuiv->periode_fin != "") {
                                            $year = date("Y", strtotime($releveSuiv->periode_fin));
                                            $trouveYear = true;
                                            break;
                                        }

                                    }
                                }

                                if(!$trouveYear)
                                    $year = $exercice;
                            }

                            if($anneePlus){
                                $year++;
                            }

                            if($mois < 10){
                                $mois = '0'.$mois;
                            }

                            $periode = $year.'-'.$mois;


                            if (!in_array($periode, $moisList)) {
                                $moisList[] = $periode;
                            }

                            $trouveReleveManquant = true;
                        }

                        $moisGrid = (int)$mois;
                        if($moisGrid < 10){
                            $moisGrid = '0'.$moisGrid;
                        }

                        $banqueNom = $banqueCompte->getBanque()->getNom();
                        if($banqueCompte->getSourceImage() !== null){
                            if($banqueCompte->getSourceImage()->getId() === 3){
                                $banqueNom= 'SB '.$banqueNom;
                            }
                        }

                        $rows[] = array(
                            'id' => $releve->image_id,
                            'cell' => array(
                                $dossier->getNom(),
//                                $releve->banque_nom,
                                $banqueNom,
                                $releve->numcompte,
                                $moisGrid,
                                $releve->periode_deb,
                                $releve->periode_fin,
                                $releve->num_releve,
                                $releve->solde_deb,
                                $releve->solde_fin,
                                $releve->controle,
                                $releve->date_scan,
                                '<i class="fa fa-file-text"></i>',
                                $releve->image_nom,
                                '<i class="fa fa-copy"></i>'
//                                $releve->image_id_precedent,
//                                $releve->image_id_suivant,
//                                $releve->releve_intermediaire
                            )
                        );

                    }

                    //Sauver-na isaky ny consultation
                    $this->getDoctrine()
                        ->getRepository('AppBundle:ReleveManquant')
                        ->SaveRelevesManquant($exercice,$dossier,$banqueCompte,$moisList, $statusReleve, $em);


                    if($trouveReleveManquant == false){
                        if(count($infoReleves) > 0){

                            $releve = $infoReleves[count($infoReleves) -1];
                            $moisOk = date("Y", strtotime($releve->periode_deb)) . '-' . date("m", strtotime($releve->periode_deb));
                        }
                    }

                    $this->getDoctrine()
                        ->getRepository('AppBundle:ReleveManquant')
                        ->SaveRelevesComplet($exercice,$dossier,$banqueCompte, $moisOk, $em);

                } else {

                    /** @var  $banqueCompte BanqueCompte */
                    foreach ($banqueComptes as $banqueCompte) {
//                        $infoReleves = array();

                        $resReleves = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoReleveByDossier($banqueCompte->getId(), $exercice);

                        $infoReleves = $this->getDoctrine()
                            ->getRepository('AppBundle:ReleveManquant')
                            ->InitializeInfoReleves($resReleves, $dossier, $exercice);


                        //Raha tsy misy images mihitsy dia atao relevé manquant katr@ mois farany
                        if(count($infoReleves) == 0){
                            $infoReleves = $this->getDoctrine()
                                ->getRepository('AppBundle:ReleveManquant')
                                ->InitializePasImageInfoReleves($dossier,$exercice,$banqueCompte);
                        }

                        $moisList = array();

                        //Raha tsy misy releve mihitsy
                        $statusReleve = count($resReleves) !== 0;

                        $trouveReleveManquant  = false;
                        $moisOk = "";

                        //Affichage
                        for ($i = 0, $iMax = count($infoReleves); $i < $iMax; $i++) {

                            $releve = $infoReleves[$i];

                            $anneePlus = false;


                            if($releve->periode_deb != '') {
                                $mois = (int)date("m", strtotime($releve->periode_deb));

                                if (date("d", strtotime($releve->periode_deb)) >= 25) {

                                    if($releve->periode_fin != ""){
                                        $moisFin = (int)date("m", strtotime($releve->periode_fin));

                                        if($moisFin != $mois){
                                            $mois = $mois + 1;
                                        }
                                    }

                                }
                            }

                            else{
                                $mois = $this->SetMoisManquant($infoReleves, $i);
                            }

                            if($mois == 13){
                                $mois = 1;
                                $anneePlus++;
                            }


                            if ($releve->controle === 'Relevé Manquant') {


                                if($releve->periode_deb != ""){
                                    $year = date("Y", strtotime($releve->periode_deb));
                                }
                                else{
                                    $year = $exercice;
                                }

                                if($anneePlus){
                                    $year++;
                                }

                                if($mois < 10){
                                    $mois = '0'.$mois;
                                }


                                $periode = $year.'-'.$mois;

                                if (!in_array($periode, $moisList)) {
                                    $moisList[] = $periode;
                                }

                                $trouveReleveManquant = true;
                            }

                            $moisGrid = (int)$mois;
                            if($moisGrid < 10){
                                $moisGrid = '0'.$moisGrid;
                            }

                            //Ho an'ny mois tokony efa hisy images
                            if($releve->image_id == -1)
                            {
                                $rows[] = array(
                                    'id' => $releve->image_id,
                                    'cell' => array(
                                        $dossier->getNom(),
                                        $releve->banque_nom,
                                        $releve->numcompte,
                                        $moisGrid,
                                        '',
                                        $releve->periode_fin,
                                        $releve->num_releve,
                                        $releve->solde_deb,
                                        $releve->solde_fin,
                                        $releve->controle,
                                        $releve->date_scan,
                                        '',
                                        $releve->image_nom,
                                        ''
                                    )
                                );
                                continue;
                            }

                            $rows[] = array(
                                'id' => $releve->image_id,
                                'cell' => array(
                                    $dossier->getNom(),
                                    $releve->banque_nom,
                                    $releve->numcompte,
                                    $moisGrid,
                                    $releve->periode_deb,
                                    $releve->periode_fin,
                                    $releve->num_releve,
                                    $releve->solde_deb,
                                    $releve->solde_fin,
                                    $releve->controle,
                                    $releve->date_scan,
                                    '<i class="fa fa-file-text"></i>',
                                    $releve->image_nom,
                                    '<i class="fa fa-copy"></i>'
//                                $releve->image_id_precedent,
//                                $releve->image_id_suivant,
//                                $releve->releve_intermediaire
                                )
                            );

                        }

                        //Sauver-na isaky ny consultation
                        $this->getDoctrine()
                            ->getRepository('AppBundle:ReleveManquant')
                            ->SaveRelevesManquant($exercice,$dossier,$banqueCompte,$moisList, $statusReleve, $em);


                        if($trouveReleveManquant == false){
                            if(count($infoReleves) > 0){

                                $releve = $infoReleves[count($infoReleves) -1];
                                $moisOk = date("Y", strtotime($releve->periode_deb)) . '-' . date("m", strtotime($releve->periode_deb));
                            }
                        }

                        $this->getDoctrine()
                            ->getRepository('AppBundle:ReleveManquant')
                            ->SaveRelevesComplet($exercice,$dossier,$banqueCompte, $moisOk, $em);

                        //Ligne separation
                        if (count($infoReleves) > 0) {

                            $rows[] = array(
                                'id' => -2,
                                'cell' => array(
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',//0.01,
                                    '',//0.01,
                                    'Fin',
                                    ''
                                )
                            );
                        }
                    }
                }

            }

            $liste = array('rows' => $rows, 'isDossier' => $isDossier);

            return new JsonResponse($liste);

        }

        throw new AccessDeniedException('Accès refusé');
    }

    /**
     * @param $info
     * @param $index
     * @return int
     */
    public function SetMoisManquant($info, $index){

        if($index == 0){
//            return 1;

            if($info[0]->periode_deb != ''){
                return date("m", strtotime($info[0]->periode_deb));
            }
            else{
                return 1;
            }
        }

        $infoPrec = $info[$index-1];
        $infoPrecMoisDeb = -1;
        $infoPrecMoisFin = -1;
        $infoSuivMoisDeb = -1;
        $infoSuivMoisFin = -1;

        if($infoPrec->periode_deb != "") {
            $infoPrecMoisDeb = date("m", strtotime($infoPrec->periode_deb));
        }

        if($infoPrec->periode_fin != ""){
            $infoPrecMoisFin = date("m", strtotime($infoPrec->periode_fin));
        }

        if($index  < count($info) -1) {
            $infoSuiv = $info[$index + 1];
            if ($infoSuiv->periode_deb != "") {
                $infoSuivMoisDeb = date("m", strtotime($infoSuiv->periode_deb));
            }

            if($infoSuiv->periode_fin != ""){
                $infoSuivMoisFin = date("m", strtotime($infoSuiv->periode_fin));
            }

        }


        if($infoPrecMoisDeb == $infoPrecMoisFin){
            $res = $infoPrecMoisDeb + 1;
        }
        else{
            if($infoPrec->periode_fin != ""){
                $infoPrecDayFin = date("d", strtotime($infoPrec->periode_fin));

                if($infoPrecDayFin >= 25){
                    if($infoPrecMoisFin != -1){
                        $res = $infoPrecMoisFin + 1;
                    }
                    else{
                        $res = $infoPrecMoisDeb + 2;
                    }
                }
                else{
                    if($infoPrecMoisFin != -1){
                        $res = $infoPrecMoisFin;
                    }
                    else{
                        $res = $infoPrecMoisDeb + 1;
                    }
                }
            }
            else {
                if ($infoSuivMoisDeb != -1)
                    $res = $infoSuivMoisDeb;
                elseif($infoSuivMoisFin != -1)
                    $res = $infoSuivMoisFin;
                else
                    $res = 12;
            }
        }


//        if($infoPrecMoisDeb == $infoSuivMoisDeb) {
//
//            $infoPrecDayDeb = date("d", strtotime($infoPrec->periode_deb));
//
//            if($infoPrec->periode_fin != '') {
//                $infoPrecMoisFin = date("m", strtotime($infoPrec->periode_fin));
//
//                if($infoPrecMoisFin != $infoPrecMoisDeb){
//                    if($infoPrecDayDeb >= 25){
//                        $infoPrecMoisDeb++;
//                    }
//                }
//
//            }
//
//            $res = intval($infoPrecMoisDeb);
//        }
//        else{
//
//            $infoPrecDayDeb = date("d", strtotime($infoPrec->periode_deb));
//
//            $res = $infoPrecMoisDeb + 1;
//
//
//            if($infoPrec->periode_fin != ''){
//                $infoPrecMoisFin = date("d", strtotime($infoPrec->periode_fin));
//
//                if($infoPrecMoisFin != $infoPrecMoisDeb){
//                    if($infoPrecDayDeb >= 25 && $infoSuivMoisDeb > $res){
//                        $res++;
//                    }
//
//                }
//            }
//        }

         return (int)$res;

    }


    /**
     * Mi-lister ny banque-n'ny dossier iray
     * @param Request $request
     * @return JsonResponse
     */
    public function BanqueDossierAction(Request $request){

        $post = $request->request;

        $dossierId = Boost::deboost($post->get('dossierId'),$this);

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $banqueComptes = $this->getDoctrine()
            ->getRepository('AppBundle:BanqueCompte')
            ->findBy(array('dossier'=>$dossier));


        $banques = array();

        /** @var  $banqueCompte BanqueCompte*/
        foreach ($banqueComptes as $banqueCompte){

            $banque = $banqueCompte->getBanque();

            if(!in_array($banque,$banques)){
                $banques[] = $banque;
            }
        }


        $resBanque = array();

        if(count($banques)>0) {
            foreach ($banques as $banque) {
                $resBanque[] = array('id' => Boost::boost($banque->getId()), 'nom_banque' => $banque->getNom());
            }
        }
        $liste = array('banques' => $resBanque);

        return new JsonResponse($liste);
    }


    /**
     * Mi-modifier ny Souscategorie an'ny image ho doublon
     * @param Request $request
     * @return JsonResponse
     */
    public function banqueDoublonAction(Request $request){

        $post = $request->request;

        $imageId = $post->get('imageId');

        $image = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->find($imageId);

        $em = $this->getDoctrine()
            ->getManager();

        $souscategorie = $this->getDoctrine()
            ->getRepository('AppBundle:Souscategorie')
            ->find(3);

        $soussouscategorie = $this->getDoctrine()
            ->getRepository('AppBundle:Soussouscategorie')
            ->find(5);

        if($image->getCtrlImputation()> 1){

            $t = $this->getDoctrine()
                ->getRepository('AppBundle:ImputationControle')
                ->findBy(array('image'=>$image));

            if(count($t)) {
                $t[0]->setSouscategorie($souscategorie);
                $t[0]->setSoussouscategorie($soussouscategorie);

                $em->flush();
            }
        }
        if($image->getImputation() > 1){
            $t = $this->getDoctrine()
                ->getRepository('AppBundle:ImputationControle')
                ->findBy(array('image'=>$image));

            if(count($t)) {
                $t[0]->setSouscategorie($souscategorie);
                $t[0]->setSoussouscategorie($soussouscategorie);
            }
        }
        if($image->getCtrlSaisie() > 1){

            $t = $this->getDoctrine()
                ->getRepository('AppBundle:SaisieControle')
                ->findBy(array('image'=>$image));

            if(count($t)) {

                $t[0]->setSoussouscategorie($soussouscategorie);
            }
        }
        if($image->getSaisie2() > 1){

            $t = $this->getDoctrine()
                ->getRepository('AppBundle:Saisie2')
                ->findBy(array('image'=>$image));

            if(count($t)) {

                $t[0]->setSoussouscategorie($soussouscategorie);
            }
        }
        if($image->getSaisie1() > 1){

            $t = $this->getDoctrine()
                ->getRepository('AppBundle:Saisie1')
                ->findBy(array('image'=>$image));

            if(count($t)) {

                $t[0]->setSoussouscategorie($soussouscategorie);


            }
        }

        try {
            /** @var Separation $separation */
            $separation = $this->getDoctrine()
                ->getRepository('AppBundle:Separation')
                ->getSeparationByImage($image);


            $oldCategorie = $separation->getCategorie();
            $oldSouscategorie = $separation->getSouscategorie();
            $oldSoussouscategorie = $separation->getSoussouscategorie();

            $historiqueCategorie = new HistoriqueCategorie();
            $historiqueCategorie
                ->setImage($image)
                ->setCategorie($oldCategorie)
                ->setSouscategorie($oldSouscategorie)
                ->setSoussouscategorie($oldSoussouscategorie)
                ->setOperateur($this->getDoctrine()->getRepository('AppBundle:Operateur')->find(506))
                ->setDateModification(new \DateTime('now'))
                ->setMotif('CONTROLE RELEVE');

            $em->persist($historiqueCategorie);


            $separation->setSouscategorie($souscategorie);
            $separation->setSoussouscategorie($soussouscategorie);

        } catch (NonUniqueResultException $e) {
        }



        $em->flush();

        $data = array(
            'erreur' => false
        );

        return new JsonResponse(json_encode($data));

    }


    /**
     * Mijery ny liste-ny num compte banque raha misy maromaro ho an'ny dossier/banque iray
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function numCompteBanqueAction(Request $request){
        if($request->isXmlHttpRequest()) {
            $post = $request->request;

            $banqueId = Boost::deboost($post->get('banqueId'), $this);
            $dossierId = Boost::deboost($post->get('dossierId'), $this);


            $banque = $this->getDoctrine()
                ->getRepository('AppBundle:Banque')
                ->find($banqueId);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $banqueCompte = $this->getDoctrine()
                ->getRepository('AppBundle:BanqueCompte')
                ->findBy(array('dossier'=>$dossier, 'banque'=>$banque));


            $listeNumCompte = array();

            foreach ($banqueCompte as $bc) {

                if (!in_array($bc->getNumcompte(), $listeNumCompte))
                {
                    $compteObject = new \stdClass();
                    $compteObject->compte = $bc->getNumcompte();
                    $compteObject->id = Boost::boost($bc->getId());
                    $listeNumCompte[] = $compteObject;
                    //$listeNumCompte[] = $bc->getNumcompte();
                }
            }

            return new JsonResponse($listeNumCompte);
        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

}