<?php

namespace EtatFinancierBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EtatFinancierBundle:Default:index.html.twig', array('name' => $name));
    }
}
