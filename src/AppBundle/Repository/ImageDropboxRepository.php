<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 03/10/2019
 * Time: 08:51
 */

namespace AppBundle\Repository;


use Alorel\Dropbox\Exception\NoTokenException;
use Alorel\Dropbox\Operation\Files\Copy;
use Alorel\Dropbox\Operation\Files\CreateFolder;
use Alorel\Dropbox\Operation\Files\Delete;
use Alorel\Dropbox\Operation\Files\Download;
use Alorel\Dropbox\Operation\Files\ListFolder\ListFolder;
use Alorel\Dropbox\Options\Option;
use AppBundle\Controller\Boost;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageATraiter;
use AppBundle\Entity\ImageDropbox;
use AppBundle\Entity\LogActivite;
use AppBundle\Entity\LotGroup;
use AppBundle\Entity\SourceImage;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class ImageDropboxRepository extends EntityRepository
{


//    private $token = '7RzmJDbXU4AAAAAAAAAAOyuruqKq80GCZy0KR0sSqpHzPOorod6FifqknHBX-yPX';
    private $token = '7RzmJDbXU4AAAAAAAAAATj6u3teIqOL8W-GjkbpILuVud16BACppThmbvQney4SV';

    public function parseDropbox($init, &$dossierInconnus){

        $now = new \DateTime();
        $em = $this->getEntityManager();

        $dossierInconnus = [];

        if($init == 0) {
            $existants = $this->getEntityManager()
                ->getRepository('AppBundle:ImageDropbox')
                ->findBy([
                    'status' => 0,
                    'dossier' => null
                ]);
        }
        else{
            $existants = $this->getEntityManager()
                ->getRepository('AppBundle:ImageDropbox')
                ->findBy([
                    'status' => 0,
                ]);
        }
        foreach ($existants as $existant){
            $em->remove($existant);
        }

        $em->flush();

        $basePath = '/images_dropbox/clients';
        $savePath = '/images_dropbox/save';

        try {
            $lf = new ListFolder(false, $this->token);

            $listClient = json_decode($lf->raw($basePath)->getBody()->getContents());
            $clientEntries = $listClient->entries;

            foreach ($clientEntries as $clientEntry){
                $clientEntry = get_object_vars($clientEntry);
                if($clientEntry['.tag'] === 'folder'){
                    $dossierPath = $clientEntry['path_lower'];

                    $listDossier = json_decode($lf->raw($dossierPath)->getBody()->getContents());
                    $dossierEntries = $listDossier->entries;

                    foreach ($dossierEntries as $dossierEntry){
                        $dossierEntry = get_object_vars($dossierEntry);
                        if($dossierEntry['.tag'] === 'folder'){
                            $filePath = $dossierEntry['path_lower'];

                            $listFiles = json_decode($lf->raw($filePath)->getBody()->getContents());
                            $fileEntries = $listFiles->entries;

                            foreach ($fileEntries as $fileEntry){
                                $fileEntry = get_object_vars($fileEntry);
                                if($fileEntry['.tag'] === 'file'){
                                    $sourceDirectory =  $fileEntry['path_lower'];
                                    $relativePath = explode('/', $sourceDirectory);

                                    if(count($relativePath) === 6){
                                        $clientName = $relativePath[3];

                                        $client = $this->getEntityManager()
                                            ->getRepository('AppBundle:Client')
                                            ->getClientByName($clientName);

                                        $fileName = $relativePath[5];

                                        if($client){
                                            $dossierName = $relativePath[4];

                                            $dossier = $this->getEntityManager()
                                                ->getRepository('AppBundle:Dossier')
                                                ->getDossierByName($client, $dossierName);

                                            /* SAUVER L'IMAGE ORIGINALE */
                                            $saveDirectory = $savePath. '/'. $clientName . '/'.
                                                 $dossierName . '/'.$now->format('Ymd');

                                            if (!$this->foundDirectory($saveDirectory)) {
                                                $createFolder = new CreateFolder(false, $this->token);
                                                $createFolder->raw($saveDirectory);
                                            }

                                            try{
                                                $copy = new Copy(false, $this->token);
                                                $copy->raw($sourceDirectory, $saveDirectory . '/' . $fileName);
                                            }
                                            catch (RequestException $r){

                                            }


                                            $test = $this->findBy([
                                                    'original' => $fileName,
                                                    'client' => $client,
                                                ]);

                                            if(count($test) === 0) {
                                                $imageDropBox = new ImageDropbox();

                                                $imageDropBox->setClient($client)
                                                    ->setDossier($dossier)
                                                    ->setExercice($this->getEntityManager()
                                                        ->getRepository('AppBundle:ImageFtp')
                                                        ->getExerciceBidon())
                                                    ->setOriginal($fileName)
                                                    ->setPathDropbox($sourceDirectory)
                                                    ->setDateTraitement($now)
                                                    ->setDateScan($now)
                                                    ->setDossierTmp($dossierName)
                                                    ->setStatus(0)
                                                ;

                                                $em->persist($imageDropBox);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $em->flush();

            $rows = [];
            $files = $this->getEntityManager()
                ->getRepository('AppBundle:ImageDropbox')
                ->findBy([
                    'status' => 0,
                ]);

            $inconnus =[];

            /** @var ImageDropbox $file */
            foreach ($files as $file) {
                $remarque = '';
                if (!$file->getDossier()) {
                    $remarque .= "Dossier inconnu. ";
                    if ($file->getClient()) {
                        if (!in_array($file->getClient()->getNom() . $file->getDossierTmp(), $inconnus)) {
                            $inconnus[] = $file->getClient()->getNom() . $file->getDossierTmp();
                            $dossierInconnus[] = [
                                'client' => $file->getClient()->getNom(),
                                'dossier' => $file->getDossierTmp(),
                            ];
                        }
                    } else {
                        $remarque .= "Client inconnu. ";
                        $dossierInconnus[] = [
                            'client' => 'Client inconnu',
                            'dossier' => $file->getDossierTmp(),
                        ];
                    }
                }
                if (!$file->getExercice()) {
                    $remarque .= "Pas d'exercice. ";
                }
                if (!$file->getDatescan()) {
                    $remarque .= "Pas de date scan. ";
                }

                $original = explode('.', $file->getOriginal());
                $fileType = strtoupper($original[count($original) -1]);

                $rows[] = [
                    'id' => $file->getId(),
                    'cell' => [
                        $fileType == 'PDF' ? $fileType : '<span class="label label-info" style="display:inline-block;width:100%;">' . $fileType . '</span>',
                        $file->getClient() ? $file->getClient()->getId() : NULL,
                        $file->getClient() ? $file->getClient()->getNom() : NULL,
                        $file->getOriginal(),
                        $file->getDossier() ? $file->getDossier()->getNom() : NULL,
                        $file->getExercice() ? $file->getExercice() : NULL,
                        $file->getDatescan() ? $file->getDatescan()
                            ->format('Y-m-d') : NULL,
                        $file->getDossier() ? $file->getDossier()
                            ->getCloture() : NULL,
                        $remarque,
                        '<i class="fa fa-save icon-action js-save-button js-save-img" title="Enregistrer"></i>',
                    ],
                ];
            }

            $liste = [
                'rows' => $rows,
            ];

            return $liste;
        } catch (NoTokenException $e) {
        }
        return true;
    }


    public function numeroter()
    {
        $maxInLot = 200;
        $now = new \DateTime();
        $em = $this->getEntityManager();

        $utilisateur = $this->getEntityManager()
            ->getRepository('AppBundle:Utilisateur')
            ->findOneBy([
                'email' => 'dematbox@scriptura.biz',
            ]);

        $sourceImage = $this->getEntityManager()
            ->getRepository('AppBundle:SourceImage')
            ->findOneBy([
                'source' => 'DROPBOX',
            ]);

        if (!$sourceImage) {
            $sourceImage = new SourceImage();
            $sourceImage->setSource('DROPBOX');
            $em->persist($sourceImage);
            $em->flush();
        }

        /** @var ImageDropbox[] $dropboxImages */
        $dropboxImages = $this->getEntityManager()
            ->getRepository('AppBundle:ImageDropbox')
            ->createQueryBuilder('imageDropbox')
            ->select('imageDropbox')
            ->where('imageDropbox.status = :status')
            ->andWhere('imageDropbox.client IS NOT NULL')
            ->andWhere('imageDropbox.dossier IS NOT NULL')
            ->andWhere('imageDropbox.exercice IS NOT NULL')
            ->andWhere('imageDropbox.dateScan IS NOT NULL')
            ->innerJoin('imageDropbox.client', 'client')
            ->innerJoin('imageDropbox.dossier', 'dossier')
            ->setParameters([
                'status' => 0,
            ])
            ->orderBy('client.id')
            ->addOrderBy('dossier.id')
            ->addOrderBy('imageDropbox.exercice')
            ->addOrderBy('imageDropbox.dateScan')
            ->getQuery()
            ->getResult();


        if (count($dropboxImages) > 0) {
            $logActivite = new LogActivite();
            $logActivite->setDate($now)
                ->setType(1)
                ->setUtilisateur($utilisateur)
                ->setMessage('IMAGE_DROPBOX_NUMEROTATION');

            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $logActivite->setIp($_SERVER['REMOTE_ADDR']);
            }
            if (!empty($_SERVER['SERVER_NAME'])) {
                $logActivite->setDomaine($_SERVER['SERVER_NAME']);
            }

            $em->persist($logActivite);
            $em->flush();

            //directory
//            $directory = "/var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES";
            $directory = $_SERVER['DOCUMENT_ROOT'] . 'Projets/newpicdata/web/IMAGES';

            if (!file_exists($directory)) {
                if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
                }
            }

            $client = $dropboxImages[0]->getClient();
            $dossier = $dropboxImages[0]->getDossier();
            $exercice = $dropboxImages[0]->getExercice();
            $datescan = $dropboxImages[0]->getDatescan();
            /** @var LotGroup $groups */
            $groups = [];
            $i = 0;
            $nbimage = 0;

            /** @var ImageDropbox $dropboxImage */
            foreach ($dropboxImages as $dropboxImage) {

                $newLot = FALSE;

                if ($i == 0 || $client->getId() != $dropboxImage->getClient()
                        ->getId()) {
                    $lotGroup = new LotGroup();
                    $em->persist($lotGroup);
                    $em->flush();
                    $groups[] = $lotGroup;
                }
                if ($i == 0 ||
                    $client->getId() != $dropboxImage->getClient()->getId() ||
                    $dossier->getId() != $dropboxImage->getDossier()->getId() ||
                    $exercice != $dropboxImage->getExercice() ||
                    $datescan->format('Y-m-d') != $dropboxImage->getDatescan()->format('Y-m-d') ||
                    $nbimage >= $maxInLot
                ) {
                    $newLot = TRUE;
                }

                //création repertoire client
                if ($i == 0 || $datescan != $dropboxImage->getDateScan()) {
                    $directory .= '/' . $dropboxImage->getDateScan()
                            ->format('Ymd');
                    try {
                        if (!is_dir($directory)) {
                            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
                            }
                            @exec("chown -R scripturaadmin:psacln $directory");
                            @exec("chmod 777 -R $directory");
                        }
                    } catch (IOExceptionInterface $e) {
                    }
                }

                //Changement client
                if ($client->getId() != $dropboxImage->getClient()->getId()) {
                    $client = $dropboxImage->getClient();
                }
                //Changement dossier
                if ($dossier->getId() != $dropboxImage->getDossier()->getId()) {
                    $dossier = $dropboxImage->getDossier();
                }
                //Changement exercice
                if ($exercice != $dropboxImage->getExercice()) {
                    $exercice = $dropboxImage->getExercice();
                }
                //Changement datescan
                if ($datescan->format('Y-m-d') != $dropboxImage->getDateScan()->format('Y-m-d')) {
                    $datescan = $dropboxImage->getDateScan();
                }


                $path_info = pathinfo($dropboxImage->getPathDropbox());
                $basename = $path_info['filename'];
                $extension = $path_info['extension'];
                $newName = Boost::getUuid(50);

                $destFilePath = $directory . '/' . $newName . '.' . $extension;

                try {
                    $download = new Download(false, $this->token);

                    $promise = $download->raw($dropboxImage->getPathDropbox());
                    $content = $promise->getBody()->getContents();
                    file_put_contents($destFilePath, $content);

                    $nbpage = 1;

                    if (strtoupper($extension) == 'PDF') {
//                            $nbPage = intval(exec("pdfinfo /var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES/".$lot_select->getDateScan()->format('Ymd')."/".$newName.".".$extension." | awk '/Pages/ {print $2}'"));
                        $nbpage = intval(exec("pdfinfo " . $directory . " | awk '/Pages/ {print $2}'"));
                        if ($nbpage == 0)
                            $nbpage = 1;
                    }

                    if ($newLot) {
                        $lot = $this->getEntityManager()
                            ->getRepository('AppBundle:Lot')
                            ->getNewLot($dropboxImage->getDossier(), $utilisateur, '', NULL, $dropboxImage->getDateScan());
                        $lot->setLotGroup($lotGroup);
                        $em->flush();
                        $nbimage = 0;
                    }

                    $nbimage++;

                    $image = new Image();
                    $image
                        ->setLot($lot)
                        ->setExercice($exercice)
                        ->setExtImage($extension)
                        ->setNbpage($nbpage)
                        ->setNomTemp($newName)
                        ->setOriginale($basename)
                        ->setSourceImage($sourceImage);
                    $em->persist($image);

                    $em->flush();

                    $imageATraiter = new ImageATraiter();
                    $imageATraiter->setImage($image);
                    $em->persist($imageATraiter);
                    $em->flush();

                    $dropboxImage->setNomTmp($newName)
                        ->setStatus(TRUE)
                        ->setImage($image);
                    $em->flush();


                    $delete = new Delete(false, $this->token);
                    $delete->raw($dropboxImage->getPathDropbox());

                    $i++;

                } catch (NoTokenException $e) {

                }

                @exec("chmod 777 $destFilePath");

            }
            /** @var LotGroup $group */
            foreach ($groups as $group) {
                $group->setStatus(1);
            }
            $em->flush();
        }
    }

    private function foundDirectory($path){
        try {
            $lf = new ListFolder(false, $this->token);
            $list = json_decode($lf->raw($path)->getBody()->getContents());

            if(isset($list->error)){
                return false;
            }
        } catch (RequestException $e) {
            return false;
        } catch (NoTokenException $e) {
        }
        return true;
    }

}