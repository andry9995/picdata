<?php

namespace ConsultationPieceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ConsultationPieceBundle:Default:index.html.twig');
    }

    public function tableAction(){
        $table = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->getTableByImageName('ACBK2E004');

      return new JsonResponse($table);
        
    }
}
