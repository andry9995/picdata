<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * Utile pour décoder et encoder mot de passe
     *
     * @return Response
     */
    public function indexAction()
    {
        return new Response('');
    }

    public function emailTemplateAction()
    {
        return $this->render('user-email.html.twig');
    }
}
