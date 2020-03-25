<?php
namespace AppBundle\Repository;
use AppBundle\Controller\Boost;
use AppBundle\Controller\StringExt;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\OneActivite;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Tiers;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\OrderBy;


class TiersRepository extends EntityRepository
{
    /**
     * @param Tiers $tiers
     * @return Pcc
     */
    public function getHisPcc(Tiers $tiers)
    {
        if($tiers->getPcc() != null) return $tiers->getPcc();

        $part_compte = 'xxxx';
        if ($tiers->getType() == 0) $part_compte = '401%';
        elseif ($tiers->getType() == 1) $part_compte = '411%';
        elseif ($tiers->getType() == 2) $part_compte = '421%';

        /** @var Pcc $pcc */
        $pcc = $this->getEntityManager()->getRepository('AppBundle:Pcc')
            ->createQueryBuilder('pcc')
            ->where('pcc.dossier = :dossier')
            ->setParameter('dossier',$tiers->getDossier())
            ->andWhere('pcc.compte LIKE :part_compte')
            ->setParameter('part_compte',$part_compte)
            ->orderBy('pcc.compte')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($pcc)
        {
            $tiers->setPcc($pcc);
            $this->getEntityManager()->flush();
        }
        return $pcc;
    }

    /**
     * @param Dossier $dossier
     * @param $type
     * @return Tiers[]
     */
    public function getTiers(Dossier $dossier,$type = 10)
    {
        $result = $this->createQueryBuilder('t')
            ->where('t.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere("t.compteStr <> ''");

        if ($type != 10)
            $result = $result
            ->andWhere('t.type = :type')
            ->setParameter('type',$type);

        return $result->orderBy('t.type')
            ->addOrderBy('t.compteStr')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function majTierPcc(Dossier $dossier)
    {
        $em = $this->getEntityManager();
        $tiersNonMAJs = $this->getEntityManager()->getRepository('AppBundle:Tiers')
            ->createQueryBuilder('t')
            ->where('t.pcc IS NULL')
            ->andWhere('t.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->getQuery()
            ->getResult();

        if(count($tiersNonMAJs) == 0) return true;
        foreach ($tiersNonMAJs as $tiersNonMAJ)
        {
            $typeTier = $tiersNonMAJ->getType();

            $pcc = $this->getEntityManager()->getRepository('AppBundle:Pcc')->getPccTierOfDossier($dossier,$typeTier);
            if($pcc != null)
            {
                $tiersNonMAJ->setPcc($pcc);
                $pcc->setCollectifTiers($typeTier);
            }
        }

        $em->flush();
    }

    /**
     * @param Dossier $dossier
     * @param $intitule
     * @param bool $oneResult
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTiersByIntitule(Dossier $dossier,$intitule,$oneResult = true)
    {
        $result = $this->createQueryBuilder('t')
            ->where('t.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('t.intitule = :intitule')
            ->setParameter('intitule',$intitule);

        if ($oneResult)
        {
            return $result
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }
        else
        {
            return $result
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * @param $cle
     * @param null $dossier
     * @return Tiers[]
     */
    public function findLikeCle($cle, $dossier = null)
    {
        $tiers = $this->createQueryBuilder('t')
            ->where("t.intitule <> ''")
            ->andWhere("t.compteStr <> ''")
            ->andWhere('t.intitule LIKE :cle')
            ->setParameter(':cle','%'.$cle.'%');

        if ($dossier != null) $tiers = $tiers->andWhere('t.dossier = :dossier')->setParameter('dossier',$dossier);
        $temps = $tiers->getQuery()->getResult();

        return $temps;
    }

    /**
     * @param Dossier $dossier
     * @return Tiers[]
     */
    public function getTypesAndModelDossier(Dossier $dossier)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $query = "
            SELECT DISTINCT type
            FROM tiers 
            WHERE dossier_id = :dossier_id AND compte_str NOT LIKE :not_like AND compte_str <> '' ORDER BY type";
        $prep = $pdo->prepare($query);
        $prep->execute(array(
            'dossier_id' => $dossier->getId(),
            'not_like' => '%CREE%',
        ));
        $typesTemps = $prep->fetchAll();
        $typeComptes = [];
        foreach ($typesTemps as $temp)
        {
            $type = intval($temp->type);
            $tiers = $this->getOnTypeValide($dossier,$type);
            $typeComptes[$type] = $tiers;
        }

        return $typeComptes;
    }

    /**
     * @param Dossier $dossier
     * @param int $type
     * @return Tiers
     */
    public function getOnTypeValide(Dossier $dossier, $type = 0)
    {
        return $this->createQueryBuilder('t')
            ->where('t.dossier = :dossier')
            ->andWhere('t.type = :type')
            ->andWhere("t.compteStr <> ''")
            ->andWhere("t.intitule <> ''")
            ->andWhere('t.status = 1')
            ->setParameters([
                'type' => $type,
                'dossier' => $dossier
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Dossier $dossier
     * @param string $compte
     * @param int $type
     * @return Tiers
     */
    public function getOneByCompte(Dossier $dossier, $compte = '', $type = 0)
    {
        $tiers = $this->createQueryBuilder('t')
            ->where('t.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('t.compteStr = :compte')
            ->setParameter('compte',$compte);

        if ($type != 10)
            $tiers = $tiers
                ->andWhere('t.type = :type')
                ->setParameter('type',$type);

        return $tiers
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getTiersByDossier(Dossier $dossier, array $type, $compte){

        return $this->createQueryBuilder('tiers')
            ->where('tiers.type IN (:type)')
            ->andWhere('tiers.compteStr LIKE :compte_str')
            ->andWhere('tiers.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->setParameter('type', array_values($type))
            ->setParameter('compte_str', $compte.'%')
            ->orderBy('tiers.compteStr')
            ->getQuery()
            ->getResult();

    }



    //OneUp

    /**
     * @param Dossier|null $dossier
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param string $startperiod
     * @param string $endperiod
     * @param null $activity
     * @return array
     */
    public function getProspects(Dossier $dossier = null, $sort='name', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='', $activity = null, $archive = 'unarchived') {
        $qb = $this->createQueryBuilder('tiers');

        $qb->leftJoin('tiers.oneActivite', 'oneactivite');

        $qb->where('tiers.type = 4');

        $qb->andWhere('tiers.dossier = :dossier')
            ->setParameter('dossier', $dossier);

        if($activity !== null){
            $qb->andWhere('tiers.oneActivite = :activity')
                ->setParameter('activity', $activity);
        }

        switch ($archive){
            case 'archived':
                $qb->andWhere('tiers.archive = 1');
                break;
            case 'unarchived':
                $qb->andWhere('tiers.archive = 0');
                break;

            default:
                break;
        }

        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('tiers.email', ':q'),
                $qb->expr()->like('tiers.siteWeb', ':q'),
                $qb->expr()->like('tiers.numeroClient', ':q'),
                $qb->expr()->like('tiers.note', ':q'),
                $qb->expr()->like('tiers.intitule', ':q')
            ))
                ->setParameter(':q', '%'.$q.'%');
        }

        //Période
        if ($period !== 'all') {
            if ($startperiod !== '' && $endperiod !== '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere($qb->expr()->gte('CAST(tiers.creeLe AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(tiers.creeLe AS DATE)', ':endperiod'));
                }
                $qb->setParameter(':startperiod', \DateTime::createFromFormat('d/m/Y', $startperiod)->format('Y-m-d'));
                $qb->setParameter(':endperiod', \DateTime::createFromFormat('d/m/Y', $endperiod)->format('Y-m-d'));
            }
        }

        //Tri

        switch ($sort){
            case 'name':
                $qb->orderBy('tiers.intitule', $sortOrder);
                break;

            case 'datecreation':
                $qb->orderBy('tiers.creeLe', $sortOrder);
                break;

            case 'codeclient':
                $qb->orderBy('tiers.numeroClient', $sortOrder);
                break;

            case 'activite':
                $qb->orderBy('oneactivite.nom', $sortOrder);
                break;

            default:
                $qb->orderBy('tiers.intitule', $sortOrder);
                break;
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param $activite
     * @param $typeTiers
     * @return array
     */
    public function getTiersByDossierActiviteType(Dossier $dossier, $activite, $typeTiers, $archive)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.dossier = :dossier')
            ->andWhere('t.oneActivite = :activite')
            ->andWhere('t.type = :typeTiers')
            ->setParameter('dossier', $dossier)
            ->setParameter('activite', $activite)
            ->setParameter('typeTiers', $typeTiers);

        switch ($archive){
            case 'unarchived':
                $qb->andWhere('t.archive = 0');
                break;
            case 'archived':
                $qb->andWhere('t.archive = 1');
                break;
            default:
                break;
        }

        return $qb->getQuery()
            ->getResult();
    }


    /**
     * Récupère les clients
     * @param Dossier $dossier
     * @param string $sort
     * @param string $sortOrder
     * @param string $q
     * @param string $period
     * @param string $startperiod
     * @param string $endperiod
     * @return array
     */
    public function getClients(Dossier $dossier, $sort='name', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('client');

        $qb->leftJoin('client.oneActivite','oneactivite');

        $qb->where('client.type = 1');

        $qb->andWhere('client.archive = 0');

        $qb->andWhere('client.dossier = :dossier')
            ->setParameter('dossier', $dossier);

        //Recherche mot clé
        if ($q != '') {

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('client.email', ':q'),
                $qb->expr()->like('client.siteWeb', ':q'),
                $qb->expr()->like('client.numeroClient', ':q'),
                $qb->expr()->like('client.note', ':q'),
                $qb->expr()->like('client.intitule', ':q')
            ))
                ->setParameter(':q', '%'.$q.'%');
        }

        //Période
        if ($period !== 'all') {
            if ($startperiod != '' && $endperiod != '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere('client.creeLe >= :startperiod');
                    $qb->andWhere('client.creeLe <= :endperiod');


                    $dateStartArray = explode('/', $startperiod);
                    $dateStartPeriode = null;
                    if(count($dateStartArray) === 3) {
                        $dateStartPeriode = new \DateTime("$dateStartArray[2]-$dateStartArray[1]-$dateStartArray[0]");
                    }

                    $dateEndArray = explode('/', $endperiod);
                    $dateEndPeriode = null;
                    if(count($dateEndArray) === 3) {
                        $dateEndPeriode = new \DateTime("$dateEndArray[2]-$dateEndArray[1]-$dateEndArray[0]");
                    }

                    $qb->setParameter(':startperiod',$dateStartPeriode);
                    $qb->setParameter(':endperiod', $dateEndPeriode);
                }
            }
        }

        //Tri
        switch ($sort){
            case 'name':
                $qb->orderBy('client.intitule', $sortOrder);
                break;
            case 'datecreation':
                $qb->orderBy('client.creeLe', $sortOrder);
                break;
            case 'codeclient':
                $qb->orderBy('client.numeroClient', $sortOrder);
                break;
            case 'activite':
                $qb->orderBy('oneactivite.nom', $sortOrder);
                break;
            default:
                $qb->orderBy('client.intitule', $sortOrder);
                break;
        }

        return $qb->getQuery()
            ->getResult();
    }



    public function getClientProspects(Dossier $dossier = null, $sort='name', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('tiers');

        $qb->where('tiers.type = 4 OR tiers.type = 1');

        $qb->andWhere('tiers.archive = 0');

        $qb->andWhere('tiers.dossier = :dossier')
            ->setParameter('dossier', $dossier);


        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('tiers.email', ':q'),
                $qb->expr()->like('tiers.siteWeb', ':q'),
                $qb->expr()->like('tiers.numeroClient', ':q'),
                $qb->expr()->like('tiers.note', ':q'),
                $qb->expr()->like('tiers.intitule', ':q')
            ))
                ->setParameter(':q', '%'.$q.'%');
        }

        //Période
        if ($period !== 'all') {
            if ($startperiod !== '' && $endperiod !== '') {
                if ($sort === 'datecreation') {
                    $qb->andWhere($qb->expr()->gte('CAST(tiers.creeLe AS DATE)', ':startperiod'));
                    $qb->andWhere($qb->expr()->lte('CAST(tiers.creeLe AS DATE)', ':endperiod'));
                }
                $qb->setParameter(':startperiod', \DateTime::createFromFormat('d/m/Y', $startperiod)->format('Y-m-d'));
                $qb->setParameter(':endperiod', \DateTime::createFromFormat('d/m/Y', $endperiod)->format('Y-m-d'));
            }
        }

        //Tri
        if ($sort === 'name') {
            $qb->orderBy('tiers.intitule', $sortOrder);
        } elseif ($sort === 'datecreation') {
            $qb->orderBy('tiers.creeLe', $sortOrder);
        } elseif ($sort === 'codeclient') {
            $qb->orderBy('tiers.numeroClient', $sortOrder);
        } elseif ($sort === '') {
            $qb->orderBy('tiers.intitule', $sortOrder);
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Récupération de tous les comptes
     * @return array
     */
    public function getAccounts(Dossier $dossier) {
        $qb = $this->createQueryBuilder('account')
            ->where('account.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->andWhere('account.type = 4 or account.type = 1')
            ->andWhere('account.archive = 0')
        ;

        $qb->orderBy('account.intitule', 'ASC');

        return $qb->getQuery()
            ->getResult();
    }


    public function getLastCode(Dossier $dossier) {
        try {
            $qb = $this->createQueryBuilder('clientProspect');
            $qb->select('clientProspect.numeroClient')
                ->where($qb->expr()->like('clientProspect.numeroClient', ':cli'))
                ->andWhere('clientProspect.dossier = :dossier')
                ->setParameter(':cli', 'CLI-%')
                ->setParameter('dossier', $dossier)
                ->setMaxResults(1)
                ->orderBy('clientProspect.id', 'DESC');
            $lastCode = $qb
                ->getQuery()
                ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            return 'CLI-000';
        }
    }

    public function getLastCustomCode(Dossier $dossier, $prefixe) {
        try {
            $qb = $this->createQueryBuilder('clientProspect');
            $qb->select('clientProspect.numeroClient')
                ->where($qb->expr()->like('clientProspect.numeroClient', ':cli'))
                ->setParameter(':cli', $prefixe.'%')
                ->andWhere('clientProspect.dossier = :dossier')
                ->setParameter('dossier', $dossier)
                ->setMaxResults(1)
                ->orderBy('clientProspect.id', 'DESC');
            $lastCode = $qb
                ->getQuery()
                ->getSingleScalarResult();
            return $lastCode;
        } catch (\Doctrine\ORM\NoResultException $ex) {
            return $prefixe.'000';
        }
    }
}