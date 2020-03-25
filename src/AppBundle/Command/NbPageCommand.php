<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 28/03/2019
 * Time: 16:39
 */

namespace AppBundle\Command;

use AppBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\Exception\ContextErrorException;

class NbPageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:nb_page')
            ->setDescription('nb page')
            ->setHelp("maj nbr page pdf");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            "maj nbr page pdf",
            "=============================================",
            "",
        ]);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var Image[] $images */
        $images = $em->getRepository('AppBundle:Image')
            ->createQueryBuilder('image')
            ->select('image')
            ->innerJoin('image.lot', 'lot')
            ->addSelect('lot')
            ->where("image.extImage = :extention")
            ->setParameter('extention','pdf')
            ->andWhere('image.nom = :nom')
            ->setParameter('nom','NE0006RH9')
            /*->andWhere('lot.id = :lot_id')
            ->setParameter('lot_id',339308)*/
            ->getQuery()
            ->getResult();

        $output->writeln([
            "Success: " . count($images),
        ]);

        $nbPage = 1;
        $nb_success = 0;
        foreach ($images as $image) {
            $chemin = $this->getContainer()->getParameter('kernel.root_dir') . '/../web/IMAGES/' .$image->getLot()->getDateScan()->format('Ymd') . '/' . $image->getNom() . '.pdf';

            try
            {
                $stream = fopen($chemin, "r");
                $fileExist = true;
            }
            catch (ContextErrorException $ex)
            {
                $fileExist = false;
            }
            //$stream = fopen($chemin, "r");
            if ( $fileExist ) {
                $nbPage = intval(exec("pdfinfo /var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES/".$image->getLot()->getDateScan()->format('Ymd')."/".$image->getNom().".pdf | awk '/Pages/ {print $2}'"));

                /*if ( !(!$stream || !$content) ) {
                    $regex = "/\/Page\W/";
                    if ( preg_match_all($regex, $content, $matches) ) {
                        $nbPage = preg_match_all("/\/Page\W/", $content, $matches);
                    }
                }*/

                $output->writeln(
                    $image->getNom() . ' => ' . $nbPage
                );

                if ($nbPage != 0) $image->setNbpage($nbPage);
                $nb_success++;
            }
        }

        $em->flush();
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