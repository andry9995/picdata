<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 07/07/2017
 * Time: 09:17
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\ReleveComplet;
use AppBundle\Entity\ReleveManquant;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Functions\CustomPdoConnection;

class ReleveManquantRepository extends EntityRepository
{

    /**
     * Liste des dossiers actifs par exercice
     *
     * @param integer $client
     * @param integer $exercice
     *
     * @return array
     */
    public function getListDosierByExo($client,$exercice, $actif = true)
    {
        $client = Boost::deboost($client,$this);

        if ($client == 0) {
            $where = 'd.status = 1 AND d.active = 1';
            $or = 'd.status<>1 AND d.statusDebut IS NOT NULL AND d.statusDebut > :exercice AND d.active = 1';
            
            if ($actif) {
               $param = array(
                    'exercice' =>$exercice
                );
            } else {
                $param = array(
                    'exercice' => intval($exercice) - 1
                );
            }

            
        }
        else{
            $where = 'client.id = :client AND d.status = 1 AND d.active = 1';
            $or = 'client.id = :client AND d.status<>1 AND d.statusDebut IS NOT NULL AND d.statusDebut > :exercice AND d.active = 1';
            
            if ($actif) {
                $param = array(
                    'exercice' =>$exercice,
                    'client' => $client
                );
            } else {
                $param = array(
                    'exercice' => intval($exercice) - 1,
                    'client' => $client
                );
            }

        }

        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->createQueryBuilder('d');
        $listes = $qb
            ->select('d')
            ->innerJoin('d.site', 'site')
            ->addSelect('site')
            ->innerJoin('site.client', 'client')
            ->addSelect('client')
            ->where($where)
            ->orWhere($or)
            ->orderBy('d.nom', 'ASC')
            ->setParameters($param)
            ->getQuery()
            ->getResult();

        $data = array();

        foreach ($listes as $dossier) {
            $data[] = [
                'id' => $dossier->getId(),
                'idCrypter' => $dossier->getIdCrypter(),
                'nom' => $dossier->getNom(),
                'indicateurGroup' => $dossier->getIndicateurGroup(),
                'cloture' => $dossier->getCloture(),
                'site' => $dossier->getSite()->getNom(),
                'site_id' => $dossier->getSite()->getId(),
                'status' => $dossier->getStatus(),
                'statusDebut' => $dossier->getStatusDebut(),
                'client' => $dossier->getSite()->getClient()->getNom(),
                'client_id' => $dossier->getSite()->getClient()->getId(),
                'date_cloture' => $dossier->getDateCloture(),
                'debut_activite' => $dossier->getDebutActivite(),
            ];
        }

        return $data;

    }

