<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 21/03/2018
 * Time: 16:05
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\GoogleCalendarConfig;
use AppBundle\Entity\GoogleCalendarSynchro;
use AppBundle\Entity\TacheDossier;
use AppBundle\Entity\TacheEntity;
use AppBundle\Entity\TacheEntityLegaleAction;
use AppBundle\Entity\TacheEntityLibreAction;
use AppBundle\Entity\TacheLegaleParam;
use AppBundle\Entity\TachesDate;
use AppBundle\Entity\TachesEntity;
use AppBundle\Entity\TachesLibreDate;
use AppBundle\Entity\TachesSynchro;
use AppBundle\Entity\TacheStatus;
use AppBundle\Entity\TacheSynchro;
use AppBundle\Entity\TacheSynchroMoov;
use AppBundle\Functions\GoogleCalendar;
use AppBundle\Functions\StringExtension;
use Doctrine\ORM\EntityRepository;
use GeneralBundle\Controller\Functions;
use GeneralBundle\Controller\ModelAgenda;
use Symfony\Component\Validator\Constraints\IsNull;

class CalendarRepository extends EntityRepository
{
    public function getEventsByDate(array $clients, \DateTime $periode, $nomtache, $jqgrid = 0)
    {
        $year = date('Y');
        $events = [];

        try {
            foreach ($clients as $id) {
                $client = $this->getEntityManager()
                    ->getRepository('AppBundle:Client')
                    ->find($id);
                if ($client) {
                    $tache_libres = $this->getEntityManager()
                        ->getRepository('AppBundle:TacheDossier')
                        ->listeTacheAllDossier($client, $nomtache);
                    $tache_legales = $this->getEntityManager()
                        ->getRepository('AppBundle:TacheLegale')
                        ->getAllDossiersActions($client, $nomtache);

                    /** @var TacheDossier $item */
                    foreach ($tache_libres as $item) {
                        $nom = $item->getTache()->getNom();

                        $responsable = "";
                        $responsable_client = "";
                        $responsable_scriptura = "";
                        $entite = "";

                        if ($item->getResponsableScriptura() != NULL) {
                            $responsable = strtoupper($item->getResponsableScriptura()
                                ->getPrenom());
                        }
                        if ($item->getResponsableClient() != NULL) {
                            $responsable = strtoupper($item->getResponsableClient()
                                ->getEmail());
                        }
                        if ($item->getResponsableClient()) {
                            $responsable_client = $item->getResponsableClient()
                                ->getId();
                        }
                        if ($item->getResponsableScriptura()) {
                            $responsable_scriptura = $item->getResponsableScriptura()
                                ->getId();
                        }
                        if ($item->getEntite() != NULL) {
                            if ($item->getEntite() == 1) {
                                $entite = "Scriptura";
                            } elseif ($item->getEntite() == 2) {
                                $entite = "Client";
                            }
                        }
                        if ($item->getDemarrage()) {
                            $demarrage = $item->getDemarrage()->format('d/m/Y');
                        }

                        foreach ($item->getDateList() as $date) {
                            $tmp = new \DateTime($date);
                            if ($tmp->format('w') == 6) {
                                $tmp->add(new \DateInterval('P2D'));
                            } elseif ($tmp->format('w') == 0) {
                                $tmp->add(new \DateInterval('P1D'));
                            }

                            $events[] = [
                                'id' => $item->getId(),
                                'client' => $item->getDossier()->getSite()->getClient()->getNom(),
                                'dossier_id' => $item->getDossier()->getId(),
                                'dossier' => $item->getDossier()->getNom(),
                                'tache_dossier_id' => $item->getId(),
                                'tache_legale_id' => null,
                                'tache_legale_action_id' => null,
                                'report_date' => null,
                                'title' => $nom,
                                'start' => $year . '-' . $tmp->format('m-d'),
                                'responsable' => $entite,
                                'type' => 'libre'
                            ];
                        }
                    }

                    foreach ($tache_legales as $item) {
                        /** @var \DateTime $tmp */
                        $tmp = $item['date'];
                        if ($tmp->format('w') == 6) {
                            $tmp->add(new \DateInterval('P2D'));
                        } elseif ($tmp->format('w') == 0) {
                            $tmp->add(new \DateInterval('P1D'));
                        }

                        /** @var \AppBundle\Entity\TacheLegaleAction $action */
                        $action = $item['action'];
                        $responsable = "";
                        $responsable_client = "";
                        $responsable_scriptura = "";
                        $entite = "";
                        $entite_id = "";
                        $plus_tard = 0;
                        $realiser_avant = 0;
                        $demarrage = "";

                        /** @var TacheLegaleParam[] $param */
                        $params = $this->getEntityManager()
                            ->getRepository('AppBundle:TacheLegaleParam')
                            ->getByDossierAndAction($action, $item['dossier']);
                        if (count($params) > 0) {
                            /** @var TacheLegaleParam $param */
                            $param = $params[0];
                            if ($param->getOperateur() != NULL) {
                                $responsable = strtoupper($param->getOperateur()
                                    ->getPrenom());
                            }
                            if ($param->getUtilisateur() != NULL) {
                                $responsable = strtoupper($param->getUtilisateur()
                                    ->getEmail());
                            }

                            if ($param->getUtilisateur()) {
                                $responsable_client = $param->getUtilisateur()
                                    ->getId();
                            }

                            if ($param->getOperateur()) {
                                $responsable_scriptura = $param->getOperateur()
                                    ->getId();
                            }

                            if ($param->getEntite() != NULL) {
                                $entite_id = $param->getEntite();
                                if ($param->getEntite() == 1) {
                                    $entite = "Scriptura";
                                } elseif ($param->getEntite() == 2) {
                                    $entite = "Client";
                                }
                            }

                            if ($param->getPlusTard()) {
                                $plus_tard = $param->getPlusTard();
                            }
                            if ($param->getRealiserAvant()) {
                                $realiser_avant = $param->getRealiserAvant();
                            }

                            if ($param->getDemarrage()) {
                                $demarrage = $param->getDemarrage()->format('d-m-Y');
                            }
                        }

                        $events[] = [
                            'id' => $item['action']->getId(),
                            'client' => $item['dossier']->getSite()->getClient()->getNom(),
                            'dossier_id' => $item['dossier']->getId(),
                            'dossier' => $item['dossier']->getNom(),
                            'tache_dossier_id' => null,
                            'tache_legale_id' => $item['tache']->getId(),
                            'tache_legale_action_id' => $item['action']->getId(),
                            'report_date' => null,
                            'title' => $item['tache']->getNom() . ": " . $item['action']->getNom(),
                            'start' => $tmp->format('Y-m-d'),
                            'responsable' => $entite,
                            'type' => 'legal'
                        ];
                    }
                }
            }
        } catch (\Exception $ex) {

        }
        $events = $this->CheckEventStatus($events);
        $taches = $this->FilterEvents($events, $periode, $periode);

        if ($jqgrid == 1) {
            $rows = [];
            $index = 1;
            foreach ($taches as $tache) {
                $fini = $tache['status'] == 1 ? true : false;
                $rows[] = [
                    'id' => $index . '_' . $tache['dossier_id'] . '_' . $tache['type'] . '_' . $tache['id'],
                    'cell' => [
                        json_encode([
                            'id' => null,
                            'dossier_id' => $tache['dossier_id'],
                            'tache_dossier_id' => $tache['tache_dossier_id'],
                            'tache_legale_id' => $tache['tache_legale_id'],
                            'tache_legale_action_id' => $tache['tache_legale_action_id'],
                            'date' => $tache['start'],
                            'report_date' => $tache['report_date']
                        ]),
                        $tache['client'],
                        $tache['dossier'],
                        $tache['title'],
                        '',
                        $tache['responsable'],
                        $fini ? '<i class="fa fa-check-square-o fa-lg pointer" data-fait="1"></i>' : '<i class="fa fa-lg fa-square-o pointer" data-fait="0"></i>',
                        '<i class="fa fa-calendar fa-lg event-report"></i>',
                    ]
                ];
            }
            $liste = ['rows' => $rows];
            return $liste;
        } else {
            return ['taches' => $taches];
        }
    }

