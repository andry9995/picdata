<?php

namespace RubriqueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('RubriqueBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * @param $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rubriquesAction($type)
    {
        return $this->render('RubriqueBundle:Default:rubriques.html.twig',
            array('rubriques'=>$this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques($type)));
    }
}
