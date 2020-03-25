<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 24/04/2017
 * Time: 15:56
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Categorie;
use AppBundle\Entity\Client;
use AppBundle\Entity\ControleCaisse;
use AppBundle\Entity\ControleVenteComptoir;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\ResponsableCsd;
use AppBundle\Entity\SaisieControle;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurDossier;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;


class TbimageRepository extends EntityRepository
{
    const RETARD_EXCLUDE_CURRENT_MONTH = true;

    /**
     * Listes des images par categorie/mois dans Tbimages
     *
     * @param Dossier $dossier
     * @param $exercice
     * @param Categorie $categorie
     * @param \DateTime $periode
     * @param string $banque_id
     * @return array
     * @throws \Exception
     */
    public function getImageParMois(Dossier $dossier, $exercice, Categorie $categorie, \DateTime $periode, $banque_id = '', $date_scan_search = false)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $periode_debut = new \DateTime($periode->format('Y-m-01'));
        $periode_fin = clone $periode_debut;
        $periode_fin
            ->add(new \DateInterval('P1M'))
            ->sub(new \DateInterval('P1D'));
        $images = [];

        if ($categorie->getCode() == "CODE_CLIENT" ||
            $categorie->getCode() == "CODE_FRNS" ||
            $categorie->getCode() == "CODE_NDF" ||
            $categorie->getCode() == "CODE_CAISSE"||
            $categorie->getCode() == "CODE_SOC" ||
            $categorie->getCode() == "CODE_FISC"
        ) {
//            $query = "SELECT SC.*,I.id AS image_id,I.nom AS image,I.saisie1,I.saisie2,I.ctrl_saisie,
//                    I.imputation,I.ctrl_imputation, I.exercice,L.date_scan,
//                    CAT.code AS categorie_code,CAT.libelle_new AS categorie,
//                    CAT2.code AS categorie_code2,CAT2.libelle_new AS categorie2,
//                    SCAT.libelle_new AS souscategorie,SSCAT.libelle_new AS soussouscategorie,
//                    SCAT2.libelle_new AS souscategorie2,SSCAT2.libelle_new AS soussouscategorie2,
//                    IMP.date_facture AS date_facture_imp, C_IMP.date_facture AS date_facture_c_imp
//                    FROM image I
//                    INNER JOIN lot L ON(I.lot_id = L.id)
//                    LEFT JOIN saisie_controle SC ON (SC.image_id = I.id)
//                    LEFT JOIN imputation IMP ON (IMP.image_id = I.id)
//                    LEFT JOIN imputation_controle C_IMP ON (C_IMP.image_id = I.id)
//                    INNER JOIN separation SEP ON I.id = SEP.image_id
//                    INNER JOIN categorie CAT ON SEP.categorie_id = CAT.id
//                    LEFT JOIN soussouscategorie SSCAT ON SEP.soussouscategorie_id = SSCAT.id
//                    LEFT JOIN souscategorie SCAT ON SEP.souscategorie_id = SCAT.id
//                    LEFT JOIN soussouscategorie SSCAT2 ON SC.soussouscategorie_id = SSCAT2.id
//                    LEFT JOIN souscategorie SCAT2 ON SSCAT2.souscategorie_id = SCAT2.id
//                    LEFT JOIN categorie CAT2 ON SCAT2.categorie_id = CAT2.id
//                    WHERE I.decouper = :decouper AND I.exercice = :exercice AND L.dossier_id = :dossier_id
//                    AND (CAT.id = :categorie_id OR CAT2.id = :categorie_id2) ";


            $query = "SELECT SC.*,I.id AS image_id,I.nom AS image,I.saisie1,I.saisie2,I.ctrl_saisie,
                    I.imputation,I.ctrl_imputation, I.exercice,L.date_scan,
                    CAT.code AS categorie_code,CAT.libelle_new AS categorie,                  
                    SCAT.libelle_new AS souscategorie,SSCAT.libelle_new AS soussouscategorie,                  
                    IMP.date_facture AS date_facture_imp, C_IMP.date_facture AS date_facture_c_imp
                    FROM image I
                    INNER JOIN lot L ON(I.lot_id = L.id)
                    LEFT JOIN saisie_controle SC ON (SC.image_id = I.id)
                    LEFT JOIN imputation IMP ON (IMP.image_id = I.id)
                    LEFT JOIN imputation_controle C_IMP ON (C_IMP.image_id = I.id)
                    INNER JOIN separation SEP ON I.id = SEP.image_id
                    INNER JOIN categorie CAT ON SEP.categorie_id = CAT.id
                    LEFT JOIN soussouscategorie SSCAT ON SEP.soussouscategorie_id = SSCAT.id
                    LEFT JOIN souscategorie SCAT ON SEP.souscategorie_id = SCAT.id                    
                    WHERE I.decouper = :decouper AND I.exercice = :exercice AND I.supprimer = :supprimer AND L.dossier_id = :dossier_id
                    AND (CAT.id = :categorie_id AND (SCAT.libelle IS NULL OR SCAT.libelle NOT LIKE '%doublon%')) ";


            $param = [
                'decouper' => 0,
                'exercice' => $exercice,
                'dossier_id' => $dossier->getId(),
                'categorie_id' => $categorie->getId(),
                'supprimer' => 0,
                'periode_debut' => $periode_debut->format('Y-m-d'),
                'periode_fin' => $periode_fin->format('Y-m-d')
            ];

            if(!$date_scan_search) {
                $query .="AND ((SC.date_facture >= :periode_debut AND SC.date_facture <= :periode_fin) OR (SC.periode_d1 >= :periode_debut2 AND SC.periode_d1 <= :periode_fin2))";

                $param['periode_debut2'] =  $periode_debut->format('Y-m-d');
                $param['periode_fin2'] =  $periode_fin->format('Y-m-d');
            }
            else{
                $query .="AND L.date_scan >= :periode_debut AND L.date_scan <= :periode_fin";
            }

            $prep = $pdo->prepare($query);
            $prep->execute($param);
            $saisies = $prep->fetchAll(\PDO::FETCH_OBJ);

            foreach ($saisies as $saisie) {
                $avancement = 'Saisie';
                $datefacture = '';
                $datescan = '';
                $image_categorie = '';
                if ($saisie->imputation > 1) {
                    $avancement = 'Imputée';
                }
                if ($saisie->date_facture) {
                    $datefacture = $saisie->date_facture;
                }
                elseif($saisie->periode_d1){
                    $datefacture = $saisie->periode_d1;
                }

                if ($saisie->date_scan) {
                    $datescan = $saisie->date_scan;
                }
//                if ($saisie->categorie2) {
//                    $image_categorie = $saisie->categorie2;
//                } else

                if ($saisie->categorie) {
                    $image_categorie = $saisie->categorie;
                }
                $images[] = [
                    'id' => Boost::boost($saisie->image_id),
                    'tb-detail-image' => $saisie->image . '<a onclick="showImage(event)" style="padding-left: 5px;" href="#"><i class="fa fa-file-pdf-o" style="color: red;"></i></a>',
                    'tb-detail-categorie' => $image_categorie,
                    'tb-detail-datescan' => $datescan,
                    'tb-detail-datepiece' => $datefacture,
                    'tb-detail-periode-debut' => '',
                    'tb-detail-periode-fin' => '',
                    'tb-detail-rs' => $saisie->rs,
                    'tb-detail-avancement' => $avancement,
                ];
            }

        } elseif ($categorie->getCode() == "CODE_BANQUE") {

            $query = "SELECT SC.*,I.id AS image_id,I.nom AS image,I.saisie1,I.saisie2,I.ctrl_saisie,
                    I.imputation,I.ctrl_imputation, I.exercice,L.date_scan,
                    CAT.code AS categorie_code,CAT.libelle_new AS categorie,
                    CAT2.code AS categorie_code2,CAT2.libelle_new AS categorie2,
                    SCAT.libelle_new AS souscategorie,SSCAT.libelle_new AS soussouscategorie,
                    SCAT2.libelle_new AS souscategorie2,SSCAT2.libelle_new AS soussouscategorie2,
                    BQ.nom AS banque
                    FROM image I INNER JOIN lot L ON(I.lot_id = L.id)
                    LEFT JOIN saisie_controle SC ON (SC.image_id = I.id)
                    LEFT JOIN imputation IMP ON (IMP.image_id = I.id)
                    LEFT JOIN imputation_controle C_IMP ON (C_IMP.image_id = I.id)
                    INNER JOIN separation SEP ON I.id = SEP.image_id
                    INNER JOIN categorie CAT ON SEP.categorie_id = CAT.id
                    LEFT JOIN soussouscategorie SSCAT ON SEP.soussouscategorie_id = SSCAT.id
                    LEFT JOIN souscategorie SCAT ON SEP.souscategorie_id = SCAT.id
                    LEFT JOIN soussouscategorie SSCAT2 ON SC.soussouscategorie_id = SSCAT2.id
                    LEFT JOIN souscategorie SCAT2 ON SSCAT2.souscategorie_id = SCAT2.id
                    LEFT JOIN categorie CAT2 ON SCAT2.categorie_id = CAT2.id
                    LEFT JOIN banque_compte BC ON SC.banque_compte_id = BC.id
                    LEFT JOIN banque BQ ON BC.banque_id = BQ.id
                    WHERE I.decouper = :decouper AND I.exercice = :exercice AND L.dossier_id = :dossier_id AND I.supprimer = :supprimer
                    AND (CAT.id = :categorie_id OR CAT2.id = :categorie_id2) AND (SCAT.libelle = :lib_releve1 OR SCAT2.libelle = :lib_releve2)";


            $query_param = [ 'decouper' => 0,
                'exercice' => $exercice,
                'dossier_id' => $dossier->getId(),
                'categorie_id' => $categorie->getId(),
                'categorie_id2' => $categorie->getId(),
                'periode_debut' => $periode_debut->format('Y-m-d'),
                'periode_fin' => $periode_fin->format('Y-m-d'),
                'lib_releve1' => 'releves bancaires',
                'lib_releve2' => 'releves bancaires',
                'supprimer' => 0
            ];

            if(!$date_scan_search) {
                $query .= "AND ((:periode_debut BETWEEN STR_TO_DATE(DATE_FORMAT(SC.periode_d1, '%Y-%m-01'), '%Y-%m-%d') AND SC.periode_f1) OR 
                    (:periode_fin BETWEEN SC.periode_d1 AND SC.periode_f1))
                     AND BC.id = :banque_compte_id
                    ORDER BY I.nom";

                $query_param['banque_compte_id'] = $banque_id;
            }
            else {
//                $query .= "AND L.date_scan >= :periode_debut AND L.date_scan <= :periode_fin
//                    ORDER BY I.nom";


                $query = "SELECT SC.*,I.id AS image_id,I.nom AS image,I.saisie1,I.saisie2,I.ctrl_saisie,
                                I.imputation,I.ctrl_imputation, I.exercice,L.date_scan,
                                CAT.code AS categorie_code,CAT.libelle_new AS categorie,
                                CAT2.code AS categorie_code2,CAT2.libelle_new AS categorie2,
                                SCAT.libelle_new AS souscategorie,SSCAT.libelle_new AS soussouscategorie,
                                SCAT2.libelle_new AS souscategorie2,SSCAT2.libelle_new AS soussouscategorie2,
                                BQ.nom AS banque
                                FROM image I INNER JOIN lot L ON(I.lot_id=L.id) INNER JOIN dossier D ON(L.dossier_id=D.id) 
                                INNER JOIN site S ON(D.site_id=S.id) INNER JOIN client C ON(S.client_id=C.id)
                                LEFT JOIN saisie_controle SC ON (SC.image_id=I.id)
                                LEFT JOIN imputation IMP ON(IMP.image_id=I.id)
                                LEFT JOIN imputation C_IMP ON(C_IMP.image_id=I.id)
                                LEFT JOIN soussouscategorie SSCAT ON(SC.soussouscategorie_id=SSCAT.id)
                                LEFT JOIN souscategorie SCAT ON(SSCAT.souscategorie_id=SCAT.id)
                                LEFT JOIN soussouscategorie SSCAT2 ON SC.soussouscategorie_id = SSCAT2.id
                                LEFT JOIN souscategorie SCAT2 ON SSCAT2.souscategorie_id = SCAT2.id
                                LEFT JOIN categorie CAT ON(SCAT.categorie_id=CAT.id)
                                LEFT JOIN controle_caisse C_CA ON(C_CA.image_id=I.id)
                                LEFT JOIN controle_note_frais C_NDF ON(C_NDF.image_id=I.id)
                                LEFT JOIN controle_vente_comptoir C_VC ON(C_VC.image_id=I.id)
                                LEFT JOIN banque_compte BC ON(SC.banque_compte_id=BC.id)
                                LEFT JOIN banque BQ ON(BC.banque_id=BQ.id)
                                LEFT JOIN separation SEP ON(SEP.image_id=I.id)
                                LEFT JOIN categorie CAT2 ON(SEP.categorie_id=CAT2.id)
                                LEFT JOIN image_image II ON (I.id = II.image_id)
                                WHERE D.id = :dossier_id AND I.exercice = :exercice  AND I.supprimer = :supprimer 
                                AND (CAT.id = :categorie_id OR CAT2.id = :categorie_id2) 
                                AND (SCAT.libelle = :lib_releve1 OR SCAT2.libelle = :lib_releve2)
                                AND I.decouper = :decouper AND L.date_scan >= :periode_debut AND L.date_scan <= :periode_fin ";


            }

