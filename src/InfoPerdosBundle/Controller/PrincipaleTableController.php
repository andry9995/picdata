<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 14/11/2016
 * Time: 16:39
 */
namespace InfoPerdosBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\Banque;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Carburant;
use AppBundle\Entity\Client;
use AppBundle\Entity\ConventionComptable;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\FormeActivite;
use AppBundle\Entity\LogInfoperdos;
use AppBundle\Entity\Mandataire;
use AppBundle\Entity\MethodeComptable;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\PrestationFiscale;
use AppBundle\Entity\ProfessionLiberale;
use AppBundle\Entity\ResponsableCsd;
use AppBundle\Entity\Site;
use AppBundle\Entity\TvaTauxDossier;
use AppBundle\Entity\TypeVehicule;
use AppBundle\Entity\TypeVente;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Tests\ChoiceList\ArrayChoiceListTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AppBundle\Entity\RegimeFiscal;
use AppBundle\Entity\Vehicule;
use AppBundle\Entity\RegimeImposition;
use AppBundle\Entity\TypeActivite;


class PrincipaleTableController extends Controller
{
    public function indexAction()
    {
        return $this->render('InfoPerdosBundle:PrincipaleTable:index.html.twig');
    }

    public function achatAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if($json == 1) {
                return new Response();
            }
            else{
                $options = '<select>';

                $options .='<option></option>';

                $options .='<option value="1">Saisie sur factures</option>';
                $options .='<option value="0">Import excel</option>';
                $options .='<option value="2">Autre</option>';

                $options .='</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }


    public function banqueAutoCompleteAction(Request $request,$term){
        if($request->isXmlHttpRequest()){

            $qb = $this->getDoctrine()->getRepository('AppBundle:Banque')
                ->createQueryBuilder('bc')
                ->where('bc.nom like :nombanque')
                ->setParameter('nombanque', $term.'%')
                ->getQuery()
                ->getResult();


//            $res = $this->getDoctrine()
//                ->getRepository('AppBundle:Banque')
//                ->findAll();

            return new JsonResponse($qb);


        }
        else{
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    public function banqueAction(Request $request,$json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else
            {
                $banques = $this->getDoctrine()
                    ->getRepository('AppBundle:Banque')
                    ->findBy(array(), array('nom' => 'asc'));
                $options = '<select>';

                $options .='<option></option>';
                foreach ($banques as $banque)
                {
                    $options .='<option value="'.$banque->getId().'">'.$banque->getNom().'</option>';
                }


                $options .='<option value="-1">Autre</option>';
                $options .='</select>';

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function compteBanqueAction(Request $request,$dossierId)
    {
        if($request->isXmlHttpRequest())
        {
            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find(Boost::deboost($dossierId, $this));

            $pccs  = $this->getDoctrine()
                ->getRepository('AppBundle:Pcc')
                ->getPccByDossierLike($dossier, array('512'));

            /** @var Pcc[] $allowedPccs */
//            $allowedPccs  = array();

            /** @var BanqueCompte[] $banqueComptes */
//            $banqueComptes = $this->getDoctrine()
//                ->getRepository('AppBundle:BanqueCompte')
//                ->findBy(array('dossier' => $dossier));

            /** @var Pcc[] $pccsTaken */
//            $pccsTaken = array();

            /** @var BanqueCompte $banqueCompte */
//            foreach ($banqueComptes as $banqueCompte){
//                if(null !== $banqueCompte->getPcc())
//                    $pccsTaken[] = $banqueCompte->getPcc();
//            }

//            if(count($pccsTaken) > 0) {
//                foreach ($pccs as $pcc) {
//                    $canAdd = false;
//                    foreach ($pccsTaken as $pccTaken) {
//                        if (!in_array($pccTaken, $pccs)) {
//                            $canAdd = true;
//                        }
//                    }
//                    if ($canAdd) {
//                        $allowedPccs[] = $pcc;
//                    }
//                }
//            }
//            else{
//
//            }

            $allowedPccs = $pccs;

            $options = '<select>';
            $options .='<option></option>';

            foreach ($allowedPccs as $allowedPcc)
            {
                $options .='<option value="'.$allowedPcc->getId().'">'.$allowedPcc->getCompte().'['.$allowedPcc->getIntitule().']'.'</option>';
            }

            $options .='</select>';
            return new Response($options);
        }

        throw new AccessDeniedHttpException("Accès refusé");
    }

    public function banqueRecapAction(Request $request,$json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else {

                $options = '<select>';

                $options .= '<option></option>';


                $options .= '<option value="1">Saisie</option>';
                $options .= '<option value="0">Import ecritures</option>';
                $options .= '<option value="2">Déjà importé dans l\'archive</option>';

                $options .= '</select>';

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function banqueCodeAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $banques = $this->getDoctrine()
                ->getRepository('AppBundle:Banque')
                ->find($request->request->get('banqueId'));

            $banqueCode = '-1';

            if(!is_null($banques)){
                $banqueCode = $banques->getCodebanque();
            }

            return new Response($banqueCode);
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function banqueCompteAction(Request $request, $dossierId)
    {
        if($request->isXmlHttpRequest())
        {
            $idDossier = Boost::deboost($dossierId, $this);

            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->find($idDossier);


            $banqueComptes = $this->getDoctrine()
                ->getRepository('AppBundle:BanqueCompte')
                ->getBanqueCompteByDossier($dossier);
                   // ->findAll();

            $rows = array();

            if(is_array($banqueComptes)) {
                foreach ($banqueComptes as $banqueCompte)
                {
                    /* @var $banqueCompte \AppBundle\Entity\BanqueCompte*/
                    $nomBanque = '';
                    $codeBanque = '';
                    $compte = '';

                    if(null !== ($banqueCompte->getBanque()))
                    {
                        $nomBanque = $banqueCompte->getBanque()->getNom();
                        $codeBanque = $banqueCompte->getBanque()->getCodebanque();

                    }

                    if(null !== $banqueCompte->getPcc()) {
                        $compte = $banqueCompte->getPcc()->getCompte().'['.$banqueCompte->getPcc()->getIntitule().']';
                    }

                    $rows[] = array(
                        'id' => $banqueCompte->getId(),
                        'cell' => array(
                            
//                            $banqueCompte->getBanque()->getNom(),
                            $nomBanque,
                            $codeBanque,
                            '',
//                            '',
                            $banqueCompte->getNumcompte(),
                            $banqueCompte->getNumcb(),
                            $banqueCompte->getIban(),
                            $compte,
                            '<i class="fa fa-save icon-action js-save-banqueCompte" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-banqueCompte" title="Supprimer"></i>'
                        )
                    );
                }
            }


            $liste = array('rows'=>$rows);

            return new JsonResponse($liste);
            
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function banqueCompteEditAction(Request $request, $dossierId)
    {
        if($request->isXmlHttpRequest())
        {

            $idDossier = Boost::deboost($dossierId, $this);

            if($dossierId =='0' || $idDossier==false)
            {
                return new Response();
            }

            $id = $request->request->get('id');
            $banqueId = $request->request->get('banque-nom');
            $numeroCompte = $request->request->get('banque-numero');
            $numcb = $request->request->get('banque-numcb');
            $iban = $request->request->get('banque-iban');

            $compte = $request->request->get('compte-banque');

            $nomAutreBanque = $request->request->get('banque-nom-autre');
//            $codeAutreBanque = $request->request->get('banque-code-autre');

            $codeBanque = $request->request->get('banque-code');



            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($idDossier);

            $em = $this->getDoctrine()->getManager();

            if($id !=='')
            {
                try
                {
                    if($id !== 'new_row')
                    {
                        $banqueCompte = $this->getDoctrine()
                            ->getRepository('AppBundle:BanqueCompte')
                            ->find($id);

                        $banqueCompte->setDossier($dossier);
                        $banqueCompte->setNumcompte($numeroCompte);
                        $banqueCompte->setNumcb($numcb);
                        $banqueCompte->setIban($iban);

                        $pcc = null;
                        if($compte !== ''){
                            $pcc = $this->getDoctrine()
                                ->getRepository('AppBundle:Pcc')
                                ->find($compte);
                        }
                        $banqueCompte->setPcc($pcc);

                        if($banqueId !=='')
                        {

                            //Raha autre banque

                            $banque = $this->getDoctrine()
                                ->getRepository('AppBundle:Banque')
                                ->find($banqueId);

                            if(null === $banque){
                                $banque = new Banque();


                                if(null !== $nomAutreBanque && null !== $codeBanque && $nomAutreBanque !=='' && $codeBanque !=='') {

                                    $banque->setNom($request->request->get('banque-nom-autre'));
//                                    $banque->setCodebanque($request->request->get('banque-code-autre'));
                                    $banque->setCodebanque($request->request->get('banque-code'));


                                    $em->persist($banque);

                                    $em->flush();
                                }
                                else{
                                    return new Response('Erreur');
                                }

                            }

                            $banqueCompte->setBanque($banque);
                        }

                        $em->persist($banqueCompte);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));

                    }

                    $banqueCompte = new BanqueCompte();

                    $banqueCompte->setDossier($dossier);
                    $banqueCompte->setNumcompte($numeroCompte);
                    $banqueCompte->setNumcb($numcb);
                    $banqueCompte->setIban($iban);

                    $pcc = null;
                    if($compte !== ''){
                        $pcc = $this->getDoctrine()
                            ->getRepository('AppBundle:Pcc')
                            ->find($compte);
                    }
                    $banqueCompte->setPcc($pcc);


                    if($banqueId !='')
                    {
                        $banque = $this->getDoctrine()
                            ->getRepository('AppBundle:Banque')
                            ->find($banqueId);

                        if(null === $banque){
                            if(null !== $nomAutreBanque && null !== $codeBanque && $nomAutreBanque !=='' && $codeBanque !=='') {
                                $banque = new Banque();

                                $banque->setNom($request->request->get('banque-nom-autre'));
//                                    $banque->setCodebanque($request->request->get('banque-code-autre'));

                                $banque->setCodebanque($request->request->get('banque-code'));

                                $em->persist($banque);

                                $em->flush();
                            }
                            else{
                                return new Response('Erreur');
                            }
                        }

                        $banqueCompte->setBanque($banque);
                    }

                    $em->persist($banqueCompte);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );
                    return new JsonResponse(json_encode($data));
                }
                catch(\Exception $ex)
                {
                    return new Response($ex->getMessage(), 500);
                }
            }
            throw new NotFoundHttpException("Banque Compte Introuvable.");
        }

        throw new AccessDeniedException('Accès refusé');
    }

    public function banqueCompteRemoveAction(Request $request)
    {
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

    public function carburantAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if ($json == 1)
            {
                return new Response();
            }
            else
            {
                $carburants = $this->getDoctrine()
                    ->getRepository('AppBundle:Carburant')
                    ->findAll();

                $options = '<select>';

                $options .='<option></option>';

                /** @var  $carburant Carburant*/
                foreach ($carburants as $carburant)
                {
                    $options .= '<option value="' . $carburant->getId() . '">' . $carburant->getLibelle() . '</option>';
                }
                $options .= '</select>';
                return new Response($options);
            }


        }

        else
        {
            throw new AccessDeniedException("Accès refusé");
        }
    }

    public function contratPrevoyanceAction (Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $contratPrevoyances = $this->getDoctrine()
                ->getRepository('AppBundle:ContratPrevoyance')
                ->findAll();

            $rows = array();

            foreach ($contratPrevoyances as $contratPrevoyance)
            {
                $rows[] = array(
                    'id'=>$contratPrevoyance->getId(),
                    'cell'=>array(
                        $contratPrevoyance->getLibelle(),
                        '<i class="fa fa-trash icon-action js-delete-contratPrevoyance" title="Supprimer"></i>'
                    )
                );
            }

            $liste = array('rows'=>$rows);

            return new JsonResponse($liste);
        }
        else
        {
            return new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function contratPrevoyanceEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id', "");
            $libelle = $request->request->get("contratPrevoyance-libelle");

            $em = $this->getDoctrine()
                ->getManager();

            if ($id != "")
            {
                if ($id != "new_row")
                {
                    $contratPrevoyance = $this->getDoctrine()
                        ->getRepository('AppBundle:ContratPrevoyance')
                        ->find($id);
                    if ($contratPrevoyance)
                    {
                        $contratPrevoyance->setLibelle($libelle);
                    }
                }
                else
                {
                    $contratPrevoyance = new ContratPrevoyance();
                    $contratPrevoyance->setLibelle($libelle);
                    $em->persist($contratPrevoyance);
                }

                $em->flush();
                $data = array(
                    'erreur' => false
                );
                return new JsonResponse(json_encode($data));
            }
            throw new NotFoundHttpException("Contrat Prevoyance introuvable.");

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }

    }

    public function contratPrevoyanceRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');

            $data = array(
                'erreur' => true,
                'erreur_text' => "Contrat prevoyance introuvable",
            );

            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $contratPrevoyance = $this->getDoctrine()
                    ->getRepository('AppBundle:ContratPrevoyance')
                    ->find($id);

                if ($contratPrevoyance)
                {
                    $em->remove($contratPrevoyance);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );
                }
                else
                {
                    $data = array(
                        'erreur' => true,
                        'erreur_text' => "Contrat prevoyance introuvable",
                    );
                    return new JsonResponse(json_encode($data), 404);
                }
            }
            return new JsonResponse(json_encode($data));
        }
        else
        {
            return new AccessDeniedException('Accès refusé.');
        }
    }


