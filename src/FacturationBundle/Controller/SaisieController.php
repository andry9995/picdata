<?php

namespace FacturationBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\FactAnnee;
use AppBundle\Entity\FactMoisSaisi;
use AppBundle\Entity\FactPrestationClient;
use AppBundle\Entity\FactRemiseApplique;
use AppBundle\Entity\FactSaisie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SaisieController extends Controller
{
    /**
     * Index Saisie
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('FacturationBundle:Saisie:index.html.twig');
    }

    /**
     *
     *
     * @param Request $request
     * @param $dossier
     * @param $exercice
     * @param $mois : mois de saisie
     * @param FactAnnee $annee_tarif
     * @param $type : affichage mois déjà saisi (1) ou nouveau saisi (0)
     * @return JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saisieAction(Request $request, $dossier, $exercice, $mois, FactAnnee $annee_tarif, $type)
    {
        $dossier_id = Boost::deboost($dossier, $this);
        $the_dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossier_id);
        $em = $this->getDoctrine()
            ->getManager();

        if ($the_dossier) {
            if ($request->isXmlHttpRequest()) {
                $mois_saisi = $this->getDoctrine()
                    ->getRepository('AppBundle:FactMoisSaisi')
                    ->findOneBy(array(
                        'mois' => $mois,
                        'dossier' => $the_dossier,
                        'exercice' => $exercice,
                    ));

                //Affichage mois déjà saisi
                if ($type == 1) {
                    if (!$mois_saisi) {
                        throw new NotFoundHttpException('Mois de saisie introuvable.');
                    }
                    /* On complète la liste des prestation à saisir */
                    $this->getDoctrine()
                        ->getRepository('AppBundle:FactSaisie')
                        ->completeSaisie($the_dossier, $mois_saisi, $exercice, $annee_tarif);
                    $this->getDoctrine()
                        ->getRepository('AppBundle:FactSaisie')
                        ->calculerPrix($the_dossier, $mois_saisi, $exercice, $annee_tarif);
                    $saisies = $this->getDoctrine()
                        ->getRepository('AppBundle:FactSaisie')
                        ->getAllSaisieByDossierAndMoisAndExercice($the_dossier, $mois_saisi, $exercice, $annee_tarif);

                } else {

                    //Nouvelle saisie
                    if (!$mois_saisi) {
                        /* Si le mois de saisie n'extiste pas, on le crée */
                        $mois_saisi1 = new FactMoisSaisi();
                        $mois_saisi1
                            ->setMois($mois)
                            ->setExercice($exercice)
                            ->setDossier($the_dossier);
                        $em->persist($mois_saisi1);
                        $em->flush();
                        $mois_saisi = $mois_saisi1;

                        /* On complète la liste des prestation à saisir */
                        $this->getDoctrine()
                            ->getRepository('AppBundle:FactSaisie')
                            ->completeSaisie($the_dossier, $mois_saisi1, $exercice, $annee_tarif);
                        /* On calcule les prix des prestation selon les formules */
                        $this->getDoctrine()
                            ->getRepository('AppBundle:FactSaisie')
                            ->calculerPrix($the_dossier, $mois_saisi1, $exercice, $annee_tarif);
                        $saisies = $this->getDoctrine()
                            ->getRepository('AppBundle:FactSaisie')
                            ->getAllSaisieByDossierAndMoisAndExercice($the_dossier, $mois_saisi1, $exercice, $annee_tarif);
                    } else {
                        /* On complète la liste des prestation à saisir */
                        $this->getDoctrine()
                            ->getRepository('AppBundle:FactSaisie')
                            ->completeSaisie($the_dossier, $mois_saisi, $exercice, $annee_tarif);
                        /* On calcule les prix des prestation selon les formules */
                        $this->getDoctrine()
                            ->getRepository('AppBundle:FactSaisie')
                            ->calculerPrix($the_dossier, $mois_saisi, $exercice, $annee_tarif);
                        $saisies = $this->getDoctrine()
                            ->getRepository('AppBundle:FactSaisie')
                            ->getAllSaisieByDossierAndMoisAndExercice($the_dossier, $mois_saisi, $exercice, $annee_tarif);
                    }


                }
                /* Liste des valeurs pour jqGrid */
                $rows = array();
                /** @var FactSaisie $saisie */
                foreach ($saisies as $saisie) {
                    $quantite = $saisie->getQuantite();
                    $pu_fixe_indice = $saisie->getFactTarifClient()->getPuFixeIndice();
                    $pu_variable_indice = $saisie->getFactTarifClient()->getPuVariableIndice();
                    $unite_realise = $saisie->getUniteRealise();
                    $prix = $saisie->getPrix();
                    $remise_volume = $saisie->getRemiseVolume();
                    $prix_net = $saisie->getPrixNet();
                    $honoraire = $saisie->getHonoraire();

                    $rows[] = array(
                        'id' => $saisie->getId(),
                        'cell' => array(
                            $saisie->getFactTarifClient()->getFactPrestationClient()->getFactPrestation()->getFactDomaine()->getLibelle(),
                            $saisie->getFactTarifClient()->getFactPrestationClient()->getFactPrestation()->getFactDomaine()->getCode(),
                            $saisie->getFactTarifClient()->getFactPrestationClient()->getFactPrestation()->getCode(),
                            $saisie->getFactTarifClient()->getFactPrestationClient()->getFactPrestation()->getLibelle(),
                            $saisie->getFactTarifClient()->getFactPrestationClient()->getFactPrestation()->getFactUnite()->getLibelle(),
                            round($honoraire, 4) != 0 ? round($honoraire, 4) : '',
                            $saisie->getNoCalcul(),
                            $saisie->getFactTarifClient()->getFormule(),
                            round($quantite, 4) != 0 ? round($quantite, 4) : '',
                            round($pu_fixe_indice, 4) != 0 ? round($pu_fixe_indice, 4) : '',
                            round($pu_variable_indice, 4) != 0 ? round($pu_variable_indice, 4) : '',
                            round($unite_realise, 4) != 0 ? round($unite_realise, 4) : '',
                            round($prix, 4) != 0 ? round($prix, 4) : '',
                            round($remise_volume, 4) != 0 ? round($remise_volume, 4) : '',
                            round($prix_net, 4) != 0 ? round($prix_net, 4) : '',
                            '<i class="fa fa-save icon-action js-save-button js-save-saisie" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-saisie" title="Supprimer"></i>',
                        )
                    );
                }
                $nb_ligne_client = $this->getDoctrine()
                    ->getRepository('AppBundle:FactSaisie')
                    ->getNbLigneClient($the_dossier->getSite()->getClient(), $exercice, $mois_saisi->getMois());
                $nb_ligne_dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:FactSaisie')
                    ->getNbLigneDossier($the_dossier, $exercice, $mois_saisi);

                /* @var FactRemiseApplique $remise_appliquer */
                $remise_appliquer = $this->getDoctrine()
                    ->getRepository('AppBundle:FactRemiseApplique')
                    ->getRemiseAppliqueByClient($the_dossier->getSite()->getClient());
                if ($remise_appliquer) {
                    $remise = $remise_appliquer->getFactRemiseNiveau();
                    $remise_pourcentage = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('AppBundle:FactRemiseVolume')
                        ->getPercentageByVolume($remise, $nb_ligne_client);
                } else {
                    $remise_pourcentage = 0;
                }


                $liste = array(
                    'rows' => $rows,
                    'nb_ligne_client' => $nb_ligne_client,
                    'nb_ligne_dossier' => $nb_ligne_dossier,
                    'remise_pourcentage' => $remise_pourcentage,
                );
                return new JsonResponse($liste);

            } else {
                throw new AccessDeniedException('Accès refusé.');
            }
        } else {
            throw new NotFoundHttpException('Dossier introuvable.');
        }
    }

    /**
     * Modification d'une saisie
     *
     * @param Request $request
     * @return Response
     */
    public function saisieEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()
                ->getEntityManager();
            $id = $request->request->get('id');
            $quantite = intval($request->request->get('saisie-quantite'));
            $unite_realise = intval($request->request->get('saisie-unite-realise'));
            $honoraire = floatval($request->request->get('saisie-honoraire'));
            $nocalcul = $request->request->get('saisie-nocalcul') == 1 ? true : false;

            if ($id != '' && $id != 'new_row') {
                $saisie = $this->getDoctrine()
                    ->getRepository('AppBundle:FactSaisie')
                    ->find($id);
                if ($saisie) {
                    if ($quantite != 0) {
                        $saisie->setQuantite($quantite);
                    } else {
                        $saisie->setQuantite(null);
                    }
                    if ($unite_realise != 0) {
                        $saisie->setUniteRealise($unite_realise);
                    } else {
                        $saisie->setUniteRealise(null);
                    }
                    if ($honoraire != 0) {
                        $saisie->setHonoraire($honoraire);
                    } else {
                        $saisie->setHonoraire(null);
                    }

                    $pu_fixe = $saisie->getFactTarifClient()->getPuFixeIndice() ? $saisie->getFactTarifClient()->getPuFixeIndice() : 0;
                    $pu_variable = $saisie->getFactTarifClient()->getPuVariableIndice() ? $saisie->getFactTarifClient()->getPuVariableIndice() : 0;

                    if ($nocalcul == false) {
                        if ($quantite != 0) {
                            $prix = (($pu_variable * $unite_realise) + $pu_fixe) * $quantite;
                        } else {
                            $prix = ($pu_variable * $unite_realise) + $pu_fixe;
                        }

                        $remise = 0;
                        $prix_net = $prix - $remise;
                        $saisie
                            ->setNoCalcul($nocalcul)
                            ->setPrix($prix)
                            ->setPrixNet($prix_net);
                    } else {
                        $saisie
                            ->setNoCalcul($nocalcul)
                            ->setPrix(null)
                            ->setPrixNet(null);
                    }
                }
                $em->flush();
            }
            $data = array(
                'erreur' => false,
            );
            return new Response(json_encode($data));
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Liste des mois ayant des saisies pour un dossier
     *
     * @param $dossier
     * @param $exercice
     * @return JsonResponse
     */
    public function moisSaisiDossierAction($dossier, $exercice)
    {
        $dossier_id = Boost::deboost($dossier, $this);
        $the_dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossier_id);
        $mois = [];
        if ($the_dossier) {
            $mois = $this->getDoctrine()
                ->getRepository('AppBundle:FactMoisSaisi')
                ->getMoisSaisiWithSaisieDossier($the_dossier, $exercice);
        }
        $serializer = $this->get('serializer');
        $data = $serializer->serialize($mois, 'json');

        return new JsonResponse($data);
    }

    /**
     * Liste des mois ayant des saisies pour un client
     *
     * @param $client
     * @param $exercice
     * @return JsonResponse
     */
    public function moisSaisiClientAction($client, $exercice)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $mois = [];
        if ($the_client) {
            $mois = $this->getDoctrine()
                ->getRepository('AppBundle:FactMoisSaisi')
                ->getMoisSaisiWithSaisieClient($the_client, $exercice);
        }
        $serializer = $this->get('serializer');
        $data = $serializer->serialize($mois, 'json');

        return new JsonResponse($data);
    }

    /**
     * @param $client
     * @param $exercice
     * @param $mois
     * @param FactAnnee $annee_tarif
     * @return JsonResponse
     * @throws \Exception
     */
    public function controleImportHeaderAction($client, $exercice, $mois, FactAnnee $annee_tarif)
    {
        $clientId = Boost::deboost($client, $this);

        $col_names = [];
        $col_models = [];

        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($clientId);
        if ($the_client) {
            $col_names = ['Dossiers', 'Lignes importées'];
            $col_names2 = ['Total affecté', 'Non affecté', 'Contrôle'];

            $col_models = [
                [
                    'name' => 'controle-dossier', 'index' => 'controle-dossier', 'editable' => false, 'align' => 'left',
                    'sortable' => true, 'width' => 300, 'classes' => 'js-controle-dossier'
                ],
                [
                    'name' => 'controle-ligne-importer', 'index' => 'controle-ligne-importer', 'editable' => false, 'align' => 'center',
                    'sortable' => true, 'width' => 100, 'classes' => 'js-controle-importer td-import'
                ],
            ];
            $col_models2 = [
                [
                    'name' => 'controle-affecter', 'index' => 'controle-affecter', 'editable' => false, 'align' => 'center',
                    'sortable' => true, 'width' => 80, 'classes' => 'js-controle-affecter td-import'
                ],
                [
                    'name' => 'controle-non-affecter', 'index' => 'controle-non-affecter', 'editable' => false, 'align' => 'center',
                    'sortable' => true, 'width' => 100, 'classes' => 'js-controle-non-affecter td-import'
                ],
                [
                    'name' => 'controle-controle', 'index' => 'controle-controle', 'editable' => false, 'align' => 'center',
                    'sortable' => true, 'width' => 100, 'fixed' => true, 'classes' => 'js-controle-controle td-import'
                ]
            ];

            $prestations = $this->getDoctrine()
                ->getRepository('AppBundle:FactPrestationClient')
                ->getAllPrestationByClient($the_client);
            $col_prestations = [];
            $col_model_prestations = [];

            $prestation_with_criteres = [];

            /** @var FactPrestationClient $prestation */
            foreach ($prestations as $prestation) {
                $critere_ecriture = $this->getDoctrine()
                    ->getRepository('AppBundle:FactCritereEcriture')
                    ->getByPrestationClient($prestation);
                if (count($critere_ecriture) > 0) {
                    $prestation_with_criteres[] = $prestation;

                    $code = $prestation->getFactPrestation()->getCode();
                    $col_prestations[] = $code;
                    $col_model_prestations[] = [
                        'name' => 'controle-' . $code, 'index' => 'controle-' . $code, 'editable' => false, 'align' => 'center',
                        'sortable' => true, 'width' => 60, 'fixed' => true, 'cellattr' => 'col_prestation_attr',
                        'classes' => 'js-controle-' . $code . ' td-import td-import-prestation'
                    ];
                }
            }

            $col_names = array_merge($col_names, $col_prestations, $col_names2);
            $col_models = array_merge($col_models, $col_model_prestations, $col_models2);
        }


        $data = [
            'col_names' => $col_names,
            'col_models' => $col_models,
        ];
        return new JsonResponse($data);
    }

    /**
     * @param $dossier
     * @param $exercice
     * @param $mois
     * @param FactAnnee $annee_tarif
     * @return JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function controleImportDossierAction($dossier, $exercice, $mois, FactAnnee $annee_tarif)
    {
        $dossierId = Boost::deboost($dossier, $this);
        $the_dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);
        if ($the_dossier) {
            $ligne = $this->getDoctrine()
                ->getRepository('AppBundle:FactSaisie')
                ->getLignesImporter($the_dossier, $mois, $exercice, $annee_tarif);
            return new JsonResponse($ligne);
        }
        throw new NotFoundHttpException("Dossier introuvable.");
    }

    public function controleListeParPrestation(Dossier $dossier, $exercice, $mois, FactAnnee $annee_tarif, $code_prestation)
    {

    }
}
