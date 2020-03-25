<?php

namespace FacturationBundle\Controller;

use AppBundle\Entity\FactContrat;
use AppBundle\Entity\FactContratFichier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ContratController extends Controller
{
    /**
     * Index Contrat Signé Client
     *
     * @return Response
     */
    public function indexAction()
    {
        $contrats = $this->getDoctrine()
            ->getRepository('AppBundle:FactContrat')
            ->getAllContrat();

        return $this->render('@Facturation/Contrat/index.html.twig', array(
            'contrats' => $contrats,
        ));
    }

    /**
     * Liste contrats signés pour jqGrid
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function contratAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $contrats = $this->getDoctrine()
                ->getRepository('AppBundle:FactContrat')
                ->getAllContrat();

            $rows = array();
            /** @var FactContrat $contrat */
            foreach ($contrats as $contrat) {
                $rows[] = array(
                    'id' => $contrat->getId(),
                    'cell' => array(
                        $contrat->getClient()->getNom(),
                        $contrat->getDateSignature()->format('Y-m-d'),
                        count($contrat->getFactContratFichiers()) . '<i class="fa fa-file-pdf-o file-pdf pull-right"></i>',
                        $contrat->getAutoriserModif(),
                        '<i class="fa fa-edit icon-action js-edit-contrat" title="Modifier"></i><i class="fa fa-trash icon-action js-delete-contrat" title="Supprimer"></i>',
                    )
                );
            }
            $liste = array(
                'rows' => $rows,
            );
            return new JsonResponse($liste);

        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Ajouter un contrat signé
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function contratAddAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            /* Créer le repertoire CONTRATS  dans WEB s'il n'existe pas */
            $fs = new Filesystem();
            if (!$fs->exists("CONTRATS")) {
                $fs->mkdir("CONTRATS");
            }

            $id = $request->request->get('id');
            /* Liste des fichiers pdf */
            $fichiers = $request->files->get('pdf_contrat');
            $allow_tarif_edit = $request->request->get('allow_tarif_edit') == 1 ? true : false;
            $client_id = $request->request->get('client');
            $date_signature = \DateTime::createFromFormat('d-m-Y', $request->request->get('date_signature'));

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
            if ($client) {
                $file_directory = "CONTRATS/" . $client->getId();
                if (!$fs->exists($file_directory)) {
                    $fs->mkdir($file_directory);
                }

                $em = $this->getDoctrine()
                    ->getManager();
                if ($id != '') {
                    $fact_contrat = $this->getDoctrine()
                        ->getRepository('AppBundle:FactContrat')
                        ->find($id);
                } else {
                    $fact_contrat = new FactContrat();
                }
                $fact_contrat
                    ->setClient($client)
                    ->setAutoriserModif($allow_tarif_edit)
                    ->setDateSignature($date_signature);
                $em->persist($fact_contrat);

                /* On enregistre chaque fichier */
                if ($fichiers && is_array($fichiers)) {
                    /* @var UploadedFile $fichier */
                    foreach ($fichiers as $fichier) {
                        $contrat_fichier = new FactContratFichier();

                        $now = new \DateTime();
                        $extension = $fichier->getClientOriginalExtension();
                        $filename_without_extension = basename($fichier->getClientOriginalName(), '.' . $extension);
                        $filename = $filename_without_extension . '_' . $now->format('Y-m-d_H-i-s') . '.' . $extension;
                        $fichier->move($file_directory, $filename);

                        $contrat_fichier
                            ->setFichier($filename)
                            ->setFactContrat($fact_contrat);
                        $em->persist($contrat_fichier);
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

    /**
     * Détail d'un contrat: affichage liste pdf, etc ...
     *
     * @param Request $request
     * @param FactContrat $contrat
     * @return JsonResponse
     */
    public function contratDetailAction(Request $request, FactContrat $contrat)
    {
        if ($request->isXmlHttpRequest()) {
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

            $date_signature = null;

            if ($contrat->getDateSignature()) {
                $date_signature = $contrat->getDateSignature()->format('d-m-Y');
            }
            $client_id = $contrat->getClient()->getId();
            $autoriser_modif = $contrat->getAutoriserModif();
            $fichiers = $contrat->getFactContratFichiers();
            $pdfs = [];
            /** @var FactContratFichier $fichier */
            foreach ($fichiers as $fichier) {
                $pdfs[] = array(
                    'id' => $fichier->getId(),
                    'filename' => $fichier->getFichier(),
                    'filepath' => $baseurl . '/CONTRATS/' . $client_id . '/' . $fichier->getFichier(),
                );
            }

            $data = array(
                'erreur' => false,
                'data' => array(
                    'client_id' => $client_id,
                    'date_signature' => $date_signature,
                    'autoriser_modif' => $autoriser_modif,
                    'fichiers' => $pdfs,
                )
            );
            return new JsonResponse(json_encode($data));
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    public function contratEditAction(Request $request)
    {
        return new Response('ok');
    }

    /**
     * Supprimer un contrat signé
     *
     * @param Request $request
     * @return JsonResponse|AccessDeniedException
     */
    public function contratRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $contrat = $this->getDoctrine()
                ->getRepository('AppBundle:FactContrat')
                ->find($id);
            if ($contrat) {
                $em = $this->getDoctrine()
                    ->getManager();
                $em->remove($contrat);
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

    public function clientAction($attr_id = '', $attr_class = '')
    {
        $clients = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->findBy(array(
                'status' => 1,
            ), array(
                'nom' => 'ASC',
            ));
        return $this->render('@Facturation/Contrat/client.html.twig', array(
            'clients' => $clients,
            'attr_id' => $attr_id,
            'attr_class' => $attr_class
        ));
    }

    /**
     * Supprimer un PDF d'un contrat signé
     *
     * @param Request $request
     * @return JsonResponse|AccessDeniedException
     */
    public function contratFichierDeleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $fichier = $this->getDoctrine()
                ->getRepository('AppBundle:FactContratFichier')
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
}
