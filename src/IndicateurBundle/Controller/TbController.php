<?php

namespace IndicateurBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Site;
use AppBundle\Functions\CustomPdoConnection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class TbController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $exercices = Boost::getExercices(9);
        return $this->render('IndicateurBundle:Tb:index.html.twig',[
            'exercices' => $exercices,
            'affichage' => 0
        ]);
    }

    /**
     * @return Response
     */
    public function indexScoringAction()
    {
        $exercices = Boost::getExercices(9);
        return $this->render('IndicateurBundle:Tb:index.html.twig',[
            'exercices' => $exercices,
            'affichage' => 1
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function dossiersAction(Request $request)
    {
        $post = $request->request;
        $client = Boost::deboost($post->get('client'),$this);
        $site = Boost::deboost($post->get('site'),$this);
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $variation = (intval($post->get('variation')) == 1);
        $affichage = intval($post->get('affichage'));
        $exos = json_decode($post->get('exos'));

        $user = $this->getUser();
        if(is_bool($client) || is_bool($site) || is_bool($dossier)) return new Response('security');
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')->find($client);
        $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($site);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        if ($dossier) $dossiers = [$dossier];
        else
            $dossiers = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->getUserDossier(
                    $this->getUser(),
                    $client,
                    $site);

        $periodes = json_decode($post->get('p'));
        $result = $this->getDoctrine()->getRepository('AppBundle:IndicateurTb')->getTb($dossiers,$periodes,$variation,$affichage,$exos);

        /*return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => $result
        ]);*/

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function allDossiersAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        $site = Boost::deboost($request->request->get('site'),$this);

        if(is_bool($client) || is_bool($site)) return new JsonResponse([]);
        /** @var Client $client */
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')
            ->find($client);
        /** @var Site $site */
        $site = $this->getDoctrine()->getRepository('AppBundle:Site')
            ->find($site);
        $params =
            [
                'cloture' => 1,
                'type' => 3
            ];

        if ($site)
        {
            $req = '
                SELECT DISTINCT h.dossier_id 
                FROM historique_upload h 
                JOIN dossier d ON (d.id = h.dossier_id)
                WHERE h.cloture = :cloture AND 
                      h.type = :type AND 
                      d.site_id = :site 
                ORDER BY d.nom                      
            ';
            $params['site'] = $site->getId();
        }
        elseif ($client)
        {
            $req = '
                SELECT DISTINCT h.dossier_id 
                FROM historique_upload h 
                JOIN dossier d ON (d.id = h.dossier_id)
                JOIN site s ON (s.id = d.site_id)
                WHERE h.cloture = :cloture AND 
                      h.type = :type AND 
                      s.client_id = :client 
                ORDER BY d.nom 
            ';
            $params['client'] = $client->getId();
        }
        else
        {
            $req = '
                SELECT DISTINCT h.dossier_id 
                FROM historique_upload h
                JOIN dossier d ON (d.id = h.dossier_id)
                WHERE h.cloture = :cloture AND h.type = :type 
                ORDER BY d.nom 
                ';
        }

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        //$req .= 'LIMIT 3';

        $res = [];
        $prep = $pdo->prepare($req);
        $prep->execute($params);
        $ids = $prep->fetchAll();

        foreach ($ids as $id)
            $res[] = Boost::boost($id->dossier_id);

        return new JsonResponse($res);
    }

    public function exportAction(Request $request)
    {
        $datas = json_decode(urldecode($request->request->get('all_datas')));
        $stats = json_decode(urldecode($request->request->get('all_stats')));
        $cles = json_decode(urldecode($request->request->get('all_cles')));
        $entetes = json_decode(urldecode($request->request->get('all_entetes')));
        $extension = strtolower($request->request->get('format'));
        $statistiques = json_decode(urldecode($request->request->get('all_statistiques')));

        $title = 'Scoring';
        $name = $title;
        $name .= '.'.$extension;
        $name = str_replace(' ','_',$name);

        if($extension == 'xls')
        {
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
            $phpExcelObject->getProperties()->setCreator("liuggio")
                ->setLastModifiedBy("Scriptura")
                ->setTitle("Office 2005 XLSX Test Document")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("Test result file");
            $sheet = $phpExcelObject->setActiveSheetIndex(0);

            $row = 1;
            $sheet
                ->setCellValue('A'.$row, $title);
            $row++;
            $row++;

            $sheet
                ->setCellValue('A'.$row, 'Au moins une Clé / Total (Dossier)')
                ->setCellValue('B'.$row,count($statistiques->dossier_cles).' / '.count($statistiques->dossier));
            $row++;
            $sheet
                ->setCellValue('A'.$row, 'Au moins une Clé/Total (Compta)')
                ->setCellValue('B'.$row,count($statistiques->compta_cles).' / '.count($statistiques->compta));
            $row++;

            $totalCles = 0;
            foreach ($statistiques->cles_totals as $cles_total)
            {
                $sheet
                    ->setCellValue('A'.$row, $cles_total->cle)
                    ->setCellValue('B'.$row, $cles_total->occ);
                $totalCles += intval($cles_total->occ);
                $row++;
            }

            $sheet
                ->setCellValue('A'.$row, 'Total')
                ->setCellValue('B'.$row, $totalCles);

            $row++;
            $row++;

            $sheet
                ->setCellValue('A'.$row, 'Dossier')
                ->setCellValue('B'.$row, 'Exercice');
            $s = 'B';
            foreach ($entetes as $entete)
            {
                $sheet
                    ->setCellValue(++$s.$row, $entete->l.(trim($entete->n) != '' ? '('.$entete->n.')' : ''));
            }

            $row++;
            foreach ($datas as $data)
            {
                $sheet
                    ->setCellValue('A'.$row, $data->dossier)
                    ->setCellValue('B'.$row, $data->exo);

                $s = 'B';
                foreach($data as $key => $value)
                {
                    if ($key != 'dossier' && $key != 'n' && $key != 'exo')
                    {
                        $coeff = (property_exists($value,'u') && intval($value->u) == 1) ? 100 : 1;
                        $decimal = property_exists($value,'r') ? $value->r : 0;
                        $valeur = floatval($value->v) * $coeff;
                        $valeur = round($valeur,$decimal);
                        $sheet
                            ->setCellValue(++$s.$row, (($valeur != 0) ? $valeur : ''));
                    }
                }

                $row++;
            }

            $phpExcelObject->getActiveSheet()->setTitle('Simple');
            $phpExcelObject->setActiveSheetIndex(0);

            $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
            $response = $this->get('phpexcel')->createStreamedResponse($writer);
            $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $name
            );
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);

            return $response;
        }

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => [
                $statistiques,
                $datas,
                $stats,
                $cles,
                $entetes
            ]
        ]);
    }
}
