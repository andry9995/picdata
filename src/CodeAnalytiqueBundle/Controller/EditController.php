<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 06/09/2016
 * Time: 11:57
 */

namespace CodeAnalytiqueBundle\Controller;
use AppBundle\Controller\Boost;
use AppBundle\Entity\CodeAnalytique;
use AppBundle\Entity\CodeAnalytiqueSection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Count;

class EditController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        return $this->render('CodeAnalytiqueBundle:Edit:index.html.twig',array('dossier'=>$dossier));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function listeAction(Request $request)
    {
        $post = $request->request;

        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->createQueryBuilder('d')
            ->where('d.id = :id')
            ->setParameter('id',$dossier)
            ->getQuery()
            ->getOneOrNullResult();

        $analytiquesGroupeds = $this->getDoctrine()->getRepository('AppBundle:CodeAnalytique')
            ->getCodeAnalytiqueGroupedInDossier($dossier);

        return $this->render('CodeAnalytiqueBundle:Edit:liste.html.twig',[
            'analytiquesGroupeds' => $analytiquesGroupeds
        ]);

        return $this->render('CodeAnalytiqueBundle:Edit:liste.html.twig',[
            'sections' => $sections,
            'analytiques' => $analytiques
        ]);
    }

    /**
     * Add Modif suppr code analytique
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request)
    {
        $post = $request->request;

        $id_code_analytique = Boost::deboost($post->get('analytique'),$this);
        $id_dossier = Boost::deboost($post->get('dossier'),$this);
        $codeAnalytiqueSection = Boost::deboost($post->get('analytique_section'),$this);
        if(is_bool($id_code_analytique) || is_bool($id_dossier) || is_bool($codeAnalytiqueSection)) return new Response('security');

        $code = $post->get('code');
        $libelle = $post->get('libelle');
        $em = $this->getDoctrine()->getManager();

        /** @var CodeAnalytiqueSection $codeAnalytiqueSection */
        $codeAnalytiqueSection = $this->getDoctrine()->getRepository('AppBundle:CodeAnalytiqueSection')
            ->find($codeAnalytiqueSection);

        $type = intval($request->request->get('type'));

        if ($type === 1)
            $codeAnalytique = $this->getDoctrine()->getRepository('AppBundle:CodeAnalytique')
                ->find($id_code_analytique);
        else
            $codeAnalytique = $this->getDoctrine()->getRepository('AppBundle:CodeAnalytiqueSection')
                ->find($id_code_analytique);

        if($codeAnalytique == null)
        {
            $section = intval(Boost::deboost($request->request->get('section'),$this));
            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->createQueryBuilder('d')
                ->where('d.id = :id')
                ->setParameter('id',$id_dossier)
                ->getQuery()
                ->getOneOrNullResult();
            if ($section == -1)
            {
                $section = new CodeAnalytiqueSection();
                $section
                    ->setDossier($dossier)
                    ->setLibelle($libelle)
                    ->setCode($code);
                $em->persist($section);
            }
            else
            {
                $section = $this->getDoctrine()->getRepository('AppBundle:CodeAnalytiqueSection')
                    ->find($section);
                $codeAnalytique = new CodeAnalytique();
                $codeAnalytique
                    ->setDossier($dossier)
                    ->setCode($code)
                    ->setLibelle($libelle)
                    ->setCodeAnalytiqueSection($section);

                $em->persist($codeAnalytique);
            }
        }
        elseif(intval($post->get('action')) == 2) $em->remove($codeAnalytique);
        else
        {
            $codeAnalytique->setCode($code)->setLibelle($libelle);

            if ($type === 1)
                $codeAnalytique
                    ->setCodeAnalytiqueSection($codeAnalytiqueSection);
        }

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