<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 11/03/2020
 * Time: 11:16
 */

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveExt;
use AppBundle\Entity\Tiers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NonLettrableController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function showAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'), $this);
        $cleDossierExt = Boost::deboost($request->request->get('cle_dossier_ext'), $this);
        if(is_bool($releve) || is_bool($cleDossierExt)) return new Response('security');
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);
        $cleDossierExt = $this->getDoctrine()->getRepository('AppBundle:CleDossierExt')
            ->find($cleDossierExt);

        /** @var ReleveExt $releveExt */
        $releveExt = null;

        if ($cleDossierExt)
            $releveExt = $this->getDoctrine()->getRepository('AppBundle:ReleveExt')
                ->findOneBy([
                    'releve' => $releve,
                    'cleDossierExt' => $cleDossierExt
                ]);

        return $this->render('BanqueBundle:NonLettrable:liste.html.twig',[
            'releveExt' => $releveExt,
            'releve' => $releve
        ]);
    }

    public function listeAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'), $this);
        $releveExt = Boost::deboost($request->request->get('releve_ext'), $this);

        if(is_bool($releve) || is_bool($releveExt)) return new Response('security');
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);
        $releveExt = $this->getDoctrine()->getRepository('AppBundle:ReleveExt')
            ->find($releveExt);

        $nonLettrables = $releveExt ?
            json_decode($releveExt->getNonLettrable()) :
            json_decode($releve->getNonLettrable());

        $images = $this->getDoctrine()->getRepository('AppBundle:Image')
            ->getImagesByIds($nonLettrables);

        $tvaImputationControles = $this->getDoctrine()->getRepository('AppBundle:TvaImputationControle')
            ->getTvaImputationControleByImages($images, 0, true);

        $results = [];

        foreach ($tvaImputationControles as $tvaImputationControle)
        {
            $key = $tvaImputationControle->getImage()->getId();

            /** @var Pcc $bilan */
            $bilan = $tvaImputationControle->getPccBilan();
            /** @var Tiers $bilanTiers */
            $bilanTiers = $tvaImputationControle->getTiers();
            /** @var Pcc $tva */
            $tva = $tvaImputationControle->getPccTva();
            /** @var Pcc $resultat */
            $resultat = $tvaImputationControle->getPcc();

            $montant = $tvaImputationControle->getMontantTtc();

            if (array_key_exists($key, $results))
            {
                $results[$key]->m = $results[$key]->m + $montant;
                if ($bilan)
                    $results[$key]->b[] = (object)
                    [
                        'id' => Boost::boost($bilan->getId()),
                        'c' => $bilan->getCompte(),
                        'i' => $bilan->getIntitule(),
                        't' => 0
                    ];
                elseif ($bilanTiers)
                    $results[$key]->b[] = (object)
                    [
                        'id' => Boost::boost($bilanTiers->getId()),
                        'c' => $bilanTiers->getCompteStr(),
                        'i' => $bilanTiers->getIntitule(),
                        't' => 1
                    ];

                if ($tva)
                    $results[$key]->t[] = (object)
                    [
                        'id' => Boost::boost($tva->getId()),
                        'c' => $tva->getCompte(),
                        'i' => $tva->getIntitule(),
                        't' => 0
                    ];

                if ($resultat)
                    $results[$key]->r[] = (object)
                    [
                        'id' => Boost::boost($resultat->getId()),
                        'c' => $resultat->getCompte(),
                        'i' => $resultat->getIntitule(),
                        't' => 0
                    ];
            }
            else
            {
                $imputationControl = $this->getDoctrine()->getRepository('AppBundle:ImputationControle')
                    ->getImputationControle($tvaImputationControle);

                $separation = $this->getDoctrine()->getRepository('AppBundle:Separation')
                    ->getSeparationByImage($tvaImputationControle->getImage());

                $categorie = $separation->getCategorie();

                if (!$categorie && $separation->getSouscategorie())
                    $categorie = $separation->getSouscategorie()->getCategorie();
                if (!$categorie && $separation->getSoussouscategorie())
                    $categorie = $separation->getSoussouscategorie()->getSouscategorie()->getCategorie();

                $image = $tvaImputationControle->getImage();

                $libelle = $imputationControl ? $imputationControl->getRs() : '';
                if (trim($imputationControl->getNumFacture()) != '')
                    $libelle .= '-' . $imputationControl->getNumFacture();

                $res = (object)
                [
                    'id' => Boost::boost($image->getId()),
                    'b' => [],
                    't' => [],
                    'r' => [],
                    'm' => $montant,
                    'd' => ($imputationControl && $imputationControl->getDateFacture()) ? $imputationControl->getDateFacture()->format('Y-m-d') : '',
                    'c' => $categorie ? $categorie->getLibelleNew() : '',
                    'i' => (object)
                    [
                        'id' => Boost::boost($image->getId()),
                        'nom' => $image->getNom(),
                        'e' => $image->getExercice()
                    ],
                    'ty' => 0,
                    'l' => $libelle
                ];

                if ($bilan)
                    $res->b[] = (object)
                    [
                        'id' => Boost::boost($bilan->getId()),
                        'c' => $bilan->getCompte(),
                        'i' => $bilan->getIntitule(),
                        't' => 0
                    ];
                elseif ($bilanTiers)
                    $res->b[] = (object)
                    [
                        'id' => Boost::boost($bilanTiers->getId()),
                        'c' => $bilanTiers->getCompteStr(),
                        'i' => $bilanTiers->getIntitule(),
                        't' => 1
                    ];

                if ($tva)
                    $res->t[] = (object)
                    [
                        'id' => Boost::boost($tva->getId()),
                        'c' => $tva->getCompte(),
                        'i' => $tva->getIntitule(),
                        't' => 0
                    ];

                if ($resultat)
                    $res->r[] = (object)
                    [
                        'id' => Boost::boost($resultat->getId()),
                        'c' => $resultat->getCompte(),
                        'i' => $resultat->getIntitule(),
                        't' => 0
                    ];

                $results[$key] = $res;
            }
        }

        return new JsonResponse(array_values($results));

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
           'test' => array_values($results)
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function annulerAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'), $this);
        $releveExt = Boost::deboost($request->request->get('releve_ext'), $this);
        $image = Boost::deboost($request->request->get('image'), $this);
        if(is_bool($releve) || is_bool($releveExt) || is_bool($image)) return new Response('security');

        /** @var Releve $releve */
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);
        /** @var ReleveExt $releveExt */
        $releveExt = $this->getDoctrine()->getRepository('AppBundle:ReleveExt')
            ->find($releveExt);
        $image = $this->getDoctrine()->getRepository('AppBundle:Image')
            ->find($image);

        $idsNews = [];
        $idsOlds = json_decode($releveExt ? $releveExt->getNonLettrable() : $releve->getNonLettrable());

        foreach ($idsOlds as $idsOld)
        {
            if (intval($idsOld) != intval($image->getId()))
                $idsNews[] = intval($idsOld);
        }

        if ($releveExt)
            $releveExt->setNonLettrable(count($idsNews) == 0 ? '' : json_encode($idsNews));
        else
            $releve->setNonLettrable(count($idsNews) == 0 ? '' : json_encode($idsNews));

        $this->getDoctrine()->getManager()->flush();

        return new Response(1);
    }
}