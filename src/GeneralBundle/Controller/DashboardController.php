<?php

/**
 * DashboardController
 *
 * @package Picdata
 *
 * @author Scriptura
 * @copyright Scriptura (c) 2019
 */

namespace GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GeneralBundle\Controller\DefaultController;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends DefaultController
{
	public function indexAction($type)
	{
	    return $this->render('GeneralBundle:Default:index.html.twig', ['type' => $type]);
	}

	public function dashboardDefaultAction()
	{
		$result         = array();
		$isDefault      = true;
		$result['cb']   = $this->getComptesBancaires($isDefault,0,0,0,0);
		$result['obm']  = $this->getBancairesManquantes($isDefault,0,0,0,0);
		$result['tbec'] = $this->getBancairesEnCours($isDefault,0,0,0);
		return $this->response($result);
	}

	/**
	 * Traitement ajax des tableaux
	 *
	 * @param string $client
	 * @param string $exercice
	 * @param string $dossier
	 *
	 * @return JsonResponse
	 */
	public function dashboardAjaxAction(Request $request)
	{
		$client = $request->request->get('client');
		$exercice = $request->request->get('exercice');
		$dossier = $request->request->get('dossier');
		$moisData = $request->request->get('moisData');
		$listDossier = $request->request->get('listDossier');
		$type = intval($request->request->get('type'));
		$result    = array();
		$isDefault = false;
		if($type){
			$tar = $this->getTravauxARealiser($client,$exercice,$dossier, $moisData, false);
			$result['tarNames'] = $tar['names'];
			$result['tarTaches'] = $tar['taches'];
			$result['date'] = $tar['date'];
		}else{
			$result['cb']   = $this->getComptesBancaires($isDefault,$client,$exercice,$dossier,$listDossier);
			$result['obm']  = $this->getBancairesManquantes($isDefault,$client,$exercice,$dossier,$listDossier);
			$result['tbec'] = $this->getBancairesEnCours($isDefault,$client,$exercice,$dossier);
		}

		return $this->response($result);
	}

	/**
	 * Traitement des données Tâches réalisées et dépassées
	 *
	 * @param string $client
	 * @param string $exercice
	 * @param string $dossier
	 *
	 * @return JsonResponse
	 */
	public function getRealiseesDepassees($client, $exercice, $dossier)
	{
		// TVA
		$response[] = [
			'id'   => 'rd-tva',
			'cell' => [
				'TVA',
				'',
				'',
				'',
				''
			]
		];

		// IS
		$response[] = [
			'id'   => 'rd-is',
			'cell' => [
				'IS',
				'',
				'',
				'',
				''
			]
		];

		// MAJ
		$response[] = [
			'id'   => 'rd-maj',
			'cell' => [
				'MAJ',
				'',
				'',
				'',
				''
			]
		];

		return $response;
	}

