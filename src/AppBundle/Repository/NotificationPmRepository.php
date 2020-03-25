<?php
/**
 * Created by PhpStorm.
 * User: Dinoh
 * Date: 22/11/2019
 * Time: 10:32
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\NotificationPm;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurDossier;
use AppBundle\Entity\ListeMailEnvoiAutoPm;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;

class NotificationPmRepository extends EntityRepository
{
    public function getByClient(Client $client, Utilisateur $user, $site)
    {
        $em = $this->getEntityManager();
        $now = new \DateTime();
        $current_exercice = $now->format('Y');
        $dossiers = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->getUserDossier($user, $client, $site, $current_exercice, false);
        $dossier_ids = [0];
        /** @var \AppBundle\Entity\Dossier $dossier */
        foreach ($dossiers as $dossier) {
            $dossier_ids[] = $dossier->getId();
            $notification = $this->getEntityManager()
                ->getRepository('AppBundle:NotificationPm')
                ->findBy(array(
                    'dossier' => $dossier,
                ));
            if (count($notification) == 0) {
                $notification = new NotificationPm();
                $notification->setDossier($dossier);
                $em->persist($notification);
            }
        }

        try {
            $em->flush();
        } catch (OptimisticLockException $ole) {

        }

        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:NotificationPm')
            ->createQueryBuilder('ni');
        $notifications = $qb->select('ni')
            ->innerJoin('ni.dossier', 'd')
            ->addSelect('d')
            ->where($qb->expr()->in('d.id', ':dossier_ids'))
            ->setParameters(array(
                'dossier_ids' => $dossier_ids
            ))
            ->orderBy('d.nom')
            ->getQuery()
            ->getResult();

        $listes = array_map(function(NotificationPm $notification) {
            $em = $this->getEntityManager();
            $destinataires = $this->getEmailDestinataire($notification);
            $notification->setDestinataire(trim(implode(";", $destinataires), ";"));
            $nom_contact = $this->getNomContactDossier($notification);
            $notification->setNomContact($nom_contact);
            return $notification;
        }, $notifications);

        return $listes;
    }

    public function getEmailUsersDossier(Dossier $dossier)
    {
        //RECHERCHE EMAILS DES UTILISATEURS DU DOSSIER
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurDossier')
            ->createQueryBuilder('ud');
        $user_dossiers = $qb->select('ud')
            ->innerJoin('ud.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->innerJoin('ud.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('dossier = :dossier')
            ->andWhere('utilisateur.supprimer = :supprimer')
            ->setParameters(array(
                'dossier' => $dossier,
                'supprimer' => 0
            ))
            ->getQuery()
            ->getResult();
        $emails = array_map(function(UtilisateurDossier $user_dossier) {
            return $user_dossier->getUtilisateur()->getEmail();
        }, $user_dossiers);

        return $emails;
    }



    public function getEmailDestinataire(NotificationPm $notification)
    {

        $emails = array_merge(explode(";", str_replace(" ", "", $notification->getDestinataire())));

        //RECHERCHE EMAILS DES UTILISATEURS DU DOSSIER
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurDossier')
            ->createQueryBuilder('ud');
        $user_dossiers = $qb->select('ud')
            ->innerJoin('ud.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->innerJoin('ud.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('dossier = :dossier')
            ->andWhere('utilisateur.supprimer = :supprimer')
            ->andWhere($qb->expr()->notIn('utilisateur.email', $emails))
            ->setParameters(array(
                'dossier' => $notification->getDossier(),
                'supprimer' => 0
            ))
            ->getQuery()
            ->getResult();
        $destinataires = explode(";", $notification->getDestinataire());
        /** @var UtilisateurDossier $user_dossier */
        foreach ($user_dossiers as $user_dossier) {
            $destinataires[] = $user_dossier->getUtilisateur()->getEmail();
        }

        return $destinataires;
    }

    public function getNomContactDossier(NotificationPm $notification)
    {
        if ($notification->getNomContact() && $notification->getNomContact() != "")
        {
            return $notification->getNomContact();
        }

        //RECHERCHE DES UTILISATEURS DU DOSSIER
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurDossier')
            ->createQueryBuilder('ud');
        $user_dossiers = $qb->select('ud')
            ->innerJoin('ud.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->innerJoin('ud.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('dossier = :dossier')
            ->andWhere('utilisateur.supprimer = :supprimer')
            ->setParameters(array(
                'dossier' => $notification->getDossier(),
                'supprimer' => 0
            ))
            ->getQuery()
            ->getResult();
        $contact = "";
        /** @var UtilisateurDossier $user_dossier */
        foreach ($user_dossiers as $user_dossier) {
            $nom = $user_dossier->getUtilisateur()->getNom();
            $prenom = $user_dossier->getUtilisateur()->getPrenom();
            if (($nom && $nom != "") || ($prenom && $prenom != ""))
            {
                $contact = $prenom . " " . $nom;
                break;
            }
        }

        return $contact;
    }

    public function getByDossier(Dossier $dossier)
    {
        try {
            $notification = $this->getEntityManager()
                ->getRepository('AppBundle:NotificationPm')
                ->createQueryBuilder('ni')
                ->select('ni')
                ->innerJoin('ni.dossier', 'dossier')
                ->where('dossier = :dossier')
                ->setParameters([
                    'dossier' => $dossier
                ])
                ->getQuery()
                ->getOneOrNullResult();

            if (!$notification) {
                $em = $this->getEntityManager();
                $notification = new NotificationPm();
                $notification->setDossier($dossier);
                $em->persist($notification);
                $em->flush();
            }

            $destinataires = $this->getEmailDestinataire($notification);
            $notification->setDestinataire(trim(implode(";", $destinataires), ";"));
            $nom_contact = $this->getNomContactDossier($notification);
            $notification->setNomContact($nom_contact);

            return $notification;

        } catch (NonUniqueResultException $ex) {
            $notifications = $this->getEntityManager()
                ->getRepository('AppBundle:NotificationPm')
                ->createQueryBuilder('ni')
                ->select('ni')
                ->innerJoin('ni.dossier', 'dossier')
                ->where('dossier = :dossier')
                ->setParameters([
                    'dossier' => $dossier
                ])
                ->getQuery()
                ->getResult();
            $i = 0;
            foreach ($notifications as $notification) {
                $em = $this->getEntityManager();
                if ($i > 0) {
                    $em->remove($notification);
                }
                $em->flush();
            }
            return $this->getByDossier($dossier);
        }
    }

    public function getAllAuto()
    {
        $dateEche = '';
        $result = [];
        $dossiers = []; 
        $notifications = []; 
        $params = []; 
        $paramRbOb = [];
        $now = new \DateTime();
        $now->setTime(0, 0);
        /*$now = new \DateTime('2020-02-17');*/

        $listes = $this->getEntityManager()
                        ->getRepository('AppBundle:ListeMailEnvoiAutoPm')
                        ->createQueryBuilder('liste')
                        ->select('liste')
                        ->innerJoin('liste.dossier', 'dossier')
                        ->innerJoin('AppBundle:NotificationDossier', 'notifdoss', 'WITH', 'notifdoss.dossier = dossier')
                        ->innerJoin('notifdoss.notification', 'notif')
                        ->where('dossier.status = 1')
                        ->andWhere('notif.code IN (BANQUE, ENVOIE PM)')
                        ->andWhere('liste.date = :date')
                        ->setParameters([
                            'date' => $now,
                        ])
                        ->getQuery()
                        ->getResult();

        foreach ($listes as $k => $l) {
            $dossier = $l->getDossier();
            if(!in_array($dossier->getId(), $dossiers)){
                $notif = $this->getEntityManager()
                              ->getRepository('AppBundle:NotificationPm')
                              ->findBy(array('dossier' =>$dossier->getId()));
                if(count($notif) > 0){
                    $dossiers[] = $dossier->getId();
                    $notifications[] = $notif[0];
                    $params[$dossier->getId()]['terminer'] = $l->getTerminer(); 
                    $params[$dossier->getId()]['dateFin'] = $l->getDateFin(); 
                    $params[$dossier->getId()]['recurrence'] = $l->getRecurrence(); 
                    $params[$dossier->getId()]['listeId'] = $l->getId(); 
                    $params[$dossier->getId()]['isOk'] = 0; 
                }
            }
        }

        //test si RB et OB est validé
        $paramRbOb['client'][] = 0;
        $paramRbOb['dossier'] = $dossiers;
        //$paramRbOb['exercice'] = $now->format('Y');
        $paramRbOb['exercice'] = 2019;

        $listesRbOb = $this->getEntityManager()->getRepository('AppBundle:Image')->getListeImputeForPm($paramRbOb);

        if(count($listesRbOb > 0)){
            $valueRb = '';
            $tab_key_mois = array();
            $exercice = $paramRbOb['exercice'];
            $betweens = array();
            $tab_mois_cloture = [];
            $exercices = [];
            for ($i = -2; $i < 3; $i++) $exercices[] = $exercice + $i;
            foreach ($listesRbOb as $key => $value) {
                if($value->bc_etat){
                    if (!empty($value->mois)) {
                        $tabMoisManquants = explode(',', $value->mois);
                        $moisManquants = str_replace(' ', '', $tabMoisManquants);
                        //fin mois cloture
                        if ($value->cloture < 9) {
                            $debut_mois = ($exercice - 1) . '-0' . ($value->cloture + 1) . '-01';
                        } else if ($value->cloture >= 9 and $value->cloture < 12) {
                            $debut_mois = ($exercice - 1) . '-' . ($value->cloture + 1) . '-01';
                        } else {
                            $debut_mois = ($exercice) . '-01-01';
                        }
                        //debut mois cloture
                        if ($value->cloture < 10) {
                            $fin_mois = ($exercice) . '-0' . ($value->cloture) . '-01';
                        } else {
                            $fin_mois = ($exercice) . '-' . ($value->cloture) . '-01';
                        }

                        /*$tab_mois_cloture = $this->getBetweenDate($debut_mois, $fin_mois);*/

                        $k = array_key_exists($debut_mois . '-' . $fin_mois, $betweens);
                        if ($k) {
                            $tab_mois_cloture = $betweens[$debut_mois . '-' . $fin_mois];
                        } else{
                            $tab_mois_cloture = $this->getBetweenDate($debut_mois, $fin_mois);

                            $betweens[$debut_mois . '-' . $fin_mois] = $tab_mois_cloture;

                        }

                        $nb_m_mois_exist = false;
                        switch (count($moisManquants)) {
                            case 0:
                                $nb_m_mois_exist = true;
                                $valueRb = 'M-1';
                                break;
                            case 1:
                                $tab_key_mois[$key] = array_intersect($tab_mois_cloture, $moisManquants);
                                break;
                            case 2:
                                $tab_key_mois[$key] = array_intersect($tab_mois_cloture, $moisManquants);
                                break;
                            case 3:
                                $tab_key_mois[$key] = array_intersect($tab_mois_cloture, $moisManquants);
                                break;
                            case 12:
                                $nb_m_mois_exist = true;
                                //jerena aloha raha mis relevé ihany le banque amin'ny alalan'ny dossier
                                $resReleves = $this->getEntityManager()
                                                   ->getRepository('AppBundle:Image')
                                                   ->getInfoReleveByDossier($value->banque_compte_id, $exercice);
                                $valueRb = (count($resReleves) > 0) ? 'Inc.' : 'Auc.';
                                break;
                            default:
                                $nb_m_mois_exist = true;
                                $valueRb = 'Inc.';
                                break;
                        }

                        if (!$nb_m_mois_exist) {
                            $min = 13;
                            foreach ($tab_key_mois[$key] as $key_m => $key_mois_m) {
                                if ($key_m < $min) {
                                    $min = $key_m;
                                }
                            }
                            //Jerena aloha raha misy tsy ampy eo ampovoany
                            $continue = true;
                            $lastIndex = -1;
                            foreach ($tab_key_mois[$key] as $k => $v){
                                if($lastIndex == -1){
                                    $lastIndex = $k;
                                    continue;
                                }
                                if($lastIndex+1 != $k){
                                    $continue = false;
                                    break;
                                }
                                else{
                                    $lastIndex = $k;
                                }
                            }

                            if($continue) {
                                if (intval($exercice) < $now->format('Y')) {
                                    if($min > 9){
                                        $valueRb = 'M-1';
                                    }else if($min == 9){
                                        $valueRb = 'M-2';
                                    }else{
                                        $valueRb = 'Inc.';
                                    }
                                } else {
                                    if (array_key_exists($min, $tab_key_mois[$key])){
                                        $yearNow = $now->format('Y');
                                        $monthNow = $now->format('m');
                                        $dateNow = intval($now->format('d'));
                                        $datetime = \DateTime::createFromFormat('Y-m-d', $tab_key_mois[$key][$min] . "-01");
                                        $interval = $now->diff($datetime);
                                        $diff = $interval->m + 1;
                                        if($dateNow <= 6 ){
                                            $diff = $interval->m;
                                        }

                                        if ($diff === 0) {
                                            $valueRb = 'M-1';
                                        } else if ($diff > 0 && $diff < 11) {
                                            $valueRb = 'M-' . $diff;
                                        } else {
                                            $valueRb = 'Inc.';
                                        }
                                    }else{
                                        $valueRb = 'Inc.';
                                    }
                                }
                            }
                            else{
                                $valueRb = 'Inc.';
                            }
                        }
                    }
                    else {
                        $valueRb = 'M-1';
                        if ($value->cloture < 10) {
                            $fin_mois = ($exercice) . '-0' . ($value->cloture) . '-01';
                        } else {
                            $fin_mois = ($exercice) . '-' . ($value->cloture) . '-01';
                        }
                    }
                    if( $valueRb == 'M-1' ){
                        $params[$value->dossier_id]['isOk'] = 1; 
                    }
                    $dossier = $this->getEntityManager()->getRepository('AppBundle:Dossier')->find($value->dossier_id);
                    $bc = $this->getEntityManager()->getRepository('AppBundle:BanqueCompte')->find(intval($value->banque_compte_id));
                    $releveManquantsTemps = $this->getEntityManager()->getRepository('AppBundle:ReleveManquant')
                        ->createQueryBuilder('rm')
                        ->where('rm.banqueCompte = :banqueCompte')
                        ->andWhere('rm.exercice IN(:exercices)')
                        ->andWhere('rm.dossier = :dossier')
                        ->setParameters([
                            'banqueCompte' => $bc,
                            'exercices' => $exercices,
                            'dossier' => $dossier
                        ])
                        ->getQuery()
                        ->getResult();
                    $rMs = [];
                    foreach ($releveManquantsTemps as $releveManquantsTemp)
                    {
                        $rMs = array_merge($rMs, $releveManquantsTemp->getMois());
                    }
                    $rMs = array_map('trim', $rMs);
                    $releveManquants = array_intersect($tab_mois_cloture, $rMs);
                    $moisRb = '';
                    $tabMois = [];
                    foreach ($tab_mois_cloture as $j => $m)
                    {
                        if(in_array($m,$releveManquants)){
                            if($m == $now->format('Y-m') && intval($now->format('d')) <= 8){
                                continue;
                            }else if($moisRb == ''){
                                $moisRb = $m;
                            }else{
                                $moisRb = $moisRb.'; '.$m;
                            }
                        }
                    }

                    $isOb = true;
                    $libelleObExist = [];
                    $dateObExist = [];
                    $dataObMq = [];
                    $moisOb = '';
                    $sousCategoriesObs = $this->getEntityManager()->getRepository('AppBundle:Souscategorie')->getObs();
                    foreach ($sousCategoriesObs as $souscategorie)
                    {
                        $aSaisir = $this->getEntityManager()->getRepository('AppBundle:SouscategoriePasSaisir')
                            ->aSaisir($dossier, $souscategorie);
                        $souscategorieId = intval($souscategorie->getId());
                        $nature = 0;
                        if($aSaisir && $souscategorie->getActif() == 1){
                            if($souscategorieId == 5 || $souscategorieId == 7 || $souscategorieId == 941){
                                $nature = ($souscategorieId == 5) ? 2 : 1;
                                if($souscategorieId == 5 ) $nature = 2;
                                if($souscategorieId == 7 ) $nature = 1;
                                if($souscategorieId == 941 ) $nature = 3;
                                $banqueObManquantes = $this->getEntityManager()->getRepository('AppBundle:Releve')
                                                           ->getReleveObManquant($value->client_id, $dossier->getId(), $value->banque_compte_id, $exercice, $nature);

                                if(count($banqueObManquantes) > 0){
                                    $isOb = false;
                                    foreach ($banqueObManquantes as $bqObMq) {
                                        $dateParMoisOb = new \DateTime($bqObMq->dateReleve);
                                        $dateParMoisOb = $dateParMoisOb->format('Y-m');
                                        if(!in_array($dateParMoisOb, $dateObExist)){
                                            if($moisOb == ''){
                                                $moisOb = $dateParMoisOb;
                                            }else{
                                                $moisOb = $moisOb.'; '.$dateParMoisOb;
                                            }
                                        }
                                        $dateObExist[] = $dateParMoisOb;
                                    }
                                    if(!in_array($souscategorie->getLibelleNew(), $libelleObExist)){
                                        array_push($dataObMq, [
                                            'libelle' => $souscategorie->getLibelleNew(),
                                            'souscatid' => Boost::boost($souscategorie->getId()),
                                            'nb' => $moisOb,
                                            'nature' => $nature
                                        ]);
                                        $libelleObExist[] = $souscategorie->getLibelleNew();
                                    }
                                }  
                            }
                        }
                    }
                    $params[$value->dossier_id]['isOk'] = ($isOb && $valueRb == 'M-1') ? 1 : 0;
                    $params[$value->dossier_id]['d_m_rb'] = $value->banque.'*'.$value->numcompte.'*'.$moisRb;
                    $params[$value->dossier_id]['d_ob_m'] = json_encode($dataObMq, true);
                    $params[$value->dossier_id]['exercice'] = $exercice;
                }
            }
        }

        foreach ($notifications as $key => $notif) {
            $dossierNotif = $notif->getDossier();
            $paramEnvoi = json_decode($notif->getParamEnvoiAuto(), true);
            if(count($paramEnvoi) > 0 && $params[$dossierNotif->getId()]['isOk'] == 0){
                $typeEmail = intval($paramEnvoi['typeEmail']);
                if($typeEmail){
                    $type = $paramEnvoi['type'];
                    $status = $paramEnvoi['status'];
                    $listeEnvoiPm = $params[$dossierNotif->getId()]['listeId'];
                    $em = $this->getEntityManager();
                    $listeEnvoiPmEntity = $this->getEntityManager()
                                               ->getRepository('AppBundle:ListeMailEnvoiAutoPm')
                                               ->find($listeEnvoiPm);
                    if($status == 1 || $status == 3){
                        $result[$key]['notif'] = $notif;
                        $result[$key]['dateEche'] = $listeEnvoiPmEntity->getDateEcheance();
                        $result[$key]['d_m_rb'] = $params[$dossierNotif->getId()]['d_m_rb'];
                        $result[$key]['d_ob_m'] = $params[$dossierNotif->getId()]['d_ob_m'];
                        $result[$key]['exercice'] = $params[$dossierNotif->getId()]['exercice'];
                    }else if($status == 2){
                        if($type == -1){
                            $result[$key]['notif'] = $notif;
                            $result[$key]['dateEche'] = $listeEnvoiPmEntity->getDateEcheance();
                            $result[$key]['d_m_rb'] = $params[$dossierNotif->getId()]['d_m_rb'];
                            $result[$key]['d_ob_m'] = $params[$dossierNotif->getId()]['d_ob_m'];
                            $result[$key]['exercice'] = $params[$dossierNotif->getId()]['exercice'];
                        }else if($type == 'le'){
                            $dateFinFormat = $params[$dossierNotif->getId()]['dateFin'];
                            $dateNowFormat = $now->format('Y-m-d');
                            $dateFinFormat = $dateFinFormat->format('Y-m-d');
                            $timestampNow = strtotime($dateNowFormat); 
                            $timestampDateFin = strtotime($dateFinFormat); 
                            $recurrence = $params[$dossierNotif->getId()]['recurrence'];
                            if ($timestampNow <= $timestampDateFin){
                                if($recurrence){
                                    if($listeEnvoiPmEntity){
                                        $dateNow = clone $now;
                                        $newDateEnvoi = $dateNow->add(new \DateInterval('P'.$recurrence.'M'));
                                        $listeEnvoiPmEntity->setDate($newDateEnvoi);
                                        $em->flush();
                                        $result[$key]['notif'] = $notif;
                                        $result[$key]['dateEche'] = $listeEnvoiPmEntity->getDateEcheance();
                                        $result[$key]['d_m_rb'] = $params[$dossierNotif->getId()]['d_m_rb'];
                                        $result[$key]['d_ob_m'] = $params[$dossierNotif->getId()]['d_ob_m'];
                                        $result[$key]['exercice'] = $params[$dossierNotif->getId()]['exercice'];
                                        //date + recurrence
                                    }
                                }
                            }
                        }else if($type == 'apres'){
                            $terminer = $params[$dossierNotif->getId()]['terminer'];
                            $recurrence = $params[$dossierNotif->getId()]['recurrence'];
                            if($recurrence && $terminer > 0){
                                if($listeEnvoiPmEntity){
                                    $dateNow = clone $now;
                                    $newEnd = intval($terminer) - 1;
                                    $newDateEnvoi = $dateNow->add(new \DateInterval('P'.$recurrence.'M'));
                                    $listeEnvoiPmEntity->setDate($newDateEnvoi);
                                    $listeEnvoiPmEntity->setTerminer($newEnd);
                                    $em->flush();
                                    $result[$key]['notif'] = $notif;
                                    $result[$key]['dateEche'] = $listeEnvoiPmEntity->getDateEcheance();
                                    $result[$key]['d_m_rb'] = $params[$dossierNotif->getId()]['d_m_rb'];
                                    $result[$key]['d_ob_m'] = $params[$dossierNotif->getId()]['d_ob_m'];
                                    $result[$key]['exercice'] = $params[$dossierNotif->getId()]['exercice'];
                                    //terminer -1
                                    //date + recurrence
                                }
                            }
                        }else if($type == 'jamais'){
                            $terminer = $params[$dossierNotif->getId()]['terminer'];
                            $recurrence = $params[$dossierNotif->getId()]['recurrence'];
                            if($recurrence && $terminer == -1){
                                if($listeEnvoiPmEntity){
                                    $dateNow = clone $now;
                                    $newDateEnvoi = $dateNow->add(new \DateInterval('P'.$recurrence.'M'));
                                    $listeEnvoiPmEntity->setDate($newDateEnvoi);
                                    $em->flush();
                                    $result[$key]['notif'] = $notif;
                                    $result[$key]['dateEche'] = $listeEnvoiPmEntity->getDateEcheance();
                                    $result[$key]['d_m_rb'] = $params[$dossierNotif->getId()]['d_m_rb'];
                                    $result[$key]['d_ob_m'] = $params[$dossierNotif->getId()]['d_ob_m'];
                                    $result[$key]['exercice'] = $params[$dossierNotif->getId()]['exercice'];
                                    //date + recurrence
                                }
                            }
                        }
                    }
                }else{
                    $result[$key]['notif'] = $notif;
                    $result[$key]['dateEche'] = null;
                    $result[$key]['d_m_rb'] = $params[$dossierNotif->getId()]['d_m_rb'];
                    $result[$key]['d_ob_m'] = $params[$dossierNotif->getId()]['d_ob_m'];
                    $result[$key]['exercice'] = $params[$dossierNotif->getId()]['exercice'];
                }
            }
        }

        return $result;
    }

    public function datesBetweenWithInterval(\DateTime $start, \DateTime $end, $interval = 'D',$iteration = 1)
    {
        $periodInt = new \DateInterval('P'.$iteration.$interval);
        $datePeriodes = new \DatePeriod($start, $periodInt ,$end);
        $dates = array();
        foreach($datePeriodes as $date)
        {
            array_push($dates,$date->setTime(0,0,0));
        }
        return $dates;
    }
    
    public function getBetweenDate($start, $end)
    {
        $time1 = strtotime($start);
        $time2 = strtotime($end);
        $my = date('mY', $time2);
        $months = array(date('Y-m', $time1));
        while ($time1 < $time2) {
            $time1 = strtotime(date('Y-m', $time1) . ' +1 month');
            if (date('mY', $time1) != $my && ($time1 < $time2))
                $months[] = date('Y-m', $time1);
        }
        $months[] = date('Y-m', $time2);
        return $months;
    }
}