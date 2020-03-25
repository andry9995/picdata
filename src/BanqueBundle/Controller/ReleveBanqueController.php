<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 11/10/2017
 * Time: 16:43
 */

namespace BanqueBundle\Controller;

use AppBundle\Controller\StringExt;
use AppBundle\Entity\Cle;
use AppBundle\Entity\Cle2;
use AppBundle\Entity\CleBanque;
use AppBundle\Entity\CleCompte;
use AppBundle\Entity\CleDossier;
use AppBundle\Entity\ClePcg;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageFlague;
use AppBundle\Entity\ImageImage;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\MethodeComptable;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveDetail;
use AppBundle\Entity\Sousnature;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\TvaImputationControle;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Acl\Exception\Exception;

class ReleveBanqueController extends Controller
{
    /**
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function banquesAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);

        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $banques = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->getBanques($dossier);
        return $this->render('BanqueBundle:ReleveBanque:banques.html.twig',array('banques'=>$banques));
    }

    /**
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function banqueComptesAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $banque = Boost::deboost($post->get('banque'),$this);
        $tous = ($post->has('tous')) ? (intval($post->get('tous')) == 1) : false;

        if(is_bool($dossier) || is_bool($banque)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')->find($banque);

        $banqueComptes = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->getBanquesComptes($dossier,$banque);
        return $this->render('BanqueBundle:ReleveBanque:banqueComptes.html.twig',array('banqueComptes'=>$banqueComptes, 'tous'=>$tous));
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function analyseAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $banque = Boost::deboost($post->get('banque'),$this);
        $banqueCompte = Boost::deboost($post->get('banque_compte'),$this);
        if(is_bool($dossier) || is_bool($banque) || is_bool($banqueCompte)) return new Response('security');
        $doctrine = $this->getDoctrine();
        $dossier = $doctrine->getRepository('AppBundle:Dossier')->find($dossier);
        $banque = $doctrine->getRepository('AppBundle:Banque')->find($banque);
        $banqueCompte = $doctrine->getRepository('AppBundle:BanqueCompte')->find($banqueCompte);
        $exercice = $post->get('exercice');
        $isPmAction = (intval($post->get('action')) == 1);

        $clesDefaults = $this->getDoctrine()->getRepository('AppBundle:Cle')->getDefaults();
        $responses = $doctrine->getRepository('AppBundle:Releve')->getRelevesNew($dossier,$exercice,$banque,$banqueCompte);

        $results = $this->getDatas($responses);

        $res = new \stdClass();
        $res->d = $results;

        return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$res]);
        //return new JsonResponse($res);
    }

    /**
     * @param $responses
     * @return array
     */
    private function getDatas($responses)
    {
        $results = [];
        $clesPasPieces = $this->getDoctrine()->getRepository('AppBundle:Cle')->getClePasPiece();
        /** @var Releve $response */
        foreach ($responses as $response)
        {
            $status = $this->getDoctrine()->getRepository('AppBundle:ReleveDetail')->getStatus($response,$clesPasPieces);

            $status = $this->getDoctrine()->getRepository('AppBundle:Releve')->getStatus($response, $clesPasPieces);

            $results[] = (object)
            [
                'b' => $response->getBanqueCompte()->getBanque()->getNom(),
                'bc' => $response->getBanqueCompte()->getNumcompte(),
                'i' => $response->getImage()->getNom(),
                'd' => date_format($response->getDateReleve(),'d/m/Y'),
                'l' => $response->getLibelle(),
                'm' => -1 * ($response->getDebit() - $response->getCredit()),
                's' => $status,
                'ss' => $status->s,
                'imi' => Boost::boost($response->getImage()->getId())
                /*'s' => (object)
                [
                    's' => $status,
                    'i' => ($status == 7 || $status == 9) ? $libelleImageTemp : '',
                    'c' => (is_null($cleDossier)) ? '' : str_replace('%',' ',$cleDossier->getCle()->getCle()),
                    'ii' => Boost::boost((is_null($imageTemp) ? 0 : $imageTemp->getId())),
                    'ci' => Boost::boost((is_null($cleDossier)) ? 0 : $cleDossier->getId()),
                    'ids' => implode(';',$imagesIds)
                ]*/
            ];

            //imputation
            /*$releveDetails = $this->getDoctrine()
                ->getRepository('AppBundle:ReleveDetail')
                ->findBy(array('releve' => $response));

            $tiers = null;
            $charge = null;
            $tva = null;
            foreach ($releveDetails as $releveDetail)
            {
                if(!is_null($releveDetail->getCompteTiers2())) $tiers = $releveDetail;
                if(!is_null($releveDetail->getCompteChg2())) $charge = $releveDetail;
                if(!is_null($releveDetail->getCompteTva2())) $tva = $releveDetail;
            }

            $t = '0-0';
            $tm = 0;
            if ($tiers != null)
            {
                $t = '1-'.$tiers->getCompteTiers2()->getId();
                $tm = $tiers->getDebit() - $tiers->getCredit();
            }
            $c = '0-0';
            $cm = 0;
            if ($charge != null)
            {
                $c = '0-'.$charge->getCompteChg2()->getId();
                $cm = $charge->getDebit() - $charge->getCredit();
            }
            $tv = '0-0';
            $tvam = 0;
            if ($tva != null)
            {
                $tv = '0-'.$tva->getCompteTva2()->getId();
                $tvam = $tva->getDebit() - $tva->getCredit();
            }

            $cleDossier = null;
            if ($status == 6)//imputer par cle
            {
                //$response = new Releve();
                $cleDossier = $response->getCleDossier();
                $t = '0-0';
                $tm = 0;
                if (!is_null($cleDossier->getBilanTiers())) $t = '1-'. $cleDossier->getBilanTiers()->getId();
                elseif (!is_null($cleDossier->getBilanPcc())) $t = '0-'. $cleDossier->getBilanPcc()->getId();

                $c = '0-0';
                $cm = 0;
                if (!is_null($cleDossier->getResultat())) $c = '0-'.$cleDossier->getResultat()->getId();

                $tv = '0-0';
                $tvam = 0;
                if (!is_null($cleDossier->getTva())) $tv = '0-'.$cleDossier->getTva()->getId();
            }

            $images = [];
            $imagesIds = [];
            $imageTemp = null;
            $libelleImageTemp = '';
            if ($status == 7 || $status == 9) //imputer piece
            {
                if ($status == 7)
                {
                    $images[] = $response->getImageTemp();
                    $imageTemp = $response->getImageTemp();
                }
                else
                {
                    $imagesTemps = $this->getDoctrine()->getRepository('AppBundle:ImageImage')
                        ->createQueryBuilder('ii')
                        ->where('ii.releve = :releve')
                        ->andWhere('ii.imageType = 3')
                        ->setParameter('releve',$response)
                        ->getQuery()
                        ->getResult();

                    foreach ($imagesTemps as $imagesTemp)
                    {
                        //$imagesTemp = new ImageImage();
                        $images[] = $imagesTemp->getImageAutre();
                        $imagesIds[] = Boost::boost($imagesTemp->getImageAutre()->getId());
                    }
                }

                $i = 0;
                foreach ($images as $image)
                {
                    $imputationControl = $this->getDoctrine()->getRepository('AppBundle:ImputationControle')
                        ->createQueryBuilder('ic')
                        ->where('ic.image = :image')
                        ->setParameter('image',$image)
                        ->getQuery()
                        ->getOneOrNullResult();
                    $tvaImputationControl = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                        ->createQueryBuilder('tic')
                        ->where('tic.image = :image')
                        ->andWhere('tic.tiers IS NOT NULL')
                        ->setParameter('image',$image)
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

                    $libelleImageTemp .=
                        (($i == 0) ? ((is_null($tvaImputationControl) || is_null($tvaImputationControl->getTiers())) ? '' : $tvaImputationControl->getTiers()->getIntitule() . ' - ') : '') .
                        $imputationControl->getNumFacture() .
                        (($i != count($images) - 1) ? ';' : '');

                    $t = '0-0';
                    $tm = 0;
                    $c = '0-0';
                    $cm = 0;
                    $tv = '0-0';
                    $tvam = 0;
                    if (!is_null($tvaImputationControl) && count($images) == 1)
                    {
                        //$tvaImputationControl = new TvaImputationControle();
                        if ($response->getEngagementTresorerie() == 0 && !is_null($tvaImputationControl->getTiers()))
                        {
                            $t = '1-'.$tvaImputationControl->getTiers()->getId();
                        }
                        elseif ($response->getEngagementTresorerie() == 1)
                        {
                            if (!is_null($tvaImputationControl->getPcc())) $c = '0-'.$tvaImputationControl->getPcc()->getId();
                            if (!is_null($tvaImputationControl->getPccTva())) $tv = '0-'.$tvaImputationControl->getPccTva()->getId();
                        }
                    }
                    $i++;
                }
            }
            */
        }

        return $results;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function imagesListeAction(Request $request)
    {
        $imagesIds = explode(';',$request->request->get('images'));
        $images = [];

        foreach ($imagesIds as $imagesId)
        {
            $id = Boost::deboost($imagesId,$this);
            if(is_bool($id)) return new Response('security');
            $image = $this->getDoctrine()->getRepository('AppBundle:Image')->find($id);
            $imputationControl = $this->getDoctrine()->getRepository('AppBundle:ImputationControle')
                ->createQueryBuilder('ic')
                ->where('ic.image = :image')
                ->setParameter('image',$image)
                ->getQuery()
                ->getOneOrNullResult();
            $tvaImputationControl = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                ->createQueryBuilder('tic')
                ->where('tic.image = :image')
                ->andWhere('tic.tiers IS NOT NULL')
                ->setParameter('image',$image)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            $images[] = (object)
            [
                'id' => $image->getId(),
                'n' => $image->getNom(),
                'l' => ((is_null($tvaImputationControl) || is_null($tvaImputationControl->getTiers())) ? '' : $tvaImputationControl->getTiers()->getIntitule() . ' - ').$imputationControl->getNumFacture(),
            ];

        }

        return $this->render('BanqueBundle:ReleveBanque:images.html.twig',array('images'=>$images));
        //return $this->render('IndicateurBundle:Affichage:test.html.twig',array('test'=>$images));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function rapprochementManuelAction(Request $request)
    {
        $post = $request->request;
        $releve = Boost::deboost($post->get('releve'),$this);

        if(is_bool($releve)) return new Response('security');
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        $pcgs = $this->getDoctrine()->getRepository('AppBundle:Pcg')->findBy([], ['compte' => 'ASC']);
        return $this->render('BanqueBundle:ReleveBanque:rapprochement-manuel.html.twig',array('releve'=>$releve,'pcgs'=>$pcgs));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveNewCompteAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $isAuxilliaire = intval($request->request->get('is_auxilliaire')) == 1;
        $type = intval($request->request->get('type'));
        $numCompte = $request->request->get('compte');
        $intitule = $request->request->get('intitule');


        if ($isAuxilliaire)
        {
            $comptePcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')
                ->createQueryBuilder('pcc')
                ->where('pcc.dossier = :dossier')
                ->andWhere('pcc.collectifTiers = :type')
                ->setParameters(array('dossier'=>$dossier, 'type'=>$type))
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            $compte = new Tiers();
            $compte->setDossier($dossier);
            $compte->setCompte($numCompte);
            $compte->setCompteStr($numCompte);
            $compte->setIntitule($intitule);
            $compte->setPcc($comptePcc);
            $compte->setType($type);
            $compte->setStatus(0);
        }
        else
        {
            $compte = new Pcc();
            $compte->setDossier($dossier);
            $compte->setCompte($numCompte);
            $compte->setIntitule($intitule);
            $compte->setStatus(0);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($compte);
        try
        {
            $em->flush();
            return new Response(0);
        }
        catch (UniqueConstraintViolationException $ex)
        {
            return new Response(1);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function pcgsAction(Request $request)
    {
        $banqueType = Boost::deboost($request->request->get('banque_type'),$this);
        if(is_bool($banqueType)) return new Response('security');
        $banqueType = $this->getDoctrine()->getRepository('AppBundle:BanqueType')->find($banqueType);
        $typeCompte = intval($request->request->get('type_compte'));

        $banqueTypePcgs = $this->getDoctrine()->getRepository('AppBundle:BanqueTypePcg')
            ->createQueryBuilder('btp')
            ->leftJoin('btp.pcg','pcg')
            ->select('btp')
            ->addSelect('pcg')
            ->where('btp.banqueType = :banqueType')
            ->andWhere('btp.type = :type')
            ->setParameters(array('banqueType'=>$banqueType, 'type'=>$typeCompte))
            ->orderBy('pcg.compte')
            ->getQuery()
            ->getResult();

        $pcgs = [];
        foreach ($banqueTypePcgs as $banqueTypePcg) $pcgs[] = $banqueTypePcg->getPcg();
        //type_compte

        /*$pcgs = $this->getDoctrine()->getRepository('AppBundle:Pcg')
            ->createQueryBuilder('p')
            ->where('LENGTH(p.compte) > 2')
            ->orderBy('p.compte','ASC')
            ->getQuery()
            ->getResult();*/

        $pcgsSelecteds = json_decode($request->request->get('pcgs_selecteds'));

        $pcgsChilds = [];
        $pcgsParents = [];
        $pcgsObjects = [];
        foreach ($pcgs as $pcg)
        {
            //$pcg = new Pcg();
            $compte = $pcg->getCompte();
            $pcgsChilds[$compte] = [];
            $pcgsObjects[$compte] = (object)
            [
                'compte' => $compte,
                'intitule' => $pcg->getIntitule(),
                'id' => $pcg->getId(),
                't' => 0
            ];
            $parent = null;

            for ($i = strlen($compte) - 1; $i >= 0; $i--)
            {
                $key = substr($compte,0,$i);
                if (array_key_exists($key,$pcgsChilds))
                {
                    $pcgsChilds[$key][] = $compte;
                    $parent = $key;
                    break;
                }
            }

            if ($parent == null) $pcgsParents[] = $compte;
        }

        $results = [];
        foreach ($pcgsParents as $pcgsParent)
        {
            $results[] = functions::getTree($pcgsParent,$pcgsChilds,$pcgsObjects,$pcgsSelecteds);
        }

        return new JsonResponse($results);
        /*'data' : [
        'Empty Folder',
        {
            'text': 'Resources',
            'state': {
            'opened': true
            },
            'children': [
                {
                    'text': 'css',
                    'children': [
                        {
                            'text': 'animate.css', 'icon': 'none'
                        },
                        {
                            'text': 'bootstrap.css', 'icon': 'none'
                        },
                        {
                            'text': 'main.css', 'icon': 'none'
                        },
                        {
                            'text': 'style.css', 'icon': 'none'
                        }
                    ],
                    'state': {
                    'opened': true
                    }
                }
            ]
        }
    ]*/
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveImputationCleAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $resultat = Boost::deboost($post->get('resultat'),$this);
        $bilan = Boost::deboost($post->get('bilan'),$this);
        $tva = Boost::deboost($post->get('tva'),$this);
        $cle = Boost::deboost($post->get('cle'),$this);
        $releve = Boost::deboost($post->get('releve'),$this);
        $resultatType = intval($post->get('resultat_type'));
        $bilanType = intval($post->get('bilan_type'));
        $tvaType = intval($post->get('tva_type'));
        $typeCompta = intval($post->get('type_compta'));
        if(is_bool($dossier) || is_bool($resultat) || is_bool($bilan) || is_bool($tva) || is_bool($cle) || is_bool($releve)) return new Response('security');

        $doctrine = $this->getDoctrine();
        $dossier = $doctrine->getRepository('AppBundle:Dossier')->find($dossier);
        $resultat = $doctrine->getRepository(($resultatType == 0) ? 'AppBundle:Pcc' : 'AppBundle:Tiers')->find($resultat);
        $bilan = $doctrine->getRepository(($bilanType == 0) ? 'AppBundle:Pcc' : 'AppBundle:Tiers')->find($bilan);
        $tva = $doctrine->getRepository(($tvaType == 0) ? 'AppBundle:Pcc' : 'AppBundle:Tiers')->find($tva);
        $cle = $doctrine->getRepository('AppBundle:Cle')->find($cle);
        $releve = $doctrine->getRepository('AppBundle:Releve')->find($releve);

        $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')->setCleDossier(
            $cle,$dossier,$bilan,$tva,$resultat,$bilanType,20,$typeCompta
        );

        $em = $this->getDoctrine()->getManager();
        $releve->setCleDossier($cleDossier);
        $releve->setPasCle(0);
        $releve->setPasImage(0);
        $em->flush();

        return new Response(0);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function pccsAction(Request $request)
    {
        $pccsSelecteds = json_decode($request->request->get('pccs_selecteds'));
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $pcgsTemps = json_decode($request->request->get('pcgs'));
        $pcgs = [];
        foreach ($pcgsTemps as $pcgsTemp)
        {
            $idSpliter = explode('#',$pcgsTemp);
            $pcgs[] = $this->getDoctrine()->getRepository('AppBundle:Pcg')->find($idSpliter[1]);
        }

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $pccs = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPCCByPCG($pcgs,$dossier,[],count($pcgs) > 0);

        $pcgsChilds = [];
        $pcgsParents = [];
        $pcgsObjects = [];
        foreach ($pccs as $pcg)
        {
            //$pcg = new Pcc();
            $compte = '0_' . $pcg->getCompte();
            $pcgsChilds[$compte] = [];
            $pcgsObjects[$compte] = (object)
            [
                'compte' => $compte,
                'intitule' => $pcg->getIntitule(),
                'id' => $pcg->getId(),
                't' => 0
            ];
            $parent = null;

            for ($i = strlen($compte) - 1; $i >= 0; $i--)
            {
                $key = substr($compte,0,$i);
                if (array_key_exists($key,$pcgsChilds))
                {
                    $pcgsChilds[$key][] = $compte;
                    $parent = $key;
                    break;
                }
            }

            if ($pcg->getCollectifTiers() != -1)
            {
                /** @var Tiers[] $tiers */
                $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')
                    ->createQueryBuilder('t')
                    ->where('t.pcc = :pcc')
                    ->setParameter('pcc',$pcg)
                    ->orderBy('t.intitule')
                    ->getQuery()
                    //->setMaxResults(19)
                    ->getResult();

                $existe = false;
                foreach ($tiers as $tier)
                {
                    //$tier = new Tiers();
                    $compteTiers = '1_' . $tier->getCompteStr();
                    $pcgsChilds[$compteTiers] = [];
                    $pcgsChilds[$compte][] = $compteTiers;

                    $pcgsObjects[$compteTiers] = (object)
                    [
                        'compte' => $compteTiers,
                        'intitule' => $tier->getIntitule(),
                        'id' => $tier->getId(),
                        't' => 1
                    ];
                    $existe = true;
                }

                /*if ($existe)
                {
                    $compteTiers = 'A_Creer';
                    $pcgsChilds[$compteTiers] = [];
                    $pcgsChilds[$compte][] = $compteTiers;

                    $pcgsObjects[$compteTiers] = (object)
                    [
                        'compte' => $compteTiers,
                        'intitule' => 'Compte à creer',
                        'id' => -1,
                        't' => 1
                    ];
                }*/
            }

            if ($parent == null) $pcgsParents[] = $compte;
        }

        //return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$pcgsChilds]);

        $results = [];
        foreach ($pcgsParents as $pcgsParent)
        {
            $results[] = functions::getTree($pcgsParent,$pcgsChilds,$pcgsObjects,$pccsSelecteds,true);
        }

        //return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$results]);

        return new JsonResponse($results);
        //return $this->render('IndicateurBundle:Affichage:test.html.twig',array('test'=>$results));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function releveDetailsAction(Request $request)
    {
        return new Response('details');
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public static function cmp($a, $b)
    {
        return strcmp($a->k, $b->k);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function imagePasAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        $type = intval($request->get('type'));

        $em = $this->getDoctrine()->getManager();
        $imageIds = json_decode($request->request->get('images'));
        $nonLettrables = ($releve->getNonLettrable() != '') ?
            json_decode($releve->getNonLettrable()) : [];

        if (count($imageIds) == 0)
            $releve->setPasImage($type);
        foreach ($imageIds as $imageId)
        {
            $imageDecripter = Boost::deboost($imageId,$this);

            if (!in_array($imageDecripter,$nonLettrables))
                $nonLettrables[] = intval($imageDecripter);
        }

        $releve->setNonLettrable(json_encode($nonLettrables));
        $em->flush();

        return new Response(0);
    }

    /**
     * @param Request $request
     * @param $json
     * @param $did
     * @param $nid
     * @return Response
     */
    public function gridComboAction(Request $request, $json, $did, $nid){
        if($request->isXmlHttpRequest()){

            $dossierId = Boost::deboost($did, $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $select = "";

            switch ($json) {

                //0: tiers
                case 0:

                    $select = "<select>";
                    $addNouveau = false;

                    $tiersList = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->findBy(array('dossier' => $dossier), array('intitule' => 'ASC'));

                    foreach ($tiersList as $tiers) {
                        $select .= "<option value='" . $tiers->getId() . "'>" . $tiers->getIntitule() . "</option>";

                        if ($addNouveau == false) {
                            $select .= "<option value='-1'>[Nouveau Tiers]</option>";
                            $addNouveau = true;
                        }
                    }
                    $select .="</select>";
                    break;

                //1: pcc
                case 1:
                    $select = "<select>";
                    $pccList = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->findBy(array('dossier' => $dossier), array('compte' => 'ASC'));

                    foreach ($pccList as $pcc) {
                        $select .= "<option value='" . $pcc->getId() . "'>" . $pcc->getCompte() . "</option>";
                    }

                    $select .="</select>";
                    break;

                //2: nature
                case 2:
                    $select = "<select>";

                    $select .= "<option></option>";

                    $natureList = $this->getDoctrine()
                        ->getRepository('AppBundle:Nature')
                        ->findBy(array('actif' => 1), array('libelle' => 'ASC'));

                    foreach ($natureList as $nature) {
                        $select .= "<option value='" . $nature->getId() . "'>" . $nature->getLibelle() . "</option>";
                    }

                    $select .="</select>";
                    break;

                //3: sousnature
                case 3:

                    $nature = $this->getDoctrine()
                        ->getRepository('AppBundle:Nature')
                        ->find($nid);
                    if (!is_null($nature)) {
                        $sousNatureList = $this->getDoctrine()
                            ->getRepository('AppBundle:Sousnature')
                            ->findBy(array('nature' => $nature));

                    }
                    else{
                        $sousNatureList = $this->getDoctrine()
                            ->getRepository('AppBundle:Sousnature')
                            ->findAll();
                    }

                    if(is_null($nature)){
                        $select .="<select>";
                    }

                    $select .="<option></option>";

                    /** @var Sousnature $sousnature */
                    foreach ($sousNatureList as $sousnature){
                        $select .= "<option value='".$sousnature->getId()."'>".$sousnature->getLibelle()."</option>";
                    }

                    if(is_null($nature)) {
                        $select .= "</select>";
                    }

                    break;

                case 4:

                    $sousnature = $this->getDoctrine()
                        ->getRepository('AppBundle:Sousnature')
                        ->find($nid);

                    $nature = $sousnature->getNature();

                    $sousNatureList = $this->getDoctrine()
                        ->getRepository('AppBundle:Sousnature')
                        ->findBy(array('nature' => $nature));

                    /** @var Sousnature $sousnature */
                    foreach ($sousNatureList as $sousnature){
                        $select .= "<option value='".$sousnature->getId()."'>".$sousnature->getLibelle()."</option>";
                    }


                    break;


            }



            return new Response($select);
        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function releveEditAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $post = $request->request;
            $releve = Boost::deboost($post->get('id'), $this);
            if (is_bool($releve)) return new Response('security');
            $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
            /**
             * new
             * */
            $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($post->get('t'));
            $charge = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($post->get('c'));
            $tva = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($post->get('tva'));
            $montantTiers = round(floatval($post->get('tm')),2);
            $montantCharge = round(floatval($post->get('cm')),2);
            $montantTva = round(floatval($post->get('tvam')),2);

            /**
             * old
             * */
            $oldTiers = null;
            $oldCharge = null;
            $oldTva = null;
            $releveDetails = $this->getDoctrine()->getRepository('AppBundle:ReleveDetail')->findBy(array('releve' => $releve));
            foreach ($releveDetails as $releveDetail)
            {
                if(!is_null($releveDetail->getCompteTiers2())) $oldTiers = $releveDetail;
                if(!is_null($releveDetail->getCompteChg2())) $oldCharge = $releveDetail;
                if(!is_null($releveDetail->getCompteTva2())) $oldTva = $releveDetail;
            }

            /**
             * MAJ
             * */
            $em = $this->getDoctrine()->getManager();
            //bilan
            if (!is_null($oldTiers))
            {
                if (!is_null($tiers))
                {
                    $oldTiers->setCompteTiers2($tiers);
                    $oldTiers->setDebit(($montantTiers > 0) ? $montantTiers : 0);
                    $oldTiers->setCredit(($montantTiers < 0) ? abs($montantTiers) : 0);
                }
                else $em->remove($oldTiers);
            }
            elseif (!is_null($tiers))
            {
                $releveDetail = new ReleveDetail();
                $releveDetail->setCompteTiers2($tiers);
                $releveDetail->setDebit(($montantTiers > 0) ? $montantTiers : 0);
                $releveDetail->setCredit(($montantTiers < 0) ? abs($montantTiers) : 0);
                $releveDetail->setReleve($releve);
                $em->persist($releveDetail);
            }
            //charge
            if (!is_null($oldCharge))
            {
                if (!is_null($charge))
                {
                    $oldCharge->setCompteChg2($charge);
                    $oldCharge->setDebit(($montantCharge > 0) ? $montantCharge : 0);
                    $oldCharge->setCredit(($montantCharge < 0) ? abs($montantCharge) : 0);
                }
                else $em->remove($oldCharge);
            }
            elseif (!is_null($charge))
            {
                $releveDetail = new ReleveDetail();
                $releveDetail->setCompteChg2($charge);
                $releveDetail->setDebit(($montantCharge > 0) ? $montantCharge : 0);
                $releveDetail->setCredit(($montantCharge < 0) ? abs($montantCharge) : 0);
                $releveDetail->setReleve($releve);
                $em->persist($releveDetail);
            }
            //tva
            if (!is_null($oldTva))
            {
                if (!is_null($tva))
                {
                    $oldTva->setCompteTva2($tva);
                    $oldTva->setDebit(($montantTva > 0) ? $montantTva : 0);
                    $oldTva->setCredit(($montantTva < 0) ? abs($montantTva) : 0);
                }
                else $em->remove($oldTva);
            }
            elseif (!is_null($tva))
            {
                $releveDetail = new ReleveDetail();
                $releveDetail->setCompteTva2($tva);
                $releveDetail->setDebit(($montantTva > 0) ? $montantTva : 0);
                $releveDetail->setCredit(($montantTva < 0) ? abs($montantTva) : 0);
                $releveDetail->setReleve($releve);
                $em->persist($releveDetail);
            }

            $em->flush();
            return new Response(1);
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function releveEditActionn(Request $request){
        if($request->isXmlHttpRequest()){

            $idReleve = Boost::deboost($request->request->get('id'), $this);

            $releve = $this->getDoctrine()
                ->getRepository('AppBundle:Releve')
                ->find($idReleve);

            if(!is_null($releve)){

                $compteTiersTempId = $request->request->get('t');

                $compteTiersTemp = null;
                if(!is_null($compteTiersTempId)) {
                    $compteTiersTemp = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($compteTiersTempId);
                }

                $compteTvaTemp = null;
                $compteTvaTempId = $request->request->get('tv');
                if(!is_null($compteTvaTempId)) {
                    $compteTvaTemp = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->find($compteTvaTempId);
                }

                $compteChargeTemp = null;
                $compteChargeTempId = $request->request->get('c');
                if(!is_null($compteChargeTempId)) {
                    $compteChargeTemp = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->find($compteChargeTempId);
                }



                $releve->setCompteChgTemp($compteChargeTemp);
                $releve->setCompteTiersTemp($compteTiersTemp);
                $releve->setCompteTvaTemp($compteTvaTemp);

                $em = $this->getDoctrine()->getManager();

                $em->persist($releve);
                $em->flush();

                $c = "";
                if(!is_null($compteChargeTemp)){
                    $c= $compteChargeTemp->getCompte();
                }
                $t = "";
                if(!is_null($compteTiersTemp)){
                    $t = $compteTiersTemp->getIntitule();
                }
                $tv ="";
                if(!is_null($compteTvaTemp)){
                    $tv = $compteTvaTemp->getCompte();
                }

                $data = array(
                    'erreur' => false,
                    'comptechg' => $c,
                    'comptetva' => $tv,
                    'comptet' => $t
                );

            }
            else{
                $data = array(
                    'erreur' => "Releve introuvable",
                    'comptechg' => "",
                    'comptetva' => "",
                    'comptet' => ""
                );


            }

            return new JsonResponse($data);


        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function tiersCompteStrAction(Request $request, $tid){
        if($request->isXmlHttpRequest()){
            $tiers = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->find($tid);

            $compteStr = "";

            if(!is_null($tiers)){
                $compteStr = $tiers->getCompteStr();
            }

            return new Response($compteStr);
        }
        else{
            throw  new AccessDeniedHttpException("Accès refusé");
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function tiersEditAction(Request $request){
        if($request->isXmlHttpRequest()){
            $post = $request->request;

            $intitule = $post->get('intitule');

            $did = Boost::deboost($post->get('did'), $this);
            $rid = Boost::deboost($post->get('rid'), $this);

            $releve = $this->getDoctrine()
                ->getRepository('AppBundle:Releve')
                ->find($rid);

            $typeTiers = 3;

            if(!is_null($releve)){
                //2:fournisseur
                if(!is_null($releve->getDebit()) && $releve->getDebit() != 0){
                    $typeTiers = 0;
                }
                //1:client
                else if(!is_null($releve->getCredit()) && $releve->getCredit() != 0){
                    $typeTiers = 1;
                }
            }

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($did);

            if(!is_null($dossier)){
                $tiers = $this->getDoctrine()
                    ->getRepository('AppBundle:Tiers')
                    ->findBy(array('dossier' => $dossier));
                $trouve = false;

                foreach ($tiers as $tier){
                    if($tier->getIntitule() == $intitule){
                        $trouve = true;
                    }
                }

                if(!$trouve){
                    $newTiers = new Tiers();
                    $newTiers->setDossier($dossier);
                    $newTiers->setIntitule($intitule);
                    $newTiers->setCompteStr("AVALIDER");
                    $newTiers->setType($typeTiers);

                    $em = $this->getDoctrine()->getManager();

                    $em->persist($newTiers);
                    $em->flush();

                    $data = array(
                        'output' => 'insere',
                        'intitule' => $newTiers->getIntitule(),
                        'id' => $newTiers->getId()
                    );
                }
                else{
                    $data = array(
                        'output' => 'Tiers existant',
                    );
                }
            }
            else{
                $data = array(
                    'output' => 'Dossier introuvable',
                );
            }

            return new JsonResponse($data);

        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function testAction(){

        $banqueCompte = $this->getDoctrine()
            ->getRepository('AppBundle:BanqueCompte')
            ->find(116);

        $releves = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->getRelevesNew($banqueCompte->getDossier(),2017,null,$banqueCompte);



        return new JsonResponse(1);
    }
}