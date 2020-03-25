<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 04/04/2019
 * Time: 08:20
 */

namespace DrtBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    public function exportsAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $site = Boost::deboost($request->request->get('site'),$this);
        $exercice = intval($request->request->get('exercice'));

        if(is_bool($client) || is_bool($site) || is_bool($dossier)) return new Response('security');
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')
            ->find($client);
        $site = $this->getDoctrine()->getRepository('AppBundle:Site')
            ->find($site);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);

        $listes = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')
            ->getHistoriques($this->getUser(),$client,$site,$dossier,$exercice);

        return new JsonResponse($listes);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function recapsAction(Request $request)
    {
        $recaps = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')
            ->getRecaps();

        return new JsonResponse($recaps);
    }

    public function recapsDetailsAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        if(is_bool($client)) return new Response('security');
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')
            ->find($client);

        $type = intval($request->request->get('type'));

        $details = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')
            ->details($client,$type);


        if ($type == 3) {
            $journal = $this->getDoctrine()
                            ->getRepository('AppBundle:HistoriqueUpload')
                            ->journalOptions();

            return new JsonResponse((object)[
                'datas' => $details,
                'journal' => $journal
            ]);
            
        }

        return new JsonResponse((object)[
            'datas' => $details,
        ]);
    }

    public function dnpEditAction(Request $request)
    {
        // if ($request->isXmlHttpRequest()) {
        //     $journal_dossier_id = $request->request->get('id', '');
        //     $journal_code = $request->request->get('type', '');

        //     $data = array(
        //         'journal_dossier_id' => $journal_dossier_id,
        //         'journal_code' => $journal_code
        //     );

        //     $saved = $this->getDoctrine()
        //                     ->getRepository('AppBundle:HistoriqueUpload')
        //                     ->saveDnpEdit($data);

        //     if ($saved) {
        //         return new JsonResponse((object)[
        //             'status' => 200
        //         ]);
        //     }
            
        // }
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

            if ($saved) {
                return new JsonResponse((object)[
                    'status' => 200
                ]);
            }
            
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function cloturerAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);

        $exercice = intval($request->request->get('exercice'));

        $res = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')
            ->cloturerCompta($dossier,$exercice);

        return new Response($res ? 0 : 1);
    }
}