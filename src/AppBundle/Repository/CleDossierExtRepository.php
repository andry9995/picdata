<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 06/02/2020
 * Time: 11:48
 */

namespace AppBundle\Repository;


use AppBundle\Controller\StringExt;
use AppBundle\Entity\CleDossier;
use AppBundle\Entity\CleDossierExt;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageFlague;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveExt;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;

class CleDossierExtRepository extends EntityRepository
{
    /**
     * @param CleDossier $cleDossier
     * @return CleDossierExt[]
     */
    public function getForCleDossier(CleDossier $cleDossier, $notIns = [])
    {
        $cleDossiers = $this->createQueryBuilder('cde')
            ->where('cde.cleDossier = :cleDossier')
            ->setParameter('cleDossier', $cleDossier);

        if (count($notIns) > 0)
            $cleDossiers = $cleDossiers
                ->andWhere('cde.id NOT IN (:notIns)')
                ->setParameter('notIns', $notIns);

        return $cleDossiers
            ->getQuery()
            ->getResult();
    }

    /**
     * @param CleDossierExt[] $cleDossierExts
     * @return array
     */
    public function getCleDossierAdds($cleDossierExts)
    {
        $cleDossierAdds = [];
        foreach ($cleDossierExts as $cleDossierExt)
        {
            $pccs = [];

            if ($cleDossierExt->getPcc())
                $pccs[] = '0#'. $cleDossierExt->getPcc()->getId();
            elseif ($cleDossierExt->getTiers())
                $pccs[] = '1#'. $cleDossierExt->getTiers()->getId();

            $options = (object)
            [
                'recherche' => $cleDossierExt->getRechercher(),
                'format' => $cleDossierExt->getFormat(),
                'car_prec' => $cleDossierExt->getTextStart(),
                'car_fin' => $cleDossierExt->getTextEnd(),
                'pos_deb' => intval($cleDossierExt->getStart()),
                'pos_len' => intval($cleDossierExt->getTextLength())
            ];

            $cleDossierAdds[] = (object)
            [
                'pcgs' => StringExt::encodeURI($cleDossierExt->getPcgs()),
                'pccs' => StringExt::encodeURI(json_encode($pccs)),
                'options' => StringExt::encodeURI(json_encode($options))
            ];
        }

        return $cleDossierAdds;
    }

