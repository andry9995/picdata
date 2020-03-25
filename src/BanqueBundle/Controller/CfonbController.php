<?php
/**
 * Created by PhpStorm.
 * User: Dinoh
 * Date: 05/04/2019
 * Time: 08:35
 */
namespace BanqueBundle\Controller;

use AppBundle\Entity\CfonbReplace;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\CfonbActivation;
use AppBundle\Entity\CfonbRegle;

class CfonbController extends Controller
{
    public function indexAction()
    {
        return $this->render('BanqueBundle:Cfonb:index.html.twig');
    }

    public function getListAction( Request $request )
    {
        $rows = [];
        $cfonb = $this->getDoctrine()
                      ->getRepository('AppBundle:CfonbCode')
                      ->getListCfonb();
        foreach ( $cfonb as $value ) {
            if ( $value->cfonb_activation === 1 ) {
                $cfonbActivation = '<input type="checkbox" value="1" checked name="checkbox-cfonb" class="checkbox-select pointer">';
                /*$cfonbRegle = '<i class="fa fa-eyedropper pointer" aria-hidden="true"></i>';*/
            } else {
                $cfonbActivation = '<input type="checkbox" value="0" name="checkbox-cfonb" class="checkbox-select pointer">';
                /*$cfonbRegle = '<i class="fa fa-eyedropper pointer" aria-hidden="true">';*/
            }
            $rows[] = [
                'id' => $value->cfonb_code_id,
                'cell' => [
                    't-code' => $value->cfonb_code,
                    't-libelle' => $value->cfonb_libelle,
                    't-activation' => $cfonbActivation
                ]
            ];
        }
        $liste_data = [
            'rows' => $rows,
        ];
        return new JsonResponse($liste_data);
    }

    public function getRegleAction( Request $request )
    {
        if ( $request->isXmlHttpRequest() ) {
            if ( $request->getMethod() == 'POST' ) {
                $cfonbCodeId = $request->request->get('id');
                $cfonbCode = $this->getDoctrine()
                                  ->getRepository('AppBundle:CfonbCode')
                                  ->find($cfonbCodeId);

                $cfonbActivation = $this->getDoctrine()
                                        ->getRepository('AppBundle:CfonbActivation')
                                        ->findBy(array(
                                            'cfonbCode' => $cfonbCode
                                        ));

                if(!empty($cfonbActivation[0])){
                    $cfonbRegle = $this->getDoctrine()
                                       ->getRepository('AppBundle:CfonbRegle')
                                       ->findBy(array(
                                           'cfonbActivation' => $cfonbActivation[0]
                                       ));

                    if(!empty($cfonbRegle[0])){
                        return $this->render('BanqueBundle:Cfonb:cfonb-regle-edit.html.twig', array(
                            'cfonbRegle' => $cfonbRegle[0],
                            'cfonbActivationId' => $cfonbActivation[0]->getId(),
                            'hasRegle'   => 1
                        ));
                    }else{
                        return $this->render('BanqueBundle:Cfonb:cfonb-regle-edit.html.twig', array(
                            'hasRegle'   => 0,
                            'cfonbActivationId' => $cfonbActivation[0]->getId()
                        ));
                    }
                }else{
                    $em = $this->getDoctrine()->getManager();
                    $cfonbActivation = new CfonbActivation();
                    $cfonbActivation->setActive(0)
                                    ->setCfonbCode($cfonbCode);
                    $em->persist($cfonbActivation);
                    $em->flush();
                    return $this->render('BanqueBundle:Cfonb:cfonb-regle-edit.html.twig', array(
                        'hasRegle'   => 0,
                        'cfonbActivationId' => $cfonbActivation->getId()
                    ));
                }
            }
            return false;
        }
        return false;
    }

