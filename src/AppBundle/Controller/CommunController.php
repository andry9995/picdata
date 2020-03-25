<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Site;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurDossier;
use Firebase\JWT\JWT;
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

class CommunController extends Controller
{
    /**
     * @param int $conteneur "0 si retourner HTML, 1 si JSON"
     * @param int $tous
     * @param string $attr_id custom id
     * @param string $attr_data custom data
     * @param int $label_col nombre col label
     * @param int $select_col nombre col select
     * @return JsonResponse|Response
     */
    public function clientsAction($conteneur = 0, $tous = 0, $attr_id = 'client', $attr_data = '', $label_col = 2, $select_col = 10)
    {
        $user = $this->getUser();
        $clients = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->getUserClients($user);
        if ($conteneur == 0) {
            return $this->render('AppBundle:Commun:clients.html.twig', array(
                'clients' => $clients,
                'conteneur' => $conteneur,
                'tous' => $tous,
                'attr_id' => $attr_id,
                'attr_data' => $attr_data,
                'label_col' => $label_col,
                'select_col' => $select_col
            ));
        }
        if ($conteneur == 1) {
            return new JsonResponse(json_encode($clients));
        }

        return new Response('');
    }

    /**
     * @param $conteneur
     * @param $tous
     * @param string $attr_id
     * @param string $attr_data
     * @param int $label_col
     * @param int $select_col
     * @return JsonResponse|Response
     */
    public function clientsNonCrypterAction($conteneur = 0, $tous = 0, $attr_id = 'client', $attr_data = '', $label_col = 2, $select_col = 10)
    {
        $user = $this->getUser();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_CLIENT')) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Client');
            $query = $repository->createQueryBuilder('c')->where("c.nom <> ''");

            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
                $query = $query->andWhere('c = :client')->setParameter('client', $user->getClient());

            $query = $query->andWhere('c.status = 1')->orderBy('c.nom', 'ASC')->getQuery();

