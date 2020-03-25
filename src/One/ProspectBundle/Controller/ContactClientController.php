<?php

/**
 * Created by Netbeans
 * Created on : 29 juin 2017, 13:32:01
 * Author : Mamy Rakotonirina
 */

namespace One\ProspectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use One\ProspectBundle\Service\ContactClientService;

class ContactClientController extends Controller
{
    /**
     * Création d'un nouveau contact client
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction() {
        $service = new ContactClientService($this->getDoctrine()->getManager());
//        $countries = $this->getDoctrine()->getRepository('AppBundle:OnePays')->getCountries();
        $countries = $this->getDoctrine()
            ->getRepository('AppBundle:Pays')
            ->findBy(array(), array('nom' => 'ASC'));

        return $this->render('OneProspectBundle:ContactClient:new.html.twig', array(
            'countries' => $countries,
            'mycountry' => $service->getMyCountry(),
        ));
    }
    
    /**
     * Edition d'un contact client
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction() {
        $service = new ContactClientService($this->getDoctrine()->getManager());
//        $countries = $this->getDoctrine()->getRepository('AppBundle:OnePays')->getCountries();
        $countries = $this->getDoctrine()
            ->getRepository('AppBundle:Pays')
            ->findBy(array(), array('nom' => 'ASC'));

        return $this->render('OneProspectBundle:ContactClient:edit.html.twig', array(
            'countries' => $countries,
            'mycountry' => $service->getMyCountry(),
        ));
    }
}