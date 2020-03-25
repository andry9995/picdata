<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 28/08/2019
 * Time: 11:31
 */

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\BanqueSousCategorieAutre;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\ImageFlague;
use AppBundle\Entity\Releve;
use AppBundle\Entity\TvaImputationControle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LettrageDesiquilibreController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function showAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        /** @var Releve $releve */
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);

        $total = 0;
        $relevePasses = [];

        $methodeCompta =
            $this->getDoctrine()->getRepository('AppBundle:MethodeComptable')->getMethodeDossier($releve->getBanqueCompte()->getDossier());

        if (!$releve->getBanqueCompte()->getJournalDossier())
        {
            return new Response(-1);
        }

        $soeurs = null;
        if ($releve->getImageFlague())
            $soeurs = $this->getDoctrine()->getRepository('AppBundle:ImageFlague')
                ->getSoeurs($releve->getImageFlague());

        $results = [];

        if ($soeurs)
        {
            foreach ($soeurs->tic as $image)
            {
                foreach ($image as $tic)
                {
                    $results[] = $this->getDoctrine()->getRepository('AppBundle:Releve')
                        ->getEcriture($releve,null,$tic);
                }
            }

            foreach ($soeurs->rel as $image)
            {
                foreach ($image as $rel)
                {
                    $results[] = $this->getDoctrine()->getRepository('AppBundle:Releve')
                        ->getEcriture($releve,$rel);
                }
            }

            foreach ($soeurs->bsca as $image)
            {
                foreach ($image as $bsca)
                {
                    $results[] = $this->getDoctrine()->getRepository('AppBundle:Releve')
                        ->getEcriture($releve,null,null,$bsca);
                }
            }
        }
        else $results[] = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->getEcriture($releve,$releve);

        return $this->render('BanqueBundle:ReleveBanque2:lettrage-desiquilibre.html.twig',[
            'results' => $results,
            'releve' => $releve
        ]);
    }

    public function searchAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'),$this);
        if(is_bool($releve)) return new Response('security');
        /** @var Releve $releve */
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);

        $dossier = $releve->getBanqueCompte()->getDossier();
        /* 0: PICDOC  -  1: Relevé  -  2: Banque Autre */
        $type = intval($request->request->get('type'));
        $nomImage = trim($request->request->get('image'));
        $montant = floatval($request->request->get('montant'));

        $results = [];
        if ($type === 1)
        {
            $releves = $this->getDoctrine()->getRepository('AppBundle:Releve')
                ->searchByPieceMontant($dossier,$nomImage,$montant,true);

            foreach ($releves as $rel)
            {
                $results[] = $this->getDoctrine()->getRepository('AppBundle:Releve')
                    ->getEcriture($releve,$rel);
            }
        }
        elseif ($type === 2)
        {
            $banqueSousCategorieAutres = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
                ->searchByPieceMontant($dossier,$nomImage,$montant, true);

            foreach ($banqueSousCategorieAutres as $banqueSousCategorieAutre)
            {
                $results[] = $this->getDoctrine()->getRepository('AppBundle:Releve')
                    ->getEcriture($releve,null,null,$banqueSousCategorieAutre);
            }
        }
        else
        {
            $tvaImputationControles = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                ->searchByPieceMontant($dossier,$nomImage,$montant, true);

            foreach ($tvaImputationControles as $tvaImputationControle)
            {
                $results[] = $this->getDoctrine()->getRepository('AppBundle:Releve')
                    ->getEcriture($releve,null,$tvaImputationControle);
            }
        }

        return $this->render('BanqueBundle:ReleveBanque2:lettrage-desiquilibre-new.html.twig',[
            'results' => $results
        ]);
    }

    public function equilibrerAction(Request $request)
    {
        $imageFlague = Boost::deboost($request->request->get('image_flague'),$this);
        if(is_bool($imageFlague)) return new Response('security');
        $imageFlague = $this->getDoctrine()->getRepository('AppBundle:ImageFlague')
            ->find($imageFlague);

        $oldImagesFlagues = [];

        $em = $this->getDoctrine()->getManager();
        $items = json_decode($request->request->get('items'));

        if (count($items) > 0)
        {
            $imageFlagueNew = new ImageFlague();
            $imageFlagueNew
                ->setPcc($imageFlague ? $imageFlague->getPcc() : null)
                ->setTiers($imageFlague ? $imageFlague->getTiers() : null)
                ->setLettre($imageFlague ? $imageFlague->getLettre() : null)
                ->setDateDevalidation($imageFlague ? $imageFlague->getDateDevalidation() : null)
                ->setDateCreation(new \DateTime());

            $em->persist($imageFlagueNew);
            $em->flush();

            foreach ($items as $item)
            {
                $type = intval($item->type);

                if ($type == 0)
                {
                    $releve = Boost::deboost($item->id,$this);
                    if(is_bool($releve)) return new Response('security');

                    /** @var Releve $releve */
                    $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
                        ->find($releve);

                    if ($releve->getImageFlague())
                    {
                        $k = 'r-' . $releve->getImageFlague()->getId();
                        if (!array_key_exists($k, $oldImagesFlagues))
                            $oldImagesFlagues[$k] = $releve->getImageFlague();
                    }

                    $releve->setImageFlague($imageFlagueNew);
                }
                elseif ($type == 1)
                {
                    $tvaImputationControle = Boost::deboost($item->id,$this);
                    if(is_bool($tvaImputationControle)) return new Response('security');

                    /** @var TvaImputationControle $tvaImputationControle */
                    $tvaImputationControle = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
                        ->find($tvaImputationControle);

                    if ($tvaImputationControle->getImageFlague())
                    {
                        $k = 'tic-' . $tvaImputationControle->getImageFlague()->getId();
                        if (!array_key_exists($k, $oldImagesFlagues))
                            $oldImagesFlagues[$k] = $tvaImputationControle->getImageFlague();
                    }

                    $tvaImputationControle->setImageFlague($imageFlagueNew);
                }
                elseif ($type == 2)
                {
                    $bsca = Boost::deboost($item->id,$this);
                    if(is_bool($bsca)) return new Response('security');

                    /** @var BanqueSousCategorieAutre $bsca */
                    $bsca = $this->getDoctrine()->getRepository('AppBundle:BanqueSousCategorieAutre')
                        ->find($bsca);

                    if ($bsca->getImageFlague())
                    {
                        $k = 'bsca-' . $bsca->getImageFlague()->getId();
                        if (!array_key_exists($k, $oldImagesFlagues))
                            $oldImagesFlagues[$k] = $bsca->getImageFlague();
                    }

                    $bsca->setImageFlague($imageFlagueNew);
                }
            }
        }

        if ($imageFlague) $em->remove($imageFlague);
        foreach ($oldImagesFlagues as $oldImagesFlague) $em->remove($oldImagesFlague);

        $em->flush();

        return new Response(1);
    }
}