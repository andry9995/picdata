<?php

/**
 * Created by Netbeans
 * Created on : 5 sept. 2017, 22:15:44
 * Author : Mamy Rakotonirina
 */

namespace One\VenteBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\OneTache;
use AppBundle\Entity\Utilisateur;
use One\VenteBundle\Service\PdfService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class EmailController extends Controller
{
    /**
     * Envoie d'un document par email
     * @param Request $request
     * @param $type
     * @param $id
     * @return JsonResponse
     */
    public function documentAction(Request $request, $type, $id) {
        if ($request->isMethod('POST')) {
            try {
                $posted = $request->request->all();

                $dossierId = Boost::deboost($posted['dossierId'], $this);

                $exercice = new \DateTime();
                $exercice = $exercice->format('Y');

                $dateScan = null;

                $docPath = $this->getParameter('one_documents_dir').$type.DIRECTORY_SEPARATOR.$dossierId.DIRECTORY_SEPARATOR;
                $docName = "";
                $soussouscategorie =null;

                $devis = null;
                $vente = null;

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

                $message = (new \Swift_Message())
                        ->setSubject($posted['subject'])
                        ->setSender($posted['sender'])
                        ->setTo($posted['recipient'])
                        ->setReplyTo($posted['sender'])
                        ->setBody(nl2br($posted['message']), 'text/html')
                        ->attach(\Swift_Attachment::fromPath($docPath.$docName));
                $this->get('mailer')->send($message);


                $filePath = $docPath.$docName;


                $name = explode('.', $docName)[0];
                $ext = explode('.', $docName)[1];
                $filename = $name.'-'.date('YmdHis').'.'.$ext;


                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($dossierId);


                $pdfService = new PdfService($this->getDoctrine()->getEntityManager());
                $pdfService->saveImage($this->getUser(), $dossier, $exercice, $soussouscategorie, $devis, $vente, $dateScan, $filePath, $filename, $name, $ext);



                $response = array('type' => 'success');
                return new JsonResponse($response);
            } catch (\Exception $ex) {
                $response = array('type' => 'error');
                return new JsonResponse($response);
            }
            
        }

        throw new AccessDeniedException('Accès refusé');
        
    }


    public function newAction($tiersId)
    {
        $tiers = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->find($tiersId);

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        return $this->render('OneVenteBundle:Email:edit.html.twig', array(
            'tiers' => $tiers,
            'utilisateur' => $utilisateur
        ));
    }

    public function sendAction(Request $request){
        $posted = $request->request->all();

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();

        $message = (new \Swift_Message())
            ->setSubject($posted['subject'])
            ->setFrom($posted['sender'], $utilisateur->getNomComplet())
//            ->setBcc($utilisateur->getEmail())
            ->setTo($posted['recipient'])
            ->setReplyTo($posted['sender'])
            ->setBody(nl2br($posted['message']), 'text/html');

        $this->get('mailer')->send($message);


        //Enregistrement any @ Tache

        $em = $this->getDoctrine()
            ->getEntityManager();

        $tache = new OneTache();

        $tiers = $this->getDoctrine()
            ->getRepository('AppBundle:Tiers')
            ->find($posted['tiersId']);

        $tache->setStatus(1);
        $tache->setCreeLe(new \DateTime());
        $tache->setEcheance(new \DateTime());
        $tache->setSujet($posted['subject']);
        $tache->setMemo($posted['message']);
        $tache->setTiers($tiers);

        $em->persist($tache);
        $em->flush();

        $response = array('type' => 'success');

        return new JsonResponse($response);
    }

}