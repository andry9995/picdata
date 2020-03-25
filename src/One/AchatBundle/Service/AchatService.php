<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 25/04/2018
 * Time: 09:39
 */

namespace One\AchatBundle\Service;


use AppBundle\Entity\OneAchat;
use AppBundle\Entity\OneArticleAchat;
use AppBundle\Entity\OneContactFournisseur;
use AppBundle\Entity\OneFournisseur;
use AppBundle\Entity\OneInvoiceCommandeAchat;
use Doctrine\ORM\EntityManager;

class AchatService
{
    private $entity_manager;

    public function __construct(EntityManager $em)
    {
        $this->entity_manager = $em;
    }

    public function getAddress($type, $id) {
        $address = '';
        if ($type === 'contact' && $id > 0) {
            /** @var OneContactFournisseur $contact */
            $contact = $this->entity_manager->getRepository('AppBundle:OneContactFournisseur')->find($id);
            if ($contact->getAdresse() !== '')
                $address .= $contact->getAdresse().'<br>';


            if ($contact->getCodePostal() != '')
                $address .= $contact->getCodePostal().' ';

            if ($contact->getVille() != '')
                $address .= $contact->getVille().'<br>';
            elseif ($contact->getCodePostal() == '')
                $address .= '<br>';

            if ($contact->getPays())
                $address .= $contact->getPays()->getNom();
        } else {
            /** @var OneFournisseur $fournisseur */
            $fournisseur = $this->entity_manager->getRepository('AppBundle:OneFournisseur')->find($id);
            if ($fournisseur->getAdresse() != '')
                $address .= $fournisseur->getAdresse().'<br>';

            if ($fournisseur->getCodePostal() != '')
                $address .= $fournisseur->getCodePostal().' ';

            if ($fournisseur->getVille() != '')
                $address .= $fournisseur->getVille().'<br>';


            if ($fournisseur->getPays())
                $address .= $fournisseur->getPays()->getNom();
        }
        return $address;
    }


    /**
     * Génère le code suivant
     * @return string
     */
    public function getNextCodeAchat($type='facture') {
        if ($type === 'facture')
            $startcode = 'FAC-';
        elseif ($type === 'commande')
            $startcode = 'COM-';

        $lastCode = $this->entity_manager->getRepository('AppBundle:OneAchat')->getLastCodeAchat($type);
        $number = (int)explode('-', $lastCode)[1] + 1;
        if ($number < 10) {
            $nextCode = $startcode.'00'.$number;
        } elseif ($number > 10 && $number < 100) {
            $nextCode = $startcode.'0'.$number;
        } else {
            $nextCode = $startcode.$number;
        }
        return $nextCode;
    }


    public function getAchatDetails(array $fournisseurs, $type='facture') {
        $achatDetails = array();

        /** @var OneAchat[] $achats */
        $achats = $this->entity_manager
            ->getRepository('AppBundle:OneAchat')
            ->getAchats($fournisseurs, $type);

        foreach ($achats as $achat) {
            $ht = 0;
            $tva = 0;
            $remise = 0;
            $remise_tva = 0;

            //Articles de la achat
            /** @var OneArticleAchat[] $articles */
            $articles = $this->entity_manager
                ->getRepository('AppBundle:OneArticleAchat')
                ->findBy(array('achat' => $achat));
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

            //Remise de la achat
            $achat_remise = $achat->getRemise();
            if ($achat_remise > 0) {
                $remise = ($ht * $achat_remise) / 100;
            }

            //Remise TVA
            if ($achat_remise > 0 && $tva > 0) {
                $remise_tva = ($tva * $achat_remise) / 100;
                $tva = $tva - $remise_tva;
            }

            //Montant TTC de la vente
            $ttc = ($ht - $remise) + $tva;

            //Ajout du montant dans la table de montants des ventes
            $achatDetails[$achat->getId()]['ht'] = $ht;
            $achatDetails[$achat->getId()]['remise'] = $remise;
            $achatDetails[$achat->getId()]['tva'] = $tva;
            $achatDetails[$achat->getId()]['ttc'] = $ttc;
        }
        return $achatDetails;
    }


    /**
     * Récupération des IDs des commandes facturées
     * @return array
     */
    public function getInvoicedCommande() {
        $invoicedCommandes = array();

        /** @var OneInvoiceCommandeAchat[] $invoiced */
        $invoiced = $this->entity_manager
            ->getRepository('AppBundle:OneInvoiceCommandeAchat')
            ->findAll();

        foreach($invoiced as $item) {
            $invoicedCommandes[] = $item->getOneCommande()->getId();
        }
        return $invoicedCommandes;
    }

}