<?php


namespace GeneralBundle\Controller;
ini_set('max_execution_time', -1);
ini_set('max_input_time', -1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GeneralBundle\Controller\DefaultController;
use AppBundle\Controller\Boost;

class EvolutionDossierController extends DefaultController
{
	public function indexAction()
	{
	    $user       = $this->getUser();
        $repository = $this->loadRepository('Client');
        $clients    = $repository->getUserClients($user);

	    return $this->render('GeneralBundle:EvolutionDossier:index.html.twig', array(
	    	'clients' => $clients
	    ));
	}

    public function get24Mois($exercice)
    {
        $start = $exercice . '-01-01';
        $end   = new \DateTime($start);
        $end->add(new \DateInterval('P23M'));

        return array(
            'start' => $start,
            'end'   => $end->format('Y-m-d')
        );
    }

    public function evolutionAction(Request $request)
    {
        $post         = $request->request;
        $client       = $post->get('client');
        $dossier      = $post->get('dossier');
        $site         = $post->get('site');
        $exercice     = $post->get('exercice');
        $value_sd     = $post->get('value_sd') ;
        $filtre_sd    = $post->get('filtre_sd');
        $operateur_sd = $post->get('operateur_sd');
        $analyse      = $post->get('analyse');
        $images       = array();
        $repository   = $this->loadRepository('Image');
        $clientsList = json_decode($client,1);

        if ($clientsList == NULL) {
            $clientsList = array();
            array_push($clientsList, $client);
        }

        // $clientsList = array();
        // array_push($clientsList, 'N3RZZTNRRzBhWmxTbjY4NE4zUlpaVE5SUnpCaFdteFRiZz09');
        // array_push($clientsList, 'T1g3TDhCSXFvVFgwaDc1MFQxZzNURGhDU1hGdlZGZ3dhQT09');

        if (count($clientsList) > 1) {
            $key                  = 3;
            $images[0]['client']  = 'Total';
            $images[0]['dossier'] = 'Dossiers';
            $images[0]['n']       = 0;
            $images[0]['n-1']     = 0;
            $images[1]['client']  = 'Total';
            $images[1]['dossier'] = 'Images';
            $images[1]['n']       = 0;
            $images[1]['n-1']     = 0;
            $images[2]['dossier'] = '';
        } else {
            $key = 0;
        }

        $beginEnd = $this->get24Mois($exercice);
        $listMois = $this->getBetweenDate($beginEnd['start'], $beginEnd['end']);
        foreach ($clientsList as $client) {
            $param    = array(
                'client'       => $client,
                'dossier'      => $dossier,
                'exercice'     => $exercice,
                'site'         => $site,
                'analyse'      => $analyse,
                'operateur_sd' => $operateur_sd,
                'filtre_sd'    => $filtre_sd,
                'value_sd'     => $value_sd
            );

            $paramNMoinsUn = array(
                'client'       => $client,
                'dossier'      => $dossier,
                'exercice'     => $exercice - 1,
                'site'         => $site,
                'analyse'      => $analyse,
                'operateur_sd' => $operateur_sd,
                'filtre_sd'    => $filtre_sd,
                'value_sd'     => $value_sd
            );

            if ($filtre_sd && $operateur_sd && $value_sd) {
                $nbDossierNMoinsUns      = $repository->nbDossiersEvolutionNMoinsUn($paramNMoinsUn);
                $images[$key]['n-1']     = $nbDossierNMoinsUns;
                $nbImages                = $repository->totalImagesEvolution($paramNMoinsUn);
                $images[$key + 1]['n-1'] = $nbImages;

                if (count($clientsList) > 1) {
                    $images[0]['n-1'] += $nbDossierNMoinsUns;
                    $images[1]['n-1'] += $nbImages;
                }

                if ($filtre_sd == 1) {
                    // nombre d'images
                    $value = $value_sd;
                } else {
                    // pourcentage N - 1
                    $value = round( intval($nbImages) * intval($value_sd) / 100 );
                }

                // Sélection dossier
                $clientId   = Boost::deboost($client,$this);
                $clientName = $this->getDoctrine()
                        ->getRepository('AppBundle:Client')
                        ->find(intval($clientId))
                        ->getNom();

                $images[$key]['client']      = $clientName;
                $images[$key + 1]['client']  = $clientName;
                $images[$key]['dossier']     = 'Dossiers';
                $images[$key + 1]['dossier'] = 'Images';
                $images[$key]['n']           = 0;
                $images[$key + 1]['n']       = 0;

                foreach ($listMois as $k => $mois) {
                    $i = $k + 1;
                    $m = 'm' . $i;
                    $paramMois = array(
                        'client'   => $client,
                        'dossier'  => $dossier,
                        'exercice' => $exercice,
                        'site'     => $site,
                        'mois'     => $mois,
                        'analyse'  => $analyse,
                        'value'    => $value,
                        'operator' => $operateur_sd
                    );

                    $nbMois            = $repository->nbDossiersMoisFilter($paramMois);
                    $images[$key][$m]  = $nbMois;
                    $images[$key]['n'] += $nbMois;

                    if (count($clientsList) > 1) {
                        $images[0]['n'] += $nbMois;
                        if (array_key_exists($m, $images[0])) {
                            $images[0][$m] += $nbMois;
                        } else {
                            $images[0][$m] = $nbMois;
                        }
                    }

                    $data = $repository->imagesMoisEvolutionFilter($paramMois);

                    if (empty($data)) {
                        if (!array_key_exists($m, $images[$key + 1])) {
                            $images[$key + 1][$m] = 0;
                        }
                    }

                    foreach ($data as $d) {
                        if (array_key_exists($m, $images[$key + 1])) {
                            $images[$key + 1][$m] += $d->nb;
                        } else {
                            $images[$key + 1][$m] = $d->nb;
                        }

                        if ($analyse == 1) {
                            $images[$key + 1]['n'] += $d->nb;
                        }

                        if (count($clientsList) > 1) {
                            if (array_key_exists($m, $images[1])) {
                                $images[1][$m] += $d->nb;
                            } else {
                                $images[1][$m] = $d->nb;
                            }

                            $images[1]['n'] += $d->nb;
                        }
                    }
                }

                if ($analyse != 1) {
                    $images[$key + 1]['n'] = $images[$key + 1]['m24'];
                }

                $paramAnterieur = array(
                    'client'   => $client,
                    'dossier'  => $dossier,
                    'exercice' => $exercice,
                    'site'     => $site,
                    'analyse'  => $analyse,
                    'mois'     => $exercice . '-01',
                    'value'    => $value,
                    'operator' => $operateur_sd
                );

                $images[$key]['m-inf'] = $repository->nbDossiersMoisFilter($paramAnterieur,true);
                $images[$key]['n']     += $images[$key]['m-inf'];

                if (count($clientsList) > 1) {
                    $images[0]['n'] += $images[$key]['m-inf'];

                    if (array_key_exists('m-inf', $images[0])) {
                        $images[0]['m-inf'] += $images[$key]['m-inf'];
                    } else {
                        $images[0]['m-inf'] = $images[$key]['m-inf'];
                    }
                }

                $data = $repository->imagesMoisEvolutionFilter($paramAnterieur,true);

                if (empty($data)) {
                    if (!array_key_exists('m-inf', $images[$key + 1])) {
                        $images[$key + 1]['m-inf'] = 0;
                    }
                }

                foreach ($data as $d) {
                    if (array_key_exists('m-inf', $images[$key + 1])) {
                        $images[$key + 1]['m-inf'] += $d->nb;
                    } else {
                        $images[$key + 1]['m-inf'] = $d->nb;
                    }

                    if ($analyse == 1) {
                        $images[$key + 1]['n'] += $d->nb;
                    }

                    if (count($clientsList) > 1) {
                        if (array_key_exists('m-inf', $images[1])) {
                            $images[1]['m-inf'] += $d->nb;
                        } else {
                            $images[1]['m-inf'] = $d->nb;
                        }

                        $images[1]['n'] += $d->nb;
                    }
                }

            } else{
                $nbDossierNMoinsUns      = $repository->nbDossiersEvolutionNMoinsUn($paramNMoinsUn);
                // $nbDossierN              = $repository->nbDossiersEvolution($param);
                $images[$key]['dossier'] = 'Dossiers';
                $images[$key]['n-1']     = $nbDossierNMoinsUns;
                // $images[$key]['n']       = $nbDossierN;
                $images[$key]['n']       = 0;

                if (count($clientsList) > 1) {
                    $images[0]['n-1'] += $nbDossierNMoinsUns;
                    // $images[0]['n'] += $nbDossierN;
                }

                $clientId   = Boost::deboost($client,$this);
                $clientName = $this->getDoctrine()
                        ->getRepository('AppBundle:Client')
                        ->find(intval($clientId))
                        ->getNom();

                $images[$key]['client']     = $clientName;
                $images[$key + 1]['client'] = $clientName;

                $paramAnterieur = array(
                    'client'   => $client,
                    'dossier'  => $dossier,
                    'exercice' => $exercice,
                    'site'     => $site,
                    'analyse'  => $analyse,
                    'mois'     => $exercice . '-01'
                );

                $imgAnterieur              = $repository->imagesMoisEvolution($paramAnterieur,true);
                $images[$key + 1]['m-inf'] = $imgAnterieur;
                $dossierAnterieur          = $repository->nbDossierAnterieur($paramAnterieur,true);
                $images[$key]['m-inf']     = $dossierAnterieur;

                if ($param['analyse'] == 1) {
                    if (array_key_exists('n', $images[$key])) {
                        $images[$key]['n'] += $dossierAnterieur;
                    } else {
                        $images[$key]['n'] = $dossierAnterieur;
                    }
                }

                foreach ($listMois as $k => $mois) {
                    $i = $k + 1;
                    $m = 'm' . $i;
                    $paramMois = array(
                        'client'   => $client,
                        'dossier'  => $dossier,
                        'exercice' => $exercice,
                        'site'     => $site,
                        'mois'     => $mois,
                        'analyse'  => $analyse
                    );
                    $nbMois               = $repository->nbDossiersMois($paramMois);
                    $images[$key][$m]     = $nbMois;
                    $images[$key + 1][$m] = $repository->imagesMoisEvolution($paramMois);

                    if ($analyse == 1) {
                        $images[$key]['n'] += $nbMois;
                    }

                    else {

                            if ($m == 'm1') {
                                $images[$key + 1][$m] += $images[$key + 1]['m-inf'];
                            }


                            if ($k > 0) {
                                $minf = 'm' . $k;
                                $images[$key + 1][$m] += $images[$key + 1][$minf];
                            }
                    }


                    if (count($clientsList) > 1) {
                        if (array_key_exists($m, $images[0])) {
                            $images[0][$m] += $nbMois;
                        } else {
                            $images[0][$m] = $nbMois;
                        }

                        if (array_key_exists($m, $images[1])) {
                            $images[1][$m] += $images[$key + 1][$m];
                        } else {
                            $images[1][$m] = $images[$key + 1][$m];
                        }

                        if ($analyse == 1) {
                            $images[0]['n'] += $images[$key][$m];
                        }


                    }
                }

                if ($analyse != 1) {
                    $images[$key]['n'] = $images[$key]['m24'];
                }


                

                $images[$key + 1]['n']       = $repository->totalImagesEvolution($param);
                $images[$key + 1]['n-1']     = $repository->totalImagesEvolution($paramNMoinsUn);
                $images[$key + 1]['dossier'] = "Images";

                if (count($clientsList) > 1) {
                    $images[1]['n-1'] += $images[$key + 1]['n-1']; 
                    $images[1]['n'] += $images[$key + 1]['n'];

                    if (array_key_exists('m-inf', $images[0])) {
                        $images[0]['m-inf'] += $dossierAnterieur; 
                    } else {
                        $images[0]['m-inf'] = $dossierAnterieur; 
                    }

                    if (array_key_exists('m-inf', $images[1])) {
                        $images[1]['m-inf'] += $imgAnterieur; 
                    } else {
                        $images[1]['m-inf'] = $imgAnterieur; 
                    }

                    if ($analyse == 1) {
                        $images[0]['n'] += $images[$key]['m-inf'];
                    }


                }
            }

            if ($analyse != 1) {
                $images[0]['n'] = $images[0]['m24'];
            }

            $images[$key + 2]['dossier'] = "";
            $key = $key + 3;

        }


        // DOSSIERS DETAILS
        if ($filtre_sd && $operateur_sd && $value_sd) {

            if (count($clientsList) == 1) {
                
                $client = $clientsList[0];
                $dossiers = array();

                foreach ($listMois as $k => $mois) {
                        $paramMois = array(
                            'client'   => $client,
                            'dossier'  => $dossier,
                            'exercice' => $exercice,
                            'site'     => $site,
                            'mois'     => $mois,
                            'analyse'  => $analyse
                        );

                        $dossierMoisDetails = $repository->dossierMoisDetailsFilter($paramMois);

                        if ($analyse == 1) {
                            $dossiers = array_merge($dossiers, $dossierMoisDetails);
                        } else {
                            $dossiers = $dossierMoisDetails;
                        }


                }


                if ($analyse == 1) {
                    $dossierAnterieurList          = $repository->dossierMoisDetailsFilter($paramAnterieur,true);
                    $dossiers = array_merge($dossiers, $dossierAnterieurList);
                }

                usort($dossiers, function ($item1, $item2) {
                    if ($item1->dossier == $item2->dossier) return 0;
                    return $item1->dossier < $item2->dossier ? -1 : 1;
                });

                foreach ($dossiers as $it) {
                    $images[$key]['dossier'] = $it->dossier;

                    $dossier = Boost::boost($it->dossier_id);

                    // $paramAnterieur['dossier'] = $dossier;

                    // $imgAnterieurDossier  = $repository->imagesMoisEvolutionFilter($paramAnterieur,true);

                    $images[$key]['n-1'] = 0;
                    $images[$key]['n'] = 0;

                    // $images[$key]['m-inf'] = $imgAnterieurDossier;

                    // $images[$key]['n'] += $images[$key]['m-inf'];

                    $paramNMoinsUn['dossier'] = $dossier;

                    $images[$key]['n-1']     = $repository->totalImagesEvolution($paramNMoinsUn);


                    foreach ($listMois as $k => $mois) {
                        $i = $k + 1;
                        $m = 'm' . $i;
                        
                        $paramMois = array(
                            'client'   => $client,
                            'dossier'  => $dossier,
                            'exercice' => $exercice,
                            'site'     => $site,
                            'mois'     => $mois,
                            'analyse'  => $analyse,
                            'value'    => $value,
                            'operator' => $operateur_sd
                        );

                        $data = $repository->imagesMoisEvolutionFilter($paramMois);

                        if (empty($data)) {
                            if (!array_key_exists($m, $images[$key])) {
                                $images[$key][$m] = 0;
                            }
                        }

                        foreach ($data as $d) {
                            if (array_key_exists($m, $images[$key])) {
                                $images[$key][$m] += $d->nb;
                            } else {
                                $images[$key][$m] = $d->nb;
                            }

                            if ($analyse == 1) {
                                $images[$key]['n'] += $d->nb;
                            }
                            
                        }

                        if ($analyse == 2) {

                            if ($k > 0) {
                                $prec = 'm' . $k;
                                if (array_key_exists($prec, $images[$key])) {
                                    $images[$key][$m] += $images[$key][$prec];
                                }
                            } else {
                                $images[$key][$m] += $images[$key]['m-inf'];
                            }

                        }

                    }

                    $paramAnterieur = array(
                        'client'   => $client,
                        'dossier'  => $dossier,
                        'exercice' => $exercice,
                        'site'     => $site,
                        'analyse'  => $analyse,
                        'mois'     => $exercice . '-01',
                        'value'    => $value,
                        'operator' => $operateur_sd
                    );

                    $dataAnt = $repository->imagesMoisEvolutionFilter($paramAnterieur,true);

                    if (empty($dataAnt)) {
                        if (!array_key_exists('m-inf', $images[$key])) {
                            $images[$key]['m-inf'] = 0;
                        }
                    }

                    foreach ($dataAnt as $dA) {
                        if (array_key_exists('m-inf', $images[$key])) {
                            $images[$key]['m-inf'] += $dA->nb;
                        } else {
                            $images[$key]['m-inf'] = $dA->nb;
                        }

                        if ($analyse == 1) {
                            $images[$key]['n'] += $dA->nb;
                        }
                    }

                    $key += 1;
                }


            }

        } else {

            if (count($clientsList) == 1) {

                $client = $clientsList[0];
                $dossiers = array();

                foreach ($listMois as $k => $mois) {
                        $paramMois = array(
                            'client'   => $client,
                            'dossier'  => $dossier,
                            'exercice' => $exercice,
                            'site'     => $site,
                            'mois'     => $mois,
                            'analyse'  => $analyse
                        );

                        $dossierMoisDetails = $repository->dossierMoisDetails($paramMois);

                        if ($analyse == 1) {
                            $dossiers = array_merge($dossiers, $dossierMoisDetails);
                        } else {
                            $dossiers = $dossierMoisDetails;
                        }


                }


                if ($analyse == 1) {
                    $dossierAnterieurList          = $repository->dossierAnterieurList($paramAnterieur,true);
                    $dossiers = array_merge($dossiers, $dossierAnterieurList);
                    
                }

                usort($dossiers, function ($item1, $item2) {
                    if ($item1->dossier == $item2->dossier) return 0;
                    return $item1->dossier < $item2->dossier ? -1 : 1;
                });

                // sort($dossiers);


                foreach ($dossiers as $value) {
                    $images[$key]['dossier'] = $value->dossier;

                    $dossier = Boost::boost($value->dossier_id);

                    $paramAnterieur['dossier'] = $dossier;

                    $imgAnterieurDossier  = $repository->imagesMoisEvolution($paramAnterieur,true);

                    $images[$key]['n-1'] = 0;
                    $images[$key]['n'] = 0;

                    $images[$key]['m-inf'] = $imgAnterieurDossier;

                    $images[$key]['n'] += $images[$key]['m-inf'];

                    $paramNMoinsUn['dossier'] = $dossier;

                    $images[$key]['n-1']     = $repository->totalImagesEvolution($paramNMoinsUn);



                     foreach ($listMois as $k => $mois) {
                        $i = $k + 1;
                        $m = 'm' . $i;
                        $paramMois = array(
                            'client'   => $client,
                            'dossier'  => $dossier,
                            'exercice' => $exercice,
                            'site'     => $site,
                            'mois'     => $mois,
                            'analyse'  => intval($analyse)
                        );

                        $images[$key][$m] = $repository->imagesMoisEvolution($paramMois);

                        $images[$key]['n'] += $images[$key][$m];

                        if ($analyse == 2) {

                            if ($k > 0) {
                                $prec = 'm' . $k;
                                if (array_key_exists($prec, $images[$key])) {
                                    $images[$key][$m] += $images[$key][$prec];
                                }
                            } else {
                                $images[$key][$m] += $images[$key]['m-inf'];
                            }

                        }

                    }

                    $key += 1;
                }


            }
        }



        return $this->response($images);

    }

	public function beginEnd($exercice, $cloture)
    {
        if ($cloture < 9) {
            $debutMois = ($exercice - 1) . '-0' . ($cloture + 1) . '-01';
        } else if ($cloture >= 9 and $cloture < 12) {
            $debutMois = ($exercice - 1) . '-' . ($cloture + 1) . '-01';
        } else {
            $debutMois = ($exercice) . '-01-01';
        }
        if ($cloture < 10) {
            $finMois = ($exercice) . '-0' . ($cloture) . '-01';
        } else {
            $finMois = ($exercice) . '-' . ($cloture) . '-01';
        }

        $result          = array();
        $result['start'] = $debutMois;
        $result['end']   = $finMois;

        return $result;

    }

    public function getMoisInf($exercice){
        $exercice  = intval($exercice) - 2;
        $exercice2 = intval($exercice) + 2 - 1;
        $start     = $exercice . '-01-01';
        $end       = $exercice2 . '-12-01';
        return array(
            'start' =>$start,
            'end'   => $end
        );
    }
}
