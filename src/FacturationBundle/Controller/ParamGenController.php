<?php

namespace FacturationBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\FactAnnee;
use AppBundle\Entity\FactDomaine;
use AppBundle\Entity\FactIndice;
use AppBundle\Entity\FactModele;
use AppBundle\Entity\FactRemiseApplique;
use AppBundle\Entity\FactRemiseNiveau;
use AppBundle\Entity\FactRemiseVolume;
use AppBundle\Entity\FactUnite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class ParamGenController extends Controller
{
    /**
     * Index Paramètres généraux facturation
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('FacturationBundle:ParamGen:index.html.twig');
    }

    /**
     * Domaines des prestations
     *
     * @param Request $request
     * @param $json : 1=liste pour jqGrid, 0=select pour HTML
     * @return JsonResponse|Response
     */
    public function domaineAction(Request $request, $json)
    {
        if ($request->isXmlHttpRequest()) {
            $domaines = $this->getDoctrine()
                ->getRepository('AppBundle:FactDomaine')
                ->getAllDomaine();
            if ($json == 1) {
                /* Retourner liste pour jqGrid */
                $rows = array();
                /** @var FactDomaine $domaine */
                foreach ($domaines as $domaine) {
                    $rows[] = array(
                        'id' => $domaine->getId(),
                        'cell' => array(
                            $domaine->getCode(),
                            $domaine->getLibelle(),
                            '<i class="fa fa-save icon-action js-save-button js-save-domaine" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-domaine" title="Supprimer"></i>',
                        )
                    );
                }
                $liste = array(
                    'rows' => $rows,
                );
                return new JsonResponse($liste);
            } else {
                /* Retourner HTML select */
                $options = '<select>';
                /** @var FactDomaine $domaine */
                foreach ($domaines as $domaine) {
                    $options .= '<option value="' . $domaine->getId() . '">' . $domaine->getLibelle() . '</option>';
                }
                $options .= '</select>';
                return new Response($options);
            }
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Ajout nouveau domaine ou modif
     *
     * @param Request $request
     * @return Response
     */
    public function domaineEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');
            $domaine_code = $request->request->get('domaine-code');
            $domaine_nom = $request->request->get('domaine-nom');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Modification
                    if ($id != 'new_row') {
                        $domaine = $this->getDoctrine()
                            ->getRepository('AppBundle:FactDomaine')
                            ->find($id);
                        if ($domaine) {
                            $domaine->setLibelle($domaine_nom);
                            $domaine->setCode($domaine_code);
                            $em->flush();
                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    } else {
                        //Ajout nouveau domaine
                        $domaine = new FactDomaine();
                        $domaine
                            ->setLibelle($domaine_nom)
                            ->setCode($domaine_code);
                        $em->persist($domaine);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    }
                } catch (\Exception $ex) {
                    if (strpos($ex->getMessage(), "libelle_UNIQUE")) {
                        return new Response("Le domaine '$domaine_nom' existe déjà.", 500);
                    } elseif (strpos($ex->getMessage(), "code_UNIQUE")) {
                        return new Response("Le code '$domaine_code' existe déjà.", 500);
                    }
                }

            }
            throw new NotFoundHttpException("Domaine introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Supprimer un domaine
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function domaineRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Suppression
                    if ($id != 'new_row') {
                        $domaine = $this->getDoctrine()
                            ->getRepository('AppBundle:FactDomaine')
                            ->find($id);
                        if ($domaine) {
                            $em->remove($domaine);
                            $em->flush();
                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    }
                } catch (\Exception $ex) {
                    return new Response("$ex->getMessage()", 500);
                }

            }
            throw new NotFoundHttpException("Domaine introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Liste indices pour jqGrid
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function indiceAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $indices = $this->getDoctrine()
                ->getRepository('AppBundle:FactIndice')
                ->getAllIndice();
            $rows = array();
            /** @var FactIndice $indice */
            foreach ($indices as $indice) {
                $date_indice = "";
                if ($indice->getDateIndice()) {
                    $date_indice = $indice->getDateIndice()
                        ->format('Y-m-d');
                }
                $rows[] = array(
                    'id' => $indice->getId(),
                    'cell' => array(
                        $indice->getCode(),
                        $date_indice,
                        $indice->getIndexIndice(),
                        $indice->getIndice(),
                        $indice->getPourcentage(),
                        '<i class="fa fa-save icon-action js-save-button js-save-indice" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-indice" title="Supprimer"></i>',
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
     * Ajout nouvel indice ou modif
     *
     * @param Request $request
     * @return Response
     */
    public function indiceEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');
            $indice_code = $request->request->get('indice-code');
            $indice_date = trim($request->request->get('indice-date'));
            $indice_index = $request->request->get('indice-index');
            $indice_indice = $request->request->get('indice-indice');
            $indice_percent = $request->request->get('indice-percent');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Modification
                    if ($id != 'new_row') {
                        $indice = $this->getDoctrine()
                            ->getRepository('AppBundle:FactIndice')
                            ->find($id);
                        if ($indice) {
                            $indice->setCode($indice_code);
                            if ($indice_date != '') {
                                $indice->setDateIndice(\DateTime::createFromFormat('d-m-Y', $indice_date));
                            }
                            $indice->setIndexIndice(intval($indice_index));
                            if ($indice_indice != '') {
                                $indice->setIndice(floatval($indice_indice));
                            }
                            if ($indice_percent != '') {
                                $indice->setPourcentage(floatval($indice_percent));
                            }
                            $em->flush();
                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    } else {
                        //Ajout nouvel indice
                        $indice = new FactIndice();
                        $indice->setCode($indice_code);
                        if ($indice_date != '') {
                            $indice->setDateIndice(\DateTime::createFromFormat('d-m-Y', $indice_date));
                        }
                        $indice->setIndexIndice(intval($indice_index));
                        if ($indice_indice != '') {
                            $indice->setIndice(floatval($indice_indice));
                        }
                        if ($indice_percent != '') {
                            $indice->setPourcentage(floatval($indice_percent));
                        }
                        $em->persist($indice);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    }
                } catch (\Exception $ex) {
                    if (strpos($ex->getMessage(), "code_UNIQUE")) {
                        return new Response("Le code '$indice_code' existe déjà.", 500);
                    } else {
                        return new Response($ex->getMessage(), 500);
                    }
                }

            }
            throw new NotFoundHttpException("Indice introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Suprimer une indice
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function indiceRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Suppression
                    if ($id != 'new_row') {
                        $indice = $this->getDoctrine()
                            ->getRepository('AppBundle:FactIndice')
                            ->find($id);
                        if ($indice) {
                            $em->remove($indice);
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
            throw new NotFoundHttpException("Indice introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Unité de facturation
     *
     * @param Request $request
     * @param $json : 1=liste pour jqGrid, 0=select for HTML
     * @return JsonResponse|Response
     */
    public function uniteAction(Request $request, $json)
    {
        if ($request->isXmlHttpRequest()) {
            $unites = $this->getDoctrine()
                ->getRepository('AppBundle:FactUnite')
                ->getAllUnite();
            if ($json == 1) {
                /* Liste pour jqGrid */
                $rows = array();
                /** @var FactUnite $unite */
                foreach ($unites as $unite) {
                    $rows[] = array(
                        'id' => $unite->getId(),
                        'cell' => array(
                            $unite->getCode(),
                            $unite->getLibelle(),
                            '<i class="fa fa-save icon-action js-save-button js-save-unite" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-unite" title="Supprimer"></i>',
                        )
                    );
                }
                $liste = array(
                    'rows' => $rows,
                );
                return new JsonResponse($liste);
            } else {
                /* select pour HTML */
                $options = '<select>';
                /** @var FactUnite $unite */
                foreach ($unites as $unite) {
                    $options .= '<option value="' . $unite->getId() . '">' . $unite->getLibelle() . '</option>';
                }
                $options .= '</select>';
                return new Response($options);
            }
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Ajout nouvel unité ou modif
     *
     * @param Request $request
     * @return Response
     */
    public function uniteEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');
            $unite_code = $request->request->get('unite-code');
            $unite_nom = $request->request->get('unite-nom');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Modification
                    if ($id != 'new_row') {
                        $unite = $this->getDoctrine()
                            ->getRepository('AppBundle:FactUnite')
                            ->find($id);
                        if ($unite) {
                            $unite->setLibelle($unite_nom);
                            $unite->setCode($unite_code);
                            $em->flush();
                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    } else {
                        //Ajout nouvelle unité
                        $unite = new FactUnite();
                        $unite
                            ->setLibelle($unite_nom)
                            ->setCode($unite_code);
                        $em->persist($unite);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    }
                } catch (\Exception $ex) {
                    if (strpos($ex->getMessage(), "libelle_UNIQUE")) {
                        return new Response("L'unité '$unite_nom' existe déjà.", 500);
                    } elseif (strpos($ex->getMessage(), "code_UNIQUE")) {
                        return new Response("Le code '$unite_code' existe déjà.", 500);
                    }
                }

            }
            throw new NotFoundHttpException("Unité introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Supprimer une unité
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function uniteRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Suppression
                    if ($id != 'new_row') {
                        $unite = $this->getDoctrine()
                            ->getRepository('AppBundle:FactUnite')
                            ->find($id);
                        if ($unite) {
                            $em->remove($unite);
                            $em->flush();
                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    }
                } catch (\Exception $ex) {
                    return new Response("$ex->getMessage()", 500);
                }

            }
            throw new NotFoundHttpException("Unité introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Remise volume liste pour jqGrid
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function remiseVolumeAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $remises = $this->getDoctrine()
                ->getRepository('AppBundle:FactRemiseVolume')
                ->getAllRemise();
            $rows = array();

            /** @var \AppBundle\Entity\FactRemiseVolume $remise */
            foreach ($remises as $remise) {
                $rows[] = array(
                    'id' => $remise->getId(),
                    'cell' => array(
                        $remise->getCode(),
                        $remise->getFactRemiseNiveau()->getLibelle(),
                        $remise->getTranche1(),
                        $remise->getTranche2(),
                        $remise->getPourcentage(),
                        '<i class="fa fa-save icon-action js-save-button js-save-remise" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-remise" title="Supprimer"></i>',
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
     * Ajout nouvelle remise ou modif
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function remiseVolumeEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');
            $remise_code = $request->request->get('remise-code');
            $remise_tranche1 = $request->request->get('remise-tranche1');
            $remise_tranche2 = $request->request->get('remise-tranche2');
            $remise_percent = $request->request->get('remise-percent');
            $remise_niveau = $request->request->get('remise-niveau');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Modification
                    if ($id != 'new_row') {
                        $remise = $this->getDoctrine()
                            ->getRepository('AppBundle:FactRemiseVolume')
                            ->find($id);
                        if ($remise) {
                            $remise->setCode($remise_code)
                                ->setTranche1($remise_tranche1)
                                ->setTranche2($remise_tranche2)
                                ->setPourcentage($remise_percent);
                            if ($remise_niveau != '') {
                                $niveau = $this->getDoctrine()
                                    ->getRepository('AppBundle:FactRemiseNiveau')
                                    ->find($remise_niveau);
                                if ($niveau) {
                                    $remise->setFactRemiseNiveau($niveau);
                                }
                            }
                            $em->flush();
                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    } else {
                        //Ajout nouvelle Remise
                        $remise = new FactRemiseVolume();
                        $remise->setCode($remise_code)
                            ->setTranche1($remise_tranche1)
                            ->setTranche2($remise_tranche2)
                            ->setPourcentage($remise_percent);
                        if ($remise_niveau != '') {
                            $niveau = $this->getDoctrine()
                                ->getRepository('AppBundle:FactRemiseNiveau')
                                ->find($remise_niveau);
                            if ($niveau) {
                                $remise->setFactRemiseNiveau($niveau);
                            }
                        }
                        $em->persist($remise);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    }
                } catch (\Exception $ex) {
                    return new Response($ex->getMessage(), 500);
                }

            }
            throw new NotFoundHttpException("Remise introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Supprimer une remise volume
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function remiseVolumeRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Suppression
                    if ($id != 'new_row') {
                        $remise = $this->getDoctrine()
                            ->getRepository('AppBundle:FactRemiseVolume')
                            ->find($id);
                        if ($remise) {
                            $em->remove($remise);
                            $em->flush();
                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    }
                } catch (\Exception $ex) {
                    return new Response("$ex->getMessage()", 500);
                }

            }
            throw new NotFoundHttpException("Remise introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Type de Remise
     *
     * @param $json : 1=objet JSON, 0=select pour HTML
     * @param string $attr_class : class pour le select
     * @param string $attr_id : id pour le select
     * @param int $first_empty : si le premier element du select doit être vide
     * @return JsonResponse|Response
     */
    public function remiseNiveauAction($json, $attr_class = '', $attr_id = '', $first_empty = 0)
    {
        $niveaux = $this->getDoctrine()
            ->getRepository('AppBundle:FactRemiseNiveau')
            ->getAllNiveau();
        if ($json == 1) {
            $serializer = $this->get('serializer');
            $data = $serializer->serialize($niveaux, 'json');
            return new JsonResponse($data);
        } else {
            if ($attr_id != '') {
                $options = '<select id="' . $attr_id . '" class="' . $attr_class . '">';
            } else {
                $options = '<select class="' . $attr_class . '">';
            }
            if ($first_empty == 1) {
                $options .= '<option value="0">Aucune</option>';
            }
            /* @var FactRemiseNiveau $niveau */
            foreach ($niveaux as $niveau) {
                $options .= '<option value="' . $niveau->getId() . '">' . $niveau->getLibelle() . '</options>';
            }
            $options .= '</select>';
            return new Response($options);
        }
    }

    /**
     * Model des tarifs: Experts comptable, clients directs, ....
     *
     * @param Request $request
     * @param $json : 1=liste pour jqGrid, 0=liste HTML
     * @param string $attr_id, id pour HTML
     * @return JsonResponse|Response
     */
    public function modelTarifAction(Request $request, $json, $attr_id = '')
    {
        if ($json == 1) {
            if ($request->isXmlHttpRequest()) {
                $modeles = $this->getDoctrine()
                    ->getRepository('AppBundle:FactModele')
                    ->getAllModele();
                $rows = array();

                /** @var FactModele $modele */
                foreach ($modeles as $modele) {
                    $rows[] = array(
                        'id' => $modele->getId(),
                        'cell' => array(
                            $modele->getCode(),
                            $modele->getLibelle(),
                            '<i class="fa fa-save icon-action js-save-button js-save-modele" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-modele" title="Supprimer"></i>',
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
        } else {
            $modeles = $this->getDoctrine()
                ->getRepository('AppBundle:FactModele')
                ->getAllModele();
            return $this->render('@Facturation/ParamGen/modelTarif.html.twig', array(
                'modeles' => $modeles,
                'attr_id' => $attr_id,
            ));
        }
    }

    /**
     * Ajout nouveau modele ou modif
     *
     * @param Request $request
     * @return Response
     */
    public function modelTarifEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');
            $modele_code = $request->request->get('modele-code');
            $modele_nom = $request->request->get('modele-nom');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Modification
                    if ($id != 'new_row') {
                        $modele = $this->getDoctrine()
                            ->getRepository('AppBundle:FactModele')
                            ->find($id);
                        if ($modele) {
                            $modele
                                ->setCode($modele_code)
                                ->setLibelle($modele_nom);
                            $em->flush();
                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    } else {
                        //Ajout nouveau modèle
                        $modele = new FactModele();
                        $modele
                            ->setLibelle($modele_nom)
                            ->setCode($modele_code);
                        $em->persist($modele);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    }
                } catch (\Exception $ex) {
                    if (strpos($ex->getMessage(), "libelle_UNIQUE")) {
                        return new Response("L'unité '$modele_nom' existe déjà.", 500);
                    } elseif (strpos($ex->getMessage(), "code_UNIQUE")) {
                        return new Response("Le code '$modele_code' existe déjà.", 500);
                    }
                }

            }
            throw new NotFoundHttpException("Modèle de tarification introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Supprimer un modele de tarif
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function modelTarifRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id', '');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '') {
                try {
                    //Suppression
                    if ($id != 'new_row') {
                        $modele = $this->getDoctrine()
                            ->getRepository('AppBundle:FactModele')
                            ->find($id);
                        if ($modele) {
                            $em->remove($modele);
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
            throw new NotFoundHttpException("Modèle de tarification introuvable.");
        } else {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Année de tarif
     *
     * @param string $attr_id
     * @return Response
     */
    public function anneeTarifAction($attr_id = '')
    {
        $current = date('Y');
        $current_year = $current;
        for ($i = 0; $i <= 2; $i++) {
            $test_current = $this->getDoctrine()
                ->getRepository('AppBundle:FactAnnee')
                ->findOneBy(array(
                    'annee' => $current,
                ));
            if (!$test_current) {
                $em = $this->getDoctrine()
                    ->getManager();
                $annee = new FactAnnee();
                $annee->setAnnee($current);
                $em->persist($annee);
                $em->flush();
            }
            $current++;
        }
        $annees = $this->getDoctrine()
            ->getRepository('AppBundle:FactAnnee')
            ->getAllAnnee();

        return $this->render('@Facturation/ParamGen/anneeTarif.html.twig', array(
            'annees' => $annees,
            'current' => $current_year,
            'attr_id' => $attr_id,
        ));
    }

    /**
     * Remise appliquée pour un client
     *
     * @param Request $request
     * @param $client
     * @return JsonResponse
     */
    public function remiseAppliqueAction(Request $request, $client)
    {
        if ($request->isXmlHttpRequest()) {
            $client_id = Boost::deboost($client, $this);
            $the_client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
            if ($the_client) {
                /* @var FactRemiseApplique $remise */
                $remise = $this->getDoctrine()
                    ->getRepository('AppBundle:FactRemiseApplique')
                    ->getRemiseAppliqueByClient($the_client);
                if ($remise) {
                    $data = array(
                        'id' => $remise->getFactRemiseNiveau()->getId(),
                        'libelle' => $remise->getFactRemiseNiveau()->getLibelle(),
                    );
                    return new JsonResponse(json_encode($data));
                } else {
                    $data = array(
                        'id' => 0,
                        'libelle' => 'Aucune'
                    );
                    return new JsonResponse(json_encode($data));
                }
            } else {
                $data = array(
                    'id' => 0,
                    'libelle' => 'Aucune'
                );
                return new JsonResponse(json_encode($data));
            }
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Modifier le type de remise appliquée au client
     *
     * @param Request $request
     * @param $client
     * @param $remise
     * @return JsonResponse
     */
    public function remiseAppliqueEditAction(Request $request, $client, $remise)
    {
        $em = $this->getDoctrine()
            ->getEntityManager();
        if ($request->isXmlHttpRequest()) {
            $client_id = Boost::deboost($client, $this);
            $the_client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
            if ($the_client) {
                /* @var FactRemiseApplique $remise_applique */
                $remise_applique = $this->getDoctrine()
                    ->getRepository('AppBundle:FactRemiseApplique')
                    ->getRemiseAppliqueByClient($the_client);
                /* Si le client a déjà une remise, on change le type */
                if ($remise_applique) {
                    if ($remise != 0) {
                        $the_remise = $this->getDoctrine()
                            ->getRepository('AppBundle:FactRemiseNiveau')
                            ->find($remise);
                        if ($the_remise) {
                            $remise_applique->setFactRemiseNiveau($the_remise);
                        } else {
                            $em->remove($remise_applique);
                        }
                    } else {
                        $em->remove($remise_applique);
                    }
                } else {
                    /* Si le client n'a pas encore de remise, on la crée */
                    if ($remise != 0) {
                        $the_remise = $this->getDoctrine()
                            ->getRepository('AppBundle:FactRemiseNiveau')
                            ->find($remise);
                        if ($the_remise) {
                            $the_remise_applique = new FactRemiseApplique();
                            $the_remise_applique
                                ->setClient($the_client)
                                ->setFactRemiseNiveau($the_remise);
                            $em->persist($the_remise_applique);
                        }
                    }
                }
                $em->flush();
                $selected_remise = $this->getDoctrine()
                    ->getRepository('AppBundle:FactRemiseApplique')
                    ->getRemiseAppliqueByClient($the_client);
                $serializer = $this->get('serializer');

                $data = array(
                    'erreur' => false,
                    'remise' => $selected_remise,
                );
                return new JsonResponse($serializer->serialize($data, 'json'));
            } else {
                $data = array(
                    'erreur' => true,
                    'erreur_text' => 'Client introuvable.',
                );
                return new JsonResponse(json_encode($data));
            }
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }
}