    /**
     * Liste des mois manquant dans un relevé
     *
     * @param integer $client
     * @param integer $dossier
     * @param integer $exercice
     * @param integer $bc
     *
     * @return string
     */
    public function moisManquant($client,$dossier,$exercice,$bc)
    {

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $client = Boost::deboost($client,$this);

        $query = "  SELECT r.mois 
                    FROM releve_manquant  r
                    inner join dossier d on (d.id = r.dossier_id)
                    inner join site si on (si.id = d.site_id)
                    inner join client c on (c.id = si.client_id)
                    inner join banque_compte bc on bc.id = r.banque_compte_id
                    where r.dossier_id = " . $dossier;

        $query .= " and r.exercice = " . $exercice;
        
        $query .= " and c.id = " . $client;

        $query .= " and bc.id = " . $bc ;

        $query .= " AND (d.status = 1";
        $query .= " OR ( d.status <> 1 
                    AND d.status_debut IS NOT NULL 
                    AND d.status_debut > " . $exercice . " ))";

        $prep = $pdo->prepare($query);


        $prep->execute();

        $result = $prep->fetchAll();

        if (empty($result)) {
            return '';
        }

        return $result[0]->mois;
    }


    /**
     * Liste sans image
     *
     * @param array $param
     * @param array $tabCompte
     *
     * @return array
     */
    public function getListeSansImage($param, $tabCompte,$user){


        $clientId = Boost::deboost($param['client'],$this);
        $dossierId = Boost::deboost($param['dossier'],$this);

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "select c.id as client_id, d.id as dossier_id, b.nom as banque, d.status, c.nom as clients, d.nom as dossier, d.status, d.status_debut,
                (case
                    when length(bc.numcompte) >= 11 then substring(bc.numcompte, length(bc.numcompte)-10, length(bc.numcompte))
                    else bc.numcompte
                end) as comptes, rtva.libelle as regime_tva, bc.numcompte,bc.id as bc, d.cloture
                from dossier d
                inner join site s on (s.id = d.site_id)
                inner join client c on (c.id = s.client_id)
                inner join banque_compte bc on (bc.dossier_id = d.id) " . $inner_user . "
                left join banque b on (b.id = bc.banque_id)
                left join regime_tva rtva on (d.regime_tva_id = rtva.id)
                where c.status = 1 
                and d.status = 1 ";


        if($dossierId != 0 AND $clientId != 0){

            $query .= "and c.id = " . $clientId . $by_user . "
                       group by bc.numcompte, d.id 
                       having client_id = ".$clientId." and dossier_id = ".$dossierId."";

            $query .= " and bc.id NOT IN ( '" . implode( "', '" , $tabCompte ) . "' )";
            
            $query .= " AND (d.status = 1";
            $query .= " OR ( d.status <> 1 
                        AND d.status_debut IS NOT NULL 
                        AND d.status_debut > " . $param['exercice'] . " ))";

        }else if($dossierId == 0 AND $clientId != 0){

            $query .= "and c.id = " . $clientId . $by_user . "
                       group by bc.numcompte, d.id
                       having client_id = ".$clientId."";

            $query .= " and bc.id NOT IN ( '" . implode( "', '" , $tabCompte ) . "' )";

            $query .= " AND (d.status = 1";
            $query .= " OR ( d.status <> 1 
                        AND d.status_debut IS NOT NULL 
                        AND d.status_debut > " . $param['exercice'] . " ))";
        }else{ //tous
            $query .= $by_user;
            $query .= "group by bc.numcompte, d.id";
        }


        $prep = $pdo->prepare($query);

        $prep->execute();

        $result = $prep->fetchAll();

        return $result;
    }

    public function getRBM($param)
    {

        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);
        $param['dossier'] = Boost::deboost($param['dossier'],$this);

        $query = "select d.cloture, d.nom as nom_dossier,count(distinct d.nom) as nb_dossier, bc.id as bc, d.id as dossier_id, rm.mois as mois, bc.numcompte, i.exercice, count(i.id) as nb";

        $query .= " from releve r
                    inner join image i on (i.id = r.image_id)
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
                    inner join banque b on (b.id=bc.banque_id)
                    inner join separation sep on sep.image_id = i.id and sep.souscategorie_id = 10
                    left join releve_manquant rm on (d.id = rm.dossier_id)
                    where i.exercice = ".$param['exercice']."
                    and c.status = 1
                    and bc.numcompte <> ''";

        $query .= " and c.id = " .$param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " AND (d.status = 1";
        $query .= " OR ( d.status <> 1 
                    AND d.status_debut IS NOT NULL 
                    AND d.status_debut > " . $param['exercice'] . " ))";
                
        $query .= " group by bc.id";
        
        $prep  = $pdo->prepare($query);

        $prep->execute();

        $result = $prep->fetchAll();

        return $result;

    }

    /**
     *  Situation des Comptes Bancaires
     *
     * @param array $param
     *
     * @return array
     */
    public function getSituationComptesBancaire($param, $user)
    {

        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);
        $param['dossier'] = Boost::deboost($param['dossier'],$this);

        $query = "select d.cloture, i.valider, d.nom as nom_dossier,count(distinct d.nom) as nb_dossier, bc.id as bc, d.id as dossier_id, '' as mois, bc.numcompte, i.exercice";

        $by_user    = "";
        
        $user_type  = $user->getAccesUtilisateur()->getType();
        
        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user    = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query .= " from releve r
                    inner join image i on (i.id = r.image_id)
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
                    inner join banque b on (b.id=bc.banque_id)
                    inner join separation sep on sep.image_id = i.id and sep.souscategorie_id = 10 " . $inner_user . "
                    where i.exercice = ".$param['exercice']."
                    and c.status = 1
                    and bc.numcompte <> ''";

        $query .= " and c.id = " .$param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " AND (d.status = 1";
        $query .= " OR ( d.status <> 1 
                    AND d.status_debut IS NOT NULL 
                    AND d.status_debut > " . $param['exercice'] . " ))";

        $query .= $by_user;
                
        $query .= " group by bc.id, i.valider";
        
        $prep  = $pdo->prepare($query);

        $prep->execute();

        $result = $prep->fetchAll();

        return $result;

    }

    public function getListeByDossiers($dossier_ids, $exercice)
    {
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:ReleveManquant')
            ->createQueryBuilder('rm');
        $listes = $qb
            ->select('rm')
            ->innerJoin('rm.banqueCompte', 'banqueCompte')
            ->addSelect('banqueCompte')
            ->innerJoin('banqueCompte.banque', 'banque')
            ->addSelect('banque')
            ->innerJoin('rm.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('rm.exercice = :exercice')
            ->andWhere($qb->expr()->in('dossier.id', $dossier_ids))
            ->setParameters(array(
                'exercice' => $exercice,
            ))
            ->getQuery()
            ->getResult();

        $manquants = [];
        /** @var ReleveManquant $manquant */
        foreach ($listes as $manquant) {

            $status = $manquant->getStatus();

            if($status === false){
                //Jerena raha efa nisy relevé tany @ exercice tany aloha

                if($this->getEntityManager()
                    ->getRepository('AppBundle:Releve')
                    ->hasReleveOnPreviousExercice($manquant->getBanqueCompte(), $exercice)
                )
                {
                    $manquant->setStatus(true);
                }
            }

            $manquants[$manquant->getDossier()->getId()][$manquant->getBanqueCompte()->getId()] = [
                'manquant' => $manquant->getMois(),
                'status' => $manquant->getStatus(),
            ];
        }

        return $manquants;
    }

    public function getListeByBanqueCompte($banqueCompte, $exercice){
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:ReleveManquant')
            ->createQueryBuilder('rm');

        $listes = $qb
            ->where('rm.banqueCompte = :banqueCompte')
            ->andWhere('rm.exercice = :exercice')
            ->setParameter('banqueCompte', $banqueCompte)
            ->setParameter('exercice', $exercice)
            ->getQuery()
            ->getResult();

        return $listes;
    }

    public function updateReleveManquant()
    {
        $em = $this->getEntityManager();


        $dossiers = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->findBy(array('status' => 1));
//        $dossiers[] = $this->getEntityManager()
//            ->getRepository('AppBundle:Dossier')
//            ->find(11383);

        $listeRelevesManquant = array();

        /** @var  $dossier Dossier */
        foreach ($dossiers as $dossier) {
            echo $dossier->getNom() . "\r\n";

            $banqueComptes = $this->getEntityManager()
                ->getRepository('AppBundle:BanqueCompte')
                ->getBanqueCompteByDossier($dossier);


            /** @var  $banqueCompte BanqueCompte */
            foreach ($banqueComptes as $banqueCompte) {
//                $exercices = Boost::getExercices(2, 1);
                $now = new \DateTime();
                $current_exercice = $now->format('Y');
                $exercices[] = (int)$current_exercice - 1;
                $exercices[] = (int)$current_exercice;
                $exercices[] = (int)$current_exercice + 1;

                foreach ($exercices as $exercice) {

                    $resReleves = $this->getEntityManager()
                        ->getRepository('AppBundle:Image')
                        ->getInfoReleveByDossier($banqueCompte->getId(), $exercice);

                    $infoReleves = $this
                        ->getEntityManager()
                        ->getRepository('AppBundle:ReleveManquant')
                        ->InitializeInfoReleves($resReleves, $dossier, $exercice);

                    if(count($infoReleves) == 0) {
                        $infoReleves = $this->InitializePasImageInfoReleves($dossier, $banqueCompte, $exercice);
                    }

                    $moisList = array();

                    //Raha tsy misy releve mihitsy
                    $statusReleve = count($resReleves) !== 0;

                    $moisOk = "";
                    $trouveReleveManquant = false;

                    for ($i = 0, $iMax = count($infoReleves); $i < $iMax; $i++) {

                        $releve = $infoReleves[$i];

                        if ($releve->controle === 'Relevé Manquant') {

                            $periode = date("Y", strtotime($releve->periode_deb)) . '-' . date("m", strtotime($releve->periode_deb));

                            if (!in_array($periode, $moisList)) {
                                $moisList[] = $periode;
                            }

                            $trouveReleveManquant = true;
                        }

                    }
                    $this->SaveRelevesManquant($exercice,$dossier,$banqueCompte,$moisList, $statusReleve, $em);

                    if(!$trouveReleveManquant){
                        if(count($infoReleves) > 0){

                            $releve = $infoReleves[count($infoReleves) -1];
                            $moisOk = date("Y", strtotime($releve->periode_deb)) . '-' . date("m", strtotime($releve->periode_deb));
                        }

                        $this->SaveRelevesComplet($exercice,$dossier,$banqueCompte, $moisOk, $em);

                    }
                }
            }
        }

        return new JsonResponse($listeRelevesManquant);
    }

    /**
     * @param $resReleves
     * @param $dossier Dossier
     * @param $exercice
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    function InitializeInfoReleves($resReleves, $dossier, $exercice)
    {

        $infoReleves = array();

        //Manala ny doublon rehetra
        for ($i = 0; $i < count($resReleves) - 1; $i++) {

            $tempI = $resReleves[$i];
            $trouveDoublon = false;

            for ($j = $i + 1, $jMax = count($resReleves); $j < $jMax; $j++) {

                $tempJ = $resReleves[$j];

                if ($tempI->periode_deb == $tempJ->periode_deb && $tempI->periode_fin == $tempJ->periode_fin &&
                    $tempI->solde_deb == $tempJ->solde_deb && $tempI->solde_fin == $tempJ->solde_fin
                ) {
                    $trouveDoublon = true;
                    break;
                }
            }

            if ($trouveDoublon == false) {
                $infoReleves[] = $tempI;
            }
        }

        if (count($resReleves) > 0) {
            $infoReleves[] = $resReleves[count($resReleves) - 1];
        }


        $listeManquant = array();


        //Mijery ny releve manquant
        for ($i = 0; $i < count($infoReleves) - 1; $i++) {

            $ligneI = $infoReleves[$i];

            $soldeFinI = round($ligneI->solde_fin, 2, PHP_ROUND_HALF_DOWN);
            $soldeDebI = round($ligneI->solde_deb, 2, PHP_ROUND_HALF_DOWN);

            $periodeFinI = new \DateTime($ligneI->periode_fin);
            $periodeDebI = new \DateTime($ligneI->periode_deb);

            //Jerena raha 0 ny solde fin i && 0 ny solde debut i+1 && datefin i == datedebut i+1
            $ligneI1 = $infoReleves[$i + 1];
            $soldeDebI1 = round($ligneI1->solde_deb, 2, PHP_ROUND_HALF_DOWN);
            $soldeFinI1 = round($ligneI1->solde_fin, 2, PHP_ROUND_HALF_DOWN);
            $periodeDebI1 = new \DateTime($ligneI1->periode_deb);

            $trouveSuivant = false;

            if ($ligneI1->image_id_precedent == 0) {

                if (!($soldeDebI1 == 0 && $soldeFinI1 == 0)) {

                    //Raha mitovy ny periode debut i & i+1 & sode debut i+1 = solde fin i
                    if ($soldeDebI1 == 0 && $soldeFinI == 0 && $periodeDebI->diff($periodeDebI1)->days == 0) {

                        $ligneI->image_id_suivant = $ligneI1->image_id;
                        $ligneI1->image_id_precedent = $ligneI->image_id;

                        $trouveSuivant = true;
                    } //Raha 0 ny solde debut & fin an'ny i sy ny solde debut an'ny i+1
                    else if ($soldeDebI1 == 0 && $soldeFinI == 0 && $soldeDebI == 0 && $periodeFinI->diff($periodeDebI1)->days <= 15) {
                        $ligneI->image_id_suivant = $ligneI1->image_id;
                        $ligneI1->image_id_precedent = $ligneI->image_id;

                        $trouveSuivant = true;
                    }
                }
            }


            //Raha mahita ao @ i1 dia tsy mila miditra ao @ j & k intsony

            if ($trouveSuivant == false) {

                //Mijery ny contrepartie an'ny i
                for ($j = $i + 1, $jMax = count($infoReleves); $j < $jMax; $j++) {

                    $ligneJ = $infoReleves[$j];

                    $trouve = false;

                    $soldeDebJ = round($ligneJ->solde_deb, 2, PHP_ROUND_HALF_DOWN);
                    $soldeFinJ = round($ligneJ->solde_fin, 2, PHP_ROUND_HALF_DOWN);

//                    if($soldeFinJ == 0 && $soldeDebJ == 0){
//                        continue;
//                    }

                    if ($ligneJ->image_id_precedent == 0 || ($ligneJ->image_id_precedent != 0 && $ligneJ->solde_deb == 0)) {

                        $periodeDebJ = new \DateTime($ligneJ->periode_deb);

                        $diff = $periodeFinI->diff($periodeDebJ)->days;

                        if ($soldeDebJ == $soldeFinI && $diff <= 1) {
                            $ligneI->image_id_suivant = $ligneJ->image_id;
                            $ligneJ->image_id_precedent = $ligneI->image_id;

                            $trouve = true;

                            $trouveSuivant = true;

                        } else {
                            //Jerena raha entre an'ilay periode debut sy fin an'ny i ny periode debut an'i j
                            if ($soldeDebJ == $soldeFinI && $periodeDebJ >= $periodeFinI && $periodeDebJ <= $periodeFinI) {
                                $ligneI->image_id_suivant = $ligneJ->image_id;
                                $ligneJ->image_id_precedent = $ligneI->image_id;

                                $trouve = true;

                                $trouveSuivant = true;

                            } //Raha tsy anaty période dia date à verifier
                            else {
//                            if ($soldeDebJ != 0 && $soldeDebJ == $soldeFinI && ($diff > 1 || $diff < -1)) {
                                if ($soldeDebJ == $soldeFinI && abs($diff) > 1) {

                                    $ligneI->image_id_suivant = $ligneJ->image_id;
                                    $ligneJ->image_id_precedent = $ligneI->image_id;

                                    if (!($soldeDebJ == $soldeFinJ && $soldeDebJ == 0)) {
                                        $infoReleves[$j]->controle = 'Date à verifier';
                                    }

                                    $trouve = true;

                                    $trouveSuivant = true;
                                }
                            }
                        }

                        if ($trouve == true) {

                            if ($soldeFinI != 0) {
                                //Relevé Intermediaire
                                for ($k = $i + 1; $k < $j; $k++) {

                                    $ligneK = $infoReleves[$k];
                                    $periodeDebK = new \DateTime($ligneK->periode_deb);

//                                    if ($periodeDebK != $periodeDebI)
                                    {

                                        //Mbola tokony ho verifier-na ny condition faha2 '||'
                                        if ($ligneK->image_id_precedent == 0) {
                                            if (($periodeDebK >= $periodeDebI && $periodeDebK <= $periodeFinI)
                                                || ($periodeDebK >= $periodeFinI && $periodeDebK <= $periodeDebJ)
                                            ) {
                                                $ligneK->releve_intermediaire = 1;

//                                                $ligneK->controle = 'Relevé intermediaire';
                                            }

                                        } elseif ($ligneK->image_id_suivant == 0) {
                                            if (($periodeDebK >= $periodeDebI && $periodeDebK <= $periodeFinI)
                                                || ($periodeDebK >= $periodeFinI && $periodeDebK <= $periodeDebJ)
                                            ) {
                                                $ligneK->releve_intermediaire = 1;

//                                                $ligneK->controle = 'Relevé intermediaire';
                                            }
                                        }
                                    }
                                }
                            }

                            break;
                        }
                    }
                }
            }

            if ($trouveSuivant == false) {

                if ($ligneI->releve_intermediaire == 0) {

//                    $ligneI->controle = 'Relevé Manquant';

                    if (!in_array($i, $listeManquant)) {
                        $listeManquant[] = $i;
                    }
//                    if(($ligneI->solde_deb == 0 && $ligneI->solde_fin == 0)) {
//
//                        if($i>0)
//                        {
//                            if($infoReleves[$i+1]->image_id_precedent == 0)
//                            {
//
//                            }
//                        }
//
//                    }
                }

            } else {
                if ($ligneI->image_id_precedent == 0 && $ligneI->releve_intermediaire == 0) {
                    if ($i > 0) {

                        if ($infoReleves[$i - 1]->image_id_suivant == 0) {

                            if ($infoReleves[$i - 1]->solde_deb == 0 && $infoReleves[$i - 1]->solde_fin == 0 &&
                                $infoReleves[$i]->solde_deb != 0
                            ) {

//                                $infoReleves[$i - 1]->controle = 'Relevé Manquant';


                                if (!in_array($i - 1, $listeManquant)) {
                                    $listeManquant[] = $i - 1;
                                }
                            }
                        } else {
                            if ($ligneI->image_id_suivant == 0) {
//                                $infoReleves[$i]->controle = 'Relevé Manquant';

                                if (!in_array($i, $listeManquant)) {
                                    $listeManquant[] = $i;
                                }

                            } //Cas mitranga rehefa misy 0 solde debut & fin
                            else if ($ligneI->image_id_precedent == 0) {
//                                $infoReleves[$i - 1]->controle = 'Relevé Manquant';


                                if (!in_array($i - 1, $listeManquant)) {
                                    $listeManquant[] = $i - 1;
                                }
                            }
                        }
                    }
                }
            }
        }


        //Ajout ligne ho an'ny releve Manquant
        $res = array();

        $j = 0;
        while ($j < count($infoReleves)) {
            if (!in_array($j, $listeManquant)) {
                $res[] = $infoReleves[$j];

            } else {

                $res[] = $infoReleves[$j];

                $res[] = (object)array(

                    'banque_nom' => '',
                    'numcompte' => '',
                    'periode_deb' => '',
                    'periode_fin' => '',
                    'num_releve' => '',
                    'solde_deb' => '',//0.01,
                    'solde_fin' => '',//0.01,
                    'controle' => 'Relevé Manquant',
                    'date_scan' => null,
                    'image_id' => -1,
                    'image_nom' => ''
                );
            }


            $j++;


        }


        $infoReleves = $res;
        //Verifier-na raha tsy misy image eo alohan'ny' releve debut_periode
        $infoRelevesDebut = array();

        if (count($infoReleves) > 0) {

            $tbImagePeriodes = $this->getEntityManager()
                ->getRepository('AppBundle:TbimagePeriode')
                ->findBy(array('dossier' => $dossier));

            $demarrageTb = false;
            $demarrage = null;
            $premiereCloture = null;

            if (count($tbImagePeriodes) > 0) {
                $tbImagePeriode = $tbImagePeriodes[0];

                if($dossier->getDebutActivite() === null)
                    $demarrage = $tbImagePeriode->getDemarrage();
                else
                    $demarrage = $dossier->getDebutActivite();

                /** @var \DateTime $premiereCloture */
                $premiereCloture = $tbImagePeriode->getPremiereCloture();

                if (null !== $demarrage && null !== $premiereCloture) {

                    if(null !== $infoReleves[0]->periode_deb){
                        $firstReleveDebutDate = new \DateTime($infoReleves[0]->periode_deb);
                        if ((int)$firstReleveDebutDate->format('Y') <= $premiereCloture->format('Y')) {
                            $demarrageTb = true;
                        }
                    }
//
//                    else if ($exercice === $premiereCloture->format('Y')) {
//                        $demarrageTb = true;
//                    }
                }
            }
