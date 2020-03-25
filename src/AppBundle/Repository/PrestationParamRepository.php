<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AideAssocie;
use AppBundle\Entity\MenuUtilisateur;
use Doctrine\ORM\EntityRepository;
use AppBundle\Controller\Boost;
use AppBundle\Functions\CustomPdoConnection;

class PrestationParamRepository extends  EntityRepository
{
	public function getNbPrestation($param,$dossier,$exercice)
	{
		$con = new CustomPdoConnection();
        $pdo = $con->connect();

        $dossier  = Boost::deboost($dossier,$this);

        // if ($dossier == "") {
        	# code...
        // } else {
        	// mijer ny journal

        	$innerJournal = "";
        	$whereJournal = "";

        	$journal_ids = json_decode($param->getJournalIds());

        	if (!empty($journal_ids) && $journal_ids[0] != 0 && $journal_ids != "") {
        		$innerJournal .= "	inner join journal_dossier jd on (e.journal_dossier_id = jd.id)
									inner join journal j on (jd.journal_id = j.id)";
				if (count($journal_ids) == 1) {
					$whereJournal .= "	and j.id = " . $journal_ids[0];
				} else {
					$whereJournal .= " and j.id in (";

        			$whereJournal .= implode(",", $journal);

					
					// foreach ($journal_ids as $key => $journal) {

					// 	if ($key == 0) {
					// 		$whereJournal .= "	j.id = " . $journal;	
					// 	} else {
					// 		$whereJournal .= "	or j.id = " . $journal;	
					// 	}

					// }
					$whereJournal .= ")";
				}
        	}

        	$innerSource = "";
        	$whereSource = "";

        	$sources = json_decode($param->getSourceImageIds());


        	if (!empty($sources) && $sources[0] != 0 && $sources != "") {
        		$innerSource .= "	left join image i on (e.image_id = i.id)
        							left join source_image si on (i.source_image_id=si.id)";

        		if (count($sources) == 1) {
					$whereSource .= "	and si.id = " . $sources[0];
        		} else {
        			$whereSource .= " and si.id in (";

        			$whereSource .= implode(",", $sources);


					// foreach ($sources as $key => $source) {

					// 	if ($key == 0) {
					// 		$whereSource .= "	si.id = " . $source;	
					// 	} else {
					// 		$whereSource .= "	or si.id = " . $source;	
					// 	}

					// }
					$whereSource .= ")";
        		}
        	}



        	$query = " select date_format(e.date_ecr,'%Y-%m') as date_ecr, date_format(e.date_ecr,'%m') as mois, date_format(e.date_ecr,'%Y') as annee, d.id as dossier_id, d.cloture as cloture
                       from ecriture e
                       " . $innerJournal . $innerSource . " 
                       inner join dossier d on (e.dossier_id=d.id)
                       where d.id = :dossier
                       and e.exercice = :exercice";

            $query .= $whereJournal;
            $query .= $whereSource;

            if ($param->getUnite() == 0) {
            	$query .= " group by date_format(e.date_ecr,'%Y-%m')";
            }


            $prep = $pdo->prepare($query);


            $params = array(
                'dossier' => $dossier,
                'exercice' => $exercice
            );

        // }

        $prep->execute($params);

        $results = $prep->fetchAll();

        return count($results);

        // $mois = array();

        // foreach ($results as $value) {

        //     $debutFin = $this->beginEnd($exercice,$value->cloture);

        //     $moisCloture = $this->getBetweenDate($debutFin);

        //     if (!in_array($value->date_ecr, $mois)) {
        //         array_push($mois, $value->date_ecr);
        //     }

        // }

        // return count($mois);


	}

	public function beginEnd($exercice, $cloture)
    {
        if ($cloture < 9) {
            $debutMois = ($exercice - 1) . '-0' . ($cloture + 1) . '-01';
        } else if ($cloture >= 9 and $cloture < 12) {
            $debutMois = ($exercice - 1) . '-' . ($cloture + 1) . '-01';
        } else {
            $debutMois = ($exercice) . '-01-01';
        }
        if ($cloture < 10) {
            $finMois = ($exercice) . '-0' . ($cloture) . '-01';
        } else {
            $finMois = ($exercice) . '-' . ($cloture) . '-01';
        }

        $result          = array();
        $result['start'] = $debutMois;
        $result['end']   = $finMois;

        return $result;

    }

    protected function getBetweenDate($debutFin)
    {
        $time1  = strtotime($debutFin['start']);
        $time2  = strtotime($debutFin['end']);
        $my     = date('mY', $time2);
        $months = array(date('Y-m', $time1));
        while ($time1 < $time2) {
            $time1 = strtotime(date('Y-m', $time1) . ' +1 month');
            if (date('mY', $time1) != $my && ($time1 < $time2))
                $months[] = date('Y-m', $time1);
        }
        $months[] = date('Y-m', $time2);
        return $months;
    }
}