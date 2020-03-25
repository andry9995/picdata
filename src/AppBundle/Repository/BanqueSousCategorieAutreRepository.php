<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 11/04/2019
 * Time: 08:00
 */

namespace AppBundle\Repository;


use AppBundle\Controller\Boost;
use AppBundle\Entity\BanqueSousCategorieAutre;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageFlague;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveExt;
use AppBundle\Entity\Souscategorie;
use AppBundle\Entity\TvaImputationControle;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;

class BanqueSousCategorieAutreRepository extends EntityRepository
{
    /**
     * @param Releve $releve
     * @return null|object
     */
    public function checkIfHasMontant(Releve $releve)
    {
        $exercice = $releve->getImage()->getExercice();
        $exercices = '' . ($exercice - 1);
        $exercices .= ','. $exercice;
        $exercices .= ',' . ($exercice + 1);

        $idsNonLettrables = $this->getEntityManager()->getRepository('AppBundle:Releve')
            ->ImagesNonLettrables($releve,false);
        $nonLettrables = '';

        for ($i = 0; $i < count($idsNonLettrables); $i++)
        {
            $nonLettrables .= $idsNonLettrables[$i];
            if ($i != count($idsNonLettrables) - 1) $nonLettrables .= ',';
        }

        $req = '
            SELECT COUNT(bsca.image_id) AS isa, sc.id AS sc_id, ssc.id AS ssc_id 
            FROM banque_sous_categorie_autre bsca
            JOIN image i ON (i.id = bsca.image_id)
            JOIN lot l on (l.id = i.lot_id)
            JOIN separation sep ON (sep.image_id = i.id)
            JOIN souscategorie sc ON (sep.souscategorie_id = sc.id)
            JOIN soussouscategorie ssc on (sep.soussouscategorie_id = ssc.id)
            WHERE i.exercice in ('.$exercices.') 
            AND l.dossier_id = :DOSSIER_ID AND bsca.image_flague_id IS NULL AND i.id NOT IN ('.$nonLettrables.')                 
            GROUP BY bsca.image_id, sc.id, ssc.id             
            HAVING 
            (
                -ABS(ROUND(sum(bsca.montant),2)) = ROUND(:MONTANT,2) AND (sc.id = 7 OR ssc.id = 2791) OR 
                ABS(ROUND(sum(bsca.montant),2)) = ROUND(:MONTANT_2,2) AND sc.id <> 7 and ssc.id <> 2791
            )
            LIMIT 1';

        $params =
            [
                'DOSSIER_ID' => $releve->getBanqueCompte()->getDossier()->getId(),
                'MONTANT' => ($releve->getDebit() - $releve->getCredit()),
                'MONTANT_2' => ($releve->getDebit() - $releve->getCredit())
            ];

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

        if ($res && intval($res->isa) != 0) return (object)
        [
            's' => $status,
            'so' => $soeurs,
            'id' => $id,
            'l' => $libelle,
            't' => $type,
            'it' => $idType,
            'nb' => 1
        ];
        return null;
    }

    /**
     * @param Releve $releve
     * @param int $p
     * @param ReleveExt|null $releveExt
     * @return array
     */
    public function imageAAffecter(Releve $releve, $p = 0, ReleveExt $releveExt = null)
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

        $req = '
            SELECT bsca.image_id, sc.id AS sc_id, ssc.id AS ssc_id 
            FROM banque_sous_categorie_autre bsca
            JOIN image i ON (i.id = bsca.image_id)
            JOIN lot l on (l.id = i.lot_id)
            JOIN separation sep ON (sep.image_id = i.id)
            JOIN souscategorie sc ON (sep.souscategorie_id = sc.id)
            JOIN soussouscategorie ssc on (sep.soussouscategorie_id = ssc.id)
            WHERE i.exercice in ('.$exercices.') 
            AND l.dossier_id = :DOSSIER_ID AND bsca.image_flague_id IS NULL AND i.id NOT IN ('.$nonLettrables.') 
            GROUP BY bsca.image_id, sc.id, ssc.id             
            HAVING 
            (
                ABS(ROUND(sum(bsca.montant),2)) = ROUND(:MONTANT,2) AND (sc.id = 7 OR ssc.id = 2791) OR 
                -ABS(ROUND(sum(bsca.montant),2)) = ROUND(:MONTANT_2,2) AND sc.id <> 7 and ssc.id <> 2791
            )
        ';

