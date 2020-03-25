<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 04/10/2019
 * Time: 17:04
 */

namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImageDropboxCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:imagedropbox')
            ->setDescription('Numerotation automatique image sur Dropbox')
            ->setHelp('Cette commande permet de numeroter automatiquement les images sur Dropbox');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            "Recherche des images dans sur Dropbox",
            "=============================================",
            "",
        ]);

        try {
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $em->getRepository('AppBundle:ImageDropbox')
                ->parseDropbox(1, $dossier_inconnus);

            $output->writeln([
                "Numerotation des images",
                "=============================================",
                "",
            ]);

            $em->getRepository('AppBundle:ImageDropbox')
                ->numeroter();

            if (is_array($dossier_inconnus) && count($dossier_inconnus) > 0) {
                $liste = '<table border="1"><thead><tr><th>Clients</th><th>Dossiers</th></tr></thead><tbody>';

                foreach ($dossier_inconnus as $item) {
                    $liste .= '<tr><td>' . $item['client'] . '</td><td>' . $item['dossier'] . '</td></tr>';
                }

                $liste .= '</tbody></table>';

                $contenu = "Le système n'arrive pas à trouver les dossiers suivants:<br>" . $liste . '<br><br><em>Picdata.</em>';

                $destinataires = $em->getRepository('AppBundle:Config')
                    ->getEmailAccuseReception();
                $message = \Swift_Message::newInstance()
                    ->setSubject("Dossiers Inconnus - Image sur FTP")
                    ->setFrom('support@scriptura.biz', 'Image sur FTP')
                    ->setTo('arq@scriptura.biz');
                foreach ($destinataires as $destinataire) {
                    $message->addCc($destinataire);
                }
                $message->setBody(
                    $contenu, 'text/html');
                $this->getContainer()
                    ->get('mailer')
                    ->send($message);
            }

            $output->writeln([
                "=============================================",
                "Numerotation finie",
            ]);
        } catch (\Exception $e) {
            $output->writeln([
                "=============================================",
                "Une erreur est survenue",
                $e->getMessage(),
            ]);
        }
    }

}