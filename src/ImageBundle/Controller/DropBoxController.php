<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 02/10/2019
 * Time: 13:52
 */

namespace ImageBundle\Controller;


use AppBundle\Entity\ImageDropbox;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DropBoxController extends Controller
{
    public function indexAction()
    {
        return $this->render('@Image/ImageDropbox/index.html.twig');
    }

    public function listAction($init = 0)
    {
        $images = $this->getDoctrine()
            ->getRepository('AppBundle:ImageDropbox')
            ->parseDropbox($init, $dossierInconnu);
        return new JsonResponse($images);
    }

    public function numeroterAction(Request $request)
    {
        $this->getDoctrine()
            ->getRepository('AppBundle:ImageDropbox')
            ->numeroter();
        $data = [
            'erreur' => false,
        ];

        return new JsonResponse($data);
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
        /** @var ImageDropbox $imageDropbox */
        $imageDropbox = $this->getDoctrine()
            ->getRepository('AppBundle:ImageDropbox')
            ->find($id);
        if ($imageDropbox) {
            $em = $this->getDoctrine()
                ->getManager();
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossier_id);
            $datescan = null;
            if ($ds != '' && strlen($ds) == 10) {
                $datescan = \DateTime::createFromFormat('d-m-Y', $ds);
            }
            $imageDropbox
                ->setDossier($dossier)
                ->setExercice($exercice)
                ->setDateScan($datescan);
            $em->flush();
            $data = [
                'erreur' => false,
            ];
            return new  JsonResponse(json_encode($data));
        } else {
            throw new NotFoundHttpException("Image introuvable.");
        }
    }

}