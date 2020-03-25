<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 02/03/2017
 * Time: 13:50
 */

namespace AppBundle\Repository;
use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\EchangeItem;
use AppBundle\Entity\EchangeType;
use AppBundle\Functions\CustomPdoConnection;


use Doctrine\ORM\EntityRepository;

class EchangeItemRepository extends EntityRepository
{
    public function getLastEchangeItem($echangeId){
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $query = "SELECT * 
                  FROM dbboost.echange_item ei 
                  WHERE ei.echange_id = ".$echangeId."
                  and ei.supprimer = 0
                  order by ei.id desc limit 1";
        $prep = $pdo->prepare($query);
        $prep->execute();
        $res = $prep->fetchAll();
        return (count($res) > 0) ? $res[0] : $res;
    }

    public function getByEchange($echangeId) {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $query = "SELECT * 
                  FROM dbboost.echange_item ei 
                  where ei.echange_id = ".$echangeId."
                  order by ei.id asc limit 1";
        $prep = $pdo->prepare($query);
        $prep->execute();
        return $prep->fetchAll();
    }

    /**
     * @param EchangeType $echangeType
     * @param Dossier $dossier
     * @param $exercice
     * @return EchangeItem
     */
    public function getLastForDossier(EchangeType $echangeType, Dossier $dossier, $exercice)
    {
        return $this->createQueryBuilder('ei')
            ->join('ei.echange','e')
            ->where('e.dossier = :dossier')
            ->andWhere('e.exercice = :exercice')
            ->andWhere('ei.supprimer <> 1')
            ->andWhere('e.echangeType = :echangeType')
            ->setParameters([
                'dossier' => $dossier,
                'exercice' => $exercice,
                'echangeType' => $echangeType
            ])
            ->orderBy('ABS(ei.numero)','DESC')
            ->addOrderBy('ei.id','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param EchangeType $echangeType
     * @return EchangeItem[]
     */
    public function getLasts(EchangeType $echangeType)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $dateNow = new \DateTime();
        $exerciceNow = intval($dateNow->format('Y'));

        $req = '
            SELECT ei.id AS id, d.id AS d_id, e.exercice AS exercice FROM echange_item ei
            JOIN echange e ON (ei.echange_id = e.id)
            JOIN dossier d ON (e.dossier_id = d.id)
            JOIN site s ON (d.site_id = s.id)
            JOIN client c ON (s.client_id = c.id)
            WHERE e.echange_type_id = :echange_type_id 
            AND c.nom NOT LIKE :demo 
            AND e.exercice > :exercice 
            ORDER BY d.id, e.exercice, abs(ei.numero) desc, ei.id DESC;        
        ';

        $prep = $pdo->prepare($req);
        $prep->execute([
            'echange_type_id' => $echangeType->getId(),
            'demo' => 'DEMO%',
            'exercice' => $exerciceNow - 2
        ]);
        $reqs = $prep->fetchAll();

        $ids = [];
        $dossierExercices = [];
        foreach ($reqs as $re)
        {
            $key = $re->d_id . '_' . $re->exercice;
            if (!in_array($key,$dossierExercices))
            {
                $ids[] = $re->id;
                $dossierExercices[] = $key;
            }
        }

        /** @var EchangeItem[] $echangeItems */
        $echangeItems = $this->getEntityManager()->getRepository('AppBundle:EchangeItem')
            ->createQueryBuilder('ei')
            ->where('ei.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        return $echangeItems;
    }
}