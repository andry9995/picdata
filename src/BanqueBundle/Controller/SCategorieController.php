<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 13/01/2020
 * Time: 09:04
 */

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\SouscategoriePasSaisir;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SCategorieController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function sCategorieDossierAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        $site = Boost::deboost($request->request->get('site'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $exercice = intval($request->request->get('exercice'));
        if(is_bool($client) || is_bool($site) || is_bool($dossier)) return new Response('security');

        /** @var Dossier[] $dossiers */
        $dossiers = [];
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        if ($dossier) $dossiers[] = $dossier;
        else
        {
            $client = $this->getDoctrine()->getRepository('AppBundle:Client')->find($client);
            $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($site);

            $dossiers = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(), $client, $site, $exercice);


        }

        $sousCategoriesObs = $this->getDoctrine()->getRepository('AppBundle:Souscategorie')
            ->getObs();

        $results = [];
        $headers = [];
        $first = true;
        foreach ($dossiers as $dossier)
        {
            $this->getDoctrine()->getRepository('AppBundle:SouscategoriePasSaisir')
                ->initialise($dossier);

            $res =
            [
                'id' => Boost::boost($dossier->getId()),
                'd' => $dossier->getNom(),
            ];

            foreach ($sousCategoriesObs as $key => $souscategorie)
            {
                $aSaisir = $this->getDoctrine()->getRepository('AppBundle:SouscategoriePasSaisir')
                    ->aSaisir($dossier, $souscategorie);
                if ($first) $headers[] = (object)
                [
                    'l' => $souscategorie->getLibelleNew(),
                    'id' => Boost::boost($souscategorie->getId()),
                    'i' => $souscategorie->getId()
                ];

                $res[$key] = (object)
                [
                    'id' => Boost::boost($souscategorie->getId()),
                    's' => $aSaisir ? 1 : 0,
                    'i' => $souscategorie->getId()
                ];
            }

            $first = false;
            $results[] = (object) $res;
        }

        return new JsonResponse((object)[
            'datas' => $results,
            'headers' => $headers
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveStatusAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'), $this);
        $sousCategorie = Boost::deboost($request->request->get('s_categorie'), $this);
        if(is_bool($dossier) || is_bool($sousCategorie)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $sousCategorie = $this->getDoctrine()->getRepository('AppBundle:Souscategorie')
            ->find($sousCategorie);

        $isChecked = intval($request->request->get('status')) == 1;
        $sousCategoriePasSaisir = $this->getDoctrine()->getRepository('AppBundle:SouscategoriePasSaisir')
            ->getSousCategoriePasSaisir($dossier,$sousCategorie);

        $em = $this->getDoctrine()->getManager();
        if ($isChecked && $sousCategoriePasSaisir)
            $em->remove($sousCategoriePasSaisir);
        elseif(!$isChecked && !$sousCategoriePasSaisir)
        {
            $sousCategoriePasSaisir = new SouscategoriePasSaisir();
            $sousCategoriePasSaisir
                ->setSouscategorie($sousCategorie)
                ->setDossier($dossier);

            $em->persist($sousCategoriePasSaisir);
        }

        $em->flush();
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveStatusDossiersAction(Request $request)
    {
        $sousCategorie = Boost::deboost($request->request->get('sc'), $this);
        if(is_bool($sousCategorie)) return new Response('security');

        $sousCategorie = $this->getDoctrine()->getRepository('AppBundle:Souscategorie')
            ->find($sousCategorie);

        $aSaisir = intval($request->request->get('a_saisir')) == 1;
        $em = $this->getDoctrine()->getManager();

        $dossiers = json_decode($request->request->get('dossiers'));
        foreach ($dossiers as $ds)
        {
            $dossier = Boost::deboost($ds,$this);
            if(is_bool($dossier)) return new Response('security');

            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->find($dossier);

            $sousCategoriePasSaisir = $this->getDoctrine()->getRepository('AppBundle:SouscategoriePasSaisir')
                ->getSousCategoriePasSaisir($dossier,$sousCategorie);

            if ($aSaisir && $sousCategoriePasSaisir)
                $em->remove($sousCategoriePasSaisir);
            elseif (!$aSaisir && !$sousCategoriePasSaisir)
            {
                $sousCategoriePasSaisir = new SouscategoriePasSaisir();
                $sousCategoriePasSaisir
                    ->setDossier($dossier)
                    ->setSouscategorie($sousCategorie);
                $em->persist($sousCategoriePasSaisir);
            }
        }

        $em->flush();
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function natureReleveAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);

        $nature = intval($request->request->get('nature'));
        $releve->setNature($nature);

        $this->getDoctrine()->getManager()->flush();

        return new Response(1);

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => [$nature , $releve]
        ]);
    }
}