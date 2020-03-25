<?php

namespace ImageBundle\Controller;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\ImageFtp;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageFtpController extends Controller
{
    public function indexAction()
    {
        return $this->render('@Image/ImageFtp/index.html.twig');
    }

    public function listeAction($init = 0)
    {
        $images = $this->getDoctrine()
            ->getRepository('AppBundle:ImageFtp')
            ->ParseFtp($init, $dossier_inconnu);
        return new JsonResponse($images);
    }

    public function editAction(Request $request)
    {
        $id = $request->request->get('id');
        $dossier_id = $request->request->get('img-dossier');
        $exercice = $request->request->get('img-exercice');
        $ds = $request->request->get('img-datescan');
        if ($exercice == '') {
            $exercice = null;
        }
        /** @var ImageFtp $imageFtp */
        $imageFtp = $this->getDoctrine()
            ->getRepository('AppBundle:ImageFtp')
            ->find($id);
        if ($imageFtp) {
            $em = $this->getDoctrine()
                ->getManager();
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossier_id);
            $datescan = null;
            if ($ds != '' && strlen($ds) == 10) {
                $datescan = \DateTime::createFromFormat('d-m-Y', $ds);
            }
            $imageFtp
                ->setDossier($dossier)
                ->setExercice($exercice)
                ->setDatescan($datescan);
            $em->flush();
            $data = [
                'erreur' => false,
            ];
            return new  JsonResponse(json_encode($data));
        } else {
            throw new NotFoundHttpException("Image introuvable.");
        }
    }

    public function listeDossierAction(Request $request)
    {
        $client_id = $request->query->get('client', 0);
        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $select = '<select><option></option>';
        if ($client) {
            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getDossiersClient($client);
            /** @var Dossier $dossier */
            foreach ($dossiers as $dossier) {
                $select .= '<option value="' . $dossier->getId() . '">' . $dossier->getNom() . '</option>';
            }
        }
        $select .= '</select>';

        return new Response($select);
    }

    public function numeroterAction(Request $request)
    {
        $this->getDoctrine()
            ->getRepository('AppBundle:ImageFtp')
            ->numeroter();
        $data = [
            'erreur' => false,
        ];
        return new JsonResponse($data);
    }
}
