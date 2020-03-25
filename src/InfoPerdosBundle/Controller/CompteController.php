<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/10/2016
 * Time: 15:23
 */

namespace InfoPerdosBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompteController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('InfoPerdosBundle:Compte:index.html.twig',array());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function pccsAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);

        if($action == 0)
        {
            $pccs = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPccs($dossier);
            return new Response(Boost::serialize($pccs));
        }
        elseif($action == 1)
        {
            $pccsF = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPccsTiers($dossier,0);
            $pccsC = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPccsTiers($dossier,1);

            $f = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPccTier($dossier,0);
            $c = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPccTier($dossier,1);

            return $this->render('InfoPerdosBundle:Compte:pcc-combow.html.twig',array('pccsF'=>$pccsF,'pccsC'=>$pccsC,'f'=>$f,'c'=>$c));
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function setTierAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);

        $pcc = Boost::deboost($post->get('pcc'),$this);
        $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getById($pcc);

        $type = intval($post->get('type'));

        return new Response($this->getDoctrine()->getRepository('AppBundle:Pcc')->setTier($dossier,$pcc,$type));
    }
}