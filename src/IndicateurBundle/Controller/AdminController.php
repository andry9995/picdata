<?php

namespace IndicateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class AdminController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('IndicateurBundle:Admin:index.html.twig',array());
    }
}