<?php

/**
 * Created by Netbeans
 * Created on : 2 sept. 2017, 11:38:36
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\OneDevis;
use AppBundle\Entity\OneVente;
use One\VenteBundle\Service\PdfService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use One\VenteBundle\Service\DevisService;
use One\VenteBundle\Service\VenteService;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PdfController extends Controller
{
    /**
     * Génération d'un pdf d'un document
     * @param Request $request
     * @param $type
     * @param $id
     * @return Response
     */
    public function generateAction(Request $request, $type, $id) {
        if ($request->isMethod('GET')) {

            $document = null;
            $documentDetails = array();
            $articles = array();
            $modele =  null;

            switch ($type) {
                case 'devis':
                    $service = new DevisService($this->getDoctrine()->getManager());
                    $document = $this->getDoctrine()
                        ->getRepository('AppBundle:OneDevis')
                        ->find($id);

                    $documentDetails = $service->getDevisDetailsByDevis($document);

                    $articles = $this->getDoctrine()
                        ->getRepository('AppBundle:OneArticleVente')
                        ->findBy(array('devis' => $document));
                    //$contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->getContacts($document->getOneClientProspect()->getId());
                    $modele = $this->getDoctrine()
                        ->getRepository('AppBundle:OneDocumentModele')
                        ->findOneByDevis($document);
                    break;

                case 'facture':
                case 'commande':
                case 'avoir':

                    $service = new VenteService($this->getDoctrine()->getManager());
                    /** @var OneVente $document */
                    $document = $this->getDoctrine()
                        ->getRepository('AppBundle:OneVente')
                        ->find($id);

                    $documentDetails = $service->getVenteDetailsByVente($document);

                    $articles = $this->getDoctrine()
                        ->getRepository('AppBundle:OneArticleVente')
                        ->findBy(array('vente' => $document));

                    //$contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->getContacts($document->getOneClientProspect()->getId());
                    $modele = $this->getDoctrine()
                        ->getRepository('AppBundle:OneDocumentModele')
                        ->findOneByVente($document);


                    break;

                case 'livraison':
                    $service = new VenteService($this->getDoctrine()->getManager());

                    $document = $this->getDoctrine()
                        ->getRepository('AppBundle:OneVente')
                        ->find($id);

                    $documentDetails = $service->getVenteDetailsByVente($document);

                    $articles = $this->getDoctrine()
                        ->getRepository('AppBundle:OneArticleVente')
                        ->findBy(array('vente' => $document));
                    //$contacts = $this->getDoctrine()->getRepository('AppBundle:OneContactClient')->getContacts($document->getOneClientProspect()->getId());
                    $modele = $this->getDoctrine()
                        ->getRepository('AppBundle:OneDocumentModele')
                        ->findOneByVente($document);
                    break;

                case 'encaissement':
                    $document = $this->getDoctrine()
                        ->getRepository('AppBundle:OneEncaissement')
                        ->find($id);
                    $documentDetails = null;
                    $articles = $this->getDoctrine()
                        ->getRepository('AppBundle:OneEncaissementDetail')
                        ->findByOneEncaissement($document);
                    $modele = $this->getDoctrine()
                        ->getRepository('AppBundle:OneDocumentModele')
                        ->findOneByEncaissement($document);
                    break;

                case 'paiement':
                    $document = $this->getDoctrine()
                        ->getRepository('AppBundle:OnePaiement')
                        ->find($id);
                    $documentDetails = null;
                    $articles = $this->getDoctrine()
                        ->getRepository('AppBundle:OnePaiementDetail')
                        ->findOneByOnePaiement($document);
                    $modele = $this->getDoctrine()
                        ->getRepository('AppBundle:OneDocumentModele'
                        )->findOneByPaiement($document);
                    break;
            }

            $params = $this->getDoctrine()->getRepository('AppBundle:OneParametre')->find(1);

            return $this->render('OneVenteBundle:Pdf:generate.html.twig', array(
                'type' => $type,
                'document' => $document,
                'documentDetails' => $documentDetails,
                'modele' => $modele,
                'articles' => $articles,
                'params' => $params,
            ));
        }

        //POST

        $docName = "";

        $dossierId = Boost::deboost($request->request->get('dossierId'), $this);


        switch ($type){
            case 'devis':
                /** @var OneDevis $doc */
                $doc = $this->getDoctrine()->getRepository('AppBundle:OneDevis')->find($id);
                $docName = 'Devis-'.$doc->getCode().'.pdf';
                break;
            case 'facture':
                $doc = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
                $docName = 'Facture-'.$doc->getCode().'.pdf';
                break;
            case 'commande':
                $doc = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
                $docName = 'Commande-'.$doc->getCode().'.pdf';
                break;
            case 'livraison':
                $doc = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
                $docName = 'BonLivraison-'.$doc->getCode().'.pdf';
                break;
            case 'avoir':
                $doc = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
                $docName = 'Avoir-'.$doc->getCode().'.pdf';
                break;
            case 'encaissement':
                $doc = $this->getDoctrine()->getRepository('AppBundle:OneEncaissement')->find($id);
                $docName = 'Encaissement de vente-'.$doc->getCode().'.pdf';
                break;
            case 'paiement':
                $doc = $this->getDoctrine()->getRepository('AppBundle:OnePaiement')->find($id);
                $docName = 'Encaissement divers-'.$doc->getCode().'.pdf';
                break;
            default:
                break;
        }

        $docPath = $this->getParameter('one_documents_dir').$type.DIRECTORY_SEPARATOR.$dossierId.DIRECTORY_SEPARATOR;

        if (!file_exists($docPath)) {
            if (!mkdir($docPath, 0777) && !is_dir($docPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $docPath));
            }

        }

        $content = $request->request->get('content');
        $pdf = new \HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', array(10, 15, 10, 15));
        $pdf->writeHTML($content);
        $pdf->Output($docPath.$docName, 'F');

        return new Response($content);
    }

    /**
     * @param $type
     * @param $id
     * @param $dossierId
     * @return BinaryFileResponse
     */
    public function printAction($type, $id, $dossierId) {

        $dossierId = Boost::deboost($dossierId, $this);

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);


        $exercice = new \DateTime();
        $exercice = $exercice->format('Y');

        /** @var \DateTime $dateScan */
        $dateScan = null;

        $docPath = $this->getParameter('one_documents_dir').$type.DIRECTORY_SEPARATOR.$dossierId.DIRECTORY_SEPARATOR;
        $docName = "";
        $soussouscategorie =null;


        $devis = null;
        $vente = null;
        $encaissement = null;
        $paiement = null;

        switch ($type){
            case 'devis':
                $devis = $this->getDoctrine()->getRepository('AppBundle:OneDevis')->find($id);
                $docName = 'Devis-'.$devis->getCode().'.pdf';
                $exercice = $devis->getExercice();
                $dateScan = $devis->getDateDevis();
                // id:345 Devis
                $soussouscategorie = $this->getDoctrine()
                    ->getRepository('AppBundle:Soussouscategorie')
                    ->find(345);

                break;
            case 'facture':
                $vente = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
                $docName = 'Facture-'.$vente->getCode().'.pdf';
                $exercice = $vente->getExercice();
                $dateScan = $vente->getDateDevis();
                //Facture Client

                break;
            case 'commande':
                $vente = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
                $docName = 'Commande-'.$vente->getCode().'.pdf';
                $exercice = $vente->getExercice();
                $dateScan = $vente->getDateDevis();
                break;
            case 'livraison':
                $vente = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
                $docName = 'BonLivraison-'.$vente->getCode().'.pdf';
                $exercice = $vente->getExercice();
                $dateScan = $vente->getDateDevis();
                break;
            case 'avoir':
                $vente = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
                $docName = 'Avoir-'.$vente->getCode().'.pdf';
                $exercice = $vente->getExercice();
                $dateScan = $vente->getDateDevis();
                break;
            case  'encaissement':
                $doc = $this->getDoctrine()->getRepository('AppBundle:OneEncaissement')->find($id);
                $docName = 'Encaissement de vente-'.$doc->getCode().'.pdf';
                $exercice = $doc->getExercice();
                $dateScan = $doc->getDateDevis()->format("Ymd");
                break;
            case 'paiement':
                $doc = $this->getDoctrine()->getRepository('AppBundle:OnePaiement')->find($id);
                $docName = 'Encaissement divers-'.$doc->getCode().'.pdf';
                $exercice = $doc->getExercice();
                $dateScan = $doc->getDateDevis();
                break;
        }

        $filePath = $docPath.$docName;


        $name = explode('.', $docName)[0];
        $ext = explode('.', $docName)[1];
        $filename = $name.'-'.date('YmdHis').'.'.$ext;


        $pdfService = new PdfService($this->getDoctrine()->getEntityManager());
        $pdfService->saveImage($this->getUser(), $dossier, $exercice, $soussouscategorie, $devis, $vente, $dateScan, $filePath, $filename, $name, $ext);

        
        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename,
            iconv('UTF-8', 'ASCII//TRANSLIT', $filename)
        );
        return $response;
    }
    
    /**
     * Télechargement d'un pdf d'un document
     * @param type $type
     * @param type $id
     * @return BinaryFileResponse
     */
    public function downloadAction($type, $id) {
        $docPath = $this->getParameter('one_documents_dir').$type.DIRECTORY_SEPARATOR;
        if ($type == 'devis') {
            $doc = $this->getDoctrine()->getRepository('AppBundle:OneDevis')->find($id);
            $docName = 'Devis-'.$doc->getCode().'.pdf';
            $filePath = $docPath.$docName;
        } elseif ($type == 'facture') {
            $doc = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
            $docName = 'Facture-'.$doc->getCode().'.pdf';
            $filePath = $docPath.$docName;
        } elseif ($type == 'commande') {
            $doc = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
            $docName = 'Commande-'.$doc->getCode().'.pdf';
            $filePath = $docPath.$docName;
        } elseif ($type == 'livraison') {
            $doc = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
            $docName = 'BonLivraison-'.$doc->getCode().'.pdf';
            $filePath = $docPath.$docName;
        } elseif ($type == 'avoir') {
            $doc = $this->getDoctrine()->getRepository('AppBundle:OneVente')->find($id);
            $docName = 'Avoir-'.$doc->getCode().'.pdf';
            $filePath = $docPath.$docName;
        } elseif ($type == 'encaissement') {
            $doc = $this->getDoctrine()->getRepository('AppBundle:OneEncaissement')->find($id);
            $docName = 'Encaissement de vente-'.$doc->getCode().'.pdf';
            $filePath = $docPath.$docName;
        } elseif ($type == 'paiement') {
            $doc = $this->getDoctrine()->getRepository('AppBundle:OnePaiement')->find($id);
            $docName = 'Encaissement divers-'.$doc->getCode().'.pdf';
            $filePath = $docPath.$docName;
        }
        $name = explode('.', $docName)[0];
        $ext = explode('.', $docName)[1];
        $filename = $name.'-'.date('YmdHis').'.'.$ext;

        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename,
            iconv('UTF-8', 'ASCII//TRANSLIT', $filename)
        );
        return $response;
    }



}