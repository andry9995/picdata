<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 28/10/2019
 * Time: 08:18
 */

namespace EtatBundle\Controller;


use AppBundle\Controller\Boost;
use AppBundle\Entity\Tiers;
use AppBundle\Entity\TresoCategorie;
use AppBundle\Entity\TresoCategoriePcg;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DetailController extends Controller
{
    public function detailAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'), $this);
        if(is_bool($dossier)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $type = intval($request->request->get('type'));
        //0: Exercice ; 1: Mois ; 2: Jour
        $base = intval($request->request->get('base'));
        if ($base == 1) $format = 'Y-m';
        elseif ($base == 2) $format = 'Y-m-d';
        else $format = 'Y';

        $exercice = intval($request->request->get('exercice'));
        $mois = intval($request->request->get('mois'));

        $dates = $this->getDoctrine()->getRepository('AppBundle:TbimagePeriode')
            ->getAnneeMoisExercices($dossier,$exercice);

        $tresoCategories = $this->getDoctrine()->getRepository('AppBundle:TresoCategorie')
            ->getAll($type);

        $moisSelected = intval($request->request->get('mois'));

        $banqueComptes = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->getBanquesComptes($dossier);

        $categories = [];
        if ($base == 1)
        {
            foreach ($dates->ms as $key => $mois)
            {
                $categories[$mois] = $mois;
            }
        }
        elseif ($base == 2)
        {
            /** @var \DateTime $depart */
            $depart = $dates->d;
            /** @var \DateTime $fin */
            $fin = $dates->c;
            /** @var \DateTime $d */
            $d = clone $depart;

            while ($d < $fin)
            {
                if ($d->format('Ymd') >= $depart->format('Ymd') && $d->format('Ymd') <= $fin->format('Ymd'))
                {
                    if (intval($d->format('m')) == $moisSelected)
                    {
                        $categories[$d->format('Y-m-d')] = $d->format('d/m');
                    }
                }
                $d->add(new \DateInterval('P1D'));
            }
        }

        $tresos = [];
        foreach ($tresoCategories as $tresoCategorie)
        {
            $tresoCategoriePcgs = $this->getDoctrine()->getRepository('AppBundle:TresoCategoriePcg')
                ->getForTresoCategories($tresoCategorie);

            $pcgsIn = [];
            $pcgsOut = [];
            foreach ($tresoCategoriePcgs as $tresoCategoriePcg)
            {
                if($tresoCategoriePcg->getNegation() == 0) $pcgsIn[] = $tresoCategoriePcg->getPcg();
                else $pcgsOut[] = $tresoCategoriePcg->getPcg();
            }

            $pccComptes = [];
            $pccs = $this->getDoctrine()->getRepository('AppBundle:Pcc')->getPCCByPCG($pcgsIn,$dossier,$pcgsOut);
            foreach ($pccs as $pcc)
                $pccComptes[] = $pcc->getCompte();

            $tresos[] = (object)
            [
                'tc' => $tresoCategorie,
                'pccComptes' => $pccComptes
            ];
        }

        $intervals = [1,2,3,4,5,6,7,8,9,10,11,12];
        $results = [];
        foreach ($banqueComptes as $banqueCompte)
        {
            $journaux = $this->getDoctrine()->getRepository('AppBundle:Releve')
                ->getJournal($banqueCompte, $exercice, $intervals,false,$banqueCompte->getJournalDossier(),2,null,null,true,($type == 0) ? -1 : 1);

            $date = new \DateTime();
            $libelle = '';
            $imageNom = '';
            $imageId = Boost::boost(0);

            foreach ($journaux->datas as $data)
            {
                if (intval($data->isb) == 1)
                {
                    $date = \DateTime::createFromFormat('d/m/Y', $data->d);
                    $libelle = trim($data->l);
                    $imageNom = $data->i;
                    $imageId = $data->imi;
                }
                else
                {
                    $pcc = (intval($data->co->t) == 0) ?
                        $data->co->c :
                        $this->getDoctrine()->getRepository('AppBundle:Tiers')->getHisPcc($data->co->c);

                    /** @var Tiers $tiers */
                    $tiers = (intval($data->co->t) == 0) ? null : $data->co->c;
                    $tiersNom = $tiers ? $tiers->getCompteStr() : $pcc->getCompte();

                    $inCategorie = false;
                    foreach ($tresos as &$treso)
                    {
                        /** @var TresoCategorie $tresoCategorie */
                        $tresoCategorie = $treso->tc;
                        if (in_array($pcc->getCompte(),$treso->pccComptes))
                        {
                            $results[] = (object)
                            [
                                'd' => $date->format('d/m/Y'),
                                'l' => $libelle,
                                'i' => (object)
                                [
                                    'id' => $imageId,
                                    'n' => $imageNom
                                ],
                                'db' => $data->db,
                                'cr' => $data->cr,
                                'k_t' => $tiersNom,
                                'k_tc' => $tresoCategorie->getLibelle(),
                                'k_d' => $date->format($format)
                            ];

                            $inCategorie = true;
                        }
                    }

                    if (!$inCategorie)
                    {
                        $results[] = (object)
                        [
                            'd' => $date,
                            'l' => $libelle,
                            'i' => (object)
                            [
                                'id' => $imageId,
                                'nom' => $imageNom
                            ],
                            'db' => $data->db,
                            'cr' => $data->cr,
                            'k_t' => $tiersNom,
                            'k_tc' => 'Divers',
                            'k_d' => $date->format($format),
                            $date->format($format) => $data->cr - $data->db
                        ];
                    }
                }
            }
        }


        /*return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => [
                $categories,
                $results
            ]
        ]);*/

        return new JsonResponse((object)[
            'categories' => $categories,
            'results' => $results
        ]);

        return $this->render('IndicateurBundle:Affichage:test.html.twig',[
            'test' => [$categories,$results]
        ]);

        /*
        : $('#id_container_mois').find('.cl_treso_mois.active').attr('data-mois')
        */
    }
}