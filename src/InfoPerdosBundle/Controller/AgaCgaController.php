<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 07/02/2017
 * Time: 08:16
 */

namespace InfoPerdosBundle\Controller;


use AppBundle\Entity\AgaCga;
use AppBundle\Entity\Dossier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AgaCgaController extends  Controller
{

    public function editInfoPerdosAgaCgaAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $post = $request->request;

            $adherant = $post->get('adherant');
            $nom = $post->get('nom');
            $siren = $post->get('siren');
            if($siren == ''){
                $siren = null;
            }

            $numeroAdhesion = $post->get('numeroAdhesion');
            if($numeroAdhesion == ''){
                $numeroAdhesion = null;
            }



            $dateAdh = $post->get('dateAdhesion');

            if($dateAdh != '') {
                $dateAdhesion = \DateTime::createFromFormat("d/m/Y", $dateAdh);
                if (!$dateAdhesion) {
                    throw new \UnexpectedValueException("Could not parse the date: $dateAdh");
                }
            }
            else{
                $dateAdhesion = null;
            }


            $numRue = $post->get('numRue');
            if($numRue == ''){
                $numRue = null;
            }

            $codePostal = $post->get('codePostal');
            if($codePostal == ''){
                $codePostal = null;
            }

            $ville = $post->get('ville');
            if($ville == ''){
                $ville = null;
            }

            $pays = $post->get('pays');
            if($pays == ''){
                $pays = null;
            }

            $em = $this->getDoctrine()->getEntityManager();

            $idDossier = Boost::deboost($post->get('dossierId'), $this);


            if ($idDossier == 0) {
                return new Response(-1);
            } else {
                $dossier = $this->getDoctrine()
                    ->getRepository('AppBundle:Dossier')
                    ->find($idDossier);


                $agaCgas = $this->getDoctrine()
                    ->getRepository('AppBundle:AgaCga')
                    ->findBy(array('dossier'=>$dossier));

                //Nouveau aga cga
                if (count($agaCgas) == 0) {
                    try {


                        $agaCga = new AgaCga();

                        $agaCga->setDossier($dossier);
                        $agaCga->setAdherant($adherant);
                        $agaCga->setNom($nom);
                        $agaCga->setSiren($siren);
                        $agaCga->setNumeroAdhesion($numeroAdhesion);
                        $agaCga->setDateAdhesion($dateAdhesion);
                        $agaCga->setNumRue($numRue);
                        $agaCga->setCodePostal($codePostal);
                        $agaCga->setVille($ville);
                        $agaCga->setPays($pays);

                        $em->persist($agaCga);

                        $em->flush();

                        return new Response(1);

                    } catch (Exception $e) {
                        return new Response($e->getMessage());
                    }
                } //Mise à jour
                else {
                    try {

                        $agaCga = $agaCgas[0];

                        $agaCga->setAdherant($adherant);
                        $agaCga->setNom($nom);
                        $agaCga->setSiren($siren);
                        $agaCga->setNumeroAdhesion($numeroAdhesion);
                        $agaCga->setDateAdhesion($dateAdhesion);
                        $agaCga->setNumRue($numRue);
                        $agaCga->setCodePostal($codePostal);
                        $agaCga->setVille($ville);
                        $agaCga->setPays($pays);

                        $em->persist($agaCga);

                        $em->flush();


                        return new Response(2);
                    } catch (Exception $e) {
                        return new Response($e->getMessage());
                    }
                }
            }


        } else {
            throw new AccessDeniedHttpException("Accès refusé");
        }
    }


}