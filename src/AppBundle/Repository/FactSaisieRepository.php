<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 20/01/2017
 * Time: 11:58
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\FactAnnee;
use AppBundle\Entity\FactClientAssocie;
use AppBundle\Entity\FactCritereEcriture;
use AppBundle\Entity\FactMoisSaisi;
use AppBundle\Entity\FactRemiseApplique;
use AppBundle\Entity\FactSaisie;
use AppBundle\Entity\FactTarifClient;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class FactSaisieRepository extends EntityRepository
{
    public function getAllSaisieByDossierAndMoisAndExercice(Dossier $dossier, FactMoisSaisi $factMoisSaisi, $exercice, FactAnnee $annee_tarif)
    {
        $saisies = $this->getEntityManager()
            ->getRepository('AppBundle:FactSaisie')
            ->createQueryBuilder('saisie')
            ->select('saisie')
            ->innerJoin('saisie.dossier', 'dossier')
            ->addSelect('dossier')
            ->where('dossier.id = :dossier')
            ->innerJoin('saisie.factMoisSaisi', 'factMoisSaisi')
            ->addSelect('factMoisSaisi')
            ->andWhere('factMoisSaisi.id = :factMoisSaisi')
            ->innerJoin('saisie.factAnnee', 'factAnnee')
            ->addSelect('factAnnee')
            ->andWhere('factAnnee.id = :factAnnee')
            ->innerJoin('saisie.factTarifClient', 'factTarifClient')
            ->addSelect('factTarifClient')
            ->innerJoin('factTarifClient.factAnnee', 'factAnnee2')
            ->innerJoin('factTarifClient.factPrestationClient', 'factPrestationClient')
            ->addSelect('factPrestationClient')
            ->innerJoin('factPrestationClient.factPrestation', 'factPrestation')
            ->addSelect('factPrestation')
            ->innerJoin('factPrestation.factDomaine', 'factDomaine')
            ->addSelect('factDomaine')
            ->innerJoin('factTarifClient.client', 'client')
            ->addSelect('client')
            ->andWhere('client.id = :client')
            ->andWhere('saisie.exercice = :exercice')
            ->setParameters(array(
                'dossier' => $dossier->getId(),
                'factMoisSaisi' => $factMoisSaisi->getId(),
                'client' => $dossier->getSite()->getClient()->getId(),
                'exercice' => $exercice,
                'factAnnee' => $annee_tarif->getId(),
            ))
            ->orderBy('factPrestation.code')
            ->getQuery()
            ->getResult();
        return $saisies;
    }

    public function getAllSaisieByClientAndMoisAndExercice(Client $client, $mois, $exercice, FactAnnee $annee_tarif)
    {
        $saisies = $this->getEntityManager()
            ->getRepository('AppBundle:FactSaisie')
            ->createQueryBuilder('saisie')
            ->select('saisie')
            ->innerJoin('saisie.dossier', 'dossier')
            ->addSelect('dossier')
            ->innerJoin('dossier.site', 'site')
            ->innerJoin('site.client', 'client1')
            ->where('client1 = :client')
            ->innerJoin('saisie.factMoisSaisi', 'factMoisSaisi')
            ->addSelect('factMoisSaisi')
            ->andWhere('factMoisSaisi.mois = :mois')
            ->innerJoin('saisie.factAnnee', 'factAnnee')
            ->addSelect('factAnnee')
            ->andWhere('factAnnee = :factAnnee')
            ->innerJoin('saisie.factTarifClient', 'factTarifClient')
            ->addSelect('factTarifClient')
            ->innerJoin('factTarifClient.factAnnee', 'factAnnee2')
            ->innerJoin('factTarifClient.factPrestationClient', 'factPrestationClient')
            ->addSelect('factPrestationClient')
            ->innerJoin('factPrestationClient.factPrestation', 'factPrestation')
            ->addSelect('factPrestation')
            ->innerJoin('factPrestation.factDomaine', 'factDomaine')
            ->addSelect('factDomaine')
            ->innerJoin('factTarifClient.client', 'client')
            ->addSelect('client')
            ->andWhere('client = :client')
            ->andWhere('saisie.exercice = :exercice')
            ->andWhere('((saisie.uniteRealise IS NOT NULL AND saisie.uniteRealise != 0) OR 
                    (saisie.prix IS NOT NULL AND saisie.prix != 0) OR
                    (saisie.quantite IS NOT NULL AND saisie.quantite != 0))'
            )
            ->setParameters(array(
                'mois' => $mois,
                'client' => $client,
                'exercice' => $exercice,
                'factAnnee' => $annee_tarif,
            ))
            ->orderBy('dossier.nom')
            ->addOrderBy('factPrestation.code')
            ->getQuery()
            ->getResult();
        return $saisies;
    }

    public function getTarifManquant(Dossier $dossier, FactMoisSaisi $factMoisSaisi, $exercice, FactAnnee $annee_tarif)
    {
        $saisies = $this->getAllSaisieByDossierAndMoisAndExercice($dossier, $factMoisSaisi, $exercice, $annee_tarif);
        $tarifs_existant = [0];

        /* @var FactSaisie $saisie */
        foreach ($saisies as $saisie) {
            $tarifs_existant[] = $saisie->getFactTarifClient()->getId();
        }

        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:FactTarifClient')
            ->createQueryBuilder('tarif');
        $manquants = $qb
            ->select('tarif')
            ->innerJoin('tarif.client', 'client')
            ->where('client = :client')
            ->innerJoin('tarif.factAnnee', 'factAnnee')
            ->andWhere('factAnnee = :factAnnee')
            ->andWhere($qb->expr()->notIn('tarif.id', ':tarifs_existant'))
            ->setParameters(array(
                'client' => $dossier->getSite()->getClient(),
                'factAnnee' => $annee_tarif,
                'tarifs_existant' => $tarifs_existant,
            ))
            ->getQuery()
            ->getResult();
        return $manquants;
    }

    /**
     * @param Dossier $dossier
     * @param FactMoisSaisi $factMoisSaisi
     * @param $exercice
     * @param FactAnnee $annee_tarif
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function completeSaisie(Dossier $dossier, FactMoisSaisi $factMoisSaisi, $exercice, FactAnnee $annee_tarif)
    {
        $manquants = $this->getTarifManquant($dossier, $factMoisSaisi, $exercice, $annee_tarif);
        if (count($manquants) > 0) {
            $em = $this->getEntityManager();
            /* @var FactTarifClient $manquant */
            foreach ($manquants as $manquant) {
                $saisie = new FactSaisie();
                $saisie
                    ->setDossier($dossier)
                    ->setFactTarifClient($manquant)
                    ->setExercice($exercice)
                    ->setFactAnnee($annee_tarif)
                    ->setFactMoisSaisi($factMoisSaisi);
                $em->persist($saisie);
            }
            $em->flush();
        }
        return true;
    }

    /**
     * Get Quantité or Unité Réalisé selon critère par prestation
     *
     * @param Dossier $dossier
     * @param FactMoisSaisi $factMoisSaisi
     * @param $exercice
     * @param FactAnnee $annee_tarif
     * @return array
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function getQuantiteOrUniteRealise(Dossier $dossier, FactMoisSaisi $factMoisSaisi, $exercice, FactAnnee $annee_tarif)
    {
        $em = $this->getEntityManager();
        $saisies = $this->getAllSaisieByDossierAndMoisAndExercice($dossier, $factMoisSaisi, $exercice, $annee_tarif);

        $date_limit = \DateTime::createFromFormat('d-m-Y', "01-" . $factMoisSaisi->getMois());
        $date_limit->add(new \DateInterval('P1M'));
        $date_limit->sub(new \DateInterval('P1D'));
        $values = [];
        /** @var FactSaisie $saisie */
        foreach ($saisies as $saisie) {
            $prestation_client = $saisie->getFactTarifClient()->getFactPrestationClient();
            $critere_ecritures = $this->getEntityManager()
                ->getRepository('AppBundle:FactCritereEcriture')
                ->getByPrestationClient($prestation_client);

            $value = 0;

            if (count($critere_ecritures) > 0) {
                /** @var FactCritereEcriture $critere_ecriture */

                foreach ($critere_ecritures as $critere_ecriture) {
                    if ($critere_ecriture->getFactCritere()) {
                        // BOUCLER VALUE ET EXCLURE
                        $critere_nom = $critere_ecriture->getNom();
                        $critere_values = $critere_ecriture->getValue();
                        $critere_exclures = $critere_ecriture->getExclure();
                        $critere_key = $critere_ecriture->getFactCritere()->getCode();

                        switch ($critere_key) {
                            case "IMAGE_BEGIN_WITH":
                                foreach ($critere_values as $critere_value) {
                                    $value += $this->getEntityManager()
                                        ->getRepository('AppBundle:FactCritereEcriture')
                                        ->getImageBeginsWith($dossier, $critere_value, $exercice, $date_limit);
                                }
                                foreach ($critere_exclures as $critere_exclure) {
                                    $value -= $this->getEntityManager()
                                        ->getRepository('AppBundle:FactCritereEcriture')
                                        ->getImageBeginsWith($dossier, $critere_exclure, $exercice, $date_limit);
                                }
                                break;
                            case "IMAGE_END_WITH":
                                foreach ($critere_values as $critere_value) {
                                    $value += $this->getEntityManager()
                                        ->getRepository('AppBundle:FactCritereEcriture')
                                        ->getImageEndsWith($dossier, $critere_value, $exercice, $date_limit);
                                }
                                foreach ($critere_exclures as $critere_exclure) {
                                    $value -= $this->getEntityManager()
                                        ->getRepository('AppBundle:FactCritereEcriture')
                                        ->getImageEndsWith($dossier, $critere_exclure, $exercice, $date_limit);
                                }
                                break;
                            case "IMAGE_CONTAINS":
                                foreach ($critere_values as $critere_value) {
                                    $value += $this->getEntityManager()
                                        ->getRepository('AppBundle:FactCritereEcriture')
                                        ->getImageContains($dossier, $critere_value, $exercice, $date_limit);
                                }
                                foreach ($critere_exclures as $critere_exclure) {
                                    $value -= $this->getEntityManager()
                                        ->getRepository('AppBundle:FactCritereEcriture')
                                        ->getImageContains($dossier, $critere_exclure, $exercice, $date_limit);
                                }
                                break;
                        }
                    }
                }

                if ($value < 0) {
                    $value = 0;
                }
                if ($saisie->getFactTarifClient()->getFormule() && $saisie->getFactTarifClient()->getFormule() != "") {
                    $saisie->setQuantite($value);
                } else {
                    $saisie->setUniteRealise($value);
                }
                $em->flush();

                $values[] = [
                    'prestation' => $prestation_client->getFactPrestation()->getCode(),
                    'value' => $value,
                ];
            }
        }

        return $values;
    }

    /**
     * @param Dossier $dossier
     * @param FactMoisSaisi $factMoisSaisi
     * @param $exercice
     * @param FactAnnee $annee_tarif
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function calculerPrix(Dossier $dossier, FactMoisSaisi $factMoisSaisi, $exercice, FactAnnee $annee_tarif)
    {
        $saisies = $this->getAllSaisieByDossierAndMoisAndExercice($dossier, $factMoisSaisi, $exercice, $annee_tarif);

        $this->getQuantiteOrUniteRealise($dossier, $factMoisSaisi, $exercice, $annee_tarif);

        $langage = new ExpressionLanguage();
        $em = $this->getEntityManager();

        $list_val = [];
        /* @var FactSaisie $saisie */
        foreach ($saisies as $saisie) {
            $code = $saisie->getFactTarifClient()->getFactPrestationClient()->getFactPrestation()->getCode();
            $list_val['_' . $code] = $saisie->getUniteRealise() ? $saisie->getUniteRealise() : 0;
        }

        $count = count($saisies);
        for ($i = 0; $i < $count; $i++) {
            /* @var FactSaisie $saisie */
            $saisie = $saisies[$i];
            $formule = $saisie->getFactTarifClient()->getFormule();

            if ($formule && trim($formule) != '') {
                if ($saisie->getNoCalcul() == false) {
                    /* Calculer les prix avec formule */

                    $unite_realise = $langage->evaluate($formule, $list_val);
                    $saisie->setUniteRealise($unite_realise);

                    $quantite = $saisie->getQuantite() ? $saisie->getQuantite() : 0;
                    $pu_fixe = $saisie->getFactTarifClient()->getPuFixeIndice() ? $saisie->getFactTarifClient()->getPuFixeIndice() : 0;
                    $pu_variable = $saisie->getFactTarifClient()->getPuVariableIndice() ? $saisie->getFactTarifClient()->getPuVariableIndice() : 0;

                    $with_quantite = ($quantite && $quantite != 0);
                    $with_unite_realise = ($unite_realise && $unite_realise != 0);

                    if ($with_quantite && $with_unite_realise) {
                        $prix = (($pu_variable * $unite_realise) + $pu_fixe) * $quantite;
                    } elseif ($with_quantite && !$with_unite_realise) {
                        $prix = $pu_fixe * $quantite;
                    } elseif (!$with_quantite && $with_unite_realise) {
                        $prix = ($pu_variable * $unite_realise) + $pu_fixe;
                    } else {
                        $prix = null;
                    }

                    if ($prix) {
                        $prix_net = $prix;
                    } else {
                        $prix_net = null;
                    }
                    $saisie
                        ->setPrix($prix)
                        ->setPrixNet($prix_net);
                } else {
                    $saisie
                        ->setUniteRealise(null)
                        ->setPrix(null)
                        ->setPrixNet(null);
                }

                $code = $saisie->getFactTarifClient()->getFactPrestationClient()->getFactPrestation()->getCode();
                $list_val['_' . $code] = $saisie->getUniteRealise() ? $saisie->getUniteRealise() : 0;
            } else {
                $unite_realise = $saisie->getUniteRealise();
                $quantite = $saisie->getQuantite() ? $saisie->getQuantite() : 0;
                $pu_fixe = $saisie->getFactTarifClient()->getPuFixeIndice() ? $saisie->getFactTarifClient()->getPuFixeIndice() : 0;
                $pu_variable = $saisie->getFactTarifClient()->getPuVariableIndice() ? $saisie->getFactTarifClient()->getPuVariableIndice() : 0;

                $with_quantite = ($quantite && $quantite != 0);
                $with_unite_realise = ($unite_realise && $unite_realise != 0);

                if ($with_quantite && $with_unite_realise) {
                    $prix = (($pu_variable * $unite_realise) + $pu_fixe) * $quantite;
                } elseif ($with_quantite && !$with_unite_realise) {
                    $prix = $pu_fixe * $quantite;
                } elseif (!$with_quantite && $with_unite_realise) {
                    $prix = ($pu_variable * $unite_realise) + $pu_fixe;
                } else {
                    $prix = null;
                }

                if ($prix) {
                    $prix_net = $prix;
                } else {
                    $prix_net = null;
                }
                $saisie
                    ->setPrix($prix)
                    ->setPrixNet($prix_net);
            }
        }
        $em->flush();

        //Calculer la remise à appliquer
        $calculer_remise = $this->calculerRemise($dossier, $factMoisSaisi, $exercice, $annee_tarif);
        unset($calculer_remise);

        return true;
    }

    public function getNbLigneClient(Client $client, $exercice, $mois)
    {
        $client_associes = $this->getEntityManager()
            ->getRepository('AppBundle:FactClientAssocie')
            ->createQueryBuilder('factClientAssocie')
            ->select('factClientAssocie')
            ->innerJoin('factClientAssocie.clientPrincipal', 'clientPrincipal')
            ->addSelect('clientPrincipal')
            ->innerJoin('factClientAssocie.clientAutre', 'clientAutre')
            ->addSelect('clientAutre')
            ->where('clientPrincipal = :client')
            ->setParameters(array(
                'client' => $client,
            ))
            ->getQuery()
            ->getResult();
        if (count($client_associes) > 0) {
            $clients = [0];
            /* @var FactClientAssocie $client_associe */
            foreach ($client_associes as $client_associe) {
                $clients[] = $client_associe->getClientAutre()->getId();
            }

            $qb = $this->getEntityManager()
                ->getRepository('AppBundle:FactSaisie')
                ->createQueryBuilder('saisie');
            $nb = $qb
                ->innerJoin('saisie.dossier', 'dossier')
                ->innerJoin('dossier.site', 'site')
                ->innerJoin('site.client', 'client')
                ->innerJoin('saisie.factTarifClient', 'factTarifClient')
                ->innerJoin('factTarifClient.factPrestationClient', 'factPrestationClient')
                ->innerJoin('factPrestationClient.factPrestation', 'factPrestation')
                ->innerJoin('saisie.factMoisSaisi', 'factMoisSaisi')
                ->where($qb->expr()->in('client.id', ':clients'))
                ->andWhere('factPrestation.code = :code')
                ->andWhere('saisie.exercice = :exercice')
                ->andWhere('factMoisSaisi.mois = :mois')
                ->setParameters(array(
                    'clients' => $clients,
                    'code' => 220,
                    'exercice' => $exercice,
                    'mois' => $mois,
                ))
                ->select('SUM(saisie.uniteRealise) as nombre')
                ->getQuery()
                ->getResult();

            return intval($nb[0]['nombre']);
        } else {
            $nb = $this->getEntityManager()
                ->getRepository('AppBundle:FactSaisie')
                ->createQueryBuilder('saisie')
                ->innerJoin('saisie.dossier', 'dossier')
                ->innerJoin('dossier.site', 'site')
                ->innerJoin('site.client', 'client')
                ->innerJoin('saisie.factTarifClient', 'factTarifClient')
                ->innerJoin('factTarifClient.factPrestationClient', 'factPrestationClient')
                ->innerJoin('factPrestationClient.factPrestation', 'factPrestation')
                ->where('client = :client')
                ->andWhere('factPrestation.code = :code')
                ->andWhere('saisie.exercice = :exercice')
                ->setParameters(array(
                    'client' => $client,
                    'code' => 220,
                    'exercice' => $exercice
                ))
                ->select('SUM(saisie.uniteRealise) as nombre')
                ->getQuery()
                ->getResult();

            return intval($nb[0]['nombre']);
        }
    }

    public function getNbLigneDossier(Dossier $dossier, $exercice, FactMoisSaisi $factMoisSaisi)
    {
        $nb = $this->getEntityManager()
            ->getRepository('AppBundle:FactSaisie')
            ->createQueryBuilder('saisie')
            ->innerJoin('saisie.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->innerJoin('site.client', 'client')
            ->innerJoin('saisie.factTarifClient', 'factTarifClient')
            ->innerJoin('factTarifClient.factPrestationClient', 'factPrestationClient')
            ->innerJoin('factPrestationClient.factPrestation', 'factPrestation')
            ->innerJoin('saisie.factMoisSaisi', 'factMoisSaisi')
            ->where('client = :client')
            ->andWhere('factPrestation.code = :code')
            ->andWhere('saisie.exercice = :exercice')
            ->andWhere('saisie.dossier = :dossier')
            ->andWhere('factMoisSaisi = :factMoisSaisi')
            ->setParameters(array(
                'client' => $dossier->getSite()->getClient(),
                'code' => 220,
                'exercice' => $exercice,
                'dossier' => $dossier,
                'factMoisSaisi' => $factMoisSaisi,
            ))
            ->select('SUM(saisie.uniteRealise) as nombre')
            ->getQuery()
            ->getResult();

        return intval($nb[0]['nombre']);
    }

    public function calculerRemise(Dossier $dossier, FactMoisSaisi $factMoisSaisi, $exercice, FactAnnee $annee_tarif)
    {

        $saisies = $this->getAllSaisieByDossierAndMoisAndExercice($dossier, $factMoisSaisi, $exercice, $annee_tarif);
        $em = $this->getEntityManager();

        if (count($saisies) > 0) {
            /* @var FactRemiseApplique $remise_appliquer */
            $remise_appliquer = $this->getEntityManager()
                ->getRepository('AppBundle:FactRemiseApplique')
                ->getRemiseAppliqueByClient($dossier->getSite()->getClient());
            if ($remise_appliquer) {
                $remise = $remise_appliquer->getFactRemiseNiveau();
            } else {
                $remise = null;
            }
            if ($remise) {
                $nb_ligne_client = $this->getEntityManager()
                    ->getRepository('AppBundle:FactSaisie')
                    ->getNbLigneClient($dossier->getSite()->getClient(), $exercice, $factMoisSaisi->getMois());
                $remise_percent = $this->getEntityManager()
                    ->getRepository('AppBundle:FactRemiseVolume')
                    ->getPercentageByVolume($remise, $nb_ligne_client);
            } else {
                $remise_percent = 0;
            }
            if ($remise_percent != 0) {
                /* @var FactSaisie $saisie */
                foreach ($saisies as $saisie) {
                    if ($saisie->getNoCalcul() == false) {
                        $avec_remise = $saisie
                            ->getFactTarifClient()
                            ->getFactPrestationClient()
                            ->getFactPrestation()
                            ->getRemise();
                        if ($avec_remise) {
                            $prix = $saisie->getPrix() ? $saisie->getPrix() : 0;
                            $remise = ($prix * $remise_percent / 100);
                            $prix_net = $prix - $remise;
                            $saisie
                                ->setRemiseVolume($remise)
                                ->setPrixNet($prix_net);
                        }
                    } else {
                        $saisie
                            ->setRemiseVolume(null)
                            ->setPrixNet(null);
                    }
                }
                $em->flush();
            } else {
                /* @var FactSaisie $saisie */
                foreach ($saisies as $saisie) {
                    if ($saisie->getNoCalcul() == false) {
                        $prix = $saisie->getPrix();
                        $saisie
                            ->setRemiseVolume(null)
                            ->setPrixNet($prix);
                    } else {
                        $saisie
                            ->setRemiseVolume(null)
                            ->setPrixNet(null);
                    }
                }
                $em->flush();
            }
        }
    }

    /**
     * @param Client $client
     * @param $mois
     * @param $exercice
     * @param FactAnnee $annee_tarif
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function recalculerSaisieClient(Client $client, $mois, $exercice, FactAnnee $annee_tarif)
    {
        $dossiers_id = $this->getEntityManager()
            ->getRepository('AppBundle:FactSaisie')
            ->createQueryBuilder('saisie')
            ->select('DISTINCT dossier.id')
            ->innerJoin('saisie.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->innerJoin('site.client', 'client')
            ->where('client = :client')
            ->setParameters(array(
                'client' => $client
            ))
            ->orderBy('dossier.nom')
            ->getQuery()
            ->getResult();

//        foreach ($dossiers_id as $dossier_id) {
        $count = count($dossiers_id);
        for ($i = 0; $i < $count; $i++) {
            $dossier_id = $dossiers_id[$i];
            $dossier = $this->getEntityManager()
                ->getRepository('AppBundle:Dossier')
                ->find($dossier_id);
            if ($dossier) {
                $mois_saisi = $this->getEntityManager()
                    ->getRepository('AppBundle:FactMoisSaisi')
                    ->findOneBy(array(
                        'dossier' => $dossier,
                        'mois' => $mois,
                        'exercice' => $exercice,
                    ));
                if ($mois_saisi) {
                    $calculer = $this->getEntityManager()
                        ->getRepository('AppBundle:FactSaisie')
                        ->calculerPrix($dossier, $mois_saisi, $exercice, $annee_tarif);
                    unset($calculer);
                }
            }
        }

        return true;
    }

    /**
     * @param Dossier $dossier
     * @param $mois
     * @param $exercice
     * @param FactAnnee $annee_tarif
     * @return array
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function getLignesImporter(Dossier $dossier, $mois, $exercice, FactAnnee $annee_tarif)
    {
        $date_limit = \DateTime::createFromFormat('d-m-Y', "01-" . $mois);
        $date_limit->add(new \DateInterval('P1M'));
        $date_limit->sub(new \DateInterval('P1D'));

        $ecritures = $this->getEntityManager()
            ->getRepository('AppBundle:Ecriture')
            ->createQueryBuilder('ecriture')
            ->select('COUNT(ecriture.id) AS nbligne')
            ->innerJoin('ecriture.dossier', 'dossier')
            ->where("ecriture.exercice = :exercice")
            ->andWhere("dossier = :dossier")
            ->andWhere("ecriture.dateEcr <= :date_limit")
            ->setParameters(array(
                'exercice' => $exercice,
                'dossier' => $dossier,
                'date_limit' => $date_limit->format('Y-m-d')
            ))
            ->getQuery()
            ->getResult();
        $mois_saisi = $this->getEntityManager()
            ->getRepository('AppBundle:FactMoisSaisi')
            ->findOneBy(array(
                'mois' => $mois,
                'dossier' => $dossier,
                'exercice' => $exercice,
            ));
        if (!$mois_saisi) {
            $em = $this->getEntityManager();
            $mois_saisi1 = new FactMoisSaisi();
            $mois_saisi1
                ->setMois($mois)
                ->setExercice($exercice)
                ->setDossier($dossier);
            $em->persist($mois_saisi1);
            $em->flush();
            $mois_saisi = $mois_saisi1;
        }
        $prestation_values = $this->getQuantiteOrUniteRealise($dossier, $mois_saisi, $exercice, $annee_tarif);

        $total_prestation = 0;
        $prestations = array_map(function($prestation_value) use (&$total_prestation) {
            if (isset($prestation_value['value'])) {
                $total_prestation += intval($prestation_value['value']);
            }
            return ['controle-' . $prestation_value['prestation'] => $prestation_value['value']];
        }, $prestation_values);

        $ligne_importer = intval($ecritures[0]['nbligne']) !== 0 ? intval($ecritures[0]['nbligne']) : "";
        $non_affecter = intval($ecritures[0]['nbligne'] - $total_prestation) !== 0 ? intval($ecritures[0]['nbligne'] - $total_prestation) : "";
        $affecter = $total_prestation !== 0 ? $total_prestation : "";

        $controle = '<i class="fa fa-check" style="color:#18a689"></i>';
        if (intval($ligne_importer) == 0 || intval($ligne_importer) - intval($affecter) > 0) {
            $controle = '<i class="fa fa-exclamation-circle" style="color:#ed5565;"></i>';
        }

        $lignes = [
            'id' => $dossier->getId(),
            'controle-dossier' => $dossier->getNom(),
            'controle-ligne-importer' => $ligne_importer,
            'controle-non-affecter' => $non_affecter,
            'controle-affecter' => $affecter,
            'controle-controle' => $controle
        ];

        foreach ($prestations as $key => &$value) {
            foreach ($value as $key2 => &$value2) {
                if (intval($value2) == 0) {
                    $value2 = "";
                }
            }
            $lignes = $lignes + $value;
        }

        return $lignes;
    }
}