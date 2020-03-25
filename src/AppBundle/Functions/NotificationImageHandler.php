<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 12/01/2018
 * Time: 16:23
 */

namespace AppBundle\Functions;


use AppBundle\Entity\Dossier;
use AppBundle\Entity\Emails;
use AppBundle\Entity\NotificationImage;
use AppBundle\Entity\Smtp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;

class NotificationImageHandler
{

    private $em;
    private $twig;

    public function __construct(EntityManager $entityManager, TwigEngine $twig)
    {
        $this->em = $entityManager;
        $this->twig = $twig;
    }

    /**
     * Point d'Entrée Notification Images
     *
     * @throws \Exception
     */
    public function bootstrap()
    {
        $now = new \DateTime();
        $exercice = $now->format('Y');
        $exercice_n_1 = $exercice - 1;

        $smtps = $this->em->getRepository('AppBundle:Smtp')
            ->createQueryBuilder('smtp')
            ->select('smtp')
            ->innerJoin('smtp.client', 'client')
            ->addSelect('client')
            ->where('client.sendNotificationImage = :send_notication')
            ->setParameters([
                'send_notication' => TRUE,
            ])
            ->getQuery()
            ->getResult();
        /** @var \AppBundle\Entity\Smtp $smtp */
        foreach ($smtps as $smtp) {

            $this->generateEmail($smtp->getClient(), NULL, NULL, $exercice_n_1, TRUE);
            $this->generateEmail($smtp->getClient(), NULL, NULL, $exercice, FALSE);
        }
    }

