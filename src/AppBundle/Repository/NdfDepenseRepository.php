<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 12/01/2018
 * Time: 08:31
 */

namespace AppBundle\Repository;


use AppBundle\Entity\NdfDepense;
use AppBundle\Entity\NdfNote;
use Doctrine\ORM\EntityRepository;

class NdfDepenseRepository extends EntityRepository
{

    public function getDepenseByFilter($dossier,$titre,$remboursable,$facturable,$dateDu,$dateAu,$sousCategorie,$affaire,$note){


        $depenses  = $this->getEntityManager()
            ->getRepository('AppBundle:NdfDepense')
            ->createQueryBuilder('d')
            ->where('d.dossier = :dossier')
            ->setParameter(':dossier', $dossier);


        if($titre != ""){
           $depenses
                ->andWhere('d.titre like :titre')
                ->setParameter(':titre', '%'.$titre.'%');

            if($remboursable != 2){
                $depenses->andWhere('d.remboursable = :remboursable')
                    ->setParameter(':remboursable', $remboursable);

                if($facturable != 2){

                    $depenses->andWhere('d.facturable = :facturable')
                        ->setParameter(':facturable', $facturable);

                    if(null !== $sousCategorie){
                        $depenses->andWhere('d.ndfSouscategorieDossier = :sousCategorie')
                            ->setParameter(':sousCategorie', $sousCategorie);

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                    else{

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                }
                else{
                    if(null !== $sousCategorie){
                        $depenses->andWhere('d.ndfSouscategorieDossier= :sousCategorie')
                            ->setParameter(':sousCategorie', $sousCategorie);

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                    else{

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                }
            }
            else{
                if($facturable != 2){

                    $depenses->andWhere('d.facturable = :facturable')
                        ->setParameter(':facturable', $facturable);

                    if(null !== $sousCategorie){
                        $depenses->andWhere('d.ndfSouscategorieDossier = :sousCategorie')
                            ->setParameter(':sousCategorie', $sousCategorie);

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                    else{

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                }
                else{
                    if(null !== $sousCategorie){
                        $depenses->andWhere('d.ndfSouscategorieDossier= :sousCategorie')
                            ->setParameter(':sousCategorie', $sousCategorie);

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                    else{

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                }
            }
        }
        else{

            if($remboursable != 2){
                $depenses->andWhere('d.remboursable = :remboursable')
                    ->setParameter(':remboursable', $remboursable);

                if($facturable != 2){

                    $depenses->andWhere('d.facturable = :facturable')
                        ->setParameter(':facturable', $facturable);

                    if(null !== $sousCategorie){
                        $depenses->andWhere('d.ndfSouscategorieDossier= :sousCategorie')
                            ->setParameter(':sousCategorie', $sousCategorie);

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                    else{

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                }
                else{
                    if(null !== $sousCategorie){
                        $depenses->andWhere('d.ndfSouscategorieDossier = :sousCategorie')
                            ->setParameter(':sousCategorie', $sousCategorie);

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                    else{

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                }
            }
            else{
                if($facturable != 2){

                    $depenses->andWhere('d.facturable = :facturable')
                        ->setParameter(':facturable', $facturable);

                    if(null !== $sousCategorie){
                        $depenses->andWhere('d.ndfSouscategorieDossier = :sousCategorie')
                            ->setParameter(':sousCategorie', $sousCategorie);

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                    else{

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                }
                else{
                    if(null !== $sousCategorie){
                        $depenses->andWhere('d.ndfSouscategorieDossier= :sousCategorie')
                            ->setParameter(':sousCategorie', $sousCategorie);

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                    else{

                        if(null !== $affaire){
                            $depenses->andWhere('d.ndfAffaire = :affaire')
                                ->setParameter(':affaire', $affaire);

                            if(null !== $note){
                                $depenses->andWhere('d.ndfNote = :note')
                                    ->setParameter(':note', $note);
                            }
                        }
                        else if(null !== $note){
                            $depenses->andWhere('d.ndfNote = :note')
                                ->setParameter(':note', $note);
                        }
                    }
                }
            }
        }

        if(null !== $dateDu){
            $depenses->andWhere('d.date >= :dateDu')
            ->setParameter(':dateDu', $dateDu);

            if(null !== $dateAu){
                $depenses->andWhere('d.date <= :dateAu')
                    ->setParameter(':dateAu', $dateAu);
            }

        }
        else{
            if(null !== $dateAu){
                $depenses->andWhere('d.date <= :dateAu')
                    ->setParameter(':dateAu', $dateAu);
            }
        }


        return $depenses->getQuery()->getResult();
    }

    /**
     * @param $depenses NdfDepense[]
     * @return array
     */
    public function getDetailsDepense($depenses)
    {
        $detailDepenses = array();
        foreach ($depenses as $depense) {
            $montantTtc = $depense->getTtc();
            $montantTva = 0;

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

                    $montantTtc = round(($montantTtc / $taux), 2);
                }
            }


            $tvaTauxs = $this->getEntityManager()
                ->getRepository('AppBundle:NdfDepenseTva')
                ->findBy(array('ndfDepense' => $depense));

            foreach ($tvaTauxs as $tvaTaux) {
                $taux = $tvaTaux->getTvaTaux()->getTaux();

                $montantTva += $montantTtc * (1 - (1/(1+$taux/100)));
            }

            $montantTva = round($montantTva, 2);

            $detailDepenses[] = array(
                'depense' => $depense,
                'montant' => $montantTtc,
                'montantTva' => $montantTva);
        }

        return $detailDepenses;
    }


    public function isInPeriode($note, $date)
    {
        $inPeriod = true;

        /** @var NdfNote $note */
        if (!is_null($note)) {
            if (!is_null($note->getAnnee())) {
                if (!is_null($note->getMois())) {

                    $inPeriod = false;

                    $mois = explode(",", $note->getMois());

                    $moisMax = max($mois);
                    $moisMin = min($mois);

                    if ($date != '') {

                        $date_array = explode("/", $date);


                        if ($date_array[2] == $note->getAnnee()) {
                            if ($moisMin <= intval($date_array[1])) {
                                if (intval($date_array[1]) <= $moisMax) {
                                    $inPeriod = true;
                                }

                            }
                        }

                    }
                }
            }
        }
        return $inPeriod;
    }




}