<?php

namespace FacturationBundle\Controller;

use AppBundle\Entity\FactCritereEcriture;
use AppBundle\Entity\FactPrestationClient;
use AppBundle\Entity\Journal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use AppBundle\Controller\Boost;
use AppBundle\Entity\PrestationParam;


class ParamImportController extends Controller
{
    public function indexAction()
    {

        $journals = $this->getDoctrine()
            ->getRepository('AppBundle:Journal')
            ->findAll();

        $sources = $this->getDoctrine()
            ->getRepository('AppBundle:SourceImage')
            ->findAll();

        $criteres = $this->getDoctrine()
            ->getRepository('AppBundle:FactCritere')
            ->getAllCritere();
        return $this->render('FacturationBundle:ParamImport:index.html.twig', array(
            'criteres' => $criteres,
            'journals' => $journals,
            'sources' => $sources
        ));
    }

    public function simulationAction(Request $request)
    {
        $client = $request->request->get('client');
        $dossier = $request->request->get('dossier');
        $exercice = $request->request->get('exercice');

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Ecriture');

        $data = $repository->getMoisTraites($client,$dossier,$exercice);

        return new JsonResponse($data);

    }

    public function listeAction(FactPrestationClient $prestation)
    {
        return $this->getCriteres($prestation);
    }

    public function editAction(Request $request, FactPrestationClient $prestation)
    {
        $em = $this->getDoctrine()->getManager();
        $criteres = json_decode($request->request->get('criteres'), true);

        //Supprimer les critères supprimées
        $id_criteres = [];
        foreach ($criteres as $critere) {
            if ($critere['critere_id'] != 0) {
                $id_criteres[] = $critere['critere_id'];
            }
        }

        $criteres_existant = $this->getDoctrine()
            ->getRepository('AppBundle:FactCritereEcriture')
            ->getByPrestationClient($prestation);

        /** @var FactCritereEcriture $item */
        foreach ($criteres_existant as $item) {
            if (!in_array($item->getId(), $id_criteres)) {
                $em->remove($item);
                $em->flush();
            }
        }

        foreach ($criteres as $critere) {
            if ($critere['critere_id'] == 0) {
                $critere_ecriture = new FactCritereEcriture();
            } else {
                $critere_ecriture = $this->getDoctrine()
                    ->getRepository('AppBundle:FactCritereEcriture')
                    ->find($critere['critere_id']);
            }
            if ($critere_ecriture) {
                $the_critere = $this->getDoctrine()
                    ->getRepository('AppBundle:FactCritere')
                    ->findOneBy(array(
                        'code' => $critere['critere_code']
                    ));
                if ($the_critere) {
                    $critere_ecriture->setNom($critere['critere_nom'])
                        ->setValue(explode(";", $critere['critere_value']))
                        ->setExclure(explode(";", $critere['critere_exclure']))
                        ->setFactCritere($the_critere)
                        ->setFactPrestationClient($prestation);
                    $em->persist($critere_ecriture);
                    $em->flush();
                }
            }
        }
        return $this->getCriteres($prestation);
    }

    private function getCriteres(FactPrestationClient $prestation)
    {
        $criteres = $this->getDoctrine()
            ->getRepository('AppBundle:FactCritereEcriture')
            ->getByPrestationClient($prestation);

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $serializer = new Serializer(array($normalizer), array($encoder));

        return new JsonResponse($serializer->serialize($criteres, 'json'));
    }

    public function loadParamAction(Request $request)
    {
        $prestation_id = $request->request->get('prestation_id');

        $prestation = $this->getDoctrine()
            ->getRepository('AppBundle:FactPrestationClient')
            ->find($prestation_id);

        $param = $this->getDoctrine()
            ->getRepository('AppBundle:PrestationParam')
            ->findOneBy(array(
                'factPrestationClient' => $prestation
            ));

        $parameters = array();

        if ($param) {
            if ($param->getMotClef()) {
                $parameters['mot_clef'] = implode(",", json_decode($param->getMotClef()));
            }

            if ($param->getJournalIds()) {
                $journal_ids = json_decode($param->getJournalIds());
                $parameters['journal_ids'] = $journal_ids;
            }

            if ($param->getSourceImageIds()) {
               $source_ids = json_decode($param->getSourceImageIds());
               $parameters['source_ids'] = $source_ids;
            }

            $parameters['unite'] = $param->getUnite();

            $parameters['id'] = $param->getId();
        }

        $client_id = $request->request->get('client_id');

        $journals = $this->getDoctrine()
            ->getRepository('AppBundle:Journal')
            ->findAll();

        $sources = $this->getDoctrine()
            ->getRepository('AppBundle:SourceImage')
            ->findAll();

        return $this->render('FacturationBundle:ParamImport:load-param.html.twig', array(
            'journals' => $journals,
            'sources' => $sources,
            'prestation_id' => $prestation_id,
            'client_id' => $client_id,
            'parameters' => $parameters,
        ));
    }

