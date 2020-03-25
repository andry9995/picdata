<?php

namespace ChartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ChartBundle:Default:index.html.twig', array('name' => $name));
    }
}