    public function addRegleAction(Request $request){
        if ( $request->isXmlHttpRequest() ) {
            if ( $request->getMethod() == 'POST' ) {
                $debut = $request->request->get('debut');
                $fin = $request->request->get('fin');
                $longueur = $request->request->get('longueur');
                $cfonbId = $request->request->get('cfonbId');

                $cfonbActivation = $this->getDoctrine()
                                  ->getRepository('AppBundle:CfonbActivation')
                                  ->find($cfonbId);

                $cfonbRegle =  $this->getDoctrine()
                                    ->getRepository('AppBundle:CfonbRegle')
                                    ->findBy(array(
                                        'cfonbActivation' => $cfonbActivation
                                    ));

                $em = $this->getDoctrine()->getManager();
                if(empty($cfonbRegle[0])){
                    $cfonbRegle = new CfonbRegle();
                    $cfonbRegle->setDebut($debut)
                               ->setFin($fin)
                               ->setLongueur($longueur)
                               ->setCfonbActivation($cfonbActivation);
                    $em->persist($cfonbRegle);
                }else{
                    $cfonbRegle[0]->setDebut($debut)
                                  ->setFin($fin)
                                  ->setLongueur($longueur)
                                  ->setCfonbActivation($cfonbActivation);
                }
                $em->flush();

                return new JsonResponse('SUCCESS');
            }
            return new JsonResponse('ERROR');
        }
        return new JsonResponse('ERROR');
    }

    public function activationAction(Request $request) {
        if ( $request->isXmlHttpRequest() ) {
            if ( $request->getMethod() == 'POST' ) {
                $cfonbCodeId = $request->request->get('id');
                $status = $request->request->get('state');
                $cfonbCode = $this->getDoctrine()
                                  ->getRepository('AppBundle:CfonbCode')
                                  ->find($cfonbCodeId);
                $cfonbActivation = $this->getDoctrine()
                                        ->getRepository('AppBundle:CfonbActivation')
                                        ->findBy(array(
                                            'cfonbCode' => $cfonbCode
                                        ));

                $em = $this->getDoctrine()->getManager();
                if(!empty($cfonbActivation[0])){
                    $cfonbActivation[0]->setActive($status)
                                       ->setCfonbCode($cfonbCode);
                }else{
                    $cfonbActivation = new CfonbActivation();
                    $cfonbActivation->setActive($status)
                                    ->setCfonbCode($cfonbCode);
                    $em->persist($cfonbActivation);
                }
                $em->flush();
                return new JsonResponse('SUCCESS');
            }
            return new JsonResponse('ERROR');
        }
        return new JsonResponse('ERROR');
    }

    public function replaceListAction(Request $request) {
        $rows = [];
        $listes = $this->getDoctrine()
                       ->getRepository('AppBundle:CfonbReplace')
                       ->findAll();
        foreach ($listes as $liste){
            $rows[] = [
                'id' => $liste->getId(),
                'cell' => [
                    't-recherche' => $liste->getRecherche(),
                    't-remplace' => $liste->getRemplace(),
                    't-action' => '<i class="fa fa-edit icon-action js-save-modif-replace" title="Modifier"></i><i class="fa fa-trash icon-action js-remove-replace" title="Supprimer"></i>'
                ]
            ];
        }
        $liste_data = [
            'rows' => $rows,
        ];
        return new JsonResponse($liste_data);
    }

    public function removeReplaceAction($id){
        $cfonbReplace = $this->getDoctrine()
                               ->getRepository('AppBundle:CfonbReplace')
                               ->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($cfonbReplace);
        $em->flush();
        return new JsonResponse('SUCCESS');
    }

    public function addReplaceAction(Request $request){
        if ( $request->isXmlHttpRequest() ) {
            if ( $request->getMethod() == 'POST' ) {
                $mot = $request->request->get('mot');
                $replace = $request->request->get('replace');
                $id = $request->request->get('id');
                $em = $this->getDoctrine()->getManager();
                if($id == ''){
                    $cfonbReplace = new CfonbReplace();
                    $cfonbReplace->setRecherche($mot)
                                 ->setRemplace($replace);
                    $em->persist($cfonbReplace);
                }else{
                    $cfonbReplace = $this->getDoctrine()
                                         ->getRepository('AppBundle:CfonbReplace')
                                         ->find($id);
                    $cfonbReplace->setRecherche($mot)
                                  ->setRemplace($replace);
                }

                $em->flush();
                return new JsonResponse('SUCCESS');
            }
            return new JsonResponse('ERROR');
        }
        return new JsonResponse('ERROR');
    }
}