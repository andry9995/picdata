<?php

namespace InfoPerdosBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\LibelleDossier;
use AppBundle\Entity\LibelleModele;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('InfoPerdosBundle:Default:index.html.twig', array('name' => $name));
    }

    public function libelleEcritureAction(){

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(11387);

        $libelleType = $this->getDoctrine()
            ->getRepository('AppBundle:LibelleType')
            ->find(1);

        /** @var LibelleModele[] $libelleModeles */
        $libelleModeles = $this->getDoctrine()
            ->getRepository('AppBundle:LibelleModele')
            ->getLibelleModeleByDossier($dossier, $libelleType);

        /** @var LibelleDossier[] $libelleDossiers */
        $libelleDossiers = $this->getDoctrine()
            ->getRepository('AppBundle:LibelleDossier')
            ->getLibelleDossierByType($dossier, $libelleType);

        return $this->render('@InfoPerdos/MethodeComptable/libelleEcriture.html.twig', array(
            'libelleModeles' => $libelleModeles,
            'libelleDossiers' => $libelleDossiers
        ));
    }

    public function libelleEcritureSaveAction(Request $request){

        $post = $request->request;

        $items = $post->get('items');
        $dossierid = $post->get('dossierid');

        if($dossierid == '0'){
            $ret = ['message' => 'Dossier null', 'type'=> 'error'];
            return new JsonResponse($ret);
        }

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($dossierid, $this));

        $libelletypeid = $post->get('libelletype');
        $libelletype = $this->getDoctrine()
            ->getRepository('AppBundle:LibelleType')
            ->find($libelletypeid);

        $libelleDossiers = $this->getDoctrine()
            ->getRepository('AppBundle:LibelleDossier')
            ->getLibelleDossierByType($dossier, $libelletype);

        $em = $this->getDoctrine()->getManager();

        $ret =[];

        if(count($libelleDossiers) > 0){

            foreach ($libelleDossiers as $libelleDossier){
                $em->remove($libelleDossier);
            }
            $em->flush();

            if(count($items) === 0){
                $ret = ['message' => 'supression des libelles effectuées', 'type'=> 'success'];
            }
        }

        $rang = 1;
        foreach ($items as $item){

            $libelleItem = $this->getDoctrine()
                ->getRepository('AppBundle:LibelleItem')
                ->find($item['id']);

            $libelleNbCar = $item['nbcar'];
            if($libelleNbCar === ''){
                $libelleNbCar = 0;
            }

            $libellePos = $item['position'];
            if($libellePos === ''){
                $libellePos = 0;
            }

            $libelleDossier = new LibelleDossier();

            $libelleDossier->setLibelleItem($libelleItem);
            $libelleDossier->setLibelleType($libelletype);
            $libelleDossier->setDossier($dossier);
            $libelleDossier->setNbCaractere($libelleNbCar);
            $libelleDossier->setPosition($libellePos);
            $libelleDossier->setRang($rang);

            $rang++;
            $em->persist($libelleDossier);
        }

        $em->flush();

        $ret = ['message' => 'Insertions des libelles effectuées', 'type'=> 'success'];





        return new JsonResponse($ret);

    }
}
