<?php
/**
 * Created by PhpStorm.
 * User: Dinoh
 * Date: 27/02/2019
 * Time: 08:00
 */
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImageNbpageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:imagetsetnbpage')
            ->setDescription('Vérification extension des images et le nombre de page des images puis modification si 0')
            ->setHelp("Cette commande permet de vérifier l'extension des images et le nombre de page des images puis les modifier si 0");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            "Verification extensions des images et nombre page 0, modification nombre page des images",
            "=============================================",
            "",
        ]);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $images = $em->getRepository('AppBundle:Image')
                     ->createQueryBuilder('image')
                     ->select('image')
                     ->innerJoin('image.lot', 'lot')
                     ->addSelect('lot')
                     ->where(" image.nbpage = 0 AND image.extImage='pdf' ")
                     ->orderBy('image.id')
                     ->getQuery()
                     ->getResult();

        $nbPage = 1;
        $nb_success = 0;
        foreach ($images as $image) {
            $chemin = $this->getContainer()->getParameter('kernel.root_dir') . '/../web/IMAGES/' .$image->getLot()->getDateScan()->format('Ymd') . '/' . $image->getNom() . '.pdf';
            $stream = fopen($chemin, "r");
            if ( filesize($chemin)>0 ) {
                $content = fread($stream, filesize($chemin));

                if ( !(!$stream || !$content) ) {
                    $regex = "/\/Page\W/";
                    if ( preg_match_all($regex, $content, $matches) ) {
                        $nbPage = preg_match_all("/\/Page\W/", $content, $matches);
                    }
                }
                $image->setNbpage($nbPage);
                $em->flush();
                $nb_success++;
            }
        }
        $output->writeln([
            "Success: $nb_success",
        ]);

        $output->writeln([
            "Modification nombre page image fini.",
            "=============================================",
            "",
        ]);
    }
}