<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 19/09/2016
 * Time: 17:09
 */

namespace IndicateurBundle\Controller;
use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\LazyProxy\Instantiator\RealServiceInstantiator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;

class AffichageController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('IndicateurBundle:Affichage:index.html.twig',array());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function packsAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        if($dossier == null) return new Response('error');

        //return new Response(1);
        $this->getDoctrine()->getRepository('AppBundle:Tiers')->majTierPcc($dossier);

        $dateNow = new DateTime();

        $graphes = $this->getDoctrine()->getRepository('AppBundle:TypeGraphe')->getAll();
        $packs = $this->getDoctrine()->getRepository('AppBundle:IndPack')->getListe($dossier);
        return $this->render('IndicateurBundle:Affichage:pack.html.twig',
            array('packs'=>$packs,
                    'datepicker'=>Boost::getDatePickerPopOver(Boost::getExercices(),Boost::getMois($dossier->getCloture())),
                    'graphes'=>$graphes, 'count_column'=>intval($post->get('count_column')),
                    'height'=>floatval($post->get('height')),
                    'date_anciennete'=>$this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')->getDateAnciennete($dossier,$dateNow->format('Y'))));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indicateurAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $packItem = Boost::deboost($post->get('id_pack_item'),$this);
        if(is_bool($dossier) || is_bool($packItem)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        $packItem = $this->getDoctrine()->getRepository('AppBundle:IndPackItem')->getById($packItem);
        $exercices = json_decode($post->get('exercices'));
        $moiss = json_decode($post->get('moiss'));
        $code_graphe = $post->get('code_graphe');
        $analyse = intval($post->get('analyse'));

        $result = $this->getDoctrine()->getRepository('AppBundle:IndPackItem')
            ->getChartResult($dossier,$packItem,$exercices,$moiss,$code_graphe,$analyse);

        if($code_graphe == 'VAL') return $this->render('IndicateurBundle:Affichage:value.html.twig',array('value'=>$result['series'],'exercices'=>'e','moiss'=>$moiss,'unite'=>$result['categories']));
        return new Response(json_encode($result));
    }
}