<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 22/05/2017
 * Time: 14:20
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Client;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageATraiter;
use AppBundle\Entity\ImageFtp;
use AppBundle\Entity\ImageFtpVerif;
use AppBundle\Entity\LogActivite;
use AppBundle\Entity\LotGroup;
use AppBundle\Entity\SourceImage;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ImageFtpRepository extends EntityRepository
{

    /**
     * Vérification des exhaustivités des images reçues
     *  Par rapport à la liste CSV
     *
     * @return array
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function verifExhaustif()
    {
        $em = $this->getEntityManager();
        $this->ParseFtp(1, $dossier_inconnus);
        $config = $this->getEntityManager()
            ->getRepository('AppBundle:Config')
            ->findOneBy([
                'code' => 'PATH_ENVOI_IMG_FTP',
            ]);
        if (!$config) {
            return [];
        }

        $base_path = $config->getValue();
//        $save_path = $this->getSavePath();
        $now = new \DateTime();
        $date_limit = $now->sub(new \DateInterval('P2D'));
        $date_limit->setTime(0, 0);

//        $base_path = "D:/ftp/";

        $search = new Finder();
        $search->files()->in($base_path);
        $search->depth(2);
        $search->files()->name('/^Scans_Envoyees_Le_(\d{4}-\d{2}-\d{2})\.csv$/i');
        $search->sortByName();
        $not_found = [];
        /** @var \SplFileInfo $item */
        foreach ($search as $item) {
            $have_not_found = false;
            $relative_path = explode(DIRECTORY_SEPARATOR, $item->getRelativePath());
            if (count($relative_path) == 2) {
                $client_id = intval($relative_path[0]);
                $client = $this->getEntityManager()
                    ->getRepository('AppBundle:Client')
                    ->find($client_id);
                if ($client) {
                    preg_match('/(^Scans_Envoyees_Le_)(\d{4}-\d{2}-\d{2})(\.csv$)/i', $item->getFilename(), $matches);
                    if (isset($matches[1])) {
                        $datescan = new \DateTime($matches[2]);

                        /** Insertion dans ImageFtpVerif */
                        $image_ftp_verif = $this->getEntityManager()
                            ->getRepository('AppBundle:ImageFtpVerif')
                            ->findOneBy(array(
                                'client' => $client,
                                'filename' => $item->getFilename()
                            ));
                        if ($image_ftp_verif) {
                            $image_ftp_verif->setDateVerif($now);
                        } else {
                            $image_ftp_verif = new ImageFtpVerif();
                            $image_ftp_verif->setClient($client)
                                ->setFilename($item->getFilename())
                                ->setDateReception($datescan)
                                ->setDateVerif($now);
                            $em->persist($image_ftp_verif);
                        }

                        $em->flush();

                        if ($datescan <= $date_limit) {
                            $i = 0;
                            if (($handle = fopen($item->getRealPath(), "r")) !== FALSE) {
                                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                                    $num = count($data);
                                    if ($num == 4 && $i > 0) {
                                        $tmp = new \SplFileInfo($data[3]);
                                        $extension = $tmp->getExtension();
                                        $original = basename($tmp->getFilename(), "." . $extension);
                                        $the_client = $client;

                                        if (strtolower($client->getNom()) == "next2you") {
                                            if (preg_match('/^(.+)\.(.+)\.(.+)\.(.+)/i', $tmp->getFilename(), $filename)) {
                                                $nom_client = strtoupper($filename[1]);
                                                $test_client = $this->getEntityManager()
                                                    ->getRepository('AppBundle:Client')
                                                    ->findOneBy(array(
                                                        'nom' => $nom_client
                                                    ));
                                                if ($test_client) {
                                                    $the_client = $test_client;
                                                }
                                            }
                                        }
                                        $test = $this->getEntityManager()
                                            ->getRepository("AppBundle:ImageFtp")
                                            ->createQueryBuilder('img')
                                            ->select('img')
                                            ->innerJoin('img.client', 'client')
                                            ->where('img.original LIKE :original')
                                            ->andWhere('client = :client')
                                            ->setParameters(array(
                                                'original' => $original . '%',
                                                'client' => $the_client,
                                            ))
                                            ->getQuery()
                                            ->getResult();
                                        if (count($test) == 0) {
                                            $have_not_found = true;
                                            $verif = new Finder();
                                            $verif->files()->in($base_path . $client->getId());
                                            $verif->depth(1);
                                            $verif->files()->name('/^' . $original . '/i');
                                            if ($verif->count() == 0) {
                                                $not_found[] = [
                                                    'client_id' => $the_client->getId(),
                                                    'client' => $the_client->getNom(),
                                                    'filename' => $tmp->getFilename(),
                                                    'datescan' => $datescan,
                                                    'dateverif' => $now
                                                ];
//                                            echo $tmp->getFilename() . "\r\n";
                                            }
                                        }
                                    }
                                    $i++;
                                }
                                fclose($handle);

                                if ($have_not_found) {
                                    $image_ftp_verif->setStatus(0);
                                } else {
                                    $image_ftp_verif->setStatus(1);
                                }

                                $em->flush();
                            }
                        }
                    }
                }
            }
        }

        return $not_found;
    }

    /**
     *  Parser les images sur FTP et les insérer dans table image_ftp
     * @param $init
     * @param $dossier_inconnus
     * @return array
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function ParseFtp($init, &$dossier_inconnus)
    {
        $now = new \DateTime();
        $em = $this->getEntityManager();
        $dossier_inconnus = [];
        $inconnus = [];
        $config = $this->getEntityManager()
            ->getRepository('AppBundle:Config')
            ->findOneBy([
                'code' => 'PATH_ENVOI_IMG_FTP',
            ]);
        if (!$config) {
            return [];
        }
        if ($init == 0) {
            $existants = $this->getEntityManager()
                ->getRepository('AppBundle:ImageFtp')
                ->findBy([
                    'status' => FALSE,
                    'dossier' => NULL,
                ]);
        } else {
            $existants = $this->getEntityManager()
                ->getRepository('AppBundle:ImageFtp')
                ->findBy([
                    'status' => FALSE,
                ]);
        }
        /** @var ImageFtp $existant */
        foreach ($existants as $existant) {
            $em->remove($existant);
        }
        $em->flush();

        $base_path = $config->getValue();
        $save_path = $this->getSavePath();

