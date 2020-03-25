<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 18/12/2017
 * Time: 09:37
 */

namespace CleBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Cle;
use AppBundle\Entity\CleDossiers;
use AppBundle\Entity\Dossier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CleController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $dossiers = [];
        $clients = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->getUserClients($this->getUser());

        foreach ($clients as $client)
        {
            /** @var Dossier[] $doss */
            $doss = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(),$client);
            foreach ($doss as $dos) $dossiers[] = $dos->getId();
        }
        return $this->render('CleBundle:Cle:index.html.twig',['dossiers' => implode(';',$dossiers)]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listeAction(Request $request)
    {
        $cles = $this->getDoctrine()->getRepository('AppBundle:CleDossiers')
            ->clesDesactiver();
        $results = [];
        $dossiers = explode(';',$request->request->get('dossiers'));

        foreach ($cles as $key => $cle)
        {
            $dossiersExcludes = [];

            if (strpos($key,'_') !== false)
            {
                $cleDossiers = $this->getDoctrine()->getRepository('AppBundle:CleDossiers')
                    ->getCleDossiers($cle);

                foreach ($cleDossiers as $cleDossier)
                {
                    if (in_array($cleDossier->getDossier()->getId(),$dossiers))
                    {
                        $dossiersExcludes[] = (object)
                        [
                            'id' => Boost::boost($cleDossier->getDossier()->getId()),
                            'nom' => $cleDossier->getDossier()->getNom(),
                            'c_nom' => $cleDossier->getDossier()->getSite()->getClient()->getNom()
                        ];
                    }
                }
            }

            $results[] = (object)
            [
                'id' => Boost::boost($cle->getId()),
                'cl' => $cle->getCle(),
                'dx' =>  $dossiersExcludes
            ];
        }

        return new JsonResponse($results);
    }

    public function cleDossiersEditAction(Request $request)
    {
        $cle = Boost::deboost($request->request->get('cle'),$this);
        $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')
            ->find($cle);

        $action = intval($request->request->get('action'));
        $dossiersIds = explode(';',$request->request->get('dossiers'));

        if ($action == 0)
        {
            $cleDossiers = $this->getCleDossiersForCle($cle,$dossiersIds);

            return $this->render('CleBundle:Cle:cle-dossiers.html.twig',[
                'cle' => $cle,
                'cleDossiers' => json_encode($cleDossiers),
            ]);
        }
        else
        {
            $dossier = Boost::deboost($request->request->get('dossier'),$this);
            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->find($dossier);

            $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossiers')
                ->getCleDossiersForDossier($cle,$dossier);

            $em = $this->getDoctrine()->getManager();
            if ($action == 1 && !$cleDossier)
            {
                $cleDossier = new CleDossiers();
                $cleDossier
                    ->setDossier($dossier)
                    ->setCle($cle);
                $em->persist($cleDossier);
            }
            elseif ($action == 2 && $cleDossier)
            {
                $cleDoss = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
                    ->getCleDossierByCle($cle,$dossier);
                if ($cleDoss) $em->remove($cleDoss);

                $em->remove($cleDossier);
            }
            $em->flush();

            $cleDossiers = $this->getCleDossiersForCle($cle,$dossiersIds);
            return new JsonResponse($cleDossiers);
        }
    }

    /**
     * @param Cle $cle
     * @param $dossiersIds
     * @return array
     */
    private function getCleDossiersForCle(Cle $cle,$dossiersIds)
    {
        $cleDossiers = [];
        $clDs = $this->getDoctrine()->getRepository('AppBundle:CleDossiers')
            ->getCleDossiers($cle);

        foreach ($clDs as $clD)
        {
            if (in_array($clD->getDossier()->getId(),$dossiersIds))
                $cleDossiers[] = (object)
                [
                    'id' => Boost::boost($clD->getDossier()->getId()),
                    'nom' => $clD->getDossier()->getNom(),
                    'c_nom' => $clD->getDossier()->getSite()->getClient()->getNom()
                ];
        }

        return $cleDossiers;
    }
}