<?php

/**
 * Created by Netbeans
 * Created on : 15 août 2017, 19:06:16
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Service;

use AppBundle\Entity\OneContactClient;
use AppBundle\Entity\OneVente;
use AppBundle\Entity\Tiers;
use Doctrine\ORM\EntityManager;

class VenteService
{
    private $entity_manager;
    
    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }
    
    /**
     * Génère le code suivant
     * @return string
     */
    public function getNextCodeVente($type='facture') {
        if ($type == 'facture')
            $startcode = 'FAC-';
        elseif ($type == 'commande')
            $startcode = 'COM-';
        elseif ($type == 'avoir')
            $startcode = 'AVO-';
        
        $lastCode = $this->entity_manager->getRepository('AppBundle:OneVente')->getLastCodeVente($type);
        $number = intval(explode('-', $lastCode)[1]) + 1;
        if ($number < 10) {
            $nextCode = $startcode.'00'.$number;
        } elseif ($number > 10 && $number < 100) {
            $nextCode = $startcode.'0'.$number;
        } else {
            $nextCode = $startcode.$number;
        }
        return $nextCode;
    }
    
    /**
     * Récupération adresse facture
     * @param type $type
     * @param type $id
     * @return string
     */
    public function getAddress($type, $id) {
        $address = '';
        if ($type == 'contact' && $id > 0) {
            /** @var OneContactClient $contact */
            $contact = $this->entity_manager
                ->getRepository('AppBundle:OneContactClient')
                ->find($id);

            if ($contact->getAdresse1() != '')
                $address .= $contact->getAdresse1().'<br>';

            if ($contact->getAdresse2() != '')
                $address .= $contact->getAdresse2().'<br>';

            if ($contact->getCodePostal() != '')
                $address .= $contact->getCodePostal().' ';

            if ($contact->getVille() != '')
                $address .= $contact->getVille().'<br>';
            elseif ($contact->getCodePostal() == '')
                $address .= '<br>';

            if ($contact->getOnePays())
                $address .= $contact->getOnePays()->getNom();
        }
        else {

            /** @var Tiers $clientprospect */
            $clientprospect = $this->entity_manager
                ->getRepository('AppBundle:Tiers')
                ->find($id);

            if ($clientprospect->getAdresseFacturation1() != '')
                $address .= $clientprospect->getAdresseFacturation1().'<br>';

            if ($clientprospect->getAdresseFacturation2() != '')
                $address .= $clientprospect->getAdresseFacturation2().'<br>';

            if ($clientprospect->getCodePostalFacturation() != '')
                $address .= $clientprospect->getCodePostalFacturation().' ';

            if ($clientprospect->getVilleFacturation() != '')
                $address .= $clientprospect->getVilleFacturation().'<br>';
            elseif ($clientprospect->getCodePostalFacturation() == '')
                $address .= '<br>';

            if ($clientprospect->getPaysFacturation())
                $address .= $clientprospect->getPaysFacturation()->getNom();
        }
        return $address;
    }

    /**
     * @param array $clientProspects
     * @param $exercice
     * @param string $type
     * @return array
     */
    public function getVenteAmounts(array $clientProspects, $exercice,$type='facture') {
        $venteAmounts = array();
        $ventes = $this->entity_manager
            ->getRepository('AppBundle:OneVente')
            ->getVentes($clientProspects, $exercice, $type);
        foreach ($ventes as $vente) {
            //Montant HT de la vente
            $montantVenteHT = 0;
            
            //Articles de la facture
            $articles = $this->entity_manager
                ->getRepository('AppBundle:OneArticleVente')
                ->findBy(array('vente' => $vente));

            foreach ($articles as $article) {
                $quantiteArt = $article->getQuantite();
                $prixArt = $article->getPrix();
                $remiseArt = $article->getRemise();
                
                //Montant HT de l'article
                $montantArtHT = $quantiteArt*$prixArt;
                
                //Remise de l'artice
                $montantRemiseArt = 0;
                if ($remiseArt > 0) $montantRemiseArt = ($montantArtHT * $remiseArt) / 100;
                
                //Montant TTC de l'article
                $montantArtTTC = $montantArtHT - $montantRemiseArt;
                
                //Mise à jour du montant HT de la facture
                $montantVenteHT = $montantVenteHT + $montantArtTTC;
            }
            
            //Remise de la facture
            $montantRemiseVente = 0;
            $remiseVente = $vente->getRemise();
            if ($remiseVente > 0) $montantRemiseVente = ($montantVenteHT * $remiseVente) / 100;
            
            //Montant TTC de la facture
            $montantVenteTTC = $montantVenteHT - $montantRemiseVente;
            
            //Ajout du montant dans la table de montants des factures
            $venteAmounts[$vente->getId()] = $montantVenteTTC;
        }
        return $venteAmounts;
    }

    /**
     * Calcul des montants de vente
     * @param array $clientProspects
     * @param string $type
     * @return array
     */
    public function getVenteDetails(array $clientProspects, $exercice, $type='facture') {
        $venteDetails = array();
        /** @var OneVente[] $ventes */
        $ventes = $this->entity_manager
            ->getRepository('AppBundle:OneVente')
            ->getVentes($clientProspects, $exercice, $type);

        foreach ($ventes as $vente) {
            $ht = 0;
            $tva = 0;
            $remise = 0;
            $remise_tva = 0;
            
            //Articles de la vente
            $articles = $this->entity_manager
                ->getRepository('AppBundle:OneArticleVente'
                )->findBy(array('vente' => $vente));

            foreach ($articles as $article) {
                $art_quantity = $article->getQuantite();
                $art_price = $article->getPrix();
                $art_remise = $article->getRemise();
                $art_tva = $article->getTvaTaux()->getTaux();
                
                //Montant HT de l'article
                $art_ht = $art_quantity*$art_price;
                
                //Remise de l'artice
                $art_remise_amount = 0;
                if ($art_remise > 0) {
                    $art_remise_amount = ($art_ht * $art_remise) / 100;
                }
                
                //Montant de l'article
                $art_amount = $art_ht - $art_remise_amount;
                
                //Mise à jour du montant HT de la vente
                $ht = $ht + $art_amount;
                
                //Mise à jour montant TVA
                $art_tva_amount = ($art_ht * $art_tva) / 100;
                $tva = $tva + $art_tva_amount;
            }
            
            //Remise de la vente
            $vente_remise = $vente->getRemise();
            if ($vente_remise > 0) {
                $remise = ($ht * $vente_remise) / 100;
            }
            
            //Remise TVA
            if ($vente_remise > 0 && $tva > 0) {
                $remise_tva = ($tva * $vente_remise) / 100;
                $tva = $tva - $remise_tva;
            }
            
            //Montant TTC de la vente
            $ttc = ($ht - $remise) + $tva;
            
            //Ajout du montant dans la table de montants des ventes
            $venteDetails[$vente->getId()]['ht'] = $ht;
            $venteDetails[$vente->getId()]['remise'] = $remise;
            $venteDetails[$vente->getId()]['tva'] = $tva;
            $venteDetails[$vente->getId()]['ttc'] = $ttc;
        }
        return $venteDetails;
    }
    
    /**
     * Récupération des IDs des commandes facturées
     * @return type
     */
    public function getInvoicedCommande() {
        $invoicedCommandes = array();
        $invoiced = $this->entity_manager->getRepository('AppBundle:OneInvoiceCommande')->findAll();
        foreach($invoiced as $item) {
            $invoicedCommandes[] = $item->getOneCommande()->getId();
        }
        return $invoicedCommandes;
    }










    public function getVenteDetailsByVente(OneVente $vente)
    {
        $venteDetails = array();

        $ht = 0;
        $tva = 0;
        $remise = 0;

        //Articles de la vente
        $articles = $this->entity_manager
            ->getRepository('AppBundle:OneArticleVente'
            )->findBy(array('vente' => $vente));

        foreach ($articles as $article) {
            $art_quantity = $article->getQuantite();
            $art_price = $article->getPrix();
            $art_remise = $article->getRemise();
            $art_tva = $article->getTvaTaux()->getTaux();

            //Montant HT de l'article
            $art_ht = $art_quantity * $art_price;

            //Remise de l'artice
            $art_remise_amount = 0;
            if ($art_remise > 0) {
                $art_remise_amount = ($art_ht * $art_remise) / 100;
            }

            //Montant de l'article
            $art_amount = $art_ht - $art_remise_amount;

            //Mise à jour du montant HT de la vente
            $ht = $ht + $art_amount;

            //Mise à jour montant TVA
            $art_tva_amount = ($art_ht * $art_tva) / 100;
            $tva = $tva + $art_tva_amount;
        }

        //Remise de la vente
        $vente_remise = $vente->getRemise();
        if ($vente_remise > 0) {
            $remise = ($ht * $vente_remise) / 100;
        }

        //Remise TVA
        if ($vente_remise > 0 && $tva > 0) {
            $remise_tva = ($tva * $vente_remise) / 100;
            $tva = $tva - $remise_tva;
        }

        //Montant TTC de la vente
        $ttc = ($ht - $remise) + $tva;

        //Ajout du montant dans la table de montants des ventes
        $venteDetails['ht'] = $ht;
        $venteDetails['remise'] = $remise;
        $venteDetails['tva'] = $tva;
        $venteDetails['ttc'] = $ttc;

        return $venteDetails;
    }
}