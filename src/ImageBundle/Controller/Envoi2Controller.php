<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 31/08/2016
 * Time: 14:35
 */

namespace ImageBundle\Controller;
use AppBundle\Controller\Boost;
use AppBundle\Entity\CodeAnalytique;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageATraiter;
use AppBundle\Entity\ModeReglement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Envoi2Controller extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function indexAction(Request $request)
    {
        /*$chemin = $_SERVER['DOCUMENT_ROOT'] . '/IMAGES/ACZ0003VO.pdf';
        $nbPage = -1;
        if ( false !== ( $file = file_get_contents( $chemin ) ) ) {
            $nbPage = preg_match_all( "/\/Page\W/", $file, $matches );
        }
        return new Response($nbPage);*/


        if($request->isXmlHttpRequest())
        {
            $idDemo = -1;
            //directory
            $directory = "IMAGES";
            $fs = new Filesystem();
            try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }

            $post = $request->request;

            //dossier
            $dossier = $post->get('js_dossier_upl');
            $dossier = Boost::deboost($dossier,$this);
            if(is_bool($dossier)) return new Response('security');
            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->createQueryBuilder('d')
                ->where('d.id = :id')
                ->setParameter('id', $dossier)
                ->getQuery()
                ->getOneOrNullResult();

            //$dossier = new Dossier();
            $maxInLot = ($dossier->getSite()->getClient()->getId() == $idDemo) ? 1000 : 100;

            //exercice
            $exercice = $post->get('js_exercice_upl');

            //creation dossier client id
            //$dossier = new Dossier();

            //enlever repertoire client 12/01/2018
            /*$directory .= '/'.$dossier->getSite()->getClient()->getId();
            try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }*/

            //creation dossier dateScan
            $dateNow = new \DateTime();
            $directory .= '/'.$dateNow->format('Ymd');
            try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }

            //urgent
            $urgents = json_decode($post->get('js_urgents_upl'));
            $status_urgent = intval($post->get('js_urgent_stat_upl'));
            $message_urgent = $post->get('js_comment_urgent_upl');

            //code analytiques
            $analytiques = json_decode($post->get('js_analytiques_upl'));
            //mode de paiaments
            $mps = json_decode($post->get('js_mps_upl'));

            $files = $request->files->get('file');
            $source_image = null;

            $lot = $lot_urgent = null;
            $lots = [];
            if(count($files) > 0)
            {
                if($status_urgent == 3 || $status_urgent == 1) {
                    $lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $this->getUser(), '');
                    $lots[] = $lot;
                }
                if ($status_urgent == 3 || $status_urgent == 2) {
                    $lot_urgent = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $this->getUser(), trim($message_urgent));
                    $lots[] = $lot_urgent;
                }
            }

            $filesOrders = [];
            $filesUrOrders = [];
            $index = 0;
            foreach ($files as $file)
            {
                $fileCompleted = new \stdClass();
                //file
                $fileCompleted->file = $file;

                //code analytique
                $idAnalytique = array_key_exists($index,$analytiques) ? $analytiques[$index] : 0;
                $codeAnalytique = $this->getDoctrine()->getRepository('AppBundle:CodeAnalytique')
                    ->createQueryBuilder('ca')
                    ->where('ca.id = :id')
                    ->setParameter('id',$idAnalytique)
                    ->getQuery()
                    ->getOneOrNullResult();
                $fileCompleted->codeA = $codeAnalytique;

                //mp
                $mp = Boost::deboost($mps[$index],$this);
                $mp = $this->getDoctrine()->getRepository('AppBundle:ModeReglement')->find($mp);
                $fileCompleted->mp = $mp;

                //urgent
                $urgent = (intval($urgents[$index]) == 1);
                $fileCompleted->urgent = $urgent;

                if($urgent) $filesUrOrders[] = $fileCompleted;
                else $filesOrders[] = $fileCompleted;

                $index++;
            }

            foreach ($filesUrOrders as $filesUrOrder) $filesOrders[] = $filesUrOrder;

            $em = $this->getDoctrine()->getManager();
            $index = 0;
            $count = $countUrgent = 0;
            $user = $this->getUser();
            $source = $this->getDoctrine()->getRepository('AppBundle:SourceImage')->getBySource('PICDATA');
            foreach ($filesOrders as $filesOrder)
            {
                $file = $filesOrder->file;
                $file_name = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $name = basename($file_name,'.'.$extension);
                $file->move($directory, $file_name);
                $newName = $dateNow->format('Ymd').'_'.Boost::getUuid(50);
                $fs->rename($directory.'/'.$file_name, $directory.'/'.$newName.'.'.$extension);

                $image = new Image();
                //$lot_select = (intval($urgents[$index]) == 1) ? $lot_urgent : $lot;
                if($filesOrder->urgent)
                {
                    if($countUrgent == $maxInLot)
                    {
                        $lot_urgent = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $user, trim($message_urgent));
                        $lots[] = $lot_urgent;
                        $countUrgent = 1;
                    }
                    else $countUrgent++;
                    $lot_select = $lot_urgent;
                }
                else
                {
                    if($count == $maxInLot)
                    {
                        $lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $user, '');
                        $lots[] = $lot;
                        $count = 1;
                    }
                    else $count++;
                    $lot_select = $lot;
                }

                $codeAnalytique = $filesOrder->codeA;
                $image->setCodeAnalytique($codeAnalytique);
                $mp = $filesOrder->mp;
                $image->setModeReglement($mp);
                $image->setLot($lot_select);
                $image->setExercice($exercice);
                $image->setExtImage($extension);
                $image->setNbpage(2);
                $image->setNomTemp($newName);
                $image->setOriginale($name);
                $image->setSourceImage($source);
                if ($dossier->getSite()->getClient()->getId() == $idDemo) $image->setDownload(new \DateTime());

                /*if ($mp != null || $codeAnalytique != null && strtoupper(trim($extension)) == 'PDF')
                    $this->signer($directory.'/'.$newName.'.'.$extension,$codeAnalytique,$mp);*/

                $chemin = $_SERVER['DOCUMENT_ROOT'] . '/IMAGES/' .$lot_select->getDateScan()->format('Ymd') . '/' . $newName . '.' . $image->getExtImage();

                if (file_exists($chemin))
                {
                    $nbPage = 2;
                    if (strtoupper($extension) == 'PDF')
                    {
                        if ( false !== ( $ff = file_get_contents( $chemin ) ) ) {
                            $nbPage = preg_match_all( "/\/Page\W/", $ff, $matches );
                        }

                        $image->setNbpage($nbPage);
                    }

                    $em->persist($image);
                }
                else new Response("xxxxxxxxx");
                $em->flush();

                $imageATraiter = new ImageATraiter();
                $imageATraiter->setImage($image);
                $em->persist($imageATraiter);
                $em->flush();
                $index++;
            }

            $lotGroup = $this->getDoctrine()->getRepository('AppBundle:LotGroup')->getNewLotGroup(1,$this->getUser(),$dossier);
            foreach ($lots as &$l)
            {
                $l->setLotGroup($lotGroup);
            }
            $em->flush();
            return new JsonResponse(array('filecount' => count($files)));
        }

        $modePaiements = $this->getDoctrine()->getRepository('AppBundle:ModeReglement')->getListe();
        return $this->render('ImageBundle:Envoi2:index.html.twig',array('modePaiements'=>$modePaiements));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function sendAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $idDemo = -1;
            //directory
            $directory = "IMAGES";
            $fs = new Filesystem();
            try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }

            $dossier = Boost::deboost($request->request->get('js_dossier_upl'),$this);
            if(is_bool($dossier)) return new Response(-1);
            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

            $maxInLot = ($dossier->getSite()->getClient()->getId() == $idDemo) ? 1000 : 100;

            $exercice = intval($request->request->get('js_exercice_upl'));

            //creation dossier dateScan
            $dateNow = new \DateTime();
            $directory .= '/'.$dateNow->format('Ymd');
            try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }

            //urgent
            $urgents = json_decode($request->request->get('js_urgents_upl'));
            $status_urgent = intval($request->request->get('js_urgent_stat_upl'));
            $message_urgent = $request->request->get('js_comment_urgent_upl');

            $lotCummuler = intval($request->request->get('js_lot_journalier')) == 1;

            //code analytiques
            $analytiques = json_decode($request->request->get('js_analytiques_upl'));
            //commentaires
            $commentaires = json_decode($request->request->get('js_commentraires_upl'));
            //mode de paiaments
            $mps = json_decode($request->request->get('js_mps_upl'));

            $files = $request->files->get('js_id_input_image');
            $source_image = null;

            $lot = $lot_urgent = null;
            $lots = [];
            if(count($files) > 0)
            {
                if($status_urgent == 3 || $status_urgent == 1)
                {
                    $lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $this->getUser(), '',null,null,$lotCummuler);
                    $lots[] = $lot;
                }
                if ($status_urgent == 3 || $status_urgent == 2)
                {
                    $lot_urgent = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $this->getUser(), trim($message_urgent),null,null,$lotCummuler);
                    $lots[] = $lot_urgent;
                }
            }

            $filesOrders = [];
            $filesUrOrders = [];
            $index = 0;
            foreach ($files as $file)
            {
                $fileCompleted = new \stdClass();
                //file
                $fileCompleted->file = $file;

                //code analytique
                $idAnalytique = array_key_exists($index,$analytiques) ? $analytiques[$index] : 0;
                $codeAnalytique = $this->getDoctrine()->getRepository('AppBundle:CodeAnalytique')
                    ->createQueryBuilder('ca')
                    ->where('ca.id = :id')
                    ->setParameter('id',$idAnalytique)
                    ->getQuery()
                    ->getOneOrNullResult();
                $fileCompleted->codeA = $codeAnalytique;

                //commentaire
                $commentaireDossier = array_key_exists($index,$commentaires) ? $commentaires[$index] : 0;
                $commentaireDossier = $this->getDoctrine()->getRepository('AppBundle:CommentaireDossier')
                    ->find($commentaireDossier);
                $fileCompleted->commentaire = $commentaireDossier;

                //mp
                $mp = Boost::deboost($mps[$index],$this);
                $mp = $this->getDoctrine()->getRepository('AppBundle:ModeReglement')->find($mp);
                $fileCompleted->mp = $mp;

                //urgent
                $urgent = (intval($urgents[$index]) == 1);
                $fileCompleted->urgent = $urgent;

                if($urgent) $filesUrOrders[] = $fileCompleted;
                else $filesOrders[] = $fileCompleted;

                $index++;
            }

            foreach ($filesUrOrders as $filesUrOrder) $filesOrders[] = $filesUrOrder;

            $em = $this->getDoctrine()->getManager();
            $index = 0;
            $count = $countUrgent = 0;
            $user = $this->getUser();
            $source = $this->getDoctrine()->getRepository('AppBundle:SourceImage')->getBySource('PICDATA');
            foreach ($filesOrders as $filesOrder)
            {
                $file = $filesOrder->file;
                $file_name = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $extension = strtolower($ext);
                $name = basename($file_name,'.'.$ext);
                $file->move($directory, $file_name);
                $newName = $dateNow->format('Ymd').'_'.Boost::getUuid(50);
                $fs->rename($directory.'/'.$file_name, $directory.'/'.$newName.'.'.$extension);

                $image = new Image();
                //$lot_select = (intval($urgents[$index]) == 1) ? $lot_urgent : $lot;
                if($filesOrder->urgent)
                {
                    if($countUrgent == $maxInLot)
                    {
                        $lot_urgent = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $user, trim($message_urgent));
                        $lots[] = $lot_urgent;
                        $countUrgent = 1;
                    }
                    else $countUrgent++;
                    $lot_select = $lot_urgent;
                }
                else
                {
                    if($count == $maxInLot)
                    {
                        $lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $user, '');
                        $lots[] = $lot;
                        $count = 1;
                    }
                    else $count++;
                    $lot_select = $lot;
                }

                $nbPage = 1;
                $chemin = $_SERVER['DOCUMENT_ROOT'] . '/IMAGES/' .$lot_select->getDateScan()->format('Ymd') . '/' . $newName . '.' . $extension;
                if (strtoupper($extension) == 'PDF')
                {
                    $nbPage = intval(exec("pdfinfo /var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES/".$lot_select->getDateScan()->format('Ymd')."/".$newName.".".$extension." | awk '/Pages/ {print $2}'"));
                    if ($nbPage == 0)
                        $nbPage = 1;
                    /*$stream = fopen($chemin, "r");
                    $content = fread ($stream, filesize($chemin));

                    $nbPage = 1;
                    if(!(!$stream || !$content))
                    {
                        $regex = "/\/Count\s+(\d+)/";
                        if(preg_match_all($regex, $content, $matches))
                            $nbPage = max(max($matches));
                        //if ( false !== ( $ff = file_get_contents( $chemin ) ) ) {
                        //    $nbPage = preg_match_all( "/\/Page\W/", $ff, $matches );
                        //}
                    }*/
                    //$image->setNbpage($nbPage);
                }

                $image
                    ->setModeReglement($filesOrder->mp)
                    ->setLot($lot_select)
                    ->setExercice($exercice)
                    ->setExtImage($extension)
                    ->setNbpage($nbPage)
                    ->setNomTemp($newName)
                    ->setOriginale($name)
                    ->setSourceImage($source)
                    ->setCodeAnalytique($filesOrder->codeA)
                    ->setCommentaireDossier($filesOrder->commentaire);

                if ($dossier->getSite()->getClient()->getId() == $idDemo) $image->setDownload(new \DateTime());

                //if ($mp != null || $codeAnalytique != null && strtoupper(trim($extension)) == 'PDF')
                //    $this->signer($directory.'/'.$newName.'.'.$extension,$codeAnalytique,$mp);

                $em->persist($image);
                $em->flush();

                $imageATraiter = new ImageATraiter();
                $imageATraiter->setImage($image);
                $em->persist($imageATraiter);
                $em->flush();
                $index++;
            }

            $lotGroup = $this->getDoctrine()->getRepository('AppBundle:LotGroup')->getNewLotGroup(1,$this->getUser(),$dossier);
            foreach ($lots as &$l)
            {
                $l->setLotGroup($lotGroup);
            }
            $em->flush();

            return new Response(count($files));
        }

        return new Response(-1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function dossiersAction(Request $request)
    {
        return $this->render('ImageBundle:Envoi2:filtre-dossier.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function senderAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id',$dossier)
            ->getQuery()
            ->getOneOrNullResult();

        $is_urgent = $post->get('is_urgent');
        return $this->render('ImageBundle:Envoi2:sender.html.twig',array('is_urgent'=>$is_urgent, 'dossier'=>$dossier));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function sendTestAction(Request $request)
    {
        $post = $request->request;
        /*$dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id',$dossier)
            ->getQuery()
            ->getOneOrNullResult();*/

        //$exercice = $post->get('exercice');

        //$message_urgent = $post->get('message_urgent');

        $urgents = json_decode($post->get('js_urgents_upl'));

        $analytiques = json_decode($post->get('analytiques'));
        $response = '';

        for($i = 0;$i<count($analytiques);$i++)
        {
            $code_analytique = $this->getDoctrine()->getRepository('AppBundle:CodeAnalytique')
                ->createQueryBuilder('ca')
                ->where('ca.id = :id')
                ->setParameter('id',$analytiques[$i])
                ->getQuery()
                ->getOneOrNullResult();

            $response .= (($code_analytique == null) ? 'null' : $code_analytique->getLibelle()) . '<br>';
        }

        return new Response($response);
    }

    /**
     * @param $source
     * @param $codeAnalytique
     * @param $mp
     * @return Response
     */
    public function signer($source,$codeAnalytique,$mp)
    {
        $pdf = new PdfClass();
        $pdf->setSourceFile($source);
        $template_item = $pdf->ImportPage(1);
        $size = $pdf->getTemplatesize($template_item);
        if($size['w'] > $size['h'])
        {
            $pdf->AddPage('L', array($size['w'], $size['h']));
            $centreY = $size['w'] / 3;
            $centreX = $size['h'] / 3.5;
        }
        else
        {
            $pdf->AddPage('P', array($size['w'], $size['h']));
            $centreY = $size['h'] / 3.5;
            $centreX = $size['w'] / 3;
        }

        $pdf->useTemplate($template_item);
        $pdf->SetFont('Arial','B',50);
        $pdf->SetTextColor(169,169,169);
        $pdf->SetXY($centreX,$centreY);
        $pdf->Rotate(40);
        $pdf->SetAlpha(0.5);

        $signature = ($codeAnalytique != null) ? $codeAnalytique->getCode() : (($mp->getCode() != null) ? $mp->getCode() : '');
        $pdf->Cell(30, 30, $signature, 0, 1,'C',false);

        if ($codeAnalytique != null && $mp != null)
        {
            $signature = $mp->getCode();
            $pdf->SetXY($centreX,$centreY + 30);
            $pdf->Cell(30, 30, $signature, 0, 2,'C',false);
        }

        $pdf->Output($source, 'F');
        //return new Response(1);
    }
}