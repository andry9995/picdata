<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 15/12/2016
 * Time: 09:32
 */

namespace AppBundle\Repository;

use AppBundle\Entity\FactAnnee;
use AppBundle\Entity\FactIndice;
use AppBundle\Entity\FactModele;
use AppBundle\Entity\FactTarif;
use Doctrine\ORM\EntityRepository;

class FactTarifRepository extends EntityRepository
{
    public function getAllTarifByAnnee(FactAnnee $annee, FactModele $modele)
    {
        $tarifs = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarif')
            ->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.factPrestation', 'factPrestation')
            ->addSelect('factPrestation')
            ->innerJoin('factPrestation.factDomaine', 'factDomaine')
            ->addSelect('factDomaine')
            ->innerJoin('t.factAnnee', 'factAnnee')
            ->addSelect('factAnnee')
            ->innerJoin('t.factModele', 'factModele')
            ->addSelect('factModele')
            ->where('factAnnee = :annee')
            ->andWhere('factModele = :modele')
            ->setParameters(array(
                'annee' => $annee,
                'modele' => $modele,
            ))
            ->orderBy('factDomaine.code')
            ->addOrderBy('factPrestation.code')
            ->addOrderBy('factPrestation.libelle')
            ->getQuery()
            ->getResult();
        return $tarifs;
    }

    public function getPrestationTarifGeneralManquant(FactAnnee $annee, FactModele $modele)
    {
        $tarifs = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarif')
            ->getAllTarifByAnnee($annee, $modele);
        $prestations_gen = [0];

        /* @var \AppBundle\Entity\FactTarif $tarif */
        foreach ($tarifs as $tarif) {
            $prestations_gen[] = $tarif->getFactPrestation()->getId();
        }

        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestation')
            ->createQueryBuilder('p');
        $prestations = $qb
            ->select('p')
            ->where($qb->expr()->notIn('p.id', ':prestations_gen'))
            ->setParameter('prestations_gen', $prestations_gen)
            ->getQuery()
            ->getResult();

        return $prestations;

    }

    /**
     * completer les tarifs manquants selon les prestations générales
     *
     * @param FactAnnee $annee
     * @return bool
     */
    public function completeTarifGeneral(FactAnnee $annee, FactModele $modele)
    {
        $manquants = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarif')
            ->getPrestationTarifGeneralManquant($annee, $modele);
        $annee_n1 = $this->getEntityManager()
            ->getRepository('AppBundle:FactAnnee')
            ->findOneBy(array(
                'annee' => $annee->getAnnee() - 1,
            ));
        $tarifs_n_1 = [];
        if ($annee_n1) {
            $t_n_1 = $this->getEntityManager()
                ->getRepository('AppBundle:FactTarif')
                ->getAllTarifByAnnee($annee_n1, $modele);
            /* @var FactTarif $item */
            foreach ($t_n_1 as $item) {
                $tarifs_n_1[$item->getFactPrestation()->getId()] = $item;
            }
        }
        if (count($manquants) > 0) {
            $em = $this->getEntityManager();

            /* @var \AppBundle\Entity\FactPrestation $manquant */
            foreach ($manquants as $manquant) {
                $tarif = new FactTarif();
                $tarif
                    ->setFactPrestation($manquant)
                    ->setFactAnnee($annee)
                    ->setFactModele($modele);
                if (isset($tarifs_n_1[$manquant->getId()])) {
                    /* @var FactTarif $tmp_tarif */
                    $tmp_tarif = $tarifs_n_1[$manquant->getId()];
                    $tarif
                        ->setShowQuantite($tmp_tarif->getShowQuantite())
                        ->setFormule($tmp_tarif->getFormule())
                        ->setPuFixe($tmp_tarif->getPuFixeIndice())
                        ->setPuVariable($tmp_tarif->getPuVariableIndice());
                }
                $em->persist($tarif);
            }
            $em->flush();
        }
        return true;
    }

