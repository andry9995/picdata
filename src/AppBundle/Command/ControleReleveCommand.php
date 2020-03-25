<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 13/07/2017
 * Time: 15:44
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ConsultationPieceBundle\Controller\releveManquantController;

class ControleReleveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:controlereleve')
            ->setDescription("Mettre à jour la liste des relevés manquants")
            ->setHelp("Cette commande permet de mettre à jour la liste des relevés manquant pour le tableau des images");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            "Mise a jour de la liste des releves manquants",
            "=============================================",
            "",
        ]);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->getRepository('AppBundle:ReleveManquant')
           ->updateReleveManquant();

        $output->writeln([
            "=============================================",
            "Mise a jour finie",
        ]);
    }
}