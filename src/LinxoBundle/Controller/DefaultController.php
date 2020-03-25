<?php

namespace LinxoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LinxoBundle:Default:index.html.twig', array('name' => $name));
    }
}
