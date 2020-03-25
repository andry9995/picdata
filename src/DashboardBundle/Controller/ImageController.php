<?php

namespace DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    public function indexAction()
    {
        return $this->render('DashboardBundle:image:index.html.twig');
    }
}