<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndexController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** Les clients des experts comptables ne doivent pas voir la page d'acceuil
            On leur redirige vers le dashboard (qui redirige vers le login si pas connecté)
         */
        $current_host = $request->getHttpHost();
        if (preg_match('/tanaco|compta-sympa|comptaetgestion/i',$current_host)) {
            return $this->redirectToRoute('dashboard_homepage', [], 301);
        }
        return $this->render('AppBundle:index:index.html.twig');
    }
}
