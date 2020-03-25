<?php

/**
 * Created by Netbeans
 * Created on : 3 juil. 2017, 10:36:27
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Service;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\OneArticleAchat;
use AppBundle\Entity\OneArticleOpp;
use AppBundle\Entity\OneArticleVente;
use Doctrine\ORM\EntityManager;

class ArticleService
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
    public function parseArticleData($data) {
        $fields = [];
        $keyvalues = explode('&', $data);
        foreach ($keyvalues as $keyvalue) {
            $kv = explode('=', $keyvalue);
            $fields[$kv[0]] = urldecode($kv[1]);
            if ($kv[0] == 'quantite' || $kv[0] == 'montant' || $kv[0] == 'price' || $kv[0] == 'remise' || $kv[0] == 'tva')
                $fields[$kv[0]] = floatval($kv[1]);
        }
        return $fields;
    }
    
    /**
     * Enregistrement des articles pour opportunité
     * @param array $parsedData
     * @return \One\VenteBundle\Service\Response
     */
    public function saveArticleOppData($parsedData) {
        //if (!isset($parsedData['id'])) $parsedData['id'] = 0;
        //Ajout
        if (intval($parsedData['id']) == 0 ) {
            try {
                $articleOpp = new OneArticleOpp();
                //Récupération des tables liées
                $opportunite = $this->entity_manager->getRepository('AppBundle:OneOpportunite')->find($parsedData['opportunite-id']);
                $article = $this->entity_manager->getRepository('AppBundle:OneArticle')->find($parsedData['article-id']);
                
                $articleOpp->setOneArticle($article);
                $articleOpp->setOpportunite($opportunite);
                $articleOpp->setQuantite($parsedData['quantite']);
                $articleOpp->setPrix($parsedData['montant']);
                
                $this->entity_manager->persist($articleOpp);
                $this->entity_manager->flush();
                return $articleOpp->getId();
            } catch (Exception $ex) {
                return False;
            }
        } else {
            try {
                $articleOpp = $this->entity_manager->getRepository('AppBundle:OneArticleOpp')->find($parsedData['id']);
                //Récupération des tables liées
                $opportunite = $this->entity_manager->getRepository('AppBundle:OneOpportunite')->find($parsedData['opportunite-id']);
                $article = $this->entity_manager->getRepository('AppBundle:OneArticle')->find($parsedData['article-id']);
                
                $articleOpp->setOneArticle($article);
                $articleOpp->setOpportunite($opportunite);
                $articleOpp->setQuantite($parsedData['quantite']);
                $articleOpp->setPrix($parsedData['montant']);
                
                $this->entity_manager->flush();
                return $articleOpp->getId();
            } catch (Exception $ex) {
                return False;
            }
        }
    }
    
    /**
     * Enregistrement des articles pour devis
     * @param array $parsedData
     * @return \One\VenteBundle\Service\Response
     */
    public function saveArticleDevis($parsedData) {
        //if (!isset($parsedData['id'])) $parsedData['id'] = 0;
        //Ajout
        if ((int)$parsedData['id'] == 0 ) {
            try {
                $articleVente = new OneArticleVente();
                //Récupération des tables liées
                $devis = $this->entity_manager
                    ->getRepository('AppBundle:OneDevis')
                    ->find($parsedData['devis-id']);
                $article = $this->entity_manager
                    ->getRepository('AppBundle:OneArticle')
                    ->find($parsedData['article-id']);
                $tva = $this->entity_manager
                    ->getRepository('AppBundle:OneTva')
                    ->findOneByTaux($parsedData['tva']);
                
                $articleVente->setOneArticle($article);
                $articleVente->setDevis($devis);
                $articleVente->setDescription($parsedData['description']);
                $articleVente->setQuantite($parsedData['quantite']);
                $articleVente->setPrix($parsedData['price']);
                $articleVente->setRemise($parsedData['remise']);
                $articleVente->setTvaTaux($tva);
                
                $this->entity_manager->persist($articleVente);
                $this->entity_manager->flush();
                return $articleVente->getId();
            } catch (\Exception $ex) {
                return False;
            }
        } 
        //Edition
        else {
            try {
                $articleVente = $this->entity_manager
                    ->getRepository('AppBundle:OneArticleVente')
                    ->find($parsedData['id']);
                //Récupération des tables liées
                $devis = $this->entity_manager
                    ->getRepository('AppBundle:OneDevis')
                    ->find($parsedData['devis-id']);
                $article = $this->entity_manager
                    ->getRepository('AppBundle:OneArticle')
                    ->find($parsedData['article-id']);
                $tva = $this->entity_manager
                    ->getRepository('AppBundle:OneTva')
                    ->findOneByTaux($parsedData['tva']);
                
                $articleVente->setOneArticle($article);
                $articleVente->setDevis($devis);
                $articleVente->setDescription($parsedData['description']);
                $articleVente->setQuantite($parsedData['quantite']);
                $articleVente->setPrix($parsedData['price']);
                $articleVente->setRemise($parsedData['remise']);
                $articleVente->setTvaTaux($tva);
                
                $this->entity_manager->flush();
                return $articleVente->getId();
            } catch (\Exception $ex) {
                return False;
            }
        }
    }
    
    /**
     * Enregistrement des articles pour vente
     * @param array $parsedData
     * @return \One\VenteBundle\Service\Response
     */
    public function saveArticleVente($parsedData) {
        //if (!isset($parsedData['id'])) $parsedData['id'] = 0;
        //Ajout
        if ((int)$parsedData['id'] == 0 ) {
            try {
                $articleVente = new OneArticleVente();
                //Récupération des tables liées
                $vente = $this->entity_manager
                    ->getRepository('AppBundle:OneVente')
                    ->find($parsedData['vente-id']);

                $article = $this->entity_manager
                    ->getRepository('AppBundle:OneArticle')
                    ->find($parsedData['article-id']);

                $tva = $this->entity_manager
                    ->getRepository('AppBundle:OneTva')
                    ->findOneByTaux($parsedData['tva']);
                
                $articleVente->setOneArticle($article);
                $articleVente->setVente($vente);
                $articleVente->setDescription($parsedData['description']);
                $articleVente->setQuantite($parsedData['quantite']);
                $articleVente->setPrix($parsedData['price']);
                $articleVente->setRemise($parsedData['remise']);
                $articleVente->setTvaTaux($tva);
                
                $this->entity_manager->persist($articleVente);
                $this->entity_manager->flush();
                return $articleVente->getId();
            } catch (Exception $ex) {
                return False;
            }
        } 
        //Edition
        else {
            try {
                $articleVente = $this->entity_manager->getRepository('AppBundle:OneArticleVente')->find($parsedData['id']);
                //Récupération des tables liées
                $vente = $this->entity_manager
                    ->getRepository('AppBundle:OneVente')
                    ->find($parsedData['vente-id']);
                $article = $this->entity_manager
                    ->getRepository('AppBundle:OneArticle')
                    ->find($parsedData['article-id']);
                $tva = $this->entity_manager
                    ->getRepository('AppBundle:OneTva')
                    ->findOneByTaux($parsedData['tva']);
                
                $articleVente->setOneArticle($article);
                $articleVente->setVente($vente);
                $articleVente->setDescription($parsedData['description']);
                $articleVente->setQuantite($parsedData['quantite']);
                $articleVente->setPrix($parsedData['price']);
                $articleVente->setRemise($parsedData['remise']);
                $articleVente->setTvaTaux($tva);
                
                $this->entity_manager->flush();
                return $articleVente->getId();
            } catch (\Exception $ex) {
                return False;
            }
        }
    }


    /**
     * @param $parsedData
     * @return bool|int
     */
    public function saveArticleAchat($parsedData) {
        //if (!isset($parsedData['id'])) $parsedData['id'] = 0;
        //Ajout
        if ((int)$parsedData['id'] == 0 ) {
            try {
                $articleAchat = new OneArticleAchat();
                //Récupération des tables liées
                $achat = $this->entity_manager
                    ->getRepository('AppBundle:OneAchat')
                    ->find($parsedData['achat-id']);
                $article = $this->entity_manager->getRepository('AppBundle:OneArticle')->find($parsedData['article-id']);
                $tva = $this->entity_manager->getRepository('AppBundle:TvaTaux')->findOneByTaux($parsedData['tva']);

                $articleAchat->setOneArticle($article);
                $articleAchat->setAchat($achat);
                $articleAchat->setDescription($parsedData['description']);
                $articleAchat->setQuantite($parsedData['quantite']);
                $articleAchat->setPrix($parsedData['price']);
                $articleAchat->setRemise($parsedData['remise']);
                $articleAchat->setTvaTaux($tva);

                $this->entity_manager->persist($articleAchat);
                $this->entity_manager->flush();

                return $articleAchat->getId();

            } catch (\Exception $ex) {
                return False;
            }
        }
        //Edition
        else {
            try {
                $articleAchat = $this->entity_manager
                    ->getRepository('AppBundle:OneArticleAchat')
                    ->find($parsedData['id']);
                //Récupération des tables liées

                $achat = $this->entity_manager
                    ->getRepository('AppBundle:OneAchat')
                    ->find($parsedData['achat-id']);

                $article = $this->entity_manager
                    ->getRepository('AppBundle:OneArticle')
                    ->find($parsedData['article-id']);

                $tva = $this->entity_manager
                    ->getRepository('AppBundle:TvaTaux')
                    ->findOneByTaux($parsedData['tva']);

                $articleAchat->setOneArticle($article);
                $articleAchat->setAchat($achat);
                $articleAchat->setDescription($parsedData['description']);
                $articleAchat->setQuantite($parsedData['quantite']);
                $articleAchat->setPrix($parsedData['price']);
                $articleAchat->setRemise($parsedData['remise']);
                $articleAchat->setTvaTaux($tva);

                $this->entity_manager->flush();
                return $articleAchat->getId();

            } catch (\Exception $ex) {
                return False;
            }
        }
    }










    
    /**
     * Génère le code suivant
     * @return string
     */
    public function getNextCode(Dossier $dossier) {
        $lastCode = $this->entity_manager
            ->getRepository('AppBundle:OneArticle')
            ->getLastCode($dossier);

        $number = (int)explode('-', $lastCode)[1] + 1;
        if ($number < 10) {
            $nextCode = 'ART-00'.$number;
        } elseif ($number >= 10 && $number < 100) {
            $nextCode = 'ART-0'.$number;
        } else {
            $nextCode = 'ART-'.$number;
        }
        return $nextCode;
    }

    /**
     * @param $customCode
     * @param Dossier $dossier
     * @return string
     */
    public function getNextCustomCode($customCode, Dossier $dossier) {
        $customCode = strtoupper($customCode);
        $prefixe = '';
        $suffixe = '';
        for ($i = 0; $i <= strlen($customCode)-1; $i++) {
            if(is_numeric($customCode[$i]))  {
               $suffixe .= $customCode[$i];
            } else {
                $prefixe .= $customCode[$i];
            }
        }
        
        $lastCustomCode = $this->entity_manager
            ->getRepository('AppBundle:OneArticle')
            ->getLastCustomCode($prefixe, $dossier);
        $number = (int)str_replace($prefixe, '', $lastCustomCode) + 1;
        if ($number < 10) {
            $nextCode = $prefixe.'00'.$number;
        } elseif ($number >= 10 && $number < 100) {
            $nextCode = $prefixe.'0'.$number;
        } else {
            $nextCode = $prefixe.$number;
        }
        return $nextCode;
    }
    
    /**
     * Détection du pays de l'utilisateur
     * @return string $country
     */
    public function getMyCountry() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ipdetails = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        if ($ip == '127.0.0.1') 
            $country = 'MG';
        else
            $country = $ipdetails->country;
        return $country;
    }
}