            $prep = $pdo->prepare($query);
            $prep->execute($query_param);

            $saisies = $prep->fetchAll(\PDO::FETCH_OBJ);

            foreach ($saisies as $saisie) {
                $avancement = 'Saisie';
                $datefacture = '';
                $datescan = '';
                $image_categorie = '';
                $periode_d1 = '';
                $periode_f1 = '';
                if ($saisie->imputation > 1) {
                    $avancement = 'Imputée';
                }
                if ($saisie->date_facture) {
                    $datefacture = $saisie->date_facture;
                }
                if ($saisie->date_scan) {
                    $datescan = $saisie->date_scan;
                }
                if ($saisie->categorie2) {
                    $image_categorie = $saisie->categorie2;
                } elseif ($saisie->categorie) {
                    $image_categorie = $saisie->categorie;
                }
                if ($saisie->periode_d1) {
                    $periode_d1 = $saisie->periode_d1;
                }
                if ($saisie->periode_f1) {
                    $periode_f1 = $saisie->periode_f1;
                }
                $images[] = [
                    'id' => Boost::boost($saisie->image_id),
                    'tb-detail-image' => $saisie->image . '<a onclick="showImage(event)" style="padding-left: 5px;" href="#"><i class="fa fa-file-pdf-o" style="color: red;"></i></a>',
                    'tb-detail-categorie' => $image_categorie,
                    'tb-detail-datescan' => $datescan,
                    'tb-detail-datepiece' => $datefacture,
                    'tb-detail-periode-debut' => $periode_d1,
                    'tb-detail-periode-fin' => $periode_f1,
                    'tb-detail-rs' => $saisie->banque,
                    'tb-detail-avancement' => $avancement,
                ];
            }
        }

//        elseif ($categorie->getCode() == "CODE_NDF") {
//
//            $query = "SELECT SC.*,I.id AS image_id,I.nom AS image,I.saisie1,I.saisie2,I.ctrl_saisie,
//                                I.imputation,I.ctrl_imputation, I.exercice,L.date_scan,
//                                CAT.code AS categorie_code,CAT.libelle_new AS categorie,
//                                CAT2.code AS categorie_code2,CAT2.libelle_new AS categorie2,
//                                SCAT.libelle_new AS souscategorie,SSCAT.libelle_new AS soussouscategorie,
//                                SCAT2.libelle_new AS souscategorie2,SSCAT2.libelle_new AS soussouscategorie2,
//                                NDF.date AS date_ndf,NDF.description AS description_ndf
//                                FROM saisie_controle SC INNER JOIN image I ON (SC.image_id = I.id)
//                                INNER JOIN lot L ON(I.lot_id = L.id)
//                                INNER JOIN separation SEP ON I.id = SEP.image_id
//                                INNER JOIN categorie CAT ON SEP.categorie_id = CAT.id
//                                LEFT JOIN soussouscategorie SSCAT ON SEP.soussouscategorie_id = SSCAT.id
//                                LEFT JOIN souscategorie SCAT ON SEP.souscategorie_id = SCAT.id
//                                LEFT JOIN soussouscategorie SSCAT2 ON SC.soussouscategorie_id = SSCAT2.id
//                                LEFT JOIN souscategorie SCAT2 ON SSCAT2.souscategorie_id = SCAT2.id
//                                LEFT JOIN categorie CAT2 ON SCAT2.categorie_id = CAT2.id
//                                INNER JOIN controle_note_frais NDF ON I.id = NDF.image_id
//                                WHERE I.decouper = :decouper AND I.exercice = :exercice AND L.dossier_id = :dossier_id ";
//
//            if(!$date_scan_search){
//
//               $query .=" AND NDF.date >= :periode_debut AND NDF.date <= :periode_fin
//                                AND CAT.id = :categorie_id";
//            }
//            else{
//                $query .=" AND L.date_scan >= :periode_debut AND L.date_scan <= :periode_fin
//                                AND CAT.id = :categorie_id";
//            }
//
//            $prep = $pdo->prepare($query);
//            $prep->execute(array(
//                'decouper' => 0,
//                'exercice' => $exercice,
//                'dossier_id' => $dossier->getId(),
//                'categorie_id' => $categorie->getId(),
//                'periode_debut' => $periode_debut->format('Y-m-d'),
//                'periode_fin' => $periode_fin->format('Y-m-d')
//            ));
//            $saisies = $prep->fetchAll(\PDO::FETCH_OBJ);
//
//            foreach ($saisies as $saisie) {
//                $avancement = 'Saisie';
//                if ($saisie->imputation > 1) {
//                    $avancement = 'Imputée';
//                }
//                $datepiece = '';
//                $datescan = '';
//                $image_categorie = '';
//                if ($saisie->date_ndf) {
//                    $datepiece = $saisie->date_ndf;
//                }
//                if ($saisie->date_scan) {
//                    $datescan = $saisie->date_scan;
//                }
//                if ($saisie->categorie2) {
//                    $image_categorie = $saisie->categorie2;
//                } elseif ($saisie->categorie) {
//                    $image_categorie = $saisie->categorie;
//                }
//                $images[] = [
//                    'id' => Boost::boost($saisie->image_id),
//                    'tb-detail-image' => $saisie->image . '<a onclick="showImage(event)" style="padding-left: 5px;" href="#"><i class="fa fa-file-pdf-o" style="color: red;"></i></a>',
//                    'tb-detail-categorie' => $image_categorie,
//                    'tb-detail-datescan' => $datescan,
//                    'tb-detail-datepiece' => $datepiece,
//                    'tb-detail-periode-debut' => '',
//                    'tb-detail-periode-fin' => '',
//                    'tb-detail-rs' => $saisie->description_ndf,
//                    'tb-detail-avancement' => $avancement,
//                ];
//            }
//        }


