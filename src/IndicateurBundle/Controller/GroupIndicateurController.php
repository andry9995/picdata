<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/07/2016
 * Time: 10:01
 */

namespace IndicateurBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\IndGroupIndicateur;
use AppBundle\Controller\Boost;

class GroupIndicateurController extends Controller
{
    /**
     * get groupe indicateur
     *
     * @return Response
     */
    public function groupIndicateursAction()
    {
        return $this->render('IndicateurBundle:GroupIndicateur:group_indicateurs.html.twig',
            array('indicateurs'=>$this->getDoctrine()->getRepository('AppBundle:IndGroupIndicateur')->getAllGroupes())
        );
    }

    /**
     * edit group indicateur
     *
     * @param Request $request
     * @return Response
     */
    public function editGroupAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        $em = $this->getDoctrine()->getManager();

        if($action == 0)
        {
            $groupInd = new IndGroupIndicateur();
            $groupInd->setLibelle($post->get('libelle'));
            $em->persist($groupInd);
            try
            {
                $em->flush();
                return new Response(1);
            }
            catch (UniqueConstraintViolationException $violationException)
            {
                return new Response(0);
            }
        }
        elseif($action == 1)
        {
            $id = Boost::deboost($post->get('id'),$this);
            if(is_bool($id)) return new Response('security');

            $groupInd = $this->getDoctrine()->getRepository('AppBundle:IndGroupIndicateur')->createQueryBuilder('gi')
                ->where('gi.id = :id')->setParameter('id',$id)->getQuery()->getOneOrNullResult();
            if($groupInd != null)
                $groupInd->setLibelle($post->get('libelle'));
            try
            {
                $em->flush();
                return new Response(1);
            }
            catch(UniqueConstraintViolationException $e)
            {
                return new Response(0);
            }
        }
        else
        {
            try
            {
                $id = Boost::deboost($post->get('id'),$this);
                if(is_bool($id)) return new Response('security');
                $groupInd = $this->getDoctrine()->getRepository('AppBundle:IndGroupIndicateur')->getById($id);
                if($groupInd != null)
                {
                    $em->remove($groupInd);
                }
                $em->flush();
                return new Response(1);
            }
            catch(\Exception $e)
            {
                return new Response(0);
            }
        }
    }
}