    public function conventionComptableAction(Request $request,$json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else
            {
                $conventionComptables = $this->getDoctrine()
                    ->getRepository('AppBundle:ConventionComptable')
                    ->findBy(array(), array('libelle' => 'asc'));
                $options = '<select>';

                $options .='<option></option>';
                /** @var ConventionComptable $conventionComptable */
                foreach ($conventionComptables as $conventionComptable)
                {
                    $options .='<option value="'.$conventionComptable->getId().'">'.$conventionComptable->getLibelle().'</option>';
                }

                $options .='</select>';

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function dateClotureAction(Request $request,$json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else {
                $options = '<select>';

                $options .= '<option></option>';

                $options .= '<option value="1">Janvier</option>';
                $options .= '<option value="2">Février</option>';
                $options .= '<option value="3">Mars</option>';
                $options .= '<option value="4">Avril</option>';
                $options .= '<option value="5">Mai</option>';
                $options .= '<option value="6">Juin</option>';
                $options .= '<option value="7">Juillet</option>';
                $options .= '<option value="8">Août</option>';
                $options .= '<option value="9">Septembre</option>';
                $options .= '<option value="10">Octobre</option>';
                $options .= '<option value="11">Novembre</option>';
                $options .= '<option value="12">Décembre</option>';


                $options .= '</select>';

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function formeActAction(Request $request,$json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else
            {
                $post = $request->request;

                $codeFiscal = $post->get('codeFiscal');

                $dossierId = Boost::deboost($post->get('dossierId'), $this);
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($dossierId);

                if($codeFiscal == '') {
                    $formeActivites = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeActivite')
                        ->findBy(array(), array('libelle' => 'asc'));
                }
                else{
                    $formeActivites = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeActivite')
                        ->getFormeActiviteByRegimeFiscal($codeFiscal);
                }

                $options = '<select>';

                $options .='<option></option>';

                $select = false;
                foreach ($formeActivites as $formeActivite)
                {
                    $code = "";
                    if(null !== $formeActivite){
                        $code = $formeActivite->getCode();
                    }

                    if(null !== $dossier){
                        if(null !== $dossier->getFormeActivite()){
                            if($dossier->getFormeActivite()->getId() == $formeActivite->getId()) {
                                $select = true;
                            }
                        }
                    }

                    if($select){
                        $options .= '<option value="' . $formeActivite->getId() . '" '."selected". ' data-code="'.$code.'">' . $formeActivite->getLibelle() . '</option>';
                        $select = false;
                    }
                    else{


                        switch ($codeFiscal){
                            case 'CODE_BA':
                                if($formeActivite->getCode() === 'CODE_AGRICOLE'){
                                    $select = true;
                                }
                                break;
                            case 'CODE_BNC':
                                if($formeActivite->getCode() === 'CODE_PROFESSION_LIBERALE'){
                                    $select = true;
                                }
                                break;
                            case 'CODE_LMP_LMNP':
                                if($formeActivite->getCode() === 'CODE_COMMERCIALE'){
                                    $select = true;
                                }
                                break;
                            default:
                                break;
                        }

                        if($select){
                            $options .= '<option value="' . $formeActivite->getId() . '" '."selected".' data-code="'.$code.'">' . $formeActivite->getLibelle() . '</option>';
                            $select = false;
                        }
                        else{
                            $options .= '<option value="' . $formeActivite->getId() . '" data-code="'.$code.'">' . $formeActivite->getLibelle() .'</option>';
                        }
                    }
                }

                $options .='</select>';

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function formeJuridiqueAction(Request $request,$json, $jqGrid, $withSiren)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else
            {

                if($withSiren) {
                    $formesJuridiques = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeJuridique')
                        ->findBy(array(), array('libelle' => 'asc'));
                }
                else{
                    $formesJuridiques = $this->getDoctrine()
                        ->getRepository('AppBundle:FormeJuridique')
                        ->findBy(array('code' => array('CODE_CE', 'CODE_AUTRE', 'CODE_INDIVISION')), array('libelle' => 'asc'));
                }

                $options = '';

                if($jqGrid == 1) {
                    $options = '<select>';
                }


                $options .='<option></option>';
                foreach ($formesJuridiques as $formeJuridique)
                {
                    $options .='<option value="'.$formeJuridique->getId().'" data-code="'.$formeJuridique->getCode().'">'.$formeJuridique->getLibelle().'</option>';
                }



                if($jqGrid ==1) {
                    $options .= '</select>';
                }

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function mandataireComplementaireAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {

            $options = '<select>
                        <option></option>
                        <option value="0">Madelin</option>
                        <option value="1">Perco</option>
                        <option value="2">Autres</option>
                    </select>';

            return new Response($options);
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function mandataireMandataireAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else
            {
                $mandataires = $this->getDoctrine()
                    ->getRepository('AppBundle:Mandataire')
                    ->findAll();

                $options = '<select>';

                $options .='<option></option>';
                foreach ($mandataires as $mandataire)
                {
                    $options .= '<option value="'.$mandataire->getId().'">' .$mandataire->getLibelle() .'</option>';
                }

                $options .= '</select>';

                return new Response($options);
            }

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function mandataireStatutAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else
            {
                $mandataireStatuts = $this->getDoctrine()
                    ->getRepository('AppBundle:MandataireStatut')
                    ->findAll();

                $options = '<select>';

                $options .= '<option></option>';
                
                foreach ($mandataireStatuts as $mandataireStatut)
                {
                    $options .= '<option value="'.$mandataireStatut->getId().'">' .$mandataireStatut->getLibelle() .'</option>';
                }

                $options .= '</select>';

                return new Response($options);
            }

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function methodeSuiviChequeAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            $methodeSuiviCheques = $this->getDoctrine()
                ->getRepository('AppBundle:MethodeSuiviCheque')
                ->findBy(array(), array('libelle'=> 'ASC'));

            if($json == 1) {
                return new Response();
            }
            else{
                $options = '<select>';

                $options .='<option></option>';

                foreach ($methodeSuiviCheques as $methodeSuiviCheque){
                    $options .= '<option value="'.$methodeSuiviCheque->getId().'">'.$methodeSuiviCheque->getLibelle().'</option>';
                }

                $options .='</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function modeVenteAction(Request $request,$json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else
            {
                $modeVentes = $this->getDoctrine()
                    ->getRepository('AppBundle:ModeVente')
                    ->findBy(array(), array('libelle' => 'asc'));
                $options = '<select>';

                $options .='<option></option>';
                foreach ($modeVentes as $modeVente)
                {
                    $options .='<option value="'.$modeVente->getId().'">'.$modeVente->getLibelle().'</option>';
                }

                $options .='</select>';

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function natureActiviteAction(Request $request, $json)
    {
        if ($request->isXmlHttpRequest())
        {

            if($json == 1) {

                $natureActivites = $this->getDoctrine()
                    ->getRepository('AppBundle:NatureActivite')
                    ->findBy(array(), array('libelle' => 'asc'));

                $rows = array();

                foreach ($natureActivites as $natureActivite) {
                    $rows[] = array(
                        'id' => $natureActivite->getId(),
                        'cell' => array(
                            $natureActivite->getLibelle(),
                            '<i class="fa fa-trash icon-action js-delete-regimeImposition" title="Supprimer"></i>'
                        )
                    );
                }

                $liste = array('rows' => $rows);

                return new JsonResponse($liste);
            }
            else{


                $post = $request->request;

                $codeFiscal = $post->get('codeFiscal');

                $dossierId = Boost::deboost($post->get('dossierId'), $this);
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($dossierId);

                if($codeFiscal == "") {
                    $natureActivites = $this->getDoctrine()
                        ->getRepository('AppBundle:NatureActivite')
                        ->findBy(array(), array('libelle' => 'asc'));
                }
                else{
                    $natureActivites = $this->getDoctrine()
                        ->getRepository('AppBundle:NatureActivite')
                        ->getNatureActiviteByRegimeFiscal($codeFiscal);
                }



                $options = '<select>';

                $options .='<option></option>';



                $select = false;
                foreach ($natureActivites as $natureActivite)
                {
                    if(null !== $dossier){
                        if(null !== $dossier->getNatureActivite()){
                            if($dossier->getNatureActivite()->getId() == $natureActivite->getId()) {
                                $select = true;
                            }
                        }
                    }

                    $code = "";
                    if(null !== $natureActivite->getCode()){
                        $code = $natureActivite->getCode();
                    }

                    if($select){
                        $options .= '<option value="' . $natureActivite->getId() . '" '."selected".' data-code="'.$code.'">' . $natureActivite->getLibelle() . '</option>';
                        $select = false;
                    }
                    else {

                        if($codeFiscal === 'CODE_BA'){
                            if($natureActivite->getCode() === 'CODE_AGRICOLE'){
                                $select = true;
                            }
                        }
                        else if($codeFiscal === 'CODE_LMP_LMNP'){
                            if($natureActivite->getCode() === 'CODE_SERVICE'){
                                $select = true;
                            }
                        }

                        if($select){
                            $options .= '<option value="' . $natureActivite->getId() . '" '."selected".' data-code="'.$code.'">' . $natureActivite->getLibelle() . '</option>';
                            $select = false;
                        }
                        else {
                            $options .= '<option value="' . $natureActivite->getId() . ' data-code="'.$code.'">' . $natureActivite->getLibelle() .'</option>';
                        }
                    }
                }

                $options .= '</select>';

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function noteFraisAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $notesFrais = $this->getDoctrine()
                ->getRepository('AppBundle:NoteDeFrais')
                ->findAll();

            $rows = array();

            foreach ($notesFrais as $noteDeFrais)
            {
                $rows[] = array(
                    'id'=>$noteDeFrais->getId(),
                    'cell'=> array(
                        $noteDeFrais->getLibelle(),
                        '<i class="fa fa-trash icon-action js-delete-noteFrais" title="Supprimer"></i>'
                    )
                );


            }

            $liste = array('rows'=>$rows);
            return new JsonResponse($liste);

        }
        else
        {
            throw new AccessDeniedException("Accès refusé");
        }
    }

    public function noteFraisEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id', "");
            $libelle = $request->request->get("noteFrais-libelle");

            $em = $this->getDoctrine()
                ->getManager();

            if ($id != "")
            {
                if ($id != "new_row")
                {
                    $noteFrais = $this->getDoctrine()
                        ->getRepository('AppBundle:NoteDeFrais')
                        ->find($id);
                    if ($noteFrais)
                    {
                        $noteFrais->setLibelle($libelle);
                    }
                }
                else
                {
                    $noteFrais = new NoteDeFrais();
                    $noteFrais->setLibelle($libelle);
                    $em->persist($noteFrais);
                }

                $em->flush();
                $data = array(
                    'erreur' => false
                );
                return new JsonResponse(json_encode($data));
            }
            throw new NotFoundHttpException("Contrat Prevoaynce introuvable.");

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }

    }

    public function noteFraisRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $noteFrais = $this->getDoctrine()
                    ->getRepository('AppBundle:NoteDeFrais')
                    ->find($id);

                if ($noteFrais)
                {
                    $em->remove($noteFrais);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );
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
            throw new NotFoundHttpException("Note de frais introuvable.");
        }
        else
        {
            return new AccessDeniedException('Accès refusé.');
        }
    }

    public function ouiNonAction(Request $request, $json, $indifferent, $sinecessaire)
    {
        if($request->isXmlHttpRequest())
        {

            if($json == 1) {
                return new Response();
            }
            else{
                $options = '<select>';

                $options .='<option></option>';

                $options .='<option value="0">Non</option>';
                $options .='<option value="1">Oui</option>';

                if($indifferent == 1){
                    $options .='<option value="2">Indifférent</option>';
                }

                if($sinecessaire == 1){
                    $options .='<option value="2">Si nécessaire</option>';
                }

                $options .='</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function periodiciteAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if($json == 1) {
                return new Response();
            }
            else{
                $options = '<select>';

                $options .='<option></option>';

                $options .='<option value="1">Mensuelle</option>';
                $options .='<option value="2">Trimestrielle</option>';
                $options .='<option value="3">Semestrielle</option>';
                $options .='<option value="4">Annuelle</option>';
                $options .='<option value="5">Ponctuelle</option>';

                $options .='</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }


    public function typePrestationAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            $typePrestations = $this->getDoctrine()
                ->getRepository('AppBundle:TypePrestation')
                ->findBy(array(), array('id' => 'asc'));

            if($json == 1) {
                return new Response();
            }
            else{

                $options = '<select>';
                $options .='<option></option>';

                foreach ($typePrestations as $typrePrestation)
                {
                    $options .= '<option value="'.$typrePrestation->getId().'">' .$typrePrestation->getLibelle() .'</option>';
                }

                $options .= '</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function professionLiberaleCategorieAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if ($json == 1)
            {
                return new Response();
            }
            else
            {
                $plCategories = $this->getDoctrine()
                    ->getRepository('AppBundle:ProfessionLiberaleCat')
                    ->findAll();

                $options = '<select>';

                foreach ($plCategories as $plCategory)
                {
                    $options .= '<option value="' . $plCategory->getId() . '">' . $plCategory->getLibelle() . '</option>';
                }
                $options .= '</select>';
                return new Response($options);
            }


        }

        else
        {
            throw new AccessDeniedException("Accès refusé");
        }
    }

    public function professionLiberaleAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            $professionLiberales = $this->getDoctrine()
                ->getRepository('AppBundle:ProfessionLiberale')
                ->findBy(array(), array('libelle'=>'ASC'));

            if($json == 1) {
                $rows = array();

                foreach ($professionLiberales as $professionLiberale) {
                    $rows[] = array(
                        'id' => $professionLiberale->getId(),
                        'cell' => array(
                            $professionLiberale->getLibelle(),
                            $professionLiberale->getAlpha(),
                            $professionLiberale->getProfessionLiberaleCat()->getLibelle(),
                            '<i class="fa fa-save icon-action js-save-professionLiberale" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-professionLiberale" title="Supprimer"></i>'
                        )
                    );
                }

                $liste = array('rows' => $rows);

                return new JsonResponse($liste);
            }
            else{
                $options = '<select>';

                $options .='<option></option>';

                foreach ($professionLiberales as $profLib)
                {
                    $options .= '<option value="'.$profLib->getId().'">' .$profLib->getLibelle() .'</option>';
                }

                $options .= '</select>';

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedException('Accès refusé.');
        }
    }
    
    public function professionLiberaleEditAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            $libelle = $request->request->get('pl-libelle');
            $alpha = $request->request->get('pl-alpha');
            $categorieId = $request->request->get('pl-categorie');

            $em = $this->getDoctrine()->getManager();

            if($id !='')
            {
                    try
                    {
                        if($id != 'new_row')
                        {
                            $professionLib = $this->getDoctrine()
                            ->getRepository('AppBundle:ProfessionLiberale')
                            ->find($id);

                            $professionLib->setLibelle($libelle);
                            $professionLib->setAlpha($alpha);

                            if($categorieId !='')
                            {
                                $categorie = $this->getDoctrine()
                                    ->getRepository('AppBundle:ProfessionLiberaleCat')
                                    ->find($categorieId);

                                $professionLib->setProfessionLiberaleCat($categorie);
                            }

                            $em->persist($professionLib);
                            $em->flush();

                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));

                        }
                        else
                        {
                            $professionLib = new ProfessionLiberale();

                            $professionLib->setLibelle($libelle);
                            $professionLib->setAlpha($alpha);

                            if($categorieId !='')
                            {
                                $categorie = $this->getDoctrine()
                                    ->getRepository('AppBundle:ProfessionLiberaleCat')
                                    ->find($categorieId);

                                $professionLib->setProfessionLiberaleCat($categorie);
                            }

                            $em->persist($professionLib);
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
    
    public function professionLiberaleRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id', '');

            $em = $this->getDoctrine()
                ->getManager();
            if ($id != '')
            {
                try
                {
                    //Suppression
                    if ($id != 'new_row')
                    {
                        $professionLiberale = $this->getDoctrine()
                            ->getRepository('AppBundle:ProfessionLiberale')
                            ->find($id);
                        if ($professionLiberale) {
                            $em->remove($professionLiberale);
                            $em->flush();
                            $data = array(
                                'erreur' => false,
                            );
                            return new JsonResponse(json_encode($data));
                        }
                    }
                }
                catch (\Exception $ex)
                {
                    return new Response("$ex->getMessage()", 500);
                }

            }
            throw new NotFoundHttpException("Domaine introuvable.");
        }
        else
        {
            throw  new AccessDeniedException('Accès refusé.');
        }
    }

    public function regimeImpositionAction(Request $request, $json)
    {
        if ($request->isXmlHttpRequest())
        {

            $post = $request->request;

            $codeFiscal = $post->get('codeFiscal');
            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if($codeFiscal == "") {
                $impositions = $this->getDoctrine()
                    ->getRepository('AppBundle:RegimeImposition')
                    ->findBy(array(), array('libelle' => 'asc'));
            }
            else {

                if ($codeFiscal !== "CODE_BNC") {
                    if($codeFiscal === "CODE_NS"){
                        $impositions = array();
                    }
                    else {
                        $impositions = $this->getDoctrine()
                            ->getRepository('AppBundle:RegimeImposition')
                            ->findAll();
                    }
                }
                else{

                    $impositionMicros = $this->getDoctrine()
                        ->getRepository('AppBundle:RegimeImposition')
                        ->findBy(array('libelle' => 'Micro'));

                    $impostionNormals = $this->getDoctrine()
                        ->getRepository('AppBundle:RegimeImposition')
                        ->findBy(array("libelle" => "Regime normal"));

                    $impositions = array();
                    if(count($impositionMicros) > 0){
                        $impositions[] = $impositionMicros[0];
                    }

                    if(count($impostionNormals) > 0){
                        $impositions[] = $impostionNormals[0];
                    }


                }

            }

            if($json == 1) {

                $rows = array();

                foreach ($impositions as $imposition) {
                    $rows[] = array(
                        'id' => $imposition->getId(),
                        'cell' => array(
                            $imposition->getLibelle(),
                            '<i class="fa fa-trash icon-action js-delete-regimeImposition" title="Supprimer"></i>'
                        )
                    );
                }

                $liste = array('rows' => $rows);

                return new JsonResponse($liste);
            }
            else{

                $options = '<select>';

                $options .='<option></option>';

                foreach ($impositions as $imposition)
                {
                    $select = false;


                    if(!is_null($dossier)){

                        if(!is_null($dossier->getRegimeImposition())){
                            if($dossier->getRegimeImposition()->getId() == $imposition->getId()){
                                $select = true;
                            }
                        }

                    }

                    if($select){
                        $options .= '<option value="'.$imposition->getId().'"'."selected".' >' .$imposition->getLibelle() .'</option>';
                    }
                    else {
                        $options .= '<option value="' . $imposition->getId() . '">' . $imposition->getLibelle() . '</option>';
                    }
                }

                $options .= '</select>';

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function regimeImpositionEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id', "");
            $libelle = $request->request->get("regimeImposition-libelle");
            $em = $this->getDoctrine()
                ->getManager();
            if ($id != "")
            {
                if ($id != "new_row")
                {
                    $regime = $this->getDoctrine()
                        ->getRepository('AppBundle:RegimeImposition')
                        ->find($id);
                    if ($regime)
                    {
                        $regime->setLibelle($libelle);
                    }
                }
                else
                {
                    $regime = new RegimeImposition();
                    $regime->setLibelle($libelle);
                    $em->persist($regime);
                }

                $em->flush();
                $data = array(
                    'erreur' => false
                );
                return new JsonResponse(json_encode($data));
            }
            throw new NotFoundHttpException("Regime imposition introuvable.");

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }

    }

    public function regimeImpositionRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');

            $data = array(
                'erreur' => true,
                'erreur_text' => "Regime imposition introuvable",
            );

            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $regimeimp = $this->getDoctrine()
                    ->getRepository('AppBundle:RegimeImposition')
                    ->find($id);

                if ($regimeimp)
                {
                    $em->remove($regimeimp);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );
                }
                else
                {
                    $data = array(
                        'erreur' => true,
                        'erreur_text' => "Regime imposition introuvable",
                    );
                    return new JsonResponse(json_encode($data), 404);
                }
            }
            return new JsonResponse(json_encode($data));
        }
        else
        {
            return new AccessDeniedException('Accès refusé.');
        }
    }

    public function regimeFiscalAction(Request $request, $json)
    {
        if ($request->isXmlHttpRequest())
        {

            $regimes = $this->getDoctrine()
                ->getRepository('AppBundle:RegimeFiscal')
                ->findBy(array(), array('libelle' => 'asc'));

            if($json == 1) {


                $rows = array();

                foreach ($regimes as $regime) {
                    $rows[] = array(
                        'id' => $regime->getId(),
                        'cell' => array(
                            $regime->getLibelle(),
                            '<i class="fa fa-trash icon-action js-delete-regimeFiscal" title="Supprimer"></i>'
                        )
                    );
                }
                $liste = array(
                    'rows' => $rows,
                );

                return new JsonResponse($liste);
            }

            else{
                $options = '<select>';

                $options .='<option></option>';

                foreach ($regimes as $regime)
                {
                    $options .= '<option value="'.$regime->getId().'">' .$regime->getLibelle() .'</option>';
                }

                $options .= '</select>';

                return new Response($options);
            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function regimeFiscalEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) 
        {
            $id = $request->request->get('id', "");
            $libelle = $request->request->get("regimeFiscal-libelle");
            $em = $this->getDoctrine()
                ->getManager();
            if ($id != "")
            {
                if ($id != "new_row")
                {
                    $regime = $this->getDoctrine()
                        ->getRepository('AppBundle:RegimeFiscal')
                        ->find($id);
                    if ($regime)
                    {
                        $regime->setLibelle($libelle);
                    }
                }
                else
                {
                    $regime = new RegimeFiscal();
                    $regime->setLibelle($libelle);
                    $em->persist($regime);
                }

                $em->flush();
                $data = array(
                    'erreur' => false
                );
                return new JsonResponse(json_encode($data));
            }
            throw new NotFoundHttpException("Regime fiscal introuvable.");

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }

    }

    public function regimeFiscalRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');

            $data = array(
                'erreur' => true,
                'erreur_text' => "Regime fiscal introuvable",
            );

            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $regime = $this->getDoctrine()
                    ->getRepository('AppBundle:RegimeFiscal')
                    ->find($id);

                if ($regime)
                {
                    $em->remove($regime);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );
                }
                else
                {
                    $data = array(
                        'erreur' => true,
                        'erreur_text' => "Regime fiscal introuvable",
                    );
                    return new JsonResponse(json_encode($data), 404);
                }
            }
            return new JsonResponse(json_encode($data));
        }
        else
        {
            return new AccessDeniedException('Accès refusé.');
        }
    }

    public function regimeSuiviAction(Request $request,$json)
    {
        if($request->isXmlHttpRequest())
        {
            $suivis = $this->getDoctrine()->getRepository('AppBundle:RegimeSuivi')->findAll();
            if($json ==1)
            {
                $rows = array();
                foreach ($suivis as $suivi)
                {
                    $rows[] = array(
                        'id' => $suivi->getId(),
                        'cell'=> array(
                            $suivi->getLibelle(),
                            '<i class="fa fa-trash icon-action js-delete-regimeSuivi" title="Supprimer"></i>'
                        )
                    );
                }
                $liste = array('rows'=>$rows);

                return new JsonResponse($liste);
            }
            else
            {

                $options = '<select>';

                $options .='<option></option>';

                foreach ($suivis as $suivi)
                {
                    $options .= '<option value="'.$suivi->getId().'">' .$suivi->getLibelle() .'</option>';
                }

                $options .= '</select>';

                return new Response($options);
            }

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function regimeSuiviEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            $libelle = $request->request->get('regimeSuivi-libelle');

            $em = $this->getDoctrine()->getManager();
            if($id != "")
            {
                if($id != "new_row")
                {
                    $regimeSuivi = $em->getRepository('AppBundle:RegimeSuivi')
                        ->find($id);
                    if($regimeSuivi)
                    {
                        $regimeSuivi->setLibelle($libelle);
                    }
                }
                else
                {
                    $regimeSuivi = new RegimeSuivi();
                    $regimeSuivi->setLibelle($libelle);
                    $em->persist($regimeSuivi);
                }

                $em->flush();
                $data = array('erreur' => false);
                return new JsonResponse(json_encode($data));
            }

            throw new NotFoundHttpException("Regime Suivi introuvable.");


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function regimeSuiviRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');

            $data = array(
                'erreur' => true,
                'erreur_text' => "Regime suivi introuvable",
            );

            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $regimeSuivi = $this->getDoctrine()
                    ->getRepository('AppBundle:RegimeSuivi')
                    ->find($id);

                if ($regimeSuivi)
                {
                    $em->remove($regimeSuivi);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );
                }
                else
                {
                    $data = array(
                        'erreur' => true,
                        'erreur_text' => "Regime suivi introuvable",
                    );
                    return new JsonResponse(json_encode($data), 404);
                }
            }
            return new JsonResponse(json_encode($data));
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function regimeTvaAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            $tvas = $this->getDoctrine()->getRepository('AppBundle:RegimeTva')->findBy(array(), array('libelle' => 'asc'));

            if($json == 1) {

                $rows = array();

                foreach ($tvas as $tva) {
                    $rows[] = array(
                        'id' => $tva->getId(),
                        'cell' => array(
                            $tva->getLibelle(),
                            '<i class="fa fa-trash icon-action js-delete-regimeTva" title="Supprimer"></i>'
                        )
                    );
                }

                $liste = array('rows' => $rows);

                return new JsonResponse($liste);
            }
            else{
                $options = '<select>';

                $options .='<option></option>';
                foreach ($tvas as $tva)
                {
                    $options .='<option value="'.$tva->getId().'">'.$tva->getLibelle().'</option>';
                }

                $options .='</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function regimeTvaEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            $libelle = $request->request->get('regimeTva-libelle');

            $em = $this->getDoctrine()->getManager();
            if($id != "")
            {
                if($id != "new_row")
                {
                    $regimeTva = $em->getRepository('AppBundle:RegimeTva')
                        ->find($id);
                    if($regimeTva)
                    {
                        $regimeTva->setLibelle($libelle);
                    }
                }
                else
                {
                    $regimeTva = new RegimeTva();
                    $regimeTva->setLibelle($libelle);
                    $em->persist($regimeTva);
                }

                $em->flush();
                $data = array('erreur' => false);
                return new JsonResponse(json_encode($data));
            }

            throw new NotFoundHttpException("Regime Tva introuvable.");


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function regimeTvaRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $regimeTva = $this->getDoctrine()
                    ->getRepository('AppBundle:RegimeTva')
                    ->find($id);

                if ($regimeTva)
                {
                    $em->remove($regimeTva);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );
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
            return new JsonResponse(json_encode($data));
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function responsableAction(Request $request,$typeResponsable,$dossierId)
    {
        if($request->isXmlHttpRequest())
        {
            $idDossier = Boost::deboost($dossierId, $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($idDossier);

            //responsable
            if(intval($typeResponsable) == 1)
            {
                $responsableDossiers = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')//->findAll();
                    ->getResponsableDossier($typeResponsable,$dossier);

                $rows = array();


                $site = $dossier->getSite();

                $responsableSites = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->findBy(array('site'=>$site));


                foreach ($responsableSites as $responsableSite){
                    $email = $responsableSite->getEmail();
                    if(strpos($email, 'scriptura.biz') === false){
                        $responsableType = 'EC Site';


                        $bouton = '<i class="fa fa-save icon-action js-save-responsableDossier" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-responsableDossier" title="Supprimer"></i>';

                        $libelleResponsableCsdTire = '';
                        if(!is_null($responsableSite->getResponsableCsdTitre()))
                        {
                            $libelleResponsableCsdTire = $responsableSite->getResponsableCsdTitre()->getLibelle();
                        }



                        $envoi = false;
                        if($responsableSite->getEnvoiMail() == 1){
                            $envoi = true;
                        }


                        $rows[] = array(
                            'id'=>$responsableSite->getId(),
                            'cell'=>array(
                                $responsableSite->getNom(),
                                $responsableSite->getPrenom(),
                                $responsableSite->getEmail(),
                                $responsableType,
                                $libelleResponsableCsdTire,
                                $envoi,
                                $bouton
                            )
                        );
                    }

                }

                foreach ($responsableDossiers as $responsableDossier)
                {
                    $email = $responsableDossier->getEmail();
                    if(strpos($email, 'scriptura.biz') === false){
                        $bouton='';

                        /* @var $responsableDossier ResponsableCsd*/

                        //2:dossier; 3:supervision; 4:administratif

                        $responsableType = '';
                        switch ($responsableDossier->getTypeCsd()) {
                            case 2:
                                $responsableType = 'EC Dossier';
    //                            $bouton = '<i class="fa fa-save icon-action js-save-responsableDossier" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-responsableDossier" title="Supprimer"></i>';
                                break;
                            case 3:
                                $responsableType = 'EC Supervision';
    //                            $bouton = '<i class="fa fa-save icon-action js-save-responsableSupervision" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-responsableSupervision" title="Supprimer"></i>';
                                break;
                            case 4:
                                $responsableType= 'EC Admin';
    //                            $bouton = '<i class="fa fa-save icon-action js-save-responsableAdministratif" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-responsableAdministratif" title="Supprimer"></i>';
                                break;

                            case 6:
                                $responsableType = 'CF Client';
                                break;

                        }
                        $bouton = '<i class="fa fa-save icon-action js-save-responsableDossier" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-responsableDossier" title="Supprimer"></i>';

                        $libelleResponsableCsdTire = '';
                        if(!is_null($responsableDossier->getResponsableCsdTitre()))
                        {
                            $libelleResponsableCsdTire = $responsableDossier->getResponsableCsdTitre()->getLibelle();
                        }



                        $envoi = false;
                        if($responsableDossier->getEnvoiMail() == 1){
                            $envoi = true;
                        }


                        $rows[] = array(
                            'id'=>$responsableDossier->getId(),
                            'cell'=>array(
                                $responsableDossier->getNom(),
                                $responsableDossier->getPrenom(),
                                $responsableDossier->getEmail(),
                                $responsableType,
                                $libelleResponsableCsdTire,
                                $envoi,
                                $bouton
                            )
                        );
                    }
                }

                $liste = array('rows'=>$rows);
                return new JsonResponse($liste);
            }
            //mandataire
            else if(intval($typeResponsable) ==0)
            {
                $responsableDossiers = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    //->findAll();
                    ->getMandataire($dossier);

                $rows = array();

                foreach ($responsableDossiers as $mandataire)
                {
                    $email = $mandataire->getEmail();
                    if(strpos($email, 'scriptura.biz') === false){
                        /* @var $mandataire ResponsableCsd*/
                        $complementaire = 'Autres';

                        try {
                            switch ($mandataire->getComplementaire()) {
                                case 0:
                                    $complementaire = 'Madelin';
                                    break;
                                case 1:
                                    $complementaire = 'Perco';
                                    break;
                                case 2:
                                    $complementaire = 'Autres';
                                    break;
                            }
                        }
                        catch (Exception $e)
                        {}

                        $libelleStatutMandataire ='';
                        if(!is_null($mandataire->getMandataireStatut()))
                        {
                            $libelleStatutMandataire = $mandataire->getMandataireStatut()->getLibelle();
                        }

                        $libelleRegimeSuivi  ='';
                        if(!is_null($mandataire->getRegimeSuivi()))
                        {
                            $libelleRegimeSuivi = $mandataire->getRegimeSuivi()->getLibelle();
                        }

                        $libelleMandataire = '';
                        if(!is_null($mandataire->getMandataire()))
                        {
                            $libelleMandataire = $mandataire->getMandataire()->getLibelle();
                        }

                        $rows[] = array(
                            'id'=>$mandataire->getId(),
                            'cell'=>array(
                                $libelleMandataire,
                                $libelleStatutMandataire,
                                $libelleRegimeSuivi,
                                $complementaire,
                                $mandataire->getNom(),
                                $mandataire->getPrenom(),
                                $mandataire->getEmail(),
                                '<i class="fa fa-save icon-action js-save-mandataire" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-mandataire" title="Supprimer"></i>'
                            ));
                    }
                }

                $liste = array('rows'=>$rows);
                return new JsonResponse($liste);
            }


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

//    public function responsableEditAction(Request $request, $typeResponsable,$typeCsd,$dossierId)
    public function responsableEditAction(Request $request, $typeResponsable,$dossierId)
    {
        if($request->isXmlHttpRequest())
        {
            if($dossierId =='0')
            {
                $data = array(
                    'id' => '0000',
                );
                return new JsonResponse(json_encode($data));
            }

            $id = $request->request->get('id');

            $nom='';$prenom='';$email='';$mandataireId='';$mandataireStatutId='';
            $mandataireRegimeSuiviId='';$mandataireComplementaire='';$titre='';

            //Mandataire
            if($typeResponsable ==0)
            {
                $nom = $request->request->get('mandataire-nom');
                $prenom = $request->request->get('mandataire-prenom');
                $email = $request->request->get('mandataire-email');

                $mandataireId = $request->request->get('mandataire-mandataire');
                $mandataireStatutId = $request->request->get('mandataire-statut-social');
                $mandataireRegimeSuiviId = $request->request->get('mandataire-regime-suivi');
                $mandataireComplementaire = $request->request->get('mandataire-complementaire');
            }

            //Responsable
            else
            {


                //Dossier
//                if($typeCsd == 2)
//                {
                    $nom = $request->request->get('responsableDossier-nom');
                    $prenom = $request->request->get('responsableDossier-prenom');
                    $email = $request->request->get('responsableDossier-email');
                    $titre = $request->request->get('responsableDossier-titre');
                    $typeCsd = $request->request->get('responsableDossier-type');

                    $envoiMail = $request->request->get('responsableDossier-envoi');

                    $envoi = 0;
                    if($envoiMail == "Yes"){
                        $envoi = 1;
                    }
                
//                }
//                //Suprevision
//                else if($typeCsd ==3)
//                {
//                    $nom = $request->request->get('responsableSupervision-nom');
//                    $prenom = $request->request->get('responsableSupervision-prenom');
//                    $email = $request->request->get('responsableSupervision-email');
//                    $titre = $request->request->get('responsableSupervision-titre');
//                }
//                //Administratif
//                else if($typeCsd ==4)
//                {
//                    $nom = $request->request->get('responsableAdministratif-nom');
//                    $prenom = $request->request->get('responsableAdministratif-prenom');
//                    $email = $request->request->get('responsableAdministratif-email');
//                    $titre = $request->request->get('responsableAdministratif-titre');
//                }
            }

            if($id !='')
            {
                try
                {
                    if($id != 'new_row')
                    {
                        $em = $this->getDoctrine()->getManager();

                        $responsable = $this->getDoctrine()
                            ->getRepository('AppBundle:ResponsableCsd')
                            ->find($id);



                        $utilisateur = $this->getUser();

                        if($dossierId !='0')
                        {
                            $idDossier = Boost::deboost($dossierId, $this);

                            $dossier = $this->getDoctrine()
                                ->getRepository('AppBundle:Dossier')
                                ->find($idDossier);

                            $site = $dossier->getSite();





                            //**************ENREGISTREMENT LOG**************\

                            if ($dossier->getAccuseCreation()>=1) {

                                if($responsable->getTypeCsd() != $typeCsd){


                                    $log = new LogInfoperdos();
                                    $log->setDate(new \DateTime());
                                    $log->setDossier($dossier);
                                    $log->setUtilisateur($utilisateur);
                                    $log->setTab(1);
                                    $log->setBloc(3);

                                    $oldVal = '';
                                    switch ($responsable->getTypeCsd()) {
                                        case 0:
                                            $oldVal = 'Client';
                                            break;
                                        case 1:
                                            $oldVal = 'EC Site';
                                            break;

                                        case 2:
                                            $oldVal = 'EC Dossier';
                                            break;
                                        case 3:
                                            $oldVal = 'EC Supervision';
                                            break;
                                        case 4:
                                            $oldVal = 'EC Admin';
                                            break;
                                        case 5:
                                            $oldVal = 'Scriptura';
                                            break;
                                        case 6:
                                            $oldVal = 'CF Client';
                                            break;
                                    }

                                    $newVal = '';
                                    switch ($typeCsd) {
                                        case 0:
                                            $newVal = 'Client';
                                            break;
                                        case 1:
                                            $newVal = 'EC Site';
                                            break;

                                        case 2:
                                            $newVal = 'EC Dossier';
                                            break;
                                        case 3:
                                            $newVal = 'EC Supervision';
                                            break;
                                        case 4:
                                            $newVal = 'EC Admin';
                                            break;
                                        case 5:
                                            $newVal = 'Scriptura';
                                            break;
                                        case 6:
                                            $newVal = 'CF Client';
                                            break;
                                    }


                                    $log->setChamp('Responsable Dossier/Responsable');
                                    $log->setValeurAncien($oldVal);
                                    $log->setValeurNouveau($newVal);

                                    $em->persist($log);
                                    $em->flush();
                                }


                                if($responsable->getNom() != $nom){
                                    $log = new LogInfoperdos();
                                    $log->setDate(new \DateTime());
                                    $log->setDossier($dossier);
                                    $log->setUtilisateur($utilisateur);
                                    $log->setTab(1);
                                    $log->setBloc(3);

                                    $oldVal = '';
                                    if($responsable->getNom()){
                                        $oldVal = $responsable->getNom();
                                    }

                                    $newVal = '';
                                    if(!is_null($nom)){
                                        $newVal = $nom;
                                    }

                                    $log->setChamp('Responsable Dossier/Nom');
                                    $log->setValeurAncien($oldVal);
                                    $log->setValeurNouveau($newVal);

                                    $em->persist($log);
                                    $em->flush();
                                }

                                if($responsable->getPrenom() != $prenom){
                                    $log = new LogInfoperdos();
                                    $log->setDate(new \DateTime());
                                    $log->setDossier($dossier);
                                    $log->setUtilisateur($utilisateur);
                                    $log->setTab(1);
                                    $log->setBloc(3);

                                    $oldVal = '';
                                    if($responsable->getPrenom()){
                                        $oldVal = $responsable->getPrenom();
                                    }

                                    $newVal = '';
                                    if(!is_null($prenom)){
                                        $newVal = $prenom;
                                    }

                                    $log->setChamp('Responsable Dossier/Prenom');
                                    $log->setValeurAncien($oldVal);
                                    $log->setValeurNouveau($newVal);

                                    $em->persist($log);
                                    $em->flush();
                                }

                                if($responsable->getEmail() != $email){
                                    $log = new LogInfoperdos();
                                    $log->setDate(new \DateTime());
                                    $log->setDossier($dossier);
                                    $log->setUtilisateur($utilisateur);
                                    $log->setTab(1);
                                    $log->setBloc(3);

                                    $oldVal = '';
                                    if($responsable->getEmail()){
                                        $oldVal = $responsable->getEmail();
                                    }

                                    $newVal = '';
                                    if(!is_null($email)){
                                        $newVal = $email;
                                    }

                                    $log->setChamp('Responsable Dossier/Email');
                                    $log->setValeurAncien($oldVal);
                                    $log->setValeurNouveau($newVal);

                                    $em->persist($log);
                                    $em->flush();
                                }

                                if($responsable->getEnvoiMail() != $envoi) {
                                    $log = new LogInfoperdos();
                                    $log->setDate(new \DateTime());
                                    $log->setDossier($dossier);
                                    $log->setUtilisateur($utilisateur);
                                    $log->setTab(1);
                                    $log->setBloc(3);

                                    $oldVal = '';

                                    switch ($responsable->getEnvoiMail()) {
                                        case 1:
                                            $oldVal = 'Oui';
                                            break;

                                        case 0:
                                            $oldVal = 'Non';
                                            break;

                                    }

                                    $newVal = '';

                                    switch ($envoi) {
                                        case 1:
                                            $newVal = 'Oui';
                                            break;

                                        case 0:
                                            $newVal = 'Non';
                                            break;

                                    }


                                    $log->setChamp('Responsable Dossier/Email');
                                    $log->setValeurAncien($oldVal);
                                    $log->setValeurNouveau($newVal);

                                    $em->persist($log);
                                    $em->flush();
                                }


                            }


                            if($typeCsd == 1){
                                $responsable->setSite($site);
                                $responsable->setDossier(null);
                            }
                            else {
                                $responsable->setDossier($dossier);
                                $responsable->setSite(null);
                            }



                        }


                        $responsable->setNom($nom);
                        $responsable->setPrenom($prenom);
                        $responsable->setEmail($email);

                        if($mandataireId !='')
                        {
                            $mandataire = $this->getDoctrine()
                                ->getRepository('AppBundle:Mandataire')
                                ->find($mandataireId);

                            $responsable->setMandataire($mandataire);
                        }

                        if($mandataireStatutId !='')
                        {
                            $mandataireStatut = $this->getDoctrine()
                                ->getRepository('AppBundle:MandataireStatut')
                                ->find($mandataireStatutId);

                            $responsable->setMandataireStatut($mandataireStatut);
                        }

                        if($mandataireRegimeSuiviId !='')
                        {
                            $mandataireRegimeSuivi = $this->getDoctrine()
                                ->getRepository('AppBundle:RegimeSuivi')
                                ->find($mandataireRegimeSuiviId);

                            $responsable->setRegimeSuivi($mandataireRegimeSuivi);
                        }

                        if($mandataireComplementaire !='')
                        {
                            $responsable->setComplementaire($mandataireComplementaire);
                        }

                        $responsable->setTypeResponsable($typeResponsable);

                        $responsable->setEnvoiMail($envoi);

                        //Responsable
                        if($typeResponsable ==1)
                        {
                            if($titre!='')
                            {
                                $responsableTitre = $this->getDoctrine()
                                    ->getRepository('AppBundle:ResponsableCsdTitre')
                                    ->find($titre);

                                $responsable->setResponsableCsdTitre($responsableTitre);
                            }
                            if ($typeCsd!='')
                            {
                                $responsable->setTypeCsd($typeCsd);
                            }
                        }

                        $em->persist($responsable);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));

                    }

                    else
                    {
                        $em = $this->getDoctrine()->getManager();

                        $responsable = new ResponsableCsd();

                        $responsable->setNom($nom);
                        $responsable->setPrenom($prenom);
                        $responsable->setEmail($email);

                        if($dossierId !='0')
                        {
                            $idDossier = Boost::deboost($dossierId, $this);

                            $dossier = $this->getDoctrine()
                                ->getRepository('AppBundle:Dossier')
                                ->find($idDossier);

                            $site = $dossier->getSite();

                            if($typeCsd == 1){
                                $responsable->setSite($site);
                                $responsable->setDossier(null);
                            }
                            else {
                                $responsable->setDossier($dossier);
                                $responsable->setSite(null);
                            }
                        }

                        if($mandataireId !='')
                        {
                            $mandataire = $this->getDoctrine()
                                ->getRepository('AppBundle:Mandataire')
                                ->find($mandataireId);

                            $responsable->setMandataire($mandataire);
                        }

                        if($mandataireStatutId !='')
                        {
                            $mandataireStatut = $this->getDoctrine()
                                ->getRepository('AppBundle:MandataireStatut')
                                ->find($mandataireStatutId);

                            $responsable->setMandataireStatut($mandataireStatut);
                        }

                        if($mandataireRegimeSuiviId !='')
                        {
                            $mandataireRegimeSuivi = $this->getDoctrine()
                                ->getRepository('AppBundle:RegimeSuivi')
                                ->find($mandataireRegimeSuiviId);

                            $responsable->setRegimeSuivi($mandataireRegimeSuivi);
                        }

                        if($mandataireComplementaire !='')
                        {
                            $responsable->setComplementaire($mandataireComplementaire);
                        }

                        $responsable->setTypeResponsable($typeResponsable);

                        $responsable->setEnvoiMail($envoi);

                        //Responsable
                        if($typeResponsable == 1)
                        {
                            if($titre!='')
                            {
                                $responsableTitre = $this->getDoctrine()
                                    ->getRepository('AppBundle:ResponsableCsdTitre')
                                    ->find($titre);

                                $responsable->setResponsableCsdTitre($responsableTitre);
                            }

                            if ($typeCsd!='')
                            {
                                $responsable->setTypeCsd($typeCsd);
                            }
                        }

                        $em->persist($responsable);
                        $em->flush();

                        $id = $responsable->getId();


                        $data = array(
                            'erreur' => false,
                            'id'=> $id
                        );
                        return new JsonResponse($data);
                    }
                }
                catch(\Exception $ex)
                {
                    return new Response($ex->getMessage(), 500);
                }
            }
            throw new NotFoundHttpException("Mandataire introuvable.");

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function responsableRemoveAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            $data = array(
                'erreur' =>true,
                'erreur_text' => 'Responsable introuvable'
            );

            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $responsable = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsd')
                    ->find($id);

                if ($responsable)
                {
                    $em->remove($responsable);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );
                }
                else
                {
                    $data = array(
                        'erreur' => true,
                        'erreur_text' => "Responsable introuvable",
                    );
                    return new JsonResponse(json_encode($data), 404);
                }
            }
            return new JsonResponse(json_encode($data));
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function responsableTitreAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            if($json ==1)
            {
                return new Response();
            }
            else
            {
                $options = '<select>';

                $options .= '<option></option>';
                $responsableTitres = $this->getDoctrine()
                    ->getRepository('AppBundle:ResponsableCsdTitre')
                    ->findBy(array(), array('libelle' => 'asc'));
                foreach ($responsableTitres as $responsableTitre)
                {
                    $options .='<option value="'.$responsableTitre->getId().'">'.$responsableTitre->getLibelle().'</option>';
                }

                $options .= '</select>';

                return new Response($options);
            }
        }
        else
        {
            throw  new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function responsableTypeAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $options = '<select>';

            $options .= '<option value="1">EC Site</option>';
            $options .= '<option value="2">EC Dossier</option>';
            $options .= '<option value="3">EC Supervision</option>';
            $options .= '<option value="4">EC Admin</option>';
            $options .= '<option value="6">CF Client</option>';


            $options .= '</select>';

            return new Response($options);

        } else {
            throw  new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function managerAction(Request $request, $clientId, $typeresponsable, $typecsd)
    {
        if($request->isXmlHttpRequest())
        {

            $idClient = Boost::deboost($clientId, $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($idClient);

            $responsableCsds = $this->getDoctrine()
                ->getRepository('AppBundle:ResponsableCsd')
                ->findBy(array('typeCsd' => $typecsd, 'typeResponsable' => $typeresponsable, 'client' => $client));

            $rows = array();

            if(is_array($responsableCsds)) {

                $save = '';
                $delete = '';

                //Manager Client
                if(intval($typecsd) === 0 &&	intval($typeresponsable) === 4) {
                    $save = 'js-mc-save';
                    $delete = 'js-mc-delete';
                }
                //Mananger Scriptura
                elseif(intval($typecsd) === 5 &&	intval($typeresponsable) === 4){
                    $save = 'js-ms-save';
                    $delete = 'js-ms-delete';
                }
                //Chef de Mission Client
                elseif(intval($typecsd) === 0 &&	intval($typeresponsable) === 1){
                    $save = 'js-cmc-save';
                    $delete = 'js-cmc-delete';
                }
                //Chef de Mission Scriptura
                elseif(intval($typecsd) === 5 &&	intval($typeresponsable) === 1){
                    $save = 'js-cms-save';
                    $delete = 'js-cms-delete';
                }


                foreach ($responsableCsds as $manager) {
                    $rows[] = array(
                        'id' => $manager->getId(),
                        'cell' => array(
                            $manager->getNom(),
                            $manager->getPrenom(),
                            $manager->getEmail(),
                            $manager->getSkype(),
                            '<i class="fa fa-save icon-action '.$save.'" title="Enregistrer"></i><i class="fa fa-trash icon-action '.$delete.'" title="Supprimer"></i>'
                        )
                    );
                }
            }


            $liste = array('rows'=>$rows);

            return new JsonResponse($liste);

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function managerEditAction(Request $request,$clientId, $typeresponsable, $typecsd)
    {
        if ($request->isXmlHttpRequest())
        {
            $post = $request->request;

            $id = $post->get('id');
            $nom = $post->get('resp-nom');

            $prenom = $post->get('resp-prenom');
            $email = $post->get('resp-mail');

            $skype = $post->get('resp-skype');
            if($skype ==''){
                $skype = null;
            }

            $em = $this->getDoctrine()->getManager();

            if($id != "")
            {
                if($id != "new_row")
                {

                    $responsableCsd = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->find($id);

                    if($responsableCsd){
                        $responsableCsd->setNom($nom);
                        $responsableCsd->setPrenom($prenom);
                        $responsableCsd->setEmail($email);
                        $responsableCsd->setSkype($skype);
                    }

                    $em->persist($responsableCsd);
                }
                else
                {
                    $idClient = Boost::deboost($clientId, $this);

                    $client = $this->getDoctrine()
                        ->getRepository('AppBundle:Client')
                        ->find($idClient);

                    if(!is_null($client)) {

                        $responsableCsd = new ResponsableCsd();

                        $responsableCsd->setClient($client);
                        $responsableCsd->setNom($nom);
                        $responsableCsd->setPrenom($prenom);
                        $responsableCsd->setEmail($email);
                        $responsableCsd->setSkype($skype);

                        $responsableCsd->setTypeCsd($typecsd);
                        $responsableCsd->setTypeResponsable($typeresponsable);


                        $em->persist($responsableCsd);


                    }
                    else{
                        throw new NotFoundHttpException("client introuvable.");
                    }
                }

                $em->flush();
                $data = array('erreur' => false);
                return new JsonResponse(json_encode($data));
            }

            throw new NotFoundHttpException("site introuvable.");


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }


    public function saisieOdPayeAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if($json == 1) {
                return new Response();
            }
            else{
                $options = '<select>';

                $options .='<option></option>';


                $options .='<option value="1">Oui</option>';
                $options .='<option value="0">Non</option>';
                $options .='<option value="2">Import</option>';

               $options .='</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function siteAction(Request $request, $clientId)
    {
        if($request->isXmlHttpRequest())
        {

            $idClient = Boost::deboost($clientId, $this);

            $client = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->find($idClient);

            $sites = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->findBy(array('client'=>$client));

            $rows = array();

            if(is_array($sites)) {
                foreach ($sites as $site)
                {
                    $rows[] = array(
                        'id' => $site->getId(),
                        'cell' => array(
                            $site->getNom(),
                            '<i class="fa fa-save icon-action js-save-site" title="Enregistrer"></i>'
                        )
                    );
                }
            }


            $liste = array('rows'=>$rows);

            return new JsonResponse($liste);

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function siteEditAction(Request $request,$clientId)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            $nom = $request->request->get('site-nom');

            $em = $this->getDoctrine()->getManager();

            if($id != "")
            {
                if($id != "new_row")
                {
                    $site = $em->getRepository('AppBundle:Site')
                        ->find($id);
                    if($site)
                    {
                        $site->setNom($nom);
                        $em->persist($site);
                    }
                }
                else
                {
                    $idClient = Boost::deboost($clientId, $this);

                    $client = $this->getDoctrine()
                        ->getRepository('AppBundle:Client')
                        ->find($idClient);

                    if(!is_null($client)) {
                        $site = new Site();
                        $site->setClient($client);
                        $site->setNom($nom);
                        $em->persist($site);
                    }
                    else{
                        throw new NotFoundHttpException("client introuvable.");
                    }
                }

                $em->flush();
                $data = array('erreur' => false);
                return new JsonResponse(json_encode($data));
            }

            throw new NotFoundHttpException("site introuvable.");


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function tvaDateAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if($json == 1) {
                return new Response();
            }
            else{
                $options = '<select>';

                $options .='<option></option>';

                for($i = 15; $i<=25; $i++){
                    $options .='<option value="'.$i.'">'.$i.'</option>';
                }

                $options .='<option value="55">5eme jour du 5eme mois</option>';

                $options .='</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function tvaFaitGenerateurAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if($json == 1) {
                return new Response();
            }
            else{
                $options = '<select>';

                $options .='<option></option>';

                $options .='<option value="1">Encaissement</option>';
                $options .='<option value="0">Débit</option>';
                $options .='<option value="2">Mixte</option>';
                $options .='</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function tvaModeAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if($json == 1) {
                return new Response();
            }
            else{
                $options = '<select>';

                $options .='<option></option>';

                $options .='<option value="0">Accomptes semestriels</option>';
                $options .='<option value="1">Accomptes trimestriels</option>';
                $options .='<option value="2">Paiement mensuels</option>';
                $options .='<option value="3">Paiement trimestriels</option>';

                $options .='</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function typeActiviteAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $typeActivites = $this->getDoctrine()
                ->getRepository('AppBundle:TypeActivite')
                ->findBy(array(), array('libelle' => 'asc'));
//                ->findAll();

            $rows = array();

            foreach ($typeActivites as $activite)
            {
                $rows[] = array(
                    'id' => $activite->getId(),
                    'cell' => array(
                        $activite->getLibelle(),
                        '<i class="fa fa-trash icon-action js-delete-typeActivite" title="Supprimer"></i>'
                    )
                );
            }
            $liste = array(
                'rows' => $rows,
            );
            return(new JsonResponse($liste));

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function typeActiviteEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            $libelle = $request->request->get('typeActivite-libelle');

            $em = $this->getDoctrine()->getManager();
            if($id != "")
            {
                if($id != "new_row")
                {
                    $typeActivite = $em->getRepository('AppBundle:TypeActivite')
                        ->find($id);
                    if($typeActivite)
                    {
                        $typeActivite->setLibelle($libelle);
                    }
                }
                else
                {
                    $typeActivite = new TypeActivite();
                    $typeActivite->setLibelle($libelle);
                    $em->persist($typeActivite);
                }

                $em->flush();
                $data = array('erreur' => false);
                return new JsonResponse(json_encode($data));
            }

            throw new NotFoundHttpException("Type Activité introuvable.");


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function typeActiviteRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $typeActivite = $this->getDoctrine()
                    ->getRepository('AppBundle:TypeActivite')
                    ->find($id);

                if ($typeActivite)
                {
                    $em->remove($typeActivite);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );
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
            return new JsonResponse(json_encode($data));


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function typeVehiculeAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if ($json == 1)
            {
                return new Response();
            }
            else
            {
                $typeVehicules = $this->getDoctrine()
                    ->getRepository('AppBundle:TypeVehicule')
                    ->findAll();

                $options = '<select>';

                $options .='<option></option>';
                foreach ($typeVehicules as $typeVehicule)
                {
                    $options .= '<option value="' . $typeVehicule->getId() . '">' . $typeVehicule->getLibelle() . '</option>';
                }
                $options .= '</select>';
                return new Response($options);
            }


        }

        else
        {
            throw new AccessDeniedException("Accès refusé");
        }
    }


    public function ndfTypeVehiculeAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if ($json == 1)
            {
                return new Response();
            }
            else
            {
                $typeVehicules = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfTypeVehicule')
                    ->findAll();

                $options = '<select>';

                $options .='<option></option>';
                foreach ($typeVehicules as $typeVehicule)
                {
                    $options .= '<option value="' . $typeVehicule->getId() . '">' . $typeVehicule->getLibelle() . '</option>';
                }
                $options .= '</select>';
                return new Response($options);
            }


        }

        else
        {
            throw new AccessDeniedException("Accès refusé");
        }
    }

    public function typeVenteAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $typeVentes = $this->getDoctrine()
                ->getRepository('AppBundle:TypeVente')
                ->findAll();

            $rows = array();

            foreach ($typeVentes as $vente)
            {
                $rows[] = array(
                    'id' => $vente->getId(),
                    'cell' => array(
                        $vente->getLibelle(),
                        '<i class="fa fa-trash icon-action js-delete-typeVente" title="Supprimer"></i>'
                    )
                );
            }
            $liste = array(
                'rows' => $rows,
            );
            return(new JsonResponse($liste));

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function typeVenteEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            $libelle = $request->request->get('typeVente-libelle');

            $em = $this->getDoctrine()->getManager();
            if($id != "")
            {
                if($id != "new_row")
                {
                    $typeVente = $em->getRepository('AppBundle:TypeVente')
                        ->find($id);
                    if($typeVente)
                    {
                        $typeVente->setLibelle($libelle);
                    }
                }
                else
                {
                    $typeVente = new TypeVente();
                    $typeVente->setLibelle($libelle);
                    $em->persist($typeVente);
                }

                $em->flush();
                $data = array('erreur' => false);
                return new JsonResponse(json_encode($data));
            }

            throw new NotFoundHttpException("Type vente introuvable.");


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function typeVenteRemoveAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $typeVente = $this->getDoctrine()
                    ->getRepository('AppBundle:TypeVente')
                    ->find($id);

                if ($typeVente)
                {
                    $em->remove($typeVente);
                    $em->flush();

                    $data = array(
                        'erreur' => false,
                    );
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
            return new JsonResponse(json_encode($data));


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function vehiculeAction(Request $request,$dossierId)
    {
        if($request->isXmlHttpRequest())
        {
            if($dossierId !='0')
            {

                $idDossier = Boost::deboost($dossierId, $this);
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find(intval($idDossier));

                $vehicules = $this->getDoctrine()
                    ->getRepository('AppBundle:Vehicule')
                    ->getVehiculeByDossierId($dossier);
            }
            $rows = array();

            if(!empty($vehicules))
            {
                /** @var Vehicule $vehicule */
                foreach ($vehicules as $vehicule)
                {
                    $nbcv = $vehicule->getNbCv();

                    if($nbcv>15)
                    {
                        $nbcv = 'Plus de 15';
                    }


                    $typeProprietaire = '';
                    if($vehicule->getVehiculeProprietaire() != null){
                        $typeProprietaire = $vehicule->getVehiculeProprietaire()->getLibelle();
                    }

                    $marque = '';
                    if($vehicule->getVehiculeMarque() != null){
                        $marque = $vehicule->getVehiculeMarque()->getLibelle();
                    }

                    $typeRemboursement = '';
                    if($vehicule->getTypeVehicule() != null){
                        $typeRemboursement = $vehicule->getTypeVehicule()->getLibelle();
                    }

                    $typeVehicule = '';
                    if($vehicule->getNdfTypeVehicule() != null){
                        $typeVehicule = $vehicule->getNdfTypeVehicule()->getLibelle();
                    }

                    $carburant = '';
                    if($vehicule->getCarburant() != null){
                        $carburant = $vehicule->getCarburant()->getLibelle();
                    }

                    $rows[] = array(
                        'id' => $vehicule->getId(),
                        'cell' => array(
                            $typeProprietaire,
                            $marque,
                            $vehicule->getModele(),
                            'A Poster',
                            $vehicule->getImmatricule(),
                            $typeRemboursement,
                            $typeVehicule,
                            $carburant,
                            $nbcv,
                            '<i class="fa fa-save icon-action js-save-vehicule" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-vehicule" title="Supprimer"></i>'
                        )
                    );
                }

            }
            $liste = array(
                'rows' => $rows,
            );
            return(new JsonResponse($liste));
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function vehiculeEditAction(Request $request,$dossierId)
    {
        if ($request->isXmlHttpRequest()) {
            if ($dossierId == '0') {
                return new Response();
            }

            $id = $request->request->get('id');

            if ($id != '') {
                $proprietaireId = $request->request->get('vehicule-propietaire');
                $marqueId = $request->request->get('vehicule-marque');
                $modele = $request->request->get('vehicule-modele');
                $immatricule = $request->request->get('vehicule-immatricule');
                $typeRembourssementId = $request->request->get('vehicule-type-remboursement');
                $typeVehiculeId = $request->request->get('vehicule-type-vehicule');
                $carburantId = $request->request->get('vehicule-carburant');
                $puissance = $request->request->get('vehicule-puissance');

                try {
                    if ($id != 'new_row') {
                        $em = $this->getDoctrine()->getManager();

                        $vehicule = $this->getDoctrine()
                            ->getRepository('AppBundle:Vehicule')
                            ->find($id);

                        $vehicule->setModele($modele);
                        $vehicule->setImmatricule($immatricule);
                        $vehicule->setNbCv($puissance);


                        $proprietaire = null;
                        if ($proprietaireId != '') {
                            $proprietaire = $this->getDoctrine()
                                ->getRepository('AppBundle:VehiculeProprietaire')
                                ->find($proprietaireId);
                        }
                        $vehicule->setVehiculeProprietaire($proprietaire);

                        $marque = null;
                        if ($marqueId != '') {
                            $marque = $this->getDoctrine()
                                ->getRepository('AppBundle:VehiculeMarque')
                                ->find($marqueId);
                        }
                        $vehicule->setVehiculeMarque($marque);

                        $typeRembourssement = null;
                        if ($typeRembourssementId != '') {
                            $typeRembourssement = $this->getDoctrine()
                                ->getRepository('AppBundle:TypeVehicule')
                                ->find($typeRembourssementId);
                        }
                        $vehicule->setTypeVehicule($typeRembourssement);

                        $typeVehicule = null;
                        if ($typeVehiculeId != '') {
                            $typeVehicule = $this->getDoctrine()
                                ->getRepository('AppBundle:NdfTypeVehicule')
                                ->find($typeVehiculeId);
                        }
                        $vehicule->setNdfTypeVehicule($typeVehicule);

                        $carburant = null;
                        if ($carburantId != '') {
                            $carburant = $this->getDoctrine()
                                ->getRepository('AppBundle:Carburant')
                                ->find($carburantId);
                        }

                        $vehicule->setCarburant($carburant);

                        $em->persist($vehicule);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    } else {
                        $em = $this->getDoctrine()->getManager();

                        $vehicule = new Vehicule();

                        $vehicule->setModele($modele);
                        $vehicule->setImmatricule($immatricule);
                        $vehicule->setNbCv($puissance);

                        $proprietaire = null;
                        if ($proprietaireId != '') {
                            $proprietaire = $this->getDoctrine()
                                ->getRepository('AppBundle:VehiculeProprietaire')
                                ->find($proprietaireId);
                        }
                        $vehicule->setVehiculeProprietaire($proprietaire);

                        $marque = null;
                        if ($marqueId != '') {
                            $marque = $this->getDoctrine()
                                ->getRepository('AppBundle:VehiculeMarque')
                                ->find($marqueId);
                        }
                        $vehicule->setVehiculeMarque($marque);

                        $typeRembourssement = null;
                        if ($typeRembourssementId != '') {
                            $typeRembourssement = $this->getDoctrine()
                                ->getRepository('AppBundle:TypeVehicule')
                                ->find($typeRembourssementId);

                        }
                        $vehicule->setTypeVehicule($typeRembourssement);

                        $typeVehicule = null;
                        if ($typeVehiculeId != '') {
                            $typeVehicule = $this->getDoctrine()
                                ->getRepository('AppBundle:NdfTypeVehicule')
                                ->find($typeVehiculeId);

                        }
                        $vehicule->setNdfTypeVehicule($typeVehicule);

                        if ($carburantId != '') {
                            $carburant = $this->getDoctrine()
                                ->getRepository('AppBundle:Carburant')
                                ->find($carburantId);
                            $vehicule->setCarburant($carburant);
                        }

                        $idDossier = Boost::deboost($dossierId, $this);

                        $dossier = $this->getDoctrine()
                            ->getRepository('AppBundle:Dossier')
                            ->find($idDossier);

                        $vehicule->setDossier($dossier);

                        $em->persist($vehicule);
                        $em->flush();

                        $data = array(
                            'erreur' => false,
                        );
                        return new JsonResponse(json_encode($data));
                    }
                } catch (\Exception $ex) {
                    return new Response($ex->getMessage(), 1500);
                }
            }

            throw new NotFoundHttpException("Vehicule introuvable.");
        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function vehiculeRemoveAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $id = $request->get('id');

            if ($id)
            {
                $em = $this->getDoctrine()->getManager();
                $vehicule = $this->getDoctrine()
                    ->getRepository('AppBundle:Vehicule')
                    ->find($id);

                $em->remove($vehicule);
                $em->flush();

                $data = array(
                    'erreur' => false,
                );
            }
            else
            {
                $data = array(
                    'erreur' => true,
                    'erreur_text' => "Vehicule introuvable",
                );
                return new JsonResponse(json_encode($data), 404);
            }
            return new JsonResponse(json_encode($data));


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function vehiculeCarteGriseAction()
    {
        $options = '<select> <option></option> <option value="1">A poster</option><option value="0">Ne pas poster</option></select>';
        return new Response($options);
    }

    public function vehiculeMarqueAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            if ($json == 1)
            {
                return new Response();
            }
            else
            {
                $vehiculeMarques = $this->getDoctrine()
                    ->getRepository('AppBundle:VehiculeMarque')
                    ->findBy(array(), array('libelle' => 'asc'));
//                    ->findAll();

                $options = '<select>';

                $options .='<option></option>';

                foreach ($vehiculeMarques as $vehiculeMarque)
                {
                    $options .= '<option value="' . $vehiculeMarque->getId() . '">' . $vehiculeMarque->getLibelle() . '</option>';
                }
                $options .= '</select>';
                return new Response($options);
            }
        }

        else
        {
            throw new AccessDeniedException("Accès refusé");
        }
    }


    public function vehiculeProprietaireAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {
            if ($json == 1)
            {
                return new Response();
            }
            else
            {
                $vehiculeProprietaires = $this->getDoctrine()
                    ->getRepository('AppBundle:VehiculeProprietaire')
                    ->findBy(array(), array('libelle' => 'asc'));
//                    ->findAll();

                $options = '<select>';

                $options .='<option></option>';

                foreach ($vehiculeProprietaires as $vehiculeProprietaire)
                {
                    $options .= '<option value="' . $vehiculeProprietaire->getId() . '">' . $vehiculeProprietaire->getLibelle() . '</option>';
                }
                $options .= '</select>';
                return new Response($options);
            }
        }

        else
        {
            throw new AccessDeniedException("Accès refusé");
        }
    }

    public function vehiculeNombreCvAction()
    {
        $options = '<select>';

        $options .= '<option></option>';
        for ($i = 2; $i <= 15; $i++)
        {
            $options .= '<option value="' . $i . '">' . $i. '</option>';
        }

        $options .= '<option value="16">Plus de 15</option>';

        $options .='</select>';

        return new Response($options);
    }

    public function venteAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest())
        {

            if($json == 1) {
                return new Response();
            }
            else{
                $options = '<select>';

                $options .='<option></option>';

                $options .='<option value="1">Saisie factures</option>';
                $options .='<option value="0">Import excel</option>';
                $options .='<option value="3">Caisse</option>';
                $options .='<option value="2">Autre</option>';

                $options .='</select>';

                return new Response($options);

            }
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function recapAction(Request $request, $siteId, $clientId)
    {
        if($request->isXmlHttpRequest()) {
            $idSite = Boost::deboost($siteId, $this);

            $idClient = Boost::deboost($clientId, $this);
            
            $site = $this->getDoctrine()->getRepository('AppBundle:Site')
                ->find($idSite);

            $client = $this->getDoctrine()->getRepository('AppBundle:Client')
                ->find($idClient);

            if($idSite != 0) {
                $dossiers = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->findBy(array('site' => $site, 'status' => 1));
            }
            else{
                /** @var Utilisateur $utilisateur */
                $utilisateur = $this->getUser();

                $dossiers = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->getUserDossier($utilisateur,$client,null,null,true);
            };

            $rows = array();

//            $dossiers = array();
//
//            $dossiers[] = $this->getDoctrine()
//                ->getRepository('AppBundle:Dossier')
//                ->find(11388);

            if (is_array($dossiers)) {
                foreach ($dossiers as $dossier) {

                    $status = $dossier->getActive();
                    switch ($status){
                        case 1:
                            $statut = 'Créé';
                            break;

                        default:
                            $statut = 'En création';
                            break;
                    }

                    $codeApe = '';

                    if(!is_null($dossier->getActiviteComCat3())){
                        $codeApe = $dossier->getActiviteComCat3()->getCodeApe();
                    }


                    $responsable = '';

                    $responsableCsd = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findBy(array('typeResponsable' => 1, 'dossier' => $dossier));


                    $responsableSite = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findBy(array('typeResponsable' => 1, 'site' => $dossier->getSite()));

                    $trouveDossier = false;
                    $trouveSupervision = false;
                    $trouveAdministratif = false;
                    $trouveSite = false;

                    if(count($responsableSite) > 0){
                        $trouveSite = true;
                    }

                    if (!is_null($responsableCsd)) {


                        foreach ($responsableCsd as $csd) {
                            switch ($csd->getTypeCsd()) {
                                case 2:
                                    $trouveDossier = true;
                                    break;
                                case 3:
                                    $trouveSupervision = true;
                                    break;
                                case 4:
                                    $trouveAdministratif = true;
                                    break;
                            }
                        }
                    }

                    if ($trouveSupervision == true) {
                        $responsable = 'Supervision';
                    } else if ($trouveDossier == true) {
                        $responsable = 'Dossier';
                    } else if ($trouveAdministratif) {
                        $responsable = 'Administratif';
                    } else if ($trouveSite) {
                        $responsable = 'Site';
                    }

                    $mandataire = '';

                    $mandataireNomPrenom = '';

                    $respMandataire = $this->getDoctrine()
                        ->getRepository('AppBundle:ResponsableCsd')
                        ->findOneBy(array('typeResponsable' => 0, 'dossier' => $dossier));

                    if (!is_null($respMandataire)) {
                        if (!is_null($respMandataire->getMandataire())) {
                            $mandataire = $respMandataire->getMandataire()->getLibelle();
                        }

                        if($respMandataire->getNom() != '' || $respMandataire->getPrenom() != ''){
                            $mandataireNomPrenom = $respMandataire->getNom();

                            if($respMandataire->getPrenom() != ''){
                                $mandataireNomPrenom .= "; ".$respMandataire->getPrenom();
                            }
                        }
                    }

                    $dateDemarrage = '';
                    $datePremiereCloture = '';

                    if (!is_null($dossier->getDebutActivite())) {
                        $dateDemarrage = $dossier->getDebutActivite()->format('Y-m-d');
                    }

                    if (!is_null($dossier->getDateCloture())) {
                        $datePremiereCloture = $dossier->getDateCloture()->format('Y-m-d');
                    }

                    $datecloture = "";
                    if(!is_null($dossier->getCloture())){
                        $datecloture = $dossier->getCloture();

                        switch ($datecloture){
                            case 1:
                                $datecloture = 'Janvier';
                                break;
                            case 2:
                                $datecloture = 'Fevrier';
                                break;
                            case 3:
                                $datecloture = 'Mars';
                                break;
                            case 4:
                                $datecloture = 'Avril';
                                break;
                            case 5:
                                $datecloture = 'Mai';
                                break;
                            case 6:
                                $datecloture = 'Juin';
                                break;
                            case 7:
                                $datecloture = 'Juillet';
                                break;
                            case 8:
                                $datecloture = 'Août';
                                break;
                            case 9:
                                $datecloture = 'Septembre';
                                break;
                            case 10:
                                $datecloture = 'Octobre';
                                break;
                            case 11:
                                $datecloture = 'Novembre';
                                break;
                            case 12:
                                $datecloture ='Decembre';
                                break;

                        }
                    }

                    $formeJuridique = '';
                    if (!is_null($dossier->getFormeJuridique())) {
                        $formeJuridique = $dossier->getFormeJuridique()->getLibelle();
                    }

                    $regimeFiscal = '';
                    if (!is_null($dossier->getRegimeFiscal())) {
                        $regimeFiscal = $dossier->getRegimeFiscal()->getLibelle();
                    }

                    $regimeImposition = '';
                    if (!is_null($dossier->getRegimeImposition())) {
                        $regimeImposition = $dossier->getRegimeImposition()->getLibelle();
                    }

                    $typeActivite = '';
                    if (!is_null($dossier->getNatureActivite())) {
                        $typeActivite = $dossier->getNatureActivite()->getLibelle();
                    }

                    $formeActivite = '';
                    if (!is_null($dossier->getFormeActivite())) {
                        $formeActivite = $dossier->getFormeActivite()->getLibelle();
                    }

                    $professionLiberale = '';
                    if(!is_null($dossier->getProfessionLiberale())){
                        $professionLiberale = $dossier->getProfessionLiberale()->getLibelle();
                    }

                    $typeVente = '';
                    if (!is_null($dossier->getModeVente())) {
                        $typeVente = $dossier->getModeVente()->getLibelle();
                    }

                    $tvaRegime = '';
                    if (!is_null($dossier->getRegimeTva())) {
                        $tvaRegime = $dossier->getRegimeTva()->getLibelle();
                    }

                    $tvaDate = '';
                    if (!is_null($dossier->getTvaDate())) {
                        $tvaDate = $dossier->getTvaDate();
                    }

                    $tvaFaitGenerateur = '';
                    if(!is_null($dossier->getTvaFaitGenerateur())){
                        $tvaFaitGenerateur = $dossier->getTvaFaitGenerateur();

                        switch ($tvaFaitGenerateur){

                            case 0:
                                $tvaFaitGenerateur = "Débit";
                                break;

                            case 1:
                                $tvaFaitGenerateur = "Encaissement";
                                break;

                            case 2:
                                $tvaFaitGenerateur = "Mixte";
                                break;
                        }
                    }

                    $taxeSurSalaire = '';
                    if(!is_null($dossier->getTaxeSalaire())){
                        $taxeS = $dossier->getTaxeSalaire();

                        switch ($taxeS){
                            case 0:
                                $taxeSurSalaire = 'Non';
                                break;
                            case 1:
                                $taxeSurSalaire = 'Oui';
                                break;
                        }

                    }

                    $tvaMode = '';
                    if (!is_null($dossier->getTvaMode())) {
                        $tvaMode = $dossier->getTvaMode();

                        switch ($tvaMode) {
                            case 0 :
                                $tvaMode = 'Accomptes semestriels';
                                break;

                            case 1 :
                                $tvaMode = 'Accomptes trimestriels';
                                break;

                            case 2:
                                $tvaMode = 'Paiement mensuels';
                                break;

                            case 3:
                                $tvaMode = 'Paiement trimestriels';
                                break;
                        }
                    }

                    $tvaTaux = '';

                    $tvaTauxDossiers = $this->getDoctrine()
                        ->getRepository('AppBundle:TvaTauxDossier')
                        ->findBy(array('dossier' => $dossier));
                    /** @var TvaTauxDossier $tvaTauxDossier */
                    foreach ($tvaTauxDossiers as $tvaTauxDossier){

                        if($tvaTaux == '') {
                            $tvaTaux .= $tvaTauxDossier->getTvaTaux()->getTaux()."%";
                        }
                        else{
                            $tvaTaux = $tvaTaux.";".$tvaTauxDossier->getTvaTaux()->getTaux()."%";
                        }
                    }


                    $conventionComptable = '';

                    $vente = '';
                    $achat = '';
                    $banque = '';
                    $saisieOdPaye = '';
                    $rapprochementBanque = '';
                    $suiviChequeEmis = '';

                    $periodicite = '';
                    $methodeComptable = $this->getDoctrine()
                        ->getRepository('AppBundle:MethodeComptable')
                        ->findOneBy(array('dossier' => $dossier));

                    if (!is_null($methodeComptable)) {


                        if(!is_null($methodeComptable->getConventionComptable())){
                            $conventionComptable = $methodeComptable->getConventionComptable()->getLibelle();
                        }

                        if (!is_null($methodeComptable->getVente())) {
                            $vente = $methodeComptable->getVente();

                            switch ($vente) {
                                case 1:
                                    $vente = 'Saisie factures';
                                    break;
                                case 0:
                                    $vente = 'Import excel';
                                    break;
                                case 3:
                                    $vente = 'Caisse';
                                    break;
                                case 2:
                                    $vente = 'Autre';
                                    break;
                            }
                        }

                        if (!is_null($methodeComptable->getAchat())) {
                            $achat = $methodeComptable->getAchat();

                            switch ($achat) {
                                case 0:
                                    $achat = 'Import excel';
                                    break;
                                case 1:
                                    $achat = 'Saisie sur factures';
                                    break;
                                case 2:
                                    $achat = 'Autre';
                                    break;

                            }
                        }

                        if(!is_null($methodeComptable->getBanque())){
                            $banque = $methodeComptable->getBanque();

                            switch ($banque){
                                case 1:
                                    $banque = 'Saisie';
                                    break;
                                case 0:
                                    $banque = 'Import ecritures';
                                    break;
                                case 2:
                                    $banque = 'Déjà importé dans l\'archive';
                                    break;
                            }

                        }


                        if(!is_null($methodeComptable->getSaisieOdPaye())){
                            $saisieOdPaye = $methodeComptable->getSaisieOdPaye();

                            switch ($saisieOdPaye){
                                case 1:
                                    $saisieOdPaye = 'Oui';
                                    break;
                                case 0:
                                    $saisieOdPaye = 'Non';
                                    break;
                                case 2:
                                    $saisieOdPaye = 'Import';
                                    break;
                            }

                        }

                        if(!is_null($methodeComptable->getRapprochementBanque())){
                            $rapprochementBanque = $methodeComptable->getRapprochementBanque();

                            switch ($rapprochementBanque){
                                case 1:
                                    $rapprochementBanque = 'Oui';
                                    break;
                                case 0:
                                    $rapprochementBanque = 'Non';
                                    break;
                                case 2:
                                    $rapprochementBanque = 'Indifférent';
                                    break;
                            }
                        }
                        else{
                            $instructionDossiers = $this->getDoctrine()
                                ->getRepository('AppBundle:InstructionDossier')
                                ->findBy(array('client' => $dossier->getSite()->getClient()));

                            if(count($instructionDossiers) > 0) {
                                $instructionDossier = $instructionDossiers[0];

                                $rapprochementBanque = $instructionDossier->getRapprochementBanque();

                                switch ($rapprochementBanque) {
                                    case 1:
                                        $rapprochementBanque = 'Oui';
                                        break;
                                    case 0:
                                        $rapprochementBanque = 'Non';
                                        break;
                                    case 2:
                                        $rapprochementBanque = 'Indifférent';
                                        break;
                                }
                            }
                        }

                        if (!is_null($methodeComptable->getTenueComptablilite())) {
                            $periodicite = $methodeComptable->getTenueComptablilite();

                            switch ($periodicite) {
                                case 1:
                                    $periodicite = 'Mensuelle';
                                    break;
                                case 2:
                                    $periodicite = 'Trimestrielle';
                                    break;
                                case 3:
                                    $periodicite = 'Semestrielle';
                                    break;
                                case 4:
                                    $periodicite = 'Annuelle';
                                    break;
                                case 5:
                                    $periodicite = 'Ponctuelle';
                                    break;
                            }
                        }

                        if(!is_null($methodeComptable->getMethodeSuiviCheque())){
                            $suiviChequeEmis = $methodeComptable->getMethodeSuiviCheque()->getLibelle();
                        }
                        else{
                            $instructionDossiers = $this->getDoctrine()
                                ->getRepository('AppBundle:InstructionDossier')
                                ->findBy(array('client' => $dossier->getSite()->getClient()));

                            if(count($instructionDossiers) > 0){
                                $instr = $instructionDossiers[0];

                                if(!is_null($instr->getMethodeSuiviCheque())){
                                    $suiviChequeEmis = $instr->getMethodeSuiviCheque()->getLibelle();
                                }

                                else{
                                    if($rapprochementBanque == "Non"){
                                        $suiviChequeEmis = ".";
                                    }
                                }
                            }

                        }
                    }

                    $typePrestation = '';

                    if(!is_null($dossier->getTypePrestation2())){
                        $typePrestation = $dossier->getTypePrestation2()->getLibelle();
                    }

                    $prestationFiscal = $this->getDoctrine()
                        ->getRepository('AppBundle:PrestationFiscale')
                        ->findOneBy(array('dossier' => $dossier));

                    $tva = '';
                    $liasseFiscal = '';
                    $accompteIsSolde = '';
                    $cice ='';
                    $cvae ='';
                    $tvts ='';
                    $das2 ='';
                    $cfe ='';
                    $dividende ='';



                    $declarationFiscal = '';
                    $teledeclaration = '';

                    if (!is_null($prestationFiscal)) {
                        if (!is_null($prestationFiscal->getTva())) {
                            $tva = $prestationFiscal->getTva();

                            switch ($tva) {
                                case 1:
                                    $tva = 'Oui';
                                    break;
                                case 0:
                                    $tva = 'Non';
                                    break;
                            }
                        }

                        if (!is_null($prestationFiscal->getLiasse())) {
                            $liasseFiscal = $prestationFiscal->getLiasse();

                            switch ($liasseFiscal) {
                                case 1:
                                    $liasseFiscal = 'Oui';
                                    break;
                                case 0:
                                    $liasseFiscal = 'Non';
                                    break;
                            }
                        }

                        if (!is_null($prestationFiscal->getAcompteIs())){
                            $accompteIsSolde = $prestationFiscal->getAcompteIs();

                            switch ($accompteIsSolde) {
                                case 1:
                                    $accompteIsSolde = 'Oui';
                                    break;
                                case 0:
                                    $accompteIsSolde = 'Non';
                                    break;

                            }
                        }


                        if (!is_null($prestationFiscal->getCice())){
                            $cice = $prestationFiscal->getCice();

                            switch ($cice) {
                                case 1:
                                    $cice = 'Oui';
                                    break;
                                case 0:
                                    $cice = 'Non';
                                    break;
                                case 2:
                                    $cice = 'Si nécessaire';
                                    break;

                            }
                        }


                        if (!is_null($prestationFiscal->getCvae())){
                            $cvae = $prestationFiscal->getCvae();

                            switch ($cvae) {
                                case 1:
                                    $cvae = 'Oui';
                                    break;
                                case 0:
                                    $cvae = 'Non';
                                    break;

                            }
                        }

                        if (!is_null($prestationFiscal->getTvts())){
                            $tvts = $prestationFiscal->getTvts();

                            switch ($tvts) {
                                case 1:
                                    $tvts = 'Oui';
                                    break;
                                case 0:
                                    $tvts = 'Non';
                                    break;

                            }
                        }

                        if (!is_null($prestationFiscal->getDas2())){
                            $das2 = $prestationFiscal->getDas2();

                            switch ($das2) {
                                case 1:
                                    $das2 = 'Oui';
                                    break;
                                case 0:
                                    $das2 = 'Non';
                                    break;

                            }
                        }

                        if (!is_null($prestationFiscal->getCfe())){
                            $cfe = $prestationFiscal->getCfe();

                            switch ($accompteIsSolde) {
                                case 1:
                                    $cfe = 'Oui';
                                    break;
                                case 0:
                                    $cfe = 'Non';
                                    break;

                            }
                        }

                        if (!is_null($prestationFiscal->getDividende())){
                            $dividende = $prestationFiscal->getDividende();

                            switch ($dividende) {
                                case 1:
                                    $dividende = 'Oui';
                                    break;
                                case 0:
                                    $dividende = 'Non';
                                    break;
                                case 2:
                                    $dividende = 'Si nécessaire';
                                    break;
                            }
                        }

                        if (!is_null($prestationFiscal->getTeledeclarationLiasse())) {
                            $declarationFiscal = $prestationFiscal->getTeledeclarationLiasse();

                            switch ($declarationFiscal) {
                                case 1:
                                    $declarationFiscal = 'Oui';
                                    break;
                                case 0:
                                    $declarationFiscal = 'Non';
                                    break;
                            }
                        }
                        else{
//                            if(!is_null($prestationFiscal->getLiasse())){
//                                if($pre)
//                            }
                        }

                        if (!is_null($prestationFiscal->getTeledeclarationAutre())) {
                            $teledeclaration = $prestationFiscal->getTeledeclarationAutre();

                            switch ($teledeclaration) {
                                case 1:
                                    $teledeclaration = 'Oui';
                                    break;
                                case 0:
                                    $teledeclaration = 'Non';
                                    break;
                            }
                        }
                    }




                    $siren = $dossier->getSirenSte();

                    $siren = substr_replace($siren, ' ', 3, 0);
                    $siren = substr_replace($siren, ' ', 7, 0);

                    if(strlen($siren) >= 14){
                        $siren = substr_replace($siren, ' ', 11, 0);
                    }

                    if(!is_null($dossier->getFormeJuridique())){
                        $codeJuridique = $dossier->getFormeJuridique()->getCode();

                        if($codeJuridique == 'CODE_CE' || $codeJuridique == 'CODE_AUTRE'){
                            if(is_null($siren) || str_replace(' ', '',$siren) ==''){
                                $siren = ".";
                            }
                        }
                    }


                    //Date permière cloture
                    if (is_null($dossier->getDateCloture())) {

                        if (!is_null($dossier->getDebutActivite() && !is_null($dossier->getCloture()))) {

                            try {

                                $dateCloture = $this->getDoctrine()
                                    ->getRepository('AppBundle:Dossier')
                                    ->getDateCloture($dossier, date('Y'));


                                $dayDiff = $dateCloture->diff($dossier->getDebutActivite())->days;

                                $monthDiff = $dayDiff / 30;


                                if ($monthDiff >= 23) {
                                    $datePremiereCloture = '.';
                                }
                            }
                            catch (\Exception $e){
                                $datePremiereCloture = '.';
                            }
                        }
                    }


                    if (!is_null($dossier->getFormeActivite())) {

                        if ($dossier->getFormeActivite()->getCode() != "CODE_PROFESSION_LIBERALE") {
                            if (is_null($dossier->getProfessionLiberale())) {
                               $professionLiberale = '.';
                            }
                        }
                    }

                    if (!is_null($dossier->getRegimeTva())) {


                        if ($dossier->getRegimeTva()->getCode() != 'CODE_NON_SOUMIS') {
                            if (is_null($dossier->getTaxeSalaire())) {
                                $taxeSurSalaire = ".";
                            }
                        } else {
                            if (is_null($dossier->getTvaFaitGenerateur())) {
                               $tvaFaitGenerateur = ".";
                            }

                            $tvaTauxs = $this->getDoctrine()
                                ->getRepository('AppBundle:TvaTauxDossier')
                                ->findBy(array('dossier' => $dossier));

                            if (count($tvaTauxs) < 1) {
                               $tvaTaux = ".";
                            }

                            if (is_null($dossier->getTvaMode())) {
                              $tvaMode = ".";
                            }
                            if (is_null($dossier->getTvaDate())) {
                               $tvaDate = ".";
                            }
                        }
                    }

                    $prestation = $this->getDoctrine()
                        ->getRepository('AppBundle:PrestationFiscale')
                        ->findBy(array('dossier' => $dossier));

                    if (count($prestation) > 0) {


                        /** @var  $prest PrestationFiscale */
                        $prest = $prestation[0];
                        if (is_null($prest->getTva())) {
                            $prestationErreur[] = 'TVA';
                        }

                        $codePrestation = "";
                        if (!is_null($dossier->getTypePrestation2())) {
                            $codePrestation = $codePrestation = $dossier->getTypePrestation2()->getCode();
                        }

                        if ($codePrestation == "CODE_TENUE_COURANTE") {

                                if (is_null($prest->getLiasse())) {
                                   $liasseFiscal = ".";
                                }
                                if (is_null($prest->getAcompteIs())) {
                                    $accompteIsSolde = ".";
                                }
                                if (is_null($prest->getCice())) {
                                    $cice = ".";
                                }
                                if (is_null($prest->getCvae())) {
                                    $cvae = ".";
                                }
                                if (is_null($prest->getTvts())) {
                                    $tvts = ".";
                                }
                                if (is_null($prest->getDas2())) {
                                    $das2 = ".";
                                }
                                if (is_null($prest->getCfe())) {
                                    $cfe = ".";
                                }
                                if (is_null($prest->getDividende())) {
                                    $dividende = ".";
                                }
                                if (is_null($prest->getTeledeclarationLiasse())) {
                                    $declarationFiscal = ".";
                                }
                                if (is_null($prest->getTeledeclarationAutre())) {
                                   $teledeclaration = ".";
                                }
//                            if (is_null($prest->getAutres())) {
//                                $prestationErreur[] = 'Autres';
//                            }
                            }

                            else {


                                if (!is_null($dossier->getRegimeFiscal())) {

                                    if ($dossier->getRegimeFiscal()->getCode() == "CODE_BA") {
                                        $liasseOui = -1;

                                        if (is_null($prest->getLiasse())) {
                                            $liasseFiscal = ".";
                                        } else {
                                            $liasseOui = $prest->getLiasse();
                                        }

                                        if (is_null($prest->getAcompteIs())) {
                                            $accompteIsSolde = ".";
                                        }

                                        if ($liasseOui == 0) {
                                            if (is_null($prest->getTeledeclarationLiasse())) {
                                              $declarationFiscal = ".";
                                            }
                                        }

                                    }
                                }

                            }

                    }











                    $rows[] = array(
                        'id' => $dossier->getId(),
                        'cell' => array(
                            $statut,
                            $dossier->getNom(),
                            $siren,
                            $dossier->getRsSte(),
                            $formeJuridique,
                            $codeApe,
                            $dateDemarrage,
                            $datePremiereCloture,
                            $datecloture,
                            $mandataire,
                            $mandataireNomPrenom,
                            $regimeFiscal,
                            $regimeImposition,
                            $typeActivite,
                            $formeActivite,
                            $professionLiberale,

//                            'Type de vente', 'TVA régime', 'TVA paiement', 'TVA fait générateur', 'TVA Taux','TVA date', 'Taxe sur salaire',
                            $typeVente,
                            $tvaRegime,
                            $tvaMode,
                            $tvaFaitGenerateur,
                            $tvaTaux,
                            $tvaDate,
                            $taxeSurSalaire,

                            // 'Convention comptable','Périodicité', 'Ventes', 'Achats', 'Banques','Saisie des OD de paye','Rapprochement banque', 'Suivi des cheques emis',
                            $conventionComptable,
                            $periodicite,
                            $vente,
                            $achat,
                            $banque,
                            $saisieOdPaye,
                            $rapprochementBanque,
                            $suiviChequeEmis,

                            // 'Type prestation', 'TVA', 'Liasse fiscale', 'Accomptes IS et Solde','CICE','CVAE','TVTS','DAS2','CFE','Dividendes', 'Teledeclaration liasse','Teledeclaration Autres',
                            $typePrestation,
                            $tva,
                            $liasseFiscal,
                            $accompteIsSolde,
                            $cice,
                            $cvae,
                            $tvts,
                            $das2,
                            $cfe,
                            $dividende,
                            $declarationFiscal,
                            $teledeclaration,
                            '<i class="fa fa-save icon-action js-save-recap" title="Enregistrer"></i>'

                        )
                    );
                }
            }


            $liste = array('rows' => $rows);

            return new JsonResponse($liste);
        }
        else{
            throw new AccessDeniedHttpException('Accès refusé');
        }

    }


    public function scripturaAction(Request $request, $annee, $mois){
        if($request->isXmlHttpRequest()) {

            $rows = array();

            $clients = $this->getDoctrine()
                ->getRepository('AppBundle:Client')
                ->findBy(array('status' => 1), array('nom' => 'ASC'));


            /** @var Client $client */
            foreach ($clients as $client) {
                $dossiers = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->getDossiersClient($client);


                $nbTotal = 0;
                $nbEnCreation = 0;
                $nbCree = 0;
                $nbCreeMois = 0;

                /** @var Dossier $dossier */
                foreach ($dossiers as $dossier){

                    if($dossier->getStatus() == 1){
                        if($dossier->getActive() == 1){
                            $nbCree ++;
                        }
                        else{
                            $nbEnCreation ++;
                        }

                        $dateCreation = $dossier->getDateCreation();
                        $dateCurrent =  new \DateTime();
                        if(!is_null($dateCreation)){
                            $monthCreation=$dateCreation->format("m");
                            $yearCreation=$dateCreation->format("Y");

                            $monthCurrent=$dateCurrent->format("m");
                            $yearCurrent=$dateCurrent->format("Y");


                            if($annee != -1){
                                $yearCurrent = $annee;
                            }

                            if($mois != -1){
                                $monthCurrent = $mois;
                            }

                            if($monthCreation == $monthCurrent && $yearCreation == $yearCurrent){
                                $nbCreeMois ++;
                            }
                        }
                        $nbTotal++;
                    }
                }

                $rows[] = array(
                    'id' => $client->getId(),
                    'cell' => array(
                        $client->getNom(),
                        $nbTotal,
                        $nbEnCreation,
                        $nbCree,
                        $nbCreeMois
                    )
                );
            }

            $liste = array('rows' => $rows);

            return new JsonResponse($liste);
        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }






}