<?php

namespace FacturationBundle\Controller;

use AppBundle\Entity\FactClientAssocie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ClientAssocieController extends Controller
{
    /**
     * Index pour clients associés
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function indexAction()
    {
        return $this->render('FacturationBundle:ClientAssocie:index.html.twig');
    }

    /**
     * Liste clients associés pour jqGrid
     *
     * @param Request $request
     * @param $client : le client principal
     * @return JsonResponse
     */
    public function clientAssocieAction(Request $request, $client)
    {
        if ($request->isXmlHttpRequest()) {
            $client_id = Boost::deboost($client, $this);
            $the_client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
            if ($the_client) {
                $client_associes = $this->getDoctrine()
                    ->getRepository('AppBundle:FactClientAssocie')
                    ->getClientAssocie($the_client);

                $rows = array();

                /** @var FactClientAssocie $client_associe */
                foreach ($client_associes as $client_associe) {
                    $rows[] = array(
                        'id' => $client_associe->getId(),
                        'cell' => array(
                            $client_associe->getClientAutre()->getNom(),
                            '<i class="fa fa-trash icon-action js-delete-client-associe" title="Supprimer"></i>',
                        )
                    );
                }
                $liste = array(
                    'rows' => $rows,
                );
                return new JsonResponse($liste);
            } else {
                throw new NotFoundHttpException('Client introuvable.');
            }
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Ajouter un client associé
     *
     * @param Request $request
     * @param $client : le client principal
     */
    public function clientAssocieAddAction(Request $request, $client)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()
                ->getManager();
            $client_id = Boost::deboost($client, $this);
            $the_client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($client_id);
            if ($the_client) {
                /* Le client à ajouter comme client associé */
                $client_autre_id = Boost::deboost($request->request->get('client_autre'), $this);
                $client_autre = $this->getDoctrine()
                    ->getRepository('AppBundle:Client')
                    ->find($client_autre_id);
                if ($client_autre) {
                    try {
                        $factClientAssocie = new FactClientAssocie();
                        $factClientAssocie
                            ->setClientPrincipal($the_client)
                            ->setClientAutre($client_autre);
                        $em->persist($factClientAssocie);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    } catch (\Exception $ex) {
                        if (strpos($ex->getMessage(), 'client_client_UNIQUE') > 0) {
                            $data = array(
                                'erreur' => true,
                                'erreur_text' => 'Le client ' . $client_autre->getNom() . ' est déjà associé au client ' . $the_client->getNom(),
                            );
                        } else {
                            $data = array(
                                'erreur' => true,
                                'erreur_text' => $ex->getMessage(),
                            );
                        }

                        return new JsonResponse(json_encode($data));
                    }
                } else {
                    throw new NotFoundHttpException('Client introuvable.');
                }
            } else {
                throw new NotFoundHttpException('Client introuvable.');
            }
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }

    /**
     * Supprimer un client associé
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function clientAssocieRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $client_associe = $this->getDoctrine()
                ->getRepository('AppBundle:FactClientAssocie')
                ->find($id);
            if ($client_associe) {
                $em = $this->getDoctrine()
                    ->getManager();
                $em->remove($client_associe);
                $em->flush();

                $data = array(
                    'erreur' => false,
                );
                return new JsonResponse(json_encode($data));
            } else {
                throw new NotFoundHttpException('Client introuvable.' . $id);
            }
        } else {
            throw new AccessDeniedException('Accès refusé.');
        }
    }
}
