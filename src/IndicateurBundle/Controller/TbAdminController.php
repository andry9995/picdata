<?php

namespace IndicateurBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\IndicateurTb;
use AppBundle\Entity\IndicateurTbDecision;
use AppBundle\Entity\IndicateurTbDomaine;
use AppBundle\Entity\IndicateurTbInfoPerdos;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use JsonSchema\Constraints\ObjectConstraint;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Exception\Exception;

class TbAdminController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('IndicateurBundle:TbAdmin:index.html.twig',[
            'affichage' => 0
        ]);
    }

    /**
     * @return Response
     */
    public function indexScorinAction()
    {
        return $this->render('IndicateurBundle:TbAdmin:index.html.twig',[
            'affichage' => 1
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indicateurTbDomaineAction(Request $request)
    {
        $affichage = intval($request->request->get('affichage'));

        /** @var IndicateurTbDomaine[] $indicateurTbDomaines */
        $indicateurTbDomaines = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbDomaine')
            ->getAll($affichage);

        $indicateurTbInfoPerdos = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbInfoPerdos')
            ->all();

        return $this->render('IndicateurBundle:TbAdmin:indicateurs-domaine.html.twig',[
            'indicateurTbDomaines' => $indicateurTbDomaines,
            'affichage' => $affichage,
            'indicateurTbInfoPerdos' => $indicateurTbInfoPerdos
        ]);
    }

    public function editIndicateurDomaineAction(Request $request)
    {
        $action = intval($request->request->get('action'));
        $indicateurTbDomaine = Boost::deboost($request->request->get('indicateur_tb_domaine'),$this);
        if(is_bool($indicateurTbDomaine)) return new Response('security');
        $indicateurTbDomaine = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbDomaine')
            ->find($indicateurTbDomaine);

        $status = 'success';
        $em = $this->getDoctrine()->getManager();
        if ($action == 0)
        {
            $nom = trim($request->request->get('nom'));
            $indicateurTbDomaine = new IndicateurTbDomaine();
            $indicateurTbDomaine
                ->setNom($nom)
                ->setAffichage(intval($request->request->get('affichage')));

            $em->persist($indicateurTbDomaine);
            try
            {
                $em->flush();
                $titre = 'Succès';
                $message = 'Domaine ajouté avec succès';
            }
            catch (ForeignKeyConstraintViolationException $ex)
            {
                $status = 'error';
                $titre = 'Erreur';
                $message = 'Ce nom éxiste déja';
            }
        }
        else
        {
            if ($indicateurTbDomaine)
            {
                if ($action == 1)
                {
                    $nom = trim($request->request->get('nom'));
                    $indicateurTbDomaine->setNom($nom);

                    try
                    {
                        $em->flush();
                        $titre = 'Succès';
                        $message = 'Modification enregistrée avec succès';
                    }
                    catch (ForeignKeyConstraintViolationException $ex)
                    {
                        $status = 'error';
                        $titre = 'Erreur';
                        $message = 'Ce nom éxiste déja';
                    }
                }
                else
                {
                    $em->remove($indicateurTbDomaine);
                    try
                    {
                        $em->flush();
                        $titre = 'Succès';
                        $message = 'Modification enregistrée avec succès';
                    }
                    catch (ForeignKeyConstraintViolationException $ex)
                    {
                        $status = 'error';
                        $titre = 'Erreur';
                        $message = 'Domaine Non Vide';
                    }
                }
            }
            else
            {
                $titre = 'Erreur';
                $message = 'Domaine Non éxistant';
            }
        }

        return new JsonResponse((object)[
            's' => $status,
            't' => $titre,
            'm' => $message
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indicateursAction(Request $request)
    {
        $affichage = intval($request->request->get('affichage'));
        $indicateurTbDomaine = Boost::deboost($request->request->get('indicateur_tb_domaine'),$this);
        if(is_bool($indicateurTbDomaine)) return new Response('security');
        $indicateurTbDomaine = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbDomaine')
            ->find($indicateurTbDomaine);

        $indicateursTbs = $this->getDoctrine()->getRepository('AppBundle:IndicateurTb')->getIndicateurTb($affichage,$indicateurTbDomaine);
        $indicateurTbInfoPerdos = [];

        return $this->render('IndicateurBundle:TbAdmin:indicateurs.html.twig',[
            'indicateursTbs' => $indicateursTbs,
            'affichage' => $affichage,
            'indicateurTbInfoPerdos' => $indicateurTbInfoPerdos
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function addIndicateurAction(Request $request)
    {
        $nom = $request->request->get('libelle');
        $affichage = intval($request->request->get('affichage'));
        $indicateurTbDomaine = Boost::deboost($request->request->get('indicateur_tb_domaine'),$this);
        if(is_bool($indicateurTbDomaine)) return new Response('security');
        $indicateurTbDomaine = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbDomaine')
            ->find($indicateurTbDomaine);

        $em = $this->getDoctrine()->getManager();
        $indicateurTb = new IndicateurTb();
        $indicateurTb
            ->setLibelle($nom)
            ->setAffichage($affichage)
            ->setIndicateurTbDomaine($indicateurTbDomaine);

        $em->persist($indicateurTb);
        try
        {
            $em->flush();
            return new Response(0);
        }
        catch (UniqueConstraintViolationException $ex)
        {
            return new Response(1);
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
        $indicateurTb = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($indicateurTb)) return new Response('security');
        $indicateurTb = $this->getDoctrine()->getRepository('AppBundle:IndicateurTb')->find($indicateurTb);
        $em = $this->getDoctrine()->getManager();

        if ($action == 0)
        {
            $indicateurOperandes = $this->getDoctrine()->getRepository('AppBundle:IndicateurOperande')
                ->getOperandesIndicateurTbs($indicateurTb);
            $indicateurTbDecisions = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbDecision')
                ->getIndicateurTbDecisions($indicateurTb);

            $typeRubrique = 2;
            if (count($indicateurOperandes) > 1)
            {
                $typeRubrique = $indicateurOperandes[0]->getRubrique()->getType();
            }

            return $this->render('IndicateurBundle:TbAdmin:indicateur-detail.html.twig',
                array('indicateurTb'=>$indicateurTb,'indicateurOperandes'=>$indicateurOperandes,'indicateurTbDecisions'=>$indicateurTbDecisions,'typeRubrique'=>$typeRubrique));
        }
        elseif ($action == 1)
        {
            $champ = intval($post->get('champ'));
            if ($champ == 0)
            {
                $libelle = $post->get('libelle');
                $indicateurTb->setLibelle($libelle);
                try
                {
                    $em->flush();
                    return new Response(0);
                }
                catch (UniqueConstraintViolationException $ex)
                {
                    return new Response(1);
                }
            }
            elseif ($champ == 1)
            {
                $ponderation = floatval($post->get('ponderation'));
                $indicateurTb->setPonderation($ponderation);

                $em->flush();
            }
            elseif ($champ == 2)
            {
                $formule = $post->get('formule');
                $operandes = json_decode($request->get('operandes'));
                $rubriques = [];
                foreach ($operandes as $operande)
                {
                    $rubrique = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->find($operande->id);
                    $r = new \stdClass();
                    $r->r = $rubrique;
                    $r->v = $operande->v;
                    $rubriques[] = $r;
                }
                $this->getDoctrine()->getRepository('AppBundle:IndicateurOperande')->setNewOperandesTb($indicateurTb,$rubriques);

                $indicateurTb->setFormule($formule);
                $em->flush();
            }
            elseif ($champ == 3)
            {
                $type = intval($post->get('type'));
                $indicateurTb->setType($type);
                $em->flush();
            }
            elseif ($champ == 4)
            {
                $norme = $post->get('norme');
                $indicateurTb->setNorme($norme);
                $em->flush();
            }
            elseif ($champ == 5)
            {
                $description = $post->get('description');
                $indicateurTb->setDescription($description);
                $em->flush();
            }
            elseif ($champ == 6)
            {
                $unite = intval($post->get('unite'));
                $indicateurTb->setUnite($unite);
                $em->flush();
            }
            elseif ($champ == 7)
            {
                $decimal = intval($post->get('dec'));
                $indicateurTb->setNbDecimal($decimal);
                $em->flush();
            }
        }
        elseif ($action == 2)
        {
            $type = intval($request->request->get('type'));

            if ($type == 0)
            {
                if ($indicateurTb)
                {
                    $em->remove($indicateurTb);
                    $em->flush();
                }
            }
            elseif ($type == 1)
            {
                $indicateurTbInfoPerdos = Boost::deboost($request->request->get('indicateur'),$this);
                if(is_bool($indicateurTbInfoPerdos)) return new Response('security');
                $indicateurTbInfoPerdos = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbInfoPerdos')
                    ->find($indicateurTbInfoPerdos);

                if ($indicateurTbInfoPerdos)
                {
                    $em->remove($indicateurTbInfoPerdos);
                    $em->flush();
                }
            }
        }
        return new Response(1);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editDecisionAction(Request $request)
    {
        $post = $request->request;
        $action = intval($post->get('action'));
        $em = $this->getDoctrine()->getManager();

        if ($action == 0)
        {
            $indicateurTb = Boost::deboost($post->get('indicateur'),$this);
            if (is_bool($indicateurTb)) return new Response('security');

            $indicateurTb = $this->getDoctrine()->getRepository('AppBundle:IndicateurTb')->find($indicateurTb);
            $indicateurTbDecision = new IndicateurTbDecision();
            $indicateurTbDecision->setIndicateurTb($indicateurTb);

            $em->persist($indicateurTbDecision);
        }
        elseif ($action == 1)
        {
            $indicateurTbDecision = Boost::deboost($post->get('decision'),$this);
            if (is_bool($indicateurTbDecision)) return new Response('security');

            /** @var IndicateurTbDecision $indicateurTbDecision */
            $indicateurTbDecision = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbDecision')
                ->find($indicateurTbDecision);

            if ($indicateurTbDecision != null)
            {
                $indicateurTbDecision
                    ->setPoint(floatval($post->get('point')))
                    ->setConditionTb($post->get('condition'))
                    ->setCommentaire($post->get('commentaire'));
            }
        }
        elseif ($action == 2)
        {
            $indicateurTbDecision = Boost::deboost($post->get('decision'),$this);
            if (is_bool($indicateurTbDecision)) return new Response('security');
            $indicateurTbDecision = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbDecision')
                ->find($indicateurTbDecision);

            if ($indicateurTbDecision != null) $em->remove($indicateurTbDecision);
        }
        elseif ($action == 3)
        {
            $indicateurTbDecision = Boost::deboost($post->get('decision'),$this);
            if (is_bool($indicateurTbDecision)) return new Response('security');
            $indicateurTbDecision = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbDecision')
                ->find($indicateurTbDecision);

            if ($indicateurTbDecision != null)
            {
                $icon = $post->get('icon');
                $indicateurTbDecision->setIcon($icon);
            }
        }

        $em->flush();
        return new Response(0);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function sortsAction(Request $request)
    {
        $post = $request->request;
        $sorts = json_decode($post->get('sorts'));

        foreach ($sorts as $sort)
        {
            $indicateurTb = Boost::deboost($sort->id,$this);

            if (is_bool($indicateurTb)) return new Response('security');
            $indicateurTb = $this->getDoctrine()->getRepository('AppBundle:IndicateurTb')->find($indicateurTb);

            $indicateurTb->setRang($sort->rang);
        }

        $this->getDoctrine()->getManager()->flush();
        return new Response(1);
        //sorts.push({ id:$(this).attr('data-id'), rang:index });
    }

    public function showAddInfoperdosAction(Request $request)
    {
        $indicateurInfoPerdos = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbInfoPerdos')
            ->nonAffecter();

        return $this->render('IndicateurBundle:TbAdmin:show-infod-perdos.html.twig',[
            'indicateurInfoPerdos' => $indicateurInfoPerdos
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function addIndicateurInfoperdosAction(Request $request)
    {
        $indicateurInfoPerdos = Boost::deboost($request->request->get('indicateur_info_perdos'),$this);
        if (is_bool($indicateurInfoPerdos)) return new Response('security');

        $indicateurInfoPerdos = $this->getDoctrine()->getRepository('AppBundle:IndicateurInfoPerdos')
            ->find($indicateurInfoPerdos);

        $indicateurTbInfoPerdos = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbInfoPerdos')
            ->getByIndicateurInfoPerdos($indicateurInfoPerdos);

        if (!$indicateurTbInfoPerdos)
        {
            $em = $this->getDoctrine()->getManager();
            $indicateurTbInfoPerdos = new IndicateurTbInfoPerdos();
            $indicateurTbInfoPerdos
                ->setIndicateurInfoPerdos($indicateurInfoPerdos);

            $em->persist($indicateurTbInfoPerdos);
            $em->flush();
        }

        return new Response(1);
    }

    public function indicateurTranfertAction(Request $request)
    {
        $indicateurTb = Boost::deboost($request->request->get('indicateur_tb'),$this);
        if (is_bool($indicateurTb)) return new Response('security');

        /** @var IndicateurTb $indicateurTb */
        $indicateurTb = $this->getDoctrine()->getRepository('AppBundle:IndicateurTb')
            ->find($indicateurTb);

        $affichage = intval($request->request->get('affichage'));
        $indicateurTbDomaines = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbDomaine')
            ->getAll($affichage);

        return $this->render('IndicateurBundle:TbAdmin:transfert-domaine.html.twig',[
            'indicateurTb' => $indicateurTb,
            'indicateurTbDomaines' => $indicateurTbDomaines
        ]);
    }

    public function indicateurTranfererAction(Request $request)
    {
        $indicateurTb = Boost::deboost($request->request->get('indicateur_tb'),$this);
        $indicateurTbDomaine = Boost::deboost($request->request->get('indicateur_tb_domaine'), $this);

        if (is_bool($indicateurTb) || is_bool($indicateurTbDomaine)) return new Response('security');

        /** @var IndicateurTb $indicateurTb */
        $indicateurTb = $this->getDoctrine()->getRepository('AppBundle:IndicateurTb')
            ->find($indicateurTb);
        /** @var IndicateurTbDomaine $indicateurTbDomaine */
        $indicateurTbDomaine = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbDomaine')
            ->find($indicateurTbDomaine);

        $indicateurTb
            ->setIndicateurTbDomaine($indicateurTbDomaine);

        try
        {
            $this->getDoctrine()->getManager()->flush();
            return new Response(0);
        }
        catch (Exception $exception)
        {
            return new Response(1);
        }
    }
}
