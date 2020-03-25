<?php

namespace EtatBaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HistoriqueUploadController extends Controller
{
    public function indexAction()
    {
        return $this->render('EtatBaseBundle:HistoriqueUpload:index.html.twig');
    }
}
