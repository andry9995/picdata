<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 10/01/2019
 * Time: 16:28
 */

namespace AideBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GuideController extends Controller
{
    public function indexAction(){
        return $this->render('@Aide/Guide/index.html.twig');
    }

}