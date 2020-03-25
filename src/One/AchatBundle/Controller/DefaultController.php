<?php

namespace One\AchatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('OneAchatBundle:Default:index.html.twig', array('name' => $name));
    }
}
