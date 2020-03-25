<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 19/07/2019
 * Time: 15:41
 */

namespace BanqueBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\BanqueType;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Pcg;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveImputation;
use AppBundle\Entity\ReleveInstruction;
use AppBundle\Entity\Tiers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImputationController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $type = intval($request->request->get('type'));
        $adds = json_decode($request->request->get('adds'));
        /** @var BanqueType $banqueType */
        $banqueType = null;
        $banqueTypes = $this->getDoctrine()->getRepository('AppBundle:BanqueType')
            ->getBanqueTypes();

        /** @var Releve $releve */
        $releve = null;

        if ($type == 0)
        {
            $rel = Boost::deboost($adds->releve,$this);
            $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
                ->find($rel);
            $releveInstruction = $this->getDoctrine()->getRepository('AppBundle:ReleveInstruction')
                ->getByReleve($releve);

            if ($releveInstruction)
                $banqueType = $releveInstruction->getBanqueType();
        }

        if (!$banqueType) $banqueType = $banqueTypes[0];

        return $this->render('BanqueBundle:Imputation:imputation.html.twig',[
            'banqueTypes' => $banqueTypes,
            'banqueType' => $banqueType,
            'releve' => $releve,
            'type' => $type
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trsAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $releve = Boost::deboost($request->request->get('releve'),$this);
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);
        $banqueType = Boost::deboost($request->request->get('banque_type'),$this);
        $banqueType = $this->getDoctrine()->getRepository('AppBundle:BanqueType')
            ->find($banqueType);

        /** @var ReleveImputation[] $releveImputations */
        $releveImputations = [];
        if ($releve)
            $releveImputations = $this->getDoctrine()->getRepository('AppBundle:ReleveImputation')
                ->getReleveImputation($releve);

        $comptes = [];
        foreach ($releveImputations as $releveImputation)
        {
            if ($releveImputation->getTiers())
                $comptes[] = (object)
                [
                    't' => 1,
                    'id' => $releveImputation->getTiers()->getId(),
                    'tc' => $releveImputation->getType(),
                    'm' => $releveImputation->getDebit() - $releveImputation->getCredit()
                ];
            else
                $comptes[] = (object)
                [
                    't' => 0,
                    'id' => $releveImputation->getPcc()->getId(),
                    'tc' => $releveImputation->getType(),
                    'm' => $releveImputation->getDebit() - $releveImputation->getCredit()
                ];
        }

        $banqueTypePcgs = $this->getDoctrine()->getRepository('AppBundle:BanqueTypePcg')
            ->getForBanqueType($banqueType);

        /** @var Pcg[] $bilanPcgs */
        $bilanPcgs = [];
        /** @var Pcg[] $tvaPcgs */
        $tvaPcgs = [];
        /** @var Pcg[] $resultatPcgs */
        $resultatPcgs = [];

        foreach ($banqueTypePcgs as $banqueTypePcg)
        {
            if ($banqueTypePcg->getType() == 2)
                $bilanPcgs[] = $banqueTypePcg->getPcg();
            elseif ($banqueTypePcg->getType() == 1)
                $tvaPcgs[] = $banqueTypePcg->getPcg();
            elseif ($banqueTypePcg->getType() == 0)
                $resultatPcgs[] = $banqueTypePcg->getPcg();
        }

        $bilans = $this->getDoctrine()->getRepository('AppBundle:Pcc')
            ->getCompteByPcgs($dossier,$bilanPcgs,true);
        $tvas = $this->getDoctrine()->getRepository('AppBundle:Pcc')
            ->getCompteByPcgs($dossier,$tvaPcgs,true);
        $resultats = $this->getDoctrine()->getRepository('AppBundle:Pcc')
            ->getCompteByPcgs($dossier,$resultatPcgs,true);

        return $this->render('BanqueBundle:Imputation:tr-compte.html.twig',[
            'bilans' => $bilans,
            'tvas' => $tvas,
            'resultats' => $resultats,
            'comptes' => $comptes
        ]);
    }

    public function saveAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        $banqueType = Boost::deboost($request->request->get('banque_type'),$this);
        $imputations = json_decode($request->request->get('imputations'));
        if(is_bool($releve) || is_bool($banqueType)) return new Response('security');

        $em = $this->getDoctrine()->getManager();

        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);
        $banqueType = $this->getDoctrine()->getRepository('AppBundle:BanqueType')
            ->find($banqueType);

        /** @var ReleveInstruction $releveInstruction */
        $releveInstruction = $this->getDoctrine()->getRepository('AppBundle:ReleveInstruction')
            ->getByReleve($releve);

        if (!$releveInstruction)
        {
            $releveInstruction = new ReleveInstruction();
            $releveInstruction
                ->setReleve($releve)
                ->setBanqueType($banqueType);
            $em->persist($releveInstruction);
        }
        else $releveInstruction->setBanqueType($banqueType);

        $releveImputations = $this->getDoctrine()->getRepository('AppBundle:ReleveImputation')
            ->getImputation($releve);

        foreach ($releveImputations as $relImputation)
            $em->remove($relImputation);

        $releve->setEcritureChange(count($imputations) > 0 ? 1 : 0);

        foreach ($imputations as $imputation)
        {
            //{ m:montant, c:compte, t:type, type_compte:type_compte }
            $typeCompte = intval($imputation->type_compte);
            $type = intval($imputation->t);
            $compte = Boost::deboost($imputation->c,$this);

            //0: bilan pcc, 1: tiers,  2: resultat, 3: tva
            if ($typeCompte == 0 && $type == 1) $typeCompte = 1;

            /** @var Tiers $tiers */
            $tiers = null;
            /** @var Pcc $pcc */
            $pcc = null;

            $m = floatval($imputation->m);
            $debit = ($m > 0) ? $m : 0;
            $credit = ($m < 0) ? abs($m) : 0;

            if ($type == 1)
                $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')
                    ->find($compte);
            else
                $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')
                    ->find($compte);

            $releveImputation = new ReleveImputation();
            $releveImputation
                ->setReleve($releve)
                ->setTiers($tiers)
                ->setPcc($pcc)
                ->setDebit($debit)
                ->setCredit($credit)
                ->setType($typeCompte);

            $em->persist($releveImputation);
        }

        $em->flush();
        return new Response(1);
    }
}