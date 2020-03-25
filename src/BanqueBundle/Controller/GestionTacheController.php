<?php
/**
 * Created by PhpStorm.
 * User: Dinoh
 * Date: 26/07/2019
 * Time: 15:32
 */
namespace BanqueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Controller\Boost;

class GestionTacheController extends Controller
{
    public function indexAction()
    {
        return $this->render('BanqueBundle:GestionTache:index.html.twig');
    }

    public function getListAction(Request $request)
    {
    	if ($request->isXmlHttpRequest()) {
            if ($request->getMethod() == 'POST') {
                $param = array(); 
                $post = $request->request;
                $client = $post->get('client');
                $client = Boost::deboost($client, $this);
                $dossier = $post->get('dossier');
                $param['dossier'][] = Boost::deboost($dossier, $this);
                $param['exercice'] = $request->request->get('exercice');

                $selectedClient = $this->getDoctrine()
                                       ->getRepository('AppBundle:Client')
                                       ->find($client);
                $param['client'][] = $selectedClient->getId();

                $sitImages = $this->getDoctrine()
                                  ->getRepository('AppBundle:Image')
                                  ->getListeImpute($param);

                $imputees = $this->getDetailImputees($sitImages['imputees'], $param);
                $rows = [];
                $liste = [];
                foreach ( $imputees as $key => $imputee ) {
                    $rows[] = [
                        'id' => $imputee['banque_compte_id'],
                        'cell' => [
                            't-client' => $imputee['clients'],
                            't-dossier' => $imputee['dossier'],
                            't_statut' => $imputee['dossier_status'],
                            't-tva' => $imputee['regime_tva'],
                            't-banque' => $imputee['banque'],
                            't-compte' => $imputee['comptes'],
                            't_ecart' => $imputee['ecart'],
                            't_rb' => $imputee['m'],
                            't_rb2' => $imputee['importe'],
                            't_ob' => $imputee['ob'],
                            't-total' => $imputee['nb_r'],
                            't-lettre' => $imputee['nb_lettre'],
                            't-clef' => $imputee['nb_clef'],
                            't_alettre' => $imputee['alettrer'],
                            't-piece' => $imputee['nb_pc_manquant'],
                            't-cheque' => $imputee['chq_inconnu'],
                            't-rapproche' => $imputee['nbr_rapproche'],
                            't-priorite' => $imputee['prio'],
                            't-acontroler' => $imputee['acontroler'],
                            't-ech' => $imputee['ech'],
                            't-data-ob-m' => $imputee['data_ob_m'],
                            't-sb' => $imputee['sb'],
                            't-aucun-image' => ($imputee['m'] == "") ? "Auc." : $imputee['m'],
                            't_etat' => $imputee['etat'],
                            't-rest' =>$imputee['rest_valider'],
                            't-data-tache' => $imputee['tache']
                        ],
                    ];
                }
                $liste = [
                    'rows' => $rows,
                ];
                return new JsonResponse($liste);
            }
        }
    }

