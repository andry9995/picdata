<?php

namespace AdminUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParametrageController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminUserBundle:Parametrage:index.html.twig', array());
    }
}
