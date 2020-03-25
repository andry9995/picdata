<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 15/12/2016
 * Time: 09:51
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\FactModele;
use AppBundle\Entity\FactTarif;
use AppBundle\Entity\FactTarifDossier;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\FactAnnee;

class FactTarifDossierRepository extends EntityRepository
{
    public function getAllTarifByDossierAndAnnee(Dossier $dossier, FactAnnee $annee)
    {
        $tarifs = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifDossier')
            ->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('dossier = :dossier')
            ->innerJoin('t.factPrestationDossier', 'factPrestationDossier')
            ->addSelect('factPrestationDossier')
            ->innerJoin('factPrestationDossier.factPrestation', 'factPrestation')
            ->addSelect('factPrestation')
            ->innerJoin('factPrestation.factDomaine', 'factDomaine')
            ->addSelect('factDomaine')
            ->innerJoin('t.factAnnee', 'factAnnee')
            ->addSelect('factAnnee')
            ->andWhere('factAnnee = :annee')
            ->setParameters(array(
                'dossier' => $dossier,
                'annee' => $annee,
            ))
            ->orderBy('factDomaine.code')
            ->addOrderBy('factPrestation.code')
            ->addOrderBy('factPrestation.libelle')
            ->getQuery()
            ->getResult();
        return $tarifs;
    }

