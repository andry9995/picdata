<?php

/**
 * GeneralController
 *
 * @package Picdata
 *
 * @author Scriptura
 * @copyright Scriptura (c) 2019
 */

namespace GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GeneralBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;

class GeneralController extends DefaultController
{

    public $alpha      = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    public $occupation = '';

    public $o;

    public function getCellIndex($num, $init = false)
    {

        if ($init) {
            $this->occupation = '';
        }

        if ($this->occupation === '' ) {
            $this->occupation = 'A';
            return 'A' . $num ;
        } else {
            if (in_array($this->occupation, $this->alpha) && $this->occupation !== 'Z') {
                $in = array_search($this->occupation, $this->alpha);
                $this->occupation = $this->alpha[$in + 1];
                return $this->occupation . $num;
            } else{
                if ($this->occupation === 'Z') {
                    $this->occupation = 'AA';
                    return $this->occupation . $num;
                } else {
                    $first            = $this->occupation[0];
                    $second           = $this->occupation[1];
                    $in               = array_search($second, $this->alpha);
                    $this->occupation = $first . $this->alpha[$in + 1];
                    return $this->occupation . $num;
                }
            }
        }
    }

    public function indexAction()
    {

        $user       = $this->getUser();
        $repository = $this->loadRepository('Client');
        $clients    = $repository->getUserClients($user);

	    return $this->render('GeneralBundle:General:index.html.twig', array(
	    	'clients' => $clients
	    ));
	    
	}

    /**
     * Graphes Répartitions
     *
     * @param string $client
     * @param string $exercice
     *
     * @return JsonResponse 
     */
    public function repartionsAction($client, $exercice, $site)
    {
        $param['client']   = $client;
        $param['exercice'] = $exercice;
        $param['site'] = $site;
        $repository        = $this->loadRepository('Image');
        $user = $this->getUser();
        $result            = $repository->getRepartitions($param,$user);

        return $this->response($result);
    }


	/**
     * Tableau Details & Graphes
     *
     * @param string $client
     * @param string $dossier
     * @param string $exercice
     * @param integer $periode
     * @param string $perioddeb
     * @param string $periodfin
     * @param integer $typedate
     * @param integer $analyse
     * @param integer $tab
     * @param string $filtre_sd
     * @param string  $operateur_sd
     * @param integer $value_sd
     *
     * @return JsonResponse
     */
    public function generalImagesAction($client, $dossier, $exercice, $periode, $perioddeb, $periodfin, $typedate, $analyse, $tab, $filtre_sd = '', $operateur_sd = '', $value_sd = '', $site = 0)
	{

        $param     = array(
            'client'   => $client,
            'dossier'  => $dossier,
            'exercice' => $exercice,
            'periode'  => $periode,
            'analyse'  => $analyse,
            'typedate' => $typedate,
            'site'     => $site
        );

		switch ($periode) {
			case 1:
                $param['cas']        = 1;
                $periodNow           = new \DateTime();
                $param['aujourdhui'] = $periodNow->format('Y-m-d');
				break;
			
			case 2:
                $param['cas']     = 2;
                $periodeNow       = new \DateTime();
                $now              = clone $periodeNow;
                $oneWeek          = date_modify($periodeNow, "-7 days");
                $param['dateDeb'] = $oneWeek->format('Y-m-d');
                $param['dateFin'] = $now->format('Y-m-d');
				break;

			case 3:
                $param['cas']     = 3;
                $periodeNow       = new \DateTime();
                $now              = clone $periodeNow;
                $twoWeeks         = date_modify($periodeNow, "-7 days");
                $param['dateDeb'] = $twoWeeks->format('Y-m-d');
                $param['dateFin'] = $now->format('Y-m-d');
				break;

			case 4:
                $param['cas']     = 4;
                $periodeNow       = new \DateTime();
                $now              = clone $periodeNow;
                $oneMonth         = date_modify($periodeNow, "-1 months");
                $param['dateDeb'] = $oneMonth->format('Y-m-d');
                $param['dateFin'] = $now->format('Y-m-d');
				break;

			case 5:
                $param['cas'] = 5;
				break;

			case 6:
                $param['cas'] = 6;
                $debPeriode   = $perioddeb;
                $finPeriode   = $periodfin;
                if( (isset($debPeriode) && !is_null($debPeriode)) && (isset($finPeriode) && !is_null($finPeriode)) ) {
                    $param['dateDeb'] = $debPeriode;
                    $param['dateFin'] = $finPeriode;
                }
				break;
		}

		// Details
		if ($tab == 1) {
            $repository = $this->loadRepository('Image');
            $user = $this->getUser();
            $result     = $repository->getImagesRecues($param,$user);

            $images     = array();
            $images     = $this->formatData($result,$param);

            $paramFilter = array();

            $paramFilter['filtre_nb'] = $filtre_sd;
            $paramFilter['operateur_nb'] = $operateur_sd;
            $paramFilter['value_nb'] = $value_sd;

            $afterFilter = $this->ImagesFilter($images,$paramFilter);

            return $this->response($afterFilter);

		} else {

            $json = $this->getCourbeData($param);

            if ($analyse == 2)
                $json = $this->grapheCumul($json);

            $array            = array();
            $array['courbe']  = $json;
            $array['analyse'] = $analyse;

            return $this->response($array);
        }
 
	    
	}

