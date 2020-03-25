<?php

namespace AideBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AideBundle:Default:index.html.twig', array('name' => $name));
    }
}
