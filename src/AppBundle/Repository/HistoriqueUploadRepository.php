<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 14/06/2016
 * Time: 08:54
 */

namespace AppBundle\Repository;
use AppBundle\Controller\Boost;
use AppBundle\Entity\Client;
use AppBundle\Entity\HistoriqueUpload;
use AppBundle\Entity\Image;
use AppBundle\Entity\Site;
use AppBundle\Entity\Utilisateur;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Dossier;
use \DateTime;

class HistoriqueUploadRepository extends EntityRepository
{
    /**
     * @param Dossier|null $dossier
     * @param $exercice
     * @return \DateTime|null
     */
    public function getDateAnciennete(Dossier $dossier = null,$exercice)
    {
        $dateUpload = $this->getEntityManager()->getRepository('AppBundle:Ecriture')
            ->createQueryBuilder('e')
            ->select('MAX(hu.dateUpload) AS d')
            ->leftJoin('e.historiqueUpload','hu')
            ->where('e.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('e.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->andWhere('hu.type = 3')
            ->orderBy('hu.dateUpload','DESC')
            ->getQuery()
            ->getOneOrNullResult();

        if($dateUpload['d'] != null) $dateUpload = new DateTime($dateUpload['d']);
        else $dateUpload = null;
        $dateCloture = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateCloture($dossier,$exercice);

        if($dateUpload != null)
        {
            if($dateCloture <= $dateUpload) return $dateCloture;
            else return $dateUpload;
        }
        else return $dateCloture;
    }

    /**
     * @param Dossier|null $dossier
     * @param $exercice
     * @return DateTime|null
     */
    public function getDateCalculAnciennete(Dossier $dossier = null,$exercice)
    {
        $historiqueUpload = $this->createQueryBuilder('hu')
            ->where('hu.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('hu.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->andWhere('hu.exercice <> 0')
            ->orderBy('hu.id','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if($historiqueUpload != null)
        {
            if($historiqueUpload->getCloture() == 1)
            {
                $dateCloture = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getDateCloture($dossier,$exercice);
                return $dateCloture;
            }
            else return $historiqueUpload->getDateVerification();
        }
        else return $this->getDateAnciennete($dossier,$exercice);
    }

    /**
     * @param Dossier $dossier
     * @param $exercices
     * @return array
     */
    public function exercicesAreClotured(Dossier $dossier,$exercices)
    {
        $results = array();
        foreach ($exercices as $exercice) $results[$exercice] = $this->exerciceResultAndCloture($dossier,$exercice);
        return $results;
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @return bool
     */
    public function exerciceIsClotured(Dossier $dossier,$exercice)
    {
        $result = $this->createQueryBuilder('h')
            ->where('h.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('h.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->orderBy('h.id','DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();

        $cloture = $result ? $result->getCloture() : 0;
        return ($cloture == 1);
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @return mixed
     */
    public function exerciceResultAndCloture(Dossier $dossier,$exercice)
    {
        return $this->createQueryBuilder('h')
            ->where('h.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('h.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->orderBy('h.id','DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * @param Client $client
     * @param $users
     * @param $dateStart
     * @param $dateEnd
     * @param bool $notInTransfert
     * @param bool $avecTelecharger
     * @return Image[]
     */
    public function getImagesUsers(Client $client,$users,$dateStart,$dateEnd,$notInTransfert = false,$avecTelecharger = true)
    {
        $images = $this->getEntityManager()->getRepository('AppBundle:Image')
            ->createQueryBuilder('i')
            ->leftJoin('i.lot','l')
            ->leftJoin('l.dossier','d')
            ->leftJoin('d.site','s')
            ->leftJoin('s.client','c')
            ->leftJoin('l.utilisateur','u')
            ->addSelect('l')
            ->addSelect('u')
            ->where('c = :client')
            ->andWhere('i.status = 0')
            ->andWhere('l.utilisateur IN (:users)')
            ->andWhere('l.dateScan >= :dateStart')
            ->andWhere('l.dateScan <= :dateEnd')
            ->andWhere('i.supprimer = 0')
            ->setParameter('client',$client)
            ->setParameter('users',$users)
            ->setParameter('dateStart',$dateStart)
            ->setParameter('dateEnd',$dateEnd)
            ->orderBy('l.dateScan','DESC')
            ->addOrderBy('d.nom','ASC')
            ->addOrderBy('l.utilisateur');

        if (!$avecTelecharger)
            $images = $images
                ->andWhere('i.download IS NULL');

        if (!$notInTransfert) return $images
            ->getQuery()
            ->getResult();

        $imagesInTranferts = $this->getEntityManager()->getRepository('AppBundle:ImageTransfert')
            ->imagesInTransferts($client,true);

        $ids = [0];
        foreach ($imagesInTranferts as $imagesInTranfert)
            $ids[] = $imagesInTranfert->getId();

        return $images
            ->andWhere('i.id NOT IN (:imagesInTranferts)')
            ->setParameter('imagesInTranferts', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $dossiers
     * @param $filtre
     * @param $client
     * @param $site
     * @param $dossier
     * @param $dateStart
     * @param $dateEnd
     * @param $nom
     * @param $originale
     * @param Utilisateur $user
     * @return array
     */
    public function getImagesUsersOrgs($dossiers,$filtre,$client,$site,$dossier,$dateStart,$dateEnd,$nom,$originale,Utilisateur $user)
    {
        $result = $this->getEntityManager()->getRepository('AppBundle:Image')
            ->createQueryBuilder('i')
            ->leftJoin('i.lot','l')
            ->leftJoin('l.dossier','d')
            ->leftJoin('d.site','s')
            ->leftJoin('s.client','c')
            //->leftJoin('l.utilisateur','u')
            ->where('d in (:dossiers)')
            ->setParameter('dossiers',$dossiers);
        if ($filtre == 0)
        {
            $result = $result
                ->andWhere('l.dateScan >= :dateStart')
                ->setParameter('dateStart',$dateStart)
                ->andWhere('l.dateScan <= :dateEnd')
                ->setParameter('dateEnd',$dateEnd);

            if ($user->getShowDossierDemo())
            {
                if ($dossier != null) $result = $result->andWhere('d = :dossier')->setParameter('dossier',$dossier);
            }
            else
            {
                if ($dossier != null) $result = $result->andWhere('d = :dossier')->setParameter('dossier',$dossier);
                else if($site != null) $result = $result->andWhere('s = :site')->setParameter('site',$site);
                else $result = $result->andWhere('c = :client')->setParameter('client',$client);
            }
        }
        elseif ($filtre == 1)
        {
            if($nom != '')
            {
                $nom = str_replace(' ','%',$nom);
                $result = $result->andWhere('i.nom LIKE :nom')
                    ->setParameter('nom','%'.$nom.'%');
            }
            if($originale != '')
            {
                $originale = str_replace(' ','%',$originale);
                $result = $result->andWhere('i.originale LIKE :originale')
                    ->setParameter('originale','%'.$originale.'%');
            }

        }

        return $result->orderBy('c.nom')
            ->addOrderBy('d.nom')
            ->addOrderBy('l.dateScan')
            ->addOrderBy('l.lot')
            ->addOrderBy('i.nom')
            ->getQuery()->getResult();
    }

    /**
     * @param Utilisateur $utilisateur
     * @param Client $client
     * @param Site|null $site
     * @param Dossier|null $dossier
     * @param $exercice
     * @return array
     */
    public function getHistoriques(Utilisateur $utilisateur, Client $client, Site $site = null, Dossier $dossier = null,$exercice)
    {
        /** @var Dossier[] $dossiers */
        $dossiers = [];
        if ($dossier) $dossiers[] = $dossier;
        else $dossiers = $this->getEntityManager()->getRepository('AppBundle:Dossier')
            ->getUserDossier($utilisateur,$client,$site,$exercice);

        $results = [];
        foreach ($dossiers as $dos)
        {
            $n = $this->getLastDossier($dos,$exercice,false);
            $n1 = $this->getLastDossier($dos,$exercice - 1,false);
            $statusN = '';
            if ($n)
            {
                if ($n->dv && $n->s == 0) $statusN = 'Projet du '.$n->dv;
                elseif ($n->s == 1) $statusN = 'Cloturé';
            }
            $statusN1 = '';
            if ($n1)
            {
                if ($n1->dv && $n1->s == 0) $statusN1 = 'Projet du '.$n1->dv;
                elseif ($n1->s == 1) $statusN1 = 'Cloturé';
            }

            $results[] = (object)
            [
                'id' => Boost::boost($dos->getId()),
                'nom' => $dos->getNom(),
                'du_n' => $n ? $n->du : null,
                's_n' => $statusN,
                'du_n1' => $n1 ? $n1->du : null,
                's_n1' => $statusN1
            ];
        }

        return $results;
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @param bool $entity
     * @return HistoriqueUpload|object
     */
    public function getLastDossier(Dossier $dossier,$exercice,$entity = true)
    {
        /** @var HistoriqueUpload $res */
        $res = $this->createQueryBuilder('hu')
            ->where('hu.dossier = :dossier')
            ->andWhere('hu.exercice = :exercice')
            ->setParameters([
                'dossier' => $dossier,
                'exercice' => $exercice
            ])
            ->orderBy('hu.dateUpload','DESC')
            ->addOrderBy('hu.id','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($entity || !$res) return $res;

        return (object)
        [
            'du' => $res->getDateUpload() ? $res->getDateUpload()->format('d/m/Y') : null,
            'dv' => $res->getDateVerification() ? $res->getDateVerification()->format('d/m/Y') : null,
            's' => $res->getCloture()
        ];
    }

    /**
     * @param Dossier $dossier
     * @return HistoriqueUpload[]
     */
    public function getHistoriqueUploaClosed(Dossier $dossier)
    {
        /** @var HistoriqueUpload[] $historiqueUploads */
        $historiqueUploads = $this->createQueryBuilder('hu')
            ->where('hu.dossier = :dossier')
            ->andWhere('hu.type = :type')
            ->andWhere('hu.cloture = :cloture')
            ->setParameters([
                'dossier' => $dossier,
                'type' => 3,
                'cloture' => 1
            ])
            ->orderBy('hu.exercice','DESC')
            ->addOrderBy('hu.id','DESC')
            ->getQuery()
            ->getResult();

        /** @var HistoriqueUpload[] $results */
        $results = [];
        foreach ($historiqueUploads as $historiqueUpload)
            if (!array_key_exists($historiqueUpload->getExercice(),$results))
                $results[$historiqueUpload->getExercice()] = $historiqueUpload;

        return $results;
    }

    /**
     * @return object
     */
    public function getRecaps()
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $totalDossier = 0;
        $totalCompta = 0;
        $totalNonClosed = 0;

        $req = '
            SELECT c_id,c_nom, COUNT(*) AS ISA_COMPTA FROM  
                (SELECT DISTINCT c.id AS c_id, d.id AS d_id, c.nom AS c_nom,d.nom AS d_nom, hu.exercice AS hu_exercice 
                FROM historique_upload hu 
                JOIN dossier d ON (hu.dossier_id = d.id)
                JOIN site s ON (d.site_id = s.id)
                JOIN client c ON (s.client_id = c.id)
                WHERE hu.cloture = 1 AND hu.type = 3 AND exercice <> 0 
                ORDER BY c.nom, d.nom, hu.exercice DESC, hu.id DESC)
                AS isa
            GROUP BY c_id; 
        ';
        $params = [];
        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $comptas = $prep->fetchAll();

        $req = '
            SELECT COUNT(*) AS ISA_DOSSIER, c_nom,c_id
            FROM 
            (SELECT DISTINCT c_id,c_nom,d_nom AS compta FROM  
                (SELECT DISTINCT c.id AS c_id, d.id AS d_id, c.nom AS c_nom,d.nom AS d_nom, hu.exercice AS hu_exercice 
                FROM historique_upload hu 
                JOIN dossier d ON (hu.dossier_id = d.id)
                JOIN site s ON (d.site_id = s.id)
                JOIN client c ON (s.client_id = c.id)
                WHERE hu.cloture = 1 AND hu.type = 3 AND exercice <> 0 
                ORDER BY c.nom, d.nom, hu.exercice DESC, hu.id DESC)
                AS tortues_perroquets_F)
            AS ISA_DOSSIER 
            GROUP BY c_id
        ';

        $params = [];
        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $dossiers = $prep->fetchAll();

        $results = [];
        foreach ($comptas as $compta)
        {
            $key = $compta->c_id;

            if (!array_key_exists($key,$results)){

                $query = '
                    SELECT d.id
                    FROM journal_dossier jd
                    INNER JOIN  dossier d ON (jd.dossier_id=d.id)
                    INNER JOIN site s ON (d.site_id=s.id)
                    INNER JOIN client c ON (s.client_id=c.id)
                    WHERE c.id = :client_id
                    AND jd.journal_id=2
                    GROUP BY d.id
                ';

                // var_dump($key);

                // var_dump($query);die();

                $prep = $pdo->prepare($query);

                $prep->execute(array(
                    'client_id'  => $key,
                ));

                $resultat = $prep->fetchAll();

                $results[$key] = (object)
                [
                    'c' => $compta->ISA_COMPTA,
                    'dnp' => count($resultat),
                    'd' => 0,
                    'n' => $compta->c_nom,
                    'id' => Boost::boost($compta->c_id),
                    'nc' => 0
                ];
            }

            $totalCompta += intval($compta->ISA_COMPTA);
        }

        foreach ($dossiers as $dossier)
        {
            $key = $dossier->c_id;
            $results[$key]->d = $dossier->ISA_DOSSIER;

            $totalDossier += intval($dossier->ISA_DOSSIER);
        }

        //$results[$clientId][$dossierId][] = $nonCloture->exercice;
        $nonCloseds = $this->getExerciceNoClosed();
        foreach ($nonCloseds as $clientId => $nonClosed)
        {
            if (!array_key_exists($clientId,$results))
            {
                $client = $this->getEntityManager()->getRepository('AppBundle:Client')
                    ->find($clientId);

                $nCs = 0;
                foreach ($nonClosed as $dossiers)
                    foreach ($dossiers as $exercices)
                        $nCs += count($exercices);


                $query = '
                    SELECT d.id
                    FROM journal_dossier jd
                    INNER JOIN  dossier d ON (jd.dossier_id=d.id)
                    INNER JOIN site s ON (d.site_id=s.id)
                    INNER JOIN client c ON (s.client_id=c.id)
                    WHERE c.id = :client_id
                    AND jd.journal_id=2
                    GROUP BY d.id
                ';

                $prep = $pdo->prepare($query);

                $prep->execute(array(
                    'client_id'  => $clientId,
                ));

                $resultat = $prep->fetchAll();

                $results[$clientId] = (object)
                [
                    'c' => 0,
                    'd' => 0,
                    'dnp' => count($resultat),
                    'n' => $client->getNom(),
                    'id' => Boost::boost($clientId),
                    'nc' => $nCs
                ];

                $totalNonClosed += $nCs;
            }
            else
            {
                $nCs = 0;
                foreach ($nonClosed as $dossiers)
                    foreach ($dossiers as $exercices)
                        $nCs += count($exercices);

                $results[$clientId]->nc = $results[$clientId]->nc + $nCs;
                $totalNonClosed += $nCs;
            }
        }

        return (object)
        [
            'datas' => array_values($results),
            'td' => $totalDossier,
            'tc' => $totalCompta,
            'tnc' => $totalNonClosed
        ];
    }

    /**
     * @param Client $client
     * @param int $type
     * @return array
     */
    public function details(Client $client, $type = 1)
    {
        $params = [];
        if ($type == 0)
        {
            $req = '
                SELECT COUNT(*) AS compta , c_id, d_id, c_nom, d_nom FROM (
                    SELECT DISTINCT c.id AS c_id, d.id AS d_id, c.nom AS c_nom,d.nom AS d_nom, hu.exercice AS hu_exercice 
                    FROM historique_upload hu 
                    JOIN dossier d ON (hu.dossier_id = d.id)
                    JOIN site s ON (d.site_id = s.id)
                    JOIN client c ON (s.client_id = c.id)
                    WHERE hu.cloture = 1 AND hu.type = 3 AND exercice <> 0 AND c.id = :C_ID 
                    ORDER BY c.nom, d.nom, hu.exercice DESC, hu.id DESC)
                AS dossier_grouped 
                WHERE c_id = :C_ID1 
                GROUP BY d_id;            
            ';
            $params['C_ID1'] = $client->getId();
        }
        else if ($type == 2)
        {
            //$results[$clientId][$dossierId][]
            $nonClotures = $this->getExerciceNoClosed($client);
            $results = [];
            foreach ($nonClotures as $clientId => $dossiers)
            {
                $client = $this->getEntityManager()->getRepository('AppBundle:Client')
                    ->find($clientId);
                foreach ($dossiers as $dossierId => $exercices)
                {
                    $dossier = $this->getEntityManager()->getRepository('AppBundle:Dossier')
                        ->find($dossierId);
                    foreach ($exercices as $exercice)
                    {
                        $results[] = (object)
                        [
                            'id' => Boost::boost($dossierId),
                            'exo' => $exercice,
                            'd_id' => $dossier->getId(),
                            'c_nom' => $client->getNom(),
                            'd_nom' => $dossier->getNom(),
                            'compta' => $exercice
                        ];
                    }
                }
            }

            return $results;
        }
        else
        {
            if ($type == 3) {

                $req = '
                    SELECT jd.id, d.nom as dossier, jd.code_str, j.code,j.libelle, "<i class=\'fa fa-save icon-action js-save-button save-dnp\'></i>" as action
                    FROM journal_dossier jd
                    INNER JOIN  dossier d ON (jd.dossier_id=d.id)
                    INNER JOIN site s ON (d.site_id=s.id)
                    INNER JOIN client c ON (s.client_id=c.id)
                    INNER JOIN journal j on(jd.journal_id=j.id)
                    WHERE c.id = :C_ID
                    -- AND jd.journal_id = 2
                    -- GROUP BY d.id
                ';

            } else {
                $req = '
                    SELECT DISTINCT c.id AS c_id, d.id AS d_id, c.nom AS c_nom,d.nom AS d_nom, hu.exercice AS compta 
                    FROM historique_upload hu 
                    JOIN dossier d ON (hu.dossier_id = d.id)
                    JOIN site s ON (d.site_id = s.id)
                    JOIN client c ON (s.client_id = c.id)
                    WHERE hu.cloture = 1 AND hu.type = 3 AND exercice <> 0 AND c.id = :C_ID
                    ORDER BY c.nom, d.nom, hu.exercice DESC, hu.id DESC;            
                ';
            }

        }

        $params['C_ID'] = $client->getId();

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $prep = $pdo->prepare($req);
        $prep->execute($params);
        return $prep->fetchAll();
    }

    public function journalOptions($select_option = true)
    {

        $req = '
                    SELECT j.code, j.libelle, j.id, "<i class=\'fa fa-edit icon-action js-save-button edit-journal\'></i><i class=\'fa fa-times icon-action js-save-button restore-journal\'></i><i class=\'fa fa-save icon-action js-save-button save-journal\'></i><i class=\'fa fa-trash icon-action js-save-button delete-journal\'></i>" as action
                    FROM journal j
                    where j.supprimer is null
                    ORDER BY j.libelle
                ';

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $prep = $pdo->prepare($req);
        $prep->execute();

        $resultat = $prep->fetchAll();

        if (!$select_option) {
            return $resultat;
        }

        $values = "";

        foreach ($resultat as $key => $value) {
            $values .= $value->code . ":" . $value->libelle;

            if ($key < count($resultat) - 1) {
                $values .= ";";
            }
        }

        return $values;


    }

    public function saveDnpEdit($data)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $select = '
            SELECT id
            FROM journal
            WHERE code = :journal_code
        ';

        $params = array(
            'journal_code' => $data['journal_code']
        );

        $prep = $pdo->prepare($select);
        $prep->execute($params);
        $journal_id = $prep->fetchAll()[0]->id;

        $update = '
            UPDATE journal_dossier
                SET journal_id = :journal_id
            WHERE id = :journal_dossier_id
        ';

        $prep = $pdo->prepare($update);

        return $prep->execute(array(
            'journal_id' => $journal_id,
            'journal_dossier_id' => $data['journal_dossier_id']
        ));

    }


    /**
     * @param Client|null $client
     * @return array
     */
    public function getExerciceNoClosed(Client $client = null)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $req = '
            SELECT DISTINCT s.client_id, hu.dossier_id, hu.exercice 
            FROM historique_upload hu 
            JOIN dossier d ON (hu.dossier_id = d.id)
            JOIN site s ON (d.site_id = s.id)
            WHERE hu.cloture = :cloture AND hu.type = 3 AND hu.exercice <> 0 
        ';

        $paramsBase = [];
        if ($client)
        {
            $req .= 'AND s.client_id = :client_id ';
            $paramsBase['client_id'] = $client->getId();
        }

        $req .= 'ORDER BY hu.id DESC';

        $prep = $pdo->prepare($req);
        $params = $paramsBase;
        $params['cloture'] = 1;
        $prep->execute($params);
        $clotures = $prep->fetchAll();
        $exerciceDossierCloseds = [];
        foreach ($clotures as $cloture)
            $exerciceDossierCloseds[] = $cloture->client_id . '-' . $cloture->dossier_id . '-' . $cloture->exercice;

        $params = $paramsBase;
        $params['cloture'] = 0;
        $prep->execute($params);
        $nonClotures = $prep->fetchAll();

        $results = [];

        foreach ($nonClotures as $nonCloture)
        {
            $clientId = $nonCloture->client_id;
            $dossierId = $nonCloture->dossier_id;
            $key = $clientId . '-' . $dossierId . '-' . $nonCloture->exercice;
            if (!in_array($key,$exerciceDossierCloseds))
            {
                if (!array_key_exists($clientId,$results))
                    $results[$clientId] = [];
                if (!array_key_exists($dossierId,$results[$clientId]))
                    $results[$clientId][$dossierId] = [];

                $results[$clientId][$dossierId][] = $nonCloture->exercice;
            }
        }

        return $results;
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @return bool
     */
    public function cloturerCompta(Dossier $dossier, $exercice)
    {
        $historiqueUpload = $this->getLastDossier($dossier,$exercice);

        if ($historiqueUpload)
        {
            $historiqueUpload->setCloture(1);
            $this->getEntityManager()->flush();
            return true;
        }

        return false;
    }
}