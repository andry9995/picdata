<?php

/**
 * Created by Netbeans
 * Created on : 2 sept. 2017, 12:18:11
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\OneDocumentModele;

class DocumentService
{
    private $entity_manager;
    
    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }

    /**
     * Ajout d'un modèle de document standard
     * @param $type
     * @param $doc
     */
    public function addDocumentModele($type, $doc) {
        $modele = $this->entity_manager->getRepository('AppBundle:OneModele')->find(1);
        $document = new OneDocumentModele();
        
        if ($type === 'devis') {
            $document->setDevis($doc);
        } else if ($type === 'vente') {
            $document->setVente($doc);
        } else if ($type === 'encaissement') {
            $document->setEncaissement($doc);
        } else if ($type === 'paiement') {
            $document->setPaiement($doc);
        } else if ($type === 'achat'){
            $document->setAchat($doc);
        }
        
        $document->setModele($modele);
        $document->setHeadColor($modele->getHeadColor());
        $document->setFontFamily($modele->getFontFamily());
        $document->setFontSize($modele->getFontSize());
        $document->setFontColor($modele->getFontColor());
        $document->setShowCompanyName($modele->getShowCompanyName());
        $document->setShowReglement($modele->getShowReglement());
        $document->setShowNumClient($modele->getShowNumClient());
        $document->setShowTelClient($modele->getShowTelClient());
        $document->setShowShippingAddress($modele->getShowShippingAddress());
        $document->setShippingAddressLabel($modele->getShippingAddressLabel());
        $document->setBillingAddressRight($modele->getBillingAddressRight());
        $document->setBillingAddressLabel($modele->getBillingAddressLabel());
        $document->setShowTvaIntracom($modele->getShowTvaIntracom());
        $document->setDesignationLabel($modele->getDesignationLabel());
        $document->setShowQuantity($modele->getShowQuantity());
        $document->setQuantityLabel($modele->getQuantityLabel());
        $document->setShowPrice($modele->getShowPrice());
        $document->setPriceLabel($modele->getPriceLabel());
        $document->setShowUnit($modele->getShowUnit());
        $document->setShowProductCode($modele->getShowProductCode());
        $document->setShowPaymentInfo($modele->getShowPaymentInfo());
        $document->setPaymentInfoLabel($modele->getPaymentInfoLabel());
        $document->setShowDeadline($modele->getShowDeadline());
        $document->setPaymentInfoDefault($modele->getPaymentInfoDefault());
        $document->setGlobalNote($modele->getGlobalNote());
        $document->setCustomized(0);
        
        $this->entity_manager->persist($document);
        $this->entity_manager->flush();
    }
}