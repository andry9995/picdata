<?php

namespace ConsultationPieceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DecompteImageController extends Controller
{
    public function indexAction()
    {
        return $this->render('ConsultationPieceBundle:DecompteImage:index.html.twig', array(
                // ...
            ));    }

}
