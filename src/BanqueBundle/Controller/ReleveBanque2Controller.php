<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 30/07/2018
 * Time: 15:21
 */

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Controller\StringExt;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\BanqueSousCategorieAutre;
use AppBundle\Entity\BanqueType;
use AppBundle\Entity\BanqueTypePcg;
use AppBundle\Entity\Categorie;
use AppBundle\Entity\CfonbBanque;
use AppBundle\Entity\Cle;
use AppBundle\Entity\Cle2;
use AppBundle\Entity\CleBanque;
use AppBundle\Entity\CleCompte;
use AppBundle\Entity\CleDossier;
use AppBundle\Entity\CleDossierExt;
use AppBundle\Entity\CleDossiers;
use AppBundle\Entity\CleSlave;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageFlague;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveDetail;
use AppBundle\Entity\ReleveExt;
use AppBundle\Entity\ReleveImputation;
use AppBundle\Entity\ReleveJson;
use AppBundle\Entity\ReleveManquant;
use AppBundle\Entity\Separation;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\TvaImputation;
use AppBundle\Entity\TvaImputationControle;
use AppBundle\Entity\Utilisateur;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Acl\Exception\Exception;

class ReleveBanque2Controller extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        $utilisateurScriptura = (intval($utilisateur->getClient()->getId()) == 626);
        $bts = json_encode($this->getDoctrine()->getRepository('AppBundle:BanqueType')->getBanqueTypeParametres());
        return $this->render('BanqueBundle:ReleveBanque2:index.html.twig', [
            'action' =>0,
            'bts' => $bts,
            'us' => $utilisateurScriptura
        ]);
    }

    /**
     * @param Request $request
     * @return Response
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
        $obs = (intval($post->get('obs')) == 1);

        $limitQuery = intval($post->get('limit_query'));
        $offset = intval($post->get('offset'));

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        $utlisateurScriptura = (intval($utilisateur->getClient()->getId()) == 626);

        $responses = $doctrine->getRepository('AppBundle:Releve')->getRelevesNew($dossier,$exercice,$banque,$banqueCompte,false,$limitQuery,$offset);
        $results = $this->getDatas($responses,$dossier,$obs,$utlisateurScriptura,false);

        //return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$results]);

        $res = new \stdClass();
        $res->d = $results;
        //var_dump($res);die;

        return new JsonResponse($res);
        return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$res]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function imputationItemsAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        $imageFlague = Boost::deboost($request->request->get('id'),$this);

        if(is_bool($releve) || is_bool($imageFlague)) return new Response('security');

        //$releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        $imageFlague = $this->getDoctrine()->getRepository('AppBundle:ImageFlague')->find($imageFlague);

        $is = $this->getDoctrine()->getRepository('AppBundle:ImageFlague')
            ->getSoeurs($imageFlague,null,null,null,false);

        $imagesTvaImputations = $is->tic;
        $imagesReleves = $is->rel;

        $results = [];
        foreach ($imagesTvaImputations as $key => $imti)
        {
            /** @var TvaImputationControle[] $tvaImputationControles */
            $tvaImputationControles = $imti;

            $montant = 0;
            /** @var TvaImputationControle $tvaImputationControle */
            $tvaImputationControle = $tvaImputationControles[0];
            /** @var ImputationControle $imputationControle */
            $imputationControle = $this->getDoctrine()->getRepository('AppBundle:ImputationControle')
                ->createQueryBuilder('ic')
                ->where('ic.image = :image')
                ->setParameter('image',$tvaImputationControle->getImage())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            /** @var Categorie $categorie */
            $categorie = null;
            if ($imputationControle->getSoussouscategorie()) $categorie = $imputationControle->getSoussouscategorie()->getSouscategorie()->getCategorie();
            else if ($imputationControle->getSouscategorie()) $categorie = $imputationControle->getSouscategorie()->getCategorie();

            $libelle = $imputationControle->getRs();
            if (trim($libelle) == '')
                $libelle = ($tvaImputationControle->getTiers()) ? $tvaImputationControle->getTiers()->getIntitule() : $tvaImputationControle->getLibelle();

            if ($imputationControle->getNumFacture() != '')
                $libelle .= '-' . $imputationControle->getNumFacture();

            foreach ($tvaImputationControles as $item) $montant += $item->getMontantTtc();

            $results[] = (object)
            [
                'date' => ($imputationControle && $imputationControle->getDateFacture()) ? $imputationControle->getDateFacture() : new \DateTime(),
                'image' => $tvaImputationControle->getImage(),
                'libelle' => $libelle,
                'montant' => $montant,
                'categorie' => $categorie ? $categorie->getLibelleNew() : ''
            ];
        }

        foreach ($imagesReleves as $imagesReleve)
        {
            /** @var Releve[] $releves */
            $releves = $imagesReleve;

            foreach ($releves as $rel)
                $results[] = (object)
                [
                    'date' => $rel->getDateReleve(),
                    'image' => $rel->getImage(),
                    'libelle' => $rel->getLibelle(),
                    'montant' => $rel->getCredit() - $rel->getDebit(),
                    'categorie' => 'Relevés bancaires'
                ];
        }

        return $this->render('BanqueBundle:ReleveBanque2:imputation-items.html.twig', ['results' => $results]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function imputationSousCategorieAutresAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');

        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);

        $banqueSousCategorieAutres = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
            ->getChildImageFlagues($releve->getImageFlague());

        $res = [];
        foreach ($banqueSousCategorieAutres as $banqueSousCategorieAutre)
        {
            $res[] = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
                ->getStatus($banqueSousCategorieAutre);
        }

        return new JsonResponse($res);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function imagesViewAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        $cleDossierExt = Boost::deboost($request->request->get('cle_dossier_ext'), $this);
        if(is_bool($releve) || is_bool($cleDossierExt)) return new Response('security');

        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);
        $cleDossierExt = $this->getDoctrine()->getRepository('AppBundle:CleDossierExt')
            ->find($cleDossierExt);
        $methode = intval($request->request->get('methode'));

        /** @var ReleveExt $releveExt */
        $releveExt = null;

        if ($cleDossierExt)
            $releveExt = $this->getDoctrine()->getRepository('AppBundle:ReleveExt')
                ->findOneBy(['cleDossierExt' => $cleDossierExt, 'releve' => $releve]);

        /** @var BanqueSousCategorieAutre $banqueSousCategorieAutre */
        $banqueSousCategorieAutre = null;

        $negatif = true;
        if (!$releve)
        {
            $sca = Boost::deboost($request->request->get('banque_sous_categorie_autre'),$this);
            if(is_bool($sca)) return new Response('security');
            $banqueSousCategorieAutre = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
                ->find($sca);

            $separation = $this->getDoctrine()->getRepository('AppBundle:Separation')
                ->getSeparationByImage($banqueSousCategorieAutre->getImage());

            if ($separation)
            {
                if ($separation->getSoussouscategorie() && $separation->getSoussouscategorie()->getId() == 2791)
                    $negatif = false;
                elseif ($separation->getSouscategorie() && $separation->getSouscategorie()->getId() == 7)
                    $negatif = false;
            }
        }

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        $utilisateurScriptura = (intval($utilisateur->getClient()->getId()) == 626);

        return $this->render('BanqueBundle:ReleveBanque:rapprochement-picdoc.html.twig',
            [
                'releve' => $releve,
                'releveExt' => $releveExt,
                'methode' => $methode,
                'banqueSousCategorieAutre' => $banqueSousCategorieAutre,
                'negatif' => $negatif,
                'us' => $utilisateurScriptura
            ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function trUpdatedAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');

        /** @var Releve $releve */
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        $releveOne = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->getReleveNewOne($releve);

        $obs = (intval($request->request->get('obs')) == 1);
        $results = $this->getDatas($releveOne,$releve->getBanqueCompte()->getDossier(),$obs);
        return new JsonResponse($results);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function imagesAAffecterAction(Request $request)
    {
        $post = $request->request;
        $releve = Boost::deboost($post->get('releve'),$this);
        $cleDossierExt = Boost::deboost($post->get('cle_dossier_ext'), $this);
        if(is_bool($releve) || is_bool($cleDossierExt)) return new Response('security');

        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);
        $cleDossierExt = $this->getDoctrine()->getRepository('AppBundle:CleDossierExt')
            ->find($cleDossierExt);

        /** @var ReleveExt $releveExt */
        $releveExt = null;

        if ($cleDossierExt)
            $releveExt = $this->getDoctrine()->getRepository('AppBundle:ReleveExt')
                ->findOneBy(['cleDossierExt' => $cleDossierExt , 'releve' => $releve]);

        /*return $this->render('IndicateurBundle:Affichage:test.html.twig',[
           'test' => $releveExt
        ]);*/

        $tvaImputationControlsImages = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
            ->getImageAAffecter($releve,false, $releveExt);

        $results = [];
        $p = 0;
        foreach ($tvaImputationControlsImages as $tvaImputationControlsI)
        {
            /** @var TvaImputationControle $tvaImputationControlImage */
            $tvaImputationControlImage = $tvaImputationControlsI['tvaic'];
            $similarity = 25;
            $mTTcTotal = $tvaImputationControlsI['mttc'];

            /** @var TvaImputationControle[] $tvaImputationControls */
            $tvaImputationControls = $this->getDoctrine()
                ->getRepository('AppBundle:TvaImputationControle')
                ->createQueryBuilder('ti')
                ->where('ti.image = :image')
                ->setParameter('image',$tvaImputationControlImage->getImage())
                ->getQuery()
                ->getResult();

            $image = $tvaImputationControlImage->getImage();
            $imageNom = $image->getNom();

            foreach ($tvaImputationControls as $tvaImputationControl)
            {
                $mTTc = $tvaImputationControl->getMontantTtc();
                $coefficientTaux = 1;
                if ($tvaImputationControl->getTvaTaux()->getTaux())
                    $coefficientTaux += $tvaImputationControl->getTvaTaux()->getTaux() / 100;
                $mHt = $mTTc / $coefficientTaux;

                $imputationControl = $this->getDoctrine()->getRepository('AppBundle:ImputationControle')
                    ->getImputationControle($tvaImputationControl);

                if ($mTTcTotal < 0)
                {
                    $mTTc *= -1;
                    $mHt *= -1;
                }

                $g = $imageNom.'-'.$image->getNumPage();

                $key = ((is_null($imputationControl) || is_null($imputationControl->getDateFacture())) ? '21000101' : $imputationControl->getDateFacture()->format('Ymd')). '_' .$p;

                $bilan = null;
                $resultat = null;
                $tva = null;

                $keyI = $image->getId().'_';
                if ($tvaImputationControl->getTiers())
                {
                    $bilan = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getTiers()->getId()),
                        'l' => $tvaImputationControl->getTiers()->getCompteStr(),
                        't' => 1
                    ];
                    $g .= '1_'.$tvaImputationControl->getTiers()->getId();
                }
                elseif ($tvaImputationControl->getPccBilan())
                {
                    $bilan = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getPccBilan()->getId()),
                        'l' => $tvaImputationControl->getPccBilan()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$tvaImputationControl->getPccBilan()->getId();
                }

                if ($tvaImputationControl->getPcc())
                {
                    $resultat = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getPcc()->getId()),
                        'l' => $tvaImputationControl->getPcc()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$tvaImputationControl->getPcc()->getId();
                }
                if ($tvaImputationControl->getPccTva())
                {
                    $tva = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getPccTva()->getId()),
                        'l' => $tvaImputationControl->getPccTva()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$tvaImputationControl->getPccTva()->getId();
                }

                $libelle = $imputationControl ? $imputationControl->getRs() : '';
                if (trim($libelle) == '')
                    $libelle = ((is_null($tvaImputationControl->getTiers())) ? '' : $tvaImputationControl->getTiers()->getIntitule());

                if (trim($imputationControl->getNumFacture()) != '')
                    $libelle .= '-' . $imputationControl->getNumFacture();

                $mTva = $mTTc - $mHt;
                if (array_key_exists($keyI,$results))
                {
                    $results[$keyI]['ht'] += $mHt;
                    $results[$keyI]['mtva'] += $mTva;
                    $results[$keyI]['ttc'] += $mTTc;
                }
                else
                $results[$keyI] =
                [
                    'k' => $key,
                    'g' => $g,
                    'id' => $p,
                    'p' => $p,
                    'i' => $image->getNom().'-'.$image->getNumPage(),
                    'ii' => Boost::boost($image->getId()),
                    'd' => (is_null($imputationControl) || is_null($imputationControl->getDateFacture())) ? '' : $imputationControl->getDateFacture()->format('d/m/Y'),
                    't' =>  $libelle,
                    'e' => $tvaImputationControl->getImage()->getExercice(),
                    'b' => $bilan,
                    'r' => $resultat,
                    'tva' => $tva,
                    'ht' => $mHt,
                    'mtva' => $mTva,
                    'ttc' => $mTTc,
                    'tr' => (is_null($imputationControl) || is_null($imputationControl->getModeReglement())) ? '' : $imputationControl->getModeReglement()->getLibelle(),
                    'nr' => (is_null($imputationControl) || is_null($imputationControl->getNumPaiement())) ? '' : $imputationControl->getNumPaiement(),
                    'dr' => (is_null($imputationControl) || is_null($imputationControl->getDateReglement())) ? '' : $imputationControl->getDateReglement()->format('d/m/Y'),
                    'f' => (is_null($image->getImageFlague())) ? 0 : 1,
                    'sm' => $similarity,
                    'type' => 0
                ];
                $p++;
            }
        }

        $res = array_values($results);
        $resBanqueScAutres = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
            ->imageAAffecter($releve,$p,$releveExt);
        $res = array_merge($res,$resBanqueScAutres);

        return new JsonResponse($res);
    }

    /**
     * @param $rels
     * @param Dossier|null $dossier
     * @param bool $obs
     * @param bool $utilisateurScriptura
     * @return array
     */
    public function getDatas($rels,Dossier $dossier = null,$obs = false,$utilisateurScriptura = true, $updateJson = false)
    {
        /** @var Releve[] $responses */
        $responses = $rels->rs;
        $imagesAAffecters = $rels->if;
        $comptes = $rels->cs;
        $cles = $rels->cles;
        $clesTrouves = $rels->clesTrouves;
        $results = [];
        /** @var Cle[] $clesPasPieces */
        $clesPasPieces = [];

        $em = $this->getDoctrine()->getManager();

        /** @var CfonbBanque[] $cfonbCodeActives */
        $cfonbCodeActives = [];

        if (count($responses) > 0)
            $cfonbCodeActives = $this->getDoctrine()->getRepository('AppBundle:CfonbBanque')
                ->cfonbActiveInBanque($responses[0]->getBanqueCompte()->getBanque());

        if ($dossier)
            $clesPasPieces = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
                ->getClePasPiece($dossier);

        /** @var Pcc $attenteDebit */
        $attenteDebit = null;
        /** @var Pcc $attenteDebit */
        $attenteCredit = null;

        if (count($responses) > 0)
        {
            $attenteDebit = $this->getDoctrine()->getRepository('AppBundle:Pcc')
                ->getPccAttenteBanque($responses[0]->getBanqueCompte()->getDossier(), true);
            $attenteCredit = $this->getDoctrine()->getRepository('AppBundle:Pcc')
                ->getPccAttenteBanque($responses[0]->getBanqueCompte()->getDossier(),false);
        }

        foreach ($responses as $response)
        {
            $key = '_'.$response->getId();
            $withImageAAffecter = intval($imagesAAffecters[$key]);
            $compts = $comptes[$key];
            $cle = $cles[$key];
            $clesTrouve = $clesTrouves[$key];

            /** @var Releve $rl */
            $rl = $response;

            $releveJson = $this->getDoctrine()->getRepository('AppBundle:ReleveJson')
                ->getByReleve($rl);

            if (!$utilisateurScriptura && $releveJson)
            {
                $results[] = json_decode($releveJson->getJson());
                continue;
            }

            $status = $this->getDoctrine()->getRepository('AppBundle:Releve')->getStatus($response, $clesPasPieces, $withImageAAffecter, $compts, $cle,$clesTrouve,$cfonbCodeActives);

            $bilan = null;
            $tva = null;
            $charge = null;

            $bilans = [];
            $tvas = [];
            $resultats = [];

            $convention = $response->getEngagementTresorerie();
            $s = intval($status->s);

            $statLettre = null;
            $imageSoeurs = [];
            if ($response->getEcritureChange() == 1)
            {
                $releveImputations = $this->getDoctrine()->getRepository('AppBundle:ReleveImputation')
                    ->getReleveImputation($response);

                foreach ($releveImputations as $releveImputation)
                {
                    if ($releveImputation->getTiers())
                        $bilan = (object)
                        [
                            'id' => Boost::boost($releveImputation->getTiers()->getId()),
                            'l' => $releveImputation->getTiers()->getCompteStr(),
                            't' => 1
                        ];
                    elseif ($releveImputation->getPcc())
                    {
                        //0: bilan pcc, 1: tiers, 2: resultat, 3: tva
                        if ($releveImputation->getType() == 0)
                            $bilan = (object)
                            [
                                'id' => Boost::boost($releveImputation->getPcc()->getId()),
                                'l' => $releveImputation->getPcc()->getCompte(),
                                't' => 0
                            ];
                        elseif ($releveImputation->getType() == 2)
                            $charge = (object)
                            [
                                'id' => Boost::boost($releveImputation->getPcc()->getId()),
                                'l' => $releveImputation->getPcc()->getCompte(),
                                't' => 0
                            ];
                        elseif ($releveImputation->getType() == 3)
                            $tva = (object)
                            [
                                'id' => Boost::boost($releveImputation->getPcc()->getId()),
                                'l' => $releveImputation->getPcc()->getCompte(),
                                't' => 0
                            ];
                    }

                    if ($releveImputation->getImage() && !array_key_exists($releveImputation->getImage()->getId(),$imageSoeurs))
                    {
                        $imageSoeurs[$releveImputation->getImage()->getId()] = (object)
                        [
                            'id' => Boost::boost($releveImputation->getImage()->getId()),
                            'n' => $releveImputation->getImage()->getNom()
                        ];
                    }
                }
            }
            else
            {
                if ($s == 1)
                {
                    if ($convention == 0)
                    {
                        $bilan = $status->bilan;
                        $bilans = $status->bilans;
                    }
                    else
                    {
                        $tva = $status->tva;
                        $charge = $status->resultat;
                        $tvas = $status->tvas;
                        $resultats = $status->resultats;
                    }

                    if (intval($status->t) == 3)
                    {
                        $statLettre = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
                            ->getStatLettre($response->getImageFlague());
                    }
                }
                else if ($s == 2)
                {
                    if ($response->getCleDossier())
                    {
                        $bilan = $status->bilan;
                        $tva = $status->tva;
                        $charge = $status->resultat;
                    }
                }
            }

            //0: default, 1:piece a lettre, 2: clé a valider
            $flaguer = 0;

            if ($s == 3 || $s == 2 && $status->sPiece) $flaguer = 1;
            elseif ($s == 4) $flaguer = 2;

            if (count($bilans) > 0)
            {
                if (count($bilans) == 1) $bilan = $bilans[0];
                else $bilan = $bilans;
            }
            if (count($tvas) > 0)
            {
                if (count($tvas) == 1) $tva = $tvas[0];
                else $tva = $tvas;
            }
            if (count($resultats) > 0)
            {
                if (count($resultats) == 1) $charge = $resultats[0];
                else $charge = $resultats;
            }

            $status->sl = $statLettre;
            $numCompte = $response->getBanqueCompte()->getNumcompte();
            if (strlen($numCompte) >= 11)
                $numCompte = substr($numCompte,-11);

            $cleWp = 0;
            if (property_exists($status, 'sPiece') && $status->sPiece)
                $cleWp = 1;

            /*$diff = 0;
            if ($s == 1)
                $diff = $status->diff;*/

            $status->isoeur = array_values($imageSoeurs);

            $nonLettrables = [];
            if (!$response->getImageFlague() && trim($response->getNonLettrable()) != '')
            {
                $nonLettrables = $this->getDoctrine()->getRepository('AppBundle:Image')
                    ->getImageActifsByIds(json_decode($response->getNonLettrable()), false);

                $response->setNonLettrable(json_encode($nonLettrables));
            }

            $status->inl = $nonLettrables;

            if (!$bilan && !$charge && !$tva && !$response->getCleDossier())
            {
                /** @var Pcc $pccAttente */
                $pccAttente = (floatval($response->getDebit()) != 0) ? $attenteDebit : $attenteCredit;
                $bilan = (object)
                [
                    'id' => Boost::boost($pccAttente->getId()),
                    'l' => $pccAttente->getCompte(),
                    'i' => $pccAttente->getIntitule(),
                    't' => 0
                ];
            }

            $res = (object)
            [
                'id' => Boost::boost($response->getId()),
                'b' => $response->getBanqueCompte()->getBanque()->getNom(),
                'bc' => $numCompte,
                'i' => $response->getImage()->getNom(),
                'd' => date_format($response->getDateReleve(),'d/m/Y'),
                'l' => $this->getDoctrine()->getRepository('AppBundle:Releve')->getLibelleWithComplement($response,$cfonbCodeActives),
                'm' => -1 * ($response->getDebit() - $response->getCredit()),
                's' => $status,
                'ss' => $s,
                'ss3' => $s,
                'ss2' => $status,
                'imi' => Boost::boost($response->getImage()->getId()),
                't' => $bilan,
                'c' => $charge,
                'tva' => $tva,
                'ad' => $response->getAvecDetail(),
                'cleWP' => $cleWp,
                'nat' => (object)
                [
                    's' => $s,
                    'n' => $response->getNature()
                ],
                'n' => $response->getNature(),
                'find' => property_exists($status, 'find') ? $status->find : '',
                'r_goup' => $response->getId(),
                'is_stat' => 1
            ];

            $results[] = $res;

            //continue;

            $indexEclater = count($results) - 1;

            $find = '';
            if (property_exists($status, 'exts'))
            {
                $results[$indexEclater]->s->ecla = 1;

                foreach ($status->exts->releveExts as $rExt)
                {
                    /*
                    'cde' => $cleDossierExt,
                    're' => $releveExt,
                    'images' => []
                    */

                    $st = null;

                    if ($rExt)
                    {
                        $bilan = null;
                        $charge = null;
                        $tva = null;

                        /** @var CleDossierExt $cleDossierExt */
                        $cleDossierExt = $rExt->cde;
                        /** @var ReleveExt $releveExt */
                        $releveExt = $rExt->re;

                        $m = (property_exists($rExt, 'm')) ? $rExt->m : $releveExt->getMontant();

                        //2: Bilan; 1:TVA; 0: Resultat
                        if ($releveExt->getImageFlague())
                        {
                            $cmpts = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                                ->getComptesByImageFlague($releveExt->getImageFlague());

                            $imagesIds = [];
                            foreach ($cmpts->images as $im)
                                $imagesIds[] = Boost::boost($im->getId());

                            if (count($cmpts->tiers) == 1) $libelle = $cmpts->tiers[0]->getIntitule();
                            elseif (count($cmpts->images) == 1) $libelle = $cmpts->images[0]->getNom();
                            else $libelle = 'Multiple';

                            foreach ($cmpts->images as $im)
                            {

                            }

                            //0: engagement, 1:tresorerie, 2: tresorerie avec piece, 3:ecriture particulier
                            if ($cleDossierExt->getCleDossier()->getTypeCompta() != 1 &&
                                $cleDossierExt->getCleDossier()->getTypeCompta() != 2 &&
                                count($cmpts->b) > 0)
                            {
                                $bilan = $cmpts->b;
                            }

                            if ($cleDossierExt->getCleDossier()->getTypeCompta() != 0 &&
                                count($cmpts->c) > 0)
                            {
                                $charge = $cmpts->c;
                            }
                            if ($cleDossierExt->getCleDossier()->getTypeCompta() != 0 &&
                                count($cmpts->t) > 0)
                            {
                                $tva = $cmpts->t;
                            }

                            $st = (object)
                            [
                                'rExt' => 1,
                                's' => 1,
                                'libelle' => $libelle,
                                'id' => Boost::boost($releveExt->getId()),
                                'imgs' => StringExt::encodeURI(json_encode($imagesIds))
                            ];
                        }
                        else
                        {
                            if ($cleDossierExt->getTypeCompte() == 2)
                            {
                                if ($cleDossierExt->getTiers())
                                {
                                    $bilan = (object)
                                    [
                                        'id' => Boost::boost($cleDossierExt->getTiers()->getId()),
                                        'l' => $cleDossierExt->getTiers()->getCompteStr(),
                                        'i' => $cleDossierExt->getTiers()->getIntitule(),
                                        't' => 1
                                    ];
                                }
                                elseif ($cleDossierExt->getPcc())
                                {
                                    $bilan = (object)
                                    [
                                        'id' => Boost::boost($cleDossierExt->getPcc()->getId()),
                                        'l' => $cleDossierExt->getPcc()->getCompte(),
                                        'i' => $cleDossierExt->getPcc()->getIntitule(),
                                        't' => 0
                                    ];
                                }
                            }
                            elseif ($cleDossierExt->getTypeCompte() == 1)
                            {
                                $tva = (object)
                                [
                                    'id' => Boost::boost($cleDossierExt->getPcc()->getId()),
                                    'l' => $cleDossierExt->getPcc()->getCompte(),
                                    'i' => $cleDossierExt->getPcc()->getIntitule(),
                                    't' => 0
                                ];
                            }
                            elseif ($cleDossierExt->getTypeCompte() == 0)
                            {
                                $charge = (object)
                                [
                                    'id' => Boost::boost($cleDossierExt->getPcc()->getId()),
                                    'l' => $cleDossierExt->getPcc()->getCompte(),
                                    'i' => $cleDossierExt->getPcc()->getIntitule(),
                                    't' => 0
                                ];
                            }

                            $st = (object)
                            [
                                'rExt' => 1,
                                's' => count($rExt->images) > 0 ? 2 : 0
                            ];
                        }

                        $res = (object)
                        [
                            'id' => Boost::boost($response->getId()),
                            'b' => '',
                            'bc' => '',
                            'i' => '',
                            'd' => '',
                            'l' => $this->getDoctrine()->getRepository('AppBundle:Releve')->getLibelleWithComplement($response,$cfonbCodeActives),
                            'm' => $m,
                            's' => $st,
                            'ss' => $s,
                            'ss3' => $s,
                            'ss2' => $st,
                            'imi' => Boost::boost($response->getImage()->getId()),
                            't' => $bilan,
                            'c' => $charge,
                            'tva' => $tva,
                            'ad' => $response->getAvecDetail(),
                            'cleWP' => $cleWp,
                            'nat' => (object)
                            [
                                's' => $releveExt->getImageFlague() ? 1 : $s,
                                'n' => $response->getNature()
                            ],
                            'n' => $response->getNature(),
                            'find' => '', // implode(',',$rExt->images)
                            'italic' => 1,
                            'cde' => Boost::boost($cleDossierExt->getId()),
                            'r_goup' => $response->getId()
                        ];

                        $results[] = $res;
                    }
                }

                //reste
                if ($status->exts->releveExtReste)
                {
                    /** @var CleDossierExt $cleDossierExt */
                    $cleDossierExt = $status->exts->releveExtReste->cde;

                    $bilan = null;
                    $charge = null;
                    $tva = null;

                    //2: Bilan; 1:TVA; 0: Resultat
                    if ($cleDossierExt->getTypeCompte() == 2)
                    {
                        if ($cleDossierExt->getTiers())
                        {
                            $bilan = (object)
                            [
                                'id' => Boost::boost($cleDossierExt->getTiers()->getId()),
                                'l' => $cleDossierExt->getTiers()->getCompteStr(),
                                'i' => $cleDossierExt->getTiers()->getIntitule(),
                                't' => 1
                            ];
                        }
                        elseif ($cleDossierExt->getPcc())
                        {
                            $bilan = (object)
                            [
                                'id' => Boost::boost($cleDossierExt->getPcc()->getId()),
                                'l' => $cleDossierExt->getPcc()->getCompte(),
                                'i' => $cleDossierExt->getPcc()->getIntitule(),
                                't' => 0
                            ];
                        }
                    }
                    elseif ($cleDossierExt->getTypeCompte() == 1)
                    {
                        $tva = (object)
                        [
                            'id' => Boost::boost($cleDossierExt->getPcc()->getId()),
                            'l' => $cleDossierExt->getPcc()->getCompte(),
                            'i' => $cleDossierExt->getPcc()->getIntitule(),
                            't' => 0
                        ];
                    }
                    elseif ($cleDossierExt->getTypeCompte() == 0)
                    {
                        $charge = (object)
                        [
                            'id' => Boost::boost($cleDossierExt->getPcc()->getId()),
                            'l' => $cleDossierExt->getPcc()->getCompte(),
                            'i' => $cleDossierExt->getPcc()->getIntitule(),
                            't' => 0
                        ];
                    }

                    if (count($status->exts->releveExts) == 0)
                    {
                        $results[count($results) - 1]->t = $bilan;
                        $results[count($results) - 1]->c = $charge;
                        $results[count($results) - 1]->tva = $tva;
                    }
                    else
                    {
                        $results[$indexEclater]->is_stat = 0;
                        $results[$indexEclater]->nat->s = 1;

                        $m = -$status->exts->releveExtReste->m;
                        $res = (object)
                        [
                            'id' => Boost::boost($response->getId()),
                            'b' => '',
                            'bc' => '',
                            'i' => '',
                            'd' => '',
                            'l' => $this->getDoctrine()->getRepository('AppBundle:Releve')->getLibelleWithComplement($response,$cfonbCodeActives),
                            'm' => $m,
                            's' => null,
                            'ss' => $s,
                            'ss3' => $s,
                            'ss2' => $status,
                            'imi' => Boost::boost($response->getImage()->getId()),
                            't' => $bilan,
                            'c' => $charge,
                            'tva' => $tva,
                            'ad' => $response->getAvecDetail(),
                            'cleWP' => $cleWp,
                            'nat' => (object)
                            [
                                's' => $s,
                                'n' => $response->getNature()
                            ],
                            'n' => $response->getNature(),
                            'find' => '',
                            'italic' => 1,
                            'cde' => Boost::boost($cleDossierExt->getId()),
                            'r_goup' => $response->getId()
                        ];

                        $results[] = $res;
                    }
                }
            }

            $response->setFlaguer($flaguer);

            if (!$releveJson)
            {
                $releveJson = new ReleveJson();
                $releveJson
                    ->setReleve($rl)
                    ->setJson(json_encode($res))
                    ->setAModifier(0)
                    ->setDateDerniereModif(new \DateTime());

                $em->persist($releveJson);
            }
            else $releveJson->setJson(json_encode($res)); //if ($updateJson)
        }

        $em->flush();
        return $results;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveImputationPieceAction(Request $request)
    {
        $isReleve = (intval($request->request->get('type')) == 0);
        $rel = Boost::deboost($request->request->get('releve'),$this);
        $releveExt = Boost::deboost($request->request->get('releve_ext'), $this);

        if(is_bool($rel) || is_bool($releveExt)) return new Response('security');

        /** @var Releve $releve */
        $releve = null;
        /** @var BanqueSousCategorieAutre $bsca */
        $bsca = null;

        /** @var ReleveExt $releveExt */
        $releveExt = $this->getDoctrine()->getRepository('AppBundle\Entity\ReleveExt')
            ->find($releveExt);

        if ($isReleve)
            $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
                ->find($rel);
        else
            $bsca = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
                ->find($rel);

        $imputationPar = intval($request->request->get('par_piece')); //1 : piece, 2 :cle

        $images = json_decode($request->request->get('images'));
        $image = null;
        $em = $this->getDoctrine()->getManager();

        $imageFlague = new ImageFlague();
        $imageFlague->setDateCreation(new \DateTime());
        $em->persist($imageFlague);
        $em->flush();
        foreach ($images as $im)
        {
            $id = Boost::deboost($im->id,$this);
            if(is_bool($id)) return new Response('security');
            $image = $this->getDoctrine()->getRepository('AppBundle:Image')->find($id);
            $type = intval($im->type);

            if ($type == 0)
            {
                $tvaImputationControleNoFlagues = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                    ->getNotFlague($image);

                foreach ($tvaImputationControleNoFlagues as $tvaImputationControleNoFlague)
                    $tvaImputationControleNoFlague->setImageFlague($imageFlague);
            }
            elseif ($type == 1)
            {
                $banqueSousCategorieAutres = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
                    ->getAllByImages([$image]);

                foreach ($banqueSousCategorieAutres as $banqueSousCategorieAutre)
                    if (!$banqueSousCategorieAutre->getImageFlague()) $banqueSousCategorieAutre->setImageFlague($imageFlague);
            }
        }

        $em->flush();

        if ($releveExt)
            $releveExt
                ->setImageFlague($imageFlague);
        elseif ($releve)
        {
            $releve
                ->setImageFlague($imageFlague)
                ->setEngagementTresorerie(intval($request->request->get('eng_tres')))
                ->setPasCle(0)
                ->setPasImage(0);
        }
        elseif ($bsca)
            $bsca
                ->setImageFlague2($imageFlague)
                ->setEngagementTresorerie(intval($request->request->get('eng_tres')));

        $em->flush();
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function methodeAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        return new Response($this->getDoctrine()->getRepository('AppBundle:MethodeComptable')->getMethodeDossier($dossier));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function addCleAction(Request $request)
    {
        $banqueCompte = Boost::deboost($request->request->get('compte'),$this);
        $banque = Boost::deboost($request->request->get('banque'),$this);
        $releve = Boost::deboost($request->request->get('releve'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $cle = Boost::deboost($request->request->get('cle_id'),$this);
        $cleLibelle = trim($request->request->get('cle'));
        $methode = intval($request->request->get('methode'));

        if(is_bool($banque) || is_bool($banqueCompte) || is_bool($releve) || is_bool($dossier) || is_bool($cle)) return new Response('security');

        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($banqueCompte);
        if (is_null($banqueCompte)) $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')->find($banque);
        else $banque = $banqueCompte->getBanque();

        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')->find($cle);

        /** @var Cle2[] $cle2s */
        $cle2s = (is_null($cle)) ? [] : $this->getDoctrine()->getRepository('AppBundle:Cle2')
            ->createQueryBuilder('c2')
            ->where('c2.cle2 = :cle')
            ->setParameter('cle',$cle)
            ->orderBy('c2.cle')
            ->getQuery()
            ->getResult();

        /** @var BanqueType[] $banqueTypes */
        $banqueTypes = $this->getDoctrine()->getRepository('AppBundle:BanqueType')
            ->createQueryBuilder('bt')
            ->orderBy('bt.libelle')
            ->getQuery()
            ->getResult();

        $bilanPcgs = [];
        $tvaPcgs = [];
        $resPcgs = [];
        $bilanPccs = [];
        $tvaPccs = [];
        $resPccs = [];

        /** @var CleDossier $cleDossier */
        $cleDossier = null;
        if (!is_null($cle))
        {
            /** @var CleCompte[] $cleComptes */
            $cleComptes = $this->getDoctrine()->getRepository('AppBundle:CleCompte')
                ->createQueryBuilder('cc')
                ->where('cc.cle = :cle')
                ->setParameter('cle',$cle)
                ->getQuery()
                ->getResult();

            foreach ($cleComptes as $cleCompte)
            {
                if ($cleCompte->getType() == 2) $bilanPcgs[] = '0#'.$cleCompte->getPcg()->getId();
                elseif ($cleCompte->getType() == 1) $tvaPcgs[] = '0#'.$cleCompte->getPcg()->getId();
                elseif ($cleCompte->getType() == 0) $resPcgs[] = '0#'.$cleCompte->getPcg()->getId();
            }

            /** @var CleDossier $cleDossier */
            $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
                ->createQueryBuilder('c')
                ->where('c.cle = :cle')
                ->setParameter('cle',$cle)
                ->andWhere('c.dossier = :dossier')
                ->setParameter('dossier',$dossier)
                ->getQuery()
                ->getOneOrNullResult();

            if ($cleDossier->getBilanTiers()) $bilanPccs[] = '1#'.$cleDossier->getBilanTiers()->getId();
            if ($cleDossier->getBilanPcc()) $bilanPccs[] = '0#'.$cleDossier->getBilanPcc()->getId();
            if ($cleDossier->getResultat()) $resPccs[] = '0#'.$cleDossier->getResultat()->getId();
            if ($cleDossier->getTva()) $tvaPccs[] = '0#'.$cleDossier->getTva()->getId();
        }

        $addCompte = ' <div class="form-horizontal">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="checkbox checkbox-inline">
                            <input type="checkbox" id="js_id_is_auxilliaire" checked>
                            <label for="js_id_is_auxilliaire">Compte&nbsp;Auxilliaire</label>
                        </div>             
                    </div>
                </div>
                <div class="row" id="container_radio_auxilliaire">
                    <div class="col-lg-12">
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="radio-fournisseur" value="0" name="radio-type-tiers" checked="">
                            <label for="radio-fournisseur">Frns</label>
                        </div>            
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="radio-client" value="1" name="radio-type-tiers">
                            <label for="radio-client">Clt</label>
                        </div>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="radio-autre" value="2" name="radio-type-tiers">
                            <label for="radio-autre">Autre</label>
                        </div>                                
                    </div>   
                </div>
                <div class="form-group">
                    <label for="js_id_compte" class="col-lg-3 control-label">Compte</label>
                    <div class="col-lg-9">
                        <input type="text" placeholder="Numéro de compte" id="js_id_compte" class="form-control" value="">
                    </div>
                </div>                
                <div class="form-group">
                    <label for="js_id_intitule" class="col-lg-3 control-label">Intitul&eacute;</label>
                    <div class="col-lg-9">
                        <input type="text" placeholder="Intitulé du compte" id="js_id_intitule" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group text-center">
                    <span class="btn btn-xs btn-white" id="js_id_save_new_compte"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Ajouter</span>
                </div>           
            </div>';

        return $this->render('BanqueBundle:ReleveBanque2:add-cle.html.twig',[
            'dossier' => $dossier,
            'releve' => $releve,
            'banque' => $banque,
            'cle' => $cle,
            'cle2s' => $cle2s,
            'cleLibelle' => $cleLibelle,
            'banqueTypes' => $banqueTypes,
            'cleDossier' => $cleDossier,
            'bilanPcgs' => json_encode($bilanPcgs),
            'resPcgs' => json_encode($resPcgs),
            'tvaPcgs' => json_encode($tvaPcgs),
            'bilanPccs' => json_encode($bilanPccs),
            'resPccs' => json_encode($resPccs),
            'tvaPccs' => json_encode($tvaPccs),
            'methode' => $methode,
            'addCompte' => $addCompte
        ]);



        //cle: selectedText, compte:$('#js_banque_compte').val(), releve:releve, dossier:$('#dossier').val(), cle_id:cle_id
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveCleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $request->request;

        $cle = Boost::deboost($post->get('cle_id'),$this);
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $banqueType = Boost::deboost($post->get('banque_type'),$this);
        if(is_bool($dossier) || is_bool($banqueType) || is_bool($cle)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $banqueType = $this->getDoctrine()->getRepository('AppBundle:BanqueType')->find($banqueType);
        //$cle = $this->getDoctrine()->getRepository('AppBundle:Cle')->find($cle);
        $tauxTva = floatval($post->get('taux_tva'));
        $pasPiece = intval($post->get('pas_piece'));
        $type = intval($post->get('type')); //0 : save cle; 1:save et propager; 2 : supprimer
        $tousDossier = (intval($post->get('tous_dossier')) == 1);
        $typeCompte = intval($post->get('type_compta')); //0:engagement; 1:tresorerie sans piece; 2 : tresorerie avec piece
        $cleExplodes = $this->getDoctrine()->getRepository('AppBundle:Cle')->explodeCle($post->get('cle'));
        $cleLibelle = $cleExplodes->c;

        $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')
            ->getByLibelle($cleLibelle);

        $bilans = json_decode($post->get('bilans'));
        $resultats = json_decode($post->get('resultats'));
        $tvas = json_decode($post->get('tvas'));

        if (!$cle)
        {
            $cle = new Cle();

            $cle
                ->setBanqueType($banqueType)
                ->setCle($cleLibelle)
                ->setTva($tauxTva)
                ->setType(0)
                ->setTypeCompta($typeCompte);

            $em->persist($cle);
            $em->flush();

            /**
             * resultats
             */
            foreach ($resultats->pcgs as $id)
            {
                $typeId = explode('#',$id);
                $compte = $this->getDoctrine()->getRepository('AppBundle:Pcg')->find($typeId[1]);
                $cleCompte = new CleCompte();
                $cleCompte->setCle($cle);
                $cleCompte->setType(0); //resultat
                $cleCompte->setPcg($compte);
                $em->persist($cleCompte);
            }
            /**
             * tvas
             */
            foreach ($tvas->pcgs as $id)
            {
                $typeId = explode('#',$id);
                $compte = $this->getDoctrine()->getRepository('AppBundle:Pcg')->find($typeId[1]);
                $cleCompte = new CleCompte();
                $cleCompte->setCle($cle);
                $cleCompte->setType(1); //tva
                $cleCompte->setPcg($compte);
                $em->persist($cleCompte);
            }
            /**
             * Bilans
             */
            foreach ($bilans->pcgs as $id)
            {
                $typeId = explode('#',$id);
                $compte = $this->getDoctrine()->getRepository('AppBundle:Pcg')->find($typeId[1]);
                $cleCompte = new CleCompte();
                $cleCompte->setCle($cle);
                $cleCompte->setType(2); //bilan
                $cleCompte->setPcg($compte);
                $em->persist($cleCompte);
            }
        }
        $em->flush();

        $pccObject = json_decode($post->get('pcc')); //{ r:0#30821, t:0#30821, b:0#30821 }; 0 :tiers , 1 : pcc
        //bilan
        $typeId = explode('#',$pccObject->b);
        $bilanPcc = null;
        $bilanTier = null;
        if (intval($typeId[0]) == 0) $bilanPcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($typeId[1]);
        else $bilanTier = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($typeId[1]);

        //resultat
        $typeId = explode('#',$pccObject->r);
        $resultat = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($typeId[1]);

        //tva
        $typeId = explode('#',$pccObject->t);
        $tva = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($typeId[1]);

        if ($bilanPcc || $bilanTier || $resultat || $tva)
        {
            $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
                ->getCleDossierByCle($cle,$dossier);

            $add = false;
            if (!$cleDossier)
            {
                $add = true;
                $cleDossier = new CleDossier();
            }
            
            if ($typeCompte == 0 && (!$resultat || !$tva)) $typeCompte = 3;

            $cleDossier
                ->setDossier($dossier)
                ->setCle($cle)
                ->setTypeCompta($typeCompte)
                ->setTauxTva($tauxTva)
                ->setResultat($resultat)
                ->setTva($tva)
                ->setBilanTiers($bilanTier)
                ->setBilanPcc($bilanPcc)
                ->setPasPiece($pasPiece);

            if ($add) $em->persist($cleDossier);

            $em->flush();
        }

        return new Response(0);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function clesPropositionsAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        //$cle = Boost::deboost($request->request->get('cle'),$this);
        if(is_bool($releve)) return new Response('security');
        /** @var Releve $releve */
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        /** @var Cle $cle */

        $dossier = $releve->getBanqueCompte()->getDossier();

        $banques = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->getBanques($dossier);

        /** @var CfonbBanque[] $cfonbCodeActives */
        $cfonbCodeActives = $this->getDoctrine()->getRepository('AppBundle:CfonbBanque')
                ->cfonbActiveInBanque($releve->getBanqueCompte()->getBanque());
        $libelle = $this->getDoctrine()->getRepository('AppBundle:Releve')->getLibelleWithComplement($releve,$cfonbCodeActives);

        $cles = $this->getDoctrine()->getRepository('AppBundle:Cle')
            ->getClesValideLibelle($libelle,$dossier);

        $cle = (count($cles) > 0) ? $cles[0] : null;

        /** @var BanqueType[] $banqueTypes */
        $banqueTypes = $this->getDoctrine()->getRepository('AppBundle:BanqueType')
            ->createQueryBuilder('bt')
            ->orderBy('bt.libelle')
            ->getQuery()
            ->getResult();

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        $utilisateurScriptura = (intval($utilisateur->getClient()->getId()) == 626);

        return $this->render('BanqueBundle:ReleveBanque2:cle-propositions.html.twig',[
            'releve' => $releve,
            'cles'=>$cles,
            'cleChoise' => $cle,
            'banqueTypes' => $banqueTypes,
            'methode' => intval($request->request->get('methode')),
            'dossier' => $dossier,
            'banques' => $banques,
            'us' => $utilisateurScriptura
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function clePropertiesAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $cle = Boost::deboost($request->request->get('cle'),$this);
        if(is_bool($cle)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')->find($cle);
        $typeCompta = $cle->getTypeCompta();
        /** @var CleCompte[] $cleComptes */
        /*$cleComptes = $this->getDoctrine()->getRepository('AppBundle:CleCompte')
            ->createQueryBuilder('cc')
            ->where('cc.cle = :cle')
            ->setParameter('cle',$cle)
            ->getQuery()
            ->getResult();
        $resultats = [];
        $tvas = [];
        $bilans = [];
        foreach ($cleComptes as $cleCompte)
        {
            //type 0: resultat; 1:tva; 2 : bilan
            if ($cleCompte->getType() == 0)
            {
                $resultats[] = (object)
                [
                    'id' => Boost::boost($cleCompte->getPcg()->getId()),
                    'c' => $cleCompte->getPcg()->getCompte(),
                    'i' => $cleCompte->getPcg()->getIntitule()
                ];
            }
            elseif ($cleCompte->getType() == 1)
            {
                $tvas[] = (object)
                [
                    'id' => Boost::boost($cleCompte->getPcg()->getId()),
                    'c' => $cleCompte->getPcg()->getCompte(),
                    'i' => $cleCompte->getPcg()->getIntitule()
                ];
            }
            elseif ($cleCompte->getType() == 2)
            {
                $bilans[] = (object)
                [
                    'id' => Boost::boost($cleCompte->getPcg()->getId()),
                    'c' => $cleCompte->getPcg()->getCompte(),
                    'i' => $cleCompte->getPcg()->getIntitule()
                ];
            }
        }
        $pcgs = (object)
        [
            'r' => $resultats,
            't' => $tvas,
            'b' => $bilans
        ];*/

        /** @var CleDossier $cleDossier */
        $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
            ->createQueryBuilder('cd')
            ->where('cd.cle = :cle')
            ->andWhere('cd.dossier = :dossier')
            ->setParameters([
                'cle' => $cle,
                'dossier' => $dossier
            ])
            ->getQuery()
            ->getOneOrNullResult();

        $bilan = '<option value="0#0"></option>';
        $resultat = '<option value="0#0"></option>';
        $tva = '<option value="0#0"></option>';
        $tvaTaux = 0;

        if (!is_null($cleDossier))
        {
            if (!is_null($cleDossier->getBilanTiers()))
            {
                $tiers = $cleDossier->getBilanTiers();
                $bilan = '<option value="1#'. $tiers->getId() .'">'. $tiers->getCompteStr() . '-' . $tiers->getIntitule() .'</option>';
            }
            if (!is_null($cleDossier->getBilanPcc()))
            {
                $pcc = $cleDossier->getBilanPcc();
                $bilan = '<option value="0#'. $pcc->getId() .'">'. $pcc->getCompte() . '-' . $pcc->getIntitule() .'</option>';
            }
            if (!is_null($cleDossier->getResultat()))
            {
                $pcc = $cleDossier->getResultat();
                $resultat = '<option value="0#'. $pcc->getId() .'">'. $pcc->getCompte() . '-' . $pcc->getIntitule() .'</option>';
            }
            if (!is_null($cleDossier->getTva()))
            {
                $pcc = $cleDossier->getTva();
                $tva = '<option value="0#'. $pcc->getId() .'">'. $pcc->getCompte() . '-' . $pcc->getIntitule() .'</option>';
            }
            $tvaTaux = floatval($cleDossier->getTauxTva());
            $typeCompta = $cleDossier->getTypeCompta();
        }

        $result = (object)
        [
            'b' => $bilan,
            't' => $tva,
            'r' => $resultat,
            'tt' => $tvaTaux,
            'tc' => $typeCompta
        ];

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function pccsInBanqueTypeAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $banqueType = Boost::deboost($request->request->get('banque_type'),$this);

        if(is_bool($dossier) || is_bool($banqueType)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $banqueType = $this->getDoctrine()->getRepository('AppBundle:BanqueType')->find($banqueType);

        /** @var BanqueTypePcg[] $banqueTypePcgs */
        $banqueTypePcgs = $this->getDoctrine()->getRepository('AppBundle:BanqueTypePcg')
            ->createQueryBuilder('btp')
            ->leftJoin('btp.pcg','p')
            ->where('btp.banqueType = :banqueType')
            ->setParameter('banqueType',$banqueType)
            ->orderBy('p.compte')
            ->getQuery()
            ->getResult();

        $pcgsResultats = [];
        $pcgsTvas = [];
        $pcgsBilans = [];
        foreach ($banqueTypePcgs as $banqueTypePcg)
        {
            //0: resultat; 1:tva; 2 : bilan
            if ($banqueTypePcg->getType() == 0) $pcgsResultats[] = $banqueTypePcg->getPcg();
            elseif ($banqueTypePcg->getType() == 1) $pcgsTvas[] = $banqueTypePcg->getPcg();
            elseif ($banqueTypePcg->getType() == 2) $pcgsBilans[] = $banqueTypePcg->getPcg();
        }

        $pccsResultats = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPCCByPCG($pcgsResultats,$dossier,[],true);
        $pccsTvas = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPCCByPCG($pcgsTvas,$dossier,[],true);
        $pccsBilans = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPCCByPCG($pcgsBilans,$dossier,[],true);

        $pccsResultatsParents = functions::getParentsChilds($pccsResultats,$this->getDoctrine(),true);
        $pccsTvasParents = functions::getParentsChilds($pccsTvas,$this->getDoctrine());
        $pccsBilansParents = functions::getParentsChilds($pccsBilans,$this->getDoctrine(),true);

        $selectResultats = '<option value="0#0"></option>';
        $selectTvas = '<option value="0#0"></option>';
        $selectBilans = '<option value="0#0"></option>';

        foreach ($pccsResultatsParents as $parent)
        {
            $selectResultats .= '<option value="'. $parent->id .'">'. $parent->text .'</option>';
            foreach ($parent->children as $item)
            {
                $selectResultats .= '<option value="'. $item->id .'">'. $item->text .'</option>';

                foreach ($item->children as $item1)
                {
                    $selectResultats .= '<option value="'. $item1->id .'">'. $item1->text .'</option>';

                    foreach ($item1->children as $item2)
                    {
                        $selectResultats .= '<option value="'. $item2->id .'">'. $item2->text .'</option>';

                        foreach ($item2->children as $item3)
                        {
                            $selectResultats .= '<option value="'. $item3->id .'">'. $item3->text .'</option>';
                        }
                    }
                }
            }
        }
        foreach ($pccsTvasParents as $parent)
        {
            $selectTvas .= '<option value="'. $parent->id .'">'. $parent->text .'</option>';

            foreach ($parent->children as $item)
            {
                $selectTvas .= '<option value="'. $item->id .'">'. $item->text .'</option>';

                foreach ($item->children as $item1)
                {
                    $selectTvas .= '<option value="'. $item1->id .'">'. $item1->text .'</option>';

                    foreach ($item1->children as $item2)
                    {
                        $selectTvas .= '<option value="'. $item2->id .'">'. $item2->text .'</option>';

                        foreach ($item2->children as $item3)
                        {
                            $selectTvas .= '<option value="'. $item3->id .'">'. $item3->text .'</option>';
                        }
                    }
                }
            }
        }
        foreach ($pccsBilansParents as $parent)
        {
            $selectBilans .= '<option value="'. $parent->id .'">'. $parent->text .'</option>';

            foreach ($parent->children as $item)
            {
                $selectBilans .= '<option value="'. $item->id .'">'. $item->text .'</option>';

                foreach ($item->children as $item1)
                {
                    $selectBilans .= '<option value="'. $item1->id .'">'. $item1->text .'</option>';

                    foreach ($item1->children as $item2)
                    {
                        $selectBilans .= '<option value="'. $item2->id .'">'. $item2->text .'</option>';

                        foreach ($item2->children as $item3)
                        {
                            $selectBilans .= '<option value="'. $item3->id .'">'. $item3->text .'</option>';
                        }
                    }
                }
            }
        }

        return new JsonResponse(
            (object)
            [
                'r' => $selectResultats,
                't' => $selectTvas,
                'b' => $selectBilans
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveCleDossierAction(Request $request)
    {
        $cle = Boost::deboost($request->request->get('cle'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($dossier) || is_bool($cle) || is_bool($releve)) return new Response('security');

        $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')->find($cle);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);

        $pasPiece = intval($request->request->get('pas_de_piece'));
        $typeCompta = intval($request->request->get('type_compta'));
        $bilanExplode = explode('#', $request->request->get('bilan'));
        $tvaExplode = explode('#', $request->request->get('tva'));
        $resultatExplode = explode('#',$request->request->get('resultat'));
        $tvaTaux = floatval($request->request->get('tva_taux'));

        if ($typeCompta === 5)
        {
            $banque = Boost::deboost($request->request->get('banque_desactiver'),$this);
            if(is_bool($banque)) return new Response('security');
            $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')
                ->find($banque);
            $cleDossiers = new CleDossiers();
            $cleDossiers
                ->setCle($cle)
                ->setDossier($dossier);

            $em = $this->getDoctrine()->getManager();
            $em->persist($cleDossiers);

            $cleDossierASupprimer = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
                ->getCleDossierByCle($cle,$dossier);
            if ($cleDossierASupprimer) $em->remove($cleDossierASupprimer);
            $em->flush();

            return new Response(1);
        }

        $bilanPcc = null;
        $bilanTiers = null;
        if (intval($bilanExplode[0]) == 0) $bilanPcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($bilanExplode[1]);
        else $bilanTiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($bilanExplode[1]);

        $resultat = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($resultatExplode[1]);
        $tva = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($tvaExplode[1]);

        $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
            ->createQueryBuilder('cd')
            ->where('cd.cle = :cle')
            ->andWhere('cd.dossier = :dossier')
            ->setParameters([
                'cle' => $cle,
                'dossier' => $dossier
            ])
            ->getQuery()
            ->getOneOrNullResult();

        $isAdd = false;
        if (is_null($cleDossier))
        {
            $isAdd = true;
            $cleDossier = new CleDossier();
        }

        $cleDossier
            ->setBilanPcc($bilanPcc)
            ->setBilanTiers($bilanTiers)
            ->setTva($tva)
            ->setResultat($resultat)
            ->setTauxTva($tvaTaux)
            ->setTypeCompta($typeCompta)
            ->setDossier($dossier)
            ->setCle($cle)
            ->setPasPiece($pasPiece);

        $em = $this->getDoctrine()->getManager();
        if ($isAdd) $em->persist($cleDossier);
        $em->flush();

        $resultsCles = [$cle];

        foreach (json_decode($request->request->get('cles_slaves')) as $cs)
        {
            $cleS = $this->getDoctrine()->getRepository('AppBundle:Cle')
                ->find(Boost::deboost($cs,$this));

            $resultsCles[] = (object)
            [
                's' => 1,
                'c' => $cleS
            ];

            if ($cle && $cleS)
            {
                $cleSlave = $this->getDoctrine()->getRepository('AppBundle:CleSlave')
                    ->findOneBy([
                        'cle' => $cle,
                        'cleSlave' => $cleS,
                        'dossier' => $dossier
                    ]);

                if (!$cleSlave)
                {
                    $cleSlave = new CleSlave();
                    $cleSlave
                        ->setCle($cle)
                        ->setCleSlave($cleS)
                        ->setDossier($dossier);
                    $em->persist($cleSlave);
                }
            }
        }

        $releve->setCleDossier($cleDossier);
        $em->flush();

        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function annulerImputationAction(Request $request)
    {
        $type = intval($request->request->get('type'));
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        $em = $this->getDoctrine()->getManager();

        /** @var ReleveExt[] $releveExts */
        $releveExts = [];

        if ($type == 0)
        {
            $releve->setCleDossier(null);

            /** @var ReleveExt[] $releveExts */
            $releveExts = $this->getDoctrine()->getRepository('AppBundle:ReleveExt')
                ->findBy(['releve' => $releve]);

            foreach ($releveExts as $releveExt)
            {
                if ($releveExt->getImageFlague()) $em->remove($releveExt->getImageFlague());
                $em->remove($releveExt);
            }

            $releve->setPasCle(1);
        }
        elseif ($type == 1 || $type == 2)
        {
            $releve->setEngagementTresorerie(0);
            $releve->setImageTemp(null);

            $imageFlague = $releve->getImageFlague();
            $em->remove($imageFlague);
        }
        
        $releveImputations = $this->getDoctrine()->getRepository('AppBundle:ReleveImputation')
            ->getReleveImputation($releve);
        foreach ($releveImputations as $releveImputation) $em->remove($releveImputation);

        $releve->setEcritureChange(0);

        $em->flush();
        try
        {
            return new Response(0);
        }
        catch (Exception $ex)
        {
            return new Response(1);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function refreshAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        $releve =  $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);

        $releve->setPasImage(0);
        $releve->setPasCle(0);

        $this->getDoctrine()->getManager()->flush();
        return new Response(0);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getImagesByCleAction(Request $request)
    {
        $exercice = $request->request->get('exercice');
        $cle = Boost::deboost($request->request->get('cle'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier) || is_bool($cle)) return new Response('security');
        $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')->find($cle);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $results = [];
        /** @var Tiers[] $tiersTemps */
        $tiersTemps = $this->getDoctrine()->getRepository('AppBundle:Tiers')->findLikeCle($cle->getCle(),$dossier);
        $index = 0;
        foreach ($tiersTemps as $tier)
        {
            /** @var TvaImputationControle[] $tvaImputations */
            $tvaImputations = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                ->getImageByTier($tier,$exercice);

            foreach ($tvaImputations as $tvaImputation)
            {
                //$tvaImputation = new TvaImputationControle();

                $imputationControl = $this->getDoctrine()->getRepository('AppBundle:ImputationControle')
                    ->getImputationControle($tvaImputation);
                //$tvaImputation = new TvaImputationControle();
                $mht = -$tvaImputation->getMontantHt();
                $mTva = -$tvaImputation->getMontantHt() * $tvaImputation->getTvaTaux()->getTaux() / 100;

                $image = $tvaImputation->getImage();
                $imageNom = $image->getNom();
                $g = $imageNom;
                $results[] = (object)
                [
                    'k' => $index,
                    'g' => $g,
                    'id' => $index,
                    'p' => $index,
                    'i' => $imageNom,
                    'ii' => Boost::boost($image->getId()),
                    'd' => (is_null($imputationControl) || is_null($imputationControl->getDateFacture())) ? '' : $imputationControl->getDateFacture()->format('d/m/Y'),
                    't' => ((is_null($tvaImputation->getTiers())) ? '' : $tvaImputation->getTiers()->getIntitule() . ' - ') . $imputationControl->getNumFacture(),
                    'e' => $tvaImputation->getImage()->getExercice(),
                    'b' => is_null($tvaImputation->getTiers()) ? '' : $tvaImputation->getTiers()->getCompteStr(),
                    'r' => is_null($tvaImputation->getPcc()) ? '' : $tvaImputation->getPcc()->getCompte(),
                    'tva' => is_null($tvaImputation->getPccTva()) ? '' : $tvaImputation->getPccTva()->getCompte(),
                    'ht' => $mht,
                    'mtva' => $mTva,
                    'ttc' => $mht + $mTva,
                    'tr' => (is_null($imputationControl) || is_null($imputationControl->getModeReglement())) ? '' : $imputationControl->getModeReglement()->getLibelle(),
                    'nr' => (is_null($imputationControl) || is_null($imputationControl->getNumPaiement())) ? '' : $imputationControl->getNumPaiement(),
                    'dr' => (is_null($imputationControl) || is_null($imputationControl->getDateReglement())) ? '' : $imputationControl->getDateReglement()->format('d/m/Y'),
                    'f' => (is_null($image->getImageFlague())) ? 0 : 1
                ];

                $index++;
            }
        }

        return new JsonResponse($results);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function pasCleAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        /** @var Releve $releve */
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);

        $releve
            ->setPasCle(1)
            ->setPasImage(0)
            ->setCleDossier(null)
            ->setImageFlague(null);

        $this->getDoctrine()->getManager()->flush();

        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function showSearchAction(Request $request)
    {
        $exercice = intval($request->request->get('exercice'));
        $selectedText = trim($request->request->get('selected_text'));
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
        if(is_bool($banqueCompte) || is_bool($dossier)) return new Response('security');
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($banqueCompte);
        if (!is_null($banqueCompte)) $dossier = $banqueCompte->getDossier();
        else $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $methode = intval($request->request->get('methode'));

        $resultsImages = [];
        $tiersTemps = $this->getDoctrine()->getRepository('AppBundle:Tiers')->findLikeCle($selectedText,$dossier);
        $index = 0;

        $p = 0;
        $results = [];

        foreach ($tiersTemps as $tier)
        {
            /** @var TvaImputationControle[] $tvaImputations */
            $tvaImputations = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                ->getImageByTier($tier,$exercice);

            foreach ($tvaImputations as $tvaImputationControl)
            {
                $mTva = abs($tvaImputationControl->getMontantHt() * ((is_null($tvaImputationControl->getTvaTaux())) ? 0 : $tvaImputationControl->getTvaTaux()->getTaux()) / 100);
                $mTTc = abs($tvaImputationControl->getMontantHt() + $mTva);
                $imputationControl = $this->getDoctrine()->getRepository('AppBundle:ImputationControle')
                    ->getImputationControle($tvaImputationControl);

                $image = $tvaImputationControl->getImage();
                $imageNom = $image->getNom();

                /** @var Separation $separation */
                $separation = $this->getDoctrine()->getRepository('AppBundle:Separation')
                    ->createQueryBuilder('sep')
                    ->where('sep.image = :image')
                    ->setParameter('image',$tvaImputationControl->getImage())
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();

                $categorie = null;
                if ($separation)
                {
                    if (!is_null($separation->getSoussouscategorie())) $categorie = $separation->getSoussouscategorie()->getSouscategorie()->getCategorie();
                    elseif (!is_null($separation->getSouscategorie())) $categorie = $separation->getSouscategorie()->getCategorie();
                    elseif (!is_null($separation->getCategorie())) $categorie = $separation->getCategorie();
                }

                if ($categorie)
                {
                    $codeFrns = ['CODE_FRNS','CODE_MULTIFRNS'];
                    if (in_array($categorie->getCode(),$codeFrns))
                    {
                        /*if ($tvaImputationControl->getTiers()->getType() != 0)
                        {
                            $em = $this->getDoctrine()->getManager();
                            $tvaImputationControl->getTiers()->setType(0);
                            $em->flush();
                        }*/

                        $mTTc *= -1;
                        $mTva *= -1;
                    }
                }

                $g = $imageNom.'-'.$image->getNumPage();

                $key = ((is_null($imputationControl) || is_null($imputationControl->getDateFacture())) ? '21000101' : $imputationControl->getDateFacture()->format('Ymd')). '_' .$p;

                $bilan = null;
                $resultat = null;
                $tva = null;

                $keyI = $image->getId().'_';
                if ($tvaImputationControl->getTiers())
                {
                    $bilan = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getTiers()->getId()),
                        'l' => $tvaImputationControl->getTiers()->getCompteStr(),
                        't' => 1
                    ];
                    $g .= '1_'.$tvaImputationControl->getTiers()->getId();
                }
                elseif ($tvaImputationControl->getPccBilan())
                {
                    $bilan = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getPccBilan()->getId()),
                        'l' => $tvaImputationControl->getPccBilan()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$tvaImputationControl->getPccBilan()->getId();
                }

                if ($tvaImputationControl->getPcc())
                {
                    $resultat = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getPcc()->getId()),
                        'l' => $tvaImputationControl->getPcc()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$tvaImputationControl->getPcc()->getId();
                }
                if ($tvaImputationControl->getPccTva())
                {
                    $tva = (object)
                    [
                        'id' => Boost::boost($tvaImputationControl->getPccTva()->getId()),
                        'l' => $tvaImputationControl->getPccTva()->getCompte(),
                        't' => 0
                    ];
                    $g .= '0_'.$tvaImputationControl->getPccTva()->getId();
                }

                $mHt = $mTTc - $mTva;
                if (array_key_exists($keyI,$results))
                {
                    $results[$keyI]['ht'] += $mHt;
                    $results[$keyI]['mtva'] += $mTva;
                    $results[$keyI]['ttc'] += $mTTc;
                }
                else
                    $results[$keyI] =
                        [
                            'k' => $key,
                            'g' => $g,
                            'id' => $p,
                            'p' => $p,
                            'i' => $image->getNom().'-'.$image->getNumPage(),
                            'ii' => Boost::boost($image->getId()),
                            'd' => (is_null($imputationControl) || is_null($imputationControl->getDateFacture())) ? '' : $imputationControl->getDateFacture()->format('d/m/Y'),
                            't' => ((is_null($tvaImputationControl->getTiers())) ? '' : $tvaImputationControl->getTiers()->getIntitule() . ' - ') . $imputationControl->getNumFacture(),
                            'e' => $tvaImputationControl->getImage()->getExercice(),
                            'b' => $bilan,
                            'r' => $resultat,
                            'tva' => $tva,
                            'ht' => $mHt,
                            'mtva' => $mTva,
                            'ttc' => $mTTc,
                            'tr' => (is_null($imputationControl) || is_null($imputationControl->getModeReglement())) ? '' : $imputationControl->getModeReglement()->getLibelle(),
                            'nr' => (is_null($imputationControl) || is_null($imputationControl->getNumPaiement())) ? '' : $imputationControl->getNumPaiement(),
                            'dr' => (is_null($imputationControl) || is_null($imputationControl->getDateReglement())) ? '' : $imputationControl->getDateReglement()->format('d/m/Y'),
                            'f' => (is_null($image->getImageFlague())) ? 0 : 1,
                            'sm' => 5
                        ];
                $p++;
            }
        }

        $res = [];
        foreach ($results as $re) $res[] = $re;

        /** @var Releve[] $resultsReleves */
        $resultsReleves = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->getRelevesByCle($banqueCompte,$dossier,$selectedText,$exercice);

        return $this->render('BanqueBundle:ReleveBanque2:lettrage.html.twig',array(
            'cle'=>$selectedText,
            'images' => json_encode($res),
            'releves' => json_encode($resultsReleves),
            'methode' => $methode
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function rapprochersAction(Request $request)
    {
        $typeCompta = intval($request->request->get('type_compta'));
        $rapprochements = json_decode($request->request->get('rapprochements'));
        $em = $this->getDoctrine()->getManager();

        foreach ($rapprochements as $rapprochement)
        {
            $imageFlague = new ImageFlague();
            $imageFlague->setDateCreation(new \DateTime());
            $em->persist($imageFlague);
            $em->flush();

            foreach ($rapprochement->rs as $r)
            {
                $releve = Boost::deboost($r,$this);
                if(is_bool($releve)) return new Response('security');

                /** @var Releve $releve */
                $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
                $releve
                    ->setImageFlague($imageFlague)
                    ->setPasImage(0)
                    ->setPasCle(0)
                    ->setEngagementTresorerie($typeCompta);
            }

            foreach ($rapprochement->is as $i)
            {
                $image = Boost::deboost($i,$this);
                if(is_bool($image)) return new Response('security');
                $image = $this->getDoctrine()->getRepository('AppBundle:Image')->find($image);

                /** @var TvaImputationControle[] $tvaImputationsControles */
                $tvaImputationsControles = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                    ->createQueryBuilder('tic')
                    ->where('tic.image = :image')
                    ->andWhere('tic.imageFlague IS NULL')
                    ->setParameter('image',$image)
                    ->getQuery()
                    ->getResult();

                foreach ($tvaImputationsControles as $tvaImputationsControle) $tvaImputationsControle->setImageFlague($imageFlague);
                //$image->setImageFlague($imageFlague);
            }
        }

        $em->flush();
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function controlAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $banqueCompte_ = Boost::deboost($post->get('banqueCompte'),$this);
        $banque = Boost::deboost($post->get('banque'),$this);
        if(is_bool($dossier) || is_bool($banque) || is_bool($banqueCompte_)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $banqueCompte_ = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($banqueCompte_);
        $banque = ($banqueCompte_) ? null : $this->getDoctrine()->getRepository('AppBundle:Banque')->find($banque);
        $exercice = $post->get('exercice');

        $banqueComptes = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->getBanqueComptes($dossier,$banque,$banqueCompte_);

        $cloture = $dossier->getCloture();
        if (!$cloture || $cloture == 0) $cloture = 12;
        $clotureMois = $this->getDoctrine()->getRepository('AppBundle:TbimagePeriode')->getAnneeMoisExercices($dossier,$exercice);
        $clotureMoisN_1 = $this->getDoctrine()->getRepository('AppBundle:TbimagePeriode')->getAnneeMoisExercices($dossier,$exercice - 1);

        $statusGeneral = 0;
        $results = [];
        $dateNow = new \DateTime();
        $anneeMoisNow = $dateNow->format('Y-m');
        foreach ($banqueComptes as $banqueCompte)
        {
            //$mouvement = 0;
            $soldeDebut = $this->getDoctrine()->getRepository('AppBundle:Releve')->getSolde($banqueCompte,$exercice);
            $soldeFin = $this->getDoctrine()->getRepository('AppBundle:Releve')->getSolde($banqueCompte,$exercice,false);

            try
            {
                $mouvement = $this->getDoctrine()->getRepository('AppBundle:Releve')
                    ->getRelevesNew($banqueCompte->getDossier(), $exercice, null, $banqueCompte,true)->somme;
            }
            catch (ContextErrorException $contextErrorException)
            {
                $mouvement = 0;
            }

            /*if(count($mouvements) > 0){
                if(count($mouvements[0]) > 0) {
                    $mouvement = $mouvements[0][1];
                }
            }*/

            $exercices = [];
            for ($i = -2; $i < 3; $i++) $exercices[] = $exercice + $i;
            /** @var ReleveManquant[] $releveManquantsTemps */
            $releveManquantsTemps = $this->getDoctrine()->getRepository('AppBundle:ReleveManquant')
                ->createQueryBuilder('rm')
                ->where('rm.banqueCompte = :banqueCompte')
                ->andWhere('rm.dossier = :dossier')
                ->andWhere('rm.exercice IN(:exercices)')
                ->setParameters([
                    'banqueCompte' => $banqueCompte,
                    'exercices' => $exercices,
                    'dossier' => $dossier
                ])
                ->getQuery()
                ->getResult();
            $rMs = [];
            foreach ($releveManquantsTemps as $releveManquantsTemp)
            {
                $rmMois = [];
                foreach ($releveManquantsTemp->getMois() as $rmMoi)
                {
                    if (trim($rmMoi) != trim($anneeMoisNow)) $rmMois[] = $rmMoi;
                }
                $rMs = array_merge($rMs, $rmMois);
            }
            $rMs = array_map('trim',$rMs);
            $releveManquants = array_intersect($clotureMois->ms, $rMs);

            $pivot = array_search($clotureMois->m_0->format('Y-m'), $clotureMois->ms);
            if ($pivot === false) $pivot = count($clotureMois->ms) - 1;
            $aJour = true;
            $aJourA = -$pivot - 1;

            foreach ($clotureMois->ms as $m)
            {
                if ($aJour && !in_array($m,$releveManquants) && $m != $anneeMoisNow) $aJourA++;
                else $aJour = false;
            }

            $status = 0;
            if ($aJourA < -1)
            {
                if (count($clotureMois->ms) == count($releveManquants)) $status = 1;
                else $status = 2;
            }

            $ecart = round($soldeFin - $soldeDebut - $mouvement);
            if ($status != 0 || $ecart != 0)
            {
                $statusGeneral = 1;
            }

            $results[] = (object)
            [
                'bc' => $banqueCompte->getNumcompte(),
                'm' => $mouvement,
                'sd' => $soldeDebut,
                'sf' => $soldeFin,
                'rm' => $releveManquants,
                'aJourA' => $aJourA,
                'status' => $status,
                'pivot' => $pivot,
                'mois' => implode(',',$clotureMois->ms)
            ];
        }

        $importN = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')
            ->getLastDossier($dossier,$exercice,false);
        $importN_1 = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')
            ->getLastDossier($dossier,$exercice - 1,false);

        return new JsonResponse(
            (object)
            [
                's' => $statusGeneral,
                'res' => $results,
                'importN' => $importN,
                'importN_1' => $importN_1,
                'cl' => $cloture,
                'dc' => $clotureMois->c->format('d/m/Y'),
                'dcN_1' => $clotureMoisN_1->c->format('d/m/Y')
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function showEditReleveCompteAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        $methode = intval($request->request->get('methode'));
        $tiers = json_decode($request->request->get('tiers'));
        $pccs = json_decode($request->request->get('pccs'));

        $tvaOptions = '<option value="0-0" data-type="0"></option>';
        $chargeOptions = '<option value="0-0" data-type="0"></option>';
        $bilanPccOptions = '<option value="0-0" data-type="0"></option>';
        foreach ($pccs as $pcc)
        {
            $idsExplode = explode('-',$pcc->id);
            $id = $idsExplode[1];
            $libelle = $pcc->c . ' - ' . $pcc->i;

            if (strlen($pcc->c) > 2 && substr($pcc->c,0,3) == '445')
            {
                $tvaOptions .= '<option value="0-'.$id.'" data-type="0">'.$libelle.'</option>';
            }
            else if (substr($pcc->c,0,1) >= 6)
            {
                $chargeOptions .= '<option value="0-'.$id.'" data-type="0">'.$libelle.'</option>';
            }
            else if (substr($pcc->c,0,1) < 6)
            {
                $bilanPccOptions .= '<option value="0-'.$id.'" data-type="0">'.$libelle.'</option>';
            }
        }

        $bilanTiersOptions = '<option value="0" data-type="1"></option>';
        foreach ($tiers as $tier)
        {
            $idsExplode = explode('-',$tier->id);
            $id = $idsExplode[1];
            $libelle = $tier->c . ' - ' . $tier->i;
            $bilanTiersOptions .= '<option value="1-'.$id.'" data-type="1">'.$libelle.'</option>';
        }

        $releveImputations = $this->getDoctrine()->getRepository('AppBundle:ReleveImputation')
            ->getImputation($releve);
        $relevesCheckeds = [];
        foreach ($releveImputations as $releveImputation)
        {
            $montant = $releveImputation->getDebit() - $releveImputation->getCredit();
            $type = $releveImputation->getType();
            $idreleveDetail = $releveImputation->getId();

            if ($type == 0 || $type == 1) $methode = 0;
            else $methode = 1;

            //0: bilan pcc, 1: tiers,  2: resultat, 3: tva
            if ($releveImputation->getTiers()) $idCompte = '1-'.$releveImputation->getTiers()->getId();
            else $idCompte = '0-'.$releveImputation->getPcc()->getId();

            $relevesCheckeds[] = (object)
            [
                'montant' => $montant,
                'type' => $type,
                'idCompte' => $idCompte,
                'idReleveDetail' => $idreleveDetail
            ];
        }

        return $this->render('BanqueBundle:ReleveBanque2:releve-compte.html.twig',[
            'releve'=>$releve,
            'methode' => $methode,
            'tvaOptions' => $tvaOptions,
            'chargeOptions' => $chargeOptions,
            'bilanPccOptions' => $bilanPccOptions,
            'bilanTiersOptions' => $bilanTiersOptions,
            'releveCheckeds' => $relevesCheckeds
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function pccTiersAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        /** @var Pcc[] $pccs */
        $pccs = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPccs($dossier);
        /** @var Tiers[] $tiers */
        $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')->getTiers($dossier);

        $pccsResults = [];
        $tiersResults = [];
        foreach ($pccs as $pcc)
        {
            $pccsResults[] =  (object)
            [
                'id' => '0-'.$pcc->getId(),
                'c' => $pcc->getCompte(),
                'i' => $pcc->getIntitule()
            ];
            //$pcc = new Pcc();
            if ($pcc->getCollectifTiers() != -1 && count($tiers) == 0)
            {
                $tiers[] = (object)
                [
                    'id' => '0-'.$pcc->getId(),
                    'c' => $pcc->getCompte(),
                    'i' => $pcc->getIntitule()
                ];
            }
        }

        foreach ($tiers as $tier)
        {
            if ($tier->getType() != 0 && $tier->getType() != 1)
            {
                $compteAMettreAJour = ['401','411'];
                if (strlen($tier->getCompteStr()) >= 3 && in_array(substr($tier->getCompteStr(),0,3),$compteAMettreAJour))
                {
                    $tier->setType((substr($tier->getCompteStr(),0,3) === '401') ? 0 : 1);
                    $this->getDoctrine()->getManager()->flush();
                }
            }

            $tiersResults[] = (object)
            [
                'id' => '1-'.$tier->getId(),
                'c' => $tier->getCompteStr(),
                'i' => $tier->getIntitule()
            ];
        }

        $results = new \stdClass();
        $results->tiers = $tiersResults;
        $results->pccs = $pccsResults;
        return new JsonResponse($results);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveReleveCompteAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        $releve->setAvecDetail(0);

        $pasPiece = intval($request->request->get('pas_piece'));

        $em = $this->getDoctrine()->getManager();

        $releveImputationOlds = $this->getDoctrine()->getRepository('AppBundle:ReleveImputation')
            ->createQueryBuilder('ri')
            ->where('ri.releve = :releve')
            ->setParameter('releve',$releve)
            ->getQuery()
            ->getResult();

        foreach ($releveImputationOlds as $releveImputationOld) $em->remove($releveImputationOld);
        $em->flush();
        $releveDetails = json_decode($request->request->get('releve_details'));

        if (count($releveDetails) > 0)
        {
            $releve->setAvecDetail(1);
            $releve->setEcritureChange(1);
            foreach ($releveDetails as $rd)
            {
                $splites = explode('-',$rd->compte_id);
                $type = $rd->type;
                $releveImputation = new ReleveImputation();
                //0: bilan pcc, 1: tiers, 1: bilan pcc, 2: resultat, 3: tva
                if ($type != 1)
                {
                    $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($splites[1]);
                    $releveImputation->setPcc($pcc);
                }
                else
                {
                    $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($splites[1]);
                    $releveImputation->setTiers($tiers);
                }
                $montant = -floatval($rd->montant);

                $releveImputation
                    ->setType($type)
                    ->setDebit(($montant > 0) ? $montant : 0)
                    ->setCredit(($montant < 0) ? abs($montant) : 0)
                    ->setReleve($releve);

                $em->persist($releveImputation);
            }
        }
        else $releve->setEcritureChange(0);

        $releve->setMaj($pasPiece);
        $em->flush();

        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function desactiverCleAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $banque = Boost::deboost($request->request->get('banque'),$this);

        if (is_bool($dossier) || is_bool($banque)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')
            ->find($banque);

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => [
                $dossier,
                $banque
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function ecritureCategoriesAutresAction(Request $request)
    {
        $imagesAChargers = json_decode($request->request->get('images_a_chargers'));
        $ids = [];
        foreach ($imagesAChargers as $imagesACharger)
        {
            $id = Boost::deboost($imagesACharger,$this);
            if (is_bool($id)) return new Response('security');
            $ids[] = $id;
        }

        $images = $this->getDoctrine()->getRepository('AppBundle:Image')
            ->createQueryBuilder('i')
            ->andWhere('i.id IN (:ids)')
            ->setParameter('ids',$ids)
            ->getQuery()
            ->getResult();

        /** @var BanqueSousCategorieAutre[] $banquesSousCategorieAutres */
        $banquesSousCategorieAutres = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
            ->getAllByImages($images);

        $results = [];
        foreach ($banquesSousCategorieAutres as $banqueSousCategorieAutre)
        {
            $image = $banqueSousCategorieAutre->getImage();
            /** @var \DateTime $date */
            $date = null;
            if ($banqueSousCategorieAutre->getDate()) $date = $banqueSousCategorieAutre->getDate();
            elseif ($banqueSousCategorieAutre->getDateFacture()) $date = $banqueSousCategorieAutre->getDateFacture();

            $tiers = $banqueSousCategorieAutre->getCompteTiers();
            $pcc = $banqueSousCategorieAutre->getCompteBilan();
            $pccTva = $banqueSousCategorieAutre->getCompteTva();

            $bilan = null;
            $tva = null;
            $resultat = null;

            if ($tiers)
            {
                $bilan = (object)
                [
                    'id' => Boost::boost($tiers->getId()),
                    'l' => $tiers->getCompteStr(),
                    't'=> 1
                ];
            }
            if ($pcc)
            {
                if (intval(substr($pcc->getCompte(),0,1)) < 6)
                {
                    $bilan = (object)
                    [
                        'id' => Boost::boost($pcc->getId()),
                        'l' => $pcc->getCompte(),
                        't'=> 0
                    ];
                }
                else
                {
                    $resultat = (object)
                    [
                        'id' => Boost::boost($pcc->getId()),
                        'l' => $pcc->getCompte(),
                        't'=> 0
                    ];
                }
            }
            if ($pccTva)
            {
                $tva = (object)
                [
                    'id' => Boost::boost($pccTva->getId()),
                    'l' => $pccTva->getCompte(),
                    't'=> 0
                ];
            }

            $mTtc = abs($banqueSousCategorieAutre->getMontant());
            $separation = $this->getDoctrine()->getRepository('AppBundle:Separation')
                ->getSeparationByImage($image);
            if (
                $separation && (
                    $separation->getSouscategorie() && $separation->getSouscategorie()->getId() == 7 ||
                    $separation->getSoussouscategorie() && $separation->getSoussouscategorie()->getId() == 2791)
            ){ $mTtc *= -1; }


            $tvaCoeff = 1;
            if ($tva && $banqueSousCategorieAutre->getTvaTaux()) $tvaCoeff += $banqueSousCategorieAutre->getTvaTaux()->getTaux() / 100;
            $mHt = round($mTtc / $tvaCoeff, 2);
            $mTva = $mTtc - $mHt;

            $results[] = (object)
                [
                    'ii' => Boost::boost($image->getId()),
                    'i' => $image->getNom(),
                    'd' => $date ? $date->format('d/m/Y') : '',
                    't' => $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')->getLibelleComplete($banqueSousCategorieAutre),
                    'b' => $bilan,
                    'r' => $resultat,
                    'tva' => $tva,
                    'ht' => $mHt,
                    'mtva' => $mTva,
                    'ttc' => $mTtc,
                ];
        }

        return new JsonResponse($results);

        /*return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => $banquesSousCategorieAutres
        ]);*/
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function lettragesAutresAction(Request $request)
    {
        $banqueSousCategorieAutre = Boost::deboost($request->request->get('banque_sous_categorie_autre'),$this);
        if(is_bool($banqueSousCategorieAutre)) return new Response('security');
        $banqueSousCategorieAutre = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
            ->find($banqueSousCategorieAutre);

        $results = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
            ->picDocs($banqueSousCategorieAutre);

        return new JsonResponse($results);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function trBanqueAutreUpdatedAction(Request $request)
    {
        $banqueSousCategorieAutre = Boost::deboost($request->request->get('banque_categorie_autre'),$this);
        if(is_bool($banqueSousCategorieAutre)) return new Response('security');

        /** @var BanqueSousCategorieAutre $banqueSousCategorieAutre */
        $banqueSousCategorieAutre = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')->find($banqueSousCategorieAutre);
        $res = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
            ->getStatus($banqueSousCategorieAutre);
        return new JsonResponse($res);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function AnnulerLettrageBanqueAutreAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $banqueSousCategorieAutre = Boost::deboost($request->request->get('banque_sous_categorie_autre'),$this);
        /** @var BanqueSousCategorieAutre $banqueSousCategorieAutre */
        $banqueSousCategorieAutre = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')->find($banqueSousCategorieAutre);

        if ($banqueSousCategorieAutre && $banqueSousCategorieAutre->getImageFlague2())
            $em->remove($banqueSousCategorieAutre->getImageFlague2());

        $em->flush();
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function lettrageComptaAction(Request $request)
    {
        $bscaId = $request->request->get('bsca');
        $banqueSousCategorieAutre = Boost::deboost($bscaId,$this);
        if(is_bool($banqueSousCategorieAutre)) return new Response('security');
        /** @var BanqueSousCategorieAutre $banqueSousCategorieAutre */
        $banqueSousCategorieAutre = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
            ->find($banqueSousCategorieAutre);
        $index = intval($request->request->get('index'));

        $libelle = $banqueSousCategorieAutre->getLibelle();
        if (trim($libelle) == '') $libelle = $banqueSousCategorieAutre->getNomTiers();

        /** @var \DateTime $date */
        $date = $banqueSousCategorieAutre->getDate();
        if (!$date) $date = $banqueSousCategorieAutre->getDateFacture();
        if (!$date)
        {
            $imputationControle = $this->getDoctrine()->getRepository('AppBundle:ImputationControle')
                ->getByImage($banqueSousCategorieAutre->getImage());

            if ($imputationControle && $imputationControle->getDateFacture())
                $date = $imputationControle->getDateFacture();
            if (!$date && $imputationControle && $imputationControle->getDateEcheance())
                $date = $imputationControle->getDateEcheance();
        }

        $bilan = null;
        $tva = null;
        $resultat = null;

        if ($banqueSousCategorieAutre->getCompteTiers())
        {
            $bilan = (object)
            [
                'id' => $banqueSousCategorieAutre->getCompteTiers()->getId(),
                'c' => $banqueSousCategorieAutre->getCompteTiers()->getCompteStr(),
                'i' => $banqueSousCategorieAutre->getCompteTiers()->getIntitule(),
                't' => 1
            ];
        }
        elseif ($banqueSousCategorieAutre->getCompteBilan())
        {
            $bilan = (object)
            [
                'id' => $banqueSousCategorieAutre->getCompteBilan()->getId(),
                'c' => $banqueSousCategorieAutre->getCompteBilan()->getCompte(),
                'i' => $banqueSousCategorieAutre->getCompteBilan()->getIntitule(),
                't' => 0
            ];
        }

        if ($banqueSousCategorieAutre->getCompteChg())
        {
            $resultat = (object)
            [
                'id' => $banqueSousCategorieAutre->getCompteChg()->getId(),
                'c' => $banqueSousCategorieAutre->getCompteChg()->getCompte(),
                'i' => $banqueSousCategorieAutre->getCompteChg()->getIntitule(),
                't' => 0
            ];
        }
        if ($banqueSousCategorieAutre->getCompteTva())
        {
            $tva = (object)
            [
                'id' => $banqueSousCategorieAutre->getCompteTva()->getId(),
                'c' => $banqueSousCategorieAutre->getCompteTva()->getCompte(),
                'i' => $banqueSousCategorieAutre->getCompteTva()->getIntitule(),
                't' => 0
            ];
        }

        $bsca = (object)
        [
            'id' => $banqueSousCategorieAutre->getId(),
            'libelle' => $libelle,
            'image' => $banqueSousCategorieAutre->getImage(),
            'date' => $date,
            'montant' => $banqueSousCategorieAutre->getMontant(),
            'bilan' => $bilan,
            'resultat' => $resultat,
            'tva' => $tva
        ];

        return $this->render('BanqueBundle:ReleveBanque2:lettrage-compta.html.twig',[
            'bsca' => $bsca,
            'index' => $index,
            'bscaId' => $bscaId
        ]);
    }

    public function comptaALettrerAction(Request $request)
    {
        $banqueSousCategorieAutre = Boost::deboost($request->request->get('bsca'),$this);
        $idCompte = Boost::deboost($request->request->get('id_compte'),$this);
        if(is_bool($idCompte) || is_bool($banqueSousCategorieAutre)) return new Response('security');
        /** @var BanqueSousCategorieAutre $banqueSousCategorieAutre */
        $banqueSousCategorieAutre = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
            ->find($banqueSousCategorieAutre);

        $typeCompte = intval($request->request->get('type_compte'));
        /** @var Tiers $tiers */
        $tiers = null;
        /** @var Pcc $pcc */
        $pcc = null;

        if ($typeCompte == 1)
            $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')
                ->find($idCompte);
        else
            $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')
                ->find($idCompte);

        $tvaImputationControles = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
            ->getTvaImputationControleByCompte($pcc,$tiers,$banqueSousCategorieAutre->getImage()->getExercice(),$banqueSousCategorieAutre); //,

        $keyImages = [];
        foreach ($tvaImputationControles as $key => &$tvaImputationControle)
        {
            if ($tvaImputationControle->categorie_id &&
                (in_array($tvaImputationControle->categorie_id,[10,12]) && $tvaImputationControle->categorie_id != 1 ||
                    in_array($tvaImputationControle->type_piece_id,[9,13]) && $tvaImputationControle->type_piece_id == 1
                ))
            {
                $tvaImputationControle->m *= -1;
            }

            if ($key > 10) break;
            $keyImages[] = $key;
        }

        $allCombinaisons = functions::allCombinaisons($keyImages);
        $sommes = [];

        /*foreach ($allCombinaisons as $combinaison)
        {
            $somme = 0;
            foreach ($combinaison as $imageId) $somme += $arrayImages[$imageId]->montant;

            if (round(abs($somme),2) == round(abs($banqueSousCategorieAutre->getMontant()),2))
                $sommes[] = $combinaison;
        }*/

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => $allCombinaisons
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function libelleEditAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'), $this);
        if(is_bool($request)) return new Response('security');

        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);

        $action = intval($request->request->get('action'));
        $em = $this->getDoctrine()->getManager();

        if ($action == 0)
        {
            return $this->render('BanqueBundle:ReleveBanque2:libelle-edit.html.twig',[
                'releve' => $releve
            ]);
        }
        elseif ($action == 1)
        {
            $releve->setLibelle($request->request->get('libelle'));
            $em->flush();

            return new Response(1);
        }
    }
}