<?php

/**
 * Created by Netbeans
 * Created on : 16 juil. 2017, 13:22:55
 * Author : Mamy Rakotonirina
 */

namespace One\ProspectBundle\Service;

use AppBundle\Entity\OneFichier;
use Doctrine\ORM\EntityManager;

class FichierService
{
    private $entity_manager;
    
    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }
    
    /**
     * Récupère les données de formulaire sérialisées
     * @param type $fullname
     * @return array
     */
    public function parseFile($fullname) {
        //preg_match('/(.*)\.([^.]*)$/', $fullname, $matches);
        $filepath = substr($fullname, 0, strrpos($fullname, '/')).'/';
        $filename = substr($fullname, strrpos($fullname, '/') + 1);
        return array('filepath' => $filepath, 'filename' => $filename);
    }
    
    /**
     * Enregistrement des données
     * @param array $parsedData
     * @return \One\ProspectBundle\Service\Response
     */
    public function saveData($parsedData) {
        try {
            $oldFichier = $this->entity_manager->getRepository('AppBundle:OneFichier')->findOneByNom($parsedData['filename']);
            if (!is_object($oldFichier)) {
                $fichier = new OneFichier();
                $fichier->setNom($parsedData['filename']);
                $fichier->setPath($parsedData['filepath']);

                $this->entity_manager->persist($fichier);
                $this->entity_manager->flush();
                return $fichier->getId();
            } else {
                return $oldFichier->getId();
            }
        } catch (Exception $ex) {
            return False;
        }
    }
}