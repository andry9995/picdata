<?php

/**
 * Created by Netbeans
 * Created on : 3 juil. 2017, 11:07:56
 * Author : Mamy Rakotonirina
 */

namespace One\ProspectBundle\Service;

use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityManager;

class ClientProspectService
{
    private $entity_manager;
    
    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }
    
    /**
     * Génère le CodeClient suivant
     * @return string
     */
    public function getNextCode(Dossier $dossier) {
        $lastCode = $this->entity_manager
            ->getRepository('AppBundle:Tiers')
            ->getLastCode($dossier);

        $number = (int)explode('-', $lastCode)[1] + 1;
        if ($number < 10) {
            $nextCode = 'CLI-00'.$number;
        } elseif ($number > 10 && $number < 100) {
            $nextCode = 'CLI-0'.$number;
        } else {
            $nextCode = 'CLI-'.$number;
        }
        return $nextCode;
    }
    
    public function getNextCustomCode(Dossier $dossier, $customCode) {
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
            ->getRepository('AppBundle:Tiers')
            ->getLastCustomCode($dossier, $prefixe);

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
            if ($kv[0] == 'id')
                $fields[$kv[0]] = intval($kv[1]);
        }
        return $fields;
    }
}