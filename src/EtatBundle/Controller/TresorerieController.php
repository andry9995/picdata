<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 16/10/2019
 * Time: 15:56
 */

namespace EtatBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TresorerieController extends Controller
{
    public function indexAction()
    {
        return $this->render('EtatBundle:Tresorerie/show:index.html.twig',[

        ]);
    }

    public function tresorerieAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        //0: Exercice ; 1: Mois ; 2: Jour
        $base = intval($request->request->get('base'));
        $exercice = intval($request->request->get('exercice'));
        $moisSelected = intval($request->request->get('mois'));

        $results = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->tresorerie($dossier,$exercice,$base);

        $banqueComptes = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->getBanquesComptes($dossier);

        $soldeDebutExercice = 0;

        foreach ($banqueComptes as $banqueCompte)
        {
            $soldeDebut = $this->getDoctrine()->getRepository('AppBundle:Releve')->getSolde($banqueCompte,$exercice);
            $soldeDebutExercice += $soldeDebut;
        }

        $encaissements = [];
        $decaissements = [];
        $categories = [];
        $soldes = [];
        if ($base == 1)
        {
            foreach ($results->mois as $key => $mois)
            {
                $categories[] = $mois;
                $encaissements[] = floatval((array_key_exists($mois,$results->credits)) ? $results->credits[$mois] : 0);
                $decaissements[] = floatval((array_key_exists($mois,$results->debits)) ? $results->debits[$mois] : 0);
                $soldes[] = $soldeDebutExercice;

                $soldeDebutExercice +=
                    -floatval((array_key_exists($mois,$results->debits)) ? $results->debits[$mois] : 0) +
                    floatval((array_key_exists($mois,$results->credits)) ? $results->credits[$mois] : 0);
            }
        }
        elseif ($base == 2)
        {
            /** @var \DateTime $depart */
            $depart = $results->dates->d;
            /** @var \DateTime $fin */
            $fin = $results->dates->c;
            /** @var \DateTime $d */
            $d = clone $depart;

            while ($d < $fin)
            {
                $cat = $d->format('Y-m-d');
                $debit = floatval((array_key_exists($cat,$results->debits)) ? $results->debits[$cat] : 0);
                $credit = floatval((array_key_exists($cat,$results->credits)) ? $results->credits[$cat] : 0);
                $soldeDebutExercice +=
                    -$debit +
                    $credit;

                if ($d->format('Ymd') >= $depart->format('Ymd') && $d->format('Ymd') <= $fin->format('Ymd'))
                {
                    if (intval($d->format('m')) == $moisSelected)
                    {
                        $categories[] = $d->format('d/m');
                        $encaissements[] = $credit;
                        $decaissements[] = $debit;
                        $soldes[] = $soldeDebutExercice;
                    }
                }
                $d->add(new \DateInterval('P1D'));
            }
        }

        $series = [
            (object)
            [
                'type' => 'column',
                'name' => 'Encaissement réel',
                'data' => $encaissements,
                'stack' => 'encaissement'
            ],

            (object)
            [
                'type' => 'column',
                'name' => 'Décaissement réel',
                'data' => $decaissements,
                'stack' => 'decaissement'
            ]
        ];

        /*return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => $series
        ]);*/

        return new JsonResponse((object)[
            'categories' => $categories,
            'series' => $series,
            'solde' => (object)
            [
                'type' => 'spline',
                'name' => 'Solde',
                'data' => $soldes
            ],
        ]);

        /*type: 'column',
        name: 'Jane',
        data: [3, 2, 1, 3, 4],
        stack: 'male'*/
    }

    public function moisDossierAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $cloture = 12;
        if ($dossier->getCloture() && $dossier->getCloture() > 0)
        {
            $cloture = $dossier->getCloture();
            if ($cloture > 12) $cloture -= 12;
        }

        $mois = Boost::getMois($cloture);
        $moisEncours = intval((new \DateTime())->format('m'));

        return $this->render('EtatBundle:Tresorerie/show:li.mois.html.twig',[
            'mois' => $mois,
            'moisEncours' => $moisEncours
        ]);
    }
}