    /**
     * @param array $clients
     * @param \DateTime $periode
     * @return array
     * @throws \Exception
     */
    public function getEventsClient(array $clients, \DateTime $periode)
    {
        $debut = new \DateTime($periode->format('Y-m-01'));
        $debut->setTime(0, 0);
        $em = $this->getEntityManager();
        $fin = clone $debut;
        $fin->add(new \DateInterval('P1M'));
        $fin->sub(new \DateInterval('P1D'));
        $fin->setTime(23, 59,59);
        $events = [];
        $year = date('Y');
        $google_calendar = [];


        foreach ($clients as $id) {
            $client_events = [];
            $client = $this->getEntityManager()
                ->getRepository('AppBundle:Client')
                ->find($id);
            if ($client) {
                $tache_libres = $this->getEntityManager()
                    ->getRepository('AppBundle:TacheDossier')
                    ->listeTacheAllDossier($client);
                $tache_legales = $this->getEntityManager()
                    ->getRepository('AppBundle:TacheLegale')
                    ->getAllDossiersActions($client);
                $config = $this->getEntityManager()
                    ->getRepository('AppBundle:GoogleCalendarConfig')
                    ->findOneBy(array(
                        'client' => $client,
                    ));

                /** @var TacheDossier $item */
                foreach ($tache_libres as $item) {
                    $nom = $item->getTache()->getNom();
                    foreach ($item->getDateList() as $date) {
                        $tmp = new \DateTime($date);
                        if ($tmp->format('w') == 6) {
                            $tmp->add(new \DateInterval('P2D'));
                        } elseif ($tmp->format('w') == 0) {
                            $tmp->add(new \DateInterval('P1D'));
                        }

                        $events[] = [
                            'title' => $nom,
                            'start' => $year . '-' . $tmp->format('m-d'),
                            'dossier_id' => $item->getDossier()->getId(),
                            'dossier' => $item->getDossier()->getNom(),
                            'tache_dossier_id' => $item->getId(),
                            'tache_legale_id' => null,
                            'tache_legale_action_id' => null,
                            'report_date' => null,
                        ];
                        $client_events[] = [
                            'title' => $nom,
                            'start' => $year . '-' . $tmp->format('m-d'),
                            'dossier_id' => $item->getDossier()->getId(),
                            'dossier' => $item->getDossier()->getNom(),
                            'tache_dossier_id' => $item->getId(),
                            'tache_legale_id' => null,
                            'tache_legale_action_id' => null,
                            'report_date' => null,
                        ];
                    }
                }

                foreach ($tache_legales as $item) {
                    /** @var \DateTime $tmp */
                    $tmp = $item['date'];
                    if ($tmp->format('w') == 6) {
                        $tmp->add(new \DateInterval('P2D'));
                    } elseif ($tmp->format('w') == 0) {
                        $tmp->add(new \DateInterval('P1D'));
                    }
                    $events[] = [
                        'title' => $item['tache']->getNom(),
                        'start' => $tmp->format('Y-m-d'),
                        'dossier_id' => $item['dossier']->getId(),
                        'dossier' => $item['dossier']->getNom(),
                        'tache_dossier_id' => null,
                        'tache_legale_id' => $item['tache']->getId(),
                        'tache_legale_action_id' => $item['action']->getId(),
                        'report_date' => null,
                    ];
                    $client_events[] = [
                        'title' => $item['tache']->getNom(),
                        'start' => $tmp->format('Y-m-d'),
                        'dossier_id' => $item['dossier']->getId(),
                        'dossier' => $item['dossier']->getNom(),
                        'tache_dossier_id' => null,
                        'tache_legale_id' => $item['tache']->getId(),
                        'tache_legale_action_id' => $item['action']->getId(),
                        'report_date' => null,
                    ];
                }
                if ($config && $config->getIdentifiant() && trim($config->getIdentifiant()) != "") {
                    $calendar = new GoogleCalendar();
                    $calendar->setConfig($config);
                    $calendar->setTimeMin($debut);
                    $calendar->setTimeMax($fin);
                    $listes = $calendar->getCalendar();
                    $listes = array_map(function($arr) use ($id) {
                        $arr['client'] = $id;
                        return $arr;
                    }, $listes);

                    $google_calendar = array_merge($google_calendar, $listes);

                    $client_events = $this->FilterEvents($client_events, $debut, $fin);
                    $client_events = $this->CheckEventStatus($client_events);
                    $client_events = $this->GroupEvents($client_events, $debut, $fin, $client);

                    if ($config->isSendToGoogle()) {
                        foreach ($client_events as $client_event) {
                            $test = $this->getEntityManager()
                                ->getRepository('AppBundle:GoogleCalendarSynchro')
                                ->createQueryBuilder('gcs')
                                ->select('gcs')
                                ->innerJoin('gcs.client', 'client')
                                ->where('gcs.originalTitle = :original_title')
                                ->andWhere('gcs.start = :start')
                                ->andWhere('gcs.end = :end')
                                ->andWhere('client = :client')
                                ->setParameters(array(
                                    'original_title' => $client_event['original_title'],
                                    'start' => new \DateTime($client_event['start']),
                                    'end' => new \DateTime($client_event['start']),
                                    'client' => $client,
                                ))
                                ->getQuery()
                                ->getResult();
                            if (count($test) == 0) {
                                $new_event = $calendar->createEvent($client_event['gc_title'], $client_event['dossiers'], new \DateTime($client_event['start']));
                                if ($new_event) {
                                    $gcal_syncrho = new GoogleCalendarSynchro();
                                    $gcal_syncrho->setClient($client)
                                        ->setIdentifiant($new_event->getId())
                                        ->setTitle($new_event->getSummary())
                                        ->setOriginalTitle($client_event['original_title'])
                                        ->setDescription($new_event->getDescription())
                                        ->setStart(new \DateTime($new_event->getStart()->getDate()))
                                        ->setEnd(new \DateTime($new_event->getEnd()->getDate()));
                                    $em->persist($gcal_syncrho);
                                    $em->flush();
                                }
                            } else {
                                /** @var GoogleCalendarSynchro $the_event */
                                $the_event = $test[0];

                                /** Supprimer les taches doublons */
                                if (count($test) > 1) {
                                    for ($i = 1; $i < count($test); $i++) {
                                        $em->remove($test[$i]);
                                    }
                                    $em->flush();
                                }
                                $shouldUpdate = false;
                                if ($the_event->getTitle() != $client_event['gc_title'] ||
                                    $the_event->getDescription() != $client_event['dossiers']) {
                                    $shouldUpdate = true;
                                }
                                if ($shouldUpdate) {
                                    $the_event->setTitle($client_event['gc_title'])
                                        ->setDescription($client_event['dossiers']);
                                    $em->flush();

                                    /** Mettre à jour Google Calendrier */
                                    $calendar->updateEvent($the_event->getIdentifiant(), $client_event['gc_title'], $client_event['dossiers']);
                                }
                            }
                        }
                    }
                }
            }
        }
        $results = $this->FilterEvents($events, $debut, $fin);
        $results = $this->CheckEventStatus($results);
        $results = $this->GroupEvents($results, $debut, $fin);
        return ['taches' => $results, 'gcal' => $google_calendar];
    }

