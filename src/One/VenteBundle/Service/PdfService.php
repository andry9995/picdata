<?php

/**
 * Created by Netbeans
 * Created on : 2 sept. 2017, 12:18:11
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Service;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\OneDevis;
use AppBundle\Entity\OneVente;
use AppBundle\Entity\Soussouscategorie;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Image;
use AppBundle\Entity\Imputation;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\Saisie1;
use AppBundle\Entity\Saisie2;
use AppBundle\Entity\SaisieControle;
use AppBundle\Entity\Separation;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class PdfService
{
    private $entity_manager;
    
    public function __construct(EntityManager $em) {
        $this->entity_manager = $em;
    }
    
    /**
     * CSS pour les impression pdf
     * @return string
     */
    public function css() {
        $css = '<style type="text/css">'
                . '<!--'
                . '.row {width: 100%;margin-right: -15px;margin-left: -15px;}'
                . '.col-md-12 {float: left;width: 100%;position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;}'
                . '.col-md-6 {float: left;width: 50%;position: relative;min-height: 1px;padding-right: 15px;padding-left: 15px;}'
                . '.preview {color: #000000;max-height: 600px;overflow-y: auto;font-size: 14px;}'
                . '.company-name {font-weight: bold;font-size: 1.5em;}'
                . '.doc-name {font-weight: bold;font-size: 1.7em;}'
                . '.doc-identifiant {font-size: 1.2em;}'
                . '.doc-expiration {font-weight: bold;}'
                . '.doc-reglement {font-weight: bold;}'
                . '.client-name {font-weight: bold;}'
                . '.total-ttc {background-color: #F0F0F0;font-size: 1.2em;}'
                . '.table {display: table;width: 100%;max-width: 100%;margin-bottom: 20px;background-color: transparent;border-spacing: 0;border-collapse: collapse;}'
                . '.table-popup {border: none;box-shadow: none;}'
                . '.table th {border-bottom: 1px solid #DDDDDD;}'
                . '.table-popup th, .table-popup td {padding-bottom: 15px !important;padding-top: 15px !important;}'
                . '.table-popup th {background-color: #F9F9F9;}'
                . '-->'
                . '</style>';
        return $css;
    }


    /**
     * Enregistrement any @ table Image (Consultation pièce)
     * @param Utilisateur $user
     * @param Dossier $dossier
     * @param $exercice
     * @param Soussouscategorie $soussouscategorie
     * @param OneDevis $devis
     * @param OneVente $vente
     * @param $dateScan
     * @param $filePath
     * @param $filename
     * @param $name
     * @param $ext
     */
    public function saveImage($user, $dossier, $exercice, $soussouscategorie, $devis, $vente, $dateScan, $filePath, $filename, $name, $ext){

        $directory = "IMAGES/" . $dateScan->format("Ymd");
        $new = false;
        $typeDoc = '';

        if($devis !== null){
            $typeDoc = 'devis';

            if($devis->getImage() === null){
                $new = true;
            }
        }
        elseif ($vente !== null){
            $typeDoc = 'vente';

            if($vente->getImage() === null){
                $new  = true;
            }
        }

        if($new) {

            $em = $this->entity_manager;

            $fs = new Filesystem();
            try {
                $fs->mkdir($directory, 0777);
            } catch (IOExceptionInterface $e) {
            }

            copy($filePath, $directory . '/' . $filename);
            $newName = Boost::getUuid();
            $fs->rename($directory . '/' . $filename, $directory . '/' . $newName . '.' . $ext);

            $lot = $this->entity_manager
                ->getRepository('AppBundle:Lot')
                ->getNewLot($dossier, $user, '', null, $dateScan);

            $lot->setStatus(4);
            $em->flush();

            $image = new Image();
            $image->setDownload(new \DateTime('now'));
            $image->setLot($lot);
            $image->setExercice($exercice);
            $image->setExtImage($ext);
            $image->setNbpage(1);
            $image->setNomTemp($newName);
            $image->setOriginale($name);
            $image->setSourceImage($this->entity_manager
                ->getRepository('AppBundle:SourceImage')
                ->getBySource('TPE'));
            $image->setDownload(new \DateTime('now'));
            $image->setSaisie1(3);
            $image->setSaisie2(3);
            $image->setCtrlSaisie(3);
            $image->setImputation(3);
            $image->setCtrlImputation(3);

            $em->persist($image);
            $em->flush();


            switch ($typeDoc){
                case 'devis':
                    $devis->setImage($image);
                    $em->flush();
                    break;

                case 'vente':
                    $vente->setImage($image);
                    $em->flush();
                    break;
                default:
                    break;
            }


            // 240: Robot
            $operateur = $this->entity_manager
                ->getRepository('AppBundle:Operateur')
                ->find(240);

            $sepation = new Separation();
            $sepation->setSoussouscategorie($soussouscategorie);
            $sepation->setSouscategorie($soussouscategorie->getSouscategorie());
            $sepation->setCategorie($soussouscategorie->getSouscategorie()->getCategorie());
            $sepation->setImage($image);
            $sepation->setOperateur($operateur);
            $em->persist($sepation);
            $em->flush();

            $saisie1 = new Saisie1();
            $saisie1->setImage($image);
            $saisie1->setSoussouscategorie($soussouscategorie);

            $em->persist($saisie1);
            $em->flush($saisie1);

            $saisie2 = new Saisie2();
            $saisie2->setImage($image);
            $saisie2->setSoussouscategorie($soussouscategorie);

            $em->persist($saisie2);
            $em->flush($saisie2);

            $ctrlSaisie = new SaisieControle();
            $ctrlSaisie->setImage($image);
            $ctrlSaisie->setSoussouscategorie($soussouscategorie);

            $em->persist($ctrlSaisie);
            $em->flush($ctrlSaisie);

            $imputation = new Imputation();
            $imputation->setImage($image);
            $imputation->setSoussouscategorie($soussouscategorie);

            $em->persist($imputation);
            $em->flush($imputation);

            $ctrlImputation = new ImputationControle();
            $ctrlImputation->setImage($image);
            $ctrlImputation->setSoussouscategorie($soussouscategorie);

            $em->persist($ctrlImputation);
            $em->flush($ctrlImputation);
        }
        //Ecraser-na ilay fichier taloha dia soloana an'ilay vaovao
        else {
            $newName = '';
            $fs = new Filesystem();
            switch ($typeDoc) {
                case 'devis':
                    $newName = $devis->getImage()->getNom();
                    break;
                case 'vente':
                    $newName = $vente->getImage()->getNom();
                    break;
                default:
                    break;
            }
            if ($newName !== '') {
                $fs->remove($directory . '/' . $newName . '.' . $ext);
                copy($filePath, $directory . '/' . $filename);
                $fs->rename($directory . '/' . $filename, $directory . '/' . $newName . '.' . $ext);
            }
        }

    }
}