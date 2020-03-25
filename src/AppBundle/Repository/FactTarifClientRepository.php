<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 15/12/2016
 * Time: 09:47
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\FactAnnee;
use AppBundle\Entity\FactIndice;
use AppBundle\Entity\FactModele;
use AppBundle\Entity\FactTarif;
use AppBundle\Entity\FactTarifClient;
use Doctrine\ORM\EntityRepository;

class FactTarifClientRepository extends EntityRepository
{
    public function getAllTarifByClientAndAnnee(Client $client, FactAnnee $annee)
    {
        $tarifs = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifClient')
            ->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.client', 'client')
            ->addSelect('client')
            ->where('client = :client')
            ->innerJoin('t.factPrestationClient', 'factPrestationClient')
            ->addSelect('factPrestationClient')
            ->innerJoin('factPrestationClient.factPrestation', 'factPrestation')
            ->addSelect('factPrestation')
            ->innerJoin('factPrestation.factDomaine', 'factDomaine')
            ->addSelect('factDomaine')
            ->innerJoin('t.factAnnee', 'factAnnee')
            ->addSelect('factAnnee')
            ->andWhere('factAnnee = :annee')
            ->setParameters(array(
                'client' => $client,
                'annee' => $annee,
            ))
            ->orderBy('factDomaine.code')
            ->addOrderBy('factPrestation.code')
            ->addOrderBy('factPrestation.libelle')
            ->getQuery()
            ->getResult();
        return $tarifs;
    }

    public function getPrestationTarifClientManquant(Client $client, FactAnnee $annee)
    {
        $tarifs = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifClient')
            ->getAllTarifByClientAndAnnee($client, $annee);
        $prestations_client = [0];

        /* @var FactTarifClient $tarif */
        foreach ($tarifs as $tarif) {
            $prestations_client[] = $tarif
                ->getFactPrestationClient()
                ->getFactPrestation()
                ->getId();
        }

        $qb2 = $this->getEntityManager()
            ->getRepository('AppBundle:FactPrestationClient')
            ->createQueryBuilder('p');
        $prestations = $qb2
            ->select('p')
            ->innerJoin('p.client', 'client')
            ->addSelect('client')
            ->where('client = :client')
            ->andWhere('p.status = :status')
            ->innerJoin('p.factPrestation', 'factPrestation')
            ->addSelect('factPrestation')
            ->andWhere($qb2->expr()->notIn('factPrestation.id', ':prestations_client'))
            ->setParameters(array(
                'client' => $client,
                'status' => true,
                'prestations_client' => $prestations_client,
            ))
            ->getQuery()
            ->getResult();

        return $prestations;

    }