    private function GroupEvents($events, \DateTime $debut, \DateTime $fin, $client = null)
    {
        $taches = [];
        foreach ($events as $event) {
            if (!empty($event['report_date'])) {
                $start = $event['report_date'];
            } else {
                $start = $event['start'];
            }
            if (!isset($taches[$event['title']][$start]['dossiers']) ||
                !in_array($event['dossier'], $taches[$event['title']][$start]['dossiers'])) {
                if (isset($taches[$event['title']][$start])) {
                    $taches[$event['title']][$start]['nb'] += 1;
                } else {
                    $taches[$event['title']][$start]['nb'] = 1;
                }

                $taches[$event['title']][$start]['dossiers'][] = $event['dossier'];
                $taches[$event['title']][$start]['dossiers_status'][] = [
                    'dossier' => $event['dossier'],
                    'status' => $event['status'],
                ];
            }
        }

        $results = [];

        foreach ($taches as $title => $item) {
            foreach ($item as $start => $data) {
                $dossiers = array_map(function($arr) {
                    if ($arr['status'] && $arr['status'] == 1) {
                        return '* <del>' . $arr['dossier'] . '</del> (Fait)';
                    } else {
                        return '* ' . $arr['dossier'];
                    }
                }, $data['dossiers_status']);
                $results[] = [
                    'title' => " " . $title . ": " . $data['nb'] . " - images: " . $this->getNbImages(),
                    'gc_title' => "S- " . $title . ": " . $data['nb'],
                    'client' => $client,
                    'dossiers' => implode("<br>", $dossiers),
                    'original_title' => $title,
                    'start' => $start,
                    'type' => 'local'
                ];
            }
        }

        return array_values($results);
    }

    private function FilterEvents($events, \DateTime $debut, \DateTime $fin)
    {
        $results = array_filter($events, function ($event) use ($debut, $fin) {
            if (!empty($event['report_date'])) {
                $start = new \DateTime($event['report_date']);
            } else {
                $start = new \DateTime($event['start']);
            }
            $start->setTime(0, 0);

            return $start >= $debut && $start <= $fin;
        });

        return array_values($results);
    }

    private function CheckEventStatus($events)
    {
        $results = array_map(function($event) {
            /** @var TacheStatus $status */
            $status = $this->getEntityManager()
                ->getRepository('AppBundle:TacheStatus')
                ->getByParam([
                    'id' => null,
                    'dossier_id' => $event['dossier_id'],
                    'tache_dossier_id' => $event['tache_dossier_id'],
                    'tache_legale_id' => $event['tache_legale_id'],
                    'tache_legale_action_id' => $event['tache_legale_action_id'],
                    'date' => $event['start'],
                    'report_date' => null,
                ]);
            if ($status) {
                if ($status->getReportDate()) {
                    $event['report_date'] = $status->getReportDate()->format('Y-m-d');
                }
                $event['status'] = $status->getStatus();
            } else {
                $event['status'] = 0;
            }
            return $event;
        }, $events);
        return $results;
    }

    private function getNbImages()
    {
        return 0;
    }

    public function getTachesClientsNoUpdate($clients = [],\DateTime $periode)
    {
        $idGoogleDefault = 'NONE';
        $codeScriptura = '#S: ';
        $annee = intval($periode->format('Y'));
        $mois = intval($periode->format('m'));
        $debut = new \DateTime($annee.'-'.$mois.'-01');
        $debut->setTime(0,0,0);
        $debut->sub(new \DateInterval('P1M'));
        $mois++;
        if ($mois == 13)
        {
            $mois = 1;
            $annee++;
        }
        $fin = new \DateTime($annee.'-'.$mois.'-01');
        $fin->setTime(0,0,0);
        $fin->add(new \DateInterval('P1M'));

        $tachesSynchros = $this->getEntityManager()->getRepository('AppBundle:TacheSynchro')
            ->getExactByClient($debut,$fin,$clients);

        $taches = [];
        foreach ($tachesSynchros as $key => $ts)
        {
            /** @var TacheSynchro $tacheSynchro */
            $tacheSynchro = $ts->ts;
            /** @var TacheSynchroMoov $tacheSynchroMoov */
            $tacheSynchroMoov = $ts->tsm;
            /** @var Dossier $dossier */
            $dossier = $tacheSynchro->getDossier();
            /** @var TacheEntityLibreAction  $tacheEntityLibreAction */
            $tacheEntityLibreAction = $tacheSynchro->getTacheEntityLibreAction();
            /** @var TacheEntityLegaleAction $tacheEntityLegaleAction */
            $tacheEntityLegaleAction = $tacheSynchro->getTacheEntityLegaleAction();

            $titre = $dossier->getNom() . ' : ';
            if (is_null($tacheEntityLibreAction))
            {
                $nomTache = $tacheEntityLegaleAction->getTacheLegaleAction()->getTacheLegale()->getNom();
                $titre .= $nomTache . ' - ' .
                    $tacheEntityLegaleAction->getTacheLegaleAction()->getTacheListeAction()->getNom();
            }
            else
            {
                $nomTache = $tacheEntityLibreAction->getTacheEntity()->getTache()->getNom();
                $titre .= $nomTache;
            }

            $taches[] = ModelAgenda::Tache(
                $dossier,
                $tacheSynchro,
                $codeScriptura.$titre,
                (is_null($tacheSynchroMoov)) ? $tacheSynchro->getDate() : $tacheSynchroMoov->getDate(),
                0,
                $titre,
                $dossier->getNom(),
                $nomTache,
                ($tacheSynchro->getIdGoogle() != $idGoogleDefault) ? $tacheSynchro->getIdGoogle() : ($idGoogleDefault . $dossier->getId() . $key)
            );
        }

        $tachesGCalArray = [];
        foreach ($clients as $client)
        {
            $googleClients = [];
            $googleClients['gcal'] = [];
            /** @var GoogleCalendarConfig $config */
            $config = $this->getEntityManager()
                ->getRepository('AppBundle:GoogleCalendarConfig')
                ->getConfig($client);

            if ($config)
            {
                $calendar = new GoogleCalendar();
                $calendar->setConfig($config);
                $calendar->setTimeMin($debut);
                $calendar->setTimeMax($fin);

                $googleClientTemps = $calendar->getCalendar();
                foreach ($googleClientTemps as &$googleClientTemp)
                {
                    if (substr($googleClientTemp['title'],0,strlen($codeScriptura)) != $codeScriptura)
                    {
                        $dossierTache = $this->getDossierTacheByTitle($client,$googleClientTemp['title']);

                        /** @var Dossier $dossierTacheLibre */
                        $dossierTacheLibre = null;
                        if ($dossierTache) $dossierTacheLibre = $dossierTache->dossier;
                        $googleClientTemp['dossier'] = ($dossierTacheLibre) ? $dossierTacheLibre->getId() : 0;
                        $googleClients['gcal'][] = $googleClientTemp;
                    }
                }
            }

            $tachesGCalArray = array_merge($tachesGCalArray, $googleClients['gcal']);
        }

        return ['taches'=>$taches,'gcal'=>$tachesGCalArray];
    }

