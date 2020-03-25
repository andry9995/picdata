<?php

/**
 * Project: oneup
 * Author : Mamy Rakotonirina
 * Created on : 18 oct. 2017 16:35:26
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneModele;

/**
 * Description of ModeleController
 *
 */
class ModeleController extends Controller {
    public function indexAction(){
        return $this->render('@OneVente/Modele/index.html.twig');
    }

    public function listAction(Request $request) {

        $dossierId = $request->request->get('dossierId');

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($dossierId, $this));

        $modeles = $this->getDoctrine()
            ->getRepository('AppBundle:OneModele')
            ->getModelesByDossier($dossier);

        return $this->render('OneVenteBundle:Modele:list.html.twig', array(
            'modeles' => $modeles,
        ));
    }
    
    public function addAction() {
        $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);
        $default = $this->getDoctrine()->getRepository('AppBundle:OneModele')->find(1);
        return $this->render('OneVenteBundle:Modele:add.html.twig', array(
            'params' => $params,
            'default' => $default,
        ));
    }
    
    public function editAction(Request $request, $id) {

        $readonly = $request->request->get('readonly');

        $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);
        $modele = $this->getDoctrine()->getRepository('AppBundle:OneModele')->find($id);
        return $this->render('OneVenteBundle:Modele:edit.html.twig', array(
            'params' => $params,
            'modele' => $modele,
            'readonly' => $readonly
        ));
    }
    
    public function saveAction(Request $request, $dossierId) {
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $posted = $request->request->all();

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find(Boost::deboost($dossierId, $this));
            
            //Ajout
            if (!isset($posted['id']) || $posted['id'] == 0) {
                try {
                    $modele = new OneModele();
                    $modele->setModeleName($posted['modele-name']);
                    $modele->setModeleDescription($posted['modele-description']);
                    $modele->setHeadColor($posted['head-color']);
                    $modele->setFontFamily($posted['font-family']);
                    $modele->setFontSize($posted['font-size']);
                    $modele->setFontColor($posted['font-color']);
                    $modele->setShowCompanyName($posted['show-company-name']);
                    $modele->setShowReglement($posted['show-reglement']);
                    $modele->setShowNumClient($posted['show-num-client']);
                    $modele->setShowTelClient($posted['show-tel-client']);
                    $modele->setShowShippingAddress($posted['show-shipping-address']);
                    $modele->setShippingAddressLabel($posted['shipping-address-label']);
                    $modele->setBillingAddressRight($posted['billing-address-right']);
                    $modele->setBillingAddressLabel($posted['billing-address-label']);
                    $modele->setShowTvaIntracom($posted['show-tva-intracom']);
                    $modele->setDesignationLabel($posted['designation-label']);
                    $modele->setShowQuantity($posted['show-quantity']);
                    $modele->setQuantityLabel($posted['quantity-label']);
                    $modele->setShowPrice($posted['show-price']);
                    $modele->setPriceLabel($posted['price-label']);
                    $modele->setShowUnit($posted['show-unit']);
                    $modele->setShowProductCode($posted['show-product-code']);
                    $modele->setShowPaymentInfo($posted['show-payment-info']);
                    $modele->setPaymentInfoLabel($posted['payment-info-label']);
                    $modele->setShowDeadline($posted['show-deadline']);
                    $modele->setPaymentInfoDefault($posted['payment-info-default']);
                    $modele->setGlobalNote($posted['global-note']);
                    $modele->setDossier($dossier);
                    
                    $em->persist($modele);
                    $em->flush();

                    $response = array('type' => 'success', 'action' => 'add');
                    return new JsonResponse($response);

//                    return $this->redirect($this->generateUrl('one_modeles'));
                } catch (\Exception $ex) {

                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);

//                    return $this->redirect($this->generateUrl('one_modeles'));
                }
            } 
            
            //Edition
            else {
                try {
                    $modele = $em->getRepository('AppBundle:OneModele')->find($posted['id']);
                    $modele->setModeleName($posted['modele-name']);
                    $modele->setModeleDescription($posted['modele-description']);
                    $modele->setHeadColor($posted['head-color']);
                    $modele->setFontFamily($posted['font-family']);
                    $modele->setFontSize($posted['font-size']);
                    $modele->setFontColor($posted['font-color']);
                    $modele->setShowCompanyName($posted['show-company-name']);
                    $modele->setShowReglement($posted['show-reglement']);
                    $modele->setShowNumClient($posted['show-num-client']);
                    $modele->setShowTelClient($posted['show-tel-client']);
                    $modele->setShowShippingAddress($posted['show-shipping-address']);
                    $modele->setShippingAddressLabel($posted['shipping-address-label']);
                    $modele->setBillingAddressRight($posted['billing-address-right']);
                    $modele->setBillingAddressLabel($posted['billing-address-label']);
                    $modele->setShowTvaIntracom($posted['show-tva-intracom']);
                    $modele->setDesignationLabel($posted['designation-label']);
                    $modele->setShowQuantity($posted['show-quantity']);
                    $modele->setQuantityLabel($posted['quantity-label']);
                    $modele->setShowPrice($posted['show-price']);
                    $modele->setPriceLabel($posted['price-label']);
                    $modele->setShowUnit($posted['show-unit']);
                    $modele->setShowProductCode($posted['show-product-code']);
                    $modele->setShowPaymentInfo($posted['show-payment-info']);
                    $modele->setPaymentInfoLabel($posted['payment-info-label']);
                    $modele->setShowDeadline($posted['show-deadline']);
                    $modele->setPaymentInfoDefault($posted['payment-info-default']);
                    $modele->setGlobalNote($posted['global-note']);
                    $modele->setDossier($dossier);
                    
                    $em->flush();

                    $response = array('type' => 'success', 'action' => 'edit');
                    return new JsonResponse($response);

//                    return $this->redirect($this->generateUrl('one_modeles'));
                } catch (\Exception $ex) {

                    $response = array('type' => 'error', 'action' => 'add');
                    return new JsonResponse($response);
//                    return $this->redirect($this->generateUrl('one_modeles'));
                }
            }
        }
    }
    
    public function deleteAction($id) {
        try {
            $modele = $this->getDoctrine()->getRepository('AppBundle:OneModele')->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($modele);
            $em->flush();
            
            $response = array('type' => 'success', 'action' => 'delete');
            return new JsonResponse($response);
        } catch (\Doctrine\DBAL\DBALException $e) {
            $response = array('type' => 'error', 'action' => 'delete');
            return new JsonResponse($response);
        }
    }
    
    public function changeAction(Request $request) {
        if ($request->isMethod('GET')) {
            try {
                $em = $this->getDoctrine()->getManager();
                $modeleId = $request->query->get('modeleId');
                $docId = $request->query->get('docId');
                $docType = $request->query->get('docType');

                $modele = $this->getDoctrine()->getRepository('AppBundle:OneModele')->find($modeleId);
                if ($docType == 'devis') {
                    $documentModele = $em->getRepository('AppBundle:OneDocumentModele')->findOneByDevis($docId);
                } elseif ($docType == 'facture' || $docType == 'commande' || $docType == 'avoir') {
                    $documentModele = $em->getRepository('AppBundle:OneDocumentModele')->findOneByVente($docId);
                } elseif ($docType == 'encaissement') {
                    $documentModele = $em->getRepository('AppBundle:OneDocumentModele')->findOneByEncaissement($docId);
                } elseif ($docType == 'paiement') {
                    $documentModele = $em->getRepository('AppBundle:OneDocumentModele')->findOneByPaiement($docId);
                }

                $documentModele->setModele($modele);
                $documentModele->setHeadColor($modele->getHeadColor());
                $documentModele->setFontFamily($modele->getFontFamily());
                $documentModele->setFontSize($modele->getFontSize());
                $documentModele->setFontColor($modele->getFontColor());
                $documentModele->setShowCompanyName($modele->getShowCompanyName());
                $documentModele->setShowReglement($modele->getShowReglement());
                $documentModele->setShowNumClient($modele->getShowNumClient());
                $documentModele->setShowTelClient($modele->getShowTelClient());
                $documentModele->setShowShippingAddress($modele->getShowShippingAddress());
                $documentModele->setShippingAddressLabel($modele->getShippingAddressLabel());
                $documentModele->setBillingAddressRight($modele->getBillingAddressRight());
                $documentModele->setBillingAddressLabel($modele->getBillingAddressLabel());
                $documentModele->setShowTvaIntracom($modele->getShowTvaIntracom());
                $documentModele->setDesignationLabel($modele->getDesignationLabel());
                $documentModele->setShowQuantity($modele->getShowQuantity());
                $documentModele->setQuantityLabel($modele->getQuantityLabel());
                $documentModele->setShowPrice($modele->getShowPrice());
                $documentModele->setPriceLabel($modele->getPriceLabel());
                $documentModele->setShowUnit($modele->getShowUnit());
                $documentModele->setShowProductCode($modele->getShowProductCode());
                $documentModele->setShowPaymentInfo($modele->getShowPaymentInfo());
                $documentModele->setPaymentInfoLabel($modele->getPaymentInfoLabel());
                $documentModele->setShowDeadline($modele->getShowDeadline());
                $documentModele->setPaymentInfoDefault($modele->getPaymentInfoDefault());
                $documentModele->setGlobalNote($modele->getGlobalNote());
                $documentModele->setCustomized(0);

                $em->flush();
                $response = array('type' => 'success');
                return new JsonResponse($response);
            } catch (Exception $ex) {
                $response = array('type' => 'error');
                return new JsonResponse($response);
            }
        }
    }
}
