<?php

namespace DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    public function containerAction()
    {
        return $this->render('DashboardBundle::dashboard.html.twig');
    }

    public function indexAction()
    {
        return $this->render('DashboardBundle:dashboard:index.html.twig');
    }
}