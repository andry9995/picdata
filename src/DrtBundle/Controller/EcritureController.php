<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/03/2019
 * Time: 09:22
 */

namespace DrtBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\EchangeEcriture;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageATraiter;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\TvaImputationControle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class EcritureController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function ecrituresAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($client) || is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $dossierSave = $dossier;
        $exercice = intval($request->request->get('exercice'));
        $echangeType = $this->getDoctrine()->getRepository('AppBundle:EchangeType')
            ->find($request->request->get('echange_type'));

        /** @var Dossier[] $dossiers */
        $dossiers = [];

        if ($dossier) $dossiers[] = $dossier;
        else
        {
            $client = $this->getDoctrine()->getRepository('AppBundle:Client')
                ->find($client);
            $dossiers = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(),$client,null,$exercice);
        }

        $echangesEcrs = [];
        foreach ($dossiers as $dossier)
        {
            $echangeItem = $this->getDoctrine()->getRepository('AppBundle:EchangeItem')
                ->getLastForDossier($echangeType,$dossier,$exercice);

            if ($echangeItem)
            {
                $echangeEcritures = $this->getDoctrine()->getRepository('AppBundle:EchangeEcriture')
                    ->getEcritures($echangeItem,$this->get('kernel')->getRootDir()."/../web/echange/".$echangeItem->getNomFichier(),!is_null($dossierSave),!is_null($dossierSave));

                foreach ($echangeEcritures as $echangeEcriture)
                    $echangesEcrs[] = $this->getEcritureEchange($echangeEcriture);
            }
        }

        return new JsonResponse($echangesEcrs);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function instructionChangeAction(Request $request)
    {
        $echangeEcriture = Boost::deboost($request->request->get('echange_ecriture'),$this);
        if(is_bool($echangeEcriture)) return new Response('security');
        /** @var EchangeEcriture $echangeEcriture */
        $echangeEcriture = $this->getDoctrine()->getRepository('AppBundle:EchangeEcriture')
            ->find($echangeEcriture);

        $instruction = intval($request->request->get('instruction'));
        $echangeEcriture
            ->setStatus($instruction)
            ->setImage(null)
            ->setPasPiece(0);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse($this->getEcritureEchange($echangeEcriture));
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function lettrerAction(Request $request)
    {
        $echangeEcriture = Boost::deboost($request->request->get('echange_ecriture'),$this);
        $image = Boost::deboost($request->request->get('image'),$this);

        if(is_bool($echangeEcriture) || is_bool($image)) return new Response('security');

        $echangeEcriture = $this->getDoctrine()->getRepository('AppBundle:EchangeEcriture')
            ->find($echangeEcriture);

        $image = $this->getDoctrine()->getRepository('AppBundle:Image')
            ->find($image);

        $echangeEcriture->setImage($image);
        if ($image) $echangeEcriture->setPasPiece(0);
        else $echangeEcriture->setPasPiece(1);

        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse($this->getEcritureEchange($echangeEcriture));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function lettrerImageAction(Request $request)
    {
        $echangeEcriture = Boost::deboost($request->request->get('echange_ecriture'),$this);
        /** @var EchangeEcriture $echangeEcriture */
        $echangeEcriture = $this->getDoctrine()->getRepository('AppBundle:EchangeEcriture')
            ->find($echangeEcriture);

        $idDemo = -1;
        if($request->isXmlHttpRequest())
        {
            $directory = "IMAGES";
            $fs = new Filesystem();
            try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }

            $dossier = $echangeEcriture->getEchangeItem()->getEchange()->getDossier();
            $exercice = $echangeEcriture->getEchangeItem()->getEchange()->getExercice();

            //creation dossier dateScan
            $dateNow = new \DateTime();
            $directory .= '/'.$dateNow->format('Ymd');
            try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }

            $em = $this->getDoctrine()->getManager();

            $file = $request->files->get('id_image');
            if ($file)
            {
                $lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $this->getUser(), '');
                $file_name = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $name = basename($file_name,'.'.$extension);
                $source = $this->getDoctrine()->getRepository('AppBundle:SourceImage')->getBySource('PICDATA');
                $file->move($directory, $file_name);
                $newName = $dateNow->format('Ymd').'_'.Boost::getUuid(50);
                $fs->rename($directory.'/'.$file_name, $directory.'/'.$newName.'.'.$extension);

                $image = new Image();
                $nbPage = 1;
                $chemin = $_SERVER['DOCUMENT_ROOT'] . '/IMAGES/' .$lot->getDateScan()->format('Ymd') . '/' . $newName . '.' . $extension;
                if (strtoupper($extension) == 'PDF')
                {
                    $stream = fopen($chemin, "r");
                    $content = fread ($stream, filesize($chemin));

                    $nbPage = 1;
                    if(!(!$stream || !$content))
                    {
                        $regex = "/\/Count\s+(\d+)/";
                        if(preg_match_all($regex, $content, $matches))
                            $nbPage = max(max($matches));
                    }

                    $image->setNbpage($nbPage);
                }

                $image
                    ->setModeReglement(null)
                    ->setLot($lot)
                    ->setExercice($exercice)
                    ->setExtImage($extension)
                    ->setNbpage($nbPage)
                    ->setNomTemp($newName)
                    ->setOriginale($name)
                    ->setSourceImage($source)
                    ->setCodeAnalytique(null)
                    ->setCommentaireDossier(null);

                if ($dossier->getSite()->getClient()->getId() == $idDemo) $image->setDownload(new \DateTime());

                $em->persist($image);
                $em->flush();

                if ($echangeEcriture)
                    $echangeEcriture
                        ->setImage($image)
                        ->setStatus(0)
                        ->setPasPiece(0);

                $em->flush();
                $imageATraiter = new ImageATraiter();
                $imageATraiter->setImage($image);
                $em->persist($imageATraiter);

                $lotGroup = $this->getDoctrine()->getRepository('AppBundle:LotGroup')
                    ->getNewLotGroup(1,$this->getUser(),$dossier);

                $lot->setLotGroup($lotGroup);
                $em->flush();
            }

            return new JsonResponse($this->getEcritureEchange($echangeEcriture));
        }

        return new Response(-1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function imageUploaderAction(Request $request)
    {
        $echangeEcriture = Boost::deboost($request->request->get('echange_ecriture'),$this);
        if(is_bool($echangeEcriture)) return new Response('security');

        $echangeEcriture = $this->getDoctrine()->getRepository('AppBundle:EchangeEcriture')->find($echangeEcriture);
        return $this->render('DrtBundle:Drt:upload-pm.html.twig',
            [
                'echangeEcriture' => $echangeEcriture
            ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ecritureAction(Request $request)
    {
        $echangeEcriture = Boost::deboost($request->request->get('echange_ecriture'),$this);
        $echangeEcriture = $this->getDoctrine()->getRepository('AppBundle:EchangeEcriture')
            ->find($echangeEcriture);
        $result = $this->getEcritureEchange($echangeEcriture);
        return new JsonResponse($result);
    }

    /**
     * @param EchangeEcriture $echangeEcriture
     * @return object
     */
    private function getEcritureEchange(EchangeEcriture $echangeEcriture)
    {
        $echangeItem = $echangeEcriture->getEchangeItem();
        $idsImages = [];
        if ($echangeEcriture->getPasPiece() == 0 && $echangeEcriture->getStatus() == 0 && $echangeEcriture->getDateCalculALettrer())
        {
            /** @var Tiers $tiers */
            $tiers = null;
            if ($echangeEcriture->getCompte() && intval(substr($echangeEcriture->getCompte(),0,2)) != 47)
            {
                $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')
                    ->getOneByCompte($echangeItem->getEchange()->getDossier(), $echangeEcriture->getCompte(), 10);
            }

            $tvaImputationControles = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                ->getTvaImputationControleByMontant($echangeEcriture->getEchangeItem()->getEchange()->getDossier(),$echangeEcriture->getDebit() - $echangeEcriture->getCredit(),$echangeItem->getEchange()->getExercice(), $tiers);
            foreach ($tvaImputationControles as $imputationControle)
            {
                /** @var TvaImputationControle $tvaImputationControle */
                $tvaImputationControle = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                    ->find($imputationControle['tvaic']);
                $idsImages[] = $tvaImputationControle->getImage()->getId();
            }
        }

        return (object)
        [
            'id' => Boost::boost($echangeEcriture->getId()),
            'client' => $echangeItem->getEchange()->getDossier()->getSite()->getClient()->getNom(),
            'dossier' => $echangeItem->getEchange()->getDossier()->getNom(),
            'date' => $echangeEcriture->getDate()->format('d/m/Y'),
            'jnl' => $echangeEcriture->getJournal() ? $echangeEcriture->getJournal() : '',
            'compte' => $echangeEcriture->getCompte() ? $echangeEcriture->getCompte() : '',
            'piece' => $echangeEcriture->getPiece(),
            'libelle' => $echangeEcriture->getLibelle() ? $echangeEcriture->getLibelle() : '',
            'page' => $echangeEcriture->getPage() ? $echangeEcriture->getPage() : '',
            'debit' => $echangeEcriture->getDebit(),
            'credit' => $echangeEcriture->getCredit(),
            'solde' => $echangeEcriture->getSolde(),
            'images' => (object)
            [
                'ids' => implode(',',$idsImages),
                'c' => count($idsImages),
                'image' => $echangeEcriture->getImage() ?
                    (object)
                    [
                        'id' => Boost::boost($echangeEcriture->getImage()->getId()),
                        'n' => $echangeEcriture->getImage()->getNom()
                    ] : null,
                'etat' => $echangeEcriture->getStatus()
            ]
        ];
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function generateXlsAction(Request $request)
    {
        $datas = json_decode(urldecode($request->request->get('datas')));
        $exercice = intval($request->request->get('exercice'));
        $dossierNom = '';
        $dossiers = [];
        foreach ($datas as $data)
        {
            $dossierNom = $data->dossier;
            if (!array_key_exists($data->dossier,$dossiers))
                $dossiers[$data->dossier] = [];
            if (!array_key_exists($data->page,$dossiers[$data->dossier]))
                $dossiers[$data->dossier][$data->page] = [];

            $date = \DateTime::createFromFormat('d/m/Y',$data->date);
            if ($date === false || array_sum($date::getLastErrors()))
                $date = null;
            $dossiers[$data->dossier][$data->page][] = (object)
            [
                'date' =>  $date,
                'jnl' => $data->jnl,
                'compte' => $data->compte,
                'piece' => $data->piece,
                'libelle' => $data->libelle,
                'credit' => floatval($data->credit),
                'debit' => floatval($data->debit),
                'solde' => floatval($data->solde)
            ];
        }

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $backgroundTitle = '808080';
        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");

        //return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$dossiers]);

        foreach ($dossiers as $dossier => $pages)
        {
            $keyPage = 0;
            foreach ($pages as $pageNom => $page)
            {
                $sheet = $phpExcelObject->setActiveSheetIndex($keyPage);
                $phpExcelObject->getActiveSheet()->setTitle($pageNom);
                foreach ($page as $keyRow => $row)
                {
                    $sheet->setCellValue('A'.($keyRow + 1),$row->date ? $row->date->format('d/m/Y') : '');
                    $sheet->setCellValue('B'.($keyRow + 1),$row->jnl);
                    $sheet->setCellValue('C'.($keyRow + 1),$row->compte);
                    $sheet->setCellValue('D'.($keyRow + 1),$row->piece);
                    $sheet->setCellValue('E'.($keyRow + 1),$row->libelle);
                    $sheet->setCellValue('G'.($keyRow + 1),$row->debit > 0 ? $row->debit : '');
                    $sheet->setCellValue('F'.($keyRow + 1),$row->credit > 0 ? $row->credit : '');
                    $sheet->setCellValue('H'.($keyRow + 1),$row->solde > 0 ? $row->solde : '');
                }
            }
        }

        $name =
            $dossierNom . ' ' . $exercice . '.xls';

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

    public function controleAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $exercice = $request->request->get('exercice');
        if(is_bool($client) || is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')
            ->find($client);
        $echangeType = $this->getDoctrine()->getRepository('AppBundle:EchangeType')
            ->find($request->request->get('echange_type'));
        $stats = $this->getDoctrine()->getRepository('AppBundle:EchangeEcriture')
            ->getStats($echangeType,$exercice,$client,$dossier);

        return new JsonResponse($stats);

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => $stats
        ]);
    }
}