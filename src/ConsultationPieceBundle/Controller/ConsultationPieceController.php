<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 28/02/2017
 * Time: 11:10
 */

namespace ConsultationPieceBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\Banque;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Categorie;
use AppBundle\Entity\ControleCegj;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Ecriture;
use AppBundle\Entity\HistoriqueCategorie;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageATraiter;
use AppBundle\Entity\ImageImage;
use AppBundle\Entity\Imputation;
use AppBundle\Entity\ImputationCegj;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\ImputationControleCegj;
use AppBundle\Entity\LogCegj;
use AppBundle\Entity\Lot;
use AppBundle\Entity\MenuParRole;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\ReglePaiementTiers;
use AppBundle\Entity\Releve;
use AppBundle\Entity\Saisie1;
use AppBundle\Entity\Saisie1Cegj;
use AppBundle\Entity\Saisie1NoteFrais;
use AppBundle\Entity\Saisie2;
use AppBundle\Entity\Saisie2Cegj;
use AppBundle\Entity\Separation;
use AppBundle\Entity\Site;
use AppBundle\Entity\Souscategorie;
use AppBundle\Entity\Soussouscategorie;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\TvaImputation;
use AppBundle\Entity\TvaSaisie1;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurDossier;
use AppBundle\Entity\UtilisateurSite;
use AppBundle\Entity\SaisieControle;
use AppBundle\Entity\TvaImputationControle;
use DateTime;
use DoctrineExtensions\Query\Sqlite\Date;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ConsultationPieceController extends  Controller
{
    function compNomDossier($a, $b)
    {
        return strcmp($a->getNom(), $b->getNom());
    }

    function compLib($a, $b)
    {
        return strcmp($a->getLibelle(), $b->getLibelle());
    }

    function compLibNew($a, $b)
    {
        return strcmp($a->getLibelleNew(), $b->getLibelleNew());
    }

    function compNom($a, $b){
        return strcmp($a['nom_dossier'], $b['nom_dossier']);
    }

    /**
     * Mitanisa Tree an'ny Dossier par Categorie
     * @param Request $request
     * @return JsonResponse
     */
    public function dossierCategorieTreeAction(Request $request)
    {
        $post = $request->request;
        $siteId = $post->get('siteId');
        $exercice = $post->get('exercice');

        //Nampiana
        $dossierId = $post->get('dossierId');
        $idDossier = Boost::deboost($dossierId, $this);

        $user = $this->getUser();
        $role = $this->getUser()->getRoles();

        $idsite = Boost::deboost($siteId, $this);
        $dossiers = array();

        $clientId = $post->get('clientId');
        $clientId = Boost::deboost($clientId, $this);

        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($clientId);

        if ($idsite == 0) {
            $sites = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->getAllSitesByClient($client);
        } else {
            $sites[] = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->find($idsite);
        }

        if ($idDossier != 0) {
            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($idDossier);
        } else {
            foreach ($sites as $site) {

                /** @var $site Site */
                $dossierSites = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->getUserDossier($user, $role, $site->getId(), $exercice);

                foreach ($dossierSites as $dossierSite) {
                    if(!in_array($dossierSite, $dossiers))
                        $dossiers[] = $dossierSite;
                }
            }
        }

        $rows = array();

//        tEst
//        $dossiers = array();
//        $dossiers[] = $this->getDoctrine()
//            ->getRepository('AppBundle:Dossier')
//            ->find(13769 );

        usort($dossiers, array($this, 'compNomDossier'));

        /**@var $dossier  Dossier */

        foreach ($dossiers as $dossier) {

            $trouveElement = false;

            $rows[] = array(
                'id' => $dossier->getId(),
                'parent' => '#',
                'text' => $dossier->getNom()
            );


            $listeCat = array();
            $listeSCat = array();
            $listeSsCat = array();

            //Liste images any @ separation
            $catSscatSepIds = $this->getDoctrine()
                ->getRepository('AppBundle:Separation')
                ->getListeCatIdScatIdSscatIdSeparationByDossier($dossier->getId(),$exercice);

            $sscatSepIds = $catSscatSepIds['listesoussouscategorie'];
            $catSepIds = $catSscatSepIds['listeCategorie'];

            $scatSepIds = $catSscatSepIds['listesouscategorie'];


            if(!is_null($sscatSepIds) && count($sscatSepIds)>0){
                foreach ($sscatSepIds as $sscatSepId) {

                    if (!is_null($sscatSepId)) {
                        $sscat = $this->getDoctrine()
                            ->getRepository('AppBundle:Soussouscategorie')
                            ->find($sscatSepId);

                        if (!in_array($sscat, $listeSsCat, true)) {

                            if ($sscat != null) {
                                $listeSsCat[] = $sscat;
                                /** @var  $scat Souscategorie */
                                $scat = $sscat->getSouscategorie();

                                if (!in_array($scat, $listeSCat, true)) {
                                    $listeSCat[] = $scat;
                                }

                                /** @var  $cat Categorie */
                                $cat = $scat->getCategorie();

                                if (!in_array($cat, $listeCat, true)) {
                                    $listeCat[] = $sscat->getSouscategorie()->getCategorie();
                                }
                            }
                        }
                    }
                }

            }

            if(!is_null($scatSepIds) && count($scatSepIds)>0){
                foreach ($scatSepIds as $scatSepId) {


                    if (!is_null($scatSepId)) {
                        $scat = $this->getDoctrine()
                            ->getRepository('AppBundle:Souscategorie')
                            ->find($scatSepId);

                        if(!is_null($scat)) {

                            if (!in_array($scat, $listeSCat, true)) {

                                if ($scat != null) {
                                    $listeSCat[] = $scat;

                                    /** @var  $cat Categorie */
                                    $cat = $scat->getCategorie();
                                    if (!in_array($cat, $listeCat, true)) {
                                        $listeCat[] = $scat->getCategorie();
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if(!is_null($catSepIds) && count($catSepIds)) {
                foreach ($catSepIds as $catSepId) {

                    if(!is_null($catSepId)) {

                        $cat = $this->getDoctrine()
                            ->getRepository('AppBundle:Categorie')
                            ->find($catSepId);

                        if (!in_array($cat, $listeCat, true)) {

                            if ($cat != null) {
                                $listeCat[] = $cat;
                            }
                        }
                    }
                }
            }

            usort($listeCat, array($this, 'compLib'));

            //Mi-filtrer ny categorie,sous categorie, soussouscategorie
            foreach ($listeCat as $categorie) {
                /** @var $categorie Categorie */

                $sCategorieCount = 0;
                $sCategorieTemp = array();

                if ($categorie == null) {
                    continue;
                }

                $rows[] = array(
                    'id' => $dossier->getId() . 'cat' . $categorie->getCode(),
                    'parent' => $dossier->getId(),
                    'text' => $categorie->getLibelleNew()
                );

                usort($listeSCat, array($this, 'compLibNew'));

                foreach ($listeSCat as $sCategorie) {


                    /** @var $sCategorie Souscategorie */
                    if ($sCategorie->getCategorie() == $categorie) {

                        $lib = strtolower($sCategorie->getLibelleNew());

                        $pos = strpos($lib, 'doublon');

                        if($pos === false) {

                            $sCategorieCount++;

//                        $rows[] = array(
//                            'id' => $dossier->getId() . 'cat' . $categorie->getCode() . 'sCat' . $sCategorie->getId(),
//                            'parent' => $dossier->getId() . 'cat' . $categorie->getCode(),
//                            'text' => $sCategorie->getLibelle());

//                         $sCategorieTemp[] = array(
//                            'id' => $dossier->getId() . 'cat' . $categorie->getCode() . 'sCat' . $sCategorie->getId(),
//                            'parent' => $dossier->getId() . 'cat' . $categorie->getCode(),
//                            'text' => $sCategorie->getLibelle());


                            $sCategorieTemp[] = $sCategorie;

//                        foreach ($listeSsCat as $ssCategorie) {
//                            /** @var $ssCategorie Soussouscategorie */
//                            if ($ssCategorie->getSouscategorie() == $sCategorie) {
//
//                                $ssCategorieCount++;
//
//                                $rows[] = array(
//                                    'id' => $dossier->getId() . 'cat' . $categorie->getCode() . 'sCat' . $sCategorie->getId() . 'tCat' . $ssCategorie->getId(),
//                                    'parent' => $dossier->getId() . 'cat' . $categorie->getCode() . 'sCat' . $sCategorie->getId(),
//                                    'text' => $ssCategorie->getLibelle());
//                            }
//                        }
                        }
                    }
                }

                if ($sCategorieCount > 1) {
                    /** @var  $sCategorieT Souscategorie*/
                    foreach ($sCategorieTemp as $sCategorieT) {

                        $rows[] = array(
                            'id' => $dossier->getId() . 'cat' . $categorie->getCode() . 'sCat' . $sCategorieT->getId(),
                            'parent' => $dossier->getId() . 'cat' . $categorie->getCode(),
                            'text' => $sCategorieT->getLibelleNew());

                        $ssCategorieCount = 0;
                        $ssCategorieTemp = array();


                        foreach ($listeSsCat as $ssCategorie) {
                            /** @var $ssCategorie Soussouscategorie */
                            if ($ssCategorie->getSouscategorie() == $sCategorieT) {


                                $lib = strtolower($ssCategorie->getSouscategorie()->getLibelleNew());

                                $pos = strpos($lib, 'doublon');

                                if($pos === false) {
                                    $ssCategorieCount++;

                                    $ssCategorieTemp[] = $ssCategorie;
                                }
                            }
                        }

                        if ($ssCategorieCount > 1) {

                            /** @var  $ssCategorieT Soussouscategorie*/
                            foreach ($ssCategorieTemp as $ssCategorieT) {
                                $rows[] = array(
                                    'id' => $dossier->getId() . 'cat' . $categorie->getCode() . 'sCat' . $sCategorieT->getId() . 'tCat' . $ssCategorieT->getId(),
                                    'parent' => $dossier->getId() . 'cat' . $categorie->getCode() . 'sCat' . $sCategorieT->getId(),
                                    'text' => $ssCategorieT->getLibelleNew());
                            }
                        }
                    }
                }

                $trouveElement = true;
            }

            //Liste images mbola tsy any @separations

            $imagesEncours = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->getListeImageEncoursByDossier($dossier, $exercice);

            //Mijery raha mbola misy images encours
            if (count($imagesEncours) > 0) {
                $rows[] = array(
                    'id' => $dossier->getId() . 'encours',
                    'parent' => $dossier->getId(),
                    'text' => 'ENCOURS'
                );

                $trouveElement = true;
            }

            //Rehefa tsy misy n'inona2 ao @ ilay dossier dia tsy afficher-na ilay izy
            if(!$trouveElement){
                array_pop($rows);
            }
        }

        $liste = array('data' => $rows);
//        return new JsonResponse($liste);
        return new JsonResponse($rows);

    }



    /**
     * Mitanisa ny Tree an'ny Dossier par Tiers
     * @param Request $request
     * @return JsonResponse
     */
    public function dossierTiersTreeAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;
            $siteId = $post->get('siteId');
            $exercice = $post->get('exercice');
            $dossierId = $post->get('dossierId');

            $user = $this->getUser();
            $role = $this->getUser()->getRoles();

            $idsite = Boost::deboost($siteId, $this);

            $idDossier = Boost::deboost($dossierId, $this);
            $dossiers = array();

            $clientId = $post->get('clientId');
            $clientId = Boost::deboost($clientId, $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($clientId);

            if ($idsite == 0) {
                $sites = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->getAllSitesByClient($client);
            } else {
                $sites[] = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->find($idsite);
            }

            if ($idDossier != 0) {
                $dossiers = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);
            } else {
                foreach ($sites as $site) {

                    /** @var $site Site */
                    $dossierSites = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->getUserDossier($user, $role, $site->getId(), $exercice);

                    foreach ($dossierSites as $dossierSite) {
                        $dossiers[] = $dossierSite;
                    }
                }
            }

            $rows = array();

            usort($dossiers, array($this, 'compNomDossier'));


            foreach ($dossiers as $dossier) {

                $trouveElement = false;

                /** @var $dossier Dossier */
                $rows[] = array(
                    'id' => $dossier->getId(),
                    'parent' => '#',
                    'text' => $dossier->getNom()
                );

                $listeTiersId = $this->getDoctrine()
                    ->getRepository('AppBundle:Image')
                    ->getListeTiersIdImageByDossier($dossier->getId(),$exercice);

                foreach ($listeTiersId as $tiersId) {
                    /** @var $tiers Tiers */

                    $tiers = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($tiersId);

                    if ($tiers == null) {
                        continue;
                    }

                    $rows[] = array(
                        'id' => $dossier->getId() . 'tiers' . $tiers->getId(),
                        'parent' => $dossier->getId(),
                        'text' => $tiers->getIntitule()
                    );

                    $trouveElement = true;
                }



                //Rehefa tsy misy n'inona2 ao @ ilay dossier dia tsy afficher-na ilay izy
                if(!$trouveElement){
                    array_pop($rows);
                }

            }
            $liste = array('data' => $rows);
            return new JsonResponse($liste);
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    /**
     * Mitanisa ny Tree an'ny Utilisateur par Dossier
     * @param Request $request
     * @return JsonResponse
     */
    public function utilisateurDossierTreeAction(Request $request)
    {
        $post = $request->request;
        $siteId = $post->get('siteId');
        $exercice = $post->get('exercice');

//        $dossierId = $post->get('dossierId');
//        $idDossier = Boost::deboost($dossierId, $this);

        $idSite = Boost::deboost($siteId, $this);


        $clientId = $post->get('clientId');
        $clientId = Boost::deboost($clientId, $this);

        /** @var Utilisateur $user */
        $user = $this->getUser();

        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($clientId);

        $site = $this->getDoctrine()
            ->getRepository('AppBundle:Site')
            ->find($idSite);

        $utilisateurs = $this->getDoctrine()
            ->getRepository('AppBundle:Utilisateur')
            ->getUtilisateursByClient($user, $client, $site, $exercice);

        $rows = array();

        usort($utilisateurs, array($this, 'compNomDossier'));

        /** @var  $utilisateur Utilisateur*/
        foreach ($utilisateurs as $utilisateur) {

            $rows[] = array(
                'id' => $utilisateur->getId(),
                'parent' => '#',
                'text' => $utilisateur->getNom()
            );

            $listeCat = array();
            $listeSCat = array();
            $listeSsCat = array();

            //Liste image efa any @saisie, controle, imputation

            //Maka ny categorie an'ny images efa any @saisie, controle, imputation

            $sscatIds = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->getListeSoussouscategorieIdImageByUtilisateur($utilisateur->getId(), $exercice);

            if(!is_null($sscatIds)){

                foreach ($sscatIds as $sscatId) {
                    $sscat = $this->getDoctrine()
                        ->getRepository('AppBundle:Soussouscategorie')
                        ->find($sscatId->id);

                    if (!in_array($sscat, $listeSsCat, true)) {

                        if ($sscat != null) {
                            $listeSsCat[] = $sscat;

                            $scat = $sscat->getSouscategorie();

                            if (!in_array($scat, $listeSCat, true)) {
                                $listeSCat[] = $scat;
                            }

                            $cat = $scat->getCategorie();

                            if (!in_array($cat, $listeCat, true)) {
                                $listeCat[] = $sscat->getSouscategorie()->getCategorie();
                            }
                        }
                    }
                }
            }

//            //Liste images any @ separation: mbola tsy any @ saisie, controle imputation
            $catSscatSepIds = $this->getDoctrine()
                ->getRepository('AppBundle:Separation')
                ->getListeCategorieIdSoussouscategorieIdSeparationByUtilisateur($utilisateur->getId(),$exercice);

            $sscatSepIds = $catSscatSepIds['listesoussouscategorie'];
            $catSepIds = $catSscatSepIds['listeCategorie'];

            if(!is_null($sscatSepIds)){
                foreach ($sscatSepIds as $sscatSepId){

                    $sscat = $this->getDoctrine()
                        ->getRepository('AppBundle:Soussouscategorie')
                        ->find($sscatSepId);

                    if (!in_array($sscat, $listeSsCat, true)) {

                        if ($sscat != null) {
                            $listeSsCat[] = $sscat;

                            $scat = $sscat->getSouscategorie();

                            if (!in_array($scat, $listeSCat, true)) {
                                $listeSCat[] = $scat;
                            }

                            $cat = $scat->getCategorie();

                            if (!in_array($cat, $listeCat, true)) {
                                $listeCat[] = $sscat->getSouscategorie()->getCategorie();
                            }
                        }
                    }
                }
            }

            if(!is_null($catSepIds)) {
                foreach ($catSepIds as $catSepId) {

                    $cat = $this->getDoctrine()
                        ->getRepository('AppBundle:Categorie')
                        ->find($catSepId);

                    if (!in_array($cat, $listeCat, true)) {

                        if ($cat != null) {
                            $listeCat[] = $cat;
                        }
                    }
                }
            }

            //Mi-filtrer ny categorie,sous categorie, soussouscategorie
            foreach ($listeCat as $categorie) {
                /** @var $categorie Categorie */

                if ($categorie == null) {
                    continue;
                }

                $rows[] = array(
                    'id' => $utilisateur->getId() . 'cat' . $categorie->getCode(),
                    'parent' => $utilisateur->getId(),
                    'text' => $categorie->getLibelleNew()
                );

            }

            //Liste images mbola tsy any @separations

            $imagesEncours = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->getListeImageEncoursByUtilisateurSite($utilisateur->getId(), $idSite, $exercice);



            //Mijery raha mbola misy images encours
            if (count($imagesEncours) > 0) {
                $rows[] = array(
                    'id' => $utilisateur->getId() . 'encours',
                    'parent' => $utilisateur->getId(),
                    'text' => 'ENCOURS'
                );

            }

        }
        $liste = array('data' => $rows);
        return new JsonResponse($liste);


    }

    /**
     * Tableau par avancement
     * @param Request $request
     * @return JsonResponse
     */
    public function tableauAvancementAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $post = $request->request;

            $idSite = $post->get('siteId');
            $idDossier = $post->get('dossierId');
            $idClient = $post->get('clientId');
            $avancement = $post->get('avancement');
            $exercice = $post->get('exercice');

            $periodeSearch = $post->get('periodeSearch');
            $periodeSearch = filter_var($periodeSearch, FILTER_VALIDATE_BOOLEAN);

            $dateDebut = $post->get('dateDebut');
            $dateFin = $post->get('dateFin');

            $dossierIds = array();

            $user = $this->getUser();
            $role = $this->getUser()->getRoles();




            $client = Boost::deboost($idClient, $this);
            if($client == ""){
                $client = 0;
            }


            $site = Boost::deboost($idSite, $this);
            if($site == ""){
                $site = 0;
            }

            $dossier = Boost::deboost($idDossier, $this);
            if($dossier == ""){
                $dossier = 0;
            }



            if($dossier != 0){
                $dossierIds = array();
                $dossierIds[] = $dossier;
            }

            else{
                if($site != 0){

                    $siteEntity = $this->getDoctrine()
                        ->getRepository('AppBundle:Site')
                        ->find($site);

                    $dossierSites = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->getUserDossier($user, $role, $siteEntity->getId(), $exercice);
                }

                else{

                    $clientEntity = $this->getDoctrine()
                        ->getRepository('AppBundle:Client')
                        ->find($client);

                    $dossierSites = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->getUserDossier($user, $clientEntity, null, $exercice);
                }

                /** @var Dossier $dossierSite */
                foreach ($dossierSites as $dossierSite) {
                    $dossierIds[] = $dossierSite->getId();
                }
            }

            $res = array();

            switch ($avancement) {

                //Saisie
                //Imputée
                case 3:
                case 4:

                    if(!$periodeSearch) {

//                        $infosImages = $this->getDoctrine()
//                            ->getRepository('AppBundle:Image')
//                            ->getInfoImagesByClientSiteDossier($client, $site, $dossier, $avancement, $exercice, false, false, null, null);


                        $infosImages = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoImagesByDossierIds(implode(",",$dossierIds), $avancement, $exercice, false, false, null, null);

                    }
                    else{
//                        $infosImages = $this->getDoctrine()
//                            ->getRepository('AppBundle:Image')
//                            ->getInfoImagesByClientSiteDossier($client, $site, $dossier, $avancement, $exercice, false, true, $dateDebut,$dateFin);


                        $infosImages = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoImagesByDossierIds(implode(",",$dossierIds), $avancement, $exercice, false, true, $dateDebut,$dateFin);



                    }

//                    $res = $this->initializeGridV2($infosImages,array(), -1);

                $initializeGridV2 = $this->initializeGridV2($infosImages,array(), -1);

                    $res = $initializeGridV2['rows'];

                    break;

                //Categorisée
                case 2:

//                    $infosSeparations = $this->getDoctrine()
//                        ->getRepository('AppBundle:Separation')
//                        ->getInfoSeparationImagesByClientSiteDossier($client,$site,$dossier,$exercice,false,null,null);


                    $infosSeparations = $this->getDoctrine()
                        ->getRepository('AppBundle:Separation')
                        ->getInfoSeparationImagesByDossierIds(implode(",",$dossierIds),$exercice,false,null,null);



//                    $res = $this->initializeGridV2(array(), $infosSeparations, -1);


                    $initializeGridV2 = $this->initializeGridV2(array(), $infosSeparations, -1);
                    $res = $initializeGridV2['rows'];

                    break;

                //Reçue
                case 1:

//                    $infosEncours = $this->getDoctrine()
//                        ->getRepository('AppBundle:Image')
//                        ->getInfoEncoursImagesByClientSiteDossier($client,$site,$dossier,$exercice, false, null, null);


                    $infosEncours = $this->getDoctrine()
                        ->getRepository('AppBundle:Image')
                        ->getInfoEncoursImagesByDossierIds(implode(",", $dossierIds),$exercice, false, null, null);


//                    $res = $this->initializeGridEncoursV2($infosEncours);

                    $initializeGridEncoursV2 = $this->initializeGridEncoursV2($infosEncours);
                    $res = $initializeGridEncoursV2['rows'];

                    break;

                //Tous
                case 0:

//                    $infosImages = $this->getDoctrine()
//                        ->getRepository('AppBundle:Image')
//                        ->getInfoImagesByClientSiteDossier($client,$site,$dossier,$avancement,$exercice, false,false, null, null);

                    if(!$periodeSearch) {

//                        $infosImages = $this->getDoctrine()
//                            ->getRepository('AppBundle:Image')
//                            ->getInfoImagesByClientSiteDossier($client, $site, $dossier, $avancement, $exercice, false, false, null, null);


                        $infosImages = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoImagesByDossierIds(implode(",",$dossierIds), $avancement, $exercice, false, false, null, null);


//                        $infosSeparations = $this->getDoctrine()
//                            ->getRepository('AppBundle:Separation')
//                            ->getInfoSeparationImagesByClientSiteDossier($client,$site,$dossier,$exercice, false, null, null);


                        $infosSeparations = $this->getDoctrine()
                            ->getRepository('AppBundle:Separation')
                            ->getInfoSeparationImagesByDossierIds(implode(",", $dossierIds),$exercice, false, null, null);



                        $res = $this->initializeGridV2($infosImages, $infosSeparations, -1);

//                        $infosEncours = $this->getDoctrine()
//                            ->getRepository('AppBundle:Image')
//                            ->getInfoEncoursImagesByClientSiteDossier($client,$site,$dossier,$exercice, false, null, null);


                        $infosEncours = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoEncoursImagesByDossierIds(implode(",", $dossierIds),$exercice, false, null, null);

                    }

                    else{
//                        $infosImages = $this->getDoctrine()
//                            ->getRepository('AppBundle:Image')
//                            ->getInfoImagesByClientSiteDossier($client, $site, $dossier, $avancement, $exercice, false, true, $dateDebut,$dateFin);

                        $infosImages = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoImagesByDossierIds(implode(",",$dossierIds), $avancement, $exercice, false, true, $dateDebut,$dateFin);


                        $infosSeparations = array();

//                        $res = $this->initializeGridV2($infosImages, $infosSeparations, -1);

                        $initializeGridV2 = $this->initializeGridV2($infosImages, $infosSeparations, -1);
                        $res = $initializeGridV2['rows'];
                        $listeImagesDownload = $initializeGridV2['listeImagesDownload'];

                        $infosEncours = array();
                    }


//                    $encours = $this->initializeGridEncoursV2($infosEncours);

                    $initializeGridEncoursV2 = $this->initializeGridEncoursV2($infosEncours);
                    $encours = $initializeGridEncoursV2['rows'];
                    $listeImagesEncoursDownload = $initializeGridEncoursV2['listeImagesEncoursDownload'];

                    foreach ($encours as $encour) {
                        $res[] = $encour;
                    }
                    break;
            }

            /** @var  $utilsateur Utilisateur*/
            $utilsateur = $this->getUser();
            $isExpert = -1;
            if($utilsateur->getAccesUtilisateur()->getId() >= 3){
                $isExpert = 1;
            }

            $liste = array('rows' => $res, 'isExpert'=>$isExpert, 'showDossier'=>1);

            return new JsonResponse($liste);
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }

    }

    /**
     * Tableau par Categorie
     * @param Request $request
     * @return JsonResponse
     */
    public function tableauCategorieAction(Request $request)
    {
//        if ($request->isXmlHttpRequest())
        {

            $post = $request->request;

            $dossierId = $post->get('dossierId');;

            $codeCategorie = $post->get('categorieId');

            $categorie = $this->getDoctrine()
                ->getRepository('AppBundle:Categorie')
                ->findOneBy(array('code'=>$codeCategorie));

            $categorieId = null;

            $listeImagesDownload = array();
            $listeImagesEncoursDownload = array();

            if(null !== $categorie){
                $categorieId = $categorie->getId();
            }else{
                $categorieId = (int)$codeCategorie;
            }

            $souscategorieId = $post->get('souscategorieId');
            $soussouscategorieId = $post->get('soussouscategorieId');
            $exercice = $post->get('exercice');

            $utilisateurId = $post->get('utilisateurId');

            $dateDebut = '';
            $dateFin = '';

            $typeSearch = $post->get('typeSearch');

            $periodeSearch = $post->get('periodeSearch');

            $periodeSearch = filter_var($periodeSearch, FILTER_VALIDATE_BOOLEAN);


            $dateScanSearch = filter_var($post->get('dateScanSearch'), FILTER_VALIDATE_BOOLEAN);

            $dateD = $post->get('dateDebut');
            $dateF = $post->get('dateFin');

            if ($dateD != '' && $dateF != '') {
                $dateDebut = DateTime::createFromFormat('d/m/Y', $dateD);
                $dateFin = DateTime::createFromFormat('d/m/Y', $dateF);

                $dateDebut = $dateDebut->setTime(0, 0, 0);
                $dateFin = $dateFin->setTime(0, 0, 0);
            }

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $rows = array();

            //categorieId = -2: ENCOURS
            if ($categorieId > -2) {

                $images = null;
                $imageSeparations = null;

                if (!$periodeSearch) {
                    switch ($typeSearch) {
                        //Recherche par categorie
                        case 1:
                            $images = $this->getDoctrine()
                                ->getRepository('AppBundle:Image')
                                ->getListeImageByDossierCategorieV2($dossier, $exercice, $categorieId, $souscategorieId, $soussouscategorieId, $dateScanSearch, $dateDebut, $dateFin);

                            $imageSeparations = $this->getDoctrine()
                                ->getRepository('AppBundle:Separation')
                                ->getListeImageSeparationByDossierCategorie($dossierId, $exercice, $categorieId, $souscategorieId, $soussouscategorieId, $dateScanSearch, $dateDebut, $dateFin);
                            break;
                        //Recherche par utilisateur
                        case 4:
                            $images = $this->getDoctrine()
                                ->getRepository('AppBundle:Image')
                                ->getListeImageByUtilisateurCategorieDossier($utilisateurId, $categorieId, $dossierId, $exercice);

                            $imageSeparations = $this->getDoctrine()
                                ->getRepository('AppBundle:Separation')
                                ->getListeImageSeparationByUtilisateurCategorieDossier($utilisateurId, $categorieId, $dossierId, $exercice);
                            break;
                    }

                } else {

                    switch ($typeSearch) {
                        //Recherche par categorie
                        case 1:
                            $images = $this->getDoctrine()
                                ->getRepository('AppBundle:Image')
                                ->getListeImageByDossierPeriode($dossier, $exercice, $dateDebut, $dateFin);
                            break;
                        //Recherche par utilisateur
                        case 4:
                            $images = $this->getDoctrine()
                                ->getRepository('AppBundle:Image')
                                ->getListeImageByUtilisateurDossierPeriode($utilisateurId, $dossierId, $exercice, $dateDebut, $dateFin);
                            break;
                    }

                    $imageSeparations = array();

                }

                $initializeGrid = $this->initializeGrid($images, $imageSeparations, $categorieId);

                $rows = $initializeGrid['rows'];
                $listeImagesDownload = $initializeGrid['listeImagesDownload'];

                //Mbola tsy misy categorie voasafidy (Encours)
                if ($categorieId == -1) {
                    //Recherche par categorie
                    if ($typeSearch == 1) {
                        if (!$periodeSearch) {

                            $imagesEncours = $this->getDoctrine()
                                ->getRepository('AppBundle:Image')->getListeImageEncoursByDossier($dossierId, $exercice, $dateScanSearch, $dateDebut, $dateFin);

//                            $rowsEncours = $this->initializeGridEncours($imagesEncours);

                            $initializeGridEncours = $this->initializeGridEncours($imagesEncours);
                            $rowsEncours = $initializeGridEncours['rows'];
                            $listeImagesEncoursDownload = $initializeGridEncours['listeImagesDownload'];

                            foreach ($rowsEncours as $rowsEncour) {
                                $rows[] = $rowsEncour;
                            }

                        }
                    }
                    //Recherche par utilisateur
                    if ($typeSearch == 4) {
                        if (!$periodeSearch) {
                            $imagesEncours = $this->getDoctrine()
                                ->getRepository('AppBundle:Image')->getListeImageEncoursByUtilisateurDossier($utilisateurId, $dossierId, $exercice);

//                            $rowsEncours = $this->initializeGridEncours($imagesEncours);

                            $initializeGridEncours = $this->initializeGridEncours($imagesEncours);
                            $rowsEncours = $initializeGridEncours['rows'];
                            $listeImagesEncoursDownload = $initializeGridEncours['listeImagesDownload'];

                            foreach ($rowsEncours as $rowsEncour) {
                                $rows[] = $rowsEncour;
                            }

                        }
                    }
                }
            }

            //Jerena ireo mbola tsy misy catégorie
            else if ($categorieId < 0) {
                $imagesEncours = array();
                if (!$periodeSearch) {
                    if ($typeSearch == 1) {

                        $imagesEncours = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')->getListeImageEncoursByDossier($dossierId, $exercice);

                    } else if ($typeSearch == 4) {

                        $imagesEncours = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')->getListeImageEncoursByUtilisateurDossier($utilisateurId, $dossierId, $exercice);

                    }
                }

//                $rows = $this->initializeGridEncours($imagesEncours);

                $initializeGridEncours = $this->initializeGridEncours($imagesEncours);
                $rows = $initializeGridEncours['rows'];
                $listeImagesEncoursDownload = $initializeGridEncours['listeImagesDownload'];

            }

            /** @var  $utilsateur Utilisateur*/
            $utilsateur = $this->getUser();
            $isExpert = -1;

            if(null !== $utilsateur) {
                if ($utilsateur->getAccesUtilisateur()->getId() >= 3) {
                    $isExpert = 1;
                }
            }


            $isDownload = $post->get('download');
            $isDownload = filter_var($isDownload, FILTER_VALIDATE_BOOLEAN);

            if(!$isDownload) {
                $liste = array('rows' => $rows, 'isExpert' => $isExpert, 'showDossier' => 0);

                return new JsonResponse($liste);
            }
            else{

                $listeImages = array();


                foreach ($listeImagesDownload as $li){
                    $listeImages[] = $li;
                }

                foreach ($listeImagesEncoursDownload as $le){
                    $listeImages[] = $le;
                }


                if(count($listeImages) >0) {
                    $this->download($dossier->getSite()->getClient()->getId(), $listeImages);
                }


                else{
                    return $this->render('ConsultationPieceBundle:Default:index.html.twig');
                }



            }
        }
//        else {
//            throw new AccessDeniedException('Accès refusé');
//        }
    }

    /**
     * Tableau par date scan
     * @param Request $request
     * @return JsonResponse
     */
    public function tableauDateScanAction(Request $request)
    {
//        if ($request->isXmlHttpRequest())
 {

            $post = $request->request;

            $idSite = $post->get('siteId');
            $idDossier = $post->get('dossierId');

            $idClient = $post->get('clientId');

            $dateD = $post->get('dateDebut');
            $dateF = $post->get('dateFin');
            $exercice = $post->get('exercice');

            if($dateD !== '' && $dateF !== '') {
                $dateDebut = DateTime::createFromFormat('d/m/Y', $dateD);
                $dateFin = DateTime::createFromFormat('d/m/Y', $dateF);

                $dateDebut = $dateDebut->setTime(0, 0, 0);
                $dateFin = $dateFin->setTime(0, 0, 0);
            }
            else{
                $dateDebut = null;
                $dateFin = null;
            }



            $site = Boost::deboost($idSite, $this);

            $user = $this->getUser();
            $role = $this->getUser()->getRoles();

            $listeImagesDownload = array();
            $listeImagesEncoursDownload = array();

            $typeSearch = $post->get('typeSearch');


            $periodeSearch = $post->get('periodeSearch');
            $periodeSearch = filter_var($periodeSearch, FILTER_VALIDATE_BOOLEAN);

            $codeCategorie = $post->get('categorieId');

            $cat = $this->getDoctrine()
                ->getRepository('AppBundle:Categorie')
                ->findOneBy(array('code'=>$codeCategorie));

            $res = array();

            $dossierIds = array();

            $categorieId = null;

            if(!is_null($cat)){
                $categorieId = $cat->getCode();
            }
            else{
                $categorieId = -1;
            }

            if($site == ""){
                $site = 0;
            }



            $dossier = Boost::deboost($idDossier, $this);

            if($dossier == ""){
                $dossier = 0;
            }

            $client = Boost::deboost($idClient, $this);

            if($client == ""){
                $client = 0;
            }




            if($dossier != 0){

                $dossierIds[] = $dossier;
            }
            else{

                if($site != 0) {

                    $siteEntity = $this->getDoctrine()
                        ->getRepository('AppBundle:Site')
                        ->find($site);
                    $dossierSites = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->getUserDossier($user, $role, $siteEntity, $exercice);
                }
                else{

                    $clientEntity = $this->getDoctrine()
                        ->getRepository('AppBundle:Client')
                        ->find($client);

                    $dossierSites = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->getUserDossier($user, $clientEntity, null, $exercice);
                }

                /** @var  $dossierSite Dossier */
                foreach ($dossierSites as $dossierSite) {
                    $dossierIds[] = $dossierSite->getId();
                }
            }



            //Par date scan
            if($typeSearch == 6) {

                //Par date scan
                if($periodeSearch == false) {

                    if($dateDebut !== null && $dateFin !== ''){
                        $infoImage = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoImagesByDossierIds(implode(",",$dossierIds), -1, $exercice, true, false, $dateDebut->format('Y-m-d'), $dateFin->format('Y-m-d'));
                        $infoImageSeparation = $this->getDoctrine()
                            ->getRepository('AppBundle:Separation')
                            ->getInfoSeparationImagesByDossierIds(implode(",", $dossierIds), $exercice, true, $dateDebut->format('Y-m-d'), $dateFin->format('Y-m-d'));

                    }
                    else{
                        $infoImage = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoImagesByDossierIds(implode(",",$dossierIds), -1, $exercice, true, false, '', '');
                        $infoImageSeparation = $this->getDoctrine()
                            ->getRepository('AppBundle:Separation')
                            ->getInfoSeparationImagesByDossierIds(implode(",", $dossierIds), $exercice, true, '', '');

                    }



                    $initializeGridV2 = $this->initializeGridV2($infoImage, $infoImageSeparation, $categorieId);
                    $res = $initializeGridV2['rows'];
                    $listeImagesDownload = $initializeGridV2['listeImagesDownload'];

                    if($dateDebut !== '' && $dateFin !== '') {
                        $infoImageEncours = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoEncoursImagesByDossierIds(implode(",", $dossierIds), $exercice, true, $dateDebut->format('Y-m-d'), $dateFin->format('Y-m-d'));
                    }
                    else{
                        $infoImageEncours = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoEncoursImagesByDossierIds(implode(",", $dossierIds), $exercice, true, '', '');
                    }
                    $initializeGridEncoursV2 = $this->initializeGridEncoursV2($infoImageEncours);
                    $encours = $initializeGridEncoursV2['rows'];
                    $listeImagesEncoursDownload = $initializeGridEncoursV2['listeImagesEncoursDownload'];

                    if (count($encours) > 0) {
                        foreach ($encours as $encour) {
                            $res[] = $encour;
                        }
                    }
                }

                //Par date pièce
                else{

                    if($dateDebut !== '' && $dateFin !== '') {
                        $infoImage = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoImagesByDossierIds(implode(",", $dossierIds), -1, $exercice, false, true, $dateDebut->format('Y-m-d'), $dateFin->format('Y-m-d'));
                    }
                    else{
                        $infoImage = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getInfoImagesByDossierIds(implode(",", $dossierIds), -1, $exercice, false, true, '', '');
                    }

                    $initializeGridV2 = $this->initializeGridV2($infoImage, array(), $categorieId);
                    $res = $initializeGridV2['rows'];
                    $listeImagesDownload = $initializeGridV2['listeImagesDownload'];
                }
            }


            /** @var  $utilsateur Utilisateur*/
            $utilsateur = $this->getUser();
            $isExpert = -1;

            if(!is_null($utilsateur)) {
                if ($utilsateur->getAccesUtilisateur()->getId() >= 3) {
                    $isExpert = 1;
                }
            }

            $isDownload = $post->get('download');
            $isDownload = filter_var($isDownload, FILTER_VALIDATE_BOOLEAN);

            if(!$isDownload) {

                $liste = array('rows' => $res, 'isExpert' =>$isExpert, 'showDossier'=>1);

                return new JsonResponse($liste);
            }
            else {

                $listeImages = array();


                foreach ($listeImagesDownload as $li) {
                    $listeImages[] = $li;
                }

                foreach ($listeImagesEncoursDownload as $le) {
                    $listeImages[] = $le;
                }

                $this->download($client, $listeImages);

//                return new JsonResponse('telechargement effectué');
            }


        }

//        else {
//            throw new AccessDeniedHttpException('Accès refusé');
//        }
    }


    /**
     * Maka ny catogrie an'ilay pièce ho afficher-na (par numero de pièce)
     * @param Request $request
     * @return JsonResponse
     */
    public function getCategorieNumPieceAction(Request $request)
    {
        $post = $request->request;


        $numPiece = $post->get('numPiece');
        $exercice = $post->get('exercice');
        $idDossier = $post->get('idDossier');
        $idSite = $post->get('idSite');
        $idClient = $post->get('idClient');

        $numPieceArray = explode('.', $numPiece);

        if(count($numPieceArray) > 0){
            $numPiece = $numPieceArray[0];
        }

        $numPiece = str_replace(' ', '', $numPiece);

        if($idDossier != '') {
            $dossier = Boost::deboost($idDossier, $this);
        }
        else{
            $dossier = 0;
        }

        if($idSite != ''){
            $site = Boost::deboost($idSite, $this);
        }
        else{
            $site = 0;
        }

        if($idClient != ''){
            $client = Boost::deboost($idClient, $this);
        }
        else{
            $client = 0;
        }

        $dossierIds = array();

        $user = $this->getUser();
        $role = $this->getUser()->getRoles();

        if($dossier != 0){
            $dossierIds[] = $dossier;
        }
        else{
            if($site != 0){

                $siteEntity = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->find($site);

                $dossierSites = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->getUserDossier($user, $role, $siteEntity, $exercice);

            }
            else{

                $clientEntity = $this->getDoctrine()
                    ->getRepository('AppBundle:Client')
                    ->find($client);

                $dossierSites = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->getUserDossier($user, $clientEntity, null, $exercice);
            }

            /** @var Dossier $dossierSite */
            foreach ($dossierSites as $dossierSite) {
                $dossierIds[] = $dossierSite->getId();
            }
        }


        $images = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->getListeImageByDossierIdsNomImage($dossierIds, $numPiece, $exercice);

        $imageSeparations = $this->getDoctrine()
                ->getRepository('AppBundle:Separation')
                ->getListeImageSeparationByDossierIdsNomImage($dossierIds, $numPiece, $exercice);



//        if ($dossier != 0) {
//            $images = $this->getDoctrine()
//                ->getRepository('AppBundle:Image')
//                ->getListeImageByDossierNomImage($dossier, $numPiece, $exercice);

//            $imageSeparations = $this->getDoctrine()
//                ->getRepository('AppBundle:Separation')
//                ->getListeImageSeparationByDossierNomImage($dossier, $numPiece, $exercice);

//        } else if ($site != 0) {
//            $images = $this->getDoctrine()
//                ->getRepository('AppBundle:Image')
//                ->getListeImageBySiteNomImage($site, $numPiece, $exercice);

//            $imageSeparations = $this->getDoctrine()
//                ->getRepository('AppBundle:Separation')
//                ->getListeImageSeparationBySiteNomImage($site, $numPiece, $exercice);

//        } else {
//            $images = $this->getDoctrine()
//                ->getRepository('AppBundle:Image')
//                ->getListeImageByClientNomImage($client, $numPiece, $exercice);

//            $imageSeparations = $this->getDoctrine()
//                ->getRepository('AppBundle:Separation')
//                ->getListeImageSeparationByClientNomImage($client, $numPiece, $exercice);

//        }

        $categorie = '-1';



        if ($images != null) {

            /** @var  $images0  Image*/
            $images0 = $images[0];

            $listeSoussouscategorieImageByImageId = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->getListeSoussouscategorieImageByImageId($images0->getId());

            if ($listeSoussouscategorieImageByImageId != null) {
//                $categorie = $listeSoussouscategorieImageByImageId[0]['categorie']->getCode();

                /** @var  $categorieTemp Categorie*/
                $categorieTemp = $listeSoussouscategorieImageByImageId[0]['categorie'];

                $categorie = $categorieTemp->getCode();
            }
        }



        if($categorie == -1) {

            //Raha mbola tsy azo ny categorie dia jerena aloha any @ separation
            if($images != null){

                /** @var  $images0  Image*/
                $images0 = $images[0];

                $imSep = $this->getDoctrine()->getRepository('AppBundle:Separation')
                    ->findBy(array('image'=>$images0));
                if(count($imSep) > 0){
                    $categorie = $imSep[0]->getCategorie()->getCode();
                }

            }

            //        //Jerena any @separation ny catégorie an'ilay image raha mbola tsy any @ saisie
            else if ($imageSeparations != null) {
                /** @var  $imgSep Separation */
                $imgSep = $imageSeparations[0];
                if ($this->getDoctrine()
                        ->getRepository('AppBundle:Separation')
                        ->getCategorieImageByNomImage($imgSep->getImage()->getNom()) != null
                ) {
                    /** @var  $sepCat Separation*/
                    $sepCat = $this->getDoctrine()
                        ->getRepository('AppBundle:Separation')
                        ->getCategorieImageByNomImage($imgSep->getImage()->getNom())[0];

                    $categorie = $sepCat->getCategorie()->getCode();
                }
            }
        }

        return new JsonResponse($categorie);

    }

    /**
     * Tableau par numero de pièce
     * @param Request $request
     * @return JsonResponse
     */
    public function tableauNumPieceAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $post = $request->request;

            $numPiece = $post->get('numPiece');
            $exercice = $post->get('exercice');
            $idDossier = $post->get('dossierId');
            $idSite = $post->get('siteId');
            $idClient = $post->get('clientId');

            $numPieceArray = explode('.', $numPiece);

            if(count($numPieceArray) > 0){
                $numPiece = $numPieceArray[0];
            }

            $numPiece = str_replace(' ', '', $numPiece);


            if($idDossier != "") {
                $dossier = Boost::deboost($idDossier, $this);
            }
            else{
                $dossier = 0;
            }

            if($idSite != "") {
                $site = Boost::deboost($idSite, $this);
            }
            else{
                $site = 0;
            }

            if($idClient != "") {
                $client = Boost::deboost($idClient, $this);
            }
            else{
                $client = 0;
            }

            $dossierIds = array();

            $user = $this->getUser();
            $role = $this->getUser()->getRoles();

            if($dossier != 0){
                $dossierIds[] = $dossier;
            }
            else{
                if($site != 0){

                    $siteEntity = $this->getDoctrine()
                        ->getRepository('AppBundle:Site')
                        ->find($site);

                    $dossierSites = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->getUserDossier($user, $role, $siteEntity, $exercice);

                }
                else{

                    $clientEntity = $this->getDoctrine()
                        ->getRepository('AppBundle:Client')
                        ->find($client);

                    $dossierSites = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->getUserDossier($user, $clientEntity, null, $exercice);
                }

                /** @var Dossier $dossierSite */
                foreach ($dossierSites as $dossierSite) {
                    $dossierIds[] = $dossierSite->getId();
                }
            }



            $images = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->getListeImageByDossierIdsNomImage($dossierIds, $numPiece, $exercice);


            $imageSeparations = $this->getDoctrine()
                ->getRepository('AppBundle:Separation')
                ->getListeImageSeparationByDossierIdsNomImage($dossierIds, $numPiece, $exercice);


            $imageEncours = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->getListeImageEncoursByDossierIdsNomImage(implode(",",$dossierIds),$numPiece, $exercice);



            if($dossier !=0) {
//                $images = $this->getDoctrine()
//                    ->getRepository('AppBundle:Image')
//                    ->getListeImageByDossierNomImage($dossier, $numPiece, $exercice);

//                $imageSeparations = $this->getDoctrine()
//                    ->getRepository('AppBundle:Separation')
//                    ->getListeImageSeparationByDossierNomImage($dossier, $numPiece, $exercice);

//                $imageEncours = $this->getDoctrine()
//                    ->getRepository('AppBundle:Image')
//                    ->getListeImageEncoursByDossierNomImage($dossier,$numPiece, $exercice);


            }else if ($site != 0) {
//                $images = $this->getDoctrine()
//                    ->getRepository('AppBundle:Image')
//                    ->getListeImageBySiteNomImage($site, $numPiece, $exercice);

//                $imageSeparations = $this->getDoctrine()
//                    ->getRepository('AppBundle:Separation')
//                    ->getListeImageSeparationBySiteNomImage($site,$numPiece , $exercice);

//                $imageEncours = $this->getDoctrine()
//                    ->getRepository('AppBundle:Image')
//                    ->getListeImageEncoursBySiteNomImage($site, $numPiece, $exercice);
            }else {
//                $images = $this->getDoctrine()
//                    ->getRepository('AppBundle:Image')
//                    ->getListeImageByClientNomImage($client, $numPiece, $exercice);

//                $imageSeparations = $this->getDoctrine()
//                    ->getRepository('AppBundle:Separation')
//                    ->getListeImageSeparationByClientNomImage($client, $numPiece, $exercice);

//                $imageEncours = $this->getDoctrine()
//                    ->getRepository('AppBundle:Image')
//                    ->getListeImageEncoursByClientNomImage($client,$numPiece, $exercice);
            }

            $categorie = -1;

            if ($images != null) {

                /** @var  $images0  Image*/
                $images0 = $images[0];


                $listeSoussouscategorieImageByImageId = $this->getDoctrine()
                    ->getRepository('AppBundle:Image')
                    ->getListeSoussouscategorieImageByImageId($images0->getId());

                if ($listeSoussouscategorieImageByImageId != null) {
                    /** @var  $categorieTemp Categorie*/
                    $categorieTemp = $listeSoussouscategorieImageByImageId[0]['categorie'];

                    $categorie = $categorieTemp->getId();
                }
            }




            if($categorie == -1) {

                //Raha mbola tsy azo ny categorie dia jerena aloha any @ separation
                if($images != null){

                    /** @var  $images0  Image*/
                    $images0 = $images[0];

                    $imSep = $this->getDoctrine()->getRepository('AppBundle:Separation')
                        ->findBy(array('image'=>$images0));
                    if(count($imSep) > 0){
                        $categorie = $imSep[0]->getCategorie()->getId();
                    }

                }

                //Jerena any @separation ny catégorie an'ilay image raha mbola tsy any @ saisie
                else if ($imageSeparations != null) {
                    /** @var  $imgSep Separation */
                    $imgSep = $imageSeparations[0];
                    if ($this->getDoctrine()
                            ->getRepository('AppBundle:Separation')
                            ->getCategorieImageByNomImage($imgSep->getImage()->getNom()) != null
                    ) {
                        /** @var  $sepCat Separation */
                        $sepCat = $this->getDoctrine()
                            ->getRepository('AppBundle:Separation')
                            ->getCategorieImageByNomImage($imgSep->getImage()->getNom())[0];
                        $categorie = $sepCat->getCategorie()->getId();
                    }
                }
            }




            if($categorie != -1) {
//                $res = $this->initializeGrid($images, $imageSeparations, $categorie);

                $initializeGrid = $this->initializeGrid($images, $imageSeparations, $categorie);
                $res = $initializeGrid['rows'];
            }
            else{
//                $res = $this->initializeGridEncours($imageEncours);

                $initializeGridEncours = $this->initializeGridEncours($imageEncours);
                $res = $initializeGridEncours['rows'];
            }

            /** @var  $utilsateur Utilisateur*/
            $utilsateur = $this->getUser();
            $isExpert = -1;
            if($utilsateur->getAccesUtilisateur()->getId() >= 3){
                $isExpert = 1;
            }

            $liste = array('rows' => $res, 'isExpert' => $isExpert, 'showDossier' => 1);

            return new JsonResponse($liste);
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }

    }

    /**
     * Tableau Commun Tiers
     * @param Request $request
     * @return JsonResponse
     */
    public function tableauTiersAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $dossierId = $post->get('dossierId');
            $tiersId = $post->get('tiersId');
            $exercice = $post->get('exercice');

            $dateDebut = '';
            $dateFin = '';
            $periodeSearch = $post->get('periodeSearch');

            $periodeSearch = filter_var($periodeSearch, FILTER_VALIDATE_BOOLEAN);

            $dateD = $post->get('dateDebut');
            $dateF = $post->get('dateFin');

            if ($dateD != '' && $dateF != '') {
                $dateDebut = DateTime::createFromFormat('d/m/Y', $dateD);
                $dateFin = DateTime::createFromFormat('d/m/Y', $dateF);

                $dateDebut = $dateDebut->setTime(0, 0, 0);
                $dateFin = $dateFin->setTime(0, 0, 0);
            }

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $rows = array();

            $images = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->getListeImageImputeeByDossier($dossier, $exercice);


            foreach ($images as $image) {

                /** @var $image Image */

                if($image->getId() == 354449 ){
                    $izy = true;
                }

                $results = $this->getDoctrine()
                    ->getRepository('AppBundle:Image')
                    ->getInfosImageByImageId($image->getId());

                /** @var $res0 Imputation*/
                if ($results !== null) {

                    $res0 = $results['saisie'][0];
                    $tableSaisie = $results['tableSaisie'];

                    switch ($tableSaisie) {
                        case 'Saisie 1':
                            $tableSaisie = 'Saisie';
                            break;
                        case 'Saisie 2':
                            $tableSaisie = 'Saisie';
                            break;
                        case 'Controle Saisie':
                            $tableSaisie = 'Saisie';
                            break;
                        case 'Imputation':
                            $tableSaisie = 'Imputée';
                            break;
                        case 'Controle Imputation':
                            $tableSaisie = 'Imputée';
                            break;
                    }

                    /**@var $imgTiers Tiers */
                    $imgTiers = $this->getDoctrine()
                        ->getRepository('AppBundle:Image')
                        ->getTiersImageByNomImage($image->getNom());

                    $compteTiers = '';
                    $compteRes = '';

                    if ($tiersId != -1) {
                        $canShow = false;
                        if ($imgTiers != null) {
                            if ($imgTiers->getId() == $tiersId) {
                                $compteTiers = $imgTiers->getCompteStr();
                                /** @var  $resTvai TvaImputation */
//                                $resTvai = $results[0]['tva'][0];
//
//                                if ($resTvai->getPcc() != null) {
//                                    $compteRes = $resTvai->getPcc()->getCompte();
//                                }
                                $canShow = true;
                            }
                        }
                    } else {
                        $canShow = false;
                        if ($imgTiers != null) {
                            $compteTiers = $imgTiers->getCompteStr();
                            /** @var  $resTvai TvaImputation */
//                            $resTvai = $results[0]['tva'][0];

//                            if ($resTvai->getPcc() != null) {
//                                $compteRes = $resTvai->getPcc()->getCompte();
//                            }
                            $canShow = true;
                        }
                    }

                    if ($canShow) {
                        $canShowPeriode = false;

                        if ($periodeSearch) {
                            if ($dateDebut != '' && $dateFin != '') {
                                if ($dateDebut <= $res0->getDateFacture() && $dateFin >= $res0->getDateFacture()) {
                                    $canShowPeriode = true;
                                }
                            } else {
                                $canShowPeriode = true;
                            }
                        } else {
                            $canShowPeriode = true;
                        }

                        if ($canShowPeriode) {
                            $sommeHt = 0;
                            $sommeTva = 0;

                            $resTva = $results['tva'];
                            for ($i = 0, $iMax = count($resTva); $i < $iMax; $i++) {
                                /**@var $tva TvaSaisie1 */
                                $tva = $resTva[$i];
                                $sommeHt += $tva->getMontantHt();
                                if ($tva->getTvaTaux() != null) {
                                    $sommeTva += ($tva->getMontantHt()) * ($tva->getTvaTaux()->getTaux()) / 100;
                                }
                            }

                            $categorieLib = '';
                            $ssCategorieLib = '';
                            $sousCategorieLib = '';

                            if ($res0->getSoussouscategorie() != null) {
                                $categorieLib = $res0->getSoussouscategorie()->getSouscategorie()->getCategorie()->getLibelleNew();
                                $ssCategorieLib = $res0->getSoussouscategorie()->getLibelleNew();
                                $sousCategorieLib = $res0->getSoussouscategorie()->getSouscategorie()->getLibelleNew();
                            }
                            //Raha tsy misy soussouscategorie dia tokony jerena ny souscategorie raha efa imputée ilay sary
                            else if($tableSaisie == 'Imputée'){

                                if($res0->getSouscategorie() != null){
                                    $categorieLib = $res0->getSouscategorie()->getCategorie()->getLibelleNew();
                                    $sousCategorieLib = $res0->getSouscategorie()->getLibelleNew();
                                }

                                //Raha mbola tsy misy dia any @ separation no jerena farany
                                else{
                                    $sep = $this->getDoctrine()
                                        ->getRepository('AppBundle:Separation')
                                        ->findBy(array('image'=>$image));

                                    if(count($sep) > 0){
                                        if($sep[0]->getSouscategorie() !== null){
                                            $sousCategorieLib = $sep[0]->getSouscategorie()->getLibelleNew();
                                            $categorieLib = $sep[0]->getSouscategorie()->getCategorie()->getLibelleNew();
                                        }
                                        elseif($sep[0]->getCategorie() != null){

                                            $categorieLib = $sep[0]->getCategorie()->getLibelleNew();
                                        }
                                    }
                                }
                            }

                            $dateFacture = '';
                            $dateScan = '';
                            if ($res0->getDateFacture() != null) {
                                $dateFacture = $res0->getDateFacture()->format('Y-m-d');

                            }

                            if ($res0->getImage()->getLot()->getDateScan() != null) {
                                $dateScan = $res0->getImage()->getLot()->getDateScan()->format('Y-m-d');
                            }

                            //Raha tsy misy soussosus-catégorie => tsy misy catégorie : dia tsy afficher-na
                            if ($categorieLib != '') {
                                $rows[] = array(
                                    'id' => $res0->getImage()->getId(),
                                    'cell' => array(
                                        $res0->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        $sousCategorieLib,
                                        $tableSaisie,
                                        $compteTiers,
                                        $res0->getImage()->getExercice(),
                                        '<i class="fa fa-file-text"></i>',
                                        $res0->getImage()->getNom(),
                                        number_format($sommeHt, 2, '.', ''),
                                        number_format($sommeTva, 2, '.', ''),
                                        number_format($sommeHt + $sommeTva, 2, '.', ''),
                                        $dateFacture,
                                        $dateScan
                                    )
                                );
                            }
                        }
                    }
                }
            }

            /** @var  $utilsateur Utilisateur*/
            $utilsateur = $this->getUser();
            $isExpert = -1;
            if($utilsateur->getAccesUtilisateur()->getId() >= 3){
                $isExpert = 1;
            }

            $liste = array('rows' => $rows, 'isExpert' => $isExpert, 'showDossier'=> 0);

            return new JsonResponse($liste);
        } else {
            throw new AccessDeniedException('Accès refusé');
        }
    }

    /**
     * Mi-afficher ny combo exercice
     * @return Response
     */
    public function exerciceAction()
    {
        $exercices = Boost::getExercices(7,1);

        return $this->render('ConsultationPieceBundle:Default:exerciceValue.html.twig', array('exercices' => $exercices));
    }















    /**
     * Affichage Image
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function imageAction(Request $request)
    {
        $post = $request->request;
        $image_id = $post->get('image_id');

        if (is_bool($image_id)) return new Response('security');

        $image = $this->getDoctrine()->getRepository('AppBundle:Image')->createQueryBuilder('im')
            ->where('im.id = :id')
            ->setParameter('id', $image_id)
            ->getQuery()
            ->getOneOrNullResult();

        return new Response('images/' .
            $image->getLot()->getDossier()->getSite()->getClient()->getNom() . '/' .
            $image->getLot()->getDossier()->getNom() . '/' .
            $image->getExercice() . '/' .
            $image->getLot()->getDateScan()->format('Y-m-d') . '/' .
            $image->getLot()->getLot() . '/' .
            $image->getNom() . '.' . $image->getExtImage());
    }

      /**
     * Affichage Data + Image
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function dataImageShowAction(Request $request)
    {
        $post = $request->request;

        $imageId = $post->get('imageId');

        $cr = $post->get('cr');

        if($cr == 1){
            $imageId = Boost::deboost($imageId, $this);
        }

        $donneesSaisie = null;

        $height = $post->get('height');

        $height = (float)$height - 40;

        if (is_bool($imageId)) return new Response('security');


        /** @var Image $image */
        $image = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->find($imageId);


        $dateScan = $image->getLot()->getDateScan()->format("Ymd");

//PICDATA
//        $chemin = $_SERVER['DOCUMENT_ROOT'].'/IMAGES/' . $image->getLot()
//                ->getDossier()->getSite()->getClient()->getId() . '/'.$dateScan. '/' . $image->getNom() . '.' . $image->getExtImage();

        $chemin = $_SERVER['DOCUMENT_ROOT'].'/IMAGES/'
                 . '/'.$dateScan. '/' . $image->getNom() . '.' . $image->getExtImage();

//LOCAL
//        $chemin = $_SERVER['DOCUMENT_ROOT'].'/picdata/web/IMAGES/' . $image->getLot()
//                ->getDossier()->getSite()->getClient()->getId() . '/'.$dateScan. '/' . $image->getNom() . '.' . $image->getExtImage();

//192.168.0.5
//        $chemin = $_SERVER['DOCUMENT_ROOT'].'/newpicdata/web/IMAGES/' . $image->getLot()
//                ->getDossier()->getSite()->getClient()->getId() . '/'. $dateScan .'/'. $image->getNom() . '.' . $image->getExtImage();


        $fileExist = file_exists($chemin);

        $onLesexperts = true;

        if($fileExist == true){

            //PICDATA
//            $chemin = '/IMAGES/' . $image->getLot()
//                    ->getDossier()->getSite()->getClient()->getId() . '/'.$dateScan. '/' . $image->getNom() . '.' . $image->getExtImage();

            $chemin = '/IMAGES/' . '/'.$dateScan. '/' . $image->getNom() . '.' . $image->getExtImage();

//LOCAL
//        $chemin = '/picdata/web/IMAGES/' . $image->getLot()
//                ->getDossier()->getSite()->getClient()->getId() . '/'.$dateScan. '/' . $image->getNom() . '.' . $image->getExtImage();

//192.168.0.5
//        $chemin = '/newpicdata/web/IMAGES/' . $image->getLot()
//                ->getDossier()->getSite()->getClient()->getId() . '/'. $dateScan .'/'. $image->getNom() . '.' . $image->getExtImage();

        }

        else {


//PICDATA

//            $chemin = $_SERVER['DOCUMENT_ROOT'] . '/IMAGES/' . $image->getLot()
//                    ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();

            $chemin = $_SERVER['DOCUMENT_ROOT'] . '/IMAGES/' . $image->getLot()
                    ->getDossier()->getSite()->getClient()->getId().'/'.$dateScan . '/' . $image->getNom() . '.' . $image->getExtImage();


//LOCAL
//        $chemin = $_SERVER['DOCUMENT_ROOT'].'/picdata/web/IMAGES/' . $image->getLot()
//                ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();

//192.168.0.5
//        $chemin = $_SERVER['DOCUMENT_ROOT'].'/newpicdata/web/IMAGES/' . $image->getLot()
//                ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();

            $fileExist = file_exists($chemin);

            if ($fileExist == true) {

//PICDATA
                $chemin = '/IMAGES/' . $image->getLot()
                        ->getDossier()->getSite()->getClient()->getId() .'/'. $dateScan . '/' . $image->getNom() . '.' . $image->getExtImage();

//LOCAL
//            $chemin = '/picdata/web/IMAGES/' . $image->getLot()
//                    ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();

//192.168.0.5
//            $chemin = '/newpicdata/web/IMAGES/' . $image->getLot()
//                    ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();
            } else {

                $onLesexperts = false;

                $file = $this->getOvhPath($image);

                $chemin = 'http://picdata.fr/picdataovh/' . 'images/' .$file;
            }
        }

//        $embed = '<embed src="' . $chemin . '" width="100%" height="100%" id="js_embed"/>';
//        $chemin = 'http://lesexperts.biz/IMAGES/20180423/ICZ0009K6.pdf';


        $imgLists = ['jpg', 'jpeg', 'tiff', 'gif', 'tif', 'png'];

        //$chemin = $this->getUrl($imageId);


        if(in_array(strtolower($image->getExtImage()), $imgLists)){
            $embed = '<img src="'.$chemin.'" style="width:100%;">';
        }
        else if(strtolower($image->getExtImage()) === "pdf") {

            if($image->getSourceImage()->getId() === 15){
                $chemin = '/IMAGES/IMAGE_FANTOME/IMAGE.pdf' ;
            }

            $embed = '<object id="js_embed" 
            width="100%" 
            height="100%" 
            type="application/pdf" 
            trusted="yes" 
            application="yes" 
            title="IMAGE" 
            data="' . $chemin .
                '?#scrollbar=1&toolbar=0&navpanes=1">
           <p>Votre  navigateur ne peut pas affichier le fichier PDF. Vous pouvez le télécharger en cliquant <a target="_blank" href="' . $chemin . '" style="text-decoration: underline;">ICI</a></p>
        </object>';
        }
        else if(strpos($image->getExtImage(), 'xls') !== false || strpos($image->getExtImage(), 'doc') !== false){
            if($onLesexperts){
                $chemin = 'https://lesexperts.biz'.$chemin;
            }
            $embed  = '<iframe src="https://view.officeapps.live.com/op/embed.aspx?src='.$chemin.'" width="100%" height='.$height.'px frameborder="0"> </iframe>';
        }
        else{
            $embed = '<p>Votre  navigateur ne peut pas affichier ce type de fichier. Vous pouvez le télécharger en cliquant <a target="_blank" href="' . $chemin . '" style="text-decoration: underline;">ICI</a></p>';
        }

        $infos = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->getInfosImageByImageId($image->getId());

        $infosSep = $this->getDoctrine()
            ->getRepository('AppBundle:Separation')
            ->getListeImageSeparationByClientIdImage($image->getId());

        $infoSep = null;

        if($infosSep != null){
            /** @var  $infoSep Separation*/
            $infoSep = $infosSep[0];
        }

        /** @var  $dossier Dossier*/
        $dossier = $image->getLot()->getDossier();


        if (null !== $infos) {
            $etape = $infos['tableSaisie'];
        }

        else{

            if($infoSep != null) {

                $etape = 'Catégorisée';
            }
            else{
                $etape = 'Reçue';
            }
        }

        $tvaSaisie = null;
        /** @var ImputationControleCegj $cegjSaisie */
        $cegjSaisie = null;


        $etapeId = 0;

        switch ($etape) {
            case 'Saisie 1':
                /**@var $donneesSaisie Saisie1 */
                $donneesSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:Saisie1')
                    ->findOneByImage($imageId);

                $cegjSaisies = $this->getDoctrine()
                    ->getRepository('AppBundle:Saisie1Cegj')
                    ->findBy(array('image' => $image));

                if(count($cegjSaisies) > 0){
                    $cegjSaisie = $cegjSaisies[0];
                }

                $etapeId = 11;
                $etape = 'Saisie';

                break;

            case 'Saisie 2':
                /**@var $donneesSaisie Saisie2 */
                $donneesSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:Saisie2')
                    ->findOneByImage($imageId);

                $cegjSaisies = $this->getDoctrine()
                    ->getRepository('AppBundle:Saisie2Cegj')
                    ->findBy(array('image' => $image));

                if(count($cegjSaisies) > 0){
                    $cegjSaisie = $cegjSaisies[0];
                }

                $etapeId = 12;
                $etape = 'Saisie';

                break;

            case 'Controle Saisie':
                /**@var $donneesSaisie \AppBundle\Entity\SaisieControle */
                $donneesSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:SaisieControle')
                    ->findOneByImage($imageId);

                $cegjSaisies = $this->getDoctrine()
                    ->getRepository('AppBundle:ControleCegj')
                    ->findBy(array('image' => $image));

                if(count($cegjSaisies) > 0){
                    $cegjSaisie = $cegjSaisies[0];
                }

                $etapeId = 13;
                $etape = 'Saisie';

                break;

            case 'Imputation':
                /** @var $donneesSaisie Imputation */
                $donneesSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:Imputation')
                    ->findOneByImage($imageId);

                $tvaSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:TvaImputation')
                    ->getGroupedTvaImputationByImage($image);


                $cegjSaisies = $this->getDoctrine()
                    ->getRepository('AppBundle:ImputationCegj')
                    ->findBy(array('image' => $image));

                if(count($cegjSaisies) > 0){
                    $cegjSaisie = $cegjSaisies[0];
                }

                $etapeId = 14;
                $etape = 'Imputée';

                break;

            case 'Controle Imputation':
                /** @var $donneesSaisie ImputationControle */
                $donneesSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:ImputationControle')
                    ->findOneByImage($imageId);


                $tvaSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:TvaImputationControle')
                    ->getGroupedTvaImputationControleByImage($image);

                $cegjSaisies = $this->getDoctrine()
                    ->getRepository('AppBundle:ImputationControleCegj')
                    ->findBy(array('image' => $image));

                if(count($cegjSaisies) > 0){
                    $cegjSaisie = $cegjSaisies[0];
                }

                $etapeId = 15;
                $etape = 'Imputée';

                break;

            default:
                $cegjSaisies = $this->getDoctrine()
                    ->getRepository('AppBundle:Saisie1Cegj')
                    ->findBy(array('image' => $image));

                if(count($cegjSaisies) > 0){
                    $cegjSaisie = $cegjSaisies[0];
                }

                break;

        }


        $image = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->find($imageId);


        $modeReglements = $this->getDoctrine()
            ->getRepository('AppBundle:ModeReglement')
            ->findBy(array(), array('libelle' => 'ASC'));


        $modeReglement = null;


        if(null !== $donneesSaisie){
            $modeReglement = $donneesSaisie->getModeReglement();
        }

        $isAdmin = false;
        $isSuperAdmin = false;


        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        if ($this->isGranted("ROLE_SCRIPTURA_ADMIN") || $this->isGranted("ROLE_SCRIPTURA_RESP")) {
            if($utilisateur->getId() !== 2438){
                $isAdmin = true;
            }
        }

        if($this->isGranted('ROLE_SCRIPTURA_ADMIN') && $utilisateur->getTypeUtilisateur() !== null){
            if($utilisateur->getTypeUtilisateur()->getId() === 1)
                $isSuperAdmin = true;
        }

        if(!$isAdmin) {
            $accesUtilisateur = $utilisateur->getAccesUtilisateur();
            $menuParRoles = $this->getDoctrine()
                ->getRepository('AppBundle:MenuParRole')
                ->getMenuParRole($accesUtilisateur);

            /** @var MenuParRole $menuParRole */
            foreach ($menuParRoles as $menuParRole) {
                if ($menuParRole->getMenu()->getId() === 78) {
                    if ($menuParRole->getCanEdit()) {
                        $isAdmin = true;
                    }
                }
            }
        }

        $soussouscategorie = null;
        $souscategorie = null;
        /** @var Categorie $categorie */
        $categorie = null;

        $categorieCode = '';


//        if(count($tvaSaisie) > 0){

//            foreach ($tvaSaisie as $tvaG){
//                /** @var TvaImputation $tva */
//                $tva = $tvaG[0];

//                if(null !== $tva->getSoussouscategorie()){
//                    $soussouscategorie = $tva->getSoussouscategorie();
//                    $souscategorie = $tva->getSoussouscategorie()->getSouscategorie();
//                    $categorie = $tva->getSoussouscategorie()->getSouscategorie()->getCategorie();
//                    $categorieCode = $categorie->getCode();
//                    break;
//                }
//            }
//        }
//        if($categorieCode === '' && null != $donneesSaisie){
//            if(null !== $donneesSaisie->getSoussouscategorie()){
//                $soussouscategorie = $donneesSaisie->getSoussouscategorie();
//                $souscategorie = $donneesSaisie->getSoussouscategorie()->getSouscategorie();
//                $categorie = $donneesSaisie->getSoussouscategorie()->getSouscategorie()->getCategorie();
//                $categorieCode = $categorie->getCode();
//            }
//            else {
//                if(strtolower($etape) !== "saisie") {
//                    if (null !== $donneesSaisie->getSouscategorie()) {
//                        $souscategorie = $donneesSaisie->getSouscategorie();
//                        $categorie = $donneesSaisie->getSouscategorie()->getCategorie();
//                        $categorieCode = $categorie->getCode();
//                    }
//                }
//            }
//        }
        if($categorieCode === '' && null != $infoSep){
            if(null !== $infoSep->getSoussouscategorie()){
                $soussouscategorie = $infoSep->getSoussouscategorie();
                $souscategorie = $infoSep->getSoussouscategorie()->getSouscategorie();
                $categorie = $infoSep->getSoussouscategorie()->getSouscategorie()->getCategorie();
                $categorieCode = $categorie->getCode();
            }
            else if(null !== $infoSep->getSouscategorie()){
                $souscategorie = $infoSep->getSouscategorie();
                $categorie = $infoSep->getSouscategorie()->getCategorie();
                $categorieCode = $categorie->getCode();
            }
            else if(null !== $infoSep->getCategorie()){
                $categorie = $infoSep->getCategorie();
                $categorieCode = $categorie->getCode();
            }
        }


        //Calcul date echeance
        $dateEcheance = null;


        $tiers = null;

        if($donneesSaisie != null) {

            if($donneesSaisie->getDateEcheance() != null) {

                $dateEcheance = $donneesSaisie->getDateEcheance();
            }

            else{


                if($etape === 'Imputée') {

                    foreach ($tvaSaisie as $tvaG) {

                        /** @var  $tva TvaImputation*/
                        $tva = $tvaG[0];

                        if ($tva->getTiers() != null){
                            $tiers = $tva->getTiers();
                            break;
                        }
                    }
                }

                if($categorieCode === 'CODE_FRNS' || $categorieCode === 'CODE_CLIENT') {
                    $dateEcheance = $this->calculDateEcheance($image->getLot()->getDossier(), $tiers, $donneesSaisie->getDateFacture(), $donneesSaisie->getDateLivraison(), $categorieCode);
                }
            }
        }

        $codeANPC = ['CODE_COURRIER', 'CODE_ETATS_COMPTABLE',
            'CODE_GESTION', 'CODE_JURIDIQUE', 'CODE_A_RECATEGORISER',
            'CODE_ILLISIBLE', 'CODE_IMAGE'];

        $codeClientFournisseur = ['CODE_CLIENT', 'CODE_FRNS'];



        if($categorie !== null) {
            if ($categorie->getCode() === 'CODE_INSTANCE1' || $categorie->getCode() === 'CODE_ANPC') {
                $categorieTmps = $this->getDoctrine()
                    ->getRepository('AppBundle:Categorie')
                    ->findBy(array('code' => 'CODE_IMAGE'));

                if (count($categorieTmps) > 0) {
                    $categorie = $categorieTmps[0];
                }
            }
        }


        $categories = [];
        /** @var  Souscategorie[] $souscategories */
        $souscategories = null;
        if(null !== $categorie){
            $souscategories = $this->getDoctrine()
                ->getRepository('AppBundle:Souscategorie')
                ->findBy(array('categorie' => $categorie, 'actif' => 1) ,array('libelleNew'=>'ASC'));

            if(in_array($categorie->getCode(), $codeANPC)){
                /** @var Categorie[] $categories */
                $categories = $this->getDoctrine()
                    ->getRepository('AppBundle:Categorie')
                    ->getCategoriesByCodes($codeANPC);
            }
            elseif (in_array($categorie->getCode(), $codeClientFournisseur)){
                $categories = $this->getDoctrine()
                    ->getRepository('AppBundle:Categorie')
                    ->getCategoriesByCodes($codeClientFournisseur);
            }
        }


        /** @var Soussouscategorie[] $soussouscategories */
        $soussouscategories = null;
        if(null !== $souscategorie){
            $soussouscategories = $this->getDoctrine()
                ->getRepository('AppBundle:Soussouscategorie')
                ->findBy(array('souscategorie' => $souscategorie, 'actif' => 1) ,array('libelleNew'=>'ASC'));
        }

        $reglePaiementDossier = null;
        if($categorieCode === 'CODE_CLIENT' || $categorieCode === 'CODE_FRNS'){

            if($categorieCode === 'CODE_FRNS'){
                $typeTiers = 0;
            }
            else{
                $typeTiers = 1;
            }

            $reglePaiementDossiers = $this->getDoctrine()
                ->getRepository('AppBundle:ReglePaiementDossier')
                ->findBy(array('dossier'=>$dossier, 'typeTiers'=>$typeTiers));



            if(count($reglePaiementDossiers) > 0){
                $reglePaiementDossier = $reglePaiementDossiers[0];
            }
        }

        /** @var  $reglePaiementTier ReglePaiementTiers*/
        $reglePaiementTier = null;

        if($tiers !== null){
            $reglePaiementTiers = $this->getDoctrine()
                ->getRepository('AppBundle:ReglePaiementTiers')
                ->findBy(array('dossier'=>$dossier, 'tiers'=>$tiers));

            if(count($reglePaiementTiers) > 0){
                $reglePaiementTier = $reglePaiementTiers[0];
            }
        }


        //Montant avoir, image, mode paiement, date paiement, numero de paiement, reste à payer, relevé
        //

        /** @var ImageImage[] $imageImages */
        $imageReleves = $this->getDoctrine()
            ->getRepository('AppBundle:ImageImage')
            ->getImageImageByImage($image, 3);


        /** @var ImageImage[] $imageAvoirs */
        $imageAvoirs = $this->getDoctrine()
            ->getRepository('AppBundle:ImageImage')
            ->getImageImageByImage($image, 2);



        /** @var Image[] $avoirs */
        $avoirs = array();
        foreach ($imageAvoirs as $imageAvoir){
            $avoir = $imageAvoir->getImage();

            if(!in_array($avoir, $avoirs) && $avoir !== null){
                $avoirs[] = $avoir;
            }
        }


        /** @var Imputation[] $avoirSaisies */
        $avoirSaisies = array();
        /** @var TvaImputation[] $avoirTvas */
        $avoirTvas = array();

        foreach ($avoirs as $avoir){

            $info = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->getInfosImageByImageId($avoir->getId());

            if(count($info) > 0) {
                if($info['saisie']  !== null) {
                    if (count($info['saisie']) > 0) {
                        $avoirSaisies[] = $info['saisie'][0];
                    }
                }

                if($info['tva'] !== null) {
                    foreach ($info['tva'] as $tva) {
                        $avoirTvas[] = $tva;
                    }
                }
            }
        }


        $montantAvoir = 0;
        /** @var  TvaImputation $tvaImputation */
        foreach ($avoirTvas as $tvaImputation){
            $montant = $tvaImputation->getMontantHt();

            $taux = 0;
            if ($tvaImputation->getTvaTaux() !== null) {
                $taux = $tvaImputation->getTvaTaux()->getTaux();
            }

            if ($taux !== 0) {
                $montant = $montant * (1 + ($taux / 100));
            }

            $montantAvoir += $montant;
        }



        /** @var Releve[] $releves */
        $releves = array();

        /** @var ImageImage $imageReleve */
        foreach ($imageReleves as $imageReleve){
            $releve = $imageReleve->getReleve();

            if(!in_array($releve, $releves) && $releve !== null){
                $releves[] = $releve;
            }
        }


        /** @var Releve $releve */
        foreach ($releves as $releve){
            if($releve->getDebit() !== null){
                $montantAvoir = $montantAvoir + (float)$releve->getDebit();
            }
            if($releve->getCredit() !== null){
                $montantAvoir = $montantAvoir - (float)$releve->getCredit();
            }
        }

        $montantSaisis = 0;
        if(null !== $tvaSaisie) {
            foreach ($tvaSaisie as $tvaImputationG) {

                $tvaImputation = $tvaImputationG[0];

                $montant = $tvaImputation->getMontantHt();

                $taux = 0;
                if ($tvaImputation->getTvaTaux() !== null) {
                    $taux = $tvaImputation->getTvaTaux()->getTaux();
                }

                if ($taux !== 0) {
                    $montant = $montant * (1 + ($taux / 100));
                }

                $montantSaisis += $montant;
            }
        }

        $reste = $montantSaisis - $montantAvoir;


        $ecrTtc = [];
        $ecrTva = [];
        $ecrHt = [];

        if($tvaSaisie !== null) {
            foreach ($tvaSaisie as $tvaG) {
                $tva = $tvaG[0];
                $trouve = false;
                for ($i = 0; $i < count($ecrTtc); $i++) {
                    if ($ecrTtc[$i]['compte'] === $tva->getTiers()) {
                        $ecrTtc[$i]['montant'] = $ecrTtc[$i]['montant'] + $tvaG['ttc'];
                        $trouve = true;
                    }
                }
                if (!$trouve) {
                    $ecrTtc[] = ['compte' => $tva->getTiers(), 'montant' => $tvaG['ttc']];
                }

                $trouve = false;
                for ($i = 0; $i < count($ecrTva); $i++) {
                    if ($ecrTva[$i]['compte'] === $tva->getPccTva()) {
                        $ecrTva[$i]['montant'] = $ecrTva[$i]['montant'] + $tvaG['tva'];
                        $trouve = true;
                    }
                }
                if (!$trouve) {
                    $ecrTva[] = ['compte' => $tva->getPccTva(), 'montant' => $tvaG['tva']];
                }


                $trouve = false;
                for ($i = 0; $i < count($ecrHt); $i++) {
                    if ($ecrHt[$i]['compte'] === $tva->getPcc()) {
                        $ecrHt[$i]['montant'] = $ecrHt[$i]['montant'] + $tvaG['ht'];
                        $trouve = true;
                    }
                }
                if (!$trouve) {
                    $ecrHt[] = ['compte' => $tva->getPcc(), 'montant' => $tvaG['ht']];
                }
            }
        }

        $typeEcriture = 0;

        /** @var Ecriture[] $ecritures */
        $ecritures = $this->getDoctrine()
            ->getRepository('AppBundle:Ecriture')
            ->getEcrituresByImage($image, $typeEcriture);

        $banqueComptes = null;
        /** @var Banque[] $banques */
        $banques = null;


        if($dossier){
            /** @var BanqueCompte[] $banqueComptes */
            $banqueComptes = $this->getDoctrine()
                ->getRepository('AppBundle:BanqueCompte')
                ->findBy(array('dossier' => $dossier));

            foreach ($banqueComptes as $banqueCompte){
                $banques[] = $banqueCompte->getBanque();
            }

            if($donneesSaisie) {
                if ($donneesSaisie->getBanqueCompte()) {
                    $banqueComptes = [$this->getDoctrine()
                        ->getRepository('AppBundle:BanqueCompte')
                        ->find($donneesSaisie->getBanqueCompte())];
                }
            }
        }


        return $this->render('ConsultationPieceBundle:Default:dataImage.html.twig', array(
            'img' => $image,
            'etape' => $etape,
            'saisie' => $donneesSaisie,
            'tvaSaisie' => $tvaSaisie,
            'dossier' => $dossier,
            'embed' => $embed,
            'height' => $height,
            'separation' => $infoSep,
            'dateEcheance' => $dateEcheance,
            'modeReglements' => $modeReglements,
            'modeReglementSaisie' => $modeReglement,
            'categorie' => $categorie,
            'souscategorie' => $souscategorie,
            'soussouscategorie' => $soussouscategorie,
            'categories' => $categories,
            'souscategories' => $souscategories,
            'soussouscategories' => $soussouscategories,
            'cegjSaisie' => $cegjSaisie,
            'etapeId' => $etapeId,
            'isAdmin' => $isAdmin,
            'isSuperAdmin' => $isSuperAdmin,
            'reglePaiementDossier' => $reglePaiementDossier,
            'reglePaiementTiers' => $reglePaiementTier,
            'tiers' => $tiers,
            'releves' => $releves,
            'montantAvoir' => $montantAvoir,
            'resteAPayer' => $reste,
            'avoirs' => $avoirSaisies,
            'listeTiersFinal' => $ecrTtc,
            'listeResFinal' => $ecrHt,
            'listeTvaFinal' => $ecrTva,
            'utilisateur' => $utilisateur,
            'banqueComptes' =>  $banqueComptes,
            'banques' => $banques,
            'typeEcriture' => $typeEcriture,
            'ecritures' => $ecritures
        ));
    }

    public function getUrl($imageid){
        $url = '';

        /** @var Image $image */
        $image = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Image')
            ->find($imageid);

        if($image){
            /** @var Dossier $dossier */
            $dossier = $image->getLot()->getDossier();

            $url = 'http://192.168.0.9/Images%20comptabilis%C3%A9es/'.
                $dossier->getSite()->getClient()->getNom().'/'.
                $dossier->getNom().'/'.
                $image->getExercice().'/'.
                $image->getNom().'.pdf';

            $file_headers = get_headers($url);

            if ($file_headers[0] == 'HTTP/1.1 200 OK') {
                return $url;
            } else {

                $lot = $image->getLot();

                $url = 'http://192.168.0.9/intranet%20images/IMAGES_A_TRAITER/'.
                    $dossier->getSite()->getClient()->getNom().'/'.
                    $dossier->getNom().'/'.
                    $image->getExercice().'/'.
                    $lot->getDateScan()->format('Y-m-d').'/'.
                    $lot->getLot().'/'.
                    $image->getNom().'.pdf';

                $file_headers = get_headers($url);

                if ($file_headers[0] == 'HTTP/1.1 200 OK') {
                    return $url;
                }
                else {
                    $dateScanFomated = $image->getLot()->getDateScan()->format('Ymd');

                    $url = 'http://192.168.0.9/intranet%20images/' . $dateScanFomated . '/' . $image->getNom() . '.pdf';
                    $file_headers = get_headers($url);
                    if ($file_headers[0] == 'HTTP/1.1 200 OK') {
                        return $url;
                    } else {

                        $url = 'http://192.168.0.5/IMAGES/'.$dateScanFomated.'/'.$image->getNom().'.pdf';
                        $file_headers = get_headers($url);
                        if($file_headers[0] == 'HTTP/1.1 200 OK'){
                            return $url;
                        }else {
                            $url = 'https://www.lesexperts.biz/IMAGES/' . $dateScanFomated . '/' . $image->getNom() . '.pdf';
                        }
                    }
                }
            }
        }
        return $url;
    }


    public function echeanceCalculAction(Request $request){
        if($request->isXmlHttpRequest()){

            $imageId = $request->request->get('imageId');

            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find(Boost::deboost($imageId, $this));

            $dossier = $image->getLot()->getDossier();

            $infosImage = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->getInfosImageByImageId($image->getId());

            $codeCategorie = '';

            if(null !== $image) {
                $separations = $this->getDoctrine()
                    ->getRepository('AppBundle:Separation')
                    ->findBy(array('image' => $image));

                if (count($separations) > 0) {
                    /** @var Separation $separation */
                    $separation = $separations[0];
                    $codeCategorie = $separation->getCategorie()->getCode();
                }
            }

            $tiers = null;
            $saisie = null;

            if($infosImage !== null){
                $saisies = $infosImage['saisie'];
                $tvas =$infosImage['tva'];

                if(count($tvas) > 0 && ($image->getImputation() > 1 || $image->getCtrlImputation() > 1)) {
                    /** @var TvaImputation $tva */
                    foreach ($tvas as $tva) {
                        if ($tva->getTiers() !== null) {
                            $tiers = $tva->getTiers();
                        }
                    }
                }

                if(count($saisies)> 0){
                    /** @var Saisie1 $saisie */
                    $saisie = $saisies[0];
                }
            }

            if($saisie != null) {
                if($saisie->getDateEcheance() === null) {

                    $dateEcheance = $this->calculDateEcheance($dossier, $tiers, $saisie->getDateFacture(), $saisie->getDateLivraison(), $codeCategorie );

                    $res = array();
                    if($dateEcheance !== null){

                        $res = explode('-', $dateEcheance);

                    }


                    return new JsonResponse($res);
                }
            }

            return new Response('');
        }
        return new AccessDeniedException('Accès refusé');
    }


    public function echeanceSaveAction(Request $request){
        if($request->isXmlHttpRequest()){

            $post = $request->request;

            $imageId = $post->get('imageId');
            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find(Boost::deboost($imageId, $this));

            $echeance = $post->get('echeance');
            $newEcheance = null;

            if ($echeance !== '') {

                $date_array = explode("/", $echeance);
                if(count($date_array) === 3) {
                    $var_day = $date_array[0];
                    $var_month = $date_array[1];
                    $var_year = $date_array[2];
                    $newEcheance =  new \DateTime("$var_year-$var_month-$var_day");
                }
            }

            $return = array();

            if(null !== $image){
                $saisies = null;
                $tvas = null;
                if($image->getCtrlImputation() > 1) {

                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:ImputationControle')
                        ->findBy(array('image' => $image));
                }
                elseif($image->getImputation() > 1){

                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Imputation')
                        ->findBy(array('image' => $image));
                }
                elseif($image->getCtrlSaisie() > 1){

                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:SaisieControle')
                        ->findBy(array('image' => $image));
                }
                elseif($image->getSaisie2() > 1){

                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Saisie2')
                        ->findBy(array('image' => $image));
                }
                elseif($image->getSaisie1() > 1) {
                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Saisie1')
                        ->findBy(array('image' => $image));
                }


                if(count($saisies) > 0){
                    $em = $this->getDoctrine()
                        ->getEntityManager();

                    $saisie = $saisies[0];
                    $saisie->setDateEcheance($newEcheance);

                    $em->flush();
                    $return = array('type' => 'success', 'message' => 'Enregistrement effectué');

                }
                else{
                    $return = array('type' => 'warning','message' => 'Pas de données');
                }



            }
            else{
                $return = array('type' => 'warning', 'message' => 'Image introuvable');
            }

            return new JsonResponse($return);

        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    public function banqueCompteAction(Request $request){
        $imageid = $request->query->get('imageId');
        $banqueid = $request->query->get('banqueId');

        /** @var Image $image */
        $image = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->find($imageid);

        $banque = $this->getDoctrine()
            ->getRepository('AppBundle:Banque')
            ->find($banqueid);

        $dossier = null;
        /** @var BanqueCompte[] $banqueComptes */
        $banqueComptes = [];
        if($image){
            /** @var Dossier $dossier */
            $dossier = $image->getLot()->getDossier();

            $banqueComptes = $this->getDoctrine()
                ->getRepository('AppBundle:BanqueCompte')
                ->findBy(array('banque' => $banque, 'dossier' => $dossier));

        }

        return $this->render('ConsultationPieceBundle:Default:banqueCompte.html.twig',
            array('banqueComptes' => $banqueComptes));
    }

    public function dataBanqueSaveAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $post = $request->request;

            $imageid = $post->get('imageId');
            /** @var Image $image */
            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find($imageid);

            $dossier = $image->getLot()->getDossier();

            $periodeDu = $post->get('periodeDu');
            if ($periodeDu != '')
                $periodeDu = \DateTime::createFromFormat('d/m/Y', $periodeDu);
            else
                $periodeDu = null;

            $periodeAu = $post->get('periodeAu');
            if ($periodeAu != '')
                $periodeAu = \DateTime::createFromFormat('d/m/Y', $periodeAu);
            else
                $periodeAu = null;

            $numReleve = $post->get('numReleve');
            $page = $post->get('page');

            $banqueCompteid = $post->get('banqueCompte');
            $banqueCompte = $this->getDoctrine()
                ->getRepository('AppBundle:BanqueCompte')
                ->find($banqueCompteid);

            $exercice = $post->get('exercice');

            $souscategorieid = $post->get('souscategorie');
            $souscategorie = $this->getDoctrine()
                ->getRepository('AppBundle:Souscategorie')
                ->find($souscategorieid);

            $soussouscategorieid = $post->get('soussouscategorie');
            $soussouscategorie = $this->getDoctrine()
                ->getRepository('AppBundle:Soussouscategorie')
                ->find($soussouscategorieid);

            $soldeInit = str_replace(' ','',$post->get('soldeInitial'));
            if ($soldeInit == '')
                $soldeInit = 0;
            $soldeFinal = str_replace(' ','',$post->get('soldeFinal'));
            if ($soldeFinal == '')
                $soldeFinal = 0;

            if ($image) {

                $withsouscategorie= false;

                if ($image->getExercice() !== $exercice)
                    $image->setExercice($exercice);

                if ($image->getCtrlImputation() > 1) {
                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:ImputationControle')
                        ->findBy(array('image' => $image));

                    $withsouscategorie = true;
                }
                else if ($image->getImputation() > 1) {
                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Imputation')
                        ->findBy(array('image' => $image));

                    $withsouscategorie = true;
                }
                else if ($image->getCtrlSaisie() > 1)
                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:SaisieControle')
                        ->findBy(array('image' => $image));
                else if ($image->getSaisie2() > 1)
                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Saisie2')
                        ->findBy(array('image' => $image));
                else if ($image->getSaisie1() > 1)
                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Saisie1')
                        ->findBy(array('image' => $image));
                else
                    return new JsonResponse(['type' => 'error', 'action' => 'update', 'message' => 'Image en cours']);


                if (count($saisies) > 0) {

                    $em = $this->getDoctrine()
                        ->getManager();

                    /** @var Imputation $saisie */
                    $saisie = $saisies[0];

                    $saisie->setPeriodeD1($periodeDu);
                    $saisie->setPeriodeF1($periodeAu);
                    $saisie->setBanqueCompte($banqueCompte);
                    $saisie->setSoldeDebut($soldeInit);
                    $saisie->setSoldeFin($soldeFinal);
                    $saisie->setPage($page);
                    $saisie->setSoussouscategorie($soussouscategorie);

                    if($withsouscategorie){
                        $saisie->setSouscategorie($souscategorie);
                    }

                    $em->flush();


                    $separations = $this->getDoctrine()
                        ->getRepository('AppBundle:Separation')
                        ->findBy(array('image' => $image));

                    if(count($separations) > 0){
                        $separation = $separations[0];
                        $separation->setSoussouscategorie($soussouscategorie);
                        $separation->setSouscategorie($souscategorie);

                        $em->flush();
                    }

                    $id = $dossier->getId().'catCODE_BANQUE';

                    if($souscategorie){
                        $id .= 'sCat'.$souscategorie->getId();
                    }

                    return new JsonResponse(['type' => 'success', 'action' => 'update', 'message' => 'Mise à jour effectuée', 'id' => $id]);
                }
            } else
                return new JsonResponse(['type' => 'error', 'action' => 'update', 'message' => 'Image introuvable']);

            return new JsonResponse(['type' => 'success', 'action' => 'update', 'message' => 'Sauvegarde effectuée']);
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }

    public function dataSaveAction(Request $request){
        if($request->isXmlHttpRequest()){

            $post = $request->request;

//            $imageId = Boost::deboost($request->get('imageId'), $this);

            $imageId = $post->get('imageId');
            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find($imageId);

            $modeReglementId = Boost::deboost($post->get('modeReglement'), $this);

            $modeReglement = $this->getDoctrine()
                ->getRepository('AppBundle:ModeReglement')
                ->find($modeReglementId);

            $periodeDu = $post->get('periodeDu');
            $newPeriodeDu = null;

            if ($periodeDu !== '') {

                $date_array = explode("/", $periodeDu);
                if(count($date_array) === 3) {
                    $var_day = $date_array[0];
                    $var_month = $date_array[1];
                    $var_year = $date_array[2];
                    $newPeriodeDu =  new \DateTime("$var_year-$var_month-$var_day");
                }
            }

            $periodeAu = $post->get('periodeAu');
            $newPeriodeAu = null;
            if($periodeAu !== ''){
                $date_array = explode('/', $periodeAu);
                if(count($date_array) === 3){
                    $var_day = $date_array[0];
                    $var_month = $date_array[1];
                    $var_year = $date_array[2];
                    $newPeriodeAu =  new \DateTime("$var_year-$var_month-$var_day");
                }
            }

            $dateLivraison = $post->get('dateLivraison');
            $newDateLivraison = null;

            if($dateLivraison !== ''){
                $date_array = explode('/', $dateLivraison);
                if(count($date_array) === 3){
                    $var_day = $date_array[0];
                    $var_month = $date_array[1];
                    $var_year = $date_array[2];
                    $newDateLivraison =   new \DateTime("$var_year-$var_month-$var_day");
                }
            }

            $dateReglement = $post->get('dateReglement');
            $newDateReglement = null;
            if($dateReglement !== ''){
                $date_array = explode('/', $dateReglement);
                if(count($date_array) === 3){
                    $var_day = $date_array[0];
                    $var_month = $date_array[1];
                    $var_year = $date_array[2];
                    $newDateReglement = new \DateTime("$var_year-$var_month-$var_day");
                }
            }

            $numReglement = $post->get('numReglement');
            if($numReglement === ''){
                $numReglement = null;
            }


            $return = array();


             if(null !== $image){
                $saisies = null;
                $tvas = null;
                if($image->getCtrlImputation() > 1) {

                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:ImputationControle')
                        ->findBy(array('image' => $image));

                    $tvas = $this->getDoctrine()
                        ->getRepository('AppBundle:TvaImputationControle')
                        ->findBy(array('image' => $image));
                }
                elseif($image->getImputation() > 1){

                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Imputation')
                        ->findBy(array('image' => $image));

                    $tvas = $this->getDoctrine()
                        ->getRepository('AppBundle:TvaImputation')
                        ->findBy(array('image' => $image));
                }
                elseif($image->getCtrlSaisie() > 1){

                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:SaisieControle')
                        ->findBy(array('image' => $image));

                    $tvas = $this->getDoctrine()
                        ->getRepository('AppBundle:TvaSaisieControle')
                        ->findBy(array('image' => $image));
                }
                elseif($image->getSaisie2() > 1){

                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Saisie2')
                        ->findBy(array('image' => $image));

                    $tvas = $this->getDoctrine()
                        ->getRepository('AppBundle:TvaSaisie2')
                        ->findBy(array('image' => $image));
                }
                elseif($image->getSaisie1() > 1){

                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Saisie1')
                        ->findBy(array('image' => $image));

                    $tvas = $this->getDoctrine()
                        ->getRepository('AppBundle:TvaSaisie1')
                        ->findBy(array('image' => $image));
                }


                if(count($saisies) > 0){

                    $em = $this->getDoctrine()
                        ->getManager();

                    $saisie = $saisies[0];
                    $saisie->setModeReglement($modeReglement);
                    $saisie->setNumPaiement($numReglement);
                    $saisie->setDateReglement($newDateReglement);

                    $typeAchatVente = null;
                    if(null !== $saisie->getTypeAchatVente()){
                        $typeAchatVente = $saisie->getTypeAchatVente()
                            ->getId();
                    }

                    if($typeAchatVente === 2){
                        $saisie->setPeriodeD1($newPeriodeDu);
                        $saisie->setPeriodeF1($newPeriodeAu);

                        foreach ($tvas as $tva){
                            $tva->setPeriodeDeb($newPeriodeDu);
                            $tva->setPeriodeFin($newPeriodeAu);
                        }
                    }

                    elseif($typeAchatVente === 1){
                        foreach ($tvas as $tva){
                            $tva->setDateLivraison($newDateLivraison);
                        }
                    }

                    $em->flush();

                    $return = array('type' => 'success', 'message' => 'Enregistrement effectué');

                }
                else{
                    $return = array('type' => 'warning','message' => 'Pas de données');
                }



            }
            else{
                $return = array('type' => 'warning', 'message' => 'Image introuvable');
            }

            return new JsonResponse($return);

        }

        throw new AccessDeniedHttpException('Accès refusé');
    }


    function deleteDir($path)
    {
        if (is_dir($path) === true)
        {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $file)
            {
                if (in_array($file->getBasename(), array('.', '..')) !== true)
                {
                    if ($file->isDir() === true)
                    {
                        rmdir($file->getPathName());
                    }

                    else if (($file->isFile() === true) || ($file->isLink() === true))
                    {
                        unlink($file->getPathname());
                    }
                }
            }

            return rmdir($path);
        }

        if ((is_file($path) === true) || (is_link($path) === true))
        {
            return unlink($path);
        }

        return false;
    }


    /** $listeImages  array*/
    public function download($clientId, $listeImages){

        try{
//LOCAL
//        $srcDir = $_SERVER['DOCUMENT_ROOT'].'/picdata/WEB/IMAGES/'.$clientId;
//        $dstDir = $_SERVER['DOCUMENT_ROOT'].'/picdata/WEB/CPDOWNLOAD/'.$clientId;

//192.168.0.5
//        $srcDir = $_SERVER['DOCUMENT_ROOT'].'/newpicdata/web/IMAGES/'.$clientId;
//        $dstDir = $_SERVER['DOCUMENT_ROOT'].'/newpicdata/web/CPDOWNLOAD/'.$clientId;


//PICDATA
//            $srcDir = $_SERVER['DOCUMENT_ROOT'].'/IMAGES/'.$clientId;

            $srcDir = $_SERVER['DOCUMENT_ROOT'].'/IMAGES';

            $dstDir = $_SERVER['DOCUMENT_ROOT'].'/CPDOWNLOAD/'.$clientId;

        $this->deleteDir($dstDir);

        if (!file_exists($dstDir)) {
            if (!mkdir($dstDir, 0777, true) && !is_dir($dstDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dstDir));
            }
        }


        foreach ($listeImages as $image){

            $nomImage = $image['nom'];
            $extImage = $image['extension'];
            $dsImage = $image['dateScan'];

            $srcFile = $srcDir.'/'.$dsImage.'/'.$nomImage.'.'.$extImage;
            $dstFile = $dstDir.'/'.$nomImage.'.'.$extImage;


            if(file_exists($srcFile)) {
                try {

                    copy($srcFile, $dstFile);
                }
                catch (\Exception $e) {
                    echo('copy local error');
                }
            }

            else {

                $srcFile = $srcDir.'/'.$nomImage.'.'.$extImage;
                $dstFile = $dstDir.'/'.$nomImage.'.'.$extImage;

                //Raha miexiste en local
                if (file_exists($srcFile)) {

                    try {

                        copy($srcFile, $dstFile);
                    } catch (\Exception $e) {
                        echo('copy local error');
                    }
                } else {

                    $imgs = $this->getDoctrine()
                        ->getRepository('AppBundle:Image')
                        ->findBy(array('nom' => $nomImage));

                    if (count($imgs) > 0) {
                        /** @var  $img Image */
                        $img = $imgs[0];

                        $srcOvh = 'http://picdata.fr/picdataovh/' . 'images/' .
                            $img->getLot()->getDossier()->getSite()->getClient()->getNom() . '/' .
                            $img->getLot()->getDossier()->getNom() . '/' .
                            $img->getExercice() . '/' .
                            $img->getLot()->getDateScan()->format('Y-m-d') . '/' .
                            $img->getLot()->getLot() . '/' .
                            $img->getNom() . '.' . $img->getExtImage();

                        try {

                            copy($srcOvh, $dstFile);
                        } catch (\Exception $e) {

                        }
                    }

                }
            }

        }

        $zipFile = $dstDir.'/images.zip';

//        $rootPath = realpath($dstDir);

//        $zip = new \ZipArchive();
//        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
//
//        /** @var SplFileInfo[] $files */
//        $files = new \RecursiveIteratorIterator(
//            new \RecursiveDirectoryIterator($rootPath),
//            \RecursiveIteratorIterator::LEAVES_ONLY
//        );
//
//        foreach ($files as $name => $file)
//        {
//            if (!$file->isDir())
//            {
//                $filePath = $file->getRealPath();
//                $relativePath = substr($filePath, strlen($rootPath) + 1);
//
//                $zip->addFile($filePath, $relativePath);
//            }
//        }
//
//        $zip->close();


            exec("zip -r -j $zipFile $dstDir");

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($zipFile));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($zipFile));
            readfile($zipFile);

            $this->deleteDir($dstDir);

        }
        catch (\Exception $e){
            exit();
        }
    }


    public function reglePaiementAction(Request $request){
        if($request->isXmlHttpRequest()){

            $post = $request->request;

            $imageId = Boost::deboost($post->get('imageId'),$this);

            if (is_bool($imageId)) return new Response('security');

            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find($imageId);

            $dossier = $image->getLot()->getDossier();

            $separations = $this->getDoctrine()
                ->getRepository('AppBundle:Separation')
                ->findBy(array('image'=>$image));


            $tiers = null;

            if($image->getCtrlImputation() > 1){
                $tvas = $this->getDoctrine()
                    ->getRepository('AppBundle:TvaImputationControle')
                    ->findBy(array('image'=>$image));

                if(count($tvas) > 0){
                    foreach ($tvas as $tva){
                        if($tva->getTiers() != null){
                            $tiers = $tva->getTiers();
                            break;
                        }
                    }
                }
            }

            else if($image->getImputation() > 1){
                $tvas = $this->getDoctrine()
                    ->getRepository('AppBundle:TvaImputation')
                    ->findBy(array('image'=>$image));

                if(count($tvas) > 0){
                    foreach ($tvas as $tva){
                        if($tva->getTiers() != null){
                            $tiers = $tva->getTiers();
                            break;
                        }
                    }
                }
            }

            $codecategorie = '';

            if(count($separations)>0){
                /** @var  $cat Categorie*/
                $cat = $separations[0]->getCategorie();
                if($cat !=null){
                    $codecategorie = $cat->getCode();
                }
            }

            $reglePaiementDossier = null;

            if($codecategorie =='CODE_FRNS' || $codecategorie == 'CODE_CLIENT'){

                if($codecategorie == 'CODE_FRNS'){
                    $typeTiers = 0;
                }
                else{
                    $typeTiers = 1;
                }

                $reglePaiementDossiers = $this->getDoctrine()
                    ->getRepository('AppBundle:ReglePaiementDossier')
                    ->findBy(array('dossier'=>$dossier, 'typeTiers'=>$typeTiers));



                if(count($reglePaiementDossiers) > 0){
                    $reglePaiementDossier = $reglePaiementDossiers[0];
                }
            }


            /** @var  $reglePaiementTier ReglePaiementTiers*/
            $reglePaiementTier = null;
            if($tiers != null){
                $reglePaiementTiers = $this->getDoctrine()
                    ->getRepository('AppBundle:ReglePaiementTiers')
                    ->findBy(array('dossier'=>$dossier, 'tiers'=>$tiers));

                if(count($reglePaiementTiers) > 0){
                    $reglePaiementTier = $reglePaiementTiers[0];
                }
            }


            return $this->render('ConsultationPieceBundle:Default:reglePaiementConsultation.html.twig',
                array('reglePaiementDossier'=>$reglePaiementDossier,
                    'reglePaiementTiers'=>$reglePaiementTier,
                    'tiers'=>$tiers,
                    'image'=>$image));

        }
        else{
            throw  new AccessDeniedHttpException();
        }
    }





    public function testAction()
    {
        $dernierMois = new \DateTime();
        $x = $dernierMois->format( 'Y-m-t' );

//        $myDate = $x->modify('+ 45 days');


        $myDate =  date('Y-m-d', strtotime($x. ' + 1 days'));
        $nDays = 45;



        if(date('N', strtotime($myDate)) == 6){
            $myDate = date('Y-m-d', strtotime($myDate. '+ 2 days'));
        }
        elseif( date('N', strtotime($myDate)) == 7) {
            $myDate = date('Y-m-d', strtotime($myDate. '+ 1 day'));
        }
        else{
            $myDate = date('Y-m-d', strtotime($myDate. '+ 0 day'));
        }


        return new JsonResponse($myDate);

    }

    /**
     * Initialisation tableau par categorie
     * @param $images
     * @param $imageSeparations
     * @param $categorieId
     * @return array
     */
    public function initializeGrid($images, $imageSeparations, $categorieId)
    {
        $rows = array();
        $listeImagesDownload = array();

//        $images = array();

//        $images[] = $this->getDoctrine()
//            ->getRepository('AppBundle:Image')
//            ->find(387613);

        /** @var  $categorie Categorie*/
        $categorie = $this->getDoctrine()
            ->getRepository('AppBundle:Categorie')
            ->find($categorieId);

        if(null !== $categorie){
            $categorieCode = $categorie->getCode();
        }else{
            $categorieCode = $categorieId;
        }

        if ($images != null) {
            foreach ($images as $image) {
                /**@var $image Image */


                $results = $this->getDoctrine()
                    ->getRepository('AppBundle:Image')
                    ->getInfosImageByImageId($image->getId());

                /**@var $res0 Imputation */
                if ($results !== null) {

                    $res0 = $results['saisie'][0];
                    $tableSaisie = $results['tableSaisie'];

                    //Note de frais
                    $resNdf = $results['ndf'];
                    $resSeparation = $results['separation'];

                    //Releve
                    $resReleve = $results['releve'];

                    switch ($tableSaisie) {
                        case 'Saisie 1':
                            $tableSaisie = 'Saisie';
                            break;
                        case 'Saisie 2':
                            $tableSaisie = 'Saisie';
                            break;
                        case 'Controle Saisie':
                            $tableSaisie = 'Saisie';
                            break;
                        case 'Imputation':
                            $tableSaisie = 'Imputée';
                            break;
                        case 'Controle Imputation':
                            $tableSaisie = 'Imputée';
                            break;
                    }

                    if($categorieCode === 'CODE_ILLISIBLE'){
                        $tableSaisie = 'Reçue';
                    }

                    $listeSousCategorie = array();
                    $listeSsCategorie = array();


                    $sommeHt = 0;
                    $sommeTva = 0;
                    $resTva = $results['tva'];


                    for ($i = 0, $iMax = count($resTva); $i < $iMax; $i++) {
                        /**@var $tva TvaSaisie1 */
                        $tva = $resTva[$i];
                        $sommeHt += $tva->getMontantHt();
                        if ($tva->getTvaTaux() != null) {
                            $sommeTva += ($tva->getMontantHt()) * ($tva->getTvaTaux()->getTaux()) / 100;
                        }

                        if ($res0->getSoussouscategorie() != null) {
                            if ($tableSaisie === 'Imputée') {
                                if ($tva->getSoussouscategorie() != null) {
                                    if ($tva->getSoussouscategorie()->getSouscategorie() != null) {
                                        if (!in_array($tva->getSoussouscategorie()->getLibelleNew(), $listeSsCategorie, true)) {
                                            $listeSsCategorie[] = $tva->getSoussouscategorie()->getLibelleNew();
                                        }

                                        if (!in_array($tva->getSoussouscategorie()->getSouscategorie()->getLibelleNew(), $listeSousCategorie, true)) {
                                            $listeSousCategorie[] = $tva->getSoussouscategorie()->getSouscategorie()->getLibelleNew();
                                        }
                                    }
                                }
                            }
                        }
                        else if($tableSaisie === 'Imputée'){
                            if($res0->getSouscategorie() != null){
                                if(!in_array($res0->getSouscategorie()->getLibelleNew(), $listeSousCategorie, true)) {
                                    $listeSousCategorie[] = $res0->getSouscategorie()->getLibelleNew();
                                }
                            }
                        }

                    }

                    $typeEcriture = 0;
                    $ecritures = [];

                    $categorieLib = '';
                    $sousCategorieLib = '';
                    $ssCategorieLib = '';

                    $trouveCategorie = false;

                    if($resSeparation !== null) {
                        if($resSeparation->getCategorie() !== null){
                            if($resSeparation->getCategorie()->getCode() === 'CODE_FRNS' ||
                                $resSeparation->getCategorie()->getCode() === 'CODE_CLIENT' ||
                                $resSeparation->getCategorie()->getCode() === 'CODE_NDF'
                            ){
                                $ecritures = $this->getDoctrine()
                                    ->getRepository('AppBundle:Ecriture')
                                    ->getEcrituresByImage($image, $typeEcriture);
                            }

                            $categorieLib = $resSeparation->getCategorie()
                                ->getLibelleNew();

                        }

                        if($resSeparation->getSouscategorie() !== null) {
                            $sousCategorieLib = $resSeparation->getSouscategorie()
                                ->getLibelleNew();

                            $trouveCategorie = true;
                        }

                        if($resSeparation->getSoussouscategorie() !== null){
                            $ssCategorieLib = $resSeparation->getSoussouscategorie()
                                ->getLibelleNew();


                        }
                    }



                   if(!$trouveCategorie) {


                       if ($tableSaisie === 'Imputée') {
                           if ($listeSousCategorie != null) {

                               $trouveCategorie = true;

                               foreach ($listeSousCategorie as $sousCategorie) {
                                   if ($sousCategorieLib == '') {
                                       $sousCategorieLib = $sousCategorie;
                                   } else {
                                       $sousCategorieLib = $sousCategorieLib . ', ' . $sousCategorie;
                                   }
                               }
                           }
                           if ($listeSsCategorie != null) {

                               $trouveCategorie = true;

                               foreach ($listeSsCategorie as $ssCategorie) {

                                   if ($ssCategorieLib == '') {
                                       $ssCategorieLib = $ssCategorie;
                                   } else {
                                       $ssCategorieLib = $ssCategorieLib . ', ' . $ssCategorie;
                                   }
                               }
                           }
                       }

                       if ($res0->getSoussouscategorie() !== null) {
                           if ($tableSaisie !== 'Imputée') {
                               $sousCategorieLib = $res0->getSoussouscategorie()->getSouscategorie()->getLibelleNew();
                               $ssCategorieLib = $res0->getSoussouscategorie()->getLibelleNew();
                           } else if ($trouveCategorie === false) {
                               $sousCategorieLib = $res0->getSoussouscategorie()->getSouscategorie()->getLibelleNew();
                               $ssCategorieLib = $res0->getSoussouscategorie()->getLibelleNew();
                           }
                           $categorieLib = $res0->getSoussouscategorie()->getSouscategorie()->getCategorie()->getLibelleNew();
                       } else {

                           $trouveCategorieLib = false;

                           if ($tableSaisie === 'Imputée') {
                               if ($res0->getSouscategorie() != null) {
                                   $categorieLib = $res0->getSouscategorie()->getCategorie()->getLibelleNew();
                                   $trouveCategorieLib = true;
                               }
                           }

                           //AFFICHER-NA FOTSINY ILAY CATEGORIE SELECTIONNE
                           if (null !== $categorie && $trouveCategorieLib == false) {
                               $categorieLib = $categorie->getLibelleNew();
                           } else {
                               $trouveCategorieImputation = false;
                               //JERENA NY SOUSCATEGORIE RAHA IMPUTATION
                               if ($tableSaisie === 'Imputée') {
                                   if ($res0->getSouscategorie() !== null) {
//                                    $categorieLib = $res0->getSouscategorie()->getCategorie()->getLibelle();
                                       $trouveCategorieImputation = true;
                                   }

                               }
                               //JERENA NY ANY @ SEPARATION
                               if (!$trouveCategorieImputation) {
                                   if ($resSeparation !== null) {
                                       /** @var $resSeparation Separation */
                                       if ($resSeparation->getCategorie() != null) {
                                           $categorieLib = $resSeparation->getCategorie()->getLibelleNew();
                                       }
                                   }
                               }
                           }
                       }
                   }

//                        tsy afficher-na ny doublon sy ny mal affectée
                    $posScat = strpos(strtolower($sousCategorieLib), 'doublon');
                    $posMalScat = strpos(strtolower($sousCategorieLib), 'mal aff');
                    $posSscat = strpos(strtolower($ssCategorieLib), 'doublon');
                    $posMalSscat = strpos(strtolower($ssCategorieLib), 'mal aff');
                    if ($posScat !== false || $posSscat !== false || $posMalScat !== false || $posMalSscat !== false) {
                        continue;
                    }

                    //Tsy afficher-na izay catégorie tsy izy
                    if(null !== $categorie){
                        if($categorie->getLibelleNew() != $categorieLib){
                            continue;
                        }
                    }


                    //Tsy affiche-na raha tsy misy catégorie
                    if($res0->getSoussouscategorie() == null){
                        if($tableSaisie === 'Imputée'){
                            if($res0->getSouscategorie() == null && $categorieLib == ''){
                                continue;
                            }
                        }
                    }

                    $dateFacture = '';
                    $dateScan = '';
                    $dateEcheance = '';

                    $dateDebutBanque = '';
                    $dateFinBanque = '';

                    if ($res0->getDateFacture() != null) {
                        $dateFacture = $res0->getDateFacture()->format('Y-m-d');
                    }

                    if ($res0->getImage()->getLot()->getDateScan() != null) {
                        $dateScan = $res0->getImage()->getLot()->getDateScan()->format('Y-m-d');
                    }



                    if ($res0->getPeriodeD1() != null) {
                        $dateDebutBanque = $res0->getPeriodeD1()->format('Y-m-d');
                    }

                    if ($res0->getPeriodeF1() != null) {
                        $dateFinBanque = $res0->getPeriodeF1()->format('Y-m-d');
                    }

                    $chrono = '';
                    if ($res0->getChrono() != null) {
                        $chrono = $res0->getChrono();
                    }

                    $tiers = null;

                    $compteRes = '';
                    $tiersIntitule = '';
                    $tiersCompte = '';


                    if ($results['tableSaisie'] === 'Imputation' || $results['tableSaisie'] === 'Controle Imputation') {
                        if(count($ecritures) == 0) {
                            if ($resTva != null) {
                                foreach ($resTva as $tvaImputation) {
                                    /**@var $tvaImputation TvaImputation */
                                    if ($tvaImputation->getTiers() != null) {
//                                    $tiers = $tvaImputation->getTiers()->getCompteStr();
                                        $tiers = $tvaImputation->getTiers();
                                        $tiersIntitule = $tvaImputation->getTiers()->getIntitule();
                                        $tiersCompte = $tvaImputation->getTiers()->getCompteStr();
                                    }

                                    if ($tvaImputation->getPcc() != null) {
                                        $compteRes = $tvaImputation->getPcc()->getCompte();
                                    }
                                }
                            }
                        }
                        else{
                            $sommeHt = 0;
                            $sommeTva = 0;

                            foreach ($ecritures as $ecriture){
                                if($ecriture->getTiers() !== null){
                                    $tiers = $ecriture->getTiers();
                                    $tiersIntitule = $tiers->getIntitule();
                                    $tiersCompte = $tiers->getCompteStr();
                                }
                                elseif($ecriture->getPcc() !== null){
                                    if(strpos($ecriture->getPcc()->getCompte(), '445') === false) {
                                        $compteRes = $ecriture->getPcc()->getCompte();
                                    }

                                    $imageCategorieCode = '';
                                    if($resSeparation !== null){
                                        $imageCategorieCode = $resSeparation->getCategorie()
                                            ->getCode();
                                    }

                                    if(strpos($ecriture->getPcc()->getCompte(),'445')=== false){
                                        if($imageCategorieCode == 'CODE_FRNS' || $imageCategorieCode == 'CODE_NDF') {
                                            $sommeHt += abs($ecriture->getDebit());
                                        }
                                        else if($imageCategorieCode == 'CODE_CLIENT'){
                                            $sommeHt += abs($ecriture->getCredit());
                                        }
                                    }
                                    else{
                                        $sommeTva +=abs($ecriture->getDebit()-$ecriture->getCredit());
                                    }
                                }
                            }
                        }
                    }





                    if ($res0->getDateEcheance() != null) {
                        $dateEcheance = $res0->getDateEcheance()->format('Y-m-d');
                    }

                    else {

                        if ($categorieCode === 'CODE_FRNS' || $categorieCode === 'CODE_CLIENT')
                            $dateEcheance = $this->calculDateEcheance($image->getLot()->getDossier(), $tiers, $res0->getDateFacture(), $res0->getDateLivraison(), $categorieCode);

                    }

                    $sommeNdfTtc = 0;
                    $sommeNdfTva = 0;
                    $sommeNdfHt = 0;
                    $tiersNdf = '';
                    $dateNdf = '';


                    if ($res0->getDateFacture() != null) {
                        $dateFacture = $res0->getDateFacture()->format('Y-m-d');
                    }

                    if ($resNdf != null) {

                        /** @var  $ndf Saisie1NoteFrais */
                        foreach ($resNdf as $ndf) {
                            $tvaTaux = 0;
                            if ($ndf->getTypeFrais() != null) {
                                $tvaTaux = $ndf->getTypeFrais()->getTvaTaux()->getTaux();
                            }
                            $sommeNdfTtc += $ndf->getTtc();
                            $sommeNdfHt += (100 * $ndf->getTtc()) / (100 + $tvaTaux);
                            $sommeNdfTva += ($sommeNdfTtc - $sommeNdfHt);

                            if ($ndf->getDate() != null) {
                                $dateNdf = $ndf->getDate()->format('Y-m-d');
                            }
                            if ($ndf->getProfitDe() != '' && $ndf->getProfitDe() != null) {
                                $tiersNdf = $ndf->getProfitDe();
                            }
                        }
                    }

                    $sommeNdfTtc = ($sommeNdfTtc === 0) ? $sommeHt+$sommeTva : $sommeNdfTtc;
                    $sommeNdfHt = ($sommeNdfHt === 0) ? $sommeHt : $sommeNdfHt;
                    $sommeNdfTva = ($sommeNdfTva === 0) ? $sommeTva : $sommeNdfTva;

                    $dateNdf = ($dateNdf === '') ? $dateFacture : $dateNdf;

                    $banque = '';
                    $compteBanque = '';
                    if ($res0 != null) {

                        if ($res0->getBanqueCompte() != null) {
                            $compteBanque = $res0->getBanqueCompte()->getNumcompte();
                            $banque = $res0->getBanqueCompte()->getBanque()->getNom();
                        } else {
                            $banque = $res0->getRs();
                        }
                    }



//                    switch ($typeEcriture){
//                        case 1:
//                            $icon = '<i class="fa fa-book"></i>';
//                            break;
//                        case 0:
//                            $icon = '<i class="fa fa-pencil"></i>';
//                            break;
//                        case -1:
//                            $icon = '<i class="fa fa-ban"></i>';
//                            break;
//                        default:
//                            $icon = '<i class="fa fa-file-text"></i>';
//                            break;
//                    }

                    $icon = '<i class="fa fa-file-text"></i>';

                    switch ($categorieCode) {

                        //Client, fournisseur
                        case 'CODE_CLIENT':
                        case 'CODE_FRNS':
                            $rows[] = array(
                                'id' => $res0->getImage()->getId(),
                                'cell' => array(
                                    $res0->getImage()->getLot()->getDossier()->getNom(),
                                    $categorieLib,
                                    $sousCategorieLib,
                                    $tableSaisie,
                                    $res0->getRs(),
                                    $res0->getImage()->getExercice(),
                                    $icon,
                                    $res0->getImage()->getNom(),
                                    $res0->getNumFacture(),
                                    $dateFacture,
                                    $chrono,
                                    number_format($sommeHt, 2, '.', ''),
                                    number_format($sommeTva, 2, '.', ''),
                                    number_format($sommeHt + $sommeTva, 2, '.', ''),
                                    $tiersCompte,
                                    $compteRes,
                                    $dateEcheance,
                                    $dateScan
                                )
                            );
                            break;
                        //Note de Frais
                        case 'CODE_NDF':
                            $rows[] = array(
                                'id' => $res0->getImage()->getId(),
                                'cell' => array(
                                    $res0->getImage()->getLot()->getDossier()->getNom(),
                                    $categorieLib,
                                    $sousCategorieLib,
                                    $tableSaisie,
                                    $tiersIntitule,
//                                        $tiers,
                                    $res0->getImage()->getExercice(),
                                    $icon,
                                    $res0->getImage()->getNom(),
                                    $dateNdf,
                                    number_format($sommeNdfHt, 2, '.', ''),
                                    number_format($sommeNdfTva, 2, '.', ''),
                                    number_format($sommeNdfTtc, 2, '.', ''),
                                    $tiersCompte,
                                    $compteRes,
                                    $dateScan
                                )
                            );
                            break;

                        //Banque
                        case 'CODE_BANQUE':
                            $rows[] = array(
                                'id' => $res0->getImage()->getId(),
                                'cell' => array(
                                    $res0->getImage()->getLot()->getDossier()->getNom(),
                                    $categorieLib,
                                    $sousCategorieLib,
                                    $tableSaisie,
                                    $banque,
                                    $res0->getImage()->getExercice(),
                                    $icon,
                                    $res0->getImage()->getNom(),
                                    $compteBanque,
                                    $dateDebutBanque,
                                    $dateFinBanque,
                                    $res0->getSoldeDebut(),
                                    $res0->getSoldeFin(),
                                    $dateScan
                                )
                            );
                            break;

                        //Social, Fiscal
                        case 'CODE_SOC':
                        case 'CODE_FISC':
                            $rows[] = array(
                                'id' => $res0->getImage()->getId(),
                                'cell' => array(
                                    $res0->getImage()->getLot()->getDossier()->getNom(),
                                    $categorieLib,
                                    $sousCategorieLib,
                                    $tableSaisie,
                                    $tiersCompte,
                                    $res0->getImage()->getExercice(),
                                    $icon,
                                    $res0->getImage()->getNom(),
                                    $dateFacture,
                                    number_format($sommeHt, 2, '.', ''),
                                    $dateEcheance,
                                    $dateScan
                                )
                            );
                            break;

                            //wqa
                        //Contrat courrier & Gestion & Juridique
                        case 'CODE_COURRIER':
                        case 'CODE_ETATS_COMPTABLE':
                        case 'CODE_GESTION':
                        case 'CODE_JURIDIQUE':

                            $resArrCegj = $results['cegj'];
                            $description = '';
                            $ec1 = '';
                            $ec2 = '';
                            $dateCegj = '';

                            if(count($resArrCegj) > 0) {
                                /** @var ImputationControleCegj $resCegj */
                                $resCegj = $resArrCegj[0];
                                $description = $resCegj->getDescription();
                                $ec1 = $resCegj->getEntite1();
                                $ec2 = $resCegj->getEntite2();
                                $dateCegj = ($resCegj->getDatePiece() === null) ? '' : $resCegj->getDatePiece()->format('Y-m-d');
                            }
                            $rows[] = array(
                                'id' => $res0->getImage()->getId(),
                                'cell' => array(
                                    $res0->getImage()->getLot()->getDossier()->getNom(),
                                    $categorieLib,
                                    $sousCategorieLib,
                                    $ssCategorieLib,
                                    $description,
                                    $ec1,
                                    $ec2,
                                    $tableSaisie,
                                    $res0->getImage()->getExercice(),
                                    $icon,
                                    $res0->getImage()->getNom(),
                                    $dateCegj,
                                    $dateScan
                                )
                            );
                            break;

                        default:

                            $rows[] = array(
                                'id' => $res0->getImage()->getId(),
                                'cell' => array(
                                    $res0->getImage()->getLot()->getDossier()->getNom(),
                                    $categorieLib,
                                    $sousCategorieLib,
                                    $tableSaisie,
                                    $tiersCompte,
                                    $res0->getImage()->getExercice(),
                                    $icon,
                                    $res0->getImage()->getNom(),
                                    number_format($sommeHt, 2, '.', ''),
                                    number_format($sommeTva, 2, '.', ''),
                                    number_format($sommeHt + $sommeTva, 2, '.', ''),
                                    $dateFacture,
                                    $dateScan
                                )
                            );

                            break;
                    }

                    $im = array('nom' => $image->getNom(), 'extension' => $image->getExtImage(), 'dateScan' => $image->getLot()->getDateScan()->format("Ymd"));

//                    $listeImagesDownload[] = $image->getNom();

                    $listeImagesDownload[] = $im;
                }
            }
        }

        if($imageSeparations != null) {

            $icon = '<i class="fa fa-file-text"></i>';

            foreach ($imageSeparations as $imageSeparation) {
                /** @var $imageSeparation Separation */

                $soussouscategorieValide = true;

                //Raha misy soussouscategorie dia alaina avy @ soussouscategorie ny categorie
                if ($imageSeparation->getSoussouscategorie() != null) {

                    $dateScan = '';

                    $cat = $imageSeparation->getSoussouscategorie()->getSouscategorie()->getCategorie();

                    //Raha tsy mitovy ny categorie an'ilay soussouscategorie & categorie selectionné
                    if ($categorieCode != -1 && $cat->getCode() != $categorieCode) {
                        $soussouscategorieValide = false;
                    }

                    $categorieLib = $imageSeparation->getSoussouscategorie()->getSouscategorie()->getCategorie()->getLibelleNew();
                    $sousCategorieLib = $imageSeparation->getSoussouscategorie()->getSouscategorie()->getLibelleNew();
                    $ssCategorieLib = $imageSeparation->getSoussouscategorie()->getLibelleNew();


                    if ($imageSeparation->getImage()->getLot()->getDateScan() != null) {
                        $dateScan = $imageSeparation->getImage()->getLot()->getDateScan()->format('Y-m-d');
                    }


                    if ($soussouscategorieValide == true) {

                        switch ($categorieCode) {

                            //Client, Fournisseur
                            case 'CODE_CLIENT':
                            case 'CODE_FRNS':
                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        $sousCategorieLib,
                                        'Catégorisée',
                                        '',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        $dateScan
                                    )
                                );
                                break;

                            //Note de Frais
                            case 'CODE_NDF':
                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        $sousCategorieLib,
                                        'Catégorisée',
                                        '',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        $dateScan
                                    )
                                );
                                break;

                            //Banque
                            case 'CODE_BANQUE':
                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        $sousCategorieLib,
                                        'Catégorisée',
                                        '',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        $dateScan
                                    )
                                );
                                break;

                            //Fiscal, Social
                            case 'CODE_FISC':
                            case 'CODE_SOC':
                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        $sousCategorieLib,
                                        'Catégorisée',
                                        '',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        '',
                                        '',
                                        $dateScan
                                    )
                                );
                                break;

                            //Contrat courrier & Gestion & Juridique
                            case 'CODE_COURRIER':
                            case 'CODE_ETATS_COMPTABLE':
                            case 'CODE_GESTION':
                            case 'CODE_JURIDIQUE':
                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        $sousCategorieLib,
                                        $ssCategorieLib,
                                        '',
                                        '',
                                        '',
                                        'Catégorisée',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        $dateScan
                                    )
                                );

                                break;

                            default:
                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        $sousCategorieLib,
                                        'Catégorisée',
                                        '',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        '',
                                        '',
                                        '',
                                        $dateScan
                                    )
                                );
                                break;
                        }
                    }


                }

                if($imageSeparation->getSoussouscategorie() == null || $soussouscategorieValide == false) {

                    //Raha tsy misy soussouscategorie dia categorie avy @ separation ny categorie

                    if ($imageSeparation->getCategorie() !== null) {

                        $imageSeparation->getImage()->getNom();
                        $dateScan = '';
                        $categorieLib = '';
                        if ($imageSeparation->getCategorie() !== null) {
                            $categorieLib = $imageSeparation->getCategorie()->getLibelleNew();
                        }

                        if ($imageSeparation->getImage()->getLot()->getDateScan() != null) {
                            $dateScan = $imageSeparation->getImage()->getLot()->getDateScan()->format('Y-m-d');
                        }

                        switch ($categorieCode) {


                            //Client, Fournisseur
                            case 'CODE_CLIENT':
                            case 'CODE_FRNS':

                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        '',
                                        'Catégorisée',
                                        '',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        $dateScan
                                    )
                                );

                                break;

                            //Note de Frais
                            case 'CODE_NDF':
                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        '',
                                        'Catégorisée',
                                        '',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        $dateScan
                                    )
                                );
                                break;

                            //Banque
                            case 'CODE_BANQUE':
                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        '',
                                        'Catégorisée',
                                        '',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        $dateScan
                                    )
                                );
                                break;

                            //Fiscal, Social
                            case 'CODE_SOC':
                            case 'CODE_FISC':
                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        '',
                                        'Catégorisée',
                                        '',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        '',
                                        '',
                                        $dateScan
                                    )
                                );
                                break;


                            //Contrat courrier & Gestion & Juridique
                            case 'CODE_COURRIER':
                            case 'CODE_ETATS_COMPTABLE':
                            case 'CODE_GESTION':
                            case 'CODE_JURIDIQUE':

                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        '',
                                        '',
                                        '',
                                        '',
                                        '',
                                        'Catégorisée',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        $dateScan
                                    )
                                );

                                break;


                            default:
                                $rows[] = array(
                                    'id' => $imageSeparation->getImage()->getId(),
                                    'cell' => array(
                                        $imageSeparation->getImage()->getLot()->getDossier()->getNom(),
                                        $categorieLib,
                                        '',
                                        'Catégorisée',
                                        '',
                                        $imageSeparation->getImage()->getExercice(),
                                        $icon,
                                        $imageSeparation->getImage()->getNom(),
                                        '',
                                        '',
                                        '',
                                        '',
                                        $dateScan
                                    )
                                );

                                break;
                        }
                    }

                    $im = array('nom' => $imageSeparation->getImage()->getNom(), 'extension' => $imageSeparation->getImage()->getExtImage(),
                        'dateScan' => $imageSeparation->getImage()->getLot()->getDateScan()->format("Ymd"));

