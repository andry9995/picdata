<?php

namespace AppBundle\Repository;

use AppBundle\AppBundle;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\EchangeReponse;
use AppBundle\Entity\Image;
use AppBundle\Entity\Imputation;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\Saisie1;
use AppBundle\Entity\Separation;
use AppBundle\Entity\Soussouscategorie;
use AppBundle\Entity\TvaImputation;
use AppBundle\Entity\TvaImputationControle;
use AppBundle\Entity\TvaSaisie1;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurSite;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Functions\CustomPdoConnection;
use AppBundle\Controller\Boost;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;


use Symfony\Component\HttpFoundation\JsonResponse;

class ImageRepository extends EntityRepository
{
    /**
     * @param array $ids
     * @return Image[]
     */
    public function getImagesByIds($ids = [-1])
    {
        return $this->createQueryBuilder('i')
            ->where('i.id IN (:ids)')
            //->andWhere('i.supprimer = 0')
            ->setParameters([
                'ids' => $ids
            ])
            ->orderBy('i.nom')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $ids
     * @param bool $entity
     * @return array
     */
    public function getImageActifsByIds($ids = [-1], $entity = true)
    {
        if (count($ids) == 0) $ids = [-1];

        $idsStrs = '';
        for ($i = 0; $i < count($ids); $i++)
        {
            $idsStrs .= intval($ids[$i]);
            if ($i != count($ids) - 1) $idsStrs .= ',';
        }

        $params = [
            'lDoublon' => 'DOUBLON'
        ];

        $req = '
            SELECT DISTINCT i.id 
            FROM image i 
            JOIN separation sep ON (sep.image_id = i.id)
            LEFT JOIN souscategorie sc ON (sc.id = sep.souscategorie_id) 
            WHERE i.supprimer = 0 
                AND (sc.libelle_new <> :lDoublon OR sep.souscategorie_id IS NULL)
                AND i.id IN ('.$idsStrs.')     
        ';

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $res = $prep->fetchAll();

        $results = [];

        foreach ($res as $re)
            $results[] = $re->id;

        if (!$entity) return $results;

        return $this->createQueryBuilder('i')
            ->where('i.id IN (:ids)')
            ->setParameter('ids', $results)
            ->getQuery()
            ->getResult();
    }

    /**
     * Factures Fournisseurs et Clients
     *
     * @param array $param
     *
     * @return array
     */
    public function getFactures($param,$user)
    {
           
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select count(i.id) as nb, round(r.credit-r.debit,2) as montant, d.nom as nom_dossier, d.cloture
                    from image i
                    left join releve r on (i.id = r.image_id)
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
                    inner join banque bq on (bq.id=bc.banque_id) " . $inner_user . "
                    where i.supprimer = 0 
                    and r.image_flague_id is null
                    and r.libelle not like '%CHQ%'
                    and r.libelle not like '%CHEQUE%'";
        $query .= " and i.exercice = " .$param['exercice'];
        $query .= " and c.status = 1
                    and r.operateur_id is null";
        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " and (d.status = 1";
        $query .= " or ( d.status <> 1 
                    and d.status_debut is not null 
                    and d.status_debut > " . $param['exercice'] . " ))";

        $query .= $by_user;

        $query .= " group by d.id";

        $prep = $pdo->prepare($query);
        $prep->execute();
        $resultat = $prep->fetchAll();

        return $resultat;

    }

    /**
     * Chèques inconnus groupé par banque compte
     *
     * @param array $param
     *
     * @return array
     */
    public function getChequeIconnuByBanqueCompte($param,$user)
    {
        
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);
        $param['dossier'] = Boost::deboost($param['dossier'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select count(i.id) as nb, d.id, bc.numcompte, d.nom as dossierNom, d.cloture
                    from image i
                    left join releve r on (i.id = r.image_id)
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
                    inner join separation sep on (sep.image_id = i.id)  
                    inner join souscategorie ssc on (sep.souscategorie_id = ssc.id) 
                    left join cle_dossier cld on (cld.id = r.cle_dossier_id)  
                    inner join banque bq on (bq.id=bc.banque_id) " . $inner_user . "
                    where i.supprimer = 0 
                    and (r.libelle like '%CHQ%' or r.libelle like '%CHEQUE%')
                    and ROUND(r.credit-r.debit < 0)
                    and not (r.ecriture_change = 1 and r.maj = 3)
                    and r.image_flague_id is null
                    and sep.souscategorie_id IS NOT NULL 
                    and ssc.id = 10 
                    and (r.cle_dossier_id is null or cld.pas_piece is null)";

        $query .= " and i.exercice = " .$param['exercice'];
        $query .= " and c.status = 1
                    and r.operateur_id is null";
        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " and (d.status = 1";
        $query .= " or ( d.status <> 1 
                    and d.status_debut is not null 
                    and d.status_debut > " . $param['exercice'] . " ))";

        $query .= $by_user;

        $query .= " group by d.id, bc.numcompte";

        $prep = $pdo->prepare($query);
        $prep->execute();
        $resultat = $prep->fetchAll();

        return $resultat;
    
    }

    /**
     * Chèques inconnus groupé par dossier
     *
     * @param array $param
     *
     * @return array
     */
    public function getChequeIconnuByDossier($param,$user)
    {
           
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select count(i.id) as nb, d.nom as nom_dossier, d.cloture
                    from image i
                    left join releve r on (i.id = r.image_id)
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
                    inner join banque bq on (bq.id=bc.banque_id) " . $inner_user . "
                    where i.supprimer = 0 
                    and (r.libelle like '%CHQ%' or r.libelle like '%CHEQUE%')
                    and r.credit-r.debit < 0";

        $query .= " and i.exercice = " .$param['exercice'];
        $query .= " and c.status = 1
                    and r.operateur_id is null";
        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " and (d.status = 1";
        $query .= " or ( d.status <> 1 
                    and d.status_debut is not null 
                    and d.status_debut > " . $param['exercice'] . " ))";

        $query .= $by_user;

        $query .= " group by d.id";

        $prep = $pdo->prepare($query);
        $prep->execute();
        $resultat = $prep->fetchAll();

        return $resultat;
    
    }

    /**
     * Dossiers actifs avec nombres des pièces
     *
     * @param array $param
     *
     * @return array
     */
    public function getDossiersTotaux($param,$user)
    {
        
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select count(i.id) as nb, d.nom as nom_dossier, d.cloture
                    from dossier d
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id) " . $inner_user . "
                    left join lot l on (l.dossier_id = d.id)
                    left join image i on (l.id = i.lot_id and i.exercice = " . $param['exercice'] . ")";

        $query .= " where c.status = 1
                    and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " AND (d.status = 1";
        $query .= " OR ( d.status <> 1 
                    AND d.status_debut IS NOT NULL 
                    AND d.status_debut > " . $param['exercice'] . " ))" . $by_user . " 
                    group by d.nom";

        $prep  = $pdo->prepare($query);

        $prep->execute();

        $result = $prep->fetchAll();

        return $result;
    
    }

    /**
     * Imputés Travaux bancaire en cours
     *
     * @param array $param
     *
     * @return array
     */
    public function getImputes($param,$user)
    {
           
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select count(*) as nb_imputes, d.id, bc.numcompte, d.nom as dossierNom, d.cloture
                    from image i
                    left join releve r on (i.id = r.image_id)
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
                    inner join separation sep on (sep.image_id = i.id)  
                    inner join souscategorie ssc on (sep.souscategorie_id = ssc.id) 
                    inner join banque bq on (bq.id=bc.banque_id)" . $inner_user . "
                    where i.supprimer = 0 
                    and r.image_flague_id is null
                    and sep.souscategorie_id IS NOT NULL
                    and (r.cle_dossier_id is not null)
                    and ssc.id = 10";

        $query .= " and i.exercice = " .$param['exercice'];

        $query .= " and c.status = 1
                    and r.operateur_id is null";

        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " and (d.status = 1";
        $query .= " or ( d.status <> 1 
                    and d.status_debut is not null 
                    and d.status_debut > " . $param['exercice'] . " ))
                    group by d.id, bc.numcompte";

        $query .= $by_user;

        $prep = $pdo->prepare($query);

        $prep->execute();

        return $prep->fetchAll();
    }

    /**
     * Pièces manquantes Travaux bancaires en cours
     */
    public function getPiecesManquantes($param,$user)
    {
           
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select count(*) as nb_pieces_manquantes, d.nom as nom_dossier, d.cloture
                    from image i
                    left join releve r on (i.id = r.image_id)
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
                    inner join banque bq on (bq.id=bc.banque_id)
                    inner join cle cle on (cle.id = r.cle_dossier_id) " . $inner_user . "
                    where (r.image_flague_id is null or (r.cle_dossier_id is not null and cle.pas_piece = 1))
                    and i.supprimer = 0";

        $query .= " and i.exercice = " .$param['exercice'];

        $query .= " and c.status = 1
                    and r.operateur_id is null";

        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " and (d.status = 1";
        $query .= " or ( d.status <> 1 
                    and d.status_debut is not null 
                    and d.status_debut > " . $param['exercice'] . " ))";

        $query .= $by_user;

        $query .= " group by bc.numcompte";

        $prep = $pdo->prepare($query);
        $prep->execute();

        $resultat = $prep->fetchAll();

        return $resultat;
     
    }

    public function getAValider($param,$user)
    {
        
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select count(*) as nb_a_valider
                    from image i
                    left join releve r on (i.id = r.image_id)
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
                    inner join banque bq on (bq.id=bc.banque_id)
                    inner join separation sep on (i.id = sep.image_id)
                    inner join souscategorie scat on (scat.id = sep.souscategorie_id)" . $inner_user . "
                    where sep.souscategorie_id = 10
                    and i.ctrl_saisie >= 2
                    and i.valider <> 100
                    ";

        $query .= " and i.exercice = " .$param['exercice'];

        $query .= " and c.status = 1
                    and r.operateur_id is null";

        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " and (d.status = 1";
        $query .= " or ( d.status <> 1 
                    and d.status_debut is not null 
                    and d.status_debut > " . $param['exercice'] . " ))";

        $query .= $by_user;

        $prep = $pdo->prepare($query);

        $prep->execute();

        $resultat = $prep->fetchAll();

        return $resultat;
    
    }

    public function getEnCours($param,$user)
    {
        
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select count(*) as nb_en_cours, d.id, bc.numcompte, d.nom as dossierNom, d.cloture
                    from image i
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.dossier_id = d.id)
                    inner join separation sep on (i.id = sep.image_id)
                    inner join souscategorie scat on (scat.id = sep.souscategorie_id)" . $inner_user . "
                    and i.ctrl_saisie < 2";

        $query .= " and i.exercice = " .$param['exercice'];

        $query .= " and c.status = 1";

        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " and (d.status = 1";
        $query .= " or ( d.status <> 1 
                    and d.status_debut is not null 
                    and d.status_debut > " . $param['exercice'] . " ))
                    group by d.id, bc.numcompte";

        $query .= $by_user;

        $prep = $pdo->prepare($query);
        $prep->execute();

        return $prep->fetchAll();
    
    }

    public function _getEnCours($param)
    {
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);

        $query = "  select count(*) as nb_en_cours
                    from image i
                    left join releve r on (i.id = r.image_id)
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
                    inner join banque bq on (bq.id=bc.banque_id)
                    inner join separation sep on (i.id = sep.image_id)
                    inner join souscategorie scat on (scat.id = sep.souscategorie_id)
                    where sep.souscategorie_id = 10
                    and i.ctrl_saisie < 2
                    ";

        $query .= " and i.exercice = " .$param['exercice'];

        $query .= " and c.status = 1
                    and r.operateur_id is null";

        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " and (d.status = 1";
        $query .= " or ( d.status <> 1 
                    and d.status_debut is not null 
                    and d.status_debut > " . $param['exercice'] . " ))";

        $query .= " group by bc.numcompte";

        $prep = $pdo->prepare($query);

        $prep->execute();

        $resultat = $prep->fetchAll();

        if (!empty($resultat)) {
            return $resultat[0]->nb_en_cours;
        } else{
            return 0;
        }
    }

    public function getLettrees($param,$user)
    {

        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select count(*) as nb_lettrees, d.id, bc.numcompte, d.nom as dossierNom, d.cloture
                    from image i
                    left join releve r on (i.id = r.image_id)
                    inner join lot l on (l.id = i.lot_id)
                    inner join dossier d on (d.id = l.dossier_id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)
                    inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
                    inner join separation sep on (sep.image_id = i.id)  
                    inner join souscategorie ssc on (sep.souscategorie_id = ssc.id) 
                    inner join banque bq on (bq.id=bc.banque_id) " . $inner_user . "
                    where i.supprimer = 0 
                    and sep.souscategorie_id IS NOT NULL 
                    AND ((r.image_flague_id IN (SELECT bsca.image_flague_id FROM banque_sous_categorie_autre bsca  where bsca.compte_tiers_id is not null or bsca.compte_bilan_id is not null or bsca.compte_tva_id is not null or bsca.compte_chg_id is not null))
                    OR  (r.image_flague_id IN (SELECT tic.image_flague_id FROM tva_imputation_controle tic where tic.tiers_id is not null or tic.pcc_bilan_id is not null or tic.pcc_tva_id is not null))
                    OR (r.image_flague_id IN (SELECT rle.image_flague_id FROM releve rle where rle.operateur_id IS NULL and rle.id <> r.id)))
                    and ssc.id = 10 
                    and r.image_flague_id is not null";

        $query .= " and i.exercice = " .$param['exercice'];

        $query .= " and c.status = 1
                    and r.operateur_id is null";

        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }

        $query .= " and (d.status = 1";
        $query .= " or ( d.status <> 1 
                    and d.status_debut is not null 
                    and d.status_debut > " . $param['exercice'] . " ))
                    group by d.id, bc.numcompte";

        $query .= $by_user;

        $prep = $pdo->prepare($query);
        $prep->execute();

        return $prep->fetchAll();

    }


    public function getTBEC($param,$user)
    {
        $resultat = array(
            'nb_lettrees' => $this->getLettrees($param,$user),
            'nb_imputes' => $this->getImputes($param,$user),
            'nb_en_cours' => $this->getEnCours($param,$user),
            'nb_ecriture_change'=>$this->getEcritureChange($param, $user)
        );

        return $resultat;
    }

    public function getRemise($param)
    {
        
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);
        $param['dossier'] = Boost::deboost($param['dossier'],$this);

        $query = "  SELECT SC.date_reglement, SC.date_facture, SC.date_echeance, D.nom as nom_dossier, BC.numcompte, D.cloture, I.exercice, SEP.souscategorie_id, I.id as nb
                    FROM image I
                    INNER JOIN lot L ON (I.lot_id = L.id)
                    INNER JOIN dossier D ON (L.dossier_id = D.id)
                    INNER JOIN banque_compte BC ON (BC.dossier_id = D.id)
                    INNER JOIN site S ON (D.site_id = S.id)
                    INNER JOIN client C ON (S.client_id = C.id)
                    INNER JOIN separation SEP ON (I.id = SEP.image_id)
                    INNER JOIN saisie_controle SC ON (I.id = SC.image_id)
                    WHERE I.exercice = " . $param['exercice'];

        $query .= " AND C.status = 1";

        $query .= " AND BC.numcompte <> ''";

        $query .= " AND C.id = " .$param['client'];

        if ($param['dossier'] != 0) {
            $query .= " AND D.id = " . $param['dossier'];
        }

        $query .= " AND (D.status = 1";
        $query .= " OR ( D.status <> 1 
                    AND D.status_debut IS NOT NULL 
                    AND D.status_debut > " . $param['exercice'] . " ))";

        $query .= " AND SEP.souscategorie_id = 7
                    AND SC.date_reglement IS NULL OR SC.date_reglement = ''
                    GROUP BY D.id 
                    LIMIT 1000000";

        $prep = $pdo->prepare($query);

        $prep->execute();

        $resultat = $prep->fetchAll();

        return $resultat; 

    
    }

    public function getRepartitions($param,$user)
    {

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $param['client'] = Boost::deboost($param['client'],$this);

        $param['site'] = Boost::deboost($param['site'],$this);

        $and = "";

        if ($param['site'] != 0) {

            $site = intval($param['site']);

            $and .= " and S.id = ${site}";
            // $and .= " and I.exercice > 2010";
            // var_dump("expression");die();
        }


        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " AND UD.utilisateur_id = " . $user->getId();
            $inner_user = " INNER JOIN utilisateur_dossier UD ON (UD.dossier_id = D.id)";
        }

        /*Répartition par Client*/
        if ($param['client'] == 0) {
            $query = "SELECT count(I.id) as y, C.nom as name
                    FROM image I
                    INNER JOIN lot L ON(I.lot_id=L.id)
                    INNER JOIN dossier D ON(L.dossier_id = D.id)
                    INNER JOIN site S ON (D.site_id = S.id)
                    INNER JOIN client C ON (S.client_id = C.id) " . $inner_user . "
                    WHERE I.exercice = " . $param['exercice'];
            
            $query .= " AND (D.status = 1 
                        OR( D.status <> 1
                        AND D.status IS NOT NULL
                        AND D.status_debut > " . $param['exercice'] ." ))";
            $query .= " AND C.status = 1" . $by_user;
            $query .= " GROUP BY C.id";
        }
      
        /*Répartition par dossier du client séléctionné*/
        else{
            $query = "SELECT count(I.id) as y, D.nom as name, C.nom as client
                    FROM image I
                    INNER JOIN lot L ON(I.lot_id=L.id)
                    INNER JOIN dossier D ON(L.dossier_id = D.id)
                    INNER JOIN site S ON (D.site_id = S.id)
                    INNER JOIN client C ON (S.client_id = C.id)" . $inner_user . "
                    WHERE I.exercice = " . $param['exercice'];

            $query .= $and;
            
            $query .= " AND (D.status = 1 
                        OR( D.status <> 1
                        AND D.status IS NOT NULL
                        AND D.status_debut > " . $param['exercice'] ." ))";
            $query .= " AND C.status = 1";
            $query .= " AND C.id = " . $param['client'] . $by_user;
            $query .= " GROUP BY D.nom";
        }

        $prep = $pdo->prepare($query);
        $prep->execute();

        $resultat = $prep->fetchAll();

        return $resultat; 

    }

    public function getImagesRecues($param, $user, $deboost = true)
    {


        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        if ($deboost === true) {
            $param['client'] = Boost::deboost($param['client'],$this);
            if ($param['dossier'] !== '0') {
                $param['dossier'] = Boost::deboost($param['dossier'],$this);
            }
            if ($param['site'] !== '0') {
                $param['site'] = Boost::deboost($param['site'],$this);
            }
        }

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " AND UD.utilisateur_id = " . $user->getId();
            $inner_user = " INNER JOIN utilisateur_dossier UD ON (UD.dossier_id = D.id)";
        } else {
        }

        $and = "";

        /*Client différent de Tous*/
        if ($param['client'] !== '0') {
            $and .= " C.id = " . $param['client'] . " AND";
            if ($param['dossier'] !== '0') { 
                $and .= " D.id = " .$param['dossier'] . " AND";
            }
        }

        /*Client égale à Tous*/
        else{ 
            if ($param['dossier'] !== '0') {
                $and .= " D.id = " .$param['dossier'] . " AND";
            }
        }

        $and .= " I.exercice = " .$param['exercice'];


        if ($param['site'] !== '0') {

            $site = intval($param['site']);

            $and .= " and S.id = ${site}";
            // $and .= " and I.exercice > 2010";
            // var_dump("expression");die();
        }

        $and .= "   AND (
                        (D.status = 1 and D.active = 1)
                        OR (D.status <> 1 
                            AND D.status is not null 
                            AND D.status_debut > " . $param['exercice'] . " 
                            AND D.active = 1
                        )
                    )";



        /*Filtre par date pièce*/
        if ($param['typedate'] == 2) {

          $inner = "";
          $as = "";
          $having = " HAVING date_piece IS NOT NULL";

          if ($param['cas'] == 1) {
            $having .= " AND date_format(date_piece,'%Y-%m') = :periode";
          }else{
            if ($param['cas'] !==5) {
                $having .= " AND date_format(date_piece,'%Y-%m') >= :dateDeb";
                $having .= " AND date_format(date_piece,'%Y-%m') <= :dateFin";
            }
          }

          $as = ", date_format(IF(I.ctrl_imputation >= 2,IFNULL(ic.date_facture,ic.periode_d1),IF(I.imputation >= 2, IFNULL(im.date_facture,im.periode_d1),IF(I.ctrl_saisie >= 2, IFNULL(sc.date_facture,sc.periode_d1), IF(I.saisie2 >= 2, IFNULL(s2.date_facture,s2.periode_d1),IF(I.saisie1 >= 2, IFNULL(s1.date_facture,s1.periode_d1),null))))), '%Y-%m') as date_piece, D.date_cloture, D.debut_activite, '' as isnull"; 

          $inner  = " LEFT JOIN imputation_controle ic ON (ic.image_id = I.id)";
          $inner .= " LEFT JOIN imputation im ON (im.image_id = I.id)";
          $inner .= " LEFT JOIN saisie_controle sc ON (sc.image_id = I.id)";
          $inner .= " LEFT JOIN saisie2 s2 ON (s2.image_id = I.id)";
          $inner .= " LEFT JOIN saisie1 s1 ON (s1.image_id = I.id)";



          $query = "SELECT count(I.id) as nb, D.cloture, C.nom as client, D.nom as dossier, date_format(L.date_scan,'%Y-%m') as date_scan, L.id as lot, D.id as dossier_id, C.id as client_id ".$as."
                    FROM image I
                    INNER JOIN lot L ON(I.lot_id=L.id) ".$inner."
                    INNER JOIN dossier D ON(L.dossier_id = D.id)
                    INNER JOIN site S ON (D.site_id = S.id)
                    INNER JOIN client C ON (S.client_id = C.id)". $inner_user ."
                    WHERE C.status = 1 AND" . $and . $by_user;

          $query .= " GROUP BY I.id ". $having ."  ORDER BY D.nom ASC";

        }

        /*Filtre date envoi*/
        else{

          $queryScan = "";

          if ($param['cas'] == 1) {
              $queryScan = " AND L.date_scan = :periode";
          }else{
            if ($param['cas'] !==5) {
                $queryScan .= " AND L.date_scan >= :dateDeb";
                $queryScan .= " AND L.date_scan <= :dateFin";
            }
          }

          

          $query = "SELECT count(I.id) as nb, D.cloture, C.nom as client, D.nom as dossier, date_format(L.date_scan,'%Y-%m') as date_scan, L.id as lot, D.id as dossier_id, C.id as client_id, '' as isnull
                    FROM image I
                    INNER JOIN lot L ON(I.lot_id=L.id)
                    INNER JOIN dossier D ON(L.dossier_id = D.id)
                    INNER JOIN site S ON (D.site_id = S.id)
                    INNER JOIN client C ON (S.client_id = C.id)". $inner_user ."
                    WHERE C.status = 1 AND " . $and . $queryScan . $by_user ;

          $query .= " GROUP BY L.id ORDER BY D.nom ASC";
        }

        $prep = $pdo->prepare($query);


        switch ($param['cas']) {
            case 1:
                $now = $param['aujourdhui'];
                $prep->execute(array(
                    'periode' => $now,
                ));
                break;
                case 5:
            $prep->execute();
            break;
            default:
                if (isset($param['dateFin']) && isset($param['dateFin'])){
                  $dateDeb = $param['dateDeb'];
                  $dateFin = $param['dateFin'];
                    $prep->execute(array(
                        'dateDeb' => $dateDeb,
                        'dateFin' => $dateFin,
                    ));
                } else {
                    $prep->execute();
                }
        }

        $resultat = $prep->fetchAll();

        return $resultat;  


    }

    function compIntitule($a, $b){
        return strcmp($a['tiers_intitule'], $b['tiers_intitule']);
    }

    /**
     * Maka ny Info an'ny Images avy any @ table saisie, controle, imputation
     * @param $imageId
     * @return array
     */
    public function getInfosImageByImageId($imageId)
    {
        $qb = $this->createQueryBuilder('i');

        $qb->where('i.id= :imageId')
            ->setParameter('imageId', $imageId)
            ->select('i.id', 'i.saisie1', 'i.saisie2', 'i.ctrlSaisie', 'i.imputation', 'i.ctrlImputation');

        $images = $qb->getQuery()->getResult();

        $res = null;
        $resSaisie = null;
        $resTva = null;
        $resTable = '';
        $resNdf = null;
        $resReleve = null;
        $resSeparation = null;
        $resBanqueSousCategorieAutre = null;
        $resCegj = null;

        if (null !== $images) {
            foreach ($images as $img) {

                $resSeparations = $this->getEntityManager()
                    ->createQuery('SELECT s FROM AppBundle:Separation s WHERE s.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                if(count($resSeparations) > 0){
                    $resSeparation = $resSeparations[0];
                }

                if ($img['ctrlImputation'] > 1) {

                    $resSaisie = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:ImputationControle t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resTva = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:TvaImputationControle t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resCegj = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:ImputationControleCegj t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resTable = 'Controle Imputation';

                    if (count($resSaisie) > 0) {
                        /** @var  $saisie ImputationControle */
                        $saisie = $resSaisie[0];
                        if (null !== $saisie->getSoussouscategorie()) {
                            //Raha note de frais (11) ilay categorie
                            if ($saisie->getSoussouscategorie()->getSouscategorie()->getCategorie()->getCode() === 'CODE_NDF') {
                                $resNdf = $this->getEntityManager()
                                    ->createQuery('SELECT t FROM AppBundle:ImputationControleNoteFrais t WHERE t.image = :image_id')
                                    ->setParameter('image_id', $img['id'])
                                    ->getResult();
                            }
                        }

                        elseif (null !== $saisie->getSouscategorie()){
                            //Raha note de frais (11) ilay categorie
                            if ($saisie->getSouscategorie()->getCategorie()->getCode() === 'CODE_NDF') {
                                $resNdf = $this->getEntityManager()
                                    ->createQuery('SELECT t FROM AppBundle:ImputationControleNoteFrais t WHERE t.image = :image_id')
                                    ->setParameter('image_id', $img['id'])
                                    ->getResult();
                            }
                        }
                    }

                } else if ($img['imputation'] > 1) {
                    $resSaisie = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:Imputation t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resTva = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:TvaImputation t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resCegj = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:ImputationCegj t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resTable = 'Imputation';


                    if (null !== $resSaisie) {
                        /** @var  $saisie Imputation */
                        $saisie = $resSaisie[0];
                        if (!is_null($saisie->getSoussouscategorie())) {
                            //Raha note de frais (11) ilay categorie
                            if ($saisie->getSoussouscategorie()->getSouscategorie()->getCategorie()->getCode() === 'CODE_NDF') {
                                $resNdf = $this->getEntityManager()
                                    ->createQuery('SELECT t FROM AppBundle:ImputationNoteFrais t WHERE t.image = :image_id')
                                    ->setParameter('image_id', $img['id'])
                                    ->getResult();
                            }
                        }
                        else if(null !== $saisie->getSouscategorie()){
                            //Raha note de frais (11) ilay categorie
                            if ($saisie->getSouscategorie()->getCategorie()->getCode() === 'CODE_NDF') {
                                $resNdf = $this->getEntityManager()
                                    ->createQuery('SELECT t FROM AppBundle:ImputationNoteFrais t WHERE t.image = :image_id')
                                    ->setParameter('image_id', $img['id'])
                                    ->getResult();
                            }
                        }
                    }

                } else if ($img['ctrlSaisie'] > 1) {
                    $resSaisie = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:SaisieControle t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resTva = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:TvaSaisieControle t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resCegj = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:ControleCegj t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resTable = 'Controle Saisie';

                    if (null !== $resSaisie) {
                        /** @var  $saisie Saisie1 */
                        $saisie = $resSaisie[0];
                        if (null !== $saisie->getSoussouscategorie()) {
                            //Raha note de frais (11) ilay categorie
                            if ($saisie->getSoussouscategorie()->getSouscategorie()->getCategorie()->getCode() === 'CODE_NDF') {
                                $resNdf = $this->getEntityManager()
                                    ->createQuery('SELECT t FROM AppBundle:ControleNoteFrais t WHERE t.image = :image_id')
                                    ->setParameter('image_id', $img['id'])
                                    ->getResult();
                            }
                        }
                    }

                } else if ($img['saisie2'] > 1) {
                    $resSaisie = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:Saisie2 t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resTva = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:TvaSaisie2 t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resCegj = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:Saisie2Cegj t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resTable = 'Saisie 2';

                    if (count($resSaisie) > 0) {
                        /** @var  $saisie Saisie1 */
                        $saisie = $resSaisie[0];
                        if (null !== $saisie->getSoussouscategorie()) {
                            //Raha note de frais (11) ilay categorie
                            if ($saisie->getSoussouscategorie()->getSouscategorie()->getCategorie()->getCode() === 'CODE_NDF') {
                                $resNdf = $this->getEntityManager()
                                    ->createQuery('SELECT t FROM AppBundle:Saisie2NoteFrais t WHERE t.image = :image_id')
                                    ->setParameter('image_id', $img['id'])
                                    ->getResult();
                            }
                        }
                    }

                } else if ($img['saisie1'] > 1) {

                    $resSaisie = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:Saisie1 t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resTva = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:TvaSaisie1 t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resCegj = $this->getEntityManager()
                        ->createQuery('SELECT t FROM AppBundle:Saisie1Cegj t WHERE t.image = :image_id')
                        ->setParameter('image_id', $img['id'])
                        ->getResult();

                    $resTable = 'Saisie 1';

                    if (count($resSaisie) > 0) {
                        /** @var  $saisie Saisie1 */
                        $saisie = $resSaisie[0];
                        if (null !== $saisie->getSoussouscategorie()) {
                            $codeCategorie = $saisie->getSoussouscategorie()->getSouscategorie()->getCategorie()->getCode();

                            //Raha note de frais (11) ilay categorie
                            if ($codeCategorie === 'CODE_NDF') {
                                $resNdf = $this->getEntityManager()
                                    ->createQuery('SELECT t FROM AppBundle:Saisie1NoteFrais t WHERE t.image = :image_id')
                                    ->setParameter('image_id', $img['id'])
                                    ->getResult();
                            }
                        }
                    }
                }

                //Raha efa any anaty table saisie
                if (count($resSaisie) > 0) {

                    /** @var $saisie Saisie1 */
                    $saisie = $resSaisie[0];

                    if (null !== $saisie->getSoussouscategorie()) {
                        //Raha banque (16) ilay categorie
                        if ($saisie->getSoussouscategorie()->getSouscategorie()->getCategorie()->getCode() === 'CODE_BANQUE') {
                            //Raha releve bancaire ny sous categorie dia any @ releve no maka ny données
                            if ($saisie->getSoussouscategorie()->getSouscategorie()->getId() == 10) {
                                $resReleve = $this->getEntityManager()
                                    ->createQuery('SELECT t FROM AppBundle:Releve t WHERE t.image = :image_id')
                                    ->setParameter('image_id', $img['id'])
                                    ->getResult();
                            } //Raha tsy releve ny sous categorie dia any @  banque_sous_categorie_autre no maka ny données
                            else {
                                $resBanqueSousCategorieAutre = $this->getEntityManager()
                                    ->createQuery('SELECT t FROM AppBundle:Releve t WHERE t.image = :image_id')
                                    ->setParameter('image_id', $img['id'])
                                    ->getResult();
                            }
                        }
                    }

                    $res = array(
                        'saisie' => $resSaisie,
                        'tva' => $resTva,
                        'tableSaisie' => $resTable,
                        'ndf' => $resNdf,
                        'releve' => $resReleve,
                        'banque_sous_categorie_autre' => $resBanqueSousCategorieAutre,
                        'cegj' => $resCegj,
                        'separation'=> $resSeparation
                    );
                }
            }
        }

        return $res;
    }


    /**
     * Maka ny liste-ny exercice rehetra
     * @return array
     */
    public function getListeExercice()
    {
        $exercices = $this
            ->createQueryBuilder('i')
            ->select('i.exercice')
            ->distinct()
            ->addOrderBy('i.exercice')
            ->getQuery()
            ->getResult();

        return $exercices;
    }


    /**
     * Maka ny liste-ny Images par Dossier
     * @param $dossier
     * @param $exercice
     * @return array
     */
    public function getListeImageByDossier($dossier, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->where('lot.dossier = :the_dossier')
            ->setParameter('the_dossier', $dossier)
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        return $images;
    }
    /**
     * Maka ny liste-ny images par categorie
     * @param $dossier
     * @param $exercice
     * @param $categorieId
     * @param $sousCategorieId
     * @param $sousSousCategorieId
     * @return array
     */