    /**
     * Filtre sélection dossier
     * 
     * @param array $images
     * @param array $paramFilter
     *
     * @return array
     */
    public function ImagesFilter($images, $paramFilter)
    {

        $response  = array();
        $size      = count($images);

        if ($size >= 3) {
            $nbDossier = $size / 3;
        } else {
            $nbDossier = 0;
        }

        $total     = 0;
        $all       = $images[1]['totalN'];

        if ($paramFilter['filtre_nb'] != 0 && $paramFilter['operateur_nb'] != 0 && $paramFilter['value_nb'] != '') {

            $count = 0;

            for ($i=1; $i <  $size; $i = $i + 3) { 

                switch ($paramFilter['filtre_nb']) {
                    // Nombre
                    case 1:
                        switch ($paramFilter['operateur_nb']) {
                            //Egal
                            case 1:
                                if ($images[$i]['total'] == $paramFilter['value_nb'] ) {
                                    array_push($response, $images[$i - 1]);
                                    array_push($response, $images[$i]);
                                    array_push($response, $images[$i + 1]);
                                    $count++;
                                    $total += $images[$i]['total'];
                                }
                                break;
                            //Supérieur
                            case 2:
                                if ($images[$i]['total'] > $paramFilter['value_nb'] ) {
                                    array_push($response, $images[$i - 1]);
                                    array_push($response, $images[$i]);
                                    array_push($response, $images[$i + 1]);
                                    $count++;
                                    $total += $images[$i]['total'];
                                }
                                break;
                            //Inférieur
                            case 3:
                                if ($images[$i]['total'] < $paramFilter['value_nb'] ) {
                                    array_push($response, $images[$i - 1]);
                                    array_push($response, $images[$i]);
                                    array_push($response, $images[$i + 1]);
                                    $count++;
                                    $total += $images[$i]['total'];
                                }
                                break;
                        }
                        break;
                    // Pourcentage
                    case 2:
                        $percentageN1 = round( intval($images[$i + 1]['total']) * intval($paramFilter['value_nb']) / 100 );

                        switch ($paramFilter['operateur_nb']) {
                            // Egal
                            case 1:
                                if ($images[$i]['total'] == $percentageN1 ) {
                                    array_push($response, $images[$i - 1]);
                                    array_push($response, $images[$i]);
                                    array_push($response, $images[$i + 1]);
                                    $count++;
                                    $total += $images[$i]['total'];
                                }
                                break;
                            // Supérieur
                            case 2:
                                if ($images[$i]['total'] > $percentageN1 ) {
                                    array_push($response, $images[$i - 1]);
                                    array_push($response, $images[$i]);
                                    array_push($response, $images[$i + 1]);
                                    $count++;
                                    $total += $images[$i]['total'];
                                }
                                break;
                            // Inférieur
                            case 3:
                                if ($images[$i]['total'] < $percentageN1 ) {
                                    array_push($response, $images[$i - 1]);
                                    array_push($response, $images[$i]);
                                    array_push($response, $images[$i + 1]);
                                    $count++;
                                    $total += $images[$i]['total'];
                                }
                                break;
                        }
                        break;
                }

            }

            $result = array(
                'data'    => $response,
                'count'   => $count,
                'percent' => number_format(($total * 100) / $all,2) . ' %'
            );

            return $result;
        }
        else{

            $result = array(
                'data'    => $images,
                'count'   => $nbDossier,
                'percent' => false
            );

            return $result;
        }
    
    }

    /**
     * Calcul des données du graphe en cumul
     *
     * @param array $json
     *
     * @return array
     */
    public function grapheCumul($json)
    {

        for ($i=1; $i < 27; $i++) { 
            $json[0]['data'][$i] += $json[0]['data'][$i - 1];
            $json[1]['data'][$i] += $json[1]['data'][$i - 1];
            $json[2]['data'][$i] += $json[2]['data'][$i - 1];
        }

        return $json;
    }

    /**
     * Réccupération des données pour le graphe
     *
     * @param array $param
     *
     * @return array
     */
    public function getCourbeData($param)
    {
        $result = array();

        $result[0] = $this->prepareToGraph($param);

        $param['exercice'] = $param['exercice'] - 1;

        $result[1] = $this->prepareToGraph($param,1);

        $param['exercice'] = $param['exercice'] - 1;

        $result[2] = $this->prepareToGraph($param);

        return $result;
    }

    /**
     * Initialisation des mois
     *
     * @param array $array
     *
     * @return array
     */
    public function initializeMonthKey()
    {

        $array = array();

        for ($i=0; $i <= 26 ; $i++) { 
            $array[$i] = 0;
        }

        return $array;
    }

    /**
     * Traitement des données pour le graphe
     *
     * @param array $result
     * @param array param
     * @param string $exercice
     * @param array $data
     *
     * @return array
     */
    public function prepareToGraph($param, $get = 2)
    {

        $betweens = array();

        $repository = $this->loadRepository('Image');
        $user = $this->getUser();
        $result     = $repository->getImagesRecues($param,$user);

        $images = array();

        $data = $this->initializeMonthKey();

        $test = 0;

        foreach ($result as $key => $value) {

            /**if ($param['typedate'] == 2) {
                $year_scan     = explode('-', $value->date_piece)[0];
                $annee_cloture = explode('-', $value->date_cloture)[0];

                // if ($value->debut_activite && $value->date_cloture && $annee_cloture == $param['exercice']) {
                //     $debutFin = array(
                //         'start' => $value->debut_activite,
                //         'end'   => $value->date_cloture
                //     );
                // }
                // else{
                //     $debutFin = $this->beginEnd($param['exercice'],$value->cloture);
                // }

                if (date("Y") == $param['exercice']) {
                    $debutFin = $this->get24Mois($param['exercice'],2);
                } else {
                    $debutFin = $this->get24Mois($param['exercice'],3);
                }

                // $debutFin = $this->get24Mois($param['exercice']);

                // $debutFin  = $this->beginEnd($param['exercice'],$value->cloture);

            }
            else{
                $year_scan = explode('-', $value->date_scan)[0];
                $debutFin  = $this->beginEnd($param['exercice'],$value->cloture);
            }**/

                // $end             = new \DateTime($debutFin['end']);
                // $end->add(new \DateInterval('P12M'));
                // $debutFin['end'] = $end->format('Y-m-d');

                if (date("Y") == $param['exercice']) {
                    $debutFin = $this->get24Mois($param['exercice'],2);
                } else {
                    $debutFin = $this->get24Mois($param['exercice'],3);
                }

                $debutFin = $this->get24Mois($param['exercice'],3);

                $k = array_key_exists($debutFin['start'] . '-' . $debutFin['end'], $betweens);

                if ($k) {
                    $between = $betweens[$debutFin['start'] . '-' . $debutFin['end']];
                } else{
                    $between         = $this->getBetweenDate($debutFin['start'], $debutFin['end']);
                    
                    $betweens[$debutFin['start'] . '-' . $debutFin['end']] = $between;

                }

                $monthKey        = 0;

                if ($param['typedate'] == 1) {
                    $year_scan = explode('-', $value->date_scan)[0];
                    if (in_array($value->date_scan, $between)) {
                        $monthKey = intval(explode('-', $value->date_scan)[1]);

                            if ($year_scan == $param['exercice']) {
                                $data[($monthKey + 6) + 1] += $value->nb;
                            }

                            if ($year_scan == $param['exercice'] - 1) {
                                if($monthKey >= 6){
                                    $data[($monthKey - 6) + 1] += $value->nb;
                                }
                                else{
                                    $data[0] += $value->nb;
                                }
                            }

                            if ($year_scan < $param['exercice'] - 1) {
                                $data[0] += $value->nb;
                            }

                            if ($year_scan == $param['exercice'] + 1) {
                                 if($monthKey <= 6){
                                    $data[($monthKey + 18) + 1] += $value->nb;
                                }
                                if($monthKey > 6){
                                    $data[26] += $value->nb;
                                }
                            }

                            if ($year_scan > $param['exercice'] + 1) {
                                $data[26] += $value->nb;
                            }

                    }
                }
                else{
                    $year_scan     = intval(explode('-', $value->date_piece)[0]);

                    
                    if (in_array($value->date_piece, $between)) {

                        $monthKey = intval(explode('-', $value->date_piece)[1]);

                            if ($year_scan == intval($param['exercice'])) {
                                $data[($monthKey + 6) + 1] += $value->nb;
                            }

                            if ($year_scan == intval($param['exercice']) - 1) {
                                if($monthKey >= 6){
                                    $data[($monthKey - 6) + 1] += $value->nb;
                                }
                                else{
                                    $data[0] += $value->nb;
                                }
                            }

                            if ($year_scan < intval($param['exercice']) - 1) {
                                $data[0] += $value->nb;
                            }

                            if ($year_scan == intval($param['exercice']) + 1) {
                                 if($monthKey <= 6){
                                    $data[($monthKey + 18) + 1] += $value->nb;
                                }
                                if($monthKey > 6){
                                    $data[26] += $value->nb;
                                }
                            }

                            if ($year_scan > intval($param['exercice']) + 1) {
                                $data[26] += $value->nb;
                            }
                    }
                }
        }

        $images = array(
            'data' => $data
        );

        return $images;

    }

