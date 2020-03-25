<?php

namespace TableauImageBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Categorie;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\TbimageCategorie;
use AppBundle\Entity\TbimageDossierStatus;
use AppBundle\Entity\TbimagePeriode;
use AppBundle\Entity\TbimageZero;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use TableauImageBundle\Form\ParamSmtpType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class TableauController extends Controller
{
    /**
     * Index TbImage
     *
     * @return Response
     */
    public function indexAction()
    {
        $smtp_form = $this->createForm(ParamSmtpType::class);
        return $this->render('TableauImageBundle:Tableau:index.html.twig', array(
            'smtp_form' => $smtp_form->createView(),
        ));
    }

    /**
     * Liste images pour Tbimage
     *
     * @param $client
     * @param $site
     * @param $dossier
     * @param $exercice
     * @param $typedate
     * @return JsonResponse
     * @throws \Exception
     */
    public function listeImageAction(Request $request)
    {

        $post = $request->request;

        $client = $post->get('client');
        $site = $post->get('site');
        $dossier_list = $post->get('dossier_list');
        $exercice = $post->get('exercice');
        $typedate = $post->get('typedate');

        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $the_site = null;
        if ($site != '0') {
            $site_id = Boost::deboost($site, $this);
            $the_site = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->find($site_id);
        }
        $the_dossier = null;

        $dossiers = array();

        foreach ($dossier_list as $key => $dossier_id) {
            $dossier = Boost::deboost($dossier_id, $this);
            $the_dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossier);
            array_push($dossiers, $the_dossier);
        }

        // if ($dossier != '0') {
        //     $dossier_id = Boost::deboost($dossier, $this);
        //     $the_dossier = $this->getDoctrine()
        //         ->getRepository('AppBundle:Dossier')
        //         ->find($dossier_id);
        // }

        $date_scan_search = false;
        if($typedate !== ''){
            if((int)$typedate === 1){
                $date_scan_search = true;
            }
        }


        $liste = $this->getDoctrine()
            ->getRepository('AppBundle:Tbimage')
            ->getListe($the_client, $the_site, $dossiers, $exercice, $this->getUser(), TRUE, $date_scan_search);

        return new JsonResponse($liste);
    }

    /**
     * Param Catégorie/dossier Tbimage
     *
     * @param Request $request
     * @param $client
     * @param $site
     * @param $exercice
     * @return JsonResponse
     */
    public function categorieAction(Request $request, $client, $site, $exercice)
    {
        $em = $this->getDoctrine()->getManager();
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        if ($the_client) {
            $the_site = null;
            $site_id = Boost::deboost($site, $this);
            $the_site = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->find($site_id);
            $categories = $this->getDoctrine()
                ->getRepository('AppBundle:Categorie')
                ->getForTableauImage();
            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(), $the_client, $the_site, $exercice);

            $col_names = [];
            $col_model = [];

            $col_names[] = 'Dossier';
            $col_names[] = 'centr. cais.<br><input class="categ-check-all" data-code="centr_caisse"
                            data-categorie="0" type="checkbox" onclick="checkHeaderClick(event)">';

            $col_model[] = [
                'name' => 'tb-categorie-dossier',
                'index' => 'tb-categorie-dossier',
                'align' => 'left',
                'editable' => false,
                'sortable' => true,
                'width' => 200,
                'classes' => 'js-tb-categorie-dossier'
            ];
            $col_model[] = [
                'name' => 'tb-categorie-centr-caisse',
                'index' => 'tb-categorie-centr-caisse',
                'align' => 'center',
                'editable' => true,
                'sortable' => true,
                'width' => 70,
                'fixed' => true,
                'formatter' => 'checkbox',
                'edittype' => 'checkbox',
                'editoptions' => ['value' => '1:0'],
                'formatoptions' => ['disabled' => false],
                'classes' => 'js-tb-categorie-centr-caisse js-tb-categorie-check',
            ];
            /** @var Categorie $categorie */
            foreach ($categories as $categorie) {
                $col_names[] = strtolower($categorie->getAlias()) . '<br><input class="categ-check-all" data-code="' . strtolower($categorie->getCode()) . '"
                            data-categorie="' . $categorie->getId() . '" type="checkbox" onclick="checkHeaderClick(event)">';
                $col_model[] = [
                    'name' => $categorie->getId(),
                    'index' => $categorie->getId(),
                    'align' => 'center',
                    'editable' => true,
                    'sortable' => true,
                    'width' => 60,
                    'fixed' => true,
                    'formatter' => 'checkbox',
                    'edittype' => 'checkbox',
                    'editoptions' => ['value' => '1:0'],
                    'formatoptions' => ['disabled' => false],
                    'classes' => 'js-tb-categorie-' . $categorie->getId() . ' js-tb-categorie-check ' . strtolower($categorie->getCode()),
                ];
            }

            $rowData = [];

            /** @var Dossier $dossier */
            foreach ($dossiers as $dossier) {
                $row = [];
                $row['id'] = $dossier->getId();
                $row['tb-categorie-dossier'] = $dossier->getNom();
                $row['tb-categorie-centr-caisse'] = $dossier->getCentrCaisse();

                $no_categ = false;
                if (!$dossier->getTbimageCategorie()) {
                    $no_categ = true;
                } else {
                    if (count($dossier->getTbimageCategorie()->getCategorieList()) == 0) {
                        $no_categ = true;
                    }
                }

                if ($no_categ) {
                    $default_cats = $this->getDoctrine()
                        ->getRepository('AppBundle:Categorie')
                        ->getDefaultCategories();

                    $categs = [];
                    /** @var Categorie $default_cat */
                    foreach ($default_cats as $default_cat) {
                        $categs[] = $default_cat->getId();
                    }

                    if (!$dossier->getTbimageCategorie()) {
                        $tbimageCateg = new TbimageCategorie();
                        $tbimageCateg->setDossier($dossier);
                    } else {
                        $tbimageCateg = $dossier->getTbimageCategorie();
                    }
                    $tbimageCateg->setCategorieList($categs);
                    $dossier->setTbimageCategorie($tbimageCateg);
                    $em->persist($tbimageCateg);
                    $em->flush();
                }

                /** @var Categorie $categorie */
                foreach ($categories as $categorie) {
                    $row[$categorie->getId()] = $dossier->getTbimageCategorie() ? $dossier->getTbimageCategorie()->isCategorieActive($categorie) : false;
                }
                $rowData[] = $row;

            }

            $data = [
                'col_names' => $col_names,
                'col_model' => $col_model,
                'rowData' => $rowData,
            ];
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });

            $serializer = new Serializer(array($normalizer), array($encoder));
            return new JsonResponse($serializer->serialize($data, 'json'));
        } else {
            throw new NotFoundHttpException('Client introuvable');
        }
    }

    /**
     * MAJ Param Catégorie/dossier Tbimage
     *
     * @param Request $request
     * @param Dossier $dossier
     * @return JsonResponse
     */
    public function categorieEditAction(Request $request, Dossier $dossier)
    {
        try {
            $em = $this->getDoctrine()
                ->getManager();
            $rowdata = $request->request->get('rowdata');
            $categories = [];
            if (is_array($rowdata)) {
                foreach ($rowdata as $key => $value) {
                    if (intval($key) !== 0 && intval($value) === 1) {
                        $categories[] = intval($key);
                    }

                    if (strval($key) === 'tb-categorie-centr-caisse') {
                        $dossier->setCentrCaisse(intval($value) === 1 ? true : false);
                    }
                }
            }

            $tbimageCategorie = $this->getDoctrine()
                ->getRepository('AppBundle:TbimageCategorie')
                ->findOneBy([
                    'dossier' => $dossier
                ]);
            if ($tbimageCategorie) {
                $tbimageCategorie->setCategorieList($categories);
            } else {
                $tbimageCategorie = new TbimageCategorie();
                $tbimageCategorie
                    ->setDossier($dossier)
                    ->setCategorieList($categories);
                $em->persist($tbimageCategorie);
            }

            $em->flush();

            $data = [
                'erreur' => false,
            ];
            return new JsonResponse(json_encode($data));
        } catch (\Exception $ex) {
            $data = [
                'erreur' => true,
                'erreur_text' => "Il y a une erreur lors de l'enregistrement des catégories pour le dossier " . $dossier->getNom()
            ];
            return new JsonResponse(json_encode($data));
        }
    }

    /**
     * MAJ Param Catégorie/Tout Tbimage
     *
     * @param Request $request
     * @param $client
     * @param $site
     * @param $categorie
     * @param $exercice
     * @return Response
     */
    public function categorieEditAllAction(Request $request, $client, $site, $categorie, $exercice)
    {
        $client_id = Boost::deboost($client, $this);
        $site_id = Boost::deboost($site, $this);
        $em = $this->getDoctrine()
            ->getManager();

        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $the_site = $this->getDoctrine()
            ->getRepository('AppBundle:Site')
            ->find($site_id);
        $dossiers = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->getUserDossier($this->getUser(), $the_client, $the_site, $exercice);

        $the_categorie = $this->getDoctrine()
            ->getRepository('AppBundle:Categorie')
            ->find($categorie);

        $value = $request->request->get('value') && $request->request->get('value') == 1 ? true : false;
        if (!$the_categorie && $categorie == 0) {
            //Centralisation caisse
            /** @var Dossier $dossier */
            foreach ($dossiers as $dossier) {
                $dossier->setCentrCaisse($value);

                if ($value == true) {
                    $categorie_client = $this->getDoctrine()
                        ->getRepository('AppBundle:Categorie')
                        ->findOneBy(array(
                            'code' => 'CODE_CLIENT',
                        ));
                    $categorie_caisse = $this->getDoctrine()
                        ->getRepository('AppBundle:Categorie')
                        ->findOneBy(array(
                            'code' => 'CODE_CAISSE',
                        ));

                    /** @var TbimageCategorie $tbimageCategorie */
                    $tbimageCategorie = $dossier->getTbimageCategorie();

                    if ($categorie_caisse && $tbimageCategorie) {
                        $tbimageCategorie->toggleCategorie($categorie_caisse, false);
                    }

                    if ($categorie_client && $tbimageCategorie) {
                        $tbimageCategorie->toggleCategorie($categorie_client, false);
                    }


                }
            }
        } elseif ($the_categorie) {
            //Catégories
            /** @var Dossier $dossier */
            foreach ($dossiers as $dossier) {
                /** @var TbimageCategorie $tbimageCategorie */
                $tbimageCategorie = $dossier->getTbimageCategorie();
                if ($tbimageCategorie) {
                    $tbimageCategorie->toggleCategorie($the_categorie, $value);
                } else {
                    $tbimageCategorie = new TbimageCategorie();
                    $tbimageCategorie
                        ->setDossier($dossier)
                        ->toggleCategorie($the_categorie, $value);
                    $em->persist($tbimageCategorie);
                }

                if (($the_categorie->getCode() == 'CODE_CLIENT' || $the_categorie->getCode() == 'CODE_CAISSE')
                    && $value == true
                ) {
                    $dossier->setCentrCaisse(false);
                }
            }
        }

        $em->flush();

        Return new Response("ok");
    }

    /**
     * Param Période Tbimage
     *
     * @param $client
     * @param $site
     * @param $exercice
     * @return JsonResponse
     */
    public function periodeAction($client, $site, $exercice)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);

        if ($the_client) {
            $the_site = null;
            $site_id = Boost::deboost($site, $this);
            $the_site = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->find($site_id);
            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(), $the_client, $the_site, $exercice);

            $rows = [];
            /** @var Dossier $dossier */
            foreach ($dossiers as $dossier) {
                $demarrage = '';
                $premiere_cloture = '';
                $periode = 'M';
                $periodePiece = 'M';
                $mois_plus = 1;
                $jour = 1;

                if ($dossier->getTbimagePeriode()) {
                    /** @var TbimagePeriode $tbImagePeriode */
                    $tbImagePeriode = $dossier->getTbimagePeriode();

                    if ($tbImagePeriode->getPeriode() && trim($tbImagePeriode->getPeriode()) !== '') {
                        $periode = trim($tbImagePeriode->getPeriode());
                    }

                    if ($tbImagePeriode->getPeriodePiece() && trim($tbImagePeriode->getPeriodePiece()) !== '') {
                        $periodePiece = trim($tbImagePeriode->getPeriodePiece());
                    }

                    if ($tbImagePeriode->getMoisPlus()) {
                        $mois_plus = $tbImagePeriode->getMoisPlus();
                    }
                    if ($tbImagePeriode->getJour()) {
                        $jour = $tbImagePeriode->getJour();
                    }
                }

                if ($dossier->getDebutActivite()) {
                    $demarrage = $dossier->getDebutActivite()->format('Y-m-d');
                }
                if ($dossier->getDateCloture()) {
                    $premiere_cloture = $dossier->getDateCloture()->format('Y-m-d');
                }

                $rows[] = array(
                    'id' => $dossier->getId(),
                    'cell' => array(
                        $dossier->getNom(),
                        $demarrage,
                        $premiere_cloture,
                        $periode,
                        $periodePiece,
                        $mois_plus,
                        $jour
                    )
                );
            }

            $liste = array(
                'rows' => $rows,
            );

            return new JsonResponse($liste);
        } else {
            throw new NotFoundHttpException("Client introuvable.");
        }
    }

    /**
     * Edit Param Période/dossier Tbimage
     *
     * @param Request $request
     * @param Dossier $dossier
     * @return JsonResponse
     */
    public function periodeEditAction(Request $request, Dossier $dossier)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()
                ->getManager();
            $tbImagePeriode = $dossier->getTbimagePeriode();
            if (!$tbImagePeriode) {
                $tbImagePeriode = new TbimagePeriode();
                $tbImagePeriode->setDossier($dossier);
                $em->persist($tbImagePeriode);
            }
            $demarrage = null;
            $premiere_cloture = null;
            $periode = 'M';
            $mois_plus = 1;
            $jour = 1;

            if (trim($request->request->get('tb-periode-demarrage', '')) != '') {
                $demarrage = \DateTime::createFromFormat('d-m-Y', $request->request->get('tb-periode-demarrage'));
            }
            if (trim($request->request->get('tb-periode-cloture', '')) != '') {
                $premiere_cloture = \DateTime::createFromFormat('d-m-Y', $request->request->get('tb-periode-cloture'));
            }
            if (trim($request->request->get('tb-periode-periode', '')) != '') {
                $periode = trim($request->request->get('tb-periode-periode'));
            }
            $periode_piece = $periode;

            if (trim($request->request->get('tb-periode-piece', '')) != '') {
                $periode_piece = trim($request->request->get('tb-periode-piece'));
            }
            if (trim($request->request->get('tb-periode-mois-plus', '')) != '') {
                $mois_plus = trim($request->request->get('tb-periode-mois-plus'));
            }
            if (trim($request->request->get('tb-periode-jour', '')) != '') {
                $jour = trim($request->request->get('tb-periode-jour'));
            }
            $tbImagePeriode
                ->setDemarrage($demarrage)
                ->setPremiereCloture($premiere_cloture)
                ->setPeriode($periode)
                ->setPeriodePiece($periode_piece)
                ->setMoisPlus($mois_plus)
                ->setJour($jour);
            $em->flush();

            $data = [
                'erreur' => false,
                'dossier' => $dossier,
            ];

            return new JsonResponse(json_encode($data));
        } else {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    /**
     * Edit Param Période/all Tbimage
     *
     * @param Request $request
     * @param $client
     * @param $site
     * @param $exercice
     * @return JsonResponse
     */
    public function periodeEditAllAction(Request $request, $client, $site, $exercice)
    {
        if (trim($request->request->get('field', '')) != '') {

            $client_id = Boost::deboost($client, $this);
            $site_id = Boost::deboost($site, $this);
            $em = $this->getDoctrine()
                ->getManager();

            $the_client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
            $the_site = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->find($site_id);
            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(), $the_client, $the_site);

            foreach ($dossiers as $dossier) {
                $tbImagePeriode = $this->getDoctrine()
                    ->getRepository('AppBundle:TbimagePeriode')
                    ->findOneBy(array(
                        'dossier' => $dossier
                    ));
                if (!$tbImagePeriode) {
                    $tbImagePeriode = new TbimagePeriode();
                    $tbImagePeriode->setDossier($dossier);
                    $em->persist($tbImagePeriode);
                }

                $field = $request->request->get('field');
                $value = $request->request->get('value', '');
                if ($field == 'Demarrage' || $field == 'PremiereCloture') {
                    if ($value != '') {
                        $value = \DateTime::createFromFormat('d-m-Y', $value);
                    } else {
                        $value = null;
                    }
                }
                $tbImagePeriode->{"set$field"}($value);

            }
            $em->flush();
            $data = [
                'erreur' => false,
            ];
            return new JsonResponse(json_encode($data));
        } else {
            throw new BadRequestHttpException("Requête incorrecte.");
        }
    }

    /**
     * Détail image par mois/categorie Tbimage
     *
     * @param Request $request
     * @param Dossier $dossier
     * @param $exercice
     * @return JsonResponse
     * @throws \Exception
     */
    public function detailImageAction(Request $request, Dossier $dossier, $exercice, $typedate)
    {
        $date_scan_search = false;

        if($typedate != ''){
            if((int)$typedate === 1){
                $date_scan_search = true;
            }
        }

        $categorie_id = $request->query->get('categorie', null);
        $banque_id = $request->query->get('banque_id', 0);
        $mois = $request->query->get('mois', null);
        if ($categorie_id && $mois) {
            $periode = new \DateTime($mois);
            $categorie = $this->getDoctrine()
                ->getRepository('AppBundle:Categorie')
                ->find($categorie_id);
            $images = $this->getDoctrine()
                ->getRepository('AppBundle:Tbimage')
                ->getImageParMois($dossier, $exercice, $categorie, $periode, $banque_id, $date_scan_search);
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });

            $serializer = new Serializer(array($normalizer), array($encoder));
            $data = array(
                'periode' => $periode,
                'categorie' => $categorie,
                'exercice' => $exercice,
                'images' => $images,
            );
            return new JsonResponse($serializer->serialize($data, 'json'));
        }
        throw new NotFoundHttpException("Images introuvables");
    }

    /**
     * Liste images encours/exercice
     *
     * @param Dossier $dossier
     * @param $exercice
     * @return JsonResponse
     */
    public function imageEncoursAction(Dossier $dossier, $exercice)
    {
        $images = $this->getDoctrine()
            ->getRepository('AppBundle:Tbimage')
            ->getImageEnCours($dossier, $exercice);
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $serializer = new Serializer(array($normalizer), array($encoder));
        $data = array(
            'exercice' => $exercice,
            'images' => $images,
        );
        return new JsonResponse($serializer->serialize($data, 'json'));
    }

    /**
     * Edit ImageZero Tbimage
     *
     * @param Request $request
     * @param Dossier $dossier
     * @param $exercice
     * @param Categorie $categorie
     * @param $mois
     * @return Response
     */
    public function imageZeroEditAction(Request $request, Dossier $dossier, $exercice, Categorie $categorie, $mois)
    {
        $em = $this->getDoctrine()
            ->getManager();
        $the_mois = new \DateTime($mois);
        $status = $request->request->get('status');
        $banque_id = $request->request->get('banque');

        if ($status == 1) {
            if ($banque_id != '') {
                $banque = $this->getDoctrine()
                    ->getRepository('AppBundle:BanqueCompte')
                    ->find($banque_id);
                $tbimageZero = $this->getDoctrine()
                    ->getRepository('AppBundle:TbimageZero')
                    ->findBy(array(
                        'dossier' => $dossier,
                        'exercice' => $exercice,
                        'categorie' => $categorie,
                        'mois' => $the_mois,
                        'banqueCompte' => $banque
                    ));
            } else {
                $tbimageZero = $this->getDoctrine()
                    ->getRepository('AppBundle:TbimageZero')
                    ->findBy(array(
                        'dossier' => $dossier,
                        'exercice' => $exercice,
                        'categorie' => $categorie,
                        'mois' => $the_mois,
                    ));
            }
            /** @var TbimageZero $item */
            foreach ($tbimageZero as $item) {
                $em->remove($item);
            }
            $em->flush();
        } else {
            if ($banque_id != '') {
                $banque = $this->getDoctrine()
                    ->getRepository('AppBundle:BanqueCompte')
                    ->find($banque_id);
                $tbimageZero = new TbimageZero();
                $tbimageZero
                    ->setDossier($dossier)
                    ->setExercice($exercice)
                    ->setCategorie($categorie)
                    ->setMois($the_mois)
                    ->setBanqueCompte($banque);
            } else {
                $tbimageZero = new TbimageZero();
                $tbimageZero
                    ->setDossier($dossier)
                    ->setExercice($exercice)
                    ->setCategorie($categorie)
                    ->setMois($the_mois);
            }
            $em->persist($tbimageZero);
            $em->flush();
        }

        return new Response($status);
    }


    public function dossierStatusEditAction(Dossier $dossier, $exercice, $status)
    {
        $em = $this->getDoctrine()
            ->getManager();
        $dossier_status = $this->getDoctrine()
            ->getRepository('AppBundle:TbimageDossierStatus')
            ->findOneBy(array(
                'dossier' => $dossier,
                'exercice' => $exercice,
            ));
        if ($dossier_status) {
            if ($status == 0) {
                $em->remove($dossier_status);
                $dossier->setNonTraitable(false);
            } else {
                if ($status == 9) {
                    $dossier->setNonTraitable(true);
                } else {
                    $dossier_status->setStatus($status);
                    $dossier->setNonTraitable(false);
                }
            }
        } else {
            if ($status != 0) {
                if ($status == 9) {
                    $dossier->setNonTraitable(true);
                } else {
                    $dossier_status = new TbimageDossierStatus();
                    $dossier_status
                        ->setDossier($dossier)
                        ->setExercice($exercice)
                        ->setStatus($status);
                    $em->persist($dossier_status);
                    $dossier->setNonTraitable(false);
                }
            } else {
                $dossier->setNonTraitable(false);
            }
        }
        $em->flush();
        $data = [
            'erreur' => false,
        ];
        return new JsonResponse(json_encode($data));
    }

    public function testAction()
    {
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(6023);
        $exercice = 2016;
        $categorie = $this->getDoctrine()
            ->getRepository('AppBundle:Categorie')
            ->find(14);
        $periode = new \DateTime('2016-04-01');

        $images = $this->getDoctrine()
            ->getRepository('AppBundle:Tbimage')
            ->getImageParMois($dossier, $exercice, $categorie, $periode);

        return $this->render('@TableauImage/Tableau/test.html.twig', array(
            'images' => $images,
        ));
    }


    public function exportAction(Request $request)
    {

        $datas = json_decode(urldecode($request->request->get('exp_datas')));
        $clientid = $request->request->get('exp_client');
        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find(Boost::deboost($clientid, $this));
        $exercice = $request->request->get('exp_exercice');
        $dateNow = new \DateTime();

        $title = 'Tableau_images_'.$client->getNom().'_'.$exercice;
        $name = $title;
        $name .= '.xls';


        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $backgroundTitle = '808080';

        $phpExcelObject->getProperties()->setCreator("PicData")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");
        $sheet = $phpExcelObject->setActiveSheetIndex(0);

        /*Titre*/
        $sheet->setCellValue('A1', $title)
            ->setCellValue('A2', 'Client')
            ->setCellValue('B2', $client->getNom())
            ->setCellValue('A3', 'Exercice')
            ->setCellValue('B3', $exercice)
            ->setCellValue('A4', 'Editer le')
            ->setCellValue('B4', $dateNow->format('d-m-Y'));

        //entetes
        $sheet->setCellValue('A6', 'Dossier');
        $sheet->setCellValue('B6', 'Cloture');
        $sheet->setCellValue('C6', 'N-1');
        $sheet->setCellValue('D6', 'N');
        $sheet->setCellValue('E6', 'm1');
        $sheet->setCellValue('F6', 'm2');
        $sheet->setCellValue('G6', 'm3');
        $sheet->setCellValue('H6', 'm4');
        $sheet->setCellValue('I6', 'm5');
        $sheet->setCellValue('J6', 'm6');
        $sheet->setCellValue('K6', 'm7');
        $sheet->setCellValue('L6', 'm8');
        $sheet->setCellValue('M6', 'm9');
        $sheet->setCellValue('N6', 'm10');
        $sheet->setCellValue('O6', 'm11');
        $sheet->setCellValue('P6', 'm12');
        $sheet->setCellValue('Q6', 'm13');
        $sheet->setCellValue('R6', 'm14');
        $sheet->setCellValue('S6', 'm15');
        $sheet->setCellValue('T6', 'm16');
        $sheet->setCellValue('U6', 'm17');
        $sheet->setCellValue('V6', 'm18');
        $sheet->setCellValue('W6', 'm19');
        $sheet->setCellValue('X6', 'm20');
        $sheet->setCellValue('Y6', 'm21');
        $sheet->setCellValue('Z6', 'm22');
        $sheet->setCellValue('AA6', 'm23');
        $sheet->setCellValue('AB6', 'm24');


        $index = 7;
        foreach ($datas as $data) {

            $sheet->setCellValue('A'.$index, $data->dossier);
            $sheet->setCellValue('B'.$index, $data->cloture);
            $sheet->setCellValue('C'. $index, $data->imagen);
            $sheet->setCellValue('D'. $index, $data->imagen1);
            $sheet->setCellValue('E'. $index, $data->m1);
            $sheet->setCellValue('F'. $index, $data->m2);
            $sheet->setCellValue('G'. $index, $data->m3);
            $sheet->setCellValue('H'. $index, $data->m4);
            $sheet->setCellValue('I'. $index, $data->m5);
            $sheet->setCellValue('J'. $index, $data->m6);
            $sheet->setCellValue('K'. $index, $data->m7);
            $sheet->setCellValue('L'. $index, $data->m8);
            $sheet->setCellValue('M'. $index, $data->m9);
            $sheet->setCellValue('N'. $index, $data->m10);
            $sheet->setCellValue('O'. $index, $data->m11);
            $sheet->setCellValue('P'. $index, $data->m12);
            $sheet->setCellValue('Q'. $index, $data->m13);
            $sheet->setCellValue('R'. $index, $data->m14);
            $sheet->setCellValue('S'. $index, $data->m15);
            $sheet->setCellValue('T'. $index, $data->m16);
            $sheet->setCellValue('U'. $index, $data->m17);
            $sheet->setCellValue('V'. $index, $data->m18);
            $sheet->setCellValue('W'. $index, $data->m19);
            $sheet->setCellValue('X'. $index, $data->m20);
            $sheet->setCellValue('Y'. $index, $data->m21);
            $sheet->setCellValue('Z'. $index, $data->m22);
            $sheet->setCellValue('AA'. $index, $data->m23);
            $sheet->setCellValue('AB'. $index, $data->m24);

            $index++;
        }

        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        $phpExcelObject->setActiveSheetIndex(0);

        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $name
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;


    }
}
