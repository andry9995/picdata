<?php

/**
 * Created by Netbeans
 * Created on : 15 août 2017, 19:06:16
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Service;

use AppBundle\Entity\OneEncaissement;
use Doctrine\ORM\EntityManager;

class EncaissementService
{
    private $entity_manager;
    
    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }
    
    /**
     * Récupère les données de formulaire sérialisées
     * @param type $data
     * @return array
     */
    public function parseData($data) {
        $fields = [];
        $keyvalues = explode('&', $data);
        foreach ($keyvalues as $keyvalue) {
            $kv = explode('=', $keyvalue);
            $fields[$kv[0]] = urldecode($kv[1]);
            if ($kv[0] == 'quantite' || $kv[0] == 'montant' || $kv[0] == 'price' || $kv[0] == 'remise')
                $fields[$kv[0]] = floatval($kv[1]);
        }
        return $fields;
    }
    
    /**
     * Génère le code suivant
     * @return string
     */
    public function getNextCode() {
        $lastPaiCode = $this->entity_manager->getRepository('AppBundle:OnePaiement')->getLastCode();
        $lastEncCode = $this->entity_manager->getRepository('AppBundle:OneEncaissement')->getLastCode();
        $paiNumber = intval(explode('-', $lastPaiCode)[1]) + 1;
        $encNumber = intval(explode('-', $lastEncCode)[1]) + 1;
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
     * Calcul des montants des encaissements
     * @return array
     */
    public function getEncaissementAmounts(array $clientProspects, $exercice) {
        $encaissementAmounts = array();

        /** @var OneEncaissement[] $encaissements */
        $encaissements = $this->entity_manager
            ->getRepository('AppBundle:OneEncaissement')
            ->getEncaissements($clientProspects, $exercice);

        foreach ($encaissements as $encaissement) {
            $montant = 0;
            $articles = $this->entity_manager
                ->getRepository('AppBundle:OneEncaissementDetail')
                ->findBy(array('oneEncaissement' => $encaissement));

            foreach ($articles as $article) {
                $montant = $montant + $article->getMontant();
            }
            $encaissementAmounts[$encaissement->getId()] = $montant;
        }
        return $encaissementAmounts;
    }
}