    public function get24Mois($exercice, $nb = 1)
    {

        switch ($nb) {
            case 1:
                $start = $exercice . '-01-01';
                $end = new \DateTime($start);
                $end->add(new \DateInterval('P24M'));
                break;
            
            case 2:
                $last = intval($exercice) -1;
                $start = $last . '-01-01';
                $end = new \DateTime($start);
                $end->add(new \DateInterval('P24M'));
                break;
            case 3:
                $last = intval($exercice) -2;
                $start = $last . '-01-01';
                $end = new \DateTime($start);
                $end->add(new \DateInterval('P48M'));
                break;
            case 4:
                $last = intval($exercice);
                $start = $last . '-01-01';
                $end = new \DateTime($start);
                $end->add(new \DateInterval('P24M'));
                break;
        }

       

        return array(
            'start' => $start,
            'end' => $end->format('Y-m-d')
        );
    }

    public function getMoisInf($exercice)
    {

        $exercice = intval($exercice) - 2;
        $exercice2 = intval($exercice) + 2 - 1;

        $start = $exercice . '-01-01';

        $end = $exercice2 . '-12-01';

        return array(
            'start' =>$start,
            'end' => $end
        );

       
    }

	/**
     * Traitement des données pour le tableau Details
     *
     * @param array $result
     * @param array $param
     *
     * @return array
     */
    public function formatData($result, $param)
	{

        $betweens = array();

        $images  = array();
        $data    = array();
        $i       = 1;
        $all     = 0;
        $allPrev = 0;

        $isany = 0;

        $dataInf = array();

        $response = array();

        $keys = array();

        if (empty($result)) {

            $dossier = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->find($this->deboost($param['dossier']));

            // Réccupération N - 1
            if ($this->deboost($param['dossier']) != 0) {

                $annee_cloture = '';

                if ($dossier->getDateCloture() != '') {
                    $annee_cloture = explode('-', $dossier->getDateCloture()->format('Y-m-d'))[0];
                }

                if ($param['typedate'] == 2 && $dossier->getDateCloture() && $dossier->getDebutActivite() && $annee_cloture == $param['exercice']) {

                    $debutFin = array(
                        'start' => $dossier->getDebutActivite(),
                        'end' => $dossier->getDateCloture()
                    );

                    $end = new \DateTime($debutFin['end']);

                    $end->add(new \DateInterval('P12M'));

                    $debutFin['end'] = $end->format('Y-m-d');

                    $debutFin = $this->get24Mois($param['exercice']);

                    // $between = $this->getBetweenDate($debutFin);

                    $k = array_key_exists($debutFin['start'] . '-' . $debutFin['end'], $betweens);

                    if ($k) {
                        $between = $betweens[$debutFin['start'] . '-' . $debutFin['end']];
                    } else{
                        $between         = $this->getBetweenDate($debutFin['start'],$debutFin['end']);
                        
                        $betweens[$debutFin['start'] . '-' . $debutFin['end']] = $between;

                    }

                    $label = $this->getMonthLabel($between);
                }
                
                else {
                    $debutFin = $this->beginEnd($param['exercice'], $dossier->getCloture() );

                    $end = new \DateTime($debutFin['end']);

                    $end->add(new \DateInterval('P12M'));

                    $debutFin['end'] = $end->format('Y-m-d');

                    $debutFin = $this->get24Mois($param['exercice']);

                    // $between = $this->getBetweenDate($debutFin);

                    $k = array_key_exists($debutFin['start'] . '-' . $debutFin['end'], $betweens);

                    if ($k) {
                        $between = $betweens[$debutFin['start'] . '-' . $debutFin['end']];
                    } else{
                        $between         = $this->getBetweenDate($debutFin['start'],$debutFin['end']);
                        
                        $betweens[$debutFin['start'] . '-' . $debutFin['end']] = $between;

                    }

                    $label = $this->getMonthLabel($between);

                }

                $images[0] = $label;
                $images[0]['exercice'] = '';
                $images[0]['total'] = '';

                $images[1] = $this->initializeM($images[0],count($label));

                $images[1]['dossier'] = $dossier->getNom();
                $images[1]['exercice'] = 'N';
                $images[1]['total'] = 0;

                $paramPrev = array();

                $paramPrev = $param;

                $paramPrev['exercice'] = strval(intval($param['exercice']) - 1);

                $paramPrev['dossier'] = $param['dossier'];
                $paramPrev['client'] = $param['client'];

                $user = $this->getUser();

                $prev = $this->getDoctrine()
                        ->getRepository('AppBundle:Image')
                        ->getImagesRecues($paramPrev,$user);

                $imagesPrev = array();

                if (empty($prev)){

                    $imagesPrev[0]['dossier'] = $dossier->getNom();
                    $imagesPrev[0]['total'] = 0;
                    $imagesPrev[0]['exercice'] = 'N - 1';

                    $imagesPrev[0]['totalN'] = 0;
                    $imagimagesPreves[0]['totalNPrev'] = 0;

                    $imagesPrev[0] = $this->initializeM($imagesPrev[0],count($label));
                }
                else{

                    $imagesPrev = $this->formatDataPrev($prev,$paramPrev,count($label));
                }

                if (empty($imagesPrev)) {
                    // $imagesPrev[0]['client'] = $dossier->client;
                    $imagesPrev[0]['dossier'] = $dossier->getNom();
                    $imagesPrev[0]['total'] = 0;
                    $imagesPrev[0]['exercice'] = 'N - 1';

                    $imagesPrev[0]['totalN'] = 0;
                    $imagimagesPreves[0]['totalNPrev'] = 0;

                    $imagesPrev[0] = $this->initializeM($imagesPrev[0],count($label));
                }

                $images[2] = $imagesPrev[0];
            }

        
        } else {

        	foreach ($result as $key => $value) {
                if ($param['typedate'] == 2) {
                    $annee_cloture = explode('-', $value->date_cloture)[0];
                }

                $norm = true;

                if ($param['typedate'] == 2) {

                    // $norm              = false;
                    // $item              = array();
                    // $debutFin['start'] = $value->debut_activite;
                    // $debutFin['end']   = $value->date_cloture;
                    // $end               = new \DateTime($debutFin['end']);
                    // $end->add(new \DateInterval('P12M'));
                    // $debutFin['end']   = $end->format('Y-m-d');

                    $debutFin = $this->get24Mois($param['exercice']);
                    
                    $k = array_key_exists($debutFin['start'] . '-' . $debutFin['end'], $betweens);

                    if ($k) {
                        $moisCloture = $betweens[$debutFin['start'] . '-' . $debutFin['end']];
                    } else{
                        $moisCloture         = $this->getBetweenDate($debutFin['start'], $debutFin['end']);
                        
                        $betweens[$debutFin['start'] . '-' . $debutFin['end']] = $moisCloture;

                    }

                    $item['dossier']   = $value->dossier;
                    $item['nb']        = $value->nb;

                    $dfMoisInf = $this->getMoisInf($param['exercice']);

                    $moisInf = $this->getBetweenDate($dfMoisInf['start'], $dfMoisInf['end']);
                    
                    $keyMonth          = array_search($value->date_piece, $moisCloture);
                    if (!$keyMonth && strval($keyMonth) !== '0') {
                       $exist = array_search($value->date_piece, $moisInf);
                       if ($exist || strval($exist) === '0' ) {
                           $keyMonth = 24;
                       }

                    }

                    if ($keyMonth || strval($keyMonth) === '0') {

                        $m            = array();
                        $m[$keyMonth] = $value->nb;
                        $item['m']    = $m;
                        $not          = false;

                        if (!array_key_exists($value->dossier, $data)) {
                            $not                          = true;
                            $data[$value->dossier]['key'] = $i;
                            $i += 3;
                            $isany += 1;
                        }

                        if (isset($data[$value->dossier]['m'][$keyMonth])) {
                                $data[$value->dossier]['m'][$keyMonth] += $value->nb;
                        } else{
                            $data[$value->dossier]['m'][$keyMonth] = $value->nb;
                        }

                        $index  = $data[$value->dossier]['key'];
                        $index2 = intval($index) + 1 ;
                        $index3 = intval($index) - 1 ;
                        $total  = $this->getTotal($data[$value->dossier]['m'] );

                        if (array_key_exists($index, $images)) {
                            $lastTotal = $images[$index]['total'];
                            $all = $all - $lastTotal + $total;
                        } else{
                            $all += $total;
                        }

                        $client  = $this->loadRepository('Client')
                                        ->find($value->client_id)
                                        ->getNom();
                        $dossier = $this->loadRepository('Dossier')
                                        ->find($value->dossier_id)
                                        ->getNom();

                        $images[$index3]               = $this->getMonthLabel($moisCloture);
                        $images[$index3]['client']     = $value->client;
                        $images[$index3]['dossier']    = $value->dossier;
                        $images[$index3]['total']      = '';
                        $images[$index3]['exercice']   = '';
                        $images[$index3]['totalN']     = 0;
                        $images[$index3]['totalNPrev'] = 0;
                        $images[$index]['client']      = $value->client;
                        $images[$index]['dossier']     = $value->dossier;
                        $images[$index]['total']       = $total;
                        $images[$index]['exercice']    = 'N';
                        $images[$index]                = $this->initializeM($images[$index],count($images[$index3]) - 6);
                        $images[$index]                = $this->pushM($data[$value->dossier]['m'],$images[$index],$param['analyse']);
                        $images[$index]['totalN']      = 0;
                        $images[$index]['totalNPrev']  = 0;

                        $user = $this->getUser();

                        if ($not == true) {
                            $paramPrev             = array();
                            $paramPrev             = $param;
                            $paramPrev['exercice'] = strval(intval($param['exercice']) - 1);
                            // $paramPrev['dossier']  = $value->dossier_id;
                            // $paramPrev['client']   = $value->client_id;
                            $paramPrev['dossier']  = Boost::boost($value->dossier_id);
                            $paramPrev['client']   = Boost::boost($value->client_id);
                            $prev                  = $this->loadRepository('Image')
                                                          ->getImagesRecues($paramPrev,$user);
                            $imagesPrev            = array();

                            if (empty($prev)){

                                $imagesPrev[0]['client']           = $client;
                                $imagesPrev[0]['dossier']          = $dossier;
                                $imagesPrev[0]['total']            = 0;
                                $imagesPrev[0]['exercice']         = 'N - 1';
                                $imagesPrev[0]['totalN']           = 0;
                                $imagimagesPreves[0]['totalNPrev'] = 0;
                                $imagesPrev[0]                     = $this->initializeM($imagesPrev[0],count($images[$index3]) - 6);

                            } else{
                                $imagesPrev = $this->formatDataPrev($prev,$paramPrev,count($images[$index3]) - 6);
                            }

                            if ($index == 0){
                                $images[1]               = $imagesPrev[0];
                                $images[2]               = $this->getMonthLabel($moisCloture);
                                $images[2]['client']     = $value->client;
                                $images[2]['dossier']    = $value->dossier;
                                $images[2]['total']      = '';
                                $images[2]['exercice']   = '';
                                $images[1]['totalN']     = 0;
                                $images[1]['totalNPrev'] = 0;
                                $images[2]['totalN']     = 0;
                                $images[2]['totalNPrev'] = 0;

                            } else{
                            	if (empty($imagesPrev)) {
                                    $imagesPrev[0]['client']           = $client;
                                    $imagesPrev[0]['dossier']          = $dossier;
                                    $imagesPrev[0]['total']            = 0;
                                    $imagesPrev[0]['exercice']         = 'N - 1';
                                    $imagesPrev[0]['totalN']           = 0;
                                    $imagimagesPreves[0]['totalNPrev'] = 0;
                                    $imagesPrev[0]                     = $this->initializeM($imagesPrev[0],count($images[$index3]) - 6);
                                }

                                $images[$index2]               = $imagesPrev[0];
                                $images[$index2]['totalN']     = 0;
                                $images[$index2]['totalNPrev'] = 0;
                            }

                            $allPrev += $imagesPrev[0]['total'];
                        }
                    }
                } else{


                    $item            = array();
                    $debutFin        = $this->beginEnd($param['exercice'], $value->cloture);
                    $end             = new \DateTime($debutFin['end']);
                    $end->add(new \DateInterval('P12M'));
                    $debutFin['end'] = $end->format('Y-m-d');

                    $debutFin = $this->get24Mois($param['exercice']);

                    $moisCloture     = $this->getBetweenDate($debutFin['start'], $debutFin['end']);

                    $k = array_key_exists($debutFin['start'] . '-' . $debutFin['end'], $betweens);

                    if ($k) {
                        $moisCloture = $betweens[$debutFin['start'] . '-' . $debutFin['end']];
                    } else{
                        $moisCloture         = $this->getBetweenDate($debutFin['start'], $debutFin['end']);
                        
                        $betweens[$debutFin['start'] . '-' . $debutFin['end']] = $moisCloture;

                    }

                    $item['dossier'] = $value->dossier;
                    $item['nb']      = $value->nb;

                    $dfMoisInf = $this->getMoisInf($param['exercice']);

                    $moisInf = $this->getBetweenDate($dfMoisInf['start'], $dfMoisInf['end']);

                    if ($param['typedate'] == 2) {
                        $keyMonth = array_search($value->date_piece, $moisCloture);
                        if (!$keyMonth && strval($keyMonth) !== '0') {
                           $exist = array_search($value->date_piece, $moisInf);
                           if ($exist || strval($exist) === '0' ) {
                               $keyMonth = 24;
                           }

                        }
                    }
                    else{
                        $keyMonth = array_search($value->date_scan, $moisCloture);
                        if (!$keyMonth && strval($keyMonth) !== '0') {
                           $exist = array_search($value->date_scan, $moisInf);
                           if ($exist || strval($exist) === '0' ) {
                               $keyMonth = 24;
                           }

                        }
                    }

                    if($keyMonth || strval($keyMonth) === '0' ){

                        $m            = array();
                        $m[$keyMonth] = $value->nb;
                        $item['m']    = $m;
                        $not          = false;

                        if (!array_key_exists($value->dossier, $data)) {
                            $not                          = true;
                            $data[$value->dossier]['key'] = $i;
                            $i += 3;
                            $isany += 1;
                        }
                        
                        if (isset($data[$value->dossier]['m'][$keyMonth])) {
                            $data[$value->dossier]['m'][$keyMonth] += $value->nb;
                        } else{
                            $data[$value->dossier]['m'][$keyMonth] = $value->nb;
                        }

                        $index  = $data[$value->dossier]['key'];
                        $index2 = intval($index) + 1 ;
                        $index3 = intval($index) - 1 ;
                        $total  = $this->getTotal($data[$value->dossier]['m'] );

                        if (array_key_exists($index, $images)) {
                            $lastTotal = $images[$index]['total'];
                            $all       = $all - $lastTotal + $total;
                        } else{
                            $all += $total;
                        }

                        $client                        = $this->loadRepository('Client')
                                                              ->find($value->client_id)->getNom();
                        $dossier                       = $this->loadRepository('Dossier')
                                                              ->find($value->dossier_id)->getNom();

                        $images[$index3]               = $this->getMonthLabel($moisCloture);

                        // $images[$index3]['m+24'] = "<" . $images[$index3]['m'];
                        $images[$index3]['m+24'] = "< m";

                        $images[$index3]['client']     = $value->client;
                        $images[$index3]['dossier']    = $value->dossier;
                        $images[$index3]['total']      = '';
                        $images[$index3]['exercice']   = '';
                        $images[$index3]['totalN']     = 0;
                        $images[$index3]['totalNPrev'] = 0;
                        $images[$index]['client']      = $value->client;
                        $images[$index]['dossier']     = $value->dossier;
                        $images[$index]['total']       = $total;
                        $images[$index]['exercice']    = 'N';
                        $images[$index]                = $this->initializeM($images[$index]);
                        $images[$index]                = $this->pushM($data[$value->dossier]['m'],$images[$index],$param['analyse']);
                        $images[$index]['totalN']      = 0;
                        $images[$index]['totalNPrev']  = 0;

                        if ($not === true) {

                            $paramPrev             = array();
                            $paramPrev             = $param;
                            $paramPrev['exercice'] = strval(intval($param['exercice']) - 1);
                            $paramPrev['dossier']  = Boost::boost($value->dossier_id);
                            $paramPrev['client']   = Boost::boost($value->client_id);

                            $user = $this->getUser();

                            $prev                  = $this->loadRepository('Image')
                                                          ->getImagesRecues($paramPrev, $user);
                            $imagesPrev            = array();

                            if (empty($prev)){

                                $imagesPrev[0]['client']           = $client;
                                $imagesPrev[0]['dossier']          = $dossier;
                                $imagesPrev[0]['total']            = 0;
                                $imagesPrev[0]['exercice']         = 'N - 1';
                                $imagesPrev[0]['totalN']           = 0;
                                $imagimagesPreves[0]['totalNPrev'] = 0;
                                $imagesPrev[0]                     = $this->initializeM($imagesPrev[0]);
                            } else{
                                $imagesPrev = $this->formatDataPrev($prev,$paramPrev,24,$moisCloture);
                            }

                            if ($index == 0){
                                $images[1]               = $imagesPrev[0];
                                $images[2]               = $this->getMonthLabel($moisCloture);
                                $images[2]['m+24'] = "< m";
                                $images[2]['client']     = $value->client;
                                $images[2]['dossier']    = $value->dossier;
                                $images[2]['total']      = '';
                                $images[2]['exercice']   = '';
                                $images[1]['totalN']     = 0;
                                $images[1]['totalNPrev'] = 0;
                                $images[2]['totalN']     = 0;
                                $images[2]['totalNPrev'] = 0;

                            }

                            else{

                                if (empty($imagesPrev)) {
                                    $imagesPrev[0]['client']           = $client;
                                    $imagesPrev[0]['dossier']          = $dossier;
                                    $imagesPrev[0]['total']            = 0;
                                    $imagesPrev[0]['exercice']         = 'N - 1';
                                    $imagesPrev[0]['totalN']           = 0;
                                    $imagimagesPreves[0]['totalNPrev'] = 0;
                                    $imagesPrev[0]                     = $this->initializeM($imagesPrev[0]);
                                }
                              
                                $images[$index2]               = $imagesPrev[0];
                                $images[$index2]['totalN']     = 0;
                                $images[$index2]['totalNPrev'] = 0;

                            }

                            $allPrev += $imagesPrev[0]['total'];

                        }
                    }

                }
        	}
        }

        $images[1]['totalN']     = $all;
        $images[1]['totalNPrev'] = $allPrev;

        if ($param['dossier'] === '0') {

            $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find(Boost::deboost($param['client'],$this));

            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(), $the_client, Boost::deboost($param['site'],$this), $param['exercice'] - 1);

        	// $dossiers = $this->loadRepository('ReleveManquant')
         //                     ->getListDosierByExo($param['client'],$param['exercice'], false);

           	if ($i != 0) {
                $i = $i - 1;
            }

            foreach ($dossiers as $_dossier) {

            	$dossier = (object)$_dossier;

                $exist = false; 
            	foreach ($images as $image) {

                    if (array_key_exists('dossier', $image)) {
                        if ($dossier->getNom() == $image['dossier']) {
                            $exist = true;
                        }
                    }

                }

                if (!$exist) {

                	if ($dossier->getDateCloture() != null) {
                    	$annee_cloture = explode('-', $dossier->getDateCloture()->format('Y-m-d'))[0];
                	} else{
                		$annee_cloture = null;
                	}

                	if ($param['typedate'] == 2 && $dossier->getDateCloture() && $dossier->getDebutActivite() && $annee_cloture == $param['exercice']) {
                        
                        $debutFin = array(
                            'start' => $dossier->getDebutActivite(),
                            'end'   => $dossier->getDateCloture()
                        );

                        $end             = new \DateTime($debutFin['end']);
                        $end->add(new \DateInterval('P24M'));
                        $debutFin['end'] = $end->format('Y-m-d');
                        // $between         = $this->getBetweenDate($debutFin['start'], $debutFin['end']);

                        $debutFin = $this->get24Mois($param['exercice']);

                        $k = array_key_exists($debutFin['start'] . '-' . $debutFin['end'], $betweens);

                        if ($k) {
                            $between = $betweens[$debutFin['start'] . '-' . $debutFin['end']];
                        } else{
                            $between         = $this->getBetweenDate($debutFin['start'], $debutFin['end']);
                            
                            $betweens[$debutFin['start'] . '-' . $debutFin['end']] = $between;

                        }

                        $label           = $this->getMonthLabel($between);

                        $label['m+24'] = "< m";


                    } else {

                        $debutFin        = $this->beginEnd($param['exercice'], $dossier->getCloture() );
                        $end             = new \DateTime($debutFin['end']);
                        $end->add(new \DateInterval('P12M'));
                        $debutFin['end'] = $end->format('Y-m-d');
                        // $between         = $this->getBetweenDate($debutFin['start'], $debutFin['end']);

                        $debutFin = $this->get24Mois($param['exercice']);

                        $k = array_key_exists($debutFin['start'] . '-' . $debutFin['end'], $betweens);
                        
                        if ($k) {
                            $between = $betweens[$debutFin['start'] . '-' . $debutFin['end']];
                        } else{
                            $between         = $this->getBetweenDate($debutFin['start'], $debutFin['end']);
                            
                            $betweens[$debutFin['start'] . '-' . $debutFin['end']] = $between;

                        }

                        $label           = $this->getMonthLabel($between);

                        $label['m+24'] = "< m";
                        

                    }

                    $images[$i]                 = $label;
                    $images[$i]['client']       = $dossier->getSite()->getClient()->getNom();
                    $images[$i]['dossier']      = $dossier->getNom();
                    $images[$i]['exercice']     = '';
                    $images[$i]['total']        = '';
                    $images[$i + 1]             = $this->initializeM($images[$i],count($label));
                    $images[$i + 1]['dossier']  = $dossier->getNom();
                    $images[$i + 1]['exercice'] = 'N';
                    $images[$i + 1]['total']    = 0;
                    $paramPrev                  = array();
                    $paramPrev                  = $param;
                    $paramPrev['exercice']      = strval(intval($param['exercice']) - 1);

                    $paramPrev['dossier']  = Boost::boost($dossier->getId());
                    $paramPrev['client']   = Boost::boost($dossier->getSite()->getClient()->getId());

                    $user = $this->getUser();

                    $prev                       = $this->loadRepository('Image')
                                                       ->getImagesRecues($paramPrev,$user);
                    $imagesPrev                 = array();

                    if (empty($prev)){
                        $imagesPrev[0]['client']   = $dossier->getSite()->getClient()->getNom();
                        $imagesPrev[0]['dossier']  = $dossier->getNom();
                        $imagesPrev[0]['total']    = 0;
                        $imagesPrev[0]['exercice'] = 'N - 1';
                        $imagesPrev[0]             = $this->initializeM($imagesPrev[0],count($label));
                    }
                    else{
                        $imagesPrev = $this->formatDataPrev($prev,$paramPrev,count($label));
                    }

                    if (empty($imagesPrev)) {
                        $imagesPrev[0]['client']   = $dossier->getSite()->getClient()->getNom();
                        $imagesPrev[0]['dossier']  = $dossier->getNom();
                        $imagesPrev[0]['total']    = 0;
                        $imagesPrev[0]['exercice'] = 'N - 1';
                        $imagesPrev[0]             = $this->initializeM($imagesPrev[0],count($label));
                    }

                    $images[$i + 2] = $imagesPrev[0];
                    $i              = $i + 3;
                }
            }
        }

