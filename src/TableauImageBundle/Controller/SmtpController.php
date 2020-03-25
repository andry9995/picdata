<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 19/12/2017
 * Time: 13:26
 */

namespace TableauImageBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Smtp;
use AppBundle\Functions\FormUtility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TableauImageBundle\Form\ParamSmtpType;

class SmtpController extends Controller
{

    /**
     * Paramètres SMTP pour un client
     *
     * @param $client
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function smtpClientAction($client)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        if ($the_client) {
            /** @var \AppBundle\Entity\Smtp $smtp */
            $smtp = $this->getDoctrine()
                ->getRepository('AppBundle:Smtp')
                ->getSmtpByClient($the_client);
            $data = "";
            if ($smtp) {
                $data = [
                    'id' => $smtp->getId(),
                    'smtp' => $smtp->getSmtp(),
                    'port' => $smtp->getPort(),
                    'login' => $smtp->getLogin(),
                    'password' => $smtp->getPassword(),
                    'certificate' => $smtp->getCertificate(),
                    'copie' => $smtp->getCopie()
                ];
            }
            return new JsonResponse($data);
        } else {
            throw new NotFoundHttpException("Client introuvable.");
        }
    }

    public function smtpClientUpdateAction(Request $request, $client)
    {
        $client_id = Boost::deboost($client, $this);
        $the_client = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->find($client_id);
        if ($the_client) {
            $form = $this->createForm(ParamSmtpType::class);
            $form->handleRequest($request);
            $error_data = [];
            if (!$form->isValid()) {
                $error_data = array_merge($error_data, FormUtility::getErrorMessages($form));
                $data = [
                    'is_form_valid' => FALSE,
                    'message' => 'Formulaire invalide',
                    'error_data' => $error_data,
                ];
                return new JsonResponse($data);
            } else {
                $em = $this->getDoctrine()->getManager();
                $smtp_client = $this->getDoctrine()
                    ->getRepository('AppBundle:Smtp')
                    ->getSmtpByClient($the_client);
                if (!$smtp_client) {
                    $smtp_client = new Smtp();
                }
                $smtp = $form['smtp']->getData();
                $port = $form['port']->getData();
                $login = $form['login']->getData();
                $password = $form['password']->getData();
                $certificate = $form['certificate']->getData();

                $copie = $form['copie']->getData();

                $smtp_client->setSmtp($smtp)
                    ->setPort($port)
                    ->setLogin($login)
                    ->setPassword($password)
                    ->setClient($the_client)
                    ->setCopie($copie);

                if ($certificate && $certificate != "") {
                    $smtp_client->setCertificate($certificate);
                } else {
                    $smtp_client->setCertificate(NULL);
                }
                $em->persist($smtp_client);
                $em->flush();
                $data = [
                    'is_form_valid' => TRUE,
                    'erreur' => FALSE,
                ];
                return new JsonResponse($data);
            }
        } else {
            throw new NotFoundHttpException("Client introuvable.");
        }
    }
}