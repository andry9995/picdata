<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 20/09/2016
 * Time: 15:46
 */

namespace IndicateurBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\IndPack;
use AppBundle\Entity\IndPackItem;
use AppBundle\Entity\IndPackItemSpecDossier;
use AppBundle\Entity\IndPackSpecDossier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPackController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('IndicateurBundle:AdminPack:index.html.twig',array('adminGranted'=>$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')));
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
        $packs = $this->getDoctrine()->getRepository('AppBundle:IndPack')->getListe($dossier);
        return $this->render('IndicateurBundle:AdminPack:pack.html.twig',array('packs'=>$packs,'dossier'=>$dossier));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editPackAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));
        $indPack = Boost::deboost($post->get('id_pack'),$this);
        if(is_bool($indPack)) return new Response('security');
        $indPack = $this->getDoctrine()->getRepository('AppBundle:IndPack')->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id',$indPack)
            ->getQuery()
            ->getOneOrNullResult();
        //show pop-up edition
        if($action == 0) return $this->render('IndicateurBundle:AdminPack:pack-edit.html.twig',array('indPack'=>$indPack));

        $em = $this->getDoctrine()->getEntityManager();

        //delete
        if($action == 2)
        {
            $em->remove($indPack);
            $em->flush();
            return new Response(1);
        }

        //add or update
        if($action == 1)
        {
            $libelle = $post->get('libelle');
            //add
            if($indPack == null)
            {
                $dossier = Boost::deboost($post->get('dossier'),$this);
                if(is_bool($dossier)) return new Response('security');

                $indPack = new IndPack();
                $indPack->setLibelle($libelle);
                $indPack->setDossier($this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier));
                $em->persist($indPack);
            }
            else $indPack->setLibelle($libelle);

            $em->flush();
            return new Response($indPack->getId());
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editPackItemAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));
        $indicateurItems = $this->getDoctrine()->getRepository('AppBundle:IndIndicateurItem')->getAll();
        $indPackItem = Boost::deboost($post->get('id_pack_item'),$this);
        if(is_bool($indPackItem)) return new Response('security');
        $indPackItem = $this->getDoctrine()->getRepository('AppBundle:IndPackItem')->createQueryBuilder('pi')
            ->where('pi.id = :id')
            ->setParameter('id',$indPackItem)
            ->getQuery()
            ->getOneOrNullResult();
        $em = $this->getDoctrine()->getEntityManager();

        //show edit
        if($action == 0) return $this->render('IndicateurBundle:AdminPack:pack-item-edit.html.twig',array('indicateurItems'=>$indicateurItems , 'indPackItem'=>$indPackItem));
        //remove
        elseif ($action == 2) $em->remove($indPackItem);
        //add , edit
        elseif($action == 1)
        {
            //add
            $indIndicateurItem = Boost::deboost($post->get('indicateur_item'),$this);
            if(is_bool($indIndicateurItem)) return new Response('security');
            $indIndicateurItem = $this->getDoctrine()->getRepository('AppBundle:IndIndicateurItem')
                ->createQueryBuilder('ii')
                ->where('ii.id = :id')
                ->setParameter('id',$indIndicateurItem)
                ->getQuery()
                ->getOneOrNullResult();
            if($indPackItem == null)
            {
                $dossier = Boost::deboost($post->get('dossier'),$this);
                $indPack = Boost::deboost($post->get('id_pack'),$this);
                if(is_bool($dossier) || is_bool($indPack)) return new Response('security');
                $indPack = $this->getDoctrine()->getRepository('AppBundle:IndPack')
                    ->createQueryBuilder('pi')
                    ->where('pi.id = :id')
                    ->setParameter('id',$indPack)
                    ->getQuery()
                    ->getOneOrNullResult();
                $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
                $indPackItem = new IndPackItem();
                $indPackItem->setDossier($dossier);
                $indPackItem->setIndIndicateurItem($indIndicateurItem);
                $indPackItem->setIndPack($indPack);
                $em->persist($indPackItem);
            }
        }

        $em->flush();
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function packCheckAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $indPack = Boost::deboost($post->get('id_pack'),$this);
        $pack = intval($post->get('pack'));

        if(is_bool($dossier) || is_bool($indPack)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);

        $em = $this->getDoctrine()->getEntityManager();

        $indPack = ($pack == 0) ?
            $this->getDoctrine()->getRepository('AppBundle:IndPack')->getById($indPack) :
            $this->getDoctrine()->getRepository('AppBundle:IndPackItem')->getById($indPack);

        if($dossier == null || $indPack == null) return new Response('error');

        $indPackSpecDossiers = $this->getDoctrine()
            ->getRepository('AppBundle:'.(($pack == 0) ? 'IndPackSpecDossier' : 'IndPackItemSpecDossier'))
            ->createQueryBuilder('sp')
            ->where('sp.dossier = :dossier')
            ->setParameter('dossier',$dossier);

        if($pack == 0) $indPackSpecDossiers = $indPackSpecDossiers->andWhere('sp.indPack = :pack');
        else $indPackSpecDossiers = $indPackSpecDossiers->andWhere('sp.indPackItem = :pack');

        $indPackSpecDossiers = $indPackSpecDossiers
            ->setParameter('pack',$indPack)
            ->getQuery()
            ->getResult();

        if(count($indPackSpecDossiers) > 0)
            foreach ($indPackSpecDossiers as $indPackSpecDossier) $em->remove($indPackSpecDossier);
        else
        {
            if($pack == 0)
            {
                $indPackSpecDossier = new IndPackSpecDossier();
                $indPackSpecDossier->setIndPack($indPack);
            }
            else
            {
                $indPackSpecDossier = new IndPackItemSpecDossier();
                $indPackSpecDossier->setIndPackItem($indPack);
            }
            $indPackSpecDossier->setDossier($dossier);
            $em->persist($indPackSpecDossier);
        }

        $em->flush();
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function packItemsAction(Request $request)
    {
        $post = $request->request;
        $indPack = Boost::deboost($post->get('id_pack'),$this);
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($indPack) || is_bool($dossier)) return new Response('security');
        $indPack = $this->getDoctrine()->getRepository('AppBundle:IndPack')->getById($indPack);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        $indPack = $this->getDoctrine()->getRepository('AppBundle:IndPack')->getPackItems($indPack,$dossier);
        return $this->render('IndicateurBundle:AdminPack:pack-items.html.twig',array('pack'=>$indPack,'dossier'=>$dossier));
    }
}