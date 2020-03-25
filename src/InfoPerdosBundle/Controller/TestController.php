<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 14/03/2019
 * Time: 11:51
 */

namespace InfoPerdosBundle\Controller;


use AppBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestController extends Controller
{
    public function testAction(){

        $client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find(744);

        $from_details = $this->getFromDetails($client);

        $message = \Swift_Message::newInstance()
            ->setSubject("TEST SMTP")
            ->setFrom($from_details['address'], $from_details['label'])
            ->setTo('maharo@scriptura.biz')
            ->setBody(
               'TEST SMTP'
                , 'text/html');
        $this->get('app.mailer_par_client')
            ->getMailer($client)
            ->send($message);

        return new JsonResponse('Test Effectué');
    }

    private function getFromDetails(Client $client)
    {
        $smtp = $this->getDoctrine()
            ->getRepository('AppBundle:Smtp')
            ->findOneBy(array(
                'client' => $client
            ));

        $nomclient = ($client ? str_replace('_', '', strtolower($client->getNom())) : 'support');
        $nomclient = str_replace("'", '', $nomclient);
        $nomclient = str_replace("-", '', $nomclient);
        $address = $nomclient . '@lesexperts.biz';
        $label = "Equipe Support";

        if ($smtp) {
            $address = $smtp->getLogin();
        }

        return ['address' => $address, 'label' => $label];
    }

}