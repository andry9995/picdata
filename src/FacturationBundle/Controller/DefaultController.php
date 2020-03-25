<?php

namespace FacturationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FacturationBundle:Default:index.html.twig', array('name' => $name));
    }
}
