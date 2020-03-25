<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 22/06/2017
 * Time: 09:20
 */

namespace EtatBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Etat;
use AppBundle\Entity\EtatError;
use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurSpecGroup;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControlController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $exercices = Boost::getExercices();
        return $this->render('EtatBundle:Control:index.html.twig',array('exercices'=>$exercices));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function errorsAction(Request $request)
    {
        $post = $request->request;
        $client_ = Boost::deboost($post->get('client'),$this);
        $site_ = Boost::deboost($post->get('site'),$this);
        $dossier_ = Boost::deboost($post->get('dossier'),$this);
        $exercices = $post->get('exercices');
        if (!is_array($exercices)) $exercices = Boost::getExercices();

        if(is_bool($client_) || is_bool($site_) || is_bool($dossier_)) return new Response('security');

        $client = $this->getDoctrine()->getRepository('AppBundle:Client')->find($client_);
        $site = null;
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier_);
        if ($dossier == null) $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($site_);
        return new Response($this->errors($client,$site,$dossier,$exercices));
    }

    /**
     * @param Client $client
     * @param $site
     * @param $dossier
     * @param $exercices
     * @return string
     */
    public function errors(Client $client,$site,$dossier,$exercices)
    {
        $etatErrors = $this->getDoctrine()->getRepository('AppBundle:EtatError')
            ->getAllErros($client,$site,$dossier,$exercices);
        $errors = [];

        $sepKey = '_-_';
        $etats = array(0 => 'ETAT FINANCIER', 1 => 'ETAT DE GESTION', 9 => 'INDICATEUR');
        $etatsRegimes = [];
        $clients = [];
        $dossiers = [];
        foreach ($etatErrors as $etatError)
        {
            //$etatError = new EtatError();
            $dossier = $etatError->getDossier();
            $site = $dossier->getSite();
            $client = $site->getClient();
            $etatRegimeFiscal = $etatError->getEtatRegimeFiscal();
            $indicateur = $etatError->getIndicateur();
            $exerciceChoose = $etatError->getExerciceChoose();

            $exercice = $etatError->getExercice();
            $periode = $etatError->getPeriode();

            if($etatRegimeFiscal != null)
            {
                $etatType = $etatRegimeFiscal->getEtat()->getEtat();
                $idEtatRegimeFiscal = $etatRegimeFiscal->getId();
                $etatsRegimes[$etatType.$sepKey.$idEtatRegimeFiscal] = $etatRegimeFiscal;
            }
            else
            {
                $etatType = 9;
                $idEtatRegimeFiscal = $indicateur->getId();
                $etatsRegimes[$etatType.$sepKey.$idEtatRegimeFiscal] = $indicateur;
            }

            $clients[$client->getId()] = $client;
            $dossiers[$dossier->getId()] = $dossier;

            $key = $etatType.$sepKey.$idEtatRegimeFiscal.$sepKey.$client->getId().$sepKey.$dossier->getId();
            $errors[$key][$exerciceChoose][$exercice][$periode] = $periode;
        }
        /**
         * $errors[type_-_etat_-_client_-_dossier][exerciceChoose][exercice][periode] = periode
         */
        $errsJsons = [];
        $p = 0;
        foreach ($errors as $keyError => $exercicesChooses)
        {
            //key
            $keySpliter = explode($sepKey,$keyError);
            $keyEtat = intval($keySpliter[0]);
            $keyEtatRegime = intval($keySpliter[1]);
            $keyClient = intval($keySpliter[2]);
            $keyDossier = intval($keySpliter[3]);

            //object
            $etat = $etats[$keyEtat];
            $etatRegime = $etatsRegimes[$keyEtat.$sepKey.$keyEtatRegime];
            $client = $clients[$keyClient];
            $dossier = $dossiers[$keyDossier];

            //texte
            if($keyEtat == 9) $texteEtatRegime = $etatRegime->getLibelle();
            else $texteEtatRegime = $etatRegime->getEtat()->getLibelle();
            $texteClient = $client->getNom();
            $texteDossier = $dossier->getNom();

            //json
            $err = new \stdClass();
            $err->et = $etat;
            $err->etR = $texteEtatRegime;
            $err->cl = $texteClient;
            $err->dos = $texteDossier;

            //exercices chooses
            $texteExercice = '';
            $indexExerciceChoose = 0;
            $exercicesChoosesCount = count($exercicesChooses);
            foreach ($exercicesChooses as $keyExerciceChoose => $exercices)
            {
                $texteExercice .= $keyExerciceChoose;
                $texteExercice .= '[';

                $indexExercice = 0;
                $exercicesCount = count($exercices);
                foreach ($exercices as $keyExercice => $periodes)
                {
                    $texteExercice .= $keyExercice;
                    $texteExercice .= '(';

                    $indexPeriode = 0;
                    $periodeCount = count($periodes);
                    foreach ($periodes as $periode)
                    {
                        $texteExercice .= $periode;
                        if($indexPeriode != $periodeCount - 1) $texteExercice .= ',';
                        $indexPeriode++;
                    }
                    $texteExercice .= ')';
                    if($indexExercice != $exercicesCount - 1) $texteExercice .= ',';
                    $indexExercice++;
                }

                $texteExercice .= ']';
                if($indexExerciceChoose != $exercicesChoosesCount - 1) $texteExercice .= ',';
                $indexExerciceChoose++;
            }

            $err->ex = $texteExercice;
            $err->p = $p;
            $p++;

            $errsJsons[] = $err;
        }

        return json_encode($errsJsons);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function testAction(Request $request)
    {
        $post = $request->request;
        $client_ = Boost::deboost($post->get('client'),$this);
        $site_ = Boost::deboost($post->get('site'),$this);
        $dossier_ = Boost::deboost($post->get('dossier'),$this);
        $exercices = $post->get('exercices');
        if (!is_array($exercices)) $exercices = Boost::getExercices();
        if(is_bool($client_) || is_bool($site_) || is_bool($dossier_)) return new Response('security');

        $client = $this->getDoctrine()->getRepository('AppBundle:Client')->find($client_);
        $site = null;
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier_);
        if ($dossier == null) $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($site_);

        $dossiers = ($dossier != null) ? [$dossier] : $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->getUserDossier($this->getUser(),$client,$site);
        $etats = $this->getDoctrine()->getRepository('AppBundle:Etat')->findAll();
//        return $this->render('EtatBaseBundle:EtatBase:test.html.twig',array('test'=>$etats));

        $analyse = 0;
        $code_graphe = 'TAB';
        $dateAnciennete = null;
        $anciennetes = [30,60,90];
        $isEtat = true;
        //return $this->render('EtatBaseBundle:EtatBase:test.html.twig',array('test'=>$moiss));
        //$results = [];
        //Test ETATS
        foreach ($dossiers as $dos)
        {
            $moisCloture = $dos->getCloture();
            $moisArrays = Boost::getMois($moisCloture);
            $moiss = [];
            foreach ($moisArrays as $key => $moisArray)
            {
                $moiss[] = ($key < 10) ? '0'.$key : ''.$key;
            }
            $periode = new \stdClass();
            $periode->libelle = 'A';
            $periode->moiss = $moiss;
            $periodes = [$periode];

            foreach ($etats as $etat)
            {
                $etatRegimeFiscal = $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')
                    ->getEtatRegimeFiscal($etat,$dos);

                //$results[] = [$dos,$etat->getLibelle(),$etatRegimeFiscal];
                foreach ($exercices as $exercice)
                {
                    $this->getDoctrine()->getRepository('AppBundle:Indicateur')
                        ->getResultV4($dos,$etatRegimeFiscal,[$exercice],$moiss,$code_graphe,$analyse,$periodes,$dateAnciennete,$anciennetes,$isEtat,$this->getUser());
                    /*$res = array($dos,$etatRegimeFiscal,[$exercice],$moiss,$code_graphe,$analyse,$periodes,$dateAnciennete,$anciennetes,$isEtat,$this->getUser());
                    return $this->render('IndicateurBundle:AffichageV2:test.html.twig',array('test'=>$res,'test2'=>null));*/
                }
            }
        }

        //Test INDICATEURS
        $indicateurSpecGroups = $this->getDoctrine()->getRepository('AppBundle:IndicateurSpecGroup')
            ->getAllIndicateurSpecGroupDossiers($client,$site,$dossier);
        $isEtat = false;
        $indicateursInSpecGroups = [];
        foreach ($indicateurSpecGroups as $indicateurSpecGroup)
        {
            //$indicateurSpecGroup = new IndicateurSpecGroup();
            $idGroup = $indicateurSpecGroup->getIndicateurGroup()->getId();
            if (!array_key_exists($idGroup,$indicateursInSpecGroups))
                $indicateursInSpecGroups[$idGroup] = $this->getDoctrine()->getRepository('AppBundle:Indicateur')
                    ->getIndicateursInGroup($indicateurSpecGroup->getIndicateurGroup(),false);
            $dos = $indicateurSpecGroup->getDossier();

            $moisCloture = $dos->getCloture();
            $moisArrays = Boost::getMois($moisCloture);
            $moiss = [];
            foreach ($moisArrays as $key => $moisArray)
            {
                $moiss[] = ($key < 10) ? '0'.$key : ''.$key;
            }
            $periode = new \stdClass();
            $periode->libelle = 'A';
            $periode->moiss = $moiss;
            $periodes = [$periode];

            foreach ($indicateursInSpecGroups[$idGroup] as $indicateur)
            {
                //$indicateur = new Indicateur();
                $this->getDoctrine()->getRepository('AppBundle:Indicateur')
                    ->getResultV4($dos,$indicateur,$exercices,$moiss,$code_graphe,$indicateur->getAnalyse(),$periodes,$dateAnciennete,$anciennetes,$isEtat,$this->getUser());
            }
        }
        return new Response(1);
    }
}