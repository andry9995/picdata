<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 20/03/2017
 * Time: 11:21
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Banque;
use AppBundle\Entity\Client;
use AppBundle\Entity\ReleveExt;
use AppBundle\Entity\Souscategorie;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\BanqueSousCategorieAutre;
use AppBundle\Entity\CfonbBanque;
use AppBundle\Entity\CfonbCode;
use AppBundle\Entity\Cle;
use AppBundle\Entity\CleDossier;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageFlague;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\JournalDossier;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveComplementaire;
use AppBundle\Entity\ReleveDetail;
use AppBundle\Entity\ReleveJson;
use AppBundle\Entity\ReleveManquant;
use AppBundle\Entity\SaisieControle;
use AppBundle\Entity\Separation;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\TvaImputationControle;
use Doctrine\ORM\EntityRepository;
use AppBundle\Functions\CustomPdoConnection;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ReleveRepository extends EntityRepository
{
    private $id = 0;
    private $journalCentraliser = false;
    /** @var JournalDossier $journalDossier */
    private $journalDossier = null;
    /** @var CfonbCode[] $cfonbCodeActives*/
    private $cfonbCodeActives = [];
    private $methoCompta = 0;

    /**
     * @return BanqueCompte[]
     */
    public function getAllBanqueCompteInReleve()
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $params = [];

        $req = "SELECT DISTINCT banque_compte_id AS b_id FROM releve";

        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $ids = $prep->fetchAll();
        $iDs = [];

        foreach ($ids as $id) $iDs[] = $id->b_id;

        return $this->getEntityManager()->getRepository('AppBundle:BanqueCompte')
            ->createQueryBuilder('bc')
            ->where('bc.id IN (:ids)')
            ->setParameter('ids',$iDs)
            ->getQuery()
            ->getResult();
    }

    public function majPieceCle($rels,Dossier $dossier = null,$obs = false)
    {
        /** @var Releve[] $responses */
        $responses = $rels->rs;
        $imagesAAffecters = $rels->if;
        $comptes = $rels->cs;
        $cles = $rels->cles;
        $clesTrouves = $rels->clesTrouves;
        $results = [];
        /** @var Cle[] $clesPasPieces */
        $clesPasPieces = [];

        $em = $this->getEntityManager();

        /** @var CfonbBanque[] $cfonbCodeActives */
        $cfonbCodeActives = [];

        if (count($responses) > 0)
            $cfonbCodeActives = $this->getEntityManager()->getRepository('AppBundle:CfonbBanque')
                ->cfonbActiveInBanque($responses[0]->getBanqueCompte()->getBanque());

        if ($dossier)
            $clesPasPieces = $this->getEntityManager()->getRepository('AppBundle:CleDossier')
                ->getClePasPiece($dossier);

        foreach ($responses as $response)
        {
            $key = '_'.$response->getId();
            $withImageAAffecter = intval($imagesAAffecters[$key]);
            $compts = $comptes[$key];
            $cle = $cles[$key];
            $clesTrouve = $clesTrouves[$key];
            $status = $this->getEntityManager()->getRepository('AppBundle:Releve')->getStatus($response, $clesPasPieces, $withImageAAffecter, $compts, $cle,$clesTrouve,$cfonbCodeActives);

            $bilan = null;
            $tva = null;
            $charge = null;

            $bilans = [];
            $tvas = [];
            $resultats = [];

            $convention = $response->getEngagementTresorerie();
            $s = intval($status->s);

            $statLettre = null;
            if ($response->getEcritureChange() == 1)
            {
                $releveImputations = $this->getEntityManager()->getRepository('AppBundle:ReleveImputation')
                    ->getReleveImputation($response);

                foreach ($releveImputations as $releveImputation)
                {
                    if ($releveImputation->getTiers())
                        $bilan = (object)
                        [
                            'id' => Boost::boost($releveImputation->getTiers()->getId()),
                            'l' => $releveImputation->getTiers()->getCompteStr(),
                            't' => 1
                        ];
                    elseif ($releveImputation->getPcc())
                    {
                        //0: bilan pcc, 1: tiers, 2: resultat, 3: tva
                        if ($releveImputation->getType() == 0)
                            $bilan = (object)
                            [
                                'id' => Boost::boost($releveImputation->getPcc()->getId()),
                                'l' => $releveImputation->getPcc()->getCompte(),
                                't' => 0
                            ];
                        elseif ($releveImputation->getType() == 2)
                            $charge = (object)
                            [
                                'id' => Boost::boost($releveImputation->getPcc()->getId()),
                                'l' => $releveImputation->getPcc()->getCompte(),
                                't' => 0
                            ];
                        elseif ($releveImputation->getType() == 3)
                            $tva = (object)
                            [
                                'id' => Boost::boost($releveImputation->getPcc()->getId()),
                                'l' => $releveImputation->getPcc()->getCompte(),
                                't' => 0
                            ];
                    }
                }
            }
            else
            {
                if ($s == 1)
                {
                    if ($convention == 0)
                    {
                        $bilan = $status->bilan;
                        $bilans = $status->bilans;
                    }
                    else
                    {
                        $tva = $status->tva;
                        $charge = $status->resultat;
                        $tvas = $status->tvas;
                        $resultats = $status->resultats;
                    }

                    if (intval($status->t) == 3)
                    {
                        $statLettre = $this->getEntityManager()->getRepository('AppBundle:BanqueSousCategorieAutre')
                            ->getStatLettre($response->getImageFlague());
                    }
                }
                else if ($s == 2)
                {
                    if ($response->getCleDossier())
                    {
                        $bilan = $status->bilan;
                        $tva = $status->tva;
                        $charge = $status->resultat;
                    }
                }
            }

            //0: default, 1:piece a lettre, 2: clé a valider
            $flaguer = 0;

            if ($s == 3 || $s == 2 && $status->sPiece) $flaguer = 1;
            elseif ($s == 4) $flaguer = 2;

            if (count($bilans) > 0)
            {
                if (count($bilans) == 1) $bilan = $bilans[0];
                else $bilan = $bilans;
            }
            if (count($tvas) > 0)
            {
                if (count($tvas) == 1) $tva = $tvas[0];
                else $tva = $tvas;
            }
            if (count($resultats) > 0)
            {
                if (count($resultats) == 1) $charge = $resultats[0];
                else $charge = $resultats;
            }

            $status->sl = $statLettre;
            $numCompte = $response->getBanqueCompte()->getNumcompte();
            if (strlen($numCompte) >= 11)
                $numCompte = substr($numCompte,-11);

            $cleWp = 0;
            if (property_exists($status, 'sPiece') && $status->sPiece)
                $cleWp = 1;

            $res = (object)
            [
                'id' => Boost::boost($response->getId()),
                'b' => $response->getBanqueCompte()->getBanque()->getNom(),
                'bc' => $numCompte,
                'i' => $response->getImage()->getNom(),
                'd' => date_format($response->getDateReleve(),'d/m/Y'),
                'l' => $this->getEntityManager()->getRepository('AppBundle:Releve')->getLibelleWithComplement($response,$cfonbCodeActives),
                'm' => -1 * ($response->getDebit() - $response->getCredit()),
                's' => $status,
                'ss' => $s,
                'ss3' => $s,
                'ss2' => $status,
                'imi' => Boost::boost($response->getImage()->getId()),
                't' => $bilan,
                'c' => $charge,
                'tva' => $tva,
                'ad' => $response->getAvecDetail(),
                'cleWP' => $cleWp
            ];

            $results[] = $res;

            $releveJson = $this->getEntityManager()->getRepository('AppBundle:ReleveJson')
                ->getByReleve($response);

            if ($releveJson) $releveJson->setJson(json_encode($res));
            else
            {
                $releveJson = new ReleveJson();
                $releveJson
                    ->setReleve($response)
                    ->setJson(json_encode($res))
                    ->setAModifier(0)
                    ->setDateDerniereModif(new \DateTime());

                $em->persist($releveJson);
            }

            $response->setFlaguer($flaguer);
        }

        $em->flush();
        return $results;
    }

    /**
     * Maka ny liste-na relevé par Dossier
     * @param $dossierId
     * @param $exercice
     * @return array
     */
    function getListeReleveByDossier($dossierId, $exercice)
    {
        $releves = $this
            ->createQueryBuilder('r')
            ->innerJoin('r.image', 'image')
            ->addSelect('image')
            ->leftJoin('image.lot','lot')
            ->innerJoin('lot.dossier','dossier')
            ->where('lot.dossier = :dossier')
            ->setParameter('dossier', $dossierId)
            ->andWhere('image.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->getQuery()
            ->getResult();

        return $releves;
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @param Banque|null $banque
     * @param BanqueCompte|null $banqueCompte
     * @param bool $mouvement
     * @param int $limitQuery
     * @param int $offset
     * @return array|object
     */
    public function getRelevesNew(Dossier $dossier, $exercice, Banque $banque = null, BanqueCompte $banqueCompte = null, $mouvement = false,$limitQuery = -1, $offset = 0)
    {
        $exercices = '' . ($exercice - 1);
        $exercices .= ','. $exercice;
        $exercices .= ',' . ($exercice + 1);

        $demarrageCloture = $this
            ->getEntityManager()
            ->getRepository('AppBundle:TbimagePeriode')
            ->getAnneeMoisExercices($dossier,$exercice);

        /** @var \DateTime $dateDebut */
        $dateDebut = clone $demarrageCloture->d;
        /** @var \DateTime $dateFin */
        $dateFin = clone $demarrageCloture->c;

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $params =
            [
                'BANQUE_COMPTE_ID' => $banqueCompte->getId(),
                'EXERCICE' => $exercice
                /*'DATE_D1' => $dateDebut->format('Y-m-d'),
                'DATE_F1' => $dateFin->format('Y-m-d'),
                'DATE_D2' => $dateDebut->format('Y-m-d'),
                'DATE_F2' => $dateFin->format('Y-m-d')*/
            ];
        if ($mouvement)
        {
            $select = 'SELECT ROUND(SUM(r.credit) - SUM(r.debit),2) AS somme';
        }
        else
        {
            //$params ['DOSSIER_ID'] = $dossier->getId();
            /**
            (
            SELECT count(tic.image_id) AS isa
            FROM tva_imputation_controle tic
            JOIN image i ON (i.id = tic.image_id)
            JOIN lot l on (l.id = i.lot_id)
            JOIN separation sep on (sep.image_id = i.id)
            WHERE i.exercice in (".$exercices.")
            AND l.dossier_id = :DOSSIER_ID AND tic.image_flague_id IS NULL
            GROUP BY tic.image_id, sep.categorie_id
            HAVING (
            sum(montant_ttc) = (r.debit - r.credit) and sep.categorie_id in (10,12) OR
            sum(montant_ttc) = -(r.debit - r.credit) and sep.categorie_id not in (10,12)
            )
            LIMIT 1
            ) AS imag_f,
             */

            /**
            (
            SELECT COUNT(cle_t.id) AS nb_cle_trouve
            FROM cle cle_t
            WHERE r.libelle LIKE CONCAT('%',cle_t.cle,'%')
            ) AS nb_cle_trouve,*
             */

            $select = "
                SELECT r.id,                
                1 AS nb_cle_trouve,                
                
                cd.bilan_pcc, pcc_bil.compte AS compte_b, 
                cd.bilan_tiers, tier.compte_str AS compte_t, 
                cd.tva, pcc_tva.compte AS compte_tva, 
                cd.resultat, pcc_resultat.compte AS compte_resultat,
                cl.cle, cl.id as cl_id, 0 as cl_dossier 
            ";
        }

        /**
         *
         * FROM releve r
        JOIN image i ON (r.image_id = i.id)
        JOIN separation sep on (sep.image_id = i.id)
        JOIN imputation_controle ic on (ic.image_id = i.id)
        JOIN soussouscategorie ssc on (ic.soussouscategorie_id = ssc.id) "
         */

        $query = $select . " 
            FROM releve r
            JOIN image i ON (r.image_id = i.id)
            JOIN separation sep on (sep.image_id = i.id)  
            JOIN souscategorie ssc on (sep.souscategorie_id = ssc.id) ";

        if (!$mouvement)
            $query .= "
                LEFT JOIN cle_dossier cd on (cd.id = r.cle_dossier_id) 
                LEFT JOIN cle cl on (cl.id = cd.cle_id)  
                LEFT jOIN tiers tier on (tier.id = cd.bilan_tiers) 
                LEFT jOIN pcc pcc_bil on (pcc_bil.id = cd.bilan_pcc) 
                LEFT JOIN pcc pcc_tva on (pcc_tva.id = cd.tva) 
                LEFT JOIN pcc pcc_resultat on (pcc_resultat.id = cd.resultat) ";

        $query .= "
            WHERE r.banque_compte_id = :BANQUE_COMPTE_ID AND r.eclate <> 1  
            AND i.exercice = :EXERCICE 
            AND sep.souscategorie_id IS NOT NULL 
            AND ssc.id = 10 
            AND r.operateur_id IS NULL AND i.supprimer = 0                   
        "; //AND i.nom = 'ES0000IHL'
        // AND ((ic.periode_d1 BETWEEN :DATE_D1 AND :DATE_F1) OR (ic.periode_f1 BETWEEN :DATE_D2 AND :DATE_F2))
        //((ssc.libelle <> 'doublon' AND ssc.libelle_new <> 'doublon') OR ssc.libelle IS NULL)
        //AND r.libelle like '%STRIPE%'

        if (!$mouvement)
            $query .= " GROUP BY r.id ORDER BY r.date_releve ASC , r.id ASC ";

        //$limitQuery = 20;

        if ($limitQuery != -1 && !$mouvement)
            $query .= " LIMIT ".$limitQuery . " OFFSET " . ($limitQuery * ($offset));

        $prep = $pdo->prepare($query);
        $prep->execute($params);
        if ($mouvement) return $prep->fetch();

        $rels = $prep->fetchAll();

        //return $rels;

        $ids = [];
        $imageFlagues = [];
        $comptes = [];
        $cles = [];
        $clesTrouves = [];
        foreach ($rels as $rel)
        {
            $key = '_'.$rel->id;
            $ids[]  = $rel->id;
            $imageFlagues[$key] = 1;

            $bilan = null;
            $tva = null;
            $resultat = null;

            if ($rel->bilan_pcc || $rel->bilan_tiers)
                $bilan = (object)
                [
                    'id' => Boost::boost($rel->bilan_pcc ? $rel->bilan_pcc : $rel->bilan_tiers),
                    'l' => $rel->bilan_pcc ? $rel->compte_b : $rel->compte_t,
                    't' => $rel->bilan_pcc ? 0 : 1
                ];
            if ($rel->tva)
                $tva = (object)
                [
                    'id' => Boost::boost($rel->tva),
                    'l' => $rel->compte_tva,
                    't' => 0
                ];
            if ($rel->resultat)
                $resultat = (object)
                [
                    'id' => Boost::boost($rel->resultat),
                    'l' => $rel->compte_resultat,
                    't' => 0
                ];

            $comptes[$key] = (object)
            [
                'b' => $bilan,
                't' => $tva,
                'r' => $resultat
            ];

            $cle = null;
            if ($rel->cl_id)
                $cle = (object)
                [
                    'id' => Boost::boost($rel->cl_id),
                    'c' => $rel->cle,
                    'cd' => $rel->cl_dossier
                ];

            $clesTrouves[$key] = (intval($rel->nb_cle_trouve) > 0);

            $cles[$key] = $cle;
        }

        $releves = $this->createQueryBuilder('r')
            ->where('r.id IN (:ids)')
            ->setParameter('ids',$ids)
            ->orderBy('r.dateReleve')
            ->getQuery()
            ->getResult();

        return (object)
        [
            'if' => $imageFlagues,
            'rs' => $releves,
            'cs' => $comptes,
            'cles' => $cles,
            'clesTrouves' => $clesTrouves
        ];
    }

    /**
     * @param Releve $releve
     * @return object
     */
    public function getReleveNewOne(Releve $releve)
    {
        $exercice = $releve->getImage()->getExercice();
        $exercices = '' . ($exercice - 1);
        $exercices .= ','. $exercice;
        $exercices .= ',' . ($exercice + 1);

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "
            SELECT r.id,                
            (
                SELECT COUNT(cle_t.id) AS nb_cle_trouve
                FROM cle cle_t
                WHERE r.libelle LIKE CONCAT('%',cle_t.cle,'%') 
            ) AS nb_cle_trouve,                
                cd.bilan_pcc, pcc_bil.compte AS compte_b, 
                cd.bilan_tiers, tier.compte_str AS compte_t, 
                cd.tva, pcc_tva.compte AS compte_tva, 
                cd.resultat, pcc_resultat.compte AS compte_resultat,
                cl.cle, cl.id as cl_id, 0 as cl_dossier             
            FROM releve r 
                LEFT JOIN cle_dossier cd on (cd.id = r.cle_dossier_id)
                LEFT JOIN cle cl on (cl.id = cd.cle_id) 
                LEFT jOIN tiers tier on (tier.id = cd.bilan_tiers)
                LEFT jOIN pcc pcc_bil on (pcc_bil.id = cd.bilan_pcc)
                LEFT JOIN pcc pcc_tva on (pcc_tva.id = cd.tva)
                LEFT JOIN pcc pcc_resultat on (pcc_resultat.id = cd.resultat)            
            WHERE r.id = :ID
            GROUP BY r.id
        ";

        $prep = $pdo->prepare($query);
        $prep->execute(array(
            //'DOSSIER_ID' => $releve->getBanqueCompte()->getDossier()->getId(),
            'ID' => $releve->getId()
        ));
        $rels = $prep->fetchAll();

        $ids = [];
        $imageFlagues = [];
        $comptes = [];
        $cles = [];
        $clesTrouves = [];
        foreach ($rels as $rel)
        {
            $key = '_'.$rel->id;
            $ids[]  = $rel->id;
            $imageFlagues[$key] = 1;

            $bilan = null;
            $tva = null;
            $resultat = null;

            if ($rel->bilan_pcc || $rel->bilan_tiers)
                $bilan = (object)
                [
                    'id' => Boost::boost($rel->bilan_pcc ? $rel->bilan_pcc : $rel->bilan_tiers),
                    'l' => $rel->bilan_pcc ? $rel->compte_b : $rel->compte_t,
                    't' => $rel->bilan_pcc ? 0 : 1
                ];
            if ($rel->tva)
                $tva = (object)
                [
                    'id' => Boost::boost($rel->tva),
                    'l' => $rel->compte_tva,
                    't' => 0
                ];
            if ($rel->resultat)
                $resultat = (object)
                [
                    'id' => Boost::boost($rel->resultat),
                    'l' => $rel->compte_resultat,
                    't' => 0
                ];

            $comptes[$key] = (object)
            [
                'b' => $bilan,
                't' => $tva,
                'r' => $resultat
            ];

            $cle = null;
            if ($rel->cl_id)
                $cle = (object)
                [
                    'id' => Boost::boost($rel->cl_id),
                    'c' => $rel->cle,
                    'cd' => $rel->cl_dossier
                ];

            $clesTrouves[$key] = (intval($rel->nb_cle_trouve) > 0);

            $cles[$key] = $cle;
        }

        return (object)
        [
            'if' => $imageFlagues,
            'rs' => [$releve],
            'cs' => $comptes,
            'cles' => $cles,
            'clesTrouves' => $clesTrouves
        ];
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @param $banque
     * @param $banqueCompte
     * @param bool $mouvement
     * @return array
     */
    public function getReleves(Dossier $dossier, $exercice, $banque, $banqueCompte, $mouvement = false)
    {
        $req = $this->createQueryBuilder('r')
            ->leftJoin('r.image','i')
            ->leftJoin('i.lot','l')
            ->leftJoin('r.banqueCompte','bc')
            ->leftJoin('bc.banque','b')
            ->select('r')
            ->addSelect('i')
            ->where('l.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('i.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->andWhere('(r.eclate = 0 OR r.eclate = 2)')
            ->andWhere('r.banqueCompte IS NOT NULL');

        if ($banqueCompte != null)
            $req = $req->andWhere('bc = :banqueCompte')->setParameter('banqueCompte',$banqueCompte);
        elseif ($banque != null)
            $req = $req->andWhere('bc.banque = :banque')->setParameter('banque',$banque);

        if(!$mouvement) {
            return $req
                ->orderBy('r.dateReleve')
                ->addOrderBy('bc.numcompte')
                ->addOrderBy('b.nom')
                ->getQuery()->getResult();
        }
        else{
            return $req
                ->select('SUM(r.credit) - SUM(r.debit)')
                ->getQuery()->getResult();
        }
    }

    /**
     * @param Releve $releve
     * @return array
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getEclates(Releve $releve)
    {
        $releveEclates = [];
        if ($releve->getEclate() == 2)
        {
            if ($releve->getMaj() == 1)
            {
                $releveEclates = $this->createQueryBuilder('r')
                    ->where('r.releve = :releve')
                    ->setParameter('releve',$releve)
                    ->getQuery()->getResult();
            }
            else
            {
                $releves = $this->createQueryBuilder('r')
                    ->leftJoin('r.image','i')
                    ->leftJoin('i.lot','l')
                    ->where('r.id > :id')
                    ->setParameter('id',$releve->getId())
                    ->andWhere('l.dossier = :dossier')
                    ->setParameter('dossier',$releve->getImage()->getLot()->getDossier())
                    ->andWhere('r.releve IS NULL')
                    ->orderBy('r.id','ASC')
                    ->setMaxResults(200)
                    ->getQuery()->getResult();

                foreach ($releves as $r)
                {
                    if ($r->getEclate() != 1) break;
                    $releveEclates[] = $r;
                }

                foreach ($releveEclates as &$r)
                {
                    $r->setReleve($releve);
                    $releve->setMaj(1);
                }
                $this->getEntityManager()->flush();
            }
        }
        return $releveEclates;
    }

    /**
     * @param Releve $releve
     * @return null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getImageSoeur(Releve $releve)
    {
        $eclates = $this->getEclates($releve);
        return (count($eclates) > 0) ? $eclates[0]->getImage() : null;
    }

    /**
     * @param BanqueCompte $banqueCompte
     * @param $exercice
     * @param bool $debut
     * @return float
     */
    public function getSolde(BanqueCompte $banqueCompte,$exercice,$debut = true)
    {
        $demarrageCloture = $this
            ->getEntityManager()
            ->getRepository('AppBundle:TbimagePeriode')
            ->getAnneeMoisExercices($banqueCompte->getDossier(),$exercice);

        /** @var \DateTime $dateDebut */
        $dateDebut = clone $demarrageCloture->d;
        //$dateDebut->sub(new \DateInterval('P1D'));
        /** @var \DateTime $dateFin */
        $dateFin = clone $demarrageCloture->c;
        //$dateFin->add(new \DateInterval('P1D'));

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $banquecompte_id = $banqueCompte->getId();

        if($debut)
        {
            //LEFT JOIN souscategorie sc ON sc.id = ic.souscategorie_id
            //AND sc.libelle <> 'doublon' AND sc.categorie_id IN (SELECT id FROM categorie where CODE = 'CODE_BANQUE')
            //WHERE i.exercice = :exercice AND

        $query = "SELECT  ic.solde_debut AS solde_deb 
                  FROM image i
                  INNER JOIN releve r ON r.image_id = i.id
                  INNER JOIN lot l ON l.id = i.lot_id
                  INNER JOIN dossier d ON l.dossier_id = d.id
                  INNER JOIN site s ON s.id = d.site_id
                  INNER JOIN client c ON c.id = s.client_id
                  INNER JOIN imputation_controle ic ON ic.image_id = i.id
                  LEFT JOIN banque_compte bc ON ic.banque_compte_id = bc.id
                  LEFT JOIN banque b ON b.id = bc.banque_id
                  LEFT JOIN separation sep ON sep.image_id = i.id 
                  LEFT JOIN souscategorie sc ON sep.souscategorie_id = sc.id 
                  WHERE i.exercice = :exercice AND i.supprimer = 0 AND 
                    bc.id = :banquecompte_id AND sc.id = 10 
                  ORDER BY ic.periode_d1,r.num_releve,i.nom ASC LIMIT 1";
        //((ic.periode_d1 BETWEEN :debut1 AND :fin1) OR (ic.periode_f1 BETWEEN :debut2 AND :fin2)) AND

            $prep = $pdo->prepare($query);
            $prep->execute(array(
                'banquecompte_id' => $banquecompte_id,
                'exercice' => $exercice
                /*'debut1' => $dateDebut->format('Y-m-d'),
                'fin1' => $dateFin->format('Y-m-d'),
                'debut2' => $dateDebut->format('Y-m-d'),
                'fin2' => $dateFin->format('Y-m-d')*/
            ));

            $infoReleve = $prep->fetchAll();


            if(count($infoReleve) > 0) {
                return $infoReleve[0]->solde_deb;
            }
            else{
                return 0;
            }

        }
        else
        {
            /*
             *
             INNER JOIN releve r ON r.image_id = i.id
                  INNER JOIN lot l ON l.id = i.lot_id
                  INNER JOIN dossier d ON l.dossier_id = d.id
                  INNER JOIN site s ON s.id = d.site_id
                  INNER JOIN client c ON c.id = s.client_id
                  INNER JOIN imputation_controle ic ON ic.image_id = i.id
                  LEFT JOIN banque_compte bc ON ic.banque_compte_id = bc.id
                  LEFT JOIN banque b ON b.id = bc.banque_id
                  LEFT JOIN separation sep ON sep.image_id = i.id
                  LEFT JOIN souscategorie sc ON sep.souscategorie_id = sc.id
                  WHERE i.exercice = :exercice AND i.supprimer = 0 AND
        bc.id = :banquecompte_id AND sc.id = 10

            */

            $query = "SELECT  ic.solde_fin AS solde_fin 
                  FROM image i
                  INNER JOIN releve r ON r.image_id = i.id
                  INNER JOIN lot l ON l.id = i.lot_id
                  INNER JOIN dossier d ON l.dossier_id = d.id
                  INNER JOIN site s ON s.id = d.site_id
                  INNER JOIN client c ON c.id = s.client_id
                  INNER JOIN imputation_controle ic ON ic.image_id = i.id
                  LEFT JOIN banque_compte bc ON ic.banque_compte_id = bc.id
                  LEFT JOIN banque b ON b.id = bc.banque_id
                  LEFT JOIN separation sep ON sep.image_id = i.id 
                  LEFT JOIN souscategorie sc ON sep.souscategorie_id = sc.id 
                  WHERE i.exercice = :exercice AND i.supprimer = 0 AND  
                    bc.id = :banquecompte_id AND sc.id = 10  
                  ORDER BY ic.periode_d1 DESC,r.num_releve DESC,i.nom DESC LIMIT 1";
            //((ic.periode_d1 BETWEEN :debut1 AND :fin1) OR (ic.periode_f1 BETWEEN :debut2 AND :fin2)) AND

            $prep = $pdo->prepare($query);
            $prep->execute(array(
                'banquecompte_id' => $banquecompte_id,
                'exercice' => $exercice
                /*'debut1' => $dateDebut->format('Y-m-d'),
                'fin1' => $dateFin->format('Y-m-d'),
                'debut2' => $dateDebut->format('Y-m-d'),
                'fin2' => $dateFin->format('Y-m-d')*/
            ));

            $infoReleve = $prep->fetchAll();

            if(count($infoReleve) > 0) {
                return $infoReleve[0]->solde_fin;
            }
            else{
                return 0;
            }

        }
    }

    /**
     * @param $banqueCompte
     * @param $dossier
     * @param $cle
     * @param int $exercice
     * @return array
     */
    public function getRelevesByCle($banqueCompte,$dossier,$cle,$exercice = 0)
    {
        $results = [];
        $relevesTemps = $this->createQueryBuilder('r')
            ->innerJoin('AppBundle:ImputationControle', 'ic', 'WITH', 'ic.image = r.image')
            //->leftJoin('ic.souscategorie', 'sc')
            ->leftJoin('ic.soussouscategorie', 'ssc')
            ->innerJoin('r.image','i')
            ->innerJoin('i.lot','l')
            ->innerJoin('r.banqueCompte','bc')
            ->innerJoin('bc.banque','b')
            ->addSelect('i')
            ->where('i.exercice = :exercice')
            ->andWhere('(ssc.libelle != :doublon AND ssc.libelleNew != :doublon) OR ssc.libelle IS NULL')
            ->andWhere('r.libelle like :cle')
            ->andWhere('(r.eclate = 0 OR r.eclate = 2)')
            ->andWhere('ic.soussouscategorie IS NOT NULL')
            ->andWhere('r.pasCle = 0')
            ->andWhere('r.pasImage = 0')
            ->andWhere('r.imageFlague IS NULL')
            ->andWhere('r.operateur IS NULL')
            ->andWhere('i.supprimer = 0')
            ->setParameter('exercice',$exercice)
            ->setParameter('doublon','doublon')
            ->setParameter('cle','%'.$cle.'%');
            //->setParameter('min',$this->similarityMinimum());

        if (!is_null($banqueCompte)) $relevesTemps = $relevesTemps->andWhere('r.banqueCompte = :banqueCompte')->setParameter('banqueCompte',$banqueCompte);
        else $relevesTemps = $relevesTemps->andWhere('l.dossier = :dossier')->setParameter('dossier',$dossier);


        $relevesTemps = $relevesTemps
            ->orderBy('b.nom')
            ->addOrderBy('bc.numcompte')
            ->addOrderBy('r.dateReleve')
            ->getQuery()
            ->getResult();

        foreach ($relevesTemps as $relevesTemp)
        {
            //$relevesTemp = new Releve();
            if ($relevesTemp->getEclate() == 2) continue;
            if ($relevesTemp->getAvecDetail() == 1)
            {
                $releveDetails = $this->getEntityManager()->getRepository('AppBundle:ReleveDetail')
                    ->createQueryBuilder('rd')
                    ->where('rd.releve = :releve')
                    ->setParameter('releve',$relevesTemp)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult();

                if (count($releveDetails) > 0) continue;
            }

            $results[] = (object)
            [
                'id' => Boost::boost($relevesTemp->getId()),
                'i' => $relevesTemp->getImage()->getNom(),
                'ii' => Boost::boost($relevesTemp->getImage()->getId()),
                'd' => date_format($relevesTemp->getDateReleve(),'d/m/Y'),
                'l' => $relevesTemp->getLibelle(),
                'm' => -1 * ($relevesTemp->getDebit() - $relevesTemp->getCredit())
            ];
        }

        return $results;
    }

    /**
     * @return int
     */
    public function similarityMinimum()
    {
        return 80;
    }

    /**
     * @param Releve $releve
     * @param array $clesPasPieces
     * @param int $withPiece
     * @param null $comptes
     * @param null $cle
     * @param bool $cleTrouve
     * @param CfonbCode[] $cfonbCodeActives
     * @return null|object
     */
    function getStatus(Releve $releve, $clesPasPieces = [], $withPiece = 0, $comptes = null, $cle = null,$cleTrouve = false,$cfonbCodeActives = [])
    {
        /**
         * 0 : a categorise *
         * 1 : flaguer piece
         * 2 : flaguer cle *
         * 3 : piece trouve *
         * 4 : cle trouve
         */
        $status = null;
        $status = $this->getStatus_5($releve);
        if (!is_null($status)) return $status;

        // 1 : flaguer piece
        if ($releve->getImageFlague())
        {
            $result = $this->getStatus_1($releve);
            if ($result) return $result;
        }
        //2 : flaguer cle
        if ($releve->getCleDossier() && $cle)
        {
            return $this->getStatus_2($releve,$comptes,$cle);
        }
        // 3 : piece trouve
        /*if (intval($releve->getPasImage()) == 0)
        {*/
            $status = $this->getStatus_3($releve,$withPiece,$clesPasPieces);
            if (!is_null($status)) return $status;
        //}
        // 4 : cle trouve
        if ($releve->getPasCle() == 0 && $cleTrouve)
        {
            $status = $this->getStatus_4($releve,$cfonbCodeActives);
            if (!is_null($status)) return $status;
        }

        $t = 0;
        if ($releve->getPasImage() != 0) $t = 1;
        else if ($releve->getPasCle() != 0) $t = 2;

        return (object)
        [
            's' => 0,
            'so' => [],
            'id' => 0,
            'l' => '',
            't' => $t,
            'it' => 0
        ];
    }

    /**
     * @param Releve $releve
     * @param bool $lettragePartager
     * @return object
     */
    private function getStatus_1(Releve $releve, $lettragePartager = false)
    {
        $status = 1;
        /**
         * Liste soeurs
         */
        $firstImage = null;
        /**
         * libelle a afficher
         */
        $libelle = '';
        /**
         * type : image, image_flague, releve
         */
        $type = 0;
        /**
         * id du type
         */
        $idType = 0;

        $bilans = [];
        $tvas = [];
        $resultats = [];

        $bilan = null;
        $tva = null;
        $resultat = null;

        $id = Boost::boost($releve->getImageFlague()->getId());
        $srs = $this->getEntityManager()->getRepository('AppBundle:ImageFlague')->getSoeurs($releve->getImageFlague(),$releve,null,null,false,true);

        $soeursCount = count($srs->rel) + count($srs->tic) + count($srs->bsca);
        if ($soeursCount == 0)
        {
            $em = $this->getEntityManager();
            $em->remove($releve->getImageFlague());
            $em->flush();
            return null;
        }
        elseif ($soeursCount == 1)
        {
            /** @var Image $image */
            $image = null;
            $libelle = '';
            if (count($srs->tic) == 1)
            {
                $type = 0;
                foreach ($srs->tic as $key => $t)
                {
                    $image = $this->getEntityManager()->getRepository('AppBundle:Image')
                        ->find(intval($key));
                    /** @var TvaImputationControle[] $tvaImputationControles */
                    $tvaImputationControles = $t;

                    foreach ($tvaImputationControles as $tvaIc)
                    {
                        if (trim($libelle) == '')
                        {
                            $imputationControl = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                ->getImputationControle($tvaIc);

                            $libelle = $imputationControl ? $imputationControl->getRs() : '';
                            if (trim($libelle) == '')
                                $libelle = ((is_null($tvaIc->getTiers())) ? '' : $tvaIc->getTiers()->getIntitule());

                            if (trim($imputationControl->getNumFacture()) != '')
                                $libelle .= '-' . $imputationControl->getNumFacture();
                        }

                        if ($tvaIc->getTiers())
                        {
                            if (!array_key_exists('1-'.$tvaIc->getTiers()->getId(),$bilans))
                                $bilans['1-'.$tvaIc->getTiers()->getId()] = (object)
                                [
                                    'id' => Boost::boost($tvaIc->getTiers()->getId()),
                                    'l' => $tvaIc->getTiers()->getCompteStr(),
                                    'i' => $tvaIc->getTiers()->getIntitule(),
                                    't' => 1
                                ];
                        }
                        elseif ($tvaIc->getPccBilan())
                        {
                            if (!array_key_exists('0-'.$tvaIc->getPccBilan()->getId(),$bilans))
                                $bilans['0-'.$tvaIc->getPccBilan()->getId()] = (object)
                                [
                                    'id' => Boost::boost($tvaIc->getPccBilan()->getId()),
                                    'l' => $tvaIc->getPccBilan()->getCompte(),
                                    'i' => $tvaIc->getPccBilan()->getIntitule(),
                                    't' => 0
                                ];
                        }
                        if ($tvaIc->getPccTva())
                        {
                            if (!array_key_exists('0-'.$tvaIc->getPccTva()->getId(),$tvas))
                                $tvas['0-'.$tvaIc->getPccTva()->getId()] = (object)
                                [
                                    'id' => Boost::boost($tvaIc->getPccTva()->getId()),
                                    'l' => $tvaIc->getPccTva()->getCompte(),
                                    'i' => $tvaIc->getPccTva()->getIntitule(),
                                    't' => 0
                                ];
                        }
                        if ($tvaIc->getPcc())
                        {
                            if (!array_key_exists('0-'.$tvaIc->getPcc()->getId(),$resultats))
                                $resultats['0-'.$tvaIc->getPcc()->getId()] = (object)
                                [
                                    'id' => Boost::boost($tvaIc->getPcc()->getId()),
                                    'l' => $tvaIc->getPcc()->getCompte(),
                                    'i' => $tvaIc->getPcc()->getIntitule(),
                                    't' => 0
                                ];
                        }
                    }
                }
            }
            elseif (count($srs->bsca) == 1)
            {
                $type = 3;
                foreach ($srs->bsca as $key => $b)
                {
                    /** @var Image $image */
                    $image = $this->getEntityManager()->getRepository('AppBundle:Image')
                        ->find(intval($key));

                    if ($libelle == '')
                    {
                        /** @var Separation $separation */
                        $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                            ->findOneBy(['image'=>$image]);

                        if ($separation && $separation->getSouscategorie())
                            $libelle = $separation->getSouscategorie()->getLibelleNew();
                    }

                    /** @var BanqueSousCategorieAutre[] $bscas */
                    $bscas = $b;

                    $bilan = null;
                    $tva = null;
                    $resultat = null;

                    foreach ($bscas as $bsca)
                    {
                        if ($bsca->getCompteTiers())
                        {
                            if (!array_key_exists('1-'.$bsca->getCompteTiers()->getId(),$bilans))
                                $bilans['1-'.$bsca->getCompteTiers()->getId()] = (object)
                                [
                                    'id' => Boost::boost($bsca->getCompteTiers()->getId()),
                                    'l' => $bsca->getCompteTiers()->getCompteStr(),
                                    'i' => $bsca->getCompteTiers()->getIntitule(),
                                    't' => 1
                                ];
                        }
                        elseif ($bsca->getCompteBilan())
                        {
                            if (!array_key_exists('0-'.$bsca->getCompteBilan()->getId(),$bilans))
                                $bilans['0-'.$bsca->getCompteBilan()->getId()] = (object)
                                [
                                    'id' => Boost::boost($bsca->getCompteBilan()->getId()),
                                    'l' => $bsca->getCompteBilan()->getCompte(),
                                    'i' => $bsca->getCompteBilan()->getIntitule(),
                                    't' => 0
                                ];
                        }
                        if ($bsca->getCompteTva())
                        {
                            if (!array_key_exists('0-'.$bsca->getCompteTva()->getId(),$tvas))
                                $tvas['0-'.$bsca->getCompteTva()->getId()] = (object)
                                [
                                    'id' => Boost::boost($bsca->getCompteTva()->getId()),
                                    'l' => $bsca->getCompteTva()->getCompte(),
                                    'i' => $bsca->getCompteTva()->getIntitule(),
                                    't' => 0
                                ];
                        }
                        if ($bsca->getCompteChg())
                        {
                            if (!array_key_exists('0-'.$bsca->getCompteChg()->getId(),$resultats))
                                $resultats['0-'.$bsca->getCompteChg()->getId()] = (object)
                                [
                                    'id' => Boost::boost($bsca->getCompteChg()->getId()),
                                    'l' => $bsca->getCompteChg()->getCompte(),
                                    'i' => $bsca->getCompteChg()->getIntitule(),
                                    't' => 0
                                ];
                        }
                    }
                }
            }
            else
            {
                $type = 2;
                foreach ($srs->rel as $key => $t)
                {
                    $image = $this->getEntityManager()->getRepository('AppBundle:Image')
                        ->find(intval($key));

                    /** @var Releve[] $releves */
                    $releves = $t;
                    foreach ($releves as $rel) $libelle = $rel->getLibelle();
                    break;
                }
            }

            $idType = Boost::boost($image->getId());
        }
        else
        {
            if (!$lettragePartager)
            {
                $this->getEntityManager()->getRepository('AppBundle:ImageFlague')
                    ->departagerLettrage($releve->getImageFlague());
                return $this->getStatus_1($releve,true);
            }

            $libelle = 'Multiple';
            $type = 1;
        }

        /** Montant plus important en pivot */
        /** @var Releve $pivot */
        $pivot = $releve;
        if (count($srs->rel) > 0)
        {
            $releveSoeursImages = $srs->rel;
            foreach ($releveSoeursImages as $releveSoeursImage)
            {
                foreach ($releveSoeursImage as $item)
                {
                    /** @var Releve $rel */
                    $rel = $item;

                    if (abs($rel->getDebit() - $rel->getCredit()) > abs($pivot->getDebit() - $pivot->getCredit()))
                        $pivot = $rel;
                }
            }

            if ($pivot->getId() != $releve->getId())
            {
                $srs = $this->getEntityManager()->getRepository('AppBundle:ImageFlague')->getSoeurs($releve->getImageFlague(),$pivot,null,null,false,true);
            }
        }

        $releve = $pivot;
        $credit = ($releve->getCredit() - $releve->getDebit() > 0);
        $montantReleve = floatval($releve->getCredit() - $releve->getDebit());

        $typeCompta = $releve->getEngagementTresorerie();

        $sDebit = 0;
        $sCredit = 0;

        if ($soeursCount > 0)
        {
            $tvaImputationsControlesImages = $srs->tic;

            $allAvoir = true;
            foreach ($tvaImputationsControlesImages as $ki => $item)
            {
                $img = $this->getEntityManager()->getRepository('AppBundle:Image')
                    ->find($ki);

                $ic = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                    ->getByImage($img);

                if ($ic && $ic->getTypePiece() && $ic->getTypePiece()->getId() > 1)
                {
                    $allAvoir = false;
                    break;
                }
            }

            foreach ($tvaImputationsControlesImages as $tvaImputationsControlesImage)
            {
                $sensOppose = false;
                $inverse = false;

                $signe = ($tvaImputationsControlesImage[0]->getMontantTtc() < 0);
                if (count($tvaImputationsControlesImage) > 1)
                {
                    foreach ($tvaImputationsControlesImage as $item)
                    {
                        /** @var TvaImputationControle $tvaImputationControle */
                        $tvaImputationControle = $item;
                        $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                        if ($signe != $signeItem)
                        {
                            $sensOppose = true;
                            break;
                        }
                    }
                }

                foreach ($tvaImputationsControlesImage as $item)
                {
                    /** @var TvaImputationControle $tvaImputationControle */
                    $tvaImputationControle = $item;
                    $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                    $montant = abs($tvaImputationControle->getMontantTtc());
                    /** @var Separation $separation */
                    $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                        ->createQueryBuilder('s')
                        ->where('s.image = :image')
                        ->setParameter('image',$tvaImputationControle->getImage())
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

                    $cr = $credit;
                    if ($separation)
                    {
                        if (in_array($separation->getCategorie()->getId(),[10,12,9,13]))
                        {
                            $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                ->getImputationControle($tvaImputationControle);

                            if (!$allAvoir && $imputationControle && $imputationControle->getTypePiece() && intval($imputationControle->getTypePiece()->getId()) == 1)
                                $cr = !$cr;
                        }
                    }

                    $montant = abs($montant);
                    if ($sensOppose && $signe != $signeItem)
                    {
                        $montant *= -1;
                        $cr = !$cr;
                    }

                    if ($typeCompta == 0)
                    {
                        if ($tvaImputationControle->getPccBilan() || $tvaImputationControle->getTiers())
                        {
                            if ($cr) $sDebit += abs($montant);
                            else $sCredit += abs($montant);
                        }
                    }
                    else
                    {
                        $montantTva = 0;
                        if ($tvaImputationControle->getPccTva() && $tvaImputationControle->getTvaTaux())
                        {
                            $coeffTva = 1 + floatval($tvaImputationControle->getTvaTaux()->getTaux() / 100);
                            $mHT = $montant / $coeffTva;
                            $montantTva = $montant - $mHT;
                        }

                        if ($tvaImputationControle->getPcc())
                        {
                            if ($cr) $sDebit += abs($montant - $montantTva);
                            else $sCredit += abs($montant - $montantTva);
                        }

                        if ($montantTva != 0)
                        {
                            if ($cr) $sDebit += abs($montantTva);
                            else $sCredit += abs($montantTva);
                        }
                    }
                }
            }

            $releveSoeursImages = $srs->rel;
            foreach ($releveSoeursImages as $releveSoeursImage)
            {
                foreach ($releveSoeursImage as $item)
                {
                    /** @var Releve $rel */
                    $rel = $item;

                    $sDebit += floatval($rel->getDebit());
                    $sCredit += floatval($rel->getCredit());
                }
            }

            $releveSoeursBanquesAutres = $srs->bsca;
            if (count($releveSoeursBanquesAutres) > 0)
            {
                $pccAttente = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                    ->getPccAttenteBanque($releve->getBanqueCompte()->getDossier(),!$credit);

                foreach ($releveSoeursBanquesAutres as $releveSoeursBanquesAutre)
                {
                    foreach ($releveSoeursBanquesAutre as $item)
                    {
                        /** @var BanqueSousCategorieAutre $banqueSousCateogrieAutre */
                        $banqueSousCateogrieAutre = $item;
                        $isEngagement = intval($banqueSousCateogrieAutre->getEngagementTresorerie()) == 0;

                        $tvaImputationsControlesImages = [];
                        /** @var BanqueSousCategorieAutre[] $banqueSousCategoriesSoeurs */
                        $banqueSousCategoriesSoeurs = [];

                        $countSoeur = 0;
                        if ($banqueSousCateogrieAutre->getImageFlague2())
                        {
                            $tvaImputationsControlesImages = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                                ->getChildImageFlagues($banqueSousCateogrieAutre->getImageFlague2(),null,true);

                            $banqueSousCategoriesSoeurs = $this->getEntityManager()->getRepository('AppBundle:BanqueSousCategorieAutre')
                                ->getChildImageFlagues($banqueSousCateogrieAutre->getImageFlague2(),$banqueSousCateogrieAutre,true);

                            $countSoeur = count($tvaImputationsControlesImages) + count($banqueSousCategoriesSoeurs);

                            if ($countSoeur == 0)
                            {
                                /*$this->getEntityManager()->remove($banqueSousCateogrieAutre->getImageFlague2());
                                $this->getEntityManager()->flush();*/
                            }
                        }

                        if ($countSoeur > 0)
                        {
                            $isCr = $banqueSousCateogrieAutre->getMontant() < 0;
                            $allAvoir = true;
                            foreach ($tvaImputationsControlesImages as $ki => $it)
                            {
                                $img = $this->getEntityManager()->getRepository('AppBundle:Image')
                                    ->find($ki);

                                $ic = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                    ->getByImage($img);

                                if ($ic && $ic->getTypePiece() && $ic->getTypePiece()->getId() > 1)
                                {
                                    $allAvoir = false;
                                    break;
                                }
                            }

                            foreach ($tvaImputationsControlesImages as $tvaImputationsControlesImage)
                            {
                                $sensOppose = false;
                                $inverse = false;

                                $signe = ($tvaImputationsControlesImage[0]->getMontantTtc() < 0);
                                if (count($tvaImputationsControlesImage) > 1)
                                {
                                    foreach ($tvaImputationsControlesImage as $item)
                                    {
                                        /** @var TvaImputationControle $tvaImputationControle */
                                        $tvaImputationControle = $item;
                                        $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                                        if ($signe != $signeItem)
                                        {
                                            $sensOppose = true;
                                            break;
                                        }
                                    }
                                }

                                foreach ($tvaImputationsControlesImage as $item)
                                {
                                    /** @var TvaImputationControle $tvaImputationControle */
                                    $tvaImputationControle = $item;
                                    $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                                    $montant = abs($tvaImputationControle->getMontantTtc());
                                    /** @var Separation $separation */
                                    $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                                        ->createQueryBuilder('s')
                                        ->where('s.image = :image')
                                        ->setParameter('image',$tvaImputationControle->getImage())
                                        ->setMaxResults(1)
                                        ->getQuery()
                                        ->getOneOrNullResult();

                                    $cr = $isCr;
                                    if ($separation)
                                    {
                                        if (in_array($separation->getCategorie()->getId(),[10,12,9,13]))
                                        {
                                            $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                                ->getImputationControle($tvaImputationControle);

                                            if (!$allAvoir && $imputationControle && $imputationControle->getTypePiece() && intval($imputationControle->getTypePiece()->getId()) == 1)
                                                $cr = !$cr;
                                        }
                                    }

                                    $montant = abs($montant);
                                    if ($sensOppose && $signe != $signeItem)
                                    {
                                        $cr = !$cr;
                                    }

                                    if ($isEngagement)
                                    {
                                        if ($tvaImputationControle->getPccBilan() || $tvaImputationControle->getTiers())
                                        {
                                            if ($cr) $sDebit += abs($montant);
                                            else $sCredit += abs($montant);
                                        }
                                    }
                                    else
                                    {
                                        $montantTva = 0;
                                        if ($tvaImputationControle->getPccTva() && $tvaImputationControle->getTvaTaux())
                                        {
                                            $coeffTva = 1 + floatval($tvaImputationControle->getTvaTaux()->getTaux() / 100);
                                            $mHT = $montant / $coeffTva;
                                            $montantTva = $montant - $mHT;
                                        }

                                        if ($tvaImputationControle->getPcc())
                                        {
                                            if ($cr) $sDebit += abs($montant - $montantTva);
                                            else $sCredit += abs($montant - $montantTva);
                                        }

                                        if ($montantTva != 0)
                                        {
                                            if ($cr) $sDebit += abs($montantTva);
                                            else $sCredit += abs($montantTva);
                                        }
                                    }
                                }
                            }


                            foreach ($banqueSousCategoriesSoeurs as $banqueSousCategoriesSoeur)
                            {
                                if ($banqueSousCategoriesSoeur->getCompteTiers() || $banqueSousCategoriesSoeur->getCompteBilan())
                                {
                                    if ($credit) $sDebit += abs($banqueSousCategoriesSoeur->getMontant());
                                    else $sCredit += abs($banqueSousCategoriesSoeur->getMontant());
                                }
                                else
                                {
                                    $mTtc = abs($banqueSousCategoriesSoeur->getMontant());
                                    $coeffTva = 1;
                                    if ($banqueSousCategoriesSoeur->getCompteTva() && $banqueSousCategoriesSoeur->getTvaTaux())
                                        $coeffTva += $banqueSousCategoriesSoeur->getTvaTaux()->getTaux() / 100;
                                    $mHt = $mTtc / $coeffTva;
                                    $mTva = $mTtc - $mHt;

                                    if ($mTva != 0)
                                    {
                                        if ($banqueSousCategoriesSoeur->getCompteChg())
                                        {
                                            if ($credit) $sDebit += abs($mTva);
                                            else $sCredit += abs($mTva);
                                        }
                                        else
                                        {
                                            if ($credit) $sDebit += abs($mTtc);
                                            else $sCredit += abs($mTtc);
                                        }
                                    }

                                    if ($credit) $sDebit += abs($mHt);
                                    else $sCredit += abs($mHt);

                                }
                            }
                        }
                        else
                        {
                            if ($banqueSousCateogrieAutre->getCompteTiers() || $banqueSousCateogrieAutre->getCompteBilan())
                            {
                                if ($credit) $sDebit += abs($banqueSousCateogrieAutre->getMontant());
                                else $sCredit += abs($banqueSousCateogrieAutre->getMontant());
                            }
                            else
                            {
                                $mTtc = abs($banqueSousCateogrieAutre->getMontant());
                                $coeffTva = 1;
                                if ($banqueSousCateogrieAutre->getCompteTva() && $banqueSousCateogrieAutre->getTvaTaux())
                                    $coeffTva += $banqueSousCateogrieAutre->getTvaTaux()->getTaux() / 100;
                                $mHt = $mTtc / $coeffTva;
                                $mTva = $mTtc - $mHt;

                                if ($mTva != 0)
                                {
                                    if ($banqueSousCateogrieAutre->getCompteChg())
                                    {
                                        if ($credit) $sDebit += abs($mTva);
                                        else $sCredit += abs($mTva);
                                    }
                                    else
                                    {
                                        if ($credit) $sDebit += abs($mTtc);
                                        else $sCredit += abs($mTtc);
                                    }
                                }

                                if ($credit) $sDebit += abs($mHt);
                                else $sCredit += abs($mHt);
                            }
                        }
                    }
                }
            }
        }

        return (object)
        [
            's' => $status,
            'so' => [],
            'id' => $id,
            'l' => $libelle,
            't' => $type,
            'it' => $idType,
            'tic' => $srs->tic,
            'bilan' => $bilan,
            'tva' => $tva,
            'resultat' => $resultat,
            'bilans' => array_values($bilans),
            'tvas' => array_values($tvas),
            'resultats' => array_values($resultats),
            'diff' => round($montantReleve - $sDebit + $sCredit,2)
        ];
    }

    /**
     * @param Releve $releve
     * @param \stdClass $comptes
     * @param \stdClass $cle
     * @return object
     */
    public function getStatus_2New(Releve $releve, $comptes = null, $cle = null)
    {
        $status = 2;

        $soeurs = [];
        $id = $cle->id;
        $libelle = $cle->c;
        $type = 0;
        $idType = 0;

        $bilan = $comptes->b;
        $tva = $comptes->t;
        $resultat = $comptes->r;

        $statPiece = ($releve->getCleDossier()->getPasPiece() == 1) ?
            null : $this->getStatus_3($releve,5,[]);

        //$statPiece = null;
        
        $montant = 0;
        $releveExts = [];
        $releveExtReste = null;
        $find = '';
        if (!$bilan && !$tva && !$resultat)
        {
            $reste = $this->getEntityManager()->getRepository('AppBundle:CleDossierExt')
                ->getRestes($releve->getCleDossier());

            $idsNotIns = $reste ? [$reste->getId()] : [];

            $cleDossierExts = $this->getEntityManager()->getRepository('AppBundle:CleDossierExt')
                ->getForCleDossier($releve->getCleDossier(), $idsNotIns);

            foreach ($cleDossierExts as $cleDossierExt)
            {
                /*if ($find == '') $find = $this->getEntityManager()->getRepository('AppBundle:CleDossierExt')
                    ->getAddByReleve($releve, $cleDossierExt);
                continue;*/

                $releveExts[] = $this->getEntityManager()->getRepository('AppBundle:CleDossierExt')
                    ->getAddByReleve($releve, $cleDossierExt);
            }

            if (true)
            {
                foreach ($releveExts as &$ext)
                {
                    if ($ext && $ext->re)
                    {
                        /** @var ReleveExt $releveExt */
                        $releveExt = $ext->re;
                        if ($releveExt->getImageFlague())
                        {
                            $m = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                                ->getMontantImageFlague($releveExt->getImageFlague());
                            $montant += $m;
                            $ext->m = $m;
                        }
                        else $montant += floatval($releveExt->getMontant());
                    }
                }

                if (abs($montant) != abs($releve->getDebit() - $releve->getCredit()) && $reste)
                {
                    $releveExtReste = (object)
                    [
                        'm' => abs($montant) - abs($releve->getDebit() - $releve->getCredit()),
                        'cde' => $reste
                    ];
                }
            }
        }

        return (object)
        [
            's' => $status,
            'so' => $soeurs,
            'id' => $id,
            'l' =>$libelle,
            't' => $type,
            'it' => $idType,
            'bilan' => $bilan,
            'tva' => $tva,
            'resultat' => $resultat,
            'sPiece' => $statPiece,
            'exts' => (object)
            [
                'releveExts' => $releveExts,
                'releveExtReste' => $releveExtReste
            ],
            'find' => $find
        ];
    }

    /**
     * @param Releve $releve
     * @param \stdClass $comptes
     * @param \stdClass $cle
     * @return object
     */
    private function getStatus_2(Releve $releve, $comptes = null, $cle = null)
    {
        return $this->getStatus_2New($releve, $comptes, $cle);

        $status = 2;
        /**
         * Liste soeurs
         */
        $firstImage = null;
        /**
         * libelle a afficher
         */
        //$libelle = $releve->getCleDossier()->getCle()->getCle();
        $libelle = $cle->c;
        /**
         * type : image, image_flague, releve
         */
        $type = 0;
        /**
         * id du type 1:tous dossier, 0:dossier spec
         */
        $idType = ($cle->cd) ? 1 : 0;

        $statPiece = ($releve->getCleDossier()->getPasPiece() == 1) ?
            null : $this->getStatus_3($releve,5,[]);

        $id = $cle->id;
        $soeurs = [];

        $bilan = $comptes->b;
        $tva = $comptes->t;
        $resultat = $comptes->r;

        return (object)
        [
            's' => $status,
            'so' => $soeurs,
            'id' => $id,
            'l' =>$libelle,
            't' => $type,
            'it' => $idType,
            'bilan' => $bilan,
            'tva' => $tva,
            'resultat' => $resultat,
            'sPiece' => $statPiece
        ];
    }

    /**
     * @param Releve $releve
     * @param bool $entity
     * @return array|mixed
     */
    public function ImagesNonLettrables(Releve $releve, $entity = true)
    {
        $ids = [0];
        if (trim($releve->getNonLettrable()) != '') $ids = json_decode($releve->getNonLettrable());
        if (count($ids) == 0) $ids = [0];
        if (!$entity) return $ids;

        return $this->getEntityManager()->getRepository('AppBundle:Image')
                ->createQueryBuilder('i')
                ->where('i.id IN (:ids)')
                ->setParameter('ids',$ids)
                ->getQuery()
                ->getResult();
    }

    /**
     * @param Releve $releve
     * @param int $imageAAfecter
     * @param Cle[] $clesPasPieces
     * @return null|object
     */
    private function getStatus_3(Releve $releve, $imageAAfecter = 0, $clesPasPieces = [])
    {
        $hasClePasDepiece = false;
        foreach ($clesPasPieces as $clesPasPiece)
        {
            if (strpos($releve->getLibelle(),$clesPasPiece->getCle()) !== false)
            {
                $hasClePasDepiece = true;
                break;
            }
        }

        if ($hasClePasDepiece) return null;

        $idsNonLettrables = $this->ImagesNonLettrables($releve,false);
        $nonLettrables = '';

        for ($i = 0; $i < count($idsNonLettrables); $i++)
        {
            $nonLettrables .= intval($idsNonLettrables[$i]);
            if ($i != count($idsNonLettrables) - 1) $nonLettrables .= ',';
        }

        $exercice = $releve->getImage()->getExercice();
        $exercices = '' . ($exercice - 1);
        $exercices .= ','. $exercice;
        $exercices .= ',' . ($exercice + 1);

        $params = [
            'DOSSIER_ID' => $releve->getBanqueCompte()->getDossier()->getId(),
            'montant' => $releve->getDebit() - $releve->getCredit(),
            'montant_' => $releve->getDebit() - $releve->getCredit(),
            'lDoublon' => 'DOUBLON'
        ];

        $req = "
            SELECT COUNT(tic.image_id) AS isa
            FROM tva_imputation_controle tic
            JOIN image i ON (i.id = tic.image_id)
            JOIN lot l on (l.id = i.lot_id)
            JOIN separation sep on (sep.image_id = i.id)
            JOIN imputation_controle ic on (tic.image_id = ic.image_id) 
            LEFT JOIN souscategorie sc ON (sc.id = sep.souscategorie_id) 
            WHERE i.exercice in (".$exercices.") 
                AND l.dossier_id = :DOSSIER_ID AND tic.image_flague_id IS NULL 
                AND i.id NOT IN (".$nonLettrables.") AND (sc.libelle_new <> :lDoublon OR sep.souscategorie_id IS NULL)  
            GROUP BY tic.image_id, sep.categorie_id, ic.type_piece_id 
            HAVING 
            (
                ROUND(sum(tic.montant_ttc),2) = ROUND(:montant,2) and ((sep.categorie_id in (10,12) and ic.type_piece_id <> 1) OR (sep.categorie_id in (9,13) and ic.type_piece_id = 1)) OR 
                ROUND(sum(tic.montant_ttc),2) = -ROUND(:montant_,2) and not((sep.categorie_id in (10,12) and ic.type_piece_id <> 1) OR (sep.categorie_id in (9,13) and ic.type_piece_id = 1))
            )
            LIMIT 1 
        ";

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $res = $prep->fetch();

        $status = 3;
        /**
         * Liste soeurs
         */
        $soeurs = [];
        /**
         * si imputation une piece
         */
        $firstImage = null;
        /**
         * id imputation image_flague,cle_dossier
         */
        $id = 0;
        /**
         * libelle a afficher
         */
        $libelle = '';
        /**
         * type : image, image_flague, releve
         */
        $type = 0;
        /**
         * id du type
         */
        $idType = 0;

        if ($res && intval($res->isa) != 0)
        {
            return (object)
            [
                's' => $status,
                'so' => $soeurs,
                'id' => $id,
                'l' => $libelle,
                't' => $type,
                'it' => $idType,
                'nb' => 1
            ];
        }

        return null;

        return $this->getEntityManager()->getRepository('AppBundle:BanqueSousCategorieAutre')
            ->checkIfHasMontant($releve);
    }


    private function getStatus_4_sauve(Releve $releve)
    {
        $status = 4;
        /**
         * Liste soeurs
         */
        $firstImage = null;
        /**
         * libelle a afficher
         */
        $libelle = '';
        /**
         * type : image, image_flague, releve
         */
        $type = 0;
        /**
         * id du type
         */
        $idType = 0;

        $em = $this->getEntityManager();

        $statusTemp = $em->getRepository('AppBundle:Cle')->getStatusCles($releve);
        if ($statusTemp->s == 0) return null;

        $id = 0;
        $soeurs = $statusTemp->c;

        if ($statusTemp->s == 1)
        {
            $id = $statusTemp->c->getId();
            /** @var CleDossier[] $cleDossiers */
            $cleDossiers = $this->getEntityManager()->getRepository('AppBundle:CleDossier')
                ->createQueryBuilder('cd')
                ->where('cd.dossier = :dossier')
                ->andWhere('cd.cle = :cle')
                ->setParameters(array(
                    'dossier' => $releve->getBanqueCompte()->getDossier(),
                    'cle' => $statusTemp->c
                ))
                ->getQuery()
                ->getResult();

            if (count($cleDossiers) == 1)
            {
                $cleDossier = $cleDossiers[0];
                if ($cleDossier->getTypeCompta() != 2)
                {
                    $releve->setCleDossier($cleDossier);
                    $em->flush();

                    $bilan = null;
                    $resultat = null;
                    $tva = null;

                    if (in_array(intval($cleDossier->getTypeCompta()),[0,3]))
                    {
                        $bilan = (object)
                        [
                            'id' => Boost::boost($cleDossier->getBilanTiers() ? $cleDossier->getBilanTiers()->getId() : $cleDossier->getBilanPcc()->getId()),
                            'l' => $cleDossier->getBilanTiers() ? $cleDossier->getBilanTiers()->getCompteStr() : $cleDossier->getBilanPcc()->getCompte(),
                            't' => $cleDossier->getBilanTiers() ? 0 : 1
                        ];

                        if ($cleDossier->getResultat())
                            $resultat = (object)
                            [
                                'id' => Boost::boost($cleDossier->getResultat()->getId()),
                                'l' => $cleDossier->getResultat()->getCompte(),
                                't' => 0
                            ];
                        if ($cleDossier->getTva())
                            $tva = (object)
                            [
                                'id' => Boost::boost($cleDossier->getTva()->getId()),
                                'l' => $cleDossier->getTva()->getCompte(),
                                't' => 0
                            ];
                    }
                    else
                    {
                        if ($cleDossier->getResultat())
                            $resultat = (object)
                            [
                                'id' => Boost::boost($cleDossier->getResultat()->getId()),
                                'l' => $cleDossier->getResultat()->getCompte(),
                                't' => 0
                            ];
                        if ($cleDossier->getTva())
                            $tva = (object)
                            [
                                'id' => Boost::boost($cleDossier->getTva()->getId()),
                                'l' => $cleDossier->getTva()->getCompte(),
                                't' => 0
                            ];
                    }

                    $cle = (object)
                    [
                        'id' => Boost::boost($cleDossier->getCle()->getId()),
                        'c' => $cleDossier->getCle()->getCle(),
                        'cd' => $cleDossier->getDossier()->getId()
                    ];

                    return $this->getStatus_2(
                        $releve,
                        (object)
                        [
                            'b' => $bilan,
                            't' => $tva,
                            'r' => $resultat
                        ],
                        $cle
                    );
                }
            }
        }

        return (object)
        [
            's' => $status,
            'so' => $soeurs,
            'id' => $id,
            'l' =>$libelle,
            't' => $type,
            'it' => $idType,
            'nb' => ($statusTemp->s == 1) ? 1 : count($statusTemp->c)
        ];
    }


    /**
     * @param Releve $releve
     * @param CfonbCode[] $cfonbCodeActives
     * @return null|object
     */
    private function getStatus_4(Releve $releve,$cfonbCodeActives = [])
    {
        $status = 4;
        /**
         * Liste soeurs
         */
        $firstImage = null;
        /**
         * libelle a afficher
         */
        $libelle = '';
        /**
         * type : image, image_flague, releve
         */
        $type = 0;
        /**
         * id du type
         */
        $idType = 0;

        $em = $this->getEntityManager();

        $statusTemp = $em->getRepository('AppBundle:Cle')->getStatusCles($releve,$cfonbCodeActives);

        if ($statusTemp->s == 0) return null;

        $id = 0;
        $soeurs = $statusTemp->c;

        if ($statusTemp->s == 1)
        {
            $id = $statusTemp->c->getId();
            /** @var CleDossier[] $cleDossiers */
            $cleDossiers = $this->getEntityManager()->getRepository('AppBundle:CleDossier')
                ->createQueryBuilder('cd')
                ->where('cd.dossier = :dossier')
                ->andWhere('cd.cle = :cle')
                ->setParameters(array(
                    'dossier' => $releve->getBanqueCompte()->getDossier(),
                    'cle' => $statusTemp->c
                ))
                ->getQuery()
                ->getResult();

            if (count($cleDossiers) == 1)
            {
                $cleDossier = $cleDossiers[0];
                if ($cleDossier->getTypeCompta() != 2)
                    return $this->setCleDossierToReleve($releve,$cleDossier);
            }
        }
        elseif ($statusTemp->s == 2)
        {
            /** @var Cle[] $cles */
            /*$cles = $statusTemp->c;
            $clePlusProche = $cles[0];

            foreach ($cles as $cl)
            {
                if (strlen($cl->getCle()) > strlen($clePlusProche->getCle()))
                    $clePlusProche = $cl;
            }

            $cleDossiers = $this->getEntityManager()->getRepository('AppBundle:CleDossier')
                ->createQueryBuilder('cd')
                ->where('cd.dossier = :dossier')
                ->andWhere('cd.cle = :cle')
                ->setParameters(array(
                    'dossier' => $releve->getBanqueCompte()->getDossier(),
                    'cle' => $clePlusProche
                ))
                ->getQuery()
                ->getResult();

            if (count($cleDossiers) == 1)
            {
                $cleDossier = $cleDossiers[0];
                if ($cleDossier->getTypeCompta() != 2)
                    return $this->setCleDossierToReleve($releve,$cleDossier);
            }*/
        }

        return (object)
        [
            's' => $status,
            'so' => $soeurs,
            'id' => $id,
            'l' =>$libelle,
            't' => $type,
            'it' => $idType,
            'nb' => ($statusTemp->s == 1) ? 1 : count($statusTemp->c)
        ];
    }

    private function setCleDossierToReleve(Releve $releve, CleDossier $cleDossier)
    {
        $em = $this->getEntityManager();
        $releve->setCleDossier($cleDossier);
        $em->flush();

        $bilan = null;
        $resultat = null;
        $tva = null;

        if (in_array(intval($cleDossier->getTypeCompta()),[0,3]))
        {
            if ($cleDossier->getBilanTiers() || $cleDossier->getBilanPcc())
                $bilan = (object)
                [
                    'id' => Boost::boost($cleDossier->getBilanTiers() ? $cleDossier->getBilanTiers()->getId() : $cleDossier->getBilanPcc()->getId()),
                    'l' => $cleDossier->getBilanTiers() ? $cleDossier->getBilanTiers()->getCompteStr() : $cleDossier->getBilanPcc()->getCompte(),
                    't' => $cleDossier->getBilanTiers() ? 0 : 1
                ];

            if ($cleDossier->getResultat())
                $resultat = (object)
                [
                    'id' => Boost::boost($cleDossier->getResultat()->getId()),
                    'l' => $cleDossier->getResultat()->getCompte(),
                    't' => 0
                ];
            if ($cleDossier->getTva())
                $tva = (object)
                [
                    'id' => Boost::boost($cleDossier->getTva()->getId()),
                    'l' => $cleDossier->getTva()->getCompte(),
                    't' => 0
                ];
        }
        else
        {
            if ($cleDossier->getResultat())
                $resultat = (object)
                [
                    'id' => Boost::boost($cleDossier->getResultat()->getId()),
                    'l' => $cleDossier->getResultat()->getCompte(),
                    't' => 0
                ];
            if ($cleDossier->getTva())
                $tva = (object)
                [
                    'id' => Boost::boost($cleDossier->getTva()->getId()),
                    'l' => $cleDossier->getTva()->getCompte(),
                    't' => 0
                ];
        }

        $cle = (object)
        [
            'id' => Boost::boost($cleDossier->getCle()->getId()),
            'c' => $cleDossier->getCle()->getCle(),
            'cd' => $cleDossier->getDossier()->getId()
        ];

        return $this->getStatus_2(
            $releve,
            (object)
            [
                'b' => $bilan,
                't' => $tva,
                'r' => $resultat
            ],
            $cle
        );
    }

    /**
     * @param Releve $releve
     * @return null|object
     */
    private function getStatus_5(Releve $releve)
    {
        if ($releve->getEcritureChange() == 1)
        {
            $status = 5;
            $libelle = '';
            $type = 0;
            $idType = 0;

            return (object)
            [
                's' => $status,
                'so' => [],
                'id' => 0,
                'l' => $libelle,
                't' => $type,
                'it' => $idType,
            ];
        }

        return null;
    }

    /**
     * @param Dossier $dossier
     * @param Banque|null $banque
     * @param BanqueCompte|null $bc
     * @param int $exercice
     * @param int $type
     * @param Releve|null $releve
     * @param array $intervals
     * @param int $dateType
     * @param Client|null $client
     * @param Utilisateur $user
     * @param array $dossierArray
     * @return Releve[]|array
     */
    public function getPieceManquant(Dossier $dossier,Banque $banque = null, BanqueCompte $bc = null, $exercice = 2018, $type = 2,Releve $releve = null, $intervals = [90,500000],$dateType = 0,Client $client = null,Utilisateur $user, $dossierArray = [], $isTotal = false)
    {
        /**
         * 0: releve banques manquants,
         * 1: operation banques manquantes
         * 2: factures fournisseurs manquants
         * 3: factures clients manquants
         * 4: cheques inconnus
         * 8: tous releve banques manquants
         */
        $results = [];
        $count = 0;
        if ($type == 9)
        {
            $datas = $this->getAutresPmNotif($dossierArray, $exercice, $user, $dateType, $intervals);
            return $datas;
        }
        else if ($type == 8)
        {
            $datas = $this->getDetailsTachesBanque($dossierArray, $exercice);
            return $datas;
        }
        else if ($type == 0)
        {
            $clotureMois = $this->getEntityManager()->getRepository('AppBundle:TbimagePeriode')
                ->getAnneeMoisExercices($dossier,$exercice);
            $banqueComptes = ($bc) ? [$bc] :
                $this->getEntityManager()->getRepository('AppBundle:BanqueCompte')->getBanquesComptes($dossier,$banque);
            $datas = [];
            $exercices = [];
            for ($i = -2; $i < 3; $i++) $exercices[] = $exercice + $i;

            $datas = [];
            foreach ($banqueComptes as $banqueCompte)
            {
                /** @var ReleveManquant[] $releveManquantsTemps */
                $releveManquantsTemps = $this->getEntityManager()->getRepository('AppBundle:ReleveManquant')
                    ->createQueryBuilder('rm')
                    ->where('rm.banqueCompte = :banqueCompte')
                    ->andWhere('rm.exercice IN(:exercices)')
                    ->setParameters([
                        'banqueCompte' => $banqueCompte,
                        'exercices' => $exercices
                    ])
                    ->getQuery()
                    ->getResult();
                $rMs = [];
                foreach ($releveManquantsTemps as $releveManquantsTemp)
                {
                    $rMs = array_merge($rMs, $releveManquantsTemp->getMois());
                }
                $rMs = array_map('trim',$rMs);
                $releveManquants = array_intersect($clotureMois->ms, $rMs);

                $compte = $banqueCompte->getNumcompte();
                if (strlen($compte) >= 11)
                    $compte = substr($compte,-11);

                if ($banqueCompte->getSourceImage())
                {
                    if ($banqueCompte->getSourceImage()->getId() == 3) $compte = 'SB-'.$compte;
                    elseif ($banqueCompte->getSourceImage()->getId() == 11) $compte = 'BU-'.$compte;
                }

                $data = [];
                $data['id'] = '0-' . Boost::boost($banqueCompte->getId());
                $data['b'] = $banqueCompte->getBanque()->getNom();
                $data['bc'] = $compte;
                $data['sc'] = 'Relevé Bancaire';

                foreach ($clotureMois->ms as $key => $m)
                {
                    $s = (in_array($m,$releveManquants)) ? 0 : 1;
                    $relevePiece = ($s === 1) ? null :
                        $this->getEntityManager()->getRepository('AppBundle:RelevePiece')
                            ->getByMonth($banqueCompte,$exercice,$m);
                    $imageTemp = null;
                    if ($relevePiece)
                    {
                        $imageTemp = (object)
                        [
                            'id' => Boost::boost($relevePiece->getImage()->getId()),
                            'n' => $relevePiece->getImage()->getNom()
                        ];
                    }

                    $dateNow = new \DateTime();
                    if ($m > $dateNow->format('Y-m')) $s = -1;

                    $status = (object)
                    [
                        's' => $s,
                        'm' => $m,
                        'it' => $imageTemp
                    ];
                    $data['m_'.$key] = $status;
                }
                $datas[] = (object) $data;
            }

            $idSousCategorieDailys = $this->getEntityManager()->getRepository('AppBundle:Souscategorie')
                ->getObsDaily(false);

            foreach ($banqueComptes as $key => $bc) {
                $banqueObManquantes = $this->getEntityManager()->getRepository('AppBundle:BanqueObManquante')
                    ->getForDossier($dossier,$bc,$exercice);

                foreach ($banqueObManquantes as $banqueObManquante)
                {
                    if (in_array($banqueObManquante->getSouscategorie()->getId(),$idSousCategorieDailys)) continue;

                    $data = [];
                    $data['id'] = '1-' . Boost::boost($banqueObManquante->getId());

                    $compte = $banqueObManquante->getBanqueCompte()->getNumcompte();
                    if (strlen($compte) >= 11)
                        $compte = substr($compte,-11);
                    if ($bc->getSourceImage())
                    {
                        if ($bc->getSourceImage()->getId() == 3) $compte = 'SB-'.$compte;
                        elseif ($bc->getSourceImage()->getId() == 11) $compte = 'BU-'.$compte;
                    }
                    $data['b'] = $banqueObManquante->getBanqueCompte()->getBanque()->getNom();
                    $data['bc'] = $compte;
                    $data['sc'] = $banqueObManquante->getSouscategorie()->getLibelleNew();

                    $releveManquants = $banqueObManquante->getMois();
                    foreach ($clotureMois->ms as $keyM => $m)
                    {
                        $imageTemp = null;

                        $s = (in_array($m,$releveManquants)) ? 0 : 1;
                        $relevePiece = ($s === 1) ? null :
                            $this->getEntityManager()->getRepository('AppBundle:BanqueAutrePiece')
                                ->getByMonth(
                                    $banqueObManquante->getBanqueCompte(),
                                    $banqueObManquante->getSouscategorie(),
                                    $banqueObManquante->getExercice(),
                                    $m);
                        if ($relevePiece)
                        {
                            $imageTemp = (object)
                            [
                                'id' => Boost::boost($relevePiece->getImage()->getId()),
                                'n' => $relevePiece->getImage()->getNom()
                            ];
                        }


                        $dateNow = new \DateTime();
                        if ($m > $dateNow->format('Y-m')) $s = -1;
                        if ($m == $dateNow->format('Y-m')) $s = 0;

                        $status = (object)
                        [
                            's' => $s,
                            'm' => $m,
                            'it' => $imageTemp
                        ];
                        $data['m_'.$keyM] = $status;
                    }

                    $datas[] = (object) $data;
                }


                //---------DEBUT
                /** @var Souscategorie[] $sousCategoriesDailys */
                $sousCategoriesDailys = $this->getEntityManager()->getRepository('AppBundle:Souscategorie')
                    ->getObsDaily();

                $compte = $bc->getNumcompte();
                foreach ($sousCategoriesDailys as $sousCategoriesDaily)
                {
                    $sousCategoriePasSasir = $this->getEntityManager()->getRepository('AppBundle:SouscategoriePasSaisir')
                        ->aSaisir($dossier, $sousCategoriesDaily);

                    //if ($sousCategoriePasSasir) continue;

                    $data = [];
                    $data['id'] = '1-' . Boost::boost($bc->getId());
                    if (strlen($compte) >= 11)
                        $compte = substr($compte,-11);
                    if ($bc->getSourceImage())
                    {
                        if ($bc->getSourceImage()->getId() == 3) $compte = 'SB-'.$compte;
                        elseif ($bc->getSourceImage()->getId() == 11) $compte = 'BU-'.$compte;
                    }
                    $data['b'] = $bc->getBanque()->getNom();
                    $data['bc'] = $compte;
                    $data['sc'] = $sousCategoriesDaily->getLibelleNew();
                    $data['sci'] = Boost::boost($sousCategoriesDaily->getId());

                    $index = 0;
                    $hasPm = false;
                    foreach ($clotureMois->ms as $keyM => $m)
                    {
                        $depasser = 0;
                        if ($index == 0) $depasser = -1;
                        elseif ($index == count($clotureMois->ms) - 1) $depasser = 1;

                        $index++;

                        $imageTemp = null;
                        $releveDailyManquants = $this->getPmInSousCategorie($bc,$sousCategoriesDaily,$m,true,$depasser);

                        if (count($releveDailyManquants) > 0)
                        {
                            $s = 0;
                            $hasPm = true;
                        }
                        else
                        {
                            $relevePiece =
                                $this->getEntityManager()->getRepository('AppBundle:BanqueAutrePiece')
                                    ->getByMonth(
                                        $bc,
                                        $sousCategoriesDaily,
                                        $exercice,
                                        $m);
                            $s = 1;
                            if ($relevePiece)
                            {
                                $imageTemp = (object)
                                [
                                    'id' => Boost::boost($relevePiece->getImage()->getId()),
                                    'n' => $relevePiece->getImage()->getNom()
                                ];

                                $s = -2;
                            }
                        }

                        $dateNow = new \DateTime();
                        if ($m > $dateNow->format('Y-m')) $s = -1;
                        if ($m == $dateNow->format('Y-m')) $s = 0;

                        $status = (object)
                        [
                            's' => $s,
                            'm' => $m,
                            'it' => $imageTemp,
                            'd' => 1
                        ];

                        $data['m_'.$keyM] = $status;
                    }

                    if ($hasPm) $datas[] = (object) $data;
                }
                //---------FIN
            }

            $mois = [];
            foreach ($clotureMois->ms as $m)
            {
                $spliters = explode('-',$m);
                $mois[] = $spliters[1].'/'.substr($spliters[0],2,2);
            }

            return
            [
                'datas' => $datas,
                'm' => $mois
            ];
        }
        elseif ($type == 1)
        {

        }
        elseif (in_array($type,[5,6]))
        {
            $tvaImputationControles = [];

            $entetes = [];
            for ($i = 0; $i < count($intervals) - 1; $i++)
            {
                $tvaImputationControles = array_merge($tvaImputationControles,
                    $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                        ->getNonLettre($dossier,$exercice,$type,[$intervals[$i],$intervals[$i+1]],$i,$dateType)
                    );

                $entete = '> '. $intervals[$i];
                if ($i != count($intervals) - 2)
                    $entete .= ' et <= ' . $intervals[$i + 1];

                $entete .= ' Jours';
                $entetes[] = $entete;
            }

            $count = count($tvaImputationControles);
            if(!$isTotal){
                $results = [
                    'datas' => $tvaImputationControles,
                    'm' => $entetes
                ];
            }
        }
        elseif (in_array($type,[10,11]))
        {
            $ecritures = [];

            $entetes = [];
            for ($i = 0; $i < count($intervals) - 1; $i++)
            {
                $type = ($type == 10) ? 0 : 1;
                $ecritures = array_merge($ecritures,
                    $this->getEntityManager()->getRepository('AppBundle:Ecriture')
                         ->getFournisseurClientNonPayees($dossier,$exercice,$type,[$intervals[$i],$intervals[$i+1]],$i,$dateType)
                    );

                $entete = '> '. $intervals[$i];
                if ($i != count($intervals) - 2)
                    $entete .= ' et <= ' . $intervals[$i + 1];

                $entete .= ' Jours';
                $entetes[] = $entete;
            }

            $count = count($ecritures);
            if(!$isTotal){
                $results = [
                    'datas' => $ecritures,
                    'm' => $entetes
                ];
            }
        }
        else
        {
            $releves = $this->createQueryBuilder('r')
                ->innerJoin('AppBundle:Separation', 'sep', 'WITH', 'sep.image = r.image')
                ->leftJoin('r.banqueCompte','bc')
                ->leftJoin('r.image','i')
                ->where('bc.dossier = :dossier')
                ->setParameter('dossier',$dossier)
                ->andWhere('i.exercice = :exercice')
                ->setParameter('exercice',$exercice)
                ->andWhere('r.imageFlague IS NULL')
                ->andWhere('sep.souscategorie = 10')
                ->andWhere('i.supprimer = 0')
                ->andWhere('r.operateur IS NULL');

            if ($releve)
                $releves = $releves
                    ->andWhere('r = :releve')
                    ->setParameter('releve',$releve);

            if ($bc)
                $releves = $releves
                    ->andWhere('r.banqueCompte = :banqueCompte')
                    ->setParameter('banqueCompte',$bc);

            if ($banque)
                $releves = $releves
                    ->andWhere('bc.banque = :banque')
                    ->setParameter('banque',$banque);

            if ($type == 2 || $type == 3)
            {
                if ($type == 2)
                    $releves = $releves
                        ->andWhere('ROUND(r.credit - r.debit,2) < 0')
                        ->andWhere('r.libelle NOT LIKE :chq_1')
                        ->andWhere('r.libelle NOT LIKE :chq_2');
                else
                    $releves = $releves
                        ->andWhere('ROUND(r.credit - r.debit,2) > 0');
            }
            elseif ($type === 4)
            {
                $releves = $releves
                    ->andWhere('(r.libelle LIKE :chq_1 OR r.libelle LIKE :chq_2)')
                    ->andWhere('r.imageFlague IS NULL')
                    ->andWhere('ROUND(r.credit - r.debit,2) < 0');
            }

            if ($type !== 3)
                $releves = $releves
                    ->setParameter('chq_1','%CHQ%')
                    ->setParameter('chq_2','%CHEQUE%');

            /** @var Releve[] $releves */
            $releves = $releves
                ->getQuery()
                ->getResult();

            foreach ($releves as $rel)
            {
                if ($rel->getCleDossier())
                {
                    if ($rel->getCleDossier()->getPasPiece()) continue;
                    else
                    {

                        if ($this->getEntityManager()->getRepository('AppBundle:ReleveExt')
                            ->isFlaguer($rel)) continue;

                        $releveIsDebit = ($rel->getCredit() - $rel->getDebit() > 0);
                        $cleExceptionPm = $this->getEntityManager()->getRepository('AppBundle:CleExceptionPm')
                            ->cleExceptionForCleDossier($rel->getCleDossier());
                        $continue = false;

                        if ($cleExceptionPm)
                        {
                            $formule = trim($cleExceptionPm->getFormule());
                            $sens = intval($cleExceptionPm->getSens());
                            $formule2 = trim($cleExceptionPm->getFormule2());
                            $sens2 = intval($cleExceptionPm->getSens2());
                            $listVal['x'] = abs($rel->getCredit() - $rel->getDebit());
                            $langage = new ExpressionLanguage();

                            //Formule
                            if ($formule != '' && ($sens == 0 || $sens == 1) && $releveIsDebit)
                            {
                                $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule))),$listVal);
                            }
                            if ($formule != '' && ($sens == 0 || $sens == 2) && !$releveIsDebit && !$continue)
                            {
                                $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule))),$listVal);
                            }
                            //Formule2
                            if ($formule2 != '' && ($sens2 == 0 || $sens2 == 1) && $releveIsDebit && !$continue)
                            {
                                $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule2))),$listVal);
                            }
                            if ($formule2 != '' && ($sens2 == 0 || $sens2 == 2) && !$releveIsDebit && !$continue)
                            {
                                $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule2))),$listVal);
                            }
                        }

                        if ($continue) continue;
                    }
                }
                if (intval($rel->getEcritureChange()) == 1 && intval($rel->getMaj()) == 3) continue;

                $count++;
                if(!$isTotal) $results[] = $this->releveObject($rel);
            }
        }

        if($isTotal) return $count;

        return $results;
    }

    private function releveObject(Releve $releve)
    {
        $banqueCompte = $releve->getBanqueCompte();
        $compte = $banqueCompte->getNumcompte();

        if (strlen($compte) >= 11)
            $compte = substr($compte,-11);

        if ($banqueCompte->getSourceImage())
        {
            if ($banqueCompte->getSourceImage()->getId() == 3) $compte = 'SB-'.$compte;
            elseif ($banqueCompte->getSourceImage()->getId() == 11) $compte = 'BU-'.$compte;
        }

        $bilan = null;
        $charge = null;
        $tva = null;
        /** @var CleDossier $cleDossier */
        $cleDossier = null;

        if ($releve->getEcritureChange() == 1)
        {
            $releveImputations = $this->getEntityManager()->getRepository('AppBundle:ReleveImputation')
                ->getReleveImputation($releve);

            foreach ($releveImputations as $releveImputation)
            {
                if ($releveImputation->getTiers())
                    $bilan = (object)
                    [
                        'id' => Boost::boost($releveImputation->getTiers()->getId()),
                        'l' => $releveImputation->getTiers()->getCompteStr(),
                        't' => 1
                    ];
                elseif ($releveImputation->getPcc())
                {
                    //0: bilan pcc, 1: tiers, 2: resultat, 3: tva
                    if ($releveImputation->getType() == 0)
                        $bilan = (object)
                        [
                            'id' => Boost::boost($releveImputation->getPcc()->getId()),
                            'l' => $releveImputation->getPcc()->getCompte(),
                            't' => 0
                        ];
                    elseif ($releveImputation->getType() == 2)
                        $charge = (object)
                        [
                            'id' => Boost::boost($releveImputation->getPcc()->getId()),
                            'l' => $releveImputation->getPcc()->getCompte(),
                            't' => 0
                        ];
                    elseif ($releveImputation->getType() == 3)
                        $tva = (object)
                        [
                            'id' => Boost::boost($releveImputation->getPcc()->getId()),
                            'l' => $releveImputation->getPcc()->getCompte(),
                            't' => 0
                        ];
                }
            }
        }
        elseif ($releve->getCleDossier())
        {
            $cleDossier = $releve->getCleDossier();

            $cleDossierExts = $this->getEntityManager()->getRepository('AppBundle:CleDossierExt')
                ->getForCleDossier($cleDossier);

            if (count($cleDossierExts) > 0)
            {
                foreach ($cleDossierExts as $cleDossierExt)
                {
                    //2: Bilan; 1:TVA; 0: Resultat
                    if ($cleDossierExt->getTypeCompte() == 2 && !$bilan)
                    {
                        if ($cleDossierExt->getTiers())
                            $bilan = (object)
                            [
                                'id' => Boost::boost($cleDossierExt->getTiers()->getId()),
                                'l' => $cleDossierExt->getTiers()->getCompteStr(),
                                't' => 1
                            ];
                        elseif ($cleDossierExt->getPcc())
                            $bilan = (object)
                            [
                                'id' => Boost::boost($cleDossierExt->getPcc()->getId()),
                                'l' => $cleDossierExt->getPcc()->getCompte(),
                                't' => 0
                            ];
                    }
                    elseif ($cleDossierExt->getTypeCompte() == 1 && $cleDossierExt->getPcc() && !$tva)
                        $tva = (object)
                        [
                            'id' => Boost::boost($cleDossierExt->getPcc()->getId()),
                            'l' => $cleDossierExt->getPcc()->getCompte(),
                            't' => 0
                        ];
                    elseif ($cleDossierExt->getTypeCompte() == 0 && $cleDossierExt->getPcc() && $charge)
                        $charge = (object)
                        [
                            'id' => Boost::boost($cleDossierExt->getPcc()->getId()),
                            'l' => $cleDossierExt->getPcc()->getCompte(),
                            't' => 0
                        ];
                }
            }
            else
            {
                if ($releve->getCleDossier()->getBilanTiers())
                    $bilan = (object)
                    [
                        'id' => Boost::boost($releve->getCleDossier()->getBilanTiers()->getId()),
                        'l' => $releve->getCleDossier()->getBilanTiers()->getCompteStr(),
                        't' => 1
                    ];
                elseif ($releve->getCleDossier()->getBilanPcc())
                    $bilan = (object)
                    [
                        'id' => Boost::boost($releve->getCleDossier()->getBilanPcc()->getId()),
                        'l' => $releve->getCleDossier()->getBilanPcc()->getCompte(),
                        't' => 0
                    ];

                if ($releve->getCleDossier()->getResultat())
                    $charge = (object)
                    [
                        'id' => Boost::boost($releve->getCleDossier()->getResultat()->getId()),
                        'l' => $releve->getCleDossier()->getResultat()->getCompte(),
                        't' => 0
                    ];
                if ($releve->getCleDossier()->getTva())
                    $tva = (object)
                    [
                        'id' => Boost::boost($releve->getCleDossier()->getTva()->getId()),
                        'l' => $releve->getCleDossier()->getTva()->getCompte(),
                        't' => 0
                    ];
            }
        }

        if (!$bilan && !$charge && !$tva)
        {
            $attenteDebit = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                ->getPccAttenteBanque($releve->getBanqueCompte()->getDossier(), true);
            $attenteCredit = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                ->getPccAttenteBanque($releve->getBanqueCompte()->getDossier(),false);

            /** @var Pcc $pccAttente */
            $pccAttente = (floatval($releve->getDebit()) != 0) ? $attenteDebit : $attenteCredit;
            $bilan = (object)
            [
                'id' => Boost::boost($pccAttente->getId()),
                'l' => $pccAttente->getCompte(),
                'i' => $pccAttente->getIntitule(),
                't' => 0
            ];
        }

        $releveInstruction = $this->getEntityManager()->getRepository('AppBundle:ReleveInstruction')
            ->getByReleve($releve);

        $observation = $releveInstruction ? $releveInstruction->getObservation() : '';
        $decision = 0;
        /*if ($bilan || $charge || $tva)
        {
            if ($tva) $decision = 2;
            else $decision = 3;
        }
        else*/if ($releveInstruction) $decision = $releveInstruction->getInstruction();

        if ($releve->getImageTemp())
        {
            $imageTemp = $releve->getImageTemp();
            if ($imageTemp->getSupprimer() != 0) $releve->setImageTemp(null);
            else
            {
                /** @var Separation $separation */
                $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                    ->findOneBy([
                        'image' => $imageTemp
                    ]);

                if ($separation)
                {
                    $sousCategorie = $separation->getSouscategorie();
                    $sousSousCategorie = $separation->getSoussouscategorie();

                    if (($sousCategorie && strtoupper($sousCategorie->getLibelleNew()) == 'DOUBLON') ||
                        ($sousSousCategorie && strtoupper($sousSousCategorie->getLibelleNew()) == 'DOUBLON'))
                        $releve->setImageTemp(null);
                }

                if ($releve->getImageTemp())
                {
                    $nonFlaguers = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                        ->getNotFlague($releve->getImageTemp());

                    if (count($nonFlaguers) > 0)
                        $releve->setImageTemp(null);
                }
            }
        }

        $this->getEntityManager()->flush();

        return (object)
        [
            'id' => Boost::boost($releve->getId()),
            'd' => $releve->getDateReleve()->format('Y-m-d'),
            'l' => $releve->getLibelle(),
            'dp' => $releve->getDebit(),
            'rc' => $releve->getCredit(),
            'c' => $compte,
            'dec' => $decision,
            'gl' => $bilan,
            'cl' => $cleDossier ? $cleDossier->getCle()->getCle() : '',
            'obs' => $observation,
            'i' => (object)
            [
                'id' => Boost::boost($releve->getImage()->getId()),
                'n' => $releve->getImage()->getNom()
            ],
            'ch' => $charge,
            'tva' => $tva,
            'imt' => ($releve->getImageTemp()) ?
                (object)
                [
                    'id' => Boost::boost($releve->getImageTemp()->getId()),
                    'n' => $releve->getImageTemp()->getNom()
                ] : null,
            't' => $releve->getCompteTiersTemp() ? '1-'.$releve->getCompteTiersTemp()->getId() : '0-0',
            'n' => 0,
        ];
    }

    /**
     * @param ImageFlague $imageFlague
     * @param Releve|null $releve
     * @return Releve[]
     */
    public function getChildImageFlagues(ImageFlague $imageFlague, Releve $releve = null)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.image','i')
            ->where('r.imageFlague = :imageFlague')
            ->andWhere('r.id <> :id')
            ->andWhere('r.operateur IS NULL')
            ->andWhere('i.supprimer = 0')
            ->setParameters([
                'imageFlague' => $imageFlague,
                'id' => ($releve) ? $releve->getId() : -1
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param BanqueCompte $banqueCompte
     * @param $exercice
     * @param $interval
     * @param bool $centraliser
     * @param JournalDossier $journalDossier
     * @param int $filtreType
     * @param \DateTime $filtreStart
     * @param \DateTime $filtreEnd
     * @param bool $obsDetailler
     * @param int $sensResult
     * @return object
     */
    public function getJournal(BanqueCompte $banqueCompte,$exercice,$interval,$centraliser = false, JournalDossier $journalDossier = null, $filtreType = 2, \DateTime $filtreStart = null, \DateTime $filtreEnd = null, $obsDetailler = true, $sensResult = 0)
    {
        $this->journalCentraliser = $centraliser;
        $this->journalDossier = $journalDossier;

        $this->methoCompta = $this->getEntityManager()->getRepository('AppBundle:MethodeComptable')->getMethodeDossier($banqueCompte->getDossier());

        /** @var Releve[] $releves */
        $releves = $this->createQueryBuilder('r')
            ->innerJoin('AppBundle:Separation', 'sep', 'WITH', 'sep.image = r.image')
            //->leftJoin('sep.souscategorie', 'ssc')
            ->innerJoin('r.image','i')
            ->where('r.banqueCompte = :banqueCompte')
            ->setParameter('banqueCompte',$banqueCompte)
            ->andWhere('i.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->andWhere('sep.souscategorie IS NOT NULL')
            ->andWhere('sep.souscategorie = 10')
            ->andWhere('r.operateur IS NULL')
            /*->andWhere('r.libelle like :libelleLike')
            ->setParameter('libelleLike','%stripe%')*/
            /*->andWhere('r.dateReleve = :dateR')
            ->setParameter('dateR', '2018-11-09')*/
            ->andWhere('i.supprimer = 0');
            //->setMaxResults(25);

        if ($sensResult > 0)
            $releves = $releves->andWhere('r.credit > 0');
        elseif ($sensResult < 0)
            $releves = $releves->andWhere('r.debit > 0');

        $this->cfonbCodeActives = [];

        if (count($releves) > 0)
            $this->cfonbCodeActives = $this->getEntityManager()->getRepository('AppBundle:CfonbBanque')
                ->cfonbActiveInBanque($banqueCompte->getBanque());

        if ($filtreType === 0)
        {
            if ($filtreStart)
                $releves = $releves
                    ->andWhere('r.dateReleve >= :start')
                    ->setParameter('start',$filtreStart);
            if ($filtreEnd)
                $releves = $releves
                    ->andWhere('r.dateReleve <= :end')
                    ->setParameter('end',$filtreEnd);
        }
        elseif ($filtreType === 1 && ($filtreStart || $filtreEnd))
        {
            $releves = $releves
                ->leftJoin('i.lot','l');

            if ($filtreStart)
                $releves = $releves
                    ->andWhere('l.dateScan >= :start')
                    ->setParameter('start',$filtreStart->format('Y-m-d'));
            if ($filtreEnd)
                $releves = $releves
                    ->andWhere('l.dateScan <= :end')
                    ->setParameter('end',$filtreEnd->format('Y-m-d'));
        }

        if (count($interval) != 12)
        {
            $conditions = '';
            $iteration = 0;
            foreach ($interval as $i)
            {
                $conditions .= 'r.dateReleve >= :min_'.$iteration.' AND r.dateReleve < :max_'.$iteration.' ';
                if ($iteration != count($interval) - 1) $conditions .= ' OR ';

                /** @var \DateTime $min */
                $min = $i->min;
                $min->setTime(0,0,0);
                /** @var \DateTime $max */
                $max = $i->max;
                $max->setTime(23,59,59);
                $releves = $releves
                    ->setParameter('min_'.$iteration,$min)
                    ->setParameter('max_'.$iteration,$max);

                $iteration++;
            }

            $releves = $releves
                ->andWhere('('.$conditions.')');
        }

        $pccBanque = $banqueCompte->getPcc();

        /** @var Releve[] $releves */
        $releves = $releves->orderBy('r.dateReleve')->addOrderBy('i.nom')->getQuery()->getResult();

        $journaux = [];
        $total = 0;
        $keyMois = [];
        $d = \DateTime::createFromFormat('Y-m-d','1910-01-01');
        $dateLast = $d->format('Ymd');

        $relevesPasses = [];
        $keyMoisDC = [];
        foreach ($releves as $key => $releve)
        {
            //$isDebit = $releve->getCredit() - $releve->getDebit() > 0;
            if (in_array($releve->getId(),$relevesPasses)) continue;
            $relevesPasses[] = $releve->getId();

            if ($this->journalCentraliser)
            {
                $solde = floatval($releve->getCredit()) - floatval($releve->getDebit());
                $keyMoi = $releve->getDateReleve()->format('Ym') . (($solde > 0) ? '_c' : '_d');
                if (array_key_exists($keyMoi,$journaux))
                {
                    if ($solde < 0)
                        $journaux[$keyMoi]->cr =
                            floatval($journaux[$keyMoi]->cr) + abs($solde);
                    else
                        $journaux[$keyMoi]->db =
                            floatval($journaux[$keyMoi]->db) + abs($solde);
                }
                else
                {
                    $journaux[$keyMoi] = $this->getJournalObject(
                        $releve,
                        null,
                        $pccBanque,
                        null,
                        $releve->getCredit(),
                        $releve->getDebit(),
                        $releve->getId(),
                        null,
                        null,
                        1
                    );
                    $keyMois[$keyMoi] = $this->id - 1;
                }
            }
            else
            {
                $journaux[] = $this->getJournalObject(
                    $releve,
                    null,
                    $pccBanque,
                    null,
                    $releve->getCredit(),
                    $releve->getDebit(),
                    $releve->getId(),
                    $releve->getCleDossier(),
                    null,
                    1
                );
            }

            //continue;

            $credit = ($releve->getCredit() - $releve->getDebit() > 0);
            $montantReleve = abs($releve->getCredit() - $releve->getDebit());

            $typeCompta = intval($releve->getEngagementTresorerie());

            $total += $montantReleve;

            $ecritureChange = false;
            if (intval($releve->getEcritureChange()) == 1)
            {
                //0: bilan pcc, 1: tiers,  2: resultat, 3: tva
                $releveImputations = $this->getEntityManager()->getRepository('AppBundle:ReleveImputation')
                    ->getImputation($releve);

                if (count($releveImputations) > 0)
                {
                    $ecritureChange = true;

                    foreach ($releveImputations as $releveImputation)
                    {
                        $m = abs($releveImputation->getCredit() - $releveImputation->getDebit());
                        $journaux[] = $this->getJournalObject(
                            $releve,
                            null,
                            $releveImputation->getPcc(),
                            $releveImputation->getTiers(),
                            $credit ? 0 : $m,
                            $credit ? $m : 0,
                            $releve->getId()
                        );
                    }
                }
                else
                {
                    $releve->setEcritureChange(0);
                    $this->getEntityManager()->flush();
                }
            }

            if (!$ecritureChange)
            {
                if ($releve->getImageFlague())
                {
                    $soeurs = $this->getEntityManager()->getRepository('AppBundle:ImageFlague')
                        ->getSoeurs($releve->getImageFlague(),$releve);

                    $tvaImputationsControlesImages = $soeurs->tic;
                    
                    $allAvoir = true;
                    foreach ($tvaImputationsControlesImages as $ki => $item)
                    {
                        $img = $this->getEntityManager()->getRepository('AppBundle:Image')
                            ->find($ki);

                        $ic = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                            ->getByImage($img);

                        if ($ic && $ic->getTypePiece() && $ic->getTypePiece()->getId() > 1)
                        {
                            $allAvoir = false;
                            break;
                        }
                    }

                    foreach ($tvaImputationsControlesImages as $tvaImputationsControlesImage)
                    {
                        $sensOppose = false;
                        $inverse = false;

                        $signe = ($tvaImputationsControlesImage[0]->getMontantTtc() < 0);
                        if (count($tvaImputationsControlesImage) > 1)
                        {
                            foreach ($tvaImputationsControlesImage as $item)
                            {
                                /** @var TvaImputationControle $tvaImputationControle */
                                $tvaImputationControle = $item;
                                $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                                if ($signe != $signeItem)
                                {
                                    $sensOppose = true;
                                    break;
                                }
                            }
                        }

                        foreach ($tvaImputationsControlesImage as $item)
                        {
                            /** @var TvaImputationControle $tvaImputationControle */
                            $tvaImputationControle = $item;
                            $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                            $montant = abs($tvaImputationControle->getMontantTtc());
                            /** @var Separation $separation */
                            $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                                ->createQueryBuilder('s')
                                ->where('s.image = :image')
                                ->setParameter('image',$tvaImputationControle->getImage())
                                ->setMaxResults(1)
                                ->getQuery()
                                ->getOneOrNullResult();

                            $cr = $credit;
                            if ($separation)
                            {
                                if (in_array($separation->getCategorie()->getId(),[10,12,9,13]))
                                {
                                    $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                        ->getImputationControle($tvaImputationControle);

                                    if (!$allAvoir && $imputationControle && $imputationControle->getTypePiece() && intval($imputationControle->getTypePiece()->getId()) == 1)
                                        $cr = !$cr;
                                }
                            }

                            $montant = abs($montant);
                            if ($sensOppose && $signe != $signeItem)
                            {
                                $cr = !$cr;
                            }

                            if ($typeCompta == 0)
                            {
                                if ($tvaImputationControle->getPccBilan() || $tvaImputationControle->getTiers())
                                {
                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                        ($tvaImputationControle->getPccBilan() ?
                                            '0-'. $tvaImputationControle->getPccBilan()->getId() :
                                             '1-' . $tvaImputationControle->getTiers()->getId()
                                        );

                                    if (array_key_exists($key,$journaux))
                                    {
                                        $solde = $journaux[$key]->cr - $journaux[$key]->db  + ($cr ? 1 : -1) * ($montant);
                                        $journaux[$key]->db = ($solde < 0 ? abs($solde) : 0);
                                        $journaux[$key]->cr = ($solde < 0 ? 0 : abs($solde));
                                    }
                                    else
                                    {
                                        $journaux[$key] = $this->getJournalObject(
                                            $releve,
                                            $tvaImputationControle,
                                            $tvaImputationControle->getPccBilan(),
                                            $tvaImputationControle->getTiers(),
                                            $cr ? 0 : abs($montant),
                                            $cr ? abs($montant) : 0,
                                            $releve->getId(),
                                            null,
                                            null,
                                            0,
                                            $tvaImputationControle->getImage()
                                        );
                                    }
                                }
                            }
                            else
                            {
                                $montantTva = 0;
                                if ($tvaImputationControle->getPccTva() && $tvaImputationControle->getTvaTaux())
                                {
                                    $coeffTva = 1 + floatval($tvaImputationControle->getTvaTaux()->getTaux() / 100);
                                    $mHT = $montant / $coeffTva;
                                    $montantTva = $montant - $mHT;
                                }

                                if ($tvaImputationControle->getPcc())
                                {
                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                        '0-'. $tvaImputationControle->getPcc()->getId();

                                    if (array_key_exists($key,$journaux))
                                    {
                                        $solde = $journaux[$key]->cr - $journaux[$key]->db  + ($cr ? 1 : -1) * ($montant - $montantTva);
                                        $journaux[$key]->db = ($solde < 0 ? abs($solde) : 0);
                                        $journaux[$key]->cr = ($solde < 0 ? 0 : abs($solde));
                                    }
                                    else
                                    {
                                        $journaux[$key] = $this->getJournalObject(
                                            $releve,
                                            $tvaImputationControle,
                                            $tvaImputationControle->getPcc(),
                                            null,
                                            $cr ? 0 : abs($montant - $montantTva),
                                            $cr ? abs($montant - $montantTva) : 0,
                                            $releve->getId(),
                                            null,
                                            null,
                                            0,
                                            $tvaImputationControle->getImage()
                                        );
                                    }
                                }

                                if ($montantTva != 0)
                                {
                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                        '0-'. $tvaImputationControle->getPccTva()->getId();

                                    if (array_key_exists($key,$journaux))
                                    {
                                        $solde = $journaux[$key]->cr - $journaux[$key]->db  + ($cr ? 1 : -1) * ($montantTva);
                                        $journaux[$key]->db = ($solde < 0 ? abs($solde) : 0);
                                        $journaux[$key]->cr = ($solde < 0 ? 0 : abs($solde));
                                    }
                                    else
                                    {
                                        $journaux[$key] = $this->getJournalObject(
                                            $releve,
                                            $tvaImputationControle,
                                            $tvaImputationControle->getPccTva(),
                                            null,
                                            $cr ? 0 : abs($montantTva),
                                            $cr ? abs($montantTva) : 0,
                                            $releve->getId(),
                                            null,
                                            null,
                                            0,
                                            $tvaImputationControle->getImage()
                                        );
                                    }
                                }
                            }
                        }
                    }

                    $releveSoeursImages = $soeurs->rel;
                    foreach ($releveSoeursImages as $releveSoeursImage)
                    {
                        foreach ($releveSoeursImage as $item)
                        {
                            /** @var Releve $rel */
                            $rel = $item;
                            if (in_array($rel->getId(),$relevesPasses)) continue;
                                $journaux[] = $this->getJournalObject(
                                    $rel,
                                    null,
                                    $pccBanque,
                                    null,
                                    $rel->getCredit(),
                                    $rel->getDebit(),
                                    $releve->getId(),
                                    $rel->getCleDossier(),
                                    null,
                                    1
                                );
                            $relevesPasses[] = $rel->getId();
                        }
                    }

                    $releveSoeursBanquesAutres = $soeurs->bsca;
                    if (count($releveSoeursBanquesAutres) > 0)
                    {
                        $pccAttente = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                            ->getPccAttenteBanque($banqueCompte->getDossier(),!$credit);

                        foreach ($releveSoeursBanquesAutres as $releveSoeursBanquesAutre)
                        {
                            foreach ($releveSoeursBanquesAutre as $item)
                            {
                                /** @var BanqueSousCategorieAutre $banqueSousCateogrieAutre */
                                $banqueSousCateogrieAutre = $item;
                                $isEngagement = intval($banqueSousCateogrieAutre->getEngagementTresorerie()) == 0;

                                $tvaImputationsControlesImages = [];
                                /** @var BanqueSousCategorieAutre[] $banqueSousCategoriesSoeurs */
                                $banqueSousCategoriesSoeurs = [];

                                $countSoeur = 0;
                                if ($banqueSousCateogrieAutre->getImageFlague2())
                                {
                                    $tvaImputationsControlesImages = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                                        ->getChildImageFlagues($banqueSousCateogrieAutre->getImageFlague2(),null,true);

                                    $banqueSousCategoriesSoeurs = $this->getEntityManager()->getRepository('AppBundle:BanqueSousCategorieAutre')
                                        ->getChildImageFlagues($banqueSousCateogrieAutre->getImageFlague2(),$banqueSousCateogrieAutre,true);

                                    $countSoeur = count($tvaImputationsControlesImages) + count($banqueSousCategoriesSoeurs);

                                    if ($countSoeur == 0)
                                    {
                                        /*$this->getEntityManager()->remove($banqueSousCateogrieAutre->getImageFlague2());
                                        $this->getEntityManager()->flush();*/
                                    }
                                }

                                if ($countSoeur > 0)
                                {
                                    $isCr = $banqueSousCateogrieAutre->getMontant() < 0;
                                    $allAvoir = true;
                                    foreach ($tvaImputationsControlesImages as $ki => $it)
                                    {
                                        $img = $this->getEntityManager()->getRepository('AppBundle:Image')
                                            ->find($ki);

                                        $ic = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                            ->getByImage($img);

                                        if ($ic && $ic->getTypePiece() && $ic->getTypePiece()->getId() > 1)
                                        {
                                            $allAvoir = false;
                                            break;
                                        }
                                    }

                                    foreach ($tvaImputationsControlesImages as $tvaImputationsControlesImage)
                                    {
                                        $sensOppose = false;
                                        $inverse = false;

                                        $signe = ($tvaImputationsControlesImage[0]->getMontantTtc() < 0);
                                        if (count($tvaImputationsControlesImage) > 1)
                                        {
                                            foreach ($tvaImputationsControlesImage as $item)
                                            {
                                                /** @var TvaImputationControle $tvaImputationControle */
                                                $tvaImputationControle = $item;
                                                $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                                                if ($signe != $signeItem)
                                                {
                                                    $sensOppose = true;
                                                    break;
                                                }
                                            }
                                        }

                                        foreach ($tvaImputationsControlesImage as $item)
                                        {
                                            /** @var TvaImputationControle $tvaImputationControle */
                                            $tvaImputationControle = $item;
                                            $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                                            $montant = abs($tvaImputationControle->getMontantTtc());
                                            /** @var Separation $separation */
                                            $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                                                ->createQueryBuilder('s')
                                                ->where('s.image = :image')
                                                ->setParameter('image',$tvaImputationControle->getImage())
                                                ->setMaxResults(1)
                                                ->getQuery()
                                                ->getOneOrNullResult();

                                            $cr = $isCr;
                                            if ($separation)
                                            {
                                                if (in_array($separation->getCategorie()->getId(),[10,12,9,13]))
                                                {
                                                    $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                                        ->getImputationControle($tvaImputationControle);

                                                    if (!$allAvoir && $imputationControle && $imputationControle->getTypePiece() && intval($imputationControle->getTypePiece()->getId()) == 1)
                                                        $cr = !$cr;
                                                }
                                            }

                                            $montant = abs($montant);
                                            if ($sensOppose && $signe != $signeItem)
                                            {
                                                $cr = !$cr;
                                            }

                                            if ($isEngagement)
                                            {
                                                if ($tvaImputationControle->getPccBilan() || $tvaImputationControle->getTiers())
                                                {
                                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                                        ($tvaImputationControle->getPccBilan() ?
                                                            '0-'. $tvaImputationControle->getPccBilan()->getId() :
                                                            '1-' . $tvaImputationControle->getTiers()->getId()
                                                        );

                                                    if (array_key_exists($key,$journaux))
                                                    {
                                                        $solde = $journaux[$key]->cr - $journaux[$key]->db  + ($cr ? 1 : -1) * ($montant);
                                                        $journaux[$key]->db = ($solde < 0 ? abs($solde) : 0);
                                                        $journaux[$key]->cr = ($solde < 0 ? 0 : abs($solde));
                                                    }
                                                    else
                                                    {
                                                        $journaux[$key] = $this->getJournalObject(
                                                            $releve,
                                                            $tvaImputationControle,
                                                            $tvaImputationControle->getPccBilan(),
                                                            $tvaImputationControle->getTiers(),
                                                            $cr ? 0 : abs($montant),
                                                            $cr ? abs($montant) : 0,
                                                            $releve->getId(),
                                                            null,
                                                            null,
                                                            0,
                                                            $tvaImputationControle->getImage()
                                                        );
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                $montantTva = 0;
                                                if ($tvaImputationControle->getPccTva() && $tvaImputationControle->getTvaTaux())
                                                {
                                                    $coeffTva = 1 + floatval($tvaImputationControle->getTvaTaux()->getTaux() / 100);
                                                    $mHT = $montant / $coeffTva;
                                                    $montantTva = $montant - $mHT;
                                                }

                                                if ($tvaImputationControle->getPcc())
                                                {
                                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                                        '0-'. $tvaImputationControle->getPcc()->getId();

                                                    if (array_key_exists($key,$journaux))
                                                    {
                                                        $solde = $journaux[$key]->cr - $journaux[$key]->db  + ($cr ? 1 : -1) * ($montant - $montantTva);
                                                        $journaux[$key]->db = ($solde < 0 ? abs($solde) : 0);
                                                        $journaux[$key]->cr = ($solde < 0 ? 0 : abs($solde));
                                                    }
                                                    else
                                                    {
                                                        $journaux[$key] = $this->getJournalObject(
                                                            $releve,
                                                            $tvaImputationControle,
                                                            $tvaImputationControle->getPcc(),
                                                            null,
                                                            $cr ? 0 : abs($montant - $montantTva),
                                                            $cr ? abs($montant - $montantTva) : 0,
                                                            $releve->getId(),
                                                            null,
                                                            null,
                                                            0,
                                                            $tvaImputationControle->getImage()
                                                        );
                                                    }
                                                }

                                                if ($montantTva != 0)
                                                {
                                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                                        '0-'. $tvaImputationControle->getPccTva()->getId();

                                                    if (array_key_exists($key,$journaux))
                                                    {
                                                        $solde = $journaux[$key]->cr - $journaux[$key]->db  + ($cr ? 1 : -1) * ($montantTva);
                                                        $journaux[$key]->db = ($solde < 0 ? abs($solde) : 0);
                                                        $journaux[$key]->cr = ($solde < 0 ? 0 : abs($solde));
                                                    }
                                                    else
                                                    {
                                                        $journaux[$key] = $this->getJournalObject(
                                                            $releve,
                                                            $tvaImputationControle,
                                                            $tvaImputationControle->getPccTva(),
                                                            null,
                                                            $cr ? 0 : abs($montantTva),
                                                            $cr ? abs($montantTva) : 0,
                                                            $releve->getId(),
                                                            null,
                                                            null,
                                                            0,
                                                            $tvaImputationControle->getImage()
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }


                                    foreach ($banqueSousCategoriesSoeurs as $banqueSousCategoriesSoeur)
                                    {
                                        if ($banqueSousCategoriesSoeur->getCompteTiers() || $banqueSousCategoriesSoeur->getCompteBilan())
                                        {
                                            if ($banqueSousCategoriesSoeur->getCompteTiers())
                                            {
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    null,
                                                    null,
                                                    $banqueSousCategoriesSoeur->getCompteTiers(),
                                                    $credit ? 0 : abs($banqueSousCategoriesSoeur->getMontant()),
                                                    $credit ? abs($banqueSousCategoriesSoeur->getMontant()) : 0,
                                                    $releve->getId(),
                                                    null,
                                                    $banqueSousCategoriesSoeur
                                                );
                                            }
                                            else
                                            {
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    null,
                                                    $banqueSousCategoriesSoeur->getCompteBilan(),
                                                    null,
                                                    $credit ? 0 : abs($banqueSousCategoriesSoeur->getMontant()),
                                                    $credit ? abs($banqueSousCategoriesSoeur->getMontant()) : 0,
                                                    $releve->getId(),
                                                    null,
                                                    $banqueSousCategoriesSoeur
                                                );
                                            }
                                        }
                                        else
                                        {
                                            $mTtc = abs($banqueSousCategoriesSoeur->getMontant());
                                            $coeffTva = 1;
                                            if ($banqueSousCategoriesSoeur->getCompteTva() && $banqueSousCategoriesSoeur->getTvaTaux())
                                                $coeffTva += $banqueSousCategoriesSoeur->getTvaTaux()->getTaux() / 100;
                                            $mHt = $mTtc / $coeffTva;
                                            $mTva = $mTtc - $mHt;

                                            if ($mTva != 0)
                                            {
                                                if ($banqueSousCategoriesSoeur->getCompteChg())
                                                    $journaux[] = $this->getJournalObject(
                                                        $releve,
                                                        null,
                                                        $banqueSousCategoriesSoeur->getCompteTva(),
                                                        null,
                                                        $credit ? 0 : $mTva,
                                                        $credit ? $mTva : 0,
                                                        $releve->getId(),
                                                        null,
                                                        $banqueSousCategoriesSoeur
                                                    );
                                                else
                                                    $journaux[] = $this->getJournalObject(
                                                        $releve,
                                                        null,
                                                        $banqueSousCategoriesSoeur->getCompteTva(),
                                                        null,
                                                        $credit ? 0 : $mTtc,
                                                        $credit ? $mTtc : 0,
                                                        $releve->getId(),
                                                        null,
                                                        $banqueSousCategoriesSoeur
                                                    );
                                            }

                                            if ($banqueSousCategoriesSoeur->getCompteChg())
                                            {
                                                if ($banqueSousCategoriesSoeur->getCompteChg())
                                                    $journaux[] = $this->getJournalObject(
                                                        $releve,
                                                        null,
                                                        $banqueSousCategoriesSoeur->getCompteChg(),
                                                        null,
                                                        $credit ? 0 : $mHt,
                                                        $credit ? $mHt : 0,
                                                        $releve->getId(),
                                                        null,
                                                        $banqueSousCategoriesSoeur
                                                    );
                                            }
                                            else
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    null,
                                                    $pccAttente,
                                                    null,
                                                    $credit ? 0 : $mHt,
                                                    $credit ? $mHt : 0,
                                                    $releve->getId(),
                                                    null,
                                                    $banqueSousCategoriesSoeur
                                                );
                                        }
                                    }
                                }
                                else
                                {
                                    if ($banqueSousCateogrieAutre->getCompteTiers() || $banqueSousCateogrieAutre->getCompteBilan())
                                    {
                                        if ($banqueSousCateogrieAutre->getCompteTiers())
                                        {
                                            $journaux[] = $this->getJournalObject(
                                                $releve,
                                                null,
                                                null,
                                                $banqueSousCateogrieAutre->getCompteTiers(),
                                                $credit ? 0 : abs($banqueSousCateogrieAutre->getMontant()),
                                                $credit ? abs($banqueSousCateogrieAutre->getMontant()) : 0,
                                                $releve->getId(),
                                                null,
                                                $banqueSousCateogrieAutre
                                            );
                                        }
                                        else
                                        {
                                            $journaux[] = $this->getJournalObject(
                                                $releve,
                                                null,
                                                $banqueSousCateogrieAutre->getCompteBilan(),
                                                null,
                                                $credit ? 0 : abs($banqueSousCateogrieAutre->getMontant()),
                                                $credit ? abs($banqueSousCateogrieAutre->getMontant()) : 0,
                                                $releve->getId(),
                                                null,
                                                $banqueSousCateogrieAutre
                                            );
                                        }
                                    }
                                    else
                                    {
                                        $mTtc = abs($banqueSousCateogrieAutre->getMontant());
                                        $coeffTva = 1;
                                        if ($banqueSousCateogrieAutre->getCompteTva() && $banqueSousCateogrieAutre->getTvaTaux())
                                            $coeffTva += $banqueSousCateogrieAutre->getTvaTaux()->getTaux() / 100;
                                        $mHt = $mTtc / $coeffTva;
                                        $mTva = $mTtc - $mHt;

                                        if ($mTva != 0)
                                        {
                                            if ($banqueSousCateogrieAutre->getCompteChg())
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    null,
                                                    $banqueSousCateogrieAutre->getCompteTva(),
                                                    null,
                                                    $credit ? 0 : $mTva,
                                                    $credit ? $mTva : 0,
                                                    $releve->getId(),
                                                    null,
                                                    $banqueSousCateogrieAutre
                                                );
                                            else
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    null,
                                                    $banqueSousCateogrieAutre->getCompteTva(),
                                                    null,
                                                    $credit ? 0 : $mTtc,
                                                    $credit ? $mTtc : 0,
                                                    $releve->getId(),
                                                    null,
                                                    $banqueSousCateogrieAutre
                                                );
                                        }

                                        if ($banqueSousCateogrieAutre->getCompteChg())
                                        {
                                            if ($banqueSousCateogrieAutre->getCompteChg())
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    null,
                                                    $banqueSousCateogrieAutre->getCompteChg(),
                                                    null,
                                                    $credit ? 0 : $mHt,
                                                    $credit ? $mHt : 0,
                                                    $releve->getId(),
                                                    null,
                                                    $banqueSousCateogrieAutre
                                                );
                                        }
                                        else
                                            $journaux[] = $this->getJournalObject(
                                                $releve,
                                                null,
                                                $pccAttente,
                                                null,
                                                $credit ? 0 : $mHt,
                                                $credit ? $mHt : 0,
                                                $releve->getId(),
                                                null,
                                                $banqueSousCateogrieAutre
                                            );
                                    }
                                }
                            }
                        }
                    }
                }
                elseif ($releve->getCleDossier())
                {
                    $cleDossier = $releve->getCleDossier();

                    $typeCompta = intval($cleDossier->getTypeCompta());

                    $restesCleDossierExt = $this->getEntityManager()->getRepository('AppBundle:CleDossierExt')
                        ->getRestes($cleDossier);

                    if ($restesCleDossierExt)
                    {
                        $idsNotIns = [$restesCleDossierExt->getId()];

                        $cleDossierExts = $this->getEntityManager()->getRepository('AppBundle:CleDossierExt')
                            ->getForCleDossier($cleDossier, $idsNotIns);

                        $nonReste = 0;

                        foreach ($cleDossierExts as $cleDossierExt)
                        {
                            /** @var ReleveExt[] $releveExts */
                            $releveExts = $this->getEntityManager()->getRepository('AppBundle:ReleveExt')
                                ->findBy([
                                    'releve' => $releve,
                                    'cleDossierExt' => $cleDossierExt
                                ]);

                            foreach ($releveExts as $releveExt)
                            {
                                if ($releveExt->getImageFlague())
                                {
                                    $soeurs = $this->getEntityManager()->getRepository('AppBundle:ImageFlague')
                                        ->getSoeurs($releveExt->getImageFlague());

                                    $tvaImputationsControlesImages = $soeurs->tic;

                                    $allAvoir = true;
                                    foreach ($tvaImputationsControlesImages as $ki => $item)
                                    {
                                        $img = $this->getEntityManager()->getRepository('AppBundle:Image')
                                            ->find($ki);

                                        $ic = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                            ->getByImage($img);

                                        if ($ic && $ic->getTypePiece() && $ic->getTypePiece()->getId() > 1)
                                        {
                                            $allAvoir = false;
                                            break;
                                        }
                                    }

                                    foreach ($tvaImputationsControlesImages as $tvaImputationsControlesImage)
                                    {
                                        $sensOppose = false;
                                        $inverse = false;

                                        $signe = ($tvaImputationsControlesImage[0]->getMontantTtc() < 0);
                                        if (count($tvaImputationsControlesImage) > 1)
                                        {
                                            foreach ($tvaImputationsControlesImage as $item)
                                            {
                                                /** @var TvaImputationControle $tvaImputationControle */
                                                $tvaImputationControle = $item;
                                                $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                                                if ($signe != $signeItem)
                                                {
                                                    $sensOppose = true;
                                                    break;
                                                }
                                            }
                                        }

                                        foreach ($tvaImputationsControlesImage as $item)
                                        {
                                            /** @var TvaImputationControle $tvaImputationControle */
                                            $tvaImputationControle = $item;
                                            $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                                            $montant = abs($tvaImputationControle->getMontantTtc());
                                            /** @var Separation $separation */
                                            $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                                                ->createQueryBuilder('s')
                                                ->where('s.image = :image')
                                                ->setParameter('image',$tvaImputationControle->getImage())
                                                ->setMaxResults(1)
                                                ->getQuery()
                                                ->getOneOrNullResult();

                                            $cr = $credit;
                                            if ($separation)
                                            {
                                                if (in_array($separation->getCategorie()->getId(),[10,12,9,13]))
                                                {
                                                    $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                                        ->getImputationControle($tvaImputationControle);

                                                    if (!$allAvoir && $imputationControle && $imputationControle->getTypePiece() && intval($imputationControle->getTypePiece()->getId()) == 1)
                                                        $cr = !$cr;
                                                }
                                            }

                                            $montant = abs($montant);
                                            if ($sensOppose && $signe != $signeItem)
                                            {
                                                $cr = !$cr;
                                            }

                                            if ($typeCompta == 0)
                                            {
                                                if ($tvaImputationControle->getPccBilan() || $tvaImputationControle->getTiers())
                                                {
                                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                                        ($tvaImputationControle->getPccBilan() ?
                                                            '0-'. $tvaImputationControle->getPccBilan()->getId() :
                                                            '1-' . $tvaImputationControle->getTiers()->getId()
                                                        );

                                                    if (array_key_exists($key,$journaux))
                                                    {
                                                        $solde = $journaux[$key]->cr - $journaux[$key]->db  + ($cr ? 1 : -1) * ($montant);
                                                        $journaux[$key]->db = ($solde < 0 ? abs($solde) : 0);
                                                        $journaux[$key]->cr = ($solde < 0 ? 0 : abs($solde));

                                                        $nonReste += ($cr ? 1 : -1) * ($montant);
                                                    }
                                                    else
                                                    {
                                                        $journaux[$key] = $this->getJournalObject(
                                                            $releve,
                                                            $tvaImputationControle,
                                                            $tvaImputationControle->getPccBilan(),
                                                            $tvaImputationControle->getTiers(),
                                                            $cr ? 0 : abs($montant),
                                                            $cr ? abs($montant) : 0,
                                                            $releve->getId(),
                                                            null,
                                                            null,
                                                            0,
                                                            $tvaImputationControle->getImage(),
                                                            null,$montant
                                                        );

                                                        $nonReste += $cr ? $montant : -$montant;
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                $montantTva = 0;
                                                if ($tvaImputationControle->getPccTva() && $tvaImputationControle->getTvaTaux())
                                                {
                                                    $coeffTva = 1 + floatval($tvaImputationControle->getTvaTaux()->getTaux() / 100);
                                                    $mHT = $montant / $coeffTva;
                                                    $montantTva = $montant - $mHT;
                                                }

                                                if ($tvaImputationControle->getPcc())
                                                {
                                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                                        '0-'. $tvaImputationControle->getPcc()->getId();

                                                    if (array_key_exists($key,$journaux))
                                                    {
                                                        $solde = $journaux[$key]->cr - $journaux[$key]->db  + ($cr ? 1 : -1) * ($montant - $montantTva);
                                                        $journaux[$key]->db = ($solde < 0 ? abs($solde) : 0);
                                                        $journaux[$key]->cr = ($solde < 0 ? 0 : abs($solde));

                                                        $nonReste += ($cr ? 1 : -1) * ($montant - $montantTva);
                                                    }
                                                    else
                                                    {
                                                        $journaux[$key] = $this->getJournalObject(
                                                            $releve,
                                                            $tvaImputationControle,
                                                            $tvaImputationControle->getPcc(),
                                                            null,
                                                            $cr ? 0 : abs($montant - $montantTva),
                                                            $cr ? abs($montant - $montantTva) : 0,
                                                            $releve->getId(),
                                                            null,
                                                            null,
                                                            0,
                                                            $tvaImputationControle->getImage()
                                                        );

                                                        $nonReste += $cr ? $montant - $montantTva : -($montant - $montantTva);
                                                    }
                                                }

                                                if ($montantTva != 0)
                                                {
                                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                                        '0-'. $tvaImputationControle->getPccTva()->getId();

                                                    if (array_key_exists($key,$journaux))
                                                    {
                                                        $solde = $journaux[$key]->cr - $journaux[$key]->db  + ($cr ? 1 : -1) * ($montantTva);
                                                        $journaux[$key]->db = ($solde < 0 ? abs($solde) : 0);
                                                        $journaux[$key]->cr = ($solde < 0 ? 0 : abs($solde));

                                                        $nonReste += ($cr ? 1 : -1) * ($montantTva);
                                                    }
                                                    else
                                                    {
                                                        $journaux[$key] = $this->getJournalObject(
                                                            $releve,
                                                            $tvaImputationControle,
                                                            $tvaImputationControle->getPccTva(),
                                                            null,
                                                            $cr ? 0 : abs($montantTva),
                                                            $cr ? abs($montantTva) : 0,
                                                            $releve->getId(),
                                                            null,
                                                            null,
                                                            0,
                                                            $tvaImputationControle->getImage()
                                                        );

                                                        $nonReste += $cr ? $montantTva : -($montantTva);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $journaux[] = $this->getJournalObject(
                                        $releve,
                                        null,
                                        $releveExt->getCleDossierExt()->getPcc(),
                                        $releveExt->getCleDossierExt()->getTiers(),
                                        floatval($releveExt->getMontant()) < 0 ? abs(floatval($releveExt->getMontant())) : 0,
                                        floatval($releveExt->getMontant()) < 0 ? 0 : abs(floatval($releveExt->getMontant())),
                                        $releve->getId()
                                    );

                                    $nonReste += floatval($releveExt->getMontant());
                                }
                            }
                        }

                        $reste = $releve->getCredit() - $releve->getDebit() - $nonReste;

                        $journaux[] = $this->getJournalObject(
                            $releve,
                            null,
                            $restesCleDossierExt->getPcc(),
                            $restesCleDossierExt->getTiers(),
                            $reste < 0 ? abs($reste) : 0,
                            $reste < 0 ? 0 : abs($reste),
                            $releve->getId(),null,null,0,null,null,
                            ($releve->getCredit() - $releve->getDebit()) .  '-' . $nonReste
                        );

                        /*$pccAttente = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                            ->getPccAttenteBanque($banqueCompte->getDossier(),!$credit);

                        $journaux[] = $this->getJournalObject(
                            $releve,
                            null,
                            $pccAttente,
                            null,
                            $credit ? 0 : $montantReleve,
                            $credit ? $montantReleve : 0,
                            $releve->getId()
                        );*/
                    }
                    else
                    {
                        $adds = $this->getReleveAdd($releve->getLibelle());
                        $montantTva = 0;
                        $montantAdds = [];
                        $montantAddTotal = 0;

                        foreach ($adds as $cle => $add)
                        {
                            if ($add->v > $montantReleve) continue;

                            if ($cle == 'TVA')
                            {
                                if ($cleDossier->getTva())
                                {
                                    if ($add->v != 0) $montantTva = $add->v;
                                    elseif($add->t != 0) $montantTva = ($add->t != 0) ? ($montantReleve * $add->t / 100) : $add->v;
                                }
                            }
                            else
                            {
                                $montantAdd = 0;
                                if ($add->v != 0) $montantAdd = $add->v;
                                elseif($add->t != 0) $montantAdd = ($add->t != 0) ? ($montantReleve * $add->t / 100) : $add->v;

                                if ($montantAdd != 0)
                                {
                                    $montantAdds[$cle] = $montantAdd;
                                    $montantAddTotal += $montantAdd;
                                }
                            }
                        }

                        if ($cleDossier->getTva())
                        {
                            if ($montantTva == 0)
                            {
                                $coeffTva = 1 + $cleDossier->getTauxTva() / 100;
                                $mHoT = $montantReleve / $coeffTva;
                                $montantTva = $montantReleve - $mHoT;
                            }

                            if ($montantTva != 0)
                            {
                                //if (intval(substr($cleDossier->getTva()->getCompte(),0,4)) == 4456)
                                $journaux[] = $this->getJournalObject(
                                    $releve,
                                    null,
                                    $cleDossier->getTva(),
                                    null,
                                    $credit ? 0 : $montantTva,
                                    $credit ? $montantTva : 0, // $montantTva
                                    $releve->getId()
                                );
                                //$montantAddTotal -= $montantTva * 2;
                            }
                            /*else
                            {
                                if (intval(substr($cleDossier->getTva()->getCompte(),0,4)) == 4456)
                                    $journaux[] = $this->getJournalObject(
                                        $releve,
                                        null,
                                        $cleDossier->getTva(),
                                        null,
                                        $credit ? 0 : $montantTva,
                                        $credit ? $montantTva : 0,
                                        $releve->getId()
                                    );
                            }*/
                        }

                        if ($montantAddTotal != 0)
                        {
                            foreach ($montantAdds as $montantAdd)
                            {
                                if ($cleDossier->getResultat())
                                {
                                    if (intval(substr($cleDossier->getResultat()->getCompte(),0,3)) == 627 && ($cleDossier->getBilanTiers() && $cleDossier->getBilanPcc()))
                                    {
                                        $journaux[] = $this->getJournalObject(
                                            $releve,
                                            null,
                                            $cleDossier->getResultat(),
                                            null,
                                            $credit ? $montantAdd : 0,
                                            $credit ? 0 : $montantAdd,
                                            $releve->getId()
                                        );
                                        $montantAddTotal -= $montantAdd * 2;
                                    }
                                    else
                                    {
                                        $journaux[] = $this->getJournalObject(
                                            $releve,
                                            null,
                                            $cleDossier->getResultat(),
                                            null,
                                            $credit ? 0 : $montantAdd,
                                            $credit ? $montantAdd : 0,
                                            $releve->getId()
                                        );
                                    }
                                }
                                elseif ($cleDossier->getBilanTiers() || $cleDossier->getBilanPcc())
                                {
                                    $journaux[] = $this->getJournalObject(
                                        $releve,
                                        null,
                                        $cleDossier->getBilanPcc(),
                                        $cleDossier->getBilanTiers(),
                                        $credit ? 0 : $montantAdd,
                                        $credit ? $montantAdd : 0,
                                        $releve->getId()
                                    );
                                }
                            }
                        }

                        if ($montantReleve - $montantTva - $montantAddTotal != 0)
                        {
                            if ($cleDossier->getBilanTiers() || $cleDossier->getBilanPcc())
                            {
                                $journaux[] = $this->getJournalObject(
                                    $releve,
                                    null,
                                    $cleDossier->getBilanPcc(),
                                    $cleDossier->getBilanTiers(),
                                    $credit ? 0 : $montantReleve - $montantTva - $montantAddTotal,
                                    $credit ? $montantReleve - $montantTva - $montantAddTotal : 0,
                                    $releve->getId()
                                );
                            }
                            elseif ($cleDossier->getResultat())
                            {
                                $journaux[] = $this->getJournalObject(
                                    $releve,
                                    null,
                                    $cleDossier->getResultat(),
                                    null,
                                    $credit ? 0 : $montantReleve - $montantTva - $montantAddTotal,
                                    $credit ? $montantReleve - $montantTva - $montantAddTotal : 0,
                                    $releve->getId()
                                );
                            }
                        }
                    }
                }
                else
                {
                    $pccAttente = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                        ->getPccAttenteBanque($banqueCompte->getDossier(),!$credit);

                    $journaux[] = $this->getJournalObject(
                        $releve,
                        null,
                        $pccAttente,
                        null,
                        $credit ? 0 : $montantReleve,
                        $credit ? $montantReleve : 0,
                        $releve->getId()
                    );
                }
            }
        }

        return (object)
            [
                'datas' => array_values($journaux),
                'footers' => (object)
                [
                    'cr' => $total,
                    'db' => $total
                ]
            ];
    }

    /**
     * @param Releve $releve
     * @param TvaImputationControle|null $tvaImputationControle
     * @param Pcc|null $pcc
     * @param Tiers|null $tiers
     * @param int $credit
     * @param int $debit
     * @param int $groupe
     * @param CleDossier $cleDossier
     * @param BanqueSousCategorieAutre $banqueSousCategorieAutre
     * @param int $isBanque
     * @param Image $imageNom
     * @param BanqueSousCategorieAutre $bscaParent
     * @return object
     */
    private function getJournalObject(Releve $releve, TvaImputationControle $tvaImputationControle = null, Pcc $pcc = null, Tiers $tiers = null, $credit = 0, $debit = 0, $groupe = 0, CleDossier $cleDossier = null, BanqueSousCategorieAutre $banqueSousCategorieAutre = null,$isBanque = 0,Image $imageNom = null,BanqueSousCategorieAutre $bscaParent = null, $libelleAdd = '')
    {
        $date = $releve->getDateReleve();
        $libelle = $this->getEntityManager()->getRepository('AppBundle:Releve')
            ->getLibelleWithComplement($releve,$this->cfonbCodeActives);
        $libelleComplete =  $libelle;
        $image = $releve->getImage();
        $imageSoeur = $image;
        $idEntity = $releve->getId();
        $typeEntity = 0;

        if ($tvaImputationControle)
        {
            $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                ->getImputationControle($tvaImputationControle);

            $libelle = '';
            if ($imputationControle && trim($imputationControle->getRs()) != '')
                $libelle .= $imputationControle->getRs();
            if (trim($libelle) == '' && $tvaImputationControle->getTiers()) $libelle .= $tvaImputationControle->getTiers()->getIntitule();

            if ($imputationControle)
            {
                if ($imputationControle->getNumFacture() && trim($imputationControle->getNumFacture() != ''))
                    $libelle .= (($libelle != '') ? ' - ' : '') . $imputationControle->getNumFacture();
                /*if ($imputationControle->getDateFacture())
                    $libelle .= (($libelle != '') ? ' - ' : '') . $imputationControle->getDateFacture()->format('d/m/y');*/
                if ($imputationControle->getModeReglement() && $imputationControle->getModeReglement()->getLibelle() != '')
                    $libelle .= (($libelle != '') ? ' - ' : '') . $imputationControle->getModeReglement()->getLibelle();
            }

            $image = $tvaImputationControle->getImage();
            $imageSoeur = $tvaImputationControle->getImage();

            $idEntity = $tvaImputationControle->getId();
            $typeEntity = 1;
        }
        elseif ($releve->getEcritureChange() == 1 && $tiers) $libelle = $tiers->getIntitule();

        if ($libelle == '')
            $libelle = $libelleComplete;

        if ($banqueSousCategorieAutre)
        {
            $libelle = $this->getEntityManager()->getRepository('AppBundle:BanqueSousCategorieAutre')
                ->getLibelleComplete($banqueSousCategorieAutre);

            if ($banqueSousCategorieAutre->getImage())
                $image = $banqueSousCategorieAutre->getImage();

            $imageSoeur = $banqueSousCategorieAutre->getImage();
            $idEntity = $banqueSousCategorieAutre->getId();
            $typeEntity = 2;
        }

        if ($bscaParent)
        {
            $idEntity = $bscaParent->getId();
            $typeEntity = 2;
        }

        if ($this->journalCentraliser && $pcc && substr($pcc->getCompte(),0,3) == '512')
        {
            $annee = $releve->getDateReleve()->format('Y');
            $mois = $releve->getDateReleve()->format('m');

            $mois++;
            if ($mois == 13)
            {
                $annee++;
                $mois = 1;
            }
            if ($mois < 10) $mois = '0'.$mois;

            $date = \DateTime::createFromFormat('Y-m-d',$annee.'-'.$mois.'-01');
            $date->sub(new \DateInterval('P1D'));
            $libelle = 'Mois: ' . $releve->getDateReleve()->format('m/Y');
        }

        $isBq = false;
        $numBq = ($pcc) ? $pcc->getCompte() : $tiers->getCompteStr();
        if(substr($numBq,0,3) == '512') $isBq = true;

        if($releve->getCleDossier() && !$isBq){
            $cleExceptionPm = $this->getEntityManager()->getRepository('AppBundle:CleExceptionPm')
                ->cleExceptionForCleDossier($releve->getCleDossier());
            if($cleExceptionPm){
                $releveIsDebit = ($releve->getCredit() - $releve->getDebit() > 0);
                $cleExceptionPm = $this->getEntityManager()->getRepository('AppBundle:CleExceptionPm')
                    ->cleExceptionForCleDossier($releve->getCleDossier());
                $continue = false;

                if ($cleExceptionPm)
                {
                    $formule = trim($cleExceptionPm->getFormule());
                    $sens = intval($cleExceptionPm->getSens());
                    $formule2 = trim($cleExceptionPm->getFormule2());
                    $sens2 = intval($cleExceptionPm->getSens2());
                    $listVal['x'] = abs($releve->getCredit() - $releve->getDebit());
                    $langage = new ExpressionLanguage();

                    //Formule
                    if ($formule != '' && ($sens == 0 || $sens == 1) && $releveIsDebit)
                    {
                        $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule))),$listVal);
                    }
                    if ($formule != '' && ($sens == 0 || $sens == 2) && !$releveIsDebit && !$continue)
                    {
                        $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule))),$listVal);
                    }
                    //Formule2
                    if ($formule2 != '' && ($sens2 == 0 || $sens2 == 1) && $releveIsDebit && !$continue)
                    {
                        $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule2))),$listVal);
                    }
                    if ($formule2 != '' && ($sens2 == 0 || $sens2 == 2) && !$releveIsDebit && !$continue)
                    {
                        $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule2))),$listVal);
                    }
                }
                if ($continue) $libelle = $libelle.' '.$cleExceptionPm->getMotCle();
            }
        }

        $this->id++;

        //9840586

        return (object)
        [
            'id' => $this->id,
            'd' => $date->format('d/m/Y'),
            'l' => $libelle . (($libelleAdd == '') ? '' : ' # ' . $libelleAdd),
            'i' => ($imageNom && intval($this->methoCompta) == 1) ? $imageNom->getNom() : $releve->getImage()->getNom(),
            'c' => (object)
            [
                'id' => Boost::boost($pcc ? $pcc->getId() : $tiers->getId()),
                't' => $pcc ? 0 : 1,
                'l' => $pcc ? $pcc->getCompte() : $tiers->getCompteStr(),
                'i' => $pcc ? $pcc->getIntitule() : $tiers->getIntitule()
            ],
            'db' => $credit,
            'cr' => $debit,
            'imi' => Boost::boost($image->getId()),
            'g' => ($this->journalCentraliser) ? $releve->getDateReleve()->format('Ym') : $groupe,
            'cle' => $cleDossier ? $cleDossier->getCle()->getCle() : '',
            'jnl' => $this->journalDossier ? $this->journalDossier->getCodeStr() : '',
            'isb' => $isBanque,
            'ims' => (object)
            [
                'id' => Boost::boost($imageSoeur->getId()),
                'n' => $imageSoeur->getNom(),
                'id_nc' => $imageSoeur->getId()
            ],
            'ent' => (object)
            [
                'id' => Boost::boost($idEntity),
                't' => $typeEntity
            ],
            'co' => (object)
            [
                't' => $pcc ? 0 : 1,
                'c' => $pcc ? $pcc : $tiers
            ]
        ];
    }

    /**
     * @param $libelle
     * @return array
     */
    public function getReleveAdd($libelle)
    {
        $cles = ['TVA','COM','COMMISSION','INT','INTERET','INTERÊT','CAP'];

        //$res = '';

        $adds = [];
        foreach ($cles as $cle)
        {
            $add = $this->getReleveAddItem($libelle,$cle);
            if ($add)
                $adds[$cle] = $add;
        }

        //return $res;

        return $adds;
    }

    /**
     * @param $libelle
     * @param $cle
     * @return null|object
     */
    public function getReleveAddItem($libelle,$cle)
    {
        //https://openclassrooms.com/fr/courses/146276-tout-sur-le-javascript/145569-lobjet-regexp
        $libelle = strtoupper($libelle);

        $tvaTaux = 0;
        $tvaVal = 0;

        if ($cle === 'TVA') preg_match("#".$cle."(.*)[%|EUR|E|;]#", $libelle.'x', $adds);
        else preg_match("#".$cle."([0-9 .,].*)E[ ;]#", $libelle.'x', $adds);

        if ($cle != 'TVA' && count($adds) > 0)
            $tvaVal = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$adds[0]))));

        if ($cle == 'TVA')
        {
            //TVA 20.00 : 0.03E
            preg_match("#".$cle."([0-9 .,].*):(.*)[E|EUR]#", $libelle.'x', $adds1);
            if (count($adds1) > 0)
            {
                preg_match("#:(.*)[EUR|E|;]#", $adds1[0], $vals);
                if (count($vals) > 0)
                    $tvaVal = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$vals[0]))));
            }

            if ($tvaVal == 0 && count($adds) > 0)
            {
                preg_match("#%(.*)[EUR|E|;]#", $adds[0], $vals);
                if (count($vals) > 0)
                    $tvaVal = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$vals[0]))));

                if ($tvaVal == 0)
                {
                    preg_match("#".$cle.".TAUX(.*)[EUR|E](.*)[;|xxxx]#",$adds[0].'xxxx',$vals);
                    if (count($vals) > 1)
                        $tvaVal = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$vals[2]))));
                }

                if ($tvaVal == 0)
                {
                    preg_match("#".$cle."(.*)[EUR|E]#",$adds[0],$vals);
                    if (count($vals) > 0)
                        $tvaVal = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$vals[0]))));
                }
            }

            if ($tvaVal + $tvaTaux == 0)
            {
                if (count($adds) > 0)
                {
                    $add = $adds[0];
                    if (trim($add) != '')
                    {
                        preg_match("#".$cle."(.*)%#",$add.'x',$tauxs);
                        if (count($tauxs) > 0)
                            $tvaTaux = floatval(preg_replace('[,| ]','.',trim(preg_replace('/[^0-9 .,]/','',$tauxs[0]))));

                        if ($tvaTaux == 0)
                        {
                            preg_match("#".$cle.".TAUX(.*)[EUR|E]#",$add.'x',$tauxs);
                            if (count($tauxs) > 0)
                                $tvaTaux = floatval(preg_replace('[,| ]','.',trim(preg_replace('/[^0-9 .,]/','',$tauxs[0]))));
                        }
                        if ($tvaTaux == 0)
                        {
                            preg_match("#".$cle."(.*)[EUR|E]#",$add.'x',$tauxs);
                            if (count($tauxs) > 0)
                                $tvaVal = floatval(preg_replace('[,| ]','.',trim(preg_replace('/[^0-9 .,]/','',$tauxs[0]))));
                        }
                    }
                }
            }
        }
        else
        {
            if ($tvaVal == 0)
            {
                preg_match("#".$cle."([0-9 .,].*);#", $libelle, $adds);
                if (count($adds) > 0) $tvaVal = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$adds[0]))));
                else
                {
                    preg_match("#".$cle."([0-9 .,].*)xyzzyx#", $libelle.'xyzzyx', $adds);
                    if (count($adds) > 0) $tvaVal = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$adds[0]))));
                }
            }

            if ($tvaVal == 0)
            {
                preg_match("#".$cle."([0-9 .,=].*);#", $libelle, $adds);
                if (count($adds) > 0) $tvaVal = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$adds[0]))));
                else
                {
                    preg_match("#".$cle."([0-9 .,=].*)xyzzyx#", $libelle.'xyzzyx', $adds);
                    if (count($adds) > 0) $tvaVal = floatval(preg_replace('[,| ]','.', trim(preg_replace('/[^0-9 .,]/','',$adds[0]))));
                }
            }
        }

        $tva = null;
        if ($tvaVal + $tvaTaux != 0)
            $tva = (object)
            [
                't' => $tvaTaux,
                'v' => $tvaVal
            ];

        return $tva;
    }

    /**
     * @param Releve $releve
     * @param CfonbCode[] $cfonbCodeActives
     * @return mixed|string
     */
    public function getLibelleWithComplement(Releve $releve, $cfonbCodeActives = [])
    {
        /** @var ReleveComplementaire[] $releveComplementaires */
        $releveComplementaires = [];
        if (count($cfonbCodeActives) > 0)
            $releveComplementaires = $this->getEntityManager()->getRepository('AppBundle:ReleveComplementaire')
                ->getActives($releve,$cfonbCodeActives);

        $libelle = trim($releve->getLibelle());

        foreach ($releveComplementaires as $releveComplementaire)
            $libelle .= ';' . trim($releveComplementaire->getLibelle());

        $libelle = preg_replace('/\s+/', ' ',$libelle);
        return $libelle;
    }

    /**
     * @param BanqueCompte $banqueCompte
     * @param Releve[] $releves
     * @param array $relevesPasses
     * @param $total
     * @param int $methodeCompta
     * @return object
     */
    public function journalReleve(BanqueCompte $banqueCompte, $releves, &$relevesPasses = [], &$total, $methodeCompta = 0)
    {
        $this->methoCompta = $methodeCompta;
        $this->journalDossier = $banqueCompte->getJournalDossier();

        $journaux = [];
        $pccBanque = $banqueCompte->getPcc();

        foreach ($releves as $key => $releve)
        {
            //$isDebit = $releve->getCredit() - $releve->getDebit() > 0;
            if (in_array($releve->getId(),$relevesPasses)) continue;
            $relevesPasses[] = $releve->getId();

            if ($this->journalCentraliser)
            {
                $solde = floatval($releve->getCredit()) - floatval($releve->getDebit());
                $keyMoi = $releve->getDateReleve()->format('Ym') . (($solde > 0) ? '_c' : '_d');
                if (array_key_exists($keyMoi,$journaux))
                {
                    if ($solde < 0)
                        $journaux[$keyMoi]->cr =
                            floatval($journaux[$keyMoi]->cr) + abs($solde);
                    else
                        $journaux[$keyMoi]->db =
                            floatval($journaux[$keyMoi]->db) + abs($solde);
                }
                else
                {
                    $journaux[$keyMoi] = $this->getJournalObject(
                        $releve,
                        null,
                        $pccBanque,
                        null,
                        $releve->getCredit(),
                        $releve->getDebit(),
                        $releve->getId(),
                        null,
                        null,
                        1
                    );
                    $keyMois[$keyMoi] = $this->id - 1;
                }
            }
            else
            {
                $journaux[] = $this->getJournalObject(
                    $releve,
                    null,
                    $pccBanque,
                    null,
                    $releve->getCredit(),
                    $releve->getDebit(),
                    $releve->getId(),
                    $releve->getCleDossier(),
                    null,
                    1
                );
            }

            //continue;

            $credit = ($releve->getCredit() - $releve->getDebit() > 0);
            $montantReleve = abs($releve->getCredit() - $releve->getDebit());

            $typeCompta = intval($releve->getEngagementTresorerie());

            $ecritureChange = false;
            if (intval($releve->getEcritureChange()) == 1)
            {
                //0: bilan pcc, 1: tiers,  2: resultat, 3: tva
                $releveImputations = $this->getEntityManager()->getRepository('AppBundle:ReleveImputation')
                    ->getImputation($releve);

                if (count($releveImputations) > 0)
                {
                    $ecritureChange = true;

                    foreach ($releveImputations as $releveImputation)
                    {
                        $m = abs($releveImputation->getCredit() - $releveImputation->getDebit());
                        $journaux[] = $this->getJournalObject(
                            $releve,
                            null,
                            $releveImputation->getPcc(),
                            $releveImputation->getTiers(),
                            $credit ? 0 : $m,
                            $credit ? $m : 0,
                            $releve->getId()
                        );
                    }
                }
                else
                {
                    $releve->setEcritureChange(0);
                    $this->getEntityManager()->flush();
                }
            }

            if (!$ecritureChange)
            {
                if ($releve->getImageFlague())
                {
                    $soeurs = $this->getEntityManager()->getRepository('AppBundle:ImageFlague')
                        ->getSoeurs($releve->getImageFlague(),$releve);

                    $tvaImputationsControlesImages = $soeurs->tic;

                    $allAvoir = true;
                    foreach ($tvaImputationsControlesImages as $ki => $item)
                    {
                        $img = $this->getEntityManager()->getRepository('AppBundle:Image')
                            ->find($ki);

                        $ic = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                            ->getByImage($img);

                        if ($ic && $ic->getTypePiece() && $ic->getTypePiece()->getId() > 1)
                        {
                            $allAvoir = false;
                            break;
                        }
                    }

                    foreach ($tvaImputationsControlesImages as $tvaImputationsControlesImage)
                    {
                        $sensOppose = false;
                        $inverse = false;

                        $signe = ($tvaImputationsControlesImage[0]->getMontantTtc() < 0);
                        if (count($tvaImputationsControlesImage) > 1)
                        {
                            foreach ($tvaImputationsControlesImage as $item)
                            {
                                /** @var TvaImputationControle $tvaImputationControle */
                                $tvaImputationControle = $item;
                                $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                                if ($signe != $signeItem)
                                {
                                    $sensOppose = true;
                                    break;
                                }
                            }
                        }

                        foreach ($tvaImputationsControlesImage as $item)
                        {
                            /** @var TvaImputationControle $tvaImputationControle */
                            $tvaImputationControle = $item;
                            $signeItem = ($tvaImputationControle->getMontantTtc() < 0);
                            $montant = abs($tvaImputationControle->getMontantTtc());
                            /** @var Separation $separation */
                            $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                                ->createQueryBuilder('s')
                                ->where('s.image = :image')
                                ->setParameter('image',$tvaImputationControle->getImage())
                                ->setMaxResults(1)
                                ->getQuery()
                                ->getOneOrNullResult();

                            $cr = $credit;
                            if ($separation)
                            {
                                if (in_array($separation->getCategorie()->getId(),[10,12,9,13]))
                                {
                                    $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                        ->getImputationControle($tvaImputationControle);

                                    if (!$allAvoir && $imputationControle && $imputationControle->getTypePiece() && intval($imputationControle->getTypePiece()->getId()) == 1)
                                        $cr = !$cr;

                                    /*if (in_array($separation->getCategorie()->getId(),[10,12]))
                                    {

                                        if ($imputationControle && (!$imputationControle->getTypePiece() || $imputationControle->getTypePiece() && $imputationControle->getTypePiece()->getId() != 1))
                                            $montant *= -1;
                                    }
                                    else
                                    {
                                        if ($imputationControle && $imputationControle->getTypePiece() && $imputationControle->getTypePiece()->getId() == 1)
                                            $montant *= -1;
                                    }*/
                                }
                            }

                            $montant = abs($montant);
                            if ($sensOppose && $signe != $signeItem)
                            {
                                $montant *= -1;
                                if ($signe != $signeItem)
                                {
                                    //$montant *= -1;
                                }
                            }

                            if ($typeCompta == 0)
                            {
                                if ($tvaImputationControle->getPccBilan() || $tvaImputationControle->getTiers())
                                {
                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                        ($tvaImputationControle->getPccBilan() ?
                                            '0-'. $tvaImputationControle->getPccBilan()->getId() :
                                            '1-' . $tvaImputationControle->getTiers()->getId()
                                        );

                                    if (array_key_exists($key,$journaux))
                                    {
                                        $solde = $montant + abs($journaux[$key]->cr - $journaux[$key]->db);
                                        $journaux[$key]->db = ($cr ? 0 : abs($solde));
                                        $journaux[$key]->cr = ($cr ? abs($solde) : 0);
                                    }
                                    else
                                    {
                                        $journaux[$key] = $this->getJournalObject(
                                            $releve,
                                            $tvaImputationControle,
                                            $tvaImputationControle->getPccBilan(),
                                            $tvaImputationControle->getTiers(),
                                            $cr ? 0 : abs($montant),
                                            $cr ? abs($montant) : 0,
                                            $releve->getId(),
                                            null,
                                            null,
                                            0,
                                            $tvaImputationControle->getImage()
                                        );
                                    }
                                }
                            }
                            else
                            {
                                $montantTva = 0;
                                if ($tvaImputationControle->getPccTva() && $tvaImputationControle->getTvaTaux())
                                {
                                    $coeffTva = 1 + floatval($tvaImputationControle->getTvaTaux()->getTaux() / 100);
                                    $mHT = $montant / $coeffTva;
                                    $montantTva = $montant - $mHT;
                                }

                                if ($tvaImputationControle->getPcc())
                                {
                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                        '0-'. $tvaImputationControle->getPcc()->getId();

                                    if ($signe != $signeItem) $cr = !$cr;

                                    if (array_key_exists($key,$journaux))
                                    {
                                        $solde = $montant - $montantTva + abs($journaux[$key]->cr - $journaux[$key]->db);
                                        $journaux[$key]->db = ($cr ? 0 : abs($solde));
                                        $journaux[$key]->cr = ($cr ? abs($solde) : 0);
                                    }
                                    else
                                    {
                                        $journaux[$key] = $this->getJournalObject(
                                            $releve,
                                            $tvaImputationControle,
                                            $tvaImputationControle->getPcc(),
                                            null,
                                            $cr ? 0 : abs($montant - $montantTva),
                                            $cr ? abs($montant - $montantTva) : 0,
                                            $releve->getId(),
                                            null,
                                            null,
                                            0,
                                            $tvaImputationControle->getImage()
                                        );
                                    }
                                }

                                if ($montantTva != 0)
                                {
                                    $key = $tvaImputationControle->getImage()->getId() . '-' .
                                        '0-'. $tvaImputationControle->getPccTva()->getId();

                                    if (array_key_exists($key,$journaux))
                                    {
                                        $solde = $montantTva + abs($journaux[$key]->cr - $journaux[$key]->db);
                                        $journaux[$key]->db = ($cr ? 0 : abs($solde));
                                        $journaux[$key]->cr = ($cr ? abs($solde) : 0);
                                    }
                                    else
                                    {
                                        $journaux[$key] = $this->getJournalObject(
                                            $releve,
                                            $tvaImputationControle,
                                            $tvaImputationControle->getPccTva(),
                                            null,
                                            $cr ? 0 : abs($montantTva),
                                            $cr ? abs($montantTva) : 0,
                                            $releve->getId(),
                                            null,
                                            null,
                                            0,
                                            $tvaImputationControle->getImage()
                                        );
                                    }
                                }
                            }
                        }
                    }

                    $releveSoeursImages = $soeurs->rel;
                    foreach ($releveSoeursImages as $releveSoeursImage)
                    {
                        foreach ($releveSoeursImage as $item)
                        {
                            /** @var Releve $rel */
                            $rel = $item;
                            if (in_array($rel->getId(),$relevesPasses)) continue;
                            $journaux[] = $this->getJournalObject(
                                $rel,
                                null,
                                $pccBanque,
                                null,
                                $rel->getCredit(),
                                $rel->getDebit(),
                                $releve->getId(),
                                $rel->getCleDossier(),
                                null,
                                1
                            );
                            $relevesPasses[] = $rel->getId();
                        }
                    }

                    $releveSoeursBanquesAutres = $soeurs->bsca;
                    if (count($releveSoeursBanquesAutres) > 0)
                    {
                        $pccAttente = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                            ->getPccAttenteBanque($banqueCompte->getDossier(),!$credit);

                        foreach ($releveSoeursBanquesAutres as $releveSoeursBanquesAutre)
                        {
                            foreach ($releveSoeursBanquesAutre as $item)
                            {
                                /** @var BanqueSousCategorieAutre $banqueSousCateogrieAutre */
                                $banqueSousCateogrieAutre = $item;
                                $isEngagement = intval($banqueSousCateogrieAutre->getEngagementTresorerie()) == 0;

                                if ($banqueSousCateogrieAutre->getImageFlague2())
                                {
                                    $tvaImputationControles = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                                        ->getChildImageFlagues($banqueSousCateogrieAutre->getImageFlague2());

                                    /** @var Separation[] $separations */
                                    $separations = [];
                                    /** @var ImputationControle[] $imputationControles */
                                    $imputationControles = [];

                                    foreach ($tvaImputationControles as $tvaImputationControle)
                                    {
                                        $negatif = false;
                                        $separation = null;
                                        $imputationControle = null;

                                        if (array_key_exists($tvaImputationControle->getImage()->getId(),$separations))
                                        {
                                            $separation = $separations[$tvaImputationControle->getImage()->getId()];
                                        }
                                        else
                                        {
                                            $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                                                ->createQueryBuilder('s')
                                                ->where('s.image = :image')
                                                ->setParameter('image',$tvaImputationControle->getImage())
                                                ->setMaxResults(1)
                                                ->getQuery()
                                                ->getOneOrNullResult();

                                            $separations[$tvaImputationControle->getImage()->getId()] = $separation;
                                        }

                                        if (array_key_exists($tvaImputationControle->getImage()->getId(),$imputationControles))
                                        {
                                            $imputationControle = $imputationControles[$tvaImputationControle->getImage()->getId()];
                                        }
                                        else
                                        {
                                            $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                                                ->getImputationControle($tvaImputationControle);

                                            $imputationControles[$tvaImputationControle->getImage()->getId()] = $imputationControle;
                                        }

                                        if ($separation)
                                            if (in_array($separation->getCategorie()->getId(),[10,12]))
                                            {
                                                if ($imputationControle && (!$imputationControle->getTypePiece() || $imputationControle->getTypePiece() && $imputationControle->getTypePiece()->getId() != 1))
                                                    $negatif = true;
                                            }
                                            elseif (in_array($separation->getCategorie()->getId(),[9,13]))
                                            {
                                                if ($imputationControle && $imputationControle->getTypePiece() && $imputationControle->getTypePiece()->getId() == 1)
                                                    $negatif = true;
                                            }

                                        $montant = abs($tvaImputationControle->getMontantTtc());
                                        if ($negatif) $montant *= -1;
                                        if ($isEngagement)
                                        {
                                            if ($tvaImputationControle->getPccBilan() || $tvaImputationControle->getTiers())
                                            {
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    $tvaImputationControle,
                                                    $tvaImputationControle->getPccBilan(),
                                                    $tvaImputationControle->getTiers(),
                                                    $montant < 0 ? abs($montant) : 0,
                                                    $montant < 0 ? 0 : abs($montant),
                                                    $releve->getId(),
                                                    null,
                                                    null,
                                                    0,
                                                    $banqueSousCateogrieAutre->getImage(),
                                                    $banqueSousCateogrieAutre
                                                );
                                            }
                                        }
                                        else
                                        {
                                            $coeffTva = 1;
                                            if ($tvaImputationControle->getPccTva() && $tvaImputationControle->getTvaTaux())
                                                $coeffTva += $tvaImputationControle->getTvaTaux()->getTaux() / 100;
                                            $mHt = $montant / $coeffTva;
                                            $mTva = $montant - $mHt;

                                            if ($mTva != 0)
                                            {
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    $tvaImputationControle,
                                                    $tvaImputationControle->getPccTva(),
                                                    null,
                                                    $montant < 0 ? abs($mTva) : 0,
                                                    $montant < 0 ? 0 : abs($mTva),
                                                    $releve->getId(),
                                                    null,
                                                    null,
                                                    0,
                                                    $banqueSousCateogrieAutre->getImage(),
                                                    $banqueSousCateogrieAutre
                                                );
                                            }

                                            $journaux[] = $this->getJournalObject(
                                                $releve,
                                                $tvaImputationControle,
                                                $tvaImputationControle->getPcc(),
                                                null,
                                                $montant < 0 ? abs($mHt) : 0,
                                                $montant < 0 ? 0 : abs($mHt),
                                                $releve->getId(),
                                                null,
                                                null,
                                                0,
                                                $banqueSousCateogrieAutre->getImage(),
                                                $banqueSousCateogrieAutre
                                            );
                                        }
                                    }
                                }
                                else
                                {
                                    if ($banqueSousCateogrieAutre->getCompteTiers() || $banqueSousCateogrieAutre->getCompteBilan())
                                    {
                                        if ($banqueSousCateogrieAutre->getCompteTiers())
                                        {
                                            $journaux[] = $this->getJournalObject(
                                                $releve,
                                                null,
                                                null,
                                                $banqueSousCateogrieAutre->getCompteTiers(),
                                                $credit ? 0 : abs($banqueSousCateogrieAutre->getMontant()),
                                                $credit ? abs($banqueSousCateogrieAutre->getMontant()) : 0,
                                                $releve->getId(),
                                                null,
                                                $banqueSousCateogrieAutre
                                            );
                                        }
                                        else
                                        {
                                            $journaux[] = $this->getJournalObject(
                                                $releve,
                                                null,
                                                $banqueSousCateogrieAutre->getCompteBilan(),
                                                null,
                                                $credit ? 0 : abs($banqueSousCateogrieAutre->getMontant()),
                                                $credit ? abs($banqueSousCateogrieAutre->getMontant()) : 0,
                                                $releve->getId(),
                                                null,
                                                $banqueSousCateogrieAutre
                                            );
                                        }
                                    }
                                    else
                                    {
                                        $mTtc = abs($banqueSousCateogrieAutre->getMontant());
                                        $coeffTva = 1;
                                        if ($banqueSousCateogrieAutre->getCompteTva() && $banqueSousCateogrieAutre->getTvaTaux())
                                            $coeffTva += $banqueSousCateogrieAutre->getTvaTaux()->getTaux() / 100;
                                        $mHt = $mTtc / $coeffTva;
                                        $mTva = $mTtc - $mHt;

                                        if ($mTva != 0)
                                        {
                                            if ($banqueSousCateogrieAutre->getCompteChg())
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    null,
                                                    $banqueSousCateogrieAutre->getCompteTva(),
                                                    null,
                                                    $credit ? 0 : $mTva,
                                                    $credit ? $mTva : 0,
                                                    $releve->getId(),
                                                    null,
                                                    $banqueSousCateogrieAutre
                                                );
                                            else
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    null,
                                                    $banqueSousCateogrieAutre->getCompteTva(),
                                                    null,
                                                    $credit ? 0 : $mTtc,
                                                    $credit ? $mTtc : 0,
                                                    $releve->getId(),
                                                    null,
                                                    $banqueSousCateogrieAutre
                                                );
                                        }

                                        if ($banqueSousCateogrieAutre->getCompteChg())
                                        {
                                            if ($banqueSousCateogrieAutre->getCompteChg())
                                                $journaux[] = $this->getJournalObject(
                                                    $releve,
                                                    null,
                                                    $banqueSousCateogrieAutre->getCompteChg(),
                                                    null,
                                                    $credit ? 0 : $mHt,
                                                    $credit ? $mHt : 0,
                                                    $releve->getId(),
                                                    null,
                                                    $banqueSousCateogrieAutre
                                                );
                                        }
                                        else
                                            $journaux[] = $this->getJournalObject(
                                                $releve,
                                                null,
                                                $pccAttente,
                                                null,
                                                $credit ? 0 : $mHt,
                                                $credit ? $mHt : 0,
                                                $releve->getId(),
                                                null,
                                                $banqueSousCateogrieAutre
                                            );
                                    }
                                }
                            }
                        }
                    }
                }
                elseif ($releve->getCleDossier())
                {
                    $cleDossier = $releve->getCleDossier();
                    $adds = $this->getReleveAdd($releve->getLibelle());
                    $montantTva = 0;
                    $montantAdds = [];
                    $montantAddTotal = 0;

                    foreach ($adds as $cle => $add)
                    {
                        if ($add->v > $montantReleve) continue;

                        if ($cle == 'TVA')
                        {
                            if ($cleDossier->getTva())
                            {
                                if ($add->v != 0) $montantTva = $add->v;
                                elseif($add->t != 0) $montantTva = ($add->t != 0) ? ($montantReleve * $add->t / 100) : $add->v;
                            }
                        }
                        else
                        {
                            $montantAdd = 0;
                            if ($add->v != 0) $montantAdd = $add->v;
                            elseif($add->t != 0) $montantAdd = ($add->t != 0) ? ($montantReleve * $add->t / 100) : $add->v;

                            if ($montantAdd != 0)
                            {
                                $montantAdds[$cle] = $montantAdd;
                                $montantAddTotal += $montantAdd;
                            }
                        }
                    }

                    if ($cleDossier->getTva())
                    {
                        if ($montantTva == 0)
                            $montantTva = $montantReleve * $cleDossier->getTauxTva() / 100;

                        if ($montantTva != 0)
                        {
                            if (intval(substr($cleDossier->getTva()->getCompte(),0,4)) == 4456)
                                $journaux[] = $this->getJournalObject(
                                    $releve,
                                    null,
                                    $cleDossier->getTva(),
                                    null,
                                    $credit ? 0 : $montantTva,
                                    $credit ? $montantTva : 0,
                                    $releve->getId()
                                );
                            //$montantAddTotal -= $montantTva * 2;
                        }
                        /*else
                        {
                            if (intval(substr($cleDossier->getTva()->getCompte(),0,4)) == 4456)
                                $journaux[] = $this->getJournalObject(
                                    $releve,
                                    null,
                                    $cleDossier->getTva(),
                                    null,
                                    $credit ? 0 : $montantTva,
                                    $credit ? $montantTva : 0,
                                    $releve->getId()
                                );
                        }*/
                    }

                    if ($montantAddTotal != 0)
                    {
                        foreach ($montantAdds as $montantAdd)
                        {
                            if ($cleDossier->getResultat())
                            {
                                if (intval(substr($cleDossier->getResultat()->getCompte(),0,3)) == 627 && ($cleDossier->getBilanTiers() && $cleDossier->getBilanPcc()))
                                {
                                    $journaux[] = $this->getJournalObject(
                                        $releve,
                                        null,
                                        $cleDossier->getResultat(),
                                        null,
                                        $credit ? $montantAdd : 0,
                                        $credit ? 0 : $montantAdd,
                                        $releve->getId()
                                    );
                                    $montantAddTotal -= $montantAdd * 2;
                                }
                                else
                                {
                                    $journaux[] = $this->getJournalObject(
                                        $releve,
                                        null,
                                        $cleDossier->getResultat(),
                                        null,
                                        $credit ? 0 : $montantAdd,
                                        $credit ? $montantAdd : 0,
                                        $releve->getId()
                                    );
                                }
                            }
                            elseif ($cleDossier->getBilanTiers() || $cleDossier->getBilanPcc())
                            {
                                $journaux[] = $this->getJournalObject(
                                    $releve,
                                    null,
                                    $cleDossier->getBilanPcc(),
                                    $cleDossier->getBilanTiers(),
                                    $credit ? 0 : $montantAdd,
                                    $credit ? $montantAdd : 0,
                                    $releve->getId()
                                );
                            }
                        }
                    }

                    if ($montantReleve - $montantTva - $montantAddTotal != 0)
                    {
                        if ($cleDossier->getBilanTiers() || $cleDossier->getBilanPcc())
                        {
                            $journaux[] = $this->getJournalObject(
                                $releve,
                                null,
                                $cleDossier->getBilanPcc(),
                                $cleDossier->getBilanTiers(),
                                $credit ? 0 : $montantReleve - $montantTva - $montantAddTotal,
                                $credit ? $montantReleve - $montantTva - $montantAddTotal : 0,
                                $releve->getId()
                            );
                        }
                        elseif ($cleDossier->getResultat())
                        {
                            $journaux[] = $this->getJournalObject(
                                $releve,
                                null,
                                $cleDossier->getResultat(),
                                null,
                                $credit ? 0 : $montantReleve - $montantTva - $montantAddTotal,
                                $credit ? $montantReleve - $montantTva - $montantAddTotal : 0,
                                $releve->getId()
                            );
                        }
                    }
                }
                else
                {
                    $pccAttente = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                        ->getPccAttenteBanque($banqueCompte->getDossier(),!$credit);

                    $journaux[] = $this->getJournalObject(
                        $releve,
                        null,
                        $pccAttente,
                        null,
                        $credit ? 0 : $montantReleve,
                        $credit ? $montantReleve : 0,
                        $releve->getId()
                    );
                }
            }
        }

        return (object)
        [
            'datas' => array_values($journaux),
            'footers' => (object)
            [
                'cr' => $total,
                'db' => $total
            ]
        ];
    }

    /**
     * @param Dossier $dossier
     * @param string $nomImage
     * @param int $montant
     * @param bool $avecLettre
     * @return Releve[]
     */
    public function searchByPieceMontant(Dossier $dossier, $nomImage = '' , $montant = 0, $avecLettre = false)
    {
        $releves = $this->createQueryBuilder('r')
            ->leftJoin('r.image','i')
            ->leftJoin('r.banqueCompte','bc')
            ->where('bc.dossier = :dossier')
            ->setParameter('dossier',$dossier);

        if (!$avecLettre)
            $releves = $releves->andWhere('r.imageFlague IS NULL');

        if ($montant != 0)
            $releves = $releves
                ->andWhere('ROUND(r.credit - r.debit,2) = ROUND(:montant,2)')
                ->setParameter('montant',$montant);

        if ($nomImage != '')
            $releves = $releves
                ->andWhere('i.nom LIKE :likeNOM')
                ->setParameter('likeNOM','%'.$nomImage.'%');

        return $releves->getQuery()->getResult();
    }

    /**
     * @param Releve $releveMere
     * @param Releve|null $releve
     * @param TvaImputationControle|null $tvaImputationControle
     * @param BanqueSousCategorieAutre|null $banqueSousCategorieAutre
     * @return object
     */
    public function getEcriture(Releve $releveMere, Releve $releve = null, TvaImputationControle $tvaImputationControle = null, BanqueSousCategorieAutre $banqueSousCategorieAutre = null)
    {
        $isCredit = ($releveMere->getCredit() - $releveMere->getDebit() > 0);
        //0:releve , 1:tvaImputationControle , 2:bsca
        $type = 0;
        /** @var \DateTime $date */
        $date = null;
        $libelle = $releveMere->getLibelle();
        $debit = 0;
        $credit = 0;
        $id = 0;
        /** @var Image $image */
        $image = $releveMere->getImage();
        $lettre = 0;

        if ($releve)
        {
            $date = $releve->getDateReleve();
            $id = $releve->getId();
            $libelle = $releve->getLibelle();
            $image = $releve->getImage();
            $debit = $releve->getCredit();
            $credit = $releve->getDebit();
            $type = 0;
            if ($releve->getImageFlague()) $lettre = 1;
        }
        elseif ($banqueSousCategorieAutre)
        {
            /** @var SaisieControle $saisiControl */
            $saisiControl = $this->getEntityManager()->getRepository('AppBundle:SaisieControle')
                ->createQueryBuilder('sc')
                ->where('sc.image = :image')
                ->setParameter('image',$banqueSousCategorieAutre->getImage())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($saisiControl)
            {
                if ($saisiControl->getDateReglement()) $date = $saisiControl->getDateReglement();
                elseif ($saisiControl->getDateFacture()) $date = $saisiControl->getDateFacture();
                elseif ($saisiControl->getDateEcheance()) $date = $saisiControl->getDateEcheance();
            }

            $id = $banqueSousCategorieAutre->getId();
            $libelle = trim($banqueSousCategorieAutre->getLibelle());
            if ($libelle == '')
                $libelle = trim($banqueSousCategorieAutre->getNomTiers());

            $image = $banqueSousCategorieAutre->getImage();
            $debit = ($banqueSousCategorieAutre->getMontant() > 0) ? 0 : $banqueSousCategorieAutre->getMontant();
            $credit = ($banqueSousCategorieAutre->getMontant() < 0) ? $banqueSousCategorieAutre->getMontant() : 0;
            $type = 2;

            if ($banqueSousCategorieAutre->getImageFlague()) $lettre = 1;
        }
        elseif ($tvaImputationControle)
        {
            $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                ->getByImage($tvaImputationControle->getImage());

            if ($imputationControle)
            {
                if ($imputationControle->getDateReglement()) $date = $imputationControle->getDateReglement();
                elseif ($imputationControle->getDateFacture()) $date = $imputationControle->getDateFacture();
                elseif ($imputationControle->getDateEcheance()) $date = $imputationControle->getDateEcheance();
                $libelle = $imputationControle->getRs();
            }

            $id = $tvaImputationControle->getId();
            $image = $tvaImputationControle->getImage();

            $cr = $credit;
            $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                ->getSeparationByImage($tvaImputationControle->getImage());

            if (in_array($separation->getCategorie()->getId(),[10,12,9,13]))
            {
                $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                    ->getImputationControle($tvaImputationControle);

                if ($imputationControle && $imputationControle->getTypePiece() && intval($imputationControle->getTypePiece()->getId()) == 1)
                    $cr = !$cr;
            }

            $debit = $cr ? 0 : abs($tvaImputationControle->getMontantTtc());
            $credit = $cr ? abs($tvaImputationControle->getMontantTtc()) : 0;

            /*$debit = $releve->getCredit();
            $credit = $releve->getDebit();*/
            $type = 1;

            if ($tvaImputationControle->getImageFlague()) $lettre = 1;
        }

        return (object)
        [
            'id' => $id,
            'd' => $date,
            'l' => $libelle,
            'i' => (object)
            [
                'id' => $image->getId(),
                'n' => $image->getNom()
            ],
            'db' => $debit,
            'cr' => $credit,
            't' => $type,
            'let' => $lettre
        ];
    }


    public function hasReleveOnPreviousExercice(BanqueCompte $banqueCompte, $exercice)
    {

        $exercice = $exercice - 1;

        $qb = $this->createQueryBuilder('r')
            ->innerJoin('r.image', 'i')
            ->where('r.banqueCompte = :banquecompte')
            ->andWhere('i.exercice = :exercice')
            ->setParameter('exercice', $exercice)
            ->setParameter('banquecompte', $banqueCompte);

        $res = $qb->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return (count($res) > 0) ? true : false;

    }

    public function getDecaissementPm($param)
    {
        $param['client']  = Boost::deboost($param['client'],$this);
        $releves = $this->createQueryBuilder('r')
                        ->innerJoin('AppBundle:Separation', 'sep', 'WITH', 'sep.image = r.image')
                        ->leftJoin('r.banqueCompte','bc')
                        ->leftJoin('r.image','i')
                        ->leftJoin('bc.dossier','d')
                        ->leftJoin('d.site','s')
                        ->where('i.exercice = :exercice')
                        ->setParameter('exercice',$param['exercice'])
                        ->andWhere('r.imageFlague IS NULL')
                        ->andWhere('sep.souscategorie = 10')
                        ->andWhere('i.supprimer = 0')
                        ->andWhere('r.operateur IS NULL')
                        ->andWhere('ROUND(r.credit - r.debit,2) < 0')
                        ->andWhere('r.libelle NOT LIKE :chq_1')
                        ->andWhere('r.libelle NOT LIKE :chq_2')
                        ->andWhere('s.client = :client')
                        ->setParameter('client',$param['client']);
        if ($param['dossier'] != 0) {
            $releves = $releves
                        ->andWhere('bc.dossier = :dossier')
                        ->setParameter('dossier',$param['dossier']);
        }
        $releves = $releves
                    ->setParameter('chq_1','%CHQ%')
                    ->setParameter('chq_2','%CHEQUE%');
        $releves = $releves
                ->getQuery()
                ->getResult();
        $i = 0;

        foreach ($releves as $rel)
        {
            if ($rel->getCleDossier())
            {
                if ($rel->getCleDossier()->getPasPiece()) continue;
                else
                {
                    $releveIsDebit = ($rel->getCredit() - $rel->getDebit() > 0);
                    $cleExceptionPm = $this->getEntityManager()->getRepository('AppBundle:CleExceptionPm')
                        ->cleExceptionForCleDossier($rel->getCleDossier());
                    $continue = false;

                    if ($cleExceptionPm)
                    {
                        $formule = trim($cleExceptionPm->getFormule());
                        $sens = intval($cleExceptionPm->getSens());
                        $formule2 = trim($cleExceptionPm->getFormule2());
                        $sens2 = intval($cleExceptionPm->getSens2());
                        $listVal['x'] = abs($rel->getCredit() - $rel->getDebit());
                        $langage = new ExpressionLanguage();

                        //Formule
                        if ($formule != '' && ($sens == 0 || $sens == 1) && $releveIsDebit)
                        {
                            $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule))),$listVal);
                        }
                        if ($formule != '' && ($sens == 0 || $sens == 2) && !$releveIsDebit && !$continue)
                        {
                            $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule))),$listVal);
                        }
                        //Formule2
                        if ($formule2 != '' && ($sens2 == 0 || $sens2 == 1) && $releveIsDebit && !$continue)
                        {
                            $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule2))),$listVal);
                        }
                        if ($formule2 != '' && ($sens2 == 0 || $sens2 == 2) && !$releveIsDebit && !$continue)
                        {
                            $continue = $langage->evaluate(preg_replace('#[\xC2\xA0]#', '',trim(str_replace(' ','','x'.$formule2))),$listVal);
                        }
                    }

                    if ($continue) continue;
                }
            }
            if (intval($rel->getEcritureChange()) == 1 && intval($rel->getMaj()) == 3) continue;
            $i++;
        }
        return $i;
    }

    public function tresorerie(Dossier $dossier, $exercice, $base)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $dates = $this->getEntityManager()->getRepository('AppBundle:TbimagePeriode')
            ->getAnneeMoisExercices($dossier,$exercice);
        /** @var \DateTime $demarrage */
        $demarrage = $dates->d;
        /** @var \DateTime $fin */
        $fin = $dates->c;

        //0: Exercice ; 1: Mois ; 2: Jour
        if ($base > 0)
        {
            $format = ($base == 1) ? '%Y-%m' : '%Y-%m-%d';
            $req = '
                SELECT ROUND(SUM(debit),2) AS s_debit, ROUND(SUM(credit),2) AS s_credit, l.dossier_id, i.exercice, DATE_FORMAT(r.date_releve, "'.$format.'") AS date_f
                FROM releve r
                JOIN image i on (i.id = r.image_id)
                JOIN lot l on (l.id = i.lot_id)
                JOIN separation sep on (sep.image_id = i.id)  
                JOIN souscategorie ssc on (sep.souscategorie_id = ssc.id) 
                WHERE l.dossier_id = :DOSSIER_ID AND i.exercice = :EXERCICE 
                AND r.operateur_id IS NULL AND i.supprimer = 0 
                AND ssc.id = 10 AND r.eclate <> 1 
                GROUP BY l.dossier_id, i.exercice, date_f 
                ORDER BY date_f ASC 
            ';

            $params = [
                'DOSSIER_ID' => $dossier->getId(),
                'EXERCICE' => $exercice
            ];
        }
        else
        {
            $req = '
            
            ';
            $params = [];
        }

        $prep = $pdo->prepare($req);
        $prep->execute($params);

        $res = $prep->fetchAll();

        if ($base == 0)
        {
            $keyDebut = $demarrage->format('Y');
            $keyFin = $fin->format('Y');
        }
        elseif ($base == 1)
        {
            $keyDebut = $demarrage->format('Y-m');
            $keyFin = $fin->format('Y-m');
        }
        else
        {
            $keyDebut = $demarrage->format('Y-m-d');
            $keyFin = $fin->format('Y-m-d');
        }

        $debits = [];
        $credits = [];
        foreach ($res as $re)
        {
            $key = $re->date_f;
            if ($key < $keyDebut) $key = $keyDebut;
            if ($key > $keyFin) $key = $keyFin;

            if (!array_key_exists($key,$debits))
            {
                $debits[$key] = $re->s_debit;
                $credits[$key] = $re->s_credit;
            }
            else
            {
                $debits[$key] += $re->s_debit;
                $credits[$key] += $re->s_credit;
            }
        }

        return (object)
        [
            'debits' => $debits,
            'credits' => $credits,
            'mois' => $dates->ms,
            'dates' => $dates
        ];
    }

    /**
     * @param Dossier $dossier
     * @return float
     */
    public function getReleveSens(Dossier $dossier, $exercice, $sens = 0)
    {
        $releves = $this->createQueryBuilder('r')
            ->leftJoin('r.image','i')
            ->leftJoin('i.lot','l')
            ->leftJoin('AppBundle:Separation', 'sep', 'WITH', 'sep.image = r.image')
            ->leftJoin('sep.Souscategorie','sc')
            ->where('l.dossier = :dossier')
            ->andWhere('sep.souscategorie IS NOT NULL')
            ->andWhere('sc.id = 10')
            ->andWhere('r.eclate <> 1')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.supprimer = 0')
            ->andWhere('r.operateur IS NULL')
            ->setParameters([
                'exercice' => $exercice,
                'dossier' => $dossier
            ]);

        if ($sens > 0)
            $releves = $releves->andWhere('r.credit > 0');
        elseif ($sens < 0)
            $releves = $releves->andWhere('r.debit > 0');

        return $releves
            ->getQuery()
            ->getResult();
    }

    public function getDetailsTachesBanque($dossierArray, $exercice){
        $param = [];
        $param['client'][] = 0;
        $param['dossier'] = $dossierArray;
        $param['exercice'] = $exercice;

        $listes = $this->getEntityManager()->getRepository('AppBundle:Image')->getListeImputeForPm($param);

        if(count($listes > 0)){
            $tab_imputees = array();
            $tab_key_mois = array();
            $exercice = $param['exercice'];
            $dossier = $param['dossier'];
            $client = $param['client'];
            $betweens = array();
            $tab_dossier_imp = array();
            $tab_mois_cloture = [];
            $showCompte = false;
            $datas = [];
            $now = new \DateTime();
            $exercices = [];
            for ($i = -2; $i < 3; $i++) $exercices[] = $exercice + $i;
            foreach ($listes as $key => $value) {
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
                                $tab_imputees[$key]['m'] = 'M-1';
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
                                $tab_imputees[$key]['m'] = (count($resReleves) > 0) ? 'Inc.' : 'Auc.';
                                break;
                            default:
                                $nb_m_mois_exist = true;
                                $tab_imputees[$key]['m'] = 'Inc.';
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
                                        $tab_imputees[$key]['m'] = 'M-1';
                                    }else if($min == 9){
                                        $tab_imputees[$key]['m'] = 'M-2';
                                    }else{
                                        $tab_imputees[$key]['m'] = 'Inc.';
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
                                            $tab_imputees[$key]['m'] = 'M-1';
                                        } else if ($diff > 0 && $diff < 11) {
                                            $tab_imputees[$key]['m'] = 'M-' . $diff;
                                        } else {
                                            $tab_imputees[$key]['m'] = 'Inc.';
                                        }
                                    }else{
                                        $tab_imputees[$key]['m'] = 'Inc.';
                                    }
                                }
                            }
                            else{
                                $tab_imputees[$key]['m'] = 'Inc.';
                            }
                        }
                    }
                    else {
                        $tab_imputees[$key]['m'] = 'M-1';
                        if ($value->cloture < 10) {
                            $fin_mois = ($exercice) . '-0' . ($value->cloture) . '-01';
                        } else {
                            $fin_mois = ($exercice) . '-' . ($value->cloture) . '-01';
                        }
                    }

                    $remise = 0;
                    $frbanc = 0;
                    $lcr = 0;
                    $vrmt = 0;
                    $cartCredRel = 0;
                    $cartDebRel = 0;
                    $imageOb = $this->getEntityManager()
                                ->getRepository('AppBundle:Image')
                                ->getListImageBanqueGestionTache($value->dossier_id, $exercice, 0, 8, -1, 1, -1);
                     foreach ($imageOb as $im) {
                        if($im->ctrl_saisie > 2 && $im->valider != 100){
                            $frbanc += 1;
                        }
                    }

                    $taches = $this->getEntityManager()->getRepository('AppBundle:Tache')
                                              ->getTachesPourGestionTaches($value->dossier_id, $now, true, true, true,
                                                true, true, null);

                    $email = $this->getEntityManager()
                                   ->getRepository('AppBundle:Emails')
                                   ->findBy(array(
                                        'dossier' => $value->dossier_id,
                                        'typeEmail' => 'BANQUE_MANQUANTE'
                                    ));
                    $dernierEnvoi = null;
                    if(count($email) > 0){
                        $dernierEnvoi = $email[0]->getDateEnvoi();
                        $dernierEnvoi = ($dernierEnvoi) ? $dernierEnvoi->format('d/m/Y') : null;
                    }

                    $notificationBM = $this->getEntityManager()
                                           ->getRepository('AppBundle:NotificationPm')
                                           ->findBy(array('dossier' =>$value->dossier_id));
                    $notificationBMId = (count($notificationBM) > 0) ? $notificationBM[0]->getId() : null;

                    $tab_imputees[$key]['e'] = '';
                    $tab_imputees[$key]['pe'] = '';
                    $tab_imputees[$key]['tache'] = '';
                    $tab_imputees[$key]['id'] = $value->dossier_id.'-'.$value->banquecompte_id.'-'.$notificationBMId;
                    $tab_imputees[$key]['d'] = $value->dossier;
                    $tab_imputees[$key]['ml'] = '<span class="pointer show_log_ml_do">'.$dernierEnvoi.'</span>';
                    /*$tab_imputees[$key]['mail'] = '<span class="pointer send_mail_pm"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>';*/
                    $tab_imputees[$key]['mail'] = '<span class="pointer"><i class="fa fa-cog class_action_pm_notif" aria-hidden="true"></i></span>';

                    $dossier = $this->getEntityManager()->getRepository('AppBundle:Dossier')->find($value->dossier_id);
                    $bc = $this->getEntityManager()->getRepository('AppBundle:BanqueCompte')->find(intval($value->banque_compte_id));
                   /* $banqueObManquantes = $this->getEntityManager()->getRepository('AppBundle:BanqueObManquante')->getForDossier($dossier,$bc,$exercice);*/

                    
                    $tab_imputees[$key]['b'] = $value->banque.'+'.substr($value->numcompte, -6);
                    if($bc->getSourceImage() !== null){
                        if($bc->getSourceImage()->getSource() === 'SOBANK'){
                            $tab_imputees[$key]['b'] = $value->banque.'+BI+'.substr($value->numcompte, -6);
                        }
                    }
                    
                    $tabMois = [];
                    $dataObMq = [];
                    $moisOb = '';
                    /*foreach ($banqueObManquantes as $q=> $v) {
                        if($v->getSouscategorie()){
                            $moisOb = '';
                            $natureOb = '';
                            $sousCatId = $v->getSouscategorie()->getId();
                            if($sousCatId == 5 || $sousCatId == 7 || $sousCatId == 11){
                                if($sousCatId == 5) $natureOb = 2;
                                if($sousCatId == 7) $natureOb = 1;
                                $releveManquants = $v->getMois();
                                $sc = '';
                                $stateOb = false;
                                foreach ($tab_mois_cloture as $i => $m)
                                {
                                    if(in_array($m,$releveManquants)){
                                        if($m == $now->format('Y-m') && intval($now->format('d')) <= 8){
                                            continue;
                                        }else if($moisOb == ''){
                                            $moisOb = $m;
                                        }else{
                                            $stateOb = true;
                                            $moisOb = $moisOb.'; '.$m;
                                        }
                                    }
                                }
                                $sc = $v->getSouscategorie()->getLibelleNew();
                                if($moisOb != ''){
                                    array_push($dataObMq, [
                                        'libelle' => $sc,
                                        'nb' => $moisOb,
                                        'nature' => $natureOb
                                    ]); 
                                }
                            }
                        }
                    }*/              

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
                    $rMs = array_map('trim',$rMs);
                    $releveManquants = array_intersect($tab_mois_cloture, $rMs);
                    $moisRb = '';
                    foreach ($tab_mois_cloture as $j => $m)
                    {
                        if(in_array($m,$releveManquants)){
                            if($m == $now->format('Y-m') && intval($now->format('d')) <= 8){
                                continue;
                            }else if($moisRb == ''){
                                $m = explode('-', $m);
                                $moisRb = $m[1].'-'.$m[0];
                            }else{
                                $m = explode('-', $m);
                                $moisRb = $moisRb.'; '.$m[1].'-'.$m[0];
                            }
                        }
                    }

                    if($moisRb != ''){
                        array_push($tabMois, [
                            'sc' => 'Relevé Bancaire',
                            'm' => ($moisRb == '') ? 'Aucun' : $moisRb
                        ]); 
                   }
                    $tab_imputees[$key]['mq'] = json_encode($tabMois, true);

                    $tab_imputees[$key]['d_m_rb'] = $value->banque.'*'.$value->numcompte.'*'.$moisRb;

                    $notifDossier = $this->getEntityManager()->getRepository('AppBundle:NotificationDossier')
                                         ->findBy(array('dossier' => $dossier->getId()));
                    $relPm = 'Manuel';
                    foreach ($notifDossier as $notifD) {
                        $code = $notifD->getNotification()->getCode();
                        if($code == 'BANQUE'){
                            $relPm = 'Automatique';
                        }
                    }

                    $tab_imputees[$key]['tm'] = $relPm;

                    $ur = 0;
                    /*if($tab_imputees[$key]['m'] != 'M-1' && $moisOb != ''){
                        if($stateOb){
                            $moisOb = explode(', ', $moisOb);
                            $moisOb = end($moisOb);
                        }
                        $moisOb = explode('-', $moisOb);
                        $dateUr = '08-'.$moisOb[1].'-'.$moisOb[0];
                        $nowD = $now->format('d-m-Y');
                        $dateUr = strtotime($dateUr);
                        $date4 = strtotime($nowD);
                        $nbJoursTimestamp = $date4 - $dateUr;
                        $ur = $nbJoursTimestamp/86400;
                    }*/
                    if(array_key_exists($value->dossier_id, $taches['taches'])){
                        foreach ($taches['taches'][$value->dossier_id] as $k => $t) {
                          $abrevTache = explode('*', $t['titre2']);
                          if(!$t['expirer']) {
                            $dateTache = $t['datetime']->format('d-m');
                            $datetimetache = $t['datetime'];
                            $abrevTache = $abrevTache[0];
                            $statusTvaTache = $t['status'];
                            break;
                          }else{
                            $expirerTache = $t['expirer'];
                            $dateTache = $t['datetime']->format('d-m');
                            $abrevTache = $abrevTache[0];
                            $datetimetache = $t['datetime'];
                            $statusTvaTache = $t['status'];
                          }
                        }
                        $tab_imputees[$key]['tache'] = json_encode($taches['taches'][$value->dossier_id], true);
                        $dateTacheTva = new \DateTime("now");
                        $dateTacheTvaYear = $dateTacheTva->format('Y');
                        $dateTacheTvaMonth = $dateTacheTva->format('m');
                        $tachesDate = explode('-', $dateTache);
                        if(count($tachesDate) > 1){
                            if((intval($dateTacheTvaMonth) > intval($tachesDate[1])) && ($statusTvaTache == 1 || $statusTvaTache == 2)){
                                $dateTacheTvaYear++;
                            }
                        }
                        if($dateTache != '' && count($tachesDate) > 1){
                            $tab_imputees[$key]['e'] = $abrevTache;
                            $dateString = $tachesDate[0].'-'.$tachesDate[1].'-'.$dateTacheTvaYear;
                            $dateString = \DateTime::createFromFormat('d-m-Y',$dateString);
                            $tab_imputees[$key]['pe'] = $dateString->format('Y-m-d');
                        }

                        if($tab_imputees[$key]['m'] == 'M-1' && $moisOb == ''){
                            $tab_imputees[$key]['ur'] = $ur;
                        }else{
                            $dateEche = $datetimetache->format('d-m-Y');
                            $nowD = $now->format('d-m-Y');
                            $nowD = strtotime($nowD);
                            $dateEche = strtotime($dateEche);
                            $nbJoursTimestamp = $dateEche - $nowD;
                            $ur = $nbJoursTimestamp/86400;
                            $tab_imputees[$key]['ur'] = $ur;
                        }
                        $moisOb = '';
                    }

                    $isOb = false;
                    $libelleObExist = [];
                    $dateObExist = [];
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
                                $banqueObManquantes = $this->getReleveObManquant($value->client_id, $dossier->getId(), $value->banque_compte_id, $exercice, $nature);

                                if(count($banqueObManquantes) > 0){
                                    foreach ($banqueObManquantes as $bqObMq) {
                                        $dateParMoisOb = new \DateTime($bqObMq->dateReleve);
                                        $dateParMoisOb = $dateParMoisOb->format('m-Y');
                                        if(!in_array($dateParMoisOb, $dateObExist)){
                                            if($moisOb == ''){
                                                $moisOb = $dateParMoisOb;
                                            }else{
                                                $moisOb = $moisOb.'; '.$dateParMoisOb;
                                            }
                                        }
                                        $dateObExist[] = $dateParMoisOb;
                                    }
                                    $isOb = true;
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
                            }/*else{
                                $banqueObManquantes = $this->getEntityManager()->getRepository('AppBundle:BanqueObManquante')->getForDossier($dossier,$bc,$exercice);
                                if(count($banqueObManquantes) > 0)  $isOb = true;
                            }*/
                        }
                    }
                    $tab_imputees[$key]['ob'] = ($isOb) ? 'PB' : 'OK';
                    $tab_imputees[$key]['d_ob_m'] = json_encode($dataObMq, true);      

                    switch ($tab_imputees[$key]['m']) {
                        case 'M-1':
                            $tab_imputees[$key]['m'] = 1;
                            break;
                        case 'M-2':
                            $tab_imputees[$key]['m'] = 2;
                            break;
                        case 'M-3':
                            $tab_imputees[$key]['m'] = 3;
                            break;
                        case 'M-4':
                            $tab_imputees[$key]['m'] = 4;
                            break;
                        case 'M-5':
                            $tab_imputees[$key]['m'] = 5;
                            break;
                        case 'M-6':
                            $tab_imputees[$key]['m'] = 6;
                            break;
                        case 'M-7':
                            $tab_imputees[$key]['m'] = 7;
                            break;
                        case 'M-8':
                            $tab_imputees[$key]['m'] = 8;
                            break;
                        case 'M-9':
                            $tab_imputees[$key]['m'] = 9;
                            break;
                        
                        default:
                            $tab_imputees[$key]['m'] = 10;
                            break;
                    }
                    $datas[] = $tab_imputees[$key];
                }
            }
        }
        return $datas;
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
	
	/**
     * @param BanqueCompte $banqueCompte
     * @param Souscategorie $souscategorie
     * @param $dateFormatter
     * @param bool $oneResult
     * @return Releve[]
     */
    public function getPmInSousCategorie(BanqueCompte $banqueCompte, Souscategorie $souscategorie, $dateFormatter, $oneResult = false, $avecDepasse = 0)
    {
        $nature = ($souscategorie->getId() == 7) ? 1 : 2;

        $releves = $this->createQueryBuilder('r')
            ->where('r.banqueCompte = :banqueCompte')
            ->andWhere('r.imageFlague IS NULL')
            ->andWhere('r.nature = :nature')
            ->andWhere('r.imageTemp IS NULL');

        if ($avecDepasse < 0)
            $releves = $releves->andWhere("DATE_FORMAT(r.dateReleve,'%Y-%m') <= :date");
        elseif ($avecDepasse > 0)
            $releves = $releves->andWhere("DATE_FORMAT(r.dateReleve,'%Y-%m') >= :date");
        else
            $releves = $releves->andWhere("DATE_FORMAT(r.dateReleve,'%Y-%m') = :date");

        $releves = $releves
            ->setParameters([
                'banqueCompte' => $banqueCompte,
                'nature' => $nature,
                'date' => $dateFormatter
            ]);

        if ($oneResult)
            $releves = $releves->setMaxResults(1);

        return $releves->getQuery()->getResult();
    }

    /**
     * @param BanqueCompte $banqueCompte
     * @param $exercice
     * @param bool $oneResult
     * @return Releve[]
     */
    public function getPmInSousCategorieExercice(BanqueCompte $banqueCompte, $exercice, $oneResult = false)
    {
        $releves = $this->createQueryBuilder('r')
            ->leftJoin('r.image','i')
            ->where('r.banqueCompte = :banqueCompte')
            ->andWhere('r.imageFlague IS NULL')
            ->andWhere('r.nature <> 0')
            ->andWhere('i.exercice = :exercice')
            ->setParameters([
                'banqueCompte' => $banqueCompte,
                'exercice' => $exercice
            ]);

        if ($oneResult)
            $releves = $releves->setMaxResults(1);

        return $releves->getQuery()->getResult();
    }

	public function getReleveObManquant($clientId, $dossierId, $banqueCompteId, $exercice, $nature)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $req = "select r.id, r.credit, r.debit, i.nom as imageNom, i.id as imageId, r.libelle, r.date_releve as dateReleve, r.nature from releve r
                inner join image i on i.id = r.image_id
                inner join lot l on (l.id = i.lot_id)
                inner join dossier d on (l.dossier_id = d.id)
                inner join site s on (s.id = d.site_id)
                inner join client c on (c.id = s.client_id)
                inner join banque_compte bc on (bc.dossier_id = d.id and bc.id = r.banque_compte_id)
                inner join banque b on (b.id = bc.banque_id)
                inner join separation sep on (sep.image_id = i.id)
                where r.operateur_id IS NULL and i.exercice = :EXERCICE 
                and r.nature = :NATURE and c.id = :CLIENT_ID 
                and d.id = :DOSSIER_ID and bc.id = :BANQUE_COMPTE_ID";
        $params = [
                'EXERCICE' => $exercice,
                'NATURE' => $nature,
                'CLIENT_ID' => $clientId,
                'DOSSIER_ID' => $dossierId,
                'BANQUE_COMPTE_ID' => $banqueCompteId,
            ];
        $prep = $pdo->prepare($req);
        $prep->execute($params);
        return $prep->fetchAll();
    }

    public function getAutresPmNotif($dossierArray, $exercice, $user, $dateType, $interval)
    {
        $param = [];
        $param['client'][] = 0;
        $param['dossier'] = $dossierArray;
        $param['exercice'] = $exercice;

        $listes = $this->getEntityManager()->getRepository('AppBundle:Image')->getListeImputeForPm($param);

        if(count($listes > 0)){
            $data = array();
            $tab_key_mois = array();
            $exercice = $param['exercice'];
            $dossier = $param['dossier'];
            $client = $param['client'];
            $betweens = array();
            $tab_dossier_imp = array();
            $tab_mois_cloture = [];
            $showCompte = false;
            $datas = [];
            $now = new \DateTime();
            $exercices = [];
            for ($i = -2; $i < 3; $i++) $exercices[] = $exercice + $i;
            foreach ($listes as $key => $value) {
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
                                $data[$key]['m'] = 'M-1';
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
                                $data[$key]['m'] = (count($resReleves) > 0) ? 'Inc.' : 'Auc.';
                                break;
                            default:
                                $nb_m_mois_exist = true;
                                $data[$key]['m'] = 'Inc.';
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
                                if($lastIndex+1 !== $k){
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
                                        $data[$key]['m'] = 'M-1';
                                    }else if($min == 9){
                                        $data[$key]['m'] = 'M-2';
                                    }else{
                                        $data[$key]['m'] = 'Inc.';
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
                                            $data[$key]['m'] = 'M-1';
                                        } else if ($diff > 0 && $diff < 11) {
                                            $data[$key]['m'] = 'M-' . $diff;
                                        } else {
                                            $data[$key]['m'] = 'Inc.';
                                        }
                                    }else{
                                        $data[$key]['m'] = 'Inc.';
                                    }
                                }
                            }
                            else{
                                $data[$key]['m'] = 'Inc.';
                            }
                        }
                    }
                    else {
                        $data[$key]['m'] = 'M-1';
                        if ($value->cloture < 10) {
                            $fin_mois = ($exercice) . '-0' . ($value->cloture) . '-01';
                        } else {
                            $fin_mois = ($exercice) . '-' . ($value->cloture) . '-01';
                        }
                    }

                    $dossier = $this->getEntityManager()->getRepository('AppBundle:Dossier')->find($value->dossier_id);
                    $bc = $this->getEntityManager()->getRepository('AppBundle:BanqueCompte')->find(intval($value->banque_compte_id));
                    $b = $this->getEntityManager()->getRepository('AppBundle:Banque')->find(intval($value->banque_id));

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

                    if($moisRb != ''){
                        array_push($tabMois, [
                            'sc' => 'Relevé Bancaire',
                            'm' => ($moisRb == '') ? 'Aucun' : $moisRb
                        ]); 
                   }
                    $data[$key]['mq'] = json_encode($tabMois, true);

                    $data[$key]['d_m_rb'] = $value->banque.'*'.$value->numcompte.'*'.$moisRb;
                    $isOb = false;
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
                                $banqueObManquantes = $this->getReleveObManquant($value->client_id, $dossier->getId(), $value->banque_compte_id, $exercice, $nature);

                                if(count($banqueObManquantes) > 0){
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
                                    $isOb = true;
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

                    $taches = $this->getEntityManager()->getRepository('AppBundle:Tache')
                                   ->getTachesPourGestionTaches($value->dossier_id, $now, true, true, true,
                                                true, true, null);

                    if(array_key_exists($value->dossier_id, $taches['taches'])){
                        foreach ($taches['taches'][$value->dossier_id] as $k => $t) {
                          $abrevTache = explode('*', $t['titre2']);
                          if(!$t['expirer']) {
                            $dateTache = $t['datetime']->format('d-m');
                            $datetimetache = $t['datetime'];
                            $abrevTache = $abrevTache[0];
                            $statusTvaTache = $t['status'];
                            break;
                          }else{
                            $expirerTache = $t['expirer'];
                            $dateTache = $t['datetime']->format('d-m');
                            $abrevTache = $abrevTache[0];
                            $datetimetache = $t['datetime'];
                            $statusTvaTache = $t['status'];
                          }
                        }
                        $data[$key]['tache'] = json_encode($taches['taches'][$value->dossier_id], true);
                        $dateTacheTva = new \DateTime("now");
                        $dateTacheTvaYear = $dateTacheTva->format('Y');
                        $dateTacheTvaMonth = $dateTacheTva->format('m');
                        $tachesDate = explode('-', $dateTache);
                        if(count($tachesDate) > 1){
                            if((intval($dateTacheTvaMonth) > intval($tachesDate[1])) && ($statusTvaTache == 1 || $statusTvaTache == 2)){
                                $dateTacheTvaYear++;
                            }
                        }
                        if($dateTache != '' && count($tachesDate) > 1){
                            $data[$key]['e'] = $abrevTache;
                            $dateString = $tachesDate[0].'-'.$tachesDate[1].'-'.$dateTacheTvaYear;
                            $dateString = \DateTime::createFromFormat('d-m-Y',$dateString);
                            $data[$key]['pe'] = $dateString->format('Y-m-d');
                        }

                        $ur = 0;
                        if($data[$key]['m'] == 'M-1'  && $moisOb == ''){
                            $data[$key]['ur'] = $ur;
                        }else{
                            $dateEche = $datetimetache->format('d-m-Y');
                            $nowD = $now->format('d-m-Y');
                            $nowD = strtotime($nowD);
                            $dateEche = strtotime($dateEche);
                            $nbJoursTimestamp = $dateEche - $nowD;
                            $ur = $nbJoursTimestamp/86400;
                            $data[$key]['ur'] = $ur;
                        }
                        $moisOb = '';
                    }

                    $notifDossier = $this->getEntityManager()->getRepository('AppBundle:NotificationDossier')
                                         ->findBy(array('dossier' => $dossier->getId()));
                    $relPm = 'Manuel';
                    foreach ($notifDossier as $notifD) {
                        $code = $notifD->getNotification()->getCode();
                        if($code == 'ENVOIE PM'){
                            $relPm = 'Automatique';
                        }
                    }
                    $data[$key]['tm'] = $relPm;

                    switch ($data[$key]['m']) {
                        case 'M-1':
                            $data[$key]['m'] = 1;
                            break;
                        case 'M-2':
                            $data[$key]['m'] = 2;
                            break;
                        case 'M-3':
                            $data[$key]['m'] = 3;
                            break;
                        case 'M-4':
                            $data[$key]['m'] = 4;
                            break;
                        case 'M-5':
                            $data[$key]['m'] = 5;
                            break;
                        case 'M-6':
                            $data[$key]['m'] = 6;
                            break;
                        case 'M-7':
                            $data[$key]['m'] = 7;
                            break;
                        case 'M-8':
                            $data[$key]['m'] = 8;
                            break;
                        case 'M-9':
                            $data[$key]['m'] = 9;
                            break;
                        
                        default:
                            $data[$key]['m'] = 10;
                            break;
                    }

                    $notificationPM = $this->getEntityManager()
                                           ->getRepository('AppBundle:NotificationAutresPm')
                                           ->findBy(array('dossier' =>$value->dossier_id));
                    $notificationPMId = (count($notificationPM) > 0) ? $notificationPM[0]->getId() : null;

                    $data[$key]['ob'] = ($isOb) ? 'PB' : 'OK';
                    $data[$key]['d'] = $dossier->getNom();
                    $data[$key]['al'] = ($value->a_lettrer) ? $value->a_lettrer : 0;
                    $data[$key]['d_ob_m'] = json_encode($dataObMq, true);
                    $data[$key]['mail'] = '<span class="pointer"><i class="fa fa-cog class_action_autres_pm_notif" aria-hidden="true"></i></span>';
                    $decaiss = $this->getPieceManquant($dossier, $b, $bc, $exercice, 2, null, $interval, 0, null, $user, [],true);
                    $encaiss = $this->getPieceManquant($dossier, $b, $bc, $exercice, 3, null, $interval, 0, null, $user, [],true);
                    $cheque = $this->getPieceManquant($dossier, $b, $bc, $exercice, 4, null, $interval, 0, null, $user, [],true);
                    $frns = $this->getPieceManquant($dossier, $b, $bc, $exercice, 5, null, $interval, 0, null, $user, [],true);
                    $client = $this->getPieceManquant($dossier, $b, $bc, $exercice, 6, null, $interval, 0, null, $user, [],true);
                    $data[$key]['dec'] = $decaiss;
                    $data[$key]['enc'] = $encaiss;
                    $data[$key]['chq'] = $cheque;
                    $data[$key]['frns'] = $frns;
                    $data[$key]['clt'] = $client;
                    $data[$key]['id'] = $value->dossier_id.'-'.$value->banquecompte_id.'-'.$notificationPMId;
                    $datas[] = $data[$key];
                }
            }
        }
        return $datas;
    }

    public function getPieceManquantForNotif(Utilisateur $user, $dossierArray = [], $exercice = 2018, $type = 2, $intervals = [90,500000],$dateType = 0)
    {
        /**
         * 0: releve banques manquants,
         * 1: operation banques manquantes
         * 2: factures fournisseurs manquants
         * 3: factures clients manquants
         * 4: cheques inconnus
         * 8: tous releve banques manquants
         */
        $results = [];
        $count = 0;
        if ($type == 9)
        {
            $datas = $this->getAutresPmNotif($dossierArray, $exercice, $user, $dateType, $intervals);
            return $datas;
        }
        else if ($type == 8)
        {
            $datas = $this->getDetailsTachesBanque($dossierArray, $exercice);
            return $datas;
        }
    }
}