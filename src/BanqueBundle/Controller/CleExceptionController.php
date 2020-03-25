<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 17/06/2019
 * Time: 15:29
 */

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Controller\StringExt;
use AppBundle\Entity\Banque;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\CleExceptionPm;
use AppBundle\Entity\Dossier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CleExceptionController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function ClePmExceptionContainerAction(Request $request)
    {
        return $this->render('BanqueBundle:BanquePm:cle-exception-container.html.twig');
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function ClePmExceptionAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $cleDossiers = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
            ->getCleAvecPieceForDossier($dossier);

        $results = [];
        foreach ($cleDossiers as $cleDossier)
        {
            $cleException = $this->getDoctrine()->getRepository('AppBundle:CleExceptionPm')
                ->cleExceptionForCleDossier($cleDossier);

            $results[] = (object)
            [
                'id' => Boost::boost($cleDossier->getId()),
                'cle' => $cleDossier->getCle()->getCle(),
                'pp' => $cleDossier->getPasPiece(),
                's' => $cleException ? $cleException->getSens() : 0,
                'f' => $cleException ? $cleException->getFormule() : '',
                's2' => $cleException ? $cleException->getSens2() : 0,
                'f2' => $cleException ? $cleException->getFormule2() : '',
                'mc' => $cleException ? $cleException->getMotCle() : ''
            ];
        }

        return new JsonResponse($results);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function cleExceptionParamsAction(Request $request)
    {
        $cleDossier = Boost::deboost($request->request->get('cle_dossier'),$this);
        if(is_bool($cleDossier)) return new Response('security');
        $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
            ->find($cleDossier);

        $cleExceptionPms = $this->getDoctrine()->getRepository('AppBundle:CleExceptionPm')
            ->cleExceptionForCleDossier($cleDossier);

        $results = [];
        foreach ($cleExceptionPms as $cleExceptionPm)
        {
            $results[] = (object)
            [
                'id' => Boost::boost($cleExceptionPm->getId()),
                'min' => $cleExceptionPm->getMin(),
                'max' => $cleExceptionPm->getMax(),
                'sens' => (object)
                [
                    's' => $cleExceptionPm->getSens(),
                    'i' => $cleExceptionPm->getId()
                ]
            ];
        }

        for ($i = 0; $i < 2; $i++)
            $results[] = (object)
            [
                'id' => Boost::boost(0),
                'min' => 0,
                'max' => 0,
                'sens' => (object)
                [
                    's' => 0,
                    'i' => $i + 100
                ]
            ];

        return new JsonResponse($results);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function cleExceptionSaveAction(Request $request)
    {
        $cleDossier = Boost::deboost($request->request->get('cle_dossier'), $this);
        if(is_bool($cleDossier)) return new Response('security');
        $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
            ->find($cleDossier);

        $cleExceptionPm = $this->getDoctrine()->getRepository('AppBundle:CleExceptionPm')
            ->cleExceptionForCleDossier($cleDossier);

        $pasPiece = intval($request->request->get('pas_piece'));
        $motCle = trim($request->request->get('mot_cle'));
        $sens = intval($request->request->get('sens'));
        $formule = trim($request->request->get('formule'));
        $sens2 = intval($request->request->get('sens_2'));
        $formule2 = trim($request->request->get('formule_2'));
        $cleDossier->setPasPiece($pasPiece);

        $em = $this->getDoctrine()->getManager();
        if ($cleExceptionPm)
        {
            if ($pasPiece == 1 || ($formule == '' && $formule2 == ''))
                $em->remove($cleExceptionPm);
            else
                $cleExceptionPm
                    ->setSens($sens)
                    ->setFormule($formule)
                    ->setSens2($sens2)
                    ->setFormule2($formule2)
                    ->setMotCle($motCle);
        }
        else
        {
            if (($formule != '' || $formule2 != '') && $pasPiece != 1)
            {
                $cleExceptionPm = new CleExceptionPm();
                $cleExceptionPm
                    ->setCleDossier($cleDossier)
                    ->setSens($sens)
                    ->setFormule($formule)
                    ->setSens2($sens2)
                    ->setFormule2($formule2)
                    ->setMotCle($motCle);

                $em->persist($cleExceptionPm);
            }
        }

        $em->flush();

        return new Response(1);
    }

    public function exportAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('exp_dossier'),$this);
        $banque = Boost::deboost($request->request->get('exp_banque'),$this);
        $banqueCompte = Boost::deboost($request->request->get('exp_banque_compte'),$this);
        $exercice = intval($request->request->get('exp_exercice'));
        $extension = $request->request->get('extension');
        if(is_bool($dossier) || is_bool($banque) || is_bool($banqueCompte)) return new Response('security');

        /** @var Dossier $dossier */
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        /** @var Banque $banque */
        $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')
            ->find($banque);
        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);
        $datas = json_decode(urldecode($request->request->get('datas')));

        $type = intval($request->request->get('exp_type'));
        $title = $request->request->get('exp_title');

        /*return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => $datas
        ]);*/

        $dateNow = new \DateTime();

        $name = StringExt::sansAccent($title);
        $name .= '_'.$dossier->getSite()->getClient()->getNom().'-'.$dossier->getNom().'.'.$extension;
        $name = str_replace(' ','_',$name);

        if($extension == 'xls')
        {
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
            $backgroundTitle = '808080';
            $phpExcelObject->getProperties()->setCreator("liuggio")
                ->setLastModifiedBy("Scriptura")
                ->setTitle("Office 2005 XLSX Test Document")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("PM");
            $sheet = $phpExcelObject->setActiveSheetIndex(0);

            //titre
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
            $row = 7;
            if ($banqueCompte)
            {
                $sheet
                    ->setCellValue('A7', 'Banque')
                    ->setCellValue('B7', $banqueCompte->getBanque()->getNom())
                    ->setCellValue('A8', 'Compte')
                    ->setCellValue('B8', $banqueCompte->getNumcompte());

                $row += 3;
            }
            elseif ($banque)
            {
                $sheet
                    ->setCellValue('A7', 'Banque')
                    ->setCellValue('B7'.$row, $banque->getNom());

                $row += 2;
            }

            //entetes
            if ($type == 0)
            {
                $sheet
                    ->setCellValue('A'.$row,'Banque')
                    ->setCellValue('B'.$row,'Compte');

                $col = 'B';
                foreach ($datas->m as $m)
                {
                    $sheet
                        ->setCellValue(++$col.$row,$m);
                }
                $row++;
            }
            elseif (in_array($type,[2,3,4]))
            {
                $sheet
                    ->setCellValue('A'.$row,'Compte')
                    ->setCellValue('B'.$row,'Pièce')
                    ->setCellValue('C'.$row,'Date')
                    ->setCellValue('D'.$row,'Libellé');

                $col = 'E';
                if ($type != 2)
                    $sheet
                        ->setCellValue($col++.$row,'Recette');
                if ($type != 3)
                    $sheet
                        ->setCellValue($col++.$row,'Dépense');
                $sheet
                    ->setCellValue($col++.$row,'Bilan')
                    ->setCellValue($col++.$row,'Résultat')
                    ->setCellValue($col++.$row,'Tva');

                $row++;
            }

            //datas
            if ($type == 0)
            {
                foreach ($datas->datas as $data)
                {
                    $col = 'A';
                    foreach ($data as $key => $val)
                    {
                        if ($key == 'id') continue;

                        if (is_object($val))
                        {
                            $statusTexte =(intval($val->s) == 1) ? '1' : '';
                        }
                        else $statusTexte = $val;

                        $sheet
                            ->setCellValue($col++.$row,$statusTexte);
                    }
                    $row++;
                }
            }
            elseif (in_array($type,[2,3,4]))
            {
                foreach ($datas as $data)
                {
                    $sheet
                        ->setCellValue('A'.$row,$data->c)
                        ->setCellValue('B'.$row,$data->i->n)
                        ->setCellValue('C'.$row,$data->d)
                        ->setCellValue('D'.$row,$data->l);

                    $col = 'E';
                    if ($type != 2)
                        $sheet
                            ->setCellValue($col++.$row,Round($data->rc,2));
                    if ($type != 3)
                        $sheet
                            ->setCellValue($col++.$row,Round($data->dp,2));

                    $sheet
                        ->setCellValue($col++.$row,$data->b ? $data->b->l : '')
                        ->setCellValue($col++.$row,$data->ch ? $data->ch->l : '')
                        ->setCellValue($col++.$row,$data->tva ? $data->tva->l : '');

                    $row++;
                }
            }

            $phpExcelObject->getActiveSheet()->setTitle(strlen($title) > 31 ? substr($title,0,31) : $title);
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
    }
}