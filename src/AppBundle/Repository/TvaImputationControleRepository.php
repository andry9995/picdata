<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 29/08/2017
 * Time: 11:36
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Controller\DateExt;
use AppBundle\Entity\BanqueSousCategorieAutre;
use AppBundle\Entity\CleDossierExt;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageFlague;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\ReglePaiementClient;
use AppBundle\Entity\ReglePaiementDossier;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveExt;
use AppBundle\Entity\Separation;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\TvaImputationControle;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;

class TvaImputationControleRepository extends EntityRepository
{
    /**
     * @param Image $image
     * @return mixed
     */
    public function getTiersOfImage(Image $image)
    {
        $tvaImputationControle = $this->createQueryBuilder('tic')
            ->where('tic.image = :image')
            ->andWhere('tic.tiers IS NOT NULL')
            ->setParameters(['image'=>$image])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $tvaImputationControle;
    }

    /**
     * @param Releve $releve
     * @param bool $avecDistance
     * @param ReleveExt|null $releveExt
     * @return array
     */
    public function getImageAAffecter(Releve $releve, $avecDistance = false, ReleveExt $releveExt = null)
    {
        $exercice = $releve->getImage()->getExercice();
        $exercices = '' . ($exercice - 1);
        $exercices .= ','. $exercice;
        $exercices .= ',' . ($exercice + 1);

        if ($releveExt)
            $idsNonLettrables = ($releveExt->getNonLettrable() != '') ? json_decode($releveExt->getNonLettrable()) : [0];
        else
            $idsNonLettrables = ($releve->getNonLettrable() != '') ? json_decode($releve->getNonLettrable()) : [0];

        if (count($idsNonLettrables) == 0) $idsNonLettrables = [0];
        $nonLettrables = '';

        for ($i = 0; $i < count($idsNonLettrables); $i++)
        {
            $nonLettrables .= $idsNonLettrables[$i];
            if ($i != count($idsNonLettrables) - 1) $nonLettrables .= ',';
        }

        $query = '
            SELECT DISTINCT tic.image_id
            FROM tva_imputation_controle tic
            JOIN image i ON (i.id = tic.image_id)
            JOIN lot l on (l.id = i.lot_id)
            JOIN separation sep on (sep.image_id = i.id)
            LEFT JOIN souscategorie sc ON (sc.id = sep.souscategorie_id)
            JOIN imputation_controle ic on (tic.image_id = ic.image_id) 
            WHERE i.exercice in ('.$exercices.') 
            AND l.dossier_id = :DOSSIER_ID AND tic.image_flague_id IS NULL AND i.id NOT IN ('.$nonLettrables.')  
                AND (sc.libelle_new <> :lDoublon OR sep.souscategorie_id IS NULL) 
            GROUP BY tic.image_id, sep.categorie_id, ic.type_piece_id 
            HAVING 
            (
                ROUND(sum(tic.montant_ttc),2) = ROUND(:montant,2) and ((sep.categorie_id in (10,12) and ic.type_piece_id <> 1) OR (sep.categorie_id in (9,13) and ic.type_piece_id = 1)) OR 
                ROUND(sum(tic.montant_ttc),2) = -ROUND(:montant_,2) and not((sep.categorie_id in (10,12) and ic.type_piece_id <> 1) OR (sep.categorie_id in (9,13) and ic.type_piece_id = 1))
            )   
        ';

        $params = [
            'DOSSIER_ID' => $releve->getBanqueCompte()->getDossier()->getId(),
            'montant' => $releveExt ? -$releveExt->getMontant() : $releve->getDebit() - $releve->getCredit(),
            'montant_' => $releveExt ? -$releveExt->getMontant() : $releve->getDebit() - $releve->getCredit(),
            'lDoublon' => 'DOUBLON'
        ];

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $prep = $pdo->prepare($query);
        $prep->execute($params);
        $res = $prep->fetchAll();

        /** @var Image[] $images */
        $images = [];
        foreach ($res as $re)
        {
            $images[] = $this->getEntityManager()->getRepository('AppBundle:Image')
                ->find($re->image_id);
        }

        $req = 'tic as tvaic,
                ROUND(SUM(tic.montantHt + tic.montantHt * tva.taux / 100),2) as mttc,
                ROUND(SUM(tic.montantHt * tva.taux / 100),2) as mtva ';

        if ($avecDistance)
        {
            $libSel = trim($releve->getLibelle());
            $req .= ", SIMILARITY(trs.intitule,'$libSel') as sim";
        }

        $tvaImputationControls = $this->createQueryBuilder('tic')
            ->leftJoin('tic.tvaTaux','tva')
            ->leftJoin('tic.image','i')
            ->select($req)
            ->where('tic.image IN (:images)')
            ->setParameter('images',$images);

        $tvaImputationControls = $tvaImputationControls
            ->groupBy('i.id')
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($tvaImputationControls as $item)
        {
            /** @var TvaImputationControle $tvaImputationControl */
            $tvaImputationControl = $item['tvaic'];
            $mTTc = abs($item['mttc']);
            $mTva = abs($item['mtva']);

            /** @var ImputationControle $imputationControle */
            $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                ->createQueryBuilder('ic')
                ->where('ic.image = :image')
                ->setParameter('image',$tvaImputationControl->getImage())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            /** @var Separation $separation */
            $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                ->createQueryBuilder('sep')
                ->where('sep.image = :image')
                ->setParameter('image',$tvaImputationControl->getImage())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($separation &&
                (in_array($separation->getCategorie()->getId(),[10,12]) && $imputationControle->getTypePiece()->getId() != 1 ||
                in_array($separation->getCategorie()->getId(),[9,13]) && $imputationControle->getTypePiece()->getId() == 1
                ))
            {
                $mTTc *= -1;
                $mTva += -1;
            }

            $result = [
                'tvaic' => $tvaImputationControl,
                'mttc' => $mTTc,
                'mtva' => $mTva,
                'sim' => 25,
                't' => 0
            ];
            $results[] = $result;
        }

        return $results;
    }

