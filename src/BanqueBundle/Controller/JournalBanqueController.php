<?php

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Banque;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\JournalDossier;
use AppBundle\Entity\Pcc;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Types\BlobType;
use DoctrineExtensions\Query\Mysql\Binary;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Constraints\DateTime;

class JournalBanqueController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('BanqueBundle:JournalBanque:index.html.twig');
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function analyseAction(Request $request)
    {
        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
        if(is_bool($banqueCompte)) return new Response('security');
        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);

        if (!$banqueCompte->getPcc() || !$banqueCompte->getJournalDossier())
        {
            if (!$banqueCompte->getPcc() && !$banqueCompte->getJournalDossier())
                return new Response(-3);
            elseif (!$banqueCompte->getPcc())
                return new Response(-1);
            else return new Response(-2);
        }

        $centraliser = (intval($request->request->get('centraliser')) == 1);
        $obsDetailler = (intval($request->request->get('obs_detailler')) == 1);;
        $exercice = intval($request->request->get('exercice'));
        $periode = json_decode($request->request->get('periode'));
        $dateDossier = $this->getDoctrine()->getRepository('AppBundle:TbimagePeriode')
            ->getAnneeMoisExercices($banqueCompte->getDossier(),$exercice);
        /** @var \DateTime $dateCloture */
        $dateCloture = $dateDossier->c;

        $mois = [];
        foreach ($periode->p as $p)
            foreach ($p->moiss as $m)
                if (!in_array($m,$mois)) $mois[] = $m;

        $dateMax = clone  $dateCloture;
        $dateIntervals = [];
        foreach (array_reverse($periode->m) as $key => $m)
        {
            $max = clone $dateMax;
            $min = clone $max;
            $min->sub(new \DateInterval('P1M'));

            $dateMax = clone $min;
            if ($key == 0) $max = \DateTime::createFromFormat('Y-m-d','2200-12-31');
            elseif ($key == 11) $min = \DateTime::createFromFormat('Y-m-d','1900-01-01');

            if (in_array($m,$mois))
                $dateIntervals[$m] = (object)
                [
                    'max' => $max,
                    'min' => $min
                ];
        }

        $filtreType = intval($request->request->get('filtre_type'));

        /** @var \DateTime $filtreStart */
        $filtreStart = \DateTime::createFromFormat('d/m/Y',$request->request->get('filtre_start'));
        if ($filtreStart === false || array_sum($filtreStart::getLastErrors()))
            $filtreStart = null;

        /** @var \DateTime $filtreEnd */
        $filtreEnd = \DateTime::createFromFormat('d/m/Y',$request->request->get('filtre_end'));
        if ($filtreEnd === false || array_sum($filtreEnd::getLastErrors()))
            $filtreEnd = null;

        $journaux = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->getJournal($banqueCompte,$exercice,$dateIntervals,$centraliser,$banqueCompte->getJournalDossier(),$filtreType,$filtreStart,$filtreEnd,$obsDetailler);
        return new JsonResponse($journaux);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getPeriodePopOverAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        /** @var Dossier $dossier */
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);

        if ($dossier) $moiss = Boost::getMois($dossier->getCloture());
        else $moiss = Boost::getMois(12);

        $contenu = '
            <div class="row">
                <div class="col-sm-12" style="margin-top:10px!important">
                    <table class="table table-condensed table-bordered table-hover no-margin text-center table-dpk">
                        <tbody>';

        $index = $trimestre = $semestre = 0;
        $annee = 1;

        foreach ($moiss as $key => $val)
        {
            $isSemestre = false;
            //semestre
            if($index == 0 || $index % 6 == 0)
            {
                $semestre++;
                $isSemestre = true;
                $contenu .= '<tr>';
                if($index == 0) $contenu .= '<th rowspan="4" class="js_dpk_periode" data-val="'.$annee.'" data-mere-annee="-1" data-mere-semestre="-1" data-mere-trimestre="-1" data-niveau="0">A</th>';
                $contenu .= '<th rowspan="2" class="js_dpk_periode js_dpk_semestre" data-val="'.$semestre.'" data-mere-annee="'.$annee.'" data-mere-semestre="-1" data-mere-trimestre="-1" data-niveau="1">S'.$semestre.'</th>';
            }

            //trimestre
            if($index == 0 || $index % 3 == 0)
            {
                $trimestre++;
                if(!$semestre)
                    $contenu .= '<tr>';
                $contenu .= '<th class="js_dpk_periode js_dpk_trimestre" data-val="'.$trimestre.'" data-mere-annee="'.$annee.'" data-mere-semestre="'.$semestre.'" data-mere-trimestre="-1" data-niveau="2">T'.$trimestre.'</th>';
            }

            $contenu .= '<td class="js_dpk_periode js_dpk_mois td-active" data-value="'.$key.'" data-val="-1" data-mere-annee="'.$annee.'" data-mere-semestre="'.$semestre.'" data-mere-trimestre="'.$trimestre.'" data-niveau="3">' . $val . '</td>';

            //fermeture tr
            if($index == count($moiss) - 1 || (($index + 1) % 3 == 0))
            {
                $contenu .= '</tr>';
            }

            $index++;
        }

        $contenu .=
                        '</tbody>
                    </table>
                </div>
            </div>
            <div class="row" style="margin-top:10px!important">
                <div class="col-sm-12 text-right">
                    <span class="btn btn-primary btn-xs js_dpk_valider"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;Valider</span>
                </div>
            </div>';

        return new Response($contenu);

        return $this->render('@Banque/JournalBanque/btn-periode.html.twig',[
            'content' => $contenu
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function banqueComptePccAction(Request $request)
    {
        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
        if(is_bool($banqueCompte)) return new Response('security');
        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);

        $idPccBc = $banqueCompte->getPcc() ? $banqueCompte->getPcc()->getId() : 0;
        $pccBanques = $this->getDoctrine()->getRepository('AppBundle:Pcc')
            ->getPccBanque($banqueCompte->getDossier());
        $pccsUseds = $this->getDoctrine()->getRepository('AppBundle:Pcc')
            ->getPccBanqueUsed($banqueCompte->getDossier());
        /** @var Pcc[] $pccs */
        $pccs = [];
        foreach ($pccBanques as $pccBanque)
        {
            if ($idPccBc == $pccBanque->getId() || !array_key_exists($pccBanque->getId(),$pccsUseds))
                $pccs[] = $pccBanque;
        }

        $idJournaDossierBc = $banqueCompte->getJournalDossier() ? $banqueCompte->getJournalDossier()->getId() : 0;
        $journalDossiers = $this->getDoctrine()->getRepository('AppBundle:JournalDossier')
            ->getJournaux($banqueCompte->getDossier());
        $journalDossiersUseds = $this->getDoctrine()->getRepository('AppBundle:JournalDossier')
            ->getJournauxBanqueUsed($banqueCompte->getDossier());
        /** @var JournalDossier[] $journauxDossiers */
        $journauxDossiers = [];
        foreach ($journalDossiers as $journalDossier)
        {
            if ($idJournaDossierBc == $journalDossier->getId() || !array_key_exists($journalDossier->getId(),$journalDossiersUseds))
                $journauxDossiers[] = $journalDossier;
        }

        return $this->render('BanqueBundle:JournalBanque:pcc_bc_admin.html.twig',[
            'pccs' => $pccs,
            'idPccBc' => $idPccBc,
            'journauxDossiers' => $journauxDossiers,
            'idJournaDossierBc' => $idJournaDossierBc
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function pccBanqueCompteChangeAction(Request $request)
    {
        /*pcc: $('#id_pcc_banque_compte').val(),
        banque_compte: $('#js_banque_compte').val()*/

        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
        $pccId = Boost::deboost($request->request->get('pcc'),$this);
        if(is_bool($banqueCompte) || is_bool($pccId)) return new Response('security');

        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);

        $pcc = null;
        if ($banqueCompte)
        {
            $em = $this->getDoctrine()->getManager();
            $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')
                ->find($pccId);
            $banqueCompte->setPcc($pcc);
            $em->flush();
        }

        return new Response($pcc ? $pcc->getId() : 0);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function journalDossierBanqueCompteChangeAction(Request $request)
    {
        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
        $journalDossierId = Boost::deboost($request->request->get('journal_dossier'),$this);
        if(is_bool($banqueCompte) || is_bool($journalDossierId)) return new Response('security');

        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);

        $journalDossier = null;
        if ($banqueCompte)
        {
            $em = $this->getDoctrine()->getManager();
            $journalDossier = $this->getDoctrine()->getRepository('AppBundle:JournalDossier')
                ->find($journalDossierId);
            $banqueCompte->setJournalDossier($journalDossier);
            $em->flush();
        }

        return new Response($journalDossier ? $journalDossier->getId() : 0);
    }

    /**
     * @param Request $request
     * @return $this|Response
     */
    public function exportAction(Request $request)
    {
        $extension = $request->request->get('extension');
        $banqueCompte = Boost::deboost($request->request->get('exp_banque_compte'),$this);
        if(is_bool($banqueCompte)) return new Response('security');
        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);
        $datas = json_decode(urldecode($request->request->get('datas')));
        $dossier = $banqueCompte->getDossier();
        $exercice = intval($request->request->get('exp_exercice'));
        $dateNow = new \DateTime();

        $title = 'JOURNAL DE BANQUE';
        $name = $title;
        $name .= '_'.$dossier->getSite()->getClient()->getNom().'-'.$dossier->getNom().'.'.$extension;
        $name = str_replace(' ','_',$name);

        if($extension == 'xls')
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
                ->setCellValue('B5',$exercice)
                ->setCellValue('A6','Editer le')
                ->setCellValue('B6',$dateNow->format('d-m-Y'));

            //entetes
            $sheet->setCellValue('A8','Date');
            $sheet->setCellValue('B8','Journal');
            $sheet->setCellValue('C8','Compte');
            $sheet->setCellValue('D8','Image');
            $sheet->setCellValue('E8','Libellé');
            $sheet->setCellValue('F8','Débit');
            $sheet->setCellValue('G8','Crédit');

            $index = 9;
            foreach ($datas as $data)
            {
                $sheet->setCellValue('A'.$index,$data->d);
                $sheet->setCellValue('B'.$index,$data->jnl);
                $sheet->setCellValue('C'.$index,$data->c->l);
                $sheet->setCellValue('D'.$index,$data->i);
                $sheet->setCellValue('E'.$index,$data->l);
                $sheet->setCellValue('F'.$index,round($data->db,2));
                $sheet->setCellValue('G'.$index,round($data->cr,2));
                $index++;
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
        }
        elseif ($extension == 'xml')
        {
            $document = new \DOMDocument();
            $document->preserveWhiteSpace = false;
            $document->formatOutput = true;
            $document->encoding = 'ISO-8859-1';

            //Définition du noeud principal du fichier xml
            $XMLRoot = $document->createElement('journal_banque');
            $document->appendChild($XMLRoot);

            //entetes
            $XMLcabinet= $document->createElement('cabinet',utf8_encode($dossier->getSite()->getClient()->getNom()));
            $XMLRoot->appendChild($XMLcabinet);

            $XMLsite= $document->createElement('site',utf8_encode($dossier->getSite()->getNom()));
            $XMLRoot->appendChild($XMLsite);

            $XMLdossier= $document->createElement('dossier',utf8_encode($dossier->getNom()));
            $XMLRoot->appendChild($XMLdossier);

            $XMLexercice= $document->createElement('exercice',utf8_encode($exercice));
            $XMLRoot->appendChild($XMLexercice);

            //journaux
            $XMLjournaux = $document->createElement('journaux');
            $XMLRoot->appendChild($XMLjournaux);

            foreach ($datas as $data)
            {
                $XMLjournal = $document->createElement('journal');
                $XMLjournaux->appendChild($XMLjournal);

                $XMLdate = $document->createElement('date',utf8_encode($data->d));
                $XMLjournal->appendChild($XMLdate);

                $XMLCodeJnl = $document->createElement('date',utf8_encode($data->jnl));
                $XMLjournal->appendChild($XMLCodeJnl);

                $XMLpiece = $document->createElement('piece',utf8_encode($data->i));
                $XMLjournal->appendChild($XMLpiece);

                $XMLlibelle = $document->createElement('libelle',utf8_encode($data->l));
                $XMLjournal->appendChild($XMLlibelle);

                $XMLcompte = $document->createElement('compte',utf8_encode($data->c->l));
                $XMLjournal->appendChild($XMLcompte);

                $XMLdebit = $document->createElement('debit',utf8_encode($data->db));
                $XMLjournal->appendChild($XMLdebit);

                $XMLcredit = $document->createElement('credit',utf8_encode($data->cr));
                $XMLjournal->appendChild($XMLcredit);
            }

            $document->save($name);
            $response = new BinaryFileResponse($name);
            $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $name
            );
            $response->headers->set('Content-Type', 'text/xml; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);
            return $response->deleteFileAfterSend(true);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function filtreDateAction(Request $request)
    {
        /** @var \DateTime $start */
        $start = \DateTime::createFromFormat('d/m/Y',$request->request->get('start'));
        if ($start === false || array_sum($start::getLastErrors()))
            $start = null;
        /** @var \DateTime $end */
        $end = \DateTime::createFromFormat('d/m/Y',$request->request->get('end'));
        if ($end === false || array_sum($end::getLastErrors()))
            $end = null;
        $type = intval($request->request->get('type'));

        return $this->render('BanqueBundle:JournalBanque:filtre-date.html.twig',[
            'start' => $start,
            'end' => $end,
            'type' => $type
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function paramsAction(Request $request)
    {
        $action = intval($request->request->get('action'));
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $em = $this->getDoctrine()->getManager();
        $add = false;
        /** @var Pcc $pcc */
        $pcc = null;
        /** @var JournalDossier $journalDossier */
        $journalDossier = null;

        if ($action == 0)
        {
            $pccsBanques = $this->getDoctrine()->getRepository('AppBundle:Pcc')
                ->getPccBanque($dossier);
            $journalDossiers = $this->getDoctrine()->getRepository('AppBundle:JournalDossier')
                ->getJournaux($dossier);

            return $this->render('BanqueBundle:JournalBanque:params.html.twig',
                [
                    'pccsBanques' => $pccsBanques,
                    'journalDossiers' => $journalDossiers
                ]);
        }
        elseif ($action == 1 || $action == 2)
        {
            $code = strtoupper(trim($request->request->get('code')));
            $libelle = strtoupper(trim($request->request->get('libelle')));

            $type = intval($request->request->get('type'));
            $entity = Boost::deboost($request->request->get('entity'),$this);

            if ($type == 0)
            {
                $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')
                    ->find($entity);

                if (!$pcc)
                {
                    $pcc = new Pcc();
                    $add = true;
                }
                $pcc
                    ->setCompte($code)
                    ->setIntitule($libelle)
                    ->setDossier($dossier);

                if ($add) $em->persist($pcc);
            }
            else
            {
                $journalDossier = $this->getDoctrine()->getRepository('AppBundle:JournalDossier')
                    ->find($entity);

                if (!$journalDossier)
                {
                    $journalDossier = new JournalDossier();
                    $add = true;
                }

                $journalDossier
                    ->setCodeStr($code)
                    ->setCode($code)
                    ->setLibelle($libelle)
                    ->setDossier($dossier);

                if ($add)
                {
                    $journal = $this->getDoctrine()->getRepository('AppBundle:Journal')
                        ->find(2);
                    $journalDossier->setJournal($journal);
                    $em->persist($journalDossier);
                }
            }
        }

        try
        {
            $em->flush();
        }
        catch (UniqueConstraintViolationException $ex)
        {
            return new Response(-1);
        }

        if ($add)
        {
            $html = '
                <tr data-id="'.($pcc ? $pcc->getId() : $journalDossier->getId()).'">
                    <td><input type="text" class="input-in-jqgrid cl_edit cl_compte" data-action="2" value="'.($pcc? $pcc->getCompte() : $journalDossier->getCodeStr()).'"></td>
                    <td><input type="text" class="input-in-jqgrid cl_edit cl_intitule" data-action="2" value="'.($pcc ? $pcc->getIntitule() : $journalDossier->getLibelle()).'"></td>
                </tr>            
            ';
            return new Response($html);
        }

        return new Response(1);
    }
}
