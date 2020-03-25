<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 09/05/2018
 * Time: 16:54
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
use One\AchatBundle\Service\AchatService;
use One\ProspectBundle\Service\FichierService;
use One\VenteBundle\Service\ArticleService;
use One\VenteBundle\Service\DocumentService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommandeController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
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
            //fin lesexperts.biz


            $uninvoiced = $this->getDoctrine()->getRepository('AppBundle:OneAchat')->getAchatByStatus($fournisseurs,'commande', 'uninvoiced');
            $invoiced = $this->getDoctrine()->getRepository('AppBundle:OneAchat')->getAchatByStatus($fournisseurs,'commande', 'invoiced');
            $unshipped = $this->getDoctrine()->getRepository('AppBundle:OneAchat')->getAchatByStatus($fournisseurs, 'commande', 'unshipped');
            $shipped = $this->getDoctrine()->getRepository('AppBundle:OneAchat')->getAchatByStatus($fournisseurs, 'commande', 'shipped');
            /** @var OneAchat[] $commandes */
            $commandes = $this->getDoctrine()->getRepository('AppBundle:OneAchat')->getAchats($fournisseurs, 'commande', $sort, $sortorder, $q, $period, $startperiod, $endperiod, $stat);
            return $this->render('OneAchatBundle:Commande:list.html.twig', array(
                'uninvoiced' => $uninvoiced,
                'invoiced' => $invoiced,
                'unshipped' => $unshipped,
                'shipped' => $shipped,
                'commandes' => $commandes,
                'stat' => $stat,
                'q' => $q,
                'sort' => $sort,
                'sortorder' => $sortorder,
                'period' => $period,
                'startperiod' => $startperiod,
                'endperiod' => $endperiod,
                'params' => $params,
                'factureDetails' => $service->getAchatDetails($fournisseurs,'commande'),
            ));
        }

        throw new AccessDeniedException('Accès refusé');
    }


    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editAction(Request $request, $id) {
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

            $service = new AchatService($this->getDoctrine()->getManager());

            $taxes = $this->getDoctrine()->getRepository('AppBundle:TvaTaux')
                ->findBy(array('actif'=>1),array('taux'=>'ASC'));

            $achatDetails = null;

            if($id !== -1)
                $achatDetails = $service->getAchatDetails($fournisseurs, 'commande')[$id];

            //Parent: ClientProspect
            if ($parent === 'fournisseur') {


                $contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactFournisseur')->getContacts($parentid);
                return $this->render('OneAchatBundle:Commande:edit.html.twig', array(
                    'fournisseurs' => $fournisseurs,
                    'reglements' => $reglements,
                    'parent' => $parent,
                    'parentid' => $parentid,
                    'contacts' => $contacts,
                    'projets' => $projets,
                    'achat' => $achat,
                    'articles' => $articles,
                    'achatDetails' => $achatDetails,
                    'taxes' => $taxes
                ));
            }

            //Aucun parent
            return $this->render('OneAchatBundle:Commande:edit.html.twig', array(
                'fournisseurs' => $fournisseurs,
                'reglements' => $reglements,
                'parent' => $parent,
                'parentid' => $parentid,
                'contacts' => $contacts,
                'projets' => $projets,
                'achat' => $achat,
                'articles' => $articles,
                'achatDetails' => $achatDetails,
                'taxes' => $taxes
            ));
        }

        throw new AccessDeniedException('Accès refuse');
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

                    $achat->setType(1);
                    $achat->setOneFournisseur($fournisseur);
                    $achat->setOneReglement($reglement);
                    $achat->setStatusFacture($posted['status']);
                    $achat->setRemise($posted['remise-ht']);
                    $achat->setNote($posted['note']);
                    $achat->setCode($service->getNextCodeAchat('commande'));
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

                    //Ajout d'un modèle de document standard
                    $documentService->addDocumentModele('commande', $achat);

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

                    $achat->setType(1);
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

}