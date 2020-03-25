<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/02/2019
 * Time: 11:03
 */

namespace DrtBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\ImportParam;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImportExportController extends Controller
{
    public function indexAction()
    {
        return $this->render('DrtBundle:ImportExport:index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function paramsAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        $site = Boost::deboost($request->request->get('site'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($client) || is_bool($site) || is_bool($dossier)) return new Response('security');

        $client = $this->getDoctrine()->getRepository('AppBundle:Client')
            ->find($client);
        $site = $this->getDoctrine()->getRepository('AppBundle:Site')
            ->find($site);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $exercice = intval($request->request->get('exercice'));

        /** @var Dossier[] $dossiers */
        $dossiers = [];
        if ($dossier) $dossiers[] = $dossier;
        else $dossiers = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(),$client,$site,$exercice);

        $results = [];
        foreach ($dossiers as $dossier)
        {
            $results[] = $this->dossierParam($dossier);
        }

        return new JsonResponse($results);

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => [
                'dossiers' => $dossiers
            ]
        ]);
    }

    private function dossierParam(Dossier $dossier)
    {
        $importParametre = $this->getDoctrine()->getRepository('AppBundle:ImportParam')
            ->getNotPonctuelForDossier($dossier);

        //0=ponctuel,1=annuel,2=semestriel,3=quadrimestriel,4=trimestriel,6=bimensuel,12=mensuel
        $periodes = [
            1 => 'Annuel',
            2 => 'Semestriel',
            3 => 'Quadrimestriel',
            4 => 'Trimestriel',
            6 => 'Bimensuel',
            12 => 'Mensuel'
        ];

        $nextPonctuel = $this->getDoctrine()->getRepository('AppBundle:ImportParam')
            ->getNextPonctuel($dossier);

        return (object)
        [
            'id' => Boost::boost($dossier->getId()),
            'cli' => $dossier->getSite()->getClient()->getNom(),
            'dos' => $dossier->getNom(),
            'fre' => $importParametre ? $periodes[$importParametre->getPeriode()] : '',
            'jrs' => $importParametre ? $importParametre->getJour() : '',
            'pon' => $nextPonctuel ? $nextPonctuel->getDate()->format('d/m/Y') : ''
        ];
    }

    public function editParamAction(Request $request)
    {
        $action = intval($request->request->get('action'));
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $em = $this->getDoctrine()->getManager();

        $importParametre = $this->getDoctrine()->getRepository('AppBundle:ImportParam')
            ->getNotPonctuelForDossier($dossier);
        if ($action == 0)
        {
            $ponctuels = $this->getDoctrine()->getRepository('AppBundle:ImportParam')
                ->getPonctuels($dossier);
            return $this->render('DrtBundle:ImportExport:param-edit.html.twig',[
                'importParametre' => $importParametre,
                'ponctuels' => $ponctuels
            ]);
        }
        elseif ($action == 1)
        {
            $periode = intval($request->request->get('frequence'));
            $jour = intval($request->request->get('jour'));
            $aPartirDe = intval($request->request->get('a_partir_de'));
            $date = null;
            if ($aPartirDe == 3) $date = \DateTime::createFromFormat('d/m/Y',$request->request->get('date'));

            if ($periode == -1 && $importParametre) $em->remove($importParametre);
            else
            {
                $add = false;
                if (!$importParametre)
                {
                    $importParametre = new ImportParam();
                    $importParametre->setDossier($dossier);
                    $add = true;
                }

                $importParametre
                    ->setDate($date)
                    ->setPeriode($periode)
                    ->setCalculerAPartir($aPartirDe)
                    ->setJour($jour);

                if ($add) $em->persist($importParametre);
                $em->flush();

                $ponctuels = json_decode($request->request->get('ponctuels'));
                foreach ($ponctuels as $p)
                {
                    $importParam = Boost::deboost($p->id,$this);
                    if(is_bool($importParam)) return new Response('security');
                    $importParam = $this->getDoctrine()->getRepository('AppBundle:ImportParam')
                        ->find($importParam);

                    $date = \DateTime::createFromFormat('d/m/Y',$p->date);
                    if ($date === false || array_sum($date::getLastErrors()))
                        $date = null;

                    if ($importParam && (intval($p->active) == 0 || !$date))
                        $em->remove($importParam);
                    elseif ($date)
                    {
                        if ($importParam)
                            $importParam->setDate($date);
                        else
                        {
                            $importParam = new ImportParam();
                            $importParam
                                ->setDate($date)
                                ->setPeriode(0)
                                ->setDossier($dossier);

                            $em->persist($importParam);
                        }
                    }
                }
            }

            $em->flush();
            return new JsonResponse($this->dossierParam($dossier));
        }
    }
}