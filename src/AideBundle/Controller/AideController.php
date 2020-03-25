<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 08/09/2017
 * Time: 09:35
 */

namespace AideBundle\Controller;



use AppBundle\Entity\Aide1;
use AppBundle\Entity\Aide2;
use AppBundle\Entity\Aide3Contenu;
use AppBundle\Entity\AideAssocie;
use AppBundle\Entity\AideRecentUtilisateur;
use AppBundle\Entity\Menu;
use AppBundle\Entity\MenuParRole;
use AppBundle\Entity\MenuUtilisateur;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use AppBundle\Entity\Aide3;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouteCompiler;

class AideController extends Controller
{
    public function aideRecentAction(Request $request){
        if($request->isXmlHttpRequest()){
            $post = $request->request;

            $aide3Id = $post->get('aide_3_id');

            $aide3 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->find($aide3Id);

            $utilisateur = $this->getUser();

            $aideRecent = null;

            $aideRecents = $this->getDoctrine()
                ->getRepository("AppBundle:AideRecentUtilisateur")
                ->findBy(array('utilisateur'=> $utilisateur));

            $em = $this->getDoctrine()
                ->getManager();

            $trouve = false;

            $minId = -1;

            if(count($aideRecents) > 0) {
                $minId = $aideRecents[0]->getId();
            }

            /** @var AideRecentUtilisateur $aideRecent */
            foreach ($aideRecents as $aideRecent){

                if($minId>$aideRecent->getId()){
                    $minId = $aideRecent->getId();
                }

                if($aideRecent->getAide3() == $aide3){
                    $trouve = true;
                    $aideRecent->setDateConsulatation(new \DateTime());
                    break;
                }
            }

            if($trouve == false) {
                if (count($aideRecents) >= 5) {
                    $aideRecentOld = $this->getDoctrine()
                        ->getRepository('AppBundle:AideRecentUtilisateur')
                        ->find($minId);
                    $em->remove($aideRecentOld);
                    $em->flush();
                }

                $aideRecent = new AideRecentUtilisateur();
                $aideRecent->setAide3($aide3);
                $aideRecent->setUtilisateur($utilisateur);
                $aideRecent->setDateConsulatation(new \DateTime());
            }

            $em->persist($aideRecent);
            $em->flush();

            return new JsonResponse(1);
        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


    public function aide3Action(Request $request, $json)
    {
        if($request->isXmlHttpRequest()){

            $aide2 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide2')
                ->find($json);

            $aide3s = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->findBy(array('aide2'=>$aide2), array('rang' => 'ASC'));

            $rows = array();

            if(is_array($aide3s)) {
                foreach ($aide3s as $aide3)
                {
                    $rows[] = array(
                        'id' => $aide3->getId(),
                        'cell' => array(
                            $aide3->getTitre(),
                            $aide3->getRang(),
                            '<i class="fa fa-save icon-action js_save_aide_3" title="Enregistrer"></i><i class="fa fa-trash icon-action js_delete_aide_3" title="Supprimer"></i>'
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

    public function aide3EditAction(Request $request,$aide_2_id)
    {
        if ($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');

            $titre = $request->request->get('aide-3-titre');

            $rang = $request->request->get('aide-3-rang');

            if(!is_numeric($rang)){
                $rang = null;
            }

            $em = $this->getDoctrine()->getManager();

            if($id != "")
            {

                if($id != "new_row")
                {
                    /** @var Aide3 $aide3 */
                    $aide3 = $em->getRepository('AppBundle:Aide3')
                        ->find($id);

                    if(is_null($rang)) {
                       $rang = $this->getDoctrine()
                           ->getRepository('AppBundle:Aide3')
                           ->getRangMaxByAide2($aide3->getAide2()) + 1;
                    }

                    if($aide3)
                    {
                        $aide3->setTitre($titre);
                        $aide3->setRang($rang);
                        $em->persist($aide3);
                    }
                }
                else
                {
                    $aide2 = $this->getDoctrine()
                        ->getRepository('AppBundle:Aide2')
                        ->find($aide_2_id);

                    if(!is_null($aide2)) {

                        if(is_null($rang)){
                            $rang = $this->getDoctrine()
                                    ->getRepository('AppBundle:Aide3')
                                    ->getRangMaxByAide2($aide2) + 1;
                        }


                        $aide3 = new Aide3();
                        $aide3->setAide2($aide2);
                        $aide3->setTitre($titre);
                        $aide3->setRang($rang);
                        $em->persist($aide3);
                    }
                    else{
                        throw new NotFoundHttpException("Aide 2 introuvable.");
                    }
                }

                $em->flush();
                $data = array('erreur' => false);
                return new JsonResponse(json_encode($data));
            }

            throw new NotFoundHttpException("Aide 3 introuvable.");


        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function aide3DeleteAction(Request $request){
        if($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');

            if ($id)
            {
                $em = $this->getDoctrine()
                    ->getManager();
                $aide3 = $this->getDoctrine()
                    ->getRepository('AppBundle:Aide3')
                    ->find($id);

                if ($aide3)
                {
                    $em->remove($aide3);
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
                        'erreur_text' => "Aide 3 introuvable",
                    );
                    return new JsonResponse(json_encode($data), 404);
                }
            }

            throw new NotFoundHttpException("Aide 3 introuvable.");
        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function centreAideSearchAction(Request $request){
        if($request->isXmlHttpRequest()){
            $post = $request->request;
            $search = $post->get('search');

            $utilisateur = $this->getUser();

            $aide3s = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->getListeAide3BySearchUtilisateur($search, $utilisateur);



            /** @var Aide3 $aide3 */
            foreach ($aide3s as $aide3){

                $content = preg_replace('/<h1[^>]*>([\s\S]*?)<\/h1[^>]*>/', '', $aide3->getContenu());
                $content = preg_replace('/<h2[^>]*>([\s\S]*?)<\/h2[^>]*>/', '', $content);
                $content = preg_replace('/<h3[^>]*>([\s\S]*?)<\/h3[^>]*>/', '', $content);
                $content = preg_replace('/&#?[a-z0-9]+;/i', ' ', $content);
                $aide3->setContenu($content);
            }

            $isAdmin = false;

            if($this->isGranted('ROLE_SCRIPTURA_ADMIN')){
                $isAdmin = true;
            }

            return $this->render('AideBundle:Default:centre_aide_search_result.html.twig', array('aide3s' => $aide3s, 'isAdmin' => $isAdmin));
        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function centreAideAction(Request $request)
    {

        $url = $request->getUri();

        $type = 1;
        if (!strpos(strtolower($url), 'guide')) {
            $type = 0;
        }

        $aide1s = array();

        $aide1Temps = $this->getDoctrine()
            ->getRepository('AppBundle:Aide1')
            ->findBy(array('typeAide' => $type), array('rang' => 'ASC'));

        $utilisateur = $this->getUser();


        $isAdmin = false;

        if ($this->isGranted('ROLE_SCRIPTURA_ADMIN')) {
            $isAdmin = true;
        }

        $menuUtilisateurs = $this->getDoctrine()
            ->getRepository('AppBundle:MenuUtilisateur')
            ->getMenuUtilisateur($utilisateur);

        /** @var MenuUtilisateur $menuUtilisateur */
        $menus = array();

        foreach ($menuUtilisateurs as $menuUtilisateur) {
            $menus[] = $menuUtilisateur->getMenu();
        }


        if(!$isAdmin)
        {
            foreach ($aide1Temps as $aide1) {

                $aide3s = $this->getDoctrine()
                    ->getRepository('AppBundle:Aide3')
                    ->getListeAide3ByAide1($aide1);


                if(count($aide3s) > 0) {

                    /** @var Aide3 $aide3 */
                    foreach ($aide3s as $aide3) {
                        if (in_array($aide3->getMenu(), $menus)) {
                            $aide1s[] = $aide1;
                            break;
                        }
                    }
                }
                else{
                    $aide1s[] = $aide1;
                }
            }
        }
        else{
            $aide1s = $aide1Temps;
        }


        /** @var Aide1[] $aide1s */
        return $this->render('AideBundle:Default:index.html.twig',
            array('aide1s' => $aide1s, 'isAdmin' => $isAdmin, 'type' => $type
            ));
    }

    public function centreAide2Action($type,$json)
    {
        $aide1 = $this->getDoctrine()
            ->getRepository('AppBundle:Aide1')
            ->find($json);

        $aide2s = $this->getDoctrine()
            ->getRepository('AppBundle:Aide2')
            ->findBy(array('aide1' => $aide1), array('rang' => 'ASC'));


        /** @var  $aide3s Aide3 */
        $aide3s = array();
        foreach ($aide2s as $aide2) {

            $aide3Temps = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->findBy(array('aide2' => $aide2), array('rang' => 'ASC'));

            foreach ($aide3Temps as $aide3Temp) {


                $utilisateur = $this->getUser();
                $menuUtilisateurs = $this->getDoctrine()
                    ->getRepository('AppBundle:MenuUtilisateur')
                    ->getMenuUtilisateur($utilisateur);

                /** @var MenuUtilisateur $menuUtilisateur */
                $menus = array();

                foreach ($menuUtilisateurs as $menuUtilisateur){
                    $menus[] = $menuUtilisateur->getMenu();
                }

                if(is_null($aide3Temp->getMenu())) {
                    $aide3s[] = $aide3Temp;
                }
                else{
                    if(in_array($aide3Temp->getMenu(), $menus)){
                        $aide3s[] = $aide3Temp;
                    }
                }
            }
        }

        $isAdmin = false;

        if($this->isGranted('ROLE_SCRIPTURA_ADMIN')){
            $isAdmin = true;
        }

        return $this->render('AideBundle:Default:centre_aide_2.html.twig', array(
            'aide1' => $aide1,
            'aide2s' => $aide2s,
            'aide3s' => $aide3s,
            'type' => $type,
            'isAdmin' => $isAdmin));
    }

    public function centreAide2ContenuAction(Request $request){
        if($request->isXmlHttpRequest()) {
            $post = $request->request;

            $aide1Id = $post->get('aide1Id');

            $aide1 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide1')
                ->find($aide1Id);

            $aide2s = $this->getDoctrine()
                ->getRepository('AppBundle:Aide2')
                ->findBy(array('aide1' => $aide1), array('rang' => 'ASC'));

            /** @var  $aide3s Aide3 */
            $aide3s = array();
            foreach ($aide2s as $aide2) {

                $aide3Temps = $this->getDoctrine()
                    ->getRepository('AppBundle:Aide3')
                    ->findBy(array('aide2' => $aide2), array('rang' => 'ASC'));

                foreach ($aide3Temps as $aide3Temp) {
                    $aide3s[] = $aide3Temp;
                }
            }

            $isAdmin = false;

            if($this->isGranted('ROLE_SCRIPTURA_ADMIN')){
                $isAdmin = true;
            }

            return $this->render('AideBundle:Default:centre_aide_2_contenu.html.twig', array(
                'aide2s' => $aide2s,
                'aide3s' => $aide3s,
                'isAdmin' => $isAdmin));
        }
        else{
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    public function centreAide3Action($json, $type)
    {

        /** @var  $aide3 Aide3 */
        $aide3 = $this->getDoctrine()
            ->getRepository('AppBundle:Aide3')
            ->find($json);

        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();



        $menus = $this->getDoctrine()
            ->getRepository('AppBundle:MenuUtilisateur')
            ->getMenuUtilisateur($this->getUser());

        $menus_id = array();

        /** @var $menu MenuParRole|MenuUtilisateur */
        foreach ($menus as $menu) {
            $menus_id[] = $menu->getMenu()->getId();
        }


        if(!is_null($aide3->getMenu())){
            if(!in_array($aide3->getMenu()->getId(), $menus_id)){
                throw new AccessDeniedHttpException("Accès refusé");
            }
        }


        $aide3Contenu = $this->getDoctrine()
            ->getRepository('AppBundle:Aide3Contenu')
            ->getContenuAideByUtilisateur($aide3, $utilisateur);
       
        $isAdmin = false;

        if ($this->isGranted('ROLE_SCRIPTURA_ADMIN')) {
            $isAdmin = true;
        }

//            $aideAssocies = $this->getDoctrine()
//                ->getRepository('AppBundle:AideAssocie')
//                ->findBy(array('aide3Parent' => $aide3));



        //Maka ny precedent sy ny suivant
        $aideprecedent = null;
        $aideSuivant = null;

        $aide3s = $this->getDoctrine()
            ->getRepository('AppBundle:Aide3')
            ->findBy(array('aide2'=> $aide3->getAide2()), array('rang'=> 'ASC'));

        $rang = $aide3->getRang();

        $ind = -1;
        for ($i = 0;  $i < count($aide3s); $i++){
            if($aide3s[$i]->getRang() == $rang){
                $ind = $i;
                break;
            }
        }


        if($ind > 0){


            if(!is_null($aide3s[$ind -1]->getMenu())){
                if(in_array($aide3s[$ind - 1]->getMenu()->getId(), $menus_id)){
                    $aideprecedent= $aide3s[$ind -1];
                }
            }
            else{
                $aideprecedent= $aide3s[$ind -1];
            }


        }


        if($ind < count($aide3s) -1 ){

            if(!is_null($aide3s[$ind +1]->getMenu())){
                if(in_array($aide3s[$ind + 1]->getMenu()->getId(), $menus_id)){
                    $aideSuivant= $aide3s[$ind +1];
                }
            }
            else{
                $aideSuivant= $aide3s[$ind +1];
            }


//            $aideSuivant = $aide3s[$ind +1];
        }



        $aideAssocies = $this->getDoctrine()
            ->getRepository('AppBundle:AideAssocie')
            ->getAssociesByAide3Utilisateur($aide3, $this->getUser());

        $aideRecents = $this->getDoctrine()
            ->getRepository('AppBundle:AideRecentUtilisateur')
            ->findBy(array('utilisateur'=> $this->getUser()));


        return $this->render('AideBundle:Default:centre_aide_3.html.twig', array(
            'aide3' => $aide3,
            'aide3Contenu' => $aide3Contenu,
            'aideAssocies' => $aideAssocies,
            'aideRecents' => $aideRecents,
            'aidePrecedent' => $aideprecedent,
            'aideSuivant' => $aideSuivant,
            'type' => $type,
            'isAdmin' => $isAdmin));

    }

    public function centreAide3AssocieAction(Request $request,$json){
        if($request->isXmlHttpRequest()){

            $rows = array();

            $aide3 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->find($json);

            $aide3Associes = null;

            if(!is_null($aide3)){

                $aide3Associes = $this->getDoctrine()
                    ->getRepository('AppBundle:AideAssocie')
                    ->findBy(array('aide3Parent'=>$aide3));

            }

            $aide3s = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->findBy(array(),array('titre' => 'ASC'));


            foreach ($aide3s as $aide){
                $assoc = false;

                foreach ($aide3Associes as $aideAssocie){
                    if($aideAssocie->getAide3Associe() == $aide){
                        $assoc = true;
                    }
                }

                $rows[] = array('id' => $aide->getId(),
                    'cell' => array(
                        $aide->getTitre(),
                        $assoc,
                        '<i class="fa fa-save icon-action js_save_aide_3_associe" title="Enregistrer"></i>'
                    )
                );
            }

            $liste = array('rows'=>$rows);
            return new JsonResponse($liste);
        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function centreAide3AssocieEditAction(Request $request, $json){
        if($request->isXmlHttpRequest()){
            $aide3 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->find($json);

            $aide3AssocieGrid = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->find($request->request->get('id'));

            $isAssoc = $request->request->get('aide-3-associe');

            if($isAssoc == "Yes"){
                $isAssoc = true;
            }
            else{
                $isAssoc = false;
            }

            $aide3AssocieBases = $this->getDoctrine()
                ->getRepository('AppBundle:AideAssocie')
                ->findBy(array('aide3Parent' => $aide3));

            $em = $this->getDoctrine()->getManager();
            $trouve = false;

            $aide3AssocieOld = null;

            foreach ($aide3AssocieBases as $aide3AssocieBase){
                if($aide3AssocieBase->getAide3Associe() == $aide3AssocieGrid){
                    $aide3AssocieOld = $aide3AssocieBase;
                    $trouve = true;
                    break;
                }
            }

            if($isAssoc){
                if(!$trouve) {
                    try {
                        $newAide3Assoc = new AideAssocie();
                        $newAide3Assoc->setAide3Associe($aide3AssocieGrid);
                        $newAide3Assoc->setAide3Parent($aide3);

                        $em->persist($newAide3Assoc);
                        $em->flush();
                    }
                    catch (\Exception $e){
                        return new JsonResponse(-1);
                    }
                }
            }

            else{
                if($trouve && !is_null($aide3AssocieOld)){

                    $em->remove($aide3AssocieOld);
                    $em->flush();
                }
            }

            return new JsonResponse(1);

        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function centreAide1DeleteAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $post = $request->request;

            $em = $this->getDoctrine()
                ->getManager();

            $aide1Id = $post->get('aide1Id');
            $aidetype = $post->get('aideType');

            if (!($aide1Id == 0 || $aide1Id == ''))  {
                $aide1 = $this->getDoctrine()
                    ->getRepository('AppBundle:Aide1')
                    ->find($aide1Id);

                $em->remove($aide1);;
                $em->flush();
            }

            $aide1s = $this->getDoctrine()
                ->getRepository('AppBundle:Aide1')
                ->findBy(array('typeAide' => $aidetype));

            $isAdmin = false;

            if($this->isGranted('ROLE_SCRIPTURA_ADMIN')){
                $isAdmin = true;
            }

            return $this->render('AideBundle:Default:centre_aide_1.html.twig', array(
                'aide1s' => $aide1s,
                'isAdmin' => $isAdmin,
                'type' => $aidetype
                ));

        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function centreAide1EditAction(Request $request, $json){

        if($request->isXmlHttpRequest()){

            $post = $request->request;

            //Affichage edit
            if($json == 0) {

                $aide_1_id = $post->get('aide_1_id');

                $aide_type = $post->get('aide_type');

                $aide1 = $this->getDoctrine()
                    ->getRepository('AppBundle:Aide1')
                    ->find($aide_1_id);


                $isAdmin = false;

                if($this->isGranted('ROLE_SCRIPTURA_ADMIN')){
                    $isAdmin = true;
                }

                return $this->render('AideBundle:Default:centre_aide_1_edit.html.twig',
                    array('aide1' => $aide1, 'type' => $aide_type,'isAdmin' => $isAdmin));
            }

            //Enregistrement
            else{

                $em = $this->getDoctrine()
                    ->getManager();

                $aide_1_id = $post->get('aide_1_id');
                $aide_type = $post->get('aide_type');

                $titre = $post->get('titre');
                $contenu = $post->get('contenu');

                if($aide_1_id == 0 || $aide_1_id ==''){
                    $aide1 = new Aide1();
                    $aide1->setContenu($contenu);
                    $aide1->setTitre($titre);
                    $aide1->setTypeAide($aide_type);
                }
                else{
                    $aide1 = $this->getDoctrine()
                        ->getRepository('AppBundle:Aide1')
                        ->find($aide_1_id);

                    $aide1->setContenu($contenu);
                    $aide1->setTitre($titre);
                    $aide1->setTypeAide($aide_type);
                }

                $em->persist($aide1);
                $em->flush();

//                $aide1s = $this->getDoctrine()
//                    ->getRepository('AppBundle:Aide1')
//                    ->findAll();

                $aide1s = $this->getDoctrine()
                    ->getRepository('AppBundle:Aide1')
                    ->findBy(array('typeAide' => $aide_type));

                $isAdmin = false;

                if($this->isGranted('ROLE_SCRIPTURA_ADMIN')){
                    $isAdmin = true;
                }

                return $this->render('AideBundle:Default:centre_aide_1.html.twig',
                    array(
                        'aide1s' => $aide1s,
                        'isAdmin' => $isAdmin,
                        'type' => $aide_type
                    ));

            }

        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function centreAide2DeleteAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $post = $request->request;

            $aide2Id = $post->get('aide2Id');


            $em = $this->getDoctrine()
                ->getManager();


            if (!($aide2Id == 0 || $aide2Id == '')) {

                $aide2 = $this->getDoctrine()
                    ->getRepository('AppBundle:Aide2')
                    ->find($aide2Id);

                $em->remove($aide2);

                $em->flush();
            }

            return new Response(3);
        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function centreAide2EditAction(Request $request, $json){

        if($request->isXmlHttpRequest()){

            $post = $request->request;

            $aide_2_id = $post->get('aide_2_id');

            //Affichage edit
            if($json == 0) {

                $aide2 = $this->getDoctrine()
                    ->getRepository('AppBundle:Aide2')
                    ->find($aide_2_id);

                $isAdmin = false;

                if($this->isGranted('ROLE_SCRIPTURA_ADMIN')){
                    $isAdmin = true;
                }

                return $this->render('AideBundle:Default:centre_aide_2_titre_edit.html.twig', array('aide2' => $aide2, 'isAdmin' => $isAdmin));
            }

            //Enregistrement
            else{

                $em = $this->getDoctrine()
                    ->getManager();

                $aide_2_id = $post->get('aide_2_id');
                $aide_1_id = $post->get('aide_1_id');

                $titre = $post->get('titre');
//                $contenu = $post->get('contenu');

                if($aide_2_id == 0 || $aide_2_id ==''){
                    $aide2 = new Aide2();

                    $aide1 = $this->getDoctrine()
                        ->getRepository('AppBundle:Aide1')
                        ->find($aide_1_id);

                    $aide2->setTitre($titre);
                    $aide2->setAide1($aide1);
                }
                else{
                    $aide2 = $this->getDoctrine()
                        ->getRepository('AppBundle:Aide2')
                        ->find($aide_2_id);

                    $aide2->setTitre($titre);
                }

                $em->persist($aide2);
                $em->flush();


                return new Response($aide2->getId());

            }

        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function menuAction(Request $request)
    {

        if($request->isXmlHttpRequest()) {
            $rows = array();

            $menus_id = [];

            if ($this->isGranted('ROLE_SCRIPTURA_ADMIN')) {
                $menus = $this->getDoctrine()
                    ->getRepository('AppBundle:Menu')
                    ->findAll();
                /** @var $menu Menu */
                foreach ($menus as $menu) {
                    $menus_id[] = $menu->getId();
                }
                $menus_complet = $this->getDoctrine()
                    ->getRepository('AppBundle:Menu')
                    ->getAllMenu();
            } else {
                $menus = $this->getDoctrine()
                    ->getRepository('AppBundle:MenuUtilisateur')
                    ->getMenuUtilisateur($this->getUser());
                /** @var $menu MenuParRole|MenuUtilisateur */
                foreach ($menus as $menu) {
                    $menus_id[] = $menu->getMenu()->getId();
                }

                $menus_complet = $this->getDoctrine()
                    ->getRepository('AppBundle:MenuUtilisateur')
                    ->getMenuParentUtilisateur($this->getUser());
            }

            foreach ($menus_complet as $menu) {
                $rows[] = array(
                    'id' => 'm1n' . $menu->getId(),
                    'parent' => '#',
                    'text' => $menu->getLibelle()
                );

                $menu_niv2 = $this->getDoctrine()
                    ->getRepository('AppBundle:Menu')
                    ->findBy(array('menu' => $menu));

                /** @var  $menu2 Menu */
                foreach ($menu_niv2 as $menu2) {
                    $rows[] = array(
                        'id' => 'm2n' . $menu2->getId(),
                        'parent' => 'm1n' . $menu->getId(),
                        'text' => $menu2->getLibelle()
                    );

                    $menu_niv3 = $this->getDoctrine()
                        ->getRepository('AppBundle:Menu')
                        ->findBy(array('menu' => $menu2));

                    /** @var  $menu3 Menu */
                    foreach ($menu_niv3 as $menu3) {
                        $rows[] = array(
                            'id' => 'm3n' . $menu3->getId(),
                            'parent' => 'm2n' . $menu2->getId(),
                            'text' => $menu3->getLibelle()
                        );
                    }
                }
            }

            $liste = array('data' => $rows);

            return (new JsonResponse($liste));
        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }

    }

    public function menuEditAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $post = $request->request;

            $menuId = $post->get('menuId');
            $aide3Id = $post->get('aide3Id');

            $menu = $this->getDoctrine()
                ->getRepository('AppBundle:Menu')
                ->find($menuId);

            $aide3 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->find($aide3Id);

            $em = $this->getDoctrine()
                ->getEntityManager();

            if (!is_null($aide3)) {

                $aide3->setMenu($menu);

                $em->persist($aide3);
                $em->flush();

                return new JsonResponse(2);
            } else {
                return new JsonResponse('Aide 3 non trouvé');
            }


        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    public function motCleEditAction(Request $request){

        if ($request->isXmlHttpRequest()) {

            $post = $request->request;

            $aide3Id = $post->get('aide3Id');

            $motCle = $post->get('motCle');

            if($motCle ==''){
                $motCle = null;
            }

            $aide3 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->find($aide3Id);

            $em = $this->getDoctrine()
                ->getEntityManager();

            if (!is_null($aide3)) {

                $aide3->setMotCle($motCle);

                $em->persist($aide3);
                $em->flush();

                return new JsonResponse(2);
            } else {
                return new JsonResponse('Aide 3 non trouvé');
            }


        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }

    }

    public function menuFormAction(Request $request){

        if($request->isXmlHttpRequest()){

            $post = $request->request;

            $aide3Id = $post->get('aide3Id');

            $aide3 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->find($aide3Id);

            $res = array('libelle'=>'', 'id'=>'');


            if(!is_null($aide3)){

                /** @var $aide3 Aide3 */
                if(!is_null($aide3->getMenu())) {
                    $res = array('libelle' => $aide3->getMenu()->getLibelle(), 'id' => $aide3->getMenu()->getId());
                }

                return new JsonResponse($res);
            }
            else{
                return new JsonResponse($res);
            }


        }
        else{
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }

    public function motCleFormAction(Request $request){
        if($request->isXmlHttpRequest()){
            $post = $request->request;

            $aide3Id = $post->get('aide3Id');

            $aide3 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->find($aide3Id);

            $res = '';

            if(!is_null($aide3)){

                /** @var $aide3 Aide3 */
//                if(!is_null($aide3->getMenu())) {
//                    $res = $aide3->getMotCle();
//                }

                if(!is_null($aide3->getMotCle())) {
                    return new JsonResponse($aide3->getMotCle());
                }

                return $res;
            }


            return new JsonResponse($res);

        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function centreAide3TextEditAction(Request $request){
        if ($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getEntityManager();

            $post = $request->request;

            $aide3Id = $post->get('aide3Id');

            $type = $post->get('typeContenu');

            if ($aide3Id!= '0') {
                try {
                    $aide3 = $this->getDoctrine()
                        ->getRepository('AppBundle:Aide3')
                        ->find($aide3Id);

                    $aide3Contenus = $this->getDoctrine()
                        ->getRepository('AppBundle:Aide3Contenu')
                        ->findBy(array('aide3' => $aide3, 'typeContenu' => $type));

                    //Manala <div class="xxx"> </div>
                    $aide3ContenuText = preg_replace('/\<[\/]{0,1}div[^\>]*\>/i', '', $post->get('aide3Contenu'));

                    if(count($aide3Contenus) > 0){
                        $aide3Contenu = $aide3Contenus[0];
                        $aide3Contenu->setContenu($aide3ContenuText);

                        $em->flush();
                    }
                    else{
                        $aide3Contenu = new Aide3Contenu();
                        $aide3Contenu->setAide3($aide3);
                        $aide3Contenu->setContenu($aide3ContenuText);
                        $aide3Contenu->setTypeContenu($type);

                        $em->persist($aide3Contenu);

                        $em->flush();
                    }

                    $em->flush();

                    return new Response(1);

                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            } else {
                return new Response(-1);
            }

        }
        else
        {
            throw new AccessDeniedHttpException("Accès refusé.");
        }
    }

    public function centreAide3TitreEditAction(Request $request){
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $post = $request->request;
            $aide3Id = $post->get('aide3Id');

            if ($aide3Id != '-1') {
                try {
                    $aide3 = $this->getDoctrine()
                        ->getRepository('AppBundle:Aide3')
                        ->find($aide3Id);

                    $instructionTxt = preg_replace('/\<[\/]{0,1}div[^\>]*\>/i', '', $post->get('aide3Texte'));
                    $aide3->setContenu($instructionTxt);

                    $em->flush();
                    return new Response(2);
                } catch (Exception $e) {
                    return new Response($e->getMessage());
                }

            } else {
                return new Response(-1);
            }
        } else {
            throw new AccessDeniedHttpException('Accès refusé');
        }
    }


    public function centreAide3CheckAction(Request $request){
        if($request->isXmlHttpRequest()) {

            $post = $request->request;
            $aide3Id = $post->get("aide3Id");
            $type =$post->get('typeContenu');

            $existed = false;

            $aide3 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->find($aide3Id);

            $content = '';
            if(!is_null($aide3)) {
                $aide3Contenus = $this->getDoctrine()
                    ->getRepository('AppBundle:Aide3Contenu')
                    ->findBy(array("aide3" => $aide3, "typeContenu" => $type));
                if(count($aide3Contenus) > 0){
                    $existed = true;
                    $content = $aide3Contenus[0]->getContenu();
                }
            }

            return new JsonResponse(array( 'existed' => $existed, 'content' => $content  ));
        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function affichageMinAction(Request $request, $json){
        if($request->isXmlHttpRequest()){


            $aide3 = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3')
                ->find($json);

            $isAdmin = false;

            if($this->isGranted('ROLE_SCRIPTURA_ADMIN')){
                $isAdmin = true;
            }

            /** @var Utilisateur $utilisateur */
            $utilisateur = $this->getUser();

            /** @var Aide3Contenu $aide3Contenu */
            $aide3Contenu = $this->getDoctrine()
                ->getRepository('AppBundle:Aide3Contenu')
                ->getContenuAideByUtilisateur($aide3, $utilisateur);


            return $this->render('AideBundle:Default:aide_min.html.twig', array(
                'contenuAide' => $aide3,
                'isAdmin' => $isAdmin,
                'aide3Contenu' => $aide3Contenu
            ));

        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }

    public function miniatureAction(Request $request,$json){

        if($request->isXmlHttpRequest()) {

            switch ($json) {
                //Principal: suggestion, search
                case 0:

                    $request = $this->container->get('request');

                    $post = $request->request;
                    $routePath = $post->get('url');
                    $availableApiRoutes = [];


                    foreach ($this->get('router')->getRouteCollection()->all() as $name => $route) {
                        try {
                            $route = $route->compile();

                            $emptyVars = [];
                            foreach ($route->getVariables() as $v) {
                                $emptyVars[$v] = $v;
                            }
                            $url = $this->generateUrl($name, $emptyVars);

                            $availableApiRoutes[] = ["name" => $name, "url" => $url, "variables" => $route->getVariables()];
                        }
                        catch (\Exception $e){
                        }
                    }

                    $matchedRoute = array();
                    foreach ($availableApiRoutes as $rt){
                        if (strpos($rt["url"], parse_url($routePath, PHP_URL_PATH)) !== false) {
                            $matchedRoute[] = $rt["name"];
                        }
                    }

                    $suggestions = array();

                    $suggestionMenus = array();

                    $menus_id = array();

                    if(count($matchedRoute) > 0) {

                        $utilisateur = $this->getUser();



                        $menus = $this->getDoctrine()
                            ->getRepository('AppBundle:MenuUtilisateur')
                            ->getMenuUtilisateurEx($utilisateur, $menus_id);

                        $suggestionMenus = $this->getDoctrine()
                            ->getRepository('AppBundle:Aide3')
                            ->getListeAide3ByRoute($matchedRoute, $menus_id);
                    }

                    if(count($suggestionMenus) > 0) {
                        foreach ($suggestionMenus as $suggestionMenu) {
                            $suggestions[] = $suggestionMenu;
                        }


                        foreach ($suggestionMenus as $suggestionMenu) {
                            $suggestionAssoc = $this->getDoctrine()
                                ->getRepository('AppBundle:AideAssocie')
                                ->findBy(array('aide3Parent' => $suggestionMenu));

                            /** @var AideAssocie $assoc */
                            foreach ($suggestionAssoc as $assoc){
                                $allowInsert = false;
                                if(!is_null($assoc->getAide3Associe())){
                                    if(!is_null($assoc->getAide3Associe()->getMenu())){
                                        if(in_array($assoc->getAide3Associe()->getMenu()->getId(), $menus_id)){
                                            $allowInsert = true;
                                        }
                                    }
                                }
                                if(!in_array($assoc->getAide3Associe(), $suggestions)) {
                                    if($allowInsert) {
                                        $suggestions[] = $assoc->getAide3Associe();
                                    }
                                }
                            }
                        }
                    }

//                    $suggestionSMenus = $this->getDoctrine()
//                        ->getRepository('AppBundle:Aide3')
//                        ->getListeAide3SansMenu();
//
//                    if(count($suggestionSMenus) > 0) {
//                        foreach ($suggestionSMenus as $suggestionSMenu) {
//                            $suggestions[] = $suggestionSMenu;
//                        }
//                    }


                    return $this->render('AideBundle:Default:miniature.html.twig', array(
                        'suggestions' => $suggestions
                    ));

                    break;

                //Laissez nous un message
                case 1:
                    return $this->render('AideBundle:Default:mail.html.twig');
                    break;

                case 2:
//                    $suggestions = $this->getDoctrine()
//                        ->getRepository('AppBundle:Aide3')
//                        ->findBy(array('suggestion'=>1));

                    $suggestions = null;

                    //Recherche
                    if ($json == 2) {

                        $post = $request->request;

                        $searchText = $post->get('searchText');

                        /** @var  $suggestions Aide3 */

                        $suggestions = $this
                            ->getDoctrine()
                            ->getRepository('AppBundle:Aide3')
                            ->getListeAide3BySearchUtilisateur($searchText, $this->getUser());
                    }

                    return $this->render('AideBundle:Default:miniature.html.twig', array(
                        'suggestions' => $suggestions
                    ));
                    break;
            }
        }
        else{
            throw new AccessDeniedHttpException();
        }
    }

    public function sendMailAction(Request $request){
        if($request->isXmlHttpRequest()){

            $post = $request->request;
            /** @var Utilisateur $utilisateur */
            $utilisateur = $this->getUser();
            $texte = $post->get('texte');
            $destinataires = array();
            $destinataires[] = 'maharoarijaona@gmail.com';
            $message = \Swift_Message::newInstance()
                ->setSubject("Aide Lesexperts.biz")
                ->setFrom($utilisateur->getEmail(), $utilisateur->getEmail())
                ->setTo(['maharoarijaona@gmail.com']);
            $message->setBody( $texte
                , 'text/html');

            $this->get('mailer')
                ->send($message);

            return $this->render('AideBundle:Default:miniature.html.twig',array('suggestions' => null));

        }
        else{
            throw new AccessDeniedHttpException();
        }
    }

    public function uploadAction(Request $request){

        if($request->isXmlHttpRequest()) {

            $directory = "AIDES/";

            $fs = new Filesystem();
            try {
                $fs->mkdir($directory, 0777);
            } catch (IOExceptionInterface $e) {
            }

            $file = $request->files->get('file');

            $file_name = $file->getClientOriginalName();
            $file->move($directory, $file_name);


//PICDATA
            $chemin = '/'.$directory.$file_name;

//LOCAL
//            $chemin = '/picdata/web/' . $directory . $file_name;

//192.168.0.5
//            $chemin = '/newpicdata/web/' . $directory. $file_name;

            return new JsonResponse($chemin);
        }
        else{
            throw new AccessDeniedHttpException("Accès refusé");
        }


    }

    public function testMenuAction(){


        $utilisateurTsotra  = $this->getDoctrine()
            ->getRepository('AppBundle:Utilisateur')
            ->find(876);

        $res = $this->getDoctrine()
            ->getRepository('AppBundle:Aide3')
            ->getListeAide3BySearchUtilisateur('finan', $utilisateurTsotra);


        return new JsonResponse(1);


    }


    public function jivoChatAction(){

        return $this->render('@Aide/Default/jivo_chat.html.twig');
    }

}