//        elseif ($categorie->getCode() == "CODE_CAISSE") {
//            if(!$date_scan_search) {
//                $saisies = $this->getEntityManager()
//                    ->getRepository('AppBundle:SaisieControle')
//                    ->createQueryBuilder('saisieControle')
//                    ->select('saisieControle')
//                    ->innerJoin('saisieControle.image', 'image')
//                    ->addSelect('image')
//                    ->where('image.decouper = :decouper')
//                    ->andWhere('image.exercice = :exercice')
//                    ->innerJoin('image.lot', 'lot')
//                    ->addSelect('lot')
//                    ->andWhere('lot.dossier = :dossier')
//                    ->innerJoin('saisieControle.soussouscategorie', 'sscategorie')
//                    ->addSelect('sscategorie')
//                    ->innerJoin('sscategorie.souscategorie', 'scategorie')
//                    ->addSelect('scategorie')
//                    ->innerJoin('scategorie.categorie', 'categorie')
//                    ->addSelect('categorie')
//                    ->andWhere('categorie = :categorie')
//                    ->leftJoin('AppBundle\Entity\ControleCaisse', 'caisse', 'WITH', 'caisse.image = saisieControle.image')
//                    ->leftJoin('AppBundle\Entity\ControleVenteComptoir', 'vc', 'WITH', 'vc.image = saisieControle.image')
//                    ->andWhere('((caisse.date >= :periode_debut AND caisse.date <= :periode_fin) OR (vc.date >= :periode_debut AND vc.date <= :periode_fin))')
//                    ->setParameters(array(
//                        'decouper' => 0,
//                        'exercice' => $exercice,
//                        'dossier' => $dossier,
//                        'categorie' => $categorie,
//                        'periode_debut' => $periode_debut,
//                        'periode_fin' => $periode_fin
//                    ))
//                    ->orderBy('image.nom')
//                    ->getQuery()
//                    ->getResult();
//            }
//            else{
//                $saisies = $this->getEntityManager()
//                    ->getRepository('AppBundle:SaisieControle')
//                    ->createQueryBuilder('saisieControle')
//                    ->select('saisieControle')
//                    ->innerJoin('saisieControle.image', 'image')
//                    ->addSelect('image')
//                    ->where('image.decouper = :decouper')
//                    ->andWhere('image.exercice = :exercice')
//                    ->innerJoin('image.lot', 'lot')
//                    ->addSelect('lot')
//                    ->andWhere('lot.dossier = :dossier')
//                    ->innerJoin('saisieControle.soussouscategorie', 'sscategorie')
//                    ->addSelect('sscategorie')
//                    ->innerJoin('sscategorie.souscategorie', 'scategorie')
//                    ->addSelect('scategorie')
//                    ->innerJoin('scategorie.categorie', 'categorie')
//                    ->addSelect('categorie')
//                    ->andWhere('categorie = :categorie')
//                    ->andWhere('lot.dateScan >= :periode_debut')
//                    ->andWhere('lot.dateScan <= :periode_fin')
//                    ->leftJoin('AppBundle\Entity\ControleCaisse', 'caisse', 'WITH', 'caisse.image = saisieControle.image')
//                    ->leftJoin('AppBundle\Entity\ControleVenteComptoir', 'vc', 'WITH', 'vc.image = saisieControle.image')
////                    ->andWhere('((caisse.date >= :periode_debut AND caisse.date <= :periode_fin) OR (vc.date >= :periode_debut AND vc.date <= :periode_fin))')
//                    ->setParameters(array(
//                        'decouper' => 0,
//                        'exercice' => $exercice,
//                        'dossier' => $dossier,
//                        'categorie' => $categorie,
//                        'periode_debut' => $periode_debut,
//                        'periode_fin' => $periode_fin
//                    ))
//                    ->orderBy('image.nom')
//                    ->getQuery()
//                    ->getResult();
//            }
//
//            /** @var SaisieControle $saisie */
//            foreach ($saisies as $saisie) {
//                $avancement = 'Saisie';
//                if ($saisie->getImage()->getImputation() > 1) {
//                    $avancement = 'Imputée';
//                }
//                /** @var ControleCaisse $caisse */
//                foreach ($saisie->getImage()->getControleCaisses() as $caisse) {
//                    $datepiece = '';
//                    $datescan = '';
//                    $image_categorie = '';
//                    if ($caisse->getDate()) {
//                        $datepiece = $caisse->getDate()->format('Y-m-d');
//                    }
//                    if ($saisie->getImage()->getLot()->getDateScan()) {
//                        $datescan = $saisie->getImage()->getLot()->getDateScan()->format('Y-m-d');
//                    }
//                    if ($saisie->getSoussouscategorie()) {
//                        $image_categorie = $saisie->getSoussouscategorie()->getSouscategorie()->getCategorie()->getLibelle();
//                    }
//                    $images[] = [
//                        'id' => Boost::boost($saisie->getImage()->getId()),
//                        'tb-detail-image' => $saisie->getImage()->getNom() . '<a onclick="showImage(event)" style="padding-left: 5px;" href="#"><i class="fa fa-file-pdf-o" style="color: red;"></i></a>',
//                        'tb-detail-categorie' => $image_categorie,
//                        'tb-detail-datescan' => $datescan,
//                        'tb-detail-datepiece' => $datepiece,
//                        'tb-detail-periode-debut' => '',
//                        'tb-detail-periode-fin' => '',
//                        'tb-detail-rs' => $caisse->getLibelle(),
//                        'tb-detail-avancement' => $avancement,
//                    ];
//                }
//                /** @var ControleVenteComptoir $vc */
//                foreach ($saisie->getImage()->getControleVenteComptoirs() as $vc) {
//                    $datepiece = '';
//                    $datescan = '';
//                    $image_categorie = '';
//                    if ($vc->getDate()) {
//                        $datepiece = $vc->getDate()->format('Y-m-d');
//                    }
//                    if ($saisie->getImage()->getLot()->getDateScan()) {
//                        $datescan = $saisie->getImage()->getLot()->getDateScan()->format('Y-m-d');
//                    }
//                    if ($saisie->getSoussouscategorie()) {
//                        $image_categorie = $saisie->getSoussouscategorie()->getSouscategorie()->getCategorie()->getLibelle();
//                    }
//                    $images[] = [
//                        'id' => Boost::boost($saisie->getImage()->getId()),
//                        'tb-detail-image' => $saisie->getImage()->getNom() . '<a onclick="showImage(event)" style="padding-left: 5px;" href="#"><i class="fa fa-file-pdf-o" style="color: red;"></i></a>',
//                        'tb-detail-categorie' => $image_categorie,
//                        'tb-detail-datescan' => $datescan,
//                        'tb-detail-datepiece' => $datepiece,
//                        'tb-detail-periode-debut' => '',
//                        'tb-detail-periode-fin' => '',
//                        'tb-detail-rs' => $vc->getLibelle(),
//                        'tb-detail-avancement' => $avancement,
//                    ];
//                }
//            }
//        }
        return $images;
    }

    /**
     * Liste images et catégories
     * @param Client $client
     * @param $site
     * @param $dossier
     * @param $exercice
     * @param Utilisateur $user
     * @param bool $ajax
     * @param bool $date_scan_search
     * @param null $utilisateur
     * @return array
     * @throws \Exception
     */
    public function getListe(Client $client, $site, $dossiers, $exercice, Utilisateur $user, $ajax = TRUE, $date_scan_search = false)
    {


        $now = new \DateTime();
        $now->setTime(0,0);

        $with_retard = ['CODE_CLIENT', 'CODE_BANQUE', 'CODE_FRNS', 'CODE_CAISSE', 'CODE_NDF'];
        $exercice_n_1 = intval($exercice) - 1;


        // $dossiers = $this->getEntityManager()
        //     ->getRepository('AppBundle:Tbimage')
        //     ->getDossierListe($user, $client, $site, $dossier, $exercice);

        $categories = $this->getEntityManager()
            ->getRepository('AppBundle:Tbimage')
            ->getCategorieListe();


        $images = [];
        $data_banques = [];
        try {
            $images = $this->getEntityManager()
                ->getRepository('AppBundle:Tbimage')
                ->getImageListe($dossiers, $exercice, $data_banques, $date_scan_search);
        } catch (\Exception $e) {
        }

        $categ_zero = $this->getEntityManager()
            ->getRepository('AppBundle:TbimageZero')
            ->getForClient($client, $exercice, $banque_zero);
        $liste_status = $this->getEntityManager()
            ->getRepository('AppBundle:TbimageDossierStatus')
            ->getByClient($client, $exercice);
        $categ_caisse_id = 0;
        $categ_caisse = $this->getEntityManager()
            ->getRepository('AppBundle:Categorie')
            ->findBy(array(
                'code' => 'CODE_CAISSE'
            ));
        if (count($categ_caisse) > 0) {
            $categ_caisse_id = $categ_caisse[0]->getId();
        }

        $rows = [];
        $status_dossier = [];
        $status_dossiers = [];

        $index = 0;



        /** @var Dossier $dossier */
        foreach ($dossiers as $dossier) {
            /** @var int $fin_retard */
            $fin_retard = $this->getEntityManager()
                ->getRepository('AppBundle:Tbimage')
                ->getFinCheckRetard($dossier, $exercice, $debut, $fin);


            /** @var int $fin_exercice */
//            $fin_retard_color = $this->getEntityManager()
//                ->getRepository('AppBundle:Tbimage')
//                ->getFinCheckRetardColor($dossier, $exercice);

            //Réinitiliser status dossier
            $status_dossier[$dossier->getId()] = 0;

            $dossier_id = $dossier->getId();
            $to_be_show = true;
            $cumul_n = 0;
            $cumul_n_1 = 0;
            $allow_edit_status = (in_array('ROLE_SCRIPTURA_RESP', $user->getRoles()) ||
                in_array('ROLE_SCRIPTURA_ADMIN', $user->getRoles()));
            $header = $this->getEntityManager()->getRepository('AppBundle:Tbimage')->makeHeader($dossier, $exercice, $allow_edit_status, $to_be_show, $user);
            if ($to_be_show) {
                if ($ajax) {
                    $rows[] = [
                        'id' => 'header_' . $dossier->getId(),
                        'cell' => $header,
                    ];
                }

                $periode = 'M';
                $mois_plus = 1;
                $jour = 1;
                $demarrage = null;
                $premiere_cloture = null;
                $tbimagePeriode = $dossier->getTbimagePeriode();
                if ($tbimagePeriode) {
                    $periode = $tbimagePeriode->getPeriode();
                    $mois_plus = $tbimagePeriode->getMoisPlus();
                    $jour = $tbimagePeriode->getJour();
                }

                if ($dossier->getDebutActivite()) {
                    $demarrage = clone $dossier->getDebutActivite();
                }
                if ($dossier->getDateCloture()) {
                    $premiere_cloture = $dossier->getDateCloture();
                }

                //TOTAL PAR DOSSIER DES 24 MOIS
                $month_total = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

                $dossier_categories = [];
                if ($dossier->getTbimageCategorie()) {
                    $tbimageCategorie = $dossier->getTbimageCategorie();
                    $dossier_categories = $tbimageCategorie->getCategorieList();
                }
                /** Si pas de catégories selectionnées: afficher catégories par défaut */
                if (count($dossier_categories) == 0) {
                    $default_cats = $this->getEntityManager()
                        ->getRepository('AppBundle:Categorie')
                        ->getDefaultCategories();

                    /** @var Categorie $default_cat */
                    foreach ($default_cats as $default_cat) {
                        $dossier_categories[] = $default_cat->getId();
                    }

                }

                // Si avec centralisation caisse
                if ($dossier->getCentrCaisse()) {
                    $cat_client = $this->getEntityManager()
                        ->getRepository('AppBundle:Categorie')
                        ->findOneBy(array(
                            'code' => 'CODE_CLIENT'
                        ));
                    if ($cat_client) {
                        if (!in_array($cat_client->getId(), $dossier_categories)) {
                            $dossier_categories[] = $cat_client->getId();
                        }
                    }
                }
                foreach ($dossier_categories as $item) {
                    if (isset($categories[intval($item)])) {
                        /** @var Categorie $categorie */
                        $categorie = $categories[intval($item)];
                        $categorie_id = $categorie->getId();
                        $categorie_code = $categorie->getCode();

                        //Reinitialiser LIGNE
                        $data_image = [];
                        $cumul_cat_n = 0;
                        $cumul_cat_n_1 = 0;


                        $is_banque = false;

                        if ($categorie_code == "CODE_BANQUE") {
                            if(!$date_scan_search) {
                                $is_banque = true;
                                if (isset($data_banques[$dossier_id])) {
                                    foreach ($data_banques[$dossier_id] as $banque_id => $banque) {
                                        $cumul_cat_n = 0;
                                        $cumul_cat_n_1 = 0;
                                        $nom_categorie = $categorie->getLibelle() . ": " . $banque['nom'];
                                        $num_compte = $banque['compte'];
                                        $banque_mois = $banque['mois'];
                                        for ($i = 0; $i < count($banque_mois); $i++) {
                                            if ((!isset($banque_mois[$i]) || $banque_mois[$i] === 0) && $i <= $fin_retard) {
                                                if (isset($banque_zero[$dossier_id][$categorie_id][$banque_id][$i])) {
                                                    $data_banques[$dossier_id][$exercice][$banque_id][$i] = 0;
                                                } else {
                                                    if (in_array($categorie_code, $with_retard)) {
                                                        $data_banques[$dossier_id][$exercice][$banque_id][$i] = 'xxx';
                                                        $status_dossier[$dossier->getId()] = 3;
                                                    }
                                                }
                                            } elseif (!isset($banque_mois[$i]) || $banque_mois[$i] === 0) {
                                                if (isset($banque_zero[$dossier_id][$categorie_id][$banque_id][$i])) {
                                                    $data_banques[$dossier_id][$exercice][$banque_id][$i] = 0;
                                                } elseif ($i > $fin_retard) {
                                                    $data_banques[$dossier_id][$exercice][$banque_id][$i] = "";
                                                } else {
                                                    $data_banques[$dossier_id][$exercice][$banque_id][$i] = $banque_mois[$i];
                                                }
                                            } else {
                                                $data_banques[$dossier_id][$exercice][$banque_id][$i] = $banque_mois[$i];
                                                $cumul_cat_n += intval($data_banques[$dossier_id][$exercice][$banque_id][$i]);
                                                $month_total[$i] += intval($data_banques[$dossier_id][$exercice][$banque_id][$i]);
                                            }
                                        }

                                        $cumul_n += $cumul_cat_n;
                                        $cumul_n_1 += $cumul_cat_n_1;

                                        if ($cumul_cat_n == 0) {
                                            $cumul_cat_n = "";
                                        }
                                        if ($cumul_cat_n_1 == 0) {
                                            $cumul_cat_n_1 = "";
                                        }

                                        $props = [
                                            'dossier_id' => $dossier_id,
                                            'dossier_nom' => $dossier->getNom(),
                                            'periode' => $periode,
                                            'mois_plus' => $mois_plus,
                                            'jour' => $jour,
                                            'categorie_id' => $categorie_id,
                                            'categorie' => $categorie->getLibelle(),
                                            'categorie_code' => $categorie_code,
                                            'debut' => $images[$dossier_id]['debut']->format('Y-m-d'),
                                            'premiere_cloture' => $premiere_cloture,
                                            'exercice' => $exercice,
                                            'banque_id' => $banque_id,
                                            'num_compte' => $num_compte,
                                        ];
                                        $json = json_encode($props);

                                        //LIGNE
                                        if ($ajax) {
                                            /** Affichage Web */
                                            $rows[] = [
                                                'id' => 'categorie___' . $categorie_id . '___' . $categorie_code . '___' . $dossier_id . '___' . $images[$dossier_id]['debut']->format('Y-m-d').'___'.$num_compte,
                                                'cell' => array_merge(['<span data-props="' . htmlentities($json, ENT_QUOTES, 'UTF-8') . '"></span>', '<span data-dossier="' . $dossier->getNom() . '">' . $nom_categorie . '</span>', "", "", "", "", "", $cumul_cat_n_1, $cumul_cat_n, ""], $data_banques[$dossier_id][$exercice][$banque_id]),
                                            ];
                                        } else {
                                            /** Envoi mail */
                                            $nom_categorie = $categorie->getLibelle() . " " . $banque['nom'] . " #" . $num_compte;
                                            $rows[] = [
                                                'id' => $categorie_id . '___' . $categorie_code . '___' . $dossier_id . '___' . $images[$dossier_id]['debut']->format('Y-m-d'),
                                                'cell' => array_merge([$nom_categorie], $data_banques[$dossier_id][$exercice][$banque_id]),
                                            ];
                                        }
                                        $cumul_cat_n = 0;
                                        $cumul_cat_n_1 = 0;
                                    }
                                }
                            }
                        }

                        if(!$is_banque) {
                            //IMAGE N
                            for ($i = 0; $i < 23; $i++) {
                                if (isset($images[$dossier_id][$exercice][$categorie_id][$i])) {
                                    $data_image[] = $images[$dossier_id][$exercice][$categorie_id][$i];
                                    $cumul_cat_n += intval($images[$dossier_id][$exercice][$categorie_id][$i]);
                                    $month_total[$i] += intval($images[$dossier_id][$exercice][$categorie_id][$i]);
                                } else {
                                    $data_image[] = "";
                                }
                            }

                            //IMAGE N-1
                            if (isset($images[$dossier_id][$exercice_n_1][$categorie_id])) {
                                $cumul_cat_n_1 = $images[$dossier_id][$exercice_n_1][$categorie_id];
                            } else {
                                $cumul_cat_n_1 = "";
                            }

                            if ($cumul_cat_n == 0) {
                                $cumul_cat_n = "";
                            }
                            if ($cumul_cat_n_1 == 0) {
                                $cumul_cat_n_1 = "";
                            }

                            for ($i = 0; $i < count($data_image); $i++) {
                                if ((!isset($data_image[$i]) || $data_image[$i] == 0) && $i <= $fin_retard) {
                                    if ($dossier->getCentrCaisse()) {
                                        if (isset($categ_zero[$dossier_id][$categorie_id][$i]) || (isset($categ_zero[$dossier_id][$categ_caisse_id][$i]) && $categorie_code == 'CODE_CLIENT')) {
                                            $data_image[$i] = 0;
                                        } else {
                                            if (in_array($categorie_code, $with_retard)) {
                                                $data_image[$i] = 'xxx';
                                                $status_dossier[$dossier->getId()] = 3;
                                            }
                                        }
                                    } else {
                                        if (isset($categ_zero[$dossier_id][$categorie_id][$i])) {
                                            $data_image[$i] = 0;
                                        } else {
                                            if (in_array($categorie_code, $with_retard)) {
                                                $data_image[$i] = 'xxx';
                                                $status_dossier[$dossier->getId()] = 3;
                                            }
                                        }
                                    }
                                }
                            }

                            $nom_categorie = $categorie->getLibelle();
                            if ($categorie->getCode() == 'CODE_CLIENT' && $dossier->getCentrCaisse()) {
                                $nom_categorie = "CAISSE (brouillard)";
                            }
                            $props = [
                                'dossier_id' => $dossier_id,
                                'dossier_nom' => $dossier->getNom(),
                                'periode' => $periode,
                                'mois_plus' => $mois_plus,
                                'jour' => $jour,
                                'categorie_id' => $categorie_id,
                                'categorie' => $categorie->getLibelle(),
                                'categorie_code' => $categorie_code,
                                'debut' => $images[$dossier_id]['debut']->format('Y-m-d'),
                                'premiere_cloture' => $premiere_cloture,
                                'exercice' => $exercice,
                            ];
                            $json = json_encode($props);

                            //LIGNE
                            if ($ajax) {
                                /** Affichage WEB */
                                $rows[] = [
                                    'id' => 'categorie___' . $categorie_id . '___' . $categorie_code . '___' . $dossier_id . '___' . $images[$dossier_id]['debut']->format('Y-m-d'),
                                    'cell' => array_merge(['<span data-props="' . htmlentities($json, ENT_QUOTES, 'UTF-8') . '"></span>', '<span data-dossier="' . $dossier->getNom() . '">' . $nom_categorie . '</span>', "", "", "", "", "", $cumul_cat_n_1, $cumul_cat_n, ""], $data_image),
                                ];
                            } else {
                                /** Envoi email */
                                $rows[] = [
                                    'id' => $categorie_id . '___' . $categorie_code . '___' . $dossier_id . '___' . $images[$dossier_id]['debut']->format('Y-m-d'),
                                    'cell' => array_merge([$nom_categorie], $data_image),
                                ];
                            }
                        }

                        $cumul_n += intval($cumul_cat_n);
                        $cumul_n_1 += intval($cumul_cat_n_1);
                    }
                }

                if (isset($images[$dossier_id][$exercice]['encours'][0])) {
                    $encours = $images[$dossier_id][$exercice]['encours'][0];
                    $cumul_n += intval($encours);
                } else {
                    $encours = "";
                }
                if (isset($images[$dossier_id][$exercice_n_1]['encours'][0])) {
                    $encours_n_1 = $images[$dossier_id][$exercice_n_1]['encours'][0];
                    $cumul_n_1 += intval($encours_n_1);
                } else {
                    $encours_n_1 = "";
                }
                if ($ajax) {
                    $rows[] = [
                        'id' => 'encours___' . $dossier->getId() . '___' . $dossier->getNom(),
                        'cell' => [
                            '',
                            'EN COURS',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '<a href="#" onclick="showEncours(event)" class="encours" data-exercice="' . $exercice_n_1 . '">' . $encours_n_1 . '</a>',
                            '<a href="#" onclick="showEncours(event)" class="encours" data-exercice="' . $exercice . '">' . $encours . '</a>'
                        ]
                    ];
                }

                if ($cumul_n == 0) {
                    $cumul_n = "";
                }
                if ($cumul_n_1 == 0) {
                    $cumul_n_1 = "";
                }
                if ($ajax) {
                    $rows[] = [
                        'id' => 'total_' . $dossier->getId(),
                        'cell' => array_merge(['', 'TOTAL', '', '', '', '', '', $cumul_n_1, $cumul_n, ''], $month_total)
                    ];


                    $rows[] = [
                        'id' => 'separator_' . $dossier->getId(),
                        'cell' => ['']
                    ];
                }
            }

            //Tester si dossier finissable
            if ($status_dossier[$dossier->getId()] == 0) {
                if ($fin <= $now) {
                    $status_dossier[$dossier->getId()] = 2;
                } else {
                    $status_dossier[$dossier->getId()] = 3;
                }
            }

            //Tester si non traitable
            if ($dossier->getNonTraitable()) {
                $status_dossiers[] = [$dossier->getId() => 9];
            } else {
                if (isset($liste_status[$dossier->getId()])) {
                    $status_dossiers[] = [$dossier->getId() => $liste_status[$dossier->getId()]];
                } else {
                    $status_dossiers[] = [$dossier->getId() => $status_dossier[$dossier->getId()]];
                }
            }

            $index += 1;

        }



        if ($ajax) {
            $liste = [
                'rows' => $rows,
                'with_retard' => $with_retard,
                'status_dossiers' => $status_dossiers,
                'liste_status' => $liste_status,
            ];
        } else {
            $liste = [
                'rows' => $rows,
                'with_retard' => $with_retard,
                'status_dossiers' => $status_dossiers,
                'liste_status' => $liste_status,
                'dossiers' => $dossiers,
            ];
        }

        return $liste;
    }

    /**
     * Listes des dossiers pour Tbimage
     *
     * @param Utilisateur $user
     * @param Client $the_client
     * @param $the_site
     * @param $the_dossier
     * @param $the_exercice
     * @return array
     */
    public function getDossierListe(Utilisateur $user, Client $the_client, $the_site, $the_dossier, $the_exercice)
    {
        $dossiers = [];
        if (!$the_dossier) {
            $dossierTemps = $this->getEntityManager()
                ->getRepository('AppBundle:Dossier')
                ->getUserDossier($user, $the_client, $the_site, $the_exercice);


            foreach ($dossierTemps as $dossierTmp) {

                $firstSend = $this->getEntityManager()
                    ->getRepository('AppBundle:Image')
                    ->getFirstSend($dossierTmp);

                if (count($firstSend) > 0) {
                    if ($firstSend['exercice'] <= intval($the_exercice)) {
                        $dossiers[] = $dossierTmp;
                    }
                }
            }


        } else {
            $dossiers[] = $the_dossier;
        }

        return $dossiers;
    }

    /**
     * Liste categories avec id comme clé
     *
     * @return array
     */
    public function getCategorieListe()
    {
        $categories = $this->getEntityManager()
            ->getRepository('AppBundle:Categorie')
            ->getForTableauImage();
        $list = [];
        /** @var Categorie $categorie */
        foreach ($categories as $categorie) {
            $list[$categorie->getId()] = $categorie;
        }

        return $list;
    }

    /**
     * Listes images pour Tb images
     *
     * @param Dossier[] $dossiers
     * @param $exercice
     * @param $data_banques
     * @param bool $date_scan_search
     * @return array
     * @throws \Exception
     * @internal param $banques
     */
    public function getImageListe($dossiers, $exercice, &$data_banques, $date_scan_search = false)
    {



        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $now = new \DateTime();

        $ids = [];
        $data = [];
        $banques = [];
        $nom_banques = [];
        $data_banques = [];
        $liste_banques = [];
        $liste_dossiers = [];



        foreach ($dossiers as $dossier) {
            $ids[] = $dossier->getId();
            $liste_dossiers[$dossier->getId()] = $dossier;
            $tbimagePeriode = $dossier->getTbimagePeriode();
            $demarrage = null;
            $premiere_cloture = null;
            if ($dossier->getDebutActivite()) {
                $demarrage = clone $dossier->getDebutActivite();
            }

            if ($dossier->getDateCloture()) {
                $premiere_cloture = clone $dossier->getDateCloture();
            }

            if ($dossier->getCloture() == 12 || $dossier->getCloture() == null) {
                $debut = \DateTime::createFromFormat('Y-m-d', $exercice . '-01-01');
            } else {
                $annee = intval($exercice) - 1;
                $mois = intval($dossier->getCloture() + 1);
                $debut = \DateTime::createFromFormat('Y-m-d', $annee . '-' . $mois . '-01');
            }

            if ($demarrage && $premiere_cloture && $premiere_cloture->format('Y') == intval($exercice)) {
                if ($this->isNbMoisExerciceOk(clone $demarrage, clone $premiere_cloture)) {
                    $debut = \DateTime::createFromFormat('Y-m-d', $demarrage->format('Y-m-01'));
                }
            }

            $data[$dossier->getId()]['debut'] = new \DateTime($debut->format('Y-m-01'));
        }



        $dossier_ids = implode(',', $ids);

        //LISTE DES BANQUES
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:BanqueCompte')
            ->createQueryBuilder('banque_compte');
        $banque_comptes = $qb
            ->select('banque_compte')
            ->innerJoin('banque_compte.dossier', 'dossier')
            ->where($qb->expr()->in('dossier.id', $dossier_ids))
            ->addSelect('dossier')
            ->innerJoin('banque_compte.banque', 'banque')
            ->addSelect('banque')
            ->getQuery()
            ->getResult();
        //LISTE RELEVES MANQUANTS
        $manquants = $this->getEntityManager()
            ->getRepository('AppBundle:ReleveManquant')
            ->getListeByDossiers($dossier_ids, $exercice);



        /** @var BanqueCompte $banque_compte */
        foreach ($banque_comptes as $banque_compte) {
            $code = '';
            if($banque_compte->getSourceImage() !== null){
                if($banque_compte->getSourceImage()->getSource() === 'SOBANK'){
                    $code = 'SB ';
                }
            }

            if (isset($manquants[$banque_compte->getDossier()->getId()][$banque_compte->getId()])) {
                if ($manquants[$banque_compte->getDossier()->getId()][$banque_compte->getId()]['status'] === true) {
                    $liste_banques[$banque_compte->getDossier()->getId()][$banque_compte->getId()]['nom'] = $code.ucwords(mb_strtolower($banque_compte->getBanque()->getNom(), "UTF-8"));
                    $liste_banques[$banque_compte->getDossier()->getId()][$banque_compte->getId()]['compte'] = $banque_compte->getNumcompte();
                    $manquant = $manquants[$banque_compte->getDossier()->getId()][$banque_compte->getId()]['manquant'];
                    $liste_banques[$banque_compte->getDossier()->getId()][$banque_compte->getId()]['manquant'] = $manquant;
                }
            } else {
                $liste_banques[$banque_compte->getDossier()->getId()][$banque_compte->getId()]['nom'] = $code.ucwords(mb_strtolower($banque_compte->getBanque()->getNom(), "UTF-8"));
                $liste_banques[$banque_compte->getDossier()->getId()][$banque_compte->getId()]['compte'] = $banque_compte->getNumcompte();
                $liste_banques[$banque_compte->getDossier()->getId()][$banque_compte->getId()]['manquant'] = [];
            }
        }



//        $query = "SELECT C.id AS client_id, S.id AS site_id, D.nom AS dossier, D.id AS dossier_id,
//                  CAT2.libelle AS categorie2, CAT2.id AS categorie_id2, CAT2.code AS categorie_code2,
//                  CAT.libelle AS categorie, CAT.id AS categorie_id, CAT.code AS categorie_code, SCAT.libelle AS sous_categorie, SCAT.id AS sous_categorie_id,
//                  SSCAT.libelle AS sous_sous_categorie, SSCAT.id AS sous_sous_categorie_id,
//                  C_S.id AS controle_saisie_id, C_S.date_livraison, C_S.date_facture, C_S.periode_d1, C_S.periode_f1, C_S.type_caisse,
//                  IMP.date_livraison AS imp_date_livraison, IMP.date_facture AS imp_date_facture, IMP.periode_d1 AS imp_periode_d1, IMP.periode_f1 AS imp_periode_f1,
//                  C_IMP.date_livraison AS c_imp_date_livraison, C_IMP.date_facture AS c_imp_date_facture, C_IMP.periode_d1 AS c_imp_periode_d1, C_IMP.periode_f1 AS c_imp_periode_f1,
//                  C_CA.date AS date_caisse, C_NDF.date AS date_ndf, C_VC.date AS date_vc,
//                  BC.id AS banque_compte_id, BC.iban AS banque_iban, BC.numcb AS banque_num_cb, BC.numcompte AS banque_num_compte,
//                  BQ.id AS banque_id, BQ.nom AS banque_nom, BQ.codebanque AS banque_codebanque,
//                  I.id, I.exercice, I.status, I.decouper, I.saisie1, I.saisie2, I.ctrl_saisie, I.imputation, I.ctrl_imputation,
//                  II.image_id AS image_mere, L.date_scan
//                  FROM image I INNER JOIN lot L ON(I.lot_id=L.id) INNER JOIN dossier D ON(L.dossier_id=D.id)
//                  INNER JOIN site S ON(D.site_id=S.id) INNER JOIN client C ON(S.client_id=C.id)
//                  LEFT JOIN saisie_controle C_S ON (C_S.image_id=I.id)
//                  LEFT JOIN imputation IMP ON(IMP.image_id=I.id)
//                  LEFT JOIN imputation C_IMP ON(C_IMP.image_id=I.id)
//                  LEFT JOIN soussouscategorie SSCAT ON(C_S.soussouscategorie_id=SSCAT.id)
//                  LEFT JOIN souscategorie SCAT ON(SSCAT.souscategorie_id=SCAT.id)
//                  LEFT JOIN categorie CAT ON(SCAT.categorie_id=CAT.id)
//                  LEFT JOIN controle_caisse C_CA ON(C_CA.image_id=I.id)
//                  LEFT JOIN controle_note_frais C_NDF ON(C_NDF.image_id=I.id)
//                  LEFT JOIN controle_vente_comptoir C_VC ON(C_VC.image_id=I.id)
//                  LEFT JOIN banque_compte BC ON(C_S.banque_compte_id=BC.id)
//                  LEFT JOIN banque BQ ON(BC.banque_id=BQ.id)
//                  LEFT JOIN separation SEP ON(SEP.image_id=I.id)
//                  LEFT JOIN categorie CAT2 ON(SEP.categorie_id=CAT2.id)
//                  LEFT JOIN image_image II ON (I.id = II.image_id)
//                  WHERE D.id IN($dossier_ids) AND I.exercice >= :exercice_n_1 AND I.exercice <= :exercice
//                  AND I.decouper = :decouper AND (SCAT.libelle IS NULL OR SCAT.libelle NOT LIKE '%doublon%') ";





        $query = "SELECT C.id AS client_id, S.id AS site_id, D.nom AS dossier, D.id AS dossier_id,
                  CAT2.libelle AS categorie2, CAT2.id AS categorie_id2, CAT2.code AS categorie_code2,
                  CAT.libelle AS categorie, CAT.id AS categorie_id, CAT.code AS categorie_code, SCAT.libelle AS sous_categorie, SCAT.id AS sous_categorie_id,
                  SSCAT.libelle AS sous_sous_categorie, SSCAT.id AS sous_sous_categorie_id,
                  C_S.id AS controle_saisie_id, C_S.date_livraison, C_S.date_facture, C_S.periode_d1, C_S.periode_f1, C_S.type_caisse,
                  IMP.date_livraison AS imp_date_livraison, IMP.date_facture AS imp_date_facture, IMP.periode_d1 AS imp_periode_d1, IMP.periode_f1 AS imp_periode_f1,
                  C_IMP.date_livraison AS c_imp_date_livraison, C_IMP.date_facture AS c_imp_date_facture, C_IMP.periode_d1 AS c_imp_periode_d1, C_IMP.periode_f1 AS c_imp_periode_f1,
                  C_CA.date AS date_caisse, C_NDF.date AS date_ndf, C_VC.date AS date_vc,
                  BC.id AS banque_compte_id, BC.iban AS banque_iban, BC.numcb AS banque_num_cb, BC.numcompte AS banque_num_compte,
                  BQ.id AS banque_id, BQ.nom AS banque_nom, BQ.codebanque AS banque_codebanque,
                  I.id, I.exercice, I.status, I.decouper, I.saisie1, I.saisie2, I.ctrl_saisie, I.imputation, I.ctrl_imputation,
                  II.image_id AS image_mere, L.date_scan 
                  FROM image I INNER JOIN lot L ON(I.lot_id=L.id) INNER JOIN dossier D ON(L.dossier_id=D.id) 
                  INNER JOIN site S ON(D.site_id=S.id) INNER JOIN client C ON(S.client_id=C.id)
                  LEFT JOIN saisie_controle C_S ON (C_S.image_id=I.id)
                  LEFT JOIN imputation IMP ON(IMP.image_id=I.id)
                  LEFT JOIN imputation C_IMP ON(C_IMP.image_id=I.id)
                 
                  LEFT JOIN controle_caisse C_CA ON(C_CA.image_id=I.id)
                  LEFT JOIN controle_note_frais C_NDF ON(C_NDF.image_id=I.id)
                  LEFT JOIN controle_vente_comptoir C_VC ON(C_VC.image_id=I.id)
                  LEFT JOIN banque_compte BC ON(C_S.banque_compte_id=BC.id)
                  LEFT JOIN banque BQ ON(BC.banque_id=BQ.id)
                  LEFT JOIN separation SEP ON(SEP.image_id=I.id)
                   LEFT JOIN soussouscategorie SSCAT ON(SEP.soussouscategorie_id=SSCAT.id)
                  LEFT JOIN souscategorie SCAT ON(SEP.souscategorie_id=SCAT.id)
                  LEFT JOIN categorie CAT ON(SEP.categorie_id=CAT.id)
                  LEFT JOIN categorie CAT2 ON(SEP.categorie_id=CAT2.id)
                  LEFT JOIN image_image II ON (I.id = II.image_id)
                  WHERE D.id IN($dossier_ids) AND I.exercice >= :exercice_n_1 AND I.exercice <= :exercice 
                  AND I.supprimer = :supprimer
                  AND I.decouper = :decouper AND (SCAT.libelle IS NULL OR SCAT.libelle NOT LIKE '%doublon%') ";

        if (!$date_scan_search) {
            $query .= "AND C.code != :code_categ";
        }
        else{
            $query .= " ORDER BY L.date_scan";
        }

        $prep = $pdo->prepare($query);


        $query_param = ['exercice_n_1' => intval($exercice) - 1,
            'exercice' => $exercice,
            'decouper' => 0,
            'supprimer' => 0
        ];

        if (!$date_scan_search) {
            $query_param['code_categ'] = 'CODE_BANQUE';
        }



        $prep->execute($query_param);



        while ($image = $prep->fetch(\PDO::FETCH_OBJ)) {


            $dossier_id = $image->dossier_id;
            $categorie_id = "";
            $categorie_code = "";

            $img_exercice = $image->exercice;
            $banque_id = 0;

            if ($image->banque_id) {
                $banque_id = $image->banque_id;
            }
            /** @var \DateTime $debut */
            $start = $data[$dossier_id]['debut'];
            $date_piece = [];
            if (($image->ctrl_saisie == null || $image->ctrl_saisie < 2) && $image->image_mere == null
                && $image->categorie_id == null && $image->categorie_id2 == null) {
                $categorie_id = "encours";
                $categorie_code = "encours";
            } else {
                if ($image->categorie_id) {
                    $categorie_id = $image->categorie_id;
                    $categorie_code = $image->categorie_code;
                } else {
                    $categorie_id = $image->categorie_id2;
                    $categorie_code = $image->categorie_code2;
                }
            }

            //EN COURS
            if ($categorie_code == "encours") {
                if (isset($data[$dossier_id][$img_exercice]['encours'][0])) {
                    $data[$dossier_id][$img_exercice]['encours'][0] += 1;
                } else {
                    $data[$dossier_id][$img_exercice]['encours'][0] = 1;
                }
            } else {
                $date_piece = [];

                if (!$date_scan_search) {
                    //AVEC CATEGORIES
                    if ($categorie_code == "CODE_CLIENT" ||
                        $categorie_code == "CODE_FRNS" ||
                        $categorie_code == "CODE_NDF" ||
                        $categorie_code == "CODE_CAISSE" ||
                        $categorie_code == "CODE_FISC" ||
                        $categorie_code == "CODE_SOC"
                    ) {
                        if ($image->c_imp_date_facture) {
                            $date_piece[] = new \DateTime($image->c_imp_date_facture);
                        } elseif ($image->imp_date_facture) {
                            $date_piece[] = new \DateTime($image->imp_date_facture);
                        } elseif ($image->date_facture) {
                            $date_piece[] = new \DateTime($image->date_facture);
                        }
                        elseif ($image->c_imp_periode_d1){
                            $date_piece[] = new \DateTime($image->c_imp_periode_d1);
                        }
                        elseif ($image->imp_periode_d1){
                            $date_piece[] = new \DateTime($image->imp_periode_d1);
                        }
                        elseif ($image->periode_d1){
                            $date_piece[] = new \DateTime($image->periode_d1);
                        }
                    }
//                    elseif ($categorie_code == "CODE_NDF") {
//                        if ($image->date_ndf) {
//                            $date_piece[] = new \DateTime($image->date_ndf);
//                        }
//                    }
//                    elseif ($categorie_code == "CODE_CAISSE") {
//                        //0:vente comptoire;1: caisse
//                        if ($image->type_caisse == 0) {
//                            if ($image->date_vc) {
//                                $date_piece[] = new \DateTime($image->date_vc);
//                            }
//                        } elseif ($image->type_caisse == 1) {
//                            if ($image->date_caisse) {
//                                $date_piece[] = new \DateTime($image->date_caisse);
//                            }
//                        }
//                    }
                } else {
                    $date_piece[] = new \DateTime($image->date_scan);
                }
            }


            if ($date_piece && is_array($date_piece)) {
                /** @var \DateTime $item */
                foreach ($date_piece as $item) {
                    if ($item) {
                        try {

                            if (!$date_scan_search) {
                                // IMAGE N
                                if ($img_exercice == $exercice) {
                                    $diff = $this->diffInMonth(clone $start, clone $item);
                                    if ($categorie_code == 'CODE_BANQUE' && $image->sous_categorie == 'releves bancaires') {
                                        if (!isset($nom_banques[$banque_id])) {
                                            $nom_banques[$banque_id] = $image->banque_nom;
                                        }
                                        if (isset($banques[$dossier_id][$banque_id][$img_exercice][$diff])) {
                                            $banques[$dossier_id][$banque_id][$img_exercice][$diff] += 1;
                                        } else {
                                            $banques[$dossier_id][$banque_id][$img_exercice][$diff] = 1;
                                        }
                                    } else {
                                        if (isset($data[$dossier_id][$img_exercice][$categorie_id][$diff])) {
                                            $data[$dossier_id][$img_exercice][$categorie_id][$diff] += 1;
                                        } else {
                                            $data[$dossier_id][$img_exercice][$categorie_id][$diff] = 1;
                                        }
                                    }

                                } else {
                                    // IMAGE N-1
                                    if ($categorie_code == 'CODE_BANQUE' && $image->sous_categorie == 'releves bancaires') {
                                        if (!isset($nom_banques[$banque_id])) {
                                            $nom_banques[$banque_id] = $image->banque_nom;
                                        }
                                        if (isset($banques[$dossier_id][$img_exercice][$banque_id])) {
                                            $banques[$dossier_id][$banque_id][$img_exercice] += 1;
                                        } else {
                                            $banques[$dossier_id][$banque_id][$img_exercice] = 1;
                                        }
                                    } else {
                                        if (isset($data[$dossier_id][$img_exercice][$categorie_id])) {
                                            $data[$dossier_id][$img_exercice][$categorie_id] += 1;
                                        } else {
                                            $data[$dossier_id][$img_exercice][$categorie_id] = 1;
                                        }
                                    }
                                }
                            } else {
                                // IMAGE N
                                if ($img_exercice == $exercice) {

                                    $diff = $this->diffInMonth(clone $start, clone $item);

                                    if ($categorie_code == 'CODE_BANQUE' && $image->sous_categorie == 'releves bancaires') {


                                       if($start > $item){
                                           continue;
                                       }



                                        if (!isset($nom_banques[$banque_id])) {
                                            $nom_banques[$banque_id] = $image->banque_nom;
                                        }
                                        if (isset($banques[$dossier_id][$banque_id][$img_exercice][$diff])) {
                                            $banques[$dossier_id][$banque_id][$img_exercice][$diff] += 1;
                                        } else {
                                            $banques[$dossier_id][$banque_id][$img_exercice][$diff] = 1;
                                        }

                                        if (isset($data[$dossier_id][$img_exercice][$categorie_id][$diff])) {
                                            $data[$dossier_id][$img_exercice][$categorie_id][$diff] += 1;
                                        } else {
                                            $data[$dossier_id][$img_exercice][$categorie_id][$diff] = 1;
                                        }
                                    } else if ($categorie_code != 'CODE_BANQUE' && $image->sous_categorie != 'releves bancaires') {
                                        if (isset($data[$dossier_id][$img_exercice][$categorie_id][$diff])) {
                                            $data[$dossier_id][$img_exercice][$categorie_id][$diff] += 1;
                                        } else {
                                            $data[$dossier_id][$img_exercice][$categorie_id][$diff] = 1;
                                        }
                                    }

                                } else {
                                    // IMAGE N-1
                                    if ($categorie_code == 'CODE_BANQUE' && $image->sous_categorie == 'releves bancaires') {

                                        if (!isset($nom_banques[$banque_id])) {
                                            $nom_banques[$banque_id] = $image->banque_nom;
                                        }
                                        if (isset($banques[$dossier_id][$img_exercice][$banque_id])) {
                                            $banques[$dossier_id][$banque_id][$img_exercice] += 1;
                                        } else {
                                            $banques[$dossier_id][$banque_id][$img_exercice] = 1;
                                        }

                                        if (isset($data[$dossier_id][$img_exercice][$categorie_id])) {
                                            $data[$dossier_id][$img_exercice][$categorie_id] += 1;
                                        } else {
                                            $data[$dossier_id][$img_exercice][$categorie_id] = 1;
                                        }
                                    } else if ($categorie_code != 'CODE_BANQUE' && $image->sous_categorie != 'releves bancaires') {
                                        if (isset($data[$dossier_id][$img_exercice][$categorie_id])) {
                                            $data[$dossier_id][$img_exercice][$categorie_id] += 1;
                                        } else {
                                            $data[$dossier_id][$img_exercice][$categorie_id] = 1;
                                        }
                                    }
                                }
                            }


                        } catch (\Exception $ex) {
                        }
                    }
                }
            }
        }



        foreach ($liste_banques as $dossier_id => &$manquants) {
            $index_now = $this->diffInMonth(clone $this->getDebutDossier($liste_dossiers[$dossier_id], $exercice), clone $now);
            $cloture = $this->getEntityManager()
                ->getRepository('AppBundle:Dossier')
                ->getDateCloture($liste_dossiers[$dossier_id], $exercice);
            $index_cloture = $this->diffInMonth(clone $this->getDebutDossier($liste_dossiers[$dossier_id], $exercice), clone $cloture);

            foreach ($manquants as &$items) {
                for ($i = 0; $i < 23; $i++) {
                    if ($i <= $index_now && $i <= $index_cloture) {
                        $items['mois'][$i] = 1;
                    } else {
                        $items['mois'][$i] = '';
                    }
                }
                foreach ($items['manquant'] as &$manquant) {
                    $num_mois = $this->diffInMonth(clone $this->getDebutDossier($liste_dossiers[$dossier_id], $exercice), new \DateTime($manquant . '-01'));
                    if ($num_mois >= 0) {
                        $items['mois'][$num_mois] = 0;
                    }
                }
                unset($items['manquant']);
            }
        }


        $data_banques = $liste_banques;

        return $data;

    }

    /**
     * Différence en mois de deux dates
     *
     * @param \DateTime $debut
     * @param \DateTime $fin
     * @return int
     * @throws \Exception
     */
    public function diffInMonth(\DateTime $debut, \DateTime $fin)
    {
        $debut->setTime(0, 0);
        $fin->setTime(0, 0);

        $limit_debut = new \DateTime('2000-01-01');
        $limit_fin = new \DateTime('2099-12-31');

        if ($debut >= $limit_debut && $debut <= $limit_fin && $fin >= $limit_debut && $debut <= $limit_fin) {
            $reverse = false;
            if ($debut > $fin) {
                $tmp = clone $debut;
                $debut = clone $fin;
                $fin = clone $tmp;
                $reverse = true;
            }

            if (intval($debut->format('Y')) == intval($fin->format('Y'))) {
                if ($reverse) {
                    return -intval($fin->format('m')) - intval($debut->format('m'));
                } else {
                    return intval($fin->format('m')) - intval($debut->format('m'));
                }
            } else {
                $diff = -1;
                $i = 0;
                while ($debut <= $fin) {
                    $diff++;
                    $i++;
                    if ($i > 60) {
                        return -10;
                    }
                    $debut->add(new \DateInterval('P1M'));
                }
                if ($reverse) {
                    return -$diff;
                } else {
                    return $diff;
                }
            }
        } else {
            return -10;
        }
    }

    /**
     * Différence en mois de deux dates
     *
     * @param \DateTime $debut
     * @param \DateTime $fin
     * @return int
     * @throws \Exception
     */
    public function isNbMoisExerciceOk(\DateTime $debut, \DateTime $fin)
    {
        $debut->setTime(0, 0);
        $fin->setTime(0, 0);

        $limit_debut = new \DateTime('1900-01-01');
        $limit_fin = new \DateTime('2099-12-31');

        if ($debut >= $limit_debut && $debut <= $limit_fin && $fin >= $limit_debut && $debut <= $limit_fin) {
            if ($debut > $fin) {
                return false;
            }

            if (intval($debut->format('Y')) == intval($fin->format('Y'))) {
                $diff = intval($fin->format('m')) - intval($debut->format('m'));
                return $diff >= 0 && $diff <= 23;
            } else {
                $diff = -1;
                $i = 0;
                while ($debut <= $fin) {
                    $diff++;
                    $i++;
                    if ($i > 23) {
                        return false;
                    }
                    $debut->add(new \DateInterval('P1M'));
                }
                return $diff >= 0 && $diff <= 23;
            }
        } else {
            return false;
        }
    }

    /**
     * Debut de l'exercice d'un dossier
     *
     * @param Dossier $dossier
     * @param $exercice
     * @return bool|\DateTime
     * @throws \Exception
     */
    public function getDebutDossier(Dossier $dossier, $exercice)
    {
        $tbimagePeriode = $dossier->getTbimagePeriode();
        $demarrage = null;
        $premiere_cloture = null;

        if ($dossier->getDebutActivite()) {
            $demarrage = clone $dossier->getDebutActivite();
        }
        if ($dossier->getDateCloture()) {
            $premiere_cloture = clone $dossier->getDateCloture();
        }

        if ($dossier->getCloture() == 12 || $dossier->getCloture() == null) {
            $debut = \DateTime::createFromFormat('Y-m-d', $exercice . '-01-01');
        } else {
            $annee = intval($exercice) - 1;
            $mois = intval($dossier->getCloture() + 1);
            $debut = \DateTime::createFromFormat('Y-m-d', $annee . '-' . $mois . '-01');
        }

        if ($demarrage && $premiere_cloture && $premiere_cloture->format('Y') == intval($exercice)) {
            if ($this->isNbMoisExerciceOk(clone $demarrage, clone $premiere_cloture)) {
                $debut = \DateTime::createFromFormat('Y-m-d', $demarrage->format('Y-m-01'));
            }
        }

        return $debut->setTime(0, 0);
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @return false|int|string
     * @throws \Exception
     */
    public function getFinCheckRetardColor(Dossier $dossier, $exercice)
    {
        $now = new \DateTime();

        $tbimagePeriode = $dossier->getTbimagePeriode();
        $demarrage = null;
        $premiere_cloture = null;
        if ($dossier->getDebutActivite()) {
            $demarrage = clone $dossier->getDebutActivite();
        }
        if ($dossier->getDateCloture()) {
            $premiere_cloture = clone $dossier->getDateCloture();
        }

        if ($dossier->getCloture() == 12 || $dossier->getCloture() == null) {
            $debut = \DateTime::createFromFormat('Y-m-d', $exercice . '-01-01');
        } else {
            $annee = intval($exercice) - 1;
            $mois = intval($dossier->getCloture() + 1);
            $debut = \DateTime::createFromFormat('Y-m-d', $annee . '-' . $mois . '-01');
        }

        if ($demarrage && $premiere_cloture && $premiere_cloture->format('Y') == intval($exercice)) {
            if ($this->isNbMoisExerciceOk(clone $demarrage, clone $premiere_cloture)) {
                $debut = \DateTime::createFromFormat('Y-m-d', $demarrage->format('Y-m-01'));
            }
        }

        $debut->setTime(0, 0);

        $cloture = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->getDateCloture($dossier, $exercice);

        if ($premiere_cloture && $premiere_cloture->format('Y') == intval($exercice)) {
            $cloture = $premiere_cloture;
        }
        $tmp = new \DateTime($cloture->format('Y-m-01'));

        if ($now < $tmp) {
            //SI DATE_FIN > NOW
            $mois_check = new \DateTime($now->format('Y-m-01'));

            if ($mois_check < $now) {
                //Le mois précédent est déjà en retard
                $fin_retard = new \DateTime($now->format('Y-m-01'));
            } else {
                //Le mois précédent n'est pas en retard
                $fin_retard = new \DateTime($now->format('Y-m-01'));
                $fin_retard->sub(new \DateInterval('P1M'));
            }
            $result = $this->diffInMonth(new \DateTime($debut->format('Y-m-01')), clone $fin_retard);
        } else {
            //SI DATE_FIN < NOW
            $result = $this->diffInMonth(new \DateTime($debut->format('Y-m-01')), new \DateTime($cloture->format('Y-m-01')));
        }
        return $result;
    }

    /**
     * Fin de periode à mettre en retard
     *
     * @param Dossier $dossier
     * @param $exercice
     * @param $debut
     * @param $fin
     * @return int|mixed
     * @throws \Exception
     */
    public function getFinCheckRetard(Dossier $dossier, $exercice, &$debut, &$fin)
    {
        $now = new \DateTime();
        $now->setTime(0, 0);

        $tbimagePeriode = $dossier->getTbimagePeriode();
        $periode = 'M';
        $mois_plus = 1;
        $jour = 1;
        $demarrage = null;
        $premiere_cloture = null;

        if ($dossier->getDebutActivite()) {
            $demarrage = clone $dossier->getDebutActivite();
        }
        if ($dossier->getDateCloture()) {
            $premiere_cloture = clone $dossier->getDateCloture();
        }

        if ($tbimagePeriode) {
            $periode = $tbimagePeriode->getPeriodePiece();
            $mois_plus = $tbimagePeriode->getMoisPlus();
            $jour = $tbimagePeriode->getJour();
            if($periode === 'P'){
                $periode = 'M';
                $mois_plus = 1;
            }

        }


        if ($dossier->getCloture() == 12 || $dossier->getCloture() == null) {
            $debut = \DateTime::createFromFormat('Y-m-d', $exercice . '-01-01');
        } else {
            $annee = intval($exercice) - 1;
            $mois = intval($dossier->getCloture() + 1);
            $debut = \DateTime::createFromFormat('Y-m-d', $annee . '-' . $mois . '-01');
        }

        if ($demarrage && $premiere_cloture && $premiere_cloture->format('Y') == intval($exercice)) {
            if ($this->isNbMoisExerciceOk(clone $demarrage, clone $premiere_cloture)) {
                $debut = \DateTime::createFromFormat('Y-m-d', $demarrage->format('Y-m-01'));
            }
        }

        $debut->setTime(0, 0);

        $cloture = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->getDateCloture($dossier, $exercice);

        if ($premiere_cloture && $premiere_cloture->format('Y') == intval($exercice)) {
            $cloture = $premiere_cloture;
        }
        $tmp = new \DateTime($cloture->format('Y-m-01'));

        $fin = clone $cloture;
        $fin->setTime(0, 0);

        if ($mois_plus) {
            $tmp->add(new \DateInterval('P' . $mois_plus . 'M'));
        }
        if ($jour) {
            $jour_fmt = str_pad(strval($jour), 2, '0', STR_PAD_LEFT);
            $tmp = new \DateTime($tmp->format('Y-m-' . $jour_fmt));
        }

        if ($periode == 'M') {
            //PERIODE MENSUELLE
            if ($now < $tmp) {
                //SI DATE_FIN > NOW
                if ($jour) {
                    $jour_fmt = str_pad(strval($jour), 2, '0', STR_PAD_LEFT);
                    $mois_check = new \DateTime($now->format('Y-m-' . $jour_fmt));
                } else {
                    $mois_check = new \DateTime($now->format('Y-m-01'));
                }

                if ($mois_check < $now) {
                    //Le mois précédent est déjà en retard
                    $fin_retard = new \DateTime($now->format('Y-m-01'));
                    $fin_retard->sub(new \DateInterval('P' . $mois_plus . 'M'));
                } else {
                    //Le mois précédent n'est pas en retard
                    $fin_retard = new \DateTime($now->format('Y-m-01'));
                    $fin_retard->sub(new \DateInterval('P' . ($mois_plus + 1) . 'M'));
                }
                $result = $this->diffInMonth(new \DateTime($debut->format('Y-m-01')), clone $fin_retard);
                return $result;
            } else {
                //SI DATE_FIN < NOW
                $result = $this->diffInMonth(new \DateTime($debut->format('Y-m-01')), new \DateTime($cloture->format('Y-m-01')));
                return $result;
            }
        } else {
            //PERIODE AUTRE QUE MENSUELLE
            //Chaque trimestre,quadrimestre,semestre,année doivent avoir les mêmes status (en retard ou non)
            $p_T = array(1, 1, 1, 4, 4, 4, 7, 7, 7, 10, 10, 10, 13, 13, 13, 16, 16, 16, 19, 19, 19, 22, 22, 22);
            $p_diff_T = array(1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 5, 6, 6, 6, 7, 7, 7, 8, 8, 8);

            $p_Q = array(1, 1, 1, 1, 5, 5, 5, 5, 9, 9, 9, 9, 13, 13, 13, 13, 17, 17, 17, 17, 21, 21, 21, 21);
            $p_diff_Q = array(1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 6, 6);

            $p_S = array(1, 1, 1, 1, 1, 1, 7, 7, 7, 7, 7, 7, 13, 13, 13, 13, 13, 13, 19, 19, 19, 19, 19, 19);
            $p_diff_S = array(1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 4, 4, 4, 4, 4, 4);

            $p_A = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13);
            $p_diff_A = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2);

            if ($now < $tmp) {
                //SI DATE_FIN > NOW
                if ($jour) {
                    $jour_fmt = str_pad(strval($jour), 2, '0', STR_PAD_LEFT);
                    $mois_check = new \DateTime($now->format('Y-m-' . $jour_fmt));
                } else {
                    $mois_check = new \DateTime($now->format('Y-m-01'));
                }

                if ($mois_check < $now) {
                    //Le mois précédent est déjà en retard
                    $fin_retard = new \DateTime($now->format('Y-m-01'));
                    $fin_retard->sub(new \DateInterval('P' . $mois_plus . 'M'));
                } else {
                    //Le mois précédent n'est pas en retard
                    $fin_retard = new \DateTime($now->format('Y-m-01'));
                    $fin_retard->sub(new \DateInterval('P' . ($mois_plus + 1) . 'M'));
                }
                $result = $this->diffInMonth(new \DateTime($debut->format('Y-m-01')), clone $fin_retard);
            } else {
                //SI DATE_FIN < NOW
                $result = $this->diffInMonth(new \DateTime($debut->format('Y-m-01')), new \DateTime($cloture->format('Y-m-01')));
            }
            if ($result >= 0) {
                $p_col = ${'p_diff_' . $periode};

                //dernier mois en retard
                $month_retard = $p_col[$result];

                //Liste clé ayant la même periode que $month_retard
                $keys = array_keys($p_col, $month_retard);
                if ($result == max($keys)) {
                    //si c'est le dernier mois de la période ==> on met cette période en retard
                    return $result;
                } else {
                    if ($result < max($keys)) {
                        // si c'est en milieu de la période ==> la période n'est pas encore en retard
                        $max_index = array_search($month_retard, $p_col) - 1;
                        return $max_index;
                    }
                }
            }
            return $result;

        }
    }

    /**
     * Entête pour chaque dossier dans
     * Tbimage
     *
     * @param Dossier $dossier
     * @param $exercice
     * @param $allow_edit_status
     * @param $to_be_show
     * @param Utilisateur $utilisateur
     * @return array
     * @throws \Exception
     */
    public function makeHeader(Dossier $dossier, $exercice, $allow_edit_status, &$to_be_show, Utilisateur $utilisateur)
    {
        $tbimagePeriode = $dossier->getTbimagePeriode();
        $periode = 'M';
        $mois_plus = 1;
        $jour = 12;
        $demarrage = null;
        $premiere_cloture = null;
        $nom_dossier = $dossier->getNom();


        $dossierUsers = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurDossier')
            ->getDossierUsers($dossier);

        $users = [];
        if (count($dossierUsers) > 0) {
            /** @var UtilisateurDossier $dossierUser */
            foreach ($dossierUsers as $dossierUser) {
                $users[] = [
                    'user' => $dossierUser->getUtilisateur()->getNomComplet(),
                    'email' => $dossierUser->getUtilisateur()->getEmail(),
                    'actif' => $dossierUser->getUtilisateur()->getSupprimer() ? 0 : 1,
                    'last_login' => $dossierUser->getUtilisateur()->getLastLogin() ? $dossierUser->getUtilisateur()->getLastLogin()->format('d/m/Y') : '',
                ];
            }
        }

        $user_actif = false;
        $user_logged_in = true;

        $list = '<div style="display: none" class="user-list-content">';

        $list .=  '<p style="text-align: center;"> <strong>UTILISATEURS</strong> </p> <hr style="margin-top: 0; margin-bottom: 5px;">';


        foreach ($users as $user) {
            if ($user['actif'] == 1) {
                $user_actif = true;
            }
            $user_status = 'Actif';
            if ($user['last_login'] == '') {
                $user_logged_in = false;
                $user_status = 'Créé';
            }
            if ($user['actif'] != -1) {
                $user_status = 'Bloqué';
            }
            $list .= '<strong>Utilisateur:</strong> ' . $user["user"] . '<br>';
            $list .= '<strong>Email:</strong> ' . $user["email"] . '<br>';
            $list .= '<strong>Dernière connexion:</strong> ' . $user["last_login"] . '<br>';
//            $list .= '<strong>Statut:</strong> ' . $user_status;
            $list .= '<hr>';
        }

        $list .= '</div>';
        $color = '#23c6c8';
        if (!$user_logged_in) {
            $color = '#f8ac59';
        }
        if (!$user_actif) {
            $color = '#ed5565';
        }
        $userCol = '';
        if(count($users) > 0) {

            $userCol = '<span class="user-list-details">' .
                '<i class="fa fa-user fa-lg" style="color:' . $color . ';"></i>' .
                $list .
                '</span>';
        }

        $rappelCol = '<span class="pointer edit-rappel" style="color: #d44e41;"><i class="fa fa-envelope-o fa-lg"></i></span>';

        if($utilisateur) {
            if ($utilisateur->getAccesUtilisateur()) {
                if ($utilisateur->getAccesUtilisateur()->getType() === 6 ||
                    $utilisateur->getAccesUtilisateur()->getType() === 7) {
                    $rappelCol = '<span style="color: #a2a2a2;"><i class="fa fa-envelope-o fa-lg"></i></span>';
                }
            }
        }

        $dateDeclaration = ($dossier->getTvaDate() === null) ? '' : $dossier->getTvaDate();
        $tvaMode = ($dossier->getTvaMode() === null) ? -1 : $dossier->getTvaMode();

        $abrevTvaMode = '';
        $regimeTva = 'N/A';
        if($dossier->getRegimeTva() !== null){
            if($dossier->getRegimeTva()->getCode() === 'CODE_NON_SOUMIS'){
                $abrevTvaMode = 'NS';
            }
            $regimeTva  = $dossier->getRegimeTva()->getLibelle();
        }


        switch ($tvaMode){
            case 0;
                $tvaMode= 'Accomptes semestriels';
                $abrevTvaMode = 'S';
            break;
            case 1:
                $tvaMode= 'Accomptes trimestriels';
                $abrevTvaMode = 'T';
                break;
            case 2:
                $tvaMode= 'Paiement mensuels';
                $abrevTvaMode = 'M';
                break;
            case 3:
                $tvaMode= 'Paiement trimestriels';
                $abrevTvaMode = 'T';
                break;
            case -1:
                $tvaMode = '';

        }

        $detailsTva = '<div style="display: none" class="user-list-content">';
        $detailsTva .=  '<p style="text-align: center;"> <strong>TVA</strong> </p> <hr style="margin-top: 0; margin-bottom: 5px;">';

        $detailsTva .= '<ul style="padding-left: 20px;">';
        $detailsTva .= '<li>'.$regimeTva.'</li>';
        $detailsTva .= '<li>'.$tvaMode.'</li>';
        $detailsTva .= '<li>Date: '.$dateDeclaration.'</li>';
        $detailsTva .= '</ul>';

        $detailsTva .='</div>';

        $tvaCol = '<span class="user-list-details">'.$abrevTvaMode.$detailsTva.'</span>';



        $infosDossier = '<div style="display: none" class="user-list-content">';
        $infosDossier .=  '<p style="text-align: center;"> <strong>CARACTERISTIQUES</strong> </p> <hr style="margin-top: 0; margin-bottom: 5px;">';


        if($dossier->getFormeJuridique() !== null){
            $infosDossier .=  '<strong>Forme Juridique:</strong> ' . $dossier->getFormeJuridique()->getLibelle() . '<br>';
        }
        if($dossier->getActiviteComCat3() !== null){
            $infosDossier .=  '<strong>Activités:</strong> ' . $dossier->getActiviteComCat3()->getLibelle(). '<br>';
        }
        if($dossier->getRegimeFiscal() !== null){
            $infosDossier .= '<strong>Régime fiscal: </strong>'. $dossier->getRegimeFiscal()->getLibelle(). '<br>';
        }
        if($dossier->getRegimeImposition() !== null){
            $infosDossier .= '<strong>Régime imposition: </strong>'. $dossier->getRegimeImposition()->getLibelle() . '<br>' ;
        }
        if($dossier->getFormeActivite() !== null){
            $infosDossier .= '<strong>Forme activité: </strong>'. $dossier->getFormeActivite()->getLibelle(). '<br>';
        }
        if($dossier->getTypeVente() !== null){
            $infosDossier .= '<strong>Type de vente: </strong>'. $dossier->getTypeVente()->getLibelle() . '<br>';
        }

        $infosDossier .= '<hr>';

        $mandataires = $this->getEntityManager()
            ->getRepository('AppBundle:ResponsableCsd')
            ->getMandataire($dossier);
        if(count($mandataires) > 0) {
            /** @var ResponsableCsd $mandataire */
            foreach ($mandataires as $mandataire) {
                $infosDossier .= '<strong>Dirigeant: </strong>' . $mandataire->getPrenom() . ' ' . $mandataire->getNom() . '<hr>';
            }
        }


        $status = '';
        switch ($dossier->getStatus()){
            case 1:
                $status = 'Actif';
                break;
            case 2:
                $status = 'Suspendu';
                if($dossier->getStatusDebut() !== null){
                    $status .= ' à partir de '. $dossier->getStatusDebut();
                }
                break;
            case 3:
                $status = 'Radié';
                if($dossier->getStatusDebut() !== null){
                    $status .= ' à partir de '. $dossier->getStatusDebut();
                }
                break;

        }

        $infosDossier .= '<strong>Status: </strong>'. $status;

        if($dossier->getDateStopSaisie() !== null){
            $infosDossier .= '<br> <strong>Stop Saisie: </strong>'. $dossier->getDateStopSaisie()->format("d/m/Y");
        }


        $infosDossier .= '<hr>';



        $infosDossier .='</div>';

        $infoDossierCol = '<span class="user-list-details"><i class="fa fa-vcard-o fa-lg"></i>'.$infosDossier.'</span>';


        if ($allow_edit_status) {
            $nom_dossier = '<span class="pointer edit-dossier-status">' . $dossier->getNom() . '</span>';
        }

        if ($dossier->getDebutActivite()) {
            $demarrage = clone $dossier->getDebutActivite();
        }
        if ($dossier->getDateCloture()) {
            $premiere_cloture = clone $dossier->getDateCloture();
        }

        if ($tbimagePeriode) {
            $periode = $tbimagePeriode->getPeriode();
        }

        if ($premiere_cloture) {
            if (intval($premiere_cloture->format('Y')) > intval($exercice)) {
                $to_be_show = false;
            }
        }

        if ($to_be_show) {
            if ($dossier->getCloture() == 12 || $dossier->getCloture() == null) {
                $debut = \DateTime::createFromFormat('Y-m-d', $exercice . '-01-01');
            } else {
                $annee = intval($exercice) - 1;
                $mois = intval($dossier->getCloture() + 1);
                $debut = \DateTime::createFromFormat('Y-m-d', $annee . '-' . $mois . '-01');
            }

            if ($demarrage && $premiere_cloture && $premiere_cloture->format('Y') == intval($exercice)) {
                if ($this->isNbMoisExerciceOk(clone $demarrage, clone $premiere_cloture)) {
                    $debut = $demarrage;
                }
            }

            $debut->setTime(0, 0);
            $start = new \DateTime($debut->format('Y-m-01'));
            $end = clone $start;
            $end->add(new \DateInterval('P23M'));

            $month_header = [];
            while ($start <= $end) {
                $month_header[] = $start->format('m/y');
                $start->add(new \DateInterval('P1M'));
            }
            return array_merge([$periode, $nom_dossier, $userCol, $rappelCol, $tvaCol, $infoDossierCol, $dossier->getClotureJourMois(), 'N-1', 'N', '%'], $month_header);
        }

        return [$periode, $nom_dossier, $userCol, $rappelCol, $tvaCol, $infoDossierCol, $dossier->getClotureJourMois(), 'N-1', 'N', '%'];
    }

    /**
     * Listes des images encours/exercice/dossier
     *
     * @param Dossier $dossier
     * @param $exercice
     * @return array
     */
    public function getImageEnCours(Dossier $dossier, $exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "SELECT I.*,L.date_scan,D.id AS dossier_id,D.nom AS dossier,cat.id AS categorie_id,
                cat.libelle_new AS categorie,II.image_id AS image_mere 
                FROM image I 
                LEFT JOIN separation sep ON (I.id = sep.image_id) 
                LEFT JOIN categorie cat ON (sep.categorie_id = cat.id)
                INNER JOIN lot L ON (I.lot_id = L.id) 
                INNER JOIN dossier D ON (L.dossier_id = D.id)
                LEFT JOIN image_image II ON (I.id = II.image_id)
                WHERE (I.ctrl_saisie < :ctrl_saisie OR I.ctrl_saisie IS NULL) AND I.decouper = :decouper AND I.exercice = :exercice
                AND D.id = :dossier_id AND I.supprimer  = :supprimer 
                ORDER BY L.date_scan,I.nom";
        $prep = $pdo->prepare($query);
        $prep->execute(array(
            'ctrl_saisie' => 2,
            'decouper' => 0,
            'exercice' => $exercice,
            'dossier_id' => $dossier->getId(),
            'supprimer' => 0
        ));
        $images = $prep->fetchAll(\PDO::FETCH_OBJ);
        $encours = [];

        foreach ($images as $image) {
            if ($image->image_mere == null && $image->categorie_id == null) {
                $encours[] = [
                    'id' => Boost::boost($image->id),
                    'tb-detail-image' => $image->nom . '<a onclick="showImage(event)" style="padding-left: 5px;" href="#"><i class="fa fa-file-pdf-o" style="color: red;"></i></a>',
                    'tb-detail-categorie' => $image->categorie ? $image->categorie : '',
                    'tb-detail-datescan' => $image->date_scan,
                    'tb-detail-datepiece' => '',
                    'tb-detail-periode-debut' => '',
                    'tb-detail-periode-fin' => '',
                    'tb-detail-rs' => '',
                ];
            }
        }

        return $encours;
    }

    /**
     * Liste des mois couvert par une periode d'un relevé
     * @param $periode_debut
     * @param $periode_fin
     * @return array
     * @throws \Exception
     */
    public function moisReleve($periode_debut, $periode_fin)
    {
        $mois = [];
        $limit_debut = new \DateTime('2000-01-01');
        $limit_fin = new \DateTime('2099-12-31');
        if ($periode_debut && $periode_fin) {
            $tmp_debut = new \DateTime($periode_debut);
            $fin = new \DateTime($periode_fin);

            $debut = new \DateTime($tmp_debut->format('Y-m-01'));
            if ($debut >= $limit_debut && $debut <= $limit_fin && $fin >= $limit_debut && $debut <= $limit_fin) {
                $i = 0;
                while ($debut < $fin) {
                    $i++;
                    if ($i > 20) {
                        return [];
                    }
                    $mois[] = clone $debut;
                    $debut->add(new \DateInterval('P1M'));
                }
            }
        }

        return $mois;
    }
}