//                    $listeImagesDownload[] = $imageSeparation->getImage()->getNom();

                    $listeImagesDownload[] = $im;
                }
            }
        }

        return array('rows' => $rows, 'listeImagesDownload'=>$listeImagesDownload);
    }

    /**
     * Initialisation Tableau pour les Encours
     * @param $imagesEncours
     * @return array
     */
    public function initializeGridEncours($imagesEncours)
    {

        $rows = array();
        $listeImagesDownload = array();

        foreach ($imagesEncours as $imagesEncour) {

            /**@var $imagesEncour Image */
            $dateScan = '';
            if ($imagesEncour->getLot()->getDateScan() != null) {
                $dateScan = $imagesEncour->getLot()->getDateScan()->format('Y-m-d');
            }

            $rows[] = array(
                'id' => $imagesEncour->getId(),
                'cell' => array(
                    $imagesEncour->getLot()->getDossier()->getNom(),
                    'Encours',
                    '',
                    'Reçue',
                    '',
                    $imagesEncour->getExercice(),
                    '<i class="fa fa-file-text"></i>',
                    $imagesEncour->getNom(),
                    '',
                    '',
                    '',
                    '',
                    $dateScan
                )
            );


            $im = array('nom' => $imagesEncour->getNom(), 'extension' => $imagesEncour->getExtImage(), 'dateScan' => $imagesEncour->getLot()->getDateScan()->format("Ymd"));

//            $listeImagesDownload[] = $imagesEncour->getNom();

            $listeImagesDownload[] = $im;
        }

        return array('rows' => $rows, 'listeImagesDownload' => $listeImagesDownload);
    }


    public function initializeGridEncoursV2($infoEncours)
    {

        $rows = array();

        $listeImagesEncoursDownload = array();

        foreach ($infoEncours as $imagesEncour) {

            $dateScan = '';
            if ($imagesEncour->date_scan != null) {
                $dateScan = $imagesEncour->date_scan;
            }

            $dossierNom = $imagesEncour->dossier_nom;


            $rows[] = array(
                'id' => $imagesEncour->image_id,
                'cell' => array(
                    $dossierNom,
                    'Encours',
                    '',
                    'Reçue',
                    '',
                    $imagesEncour->exercice,
                    '<i class="fa fa-file-text"></i>',
                    $imagesEncour->nom,
                    '',
                    '',
                    '',
                    '',
                    $dateScan
                )
            );


            $im = array('nom' => $imagesEncour->nom, 'extension' => $imagesEncour->ext_image, 'dateScan' =>str_replace('-','',$imagesEncour->date_scan));


//            $listeImagesEncoursDownload[] = $imagesEncour->nom;

            $listeImagesEncoursDownload[] = $im;
        }

        return array('rows' =>$rows, 'listeImagesEncoursDownload' => $listeImagesEncoursDownload);
    }



    public function initializeGridV2($infoImages, $infoSeparations, $categorieCode)
    {
        $rows = array();
        $listeImagesDownload = array();

        $categories = $this->getDoctrine()
            ->getRepository('AppBundle:Categorie')
            ->findBy(array('code'=>$categorieCode));

        $categorie = null;
        /**@var $categorie  Categorie*/
        if(count($categories) > 0){
            $categorie = $categories[0];
        }


        if ($infoImages != null) {
            foreach ($infoImages as $results) {

                if ($results != null) {

                    $res0 = $results['saisie'][0];

                    $tableSaisie = $results['table'];

                    $image = $results['image'];


                    //Note de frais
                    $resNdf = $results['ndf'];

                    switch ($tableSaisie) {
                        case 'Saisie 1':
                            $tableSaisie = 'Saisie';
                            break;
                        case 'Saisie 2':
                            $tableSaisie = 'Saisie';
                            break;
                        case 'Controle Saisie':
                            $tableSaisie = 'Saisie';
                            break;
                        case 'Imputation':
                            $tableSaisie = 'Imputée';
                            break;
                        case 'Controle Imputation':
                            $tableSaisie = 'Imputée';
                            break;
                    }

                    $listeSousCategorie = array();
                    $listeSsCategorie = array();


                    if ($res0 != null) {
                        //Raha efa misy categorie ny saisie
//                        if ($res0->soussouscategorie_id != null)
                        {

                            $sommeHt = 0;
                            $sommeTva = 0;

                            $resTva = $results['tva'];
                            for ($i = 0, $iMax = count($resTva); $i < $iMax; $i++) {
                                $tva = $resTva[$i];
                                $sommeHt += $tva->montant_ht;
                                if ($tva->tva_taux != null) {
                                    $sommeTva += ($tva->montant_ht) * ($tva->tva_taux) / 100;
                                }

                            }




                            $categorieLib = '';
                            $sousCategorieLib = '';
                            $ssCategorieLib = '';

                            $trouveCategorie = false;

                            if ($tableSaisie === 'Imputée') {
                                if ($listeSousCategorie != null) {

                                    $trouveCategorie = true;

                                    foreach ($listeSousCategorie as $sousCategorie) {
                                        if ($sousCategorieLib == '') {
                                            $sousCategorieLib = $sousCategorie;
                                        } else {
                                            $sousCategorieLib = $sousCategorieLib . ', ' . $sousCategorie;
                                        }
                                    }
                                }
                                if ($listeSsCategorie != null) {

                                    $trouveCategorie = true;

                                    foreach ($listeSsCategorie as $ssCategorie) {

                                        if ($ssCategorieLib == '') {
                                            $ssCategorieLib = $ssCategorie;
                                        } else {
                                            $ssCategorieLib = $ssCategorieLib . ', ' . $ssCategorie;
                                        }
                                    }
                                }
                            }

                            if ($res0->soussouscategorie_id != null) {
                                if ($tableSaisie !== 'Imputée') {
//                                    $sousCategorieLib = $res0->getSoussouscategorie()->getSouscategorie()->getLibelle();

                                    /** @var  $soussouscategorie Soussouscategorie*/
                                    $soussouscategorie = $this->getDoctrine()
                                        ->getRepository('AppBundle:Soussouscategorie')
                                        ->find($res0->soussouscategorie_id);

                                    $ssCategorieLib = $res0->soussouscategorie_libelle_new;
                                    $sousCategorieLib = $soussouscategorie->getSouscategorie()->getLibelleNew();
                                } else if ($trouveCategorie == false) {
//                                    $sousCategorieLib = $res0->getSoussouscategorie()->getSouscategorie()->getLibelle();
                                    $sousCategorieLib = $res0->souscategorie_libelle_new;

//                                    $ssCategorieLib = $res0->getSoussouscategorie()->getLibelle();
                                    $ssCategorieLib = $res0->soussouscategorie_libelle_new;
                                }
//                                $categorieLib = $res0->getSoussouscategorie()->getSouscategorie()->getCategorie()->getLibelle();

                                $categorieLib = $this->getDoctrine()
                                    ->getRepository('AppBundle:Soussouscategorie')
                                    ->find($res0->soussouscategorie_id)
                                    ->getSouscategorie()
                                    ->getCategorie()
                                    ->getLibelleNew();


                            } else {

                                $trouveCategorieLib = false;

                                if($tableSaisie === 'Imputée'){
                                    if($res0->souscategorie_id != null){
                                        $categorieLib = $this->getDoctrine()
                                            ->getRepository('AppBundle:Souscategorie')
                                            ->find($res0->souscategorie_id)
                                            ->getCategorie()
                                            ->getLibelleNew();
                                        $trouveCategorieLib = true;
                                    }
                                }

                                //AFFICHER-NA FOTSINY ILAY CATEGORIE SELECTIONNE
                                if(null !== $categorie && $trouveCategorieLib == false) {
                                    $categorieLib = $categorie->getLibelleNew();
                                }
                                else{
                                    $trouveCategorieImputation = false;
                                    //JERENA NY SOUSCATEGORIE RAHA IMPUTATION
                                    if($tableSaisie === 'Imputée'){
                                        if($res0->souscategorie_id != null){//
                                            $trouveCategorieImputation = true;
                                        }

                                    }
                                    //JERENA NY ANY @ SEPARATION
                                    if(!$trouveCategorieImputation){
                                        $resSeparation = $this->getDoctrine()
                                            ->getRepository('AppBundle:Separation')
                                            ->findOneBy(array('image'=>$image->image_id));

                                        if(null !== $resSeparation && count($resSeparation) > 0){
                                            /** @var $resSeparation Separation*/
                                            if($resSeparation->getCategorie() != null){
                                                $categorieLib = $resSeparation->getCategorie()->getLibelleNew();
                                            }
                                        }
                                    }
                                }
                            }

//                        tsy afficher-na ny doublon
                            $posScat = strpos(strtolower($sousCategorieLib), 'doublon');
                            $posMalScat = strpos(strtolower($sousCategorieLib), 'mal aff');
                            $posSscat = strpos(strtolower($ssCategorieLib), 'doublon');
                            $posMalSscat = strpos(strtolower($ssCategorieLib), 'mal aff');
                            if ($posScat !== false || $posSscat !== false ||
                                $posMalScat !== false || $posMalSscat !== false) {
                                continue;
                            }

                            //Tsy afficher-na izay catégorie tsy izy
                            if(null !== $categorie){
                                if($categorie->getLibelleNew() != $categorieLib){
                                    continue;
                                }
                            }


                            if($res0->soussouscategorie_id == null){
                                if($tableSaisie === 'Imputée'){
                                    if($res0->souscategorie_id == null){
//                                        continue;
                                    }
                                }
                            }


                            $dateFacture = '';
                            $dateScan = '';


                            $dateDebutBanque = '';
                            $dateFinBanque = '';

                            if ($res0->date_facture != null) {
                                $dateFacture = $res0->date_facture;
                            }

                            if ($image->date_scan != null) {
                                $dateScan = $image->date_scan;
                            }



                            if ($res0->periode_d1 != null) {
                                $dateDebutBanque = $res0->periode_d1;
                            }

                            if ($res0->periode_f1 != null) {
                                $dateFinBanque = $res0->periode_f1;
                            }

                            $chrono = '';
                            if ($res0->chrono != null) {
                                $chrono = $res0->chrono;
                            }

                            $tiers = '';
                            $compteRes = '';
                            $tiersIntitule = '';
                            $tiersCompte = '';

                            if ($tableSaisie === 'Imputée') {
                                if ($resTva != null) {
                                    foreach ($resTva as $tvaImputation) {
                                        if ($tvaImputation->compte_str != null) {
                                            $tiersCompte = $tvaImputation->compte_str;
                                            $tiersIntitule = $tvaImputation->tiers_intitule;
                                            $tiers = $tvaImputation->tiers_id;
                                        }

                                        if ($tvaImputation->compte != null) {
                                            $compteRes = $tvaImputation->compte;
                                        }
                                    }
                                }
                            }


                            $dateEcheance = '';

                            if ($res0->date_echeance != null) {
                                $dateEcheance = $res0->date_echeance;
                            }

                            else {

                                $im = $this->getDoctrine()
                                    ->getRepository('AppBundle:Image')
                                    ->find($image->image_id);

                                $dateFact = new DateTime($res0->date_facture);

                                $dateLivraison = new DateTime($res0->date_livraison);

                                if ($categorieCode === 'CODE_FRNS' || $categorieCode === 'CODE_CLIENT')
                                    $dateEcheance = $this->calculDateEcheance($im->getLot()->getDossier(), $tiers, $dateFact, $dateLivraison, $categorieCode);

                            }



                            $sommeNdfTtc = 0;
                            $sommeNdfTva = 0;
                            $sommeNdfHt = 0;
                            $tiersNdf = '';
                            $dateNdf = '';


                            if ($resNdf != null) {

                                foreach ($resNdf as $ndf) {
                                    $tvaTaux = 0;
                                    if ($ndf->tva_taux != null) {
                                        $tvaTaux = $ndf->tva_taux;
                                    }
                                    $sommeNdfTtc += $ndf->ttc;
                                    $sommeNdfHt += (100 * $ndf->ttc) / (100 + $tvaTaux);
                                    $sommeNdfTva += ($sommeNdfTtc - $sommeNdfHt);

                                    if ($ndf->date != null) {
//                                        $dateNdf = $ndf->date->format('Y-m-d');
                                        $dateNdf = $ndf->date;
                                    }
                                    if ($ndf->profit_de != '' && $ndf->profit_de != null) {
                                        $tiersNdf = $ndf->profit_de;
                                    }
                                }
                            }

                            $banque = '';
                            $compteBanque = '';
                            if ($res0 != null) {

                                if ($res0->banque_compte_id != null) {

                                    $banqueCompte = $this->getDoctrine()
                                        ->getRepository('AppBundle:BanqueCompte')
                                        ->find($res0->banque_compte_id);

                                    $compteBanque = $banqueCompte->getNumcompte();
                                    $banque = $banqueCompte->getBanque()->getNom();
                                }
                            }


                            $dossierNom = '';

                            if($image->dossier_nom != null){
                                $dossierNom = $image->dossier_nom;
                            }


                            switch ($categorieCode) {

                                //Client, fournisseur
                                case 'CODE_CLIENT':
                                case 'CODE_FRNS':
                                    $rows[] = array(
                                        'id' => $image->image_id,
                                        'cell' => array(
                                            $dossierNom,
                                            $categorieLib,
                                            $sousCategorieLib,
                                            $tableSaisie,
                                            $res0->rs,
                                            $image->exercice,
                                            '<i class="fa fa-file-text"></i>',
                                            $image->nom,
                                            $res0->num_facture,
                                            $dateFacture,
                                            $chrono,
                                            number_format($sommeHt, 2, '.', ''),
                                            number_format($sommeTva, 2, '.', ''),
                                            number_format($sommeHt + $sommeTva, 2, '.', ''),
                                            $tiersCompte,
                                            $compteRes,
                                            $dateEcheance,
                                            $dateScan
                                        )
                                    );
                                    break;
                                //Note de Frais
                                case 'CODE_NDF':
                                    $rows[] = array(
                                        'id' => $image->image_id,
                                        'cell' => array(
                                            $dossierNom,
                                            $categorieLib,
                                            $sousCategorieLib,
                                            $tableSaisie,
//                                            $tiersNdf,
                                            $tiersIntitule,
                                            $image->exercice,
                                            '<i class="fa fa-file-text"></i>',
                                            $image->nom,
                                            $dateNdf,
                                            number_format($sommeNdfHt, 2, '.', ''),
                                            number_format($sommeNdfTva, 2, '.', ''),
                                            number_format($sommeNdfTtc, 2, '.', ''),
                                            $tiersCompte,
                                            $compteRes,
                                            $dateScan
                                        )
                                    );
                                    break;

                                //Banque
                                case 'CODE_BANQUE':
                                    $rows[] = array(
                                        'id' => $image->image_id,
                                        'cell' => array(
                                            $dossierNom,
                                            $categorieLib,
                                            $sousCategorieLib,
                                            $tableSaisie,
                                            $banque,
                                            $image->exercice,
                                            '<i class="fa fa-file-text"></i>',
                                            $image->nom,
                                            $compteBanque,
                                            $dateDebutBanque,
                                            $dateFinBanque,
                                            $res0->solde_debut,
                                            $res0->solde_fin,
                                            $dateScan
                                        )
                                    );
                                    break;

                                //Social, Fiscal
                                case 'CODE_SOC':
                                case 'CODE_FISC':
                                    $rows[] = array(
                                        'id' => $image->image_id,
                                        'cell' => array(
                                            $dossierNom,
                                            $categorieLib,
                                            $sousCategorieLib,
                                            $tableSaisie,
//                                            $tiers,
                                            $tiersCompte,
                                            $image->exercice,
                                            '<i class="fa fa-file-text"></i>',
                                            $image->nom,
                                            $dateFacture,
                                            number_format($sommeHt, 2, '.', ''),
                                            $dateEcheance,
                                            $dateScan
                                        )
                                    );
                                    break;

                                //Contrat courrier & Gestion & Juridique
                                case 'CODE_COURRIER':
                                case 'CODE_JURIDIQUE':
                                    $rows[] = array(
                                        'id' => $image->image_id,
                                        'cell' => array(
                                            $dossierNom,
                                            $categorieLib,
                                            $sousCategorieLib,
                                            $tableSaisie,
                                            $tiersCompte,
                                            $image->exercice,
                                            '<i class="fa fa-file-text"></i>',
                                            $image->nom,
                                            $dateFacture,
                                            $dateScan
                                        )
                                    );
                                    break;

                                default:
                                    $rows[] = array(
                                        'id' => $image->image_id,
                                        'cell' => array(
                                            $dossierNom,
                                            $categorieLib,
                                            $sousCategorieLib,
                                            $tableSaisie,
//                                            $tiers,
                                            $tiersCompte,
                                            $image->exercice,
                                            '<i class="fa fa-file-text"></i>',
                                            $image->nom,
                                            number_format($sommeHt, 2, '.', ''),
                                            number_format($sommeTva, 2, '.', ''),
                                            number_format($sommeHt + $sommeTva, 2, '.', ''),
                                            $dateFacture,
                                            $dateScan
                                        )
                                    );

                                    break;


                            }


                            $im = array('nom' => $image->nom, 'extension' => $image->ext_image, 'dateScan' => str_replace('-','',$image->date_scan));
//                            $listeImagesDownload[] = $image->nom;
                            $listeImagesDownload[] = $im;


                        }
                    }
                }
            }
        }

        if($infoSeparations != null) {
            foreach ($infoSeparations as $result) {

                $dossierNom = '';

                if($result->dossier_nom != null){
                    $dossierNom = $result->dossier_nom;
                }


                //Raha misy soussouscategorie dia alaina avy @ soussouscategorie ny categorie
                if ($result->soussouscategorie_id != null) {

                    $dateScan = '';
                    $categorieLib = '';
                    $ssCategorieLib = '';
                    $sousCategorieLib = '';
                    if ($result->soussouscategorie_id != null) {
                        $soussouscategorie = $this->getDoctrine()
                            ->getRepository('AppBundle:Soussouscategorie')
                            ->find($result->soussouscategorie_id);

                        $categorieLib = $soussouscategorie->getSouscategorie()->getCategorie()->getLibelleNew();
                        $sousCategorieLib = $soussouscategorie->getSouscategorie()->getLibelleNew();
                        $ssCategorieLib = $result->soussouscategorie_libelle;
                    }

                    if ($result->date_scan != null) {
                        $dateScan = $result->date_scan;
                    }


                    switch ($categorieCode) {

                        //Client, Fournisseur
                        case 'CODE_CLIENT':
                        case 'CODE_FRNS':
                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    $sousCategorieLib,
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $dateScan
                                )
                            );
                            break;

                        //Note de Frais
                        case 'CODE_NDF':
                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    $sousCategorieLib,
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $dateScan
                                )
                            );
                            break;

                        //Banque
                        case 'CODE_BANQUE':
                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    $sousCategorieLib,
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $dateScan
                                )
                            );
                            break;

                        //Fiscal, Social
                        case 'CODE_FISC':
                        case 'CODE_SOC':
                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    $sousCategorieLib,
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    '',
                                    '',
                                    $dateScan
                                )
                            );
                            break;

                        //Contrat courrier & Gestion & Juridique
                        case 'CODE_JURIDIQUE':
                        case 'CODE_COURRIER':
                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    $sousCategorieLib,
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    $dateScan
                                )
                            );

                            break;

                        default:
                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    $sousCategorieLib,
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    '',
                                    '',
                                    '',
                                    $dateScan
                                )
                            );



                            break;
                    }


                } //Raha tsy misy soussouscategorie dia categorie avy @ separation ny categorie
                else if ($result->categorie_id != null) {

                    $dateScan = '';
                    $categorieLib = $result->categorie_libelle;

                    if ($result->date_scan != null) {
                        $dateScan = $result->date_scan;
                    }


                    switch ($categorieCode) {

                        //Client, Fournisseur
                        case 'CODE_CLIENT':
                        case 'CODE_FRNS':

                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    '',
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $dateScan
                                )
                            );

                            break;

                        //Note de Frais
                        case 'CODE_NDF':
                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    '',
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $dateScan
                                )
                            );
                            break;

                        //Banque
                        case 'CODE_BANQUE':
                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    '',
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $dateScan
                                )
                            );
                            break;

                        //Fiscal, Social
                        case 'CODE_SOC':
                        case 'CODE_FISC':
                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    '',
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    '',
                                    '',
                                    $dateScan
                                )
                            );
                            break;


                        //Contrat courrier & Gestion & Juridique
                        case 'CODE_COURRIER':
                        case 'CODE_JURIDIQUE':
                            $rows[] = array(
                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    '',
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    $dateScan
                                )
                            );

                            break;


                        default:
                            $rows[] = array(
//                                'id' => $result->image_id,
//                                'cell' => array(
//                                    $dossierNom,
//                                    $categorieLib,
//                                    '',
//                                    'Catégorisée',
//                                    '',
//                                    $result->exercice,
//                                    '<i class="fa fa-file-text"></i>',
//                                    $result->nom,
//                                    '',
//                                    '',
//                                    '',
//                                    '',
//                                    $dateScan

                                'id' => $result->image_id,
                                'cell' => array(
                                    $dossierNom,
                                    $categorieLib,
                                    '',
                                    'Catégorisée',
                                    '',
                                    $result->exercice,
                                    '<i class="fa fa-file-text"></i>',
                                    $result->nom,
                                    '',
                                    '',
                                    '',
                                    '',
                                    $dateScan
                                )
                            );


                            break;
                    }


                    $im = array('nom' => $result->nom, 'extension' => $result->ext_image, 'dateScan' => str_replace('-','',$result->date_scan));

