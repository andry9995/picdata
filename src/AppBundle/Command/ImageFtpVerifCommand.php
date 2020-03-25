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

class ImageFtpVerifCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:imageftpverif')
            ->setDescription("Vérification de l'exhaustivité des images reçues sur FTP clients")
            ->setHelp("Cette commande permet de vérifier l'exhaustivité des images reçues sur FTP clients");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            "Verification des images recues",
            "=============================================",
            "",
        ]);

        try {
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $not_found = $em->getRepository('AppBundle:ImageFtp')->verifExhaustif();

            if (is_array($not_found) && count($not_found) > 0) {
                $liste = '<table border="1"><tr><th>Client</th><th>Fichier</th><th>Date envoi</th></tr>';
                foreach ($not_found as $item) {
                    $liste .= '<tr><td>' . $item['client'] . '</td><td>' . $item['filename'] . '</td><td>' . $item['datescan']->format('d/m/Y') . '</td></tr>';
                }
                $liste .= '</table>';

                $contenu = "Images dans FTP non trouvées:<br>" . $liste . '<br><br><em>Picdata.</em>';

                $destinataires = $em->getRepository('AppBundle:Config')
                    ->getEmailAccuseReception();
                $message = \Swift_Message::newInstance()
                    ->setSubject("Non trouvées - Image sur FTP")
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
                "Vérification finie",
            ]);
        } catch (\Exception $e) {
            $output->writeln([
                "=============================================",
                "Une erreur est survenue.",
                $e->getMessage(),
            ]);
        }
    }
}