    /**
     * @param array $clients
     * @param \DateTime $periode
     * @param Dossier|null $dossier
     * @return array
     */
    public function getTachesClients($clients = [], \DateTime $periode,Dossier $dossier = null)
    {
        $idGoogleDefault = 'NONE';
        $codeScriptura = '#S: ';
        $debut = new \DateTime($periode->format('Y-01-01'));
        $debut->setTime(0, 0,0);
        $fin = new \DateTime($periode->format('Y-12-31'));
        $fin->setTime(23,59,59);

        $em = $this->getEntityManager();
        $annee = intval($debut->format('Y'));

        $tacheEntitys = $this->getEntityManager()->getRepository('AppBundle:TacheEntity')
            ->getAndGroupByClient($clients);

        /** @var GoogleCalendar $calendar */
        $calendar = null;
        $taches = [];
        $tachesGCalArray = [];
        foreach ($tacheEntitys as $keyClient => $tacheEntityClients)
        {
            $client = $this->getEntityManager()->getRepository('AppBundle:Client')->find($keyClient);
            /** @var GoogleCalendarConfig $config */
            $config = $this->getEntityManager()
                ->getRepository('AppBundle:GoogleCalendarConfig')
                ->getConfig($client);

            $tacheScripturas = [];
            $googleClients = [];
            $googleClients['scr'] = [];
            $googleClients['gcal'] = [];
            $prioriteDossiers = [];
            if ($config)
            {
                $calendar = new GoogleCalendar();
                $calendar->setConfig($config);
                $calendar->setTimeMin($debut);
                $calendar->setTimeMax($fin);
                $googleClientTemps = $calendar->getCalendar();

                foreach ($googleClientTemps as &$googleClientTemp)
                {
                    if (substr($googleClientTemp['title'],0,strlen($codeScriptura)) == $codeScriptura)
                        $googleClients['scr'][$googleClientTemp['id']] = $googleClientTemp;
                    else
                    {
                        $dossierTache = $this->getDossierTacheByTitle($client,$googleClientTemp['title']);

                        /** @var Dossier $dossierTacheLibre */
                        $dossierTacheLibre = null;
                        if ($dossierTache) $dossierTacheLibre = $dossierTache->dossier;
                        $googleClientTemp['dossier'] = ($dossierTacheLibre) ? $dossierTacheLibre->getId() : 0;
                        $googleClients['gcal'][] = $googleClientTemp;

                        $dateT = \DateTime::createFromFormat('Y-m-d',$googleClientTemp['start']);
                        $dateT->setTime(0,0,0);
                        if ($dateT < (new \DateTime())->setTime(0,0,0)) continue;

                        if ($dossierTache)
                        {
                            $keyDossier = $dossierTache->dossier->getId();
                            if (array_key_exists($keyDossier,$prioriteDossiers))
                            {
                                if ($prioriteDossiers[$keyDossier] > $dateT)
                                {
                                    $prioriteDossiers[$keyDossier] = (object)
                                    [
                                        'd' => clone  $dateT,
                                        'id' => [$googleClientTemp['id']],
                                        't' => 0
                                    ];
                                }
                            }
                            else
                            {
                                $prioriteDossiers[$keyDossier] = (object)
                                [
                                    'd' => clone  $dateT,
                                    'id' => $googleClientTemp['id'],
                                    't' => 0
                                ];
                            }
                        }
                    }
                }
            }

            $index = 0;
            /** @var TacheEntity $tacheEntity */
            foreach ($tacheEntityClients as $tacheEntity)
            {
                if (!is_null($tacheEntity->getTacheLegale()))
                {
                    $dossierTacheDates = $this->getEntityManager()->getRepository('AppBundle:TacheEntityLegaleAction')->getTacheEntityLegaleActions($tacheEntity,$annee);
                }
                else
                {
                    $dossierTacheDates = $this->getEntityManager()->getRepository('AppBundle:TacheEntityLibreAction')->getTacheEntityLibreActions($tacheEntity,$annee);
                }

                foreach ($dossierTacheDates as $dossierTacheDate)
                {
                    /** @var Dossier $dossier */
                    $dossier = $dossierTacheDate->dossier;
                    /** @var TacheEntityLegaleAction $tacheEntityLegaleAction */
                    $tacheEntityLegaleAction = null;
                    /** @var TacheEntityLibreAction $tacheEntityLibreAction */
                    $tacheEntityLibreAction = null;

                    if (!is_null($tacheEntity->getTacheLegale()))
                        $tacheEntityLegaleAction = $dossierTacheDate->tacheEntityLegaleAction;
                    else
                        $tacheEntityLibreAction = $dossierTacheDate->tacheEntityLibreAction;

                    /** @var \DateTime[] $listeDate */
                    $listeDate = $dossierTacheDate->liste;

                    foreach ($listeDate as $key => $d_)
                    {
                        $d = clone $d_;

                        if ($d > $fin || $d < $debut) continue;
                        /** @var TacheSynchro $tacheSynchro */
                        $tacheSynchro = $this->getEntityManager()->getRepository('AppBundle:TacheSynchro')
                            ->findOneByDate($dossier,$d,$tacheEntityLegaleAction,$tacheEntityLibreAction);

                        $titre = $codeScriptura.$dossier->getNom() . ' : ';
                        if (is_null($tacheEntityLibreAction))
                        {
                            $nomTache = $tacheEntityLegaleAction->getTacheLegaleAction()->getTacheLegale()->getNom();
                            $titre .= $nomTache . ' - ' .
                                $tacheEntityLegaleAction->getTacheLegaleAction()->getTacheListeAction()->getNom();
                        }
                        else
                        {
                            $nomTache = $tacheEntityLibreAction->getTacheEntity()->getTache()->getNom();
                            $titre .= $nomTache;
                        }

                        if (is_null($tacheSynchro))
                        {
                            $tacheSynchro = new TacheSynchro();
                            $tacheSynchro
                                ->setDossier($dossier)
                                ->setDate($d)
                                ->setTacheEntityLegaleAction($tacheEntityLegaleAction)
                                ->setTacheEntityLibreAction($tacheEntityLibreAction);

                            if ($config && $config->isSendToGoogle())
                            {
                                $newEvent = $calendar->createEvent($titre, $nomTache, $d);
                                if ($newEvent)
                                    $tacheSynchro->setIdGoogle($newEvent->getId());
                            }
                            else $tacheSynchro->setIdGoogle($idGoogleDefault);
                            $em->persist($tacheSynchro);
                            $em->flush();
                        }
                        else
                        {
                            /** @var TacheSynchroMoov $lastMoov */
                            $lastMoov = $this->getEntityManager()->getRepository('AppBundle:TacheSynchroMoov')->getLastMoov($tacheSynchro);
                            if ($lastMoov)
                            {
                                $d = clone $lastMoov->getDate();
                            }

                            if (array_key_exists($tacheSynchro->getIdGoogle(),$googleClients['scr']))
                            {
                                if (trim($googleClients['scr'][$tacheSynchro->getIdGoogle()]['title']) != trim($titre))
                                    $calendar->updateEvent($tacheSynchro->getIdGoogle(),$titre,$nomTache);

                                if ($googleClients['scr'][$tacheSynchro->getIdGoogle()]['start'] != $d->format('Y-m-d'))
                                {
                                    $oldId = $tacheSynchro->getIdGoogle();
                                    $eventUpdated = $calendar->updateDateEvent($tacheSynchro->getIdGoogle(),$d);

                                    if ($eventUpdated)
                                    {
                                        $googleClients['scr'][$eventUpdated['id']] = $eventUpdated;
                                        $tacheSynchro->setIdGoogle($eventUpdated['id']);
                                        $em->flush();
                                        unset($googleClients['scr'][$oldId]);
                                    }
                                }
                            }
                        }

                        /*if (($config && !$config->isSendToGoogle()) || !$config)
                        {
                            $d = clone $d_;
                        }*/

                        if ($config && $config->isSendToGoogle() && $tacheSynchro->getIdGoogle() == $idGoogleDefault)
                        {
                            $trouve = false;
                            foreach ($googleClients['scr'] as $keyGoogle => $googleClient)
                            {
                                if ($tacheSynchro->getDate()->format('Y-m-d') == $googleClient['start'] && $titre == $googleClient['title'])
                                {
                                    $trouve = true;
                                    $tacheSynchro->setIdGoogle($keyGoogle);
                                }
                            }

                            if (!$trouve)
                            {
                                $newEvent = $calendar->createEvent($titre, $nomTache, $d);
                                if ($newEvent)
                                {
                                    $tacheSynchro->setIdGoogle($newEvent->getId());
                                    if (!array_key_exists($tacheSynchro->getIdGoogle(),$googleClients['scr']))
                                        $googleClients['scr'][$tacheSynchro->getIdGoogle()] = $newEvent;
                                }
                            }

                            $em->flush();
                        }

                        $now = new \DateTime();
                        $now->setTime(0,0,0);
                        if ($d >= $now )
                        {
                            $keyDossier = $dossier->getId();
                            if (array_key_exists($keyDossier,$prioriteDossiers))
                            {
                                if ($prioriteDossiers[$keyDossier] > $d)
                                {
                                    $prioriteDossiers[$keyDossier] = (object)
                                    [
                                        'd' => clone $d,
                                        'id' => $tacheSynchro,
                                        't' => 1
                                    ];
                                }
                            }
                            else
                            {
                                $prioriteDossiers[$keyDossier] = (object)
                                [
                                    'd' => clone $d,
                                    'id' => $tacheSynchro,
                                    't' => 1
                                ];
                            }
                        }

                        $tacheScripturas[((is_null($tacheSynchro) || (!is_null($tacheSynchro) && $tacheSynchro->getIdGoogle() == $idGoogleDefault)) ? ('0-'.$index) : $tacheSynchro->getIdGoogle())] = (object)
                        [
                            'd' => $d,
                            'tlegale' => $tacheEntityLegaleAction,
                            'tlibre' => $tacheEntityLibreAction,
                            'dossier' => $dossier,
                            'tsynchro' => $tacheSynchro,
                        ];
                        $index++;
                    }
                }
            }

            foreach ($googleClients['scr'] as $keyGoogle => $googleClientScr)
            {
                if ($config && (!$config->isSendToGoogle() || !array_key_exists($keyGoogle,$tacheScripturas)))
                {
                    $calendar->removeEvent($keyGoogle);
                    /** @var TacheSynchro $tacheSynchroToDelete */
                    $tacheSynchroToDelete = $this->getEntityManager()->getRepository('AppBundle:TacheSynchro')
                        ->getByIdGoogle($keyGoogle);

                    if ($tacheSynchroToDelete) $tacheSynchroToDelete->setIdGoogle($idGoogleDefault);//$em->remove($tacheSynchroToDelete);
                }
            }

            $em->flush();

            $taches = array_merge($taches, $tacheScripturas);
            $tachesGCalArray = array_merge($tachesGCalArray, $googleClients['gcal']);

            $this->getEntityManager()->getRepository('AppBundle:TachePrioriteDossier')
                ->updateTachePriority($client,$prioriteDossiers);
        }

        // Tache scriptura
        $tachesArray = [];
        foreach ($taches as $key => $tach)
        {
            /** @var TacheEntityLegaleAction $tacheEntityLegaleAction */
            $tacheEntityLegaleAction = $tach->tlegale;
            /** @var TacheEntityLibreAction $tacheEntityLibreAction */
            $tacheEntityLibreAction = $tach->tlibre;
            /** @var Dossier $dossier */
            $dossier = $tach->dossier;

            $titre = $dossier->getNom() . ' : ';
            if (is_null($tacheEntityLibreAction))
            {
                $nomTache = $tacheEntityLegaleAction->getTacheLegaleAction()->getTacheLegale()->getNom();
                $titre .= $nomTache . ' - ' .
                    $tacheEntityLegaleAction->getTacheLegaleAction()->getTacheListeAction()->getNom();
            }
            else
            {
                $nomTache = $tacheEntityLibreAction->getTacheEntity()->getTache()->getNom();
                $titre .= $nomTache;
            }

            $tachesArray[] = ModelAgenda::Tache(
                $dossier,
                $tach->tsynchro,
                $codeScriptura.$titre,
                $tach->d,
                0,
                $titre,
                $dossier->getNom(),
                $nomTache,
                ($tach->tsynchro->getIdGoogle() != $idGoogleDefault) ? $tach->tsynchro->getIdGoogle() : ($idGoogleDefault . $dossier->getId() . $key)
            );
        }

        return ['taches'=>$tachesArray,'gcal'=>$tachesGCalArray];
    }

