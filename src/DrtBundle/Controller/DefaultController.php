<?php

namespace DrtBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DrtBundle:Default:index.html.twig');
    }
}