//            else{
//                $demarrage = $dossier->getDebutActivite();
//            }


            $clotureDate = $this->getEntityManager()
                ->getRepository('AppBundle:Dossier')
                ->getDateCloture($dossier, $exercice);

            if (!$demarrageTb) {

                $debutActivite = $dossier->getDebutActivite();
                $useDebutAct = false;
                if(null !== $debutActivite){
                    $anneeDebAct = $debutActivite->format('Y');

                    if($anneeDebAct == $exercice){
                        $debutDate = $debutActivite;
                        $useDebutAct = true;
                    }
                }
                if(!$useDebutAct) {
                    $debutDate = $clotureDate->modify('-12 months')->modify('+1 days');
                }

            } else {
                $debutDate = $demarrage;
            }

            $debutYear = $debutDate->format('Y');
            $debutMonth = $debutDate->format('m');


            $infoReleve = $infoReleves[0];

            if (null !== $infoReleve->periode_deb) {

                $firstReleveDebutDate = new \DateTime($infoReleve->periode_deb);

                $complete = true;
                if($premiereCloture !== null){
                    if($premiereCloture <= $firstReleveDebutDate){
                        $complete = false;
                    }
                }
//                if($complete)
                {

                    if ($firstReleveDebutDate > $debutDate) {

                        $firstReleveMonth = $firstReleveDebutDate->format('m');
                        $firstReleveDay = $firstReleveDebutDate->format('d');

                        $diffYear = (int)$firstReleveDebutDate->format('Y') - (int)$debutYear;
                        $diff = ($diffYear * 12) + ((int)$firstReleveMonth - (int)$debutMonth);

                        $firstReleveMonth = (int)$debutMonth;

                        if ((int)$firstReleveDay > 15) {
                            $diff++;
                        }

                        for ($i = 0; $i < $diff; $i++) {

                            if ($firstReleveMonth < 10) {
                                $firstReleveMonth = "0" . $firstReleveMonth;
                            }

                            if ($firstReleveMonth == 13) {
                                $firstReleveMonth = 1;
                                $debutYear = $debutYear + 1;
                            }

                            $periodeDeb = $debutYear . '-' . $firstReleveMonth . '-01';

                            $info = (object)array(
//                            'banque_nom' => $banqueNom,
//                            'numcompte' => $numCompte,
//                            'periode_deb' => $periodeDeb,
                                'banque_nom' => '',
                                'numcompte' => '',
                                'periode_deb' => $periodeDeb,
                                'periode_fin' => null,
                                'num_releve' => '',
                                'solde_deb' => '',//0.01,
                                'solde_fin' => '',//0.01,
                                'controle' => 'Relevé Manquant',
                                'date_scan' => null,
                                'image_id' => -1,
                                'image_nom' => ''
                            );

                            $infoRelevesDebut[] = $info;

                            $firstReleveMonth++;

                        }
                    }
                }
            }
        }

        //Atambatra ny releve debut & inforeleve
        $res = array_merge($infoRelevesDebut, $infoReleves);
        $infoReleves = $res;

        //Verification raha tsy misy images eo anelanelan'ny mois actuel sy ny mois cloture
        if (count($infoReleves) >= 1) {

            $clotureDate = $this->getEntityManager()
                ->getRepository('AppBundle:Dossier')
                ->getDateCloture($dossier, $exercice);

            $lastReleveFinDate = null;


            if (null !== $infoReleves[count($infoReleves) - 1]->periode_fin && null !== $infoReleves[count($infoReleves) - 1]->periode_deb) {

                $lastReleveFinDate = new \DateTime($infoReleves[count($infoReleves) - 1]->periode_fin);
                $lastReleveDebutDate = new \DateTime($infoReleves[count($infoReleves) - 1]->periode_deb);

                if($lastReleveFinDate >= $lastReleveDebutDate) {

                    if ($lastReleveFinDate < $clotureDate && $lastReleveDebutDate < $clotureDate) {

                        $currentDate = new \DateTime('now');
                        $currentYear = $currentDate->format('Y');
                        $currentMonth = $currentDate->format('m');

                        $clotureMonth = (int)$clotureDate->format('m');
                        $clotureYear = (int)$clotureDate->format('Y');

                        if((int)$lastReleveFinDate->format('d') > 15 && ((int)$lastReleveFinDate->format('m') < $clotureMonth)) {
                            $lastRelevemonth = (int)$lastReleveFinDate->format('m') + 1;
                        }
                        else{
                            if((int)$lastReleveFinDate->format('d') > 15 && ((int)$lastReleveFinDate->format('Y') < $clotureYear)){
                                $lastRelevemonth = (int)$lastReleveFinDate->format('m') + 1;
                            }else {
                                $lastRelevemonth = (int)$lastReleveFinDate->format('m');
                            }
                        }

                        $lastReleveYear = $lastReleveFinDate->format('Y');



                        $anneeSuivante = false;


                        $diffYear = (int)$clotureDate->format('Y') - (int)$lastReleveYear;
                        $diffMonth = ($diffYear * 12) + (int)$clotureDate->format('m') - (int)$lastReleveFinDate->format('m');


                        if($diffMonth > 0) {

                            for ($i = 0; $i <= $diffMonth; $i++) {

                                if ($lastRelevemonth < 10) {
                                    $lastRelevemonth = "0" . $lastRelevemonth;
                                }

                                if ($lastRelevemonth == 13) {
                                    $lastRelevemonth = 1;
                                    $lastReleveYear = (int)$lastReleveYear + 1;
                                    $anneeSuivante = true;
                                }

                                if ($anneeSuivante) {
                                    if ($lastRelevemonth - 1 == $clotureMonth) {
                                        break;
                                    }
                                }

                                if ($lastReleveYear == $currentYear) {
                                    if ($lastRelevemonth - 1 == $currentMonth) {
                                        break;
                                    }
                                }

                                if ($lastReleveYear > (int)$clotureDate->format('Y')) {
                                    break;
                                }


                                $periodeDeb = $lastReleveYear . '-' . $lastRelevemonth . '-01';

                                $info = (object)array(
//                            'banque_nom' => $banqueNom,
//                            'numcompte' => $numCompte,
//                            'periode_deb' => $periodeDeb,
                                    'banque_nom' => '',
                                    'numcompte' => '',
                                    'periode_deb' => $periodeDeb,
                                    'periode_fin' => null,
                                    'num_releve' => '',
                                    'solde_deb' => '',//0.01,
                                    'solde_fin' => '',//0.01,
                                    'controle' => 'Relevé Manquant',
                                    'date_scan' => null,
                                    'image_id' => -1,
                                    'image_nom' => ''
                                );

                                $infoReleves[] = $info;

                                $lastRelevemonth++;
                            }
                        }
                    }

                }
                else{
                    $infoReleves[count($infoReleves) - 1]->controle = 'Relevé Manquant';
                }
            }

        }


        return $infoReleves;
    }

    /**
     * @param $dossier Dossier
     * @param $exercice
     * @param $banqueCompte BanqueCompte
     * @return array
     */
    function InitializePasImageInfoReleves($dossier, $exercice, $banqueCompte)
    {
        $infoReleves = array();

        $clotureDate = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->getDateCloture($dossier, $exercice);


        $currentDate = new \DateTime('now');

        $lastReleveMonth = 0;

        $currentMonth = (int)$currentDate->format('m');
        $currentYear = (int)$currentDate->format('Y');
        $clotureMonth = (int)$clotureDate->format('m');


        //Raha now > exerice => katreo @ date cloture no fenoina relevé manquant
        $month = $lastReleveMonth + 1;

        if ($currentYear > $exercice) {
//            if ($lastReleveMonth < $clotureMonth) {
//                for ($i = 0; $i < ($clotureMonth - $lastReleveMonth); $i++) {
//
//                    if($month < 10){
//                        $month = "0".$month;
//                    }
//
//                    $periodeDeb = $exercice . '-' . $month . '-01';
//                    $info = (object)array(
////                        'banque_nom' => $banqueNom,
////                        'numcompte' => $numCompte,
////                        'periode_deb' => $periodeDeb,
//                        'banque_nom' => '',
//                        'numcompte' => '',
//                        'periode_deb' => $periodeDeb,
//                        'periode_fin' => null,
//                        'num_releve' => '',
//                        'solde_deb' => '',//0.01,
//                        'solde_fin' => '',//0.01,
//                        'controle' => 'Relevé Manquant',
//                        'date_scan' => null,
//                        'image_id' => -1,
//                        'image_nom' => ''
//                    );
//
//                    $infoReleves[] = $info;
//                    $month++;
//                }


                $month = $clotureMonth;

                if($month == 12){
                    $month = 1;
                    $exerciceManquant = $exercice;
                }
                else {
                    $exerciceManquant = $exercice - 1;
                }

                for ($i = 0; $i < 12; $i++) {


                    if($month > 12){
                        $exerciceManquant = $exerciceManquant + 1;
                        $month = 1;
                    }



                    if($month < 10){
                        $month = "0".$month;
                    }

                    $periodeDeb = $exerciceManquant . '-' . $month . '-01';
                    $info = (object)array(
//                        'banque_nom' => $banqueNom,
//                        'numcompte' => $numCompte,
//                        'periode_deb' => $periodeDeb,
                        'banque_nom' => '',
                        'numcompte' => '',
                        'periode_deb' => $periodeDeb,
                        'periode_fin' => null,
                        'num_releve' => '',
                        'solde_deb' => '',//0.01,
                        'solde_fin' => '',//0.01,
                        'controle' => 'Relevé Manquant',
                        'date_scan' => null,
                        'image_id' => -1,
                        'image_nom' => ''
                    );

                    $infoReleves[] = $info;
                    $month++;
                }
//            }
        } //Raha now = exercice => katreo @ mois actuel no fenoina relevé manquant
        elseif ($currentYear == $exercice) {
//            if ($lastReleveMonth < $currentMonth) {
//                for ($i = 0; $i < ($currentMonth - $lastReleveMonth); $i++) {
//
//                    if($month <10){
//                        $month = "0".$month;
//                    }
//
//                    $periodeDeb = $currentYear . '-' . $month . '-01';
//                    $info = (object)array(
////                        'banque_nom' => $banqueNom,
////                        'numcompte' => $numCompte,
//                        'banque_nom' => '',
//                        'numcompte' => '',
//                        'periode_deb' => $periodeDeb,
//                        'periode_fin' => null,
//                        'num_releve' => '',
//                        'solde_deb' => '',//0.01,
//                        'solde_fin' => '',//0.01,
//                        'controle' => 'Relevé Manquant',
//                        'date_scan' => null,
//                        'image_id' => -1,
//                        'image_nom' => ''
//                    );
//
//                    $infoReleves[] = $info;
//                    $month++;
//                }


                $month = $clotureMonth +1;
                $exerciceManquant = $exercice - 1;

                if($month == 12){
                    $month = 1;
                    $exerciceManquant = $exercice;
                    $difference = $currentMonth -1;
                }
                else{
                    $difference = (12-$month) + $currentMonth;
                }

                //Calcul difference


//                for ($i = 0; $i < ($currentMonth - $lastReleveMonth); $i++) {

            $nbMois = 0;
            for ($i = 0; $i <= $difference; $i++){

                    if($month > 12){
                        $exerciceManquant = $exerciceManquant + 1;
                        $month = 1;
                    }

                    if($month <10){
                        $month = "0".$month;
                    }

                    $periodeDeb = $exerciceManquant . '-' . $month . '-01';
                    $info = (object)array(
//                        'banque_nom' => $banqueNom,
//                        'numcompte' => $numCompte,
                        'banque_nom' => '',
                        'numcompte' => '',
                        'periode_deb' => $periodeDeb,
                        'periode_fin' => null,
                        'num_releve' => '',
                        'solde_deb' => '',//0.01,
                        'solde_fin' => '',//0.01,
                        'controle' => 'Relevé Manquant',
                        'date_scan' => null,
                        'image_id' => -1,
                        'image_nom' => ''
                    );

                    $infoReleves[] = $info;
                    $month++;

                    $nbMois++;

                    if($nbMois >= 12){
                        break;
                    }
                }



//            }
        }

        return $infoReleves;

    }

    /**
     * @param $exercice
     * @param $dossier Dossier
     * @param $banqueCompte BanqueCompte
     * @param $moisList array
     * @param $statusReleve
     * @param $em EntityManager
     */
    function SaveRelevesManquant($exercice, $dossier, $banqueCompte, $moisList, $statusReleve,$em){
        //SAVE
        /** @var  $releveManquant ReleveManquant */
        $resReleveManquant = $this->getEntityManager()
            ->getRepository('AppBundle:ReleveManquant')
            ->findBy(array('exercice' => $exercice, 'dossier' => $dossier, 'banqueCompte' => $banqueCompte));

        //Insertion na mise à jour any @ base de données
        if (count($moisList) > 0) {

            //Raha mbola tsy misy dia insertion
            if (count($resReleveManquant) == 0) {
                $releveManquant = new ReleveManquant();

                $releveManquant->setDossier($dossier);
                $releveManquant->setBanqueCompte($banqueCompte);
                $releveManquant->setExercice($exercice);
                $releveManquant->setMois($moisList);

                $releveManquant->setStatus($statusReleve);

                $em->persist($releveManquant);
            } //Raha efa misy dia atao mise à jour fotsiny ny mois
            else {
                $releveManquant = $resReleveManquant[0];
                $releveManquant->setMois($moisList);

                $releveManquant->setStatus($statusReleve);
            }

            $em->flush();

            $info = array('dossier' => $dossier->getId(), 'banque' => $banqueCompte->getId(),
                'exercice' => $exercice, 'mois' => $moisList);

            $listeRelevesManquant[] = $info;
        } //Fafana ao anaty table raha efa feno ilay relevé
        else if (count($resReleveManquant) > 0) {

            $releveManquant = $resReleveManquant[0];
            $em->remove($releveManquant);

            $em->flush();

        }
    }

    /**
     * @param $exercice
     * @param $dossier Dossier
     * @param $banqueCompte BanqueCompte
     * @param $moisOk
     * @param $em EntityManager
     */
    function SaveRelevesComplet($exercice, $dossier, $banqueCompte, $moisOk, $em){
        //SAVE

        $resReleveComplet = $this->getEntityManager()
            ->getRepository('AppBundle:ReleveComplet')
            ->findBy(array('exercice' => $exercice, 'dossier' => $dossier, 'banqueCompte' => $banqueCompte));

        //Insertion na mise à jour any @ base de données
        if ($moisOk != "") {

            //Raha mbola tsy misy dia insertion
            if (count($resReleveComplet) == 0) {
                $releveComplet = new ReleveComplet();

                $releveComplet->setDossier($dossier);
                $releveComplet->setBanqueCompte($banqueCompte);
                $releveComplet->setExercice($exercice);
                $releveComplet->setMois($moisOk);

                $em->persist($releveComplet);
            } //Raha efa misy dia atao mise à jour fotsiny ny mois
            else {
                /** @var  $releveComplet ReleveComplet*/
                $releveComplet = $resReleveComplet[0];
                $releveComplet->setMois($moisOk);
            }

            $em->flush();

            $info = array('dossier' => $dossier->getId(), 'banque' => $banqueCompte->getId(),
                'exercice' => $exercice, 'mois' => $moisOk);

            $listeRelevesManquant[] = $info;
        } //Fafana ao anaty table raha efa feno ilay relevé
        else
            if (count($resReleveComplet) > 0) {

            $releveComplet = $resReleveComplet[0];
            $em->remove($releveComplet);

            $em->flush();

        }
    }

    public function getNewSituationComptesBancaire($param, $listDossier)
    {
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);
        $param['dossier'] = Boost::deboost($param['dossier'],$this);
        $dossierArray     = [];

        $dossier = " and d.id = ".$param['dossier']."";
        if($param['dossier'] == 0){
            foreach ($listDossier as $k => $d) {
                $dossierArray[] = Boost::deboost($d,$this);
            }
            $dossier = " and d.id IN ( '" . implode("', '", $dossierArray) . "' )";
        }
        $query = "select d.cloture, i.valider, d.nom as nom_dossier,count(distinct d.nom) as nb_dossier, bc.id as bc, d.id as dossier_id, rm.mois, bc.numcompte, i.exercice
            from releve r
            inner join releve_manquant rm on rm.banque_compte_id = r.banque_compte_id
            inner join image i on i.id = r.image_id
            inner join lot l on (l.id = i.lot_id)
            inner join dossier d on (d.id = l.dossier_id)
            inner join site s on s.id = d.site_id
            inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
            inner join banque b on (b.id=bc.banque_id)
            inner join separation sep on (sep.image_id = i.id)  
            inner join souscategorie ssc on (sep.souscategorie_id = ssc.id) 
            where rm.exercice = " . $param['exercice'] . "
            and r.operateur_id is null
            and sep.souscategorie_id IS NOT NULL 
            and ssc.id = 10 
            and bc.numcompte <> ''
            and (d.status = 1)
            and i.exercice = " . $param['exercice'] . "
            and s.client_id = " .$param['client'] . "
            " . $dossier. "
            group by bc.id";
        $prep = $pdo->prepare($query);
        $prep->execute();
        return $prep->fetchAll();
    }
}