            if ($conteneur == 0) {
                $clients = $query->getResult();
                return $this->render('AppBundle:Commun:clients-non-crypter.html.twig', array(
                    'clients' => $clients,
                    'conteneur' => $conteneur,
                    'tous' => $tous,
                    'attr_id' => $attr_id,
                    'attr_data' => $attr_data,
                    'label_col' => $label_col,
                    'select_col' => $select_col
                ));
            }
            if ($conteneur == 1) {
                $clients = $query->getArrayResult();
                return new JsonResponse($clients);
            }
        }

        return new Response('');
    }

    /**
     * @param int $label_col
     * @param int $select_col
     * @return Response
     */
    public function clientsMultiAction($label_col = 4, $select_col = 8)
    {
        if ($this->isGranted('ROLE_SCRIPTURA_ADMIN')) {
            $clients = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->getAllClientActif();
            return $this->render('@App/Commun/clients-multi.html.twig', array(
                'clients' => $clients,
                'label_col' => $label_col,
                'select_col' => $select_col,
            ));
        } else {
            return new Response('');
        }
    }

    /**
     * @param $conteneur "0 si retourner HTML, 1 si JSON"
     * @param $client "id du client"
     * @param $tous "1 si ajouter 'Tous' dans la liste"
     * @param string $attr_id
     * @param string $attr_data
     * @param int $label_col
     * @param int $select_col
     * @return JsonResponse|Response
     */
    public function sitesAction($conteneur = 0, $client, $tous = 1, $attr_id = 'site', $attr_data = '', $label_col = 2, $select_col = 10, $infoperdos)
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);

        $sites = $this->getDoctrine()
            ->getRepository('AppBundle:Site')
            ->getUserSites($user, $the_client);

        $siteDefaut = false;
        //Mijery ny 'SITE' tsy misy dossier
        if ($infoperdos == 1) {
            /** @var Site $site */
            foreach ($sites as $site) {
                if (strtolower($site->getNom()) === "site") {
                    $dossiers = $this->getDoctrine()
                        ->getRepository('AppBundle:Dossier')
                        ->findBy(array('site' => $site), null, 1);

                    if (count($dossiers) === 0) {
                        $siteDefaut = true;
                    }
                }
            }
        }

        if ($conteneur == 0)
            return $this->render('AppBundle:Commun:sites.html.twig', array(
                'sites' => $sites,
                'tous' => $tous,
                'attr_id' => $attr_id,
                'attr_data' => $attr_data,
                'label_col' => $label_col,
                'select_col' => $select_col,
                'site_defaut' => $siteDefaut
            ));
        else {
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });

            $serializer = new Serializer(array($normalizer), array($encoder));
            return new JsonResponse($serializer->serialize($sites, 'json'));
        }
    }

    /**
     * Get all sites of a client
     *
     * @param $client
     * @param $crypter
     * @return JsonResponse
     */
    public function sitesClientAction($client, $crypter = 1)
    {
        if ($crypter == 1) {
            $client_id = Boost::deboost($client, $this);
        } else {
            $client_id = $client;
        }
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        if ($the_client) {
            $sites = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->getAllSitesByClient($the_client);
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });

            $serializer = new Serializer(array($normalizer), array($encoder));
            return new JsonResponse($serializer->serialize($sites, 'json'));
        } else {
            throw new NotFoundHttpException("Client introuvable.");
        }
    }

    /**
     * @param Request $request
     * @param int $conteneur
     * @param $site
     * @param int $tous
     * @param $client
     * @param int $infoperdos
     * @param int $tdi
     * @param string $attr_id
     * @param string $attr_data
     * @return JsonResponse|Response
     */
    public function dossiersAction(Request $request, $conteneur = 0, $site, $tous = 1, $client, $infoperdos = 0, $tdi = 0, $attr_id = 'dossier', $attr_data = '')
    {
        $client_id = Boost::deboost($client, $this);
        $site_id = Boost::deboost($site, $this);

        if (is_bool($client_id) || is_bool($site_id)) {
            throw new AccessDeniedHttpException('security');
        }

        $exercice = $request->request->get('exercice', null);
        if (!$exercice) {
            $exercice = $request->query->get('exercice', null);
        }

        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $the_site = $this->getDoctrine()
            ->getRepository('AppBundle:Site')
            ->find($site_id);

        if (intval($exercice) == 0) $exercice = null;

        if ($infoperdos == 0) {
            $dossierTemps = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(), $the_client, $the_site, $exercice);
        } else {
            $dossierTemps = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(), $the_client, $the_site, $exercice, true);
        }

        $dossiers = [];

        if ((int)$tdi === 1) {
            //Jerena ny exercice-na envoi d'images voalohany
            /** @var Dossier[] $dossierTmps */
            foreach ($dossierTemps as $dossierTmp) {

                $firstSend = $this->getDoctrine()
                    ->getRepository('AppBundle:Image')
                    ->getFirstSend($dossierTmp);

                if (count($firstSend) > 0) {
                    if ($firstSend['exercice'] <= intval($exercice)) {
                        $dossiers[] = $dossierTmp;
                    }
                }

            }
        } else {
            $dossiers = $dossierTemps;
        }

        if ($conteneur == 0)
            return $this->render('AppBundle:Commun:dossiers.html.twig', array(
                'dossiers' => $dossiers,
                'tous' => $tous,
                'attr_id' => $attr_id,
                'attr_data' => $attr_data,
                'infoperdos' => $infoperdos
            ));
        if ($conteneur == 1) {
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });

            $serializer = new Serializer(array($normalizer), array($encoder));

            $data = [];
            /** @var Dossier $dossier */
            foreach ($dossiers as $dossier) {
                $data[] = [
                    'id' => $dossier->getId(),
                    'idCrypter' => $dossier->getIdCrypter(),
                    'nom' => $dossier->getNom(),
                    'indicateurGroup' => $dossier->getIndicateurGroup(),
                    'cloture' => $dossier->getCloture(),
                    'site' => $dossier->getSite()->getNom(),
                    'site_id' => $dossier->getSite()->getId(),
                    'status' => $dossier->getStatus(),
                    'statusDebut' => $dossier->getStatusDebut(),
                    'client' => $dossier->getSite()->getClient()->getNom(),
                    'client_id' => $dossier->getSite()->getClient()->getId(),
                ];
            }

            $json = $serializer->serialize($data, 'json');
            return new JsonResponse($json);
        }
    }

    public function dossiersTmpAction($conteneur = 0, $site, $tous = 1, $client, $attr_id = 'dossier', $attr_data = '')
    {
        $client_id = Boost::deboost($client, $this);
        $site_id = Boost::deboost($site, $this);
        if (is_bool($client_id) || is_bool($site_id)) {
            throw new AccessDeniedHttpException('security');
        }

        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $the_site = $this->getDoctrine()
            ->getRepository('AppBundle:Site')
            ->find($site_id);

        $dossiers = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->getUserDossierTmp($this->getUser(), $the_client, $the_site);

        if ($conteneur == 0)
            return $this->render('AppBundle:Commun:dossiers.html.twig', array(
                'dossiers' => $dossiers,
                'tous' => $tous,
                'attr_id' => $attr_id,
                'attr_data' => $attr_data,
            ));
        if ($conteneur == 1) {
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });

            $serializer = new Serializer(array($normalizer), array($encoder));
            $data = [];
            /** @var Dossier $dossier */
            foreach ($dossiers as $dossier) {
                $dossierUsers = $this->getDoctrine()
                    ->getRepository('AppBundle:UtilisateurDossier')
                    ->getDossierUsers($dossier);
                $users = [];
                $sbs = [];

                /** @var BanqueCompte[] $banqueComptes */
                $banqueComptes = $this->getDoctrine()
                    ->getRepository('AppBundle:BanqueCompte')
                    ->getBanqueCompteByDossier($dossier);

                if (count($dossierUsers) > 0) {
                    /** @var UtilisateurDossier $dossierUser */
                    foreach ($dossierUsers as $dossierUser) {
                        $users[] = [
                            'user_id' => Boost::boost($dossierUser->getUtilisateur()->getId()),
                            'user' => $dossierUser->getUtilisateur()->getNomComplet(),
                            'email' => $dossierUser->getUtilisateur()->getEmail(),
                            'actif' => $dossierUser->getUtilisateur()->getSupprimer() ? 0 : 1,
                            'last_login' => $dossierUser->getUtilisateur()->getLastLogin() ? $dossierUser->getUtilisateur()->getLastLogin()->format('d/m/Y') : '',
                        ];
                    }
                }

                if(count($banqueComptes) > 0){
                    foreach ($banqueComptes as $banqueCompte){
                        if($banqueCompte->getSourceImage() !== null){
                            if($banqueCompte->getSourceImage()->getId() === 3){
                                $sbs[] = [
                                    'banque' => $banqueCompte->getBanque()->getNom(),
                                    'numcompte' =>$banqueCompte->getNumcompte()
                                ];
                            }
                        }
                    }
                }
                $data[] = [
                    'id' => $dossier->getId(),
                    'idCrypter' => $dossier->getIdCrypter(),
                    'nom' => $dossier->getNom(),
                    'indicateurGroup' => $dossier->getIndicateurGroup(),
                    'cloture' => $dossier->getCloture(),
                    'site' => $dossier->getSite()->getNom(),
                    'site_id' => $dossier->getSite()->getId(),
                    'status' => $dossier->getStatus(),
                    'statusDebut' => $dossier->getStatusDebut(),
                    'client' => $dossier->getSite()->getClient()->getNom(),
                    'client_id' => $dossier->getSite()->getClient()->getId(),
                    'active' => $dossier->getActive(),
                    'dateStopSaisie' => $dossier->getDateStopSaisie() ? $dossier->getDateStopSaisie()->format('d/m/Y') : null,
                    'users' => $users,
                    'sbs' =>$sbs
                ];
            }

            $json = $serializer->serialize($data, 'json');
            return new JsonResponse($json);
        }
        throw new BadRequestHttpException("Erreur requête HTTP");
    }

    /**
     * @param $client
     * @param $json
     * @param $tous
     * @param $crypter
     * @param int $label_col
     * @param int $select_col
     * @return JsonResponse|Response
     */
    public function dossiersClientAction($client, $json = 0, $tous = 1, $crypter = 1, $label_col = 4, $select_col = 8)
    {
        if ($crypter == 1) {
            $client_id = Boost::deboost($client, $this);
            $the_client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
        } else {
            $the_client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client);
        }
        if ($the_client) {
            $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getDossiersClient($the_client);
            if ($json == 0) {
                return $this->render('@App/Commun/dossiers-client.html.twig', array(
                    'dossiers' => $dossiers,
                    'label_col' => $label_col,
                    'select_col' => $select_col,
                    'tous' => $tous,
                    'crypter' => $crypter,
                ));
            } else {
                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();

                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });

                $serializer = new Serializer(array($normalizer), array($encoder));

                $data = [];
                /** @var Dossier $dossier */
                foreach ($dossiers as $dossier) {
                    $data[] = [
                        'id' => $dossier->getId(),
                        'idCrypter' => $dossier->getIdCrypter(),
                        'nom' => $dossier->getNom(),
                        'indicateurGroup' => $dossier->getIndicateurGroup(),
                        'cloture' => $dossier->getCloture(),
                    ];
                }

                return new JsonResponse($serializer->serialize($data, 'json'));
            }
        } else {
            throw new NotFoundHttpException("Client introuvable.");
        }
    }

    /**
     * @param int $tous "1 si ajouter 'Tous' dans la liste"
     * @param string $attr_id custom id
     * @param int $label_col nombre col pour label
     * @param int $select_col nombre col pour select
     * @param int $nbr
     * @return Response
     */
    public function exercicesAction($tous = 0, $attr_id = 'exercice', $label_col = 4, $select_col = 6, $nbr = 6, $in_form = true)
    {
        return $this->render('AppBundle:Commun:exercices.html.twig', array(
            'exercices' => Boost::getExercices($nbr),
            'tous' => $tous,
            'attr_id' => $attr_id,
            'label_col' => $label_col,
            'select_col' => $select_col,
            'in_form' => $in_form
        ));
    }

    /**
     * @param $dossier
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function datePickerAction($dossier)
    {
        $dossier = Boost::deboost($dossier, $this);
        if (is_bool($dossier)) return new Response('security');

        $dossier_sel = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        $cloture = 12;
        if ($dossier_sel != null) $cloture = $dossier_sel->getCloture();

        $moiss = Boost::getMois($cloture);
        $exercices = Boost::getExercices(5, 1);

        return $this->render('AppBundle:Commun:datePicker.html.twig', array('exercices' => $exercices, 'moiss' => $moiss));
    }

    /**
     * afficher utilisateurs return combow si $conteneur = 0 json si 1
     *
     * @param $conteneur
     * @param string $client
     * @return JsonResponse|Response
     */
    public function utilisateursAction($conteneur = 0, $client = '')
    {
        $user = $this->getUser();
        $acces_utilisateur = $user->getAccesUtilisateur()->getCode();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_CLIENT')) {
            $client_sel = null;
            if ($client != '')
                $client_sel = $this->getDoctrine()->getRepository('AppBundle:Client')->createQueryBuilder('c')
                    ->where('c.id = :id')
                    ->setParameter('id', $client)
                    ->getQuery()
                    ->getOneOrNullResult();

            $utilisateurs = array();

            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $repository = $this->getDoctrine()->getRepository('AppBundle:Utilisateur');
                if ($client_sel == null)
                    $query = $repository->createQueryBuilder('u')
                        ->addOrderBy('u.client', 'ASC')
                        ->addOrderBy('u.nom', 'ASC')
                        ->getQuery();
                else
                    $query = $repository->createQueryBuilder('u')
                        ->where('u.client = :client')
                        ->setParameter('client', $client_sel)
                        ->addOrderBy('u.client', 'ASC')
                        ->addOrderBy('u.nom', 'ASC')
                        ->getQuery();
            } else {
                $repository = $this->getDoctrine()->getRepository('AppBundle:Utilisateur');
                $query = $repository->createQueryBuilder('u')
                    ->where('u.client = :client')
                    ->setParameter('client', $client_sel)
                    ->addOrderBy('u.client', 'ASC')
                    ->addOrderBy('u.nom', 'ASC')
                    ->getQuery();
            }

            if ($conteneur == 0) {
                $utilisateurs = $query->getResult();
                return $this->render('AppBundle:Commun:utilisateurs.html.twig', array('conteneur' => $conteneur));
            }
            if ($conteneur == 1) {
                $utilisateurs = $query->getArrayResult();
                return new JsonResponse($utilisateurs);
            }
        }

        return new Response('Accès refusé');
    }

    /**
     * @param $conteneur
     * @param $tous
     * @return JsonResponse|Response
     */
    public function regimeFiscalsAction($conteneur = 0, $tous = 1)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:RegimeFiscal');
        $query = $repository->createQueryBuilder('r')
            ->where('r.status = 1')
            ->orderBy('r.libelle', 'ASC')->getQuery();
        $regimeFiscals = $query->getArrayResult();

        if ($conteneur == 0)
            return $this->render('AppBundle:Commun:regimeFiscals.html.twig', array('regimeFiscals' => $regimeFiscals, 'tous' => $tous));
        if ($conteneur == 1)
            return new JsonResponse($regimeFiscals);
    }

    /**
     * @param $dossier
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function clotureAction($dossier)
    {
        $dossier = Boost::deboost($dossier, $this);
        if (is_bool($dossier)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id', $dossier)
            ->getQuery()
            ->getOneOrNullResult();

        return new Response(($dossier != null) ? $dossier->getCloture() : 12);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function imagePicdataAction(Request $request)
    {
        $post = $request->request;
        $image_id = Boost::deboost($post->get('image_id'), $this);
        if (is_bool($image_id)) return new Response('security');

        $image = $this->getDoctrine()->getRepository('AppBundle:Image')->createQueryBuilder('im')
            ->where('im.id = :id')
            ->setParameter('id', $image_id)
            ->getQuery()
            ->getOneOrNullResult();
        $params = '?nomfichier=' . $image->getNom() . '.' . $image->getExtImage() . '&numimg=' . $image->getNumPage();
        return new Response($params);

        return new Response('images/' .
            $image->getLot()->getDossier()->getSite()->getClient()->getNom() . '/' .
            $image->getLot()->getDossier()->getNom() . '/' .
            $image->getExercice() . '/' .
            $image->getLot()->getDateScan()->format('Y-m-d') . '/' .
            $image->getLot()->getLot() . '/' .
            $image->getNom() . '.' . $image->getExtImage());
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function dateAncienneteAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'), $this);
        if (is_bool($dossier)) return new Response('security');

        $exercice = $post->get('exercice');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        return new JsonResponse($this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')->getDateAnciennete($dossier, $exercice));
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function dateCalculAncienneteAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'), $this);
        if (is_bool($dossier)) return new Response('security');

        $exercice = $post->get('exercice');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        return new JsonResponse($this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')->getDateCalculAnciennete($dossier, $exercice));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function clotureExercicesAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'), $this);
        if (is_bool($dossier)) return new Response('security');

        $exercices = json_decode($post->get('exercices'));
        $exercices = array_reverse($exercices);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        $clotures = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')->exercicesAreClotured($dossier, $exercices);
        return $this->render('AppBundle:Commun:exercicesClotures.html.twig', array('clotures' => $clotures));
    }

    /**
     * @param Request $request
     * @param string $otherParam
     * @return Response
     */
    public function periodesAction(Request $request, $otherParam = '')
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'), $this);
        if (is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        $periodePicker = Boost::getDatePickerPopOverV2(Boost::getExercices(6,0), Boost::getMois($dossier->getCloture()));
        return $this->render('AppBundle:Commun:periodes-picker.html.twig', array('periodePicker' => $periodePicker, 'otherParam' => $otherParam));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function imageAction(Request $request)
    {
        $post = $request->request;
        $image = Boost::deboost($post->get('img'), $this);
        if (is_bool($image)) return new Response('security');
        $image = $this->getDoctrine()->getRepository('AppBundle:Image')->find($image);
        return $this->render('AppBundle:Commun:image.html.twig', array('image' => $image));
    }

    public function userAction()
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        $theme = $this->getDoctrine()
            ->getRepository('AppBundle:ClientTheme')
            ->getColorTheme($utilisateur->getClient());

        return new JsonResponse([
            'nom' => ($utilisateur->getNom() == null) ? '' : $utilisateur->getNom(),
            'prenom' => ($utilisateur->getPrenom() == null) ? '' : $utilisateur->getPrenom(),
            'email' => $utilisateur->getEmail(),
            'client' => $utilisateur->getClient()->getNom(),
            'theme' => $theme['primarycolor']
        ]);
    }

    public function jwtAction(){

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        $key       = "746BD197AAF950731B309F6F49AA86957F5BC98D73C0DC40ED9788504506D239";
        $subdomain = "scriptura6903";
        $now       = time();
        $token = array(
            // "jti"   => md5($now . rand()),
            "name"  => $utilisateur->getNom(),
            "email" => $utilisateur->getEmail(),
            "iat"   => $now,
            'external_id' => $utilisateur->getId()
        );
        
        $jwt = JWT::encode($token, $key);

//        $location = "https://" . $subdomain . ".zendesk.com/access/jwt?jwt=" . $jwt;
//        if(isset($_GET["return_to"])) {
//            $location .= "&return_to=" . urlencode($_GET["return_to"]);
//        }
//// Redirect
//        header("Location: " . $location);
//
//        return new Response('JWT OK');

        return new JsonResponse($jwt);

        // return new JsonResponse(['success' => true, 'jwt' => $jwt]);
    }

    public function getDossiersActifByClientAction($client,$exercice)
    {
        // $dossiers = $this->getDoctrine()
        //                 ->getRepository('AppBundle:ReleveManquant')
        //                 ->getListDosierByExo($client,$exercice);

        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client);

        if (intval($exercice) == 0) $exercice = null;

        $dossiers = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(), $the_client, null, $exercice, true);

        return new JsonResponse($dossiers);
    }

    public function infosUserAction()
    {
        $utilisateur = $this->getUser();

        return new JsonResponse([
            'nom' => ($utilisateur->getNom() == null) ? '' : $utilisateur->getNom(),
            'prenom' => ($utilisateur->getPrenom() == null) ? '' : $utilisateur->getPrenom(),
            'email' => $utilisateur->getEmail(),
            'client' => $utilisateur->getClient()->getNom(),
            'tel' => ($utilisateur->getTel() == null) ? '' : $utilisateur->getTel()
        ]);
    }


    
}
