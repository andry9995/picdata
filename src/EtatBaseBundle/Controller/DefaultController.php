<?php

namespace EtatBaseBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends Controller
{
    /**
     * @param $etat
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction($etat,Request $request)
    {
        $get = $request->query;

        if($request->query->has('token'))
        {
            if (!$this->autologinAction($get->get('token'))) return new Response('Utilisateur inconnu!!');
        }
        elseif($this->getUser() == null)
        {
            return $this->redirect($this->generateUrl('login'));
        }
        return $this->render('EtatBaseBundle:Default:index.html.twig', array('etat'=>$etat));
    }

    /**
     * @param $login
     * @return bool
     */
    public function autologinAction($login)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')
            ->getUserByLogin(Boost::deboost($login,$this));

        $p = Boost::deboost($user->getPassword(),$this);
        if($user != null && !is_bool($p))
        {
            $token = new UsernamePasswordToken($user, $p, 'main', $user->getRoles());
            $context = $this->get('security.context');
            $context->setToken($token);
            return true;
        }
        return false;
    }
}