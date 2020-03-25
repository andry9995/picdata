<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 09/01/2018
 * Time: 14:54
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImageFtpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:imageftp')
            ->setDescription("Numerotation automatique image sur FTP clients")
            ->setHelp("Cette commande permet de Numerotater automatique image sur FTP clients");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            "Recherche des images dans FTP",
            "=============================================",
            "",
        ]);

        try {
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $em->getRepository('AppBundle:ImageFtp')
                ->ParseFtp(1, $dossier_inconnus);

            $output->writeln([
                "Numerotation des images",
                "=============================================",
                "",
            ]);

            $em->getRepository('AppBundle:ImageFtp')
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