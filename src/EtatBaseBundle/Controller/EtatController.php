<?php

namespace EtatBaseBundle\Controller;

use Composer\Repository\RepositoryFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AppBundle\Controller\Boost;
use \DateTime;
use Symfony\Component\Validator\Constraints\Date;

class EtatController extends Controller
{
    /**
     * @param $etat
     * @param Request $request
     * @return Response
     */
    public function indexAction($etat,Request $request)
    {
        $post = $request->request;
        $dossier = $post->get('dossier');

        $dossier = Boost::deboost($dossier,$this);
        if(is_bool($dossier)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);

        $this->getDoctrine()->getRepository('AppBundle:Tiers')->majTierPcc($dossier);

        $exercices = json_decode($post->get('exercice'));

        $mois = ($post->get('mois') != 'Tous') ? json_decode($post->get('mois')) : true;
        $id_compte = $post->get('id_compte');
        $avec_solde = $post->get('avec_solde');

        //balance générale
        if($etat == 0)
        {
            $balance = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getBalance($dossier,$exercices,$mois,$avec_solde);
            return $this->render('EtatBaseBundle:Etat:balance_generale.html.twig', array('balance'=>$balance, 'tier'=>false));
        }
        //balance tiers
        if($etat == 1 || $etat == 2)
        {
            $type = ($etat == 1) ? 0 : 1;
            $balance = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getBalanceTier($dossier,$exercices,$mois,$type,$avec_solde);
            return $this->render('EtatBaseBundle:Etat:balance_generale.html.twig', array('balance'=>$balance,'tier'=>true));
        }
        if($etat == 3 || $etat == 4)
        {
            $periode_agee = json_decode($post->get('periode_agee'));
            $date_anciennete = new DateTime($post->get('date_anciennete'));
            $type = ($etat == 3) ? 0 : 1;
            $balance = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getBalanceAgeeTier($dossier,$exercices,$mois,$type,$periode_agee,$date_anciennete);
            return $this->render('EtatBaseBundle:Etat:balance_agee_tier.html.twig', array('balance'=>$balance,'type'=>$type));
        }
        if($etat == 5)
        {
            $journal = $post->get('journal');
            $journaux = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getJournaux($dossier,$exercices,$mois,$journal);
            return $this->render('EtatBaseBundle:Etat:journaux.html.twig', array('journaux'=>$journaux));
        }
        if($etat == 6)
        {
            $journaux = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getJournalCentralisateur($dossier,$exercices,$mois);
            return $this->render('EtatBaseBundle:Etat:journal_centralisateur.html.twig', array('journaux'=>$journaux));
        }
        if($etat == 7)
        {
            $grand_livres = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getGrandLivre($dossier,$exercices,$mois,$avec_solde,$id_compte);
            return new JsonResponse($grand_livres);
        }
        if($etat == 8 || $etat == 9)
        {
            $type = ($etat == 8) ? 0 : 1;
            $typeTier = $post->get('typeTier');
            if($typeTier != -1) $type = $typeTier;
            $grand_livres = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getGrandLivreTiers($dossier,$exercices,$mois,$type,$avec_solde,$id_compte);
            return new JsonResponse($grand_livres);
        }
        if($etat == 10)
        {
            $controles = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getControle($dossier,$exercices,$mois);
            return $this->render('EtatBaseBundle:Etat:controle.html.twig', array('controles'=>$controles));
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function showParametreAgeeAction(Request $request)
    {
        $post = $request->request;
        $periode_agee = json_decode($post->get('periode_agee'));
        return $this->render('EtatBaseBundle:Etat:parametre_agee.html.twig', array('periode_agee'=>$periode_agee,'date_anciennete'=>new DateTime($post->get('date_anciennete'))));
    }

    /**
     * @param $extention
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportAction($extention,Request $request)
    {
        $post = $request->request;
        $array_tr = json_decode(urldecode($post->get('array_tr')));
        $etat = $post->get('etat');
        $exercices = json_decode(urldecode($post->get('exercice')));
        $titre = json_decode(urldecode($post->get('titre')));
        $dossier = $post->get('dossier');
        $date_now = new \DateTime();
        $periode_agee = json_decode(urldecode($post->get('periode_agee')));
        $periode_agee[] = 0;

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id', $dossier)
            ->getQuery()
            ->getOneOrNullResult();

        $name = trim(explode('(',$titre)[0]);
        $name .= '_'.$dossier->getSite()->getClient()->getNom().'-'.$dossier->getNom().'.'.$extention;
        $name = str_replace(' ','_',$name);

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
            $sheet->setCellValue('A1', $titre)
                ->setCellValue('A2','Client')
                ->setCellValue('B2',$dossier->getSite()->getClient()->getNom())
                ->setCellValue('A3','Site')
                ->setCellValue('B3',$dossier->getSite()->getNom())
                ->setCellValue('A4','Dossier')
                ->setCellValue('B4',$dossier->getNom())
                ->setCellValue('A5',utf8_encode('Edit� le'))
                ->setCellValue('B5',$date_now->format('d-m-Y'));

            $index = 6;
            //balance
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
            }

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
            return $response;
            //$phpExcelObject->getActiveSheet()->getColumnDimensionByColumn('A')->setAutoSize(true);
            //$phpExcelObject->getActiveSheet()->getColumnDimensionByColumn('A')->setWidth('10');
        }
        elseif($extention == 'pdf')
        {
            $html = $this->renderView('EtatBaseBundle:Etat:pdf.html.twig', array('array_tr' => $array_tr, 'exercices' => $exercices, 'etat' => $etat, 'titre'=>$titre, 'dossier'=>$dossier, 'date_now'=>$date_now , 'periode_agee'=>$periode_agee));
            $html2pdf = $this->get('html2pdf_factory')->create('L', 'A4', 'fr');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);
            $html2pdf->Output($name, 'D');
            return new Response($dossier->getSite()->getClient()->getNom().'-'.$dossier->getNom().'.'.$extention);
        }
        return new Response();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function journauxAction(Request $request)
    {
        $dossier = $request->request->get('dossier');

        $dossier = Boost::deboost($dossier,$this);
        if(is_bool($dossier)) return new Response('security');

        $journaux = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getJournaux($dossier);
        return $this->render('EtatBaseBundle:Etat:journaux_liste.html.twig', array('journaux'=>$journaux));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function dateMajEcritureAction(Request $request)
    {
        $post = $request->request;
        $exercice = $post->get('exercice');
        $dossier = $post->get('dossier');
        $date = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getDerniereMAJ($exercice,$dossier);

        return new Response(($date != null) ? $date->format('d-m-Y') : '');
    }
}