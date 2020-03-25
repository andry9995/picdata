<?php

namespace One\UtilisateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction(){
        return $this->render('OneUtilisateurBundle:Default:index.html.twig');
    }
}
