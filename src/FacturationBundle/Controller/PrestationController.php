<?php

namespace FacturationBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\FactPrestation;
use AppBundle\Entity\FactPrestationClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PrestationController extends Controller
{
    /**
     * Index prestation
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('FacturationBundle:Prestation:index.html.twig');
    }

    /**
     * Liste des prestations générales pour jqGrid
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function prestationGeneralAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $prestations = $this->getDoctrine()
                ->getRepository('AppBundle:FactPrestation')
                ->getAllPrestation();
            $rows = array();

            /** @var FactPrestation $prestation */
            foreach ($prestations as $prestation) {
                $rows[] = array(
                    'id' => $prestation->getId(),
                    'cell' => array(
                        $prestation->getFactDomaine()->getLibelle(),
                        $prestation->getFactDomaine()->getCode(),
                        $prestation->getCode(),
                        $prestation->getLibelle(),
                        $prestation->getFactUnite()->getLibelle(),
                        $prestation->getIndice(),
                        $prestation->getRemise(),
                        '<i class="fa fa-save icon-action js-save-button js-save-prest-gen" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-prest-gen" title="Supprimer"></i>',
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
     * Ajout nouvelle prestation générale ou modif
     *
     * @param Request $request
     * @return Response
     */
    public function prestationGeneralEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $domaine_id = $request->request->get('p-gen-domaine');
            $unite_id = $request->request->get('p-gen-unite');
            $code = $request->request->get('p-gen-code');
            $libelle = $request->request->get('p-gen-prestation');
            $indice = $request->request->get('p-gen-indice') == 1 ? true : false;
            $remise = $request->request->get('p-gen-remise') == 1 ? true : false;

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Modification
                    if ($id != 'new_row') {
                        $prestation = $this->getDoctrine()
                            ->getRepository('AppBundle:FactPrestation')
                            ->find($id);
                        if ($prestation) {
                            $prestation->setCode($code)
                                ->setLibelle($libelle)
                                ->setIndice($indice)
                                ->setRemise($remise);
                            if ($domaine_id != '') {
                                $domaine = $this->getDoctrine()
                                    ->getRepository('AppBundle:FactDomaine')
                                    ->find($domaine_id);
                                if ($domaine) {
                                    $prestation->setFactDomaine($domaine);
                                }
                            }
                            if ($unite_id != '') {
                                $unite = $this->getDoctrine()
                                    ->getRepository('AppBundle:FactUnite')
                                    ->find($unite_id);
                                if ($unite) {
                                    $prestation->setFactUnite($unite);
                                }
                            }
                            $em->flush();

                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    } else {
                        //Ajout nouvelle Prestation
                        $prestation = new FactPrestation();
                        $prestation->setCode($code)
                            ->setLibelle($libelle)
                            ->setIndice($indice)
                            ->setRemise($remise);
                        if ($domaine_id != '') {
                            $domaine = $this->getDoctrine()
                                ->getRepository('AppBundle:FactDomaine')
                                ->find($domaine_id);
                            if ($domaine) {
                                $prestation->setFactDomaine($domaine);
                            }
                        }
                        if ($unite_id != '') {
                            $unite = $this->getDoctrine()
                                ->getRepository('AppBundle:FactUnite')
                                ->find($unite_id);
                            if ($unite) {
                                $prestation->setFactUnite($unite);
                            }
                        }
                        $em->persist($prestation);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    }
                } catch (\Exception $ex) {
                    if (strpos($ex->getMessage(), "UNIQUE_code_libelle")) {
                        return new Response("La prestation '$code $libelle' existe déjà.", 500);
                    } else {
                        return new Response($ex->getMessage(), 500);
                    }
                }

            }
            throw new NotFoundHttpException("Prestation introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Supprimer une prestation générale
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function prestationGeneralRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Suppression
                    if ($id != 'new_row') {
                        $prestation = $this->getDoctrine()
                            ->getRepository('AppBundle:FactPrestation')
                            ->find($id);
                        if ($prestation) {
                            $em->remove($prestation);
                            $em->flush();
                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    }
                } catch (\Exception $ex) {
                    return new Response($ex->getMessage(), 500);
                }

            }
            throw new NotFoundHttpException("Prestation introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Prestation client, liste pour jqGrid
     *
     * @param Request $request
     * @param $client
     * @return JsonResponse
     */
    public function prestationClientAction(Request $request, $client, $jqgrid)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);

        if ($the_client) {
            /* Completer la prestation client à partir de la presation générale */
            $this->getDoctrine()
                ->getRepository('AppBundle:FactPrestation')
                ->completePrestationClient($the_client);

            $prestations = $this->getDoctrine()
                ->getRepository('AppBundle:FactPrestationClient')
                ->getAllPrestationByClient($the_client);

            if ($jqgrid == 1) {
                $rows = array();

                /** @var FactPrestationClient $prestation */
                foreach ($prestations as $prestation) {
                    $rows[] = array(
                        'id' => $prestation->getId(),
                        'cell' => array(
                            $prestation->getFactPrestation()->getFactDomaine()->getLibelle(),
                            $prestation->getFactPrestation()->getFactDomaine()->getCode(),
                            '0', //Type 0 = Prestation Client
                            $prestation->getStatus(),
                            $prestation->getFactPrestation()->getCode(),
                            $prestation->getFactPrestation()->getLibelle(),
                            $prestation->getFactPrestation()->getFactUnite()->getLibelle(),
                            $prestation->getIndice(),
                            $prestation->getRemise(),
                            '<i class="fa fa-save icon-action js-save-button js-save-prest-client" title="Enregistrer"></i>',
                        )
                    );
                }
                $liste = array(
                    'rows' => $rows,
                );
                return new JsonResponse($liste);
            } else {
                $liste = array();
                /** @var FactPrestationClient $prestation */
                foreach ($prestations as $prestation) {
                    $liste[] = [
                        'id' => $prestation->getId(),
                        'prestation' => $prestation->getFactPrestation()->getLibelle(),
                        'code' => $prestation->getFactPrestation()->getCode(),
                    ];
                }
                return new JsonResponse($liste);
            }
        } else {
            throw new NotFoundHttpException("Client introuvable.");
        }
    }

    /**
     * Modif prestation client.
     * Pas d'ajout car on copie la liste générale
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function prestationClientEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $indice = $request->request->get('p-client-indice') == 1 ? true : false;
            $remise = $request->request->get('p-client-remise') == 1 ? true : false;
            $status = $request->request->get('p-client-status') == 1 ? true : false;

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Modification
                    if ($id != 'new_row') {
                        $prestation = $this->getDoctrine()
                            ->getRepository('AppBundle:FactPrestationClient')
                            ->find($id);
                        if ($prestation) {
                            $prestation->setIndice($indice)
                                ->setRemise($remise)
                                ->setStatus($status);

                            $em->flush();

                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    }
                } catch (\Exception $ex) {
                    return new Response($ex->getMessage(), 500);
                }

            }
            throw new NotFoundHttpException("Prestation introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Liste prestation dossier pour jqGrid
     *
     * @param Request $request
     * @param $dossier
     * @return JsonResponse
     */
    public function prestationDossierAction(Request $request, $dossier)
    {
        $dossier_id = Boost::deboost($dossier, $this);
        $the_dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossier_id);

        if ($request->isXmlHttpRequest()) {
            if ($the_dossier) {
                $this->getDoctrine()
                    ->getRepository('AppBundle:FactPrestation')
                    ->completePrestationDossier($the_dossier);

                $prestations = $this->getDoctrine()
                    ->getRepository('AppBundle:FactPrestationDossier')
                    ->getAllPrestationByDossier($the_dossier);
                $rows = array();

                /** @var FactPrestationClient $prestation */
                foreach ($prestations as $prestation) {
                    $rows[] = array(
                        'id' => $prestation->getId(),
                        'cell' => array(
                            $prestation->getFactPrestation()->getFactDomaine()->getLibelle(),
                            $prestation->getFactPrestation()->getFactDomaine()->getCode(),
                            '1', //Type 1 = Prestation Dossier
                            $prestation->getStatus(),
                            $prestation->getFactPrestation()->getCode(),
                            $prestation->getFactPrestation()->getLibelle(),
                            $prestation->getFactPrestation()->getFactUnite()->getLibelle(),
                            $prestation->getIndice(),
                            $prestation->getRemise(),
                            '<i class="fa fa-save icon-action js-save-button js-save-prest-client" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-prest-client" title="Supprimer"></i>',
                        )
                    );
                }
                $liste = array(
                    'rows' => $rows,
                );
                return new JsonResponse($liste);
            } else {
                throw new NotFoundHttpException("Dossier introuvable.");
            }
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Modif prestation dossier
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function prestationDossierEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $indice = $request->request->get('p-client-indice') == 1 ? true : false;
            $remise = $request->request->get('p-client-remise') == 1 ? true : false;
            $status = $request->request->get('p-client-status') == 1 ? true : false;

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Modification
                    if ($id != 'new_row') {
                        $prestation = $this->getDoctrine()
                            ->getRepository('AppBundle:FactPrestationDossier')
                            ->find($id);
                        if ($prestation) {
                            $prestation->setIndice($indice)
                                ->setRemise($remise)
                                ->setStatus($status);

                            $em->flush();

                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    }
                } catch (\Exception $ex) {
                    return new Response($ex->getMessage(), 500);
                }

            }
            throw new NotFoundHttpException("Prestation introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }
}
