<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 13/06/2016
 * Time: 13:34
 */
namespace RubriqueBundle\Controller;

use AppBundle\Entity\Pcg;
use AppBundle\Entity\PcgsRubrique;
use AppBundle\Entity\Rubrique;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Controller\Boost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use stdClass;


class AdminController extends Controller
{
    /**
     * @param $admin_dossier
     * @return Response
     */
    public function indexAction($admin_dossier)
    {
        $pcgs =
        $rubriques =
        $superRubriques =
        $hyperRubriques =
        $rubriquesFormules =
        $superRubriquesFormules =
        $hyperRubriquesFormules = array();

        /*$rubs = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques(10);
        foreach ($rubs as $rub)
        {
            if($rub->getType() == 0 && $rub->getFormule() == '') $rubriques[] = $rub;
            elseif($rub->getType() == 1 && $rub->getFormule() == '') $superRubriques[] = $rub;
            elseif($rub->getType() == 2 && $rub->getFormule() == '') $hyperRubriques[] = $rub;
            elseif($rub->getType() == 0 && $rub->getFormule() != '') $rubriquesFormules[] = $rub;
            elseif($rub->getType() == 1 && $rub->getFormule() != '') $superRubriquesFormules[] = $rub;
            elseif($rub->getType() == 2 && $rub->getFormule() != '') $hyperRubriquesFormules[] = $rub;
        }
        $pcgs = $this->getDoctrine()->getRepository('AppBundle:PcgRubrique')->getPcgsRubriques();*/

        return $this->render('RubriqueBundle:Admin:index.html.twig', array('admin_dossier'=>$admin_dossier,
            'rubriques'=>$rubriques, 'superRubriques'=>$superRubriques, 'hyperRubriques'=>$hyperRubriques,
            'rubriquesFormules'=>$rubriquesFormules, 'superRubriquesFormules'=>$superRubriquesFormules, 'hyperRubriquesFormules'=>$hyperRubriquesFormules,
            'pcgs'=>$pcgs));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function tableRubriquesAction(Request $request)
    {
        $post = $request->request;
        $type = intval($post->get('type'));
        $rubriques = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques($type,2);
        $datas = [];
        $colMax = 25;

        foreach ($rubriques as $rubrique)
        {
            $donnees = [];
            //id rubrique
            $donnees['col_0'] = Boost::boost($rubrique->getId());
            //libelle
            $donnees['col_1'] = $rubrique->getLibelle();
            //supprimer
            $donnees['col_2'] = '';
            //montant
            $donnees['col_3'] = 0;

            $caracteres = $this->getDoctrine()->getRepository('AppBundle:PcgsRubrique')->getCaracteres();
            $pcgs = $this->getDoctrine()->getRepository('AppBundle:PcgsRubrique')->getPcgs($rubrique,false);
            foreach ($pcgs as $keyPcg => $pcg)
            {
                $caractere = '';

                if($pcg->getSolde() != 0) $caractere .= array_search($pcg->getSolde(), $caracteres);
                if($pcg->getTypeCompte() != 0) $caractere .= array_search($pcg->getTypeCompte() + 4, $caracteres);

                $key = $keyPcg + 4;
                $donnees['col_'.$key] = (($pcg->getNegation() == 1) ? '-' : '') . $pcg->getPcg()->getCompte() . $caractere;
            }
            $datas[] = $donnees;
        }

        $entetes = [];
        $models = [];
        for ($i = 0; $i < $colMax; $i++)
        {
            $text = $i - 3;
            $align = 'center';
            $classes = '';
            //id
            if($i == 0)
            {
                $text = 'ID';
                $align = 'left';
                $classes = 'js_id';
            }
            //rubrique
            elseif($i == 1)
            {
                $text = 'RUBRIQUE';
                $align = 'left';
                $classes = 'js_lib';
            }
            //supprimer
            elseif($i == 2)
            {
                $text = '';
                $align = 'center';
                $classes = 'js_rem pointer';
            }
            //montant
            elseif ($i == 3)
            {
                $text = 'MONTANT';
                $align = 'right';
            }

            $entetes[] = $text;
            $model = new stdClass();
            $model->name = 'col_'.$i;
            $model->class = $classes;
            $model->align = $align;
            $models[] = $model;
        }

        $reponse = new stdClass();
        $reponse->entetes = $entetes;
        $reponse->models = $models;
        $reponse->datas = $datas;
        return new JsonResponse($reponse);
    }

    public function tableEditRubriqueAction(Request $request)
    {
        $post = $request->request;
        $rubrique = Boost::deboost($post->get('rubrique'),$this);
        if(is_bool($rubrique)) return new Response('security');
        $modif = intval($post->get('modif'));
        $newVal = $post->get('new_val');
        $rubrique = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->find($rubrique);
        $action = intval($post->get('action'));
        $em = $this->getDoctrine()->getManager();

        //modification rubrique
        if($modif == 0)
        {
            //ajout modif
            if($action == 0)
            {
                //modif
                if($rubrique != null)
                {
                    $rubrique->setLibelle($newVal);
                }
                //ajout
                else
                {
                    $libelle = $post->get('libelle');
                    $type = intval($post->get('type'));
                    $rubrique = new Rubrique();
                    $rubrique->setLibelle($libelle);
                    $rubrique->setType($type);
                    $em->persist($rubrique);
                }
            }
            //suppresion
            elseif ($action == 1)
            {
                $em->remove($rubrique);
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
        //modification pcg
        else if($modif == 1)
        {
            $oldVal = $post->get('old_val');
            $pcgOld = abs(intval($oldVal));
            $pcgOld = $this->getDoctrine()->getRepository('AppBundle:Pcg')->getByCompte($pcgOld);
            $pcgNew = abs(intval($newVal));
            $pcgNew = $this->getDoctrine()->getRepository('AppBundle:Pcg')->getByCompte($pcgNew);
            $negation = (intval($newVal) < 0) ? 1 : 0;

            $caracteres = $this->getDoctrine()->getRepository('AppBundle:PcgsRubrique')->getCaracteres();
            $solde = 0;
            $typeCompte = 0;
            $chars = str_split($newVal);
            foreach ($chars as $char)
            {
                if(array_key_exists($char,$caracteres))
                {
                    $carVal = $caracteres[$char];
                    if($carVal < 5) $solde = $carVal;
                    else $typeCompte = $carVal - 4;
                }
            }

            //modifie ancien
            if($pcgOld)
            {
                if($pcgNew == null && $newVal != '') return new Response(2);
                $pcgsRubrique = $this->getDoctrine()->getRepository('AppBundle:PcgsRubrique')->getByRubriqueCompte($rubrique,$pcgOld);
                if($newVal == '') $em->remove($pcgsRubrique);
                else
                {
                    $pcgsRubrique->setPcg($pcgNew);
                    $pcgsRubrique->setNegation($negation);
                    $pcgsRubrique->setSolde($solde);
                    $pcgsRubrique->setTypeCompte($typeCompte);
                }
            }
            //ajout nouveau
            else
            {
                if($pcgNew == null) return new Response(2);
                $pcgsRubrique = new PcgsRubrique();
                $pcgsRubrique->setRubrique($rubrique);
                $pcgsRubrique->setPcg($pcgNew);
                $pcgsRubrique->setNegation($negation);
                $pcgsRubrique->setSolde($solde);
                $pcgsRubrique->setTypeCompte($typeCompte);
                $em->persist($pcgsRubrique);
            }
        }

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

    public function tableShowCalculesAction(Request $request)
    {
        $post = $request->request;
        $type = intval($post->get('type'));
        $height = floatval($post->get('height'));
        $rubriques = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques($type,1);
        return $this->render('RubriqueBundle:Admin:rubriques-calcules.html.twig',array('rubriques'=>$rubriques,'height'=>$height));
    }

    public function tableRubiquesFillesAction(Request $request)
    {
        $post = $request->request;
        $rubrique = Boost::deboost($post->get('rubrique'),$this);
        if(is_bool($rubrique)) return new Response('security');
        $rubrique = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getById($rubrique);
        $rubriquesFilles = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getFillesObject($rubrique);
        return $this->render('RubriqueBundle:Admin:formule.html.twig',array('rubrique'=>$rubrique,'rubriquesFilles'=>$rubriquesFilles));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function compteIndicateurAction(Request $request)
    {
        $post = $request->request;
        $admin_dossier = Boost::deboost($post->get('admin_dossier'),$this);
        if(is_bool($admin_dossier)) return new Response('security');

        if($admin_dossier == 0)
        {
            return new Response(Boost::serialize($this->getDoctrine()->getRepository('AppBundle:PcgRubrique')->getPcgRubrique()));
        }
        else
        {

        }
        return new Response($admin_dossier);
    }

    /**
     *
     * @ParamConverter("pcg", class="AppBundle:Pcg")
     *
     * @param Request $request
     * @return Response
     */
    public function changeRubriqueAction(Request $request,Pcg $pcg)
    {
        $post = $request->request;
        $rubrique = $post->get('rubrique');
        $type = $post->get('type');
        return new Response($this->getDoctrine()->getRepository('AppBundle:PcgRubrique')->setRubrique($pcg,$rubrique,$type));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function rubriquesAction(Request $request)
    {
        $post = $request->request;
        $type = $post->get('type');
        $niveau = $post->has('niveau') ? $post->get('niveau') : 0;
        return new Response(Boost::serialize($this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques($type,$niveau)));
    }

    /**
     * @return Response
     */
    public function rubriquesAdminAction()
    {
        return $this->render('RubriqueBundle:Admin:rubriques_admin.html.twig', array());
    }

    /**
     * @ParamConverter("pcg", class="AppBundle:Pcg")
     * @param Request $request
     * @param Pcg $pcg
     * @return Response
     */
    public function pcgRubriqueEditAction(Request $request,Pcg $pcg)
    {
        $post = $request->request;
        $libelle = $post->get('libelle');
        $type = intval($post->get('type'));
        $rubrique = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriqueByLibelleType($libelle,$type);
        return new Response($this->getDoctrine()->getRepository('AppBundle:PcgRubrique')->setRubriqueToLikePcg($pcg,$rubrique,$type));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function rubriquesEditAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        $rubrique = Boost::deboost($post->get('id'),$this);
        if(is_bool($rubrique)) return new Response('security');

        $libelle = $post->get('libelle');
        $solde = intval($post->get('solde'));
        $type_solde = intval($post->get('type_compte'));
        $em = $this->getDoctrine()->getManager();

        if($action == 0)
        {
            $rubrique = new Rubrique();
            $rubrique->setLibelle($libelle);
            $rubrique->setType(intval($post->get('type')));
            $rubrique->setSolde($solde);
            $rubrique->setTypeCompte($type_solde);
            $em->persist($rubrique);
        }
        else
        {
            $rubrique = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->createQueryBuilder('r')
                ->where('r.id = :id')
                ->setParameter('id',$rubrique)
                ->getQuery()
                ->getOneOrNullResult();

            if($action == 1)
            {
                $rubrique->setLibelle($libelle);
                $rubrique->setSolde($solde);
                $rubrique->setTypeCompte($type_solde);
            }
            else $em->remove($rubrique);
        }

        try
        {
            $em->flush();
        }
        catch (UniqueConstraintViolationException $ex)
        {
            return new Response(0);
        }

        if($action == 0) return $this->render('RubriqueBundle:Admin:new-tr.html.twig',array('rubrique'=>$rubrique));
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function rubriqueValidateAction(Request $request)
    {
        $post = $request->request;
        $pcg = $post->get('id');
        $pcg = $this->getDoctrine()->getRepository('AppBundle:Pcg')->getById($pcg);

        $rubrique = $post->get('rubrique');
        $superRubrique = $post->get('superRubrique');
        $hyperRubrique = $post->get('hyperRubrique');

        $rubriques = $this->getDoctrine()->getRepository('AppBundle:Rubrique')
            ->createQueryBuilder('r')
            ->where('r.id = :id1 OR r.id = :id2 OR r.id = :id3')
            ->setParameter('id1',$rubrique)
            ->setParameter('id2',$superRubrique)
            ->setParameter('id3',$hyperRubrique)
            ->getQuery()
            ->getResult();

        $rubrique = null;
        $superRubrique = null;
        $hyperRubrique = null;

        foreach ($rubriques as $r_item)
        {
            if($r_item->getType() == 0) $rubrique = $r_item;
            elseif($r_item->getType() == 1) $superRubrique = $r_item;
            elseif($r_item->getType() == 2) $hyperRubrique = $r_item;
        }

        return new Response($this->getDoctrine()
            ->getRepository('AppBundle:PcgRubrique')
            ->setRubrique2($pcg,$rubrique,$superRubrique,$hyperRubrique));
    }

    /**
     * @return Response
     */
    public function testResultAction()
    {
        $pcg = $this->getDoctrine()->getRepository('AppBundle:Pcg')->getById(3);
        $rubrique = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getById(1);

        $pcgRubriqueSets = $this->getDoctrine()->getRepository('AppBundle:PcgRubrique')->createQueryBuilder('pr')
            ->leftJoin('pr.pcg','p')
            ->leftJoin('pr.rubrique','r')
            ->where('p.compte LIKE :compte')
            ->setParameter('compte',$pcg->getCompte().'%')
            ->andWhere('r.type = :type')
            ->setParameter('type',$rubrique->getType())
            ->getQuery()
            ->getResult();

        $pcgsSets = array();
        foreach ($pcgRubriqueSets as $pcgRubriqueSet) $pcgsSets[] = $pcgRubriqueSet->getPcg();

        $pcgs = $this->getDoctrine()->getRepository('AppBundle:Pcg')
            ->createQueryBuilder('p')
            ->where('p.compte LIKE :compte')
            ->setParameter('compte',$pcg->getCompte().'%')
            ->andWhere('p NOT IN (:pcgsSets)')
            ->setParameter('pcgsSets',$pcgsSets)
            ->getQuery()
            ->getResult();

        return $this->render('RubriqueBundle:Admin:test.html.twig',array('test'=>$pcgs));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function rubriqueChangeAction(Request $request)
    {
        $post = $request->request;
        $pcg = Boost::deboost($post->get('pcg'),$this);
        if(is_bool($pcg)) return new Response('security');
        $pcg = $this->getDoctrine()->getRepository('AppBundle:Pcg')->getById($pcg);
        $type = $post->get('type');
        $rubriques = json_decode($post->get('rubriques'));
        return new Response($this->getDoctrine()->getRepository('AppBundle:PcgRubrique')->setRubPcg($pcg,$rubriques,$type));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editFormuleAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));
        $rubrique = Boost::deboost($post->get('id'),$this);
        if(is_bool($rubrique)) return new Response('security');
        $rubrique = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getById($rubrique);

        //show edit
        if($action == 0)
        {
            $rubriquesFilles = array();
            if($rubrique != null) $rubriquesFilles = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getFilles($rubrique);
            return $this->render('RubriqueBundle:Admin:formule-edit.html.twig',array('rubrique'=>$rubrique,'rubriquesFilles'=>$rubriquesFilles));
        }
        //add, remove ,edit formule
        else
        {
            $em = $this->getDoctrine()->getEntityManager();
            //add edit
            if($action == 1)
            {
                $formule = $post->get('formule');
                $type = intval($post->get('type'));
                $libelle = $post->get('libelle');
                $rubriquesFilles = implode(';',json_decode($post->get('rubriques_in_formules')));
                $isAdd = false;
                //add
                if($rubrique == null)
                {
                    $rubrique = new Rubrique();
                    $rubrique->setType($type);
                    $isAdd = true;
                }
                $rubrique->setFormule($formule);
                $rubrique->setLibelle($libelle);
                $rubrique->setRubriquesFilles($rubriquesFilles);

                //add
                if($isAdd) $em->persist($rubrique);

                try
                {
                    $em->flush();
                    return $this->render('RubriqueBundle:Admin:response-add.html.twig',array('rubriqueFormule'=>$rubrique));
                }
                catch (UniqueConstraintViolationException $ex)
                {
                    return new Response(0);
                }
            }
            //edit
            elseif($action == 2)
            {
                $em->remove($rubrique);
                $em->flush();
                return new Response(1);
            }
        }
    }
}