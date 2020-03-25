<?php

namespace InfoPerdosBundle\Controller;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\LogActivite;
use AppBundle\Entity\ResponsableCsd;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ActivationDossierController extends Controller
{
    /**
     * Index Activation Dossier (Liste dossier chargé avec AJAX)
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('InfoPerdosBundle:ActivationDossier:index.html.twig');
    }

    public function editAction(Request $request, Dossier $dossier)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()
                ->getManager();
            $old_date_stop_saisie = $dossier->getDateStopSaisie() ? clone $dossier->getDateStopSaisie() : null;

            $status = intval($request->request->get('status', 0));
            $status_debut = intval($request->request->get('status_debut', ''));
            $stop_saisie_date = $request->request->get('stop_saisie_date', '');

            if ($status == 0) {
                $status = 1;
            }

            $dossier->setStatus($status);
            if ($status != 1) {
                if (strlen(strval($status_debut)) == 4) {
                    $dossier->setStatusDebut($status_debut);
                }
            } else {
                $dossier->setStatusDebut(null);
            }

            $activite_message = "ACTIVER_DOSSIER";
            if ($status == 2) {
                $activite_message = "SUSPENDRE_DOSSIER";
            } elseif ($status == 3) {
                $activite_message = "RADIER_DOSSIER";
            }
            $activite_description = '';
            if (strlen($stop_saisie_date) == 10) {
                $dossier->setDateStopSaisie(\DateTime::createFromFormat('d/m/Y', $stop_saisie_date));
                $this->sendNotificationStopSaisie($dossier, TRUE);
                $activite_description = 'Stopper saisie';
            } else {
                $dossier->setDateStopSaisie(null);
                if ($old_date_stop_saisie) {
                    $this->sendNotificationStopSaisie($dossier, FALSE);
                }
            }

            $em->flush();


            /** ENREGISTREMENT LOG ACTIVITE */
            $log_activite = new LogActivite();
            $log_activite->setUtilisateur($this->getUser())
                ->setDossier($dossier)
                ->setDate(new \DateTime())
                ->setType(2)
                ->setMessage($activite_message)
                ->setDescription($activite_description);
            $em->persist($log_activite);
            $em->flush();

            /** ENVOI EMAIL NOTIFICATION MODIF */
            $status_text = "Actif";
            if ($status == 2) {
                $status_text = "Suspendu";
            } elseif ($status == 3) {
                $status_text = "Radié";
            }

            $sujet = "Modification du dossier " . $dossier->getNom() . " (" . $dossier->getSite()->getClient()->getNom() . ")";
            $texte = "Le dossier <strong>" . $dossier->getNom() . "</strong>, client " . $dossier->getSite()->getClient()->getNom()
                . ", est <strong>" . $status_text . "</strong>.";

            $contenu = $this->renderView('@InfoPerdos/Emails/notificationStatutDossier.html.twig', array(
                'texte' => $texte,
                'utilisateur' => $this->getUser(),
            ));

            $message = \Swift_Message::newInstance()
                ->setSubject($sujet)
                ->setFrom('support@lesexperts.biz', 'Gestion Dossier Picdata');

            /** Envoyé à ARQ si pas destinataires */
            $message->setTo("arq@scriptura.biz");
            $message->addCc("support@scriptura.biz");

            /** Responsable Site ou Client */
            $responsables = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->getResponsableSiteOuClient($dossier);


            $clientFinaux = $this->getDoctrine()
                ->getRepository('AppBundle:Utilisateur')
                ->getUtilisateursByTypeAcces(6);

            $clientFinauxMail  = [];

            /** @var Utilisateur $clientFinal */
            foreach ($clientFinaux as $clientFinal){
                if($clientFinal->getEmail() !== '' && $clientFinal->getEmail() !== null)
                    if(!in_array($clientFinal->getEmail(), $clientFinauxMail))
                        $clientFinauxMail[] = $clientFinal->getEmail();
            }


            /** @var ResponsableCsd $responsable */
            foreach ($responsables as $responsable) {
                if ($responsable->getEmail() && $responsable->getEmail() != '') {
                    if($responsable->getEnvoiMail() === 1){
                        if(!in_array($responsable->getEnvoiMail(), $clientFinauxMail)){
                            $message->addTo($responsable->getEmail());
                        }
                    }
                }
            }

            /** Responsables Scriptura */
            $responsables = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->getResponsableClientScriptura($dossier->getSite()->getClient());

            /** @var ResponsableCsd $responsable */
            foreach ($responsables as $responsable) {
                if ($responsable->getEmail() && $responsable->getEmail() != '') {
                    $message->addTo($responsable->getEmail());
                }
            }

            $message->setBody($contenu, 'text/html');
            $this->get('mailer')->send($message);

            $data = [
                'erreur' => false,
            ];

            return new JsonResponse(json_encode($data));
        } else {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    private function sendNotificationStopSaisie(Dossier $dossier, $stop = TRUE)
    {
        if ($stop) {
            $activite_message = 'STOP_SAISIE';
            $sujet = "Stop Saisie " . $dossier->getNom() . " (" . $dossier->getSite()->getClient()->getNom() . ")";
            $texte = "La saisie du dossier <strong>" . $dossier->getNom() . "</strong>, client " . $dossier->getSite()->getClient()->getNom()
                . ", est <strong>à stopper</strong> au <strong>" . $dossier->getDateStopSaisie()->format('d/m/Y') . "</strong>.";
        } else {
            $activite_message = 'REPRENDRE_SAISIE';
            $sujet = "Reprendre Saisie " . $dossier->getNom() . " (" . $dossier->getSite()->getClient()->getNom() . ")";
            $texte = "La saisie du dossier <strong>" . $dossier->getNom() . "</strong>, client " . $dossier->getSite()->getClient()->getNom()
                . ", est <strong>à reprendre</strong>.";
        }

        $em = $this->getDoctrine()->getManager();
        /** ENREGISTREMENT LOG ACTIVITE */
        $log_activite = new LogActivite();
        $log_activite->setUtilisateur($this->getUser())
            ->setDossier($dossier)
            ->setDate(new \DateTime())
            ->setType(2)
            ->setMessage($activite_message)
            ->setDescription("stopper saisie");
        $em->persist($log_activite);
        $em->flush();

        $contenu = $this->renderView('@InfoPerdos/Emails/notificationStatutDossier.html.twig', array(
            'texte' => $texte,
            'utilisateur' => $this->getUser(),
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject($sujet)
            ->setFrom('support@lesexperts.biz', 'Gestion Dossier Picdata');

        /** Envoyé à ARQ si pas destinataires */
        $message->setTo("arq@scriptura.biz");

        /** Responsable Dossier */
        $responsables = $this->getDoctrine()
            ->getRepository('AppBundle:ResponsableCsd')
            ->getResponsableParDossier($dossier);

        $clientFinaux = $this->getDoctrine()
            ->getRepository('AppBundle:Utilisateur')
            ->getUtilisateursByTypeAcces(6);

        $clientFinauxMail  = [];

        /** @var Utilisateur $clientFinal */
        foreach ($clientFinaux as $clientFinal){
            if($clientFinal->getEmail() !== '' && $clientFinal->getEmail() !== null)
                if(!in_array($clientFinal->getEmail(), $clientFinauxMail))
                    $clientFinauxMail[] = $clientFinal->getEmail();
        }

        /** @var ResponsableCsd $responsable */
        foreach ($responsables as $responsable) {
            if ($responsable->getEmail() && $responsable->getEmail() != '') {
                if($responsable->getEnvoiMail() === 1){
                    if(!in_array($responsable->getEnvoiMail(), $clientFinauxMail)) {
                        $message->addTo($responsable->getEmail());
                    }
                }

            }
        }

        /** Responsables Scriptura */
        $responsables = $this->getDoctrine()
            ->getRepository('AppBundle:ResponsableCsd')
            ->getResponsableClientScriptura($dossier->getSite()->getClient());

        /** @var ResponsableCsd $responsable */
        foreach ($responsables as $responsable) {
            if ($responsable->getEmail() && $responsable->getEmail() != '') {
                $message->addTo($responsable->getEmail());
            }
        }

        /** Destinataires autres */
        $emails = $this->getDoctrine()
            ->getRepository('AppBundle:Config')
            ->getEmailNotificationStopSaisie();

        foreach ($emails as $email) {
            if ($email && $email != '') {
                $message->addTo($email);
            }
        }

        /** Copie */
//        $message->setCc("support@scriptura.biz");

        $message->setBody($contenu, 'text/html');
        $this->get('mailer')->send($message);
        return TRUE;
    }
}
