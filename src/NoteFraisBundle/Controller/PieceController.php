<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 08/03/2018
 * Time: 09:36
 */

namespace NoteFraisBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PieceController extends Controller
{
    public function envoiPieceAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find(11388);

            if ($dossier == null) {
                return new Response(-1);
            }

            $post = $request->request;
            $exercice = $post->get('dataId');

            $files = $request->files->get('ndf_envoi');
            $source_image = null;

            $em = $this->getDoctrine()->getEntityManager();

            if (count($files) > 0) {
                $lot = $this->getDoctrine()
                    ->getRepository('AppBundle:Lot')
                    ->getNewLot($dossier, $this->getUser(), '');

                $lot->setStatus(4);
                $em->flush();

            }

            $dateScan = $lot->getDateScan()->format("Ymd");

//        directory
            $directory = "IMAGES/" . $dateScan;
            $fs = new Filesystem();
            try {
                $fs->mkdir($directory, 0777);
            } catch (IOExceptionInterface $e) {
            }


            $nbFiles = 0;
            if ($files != null) {

                foreach ($files as $file) {
                    $file_name = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $name = basename($file_name, '.' . $extension);
                    $file->move($directory, $file_name);
                    $newName = Boost::getUuid();
                    $fs->rename($directory . '/' . $file_name, $directory . '/' . $newName . '.' . $extension);

                    $image = new Image();

                    $image->setLot($lot);
                    $image->setExercice($exercice);
                    $image->setExtImage($extension);
                    $image->setNbpage(1);
                    $image->setNomTemp($newName);
                    $image->setOriginale($name);
                    $image->setSourceImage($this->getDoctrine()->getRepository('AppBundle:SourceImage')->getBySource('PICDATA'));
                    $image->setDownload(new \DateTime('now'));


                    $em->persist($image);

                    $em->flush();

                    $nbFiles++;
                }

                return new JsonResponse($nbFiles);
            }

            return new JsonResponse(-1);

        }

        throw new AccessDeniedHttpException("Accès refusé");
    }

}