    /**
     * Générer Email à envoyer
     *
     * @param $client
     * @param $site
     * @param $dossier
     * @param $exercice
     * @param bool $is_n_1
     * @return bool
     * @throws \Exception
     */
    public function generateEmail($client, $site, $dossier, $exercice, $is_n_1 = FALSE)
    {
        $now = new \DateTime();
        $user = $this->em
            ->getRepository('AppBundle:Utilisateur')
            ->findOneBy([
                'email' => 'philcastellan@gmail.com',
            ]);
        if ($user) {
            $listes = $this->em
                ->getRepository('AppBundle:Tbimage')
                ->getListe($client, $site, $dossier, $exercice, $user, FALSE, false);

            setlocale(LC_TIME, 'fr_FR', 'fra');

            $politesses =[
                1 => "Cher",
                2 => "Chère",
                3 => "Chère",
                4 => "Cher"
            ];

            $titre = [
                1 => "Monsieur",
                2 => "Madame",
                3 => "Mademoiselle",
                4 => "Monsieur, Madame",
            ];
            $periode = [
                "P" => "date",
                "M" => "mois",
                "T" => "trimestre",
                "Q" => "quadrimestre",
                "S" => "semestre",
                "A" => "année",
            ];

            $rows = $listes['rows'];
            $with_retard = $listes['with_retard'];
            $liste_status = $listes['liste_status'];
            /** @var \Doctrine\Common\Collections\ArrayCollection $dossiers */
            $dossiers = new ArrayCollection($listes['dossiers']);

            $datas = [];


            foreach ($rows as $row) {
                //9___CODE_CLIENT___15822___2016-01-01
                $split = preg_split('/___/', $row['id']);
                $id_categorie = isset($split[0]) ? $split[0] : NULL;
                $code_categorie = isset($split[1]) ? $split[1] : NULL;
                $id_dossier = isset($split[2]) ? $split[2] : NULL;

                $debut = isset($split[3]) ? new \DateTime($split[3]) : NULL;
                $cell = $row['cell'];
                /** @var Dossier $dossier */
                $dossier = NULL;
                $dossiers->exists(function ($index, Dossier $item) use ($id_dossier, &$dossier) {
                    if ($item->getId() === intval($id_dossier)) {
                        $dossier = $item;
                        return TRUE;
                    }
                    return FALSE;
                });

                if (in_array($code_categorie, $with_retard)) {
                    if(!$dossier->getNonTraitable() && (!isset($liste_status[$id_dossier]) || $liste_status[$id_dossier] != 1)) {
                        $datas[$id_dossier][] = [
                            'dossier' => $dossier,
                            'id_categorie' => $id_categorie,
                            'code_categorie' => $code_categorie,
                            'debut' => $debut,
                            'image' => $cell,
                        ];
                    }
                }
            }
            $results = [];
            $config = [];

            foreach ($datas as $key => $values) {
                $dossier = NULL;
                if ($dossiers->exists(function ($index, Dossier $item) use ($key, &$dossier) {
                    if ($item->getId() === intval($key)) {
                        $dossier = $item;
                        return TRUE;
                    }
                    return FALSE;
                })) {

                    /** @var NotificationImage $notification */
                    $notification = $this->em
                        ->getRepository('AppBundle:NotificationImage')
                        ->getByDossier($dossier);



                    if ($this->okForSend($notification, $is_n_1)) {
                        echo $exercice . "\r\n";
                        echo $notification->getDossier()->getId() . "\r\n";
                        $manquant = [];
                        $contenu = $notification->getContenu();
                        $destinataire = $notification->getDestinataire();
                        $copie = $notification->getCopie();
//                        $titre_contact = $notification->getTitreContact() && isset($titre[$notification->getTitreContact()])
//                            ? $titre[$notification->getTitreContact()] : "Monsieur";

                        $titre_contact = "Monsieur";
                        $politesse = "Cher";
                        if($notification->getTitreContact() !== null){
                            if(isset($titre[$notification->getTitreContact()])){
                                $titre_contact = $titre[$notification->getTitreContact()];
                                $politesse = $politesses[$notification->getTitreContact()];
                            }
                        }


                        $nom_contact = $notification->getNomContact();
                        $debut_envoi = $notification->getDebutEnvoi();
                        $periode_envoi = $notification->getDossier()->getTbimagePeriode() ? $notification->getDossier()->getTbimagePeriode()->getPeriodePiece() : "";
                        $dernier_envoi = $is_n_1 ? $notification->getDernierEnvoiN1() : $notification->getDernierEnvoiN();

                        if($periode_envoi === "P"){
                            $contenu = str_replace("[[frequence]]", $debut_envoi->format('d-m-Y'), $contenu);
                        }
                        else {
                            $contenu = str_replace("[[frequence]]", $periode[$periode_envoi], $contenu);
                        }

                        $nom_client = "";

                        if($client !== null){
                            $nom_client = $client->getNom();
                        }

                        $config[$dossier->getId()] = [
                            'contenu' => $contenu,
                            'destinataire' => $destinataire,
                            'copie' => $copie,
                            'titre_contact' => $titre_contact,
                            'nom_contact' => $nom_contact,
                            'politesse' => $politesse,
                            'debut_envoi' => $debut_envoi,
                            'periode_envoi' => $periode_envoi,
                            'dernier_envoi' => $dernier_envoi,
                            'nom_client' => $nom_client
                        ];

                        foreach ($values as $value) {
                            if ($value['dossier']) {
                                $id_dossier = $value['dossier']->getId();

                                if ($value['image']) {
                                    for ($i = 0; $i < count($value['image']); $i++) {
                                        if ($i > 0 && $value['image'][$i] === 'xxx') {
                                            $nom_categorie = $value['image'][0];
                                            if ($i == 1) {
                                                $date = $value['debut'];
                                            } else {
                                                /** @var \DateTime $tmp */
                                                $tmp = clone $value['debut'];
                                                $tmp->add(new \DateInterval('P' . ($i - 1) . 'M'));
                                                $date = $tmp;
                                            }
                                            // Tester si date n'est pas le mois encours
                                            $tmp_date = new \DateTime($date->format('Y-m-01'));
                                            $tmp_now = new \DateTime($now->format('Y-m-01'));
                                            if ($tmp_date < $tmp_now) {
                                                $manquant[$id_dossier][] = [
                                                    $nom_categorie => utf8_encode(strftime('%B %Y', $date->getTimestamp()))
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $results[] = $manquant;
                    }
                }
            }

            foreach ($results as $key => $value) {
                foreach ($value as $dossier => $manquants) {
                    try {
                        $the_config = $config[$dossier];
                        /** @var Dossier $the_dossier */
                        $the_dossier = $this->em->getRepository('AppBundle:Dossier')
                            ->find($dossier);

                        if ($the_config['destinataire']) {
                            $content = $this->twig->render('@TableauImage/Email/rappel_image.html.twig', [
                                'dossier' => $dossier,
                                'manquants' => $manquants,
                                'config' => $config[$dossier],
                                'exercice' => $exercice,
                                'client_id' => $the_dossier->getSite()->getClient()->getId(),
                            ]);


                            if ($the_dossier) {

                                /** @var NotificationImage $notification */
                                $notification = $this->em
                                    ->getRepository('AppBundle:NotificationImage')
                                    ->getByDossier($the_dossier);
//                                $sujet = "Pièces à nous envoyer d'urgence";
                                $sujet = "Relance de pièces pour ".$the_dossier->getSite()->getClient()->getNom();
                                if($notification->getObjet()){
                                    if(str_replace(' ','',$notification->getObjet()) !== ''){
                                        $sujet = $notification->getObjet();
                                    }
                                }

                                $sujet .= " - Exercice $exercice";

                                /** @var Smtp $smtp */
                                $smtp = $this->em->getRepository('AppBundle:Smtp')
                                    ->getSmtpByClient($the_dossier->getSite()->getClient());

                                $copie_cachees = $smtp->getCopie();
                                $email = new Emails();

                                $email->setToAddress($the_config['destinataire'])
                                    ->setFromLabel("Rappel pièces")
                                    ->setCc($the_config['copie'])
//                                    ->setSujet("Pièces à nous envoyer d'urgence - Exercice $exercice")
                                    ->setSujet($sujet)
                                    ->setContenu($content)
                                    ->setDateCreation(new \DateTime())
                                    ->setTypeEmail("RAPPEL_IMAGE")
                                    ->setSmtp($smtp)
                                    ->setDossier($the_dossier);

                                if($copie_cachees !== null) {
                                    if (trim($copie_cachees) !== '') {
                                        $email->setBcc($copie_cachees);
                                    }
                                }

                                $this->em->persist($email);

                                if ($is_n_1) {
                                    $notification->setDernierEnvoiN1(new \DateTime());
                                } else {
                                    $notification->setDernierEnvoiN(new \DateTime());
                                }

                                $this->em->flush();
                            }
                        }
                    } catch (\Exception $e) {
                        echo $e->getMessage() . "\r\n";
                        return FALSE;
                    }
                }
            }
        }
        return TRUE;
    }

    /**
     * Tester si on peut envoyer la Notification
     *
     * @param NotificationImage|NULL $notification
     * @param bool $is_n_1
     * @return bool
     * @throws \Exception
     */
    private function okForSend(NotificationImage $notification = NULL, $is_n_1 = FALSE)
    {
        if ($notification) {
            $periode = [
                "P" => 0,
                "M" => 1,
                "T" => 3,
                "Q" => 4,
                "S" => 6,
                "A" => 12,
            ];

            $contenu = $notification->getContenu();
            $destinataire = $notification->getDestinataire();
            $debut_envoi = $notification->getDebutEnvoi();
            $periode_envoi = $notification->getDossier()->getTbimagePeriode() ? $notification->getDossier()->getTbimagePeriode()->getPeriodePiece() : "";
            $dernier_envoi = $is_n_1 ? $notification->getDernierEnvoiN1() : $notification->getDernierEnvoiN();

            if (
                !$contenu || strlen($contenu) === 0 || !$destinataire || strlen($destinataire) === 0
                || ($is_n_1 && !$notification->getEnvoiN1()) || (!$is_n_1 && !$notification->getEnvoiN())
                || !$debut_envoi || !$periode_envoi || $periode_envoi == ''
            ) {
                return FALSE;
            }

            $now = new \DateTime();
            $now->setTime(0, 0);
            $jour_debut = $debut_envoi->format('d');


            if ($dernier_envoi) {
                if($periode[$periode_envoi] === 0){
                    $last = new \DateTime($dernier_envoi->format('Y-m-d'));
                }
                else {
                    $last = new \DateTime($dernier_envoi->format('Y-m-01'));
                }
                $next = clone $last;
                $next->add(new \DateInterval('P' . $periode[$periode_envoi] . 'M'));
                if ($jour_debut > 1) {
                    $next->add(new \DateInterval('P' . ($jour_debut - 1) . 'D'));
                }
            } else {
                $next = clone $debut_envoi;
            }

            if($periode[$periode_envoi] === 0){
                if($next == $now){
                    return true;
                }
            }else {
                if ($next <= $now) {
                    return TRUE;
                }
            }
            return FALSE;
        }
        return FALSE;
    }
}