<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 12/10/2017
 * Time: 11:18
 */

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\BanqueCompte;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GestionBanqueController extends Controller
{

    public function banqueCompteEditAction(Request $request, $dossierId)
    {
        if($request->isXmlHttpRequest())
        {

            $idDossier = Boost::deboost($dossierId, $this);

            if($idDossier =='0' || $idDossier==false)
            {
                return new Response();
            }

            $id = $request->request->get('id');
            $banqueId = $request->request->get('banque-nom');
            $numCompte = $request->request->get('banque-compte');
            $journalId = $request->request->get('banque-journal');
            $pccId = $request->request->get('banque-compte-comptable');

            $journal = null;

            if($journalId != ''){
                $journal = $this->getDoctrine()
                    ->getRepository('AppBundle:Journal')
                    ->find($journalId);
            }

            $pcc = null;
            if($pccId !=  ''){
                $pcc = $this->getDoctrine()
                    ->getRepository('AppBundle:Pcc')
                    ->find($pccId);
            }

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($idDossier);

            $em = $this->getDoctrine()->getManager();

            if($id !='')
            {
                try
                {
                    if($id != 'new_row')
                    {
                        $banqueCompte = $this->getDoctrine()
                            ->getRepository('AppBundle:BanqueCompte')
                            ->find($id);

                        $banqueCompte->setDossier($dossier);
                        $banqueCompte->setNumcompte($numCompte);
                        $banqueCompte->setPcc($pcc);
                        $banqueCompte->setJournal($journal);

                        if($banqueId !='')
                        {

                            $banque = $this->getDoctrine()
                                ->getRepository('AppBundle:Banque')
                                ->find($banqueId);

                            $banqueCompte->setBanque($banque);
                        }

                        $em->persist($banqueCompte);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));

                    }
                    else
                    {
                        $banqueCompte = new BanqueCompte();

                        $banqueCompte->setDossier($dossier);
                        $banqueCompte->setNumcompte($numCompte);
                        $banqueCompte->setPcc($pcc);
                        $banqueCompte->setJournal($journal);


                        if($banqueId !='')
                        {
                            $banque = $this->getDoctrine()
                                ->getRepository('AppBundle:Banque')
                                ->find($banqueId);

                            $banqueCompte->setBanque($banque);
                        }

                        $em->persist($banqueCompte);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    }
                }
                catch(\Exception $ex)
                {
                    return new Response($ex->getMessage(), 500);
                }
            }
            throw new NotFoundHttpException("Profession liberale introuvable.");
        }
        else
        {
            throw new AccessDeniedException('Accès refusé');
        }
    }


    public function banqueCompteRemoveAction(Request $request){
        if($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');

            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $banqueCompte = $this->getDoctrine()
                    ->getRepository('AppBundle:BanqueCompte')
                    ->find($id);

                if ($banqueCompte)
                {
                    $em->remove($banqueCompte);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );

                    return new JsonResponse(json_encode($data));
                }
                else
                {
                    $data = array(
                        'erreur' => true,
                        'erreur_text' => "Tache introuvable",
                    );
                    return new JsonResponse(json_encode($data), 404);
                }
            }

            throw new NotFoundHttpException("Compte Banque introuvable.");
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function compteComptableAction(Request $request, $json){

        if($request->isXmlHttpRequest()) {

            $options = '<select>';


            $dossierId = Boost::deboost($json, $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if (!is_null($dossier)) {
                $pccs = $this->getDoctrine()
                    ->getRepository('AppBundle:Pcc')
                    ->findBy(array('dossier' => $dossier), array('compte' => 'ASC'));

                foreach ($pccs as $pcc) {
                    $options .= '<option value="' . $pcc->getId() . '">[' . $pcc->getCompte() . '][' . $pcc->getIntitule() . ']</option>';

                }
            }


            $options .= '</select>';

            return new Response($options);


        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function gestionAction(Request $request){

        if($request->isXmlHttpRequest()){

            $rows = array();

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if(!is_null($dossier)){
                $banqueComptes = $this->getDoctrine()
                    ->getRepository('AppBundle:BanqueCompte')
                    ->findBy(array('dossier' => $dossier));

                foreach ($banqueComptes as $banqueCompte){

                    /* @var $banqueCompte \AppBundle\Entity\BanqueCompte*/
                    $nomBanque = '';
                    $codeBanque = '';
                    $compteBanque = '';
                    $compteCompta = '';
                    $journal = '';
                    $solde = '';
                    $dateModification = '';


                    if($banqueCompte->getBanque() != null)
                    {
                        $nomBanque = $banqueCompte->getBanque()->getNom();
                        $codeBanque = $banqueCompte->getBanque()->getCodebanque();
                        $compteBanque = $banqueCompte->getNumcompte();

                        if(!is_null($banqueCompte->getJournal())){
                            $journal = $banqueCompte->getJournal()->getCode();
                        }

                        if(!is_null($banqueCompte->getPcc())){
                            $compteCompta = '['.$banqueCompte->getPcc()->getCompte().']['.$banqueCompte->getPcc()->getIntitule().']';
                        }

                        if(!is_null($banqueCompte->getSolde())){
                            $solde = $banqueCompte->getSolde();
                        }

                    }


                    $rows[] = array(
                        'id' => $banqueCompte->getId(),
                        'cell' => array(

                            $nomBanque,
                            $codeBanque,
                            $compteBanque,
                            $solde,
                            '',
                            $compteCompta,
                            $journal,
                            '<i class="fa fa-save icon-action js-save-banqueCompte" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-banqueCompte" title="Supprimer"></i>'
                        )
                    );
                }
            }

            $liste = array('rows'=>$rows);

            return new JsonResponse($liste);

        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function journalAction(Request $request){

        if($request->isXmlHttpRequest()){

            $journals = $this->getDoctrine()
                ->getRepository('AppBundle:Journal')
                ->findBy(array(), array('libelle' => 'asc'));
            $options = '<select>';

            $options .='<option></option>';
            foreach ($journals as $journal)
            {
                $options .='<option value="'.$journal->getId().'">'.$journal->getCode().'</option>';
            }

            $options .='</select>';

            return new Response($options);

        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }
}