    /**
     * @param Client $client
     * @param string $title
     * @return null|object
     */
    public function getDossierTacheByTitle(Client $client,$title = '')
    {
        $titleSpliters = explode(':', $title);
        if (count((array)$titleSpliters) < 2) return null;

        $dossierNom = trim($titleSpliters[0]);
        $tacheNom = trim($titleSpliters[1]);

        if ($dossierNom == '' || $tacheNom == '') return null;

        $dossier = $this->getEntityManager()->getRepository('AppBundle:Dossier')
            ->createQueryBuilder('d')
            ->leftJoin('d.site', 's')
            ->where('s.client = :client')
            ->andWhere('d.nom = :nom')
            ->andWhere('d.status = 1')
            ->setParameters([
                'client' => $client,
                'nom' => $dossierNom
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $tache = $this->getEntityManager()->getRepository('AppBundle:Tache')
            ->createQueryBuilder('t')
            ->where('t.nom = :nom')
            ->setParameter('nom', $tacheNom)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$dossier || !$tache) return null;

        return (object)
        [
            'dossier' => $dossier,
            'tache' => $tache
        ];
    }

    /**
     * @param Client[] $clients
     * @param \DateTime $periode
     * @return array
     */
    public function taches3Events($clients, \DateTime $periode)
    {
        $codeScriptura = '#S: ';
        $defaultIdGoogle = 'NONE';

        $em = $this->getEntityManager();

        $debut = new \DateTime($periode->format('Y-01-01'));
        $debut->setTime(0, 0,0);
        $fin = new \DateTime($periode->format('Y-12-31'));
        $fin->setTime(23,59,59);

        $googleClients['gcal'] = [];
        //$prioriteDossiers = [];

        $dateNow = new \DateTime();
        $yearNow = intval($dateNow->format('Y'));
        $yearPeriode = intval($periode->format('Y'));
        $priorites = [];

        $tachesLibresDates = $this->getEntityManager()->getRepository('AppBundle:TachesLibreDate')
            ->getTachesLibreDatesForClients($clients);
        /**
         * $tachesSynchrosLibres[id_client] = array [id_google] = tachesSynchro
         */
        $tachesClients = $this->tachesSynchrosLibres($tachesLibresDates,$periode);

        //$tachesClients = [];

        $tachesEntitys = $this->getEntityManager()->getRepository('AppBundle:TachesEntity')
            ->getTachesEntityEnabledClients($clients);

        /**
         * $tachesClients[id_client] = array [id_google] = tachesSynchro
         */
        foreach ($tachesEntitys as $tachesEntity)
        {
            $tacheObject = $this->tachesSynchros($tachesEntity,null,$periode);

            /** @var Dossier $dossier */
            $dossier = $tacheObject->dossier;
            /** @var TachesSynchro[] $tachesSynchros */
            $tachesSynchros = $tacheObject->tachesSynchros;

            $responsable = 0;
            $tResponsables = ['TVA'];
            if ($tachesEntity)
            {
                $nomTaches = trim(strtoupper($tachesEntity->getTachesDate()->getTachesAction()->getTachesItem()->getTaches()->getNom()));
                if (in_array($nomTaches,$tResponsables))
                {
                    $prestationFiscale = $this->getEntityManager()->getRepository('AppBundle:PrestationFiscale')
                        ->findOneBy(['dossier' => $dossier]);

                    if ($nomTaches == 'TVA' && $prestationFiscale && intval($prestationFiscale->getTva()) == 0)
                        $responsable = ($dossier->getSite()->getClient()->getTypeClient() == 0) ? 1 : 2;
                }
            }

            if ($responsable != $tachesEntity->getResponsable())
            $tachesEntity->setResponsable($responsable);

            $key = $dossier->getSite()->getClient()->getId();
            if (!array_key_exists($key,$tachesClients)) $tachesClients[$key] = [];
            $tachesClients[$key] = array_merge($tachesClients[$key],$tachesSynchros);
        }

        foreach ($clients as $client)
        {
            print_r($client->getNom().':'.$client->getId().'-'.$periode->format('Y').' : ');

            $tachesSynchroInDataBases = $this->getEntityManager()->getRepository('AppBundle:TachesSynchro')
                ->getTachesSynchroForClient($client,$debut,$fin);

            foreach ($tachesSynchroInDataBases as $tachesSynchroInDataBase)
            {
                $key = $tachesSynchroInDataBase->getIdGoogle() . (($tachesSynchroInDataBase->getIdGoogle() == 'NONE') ? ('-'.$tachesSynchroInDataBase->getId()) : '');
                if (!array_key_exists($key,$tachesClients[$client->getId()])) $em->remove($tachesSynchroInDataBase);
            }

            /** @var GoogleCalendarConfig $config */
            $config = $this->getEntityManager()->getRepository('AppBundle:GoogleCalendarConfig')
                ->getConfig($client);
            $scripturaTaches = [];
            $calendar = null;
            if ($config)
            {
                $calendar = new GoogleCalendar();
                $calendar->setConfig($config);
                $calendar->setTimeMin($debut);
                $calendar->setTimeMax($fin);
                $googleClientTemps = $calendar->getCalendar();

                foreach ($googleClientTemps as &$googleClientTemp)
                {
                    $isTacheScriptura = (count(explode('*',$googleClientTemp['title'])) > 1 || substr($googleClientTemp['title'],0,strlen($codeScriptura)) == $codeScriptura);
                    if ($isTacheScriptura)
                    {
                        if (!array_key_exists($googleClientTemp['id'],$tachesClients[$client->getId()]) || !$config->isSendToGoogle())
                            $calendar->removeEvent($googleClientTemp['id']);
                        else
                            $scripturaTaches[$googleClientTemp['id']] = $googleClientTemp;
                    }
                    else
                    {
                        $dossierTache = $this->getDossierTacheByTitle($client,$googleClientTemp['title']);
                        $dossierTacheLibre = null;
                        if ($dossierTache) $dossierTacheLibre = $dossierTache->dossier;
                        $googleClientTemp['dossier'] = ($dossierTacheLibre) ? $dossierTacheLibre->getId() : 0;
                        $googleClients['gcal'][] = $googleClientTemp;
                    }
                }
            }

            foreach ($tachesClients[$client->getId()] as $key => $ts)
            {
                /** @var TachesSynchro $tachesSynchro */
                $tachesSynchro = $ts;
                /** @var Dossier $dossier */
                $dossier = $tachesSynchro->getDossier();
                $dateExact = $tachesSynchro->getDate();
                if ($config)
                {
                    $titre = $codeScriptura.$tachesSynchro->getDossier()->getNom() . ' : ';
                    $titre2 = '';
                    if ($tachesSynchro->getTachesDate())
                    {
                        $titre2 = $tachesSynchro->getTachesDate()->getTachesAction()->getCode();
                        $titre2 .= '*'.((strlen($dossier->getNom()) <= 15) ? $dossier->getNom() : substr($dossier->getNom(),0,15)) .'*';

                        $tachesEntity = $this->getEntityManager()->getRepository('AppBundle:TachesEntity')
                            ->findOneBy([
                                'dossier' => $dossier,
                                'tachesDate' => $tachesSynchro->getTachesDate()
                            ]);

                        if ($tachesEntity && $tachesEntity->getResponsable())
                            $titre2 .= 'XX';
                        else
                        {
                            $client = $dossier->getSite()->getClient();

                            if ($client->getTypeClient() == 0) $titre2 .= substr($client->getNom(),0,2);
                            else $titre2 .= substr($dossier->getNom(),0,2);
                        }

                        $nomTache = $tachesSynchro->getTachesDate()->getTachesAction()->getTacheListeAction()->getNom();
                        $titre .= $nomTache . ' - ' .
                            $tachesSynchro->getTachesDate()->getTachesAction()->getTachesItem()->getTaches()->getNom();
                    }
                    else
                    {
                        $tachesLibre = $tachesSynchro->getTachesLibreDate()->getTachesLibre();
                        if ($tachesLibre->getTachesLibre()) $tachesLibre = $tachesLibre->getTachesLibre();
                        $nomTache = $tachesLibre->getTache()->getNom();
                        $titre .= $nomTache;

                        $titre2 = $nomTache . '*' . ((strlen($dossier->getNom()) <= 15) ? $dossier->getNom() : substr($dossier->getNom(),0,15)) . '*';

                        if ($tachesSynchro->getTachesLibreDate() && $tachesSynchro->getTachesLibreDate() && $tachesSynchro->getTachesLibreDate()->getTachesLibre())
                        {
                            if ($tachesSynchro->getTachesLibreDate()->getTachesLibre()->getResponsable() == 0)
                                $titre2 .= 'XX';
                            else
                            {
                                $client = $dossier->getSite()->getClient();

                                if ($client->getTypeClient() == 0) $titre2 .= substr($client->getNom(),0,2);
                                else $titre2 .= substr($dossier->getNom(),0,2);
                            }
                        }
                    }

                    if ($config->isSendToGoogle())
                    {
                        $tachesSynchroMoov = $this->getEntityManager()->getRepository('AppBundle:TachesSynchroMoov')
                            ->getLastMoov($tachesSynchro);

                        if ($tachesSynchroMoov) $dateExact = $tachesSynchroMoov->getDate();
                        if (array_key_exists($key,$scripturaTaches))
                        {
                            $dateInGoogle = \DateTime::createFromFormat('Y-m-d',$scripturaTaches[$key]['start']);

                            if ($scripturaTaches[$key]['title'] != $titre2)
                                $calendar->updateEvent($key, $titre2, $dossier->getNom());

                            if ($dateInGoogle->format('dmY') != $dateExact->format('dmY'))
                            {
                                $eventUpdated = $calendar->updateDateEvent($key,$dateExact);
                                $tachesSynchro->setIdGoogle($eventUpdated->id);
                            }
                        }
                        else
                        {
                            $newEvent = $calendar->createEvent($titre2, $nomTache, $dateExact);
                            if ($newEvent)
                            {
                                $tachesSynchro->setIdGoogle($newEvent->getId());
                            }
                        }
                    }
                    elseif ($tachesSynchro->getIdGoogle() != $defaultIdGoogle)
                    {
                        $tachesSynchro->setIdGoogle($defaultIdGoogle);
                    }
                }
            }

            $em->flush();

            /*if ($yearPeriode == $yearNow + 1)
            {
                $priorites = $this->getEntityManager()->getRepository('AppBundle:TachesPrioriteDossier')
                    ->updatePriorites([$client]);
            }*/

            print ' OK <br/> # ';
        }
        return [$priorites/*, $tachesClients,$googleClients*/];
    }

    /**
     * @param Client[] $clients
     * @param \DateTime $periode
     * @param bool $isLegale
     * @param bool $isLibre
     * @param bool $isFaite
     * @param bool $isScriptura
     * @param bool $isEc
     * @param bool $isCf
     * @return array
     */
    public function taches3EventsNoUpdates($clients, $dossier, $periode,$isLegale = true, 
                                           $isLibre = true, $isFaite = true, $isScriptura = true, 
                                           $isEc = false, $isCf = false, $nbMoisSuiv = null)
    {
        $idGoogleDefault = 'NONE';
        $codeScriptura = '#S: ';
        $annee = intval($periode->format('Y'));
        $mois = intval($periode->format('m'));
        $debut = new \DateTime($annee.'-'.$mois.'-01');
        $debut->setTime(0,0,0);
        $debut->sub(new \DateInterval('P1M'));
        $responsable = 0;
        $mois++;
        $jourFeries = null;
        $dataTitre = [];
        if ($mois == 13)
        {
            $mois = 1;
            $annee++;
        }
        $fin = new \DateTime($annee.'-'.$mois.'-01');
        if($nbMoisSuiv != null){
            $fin->add(new \DateInterval('P'.$nbMoisSuiv.'M'));
        }
        $fin->setTime(23,59,59);
        $fin->add(new \DateInterval('P1M'));

        if($dossier != null){
            $tachesSynchros = $this->getEntityManager()->getRepository('AppBundle:TachesSynchro')
                ->getTachesSynchroForDossiers($dossier,$debut,$fin,$isLegale,$isLibre,$isFaite);
        }else{
            $jourFeries = $this->getEntityManager()->getRepository('AppBundle:JourFerie')->findAll();
            $tachesSynchros = $this->getEntityManager()->getRepository('AppBundle:TachesSynchro')
                ->getTachesSynchroForClients($clients,$debut,$fin,$isLegale,$isLibre,$isFaite);
        }

        $taches = [];
        foreach ($tachesSynchros as $key => $ts)
        {
            if ($ts->getDate()->format('Y-md-') <= '2019-08-01') continue;

            /** @var Dossier $dossier */
            $dossier = $ts->getDossier();
            $date = $ts->getDate();

            $tachesSynchroMoov = $this->getEntityManager()->getRepository('AppBundle:TachesSynchroMoov')
                ->getLastMoov($ts);

            if ($tachesSynchroMoov) $date = $tachesSynchroMoov->getDate();

            $titre = $dossier->getNom() . ' : ';
            $titre2 = '';
            if ($ts->getTachesDate())
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

                $titre2 = $ts->getTachesDate()->getTachesAction()->getLibelle();
                $titre2 .= '*'.((strlen($dossier->getNom()) <= 15) ? $dossier->getNom() : substr($dossier->getNom(),0,15)).'*';

                if ($tachesEntity && $tachesEntity->getResponsable())
                    $titre2 .= 'XX';
                else
                {
                    $client = $dossier->getSite()->getClient();

                    if ($client->getTypeClient() == 0) $titre2 .= substr($client->getNom(),0,2);
                    else $titre2 .= substr($dossier->getNom(),0,2);
                }
                if($tachesEntity)
                    $responsable = $tachesEntity->getResponsable();
            }
            else
            {
                $tachesLibre = $ts->getTachesLibreDate()->getTachesLibre();
                if ($tachesLibre->getTachesLibre()) $tachesLibre = $tachesLibre->getTachesLibre();
                $nomTache = $tachesLibre->getTache()->getNom();
                $titre .= $nomTache;

                $titre2 = $nomTache . '*' . ((strlen($dossier->getNom()) <= 15) ? $dossier->getNom() : substr($dossier->getNom(),0,15)) . '*';

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
                    if($ts->getTachesLibreDate()->getTachesLibre())
                        $responsable = $ts->getTachesLibreDate()->getTachesLibre()->getResponsable();
                }
            }

            $isDepasser = 3;
            if($jourFeries != null){
                foreach ($jourFeries as $k => $j) {
                    $dateTache = $date->format('Ymd');
                    $dateJ = $j->getDate();
                    $dateJ = $dateJ->format('Ymd');
                    if($dateTache == $dateJ)
                        $date->add(new \DateInterval('P1D'));
                }
            }
            $date = Functions::getNextOuvrable($date);
            if($ts->getStatus()){
                $isDepasser = 2;
            }else{
                $dateNow = new \DateTime();
                $dn = explode("-", $dateNow->format('Y-m-d')); 
                $dateNow = $dn[0].$dn[1].$dn[2]; 
                $de = explode("-", $date->format('Y-m-d')); 
                $dateEch = $de[0].$de[1].$de[2]; 
                if($dateEch < $dateNow)
                    $isDepasser = 1;
            }

            if(!in_array(($titre2.'*'.$date->format('ymd')), $dataTitre)){
                $taches[] = ModelAgenda::Tache3(
                    $dossier,
                    $ts,
                    $titre2,
                    $date,
                    0,
                    $titre,
                    $dossier->getNom(),
                    $nomTache,
                    ($ts->getIdGoogle() != $idGoogleDefault) ? $ts->getIdGoogle() : ($idGoogleDefault .'#'. $dossier->getId() .'#'. $key),
                    '',
                    '',
                    '',
                    $isDepasser,
                    $responsable
                );
            }
            $dataTitre[] = $titre2.'*'.$date->format('ymd');
        }