        $params =
            [
                'DOSSIER_ID' => $releve->getBanqueCompte()->getDossier()->getId(),
                'MONTANT' => -($releveExt ? $releveExt->getMontant() : $releve->getDebit() - $releve->getCredit()),
                'MONTANT_2' => -($releveExt ? $releveExt->getMontant() : $releve->getDebit() - $releve->getCredit())
            ];

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $res = $prep->fetchAll();

        $results = [];
        foreach ($res as $re)
        {
            /** @var Image $image */
            $image = $this->getEntityManager()->getRepository('AppBundle:Image')
                ->find($re->image_id);

            $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                ->getSeparationByImage($image);

            $lib = '';
            if ($separation->getSoussouscategorie()) $lib = $separation->getSoussouscategorie()->getLibelleNew();
            elseif ($separation->getSouscategorie()) $lib = $separation->getSouscategorie()->getLibelleNew();


            $scId = intval($re->sc_id);
            $sscId = intval($re->ssc_id);

            /** @var BanqueSousCategorieAutre[] $banqueSCAutres */
            $banqueSCAutres = $this->getEntityManager()->getRepository('AppBundle:BanqueSousCategorieAutre')
                ->findBy(['image'=>$image]);

            $imageNom = $image->getNom();
            $g = $imageNom.'-'.$image->getNumPage();
            $keyI = $image->getId().'_';

            foreach ($banqueSCAutres as $banqueSCAutre)
            {
                $bilan = null;
                $resultat = null;
                $tva = null;
                $similarity = 25;
                /** @var \DateTime $date */
                $date = null;
                if ($banqueSCAutre->getDate()) $date = $banqueSCAutre->getDate();
                elseif ($banqueSCAutre->getDateFacture()) $date = $banqueSCAutre->getDateFacture();

                $key = $date ? $date->format('Yml') : '21000101'. '_' .$p;

                if ($banqueSCAutre->getCompteTiers())
                {
                    $bilan = (object)
                    [
                        'id' => Boost::boost($banqueSCAutre->getCompteTiers()->getId()),
                        'l' => $banqueSCAutre->getCompteTiers()->getCompteStr(),
                        't' => 1
                    ];
                    $g .= '1_'.$banqueSCAutre->getCompteTiers()->getId();
                }
                elseif ($banqueSCAutre->getCompteBilan())
                {
                    if (intval(substr($banqueSCAutre->getCompteBilan()->getCompte(),0,1)) < 6)
                    {
                        if (intval(substr($banqueSCAutre->getCompteBilan()->getCompte(),0,3)) === 445)
                        {
                            $tva = (object)
                            [
                                'id' => Boost::boost($banqueSCAutre->getCompteTva()->getId()),
                                'l' => $banqueSCAutre->getCompteTva()->getCompte(),
                                't' => 0
                            ];
                            $g .= '0_'.$banqueSCAutre->getCompteTva()->getId();
                        }
                        else
                        {
                            $bilan = (object)
                            [
                                'id' => Boost::boost($banqueSCAutre->getCompteBilan()->getId()),
                                'l' => $banqueSCAutre->getCompteBilan()->getCompte(),
                                't' => 0
                            ];
                            $g .= '0_'.$banqueSCAutre->getCompteBilan()->getId();
                        }
                    }
                    else
                    {
                        $resultat = (object)
                        [
                            'id' => Boost::boost($banqueSCAutre->getCompteBilan()->getId()),
                            'l' => $banqueSCAutre->getCompteBilan()->getCompte(),
                            't' => 0
                        ];
                        $g .= '0_'.$banqueSCAutre->getCompteBilan()->getId();
                    }
                }

                if ($banqueSCAutre->getCompteChg())
                {
                    $resultat = (object)
                    [
                        'id' => Boost::boost($banqueSCAutre->getCompteChg()->getId()),
                        'l' => $banqueSCAutre->getCompteChg()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$banqueSCAutre->getCompteChg()->getId();
                }
                if ($banqueSCAutre->getCompteTva())
                {
                    $tva = (object)
                    [
                        'id' => Boost::boost($banqueSCAutre->getCompteTva()->getId()),
                        'l' => $banqueSCAutre->getCompteTva()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$banqueSCAutre->getCompteTva()->getId();
                }

                $mTTc = abs($banqueSCAutre->getMontant());

                if ($scId = 7 || $sscId = 2791) $mTTc *= -1;

                $tauxCoeff = 1;
                if ($banqueSCAutre->getTvaTaux()) $tauxCoeff += $banqueSCAutre->getTvaTaux()->getTaux() / 100;
                $mHt = round($mTTc / $tauxCoeff,2);
                $mTva = $mTTc - $mHt;

                if (array_key_exists($keyI,$results))
                {
                    $results[$keyI]['ht'] += $mHt;
                    $results[$keyI]['mtva'] += $mTva;
                    $results[$keyI]['ttc'] += $mTTc;
                }
                else
                    $results[$keyI] =
                        [
                            'k' => $key,
                            'g' => $g,
                            'id' => $p,
                            'p' => $p,
                            'i' => $image->getNom().'-'.$image->getNumPage(),
                            'ii' => Boost::boost($image->getId()),
                            'd' => $date ? $date->format('d/m/Y') : '',
                            't' => $lib,
                            'e' => $image->getExercice(),
                            'b' => $bilan,
                            'r' => $resultat,
                            'tva' => $tva,
                            'ht' => $mHt,
                            'mtva' => $mTva,
                            'ttc' => $mTTc,
                            'tr' => '',
                            'nr' => '',
                            'dr' => '',
                            'f' => (is_null($image->getImageFlague())) ? 0 : 1,
                            'sm' => $similarity,
                            'type' => 1
                        ];
                $p++;
            }

        }

        return array_values($results);
    }

    /**
     * @param Image[] $images
     * @return BanqueSousCategorieAutre[]
     */
    public function getAllByImages($images = [])
    {
        return $this->createQueryBuilder('bsca')
            ->where('bsca.image IN (:images)')
            ->andWhere('bsca.imageFlague IS NULL')
            ->setParameters([
                'images' => $images
            ])
            ->orderBy('bsca.image')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param ImageFlague $imageFlague
     * @param BanqueSousCategorieAutre|null $banqueSousCategorieAutre
     * @param bool $imageFlague2
     * @return BanqueSousCategorieAutre[]
     */
    public function getChildImageFlagues(ImageFlague $imageFlague, BanqueSousCategorieAutre $banqueSousCategorieAutre = null, $imageFlague2 = false)
    {
        if (!$imageFlague2)
            return $this->createQueryBuilder('bsca')
                ->leftJoin('bsca.image','i')
                ->where('bsca.imageFlague = :imageFlague')
                ->andWhere('bsca.id <> :id')
                ->andWhere('i.supprimer = 0')
                ->setParameters([
                    'imageFlague' => $imageFlague,
                    'id' => $banqueSousCategorieAutre ? $banqueSousCategorieAutre->getId() : -1
                ])
                ->getQuery()
                ->getResult();
        else
            return $this->createQueryBuilder('bsca')
                ->leftJoin('bsca.image','i')
                ->where('bsca.imageFlague2 = :imageFlague')
                ->andWhere('bsca.id <> :id')
                ->andWhere('i.supprimer = 0')
                ->setParameters([
                    'imageFlague' => $imageFlague,
                    'id' => $banqueSousCategorieAutre ? $banqueSousCategorieAutre->getId() : -1
                ])
                ->getQuery()
                ->getResult();
    }

    /**
     * @param BanqueSousCategorieAutre $banqueSousCategorieAutre
     * @return bool
     */
    public function checkIfHasMontantPicDoc(BanqueSousCategorieAutre $banqueSousCategorieAutre)
    {
        $exercice = $banqueSousCategorieAutre->getImage()->getExercice();
        $exercices = '' . ($exercice - 1);
        $exercices .= ','. $exercice;
        $exercices .= ',' . ($exercice + 1);

        $req = '
            SELECT COUNT(tic.image_id) AS isa
            FROM tva_imputation_controle tic
            JOIN image i ON (i.id = tic.image_id)
            JOIN lot l ON (l.id = i.lot_id)
            JOIN separation s ON (s.image_id = i.id)
            JOIN imputation_controle ic ON (ic.image_id = i.id)
            WHERE i.exercice IN ('.$exercices.')
            AND l.dossier_id = :DOSSIER_ID AND tic.image_flague_id IS NULL
            GROUP BY tic.image_id,s.categorie_id,ic.type_piece_id
            HAVING
            (
              (-ABS(ROUND(sum(tic.montant_ttc),2)) = ROUND(:MONTANT,2) 
                AND (s.categorie_id IN (10,12) AND ic.type_piece_id <> 1 OR s.categorie_id IN (9,13) AND ic.type_piece_id = 1)) OR
                 
              (ABS(ROUND(sum(tic.montant_ttc),2)) = ROUND(:MONTANT_1,2) 
                AND (s.categorie_id NOT IN (10,12) AND ic.type_piece_id = 1 OR s.categorie_id NOT IN (9,13) AND ic.type_piece_id <> 1))                
            )
            LIMIT 1
            ';

        $montant = $banqueSousCategorieAutre->getMontant();

        $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
            ->getSeparationByImage($banqueSousCategorieAutre->getImage());

        $negatif = true;
        if ($separation)
        {
            if ($separation->getSoussouscategorie() && $separation->getSoussouscategorie()->getId() == 2791)
                $negatif = false;
            elseif ($separation->getSouscategorie() && $separation->getSouscategorie()->getId() == 7)
                $negatif = false;
        }

        if ($negatif) $montant *= -1;

        $params =
            [
                'DOSSIER_ID' => $banqueSousCategorieAutre->getImage()->getLot()->getDossier()->getId(),
                'MONTANT' => $montant,
                'MONTANT_1' => $montant
            ];

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $res = $prep->fetch();

        return ($res && intval($res->isa) > 0);
    }

    /**
     * @param BanqueSousCategorieAutre $banqueSousCategorieAutre
     * @return object
     */
    public function getStatus(BanqueSousCategorieAutre $banqueSousCategorieAutre)
    {
        $date = null;
        if ($banqueSousCategorieAutre->getDate()) $date = $banqueSousCategorieAutre->getDate();
        elseif ($banqueSousCategorieAutre->getDateFacture()) $date = $banqueSousCategorieAutre->getDateFacture();

        $mTtc = $banqueSousCategorieAutre->getMontant();
        $coefTva = 1;
        if ($banqueSousCategorieAutre->getCompteTva() && $banqueSousCategorieAutre->getTvaTaux())
            $coefTva += $banqueSousCategorieAutre->getTvaTaux()->getTaux() / 100;
        $mHt = $mTtc / $coefTva;
        $mTva = $mTtc - $mHt;

        $bilan = null;
        $tva = null;
        $resultat = null;

        if ($banqueSousCategorieAutre->getCompteTiers())
        {
            $bilan = (object)
            [
                'id' => Boost::boost($banqueSousCategorieAutre->getCompteTiers()->getId()),
                'l' => $banqueSousCategorieAutre->getCompteTiers()->getCompteStr(),
                't' => 1
            ];
        }
        elseif ($banqueSousCategorieAutre->getCompteBilan())
        {
            if (intval(substr($banqueSousCategorieAutre->getCompteBilan()->getCompte(),0,1)) < 6)
            {
                if (intval(substr($banqueSousCategorieAutre->getCompteBilan()->getCompte(),0,3)) === 445)
                {
                    $tva = (object)
                    [
                        'id' => Boost::boost($banqueSousCategorieAutre->getCompteTva()->getId()),
                        'l' => $banqueSousCategorieAutre->getCompteTva()->getCompte(),
                        't' => 0
                    ];
                }
                else
                {
                    $bilan = (object)
                    [
                        'id' => Boost::boost($banqueSousCategorieAutre->getCompteBilan()->getId()),
                        'l' => $banqueSousCategorieAutre->getCompteBilan()->getCompte(),
                        't' => 0
                    ];
                }
            }
            else
            {
                $resultat = (object)
                [
                    'id' => Boost::boost($banqueSousCategorieAutre->getCompteBilan()->getId()),
                    'l' => $banqueSousCategorieAutre->getCompteBilan()->getCompte(),
                    't' => 0
                ];
            }
        }
        if ($banqueSousCategorieAutre->getCompteChg())
        {
            $resultat = (object)
            [
                'id' => Boost::boost($banqueSousCategorieAutre->getCompteChg()->getId()),
                'l' => $banqueSousCategorieAutre->getCompteChg()->getCompte(),
                't' => 0
            ];
        }
        if ($banqueSousCategorieAutre->getCompteTva())
        {
            $tva = (object)
            [
                'id' => Boost::boost($banqueSousCategorieAutre->getCompteTva()->getId()),
                'l' => $banqueSousCategorieAutre->getCompteTva()->getCompte(),
                't' => 0
            ];
        }

        $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
            ->getSeparationByImage($banqueSousCategorieAutre->getImage());

        $negatif = true;
        if ($separation)
        {
            if ($separation->getSoussouscategorie() && $separation->getSoussouscategorie()->getId() == 2791)
                $negatif = false;
            elseif ($separation->getSouscategorie() && $separation->getSouscategorie()->getId() == 7)
                $negatif = false;
        }

        if ($negatif)
        {
            $mHt *= -1;
            $mTva *= -1;
            $mTtc *= -1;
        }

        if (!$date)
        {
            $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                ->getByImage($banqueSousCategorieAutre->getImage());

            if ($imputationControle && $imputationControle->getDateFacture())
                $date = $imputationControle->getDateFacture();
            if ($imputationControle && $imputationControle->getDateEcheance() && !$date)
                $date = $imputationControle->getDateEcheance();
        }

        return (object)
        [
            'id' => Boost::boost($banqueSousCategorieAutre->getId()),
            'date' => $date ? $date->format('d/m/Y') : null,
            'libelle' => $this->getLibelleComplete($banqueSousCategorieAutre),
            'status' => $this->status($banqueSousCategorieAutre),
            'mHt' => $mHt,
            'mTva' => $mTva,
            'mTtc' => $mTtc,
            'bilan' => $bilan,
            'tva' => $tva,
            'resultat' => $resultat,
            'image' => (object)
            [
                'id' => Boost::boost($banqueSousCategorieAutre->getImage()->getId()),
                'nom' => $banqueSousCategorieAutre->getImage()->getNom()
            ]
        ];
    }

    /**
     * @param BanqueSousCategorieAutre $banqueSousCategorieAutre
     * @return object
     */
    public function status(BanqueSousCategorieAutre $banqueSousCategorieAutre)
    {
        /**
         * @var int $status
         * 0 : lettré
         * 1 : piece à valider
         * 2 : piece manquante
         */
        $status = 2;
        /** @var Image[] $images */
        $images = [];

        $imageFlagueId = 0;
        if ($banqueSousCategorieAutre->getImageFlague2())
        {
            $status = 0;
            $imageFlagueId = $banqueSousCategorieAutre->getImageFlague2()->getId();
            $tvaImputationControles = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                ->getChildImageFlagues($banqueSousCategorieAutre->getImageFlague2());

            foreach ($tvaImputationControles as $tvaImputationControle)
            {
                $key = $tvaImputationControle->getImage()->getId();
                if (!array_key_exists($key,$images))
                    $images[$key] = (object)
                    [
                        'id' => Boost::boost($tvaImputationControle->getImage()->getId()),
                        'nom' => $tvaImputationControle->getImage()->getNom()
                    ];
            }
        }
        if ($status == 2 && $this->checkIfHasMontantPicDoc($banqueSousCategorieAutre)) $status = 1;

        return (object)
        [
            'images' => array_values($images),
            'status' => $status,
            'image_flague' => Boost::boost($imageFlagueId)
        ];
    }

    public function picDocs(BanqueSousCategorieAutre $banqueSousCategorieAutre)
    {
        $exercice = $banqueSousCategorieAutre->getImage()->getExercice();
        $exercices = '' . ($exercice - 1);
        $exercices .= ','. $exercice;
        $exercices .= ',' . ($exercice + 1);

        $req = '
            SELECT tic.image_id 
            FROM tva_imputation_controle tic
            JOIN image i ON (i.id = tic.image_id)
            JOIN lot l ON (l.id = i.lot_id)
            JOIN separation s ON (s.image_id = i.id)
            JOIN imputation_controle ic ON (ic.image_id = i.id)
            WHERE i.exercice IN ('.$exercices.')
            AND l.dossier_id = :DOSSIER_ID AND tic.image_flague_id IS NULL
            GROUP BY tic.image_id,s.categorie_id,ic.type_piece_id
            HAVING
            (
              (-ABS(ROUND(sum(tic.montant_ttc),2)) = ROUND(:MONTANT,2) 
                AND (s.categorie_id IN (10,12) AND ic.type_piece_id <> 1 OR s.categorie_id IN (9,13) AND ic.type_piece_id = 1)) OR
                 
              (ABS(ROUND(sum(tic.montant_ttc),2)) = ROUND(:MONTANT_1,2) 
                AND (s.categorie_id NOT IN (10,12) AND ic.type_piece_id = 1 OR s.categorie_id NOT IN (9,13) AND ic.type_piece_id <> 1))                
            )
            ';

        $montant = $banqueSousCategorieAutre->getMontant();
        $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
            ->getSeparationByImage($banqueSousCategorieAutre->getImage());

        $negatif = true;
        if ($separation)
        {
            if ($separation->getSoussouscategorie() && $separation->getSoussouscategorie()->getId() == 2791)
                $negatif = false;
            elseif ($separation->getSouscategorie() && $separation->getSouscategorie()->getId() == 7)
                $negatif = false;
        }

        if ($negatif) $montant *= -1;

        $params =
            [
                'DOSSIER_ID' => $banqueSousCategorieAutre->getImage()->getLot()->getDossier()->getId(),
                'MONTANT' => $montant,
                'MONTANT_1' => $montant
            ];

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $res = $prep->fetchAll();

        $results = [];
        $p = 0;

        foreach ($res as $re)
        {
            /** @var Image $image */
            $image = $this->getEntityManager()->getRepository('AppBundle:Image')
                ->find($re->image_id);

            /** @var TvaImputationControle[] $tvaImputationControls */
            $tvaImputationControls = $this->getEntityManager()
                ->getRepository('AppBundle:TvaImputationControle')
                ->createQueryBuilder('ti')
                ->where('ti.image = :image')
                ->setParameter('image',$image)
                ->getQuery()
                ->getResult();

            $imageNom = $image->getNom();
            $similarity = 25;

            foreach ($tvaImputationControls as $tvaImputationControl)
            {
                $mTva = $tvaImputationControl->getMontantHt() * ((is_null($tvaImputationControl->getTvaTaux())) ? 0 : $tvaImputationControl->getTvaTaux()->getTaux()) / 100;
                $mTTc = $tvaImputationControl->getMontantHt() + $mTva;
                $imputationControl = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                    ->getImputationControle($tvaImputationControl);

                if ($negatif)
                {
                    $mTva *= -1;
                    $mTTc *= -1;
                }

                $g = $imageNom.'-'.$image->getNumPage();

                $key = ((is_null($imputationControl) || is_null($imputationControl->getDateFacture())) ? '21000101' : $imputationControl->getDateFacture()->format('Ymd')). '_' .$p;

                $bilan = null;
                $resultat = null;
                $tva = null;

                $keyI = $image->getId().'_';
                if ($tvaImputationControl->getTiers())
                {
                    $bilan = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getTiers()->getId()),
                        'l' => $tvaImputationControl->getTiers()->getCompteStr(),
                        't' => 1
                    ];
                    $g .= '1_'.$tvaImputationControl->getTiers()->getId();
                }
                elseif ($tvaImputationControl->getPccBilan())
                {
                    $bilan = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getPccBilan()->getId()),
                        'l' => $tvaImputationControl->getPccBilan()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$tvaImputationControl->getPccBilan()->getId();
                }

                if ($tvaImputationControl->getPcc())
                {
                    $resultat = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getPcc()->getId()),
                        'l' => $tvaImputationControl->getPcc()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$tvaImputationControl->getPcc()->getId();
                }
                if ($tvaImputationControl->getPccTva())
                {
                    $tva = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getPccTva()->getId()),
                        'l' => $tvaImputationControl->getPccTva()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$tvaImputationControl->getPccTva()->getId();
                }