    public function completeTarifClient(Client $client, FactAnnee $annee, FactModele $modele, $recalculer=false)
    {
        $manquants = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifClient')
            ->getPrestationTarifClientManquant($client, $annee);
        $annee_n1 = $this->getEntityManager()
            ->getRepository('AppBundle:FactAnnee')
            ->findOneBy(array(
                'annee' => $annee->getAnnee() - 1,
            ));

        /* Tarif Client n-1 */
        $tarifs_n_1 = [];
        if ($annee_n1) {
            $t_n_1 = $this->getEntityManager()
                ->getRepository('AppBundle:FactTarifClient')
                ->getAllTarifByClientAndAnnee($client, $annee_n1);
            /* @var FactTarifClient $item */
            foreach ($t_n_1 as $item) {
                $tarifs_n_1[$item->getFactPrestationClient()->getFactPrestation()->getId()] = $item;
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
            /* @var \AppBundle\Entity\FactPrestationClient $manquant */
            foreach ($manquants as $manquant) {
                $tarif = new FactTarifClient();
                $tarif
                    ->setClient($client)
                    ->setFactPrestationClient($manquant)
                    ->setFactAnnee($annee)
                    ->setFactModele($modele);
                if (isset($tarifs_n_1[$manquant->getFactPrestation()->getId()])) {
                    /* @var FactTarifClient $tmp_tarif */
                    $tmp_tarif = $tarifs_n_1[$manquant->getFactPrestation()->getId()];
                    $tarif
                        ->setShowQuantite($tmp_tarif->getShowQuantite())
                        ->setFormule($tmp_tarif->getFormule())
                        ->setPuFixe($tmp_tarif->getPuFixeIndice())
                        ->setPuVariable($tmp_tarif->getPuVariableIndice());
                } elseif (isset($tarifs_g_n[$manquant->getFactPrestation()->getId()])) {
                    /* @var FactTarifClient $tmp_tarif */
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
            ->getRepository('AppBundle:FactTarifClient')
            ->getAllTarifByClientAndAnnee($client, $annee);
        if ($tarifs && count($tarifs) > 0) {
            /* @var FactTarifClient $tarif */
            foreach ($tarifs as $tarif) {
                $tarif->setFactModele($modele);
                if ($recalculer) {
                    if (isset($tarifs_g_n[$tarif->getFactPrestationClient()->getFactPrestation()->getId()])) {
                        $tmp_tarif = $tarifs_g_n[$tarif->getFactPrestationClient()->getFactPrestation()->getId()];
                        $tarif
                            ->setShowQuantite($tmp_tarif->getShowQuantite())
                            ->setFormule($tmp_tarif->getFormule())
                            ->setPuFixe($tmp_tarif->getPuFixe())
                            ->setPuVariable($tmp_tarif->getPuVariable())
                            ->setPuFixeIndice($tmp_tarif->getPuFixeIndice())
                            ->setPuVariableIndice($tmp_tarif->getPuVariableIndice());
                    }
                }
            }
        }
        $em->flush();

        return true;
    }

    public function calculerPu(Client $client, FactAnnee $annee, FactModele $modele, $recalculer = false)
    {
        $em = $this->getEntityManager();

        $annee_n_1 = $this->getEntityManager()
            ->getRepository('AppBundle:FactAnnee')
            ->findOneBy(array(
                'annee' => $annee->getAnnee() - 1
            ));
        $tarifs_n_1 = [];

        // Liste tarifs N-1
        if ($annee_n_1) {
            $t_n_1 = $this->getEntityManager()
                ->getRepository('AppBundle:FactTarifClient')
                ->getAllTarifByClientAndAnnee($client, $annee_n_1);
            /* @var FactTarifClient $item */
            foreach ($t_n_1 as $item) {
                $tarifs_n_1[$item->getFactPrestationClient()->getFactPrestation()->getId()] = $item;
            }
        }

        $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifClient')
            ->completeTarifClient($client, $annee, $modele, $recalculer);

        $tarifs = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifClient')
            ->getAllTarifByClientAndAnnee($client, $annee);

        /** @var FactIndice $the_indice */
        $the_indice = $this->getEntityManager()
            ->getRepository('AppBundle:FactIndice')
            ->getIndiceByAnnee($annee);

        /* @var FactTarifClient $tarif */
        foreach ($tarifs as $tarif) {
            $the_tarif = $this->getEntityManager()
                ->getRepository('AppBundle:FactTarifClient')
                ->find($tarif->getId());
            if (isset($tarifs_n_1[$tarif->getFactPrestationClient()->getFactPrestation()->getId()])) {
                /* @var FactTarifClient $tarif_n_1 */
                $tarif_n_1 = $tarifs_n_1[$tarif->getFactPrestationClient()->getFactPrestation()->getId()];
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
                    /* Si tarif (n-1) n'existe pas: Pu_indice_n = Pu_n */
                    if ((!$the_tarif->getPuFixeIndice() || $the_tarif->getPuFixeIndice() == 0)
                        && $the_tarif->getPuFixe() && $the_tarif->getPuFixe() != 0) {
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