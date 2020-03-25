<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 14/04/2017
 * Time: 16:00
 */

namespace EtatBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\EtatCommentaire;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowController extends Controller
{
    /**
     * ETAT FINANCIER
     * @return Response
     */
    public function indexFinancierAction()
    {
        return $this->index(0);
    }

    /**
     * ETAT DE GESTION
     * @return Response
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
        $isModifiable = $this->get('security.authorization_checker')->isGranted('ROLE_CLIENT_RESP');
        $etats = $this->getDoctrine()->getRepository('AppBundle:Etat')->getEtats($etat,null,null,0,false);
        return $this->render('EtatBundle:Show:index.html.twig',[
            'etat' => $etat,
            'etats' => $etats,
            'isModifiable' => $isModifiable
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function periodesAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $indicateurs = json_decode($post->get('indicateurs'));
        $periodes = [];
        foreach ($indicateurs as $ind)
        {
            $etat = Boost::deboost($ind,$this);
            if(is_bool($etat)) return new Response('security');
            $etat = $this->getDoctrine()->getRepository('AppBundle:Etat')->getById($etat);
            $indicateur = $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getEtatRegimeFiscal($etat,$dossier);
            $periodeBinaire = decbin($indicateur->getPeriode());
            while (strlen($periodeBinaire) < 4) $periodeBinaire = '0'.$periodeBinaire;
            $periodes[] = $periodeBinaire;
        }

        return $this->forward('AppBundle:Commun:periodes',array('request'=>$request,'otherParam'=>json_encode($periodes)));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function etatsStatusAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $etat = intval($post->get('etat'));
        $etats = json_decode($post->get('etats'));
        $etatsIds = [];
        foreach ($etats as $etat)
        {
            $id = Boost::deboost($etat,$this);
            if(is_bool($dossier)) return new Response('security');
            $etatsIds[] = $id;
        }
        $result = $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getEtatRegimeStatus($dossier,$etatsIds,$this->getUser());
        return new JsonResponse($result);
    }

    public function commentaireAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'), $this);
        $etat = Boost::deboost($request->request->get('indicateur'),$this);
        if(is_bool($dossier) || is_bool($etat)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $etat = $this->getDoctrine()->getRepository('AppBundle:Etat')
            ->find($etat);

        $isModifiable = $this->get('security.authorization_checker')->isGranted('ROLE_CLIENT_RESP');

        $etatCommentaire = $this->getDoctrine()->getRepository('AppBundle:EtatCommentaire')
            ->getByEtatDossier($etat,$dossier);

        $commentaire = $etatCommentaire ? $etatCommentaire->getCommentaire() : '';
        return $this->render('EtatBundle:Show:commentaire.html.twig',[
            'commentaire' => $commentaire,
            'isModifiable' => $isModifiable
        ]);
    }

    public function commentaireSaveAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'), $this);
        $etat = Boost::deboost($request->request->get('indicateur'),$this);
        if(is_bool($dossier) || is_bool($etat)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $etat = $this->getDoctrine()->getRepository('AppBundle:Etat')
            ->find($etat);

        $etatCommentaire = $this->getDoctrine()->getRepository('AppBundle:EtatCommentaire')
            ->getByEtatDossier($etat,$dossier);
        $commentaire = trim($request->request->get('commentaire'));

        $em = $this->getDoctrine()->getManager();
        $add = false;

        if (!$etatCommentaire)
        {
            $add = true;
            $etatCommentaire = new EtatCommentaire();
            $etatCommentaire
                ->setDossier($dossier)
                ->setEtat($etat);
        }

        $etatCommentaire
            ->setCommentaire($commentaire)
            ->setUtilisateur($this->getUser())
            ->setDateModif(new \DateTime());
        if ($add) $em->persist($etatCommentaire);

        $em->flush();
        return new Response(1);
    }
}