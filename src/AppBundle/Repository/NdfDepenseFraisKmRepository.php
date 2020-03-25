<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 22/01/2018
 * Time: 09:39
 */

namespace AppBundle\Repository;


use AppBundle\Entity\NdfDepenseFraisKm;
use AppBundle\Entity\NdfNote;
use DateTime;
use Doctrine\ORM\EntityRepository;

class NdfDepenseFraisKmRepository extends EntityRepository
{
    public function getDepenseFraisKmByFilter($dossier,$titre,$remboursable,$facturable,$dateDu,$dateAu,$sousCategorie,$affaire,$note){


        $depenses  = $this->getEntityManager()
            ->getRepository('AppBundle:NdfDepenseFraisKm')
            ->createQueryBuilder('d')
            ->where('d.dossier = :dossier')
            ->setParameter(':dossier', $dossier);


        if($titre != "") {
            $depenses
                ->andWhere('d.titre like :titre')
                ->setParameter(':titre', '%' . $titre . '%');

            if ($facturable != 2) {

                $depenses->andWhere('d.facturable = :facturable')
                    ->setParameter(':facturable', $facturable);

                if (null !== $affaire) {
                    $depenses->andWhere('d.ndfAffaire = :affaire')
                        ->setParameter(':affaire', $affaire);

                    if (null !== $note) {
                        $depenses->andWhere('d.ndfNote = :note')
                            ->setParameter(':note', $note);
                    }
                } else if (null !== $note) {
                    $depenses->andWhere('d.ndfNote = :note')
                        ->setParameter(':note', $note);
                }
            }
            else{
                if (null !== $affaire) {
                    $depenses->andWhere('d.ndfAffaire = :affaire')
                        ->setParameter(':affaire', $affaire);

                    if (null !== $note) {
                        $depenses->andWhere('d.ndfNote = :note')
                            ->setParameter(':note', $note);
                    }
                } else if (null !== $note) {
                    $depenses->andWhere('d.ndfNote = :note')
                        ->setParameter(':note', $note);
                }
            }

        }
        else{
            if ($facturable != 2) {

                $depenses->andWhere('d.facturable = :facturable')
                    ->setParameter(':facturable', $facturable);

                if (null !== $affaire) {
                    $depenses->andWhere('d.ndfAffaire = :affaire')
                        ->setParameter(':affaire', $affaire);

                    if (null !== $note) {
                        $depenses->andWhere('d.ndfNote = :note')
                            ->setParameter(':note', $note);
                    }
                } else if (null !== $note) {
                    $depenses->andWhere('d.ndfNote = :note')
                        ->setParameter(':note', $note);
                }
            }
            else{
                if (null !== $affaire) {
                    $depenses->andWhere('d.ndfAffaire = :affaire')
                        ->setParameter(':affaire', $affaire);

                    if (null !== $note) {
                        $depenses->andWhere('d.ndfNote = :note')
                            ->setParameter(':note', $note);
                    }
                } else if (null !== $note) {
                    $depenses->andWhere('d.ndfNote = :note')
                        ->setParameter(':note', $note);
                }
            }
        }

        if(null !== $dateDu){
            $depenses->andWhere('d.periodeDeb >= :dateDu')
                ->setParameter(':dateDu', $dateDu);

            if(null !== $dateAu){
                $depenses->andWhere('d.periodeFin <= :dateAu')
                    ->setParameter(':dateAu', $dateAu);
            }

        }
        else{
            if(null !== $dateAu){
                $depenses->andWhere('d.periodeFin <= :dateAu')
                    ->setParameter(':dateAu', $dateAu);
            }
        }


        return $depenses->getQuery()->getResult();
    }

    public function isInPeriode($note, $dateDebut, $dateFin)
    {
        $inPeriod = true;

        /** @var NdfNote $note */
        if (null !== $note) {
            if (null !== $note->getAnnee()) {
                if (null !== $note->getMois()) {

                    $inPeriod = false;
                    $debut = false;
                    $fin = false;

                    $mois = explode(",", $note->getMois());

                    $moisMax = max($mois);
                    $moisMin = min($mois);

                    if ($dateDebut != '') {

                        $date_array = explode("/", $dateDebut);

                        if ($date_array[2] == $note->getAnnee()) {
                            if ($moisMin <= (int)$date_array[1] &&
                                $moisMax >= (int)$date_array[1]) {
                                $debut = true;
                            }
                        }
                    }

                    if ($dateFin != '') {

                        $date_array = explode("/", $dateFin);

                        if ($date_array[2] == $note->getAnnee()) {
                            if ($moisMax >= (int)$date_array[1] &&
                                $moisMin <= (int)$date_array[1]) {
                                $fin = true;
                            }
                        }
                    }

                    if ($debut && $fin) {
                        $inPeriod = true;
                    }
                }
            }
        }
        return $inPeriod;
    }

    public function getListDepenseFraisKmByNdfUtilisateur($ndfUtilisateur, $exerice){
        /** @var NdfDepenseFraisKm[] $depensesFks */
        $depensesFks =  $this
            ->createQueryBuilder('dfk')
            ->innerJoin('dfk.ndfNote', 'ndfNote')
            ->where('ndfNote.ndfUtilisateur = :utilisiateur')
            ->andWhere('ndfNote.annee = :annee')
            ->setParameter('utilisiateur', $ndfUtilisateur)
            ->setParameter('annee', $exerice)
            ->select('dfk')
            ->getQuery()
            ->getResult();

        return $depensesFks;
    }

    public function getRegulDepenseFraisKmByNdfUtilisateur($ndfUtilisateur, $exercice){

        $periodeDebut = DateTime::createFromFormat('d/m/Y', '01/01/'.$exercice)->setTime(0,0,0);
        $periodeFin = DateTime::createFromFormat('d/m/Y', '31/12/'.$exercice)->setTime(0,0,0);

        /** @var NdfDepenseFraisKm $depensesFks */
        $depensesFks = $this
            ->createQueryBuilder('dfk')
            ->innerJoin('dfk.ndfNote', 'ndfNote')
            ->where('dfk.regul = 1')
            ->andWhere('ndfNote.ndfUtilisateur = :utilisateur')
            ->andWhere('dfk.periodeDeb = :periodeDebut')
            ->andWhere('dfk.periodeFin = :periodeFin')
            ->setParameter('utilisateur', $ndfUtilisateur)
            ->setParameter('periodeDebut', $periodeDebut)
            ->setParameter('periodeFin', $periodeFin)
            ->select('dfk')
            ->getQuery()
            ->getResult();

        return $depensesFks;
    }
}