        return $images;
		
	}

	/**
     * Traitement des données pour le tableau Details pour N - 1
     *
     * @param array $result
     * @param array $param
     * @param integer $count
     * @param array $months
     *
     * @return array
     */
    public function formatDataPrev($result,$param, $count = 25,$months = array())
    {
        $betweens = array();

        $images = array();
        $data = array();
        $i = 0;

        $o = 0;

        foreach ($result as $key => $value) {

            $o += $value->nb;

            $annee_cloture = '';

            if ($param['typedate'] == 2) {
                $annee_cloture = explode('-', $value->date_cloture)[0];
            }

            if ($param['typedate'] == 2 && $value->date_cloture && $value->debut_activite && $param['exercice'] == $annee_cloture) {

                $item              = array();
                $cloture           = $value->cloture;
                $da                = explode('-', $value->debut_activite);
                $dc                = explode('-', $value->date_cloture);
                $debut_activite    = strval(intval($da[0])) . '-' . $da[1]; 
                $date_cloture      = strval(intval($dc[0])) . '-' . $dc[1]; 
                $debutFin['start'] = $debut_activite;
                $debutFin['end']   = $date_cloture;
                $end               = new \DateTime($debutFin['end']);
                $end->add(new \DateInterval('P12M'));
                $debutFin['end']   = $end->format('Y-m-d');

                $debutFin = $this->get24Mois($param['exercice']);

                if ($months) {
                    $moisCloture = $this->getMoisCloture($months);
                }
                else{
                    $k = array_key_exists($debutFin['start'] . '-' . $debutFin['end'], $betweens);

                    if ($k) {
                        $moisCloture = $betweens[$debutFin['start'] . '-' . $debutFin['end']];
                    } else{
                        $moisCloture         = $this->getBetweenDate($debutFin['start'], $debutFin['end']);
                        
                        $betweens[$debutFin['start'] . '-' . $debutFin['end']] = $moisCloture;

                    }
                }

                $item['dossier'] = $value->dossier;
                $item['nb']      = $value->nb;
                $keyMonth        = array_search($value->date_piece, $moisCloture);

                $dfMoisInf = $this->getMoisInf($param['exercice']);

                $moisInf = $this->getBetweenDate($dfMoisInf['start'], $dfMoisInf['end']);

                if (!$keyMonth && strval($keyMonth) !== '0') {
                   $exist = array_search($value->date_piece, $moisInf);
                   if ($exist || strval($exist) === '0' ) {
                       $keyMonth = 24;
                   }

                }

                if ($keyMonth || strval($keyMonth) === '0') {
                    if (!array_key_exists($value->dossier, $data)) {
                        $data[$value->dossier]['key'] = $i;
                        $i += 1;
                    }
                    
                    if (isset($data[$value->dossier]['m'][$keyMonth])) {
                        $data[$value->dossier]['m'][$keyMonth] += $value->nb;
                    } else{
                        $data[$value->dossier]['m'][$keyMonth] = $value->nb;
                    }
                    $index                      = $data[$value->dossier]['key'];
                    $total                      = $this->getTotal($data[$value->dossier]['m'] );
                    $images[$index]['client']   = $value->client;
                    $images[$index]['dossier']  = $value->dossier;
                    $images[$index]['total']    = $total;
                    $images[$index]['exercice'] = 'N - 1';
                    $images[$index]             = $this->initializeM($images[$index], $count);
                    $images[$index]             = $this->pushM($data[$value->dossier]['m'],$images[$index],$param['analyse']);
                }
            }

            else{

                $item            = array();
                $cloture         = $value->cloture;
                $debutFin        = $this->beginEnd($param['exercice'], $cloture);
                $end             = new \DateTime($debutFin['end']);
                $end->add(new \DateInterval('P12M'));
                $debutFin['end'] = $end->format('Y-m-d');

                $debutFin = $this->get24Mois($param['exercice']);

                $k = array_key_exists($debutFin['start'] . '-' . $debutFin['end'], $betweens);

                if ($k) {
                    $moisCloture = $betweens[$debutFin['start'] . '-' . $debutFin['end']];
                } else{
                    $moisCloture         = $this->getBetweenDate($debutFin['start'], $debutFin['end']);
                    
                    $betweens[$debutFin['start'] . '-' . $debutFin['end']] = $moisCloture;

                }

                $item['dossier'] = $value->dossier;
                $item['nb']      = $value->nb;

                $dfMoisInf = $this->getMoisInf($param['exercice']);

                $moisInf = $this->getBetweenDate($dfMoisInf['start'], $dfMoisInf['end']);

                if ($param['typedate'] == 2) {
                    $keyMonth = array_search($value->date_piece, $moisCloture);
                    if (!$keyMonth && strval($keyMonth) !== '0') {
                        $exist = array_search($value->date_piece, $moisInf);
                        if ($exist || strval($exist) === '0' ) {
                               $keyMonth = 24;
                        }
                    }
                }
                else{
                    $keyMonth = array_search($value->date_scan, $moisCloture);
                    if (!$keyMonth && strval($keyMonth) !== '0') {
                        $exist = array_search($value->date_scan, $moisInf);
                        if ($exist || strval($exist) === '0' ) {
                               $keyMonth = 24;
                       }
                    }
                }

                if ($keyMonth || strval($keyMonth) === '0') {

                    if (!array_key_exists($value->dossier, $data)) {
                        $data[$value->dossier]['key'] = $i;
                        $i += 1;
                    }
                    
                    if (isset($data[$value->dossier]['m'][$keyMonth])) {
                        $data[$value->dossier]['m'][$keyMonth] += $value->nb;
                    }

                    else{

                        $data[$value->dossier]['m'][$keyMonth] = $value->nb;
                    }

                    $index                      = $data[$value->dossier]['key'];
                    $total                      = $this->getTotal($data[$value->dossier]['m'] );
                    $images[$index]['client']   = $value->client;
                    $images[$index]['dossier']  = $value->dossier;
                    $images[$index]['total']    = $total;
                    $images[$index]['exercice'] = 'N - 1';
                    $images[$index]             = $this->initializeM($images[$index]);
                    $images[$index]             = $this->pushM($data[$value->dossier]['m'],$images[$index],$param['analyse']);
                }
           
            }

        }

        return $images;
    }

    /**
     * Liste des mois par exercice
     *
     * @param array $months
     *
     * @return array
     */
    public function getMoisCloture($months)
    {
        
        $result = array();

        foreach ($months as $month) {
            
            $explode = explode('-', $month);
            $value   = strval(intval($explode[0]) - 1) . '-' . $explode[1];
            array_push($result, $value);

        }

        return $result;

    }

	/**
     * Calcul des nombres d'images pour les mois
     *
     * @param array $m
     * @param array $result
     * @param integer $analyse
     *
     * @return array 
     */
    public function pushM($m, $result, $analyse)
    {

            $last = count($result) - 1;

           foreach ($m as $key => $value) {
            $index = "m+" . $key;
            if ($key == 0) {
                $index = "m";
            }
            $result[$index] = $value;
           }

           if ($analyse == 2) {
                $i = 0;
                $result['m'] += $result['m+24'];
                foreach ($result as $key => $value) {
                    if ($key != 'client' && $key != 'dossier' && $key != 'total' && $key != 'exercice' && $key != "totalN" && $key != 'totalNPrev') {
                        $key1   = $i + 1;
                        if ($key1 < 24) {
                            $index1 = "m+" .$key1;
                            if (array_key_exists($index1, $result)) {
                                $result[$index1] += $result[$key];
                            }
                            $i += 1;
                        }

                    }
                }
           }

       return $result;
    }

	/**
     * Initialisation des nombres d'images par mois
     *
     * @param array $m
     * @param integer $nb
     *
     * @return array 
     */
    public function initializeM($m, $nb = 25)
    {
        for($j=0; $j<$nb; $j++){

            $label = "m+".$j;

            if ($j == 0) {
                $label = "m";
            }

            $m[$label] = 0;
        }

        return $m;
        
    }

	/**
     * Libellé des mois
     *
     * @param $moisCloture
     *
     * @return array
     */
    public function getMonthLabel($moisCloture)
    {
        $result = array();
        foreach ($moisCloture as $key => $value) {

            if (intval($key) == 0) {
               $label          = "m";
               $name           = explode('-', $value)[1] . '-' . substr(explode('-', $value)[0], -2);
               $result[$label] = $name; 
            }

            else{
                $index          = $key;
                $label          = "m+" . $index;
                $name           = explode('-', $value)[1] . '-' . substr(explode('-', $value)[0], -2);
                $result[$label] = $name;
            }

        }

        return $result;
    }

	/**
     * Total des colonnes pour chaque ligne
     *
     * @param array $data
     *
     * @return integer 
     */
    public function getTotal($data)
    {
        $total = 0;

        foreach ($data as $nb) {
            $total += $nb;
        }

        return $total;
    }

    /**
     * Export excel Tableau Details
     *
     * @param Request $request
     */
    public function exportDetailsAction(Request $request)
    {
        $expDatas        = json_decode(urldecode($request->request->get('exp-datas')),true);
        $dossierSelector = $this->deboost($request->request->get('exp-dossier'));
        $typedate        = $request->request->get('exp-typedate');
        $exercice        = $request->request->get('exp-exercice');
        $client          = $this->deboost($request->request->get('exp-client'));

        if ($client != 0) {
            $clientValue = $this->loadRepository('Client')
                                ->find($client)
                                ->getNom();
        } else{
            $clientValue = 'Clients';
        }

        $datas           = $expDatas;
        $dossier         = $dossierSelector;
        $extension       = '.xls';
        $title           = 'Details_' . $clientValue . '_' . $exercice;
        $name            = $title . $extension;
        $phpExcelObject  = $this->get('phpexcel')->createPHPExcelObject();
        $backgroundTitle = '808080';

        $phpExcelObject->getProperties()
            ->setCreator("Picdata")
            ->setLastModifiedBy("Picdata")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription("Tableau de detail des images " . $clientValue . " de l'exercice " . $exercice)
            ->setKeywords("Picdata tableau details " . $clientValue)
            ->setCategory("exportation excel Picdata");

        $sheet = $phpExcelObject->setActiveSheetIndex(0);
         
        $i     = 1;

        $sheet->setCellValue($this->getCellIndex($i,true), '')
              ->setCellValue($this->getCellIndex($i + 1,true), 'Client')
              ->setCellValue($this->getCellIndex($i + 1), $clientValue )
              ->setCellValue($this->getCellIndex($i + 2,true), 'Exercice')
              ->setCellValue($this->getCellIndex($i + 2), $exercice);

        $key = 6;

        if ($client == 0) {
            
        } else {
             
        }

    }

    public function initializeCellValue($sheet, $client = false)
    {
        $num = 6;
        for ($i=0; $i < 28; $i++) { 
            $cellIndex = $this->getCellIndex($num);
            if ($i < 4) {
                switch ($i) {
                    case 0:
                        $sheet->setCellValue($cellIndex, 'Dossier');
                        break;
                    case 1:
                        $sheet->setCellValue($cellIndex, 'Exercice');
                        break;
                    case 2:
                        $sheet->setCellValue($cellIndex, 'Client');
                        break;
                    case 3:
                        $sheet->setCellValue($cellIndex, 'Total images');
                        break;
                }
            } else {
                $sheet->setCellValue($cellIndex, 'm' . $i - 3);
            }
        }
    }

}
