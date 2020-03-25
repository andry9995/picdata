<?php

namespace TypeGrapheBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\TypeGraphe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TypeGrapheBundle:Default:index.html.twig', array('name' => $name));
    }
}