//    public function getListeImageByDossierCategorie($dossier, $exercice, $categorieId, $sousCategorieId, $sousSousCategorieId)
//    {
//
//        $images = $this
//            ->createQueryBuilder('i')
//            ->leftJoin('i.lot', 'lot')
//            ->leftJoin('lot.dossier', 'dossier')
//            ->where('lot.dossier = :the_dossier')
//            ->setParameter('the_dossier', $dossier)
//            ->andWhere('i.saisie1 > 0 OR i.saisie2 > 0')
//            ->andWhere('i.exercice = :exercice')
//            ->setParameter(':exercice', $exercice)
//            ->getQuery()
//            ->getResult();
//
//
//        $listeImages = array();
//
//        if ($categorieId == -1) {
//            $listeImages = $images;
//            return $listeImages;
//        }
//
//        $estSaisie = false;
//
//        if ($images != null) {
//            foreach ($images as $img) {
//                /**@var $img Image */
//                if ($img->getCtrlImputation() != 0) {
//
//                    $resSaisie = $this->getEntityManager()
//                        ->createQuery('SELECT t FROM AppBundle:ImputationControle t WHERE t.image = :image_id')
//                        ->setParameter('image_id', $img->getId())
//                        ->getResult();
//                } else if ($img->getImputation() != 0) {
//                    $resSaisie = $this->getEntityManager()
//                        ->createQuery('SELECT t FROM AppBundle:Imputation t WHERE t.image = :image_id')
//                        ->setParameter('image_id', $img->getId())
//                        ->getResult();
//
//                } else if ($img->getCtrlSaisie() != 0) {
//                    $resSaisie = $this->getEntityManager()
//                        ->createQuery('SELECT t FROM AppBundle:SaisieControle t WHERE t.image = :image_id')
//                        ->setParameter('image_id', $img->getId())
//                        ->getResult();
//
//
//                } else if ($img->getSaisie2() != 0) {
//                    $resSaisie = $this->getEntityManager()
//                        ->createQuery('SELECT t FROM AppBundle:Saisie2 t WHERE t.image = :image_id')
//                        ->setParameter('image_id', $img->getId())
//                        ->getResult();
//
//                } else if ($img->getSaisie1() != 0) {
//
//                    $resSaisie = $this->getEntityManager()
//                        ->createQuery('SELECT t FROM AppBundle:Saisie1 t WHERE t.image = :image_id')
//                        ->setParameter('image_id', $img->getId())
//                        ->getResult();
//
//                }
//
//                //efa any @ table saisie
//
//                if ($resSaisie != null) {
//                    /** @var  $res ImputationControle */
//                    $res = $resSaisie[0];
//                    if ($sousSousCategorieId > 0) {
//
//                        if ($res->getSoussouscategorie() != null) {
//                            if ($res->getSoussouscategorie()->getId() == $sousSousCategorieId) {
//                                $listeImages[] = $img;
//                            }
//                        }
//
//                    } else if ($sousCategorieId > 0) {
//                        if ($res->getSoussouscategorie() != null) {
//                            if ($res->getSoussouscategorie()->getSouscategorie()->getId() == $sousCategorieId) {
//                                $listeImages[] = $img;
//                            }
//                        }
//
//                    } else if ($categorieId > 0) {
//                        if ($res->getSoussouscategorie() != null) {
//                            if ($res->getSoussouscategorie()->getSouscategorie()->getCategorie()->getId() == $categorieId) {
//                                $listeImages[] = $img;
//                            }
//                        }
//
//                    }
//                }
//            }
//        }
//        return $listeImages;
//    }


    /**
     * Maka ny liste-ny images par categorie
     * @param $dossier
     * @param $exercice
     * @param $categorieId
     * @param $sousCategorieId
     * @param $sousSousCategorieId
     * @return array
     */
    public function getListeImageByDossierCategorieV2($dossier, $exercice, $categorieId, $sousCategorieId, $sousSousCategorieId, $dateScanSearch, $dateDebut, $dateFin)
    {
        $qb = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
           ->innerJoin('AppBundle:Separation', 'sep', 'WITH', 'sep.image = i')
           ->leftJoin('AppBundle:Soussouscategorie', 'ssc', 'WITH', 'sep.soussouscategorie = ssc')
            ->where('lot.dossier = :the_dossier')
            ->setParameter('the_dossier', $dossier)
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.exercice = :exercice OR ssc.multiExercice = 1')
            ->andWhere('i.supprimer = 0')
            ->andWhere('i.decouper = 0')
            ->setParameter(':exercice', $exercice);
        if($dateScanSearch) {
            if($dateDebut !== '' && $dateFin !== ''){
                $qb ->andWhere('lot.dateScan >= :dateDebut')
                    ->setParameter(':dateDebut', $dateDebut)
                    ->andWhere('lot.dateScan <= :dateFin')
                    ->setParameter(':dateFin', $dateFin);
            }
        }

        if(intval($sousSousCategorieId) !== -1){
            $sousSousCategorie = $this->getEntityManager()
                ->getRepository('AppBundle:Soussouscategorie')
                ->find($sousSousCategorieId);

            $qb->andWhere('sep.soussouscategorie = :soussouscategorie')
                ->setParameter('soussouscategorie', $sousSousCategorie);
        }
        else if(intval($sousCategorieId) !== -1){
            $sousCategorie = $this->getEntityManager()
                ->getRepository('AppBundle:Souscategorie')
                ->find($sousCategorieId);
            $qb->andWhere('sep.souscategorie = :souscategorie')
                ->setParameter('souscategorie', $sousCategorie);
        }
        else if(intval($categorieId) !== -1) {
            $categorie = $this->getEntityManager()
                ->getRepository('AppBundle:Categorie')
                ->find($categorieId);

            $qb->andWhere('sep.categorie = :categorie')
                ->setParameter('categorie', $categorie);
        }

        return $qb->getQuery()->getResult();


    }

    /**
     * Maka ny liste-ny images par dossier, par periode
     * @param $dossier
     * @param $exercice
     * @param $dateDebut
     * @param $dateFin
     * @return array
     */
    public function getListeImageByDossierPeriode($dossier, $exercice, $dateDebut, $dateFin)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->where('lot.dossier = :the_dossier')
            ->setParameter('the_dossier', $dossier)
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();


        $listeImages = array();

        if (!is_null($images)) {
            foreach ($images as $img) {
                /**@var $img Image */
                if ($img->getCtrlImputation() != 0) {

                    if($dateDebut !== '' && $dateFin !== '') {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:ImputationControle t WHERE t.image = :image_id AND t.dateFacture >= :dateDebut AND t.dateFacture <= :dateFin')
                            ->setParameter('image_id', $img->getId())
                            ->setParameter('dateDebut', $dateDebut)
                            ->setParameter('dateFin', $dateFin)
                            ->getResult();
                    }
                    else{
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:ImputationControle t WHERE t.image = :image_id ')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }
                } else if ($img->getImputation() != 0) {
                    if($dateDebut !== '' && $dateFin !== '') {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Imputation t WHERE t.image = :image_id AND t.dateFacture >= :dateDebut AND t.dateFacture <= :dateFin')
                            ->setParameter('image_id', $img->getId())
                            ->setParameter('dateDebut', $dateDebut)
                            ->setParameter('dateFin', $dateFin)
                            ->getResult();
                    }
                    else{
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Imputation t WHERE t.image = :image_id ')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }

                } else if ($img->getCtrlSaisie() != 0) {
                    if($dateDebut !== '' && $dateFin !== '') {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:SaisieControle t WHERE t.image = :image_id AND t.dateFacture >= :dateDebut AND t.dateFacture <= :dateFin')
                            ->setParameter('image_id', $img->getId())
                            ->setParameter('dateDebut', $dateDebut)
                            ->setParameter('dateFin', $dateFin)
                            ->getResult();
                    }
                    else{
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:SaisieControle t WHERE t.image = :image_id ')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }


                } else if ($img->getSaisie2() != 0) {
                    if($dateDebut !== '' && $dateFin !== '') {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Saisie2 t WHERE t.image = :image_id AND t.dateFacture >= :dateDebut AND t.dateFacture <= :dateFin')
                            ->setParameter('image_id', $img->getId())
                            ->setParameter('dateDebut', $dateDebut)
                            ->setParameter('dateFin', $dateFin)
                            ->getResult();
                    }
                    else{
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Saisie2 t WHERE t.image = :image_id ')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }

                } else if ($img->getSaisie1() != 0) {
                    if($dateDebut !== '' && $dateFin !== '') {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Saisie1 t WHERE t.image = :image_id AND t.dateFacture >= :dateDebut AND t.dateFacture <= :dateFin')
                            ->setParameter('image_id', $img->getId())
                            ->setParameter('dateDebut', $dateDebut)
                            ->setParameter('dateFin', $dateFin)
                            ->getResult();
                    }
                    else{
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Saisie1 t WHERE t.image = :image_id ')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }

                }

                //efa any @ table saisie

                if (!is_null($resSaisie)) {
                    $listeImages[] = $img;
                }
            }
        }
        return $listeImages;
    }

    /**
     * Maka ny liste-ny Image par Dossier par Periode
     * @param $dossier
     * @param $dateDebut
     * @param $dateFin
     * @param $exercice
     * @return array
     */
    public function getListeImageByDossierDateScan($dossier, $dateDebut, $dateFin, $exercice)
    {
        $qb = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->andWhere('dossier.id = :dossier')
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.supprimer = 0')
            ->andWhere('i.exercice = :exercice')
            ->setParameter(':dossier', $dossier)
            ->setParameter(':exercice', $exercice);

        if ($dateFin !== '' && $dateDebut !== '') {
            $qb->andWhere('lot.dateScan >= :dateDebut')
                ->andWhere('lot.dateScan <= :dateFin')
                ->setParameter(':dateDebut', $dateDebut)
                ->setParameter(':dateFin', $dateFin);
        }

        $images = $qb->getQuery()
            ->getResult();

        return $images;
    }

    /**
     * Maka ny liste-ny Image par Dossier,nom image, exercice
     * @param $dossier
     * @param $nomImage
     * @param $exercice
     * @return array
     */
    public function getListeImageByDossierNomImage($dossier, $nomImage, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.nom LIKE :nomImage')
            ->andWhere('dossier.id = :dossier')
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':dossier', $dossier)
            ->setParameter(':exercice', $exercice)
            ->setParameter(':nomImage', $nomImage)
            ->getQuery()
            ->getResult();

        return $images;
    }



    public function getListeImageByDossierIdsNomImage($dossierIds, $nomImage, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->leftJoin('AppBundle:Separation', 'sep', 'WITH', 'sep.image = i')
            ->leftJoin('AppBundle:Soussouscategorie', 'ssc', 'WITH', 'sep.soussouscategorie = ssc')
            ->andWhere('i.exercice = :exercice  OR ssc.multiExercice = 1')
            ->andWhere('i.nom LIKE :nomImage')
            ->andWhere('dossier.id IN (:dossierIds)')
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':dossierIds', array_values($dossierIds))
            ->setParameter(':exercice', $exercice)
            ->setParameter(':nomImage', $nomImage)
            ->getQuery()
            ->getResult();

        return $images;
    }











    /**
     * Maka ny liste-ny Image par Client par Periode
     * @param $client
     * @param $dateDebut
     * @param $dateFin
     * @param $exercice
     * @return array
     */
    public function getListeImageByClientDateScan($client, $dateDebut, $dateFin, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->where('site.client = :client')
            ->andWhere('lot.dateScan >= :dateDebut')
            ->andWhere('lot.dateScan <= :dateFin')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':client', $client)
            ->setParameter(':dateDebut', $dateDebut)
            ->setParameter(':dateFin', $dateFin)
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        return $images;
    }

    /**
     * Maka ny liste-ny Image par Client,nom image, exercice
     * @param $client
     * @param $nomImage
     * @param $exercice
     * @return array
     */
    public function getListeImageByClientNomImage($client, $nomImage, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->innerJoin('site.client', 'client')
            ->where('client.id = :client')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.nom LIKE :nomImage')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':client', $client)
            ->setParameter(':exercice', $exercice)
            ->setParameter(':nomImage', $nomImage)
            ->getQuery()
            ->getResult();

        return $images;
    }


    /**
     * Maka ny liste-ny Images par Site
     * @param $site
     * @param $exercice
     * @return array
     */
    public function getListeImageBySite($site, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->where('site.id = :site')
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.supprimer = 0')
            ->andWhere('i.exercice = :exercice')
            ->setParameter(':exercice', $exercice)
            ->setParameter(':site', $site)
            ->getQuery()
            ->getResult();

        return $images;
    }

    /**
     * Maka ny liste-ny Image par Site par Periode
     * @param $site
     * @param $dateDebut
     * @param $dateFin
     * @param $exercice
     * @return array
     */
    public function getListeImageBySiteDateScan($site, $dateDebut, $dateFin, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->where('site.id = :site')
            ->andWhere('lot.dateScan >= :dateDebut')
            ->andWhere('lot.dateScan <= :dateFin')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':site', $site)
            ->setParameter(':dateDebut', $dateDebut)
            ->setParameter(':dateFin', $dateFin)
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        return $images;
    }

    /**
     * Maka ny liste-ny Image par Site,nom image, exercice
     * @param $site
     * @param $nomImage
     * @param $exercice
     * @return array
     */
    public function getListeImageBySiteNomImage($site, $nomImage, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->where('site.id = :site')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.nom LIKE :nomImage')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':site', $site)
            ->setParameter(':exercice', $exercice)
            ->setParameter(':nomImage', $nomImage)
            ->getQuery()
            ->getResult();

        return $images;
    }


    /**
     * Maka ny liste-ny images par utilisateur, categorie, dossier
     * @param $utilisateur
     * @param $categorieId
     * @param $dossier
     * @param $exercice
     * @return array
     */
    public function getListeImageByUtilisateurCategorieDossier($utilisateur, $categorieId, $dossier, $exercice)
    {
        $codecategorie = '';

        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->leftJoin('AppBundle:Separation', 'sep', 'WITH', 'sep.image = i')
            ->leftJoin('AppBundle:Soussouscategorie', 'ssc', 'WITH', 'sep.soussouscategorie = ssc')
            ->where('lot.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.supprimer = 0')
            ->andWhere('i.exercice = :exercice OR ssc.multiExercice = 1')
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        $listeImages = array();

        if ($categorieId == -1) {
            $listeImages = $images;
            return $listeImages;
        } else {

            $catEntity = $this->getEntityManager()
                ->getRepository('AppBundle:Categorie')
                ->find($categorieId);

            if($catEntity !== null){
                $codecategorie = $catEntity->getCode();
            }

            if (!is_null($images)) {
                foreach ($images as $img) {

                    $resSaisie = array();

                    /**@var $img Image */
                    if ($img->getCtrlImputation() != 0) {

                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:ImputationControle t WHERE t.image = :image_id')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    } else if ($img->getImputation() != 0) {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Imputation t WHERE t.image = :image_id')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();

                    } else if ($img->getCtrlSaisie() != 0) {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:SaisieControle t WHERE t.image = :image_id')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();


                    } else if ($img->getSaisie2() != 0) {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Saisie2 t WHERE t.image = :image_id')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();

                    } else if ($img->getSaisie1() != 0) {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Saisie1 t WHERE t.image = :image_id')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }

                    //efa any @ table saisie
                    if (count($resSaisie) > 0) {
                        /** @var  $res ImputationControle */
                        $res = $resSaisie[0];
                        if (!is_null($res->getSoussouscategorie())) {
                            if ($res->getSoussouscategorie()->getSouscategorie()->getCategorie()->getId() == $categorieId ||
                            $res->getSoussouscategorie()->getSouscategorie()->getCategorie()->getCode() == $codecategorie) {
                                $listeImages[] = $img;
                            }
                        }
                    }
                }
            }

            if ($dossier == -1) {
                return $listeImages;
            }

            $listeImagesFinal = array();

            /** @var  $image Image */
            foreach ($listeImages as $image) {
                if ($image->getLot()->getDossier()->getId() == $dossier) {
                    $listeImagesFinal[] = $image;
                }
            }

            $listeImages = $listeImagesFinal;

        }

        return $listeImages;
    }

    /**
     * Maka ny liste-ny images par utilisateur, dossier, periode
     * @param $utilisateur
     * @param $dossier
     * @param $exercice
     * @param $dateDebut
     * @param $dateFin
     * @return array
     */
    public function getListeImageByUtilisateurDossierPeriode($utilisateur, $dossier, $exercice, $dateDebut, $dateFin)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->where('lot.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->andWhere('i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.supprimer = 0')
            ->andWhere('i.exercice = :exercice')
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        $listeImages = array();


        if (!is_null($images)) {
            foreach ($images as $img) {
                $resSaisie = array();
                /**@var $img Image */
                if ($img->getCtrlImputation() > 1) {

                    if($dateDebut !== '' && $dateFin !== '') {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:ImputationControle t WHERE t.image = :image_id AND t.dateFacture >= :dateDebut AND t.dateFacture <= :dateFin')
                            ->setParameter('image_id', $img->getId())
                            ->setParameter('dateDebut', $dateDebut)
                            ->setParameter('dateFin', $dateFin)
                            ->getResult();
                    }
                    else{
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:ImputationControle t WHERE t.image = :image_id ')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }
                } else if ($img->getImputation() > 1) {

                    if($dateDebut !== '' && $dateFin !== '') {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Imputation t WHERE t.image = :image_id AND t.dateFacture >= :dateDebut AND t.dateFacture <= :dateFin')
                            ->setParameter('image_id', $img->getId())
                            ->setParameter('dateDebut', $dateDebut)
                            ->setParameter('dateFin', $dateFin)
                            ->getResult();
                    }
                    else{
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Imputation t WHERE t.image = :image_id ')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }

                } else if ($img->getCtrlSaisie() > 1) {
                    if($dateDebut !== '' && $dateFin !== '') {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:SaisieControle t WHERE t.image = :image_id AND t.dateFacture >= :dateDebut AND t.dateFacture <= :dateFin')
                            ->setParameter('image_id', $img->getId())
                            ->setParameter('dateDebut', $dateDebut)
                            ->setParameter('dateFin', $dateFin)
                            ->getResult();
                    }
                    else{
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:SaisieControle t WHERE t.image = :image_id ')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }


                } else if ($img->getSaisie2() > 1) {
                    if($dateDebut !== '' && $dateFin !== '') {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Saisie2 t WHERE t.image = :image_id AND t.dateFacture >= :dateDebut AND t.dateFacture <= :dateFin')
                            ->setParameter('image_id', $img->getId())
                            ->setParameter('dateDebut', $dateDebut)
                            ->setParameter('dateFin', $dateFin)
                            ->getResult();
                    }
                    else{
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Saisie2 t WHERE t.image = :image_id ')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }

                } else if ($img->getSaisie1() > 1) {
                    if($dateDebut !== '' && $dateFin !== '') {
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Saisie1 t WHERE t.image = :image_id AND t.dateFacture >= :dateDebut AND t.dateFacture <= :dateFin')
                            ->setParameter('image_id', $img->getId())
                            ->setParameter('dateDebut', $dateDebut)
                            ->setParameter('dateFin', $dateFin)
                            ->getResult();
                    }
                    else{
                        $resSaisie = $this->getEntityManager()
                            ->createQuery('SELECT t FROM AppBundle:Saisie1 t WHERE t.image = :image_id ')
                            ->setParameter('image_id', $img->getId())
                            ->getResult();
                    }
                }

                //efa any @ table saisie
                if (count($resSaisie) > 0) {
                    $listeImages[] = $img;
                }
            }
        }

        if ($dossier == -1) {
            return $listeImages;
        }

        $listeImagesFinal = array();

        /** @var  $image Image */
        foreach ($listeImages as $image) {
            if ($image->getLot()->getDossier()->getId() == $dossier) {
                $listeImagesFinal[] = $image;
            }
        }

        $listeImages = $listeImagesFinal;


        return $listeImages;
    }

    /**
     * Maka ny Liste-ny Image Utilisateur par Dossier
     * @param $dossierId
     * @param $utilisateurId
     * @param $exercice
     * @return array
     */
    public function getListeImageByUtilisateurDossier($dossierId, $utilisateurId, $exercice)
    {

        if ($dossierId == -1) {
            $images = $this
                ->createQueryBuilder('i')
                ->innerJoin('i.lot', 'lot')
                ->innerJoin('lot.dossier', 'dossier')
                ->andWhere('lot.utilisateur = :utilisateur')
                ->setParameter(':utilisateur', $utilisateurId)
                ->andWhere('i.exercice = :exercice')
                ->setParameter(':exercice', $exercice)
                ->andWhere('i.supprimer = 0')
                ->getQuery()
                ->getResult();
        } else {
            $images = $this
                ->createQueryBuilder('i')
                ->innerJoin('i.lot', 'lot')
                ->innerJoin('lot.dossier', 'dossier')
                ->where('lot.dossier = :the_dossier')
                ->setParameter(':the_dossier', $dossierId)
                ->andWhere('lot.utilisateur = :utilisateur')
                ->setParameter(':utilisateur', $utilisateurId)
                ->andWhere('i.exercice = :exercice')
                ->setParameter(':exercice', $exercice)
                ->andWhere('i.supprimer = 0')
                ->getQuery()
                ->getResult();
        }


        return $images;
    }

    /**
     * Maka ny liste-ny Images EnCours par Site(mbola tsy vita saisie sady mbola tsy any @ separation)
     * @param $clientId
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursByClient($clientId, $exercice)
    {
        $images = $this->getEntityManager()
            ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                    INNER JOIN i.lot lot 
                                    INNER JOIN lot.dossier dossier 
                                    INNER JOIN dossier.site site
                                    INNER JOIN site.client client 
                                    WHERE client.id = :the_client 
                                    AND i.exercice = :exercice 
                                    AND i.saisie2 <= 1 
                                    AND i.saisie1 <= 1 
                                    AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                    AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                    AND i.=0 AND i.supprimer = 0 AND i.extImage NOT IN (:ext)')
            ->setParameter('the_client', $clientId)
            ->setParameter('exercice', $exercice)
            ->setParameter('ext', array("zip", "7z","ini" ))
            ->getResult();

        return $images;

    }

    /**
     * @param $client
     * @param $nomImage
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursByClientNomImage($client, $nomImage, $exercice)
    {
        $images = $this->getEntityManager()
            ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                         INNER JOIN i.lot lot 
                                         INNER JOIN lot.dossier dossier 
                                         INNER JOIN dossier.site site 
                                         INNER JOIN site .client client
                                       WHERE i.nom = :nomImage
                                         AND client.id = :client
                                         AND i.exercice = :exercice 
                                         AND i.saisie2 <= 1 
                                         AND i.saisie1 <= 1 
                                         AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                         AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                         AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
            ->setParameter('nomImage', $nomImage)
            ->setParameter('exercice', $exercice)
            ->setParameter('client', $client)
            ->setParameter('ext', array("zip", "7z","ini" ))
            ->getResult();

        return $images;

    }




    public function getListeImageEncoursByDossierIdsNomImage($dossierIds, $nomImage, $exercice)
    {
        $images = $this->getEntityManager()
            ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                         INNER JOIN i.lot lot 
                                         INNER JOIN lot.dossier dossier 
                                      
                                       WHERE i.nom = :nomImage
                                         AND dossier.id IN ('.$dossierIds.')                                  
                                         AND i.exercice = :exercice 
                                         AND i.saisie2 <= 1 
                                         AND i.saisie1 <= 1 
                                         AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                         AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                         AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
            ->setParameter('nomImage', $nomImage)
            ->setParameter('exercice', $exercice)
            ->setParameter('ext', array("zip", "7z","ini" ))
            ->getResult();

        return $images;

    }





    /**
     * Maka ny liste-ny Images EnCours (mbola tsy vita saisie sady mbola tsy any @ separation)
     * @param $dossierId
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursByDossier($dossierId, $exercice, $dateScanSearch = false, $dateDebut=null, $dateFin=null)
    {

        if(!$dateScanSearch) {
            $images = $this->getEntityManager()
                ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                        INNER JOIN i.lot lot 
                                        INNER JOIN lot.dossier dossier
                                      WHERE dossier.id = :the_dossier 
                                        AND i.exercice = :exercice 
                                        AND i.saisie2 <= 1 
                                        AND i.saisie1 <= 1
                                        AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                        AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                        AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
                ->setParameter('the_dossier', $dossierId)
                ->setParameter('exercice', $exercice)
                ->setParameter('ext', array("zip", "7z", "ini"))
                ->getResult();
        }
        else{
            $images = $this->getEntityManager()
                ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                        INNER JOIN i.lot lot 
                                        INNER JOIN lot.dossier dossier
                                      WHERE dossier.id = :the_dossier 
                                        AND lot.dateScan >= :dateDebut
                                        AND lot.dateScan <= :dateFin
                                        AND i.exercice = :exercice 
                                        AND i.saisie2 <= 1 
                                        AND i.saisie1 <= 1
                                        AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                        AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                        AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
                ->setParameter('the_dossier', $dossierId)
                ->setParameter('exercice', $exercice)
                ->setParameter('dateDebut', $dateDebut)
                ->setParameter('dateFin', $dateFin)
                ->setParameter('ext', array("zip", "7z", "ini"))
                ->getResult();
        }

        return $images;
    }

    /**
     * Maka ny liste-ny Images EnCours par utilisateur, dossier (mbola tsy vita saisie sady mbola tsy any @ separation)
     * @param $utilisateurId
     * @param $dossierId
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursByUtilisateurDossier($utilisateurId, $dossierId, $exercice)
    {
        if ($dossierId > 0) {
            $images = $this->getEntityManager()
                ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                           INNER JOIN i.lot lot 
                                           INNER JOIN lot.dossier dossier
                                         WHERE dossier.id = :dossier 
                                           AND lot.utilisateur = :utilisateur
                                           AND i.exercice = :exercice 
                                           AND i.saisie2 <= 1 
                                           AND i.saisie1 <= 1 
                                           AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                           AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                           AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
                ->setParameter('dossier', $dossierId)
                ->setParameter('exercice', $exercice)
                ->setParameter('utilisateur', $utilisateurId)
                ->setParameter('ext', array("zip", "7z","ini" ))
                ->getResult();
        } else {
            $images = $this->getEntityManager()
                ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                                          INNER JOIN i.lot lot 
                                                          INNER JOIN lot.dossier dossier
                                                        WHERE lot.utilisateur = :utilisateur
                                                          AND i.exercice = :exercice 
                                                          AND i.saisie2 <= 1 
                                                          AND i.saisie1 <= 1
                                                          AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                                          AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                                          AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
                ->setParameter('exercice', $exercice)
                ->setParameter('utilisateur', $utilisateurId)
                ->setParameter('ext', array("zip", "7z","ini" ))
                ->getResult();
        }

        return $images;
    }

    /**
     * Maka ny liste-ny Images EnCours par Dossier, Utilisateur(mbola tsy vita saisie sady mbola tsy any @ separation)
     * @param $utilisateurId
     * @param $siteId
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursByUtilisateurSite($utilisateurId, $siteId, $exercice)
    {
        if ($siteId == 0) {
            $images = $this->getEntityManager()
                ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                           INNER JOIN i.lot lot 
                                         WHERE  i.exercice = :exercice 
                                           AND i.saisie2 <= 1 
                                           AND i.saisie1 <= 1 
                                           AND lot.utilisateur = :utilisateur 
                                           AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                           AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                           AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
                ->setParameter('exercice', $exercice)
                ->setParameter('utilisateur', $utilisateurId)
                ->setParameter('ext', array("zip", "7z","ini" ))
                ->getResult();
        } else {
            $images = $this->getEntityManager()
                ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                          INNER JOIN i.lot lot 
                                          INNER JOIN lot.dossier dossier
                                          WHERE  i.exercice = :exercice  
                                          AND i.saisie2 <= 1 
                                          AND i.saisie1 <= 1 
                                          AND lot.utilisateur = :utilisateur 
                                          AND dossier.site = :site
                                          AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                          AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                          AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
                ->setParameter('exercice', $exercice)
                ->setParameter('utilisateur', $utilisateurId)
                ->setParameter('site', $siteId)
                ->setParameter('ext', array("zip", "7z","ini" ))
                ->getResult();
        }

        return $images;
    }

    /**
     * @param $client
     * @param $dateDebut
     * @param $dateFin
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursByClientDateScan($client, $dateDebut, $dateFin, $exercice)
    {
        $images = $this->getEntityManager()
            ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                        INNER JOIN i.lot lot 
                                        INNER JOIN lot.dossier dossier 
                                        INNER JOIN dossier.site site 
                                        INNER JOIN site.client client
                                      WHERE lot.dateScan >= :dateDebut
                                        AND client.id = :client
                                        AND lot.dateScan <= :dateFin
                                        AND i.exercice = :exercice 
                                        AND i.saisie2 <= 1 
                                        AND i.saisie1 <= 1 
                                        AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                        AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                        AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->setParameter('exercice', $exercice)
            ->setParameter('client', $client)
            ->setParameter('ext', array("zip", "7z","ini" ))
            ->getResult();

        return $images;

    }

    /**
     * @param $dossier
     * @param $dateDebut
     * @param $dateFin
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursByDossierDateScan($dossier, $dateDebut, $dateFin, $exercice)
    {
        $images = $this->getEntityManager()
            ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                        INNER JOIN i.lot lot 
                                        INNER JOIN lot.dossier dossier 
                                        INNER JOIN dossier.site site 
                                      WHERE lot.dateScan >= :dateDebut
                                        AND dossier.id = :dossier
                                        AND lot.dateScan <= :dateFin
                                        AND i.exercice = :exercice 
                                        AND i.saisie2 <= 1 
                                        AND i.saisie1 <= 1 
                                        AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                        AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                        AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->setParameter('exercice', $exercice)
            ->setParameter('dossier', $dossier)
            ->setParameter('ext', array("zip", "7z","ini" ))
            ->getResult();

        return $images;

    }

    /**
     * @param $dossier
     * @param $nomImage
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursByDossierNomImage($dossier, $nomImage, $exercice)
    {
        $images = $this->getEntityManager()
            ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                         INNER JOIN i.lot lot 
                                         INNER JOIN lot.dossier dossier 
                                         INNER JOIN dossier.site site 
                                       WHERE i.nom = :nomImage 
                                         AND dossier.id = :dossier
                                         AND i.exercice = :exercice 
                                         AND i.saisie2 <= 1 
                                         AND i.saisie1 <= 1 
                                         AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                         AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                         AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
            ->setParameter('nomImage', $nomImage)
            ->setParameter('exercice', $exercice)
            ->setParameter('dossier', $dossier)
            ->setParameter('ext', array("zip", "7z","ini" ))
            ->getResult();

        return $images;

    }

    /**
     * Maka ny liste-ny Images EnCours par Site(mbola tsy vita saisie sady mbola tsy any @ separation)
     * @param $siteId
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursBySite($siteId, $exercice)
    {

        $images = $this->getEntityManager()
            ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                 INNER JOIN i.lot lot 
                                 INNER JOIN lot.dossier dossier 
                                 INNER JOIN dossier.site site 
                               WHERE site.id = :the_site 
                                 AND i.exercice = :exercice 
                                 AND i.saisie2 <= 1 
                                 AND i.saisie1 <= 1 
                                 AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                 AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                 AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
            ->setParameter('the_site', $siteId)
            ->setParameter('exercice', $exercice)
            ->setParameter('ext', array("zip", "7z","ini" ))
            ->getResult();

        return $images;

    }

    /**
     * @param $site
     * @param $dateDebut
     * @param $dateFin
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursBySiteDateScan($site, $dateDebut, $dateFin, $exercice)
    {
        $images = $this->getEntityManager()
            ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                           INNER JOIN i.lot lot 
                                           INNER JOIN lot.dossier dossier 
                                           INNER JOIN dossier.site site 
                                         WHERE lot.dateScan >= :dateDebut
                                           AND site.id = :site
                                           AND lot.dateScan <= :dateFin
                                           AND i.exercice = :exercice 
                                           AND i.saisie2 <= 1 
                                           AND i.saisie1 <= 1 
                                           AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                           AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                           AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->setParameter('exercice', $exercice)
            ->setParameter('site', $site)
            ->setParameter('ext', array("zip", "7z","ini" ))
            ->getResult();

        return $images;

    }

    /**
     * @param $site
     * @param $nomImage
     * @param $exercice
     * @return array
     */
    public function getListeImageEncoursBySiteNomImage($site, $nomImage, $exercice)
    {
        $images = $this->getEntityManager()
            ->createQuery('SELECT i, lot FROM AppBundle:Image i 
                                     INNER JOIN i.lot lot 
                                     INNER JOIN lot.dossier dossier 
                                     INNER JOIN dossier.site site 
                                   WHERE i.nom = :nomImage 
                                     AND site.id = :site
                                     AND i.exercice = :exercice 
                                     AND i.saisie2 <= 1 
                                     AND i.saisie1 <= 1 
                                     AND i.id NOT IN (SELECT img FROM AppBundle:Separation sep INNER JOIN sep.image img)
                                     AND i.id NOT IN (SELECT img1 FROM AppBundle:ImageImage imgimg INNER JOIN imgimg.image img1 WHERE imgimg.imageType = 1)
                                     AND i.decouper=0 AND i.supprimer=0 AND i.extImage NOT IN (:ext)')
            ->setParameter('nomImage', $nomImage)
            ->setParameter('exercice', $exercice)
            ->setParameter('site', $site)
            ->setParameter('ext', array("zip", "7z","ini" ))
            ->getResult();

        return $images;

    }


    /**
     * @param $client
     * @param $exercice
     * @return array
     */
    public function getListeImageImputeeByClient($client, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->leftJoin('dossier.site', 'site')
            ->where('site.client= :client')
            ->setParameter('client', $client)
            ->andWhere('i.imputation > 1 OR i.ctrlImputation > 1')
            ->andWhere('i.exercice = :exercice')
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        return $images;
    }

    /**
     * @param $dossier
     * @param $exercice
     * @return array
     */
    public function getListeImageImputeeByDossier($dossier, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->where('lot.dossier = :the_dossier')
            ->setParameter('the_dossier', $dossier)
            ->andWhere('i.imputation > 1 OR i.ctrlImputation > 1')
            ->andWhere('i.exercice = :exercice')
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        return $images;
    }

    /**
     * @param $site
     * @param $exercice
     * @return array
     */
    public function getListeImageImputeeBySite($site, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->where('dossier.site= :site')
            ->setParameter('site', $site)
            ->andWhere('i.imputation > 1 OR i.ctrlImputation > 1')
            ->andWhere('i.exercice = :exercice')
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        return $images;
    }

    /**
     * @param $client
     * @param $exercice
     * @return array
     */
    public function getListeImageSaisieByClient($client, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->leftJoin('dossier.site', 'site')
            ->where('site.client= :client')
            ->setParameter('client', $client)
            ->andWhere('i.ctrlSaisie >0 OR i.saisie1 >1 OR i.saisie2 >1')
            ->andWhere('i.imputation <= 1')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        return $images;
    }

    /**
     * @param $dossier
     * @param $exercice
     * @return array
     */
    public function getListeImageSaisieByDossier($dossier, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->where('lot.dossier = :the_dossier')
            ->setParameter('the_dossier', $dossier)
            ->andWhere('i.ctrlSaisie>0 OR i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.imputation <= 1')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        return $images;
    }

    /**
     * @param $site
     * @param $exercice
     * @return array
     */
    public function getListeImageSaisieBySite($site, $exercice)
    {
        $images = $this
            ->createQueryBuilder('i')
            ->leftJoin('i.lot', 'lot')
            ->leftJoin('lot.dossier', 'dossier')
            ->where('dossier.site = :site')
            ->setParameter('site', $site)
            ->andWhere('i.ctrlSaisie>0 OR i.saisie1 > 1 OR i.saisie2 > 1')
            ->andWhere('i.imputation <= 1')
            ->andWhere('i.exercice = :exercice')
            ->andWhere('i.supprimer = 0')
            ->setParameter(':exercice', $exercice)
            ->getQuery()
            ->getResult();

        return $images;
    }


    /**
     * Maka ny Tiers avy @ Table imputation na controle imputation
     * @param $nomImage
     * @return \AppBundle\Entity\Tiers|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTiersImageByNomImage($nomImage)
    {
        $images = $this->createQueryBuilder('i')
            ->where('i.nom = :nomImage')
            ->andWhere('i.supprimer = 0')
            ->setParameter('nomImage', $nomImage)
            ->select('i.id', 'i.imputation', 'i.ctrlImputation')
            ->getQuery()
            ->getResult();

        $tiers = null;

        foreach ($images as $image) {

            $resTiers = array();
            if ($image['ctrlImputation'] > 1) {

                $resTiers = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaImputationControle t WHERE t.image = :image_id')
                    ->setParameter('image_id', $image['id'])
                    ->getResult();

            } else if ($image['imputation'] > 1) {
                $resTiers = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaImputation t WHERE t.image = :image_id')
                    ->setParameter('image_id', $image['id'])
                    ->getResult();
            }

            if (count($resTiers)>0) {

                $resTiers = $resTiers[0];
                /**@var $resTiers TvaImputation */
                $tiers = $resTiers->getTiers();
            }
        }

        return $tiers;
    }

    public function getListeSoussouscategorieImageByImageId($imageId)
    {
        $qb = $this->createQueryBuilder('i');

        $qb->where('i.id= :imageId')
            ->andWhere('i.supprimer = 0')
            ->setParameter('imageId', $imageId)
            ->select('i.id', 'i.saisie1', 'i.saisie2', 'i.ctrlSaisie', 'i.imputation', 'i.ctrlImputation');

        $image = $qb->getQuery()->getResult();

        $listeSoussouscategorie = array();


        if (!is_null($image)) {

            $img = $image[0];

            $tvas = array();

            $saisies = null;

            if ($img['ctrlImputation'] > 1) {

                $tvas = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaImputationControle t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                $saisies = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:ImputationControle t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();


            } else if ($img['imputation'] > 1) {
                $tvas = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaImputation t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                $saisies = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:Imputation t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

            } else if ($img['ctrlSaisie'] > 1) {
                $tvas = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaSaisieControle t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                $saisies = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:SaisieControle t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();


            } else if ($img['saisie2'] > 1) {
                $tvas = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaSaisie2 t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                $saisies = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:Saisie2 t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

            } else if ($img['saisie1'] > 1) {

                $tvas = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaSaisie1 t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                $saisies = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:Saisie1 t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();
            }

            $trouveCategorie = false;

            if (!is_null($tvas)) {
                /** @var  $tva TvaSaisie1 */
                foreach ($tvas as $tva) {
                    if (!is_null($tva->getSoussouscategorie())) {
//                        $listeSoussouscategorie[] = $tva->getSoussouscategorie();

                        $listeSoussouscategorie[] = array('soussouscategorie'=>$tva->getSoussouscategorie(),
                            'souscategorie'=>$tva->getSoussouscategorie()->getSouscategorie(),
                            'categorie'=>$tva->getSoussouscategorie()->getSouscategorie()->getCategorie());

                        $trouveCategorie = true;
                    }
                }
            }


            if ($trouveCategorie == false && count($saisies) > 0) {
                /** @var  $saisie Saisie1 */
                $saisie = $saisies[0];
//                $listeSoussouscategorie[] = $saisie->getSoussouscategorie();

                if(!is_null($saisie->getSoussouscategorie())){
                    $listeSoussouscategorie[] = array('soussouscategorie'=>$saisie->getSoussouscategorie(),
                        'souscategorie'=>$saisie->getSoussouscategorie()->getSouscategorie(),
                        'categorie'=>$saisie->getSoussouscategorie()->getSouscategorie()->getCategorie());
                }

                else if($img['imputation'] > 1 || $img['ctrlImputation'] > 1){
                    /** @var $saisie Imputation */

                    if(!is_null($saisie->getSouscategorie())) {
                        $listeSoussouscategorie[] = array('soussouscategorie' => null,
                            'souscategorie' => $saisie->getSouscategorie(),
                            'categorie' => $saisie->getSouscategorie()->getCategorie());
                    }
                }
            }

        }

        return $listeSoussouscategorie;
    }


    public function getCategorieImageByImageId($imageId)
    {
        $qb = $this->createQueryBuilder('i');

        $qb->where('i.id= :imageId')
            ->andWhere('i.supprimer = 0')
            ->setParameter('imageId', $imageId)
            ->select('i.id', 'i.saisie1', 'i.saisie2', 'i.ctrlSaisie', 'i.imputation', 'i.ctrlImputation');

        $image = $qb->getQuery()->getResult();

        $listeCategorie = null;


        if (!is_null($image)) {

            $img = $image[0];

            $tvas = array();

            $saisies = null;

            $separations = $this->getEntityManager()
                ->createQuery('SELECT s FROM AppBundle:Separation s WHERE s.image = :image_id')
                ->setParameter('image_id', $img['id'])
                ->getResult();

            if ($img['ctrlImputation'] >1) {

                $tvas = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaImputationControle t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                $saisies = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:ImputationControle t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();


            } else if ($img['imputation'] >1) {
                $tvas = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaImputation t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                $saisies = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:Imputation t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

            } else if ($img['ctrlSaisie'] >1) {
                $tvas = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaSaisieControle t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                $saisies = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:SaisieControle t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();


            } else if ($img['saisie2'] >1) {
                $tvas = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaSaisie2 t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                $saisies = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:Saisie2 t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

            } else if ($img['saisie1'] >1) {

                $tvas = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:TvaSaisie1 t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();

                $saisies = $this->getEntityManager()
                    ->createQuery('SELECT t FROM AppBundle:Saisie1 t WHERE t.image = :image_id')
                    ->setParameter('image_id', $img['id'])
                    ->getResult();
            }

            $trouveCategorie = false;

            if (!is_null($tvas)) {
                /** @var  $tva TvaSaisie1 */
                foreach ($tvas as $tva) {
                    if (!is_null($tva->getSoussouscategorie())) {
                        $listeCategorie[] = $tva->getSoussouscategorie()->getSouscategorie()->getCategorie();
                        $trouveCategorie = true;
                    }
                }
            }

            //Raha tsy misy any @ tva dia mijery any @ saisie
            if ($trouveCategorie == false && count($saisies) > 0) {
                /** @var  $saisie Saisie1 */
                $saisie = $saisies[0];
                if(!is_null($saisie->getSoussouscategorie())) {
                    $listeCategorie[] = $saisie->getSoussouscategorie()->getSouscategorie()->getCategorie();
                    $trouveCategorie = true;
                }
                //Raha tsy misy soussouscategorie dia mijery souscategorie
                else {
                    /** @var $saisie Imputation */
                    if ($img['imputation'] > 1) {
                        $listeCategorie[] = $saisie->getSouscategorie()->getCategorie();
                        $trouveCategorie = true;
                    }
                }
            }

            //Raha mbola tsy nahita dia miverina mijery separation
            if($trouveCategorie == false){
                if(count($separations) > 0){
                    /** @var  $separation Separation*/
                    $separation = $separations[0];

                    $listeCategorie[] = $separation->getCategorie();
                }
            }

        }

        return $listeCategorie;
    }

    public function getListeSoussouscategorieIdImageByDossier($dossier, $exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $listeSoussouscategorie = array();

        $query = "SELECT i.id as id, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation
                  FROM image i
                  INNER JOIN lot l ON (l.id = i.lot_id )
                  INNER JOIN dossier d ON (d.id = l.dossier_id)
                  WHERE d.id = :dossier_id AND i.exercice = :exercice AND (i.saisie1>1 OR i.saisie2>1) AND i.supprimer = 0";

        $prep = $pdo->prepare($query);
        $prep->execute(array(
            'exercice' => $exercice,
            'dossier_id' => $dossier,
        ));
        $images = $prep->fetchAll();

        foreach ($images as $image) {

            $tableSaisie = "";
            $tableTva = "";

            if ($image->ctrlImputation > 1) {
                $tableSaisie = "imputation_controle";
                $tableTva = "tva_imputation_controle";
            } else if ($image->imputation > 1) {
                $tableSaisie = 'imputation';
                $tableTva = "tva_imputation";
            } else if ($image->ctrlSaisie > 1) {
                $tableSaisie = 'saisie_controle';
                $tableTva = 'tva_saisie_controle';
            } else if ($image->saisie2 > 1) {
                $tableSaisie = 'saisie2';
                $tableTva = 'tva_saisie2';
            } else if ($image->saisie1 > 1) {
                $tableSaisie = 'saisie1';
                $tableTva = 'tva_saisie1';
            }

            $query = "SELECT ssc.id as id 
            FROM " . $tableTva . " t
            INNER JOIN soussouscategorie ssc ON ssc.id = t.soussouscategorie_id 
            WHERE image_id = :image";
            $prep = $pdo->prepare($query);
            $prep->execute(array(
                'image' => $image->id
            ));

            $tvaCats = $prep->fetchAll();

            if (count($tvaCats) > 0) {
                foreach ($tvaCats as $tvaCat) {
                    if (!is_null($tvaCat)) {
                        $listeSoussouscategorie[] = $tvaCat;
                    }
                }
            } else {

                $query = "SELECT ssc.id as id 
                FROM " . $tableSaisie . " t
                INNER JOIN soussouscategorie ssc ON ssc.id = t.soussouscategorie_id 
                WHERE image_id = :image";
                $prep = $pdo->prepare($query);
                $prep->execute(array(
                    'image' => $image->id
                ));

                $saisieCats = $prep->fetchAll();

                if (count($saisieCats) > 0) {
                    foreach ($saisieCats as $saisieCat) {
                        if (!is_null($saisieCat)) {
                            $listeSoussouscategorie[] = $saisieCat;
                        }
                    }
                }
            }
        }
        return $listeSoussouscategorie;
    }


    public function getListeSoussouscategorieIdImageByUtilisateur($utilisateur, $exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $listeSoussouscategorie = array();

        $query = "SELECT i.id as id, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation
                  FROM image i
                  INNER JOIN lot l ON (l.id = i.lot_id )
                  WHERE l.utilisateur_id = :utilisateur_id AND i.exercice = :exercice AND (i.saisie1>1 OR i.saisie2>1) AND i.supprimer = 0";

        $prep = $pdo->prepare($query);
        $prep->execute(array(
            'exercice' => $exercice,
            'utilisateur_id' => $utilisateur,
        ));
        $images = $prep->fetchAll();

        foreach ($images as $image) {

            $tableSaisie = "";
            $tableTva = "";

            if ($image->ctrlImputation > 1) {
                $tableSaisie = "imputation_controle";
                $tableTva = "tva_imputation_controle";
            } else if ($image->imputation > 1) {
                $tableSaisie = 'imputation';
                $tableTva = "tva_imputation";
            } else if ($image->ctrlSaisie > 1) {
                $tableSaisie = 'saisie_controle';
                $tableTva = 'tva_saisie_controle';
            } else if ($image->saisie2 > 1) {
                $tableSaisie = 'saisie2';
                $tableTva = 'tva_saisie2';
            } else if ($image->saisie1 > 1) {
                $tableSaisie = 'saisie1';
                $tableTva = 'tva_saisie1';
            }

            $query = "SELECT ssc.id as id 
            FROM " . $tableTva . " t
            INNER JOIN soussouscategorie ssc ON ssc.id = t.soussouscategorie_id 
            WHERE image_id = :image";
            $prep = $pdo->prepare($query);
            $prep->execute(array(
                'image' => $image->id
            ));

            $tvaCats = $prep->fetchAll();

            if (count($tvaCats) > 0) {
                foreach ($tvaCats as $tvaCat) {
                    if (!is_null($tvaCat)) {
                        $listeSoussouscategorie[] = $tvaCat;
                    }
                }
            } else {

                $query = "SELECT ssc.id as id 
                FROM " . $tableSaisie . " t
                INNER JOIN soussouscategorie ssc ON ssc.id = t.soussouscategorie_id 
                WHERE image_id = :image";
                $prep = $pdo->prepare($query);
                $prep->execute(array(
                    'image' => $image->id
                ));

                $saisieCats = $prep->fetchAll();

                if (count($saisieCats) > 0) {
                    foreach ($saisieCats as $saisieCat) {
                        if (!is_null($saisieCat)) {
                            $listeSoussouscategorie[] = $saisieCat;
                        }
                    }
                }
            }
        }
        return $listeSoussouscategorie;
    }

    public function getListeTiersIdImageByDossier($dossier, $exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $listeTiersIdLib = array();
        $listeTiers = array();

        $query = "SELECT i.id as image_id, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation
                  FROM image i
                  INNER JOIN lot l ON (l.id = i.lot_id )
                  INNER JOIN dossier d ON (d.id = l.dossier_id)
                  WHERE d.id = :dossier_id AND i.exercice = :exercice AND (i.imputation > 1 OR i.ctrl_imputation > 1) AND i.supprimer = 0";

        $prep = $pdo->prepare($query);
        $prep->execute(array(
            'exercice' => $exercice,
            'dossier_id' => $dossier,
        ));
        $images = $prep->fetchAll();

        foreach ($images as $image) {

            if ($image->ctrlImputation > 1) {
                $tableTva = "tva_imputation_controle";
            } else if ($image->imputation > 1) {
                $tableTva = "tva_imputation";
            }

            $query = "SELECT t.tiers_id AS tiers_id, ti.intitule AS tiers_intitule FROM " . $tableTva . " t
            INNER JOIN tiers ti ON ti.id = t.tiers_id
            WHERE image_id = :image
            LIMIT 1";

            $prep = $pdo->prepare($query);
            $prep->execute(array(
                'image' => $image->image_id
            ));

            $tiers = $prep->fetchAll();

            if (count($tiers) > 0) {

                $temp = array('tiers_id'=>$tiers[0]->tiers_id, 'tiers_intitule'=>$tiers[0]->tiers_intitule);
                if(!in_array($temp, $listeTiersIdLib,true)){
                    $listeTiersIdLib[] = $temp;
                }
            }
        }

        usort($listeTiersIdLib, array($this, 'compIntitule'));

        foreach ($listeTiersIdLib as $listeTier){
            $listeTiers[] = $listeTier['tiers_id'];
        }

        return $listeTiers;

    }

//    public function getInfoImagesByClientSiteDossier($client_id, $site_id, $dossier_id, $avancement, $exercice)
//    public function getInfoImagesByClientSiteDossier($client_id, $site_id, $dossier_id, $avancement, $exercice, $dateScan, $dateDebut, $dateFin)
//    public function getInfoImagesByClientSiteDossier($client_id, $site_id, $dossier_id, $avancement, $exercice, $dateScan, $periodeSearch, $dateDebut, $dateFin)
//    {
//        $con = new CustomPdoConnection();
//        $pdo = $con->connect();
//
//        $res = array();
//
//        $listeImages = array();
//
//
//        switch ($avancement) {
//            case 4:
//                if ($dossier_id != 0) {
//                    $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  WHERE d.id = :dossier_id AND i.exercice = :exercice AND (i.imputation > 1 OR i.ctrl_imputation > 1)";
//
//                    $prep = $pdo->prepare($query);
//                    $prep->execute(array(
//                        'dossier_id' => $dossier_id,
//                        'exercice' => $exercice
//                    ));
//
//                    $listeImages = $prep->fetchAll();
//
//                } else if ($site_id != 0) {
//                    $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  WHERE s.id = :site_id AND i.exercice = :exercice AND (i.imputation > 1 OR i.ctrl_imputation > 1)";
//
//                    $prep = $pdo->prepare($query);
//                    $prep->execute(array(
//                        'site_id' => $site_id,
//                        'exercice' => $exercice
//                    ));
//
//                    $listeImages = $prep->fetchAll();
//                } else if ($client_id != 0) {
//                    $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  INNER JOIN client c ON (c.id = s.client_id)
//                  WHERE c.id = :client_id AND i.exercice = :exercice AND (i.imputation > 1 OR i.ctrl_imputation > 1)";
//
//                    $prep = $pdo->prepare($query);
//                    $prep->execute(array(
//                        'client_id' => $client_id,
//                        'exercice' => $exercice
//                    ));
//
//                    $listeImages = $prep->fetchAll();
//                }
//                break;
//            case 3:
//                if ($dossier_id != 0) {
//                    $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  WHERE d.id = :dossier_id AND i.exercice = :exercice AND (i.saisie1 > 1 OR i.saisie2 > 1) AND (i.imputation <= 1)";
//
//                    $prep = $pdo->prepare($query);
//                    $prep->execute(array(
//                        'dossier_id' => $dossier_id,
//                        'exercice' => $exercice
//                    ));
//
//                    $listeImages = $prep->fetchAll();
//                } else if ($site_id != 0) {
//                    $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  WHERE s.id = :site_id AND i.exercice = :exercice AND (i.saisie1 > 1 OR i.saisie2 > 1) AND (i.imputation <= 1)";
//
//                    $prep = $pdo->prepare($query);
//                    $prep->execute(array(
//                        'site_id' => $site_id,
//                        'exercice' => $exercice
//                    ));
//
//                    $listeImages = $prep->fetchAll();
//                } else if ($client_id != 0) {
//                    $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  INNER JOIN client c ON (c.id = s.client_id)
//                  WHERE c.id = :client_id AND i.exercice = :exercice AND (i.saisie1 > 1 OR i.saisie2 > 1) AND (i.imputation <= 1)";
//
//                    $prep = $pdo->prepare($query);
//                    $prep->execute(array(
//                        'client_id' => $client_id,
//                        'exercice' => $exercice
//                    ));
//
//                    $listeImages = $prep->fetchAll();
//                }
//                break;
//
//
//            default:
//
//                if ($dateScan == true) {
//
//                    if ($dossier_id != 0) {
//                        $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  WHERE d.id = :dossier_id AND i.exercice = :exercice AND (i.saisie1 > 1 OR i.saisie2 > 1) AND l.date_scan >= :dateDebut AND l.date_scan <= :dateFin";
//
//                        $prep = $pdo->prepare($query);
//                        $prep->execute(array(
//                            'dossier_id' => $dossier_id,
//                            'exercice' => $exercice,
//                            'dateDebut' => $dateDebut,
//                            'dateFin' => $dateFin
//                        ));
//
//                        $listeImages = $prep->fetchAll();
//                    } else if ($site_id != 0) {
//                        $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  WHERE s.id = :site_id AND i.exercice = :exercice AND (i.saisie1 > 1 OR i.saisie2 > 1) AND l.date_scan >= :dateDebut AND l.date_scan <= :dateFin";
//
//                        $prep = $pdo->prepare($query);
//                        $prep->execute(array(
//                            'site_id' => $site_id,
//                            'exercice' => $exercice,
//                            'dateDebut' => $dateDebut,
//                            'dateFin' => $dateFin
//                        ));
//
//                        $listeImages = $prep->fetchAll();
//                    } else if ($client_id != 0) {
//                        $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  INNER JOIN client c ON (c.id = s.client_id)
//                  WHERE c.id = :client_id  AND i.exercice = :exercice AND (i.saisie1 > 1 OR i.saisie2 > 1) AND l.date_scan >= :dateDebut AND l.date_scan <= :dateFin";
//
//                        $prep = $pdo->prepare($query);
//                        $prep->execute(array(
//                            'client_id' => $client_id,
//                            'exercice' => $exercice,
//                            'dateDebut' => $dateDebut,
//                            'dateFin' => $dateFin
//                        ));
//
//                        $listeImages = $prep->fetchAll();
//                    }
//                }
//
//                else {
//                    if ($dossier_id != 0) {
//                        $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//
//                  LEFT JOIN saisie1 s1 ON i.id = s1.image_id AND i.saisie1 > 1
//
//
//
//                  WHERE d.id = :dossier_id AND i.exercice = :exercice AND (i.saisie1 > 1 OR i.saisie2 > 1) AND
//
//                  (
//
//                    (s1.date_facture>= :dateDebut AND s1.date_facture<= :dateFin)
//
//                  )
//
//
//
//
//
//
//                  ";
//
//                        $prep = $pdo->prepare($query);
//                        $prep->execute(array(
//                            'dossier_id' => $dossier_id,
//                            'exercice' => $exercice,
//                            'dateDebut' => $dateDebut,
//                            'dateFin' => $dateFin
//                        ));
//
//                        $listeImages = $prep->fetchAll();
//                    } else if ($site_id != 0) {
//                        $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//
//				  LEFT JOIN saisie1 s1 ON i.id = s1.image_id AND i.saisie1 > 1
//
//                  WHERE s.id = :site_id AND i.exercice = :exercice AND (i.saisie1 > 1 OR i.saisie2 > 1) AND
//
//                  (
//                    (s1.date_facture>= :dateDebut AND s1.date_facture<= :dateFin)
//
//                  )
//
//
//
//                  ";
//
//                        $prep = $pdo->prepare($query);
//                        $prep->execute(array(
//                            'site_id' => $site_id,
//                            'exercice' => $exercice,
//                            'dateDebut' => $dateDebut,
//                            'dateFin' => $dateFin
//                        ));
//
//                        $listeImages = $prep->fetchAll();
//                    } else if ($client_id != 0) {
//                        $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie
//                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  INNER JOIN client c ON (c.id = s.client_id)
//
//                  LEFT JOIN saisie1 s1 ON i.id = s1.image_id AND i.saisie1 > 1
//
//
//                  WHERE c.id = :client_id  AND i.exercice = :exercice AND (i.saisie1>1 OR i.saisie2>1) AND
//
//                  (
//                    (s1.date_facture >= :dateDebut AND s1.date_facture <= :dateFin)
//                  )
//
//
//
//                  ";
//
//                        $prep = $pdo->prepare($query);
//                        $prep->execute(array(
//                            'client_id' => $client_id,
//                            'exercice' => $exercice,
//                            'dateDebut' => $dateDebut,
//                            'dateFin' => $dateFin
//                        ));
//
//                        $listeImages = $prep->fetchAll();
//                    }
//                }
//
//                break;
//        }
//
//        if (count($listeImages) > 0) {
//
//            $tableSaisie = '';
//            $tableTva = '';
//            $tableNdf = '';
//
//            foreach ($listeImages as $image) {
//
//
//                $resSaisie = null;
//                $resTva = null;
//                $resTable = '';
//                $resNdf = null;
//                $resReleve = null;
//                $resBanqueSousCategorieAutre = null;
//
//                if ($image->ctrlImputation > 1) {
//                    $tableSaisie = 'imputation_controle';
//                    $tableTva = 'tva_imputation_controle';
//                    $tableNdf = 'imputation_controle_note_frais';
//                    $resTable = 'Imputation';
//                } else if ($image->imputation > 1) {
//                    $tableSaisie = 'imputation';
//                    $tableTva = 'tva_imputation';
//                    $tableNdf = 'imputation_note_frais';
//                    $resTable = 'Imputation';
//                } else if ($image->ctrlSaisie > 1) {
//                    $tableSaisie = 'saisie_controle';
//                    $tableTva = 'tva_saisie_controle';
//                    $tableNdf = 'controle_note_frais';
//                    $resTable = 'Saisie';
//                } else if ($image->saisie1 != 1) {
//                    $tableSaisie = 'saisie1';
//                    $tableTva = 'tva_saisie1';
//                    $tableNdf = 'saisie1_note_frais';
//                    $resTable = 'Saisie';
//                } else if ($image->saisie2 = !1) {
//                    $tableSaisie = 'saisie2';
//                    $tableTva = 'tva_saisie2';
//                    $tableNdf = 'saisie2_note_frais';
//                    $resTable = 'Saisie';
//                }
//
//
//                if($periodeSearch == false) {
//
//                    if($tableSaisie == 'imputation_controle' || $tableSaisie == 'imputation') {
//                        $query = "SELECT t.date_facture AS date_facture, t.date_livraison AS date_livraison, t.date_echeance AS date_echeance, t.periode_f1 AS periode_f1,
//                          t.periode_d1 AS periode_d1, t.chrono AS chrono, t.banque_compte_id AS banque_compte_id,
//                          t.soussouscategorie_id AS soussouscategorie_id, t.souscategorie_id AS souscategorie_id, c.code AS code, t.solde_debut AS solde_debut,
//                          t.solde_fin AS solde_fin, t.rs AS rs, t.num_facture AS num_facture, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle,
//                          c.libelle AS categorie_libelle, ssc.libelle_new AS soussouscategorie_libelle_new, sc.libelle_new AS souscategorie_libelle_new
//                          FROM " . $tableSaisie . " t
//                          LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
//                          LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id)
//                          LEFT JOIN categorie c ON (c.id = sc.categorie_id)
//                          WHERE image_id = :image_id";
//                    }
//                    else{
//                        $query = "SELECT t.date_facture AS date_facture, t.date_livraison AS date_livraison, t.date_echeance AS date_echeance, t.periode_f1 AS periode_f1,
//                          t.periode_d1 AS periode_d1, t.chrono AS chrono, t.banque_compte_id AS banque_compte_id,
//                          t.soussouscategorie_id AS soussouscategorie_id, c.code AS code, t.solde_debut AS solde_debut,
//                          t.solde_fin AS solde_fin, t.rs AS rs, t.num_facture AS num_facture, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle,
//                          c.libelle AS categorie_libelle, ssc.libelle_new AS soussouscategorie_libelle_new, sc.libelle_new AS souscategorie_libelle_new
//                          FROM " . $tableSaisie . " t
//                          LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
//                          LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id)
//                          LEFT JOIN categorie c ON (c.id = sc.categorie_id)
//                          WHERE image_id = :image_id";
//                    }
//
//                    $prep = $pdo->prepare($query);
//
//                    $prep->execute(array(
//                        'image_id' => $image->image_id
//                    ));
//                }
//                else{
//
//                    if($tableSaisie == 'imputation_controle' || $tableSaisie == 'imputation') {
//                        $query = "SELECT t.date_facture AS date_facture, t.date_livraison AS date_livraison, t.date_echeance AS date_echeance, t.periode_f1 AS periode_f1,
//                          t.periode_d1 AS periode_d1, t.chrono AS chrono, t.banque_compte_id AS banque_compte_id,
//                          t.soussouscategorie_id AS soussouscategorie_id, t.souscategorie_id AS souscategorie_id,c.code AS code, t.solde_debut AS solde_debut,
//                          t.solde_fin AS solde_fin, t.rs AS rs, t.num_facture AS num_facture,ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle,
//                          c.libelle AS categorie_libelle, ssc.libelle_new AS soussouscategorie_libelle_new, sc.libelle_new AS souscategorie_libelle_new
//                          FROM " . $tableSaisie . " t
//                          LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
//                          LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id)
//                          LEFT JOIN categorie c ON (c.id = sc.categorie_id)
//                          WHERE image_id = :image_id
//                          AND t.date_facture >= :dateDebut AND t.date_facture <= :dateFin";
//                    }
//                    else {
//                        $query = "SELECT t.date_facture AS date_facture, t.date_livraison AS date_livraison, t.date_echeance AS date_echeance, t.periode_f1 AS periode_f1,
//                          t.periode_d1 AS periode_d1, t.chrono AS chrono, t.banque_compte_id AS banque_compte_id,
//                          t.soussouscategorie_id AS soussouscategorie_id, c.code AS code, t.solde_debut AS solde_debut,
//                          t.solde_fin AS solde_fin, t.rs AS rs, t.num_facture AS num_facture, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle,
//                          c.libelle AS categorie_libelle, ssc.libelle_new AS soussouscategorie_libelle_new, sc.libelle_new AS souscategorie_libelle_new
//                          FROM " . $tableSaisie . " t
//                          LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
//                          LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id)
//                          LEFT JOIN categorie c ON (c.id = sc.categorie_id)
//                          WHERE image_id = :image_id
//                          AND t.date_facture >= :dateDebut AND t.date_facture <= :dateFin";
//                    }
//                    $prep = $pdo->prepare($query);
//
//                    $prep->execute(array(
//                        'image_id' => $image->image_id,
//                        'dateDebut' => $dateDebut,
//                        'dateFin' =>$dateFin
//                    ));
//                }
//
//                $saisies = $prep->fetchAll();
//
//                if (count($saisies) > 0) {
//                    foreach ($saisies as $saisie) {
//                        $resSaisie[] = $saisie;
//
//                        if ($saisie->code == 'CODE_NDF') {
//                            $query = "SELECT t.date, t.profit_de, t.ttc, tt.taux  AS tva_taux
//                                      FROM " . $tableNdf . " t
//                                      LEFT JOIN type_frais tf on (tf.id = t.type_frais_id)
//                                      LEFT JOIN tva_taux tt on (tf.tva_taux_id = tt.id)
//                                      WHERE t.image_id = :image_id";
//
//                            $prep = $pdo->prepare($query);
//                            $prep->execute(array(
//                                'image_id' => $image->image_id
//                            ));
//
//                            $ndfs = $prep->fetchAll();
//
//                            if (count($ndfs) > 0) {
//                                foreach ($ndfs as $ndf) {
//                                    $resNdf[] = $ndf;
//                                }
//                            }
//                        }
//                    }
//                }
//
//
//                if($tableSaisie == 'imputation_controle' || $tableSaisie == 'imputation') {
//                    $query = "SELECT t.montant_ht AS montant_ht,tva.taux AS tva_taux, ti.compte_str AS compte_str, ti.intitule AS tiers_intitule, ti.id AS tiers_id,p.compte AS compte,
//                 t.soussouscategorie_id as soussouscategorie_id, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle
//                 FROM " . $tableTva . " t
//                 LEFT JOIN tva_taux tva ON (tva.id = t.tva_taux_id)
//                 LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
//                 LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id)
//                 LEFT JOIN tiers ti ON (ti.id = t.tiers_id)
//                 LEFT JOIN pcc p ON (p.id = t.pcc_id)
//                 WHERE image_id = :image_id";
//                }
//                else{
//                    $query = "SELECT t.montant_ht AS montant_ht,tva.taux AS tva_taux, '' AS compte_str, '' AS tiers_intitule, '-1' AS tiers_id, '' AS compte,
//                 t.soussouscategorie_id as soussouscategorie_id, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle
//                 FROM " . $tableTva . " t
//                 LEFT JOIN tva_taux tva ON (tva.id = t.tva_taux_id)
//                 LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
//                 LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id)
//                 WHERE image_id = :image_id";
//                }
//                $prep = $pdo->prepare($query);
//
//                $prep->execute(array(
//                    'image_id' => $image->image_id
//                ));
//
//                $tvas = $prep->fetchAll();
//
//                if (count($tvas) > 0) {
//                    foreach ($tvas as $tva) {
//                        $resTva[] = $tva;
//                    }
//                }
//
//
//                $res[] = array('image' => $image, 'table' => $resTable, 'saisie' => $resSaisie, 'tva' => $resTva, 'ndf' => $resNdf);
//            }
//        }
//        return $res;
//    }


    public function getInfoImagesByDossierIds($dossier_ids, $avancement, $exercice, $dateScan, $periodeSearch, $dateDebut, $dateFin)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $res = array();

        $param = ['exercice' => $exercice];

        switch ($avancement) {
            case 4:
                    $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie 
                                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
                                  d.id AS dossier_id, d.nom AS dossier_nom, i.ext_image AS ext_image
                                  FROM image i
                                  INNER JOIN lot l ON (l.id = i.lot_id )
                                  INNER JOIN dossier d ON (d.id = l.dossier_id)
                                  LEFT JOIN separation sep ON sep.image_id = i.id
                                  LEFT JOIN soussouscategorie ssc ON ssc.id = sep.soussouscategorie_id 
                                  WHERE d.id IN (".$dossier_ids.") AND (i.exercice = :exercice OR ssc.multi_exercice = 1) AND (i.imputation > 1 OR i.ctrl_imputation > 1) AND i.supprimer = 0";

                    $prep = $pdo->prepare($query);
                    $prep->execute($param);

                    $listeImages = $prep->fetchAll();

                break;
            case 3:

                    $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie 
                                  AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
                                  d.id AS dossier_id, d.nom AS dossier_nom, i.ext_image AS ext_image
                                  FROM image i
                                  INNER JOIN lot l ON (l.id = i.lot_id )
                                  INNER JOIN dossier d ON (d.id = l.dossier_id)
                                  LEFT JOIN separation sep ON sep.image_id = i.id
                                  LEFT JOIN soussouscategorie ssc ON ssc.id = sep.soussouscategorie_id 
                                  WHERE d.id IN (".$dossier_ids.") AND (i.exercice = :exercice OR ssc.multi_exercice = 1) AND (i.saisie1 > 1 OR i.saisie2 > 1) AND (i.imputation <= 1) AND i.supprimer = 0 ";

                    $prep = $pdo->prepare($query);
                    $prep->execute($param);

                    $listeImages = $prep->fetchAll();

                break;


            default:

                if ($dateScan == true) {

                    if ($dateDebut !== '' && $dateFin !== '') {

                        $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie 
                                      AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
                                      d.id AS dossier_id, d.nom AS dossier_nom, i.ext_image AS ext_image
                                      FROM image i
                                      INNER JOIN lot l ON (l.id = i.lot_id )
                                      INNER JOIN dossier d ON (d.id = l.dossier_id)
                                      LEFT JOIN separation sep ON sep.image_id = i.id
                                      LEFT JOIN soussouscategorie ssc ON ssc.id = sep.soussouscategorie_id 
                                      WHERE d.id IN (" . $dossier_ids . ") AND (i.exercice = :exercice OR ssc.multi_exercice = 1) AND (i.saisie1 > 1 OR i.saisie2 > 1) AND l.date_scan >= :dateDebut AND l.date_scan <= :dateFin AND i.supprimer = 0 ";

                        $param['dateDebut'] = $dateDebut;
                        $param['dateFin'] = $dateFin;

                        $prep = $pdo->prepare($query);
                        $prep->execute($param);

                        $listeImages = $prep->fetchAll();
                    }
                    else {
                        $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie 
                                      AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
                                      d.id AS dossier_id, d.nom AS dossier_nom, i.ext_image AS ext_image
                                      FROM image i
                                      INNER JOIN lot l ON (l.id = i.lot_id )
                                      INNER JOIN dossier d ON (d.id = l.dossier_id)
                                      LEFT JOIN separation sep ON sep.image_id = i.id
                                      LEFT JOIN soussouscategorie ssc ON ssc.id = sep.soussouscategorie_id 
                                      WHERE d.id IN (" . $dossier_ids . ") AND (i.exercice = :exercice OR ssc.multi_exercice = 1) AND (i.saisie1 > 1 OR i.saisie2 > 1) AND i.supprimer = 0 ";

                        $prep = $pdo->prepare($query);
                        $prep->execute($param);

                        $listeImages = $prep->fetchAll();
                    }
                }

                else {

                    if($dateDebut !== '' && $dateFin !== ''){
                        $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie 
                                      AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
                                      d.id AS dossier_id, d.nom AS dossier_nom, i.ext_image AS ext_image
                                      FROM image i
                                      INNER JOIN lot l ON (l.id = i.lot_id )
                                      INNER JOIN dossier d ON (d.id = l.dossier_id)                                      
                                      LEFT JOIN saisie1 s1 ON i.id = s1.image_id AND i.saisie1 > 1   
                                      LEFT JOIN separation sep ON sep.image_id = i.id
                                      LEFT JOIN soussouscategorie ssc ON ssc.id = sep.soussouscategorie_id                                     
                                      WHERE d.id IN (".$dossier_ids.") AND i.supprimer = 0 AND (i.exercice = :exercice OR ssc.multi_exercice = 1)AND (i.saisie1 > 1 OR i.saisie2 > 1) AND                                      
                                      (                                     
                                        (s1.date_facture>= :dateDebut AND s1.date_facture<= :dateFin)                                          
                                      )                                      
                                      ";

                        $param['dateDebut'] = $dateDebut;
                        $param['dateFin'] = $dateFin;


                        $prep = $pdo->prepare($query);
                        $prep->execute($param);
                    }
                    else{
                        $query = "SELECT i.id as image_id, i.nom as nom, i.saisie1 as saisie1, i.saisie2 as saisie2, i.ctrl_saisie 
                                      AS ctrlSaisie, i.imputation AS imputation, i.ctrl_imputation AS ctrlImputation,l.date_scan AS date_scan, i.exercice AS exercice,
                                      d.id AS dossier_id, d.nom AS dossier_nom, i.ext_image AS ext_image
                                      FROM image i
                                      INNER JOIN lot l ON (l.id = i.lot_id )
                                      INNER JOIN dossier d ON (d.id = l.dossier_id)                                      
                                      LEFT JOIN saisie1 s1 ON i.id = s1.image_id AND i.saisie1 > 1  
                                      LEFT JOIN separation sep ON sep.image_id = i.id
                                      LEFT JOIN soussouscategorie ssc ON ssc.id = sep.soussouscategorie_id                                      
                                      WHERE d.id IN (".$dossier_ids.") AND (i.exercice = :exercice OR ssc.multi_exercice = 1) AND (i.saisie1 > 1 OR i.saisie2 > 1) AND i.supprimer = 0 ";

                        $prep = $pdo->prepare($query);
                        $prep->execute($param);
                    }
                    $listeImages = $prep->fetchAll();
                }

                break;
        }

        if (count($listeImages) > 0) {

            $tableSaisie = '';
            $tableTva = '';
            $tableNdf = '';

            foreach ($listeImages as $image) {


                $resSaisie = null;
                $resTva = null;
                $resTable = '';
                $resNdf = null;
                $resReleve = null;
                $resBanqueSousCategorieAutre = null;

                if ($image->ctrlImputation > 1) {
                    $tableSaisie = 'imputation_controle';
                    $tableTva = 'tva_imputation_controle';
                    $tableNdf = 'imputation_controle_note_frais';
                    $resTable = 'Imputation';
                } else if ($image->imputation > 1) {
                    $tableSaisie = 'imputation';
                    $tableTva = 'tva_imputation';
                    $tableNdf = 'imputation_note_frais';
                    $resTable = 'Imputation';
                } else if ($image->ctrlSaisie > 1) {
                    $tableSaisie = 'saisie_controle';
                    $tableTva = 'tva_saisie_controle';
                    $tableNdf = 'controle_note_frais';
                    $resTable = 'Saisie';
                } else if ($image->saisie1 != 1) {
                    $tableSaisie = 'saisie1';
                    $tableTva = 'tva_saisie1';
                    $tableNdf = 'saisie1_note_frais';
                    $resTable = 'Saisie';
                } else if ($image->saisie2 = !1) {
                    $tableSaisie = 'saisie2';
                    $tableTva = 'tva_saisie2';
                    $tableNdf = 'saisie2_note_frais';
                    $resTable = 'Saisie';
                }


                if($periodeSearch == false) {

                    if($tableSaisie == 'imputation_controle' || $tableSaisie == 'imputation') {
                        $query = "SELECT t.date_facture AS date_facture, t.date_livraison AS date_livraison, t.date_echeance AS date_echeance, t.periode_f1 AS periode_f1, 
                          t.periode_d1 AS periode_d1, t.chrono AS chrono, t.banque_compte_id AS banque_compte_id,
                          t.soussouscategorie_id AS soussouscategorie_id, t.souscategorie_id AS souscategorie_id, c.code AS code, t.solde_debut AS solde_debut, 
                          t.solde_fin AS solde_fin, t.rs AS rs, t.num_facture AS num_facture, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle,
                          c.libelle AS categorie_libelle, ssc.libelle_new AS soussouscategorie_libelle_new, sc.libelle_new AS souscategorie_libelle_new
                          FROM " . $tableSaisie . " t 
                          LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
                          LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id) 
                          LEFT JOIN categorie c ON (c.id = sc.categorie_id)
                          WHERE image_id = :image_id";
                    }
                    else{
                        $query = "SELECT t.date_facture AS date_facture, t.date_livraison AS date_livraison, t.date_echeance AS date_echeance, t.periode_f1 AS periode_f1, 
                          t.periode_d1 AS periode_d1, t.chrono AS chrono, t.banque_compte_id AS banque_compte_id,
                          t.soussouscategorie_id AS soussouscategorie_id, c.code AS code, t.solde_debut AS solde_debut, 
                          t.solde_fin AS solde_fin, t.rs AS rs, t.num_facture AS num_facture, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle,
                          c.libelle AS categorie_libelle, ssc.libelle_new AS soussouscategorie_libelle_new, sc.libelle_new AS souscategorie_libelle_new 
                          FROM " . $tableSaisie . " t 
                          LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
                          LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id) 
                          LEFT JOIN categorie c ON (c.id = sc.categorie_id)
                          WHERE image_id = :image_id";
                    }

                    $prep = $pdo->prepare($query);

                    $prep->execute(array(
                        'image_id' => $image->image_id
                    ));
                }
                else{

                    if($dateDebut !== '' && $dateFin !== '') {

                        if ($tableSaisie == 'imputation_controle' || $tableSaisie == 'imputation') {
                            $query = "SELECT t.date_facture AS date_facture, t.date_livraison AS date_livraison, t.date_echeance AS date_echeance, t.periode_f1 AS periode_f1, 
                                          t.periode_d1 AS periode_d1, t.chrono AS chrono, t.banque_compte_id AS banque_compte_id,
                                          t.soussouscategorie_id AS soussouscategorie_id, t.souscategorie_id AS souscategorie_id,c.code AS code, t.solde_debut AS solde_debut, 
                                          t.solde_fin AS solde_fin, t.rs AS rs, t.num_facture AS num_facture,ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle,
                                          c.libelle AS categorie_libelle, ssc.libelle_new AS soussouscategorie_libelle_new, sc.libelle_new AS souscategorie_libelle_new 
                                          FROM " . $tableSaisie . " t 
                                          LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
                                          LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id) 
                                          LEFT JOIN categorie c ON (c.id = sc.categorie_id)
                                          WHERE image_id = :image_id 
                                          AND t.date_facture >= :dateDebut AND t.date_facture <= :dateFin";
                        } else {
                            $query = "SELECT t.date_facture AS date_facture, t.date_livraison AS date_livraison, t.date_echeance AS date_echeance, t.periode_f1 AS periode_f1, 
                                          t.periode_d1 AS periode_d1, t.chrono AS chrono, t.banque_compte_id AS banque_compte_id,
                                          t.soussouscategorie_id AS soussouscategorie_id, c.code AS code, t.solde_debut AS solde_debut, 
                                          t.solde_fin AS solde_fin, t.rs AS rs, t.num_facture AS num_facture, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle,
                                          c.libelle AS categorie_libelle, ssc.libelle_new AS soussouscategorie_libelle_new, sc.libelle_new AS souscategorie_libelle_new 
                                          FROM " . $tableSaisie . " t 
                                          LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
                                          LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id) 
                                          LEFT JOIN categorie c ON (c.id = sc.categorie_id)
                                          WHERE image_id = :image_id 
                                          AND t.date_facture >= :dateDebut AND t.date_facture <= :dateFin";
                        }
                        $prep = $pdo->prepare($query);

                        $prep->execute(array(
                            'image_id' => $image->image_id,
                            'dateDebut' => $dateDebut,
                            'dateFin' => $dateFin
                        ));
                    }
                    else{

                        if ($tableSaisie == 'imputation_controle' || $tableSaisie == 'imputation') {
                            $query = "SELECT t.date_facture AS date_facture, t.date_livraison AS date_livraison, t.date_echeance AS date_echeance, t.periode_f1 AS periode_f1, 
                                          t.periode_d1 AS periode_d1, t.chrono AS chrono, t.banque_compte_id AS banque_compte_id,
                                          t.soussouscategorie_id AS soussouscategorie_id, t.souscategorie_id AS souscategorie_id,c.code AS code, t.solde_debut AS solde_debut, 
                                          t.solde_fin AS solde_fin, t.rs AS rs, t.num_facture AS num_facture,ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle,
                                          c.libelle AS categorie_libelle, ssc.libelle_new AS soussouscategorie_libelle_new, sc.libelle_new AS souscategorie_libelle_new 
                                          FROM " . $tableSaisie . " t 
                                          LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
                                          LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id) 
                                          LEFT JOIN categorie c ON (c.id = sc.categorie_id)
                                          WHERE image_id = :image_id ";
                        } else {
                            $query = "SELECT t.date_facture AS date_facture, t.date_livraison AS date_livraison, t.date_echeance AS date_echeance, t.periode_f1 AS periode_f1, 
                                          t.periode_d1 AS periode_d1, t.chrono AS chrono, t.banque_compte_id AS banque_compte_id,
                                          t.soussouscategorie_id AS soussouscategorie_id, c.code AS code, t.solde_debut AS solde_debut, 
                                          t.solde_fin AS solde_fin, t.rs AS rs, t.num_facture AS num_facture, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle,
                                          c.libelle AS categorie_libelle, ssc.libelle_new AS soussouscategorie_libelle_new, sc.libelle_new AS souscategorie_libelle_new 
                                          FROM " . $tableSaisie . " t 
                                          LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
                                          LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id) 
                                          LEFT JOIN categorie c ON (c.id = sc.categorie_id)
                                          WHERE image_id = :image_id";
                        }
                        $prep = $pdo->prepare($query);

                        $prep->execute(array(
                            'image_id' => $image->image_id
                        ));

                    }
                }

                $saisies = $prep->fetchAll();

                if (count($saisies) > 0) {
                    foreach ($saisies as $saisie) {
                        $resSaisie[] = $saisie;

                        if ($saisie->code == 'CODE_NDF') {
                            $query = "SELECT t.date, t.profit_de, t.ttc, tt.taux  AS tva_taux
                                      FROM " . $tableNdf . " t 
                                      LEFT JOIN type_frais tf on (tf.id = t.type_frais_id)
                                      LEFT JOIN tva_taux tt on (tf.tva_taux_id = tt.id)
                                      WHERE t.image_id = :image_id";

                            $prep = $pdo->prepare($query);
                            $prep->execute(array(
                                'image_id' => $image->image_id
                            ));

                            $ndfs = $prep->fetchAll();

                            if (count($ndfs) > 0) {
                                foreach ($ndfs as $ndf) {
                                    $resNdf[] = $ndf;
                                }
                            }
                        }
                    }
                }


                if($tableSaisie == 'imputation_controle' || $tableSaisie == 'imputation') {
                    $query = "SELECT t.montant_ht AS montant_ht,tva.taux AS tva_taux, ti.compte_str AS compte_str, ti.intitule AS tiers_intitule, ti.id AS tiers_id,p.compte AS compte, 
                                     t.soussouscategorie_id as soussouscategorie_id, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle
                                     FROM " . $tableTva . " t
                                     LEFT JOIN tva_taux tva ON (tva.id = t.tva_taux_id) 
                                     LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
                                     LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id)
                                     LEFT JOIN tiers ti ON (ti.id = t.tiers_id)
                                     LEFT JOIN pcc p ON (p.id = t.pcc_id)
                                     WHERE image_id = :image_id";
                }
                else{
                    $query = "SELECT t.montant_ht AS montant_ht,tva.taux AS tva_taux, '' AS compte_str, '' AS tiers_intitule, '-1' AS tiers_id, '' AS compte, 
                                     t.soussouscategorie_id as soussouscategorie_id, ssc.libelle AS soussouscategorie_libelle, sc.libelle AS souscategorie_libelle
                                     FROM " . $tableTva . " t
                                     LEFT JOIN tva_taux tva ON (tva.id = t.tva_taux_id) 
                                     LEFT JOIN soussouscategorie ssc ON (ssc.id = t.soussouscategorie_id)
                                     LEFT JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id)
                                     WHERE image_id = :image_id";
                }
                $prep = $pdo->prepare($query);

                $prep->execute(array(
                    'image_id' => $image->image_id
                ));

                $tvas = $prep->fetchAll();

                if (count($tvas) > 0) {
                    foreach ($tvas as $tva) {
                        $resTva[] = $tva;
                    }
                }


                $res[] = array('image' => $image, 'table' => $resTable, 'saisie' => $resSaisie, 'tva' => $resTva, 'ndf' => $resNdf);
            }
        }
        return $res;
    }