   public function getDetailImputees($data_imputees, $param)
    {
        $tab_imputees = array();
        $tab_key_mois = array();
        $tab_exist_comptes = array();
        $last_key = 0;
        $exercice = $param['exercice'];
        $dossier = $param['dossier'];
        $client = $param['client'];
        $param['periode'] = 4; // tout l'exercice
        $betweens = array();
        $clientEntity = $this->getDoctrine()
                       ->getRepository('AppBundle:Client')
                       ->find($client[0]);

        $prioriteParam =  $this->getDoctrine()
                               ->getRepository('AppBundle:PrioriteParam')
                               ->findAll();
        $taches = null;
        $periodeTache = new \DateTime();
        if($dossier == 0){
            $taches = $this->getDoctrine()->getRepository('AppBundle:Tache')
                                          ->getTachesPourGestionTaches($param['dossier'], $periodeTache, true, true, true,
                                            true, true, $clientEntity);
        }

        foreach ($data_imputees as $key => $value) {
            if($taches == null || (!array_key_exists($value->dossier_id, $taches['taches'])))
                $taches = $this->getDoctrine()->getRepository('AppBundle:Tache')
                                          ->getTachesPourGestionTaches($value->dossier_id, $periodeTache, true, true, true,
                                            true, true, null);
            if(array_key_exists($value->dossier_id, $taches['taches'])){
                if (!empty($value->mois)) {
                    $tab_mois_manquant = explode(',', $value->mois);
                    $mois_manquant = str_replace(' ', '', $tab_mois_manquant);
                    //fin mois cloture
                    if ($value->cloture < 9) {
                        $debut_mois = ($exercice - 1) . '-0' . ($value->cloture + 1) . '-01';
                    } else if ($value->cloture >= 9 and $value->cloture < 12) {
                        $debut_mois = ($exercice - 1) . '-' . ($value->cloture + 1) . '-01';
                    } else {
                        $debut_mois = ($exercice) . '-01-01';
                    }
                    //debut mois cloture
                    if ($value->cloture < 10) {
                        $fin_mois = ($exercice) . '-0' . ($value->cloture) . '-01';
                    } else {
                        $fin_mois = ($exercice) . '-' . ($value->cloture) . '-01';
                    }

                    /*$tab_mois_cloture = $this->getBetweenDate($debut_mois, $fin_mois);*/

                    $k = array_key_exists($debut_mois . '-' . $fin_mois, $betweens);
                    if ($k) {
                        $tab_mois_cloture = $betweens[$debut_mois . '-' . $fin_mois];
                    } else{
                        $tab_mois_cloture = $this->getBetweenDate($debut_mois, $fin_mois);

                        $betweens[$debut_mois . '-' . $fin_mois] = $tab_mois_cloture;

                    }

                    $nb_m_mois_exist = false;
                    switch (count($mois_manquant)) {
                        case 0:
                            $nb_m_mois_exist = true;
                            $tab_imputees[$key]['m'] = 'M-1';
                            break;
                        case 1:
                            $tab_key_mois[$key] = array_intersect($tab_mois_cloture, $mois_manquant);
                            break;
                        case 2:
                            $tab_key_mois[$key] = array_intersect($tab_mois_cloture, $mois_manquant);
                            break;
                        case 3:
                            $tab_key_mois[$key] = array_intersect($tab_mois_cloture, $mois_manquant);
                            break;
                        case 12:
                            $nb_m_mois_exist = true;
                            //jerena aloha raha mis relevÃ© ihany le banque amin'ny alalan'ny dossier
                            $resReleves = $this->getDoctrine()
                                               ->getRepository('AppBundle:Image')
                                               ->getInfoReleveByDossier($value->banque_compte_id, $exercice);
                            $tab_imputees[$key]['m'] = (count($resReleves) > 0) ? 'Inc.' : 'Auc.';
                            break;
                        default:
                            $nb_m_mois_exist = true;
                            $tab_imputees[$key]['m'] = 'Inc.';
                            break;
                    }

                    if (!$nb_m_mois_exist) {
                        $min = 13;
                        $now = new \DateTime();
                        foreach ($tab_key_mois[$key] as $key_m => $key_mois_m) {
                            if ($key_m < $min) {
                                $min = $key_m;
                            }
                        }
                        //Jerena aloha raha misy tsy ampy eo ampovoany
                        $continue = true;
                        $lastIndex = -1;
                        foreach ($tab_key_mois[$key] as $k => $v){
                            if($lastIndex === -1){
                                $lastIndex = $k;
                                continue;
                            }
                            if($lastIndex+1 !== $k){
                                $continue = false;
                                break;
                            }
                            else{
                                $lastIndex = $k;
                            }
                        }

                        if($continue) {
                            if (intval($exercice) < $now->format('Y')) {
                                switch ($min) {
                                    case 11:
                                        $tab_imputees[$key]['m'] = 'M-1';
                                        break;
                                    case 10:
                                        $tab_imputees[$key]['m'] = 'M-1';
                                        break;
                                    case 9:
                                        $tab_imputees[$key]['m'] = 'M-2';
                                        break;
                                    default:
                                        $tab_imputees[$key]['m'] = 'Inc.';
                                        break;
                                }
                            } else {
                                if (array_key_exists($min, $tab_key_mois[$key])){
                                    $now = new \DateTime();
                                    $yearNow = $now->format('Y');
                                    $monthNow = $now->format('m');
                                    $dateNow = intval($now->format('d'));
                                    $datetime = \DateTime::createFromFormat('Y-m-d', $tab_key_mois[$key][$min] . "-01");
                                    $interval = $now->diff($datetime);
                                    $diff = $interval->m + 1;
                                    if($dateNow <= 6 ){
                                        $diff = $interval->m;
                                    }

                                    if ($diff === 0) {
                                        $tab_imputees[$key]['m'] = 'M-1';
                                    } else if ($diff > 0) {
                                        $tab_imputees[$key]['m'] = 'M-' . $diff;
                                    } else {
                                        $tab_imputees[$key]['m'] = 'Inc.';
                                    }
                                }else{
                                    $tab_imputees[$key]['m'] = 'Inc.';
                                }
                            }
                        }
                        else{
                            $tab_imputees[$key]['m'] = 'Inc.';
                        }
                    }
                }
                else {
                    $tab_imputees[$key]['m'] = 'M-1';
                }

                $regimeTva = trim($value->regime_tva);
                $abrevRegimeTva = '';
                if($regimeTva !== ''){
                    $abrevs = explode(' ',str_replace(['-', '_'],' ',$regimeTva));
                    foreach ($abrevs as $abrev){
                        $abrevRegimeTva .= strtoupper($abrev[0]);
                    }

                }

                $dossierStatus = '';
                if($value->status === 1){
                    $dossierStatus = 'Actif';
                } else if($value->status === 2) {
                    $dossierStatus = 'Suspendu';
                }else if($value->status === 3){
                    $dossierStatus = 'Radié';
                }

                $tab_imputees[$key]['clients'] = $value->clients;
                $tab_imputees[$key]['dossier'] = $value->dossier;
                $tab_imputees[$key]['banque'] = $value->banque;
                $tab_imputees[$key]['nb_r'] = $value->nb_r;
                $nbr_rapproche = ($value->nb_r != 0) ? (($value->nb_lettre + $value->nb_clef + $value->nb_ecriture_change) * 100) / $value->nb_r : 0;
                $nbr_pc_manquant = ($value->nb_r != 0) ? ($value->nb_r - ($value->nb_lettre + $value->nb_clef)) : 0;
                $tab_imputees[$key]['nbr_rapproche'] = $this->ifNull($nbr_rapproche);
                $tab_imputees[$key]['nb_pc_manquant'] = $this->ifNull($nbr_pc_manquant);
                $tab_imputees[$key]['nb_clef'] = $this->ifNull($value->nb_clef);
                $tab_imputees[$key]['nb_lettre'] = $this->ifNull($value->nb_lettre);
                $tab_imputees[$key]['chq_inconnu'] = $this->ifNull($value->chq_inconnu);
                $tab_imputees[$key]['dossier_status'] = $dossierStatus;
                $tab_imputees[$key]['valider'] = $value->valider;
                $tab_imputees[$key]['banque_compte_id'] = $value->banque_compte_id;
                $tab_imputees[$key]['importe'] = 'Imp.';
                $tab_imputees[$key]['etat'] = $value->etat;
                $tab_imputees[$key]['rest_valider'] = $value->rest_valider;

                //date echeance tva
                $now = new \DateTime();
                $miniReel = ['01','04','07','10'];
                $reelSimplifie = ['07','12'];
                $month = $now->format('m');

                $remise = 0;
                $frbanc = 0;
                $lcr = 0;
                $vrmt = 0;
                $cartCredRel = 0;
                $cartDebRel = 0;

                $imageOb = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getListImageBanqueGestionTache($value->dossier_id, $exercice, 0, 8, -1, 1, -1);
                 foreach ($imageOb as $im) {
                    if($im->ctrl_saisie > 2 && $im->valider != 100){
                        $frbanc += 1;
                    }
                }
                $dataObMq[0]['nb'] = $frbanc;
                $dataObMq[0]['libelle'] = 'Frais Bancaire';

                $imageOb = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getListImageBanqueGestionTache($value->dossier_id, $exercice, 0, 5, -1, 1, -1);
                 foreach ($imageOb as $im) {
                    if($im->ctrl_saisie > 2 && $im->valider != 100){
                        $lcr += 1;
                    }
                }
                $dataObMq[1]['nb'] = $lcr;
                $dataObMq[1]['libelle'] = 'RelevÃ©  LCR';

                $imageOb = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getListImageBanqueGestionTache($value->dossier_id, $exercice, 0, 7, -1, 1, -1);
                 foreach ($imageOb as $im) {
                    if($im->ctrl_saisie > 2 && $im->valider != 100){
                        $remise += 1;
                    }
                }
                $dataObMq[2]['nb'] = $remise;
                $dataObMq[2]['libelle'] = 'Remise en banque';

                $imageOb = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getListImageBanqueGestionTache($value->dossier_id, $exercice, 0, 153, 1905, 1, -1);

                $imageObChq = $this->getDoctrine()
                            ->getRepository('AppBundle:Image')
                            ->getListImageBanqueGestionTache($value->dossier_id, $exercice, 0, 6, -1, 1, -1);
                $imageOb = array_merge($imageOb, $imageObChq);
                foreach ($imageOb as $im) {
                    if($im->ctrl_saisie > 2 && $im->valider != 100){
                        $vrmt += 1;
                    }
                }

                $dataObMq[3]['nb'] = $vrmt;
                $dataObMq[3]['libelle'] = 'VRT/CHQ EMIS';

               /* $souscategoriecarte = $this->getDoctrine()
                                           ->getRepository('AppBundle:Soussouscategorie')
                                           ->findBy(['souscategorie' => $this->getDoctrine()
                                                ->getRepository('AppBundle:Souscategorie')
                                                ->find(1)]
                                           );

                $carteKey = 4;
                foreach ($souscategoriecarte as $sscarte) {
                    $carteIndex = 0;
                    $imageOb = $this->getDoctrine()
                                    ->getRepository('AppBundle:Image')
                                    ->getListImageBanque($value->dossier_id, $exercice, 0, 1, $sscarte->getId(), 1, -1);
                    foreach ($imageOb as $im) {
                        if($im->ctrl_saisie > 2 && $im->valider != 100){
                            $carteIndex += 1;
                        }
                    }
                    $dataObMq[$carteKey]['nb'] = $carteIndex;
                    $dataObMq[$carteKey]['libelle'] = $sscarte->getLibelleNew();
                    $carteKey++;
                }*/

                $imageOb = $this->getDoctrine()
                                ->getRepository('AppBundle:Image')
                                ->getListImageBanqueGestionTache($value->dossier_id, $exercice, 0, 1, 1901, 1, -1);
                 foreach ($imageOb as $im) {
                    if($im->ctrl_saisie > 2 && $im->valider != 100){
                        $cartCredRel += 1;
                    }
                }
                $dataObMq[4]['nb'] = $cartCredRel;
                $dataObMq[4]['libelle'] = 'Cartes de crÃ©dit relevÃ©';

                $imageOb = $this->getDoctrine()
                                ->getRepository('AppBundle:Image')
                                ->getListImageBanqueGestionTache($value->dossier_id, $exercice, 0, 1, 2791, 1, -1);
                 foreach ($imageOb as $im) {
                    if($im->ctrl_saisie > 2 && $im->valider != 100){
                        $cartDebRel += 1;
                    }
                }
                $dataObMq[5]['nb'] = $cartDebRel;
                $dataObMq[5]['libelle'] = 'Cartes DÃ©bits tickets';

                $isOb = false;
                foreach ($dataObMq as $dataOb) {
                    if($dataOb['nb'] > 0){
                        $isOb = true;
                    }
                }
                $tab_imputees[$key]['ob'] = ($isOb) ? 'PB' : 'OK';
                $tab_imputees[$key]['data_ob_m'] = json_encode($dataObMq, true);

                /*$releve = $this->getDoctrine()
                               ->getRepository('AppBundle:Releve')
                               ->getReleveWithImageFlague($value->client_id, $value->dossier_id, $exercice);
                foreach ($releve as $r){
                    $alettrer = $this->getDoctrine()
                                     ->getRepository('AppBundle:TvaImputationControle')
                                     ->getNbAlettrer($value->dossier_id, $exercice, $r->montant);
                    if(count($alettrer) > 0) $nbAlettrer++;
                }
                */
                $tab_imputees[$key]['alettrer'] = ($value->a_lettrer) ? $value->a_lettrer : 0;

                $param['dossier'] = $value->dossier_id;
                $param['cas'] = 4;
                $param['exercice'] = $exercice;
                $nbImagesEncours = $this->getDoctrine()
                                        ->getRepository('AppBundle:Image')
                                        ->getNbImageEncours($param);

                $color = 0;

                if($tab_imputees[$key]['alettrer'] > 0)
                    $color = 1;

                if($nbImagesEncours > 0)
                    $color = 2;

                $tab_imputees[$key]['color'] = $color;

                //rb1 ok
                $param['dossier'] = $value->dossier_id;
                $dataRb1AC = $this->getDoctrine()
                                  ->getRepository('AppBundle:Image')
                                  ->getRb1AControler($param);
                $acontroler = $dataRb1AC['acontroler'];
                $tab_imputees[$key]['acontroler'] = $acontroler.'-'.$dataRb1AC['imgSaisieKo'];

                //ecart
                $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($value->banque_compte_id);
                $soldeDebut = $this->getDoctrine()->getRepository('AppBundle:Image')->getSoldes($banqueCompte,$exercice);
                $soldeFin = $this->getDoctrine()->getRepository('AppBundle:Image')->getSoldes($banqueCompte,$exercice,false);

                $mouvements = $this->getDoctrine()
                                    ->getRepository('AppBundle:Image')
                                    ->getMouvement($exercice, $value->banquecompte_id);

                $ecart = (float)($soldeFin - $soldeDebut - $mouvements);

                $tab_imputees[$key]['ecart'] = $ecart;

                $code = '';
                if($banqueCompte->getSourceImage() !== null){
                    if($banqueCompte->getSourceImage()->getSource() === 'SOBANK'){
                        $code = 'BI';
                    }
                }
                $tab_imputees[$key]['comptes'] = $value->comptes;
                $tab_imputees[$key]['sb'] = $code;
                $tab_imputees[$key]['tache'] = '';
                $datetimeTache = '';
                $tab_imputees[$key]['ech'] = '';
                $abrevTache = '';
                $tab_imputees[$key]['respons_tache'] = '';

                //tache
                if(array_key_exists($value->dossier_id, $taches['taches'])){
                    foreach ($taches['taches'][$value->dossier_id] as $k => $t) {
                      $abrevTache = explode('*', $t['titre2']);
                      if(!$t['expirer']) {
                        $dateTache = $t['datetime']->format('d-m');
                        $datetimeTache = $t['datetime'];
                        $titre2Tache = $t['titre2'];
                        $abrevTache = $abrevTache[0];
                        $statusTvaTache = $t['status'];
                        $expirerTache = $t['expirer'];
                        if($t['responsable'] === 0){
                            $reponsableTache = "Scriptura";
                        }else if($t['responsable'] == 1){
                            $reponsableTache = "Cabinet";
                        }else{
                            $reponsableTache = "Client";
                        }
                        break;
                      }else{
                        $expirerTache = $t['expirer'];
                        $dateTache = $t['datetime']->format('d-m');
                        $datetimeTache = $t['datetime'];
                        $titre2Tache = $t['titre2'];
                        $abrevTache = $abrevTache[0];
                        $statusTvaTache = $t['status'];
                        if($t['responsable'] === 0){
                            $reponsableTache = "Scriptura";
                        }else if($t['responsable'] == 1){
                            $reponsableTache = "Cabinet";
                        }else{
                            $reponsableTache = "Client";
                        }
                      }
                    }
                    $tab_imputees[$key]['tache'] = json_encode($taches['taches'][$value->dossier_id], true);
                    if(count($taches['taches'][$value->dossier_id]) > 1){
                        $libelleTache = (count($taches['taches'][$value->dossier_id] == 1)) ? 'TÃ¢che' : 'TÃ¢ches';
                        $valueInCellTache = $expirerTache.'='.$abrevTache.'='.count($taches['taches'][$value->dossier_id]).$libelleTache;
                    }else{
                        $valueInCellTache = $expirerTache.'='.$abrevTache.'='.false;
                    }
                    $tab_imputees[$key]['respons_tache'] = $reponsableTache;
                    $dateTacheTva = new \DateTime("now");
                    $dateTacheTvaYear = $dateTacheTva->format('y');
                    $dateTacheTvaMonth = $dateTacheTva->format('m');
                    $tachesDate = explode('-', $dateTache);
                    $isTvaNewYear = false;
                    if(count($tachesDate) > 1){
                        if((intval($dateTacheTvaMonth) > intval($tachesDate[1])) && ($statusTvaTache == 1 || $statusTvaTache == 2)){
                            $isTvaNewYear = true;
                            $dateTacheTvaYear++;
                        }
                    }
                    if($dateTache != '' && count($tachesDate) > 1){
                        if($tab_imputees[$key]['m'] != 'Auc.')
                            $tab_imputees[$key]['ech'] = $dateTacheTvaYear.'-'.$tachesDate[1].'-'.$tachesDate[0];
                    }
                }
                if($abrevTache == '') $valueInCellTache = false.'='.$abrevRegimeTva.'='.false;
                $tab_imputees[$key]['regime_tva'] = $valueInCellTache;

                //prioritÃ©
                $coulPriorte = "";
                $heure = 0;
                $month = $now->format('m');
                $tab_imputees[$key]['prio'] = '';
                if($tab_imputees[$key]['ech'] != '' && $datetimeTache != ''){
                    $dtime1 = $datetimeTache->format('Y').'-'.$datetimeTache->format('m-d');
                    if($isTvaNewYear)
                        $dtime1 = ($datetimeTache->format('Y') + 1).'-'.$datetimeTache->format('m-d');
                    $datetime1 = new \DateTime($dtime1); 
                    $datetime1->setTime(0,0); 
                    $datetime2 = new \DateTime("now"); 
                    $datetime2->setTime(0, 0);
                    $interval = 9000;
                    $datetime1 = $this->checkWeekend(new \DateTime($datetime1->format('Y-m-d')));
                    if ($datetime1 < $datetime2) {
                        $interval = 0;
                    } else {
                        /**  Calculer NB Heure entre dÃ©lai et date du jour */
                        $interval = $this->nbHeureTravail(clone $datetime2, clone $datetime1);
                    }
                    $dateDiff = date_diff($datetime1, $datetime2);
                    $nbday = $dateDiff->format("%a");
                    if ($interval > 0) {
                        foreach ($prioriteParam[1]->getParamValue() as $val) {
                            if ($interval >= $val['min'] && $interval <= $val['max']) {
                                $coulPriorte = $val['max'] .' '.$val['color'].' '.$nbday;
                            }
                        }
                    }else{
                        $coulPriorte = '10000000000000000 ExpirÃ© '.$nbday;
                    }
                    $tab_imputees[$key]['prio'] = $coulPriorte;
                }
            }
            $tab_exist_comptes[] = $value->numcompte;
            $last_key = $key;
        }
        $last_key++;
        $listSansImage = $this->getDoctrine()
                          ->getRepository('AppBundle:Image')
                          ->getListeImputeSansImage($client, $dossier, $tab_exist_comptes);
    
        foreach ($listSansImage as $key=>$value){
            $regimeTva = trim($value->regime_tva);
            $abrevRegimeTva = '';
            if($regimeTva !== ''){
                $abrevs = explode(' ',str_replace(['-', '_'],' ',$regimeTva));
                foreach ($abrevs as $abrev){
                    $abrevRegimeTva .= strtoupper($abrev[0]);
                }

            }

            $dossierStatus = '';
            if($value->status === 1){
                $dossierStatus = 'Actif';
            } else if($value->status === 2) {
                $dossierStatus = 'Suspendu';
            }else if($value->status === 3){
                $dossierStatus = 'Radié';
            }


            $tab_imputees[$last_key]['clients'] = $value->clients;
            $tab_imputees[$last_key]['dossier'] = $value->dossier;
            $tab_imputees[$last_key]['comptes'] = $value->comptes;
            $tab_imputees[$last_key]['banque'] = $value->banque;
            $tab_imputees[$last_key]['regime_tva'] = ' -'.$abrevRegimeTva.'- ';
            $tab_imputees[$last_key]['dossier_status'] = $dossierStatus;
            $tab_imputees[$last_key]['nb_r'] = 0;
            $tab_imputees[$last_key]['nbr_rapproche'] = 0;
            $tab_imputees[$last_key]['nb_pc_manquant'] = 0;
            $tab_imputees[$last_key]['nb_clef'] = 0;
            $tab_imputees[$last_key]['nb_lettre'] = 0;
            $tab_imputees[$last_key]['chq_inconnu'] = 0;
            $tab_imputees[$last_key]['valider'] = 0;
            $tab_imputees[$last_key]['banque_compte_id'] = $value->banque_compte_id;
            $tab_imputees[$last_key]['ecart'] = 0;
            $tab_imputees[$last_key]['prio'] = '';
            $tab_imputees[$last_key]['etat'] = '';
            $tab_imputees[$last_key]['tache'] = '';
            $tab_imputees[$last_key]['respons_tache'] = '';
            $tab_imputees[$last_key]['color'] = 0;
            $tab_imputees[$last_key]['rest_valider'] = 0;

            //alettrer
            /*foreach ($listeNonLettre as $v) $alettrer += $v->nb_non_lettre;
            foreach ($listeNonLettreBanque as $v) $alettrer += $v->nb;*/
            $tab_imputees[$last_key]['alettrer'] = 0;

            $code = '';
            if(!empty($value->banque_compte_id)){
                $tab_imputees[$last_key]['etat'] = ($value->etat) ? $value->etat : '';
                $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($value->banque_compte_id);
                if($banqueCompte->getSourceImage() !== null){
                    if($banqueCompte->getSourceImage()->getSource() === 'SOBANK'){
                        $code = 'BI';
                    }
                }
            }
            $tab_imputees[$last_key]['sb'] = $code;
            $tab_imputees[$last_key]['ech'] = "";
            if(!empty($value->comptes)) {
                $tab_imputees[$last_key]['m'] = 'Auc.';
                $tab_imputees[$last_key]['importe'] = 'Nn imp.';

                //date echeance tva
                $now = new \DateTime();
                $miniReel = ['01','04','07','10'];
                $reelSimplifie = ['07','12'];
                $month = $now->format('m');
                if($value->ech != null){
                    if($value->tva_mode != 2){
                        $dayWithMonth = $now->format('d-m');
                        if($value->tva_mode == 3 && ($abrevRegimeTva == 'MR' || $abrevRegimeTva == 'RN') && in_array($month, $miniReel)) { //mini reel ou réel normal
                            $tab_imputees[$last_key]['ech'] = $now->format('y').'-'.$month.'-'.$value->ech;
                        }else if($value->tva_mode == 0 && $abrevRegimeTva == 'RS' && (in_array($month, $reelSimplifie) || $dayWithMonth == '15-05')){ // réel simplifié
                            $tab_imputees[$last_key]['ech'] = $now->format('y').'-'.$month.'-'.$value->ech;
                        }else{
                            $tab_imputees[$last_key]['ech'] = '';
                        }
                    }else{
                        $tab_imputees[$last_key]['ech'] = $now->format('y').'-'.$month.'-'.$value->ech;
                    }
                }else{
                    $tab_imputees[$last_key]['ech'] = '';
                }

                //rb1 ok
                $param['dossier'] = $value->dossier_id;
                $dataRb1AC = $this->getDoctrine()
                                  ->getRepository('AppBundle:Image')
                                  ->getRb1AControler($param);
                $acontroler = $dataRb1AC['acontroler'];
                $tab_imputees[$last_key]['acontroler'] = $acontroler.'-'.$dataRb1AC['imgSaisieKo'];
            }else{
                $tab_imputees[$last_key]['m'] = '';
                $tab_imputees[$last_key]['importe'] = '';
                $tab_imputees[$last_key]['acontroler'] = '';
            }

            $tab_imputees[$last_key]['ob'] = '';
            $tab_imputees[$last_key]['data_ob_m'] = '';
            $last_key++;
        }
        return $tab_imputees;
    }