//        $base_path = "D:/ftp/";

        $search = new Finder();
        $search->files()->in($base_path);
        $search->depth(2);
        $search->sortByName();

        /** @var SplFileInfo $item */
        foreach ($search as $item) {
            $relative_path = explode(DIRECTORY_SEPARATOR, $item->getRelativePath());
            if (count($relative_path) == 2) {
                $client_id = intval($relative_path[0]);
                $client = $this->getEntityManager()
                    ->getRepository('AppBundle:Client')
                    ->find($client_id);
                if ($client) {
                    $this->getDossierExercice($client, basename($item->getFilename(), '.' . $item->getExtension()), $real_client, $dossier, $exercice, $dossier_tmp);
                    //TESTER SI IMAGE NON PDF
                    $image_exts = [
                        'jpg',
                        'jpeg',
                        'png',
                        'tif',
                        'tiff',
                        'bmp',
                        'gif',
                    ];

                    if ($item->getSize() > 0) {
                        $original = $item->getFilename();
                        $path_ftp = str_replace("\\", "/", $item->getRealPath());
                        $extension = strtolower($item->getExtension());

                        /* SAUVER L'IMAGE ORIGINALE */
                        $save_directory = $save_path . $client_id . "/" . $relative_path[1];
                        if (!is_dir($save_directory)) {
                            @exec("mkdir -p $save_directory");
                            @exec("chown -R scripturaadmin:psacln $save_directory");
                            @exec("chmod 0777 $save_directory");
                        }
                        @exec("cp " . $item->getRealPath() . " " . $save_directory . "/");

                        /* CONVERSION IMAGS JPG, PNG, TIFF, ... EN PDF */
                        if ($extension != 'pdf') {
                            $match = false;
                            foreach ($image_exts as $ext) {
                                if (preg_match('/^' . $ext . '/i', $extension) > 0) {
                                    $match = true;
                                    break;
                                }
                            }
                            if ($match) {
                                $new_filename = $item->getPathInfo()
                                        ->getRealPath() . DIRECTORY_SEPARATOR . $item->getBasename('.' . $item->getExtension()) . '.pdf';

                                exec("convert \"" . $item->getRealPath() . '" "' . $new_filename . '"');
                                if (is_file($new_filename)) {
                                    @unlink($item->getRealPath());
                                    $original = basename($new_filename);
                                    $path_ftp = $new_filename;
                                }
                            }
                        }

                        $test = $this->getEntityManager()
                            ->getRepository('AppBundle:ImageFtp')
                            ->findBy([
                                'original' => $original,
                                'client' => $real_client,
                            ]);
                        if (count($test) == 0) {
                            if ($this->isDatescan($relative_path[1])) {
                                $imageFtp = new ImageFtp();
                                $datescan = \DateTime::createFromFormat("d-m-Y", $relative_path[1]);
                                $imageFtp
                                    ->setClient($real_client)
                                    ->setDossier($dossier)
                                    ->setExercice($exercice)
                                    ->setDatescan($datescan)
                                    ->setDateTraitement($now)
                                    ->setOriginal($original)
                                    ->setPathFtp($path_ftp)
                                    ->setDossierTmp($dossier_tmp);
                                $em->persist($imageFtp);
                            }
                        }
                    } else {
                        /** Supprimer les images 0 octets */
                        @unlink($item->getRealPath());
                    }
                }
            }
        }
        $em->flush();

        $rows = [];
        $files = $this->getEntityManager()
            ->getRepository('AppBundle:ImageFtp')
            ->findBy([
                'status' => FALSE,
            ]);
        /** @var ImageFtp $file */
        foreach ($files as $file) {
            $remarque = '';
            if (!$file->getDossier()) {
                $remarque .= "Dossier inconnu. ";
                if ($file->getClient()) {
                    if (!in_array($file->getClient()->getNom() . $file->getDossierTmp(), $inconnus)) {
                        $inconnus[] = $file->getClient()->getNom() . $file->getDossierTmp();
                        $dossier_inconnus[] = [
                            'client' => $file->getClient()->getNom(),
                            'dossier' => $file->getDossierTmp(),
                        ];
                    }
                } else {
                    $remarque .= "Client inconnu. ";
                    $dossier_inconnus[] = [
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

            $fileInfo = new \SplFileInfo($file->getOriginal());
            $fileType = strtoupper($fileInfo->getExtension());
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
    }

    /**
     * Numeroter les images dans table image_ftp
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
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
                'source' => 'FTP',
            ]);
        if (!$sourceImage) {
            $sourceImage = new SourceImage();
            $sourceImage->setSource('FTP');
            $em->persist($sourceImage);
            $em->flush();
        }

        /** @var ImageFtp[] $ftpImages */
        $ftpImages = $this->getEntityManager()
            ->getRepository('AppBundle:ImageFtp')
            ->createQueryBuilder('imageFtp')
            ->select('imageFtp')
            ->where('imageFtp.status = :status')
            ->andWhere('imageFtp.client IS NOT NULL')
            ->andWhere('imageFtp.dossier IS NOT NULL')
            ->andWhere('imageFtp.exercice IS NOT NULL')
            ->andWhere('imageFtp.datescan IS NOT NULL')
            ->innerJoin('imageFtp.client', 'client')
            ->innerJoin('imageFtp.dossier', 'dossier')
            ->setParameters([
                'status' => FALSE,
            ])
            ->orderBy('client.id')
            ->addOrderBy('dossier.id')
            ->addOrderBy('imageFtp.exercice')
            ->addOrderBy('imageFtp.datescan')
            ->getQuery()
            ->getResult();


        if (count($ftpImages) > 0) {
            $logActivite = new LogActivite();
            $logActivite->setDate($now)
                ->setType(1)
                ->setUtilisateur($utilisateur)
                ->setMessage('IMAGE_FTP_NUMEROTATION');

            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $logActivite->setIp($_SERVER['REMOTE_ADDR']);
            }
            if (!empty($_SERVER['SERVER_NAME'])) {
                $logActivite->setDomaine($_SERVER['SERVER_NAME']);
            }

            $em->persist($logActivite);
            $em->flush();

            //directory
            $directory = "/var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES";
            $fs = new Filesystem();
            try {
                if (!is_dir($directory)) {
                    $fs->mkdir($directory, 0777);
                    @exec("chown -R scripturaadmin:psacln $directory");
                }
            } catch (IOExceptionInterface $e) {
            }

            $client = $ftpImages[0]->getClient();
            $dossier = $ftpImages[0]->getDossier();
            $exercice = $ftpImages[0]->getExercice();
            $datescan = $ftpImages[0]->getDatescan();
            /** @var LotGroup $groups */
            $groups = [];
            $i = 0;
            $nbimage = 0;

            /** @var ImageFtp $ftpImage */
            foreach ($ftpImages as $ftpImage) {
                $fileInfo = new \SplFileInfo($ftpImage->getOriginal());
                if (strtolower($fileInfo->getExtension() == 'pdf')) {
                    $newLot = FALSE;

                    if ($i == 0 || $client->getId() != $ftpImage->getClient()
                            ->getId()) {
                        $lot_group = new LotGroup();
                        $em->persist($lot_group);
                        $em->flush();
                        $groups[] = $lot_group;
                    }
                    if ($i == 0 ||
                        $client->getId() != $ftpImage->getClient()->getId() ||
                        $dossier->getId() != $ftpImage->getDossier()->getId() ||
                        $exercice != $ftpImage->getExercice() ||
                        $datescan->format('Y-m-d') != $ftpImage->getDatescan()->format('Y-m-d') ||
                        $nbimage >= $maxInLot
                    ) {
                        $newLot = TRUE;
                    }

                    //création repertoire client
                    if ($i == 0 || $datescan != $ftpImage->getDatescan()) {
                        $directory = '/var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES/' . $ftpImage->getDatescan()
                                ->format('Ymd');
                        try {
                            if (!is_dir($directory)) {
                                $fs->mkdir($directory, 0777);
                                @exec("chown -R scripturaadmin:psacln $directory");
                                @exec("chmod 777 -R $directory");
                            }
                        } catch (IOExceptionInterface $e) {
                        }
                    }

                    //Changement client
                    if ($client->getId() != $ftpImage->getClient()->getId()) {
                        $client = $ftpImage->getClient();
                    }
                    //Changement dossier
                    if ($dossier->getId() != $ftpImage->getDossier()->getId()) {
                        $dossier = $ftpImage->getDossier();
                    }
                    //Changement exercice
                    if ($exercice != $ftpImage->getExercice()) {
                        $exercice = $ftpImage->getExercice();
                    }
                    //Changement datescan
                    if ($datescan->format('Y-m-d') != $ftpImage->getDatescan()->format('Y-m-d')) {
                        $datescan = $ftpImage->getDatescan();
                    }


                    $path_info = pathinfo($ftpImage->getPathFtp());
                    $basename = $path_info['filename'];
                    $extension = $path_info['extension'];
                    $newName = Boost::getUuid(50);

                    $destFilePath = $directory . '/' . $newName . '.' . $extension;
                    $fs->copy($ftpImage->getPathFtp(), $destFilePath, TRUE);
                    @exec("chmod 777 $destFilePath");
                    $nbpage = 1;

//                    if (strtoupper($extension) == 'PDF') {
//                        if (FALSE !== ($the_pdf = file_get_contents($ftpImage->getPathFtp()))) {
//                            $nbpage = preg_match_all("/\/Page\W/", $the_pdf, $matches);
//                        }
//                    }

//                    if (!$newLot) {
//                        if (isset($lot) && $lot) {
//                            $test = $this->getEntityManager()
//                                ->getRepository('AppBundle:Image')
//                                ->createQueryBuilder('i')
//                                ->select('i')
//                                ->innerJoin('i.lot', 'l')
//                                ->where('l = :lot')
//                                ->setParameters(array(
//                                    'lot' => $lot
//                                ))
//                                ->getQuery()
//                                ->getResult();
//                            if (count($test) >= $maxInLot) {
//                                $newLot = TRUE;
//                            }
//                        } else {
//                            $newLot = TRUE;
//                        }
//                    }

                    if ($newLot)
                    {
                        $lot = $this->getEntityManager()
                            ->getRepository('AppBundle:Lot')
                            ->getNewLot($ftpImage->getDossier(), $utilisateur, '', NULL, $ftpImage->getDatescan());
                        $lot->setLotGroup($lot_group);
                        $em->flush();
                        $nbimage = 0;
                    }

                    if (strtoupper($extension) == 'PDF')
                    {
                        $nbpage = intval(exec("pdfinfo /var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES/".$lot->getDateScan()->format('Ymd')."/".$newName.".".$extension." | awk '/Pages/ {print $2}'"));
                        if ($nbpage == 0)
                            $nbpage = 1;
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

                    $image_a_traiter = new ImageATraiter();
                    $image_a_traiter->setImage($image);
                    $em->persist($image_a_traiter);
                    $em->flush();

                    $ftpImage->setNomTmp($newName)
                        ->setStatus(TRUE)
                        ->setImage($image);

                    $em->flush();

                    $fs->remove($ftpImage->getPathFtp());

                    $i++;
                }
            }
            /** @var LotGroup $group */
            foreach ($groups as $group) {
                $group->setStatus(1);
            }
            $em->flush();
        }
    }

    /**
     * Get Repertoire pour sauvegarde image sur FTP
     *
     * @return string
     */
    private function getSavePath()
    {
        $config_save = $this->getEntityManager()
            ->getRepository('AppBundle:Config')
            ->findOneBy([
                'code' => 'PATH_ENVOI_IMG_FTP_SAVE',
            ]);
        if (!$config_save) {
            $save_path = "/var/www/vhosts/ns315229.ip-37-59-25.eu/ftp/save/";
        } else {
            $save_path = $config_save->getValue();
        }

        return $save_path;
    }

    /**
     * Get dossier et exercice à partir nom de fichier
     *
     * @param Client $client
     * @param $filename
     * @param $real_client
     * @param $dossier
     * @param $exercice
     * @param $dossier_tmp
     * @return bool
     */
    function getDossierExercice(Client $client, $filename, &$real_client, &$dossier, &$exercice, &$dossier_tmp)
    {
        $image_separator = $client->getImageFtpSeparator();
        $dossier_tmp = '';
        if ($image_separator == '') {
            $image_separator = '_';
        }
        if ($image_separator == '.') {

            $array = explode('.', $filename);
            $multiple = $this->getEntityManager()
                ->getRepository('AppBundle:ImageFtpConfig')
                ->isClientMultiple($client);
            if ($multiple) {
                // next2you.mayer-prezioso-company.20180103143929569
                $exercice = $this->getExerciceBidon();
                if (isset($array[0])) {
                    $nom_client = strtoupper($array[0]);
//                    if($nom_client === 'BHN-EXPERTISE-MAUREPAS'){
//                        $nom_client = 'MARIONNEAU';
//                    }
                    $real_client = $this->getEntityManager()
                        ->getRepository('AppBundle:Client')
                        ->findOneBy([
                            'nom' => $nom_client,
                        ]);
                    if ($real_client && isset($array[1])) {
                        $dossier_tmp = $array[1];
                        $nom_tmp = str_replace('_', '%', str_replace('-', '%', $array[1]));
                        $dossier = $this->findDossierLike($real_client, $nom_tmp);
                        if ($dossier) {
                            return TRUE;
                        } else {
//                            return FALSE;

                            if(isset($array[1])){
                                $dossier_tmp = $array[1];
                                $nom_tmp = str_replace('_', '%', str_replace('-', '%', $array[1]));

                                $real_client = $this->getEntityManager()
                                    ->getRepository('AppBundle:Client')
                                    ->find(764);

                                $dossier = $this->findDossierLike($real_client, $nom_tmp);

                                if($dossier){
                                    return TRUE;
                                }
                                else{
                                    return FALSE;
                                }

                            }
                        }
                    } else {

                        if(isset($array[1])){
                            $dossier_tmp = $array[1];
                            $nom_tmp = str_replace('_', '%', str_replace('-', '%', $array[1]));

                            $real_client = $this->getEntityManager()
                                ->getRepository('AppBundle:Client')
                                ->find(764);

                            $dossier = $this->findDossierLike($real_client, $nom_tmp);

                            if($dossier){
                                return TRUE;
                            }
                            else{
                                return FALSE;
                            }

                        }

                        $real_client = NULL;
                        $dossier = NULL;
                        return FALSE;
                    }
                }
            } else {
                // la-boite-a-conseils.2016.0000074367
                $real_client = $client;
                if (count($array) >= 3) {
                    $count = count($array);

                    $ex_tmp = intval($array[$count - 2]);
                    if ($ex_tmp != 0 && $ex_tmp >= 2000 && $ex_tmp <= 2099) {
                        $exercice = $ex_tmp;
                        $ex_position = strpos($filename, '.' . $exercice . '.');
                        $tmp = substr($filename, 0, $ex_position);
                        $dossier_tmp = $tmp;
                        $tmp_nom = str_replace('_', '%', str_replace('-', '%', $tmp));

                        $dossier = $this->findDossierLike($real_client, $tmp_nom);
                        if ($dossier) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        $exercice = NULL;
                        $dossier = NULL;
                        return FALSE;
                    }
                } else {
                    $dossier = NULL;
                    $exercice = NULL;
                    return FALSE;
                }
            }
        } elseif ($image_separator == '_') {
            // HAU1_HAUSSY_LAETITIA_2016_scan_9055742
            $real_client = $client;
            $array = explode('_', $filename);
            if (count($array) >= 4) {
                $count = count($array);

                $ex_tmp = intval($array[$count - 3]);
                if ($ex_tmp != 0 && $ex_tmp >= 2000 && $ex_tmp <= 2099) {
                    $exercice = $ex_tmp;
                    $ex_position = strpos($filename, '_' . $exercice . '_');
                    $tmp = substr($filename, 0, $ex_position);
                    $dossier_tmp = $tmp;
                    $tmp_nom = str_replace('_', '%', str_replace('-', '%', $tmp));

                    $dossier = $this->findDossierLike($real_client, $tmp_nom);
                    if ($dossier) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                } else {
                    $exercice = NULL;
                    $dossier = NULL;
                    return FALSE;
                }
            } else {
                $dossier = NULL;
                $exercice = NULL;
                return FALSE;
            }
        }
        $dossier = NULL;
        $exercice = NULL;
        $real_client = NULL;
        return FALSE;
    }

    /**
     * Get exercice bidon
     *
     * @return int|string
     */
    public function getExerciceBidon()
    {
        $now = new \DateTime();
        $year = $now->format('Y');
        $limit = new \DateTime($year . '-01-31');
        if ($now <= $limit) {
            $exercice = $year - 1;
        } else {
            $exercice = $year;
        }
        return $exercice;
    }

    /**
     * Rechecher nom de dossier à partir pattern
     *
     * @param Client $client
     * @param $nom
     * @return null
     */
    private function findDossierLike(Client $client, $nom)
    {
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->createQueryBuilder('dossier')
            ->select('dossier')
            ->where('dossier.nom LIKE :dossier')
            ->innerJoin('dossier.site', 'site')
            ->addSelect('site')
            ->innerJoin('site.client', 'client')
            ->addSelect('client')
            ->andWhere('client = :client')
            ->setParameters([
                'dossier' => $nom,
                'client' => $client,
            ])
            ->orderBy('dossier.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        if (count($qb) > 0) {
            $dossier = $qb[0];
        } else {
            $dossier = NULL;
        }
        return $dossier;
    }

    /**
     * Tester si un nom de repertoire est une  datescan valide
     * @param $str
     * @return bool
     */
    function isDatescan($str)
    {
        if (strlen($str) != 10) {
            return FALSE;
        }

        $day = intval(substr($str, 0, 2));
        $month = intval(substr($str, 3, 2));
        $year = intval(substr($str, 6, 4));

        if ($day > 0 && $month > 0 and $year > 2000 and $year < 2099) {
            return checkdate($month, $day, $year);
        } else {
            return FALSE;
        }
    }
}