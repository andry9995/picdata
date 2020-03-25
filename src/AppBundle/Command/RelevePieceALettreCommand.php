<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 20/08/2019
 * Time: 14:35
 */

namespace AppBundle\Command;

use AppBundle\Entity\BanqueCompte;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class RelevePieceALettreCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:relevePieceCle')
            ->setDescription('Mise à jour Piece a lettrer')
            ->setHelp('Cette commande met a jour les pieces (flaguer) a lettrer dans releve');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            "Mise a jour releve piece lettrer et cle a valider",
            "=============================================",
            "",
        ]);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var BanqueCompte[] $banqueComptes */
        $banqueComptes = $em->getRepository('AppBundle:Releve')
            ->getAllBanqueCompteInReleve();

        $annee = intval((new \DateTime())->format('Y'));
        $exercices = [$annee - 1, $annee];

        foreach ($banqueComptes as $banqueCompte)
        {
            $output->writeln([
                '--------------------------------'
            ]);
            $output->writeln([
                'Compte = ' . $banqueCompte->getId().'-'.$banqueCompte->getNumcompte().' # '.
                'Dossier = ' . $banqueCompte->getDossier()->getId().'-'. $banqueCompte->getDossier()->getNom()
            ]);

            foreach ($exercices as $exercice)
            {
                $output->write([
                    $exercice. '-'
                ]);

                $responses = $em->getRepository('AppBundle:Releve')->getRelevesNew($banqueCompte->getDossier(),$exercice,null,$banqueCompte);
                $em->getRepository('AppBundle:Releve')->majPieceCle($responses,$banqueCompte->getDossier());

                $output->writeln(['OK']);
            }

            $output->writeln([
                '--------------------------------'
            ]);
        }

        $output->writeln([
            "=============================================",
            "Mise a jour finie",
        ]);
    }
}