    public function getPrestationTarifDossierManquant(Dossier $dossier, FactAnnee $annee)
    {
        $tarifs = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifDossier')
            ->getAllTarifByDossierAndAnnee($dossier, $annee);
        $prestations_dossier = [0];

        /* @var FactTarifDossier $tarif */
        foreach ($tarifs as $tarif) {
            $prestations_dossier[] = $tarif
                ->getFactPrestationDossier()
                ->getFactPrestation()
                ->getId();
        }

        $qb2 = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestationDossier')
            ->createQueryBuilder('p');
        $prestations = $qb2
            ->select('p')
            ->innerJoin('p.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('dossier = :dossier')
            ->andWhere('p.status = :status')
            ->innerJoin('p.factPrestation', 'factPrestation')
            ->addSelect('factPrestation')
            ->andWhere($qb2->expr()->notIn('factPrestation.id', ':prestations_dossier'))
            ->setParameters(array(
                'dossier' => $dossier,
                'status' => true,
                'prestations_dossier' => $prestations_dossier,
            ))
            ->getQuery()
            ->getResult();

        return $prestations;

    }

    public function completeTarifDossier(Dossier $dossier, FactAnnee $annee, FactModele $modele)
    {
        $manquants = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifDossier')
            ->getPrestationTarifDossierManquant($dossier, $annee);
        $annee_n1 = $this->getEntityManager()
            ->getRepository('AppBundle:FactAnnee')
            ->findOneBy(array(
                'annee' => $annee->getAnnee() - 1,
            ));

        /* Tarif Dossier n-1 */
        $tarifs_n_1 = [];
        if ($annee_n1) {
            $t_n_1 = $this->getEntityManager()
                ->getRepository('AppBundle:FactTarifDossier')
                ->getAllTarifByDossierAndAnnee($dossier, $annee_n1);
            /* @var FactTarifDossier $item */
            foreach ($t_n_1 as $item) {
                $tarifs_n_1[$item->getFactPrestationDossier()->getFactPrestation()->getId()] = $item;
            }
        }

        /* Tafif general annee n */
        $tarifs_g_n = [];
        $t_g_n = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarif')
            ->getAllTarifByAnnee($annee, $modele);
        /* @var FactTarif $item */
        foreach ($t_g_n as $item) {
            $tarifs_g_n[$item->getFactPrestation()->getId()] = $item;
        }

        $em = $this->getEntityManager();

        if (count($manquants) > 0) {
            /* @var \AppBundle\Entity\FactPrestationDossier $manquant */
            foreach ($manquants as $manquant) {
                $tarif = new FactTarifDossier();
                $tarif
                    ->setDossier($dossier)
                    ->setFactPrestationDossier($manquant)
                    ->setFactAnnee($annee)
                    ->setFactModele($modele);
                if (isset($tarifs_n_1[$manquant->getFactPrestation()->getId()])) {
                    /* @var FactTarifDossier $tmp_tarif */
                    $tmp_tarif = $tarifs_n_1[$manquant->getFactPrestation()->getId()];
                    $tarif
                        ->setShowQuantite($tmp_tarif->getShowQuantite())
                        ->setFormule($tmp_tarif->getFormule())
                        ->setPuFixe($tmp_tarif->getPuFixeIndice())
                        ->setPuVariable($tmp_tarif->getPuVariableIndice());
                } elseif (isset($tarifs_g_n[$manquant->getFactPrestation()->getId()])) {
                    /* @var FactTarifDossier $tmp_tarif */
                    $tmp_tarif = $tarifs_g_n[$manquant->getFactPrestation()->getId()];
                    $tarif
                        ->setShowQuantite($tmp_tarif->getShowQuantite())
                        ->setFormule($tmp_tarif->getFormule())
                        ->setPuFixe($tmp_tarif->getPuFixe())
                        ->setPuVariable($tmp_tarif->getPuVariable())
                        ->setPuFixeIndice($tmp_tarif->getPuFixeIndice())
                        ->setPuVariableIndice($tmp_tarif->getPuVariableIndice());

                }
                $em->persist($tarif);
            }
        }

        /* MAJ modele */
        $tarifs = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifDossier')
            ->getAllTarifByDossierAndAnnee($dossier, $annee);
        if ($tarifs && count($tarifs) > 0) {
            /* @var FactTarifDossier $tarif */
            foreach ($tarifs as $tarif) {
                $tarif->setFactModele($modele);
            }
        }
        $em->flush();
        return true;
    }

    public function calculerPu(Dossier $dossier, FactAnnee $annee, FactModele $modele)
    {
        $em = $this->getEntityManager();

        $annee_n_1 = $this->getEntityManager()
            ->getRepository('AppBundle:FactAnnee')
            ->findOneBy(array(
                'annee' => $annee->getAnnee() - 1
            ));
        $tarifs_n_1 = [];
        if ($annee_n_1) {
            $this->getEntityManager()
                ->getRepository('AppBundle:FactTarifDossier')
                ->completeTarifDossier($dossier, $annee_n_1, $modele);
            $t_n_1 = $this->getEntityManager()
                ->getRepository('AppBundle:FactTarifDossier')
                ->getAllTarifByDossierAndAnnee($dossier, $annee_n_1);
            /* @var FactTarifDossier $item */
            foreach ($t_n_1 as $item) {
                $tarifs_n_1[$item->getFactPrestationDossier()->getFactPrestation()->getId()] = $item;
            }
        }

        $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifDossier')
            ->completeTarifDossier($dossier, $annee, $modele);

        $tarifs = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifDossier')
            ->getAllTarifByDossierAndAnnee($dossier, $annee);

        /** @var FactIndice $the_indice */
        $the_indice = $this->getEntityManager()
            ->getRepository('AppBundle:FactIndice')
            ->getIndiceByAnnee($annee);

        /* @var FactTarifDossier $tarif */
        foreach ($tarifs as $tarif) {
            $the_tarif = $this->getEntityManager()
                ->getRepository('AppBundle:FactTarifDossier')
                ->find($tarif->getId());
            if (isset($tarifs_n_1[$tarif->getFactPrestationDossier()->getFactPrestation()->getId()])) {
                /* @var FactTarifDossier $tarif_n_1 */
                $tarif_n_1 = $tarifs_n_1[$tarif->getFactPrestationDossier()->getFactPrestation()->getId()];
                $pu_fixe_n_1 = $tarif_n_1->getPuFixe();
                $pu_fixe_indice_n_1 = $tarif_n_1->getPuFixeIndice();
                $pu_var_n_1 = $tarif_n_1->getPuVariable();
                $pu_var_indice_n_1 = $tarif_n_1->getPuVariableIndice();
                $indice = $the_indice->getIndice();
                /* Si tarif (n-1) existe : Pu (n) = Pu_indice(n-1) */
                if ($pu_fixe_indice_n_1 && $pu_fixe_indice_n_1 != 0) {
                    if (!$the_tarif->getPuFixe() || $the_tarif->getPuFixe() == 0) {
                        $the_tarif->setPuFixe($pu_fixe_indice_n_1);
                    }
                    if (!$the_tarif->getPuFixeIndice() || $the_tarif->getPuFixeIndice() == 0) {
                        $the_tarif->setPuFixeIndice($pu_fixe_indice_n_1 * $indice);
                    }
                } else {
                    if (!$the_tarif->getPuFixe() || $the_tarif->getPuFixe() == 0) {
                        $the_tarif->setPuFixe($pu_fixe_n_1 * $indice);
                    }
                }
                if ($pu_var_indice_n_1 && $pu_var_indice_n_1 != 0) {
                    if (!$the_tarif->getPuVariable() || $the_tarif->getPuVariable() == 0) {
                        $the_tarif->setPuVariable($pu_var_indice_n_1);
                    }
                    if (!$the_tarif->getPuVariableIndice() || $the_tarif->getPuVariableIndice() == 0) {
                        $the_tarif->setPuVariableIndice($pu_var_indice_n_1 * $indice);
                    }
                } else {
                    if (!$the_tarif->getPuVariable() || $the_tarif->getPuVariable() == 0) {
                        $the_tarif->setPuVariable($pu_var_n_1 * $indice);
                    }
                }
            } else {
                /* Si tarif (n-1) n'existe pas: Pu_indice_n_1 = Pu_n_1 */
                $the_tarif->setPuFixeIndice($the_tarif->getPuFixe());
                $the_tarif->setPuVariableIndice($the_tarif->getPuVariable());
            }

            $em->flush();
        }

        return true;
    }
}