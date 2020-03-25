<?php

namespace InfoPerdosBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Entity\LogActivite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class StatutClientController extends Controller
{

    /**
     * Index Statut Client (Liste client à charger avec AJAX)
     *
     * @return Response
     */
    public function clientStatutAction()
    {
        return $this->render('InfoPerdosBundle:Client:client-statut.html.twig');
    }

    public function clientStatusListeAction()
    {
        $clients = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->getAll();
        $rows = [];
        /** @var Client $client */
        foreach ($clients as $client) {
            $date_status = "";
            if ($client->getStatusUpdate()) {
                $date_status = $client->getStatusUpdate()->format('d/m/Y');
            }
            $rows[] = [
                'id' => $client->getId(),
                'cell' => [
                    mb_strtoupper($client->getNom(), 'UTF-8'),
                    $client->getStatus(),
                    $client->getStatus() == 1 ? TRUE : FALSE,
                    $date_status,
                ]
            ];
        }
        $data = [
            'rows' => $rows,
        ];
        return new JsonResponse($data);
    }

    public function clientStatusEditAction(Request $request, Client $client)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()
                ->getManager();
            $status = intval($request->request->get('status', 0));
            $client
                ->setStatus($status)
                ->setStatusUpdate(new \DateTime());

            $message = '';
            if(intval($status) === 1){
                $message = 'ACTIVATION CLIENT';
            }
            else{
                $message = 'DESACTIVATION CLIENT';
            }

            $logActivite = new LogActivite();
            $logActivite->setUtilisateur($this->getUser())
                ->setClient($client)
                ->setDate(new \DateTime())
                ->setType(3)
                ->setMessage($message)
            ;



            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $logActivite->setIp($_SERVER['REMOTE_ADDR']);
            }
            if (!empty($_SERVER['SERVER_NAME'])) {
                $logActivite->setDomaine($_SERVER['SERVER_NAME']);
            }



            $em->persist($logActivite);

            $em->flush();

            $data = [
                'erreur' => FALSE,
            ];

            return new JsonResponse(json_encode($data));
        } else {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }
}
