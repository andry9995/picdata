<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/07/2016
 * Time: 10:25
 */

namespace IndicateurBundle\Controller;

use AppBundle\Entity\IndIndicateur;
use AppBundle\Entity\IndIndicateurTypeGraphe;
use Doctrine\DBAL\Driver\Mysqli\MysqliException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\IndGroupIndicateur;
use AppBundle\Controller\Boost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class IndicateurController extends Controller
{
    /**
     *
     * @ParamConverter("group", class="AppBundle:IndGroupIndicateur")
     *
     * @param IndGroupIndicateur $group
     * @return Response
     */
    public function indicateursAction(IndGroupIndicateur $group, $index_group)
    {
        return $this->render('IndicateurBundle:Indicateur:indicateurs.html.twig',
            array('indicateurs'=>$this->getDoctrine()->getRepository('AppBundle:IndIndicateur')->getIndicateur($group) , 'index_group'=>$index_group));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editIndicateurAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        //affichage view edit
        if($action == 3 || $action == 6)
        {
            if($action == 6)
            {
                $id = Boost::deboost($post->get('id'),$this);
                if(is_bool($id)) return new Response('security');
                $indicateur = $this->getDoctrine()->getRepository('AppBundle:IndIndicateur')->createQueryBuilder('i')
                    ->where('i.id = :id')
                    ->setParameter('id',$id)
                    ->getQuery()
                    ->getOneOrNullResult();
            }
            else $indicateur = null;

            return $this->render('IndicateurBundle:Indicateur:indicateur-edit.html.twig',
                array('type_graphes' => $this->getDoctrine()->getRepository('AppBundle:IndIndicateurTypeGraphe')->getAllInIndicateur($indicateur),
                    'indicateur' => $indicateur));
        }

        //ajout suppresion
        if($action == 4 || $action == 5 || $action == 7)
        {
            $id_group = Boost::deboost($post->get('id_group'),$this);
            $id_indicateur = Boost::deboost($post->get('id_indicateur'),$this);
            if(is_bool($id_group) || is_bool($id_indicateur)) return new Response('security');

            $em = $this->getDoctrine()->getEntityManager();
            $libelle = $post->get('libelle');

            $objects = json_decode($post->get('objects'));

            if($action == 4)
            {
                $indGroupIndicateur = $this->getDoctrine()->getRepository('AppBundle:IndGroupIndicateur')
                    ->createQueryBuilder('ig')
                    ->where('ig.id = :id')
                    ->setParameter('id',$id_group)
                    ->getQuery()
                    ->getOneOrNullResult();

                $indIndicateur = new IndIndicateur();
                $indIndicateur->setLibelle($libelle);
                $indIndicateur->setIndGroupIndicateur($indGroupIndicateur);

                $em->persist($indIndicateur);

                try
                {
                    $em->flush();
                }
                catch (UniqueConstraintViolationException $violationException)
                {
                    return new Response(0);
                }

                //type graphe
                foreach ($objects as $object)
                {
                    if($object->status)
                    {
                        $id_type_graphe = Boost::deboost($object->idTypeGraphe,$this);
                        if(is_bool($id_type_graphe)) return new Response('security');
                        $id_type_graphe = intval($id_type_graphe);
                        $typeGraphe = $this->getDoctrine()->getRepository('AppBundle:TypeGraphe')
                            ->createQueryBuilder('tg')
                            ->where('tg.id = :id')
                            ->setParameter('id', $id_type_graphe)
                            ->getQuery()
                            ->getOneOrNullResult();

                        $indIndicateurTypeGraphe = new IndIndicateurTypeGraphe();
                        $indIndicateurTypeGraphe->setIndIndicateur($indIndicateur);
                        $indIndicateurTypeGraphe->setTypeGraphe($typeGraphe);
                        $em->persist($indIndicateurTypeGraphe);
                    }
                }

                $em->flush();
            }
            else
            {
                if($action == 5)
                {
                    $indicateur = $this->getDoctrine()->getRepository('AppBundle:IndIndicateur')->getById($id_indicateur);
                    $em->remove($indicateur);
                }
                else
                {
                    $indicateur = $this->getDoctrine()->getRepository('AppBundle:IndIndicateur')->createQueryBuilder('i')
                        ->where('i.id = :id')
                        ->setParameter('id',$id_indicateur)
                        ->getQuery()
                        ->getOneOrNullResult();
                    //modification libelle
                    $indicateur->setLibelle($libelle);

                    try
                    {
                        $em->flush();
                    }
                    catch (UniqueConstraintViolationException $violationException)
                    {
                        return new Response(0);
                    }

                    //modification type graphe
                    foreach ($objects as $object)
                    {
                        $id_type_graphe = Boost::deboost($object->idTypeGraphe,$this);
                        if(is_bool($id_type_graphe)) return new Response('security');
                        $id_type_graphe = intval($id_type_graphe);
                        $typeGraphe = $this->getDoctrine()->getRepository('AppBundle:TypeGraphe')
                            ->createQueryBuilder('tg')
                            ->where('tg.id = :id')
                            ->setParameter('id', $id_type_graphe)
                            ->getQuery()
                            ->getOneOrNullResult();

                        $indIndicateurTypeGraphe = $this->getDoctrine()->getRepository('AppBundle:IndIndicateurTypeGraphe')
                            ->createQueryBuilder('it')
                            ->where('it.indIndicateur = :indIndicateur')
                            ->setParameter('indIndicateur',$indicateur)
                            ->andWhere('it.typeGraphe = :typeGraphe')
                            ->setParameter('typeGraphe',$typeGraphe)
                            ->getQuery()
                            ->getOneOrNullResult();

                        if($object->status)
                        {
                            if($indIndicateurTypeGraphe == null)
                            {
                                $indIndicateurTypeGraphe = new IndIndicateurTypeGraphe();
                                $indIndicateurTypeGraphe->setIndIndicateur($indicateur);
                                $indIndicateurTypeGraphe->setTypeGraphe($typeGraphe);
                                $em->persist($indIndicateurTypeGraphe);
                            }
                        }
                        else
                        {
                            if($indIndicateurTypeGraphe != null)
                            {
                                $em->remove($indIndicateurTypeGraphe);
                            }
                        }
                    }
                }

                try
                {
                    $em->flush();
                }
                catch (ForeignKeyConstraintViolationException $violationException)
                {
                    return new Response(0);
                }
            }
            return new Response(1);
        }
    }

    /**
     * add row or column to indicateur table
     *
     * @param Request $request
     * @return Response
     */
    public function addCellAction(Request $request)
    {
        $post = $request->request;

        $id_indicateur = Boost::deboost($post->get('id_indicateur'),$this);
        if(is_bool($id_indicateur)) return new Response('security');

        $indicateur = $this->getDoctrine()->getRepository('AppBundle:IndIndicateur')
            ->createQueryBuilder('i')
            ->where('i.id = :id')
            ->setParameter('id',$id_indicateur)
            ->getQuery()
            ->getOneOrNullResult();
        if(intval($post->get('action')) == 0)
        {
            $indicateur->setRowNumber($indicateur->getRowNumber() + 1);
        }
        else
        {
            if($indicateur->getRowNumber() == 0) $indicateur->setRowNumber($indicateur->getRowNumber() + 1);
            $indicateur->setColNumber($indicateur->getColNumber() + 1);
        }
        $this->getDoctrine()->getEntityManager()->flush();

        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function groupItemsAjaxAction(Request $request)
    {
        $post = $request->request;

        $indGroupIndicateur = Boost::deboost($post->get('id_group'),$this);
        if(is_bool($indGroupIndicateur)) return new Response('security');

        $index = $post->get('index');
        $indGroupIndicateur = $this->getDoctrine()->getRepository('AppBundle:IndGroupIndicateur')
            ->getById($indGroupIndicateur);
        return $this->indicateursAction($indGroupIndicateur, $index);
    }
}