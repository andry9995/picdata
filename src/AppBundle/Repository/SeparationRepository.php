<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 28/02/2017
 * Time: 14:16
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Image;
use AppBundle\Entity\Separation;
use Doctrine\ORM\EntityRepository;
use AppBundle\Functions\CustomPdoConnection;

class SeparationRepository extends EntityRepository
{
    /**
     * Maka ny categorie any @ Separation
     * @param $image
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCategoryByImage($image)
    {
        $category = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->andWhere('s.image = :the_image')
            ->setParameter('the_image', $image)
            ->join('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->join('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->getQuery()
            ->getOneOrNullResult();

        return $category;
    }

    /**
     * Maka liste Image mbola any @ Separation par Client
     * @param $clientId
     * @param $exercice
     * @return array
     */
    public function getListeImageSeparationByClient($clientId, $exercice)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->where('site.client = :clientId')
            ->andWhere('image.exercice = :exercice')
            ->andWhere('image.saisie1 <= 1')
            ->andWhere('image.saisie2 <= 1')
            ->andWhere('image.ctrlSaisie <= 1')
            ->andWhere('image.imputation <= 1')
            ->andWhere('image.ctrlImputation <= 1')
            ->andWhere('image.supprimer = 0')
            ->andWhere('image.decouper = 0')
            ->setParameter('exercice', $exercice)
            ->setParameter('clientId', $clientId)
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * * Maka ny Liste Image mbola any @ separation par Client, Nom Image
     * @param $client
     * @param $dateDebut
     * @param $dateFin
     * @param $exercice
     * @return array
     */
    public function getListeImageSeparationByClientDateScan($client, $dateDebut, $dateFin, $exercice)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->where('lot.dateScan >= :dateDebut')
            ->andWhere('lot.dateScan <= :dateFin')
            ->andWhere('image.exercice = :exercice')
            ->andWhere('image.saisie1 <= 1')
            ->andWhere('image.saisie2 <= 1')
            ->andWhere('image.ctrlSaisie <= 1')
            ->andWhere('image.imputation <= 1')
            ->andWhere('image.ctrlImputation <= 1')
            ->andWhere('image.supprimer = 0')
            ->andWhere('image.decouper = 0')
            ->andWhere('site.client= :client')
            ->setParameter('exercice', $exercice)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->setParameter('client', $client)
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Maka ny Liste Image mbola any @ separation par Client, Nom Image
     * @param $client
     * @param $nomImage
     * @param $exercice
     * @return array
     */
    public function getListeImageSeparationByClientNomImage($client, $nomImage, $exercice)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->where('image.nom = :nomImage')
            ->andWhere('image.exercice = :exercice')
            ->andWhere('image.saisie1 <= 1')
            ->andWhere('image.saisie2 <= 1')
            ->andWhere('image.ctrlSaisie <= 1')
            ->andWhere('image.imputation <= 1')
            ->andWhere('image.ctrlImputation <= 1')
            ->andWhere('image.supprimer = 0')
            ->andWhere('image.decouper = 0')
            ->andWhere('site.client= :client')
            ->setParameter('exercice', $exercice)
            ->setParameter('nomImage', $nomImage)
            ->setParameter('client', $client)
            ->getQuery()
            ->getResult();

