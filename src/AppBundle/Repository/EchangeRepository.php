<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 02/03/2017
 * Time: 13:50
 */

namespace AppBundle\Repository;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Echange;
use AppBundle\Entity\EchangeItem;


use AppBundle\Entity\EchangeType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Functions\CustomPdoConnection;

class EchangeRepository extends EntityRepository
{
    /**
     * @param EchangeType $echangeType
     * @param Dossier $dossier
     * @param $exercice
     * @return Echange
     */
    public function getEchangeByDossierExercice(EchangeType $echangeType, Dossier $dossier, $exercice)
    {
        $echange = $this->createQueryBuilder('ed')
            ->where('ed.dossier = :dossier')
            ->andWhere('ed.exercice = :exercice')
            ->andWhere('ed.echangeType = :echangeType')
            ->setParameters([
                'dossier' => $dossier,
                'exercice' => $exercice,
                'echangeType' => $echangeType
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$echange)
        {
            $echange = new Echange();
            $echange
                ->setDossier($dossier)
                ->setExercice($exercice)
                ->setEchangeType($echangeType)
                ->setDateEnvoi(new \DateTime());

            $this->getEntityManager()->persist($echange);
            $this->getEntityManager()->flush();
        }

        return $echange;
    }

    /**
     * @param $param
     * @return array drt
     */
    public function getDrt( $param )
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        if($param['statut'] == 5 || $param['statut'] == 2 || $param['statut'] == 7) $param['statut'] = 0;
        if($param['statut'] != 3){ //3 => toute
            $statutDrt = ' AND ei.status = ';
            $statutDrt .= $param['statut'].' ';
        }else{
            $statutDrt = ' ';
        }

        if($param['dossier'] == 0){
            $dossierDrt = '';
        }else{
            $dossierDrt = ' AND d.id = ';
            $dossierDrt .= $param['dossier'].' ';
        }

        switch ($param['cas']) {
            case 1:
                $chrono = "AND e.date_envoi = :chrono ";
                break;
            case 5:
                $chrono = " ";
                break;
            case 6:
                $chrono = " AND (e.exercice = :exercice_n ";
                $chrono .= " OR e.exercice = :exercice_n1 ) ";
                break;
            default:
                $chrono = "AND e.date_envoi >= ";
                $chrono .= ":dateDeb";
                $chrono .= " AND e.date_envoi <= ";
                $chrono .= ":dateFin ";
        }

        $query = "SELECT ABS(ei.numero) as rang_ei, ep.id as echange_reponse_id, ei.id as echange_item_id, et.nom as type, e.exercice, date_format(e.date_envoi,'%d/%m/%Y') as echange_date_envoi, ei.numero, d.nom as dossier, date_format(ep.date_envoi,'%d/%m/%Y') as reponse_date_envoi, ei.status, c.nom as client, d.nom as dossier, d.id as dossierId, ep.numero as numero_reponse, ep.message as message_rdrt, ei.date_creation, date_format(ei.date_creation,'%d/%m/%Y') as date_creation_ei, ei.message as message_drt, ei.telecharger,
            (CASE ei.status 
               WHEN 0 THEN 'Ouverte'
               ELSE 'Clôturée'
             END) as nom_statut
            FROM echange e
            INNER JOIN echange_type et on (et.id = e.echange_type_id)
            INNER JOIN echange_item ei on (ei.echange_id = e.id)
            INNER JOIN dossier d on (d.id = e.dossier_id)
            INNER JOIN site s on (s.id = d.site_id)
            INNER JOIN client c on (c.id = s.client_id)
            LEFT JOIN echange_reponse ep on (ep.echange_item_id = ei.id)
            WHERE e.exercice = :exercice
            AND c.id = ".$param['client']."
            AND et.id = ".$param['echangeType']."
            ".$chrono."
            ".$statutDrt."
            ".$dossierDrt."
            AND ei.supprimer = 0
            ORDER BY d.nom, rang_ei, nom_statut, ep.numero ASC";
        $prep = $pdo->prepare($query);
        switch ($param['cas']) {
            case 1:
                $now = $param['aujourdhui'];
                $prep->execute(array(
                    'exercice' => $param['exercice'],
                    'chrono' => $now,
                ));
                break;
            case 5:
                $prep->execute(array(
                    'exercice' => $param['exercice'],
                ));
                break;
            case 6:
                $prep->execute(array(
                    'exercice' => $param['exercice'],
                    'exercice_n' => $param['exercice'],
                    'exercice_n1' => $param['exercice'] - 1,
                ));
                break;
            default:
                $dateDeb = $param['dateDeb'];
                $dateFin = $param['dateFin'];
                $prep->execute(array(
                    'exercice' => $param['exercice'],
                    'dateDeb' => $dateDeb,
                    'dateFin' => $dateFin,
                ));
        }
        return $prep->fetchAll();
    }
}