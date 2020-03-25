<?php

namespace TableauImageBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Categorie;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Emails;
use AppBundle\Entity\NotificationImage;
use AppBundle\Entity\Tbimage;
use AppBundle\Entity\TbimageCategorie;
use AppBundle\Entity\TbimagePeriode;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RappelImageController extends Controller
{

    public function listParametreAction($client, $site)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $rows = [];
        if ($the_client) {
            if ($site == '0') {
                $the_site = NULL;
            } else {
                $site_id = Boost::deboost($site, $this);
                $the_site = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->find($site_id);
            }
            $notifications = $this->getDoctrine()
                ->getRepository('AppBundle:NotificationImage')
                ->getByClient($the_client, $this->getUser(), $the_site);

            /** @var \AppBundle\Entity\NotificationImage $notification */
            foreach ($notifications as $notification) {
                $debut = "";
                if ($notification->getDebutEnvoi()) {
                    $debut = $notification->getDebutEnvoi()->format('Y-m-d');
                }
                $contenu = "";
                if ($notification->getContenu()) {
                    if (strlen($notification->getContenu()) >= 50) {
                        $contenu = substr($notification->getContenu(), 0, 50) . ' ...';
                    } else {
                        $contenu = $notification->getContenu();
                    }
                }

                $dossier = $notification->getDossier();

                $status = '';
                switch ($dossier->getStatus()){
                    case 1:
                        $status = 'Actif';
                        break;
                    case 2:
                        $status = 'Suspendu';
                        if($dossier->getStatusDebut() !== null){
                            $status .= ' - '.$dossier->getStatusDebut();
                        }
                        break;
                    case 3:
                        $status = 'Radié';
                        if($dossier->getStatusDebut() !== null){
                            $status .= ' - '.$dossier->getStatusDebut();
                        }
                        break;
                }

                $rows[] = [
                    'id' => $notification->getId(),
                    'cell' => [
                        $dossier->getNom(),
                        $status,
                        ($dossier->getDateStopSaisie() === null) ? '' : $dossier->getDateStopSaisie()->format('d/m/Y'),
                        $notification->getEnvoiN1(),
                        $notification->getEnvoiN(),
                        $notification->getDestinataire(),
                        $notification->getCopie(),
                        $notification->getTitreContact(),
                        $notification->getNomContact(),
                        $notification->getDossier()->getTbimagePeriode() ? $notification->getDossier()->getTbimagePeriode()->getPeriodePiece() : "",
                        $debut,
                        $contenu,
                        $notification->getContenu(),
                        $notification->getObjet(),
                        '<i class="fa fa-save icon-action js-save-button js-save-rappel-image" title="Enregistrer"></i>',
                    ],
                ];
            }
        }

        $liste = [
            'rows' => $rows,
        ];

        return new JsonResponse($liste);
    }

    public function updateParametreAction(Request $request, NotificationImage $notification)
    {
        $em = $this->getDoctrine()->getManager();
        $envoi_n = $request->request->get('rappel-img-n') == '1' ? TRUE : FALSE;
        $envoi_n_1 = $request->request->get('rappel-img-n-1') == '1' ? TRUE : FALSE;
        $periode = $request->request->get('rappel-img-freq') != '' ? $request->request->get('rappel-img-freq') : 'M';
        $debut = $request->request->get('rappel-img-debut');
        $debut_envoi = NULL;
        if ($debut != '') {
            $debut_envoi = \DateTime::createFromFormat('d/m/Y', $debut);
        }
        $notification->setEnvoiN($envoi_n)
            ->setEnvoiN1($envoi_n_1)
            ->setPeriode($periode)
            ->setDebutEnvoi($debut_envoi);
        $em->flush();
        $data = [
            'erreur' => FALSE,
        ];
        return new JsonResponse($data);
    }

    public function updateParametreAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $client_id = Boost::deboost($request->request->get('client'), '');
        $site_id = Boost::deboost($request->request->get('site'), '');
        $field = $request->request->get('field', '');
        $value = $request->request->get('value');

        if ($field == 'DebutEnvoi') {
            if (trim($value) == '') {
                $value = NULL;
            } else {
                $value = \DateTime::createFromFormat('d/m/Y', $value);
            }
        }

        if ($field != '' && method_exists('AppBundle\Entity\NotificationImage', 'set' . $field)) {
            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
            $site = NULL;
            if ($site_id != '0') {
                $site = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->find($site_id);
            }
            $notifications = $this->getDoctrine()
                ->getRepository('AppBundle:NotificationImage')
                ->getByClient($client, $this->getUser(), $site);
            /** @var NotificationImage $notification */
            foreach ($notifications as $notification) {
                $notification->{'set' . $field}($value);
            }
        }
        $em->flush();

        $data = [
            'erreur' => FALSE,
        ];
        return new JsonResponse($data);
    }

    public function updateDestinataireAction(Request $request, NotificationImage $notification)
    {
        $em = $this->getDoctrine()->getManager();
        $titre = $request->request->get('titre', 1);
        $nom = $request->request->get('nom', '');
        $destinataire = $request->request->get('destinataire', '');
        $copie = $request->request->get('copie');

        $user_emails = $this->getDoctrine()
            ->getRepository('AppBundle:NotificationImage')
            ->getEmailUsersDossier($notification->getDossier());
        $array_destinataires = explode(";", $destinataire);

        $new_destinataires = array_filter($array_destinataires, function($dest) use ($user_emails) {
            return !in_array($dest, $user_emails);
        });

        if ($nom != '' && $destinataire != '') {
            $notification
                ->setTitreContact($titre)
                ->setNomContact($nom)
                ->setCopie($copie);
            if (count($new_destinataires) > 0) {
                $notification->setDestinataire(trim(implode(";", $new_destinataires), ";"));
            }
            $em->flush();

            $data = [
                'erreur' => FALSE,
            ];
            return new JsonResponse($data);
        } else {
            throw new BadRequestHttpException('Données invalides.');
        }
    }

    public function emailDefaultContentAction()
    {
        /** @var \AppBundle\Entity\EmailTemplate $template */
        $template = $this->getDoctrine()
            ->getRepository('AppBundle:EmailTemplate')
            ->getByCode('email_image_default');
        $default_content = '';
        if ($template) {
            $default_content = $template->getContenu();
        }
        return new Response($default_content);
    }

    public function editEmailContenuAction(Request $request, $tous)
    {
        $em = $this->getDoctrine()->getManager();
        $notification_id = $request->request->get('notification', '');
        $contenu = $request->request->get('contenu');
        $objet = $request->request->get('objet');

        if(trim($objet) === ''){
            $objet = null;
        }

        $client_id = Boost::deboost($request->request->get('client'), '');
        $site_id = Boost::deboost($request->request->get('site'), '');

        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $site = NULL;
        if ($site_id != '0') {
            $site = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->find($site_id);
        }

        if ($tous == 1) {
            $notifications = $this->getDoctrine()
                ->getRepository('AppBundle:NotificationImage')
                ->getByClient($client, $this->getUser(), $site);

            /** @var NotificationImage $notification */
            foreach ($notifications as $notification) {
                $notification->setContenu($contenu);
                $notification->setObjet($objet);
            }
        } else {
            /** @var NotificationImage $notification */
            $notification = $this->getDoctrine()
                ->getRepository('AppBundle:NotificationImage')
                ->find($notification_id);
            if ($notification) {
                $notification->setContenu($contenu);
                $notification->setObjet($objet);
            }
        }

        $em->flush();

        $data = [
            'erreur' => FALSE,
        ];
        return new JsonResponse($data);
    }

    public function envoiStatusAction($client)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $data = [];
        if ($the_client) {
            $data = [
                'sendNotificationImage' => $the_client->getSendNotificationImage(),
            ];
        }
        return new JsonResponse($data);
    }

    public function envoiStatusEditAction(Request $request, $client)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        $envoi_status = $request->request->get('envoi_status') == '1' ? TRUE : FALSE;
        if ($the_client) {
            $em = $this->getDoctrine()->getManager();
            $the_client->setSendNotificationImage($envoi_status);
            $em->flush();
        }

        $data = [
            'erreur' => FALSE,
            'status' => $envoi_status,
            'client' => $the_client->getId(),
        ];
        return new JsonResponse($data);
    }

    public function logEnvoiAction($client, $site, $dossier)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        if ($the_client) {
            $the_site = NULL;
            if ($site != '0') {
                $site_id = Boost::deboost($site, $this);
                $the_site = $this->getDoctrine()
                    ->getRepository('AppBundle:Site')
                    ->find($site_id);
            }
            $the_dossier = NULL;
            if ($dossier != '0') {
                $dossier_id = Boost::deboost($dossier, $this);
                $the_dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($dossier_id);
            }
            $emails = $this->getDoctrine()
                ->getRepository('AppBundle:Emails')
                ->getNotificationImage($this->getUser(), $the_client, $the_site, $the_dossier);
            $rows = [];
            /** @var \AppBundle\Entity\Emails $email */
            foreach ($emails as $email) {
                $status = '<span class="label label-danger" style="display:inline-block;width: 100%">Non envoyé</span>';
                if ($email->getStatus() == 1) {
                    $status = '<span class="label label-info" style="display:inline-block;width: 100%">Envoyé</span>';
                } elseif ($email->getStatus() == 9) {
                    $status = '<span class="label label-warning" style="display:inline-block;width: 100%">En attente</span>';
                }
                $rows[] = array(
                    'id' => $email->getId(),
                    'cell' => array(
                        $email->getDossier()->getNom(),
                        $email->getDateEnvoi() ? $email->getDateEnvoi()->format('Y-m-d') : '',
                        $email->getSujet(),
                        $status,
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

    public function emailEnvoyeAction(Emails $email)
    {
        $status = '<span class="label label-danger">Non envoyé</span>';
        if ($email->getStatus() == 1) {
            $status = '<span class="label label-info">Envoyé</span>';
        } elseif ($email->getStatus() == 9) {
            $status = '<span class="label label-warning">En attente</span>';
        }
        $data = [
            'dossier' => $email->getDossier() ? $email->getDossier()->getNom() : '',
            'destinataire' => $email->getToAddress(),
            'copie' => $email->getCc(),
            'sujet' => $email->getSujet(),
            'contenu' => $email->getContenu(),
            'date_envoi' => $email->getDateEnvoi() ? $email->getDateEnvoi()->format('d/m/Y') : '',
            'status' => $status
        ];
        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function rappelParamAction(Request $request){
        if($request->isXmlHttpRequest()){

            $post = $request->request;
            $dossier_id = $post->get('dossier_id');
            $the_dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossier_id);
            $notification_images= $this->getDoctrine()
                ->getRepository('AppBundle:NotificationImage')
                ->findBy(array('dossier' => $the_dossier));

            /** @var NotificationImage $notification_image */
            $notification_image = null;
            if(count($notification_images) > 0){
                $notification_image = $notification_images[0];
            }

            $stop = false;
            if($notification_image !== null){
                if(!$notification_image->getEnvoiN() && !$notification_image->getEnvoiN1()){
                    $stop = true;
                }
            }

            /** @var TbimagePeriode $tbimage_periodes */
            $tbimage_periode = $this->getDoctrine()
                ->getRepository('AppBundle:TbimagePeriode')
                ->getTbimagePeriodeByDossier($the_dossier);

            /** @var Categorie[] $categories */
            $categories = $this->getDoctrine()
                ->getRepository('AppBundle:Categorie')
                ->getForTableauImage();
            /** @var TbimageCategorie $tb_image_categorie */
            $tb_image_categorie = $this->getDoctrine()
                ->getRepository('AppBundle:TbimageCategorie')
                ->getTbImageCategorieByDossier($the_dossier);
            $categorie_list = [];
            if($tb_image_categorie !== null){
                $categorie_list = $tb_image_categorie->getCategorieList();
            }

            return $this->render('@TableauImage/Tableau/rappel-param-form.html.twig',
                array(
                    'notification' => $notification_image,
                    'tbimageperiode' => $tbimage_periode,
                    'categorielist' => $categorie_list,
                    'categories' => $categories,
                    'stopenvoi' => $stop
                ));
        }
        throw  new AccessDeniedHttpException('Accès refusé');

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function rappelParamEditAction(Request $request){

        $post = $request->request;

        $the_dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($post->get('notification-dossier-id'));

        if($the_dossier === null){
            return new JsonResponse(['type' => 'error', 'message' => 'dossier null']);
        }

        $periodicite = $post->get('rappel-periodicite');
        $mois_plus = $post->get('rappel-mois-plus');
        $jour = $post->get('rappel-jour');
        $debut = $post->get('rappel-debut');

        $stop = ($post->get('check-stop-send') === 'on') ? true: false;

        if($debut !== ''){
            $debut = \DateTime::createFromFormat('d/m/Y', $debut);
        }

        $categories = $post->get('rappel-categories');
        $destinataire = $post->get('rappel-destinataire');
        $contenu = $post->get('rappel-content');

        $objet = $post->get('rappel-objet');

        if(trim($objet) === ''){
            $objet = null;
        }

        $notification_image = $this->getDoctrine()
            ->getRepository('AppBundle:NotificationImage')
            ->getByDossier($the_dossier);

        $em = $this->getDoctrine()->getManager();

        if($notification_image === null) {
            $notification_image = new NotificationImage();
            $notification_image->setPeriode($periodicite);
            $notification_image->setDossier($the_dossier);
            $notification_image->setDestinataire($destinataire);
            $notification_image->setContenu($contenu);
            $notification_image->setDebutEnvoi($debut);

            $notification_image->setEnvoiN(!$stop);
            $notification_image->setEnvoiN1(!$stop);

            $notification_image->setObjet($objet);

            $em->persist($notification_image);

        }
        else{
            $notification_image->setPeriode($periodicite);
            $notification_image->setDestinataire($destinataire);
            $notification_image->setContenu($contenu);
            $notification_image->setDebutEnvoi($debut);

            if($stop) {
                $notification_image->setEnvoiN(!$stop);
                $notification_image->setEnvoiN1(!$stop);
            }
            else{
               if(!$notification_image->getEnvoiN())
                   $notification_image->setEnvoiN(true);
            }

            $notification_image->setObjet($objet);

        }
        $em->flush();

        /** @var TbimagePeriode $tbimage_periode */
        $tbimage_periode = $this->getDoctrine()
            ->getRepository('AppBundle:TbimagePeriode')
            ->getTbimagePeriodeByDossier($the_dossier);

        if($tbimage_periode === null){
            $tbimage_periode = new TbimagePeriode();
            $tbimage_periode->setPeriodePiece($periodicite);
            $tbimage_periode->setDossier($the_dossier);
            $tbimage_periode->setJour($jour);
            $tbimage_periode->setMoisPlus($mois_plus);

            $em->persist($tbimage_periode);
        }
        else{
            $tbimage_periode->setJour($jour);
            $tbimage_periode->setPeriodePiece($periodicite);
            $tbimage_periode->setMoisPlus($mois_plus);
        }

        $em->flush();


        /** @var TbimageCategorie $tbimage_categorie */
        $tbimage_categorie = $this->getDoctrine()
            ->getRepository('AppBundle:TbimageCategorie')
            ->getTbImageCategorieByDossier($the_dossier);

        if($tbimage_categorie === null){
            $tbimage_categorie = new TbimageCategorie();

            $tbimage_categorie->setDossier($the_dossier);
            $tbimage_categorie->setCategorieList($categories);

            $em->persist($tbimage_categorie);
        }
        else{
            $tbimage_categorie->setCategorieList($categories);
        }
        $em->flush();


        return new JsonResponse(['type' => 'succès', 'message' => 'Modification effectuée']);
    }


    public function historiqueAction(Request $request){
        $dossier_id = $request->query->get('dossierid');

        $the_dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossier_id);

        $emails = $this->getDoctrine()
            ->getRepository('AppBundle:Emails')
            ->findBy(array('dossier' => $the_dossier));

        $row = [];
        foreach ($emails as $email){

            $status = ($email->getStatus() === 1) ? '<span class="label label-info" style="display:inline-block;width: 100%">Envoyé</span>' :
                '<span class="label label-danger" style="display:inline-block;width: 100%">Non envoyé</span>';
            $row[] = ['id' => $email->getId(),
                'cell' => [
                    ($email->getDateCreation()) ? $email->getDateCreation()->format('Y-m-d') : '',
                    $email->getSujet(),
                    $status
                ]];
        }


        return new JsonResponse($row);
    }
}
