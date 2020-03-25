<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 04/04/2018
 * Time: 15:37
 */

namespace One\AchatBundle\Service;


use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityManager;

class FournisseurService
{
    private $entity_manager;

    public function __construct(EntityManager $em)
    {
        $this->entity_manager = $em;
    }

    /**
     * Génère le CodeFournisseur suivant
     * @return string
     */
    public function getNextCode(Dossier $dossier) {
        $lastCode = $this->entity_manager
            ->getRepository('AppBundle:OneFournisseur')->getLastCode($dossier);
        $number = (int)explode('-', $lastCode)[1] + 1;
        if ($number < 10) {
            $nextCode = 'FOU-00'.$number;
        } elseif ($number > 10 && $number < 100) {
            $nextCode = 'FOU-0'.$number;
        } else {
            $nextCode = 'FOU-'.$number;
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
            ->getRepository('AppBundle:OneFournisseur')
            ->getLastCustomCode($dossier, $prefixe);
        $number = (int)(str_replace($prefixe, '', $lastCustomCode)) + 1;
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
     * Récupère les données de formulaire sérialisées
     * @param $data
     * @return array
     */
    public function parseData($data) {
        $fields = [];
        $keyvalues = explode('&', $data);
        foreach ($keyvalues as $keyvalue) {
            $kv = explode('=', $keyvalue);
            $fields[$kv[0]] = urldecode($kv[1]);
            if ($kv[0] === 'id')
                $fields[$kv[0]] = (int)$kv[1];
        }
        return $fields;
    }

}