    /**
     * @param ImageFlague $imageFlague
     * @param TvaImputationControle|null $tvaImputationControle
     * @param bool $groupedByImage
     * @return array
     */
    public function getChildImageFlagues(ImageFlague $imageFlague, TvaImputationControle $tvaImputationControle = null, $groupedByImage = false)
    {
        /** @var TvaImputationControle[] $tvaImputationControles */
        $tvaImputationControles = $this->createQueryBuilder('tic')
            ->leftJoin('tic.image','i')
            ->where('tic.imageFlague = :imageFlague')
            ->andWhere('tic.id <> :id')
            ->andWhere('i.supprimer = 0')
            ->setParameters([
                'imageFlague' => $imageFlague,
                'id' => $tvaImputationControle ? $tvaImputationControle->getId() : -1
            ])
            ->orderBy('i.id')
            ->addOrderBy('tic.montantTtc','DESC')
            ->getQuery()
            ->getResult();

        if (!$groupedByImage) return $tvaImputationControles;

        $res = [];
        foreach ($tvaImputationControles as $tvaImputationControle)
        {
            $key = $tvaImputationControle->getImage()->getId();
            if (!array_key_exists($key, $res))
                $res[$key] = [];

            $res[$key][] = $tvaImputationControle;
        }

        return $res;
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @return array
     */
    public function getImageAAffecterAll(Dossier $dossier,$exercice)
    {
        $results = $this->createQueryBuilder('tic')
            ->leftJoin('tic.image','i')
            ->leftJoin('i.lot','l')
            ->leftJoin('tic.tvaTaux','tva')
            ->where('l.dossier = :dossier')
            ->andWhere('i.exercice in (:exercices)')
            ->setParameters(array(
                    'dossier' => $dossier,
                    'exercices' => array($exercice,$exercice - 1))
            )
            ->setMaxResults(50)
            ->setFirstResult(0)
            ->orderBy('i.exercice','DESC')
            ->addOrderBy('i.nom')
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * @param Image $image
     * @return TvaImputationControle[]
     */
    public function getNotFlague(Image $image)
    {
        return $this->createQueryBuilder('i')
            ->where('i.image = :image')
            ->andWhere('i.imageFlague IS NULL')
            ->setParameter('image',$image)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Tiers $tiers
     * @param $exercice
     * @return array
     */
    public function getImageByTier(Tiers $tiers,$exercice = 0)
    {
        if ($exercice == 0)
        {
            $date_now = new \DateTime();
            $exercice = intval($date_now->format('Y'));
        }
        $exercices = [];
        $exercices[] = $exercice - 1;
        $exercices[] = $exercice;
        $exercices[] = $exercice + 1;

        return $this->createQueryBuilder('ti')
            ->leftJoin('ti.image','i')
            ->leftJoin('ti.tiers','t')
            ->leftJoin('t.dossier','d')
            ->where('ti.tiers IS NOT NULL')
            ->andWhere('ti.tiers = :tiers')
            ->setParameter('tiers',$tiers)
            ->andWhere('i.exercice IN (:exercices)')
            ->setParameter('exercices',$exercices)
            ->andWhere('ti.imageFlague IS NULL')
            ->orderBy('d.nom')
            ->addOrderBy('i.nom')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $images
     * @param int $exercice
     * @param bool $nonFlaguer
     * @return TvaImputationControle[]
     */
    public function getTvaImputationControleByImages(array $images, $exercice = 0, $nonFlaguer = false)
    {
        $tvaImputationControles = $this->createQueryBuilder('ti')
            ->innerJoin('ti.image', 'image')
            ->innerJoin('image.lot', 'lot')
            ->innerJoin('lot.dossier', 'dossier')
            ->where('ti.image IN (:images)')
            ->setParameter('images', array_values($images));

        if ($exercice != 0)
            $tvaImputationControles = $tvaImputationControles
                ->andWhere('image.exercice = :exercice')
                ->setParameter('exercice', $exercice);

        if ($nonFlaguer)
            $tvaImputationControles = $tvaImputationControles
                ->andWhere('ti.imageFlague IS NULL');

        return $tvaImputationControles
            ->orderBy('dossier.nom')
            ->addOrderBy('image.nom')
            ->select('ti')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param $montant
     * @param $exercice
     * @param Tiers $tiers
     * @return array
     */
    public function getTvaImputationControleByMontant(Dossier $dossier, $montant, $exercice, Tiers $tiers = null)
    {
        $req = 'tic as tvaic,
                ROUND(SUM(tic.montantHt + tic.montantHt * tva.taux / 100),2) as mttc,
                ROUND(SUM(tic.montantHt * tva.taux / 100),2) as mtva ';

        $tvaImputationControls = $this->createQueryBuilder('tic')
            ->leftJoin('tic.image','i')
            ->leftJoin('i.lot','l')
            ->leftJoin('tic.tvaTaux','tva')
            ->leftJoin('AppBundle:ImputationControle', 'ic', 'WITH', 'tic.image = ic.image')
            ->select($req)
            ->where('i.exercice IN (:exercices)')
            ->andWhere('l.dossier = :dossier')
            ->andWhere('tic.imageFlague IS NULL')
            ->having('ABS(mttc) = :mttc')
            ->setParameter('dossier',$dossier)
            ->setParameter('exercices',array($exercice,$exercice - 1,$exercice + 1))
            ->setParameter('mttc',abs($montant));

        if ($tiers)
            $tvaImputationControls = $tvaImputationControls
                ->andWhere('tic.tiers = :tiers')
                ->setParameter('tiers',$tiers);

        return $tvaImputationControls
            ->orderBy('i.exercice','DESC')
            ->groupBy('i.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $image
     * @return array
     */
    public function getGroupedTvaImputationControleByImage($image)
    {

        return $this->createQueryBuilder('ti')
            ->where('ti.image = :image')
            ->setParameter('image', $image)
            ->groupBy('ti.pcc')
            ->addGroupBy('ti.tiers')
            ->addGroupBy('ti.pccTva')
            ->select('ti')
            ->addSelect('SUM(ti.montantTtc) AS ttc')
            ->addSelect('SUM(ti.montantHt) AS ht')
            ->addSelect('SUM(ROUND(ti.montantTtc - ti.montantHt, 2)) AS tva')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @return array
     */
    public function getExports(Dossier $dossier, $exercice)
    {
        $results = [];
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $req = '
            SELECT 
                i.id, i.nom, 
                DATE_FORMAT(ic.date_facture,\'%d/%m/%Y\') AS niceDate, tic.libelle, ABS(tic.montant_ht) AS montant_ht, 
                tic.tiers_id, tic.pcc_bilan_id, 
                tic.pcc_id, tic.pcc_tva_id, 
                tt.taux, ic.type_piece_id, 
                sc.libelle_new, sep.categorie_id 
            FROM tva_imputation_controle tic
            JOIN image i ON (i.id = tic.image_id)
            JOIN lot l ON (l.id = i.lot_id)
            JOIN imputation_controle ic ON (ic.image_id = tic.image_id)
            JOIN tva_taux tt ON (tt.id = tic.tva_taux_id)
            JOIN separation sep ON (sep.image_id = i.id)
            JOIN souscategorie sc On (sc.id = sep.souscategorie_id)
            WHERE l.dossier_id = :DOSSIER_ID AND i.exercice = :EXERCICE AND sc.libelle_new NOT LIKE :DOUBLON_LIKE         
        ';
        $prep = $pdo->prepare($req);
        $prep->execute([
            'DOSSIER_ID' => $dossier->getId(),
            'EXERCICE' => $exercice,
            'DOUBLON_LIKE' => 'DOUBLON%'
        ]);
        $res = $prep->fetchAll();
        foreach ($res as $index => $re)
        {
            $montantHt = $re->montant_ht;
            $montantTva = 0;
            if ($re->pcc_tva_id && $re->taux != 0)
            {
                $montantTva = $montantHt * $re->taux / 100;
            }
            $montantTtc = $montantHt + $montantTva;
            $isDebit = true;
            if (
                in_array(intval($re->categorie_id),[10,12]) && $re->type_piece_id != 1 ||
                in_array(intval($re->categorie_id),[9,13]) && $re->type_piece_id == 1
            ){ $isDebit = false; }

            if ($re->tiers_id || $re->pcc_bilan_id)
            {
                $key = $re->id . '_';
                if ($re->tiers_id) $key .= $re->tiers_id . '_1';
                else $key .= $re->pcc_bilan_id . '_0';
                $debit = $isDebit ? $montantTtc : 0;
                $credit = $isDebit ? 0 : $montantTtc;

                if (array_key_exists($key,$results))
                {
                    $results[$key]->db += $debit;
                    $results[$key]->cr += $credit;
                }
                else
                {
                    $results[$key] = (object)
                    [
                        'id' => $key,
                        'dt' => $re->niceDate,
                        'db' => $debit,
                        'cr' => $credit,
                        'pi' => (object)
                        [
                            'id' => Boost::boost($re->id),
                            'nom' => $re->nom
                        ],
                        'lb' => $re->libelle,
                        'gr' => $re->id
                    ];
                }
            }
            if ($re->pcc_tva_id && $re->taux != 0)
            {
                $key = $re->id . '_' . $re->pcc_tva_id.'_0';
                $debit = $isDebit ? 0 : $montantTva;
                $credit = $isDebit ? $montantTva : 0;

                if (array_key_exists($key,$results))
                {
                    $results[$key]->db += $debit;
                    $results[$key]->cr += $credit;
                }
                else
                {
                    $results[$key] = (object)
                    [
                        'id' => $key,
                        'dt' => $re->niceDate,
                        'db' => $debit,
                        'cr' => $credit,
                        'pi' => (object)
                        [
                            'id' => Boost::boost($re->id),
                            'nom' => $re->nom
                        ],
                        'lb' => $re->libelle,
                        'gr' => $re->id
                    ];
                }
            }
            if ($re->pcc_id)
            {
                $key = $re->id . '_' . $re->pcc_id.'_0';
                $debit = $isDebit ? 0 : $montantHt;
                $credit = $isDebit ? $montantHt : 0;

                if (array_key_exists($key,$results))
                {
                    $results[$key]->db += $debit;
                    $results[$key]->cr += $credit;
                }
                else
                {
                    $results[$key] = (object)
                    [
                        'id' => $key,
                        'dt' => $re->niceDate,
                        'db' => $debit,
                        'cr' => $credit,
                        'pi' => (object)
                        [
                            'id' => Boost::boost($re->id),
                            'nom' => $re->nom
                        ],
                        'lb' => $re->libelle,
                        'gr' => $re->id
                    ];
                }
            }
        }

        return array_values($results);
    }

    /**
     * @param Pcc|null $pcc
     * @param Tiers|null $tiers
     * @param int $exercice
     * @param BanqueSousCategorieAutre $banqueSousCategorieAutre
     * @return array
     */
    public function getTvaImputationControleByCompte(Pcc $pcc = null, Tiers $tiers = null, $exercice = 0, BanqueSousCategorieAutre $banqueSousCategorieAutre = null)
    {
        $req = '
            SELECT ROUND(SUM(ABS(tic.montant_ttc)),2) AS m, tic.image_id, sep.categorie_id, ic.type_piece_id  
            FROM tva_imputation_controle tic
            JOIN image i ON (i.id = tic.image_id)
            JOIN imputation_controle ic ON (ic.image_id = tic.image_id)
            JOIN separation sep ON (sep.image_id = tic.image_id)
            WHERE tic.image_flague_id IS NULL AND 
              ABS(ROUND(tic.montant_ttc,2)) <= :MONTANT AND 
        ';
        $params['MONTANT'] = abs($banqueSousCategorieAutre->getMontant());
        if ($exercice != 0)
        {
            $req .= 'i.exercice = :EXERCICE AND ';
            $params['EXERCICE'] = $exercice;
        }

        if ($tiers)
        {
            $req .= 'tic.tiers_id = :TIERS ';
            $params['TIERS'] = $tiers->getId();
        }
        elseif ($pcc)
        {
            $req .= '(tic.pcc_id = :PCC OR tic.pcc_bilan_id = :PCC1 OR tic.pcc_tva_id = :PCC2) ';
            $params['PCC'] = $pcc->getId();
            $params['PCC1'] = $pcc->getId();
            $params['PCC2'] = $pcc->getId();
        }

        $req .= 'GROUP BY tic.image_id';

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $prep = $pdo->prepare($req);
        $prep->execute($params);
        return $prep->fetchAll();
    }

    /**
     * @param Dossier $dossier
     * @param $exercice
     * @param $type
     * @param array $inteval
     * @param int $index
     * @param int $dateType
     * @return array
     */
    public function getNonLettre(Dossier $dossier, $exercice, $type, $inteval = [90,100000],$index = 0,$dateType = 0)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        if ($dateType == 0)
        {
            $req = '
                SELECT 
                      ROUND(SUM(tic.montant_ttc),2) AS tic_m_ttc,  
                      ic.date_facture AS ic_date_facture,   
                      ic.date_echeance AS ic_date_echeance, 
                      i.id AS i_id,i.nom AS i_nom, tic.pcc_id,  
                      ic.rs AS ic_rs, 
                      tic.tiers_id AS tic_tiers_id,  
                      tic.pcc_bilan_id AS tic_pcc_bilan_id,  
                      tic.pcc_tva_id AS tic_pcc_tva_id, 
                      tic.pcc_id AS tic_pcc_id, 
                      sc.libelle_new  
                FROM tva_imputation_controle tic 
                LEFT JOIN imputation_controle ic ON (tic.image_id = ic.image_id) 
                LEFT JOIN separation sep ON (sep.image_id = tic.image_id) 
                LEFT JOIN souscategorie sc ON (sc.id = sep.souscategorie_id) 
                JOIN image i ON (i.id = tic.image_id) 
                JOIN lot l ON (l.id = i.lot_id) 
                WHERE l.dossier_id = :DOSSIER_ID  
                AND tic.image_flague_id IS NULL 
                AND ic.date_echeance IS NOT NULL 
                AND DATEDIFF(:NOW_1, ic.date_echeance) > :MIN_INTERVAL   
                AND DATEDIFF(:NOW_2, ic.date_echeance) <= :MAX_INTERVAL   
                AND i.exercice = :EXERCICE 
                AND i.supprimer <> 1 
                AND sep.categorie_id IN ('.(($type == 5) ? '10,12' : '9,13').') 
                AND sc.libelle_new <> :DOUBLON 
                GROUP BY i.id       
            ';

            $reqDateFacture = '
                SELECT 
                      ROUND(SUM(tic.montant_ttc),2) AS tic_m_ttc,  
                      ic.date_facture AS ic_date_facture,   
                      ic.date_livraison AS ic_date_livraison, 
                      ic.date_echeance AS ic_date_echeance, 
                      i.id AS i_id,i.nom AS i_nom, tic.pcc_id,  
                      ic.rs AS ic_rs, 
                      tic.tiers_id AS tic_tiers_id,  
                      tic.pcc_bilan_id AS tic_pcc_bilan_id,  
                      tic.pcc_tva_id AS tic_pcc_tva_id, 
                      tic.pcc_id AS tic_pcc_id, 
                      sc.libelle_new  
                FROM tva_imputation_controle tic 
                LEFT JOIN imputation_controle ic ON (tic.image_id = ic.image_id) 
                LEFT JOIN separation sep ON (sep.image_id = tic.image_id) 
                LEFT JOIN souscategorie sc ON (sc.id = sep.souscategorie_id) 
                JOIN image i ON (i.id = tic.image_id) 
                JOIN lot l ON (l.id = i.lot_id) 
                WHERE l.dossier_id = :DOSSIER_ID  
                AND tic.image_flague_id IS NULL 
                AND ic.date_echeance IS NULL 
                AND (ic.date_facture IS NOT NULL OR ic.date_livraison IS NOT NULL)  
                AND i.exercice = :EXERCICE 
                AND i.supprimer <> 1 
                AND sep.categorie_id IN ('.(($type == 5) ? '10,12' : '9,13').') 
                AND sc.libelle_new <> :DOUBLON 
                GROUP BY i.id       
            ';

            /** @var ReglePaiementDossier $reglePaiementDossier */
            $reglePaiementDossier = $this->getEntityManager()->getRepository('AppBundle:ReglePaiementDossier')
                ->getForDossier($dossier,($type == 5) ? 0 : 1 );
            /** @var ReglePaiementClient $reglePaiementClient */
            $reglePaiementClient = null;

            if (!$reglePaiementDossier)
                $reglePaiementClient = $this->getEntityManager()->getRepository('AppBundle:ReglePaiementClient')
                    ->getForClient($dossier->getSite()->getClient(),($type == 5) ? 0 : 1 );

            $paramsDateFacture = [
                'DOSSIER_ID' => $dossier->getId(),
                'EXERCICE' => $exercice,
                'DOUBLON' => 'DOUBLON'
            ];

            $prepDateFacr = $pdo->prepare($reqDateFacture);
            $prepDateFacr->execute($paramsDateFacture);

            foreach ($prepDateFacr->fetchAll() as $item)
            {
                $res = $this->getResultPM($item,$index,$reglePaiementClient,$reglePaiementDossier,$inteval);
                if ($res)
                    $results[] = $res;
            }
        }
        else
        {
            $req = '
                SELECT 
                      ROUND(SUM(tic.montant_ttc),2) AS tic_m_ttc,  
                      ic.date_facture AS ic_date_facture,   
                      ic.date_echeance AS ic_date_echeance, 
                      i.id AS i_id,i.nom AS i_nom, tic.pcc_id,  
                      ic.rs AS ic_rs, 
                      tic.tiers_id AS tic_tiers_id,  
                      tic.pcc_bilan_id AS tic_pcc_bilan_id,  
                      tic.pcc_tva_id AS tic_pcc_tva_id, 
                      tic.pcc_id AS tic_pcc_id, 
                      sc.libelle_new  
                FROM tva_imputation_controle tic
                LEFT JOIN imputation_controle ic ON (tic.image_id = ic.image_id) 
                LEFT JOIN separation sep ON (sep.image_id = tic.image_id) 
                LEFT JOIN souscategorie sc ON (sc.id = sep.souscategorie_id) 
                JOIN image i ON (i.id = tic.image_id) 
                JOIN lot l ON (l.id = i.lot_id) 
                WHERE l.dossier_id = :DOSSIER_ID  
                AND tic.image_flague_id IS NULL 
                AND ic.date_facture IS NOT NULL 
                AND DATEDIFF(:NOW_1, ic.date_facture) > :MIN_INTERVAL   
                AND DATEDIFF(:NOW_2, ic.date_facture) <= :MAX_INTERVAL   
                AND i.exercice = :EXERCICE 
                AND i.supprimer <> 1 
                AND sep.categorie_id IN ('.(($type == 5) ? '10,12' : '9,13').') 
                AND sc.libelle_new <> :DOUBLON 
                GROUP BY i.id       
            ';
        }

        $params = [
            'DOSSIER_ID' => $dossier->getId(),
            'MIN_INTERVAL' => $inteval[0],
            'MAX_INTERVAL' => $inteval[1],
            'EXERCICE' => $exercice,
            'DOUBLON' => 'DOUBLON',
            'NOW_1' => (new \DateTime())->format('Y-m-d'),
            'NOW_2' => (new \DateTime())->format('Y-m-d')
        ];

        $prep = $pdo->prepare($req);
        $prep->execute($params);

        $results = [];
        foreach ($prep->fetchAll() as $item)
        {
            $results[] = $this->getResultPM($item,$index);
        }

        return $results;
    }

    /**
     * @param $item
     * @param int $index
     * @param ReglePaiementClient|null $reglePaiementClient
     * @param ReglePaiementDossier|null $reglePaiementDossier
     * @param array $inteval
     * @return object
     */
    private function getResultPM($item,$index = 0,ReglePaiementClient $reglePaiementClient = null,ReglePaiementDossier $reglePaiementDossier = null,$inteval = [])
    {
        $bilan = null;
        $tva = null;
        $resultat = null;

        if ($item->tic_tiers_id || $item->tic_pcc_bilan_id)
        {
            if ($item->tic_tiers_id)
            {
                $tiers = $this->getEntityManager()->getRepository('AppBundle:Tiers')
                    ->find($item->tic_tiers_id);

                $bilan = (object)
                [
                    'id' => Boost::boost($tiers->getId()),
                    'l' => $tiers->getCompteStr(),
                    't' => 1
                ];
            }
            else
            {
                $pcc = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                    ->find($item->tic_pcc_bilan_id);

                $bilan = (object)
                [
                    'id' => Boost::boost($pcc->getId()),
                    'l' => $pcc->getCompte(),
                    't' => 0
                ];
            }
        }
        if ($item->tic_pcc_tva_id)
        {
            $pcc = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                ->find($item->tic_pcc_tva_id);

            $tva = (object)
            [
                'id' => Boost::boost($pcc->getId()),
                'l' => $pcc->getCompte(),
                't' => 0
            ];
        }
        if ($item->tic_pcc_id)
        {
            $pcc = $this->getEntityManager()->getRepository('AppBundle:Pcc')
                ->find($item->tic_pcc_id);

            $resultat = (object)
            [
                'id' => Boost::boost($pcc->getId()),
                'l' => $pcc->getCompte(),
                't' => 0
            ];
        }

        $image = $this->getEntityManager()->getRepository('AppBundle:Image')
            ->find($item->i_id);
        $imageComment = $this->getEntityManager()->getRepository('AppBundle:ImageComment')
            ->getByImage($image);

        /** @var \DateTime $dateEcheance */
        $dateEcheance = null;

        if ($item->ic_date_echeance && trim($item->ic_date_echeance) != '')
            $dateEcheance = \DateTime::createFromFormat('Y-m-d',$item->ic_date_echeance);

        if (!$dateEcheance)
        {
            // type_date => 0:Date facture; 1:Date livraison
            if ($reglePaiementDossier)
            {
                if ($reglePaiementDossier->getTypeDate() == 0 && $item->ic_date_facture)
                    $dateEcheance = $this->getDateEcheance($reglePaiementClient,$reglePaiementDossier,\DateTime::createFromFormat('Y-m-d',$item->ic_date_facture));
                if ($reglePaiementDossier->getTypeDate() == 1 && $item->ic_date_livraison)
                    $dateEcheance = $this->getDateEcheance($reglePaiementClient,$reglePaiementDossier,\DateTime::createFromFormat('Y-m-d',$item->ic_date_livraison));
            }
            elseif ($reglePaiementClient)
            {
                if ($reglePaiementClient->getTypeDate() == 0 && $item->ic_date_facture)
                    $dateEcheance = $this->getDateEcheance($reglePaiementClient,$reglePaiementDossier,\DateTime::createFromFormat('Y-m-d',$item->ic_date_facture));
                if ($reglePaiementClient->getTypeDate() == 1 && $item->ic_date_livraison)
                    $dateEcheance = $this->getDateEcheance($reglePaiementClient,$reglePaiementDossier,\DateTime::createFromFormat('Y-m-d',$item->ic_date_livraison));
            }
        }

        if (!$dateEcheance)
        {
            if ($item->ic_date_facture)
                $dateEcheance = \DateTime::createFromFormat('Y-m-d',$item->ic_date_facture);
            elseif ($item->ic_date_livraison)
                $dateEcheance = \DateTime::createFromFormat('Y-m-d',$item->ic_date_livraison);
            else
                $dateEcheance = new \DateTime();

            $dateEcheance->add(new \DateInterval('P45D'));

            $dateEcheance = DateExt::getNextOuvrable($dateEcheance);
        }

        if (count($inteval) > 0)
        {
            $dateInterval = date_diff(new \DateTime(), $dateEcheance);
            $diff = intval($dateInterval->format('%a'));

            $index = -1;
            for ($i = 0; $i < count($inteval); $i++)
            {
                if ($i == (count($inteval) - 1) && $diff > $inteval[$i])
                    $index = $i;
                elseif ($i != (count($inteval) - 1) && $diff > $inteval[$i] && $diff <= $inteval[$i + 1])
                    $index = $i;

                if ($index != -1) break;
            }
        }

        if ($index == -1) return null;

        return (object)
        [
            'id' => Boost::boost($item->i_id),
            'i' => (object)
            [
                'id' => Boost::boost($item->i_id),
                'n' => $item->i_nom,
            ],
            'd' => $item->ic_date_facture,
            'de' => $dateEcheance->format('Y-m-d'),
            'l' => $item->ic_rs,
            'b' => $bilan,
            'tva' => $tva,
            'r' => $resultat,
            'm_'.$index => $item->tic_m_ttc,
            'st' => $imageComment ? $imageComment->getStatus() : 0,
            'cm' => $imageComment ? $imageComment->getCommentaire() : ''
        ];
    }

    /**
     * @param ReglePaiementClient|null $reglePaiementClient
     * @param ReglePaiementDossier|null $reglePaiementDossier
     * @param \DateTime $date
     * @return \DateTime|null
     */
    private function getDateEcheance(ReglePaiementClient $reglePaiementClient = null, ReglePaiementDossier $reglePaiementDossier = null,\DateTime $date)
    {
        // nbre_jour : par default 45
        // date_le : jour precise apres la date calcule
        /** @var \DateTime $dateEcheance */
        $dateEcheance = clone $date;
        $jourAdd = $reglePaiementDossier ? $reglePaiementDossier->getNbreJour() : $reglePaiementClient->getNbreJour();
        $dateLe = null;

        if ($reglePaiementDossier) $dateLe = $reglePaiementDossier->getDateLe();
        if ($reglePaiementClient && !$dateLe) $dateLe = $reglePaiementClient->getDateLe();

        $dateEcheance->add(new \DateInterval('P'.$jourAdd.'D'));
        if ($dateLe)
        {
            if (intval($dateLe) < 10) $dateLe = '0' . $dateLe;
            $datePrecise = \DateTime::createFromFormat('Ymd',
                $dateEcheance->format('Y').
                $dateEcheance->format('m').
                $dateLe
            );

            if ($datePrecise->format('Ymd') >= $dateEcheance->format('Ymd'))
                $dateEcheance = clone $datePrecise;
            else
            {
                $annee = intval($datePrecise->format('Y'));
                $mois = intval($datePrecise->format('m')) + 1;

                if ($mois == 13)
                {
                    $mois = 1;
                    $annee++;
                }
                if ($mois < 10) $mois = '0'.$mois;

                $dateEcheance = \DateTime::createFromFormat('Ymd',$annee.$mois.$dateLe);
            }
        }

        return $dateEcheance;
    }

    /**
     * @param Dossier $dossier
     * @param string $nomImage
     * @param int $montant
     * @param bool $avecLettre
     * @return TvaImputationControle[]
     */
    public function searchByPieceMontant(Dossier $dossier, $nomImage = '' , $montant = 0, $avecLettre = false)
    {
        $tics = $this->createQueryBuilder('tic')
            ->select('tic as ti_c,ROUND(SUM(tic.montantTtc),2) as m')
            ->leftJoin('tic.image', 'i')
            ->leftJoin('i.lot','l')
            ->where('l.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->groupBy('i.id');

        if (!$avecLettre)
            $tics = $tics
                ->andWhere('tic.imageFlague IS NULL');

        if ($montant != 0)
            $tics = $tics
                ->having('m = :montant')
                ->setParameter('montant',$montant);
        if ($nomImage != '')
            $tics = $tics
                ->andWhere('i.nom LIKE :nomImage')
                ->setParameter('nomImage','%'.$nomImage.'%');

        $tics =  $tics
            ->getQuery()
            ->getResult();

        /** @var TvaImputationControle[] $tvaImputationControles */
        $tvaImputationControles = [];

        foreach ($tics as $tic)
        {
            $tvaImputationControles = array_merge($tvaImputationControles,$this
                ->createQueryBuilder('tic')
                ->where('tic.image = :image')
                ->setParameter('image',$tic['ti_c']->getImage())
                ->andWhere('tic.imageFlague IS NULL')
                ->getQuery()
                ->getResult());
        }

        return $tvaImputationControles;
    }

    /**
     * @param ImageFlague $imageFlague
     * @return int
     */
    public function getMontantImageFlague(ImageFlague $imageFlague)
    {
        $req = '
            SELECT SUM(tic.montant_ttc) AS s 
            FROM tva_imputation_controle tic
            JOIN image i ON (i.id = tic.image_id)
            WHERE tic.image_flague_id = :image_flague_id AND i.supprimer = 0';

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $params = [
            'image_flague_id' => $imageFlague->getId()
        ];

        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $prepRes = $prep->fetch();

        return $prepRes->s;
    }

    /**
     * @param ImageFlague $imageFlague
     * @return object
     */
    public function getComptesByImageFlague(ImageFlague $imageFlague)
    {
        /** @var TvaImputationControle[] $tvaImputationControles */
        $tvaImputationControles = $this->getChildImageFlagues($imageFlague);

        $tiers = [];
        $images = [];

        $bilans = [];
        $tvas = [];
        $resultats = [];

        foreach ($tvaImputationControles as $tvaImputationControle)
        {
            $bilanPcc = $tvaImputationControle->getPccBilan();
            $bilanTiers = $tvaImputationControle->getTiers();
            $tva = $tvaImputationControle->getPccTva();
            $resultat = $tvaImputationControle->getPcc();

            $keyImage = $tvaImputationControle->getImage()->getId();
            if (!array_key_exists($keyImage, $images)) $images[$keyImage] = $tvaImputationControle->getImage();

            if ($bilanTiers)
            {
                $key = '1-'.$bilanTiers->getId();
                if (!array_key_exists($key, $bilans))
                {
                    $bilans[$key] = (object)
                    [
                        'id' => Boost::boost($bilanTiers->getId()),
                        'l' => $bilanTiers->getCompteStr(),
                        'i' => $bilanTiers->getIntitule(),
                        't' => 1
                    ];

                    $tiers[$key] = $bilanTiers;
                }
            }
            elseif ($bilanPcc)
            {
                $key = '0-'.$bilanPcc->getId();
                if (!array_key_exists($key, $bilans))
                    $bilans[$key] = (object)
                    [
                        'id' => Boost::boost($bilanPcc->getId()),
                        'l' => $bilanPcc->getCompte(),
                        'i' => $bilanPcc->getIntitule(),
                        't' => 0
                    ];
            }

            if ($tva)
            {
                $key = '0-'.$tva->getId();
                if (!array_key_exists($key, $tvas))
                    $tvas[$key] = (object)
                    [
                        'id' => Boost::boost($tva->getId()),
                        'l' => $tva->getCompte(),
                        'i' => $tva->getIntitule(),
                        't' => 0
                    ];
            }
            elseif ($resultat)
            {
                $key = '0-'.$resultat->getId();
                if (!array_key_exists($key, $resultats))
                    $resultats[$key] = (object)
                    [
                        'id' => Boost::boost($resultat->getId()),
                        'l' => $resultat->getCompte(),
                        'i' => $resultat->getIntitule(),
                        't' => 0
                    ];
            }
        }

        return (object)
        [
            'tiers' => array_values($tiers),
            'images' => array_values($images),
            'b' => array_values($bilans),
            't' => array_values($tvas),
            'c' => array_values($resultats)
        ];
    }

    public function getTvaImputationControleByImagesIds($imagesIds)
    {

    }
}