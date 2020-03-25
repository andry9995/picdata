<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 03/07/2017
 * Time: 13:58
 */

namespace EtatBaseBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Tiers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class EtatBaseController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $anciennetes = json_encode(array(30,60,90));
        return $this->render('EtatBaseBundle:EtatBase:index.html.twig',array('anciennetes'=>$anciennetes));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function etatBaseAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $compteDe = null;
        $compteA = null;
        $compte_de = json_decode($post->get('compte_de'));
        $compte_a = json_decode($post->get('compte_a'));
        $colSolde = (intval($post->get('col_solde')) == 1);

        if (is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $compteDe = (intval($compte_de->t) == 2) ?
            $this->getDoctrine()->getRepository('AppBundle:Pcc')->find(Boost::deboost($compte_de->id,$this)) :
            $this->getDoctrine()->getRepository('AppBundle:Tiers')->find(Boost::deboost($compte_de->id,$this));

        $compteA = (intval($compte_a->t) == 2) ?
            $this->getDoctrine()->getRepository('AppBundle:Pcc')->find(Boost::deboost($compte_a->id,$this)) :
            $this->getDoctrine()->getRepository('AppBundle:Tiers')->find(Boost::deboost($compte_a->id,$this));

        $exercices = json_decode($post->get('exercices'));
        $mois = json_decode($post->get('mois'));
        $periodes = json_decode($post->get('periodes'));
        $etat = intval($post->get('etat'));
        $option = intval($post->get('option'));
        $avecLettre = (intval($post->get('avec_lettre')) == 1);
        $regroupeLettre = (intval($post->get('regroupe_lettre')) == 1);
        $anDet = (intval($post->get('an_det')) == 1);

        $moisSelects = [];
        foreach ($periodes as $periode)
        {
            foreach ($periode->moiss as $moi)
            {
                $moisSelects[] = $moi;
            }
        }

        $resultat = 0;
        if ($etat == 0)
        {
            $resultat = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getBalance($dossier,$exercices,$moisSelects,true,$compteDe,$compteA);
            return $this->render('EtatBaseBundle:Etat:balance_generale.html.twig', array('balance'=>$resultat, 'tier'=>false, 'colSolde'=>$colSolde));
        }
        elseif ($etat == 1)
        {
            if ($option == 0)
            {
                $type = 1;
                $resultat = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getBalanceTier($dossier,$exercices,$moisSelects,$type,0,$compteDe,$compteA);
                return $this->render('EtatBaseBundle:Etat:balance_generale.html.twig', array('balance'=>$resultat,'tier'=>true, 'colSolde'=>$colSolde));
            }
            elseif ($option == 1)
            {
                $anciennetes = json_decode($post->get('anciennetes'));
                $dateAnciennete = new \DateTime($post->get('date_anciennete'));
                $type = 1;
                $resultat = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getBalanceAgeeTier($dossier,$exercices,$moisSelects,$type,$anciennetes,$dateAnciennete,$compteDe,$compteA);

                /*return $this->render('EtatBaseBundle:EtatBase:test.html.twig',array('test'=>$resultat));*/
                return $this->render('EtatBaseBundle:Etat:balance_agee_tier.html.twig', array('balance'=>$resultat,'type'=>$type));
            }
        }
        elseif ($etat == 2)
        {
            if ($option == 0)
            {
                $type = 0;
                $resultat = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getBalanceTier($dossier,$exercices,$moisSelects,$type,0,$compteDe,$compteA);
                return $this->render('EtatBaseBundle:Etat:balance_generale.html.twig', array('balance'=>$resultat,'tier'=>true, 'colSolde'=>$colSolde));
            }
            elseif ($option == 1)
            {
                $anciennetes = json_decode($post->get('anciennetes'));
                $dateAnciennete = new \DateTime($post->get('date_anciennete'));
                $type = 0;
                $resultat = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getBalanceAgeeTier($dossier,$exercices,$moisSelects,$type,$anciennetes,$dateAnciennete,$compteDe,$compteA);

                //return $this->render('EtatBaseBundle:EtatBase:test.html.twig',array('test'=>$resultat));
                return $this->render('EtatBaseBundle:Etat:balance_agee_tier.html.twig', array('balance'=>$resultat,'type'=>$type));
            }
        }
        elseif ($etat == 3)
        {
            if ($option == 2)
            {
                $resultat = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getGrandLivre($dossier,$exercices,$moisSelects,$avecLettre,0,$compteDe,$compteA,$regroupeLettre,$anDet,$colSolde);
                return new JsonResponse($resultat);
            }
            elseif ($option == 1 || $option == 0)
            {
                $resultat = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getGrandLivreTiers($dossier,$exercices,$moisSelects,$option,$avecLettre,0,$compteDe,$compteA,$regroupeLettre,$anDet);
                return new JsonResponse($resultat);
            }
        }
        elseif ($etat == 4)
        {
            $journal = Boost::deboost($post->get('journal'),$this);
            if (is_bool($journal)) return new Response('security');
            //$journal = $post->get('journal');
            $journaux = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getJournaux($dossier,$exercices,$moisSelects,$journal,$compteDe,$compteA);
            return $this->render('EtatBaseBundle:Etat:journaux.html.twig', array('journaux'=>$journaux,'colSolde'=>$colSolde));
        }
        elseif ($etat == 5)
        {
            $resultat = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getJournalCentralisateur($dossier,$exercices,$moisSelects);
            return $this->render('EtatBaseBundle:Etat:journal_centralisateur.html.twig', array('journaux'=>$resultat));
        }

        return $this->render('EtatBaseBundle:EtatBase:test.html.twig',array('test'=>$resultat));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function journauxAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if (is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $journaux = $this->getDoctrine()->getRepository('AppBundle:JournalDossier')->getJournaux($dossier);
        return $this->render('EtatBaseBundle:EtatBase:journaux-select.html.twig',array('journaux'=>$journaux));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function pccTiersAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if (is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        //PCCS
        $pccsTemps = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPccs($dossier);
        $pccs = [];
        foreach ($pccsTemps as $temp)
        {
            $pccs[] = (object)
            [
                'id' => Boost::boost($temp->getId()),
                't' => 0,
                'c' => $temp->getCompte(),
                'i' => $temp->getIntitule()
            ];
        }

        //tiers
        $tiersTemps = $this->getDoctrine()->getRepository('AppBundle:Tiers')->getTiers($dossier);
        $tiers = [];
        foreach ($tiersTemps as $temp)
        {
            //$temp = new Tiers();
            $tiers[] = (object)
            [
                'id' => Boost::boost($temp->getId()),
                't' => $temp->getType(),
                'c' => $temp->getCompteStr(),
                'i' => $temp->getIntitule()
            ];
        }

        $results = (object)
        [
            'pccs' => $pccs,
            'tiers' => $tiers
        ];

        return new JsonResponse($results);

    }

    /**
     * @param Request $request
     * @return Response
     */
    public function exportAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('exp_dossier'),$this);
        if (is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $exercices = json_decode(urldecode($post->get('exercices')));
        $headers = json_decode(urldecode($post->get('headers')));
        $bodys = json_decode(urldecode($post->get('bodys')));
        $format = intval($post->get('formats'));
        $title = $post->get('titles');
        $etat = intval($post->get('etat'));

        $extention = ($format == 0) ? 'xls' : 'pdf';

        $name = $title;
        $name .= '_'.$dossier->getSite()->getClient()->getNom().'-'.$dossier->getNom().'.'.$extention;
        $name = str_replace(' ','_',$name);
        $dateNow = new \DateTime();

        if($extention == 'xls')
        {
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
            $backgroundTitle = '808080';
            $phpExcelObject->getProperties()->setCreator("liuggio")
                ->setLastModifiedBy("Giulio De Donato")
                ->setTitle("Office 2005 XLSX Test Document")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("Test result file");
            $sheet = $phpExcelObject->setActiveSheetIndex(0);

            /*Titre*/
            $sheet->setCellValue('A1', $title)
                ->setCellValue('A2','Client')
                ->setCellValue('B2',$dossier->getSite()->getClient()->getNom())
                ->setCellValue('A3','Site')
                ->setCellValue('B3',$dossier->getSite()->getNom())
                ->setCellValue('A4','Dossier')
                ->setCellValue('B4',$dossier->getNom())
                ->setCellValue('A5','Exercice')
                ->setCellValue('B5',((count($exercices) >= 1) ? $exercices[0] : implode(',',$exercices)))
                ->setCellValue('A6','Editer le')
                ->setCellValue('B6',$dateNow->format('d-m-Y'));

            $index = 7;
            /**
             * headers
             */
            foreach ($headers as $header)
            {
                foreach ($header as $td)
                {
                    $text = $td->t;
                    $pos = $td->pos;

                    $col = intval($pos->col);
                    $colCode = chr(ord('A') + $col);

                    $colMerge = intval($pos->colspan) - 1;
                    $rowMerge = intval($pos->rowspan) - 1;
                    $sheet->mergeCells($colCode.$index.':'.chr(ord($colCode)+$colMerge).$index);
                    $sheet->mergeCells($colCode.$index.':'.$colCode.($index + $rowMerge));

                    $sheet->setCellValue($colCode.$index,$text);
                    $sheet->getStyle($colCode.$index)->getAlignment()->applyFromArray(
                        array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,)
                    );

                    $styles = $td->styles;
                    if($styles != null)
                    {
                        $bg = $styles->bg;
                        if($bg != null) Boost::cellColor($phpExcelObject, $colCode.$index.':'.$colCode.$index, $bg);
                        $cl = $styles->cl;
                        if($cl == null) $cl = 'FFFFFF';
                        $bold = intval($styles->bold) == 1;

                        Boost::cellTextColor($phpExcelObject, $colCode.$index, $cl, $bold, $size = false);
                    }
                }
                $index++;
            }
            /**
             * bodys
             */
            foreach ($bodys as $body)
            {
                $colS = 0;
                foreach ($body as $col => $td)
                {
                    $colCode = chr(ord('A') + $col + $colS);
                    $text = $td->t;

                    $pos = $td->pos;
                    $colMerge = intval($pos->colspan) - 1;
                    $sheet->mergeCells($colCode.$index.':'.chr(ord($colCode)+$colMerge).$index);
                    $sheet->setCellValue($colCode.$index,$text);

                    $styles = $td->styles;
                    if($styles != null)
                    {
                        $bg = $styles->bg;
                        if($bg != null) Boost::cellColor($phpExcelObject, $colCode.$index.':'.$colCode.$index, $bg);
                        $cl = $styles->cl;
                        if($cl == null) $cl = 'FFFFFF';
                        $bold = intval($styles->bold) == 1;

                        Boost::cellTextColor($phpExcelObject, $colCode.$index, $cl, $bold, $size = false);
                    }
                    $colS += $colMerge;
                }
                $index++;
            }

            /*//balance
            if($etat < 3)
            {
                $index++;
                //Entete tableau
                $sheet->setCellValue('A'.$index,'Compte')
                    ->setCellValue('B'.$index,utf8_encode('Intitul�'));
                $col = 'C';
                if(count($exercices) == 1)
                {
                    $sheet->setCellValue('C'.$index,utf8_encode('D�bit'))
                        ->setCellValue('D'.$index,utf8_encode('Cr�dit'));
                    $col = 'E';
                }
                for($i = 0; $i<count($exercices); $i++)
                {
                    $sheet->setCellValue($col.$index,utf8_encode('Solde D�bit'));
                    $col++;
                    $sheet->setCellValue($col.$index,utf8_encode('Solde Cr�dit'));
                    $col++;
                }
                Boost::cellColor($phpExcelObject,'A'.$index.':'.$col.$index,$backgroundTitle);
                Boost::cellTextColor($phpExcelObject,'A'.$index.':'.$col.$index,'ffffff',$bold=false,$size=false);

                //Donnees
                $index++;
                foreach($array_tr as $tr)
                {
                    $col = 'A';
                    for($i = 2 ; $i <count($tr) ; $i++)
                    {
                        $val = $tr[$i];
                        iconv("ISO-8859-1//TRANSLIT","UTF-8",$val);
                        if($i > 3)
                        {
                            $val = Boost::parseNumber($val,',');
                            if($val == 0) $val = '';
                        }
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValue($col.$index,$val);
                        $col++;
                    }
                    $index++;
                }
            }
            //Balance ag�e
            if($etat == 3 or $etat == 4)
            {
                $ibox_index = 0;
                $index++;
                foreach ($array_tr as $ibox) {
                    $ibox_index++;
                    if($ibox_index != count($array_tr))
                    {
                        $fc = ($etat == 3) ? 'Fournisseur' : 'Client';
                        $dc = utf8_encode(($ibox->d == 1) ? 'D�bit' : 'Cr�dit');
                        //entete
                        $sheet->setCellValue('A' . $index, $fc . 's ' . $dc . 'eurs');
                        Boost::cellTextColor($phpExcelObject, 'A' . $index, '000000', $bold = true, $size = false);
                        $index++;
                        $sheet->setCellValue('A' . $index, 'Compte')
                            ->setCellValue('B' . $index, $fc . ' ' . $dc);
                        $col = 'C';
                        for ($i = 0; $i < count($periode_agee); $i++) {
                            if (count($periode_agee) == 1) $entete = '';
                            elseif ($i == 0) $entete = 'Plus de ' . $periode_agee[$i];
                            elseif ($i == count($periode_agee) - 1) $entete = 'Moins de ' . $periode_agee[$i - 1];
                            else $entete = 'De ' . $periode_agee[$i] . ' � ' . $periode_agee[$i - 1];

                            $sheet->setCellValue($col . $index, utf8_encode($entete));
                            $col++;
                        }
                        $sheet->setCellValue($col . $index, 'Total');
                        Boost::cellColor($phpExcelObject, 'A' . $index . ':' . $col . $index, $backgroundTitle);
                        Boost::cellTextColor($phpExcelObject, 'A' . $index . ':' . $col . $index, 'ffffff', $bold = false, $size = false);
                        foreach ($ibox->t as $tr) {
                            $index++;
                            $col = 'A';
                            for ($i = 0; $i < count($tr); $i++) {
                                $val = $tr[$i];
                                iconv("ISO-8859-1//TRANSLIT", "UTF-8", $val);
                                if ($i > 1) {
                                    $val = Boost::parseNumber($val, ',');
                                    if ($val == 0) $val = '';
                                }
                                $phpExcelObject->setActiveSheetIndex(0)->setCellValue($col . $index, $val);
                                $col++;
                            }
                        }
                    }
                    foreach($ibox->f as $tr)
                    {
                        $index++;
                        $sheet->setCellValue('B'.$index,($ibox_index == count($array_tr)) ? 'TOTAL GENERAL' : $fc.'s '.$dc.'eurs');
                        $col = 'C';
                        for($i = 0; $i < count($tr);$i++)
                        {
                            $val = $tr[$i];
                            iconv("ISO-8859-1//TRANSLIT","UTF-8",$val);
                            $val = Boost::parseNumber($val,',');
                            if($val == 0) $val = '';
                            $phpExcelObject->setActiveSheetIndex(0)->setCellValue($col.$index,$val);
                            $col++;
                        }
                        Boost::cellTextColor($phpExcelObject,'A'.$index.':'.$col.$index ,'000000', $bold=true, $size=false);
                    }
                    $index++;
                    if($ibox_index != count($array_tr) - 1) $index++;
                }
            }
            //Journaux
            if($etat == 5)
            {
                $index++;
                //entete
                $sheet->setCellValue('A'.$index,'Date')
                    ->setCellValue('B'.$index,'Journal')
                    ->setCellValue('C'.$index,'Compte')
                    ->setCellValue('D'.$index,utf8_encode('Pi�ce'))
                    ->setCellValue('E'.$index,utf8_encode('Libell� op�ration'))
                    ->setCellValue('F'.$index,utf8_encode('D�bit'))
                    ->setCellValue('G'.$index,utf8_encode('Cr�dit'));
                $col = 'G';
                Boost::cellColor($phpExcelObject,'A'.$index.':'.$col.$index,$backgroundTitle);
                Boost::cellTextColor($phpExcelObject,'A'.$index.':'.$col.$index,'ffffff',$bold=false,$size=false);
                $index++;

                //Donnees
                foreach($array_tr as $tr)
                {
                    $col = 'A';
                    for($i = 2;$i < count($tr);$i++)
                    {
                        $val = $tr[$i];
                        iconv("ISO-8859-1//TRANSLIT","UTF-8",$val);
                        if($i > 6)
                        {
                            $val = Boost::parseNumber($val,',');
                            if($val == 0) $val = '';
                        }
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValue($col.$index,$val);
                        $col++;
                    }
                    $index++;
                }
            }
            //journal centralisateur
            if($etat == 6)
            {
                $index++;
                //entete
                $sheet->setCellValue('A'.$index,'Date')
                    ->setCellValue('B'.$index,'Journal')
                    ->setCellValue('C'.$index,utf8_encode('Libell�'))
                    ->setCellValue('D'.$index,utf8_encode('Total D�bit'))
                    ->setCellValue('E'.$index,utf8_encode('Total Cr�dit'));
                $col = 'E';
                Boost::cellColor($phpExcelObject,'A'.$index.':'.$col.$index,$backgroundTitle);
                Boost::cellTextColor($phpExcelObject,'A'.$index.':'.$col.$index,'ffffff',$bold=false,$size=false);
                $index++;

                //Donnees
                foreach($array_tr as $tr)
                {
                    $col = 'A';
                    for($i = 0;$i < count($tr);$i++)
                    {
                        $val = $tr[$i];
                        iconv("ISO-8859-1//TRANSLIT","UTF-8",$val);
                        if($i > 2)
                        {
                            $val = Boost::parseNumber($val,',');
                            if($val == 0) $val = '';
                        }
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValue($col.$index,$val);
                        $col++;
                    }
                    $index++;
                }
            }
            //grand livre
            if($etat > 6 && $etat < 10)
            {
                foreach($array_tr as $ibox)
                {
                    $index++;
                    $compte = $ibox->c;
                    iconv("ISO-8859-1//TRANSLIT","UTF-8",$compte);
                    $intitule = $ibox->i;
                    iconv("ISO-8859-1//TRANSLIT","UTF-8",$intitule);
                    //entete
                    $sheet->setCellValue('A'.$index,$compte)
                        ->setCellValue('B'.$index,$intitule);
                    Boost::cellTextColor($phpExcelObject,'A'.$index.':B'.$index,'000000',$bold=true,$size=false);
                    $index++;
                    $sheet->setCellValue('A'.$index,'Date')
                        ->setCellValue('B'.$index,'Journal')
                        ->setCellValue('C'.$index,utf8_encode('Pi�ce'))
                        ->setCellValue('D'.$index,utf8_encode('Libell�'))
                        ->setCellValue('E'.$index,utf8_encode('D�bit'))
                        ->setCellValue('F'.$index,utf8_encode('Cr�dit'))
                        ->setCellValue('G'.$index,utf8_encode('L'))
                        ->setCellValue('H'.$index,utf8_encode('Solde D�bit'))
                        ->setCellValue('I'.$index,utf8_encode('Solde Cr�dit'));
                    $col = 'I';
                    Boost::cellColor($phpExcelObject,'A'.$index.':'.$col.$index,$backgroundTitle);
                    Boost::cellTextColor($phpExcelObject,'A'.$index.':'.$col.$index,'ffffff',$bold=false,$size=false);

                    //donnees
                    foreach($ibox->t as $tr)
                    {
                        $index++;
                        $col = 'A';
                        for($i = 0; $i < count($tr) ;$i++)
                        {
                            $val = $tr[$i];
                            iconv("ISO-8859-1//TRANSLIT","UTF-8",$val);
                            if($i > 3 && $i != 6)
                            {
                                $val = Boost::parseNumber($val,',');
                                if($val == 0) $val = '';
                            }
                            $phpExcelObject->setActiveSheetIndex(0)->setCellValue($col.$index,$val);
                            $col++;
                        }
                    }
                    //foot
                    foreach($ibox->f as $tr)
                    {
                        $index++;
                        $col = 'D';
                        $phpExcelObject->setActiveSheetIndex(0)->setCellValue($col.$index,'Totaux du compte '.$compte);
                        $col++;
                        for($i = 0; $i < count($tr) ;$i++)
                        {
                            $val = $tr[$i];
                            iconv("ISO-8859-1//TRANSLIT","UTF-8",$val);
                            $val = Boost::parseNumber($val,',');
                            if($val == 0) $val = '';
                            $phpExcelObject->setActiveSheetIndex(0)->setCellValue($col.$index,$val);
                            $col++;
                        }
                        Boost::cellTextColor($phpExcelObject,'A'.$index.':'.$col.$index,'000000',$bold=true,$size=false);
                        $index++;
                    }
                }
            }*/

            $phpExcelObject->getActiveSheet()->setTitle('Simple');
            $phpExcelObject->setActiveSheetIndex(0);

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

            //$phpExcelObject->disconnectWorksheets();
            //unset($writer, $phpExcelObject);

            return $response;
            //$phpExcelObject->getActiveSheet()->getColumnDimensionByColumn('A')->setAutoSize(true);
            //$phpExcelObject->getActiveSheet()->getColumnDimensionByColumn('A')->setWidth('10');
        }
        elseif($extention == 'pdf')
        {
            $html = $this->renderView('EtatBaseBundle:Etat:pdf.html.twig', array('array_tr' => $bodys, 'exercices' => $exercices, 'etat' => $etat, 'titre'=>$title, 'dossier'=>$dossier , 'periode_agee'=>null));
            $html2pdf = $this->get('html2pdf_factory')->create('L', 'A4', 'fr');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);
            $html2pdf->Output($name, 'D');
            return new Response($dossier->getSite()->getClient()->getNom().'-'.$dossier->getNom().'.'.$extention);
        }
        return new Response();
    }
}