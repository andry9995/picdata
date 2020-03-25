<?php

namespace FacturationBundle\Controller;

use AppBundle\Entity\FactDomaine;
use AppBundle\Entity\FactIndice;
use AppBundle\Entity\FactModele;
use AppBundle\Entity\FactPrestationGenerale;
use AppBundle\Entity\FactUnite;
use Proxies\__CG__\AppBundle\Entity\FactRemiseVolume;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Repository\FactDomaineRepository;


class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('FacturationBundle:Admin:index.html.twig');
    }

    public function paramGenAction()
    {
        $em = $this->getDoctrine()->getManager();

        $listDomaines = $em->getRepository('AppBundle:FactDomaine')
            ->findAll();

        $listIndices = $em->getRepository('AppBundle:FactIndice')
            ->findAll();

        $listUnites = $em->getRepository('AppBundle:FactUnite')
            ->findAll();


        $listModele = $em->getRepository('AppBundle:FactModele')
            ->findAll();

        return $this->render('FacturationBundle:Admin:paramGen.html.twig',
            array('listDomaines'=>$listDomaines,
                'listIndices'=>$listIndices,
                'listUnites'=>$listUnites,
                'listModele'=>$listModele));
    }

    public function prestationAction()
    {
        return $this->render('FacturationBundle:Admin:prestation.html.twig');
    }

    public function tarificationAction()
    {
        return $this->render('FacturationBundle:Admin:tarification.html.twig');
    }



    public function domainesAction()
    {
        $domaines = $this->getDoctrine()->getRepository('AppBundle:FactDomaine')->findAll();
        return $this->render('FacturationBundle:Domaine:domaine.html.twig',array('domaines'=>$domaines));
    }

    public function editDomaineAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        if($action ==0)
        {
            return $this->render('FacturationBundle:Domaine:domaineEdit.html.twig',array('domaine'=>null));
        }

        elseif($action == 1)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $codeMax = $em->createQueryBuilder()
                ->select('MAX(e.code)')
                ->from('AppBundle:FactDomaine', 'e')
                ->getQuery()
                ->getSingleScalarResult();

            $domaine = new FactDomaine();
            $domaine->setLibelle($post->get('libelle'));
            $domaine->setCode($codeMax+1);

            $em->persist($domaine);
            try
            {
                $em->flush();
                return new Response($domaine->getId());
            }

            catch (UniqueConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }

    }

    public function indicesAction()
    {
        $indices = $this->getDoctrine()->getRepository('AppBundle:FactIndice')->findAll();
        return $this->render('FacturationBundle:Indice:indice.html.twig',array('indices'=>$indices));
    }

    public function editIndiceAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        if($action==0)
        {
            return $this->render('FacturationBundle:Indice:IndiceEdit.html.twig',array('indice'=>null));
        }
        elseif ($action==1)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $codeMax = $em->createQueryBuilder()
                ->select('MAX(m.code)')
                ->from('AppBundle:FactIndice', 'm')
                ->getQuery()
                ->getSingleScalarResult();

            $dat = new \DateTime($post->get('date'));

            $indice = new FactIndice();
            $indice->setIndexIndice(intval($post->get('index_indice')));
            $indice->setIndice(floatval($post->get('indice')));
            $indice->setPourcentage(floatval($post->get('pourcentage')));
            $indice->setDateIndice($dat);
            $indice->setCode($codeMax+1);

            $em->persist($indice);
            try
            {
                $em->flush();
                return new Response($indice->getId());
            }

            catch (UniqueConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }
    }

    public function modelesAction()
    {
        $modeles = $this->getDoctrine()->getRepository('AppBundle:FactModele')->findAll();
        return $this->render('FacturationBundle:Modele:modele.html.twig',array('modeles'=>$modeles));
    }

    public function editModAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        if($action ==0)
        {
            return $this->render('FacturationBundle:Modele:ModeleEdit.html.twig',array('modele'=>null));
        }

        elseif ($action==1)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $codeMax = $em->createQueryBuilder()
                ->select('MAX(m.code)')
                ->from('AppBundle:FactModele', 'm')
                ->getQuery()
                ->getSingleScalarResult();

            $modele = new FactModele();
            $modele->setLibelle($post->get('libelle'));
            $modele->setCode($codeMax+1);

            $em->persist($modele);
            try
            {
                $em->flush();
                return new Response($modele->getId());
            }

            catch (UniqueConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }
    }

    public function remisevsAction()
    {
        $remisevs = $this->getDoctrine()->getRepository('AppBundle:FactRemiseVolume')->findAll();
        return $this->render('FacturationBundle:Remise_Volume:remiseVolume.html.twig',array('remisevs'=>$remisevs));
    }

    public function editRemisevAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        if($action==0)
        {
            return $this->render('FacturationBundle:Remise_Volume:remiseVolumeEdit.html.twig',array('indice'=>null));
        }

        elseif($action ==1)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $codeMax = $em->createQueryBuilder()
                ->select('MAX(m.code)')
                ->from('AppBundle:FactRemiseVolume', 'm')
                ->getQuery()
                ->getSingleScalarResult();

            $remise = new FactRemiseVolume();
            $remise->setTranche1(intval($post->get('tranche1')));
            $remise->setTranche2(intval($post->get('tranche2')));
            $remise->setPourcentage(floatval($post->get('pourcentage')));
            $remise->setCode($codeMax+1);

            $em->persist($remise);
            try
            {
                $em->flush();
                return new Response($remise->getId());
            }

            catch (UniqueConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }
    }

    public function unitesAction()
    {
        $unites = $this->getDoctrine()->getRepository('AppBundle:FactUnite')->findAll();
        return $this->render('FacturationBundle:Unite:unite.html.twig',array('unites'=>$unites));
    }

    public function editUniteAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        if($action == 0)
        {
            return $this->render('FacturationBundle:Unite:uniteEdit.html.twig',array('unite'=>null));
        }

        elseif ($action ==1)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $codeMax = $em->createQueryBuilder()
                ->select('MAX(u.code)')
                ->from('AppBundle:FactUnite', 'u')
                ->getQuery()
                ->getSingleScalarResult();

            $unite = new FactUnite();
            $unite->setLibelle($post->get('libelle'));
            $unite->setCode($codeMax+1);

            $em->persist($unite);
            try
            {
                $em->flush();
                return new Response($unite->getId());
            }

            catch (UniqueConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }
    }


    public function prestGenAction(Request $request)
    {
        $domaines = $this->getDoctrine()->getRepository('AppBundle:FactDomaine')->getListe();
        $unites = $this->getDoctrine()->getRepository('AppBundle:FactUnite')->findAll();
        $typeCalculs = $this->getDoctrine()->getRepository('AppBundle:FactTypeCalcul')->findAll();
        return $this->render('FacturationBundle:Prestation_Generale:prestationGen.html.twig',
            array('domaines'=>$domaines,
                'unites'=>$unites,
                'typeCalculs'=>$typeCalculs));
    }

    public function editPrestGenAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));

        $domaines = $this->getDoctrine()->getRepository('AppBundle:FactDomaine')->findAll();
        $unites = $this->getDoctrine()->getRepository('AppBundle:FactUnite')->findAll();
        $typecalculs = $this->getDoctrine()->getRepository('AppBundle:FactTypeCalcul')->findAll();

        if($action ==0)
        {
            return $this->render('FacturationBundle:Prestation_Generale:prestationgenEdit.html.twig',
                array('domaines'=>$domaines,
                'unites'=>$unites,
                'typecalculs'=>$typecalculs));
        }
        elseif($action == 1)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $domaine = $this->getDoctrine()->getRepository('AppBundle:FactDomaine')->find(intval($post->get('domaine')));
            $unite = $this->getDoctrine()->getRepository('AppBundle:FactUnite')->find(intval($post->get('unite')));
            $typecalcul = $this->getDoctrine()->getRepository('AppBundle:FactTypeCalcul')->find(intval($post->get('typecalcul')));

            $prestgen = new FactPrestationGenerale();
            $prestgen->setLibelle($post->get('libelle'));
            $prestgen->setCode(intval($post->get('code')));
            $prestgen->setFactDomaine($domaine);
            $prestgen->setFactUnite($unite);
            $prestgen->setFactTypeCalcul($typecalcul);
            $prestgen->setCalcIndice(boolval($post->get('calcIndice')));

            $em->persist($prestgen);
            try
            {
                $em->flush();
                return new Response($prestgen->getId());
            }

            catch (UniqueConstraintViolationException $ex)
            {
                return new Response(0);
            }
        }
    }








}