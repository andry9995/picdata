<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 03/04/2018
 * Time: 16:50
 */

namespace One\AchatBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\OneAchat;
use AppBundle\Entity\OneArticleAchat;
use AppBundle\Entity\OneContactFournisseur;
use AppBundle\Entity\OneDepenseAchat;
use AppBundle\Entity\OneFournisseur;
use AppBundle\Entity\OneReglement;
use AppBundle\Entity\Pcc;
use One\AchatBundle\OneAchatBundle;
use One\AchatBundle\Service\AchatService;
use One\AchatBundle\Service\DepenseService;
use One\ProspectBundle\Service\FichierService;
use One\VenteBundle\Service\ArticleService;
use One\VenteBundle\Service\DocumentService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AchatController extends Controller
{
    public function indexAction(){
        return $this->render('OneAchatBundle:Achat:index.html.twig');
    }



    public function listAction(Request $request) {
        if ($request->isMethod('GET')) {

            $service = new AchatService($this->getDoctrine()->getManager());
            $stat = $request->query->get('stat');
            $q = $request->query->get('q');
            $sort = $request->query->get('sort');
            $sortorder = $request->query->get('sortorder');
            $period = $request->query->get('period');
            $startperiod = $request->query->get('startperiod');
            $endperiod = $request->query->get('endperiod');
            $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);


            if(null === $dossier){
                return new Response('');
            }

            $fournisseurs = $this->getDoctrine()
                ->getRepository('AppBundle:OneFournisseur')
                ->getAccounts($dossier);

            $imputations = array();
//            $imputations = $this->getDoctrine()
//                ->getRepository('AppBundle:ImputationControle')
//                ->getFactureClientsByDossier($dossier, $q, $period, $startperiod, $endperiod );
            //fin lesexperts.biz


            $paid = $this->getDoctrine()->getRepository('AppBundle:OneAchat')->getAchatByStatus($fournisseurs,'facture', 'paid');
            $unpaid = $this->getDoctrine()->getRepository('AppBundle:OneAchat')->getAchatByStatus($fournisseurs,'facture', 'unpaid');
            /** @var OneAchat[] $factures */
            $factures = $this->getDoctrine()->getRepository('AppBundle:OneAchat')->getAchats($fournisseurs,'facture', $sort, $sortorder, $q, $period, $startperiod, $endperiod, $stat);
            return $this->render('OneAchatBundle:Achat:list.html.twig', array(
                'paid' => $paid,
                'unpaid' => $unpaid,
                'factures' => $factures,
                'stat' => $stat,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
                'factureDetails' => $service->getAchatDetails($fournisseurs, 'facture'),
                'imputations' => $imputations
            ));
        }
    }



    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id, $one) {
        if ($request->isMethod('GET')) {
            $parent = $request->query->get('parent');
            $parentid = (int)($request->query->get('parentid'));

            //debut lesexperts.biz
            $dossierId = Boost::deboost($request->query->get('dossierId'), $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);
            //fin lesexperts.biz

            $achat = $this->getDoctrine()
                ->getRepository('AppBundle:OneAchat')
                ->find($id);

            /** @var OneFournisseur[] $fournisseurs */
            $fournisseurs = $this->getDoctrine()->getRepository('AppBundle:OneFournisseur')->getAccounts($dossier);
            /** @var OneReglement[] $reglements */
            $reglements = $this->getDoctrine()->getRepository('AppBundle:OneReglement')->getReglements();
            $projets = $this->getDoctrine()->getRepository('AppBundle:OneProjet')->getProjets();

            /** @var OneContactFournisseur[] $contacts */
            $contacts = array();


            /** @var OneArticleAchat[] $articles */
            $articles = $this->getDoctrine()
                ->getRepository('AppBundle:OneArticleAchat')
                ->getArticlesAchat($id);

            /** @var OneDepenseAchat[] $depenses */
            $depenses = $this->getDoctrine()
                ->getRepository('AppBundle:OneDepenseAchat')
                ->findBy(array('achat' =>$achat ));

            $service = new AchatService($this->getDoctrine()->getManager());

            $taxes = $this->getDoctrine()->getRepository('AppBundle:TvaTaux')
                ->findBy(array('actif'=>1),array('taux'=>'ASC'));

            $achatDetails = null;

            if($id !== -1)
                $achatDetails = $service->getAchatDetails($fournisseurs, 'facture')[$id];


            /** @var Pcc[] $pccs */
            $pccs = $this->getDoctrine()
                ->getRepository('AppBundle:Pcc')
                ->getPccByDossierLike($dossier, array('4'));

            if ($parent === 'fournisseur') {


                $contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactFournisseur')->getContacts($parentid);
                return $this->render('OneAchatBundle:Achat:edit.html.twig', array(
                    'fournisseurs' => $fournisseurs,
                    'reglements' => $reglements,
                    'parent' => $parent,
                    'parentid' => $parentid,
                    'contacts' => $contacts,
                    'projets' => $projets,
                    'achat' => $achat,
                    'articles' => $articles,
                    'depenses' => $depenses,
                    'achatDetails' => $achatDetails,
                    'taxes' => $taxes,
                    'pccs' => $pccs
                ));
            }

            //Aucun parent
            return $this->render('OneAchatBundle:Achat:edit.html.twig', array(
                'fournisseurs' => $fournisseurs,
                'reglements' => $reglements,
                'parent' => $parent,
                'parentid' => $parentid,
                'contacts' => $contacts,
                'projets' => $projets,
                'achat' => $achat,
                'articles' => $articles,
                'depenses' => $depenses,
                'achatDetails' => $achatDetails,
                'taxes' => $taxes,
                'pccs' => $pccs
            ));
        }

        throw new AccessDeniedHttpException('Accès refuse');
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function addressAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('GET')) {
                $type = $request->query->get('type');
                $id = (int)($request->query->get('id'));
                $service = new AchatService($this->getDoctrine()->getEntityManager());
                $address = $service->getAddress($type, $id);
                return new Response($address);
            }
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request) {
        if ($request->getMethod() === 'POST') {

            $em = $this->getDoctrine()->getManager();

            $service = new AchatService($em);
            $articleService = new ArticleService($em);
            $fichierService = new FichierService($em);
            $documentService = new DocumentService($em);
            $depenseService = new DepenseService($em);

            $posted = $request->request->all();

            //debut lesexperts.biz
            $dossierId = Boost::deboost($posted['id-dossier'], $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);
            //fin lesexperts.biz

            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $achat = new OneAchat();

                    //Récupération des tables liées
                    $fournisseur = $this->getDoctrine()
                        ->getRepository('AppBundle:OneFournisseur')
                        ->find($posted['fournisseur']);

                    $reglement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneReglement')
                        ->find($posted['reglement']);

                    $achat->setType(2);
                    $achat->setOneFournisseur($fournisseur);
                    $achat->setOneReglement($reglement);
                    $achat->setStatusFacture($posted['status']);
                    $achat->setRemise($posted['remise-ht']);
                    $achat->setNote($posted['note']);
                    $achat->setCode($service->getNextCodeAchat('facture'));
                    $achat->setCreeLe(new \DateTime('now'));

                    if ($posted['date-facture'] != '')
                        $achat->setDateFacture(\DateTime::createFromFormat('d/m/Y', $posted['date-facture']));
                    else
                        $achat->setDateFacture(new \DateTime('now'));

                    if ((int)$posted['contact-fournisseur'] > 0) {
                        $contactFournisseur = $this->getDoctrine()
                            ->getRepository('AppBundle:OneContactFournisseur')
                            ->find($posted['contact-fournisseur']);
                        $achat->setContact($contactFournisseur);
                    }

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

                    $achat->setFichier(serialize($filesID));

                    $em->persist($achat);
                    $em->flush();

//                    Sauvegarde des articles
                    if (isset($posted['articles'])) {
                        foreach ($posted['articles'] as $article) {
                            $data = $articleService->parseArticleData($article);
                            $data['achat-id'] = (int)$achat->getId();
                            $articleService->saveArticleAchat($data);
                        }
                    }

                    if(isset($posted['depenses'])){
                        foreach ($posted['depenses'] as $depense){
                            $data = $depenseService->parseDepenseData($depense);
                            $data['achat-id'] = (int)$achat->getId();
                            $depenseService->saveDepenseAchat($data);
                        }
                    }

                    //Ajout d'un modèle de document standard
                    $documentService->addDocumentModele('achat', $achat);

                    $pids = array();

                    if (!isset($pids)) $pids = array();
                    $response = array('type' => 'success', 'action' => 'add', 'id' => $achat->getId(), 'pids' => $pids);
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            }

            //Edition
            else {
                try {
                    $em = $this->getDoctrine()->getEntityManager();

                    $achat = $this->getDoctrine()
                        ->getRepository('AppBundle:OneAchat')
                        ->find($posted['id']);

                    //Récupération des tables liées
                    $fournisseur = $this->getDoctrine()
                        ->getRepository('AppBundle:OneFournisseur')
                        ->find($posted['fournisseur']);

                    $reglement = $this->getDoctrine()
                        ->getRepository('AppBundle:OneReglement')
                        ->find($posted['reglement']);

                    $achat->setType(2);
                    $achat->setOneFournisseur($fournisseur);
                    $achat->setOneReglement($reglement);
                    $achat->setStatusFacture($posted['status']);
                    $achat->setRemise($posted['remise-ht']);
                    $achat->setNote($posted['note']);
                    $achat->setModifieLe(new \DateTime('now'));

                    if ($posted['date-facture'] != '')
                        $achat->setDateFacture(\DateTime::createFromFormat('j/m/Y', $posted['date-facture']));
                    else
                        $achat->setDateFacture(new \DateTime('now'));

                    if ((int)$posted['contact-fournisseur'] > 0) {
                        $contactFournisseur = $this->getDoctrine()
                            ->getRepository('AppBundle:OneContactFournisseur')
                            ->find($posted['contact-fournisseur']);
                        $achat->setContact($contactFournisseur);
                    }

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
                        $fem = $this->getDoctrine()->getManager();
                        foreach ($posted['deleted-files'] as $file) {
                            $fichier = $this->getDoctrine()->getRepository('AppBundle:OneFichier')->findOneByNom($file);
                            $fem->remove($fichier);
                            unlink($this->getParameter('one_upload_dir').$file);
                        }
                        $fem->flush();
                    }

                    $achat->setFichier(serialize($filesID));

                    //Sauvegarde des articles
                    if (isset($posted['articles'])) {
                        foreach ($posted['articles'] as $article) {
                            $data = $articleService->parseArticleData($article);
                            $data['achat-id'] = (int)$achat->getId();
                            $articleService->saveArticleAchat($data);
                        }
                    }

                    //Suppression des articles supprimés
                    if (isset($posted['deleted-articles'])) {
                        $aem = $this->getDoctrine()->getManager();
                        foreach ($posted['deleted-articles'] as $artid) {
                            $article = $this->getDoctrine()
                                ->getRepository('AppBundle:OneArticleAchat')
                                ->find($artid);
                            $aem->remove($article);
                        }
                        $aem->flush();
                    }

                    //Sauvegarde des articles
                    if (isset($posted['depenses'])) {
                        foreach ($posted['depenses'] as $depense) {
                            $data = $depenseService->parseDepenseData($depense);
                            $data['achat-id'] = (int)$achat->getId();
                            $depenseService->saveDepenseAchat($data);
                        }
                    }

                    //Suppression des articles supprimés
                    if (isset($posted['deleted-depenses'])) {
                        $dem = $this->getDoctrine()->getManager();
                        foreach ($posted['deleted-depenses'] as $depid) {
                            $depense = $this->getDoctrine()
                                ->getRepository('AppBundle:OneDepenseAchat')
                                ->find($depid);
                            $dem->remove($depense);
                        }
                        $dem->flush();
                    }


                    $em->flush();

                    if (!isset($pids)) $pids = array();
                    $response = array('type' => 'success', 'action' => 'edit', 'id' => $posted['id'], 'pids' => $pids);
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                }
            }
        }
        throw new AccessDeniedException('Accès refusé');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $achat = $this->getDoctrine()
                ->getRepository('AppBundle:OneAchat')
                ->find($id);

            $em->remove($achat);

            //Suppression des articles correspondants
            $articles = $this->getDoctrine()
                ->getRepository('AppBundle:OneArticleAchat')
                ->findBy(array('achat' => $achat));
            foreach ($articles as $article) {
                $em->remove($article);
            }

            //Suppression des fichiers correspondants
            $filesID = unserialize($achat->getFichier());
            if (count($filesID) > 0) {
                foreach ($filesID as $fileID) {
                    $file = $this->getDoctrine()->getRepository('AppBundle:OneFichier')->find($fileID);
                    unlink($this->getParameter('one_upload_dir').$file->getNom());
                    $em->remove($file);
                }
            }


            //Suppression des personnalisation de document
            $documents = $this->getDoctrine()->getRepository('AppBundle:OneDocumentModele')
                ->findBy(array('achat' => $achat));

            if(count($documents) > 0) {
                $em->remove($documents[0]);
            }


            $em->flush();

            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
    }


}