    public function calculerPu(FactAnnee $annee, FactModele $modele, $recalculer = false)
    {
        $em = $this->getEntityManager();

        $annee_n_1 = $this->getEntityManager()
            ->getRepository('AppBundle:FactAnnee')
            ->findOneBy(array(
                'annee' => $annee->getAnnee() - 1
            ));
        $tarifs_n_1 = [];
        if ($annee_n_1) {
//            $this->getEntityManager()
//                ->getRepository('AppBundle:FactTarif')
//                ->completeTarifGeneral($annee_n_1, $modele);
            $t_n_1 = $this->getEntityManager()
                ->getRepository('AppBundle:FactTarif')
                ->getAllTarifByAnnee($annee_n_1, $modele);
            /* @var FactTarif $item */
            foreach ($t_n_1 as $item) {
                $tarifs_n_1[$item->getFactPrestation()->getId()] = $item;
            }
        }

        $this->getEntityManager()
            ->getRepository('AppBundle:FactTarif')
            ->completeTarifGeneral($annee, $modele);

        $tarifs = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarif')
            ->getAllTarifByAnnee($annee, $modele);

        /** @var FactIndice $the_indice */
        $the_indice = $this->getEntityManager()
            ->getRepository('AppBundle:FactIndice')
            ->getIndiceByAnnee($annee);

        /* @var FactTarif $tarif */
        foreach ($tarifs as $tarif) {
            $the_tarif = $this->getEntityManager()
                ->getRepository('AppBundle:FactTarif')
                ->find($tarif->getId());
            if (isset($tarifs_n_1[$tarif->getFactPrestation()->getId()])) {
                /* @var FactTarif $tarif_n_1 */
                $tarif_n_1 = $tarifs_n_1[$tarif->getFactPrestation()->getId()];
                $pu_fixe_n_1 = $tarif_n_1->getPuFixe();
                $pu_fixe_indice_n_1 = $tarif_n_1->getPuFixeIndice();
                $pu_var_n_1 = $tarif_n_1->getPuVariable();
                $pu_var_indice_n_1 = $tarif_n_1->getPuVariableIndice();
                $indice = $the_indice->getIndice();
                /* Si tarif (n-1) existe : Pu (n) = Pu_indice(n-1) */
                if ($pu_fixe_indice_n_1 && $pu_fixe_indice_n_1 != 0) {
                    if ($recalculer) {
                        $the_tarif->setPuFixe($pu_fixe_indice_n_1);
                        $the_tarif->setPuFixeIndice($pu_fixe_indice_n_1 * $indice);
                    } else {
                        if (!$the_tarif->getPuFixe() || $the_tarif->getPuFixe() == 0) {
                            $the_tarif->setPuFixe($pu_fixe_indice_n_1);
                        }
                        if (!$the_tarif->getPuFixeIndice() || $the_tarif->getPuFixeIndice() == 0) {
                            $the_tarif->setPuFixeIndice($pu_fixe_indice_n_1 * $indice);
                        }
                    }
                } else {
                    if ($recalculer) {
                        $the_tarif->setPuFixe($pu_fixe_n_1 * $indice);
                    } else {
                        if (!$the_tarif->getPuFixe() || $the_tarif->getPuFixe() == 0) {
                            $the_tarif->setPuFixe($pu_fixe_n_1 * $indice);
                        }
                    }
                }
                if ($pu_var_indice_n_1 && $pu_var_indice_n_1 != 0) {
                    if ($recalculer) {
                        $the_tarif->setPuVariable($pu_var_indice_n_1);
                        $the_tarif->setPuVariableIndice($pu_var_indice_n_1 * $indice);
                    } else {
                        if (!$the_tarif->getPuVariable() || $the_tarif->getPuVariable() == 0) {
                            $the_tarif->setPuVariable($pu_var_indice_n_1);
                        }
                        if (!$the_tarif->getPuVariableIndice() || $the_tarif->getPuVariableIndice() == 0) {
                            $the_tarif->setPuVariableIndice($pu_var_indice_n_1 * $indice);
                        }
                    }
                } else {
                    if ($recalculer) {
                        $the_tarif->setPuVariable($pu_var_n_1 * $indice);
                    } else {
                        if (!$the_tarif->getPuVariable() || $the_tarif->getPuVariable() == 0) {
                            $the_tarif->setPuVariable($pu_var_n_1 * $indice);
                        }
                    }
                }
            } else {
                if (!$recalculer) {
                    /* Si tarif (n-1) n'existe pas: Pu_indice_n_1 = Pu_n_1 */
                    if ((!$the_tarif->getPuFixeIndice() || $the_tarif->getPuFixeIndice() == 0) &&
                        $the_tarif->getPuFixe() && $the_tarif->getPuFixe() != 0) {
                        $the_tarif->setPuFixeIndice($the_tarif->getPuFixe());
                    }
                    if ((!$the_tarif->getPuVariableIndice() || $the_tarif->getPuVariableIndice() == 0) &&
                        $the_tarif->getPuVariable() && $the_tarif->getPuVariable() != 0) {
                        $the_tarif->setPuVariableIndice($the_tarif->getPuVariable());
                    }
                }
            }

            $em->flush();
        }

        return true;
    }
}