        $tachesGCalArray = [];
       /* foreach ($clients as $client)
        {
            $googleClients = [];
            $googleClients['gcal'] = [];*/
            /** @var GoogleCalendarConfig $config */
           /* $config = $this->getEntityManager()
                ->getRepository('AppBundle:GoogleCalendarConfig')
                ->getConfig($client);

            if ($config)
            {
                $calendar = new GoogleCalendar();
                $calendar->setConfig($config);
                $calendar->setTimeMin($debut);
                $calendar->setTimeMax($fin);

                $googleClientTemps = $calendar->getCalendar();
                foreach ($googleClientTemps as &$googleClientTemp)
                {
                    if (count(explode('*',$googleClientTemp['title'])) <= 1)*//*substr($googleClientTemp['title'],0,strlen($codeScriptura)) != $codeScriptura*/
                   /* {
                        $dossierTache = $this->getDossierTacheByTitle($client,$googleClientTemp['title']);
*/
                        /** @var Dossier $dossierTacheLibre */
                       /* $dossierTacheLibre = null;
                        if ($dossierTache)
                        {
                            $dossierTacheLibre = $dossierTache->dossier;
                            $googleClientTemp['color'] = $dossierTacheLibre->getSite()->getClient()->getTacheColor();

                            $titleSpliters = explode(':',$googleClientTemp['title']);
                            $fait = false;
                            if (count($titleSpliters) > 2)
                                $fait = substr(strtoupper(trim($titleSpliters[2])),0,4) == 'FAIT';

                            if (!$isLibre || !$isFaite && $fait) continue;
                        }
                        $googleClientTemp['dossier'] = ($dossierTacheLibre) ? $dossierTacheLibre->getId() : 0;
                        $googleClients['gcal'][] = $googleClientTemp;
                    }
                }
            }

            $tachesGCalArray = array_merge($tachesGCalArray, $googleClients['gcal']);
        }*/

