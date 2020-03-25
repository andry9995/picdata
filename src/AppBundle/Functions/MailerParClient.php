<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 14/02/2018
 * Time: 08:40
 */

namespace AppBundle\Functions;


use AppBundle\Entity\Client;
use Doctrine\ORM\EntityManager;

class MailerParClient
{
    private $em;
    private $mailer;

    public function __construct(EntityManager $em, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function getMailer(Client $client)
    {
        $smtp_client = $this->em->getRepository('AppBundle:Smtp')
            ->findOneBy(array(
                'client' => $client
            ));
        if (!$smtp_client) {
            $scriptura = $this->em
                ->getRepository('AppBundle:Client')
                ->findOneBy(array(
                    'nom' => 'SCRIPTURA',
                ));
            if ($scriptura) {
                $smtp_client = $this->em->getRepository('AppBundle:Smtp')
                    ->findOneBy(array(
                        'client' => $scriptura
                    ));
                if (!$smtp_client) {
                    return $this->mailer;
                }
            } else {
                return $this->mailer;
            }
        }

        $smtp = \Swift_SmtpTransport::newInstance($smtp_client->getSmtp(), $smtp_client->getPort())
            ->setUsername($smtp_client->getLogin())
            ->setPassword($smtp_client->getPassword())
            ->setAuthMode('login');
        if ($smtp_client->getCertificate() && trim($smtp_client->getCertificate()) != '') {
            $smtp->setEncryption($smtp_client->getCertificate());
        }
        $the_mailer = \Swift_Mailer::newInstance($smtp);

        return $the_mailer;
    }
}