//                    $listeImagesDownload[] = $result->nom;

                    $listeImagesDownload[] = $im;


                }
            }
        }

        return array('rows' => $rows, 'listeImagesDownload' => $listeImagesDownload);
    }


    public function showDateModalAction(Request $request){

        $post = $request->request;

        $clientId = Boost::deboost($post->get('clientId'),$this);
        $siteId = Boost::deboost($post->get('siteId'), $this);

        $exercice = $post->get('exercice');
        if($exercice === ''){
            $exercice = null;
        }

        $categories = $this->getDoctrine()
            ->getRepository('AppBundle:Categorie')
            ->findBy(array('actif'=> 1), array('libelleNew' => 'asc'));

        $resCat = array();

        $user = $this->getUser();
        $role = $this->getUser()->getRoles();

        foreach ($categories as $categorie){
            $resCat[] = array('id'=>$categorie->getCode(), 'libelle'=>$categorie->getLibelleNew());
        }

        $dossiersites = array();
        $dossiers = array();

        if($siteId != 0) {

            $site = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->find($siteId);

            /** @var $site Site */
            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getUserDossier($user, $role, $site->getId(), $exercice);


        }
        else{

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($clientId);


            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getUserDossier($user, $client, null, $exercice);


        }



        $resDoss = array();

        if(count($dossiers)>0) {
            foreach ($dossiers as $dossier) {

                $resDoss[] = array('id' => $dossier->getId(), 'nom_dossier' => $dossier->getNom());
            }
        }


        usort($resDoss, array($this, 'compNom'));

        $liste = array('dossiers' => $resDoss, 'categories'=>$resCat);

        return new JsonResponse($liste);
    }

    /**
     * @param $datePiece DateTime
     * @return Date
     */
    public function calculEcheance($datePiece, $nbreJour, $dateLe){

        $dernierMois = null;

        if($dateLe == 30) {
            if(null !== $datePiece) {
                $dernierMois = $datePiece->format('Y-m-t');
            }
        }
        else if(null !== $dateLe){

            $temp = $datePiece->format('Y').'-'.$datePiece->format('m').'-'.$dateLe;

            $dernierMois = $temp;
        }
        else{
            if(null !== $datePiece) {
                $dernierMois = $datePiece->format('Y-m-d');
            }
        }

        if(null === $dernierMois){
            return null;
        }

        $dernierMois = date('Y-m-d', strtotime($dernierMois. '+ '.$nbreJour.' days'));

        //Raha sabootsy dia +2
        if(date('N', strtotime($dernierMois)) == 6){
            $res = date('Y-m-d', strtotime($dernierMois. '+ 2 days'));
        }
        //Raha alahady dia +1
        elseif( date('N', strtotime($dernierMois)) == 7) {
            $res = date('Y-m-d', strtotime($dernierMois. '+ 1 day'));
        }
        else{
            $res = $dernierMois;
        }

        return $res;
    }


    /**
     * @param $dossier Dossier
     * @param $tiers Tiers
     * @param $dateFacutre
     * @param $dateLivraison
     * @param $categorieCode
     * @return Date|null
     * @internal param Saisie1 $saisie
     */
    public function calculDateEcheance($dossier, $tiers, $dateFacutre, $dateLivraison, $categorieCode){
        $typeTiers = -1;

        $dateEcheance = null;

        switch ($categorieCode){
            case 'CODE_FRNS':
                $typeTiers = 0;
                break;

            case 'CODE_CLIENT':
                $typeTiers = 1;
                break;

            default:
                break;
        }

        $reglePaiementTiers = null;

        if($tiers != null){

            $reglePaiementTiers = $this->getDoctrine()
                ->getRepository('AppBundle:ReglePaiementTiers')
                ->findBy(array('dossier'=>$dossier, 'tiers'=>$tiers));
        }


        if(count($reglePaiementTiers) > 0){

            /** @var  $reglePaiementTier ReglePaiementTiers*/
            $reglePaiementTier = $reglePaiementTiers[0];

            $reglementTypeDate = $reglePaiementTier->getTypeDate();

            switch ($reglementTypeDate){
                case 0:
                    $datePiece = $dateFacutre;
                    break;
                case 1:
                    $datePiece = $dateLivraison;
                    break;
                default:
                    $datePiece = null;
                    break;
            }


            $nbreJour = 45;

            if($reglePaiementTier->getNbreJour() != null){
                $nbreJour = $reglePaiementTier->getNbreJour();
            };

            $dateLe = 30;
            if($reglePaiementTier->getDateLe() != null){
                $dateLe = $reglePaiementTier->getDateLe();
            }


            if($datePiece != null) {
                $dateEcheance = $this->calculEcheance($datePiece, $nbreJour, $dateLe);
            }

        }
        else{

            $reglePaiementDossiers = $this->getDoctrine()
                ->getRepository('AppBundle:ReglePaiementDossier')
                ->findBy(array('dossier'=>$dossier, 'typeTiers'=>$typeTiers));

            if(count($reglePaiementDossiers) > 0) {

                $reglePaiementDossier = $reglePaiementDossiers[0];

                $reglementTypeDate = $reglePaiementDossier->getTypeDate();

                switch ($reglementTypeDate) {
                    case 0:
                        $datePiece = $dateFacutre;
                        break;
                    case 1:
                        $datePiece = $dateLivraison;
                        break;
                    default:
                        $datePiece = null;
                        break;
                }


                $nbreJour = 45;

                if ($reglePaiementDossier->getNbreJour() != null) {
                    $nbreJour = $reglePaiementDossier->getNbreJour();
                };


                $dateLe = null;
                if($reglePaiementDossier->getDateLe() != null){
                    $dateLe = $reglePaiementDossier->getDateLe();
                }

                if($datePiece != null) {
                    $dateEcheance = $this->calculEcheance($datePiece, $nbreJour, $dateLe);
                }
            }
            else{
                $dateEcheance = $this->calculEcheance($dateFacutre, 45, null);
            }

        }

        return $dateEcheance;
    }



    public function initSousCategorieComboAction(Request $request){

        $post = $request->request;

        $categorieId = $post->get('categorieId');
        $sousCategorieId = $post->get('souscategorieId');

        $categorie = $this->getDoctrine()
            ->getRepository('AppBundle:Categorie')
            ->find($categorieId);

        $sousCategories = $this->getDoctrine()
            ->getRepository('AppBundle:Souscategorie')
            ->findBy(array('categorie'=> $categorie, 'actif' => 1), array('libelleNew' => 'ASC'));

        $options = '<option></option>';
        foreach ($sousCategories as $category){
            if($category->getId() === (int) $sousCategorieId){
                $options .= '<option value="'.$category->getId().'" selected>'.strtoupper($category->getLibelleNew()).'</option>';
            }
            else {
                $options .= '<option value="'.$category->getId().'">'.strtoupper($category->getLibelleNew()).'</option>';
            }
        }

        return new Response($options);
    }


    public function initSousSouscategorieComboAction(Request $request){


        $post = $request->request;

        $sousCategorieId = $post->get('souscategorieId');
        $sousSousCategorieId = $post->get('soussouscategorieId');

        $sousCategorie = $this->getDoctrine()
            ->getRepository('AppBundle:Souscategorie')
            ->find($sousCategorieId);

        $sousSousCategories = $this->getDoctrine()
            ->getRepository('AppBundle:Soussouscategorie')
            ->findBy(array('souscategorie'=>$sousCategorie, 'actif'=> 1), array('libelleNew' => 'ASC'));

        $options = '<option></option>';
        foreach ($sousSousCategories as $category){
            if($category->getId() === (int) $sousSousCategorieId){
                $options .= '<option value="'.$category->getId().'" selected>'.strtoupper($category->getLibelleNew()).'</option>';
            }
            else {
                $options .= '<option value="'.$category->getId().'">'.strtoupper($category->getLibelleNew()).'</option>';
            }
        }

        return new Response($options);

    }


    public function cegjSaveAction(Request $request){
        if($request->isXmlHttpRequest()){
            $posted = $request->request->all();

            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find($posted['image-id']);


            if(null===$image){
                $response = array('type' => 'error', 'action' => 'add');
                return new JsonResponse($response);
            }

            $dossier = null;
            $treeId = array();
            $oldTreeId = array();

            if(null !== $image->getLot()){
                /** @var Lot $lot */
                $lot = $image->getLot();
                $dossier = $lot->getDossier();

            }

            $etape = (int) $posted['etape-id'];

            $cej = null;
            $saisie = null;

            /** @var Categorie $oldCategorie */
            $oldCategorie = null;
            /** @var Souscategorie $oldSouscategorie */
            $oldSouscategorie = null;
            /** @var Soussouscategorie $oldSoussouscategorie */
            $oldSoussouscategorie = null;

            $categorie = $this->getDoctrine()
                ->getRepository('AppBundle:Categorie')
                ->find($posted['categorie']);
            $catLib = '';

            $isCej = false;

            $arecategoriser = false;
            $codeGejList = array(
                'CODE_COURRIER', 'CODE_ETATS_COMPTABLE', 'CODE_GESTION',
                'CODE_JURIDIQUE'
            );

            if(null !== $categorie){
                $catLib = $categorie->getLibelleNew();

                if(in_array($categorie->getCode(), $codeGejList)){
                    $isCej = true;
                }

                else if($categorie->getCode() === "CODE_A_RECATEGORISER"){
                    $arecategoriser = true;
                }
            }

            $souscategorie = $this->getDoctrine()
                ->getRepository('AppBundle:Souscategorie')
                ->find($posted['souscategorie']);
            $sCatLib = '';
            if(null !== $souscategorie){
                $sCatLib = $souscategorie->getLibelleNew();
            }

            $soussouscategorie = $this->getDoctrine()
                ->getRepository('AppBundle:Soussouscategorie')
                ->find($posted['soussouscategorie']);
            $ssCatLib = '';
            if(null !== $soussouscategorie){
                $ssCatLib = $soussouscategorie->getLibelleNew();
            }

            $description = ($posted['description'] === '') ? null : $posted['description'];

            $entite1 = ($posted['entite-concerne1'] === '') ? null : $posted['entite-concerne1'];
            $entite2 = ($posted['entite-concerne2'] === '') ? null : $posted['entite-concerne2'];
//            $entite3 = ($posted['entite-concerne3'] === '') ? null : $posted['entite-concerne3'];

            $commentaire = ($posted['commentaire'] === '') ? null : $posted['commentaire'];
//            $entite4 = ($posted['entite-concerne4'] === '') ? null : $posted['entite-concerne4'];


            $datePiece = ($posted['date-piece'] === '') ? null : \DateTime::createFromFormat('d/m/Y', $posted['date-piece']);


            $periodeDebut = ($posted['periode-debut'] === '') ? null : \DateTime::createFromFormat('d/m/Y', $posted['periode-debut']);
            $periodeFin = ($posted['periode-fin'] === '') ? null : \DateTime::createFromFormat('d/m/Y', $posted['periode-fin']);

            $em = $this->getDoctrine()
                ->getManager();

            $separation = null;



            switch ($etape) {
                case 11:

                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Saisie1')
                        ->findBy(array('image' => $image));



                    if (count($saisies) > 0) {
                        $saisie = $saisies[0];

                        if (null !== $saisie->getSoussouscategorie()) {
                            $oldSoussouscategorie = $saisie->getSoussouscategorie();
                        }
                        else {
                            $separations = $this->getDoctrine()
                                ->getRepository('AppBundle:Separation')
                                ->findBy(array('image' => $image));

                            if(count($separations) > 0){
                                $separation = $separations[0];
                                $oldCategorie = $separation->getCategorie();
                            }
                        }

                        $saisie->setSoussouscategorie($soussouscategorie);
                    } else {
                        $isCej = false;
                    }

                    if ($isCej && !$arecategoriser) {

                        $cejs = $this->getDoctrine()
                            ->getRepository('AppBundle:Saisie1Cegj')
                            ->findBy(array('image' => $image));

                        if (count($cejs) > 0) {
                            $cej = $cejs[0];

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);
                        } else {
                            $cej = new Saisie1Cegj();
                            $cej->setImage($image);

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);

                            $em->persist($cej);


                        }
                    }

                    $em->flush();


                    break;

                case 12:


                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Saisie2')
                        ->findBy(array('image' => $image));

                    if (count($saisies) > 0) {
                        $saisie = $saisies[0];

                        if (null !== $saisie->getSoussouscategorie()) {
                            $oldSoussouscategorie = $saisie->getSoussouscategorie();
                        }
                        else {
                            $separations = $this->getDoctrine()
                                ->getRepository('AppBundle:Separation')
                                ->findBy(array('image' => $image));

                            if(count($separations) > 0){
                                $separation = $separations[0];
                                $oldCategorie = $separation->getCategorie();
                            }
                        }


                        $saisie->setSoussouscategorie($soussouscategorie);
                    } else {
                        $isCej = false;
                    }

                    if ($isCej && !$arecategoriser) {

                        $cejs = $this->getDoctrine()
                            ->getRepository('AppBundle:Saisie2Cegj')
                            ->findBy(array('image' => $image));

                        if (count($cejs) > 0) {
                            $cej = $cejs[0];

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);
                        } else {
                            $cej = new Saisie2Cegj();
                            $cej->setImage($image);

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);

                            $em->persist($cej);


                        }
                    }

                    $em->flush();


                    break;
                case 13:


                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:SaisieControle')
                        ->findBy(array('image' => $image));

                    if (count($saisies) > 0) {
                        $saisie = $saisies[0];

                        if (null !== $saisie->getSoussouscategorie()) {
                            $oldSoussouscategorie = $saisie->getSoussouscategorie();
                        }
                        else {
                            $separations = $this->getDoctrine()
                                ->getRepository('AppBundle:Separation')
                                ->findBy(array('image' => $image));

                            if(count($separations) > 0){
                                $separation = $separations[0];
                                $oldCategorie = $separation->getCategorie();
                            }
                        }


                        $saisie->setSoussouscategorie($soussouscategorie);
                    } else {
                        $isCej = false;
                    }

                    if ($isCej && !$arecategoriser) {

                        $cejs = $this->getDoctrine()
                            ->getRepository('AppBundle:ControleCegj')
                            ->findBy(array('image' => $image));

                        if (count($cejs) > 0) {
                            $cej = $cejs[0];

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($$datePiece);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);
                        } else {
                            $cej = new ControleCegj();
                            $cej->setImage($image);

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);

                            $em->persist($cej);
                        }
                    }

                    $em->flush();


                    break;
                case 14:


                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:Imputation')
                        ->findBy(array('image' => $image));

                    if (count($saisies) > 0) {
                        $saisie = $saisies[0];

                        if (null !== $saisie->getSoussouscategorie()) {
                            $oldSoussouscategorie = $saisie->getSoussouscategorie();
                        } else if (null !== $saisie->getSouscategorie()) {
                            $oldSouscategorie = $saisie->getSouscategorie();
                        }
                        else {
                            $separations = $this->getDoctrine()
                                ->getRepository('AppBundle:Separation')
                                ->findBy(array('image' => $image));

                            if(count($separations) > 0){
                                $separation = $separations[0];
                                $oldCategorie = $separation->getCategorie();
                            }
                        }



                        $saisie->setSoussouscategorie($soussouscategorie);
                        $saisie->setSouscategorie($souscategorie);
                    } else {
                        $isCej = false;
                    }

                    if ($isCej && !$arecategoriser) {

                        $cejs = $this->getDoctrine()
                            ->getRepository('AppBundle:ImputationCegj')
                            ->findBy(array('image' => $image));

                        if (count($cejs) > 0) {
                            $cej = $cejs[0];

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);
                        } else {
                            $cej = new ImputationCegj();
                            $cej->setImage($image);

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);

                            $em->persist($cej);
                        }
                    }

                    $em->flush();


                    break;
                case 15:


                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:ImputationControle')
                        ->findBy(array('image' => $image));

                    if (count($saisies) > 0) {
                        $saisie = $saisies[0];

                        if (null !== $saisie->getSoussouscategorie()) {
                            $oldSoussouscategorie = $saisie->getSoussouscategorie();
                        } else if (null !== $saisie->getSouscategorie()) {
                            $oldSouscategorie = $saisie->getSouscategorie();
                        }
                        else {
                            $separations = $this->getDoctrine()
                                ->getRepository('AppBundle:Separation')
                                ->findBy(array('image' => $image));

                            if(count($separations) > 0){
                                $separation = $separations[0];
                                $oldCategorie = $separation->getCategorie();
                            }
                        }


                        $saisie->setSoussouscategorie($soussouscategorie);
                        $saisie->setSouscategorie($souscategorie);
                    } else {
                        $isCej = false;
                    }


                    if ($isCej && !$arecategoriser) {

                        $cejs = $this->getDoctrine()
                            ->getRepository('AppBundle:ImputationControleCegj')
                            ->findBy(array('image' => $image));

                        if (count($cejs) > 0) {
                            $cej = $cejs[0];

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);
                        } else {
                            $cej = new ImputationControleCegj();
                            $cej->setImage($image);

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);

                            $em->persist($cej);
                        }
                    }

                    $em->flush();


                    break;

                case 0:


                    $separations = $this->getDoctrine()
                        ->getRepository('AppBundle:Separation')
                        ->findBy(array('image' => $image));

                    if (count($separations) > 0) {
                        /** @var Separation $separation */
                        $separation = $separations[0];

                        if (null !== $separation->getSoussouscategorie()) {
                            $oldSoussouscategorie = $separation->getSoussouscategorie();
                        } elseif (null !== $separation->getSouscategorie()) {
                            $oldSouscategorie = $separation->getSouscategorie();
                        } elseif (null !== $separation->getCategorie()) {
                            $oldCategorie = $separation->getCategorie();
                        }

                        $separation->setCategorie($categorie);
                        $separation->setSouscategorie($souscategorie);
                        $separation->setSoussouscategorie($soussouscategorie);

                    } else {
                        $isCej = false;
                    }

                    if ($isCej && !$arecategoriser) {

                        $saisie1s = $this->getDoctrine()
                            ->getRepository('AppBundle:Saisie1')
                            ->findBy(array('image' => $image));

                        if (count($saisie1s) === 0) {
                            $saisie1 = new Saisie1();
                            $saisie1->setImage($image);
                            $em->persist($saisie1);
                        }

                        $cejs = $this->getDoctrine()
                            ->getRepository('AppBundle:Saisie1Cegj')
                            ->findBy(array('image' => $image));

                        if (count($cejs) > 0) {
                            $cej = $cejs[0];

                            $cej->setImage($image);

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
                        } else {

                            $cej = new Saisie1Cegj();
                            $cej->setImage($image);

                            $cej->setDescription($description);
                            $cej->setEntite1($entite1);
                            $cej->setEntite2($entite2);
//                            $cej->setEntite3($entite3);
                            $cej->setDatePiece($datePiece);
                            $cej->setPeriodeDebut($periodeDebut);
                            $cej->setPeriodeFin($periodeFin);
                            $cej->setCommentaire($commentaire);
//                        $cej->setEntite4($entite4);

                            $em->persist($cej);
                        }

                        //Ovaina ny statut an'ilay image
                        $image->setSaisie1(2);

                    }
                    $em->flush();

                    break;
            }


            if ($etape !== 0) {
                $separations = $this->getDoctrine()
                    ->getRepository('AppBundle:Separation')
                    ->findBy(array('image' => $image));

                if (count($separations) > 0) {
                    $separation = $separations[0];

                    $separation->setCategorie($categorie);
                    $separation->setSouscategorie($souscategorie);
                    $separation->setSoussouscategorie($soussouscategorie);

                    $em->flush();
                }
            }

            if(null !== $oldSoussouscategorie){
                $oldCategorie = $oldSoussouscategorie->getSouscategorie()
                    ->getCategorie();
            }
            else if(null !== $oldSouscategorie){
                $oldCategorie = $oldSouscategorie->getCategorie();
            }

            $sameCat = true;
            if($oldCategorie !== $categorie  && $oldCategorie !== null) {
                $sameCat = false;
                if (null !== $dossier){
                    if(null !== $categorie) {
                        $treeId = array('dossierId' => $dossier->getId(), 'categorie' => $categorie->getCode());
                    }
                    if(null !== $oldCategorie){
                        $oldTreeId = array('dossierId' => $dossier->getId(), 'categorie' => $oldCategorie->getCode());
                    }
                }
            }

            //Enregistrement log
            $log = new LogCegj();
            $log->setImage($image);
            $log->setUtilisateur($this->getUser());
            $log->setDate(new \DateTime());
            $log->setEtape($etape);
            $canPersist = false;



            if($soussouscategorie !== $oldSoussouscategorie){
                $log->setOldSoussouscategorie($oldSoussouscategorie);
                $log->setNewSoussouscategorie($soussouscategorie);
                $canPersist = true;
            }

            if($souscategorie !== $oldSouscategorie){
                $log->setOldSouscategorie($oldSouscategorie);
                $log->setNewSouscategorie($souscategorie);
                $canPersist = true;
            }

            if($categorie !== $oldCategorie){
                $log->setOldCategorie($oldCategorie);
                $log->setNewCategorie($categorie);
                $canPersist = true;
            }


            if($canPersist){

                if($arecategoriser){
                    $this->setArecategoriser($image);
                }

                $em->persist($log);
                $em->flush();
            }



            $response = array('type' => 'success', 'action' => 'add',
                'id' => $image->getId(),
                'treeId' => $treeId,
                'oldTreeId' => $oldTreeId,
                'sameCat' => $sameCat,
                'catLib' => $catLib,
                'sCatLib' => $sCatLib,
                'ssCatLib' => $ssCatLib,
                'description' => $description,
                'datePiece' => ($datePiece == null) ? '' : $datePiece->format('d/m/Y'),
                'ec1' => $entite1,
                'ec2' => $entite2);

            return new JsonResponse($response);


        }
        throw new AccessDeniedHttpException('Accès refusé');
    }



    public function categorieSaveAction(Request $request){
        if($request->isXmlHttpRequest()){
            $post = $request->request;

            $imageid = Boost::deboost($post->get('imageid'), $this);

            /** @var Image $image */
            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find($imageid);


            $categorieid = $post->get('categorieid');
            $souscategorieid = $post->get('souscategorieid');
            $soussouscategorieid = $post->get('soussouscategorieid');

            $categorie = null;

            if($categorieid !== null) {
                $categorie = $this->getDoctrine()
                    ->getRepository('AppBundle:Categorie')
                    ->find($categorieid);
            }

            $souscategorie = null;
            if($souscategorieid !== null) {
                $souscategorie = $this->getDoctrine()
                    ->getRepository('AppBundle:Souscategorie')
                    ->find($souscategorieid);
            }

            $soussouscategorie = null;
            if($souscategorieid !== null) {
                $soussouscategorie = $this->getDoctrine()
                    ->getRepository('AppBundle:Soussouscategorie')
                    ->find($soussouscategorieid);
            }

            if(null===$image){
                $response = array('type' => 'error', 'action' => 'edit');
                return new JsonResponse($response);
            }

            $dossier = $image->getLot()->getDossier();

            $tvaSaisieEntities =  ['TvaSaisie1', 'TvaSaisie2','TvaSaisieControle'];
            $saisieEntities = ['Saisie1', 'Saisie2', 'SaisieControle'];
            $imputationEntities = ['Imputation', 'ImputationControle'];
            $TvamputationEntities = ['TvaImputation','TvaImputationControle'];

            /** @var Separation $separation */
            $separation = $this->getDoctrine()
                ->getRepository('AppBundle:Separation')
                ->getSeparationByImage($image);

            $oldcategorie = null;
            $oldSouscategorie = null;
            $oldSoussouscategorie = null;
            if($separation !== null){
                $oldcategorie = $separation->getCategorie();
                $oldSouscategorie = $separation->getSouscategorie();
                $oldSoussouscategorie = $separation->getSoussouscategorie();
            }

            $sameCat = true;

            if($soussouscategorie !== null){
                if($oldSoussouscategorie !== $soussouscategorie){
                    $sameCat = false;
                }
            }

            elseif($souscategorie !== null) {
                if ($oldSouscategorie !== $souscategorie) {
                    $sameCat = false;
                }
            }
            elseif($oldcategorie !== $categorie){
                $sameCat = false;
            }

            $oldTreeId = [];
            $treeId = [];

            if(null !== $categorie) {
                $treeId = ['dossierId' => $dossier->getId(),
                    'categorie' => $categorie->getCode()
                ];
            }

            if(null !== $separation){
                if($separation->getCategorie() !== null) {
                    $oldTreeId = [
                        'dossierId' => $dossier->getId(),
                        'categorie' => $separation->getCategorie()->getCode()
                    ];
                }
            }

            if(!$sameCat){

                $em = $this->getDoctrine()
                    ->getManager();

                foreach ($saisieEntities as $saisieEntity){
                    /** @var Saisie1[] $saisies */
                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:'.$saisieEntity)
                        ->findBy(['image' => $image]);

                    foreach ($saisies as $saisie){
                        $saisie->setSoussouscategorie($soussouscategorie);
                    }
                }

                foreach ($tvaSaisieEntities as $tvaSaisieEntity){
                    /** @var TvaSaisie1[] $tvaSaisies */
                    $tvaSaisies = $this->getDoctrine()
                        ->getRepository('AppBundle:'.$tvaSaisieEntity)
                        ->findBy(['image' => $image]);

                    foreach ($tvaSaisies as $tvaSaisie){
                        $tvaSaisie->setSoussouscategorie($soussouscategorie);
                    }
                }

                foreach ($imputationEntities as $imputationEntity){
                    $imputations = $this->getDoctrine()
                        ->getRepository('AppBundle:'.$imputationEntity)
                        ->findBy(['image' => $image]);

                    foreach ($imputations as $imputation){
                        $imputation->setSoussouscategorie($soussouscategorie);
                        $imputation->setSouscategorie($souscategorie);
                    }

                }
                foreach ($TvamputationEntities as $tvamputationEntity){
                    $tvaImputations = $this->getDoctrine()
                        ->getRepository('AppBundle:'.$tvamputationEntity)
                        ->findBy(['image' => $image]);
                    /** @var TvaImputation $tvaImputation */
                    foreach ($tvaImputations as $tvaImputation){
                        $tvaImputation->setSoussouscategorie($soussouscategorie);
                        $tvaImputation->setSouscategorie($souscategorie);
                    }

                }

                if($separation !== null) {
                    $separation->setCategorie($categorie);
                    $separation->setSouscategorie($souscategorie);
                    $separation->setSoussouscategorie($soussouscategorie);
                }

                $historique = new HistoriqueCategorie();
                $historique->setCategorie($oldcategorie);
                $historique->setSouscategorie($oldSouscategorie);
                $historique->setSoussouscategorie($oldSoussouscategorie);
                $historique->setUtilisateur($this->getUser());
                $historique->setImage($image);
                $historique->setDateModification(new DateTime('now'));
                $historique->setMotif('CLIENT-FOURNISSEUR');

                $em->persist($historique);

                $em->flush();

            }

            $catLib = ($categorie === null) ? '' : $categorie->getLibelleNew();
            $sCatLib = ($souscategorie === null) ? '' : $souscategorie->getLibelleNew();

            $response = [
                'type' => 'success',
                'action' => 'edit',
                'message' => 'modification effectuée',
                'id' => $image->getId(),
                'treeId' => $treeId,
                'oldTreeId' => $oldTreeId,
                'sameCat' => $sameCat,
                'catLib' => $catLib,
                'sCatLib' => $sCatLib
            ];

            return new JsonResponse($response);


        }
        throw new AccessDeniedHttpException('Accès refusé');
    }



    public function setArecategoriser(Image $image){
        $em = $this->getDoctrine()
            ->getManager();

        $separations = $this->getDoctrine()
            ->getRepository('AppBundle:Separation')
            ->findBy(array('image' => $image));

        if(count($separations) > 0){
            $separation = $separations[0];
            $separation->setSouscategorie(null);
            $separation->setSoussouscategorie(null);
            $separation->setCategorie($this->getDoctrine()
            ->getRepository('AppBundle:Categorie')
            ->find(41));

            $em->flush();
        }


        $saisie1s = $this->getDoctrine()
            ->getRepository('AppBundle:Saisie1')
            ->findBy(array('image' => $image));

        if(count($saisie1s) > 0){
            $saisie1 = $saisie1s[0];
            $em->remove($saisie1);
        }

        $saisie2s = $this->getDoctrine()
            ->getRepository('AppBundle:Saisie2')
            ->findBy(array('image' => $image));

        if(count($saisie2s) > 0){
            $saisie = $saisie2s[0];
            $em->remove($saisie);
        }

        $controles = $this->getDoctrine()
            ->getRepository('AppBundle:SaisieControle')
            ->findBy(array('image' => $image));

        if(count($controles) > 0){
            $controle = $controles[0];
            $em->remove($controle);
        }

        $imputations = $this->getDoctrine()
            ->getRepository('AppBundle:Imputation')
            ->findBy(array('image' => $image));

        if(count($imputations) > 0){
            $imputation = $imputations[0];
            $em->remove($imputation);
        }

        $imputationControles = $this->getDoctrine()
            ->getRepository('AppBundle:ImputationControle')
            ->findBy(array('image' => $image));

        if(count($imputationControles) > 0){
            $imputationControle = $imputationControles[0];
            $em->remove($imputationControle);
        }

        $image->setSaisie1(0);
        $image->setSaisie2(0);
        $image->setCtrlSaisie(0);
        $image->setImputation(0);
        $image->setCtrlImputation(0);


        /** @var ImageATraiter[] $imageTraiters */
        $imageTraiters = $this->getDoctrine()
            ->getRepository('AppBundle:ImageATraiter')
            ->findBy(array('image' => $image));

        if(count($imageTraiters)){
            $imageTraiter = $imageTraiters[0];
            $imageTraiter->setStatus(2);
            $imageTraiter->setSaisie1(0);
            $imageTraiter->setSaisie2(0);
        }

        $em->flush();



    }


    public function ecritureCEGJAction(Request $request){
        if($request->isXmlHttpRequest()){

            $post = $request->request;

            $imageId = Boost::deboost($post->get('imageId'),$this);



            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find($imageId);


            $dossier = $image->getLot()->getDossier();


            /** @var Pcc[] $pccs */
            $pccs = $this->getDoctrine()
                ->getRepository('AppBundle:Pcc')
                ->getPccByDossierLike($dossier, array(''));


            return $this->render('ConsultationPieceBundle:Default:ecritureEditCEGJ.html.twig',
                array('pccs' => $pccs));

        }
        else{
            throw  new AccessDeniedHttpException();
        }
    }

    public function dateScanAction(Request $request){
        if(!$request->isXmlHttpRequest())
            throw new AccessDeniedHttpException('Accès refusé');

        $get = $request->query;

        $dossierid = $get->get('dossier');
        if($dossierid === '')
            $dossierid = -1;

        $exerice = $get->get('exercice');

        $clientid = $get->get('client');
        $clientid = Boost::deboost($clientid, $this);
        $siteid = $get->get('site');
        $siteid = Boost::deboost($siteid, $this);

        $dateScans = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->getDateScanByCSDExercice($clientid, $siteid, $dossierid, $exerice);

        return $this->render('@ConsultationPiece/Default/dateScanOptions.html.twig', ['datescans' => $dateScans]);
    }


    function getOvhPath(Image $image)
    {
        $dossier = $image->getLot()->getDossier();
        $client = $dossier->getSite()->getClient();

        $file =
            $client->getNom() . '/' .
            $dossier->getNom() . '/' .
            $image->getExercice() . '/' .
            $image->getLot()->getDateScan()->format('Y-m-d') . '/' .
            $image->getLot()->getLot() . '/' .
            $image->getNom() . '.' . $image->getExtImage();

        if($client->getId() === 525) {
            $cers = ['CER_CARCASSONNE', 'CER_NARBONNE', 'CER_PEZENAS', 'CERMIDI_MONTPELLIER'];

            $conn_id = ftp_connect("ns384250.ovh.net");
            $login_result = ftp_login($conn_id, "picdataimage", "^aW63wo9");

            foreach ($cers as $cer){
                $file = '/'.$cer.'/'.
                    $dossier->getNom() . '/' .
                    $image->getExercice() . '/' .
                    $image->getLot()->getDateScan()->format('Y-m-d') . '/' .
                    $image->getLot()->getLot() . '/' .
                    $image->getNom() . '.' . $image->getExtImage();

                $res = ftp_size($conn_id, $file);

                if ($res != -1) {
                    ftp_close($conn_id);
                   return $file;
                }
            }
            ftp_close($conn_id);
        }

        return $file;
    }








}