	/**
	 * Traitement des données Situation des travaux en cours
	 *
	 * @param string $client
	 * @param string $exercice
	 * @param string $dossier
	 *
	 * @return JsonResponse
	 */
	public function getTravauxEnCours($client, $exercice, $dossier)
	{

		$response[] = [
			'id'           => 'tc-dossier',
			'list'         => 'Dossiers totaux',
			'n'            => '',
			'percent-n'    => '',
			'n-1'          => '',
			'percent-n-1'  => '',
			'dossiers-n'   => '',
			'dossiers-n-1' => '',
			'level'        => '0',
			'parent'       => "null",
			'isLeaf'       => false,
			'expanded'     => true,
			'loaded'       => true,
		];

		$response[] = [
			'id'           => 'tc-drtimp',
			'list'         => 'DRT impossibles',
			'n'            => '',
			'percent-n'    => '',
			'n-1'          => '',
			'percent-n-1'  => '',
			'dossiers-n'   => '',
			'dossiers-n-1' => '',
			'level'        => '1',
			'parent'       => "tc-dossier",
			'isLeaf'       => false,
			'expanded'     => true,
			'loaded'       => true,
		];

		$response[] = [
			'id'           => 'tc-insuffisantes',
			'list'         => 'Pièces insuff',
			'n'            => '',
			'percent-n'    => '',
			'n-1'          => '',
			'percent-n-1'  => '',
			'dossiers-n'   => '',
			'dossiers-n-1' => '',
			'level'        => '2',
			'parent'       => "tc-drtimp",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		$response[] = [
			'id'           => 'tc-rbinc',
			'list'         => 'RB incomplets',
			'n'            => '',
			'percent-n'    => '',
			'n-1'          => '',
			'percent-n-1'  => '',
			'dossiers-n'   => '',
			'dossiers-n-1' => '',
			'level'        => '2',
			'parent'       => "tc-drtimp",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		$response[] = [
			'id'           => 'tc-drtafaire',
			'list'         => 'DRT à faire',
			'n'            => '',
			'percent-n'    => '',
			'n-1'          => '',
			'percent-n-1'  => '',
			'dossiers-n'   => '',
			'dossiers-n-1' => '',
			'level'        => '1',
			'parent'       => "tc-dossier",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		$response[] = [
			'id'           => 'tc-drtenattente',
			'list'         => 'DRT en attente',
			'n'            => '',
			'percent-n'    => '',
			'n-1'          => '',
			'percent-n-1'  => '',
			'dossiers-n'   => '',
			'dossiers-n-1' => '',
			'level'        => '1',
			'parent'       => "tc-dossier",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		$response[] = [
			'id'           => 'tc-finissables',
			'list'         => 'Finissables',
			'n'            => '',
			'percent-n'    => '',
			'n-1'          => '',
			'percent-n-1'  => '',
			'dossiers-n'   => '',
			'dossiers-n-1' => '',
			'level'        => '1',
			'parent'       => "tc-dossier",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		$response[] = [
			'id'           => 'tc-afscriptura',
			'list'         => 'A finir Scriptura',
			'n'            => '',
			'percent-n'    => '',
			'n-1'          => '',
			'percent-n-1'  => '',
			'dossiers-n'   => '',
			'dossiers-n-1' => '',
			'level'        => '1',
			'parent'       => "tc-dossier",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		$response[] = [
			'id'           => 'tc-termines',
			'list'         => 'Terminés',
			'n'            => '',
			'percent-n'    => '',
			'n-1'          => '',
			'percent-n-1'  => '',
			'dossiers-n'   => '',
			'dossiers-n-1' => '',
			'level'        => '1',
			'parent'       => "tc-dossier",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		return $response;
	}

	/**
	 * Récupération des données dossiers avec pièces manquantes
	 *
	 * @param array $param
	 *
	 * @return array
	 */
	public function prepareDataPM($param)
	{

		$repository       = $this->loadRepository('Image');
		$obmRepository = $this->loadRepository('BanqueObManquante');
		$param['dossier'] = $this->deboost($param['dossier']);
		
		$totaux           = $pieces = $ob = $dossierOb = $ci = $dossierCi = $ff = $dossierFF = $fc = $dossierFC = $rb = $dossierRb = 0;
		$tabDossier       = $tabOB  = $tabCI = $tabFC = $tabFF = $tabDossierRB = $tabCompte = array();

		$user = $this->getUser();

		// Tous les dossiers
		if ($param['dossier'] == 0) {
			
             // Dossiers Totaux
             $result = $repository->getDossiersTotaux($param,$user);
             foreach ($result as $value) {
             	$pieces += $value->nb;
             	array_push($tabDossier, $value->nom_dossier . '*' . $value->cloture . '*' . $param['exercice']);
             	$totaux++;
             }
	        
		} else{ // Dossier spécifique
			
			// Dossiers Totaux
			$result = $repository->getDossiersTotaux($param,$user);
        	foreach ($result as $v) {
        		$pieces += $v->nb;
        		array_push($tabDossier, $v->nom_dossier . '*' . $v->cloture . '*' . $param['exercice']);
        	}
        	$totaux++;
		}

		// OB
		$resultOB = $obmRepository->getOBPM($param,$user);

		foreach ($resultOB as $value) {

			$ob += $value->nb_pieces_manquantes ;

			if (!in_array($value->nom_dossier . '*' . $value->cloture . '*' . $param['exercice'], $tabOB)) {
				$dossierOb++;
				array_push($tabOB, $value->nom_dossier . '*' . $value->cloture . '*' . $param['exercice']);
			}

		}

		// Chèques inconnus
		$resultCI = $repository->getChequeIconnuByDossier($param,$user);

		foreach ($resultCI as $value) {
			$ci += $value->nb;
			array_push($tabCI, $value->nom_dossier . '*' . $value->cloture . '*' . $param['exercice']);
		}

		// Factures
		$resultFact = $repository->getFactures($param,$user);
		foreach ($resultFact as $value) {
			// Fournisseurs
			if ($value->montant < 0) {
				$ff += $value->nb;
				$dossierFF++;
				array_push($tabFF, $value->nom_dossier . '*' . $value->cloture . '*' . $param['exercice']);
			} else{ // Clients
				$fc += $value->nb;
				$dossierFC++;
				array_push($tabFC, $value->nom_dossier . '*' . $value->cloture . '*' . $param['exercice']);
			}
		}

		$response = array(
			'totaux'          => $totaux,
			'nb_pieces'       => $pieces,
			'nb_ob'           => $ob,
			'dossier_ob'      => $dossierOb,
			'nb_rb'           => $rb,
			'dossier_rb'      => $dossierRb,
			'nb_ci'           => $ci,
			'dossier_ci'      => count($resultCI),
			'nb_ff'           => $ff,
			'dossier_ff'      => $dossierFF,
			'nb_fc'           => $fc,
			'dossier_fc'      => $dossierFC,
			'nb_manques'      => $rb + $ob + $ci + $ff + $fc,
			'dossier_manques' => $dossierRb + $dossierOb + count($resultCI) + $dossierFF + $dossierFC,
			'dossiers'        => json_encode($tabDossier),
			'dossiers_ob'     => json_encode($tabOB),
			'dossiers_ci'     => json_encode($tabCI),
			'dossiers_ff'     => json_encode($tabFF),
			'dossiers_fc'     => json_encode($tabFC)
		);

		return $response;
		
	}

	/**
	 * Traitement des données Dossiers avec des pièces manquantes
	 *
	 * @param string $client
	 * @param string $exercice
	 * @param string dossier
	 *
	 * @return JsonResponse
	 */
	public function getPiecesManquantes($client, $exercice, $dossier)
	{
		$param = array(
			'exercice' => $exercice,
			'client'   => $client,
			'dossier'  => $dossier
		);

		$data = $this->prepareDataPM($param);

		$paramNMoinsUn = array(
			'exercice' => $exercice - 1,
			'client'   => $client,
			'dossier'  => $dossier
		);

		$dataNMoinsUn = $this->prepareDataPM($paramNMoinsUn);

		$response[] = [
			'id'           => 'dpm-dossier',
			'list'         => 'Dossiers totaux',
			'n'            => $this->numberFormat($data['totaux']), 
			'pieces-n'     => $this->numberFormat($data['nb_pieces']), 
			'n-1'          => $this->numberFormat($dataNMoinsUn['totaux']), 
			'pieces-n-1'   => $this->numberFormat($dataNMoinsUn['nb_pieces']), 
			'dossiers-n'   => $data['dossiers'],
			'dossiers-n-1' => $dataNMoinsUn['dossiers'],
			'level'        => '0',
			'parent'       => "null",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => "ui-icon-blank"
		];

		$response[] = [
			'id'           => 'dpm-dmanques',
			'list'         => 'Avec manques',
			'n'            => $this->numberFormat($data['dossier_manques']),
			'pieces-n'     => $this->numberFormat($data['nb_manques']),
			'n-1'          => $this->numberFormat($dataNMoinsUn['dossier_manques']),
			'pieces-n-1'   => $this->numberFormat($dataNMoinsUn['nb_manques']),
			'dossiers-n'   => '',
			'dossiers-n-1' => '',
			'level'        => '0',
			'parent'       => "null",
			'isLeaf'       => false,
			'expanded'     => true,
			'loaded'       => true,

		];

		// $response[] = [
		// 	'id'           => 'dpm-rb',
		// 	'list'         => 'RB',
		// 	'n'            => $data['dossier_rb'],
		// 	'pieces-n'     => $data['nb_rb'],
		// 	'n-1'          => $dataNMoinsUn['dossier_rb'],
		// 	'pieces-n-1'   => $dataNMoinsUn['nb_rb'],
		// 	'dossiers-n'   => '',
		// 	'dossiers-n-1' => '',
		// 	'level'        => '1',
		// 	'parent'       => "dpm-dmanques",
		// 	'isLeaf'       => true,
		// 	'expanded'     => false,
		// 	'loaded'       => true,
		// 	'icon'         => 'ui-icon-blank'

		// ];

		$response[] = [
			'id'           => 'dpm-ob',
			'list'         => 'OB',
			'n'            => $this->numberFormat($data['dossier_ob']),
			'pieces-n'     => $this->numberFormat($data['nb_ob']),
			'n-1'          => $this->numberFormat($dataNMoinsUn['dossier_ob']),
			'pieces-n-1'   => $this->numberFormat($dataNMoinsUn['nb_ob']),
			'dossiers-n'   => $data['dossiers_ob'],
			'dossiers-n-1' => $dataNMoinsUn['dossiers_ob'],
			'level'        => '1',
			'parent'       => "dpm-dmanques",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		$response[] = [
			'id'           => 'dpm-ci',
			'list'         => 'Chq inconnus',
			'n'            => $this->numberFormat($data['dossier_ci']),
			'pieces-n'     => $this->numberFormat($data['nb_ci']),
			'n-1'          => $this->numberFormat($dataNMoinsUn['dossier_ci']),
			'pieces-n-1'   => $this->numberFormat($dataNMoinsUn['nb_ci']),
			'dossiers-n'   => $data['dossiers_ci'],
			'dossiers-n-1' => $dataNMoinsUn['dossiers_ci'],
			'level'        => '1',
			'parent'       => "dpm-dmanques",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		$response[] = [
			'id'           => 'dpm-ffrs',
			'list'         => 'Fact FRS',
			'n'            => $this->numberFormat($data['dossier_ff']),
			'pieces-n'     => $this->numberFormat($data['nb_ff']),
			'n-1'          => $this->numberFormat($dataNMoinsUn['dossier_ff']),
			'pieces-n-1'   => $this->numberFormat($dataNMoinsUn['nb_ff']),
			'dossiers-n'   => $data['dossiers_ff'],
			'dossiers-n-1' => $dataNMoinsUn['dossiers_ff'],
			'level'        => '1',
			'parent'       => "dpm-dmanques",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		$response[] = [
			'id'           => 'dpm-fclients',
			'list'         => 'Fact clients',
			'n'            => $this->numberFormat($data['dossier_fc']),
			'pieces-n'     => $this->numberFormat($data['nb_fc']),
			'n-1'          => $this->numberFormat($dataNMoinsUn['dossier_fc']),
			'pieces-n-1'   => $this->numberFormat($dataNMoinsUn['nb_fc']),
			'dossiers-n'   => $data['dossiers_fc'],
			'dossiers-n-1' => $dataNMoinsUn['dossiers_fc'],
			'level'        => '1',
			'parent'       => "dpm-dmanques",
			'isLeaf'       => true,
			'expanded'     => false,
			'loaded'       => true,
			'icon'         => 'ui-icon-blank'
		];

		return $response;

	}

	/**
	 * Récupération des données Travaux bancaires en cours
	 *
	 * @param array $param
	 *
	 * @return array
	 */
	public function prepareDataTBEC($param)
	{

		$response = array(
			'nb_lettrees'          => 0,
			'nb_pieces_manquantes' => 0,
			'nb_imputes'           => 0,
			'nb_ecriture_change'   => 0,
			'nb_rapp'              => 0,
			'nb_dossier'           => 0,
			'nb_en_cours'          => 0,
			'lignes'               => 0,
			'nb_a_valider'         => 0,
			'comptes'              => 0
		);

		$repository       = $this->loadRepository('Image');
		$param['dossier'] = $this->deboost($param['dossier']);
		$tabDossier       = $tabDossierEncaiss = $tabDossierDecaiss = $tabDossierChq = $tabDossierRappro = $tabDossierCours = $tabCompte = array();
		$tabCompteEncaiss = $tabCompteDecaiss = $tabCompteCheque = $tabComptLet = $tabComptImp = $tabComptEcr = $tabComptEncour = 0;

		$user = $this->getUser();
    	$result = $repository->getTBEC($param,$user);

		$repositoryReleve       = $this->loadRepository('Releve');

		$response['nb_encaiss'] = 0;
		$nbEncaiss = $repository->getEncaissementPm($param);
		foreach ($nbEncaiss as $key => $value) {
			if(!in_array($value->id, $tabDossier)){
				$tabDossier[] = $value->id;
			}
			array_push($tabDossierEncaiss, $value->dossierNom.'*'.$value->numcompte.'*'.$value->nb);
			$tabCompteEncaiss += 1;
			$response['nb_encaiss'] += $value->nb;
		}
		$response['nb_decaiss'] = 0;
		$nbDecaiss = $repository->getDecaissementPm($param);
		foreach ($nbDecaiss as $key => $value) {
			if(!in_array($value->id, $tabDossier)){
				$tabDossier[] = $value->id;
			}
			array_push($tabDossierDecaiss, $value->dossierNom .'*'.$value->numcompte.'*'.$value->nb);
			$tabCompteDecaiss += 1;
			$response['nb_decaiss'] += $value->nb;
		}
		$param['dossier'] = Boost::boost($param['dossier']);
		$nbCheque  = $repository->getChequeIconnuByBanqueCompte($param,$user);
		$response['nb_cheque'] = 0;
		foreach ($nbCheque as $key => $value) {
			if(!in_array($value->id, $tabDossier)){
				$tabDossier[] = $value->id;
			}
			array_push($tabDossierChq, $value->dossierNom.'*'.$value->numcompte.'*'.$value->nb);
			$tabCompteCheque += 1;
			$response['nb_cheque'] += $value->nb;
		}

		foreach($result['nb_lettrees'] as $key => $value) {
			if(!in_array($value->id, $tabDossier)){
				$tabDossier[] = $value->id;
			}
			if(!in_array($value->numcompte, $tabCompte)){
				$tabCompte[] = $value->numcompte;
			}
			if (!in_array($value->dossierNom, $tabDossierRappro)) {
				array_push($tabDossierRappro, $value->dossierNom);
			}
			$tabComptLet += 1;
			$response['nb_lettrees'] += $value->nb_lettrees;
		}

		foreach ($result['nb_imputes'] as $key => $value) {
			if(!in_array($value->id, $tabDossier)){
				$tabDossier[] = $value->id;
			}
			if(!in_array($value->numcompte, $tabCompte)){
				$tabCompte[] = $value->numcompte;
			}
			if (!in_array($value->dossierNom, $tabDossierRappro)) {
				array_push($tabDossierRappro, $value->dossierNom);
			}
			$tabComptImp += 1;
			$response['nb_imputes'] += $value->nb_imputes;
		}

		foreach ($result['nb_ecriture_change'] as $key => $value) {
			if(!in_array($value->id, $tabDossier)){
				$tabDossier[] = $value->id;
			}
			if(!in_array($value->numcompte, $tabCompte)){
				$tabCompte[] = $value->numcompte;
			}
			if (!in_array($value->dossierNom, $tabDossierRappro)) {
				array_push($tabDossierRappro, $value->dossierNom);
			}
			$tabComptEcr += 1;
			$response['nb_ecriture_change'] += $value->nb_ec;
		}

		foreach ($result['nb_en_cours'] as $key => $value) {
			if(!in_array($value->id, $tabDossier)){
				$tabDossier[] = $value->id;
			}
			if (!in_array($value->dossierNom.'*'.$value->numcompte.'*'.$value->nb_en_cours, $tabDossierCours)) {
				array_push($tabDossierCours, $value->dossierNom.'*'.$value->numcompte.'*'.$value->nb_en_cours);
			}
			$tabComptEncour += 1;
			$response['nb_en_cours'] += $value->nb_en_cours;
		}
		$response['nb_rapp']                   = count($tabCompte);
		$response['comptes']                   = $response['nb_rapp'] + $tabComptEncour;
		$response['nb_cpt_encour']             = $tabComptEncour;
		$response['nb_dossier']                = count($tabDossier);
		$response['nb_compte']                 = $tabCompteDecaiss + $tabCompteEncaiss + $tabCompteCheque;
		$response['nb_cpt_decaiss']            = $tabCompteDecaiss;
		$response['nb_cpt_encaiss']            = $tabCompteEncaiss;
		$response['nb_cpt_chq']                = $tabCompteCheque;
		$response['dossiers_rapp']             = json_encode($tabDossierRappro);
		$response['dossiers_decaiss']          = json_encode($tabDossierDecaiss);
		$response['dossiers_encaiss']          = json_encode($tabDossierEncaiss);
		$response['dossiers_chq']              = json_encode($tabDossierChq);
		$response['dossiers_en_cours']         = json_encode($tabDossierCours);

		return $response;

	}

	/**
	 * Retourne la valeur de la première clé d'une tableau
	 *
	 * @param array $result
	 * @param string $key
	 *
	 * @return integer
	 */
	public function singleValue($result, $key)
	{
		return (!empty($result[$key])) ? $result[$key][0]->$key : 0 ;
	}

	/**
	 * Traitement des données Travaux bancaires en cours
	 *
	 * @param string $client
	 * @param string $exercice
	 * @param string $dossier
	 *
	 * @return JsonResponse
	 */
	public function getBancairesEnCours($isDefault, $client, $exercice, $dossier)
	{

		$param = array(
			'exercice' => $exercice,
			'client'   => $client,
			'dossier'  => $dossier
		);

		if($isDefault){
			$response[] = [
				'id'           => 'tbc-dossier',
				'list'         => 'Dossiers',
				'dossier-nb'   => 0,
				'n'            => '',
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'tbc-compte',
				'list'         => 'Totals des Comptes',
				'dossier-nb'   => 0,
				'n'            => '',
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => false,
				'expanded'     => true,
				'loaded'       => true,
			];

			$response[] = [
				'id'           => 'tbc-rapp',
				'list'         => 'Rapprochés',
				'dossier-nb'   => 0,
				'n'            => '',
				'dossiers-n'   => 0,
				'level'        => '1',
				'parent'       => "tbc-compte",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tbc-encours',
				'list'         => 'En cours',
				'dossier-nb'   => 0,
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => '1',
				'parent'       => "tbc-compte",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tbc-pm',
				'list'         => 'Pièces manquantes',
				'n'            => '',
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => false,
				'expanded'     => true,
				'loaded'       => true,
			];

			$response[] = [
				'id'           => 'tbc-decaissements',
				'list'         => 'Décaissements',
				'dossier-nb'   => 0,
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => '1',
				'parent'       => "tbc-pm",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tbc-encaissements',
				'list'         => 'Encaissements',
				'dossier-nb'   => 0,
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => '1',
				'parent'       => "tbc-pm",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tbc-cheque',
				'list'         => 'Cheque',
				'dossier-nb'   => 0,
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => '1',
				'parent'       => "tbc-pm",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];
		}else{
			$data = $this->prepareDataTBEC($param);
			$response[] = [
				'id'           => 'tbc-dossier',
				'list'         => 'Dossiers',
				'dossier-nb'   => $this->numberFormat($data['nb_dossier']),
				'n'            => '',
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'tbc-compte',
				'list'         => 'Totals des Comptes',
				'dossier-nb'   => $this->numberFormat($data['comptes']),
				'n'            => '',
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => false,
				'expanded'     => true,
				'loaded'       => true,
			];

			$response[] = [
				'id'           => 'tbc-rapp',
				'list'         => 'Rapprochés',
				'dossier-nb'   => '<span class="class-tbc-rapp">'.$this->numberFormat($data['nb_rapp']).'</span>',
				'n'            => '',
				'dossiers-n'   => $data['dossiers_rapp'],
				'level'        => '1',
				'parent'       => "tbc-compte",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tbc-encours',
				'list'         => 'En cours',
				'dossier-nb'   => '<span class="class-tbc-encours">'.$this->numberFormat($data['nb_cpt_encour']).'</span>',
				'n'            => $this->numberFormat($data['nb_en_cours']),
				'dossiers-n'   => $data['dossiers_en_cours'],
				'level'        => '1',
				'parent'       => "tbc-compte",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tbc-pm',
				'list'         => 'Pièces manquantes',
				'n'            => '',
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => false,
				'expanded'     => true,
				'loaded'       => true,
			];

			$response[] = [
				'id'           => 'tbc-decaissements',
				'list'         => 'Décaissements',
				'dossier-nb'   => '<span class="class-tbc-decaissements">'.$this->numberFormat($data['nb_cpt_decaiss']).'</span>',
				'n'            => $this->numberFormat($data['nb_decaiss']),
				'dossiers-n'   => $data['dossiers_decaiss'],
				'level'        => '1',
				'parent'       => "tbc-pm",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tbc-encaissements',
				'list'         => 'Encaissements',
				'dossier-nb'   => '<span class="class-tbc-encaissements">'.$this->numberFormat($data['nb_cpt_encaiss']).'</span>',
				'n'            => $this->numberFormat($data['nb_encaiss']),
				'dossiers-n'   => $data['dossiers_encaiss'],
				'level'        => '1',
				'parent'       => "tbc-pm",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tbc-cheque',
				'list'         => 'Cheque',
				'dossier-nb'   => '<span class="class-tbc-cheque">'.$this->numberFormat($data['nb_cpt_chq']).'</span>',
				'n'            => $this->numberFormat($data['nb_cheque']),
				'dossiers-n'   => $data['dossiers_chq'],
				'level'        => '1',
				'parent'       => "tbc-pm",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];
		}

		//$dataNMoinsUn = $this->prepareDataTBEC($paramNMoinsUn);

		

		return $response;
	}


	public function pushInArray($array, $substr1)
	{
		$item = $substr1;

		if (!in_array($item, $array)) {
			array_push($array, $item);
		}

		return $array;
	}

	public function prepareDataOBM($param)
	{
		$repository = $this->loadRepository('Image');
		$obmRepository = $this->loadRepository('BanqueObManquante');

		$tabDossier = $tabCompte = $dossierRemise = $dossierLCR = $dossierFrais = $dossierVrt = $dossierCI = $dossierRelB = array();

		$data = array(
			'nb_remise'       => 0,
			'nb_frais'        => 0,
			'nb_dossier'      => 0,
			'nb_compte'       => 0,
			'nb_frais_cpt'=> 0,
			'nb_lcr_cpt'  => 0,
			'nb_remise_cpt'=> 0,
			'nb_vrt_cpt'=> 0,
			'nb_rel_b_cpt'=> 0,
			'nb_rcb_cpt'=> 0,
			'ob_manquantes'   => 0,
			'nb_chq_inconnus' => 0,
			'nb_chq_inconnus_cpt' => 0,
			'nb_rcb'          => 0,
			'nb_lcr'          => 0,
			'nb_vrt'          => 0,
			'nb_rel_b'          => 0,
			'nb_autres'       => 0,
			'list_dossier'    => '',
			'dossier_remise'  => '',
			'dossier_lcr'     => '',
			'dossier_frais'   => '',
			'dossier_vrt'     => '',
			'dossier_ci'      => ''
		);

		$user = $this->getUser();

		/*$chqI = $repository->getChequeIconnuByBanqueCompte($param,$user);*/
		
		$tabDossierCompte = array();

		/*foreach ($chqI as $value) {
			// Chèque inconnus
			$data['nb_chq_inconnus'] += $value->nb;
			$tabDossier = $this->pushInArray($tabDossier,$value->id);
			if (!in_array($value->numcompte, $tabDossierCompte)) {
				array_push($tabDossierCompte, $value->numcompte);
			} 
			$data['nb_chq_inconnus_cpt'] = $data['nb_chq_inconnus_cpt'] + 1;
			$dossierCI = $this->pushInArray($tabDossier,$value->id);

		}*/

		$dossierId = Boost::deboost($param['dossier'],$this);
		if($dossierId != 0){
			$dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossierId);
			$clotureMois = $this->getDoctrine()->getRepository('AppBundle:TbimagePeriode')
	                ->getAnneeMoisExercices($dossier, intval($param['exercice']));
			$banqueComptes = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->getBanquesComptes($dossier);
			$exercices = [];
	        for ($i = -2; $i < 3; $i++) $exercices[] = intval($param['exercice']) + $i;
			foreach ($banqueComptes as $key => $bc) {
				$releveManquantsTemps = $this->getDoctrine()->getRepository('AppBundle:ReleveManquant')
	                    ->createQueryBuilder('rm')
	                    ->where('rm.banqueCompte = :banqueCompte')
	                    ->andWhere('rm.exercice IN(:exercices)')
	                    ->setParameters([
	                        'banqueCompte' => $bc,
	                        'exercices' => $exercices
	                    ])
	                    ->getQuery()
	                    ->getResult();
	            $rMs = [];
	            foreach ($releveManquantsTemps as $releveManquantsTemp)
	            {
	                $rMs = array_merge($rMs, $releveManquantsTemp->getMois());
	            }
	            $rMs = array_map('trim',$rMs);
	            $releveManquants = array_intersect($clotureMois->ms, $rMs);
				if (!in_array($bc->getNumcompte(), $tabDossierCompte)) {
					array_push($tabDossierCompte, $bc->getNumcompte());
				}
				$data['nb_rel_b_cpt'] = $data['nb_rel_b_cpt'] + 1;
				$dossierRelB = $this->pushInArray($dossierRelB, $dossier->getNom()); 
				$data['nb_rel_b'] += count($releveManquants);
				$tabDossier = $this->pushInArray($tabDossier,$dossier->getId());
			}
		}else{
			$clientId  = Boost::deboost($param['client'],$this);
			$client = $this->getDoctrine()
                        ->getRepository('AppBundle:Client')
                        ->find(intval($clientId));
        	$dossiers = $this->getDoctrine()
                        	 ->getRepository('AppBundle:Dossier')
                        	 ->getDossiersClient($client, intval($param['exercice']));
            foreach ($dossiers as $k => $d) {
            	$clotureMois = $this->getDoctrine()->getRepository('AppBundle:TbimagePeriode')
	                ->getAnneeMoisExercices($d, intval($param['exercice']));
				$banqueComptes = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->getBanquesComptes($d);
				$exercices = [];
				for ($i = -2; $i < 3; $i++) $exercices[] = intval($param['exercice']) + $i;
				foreach ($banqueComptes as $key => $bc) {
					$releveManquantsTemps = $this->getDoctrine()->getRepository('AppBundle:ReleveManquant')
		                    ->createQueryBuilder('rm')
		                    ->where('rm.banqueCompte = :banqueCompte')
		                    ->andWhere('rm.exercice IN(:exercices)')
		                    ->setParameters([
		                        'banqueCompte' => $bc,
		                        'exercices' => $exercices
		                    ])
		                    ->getQuery()
		                    ->getResult();
		            $rMs = [];
		            foreach ($releveManquantsTemps as $releveManquantsTemp)
		            {
		                $rMs = array_merge($rMs, $releveManquantsTemp->getMois());
		            }
		            $rMs = array_map('trim',$rMs);
		            $releveManquants = array_intersect($clotureMois->ms, $rMs);
					if (!in_array($bc->getNumcompte(), $tabDossierCompte)) {
						array_push($tabDossierCompte, $bc->getNumcompte());
					}
					$data['nb_rel_b_cpt'] = $data['nb_rel_b_cpt'] + 1;
					$dossierRelB = $this->pushInArray($dossierRelB, $d->getNom()); 
					$data['nb_rel_b'] += count($releveManquants);
					$tabDossier = $this->pushInArray($tabDossier,$d->getId());
				}
            }
		}

		$result = $obmRepository->getOBManquantes($param,$user);

		foreach ($result as $key => $value) {
			switch ($value->souscategorie_id) {
				case 8:
					$tabDossier = $this->pushInArray($tabDossier,$value->id);
					if (!in_array($value->numcompte, $tabDossierCompte)) {
						array_push($tabDossierCompte, $value->numcompte);
					} 
					$data['nb_frais_cpt'] = $data['nb_frais_cpt'] + 1;
					$dossierFrais = $this->pushInArray($dossierFrais, $value->nomDossier);
					$data['nb_frais'] += count(json_decode($value->mois));

					break;

				case 5:
					$tabDossier = $this->pushInArray($tabDossier,$value->id);
					if (!in_array($value->numcompte, $tabDossierCompte)) {
						array_push($tabDossierCompte, $value->numcompte);
					}
					$data['nb_lcr_cpt'] = $data['nb_lcr_cpt'] + 1;
					$dossierLCR = $this->pushInArray($dossierLCR, $value->nomDossier);
					$data['nb_lcr'] += count(json_decode($value->mois));
					break;

				case 7:
					$tabDossier = $this->pushInArray($tabDossier,$value->id);
					if (!in_array($value->numcompte, $tabDossierCompte)) {
						array_push($tabDossierCompte, $value->numcompte);
					}
					$data['nb_remise_cpt'] = $data['nb_remise_cpt'] + 1;
					$dossierRemise = $this->pushInArray($dossierRemise, $value->nomDossier);
					$data['nb_remise'] += count(json_decode($value->mois));
					break;

				case 6:
					$tabDossier = $this->pushInArray($tabDossier,$value->id);
					$data['nb_vrt_cpt'] = $data['nb_vrt_cpt'] + 1;
					if (!in_array($value->numcompte, $tabDossierCompte)) {
						array_push($tabDossierCompte, $value->numcompte);
					}
					$data['nb_vrt'] += count(json_decode($value->mois));
					$dossierVrt = $this->pushInArray($dossierVrt, $value->nomDossier);
					break;
			}
			
		}

		$data['nb_dossier']           = count($tabDossier);
		$data['ob_manquantes']        = $data['nb_rel_b'] + $data['nb_remise'] + $data['nb_frais'] + $data['nb_lcr'] + $data['nb_vrt'] + $data['nb_rcb'] + $data['nb_autres'];
		$data['list_dossier']         = json_encode($tabDossier,0);
		$data['dossier_remise']       = json_encode($dossierRemise,0);
		$data['dossier_lcr']          = json_encode($dossierLCR,0);
		$data['dossier_frais']        = json_encode($dossierFrais,0);
		$data['dossier_vrt']          = json_encode($dossierVrt,0);
		$data['dossier_ci']           = json_encode($dossierCI,0);
		$data['dossier_rel_b']           = json_encode($dossierRelB,0);
		$data['nb_compte'] = count($tabDossierCompte);
		$data['nb_obm']  = $data['nb_frais_cpt'] + $data['nb_lcr_cpt'] + $data['nb_remise_cpt'] + $data['nb_vrt_cpt'] + $data['nb_rel_b_cpt'];

		return $data;
	}


	/**
	 * Traitement des données Opération bancaires manquantes
	 *
	 * @param string $client
	 * @param string $exercice
	 * @param string $dossier
	 *
	 * @return JsonResponse
	 */
	public function getBancairesManquantes($isDefault, $client, $exercice, $dossier)
	{

		$param = array(
			'client'   => $client,
			'exercice' => $exercice,
			'dossier'  => $dossier
		);

		if($isDefault){
			$response[] = [
				'id'           => 'obm-dossier',
				'list'         => 'Dossiers',
				'nb-dossiers'  => 0,
				'n'            => '',
				'dossiers-n'   => 0,
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'obm-compte',
				'list'         => 'Comptes',
				'nb-dossiers'  => 0,
				'n'            => '',
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'obm-manquantes',
				'list'         => 'OB manquantes',
				'nb-dossiers'  => 0,
				'n'            => 0,
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => false,
				'expanded'     => true,
				'loaded'       => true,
			];

			$response[] = [
				'id'           => 'obm-remises',
				'list'         => 'Remises en bq',
				'nb-dossiers'  => 0,
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => '1',
				'parent'       => "obm-manquantes",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'obm-cb',
				'list'         => 'Rélevés CB',
				'nb-dossiers'  => 0,
				'n'            => 0,
				'dossiers-n'   => '',
				'level'        => '1',
				'parent'       => "obm-manquantes",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'obm-vrt',
				'list'         => 'LCR, Virements',
				'nb-dossiers'  => 0,
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => '1',
				'parent'       => "obm-manquantes",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'obm-frais',
				'list'         => 'Frais bancaires',
				'nb-dossiers'  => 0,
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => '1',
				'parent'       => "obm-manquantes",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'obm-autres',
				'list'         => 'Autres',
				'nb-dossiers'  =>  0,
				'n'            => 0,
				'dossiers-n'   => '',
				'level'        => '1',
				'parent'       => "obm-manquantes",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];
		}else{
			$data = $this->prepareDataOBM($param);

			$response[] = [
				'id'           => 'obm-dossier',
				'list'         => 'Dossiers',
				'nb-dossiers'  => number_format($data['nb_dossier'], 0, '', ' '),
				'n'            => '',
				'dossiers-n'   => $data['list_dossier'],
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'obm-compte',
				'list'         => 'Comptes',
				'nb-dossiers'  => number_format($data['nb_compte'], 0, '', ' '),
				'n'            => '',
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'obm-manquantes',
				'list'         => 'OB manquantes',
				'nb-dossiers'  => number_format($data['nb_obm'], 0, '', ' '),
				'n'            => number_format($data['ob_manquantes'], 0, ',', ' '),
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => "null",
				'isLeaf'       => false,
				'expanded'     => true,
				'loaded'       => true,
			];

			$response[] = [
				'id'           => 'obm-remises',
				'list'         => 'Remises en bq',
				'nb-dossiers'  => '<span class="class-obm-remises">'.number_format($data['nb_remise_cpt'], 0, '', ' ').'</span>',
				'n'            => number_format($data['nb_remise'], 0, '', ' '),
				'dossiers-n'   => $data['dossier_remise'],
				'level'        => '1',
				'parent'       => "obm-manquantes",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'obm-cb',
				'list'         => 'Rélevés CB',
				'nb-dossiers'  => '<span class="class-obm-cb">'.number_format($data['nb_rel_b_cpt'], 0, '', ' ').'</span>',
				'n'            => number_format($data['nb_rel_b'], 0, '', ' '),
				'dossiers-n'   => $data['dossier_rel_b'],
				'level'        => '1',
				'parent'       => "obm-manquantes",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'obm-vrt',
				'list'         => 'LCR, Virements',
				'nb-dossiers'  => '<span class="class-obm-vrt">'.number_format(($data['nb_vrt_cpt'] + $data['nb_lcr_cpt']), 0, '', ' ').'</span>',
				'n'            => number_format(($data['nb_vrt'] + $data['nb_lcr']), 0, '', ' '),
				'dossiers-n'   => $data['dossier_vrt'],
				'level'        => '1',
				'parent'       => "obm-manquantes",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'obm-frais',
				'list'         => 'Frais bancaires',
				'nb-dossiers'  => '<span class="class-obm-frais">'.number_format($data['nb_frais_cpt'], 0, '', ' ').'</span>',
				'n'            => number_format($data['nb_frais'], 0, '', ' '),
				'dossiers-n'   => $data['dossier_frais'],
				'level'        => '1',
				'parent'       => "obm-manquantes",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'obm-autres',
				'list'         => 'Autres',
				'nb-dossiers'  =>  0,
				'n'            => number_format($data['nb_autres'], 0, '', ' '),
				'dossiers-n'   => '',
				'level'        => '1',
				'parent'       => "obm-manquantes",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];
		}

		return $response;
	}

	public function numberFormat($value)
	{
		return number_format($value, 0, '', ' ');
	}

	public function verifyMonths($moisManquants, $tabMoisCloture)
	{
		$manquants = true;

		foreach ($tabMoisCloture as $mois) {
			if (!in_array($mois, $moisManquants)) {
				$manquants = false;
				break;
			}
		}

		return $manquants;
	}

	/**
	 * Récupération des données Situation des comptes bancaires
	 *
	 *	@param array $param
	 *
	 * @return array
	 */
	public function prepareDataCB($param, $listDossier)
	{
		$user = $this->getUser();
		$exercice     = $param['exercice'];
		$repository   = $this->loadRepository('ReleveManquant');
		$result       = $repository->getNewSituationComptesBancaire($param,$listDossier);
		$nbMoisUn     = $nbMoisDeux = $nbMoisTrois = $nbAbsTotal = $nbIncomplet = $nbEnCours = $nbDossiers = $nbComptes = 0;
		$tabKeyMois   = $response = $listDossier = $tabDossier = $tabCompte = $betweens = array();
		$dossierAjour = $dossierAMMoins1 = $dossierAMMoins2 = $dossierIncomplets = $dossierEnCours = $dossierSansRB = '';

		foreach ($result as $key => $value) {

			$in_array = in_array($value->bc, $listDossier);

			if (!in_array($value->nom_dossier, $tabDossier)) {
				array_push($tabDossier, $value->nom_dossier);
			}

			if (!$in_array) {
				//$value->mois = $repository->moisManquant($param['client'],$value->dossier_id,$param['exercice'], $value->bc);

				//Rélevés avec mois manquants
				if ($value->mois != '') {
					
					$tabMoisManquants = explode(',', $value->mois);
					$moisManquants    = str_replace(' ', '', $tabMoisManquants);

					if ($value->cloture < 9) {
						$debutMois = ($exercice - 1) . '-0' . ($value->cloture + 1) . '-01' ;
					} else if ($value->cloture >= 9 && $value->cloture < 12) {
						$debutMois = ($exercice - 1) . '-' . ($value->cloture + 1) . '-01';
					} else{
						$debutMois = $exercice . '-01-01';
					}

					if ($value->cloture < 10) {
						$finMois = $exercice . '-0' . $value->cloture . '-01';
					} else {
						$finMois = $exercice . '-' . $value->cloture . '-01';
					}

					$k = array_key_exists($debutMois . '-' . $finMois, $betweens);
                    if ($k) {
                        $tabMoisCloture = $betweens[$debutMois . '-' . $finMois];
                    } else{
                        $tabMoisCloture = $this->getBetweenDate($debutMois, $finMois);
                        $betweens[$debutMois . '-' . $finMois] = $tabMoisCloture;
                    }
					$nbMMoisExist   = false;
					$count          = count($moisManquants);

					switch ($count) {

						//  A jour
						case 0:
							$nbMMoisExist = true;
							$nbMoisUn++;

							if ($dossierAjour == '') {
								$dossierAjour = $value->nom_dossier . '*' . $value->numcompte;
							} else{

								$tab = explode(',', $dossierAjour);

								if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
									$dossierAjour .= ',' . $value->nom_dossier . '*' . $value->numcompte;
								}

							}

							break;
						case 1:
							$tabKeyMois[$key] = array_intersect($tabMoisCloture, $moisManquants);
							break;
						
						case 2:
							$tabKeyMois[$key] = array_intersect($tabMoisCloture, $moisManquants);
							break;
						case 3:
							$tabKeyMois[$key] = array_intersect($tabMoisCloture, $moisManquants);
							break;

						// Sans RB
						case ($count >= 12):
							$nbMMoisExist = true;
							$resReleves = $this->getDoctrine()
                                               ->getRepository('AppBundle:Image')
                                               ->getInfoReleveByDossier($value->bc, $exercice);
                            if(count($resReleves) > 0){
                            	$nbIncomplet++;
                            	if ($dossierIncomplets == '') {
									$dossierIncomplets = $value->nom_dossier . '*' . $value->numcompte;
								} else{
									$tab = explode(',', $dossierIncomplets);

									if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
										$dossierIncomplets .= ',' . $value->nom_dossier . '*' . $value->numcompte;
									}

								}
                            }
							break;
						default:
							$nbMMoisExist = true;
							$nbIncomplet++;

							if ($dossierIncomplets == '') {
								$dossierIncomplets = $value->nom_dossier . '*' . $value->numcompte;
							} else{

								$tab = explode(',', $dossierIncomplets);

								if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
									$dossierIncomplets .= ',' . $value->nom_dossier . '*' . $value->numcompte;
								}

							}
							break;
					}

					if (!$nbMMoisExist) {

						$min = 13;
                        $now = new \DateTime();
                        foreach ($tabKeyMois[$key] as $key_m => $key_mois_m) {
                            if ($key_m < $min) {
                                $min = $key_m;
                            }
                        }

						$continue = true;
                        $lastIndex = -1;
                        foreach ($tabKeyMois[$key] as $k => $v){
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

						if ($continue) {

							if (intval($exercice) < $now->format('Y')) {

								switch ($min) {
									
									// A jour
									case 11:
										$nbMoisUn++;
										if ($dossierAjour == '') {
											$dossierAjour = $value->nom_dossier . '*' . $value->numcompte;
										} else{

											$tab = explode(',', $dossierAjour);

											if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
												$dossierAjour .= ',' . $value->nom_dossier . '*' . $value->numcompte;
											}

										}
										break;
									
									// A M-1
									case 10:
										$nbMoisDeux++;
										if ($dossierAMMoins1 == '') {
											$dossierAMMoins1 = $value->nom_dossier . '*' . $value->numcompte;
										} else{

											$tab = explode(',', $dossierAMMoins1);

											if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
												$dossierAMMoins1 .= ',' . $value->nom_dossier . '*' . $value->numcompte;
											}

										}
										break;
									
									// A M-2
									case 9:
										$nbMoisTrois++;
										if ($dossierAMMoins2 == '') {
											$dossierAMMoins2 = $value->nom_dossier . '*' . $value->numcompte;
										} else{

											$tab = explode(',', $dossierAMMoins2);

											if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
												$dossierAMMoins2 .= ',' . $value->nom_dossier . '*' . $value->numcompte;
											}

										}
										break;
									default:
										$nbIncomplet++;
										if ($dossierIncomplets == '') {
											$dossierIncomplets = $value->nom_dossier . '*' . $value->numcompte;
										} else{

											$tab = explode(',', $dossierIncomplets);

											if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
												$dossierIncomplets .= ',' . $value->nom_dossier . '*' . $value->numcompte;
											}

										}
										break;
								}
							} else {

	                            $now = new \DateTime();

	                            if (array_key_exists($min, $tabKeyMois[$key])) {
                                    $now = new \DateTime();
                                    $dateNow = intval($now->format('d'));
	                            	$datemin  = \DateTime::createFromFormat('Y-m-d', $tabKeyMois[$key][$min] . "-01");
									$interval = $now->diff($datemin);
                                    $diff = $interval->m + 1;
                                    if($dateNow <= 6 ){
                                        $diff = $interval->m;
                                    }

									if ($diff == 0 || $diff == 1) {
										$nbMoisUn++;
	                            		if ($dossierAjour == '') {
											$dossierAjour = $value->nom_dossier . '*' . $value->numcompte;
										} else{

											$tab = explode(',', $dossierAjour);

											if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
												$dossierAjour .= ',' . $value->nom_dossier . '*' . $value->numcompte;
											}

										}
									} elseif ($diff == 2) {
										$nbMoisDeux++;
	                            		if ($dossierAMMoins1 == '') {
											$dossierAMMoins1 = $value->nom_dossier . '*' . $value->numcompte;
										} else{

											$tab = explode(',', $dossierAMMoins1);

											if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
												$dossierAMMoins1 .= ',' . $value->nom_dossier . '*' . $value->numcompte;
											}

										}
									} elseif ($diff == 3) {
										$nbMoisTrois++;
	                            		if ($dossierAMMoins2 == '') {
											$dossierAMMoins2 = $value->nom_dossier . '*' . $value->numcompte;
										} else{

											$tab = explode(',', $dossierAMMoins2);

											if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
												$dossierAMMoins2 .= ',' . $value->nom_dossier . '*' . $value->numcompte;
											}

										}
									}else if($diff < 0){
										$nbIncomplet++;
	                            		if ($dossierIncomplets == '') {
											$dossierIncomplets = $value->nom_dossier . '*' . $value->numcompte;
										} else{

											$tab = explode(',', $dossierIncomplets);

											if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
												$dossierIncomplets .= ',' . $value->nom_dossier . '*' . $value->numcompte;
											}

										}
									}
	                            } else {
									$nbIncomplet++;
                            		if ($dossierIncomplets == '') {
										$dossierIncomplets = $value->nom_dossier . '*' . $value->numcompte;
									} else{

										$tab = explode(',', $dossierIncomplets);

										if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
											$dossierIncomplets .= ',' . $value->nom_dossier . '*' . $value->numcompte;
										}
									}
									
	                            }

							}
						} else { // Sans RB
							$nbIncomplet ++;
							if ($dossierIncomplets == '') {
								$dossierIncomplets = $value->nom_dossier . '*' . $value->numcompte;
							} else{

								$tab = explode(',', $dossierIncomplets);

								if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
									$dossierIncomplets .= ',' . $value->nom_dossier . '*' . $value->numcompte;
								}

							}
						}
					}

				} else { // Rélevés A jour
					$nbMoisUn++;
					if ($dossierAjour == '') {
						$dossierAjour = $value->nom_dossier . '*' . $value->numcompte;
					} else{

						$tab = explode(',', $dossierAjour);

						if (!in_array($value->nom_dossier . '*' . $value->numcompte, $tab)) {
							$dossierAjour .= ',' . $value->nom_dossier . '*' . $value->numcompte;
						}

					}
				}