        return
            [
                'taches'=>$taches,
                'gcal'=>$tachesGCalArray
            ];
    }

    /**
     * @param TachesEntity|null $tachesEntity
     * @param TachesDate|null $td
     * @param \DateTime|null $periode
     * @return object
     */
    public function tachesSynchros(TachesEntity $tachesEntity = null,TachesDate $td = null, \DateTime $periode = null)
    {
        $debut = new \DateTime($periode->format('Y-01-01'));
        $debut->setTime(0, 0,0);
        $fin = new \DateTime($periode->format('Y-12-31'));
        $fin->setTime(23,59,59);

        $dossier = null;
        $tachesDateSelect = null;
        if ($tachesEntity)
        {
            $tachesDateSelect = $tachesEntity->getTachesDate();
            $dossier = $tachesEntity->getDossier();
        }
        elseif ($td)
        {
            $tachesDateSelect = $td;
            $dossier = $td->getDossier();
        }

        $taches = [];
        $taches[] = $this->getEntityManager()->getRepository('AppBundle:TachesDate')
            ->getDatesInYear($tachesDateSelect,$dossier,$periode);

        /** @var TachesSynchro[] $tachesSynchros */
        $tachesSynchros = [];

        foreach ($taches as $tach)
        {
            /** @var TachesDate $tachesDate */
            $tachesDate = $tach->tachesDate;
            /** @var \DateTime[] $dates */
            $dates = $tach->date;
            foreach ($dates as $date)
            {
                if ($date >= $debut && $date <= $fin)
                {
                    $tachesSynchro = $this->getEntityManager()->getRepository('AppBundle:TachesSynchro')
                        ->getTachesSynchro($dossier,$date,$tachesDate,null);
                    $key = $tachesSynchro->getIdGoogle() . (($tachesSynchro->getIdGoogle() == 'NONE') ? ('-'.$tachesSynchro->getId()) : '');
                    $tachesSynchros[$key] = $tachesSynchro;
                }
            }
        }

        return (object)
        [
            'dossier' => $dossier,
            'tds' => $taches,
            'tachesSynchros' => $tachesSynchros
        ];
    }

    public function tachesSynchrosLibres($tlds, \DateTime $periode = null)
    {
        $debut = new \DateTime($periode->format('Y-01-01'));
        $debut->setTime(0, 0,0);
        $fin = new \DateTime($periode->format('Y-12-31'));
        $fin->setTime(23,59,59);

        $tachesSynchros = [];
        foreach ($tlds as $keyClient => $tldItems)
        {
            foreach ($tldItems as $tld)
            {
                /** @var Dossier $dossier */
                $dossier = $tld->dossier;
                /** @var TachesLibreDate[] $tachesLibreDates */
                $tachesLibreDates = $tld->tachesLibreDates;

                foreach ($tachesLibreDates as $tachesLibreDate)
                {
                    $datesInYears = $this->getEntityManager()->getRepository('AppBundle:TachesLibreDate')
                        ->getDatesInYear($tachesLibreDate,$dossier,$periode);

                    foreach ($datesInYears as $date)
                    {
                        if ($date >= $debut && $date <= $fin)
                        {
                            $tachesSynchro = $this->getEntityManager()->getRepository('AppBundle:TachesSynchro')
                                ->getTachesSynchro($dossier,$date,null,$tachesLibreDate);
                            $key = $tachesSynchro->getIdGoogle() . (($tachesSynchro->getIdGoogle() == 'NONE') ? ('-'.$tachesSynchro->getId()) : '');

                            if (!array_key_exists($keyClient,$tachesSynchros)) $tachesSynchros[$keyClient] = [];
                            $tachesSynchros[$keyClient][$key] = $tachesSynchro;
                        }
                    }
                }
            }
        }

        return $tachesSynchros;
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