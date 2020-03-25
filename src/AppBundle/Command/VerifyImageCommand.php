<?php

/**
 * CheckImageCommand
 *
 * Copie des images de l'ancien FTP 
 * non existant dans le nouveau FTP
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use AppBundle\Functions\CustomPdoConnection;


class VerifyImageCommand extends ContainerAwareCommand
{

    
    public $pdo = null;

    protected function configure()
    {
        $this
            ->setName('verify:img')
            ->addArgument('client_folder', InputArgument::REQUIRED, 'Dossier A Verifier')
            ->addArgument('exercice', InputArgument::OPTIONAL, 'Exercice')
            ->setDescription('Vérification des images')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client_folder = $input->getArgument('client_folder');
        $exercice      = $input->getArgument('exercice');
        $ftp_server    = "ns384250.ovh.net";
        $ftp_user_name = "picdataimage";
        $ftp_user_pass = "^aW63wo9";

        $con       = new CustomPdoConnection();
        $this->pdo = $con->connect();

        $conn = $this->connectFtp($ftp_server,$ftp_user_name,$ftp_user_pass);

        if ($conn) {
            $this->process($conn, $client_folder, $output, $exercice);
        } else {
            // erreur de connexion
        }
    }

    /**
     * Procédure de vérification
     *
     * @param string $conn
     * @param string $client_folder
     */
    public function process($conn,$client_folder, $output, $exercice)
    {
        $log = $this->createLogFile($client_folder,$exercice);


        if ($log != "") {
            $log = json_decode($log,true);
        } else {
            $log = array();
        }


    	$path = "/" . $client_folder;
    	$dossierDirectories = $this->listDirectory($conn,$path);

        $nbDossiers = count($dossierDirectories);

        $progressBar = new ProgressBar($output, $nbDossiers);

        $output->writeln('Verification du Cabinet ' . $client_folder . " (" . $nbDossiers . " Dossiers)" .  date("H:i:s"));

        $progressBar->start();


        if ($dossierDirectories) {
            foreach ($dossierDirectories as $dossier) {

                $sp5 = str_repeat(' ', 3);

                $output->writeln("\n" . $sp5 . "Dossier " . $dossier);

                if ((array_key_exists($dossier, $log)  && $log[$dossier]['status'] == false ) || (!array_key_exists($dossier, $log)) ) {

                    $errorLot = false;

                    if (!array_key_exists($dossier, $log)) {
                        $log[$dossier] = array();
                        $log[$dossier]['status'] = false;
                    }

                    $dossierPath    = $path . "/" . $dossier . "/";
                    $exoDirectories = $this->listDirectory($conn,$dossierPath);

                    if ($exoDirectories) {

                        foreach ($exoDirectories as $exo) {

                            $output->writeln("     Exercice " . $exo);


                            if  (($exo == $exercice) && ( (array_key_exists($exo, $log[$dossier]) && $log[$dossier][$exo]['status'] == false ) || (!array_key_exists($exo, $log[$dossier])))) {

                                if (!array_key_exists($exo, $log[$dossier])) {
                                    $log[$dossier][$exo] = array();
                                    $log[$dossier][$exo]['status'] = false;
                                }

                                $exoPath     = $dossierPath . "/" . $exo . "/";
                                $dsDirectory = $this->listDirectory($conn,$exoPath);

                                if ($dsDirectory) {

                                    $errorDs = true;

                                    foreach ($dsDirectory as $ds) {

                                        $output->writeln("       Date scan " . $ds);

                        
                                        if ((array_key_exists($ds, $log[$dossier][$exo]) && $log[$dossier][$exo][$ds]['status'] == false  ) || (!array_key_exists($ds, $log[$dossier][$exo])) ) {

                                            if (!array_key_exists($ds, $log[$dossier][$exo])) {
                                                $log[$dossier][$exo][$ds] = array();
                                                $log[$dossier][$exo][$ds]['status'] = false;
                                            }

                                            $dsPath       = $exoPath . "/" . $ds . "/";
                                            $lotDirectory = $this->listDirectory($conn,$dsPath);

                                            if ($lotDirectory) {

                                                foreach ($lotDirectory as $lot) {

                                                    $output->writeln("         Lot " . $lot);


                                                    if ((array_key_exists($lot, $log[$dossier][$exo][$ds]) && $log[$dossier][$exo][$ds][$lot]['status'] == false) || (!array_key_exists($lot, $log[$dossier][$exo][$ds]))) {

                                                        
                                                        
                                                        if (!array_key_exists($lot, $log[$dossier][$exo][$ds])) {
                                                            $log[$dossier][$exo][$ds][$lot] = array();
                                                            $log[$dossier][$exo][$ds][$lot]['status'] = false;
                                                        }

                                                        $lotPath  = $dsPath . "/" . $lot . "/";
                                                        $pdfList  = $this->listFile($conn,$lotPath);
                                                        $errorLot = $this->checkPdfList($pdfList,$ds,$conn,$lotPath, $log,$dossier,$exo,$lot,$client_folder);

                                                        if (!$errorLot) {
                                                            $log[$dossier][$exo][$ds][$lot] = true;
                                                        }

                                                        $this->putLogFile($log,$client_folder,$exercice);
                                                    }
                                				}
                                            }

                                            if (!$errorLot) {
                                                $log[$dossier][$exo][$ds]['status'] = true;
                                            }

                                            $this->putLogFile($log,$client_folder,$exercice);
                                        }
                        			}
                                }

                                if (!$errorLot) {
                                    $log[$dossier][$exo]['status'] = true;
                                }

                                $this->putLogFile($log,$client_folder,$exercice);

                            }
                		}
                    }

                    if (!$errorLot) {
                        $log[$dossier]['status'] = true;
                    }

                    $this->putLogFile($log,$client_folder,$exercice);
                }
        	
                $progressBar->advance();

            }

            $progressBar->finish();

            $output->writeln( "\n" . "Vérification Terminé ". date("H:i:s") . "!");

        }

        $log['status'] = true;

        $this->checked($client_folder,$exercice);

        $this->putLogFile($log,$client_folder,$exercice);
        # code...
    }

    public function checked($client_folder,$exercice)
    {
        $select = " select id
                    from check_image
                    where cabinet = :client_folder
                    and exercice = :exercice";

        $prep = $this->pdo->prepare($select);

        $prep->execute(array(
            'client_folder' => $client_folder,
            'exercice' => $exercice
        ));

        $res = $prep->fetchAll()[0];

        $id = $res->id;

        $query = "  update check_image
                        set status = :status
                        where id = :id";

        
        $prep = $this->pdo->prepare($query);

        $prep->execute(array(
            'status' => 1,
            'id' => $id
        ));

    }

    /**
     * Sauvegarde du log de verification en format json
     *
     * @param array $json
     */
    public function putLogFile($json,$client_folder,$exercice, $count = false)
    {




        $json = json_encode($json);


        $select = " select id, nb
                    from check_image
                    where cabinet = :client_folder
                    and exercice = :exercice";

        $prep = $this->pdo->prepare($select);

        $prep->execute(array(
            'client_folder' => $client_folder,
            'exercice' => $exercice
        ));

        $res = $prep->fetchAll()[0];

        $id = $res->id;

        $nb = $res->nb;


        if (!$nb) {
            $query = "  update check_image
                        set log = :json
                        where id = :id";

            $prep = $this->pdo->prepare($query);

            $prep->execute(array(
                'json' => $json,
                'id' => $id
            ));
        } else {
            $query = "  update check_image
                        set log = :json,
                            nb = :nb
                        where id = :id";

            $prep = $this->pdo->prepare($query);

            $prep->execute(array(
                'json' => $json,
                'id' => $id,
                'nb' => $nb + 1
            ));
        }

    }

    /**
     * Vérification des fichiers pdf pour un dossier Lot
     *
     * @param array $pdfList
     * @param string $ds
     * @param string $conn
     * @param string $source
     * @param array $log
     * @param string $dossier
     * @param string $exo
     * @param string $lot
     *
     * @return boolean
     */
    public function checkPdfList($pdfList,$ds,$conn,$source,$log,$dossier,$exo,$lot,$client_folder)
    {

        $error = false;

    	foreach ($pdfList as $pdf) {

            if ((array_key_exists($pdf, $log[$dossier][$exo][$ds][$lot]) && $log[$dossier][$exo][$ds][$lot][$pdf]['status'] == false) || ( !array_key_exists($pdf, $log[$dossier][$exo][$ds][$lot]))) {

                if (!array_key_exists($pdf, $log)) {
                    $log[$dossier][$exo][$ds][$lot][$pdf] = array();
                    $log[$dossier][$exo][$ds][$lot][$pdf]['status'] = false;
                }

                $exist  = false;
                $finder = new Finder();
                $finder->files()->in("/var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES")->name($pdf);

                foreach ($finder as $file) {
                    $exist = true;
                }

                if (!$exist) {
                    $destination = $this->createFolder($ds);
                    echo "           Copie de " . $pdf . "\n";
                    if (ftp_get($conn, $destination . "/" . $pdf, $source . "/" . $pdf, FTP_BINARY)) {
                        $log[$dossier][$exo][$ds][$lot][$pdf]['status'] = true;
                        // echo "Success \n";
                        $this->putLogFile($log,$client_folder,$exo, true);
                    }
                    else {
                        $error = true;
                        echo "           Problème de copie " . $pdf . "\n";
                        $this->putLogFile($log,$client_folder,$exo);
                    }


                } else {
                    // echo "Ce fichier existe dejà \n";
                    $log[$dossier][$exo][$ds][$lot][$pdf]['status'] = true;
                    $this->putLogFile($log,$client_folder,$exo);
                }


            } else {
                echo "           fichier ". $pdf ." deja verifier \n";
                // fichier dejà verifier
            }

        }

         return $error;
    }

    /**
     * Creation de fichier log de vérification
     *
     * @return string
     */
    public function createLogFile($client_folder,$exercice)
    {

        $query = "  select ci.log 
                    from check_image ci
                    where ci.cabinet = :client_folder";

        $prep = $this->pdo->prepare($query);

        $prep->execute(array(
            'client_folder' => $client_folder
        ));

        $result = $prep->fetchAll()[0]->log;

        return $result;

    }

    /**
     * Création de dossier avec date scan
     *
     * @param string $ds
     *
     * @return string
     */
    public function createFolder($ds)
    {
        $dateScanDirectory = "web/IMAGES/" . str_replace("-", "", $ds);
        $fs                = new Filesystem();

        try {
            $fs->mkdir($dateScanDirectory, 0777);
        } catch (IOExceptionInterface $e) {
        }

        return $dateScanDirectory;
        
    }

    /**
     * Filtrage des Fichiers pdf
     *
     * @param string $file
     *
     * @return string 
     */
    function is_pdf($file) {
	    return preg_match('/.*\.pdf/', $file) > 0;
	}

	/**
     * Liste des Fichiers pdf
     *
     * @param string $conn
     * @param string $path
     *
     * @return array
     */
    public function listFile($conn,$path){

        $lines = ftp_rawlist($conn, $path);
        $files = array();

		if (!$lines) {
			return false;
		}

		foreach ($lines as $line)
		{
            $tokens = explode(" ", $line);
            $name   = $tokens[count($tokens) - 1];
            $type   = $tokens[0][0];

		    // Type Fichiers
            if ($type != 'd')
		    {
		        array_push($files, $name);
		    }
		}

		$filtered = array_filter($files, array($this, 'is_pdf'));

		return $filtered;

	}


    /**
     * Liste des Repertoires
     *
     * @param string $conn
     * @param string $path
     *
     * @return array
     */
    public function listDirectory($conn,$path)
    {
        $lines       = ftp_rawlist($conn, $path);
        $directories = array();

    	if (!$lines) {
    		return false;
    	}

		foreach ($lines as $line)
		{
            $tokens = explode(" ", $line);
            $name   = $tokens[count($tokens) - 1];
            $type   = $tokens[0][0];

		    // Type Repertoir
            if ($type == 'd')
		    {
		        array_push($directories, $name);
		    }
		}

		return $directories;
    }


    /**
     * Connection ftp
     *
     * @param string $ftp_server 
     * @param string $ftp_user_name
     * @param string $ftp_user_pass
     *
     * @return string
     */
    function connectFtp($ftp_server,$ftp_user_name,$ftp_user_pass)
    {
        $conn_id      = ftp_connect($ftp_server);
        ftp_pasv($conn_id, true);
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

        if ((!$conn_id) || (!$login_result)) {
            return false;
        } else {
        	return $conn_id;
        }
    }
}