<?php

/**
 * Created by Netbeans
 * Created on : 23 août 2017, 16:57:07
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneEncaissement;
use AppBundle\Entity\OneEncaissementDetail;
use One\VenteBundle\Service\EncaissementService;
use One\ProspectBundle\Service\FichierService;
use One\VenteBundle\Service\DocumentService;

class EncaissementController extends Controller
{
    /**
     * Liste des encaissements
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request) {
        if ($request->isMethod('GET')) {
            $service = new EncaissementService($this->getDoctrine()->getManager());
            $stat = $request->query->get('stat');
            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');
            $params = $this->getDoctrine()
                ->getRepository('AppBundle:OneParametre')
                ->find(1);
            $deposit = $this->getDoctrine()
                ->getRepository('AppBundle:OneEncaissement')
                ->findBy(array('status' => 1));
            $undeposit = $this->getDoctrine()
                ->getRepository('AppBundle:OneEncaissement')
                ->findBy(array('status' => 0));

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $exercice = $request->query->get('exercice');

            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);
            //fin lesexperts.biz

            /** @var OneEncaissement[] $encaissements */
            $encaissements = $this->getDoctrine()
                ->getRepository('AppBundle:OneEncaissement')
                ->getEncaissements($clientProspects, $exercice, $sort, $sortorder, $q, $period, $startperiod, $endperiod, $stat);

            return $this->render('OneVenteBundle:Encaissement:list.html.twig', array(
                'deposit' => $deposit,
                'undeposit' => $undeposit,
                'encaissements' => $encaissements,
                'stat' => $stat,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
                'encaissementAmounts' => $service->getEncaissementAmounts($clientProspects, $exercice),
            ));
        }
    }
    
    /**
     * Ajout d'une nouvel encaissement
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request) {
        if ($request->isMethod('GET')) {
            $parent = $request->query->get('parent');
            $parentid = (int)$request->query->get('parentid');

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $exercice = $request->query->get('exercice');

            $exercices = Boost::getExercices(6,1);

            //fin lesexperts.biz
            /** @var Tiers[] $clientProspects */
            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);

            $typeencaissements = $this->getDoctrine()
                ->getRepository('AppBundle:OneTypeEncaissement')
                ->getTypeEncaissements();

            $moyenpaiements = $this->getDoctrine()
                ->getRepository('AppBundle:OneMoyenPaiement')
                ->getMoyenPaiements();

            $comptes = $this->getDoctrine()
                ->getRepository('AppBundle:OneCompte')
                ->getComptes();

            $projets = $this->getDoctrine()
                ->getRepository('AppBundle:OneProjet')
                ->getProjets();

            $banques = $this->getDoctrine()
                ->getRepository('AppBundle:BanqueCompte')
                ->findBy(array('dossier' => $dossier));

            /** @var Pcc[] $pccs */
            $pccs = $this->getDoctrine()
                ->getRepository('AppBundle:Pcc')
                ->getPccByDossierLike($dossier, array('41'));



            return $this->render('OneVenteBundle:Encaissement:new.html.twig', array(
                'clientProspects' => $clientProspects,
                'typeencaissements' => $typeencaissements,
                'moyenpaiements' => $moyenpaiements,
                'comptes' => $comptes,
                'projets' => $projets,
                'parent' => $parent,
                'parentid' => $parentid,
                'banques' => $banques,
                'pccs' => $pccs,
                'exercice' => $exercice,
                'exercices' => $exercices
            ));
        }
    }
    
    public function editAction(Request $request, $id) {
        $encaissement = $this->getDoctrine()
            ->getRepository('AppBundle:OneEncaissement')
            ->find($id);
        /** @var OneEncaissementDetail[] $articles */
        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OneEncaissementDetail')
            ->findByoneEncaissement($encaissement);

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        $exercices = Boost::getExercices(6,1);
        //fin lesexperts.biz
        /** @var Tiers[] $clientProspects */
        $clientProspects = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->getClientProspects($dossier);
        $typeencaissements = $this->getDoctrine()
            ->getRepository('AppBundle:OneTypeEncaissement')
            ->getTypeEncaissements();
        $moyenpaiements = $this->getDoctrine()
            ->getRepository('AppBundle:OneMoyenPaiement')
            ->getMoyenPaiements();
        $comptes = $this->getDoctrine()
            ->getRepository('AppBundle:OneCompte')
            ->getComptes();
        $projets = $this->getDoctrine()
            ->getRepository('AppBundle:OneProjet')
            ->getProjets();
        $fichiers = $this->getDoctrine()
            ->getRepository('AppBundle:OneFichier')
            ->getFiles(unserialize($encaissement->getFichier()));

        $banques = $this->getDoctrine()
            ->getRepository('AppBundle:BanqueCompte')
            ->findBy(array('dossier' => $dossier));

        /** @var Pcc[] $pccs */
        $pccs = $this->getDoctrine()
            ->getRepository('AppBundle:Pcc')
            ->getPccByDossierLike($dossier, array('41'));


        return $this->render('OneVenteBundle:Encaissement:edit.html.twig', array(
            'encaissement' => $encaissement,
            'clientProspects' => $clientProspects,
            'typeencaissements' => $typeencaissements,
            'moyenpaiements' => $moyenpaiements,
            'comptes' => $comptes,
            'projets' => $projets,
            'articles' => $articles,
            'fichiers' => $fichiers,
            'banques' => $banques,
            'pccs' => $pccs,
            'exercices' => $exercices
        ));
    }
    
    /**
     * Visualisation d'un encaissement
     * @param type $id
     * @return Response
     */
    public function showAction(Request $request, $id) {
        $encaissement = $this->getDoctrine()
            ->getRepository('AppBundle:OneEncaissement')
            ->find($id);
        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:OneEncaissementDetail')
            ->findByOneEncaissement($encaissement);
        $modele = $this->getDoctrine()
            ->getRepository('AppBundle:OneDocumentModele')
            ->findOneByEncaissement($encaissement);

        $dossierId = $request->query->get('dossierId');
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($dossierId,  $this));

        $modeles = $this->getDoctrine()
            ->getRepository('AppBundle:OneModele')
            ->getModelesByDossier($dossier);

        $filename = $this->getParameter('one_documents_dir').'encaissement'.DIRECTORY_SEPARATOR.'Encaissement de vente-'.$encaissement->getCode().'.pdf';
        $params = $this->getDoctrine()
            ->getRepository('AppBundle:OneParametre')
            ->find(1);

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        return $this->render('OneVenteBundle:Encaissement:show.html.twig', array(
            'encaissement' => $encaissement,
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
     * Sauvegarde d'un encaissement
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $service = new EncaissementService($this->getDoctrine()->getManager());
            $fichierService = new FichierService($this->getDoctrine()->getManager());
            $documentService = new DocumentService($this->getDoctrine()->getManager());
            $posted = $request->request->all();

            $exercice = $posted['exercice-rattachement'];
            if($exercice === ''){
                $exercice = null;
            }
            
            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $encaissement = new OneEncaissement();
                    
                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);
                    $typeEncaissement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneTypeEncaissement')
                        ->find($posted['type-encaissement']);
                    $moyenPaiement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneMoyenPaiement')
                        ->find($posted['moyen-paiement']);

                    $banqueCompte = $this->getDoctrine()
                        ->getRepository('AppBundle:BanqueCompte')
                        ->find($posted['banque']);
                    
                    $encaissement->setCode($service->getNextCode());
                    $encaissement->setTiers($clientProspect);
                    $encaissement->setOneTypeEncaissement($typeEncaissement);
                    $encaissement->setOneMoyenPaiement($moyenPaiement);
                    $encaissement->setIdTransaction($posted['id-transaction']);
                    $encaissement->setNote($posted['note']);
                    $encaissement->setBanqueCompte($banqueCompte);
                    $encaissement->setCreeLe(new \DateTime('now'));
                    $encaissement->setExercice($exercice);
                            
                    if ($posted['date-encaissement'] !== '')
                        $encaissement->setDateEncaissement(\DateTime::createFromFormat('d/m/Y', $posted['date-encaissement']));
                    else
                        $encaissement->setDateEncaissement(new \DateTime('now'));
                    
//                    if (intval($posted['projet']) > 0) {
//                        $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($posted['projet']);
//                        $encaissement->setOneProjet($projet);
//                    }
                    
                    //Sauvegarde des fichiers
                    $filesID = array();
                    if (isset($posted['uploaded-files'])) {
                        foreach ($posted['uploaded-files'] as $file) {
                            $data = $fichierService->parseFile($file);
                            $filesID[] = $fichierService->saveData($data);
                        }
                    }
                    
                    //Suppression des fichiers uploadés
                    if (isset($posted['deleted-files'])) {
                        foreach ($posted['deleted-files'] as $file) {
                            unlink($this->getParameter('one_upload_dir').$file);
                        }
                    }
                    
                    $encaissement->setFichier(serialize($filesID));
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($encaissement);
                    $em->flush();
                    
                    //Enregistrement des détails
                    if ($posted['type-encaissement'] == 1) {
                        $detail = new OneEncaissementDetail();
//                        $compte = $this->getDoctrine()
//                            ->getRepository('AppBundle:OneCompte')->find(1);
//                        $detail->setOneCompte($compte);
                        $detail->setOneEncaissement($encaissement);
                        $detail->setMontant(str_replace(' ', '', $posted['montant']));
                        $dem = $this->getDoctrine()->getManager();
                        $dem->persist($detail);
                        $dem->flush();
                    } elseif ($posted['type-encaissement'] == 2) {
                        $detail = new OneEncaissementDetail();
                        //$encaissement = $this->getDoctrine()->getRepository('AppBundle:OneEncaissement')->find($encaissement->getId());
                        $detail->setOneEncaissement($encaissement);
                        $detail->setMontant(str_replace(' ', '', $posted['montant']));
                        $dem = $this->getDoctrine()->getManager();
                        $dem->persist($detail);
                        $dem->flush();
                    } else {
                        if (isset($posted['articles'])) {
                            foreach ($posted['articles'] as $article) {
                                $detail = new OneEncaissementDetail();
                                $data = $service->parseData($article);

                                $pcc = $this->getDoctrine()
                                    ->getRepository('AppBundle:Pcc')
                                    ->find($data['compte-id']);
                                $detail->setPcc($pcc);
                                $detail->setOneEncaissement($encaissement);
                                $detail->setMontant($data['montant']);
                                $dem = $this->getDoctrine()->getManager();
                                $dem->persist($detail);
                                $dem->flush();
                            }
                        }
                    }
                    
                    //Ajout d'un modèle de document standard
                    $documentService->addDocumentModele('encaissement', $encaissement);
                    
                    $response = array('type' => 'success', 'action' => 'add', 'id' => $encaissement->getId());
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            } else {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $encaissement = $em
                        ->getRepository('AppBundle:OneEncaissement')
                        ->find($posted['id']);
                    
                    //Récupération des tables liées
                    $clientProspect = $this->getDoctrine()
                        ->getRepository('AppBundle:Tiers')
                        ->find($posted['client-prospect']);

                    $typeEncaissement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneTypeEncaissement')
                        ->find($posted['type-encaissement']);

                    $moyenPaiement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneMoyenPaiement')
                        ->find($posted['moyen-paiement']);

                    $banqueCompte = $this->getDoctrine()
                        ->getRepository('AppBundle:BanqueCompte')
                        ->find($posted['banque']);

                    $encaissement->setTiers($clientProspect);
                    $encaissement->setOneTypeEncaissement($typeEncaissement);
                    $encaissement->setOneMoyenPaiement($moyenPaiement);
                    $encaissement->setIdTransaction($posted['id-transaction']);
                    $encaissement->setNote($posted['note']);
                    $encaissement->setBanqueCompte($banqueCompte);
                    $encaissement->setModifieLe(new \DateTime('now'));
                    $encaissement->setExercice($exercice);
                            
                    if ($posted['date-encaissement'] !== '')
                        $encaissement->setDateEncaissement(\DateTime::createFromFormat('d/m/Y', $posted['date-encaissement']));
                    else
                        $encaissement->setDateEncaissement(new \DateTime('now'));
                    
//                    if (intval($posted['projet']) > 0) {
//                        $projet = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->find($posted['projet']);
//                        $encaissement->setOneProjet($projet);
//                    }
                    
                    //Sauvegarde des fichiers
                    $filesID = array();
                    if (isset($posted['uploaded-files'])) {
                        foreach ($posted['uploaded-files'] as $file) {
                            $data = $fichierService->parseFile($file);
                            $filesID[] = $fichierService->saveData($data);
                        }
                    }
                    
                    //Suppression des fichiers uploadés
                    if (isset($posted['deleted-files'])) {
                        $rem = $this->getDoctrine()->getManager();
                        foreach ($posted['deleted-files'] as $file) {
                            $fichier = $this->getDoctrine()
                                ->getRepository('AppBundle:OneFichier')
                                ->findOneByNom($file);
                            $rem->remove($fichier);
                            unlink($this->getParameter('one_upload_dir').$file);
                        }
                        $rem->flush();
                    }
                    
                    $encaissement->setFichier(serialize($filesID));
                    
                    $em->flush();
                    
                    //Enregistrement des détails
                    if ($posted['type-encaissement'] == 1) {
                        $dem = $this->getDoctrine()->getManager();
                        /** @var OneEncaissementDetail $detail */
                        $detail = $dem->getRepository('AppBundle:OneEncaissementDetail')
                            ->findOneByOneEncaissement($encaissement);
//                        $compte = $this->getDoctrine()->getRepository('AppBundle:OneCompte')->find(1);
//                        $detail->setOneCompte($compte);
                        $detail->setOneEncaissement($encaissement);
                        $detail->setMontant(str_replace(' ', '', $posted['montant']));
                        $dem->flush();
                    } elseif ($posted['type-encaissement'] == 2) {
                        $dem = $this->getDoctrine()->getManager();
                        /** @var OneEncaissementDetail $detail */
                        $detail = $dem->getRepository('AppBundle:OneEncaissementDetail')
                            ->findOneByOneEncaissement($encaissement);
                        $detail->setOneEncaissement($encaissement);
                        $detail->setMontant(str_replace(' ', '', $posted['montant']));
                        $dem->flush();
                    } else {
                        if (isset($posted['articles'])) {
                            foreach ($posted['articles'] as $article) {
                                $data = $service->parseData($article);
                                if ((int)$data['id'] == 0) {
                                    $detail = new OneEncaissementDetail();
//                                    $compte = $this->getDoctrine()->getRepository('AppBundle:OneCompte')->find($data['compte-id']);
//                                    $detail->setOneCompte($compte);
                                    $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($data['compte-id']);
                                    $detail->setPcc($pcc);

                                    $detail->setOneEncaissement($encaissement);
                                    $detail->setMontant($data['montant']);
                                    $dem = $this->getDoctrine()->getManager();
                                    $dem->persist($detail);
                                    $dem->flush();
                                } else {
                                    $dem = $this->getDoctrine()->getManager();
                                    $detail = $dem->getRepository('AppBundle:OneEncaissementDetail')->find($data['id']);
//                                    $compte = $this->getDoctrine()->getRepository('AppBundle:OneCompte')->find($data['compte-id']);
//                                    $detail->setOneCompte($compte);
                                    $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($data['compte-id']);
                                    $detail->setPcc($pcc);

                                    $detail->setOneEncaissement($encaissement);
                                    $detail->setMontant($data['montant']);
                                    $dem->flush();
                                }
                            }
                        }
                    }
                    
                    $response = array('type' => 'success', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                }
            }
        }
    }
    
    /**
     * Suppression d'un encaissement
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $encaissement = $this->getDoctrine()->getRepository('AppBundle:OneEncaissement')->find($id);
            $em->remove($encaissement);
            
            //Suppression des détails correspondants
            $details = $this->getDoctrine()->getRepository('AppBundle:OneEncaissementDetail')->findByOneEncaissement($encaissement);
            foreach ($details as $detail) {
                $em->remove($detail);
            }
            
            //Suppression des fichiers correspondants
            $filesID = unserialize($encaissement->getFichier());
            if (count($filesID) > 0) {
                foreach ($filesID as $fileID) {
                    $file = $this->getDoctrine()->getRepository('AppBundle:OneFichier')->find($fileID);
                    unlink($this->getParameter('one_upload_dir').$file->getNom());
                    $em->remove($file);
                }
            }
            
            //Suppression des personnalisation de document
            $document = $this->getDoctrine()->getRepository('AppBundle:OneDocumentModele')->findOneByEncaissement($encaissement);
            if ($document)
                $em->remove($document);
            
            $em->flush();
            
            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
    }
    
    /**
     * Liste encaissements d'un client dans un modal
     * @param Request $request
     * @return Response
     */
    public function listmodalAction(Request $request) {
        if ($request->isMethod('GET')) {
            $facid = (int)$request->query->get('facid');
            $clientid = (int)$request->query->get('clientid');
            $excludeids = $request->query->get('excludeids');

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $exercice = $request->query->get('exercice');

            $clientProspects = $this->getDoctrine()
                ->getRepository('AppBundle:Tiers')
                ->getClientProspects($dossier);
            //fin lesexperts.biz

            $client = null;

            if ($clientid > 0) {
                $client = $this->getDoctrine()
                    ->getRepository('AppBundle:Tiers')
                    ->find($clientid);
            } elseif ($facid > 0) {
                $facture = $this->getDoctrine()
                    ->getRepository('AppBundle:OneVente')
                    ->find($facid);
                $client = $facture->getTiers();
            }
            
            $service = new EncaissementService($this->getDoctrine()->getManager());
            $params = $this->getDoctrine()
                ->getRepository('AppBundle:OneParametre')
                ->find(1);

            $encaissements = $this->getDoctrine()
                ->getRepository('AppBundle:OneEncaissement')
                ->getEncaissementsPaiement($client, $excludeids);
            return $this->render('OneVenteBundle:Encaissement:listmodal.html.twig', array(
                'facid' => $facid,
                'encaissements' => $encaissements,
                'encaissementAmounts' => $service->getEncaissementAmounts($clientProspects, $exercice),
                'params' => $params,
            ));
        }
    }
}