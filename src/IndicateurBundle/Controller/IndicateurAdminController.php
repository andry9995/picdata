<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/11/2016
 * Time: 10:33
 */

namespace IndicateurBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurCell;
use AppBundle\Entity\IndicateurGroup;
use AppBundle\Entity\IndicateurOperande;
use AppBundle\Entity\IndicateurPack;
use AppBundle\Entity\IndicateurTypeGraphe;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\BooleanNode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndicateurAdminController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $rubriques = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques(0);
        $super_rubriques = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques(1);
        $hyper_rubriques = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques(2);

        //$adminGranted = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $adminGranted = ($this->getUser()->getAccesUtilisateur()->getType() <= 2);

        return $this->render('IndicateurBundle:IndicateurAdmin:index.html.twig',array('adminGranted'=>$adminGranted,
            'rubriques'=>$rubriques, 'super_rubriques'=>$super_rubriques, 'hyper_rubriques'=>$hyper_rubriques));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function groupsAction(Request $request)
    {
        $post = $request->request;
        $paramGen = intval($post->get('param_gen') == 1);
        $dossier = null;
        $client = null;

        if(!$paramGen)
        {
            $dossier = Boost::deboost($post->get('dossier'),$this);
            $client = Boost::deboost($post->get('client'),$this);
            if(is_bool($dossier) || is_bool($client)) return new Response('security');
            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
            if($dossier == null) $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
            else
            {
                $indicateurGroup = $this->getDoctrine()->getRepository('AppBundle:IndicateurSpecGroup')->getIndicateurGroup($dossier);
                $dossier->setIndicateurGroup($indicateurGroup);
                $client = null;
            }
        }
        $dStyles = $this->getDoctrine()->getRepository('AppBundle:IndicateurCell')->getDefaultStyles();
        $groups = $this->getDoctrine()->getRepository('AppBundle:IndicateurGroup')->getGroups($paramGen,$client,$dossier);
        return $this->render('IndicateurBundle:IndicateurAdmin:groups.html.twig',array('groups'=>$groups,'client'=>$client,'dossier'=>$dossier,'dStyles'=>$dStyles));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function packsAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        $packs = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->getListe($dossier);
        return $this->render('IndicateurBundle:IndicateurAdmin:pack.html.twig',array('packs'=>$packs,'dossier'=>$dossier));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editPackAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        $dossier = Boost::deboost($post->get('dossier'),$this);
        $client = Boost::deboost($post->get('client'),$this);
        $indicateurPack = Boost::deboost($post->get('pack'),$this);
        if(is_bool($client) || is_bool($dossier) || is_bool($indicateurPack)) return new Response('security');

        $em = $this->getDoctrine()->getEntityManager();
        $indicateurPack = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->getById($indicateurPack);

        //show edit
        if($action == 0)
        {
            return $this->render('IndicateurBundle:IndicateurAdmin:pack-edit.html.twig',array('pack'=>$indicateurPack));
        }
        //add
        elseif($action == 1)
        {
            $libelle = trim($post->get('libelle'));
            if($indicateurPack == null)
            {
                $indicateurGroup = Boost::deboost($post->get('group_pack'),$this);
                if(is_bool($indicateurGroup)) return new Response('security');
                $indicateurGroup = $this->getDoctrine()->getRepository('AppBundle:IndicateurGroup')->getById($indicateurGroup);
                $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
                if($dossier != null) $client = null;
                else $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);

                $indicateurPack = new IndicateurPack();
                $indicateurPack->setIndicateurGroup($indicateurGroup);
                $indicateurPack->setClient($client);
                $indicateurPack->setDossier($dossier);
                $indicateurPack->setLibelle($libelle);
                $em->persist($indicateurPack);
            }
            else
            {
                $indicateurPack->setLibelle($libelle);
            }

            try
            {
                $em->flush();
                return new Response($indicateurPack->getId());
            }
            catch (UniqueConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }
        elseif ($action == 2)
        {
            $em->remove($indicateurPack);

            try
            {
                $em->flush();
                return new Response(1);
            }
            catch (ForeignKeyConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editIndicateurAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateur)) return new Response('security');
        $indicateur = $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($indicateur);
        $graphes = $this->getDoctrine()->getRepository('AppBundle:TypeGraphe')->getAll();

        $em = $this->getDoctrine()->getManager();

        //show edit
        if($action == 0)
        {
            if($indicateur)
                $indicateur = $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getComplete($indicateur);

            return $this->render('IndicateurBundle:IndicateurAdmin:indicateur-edit.html.twig',array('indicateur'=>$indicateur,'graphes'=>$graphes));
        }
        //add , edit
        elseif ($action == 1)
        {
            $libelle = $post->get('libelle');
            $formule = $post->get('formule');
            $unite = $post->get('unite');
            $isTable = intval($post->get('is_table'));
            $typeOperation = intval($post->get('type_operation'));
            $indicateurPack = null;
            $max = intval($post->get('limit'));
            $isDecimal = intval($post->get('is_decimal'));
            $description = $post->get('description');
            $analyse = bindec($post->get('analyse'));
            $periode = bindec($post->get('periode'));
            $libelleAfficher = $post->get('nom_afficher');
            $theme = intval($post->get('theme'));
            $showExerciceValide = intval($post->get('show_exercice_valide'));

            if(!$indicateur)
            {
                $client = Boost::deboost($post->get('client'),$this);
                $dossier = Boost::deboost($post->get('dossier'),$this);
                $indicateurPack = Boost::deboost($post->get('pack_indicateur'),$this);

                if(is_bool($indicateurPack) || is_bool($dossier) || is_bool($client)) return new Response('security');
                $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);

                if($dossier == null) $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
                else $client = null;

                $indicateurPack = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->getById($indicateurPack);
                $indicateur = new Indicateur();
                $indicateur->setIndicateurPack($indicateurPack)
                    ->setClient($client)
                    ->setDossier($dossier);
            }

            $indicateur
                ->setLibelle($libelle)
                ->setFormule($formule)
                ->setUnite($unite)
                ->setIsTable($isTable)
                ->setTypeOperation($typeOperation)
                ->setMax($max)
                ->setIsDecimal($isDecimal)
                ->setDescription($description)
                ->setAnalyse($analyse)
                ->setPeriode($periode)
                ->setLibelleAffiche($libelleAfficher)
                ->setTheme($theme)
                ->setShowExerciceClos($showExerciceValide);

            if($indicateurPack != null) $em->persist($indicateur);

            $em->flush();

            //delete old graphes
            $this->getDoctrine()->getRepository('AppBundle:IndicateurTypeGraphe')->deleteOldGraphes($indicateur);

            //delete old operandes
            $this->getDoctrine()->getRepository('AppBundle:IndicateurOperande')->deleteOldOperande($indicateur);

            //insert new graphes
            $id_graphes = array();
            $temps = json_decode($post->get('graphes'));
            foreach ($temps as $temp)
            {
                $id = Boost::deboost($temp,$this);
                if(is_bool($id)) return new Response('security');
                $id_graphes[] = intval($id);
            }
            $newGraphes = $this->getDoctrine()->getRepository('AppBundle:TypeGraphe')->getByIds($id_graphes);
            foreach ($newGraphes as $newGraphe)
            {
                $indicateurTypeGraphe = new IndicateurTypeGraphe();
                $indicateurTypeGraphe->setIndicateur($indicateur);
                $indicateurTypeGraphe->setTypeGraphe($newGraphe);
                $em->persist($indicateurTypeGraphe);
            }

            //insert new operandes
            $temps = json_decode($post->get('operandes'));
            foreach ($temps as $temp)
            {
                $id = Boost::deboost($temp->id,$this);
                if(is_bool($id)) return new Response('security');
                $rubrique = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getById($id);

                $indicateurOperande = new IndicateurOperande();
                $indicateurOperande
                    ->setIndicateur($indicateur)
                    ->setRubrique($rubrique)
                    ->setVariationN(intval($temp->variation));
                $em->persist($indicateurOperande);
            }

            $em->flush();
            return new Response(1);
        }
        elseif($action == 2)
        {
            if($indicateur != null) $em->remove($indicateur);
            try
            {
                $em->flush();
                return new Response(1);
            }
            catch (ForeignKeyConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function changeCheckAction(Request $request)
    {
        $post = $request->request;
        $entity = Boost::deboost($post->get('entity'),$this);
        $client = Boost::deboost($post->get('client'),$this);
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($client) || is_bool($dossier) || is_bool($entity)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        if($dossier == null) $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
        else $client = null;

        $oldStatus = (intval($post->get('status')) == 1);
        $type = intval($post->get('type'));

        if($type == 0)
        {
            $indicateurGroup = $this->getDoctrine()->getRepository('AppBundle:IndicateurGroup')->getById($entity);
            $this->getDoctrine()->getRepository('AppBundle:IndicateurSpecGroup')->changeEnabledTo($indicateurGroup,$dossier,$oldStatus);
        }
        elseif($type == 1)
        {
            $indicateurPack = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->getById($entity);
            $this->getDoctrine()->getRepository('AppBundle:IndicateurSpecPack')->changeEnabledTo($indicateurPack,$client,$dossier,$oldStatus);
        }
        elseif($type == 2)
        {
            $indicateur = $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($entity);
            $this->getDoctrine()->getRepository('AppBundle:IndicateurSpecIndicateur')->changeEnabledTo($indicateur,$client,$dossier,$oldStatus);
        }

        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function reloadPackAction(Request $request)
    {
        $post = $request->request;
        $indicateurPack = Boost::deboost($post->get('pack'),$this);
        $client = Boost::deboost($post->get('client'),$this);
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($indicateurPack) || is_bool($dossier) || is_bool($client)) return new Response('security');
        $indicateurPack = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->getById($indicateurPack);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);

        if($dossier == null) $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
        else $client = null;

        $indicateurPacks = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->getPacksInGroups($indicateurPack->getIndicateurGroup(),$client,$dossier,$indicateurPack);
        return $this->render('IndicateurBundle:IndicateurAdmin:indicateurs.html.twig',array('client'=>$client,'dossier'=>$dossier,'pack'=>$indicateurPacks[0]));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editCellAction(Request $request)
    {
        $post = $request->request;
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateur)) return new Response('security');

        $row = intval($post->get('row'));
        $col = intval($post->get('col'));
        $formule = $post->get('formule');
        $operandes = json_decode($post->get('operandes'));
        $variations = json_decode($post->get('variations'));
        $isEtat = (intval($post->get('is_etat')) == 1);

        $indicateur = ($isEtat) ?
            $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getById($indicateur) :
            $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($indicateur);

        $indicateurCell = $this->getDoctrine()->getRepository('AppBundle:IndicateurCell')->getByRowCol($indicateur,$row,$col,$isEtat);
        $em = $this->getDoctrine()->getEntityManager();

        $isFormule = (count($operandes) > 0) ? 1 : 0;
        $fontFamily = $post->get('cell_font_family');
        $fontBold = $post->get('cell_font_weight');
        $fontItalic = $post->get('cell_italic');
        $textAlign = $post->get('cell_text_align');
        $textIndent = $post->get('cell_indent');
        $border = bindec($post->get('cell_border'));
        $color = $post->get('cell_color');
        $bg = $post->get('cell_bg');
        if($indicateurCell != null)
        {
            /*if($formule == '')
            {
                $em->remove($indicateurCell);
                $indicateurFormatCol = $this->getDoctrine()->getRepository('AppBundle:IndicateurFormatCol')->getColFormatByCol($indicateur,$col,$isEtat);
                if($indicateurFormatCol != null) $em->remove($indicateurFormatCol);
            }
            else
            {*/
                //delete old operandes
                $this->getDoctrine()->getRepository('AppBundle:IndicateurOperande')->deleteOldOperandeCell($indicateurCell);
                $indicateurCell->setFormule($formule);
                $indicateurCell->setIsFormule($isFormule);
                $indicateurCell->setFontFamily($fontFamily);
                $indicateurCell->setFontBold($fontBold);
                $indicateurCell->setFontItalic($fontItalic);
                $indicateurCell->setTextAlign($textAlign);
                $indicateurCell->setIndent($textIndent);
                $indicateurCell->setBorder($border);
                $indicateurCell->setColor($color);
                $indicateurCell->setBgColor($bg);
            /*}*/
        }
        else
        {
            /*if($formule != '')
            {*/
                $indicateurCell = new IndicateurCell();
                $indicateurCell->setFormule($formule);
                $indicateurCell->setIsFormule($isFormule);
                if($isEtat) $indicateurCell->setEtatRegimeFiscal($indicateur);
                else $indicateurCell->setIndicateur($indicateur);
                $indicateurCell->setRow($row);
                $indicateurCell->setCol($col);
                $indicateurCell->setFontFamily($fontFamily);
                $indicateurCell->setFontBold($fontBold);
                $indicateurCell->setFontItalic($fontItalic);
                $indicateurCell->setTextAlign($textAlign);
                $indicateurCell->setIndent($textIndent);
                $indicateurCell->setBorder($border);
                $indicateurCell->setColor($color);
                $indicateurCell->setBgColor($bg);
                $indicateurCell->setStyles('');
                $em->persist($indicateurCell);
            /*}*/
        }

        $em->flush();
        if(count($operandes) > 0)
        {
            $index = 0;
            foreach ($operandes as $operande)
            {
                /*$rubrique = Boost::deboost($operande,$this);
                if(is_bool($rubrique)) return new Response('security');*/
                $rubrique = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getById($operande);

                $indicateurOperande = new IndicateurOperande();
                $indicateurOperande->setIndicateurCell($indicateurCell);
                $indicateurOperande->setRubrique($rubrique);
                $indicateurOperande->setVariationN(intval($variations[$index]));
                $em->persist($indicateurOperande);
                $index++;
            }
            $em->flush();
        }
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editRowColAction(Request $request)
    {
        $post = $request->request;
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateur)) return new Response('security');

        $isEtat = (intval($post->get('is_etat')) == 1);

        $indicateur = ($isEtat) ?
            $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getById($indicateur) :
            $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($indicateur);
        $action = intval($post->get('action'));

        $rowDeleted = intval($post->get('row_deleted'));
        $colDeleted = intval($post->get('col_delete'));

        $result = $this->getDoctrine()->getRepository('AppBundle:Indicateur')->editRowCol($indicateur,$action,$rowDeleted,$colDeleted,$isEtat);
        return new Response($result);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function changeFormatColAction(Request $request)
    {
        $post = $request->request;
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateur)) return new Response('security');
        $format = intval($post->get('format'));
        $decimal = intval($post->get('decimal'));
        $col = intval($post->get('col'));
        $isEtat = (intval($post->get('is_etat')) == 1);
        $indicateur = ($isEtat) ?
            $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getById($indicateur) :
            $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($indicateur);
        return new Response($this->getDoctrine()->getRepository('AppBundle:IndicateurFormatCol')->changeFormat($indicateur,$format,$decimal,$col,$isEtat));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function changeRangPacksAction(Request $request)
    {
        $post = $request->request;
        $liste =json_decode($post->get('liste'));
        $type = intval($post->get('type'));

        if($type == 0)
        {
            $indicateurPacks = array();
            foreach ($liste as $pack)
            {
                $indicateurPack = Boost::deboost($pack,$this);
                if(is_bool($indicateurPack)) return new Response('security');
                $indicateurPack = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->getById($indicateurPack);
                $indicateurPacks[] = $indicateurPack;
            }
            $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->arrangeRang($indicateurPacks);
        }
        elseif($type == 1)
        {
            $indicateurs = array();
            foreach ($liste as $ind)
            {
                $indicateur = Boost::deboost($ind,$this);
                if(is_bool($indicateur)) return new Response('security');
                $indicateur = $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($indicateur);
                $indicateurs[] = $indicateur;
            }
            $this->getDoctrine()->getRepository('AppBundle:Indicateur')->arrangeRang($indicateurs);
        }
        elseif($type == 2)
        {
            $indicateurGroups = array();
            foreach ($liste as $ind)
            {
                $indicateurGroup = Boost::deboost($ind,$this);
                if(is_bool($indicateurGroup)) return new Response('security');
                $indicateurGroup = $this->getDoctrine()->getRepository('AppBundle:IndicateurGroup')->getById($indicateurGroup);
                $indicateurGroups[] = $indicateurGroup;
            }
            $this->getDoctrine()->getRepository('AppBundle:Indicateur')->arrangeRang($indicateurGroups);
        }

        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editGroupAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));
        $indicateurGroup = Boost::deboost($post->get('indicateur_group'),$this);
        if(is_bool($indicateurGroup)) return new Response('security');
        $indicateurGroup = $this->getDoctrine()->getRepository('AppBundle:IndicateurGroup')->getById($indicateurGroup);
        $em = $this->getDoctrine()->getEntityManager();

        if($action == 0)
        {
            return $this->render('IndicateurBundle:IndicateurAdmin:group-edit.html.twig',array('group'=>$indicateurGroup));
        }
        elseif($action == 1)
        {
            $libelle = $post->get('libelle');
            $insertion = false;
            if($indicateurGroup == null)
            {
                $insertion = true;
                $indicateurGroup = new IndicateurGroup();
                $client = Boost::deboost($post->get('client'),$this);
                $dossier = Boost::deboost($post->get('dossier'),$this);
                if(is_bool($client) || is_bool($dossier)) return new Response('security');
                $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
                if($dossier == null) $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
                else $client = null;
                $indicateurGroup->setClient($client);
                $indicateurGroup->setDossier($dossier);
            }
            $indicateurGroup->setLibelle($libelle);

            if($insertion) $em->persist($indicateurGroup);
            try
            {
                $em->flush();
                return new Response(1);
            }
            catch (UniqueConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }
        elseif($action == 2)
        {
            $em->remove($indicateurGroup);
            try
            {
                $em->flush();
                return new Response(1);
            }
            catch (ForeignKeyConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function reloadGroupAction(Request $request)
    {
        $post = $request->request;
        $indicateurGroup = Boost::deboost($post->get('group'),$this);
        $client = Boost::deboost($post->get('client'),$this);
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($indicateurGroup) || is_bool($client) || is_bool($dossier)) return new Response('security');
        $indicateurGroup = $this->getDoctrine()->getRepository('AppBundle:IndicateurGroup')->getById($indicateurGroup);
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        $index_group = intval($post->get('index'));
        $packs = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->getPacksInGroups($indicateurGroup,$client,$dossier);
        return $this->render('IndicateurBundle:IndicateurAdmin:pack.html.twig',array('packs'=>$packs,'index_group'=>$index_group,'client'=>$client,'dossier'=>$dossier));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function dupliquerAction(Request $request)
    {
        $post = $request->request;
        $ind = Boost::deboost($post->get('ind'),$this);
        $client = Boost::deboost($post->get('client'),$this);
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($ind) || is_bool($client) || is_bool($dossier)) return new Response('security');
        $type = intval($post->get('type'));

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        if($dossier == null) $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
        else $client = null;

        $reponse = 0;
        if($type == 1)
        {
            $ind = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->getById($ind);
            $reponse = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->dupliquer($ind,$client,$dossier);
        }
        elseif ($type == 2)
        {
            $ind = $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($ind);
            $reponse = $this->getDoctrine()->getRepository('AppBundle:Indicateur')->dupliquer($ind,$client,$dossier);
        }
        return new Response($reponse);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function changeTdToGrapheAction(Request $request)
    {
        $post = $request->request;
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateur)) return new Response('security');

        $indicateur = $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($indicateur);
        $val = intval($post->get('val'));
        $row = intval($post->get('row'));

        $reponse = $this->getDoctrine()->getRepository('AppBundle:IndicateurFormatCol')
            ->changeTdToGraphe($indicateur,$row,$val);

        return new Response($reponse);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function validerIndicateurAction(Request $request)
    {
        $post = $request->request;
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateur)) return new Response('security');

        $isEtat = (intval($post->get('is_etat')) == 1);
        $indicateur = ($isEtat) ?
            $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getById($indicateur) :
            $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($indicateur);
        $status = intval($post->get('status'));
        $em = $this->getDoctrine()->getEntityManager();
        $indicateur->setValider($status);
        try
        {
            $em->flush();
            return new Response(1);
        }
        catch (\Exception $exception)
        {
            return new Response(0);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function validerIndicateurPackAction(Request $request)
    {
        $post = $request->request;
        $pack = Boost::deboost($post->get('pack'),$this);
        if(is_bool($pack)) return new Response('security');
        $pack = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->find($pack);

        $status = intval($post->get('status'));
        $em = $this->getDoctrine()->getEntityManager();

        $pack->setValider($status);
        try
        {
            $em->flush();
            return new Response(1);
        }
        catch (\Exception $exception)
        {
            return new Response(0);
        }
    }

    public function exercicesClosedsAction(Request $request)
    {

    }
}