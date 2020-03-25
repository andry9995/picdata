<?php

/**
 * BanqueObManquanteRepository
 *
 * @package Intranet
 *
 * @author Scriptura
 * @copyright Scriptura (c) 2019
 */

namespace AppBundle\Repository;

use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\BanqueObManquante;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Souscategorie;
use Doctrine\ORM\EntityRepository;
use AppBundle\Functions\CustomPdoConnection;
use AppBundle\Controller\Boost;

class BanqueObManquanteRepository extends EntityRepository
{

    /**
     * @var \PDO
     *
     */
    private $pdo = null;

    /**
     * Contructeur
     *
     * Création de l'instance de l'objet PDO
     */
    /*public function __construct()
    {

    }*/

    /**
     * Execution d'une requete  select sql
     *
     * @param string $query
     * @param array $param
     *
     * @return array
     */
    public function fetch($query, $param = array())
    {
        $con = new CustomPdoConnection();
        $this->pdo = $con->connect();

        $prep = $this->pdo->prepare($query);
        $prep->execute($param);
        $resultat = $prep->fetchAll();

        return $resultat;
    }

    /**
     * Insertion ou Mise à jour dans une table
     *
     * @param string $query
     */
    public function push($query, $param = array())
    {
        $prep = $this->pdo->prepare($query);
        $prep->execute($param);
    }

    /**
     * Nombre de banque compte d'un dossier
     *
     * @param integer $dossier
     *
     * @return integer
     */
    public function countBanqueComptes($dossier)
    {
        $query = "  select count(bc.id) as nb
                    from banque_compte bc 
                    where dossier_id = " . $dossier ;

        return $this->fetch($query)[0]->nb;
    }

    /**
     * Liste des ob manquantes
     *
     * @param array $param
     *
     * @return array
     */
    public function getOBManquantes($param,$user)
    {
        $param['client']  = Boost::deboost($param['client'],$this);
        $param['dossier'] = Boost::deboost($param['dossier'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select obm.souscategorie_id, obm.exercice, d.nom as nomDossier, d.cloture, obm.mois, d.id, bc.numcompte
                    from banque_ob_manquante obm
                    inner join dossier d on (obm.dossier_id = d.id)
                    inner join site s on (s.id = d.site_id)
                    inner join banque_compte bc on (bc.id = obm.banque_compte_id)
                    inner join banque bq on bq.id = bc.banque_id 
                    inner join imputation_controle ic on bc.id = ic.banque_compte_id 
                    inner join separation sep on ic.image_id = sep.image_id and sep.souscategorie_id = 10
                    inner join client c on (c.id = s.client_id)" . $inner_user . "
                    where obm.exercice = " . $param['exercice'];

        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and obm.dossier_id = " . $param['dossier'];
        }

        $query .= $by_user;
        $query .= " group by obm.id";

        return $this->fetch($query);
        
    }

    /**
     * Dosiers avec des pièces manquantes
     * OB
     *
     * @param array $param
     *
     * @return array
     */
    public function getOBPM($param,$user)
    {

        $param['client']  = Boost::deboost($param['client'],$this);

        $by_user = "";

        $user_type = $user->getAccesUtilisateur()->getType();

        $inner_user = "";

        if ($user_type > 4 && $user_type != 7) {
            $by_user = " and ud.utilisateur_id = " . $user->getId();
            $inner_user = " inner join utilisateur_dossier ud ON (ud.dossier_id = d.id)";
        }

        $query = "  select sum(obm.nb_pieces_manquantes) as nb_pieces_manquantes, d.nom as nom_dossier, d.cloture
                    from banque_ob_manquante obm
                    inner join dossier d on (obm.dossier_id = d.id)
                    inner join site s on (s.id = d.site_id)
                    inner join client c on (c.id = s.client_id)" . $inner_user . "
                    where obm.exercice = " . $param['exercice'];

        $query .= " and c.id = " . $param['client'];

        if ($param['dossier'] != 0) {
            $query .= " and obm.dossier_id = " . $param['dossier'];
        }

        $query .= $by_user;

        $query .= " group by d.id";

        return $this->fetch($query);
    }

    /**
     * @param Dossier $dossier
     * @param int $exercice
     * @param BanqueCompte $banqueCompte
     * @return BanqueObManquante[]
     */
    public function getForDossier(Dossier $dossier, BanqueCompte $banqueCompte = null, $exercice = 0)
    {
        $res = $this->createQueryBuilder('obm')
            ->where('obm.dossier = :dossier')
            ->setParameter('dossier',$dossier);

        if ($banqueCompte)
            $res = $res
                ->andWhere('obm.banqueCompte = :banqueCompte')
                ->setParameter('banqueCompte',$banqueCompte);

        if ($exercice != 0)
            $res = $res
                ->andWhere('obm.exercice = :exercice')
                ->setParameter('exercice',$exercice);

        /** @var Souscategorie[] $sousCategorieNonSaisirs */
        $sousCategorieNonSaisirs = $this->getEntityManager()->getRepository('AppBundle:SouscategoriePasSaisir')
            ->getForDossier($dossier, true);

        if (count($sousCategorieNonSaisirs) > 0)
            $res = $res->andWhere('obm.souscategorie NOT IN (:sousCategories)')
                ->setParameter('sousCategories',$sousCategorieNonSaisirs);

        return $res->getQuery()->getResult();
    }
}