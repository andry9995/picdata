<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 02/05/2016
 * Time: 14:17
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Site;
use AppBundle\Entity\Tache;
use Doctrine\ORM\EntityRepository;
use GeneralBundle\Controller\Functions;

class TacheRepository extends EntityRepository {

    /**
     * @return Tache[]
     */
  public function getAllTache() {

    $taches = $this->getEntityManager()->getRepository('AppBundle:Tache')
      ->createQueryBuilder('t')
      ->select('t')
      ->leftJoin('t.tacheDomaine', 'd')
      ->addSelect('d')
      ->orderBy('t.nom', 'ASC')
      ->getQuery()
      ->getResult();
    return $taches;

  }


  public function listeClient($to_array = FALSE) {
    $clients = $this->getEntityManager()
      ->getRepository('AppBundle:Client')
      ->createQueryBuilder('c')
      ->select('c')
      ->where('c.status = :status')
      ->setParameter('status', 1)
      ->orderBy('c.nom')
      ->getQuery();
    if ($to_array) {
      return $clients->getArrayResult();
    }
    return $clients->getResult();
  }

  public function listeSite(Client $client, $to_array = FALSE) {
    $clients = $this->getEntityManager()
      ->getRepository('AppBundle:Site')
      ->createQueryBuilder('s')
      ->select('s')
      ->where('s.status = :status')
      ->andWhere('s.client = :client')
      ->setParameter('status', 1)
      ->setParameter('client', $client)
      ->orderBy('s.nom')
      ->getQuery();
    if ($to_array) {
      return $clients->getArrayResult();
    }
    return $clients->getResult();
  }

  public function listeDossier(Site $site, $to_array = FALSE) {
    $clients = $this->getEntityManager()
      ->getRepository('AppBundle:Dossier')
      ->createQueryBuilder('d')
      ->select('d')
      ->where('d.status = :status')
      ->andWhere('s.site = :site')
      ->setParameter('status', 1)
      ->setParameter('site', $site)
      ->orderBy('s.nom')
      ->getQuery();
    if ($to_array) {
      return $clients->getArrayResult();
    }
    return $clients->getResult();
  }

  public function getUtilisateurParClient(Client $client, $to_array = FALSE) {
    $utilisateurs = $this->getEntityManager()
      ->getRepository('AppBundle:Utilisateur')
      ->createQueryBuilder('u')
      ->select('u')
      ->where('u.client = :client')
      ->setParameter('client', $client)
      ->orderBy('u.login')
      ->getQuery();
    if ($to_array) {
      return $utilisateurs->getArrayResult();
    }
    return $utilisateurs->getResult();
  }

