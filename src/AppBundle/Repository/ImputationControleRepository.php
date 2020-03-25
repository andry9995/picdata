<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 29/08/2017
 * Time: 17:28
 */

namespace AppBundle\Repository;

use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\JournalDossier;
use AppBundle\Entity\Souscategorie;
use AppBundle\Entity\LibelleDossier;
use AppBundle\Entity\LibelleItem;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\TvaImputationControle;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;
use Proxies\__CG__\AppBundle\Entity\Categorie;

class ImputationControleRepository extends EntityRepository
{
    /**
     * @param TvaImputationControle $tvaImputationControle
     * @return ImputationControle
     */
    public function getImputationControle(TvaImputationControle $tvaImputationControle)
    {
        return $this->createQueryBuilder('ic')
            ->where('ic.image = :image')
            ->setParameter('image', $tvaImputationControle->getImage())
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * Maka ny montant ttc & imputation par tiers
     * @param Tiers $tiers
     * @param string $q
     * @return array
     */
    public function getFactureClientsByTiers(Tiers $tiers, $exerice,  $q = '')
    {
        $imputation = array();

        if($q !== ''){
            $imputation = $this->createQueryBuilder('imputationControle')
                ->innerJoin('imputationControle.image','image')
                ->innerJoin('image.lot', 'lot')
                ->innerJoin('lot.dossier', 'dossier')
                ->where('imputationControle.numFacture LIKE :q')
                ->setParameter('q', $q.'%')
                ->andWhere('dossier = :dossier')
                ->setParameter('dossier' , $tiers->getDossier())
                ->andWhere('image.exercice = :exercice')
                ->setParameter('exercice', $exerice)
                ->getQuery()
                ->getResult();

            $images = array();

            /** @var ImputationControle $imp */
            foreach ($imputation as $imp){
                $images[] = $imp->getImage();
            }

            $tvas = $this->getEntityManager()
                ->getRepository('AppBundle:TvaImputationControle')
                ->getTvaImputationControleByImages($images, $exerice);
        }
        else{

            /** @var TvaImputationControle $tva */
            $tvas = $this->getEntityManager()
                ->getRepository('AppBundle:TvaImputationControle')
                ->getImageByTier($tiers);
        }



        $images = array();
        $lastImage = null;
        $imageTtc = array();

        $lastImage = null;
        $index = -1;
        $montant = 0;

        foreach ($tvas as $tva) {

            $montantHt = $tva->getMontantHt();
            $tvaTaux = $tva->getTvaTaux()->getTaux();
            $montantTva = $montantHt *  $tvaTaux / 100;
            $montantTtc = $montantHt + $montantTva;

            if (!in_array($tva->getImage(), $images)) {
                $images[] = $tva->getImage();
                $imageTtc[] = array('image' => $tva->getImage(), 'montant' => round($montantTtc, 2));
                $montant = $montantTtc;
                $index++;
            } else {
                $montant += $montantTtc;
                $imageTtc[$index]['montant'] =  round($montant, 2);
            }
        }

        if($q === '') {
            $imputation = $this->createQueryBuilder('ic')
                ->innerJoin('ic.image', 'img')
                ->where('ic.image IN (:images)')
                ->setParameter('images', array_values($images))
                ->orderBy('img.nom')
                ->select('ic')
                ->getQuery()
                ->getResult();
        }

        $final = array();
        $index = 0;

        foreach ($imageTtc as $ttc) {
            /** @var Image $imgTtc */
            $imgTtc = $ttc['image'];
            $imgImp = $imputation[$index]->getImage();

            if($imgImp === $imgTtc) {
                    $final[] = array('imputation' => $imputation[$index], 'montant' => $ttc['montant']);
            }
            $index++;
        }

        return $final;
    }

    /**
     * Maka ny montant ttc & imputation par tiers
     * @param Dossier $dossier
     * @param $exercice
     * @param string $q
     * @param string $period
     * @param string $startperiod
     * @param string $endperiod
     * @return array
     */
    public function getFactureClientsByDossier(Dossier $dossier, $exercice, $q = '', $period='all', $startperiod = '', $endperiod = '')
    {
        $categories = $this->getEntityManager()
            ->getRepository('AppBundle:Categorie')
            ->findBy(array('code' => 'CODE_CLIENT'));

        $categorie = $categories[0];

        $souscategories = $this->getEntityManager()
            ->getRepository('AppBundle:Souscategorie')
            ->findBy(array('categorie' => $categorie));

        $soussouscategories = $this->getEntityManager()
            ->getRepository('AppBundle:Soussouscategorie')
            ->createQueryBuilder('soussouscategorie')
            ->where('soussouscategorie.souscategorie IN (:souscategories)')
            ->setParameter('souscategories', array_values($souscategories))
            ->getQuery()
            ->getResult();

        $qb = $this->createQueryBuilder('imputation_controle')
            ->innerJoin('imputation_controle.image', 'image')
            ->innerJoin('image.lot', 'lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->addSelect('image')
            ->where('lot.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->andWhere('image.exercice = :exercice')
            ->setParameter('exercice', $exercice)
            ->andWhere('imputation_controle.souscategorie IN (:souscategorie) OR imputation_controle.soussouscategorie IN (:soussouscategorie)')
            ->setParameter('souscategorie', $souscategories)
            ->setParameter('soussouscategorie', $soussouscategories)
            ->addOrderBy('image.nom', 'ASC');

        if($q !== ''){
            $qb->andWhere('imputation_controle.numFacture LIKE :q')
                ->setParameter('q', $q.'%');
        }

       if($period !== 'all'){

            if($startperiod !== '' && $endperiod !== '') {
                $qb->andWhere('imputation_controle.dateFacture >= :startperiod')
                    ->andWhere('imputation_controle.dateFacture <= :endperiod');

                $dateStartArray = explode('/', $startperiod);
                $dateStartPeriode = null;
                if (count($dateStartArray) === 3) {
                    $dateStartPeriode = new \DateTime("$dateStartArray[2]-$dateStartArray[1]-$dateStartArray[0]");
                }

                $dateEndArray = explode('/', $endperiod);
                $dateEndPeriode = null;
                if (count($dateEndArray) === 3) {
                    $dateEndPeriode = new \DateTime("$dateEndArray[2]-$dateEndArray[1]-$dateEndArray[0]");
                }

                $qb->setParameter(':startperiod', $dateStartPeriode);
                $qb->setParameter(':endperiod', $dateEndPeriode);

            }
       }

       /** @var ImputationControle[] $imputations */
       $imputation = $qb
           ->getQuery()
           ->getResult();


       $imgs = array();

       /** @var ImputationControle $imp */
       foreach ($imputation as $imp){
           $imgs[] = $imp->getImage();
       }

        /** @var TvaImputationControle $tva */
        $tvas = $this->getEntityManager()
            ->getRepository('AppBundle:TvaImputationControle')
            ->getTvaImputationControleByImages($imgs, $exercice);


        $imageTtc = array();

        $lastImage = null;
        $index = -1;
        $montant = 0;

        $imageIds = array();

        $imputation = array();

        foreach ($tvas as $tva) {

            $montantHt = $tva->getMontantHt();
            $tvaTaux = $tva->getTvaTaux()->getTaux();
            $montantTva = $montantHt *  $tvaTaux / 100;
            $montantTtc = $montantHt + $montantTva;

            $tvaImage = $tva->getImage()->getId();

            if (!in_array($tvaImage, $imageIds)) {
                $imageIds[] = $tvaImage;
                $imageTtc[] = array('image' => $tva->getImage(), 'montant' => round($montantTtc, 2), 'tiers' => $tva->getTiers());
                $tempImp = $this->findBy(array('image' => $tva->getImage()));

                if(count($tempImp) > 0){
                    $imputation[] = $tempImp[0];
                }

                $montant = $montantTtc;
                $index++;
            } else {
                $montant += $montantTtc;
                $imageTtc[$index]['montant'] =  round($montant, 2);
            }
        }

        $final = array();
        $index = 0;

        foreach ($imageTtc as $ttc) {
            $final[] = array('imputation' => $imputation[$index], 'montant' => $ttc['montant'], 'tiers' => $ttc['tiers']);
            $index++;
        }

        return $final;
    }

    /**
     * @param Image $image
     * @return ImputationControle
     */
    public function getByImage(Image $image)
    {
        return $this->createQueryBuilder('ic')
            ->where('ic.image = :image')
            ->setParameter('image',$image)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }



    public function getInfoJournal($dossier, $journalDossierId, $exercice, $interval, $imageid)
    {
        $dossierE = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->find($dossier);

        $journalDossier = null;

        if (intval($journalDossierId) !== 0) {
            $journalDossier = $this->getEntityManager()
                ->getRepository('AppBundle:JournalDossier')
                ->find($journalDossierId);

            $categories = $this->getEntityManager()
                ->getRepository('AppBundle:Categorie')
                ->findBy(['journal' => $journalDossier->getJournal()]);
        } else {
            $categories = $this->getEntityManager()
                ->getRepository('AppBundle:Categorie')
                ->getCategoriesByCodes(['CODE_CLIENT', 'CODE_FRNS', 'CODE_NDF', 'CODE_CAISSE', 'CODE_FISC', 'CODE_SOC']);
        }

        $ecritures = [];

        if($imageid !== null) {


            $separation = $this->getEntityManager()
                ->getRepository('AppBundle:Separation')
                ->getSeparationByImage($this->getEntityManager()
                    ->getRepository('AppBundle:Image')
                    ->find($imageid)
                );


            if($separation !== null) {
                $categories = $this->getEntityManager()
                    ->getRepository('AppBundle:Categorie')
                    ->getCategoriesByCodes([$separation->getCategorie()->getCode()]);
            }
        }

        foreach ($categories as $categorieE) {

            $libelleDossiers = $this->getEntityManager()
                ->getRepository('AppBundle:LibelleDossier')
                ->getLibelleDossierByDossierCategorie($dossierE, $categorieE);

            $con = new CustomPdoConnection();
            $pdo = $con->connect();

            $query = "select tva.id, i.nom as image_nom, i.id as image_id, ic.date_facture as date, tva.libelle,
                        round(sum(tva.montant_ht),2) as montant_ht, round(sum(tva.montant_ttc), 2) as montant_ttc, ic.type_piece_id, 
                        p.id as pcc_id, p.compte as pcc_compte, pbilan.id as pbilan_id, pbilan.compte as pbilan_compte,
                        ptva.id as ptva_id, ptva.compte as ptva_compte, t.id as tiers_id, t.compte_str as tiers_compte, iflague.lettre,
                        d.id as devise_id, d.nom as devise_nom,
                        ic.rs,ic.num_facture,tva.date_livraison,tva.periode_deb, tva.periode_fin,mr.code as mode_reglement,ic.num_paiement,ic.chrono  
                        from imputation_controle ic 
                        inner join image i on i.id = ic.image_id
                        inner join tva_imputation_controle  tva on tva.image_id = ic.image_id
                        left join tiers t on t.id = tva.tiers_id
                        left join pcc p on p.id = tva.pcc_id
                        left join pcc ptva on ptva.id = tva.pcc_tva_id
                        left join pcc pbilan on pbilan.id = tva.pcc_bilan_id
                        inner join lot l on l.id = i.lot_id
                        inner join separation sep on sep.image_id = i.id
                        left join souscategorie sc on sc.id = sep.souscategorie_id
                        left join image_flague iflague on iflague.id = tva.image_flague_id
                        left join devise d on d.id = ic.devise_id
                        left join mode_reglement mr on mr.id = ic.mode_reglement_id";

            $params = [];

            if($imageid === null) {
                $where = " where 
                        i.supprimer = :supprimer and i.decouper = :decouper and l.dossier_id = :dossier and exercice = :exercice
                        and sep.categorie_id = :categorie and (sep.souscategorie_id is null or sc.libelle not like '%doublon%')";

                if (count($interval) != 12) {
                    $conditions = '';
                    $iteration = 0;
                    foreach ($interval as $i) {
                        $conditions .= 'ic.date_facture >= :min_' . $iteration . ' AND ic.date_facture < :max_' . $iteration . ' ';
                        if ($iteration != count($interval) - 1) $conditions .= ' OR ';

                        /** @var \DateTime $min */
                        $min = $i->min;
                        $min->setTime(0, 0, 0);
                        /** @var \DateTime $max */
                        $max = $i->max;
                        $max->setTime(23, 59, 59);

                        $params ['min_' . $iteration] = $min->format('Y-m-d');
                        $params['max_' . $iteration] = $max->format('Y-m-d');

                        $iteration++;
                    }

                    if ($conditions !== '')
                        $where .= 'AND (' . $conditions . ')';
                }

                $tmp = [
                    'exercice' => $exercice,
                    'dossier' => $dossier,
                    'categorie' => $categorieE->getId(),
                    'supprimer' => 0,
                    'decouper' => 0
                ];
            }
            else{
                $where = " where 
                        i.id = :image_id ";

                $tmp = [
                    'image_id' => $imageid
                ];
            }

            $groupBy = " group by image_id, pcc_id, pbilan_id, tiers_id order by ic.image_id";

            $query .= $where . " " . $groupBy;



            $params = array_merge($tmp, $params);

            $prep = $pdo->prepare($query);

            $prep->execute($params);

            $imputations = $prep->fetchAll();

            if($journalDossier  !== null) {
                $journal = $journalDossier->getCodeStr();
            }
            else{
                $journal = $categorieE->getJournal()->getCode();
            }

            $journalDossierEcriture = null;

            /** @var JournalDossier $journalDossierEcriture */
            $journalDossierEcriture = $this->getEntityManager()
                ->getRepository('AppBundle:JournalDossier')
                ->getJournalDossierActif($dossierE, $exercice, $journal);

            foreach ($imputations as $iKey => $imputation) {

                $tmp = [];

                $montantTtc = $imputation->montant_ttc;
                $montantHt = $imputation->montant_ht;

                $montantTva = $montantTtc-$montantHt;
                $libelle = '';

                $foundDate = false;


                if (count($libelleDossiers) === 0) {
                    $libelleModels = $this->getEntityManager()
                        ->getRepository('AppBundle:LibelleModele')
                        ->getLibelleModeleByCategorie($categorieE);

                    /** @var LibelleDossier $libelleModel */
                    foreach ($libelleModels as $libelleModel) {
                        $libelleTmp = '';

                        $champ = $libelleModel->getLibelleItem()->getChamp();
                        $nbCar = $libelleModel->getLibelleItem()->getNbCaractere();
                        $position = 0;

                        if ($champ === 'date_livraison' || $champ === 'periode_deb') {
                            if (!$foundDate) {
                                if ($champ === 'date_livraison') {
                                    $dateTmp = \DateTime::createFromFormat('Y-m-d', $imputation->$champ);
                                    if ($dateTmp !== false) {
                                        $libelleTmp = $dateTmp->format('dmY');
                                        $foundDate = true;
                                    }
                                } else if ($champ === 'periode_deb') {
                                    $dateTmp = \DateTime::createFromFormat('Y-m-d', $imputation->$champ);
                                    $dateTmpAu = \DateTime::createFromFormat('Y-m-d', $imputation->periode_fin);
                                    if ($dateTmp !== false && $dateTmpAu !== false) {
                                        $libelleTmp = $dateTmp->format('dmY') . 'AU' . $dateTmpAu->format('dmY');
                                        $foundDate = true;
                                    }
                                }
                            }
                        } else {
                            $iChamp = $imputation->$champ;
                            if ($iChamp !== null && $iChamp !== '') {
                                if ($position === 1) {
                                    $libelleTmp = substr($iChamp, -$nbCar);
                                } else {
                                    $libelleTmp = substr($iChamp, 0, $nbCar);
                                }

                            }
                        }

                        if ($libelleTmp !== '') {
                            if ($libelle === '')
                                $libelle .= $libelleTmp;
                            else
                                $libelle .= '-' . $libelleTmp;
                        }

                    }
                } else {

                    /** @var LibelleDossier $libelleDossier */
                    foreach ($libelleDossiers as $libelleDossier) {
                        $libelleTmp = '';

                        $champ = $libelleDossier->getLibelleItem()->getChamp();
                        $nbCar = $libelleDossier->getNbCaractere();
                        $position = $libelleDossier->getPosition();

                        if ($champ === 'date_livraison' || $champ === 'periode_deb') {
                            if (!$foundDate) {
                                if ($champ === 'date_livraison') {
                                    $dateTmp = \DateTime::createFromFormat('Y-m-d', $imputation->$champ);
                                    if ($dateTmp !== false) {
                                        $libelleTmp = $dateTmp->format('dmY');
                                        $foundDate = true;
                                    }
                                } else if ($champ === 'periode_deb') {
                                    $dateTmp = \DateTime::createFromFormat('Y-m-d', $imputation->$champ);
                                    $dateTmpAu = \DateTime::createFromFormat('Y-m-d', $imputation->periode_fin);
                                    if ($dateTmp !== false && $dateTmpAu !== false) {
                                        $libelleTmp = $dateTmp->format('dmY') . 'AU' . $dateTmpAu->format('dmY');
                                        $foundDate = true;
                                    }
                                }
                            }
                        } else {
                            $iChamp = $imputation->$champ;
                            if ($iChamp !== null && $iChamp !== '') {
                                if ($position === 1) {
                                    $libelleTmp = substr($iChamp, -$nbCar);
                                } else {
                                    $libelleTmp = substr($iChamp, 0, $nbCar);
                                }

                            }
                        }

                        if ($libelleTmp !== '') {
                            if ($libelle === '')
                                $libelle .= $libelleTmp;
                            else
                                $libelle .= '-' . $libelleTmp;
                        }
                    }
                }


                //type_piece_id = 2 : Facture
                if ((($categorieE->getId() === 10 || $categorieE->getId() === 11) && $imputation->type_piece_id === 2) ||
                    ($categorieE->getId() === 9 && $imputation->type_piece_id === 1)
                ) {


                    if ($imputation->tiers_id !== null) {

                        $tmp['credit'] [] = [
                            'montant' => $montantTtc,
                            'compte' => $imputation->tiers_compte,
                            'compte_id' => $imputation->tiers_id,
                            'type_compte' => 'tiers'
                        ];
                    }

                    if ($imputation->pcc_id !== null) {
                        $tmp['debit'] [] = [
                            'montant' => $montantHt,
                            'compte' => $imputation->pcc_compte,
                            'compte_id' => $imputation->pcc_id,
                            'type_compte' => 'pcc'
                        ];

                        if ($montantTva != 0) {
                            $tmp['debit'] [] = [
                                'montant' => $montantTva,
                                'compte' => $imputation->ptva_compte,
                                'compte_id' => $imputation->ptva_id,
                                'type_compte' => 'pcc'
                            ];
                        }
                    }


                    if(isset($ecritures[$imputation->image_id])){
                        $comptaTmp = $ecritures[$imputation->image_id]['compta'];

                        $debits = $tmp['debit'];
                        $credits = $tmp['credit'];


                        foreach ($debits as $debit){
                            $comptaTmp['debit'][] = $debit;
                        }

                        foreach ($credits as $credit){
                            $comptaTmp['credit'][] = $credit;
                        }

                        $tmp = $comptaTmp;

                    }

                    $ecritures[$imputation->image_id] = [
                        'libelle' => $libelle,
                        'journal' => $journal,
                        'lettre' => $imputation->lettre,
                        'devise' => $imputation->devise_nom,
                        'compta' => $tmp,
                        'image' => $imputation->image_nom,
                        'date' => $imputation->date,
                        'remarque' => '',
                        'journal_dossier' => ($journalDossierEcriture === null) ? '' : $journalDossierEcriture->getId()
                    ];


                } elseif ((($categorieE->getId() === 10 || $categorieE->getId() === 1)&& $imputation->type_piece_id === 1) ||
                    ($categorieE->getId() === 9 && $imputation->type_piece_id === 2)) {

                    if ($imputation->tiers_id !== null) {
                        $tmp['debit'] [] = [
                            'montant' => $imputation->montant_ttc,
                            'compte' => $imputation->tiers_compte,
                            'compte_id' => $imputation->tiers_id,
                            'type_compte' => 'tiers'
                        ];
                    }

                    if ($imputation->pcc_id !== null) {
                        $tmp['credit'] [] = [
                            'montant' => $imputation->montant_ht,
                            'compte' => $imputation->pcc_compte,
                            'compte_id' => $imputation->pcc_id,
                            'type_compte' => 'pcc'
                        ];

                        if ($montantTva != 0) {
                            $tmp['credit'] [] = [
                                'montant' => $montantTva,
                                'compte' => $imputation->ptva_compte,
                                'compte_id' => $imputation->ptva_id,
                                'type_compte' => 'pcc'
                            ];
                        }
                    }

                    if(isset($ecritures[$imputation->image_id])){
                        $comptaTmp = $ecritures[$imputation->image_id]['compta'];

                        $debits = $tmp['debit'];
                        $credits = $tmp['credit'];


                        foreach ($debits as $debit){
                            $comptaTmp['debit'][] = $debit;
                        }

                        foreach ($credits as $credit){
                            $comptaTmp['credit'][] = $credit;
                        }

                        $tmp = $comptaTmp;
                    }

                    $ecritures[$imputation->image_id] = [
                        'libelle' => $libelle,
                        'journal' => $journal,
                        'devise' => $imputation->devise_nom,
                        'lettre' => $imputation->lettre,
                        'compta' => $tmp,
                        'image' => $imputation->image_nom,
                        'date' => $imputation->date,
                        'remarque' =>  '',
                        'journal_dossier' => ($journalDossierEcriture === null) ? '' : $journalDossierEcriture->getId()
                    ];

                }
            }
        }
        return $ecritures;
    }
}