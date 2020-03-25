<?php

namespace JournalBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Ecriture;
use AppBundle\Entity\Image;
use AppBundle\Entity\JournalDossier;
use Doctrine\ORM\Query\Expr\Math;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('JournalBundle:Default:index.html.twig');
    }

    public function journalDossierAction(Request $request){
        if(!$request->isXmlHttpRequest())
            throw new AccessDeniedHttpException('Accès refusé');

        $dossierid = Boost::deboost($request->query->get('dossierid'), $this);

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierid);

        $exercice = $request->query->get('exercice');

        /** @var JournalDossier[] $journalDossiers */
        $journalDossiersTmps = $this->getDoctrine()
            ->getRepository('AppBundle:JournalDossier')
            ->getJournaux($dossier);

        $journalDossiers = [];

        foreach ($journalDossiersTmps as $journalDossiersTmp){
            if(strpos(strtolower($journalDossiersTmp->getCodeStr()), 'bq') !== FALSE){
                continue;
            }
            $journalDossiers[] = $journalDossiersTmp;
        }

        /** @var JournalDossier[] $journauxPicdocActifs */
        $journauxPicdocActifs = $this->getDoctrine()
            ->getRepository('AppBundle:JournalDossier')
            ->getJournauxPicdocActifs($dossier, $exercice);

        $journauxComptaActifs = $this->getDoctrine()
            ->getRepository('AppBundle:JournalDossier')
            ->getJournauxComptaActifs($dossier, $exercice);

        return $this->render('@Journal/Default/journal-dossier-option.html.twig',
            [
                'journalDossiers' => $journalDossiers,
                'journalPicdocActifs' => $journauxPicdocActifs,
                'journalComptaActifs' => $journauxComptaActifs
            ]);
    }

    public function journalDetailsAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
            throw new AccessDeniedHttpException('Accès refusé');

        $post = $request->request;

        $ecrituresPicDoc = [];
        $ecrituresCompta = [];
        $ecritures = [];

        $imageid = $post->get('image');
        $image = null;

        if($imageid !== null){
            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find($imageid);
        }

        $dossierId = Boost::deboost($post->get('dossier'), $this);

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $exercice = $post->get('exercice');
        $journalDossierId = Boost::deboost($post->get('journalDossier'), $this);

        $periode = json_decode($post->get('periode'));

        $fromPicDoc = ($post->get('frompicdoc') === 'true') ? true : false;
        $fromCompta = ($post->get('fromcompta') === 'true') ? true : false;

        $dateDossier = $this->getDoctrine()->getRepository('AppBundle:TbimagePeriode')
            ->getAnneeMoisExercices($dossier, $exercice);

        /** @var \DateTime $dateCloture */
        $dateCloture = $dateDossier->c;

        $mois = [];
        foreach ($periode->p as $p)
            foreach ($p->moiss as $m)
                if (!in_array($m, $mois)) $mois[] = $m;

        $dateMax = clone  $dateCloture;
        $dateIntervals = [];
        foreach (array_reverse($periode->m) as $key => $m) {
            $max = clone $dateMax;
            $min = clone $max;
            $min->sub(new \DateInterval('P1M'));

            $dateMax = clone $min;
            if ($key == 0) $max = \DateTime::createFromFormat('Y-m-d', '2200-12-31');
            elseif ($key == 11) $min = \DateTime::createFromFormat('Y-m-d', '1900-01-01');

            if (in_array($m, $mois))
                $dateIntervals[$m] = (object)
                [
                    'max' => $max,
                    'min' => $min
                ];
        }

        if($image === null) {
            if ($fromPicDoc) {
                $ecrituresPicDoc = $this->getDoctrine()
                    ->getRepository('AppBundle:ImputationControle')
                    ->getInfoJournal($dossierId, $journalDossierId, intval($exercice), $dateIntervals, null );
            }
            if ($fromCompta) {
                $ecrituresCompta = $this->getDoctrine()
                    ->getRepository('AppBundle:Ecriture')
                    ->getInfoJournal($dossierId, $journalDossierId, intval($exercice), $dateIntervals);
            }
        }
        else{

            $ecrituresPicDoc = $this->getDoctrine()
                ->getRepository('AppBundle:ImputationControle')
                ->getInfoJournal($dossierId, $journalDossierId, intval($exercice), $dateIntervals, $image->getId());
        }

        if(count($ecrituresPicDoc) > 0 && count($ecrituresCompta) > 0) {
            foreach ($ecrituresPicDoc as $key => $value) {

                $imagePicdoc = $value['image'];
                $inCompta = false;
                $comptaKeys = [];
                foreach ($ecrituresCompta as $cK => $ecritureCompta){
                    $imageCompta = $ecritureCompta['image'];

                    if($imagePicdoc === $imageCompta){
                        $inCompta = true;
                        $comptaKeys[] = $cK;
                    }
                }
                if ($inCompta) {
                    $correct = false;

                    $comptaPicDoc = $value['compta'];

                    $debitsPicdoc = 0;
                    $creditsPicdoc = 0;

                    foreach ($comptaPicDoc['debit'] as $deb) {
                        $debitsPicdoc += $deb['montant'];
                    }

                    foreach ($comptaPicDoc['credit'] as $crd) {
                        $creditsPicdoc += $crd['montant'];
                    }

                    foreach ($comptaKeys as $comptaKey) {
                        $debitsCompta = 0;
                        $creditsCompta = 0;

                        $comptaCompta = $ecrituresCompta[$comptaKey]['compta'];
                        if (isset($comptaCompta['debit'])) {
                            foreach ($comptaCompta['debit'] as $deb) {
                                $debitsCompta += $deb['montant'];
                            }
                        }

                        if (isset($comptaCompta['credit'])) {
                            foreach ($comptaCompta['credit'] as $crd) {
                                $creditsCompta += $crd['montant'];
                            }
                        }

                        if (abs((float)$debitsCompta - (float)$debitsPicdoc) <= 0.1 &&
                            abs((float)$creditsCompta - (float)$creditsPicdoc) <= 0.1) {
                            $correct = true;

                            $ecritures[$key] = $ecrituresCompta[$comptaKey];
                            break;
                        }
                    }

                    if (!$correct) {

                        $val = $ecrituresCompta[$comptaKey];
                        $val['remarque'] = 'Compta # PicDoc';

                        $ecritures[$key] = $val;
                    }

                } else {
                    $value['remarque'] = 'N\'existe pas dans compta';
                    $ecritures[$key] = $value;
                }
            }
            foreach ($ecrituresCompta as $key => $value) {

                $imageCompta = $value['image'];
                $inEcriture = false;
                foreach ($ecritures as $ecriture){
                    $imageEcriture = $ecriture['image'];
                    if($imageCompta === $imageEcriture){
                        $inEcriture = true;
                        break;
                    }
                }

                if (!$inEcriture) {
                    $ecritures[$key] = $value;
                }
            }
        }
        else {
            $ecritures = array_merge($ecrituresPicDoc, $ecrituresCompta);
        }

        $rows = [];

        $i = 0;

        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($ecritures as $key => $ecriture){

            $remarqueType = -1;
            switch ($ecriture['remarque']){
                case 'N\'existe pas dans compta':
                    $remarqueType = 0;
                    break;
                case 'Compta # PicDoc':
                    $remarqueType = 1;
                    break;
                default:
                    break;
            }

            $compta = $ecriture['compta'];

            if(count($compta) === 2) {
                $debits = $compta['debit'];
                $credits = $compta['credit'];
            }
            else
                continue;

            foreach ($credits as $credit) {

                if($credit['montant'] < 0){
                    $rows[] = ['id' => $i, 'cell' => [
                        'j_libelle' => $ecriture['libelle'],
                        'j_lettre' => $ecriture['lettre'],
                        'j_journal' => $ecriture['journal'],
                        'j_devise' => $ecriture['devise'],
                        'j_date' => $ecriture['date'],
                        'j_image' => $ecriture['image'],
                        'j_image_id' => Boost::boost($key),
                        'j_compte' => $credit['compte'],
                        'j_debit' => -$credit['montant'],
                        'j_credit' => 0,
                        'j_remarque' => $ecriture['remarque'],
                        'j_remarque_type' => $remarqueType,
                        'j_image_id_nc' => $key,
                        'j_compte_id' => $credit['compte_id'],
                        'j_type_compte' => $credit['type_compte'],
                        'j_journal_dossier_id' => $ecriture['journal_dossier']
                    ]
                    ];

                    $totalDebit += -$credit['montant'];
                }
                else {
                    $rows[] = ['id' => $i, 'cell' => [
                        'j_libelle' => $ecriture['libelle'],
                        'j_lettre' => $ecriture['lettre'],
                        'j_journal' => $ecriture['journal'],
                        'j_devise' => $ecriture['devise'],
                        'j_date' => $ecriture['date'],
                        'j_image' => $ecriture['image'],
                        'j_image_id' => Boost::boost($key),
                        'j_compte' => $credit['compte'],
                        'j_debit' => 0,
                        'j_credit' => $credit['montant'],
                        'j_remarque' => $ecriture['remarque'],
                        'j_remarque_type' => $remarqueType,
                        'j_image_id_nc' => $key,
                        'j_compte_id' => $credit['compte_id'],
                        'j_type_compte' => $credit['type_compte'],
                        'j_journal_dossier_id' => $ecriture['journal_dossier']
                    ]
                    ];

                    $totalCredit += $credit['montant'];
                }
                $i++;
            }

            foreach ($debits as $debit){

                if($debit['montant'] < 0){
                    $rows[] = ['id' => $i, 'cell' => [
                        'j_libelle' => $ecriture['libelle'],
                        'j_lettre' => $ecriture['lettre'],
                        'j_journal' => $ecriture['journal'],
                        'j_devise' => $ecriture['devise'],
                        'j_date' => $ecriture['date'],
                        'j_image' => $ecriture['image'],
                        'j_image_id' => Boost::boost($key),
                        'j_compte' => $debit['compte'],
                        'j_debit' => 0,
                        'j_credit' => -$debit['montant'],
                        'j_remarque' => $ecriture['remarque'],
                        'j_remarque_type' => $remarqueType,
                        'j_image_id_nc' => $key,
                        'j_compte_id' => $credit['compte_id'],
                        'j_type_compte' => $credit['type_compte'],
                        'j_journal_dossier_id' => $ecriture['journal_dossier']
                    ]
                    ];

                    $totalCredit += -$debit['montant'];
                }
                else {
                    $rows[] = ['id' => $i, 'cell' => [
                        'j_libelle' => $ecriture['libelle'],
                        'j_lettre' => $ecriture['lettre'],
                        'j_journal' => $ecriture['journal'],
                        'j_devise' => $ecriture['devise'],
                        'j_date' => $ecriture['date'],
                        'j_image' => $ecriture['image'],
                        'j_image_id' => Boost::boost($key),
                        'j_compte' => $debit['compte'],
                        'j_debit' => $debit['montant'],
                        'j_credit' => 0,
                        'j_remarque' => $ecriture['remarque'],
                        'j_remarque_type' => $remarqueType,
                        'j_image_id_nc' => $key,
                        'j_compte_id' => $credit['compte_id'],
                        'j_type_compte' => $credit['type_compte'],
                        'j_journal_dossier_id' => $ecriture['journal_dossier']
                    ]
                    ];

                    $totalDebit += $debit['montant'];
                }
                $i++;
            }
        }
        return new JsonResponse([
            'rows' => $rows,
            'userdata' => ['j_debit' => $totalDebit,'j_credit' => $totalCredit]
        ]);
    }

    public function journalExportAction(Request $request)
    {

        $post = $request->request;
        $datas = json_decode(urldecode($request->request->get('datas')));
        $extension = $post->get('extension');
        $dossierId = Boost::deboost($post->get('exp_dossier'), $this);
        $journalDossierId = Boost::deboost($post->get('exp_journal_dossier'), $this);
        $exercice = $post->get('exp_exercice');

        $dateNow = new \DateTime();

        /** @var Dossier $dossier */
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        /** @var JournalDossier $journalDossier */
        $journalDossier = $this->getDoctrine()
            ->getRepository('AppBundle:JournalDossier')
            ->find($journalDossierId);

        $title = 'JOURNAL';

        if($journalDossier !== null){
            $title .= ' '.strtoupper($journalDossier->getLibelle());
        }


        $name = $title;
        $name .= '_' . $dossier->getSite()->getClient()->getNom() . '-' . $dossier->getNom() . '.' . $extension;
        $name = str_replace(' ', '_', $name);


        if ($extension == 'xls') {

            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
            $backgroundTitle = '808080';
            $phpExcelObject->getProperties()
                ->setCreator("picdata")
                ->setLastModifiedBy("picdata")
                ->setTitle("Office 2005 XLSX Test Document")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("Test result file");
            $sheet = $phpExcelObject->setActiveSheetIndex(0);

            //Titre
            $sheet->setCellValue('A1', $title)
                ->setCellValue('A2', 'Client')
                ->setCellValue('B2', $dossier->getSite()->getClient()->getNom())
                ->setCellValue('A3', 'Site')
                ->setCellValue('B3', $dossier->getSite()->getNom())
                ->setCellValue('A4', 'Dossier')
                ->setCellValue('B4', $dossier->getNom())
                ->setCellValue('A5', 'Exercice')
                ->setCellValue('B5', $exercice)
                ->setCellValue('A6', 'Editer le')
                ->setCellValue('B6', $dateNow->format('d-m-Y'));

            //Entetes
            $sheet->setCellValue('A8', 'Date');
            $sheet->setCellValue('B8', 'Image');
            $sheet->setCellValue('C8', 'Journal');
            $sheet->setCellValue('D8', 'Compte');
            $sheet->setCellValue('E8', 'Libellé');
            $sheet->setCellValue('F8', 'Débit');
            $sheet->setCellValue('G8', 'Crédit');
            $sheet->setCellValue('H8', 'Remarque');

            $index = 9;
            foreach ($datas as $d) {
                $data = $d->cell;

                $sheet->setCellValue('A' . $index, $data->j_date);
                $sheet->setCellValue('B' . $index, $data->j_image);
                $sheet->setCellValue('C' . $index, $data->j_journal);
                $sheet->setCellValue('D' . $index, $data->j_compte);
                $sheet->setCellValue('E'.$index,$data->j_libelle);
                $sheet->setCellValue('F' . $index, round($data->j_debit, 2));
                $sheet->setCellValue('G' . $index, round($data->j_credit, 2));
                $sheet->setCellValue('H' . $index, $data->j_remarque);
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

        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->encoding = 'ISO-8859-1';

        //Définition du noeud principal du fichier xml
        $XMLRoot = $document->createElement(str_replace('\'', '_', str_replace(' ','_',strtolower($title))));
        $document->appendChild($XMLRoot);

        //entetes
        $XMLcabinet = $document->createElement('cabinet', utf8_encode($dossier->getSite()->getClient()->getNom()));
        $XMLRoot->appendChild($XMLcabinet);

        $XMLsite = $document->createElement('site', utf8_encode($dossier->getSite()->getNom()));
        $XMLRoot->appendChild($XMLsite);

        $XMLdossier = $document->createElement('dossier', utf8_encode($dossier->getNom()));
        $XMLRoot->appendChild($XMLdossier);

        $XMLexercice = $document->createElement('exercice', utf8_encode($exercice));
        $XMLRoot->appendChild($XMLexercice);

        //journaux
        $XMLjournaux = $document->createElement('journaux');
        $XMLRoot->appendChild($XMLjournaux);

        foreach ($datas as $d) {

            $data = $d->cell;

            $XMLjournal = $document->createElement('journal');
            $XMLjournaux->appendChild($XMLjournal);

            $XMLdate = $document->createElement('date', utf8_encode($data->j_date));
            $XMLjournal->appendChild($XMLdate);

            $XMLCodeJnl = $document->createElement('date', utf8_encode($data->j_journal));
            $XMLjournal->appendChild($XMLCodeJnl);

            $XMLpiece = $document->createElement('piece', utf8_encode($data->j_image));
            $XMLjournal->appendChild($XMLpiece);

            $XMLlibelle = $document->createElement('libelle', utf8_encode($data->j_libelle));
            $XMLjournal->appendChild($XMLlibelle);

            $XMLcompte = $document->createElement('compte', utf8_encode($data->j_compte));
            $XMLjournal->appendChild($XMLcompte);

            $XMLdebit = $document->createElement('debit', utf8_encode($data->j_debit));
            $XMLjournal->appendChild($XMLdebit);

            $XMLcredit = $document->createElement('credit', utf8_encode($data->j_credit));
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

    public function historiqueUploadAction(Request $request){
        if(!$request->isXmlHttpRequest())
            throw new AccessDeniedHttpException('Accès refusé');

        $post = $request->query;


        $exercice = $post->get('exercice');

        $dossierId = Boost::deboost($post->get('dossier'), $this);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $cloture = $dossier->getCloture();
        if (!$cloture || $cloture == 0) $cloture = 12;
        $clotureMois = $this->getDoctrine()
            ->getRepository('AppBundle:TbimagePeriode')
            ->getAnneeMoisExercices($dossier,$exercice);

        $clotureMoisN_1 = $this->getDoctrine()
            ->getRepository('AppBundle:TbimagePeriode')
            ->getAnneeMoisExercices($dossier,$exercice - 1);


        $importN = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')
            ->getLastDossier($dossier,$exercice,false);
        $importN_1 = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')
            ->getLastDossier($dossier,$exercice - 1,false);

        return new JsonResponse(
            (object)
            [
                'importN' => $importN,
                'importN_1' => $importN_1,
                'cl' => $cloture,
                'dc' => $clotureMois->c->format('d/m/Y'),
                'dcN_1' => $clotureMoisN_1->c->format('d/m/Y')
            ]
        );

    }

    public function addEcritureAction(Request $request){
        if(!$request->isXmlHttpRequest())
            throw new AccessDeniedHttpException();

        $datas =json_decode($request->request->get('datas'));

        $type = 'success';
        $message = '';
        $tiers = null;
        $pcc = null;

        $em = $this->getDoctrine()->getManager();

        foreach ($datas as $data) {
            $typeCompte = $data->j_type_compte;

            $debit = strip_tags($data->j_debit);
            $credit = strip_tags($data->j_credit);

            /** @var Image $image */
            $image = $this->getDoctrine()
                ->getRepository('AppBundle:Image')
                ->find($data->j_image_id_nc);

            $dateEcriture = \DateTime::createFromFormat('d/m/Y', $data->j_date);

            $journalDossier = $this->getDoctrine()
                ->getRepository('AppBundle:JournalDossier')
                ->find($data->j_journal_dossier_id);

            $libelle = $data->j_libelle;

            if (trim($libelle) === '') {
                $type = 'error';
                $message = 'Pas de libellé';
                break;
            }

            if ($dateEcriture === FALSE) {
                $type = 'error';
                $message = 'Pas de date';
                break;
            }

            if ($journalDossier === null) {
                $type = 'error';
                $message = 'Journal introuvable';
                break;
            }

            switch ($typeCompte) {
                case 'pcc':
                    $pcc = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->find($data->j_compte_id);
                    break;
                case 'tiers':
                    $tiers = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($data->j_compte_id);
                    break;
            }

            if ($pcc === null && $tiers === null) {
                $type = 'error';
                $message = 'Il n\'y pas de compte';
                break;
            }

            if (floatval($debit) == 0 && floatval($credit) == 0) {
                $type = 'error';
                $message = 'Montant débit & crédit 0';
                break;
            }


            $ecriture = new Ecriture();
            $ecriture->setImage($image)
                ->setDebit($debit)
                ->setCredit($credit)
                ->setPcc($pcc)
                ->setTiers($tiers)
                ->setDossier($image->getLot()->getDossier())
                ->setJournalDossier($journalDossier)
                ->setExercice($image->getExercice())
                ->setDateEcr($dateEcriture)
                ->setLibelle($libelle);

//            $em->persist($ecriture);

        }

//        $em->flush();

        return new JsonResponse(['type' => $type, 'message' =>  $message]);
    }


    public function centralisateurAction(Request $request){

        $post = $request->request;
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($post->get('dossier'), $this));
        $exercice = $post->get('exercice');

        $centralisateurCompta = $this->getDoctrine()
            ->getRepository('AppBundle:Ecriture')
            ->getInfoCentralisateur($dossier->getId(), $exercice);

        $ecrituresCompta = [];

        foreach ($centralisateurCompta as $centralisateur) {

            /** @var JournalDossier $journalTmp */
            $journalTmp = $this->getDoctrine()
                ->getRepository('AppBundle:JournalDossier')
                ->find($centralisateur->journal_dossier_id);

            if (strpos('bq', strtolower($journalTmp->getCodeStr())) !== false)
                continue;


            if($journalTmp->getJournal()->getId() === 1){

                $dateCloture = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->getDateCloture($dossier,$exercice);


                $dateAnouveau = $dateCloture->format('Y-m');

                $centralisateur->mois = $dateAnouveau;
            }

            if (!isset($ecrituresCompta[$centralisateur->journal_dossier_id][$centralisateur->mois])) {
                $ecrituresCompta[$centralisateur->journal_dossier_id][$centralisateur->mois] =
                    ['debit' => $centralisateur->debit, 'credit' => $centralisateur->credit];
            }
            $ecrituresCompta[$centralisateur->journal_dossier_id][$centralisateur->mois]['debit'] = $centralisateur->debit;
            $ecrituresCompta[$centralisateur->journal_dossier_id][$centralisateur->mois]['credit'] = $centralisateur->credit;
        }

        $dossierId = Boost::deboost($post->get('dossier'), $this);

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);


        $exercice = $post->get('exercice');


        $dateIntervals = [1,2,3,4,5,6,7,8,9,10,11,12];


        $ecrituresPicDoc = $this->getDoctrine()
            ->getRepository('AppBundle:ImputationControle')
            ->getInfoJournal($dossier->getId(), 0, intval($exercice), $dateIntervals, null);

        $ecritureJournal = [];

        foreach ($ecrituresPicDoc as $ecriturePicDoc) {

            $journalDossier = $ecriturePicDoc['journal_dossier'];
            $date = \DateTime::createFromFormat('Y-m-d', $ecriturePicDoc['date'])->format('Y-m');

            $compta = $ecriturePicDoc['compta'];

            $debits = $compta['debit'];
            $credits = $compta['credit'];

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($credits as $credit) {
                if($credit['montant'] < 0){
                    $totalDebit += -$credit['montant'];
                }
                else {
                    $totalCredit += $credit['montant'];
                }
            }

            foreach ($debits as $debit) {
                if ($debit['montant'] < 0) {
                    $totalCredit += -$debit['montant'];
                } else {
                    $totalDebit += $debit['montant'];
                }
            }

            if (!isset($ecritureJournal[$journalDossier][$date])) {
                $ecritureJournal[$journalDossier][$date] = ['debit' => $totalDebit, 'credit' => $totalCredit];
            }

            else {
                $ecritureJournal[$journalDossier][$date]['debit'] += $totalDebit;
                $ecritureJournal[$journalDossier][$date]['credit'] += $totalCredit;
            }
        }

        $centralisateurComptaPic = [];

        foreach ($ecrituresCompta as $key => $value){
            foreach ($value as $k => $v){
                $centralisateurComptaPic[$key][$k]['compta'] = $v;
            }
        }
        foreach ($ecritureJournal as $key => $value){
            foreach ($value as $k => $v){
                $centralisateurComptaPic[$key][$k]['picdoc'] = $v;
            }
        }

        $rows = [];
        $i = 0;

        foreach ($centralisateurComptaPic as $key => $value){
            $totalDebitCompta = 0;
            $totalCreditCompta = 0;

            $totalDebitPicdoc = 0;
            $totalCredtPicdoc = 0;


            $journalDossier = $this->getDoctrine()
                ->getRepository('AppBundle:JournalDossier')
                ->find($key);

            foreach ($value as $k => $v){

                $cDate = $k;
                $cJournal = $journalDossier->getCodeStr();
                $cLibelle = $journalDossier->getLibelle();
                $debitCompta = 0;
                $creditCompta = 0;
                $debitPicdoc = 0;
                $creditPicdoc = 0;

                if(isset($v['compta'])){
                    $debitCompta = $v['compta']['debit'];
                    $creditCompta = $v['compta']['credit'];

                    $totalDebitCompta +=$debitCompta;
                    $totalCreditCompta +=$creditCompta;
                }

                if(isset($v['picdoc'])){
                    $debitPicdoc = $v['picdoc']['debit'];
                    $creditPicdoc = $v['picdoc']['credit'];

                    $totalDebitPicdoc += $debitPicdoc;
                    $totalCredtPicdoc += $creditPicdoc;
                }



                $remarque = '';
                if(abs((float)$debitPicdoc - (float) $debitCompta) > 0.1){
                    $remarque = 'Compta # Robot';
                }

                $rows[] = [
                    'id' => $i,
                    'cell' => [
                        'c_date' => $cDate,
                        'c_journal' => $cJournal,
                        'c_libelle' => $cLibelle,
                        'c_debit_compta' => $debitCompta,
                        'c_credit_compta' => $creditCompta,
                        'c_debit_picdoc' => $debitPicdoc,
                        'c_credit_picdoc' => $creditPicdoc,
                        'c_remarque' => $remarque
                    ]
                ];

                $i++;
            }

            $rows[] = [
                'id' => $i,
                'cell' => [
                    'c_date' => '',
                    'c_journal' => '',
                    'c_libelle' => 'TOTAL',
                    'c_debit_compta' => $totalDebitCompta,
                    'c_credit_compta' => $totalCreditCompta,
                    'c_debit_picdoc' => $totalDebitPicdoc,
                    'c_credit_picdoc' => $totalCredtPicdoc,
                    'c_remarque' => ''
                ]
            ];

            $i++;


        }

        return new JsonResponse([
            'rows' => $rows,
            'ecritureJournal' => $ecritureJournal,
            'ecriturePicdo' => $ecrituresPicDoc
        ]);
    }

    public function imageIdAction(Request $request){
        $nomImage = $request->get('image');

        $images = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->findBy(['nom' => $nomImage]);

        if(count($images) > 0){
            return new JsonResponse(Boost::boost($images[0]->getId()));
        }

        return new JsonResponse(Boost::boost(-1));

    }



}