                $mHt = $mTTc - $mTva;
                if (array_key_exists($keyI,$results))
                {
                    $results[$keyI]['ht'] += $mHt;
                    $results[$keyI]['mtva'] += $mTva;
                    $results[$keyI]['ttc'] += $mTTc;
                }
                else
                    $results[$keyI] =
                        [
                            'k' => $key,
                            'g' => $g,
                            'id' => $p,
                            'p' => $p,
                            'i' => $image->getNom().'-'.$image->getNumPage(),
                            'ii' => Boost::boost($image->getId()),
                            'd' => (is_null($imputationControl) || is_null($imputationControl->getDateFacture())) ? '' : $imputationControl->getDateFacture()->format('d/m/Y'),
                            't' => ((is_null($tvaImputationControl->getTiers())) ? '' : $tvaImputationControl->getTiers()->getIntitule() . ' - ') . $imputationControl->getNumFacture(),
                            'e' => $tvaImputationControl->getImage()->getExercice(),
                            'b' => $bilan,
                            'r' => $resultat,
                            'tva' => $tva,
                            'ht' => $mHt,
                            'mtva' => $mTva,
                            'ttc' => $mTTc,
                            'tr' => (is_null($imputationControl) || is_null($imputationControl->getModeReglement())) ? '' : $imputationControl->getModeReglement()->getLibelle(),
                            'nr' => (is_null($imputationControl) || is_null($imputationControl->getNumPaiement())) ? '' : $imputationControl->getNumPaiement(),
                            'dr' => (is_null($imputationControl) || is_null($imputationControl->getDateReglement())) ? '' : $imputationControl->getDateReglement()->format('d/m/Y'),
                            'f' => (is_null($image->getImageFlague())) ? 0 : 1,
                            'sm' => $similarity,
                            'type' => 0
                        ];
                $p++;
            }
        }
        return array_values($results);
    }

    /**
     * @param BanqueSousCategorieAutre $banqueSousCategorieAutre
     * @return string
     */
    public function getLibelleComplete(BanqueSousCategorieAutre $banqueSousCategorieAutre)
    {
        $libelle = trim($banqueSousCategorieAutre->getLibelle());
        if ($banqueSousCategorieAutre->getNomTiers() && trim($banqueSousCategorieAutre->getNomTiers()) != '')
            $libelle .= ' ' . $banqueSousCategorieAutre->getNomTiers();

        return trim($libelle);
    }

    /**
     * @param ImageFlague $imageFlague
     * @return object
     */
    public function getStatLettre(ImageFlague $imageFlague)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $prep = $pdo->prepare('
          SELECT COUNT(id) AS isa 
          FROM banque_sous_categorie_autre 
          WHERE image_flague_id = :image_flague_id        
        ');
        $params = [
            'image_flague_id' => $imageFlague->getId()
        ];
        $prep->execute($params);
        $res = $prep->fetch();
        $total = intval($res->isa);

        $prep = $pdo->prepare('
          SELECT COUNT(id) AS isa 
          FROM banque_sous_categorie_autre 
          WHERE image_flague_id = :image_flague_id AND 
            image_flague_2_id IS NOT NULL       
        ');
        $params = [
            'image_flague_id' => $imageFlague->getId()
        ];
        $prep->execute($params);
        $res = $prep->fetch();
        $lettre = intval($res->isa);

        return (object)
        [
            'total' => $total,
            'lettre' => $lettre
        ];
    }

    /**
     * @param Dossier $dossier
     * @param string $nomImage
     * @param int $montant
     * @param bool $avecLettre
     * @return BanqueSousCategorieAutre[]
     */
    public function searchByPieceMontant(Dossier $dossier, $nomImage = '' , $montant = 0, $avecLettre = false)
    {
        $banqueSousCategorieAutres = $this->createQueryBuilder('bsca')
            ->select('bsca as bsca_a,ROUND(SUM(bsca.montant),2) as m')
            ->leftJoin('bsca.image', 'i')
            ->leftJoin('i.lot','l')
            ->where('l.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->groupBy('i.id');

        if (!$avecLettre)
            $banqueSousCategorieAutres = $banqueSousCategorieAutres->andWhere('bsca.imageFlague IS NULL');

        if ($montant != 0)
            $banqueSousCategorieAutres = $banqueSousCategorieAutres
                ->having('m = :montant')
                ->setParameter('montant',$montant);
        if ($nomImage != '')
            $banqueSousCategorieAutres = $banqueSousCategorieAutres
                ->andWhere('i.nom LIKE :nomImage')
                ->setParameter('nomImage','%'.$nomImage.'%');

        $banqueSousCategorieAutres = $banqueSousCategorieAutres
            ->getQuery()
            ->getResult();

        /** @var BanqueSousCategorieAutre[] $bscas */
        $bscas = [];

        foreach ($banqueSousCategorieAutres as $banqueSousCategorieAutre)
        {
            $bscas = array_merge($bscas,
                $this->getEntityManager()->getRepository('AppBundle:BanqueSousCategorieAutre')
                    ->createQueryBuilder('bsca')
                    ->where('bsca.image = :image')
                    ->setParameter('image', $banqueSousCategorieAutre['bsca_a']->getImage())
                    ->andWhere('bsca.imageFlague IS NULL')
                    ->getQuery()
                    ->getResult()
            );
        }

        return $bscas;
    }
}