    public function saveParamAction(Request $request)
    {
        $journals = $request->request->get('journals');
        $sources = $request->request->get('sources');
        $mot_clef = $request->request->get('mot_clef');
        $prestation_id = $request->request->get('prestation_id');
        $client_id = $request->request->get('client_id');
        $param_id = $request->request->get('param_id');

        $unite = $request->request->get('unite');

        $mots  = explode(",", $mot_clef);

        if ($param_id) {
            $param = $this->getDoctrine()
                ->getRepository('AppBundle:PrestationParam')
                ->find($param_id);
        } else{
            $param = new PrestationParam();
        }

        $param->setJournalIds(json_encode($journals));
        $param->setSourceImageIds(json_encode($sources));
        $param->setMotClef(json_encode($mots));
        $param->setUnite($unite);

        $prestation = $this->getDoctrine()
            ->getRepository('AppBundle:FactPrestationClient')
            ->find($prestation_id);

        $param->setFactPrestationClient($prestation);

        $em = $this->getDoctrine()->getManager();

        $em->persist($param);

        $em->flush();

        return new JsonResponse($param->getId());
    }

    public function nbPrestationAction(Request $request)
    {
        $param_id = $request->request->get('param_id');

        $param = $this->getDoctrine()
            ->getRepository('AppBundle:PrestationParam')
            ->find($param_id);

        $dossier = $request->request->get('dossier');
        $exercice = $request->request->get('exercice');

        $nb = $this->getDoctrine()
            ->getRepository('AppBundle:PrestationParam')
            ->getNbPrestation($param,$dossier,$exercice);

        return new JsonResponse ($nb);

    }

    public function etatComptaAction(Request $request)
    {
        $client = $request->request->get('client');
        $exercice = $request->request->get('exercice');

        $data = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->etatCompta($client,$exercice);

        return new JsonResponse($data);
    }

    public function journauxParamAction(Request $request)
    {
        $dossier = $request->request->get('dossier');

        $details = $this->getDoctrine()
                            ->getRepository('AppBundle:Dossier')
                            ->journauxParam($dossier);

        $journal = $this->getDoctrine()
                            ->getRepository('AppBundle:HistoriqueUpload')
                            ->journalOptions();

        return new JsonResponse((object)[
            'data' => $details,
            'journal' => $journal
        ]);
    }

    public function journalListAction()
    {
        $journal = $this->getDoctrine()
                            ->getRepository('AppBundle:HistoriqueUpload')
                            ->journalOptions(false);

        return new JsonResponse($journal);
    }

    public function journalEditAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $code = $request->request->get('code', '');
            $libelle = $request->request->get('libelle', '');
            $id = $request->request->get('id', '');

            if ($id == 'new_row') {
                $journal = new Journal();

                $journal->setCode($code);
                $journal->setLibelle($libelle);

                $em = $this->getDoctrine()->getManager();
                $em->persist($journal);
                $em->flush();

                return new JsonResponse((object)[
                    'status' => 200
                ]);
            }

            $data = array(
                'code' => $code,
                'libelle' => $libelle,
                'id' => $id
            );

            $saved = $this->getDoctrine()
                            ->getRepository('AppBundle:Journal')
                            ->saveJournalEdit($data);

            if ($saved) {
                return new JsonResponse((object)[
                    'status' => 200
                ]);
            }
            
        }
    }

    public function journalDeleteAction(Journal $journal)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($journal);
        $em->flush();

        return new JsonResponse((object)[
            'status' => 200
        ]);
    }

    public function JournalDossierParamAction($client)
    {
        $journal_dossiers = $this->getDoctrine()
                            ->getRepository('AppBundle:Journal')
                            ->getJournalDossier($client);

        $journal = $this->getDoctrine()
                            ->getRepository('AppBundle:HistoriqueUpload')
                            ->journalOptions();

        return new JsonResponse((object)[
            'data' => $journal_dossiers,
            'journal' => $journal
        ]);

        // return new JsonResponse($journal_dossiers);
    }

    public function saveJournalDossierTypeAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $id              = $request->request->get('id', '');
            $journal_code    = $request->request->get('type_journal', '');

            $data = array(
                'journal_code' => $journal_code,
                'id' => $id,
            );

            $saved = $this->getDoctrine()
                            ->getRepository('AppBundle:Journal')
                            ->saveJournalDossier($data);

            return new JsonResponse((object)[
                'status' => 200,
                'count' => $saved
            ]);
            
        }
    }

    public function syncJournalModelAction()
    {
        $sync = $this->getDoctrine()
                     ->getRepository('AppBundle:Journal')
                     ->syncJM();

        return new JsonResponse((object)[
            'status' => 200,
            'count' => $sync
        ]);
    }
}
