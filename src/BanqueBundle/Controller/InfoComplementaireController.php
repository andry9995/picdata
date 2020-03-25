<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/05/2019
 * Time: 09:00
 */

namespace BanqueBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\Banque;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\CfonbBanque;
use AppBundle\Entity\CfonbCode;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Pcc;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InfoComplementaireController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function paramsAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $banqueId = Boost::deboost($request->request->get('banque'),$this);
        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);

        if(is_bool($dossier) || is_bool($banqueId) || is_bool($banqueCompte)) return new Response('security');
        /** @var Dossier $dossier */
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);
        /** @var Banque $banque */
        $banque = null;
        if ($banqueCompte)
            $banque = $banqueCompte->getBanque();
        else
            $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')
                ->find($banqueId);

        /** @var Banque[] $banques */
        $banques = [];
        if ($banque) $banques[] = $banque;
        else $banques = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->getBanques($dossier);

        return $this->render('BanqueBundle:InfoComplementaire:params.html.twig',[
            'banques' => $banques
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function cfonbAction(Request $request)
    {
        $banque = Boost::deboost($request->request->get('banque'),$this);
        if(is_bool($banque)) return new Response('security');
        $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')
            ->find($banque);
        $cfonbCodes = $this->getDoctrine()->getRepository('AppBundle:CfonbCode')
            ->getAlls();

        $cfonbBanques = $this->getDoctrine()->getRepository('AppBundle:CfonbBanque')
            ->cfonbBanques($banque,true);

        return $this->render('BanqueBundle:InfoComplementaire:cfonb.html.twig',[
            'cfonbCodes' => $cfonbCodes,
            'cfonbBanques' => $cfonbBanques
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function cfonbReleveSaveAction(Request $request)
    {
        $banque = Boost::deboost($request->request->get('banque'),$this);
        if(is_bool($banque)) return new Response('security');
        $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')
            ->find($banque);
        $cfonbs = json_decode($request->request->get('cfonbs'));
        $em = $this->getDoctrine()->getManager();

        foreach ($cfonbs as $cfonb)
        {
            $cfonbCode = Boost::deboost($cfonb->cfonb_code,$this);
            $cfonbBanque = Boost::deboost($cfonb->cfonb_banque,$this);
            if(is_bool($banque) ||is_bool($cfonbBanque)) return new Response('security');
            /** @var CfonbCode $cfonbCode */
            $cfonbCode = $this->getDoctrine()->getRepository('AppBundle:CfonbCode')
                ->find($cfonbCode);
            /** @var CfonbBanque $cfonbBanque */
            $cfonbBanque = $this->getDoctrine()->getRepository('AppBundle:CfonbBanque')
                ->find($cfonbBanque);
            $cocher = (intval($cfonb->etat) == 1);

            if ($cocher)
            {
                if (!$cfonbBanque)
                {
                    $cfonbBanque = new CfonbBanque();
                    $cfonbBanque
                        ->setBanque($banque)
                        ->setCfonbCode($cfonbCode);

                    $em->persist($cfonbBanque);
                }
            }
            elseif ($cfonbBanque) $em->remove($cfonbBanque);
        }

        try
        {
            $em->flush();
            return new Response(1);
        }
        catch (UniqueConstraintViolationException $ex)
        {
            return new Response(0);
        }
    }

    public function modeImportShowAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $banque = Boost::deboost($request->request->get('banque'),$this);
        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);

        if(is_bool($dossier) || is_bool($banque) || is_bool($banqueCompte)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        /*$banque = $this->getDoctrine()->getRepository('AppBundle:Banque')
            ->find($banque);
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);*/

        $banqueComptes = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->getBanqueCompteByDossier($dossier);

        /*$banqueComptes = $banqueCompte ? [$banqueCompte] :
            $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->getBanquesComptes($dossier,$banque);*/

        $pccBanques = $this->getDoctrine()->getRepository('AppBundle:Pcc')
            ->getPccBanque($dossier);

        $journalDossiers = $this->getDoctrine()->getRepository('AppBundle:JournalDossier')
            ->getJournaux($dossier);

        $banques = $this->getDoctrine()->getRepository('AppBundle:Banque')
            ->getAll();

        return $this->render('BanqueBundle:ReleveBanque2:mode-import.html.twig',[
            'banqueComptes' => $banqueComptes,
            'pccBanques' => $pccBanques,
            'banques' => $banques,
            'journalDossiers' => $journalDossiers
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function banqueCompteParamsAction(Request $request)
    {
        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
        if(is_bool($banqueCompte)) return new Response('security');
        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);

        /** @var Pcc[] $pccUseds */
        $pccUseds = $this->getDoctrine()->getRepository('AppBundle:Pcc')
            ->getPccBanqueUsed($banqueCompte->getDossier());

        /** @var array $aEnlever */
        $aEnlever = [];
        foreach ($pccUseds as $pcc)
        {
            if ($banqueCompte->getPcc() && $pcc->getId() == $banqueCompte->getPcc()->getId()) continue;
            $aEnlever[] = intval($pcc->getId());
        }

        $journalDossierUseds = $this->getDoctrine()->getRepository('AppBundle:JournalDossier')
            ->getJournauxBanqueUsed($banqueCompte->getDossier());

        /** @var array $aEnleverJds */
        $aEnleverJds = [];
        foreach ($journalDossierUseds as $journalDossierUsed)
        {
            if ($banqueCompte->getJournalDossier() && $journalDossierUsed->getId() == $banqueCompte->getJournalDossier()->getId()) continue;
            $aEnleverJds[] = intval($journalDossierUsed->getId());
        }

        return new JsonResponse((object)[
            'mi' => $banqueCompte->getModeSaisie(),
            'pcc' => $banqueCompte->getPcc() ? $banqueCompte->getPcc()->getId() : 0,
            'a_enlever' => $aEnlever,
            'aEnleverJds' => $aEnleverJds,
            'jd' => $banqueCompte->getJournalDossier() ? $banqueCompte->getJournalDossier()->getId() : 0
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function banqueCompteParamSaveAction(Request $request)
    {
        $banqueCompte = Boost::deboost($request->request->get('banque_compte'),$this);
        if(is_bool($banqueCompte)) return new Response('security');
        /** @var BanqueCompte $banqueCompte */
        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->find($banqueCompte);

        $type = intval($request->request->get('type'));
        $em = $this->getDoctrine()->getManager();
        $val = intval($request->request->get('val'));

        if ($type == 0)
        {
            $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($val);
            $banqueCompte->setPcc($pcc);
        }
        elseif ($type == 2)
        {
            $journalDossier = $this->getDoctrine()->getRepository('AppBundle:JournalDossier')->find($val);
            $banqueCompte->setJournalDossier($journalDossier);
        }
        else $banqueCompte->setModeSaisie($val);

        $em->flush();
        return new Response(1);
    }

    public function banqueCompteAddAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $compte = trim($request->request->get('compte'));

        $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->getOneByDossierCompte($dossier,$compte);

        if ($banqueCompte)
        {
            return new Response(-1);
        }
        else
        {
            $banque = $this->getDoctrine()->getRepository('AppBundle:Banque')
                ->getOneByCode(substr($compte,0,5));
            if (!$banque)
            {
                return new Response(-2);
            }

            $em = $this->getDoctrine()->getManager();
            $banqueCompte = new BanqueCompte();
            $banqueCompte
                ->setDossier($dossier)
                ->setStatus(1)
                ->setNumcompte($compte)
                ->setBanque($banque);

            $em->persist($banqueCompte);
            $em->flush();

            return $this->render('BanqueBundle:ReleveBanque2:mode-import-tr.html.twig',[
                'banqueCompte' => $banqueCompte
            ]);
        }

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => $dossier
        ]);
    }
}