    /**
     * @param CleDossier $cleDossier
     * @return CleDossierExt
     */
    public function getRestes(CleDossier $cleDossier)
    {
        return $this->createQueryBuilder('cde')
            ->where('cde.cleDossier = :cleDossier')
            ->andWhere('cde.rechercher = :recherche')
            ->setParameters([
                'cleDossier' => $cleDossier,
                'recherche' => 4
            ])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * @param Releve $releve
     * @param CleDossierExt $cleDossierExt
     * @return object
     */
    public function getAddByReleve(Releve $releve, CleDossierExt $cleDossierExt)
    {
        $start = intval($cleDossierExt->getStart());
        $textLength = intval($cleDossierExt->getTextLength());
        $textStart = $cleDossierExt->getTextStart();
        $textEnd = $cleDossierExt->getTextEnd();
        $format = intval($cleDossierExt->getFormat());
        $rechercher = intval($cleDossierExt->getRechercher());

        $releveExt = $this->getEntityManager()->getRepository('AppBundle:ReleveExt')
            ->findOneBy([
                'releve' => $releve,
                'cleDossierExt' => $cleDossierExt
            ]);

        if ($releveExt && $releveExt->getImageFlague())
        {
            return (object)
            [
                'cde' => $cleDossierExt,
                're' => $releveExt,
                'images' => []
            ];
        }

        $res = '';

        $libelle = $releve->getLibelle();

        if ($start != -1 && $textLength != -1)
        {
            $res = substr($releve->getLibelle(), $start, $textLength);
        }
        elseif ($textStart != '' && $textLength != -1)
        {
            $pos = strpos($libelle, $textStart) + strlen($textStart);
            if ($pos !== false)
                $res = substr($libelle, $pos, $textLength);
        }
        elseif ($textStart != '' && $textEnd)
        {
            $formule = $textStart .'?'. $textEnd;

            //F[an]?
            //preg_match('#F([A-Za-z0-9]*) (.*)[ |;]#i', $libelle.'#', $vals);
            if (strpos($formule,'[an]') || strpos($formule,'[n]') || strpos($formule,'[a]'))
            {
                $formule = str_replace('[an]','([A-Za-z0-9]*)',$formule);
                $formule = str_replace('[n]','([0-9]*)',$formule);
                $formule = str_replace('[a]', '([A-Za-z]*)',$formule);

                $formule = str_replace('?' ,'(.*)', $formule);
            }
            //F?
            /*preg_match('#F([A-Za-z0-9]*) #i', $libelle.'#', $vals);*/
            else
            {

                if ($format == 1 && $rechercher == 1)
                    $f = '([0-9]*)';
                else
                    $f = '([A-Za-z0-9]*)';

                $formule = str_replace('?', $f, $formule);
            }

            preg_match('#'.$formule.'#i', $libelle . ' xx', $vals);

            if (count($vals) > 0)
                $res = $vals[count($vals) - 1];
        }

        if ($res == '') return null;

        if ($format == 1 && $rechercher == 1)
        {
            $res = floatval(preg_replace('/[^0-9.,]/','',$res));
            $relevePercent = abs($releve->getDebit() - $releve->getCredit()) * 20 / 100;

            $iteration = 0;
            while ($iteration < 4 && abs($res - $releve->getDebit() - $releve->getCredit()) > $relevePercent)
            {
                $res *= 0.1;
                $iteration++;
            }

            if ($iteration == 4)
            {
                return null;
            }
        }

        $res = ($format == 1 && $rechercher == 1) ? round($res,2) : $res;

        //return $res;

        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $params = [];

        $em = $this->getEntityManager();
        //0: numfact; 1:montant; 2:date facture; 3: tiers; 4:reste

        //numfacture
        if ($rechercher == 0)
        {
            $req = '
                SELECT DISTINCT i.id 
                FROM tva_imputation_controle tic 
                JOIN imputation_controle ic ON (tic.image_id = ic.image_id)
                JOIN image i ON (i.id = ic.image_id)
                JOIN lot l ON (l.id = i.lot_id)
                JOIN separation sep ON (i.id = sep.image_id)
                LEFT JOIN souscategorie sc ON (sc.id = sep.souscategorie_id)
                WHERE ic.num_facture LIKE :likeFact 
                        AND l.dossier_id = :dossier 
                        AND (sc.libelle_new <> :lDoublon OR sep.souscategorie_id IS NULL) 
                        AND i.supprimer = 0 
                        AND tic.image_flague_id IS NULL';

            $params['likeFact'] = '%'.$res.'%';
            $params['dossier'] = $releve->getBanqueCompte()->getDossier()->getId();
            $params['lDoublon'] = 'DOUBLON';

            $prep = $pdo->prepare($req);
            $prep->execute($params);
            $prepRes = $prep->fetchAll();

            $ids = [-1];
            foreach ($prepRes as $prepRe)
                $ids[] = $prepRe->id;

            /** @var Image[] $images */
            $images = $this->getEntityManager()->getRepository('AppBundle:Image')
                ->createQueryBuilder('i')
                ->where('i.id IN (:ids)')
                ->setParameter('ids', $ids)
                ->getQuery()
                ->getResult();

            if (count($images) == 1)
            {
                $imageFlague = new ImageFlague();
                $imageFlague
                    ->setDateCreation(new \DateTime());

                $em->persist($imageFlague);
                $em->flush();

                if ($releveExt) $releveExt->setImageFlague($imageFlague);
                else
                {
                    $releveExt = new ReleveExt();
                    $releveExt
                        ->setReleve($releve)
                        ->setCleDossierExt($cleDossierExt)
                        ->setImageFlague($imageFlague);

                    $em->persist($releveExt);
                }

                $tvaImputationControles = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                    ->getNotFlague($images[0]);

                foreach ($tvaImputationControles as $tvaImputationControle)
                    $tvaImputationControle->setImageFlague($imageFlague);

                $em->flush();

                return (object)
                [
                    'cde' => $cleDossierExt,
                    're' => $releveExt,
                    'images' => []
                ];
            }
            elseif (count($images) == 0) return null;
            else
            {
                return (object)
                [
                    'cde' => $cleDossierExt,
                    're' => $releveExt,
                    'images' => $images
                ];
            }
        }

        //montant
        elseif ($rechercher == 1)
        {
            $req = '
                SELECT i.id, ROUND(sum(tic.montant_ttc),2) AS s
                FROM tva_imputation_controle tic
                JOIN imputation_controle ic ON (tic.image_id = ic.image_id)
                JOIN image i ON (i.id = ic.image_id)
                JOIN lot l ON (l.id = i.lot_id)
                JOIN separation sep ON (i.id = sep.image_id)
                LEFT JOIN souscategorie sc ON (sc.id = sep.souscategorie_id)
                WHERE l.dossier_id = :dossier 
                        AND (sc.libelle_new <> :lDoublon OR sep.souscategorie_id IS NULL) 
                        AND tic.image_flague_id IS NULL
                        AND i.supprimer = 0 
                GROUP BY i.id, sep.categorie_id, ic.type_piece_id
                HAVING 
                (
                    ROUND(sum(tic.montant_ttc),2) = -ROUND(:montant,2) AND ((sep.categorie_id IN (10,12) AND ic.type_piece_id <> 1) OR (sep.categorie_id IN (9,13) AND ic.type_piece_id = 1)) OR
                    ROUND(sum(tic.montant_ttc),2) = ROUND(:montant_,2) AND NOT((sep.categorie_id IN (10,12) AND ic.type_piece_id <> 1) OR (sep.categorie_id IN (9,13) AND ic.type_piece_id = 1))
                )';

            $params['montant'] = $res;
            $params['montant_'] = $res;
            $params['dossier'] = $releve->getBanqueCompte()->getDossier()->getId();
            $params['lDoublon'] = 'DOUBLON';

            $prep = $pdo->prepare($req);
            $prep->execute($params);
            $prepRes = $prep->fetchAll();

            $ids = [-1];
            foreach ($prepRes as $prepRe)
                $ids[] = $prepRe->id;

            /** @var Image[] $images */
            $images = $this->getEntityManager()->getRepository('AppBundle:Image')
                ->createQueryBuilder('i')
                ->where('i.id IN (:ids)')
                ->setParameter('ids', $ids)
                ->getQuery()
                ->getResult();

            if (!$releveExt)
            {
                $releveExt = new ReleveExt();
                $releveExt
                    ->setReleve($releve)
                    ->setCleDossierExt($cleDossierExt);

                $em->persist($releveExt);
            }

            $em->flush();
            $releveExt->setMontant($res);

            return (object)
            [
                'cde' => $cleDossierExt,
                'images' => $images,
                're' => $releveExt
            ];
        }

        return null;
    }
}