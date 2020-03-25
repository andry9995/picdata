<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 09/01/2018
 * Time: 11:56
 */

namespace NoteFraisBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Image;
use AppBundle\Entity\Imputation;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\NdfCategorie;
use AppBundle\Entity\NdfCategorieDossier;
use AppBundle\Entity\NdfContact;
use AppBundle\Entity\NdfDepense;
use AppBundle\Entity\NdfDepenseContact;
use AppBundle\Entity\NdfDepenseFraisKm;
use AppBundle\Entity\NdfDepenseTva;
use AppBundle\Entity\NdfFraisKilometrique;
use AppBundle\Entity\NdfNote;
use AppBundle\Entity\NdfSouscategorieDossier;
use AppBundle\Entity\Saisie1;
use AppBundle\Entity\Saisie2;
use AppBundle\Entity\SaisieControle;
use AppBundle\Entity\Separation;
use AppBundle\Entity\Vehicule;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Acl\Exception\Exception;

class NoteFraisDepenseController extends Controller
{


    function depenseFKEditAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $vehicules = $this->getDoctrine()
                ->getRepository('AppBundle:Vehicule')
                ->findBy(array('dossier' => $dossier));


            $notes = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->findBy(array('dossier' => $dossier));

            $affaires = $this->getDoctrine()
                ->getRepository('AppBundle:NdfAffaire')
                ->findBy(array('dossier' => $dossier));

            $depenseFKId = Boost::deboost($post->get('depenseFkId'), $this);

            $depenseFK = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepenseFraisKm')
                ->find($depenseFKId);

            $noteId = Boost::deboost($post->get('noteId'), $this);

            $note = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->find($noteId);

            $affaireId = Boost::deboost($post->get('affaire_id'), $this);

            $affaire = $this->getDoctrine()
                ->getRepository('AppBundle:NdfAffaire')
                ->find($affaireId);

            //Affichage modal
            if ($json == 0) {

                return $this->render('NoteFraisBundle:Depense:depenseFraisKmEdit.html.twig', array(
                    'vehicules' => $vehicules,
                    'notes' => $notes,
                    'affaires' => $affaires,
                    'depenseFk' => $depenseFK,
                    'note' => $note
                ));
            }

            //Sauvegarde
            $em = $this->getDoctrine()
                ->getEntityManager();

            $titre = $post->get('titre');

            $periodeDu = $post->get('periodeDu');
            $newPeriodeDu = '';

            if ($periodeDu != '') {

                $date_array = explode("/", $periodeDu);
                $var_day = $date_array[0];
                $var_month = $date_array[1];
                $var_year = $date_array[2];
                $newPeriodeDu = "$var_year-$var_month-$var_day";
            }

            if ($newPeriodeDu != '') {
                $newPeriodeDu = (new \DateTime($newPeriodeDu));
            } else {
                $newPeriodeDu = null;
            }

            $periodeAu = $post->get('periodeAu');
            $newPeriodeAu = '';

            if ($periodeAu != '') {

                $date_array = explode("/", $periodeAu);
                $var_day = $date_array[0];
                $var_month = $date_array[1];
                $var_year = $date_array[2];
                $newPeriodeAu = "$var_year-$var_month-$var_day";
            }

            if ($newPeriodeAu != '') {
                $newPeriodeAu = (new \DateTime($newPeriodeAu));
            } else {
                $newPeriodeAu = null;
            }


            $veh = null;
            $vehiculeId = Boost::deboost($post->get('vehiculeId'), $this);
            $veh = $this->getDoctrine()
                ->getRepository('AppBundle:Vehicule')
                ->find($vehiculeId);

            $depart = $post->get('depart');
            if ($depart == '') {
                $depart = null;
            }

            $arrivee = $post->get('arrivee');
            if ($arrivee == '') {
                $arrivee = null;
            }

            $trajet = $post->get('trajet');
            if ($trajet == '') {
                $trajet = null;
            }

            $description = $post->get('description');
            if ($description == '') {
                $description = null;
            }

            $ttc = $post->get('ttc');
            if (!is_numeric($ttc)) {
                $ttc = null;
            }

            $facturable = ($post->get('aFacturer') === 'true' || $post->get('aFacturer') === 'on') ? 1 : 0;

            $depLat = $post->get('depLat');
            if ($depLat == '') {
                $depLat = null;
            }

            $depLng = $post->get('depLng');
            if ($depLng == '') {
                $depLng = null;
            }

            $arrLat = $post->get('arrLat');
            if ($arrLat == '') {
                $arrLat = null;
            }
            $arrLng = $post->get('arrLng');
            if ($arrLng == '') {
                $arrLng = null;
            }

            $isInPeriode = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepenseFraisKm')
                ->isInPeriode($note, $post->get('periodeDu'), $post->get('periodeAu'));

            if (!$isInPeriode) {
                return new JsonResponse(array(
                    'noteId' => '',
                    'noteLibelle' => '',
                    'errMsg' => 'La date ne correspond pas à la période de la note'));
            }

