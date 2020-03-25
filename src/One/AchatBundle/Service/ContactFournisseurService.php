<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 04/04/2018
 * Time: 15:50
 */

namespace One\AchatBundle\Service;


use AppBundle\Entity\OneContactFournisseur;
use AppBundle\Entity\Pays;
use Doctrine\ORM\EntityManager;

class ContactFournisseurService
{
    private $entity_manager;

    public function __construct(EntityManager $em)
    {
        $this->entity_manager = $em;
    }

    /**
     * Récupère les données de formulaire sérialisées
     * @return array
     */
    public function parseData($data) {
        //id=&nom=Castellan&prenom=Philippe&email=info%40scriptura.biz&tel-portable
        $fields = [];
        $keyvalues = explode('&', $data);
        foreach ($keyvalues as $keyvalue) {
            $kv = explode('=', $keyvalue);
            $fields[$kv[0]] = urldecode($kv[1]);
            if ($kv[0] === 'id' || $kv[0] === 'pays')
                $fields[$kv[0]] = (int)$kv[1];
            if ($kv[0] === 'nom' || $kv[0] === 'prenom')
                $fields[$kv[0]] = str_replace ('+', ' ', $kv[1]);
        }
        return $fields;
    }

    /**
     * Enregistrement des données
     * @param array $parsedData
     * @return int
     */
    public function saveData($parsedData) {
        if (!isset($parsedData['id'])) $parsedData['id'] = 0;
        //Ajout
        if ($parsedData['id'] == 0 ) {
            try {
                $contact = new OneContactFournisseur();

                $fournisseur = $this->entity_manager
                    ->getRepository('AppBundle:OneFournisseur')
                    ->find($parsedData['fournisseur']);

                /** @var Pays $pays */
                $pays = $this->entity_manager
                    ->getRepository('AppBundle:Pays')
                    ->find($parsedData['pays']);

                $contact->setNom($parsedData['nom']);
                $contact->setPrenom($parsedData['prenom']);
                $contact->setEmail($parsedData['email']);
                $contact->setTelephone($parsedData['tel-portable']);
                $contact->setPays($pays);


                $contact->setCodePostal($parsedData['code-postal']);
                $contact->setVille($parsedData['ville']);
                $contact->setAdresse($parsedData['adresse']);


                $contact->setOneFournisseur($fournisseur);

                $this->entity_manager->persist($contact);
                $this->entity_manager->flush();

                return $contact->getId();
            } catch (\Exception $ex) {
                return False;
            }
        } else {
            try {
                $contact = $this->entity_manager
                    ->getRepository('AppBundle:OneContactFournisseur')
                    ->find($parsedData['id']);


                $fournisseur = $this->entity_manager
                    ->getRepository('AppBundle:OneFournisseur')
                    ->find($parsedData['fournisseur']);

                /** @var Pays $pays */
                $pays = $this->entity_manager
                    ->getRepository('AppBundle:Pays')
                    ->find($parsedData['pays']);

                $contact->setNom($parsedData['nom']);
                $contact->setPrenom($parsedData['prenom']);
                $contact->setEmail($parsedData['email']);
                $contact->setTelephone($parsedData['tel-portable']);
                $contact->setPays($pays);


                $contact->setCodePostal($parsedData['code-postal']);
                $contact->setVille($parsedData['ville']);
                $contact->setAdresse($parsedData['adresse']);

                $contact->setOneFournisseur($fournisseur);

                $this->entity_manager->flush();
                return $contact->getId();
            } catch (\Exception $ex) {
                return False;
            }
        }
    }
}