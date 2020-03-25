<?php

namespace ImageBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\CommentaireDossier;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentaireController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function listeAction(Request $request)
    {
        $post = $request->request;

        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $commentaires = $this->getDoctrine()->getRepository('AppBundle:CommentaireDossier')->getCommentaires($dossier);
        return $this->render('ImageBundle:Commentaire:liste.html.twig',array('commentaires'=>$commentaires));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request)
    {
        $post = $request->request;

        $commentaireDossier = Boost::deboost($post->get('commentaire_dossier'),$this);
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($commentaireDossier) || is_bool($dossier)) return new Response('security');

        $code = $post->get('code');
        $commentaire = $post->get('libelle');
        $em = $this->getDoctrine()->getManager();

        /** @var CommentaireDossier $commentaireDossier */
        $commentaireDossier = $this->getDoctrine()->getRepository('AppBundle:CommentaireDossier')
            ->find($commentaireDossier);

        if(is_null($commentaireDossier))
        {
            $commentaireDossier = new CommentaireDossier();

            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->find($dossier);

            $commentaireDossier
                ->setDossier($dossier)
                ->setCode($code)
                ->setCommentaire($commentaire);

            $em->persist($commentaireDossier);
        }
        elseif(intval($post->get('action')) == 2) $em->remove($commentaireDossier);
        else $commentaireDossier->setCode($code)->setCommentaire($commentaire);

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
}
