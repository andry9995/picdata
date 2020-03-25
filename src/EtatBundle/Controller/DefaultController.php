<?php

namespace EtatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EtatBundle:Default:index.html.twig', array('name' => $name));
    }
}
