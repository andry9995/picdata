<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 12/01/2018
 * Time: 10:43
 */

namespace AppBundle\Repository;


use AppBundle\Entity\NdfNote;
use Doctrine\ORM\EntityRepository;

class NdfNoteRepository extends EntityRepository
{

    /**
     * @param $note
     * @return array
     */
    public function getMoisNote($note)
    {
        $mois = array();
        /** @var NdfNote $note */
        if (!is_null($note->getMois())) {
            $moisStr = array();
            if ($note->getMois() != "") {
                $moisStr = explode(',', $note->getMois());
            }

            if(count($moisStr) > 0){
                foreach ($moisStr as $ms){
                    $mois[] = intval($ms);
                }
            }
        }

        return $mois;
    }


    /**
     * @param $note NdfNote
     * @return array
     */
    public function getPeriodeNote($note){

        $mois = $this->getMoisNote($note);
        $annee = null;
        $periode = array();
        if(!is_null($note->getAnnee())){
            $annee = $note->getAnnee();
        }

        $moisStr = array(
            1 => "Janvier",
            2 => "Février",
            3 => "Mars",
            4 => "Avril",
            5 => "Mai",
            6 => "Juin",
            7 => "Juillet",
            8 => "Août",
            9 => "Septembre",
            10 => "Octobre",
            11 => "Novemebre",
            12 => "Décembre"

        );

        if(!is_null($annee) && count($mois) > 0){
            $min = $moisStr[min($mois)];
            $max = $moisStr[max($mois)];

            $periode = array(
                "du" => $min." ".$annee,
                "au" => $max." ".$annee);

        }

        return $periode;

    }

    /** @var NdfNote[] $notes
     * @return array
     */
    public function getNoteDetails($notes){

        $detailNotes = array();
        foreach ($notes as $note) {
            $depenses = $this->getEntityManager()
                ->getRepository('AppBundle:NdfDepense')
                ->findBy(array('ndfNote' => $note), array('date' => 'ASC'));

            $depenseFKs = $this->getEntityManager()
                ->getRepository('AppBundle:NdfDepenseFraisKm')
                ->findBy(array('ndfNote' => $note));

            $nbDepense = count($depenses) + count($depenseFKs);
            $totalTtc = 0;
            $totalRemboursable = 0;
            $periodeDu = null;
            $periodeAu = null;

            $periodeFKDu = null;
            $periodeFKAu = null;


            if (count($depenses) > 0) {

                $periodeDu = $depenses[0]->getDate();
                $periodeAu = $depenses[count($depenses) - 1]->getDate();

                foreach ($depenses as $depense) {
                    $depenseTtc = $depense->getTtc();

                    if (!is_null($depense->getDevise())) {
                        if ($depense->getDevise()->getId() != 1) {
                            $date = $depense->getDate()->format('Y-m-d');

                            $deviseId = $depense->getDevise()->getId();
                            $taux = 1;
                            $tauxs = $this->getEntityManager()
                                ->getRepository('AppBundle:DeviseTaux')
                                ->getTauxByDate($deviseId, $date);

                            if (count($tauxs) == 1) {
                                $taux = $tauxs[0]->taux;
                            }

                            $depenseTtc = round(($depenseTtc / $taux), 2);
                        }
                    }

                    $totalTtc += $depenseTtc;

                    if ($depense->getRemboursable() == 1) {
                        $totalRemboursable += $depenseTtc;
                    }
                }
            }


            if(count($depenseFKs) > 0){

                $periodeFKDu = $depenseFKs[0]->getPeriodeDeb();
                $periodeFKAu = $depenseFKs[count($depenseFKs) - 1]->getPeriodeDeb();


                foreach ($depenseFKs as $depenseFK) {
                    $depenseFKTtc = $depenseFK->getTtc();
                    $totalTtc += $depenseFKTtc;
                }
            }



            $perd = '';
            $pera = '';

            if(!is_null($periodeDu)){
                if(!is_null($periodeFKDu)){
                    if($periodeDu > $periodeFKDu){
                        $perd = $periodeFKDu->format('d/m/Y');
                    }
                }
                else{
                    $perd = $periodeDu->format('d/m/Y');
                }
            }
            else if(!is_null($periodeFKDu)){
                $perd = $periodeFKDu->format('d/m/Y');
            }


            if(!is_null($periodeAu)){
                if(!is_null($periodeFKAu)){
                    if($periodeAu < $periodeFKAu){
                        $pera = $periodeFKDu->format('d/m/Y');
                    }
                }
                else{
                    $pera = $periodeAu->format('d/m/Y');
                }
            }
            else if(!is_null($periodeFKAu)){
                $pera = $periodeFKAu->format('d/m/Y');
            }


            $retPeriode = '';
            if ($perd != '') {
                $retPeriode = $perd ;
            }

            if($retPeriode != '') {

                if ($pera != '') {
                    $retPeriode = $perd . ' Au ' . $pera;
                }
            }
            else{
                if($pera != ''){
                    $retPeriode = $pera;
                }
            }

            $depArr = array(
                'nbDepense' => $nbDepense,
                'totalTtc' => $totalTtc,
                'remboursable' => $totalRemboursable,
                'periode' => $retPeriode,
                'periodeDu' => $perd,
                'periodeAu' => $pera);



            $periode = $this->getPeriodeNote($note);

            $detailNotes[] = array('note' => $note, 'depense' => $depArr, 'periode' => $periode);
        }

        return $detailNotes;
    }
}