//    public function getInfoEncoursImagesByClientSiteDossier($client_id, $site_id, $dossier_id, $exercice, $dateScan, $dateDebut, $dateFin)
//    {
//
//        $con = new CustomPdoConnection();
//        $pdo = $con->connect();
//
//        $listeImages = array();
//
//        if ($dateScan == false) {
//
//            if ($dossier_id != 0) {
//                $query = "SELECT i.id as image_id, i.nom as nom, l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  WHERE d.id = :dossier_id AND i.exercice = :exercice AND (i.saisie1 <= 1 OR i.saisie2 <= 1)
//                  AND i.id NOT IN (SELECT image_id FROM separation)";
//
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'dossier_id' => $dossier_id,
//                    'exercice' => $exercice
//                ));
//
//                $listeImages = $prep->fetchAll();
//
//            } else if ($site_id != 0) {
//                $query = "SELECT i.id as image_id, i.nom as nom, l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  WHERE s.id = :site_id AND i.exercice = :exercice AND (i.saisie1 <= 1 OR i.saisie2 <= 1)
//                  AND i.id NOT IN (SELECT image_id FROM separation)";
//
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'site_id' => $site_id,
//                    'exercice' => $exercice
//                ));
//
//                $listeImages = $prep->fetchAll();
//            } else if ($client_id != 0) {
//                $query = "SELECT i.id as image_id, i.nom as nom, l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  INNER JOIN client c ON (c.id = s.client_id)
//                  WHERE c.id = :client_id AND i.exercice = :exercice AND (i.saisie1 <= 1 OR i.saisie2 <= 1)
//                  AND i.id NOT IN (SELECT image_id FROM separation)";
//
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'client_id' => $client_id,
//                    'exercice' => $exercice
//                ));
//
//                $listeImages = $prep->fetchAll();
//            }
//        } else {
//            if ($dossier_id != 0) {
//                $query = "SELECT i.id as image_id, i.nom as nom, l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  WHERE d.id = :dossier_id AND i.exercice = :exercice AND (i.saisie1 <= 1 OR i.saisie2 <= 1)
//                  AND i.id NOT IN (SELECT image_id FROM separation) AND l.date_scan>= :dateDebut AND l.date_scan<= :dateFin";
//
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'dossier_id' => $dossier_id,
//                    'exercice' => $exercice,
//                    'dateDebut' => $dateDebut,
//                    'dateFin' => $dateFin
//                ));
//
//                $listeImages = $prep->fetchAll();
//
//            } else if ($site_id != 0) {
//                $query = "SELECT i.id as image_id, i.nom as nom, l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  WHERE s.id = :site_id AND i.exercice = :exercice AND (i.saisie1 <= 1 OR i.saisie2 <= 1)
//                  AND i.id NOT IN (SELECT image_id FROM separation) AND l.date_scan>= :dateDebut AND l.date_scan<= :dateFin";
//
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'site_id' => $site_id,
//                    'exercice' => $exercice,
//                    'dateDebut' => $dateDebut,
//                    'dateFin' => $dateFin
//                ));
//
//                $listeImages = $prep->fetchAll();
//            } else if ($client_id != 0) {
//                $query = "SELECT i.id as image_id, i.nom as nom, l.date_scan AS date_scan, i.exercice AS exercice,
//                  d.id AS dossier_id, d.nom AS dossier_nom
//                  FROM image i
//                  INNER JOIN lot l ON (l.id = i.lot_id )
//                  INNER JOIN dossier d ON (d.id = l.dossier_id)
//                  INNER JOIN site s ON (s.id = d.site_id)
//                  INNER JOIN client c ON (c.id = s.client_id)
//                  WHERE c.id = :client_id AND i.exercice = :exercice AND (i.saisie1 <= 1 OR i.saisie2 <= 1)
//                  AND i.id NOT IN (SELECT image_id FROM separation) AND l.date_scan>= :dateDebut AND l.date_scan<= :dateFin";
//
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'client_id' => $client_id,
//                    'exercice' => $exercice,
//                    'dateDebut' => $dateDebut,
//                    'dateFin' => $dateFin
//                ));
//
//                $listeImages = $prep->fetchAll();
//            }
//        }
//
//        return $listeImages;
//    }





    public function getInfoEncoursImagesByDossierIds($dossier_ids, $exercice, $dateScan, $dateDebut, $dateFin)
    {

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        if ($dateScan == false) {

            $query = 'SELECT i.id as image_id, i.nom as nom, l.date_scan AS date_scan, i.exercice AS exercice,
                              d.id AS dossier_id, d.nom AS dossier_nom, i.ext_image AS ext_image
                              FROM image i
                              INNER JOIN lot l ON (l.id = i.lot_id )
                              INNER JOIN dossier d ON (d.id = l.dossier_id)
                              WHERE d.id IN (' . $dossier_ids . ') AND i.exercice = :exercice 
                              AND (i.saisie1 <= 1 OR i.saisie2 <= 1) 
                              AND i.id NOT IN (SELECT image_id FROM separation)
                              AND i.id NOT IN (SELECT image_id FROM image_image WHERE image_type = 1)                  
                              AND i.decouper=0 AND i.supprimer = 0';


            $prep = $pdo->prepare($query);
            $prep->execute(array(
                'exercice' => $exercice,
            ));

            $listeImages = $prep->fetchAll();


        } else {
            if($dateDebut !== '' && $dateFin !== '') {

                $query = 'SELECT i.id as image_id, i.nom as nom, l.date_scan AS date_scan, i.exercice AS exercice,
                                  d.id AS dossier_id, d.nom AS dossier_nom, i.ext_image AS ext_image
                                  FROM image i
                                  INNER JOIN lot l ON (l.id = i.lot_id )
                                  INNER JOIN dossier d ON (d.id = l.dossier_id)
                                  WHERE d.id IN (' . $dossier_ids . ') AND i.exercice = :exercice 
                                  AND (i.saisie1 <= 1 OR i.saisie2 <= 1) 
                                  AND i.id NOT IN (SELECT image_id FROM separation) 
                                  AND i.id NOT IN (SELECT image_id FROM image_image WHERE image_type = 1) 
                                  AND l.date_scan>= :dateDebut AND l.date_scan<= :dateFin
                                  AND i.decouper=0 AND i.supprimer = 0';

                $prep = $pdo->prepare($query);
                $prep->execute(array(
                    'exercice' => $exercice,
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin
                ));

                $listeImages = $prep->fetchAll();
            }
            else{

                $query = 'SELECT i.id as image_id, i.nom as nom, l.date_scan AS date_scan, i.exercice AS exercice,
                                  d.id AS dossier_id, d.nom AS dossier_nom, i.ext_image AS ext_image
                                  FROM image i
                                  INNER JOIN lot l ON (l.id = i.lot_id )
                                  INNER JOIN dossier d ON (d.id = l.dossier_id)
                                  WHERE d.id IN (' . $dossier_ids . ') AND i.exercice = :exercice 
                                  AND (i.saisie1 <= 1 OR i.saisie2 <= 1) 
                                  AND i.id NOT IN (SELECT image_id FROM separation) 
                                  AND i.id NOT IN (SELECT image_id FROM image_image WHERE image_type = 1)                                
                                  AND i.decouper=0 AND i.supprimer = 0';

                $prep = $pdo->prepare($query);
                $prep->execute(array(
                    'exercice' => $exercice
                ));

                $listeImages = $prep->fetchAll();

            }
        }

        return $listeImages;
    }




    public function getInfoReleveByDossier($banquecompte_id, $exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "SELECT  DISTINCT b.nom AS banque_nom, bc.numcompte AS numcompte, bc.id AS banquecompte_id, 
                  ic.periode_d1 AS periode_deb, ic.periode_f1 AS periode_fin,
                  CASE WHEN r.num_releve IS NOT NULL THEN r.num_releve ELSE ic.num_releve END AS num_releve, 
                  ic.solde_debut AS solde_deb ,ic.solde_fin AS solde_fin, l.date_scan AS date_scan,
                  i.id AS image_id, i.nom AS image_nom, ssc.libelle AS soussouscategorie_libelle ,
                  0 AS image_id_suivant, 0 AS image_id_precedent, 0 AS releve_intermediaire, '' AS controle
                  FROM image i
                  INNER JOIN separation sep ON sep.image_id = i.id
                  LEFT JOIN releve r ON r.image_id = i.id
                  INNER JOIN lot l ON l.id = i.lot_id
                  INNER JOIN dossier d ON l.dossier_id = d.id
                  INNER JOIN site s ON s.id = d.site_id
                  INNER JOIN client c ON c.id = s.client_id
                  INNER JOIN imputation_controle ic ON ic.image_id = i.id
                  LEFT JOIN banque_compte bc ON ic.banque_compte_id = bc.id
                  LEFT JOIN banque b ON b.id = bc.banque_id
                  LEFT JOIN souscategorie sc ON sc.id = sep.souscategorie_id
                  LEFT JOIN soussouscategorie ssc ON ssc.id = sep.soussouscategorie_id
                  WHERE i.exercice = :exercice AND i.supprimer = 0 AND bc.id = :banquecompte_id AND (ssc.libelle NOT LIKE '%doublon%' OR ssc.libelle IS null ) AND sc.id = 10
                  ORDER BY ic.periode_d1,r.num_releve,i.nom";

            $prep = $pdo->prepare($query);
            $prep->execute(array(
                'banquecompte_id' => $banquecompte_id,
                'exercice' => $exercice
            ));

        return $prep->fetchAll();

    }


    /**
     * @param Dossier $dossier
     * @return bool
     */
    public function imageInDossier(Dossier $dossier){

        $images = $this->createQueryBuilder('i')
            ->innerJoin('i.lot' , 'l')
            ->innerJoin('l.dossier', 'd')
            ->where('d = :dossier')
            ->andWhere('i.supprimer = 0')
            ->setParameter('dossier', $dossier)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return count($images) > 0;
    }

    public function getFirstSend(Dossier $dossier){
        $res = $this->createQueryBuilder('image')
            ->innerJoin('image.lot','lot')
            ->where('lot.dossier = :dossier')
            ->andWhere('image.supprimer = 0')
            ->setParameter('dossier', $dossier)
            ->orderBy('image.exercice', 'ASC')
            ->select('image.exercice')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if(count($res) > 0){
            return $res[0];
        }

        return null;
    }

    /**
     * @param EchangeReponse $echangeReponse
     * @return Image[]
     */
    public function getChildEchangeReponses(EchangeReponse $echangeReponse)
    {
        return $this->createQueryBuilder('i')
            ->where('i.echangeReponse = :echangeReponse')
            ->setParameter('echangeReponse',$echangeReponse)
            ->getQuery()
            ->getResult();
    }



    public function getDateScanByCSDExercice($clientid, $siteid, $dossierid, $exercice){
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param = ['exercice' => $exercice];

        if(intval($dossierid) > -1){
            $query = "SELECT DISTINCT  l.date_scan FROM image i 
                        INNER JOIN lot l ON l.id=i.lot_id 
                        LEFT JOIN separation sep ON sep.image_id = i.id
                        LEFT JOIN soussouscategorie ssc ON ssc.id = sep.soussouscategorie_id
                        WHERE (exercice = :exercice OR ssc.multi_exercice = 1) AND dossier_id = :dossier";
            $param['dossier'] = $dossierid;
        }
        else if(intval($siteid) > 0){
            $query = "SELECT DISTINCT  l.date_scan FROM image i 
                        INNER JOIN lot l ON l.id=i.lot_id 
                        INNER JOIN dossier d ON d.id = l.dossier_id
                        INNER JOIN site s ON s.id = d.site_id
                        LEFT JOIN separation sep ON sep.image_id = i.id
                        LEFT JOIN soussouscategorie ssc ON ssc.id = sep.soussouscategorie_id
                        WHERE (exercice = :exercice OR ssc.multi_exercice = 1) AND s.id = :site";

            $param['site'] = $siteid;
        }
        else if (intval($clientid) > 0){
            $query = "SELECT DISTINCT  l.date_scan FROM image i 
                        INNER JOIN lot l ON l.id=i.lot_id 
                        INNER JOIN dossier d ON d.id = l.dossier_id
                        INNER JOIN site s ON s.id = d.site_id
                        INNER JOIN client c ON c.id = s.client_id
                        LEFT JOIN separation sep ON sep.image_id = i.id
                        LEFT JOIN soussouscategorie ssc ON ssc.id = sep.soussouscategorie_id
                        WHERE (exercice = :exercice OR ssc.multi_exercice = 1) AND c.id = :client";

            $param['client'] = $clientid;
        }

        $query .= " ORDER BY date_scan";

        $prep = $pdo->prepare($query);
        $prep->execute($param);
        return $prep->fetchAll();
    }

    public function getListeImpute( $param )
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $data = array();

        //client dossier principal
        if ( $param['client'][0] == 0 && $param['dossier'][0] == 0 ) {
            $clientOrDossier          = " ";
            $clientOrDossierPm        = " ";
            $clientOrDossierChq       = " ";
            $clientOrDossierLettree   = " ";
            $clientOrDossierClef      = " ";
            $clientOrDossierMois      = " ";
            $clientOrDossierALettrer  = " ";
            $clientOrDossierEcrChange = " ";
            $clientOrDossierValider   = " ";
        } else if ( $param['client'][0] == 0 && $param['dossier'][0] != 0 ) {
            $clientOrDossier          = "AND bc.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";
 
            $clientOrDossierPm        = "AND bcp.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierChq       = "AND bccq.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierLettree   = "AND bcle.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierClef      = "AND bcc.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierMois      = "AND rmm.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierALettrer  = "AND bcale.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierEcrChange  = "AND bcechg.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierValider  = "AND bcval.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";
        } else if ( $param['client'][0] != 0 && $param['dossier'][0] == 0 ) {
            $clientOrDossier         = "AND c.id IN ( '" . implode("', '", $param['client']) . "' )";

            $clientOrDossierPm       = "AND cp.id IN ( '" . implode("', '", $param['client']) . "' )";

            $clientOrDossierChq      = "AND ccq.id IN ( '" . implode("', '", $param['client']) . "' )";

            $clientOrDossierLettree  = "AND cle.id IN ( '" . implode("', '", $param['client']) . "' )";

            $clientOrDossierClef     = "AND cc.id IN ( '" . implode("', '", $param['client']) . "' )";

            $clientOrDossierMois     = "AND cm.id IN ( '" . implode("', '", $param['client']) . "' )";

            $clientOrDossierALettrer = "AND cale.id IN ( '" . implode("', '", $param['client']) . "' )";

            $clientOrDossierEcrChange = "AND cechg.id IN ( '" . implode("', '", $param['client']) . "' )";

            $clientOrDossierValider  = "AND cval.id IN ( '" . implode("', '", $param['client']) . "' )";
        } else if ( $param['client'][0] != 0 && $param['dossier'][0] != 0 ) {
            $clientOrDossier          = "AND c.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossier         .= " AND bc.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierPm        = "AND cp.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossierPm       .= " AND bcp.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierChq       = "AND ccq.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossierChq      .= " AND bccq.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierLettree   = "AND cle.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossierLettree  .= " AND bcle.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierClef      = "AND cc.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossierClef     .= " AND bcc.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierMois      = "AND cm.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossierMois     .= " AND rmm.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierALettrer  = "AND cale.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossierALettrer .= " AND bcale.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierEcrChange  = "AND cechg.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossierEcrChange .= " AND bcechg.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierValider   = "AND cval.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossierValider  .= " AND bcval.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";
        } 

        //get imputees
        $query = "select count(*) as nb_r, d.status, d.status_debut, b.nom as banque, c.nom as clients, d.nom as dossier, d.cloture, bc.id as banquecompte_id, d.id as dossier_id, c.id as client_id,
            (case
                when length(bc.numcompte) >= 11 then substring(bc.numcompte, length(bc.numcompte)-10, length(bc.numcompte))
                else bc.numcompte
            end) as comptes, rtva.libelle as regime_tva, bc.numcompte, bc.etat, i.valider, bc.id as banque_compte_id, d.tva_date as ech, d.tva_mode, d.debut_activite, bc.etat as bc_etat, 
            
            (select rmm.mois
            from releve rm
            inner join releve_manquant rmm on rmm.banque_compte_id = rm.banque_compte_id
            inner join image im on im.id = rm.image_id
            inner join lot lm on (lm.id = im.lot_id)
            inner join dossier dm on (dm.id = lm.dossier_id)
            inner join site sm on sm.id=dm.site_id
            inner join client cm on cm.id=sm.client_id
            inner join banque_compte bcm on (bcm.id = rm.banque_compte_id and bcm.dossier_id = dm.id)
            inner join banque bm on (bm.id=bcm.banque_id)
            inner join separation sepm on (sepm.image_id = im.id)  
            inner join souscategorie sscm on (sepm.souscategorie_id = sscm.id) 
            where rmm.exercice = " . $param['exercice'] . "
            and rm.banque_compte_id=r.banque_compte_id 
            and cm.status = 1
            and rm.operateur_id is null
            and sepm.souscategorie_id IS NOT NULL 
            and sscm.id = 10 
            " . $clientOrDossierMois . "
            group by bcm.numcompte) as mois,

            (select count(*) as nb_c
            from image ic
            left join releve rc on (ic.id = rc.image_id)
            inner join lot lc on (lc.id = ic.lot_id)
            inner join dossier dc on (dc.id = lc.dossier_id)
            inner join site sc on (sc.id = dc.site_id)
            inner join client cc on (cc.id = sc.client_id)
            inner join banque_compte bcc on (bcc.id = rc.banque_compte_id and bcc.dossier_id = dc.id)
            inner join banque bc on (bc.id=bcc.banque_id)
            inner join separation sepc on (sepc.image_id = ic.id)  
            inner join souscategorie sscc on (sepc.souscategorie_id = sscc.id) 
            where ic.supprimer = 0 
            and (rc.cle_dossier_id is not null)
            and ic.exercice = " . $param['exercice'] . "
            and rc.banque_compte_id=r.banque_compte_id 
            and cc.status = 1
            and rc.operateur_id is null
            and rc.image_flague_id is null
            and sepc.souscategorie_id IS NOT NULL 
            and sscc.id = 10 
            " . $clientOrDossierClef . "
            group by bcc.numcompte) as nb_clef,
            
             (select count(*) as nb_rle
            from image ile
            left join releve rle on (ile.id = rle.image_id)
            inner join lot lle on (lle.id = ile.lot_id)
            inner join dossier dle on (dle.id = lle.dossier_id)
            inner join site sle on (sle.id = dle.site_id)
            inner join client cle on (cle.id = sle.client_id)
            inner join banque_compte bcle on (bcle.id = rle.banque_compte_id and bcle.dossier_id = dle.id)
            inner join banque ble on (ble.id=bcle.banque_id)
            inner join separation seple on (seple.image_id = ile.id)  
            inner join souscategorie sscle on (seple.souscategorie_id = sscle.id) 
            where ile.supprimer = 0 
            and rle.image_flague_id is not null
            and ile.exercice = " . $param['exercice'] . "
            and rle.banque_compte_id=r.banque_compte_id 
            and cle.status = 1
            and rle.operateur_id is null
            and seple.souscategorie_id IS NOT NULL 
            AND ((rle.image_flague_id IN (SELECT bsca.image_flague_id FROM banque_sous_categorie_autre bsca  where bsca.compte_tiers_id is not null or bsca.compte_bilan_id is not null or bsca.compte_tva_id is not null or bsca.compte_chg_id is not null))
            OR  (rle.image_flague_id IN (SELECT tic.image_flague_id FROM tva_imputation_controle tic where tic.tiers_id is not null or tic.pcc_bilan_id is not null or tic.pcc_tva_id is not null))
            OR (rle.image_flague_id IN (SELECT rlele.image_flague_id FROM releve rlele where rlele.operateur_id IS NULL and rlele.id <> rle.id)))
            and sscle.id = 10 
            " . $clientOrDossierLettree . "
            group by bcle.numcompte) as nb_lettre,
            
            (SELECT count(*) as nb_rcq 
            from image icq
            left join releve rcq on (icq.id = rcq.image_id)
            inner join lot lcq on (lcq.id = icq.lot_id)
            inner join dossier dcq on (dcq.id = lcq.dossier_id)
            inner join site scq on (scq.id = dcq.site_id)
            inner join client ccq on (ccq.id = scq.client_id)
            inner join banque_compte bccq on (bccq.id = rcq.banque_compte_id and bccq.dossier_id = dcq.id)
            inner join banque bcq on (bcq.id=bccq.banque_id)
            inner join separation sepcq on (sepcq.image_id = icq.id)  
            inner join souscategorie ssccq on (sepcq.souscategorie_id = ssccq.id) 
            left join cle_dossier cldcq on (cldcq.id = rcq.cle_dossier_id)  
            where icq.supprimer = 0 
            and (rcq.libelle like '%CHQ%' OR rcq.libelle like '%CHEQUE%') 
            and (ROUND(rcq.credit - rcq.debit,2) < 0)
            and not (rcq.ecriture_change = 1 and rcq.maj = 3)
            and rcq.image_flague_id is null
            and icq.exercice = " . $param['exercice'] . "
            and rcq.banque_compte_id=r.banque_compte_id 
            and ccq.status = 1
            and rcq.operateur_id is null
            and sepcq.souscategorie_id IS NOT NULL 
            and ssccq.id = 10 
            and (rcq.cle_dossier_id is null or cldcq.pas_piece is null)
            " . $clientOrDossierChq . "
            group by bccq.numcompte) as chq_inconnu,

            (SELECT count(rale.id) as nb_alettrer
            from image iale
            left join releve rale on (iale.id = rale.image_id)
            inner join lot lale on (lale.id = iale.lot_id)
            inner join dossier dale on (dale.id = lale.dossier_id)
            inner join site sale on (sale.id = dale.site_id)
            inner join client cale on (cale.id = sale.client_id)
            inner join banque_compte bcale on (bcale.id = rale.banque_compte_id and bcale.dossier_id = dale.id)
            inner join banque bale on (bale.id=bcale.banque_id)
            inner join separation sepale on (sepale.image_id = iale.id)  
            inner join souscategorie sscale on (sepale.souscategorie_id = sscale.id) 
            where iale.supprimer = 0
            and iale.exercice = " . $param['exercice'] . "
            and cale.status = 1
            and rale.banque_compte_id=r.banque_compte_id 
            and rale.operateur_id is null
            and rale.flaguer = 1
            and sepale.souscategorie_id IS NOT NULL 
            and sscale.id = 10 
            " . $clientOrDossierALettrer . "
            group by bcale.numcompte) as a_lettrer,

            (SELECT count(rval.id) as nb_restvalider
            from image ival
            inner join releve rval on (ival.id = rval.image_id)
            inner join lot lval on (lval.id = ival.lot_id)
            inner join dossier dval on (dval.id = lval.dossier_id)
            inner join site sval on (sval.id = dval.site_id)
            inner join client cval on (cval.id = sval.client_id)
            inner join banque_compte bcval on (bcval.id = rval.banque_compte_id and bcval.dossier_id = dval.id)
            inner join banque bval on (bval.id=bcval.banque_id)
            inner join separation sepval on (sepval.image_id = ival.id)  
            inner join souscategorie sscval on (sepval.souscategorie_id = sscval.id) 
            where ival.supprimer = 0
            and ival.exercice = " . $param['exercice'] . "
            and cval.status = 1
            and rval.banque_compte_id=r.banque_compte_id 
            and rval.operateur_id is null
            and (rval.flaguer = 1 or rval.flaguer = 2)
            and sepval.souscategorie_id IS NOT NULL 
            and sscval.id = 10 
            " . $clientOrDossierValider . "
            group by bcval.numcompte, bcval.dossier_id) as rest_valider,

            (SELECT count(*) as nb_ecriture_change 
            from image iechg
            left join releve rechg on (iechg.id = rechg.image_id)
            inner join lot lechg on (lechg.id = iechg.lot_id)
            inner join dossier dechg on (dechg.id = lechg.dossier_id)
            inner join site sechg on (sechg.id = dechg.site_id)
            inner join client cechg on (cechg.id = sechg.client_id)
            inner join banque_compte bcechg on (bcechg.id = rechg.banque_compte_id and bcechg.dossier_id = dechg.id)
            inner join banque bechg on (bechg.id=bcechg.banque_id)
            inner join separation sepechg on (sepechg.image_id = iechg.id)  
            inner join souscategorie sscechg on (sepechg.souscategorie_id = sscechg.id) 
            where iechg.supprimer = 0
            and iechg.exercice = " . $param['exercice'] . "
            and cechg.status = 1
            and rechg.banque_compte_id=r.banque_compte_id 
            and rechg.operateur_id is null
            and rechg.ecriture_change = 1
            and sepechg.souscategorie_id IS NOT NULL 
            and sscechg.id = 10 
            " . $clientOrDossierEcrChange . "
            group by bcechg.numcompte) as nb_ecriture_change

            from image i
            left join releve r on (r.image_id = i.id)
            inner join lot l on (l.id = i.lot_id)
            inner join dossier d on (l.dossier_id = d.id)
            inner join site s on (s.id = d.site_id)
            inner join client c on (c.id = s.client_id)
            inner join banque_compte bc on (bc.dossier_id = d.id and bc.id = r.banque_compte_id)
            inner join banque b on (b.id = bc.banque_id)
            inner join separation sep on (sep.image_id = i.id)  
            inner join souscategorie ssc on (sep.souscategorie_id = ssc.id) 
            left join regime_tva rtva on (d.regime_tva_id = rtva.id)
            where i.exercice = " . $param['exercice'] . " and i.supprimer = 0 
            and c.status = 1
            and d.status = 1
            and r.operateur_id is null
            and sep.souscategorie_id IS NOT NULL 
            and ssc.id = 10 
            " . $clientOrDossier . "
            group by bc.numcompte";

        $prep = $pdo->prepare($query);
        $prep->execute();
        $data['imputees'] = $prep->fetchAll();
        return $data;
    }

    public function getListImageBanqueGestionTacheOb( $did, $exercice, $dscan, $souscat, $soussouscat, $etape, $banquecompteid )
    {
        if ( $exercice == '' ) {
            $now = new \DateTime();
            $exercice = $now->format('Y');
        }

        if ( $etape == 1 ) {
            $where = "AND I.saisie1 > 0 ";
        } else {
            $where = "AND I.ctrl_saisie >= 2 ";
        }

        //releve bancaire
        $banquecompte = $this->getEntityManager()
                             ->getRepository('AppBundle:BanqueCompte')
                             ->find($banquecompteid);


        if ( !$banquecompte ) {
            $query = "SELECT I.*, S.souscategorie_id, '' AS avec_releve FROM  image I 
                        INNER JOIN lot L ON I.lot_id = L.id 
                        INNER JOIN dossier D ON L.dossier_id = D.id 
                        INNER JOIN separation S ON S.image_id = I.id 
                        INNER JOIN categorie C ON S.categorie_id = C.id 
                        INNER JOIN souscategorie SC ON S.souscategorie_id = SC.id 
                        LEFT JOIN soussouscategorie SSC ON S.soussouscategorie_id = SSC.id 
                        LEFT JOIN  saisie_controle SCTRL ON SCTRL.image_id = I.id  
                        WHERE L.dossier_id =" . $did . " AND I.exercice=" . $exercice . " AND I.supprimer = 0 " . $where;
        } else {
            $query = "SELECT I.*,S.souscategorie_id, '' AS avec_releve FROM  image I 
                        INNER JOIN lot L ON I.lot_id = L.id 
                        INNER JOIN dossier D ON L.dossier_id = D.id 
                        INNER JOIN separation S ON S.image_id = I.id 
                        INNER JOIN categorie C ON S.categorie_id = C.id 
                        INNER JOIN souscategorie SC ON S.souscategorie_id = SC.id 
                        LEFT JOIN soussouscategorie SSC ON S.soussouscategorie_id = SSC.id 
                        LEFT JOIN  saisie_controle SCTRL ON SCTRL.image_id = I.id  
                        WHERE L.dossier_id =" . $did . " AND I.exercice=" . $exercice . " AND I.supprimer = 0 AND 
                        SCTRL.banque_compte_id =" . $banquecompteid;
        }


        if ( $dscan <> 0 ) {
            $query .= " AND L.date_scan ='" . $dscan . "'";
        }

        if ( $soussouscat != -1 && isset($soussouscat) ) {
            $query .= " AND SSC.id =" . $soussouscat;
        } else {
            $query .= " AND SC.id =" . $souscat;
        }

        $query .= " ORDER BY SCTRL.periode_d1, SCTRL.periode_f1, I.nom";

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $prep = $pdo->prepare($query);

        $prep->execute();

        $res = $prep->fetchAll();
        return $res;
    }

    public function getNbImageEncours( $param, $detail = false )
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        switch ( $param['cas'] ) {
            case 1:
                $periode = "AND l.date_scan = :periode ";
                break;
            case 4:
                $periode = " ";

                break;
            default:
                $periode = "AND l.date_scan >= ";
                $periode .= ":dateDeb";
                $periode .= " AND l.date_scan <= ";
                $periode .= ":dateFin ";
        }

        if ( $param['dossier'] == 0 ) {
            $dossier = " ";
        } else {
            $dossier = "AND d.id = ";
            $dossier .= $param['dossier'] . " ";
        }

        if($detail){
            $query = "SELECT distinct(i.id) as imageId, i.nom as image, date_format(l.date_scan,'%d-%m-%Y') as date_scan, date_format(sc.date_facture,'%d-%m-%Y') as date_piece, ic.rs, i.imputation, cat.libelle_new, cat.id, pi.image_id as prioriteImageId ";
        }else{
            $query = "SELECT count(i.id) as nb_img_cours ";
        }

        $query .= "FROM image i 
                INNER JOIN lot l on l.id=i.lot_id 
                INNER JOIN dossier d on d.id=l.dossier_id
                left join separation sep on (sep.image_id = i.id)
                left join imputation_controle ic on (ic.image_id = i.id) 
                left join categorie cat on (cat.id = sep.categorie_id)
                left join saisie_controle sc on (sc.image_id = i.id) 
                left join priorite_image pi on pi.image_id = i.id
                WHERE i.exercice = " . $param['exercice'] . "
                " . $dossier . "
                " . $periode . " 
                AND i.saisie2 <= 1 
                AND i.saisie1 <= 1
                AND i.id NOT IN (SELECT img.id FROM separation sep INNER JOIN image img on img.id=sep.image_id)
                AND i.decouper=0 AND ucase(i.ext_image) = 'PDF' AND i.supprimer=0
                AND NOT (l.date_scan BETWEEN CAST('2010-01-01' AS DATE) AND CAST('2019-03-31' AS DATE))";
        $prep = $pdo->prepare($query);
        switch ( $param['cas'] ) {
            case 1:
                $now = $param['aujourdhui'];
                $prep->execute(array(
                    'periode' => $now,
                ));
                break;
            case 4:
                $prep->execute();
                break;
            default:
                $dateDeb = $param['dateDeb'];
                $dateFin = $param['dateFin'];
                $prep->execute(array(
                    'dateDeb' => $dateDeb,
                    'dateFin' => $dateFin,
                ));
        }
        $nbImagesEncours = $prep->fetchAll();
        if($detail){
            return $nbImagesEncours;
        }else{
            return (count($nbImagesEncours) == 1) ? $nbImagesEncours[0]->nb_img_cours : 0;
        }
    }

    /**
     * TAF : Nombre des doublons et trou dans RB1
     *
     * @param array $param
     * @param integer $souscategorie
     *
     * @return array
     */
    public function getRb1AControler( $param, $souscategorie = 10 )
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "SELECT sc.banque_compte_id, sc.periode_d1, sc.periode_f1, sc.solde_debut, sc.solde_fin, false as is_doublon, i.ctrl_saisie
                FROM image i 
                INNER JOIN lot l ON (i.lot_id = l.id)
                INNER JOIN dossier d ON (l.dossier_id = d.id)
                INNER JOIN site s ON (d.site_id = s.id)
                INNER JOIN client c ON (s.client_id = c.id)
                INNER JOIN separation sep ON (i.id = sep.image_id)
                INNER JOIN saisie_controle sc ON (i.id = sc.image_id)
                WHERE sep.souscategorie_id = " . $souscategorie;

        if ( $param['client'] != 0 ) {
            $query .= " AND c.id IN ( '" . implode("', '", $param['client']) . "' )";
        }

        if ( $param['dossier'] != 0 ) {
            $query .= " AND d.id = " . $param['dossier'];
        }

        $query .= " AND i.exercice = " . $param['exercice'];

        $query .= " AND i.supprimer = 0";

        $query .= " AND sc.banque_compte_id IS NOT NULL";

        $orderby = " ORDER BY sc.banque_compte_id, sc.periode_d1, sc.periode_f1, i.nom";

        switch ( $param['periode'] ) {
            case 1:
                $now = new \DateTime();
                $dateNow = $now->format('Y-m-d');

                $query .= " AND l.date_scan = :dateNow" . $orderby;

                $prep = $pdo->prepare($query);

                $prep->execute(array(
                    'dateNow' => $dateNow
                ));
                break;

            case 2:
                $dateNow = new \DateTime();
                $now = clone $dateNow;
                $oneWeek = date_modify($dateNow, "-7 days");
                $dateDeb = $oneWeek->format('Y-m-d');
                $dateFin = $now->format('Y-m-d');

                $query .= " AND l.date_scan >= :dateDeb";
                $query .= " AND l.date_scan <= :dateFin" . $orderby;

                $prep = $pdo->prepare($query);

                $prep->execute(array(
                    'dateDeb' => $dateDeb,
                    'dateFin' => $dateFin,
                ));
                break;

            case 3:
                $dateNow = new \DateTime();
                $now = clone $dateNow;
                $oneMonth = date_modify($dateNow, "-1 months");
                $dateDeb = $oneMonth->format('Y-m-d');
                $dateFin = $now->format('Y-m-d');

                $query .= " AND l.date_scan >= :dateDeb";
                $query .= " AND l.date_scan <= :dateFin" . $orderby;

                $prep = $pdo->prepare($query);

                $prep->execute(array(
                    'dateDeb' => $dateDeb,
                    'dateFin' => $dateFin,
                ));
                break;

            case 4:
                $prep = $pdo->prepare($query . $orderby);
                $prep->execute();
                break;

            case 5:
                $query .= " AND l.date_scan >= :dateDeb";
                $query .= " AND l.date_scan <= :dateFin" . $orderby;

                $prep = $pdo->prepare($query);

                $prep->execute(array(
                    'dateDeb' => $param['perioddeb'],
                    'dateFin' => $param['periodfin'],
                ));
                break;

        }

        $resultat = $prep->fetchAll();

        $nbDoublon = 0;

        $nbTrou = 0;

        $nbImgSaisieKo = 0;

        $response = array();

        for ( $i = 0; $i < count($resultat) - 1; $i++ ) {
            if ( !$resultat[$i]->is_doublon ) {

                $ti = $resultat[$i];

                for ( $j = $i + 1; $j < count($resultat); $j++ ) {

                    $tj = $resultat[$j];

                    if ( ($tj->periode_d1 === $ti->periode_d1 && $tj->periode_f1 === $ti->periode_f1 && $tj->solde_debut === $ti->solde_debut && $tj->solde_fin === $ti->solde_fin) && ($tj->banque_compte_id == $ti->banque_compte_id)
                    ) {
                        $nbDoublon++;
                        $resultat[$j]->is_doublon = true;
                    }
                }
            }
        }

        for ( $i = 0; $i < count($resultat) - 1; $i++ ) {
            $ti = $resultat[$i];
            $tj = $resultat[$i + 1];

            if ( !$tj->is_doublon ) {
                if ( ($ti->solde_fin != $tj->solde_debut) && ($tj->banque_compte_id == $ti->banque_compte_id) ) {
                    $nbTrou++;
                }
            }
        }

        for ( $i = 0; $i < count($resultat) - 1; $i++ ) {
            $ti = $resultat[$i];

            if ( $ti->ctrl_saisie < 2 ) {
                $nbImgSaisieKo++;
            }
        }

        $response['doublon'] = $nbDoublon;

        $response['trou'] = $nbTrou;

        $response['acontroler'] = $nbTrou + $nbDoublon;

        $response['imgSaisieKo'] = $nbImgSaisieKo;

        return $response;

    }

    /**
     * @param BanqueCompte $banqueCompte
     * @param $exercice
     * @param bool $debut
     * @return float
     */
    public function getSoldes( BanqueCompte $banqueCompte, $exercice, $debut = true )
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $banquecompte_id = $banqueCompte->getId();

        if ( $debut ) {
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

            $prep = $pdo->prepare($query);
            $prep->execute(array(
                'banquecompte_id' => $banquecompte_id,
                'exercice' => $exercice
            ));

            $infoReleve = $prep->fetchAll();


            if ( count($infoReleve) > 0 ) {
                return $infoReleve[0]->solde_deb;
            } else {
                return 0;
            }

        } else {
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

            $prep = $pdo->prepare($query);
            $prep->execute(array(
                'banquecompte_id' => $banquecompte_id,
                'exercice' => $exercice
            ));

            $infoReleve = $prep->fetchAll();

            if ( count($infoReleve) > 0 ) {
                return $infoReleve[0]->solde_fin;
            } else {
                return 0;
            }

        }
    }

    public function getMouvement( $exercice, $banquecompte_id )
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $query = "select ROUND(SUM(r.credit) - SUM(r.debit),2) as mouvement from releve r                          
                inner join image i on i.id = r.image_id
                inner join separation sep on sep.image_id = i.id
                inner join lot l on l.id = i.lot_id
                inner join banque_compte bc on bc.id = r.banque_compte_id
                where i.exercice = :exercice
                and i.supprimer = 0
                and sep.souscategorie_id = 10
                and r.operateur_id is null               
                and (r.banque_compte_id IS NOT NULL)               
                and (bc.id = :banquecompte_id)";
        $prep = $pdo->prepare($query);
        $prep->execute(array(
            'exercice' => $exercice,
            'banquecompte_id' => $banquecompte_id
        ));
        $mouvement = $prep->fetchAll();
        return $mouvement[0]->mouvement;
    }

     public function getListeImputeSansImage( $clientId, $dossierId, $tab_comptes_exist )
    {
        $con = new CustomPdoConnection();    
        $pdo = $con->connect();
        $query = "select c.id as client_id, d.id as dossier_id, b.nom as banque, d.status, c.nom as clients, d.nom as dossier, d.status, d.status_debut, d.cloture,
                (case
                    when length(bc.numcompte) >= 11 then substring(bc.numcompte, length(bc.numcompte)-10, length(bc.numcompte))
                    else bc.numcompte
                end) as comptes, rtva.libelle as regime_tva, bc.numcompte, bc.id as banque_compte_id, bc.etat, d.tva_date as ech, d.tva_mode, d.debut_activite
                from dossier d
                inner join site s on (s.id = d.site_id)
                inner join client c on (c.id = s.client_id)
                left join banque_compte bc on (bc.dossier_id = d.id)
                left join banque b on (b.id = bc.banque_id)
                left join regime_tva rtva on (d.regime_tva_id = rtva.id)
                where c.status = 1 ";
        if ( $dossierId[0] != 0 AND $clientId[0] != 0 ) {
            $query .= "and c.id IN ( '" . implode("', '", $clientId) . "' )
                       group by bc.numcompte, d.id 
                       having client_id IN ( '" . implode("', '", $clientId) . "' ) and dossier_id = " . $dossierId . " 
                       and (bc.numcompte is null or bc.numcompte NOT IN ( '" . implode("', '", $tab_comptes_exist) . "'))";
        } else if ( $dossierId[0] == 0 AND $clientId[0] != 0 ) {
            $query .= "and c.id IN ( '" . implode("', '", $clientId) . "' )
                       group by bc.numcompte, d.id
                       having client_id IN ( '" . implode("', '", $clientId) . "' )
                       and (bc.numcompte is null or bc.numcompte NOT IN ( '" . implode("', '", $tab_comptes_exist) . "'))";
        } else if( $dossierId[0] != 0 AND $clientId[0] == 0 ) {
            $query .= "and d.id IN ( '" . implode("', '", $dossierId) . "' )
                       group by bc.numcompte, d.id
                       having dossier_id IN ( '" . implode("', '", $dossierId) . "' )
                       and (bc.numcompte is null or bc.numcompte NOT IN ( '" . implode("', '", $tab_comptes_exist) . "'))";
        } else { //tous
            $query .= "group by bc.numcompte, d.id
                       and (bc.numcompte is null or bc.numcompte NOT IN ( '" . implode("', '", $tab_comptes_exist) . "'))";
        }

        $prep = $pdo->prepare($query);

        $prep->execute();
        return $prep->fetchAll();
    }

    public function getDerniereDemandeDrt($dossierId, $exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $chrono = "AND e.date_envoi >= ";
        $chrono .= ":dateDeb";
        $chrono .= " AND e.date_envoi <= ";
        $chrono .= ":dateFin ";
        $query = "SELECT ABS(ei.numero) as rang_ei, date_format(ei.date_creation,'%d/%m/%Y') as date_envoi, d.id as dossierId
            FROM echange e
            INNER JOIN echange_type et on (et.id = e.echange_type_id)
            INNER JOIN echange_item ei on (ei.echange_id = e.id)
            INNER JOIN dossier d on (d.id = e.dossier_id)
            LEFT JOIN echange_reponse ep on (ep.echange_item_id = ei.id)
            WHERE e.exercice = ".$exercice."
            AND (et.id = 1 OR et.id = 2)
            AND ei.status = 0
            AND d.id IN ( '" . implode("', '", $dossierId) . "' )
            ".$chrono."
            AND ei.supprimer = 0
            ORDER BY d.nom, rang_ei, ep.numero DESC
            LIMIT 1";
        $prep = $pdo->prepare($query);
        $prep->execute(array(
            'dateDeb' => $exercice.'-01-01',
            'dateFin' => $exercice.'-12-31',
        ));
        return $prep->fetchAll();
    }

    public function getListImageBanqueGestionTache( $did, $exercice, $dscan, $souscat, $soussouscat, $etape, $banquecompteid )
    {
        if ( $exercice == '' ) {
            $now = new \DateTime();
            $exercice = $now->format('Y');
        }

        if ( $etape == 1 ) {
            $where = "AND I.saisie1 > 0 ";
        } else {
            $where = "AND I.ctrl_saisie >= 2 ";
        }

        //releve bancaire
        $banquecompte = $this->getEntityManager()
                             ->getRepository('AppBundle:BanqueCompte')
                             ->find($banquecompteid);


        if ( !$banquecompte ) {
            $query = "SELECT I.*, S.souscategorie_id, '' AS avec_releve FROM  image I 
                        INNER JOIN lot L ON I.lot_id = L.id 
                        INNER JOIN dossier D ON L.dossier_id = D.id 
                        INNER JOIN separation S ON S.image_id = I.id 
                        INNER JOIN categorie C ON S.categorie_id = C.id 
                        INNER JOIN souscategorie SC ON S.souscategorie_id = SC.id 
                        LEFT JOIN soussouscategorie SSC ON S.soussouscategorie_id = SSC.id 
                        LEFT JOIN  saisie_controle SCTRL ON SCTRL.image_id = I.id  
                        WHERE L.dossier_id =" . $did . " AND I.exercice=" . $exercice . " AND I.supprimer = 0 " . $where;
        } else {
            $query = "SELECT I.*,S.souscategorie_id, '' AS avec_releve FROM  image I 
                        INNER JOIN lot L ON I.lot_id = L.id 
                        INNER JOIN dossier D ON L.dossier_id = D.id 
                        INNER JOIN separation S ON S.image_id = I.id 
                        INNER JOIN categorie C ON S.categorie_id = C.id 
                        INNER JOIN souscategorie SC ON S.souscategorie_id = SC.id 
                        LEFT JOIN soussouscategorie SSC ON S.soussouscategorie_id = SSC.id 
                        LEFT JOIN  saisie_controle SCTRL ON SCTRL.image_id = I.id  
                        WHERE L.dossier_id =" . $did . " AND I.exercice=" . $exercice . " AND I.supprimer = 0 AND 
                        SCTRL.banque_compte_id =" . $banquecompteid;
        }


        if ( $dscan <> 0 ) {
            $query .= " AND L.date_scan ='" . $dscan . "'";
        }

        if ( $soussouscat != -1 && isset($soussouscat) ) {
            $query .= " AND SSC.id =" . $soussouscat;
        } else {
            $query .= " AND SC.id =" . $souscat;
        }

        $query .= " ORDER BY SCTRL.periode_d1, SCTRL.periode_f1, I.nom";

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $prep = $pdo->prepare($query);

        $prep->execute();

        $res = $prep->fetchAll();
        return $res;
    }

    public function getImageAtraiterByDatescan($dossierTab, $exercice)
    {
        $query = "SELECT count(i.id) as nb FROM image i
                inner join lot l on l.id = i.lot_id
                where l.dossier_id IN ( '" . implode("', '", $dossierTab) . "' ) 
                and i.exercice = ".$exercice." 
                and i.supprimer = 0";
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $prep = $pdo->prepare($query);
        $prep->execute();

        $res = $prep->fetchAll();
        return (!empty($res)) ? $res[0]->nb : 0;
    }

    public function getEncaissementPm($param)
    {
        $param['client']  = Boost::deboost($param['client'],$this);
        $query = "SELECT count(*) as nb , d.id, bc.numcompte, d.nom as dossierNom, d.cloture
                from releve r
                inner join separation sep on (sep.image_id = r.image_id)
                left join banque_compte bc on (bc.id = r.banque_compte_id)
                left join image i on (i.id = r.image_id)
                inner join lot l on (l.id = i.lot_id)
                inner join dossier d on (d.id = l.dossier_id)
                inner join site s on (s.id = d.site_id)
                left join cle_dossier cle on (cle.id = r.cle_dossier_id)
                where s.client_id = ".$param['client']."
                and i.exercice = ".$param['exercice']."
                and r.image_flague_id is null
                and sep.souscategorie_id = 10
                and i.supprimer = 0
                and r.operateur_id is null
                and ROUND(r.credit - r.debit,2) > 0
                and not (r.ecriture_change = 1 and r.maj = 3)
                and (r.cle_dossier_id is null or cle.pas_piece = 0)";

        if ($param['dossier'] != 0) {
            $query .= " AND d.id = " . $param['dossier'];
        }
        $query .= " group by d.id, bc.numcompte";

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $prep = $pdo->prepare($query);
        $prep->execute();
        return $prep->fetchAll();
    }

    public function getEcritureChange($param, $user)
    {
        $con              = new CustomPdoConnection();
        $pdo              = $con->connect();
        $param['client']  = Boost::deboost($param['client'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }
        $query = "SELECT count(*) as nb_ec, d.id, bc.numcompte, d.nom as dossierNom, d.cloture
            from image i
            left join releve r on (i.id = r.image_id)
            inner join lot l on (l.id = i.lot_id)
            inner join dossier d on (d.id = l.dossier_id)
            inner join site s on (s.id = d.site_id)
            inner join client c on (c.id = s.client_id)
            inner join banque_compte bc on (bc.id = r.banque_compte_id and bc.dossier_id = d.id)
            inner join banque b on (b.id=bc.banque_id)
            inner join separation sep on (sep.image_id = i.id)  
            inner join souscategorie ssc on (sep.souscategorie_id = ssc.id) " . $inner_user . "
            where i.supprimer = 0
            and i.exercice = " . $param['exercice'] . "
            and c.status = 1
            and r.operateur_id is null
            and r.ecriture_change = 1
            and sep.souscategorie_id IS NOT NULL 
            and ssc.id = 10
            and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and d.id = " . $param['dossier'];
        }
        $query .= " and (d.status = 1";
        $query .= " or ( d.status <> 1 
                    and d.status_debut is not null 
                    and d.status_debut > " . $param['exercice'] . " ))
                    group by d.id, bc.numcompte";

        $prep = $pdo->prepare($query);
        $prep->execute();
        return $prep->fetchAll();
    }

    public function getDecaissementPm($param)
    {
        $param['client']  = Boost::deboost($param['client'],$this);
        $query = "SELECT count(*) as nb , d.id, bc.numcompte, d.nom as dossierNom, d.cloture
                from releve r
                inner join separation sep on (sep.image_id = r.image_id)
                left join banque_compte bc on (bc.id = r.banque_compte_id)
                left join image i on (i.id = r.image_id)
                inner join lot l on (l.id = i.lot_id)
                inner join dossier d on (d.id = l.dossier_id)
                inner join site s on (s.id = d.site_id)
                left join cle_dossier cle on (cle.id = r.cle_dossier_id)
                where s.client_id = ".$param['client']."
                and i.exercice = ".$param['exercice']."
                and r.image_flague_id is null
                and sep.souscategorie_id = 10
                and i.supprimer = 0
                and r.operateur_id is null
                and ROUND(r.credit - r.debit,2) < 0
                and not (r.ecriture_change = 1 and r.maj = 3)
                and r.libelle NOT LIKE '%CHQ%' and r.libelle NOT LIKE '%CHEQUE%'
                and (r.cle_dossier_id is null or cle.pas_piece = 0) 
                and not (r.ecriture_change = 1 and r.maj = 3)";

        if ($param['dossier'] != 0) {
            $query .= " AND d.id = " . $param['dossier'];
        }
        $query .= " group by d.id, bc.numcompte";

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $prep = $pdo->prepare($query);
        $prep->execute();
        return $prep->fetchAll();
    }

    public function getListeImputeForPm($param)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $data = array();

        //client dossier principal
        if ( $param['client'][0] == 0 && $param['dossier'][0] == 0 ) {
            $clientOrDossier          = " ";
            $clientOrDossierMois      = " ";
            $clientOrDossierALettrer  = " ";
        } else if ( $param['client'][0] == 0 && $param['dossier'][0] != 0 ) {
            $clientOrDossier          = "AND bc.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";
            $clientOrDossierMois      = "AND rmm.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";
            $clientOrDossierALettrer  = "AND bcale.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";
        } else if ( $param['client'][0] != 0 && $param['dossier'][0] == 0 ) {
            $clientOrDossier         = "AND c.id IN ( '" . implode("', '", $param['client']) . "' )";

            $clientOrDossierMois     = "AND cm.id IN ( '" . implode("', '", $param['client']) . "' )";

            $clientOrDossierALettrer = "AND cale.id IN ( '" . implode("', '", $param['client']) . "' )";
        } else if ( $param['client'][0] != 0 && $param['dossier'][0] != 0 ) {
            $clientOrDossier          = "AND c.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossier         .= " AND bc.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierMois      = "AND cm.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossierMois     .= " AND rmm.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";

            $clientOrDossierALettrer  = "AND cale.id IN ( '" . implode("', '", $param['client']) . "' )";
            $clientOrDossierALettrer .= " AND bcale.dossier_id IN ( '" . implode("', '", $param['dossier']) . "' ) ";
        } 

        //get imputees
        $query = "select count(*) as nb_r, d.status, d.status_debut, b.nom as banque, c.nom as clients, d.nom as dossier, d.cloture, bc.id as banquecompte_id, d.id as dossier_id, c.id as client_id,
            (case
                when length(bc.numcompte) >= 11 then substring(bc.numcompte, length(bc.numcompte)-10, length(bc.numcompte))
                else bc.numcompte
            end) as comptes, rtva.libelle as regime_tva, bc.numcompte, bc.etat, i.valider, bc.id as banque_compte_id, d.tva_date as ech, d.tva_mode, d.debut_activite, bc.etat as bc_etat, b.id as banque_id,
            
            (select rmm.mois
            from releve rm
            inner join releve_manquant rmm on rmm.banque_compte_id = rm.banque_compte_id
            inner join image im on im.id = rm.image_id
            inner join lot lm on (lm.id = im.lot_id)
            inner join dossier dm on (dm.id = lm.dossier_id)
            inner join site sm on sm.id=dm.site_id
            inner join client cm on cm.id=sm.client_id
            inner join banque_compte bcm on (bcm.id = rm.banque_compte_id and bcm.dossier_id = dm.id)
            inner join banque bm on (bm.id=bcm.banque_id)
            inner join separation sepm on (sepm.image_id = im.id)  
            inner join souscategorie sscm on (sepm.souscategorie_id = sscm.id) 
            where rmm.exercice = " . $param['exercice'] . "
            and rm.banque_compte_id=r.banque_compte_id 
            and cm.status = 1
            and rm.operateur_id is null
            and sepm.souscategorie_id IS NOT NULL 
            and sscm.id = 10 
            " . $clientOrDossierMois . "
            group by bcm.numcompte) as mois,

            (SELECT count(rale.id) as nb_alettrer
            from image iale
            left join releve rale on (iale.id = rale.image_id)
            inner join lot lale on (lale.id = iale.lot_id)
            inner join dossier dale on (dale.id = lale.dossier_id)
            inner join site sale on (sale.id = dale.site_id)
            inner join client cale on (cale.id = sale.client_id)
            inner join banque_compte bcale on (bcale.id = rale.banque_compte_id and bcale.dossier_id = dale.id)
            inner join banque bale on (bale.id=bcale.banque_id)
            inner join separation sepale on (sepale.image_id = iale.id)  
            inner join souscategorie sscale on (sepale.souscategorie_id = sscale.id) 
            where iale.supprimer = 0
            and iale.exercice = " . $param['exercice'] . "
            and cale.status = 1
            and rale.banque_compte_id=r.banque_compte_id 
            and rale.operateur_id is null
            and rale.flaguer = 1
            and sepale.souscategorie_id IS NOT NULL 
            and sscale.id = 10 
            " . $clientOrDossierALettrer . "
            group by bcale.numcompte) as a_lettrer

            from image i
            left join releve r on (r.image_id = i.id)
            inner join lot l on (l.id = i.lot_id)
            inner join dossier d on (l.dossier_id = d.id)
            inner join site s on (s.id = d.site_id)
            inner join client c on (c.id = s.client_id)
            inner join banque_compte bc on (bc.dossier_id = d.id and bc.id = r.banque_compte_id)
            inner join banque b on (b.id = bc.banque_id)
            inner join separation sep on (sep.image_id = i.id)  
            inner join souscategorie ssc on (sep.souscategorie_id = ssc.id) 
            left join regime_tva rtva on (d.regime_tva_id = rtva.id)
            where i.exercice = " . $param['exercice'] . " and i.supprimer = 0 
            and c.status = 1
            and d.status = 1
            and r.operateur_id is null
            and sep.souscategorie_id IS NOT NULL 
            and ssc.id = 10 
            " . $clientOrDossier . "
            group by bc.numcompte";

        $prep = $pdo->prepare($query);
        $prep->execute();
        return $prep->fetchAll();
    }

    public function dossierMoisDetailsFilter($param, $anterieur = false)
    {
        
        

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }
        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(i.id) as nb,d.nom as dossier, d.id as dossier_id
                    from image i
                    inner join lot l on (i.lot_id=l.id)
                    inner join dossier d on (l.dossier_id=d.id)
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.status = 1
                    and c.id = :client_id
                    and i.exercice = ${param['exercice']}";

        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        if ($anterieur) {
            if ($param['analyse'] == 1) {
                $mois = $param['mois'];

                $and .= "   and date_format(l.date_scan,'%Y-%m') < :mois
                            and date_format(d.date_creation,'%Y-%m') < '${mois}'";

                // $and .= "   and date_format(l.date_scan,'%Y-%m') < :mois";
                // $and .= "   and date_format(d.date_creation,'%Y-%m') = '${mois}'";
            } else {
                $mois = $param['mois'];
                $and .= "   and date_format(d.date_creation,'%Y-%m') = '${mois}'";
                $and .= "   and date_format(l.date_scan,'%Y-%m') < :mois";
            }
        } else {
            if ($param['analyse'] == 1) {
                $mois = $param['mois'];
                $and .= "   and date_format(l.date_scan,'%Y-%m') = :mois";
                $and .= "   and date_format(d.date_creation,'%Y-%m') = '${mois}'";

            } else {
                $mois = $param['mois'];
                $and .= "   and date_format(l.date_scan,'%Y-%m') and date_format(l.date_scan,'%Y-%m') <= :mois";
                $and .= "   and date_format(d.date_creation,'%Y-%m') = '${mois}'";
            }
        }

        $query .=  $and;

        $query .= " group by d.id";

        $query .= " having nb > 1";

        $prep = $pdo->prepare($query);

        $options = array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
            'mois' => $param['mois']
        );


        $prep->execute($options);


        $resultat = $prep->fetchAll();

        return $resultat;
    
    
    
    }


    public function nbDossiersMoisFilter($param, $anterieur = false)
    {
        

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }
        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(i.id) as nb,d.nom as dossier,d.cloture as cloture,date_format(l.date_scan,'%Y-%m') as date_scan, d.id as dossier_id, c.nom as client, date_format(d.date_creation,'%Y-%m') as date_creation
                    from image i
                    inner join lot l on (i.lot_id=l.id)
                    inner join dossier d on (l.dossier_id=d.id)
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.status = 1
                    and c.id = :client_id
                    and i.exercice = ${param['exercice']}";

        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        if ($anterieur) {
            if ($param['analyse'] == 1) {
                $mois = $param['mois'];

                $and .= "   and date_format(l.date_scan,'%Y-%m') < :mois
                            and date_format(d.date_creation,'%Y-%m') < '${mois}'";

                // $and .= "   and date_format(l.date_scan,'%Y-%m') < :mois";
                // $and .= "   and date_format(d.date_creation,'%Y-%m') = '${mois}'";
            } else {
                $mois = $param['mois'];
                $and .= "   and date_format(d.date_creation,'%Y-%m') = '${mois}'";
                $and .= "   and date_format(l.date_scan,'%Y-%m') < :mois";
            }
        } else {
            if ($param['analyse'] == 1) {
                $mois = $param['mois'];
                $and .= "   and date_format(l.date_scan,'%Y-%m') = :mois";
                $and .= "   and date_format(d.date_creation,'%Y-%m') = '${mois}'";

            } else {
                $mois = $param['mois'];
                $and .= "   and date_format(l.date_scan,'%Y-%m') and date_format(l.date_scan,'%Y-%m') <= :mois";
                $and .= "   and date_format(d.date_creation,'%Y-%m') = '${mois}'";
            }
        }

        $query .=  $and;

        $query .= " group by d.id";

        // $query .= " having nb = 1";

        switch ($param['operator']) {
            case 1:
                $query .= " having nb = :value";
                break;
            case 2:
                $query .= " having nb > :value";
                break;
            case 3:
                $query .= " having nb < :value";
                break;
        }

        $prep = $pdo->prepare($query);

        $options = array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
            'mois' => $param['mois'],
            'value'     => $param['value']
        );


        $prep->execute($options);


        $resultat = $prep->fetchAll();

        return count($resultat);
    
    
    }


    public function imagesMoisEvolutionFilter($param, $anterieur = false)
    {

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }
        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(i.id) as nb,d.nom as dossier,d.cloture as cloture,date_format(l.date_scan,'%Y-%m') as date_scan, d.id as dossier_id, c.nom as client, date_format(d.date_creation,'%Y-%m') as date_creation
                    from image i
                    inner join lot l on (i.lot_id=l.id)
                    inner join dossier d on (l.dossier_id=d.id)
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.status = 1
                    and c.id = :client_id
                    and i.exercice = ${param['exercice']}";

        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        if ($anterieur) {
            if ($param['analyse'] == 1) {
                $and .= "   and date_format(l.date_scan,'%Y-%m') < :mois";
            } else {
                $and .= "   and date_format(l.date_scan,'%Y-%m') < :mois";
            }
        } else {
            if ($param['analyse'] == 1) {
                $and .= "   and date_format(l.date_scan,'%Y-%m') = :mois";

            } else {
                $and .= "   and date_format(l.date_scan,'%Y-%m') <= :mois";
            }
        }

        $query .=  $and;

        $query .= " group by d.id";

        switch ($param['operator']) {
            case 1:
                $query .= " having nb = :value";
                break;
            case 2:
                $query .= " having nb > :value";
                break;
            case 3:
                $query .= " having nb < :value";
                break;
        }


        $prep = $pdo->prepare($query);

        $options = array(
            'client_id' => $param['client'],
            'exercice'  => $param['exercice'],
            'mois'      => $param['mois'],
            'value'     => $param['value']
        );


        $prep->execute($options);

        $resultat = $prep->fetchAll();

        return $resultat;
    
    }

    public function imagesMoisEvolution($param, $anterieur = false)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }
        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(i.id) as nb
                    from image i
                    inner join lot l on (i.lot_id=l.id)
                    inner join dossier d on (l.dossier_id=d.id)
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.status = 1
                    and c.id = :client_id
                    and i.exercice = ${param['exercice']}";

        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        if ($anterieur) {
            if ($param['analyse'] == 1) {

                $and .= "   and date_format(l.date_scan,'%Y-%m') < :mois";

                // $and .= " and (date_format(d.date_creation,'%Y-%m') < '${param['mois']}')";

                // $and .= " and date_format(d.date_creation,'%Y-%m') = date_format(l.date_scan,'%Y-%m')";

            } else {
                $and .= "   and date_format(l.date_scan,'%Y-%m') < :mois";
            }
        } else {


            if ($param['analyse'] == 1 || $param['analyse'] != 1) {
                $and .= "   and date_format(l.date_scan,'%Y-%m') = :mois";
                // $and .= " and (date_format(d.date_creation,'%Y-%m') = '${param['mois']}')";

            } else {

                $debutMois = $param['exercice']. '-01';
                $and .= "   and date_format(l.date_scan,'%Y-%m') and date_format(l.date_scan,'%Y-%m') <= :mois";
            }
        }

        $query .=  $and;

        // $query .= " group by c.id";

        $prep = $pdo->prepare($query);

        $options = array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
            'mois' => $param['mois']
        );


        $prep->execute($options);


        $resultat = $prep->fetchAll();

        if (empty($resultat)) {
            return 0;
        }


        return $resultat[0]->nb;
    }

    public function dossierAnterieurList($param)
    {

        
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }

        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select d.id as dossier_id, d.nom as dossier
                    from dossier d
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.id = :client_id";


        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";


        $and .= " and (date_format(d.date_creation,'%Y-%m') < :mois or d.date_creation is null)";




        $query .=  $and;

        // $query .= " group by c.id";

        $prep = $pdo->prepare($query);

        // var_dump($prep);die();

        // var_dump($param,$query);die();

        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
            'mois' => $param['mois']
        ));

        $resultat = $prep->fetchAll();

        return $resultat;
    
    }

    public function nbDossierAnterieur($param)
    {
        
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }

        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(d.id) as nb, d.nom as dossier
                    from dossier d
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.id = :client_id";


        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";


        $and .= " and (date_format(d.date_creation,'%Y-%m') < :mois or d.date_creation is null)";




        $query .=  $and;

        $query .= " group by c.id";

        $prep = $pdo->prepare($query);

        // var_dump($prep);die();

        // var_dump($param,$query);die();

        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
            'mois' => $param['mois']
        ));

        $resultat = $prep->fetchAll();

        if (empty($resultat)) {
            return 0;
        }

        return $resultat[0]->nb;
    }

    public function imagesEvolution($param)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }
        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(i.id) as nb,d.nom as dossier,d.cloture as cloture,date_format(l.date_scan,'%Y-%m') as date_scan, d.id as dossier_id, c.nom as client
                    from image i
                    inner join lot l on (i.lot_id=l.id)
                    inner join dossier d on (l.dossier_id=d.id)
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.status = 1
                    and c.id = :client_id
                    and i.exercice = ${param['exercice']}";

        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        $debutMois = $param['exercice']. '-01';
        $finMois = $param['exercice']. '-12';

        $and .= " and date_format(l.date_scan,'%Y-%m') >= '". $debutMois ."' and date_format(l.date_scan,'%Y-%m') <= '". $finMois ."'";

        if ($param['analyse'] == 1) {
          $and .= " and date_format(d.date_creation,'%Y-%m') <= '". $finMois ."'";
        }

        $query .=  $and;

        $prep = $pdo->prepare($query);


        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
        ));

        $resultat = $prep->fetchAll();

        if (empty($resultat)) {
            return 0;
        }

        return $resultat[0]->nb;

    }

    public function dossierMoisDetails($param)
    {
        
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }

        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select d.id as dossier_id, d.nom as dossier
                    from dossier d
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.id = :client_id";


        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        if ($param['analyse'] == 1) {
            $and .= " and (date_format(d.date_creation,'%Y-%m') = '${param['mois']}')";
        }

        $query .=  $and;

        // $query .= " group by c.id";

        $prep = $pdo->prepare($query);


        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
        ));

        $resultat = $prep->fetchAll();

        return $resultat;
    
    }

    public function nbDossiersMois($param)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }

        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(d.id) as nb, d.nom as dossier
                    from dossier d
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.id = :client_id";


        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        if ($param['analyse'] == 1) {
            $and .= " and (date_format(d.date_creation,'%Y-%m') = '${param['mois']}')";
        }

        $query .=  $and;

        $query .= " group by c.id";

        $prep = $pdo->prepare($query);


        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
        ));

        $resultat = $prep->fetchAll();

        if (empty($resultat)) {
            return 0;
        }

        return $resultat[0]->nb;
    }


    public function nbDossiersEvolution($param)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }

        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(d.id) as nb
                    from dossier d
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.id = :client_id";


        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        $year = date("Y");

        $debutMois = $param['exercice'] . '-01';

        $finMois = $param['exercice'] . '-12';


        if ($param['analyse'] == 1) {
            // $and .="   and( date_format(d.date_creation,'%Y-%m') >= ${debutMois} and (date_format(d.date_creation,'%Y-%m') <= ${finMois} ))";
            $and .="   and(date_format(d.date_creation,'%Y-%m') >= '${debutMois}')";
            $and .="   and(date_format(d.date_creation,'%Y-%m') <= '${finMois}')";
        }



        $query .=  $and;

        $query .= " group by c.id";

        $prep = $pdo->prepare($query);

        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice']
        ));

        $resultat = $prep->fetchAll();

        if (empty($resultat)) {
            return 0;
        }

        return $resultat[0]->nb;



    }

    public function nbDosierClient($param)
    {
        
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }

        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(d.id) as nb
                    from dossier d
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.id = :client_id";


        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";


        $query .=  $and;

        $query .= " group by c.id";

        $prep = $pdo->prepare($query);

        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice']
        ));

        $resultat = $prep->fetchAll();

        if (empty($resultat)) {
            return 0;
        }

        return $resultat[0]->nb;
    
    }


    public function nbDossiersEvolutionNMoinsUn($param)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }

        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(d.id) as nb
                    from dossier d
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.id = :client_id";


        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        // $and .="    and (date_format(d.date_creation,'%Y-%m') <= :moisFin or d.date_creation is null)";


        $query .=  $and;

        $query .= " group by c.id";

        $prep = $pdo->prepare($query);

        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
            // 'moisFin' => $param['exercice'] . '-12'
        ));

        $resultat = $prep->fetchAll();

        if (empty($resultat)) {
            return 0;
        }

        return $resultat[0]->nb;
    }


    public function dossiersMoisFilter($param)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }

        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(d.id) as nb, d.nom as dossier, d.id as dossier_id
                    from dossier d
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.id = :client_id";


        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        $and .= " and (date_format(d.date_creation,'%Y-%m') <= :mois)";


        $query .=  $and;

        $query .= " group by d.id";

        $prep = $pdo->prepare($query);

        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
            'mois' => $param['mois']
        ));

        $resultat = $prep->fetchAll();

        return $resultat;

    }

    public function imgMoisEvolutionFilter($param)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        $and .= " and d.id = " . $param['dossier'];

        // if ($param['dossier'] != '0') {
        //     $param['dossier'] = Boost::deboost($param['dossier'],$this);
        //     $and .= " and d.id = " . $param['dossier'];
        // }

        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(i.id) as nb,d.nom as dossier,d.cloture as cloture,date_format(l.date_scan,'%Y-%m') as date_scan, d.id as dossier_id, c.nom as client
                    from image i
                    inner join lot l on (i.lot_id=l.id)
                    inner join dossier d on (l.dossier_id=d.id)
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.status = 1
                    and c.id = :client_id
                    and i.exercice = ${param['exercice']}";

        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        if ($param['analyse'] == 1) {
            $and .= "   and date_format(l.date_scan,'%Y-%m') = :mois";
        } else {
            $and .= "   and date_format(l.date_scan,'%Y-%m') = :mois";
            // $debutMois = $param['exercice']. '-01';
            // $and .= "   and (date_format(l.date_scan,'%Y-%m') >= '". $debutMois ."' and date_format(l.date_scan,'%Y-%m') <= :mois)";
        }

        $and .= "   group by d.id";


        $query .=  $and;
        

        $prep = $pdo->prepare($query);


        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
            'mois' => $param['mois']
        ));

        $resultat = $prep->fetchAll();

        if (empty($resultat)) {
            return 0;
        }

        return $resultat[0]->nb;
    }

    public function totalImagesEvolution($param)
    {
        
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        if ($param['dossier'] != '0') {
            $param['dossier'] = Boost::deboost($param['dossier'],$this);
            $and .= " and d.id = " . $param['dossier'];
        }
        if ($param['site'] != '0') {
            $param['site'] = Boost::deboost($param['site'],$this);
            $and .= " and s.id = " . $param['site'];
        }

        $query = "  select count(i.id) as nb,d.nom as dossier,d.cloture as cloture,date_format(l.date_scan,'%Y-%m') as date_scan, d.id as dossier_id, c.nom as client, date_format(d.date_creation,'%Y-%m') as date_creation
                    from image i
                    inner join lot l on (i.lot_id=l.id)
                    inner join dossier d on (l.dossier_id=d.id)
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.status = 1
                    and c.id = :client_id
                    and i.exercice = ${param['exercice']}";

        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";

        if ($param['analyse'] == 1) {

            // $finMois = $param['exercice'] . '-12';

            // $and .= "   and date_format(l.date_scan,'%Y-%m') <= '${finMois}'";

            // $and .= " and (date_format(d.date_creation,'%Y-%m') <= '${finMois}')";

            // $and .= " and date_format(d.date_creation,'%Y-%m') = date_format(l.date_scan,'%Y-%m')";

        }
        

        $query .=  $and;


        $prep = $pdo->prepare($query);


        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice'],
        ));

        $resultat = $prep->fetchAll();

        if (empty($resultat)) {
            return 0;
        }

        // if ($anterieur) {
        //     # code...
        // var_dump($resultat);
        // die();
        // }


        return $resultat[0]->nb;
    }



    public function factFinalList($param)
    {

        $data = array();
        
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $param['client'] = Boost::deboost($param['client'],$this);

        $and="";

        $query = "  select d.id, d.nom, d.cloture
                    from dossier d
                    inner join site s on (d.site_id=s.id)
                    inner join client c on (s.client_id=c.id)
                    where c.id = :client_id";

        $and .= "   and (
                        (d.status = 1 and d.active = 1)
                        OR (d.status <> 1 
                            and d.status is not null 
                            and d.status_debut > :exercice 
                            and d.active = 1
                        )
                    )";


        $query .=  $and;

        $query .= " order by d.nom";

        $prep = $pdo->prepare($query);

        $prep->execute(array(
            'client_id'  => $param['client'],
            'exercice'   => $param['exercice']
        ));

        $resultat = $prep->fetchAll();


        foreach ($resultat as $key => $value) {

            $item['bill_chrono'] = $key + 1;
            $item['bill_tarif'] = $param['annee'];

            $dossier = $this->getEntityManager()
                            ->getRepository('AppBundle:Dossier')
                            ->find($value->id);

            $item['bill_dossier'] = $value->nom;
            $cloture = $this->getEntityManager()
                                ->getRepository('AppBundle:Dossier')
                                ->getDateCloture($dossier, $param['exercice']);
            $item['bill_cloture'] = $cloture->format('d-m-Y');
            array_push($data, $item);
        }

        return $data;

        // var_dump($resultat);die();
    
    
    }
}