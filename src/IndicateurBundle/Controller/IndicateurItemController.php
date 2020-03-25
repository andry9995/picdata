<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/07/2016
 * Time: 10:32
 */

namespace IndicateurBundle\Controller;

use AppBundle\Entity\IndFormule;
use AppBundle\Entity\IndIndicateurItem;
use AppBundle\Entity\IndOperande;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\IndIndicateur;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;

class IndicateurItemController extends Controller
{
    /**
     * @ParamConverter("indicateur", class="AppBundle:IndIndicateur")
     *
     * @param IndIndicateur $indicateur
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indicateurItemsAction(IndIndicateur $indicateur)
    {
        return $this->render('IndicateurBundle:IndicateurItem:indicateur-item.html.twig',
            array('indicateur_items'=>$this->getDoctrine()->getRepository('AppBundle:IndIndicateurItem')->getIndicateurItems($indicateur),
                    'indicateur'=>$indicateur));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indicateurItemsAjaxAction(Request $request)
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
        return $this->indicateurItemsAction($indicateur);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function indicateurItemEditAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        $id_indicateur = Boost::deboost($post->get('id_indicateur'),$this);
        $id_indicateur_item = Boost::deboost($post->get('id_indicateur_item'),$this);
        if(is_bool($id_indicateur) || is_bool($id_indicateur_item)) return new Response('security');

        $id_indicateur = intval($id_indicateur);
        $id_indicateur_item = intval($id_indicateur_item);

        if(intval($id_indicateur) != 0)
        {
            $indicateur = $this->getDoctrine()->getRepository('AppBundle:IndIndicateur')
                ->createQueryBuilder('i')
                ->where('i.id = :id_indicateur')
                ->setParameter('id_indicateur', $id_indicateur)
                ->getQuery()
                ->getOneOrNullResult();
            $indicateurItem = null;
            $indFormule = null;
            $operandes = null;
        }
        else
        {
            $indicateur = null;
            $indicateurItem = $this->getDoctrine()->getRepository('AppBundle:IndIndicateurItem')
                ->createQueryBuilder('ii')
                ->where('ii.id = :id_indicateur_item')
                ->setParameter('id_indicateur_item',$id_indicateur_item)
                ->getQuery()
                ->getOneOrNullResult();
            $indFormule = $this->getDoctrine()->getRepository('AppBundle:IndFormule')->getFormules($indicateurItem);
            $operandes = $this->getDoctrine()->getRepository('AppBundle:IndOperande')->getOperande($indFormule);
        }

        $em = $this->getDoctrine()->getEntityManager();

        if($action == 0)
        {
            return $this->render('IndicateurBundle:IndicateurItem:indicateur-item-edit.html.twig',
                array('indicateur'=>$indicateur, 'indicateur_item'=>$indicateurItem, 'indFormule'=>$indFormule,
                        'operandes'=>$operandes));
        }
        else if($action == 1)
        {
            $operandes = json_decode($post->get('operandes'));

            $libelle = strtoupper($post->get('libelle'));
            $unite = trim($post->get('unite'));
            $typeRubrique = intval($post->get('type_rubrique'));
            $typeOperateur = intval($post->get('type_operateur'));
            if($indicateurItem == null)
            {
                $indicateurItem = new IndIndicateurItem();
                $indicateurItem->setIndIndicateur($indicateur);
                $indicateurItem->setLibelle($libelle);
                $indicateurItem->setRow($post->get('row'));
                $indicateurItem->setCol($post->get('col'));
                $indicateurItem->setTypeRubrique($typeRubrique);
                $indicateurItem->setTypeOperation($typeOperateur);
                $indicateurItem->setUnite($unite);
                $em->persist($indicateurItem);
            }
            else
            {
                $indicateurItem->setLibelle($libelle);
                $indicateurItem->setTypeRubrique($typeRubrique);
                $indicateurItem->setTypeOperation($typeOperateur);
                $indicateurItem->setUnite($unite);
            }
            try
            {
                $em->flush();
            }
            catch (UniqueConstraintViolationException $violationException)
            {
                return new Response(-1);
            }

            $result_id = Boost::boost( $indicateurItem->getId());

            //formule
            $indFormule = $this->getDoctrine()->getRepository('AppBundle:IndFormule')->createQueryBuilder('f')
                ->where('f.indIndicateurItem = :indicateurItem')
                ->setParameter('indicateurItem',$indicateurItem)
                ->getQuery()
                ->getOneOrNullResult();
            $libelle_formule = strtoupper($post->get('libelle_formule'));
            if($indFormule == null) $indFormule = new IndFormule();
            $indFormule->setLibelle($libelle_formule);
            $indFormule->setFormule($post->get('formule'));
            $indFormule->setIndIndicateurItem($indicateurItem);
            $em->persist($indFormule);
            $em->flush();

            //delete old operande
            $old_operandes = $this->getDoctrine()->getRepository('AppBundle:IndOperande')
                ->createQueryBuilder('o')
                ->where('o.indFormuleInt = :indFormule')
                ->setParameter('indFormule',$indFormule)
                ->getQuery()
                ->getResult();
            foreach ($old_operandes as $old_operande)
            {
                $em->remove($old_operande);
            }
            $em->flush();

            //insert new operandes
            $rang = 0;
            foreach ($operandes as $operande)
            {
                //rubrique
                $rubrique_id = Boost::deboost($operande->id,$this);
                if(is_bool($rubrique_id)) return new Response('security');
                $rubrique = $this->getDoctrine()->getRepository('AppBundle:Rubrique')
                    ->createQueryBuilder('r')
                    ->where('r.id = :id')
                    ->setParameter('id',$rubrique_id)
                    ->getQuery()
                    ->getOneOrNullResult();

                $rang++;
                $indOperande = new IndOperande();
                $indOperande->setRang($rang);
                $indOperande->setIndFormuleInt($indFormule);
                $indOperande->setRubrique($rubrique);
                $indOperande->setVariationN(intval($operande->variation));

                $em->persist($indOperande);
            }
            $em->flush();
            return new Response($result_id);
        }
        else if($action == 5)
        {
            $indicateurItem = $this->getDoctrine()->getRepository('AppBundle:IndIndicateurItem')->getById($id_indicateur_item);
            $em->remove($indicateurItem);
            try
            {
                $em->flush();
                return new Response(1);
            }
            catch (ForeignKeyConstraintViolationException $violationException)
            {
                return new Response(0);
            }
        }
    }
}