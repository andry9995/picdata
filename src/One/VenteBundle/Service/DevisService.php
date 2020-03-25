<?php

/**
 * Created by Netbeans
 * Created on : 15 août 2017, 19:06:16
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Service;

use AppBundle\Entity\OneArticleVente;
use AppBundle\Entity\OneContactClient;
use AppBundle\Entity\OneDevis;
use AppBundle\Entity\Tiers;
use Doctrine\ORM\EntityManager;

class DevisService
{
    private $entity_manager;
    
    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }
    
    /**
     * Récupération des IDs des devis facturés
     * @return type
     */
    public function getInvoicedDevis() {
        $invoicedDevis = array();
        $invoiced = $this->entity_manager->getRepository('AppBundle:OneInvoiceDevis')->findAll();
        foreach($invoiced as $item) {
            $invoicedDevis[] = $item->getOneDevis()->getId();
        }
        return $invoicedDevis;
    }
    
    /**
     * Récupération des IDs des devis commandés
     * @return type
     */
    public function getCommandedDevis() {
        $commandedDevis = array();
        $commanded = $this->entity_manager->getRepository('AppBundle:OneCommandeDevis')->findAll();
        foreach($commanded as $item) {
            $commandedDevis[] = $item->getOneDevis()->getId();
        }
        return $commandedDevis;
    }
    
    /**
     * Génère le code suivant
     * @return string
     */
    public function getNextCode() {
        $lastCode = $this->entity_manager->getRepository('AppBundle:OneDevis')->getLastCode();
        $number = intval(explode('-', $lastCode)[1]) + 1;
        if ($number < 10) {
            $nextCode = 'DEV-00'.$number;
        } elseif ($number >= 10 && $number < 100) {
            $nextCode = 'DEV-0'.$number;
        } else {
            $nextCode = 'DEV-'.$number;
        }
        return $nextCode;
    }
    
    public function getAddress($type, $id) {
        $address = '';
        if ($type === 'contact' && $id > 0) {
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
        } else {
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
     * @return array
     */
    public function getDevisDetails(array $clientProspects, $exercice) {
        $devisDetails = array();

        $deviss = $this->entity_manager
            ->getRepository('AppBundle:OneDevis')
            ->getDevis($clientProspects, $exercice);
        /** @var OneDevis $devis */
        foreach ($deviss as $devis) {
            $ht = 0;
            $tva = 0;
            $remise = 0;
            $remise_tva = 0;
            
            //Articles de la facture
            /** @var OneArticleVente[] $articles */
            $articles = $this->entity_manager
                ->getRepository('AppBundle:OneArticleVente')
                ->findBy(array('devis' =>$devis));

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
                
                //Mise à jour du montant HT de la facture
                $ht = $ht + $art_amount;
                
                //Mise à jour montant TVA
                $art_tva_amount = ($art_ht * $art_tva) / 100;
                $tva = $tva + $art_tva_amount;
            }
            
            //Remise du devis
            $devis_remise = $devis->getRemise();
            if ($devis_remise > 0) {
                $remise = ($ht * $devis_remise) / 100;
            }
            
            //Remise TVA
            if ($devis_remise > 0 && $tva > 0) {
                $remise_tva = ($tva * $devis_remise) / 100;
                $tva = $tva - $remise_tva;
            }
            
            //Montant TTC de la facture
            $ttc = ($ht - $remise) + $tva;
            
            //Ajout du montant dans la table de montants des factures
            $devisDetails[$devis->getId()]['ht'] = $ht;
            $devisDetails[$devis->getId()]['remise'] = $remise;
            $devisDetails[$devis->getId()]['tva'] = $tva;
            $devisDetails[$devis->getId()]['ttc'] = $ttc;
        }
        return $devisDetails;
    }


    public function getDevisDetailsByDevis(OneDevis $devis)
    {
        $devisDetails = array();

        /** @var OneDevis $devis */

        $ht = 0;
        $tva = 0;
        $remise = 0;
        $remise_tva = 0;

        //Articles de la facture
        /** @var OneArticleVente[] $articles */
        $articles = $this->entity_manager
            ->getRepository('AppBundle:OneArticleVente')
            ->findBy(array('devis' => $devis));

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

            //Mise à jour du montant HT de la facture
            $ht = $ht + $art_amount;

            //Mise à jour montant TVA
            $art_tva_amount = ($art_ht * $art_tva) / 100;
            $tva = $tva + $art_tva_amount;
        }

        //Remise du devis
        $devis_remise = $devis->getRemise();
        if ($devis_remise > 0) {
            $remise = ($ht * $devis_remise) / 100;
        }

        //Remise TVA
        if ($devis_remise > 0 && $tva > 0) {
            $remise_tva = ($tva * $devis_remise) / 100;
            $tva = $tva - $remise_tva;
        }

        //Montant TTC de la facture
        $ttc = ($ht - $remise) + $tva;

        //Ajout du montant dans la table de montants des factures
        $devisDetails['ht'] = $ht;
        $devisDetails['remise'] = $remise;
        $devisDetails['tva'] = $tva;
        $devisDetails['ttc'] = $ttc;

        return $devisDetails;
    }
}