				array_push($listDossier, $value->bc);
				$nbComptes++;
			}

			if (!in_array($value->bc, $tabCompte)) {
				array_push($tabCompte, $value->bc);
			}
		}

		$sansImages = $repository->getListeSansImage($param, $tabCompte,$user);
		$nbDossiers = count($tabDossier);

		// Sans images == sans RB
		foreach ($sansImages as $si) {
			if (!in_array($si->dossier, $tabDossier)) {
				array_push($tabDossier, $si->dossier);
				$nbDossiers++;
			}

			if ($dossierSansRB == '') {
				$dossierSansRB = $si->dossier . '*' . $si->numcompte;
				$nbComptes++;
			} else {
				$tab = explode(',', $dossierSansRB);

				if (!in_array($si->dossier . '*' . $si->numcompte, $tab)) {
					$dossierSansRB .= ',' . $si->dossier . '*' . $si->numcompte;
				}
				$nbComptes++;
			}

		}

		$nbValides         = $nbMoisUn + $nbMoisDeux + $nbMoisTrois + $nbIncomplet;
		$nbReleves         = $nbValides;
		$inexistants       = $nbComptes - ($nbReleves + $nbAbsTotal);
		$nbAbsTotal       += $inexistants;

		// Liste des dossiers à afficher dans qtip
		$dossiers = array(
			'a_jour'      => $dossierAjour, 
			'a_m_1'       => $dossierAMMoins1,
			'a_m_2'       => $dossierAMMoins2,
			'incomplets'  => $dossierIncomplets,
			'en_cours'    => $dossierEnCours,
			'inexistants' => $dossierSansRB,
			'list_dossier' => implode(',', $tabDossier)
		);

		$data = array(
			'nb_dossier'            => $nbDossiers,
			'nb_compte'             => $nbComptes,
			'nb_releve'             => $nbReleves,
			'nb_valide'             => $nbValides,
			'a_jour'                => $nbMoisUn,
			'a_m_1'                 => $nbMoisDeux,
			'a_m_2'                 => $nbMoisTrois,
			'incomplets'            => $nbIncomplet,
			'inexistants'           => $nbAbsTotal,
			// 'percent_inexistant' => $percentInexitants,
			'dossiers'              => $dossiers
		);

		return $data;
	}

	/**
	 * Calcul pourcentage de $nb1 par rapport à $nb2
	 *
	 * @param integer $nb1
	 * @param integer $nb2
	 *
	 * @return float 
	 */
	public function percentValue($nb1, $nb2)
	{
		if ($nb2 == 0) {
			return 0 . '%';
		}
		return round(($nb1 * 100) / $nb2) . '%';
	}


	/**
	 * Traitement des données Situation des comptes bancaires
	 *
	 * @param string $client
	 * @param string exercice
	 * @param string $dossier
	 *
	 * @return JsonResponse
	 */
	public function getComptesBancaires($isDefault, $client, $exercice, $dossier, $listDossier)
	{

		// N
		$param = array(
			'client'   => $client, 
			'exercice' => $exercice,
			'dossier'  => $dossier
		);

		if($isDefault){
			$response[] = [
				'id'           => 'tr-dossier',
				'list'         => 'Dossiers',
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => 0,
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'tr-compte',
				'list'         => 'Comptes',
				'n'            => 0,
				'dossiers-n'   => '',
				'level'        => 0,
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'tr-releve',
				'list'         => 'Rélevés Bancaires',
				'n'            => 0,
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => null,
				'isLeaf'       => false,
				'expanded'     => true,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'tr-a-jour',
				'list'         => 'à jour',
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => "1",
				'parent'       => "tr-releve",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tr-m-1',
				'list'         => 'à m-2',
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => "1",
				'parent'       => "tr-releve",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tr-m-2',
				'list'         => 'à m-3',
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => "1",
				'parent'       => "tr-releve",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tr-incomplets',
				'list'         => 'incomplets',
				'n'			   => 0,
				'dossiers-n'   => 0,
				'level'        => "1",
				'parent'       => "tr-releve",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tr-inexitants',
				'list'         => 'Sans Révelés',
				'n'            => 0,
				'dossiers-n'   => 0,
				'level'        => 0,
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];
		}else{
			$data = $this->prepareDataCB($param, $listDossier);
			$response[] = [
				'id'           => 'tr-dossier',
				'list'         => 'Dossiers',
				'n'            => '<span class="class-tr-dossier">'.$this->numberFormat($data['nb_dossier']).'</span>',
				'dossiers-n'   => $data['dossiers']['list_dossier'],
				'level'        => 0,
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'tr-compte',
				'list'         => 'Comptes',
				'n'            => '<span class="class-tr-compte">'.$this->numberFormat($data['nb_compte']).'</span>',
				'dossiers-n'   => '',
				'level'        => 0,
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'tr-releve',
				'list'         => 'Rélevés Bancaires',
				'n'            => '<span class="class-tr-releve">'.$this->numberFormat($data['nb_releve']).'</span>',
				'dossiers-n'   => '',
				'level'        => '0',
				'parent'       => null,
				'isLeaf'       => false,
				'expanded'     => true,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];

			$response[] = [
				'id'           => 'tr-a-jour',
				'list'         => 'à jour',
				'n'            => '<span class="class-tr-a-jour">'.$this->numberFormat($data['a_jour']).'</span>',
				'dossiers-n'   => $data['dossiers']['a_jour'],
				'level'        => "1",
				'parent'       => "tr-releve",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tr-m-1',
				'list'         => 'à m-2',
				'n'            => '<span class="class-tr-m-1">'.$this->numberFormat($data['a_m_1']).'</span>',
				'dossiers-n'   => $data['dossiers']['a_m_1'],
				'level'        => "1",
				'parent'       => "tr-releve",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tr-m-2',
				'list'         => 'à m-3',
				'n'            => '<span class="class-tr-m-2">'.$this->numberFormat($data['a_m_2']).'</span>',
				'dossiers-n'   => $data['dossiers']['a_m_2'],
				'level'        => "1",
				'parent'       => "tr-releve",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tr-incomplets',
				'list'         => 'incomplets',
				'n'			   => '<span class="class-tr-incomplets">'.$this->numberFormat($data['incomplets']).'</span>',
				'dossiers-n'   => $data['dossiers']['incomplets'],
				'level'        => "1",
				'parent'       => "tr-releve",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => 'ui-icon-blank'
			];

			$response[] = [
				'id'           => 'tr-inexitants',
				'list'         => 'Sans Révelés',
				'n'            => '<span class="class-tr-incomplets">'.$this->numberFormat($data['inexistants']).'</span>',
				'dossiers-n'   => $data['dossiers']['inexistants'],
				'level'        => 0,
				'parent'       => "null",
				'isLeaf'       => true,
				'expanded'     => false,
				'loaded'       => true,
				'icon'         => "ui-icon-blank"
			];
		}

        return $response;

	}

	public function getTravauxARealiser($clientId, $exercice, $dossierId, $moisData, $isGetInfo = false)
	{
    	$clientId = Boost::deboost($clientId,$this);
    	$dossierId = Boost::deboost($dossierId,$this);
    	$dossier = null;
    	$data = null;

		/*$taches = $this->getDoctrine()
					   ->getRepository('AppBundle:Tache')
    				   ->findAll();
    	if(count($taches) > 0){
    		foreach ($taches as $key => $tache)
	    		$response['colModel'][$key] = $tache->getNom();
    	}*/

    	$client = $this->getDoctrine()
    					->getRepository('AppBundle:Client')
    					->find($clientId);

    	if($dossierId != 0)
	    	$dossier = $this->getDoctrine()
	    					->getRepository('AppBundle:Dossier')
	    					->find($dossierId);

		$periode = new \DateTime();
		if(intval($moisData) != 0){
			$annee = $periode->format('Y');
			$periode = new \DateTime($annee.'-'.$moisData.'-01');
		}

        $events = $this->getDoctrine()->getRepository('AppBundle:Calendar')
            ->taches3EventsNoUpdates($client, $dossier, $periode,
                true,true,false,
                true,true,true,9);

        $response = $this->getDataToPilotageTab($events, $isGetInfo, $periode);

		return $response;
	}

	public function getDataToPilotageTab($events, $isGetInfo, $periode)
	{
		//var_dump($events);die;
		$liste = [];
		$models = [];
		$data = [];
		$names = [];
		$res = [];
		$moisArray = [];
		$moisN = $periode->format('m');
		$moisSuiv = $moisN;
		for ($i=0; $i < 11; $i++) { 
			if($moisSuiv > 12){
				$moisArray[] = $moisSuiv - 12;
			}else{
				$moisArray[] = $moisSuiv;
			}
			$moisSuiv++;
		}
		if(count($events['taches']) > 0){
			foreach ($events['taches'] as $k => $v) {
				$date = $v['start'];
				$dateT = \DateTime::createFromFormat('Y-m-d',$date);
				$jour = $dateT->format('d');
				$mois = $dateT->format('n');
				if(intval($mois) == intval($moisN)){ 
					if(!in_array(intval($jour), $names))
						$names[] = $jour;
					$liste[$jour][$k] = $v;
				}else if(in_array($mois, $moisArray)){
					//$keyMois = array_keys($moisArray, $mois);
					$nameKeyMois = 'M+'.$mois;
					if(!in_array($nameKeyMois, $names))
						$names[] = $nameKeyMois;
					$liste[$nameKeyMois][$k] = $v; 
				}
			}
		}
		sort($names);

		$nomTachesArray = [];
		$dataFinal = [];
		if(count($names) > 0){
			foreach ($names as $key => $val) {
				$listeData = $this->getDataToTravauARealiserByListDate($liste[$val], $nomTachesArray, $data, $val);
				$data = array_merge($data, $listeData['data']);
				foreach ($listeData['nomTache'] as $k => $v) {
					$nomTachesArray[] = $v;
				}
			}
		}

		//$nomArray = ['Nb de taches', 'Images a traiter'];
		$nbTache = [];
		$datescan = '';
		foreach ($liste as $key => $value) {
			$isSuivArray = explode('+', $key);
			$isSuiv = (count($isSuivArray) > 1) ? true : false;
			$nbTache['nbTache'][$key] = number_format(count($value), 0, ',', ' ');
			$nbTache['imgATraiter'][$key] = number_format($this->getImageAtraiterByDatescan($value, $isSuiv), 0 , ',', ' ');
		}

		$nbTache['nbTache']['id'] = 'nb_de_taches';
		$nbTache['nbTache']['Taches'] = 'Nb de taches';
		$nbTache['imgATraiter']['id'] = 'images_a_traiter';
		$nbTache['imgATraiter']['Taches'] = 'Images à traiter';

		$data = $data + $nbTache;

		if($isGetInfo){
			$dataFinal = $liste;
		}else{
			foreach ($data as $value) 
				$dataFinal[] = $value;
		}

		sort($names);
		$nameSup = [];
		$nameInf = [];
		$newNames = [];
		foreach ($names as $key => $value) {
			$explodeNameSuiv = explode('+', $value);
			if(count($explodeNameSuiv) > 1){
				if(intval($moisN) < intval($explodeNameSuiv[1])){
					$nameSup[] = $explodeNameSuiv[1];
				}else{
					$nameInf[] = $explodeNameSuiv[1];
				}
			}else{
				$newNames[] = $value;
			}
		}

		sort($nameInf);
		sort($nameSup);

		foreach ($nameSup as $key => $value) {
			$newNames[] = 'M+'.$value;
		}
		foreach ($nameInf as $key => $value) {
			$newNames[] = 'M+'.$value;
		}

		$res['names'] = $newNames;
		$res['taches'] = $dataFinal;
		$res['date'] = intval($moisN);
		/*if(count($events['gcal']) > 0)
			$liste[] = $events['gcal'];*/
		return $res; 
	}

	public function getDataToTravauARealiserByListDate($listepardate, $nomTacheArray, $data, $jourEtmois)
	{
		$tacheArray = [];
		$res = [];
		$nomArray = [];
		foreach ($listepardate as $k => $v) {
			$title = '';
			$nomTache = '';
			$title = explode('*', $v['title']);
			$nomTache = $title[0];
			if(!in_array($nomTache, $nomArray))
				$nomArray[] = $nomTache;
			$tacheArray[$nomTache][$k] = $v;
		}

		foreach ($nomArray as $k => $v) {
			$newTache = [];
			if(in_array($v, $nomTacheArray)){
				$newTache = [
					$jourEtmois    => count($tacheArray[$v])
				];
				$data[$v] = $data[$v] + $newTache;
			}else{
				$data[$v] = [
					'id'		   => $jourEtmois,
					'Taches'       => $v,
					$jourEtmois    => count($tacheArray[$v])
				];
			}
		}
		$res['nomTache'] = $nomArray;
		$res['data'] = $data;
		return $res;
	}

	public function dashboardGetInfoTacheAction(Request $request)
	{
		$client = $request->request->get('client');
		$exercice = $request->request->get('exercice');
		$dossier = $request->request->get('dossier');
		$moisData = $request->request->get('moisData');
		$moisC = $request->request->get('moisC');
		$tache = $request->request->get('tache');
		$tacheDate = $request->request->get('tacheDate');
		$idModal = $request->request->get('idModal');
		$tar = $this->getTravauxARealiser($client,$exercice,$dossier,$moisData,true);
		$dossierArray = [];
		$depasserArray = [];
		$dateTitreTab = [];
		$isTitreDepasse= false;
		$titre = '';
		foreach ($tar['taches'][$tacheDate] as $k => $v) {
			$title = '';
			$title = explode('*', $v['title']);
			$nomTache = $title[0];
			if($nomTache == $tache){
				if(!in_array($v['dossier'], $dossierArray)){
					$dateTitre = $v['start'];
					$dateTitreT = \DateTime::createFromFormat('Y-m-d',$dateTitre);
					$jourTitre = $dateTitreT->format('d');
					if(!in_array($jourTitre, $dateTitreTab)){
						$dateTitreTab[] = $jourTitre;
						if($titre == '')
							$titre = $tache.': Le replace '.$moisC;
					}
					$jour = $dateTitreT->format('d');
					$dossierArray[] = $v['dossier'];
					$depasserArray[$v['dossier']]['depasser'] = $v['depasser'];
					$depasserArray[$v['dossier']]['resp'] = $v['responsable'];
				}
			}
		}
		$date = new \DateTime();
		if(intval($moisData) != 0){
			$annee = $date->format('Y');
			$date = new \DateTime($annee.'-'.$moisData.'-01');
		}
		$detailTacheData = $this->getDetailTache($dossierArray, $date, $depasserArray);
		$detailTacheImpute = $detailTacheData['impute'];
        $colorRb = '';
        $colorOb = '';
        $colorRappro = '';
        foreach ($detailTacheImpute as $key => $value) {
            if ($value['rb2'] == 'Imp.') {
                if ($value['ecart'] == 0) {
                    $colorRb = '#008000';
                }else{
                    $colorRb = '#ffd700';
                }
                if ($value['acontroler'] > 0 || $value['m'] == 'Inc.' || $value['m'] == 'Auc.') {
                    $colorRb = '#e95443';
                }
            }else{
                $colorRb = '#e95443';
            }
            $detailTacheImpute[$key]['colorRb'] = $colorRb;
            if($value['ob'] == 'PB'){
                $colorOb = '#e95443';
            }else{
                $colorOb = '#008000';
            }
            $detailTacheImpute[$key]['colorOb'] = $colorOb;
            if($value['nbr_rapproche'] == 100){
                $colorRappro = '#008000';
            }
            if($value['m'] == 'Inc.' || $value['ob'] == 'PB' || $value['m'] == 'Auc.'){
                $colorRappro = '#e95443';
            }else{
                $colorRappro = '#ffd700';
            }
            $detailTacheImpute[$key]['colorRappro'] = $colorRappro;
        }
        sort($dateTitreTab);
        $dateTitreTabToStr = implode(', ',$dateTitreTab);
    	$titre = str_replace("replace", $dateTitreTabToStr ,$titre);

        return $this->render('@General/Grid/taches-details.html.twig',[
            'detailsTaches' => $detailTacheImpute,
            'showCompte' => $detailTacheData['showCompte'],
            'titre' => $titre,
            'idModal' => $idModal
        ]);
	}

	public function getDetailTache($dossierArray, $date, $depasserArray){
        $param = [];
        $param['client'] = 0;
        $param['dossier'] = $dossierArray;
        $param['exercice'] = $date->format('Y');

        $data = $this->getDoctrine()->getRepository('AppBundle:Image')
            ->getListeImpute($param);

        $dateEnvoi = [];
        $derniereDemande = $this->getDoctrine()
                                ->getRepository('AppBundle:Image')
                                ->getDerniereDemandeDrt($param['dossier'], $param['exercice']);

        if(count($derniereDemande) > 0){
        	foreach ($derniereDemande as $key => $value) {
        		$dateEnvoi[$value->dossierId] = $value->date_envoi;
        	}
        }

        if(count($data['imputees'] > 0)){
            $tab_imputees = array();
            $tab_key_mois = array();
            $tab_exist_comptes = array();
            $last_key = 0;
            $exercice = $param['exercice'];
            $dossier = $param['dossier'];
            $client = $param['client'];
            $betweens = array();
            $tab_dossier_imp = array();
            $showCompte = false;
            $dossierTab = [];
            foreach ($data['imputees'] as $key => $value) {
            	if($value->bc_etat){
            		$dossierTab[] = $value->dossier_id;
	                if (!empty($value->mois)) {
	                    $tabMoisManquants = explode(',', $value->mois);
	                    $moisManquants = str_replace(' ', '', $tabMoisManquants);
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
	                    switch (count($moisManquants)) {
	                        case 0:
	                            $nb_m_mois_exist = true;
	                            $tab_imputees[$key]['m'] = 'M-1';
	                            break;
	                        case 1:
	                            $tab_key_mois[$key] = array_intersect($tab_mois_cloture, $moisManquants);
	                            break;
	                        case 2:
	                            $tab_key_mois[$key] = array_intersect($tab_mois_cloture, $moisManquants);
	                            break;
	                        case 3:
	                            $tab_key_mois[$key] = array_intersect($tab_mois_cloture, $moisManquants);
	                            break;
	                        case 12:
	                            $nb_m_mois_exist = true;
	                            //jerena aloha raha mis relevé ihany le banque amin'ny alalan'ny dossier
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
	                    if ($value->cloture < 10) {
	                        $fin_mois = ($exercice) . '-0' . ($value->cloture) . '-01';
	                    } else {
	                        $fin_mois = ($exercice) . '-' . ($value->cloture) . '-01';
	                    }
	                }

	                $nbr_rapproche = ($value->nb_r != 0) ? (($value->nb_lettre + $value->nb_clef + $value->nb_ecriture_change) * 100) / $value->nb_r : 0;
	                $nbr_pc_manquant = ($value->nb_r != 0) ? ($value->nb_r - ($value->nb_lettre + $value->nb_clef)) : 0;
	                $tab_imputees[$key]['nb_pc_manquant'] = $this->ifNull($nbr_pc_manquant);
	                $tab_imputees[$key]['chq_inconnu'] = $this->ifNull($value->chq_inconnu);
	                $tab_imputees[$key]['nbr_rapproche'] = $this->ifNull($nbr_rapproche);
	                $tab_imputees[$key]['alettrer'] = ($value->a_lettrer) ? $value->a_lettrer : 0;
	                $tab_imputees[$key]['date_envoi'] = (array_key_exists($value->dossier_id, $dateEnvoi)) ? $dateEnvoi[$value->dossier_id] : '';
	                $tab_imputees[$key]['dossier'] = substr($value->dossier,0,15);
	                $tab_imputees[$key]['depasser'] = ($depasserArray[$value->dossier_id]['depasser'] == 1) ? 'Oui' : 'Non';
	                $tab_imputees[$key]['responsable'] = $depasserArray[$value->dossier_id]['resp'];
	                $dateCloture = new \ DateTime($fin_mois);
	                $tab_imputees[$key]['cloture'] = $dateCloture->format('m-y');
	                if(in_array($value->dossier_id, $tab_dossier_imp)) $showCompte = true;
	                $tab_dossier_imp[] = $value->dossier_id;
	                $tab_imputees[$key]['compte'] = substr($value->comptes,-5);
	                $tab_imputees[$key]['nomBanque'] = $value->banque;

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
	                $dataObMq[1]['libelle'] = 'Relevé  LCR';

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

	                $imageOb = $this->getDoctrine()
	                                ->getRepository('AppBundle:Image')
	                                ->getListImageBanqueGestionTache($value->dossier_id, $exercice, 0, 1, 1901, 1, -1);
	                 foreach ($imageOb as $im) {
	                    if($im->ctrl_saisie > 2 && $im->valider != 100){
	                        $cartCredRel += 1;
	                    }
	                }
	                $dataObMq[4]['nb'] = $cartCredRel;
	                $dataObMq[4]['libelle'] = 'Cartes de crédit relevé';

	                $imageOb = $this->getDoctrine()
	                                ->getRepository('AppBundle:Image')
	                                ->getListImageBanqueGestionTache($value->dossier_id, $exercice, 0, 1, 2791, 1, -1);
	                 foreach ($imageOb as $im) {
	                    if($im->ctrl_saisie > 2 && $im->valider != 100){
	                        $cartDebRel += 1;
	                    }
	                }
	                $dataObMq[5]['nb'] = $cartDebRel;
	                $dataObMq[5]['libelle'] = 'Cartes Débits tickets';

	                $isOb = false;
	                foreach ($dataObMq as $dataOb) {
	                    if($dataOb['nb'] > 0){
	                        $isOb = true;
	                    }
	                }
	                $tab_imputees[$key]['ob'] = ($isOb) ? 'PB' : 'OK';
	                $tab_imputees[$key]['data_ob_m'] = json_encode($dataObMq, true);
	                $param['periode'] = 4;
                    $param['dossier'] = $value->dossier_id;
	                $dataRb1AC = $this->getDoctrine()
	                                  ->getRepository('AppBundle:Image')
	                                  ->getRb1AControler($param);
	                $tab_imputees[$key]['acontroler'] = $dataRb1AC['imgSaisieKo'];
	                $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($value->banque_compte_id);
	                $soldeDebut = $this->getDoctrine()->getRepository('AppBundle:Image')->getSoldes($banqueCompte,$exercice);
	                $soldeFin = $this->getDoctrine()->getRepository('AppBundle:Image')->getSoldes($banqueCompte,$exercice,false);

	                $mouvements = $this->getDoctrine()
	                                    ->getRepository('AppBundle:Image')
	                                    ->getMouvement($exercice, $value->banquecompte_id);

	                $ecart = (float)($soldeFin - $soldeDebut - $mouvements);

	                $tab_imputees[$key]['ecart'] = round($ecart);
	                $tab_imputees[$key]['rb2'] = 'Imp.';
	                $last_key = $key;
            		$tab_exist_comptes[] = $value->numcompte;
	            }
	        }
        }
        $last_key++;
        $listSansImage = $this->getDoctrine()
                              ->getRepository('AppBundle:Image')
                              ->getListeImputeSansImage($client, $dossier, $tab_exist_comptes);

        foreach ($listSansImage as $key=>$value){
        	$tab_imputees[$last_key]['nb_pc_manquant'] = 0;
            $tab_imputees[$last_key]['chq_inconnu'] = 0;
            $tab_imputees[$last_key]['nbr_rapproche'] = 0;
            $tab_imputees[$last_key]['alettrer'] = 0;
            $tab_imputees[$last_key]['date_envoi'] = '';
            $tab_imputees[$last_key]['dossier'] = substr($value->dossier,0,15);
            $tab_imputees[$last_key]['depasser'] = ($depasserArray[$value->dossier_id]['depasser'] == 1) ? 'Oui' : 'Non';
            $tab_imputees[$last_key]['responsable'] = $depasserArray[$value->dossier_id]['resp'];
            if ($value->cloture < 10) {
                $fin_mois = ($exercice) . '-0' . ($value->cloture) . '-01';
            } else {
                $fin_mois = ($exercice) . '-' . ($value->cloture) . '-01';
            }
            $dateCloture = new \ DateTime($fin_mois);
            $tab_imputees[$last_key]['cloture'] = $dateCloture->format('m-y');
            $tab_imputees[$last_key]['compte'] = substr($value->comptes,-5);
            $tab_imputees[$last_key]['nomBanque'] = $value->banque;
         	$tab_imputees[$last_key]['ob'] = 'PB';
            $tab_imputees[$last_key]['data_ob_m'] = '';
            if(in_array($value->dossier_id, $tab_dossier_imp)) $showCompte = true;
            $tab_dossier_imp[] = $value->dossier_id;
            $tab_imputees[$last_key]['ecart'] = '';
            $tab_imputees[$last_key]['rb2'] = 'Nn imp.';
            $tab_imputees[$last_key]['m'] = 'Auc.';
            $last_key++;
        }
        $data = [];
        $data['impute'] = $tab_imputees;
        $data['showCompte'] = $showCompte;
        $data['dossierId'] = $showCompte;
        return $data;
    }

    public function ifNull($value,$null = 0)
    {
        $value = ($value) ? $value :  $null;

        return $value;
    }

    public function getImageAtraiterByDatescan($data, $isSuiv)
    {
		$isdate = false;
		$dossierTab = [];
    	foreach ($data as $key => $value) {
    		if(!$isdate){
	    		if(!$isSuiv){
	    			$debut = $value['start'];
	    			$fin = '';
	    			$isdate = true;
	    		}else{
	    			$debut = $value['start'];
					$date = new \DateTime($value['start']); 
	    			$fin = $date->format('Y-m-t');
	    			$isdate = true;
	    		}
	    	}
			$dossierTab[] = $value['dossier'];
    	}
		$debutExplodde = explode('-', $debut);
		$exercice = $debutExplodde[0];

    	return $this->getDoctrine()->getRepository('AppBundle:Image')->getImageAtraiterByDatescan($dossierTab,$exercice);
    }
}
