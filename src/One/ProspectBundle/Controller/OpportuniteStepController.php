<?php
/**
 * Created by PhpStorm.
 * User: Mamy Rakotonirina
 * Date: 10/04/2018
 * Time: 11:17
 */

namespace One\ProspectBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\OneOpportuniteStep;
use AppBundle\Entity\OneStatusOpp;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OpportuniteStepController extends Controller
{
    /**
     * List des étapes d'une opportunité
     * @return Response
     */
    public function listAction()
    {
        return $this->render('OneProspectBundle:OpportuniteStep:list.html.twig');
    }

    public function newAction()
    {
        return $this->render('OneProspectBundle:OpportuniteStep:new.html.twig');
    }

    public function editAction($id) {
        $opportuniteStep = $this->getDoctrine()->getRepository('AppBundle:OneStatusOpp')->find($id);
        return $this->render('OneProspectBundle:OpportuniteStep:edit.html.twig', array(
            'opportuniteStep' => $opportuniteStep,
        ));
    }

    public function saveAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $posted = $request->request->all();

            $dossierId = $posted['dossier-id'];

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find(Boost::deboost($dossierId, $this));

            $status = 0;
            if(isset($posted['status'])){
                if($posted['status'] === 'on'){
                    $status = 1;
                }
            }


            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $lastPosition = (int)$this->getDoctrine()
                        ->getRepository('AppBundle:OneStatusOpp')
                        ->getLastPosition($dossier);

                    $opportuniteStep = new OneStatusOpp();
                    $opportuniteStep->setNom($posted['nom']);
                    $opportuniteStep->setStatus($status);
                    $opportuniteStep->setCreeLe(new \DateTime('now'));
                    $opportuniteStep->setOrdre($lastPosition+1);
                    $opportuniteStep->setDossier($dossier);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($opportuniteStep);
                    $em->flush();

                    $response = array('type' => 'success', 'id' => $opportuniteStep->getId());
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error');
                    return new JsonResponse($response);
                }
            } else {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $opportuniteStep = $em->getRepository('AppBundle:OneStatusOpp')->find($posted['id']);
                    $opportuniteStep->setNom($posted['nom']);
                    $opportuniteStep->setStatus($status);
                    $opportuniteStep->setDossier($dossier);
                    $opportuniteStep->setCreeLe(new \DateTime('now'));
                    $em->flush();

                    $response = array('type' => 'success', 'id' => $opportuniteStep->getId());
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error');
                    return new JsonResponse($response);
                }
            }
        }

        throw new AccessDeniedException('Accès refusé');
    }

    public function deleteAction($id) {
        try {
            $opportuniteStep = $this->getDoctrine()->getRepository('AppBundle:OneStatusOpp')->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($opportuniteStep);
            $em->flush();

            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
    }

    public function updateAction(Request $request)
    {
        if ($request->isMethod('GET')) {
            $opp_id = str_replace('opp-', '', $request->query->get('opp_id'));
            $step_id = $request->query->get('step_id');

            $em = $this->getDoctrine()->getManager();
            $opportunite = $em->getRepository('AppBundle:OneOpportunite')->find((int)$opp_id);
            $statut = $this->getDoctrine()->getRepository('AppBundle:OneStatusOpp')->find((int)$step_id);

            $opportunite->setOneStatusOpp($statut);
            $em->flush();
        }
        return new Response('debug');
    }

    public function orderAction(Request $request)
    {
        if ($request->isMethod('GET')) {
            $em = $this->getDoctrine()->getManager();
            $steps = $request->query->get('steps');

            foreach($steps as $step_id => $step_order) {
                $step_id = (int)str_replace('step-', '', $step_id);
                $oppStat = $em->getRepository('AppBundle:OneStatusOpp')->find($step_id);
                $oppStat->setOrdre((int)$step_order);
                $em->flush();
            }
        }
        return new Response('debug');
    }
}