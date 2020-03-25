<?php

namespace AjaxLoginBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function autoAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')
            ->find(Boost::deboost($request->query->get('l'),$this));
        $this->redirect( $this->generateUrl('logout' ));
        $test = $this->autologin($user);
        return $this->redirectToRoute($request->query->get('ln'));
    }

    /**
     * @param Utilisateur|null $user
     * @return bool
     */
    private function autologin(Utilisateur $user = null)
    {
        if ($user)
        {
            $p = $user->getPassword();
            $token = new UsernamePasswordToken($user, $p, 'main', $user->getRoles());
            $context = $this->get('security.context');
            $context->setToken($token);
            return true;
        }

        return false;
    }
}
