<?php

namespace FacturationBundle\Controller;

use AppBundle\Entity\FactAnnee;
use AppBundle\Entity\FactMoisSaisi;
use AppBundle\Entity\FactSaisie;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FacturationController extends Controller
{
    /**
     * Index de la facturation finale
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('FacturationBundle:Facturation:index.html.twig');
    }

    /**
     * Listes de paramètres et date pour jqGrid de
     * la facturation finale
     *
     * @param Request $request
     * @param $client
     * @param $mois : le mois de saisie de la facturation
     * @param $exercice
     * @param FactAnnee $annee_tarif : année du tarif appliqué
     * @param int $recalculer : si = 1, on doit d'abord recalculer la facturation avant de l'afficher
     * @return JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function facturationAction(Request $request, $client, $mois, $exercice, FactAnnee $annee_tarif, $recalculer = 0)
    {
        if ($request->isXmlHttpRequest()) {
            $jq_integer_format = ['thousandsSeparator' => " ", 'defaultValue' => ''];
            $jq_number_format = ['decimalSeparator' => ".", 'thousandsSeparator' => " ", 'decimalPlaces' => 2, 'defaultValue' => ''];
            $client_id = Boost::deboost($client, $this);
            $the_client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
            if ($the_client) {
                /* Recalculer les presations de tous les dossier du client */
                if ($recalculer == 1) {
                    $this->getDoctrine()
                        ->getRepository('AppBundle:FactSaisie')
                        ->recalculerSaisieClient($the_client, $mois, $exercice, $annee_tarif);
                }

                /* Colonnes fixes */
                $col_name_fixe1 = ['Dossier', 'Clôture', 'Responsable', 'Régime<br>Fiscal', 'Cumul<br>Images', 'Tarif<br>Appliqué'];
                $col_name_fixe2 = ['TOTAL', 'Remise', 'TOTAL NET', 'Pu par Ligne'];
                $col_model_fixe1 = [
                    ['name' => 'fact_dossier', 'index' => 'fact_dossier', 'editable' => false, 'sortable' => false, 'width' => 200,
                        'classes' => 'js-fact-dossier'],
                    ['name' => 'fact_cloture', 'index' => 'fact_cloture', 'editable' => false, 'align' => 'center', 'sortable' => false,
                        'width' => 80, 'classes' => 'js-fact-cloture'],
                    ['name' => 'fact_responsable', 'index' => 'fact_responsable', 'editable' => false, 'sortable' => false, 'width' => 150,
                        'classes' => 'js-fact-responsable'],
                    ['name' => 'fact_regime_fiscal', 'index' => 'fact_regime_fiscal', 'editable' => false, 'align' => 'center', 'sortable' => false,
                        'width' => 60, 'classes' => 'js-fact-regime-fiscal'],
                    ['name' => 'fact_cumul_image', 'index' => 'fact_cumul_image', 'editable' => false, 'align' => 'center', 'sortable' => false,
                        'width' => 60, 'classes' => 'js-fact-cumul-image'],
                    ['name' => 'fact_tarif', 'index' => 'fact_tarif', 'editable' => false, 'align' => 'center', 'sortable' => false,
                        'width' => 60, 'classes' => 'js-fact-tarif'],
                ];
                $col_model_fixe2 = [
                    ['name' => 'fact_total', 'index' => 'fact_total', 'editable' => false, 'align' => 'center', 'sortable' => false,
                        'width' => 100, 'formatter' => 'number', 'formatoptions' => $jq_number_format, 'classes' => 'js-fact-total'],
                    ['name' => 'fact_remise', 'index' => 'fact_remise', 'editable' => false, 'align' => 'center', 'sortable' => false,
                        'width' => 100, 'formatter' => 'number', 'formatoptions' => $jq_number_format, 'classes' => 'js-fact-remise'],
                    ['name' => 'fact_total_net', 'index' => 'fact_total_net', 'editable' => false, 'align' => 'center', 'sortable' => false,
                        'width' => 100, 'formatter' => 'number', 'formatoptions' => $jq_number_format, 'classes' => 'js-fact-total-net'],
                    ['name' => 'fact_pu_ligne', 'index' => 'fact_pu_ligne', 'editable' => false, 'align' => 'center', 'sortable' => false,
                        'width' => 100, 'formatter' => 'number', 'formatoptions' => $jq_number_format, 'classes' => 'js-fact-pu-ligne'],
                ];

                $saisies = $this->getDoctrine()
                    ->getRepository('AppBundle:FactSaisie')
                    ->getAllSaisieByClientAndMoisAndExercice($the_client, $mois, $exercice, $annee_tarif);

                $code_prestations = [];
                $code_prestation_prix = [];
                $total_prix = 0;
                $total_prix_net = 0;
                $total_remise = 0;

                $rows_data = [];
                $row_data = [];
                $i = 0;

                /* Unités realisés et prix des prestation par codes */
                /* @var FactSaisie $saisie */
                foreach ($saisies as $saisie) {
                    $show_quantite = $saisie->getFactTarifClient()->getShowQuantite();
                    $code = $saisie->getFactTarifClient()->getFactPrestationClient()->getFactPrestation()->getCode();
                    if ($show_quantite) {
                        $row_data['fact_' . $code] = $saisie->getQuantite();
                    } else {
                        $row_data['fact_' . $code] = $saisie->getUniteRealise();
                    }
                    $row_data['fact_prix_' . $code] = floatval($saisie->getPrix());

                    if ($saisie->getPrix()) {
                        $total_prix += floatval($saisie->getPrix());
                    }
                    if ($saisie->getPrixNet()) {
                        $total_prix_net += floatval($saisie->getPrixNet());
                    }
                    if ($saisie->getRemiseVolume()) {
                        $total_remise += floatval($saisie->getRemiseVolume());
                    }

                    /* @var FactSaisie[] $saisies */
                    if ($i < count($saisies)) {
                        $dossier_id = $saisies[$i]->getDossier()->getId();
                        $dossier = $saisies[$i]->getDossier()->getNom();
                        if ($i == count($saisies) - 1) {
                            $row_data['id'] = $saisie->getId();
                            $row_data['fact_dossier'] = $dossier;
                            $row_data['fact_responsable'] = '';
                            $row_data['fact_tarif'] = $annee_tarif->getAnnee();
                            $cloture = $this->getDoctrine()
                                ->getRepository('AppBundle:Dossier')
                                ->getDateCloture($saisie->getDossier(), $exercice);
                            $row_data['fact_cloture'] = $cloture->format('d-m-Y');
                            $row_data['fact_regime_fiscal'] = $saisie->getDossier()->getRegimeFiscal() ? $saisie->getDossier()->getRegimeFiscal()->getLibelle() : "";
                            $row_data['fact_total'] = round($total_prix, 2);
                            $row_data['fact_remise'] = round($total_remise, 2);
                            $row_data['fact_total_net'] = round($total_prix_net, 2);
                            $pu_ligne = 0;
                            if (isset($row_data['fact_220']) && floatval($row_data['fact_220']) != 0) {
                                $pu_ligne = $total_prix / floatval($row_data['fact_220']);
                            }
                            $row_data['fact_pu_ligne'] = round($pu_ligne, 2);

                            $rows_data[] = $row_data;
                        } else {
                            $next_dossier_id = $saisies[$i + 1]->getDossier()->getId();

                            if ($next_dossier_id != $dossier_id) {
                                $row_data['id'] = $saisie->getId();
                                $row_data['fact_dossier'] = $dossier;
                                $row_data['fact_responsable'] = '';
                                $row_data['fact_tarif'] = $annee_tarif->getAnnee();
                                $cloture = $this->getDoctrine()
                                    ->getRepository('AppBundle:Dossier')
                                    ->getDateCloture($saisie->getDossier(), $exercice);
                                $row_data['fact_cloture'] = $cloture->format('d-m-Y');
                                $row_data['fact_regime_fiscal'] = $saisie->getDossier()->getRegimeFiscal() ? $saisie->getDossier()->getRegimeFiscal()->getLibelle() : "";
                                $row_data['fact_total'] = round($total_prix, 2);
                                $row_data['fact_remise'] = round($total_remise, 2);
                                $row_data['fact_total_net'] = round($total_prix_net, 2);
                                $pu_ligne = 0;
                                if (isset($row_data['fact_220']) && floatval($row_data['fact_220']) != 0) {
                                    $pu_ligne = $total_prix / floatval($row_data['fact_220']);
                                }
                                $row_data['fact_pu_ligne'] = round($pu_ligne, 2);

                                $rows_data[] = $row_data;

                                $row_data = [];
                                $total_prix = 0;
                                $total_remise = 0;
                                $total_prix_net = 0;
                            }
                        }
                    }

                    if (!in_array($code, $code_prestations)) {
                        $code_prestations[] = strval($code);
                        $code_prestation_prix[] = 'prix_' . $code;
                    }


                    $i++;
                }
                sort($code_prestations);
                sort($code_prestation_prix);

                /* Colonnes variables */
                $col_model_variable = [];
                foreach ($code_prestations as $code_prestation) {
                    $col_model_variable[] = ['name' => 'fact_' . $code_prestation, 'index' => 'fact_' . $code_prestation, 'editable' => false, 'align' => 'center', 'sortable' => false,
                        'width' => 80, 'formatter' => 'integer', 'formatoptions' => $jq_integer_format, 'classes' => 'js-fact-' . $code_prestation];
                }
                foreach ($code_prestation_prix as $code_prestation) {
                    $col_model_variable[] = ['name' => 'fact_' . $code_prestation, 'index' => 'fact_' . $code_prestation, 'editable' => false, 'align' => 'center', 'sortable' => false,
                        'width' => 80, 'formatter' => 'number', 'formatoptions' => $jq_number_format, 'classes' => 'js-fact-' . $code_prestation];
                }

                /* Fusion des colonnes fixes et variables */
                $col_names = array_merge($col_name_fixe1, $code_prestations, $code_prestation_prix, $col_name_fixe2);

                /* ColModels */
                $col_models = array_merge($col_model_fixe1, $col_model_variable, $col_model_fixe2);

                /* Colonnes avec total dans Footer jqGrid */
                $col_with_total = array_merge($code_prestations, $code_prestation_prix, ['total', 'total_net', 'remise']);
                $nb_ligne_client = $this->getDoctrine()
                    ->getRepository('AppBundle:FactSaisie')
                    ->getNbLigneClient($the_client, $exercice, $mois);
                $data = [
                    'col_names' => $col_names,
                    'col_models' => $col_models,
                    'rows_data' => $rows_data,
                    'col_with_total' => $col_with_total,
                    'nb_ligne_client' => $nb_ligne_client,
                    'code_prestations' => $code_prestations,
                ];

                return new JsonResponse(json_encode($data));
            } else {
                throw new NotFoundHttpException('client introuvable');
            }
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Exportation de la facturation en Excel
     * Source de donnée = jqGrid (ajax)
     *
     * @param Request $request
     * @param $client
     * @param $mois
     * @param $exercice
     * @param FactAnnee $annee_tarif
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportExcelAction(Request $request, $client, $mois, $exercice, FactAnnee $annee_tarif)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        if ($the_client) {
            /* Titre des colonnes */
            $colNames = json_decode($request->request->get('colNames'));
            /* Nom des colonnes */
            $colModels = json_decode($request->request->get('colModel'));
            /* Valeur des colonnes */
            $rowDatas = json_decode($request->request->get('rowData'));
            /* Total des colonnes */
            $footerDatas = json_decode($request->request->get('footerData'));
            /* Code des prestations */
            $code_prestations = json_decode($request->request->get('codePrestation'));

//            var_dump($colNames);
//            var_dump($colModel);
//            var_dump($rowDatas);
//            var_dump($footerDatas);
//            var_dump($code_prestations);

            setlocale(LC_TIME, 'fr-FR', 'fr');
            $the_mois = \DateTime::createFromFormat('d-m-Y', '01-' . $mois);

            /* Création de l'Excel */
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
            /* Titre */
            $title = "Facturation_" . $the_client->getNom() . '_' . Boost::getMoisLettre($the_mois->format('m'), false, true) . '-' . $the_mois->format('Y');
            $phpExcelObject->getProperties()->setCreator("picdata")
                ->setLastModifiedBy($this->getUser()->getPrenom() . ' ' . $this->getUser()->getNom())
                ->setTitle($title)
                ->setSubject($title)
                ->setDescription($title)
                ->setKeywords($title)
                ->setCategory("Facturation");
            $title_facturation = "Facturation des prestations en faveur de " . $the_client->getNom() . " arrêtées au " .
                Boost::getMoisLettre($the_mois->format('m'), false, true) . '-' . $the_mois->format('Y'); // . strftime('%B %Y', $the_mois->getTimestamp());
            $phpExcelObject->setActiveSheetIndex(0);

            /* Titre facturation */
            $phpExcelObject
                ->getActiveSheet()
                ->setCellValue('D1', $title_facturation);


            //Contenu du fichier Excel

            /* Libellé des prestations */
            $address = 'G6';
            foreach ($code_prestations as $code_prestation) {
                $prestation = $this->getDoctrine()
                    ->getRepository('AppBundle:FactPrestation')
                    ->findOneBy(array(
                        'code' => intval($code_prestation),
                    ));
                if ($prestation) {
                    $phpExcelObject->getActiveSheet()
                        ->setCellValue($address, $prestation->getLibelle());
                }
                $phpExcelObject
                    ->getActiveSheet()
                    ->getStyle($address)
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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

                $split = \PHPExcel_Cell::coordinateFromString($address);
                ++$split[0];
                $address = implode($split);
            }

            /* Titre prix facturation */
            $phpExcelObject->getActiveSheet()
                ->setCellValue($address, "Facturation");

            /* Titre Remise */
            $addr_remise = $address;

            $split = \PHPExcel_Cell::coordinateFromString($addr_remise);
            for ($i = 0; $i < count($code_prestations); $i++) {
                ++$split[0];
            }

            $addr_remise = implode($split);
            $debut_remise = $addr_remise;
            for ($i = 0; $i < 4; $i++) {
                $phpExcelObject
                    ->getActiveSheet()
                    ->getStyle($addr_remise)
                    ->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $phpExcelObject->getActiveSheet()
                    ->getStyle($addr_remise)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'D9D9D9')
                            )
                        )
                    );
                if ($i == 1) {
                    $phpExcelObject
                        ->getActiveSheet()
                        ->setCellValue($addr_remise, "Remise");
                }
                if ($i == 2) {
                    $phpExcelObject->getActiveSheet()
                        ->setCellValue($addr_remise, "%");
                }
                if ($i == 3) {
                    $fin_remise = $addr_remise;
                    $phpExcelObject->getActiveSheet()
                        ->getStyle("$debut_remise:$fin_remise")
                        ->getBorders()
                        ->getTop()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $phpExcelObject->getActiveSheet()
                        ->getStyle("$debut_remise:$fin_remise")
                        ->getBorders()
                        ->getBottom()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $phpExcelObject->getActiveSheet()
                        ->getStyle("$debut_remise:$fin_remise")
                        ->getBorders()
                        ->getLeft()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $phpExcelObject->getActiveSheet()
                        ->getStyle("$debut_remise:$fin_remise")
                        ->getBorders()
                        ->getRight()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
                if ($i < 4) {
                    $split = \PHPExcel_Cell::coordinateFromString($addr_remise);
                    ++$split[0];
                    $addr_remise = implode($split);
                }
            }
            /* Fin Titre Remise */

            $address_start = $address;
            for ($i = 0; $i < count($code_prestations) - 1; $i++) {
                $split = \PHPExcel_Cell::coordinateFromString($address);
                ++$split[0];
                $address = implode($split);
            }
            $address_end = $address;

            /* Fusionner les cellules prix_prestations */
            $phpExcelObject->getActiveSheet()->mergeCells("$address_start:$address_end");
            $phpExcelObject
                ->getActiveSheet()
                ->getStyle($address_start)
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            /* Bordure */
            $phpExcelObject->getActiveSheet()
                ->getStyle("$address_start:$address_end")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            /* Background */
            $phpExcelObject->getActiveSheet()
                ->getStyle($address_start)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => '76933C')
                        )
                    )
                );

            /* Liste des colonnes */
            $address = 'A7';
            foreach ($colNames as $colName) {
                if (trim($colName) != "") {
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
                    ++$split[0];
                    $address = implode($split);
                }
            }
            $col_models = [];

            /* Background prix_facturation */
            $address = $address_start;
            $split = \PHPExcel_Cell::coordinateFromString($address);
            ++$split[1];
            $address = implode($split);
            for ($j = 0; $j < count($code_prestations); $j++) {
                $phpExcelObject->getActiveSheet()
                    ->getStyle($address)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => '76933C')
                            )
                        )
                    );
                $split = \PHPExcel_Cell::coordinateFromString($address);
                ++$split[0];
                $address = implode($split);
            }

            /* Background colonne Remise et Total */
            for ($j = 0; $j < 4; $j++) {
                $phpExcelObject->getActiveSheet()
                    ->getStyle($address)
                    ->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'D8E4BC')
                            )
                        )
                    );
                $split = \PHPExcel_Cell::coordinateFromString($address);
                ++$split[0];
                $address = implode($split);
            }

            /* Hauteur de la ligne au dessus des titres de colonnes */
            $phpExcelObject->getActiveSheet()->getRowDimension(6)->setRowHeight(50);

            foreach ($colModels as $colModel) {
                if ($colModel->name != "rn") {
                    $col_models[] = $colModel->name;
                }
            }

            $addressX = 'A';
            $addressY = 8;
            foreach ($rowDatas as $rowData) {
                foreach ($col_models as $col_model) {
                    $address = $addressX . $addressY;
                    if (property_exists($rowData, $col_model)) {
//                        var_dump($rowData->{$col_model});
                        $phpExcelObject->getActiveSheet()
                            ->setCellValue($address, $rowData->{$col_model});
                    }
                    $phpExcelObject->getActiveSheet()
                        ->getStyle($address)
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    if ($col_model != 'fact_dossier' && $col_model != 'fact_responsable') {
                        $phpExcelObject
                            ->getActiveSheet()
                            ->getStyle($address)
                            ->getAlignment()
                            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $addressX++;
                }
                $addressY++;
                $addressX = 'A';
            }
            $addressX = 'A';

            foreach ($col_models as $col_model) {
                $address = $addressX . $addressY;
                if (property_exists($footerDatas, $col_model)) {
//                        var_dump($rowData->{$col_model});
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
                    if ($col_model != 'fact_cloture') {
                        $phpExcelObject
                            ->getActiveSheet()
                            ->getStyle($address)
                            ->getAlignment()
                            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                }
                $addressX++;
            }

            $phpExcelObject->getActiveSheet()->setTitle("Facture");
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
        } else {
            throw new NotFoundHttpException('Client introuvable.');
        }
    }
}
