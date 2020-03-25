<?php

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Image;
use AppBundle\Entity\ReleveDetail;
use AppBundle\Entity\Separation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Filesystem\Filesystem;

class PmController extends Controller
{

    public function pmEnvoiAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $releveId = Boost::deboost($post->get('releve'), $this);

            $releve = $this->getDoctrine()
                ->getRepository('AppBundle:Releve')
                ->find($releveId);

            //dossier
            $dossier = $post->get('dossier');
            $dossier = Boost::deboost($dossier, $this);
            if (is_bool($dossier)) return new Response('security');

            //directory
            $directory = "IMAGES";
            $fs = new Filesystem();
            try {
                $fs->mkdir($directory, 0777);
            } catch (IOExceptionInterface $e) {
            }

            $post = $request->request;

            //dossier
            $dossier = $post->get('dossier');
            $dossier = Boost::deboost($dossier, $this);
            if (is_bool($dossier)) return new Response('security');
            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->createQueryBuilder('d')
                ->where('d.id = :id')
                ->setParameter('id', $dossier)
                ->getQuery()
                ->getOneOrNullResult();

            //exercice
            $exercice = $post->get('exercice');

            //creation dossier client id
            $directory .= '/' . $dossier->getSite()->getClient()->getId();
            try {
                $fs->mkdir($directory, 0777);
            } catch (IOExceptionInterface $e) {
            }

            //creation dossier dateScan
            $dateNow = new \DateTime();
            $directory .= '/' . $dateNow->format('Ymd');
            try {
                $fs->mkdir($directory, 0777);
            } catch (IOExceptionInterface $e) {
            }


            $file = $request->files->get('envoi_pm');

            $source_image = null;

            $lot = $lot_urgent = null;
            if (count($file) > 0) {
                $lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $this->getUser(), '');
            }

            $source = $this->getDoctrine()->getRepository('AppBundle:SourceImage')->getBySource('PICDATA');

            if ($file != null) {

                $file_name = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $name = basename($file_name, '.' . $extension);
                $file->move($directory, $file_name);
                $newName = $dateNow->format('Ymd') . '_' . Boost::getUuid(50);
                $fs->rename($directory . '/' . $file_name, $directory . '/' . $newName . '.' . $extension);

                $image = new Image();

                $image->setLot($lot);
                $image->setExercice($exercice);
                $image->setExtImage($extension);
                $image->setNbpage(1);
                $image->setNomTemp($newName);
                $image->setOriginale($name);
                $image->setSourceImage($source);

//                $em = $this->getDoctrine()->getEntityManager();
//                $em->persist($image);
//                $em->flush();


                if(!is_null($releve)) {
//                    $releve->setImageTemp($image);
//                    $em->persist($releve);
//                    $em->flush();
                }


                $im = $this->getDoctrine()
                    ->getRepository('AppBundle:Image')
                    ->find(3990633);


                return new JsonResponse(array(
                    'idTemp' => Boost::boost($im->getId()),
                    'nomTemp' => $im->getNom(),
                    'fileCount' => count($file)
                ));
            } else {
                return new JsonResponse(array(
                    'idTemp' => '',
                    'nomTemp' => '',
                    'filecount' => -1
                ));
            }
        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }

    }
    /**
     * @param Request $request
     * @return Response
     */
    public function pmShowAction(Request $request)
    {
        $releveId = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releveId)) return new Response('security');

        $releve = $this->getDoctrine()
            ->getRepository('AppBundle:Releve')
            ->find($releveId);


        $releveDetails = $this->getDoctrine()
            ->getRepository('AppBundle:ReleveDetail')
            ->findBy(array('releve' => $releve));

        return $this->render('BanqueBundle:Pm:parcourir.html.twig',array(
            'releve'=> $releve,
            'releveDetails' => $releveDetails
        ));
    }
}