            //Mise a jour
            if ($depenseFK) {

                $depenseFK->setTitre($titre);
                $depenseFK->setVehicule($veh);
                $depenseFK->setPeriodeDeb($newPeriodeAu);
                $depenseFK->setPeriodeFin($newPeriodeDu);
                $depenseFK->setDepart($depart);
                $depenseFK->setArrivee($arrivee);
                $depenseFK->setTrajet($trajet);
                $depenseFK->setDescription($description);
                $depenseFK->setFacturable($facturable);
                $depenseFK->setTtc($ttc);
                $depenseFK->setNdfNote($note);
                $depenseFK->setNdfAffaire($affaire);

                $depenseFK->setDepartLat($depLat);
                $depenseFK->setDepartLong($depLng);
                $depenseFK->setArriveeLat($arrLat);
                $depenseFK->setArriveeLong($arrLng);

                $em->flush();
            } //Ajout
            else {

                $depenseFK = new NdfDepenseFraisKm();

                $depenseFK->setDossier($dossier);

                $depenseFK->setTitre($titre)
                    ->setVehicule($veh)
                    ->setPeriodeDeb($newPeriodeDu)
                    ->setPeriodeFin($newPeriodeDu)
                    ->setDepart($depart)
                    ->setArrivee($arrivee)
                    ->setTrajet($trajet)
                    ->setDescription($description)
                    ->setFacturable($facturable)
                    ->setTtc($ttc)
                    ->setNdfNote($note)
                    ->setNdfAffaire($affaire)
                    ->setDepartLat($depLat)
                    ->setDepartLong($depLng)
                    ->setArriveeLat($arrLat)
                    ->setArriveeLong($arrLng);


                $em->persist($depenseFK);
                $em->flush();

            }

            $noteId = null;
            $noteLibelle = '';
            $reloadNoteSelect = false;

            if (!is_null($depenseFK->getNdfNote())) {
                $noteId = Boost::boost($depenseFK->getNdfNote()->getId());
                $noteLibelle = $depenseFK->getNdfNote()->getLibelle();

                $ndfUtilisateur = $depenseFK->getNdfNote()
                    ->getNdfUtilisateur();

                $annee = $depenseFK->getNdfNote()
                    ->getAnnee();

                if (!is_null($ndfUtilisateur) && !is_null($annee)) {
                    $depenseFKs = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfDepenseFraisKm')
                        ->getListDepenseFraisKmByNdfUtilisateur($ndfUtilisateur, $annee);


                    $vehicules = array();

                    foreach ($depenseFKs as $depenseFK) {

                        if (!in_array($depenseFK->getVehicule(), $vehicules) && !is_null($depenseFK->getVehicule())) {
                            $vehicules[] = $depenseFK->getVehicule();
                        }
                    }


                    $diff = 0;

                    foreach ($vehicules as $vehicule) {
                        $distance = 0;
                        $sommeTtc = 0;

                        foreach ($depenseFKs as $depenseFK) {
                            if ($depenseFK->getVehicule() == $vehicule) {
                                $distance += $depenseFK->getTrajet();
                                $sommeTtc += $depenseFK->getTtc();
                            }
                        }

                        //calcul IK par rapport à la distance
                        $sommeRegul = $this->getDoctrine()
                            ->getRepository('AppBundle:NdfFraisKilometrique')
                            ->calculFraisKm($note->getAnnee(), $vehicule->getTypeVehicule()->getId(), $vehicule->getNbCv(), $distance);


                        if (abs($sommeTtc - $sommeRegul) > 1) {
                            $diff += $sommeTtc - $sommeRegul;
                        }
                    }

                    //Jerena raha efa misy regul
                    $noteRegul = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfNote')
                        ->findBy(array(
                            'ndfUtilisateur' => $ndfUtilisateur,
                            'regul' => 1,
                            'annee' => $annee));

                    $depenseRegul = array();

                    if (count($noteRegul) > 0) {
                        $depenseRegul = $this->getDoctrine()
                            ->getRepository('AppBundle:NdfDepenseFraisKm')
                            ->getRegulDepenseFraisKmByNdfUtilisateur($ndfUtilisateur, $annee);
                    }

                    if ($diff == 0) {
                        if (count($depenseRegul) > 0) {
                            $depReg = $depenseRegul[0];

                            $em->remove($depReg);
                            $em->flush();


                            $noteReg = $noteRegul[0];

                            $idNoteReg = Boost::boost($noteReg->getId());

                            $em->remove($noteReg);
                            $em->flush();

                            $reloadNoteSelect = true;

                        }
                    } else {
                        if (count($depenseRegul) > 0) {
                            /** @var NdfDepenseFraisKm $depReg */
                            $depReg = $depenseRegul[0];

                            $depReg->setTtc($diff);

                            $em->flush();

                        } else {

                            $depReg = null;

                            if (count($noteRegul) > 0) {
                                $noteReg = $noteRegul[0];
                            } else {
                                $noteReg = new NdfNote();
                                $noteReg->setDossier($dossier);
                                $noteReg->setNdfUtilisateur($ndfUtilisateur);
                                $noteReg->setLibelle('Régularisation IK ' . $annee);
                                $noteReg->setAnnee($annee);
                                $noteReg->setMois('1,2,3,4,5,6,7,8,9,10,11,12');
                                $noteReg->setRegul(1);

                                $em->persist($noteReg);
                                $em->flush();

                                $reloadNoteSelect = true;

                            }

                            $depReg = new NdfDepenseFraisKm();
                            $depReg->setNdfNote($noteReg);
                            $depReg->setDossier($dossier);

                            $depReg->setTitre('Régularisation IK ' . $annee);

                            $depReg->setPeriodeDeb(\DateTime::createFromFormat('d/m/Y', '01/01/' . $annee));
                            $depReg->setPeriodeFin(\DateTime::createFromFormat('d/m/Y', '31/12/' . $annee));

                            $depReg->setRegul(1);

                            $depReg->setTtc($diff);


                            $em->persist($depReg);
                            $em->flush();

                        }
                    }

                }
            }