    public function getBetweenDate($start, $end)
    {
        $time1 = strtotime($start);
        $time2 = strtotime($end);
        $my = date('mY', $time2);
        $months = array(date('Y-m', $time1));
        while ($time1 < $time2) {
            $time1 = strtotime(date('Y-m', $time1) . ' +1 month');
            if (date('mY', $time1) != $my && ($time1 < $time2))
                $months[] = date('Y-m', $time1);
        }
        $months[] = date('Y-m', $time2);
        return $months;
    }

    public function ifNull($value,$null = 0)
    {
        $value = ($value) ? $value :  $null;

        return $value;
    }

    public function checkWeekend(\DateTime $date)
    {
        $tmp = clone $date;
        if ($tmp->format('w') == 6) {
            $tmp->add(new \DateInterval('P2D'));
        }
        if ($tmp->format('w') == 0) {
            $tmp->add(new \DateInterval('P1D'));
        }
        return $tmp;
    }

     public function nbHeureTravail(\DateTime $start, \DateTime $end)
    {
        $debut = new \DateTime($start->format('Y-m-d'));
        $fin = new \DateTime($end->format('Y-m-d'));
        $nbHeure = 0;
        $hours = [];
        $param = $this->getDoctrine()
                      ->getRepository('AppBundle:PrioriteParam')
                      ->findOneBy(array(
                        'paramName' => 'priorite_jour'
                      ));
//        {"weekday":1,"checked":true,"heure":8}
        if ($param) {
            foreach ($param->getParamValue() as $value) {
                $hours[$value['weekday']] = $value;
            }
        }

        while ($debut < $fin) {
            $weekday = $debut->format('N');
            if (isset($hours[$weekday]) && $hours[$weekday]['checked'] === true) {
                $nbHeure += intval($hours[$weekday]['heure']);
            }
            $debut->add(new \DateInterval('P1D'));
        }

        return $nbHeure;
    }
}
?>