        return $results;
    }


    /**
     * Maka ny Liste Image mbola any @ separation par Client, Id Image
     * @param $idImage
     * @return array
     */
    public function getListeImageSeparationByClientIdImage($idImage)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->where('image.id = :idImage')
            ->setParameter('idImage', $idImage)
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Maka liste Image mbola any @ Separation par Dossier
     * @param $dossierId
     * @param $exercice
     * @return array
     */
    public function getListeImageSeparationByDossier($dossierId, $exercice)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->where('dossier.id = :dossierID')
            ->andWhere('image.exercice = :exercice')
            ->andWhere('image.saisie1 <= 1')
            ->andWhere('image.saisie2 <= 1')
            ->andWhere('image.ctrlSaisie <= 1')
            ->andWhere('image.imputation <= 1')
            ->andWhere('image.ctrlImputation <= 1')
            ->andWhere('image.supprimer = 0')
            ->andWhere('image.decouper = 0')
            ->setParameter('exercice', $exercice)
            ->setParameter('dossierID', $dossierId)
            ->getQuery()
            ->getResult();

        return $results;
    }


    public function getListeCatIdScatIdSscatIdSeparationByDossier($dossier, $exercice){
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $listeSoussouscategorie = array();
        $listeCategorie = array();
        $listeSouscategorie = array();

        $query = "SELECT DISTINCT 
					  c.id as categorie_id, c.libelle_new as categorie_libelle, 					  
					  sc.id as souscategorie_id, sc.libelle_new as souscategorie_libelle,
					  ssc.id as soussouscategorie_id, ssc.libelle_new as soussosucategorie_libelle
                      FROM separation s
                      INNER JOIN image i ON (i.id = s.image_id)
                      INNER JOIN lot l ON (l.id = i.lot_id )
                      INNER JOIN dossier d ON (d.id = l.dossier_id)
					  INNER JOIN categorie c on (c.id = s.categorie_id) and c.libelle_new not like '%doublon%' and c.libelle_new not like '%mal affect%' and c.libelle_new not like '%illisible%'
					  LEFT JOIN souscategorie sc on (sc.id = s.souscategorie_id) and sc.libelle_new not like '%doublon%' and sc.libelle_new not like '%mal affect%' and sc.libelle_new not like '%illisible%' 
					  LEFT JOIN soussouscategorie ssc on (ssc.id = s.soussouscategorie_id) and ssc.libelle_new not like '%doublon%' and ssc.libelle_new not like '%mal affect%' and ssc.libelle_new not like '%illisible%'
					  WHERE d.id = :dossier_id AND (i.exercice = :exercice OR ssc.multi_exercice = 1) AND i.supprimer = 0 AND i.decouper = 0";

        $prep = $pdo->prepare($query);
        $prep->execute(array(
            'exercice' => $exercice,
            'dossier_id' => $dossier,
        ));
        $separations = $prep->fetchAll();

        foreach ($separations as $sep) {

            $soussouscategorieValide = false;
            $souscategorieValide = false;

            /**Jerena raha mitovy ny categorie azo avy @ soussouscategorie sy ny categorie tsotra*/
            if(!is_null($sep->soussouscategorie_id)){

                $query = "SELECT c.id as categorie_id FROM soussouscategorie ssc 
                              INNER JOIN souscategorie sc ON (sc.id = ssc.souscategorie_id) 
                              INNER JOIN categorie c ON (c.id = sc.categorie_id)
                              WHERE ssc.id = :soussouscategorie_id";

                $prep = $pdo->prepare($query);
                $prep->execute(array(
                    'soussouscategorie_id' => $sep->soussouscategorie_id
                ));

                $cat_id= $prep->fetchAll();

                if($cat_id[0]->categorie_id == $sep->categorie_id) {

                    $soussouscategorieValide = true;

                    if (!in_array($sep->soussouscategorie_id, $listeSoussouscategorie)) {
                        $listeSoussouscategorie[] = $sep->soussouscategorie_id;
                    }
                }
            }

            /**Jerena raha mitovy ny categorie azo avy @ souscategorie sy ny categorie tsotra*/
            if(!$soussouscategorieValide) {

                if (!is_null($sep->souscategorie_id)) {

                    $query = "SELECT c.id as categorie_id FROM souscategorie sc 
                      INNER JOIN categorie c ON (sc.categorie_id = c.id) 
                      WHERE sc.id = :souscategorie_id";


                    $prep = $pdo->prepare($query);
                    $prep->execute(array(
                        'souscategorie_id' => $sep->souscategorie_id
                    ));

                    $cat_id = $prep->fetchAll();

                    if ($cat_id[0]->categorie_id == $sep->categorie_id) {

                        $souscategorieValide = true;

                        if (!in_array($sep->souscategorie_id, $listeSouscategorie)) {
                            $listeSouscategorie[] = $sep->souscategorie_id;
                        }


                    }
                }
            }

            if(!$souscategorieValide){
                if(!is_null($sep->categorie_id)) {
                   if (!in_array($sep->categorie_id, $listeCategorie)) {
                       $listeCategorie[] = $sep->categorie_id;
                   }
               }
            }
        }

        $listeCatScatSscat = array('listeCategorie'=>$listeCategorie,
            'listesoussouscategorie'=>$listeSoussouscategorie,
            'listesouscategorie' =>$listeSouscategorie);

        return $listeCatScatSscat;
    }

    /**
     * Maka ny Liste Image mbola any @ separation par Dossier, categorie
     * @param $dossierId
     * @param $exercice
     * @param $categorieId
     * @param $sousCategorieId
     * @param $sousSouCategorieId
     * @return array
     */
    public function getListeImageSeparationByDossierCategorie($dossierId, $exercice, $categorieId, $sousCategorieId, $sousSouCategorieId, $dateScanSearch, $dateDebut, $dateFin)
    {

        if (!$dateScanSearch) {
            if ($sousSouCategorieId > 0) {
                $results = $this->getEntityManager()
                    ->getRepository('AppBundle:Separation')
                    ->createQueryBuilder('s')
                    ->innerJoin('s.image', 'image')
                    ->addSelect('image')
                    ->leftJoin('s.categorie', 'categorie')
                    ->addSelect('categorie')
                    ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                    ->addSelect('soussouscategorie')
                    ->leftJoin('image.lot', 'lot')
                    ->addSelect('lot')
                    ->innerJoin('lot.dossier', 'dossier')
                    ->where('dossier.id = :dossierID')
                    ->andWhere('image.exercice = :exercice OR soussouscategorie.multiExercice = 1')
                    ->andWhere('image.saisie1 <= 1')
                    ->andWhere('image.saisie2 <= 1')
                    ->andWhere('image.ctrlSaisie <= 1')
                    ->andWhere('image.imputation <= 1')
                    ->andWhere('image.ctrlImputation <= 1')
                    ->andWhere('image.supprimer = 0')
                    ->andWhere('image.decouper = 0')
                    ->andWhere('s.soussouscategorie = :soussouscategorie')
                    ->setParameter('exercice', $exercice)
                    ->setParameter('dossierID', $dossierId)
                    ->setParameter('soussouscategorie', $sousSouCategorieId)
                    ->getQuery()
                    ->getResult();
            } else if ($sousCategorieId > 0) {
                $results = $this->getEntityManager()
                    ->getRepository('AppBundle:Separation')
                    ->createQueryBuilder('s')
                    ->innerJoin('s.image', 'image')
                    ->addSelect('image')
                    ->leftJoin('s.categorie', 'categorie')
                    ->addSelect('categorie')
                    ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                    ->addSelect('soussouscategorie')
                    ->leftJoin('image.lot', 'lot')
                    ->addSelect('lot')
                    ->innerJoin('lot.dossier', 'dossier')
                    ->where('dossier.id = :dossierID')
                    ->andWhere('image.exercice = :exercice OR soussouscategorie.multiExercice = 1')
                    ->andWhere('image.saisie1 <= 1')
                    ->andWhere('image.saisie2 <= 1')
                    ->andWhere('image.ctrlSaisie <= 1')
                    ->andWhere('image.imputation <= 1')
                    ->andWhere('image.ctrlImputation <= 1')
                    ->andWhere('image.supprimer = 0')
                    ->andWhere('image.decouper = 0')
                    ->andWhere('soussouscategorie.souscategorie = :souscategorie')
                    ->setParameter('exercice', $exercice)
                    ->setParameter('dossierID', $dossierId)
                    ->setParameter('souscategorie', $sousCategorieId)
                    ->getQuery()
                    ->getResult();
            } else if ($categorieId > 0) {
                $results = $this->getEntityManager()
                    ->getRepository('AppBundle:Separation')
                    ->createQueryBuilder('s')
                    ->innerJoin('s.image', 'image')
                    ->addSelect('image')
                    ->leftJoin('s.categorie', 'categorie')
                    ->addSelect('categorie')
                    ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                    ->addSelect('soussouscategorie')
                    ->leftJoin('image.lot', 'lot')
                    ->addSelect('lot')
                    ->innerJoin('lot.dossier', 'dossier')
                    ->where('dossier.id = :dossierID')
                    ->andWhere('image.exercice = :exercice OR soussouscategorie.multiExercice = 1')
                    ->andWhere('image.saisie1 <= 1')
                    ->andWhere('image.saisie2 <= 1')
                    ->andWhere('image.ctrlSaisie <= 1')
                    ->andWhere('image.imputation <= 1')
                    ->andWhere('image.ctrlImputation <= 1')
                    ->andWhere('image.supprimer = 0')
                    ->andWhere('image.decouper = 0')
                    ->andWhere('s.categorie = :categorie')
                    ->setParameter('exercice', $exercice)
                    ->setParameter('dossierID', $dossierId)
                    ->setParameter('categorie', $categorieId)
                    ->getQuery()
                    ->getResult();

            } else {
                $results = $this->getEntityManager()
                    ->getRepository('AppBundle:Separation')
                    ->createQueryBuilder('s')
                    ->innerJoin('s.image', 'image')
                    ->addSelect('image')
                    ->leftJoin('s.categorie', 'categorie')
                    ->addSelect('categorie')
                    ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                    ->addSelect('soussouscategorie')
                    ->leftJoin('image.lot', 'lot')
                    ->addSelect('lot')
                    ->innerJoin('lot.dossier', 'dossier')
                    ->where('dossier.id = :dossierID')
                    ->andWhere('image.exercice = :exercice OR soussouscategorie.multiExercice = 1')
                    ->andWhere('image.saisie1 <= 1')
                    ->andWhere('image.saisie2 <= 1')
                    ->andWhere('image.ctrlSaisie <= 1')
                    ->andWhere('image.imputation <= 1')
                    ->andWhere('image.ctrlImputation <= 1')
                    ->andWhere('image.supprimer = 0')
                    ->andWhere('image.decouper = 0')
                    ->setParameter('exercice', $exercice)
                    ->setParameter('dossierID', $dossierId)
                    ->getQuery()
                    ->getResult();
            }
        }
        else {
            if ($sousSouCategorieId > 0) {
                $qb = $this->getEntityManager()
                    ->getRepository('AppBundle:Separation')
                    ->createQueryBuilder('s')
                    ->innerJoin('s.image', 'image')
                    ->addSelect('image')
                    ->leftJoin('s.categorie', 'categorie')
                    ->addSelect('categorie')
                    ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                    ->addSelect('soussouscategorie')
                    ->leftJoin('image.lot', 'lot')
                    ->addSelect('lot')
                    ->innerJoin('lot.dossier', 'dossier')
                    ->where('dossier.id = :dossierID')
                    ->andWhere('image.exercice = :exercice OR soussouscategorie.multiExercice = 1')
                    ->andWhere('image.saisie1 <= 1')
                    ->andWhere('image.saisie2 <= 1')
                    ->andWhere('image.ctrlSaisie <= 1')
                    ->andWhere('image.imputation <= 1')
                    ->andWhere('image.ctrlImputation <= 1')
                    ->andWhere('image.supprimer = 0')
                    ->andWhere('image.decouper = 0')
                    ->andWhere('s.soussouscategorie = :soussouscategorie')
                    ->setParameter('exercice', $exercice)
                    ->setParameter('dossierID', $dossierId)
                    ->setParameter('soussouscategorie', $sousSouCategorieId);


                if ($dateDebut !== '' && $dateFin !== '') {
                    $qb->andWhere('lot.dateScan >= :dateDebut')
                        ->setParameter(':dateDebut', $dateDebut)
                        ->andWhere('lot.dateScan <= :dateFin')
                        ->setParameter(':dateFin', $dateFin);
                }
                $results = $qb->getQuery()
                    ->getResult();

            } else if ($sousCategorieId > 0) {
                $qb = $this->getEntityManager()
                    ->getRepository('AppBundle:Separation')
                    ->createQueryBuilder('s')
                    ->innerJoin('s.image', 'image')
                    ->addSelect('image')
                    ->leftJoin('s.categorie', 'categorie')
                    ->addSelect('categorie')
                    ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                    ->addSelect('soussouscategorie')
                    ->leftJoin('image.lot', 'lot')
                    ->addSelect('lot')
                    ->innerJoin('lot.dossier', 'dossier')
                    ->where('dossier.id = :dossierID')
                    ->andWhere('image.exercice = :exercice OR soussouscategorie.multiExercice = 1')
                    ->andWhere('image.saisie1 <= 1')
                    ->andWhere('image.saisie2 <= 1')
                    ->andWhere('image.ctrlSaisie <= 1')
                    ->andWhere('image.imputation <= 1')
                    ->andWhere('image.ctrlImputation <= 1')
                    ->andWhere('image.supprimer = 0')
                    ->andWhere('image.decouper = 0')
                    ->andWhere('soussouscategorie.souscategorie = :souscategorie')
                    ->setParameter('exercice', $exercice)
                    ->setParameter('dossierID', $dossierId)
                    ->setParameter('souscategorie', $sousCategorieId);

                if ($dateDebut !== '' && $dateFin !== '') {
                    $qb->andWhere('lot.dateScan >= :dateDebut')
                        ->setParameter(':dateDebut', $dateDebut)
                        ->andWhere('lot.dateScan <= :dateFin')
                        ->setParameter(':dateFin', $dateFin);
                }

                $results = $qb->getQuery()
                    ->getResult();

            } else if ($categorieId > 0) {
                $qb = $this->getEntityManager()
                    ->getRepository('AppBundle:Separation')
                    ->createQueryBuilder('s')
                    ->innerJoin('s.image', 'image')
                    ->addSelect('image')
                    ->leftJoin('s.categorie', 'categorie')
                    ->addSelect('categorie')
                    ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                    ->addSelect('soussouscategorie')
                    ->leftJoin('image.lot', 'lot')
                    ->addSelect('lot')
                    ->innerJoin('lot.dossier', 'dossier')
                    ->where('dossier.id = :dossierID')
                    ->andWhere('image.exercice = :exercice OR soussouscategorie.multiExercice = 1')
                    ->andWhere('image.saisie1 <= 1')
                    ->andWhere('image.saisie2 <= 1')
                    ->andWhere('image.ctrlSaisie <= 1')
                    ->andWhere('image.imputation <= 1')
                    ->andWhere('image.ctrlImputation <= 1')
                    ->andWhere('image.supprimer = 0')
                    ->andWhere('image.decouper = 0')
                    ->andWhere('s.categorie = :categorie')
                    ->setParameter('exercice', $exercice)
                    ->setParameter('dossierID', $dossierId)
                    ->setParameter('categorie', $categorieId);

                if ($dateDebut !== '' && $dateFin !== '') {
                    $qb->andWhere('lot.dateScan >= :dateDebut')
                        ->setParameter(':dateDebut', $dateDebut)
                        ->andWhere('lot.dateScan <= :dateFin')
                        ->setParameter(':dateFin', $dateFin);
                }


                $results = $qb->getQuery()
                    ->getResult();

            } else {

                $qb = $this->getEntityManager()
                    ->getRepository('AppBundle:Separation')
                    ->createQueryBuilder('s')
                    ->innerJoin('s.image', 'image')
                    ->addSelect('image')
                    ->leftJoin('s.categorie', 'categorie')
                    ->addSelect('categorie')
                    ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                    ->addSelect('soussouscategorie')
                    ->leftJoin('image.lot', 'lot')
                    ->addSelect('lot')
                    ->innerJoin('lot.dossier', 'dossier')
                    ->where('dossier.id = :dossierID')
                    ->andWhere('image.exercice = :exercice OR soussouscategorie.multiExercice = 1')
                    ->andWhere('image.saisie1 <= 1')
                    ->andWhere('image.saisie2 <= 1')
                    ->andWhere('image.ctrlSaisie <= 1')
                    ->andWhere('image.imputation <= 1')
                    ->andWhere('image.ctrlImputation <= 1')
                    ->andWhere('image.supprimer = 0')
                    ->andWhere('image.decouper = 0')
                    ->setParameter('exercice', $exercice)
                    ->setParameter('dossierID', $dossierId);

                if($dateDebut !== '' && $dateFin !== ''){
                    $qb->andWhere('lot.dateScan >= :dateDebut')
                        ->setParameter(':dateDebut', $dateDebut)
                        ->andWhere('lot.dateScan <= :dateFin')
                        ->setParameter(':dateFin', $dateFin);
                }


                $results = $qb->getQuery()
                    ->getResult();
            }

        }

        return $results;
    }

    /**
     * Maka ny Liste Image mbola any @ separation par Dossier, Nom Image
     * @param $dossier
     * @param $nomImage
     * @param $exercice
     * @return array
     */
    public function getListeImageSeparationByDossierNomImage($dossier, $nomImage, $exercice)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->where('lot.dossier', 'dossier')
            ->andWhere('image.nom = :nomImage')
            ->andWhere('image.exercice = :exercice')
            ->andWhere('image.saisie1 <= 1')
            ->andWhere('image.saisie2 <= 1')
            ->andWhere('image.ctrlSaisie <= 1')
            ->andWhere('image.imputation <= 1')
            ->andWhere('image.ctrlImputation <= 1')
            ->andWhere('image.supprimer = 0')
            ->andWhere('image.decouper = 0')
            ->andWhere('lot.dossier = :dossier')
            ->setParameter('exercice', $exercice)
            ->setParameter('nomImage', $nomImage)
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();

        return $results;
    }





    public function getListeImageSeparationByDossierIdsNomImage($dossierIds, $nomImage, $exercice)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->innerJoin('lot.dossier', 'dossier')