            $noteLi = '';
            if ($reloadNoteSelect) {


                $noteLi .= '<li data-id="-1"><a href="#" class="depense-note-filtre">Toutes les notes</a></li>';

                $notes = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfNote')
                    ->findBy(array('dossier' => $dossier));

                foreach ($notes as $note) {
                    $noteLi .= '<li data-id="' . Boost::boost($note->getId()) . '">
                                <a href="#" class="depense-note-filtre">' . $note->getLibelle() . '</a>
                            </li>';
                }
            }


            return new JsonResponse(array(
                'noteId' => $noteId,
                'noteLibelle' => $noteLibelle,
                'errMsg' => '',
                'noteLi' => $noteLi
            ));
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }

    function depenseEditAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            /** @var NdfCategorie[] $categories */
            $categories = $this->getDoctrine()
                ->getRepository('AppBundle:NdfCategorie')
                ->getNdfCategorieActifByDossier($dossier);

            $sousCategoriesDossier = $this->getDoctrine()
                ->getRepository('AppBundle:NdfSouscategorieDossier')
                ->findBy(array('dossier' => $dossier, 'status' => 1), array('libelle' => 'ASC'));


            $modeReglements = $this->getDoctrine()
                ->getRepository('AppBundle:ModeReglement')
                ->findAll();

            $payss = $this->getDoctrine()
                ->getRepository('AppBundle:Pays')
                ->findAll();

            $devises = $this->getDoctrine()
                ->getRepository('AppBundle:Devise')
                ->findAll();

            $notes = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->findBy(array('dossier' => $dossier));


            $affaires = $this->getDoctrine()
                ->getRepository('AppBundle:NdfAffaire')
                ->findBy(array('dossier' => $dossier));

            $tvaTauxs = $this->getDoctrine()
                ->getRepository('AppBundle:TvaTaux')
                ->findBy(array('actif' => 1), array('taux' => 'ASC'));


            /** @var NdfContact[] $contacts */
            $contacts = $this->getDoctrine()
                ->getRepository('AppBundle:NdfContact')
                ->findBy(array('dossier' => $dossier));


            $depenseId = Boost::deboost($post->get('depenseId'), $this);

            $depense = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepense')
                ->find($depenseId);


            $noteId = Boost::deboost($post->get('noteId'), $this);

            $note = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->find($noteId);


            //Affichage modal
            if ($json == 0) {

                return $this->render('NoteFraisBundle:Depense:depenseEdit.html.twig', array(
                    'depense' => $depense,
                    'categories' => $categories,
                    'sousCategoriesDossier' => $sousCategoriesDossier,
                    'modeReglements' => $modeReglements,
                    'payss' => $payss,
                    'devises' => $devises,
                    'notes' => $notes,
                    'affaires' => $affaires,
                    'contacts' => $contacts,
                    'tvaTauxs' => $tvaTauxs,
                    'note' => $note
                ));
            }


            //Sauvegarde
            $em = $this->getDoctrine()
                ->getEntityManager();

            $titre = $post->get('titre');

            $date = $post->get('date');

            $newDate = '';

            if ($date != '') {

                $date_array = explode("/", $date);
                $var_day = $date_array[0];
                $var_month = $date_array[1];
                $var_year = $date_array[2];
                $newDate = "$var_year-$var_month-$var_day";
            }

            if ($newDate != '') {
                $newDate = (new \DateTime($newDate));
            } else {
                $newDate = null;
            }

            $categorie = null;

            $sousCategorie = null;
            $sousCategorieId = $post->get('sousCategorie');
            if ($sousCategorieId != '') {
                $sousCategorie = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfSouscategorieDossier')
                    ->find($sousCategorieId);
            }

            $typeReglement = null;
            $typeReglementId = $post->get('typeReglement');
            if ($typeReglementId != '') {
                $typeReglement = $typeReglementId;
            }

            $modeReglement = null;

            $modeReglementId = $post->get('modeReglement');
            if ($modeReglementId != '') {
                $modeReglement = $this->getDoctrine()
                    ->getRepository('AppBundle:ModeReglement')
                    ->find($modeReglementId);
            }


            $pays = null;
            $paysId = $post->get('pays');
            if ($paysId != '') {
                $pays = $this->getDoctrine()
                    ->getRepository('AppBundle:Pays')
                    ->find($paysId);
            }

            $ttc = null;
            $ttcId = $post->get('ttc');
            if (is_numeric($ttcId)) {
                $ttc = $ttcId;
            }


            $devise = null;
            $deviseId = $post->get('devise');
            if ($deviseId != '') {
                $devise = $this->getDoctrine()
                    ->getRepository('AppBundle:Devise')
                    ->find($deviseId);
            }

            $remboursable = ($post->get('aRembourser') === 'true') ? 1 : 0;
            $facturable = ($post->get('aFacturer') === 'true') ? 1 : 0;
            $pj = ($post->get('pj') === 'true') ? 1 : 0;

            $affaireId = Boost::deboost($post->get('affaire'), $this);

            $affaire = $this->getDoctrine()
                ->getRepository('AppBundle:NdfAffaire')
                ->find($affaireId);

            $tvaTaux = $post->get('tvaTaux');

            $contactFroms = $post->get('contact');


            $isInPeriode = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepense')
                ->isInPeriode($note, $post->get('date'));

            if (!$isInPeriode) {
                return new JsonResponse(array('noteId' => '', 'noteLibelle' => '', 'errMsg' => 'La date ne correspond pas à la période de la note'));
            }

            //Mise a jour
            if (null !== $depense) {

                /////////////////////////TVA TAUX
                if (count($tvaTaux) > 0 && $tvaTaux != '') {

                    $tvaTauxDepenses = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfDepenseTva')
                        ->findBy(array('ndfDepense' => $depense));

                    if (count($tvaTauxDepenses) > 0) {
                        foreach ($tvaTauxDepenses as $ttd) {
                            $em->remove($ttd);
                            $em->flush();
                        }
                    }

                    if ($tvaTaux != null) {
                        foreach ($tvaTaux as $taux) {

                            if ($taux != '') {

                                $newTvaTauxDep = $this->getDoctrine()
                                    ->getRepository('AppBundle:TvaTaux')
                                    ->find(intval($taux));

                                $tvaDep = new NdfDepenseTva();
                                $tvaDep->setNdfDepense($depense);
                                $tvaDep->setTvaTaux($newTvaTauxDep);

                                $em->persist($tvaDep);
                                $em->flush();
                            }
                        }
                    }
                } else {
                    $tvaTauxDepenses = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfDepenseTva')
                        ->findBy(array('ndfDepense' => $depense));

                    foreach ($tvaTauxDepenses as $ttd) {
                        $em->remove($ttd);
                    }
                }

                /////////////////////////CONTACTS
                if (count($contactFroms) > 0 && $contactFroms != '') {

                    $depenseContacts = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfDepenseContact')
                        ->findBy(array('ndfDepense' => $depense));

                    if (count($depenseContacts) > 0) {
                        foreach ($depenseContacts as $dc) {
                            $em->remove($dc);
                            $em->flush();
                        }
                    }

                    if ($contactFroms != null) {
                        foreach ($contactFroms as $contactFrom) {

                            if ($contactFrom != '') {

                                $newContact = $this->getDoctrine()
                                    ->getRepository('AppBundle:NdfContact')
                                    ->find(intval($contactFrom));

                                $depenseContact = new NdfDepenseContact();
                                $depenseContact->setNdfDepense($depense);
                                $depenseContact->setNdfContact($newContact);

                                $em->persist($depenseContact);
                                $em->flush();
                            }
                        }
                    }
                } else {
                    $tvaTauxDepenses = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfDepenseTva')
                        ->findBy(array('ndfDepense' => $depense));

                    foreach ($tvaTauxDepenses as $ttd) {
                        $em->remove($ttd);
                    }
                }


                $depense->setTitre($titre);
                $depense->setDate($newDate);
//                $depense->setNdfCategorieDossier($categorie);
                $depense->setNdfSouscategorieDossier($sousCategorie);
                $depense->setTypeReglement($typeReglement);
                $depense->setModeReglement($modeReglement);
                $depense->setPays($pays);
                $depense->setTtc($ttc);
                $depense->setDevise($devise);
                $depense->setNdfNote($note);
                $depense->setRemboursable($remboursable);
                $depense->setFacturable($facturable);
                $depense->setNdfAffaire($affaire);
                $depense->setPj($pj);

                /**
                 *
                 * affaire:affaire
                 */

                $em->flush();


                //Mise à jour any @table note de frais
                if (null !== $depense->getImage()) {
                    $saisie1s = $this->getDoctrine()
                        ->getRepository('AppBundle:Saisie1NoteFrais')
                        ->findBy(array('image' => $depense->getImage()));

                    //Insertion
                    if (count($saisie1s) > 0) {
                        $saisie1 = $saisie1s[0];

                        $saisie1->setTtc($ttc);
                        $saisie1->setDate($newDate);
                        $saisie1->setDescription($depense->setTitre());

                    } //Mise à jour
                    else {

                    }
                }


            } //Ajout
            else {

                $depense = new NdfDepense();

                $depense->setDossier($dossier);

                $depense->setTitre($titre);
                $depense->setDate($newDate);
//                $depense->setNdfCategorieDossier($categorie);
                $depense->setNdfSouscategorieDossier($sousCategorie);
                $depense->setTypeReglement($typeReglement);
                $depense->setModeReglement($modeReglement);
                $depense->setPays($pays);
                $depense->setTtc($ttc);
                $depense->setDevise($devise);
                $depense->setNdfNote($note);
                $depense->setRemboursable($remboursable);
                $depense->setFacturable($facturable);
                $depense->setNdfAffaire($affaire);
                $depense->setPj($pj);

                /**
                 *
                 * affaire:affaire,
                 */

                $em->persist($depense);
                $em->flush();

                if (count($contactFroms) > 0) {
                    if ($contactFroms != null) {
                        foreach ($contactFroms as $contactFrom) {
                            $newContact = $this->getDoctrine()
                                ->getRepository('AppBundle:NdfContact')
                                ->find((int)$contactFrom);


                            $depenseContact = new NdfDepenseContact();
                            $depenseContact->setNdfDepense($depense);
                            $depenseContact->setNdfContact($newContact);

                            $em->persist($depenseContact);
                            $em->flush();
                        }
                    }
                }

                if (count($tvaTaux) > 0) {

                    if ($tvaTaux != null) {
                        foreach ($tvaTaux as $taux) {

                            if ($taux != '') {

                                $newTvaTauxDep = $this->getDoctrine()
                                    ->getRepository('AppBundle:TvaTaux')
                                    ->find((int)$taux);

                                $tvaDep = new NdfDepenseTva();
                                $tvaDep->setNdfDepense($depense);
                                $tvaDep->setTvaTaux($newTvaTauxDep);

                                $em->persist($tvaDep);
                            }
                        }
                    }

                    $em->flush();
                }
            }


            $noteId = null;
            $noteLibelle = '';

            if (null !== $depense->getNdfNote()) {
                $noteId = Boost::boost($depense->getNdfNote()->getId());
                $noteLibelle = $depense->getNdfNote()->getLibelle();
            }
            return new JsonResponse(array(
                'noteId' => $noteId,
                'noteLibelle' => $noteLibelle,
                'errMsg' => ''));
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    function depenseFiltreAction(Request $request, $json)
    {
        if($request->isXmlHttpRequest()) {

            $post = $request->request;
            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if ($json == 0) {


                $categories = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfCategorie')
                    ->findAll();


                $sousCategories = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfSouscategorieDossier')
                    ->findBy(array('dossier' => $dossier, 'status' => 1),
                        array('libelle' => 'ASC'));


                $notes = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfNote')
                    ->findBy(array(), array('libelle' => 'ASC'));

                $affaires = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfAffaire')
                    ->findBy(array('status' => 1), array('libelle' => 'ASC'));


                return $this->render('NoteFraisBundle:Depense:filtre.html.twig', array(
                    'sousCategoriesDossier' => $sousCategories,
                    'categories' => $categories,
                    'notes' => $notes,
                    'affaires' => $affaires
                ));
            }

            $post = $request->request;

            $titre = $post->get('titre');
            $remboursable = $post->get('remboursable');
            $facturable = $post->get('facturable');

            $sousCategorieId = Boost::deboost($post->get('sousCategorieId'), $this);
            $sousCategorie = $this->getDoctrine()
                ->getRepository('AppBundle:NdfSouscategorieDossier')
                ->find($sousCategorieId);

            $noteId = Boost::deboost($post->get('noteId'), $this);
            $note = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->find($noteId);

            $affaireId = Boost::deboost($post->get('affaireId'), $this);
            $affaire = $this->getDoctrine()
                ->getRepository('AppBundle:NdfAffaire')
                ->find($affaireId);


            $dateDu = $post->get('dateDu');
            $dateDu = str_replace(" ", "", $dateDu);

            $newDateDu = '';

            if ($dateDu != '') {

                $date_array = explode("/", $dateDu);
                $var_day = $date_array[0];
                $var_month = $date_array[1];
                $var_year = $date_array[2];
                $newDateDu = "$var_year-$var_month-$var_day";
                $newDateDu = str_replace(" ", "", $newDateDu);
            }

            if ($newDateDu != '') {
                $newDateDu = (new \DateTime($newDateDu));
            } else {
                $newDateDu = null;
            }


            $dateAu = $post->get('dateAu');
            $dateAu = str_replace(" ", "", $dateAu);

            $newDateAu = '';

            if ($dateAu != '') {

                $date_array = explode("/", $dateAu);
                $var_day = $date_array[0];
                $var_month = $date_array[1];
                $var_year = $date_array[2];
                $newDateAu = "$var_year-$var_month-$var_day";
                $newDateAu = str_replace(" ", "", $newDateAu);
            }

            if ($newDateAu != '') {
                $newDateAu = (new \DateTime($newDateAu));
            } else {
                $newDateAu = null;
            }

            $depenses = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepense')
                ->getDepenseByFilter($dossier, $titre, $remboursable, $facturable, $newDateDu, $newDateAu, $sousCategorie, $affaire, $note);

            $detailDepenses = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepense')
                ->getDetailsDepense($depenses);

            $depenseFKs = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepenseFraisKm')
                ->getDepenseFraisKmByFilter($dossier, $titre, $remboursable, $facturable, $newDateDu, $newDateAu, $sousCategorie, $affaire, $note);

            return $this->render('NoteFraisBundle:Depense:depenseTable.html.twig', array(
                'detailDepenses' => $detailDepenses,
                'depenseFKs' => $depenseFKs));
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    function depenseDeleteAction(Request $request)
    {

        $post = $request->request;

        $depenseId = Boost::deboost($post->get('id'), $this);

        $dataType = $post->get('data_type');

        $em = $this->getDoctrine()
            ->getEntityManager();

        if ($dataType == 0) {
            $depense = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepense')
                ->find($depenseId);


            if (null !== $depense) {

                $tvaTauxDepenses = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfDepenseTva')
                    ->findBy(array('ndfDepense' => $depense));

                foreach ($tvaTauxDepenses as $tvaTauxDepense) {
                    $em->remove($tvaTauxDepense);

                }

                $em->remove($depense);
                $em->flush();

                return new JsonResponse('Dépense supprimé');
            }

            return new JsonResponse('Dépense introuvable');
        }

        if ($dataType == 1) {

            $depenseFK = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepenseFraisKm')
                ->find($depenseId);

            if (null !== $depenseFK) {
                $em->remove($depenseFK);
                $em->flush();
                return new JsonResponse('Dépense FK Supprimé');
            }

            return new JsonResponse('Dépense FK introuvable');
        }

        return new JsonResponse(-1);
    }

    function depenseDupliquerAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            //datatype: depense ou FK
            $dataType = $post->get('dataType');

            $em = $this->getDoctrine()
                ->getEntityManager();


            $ret = array('noteId' => 0);

            if ($dataType == 0) {

                $depenseId = Boost::deboost($post->get('depenseId'), $this);

                $depense = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfDepense')
                    ->find($depenseId);

                if (null !== $depense) {


                    $duplication = new NdfDepense();

                    $duplication->setDossier($depense->getDossier());
                    $duplication->setTitre($depense->getTitre());
                    $duplication->setDate($depense->getDate());
//                $duplication->setNdfCategorieDossier($depense->getNdfCategorieDossier());
                    $duplication->setNdfSouscategorieDossier($depense->getNdfSouscategorieDossier());
                    $duplication->setModeReglement($depense->getModeReglement());
                    $duplication->setPays($depense->getPays());
                    $duplication->setTtc($depense->getTtc());
                    $duplication->setDevise($depense->getDevise());
                    $duplication->setNdfNote($depense->getNdfNote());
                    $duplication->setDescription($depense->getDescription());
                    $duplication->setNdfAffaire($depense->getNdfAffaire());

                    $em->persist($duplication);
                    $em->flush();


                    $tvaTauxs = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfDepenseTva')
                        ->findBy(array('ndfDepense' => $depense));


                    foreach ($tvaTauxs as $tvaTaux) {
                        $duplicationTva = new NdfDepenseTva();
                        $duplicationTva->setNdfDepense($tvaTaux->getNdfDepense());
                        $duplicationTva->setTvaTaux($tvaTaux->getTvaTaux());

                        $em->persist($duplicationTva);
                        $em->flush();

                    }

                    $contacts = $this->getDoctrine()
                        ->getRepository('AppBundle:NdfDepenseContact')
                        ->findBy(array('ndfDepense' => $depense));

                    foreach ($contacts as $contact) {
                        $duplicationContact = new NdfDepenseContact();
                        $duplicationContact->setNdfDepense($contact->getNdfDepense());
                        $duplicationContact->setNdfContact($contact->getNdfContact());

                        $em->persist($duplicationContact);
                        $em->flush();
                    }


                    if (null !== $duplication->getNdfNote()) {
                        $ret = array('noteId' => Boost::boost($duplication->getNdfNote()->getId()));
                    }
                }

                return new JsonResponse($ret);
            }

            if ($dataType == 1) {

                $ret = array('note' => 0);

                $depenseFKId = Boost::deboost($post->get('depenseId'), $this);

                $depenseFK = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfDepenseFraisKm')
                    ->find($depenseFKId);


                if (null !== $depenseFK) {
                    $duplicationFk = new NdfDepenseFraisKm();

                    $duplicationFk->setVehicule($depenseFK->getVehicule());
                    $duplicationFk->setDossier($depenseFK->getDossier());
                    $duplicationFk->setTitre($depenseFK->getTitre());
                    $duplicationFk->setPeriodeDeb($depenseFK->getPeriodeDeb());
                    $duplicationFk->setPeriodeFin($depenseFK->getPeriodeFin());
                    $duplicationFk->setDepart($depenseFK->getDepart());
                    $duplicationFk->setArrivee($depenseFK->getArrivee());
                    $duplicationFk->setTrajet($depenseFK->getTrajet());
                    $duplicationFk->setNdfNote($depenseFK->getNdfNote());
                    $duplicationFk->setDescription($depenseFK->getDescription());
                    $duplicationFk->setFacturable($depenseFK->getFacturable());
                    $duplicationFk->setTtc($depenseFK->getTtc());
                    $duplicationFk->setNdfAffaire($depenseFK->getNdfAffaire());

                    $em->persist($duplicationFk);
                    $em->flush();

                    if (null !== $duplicationFk->getNdfNote()) {
                        $ret = array('noteId' => Boost::boost($duplicationFk->getNdfNote()->getId()));
                    }
                }

                return new JsonResponse($ret);

            }

            return new JsonResponse(-1);
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    function depenseTableauAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $noteId = Boost::deboost($request->request->get('noteId'), $this);

            $note = $this->getDoctrine()
                ->getRepository('AppBundle:NdfNote')
                ->find($noteId);

            if (null !== $note) {
                $depenses = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfDepense')
                    ->findBy(array('ndfNote' => $note));

                $depenseFKs = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfDepenseFraisKm')
                    ->findBy(array('ndfNote' => $note));
            } else {
                $depenses = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfDepense')
                    ->findBy(array('dossier' => $dossier));

                $depenseFKs = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfDepenseFraisKm')
                    ->findBy(array('dossier' => $dossier));
            }

            $detailDepenses = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepense')
                ->getDetailsDepense($depenses);

            return $this->render('NoteFraisBundle:Depense:depenseTable.html.twig', array(
                'detailDepenses' => $detailDepenses,
                'depenseFKs' => $depenseFKs));
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    function depenseTvaTauxAction(Request $request)
    {
        $post = $request->request;
        $depenseId = Boost::deboost($post->get('depenseId'), $this);

        $depense = $this->getDoctrine()
            ->getRepository('AppBundle:NdfDepense')
            ->find($depenseId);

        $resulat = array();


        if (null !== $depense) {
            $tvaTauxDepenses = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepenseTva')
                ->findBy(array('ndfDepense' => $depense));

            foreach ($tvaTauxDepenses as $tvaTauxDepense) {
                $resulat[] = $tvaTauxDepense->getTvaTaux()->getId();
            }

        }
        return new JsonResponse($resulat);

    }

    function depenseContactAction(Request $request)
    {
        $post = $request->request;
        $depenseId = Boost::deboost($post->get('depenseId'), $this);

        $depense = $this->getDoctrine()
            ->getRepository('AppBundle:NdfDepense')
            ->find($depenseId);

        $resulat = array();


        if (null !== $depense) {
            $contactDepenses = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepenseContact')
                ->findBy(array('ndfDepense' => $depense));

            foreach ($contactDepenses as $contactDepense) {
                $resulat[] = $contactDepense->getNdfContact()->getId();
            }

        }
        return new JsonResponse($resulat);

    }

    function depenseTarificationAction(Request $request)
    {
        $post = $request->request;

        $vehiculeId = Boost::deboost($post->get('vehicule_id'), $this);
        $trajet = $post->get('trajet');

        $vehicule = $this->getDoctrine()
            ->getRepository('AppBundle:Vehicule')
            ->find($vehiculeId);

        $tarif = 0;

        if (null !== $vehicule) {

            if (null !== $vehicule->getNdfTypeVehicule()) {
                $tarif = $this->getDoctrine()
                    ->getRepository('AppBundle:NdfFraisKilometrique')
                    ->calculFraisKm(2017,$vehicule->getTypeVehicule()->getId(),$vehicule->getNbCv(), $trajet);
            }
        }
        return new JsonResponse($tarif);
    }

    function comboVehiculeAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            $vehicules = $this->getDoctrine()
                ->getRepository('AppBundle:Vehicule')
                ->findBy(array('dossier' => $dossier));

            $ret = '<option></option>';
            foreach ($vehicules as $vehicule) {

                $veh = ($vehicule->getVehiculeMarque() == null) ? $vehicule->getVehiculeMarque()->getLibelle() : '';
                $veh .= ' ' . $vehicule->getModele() . ' ' . $vehicule->getImmatricule();

                $ret .= '<option value="' . Boost::boost($vehicule->getId()) . '">' . $veh . '</option>';
            }

            return new Response($ret);
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }

    function deviseAction(Request $request)
    {

        $post = $request->request;
        $deviseId = $post->get('deviseId');

        $devise = null;

        if (null !== $deviseId) {
            $devise = $this->getDoctrine()
                ->getRepository('AppBundle:Devise')
                ->find($deviseId);
        }

        $date = $post->get('date');
        $newDate = '';
        if ($date != '') {


                $date_array = explode("/", $date);

                if(count($date_array) > 2) {
                    $var_day = $date_array[0];
                    $var_month = $date_array[1];
                    $var_year = $date_array[2];
                    $newDate = "$var_year-$var_month-$var_day";
                }


        }

        if ($newDate == '') {
            $current = new \DateTime();
            $newDate = $current->format('Y') . '-01-01';
        }

        $newDate = str_replace(" ", "", $newDate);

        $montant = $post->get('montant');
        $taux = 1;
        $res = 0;

        if (null !== $devise) {
            $tauxDevise = $this->getDoctrine()
                ->getRepository('AppBundle:DeviseTaux')
                ->getTauxByDate($devise->getId(), $newDate);


            if (count($tauxDevise) == 1) {
                $taux = $tauxDevise[0]->taux;
            }
        }

        if (is_numeric($montant)) {
            $res = round(($montant / $taux), 2);
        }

        return new JsonResponse($res);
    }

    /**
     * @param Request $request
     * @param $json
     * @return JsonResponse|Response
     */
    public function depensePjAction(Request $request, $json)
    {

        if($request->isXmlHttpRequest()) {
            if ($json == 0) {
                return $this->render('NoteFraisBundle:Depense:depenseImageEnvoi.html.twig');
            }

            $post = $request->request;

            $dossierId = Boost::deboost($post->get('dossierId'), $this);

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find($dossierId);

            if (null === $dossier) {
                return new Response(-1);
            }

            $ret = array('noteId' => 0);

            $depenseId = Boost::deboost($post->get('depenseId'), $this);

            $depense = $this->getDoctrine()
                ->getRepository('AppBundle:NdfDepense')
                ->find($depenseId);

            $exercice = date('Y');
            if (null !== $depense) {

                /** @var NdfNote $note */
                $note = $depense->getNdfNote();

                if (null !== $note) {
                    if (null !== $note->getAnnee()) {
                        $exercice = $note->getAnnee();
                    }

                    $ret = array('noteId' => Boost::boost($note->getId()));
                }
            }


            $files = $request->files->get('depense_envoi');
            $source_image = null;
            $lot = $lot_urgent = null;

            $em = $this->getDoctrine()->getEntityManager();

            if (count($files) > 0) {
                $lot = $this->getDoctrine()
                    ->getRepository('AppBundle:Lot')
                    ->getNewLot($dossier, $this->getUser(), '');

                $lot->setStatus(4);
                $em->flush();

            }

            $dateScan = $lot->getDateScan()->format("Ymd");

            //directory
            $directory = "IMAGES/" . $dateScan;
            $fs = new Filesystem();
            try {
                $fs->mkdir($directory, 0777);
            } catch (IOExceptionInterface $e) {
            }


            if ($files != null) {
                foreach ($files as $file) {
                    $file_name = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $name = basename($file_name, '.' . $extension);
                    $file->move($directory, $file_name);
                    $newName = Boost::getUuid();
                    $fs->rename($directory . '/' . $file_name, $directory . '/' . $newName . '.' . $extension);

                    $image = new Image();

                    $image->setLot($lot);
                    $image->setExercice($exercice);
                    $image->setExtImage($extension);
                    $image->setNbpage(1);
                    $image->setNomTemp($newName);
                    $image->setOriginale($name);
                    $image->setSourceImage($this->getDoctrine()->getRepository('AppBundle:SourceImage')->getBySource('PICDATA'));
                    $image->setDownload(new \DateTime('now'));
                    $image->setSaisie1(3);
                    $image->setSaisie2(3);
                    $image->setCtrlSaisie(3);
                    $image->setImputation(3);
                    $image->setCtrlImputation(3);


                    $em->persist($image);

                    $em->flush();


                    if (null !== $depense) {
                        $depense->setImage($image);
                        $em->flush();
                    }

                    $soussouscategorie = null;
                    if (null !== $depense) {
                        /** @var NdfSouscategorieDossier $ndfSousCategorieDossier */
                        $ndfSousCategorieDossier = $depense->getNdfSouscategorieDossier();
                        if (null !== $ndfSousCategorieDossier) {
                            if (null !== $ndfSousCategorieDossier->getNdfSouscategorie()) {
                                $soussouscategorie = $ndfSousCategorieDossier->getNdfSouscategorie()->getSoussouscategorie();
                            }
                        }
                    }

                    $saisie1 = new Saisie1();
                    $saisie1->setImage($image);
                    $saisie1->setSoussouscategorie($soussouscategorie);


                    $em->persist($saisie1);
                    $em->flush($saisie1);


                    $saisie2 = new Saisie2();
                    $saisie2->setImage($image);
                    $saisie2->setSoussouscategorie($soussouscategorie);

                    $em->persist($saisie2);
                    $em->flush($saisie2);


                    $ctrlSaisie = new SaisieControle();
                    $ctrlSaisie->setImage($image);
                    $ctrlSaisie->setSoussouscategorie($soussouscategorie);

                    $em->persist($ctrlSaisie);
                    $em->flush($ctrlSaisie);


                    $imputation = new Imputation();
                    $imputation->setImage($image);
                    $imputation->setSoussouscategorie($soussouscategorie);

                    $em->persist($imputation);
                    $em->flush($imputation);


                    $ctrlImputation = new ImputationControle();
                    $ctrlImputation->setImage($image);
                    $ctrlImputation->setSoussouscategorie($soussouscategorie);

                    $em->persist($ctrlImputation);
                    $em->flush($ctrlImputation);


                    break;
                }

                return new JsonResponse($ret);
            }

            return new JsonResponse(-1);
        }
        throw new AccessDeniedHttpException('Accès refusé');
    }

}