<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 10/01/2018
 * Time: 11:36
 */

namespace NoteFraisBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\NdfNote;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class NoteFraisNoteController extends Controller
{
    function noteEditAction(Request $request, $json)
    {

        if ($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);
            $noteId = Boost::deboost($post->get('noteId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            /** @var NdfNote $note */
            $note = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->find($noteId);

            $exercices = Boost::getExercices(3, 1);

            sort($exercices, SORT_NUMERIC);

            $utilsateurs = $this->getDoctrine()
                ->getRepository('AppBundle:NdfUtilisateur')
                ->findBy(array('dossier' => $dossier));


            $mois = array();

            if (null !== $note) {
                $mois = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfNote')
                    ->getMoisNote($note);
            }

            //Affichage modal
            if ($json == 0) {

                return $this->render('NoteFraisBundle:Note:noteEdit.html.twig', array(
                    'note' => $note,
                    'exericices' => $exercices,
                    'mois' => $mois,
                    'utilisateurs' => $utilsateurs
                ));
            }

            //Sauvegarde
            $libelle = $post->get('libelle');
            $description = $post->get('description');

            $utilisateurId = Boost::deboost($post->get('utilisateur'), $this);

            $utilisateur = $this->getDoctrine()
                ->getRepository('AppBundle:NdfUtilisateur')
                ->find($utilisateurId);

            $annee = $post->get('annee');
            $moiss = $post->get('mois');
            $mois = '';
            if ($annee == '') {
                $annee = null;
            }

            if (count($moiss) > 0) {
                foreach ($moiss as $m) {
                    if ($mois == "") {
                        $mois .= $m;
                    } else {
                        $mois .= "," . $m;
                    }
                }
            }

            if ($description == '') {
                $description = null;
            }

            $em = $this->getDoctrine()
                ->getEntityManager();

            //Mise à jour
            if (null !== $note) {
                $note->setLibelle($libelle);
                $note->setDescription($description);
                $note->setAnnee($annee);
                $note->setMois($mois);
                $note->setNdfUtilisateur($utilisateur);

                $em->flush();
            } //Insertion
            else {

                $note = new NdfNote();
                $note->setDossier($dossier);

                $note->setLibelle($libelle);
                $note->setDescription($description);

                $note->setAnnee($annee);
                $note->setMois($mois);
                $note->setNdfUtilisateur($utilisateur);

                $em->persist($note);
                $em->flush();
            }


            return new JsonResponse(array('id' => Boost::boost($note->getId()),
                'libelle' => $note->getLibelle(),
                'description' => $note->getDescription()));
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

}