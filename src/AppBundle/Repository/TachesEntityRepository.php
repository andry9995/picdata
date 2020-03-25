<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 23/11/2018
 * Time: 15:35
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Taches;
use AppBundle\Entity\TachesAction;
use AppBundle\Entity\TachesDate;
use AppBundle\Entity\TachesEntity;
use AppBundle\Entity\TachesItem;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;

class TachesEntityRepository extends EntityRepository
{
    /**
     * @param TachesAction $tachesAction
     * @param Dossier $dossier
     * @return TachesDate[]
     */
    public function getTachesDateEnabled(TachesAction $tachesAction, Dossier $dossier)
    {
        /** @var TachesEntity[] $tachesEntitys */
        $tachesEntitys = $this->createQueryBuilder('te')
            ->leftJoin('te.tachesDate','td')
            ->where('td.tachesAction = :tachesAction')
            ->andWhere('td.dossier IS NULL')
            ->andWhere('te.status = :status')
            ->andWhere('te.dossier = :dossier')
            ->setParameters([
                'tachesAction' => $tachesAction,
                'status' => 1,
                'dossier' => $dossier
            ])
            ->getQuery()
            ->getResult();

        $tachesDates = [];
        foreach ($tachesEntitys as $tachesEntity)
        {
            $tachesDates[] = $tachesEntity->getTachesDate();
        }

        return $tachesDates;
    }

    /**
     * @param TachesAction $tachesAction
     * @param Dossier $dossier
     * @return array
     */
    public function getTachesDateEnabledIds(TachesAction $tachesAction, Dossier $dossier)
    {
        $tachesDates = $this->getTachesDateEnabled($tachesAction,$dossier);
        $ids = [0];
        foreach ($tachesDates as $tachesDate) $ids[] = $tachesDate->getId();

        return $ids;
    }

