<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 12/03/2019
 * Time: 09:35
 */

namespace DrtBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Controller\StringExt;
use AppBundle\Entity\EchangeItem;
use AppBundle\Entity\Image;
use AppBundle\Entity\TvaImputationControle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AnalyseController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function analyserAction(Request $request)
    {
        $spliters = explode('-', $request->request->get('echange_item'));

        /** @var EchangeItem $echangeItem */
        $echangeItem = $this->getDoctrine()->getRepository('AppBundle:EchangeItem')
            ->find($spliters[0]);
        $dossier = $echangeItem->getEchange()->getDossier();

        $fichiersAccepter = ['XLS','XLSX'];
        $expodes = explode('.',$echangeItem->getNomFichier());
        if (!in_array(strtoupper(trim($expodes[count($expodes) - 1])),$fichiersAccepter))
            return new Response(1);

        $file = $this->get('kernel')->getRootDir()."/../web/echange/".$echangeItem->getNomFichier();
        if (!file_exists($file)) return new Response(0);
        $objPHPExcel = \PHPExcel_IOFactory::load($file);

        $pages = [];
        $colMax = 'A';
        $mergesPages = [];
        $stylesPages = [];
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
        {
            if ($worksheet->getSheetState() != \PHPExcel_Worksheet::SHEETSTATE_HIDDEN)
            {
                $merges = [];
                $colonneDebit = null;
                $colonneCredit = null;
                $colonneDate = null;
                $rows = [];
                $maxColonne = 0;
                $styles = [];
                foreach ($worksheet->getRowIterator() as $row)
                {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $debit = 0;
                    $credit = 0;
                    /** @var \DateTime $date */
                    $row = [];
                    $style = [];
                    $maxX = 0;
                    foreach ($cellIterator as $cell)
                    {
                        if ($cell)
                        {
                            $date = null;
                            $colonne = $cell->getCoordinate();
                            $valeur = trim($cell->getCalculatedValue());
                            $colName = substr($colonne,0,1);
                            if ($colName > $colMax) $colMax = $colName;

                            if (in_array(strtoupper(StringExt::sansAccent($valeur)),['DEBIT','CREDIT','DATE']))
                            {
                                if (strtoupper(StringExt::sansAccent($valeur)) == 'DEBIT')
                                    $colonneDebit = $colName;
                                elseif (strtoupper(StringExt::sansAccent($valeur)) == 'CREDIT')
                                    $colonneCredit = $colName;
                                else $colonneDate = $colName;
                            }
                            elseif (
                                $colName == $colonneCredit && $colonneCredit ||
                                $colName == $colonneDebit && $colonneDebit ||
                                $colName == $colonneDate && $colonneDate)
                            {
                                if ($colName == $colonneCredit && $colonneCredit)
                                    $credit = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$valeur))));
                                elseif ($colName == $colonneDebit && $colonneDebit)
                                    $debit = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$valeur))));
                                else
                                    $date = \PHPExcel_Shared_Date::ExcelToPHPObject($cell->getCalculatedValue());
                            }

                            $index = ord(strtolower($colName)) - 97;

                            if ($index > $maxX) $maxX = $index;
                            $row[] = ($date && trim($valeur) != '' && intval($valeur) != 0) ?
                                $date->format('d/m/Y') :
                                $valeur;

                            $merge = $cell->getMergeRange();
                            if ($merge && array_key_exists($merge,$merges))
                                $style[] = (object)
                                [
                                    'color' => '000000',
                                    'bgColor' => 'FFFFFF',
                                ];
                            else
                                $style[] = (object)
                                [
                                    'color' => $objPHPExcel->getActiveSheet()->getStyle($colonne)->getFont()->getColor()->getRGB(),
                                    'bgColor' => $objPHPExcel->getActiveSheet()->getStyle($colonne)->getFill()->getStartColor()->getRGB(),
                                ];

                            if ($merge && !array_key_exists($merge,$merges))
                                $merges[$merge] = $merge;
                        }
                    }

                    $images = [];
                    if ($debit - $credit != 0)
                    {
                        $tvaImputationControles = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                            ->getTvaImputationControleByMontant($dossier,$debit - $credit,$echangeItem->getEchange()->getExercice());

                        foreach ($tvaImputationControles as $imputationControle)
                        {
                            /** @var TvaImputationControle $tvaImputationControle */
                            $tvaImputationControle = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                                ->find($imputationControle['tvaic']);

                            $images[] = (object)
                            [
                                'id' => $tvaImputationControle->getImage()->getId(),
                                'nom' => $tvaImputationControle->getImage()->getNom()
                            ];
                        }
                    }

                    $row[] = json_encode($images);
                    if (count($row) > $maxColonne) $maxColonne = count($row);
                    $rows[] = $row;

                    $styles[] = $style;
                }

                $pages[$worksheet->getTitle()] = (object)
                [
                    'maxCol' => $maxColonne,
                    'datas' => json_encode($rows)
                ];

                $mrgs = [];
                foreach ($merges as $merge)
                {
                    $spliters = explode(':',$merge);
                    $row = intval(substr($spliters[0],1,strlen($spliters[0]) - 1));
                    $start = ord(strtolower(substr($spliters[0],0,1))) - 97;
                    $end = ord(strtolower(substr($spliters[1],0,1))) - 97;

                    $r = $row - 1;
                    $c = $start;
                    $mrgs[] = (object)
                    [
                        'row' => $r,
                        'start' => $c,
                        'end' => $end
                    ];
                }

                $mergesPages[$worksheet->getTitle()] = json_encode($mrgs);
                $stylesPages[$worksheet->getTitle()] = json_encode($styles);
            }
        }

        return $this->render('DrtBundle:Drt:analyse.html.twig',[
            'pages' => $pages,
            'echangeItem' => $echangeItem,
            'mergesPages' => $mergesPages,
            'stylesPages' => $stylesPages
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function imagesAction(Request $request)
    {
        $ids = explode(',', $request->request->get('images'));
        /** @var Image[] $images */
        $images = $this->getDoctrine()->getRepository('AppBundle:Image')
            ->createQueryBuilder('i')
            ->where('i.id IN (:ids)')
            ->setParameter('ids',$ids)
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($images as $image)
        {
            /** @var TvaImputationControle[] $tics */
            $tics = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                ->createQueryBuilder('tic')
                ->where('tic.image = :image')
                ->setParameter('image',$image)
                ->getQuery()
                ->getResult();

            $bilan = null;
            $bilanPcc = null;
            $tva = null;
            $resultat = null;

            $montantTva = 0;
            $montatTTc = 0;
            foreach ($tics as $tic)
            {
                $montatTTc += $tic->getMontantTtc();
                $montantTva += $tic->getMontantTtc() * $tic->getTvaTaux()->getTaux() / 100;

                if ($tic->getPccBilan())
                    $bilanPcc = (object)
                    [
                        'id' => Boost::boost($tic->getPccBilan()->getId()),
                        'l' => $tic->getPccBilan()->getCompte(),
                        't' => 0
                    ];
                if ($tic->getTiers())
                    $bilan = (object)
                    [
                        'id' => Boost::boost($tic->getTiers()->getId()),
                        'l' => $tic->getTiers()->getCompteStr(),
                        't' => 1,
                        'i' => $tic->getTiers()->getIntitule()
                    ];
                if ($tic->getPccTva())
                    $tva = (object)
                    [
                        'id' => Boost::boost($tic->getPccTva()->getId()),
                        'l' => $tic->getPccTva()->getCompte(),
                        't' => 0
                    ];
                if ($tic->getPcc())
                    $resultat = (object)
                    [
                        'id' => Boost::boost($tic->getPcc()->getId()),
                        'l' => $tic->getPcc()->getCompte(),
                        't' => 0
                    ];
            }

            $results[] = (object)
            [
                'id' => Boost::boost($image->getId()),
                'image' => $image->getNom(),
                'imi' => Boost::boost($image->getId()),
                'libelle' => ($bilan) ? $bilan->i : '',
                'bilan' => $bilan,
                'bilanPcc' => $bilanPcc,
                'tva' => $tva,
                'resultat' => $resultat,
                'mTTC' => $montatTTc,
                'mTVA' => $montantTva,
                'mHT' => $montatTTc - $montantTva
            ];
        }

        return new JsonResponse($results);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function generateXlsAction(Request $request)
    {
        $pages = json_decode(urldecode($request->request->get('pages')));
        $names = json_decode(urldecode($request->request->get('names')));
        $styles = json_decode(urldecode($request->request->get('styles')));

        /*return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => $styles
        ]);*/

        $echangeItem = Boost::deboost(urldecode($request->request->get('echange_item')),$this);
        /** @var EchangeItem $echangeItem */
        $echangeItem = $this->getDoctrine()->getRepository('AppBundle:EchangeItem')
            ->find($echangeItem);

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $backgroundTitle = '808080';
        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");

        foreach ($pages as $key => $page)
        {
            $sheet = $phpExcelObject->setActiveSheetIndex($key);
            $phpExcelObject->getActiveSheet()->setTitle($names[$key]);
            foreach ($page as $keyRow => $row)
            {
                foreach ($row as $keyTd => $td)
                {
                    $caractere = chr(ord('A') + $keyTd);
                    $sheet->setCellValue($caractere.($keyRow + 1),$td);
                    $phpExcelObject->getActiveSheet()->getStyle($caractere.($keyRow + 1))
                        ->getFont()->getColor()->setRGB($styles[$key][$keyRow][$keyTd]->color);

                    $bgColor = str_replace('#','',$styles[$key][$keyRow][$keyTd]->bgColor);
                    if ($bgColor == '000000')
                        $bgColor = 'FFFFFF';

                    $color = str_replace('#','',$styles[$key][$keyRow][$keyTd]->color);

                    Boost::cellColor($phpExcelObject,$caractere.($keyRow + 1).':'.$caractere.($keyRow + 1),
                        $bgColor);
                    Boost::cellTextColor($phpExcelObject,$caractere.($keyRow + 1).':'.$caractere.($keyRow + 1),$color,$bold=false,$size=false);

                    if (intval($styles[$key][$keyRow][$keyTd]->colSpan) > 1)
                    {
                        $caractereMerge = chr(ord('A') + $keyTd + intval($styles[$key][$keyRow][$keyTd]->colSpan) - 1);
                        $phpExcelObject->getActiveSheet()->mergeCells($caractere.($keyRow + 1).':'.$caractereMerge.($keyRow + 1));
                    }
                }
            }
        }
        //return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=> $caracteres]);

        //$explodes = explode('.',$echangeItem->getNomFichier());
        $name =
            $echangeItem->getEchange()->getEchangeType()->getNom() . ' ' .
            $echangeItem->getEchange()->getDossier()->getNom() . ' ' .
            substr($echangeItem->getEchange()->getExercice(),2,2) . ' ' .
            $echangeItem->getNumero() . '.xls';

        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
             $name
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function chargerReponseAction(Request $request)
    {
        $index = intval($request->request->get('index'));
        $echangeItem = Boost::deboost($request->request->get('echange_item'),$this);
        /** @var EchangeItem $echangeItem */
        $echangeItem = $this->getDoctrine()->getRepository('AppBundle:EchangeItem')
            ->find($echangeItem);

        $echangeReponses = $this->getDoctrine()->getRepository('AppBundle:EchangeReponse')
            ->getEchangeReponses($echangeItem);

        $results = [];
        foreach ($echangeReponses as $echangeReponse)
        {
            $spliters = explode('.',$echangeReponse->getNomFichier());
            $results[] = (object)
            [
                'ext' => $spliters[count($spliters) - 1],
                'echangeReponse' => $echangeReponse,
                'images' => $this->getDoctrine()->getRepository('AppBundle:Image')
                    ->getChildEchangeReponses($echangeReponse)
            ];
        }

        $extensions = (object)
        [
            'images' => ['jpg','jpeg','png','gif','tif','tiff'],
            'xls' => ['xls','xlsx'],
            'pdf' => ['pdf']
        ];

        return $this->render('DrtBundle:Drt:reponses.html.twig',[
           'results' => $results,
            'index' => $index,
            'extensions' => $extensions
        ]);
    }
}