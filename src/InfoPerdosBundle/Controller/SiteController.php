<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 01/06/2017
 * Time: 10:58
 */

namespace InfoPerdosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{
    public function indexAction(){
        return $this->render('InfoPerdosBundle:Site:index.html.twig');
    }

}