    /**
     * @param TachesDate $tachesDate
     * @param Dossier $dossier
     * @param bool $updated
     * @param int $status
     * @param int $responsable
     * @param int $jourAdd
     * @return TachesEntity
     */
    public function getTachesEntity(TachesDate $tachesDate, Dossier $dossier,$updated = false, $status = 1, $responsable = 0, $jourAdd = 0)
    {
        /** @var TachesEntity $tachesEntity */
        $tachesEntity = $this->createQueryBuilder('te')
            ->where('te.dossier = :dossier')
            ->andWhere('te.tachesDate = :tacheDate')
            ->setParameters([
                'dossier' => $dossier,
                'tacheDate' => $tachesDate
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $isTva = ($tachesDate->getTachesAction()->getTachesItem()->getTaches()->getTachesGroup() && strtoupper($tachesDate->getTachesAction()->getTachesItem()->getTaches()->getTachesGroup()->getNom()) == 'TVA');

        $em = $this->getEntityManager();
        $add = false;
        if ($status == 0)
        {
            $responsable = 0;
            $jourAdd = 0;
        }

        if (!$tachesEntity)
        {
            $clotures = json_decode($tachesDate->getClotures());
            if (in_array($dossier->getCloture(),$clotures) && !($isTva && $dossier->getRegimeTva() && $dossier->getRegimeTva()->getCode() == 'CODE_NON_SOUMIS'))
            {
                $add = true;
                $tachesEntity = new TachesEntity();
                $tachesEntity
                    ->setDossier($dossier)
                    ->setTachesDate($tachesDate);
            }
        }
        elseif (($isTva && $dossier->getRegimeTva() && $dossier->getRegimeTva()->getCode() == 'CODE_NON_SOUMIS'))
        {
            $em->remove($tachesEntity);
            $em->flush();
            return null;
        }

        if ($updated)
            $tachesEntity
                ->setStatus($status)
                ->setResponsable($responsable)
                ->setJourAdditif($jourAdd);

        if ($add)
        {
            $responsable = 0;
            $tResponsables = ['TVA'];
            if ($tachesEntity)
            {
                $nomTaches = trim(strtoupper($tachesEntity->getTachesDate()->getTachesAction()->getTachesItem()->getTaches()->getNom()));
                if (in_array($nomTaches,$tResponsables))
                {
                    $prestationFiscale = $this->getEntityManager()->getRepository('AppBundle:PrestationFiscale')
                        ->findOneBy(['dossier' => $dossier]);

                    if ($prestationFiscale && $prestationFiscale->getTva() && $prestationFiscale->getTva() != 1 && $nomTaches == 'TVA')
                        $responsable = ($dossier->getSite()->getClient()->getTypeClient() == 0) ? 1 : 2;
                }
            }
            $tachesEntity->setResponsable($responsable);

            $em->persist($tachesEntity);
        }
        $em->flush();

        return $tachesEntity;
    }

    /**
     * @param Client[] $clients
     * @return TachesEntity[]
     */
    public function getTachesEntityEnabledClients($clients)
    {
        return $this->createQueryBuilder('te')
            ->join('te.dossier','d')
            ->join('d.site','s')
            ->where('te.status = 1')
            ->andWhere('s.client IN (:clients)')
            ->setParameter('clients',$clients)
            /*->andWhere('d.id = :id')
            ->setParameter('id',11266)*/
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Client|null $cl
     * @return Client[]
     */
    public function getClientsHasTaches(Client $cl = null)
    {
        if ($cl) return [$cl];

        $clientsEntitys = $this->getClientsInTaches();
        $clientsDates = $this->getEntityManager()->getRepository('AppBundle:TachesDate')->getClientsInTaches();

        /** @var int[] $clientsPassers */
        $clientsPassers = [];
        /** @var Client[] $clients */
        $clients = [];
        foreach ($clientsEntitys as $client)
        {
            if (!in_array($client->getId(),$clientsPassers))
            {
                $clientsPassers[] = $client->getId();
                $clients[] = $client;
            }
        }
        foreach ($clientsDates as $client)
        {
            if (!in_array($client->getId(),$clientsPassers))
            {
                $clientsPassers[] = $client->getId();
                $clients[] = $client;
            }
        }

        return $clients;
    }

    /**
     * @return Client[]
     */
    public function getClientsInTaches()
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $req = '
            SELECT DISTINCT s.client_id FROM taches_entity te
            JOIN dossier d on (d.id = te.dossier_id)
            JOIN site s on (s.id = d.site_id);        
        ';

        $prep = $pdo->prepare($req);
        $prep->execute();
        $res = $prep->fetchAll();

        /** @var int[] $ids */
        $ids = [];
        foreach ($res as $re)
        {
            $ids[] = $re->client_id;
        }

        return $this->getEntityManager()->getRepository('AppBundle:Client')
            ->createQueryBuilder('c')
            ->where('c.id IN (:ids)')
            ->setParameter('ids',$ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param Taches $taches
     * @param TachesItem[] $tachesItems
     */
    public function deleteNotForDossier(Dossier $dossier, Taches $taches, $tachesItems = [])
    {
        $notForDossiers = $this->createQueryBuilder('te')
            ->leftJoin('te.tachesDate','td')
            ->leftJoin('td.tachesAction','ta')
            ->leftJoin('ta.tachesItem','ti')
            ->where('ti.taches = :taches')
            ->setParameter('taches',$taches)
            ->andWhere('te.dossier = :dossier')
            ->setParameter('dossier',$dossier);

        if (count($tachesItems) > 0)
            $notForDossiers = $notForDossiers
                ->andWhere('ta.tachesItem NOT IN (:tachesItems)')
                ->setParameter('tachesItems',$tachesItems);

        $notForDossiers = $notForDossiers
            ->getQuery()
            ->getResult();

        if (count($notForDossiers) > 0)
        {
            $em = $this->getEntityManager();
            foreach ($notForDossiers as $notForDossier)
                $em->remove($notForDossier);

            $em->flush();
        }
    }
}