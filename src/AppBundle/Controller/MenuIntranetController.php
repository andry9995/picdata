<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 27/02/2019
 * Time: 14:15
 */

namespace AppBundle\Controller;


use AppBundle\Entity\OperateurUtilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MenuIntranetController extends Controller
{
    /**
     * @return Response
     */
    public function operateurAction()
    {
        /** @var OperateurUtilisateur $utilisateurOperateur */
        $utilisateurOperateur = $this->getDoctrine()
            ->getRepository('AppBundle:OperateurUtilisateur')
            ->getUtilisateurOperateur(null,$this->getUser());
        if ($utilisateurOperateur)
            return new Response($utilisateurOperateur->getOperateur()->getId());

        return new Response(0);
    }
}