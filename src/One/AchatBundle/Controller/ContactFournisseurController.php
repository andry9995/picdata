<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 04/04/2018
 * Time: 14:20
 */

namespace One\AchatBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactFournisseurController extends Controller
{
    /**
     * Création d'un nouveau contact fournisseur
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction() {

        $pays = $this->getDoctrine()
            ->getRepository('AppBundle:Pays')
            ->findAll();
        return $this->render('OneAchatBundle:ContactFournisseur:new.html.twig', array('payss' => $pays));
    }

    /**
     * Edition d'un contact client
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction() {
        $pays = $this->getDoctrine()
            ->getRepository('AppBundle:Pays')
            ->findAll();

        return $this->render('OneAchatBundle:ContactFournisseur:edit.html.twig', array('payss' => $pays));
    }

}