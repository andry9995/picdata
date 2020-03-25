<?php

/**
 * Created by Netbeans
 * Created on : 27 août 2017, 11:14:52
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\OneVente;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OnePaiement;
use AppBundle\Entity\OnePaiementDetail;
use One\VenteBundle\Service\VenteService;
use One\VenteBundle\Service\PaiementService;
use One\VenteBundle\Service\DocumentService;

class PaiementController extends Controller
{
    /**
     * Liste des paiements
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request) {
        if ($request->isMethod('GET')) {
            $service = new VenteService($this->getDoctrine()->getManager());
            $stat = $request->query->get('stat');
            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $exercice = $request->query->get('exercice');

            if(null === $dossier){
                return new Response('');
            }
            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);
            //fin lesexperts.biz

            /** @var OneVente[] $factures */
            $factures = null;
            /** @var OnePaiement[] $paiements */
            $paiements = null;

            if ($stat === 'all') {
                $factures = $this->getDoctrine()
                    ->getRepository('AppBundle:OneVente')
                    ->getVentesForPaiement($clientProspects, $exercice, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
                $paiements = $this->getDoctrine()
                    ->getRepository('AppBundle:OnePaiement')
                    ->getPaiements($factures, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            } elseif ($stat === 'unpaid') {
                $factures = $this->getDoctrine()
                    ->getRepository('AppBundle:OneVente')
                    ->getVentesForPaiement($clientProspects, $exercice, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
                $paiements = array();
            } elseif ($stat === 'paid') {
                $factures = array();
                $paiements = $this->getDoctrine()
                    ->getRepository('AppBundle:OnePaiement')
                    ->getPaiements($factures, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            }
            $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);

            return $this->render('OneVenteBundle:Paiement:list.html.twig', array(
                'factures' => $factures,
                'paiements' => $paiements,
                'factureDetails' => $service->getVenteDetails($clientProspects, $exercice,'facture'),
                'stat' => $stat,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
            ));
        }
    }
    
    /**
     * Page de filtre sur les paiements à effectuer
     * @param Request $request
     * @return Response
     */
    public function getAction(Request $request) {
        if ($request->isMethod('GET')) {
            return $this->render('OneVenteBundle:Paiement:get.html.twig');
        }
    }
    
    /**
     * Nouveau paiement
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request) {
        if ($request->isMethod('GET')) {
            $facid = $request->query->get('facid');
            $unpaid = $request->query->get('unpaid');
            $page = $request->query->get('page');
            $moyenpaiements = $this->getDoctrine()
                ->getRepository('AppBundle:OneMoyenPaiement')
                ->getMoyenPaiements();

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);
            //fin lesexperts.biz


            $banquecomptes = $this->getDoctrine()
                ->getRepository('AppBundle:BanqueCompte')
                ->findBy(array('dossier' => $dossier));

            return $this->render('OneVenteBundle:Paiement:new.html.twig', array(
                    'page' => $page,
                    'facid' => $facid,
                    'unpaid' => $unpaid,
                    'moyenpaiements' => $moyenpaiements,
                    'banquecomptes' => $banquecomptes,
                ));
        }
    }
    
    /**
     * Nouveau paiement par encaissement
     * @param Request $request
     * @return Response
     */
    public function encnewAction(Request $request) {
        if ($request->isMethod('GET')) {
            $facid = $request->query->get('facid');
            $page = $request->query->get('page');
            $data = $request->query->get('data');
            $details = explode(';', $data);
            $encid = $details[0];
            $encdate = $details[1];
            $encmontant = $details[2];
            $encmoyen = $this->getDoctrine()->getRepository('AppBundle:OneMoyenPaiement')->find($details[3]);
            return $this->render('OneVenteBundle:Paiement:encnew.html.twig', array(
                    'page' => $page,
                    'facid' => $facid,
                    'encid' => $encid,
                    'encdate' => $encdate,
                    'encmontant' => $encmontant,
                    'encmoyen' => $encmoyen,
                ));
        }
    }
    
    /**
     * Nouveau paiement par avoir
     * @param Request $request
     * @return Response
     */
    public function avonewAction(Request $request) {
        if ($request->isMethod('GET')) {
            $facid = $request->query->get('facid');
            $page = $request->query->get('page');
            $data = $request->query->get('data');
            $dossierId = $request->query->get('dossierId');

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find(Boost::deboost($dossierId, $this));

            $details = explode(';', $data);
            $avoid = $details[0];
            $avodate = $details[1];
            $avomontant = $details[2];
            $avocode = $details[3];
            $moyenpaiements = $this->getDoctrine()
                ->getRepository('AppBundle:OneMoyenPaiement')
                ->getMoyenPaiements();

            $banquecomptes = $this->getDoctrine()
                ->getRepository('AppBundle:BanqueCompte')
                ->findBy(array('dossier'  => $dossier));

            return $this->render('OneVenteBundle:Paiement:avonew.html.twig', array(
                    'page' => $page,
                    'facid' => $facid,
                    'avoid' => $avoid,
                    'avodate' => $avodate,
                    'avomontant' => $avomontant,
                    'avocode' => $avocode,
                    'moyenpaiements' => $moyenpaiements,
                    'banquecomptes' => $banquecomptes,
                ));
        }
    }
    
    /**
     * Visualisation d'un paiement
     * @param type $id
     * @return Response
     */
    public function showAction(Request $request, $id)
    {
        $paiement = $this->getDoctrine()
            ->getRepository('AppBundle:OnePaiement')
            ->find($id);

        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OnePaiementDetail')
            ->findByOnePaiement($paiement);

        $modele = $this->getDoctrine()
            ->getRepository('AppBundle:OneDocumentModele')
            ->findOneByPaiement($paiement);

        $dossierId = $request->query
            ->get('dossierId');

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($dossierId, $this));

        $modeles = $this->getDoctrine()
            ->getRepository('AppBundle:OneModele'
            )->getModelesByDossier($dossier);

        $filename = $this->getParameter('one_documents_dir') . 'paiement' . DIRECTORY_SEPARATOR . 'Encaissement divers-' . $paiement->getCode() . '.pdf';
        $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        return $this->render('OneVenteBundle:Paiement:show.html.twig', array(
            'paiement' => $paiement,
            'modele' => $modele,
            'modeles' => $modeles,
            'articles' => $articles,
            'filename' => $filename,
            'params' => $params,
            'dossier' => $dossier,
            'utilisateur' => $utilisateur
        ));
    }
    
    /**
     * Sauvegarde de paiements
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            try {
                $service = new PaiementService($this->getDoctrine()->getManager());
                $venteService = new VenteService($this->getDoctrine()->getManager());
                $documentService = new DocumentService($this->getDoctrine()->getManager());
                $em = $this->getDoctrine()->getManager();
                $posted = $request->request->all();
                if (isset($posted['paiement'])) {
                    $ids = array();
                    foreach($posted['paiement'] as $item) {
                        $data = explode(';', $item);
                        
                        //Ajout
                        if (intval($data[1]) == 0) {
                            $vente = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($data[0]);
                            $moyenPaiement = $this->getDoctrine()->getRepository('AppBundle:OneMoyenPaiement')->find($data[4]);
                            $paiement = new OnePaiement();
                            $paiement->setCode($service->getNextCode());
                            $paiement->setOneVente($vente);
                            $paiement->setOneMoyenPaiement($moyenPaiement);
                            $paiement->setMontant(floatval($data[3]));
                            $paiement->setRefBanque($data[5]);
                            $paiement->setRetard(intval($data[6]));

                            if ($data[2] != '')
                                $paiement->setDateReception(\DateTime::createFromFormat('j/m/Y', $data[2]));
                            else
                                $paiement->setDateReception(new \DateTime('now'));

                            if ($data[7] != '') {
                                $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($data[7]);
                                $paiement->setBanqueCompte($banqueCompte);
                            }
                            
                            $em->persist($paiement);
                            $em->flush();
                            $ids[] = $paiement->getId();

                            //Ajout détail paiement par encaissement
                            if(intval($data[8]) > 0) {
                                $paiementDetail = new OnePaiementDetail();
                                $encaissement = $this->getDoctrine()->getRepository('AppBundle:OneEncaissement')->find($data[8]);
                                $paiementDetail->setOnePaiement($paiement);
                                $paiementDetail->setOneEncaissement($encaissement);
                                $em->persist($paiementDetail);
                                $em->flush();
                            }

                            //Ajout détail paiement par avoir
                            if(intval($data[9]) > 0) {
                                $paiementDetail = new OnePaiementDetail();
                                $avoir = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($data[9]);
                                $paiementDetail->setOnePaiement($paiement);
                                $paiementDetail->setOneAvoir($avoir);
                                $em->persist($paiementDetail);
                                $em->flush();
                            }
                            
                            //Mise à jour du statut de la facture
//                            if($service->checkFacturePaiement($vente, $venteService->getVenteDetails('facture')[$vente->getId()]['ttc'])) {
//                                $vente->setStatusFacture(1);
//                                $em->flush();
//                            }

                            if($service->checkFacturePaiement($vente, $venteService->getVenteDetailsByVente($vente)['ttc'])) {
                                $vente->setStatusFacture(1);
                                $em->flush();
                            }
                            
                            //Ajout d'un modèle de document standard
                            $documentService->addDocumentModele('paiement', $paiement);
                        }
                    }
                    $response = array('type' => 'success', 'action' => 'add', 'ids' => $ids);
                    return new JsonResponse($response);
                }
            } catch (Exception $ex) {
                $response = array('type' => 'error', 'action' => 'add');
                return new JsonResponse($response);
            }
            
        }
    }
}