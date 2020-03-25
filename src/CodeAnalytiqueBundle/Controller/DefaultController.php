<?php

namespace CodeAnalytiqueBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CodeAnalytiqueBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function codeAnalytiquesAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id',$dossier)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('CodeAnalytiqueBundle:Default:code_analytiques.html.twig',
            array('code_analytiques'=>$this->getDoctrine()->getRepository('AppBundle:CodeAnalytique')->getCodeAnalytique($dossier)));
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function listeAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id',$dossier)
            ->getQuery()
            ->getOneOrNullResult();

        //$codeAnalytiques = $this->getDoctrine()->getRepository('AppBundle:CodeAnalytique')->getCodeAnalytique($dossier);
        $commentaireDossiers = $this->getDoctrine()->getRepository('AppBundle:CommentaireDossier')->getCommentaires($dossier);

        $cAs = [];
        $cAs = $this->getDoctrine()->getRepository('AppBundle:CodeAnalytique')
            ->getCodeAnalytiqueGroupedObject($dossier);
        /*foreach ($codeAnalytiques as $codeAnalytique)
            $cAs[] = (object)
            [
                'id' => $codeAnalytique->getId(),
                'code' => $codeAnalytique->getCode(),
                'libelle' => $codeAnalytique->getLibelle()
            ];*/
        $cDs = [];
        foreach ($commentaireDossiers as $commentaireDossier)
            $cDs[] = (object)
            [
                'id' => $commentaireDossier->getId(),
                'code' => $commentaireDossier->getCode(),
                'libelle' => $commentaireDossier->getCommentaire()
            ];

        return new JsonResponse((object)
        [
            'ca' => $cAs,
            'cd' => $cDs
        ]);

        return new Response(Boost::serialize($codeAnalytiques));
    }
}
