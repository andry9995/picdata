<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 04/01/2018
 * Time: 13:06
 */

namespace NoteFraisBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\NdfCategorieDossier;
use AppBundle\Entity\NdfDepense;
use AppBundle\Entity\NdfDepenseFraisKm;
use AppBundle\Entity\NdfSouscategorieDossier;
use AppBundle\Entity\Soussouscategorie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class NoteFraisController extends Controller
{
    public function indexAction(Request $request)
    {
        $post = $request->request;
        $dossierId = $post->get('dossierId');

        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find(Boost::deboost($dossierId, $this));

        $notes = $this->getDoctrine()
            ->getRepository('AppBundle:NdfNote')
            ->findBy(array('dossier' => $dossier));

        $detailNotes = $this->getDoctrine()
            ->getRepository('AppBundle:NdfNote')
            ->getNoteDetails($notes);

        $depenses = $this->getDoctrine()
            ->getRepository('AppBundle:NdfDepense')
            ->findBy(array('dossier' => $dossier));

        $detailDepenses = $this->getDoctrine()
            ->getRepository('AppBundle:NdfDepense')
            ->getDetailsDepense($depenses);

        $depenseFKs = $this->getDoctrine()
            ->getRepository('AppBundle:NdfDepenseFraisKm')
            ->findBy(array('dossier' => $dossier));

        return $this->render('NoteFraisBundle:Default:index.html.twig', array(
            'detailNotes' => $detailNotes,
            'detailDepenses' => $detailDepenses,
            'depenseFKs' => $depenseFKs,
            'fromModal' => false));

    }

    public function indexDepenseAction(Request $request){

        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);
            $noteId = Boost::deboost($post->get('noteId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $detailDepenses = array();
            $depenseFKs = array();

            $notes = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->findBy(array('dossier' => $dossier));

            $note = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->find($noteId);

            if (null !== $note) {
                $depenses = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfDepense')
                    ->findBy(array('ndfNote' => $note));

                $detailDepenses = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfDepense')
                    ->getDetailsDepense($depenses);

                $depenseFKs = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfDepenseFraisKm')
                    ->findBy(array('ndfNote' => $note));
            }


            return $this->render('NoteFraisBundle:Depense:index.html.twig', array(
                'detailDepenses' => $detailDepenses,
                'depenseFKs' => $depenseFKs,
                'notes' => $notes,
                'note' => $note
            ));
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    public function indexImageAction(){

        $exercices = Boost::getExercices(3,1);
        sort($exercices, SORT_NUMERIC);
        return $this->render('NoteFraisBundle:Image:index.html.twig', array(
            'exericices' => $exercices,
            'note' => null));

    }

    public function indexNoteAction(Request $request){

        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $notes = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->findBy(array('dossier' => $dossier));

            $detailNotes = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->getNoteDetails($notes);

            $affaires = $this->getDoctrine()
                ->getRepository('AppBundle:NdfAffaire')
                ->findBy(array('dossier' => $dossier));


            return $this->render('NoteFraisBundle:Note:index.html.twig', array(
                'detailNotes' => $detailNotes,
                'affaires' => $affaires,
                'fromModal' => false));
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }

    public function indexParametreAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId =  Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $ndfSousCategoriesDossier = array();

            $ndfSouscategoriePcg = array();

            $ndfCategories = $this->getDoctrine()
                ->getRepository('AppBundle:NdfCategorie')
                ->getNdfCategorieByDossier($dossier);

            if (null !== $dossier) {


                $ndfSousCategoriesDossier = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfSouscategorieDossier')
                    ->findBy(array('dossier' => $dossier), array('libelle' => 'ASC'));

                //Raha mbola tsy misy dia initialiser-na aloha
                if (count($ndfSousCategoriesDossier) == 0) {
                    $this->initialiseSousCategorieDossier($dossier);

                    $ndfSousCategoriesDossier = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfSouscategorieDossier')
                        ->findBy(array('dossier' => $dossier), array('libelle' => 'ASC'));
                }


                foreach ($ndfSousCategoriesDossier as $scat) {

                    if (null === $scat) {
                        continue;
                    }

                    $ndfScatPcgCharges = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfSouscategorieCharge')
                        ->findBy(array('ndfSouscategorie' => $scat->getNdfSouscategorie()));
                    $pcgCharge = '';
                    foreach ($ndfScatPcgCharges as $ndfScatPcgCharge) {
                        $temp = $ndfScatPcgCharge->getCompte();

                        $tempLength = strlen($temp);

                        if ($tempLength < 6) {
                            for ($i = $tempLength; $i < 6; $i++) {
                                $temp = $temp . 'X';
                            }
                        }
                        if (str_replace(' ', '', $pcgCharge) === '') {
                            $pcgCharge = $temp;
                        } else {
                            $pcgCharge .= ', ' . $temp;
                        }
                    }

                    $ndfScatPcgTvas = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfSouscategorieTva')
                        ->findBy(array('ndfSouscategorie' => $scat->getNdfSouscategorie()));

                    $pcgTva = '';
                    foreach ($ndfScatPcgTvas as $ndfScatPcgCharge) {
                        $temp = $ndfScatPcgCharge->getCompte();

                        $tempLength = strlen($temp);


                        if ($tempLength < 6) {
                            for ($i = $tempLength; $i < 6; $i++) {
                                $temp = $temp . 'X';
                            }
                        }
                        if ($pcgTva === '') {
                            $pcgTva = $temp;
                        } else {
                            $pcgTva .= ', ' . $temp;
                        }
                    }

                    $ndfSouscategoriePcg[] = array(
                        'ndfSouscategorie' => $scat,
                        'pcgCharge' => $pcgCharge,
                        'pcgTva' => $pcgTva
                    );
                }

            }


            $typeVehicules = $this->getDoctrine()
                ->getRepository('AppBundle:NdfTypeVehicule')
                ->findAll();

            $vehicules = $this->getDoctrine()
                ->getRepository('AppBundle:Vehicule')
                ->findBy(array('dossier' => $dossier));


            $affaires = $this->getDoctrine()
                ->getRepository('AppBundle:NdfAffaire')
                ->findBy(array('dossier' => $dossier));

            $contacts = $this->getDoctrine()
                ->getRepository('AppBundle:NdfContact')
                ->findBy(array('dossier' => $dossier));

            $utilisateurs = $this->getDoctrine()
                ->getRepository('AppBundle:NdfUtilisateur')
                ->findBy(array('dossier' => $dossier));


            return $this->render('NoteFraisBundle:Administration:index.html.twig', array(
                'typeVehicules' => $typeVehicules,
                'vehicules' => $vehicules,
                'affaires' => $affaires,
                'contacts' => $contacts,
                'ndfSousCategoriesDossier' => $ndfSousCategoriesDossier,
                'ndfCategories' => $ndfCategories,
                'utilisateurs' => $utilisateurs,
                'ndfSouscategoriesDossierPcg' => $ndfSouscategoriePcg
            ));
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }

    public function depenseNoteTableauAction(Request $request, $json){

        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if ((int)$json === 0) {
                $notes = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfNote')
                    ->findBy(array('dossier' => $dossier));

                $detailNotes = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfNote')
                    ->getNoteDetails($notes);

                return $this->render('NoteFraisBundle:Note:noteTable.html.twig', array(
                    'detailNotes' => $detailNotes,
                    'fromModal' => true));
            }

            if ((int)$json === 3) {
                $notes = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfNote')
                    ->findBy(array('dossier' => $dossier));

                $detailNotes = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfNote')
                    ->getNoteDetails($notes);

                return $this->render('NoteFraisBundle:Note:noteTable.html.twig', array(
                    'detailNotes' => $detailNotes,
                    'fromModal' => false));
            }


            //Assigner Note
            $post = $request->request;

            $depenseId = Boost::deboost($post->get('depenseId'), $this);

            $dataType = $post->get('dataType');

            $noteId = Boost::deboost($post->get('noteId'), $this);
            $note = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->find($noteId);

            $noteId = null;
            $noteLibelle = "";


            if (null !== $note) {

                $noteId = Boost::boost($note->getId());
                $noteLibelle = $note->getLibelle();

                $em = $this->getDoctrine()
                    ->getEntityManager();

                if ($dataType == 0) {
                    $depense = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfDepense')
                        ->find($depenseId);

                    if (null !== $depense) {
                        $depense->setNdfNote($note);
                        $em->flush();
                    }
                } else if ($dataType == 1) {
                    /** @var NdfDepenseFraisKm $depenseFK */
                    $depenseFK = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfDepenseFraisKm')
                        ->find($depenseId);

                    if (null !== $depenseFK) {
                        $depenseFK->setNdfNote($note);
                        $em->flush();
                    }
                }
            }

            return new JsonResponse(array(
                'noteId' => $noteId,
                'noteLibelle' => $noteLibelle));
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }


    /** Mi-initialiser ny ndf_categorie_dossier raha mbola tsy misy */
    /** @var Dossier  $dossier*/
    public function initialiseCategorieDossier($dossier){

        $ndfCategorieDossiers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:NdfCategorieDossier')
            ->findBy(array('dossier' => $dossier));


        if(count($ndfCategorieDossiers) == 0){
            $ndfCategories = $this->getDoctrine()
                ->getRepository('AppBundle:NdfCategorie')
                ->findAll();

            $em = $this->getDoctrine()
                ->getEntityManager();

            foreach ($ndfCategories as $ndfCategorie){
                $ndfCategorieDossier = new NdfCategorieDossier();

                $ndfCategorieDossier->setDossier($dossier);
                $ndfCategorieDossier->setLibelle($ndfCategorie->getLibelle());

                $em->persist($ndfCategorieDossier);
                $em->flush();
            }

        }
    }



    /** Mi-initialiser ny ndf_categorie_dossier raha mbola tsy misy */
    /** @var Dossier  $dossier*/
    public function initialiseSousCategorieDossier($dossier){

        $ndfSousCategoriesDossier = $this
            ->getDoctrine()
            ->getRepository('AppBundle:NdfSousCategorieDossier')
            ->findBy(array('dossier' => $dossier));


        if(count($ndfSousCategoriesDossier) == 0){
            $ndfSousCategories = $this->getDoctrine()
                ->getRepository('AppBundle:NdfSousCategorie')
                ->findAll();

            $em = $this->getDoctrine()
                ->getEntityManager();

            foreach ($ndfSousCategories as $ndfSouscategorie){
                $ndfSousCategorieDossier = new NdfSouscategorieDossier();

                $ndfSousCategorieDossier->setDossier($dossier);
                /** @var Soussouscategorie $soussouscategorie */
                $soussouscategorie = $ndfSouscategorie->getSoussouscategorie();
                $ndfSousCategorieDossier->setNdfSouscategorie($ndfSouscategorie);
                $ndfSousCategorieDossier->setLibelle($soussouscategorie->getLibelleNew());
                $ndfSousCategorieDossier->setTvaTaux($ndfSouscategorie->getTvaTaux());
                $ndfSousCategorieDossier->setTvaRec($ndfSouscategorie->getTvaRec());
                $ndfSousCategorieDossier->setStatus(1);

                $em->persist($ndfSousCategorieDossier);
                $em->flush();
            }

        }
    }
}