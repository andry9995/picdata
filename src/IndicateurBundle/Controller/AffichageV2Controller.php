<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 21/11/2016
 * Time: 08:50
 */

namespace IndicateurBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Ecriture;
use AppBundle\Entity\HistoriqueUpload;
use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurCommentaire;
use AppBundle\Entity\IndicateurSpecGroup;
use AppBundle\Entity\IndicateurTbCle;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AffichageV2Controller extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('IndicateurBundle:AffichageV2:index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function packsAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        if($dossier == null) return new Response('error');

        $this->getDoctrine()->getRepository('AppBundle:Tiers')->majTierPcc($dossier);
        $dateNow = new DateTime();

        $graphes = $this->getDoctrine()->getRepository('AppBundle:TypeGraphe')->getAll();

        $indicateurGroup = $this->getDoctrine()->getRepository('AppBundle:IndicateurSpecGroup')->getIndicateurGroup($dossier);

        if($indicateurGroup == null)
        {
            $indicateurGroups = $this->getDoctrine()->getRepository('AppBundle:IndicateurGroup')
                ->createQueryBuilder('ig')
                ->where('ig.client IS NULL')
                ->andWhere('ig.dossier IS NULL')
                ->orderBy('ig.libelle')
                ->getQuery()
                ->getResult();

            return $this->render('IndicateurBundle:AffichageV2:parametres.html.twig',array('dossier'=>$dossier, 'indicateurGroups' => $indicateurGroups));
        }

        $packs = $this->getDoctrine()->getRepository('AppBundle:IndicateurPack')->getPacksInGroups($indicateurGroup,null,$dossier,null,false);

        return $this->render('IndicateurBundle:AffichageV2:pack.html.twig',
            array('packs'=>$packs,
                'datepicker'=>Boost::getDatePickerPopOverV2(Boost::getExercices(),Boost::getMois($dossier->getCloture())),
                'graphes'=>$graphes, 'count_column'=>intval($post->get('count_column')),
                'height'=>floatval($post->get('height')),
                'date_anciennete'=>$this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')->getDateCalculAnciennete($dossier,$dateNow->format('Y'))));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indicateurAction(Request $request)
    {
        error_reporting(E_ERROR);
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        if(is_bool($dossier) || is_bool($indicateur)) return new Response('security');

        $isEtat = (intval($post->get('is_etat')) == 1);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);

        $exercicesTemps = json_decode($post->get('exercices'));
        $exercices = $exercicesTemps;

        if($isEtat)
        {
            $etat = $this->getDoctrine()->getRepository('AppBundle:Etat')->find($indicateur);
            $indicateur = $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getEtatRegimeFiscal($etat,$dossier);
            $exercices = $exercicesTemps;
        }
        else
        {
            /** @var Indicateur $indicateur */
            $indicateur = $this->getDoctrine()->getRepository('AppBundle:Indicateur')->find($indicateur);
            if (intval($indicateur->getShowExerciceClos()) != 1)
            {
                $exercices = [];
                foreach ($exercicesTemps as $exercicesTemp)
                {
                    $isClosed = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')
                        ->exerciceIsClotured($dossier,$exercicesTemp);
                    if ($isClosed) $exercices[] = $exercicesTemp;
                }
            }
        }

        $code_graphe = $post->get('code_graphe');
        $typeGraphe = $this->getDoctrine()->getRepository('AppBundle:TypeGraphe')->getByCode($code_graphe);
        $analyse = intval($post->get('analyse'));
        //periodes

        $moiss = json_decode($post->get('moiss'));
        $periodes = json_decode($post->get('periodes'));

        $periodesLast = [];
        foreach ($periodes as $periode)
        {
            $periodesLast[] = $periode->libelle;
        }
        if(!$isEtat) $this->getDoctrine()->getRepository('AppBundle:IndicateurLastShow')->setLast($dossier,$indicateur,$typeGraphe,$exercices,$analyse,$periodesLast);

        //anciennetes
        $dateAnciennete = Boost::getDateByString($post->get('date_anciennete'),'-',2);
        $anciennetes = json_decode($post->get('anciennetes'));

        $resultat = $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getResultV4($dossier,$indicateur,$exercices,$moiss,$code_graphe,$analyse,$periodes,$dateAnciennete,$anciennetes,$isEtat,$this->getUser());

        //return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=> $resultat]);

        if($code_graphe == 'VAL') return $this->render('IndicateurBundle:AffichageV2:valeur.html.twig',array('res'=>$resultat));
        else return new JsonResponse($resultat);

        $res = array($dossier,$indicateur,$exercices,$moiss,$code_graphe,$analyse,$periodes,$dateAnciennete,$anciennetes,$isEtat,$this->getUser());

        return $this->render('IndicateurBundle:AffichageV2:test.html.twig',array('test'=>$res,'test2'=>null));
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function detailsAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        $indicateur = Boost::deboost($post->get('indicateur'),$this);
        $exercices = json_decode($post->get('exercices'));
        $periodes = json_decode($post->get('periodes'));
        $moiss = json_decode($post->get('moiss'));
        $category = $post->get('category');
        $nm = $post->get('nm');
        $isTd = (intval($post->get('is_td')) == 1);
        $row = intval($post->get('row'));
        $col = intval($post->get('col'));
        $isEtat = (intval($post->get('is_etat')) == 1);

        if(is_bool($dossier) || is_bool($indicateur)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        $indicateur = ($isEtat) ?
            $this->getDoctrine()->getRepository('AppBundle:EtatRegimeFiscal')->getById($indicateur) :
            $this->getDoctrine()->getRepository('AppBundle:Indicateur')->getById($indicateur);

        $code_graphe = '';
        $analyse = intval($post->get('analyse'));
        $dateAnciennete = new DateTime();
        $anciennetes = [];

        $result = $this->getDoctrine()->getRepository('AppBundle:Indicateur')
            ->getDetailsV4($dossier,$indicateur,$exercices,$moiss,$code_graphe,$analyse,$periodes,$dateAnciennete,$anciennetes,$category,$nm,$isTd,$row,$col,$isEtat);

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function parametreAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $indicateurGroup = Boost::deboost($request->request->get('indicateur_group'),$this);

        if(is_bool($dossier) || is_bool($indicateurGroup)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $indicateurGroup = $this->getDoctrine()->getRepository('AppBundle:IndicateurGroup')->find($indicateurGroup);

        $em = $this->getDoctrine()->getManager();
        $indicateurGroupSpec = new IndicateurSpecGroup();

        $indicateurGroupSpec->setDossier($dossier);
        $indicateurGroupSpec->setIndicateurGroup($indicateurGroup);

        $em->persist($indicateurGroupSpec);
        $em->flush();

        return new Response(1);
    }

    /**
     * @return Response
     */
    public function clesAction()
    {
        $cles = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbCle')
            ->getAll();
        return $this->render('IndicateurBundle:Tb:cles.html.twig',[
            'cles' => $cles
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function cleSaveAction(Request $request)
    {
        $indicateurTbCle = Boost::deboost($request->request->get('id'),$this);
        if(is_bool($indicateurTbCle)) return new Response('security');
        $indicateurTbCle = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbCle')
            ->find($indicateurTbCle);
        $cle = $request->request->get('cle');
        $action = intval($request->request->get('action'));
        $sens = intval($request->request->get('sens'));

        $em = $this->getDoctrine()->getManager();
        $add = false;
        if (!$indicateurTbCle)
        {
            $indicateurTbCle = new IndicateurTbCle();
            $add = true;
        }
        $indicateurTbCle
            ->setCle($cle)
            ->setSens($sens);

        if ($add) $em->persist($indicateurTbCle);
        elseif ($action == 1) $em->remove($indicateurTbCle);

        try
        {
            $em->flush();
        }
        catch (UniqueConstraintViolationException $uex)
        {
            return new Response(-1);
        }

        if ($add)
        {
            return $this->render('IndicateurBundle:Tb:cle-new-tr.html.twig',['cle' => $indicateurTbCle]);
        }
        elseif ($action === 1)
        {
            return new Response(1);
        }
        else
        {
            return new Response(0);
        }
    }

    public function occurenceDetailsAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $exercice = intval($request->request->get('exercice'));

        $indicateurTbCles = $this->getDoctrine()->getRepository('AppBundle:IndicateurTbCle')
            ->getAll();

        $res = [];
        foreach ($indicateurTbCles as $indicateurTbCle)
        {
            $ecritures = $this->getDoctrine()->getRepository('AppBundle:Ecriture')
                ->occurenceDetails($indicateurTbCle,$dossier,$exercice);

            foreach ($ecritures as $ecriture)
            {
                $res[] = (object)
                [
                    'id' => Boost::boost($ecriture->getId()),
                    'dat' => $ecriture->getDateEcr()->format('d/m/Y'),
                    'lib' => $ecriture->getLibelle(),
                    'deb' => $ecriture->getDebit(),
                    'cre' => $ecriture->getCredit(),
                    'cle' => $indicateurTbCle->getCle()
                ];
            }
        }

        return new JsonResponse($res);
    }

    public function exerciceStatusAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $exercices = json_decode($request->request->get('exercices'));
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
        $clotures = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')->exercicesAreClotured($dossier, $exercices);

        $results = [];
        foreach ($clotures as $exercice => $cloture)
        {
            /** @var HistoriqueUpload $historiqueUpload */
            $historiqueUpload = $cloture;
            $results[] = (object)
            [
                'exo' => $exercice,
                'res' => $historiqueUpload ? $historiqueUpload->getResultat() : '',
                'sta' => $historiqueUpload ? $historiqueUpload->getCloture() : 0,
                'dup' => $historiqueUpload ? $historiqueUpload->getDateUpload()->format('d/m/Y') : '',
                'dve' => ($historiqueUpload && $historiqueUpload->getDateVerification()) ? $historiqueUpload->getDateVerification()->format('d/m/Y') : ''
            ];
        }

        return new JsonResponse($results);
    }

    public function indicateurCommentaireAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $indicateur = Boost::deboost($request->request->get('indicateur'),$this);

        if(is_bool($dossier) || is_bool($indicateur)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $indicateur = $this->getDoctrine()->getRepository('AppBundle:Indicateur')
            ->find($indicateur);

        $indicateurCommentaire = $this->getDoctrine()->getRepository('AppBundle:IndicateurCommentaire')
            ->getIndicateurCommentaire($dossier,$indicateur);

        $isModifiable = $this->get('security.authorization_checker')->isGranted('ROLE_CLIENT_RESP');

        return $this->render('IndicateurBundle:AffichageV2:indicateur_commentaire.html.twig',[
            'indicateurCommentaire' => $indicateurCommentaire,
            'isModifiable' => $isModifiable
        ]);
    }

    public function indicateurCommentaireChangeAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $indicateur = Boost::deboost($request->request->get('indicateur'),$this);

        if(is_bool($dossier) || is_bool($indicateur)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $indicateur = $this->getDoctrine()->getRepository('AppBundle:Indicateur')
            ->find($indicateur);
        $indicateurCommentaire = $this->getDoctrine()->getRepository('AppBundle:IndicateurCommentaire')
            ->getIndicateurCommentaire($dossier,$indicateur);

        $commentaire = trim($request->request->get('commentaire'));

        $em = $this->getDoctrine()->getManager();

        if ($indicateurCommentaire)
        {
            if ($commentaire == '') $em->remove($indicateurCommentaire);
            else
                $indicateurCommentaire
                    ->setCommentaire($commentaire)
                    ->setUtilisateur($this->getUser());
        }
        else
        {
            $indicateurCommentaire = new IndicateurCommentaire();
            $indicateurCommentaire
                ->setDossier($dossier)
                ->setIndicateur($indicateur)
                ->setCommentaire($commentaire)
                ->setDateModif(new \DateTime())
                ->setUtilisateur($this->getUser());

            $em->persist($indicateurCommentaire);
        }
        
        $em->flush();
        return new Response(1);
    }

    public function exportAction(Request $request)
    {
        $extension = trim($request->request->get('extension'));

        $dossier = Boost::deboost($request->request->get('exp_dossier'),$this);
        $indicateur = Boost::deboost($request->request->get('exp_indicateur'),$this);
        if(is_bool($dossier) || is_bool($indicateur)) return new Response('security');

        /** @var Dossier $dossier */
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        /** @var Indicateur $indicateur */
        $indicateur = $this->getDoctrine()->getRepository('AppBundle:Indicateur')
            ->find($indicateur);

        $datas = json_decode(urldecode($request->request->get('datas')));
        $headers = json_decode(urldecode($request->request->get('headers')));
        $dateNow = new \DateTime();

        $commentaire = '';
        $indicateurCommentaire = $this->getDoctrine()->getRepository('AppBundle:IndicateurCommentaire')
            ->getIndicateurCommentaire($dossier,$indicateur);
        if ($indicateurCommentaire) $commentaire = trim($indicateurCommentaire->getCommentaire());

        $name =
            $dossier->getSite()->getClient()->getNom() . ' ' .
            $dossier->getNom() . ' ' .
            $indicateur->getLibelle() .
            '.'.$extension;

        if($extension == 'xls')
        {
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
            $backgroundTitle = '808080';
            $phpExcelObject->getProperties()->setCreator('Scr')
                ->setLastModifiedBy($this->getUser()->getEmail())
                ->setTitle("Office 2005 XLSX Test Document")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription('Documment générer par PHP')
                ->setKeywords("office 2005 openxml php")
                ->setCategory('Export Indicateur');
            $sheet = $phpExcelObject->setActiveSheetIndex(0);

            /*Titre*/
            $sheet->setCellValue('A1', $indicateur->getLibelle())
                ->setCellValue('A2','Client')
                ->setCellValue('B2',$dossier->getSite()->getClient()->getNom())
                ->setCellValue('A3','Site')
                ->setCellValue('B3',$dossier->getSite()->getNom())
                ->setCellValue('A4','Dossier')
                ->setCellValue('B4',$dossier->getNom())
                ->setCellValue('A5','Editer le')
                ->setCellValue('B5',$dateNow->format('d-m-Y'));

            $row = 7;
            foreach ($headers as $header)
            {
                $col = 'A';
                foreach ($header as $cell)
                {
                    $sheet->setCellValue($col.$row, $cell->v);
                    $col++;
                }
                $row++;
            }

            foreach ($datas as $header)
            {
                $col = 'A';
                foreach ($header as $cell)
                {
                    $sheet->setCellValue($col.$row, $cell->v);
                    $col++;
                }
                $row++;
            }

            if ($commentaire == '')
            {
                $row += 2;
                $sheet->setCellValue('A'.$row, $commentaire);
            }

            $phpExcelObject->getActiveSheet()->setTitle('Indicateur');
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
        elseif ($extension == 'pdf')
        {
            $html = $this->renderView('IndicateurBundle:AffichageV2:export.html.twig',[
                'indicateur' => $indicateur,
                'dossier' => $dossier,
                'headers' => $headers,
                'datas' => $datas,
                'commentaire' => $commentaire
            ]);
            $html2pdf = $this->get('html2pdf_factory')->create('L', 'A4', 'fr');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);
            $html2pdf->Output($name, 'D');
            return new Response($name);
        }
    }
}