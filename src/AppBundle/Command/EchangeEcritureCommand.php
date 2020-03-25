<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 27/03/2019
 * Time: 11:06
 */

namespace AppBundle\Command;
use AppBundle\Entity\EchangeItem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class EchangeEcritureCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:echangeEcriture')
            ->setDescription('Analyse DRT')
            ->setHelp('Cette commande analyse les DRT et met a jour echange ecriture');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            "Analyse DRT",
            "=============================================",
            "",
        ]);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $echangeType = $em->getRepository('AppBundle:EchangeType')
            ->findOneBy(['nom' => 'DRT']);

        /** @var EchangeItem[] $echangeItems */
        $echangeItems = [];
        if ($echangeType)
            $echangeItems = $em->getRepository('AppBundle:EchangeItem')
                ->getLasts($echangeType);

        foreach ($echangeItems as $echangeItem)
        {
            $em->getRepository('AppBundle:EchangeEcriture')
                ->getEcritures(
                    $echangeItem,
                    $this->getContainer()->get('kernel')->getRootDir()."/../web/echange/".$echangeItem->getNomFichier(),
                    true,
                    true);
        }

        $output->writeln([
            "=============================================",
            "Mise a jour finie",
        ]);
    }
}