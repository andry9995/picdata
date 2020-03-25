<?php

/**
 * Created by Netbeans
 * Created on : 15 août 2017, 19:06:16
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Service;

use AppBundle\Entity\OneVente;
use Doctrine\ORM\EntityManager;

class AvoirService
{
    private $entity_manager;
    
    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }

    /**
     * @param array $clientProspects
     * @param $exerice
     * @return array
     */
    public function getLeftAmounts(array $clientProspects, $exercice) {
        $leftAmounts = array();
        /** @var OneVente[] $avoirs */
        $avoirs = $this->entity_manager
            ->getRepository('AppBundle:OneVente')
            ->getVentes($clientProspects, $exercice, 'avoir');
        foreach ($avoirs as $avoir) {
            //Montant HT de l'avoir
            $montantAvoirHT = 0;
            
            //Articles de l'avoir
            $articles = $this->entity_manager
                ->getRepository('AppBundle:OneArticleVente')
                ->findBy(array('vente' => $avoir));

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
                
                //Mise à jour du montant HT de l'avoir
                $montantAvoirHT = $montantAvoirHT + $montantArtTTC;
            }
            
            //Remise de l'avoir
            $montantRemiseAvoir = 0;
            $remiseAvoir = $avoir->getRemise();
            if ($remiseAvoir > 0) $montantRemiseAvoir = ($montantAvoirHT * $remiseAvoir) / 100;
            
            //Montant TTC de l'avoir
            $montantAvoirTTC = $montantAvoirHT - $montantRemiseAvoir;
            
            //Récupération des paiements par avoir
            $paiementDetails = $this->entity_manager->getRepository('AppBundle:OnePaiementDetail')->findByOneAvoir($avoir);
            foreach($paiementDetails as $detail) {
                $montantAvoirTTC -= $detail->getOnePaiement()->getMontant();
            }
            
            //Ajout du montant dans la table de montants restants
            $leftAmounts[$avoir->getId()] = $montantAvoirTTC;
        }
        return $leftAmounts;
    }
}