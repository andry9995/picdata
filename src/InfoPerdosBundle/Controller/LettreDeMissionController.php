<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 24/08/2018
 * Time: 14:43
 */

namespace InfoPerdosBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\LettreMission;
use AppBundle\Entity\LettreMissionFichier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Filesystem\Filesystem;

class LettreDeMissionController extends Controller
{
    public function  indexAction(){
        return $this->render('@InfoPerdos/LettreDeMission/index.html.twig');
    }

    public function dossierAction(Request $request){



        $clientid = Boost::deboost($request->request->get('clientid'), $this);

        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($clientid);

        $dossiers = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->getDossiersClient($client);

        return $this->render('InfoPerdosBundle:LettreDeMission:dossier.html.twig',
            array('dossiers' => $dossiers)
        );



    }

    public function listeAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            $clientId = $request->request->get('clientid');

            $clientId = Boost::deboost($clientId, $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($clientId);

            if (null !== $client) {
                $ldms = $this->getDoctrine()
                    ->getRepository('AppBundle:LettreMission')
                    ->getLettreMissionByClient($client);

                $rows = [];

                /** @var LettreMission $ldm */
                foreach ($ldms as $ldm) {

                    $fichiers = $this->getDoctrine()
                        ->getRepository('AppBundle:LettreMissionFichier')
                        ->getFichierByLettreMission($ldm);

                    $rows[] = array(
                        'id' => $ldm->getId(),
                        'cell' => array(
                            $ldm->getDossier()->getNom(),
                            $ldm->getDateLettre()->format('Y-m-d'),
                            count($fichiers) . '<i class="fa fa-file-pdf-o file-pdf pull-right"></i>',
                            '<i class="fa fa-trash icon-action js-delete-ldm" title="Supprimer"></i>',
                        )
                    );
                }
                $liste = array(
                    'rows' => $rows,
                );
                return new JsonResponse($liste);
            }
            return new JsonResponse($client);
        }

        throw new AccessDeniedException('Accès refusé');

    }

    public function addAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            /* Créer le repertoire CONTRATS  dans WEB s'il n'existe pas */
            $fs = new Filesystem();
            if (!$fs->exists("LDM")) {
                $fs->mkdir("LDM");
            }

            $id = $request->request->get('id');
            /* Liste des fichiers pdf */
            $fichiers = $request->files->get('pdf_ldm');
            $dossier_id = $request->request->get('dossierid');
            $date_ldm = \DateTime::createFromFormat('d-m-Y', $request->request->get('date_ldm'));

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossier_id);
            if ($dossier !== null) {
                $file_directory = "CONTRATS/" . $dossier->getId();
                if (!$fs->exists($file_directory)) {
                    $fs->mkdir($file_directory);
                }

                $em = $this->getDoctrine()->getManager();

                if ($id != '') {
                    $ldm = $this->getDoctrine()
                        ->getRepository('AppBundle:LettreMission')
                        ->find($id);
                } else {
                    $ldm = new LettreMission();
                }
                $ldm
                    ->setDossier($dossier)
                    ->setDateLettre($date_ldm);

                $em->persist($ldm);

                /* On enregistre chaque fichier */
                if ($fichiers && is_array($fichiers)) {
                    /* @var UploadedFile $fichier */
                    foreach ($fichiers as $fichier) {
                        $ldm_fichier = new LettreMissionFichier();

                        $now = new \DateTime();
                        $extension = $fichier->getClientOriginalExtension();
                        $filename_without_extension = basename($fichier->getClientOriginalName(), '.' . $extension);
                        $filename = $filename_without_extension . '_' . $now->format('Y-m-d_H-i-s') . '.' . $extension;
                        $fichier->move($file_directory, $filename);

                        $ldm_fichier
                            ->setFichier($filename)
                            ->setLettreMission($ldm);
                        $em->persist($ldm_fichier);
                    }
                }
                $em->flush();
            }
            $data = array(
                'erreur' => false,
            );
            return new JsonResponse(json_encode($data));
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }

    }

    public function detailAction(Request $request, LettreMission $ldm){
        if ($request->isXmlHttpRequest()) {
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

            $date_ldm = null;

            if ($ldm->getDateLettre()) {
                $date_ldm = $ldm->getDateLettre()->format('d-m-Y');
            }
            $dossier_id = $ldm->getDossier()->getId();

            $fichiers = $this->getDoctrine()
                ->getRepository('AppBundle:LettreMissionFichier')
                ->getFichierByLettreMission($ldm);

            $pdfs = [];
            /** @var LettreMissionFichier $fichier */
            foreach ($fichiers as $fichier) {
                $pdfs[] = array(
                    'id' => $fichier->getId(),
                    'filename' => $fichier->getFichier(),
                    'filepath' => $baseurl . '/CONTRATS/' . $dossier_id . '/' . $fichier->getFichier(),
                );
            }

            $data = array(
                'erreur' => false,
                'data' => array(
                    'dossier_id' => $dossier_id,
                    'date_ldm' => $date_ldm,
                    'fichiers' => $pdfs,
                )
            );
            return new JsonResponse(json_encode($data));
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    public function ldmFichierDeleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $fichier = $this->getDoctrine()
                ->getRepository('AppBundle:LettreMissionFichier')
                ->find($id);
            if ($fichier) {
                $em = $this->getDoctrine()
                    ->getManager();
                $em->remove($fichier);
                $em->flush();
            }
            $data = array(
                'erreur' => false
            );

            return new JsonResponse(json_encode($data));
        } else {
            return new AccessDeniedException('Accès refusé.');
        }
    }

    public function ldmDeleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $ldm = $this->getDoctrine()
                ->getRepository('AppBundle:LettreMission')
                ->find($id);
            if ($ldm) {
                $em = $this->getDoctrine()
                    ->getManager();
                $em->remove($ldm);
                $em->flush();
            }
            $data = array(
                'erreur' => false
            );

            return new JsonResponse(json_encode($data));
        } else {
            return new AccessDeniedException('Accès refusé.');
        }
    }
}