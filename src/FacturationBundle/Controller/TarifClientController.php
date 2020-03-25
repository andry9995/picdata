<?php

namespace FacturationBundle\Controller;

use AppBundle\Entity\FactAnnee;
use AppBundle\Entity\FactIndice;
use AppBundle\Entity\FactModele;
use AppBundle\Entity\FactTarif;
use AppBundle\Entity\FactTarifClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AppBundle\Controller\Boost;

class TarifClientController extends Controller
{
    /**
     * Liste tarif client pour jqGrid
     *
     * @param Request $request
     * @param FactAnnee $annee
     * @param $client
     * @param $indice
     * @param FactModele $modele
     * @param $recalculer
     * @return JsonResponse
     */
    public function tarifClientAction(Request $request, FactAnnee $annee, $client, $indice, FactModele $modele, $recalculer)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        if ($the_client) {
            if ($request->isXmlHttpRequest()) {
                $force_recalculer = false;
                if ($recalculer == 1) {
                    $force_recalculer = true;
                }

                $can_modify = $this->getDoctrine()
                    ->getRepository('AppBundle:FactContrat')
                    ->isEditEnabled($the_client);

                $this->getDoctrine()
                    ->getRepository('AppBundle:FactTarifClient')
                    ->calculerPu($the_client, $annee, $modele, $force_recalculer);

                $tarifs = $this->getDoctrine()
                    ->getRepository('AppBundle:FactTarifClient')
                    ->getAllTarifByClientAndAnnee($the_client, $annee);

                $tarifs_n_1 = [];
                $annee_n_1 = $this->getDoctrine()
                    ->getRepository('AppBundle:FactAnnee')
                    ->findOneBy(array(
                        'annee' => $annee->getAnnee() - 1
                    ));
                if ($annee_n_1) {
                    $t_n_1 = $this->getDoctrine()
                        ->getRepository('AppBundle:FactTarifClient')
                        ->getAllTarifByClientAndAnnee($the_client, $annee_n_1);
                    /* @var FactTarifClient $item */
                    foreach ($t_n_1 as $item) {
                        $tarifs_n_1[$item->getFactPrestationClient()->getFactPrestation()->getId()] = $item;
                    }
                }

                /** @var FactIndice $the_indice */
                $the_indice = $this->getDoctrine()
                    ->getRepository('AppBundle:FactIndice')
                    ->getIndiceByAnnee($annee);

                $rows = array();

                /* Tarifs sans indice */
                if ($indice == 0) {
                    /** @var FactTarifClient $tarif */
                    foreach ($tarifs as $tarif) {
                        $pu_fixe_n_1 = "";
                        $pu_variable_n_1 = "";
                        $pu_fixe = $tarif->getPuFixe();
                        $pu_variable = $tarif->getPuVariable();

                        if (isset($tarifs_n_1[$tarif->getFactPrestationClient()->getFactPrestation()->getId()])) {
                            /* @var FactTarifClient $tarif_n_1 */
                            $tarif_n_1 = $tarifs_n_1[$tarif->getFactPrestationClient()->getFactPrestation()->getId()];
                            $pu_fixe_n_1 = $tarif_n_1->getPuFixeIndice();
                            $pu_variable_n_1 = $tarif_n_1->getPuVariableIndice();
                        }
                        $rows[] = array(
                            'id' => $tarif->getId(),
                            'cell' => array(
                                $tarif->getFactPrestationClient()->getFactPrestation()->getFactDomaine()->getLibelle(),
                                $tarif->getFactPrestationClient()->getFactPrestation()->getFactDomaine()->getCode(),
                                $tarif->getFactPrestationClient()->getFactPrestation()->getCode(),
                                $tarif->getFactPrestationClient()->getFactPrestation()->getLibelle(),
                                $tarif->getFactPrestationClient()->getFactPrestation()->getFactUnite()->getLibelle(),
                                $tarif->getShowQuantite(),
                                $tarif->getFormule(),
                                round($pu_fixe_n_1, 4) != 0 ? round($pu_fixe_n_1, 4) : '',
                                round($pu_variable_n_1, 4) != 0 ? round($pu_variable_n_1, 4) : '',
                                round($pu_fixe, 4) != 0 ? round($pu_fixe, 4) : '',
                                round($pu_variable, 4) != 0 ? round($pu_variable, 4) : '',
                                '<i class="fa fa-save icon-action js-save-button js-save-tarif-client" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-tarif-client" title="Supprimer"></i>',
                            )
                        );
                    }
                } else {
                    /* Tarifs avec indice */
                    /** @var FactTarifClient $tarif */
                    foreach ($tarifs as $tarif) {
                        $pu_fixe_indice_n_1 = "";
                        $pu_variable_indice_n_1 = "";
                        $pu_fixe_indice = $tarif->getPuFixeIndice();
                        $pu_variable_indice = $tarif->getPuVariableIndice();

                        if (isset($tarifs_n_1[$tarif->getFactPrestationClient()->getFactPrestation()->getId()])) {
                            /* @var FactTarifClient $tarif_n_1 */
                            $tarif_n_1 = $tarifs_n_1[$tarif->getFactPrestationClient()->getFactPrestation()->getId()];
                            $pu_fixe_indice_n_1 = $tarif_n_1->getPuFixeIndice();
                            $pu_variable_indice_n_1 = $tarif_n_1->getPuVariableIndice();
                        }
                        $rows[] = array(
                            'id' => $tarif->getId(),
                            'cell' => array(
                                $tarif->getFactPrestationClient()->getFactPrestation()->getFactDomaine()->getLibelle(),
                                $tarif->getFactPrestationClient()->getFactPrestation()->getFactDomaine()->getCode(),
                                $tarif->getFactPrestationClient()->getFactPrestation()->getCode(),
                                $tarif->getFactPrestationClient()->getFactPrestation()->getLibelle(),
                                $tarif->getFactPrestationClient()->getFactPrestation()->getFactUnite()->getLibelle(),
                                $tarif->getShowQuantite(),
                                $tarif->getFormule(),
                                round($pu_fixe_indice_n_1, 4) != 0 ? round($pu_fixe_indice_n_1, 4) : '',
                                round($pu_variable_indice_n_1, 4) != 0 ? round($pu_variable_indice_n_1, 4) : '',
                                round($pu_fixe_indice, 4) != 0 ? round($pu_fixe_indice, 4) : '',
                                round($pu_variable_indice, 4) != 0 ? round($pu_variable_indice, 4) : '',
                                '<i class="fa fa-save icon-action js-save-button js-save-tarif-client" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-tarif-client" title="Supprimer"></i>',
                            )
                        );
                    }
                }
                $serializer = $this->get('serializer');
                $the_indice = $serializer->serialize($the_indice, 'json');
                $liste = array(
                    'rows' => $rows,
                    'indice' => $the_indice,
                    'can_modify' => $can_modify,
                );

                return new JsonResponse($liste);
            } else {
                throw new AccessDeniedException('Accès refusé.');
            }
        } else {
            throw new NotFoundHttpException("Client introuvable.");
        }
    }

    /**
     * Modifier un tarif client
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function tarifClientEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $show_quantite = $request->request->get('t-client-quantite') == 1 ? true : false;
            $formule = $request->request->get('t-client-formule', '');
            $pu_fixe = $request->request->get('t-client-pu-fixe', '');
            $pu_variable = $request->request->get('t-client-pu-variable', '');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Modification
                    if ($id != 'new_row') {
                        $tarif = $this->getDoctrine()
                            ->getRepository('AppBundle:FactTarifClient')
                            ->find($id);
                        if ($tarif) {
                            $tarif
                                ->setShowQuantite($show_quantite)
                                ->setFormule($formule);
                            if (is_numeric($pu_fixe)) {
                                $tarif
                                    ->setPuFixe($pu_fixe)
                                    ->setPuFixeIndice($pu_fixe);
                            } else {
                                $tarif
                                    ->setPuFixe(null)
                                    ->setPuFixeIndice(null);
                            }
                            if (is_numeric($pu_variable)) {
                                $tarif
                                    ->setPuVariable($pu_variable)
                                    ->setPuVariableIndice($pu_variable);
                            } else {
                                $tarif
                                    ->setPuVariable(null)
                                    ->setPuVariableIndice(null);
                            }
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
            throw new NotFoundHttpException("Tarif introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Supprimer un tarif client
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function tarifClientRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Suppression
                    if ($id != 'new_row') {
                        $tarif = $this->getDoctrine()
                            ->getRepository('AppBundle:FactTarifClient')
                            ->find($id);
                        if ($tarif) {
                            $em->remove($tarif);
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
            throw new NotFoundHttpException("Tarif introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }
}
