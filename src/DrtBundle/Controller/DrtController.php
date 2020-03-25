<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/01/2019
 * Time: 11:14
 */

namespace DrtBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageATraiter;
use AppBundle\Entity\ResponsableCsd;
use AppBundle\Entity\EchangeItem;
use AppBundle\Entity\EchangeReponse;
use AppBundle\Entity\Emails;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Functions\CustomPdoConnection;
use Symfony\Component\Filesystem\Filesystem;

class DrtController extends Controller
{
    public $pdo;

    //initisalisation pdo

    /**
     * DrtController constructor.
     */
    public function __construct()
    {
        $con = new CustomPdoConnection();
        $this->pdo = $con->connect();
    }

    /**
     * @return Response
     */
    public function drtIndexAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:EchangeType');
        $query = $repository->createQueryBuilder('et')->where("et.nom <> ''")->orderBy('et.id', 'ASC')->getQuery();
        $echangeType = $query->getResult();
        $user = $this->getUser();
        $client_nom = $user->getClient()->getNom();
        return $this->render('DrtBundle:Drt:index.html.twig', array(
            'echangeType' => $echangeType,
            'client_nom'  => $client_nom
        ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function drtAddEchangeAction( Request $request )
    {
        $post = $request->request;
        $exercice = $post->get('exercice');
        $statut = $post->get('statut');
        $client = $post->get('client');
        $client = Boost::deboost($client, $this);
        $echange_type = $post->get('echange_type');
        $dossierId = $post->get('dossier');
        if(count(explode('-',$echange_type)) != 3){
            $dossierId = Boost::deboost($dossierId, $this);
        }else{
            $array_data_echange_type = explode('-',$echange_type);
            $echange_type = $array_data_echange_type[2];
            $echange_item = $array_data_echange_type[0];
        }
        $drt_add = $post->get('drt_add');
        $message = $post->get('message');
        $data_date = $post->get('date');
        $data_date = json_decode($data_date, true);
        $data = array();
        $data_dossier = array();
        if ( isset($dossierId) ) {
            $data_dossier = $this->getDoctrine()
                                 ->getRepository('AppBundle:Dossier')
                                 ->find($dossierId);
        }
        if ( $drt_add ) {
            $echange_type = $this->getDoctrine()
                                 ->getRepository('AppBundle:EchangeType')
                                 ->find($echange_type);

            $em = $this->getDoctrine()->getManager();

            $echange = $this->getDoctrine()->getRepository('AppBundle:Echange')
                            ->getEchangeByDossierExercice($echange_type, $data_dossier, $exercice);

            $files = $request->files->get('js_id_input_file_add_drt');

            foreach ( $files as $file ) {
                $getLastEchangeItem = $this->getDoctrine()
                                           ->getRepository('AppBundle:EchangeItem')
                                           ->getLastEchangeItem($echange->getId());

                $numero = (count($getLastEchangeItem) == 0) ? 1 : $getLastEchangeItem->numero + 1;

                $name = $echange_type->getId() . '_' . $echange->getId() . '_' . $numero;
                $extension = $file->getClientOriginalExtension();
                $fileName = $name . '.' . $extension;
                $Dir = $this->container->getParameter('kernel.root_dir') . '/../web/echange';
                $file->move($Dir, $fileName);

                if ( file_exists($Dir . '/' . $fileName) ) {
                    foreach ($data_date as $date) {
                        //enregistre nouveau DRT ou DRP si le nom est dans data_date
                        if ( $date['name'] == $file->getClientOriginalName() ) {
                            if(isset($echange_item) && !empty($echange_item)){
                                $echange_item = $this->getDoctrine()
                                                     ->getRepository('AppBundle:EchangeItem')
                                                     ->find($echange_item);
                                $new_echange_item = $this->addNewEchangeItemAction($echange_item, $message);
                            }else{
                                $echange_item = new EchangeItem();
                                $echange_item->setNumero($numero);
                                $echange_item->setNomFichier($fileName);
                                $echange_item->setStatus(0);
                                $echange_item->setEchange($echange);
                                $echange_item->setDateCreation(new \DateTime($date['date']));
                                $echange_item->setMessage($message);
                                $em->persist($echange_item);
                                $em->flush();
                            }


                            //envoi mail
                            /*$drt = $echange->getEchangeType()->getNom() . ' ' . $echange->getDossier()->getNom() . ' ' . substr($exercice, 2, 3) . ' ' . $echange_item->getNumero();
                            $this->configureSendEmail($data_dossier, $echange_item, $drt);*/
                        }else{
                            return new JsonResponse('ERROR');
                        }
                    }
                } else {
                    return new JsonResponse('ERROR');
                }
            }

            $data = array('dossier' => $post->get('dossier'), 'exercice' => $exercice);
            return new JsonResponse($data);
        }
        $data['dossier'] = array('id' => $data_dossier->getId(), 'nom' => $data_dossier->getNom());
        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function drtGetListAction( Request $request )
    {
        $idata = json_decode($request->request->get('idata'), true);
        $rows = [];
        $liste_data = [];
        $param = array();
        $param['exercice'] = $idata['exercice'];
        $param['statut'] = $idata['statut'];
        $param['dossier'] = Boost::deboost($idata['dossier'], $this);
        $param['chrono'] = $idata['chrono'];
        $param['client'] = Boost::deboost($idata['client'], $this);
        $param['echangeType'] = $idata['echangeType'];
        $periodNow = new \DateTime();

        //action form
        $actions = " ";
        $actions = $actions . "<div class='btn-group'>";
        $actions = $actions . "  <button data-toggle='dropdown' class='btn btn-primary btn-xs dropdown-toggle'>Actions <span class='caret'></span></button>";
        $actions = $actions . "  <ul class='dropdown-menu'>";
        $actions = $actions . "    <li><a class='ajout-nouvelle-echange' href='#' onclick='addNewEchangeDrt();'></a></li>";
        $actions = $actions . "    <li class='divider'></li>";
        $actions = $actions . "    <li><a class='repondre-echange-title' href='#' onclick='showRepondreDrt();'></a></li>";
        $actions = $actions . "    <li><a class='supprime-echange-title' href='#' onclick='showSupprimeDrt();'></a></li>";
        $actions = $actions . "  </ul>";
        $actions = $actions . "</div>";

        /* 1=>aujourd'hui, 2=>dépuis une semaine, 3=>deux semaine, 4=>un mois, 5=>tous exercice, 6=>deux exercice, 7=>fourchette */
        switch ( $idata['chrono'] ) {
            case 1:
                $param['cas'] = 1;
                $param['aujourdhui'] = $periodNow->format('Y-m-d');
                break;
            case 2:
                $param['cas'] = 2;
                $now = clone $periodNow;
                $oneWeek = date_modify($periodNow, "-7 days");
                $param['dateDeb'] = $oneWeek->format('Y-m-d');
                $param['dateFin'] = $now->format('Y-m-d');
                break;
            case 3:
                $param['cas'] = 3;
                $now = clone $periodNow;
                $twoWeek = date_modify($periodNow, "-14 days");
                $param['dateDeb'] = $twoWeek->format('Y-m-d');
                $param['dateFin'] = $now->format('Y-m-d');
                break;
            case 4:
                $param['cas'] = 4;
                $now = clone $periodNow;
                $oneMonth = date_modify($periodNow, "-1 months");
                $param['dateDeb'] = $oneMonth->format('Y-m-d');
                $param['dateFin'] = $now->format('Y-m-d');
                break;
            case 5:
                $param['cas'] = 5;
                break;
            case 6:
                $param['cas'] = 6;
                break;
            case 7:
                $param['cas'] = 7;
                $debPeriode = $idata['chronoDeb'];
                $finPeriode = $idata['chronoFin'];
                if ((isset($debPeriode) && !is_null($debPeriode)) && (isset($finPeriode) && !is_null($finPeriode))) {
                    $param['dateDeb'] = $debPeriode;
                    $param['dateFin'] = $finPeriode;
                }
                break;
        }
        $listes = $this->getDoctrine()
                       ->getRepository('AppBundle:Echange')
                       ->getDrt($param);
        $drt = '';
        if ( !$idata['dossierOrChrono'] ) {
            foreach ( $listes as $key => $liste ) {
                $echange_reponse_id = (empty($liste->echange_reponse_id)) ? 0 : $liste->echange_reponse_id;
                $date_creation = strtotime($liste->date_creation);
                $now = new \DateTime();
                $dateNow = strtotime($now->format('Y-m-d'));
                $jour_attente = ceil(abs($dateNow - $date_creation) / 86400);
                if ( (!empty($liste->reponse_date_envoi) && $idata['statut'] != 0 && $idata['statut'] != 5) ) {
                    if ( $liste->status == 0 ) {
                        $statut = 'Partielle';
                        $statut_stat = 2;
                    } else {
                        $statut = 'Clôturée';
                        $jour_attente = '';
                        $statut_stat = 3;
                    }
                    $liste_drt = $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero;
                    if ( $drt != $liste_drt ) {
                        $rows[] = [
                            'id' => $liste->echange_item_id.'-'.$echange_reponse_id,
                            'cell' => [
                                't-index' => $key+1,
                                't-drt' => $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero,
                                't-upload-drt' => '<i class="fa fa-download" aria-hidden="true"></i>',
                                't-e-date-envoi' => $liste->date_creation_ei,
                                't-attente' => $jour_attente,
                                't-statut' => $statut,
                                't-message-drt' => $liste->message_drt,
                                't-reponse' => 'R' . ($liste->numero_reponse) . '-' . $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero,
                                't-upload-rdrt' => '<i class="fa fa-download" aria-hidden="true"></i>',
                                't-message-rdrt' => $liste->message_rdrt,
                                't-reponse-date' => $liste->reponse_date_envoi,
                                't-actions' => $actions,
                                't-dossierId' => $liste->dossierId,
                                't-statut-stat' => $statut_stat
                            ],
                        ];
                        $drt = $liste_drt;
                    } else {
                        $rows[] = [
                            'id' => $liste->echange_item_id.'-'.$echange_reponse_id,
                            'cell' => [
                                't-index' => $key+1,
                                't-drt' => '',
                                't-upload-drt' => '',
                                't-e-date-envoi' => '',
                                't-attente' => '',
                                't-statut' => '',
                                't-message-drt' => '',
                                't-reponse' => 'R' . ($liste->numero_reponse) . '-' . $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero,
                                't-upload-rdrt' => '<i class="fa fa-download" aria-hidden="true"></i>',
                                't-message-rdrt' => $liste->message_rdrt,
                                't-reponse-date' => $liste->reponse_date_envoi,
                                't-actions' => '',
                                't-dossierId' => $liste->dossierId,
                                't-statut-stat' => $statut_stat
                            ],
                        ];
                    }

                } else if ( empty($liste->reponse_date_envoi) ) {
                    if ( $liste->status == 0 ) {
                        $statut = 'Ouverte';
                        $statut_stat = 0;
                    } else {
                        $statut = 'Clôturée';
                        $jour_attente = '';
                        $statut_stat = 3;
                    }
                    if($liste->telecharger == 1 && ($idata['statut'] == 5 || $idata['statut'] == 1 || $idata['statut'] == 3 || $idata['statut'] == 7)){
                        $statut = 'En cours';
                        $statut_stat_en_cours = 1;
                        $rows[] = [
                            'id' => $liste->echange_item_id.'-'.$echange_reponse_id,
                            'cell' => [
                                't-index' => $key+1,
                                't-drt' => $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero,
                                't-upload-drt' => '<i class="fa fa-download" aria-hidden="true"></i>',
                                't-e-date-envoi' => $liste->date_creation_ei,
                                't-attente' => $jour_attente,
                                't-statut' => $statut,
                                't-message-drt' => $liste->message_drt,
                                't-reponse' => '',
                                't-upload-rdrt' => '',
                                't-message-rdrt' => '',
                                't-reponse-date' => '',
                                't-actions' => $actions,
                                't-dossierId' => $liste->dossierId,
                                't-statut-stat' => $statut_stat_en_cours
                            ],
                        ];
                    }else if($liste->telecharger == 0 && ($idata['statut'] == 0 || $idata['statut'] == 1 || $idata['statut'] == 3 || $idata['statut'] == 7)){
                        $rows[] = [
                            'id' => $liste->echange_item_id.'-'.$echange_reponse_id,
                            'cell' => [
                                't-index' => $key+1,
                                't-drt' => $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero,
                                't-upload-drt' => '<i class="fa fa-download" aria-hidden="true"></i>',
                                't-e-date-envoi' => $liste->date_creation_ei,
                                't-attente' => $jour_attente,
                                't-statut' => $statut,
                                't-message-drt' => $liste->message_drt,
                                't-reponse' => '',
                                't-upload-rdrt' => '',
                                't-message-rdrt' => '',
                                't-reponse-date' => '',
                                't-actions' => $actions,
                                't-dossierId' => $liste->dossierId,
                                't-statut-stat' => $statut_stat
                            ],
                        ];
                    }

                }
            }
        } else {
            foreach ( $listes as $key => $liste ) {
                $echange_reponse_id = (empty($liste->echange_reponse_id)) ? 0 : $liste->echange_reponse_id;
                $date_creation = strtotime($liste->date_creation);
                $now = new \DateTime();
                $dateNow = strtotime($now->format('Y-m-d'));
                $jour_attente = ceil(abs($dateNow - $date_creation) / 86400);
                if ( !empty($liste->reponse_date_envoi) && $idata['statut'] != 0 &&  $idata['statut'] != 5) {
                    if ( $liste->status == 0 ) {
                        $statut = 'Partielle';
                        $statut_stat = 2;
                    } else {
                        $statut = 'Clôturée';
                        $jour_attente = '';
                        $statut_stat = 3;
                    }
                    $liste_drt = $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero;
                    if ( $drt != $liste_drt ) {
                        $rows[] = [
                            'id' => $liste->echange_item_id.'-'.$echange_reponse_id,
                            'cell' => [
                                't-index' => $key+1,
                                't-e-date-envoi' => $liste->date_creation_ei,
                                't-attente' => $jour_attente,
                                't-statut' => $statut,
                                't-dossier' => $liste->dossier,
                                't-drt' => $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero,
                                't-upload-drt' => '<i class="fa fa-download" aria-hidden="true"></i>',
                                't-message-drt' => $liste->message_drt,
                                't-reponse' => 'R' . ($liste->numero_reponse) . '-' . $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero,
                                't-upload-rdrt' => '<i class="fa fa-download" aria-hidden="true"></i>',
                                't-message-rdrt' => $liste->message_rdrt,
                                't-reponse-date' => $liste->reponse_date_envoi,
                                't-actions' => $actions,
                                't-dossierId' => $liste->dossierId,
                                't-statut-stat' => $statut_stat
                            ],
                        ];
                        $drt = $liste_drt;
                    } else {
                        $rows[] = [
                            'id' => $liste->echange_item_id.'-'.$echange_reponse_id,
                            'cell' => [
                                't-index' => $key+1,
                                't-e-date-envoi' => '',
                                't-attente' => '',
                                't-statut' => '',
                                't-dossier' => '',
                                't-drt' => '',
                                't-upload-drt' => '',
                                't-message-drt' => '',
                                't-reponse' => 'R' . ($liste->numero_reponse) . '-' . $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero,
                                't-upload-rdrt' => '<i class="fa fa-download" aria-hidden="true"></i>',
                                't-message-rdrt' => $liste->message_rdrt,
                                't-reponse-date' => $liste->reponse_date_envoi,
                                't-actions' => '',
                                't-dossierId' => $liste->dossierId,
                                't-statut-stat' => $statut_stat
                            ],
                        ];
                    }

                } else if ( empty($liste->reponse_date_envoi) ) {
                    if ( $liste->status == 0 ) {
                        $statut = 'Ouverte';
                        $statut_stat = 0;
                    } else {
                        $statut = 'Clôturée';
                        $jour_attente = '';
                        $statut_stat = 3;
                    }
                    if($liste->telecharger == 1 && ($idata['statut'] == 5 || $idata['statut'] == 1 || $idata['statut'] == 3 || $idata['statut'] == 7)){
                        $statut = 'En cours';
                        $statut_stat_en_cours = 1;
                        $rows[] = [
                            'id' => $liste->echange_item_id.'-'.$echange_reponse_id,
                            'cell' => [
                                't-index' => $key+1,
                                't-e-date-envoi' => $liste->date_creation_ei,
                                't-attente' => $jour_attente,
                                't-statut' => $statut,
                                't-dossier' => $liste->dossier,
                                't-drt' => $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero,
                                't-upload-drt' => '<i class="fa fa-download" aria-hidden="true"></i>',
                                't-message-drt' => $liste->message_drt,
                                't-reponse' => '',
                                't-upload-rdrt' => '',
                                't-message-rdrt' => '',
                                't-reponse-date' => '',
                                't-actions' => $actions,
                                't-dossierId' => $liste->dossierId,
                                't-statut-stat' => $statut_stat_en_cours
                            ],
                        ];
                    }else if($liste->telecharger == 0 && ($idata['statut'] == 0 || $idata['statut'] == 1 || $idata['statut'] == 3 || $idata['statut'] == 7)){
                        $rows[] = [
                            'id' => $liste->echange_item_id.'-'.$echange_reponse_id,
                            'cell' => [
                                't-index' => $key+1,
                                't-e-date-envoi' => $liste->date_creation_ei,
                                't-attente' => $jour_attente,
                                't-statut' => $statut,
                                't-dossier' => $liste->dossier,
                                't-drt' => $liste->type . ' ' . $liste->dossier . ' ' . substr($liste->exercice, 2, 3) . ' ' . $liste->numero,
                                't-upload-drt' => '<i class="fa fa-download" aria-hidden="true"></i>',
                                't-message-drt' => $liste->message_drt,
                                't-reponse' => '',
                                't-upload-rdrt' => '',
                                't-message-rdrt' => '',
                                't-reponse-date' => '',
                                't-actions' => $actions,
                                't-dossierId' => $liste->dossierId,
                                't-statut-stat' => $statut_stat
                            ],
                        ];
                    }

                }
            }
        }

        $liste_data = [
            'rows' => $rows,
        ];
        return new JsonResponse($liste_data);
    }


    /**
     * @param $dossier
     * @param $exercice
     * @param $numero_drt
     * @param $numero_reponse
     * @return Response
     */
    public function drtUploadFileAction( $dossier, $exercice, $echangeType, $numero_drt, $numero_reponse )
    {
        $echange_type = $this->getDoctrine()
                             ->getRepository('AppBundle:EchangeType')
                             ->find($echangeType);

        $data_dossier = $this->getDoctrine()
                             ->getRepository('AppBundle:Dossier')
                             ->find($dossier);

        $echange = $this->getDoctrine()
                        ->getRepository('AppBundle:Echange')
                        ->getEchangeByDossierExercice($echange_type, $data_dossier, $exercice);

        $echange_item = $this->getDoctrine()
                             ->getRepository('AppBundle:EchangeItem')
                             ->findOneBy(array(
                                 'numero' => $numero_drt,
                                 'echange' => $echange->getId()
                             ));

        $fs = new Filesystem();
        $Dir = $this->container->getParameter('kernel.root_dir') . '/../web/echange';
        $drt = $echange_type->getNom(). ' ' .$echange->getDossier()->getNom(). ' ' .substr($exercice, 2, 3). ' ' .$echange_item->getNumero();
         if ( $numero_reponse == 0 ) {
             $em = $this->getDoctrine()->getManager();
             $echange_item->setTelecharger(1);
             $em->flush();
             $fileName = $echange_item->getNomFichier();
             $extension = explode('.', $fileName)[1];
             $newFileName = $drt.'.'.$extension;
             $fs->rename($Dir.'/'.$fileName, $Dir.'/'.$newFileName);
        } else {
            $echange_reponse = $this->getDoctrine()
                                    ->getRepository('AppBundle:EchangeReponse')
                                    ->findOneBy(array(
                                        'numero' => $numero_reponse,
                                        'echangeItem' => $echange_item->getId()
                                    ));
            $fileName = $echange_reponse->getNomFichier();
            $extension = explode('.', $fileName)[1];
            $newFileName =  'R' . $numero_reponse . '-' .$drt.'.'.$extension;
            $fs->rename($Dir.'/'.$fileName, $Dir.'/'.$newFileName);
        }

        if ( file_exists($Dir . '/' . $newFileName) ) {
            $response = new Response();
            $response->headers->set('Content-Type', mime_content_type($Dir . '/' . $newFileName));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($newFileName) . '"');
            $response->headers->set('Content-Length', filesize($Dir . '/' . $newFileName));
            $response->headers->set('Pragma', "no-cache");
            $response->headers->set('Expires', "0");
            $response->headers->set('Content-Transfer-Encoding', "binary");
            $response->sendHeaders();
            $response->setContent(readfile($Dir . '/' . $newFileName));
            $fs->rename($Dir.'/'.$newFileName, $Dir.'/'.$fileName);
            return $response;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function drtAddReponseAction( Request $request )
    {
        $em = $this->getDoctrine()->getManager();
        $idata = json_decode($request->request->get('idata'), true);
        $exercice = $idata['exercice'];
        $numero = $idata['numero'];
        $echangeType = $idata['echangeType'];
        $statut = $idata['statut'];
        $client = $idata['client'];
        $client = Boost::deboost($client, $this);
        $dossier = $idata['dossier'];
        $is_reponse = $idata['is_reponse'];
        $echange_type = $this->getDoctrine()
                             ->getRepository('AppBundle:EchangeType')
                             ->find($echangeType);

        $data_dossier = $this->getDoctrine()
                             ->getRepository('AppBundle:Dossier')
                             ->find($dossier);

        $echange = $this->getDoctrine()->getRepository('AppBundle:Echange')
                        ->getEchangeByDossierExercice($echange_type, $data_dossier, $exercice);

        $echange_item = $this->getDoctrine()
                             ->getRepository('AppBundle:EchangeItem')
                             ->findOneBy(array(
                                 'numero' => $numero,
                                 'echange' => $echange->getId()
                             ));
        $drt = $echange->getEchangeType()->getNom() . ' ' . $echange->getDossier()->getNom() . ' ' . substr($exercice, 2, 3) . ' ' . $echange_item->getNumero();
        if (!$is_reponse) { //change statut
            $getLastEchangeReponse = $this->getDoctrine()
                                          ->getRepository('AppBundle:EchangeReponse')
                                          ->getLastEchangeReponse($echange_item->getId());
            /*$this->configureSendEmail($data_dossier, $echange_item, $drt, null, $statut);*/
            if($statut == 1){
                $statut = 1;
            }else{
                $statut = 0;
            }
            $echange_item->setStatus($statut); // 0=>ouverte, 1=>cloturé
            $echange_item->setTelecharger(0);
            $em->flush();

            return new JsonResponse(Boost::boost($dossier, $this));
        } else {
            $message = $idata['message'];
            $file = $request->files->get('reponse');
            $files_images = $request->files->get('image');

            $getLastEchangeReponse = $this->getDoctrine()
                                          ->getRepository('AppBundle:EchangeReponse')
                                          ->getLastEchangeReponse($echange_item->getId());
            $numero_reponse = (empty($getLastEchangeReponse)) ? 1 : $getLastEchangeReponse->numero + 1;
            $name = 'R' . $numero_reponse . '_' . $echange_type->getId() . '_' . $echange->getId() . '_' . $numero;
            $extension = $file->getClientOriginalExtension();
            $fileName = $name . '.' . $extension;
            $Dir = $this->container->getParameter('kernel.root_dir') . '/../web/echange';
            $file->move($Dir, $fileName);

            if ( file_exists($Dir . '/' . $fileName) ) {
                $echange_item->setTelecharger(0);

                $echange_reponse = new EchangeReponse();
                $echange_reponse->setNomFichier($fileName);
                $echange_reponse->setDateEnvoi(new \DateTime());
                $echange_reponse->setEchangeItem($echange_item);
                $echange_reponse->setNumero($numero_reponse);
                $echange_reponse->setMessage($message);
                $em->persist($echange_reponse);
                $em->flush();

                $directory = "IMAGES";
                $fs = new Filesystem();
                try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }
                $lot = null;
                $user = $this->getUser();
                $dateNow = new \DateTime();
                $directory .= '/'.$dateNow->format('Ymd');
                try { $fs->mkdir($directory,0777); } catch (IOExceptionInterface $e) { }
                $source = $this->getDoctrine()->getRepository('AppBundle:SourceImage')->getBySource('PICDATA');
                $lot_select = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($echange->getDossier(), $user, '');

                //enregistre reponse dans images
                $nbPage = 1;
                $file_name = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $name = basename($file_name,'.'.$extension);
                $fs->copy($Dir.'/'.$fileName, $directory.'/'.$file_name, true);
                $newName = $dateNow->format('Ymd').'_'.Boost::getUuid(50);
                $fs->rename($directory.'/'.$file_name, $directory.'/'.$newName.'.'.$extension);
                $chemin = $this->container->getParameter('kernel.root_dir') . '/../web/IMAGES/' .$lot_select->getDateScan()->format('Ymd') . '/' . $newName . '.' . $extension;

                if (strtoupper($extension) == 'PDF')
                {
                    $stream = fopen($chemin, "r");
                    $content = fread ($stream, filesize($chemin));

                    $nbPage = 1;
                    if(!(!$stream || !$content))
                    {
                        $regex = "/\/Page\W/";
                        if(preg_match_all($regex, $content, $matches)){
                            $nbPage = preg_match_all("/\/Page\W/", $content, $matches);
                        }
                    }
                }

                $image_reponse = new Image();
                $image_reponse
                    ->setLot($lot_select)
                    ->setExercice($exercice)
                    ->setExtImage($extension)
                    ->setNbpage($nbPage)
                    ->setNomTemp($newName)
                    ->setOriginale($name)
                    ->setSourceImage($source)
                    ->setEchangeReponse($echange_reponse);
                $em->persist($image_reponse);
                $em->flush();

                $image_a_traiter = new ImageATraiter();
                $image_a_traiter->setImage($image_reponse);
                $em->persist($image_a_traiter);
                $em->flush();

                //enregistre piece dans image
                if($files_images) {
                    foreach ($files_images as $file_image){
                        $file_image_name = $file_image->getClientOriginalName();
                        $extension_image = $file_image->getClientOriginalExtension();
                        $name = basename($file_image_name,'.'.$extension_image);
                        $file_image->move($directory, $file_image_name);
                        $newName = $dateNow->format('Ymd').'_'.Boost::getUuid(50);
                        $fs->rename($directory.'/'.$file_image_name, $directory.'/'.$newName.'.'.$extension_image);
                        $nbPage = 1;
                        $chemin = $this->container->getParameter('kernel.root_dir') . '/../web/IMAGES/' .$lot_select->getDateScan()->format('Ymd') . '/' . $newName . '.' . $extension_image;

                        if (strtoupper($extension_image) == 'PDF')
                        {
                            $stream = fopen($chemin, "r");
                            $content = fread ($stream, filesize($chemin));

                            $nbPage = 1;
                            if(!(!$stream || !$content))
                            {
                                $regex = "/\/Page\W/";
                                if(preg_match_all($regex, $content, $matches)){
                                    $nbPage = preg_match_all("/\/Page\W/", $content, $matches);
                                }
                            }
                        }

                        $image = new Image();
                        $image
                            ->setLot($lot_select)
                            ->setExercice($exercice)
                            ->setExtImage($extension_image)
                            ->setNbpage($nbPage)
                            ->setNomTemp($newName)
                            ->setOriginale($name)
                            ->setSourceImage($source)
                            ->setEchangeReponse($echange_reponse);
                        $em->persist($image);
                        $em->flush();
                        $image_a_traiter_piece = new ImageATraiter();
                        $image_a_traiter_piece->setImage($image);
                        $em->persist($image_a_traiter_piece);
                        $em->flush();
                    }
                }
                $lotGroup = $this->getDoctrine()->getRepository('AppBundle:LotGroup')->getNewLotGroup(1,$this->getUser(),$data_dossier);
                $lot_select->setLotGroup($lotGroup);
                $em->flush();

                $rdrt = 'R' . $numero_reponse . '-' . $drt;
                $this->configureSendEmail($data_dossier, $echange_item, $drt, $rdrt);
            } else {
                return new JsonResponse('ERROR');
            }
            $data = array('dossier' => Boost::boost($dossier, $this), 'exercice' => $exercice);
            return new JsonResponse($data);
        }
    }

    /**
     * @param Dossier $dossier
     * @param $drt
     * @param null $rdrt
     * @param null $statut
     * @return bool
     */
    public function configureSendEmail( Dossier $dossier, EchangeItem $echangeItem, $drt, $rdrt = null, $statut = null )
    {
        $client = $dossier->getSite()->getClient()->getNom();
        $site = $dossier->getSite()->getNom();
        $config = array();
        $config['copie-cache'] = '';
        $config['destinataire'] = '';
        if ( empty($rdrt) && empty($statut) ) {
            $sujet = "Réception de la " . $drt . " venant du " . $client;
            $contenu = $this->renderView('@Drt/Drt/notificationAjoutDrt.html.twig', array(
                'drt' => $drt,
                'dossier' => $dossier->getNom(),
                'client' => $client,
                'site' => $site,
                'echangeType' => explode(' ', $drt)[0],
                'utilisateur' => $this->getUser()
            ));
        } else if ( !empty($rdrt) && empty($statut) ) {
            $sujet = "Réception réponse de la " . $drt . " (" . $client . ")";
            $texte = "La <strong>" . $drt . "</strong>, du dossier <strong>" . $dossier->getNom() . "</strong> <br> et du client <strong>" . $client . "</strong>, est <strong>" . $statut . "</strong>";
            $contenu = $this->renderView('@Drt/Drt/notificationReponseDrt.html.twig', array(
                'drt' => $drt,
                'rdrt' => $rdrt,
                'dossier' => $dossier->getNom(),
                'client' => $client,
                'site' => $site,
                'echangeType' => explode(' ', $drt)[0],
                'utilisateur' => $this->getUser()
            ));
        } else {
            switch ( $statut ) {
                case 0:
                    $statut = 'Ouverte';
                    break;
                case 1:
                    $statut = 'Clôturée';
                    break;
                case 2:
                    $statut = 'Répondue partiellement';
                    break;
                case 4:
                    $statut = 'Réouverte';
                    break;
                default:
                    $statut = '';
                    break;
            }
            $sujet = "Modification statut de la " . $drt . " (" . $client . ")";
            $texte = "La <strong>" . $drt . "</strong>, du dossier <strong>" . $dossier->getNom() . "</strong> <br> et du client <strong>" . $client . "</strong>, est <strong>" . $statut . "</strong>";
            $contenu = $this->renderView('@Drt/Drt/notificationClotureDrt.html.twig', array(
                'texte' => $texte,
                'drt' => $drt,
                'utilisateur' => $this->getUser()
            ));
        }

        $smtp_client = $this->getDoctrine()
                            ->getRepository('AppBundle:Smtp')
                            ->findOneBy(array(
                                'client' => $dossier->getSite()->getClient()
                            ));
        if ( count($smtp_client) == 0 ) {
            $from_address = 'support@'.strtolower($client).'.biz';
        }else{
            $from_address = $smtp_client->getLogin();
        }

        $message = \Swift_Message::newInstance()
                                 ->setSubject($sujet)
                                 ->setFrom($from_address, explode(' ', $drt)[0]);

        $message->setBcc("dinoh@scriptura.biz");

        /** Responsable Dossier */
        $responsables = $this->getDoctrine()
                             ->getRepository('AppBundle:ResponsableCsd')
                             ->getResponsableParDossier($dossier);

        $clientsAccesDrt = $this->getDoctrine()
                             ->getRepository('AppBundle:Utilisateur')
                             ->getUtilisateurAccesDrt();

        $clientFinauxDrt = [];
        $clientFinauxMail = [];

        /** @var Utilisateur $clientAccesDrt */
        foreach ( $clientsAccesDrt as $clientAccesDrt ) {
            if ( $clientAccesDrt->email !== '' && $clientAccesDrt->email !== null ) {
                if ( !in_array($clientAccesDrt->email, $clientFinauxDrt) ) {
                    $clientFinauxDrt[] = $clientAccesDrt->email;
                    if( $clientAccesDrt->type == 6 ) { // client final
                        $clientFinauxMail[] = $clientAccesDrt->email;
                        $message->addTo($clientAccesDrt->email);
                        if( $config['destinataire'] == '' ){
                            $config['destinataire'] = $clientAccesDrt->email;
                        }else{
                            $config['destinataire'] = $config['destinataire'].';'. $clientAccesDrt->email;
                        }
                    }
                }
            }
        }

        /** @var ResponsableCsd $responsable */
        /*foreach ( $responsables as $responsable ) {
            if ( $responsable->getEmail() && $responsable->getEmail() != '' ) {
                if ( $responsable->getEnvoiMail() === 1 ) {
                    if ( in_array($responsable->getEmail(), $clientFinauxDrt) ) {
                        if( !in_array($responsable->getEmail(), $clientFinauxMail) ) {
                            $clientFinauxMail[] = $responsable->getEmail();
                            $message->addTo($responsable->getEmail());
                            if( $config['destinataire'] == '' ){
                                $config['destinataire'] = $responsable->getEmail();
                            }else{
                                $config['destinataire'] = $config['destinataire'].';'. $responsable->getEmail();
                            }
                        }
                    }
                }
            }
        }*/

        /** Responsable Site ou Client */
        /*$responsables = $this->getDoctrine()
                             ->getRepository('AppBundle:ResponsableCsd')
                             ->getResponsableSiteOuClient($dossier);*/

        /** @var ResponsableCsd site ou client */
        /*foreach ($responsables as $responsable) {
            if ($responsable->getEmail() && $responsable->getEmail() != '') {
                if($responsable->getEnvoiMail() === 1){
                    if(in_array($responsable->getEmail(), $clientFinauxDrt)){
                        if( !in_array($responsable->getEmail(), $clientFinauxMail) ) {
                            $clientFinauxMail[] = $responsable->getEmail();
                            $message->addTo($responsable->getEmail());
                            if( $config['destinataire'] == '' ){
                                $config['destinataire'] = $responsable->getEmail();
                            }else{
                                $config['destinataire'] = $config['destinataire'].';'. $responsable->getEmail();
                            }
                        }
                    }
                }
            }
        }*/

        /** Responsables Client */
        /*$responsables = $this->getDoctrine()
                             ->getRepository('AppBundle:ResponsableCsd')
                             ->getResponsableClient($dossier);*/


        /** @var ResponsableCsd client */
        /*foreach ($responsables as $responsable) {
            if ($responsable->getEmail() && $responsable->getEmail() != '') {
                if($responsable->getEnvoiMail() === 1){
                    if(in_array($responsable->getEmail(), $clientFinauxDrt)){
                        $clientFinauxMail[] = $responsable->getEmail();
                        if( !in_array($responsable->getEmail(), $clientFinauxMail) ) {
                            $clientFinauxMail[] = $responsable->getEmail();
                            $message->addTo($responsable->getEmail());
                            if( $config['destinataire'] == '' ){
                                $config['destinataire'] = $responsable->getEmail();
                            }else{
                                $config['destinataire'] = $config['destinataire'].';'. $responsable->getEmail();
                            }
                        }
                    }
                }
            }
        }*/

        /** Responsables Scriptura */
        /*$responsables = $this->getDoctrine()
                             ->getRepository('AppBundle:ResponsableCsd')
                             ->getResponsableClientScriptura($dossier->getSite()->getClient());*/

        /** @var ResponsableCsd $responsable */
        foreach ( $responsables as $responsable ) {
            if ( $responsable->getEmail() && $responsable->getEmail() != '' ) {
                if(in_array($responsable->getEmail(), $clientFinauxDrt)) {
                    if( !in_array($responsable->getEmail(), $clientFinauxMail) ) {
                        $clientFinauxMail[] = $responsable->getEmail();
                        $message->addBcc($responsable->getEmail());
                        if( $config['copie-cache'] == '' ){
                            $config['copie-cache'] = $responsable->getEmail();
                        }else{
                            $config['copie-cache'] = $config['copie-cache'] .';'.$responsable->getEmail();
                        }
                    }
                }
            }
        }

        $message->setBody($contenu, 'text/html');
        $email_statut = 0;
        if($this->get('mailer')->send($message)){
            $email_statut = 1;
        }

        /*sauvegarde contenu mail dans emails*/
        $em = $this->getDoctrine()
                   ->getManager();

        $email = new Emails();
        $email
            ->setStatus($email_statut)
            ->setContenu($contenu)
            ->setDateCreation(new \DateTime())
            ->setDossier($dossier)
            ->setFromAddress($from_address)
            ->setFromLabel(explode(' ', $drt)[0])
            ->setTypeEmail('RAPPEL_'.explode(' ', $drt)[0])
            ->setSujet($sujet)
            ->setBcc($config['copie-cache'])
            ->setToAddress($config['destinataire'])
            ->setEchangeItem($echangeItem);

        if( count($smtp_client) > 0 ){
            $email->setSmtp($smtp_client);
        }

        if( $email_statut == 1 ) {
            $email->setDateEnvoi(new \DateTime());
            $email->setNbTentativeEnvoi(1);
        }
        $em->persist($email);
        $em->flush();
        return TRUE;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function drtImportAction( Request $request )
    {
        $post = $request->request;
        $client = $post->get('client');
        $client = Boost::deboost($client, $this);
        $is_import = $post->get('is_import');
        $data_date = $post->get('date');
        $data_date = json_decode($data_date, true);
        $list_erreur = array();
        $existe = array();
        $pas_dossier = array();
        $fichier_incorrect = array();
        $data_drt = array();
        if($is_import == 0){
            $files = $request->files->get('js_id_input_file_reimport');
            $list_erreur = $post->get('list_erreur');
            $list_erreur = json_decode($list_erreur, true);
        }else{
            $files = $request->files->get('js_id_input_file_import');
        }
        $Dir = $this->container->getParameter('kernel.root_dir') . '/../web/echange';
        $annee = 2000;
        $drt_importe = 0;
        $em = $this->getDoctrine()->getManager();

        //debut importation
        foreach ( $files as $file ) {
            $file_name_original = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $file_name = basename($file_name_original,'.'.$file_extension);
            $file_name = ltrim($file_name);
            $file_name = rtrim($file_name);
            $drt_explode = array_map('trim',explode("_",$file_name));
            $drt_implode = implode('_',$drt_explode);
            if(strlen(strval($drt_explode[0])) > 3){
                $drt_explode_space = str_replace(' ','*', $drt_implode);
                $drt_name = explode('*', $drt_explode_space);
                $exercice_numero = array_reverse(explode('*', $drt_explode_space));
            }else{
                $drt_name = explode('_', $drt_implode);
                $exercice_numero = array_reverse(explode('_', $drt_implode));
            }

            if($exercice_numero[0] != 0){
                $exercice = $exercice_numero[1];
                $numero_extension = $exercice_numero[0];
                $numero = explode('.', $numero_extension)[0];
                $numero = trim($numero);
                $numero = intval($numero);
                $echange_type = $drt_name[0];
                $echange_type = trim($echange_type);
                $exercice = trim($exercice);
                $exercice = intval($exercice);

                if( $exercice != 0 || $numero != 0 ){
                    if( $echange_type == 'R' ) {
                        $numero_reponse = $drt_name[1];
                        $dossier_name = substr( $drt_implode , 0, strlen($drt_implode) - strlen($numero_extension) -1 );
                        $dossier_name = substr( $dossier_name , 0, strlen($dossier_name) - strlen($exercice) -1 );
                        $dossier_name = substr( $dossier_name , strlen($drt_name[2]) + 1, strlen($dossier_name) - strlen($drt_name[2]));
                        $dossier_name = substr( $dossier_name , strlen($numero_reponse) + 1, strlen($dossier_name) - strlen($numero_reponse));
                        $dossier_name = substr( $dossier_name , strlen($echange_type) + 1, strlen($dossier_name) - strlen($echange_type));
                        $dossier_name = trim($dossier_name);
                        $echange_type = $drt_name[2];
                    }else{
                        $dossier_name = substr( $drt_implode , 0, strlen($drt_implode) - strlen($numero_extension) -1 );
                        $dossier_name = substr( $dossier_name , 0, strlen($dossier_name) - strlen($exercice) -1 );
                        $dossier_name = substr( $dossier_name , strlen($echange_type) + 1, strlen($dossier_name) - strlen($echange_type));
                        $dossier_name = trim($dossier_name);
                    }
                    $exercice = $annee + $exercice;
                    $extension = array_reverse(explode('.', $file_name_original))[0];

                    $echange_type_data = $this->getDoctrine()
                                              ->getRepository('AppBundle:EchangeType')
                                              ->findOneBy(array(
                                                  'nom' => $echange_type
                                              ));

                    $dossier = $this->getDoctrine()
                                    ->getRepository('AppBundle:Dossier')
                                    ->findOneBy(array('nom' => $dossier_name));

                    if( count($dossier) > 0 && count($echange_type_data) > 0 ) {
                        $echange = $this->getDoctrine()->getRepository('AppBundle:Echange')
                                        ->getEchangeByDossierExercice($echange_type_data, $dossier, $exercice);

                        $echange_item_exist = $this->getDoctrine()
                                                   ->getRepository('AppBundle:EchangeItem')
                                                   ->findOneBy(array(
                                                       'numero' => $numero,
                                                       'echange' => $echange
                                                   ));

                        if(((count($echange_item_exist) == 0) && ($drt_name[0] != 'R')) || ((count($echange_item_exist) > 0) && ($drt_name[0] == 'R'))) {
                            if( $drt_name[0] == 'R' ) {
                                $echange_reponse_exist = $this->getDoctrine()
                                                              ->getRepository('AppBundle:EchangeReponse')
                                                              ->findOneBy(array(
                                                                  'numero' => $numero_reponse,
                                                                  'echangeItem' => $echange_item_exist->getId()
                                                              ));

                                if( count($echange_reponse_exist) == 0 ) {
                                    $new_file_name = 'R' . $numero_reponse . '_' . $echange_type_data->getId() . '_' . $echange->getId() . '_' . $numero;
                                    $new_file_name = $new_file_name . '.' . $extension;
                                    $file->move($Dir, $new_file_name);

                                    $echange_reponse = new EchangeReponse();
                                    $echange_reponse->setNomFichier($new_file_name);
                                    $echange_reponse->setDateEnvoi(new \DateTime());
                                    $echange_reponse->setEchangeItem($echange_item_exist);
                                    $echange_reponse->setNumero($numero_reponse);
                                    $em->persist($echange_reponse);
                                    $em->flush();
                                    $drt_importe ++;
                                    $drt_name = 'R' . $numero_reponse . '-' .$echange->getEchangeType()->getNom() . ' ' . $echange->getDossier()->getNom() . ' ' . substr($exercice, 2, 3) . ' ' . $echange_item_exist->getNumero();
                                    array_push($data_drt, array(
                                        'drt' => $drt_name,
                                        'dossier' => $dossier,
                                        'echangeItem' => $echange_item_exist
                                    ));
                                }else{
                                    array_push($existe, array(
                                        'file_name' => $file_name_original,
                                        'echange_type' => 'R '.$numero_reponse.' '.$echange_type,
                                    ));
                                }
                            }else{
                                $new_file_name = $echange_type_data->getId() . '_' . $echange->getId() . '_' . $numero;
                                $new_file_name = $new_file_name . '.' . $extension;
                                $file->move($Dir, $new_file_name);
                                $drt_name = '';
                                foreach ($data_date as $date) {
                                    if ( $date['name'] == $file->getClientOriginalName() ) {
                                        $echange_item = new EchangeItem();
                                        $echange_item->setNumero($numero);
                                        $echange_item->setNomFichier($new_file_name);
                                        $echange_item->setStatus(0);
                                        $echange_item->setEchange($echange);
                                        $echange_item->setDateCreation(new \DateTime($date['date']));
                                        $em->persist($echange_item);
                                        $em->flush();
                                        $drt_name = $echange->getEchangeType()->getNom() . ' ' . $echange->getDossier()->getNom() . ' ' . substr($exercice, 2, 3) . ' ' . $echange_item->getNumero();
                                    }
                                }

                                array_push($data_drt, array(
                                    'drt' => $drt_name,
                                    'dossier' => $dossier,
                                    'echangeItem' => $echange_item
                                ));
                                $drt_importe ++;
                            }
                        }else{
                            array_push($existe, array(
                                'file_name' => $file_name_original,
                                'echange_type' => $echange_type
                            ));
                        }
                    } else if(count($dossier) > 0 && count($echange_type_data) == 0) {
                        array_push($fichier_incorrect, array(
                            'file_name' => $file_name_original
                        ));
                    }else{
                        $dossier_array = $this->similarDossier($client, $dossier_name);
                        array_push($pas_dossier, array(
                            'file_name' => $file_name_original,
                            'dossier'   => $dossier_array
                        ));
                    }
                }else{
                    array_push($fichier_incorrect, array(
                        'file_name' => $file_name_original
                    ));
                }
            }else{
                array_push($fichier_incorrect, array(
                    'file_name' => $file_name_original
                ));
            }
        }

        //envoi mail
        /*$this->configureSendImportEmail($data_drt);*/

        $reponse = array('existe' =>  $existe, 'pas_dossier' => $pas_dossier, 'drt_importe' => $drt_importe, 'fichier_incorrect' => $fichier_incorrect , 'list_erreur_import' => $list_erreur);
        return new JsonResponse($reponse);
    }

    public function drtDeleteAction(Request $request) {
        $post = $request->request;
        $echangeType = $post->get('echangeType');
        $dossier = $post->get('dossier');
        $exercice = $post->get('exercice');
        $drtOrRdrt = $post->get('drtOrRdrt');
        $numero_drt = $post->get('numero_drt');
        $numero_reponse = $post->get('numero_reponse');
        $echange_type = $this->getDoctrine()
                             ->getRepository('AppBundle:EchangeType')
                             ->find($echangeType);

        $data_dossier = $this->getDoctrine()
                             ->getRepository('AppBundle:Dossier')
                             ->find($dossier);

        $echange = $this->getDoctrine()->getRepository('AppBundle:Echange')
                        ->getEchangeByDossierExercice($echange_type, $data_dossier, $exercice);

        $echange_item = $this->getDoctrine()
                             ->getRepository('AppBundle:EchangeItem')
                             ->findOneBy(array(
                                 'numero' => $numero_drt,
                                 'echange' => $echange->getId()
                             ));

        $em = $this->getDoctrine()->getManager();
        if($drtOrRdrt == 1){
            $echange_reponse = $this->getDoctrine()
                                    ->getRepository('AppBundle:EchangeReponse')
                                    ->findOneBy(array(
                                        'numero' => $numero_reponse,
                                        'echangeItem' => $echange_item->getId()
                                    ));
            if(count($echange_reponse) > 0) {
                $echange_reponse->setSupprimer(1);
                $etat = 'delete_ok';
            }else{
                $etat = 'delete_nok';
            }
        }else{
            if(count($echange_item) > 0) {
                $echange_item->setSupprimer(1);
                $etat = 'delete_ok';
            }else{
                $etat = 'delete_nok';
            }
        }
        $em->flush();
        return new JsonResponse(array('status' => $etat, 'echange_type' => $echange_type->getNom()));
    }

    public function similarDossier($clientId, $dossier_name) {
        $dossier_array = [];
        $sites = $this->getDoctrine()
                      ->getRepository('AppBundle:Site')
                      ->findOneBy(array('client' => $clientId));
        $dossiers = $this->getDoctrine()
                         ->getRepository('AppBundle:Dossier')
                         ->findBy(array('site' => $sites));

        foreach ($dossiers as $dossier) {
            $dossierNom = $dossier->getNom();
            $dossier_name = strtolower($dossier_name);
            $dossier_name = preg_replace('/[^[:alnum:]-_]/', '_', $dossier_name);
            $dossier_name = str_replace(array('-', 'consulting', 'madame', 'monsieur', 'conseil'),
                array('_', '', '', '', ''), $dossier_name);
            $dossierNom = strtolower($dossierNom);
            $dossierNom = str_replace(array('-', 'consulting', 'madame', 'monsieur', 'conseil'),
                array('_', '', '', '', ''), $dossierNom);
            similar_text($dossier_name, $dossierNom, $percent);
            if ( $percent >= 80 ) {
                $dossier_array[] = array('nom' => $dossier->getNom(), 'pourcentage' => $percent);
            }
        }
        return $dossier_array;

    }

    public function configureSendImportEmail($data_drt = array()){
        if(!empty($data_drt)){
            $dossier_name = '';
            $data_drt_dossier = array();
            $config = array();
            foreach ( $data_drt as $data ){
                $dossier = $data['dossier'];
                $dossier = $dossier->getNom();
                $echange_type = explode(' ', $data['drt'])[0];

                if($dossier_name != $dossier) {
                    if(strlen($echange_type) != 3){
                        $data_drt_dossier[$dossier]['drt'] = '';
                        $data_drt_dossier[$dossier]['rdrt'] = $data['drt'];
                    }else{
                        $data_drt_dossier[$dossier]['drt'] = $data['drt'];
                        $data_drt_dossier[$dossier]['rdrt'] = '';
                    }
                    $client = $data['dossier']->getSite()->getClient()->getNom();
                    $site = $data['dossier']->getSite()->getNom();
                    $data_drt_dossier[$dossier]['client'] = $client;
                    $data_drt_dossier[$dossier]['site'] = $site;
                    $data_drt_dossier[$dossier]['echange_type'] = $echange_type;
                    $data_drt_dossier[$dossier]['mail'] = '';
                    $data_drt_dossier[$dossier]['mail_scriptura'] = '';
                    $data_drt_dossier[$dossier]['echangeItem'] = $data['echangeItem'];
                    $data_drt_dossier[$dossier]['dossier'] = $data['dossier'];

                    $smtp_client = $this->getDoctrine()
                                        ->getRepository('AppBundle:Smtp')
                                        ->findOneBy(array(
                                            'client' => $data['dossier']->getSite()->getClient()
                                        ));

                    if ( count($smtp_client) == 0 ) {
                        $from_address = 'support@'.strtolower($client).'.biz';
                        $data_drt_dossier[$dossier]['smtp'] = null;
                    }else{
                        $from_address = $smtp_client->getLogin();
                        $data_drt_dossier[$dossier]['smtp'] = $smtp_client;
                    }
                    $data_drt_dossier[$dossier]['from_address'] = $from_address;

                    $responsables = $this->getDoctrine()
                                         ->getRepository('AppBundle:ResponsableCsd')
                                         ->getResponsableParDossier($data['dossier']);

                    $clientsAccesDrt = $this->getDoctrine()
                                            ->getRepository('AppBundle:Utilisateur')
                                            ->getUtilisateurAccesDrt();

                    $clientFinauxDrt = [];
                    $clientFinauxMail = [];
                    $data_drt_dossier[$dossier]['mail'] = '';
                    $data_drt_dossier[$dossier]['mail_scriptura'] = '';

                    /** @var Utilisateur $clientAccesDrt */
                    foreach ( $clientsAccesDrt as $clientAccesDrt ) {
                        if ( $clientAccesDrt->email !== '' && $clientAccesDrt->email !== null ) {
                            if ( !in_array($clientAccesDrt->email, $clientFinauxDrt) ) {
                                $clientFinauxDrt[] = $clientAccesDrt->email;
                                if( $clientAccesDrt->type == 6 ) { // client final
                                    $clientFinauxMail[] = $clientAccesDrt->email;
                                    if( $data_drt_dossier[$dossier]['mail'] == '' ){
                                        $data_drt_dossier[$dossier]['mail'] = $clientAccesDrt->email;
                                    }else{
                                        $data_drt_dossier[$dossier]['mail'] = $data_drt_dossier[$dossier]['mail'].';'.$clientAccesDrt->email;
                                    }
                                }
                            }
                        }
                    }

                    /** @var ResponsableCsd $responsable */
                    foreach ( $responsables as $responsable ) {
                        if ( $responsable->getEmail() && $responsable->getEmail() != '' ) {
                            if ( $responsable->getEnvoiMail() === 1 ) {
                                if(in_array($responsable->getEmail(), $clientFinauxDrt)){
                                    if( !in_array($responsable->getEmail(), $clientFinauxMail) ) {
                                        $clientFinauxMail[] = $responsable->getEmail();
                                        if( $data_drt_dossier[$dossier]['mail'] == '' ){
                                            $data_drt_dossier[$dossier]['mail'] = $responsable->getEmail();
                                        }else{
                                            $data_drt_dossier[$dossier]['mail'] = $data_drt_dossier[$dossier]['mail'].';'.$responsable->getEmail();
                                        }
                                    }
                                }
                            }
                        }
                    }

                    /** Responsable Site ou Client */
                    $responsables = $this->getDoctrine()
                                         ->getRepository('AppBundle:ResponsableCsd')
                                         ->getResponsableSiteOuClient($data['dossier']);

                    /** @var ResponsableCsd site ou client */
                    foreach ($responsables as $responsable) {
                        if ($responsable->getEmail() && $responsable->getEmail() != '') {
                            if($responsable->getEnvoiMail() === 1){
                                if(in_array($responsable->getEmail(), $clientFinauxDrt)){
                                    if( !in_array($responsable->getEmail(), $clientFinauxMail) ) {
                                        $clientFinauxMail[] = $responsable->getEmail();
                                        if( $data_drt_dossier[$dossier]['mail'] == '' ){
                                            $data_drt_dossier[$dossier]['mail'] = $responsable->getEmail();
                                        }else{
                                            $data_drt_dossier[$dossier]['mail'] = $data_drt_dossier[$dossier]['mail'].';'.$responsable->getEmail();
                                        }
                                    }
                                }
                            }
                        }
                    }

                    /** Responsables Client */
                    $responsables = $this->getDoctrine()
                                         ->getRepository('AppBundle:ResponsableCsd')
                                         ->getResponsableClient($data['dossier']);


                    /** @var ResponsableCsd client */
                    foreach ($responsables as $responsable) {
                        if ($responsable->getEmail() && $responsable->getEmail() != '') {
                            if($responsable->getEnvoiMail() === 1){
                                if(in_array($responsable->getEmail(), $clientFinauxDrt)){
                                    if( !in_array($responsable->getEmail(), $clientFinauxMail) ) {
                                        $clientFinauxMail[] = $responsable->getEmail();
                                        if( $data_drt_dossier[$dossier]['mail'] == '' ){
                                            $data_drt_dossier[$dossier]['mail'] = $responsable->getEmail();
                                        }else{
                                            $data_drt_dossier[$dossier]['mail'] = $data_drt_dossier[$dossier]['mail'].';'.$responsable->getEmail();
                                        }
                                    }
                                }
                            }
                        }
                    }

                    /** Responsables Scriptura */
                    $responsables = $this->getDoctrine()
                                         ->getRepository('AppBundle:ResponsableCsd')
                                         ->getResponsableClientScriptura($data['dossier']->getSite()->getClient());

                    /** @var ResponsableCsd $responsable */
                    foreach ( $responsables as $responsable ) {
                        if ( $responsable->getEmail() && $responsable->getEmail() != '' ) {
                            $data_drt_dossier[$dossier]['mail_scriptura'] = $data_drt_dossier[$dossier]['mail_scriptura'].' '.$responsable->getEmail();
                            if( $data_drt_dossier[$dossier]['mail_scriptura'] == '' ){
                                $data_drt_dossier[$dossier]['mail_scriptura'] = $responsable->getEmail();
                            }else{
                                $data_drt_dossier[$dossier]['mail_scriptura'] = $data_drt_dossier[$dossier]['mail_scriptura'].';'.$responsable->getEmail();
                            }
                        }
                    }

                    $dossier_name = $dossier;
                }else{
                    if(array_key_exists($dossier, $data_drt_dossier)){
                        if(strlen($echange_type) != 3){
                            $data_drt_dossier[$dossier]['rdrt'] = $data_drt_dossier[$dossier]['rdrt'].' '.$data['drt'];
                        }else{
                            $data_drt_dossier[$dossier]['drt'] = $data_drt_dossier[$dossier]['drt'].' '.$data['drt'];
                        }
                    }
                }
            }

            foreach ( $data_drt_dossier as $key=>$drt_dossier ) {
                $config['copie-cache'] = '';
                $config['destinataire'] = '';
                $sujet = "Nouvelles ".$drt_dossier['echange_type']. "(".$drt_dossier['client'].")";
                $contenu = $this->renderView('@Drt/Drt/notificationImportDrt.html.twig', array(
                    'drt' => $drt_dossier['drt'],
                    'rdrt' => $drt_dossier['rdrt'],
                    'dossier' => $key,
                    'client' => $drt_dossier['client'],
                    'site' => $drt_dossier['site'],
                    'echangeType' => $drt_dossier['echange_type'],
                    'utilisateur' => $this->getUser()
                ));

                $message = \Swift_Message::newInstance()
                                         ->setSubject($sujet)
                                         ->setFrom($drt_dossier['from_address'], $drt_dossier['echange_type']);
                $message->setBcc("dinoh@scriptura.biz");

                $adresse_mail = explode(';', $drt_dossier['mail']);
                foreach ( $adresse_mail as $mail ) {
                    if($mail != ''){
                        $message->addTo($mail);
                        $config['destinataire'] = $mail;
                    }
                }
                $adresse_mail_scriptura = explode(';', $drt_dossier['mail_scriptura']);
                foreach ( $adresse_mail_scriptura as $mail ) {
                    if($mail != ''){
                        $message->addBcc($mail);
                        $config['copie-cache'] = $mail;
                    }
                }

                $message->setBody($contenu, 'text/html');
                $email_statut = 0;
                if($this->get('mailer')->send($message)){
                    $email_statut = 1;
                }

                /*sauvegarde contenu mail dans emails*/
                $em = $this->getDoctrine()
                           ->getManager();

                $email = new Emails();
                $email
                    ->setStatus($email_statut)
                    ->setContenu($contenu)
                    ->setDateCreation(new \DateTime())
                    ->setDossier($drt_dossier['dossier'])
                    ->setFromAddress($drt_dossier['from_address'])
                    ->setFromLabel($drt_dossier['echange_type'])
                    ->setTypeEmail('RAPPEL_IMPORT_'.$drt_dossier['echange_type'])
                    ->setSujet($sujet)
                    ->setBcc($config['copie-cache'])
                    ->setToAddress($config['destinataire'])
                    ->setEchangeItem($drt_dossier['echangeItem'])
                    ->setSmtp($drt_dossier['smtp']);

                if( $email_statut == 1 ) {
                    $email->setDateEnvoi(new \DateTime());
                    $email->setNbTentativeEnvoi(1);
                }
                $em->persist($email);
                $em->flush();
            }
            return TRUE;
        }
    }

    public function addNewEchangeItemAction(EchangeItem $echangeItem, $message = null){
        if( $echangeItem ) {
            $em = $this->getDoctrine()
                       ->getManager();
            $numero = $echangeItem->getNumero();
            $new_numero = intval($numero) * -1;
            $old_filename = $echangeItem->getNomFichier();
            $extension = explode('.', $old_filename)[1];
            $new_filename = $echangeItem->getEchange()->getEchangeType()->getId() . '_' . $echangeItem->getEchange()->getId() . '_' . $new_numero. '.' . $extension;
            $echangeItem->setNumero($new_numero);
            $echangeItem->setNomFichier($new_filename);
            $echangeItem->setStatus(1); // clôture old echange item (DRT ou DRP)
            $em->flush();

            $new_echange_item = new EchangeItem();
            $new_echange_item
                ->setNumero($numero)
                ->setNomFichier($old_filename)
                ->setStatus(0)
                ->setEchange($echangeItem->getEchange())
                ->setSupprimer($echangeItem->getSupprimer())
                ->setDateCreation($echangeItem->getDateCreation())
                ->setMessage($message)
                ->setTelecharger($echangeItem->getTelecharger())
                ->setEchangeItem($echangeItem);
            $em->persist($new_echange_item);
            $em->flush();
            return $new_echange_item->getNomFichier();
        }
        return new JsonResponse('ERROR');
    }
}