//            ->where('lot.dossier', 'dossier')
            ->andWhere('image.nom = :nomImage')
            ->andWhere('image.exercice = :exercice')
            ->andWhere('image.saisie1 <= 1')
            ->andWhere('image.saisie2 <= 1')
            ->andWhere('image.ctrlSaisie <= 1')
            ->andWhere('image.imputation <= 1')
            ->andWhere('image.ctrlImputation <= 1')
            ->andWhere('image.supprimer = 0')
            ->andWhere('image.decouper = 0')
            ->andWhere('dossier.id IN (:dossierIds)')
            ->setParameter('exercice', $exercice)
            ->setParameter('nomImage', $nomImage)
            ->setParameter('dossierIds', ($dossierIds))
            ->getQuery()
            ->getResult();

        return $results;
    }


    /**
     * * Maka ny Liste Image mbola any @ separation par Dossier, Nom Image
     * @param $dossier
     * @param $dateDebut
     * @param $dateFin
     * @param $exercice
     * @return array
     */
    public function getListeImageSeparationByDossierDateScan($dossier, $dateDebut, $dateFin, $exercice)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->where('lot.dateScan >= :dateDebut')
            ->andWhere('lot.dateScan <= :dateFin')
            ->andWhere('image.exercice = :exercice')
            ->andWhere('image.saisie1 <= 1')
            ->andWhere('image.saisie2 <= 1')
            ->andWhere('image.ctrlSaisie <= 1')
            ->andWhere('image.imputation <= 1')
            ->andWhere('image.ctrlImputation <= 1')
            ->andWhere('image.supprimer = 0')
            ->andWhere('image.decouper = 0')
            ->andWhere('dossier = :dossier')
            ->setParameter('exercice', $exercice)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Maka liste Image mbola any @ Separation par Site
     * @param $siteId
     * @param $exercice
     * @return array
     */
    public function getListeImageSeparationBySite($siteId, $exercice)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->where('site.id = :siteId')
            ->andWhere('image.exercice = :exercice')
            ->andWhere('image.saisie1 <= 1')
            ->andWhere('image.saisie2 <= 1')
            ->andWhere('image.ctrlSaisie <= 1')
            ->andWhere('image.imputation <= 1')
            ->andWhere('image.ctrlImputation <= 1')
            ->andWhere('image.supprimer = 0')
            ->andWhere('image.decouper = 0')
            ->setParameter('exercice', $exercice)
            ->setParameter('siteId', $siteId)
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * * Maka ny Liste Image mbola any @ separation par Site, Nom Image
     * @param $site
     * @param $dateDebut
     * @param $dateFin
     * @param $exercice
     * @return array
     */
    public function getListeImageSeparationBySiteDateScan($site, $dateDebut, $dateFin, $exercice)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->where('lot.dateScan >= :dateDebut')
            ->andWhere('lot.dateScan <= :dateFin')
            ->andWhere('image.exercice = :exercice')
            ->andWhere('image.saisie1 <= 1')
            ->andWhere('image.saisie2 <= 1')
            ->andWhere('image.ctrlSaisie <= 1')
            ->andWhere('image.imputation <= 1')
            ->andWhere('image.ctrlImputation <= 1')
            ->andWhere('image.supprimer = 0')
            ->andWhere('image.decouper = 0')
            ->andWhere('dossier.site = :site')
            ->setParameter('exercice', $exercice)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->setParameter('site', $site)
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Maka ny Liste Image mbola any @ separation par Site, Nom Image
     * @param $site
     * @param $nomImage
     * @param $exercice
     * @return array
     */
    public function getListeImageSeparationBySiteNomImage($site, $nomImage, $exercice)
    {
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image', 'image')
            ->addSelect('image')
            ->leftJoin('s.categorie', 'categorie')
            ->addSelect('categorie')
            ->leftJoin('s.soussouscategorie', 'soussouscategorie')
            ->addSelect('soussouscategorie')
            ->leftJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->where('image.nom = :nomImage')
            ->andWhere('image.exercice = :exercice')
            ->andWhere('image.saisie1 <= 1')
            ->andWhere('image.saisie2 <= 1')
            ->andWhere('image.ctrlSaisie <= 1')
            ->andWhere('image.imputation <= 1')
            ->andWhere('image.ctrlImputation <= 1')
            ->andWhere('image.supprimer = 0')
            ->andWhere('image.decouper = 0')
            ->andWhere('dossier.site = :site')
            ->setParameter('exercice', $exercice)
            ->setParameter('nomImage', $nomImage)
            ->setParameter('site', $site)
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * Maka ny Liste Image mbola any @ separation par Utilisateur, Categorie, Dossier
     * @param $utilisateurId
     * @param $categorieId
     * @param $dossierId
     * @param $exercice
     * @return array
     */
    public function getListeImageSeparationByUtilisateurCategorieDossier($utilisateurId, $categorieId, $dossierId, $exercice)
    {
        if ($dossierId > 0) {
            $results = $this->getEntityManager()
                ->getRepository('AppBundle:Separation')
                ->createQueryBuilder('s')
                ->innerJoin('s.image', 'image')
                ->addSelect('image')
                ->leftJoin('s.categorie', 'categorie')
                ->addSelect('categorie')
                ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                ->addSelect('soussouscategorie')
                ->leftJoin('image.lot', 'lot')
                ->addSelect('lot')
                ->innerJoin('lot.utilisateur', 'utilisateur')
                ->innerJoin('lot.dossier', 'dossier')
                ->where('dossier.id = :dossierID')
                ->andWhere('utilisateur.id = :utilisateurID')
                ->andWhere('image.exercice = :exercice or soussouscategorie.multiExercice = 1')
                ->andWhere('image.saisie1 <= 1')
                ->andWhere('image.saisie2 <= 1')
                ->andWhere('image.ctrlSaisie <= 1')
                ->andWhere('image.imputation <= 1')
                ->andWhere('image.ctrlImputation <= 1')
                ->andWhere('image.supprimer = 0')
                ->andWhere('image.decouper = 0')
                ->andWhere('s.categorie = :categorie')
                ->setParameter('dossierID', $dossierId)
                ->setParameter('utilisateurID', $utilisateurId)
                ->setParameter('exercice', $exercice)
                ->setParameter('categorie', $categorieId)
                ->getQuery()
                ->getResult();

        } else if ($categorieId > 0) {
            $results = $this->getEntityManager()
                ->getRepository('AppBundle:Separation')
                ->createQueryBuilder('s')
                ->innerJoin('s.image', 'image')
                ->addSelect('image')
                ->leftJoin('s.categorie', 'categorie')
                ->addSelect('categorie')
                ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                ->addSelect('soussouscategorie')
                ->leftJoin('image.lot', 'lot')
                ->addSelect('lot')
                ->innerJoin('lot.utilisateur', 'utilisateur')
                ->where('utilisateur.id = :utilisateurID')
                ->andWhere('image.exercice = :exercice or soussouscategorie.multiExercice = 1')
                ->andWhere('image.saisie1 <= 1')
                ->andWhere('image.saisie2 <= 1')
                ->andWhere('image.ctrlSaisie <= 1')
                ->andWhere('image.imputation <= 1')
                ->andWhere('image.ctrlImputation <= 1')
                ->andWhere('image.supprimer = 0')
                ->andWhere('image.decouper = 0')
                ->andWhere('s.categorie = :categorie')
                ->setParameter('utilisateurID', $utilisateurId)
                ->setParameter('exercice', $exercice)
                ->setParameter('categorie', $categorieId)
                ->getQuery()
                ->getResult();
        } else {
            $results = $this->getEntityManager()
                ->getRepository('AppBundle:Separation')
                ->createQueryBuilder('s')
                ->innerJoin('s.image', 'image')
                ->addSelect('image')
                ->leftJoin('image.lot', 'lot')
                ->leftJoin('s.soussouscategorie', 'soussouscategorie')
                ->addSelect('lot')
                ->innerJoin('lot.utilisateur', 'utilisateur')
                ->where('utilisateur.id = :utilisateurID')
                ->andWhere('image.exercice = :exercice  or soussouscategorie.mulitExercice = 1')
                ->andWhere('image.saisie1 <= 1')
                ->andWhere('image.saisie2 <= 1')
                ->andWhere('image.ctrlSaisie <= 1')
                ->andWhere('image.imputation <= 1')
                ->andWhere('image.ctrlImputation <= 1')
                ->andWhere('image.supprimer = 0')
                ->andWhere('image.decouper = 0')
                ->setParameter('utilisateurID', $utilisateurId)
                ->setParameter('exercice', $exercice)
                ->getQuery()
                ->getResult();
        }

        return $results;
    }



    public function getCategorieImageByNomImage($nomImage){
        $results = $this->getEntityManager()
            ->getRepository('AppBundle:Separation')
            ->createQueryBuilder('s')
            ->innerJoin('s.image','image')
            ->leftJoin('s.categorie','categorie')
            ->leftJoin('s.soussouscategorie','soussouscategorie')
            ->where('image.nom = :nomImage')
            ->setParameter('nomImage', $nomImage)
            ->getQuery()
            ->getResult();

        return $results;
    }


    public function getListeCategorieIdSoussouscategorieIdSeparationByUtilisateur($utilisateur,$exercice){
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $listeSoussouscategorie = array();
        $listeCategorie = array();

        $query = "SELECT s.image_id as image_id,s.categorie_id as categorie_id, s.soussouscategorie_id as soussouscategorie_id
                  FROM separation s
                  INNER JOIN image i ON (i.id = s.image_id)
                  INNER JOIN lot l ON (l.id = i.lot_id )
                  WHERE l.utilisateur_id = :utilisateur_id AND i.exercice = :exercice AND i.supprimer =  0 AND i.decouper = 0 AND (i.saisie1 <= 1 AND i.saisie2 <= 1)";

        $prep = $pdo->prepare($query);
        $prep->execute(array(
            'exercice' => $exercice,
            'utilisateur_id' => $utilisateur,
        ));
        $separations = $prep->fetchAll();

        foreach ($separations as $sep) {


            if(!is_null($sep->soussouscategorie_id)){
                if(!in_array($sep->soussouscategorie_id, $listeSoussouscategorie))
                {
                    $listeSoussouscategorie[] = $sep->soussouscategorie_id;
                }
            }
            else{

                if(!is_null($sep->categorie_id)) {
                    if (!in_array($sep->categorie_id, $listeCategorie)) {
                        $listeCategorie[] = $sep->categorie_id;
                    }
                }
            }
        }

        $listeCategorieSoussouscategorie = array('listeCategorie'=>$listeCategorie,'listesoussouscategorie'=>$listeSoussouscategorie);

        return $listeCategorieSoussouscategorie;
    }


//    public function getInfoSeparationImagesByClientSiteDossier($client_id, $site_id, $dossier_id, $exercice, $dateScan, $dateDebut, $dateFin)
//    {
//        $con = new CustomPdoConnection();
//        $pdo = $con->connect();
//
//        $infoSeparations = array();
//
//        if ($dateScan == false) {
//
//            if ($dossier_id != 0) {
//
//                $query = "SELECT i.id AS image_id, i.nom AS nom, i.exercice AS exercice, l.date_scan AS date_scan, ssc.id AS soussouscategorie_id,
//                      ssc.libelle AS soussouscategorie_libelle, c.id AS categorie_id, c.libelle AS categorie_libelle,
//                      d.id AS dossier_id, d.nom AS dossier_nom, ssc.libelle_new AS soussouscategorie_libelle_new
//                      FROM separation s
//                      INNER JOIN image i ON (i.id = s.image_id)
//                      INNER JOIN lot l ON (l.id = i.lot_id)
//                      INNER JOIN dossier d ON (d.id = l.dossier_id)
//                      LEFT JOIN soussouscategorie ssc ON (ssc.id = s.soussouscategorie_id)
//                      LEFT JOIN categorie c ON (c.id = s.categorie_id)
//                      WHERE i.saisie1 <= 1  AND i.saisie2 <= 1 AND i.ctrl_saisie <= 1  AND i.imputation <= 1 AND i.ctrl_imputation <= 1
//                      AND i.exercice = :exercice AND d.id = :dossier_id";
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'dossier_id' => $dossier_id,
//                    'exercice' => $exercice
//                ));
//
//                $infoSeparations = $prep->fetchAll();
//            } else if ($site_id != 0) {
//
//                $query = "SELECT i.id AS image_id,i.nom AS nom, i.exercice AS exercice, l.date_scan AS date_scan, ssc.id AS soussouscategorie_id,
//                      ssc.libelle AS soussouscategorie_libelle, c.id AS categorie_id, c.libelle AS categorie_libelle,
//                      d.id AS dossier_id, d.nom AS dossier_nom, ssc.libelle_new AS soussouscategorie_libelle_new
//                      FROM separation s
//                      INNER JOIN image i ON (i.id = s.image_id)
//                      INNER JOIN lot l ON (l.id = i.lot_id)
//                      INNER JOIN dossier d ON (d.id = l.dossier_id)
//                      INNER JOIN site si ON (si.id = d.site_id)
//                      LEFT JOIN soussouscategorie ssc ON (ssc.id = s.soussouscategorie_id)
//                      LEFT JOIN categorie c ON (c.id = s.categorie_id)
//                      WHERE i.saisie1 <= 1  AND i.saisie2 <= 1 AND i.ctrl_saisie <= 1  AND i.imputation <= 1 AND i.ctrl_imputation <= 1
//                      AND i.exercice = :exercice AND si.id = :site_id";
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'site_id' => $site_id,
//                    'exercice' => $exercice
//                ));
//
//                $infoSeparations = $prep->fetchAll();
//
//            } else if ($client_id != 0) {
//                $query = "SELECT i.id AS image_id, i.nom AS nom, i.exercice AS exercice, l.date_scan AS date_scan, ssc.id AS soussouscategorie_id,
//                      ssc.libelle AS soussouscategorie_libelle, c.id AS categorie_id, c.libelle AS categorie_libelle,
//                      d.id AS dossier_id, d.nom AS dossier_nom, ssc.libelle_new AS soussouscategorie_libelle_new
//                      FROM separation s
//                      INNER JOIN image i ON (i.id = s.image_id)
//                      INNER JOIN lot l ON (l.id = i.lot_id)
//                      INNER JOIN dossier d ON (d.id = l.dossier_id)
//                      INNER JOIN site si ON (si.id = d.site_id)
//                      INNER JOIN client cl ON (cl.id = si.client_id)
//                      LEFT JOIN soussouscategorie ssc ON (ssc.id = s.soussouscategorie_id)
//                      LEFT JOIN categorie c ON (c.id = s.categorie_id)
//                      WHERE i.saisie1 <= 1  AND i.saisie2 <= 1 AND i.ctrl_saisie <= 1  AND i.imputation <= 1 AND i.ctrl_imputation <= 1
//                      AND i.exercice = :exercice AND cl.id = :client_id";
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'client_id' => $client_id,
//                    'exercice' => $exercice
//                ));
//
//                $infoSeparations = $prep->fetchAll();
//            }
//
//        } else {
//
//            if ($dossier_id != 0) {
//
//                $query = "SELECT i.id AS image_id, i.nom AS nom, i.exercice AS exercice, l.date_scan AS date_scan, ssc.id AS soussouscategorie_id,
//                      ssc.libelle AS soussouscategorie_libelle, c.id AS categorie_id, c.libelle AS categorie_libelle,
//                      d.id AS dossier_id, d.nom AS dossier_nom, ssc.libelle_new AS soussouscategorie_libelle_new
//                      FROM separation s
//                      INNER JOIN image i ON (i.id = s.image_id)
//                      INNER JOIN lot l ON (l.id = i.lot_id)
//                      INNER JOIN dossier d ON (d.id = l.dossier_id)
//                      LEFT JOIN soussouscategorie ssc ON (ssc.id = s.soussouscategorie_id)
//                      LEFT JOIN categorie c ON (c.id = s.categorie_id)
//                      WHERE i.saisie1 <= 1  AND i.saisie2 <= 1 AND i.ctrl_saisie <= 1  AND i.imputation <= 1 AND i.ctrl_imputation <= 1
//                      AND i.exercice = :exercice AND d.id = :dossier_id AND l.date_scan>= :dateDebut AND l.date_scan<= :dateFin";
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'dossier_id' => $dossier_id,
//                    'exercice' => $exercice,
//                    'dateDebut' => $dateDebut,
//                    'dateFin' => $dateFin
//                ));
//
//                $infoSeparations = $prep->fetchAll();
//            } else if ($site_id != 0) {
//
//                $query = "SELECT i.id AS image_id,i.nom AS nom, i.exercice AS exercice, l.date_scan AS date_scan, ssc.id AS soussouscategorie_id,
//                      ssc.libelle AS soussouscategorie_libelle, c.id AS categorie_id, c.libelle AS categorie_libelle,
//                      d.id AS dossier_id, d.nom AS dossier_nom, ssc.libelle_new AS soussouscategorie_libelle_new
//                      FROM separation s
//                      INNER JOIN image i ON (i.id = s.image_id)
//                      INNER JOIN lot l ON (l.id = i.lot_id)
//                      INNER JOIN dossier d ON (d.id = l.dossier_id)
//                      INNER JOIN site si ON (si.id = d.site_id)
//                      LEFT JOIN soussouscategorie ssc ON (ssc.id = s.soussouscategorie_id)
//                      LEFT JOIN categorie c ON (c.id = s.categorie_id)
//                      WHERE i.saisie1 <= 1  AND i.saisie2 <= 1 AND i.ctrl_saisie <= 1  AND i.imputation <= 1 AND i.ctrl_imputation <= 1
//                      AND i.exercice = :exercice AND si.id = :site_id AND l.date_scan>= :dateDebut AND l.date_scan<= :dateFin";
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'site_id' => $site_id,
//                    'exercice' => $exercice,
//                    'dateDebut' => $dateDebut,
//                    'dateFin' => $dateFin
//                ));
//
//                $infoSeparations = $prep->fetchAll();
//
//            } else if ($client_id != 0) {
//                $query = "SELECT i.id AS image_id, i.nom AS nom, i.exercice AS exercice, l.date_scan AS date_scan, ssc.id AS soussouscategorie_id,
//                      ssc.libelle AS soussouscategorie_libelle, c.id AS categorie_id, c.libelle AS categorie_libelle,
//                      d.id AS dossier_id, d.nom AS dossier_nom,ssc.libelle_new AS soussouscategorie_libelle_new
//                      FROM separation s
//                      INNER JOIN image i ON (i.id = s.image_id)
//                      INNER JOIN lot l ON (l.id = i.lot_id)
//                      INNER JOIN dossier d ON (d.id = l.dossier_id)
//                      INNER JOIN site si ON (si.id = d.site_id)
//                      INNER JOIN client cl ON (cl.id = si.client_id)
//                      LEFT JOIN soussouscategorie ssc ON (ssc.id = s.soussouscategorie_id)
//                      LEFT JOIN categorie c ON (c.id = s.categorie_id)
//                      WHERE i.saisie1 <= 1  AND i.saisie2 <= 1 AND i.ctrl_saisie <= 1  AND i.imputation <= 1 AND i.ctrl_imputation <= 1
//                      AND i.exercice = :exercice AND cl.id = :client_id AND l.date_scan>= :dateDebut AND l.date_scan<= :dateFin";
//                $prep = $pdo->prepare($query);
//                $prep->execute(array(
//                    'client_id' => $client_id,
//                    'exercice' => $exercice,
//                    'dateDebut' => $dateDebut,
//                    'dateFin' => $dateFin
//                ));
//
//                $infoSeparations = $prep->fetchAll();
//            }
//
//        }
//        return $infoSeparations;
//
//    }


    public function getInfoSeparationImagesByDossierIds($dossier_ids, $exercice, $dateScan, $dateDebut, $dateFin)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();


        if ($dateScan == false) {

            $query = "SELECT i.id AS image_id, i.nom AS nom, i.exercice AS exercice, l.date_scan AS date_scan, ssc.id AS soussouscategorie_id, 
                      ssc.libelle AS soussouscategorie_libelle, c.id AS categorie_id, c.libelle AS categorie_libelle,
                      d.id AS dossier_id, d.nom AS dossier_nom, ssc.libelle_new AS soussouscategorie_libelle_new, i.ext_image AS ext_image
                      FROM separation s 
                      INNER JOIN image i ON (i.id = s.image_id)
                      INNER JOIN lot l ON (l.id = i.lot_id)
                      INNER JOIN dossier d ON (d.id = l.dossier_id)
                      LEFT JOIN soussouscategorie ssc ON (ssc.id = s.soussouscategorie_id)
                      LEFT JOIN categorie c ON (c.id = s.categorie_id)
                      WHERE i.saisie1 <= 1  AND i.saisie2 <= 1 AND i.ctrl_saisie <= 1  AND i.imputation <= 1 AND i.ctrl_imputation <= 1 
                      AND i.exercice = :exercice AND i.supprimer = 0 AND i.decouper = 0 AND d.id IN (" . $dossier_ids . ")";
            $prep = $pdo->prepare($query);
            $prep->execute(array(

                'exercice' => $exercice
            ));

            $infoSeparations = $prep->fetchAll();


        } else {

            if($dateDebut !== '' && $dateFin !== '') {
                $query = "SELECT i.id AS image_id, i.nom AS nom, i.exercice AS exercice, l.date_scan AS date_scan, ssc.id AS soussouscategorie_id, 
                      ssc.libelle AS soussouscategorie_libelle, c.id AS categorie_id, c.libelle AS categorie_libelle,
                      d.id AS dossier_id, d.nom AS dossier_nom, ssc.libelle_new AS soussouscategorie_libelle_new, i.ext_image AS ext_image
                      FROM separation s 
                      INNER JOIN image i ON (i.id = s.image_id)
                      INNER JOIN lot l ON (l.id = i.lot_id)
                      INNER JOIN dossier d ON (d.id = l.dossier_id)
                      LEFT JOIN soussouscategorie ssc ON (ssc.id = s.soussouscategorie_id)
                      LEFT JOIN categorie c ON (c.id = s.categorie_id)
                      WHERE i.saisie1 <= 1  AND i.saisie2 <= 1 AND i.ctrl_saisie <= 1  AND i.imputation <= 1 AND i.ctrl_imputation <= 1 
                      AND i.exercice = :exercice AND i.supprimer = 0 AND i.decouper = 0 AND d.id IN (" . $dossier_ids . ") AND l.date_scan>= :dateDebut AND l.date_scan<= :dateFin";
                $prep = $pdo->prepare($query);
                $prep->execute(array(
                    'exercice' => $exercice,
                    'dateDebut' => $dateDebut,
                    'dateFin' => $dateFin
                ));
            }
            else{
                $query = "SELECT i.id AS image_id, i.nom AS nom, i.exercice AS exercice, l.date_scan AS date_scan, ssc.id AS soussouscategorie_id, 
                      ssc.libelle AS soussouscategorie_libelle, c.id AS categorie_id, c.libelle AS categorie_libelle,
                      d.id AS dossier_id, d.nom AS dossier_nom, ssc.libelle_new AS soussouscategorie_libelle_new, i.ext_image AS ext_image
                      FROM separation s 
                      INNER JOIN image i ON (i.id = s.image_id)
                      INNER JOIN lot l ON (l.id = i.lot_id)
                      INNER JOIN dossier d ON (d.id = l.dossier_id)
                      LEFT JOIN soussouscategorie ssc ON (ssc.id = s.soussouscategorie_id)
                      LEFT JOIN categorie c ON (c.id = s.categorie_id)
                      WHERE i.saisie1 <= 1  AND i.saisie2 <= 1 AND i.ctrl_saisie <= 1  AND i.imputation <= 1 AND i.ctrl_imputation <= 1 
                      AND i.exercice = :exercice AND i.supprimer = 0 AND i.decouper = 0 AND d.id IN (" . $dossier_ids . ") ";
                $prep = $pdo->prepare($query);
                $prep->execute(array(
                    'exercice' => $exercice
                ));
            }

            $infoSeparations = $prep->fetchAll();
        }
        return $infoSeparations;

    }

    /**
     * @param Image $image
     * @return Separation
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSeparationByImage(Image $image)
    {
        return $this->createQueryBuilder('s')
            ->where('s.image = :image')
            ->setParameter('image', $image)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}