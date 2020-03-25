<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 31/03/2017
 * Time: 11:03
 */

namespace EtatBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * ETAT FINANCIER
     */
    public function indexFinancierAction()
    {
        return $this->index(0);
    }

    /**
     * ETAT DE GESTION
     */
    public function indexGestionAction()
    {
        return $this->index(1);
    }

    /**
     * @param $etat
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index($etat)
    {
        $etats = $this->getDoctrine()->getRepository('AppBundle:Etat')->getEtatsListe($etat);
        return $this->render('EtatBundle:Admin:index.html.twig',array('adminGranted'=>$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'),'etat'=>$etat,'etats'=>$etats));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function etatDetailsAction(Request $request)
    {
        $post = $request->request;
        $client = Boost::deboost($post->get('client'),$this);
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $type = Boost::deboost($post->get('type'),$this);
        $regimeFiscal = Boost::deboost($post->get('regime'),$this);
        $etat = Boost::deboost($post->get('etat'),$this);
        if(is_bool($client) || is_bool($dossier) || is_bool($type) || is_bool($regimeFiscal) || is_bool($etat)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $type = intval($type);
        $regimeFiscal = $this->getDoctrine()->getRepository('AppBundle:RegimeFiscal')->find($regimeFiscal);
        $etat = $this->getDoctrine()->getRepository('AppBundle:Etat')->find($etat);

        if($dossier == null) $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
        else $client = null;

        $etatsDetails = $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')
            ->getEtatRegimes($etat,$regimeFiscal,$client,$dossier);

        $dStyles = $this->getDoctrine()->getRepository('AppBundle:IndicateurCell')->getDefaultStyles();
        return $this->render('EtatBundle:Admin:etats-regime.html.twig',array('etatsDetails'=>$etatsDetails, 'keyEtat'=>$etat->getId(), 'dStyles'=>$dStyles));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function etatsAction(Request $request)
    {
        $post = $request->request;
        $client = Boost::deboost($post->get('client'),$this);
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $type = Boost::deboost($post->get('type'),$this);
        $etat = intval($post->get('etat'));
        if(is_bool($client) || is_bool($dossier) || is_bool($type)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        $type = intval($type);

        if($dossier == null) $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
        else $client = null;

        $etats = $this->getDoctrine()->getRepository('AppBundle:Etat')->getEtats($etat,$client,$dossier,$type);

        return $this->render('EtatBundle:Admin:etats.html.twig',array('etats'=>$etats));

        return $this->render('EtatBundle:Default:test.html.twig',array('test'=>$etats));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function stylesAction(Request $request)
    {
        $post = $request->request;
        $styles = json_decode($post->get('styles'));

        return $this->render('EtatBundle:Admin:style.html.twig',array('styles'=>$styles));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function stylesChangeAction(Request $request)
    {
        $post = $request->request;
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateur)) return new Response('security');
        $isEtat = (intval($post->get('is_etat')) == 1);
        $indicateur = ($isEtat) ?
            $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getById($indicateur) :
            $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($indicateur);
        $styles = json_decode($post->get('styles'));
        $cellsIndexs = json_decode($post->get('cells'));

        $result = $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->changeStyles($indicateur,$isEtat,$styles,$cellsIndexs);

        return new Response($result);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function periodesAction(Request $request)
    {
        $post = $request->request;
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateur)) return new Response('security');
        $indicateur = $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getById($indicateur);

        $periodeBinary = decbin($indicateur->getPeriode());
        while (strlen($periodeBinary < 4)) $periodeBinary = '0'.$periodeBinary;

        return $this->render('EtatBundle:Admin:periode.html.twig',array('periodeBinary'=>$periodeBinary));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function periodesChangeAction(Request $request)
    {
        $post = $request->request;
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateur)) return new Response('security');
        $isEtat = (intval($post->get('is_etat')) == 1);

        $indicateur = ($isEtat) ?
            $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getById($indicateur) :
            $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($indicateur);

        $indicateur->setPeriode(bindec($post->get('periode')));

        $em = $this->getDoctrine()->getEntityManager();
        try
        {
            $em->flush();
            return new Response(1);
        }
        catch (\Exception $exception)
        {
            return new Response(0);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    /*public function showControlAction(Request $request)
    {
        $post = $request->request;
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateur)) return new Response('security');
        $isEtat = (intval($post->get('is_etat')) == 1);
        $cells = json_decode($post->get('cells'));
        $indicateurCells = [];
        foreach ($cells as $cell)
        {

        }

        return $this->render('EtatBundle:Admin:control.html.twig', array(
            'test'=>$cells
        ));
    }*/
}