  public function getTachesPourGestionTaches($dossiers, $periode,
                                             $isLegale = true, $isLibre = true,
                                             $isScriptura = true, $isEc = true, $isCf = true, $client = null){

    $annee = intval($periode->format('Y'));
    $mois = intval($periode->format('m'));
    $debut = new \DateTime($annee.'-01-01');
    if($annee == 2019)
      $debut = new \DateTime($annee.'-08-01');
    $debut->setTime(0,0,0);
    $fin = new \DateTime($annee.'-12-31');
    $fin->add(new \DateInterval('P3M'));
    $fin->setTime(23,59,59);
    $isDepasse = false;
    $jourFeries = null;
    $tachesSynchros = null;
    //$fin->add(new \DateInterval('P1M'));

    if($client){
      $jourFeries = $this->getEntityManager()->getRepository('AppBundle:JourFerie')->findAll();
      $tachesSynchros = $this->getEntityManager()->getRepository('AppBundle:TachesSynchro')
                ->getTachesSynchroForClients($client->getId(),$debut,$fin,$isLegale,$isLibre);
    }

    if($tachesSynchros == null){
      $tachesSynchros = $this->getEntityManager()->getRepository('AppBundle:TachesSynchro')
          ->getTachesSynchroForDossiers($dossiers,$debut,$fin,$isLegale,$isLibre);

      if(count($tachesSynchros) == 0){
        $isDepasse = true;
        $debut = new \DateTime($annee.'-01-01');
        $tachesSynchros = $this->getEntityManager()->getRepository('AppBundle:TachesSynchro')
          ->getTachesSynchroForDossiers($dossiers,$debut,$fin,$isLegale,$isLibre);
      }
    }

    $taches = [];
    $tachesAvenir = [];
    $reponsable = 0;
    $dateNow = new \DateTime("now");
    $d2 = explode("-", $dateNow->format('Y-m-d')); 
    foreach ($tachesSynchros as $key => $ts)
    {
      /** @var Dossier $dossier */
      $dossier = $ts->getDossier();
      $date = $ts->getDate();

      $tachesSynchroMoov = $this->getEntityManager()->getRepository('AppBundle:TachesSynchroMoov')
          ->getLastMoov($ts);

      if ($tachesSynchroMoov) $date = $tachesSynchroMoov->getDate();

      $titre = $dossier->getNom() . ' : ';
      $titre2 = '';
      if ($ts->getTachesDate() && $ts->getTachesDate()->getTachesAction())
      {
        $nomTache = $ts->getTachesDate()->getTachesAction()->getTacheListeAction()->getNom();
        $titre .= $nomTache . ' - ' .
            $ts->getTachesDate()->getTachesAction()->getTachesItem()->getTaches()->getNom();

        $tachesEntity = $this->getEntityManager()->getRepository('AppBundle:TachesEntity')
            ->findOneBy([
                'dossier' => $ts->getDossier(),
                'tachesDate' => $ts->getTachesDate()
            ]);

        if ($tachesEntity)
        {
            if (
                !$isScriptura && $tachesEntity->getResponsable() == 0 ||
                !$isEc && $tachesEntity->getResponsable() == 1 ||
                !$isCf && $tachesEntity->getResponsable() == 2) continue;
        }

        $titre2 = $ts->getTachesDate()->getTachesAction()->getCode();
        $titre2 .= '*'.$dossier->getNom().'*';

        if ($tachesEntity && $tachesEntity->getResponsable())
            $titre2 .= 'XX';
        else
        {
            $client = $dossier->getSite()->getClient();

            if ($client->getTypeClient() == 0) $titre2 .= substr($client->getNom(),0,2);
            else $titre2 .= substr($dossier->getNom(),0,2);
        }
        if($tachesEntity)
          $reponsable = $tachesEntity->getResponsable();
      }
      else
      {
        if($ts->getTachesLibreDate() && $ts->getTachesLibreDate()->getTachesLibre()){
          $tachesLibre = $ts->getTachesLibreDate()->getTachesLibre();
          if ($tachesLibre->getTachesLibre()) $tachesLibre = $tachesLibre->getTachesLibre();
          $nomTache = $tachesLibre->getTache()->getNom();
          $titre .= $nomTache;

          $titre2 = $nomTache . '*' . $dossier->getNom() . '*';

          if ($ts->getTachesLibreDate() && $ts->getTachesLibreDate() && $ts->getTachesLibreDate()->getTachesLibre())
          {
              if (
                  !$isScriptura && $ts->getTachesLibreDate()->getTachesLibre()->getResponsable() == 0 ||
                  !$isEc && $ts->getTachesLibreDate()->getTachesLibre()->getResponsable() == 1 ||
                  !$isCf && $ts->getTachesLibreDate()->getTachesLibre()->getResponsable() == 2) continue;

              if ($ts->getTachesLibreDate()->getTachesLibre()->getResponsable() == 0)
                  $titre2 .= 'XX';
              else
              {
                  $client = $dossier->getSite()->getClient();

                  if ($client->getTypeClient() == 0) $titre2 .= substr($client->getNom(),0,2);
                  else $titre2 .= substr($dossier->getNom(),0,2);
              }
              $reponsable = $ts->getTachesLibreDate()->getTachesLibre()->getResponsable();
          }
        }
      }

      if($jourFeries != null){
        foreach ($jourFeries as $k => $j) {
            $dateTache = $date->format('Ymd');
            $dateJ = explode('-', $j['start']);
            $dateJ = $dateJ[0].$dateJ[1].$dateJ[2];
            if($dateTache == $dateJ)
                $date->add(new \DateInterval('P1D'));
        }
      }

      $date = Functions::getNextOuvrable($date);

      if($ts->getStatus() == 0){ 
        $expirer = false;
        $d1 = explode("-", $date->format('Y-m-d')); 
        $finab = $d1[0].$d1[1].$d1[2]; 
        $auj = $d2[0].$d2[1].$d2[2]; 
        if($auj > $finab) {
          $expirer = true;
        }
        $taches[$dossier->getId()][] = [
          'titre2' => $titre2,
          'date' => $date->format('d/m/Y'),
          'titre' =>  $titre,
          'nomTache' =>  $nomTache,
          'status' =>  $ts->getStatus(),
          'datetime' => $date,
          'responsable' => $reponsable,
          'expirer' => $expirer,
          'dossierId' => $dossier->getId()
        ];
      }
      
      $tachesAvenir[$dossier->getId()][] = [
          'titre2' => $titre2,
          'date' => $date->format('d/m').'/'.($date->format('Y') + 1),
          'titre' =>  $titre,
          'nomTache' =>  $nomTache,
          'status' =>  2,
          'responsable' => $reponsable,
          'datetime' => $date,
          'expirer' => false,
          'dossierId' => $dossier->getId()
      ];
    }

    $tachesAfaire = $taches;

    if(count($taches) == 0 || $isDepasse == true)
      $tachesAfaire = $tachesAvenir;

    $date = "";
    $datetimeTache = "";
    $reponsable = "";
    $expirer = false;
    $nbExpirer = 0; 
    $abrevTache = ""; 
    $titre2 = ""; 
    $statusTva = ""; 
    $res = [];
/*    foreach ($tachesAfaire as $key => $tache) {
      $k = $key;
      $abrevTache = explode('*', $tache[$k]['titre2']);
      if(!$tache['expirer']) {
        $res[$k]['date'] = $tache[$k]['datetime']->format('d-m');
        $res[$k]['datetime'] = $tache[$k]['datetime'];
        $res[$k]['titre2'] = $tache[$k]['titre2'];
        $res[$k]['abrevTache'] = $abrevTache[0];
        $res[$k]['statusTva'] = $tache[$k]['status'];
        if($tache['responsable'] === 0){
            $reponsable = "Scriptura";
        }else if($tache['responsable'] == 1){
            $reponsable = "Cabinet";
        }else{
            $reponsable = "Client";
        }
        $res[$k]['respons_tache'] = $reponsable;
        break;
      }else{
        $expirer = true;
        $res[$k]['date'] = $tache[$k]['datetime']->format('d-m');
        $res[$k]['datetime'] = $tache[$k]['datetime'];
        $res[$k]['titre2'] = $tache[$k]['titre2'];
        $res[$k]['abrevTache'] = $abrevTache[0];
        $res[$k]['statusTva'] = $tache[$k]['status'];
        if($tache['responsable'] === 0){
            $reponsable = "Scriptura";
        }else if($tache['responsable'] == 1){
            $reponsable = "Cabinet";
        }else{
            $reponsable = "Client";
        }
        $res[$k]['respons_tache'] = $reponsable;
      }
    }
*/
    return $reuslt = [
        'taches' => $tachesAfaire,
        'detailTaches'   => $res
    ];
  }
}