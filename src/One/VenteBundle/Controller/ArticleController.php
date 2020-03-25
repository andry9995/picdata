<?php

/**
 * Created by Netbeans
 * Created on : 10 juil. 2017, 09:56:01
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\TvaTaux;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneArticle;
use One\VenteBundle\Service\ArticleService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ArticleController extends Controller
{
    /**
     * Liste
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request) {
        if ($request->isMethod('GET')) {
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
            //fin lesexperts.biz

            if(null === $dossier){
                return new Response('');
            }

            $articles = $this->getDoctrine()->getRepository('AppBundle:OneArticle')->getArticles($dossier, $sort, $sortorder, $q, $period, $startperiod, $endperiod);
            return $this->render('OneVenteBundle:Article:list.html.twig', array(
                'articles' => $articles,
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
    
    public function newAction(Request $request) {
        $units = $this->getDoctrine()->getRepository('AppBundle:OneUniteArticle')->getUnits();
        $families = $this->getDoctrine()->getRepository('AppBundle:OneFamilleArticle')->getFamilies();
        $taxes = $this->getDoctrine()->getRepository('AppBundle:OneTva')->getTva();

        $post = $request->request;

        $dossierId = Boost::deboost($post->get('dossierId'), $this);

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);


        /** @var TvaTaux[] $tvaTaux */
        $tvaTaux = $this->getDoctrine()
            ->getRepository('AppBundle:TvaTaux')
            ->findBy(array('actif' => 1), array('taux' => 'ASC'));

        /** @var Pcc[] $pccAchats */
        $pccAchats = $this->getDoctrine()
            ->getRepository('AppBundle:Pcc')
            ->getPccByDossierLike($dossier, array('60'));


        /** @var Pcc[] $pccVentes */
        $pccVentes = $this->getDoctrine()
            ->getRepository('AppBundle:Pcc')
            ->getPccByDossierLike($dossier, array('70'));


        return $this->render('OneVenteBundle:Article:new.html.twig', array(
            'units' => $units,
            'families' => $families,
            'taxes' => $taxes,
            'pccVentes' => $pccVentes,
            'pccAchats' => $pccAchats,
            'tvaTauxs' => $tvaTaux
        ));
    }
    
    public function editAction($id) {
        /** @var OneArticle $article */
        $article = $this->getDoctrine()->getrepository('AppBundle:OneArticle')->find($id);
        $units = $this->getDoctrine()->getRepository('AppBundle:OneUniteArticle')->getUnits();
        $families = $this->getDoctrine()->getRepository('AppBundle:OneFamilleArticle')->getFamilies();
        $taxes = $this->getDoctrine()->getRepository('AppBundle:OneTva')->getTva();

        /** @var TvaTaux[] $tvaTaux */
        $tvaTaux = $this->getDoctrine()
            ->getRepository('AppBundle:TvaTaux')
            ->findBy(array('actif' => 1), array('taux' => 'ASC'));

        /** @var Pcc[] $pccAchats */
        $pccAchats = $this->getDoctrine()
            ->getRepository('AppBundle:Pcc')
            ->getPccByDossierLike($article->getDossier(), array('60'));


        /** @var Pcc[] $pccVentes */
        $pccVentes = $this->getDoctrine()
            ->getRepository('AppBundle:Pcc')
            ->getPccByDossierLike($article->getDossier(), array('70'));

        return $this->render('OneVenteBundle:Article:edit.html.twig', array(
            'article' => $article,
            'units' => $units,
            'families' => $families,
            'taxes' => $taxes,
            'tvaTauxs' => $tvaTaux,
            'pccAchats' => $pccAchats,
            'pccVentes' => $pccVentes
        ));
    }
    
    public function saveAction(Request $request, $dossier) {
        if ($request->isMethod('POST')) {
            $service = new ArticleService($this->getDoctrine()->getManager());
            $posted = $request->request->all();

            //debut lesexperts.biz
//            $dossierId = Boost::deboost($posted['id-dossier'], $this);
            $dossierId = Boost::deboost($dossier, $this);
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);
            //fin lesexperts.biz

            if(null === $dossier){
                $response = array('type' => 'error', 'action' => 'add');
                return new JsonResponse($response);
            }

            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $article = new OneArticle();
                    $unit = $this->getDoctrine()->getRepository('AppBundle:OneUniteArticle')->find($posted['unite-article']);
                    $family = $this->getDoctrine()->getRepository('AppBundle:OneFamilleArticle')->find($posted['famille-article']);
                    $tva = $this->getDoctrine()->getRepository('AppBundle:OneTva')->find($posted['tva-article']);


                    $pccAchat =  null;

                    if(isset($posted['compte-achat'])) {
                        $pccAchat = $this->getDoctrine()
                            ->getRepository('AppBundle:Pcc')
                            ->find($posted['compte-achat']);
                    }

                    $pccVente = null;

                    if(isset($posted['compte-vente'])) {
                        $pccVente = $this->getDoctrine()
                            ->getRepository('AppBundle:Pcc')
                            ->find($posted['compte-vente']);
                    }

                    $tvaAchat = $this->getDoctrine()
                        ->getRepository('AppBundle:TvaTaux')
                        ->find($posted['taxe-achat']);

                    $tvaVente = $this->getDoctrine()
                        ->getRepository('AppBundle:TvaTaux')
                        ->find($posted['taxe-vente']);

                    $article->setNom($posted['nom']);
                    $article->setOneUniteArticle($unit);
                    $article->setPrixAchat($posted['prix-achat']);
                    $article->setPrixVente($posted['prix-vente']);
                    $article->setDescription($posted['description']);
                    $article->setOneFamilleArticle($family);
                    $article->setTvaTaux($tva);

                    $article->setTvaTauxAchat($tvaAchat);
                    $article->setTvaTauxVente($tvaVente);
                    $article->setPccAchat($pccAchat);
                    $article->setPccVente($pccVente);

                    if ($posted['code'] == '')
                        $article->setCode($service->getNextCode($dossier));
                    else
                        $article->setCode($service->getNextCustomCode($posted['code']), $dossier);

                    $article->setDossier($dossier);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($article);
                    $em->flush();

                    $response = array('type' => 'success', 'action' => 'add');
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
                }
            }

            
            //Edition
            else {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $article = $em->getRepository('AppBundle:OneArticle')->find($posted['id']);
                    $unit = $this->getDoctrine()->getRepository('AppBundle:OneUniteArticle')->find($posted['unite-article']);
                    $family = $this->getDoctrine()->getRepository('AppBundle:OneFamilleArticle')->find($posted['famille-article']);
                    $tva = $this->getDoctrine()->getRepository('AppBundle:OneTva')->find($posted['tva-article']);


                    $pccAchat = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->find($posted['compte-achat']);

                    $pccVente = $this->getDoctrine()
                        ->getRepository('AppBundle:Pcc')
                        ->find($posted['compte-vente']);

                    $tvaAchat = $this->getDoctrine()
                        ->getRepository('AppBundle:TvaTaux')
                        ->find($posted['taxe-achat']);

                    $tvaVente = $this->getDoctrine()
                        ->getRepository('AppBundle:TvaTaux')
                        ->find($posted['taxe-vente']);
                    
                    $article->setNom($posted['nom']);
                    $article->setOneUniteArticle($unit);
                    $article->setPrixAchat($posted['prix-achat']);
                    $article->setPrixVente($posted['prix-vente']);
                    $article->setDescription($posted['description']);
                    $article->setOneFamilleArticle($family);
                    $article->setTvaTaux($tva);


                    $article->setTvaTauxAchat($tvaAchat);
                    $article->setTvaTauxVente($tvaVente);
                    $article->setPccAchat($pccAchat);
                    $article->setPccVente($pccVente);

                    if ($posted['code'] == '') {
                        $article->setCode($service->getNextCode($dossier));
                    } elseif($article->getCode() != $posted['code']) {
                        $article->setCode($service->getNextCustomCode($posted['code']), $dossier);
                    }
                    
                    $em->flush();
                    
                    $response = array('type' => 'success', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                } catch (\Exception $ex) {
                    $response = array('type' => 'error', 'action' => 'edit', 'id' => $posted['id']);
                    return new JsonResponse($response);
                }
            }
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }
    
    /**
     * Suppresion d'un artile
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $article = $this->getDoctrine()->getRepository('AppBundle:OneArticle')->find($id);
            
            $em->remove($article);
            $em->flush();
            
            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
        
    }
    
    /**
     * Liste article dans modal pour opportunité
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listforopportuniteAction(Request $request) {

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->request->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);
        //fin lesexperts.biz

        $articles = $this->getDoctrine()->getRepository('AppBundle:OneArticle')->getArticles($dossier);
        return $this->render('OneVenteBundle:Article:listforopportunite.html.twig', array(
            'articles' => $articles,
        ));
    }
    
    /**
     * Liste article dans modal pour devis/facture/commande/avoir
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listforventeAction(Request $request) {

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->request->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);
        //fin lesexperts.biz

        $articles = $this->getDoctrine()->getRepository('AppBundle:OneArticle')->getArticles($dossier);
        return $this->render('OneVenteBundle:Article:listforvente.html.twig', array(
            'articles' => $articles,
        ));
    }
    
    /**
     * Création d'un article depuis un modal
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newinmodalAction(Request $request, $parent) {
        $units = $this->getDoctrine()->getRepository('AppBundle:OneUniteArticle')->getUnits();
        $families = $this->getDoctrine()->getRepository('AppBundle:OneFamilleArticle')->getFamilies();
        $taxes = $this->getDoctrine()->getRepository('AppBundle:OneTva')->getTva();

        $post = $request->request;

        $dossierId = Boost::deboost($post->get('dossierId'), $this);

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);

        /** @var TvaTaux[] $tvaTaux */
        $tvaTaux = $this->getDoctrine()
            ->getRepository('AppBundle:TvaTaux')
            ->findBy(array('actif' => 1), array('taux' => 'ASC'));

        /** @var Pcc[] $pccAchats */
        $pccAchats = $this->getDoctrine()
            ->getRepository('AppBundle:Pcc')
            ->getPccByDossierLike($dossier, array('60'));


        /** @var Pcc[] $pccVentes */
        $pccVentes = $this->getDoctrine()
            ->getRepository('AppBundle:Pcc')
            ->getPccByDossierLike($dossier, array('70'));

        return $this->render('OneVenteBundle:Article:newinmodal.html.twig', array(
            'units' => $units,
            'families' => $families,
            'taxes' => $taxes,
            'pccVentes' => $pccVentes,
            'pccAchats' => $pccAchats,
            'tvaTauxs' => $tvaTaux,
            'parent' => $parent
        ));
    }
    
    /**
     * Ajout d'un article dans devis/facture/commande/avoir
     * @param Request $request
     */
    public function addinventeAction(Request $request) {
        if ($request->isMethod('POST')) {
            $articles = array();
            $taxes = $this->getDoctrine()->getRepository('AppBundle:OneTva')->getTva();
            $items = $request->request->get('articles');
            foreach($items as $value) {
                $item = array();
                $data = explode(';', $value);
                $item['id'] = $data[0];
                $item['code'] = $data[1];
                $item['name'] = $data[2];
                $item['unit'] = $data[3];
                $item['price'] = $data[4];
                $item['tva'] = $data[5];
                $articles[] = $item;
            }
            return $this->render('OneVenteBundle:Article:addinvente.html.twig', array(
                'articles' => $articles,
                'taxes' => $taxes,
            ));
        }
    }
}