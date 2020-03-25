<?php

/**
 * Created by Netbeans
 * Created on : 27 août 2017, 17:37:36
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Service;

use Doctrine\ORM\EntityManager;

class PaiementService
{
    private $entity_manager;
    
    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }
    
    public function getPaidAmounts(array $clientProspects, $exercice) {
        $paidAmounts = array();
        $ventes = $this->entity_manager
            ->getRepository('AppBundle:OneVente')
            ->getVentesForPaiement($clientProspects, $exercice);
        foreach ($ventes as $vente) {
            $facPaidAmount = 0;
            $paiements = $this->entity_manager->getRepository('AppBundle:OnePaiement')->findByOneVente($vente);
            foreach ($paiements as $paiement) {
                $facPaidAmount = $facPaidAmount + $paiement->getMontant();
            }
            $paidAmounts[$vente->getId()] = floatval($facPaidAmount);
        }
        return $paidAmounts;
    }
    
    /**
     * Génère le code suivant
     * @return string
     */
    public function getNextCode() {
        $lastPaiCode = $this->entity_manager
            ->getRepository('AppBundle:OnePaiement')
            ->getLastCode();

        $lastEncCode = $this->entity_manager
            ->getRepository('AppBundle:OneEncaissement')
            ->getLastCode();

        $paiNumber = (int)explode('-', $lastPaiCode)[1] + 1;
        $encNumber = (int)explode('-', $lastEncCode)[1] + 1;
        $number = max(array($paiNumber, $encNumber));
        
        if ($number < 10) {
            $nextCode = 'ENC-00'.$number;
        } elseif ($number >= 10 && $number < 100) {
            $nextCode = 'ENC-0'.$number;
        } else {
            $nextCode = 'ENC-'.$number;
        }
        return $nextCode;
    }
    
    /**
     * Teste si toute la facture a été payée
     * @param type $facture
     * @param type $factureAmount
     * @return boolean
     */
    public function checkFacturePaiement($facture, $factureAmount) {
        $totalAmount = 0;
        $paiements = $this->entity_manager->getRepository('AppBundle:OnePaiement')->findByOneVente($facture);
        foreach($paiements as $paiement) {
            $totalAmount += $paiement->getMontant();
        }
        if($totalAmount == $factureAmount) {
            return True;
        } else {
            return False;
        }
    }
    
    /**
     * Récypère les types des paiements
     * @return string
     */
    public function getPaiementType() {
        $paiementType = array();
        $paiements = $this->entity_manager->getRepository('AppBundle:OnePaiement')->findAll();
        foreach ($paiements as $paiement) {
            $detail = $this->entity_manager->getRepository('AppBundle:OnePaiementDetail')->findByOnePaiement($paiement);
            if (!$detail) {
                $paiementType[$paiement->getId()] = 'simple';
            } else {
                $paiementType[$paiement->getId()] = 'enc-avo';
            }
        }
        return $paiementType;
    }
}