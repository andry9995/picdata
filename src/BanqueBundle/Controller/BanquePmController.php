<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 29/11/2018
 * Time: 09:50
 */

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\BanqueAutrePiece;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\BanqueObManquante;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageATraiter;
use AppBundle\Entity\ImageComment;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\Lot;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveInstruction;
use AppBundle\Entity\RelevePiece;
use AppBundle\Entity\Souscategorie;
use AppBundle\Entity\SouscategoriePasSaisir;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\NotificationDossier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Emails;

class BanquePmController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $tabs =
            [
                '0' => 'Banques<br>Manquantes',
                '1' => 'Opérations bancaires manquantes',
                '2' => 'Dépenses<br>sans pièces',
                '3' => 'Encaissements<br>sans pièces',
                '4' => 'Cheques<br>non identifiés',
                '10' => 'Factures fournisseurs<br>non payées',
                '11' => 'Factures clients<br>non payées',
                '5' => 'Fournisseurs',
                '6' => 'Clients',
                '7' => 'Autres<br>',
            ];
        return $this->render('BanqueBundle:BanquePm:index.html.twig',[
            'isPieceManquante' => 1,
            'tabs'=>$tabs
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function itemAction(Request $request)
    {
        $isPieceManquante = intval($request->request->get('isPieceManquante'));
        $client = null;
        $dossierArray = [];
        $exercice = intval($request->request->get('exercice'));
        $type = intval($request->request->get('type'));
        $intervals = json_decode($request->request->get('intervals'));
        $dateType = intval($request->request->get('date'));
        $user = $this->getUser();
        if(!$isPieceManquante){
            $listDossier = $request->request->get('listDossier');
            foreach ($listDossier as $key => $d) {
                $dossierId = Boost::deboost($d,$this);
                if(intval($dossierId) != 0) $dossierArray[] = intval($dossierId);
            }
            $pManquants = $this->getDoctrine()->getRepository('AppBundle:Releve')
                ->getPieceManquantForNotif($user,$dossierArray,$exercice,$type,$intervals,$dateType);
        }else{
            $banqueId = Boost::deboost($request->request->get('banque'),$this);
            $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
            $dossier = Boost::deboost($request->request->get('dossier'),$this);
            if($isPieceManquante && (is_bool($banqueId) || is_bool($banqueCompte) || is_bool($dossier))) return new Response('security');

            $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($banqueCompte);
            $banque = null;
            if (!$banqueCompte)
                $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')
                    ->find($banqueId);
            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
            $pManquants = $this->getDoctrine()->getRepository('AppBundle:Releve')
                    ->getPieceManquant($dossier,$banque,$banqueCompte,$exercice,$type,null,$intervals,$dateType,$client,$user,$dossierArray, false);

            /*if ($type == 0) return $this->render('IndicateurBundle:Affichage:test.html.twig',[
                'test' => $pManquants
            ]);*/
        }
        if (in_array($type,[0,2,3,4,5,6,8,9,10,11]))
            return new JsonResponse($pManquants);

        /*if (in_array($type,[5,6]))
            return $this->render('IndicateurBundle:Affichage:test.html.twig',[
                'test' => $pManquants
            ]);*/

        return new Response('En Cours...');
        return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$pManquants]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function imputationAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        /** @var Releve $releve */
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);

        $releveInstruction = $this->getDoctrine()->getRepository('AppBundle:ReleveInstruction')
            ->getByReleve($releve);

        $instruction = intval($request->request->get('instruction'));
        $em = $this->getDoctrine()->getManager();

        if ($releveInstruction)
            $releveInstruction->setInstruction($instruction);
        else
        {
            $releveInstruction = new ReleveInstruction();
            $releveInstruction
                ->setInstruction($instruction)
                ->setReleve($releve);

            $em->persist($releveInstruction);
        }

        $em->flush();

        $relevesCheckeds = [];
        $methode = 0;

        /*$bilan = null;
        $charge = null;
        $tva = null;

        if ($releve->getEcritureChange() == 1)
        {
            $releveImputations = $this->getDoctrine()->getRepository('AppBundle:ReleveImputation')
                ->getImputation($releve);
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
        }

        return $this->render('@Banque/BanquePm/imputation.html.twig',
            [
                'releve' => $releve,
                'releveCheckeds' => $relevesCheckeds,
                'methode' => $methode
            ]);*/
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function tiersPccsAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $tiersTemps = $this->getDoctrine()->getRepository('AppBundle:Tiers')
            ->getTiers($dossier);
        $tiers = [];
        foreach ($tiersTemps as $tiersTemp)
            $tiers[] = (object)
            [
                'id' => $tiersTemp->getId(),
                'c' => $tiersTemp->getCompteStr(),
                'i' => $tiersTemp->getIntitule(),
                't' => 1,
                'ty' => $tiersTemp->getType()
            ];

        $pccsTemps = $this->getDoctrine()->getRepository('AppBundle:Pcc')
            ->getPccs($dossier,false);
        $pccs = [];
        foreach ($pccsTemps as $pccsTemp)
            $pccs[] = (object)
            [
                'id' => $pccsTemp->getId(),
                'c' => $pccsTemp->getCompte(),
                'i' => $pccsTemp->getIntitule(),
                't' => 0,
                'ty' => -1
            ];
            
        return new JsonResponse((object)[
            't' => $tiers,
            'p' => $pccs
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function newTrAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $releve = Boost::deboost($request->request->get('releve'),$this);
        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
        $banqueId = Boost::deboost($request->request->get('banque'),$this);

        if(is_bool($dossier) || is_bool($releve) || is_bool($banqueCompte) || is_bool($banqueId)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);
        $banque = null;
        if (!$banqueCompte)
            $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')
                ->find($banqueId);

        $exercice = intval($request->request->get('exercice'));
        $type = intval($request->request->get('type'));

        $pManquants = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->getPieceManquant($dossier,$banque,$banqueCompte,$exercice,$type,$releve);

        return new JsonResponse($pManquants[0]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function imageUploaderAction(Request $request)
    {
        $type = intval($request->request->get('type'));
        $typeBanque = intval($request->request->get('type_banque'));
        $id = Boost::deboost($request->request->get('releve'),$this);
        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
        /** @var Souscategorie $sousCategorie */
        $sousCategorie = Boost::deboost($request->request->get('sc'), $this);


        if(is_bool($id) || is_bool($banqueCompte) || is_bool($sousCategorie)) return new Response('security');
        $mois = $request->request->get('mois');
        /** @var BanqueObManquante $banqueObManquant */
        $banqueObManquant = null;
        /** @var Releve $releve */
        $releve = null;

        /** @var Releve[] $relevePms */
        $relevePms = [];

        if ($typeBanque == 0)
        {
            $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
                ->find($id);
            $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
                ->find($banqueCompte);
        }
        else
        {
            $isReleve = intval($request->request->get('is_releve')) == 1;

            /** @var BanqueObManquante $banqueObManquant */
            $banqueObManquant = $isReleve ? null :
                $this->getDoctrine()->getRepository('AppBundle:BanqueObManquante')->find($banqueCompte);

            $banqueCompte = $isReleve ?
                $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($banqueCompte) :
                $banqueObManquant->getBanqueCompte();

            $sousCategorie = $this->getDoctrine()->getRepository('AppBundle:Souscategorie')
                ->find($sousCategorie);

            $relevePms = $this->getDoctrine()->getRepository('AppBundle:Releve')
                ->getPmInSousCategorie($banqueCompte,$sousCategorie,$mois);
        }

        return $this->render('BanqueBundle:BanquePm:image-uploader.html.twig',
            [
                'releve'=>$releve,
                'type'=>$type,
                'mois' => $mois,
                'banqueCompte' => $banqueCompte,
                'banqueObManquant' => $banqueObManquant,
                'typeBanque' => $typeBanque,
                'releves' => $relevePms
            ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $idDemo = -1;
        if($request->isXmlHttpRequest())
        {
            $directory = "IMAGES";
            $fs = new Filesystem();
            try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }

            $releveOb = Boost::deboost($request->request->get('releve_ob'),$this);
            $releve = Boost::deboost($request->request->get('releve'),$this);
            $dossier = Boost::deboost($request->request->get('dossier'),$this);
            if(is_bool($dossier) || is_bool($releve)) return new Response(-1);

            /** @var Dossier $dossier */
            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
            /** @var Releve $releve */
            $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
            /** @var Releve $releveOb */
            $releveOb = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releveOb);
            $exercice = intval($request->request->get('exercice'));

            //creation dossier dateScan
            $dateNow = new \DateTime();
            $directory .= '/'.$dateNow->format('Ymd');
            try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }

            $em = $this->getDoctrine()->getManager();

            $dateScan = \DateTime::createFromFormat('Ymd','20201212');
            $lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $this->getUser(), '',null,$dateScan);
            $file = $request->files->get('id_image');
            if ($file)
            {
                $file_name = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $name = basename($file_name,'.'.$extension);
                $source = $this->getDoctrine()->getRepository('AppBundle:SourceImage')->getBySource('PICDATA');
                $file->move($directory, $file_name);
                $newName = $dateNow->format('Ymd').'_'.Boost::getUuid(50);
                $fs->rename($directory.'/'.$file_name, $directory.'/'.$newName.'.'.$extension);

                $image = new Image();
                $nbPage = 1;

                if (strtoupper($extension) == 'PDF')
                {
                    $nbPage = intval(exec("pdfinfo /var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES/".$lot->getDateScan()->format('Ymd')."/".$newName.".".$extension." | awk '/Pages/ {print $2}'"));
                    if ($nbPage == 0)
                        $nbPage = 1;

                    $image->setNbpage($nbPage);
                }

                $image
                    ->setModeReglement(null)
                    ->setLot($lot)
                    ->setExercice($exercice)
                    ->setExtImage($extension)
                    ->setNbpage($nbPage)
                    ->setNomTemp($newName)
                    ->setOriginale($name)
                    ->setSourceImage($source)
                    ->setCodeAnalytique(null)
                    ->setCommentaireDossier(null);
                    /*->setSupprimer(1)
                    ->setRenommer(1)
                    ->setNumerotationLocal(1);*/

                if ($dossier->getSite()->getClient()->getId() == $idDemo) $image->setDownload(new \DateTime());

                $em->persist($image);
                $em->flush();

                if ($releveOb) $releveOb->setImageTemp($image);
                if ($releve) $releve->setImageTemp($image);
                else
                {
                    $typeBanque = intval($request->request->get('type_banque'));
                    if ($typeBanque == 0)
                    {
                        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
                        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
                            ->find($banqueCompte);
                        $relevePiece = new RelevePiece();
                        $relevePiece
                            ->setImage($image)
                            ->setBanqueCompte($banqueCompte)
                            ->setExercice($exercice)
                            ->setMois($request->request->get('mois'));

                        $em->persist($relevePiece);
                    }
                    else
                    {
                        $banqueObManquant = Boost::deboost($request->request->get('banque_ob_manquant'),$this);
                        $banqueObManquant = $this->getDoctrine()->getRepository('AppBundle:BanqueObManquante')
                            ->find($banqueObManquant);

                        $banqueCompte = $releveOb ? $releveOb->getBanqueCompte() : $banqueObManquant->getBanqueCompte();
                        $sousCategorie = $releveOb ?
                            $this->getDoctrine()->getRepository('AppBundle:Souscategorie')->getCategorieByNatureReleve($releveOb->getNature()) :
                            $banqueObManquant->getSouscategorie();

                        $banqueAutrePiece = new BanqueAutrePiece();
                        $banqueAutrePiece
                            ->setImage($image)
                            ->setExercice($exercice)
                            ->setBanqueCompte($banqueCompte)
                            ->setMois($request->request->get('mois'))
                            ->setSousCategorie($sousCategorie)
                            ->setReleve($releveOb);

                        $em->persist($banqueAutrePiece);
                    }
                }

                $em->flush();
                $lotGroup = $this->getDoctrine()->getRepository('AppBundle:LotGroup')->getNewLotGroup(1,$this->getUser(),$dossier);
                $lot->setLotGroup($lotGroup);
                $imageATraiter = new ImageATraiter();
                $imageATraiter->setImage($image);
                $em->persist($imageATraiter);
                $em->flush();
            }
            return new Response(1);
        }

        return new Response(-1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function showEditTiersAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($dossier) || is_bool($releve)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        $tiersModels = $this->getDoctrine()->getRepository('AppBundle:Tiers')
            ->getTypesAndModelDossier($dossier);

        return $this->render('BanqueBundle:BanquePm:edit-tiers.html.twig',[
            'dossier' => $dossier,
            'releve' => $releve,
            'tiersModels' => $tiersModels
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveTiersAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($dossier) || is_bool($releve)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $action = intval($request->request->get('action'));
        $type = intval($request->request->get('type'));
        $compte = $request->request->get('compte');
        $intitule = $request->request->get('intitule');

        $em = $this->getDoctrine()->getManager();
        $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')
            ->getPccTier($dossier,$type);

        $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')
            ->getOneByCompte($dossier,$compte,$type);

        if (!$tiers)
        {
            $tiers = new Tiers();
            $tiers
                ->setPcc($pcc)
                ->setType($type)
                ->setDossier($dossier)
                ->setCompteStr($compte)
                ->setCompte($compte)
                ->setIntitule($intitule)
                ->setCreeLe(new \DateTime());
            $em->persist($tiers);
            $em->flush();
        }

        if ($action === 1)
        {
            $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
            $releve->setCompteTiersTemp($tiers);
        }

        $em->flush();
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function changeTiersAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')->find($releve);
        $tiersSpliter = explode('-',$request->request->get('tiers'));
        $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')
            ->find($tiersSpliter[1]);
        $releve->setCompteTiersTemp($tiers);
        $this->getDoctrine()->getManager()->flush();
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function detailMoisAction(Request $request)
    {
        $type = intval($request->request->get('type'));
        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = null;
        /** @var BanqueObManquante $banqueObManquant */
        $banqueObManquant = null;
        $id = Boost::deboost($request->request->get('banque_compte'),$this);

        $sousCategorie = Boost::deboost($request->request->get('banque_pm_detail_mois'),$this);
        if(is_bool($id) || is_bool($sousCategorie)) return new Response('security');

        if ($type == 0)
            $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
                ->find($id);
        else
            $banqueObManquant = $this->getDoctrine()->getRepository('AppBundle:BanqueObManquante')
                ->find($id);

        $mois = $request->request->get('mois');
        $debut = \DateTime::createFromFormat('Y-m-d',$mois . '-01');
        $moisNext = intval($debut->format('m')) + 1;
        $anneeNext = intval($debut->format('Y'));
        if ($moisNext == 13)
        {
            $moisNext = 1;
            $anneeNext++;
        }
        $fin = \DateTime::createFromFormat('Y-m-d',$anneeNext .'-'. (($moisNext < 10) ? '0'.$moisNext : $moisNext) . '-01');
        $fin->sub(new \DateInterval('P1D'));

        if ($banqueObManquant)
        {
            /** @var ImputationControle[] $imputationControles */
            $imputationControles = $this->getDoctrine()->getRepository('AppBundle:ImputationControle')
                ->createQueryBuilder('ic')
                ->where('ic.banqueCompte = :banqueCompte')
                ->andWhere('ic.souscategorie = :sousCategorie')
                ->andWhere('(
                    (ic.dateReglement IS NOT NULL AND (ic.dateReglement BETWEEN :debut AND :fin)) OR
                    (ic.dateFacture IS NOT NULL AND (ic.dateFacture BETWEEN :debut AND :fin)) OR
                    (ic.dateEcheance IS NOT NULL AND (ic.dateEcheance BETWEEN :debut AND :fin))
                )')
                ->setParameters([
                    'banqueCompte' => $banqueObManquant->getBanqueCompte(),
                    'sousCategorie' => $banqueObManquant->getSouscategorie(),
                    'debut' => $debut,
                    'fin' => $fin
                ])
                ->getQuery()
                ->getResult();
        }
        else
        {
            $sousCategorie = $this->getDoctrine()->getRepository('AppBundle:Souscategorie')
                ->find(10);
            $sousSousCategorie = $this->getDoctrine()->getRepository('AppBundle:Soussouscategorie')
                ->find(11);

            /** @var ImputationControle[] $imputationControles */
            $imputationControles = $this->getDoctrine()->getRepository('AppBundle:ImputationControle')
                ->createQueryBuilder('ic')
                ->where('ic.banqueCompte = :banqueCompte')
                ->andWhere('(ic.souscategorie = :sousCategorie OR ic.soussouscategorie = :sousSousCategorie)')
                ->andWhere('(:debut BETWEEN ic.periodeD1 AND ic.periodeF1) OR (:fin BETWEEN ic.periodeD1 AND ic.periodeF1)')
                ->setParameters([
                    'banqueCompte' => $banqueCompte,
                    'sousCategorie' => $sousCategorie,
                    'sousSousCategorie' => $sousSousCategorie,
                    'debut' => $debut,
                    'fin' => $fin
                ])
                ->getQuery()
                ->getResult();
        }

        return $this->render('BanqueBundle:BanquePm:mois-details.html.twig',[
            'imputationControles'=>$imputationControles,
            'type' => $type
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function imageCommentSaveAction(Request $request)
    {
        $image = Boost::deboost($request->request->get('image'),$this);
        if(is_bool($image)) return new Response('security');
        $image = $this->getDoctrine()->getRepository('AppBundle:Image')
            ->find($image);

        $statut = intval($request->request->get('statut'));
        $commentaire = $request->request->get('comment');

        $imageComment = $this->getDoctrine()->getRepository('AppBundle:ImageComment')
            ->getByImage($image);

        $em = $this->getDoctrine()->getManager();
        if ($imageComment)
        {
            if ($statut == 0 && $commentaire == '')
                $em->remove($imageComment);
            else
                $imageComment
                    ->setStatus($statut)
                    ->setCommentaire($commentaire);
        }
        elseif ($statut != 0 || $commentaire != '')
        {
            $imageComment = new ImageComment();
            $imageComment
                ->setImage($image)
                ->setStatus($statut)
                ->setCommentaire($commentaire);

            $em->persist($imageComment);
        }

        $em->flush();

        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveObservationAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);
        $observation = trim($request->request->get('observation'));
        $releveInstruction = $this->getDoctrine()->getRepository('AppBundle:ReleveInstruction')
            ->getByReleve($releve);

        $em = $this->getDoctrine()->getManager();
        if (!$releveInstruction)
        {
            $releveInstruction = new ReleveInstruction();
            $releveInstruction
                ->setReleve($releve)
                ->setObservation($observation);
            $em->persist($releveInstruction);
        }
        else
            $releveInstruction->setObservation($observation);

        $em->flush();
        return new Response(1);
    }

    public function sendMailAction(Request $request)
    {
        $dossier = intval($request->request->get('dossier'));
        $exercice = intval($request->request->get('exercice'));
        $dataObMq = $request->request->get('dataObMq');
        $dataRbMq = $request->request->get('dataRbMq');
        $dataObMq = json_decode($dataObMq);
        $htmlObMq = '';
        if(is_bool($dossier)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $type = intval($request->request->get('type'));
        $title = $request->request->get('title');
        $client = $dossier->getSite()->getClient();
        $site = $dossier->getSite()->getNom();
        $dateNow = new \DateTime();
        $config = array();
        $config['copie-cache'] = '';
        $config['destinataire'] = '';
        $sujet = "Rappels Pièces Manquantes";

        $smtp_client = $this->getDoctrine()
                            ->getRepository('AppBundle:Smtp')
                            ->findOneBy(array(
                                'client' => $client
                            ));
        $isEc = true;
        if ( count($smtp_client) == 0 ) {
            $isEc = false;
            $from_address = 'support@'.strtolower($client->getNom()).'.biz';
        }else{
            $from_address = $smtp_client->getLogin();
        }

        $message = \Swift_Message::newInstance()
                                 ->setSubject($sujet)
                                 ->setFrom("andry@scriptura.biz", 'Pièces de Banques Manquantes');
        
        $email = $this->getDoctrine()
                     ->getRepository('AppBundle:NotificationPm')
                     ->getByDossier($dossier);

        $titre = [
            1 => "Monsieur",
            2 => "Madame",
            3 => "Mademoiselle",
            4 => "Monsieur, Madame",
        ];

        $politesses =[
            1 => "Cher",
            2 => "Chère",
            3 => "Chère",
            4 => "Cher"
        ];
        $titre_contact = "Monsieur";
        $politesse = "Cher";
        if($email->getTitreContact() !== null){
            if(isset($titre[$email->getTitreContact()])){
                $titre_contact = $titre[$email->getTitreContact()];
                $politesse = $politesses[$email->getTitreContact()];
            }
        }
        $mail = '';
        $mailCopie = '';
        $objet = '';
        if($email->getDestinataire() != null || $email->getDestinataire() != '')
            $mail = explode(';', $email->getDestinataire());
        if($email->getCopie() != null || $email->getCopie() != '')
            $mailCopie = explode(';', $email->getCopie());

        if($email->getContenu() != null || $email->getContenu() != '')
            $objet = $email->getContenu();

        if($mail == '' || $objet == '') return new JsonResponse('nomail');

        //traitement rb
        $dataRbMq = explode('*', $dataRbMq);
        $banqueRbMq = $dataRbMq[0];
        $compteRbMq = $dataRbMq[1];
        $releveRbMq = $dataRbMq[2];
        $htmlObMq = '';
        $htmlRbMq = '';
        if($releveRbMq != ''){
            $htmlMq = '<ul style="list-style-type: disc;"><li><b>Relevé bancaires</b></li>- '.$banqueRbMq.'<ul style="margin-left: -10px !important; list-style-type: circle;"><li>Compte # '.$compteRbMq;
            $htmlRbMq = $htmlMq.'<ul style="margin-left: -35px !important; list-style-type: none;"><li>+   Relevé Bancaire : '.$releveRbMq.'</li></ul></li></ul></ul>';
        }

        foreach ($dataObMq as $dObM) {
            if($htmlObMq == ''){
                $htmlObMq = '<ul style="list-style-type: disc;"><li><b>Opérations bancaires</b></li>- '.$banqueRbMq.'<ul style="margin-left: -10px !important; list-style-type: circle;"><li>Compte # '.$compteRbMq.'<ul style="margin-left: -35px !important; list-style-type: none;"><li>+   '.$dObM->libelle.' : '.$dObM->nb.'</li>';
            }else{
                $htmlObMq .= '<ul style="margin-left: -35px !important; list-style-type: none;"><li>+   '.$dObM->libelle.' : '.$dObM->nb.'</li>';
            }
        }
        $htmlObMq .= '</ul></li></ul></ul>';
        $htmlDossier = '<b>'.strtoupper($dossier->getNom()).'</b>';
        $htmlExo = '<b>Exercice: </b>'.$exercice;
        $objet = str_replace("[[date]]", $dateNow->format('d-m-Y'), $objet);
        $objet = str_replace("[[releves]]", $htmlRbMq, $objet);
        $objet = str_replace("[[operation]]", $htmlObMq, $objet);
        $objet = str_replace("[[dossier]]", $htmlDossier, $objet);
        $objet = str_replace("[[exercice]]", $htmlExo, $objet);

        if(!$isEc){
            $clientTheme = $this->getDoctrine()->getRepository('AppBundle:ClientTheme')
                ->find(1);
            $contenu = $this->renderView('@Banque/BanquePm/email-pm-scriptura.html.twig', array(
                'dossier' => $dossier->getNom(),
                'client' => $client,
                'site' => $site,
                'utilisateur' => $this->getUser(),
                'politesse' => $politesse,
                'titre_contact' => $titre_contact,
                'objet' => $objet,
                'nom' => $email->getNomContact()
            ));
        }else{
            $clientTheme = $this->getDoctrine()->getRepository('AppBundle:ClientTheme')
                ->findBy(array('client' => $client));
            $clientTheme = (count($clientTheme) > 0) ? $clientTheme[0] : null;
            $contenu = $this->renderView('@Banque/BanquePm/email-pm-ec.html.twig', array(
                'dossier' => $dossier->getNom(),
                'client' => $client,
                'site' => $site,
                'utilisateur' => $this->getUser(),
                'politesse' => $politesse,
                'titre_contact' => $titre_contact,
                'objet' => $objet,
                'nom' => $email->getNomContact()
            ));
        }

        //print_r($contenu);die;

        /*if($mail != '')
            foreach ($mail as $key => $m) {
                $message->addTo($m);
            }

        if($mailCopie != '')
            foreach ($mailCopie as $key => $mc) {
                $message->addBcc($mc);
            }*/
        $message->addTo('dinoh@scriptura.biz');
        $message->setBody($contenu, 'text/html');
        $email_statut = 0;
        if($this->get('mailer')->send($message)){
            $email_statut = 1;
        }
        $dateN = new \DateTime();

        $taches = $this->getDoctrine()
                       ->getRepository('AppBundle:Tache')
                       ->getTachesPourGestionTaches($dossier->getId(), $dateN, true, true, true, true, true, null);
        if(array_key_exists($dossier->getId(), $taches['taches'])){
            foreach ($taches['taches'][$dossier->getId()] as $k => $t) {
                if(!$t['expirer']) {
                    $datetimetache = $t['datetime'];
                    break;
                }
            }
        }
        $em = $this->getDoctrine()->getManager();
        $logEmail = new Emails();

        $logEmail->setToAddress($email->getDestinataire())
            ->setFromLabel("Banque Manquante")
            ->setCc($email->getCopie())
            ->setBcc($email->getDestinataire())
            ->setSujet($sujet)
            ->setContenu($contenu)
            ->setDateCreation(new \DateTime())
            ->setTypeEmail("BANQUE_MANQUANTE")
            ->setSmtp($smtp_client)
            ->setDateEcheance($datetimetache)
            ->setStatus($email_statut)
            ->setDossier($dossier)
            ->setFromAddress($from_address);
        if( $email_statut == 1 ) {
            $logEmail->setDateEnvoi(new \DateTime());
            $logEmail->setNbTentativeEnvoi(1);
        }
        $em->persist($logEmail);
        $em->flush();
        return new JsonResponse('ok');
    }

    public function showObManquanteAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        $banqueCompteId = $request->request->get('banqueCompteId');
        $dossierId = $request->request->get('dossierId');
        $nature = $request->request->get('nature');
        $rel = $this->getDoctrine()->getRepository('AppBundle:Releve')->getReleveObManquant($client,$dossierId,$banqueCompteId, 2019, $nature);
        return $this->render('BanqueBundle:BanquePm:ob-manquante-detail.html.twig',[
            'releve'=>$rel
        ]);
    }

    public function saveStatusObAction(Request $request)
    {
        $dossier = $request->request->get('dossier');
        $sousCategorie = Boost::deboost($request->request->get('s_categorie'), $this);
        if(is_bool($sousCategorie)) return new Response('security');

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

    public function showDetailsPmAction(Request $request)
    {
        $banqueCompteId = $request->request->get('banqueCompteId');
        $dossierId = $request->request->get('dossierId');
        $type = $request->request->get('type');
        $exercice = intval($request->request->get('exercice'));

        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($banqueCompteId);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossierId);
        $user = $this->getUser();
        $isEncais = false;
        switch ($type) {
            case 'dec':
                $type = 2;
                break;

            case 'enc':
                $type = 3;
                $isEncais = true;
                break;
            
            default:
                $type = 4;
                break;
        }

        $pManquants = $this->getDoctrine()->getRepository('AppBundle:Releve')
                ->getPieceManquant($dossier, null, $banqueCompte, $exercice, $type, null, [90,500000], 0, null, $user, [], false);
        return $this->render('BanqueBundle:BanquePm:pm-manquante-detail.html.twig',[
            'pManquants'=>$pManquants,
            'isEncais' => $isEncais
        ]);
    }

    public function notificationAction()
    {
        $tabs =
            [
                '8' => 'Banques<br>Tous dossiers',
                '9' => 'Autres PM<br> Notification'
            ];
        return $this->render('BanqueBundle:BanquePm:index.html.twig',[
            'isPieceManquante' => 0,
            'tabs' => $tabs 
        ]);
    }

    public function notificationTypeMailAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $dossier = $request->request->get('dossier');
            $value = intval($request->request->get('value'));
            $classe = $request->request->get('classe');
            $code = '';

            $dossier = $this->getDoctrine()
                            ->getRepository('AppBundle:Dossier')
                            ->find($dossier);

            if (!is_null($dossier)) {
                $em = $this->getDoctrine()->getManager();
                $relbq = 'table_pm_8_tm';
                $relpm = 'table_pm_9_tm';

                switch ($classe) {
                    case $relbq:
                        $code = 'BANQUE';
                        break;
                    case $relpm:
                        $code = 'ENVOIE PM';
                        break;
                }

                $notification = $this->getDoctrine()
                                     ->getRepository('AppBundle:Notification')
                                     ->findBy(array('code' => $code));
                $notification = $notification[0];

                $notifDossier = $this->getDoctrine()
                                     ->getRepository('AppBundle:NotificationDossier')
                                     ->findBy(array(
                                        'dossier' => $dossier,
                                        'notification' => $notification
                                     ));

                if($value && count($notifDossier < 1))
                {
                    $notifDossierConnex = new NotificationDossier();
                    $notifDossierConnex->setDossier($dossier)
                                       ->setNotification($notification);
                    $em->persist($notifDossierConnex);
                }else{
                    $em->remove($notifDossier[0]);
                }
                $em->flush();
            }
            return new Response(2);
        }else {
            throw new AccessDeniedException('Accès refusé');
        }
    }

    public function notificationGetLogForDossierAction(Request $request)
    {
        $dossier = $request->request->get('dossier');
        $index = $request->request->get('index');
        $emails = $this->getDoctrine()
                        ->getRepository('AppBundle:Emails')
                        ->findBy(array(
                            'dossier' => $dossier,
                            'typeEmail' => 'BANQUE_MANQUANTE'
                        ));
        return $this->render('BanqueBundle:BanquePm:liste-log-email.html.twig', [
            'emails' => $emails,
            'index' => $index 
        ]);
    }

    public function notificationGetContenuLogForDossierAction(Request $request)
    {
        $id = $request->request->get('id');
        $email = $this->getDoctrine()
                    ->getRepository('AppBundle:Emails')
                    ->find($id);
        return $this->render('BanqueBundle:BanquePm:liste-log-contenu-email.html.twig', [
            'email' => $email
        ]);
    }
}