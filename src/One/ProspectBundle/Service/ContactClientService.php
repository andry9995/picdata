<?php

/**
 * Created by Netbeans
 * Created on : 3 juil. 2017, 10:36:27
 * Author : Mamy Rakotonirina
 */

namespace One\ProspectBundle\Service;

use AppBundle\Entity\OneContactClient;
use Doctrine\ORM\EntityManager;

class ContactClientService
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
        //id=&nom=Castellan&prenom=Philippe&email=info%40scriptura.biz&tel-portable=&tel-pro=&tel-perso=&adresse-1=&adresse-2=&ville=Antananarivo&code-postal=101&pays=130&note=&service=&fonction=
        $fields = [];
        $keyvalues = explode('&', $data);
        foreach ($keyvalues as $keyvalue) {
            $kv = explode('=', $keyvalue);
            $fields[$kv[0]] = urldecode($kv[1]);
            if ($kv[0] == 'id' || $kv[0] == 'pays')
                $fields[$kv[0]] = intval($kv[1]);
            if ($kv[0] == 'nom' || $kv[0] == 'prenom')
                $fields[$kv[0]] = str_replace ('+', ' ', $kv[1]);
        }
        return $fields;
    }
    
    /**
     * Enregistrement des données
     * @param array $parsedData
     * @return \One\ProspectBundle\Service\Response
     */
    public function saveData($parsedData) {
        if (!isset($parsedData['id'])) $parsedData['id'] = 0;
        //Ajout
        if ($parsedData['id'] == 0 ) {
            try {
                $contact = new OneContactClient();
                //Récupération des tables liées
//                $country = $this->entity_manager->getRepository('AppBundle:OnePays')->find($parsedData['pays']);

                $country = $this->entity_manager
                    ->getRepository('AppBundle:Pays')
                    ->find($parsedData['pays']);

                $tiers = $this->entity_manager
                    ->getRepository('AppBundle:Tiers')
                    ->find($parsedData['client-prospect']);
                
                $contact->setNom($parsedData['nom']);
                $contact->setPrenom($parsedData['prenom']);
                $contact->setEmail($parsedData['email']);
                $contact->setTelPortable($parsedData['tel-portable']);
                $contact->setTelPro($parsedData['tel-pro']);
                $contact->setTelPerso($parsedData['tel-perso']);
                $contact->setAdresse1($parsedData['adresse-1']);
                $contact->setAdresse2($parsedData['adresse-2']);
                $contact->setVille($parsedData['ville']);
                $contact->setCodePostal($parsedData['code-postal']);
//                $contact->setOnePays($country);
                $contact->setPays($country);
                $contact->setTiers($tiers);
                $contact->setService($parsedData['service']);
                $contact->setFonction($parsedData['fonction']);

                $this->entity_manager->persist($contact);
                $this->entity_manager->flush();
                return $contact->getId();
            } catch (Exception $ex) {
                return False;
            }
        } else {
            try {
                $contact = $this->entity_manager
                    ->getRepository('AppBundle:OneContactClient')
                    ->find($parsedData['id']);
                //Récupération des tables liées
//                $country = $this->entity_manager->getRepository('AppBundle:OnePays')->find($parsedData['pays']);
                $country = $this->entity_manager
                    ->getRepository('AppBundle:Pays')
                    ->find($parsedData['pays']);

                $clientProspect = $this->entity_manager
                    ->getRepository('AppBundle:Tiers'
                    )->find($parsedData['client-prospect']);
                
                $contact->setNom($parsedData['nom']);
                $contact->setPrenom($parsedData['prenom']);
                $contact->setEmail($parsedData['email']);
                $contact->setTelPortable($parsedData['tel-portable']);
                $contact->setTelPro($parsedData['tel-pro']);
                $contact->setTelPerso($parsedData['tel-perso']);
                $contact->setAdresse1($parsedData['adresse-1']);
                $contact->setAdresse2($parsedData['adresse-2']);
                $contact->setVille($parsedData['ville']);
                $contact->setCodePostal($parsedData['code-postal']);
//                $contact->setOnePays($country);
                $contact->setPays($country);
                $contact->setTiers($clientProspect);
                $contact->setService($parsedData['service']);
                $contact->setFonction($parsedData['fonction']);
                
                $this->entity_manager->flush();
                return $contact->getId();
            } catch (Exception $ex) {
                return False;
            }
        }
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