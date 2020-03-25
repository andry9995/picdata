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


class CountImageCheckedCommand extends ContainerAwareCommand
{

    
    public $pdo = null;

    protected function configure()
    {
        $this
            ->setName('count:img')
            ->addArgument('exercice', InputArgument::REQUIRED, 'Exercice')
            ->setDescription('Vérification des images')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exercice      = $input->getArgument('exercice');

        $con       = new CustomPdoConnection();
        $this->pdo = $con->connect();

        $this->process($output, $exercice);

    }


    public function process($output,$exercice)
    {
        $query = "  select cabinet 
                    from check_image ci
                    where exercice = :exercice";

        $prep = $this->pdo->prepare($query);

        $prep->execute(array(
            'exercice' => $exercice
        ));

        $result = $prep->fetchAll();

        foreach ($result as $value) {
            $values = array($value->cabinet,null,$exercice - 1,0,0);
            $q = "insert into check_image (`cabinet`,`log`,`exercice`,`nb`,`status`) values ( '" . implode( "', '" , $values ) . "' )";
            $p = $this->pdo->prepare($q);
            $p->execute();
        }

        return $result;
    }

}