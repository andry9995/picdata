<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 15/01/2019
 * Time: 15:32
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\TachesDate;
use AppBundle\Entity\TachesLibreDate;
use AppBundle\Entity\TachesSynchro;
use AppBundle\Functions\CustomPdoConnection;
use AppBundle\Functions\GoogleCalendar;
use Doctrine\ORM\EntityRepository;

class TachesSynchroRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @param \DateTime $date
     * @param TachesDate|null $tachesDate
     * @param TachesLibreDate|null $tachesLibreDate
     * @return TachesSynchro
     */
    public function getTachesSynchro(Dossier $dossier,\DateTime $date, TachesDate $tachesDate = null, TachesLibreDate $tachesLibreDate = null)
    {
        $tachesSynchro = $this->createQueryBuilder('ts')
            ->where('ts.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('ts.date = :date')
            ->setParameter('date',$date);

        if ($tachesDate)
            $tachesSynchro = $tachesSynchro
                ->andWhere('ts.tachesDate = :tachesDate')
                ->setParameter('tachesDate',$tachesDate);
        if ($tachesLibreDate)
            $tachesSynchro = $tachesSynchro
                ->andWhere('ts.tachesLibreDate = :tachesLibreDate')
                ->setParameter('tachesLibreDate',$tachesLibreDate);

        $tachesSynchro = $tachesSynchro
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$tachesSynchro)
        {
            $tachesSynchro = new TachesSynchro();
            $tachesSynchro
                ->setDossier($dossier)
                ->setTachesDate($tachesDate)
                ->setDate($date)
                ->setTachesLibreDate($tachesLibreDate);

            $em = $this->getEntityManager();
            $em->persist($tachesSynchro);
            $em->flush();
        }

        return $tachesSynchro;
    }

    /**
     * @param Client[] $clients
     * @param \DateTime $debut
     * @param \DateTime $fin
     * @param bool $isLegale
     * @param bool $isLibre
     * @param bool $isFaite
     * @return TachesSynchro[]
     */
    public function getTachesSynchroForClients($clients, \DateTime $debut, \DateTime $fin, $isLegale = true, $isLibre = true, $isFaite = true)
    {
        $tachesSynchros = $this->createQueryBuilder('ts')
            ->join('ts.dossier','d')
            ->join('d.site','s')
            ->join('AppBundle:BanqueCompte', 'bc', 'WITH', 'bc.dossier = d')
            ->where('s.client IN (:clients)')
            ->andWhere('bc.etat = 1')
            ->andWhere('d.status = 1')
            ->andWhere('ts.date BETWEEN :debut AND :fin')
            ->setParameters([
                'clients' => $clients,
                'debut' => $debut,
                'fin' => $fin
            ]);

        if (!$isLegale)
            $tachesSynchros = $tachesSynchros
                ->andWhere('ts.tachesDate IS NULL');
        if (!$isLibre)
            $tachesSynchros = $tachesSynchros
                ->andWhere('ts.tachesLibreDate IS NULL');
        if (!$isFaite)
            $tachesSynchros = $tachesSynchros
                ->andWhere('ts.status = 0');

        return $tachesSynchros
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Client $client
     * @param \DateTime $debut
     * @param \DateTime $fin
     * @return TachesSynchro[]
     */
    public function getTachesSynchroForClient(Client $client, \DateTime $debut, \DateTime $fin)
    {
        return $this->createQueryBuilder('ts')
            ->join('ts.dossier','d')
            ->join('d.site','s')
            ->where('s.client = :client')
            ->andWhere('ts.date BETWEEN :debut AND :fin')
            //->andWhere('d.id = :dossier_id')
            ->setParameters([
                'client' => $client,
                'debut' => $debut,
                'fin' => $fin
                //,'dossier_id' => 11266
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Client $client
     * @return array
     */
    public function getTachesSynchrosForPriorite(Client $client)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $dateNow = new \DateTime();
        $req = '
            SELECT MIN(t.date) as min_date, t.id, t.dossier_id FROM taches_synchro t
            JOIN dossier d ON (d.id = t.dossier_id)
            JOIN site s ON (s.id = d.site_id)
            WHERE s.client_id = :client_id AND t.date >= :date_now
            GROUP BY t.dossier_id
        ';
        $prep = $pdo->prepare($req);
        $prep->execute([
            'client_id' => $client->getId(),
            'date_now' => $dateNow->format('Y-m-d')
        ]);
        $res = $prep->fetchAll();

        $priorites = [];
        foreach ($res as $re)
        {
            $date = clone \DateTime::createFromFormat('Y-m-d',$re->min_date);
            $date->setTime(0,0,0);
            $priorites[intval($re->dossier_id)] = (object)
            [
                'd' => $date,
                't' => 0,
                'id' => $re->id
            ];
        }
        return $priorites;
    }

    /**
     * @param Dossier $dossier
     * @return TachesSynchro[]
     */
    public function getAllForDossier(Dossier $dossier)
    {
        return $this->createQueryBuilder('ts')
            ->where('ts.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param TachesSynchro|null $tachesSynchro
     * @param string $idGoogle
     * @param string $nomTache
     * @param int $status
     * @param \DateTime|null $date
     */
    public function marquerFait(Dossier $dossier, TachesSynchro $tachesSynchro = null, $idGoogle = '', $nomTache = '',$status = 1,\DateTime $date = null, \DateTime $dateFait = null)
    {
        if ($tachesSynchro){
            $tachesSynchro->setStatus($status);
            $tachesSynchro->setDatefait($dateFait);
        } 
        else
        {
            $config = $this->getEntityManager()->getRepository('AppBundle:GoogleCalendarConfig')
                ->getConfig($dossier->getSite()->getClient());

            if ($config)
            {
                $calendar = new GoogleCalendar();
                $calendar->setConfig($config);
                $nom = $dossier->getNom().':'.$nomTache;
                if ($status == 1) $nom .= ':faite';

                if (intval($idGoogle) == -1 && $date) $calendar->createEvent($nom, $dossier->getNom(),$date);
                $calendar->updateEvent($idGoogle,$nom,$dossier->getNom());
            }
        }
        $this->getEntityManager()->flush();
    }

     /**
     * @param Dossier[] $dossiers
     * @param \DateTime $debut
     * @param \DateTime $fin
     * @param bool $isLegale
     * @param bool $isLibre
     * @param bool $isFaite
     * @return TachesSynchro[]
     */
    public function getTachesSynchroForDossiers($dossiers, \DateTime $debut, \DateTime $fin, $isLegale = true, $isLibre = true, $isFaite = true)
    {
        $tachesSynchros = $this->createQueryBuilder('ts')
            ->join('ts.dossier','d')
            ->join('AppBundle:BanqueCompte', 'bc', 'WITH', 'bc.dossier = d')
            ->where('ts.dossier IN (:dossiers)')
            ->andWhere('bc.etat = 1')
            ->andWhere('d.status = 1')
            ->andWhere('ts.date BETWEEN :debut AND :fin')
            ->setParameters([
                'dossiers' => $dossiers,
                'debut' => $debut,
                'fin' => $fin
            ]);

        if (!$isLegale)
            $tachesSynchros = $tachesSynchros
                ->andWhere('ts.tachesDate IS NULL');
        if (!$isLibre)
            $tachesSynchros = $tachesSynchros
                ->andWhere('ts.tachesLibreDate IS NULL');
        if (!$isFaite)
            $tachesSynchros = $tachesSynchros
                ->andWhere('ts.status = 0');

        $tachesSynchros = $tachesSynchros
                ->orderby('ts.date', 'asc');
        return $tachesSynchros
            ->getQuery()
            ->getResult();
    }
}