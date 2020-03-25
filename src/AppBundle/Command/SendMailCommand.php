<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 15/01/2018
 * Time: 17:21
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendMailCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('app:sendmail')
            ->setDescription("Envoi email dans la liste")
            ->setHelp("Cette commande permet d'envoyer des emails listees dans la table des emails.");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            "Envoi des emails.",
            "=============================================",
            "",
        ]);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $emails = $em->getRepository('AppBundle:Emails')
            ->getNonEnvoye('RAPPEL_IMAGE');
        $nb_success = 0;
        $nb_fail = 0;

        /** @var \AppBundle\Entity\Emails $email */
        foreach ($emails as $email) {
            try {
                $smtp = \Swift_SmtpTransport::newInstance($email->getSmtp()
                    ->getSmtp(), $email->getSmtp()->getPort())
                    ->setUsername($email->getSmtp()->getLogin())
                    ->setPassword($email->getSmtp()->getPassword())
                    ->setAuthMode('login');
                if ($email->getSmtp() && trim($email->getSmtp()
                        ->getCertificate()) != '') {
                    $smtp->setEncryption($email->getSmtp()->getCertificate());
                }
                $mailer = \Swift_Mailer::newInstance($smtp);
                if ($email->getFromAddress()) {
                    if ($email->getFromLabel()) {
                        $from = [$email->getFromAddress() => $email->getFromLabel()];
                    } else {
                        $from = [$email->getFromAddress() => ''];
                    }
                } else {
                    $from = [$email->getSmtp()->getLogin() => ''];
                }

                $message = \Swift_Message::newInstance()
                    ->setSubject($email->getSujet())
                    ->setFrom($from)
                    ->setTo(array_filter(explode(";", $email->getToAddress())));
                if ($email->getCc()) {
                    $message->setCc(array_filter(explode(";", $email->getCc())));
                }
                $message->setBody($email->getContenu(), 'text/html', 'UTF-8')
                    ->setContentType('text/html')
                    ->setCharset('UTF-8');

                $result = $mailer->send($message);
                if ($result > 0) {
                    $nb_envoi = $email->getNbTentativeEnvoi() ? intval($email->getNbTentativeEnvoi()) + 1 : 1;
                    $email->setNbTentativeEnvoi($nb_envoi)
                        ->setStatus(1)
                        ->setDateEnvoi(new \DateTime());
                    $em->flush();
                    $nb_success++;
                }
            } catch (\Exception $ex) {
                $em = $this->getContainer()->get('doctrine.orm.entity_manager');
                $nb_envoi = $email->getNbTentativeEnvoi() ? intval($email->getNbTentativeEnvoi()) + 1 : 1;
                $email->setLastError($ex->getMessage())
                    ->setNbTentativeEnvoi($nb_envoi);
                $em->flush();
                $nb_fail++;
            }
            $output->writeln([
                "Success: $nb_success",
                "Failed: $nb_fail",
            ]);
        }

        $output->writeln([
            "Envoi fini.",
            "=============================================",
            "",
        ]);
    }
}