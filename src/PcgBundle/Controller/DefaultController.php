<?php

namespace PcgBundle\Controller;

use AppBundle\Entity\Pcg;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PcgBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request)
    {
        $post = $request->request;
        $compte = intval($post->get('compte'));
        $intitule = trim($post->get('intitule'));
        $action = intval($post->get('action'));

        $em = $this->getDoctrine()->getEntityManager();
        if($action == 1)
        {
            $pcg = new Pcg();
            $pcg->setCompte($compte);
            $pcg->setIntitule($intitule);

            $em->persist($pcg);
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
    }
}
