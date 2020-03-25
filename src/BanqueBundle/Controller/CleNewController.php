<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 31/01/2020
 * Time: 14:56
 */

namespace BanqueBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Controller\StringExt;
use AppBundle\Entity\BanqueType;
use AppBundle\Entity\CfonbBanque;
use AppBundle\Entity\Cle;
use AppBundle\Entity\CleDossier;
use AppBundle\Entity\CleDossierExt;
use AppBundle\Entity\CleSlave;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveExt;
use AppBundle\Entity\Tiers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CleNewController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function showEditAction(Request $request)
    {
        $releve = Boost::deboost($request->request->get('releve'), $this);
        $dossier = Boost::deboost($request->request->get('dossier'), $this);
        $cle = Boost::deboost($request->request->get('cle_id'), $this);
        if(is_bool($dossier) || is_bool($releve) || is_bool($cle)) return new Response('security');

        $lTypeComptas = ['Résultat','Tva','Bilan'];

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        /** @var Releve $releve */
        $releve = $this->getDoctrine()->getRepository('AppBundle:Releve')
            ->find($releve);

        if (intval($cle) != 0 && $releve && $releve->getCleDossier())
            $cle = $releve->getCleDossier()->getCle();

        $key = trim($request->request->get('cle'));

        /** @var  $cles */
        $cles = [];

        if (!$cle)
        {
            if ($key == '')
            {
                /** @var CfonbBanque[] $cfonbCodeActives */
                $cfonbCodeActives = $this->getDoctrine()->getRepository('AppBundle:CfonbBanque')
                    ->cfonbActiveInBanque($releve->getBanqueCompte()->getBanque());
                $libelle = $this->getDoctrine()->getRepository('AppBundle:Releve')->getLibelleWithComplement($releve,$cfonbCodeActives);

                $cles = $this->getDoctrine()->getRepository('AppBundle:Cle')
                    ->getClesValideLibelle($libelle,$dossier);

                $cle = $cles[0];
            }
            else
                $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')
                    ->getByLibelle($key);
        }
        else $key = $cle->getCle();

        /** @var CleDossier $cleDossier */
        $cleDossier = null;

        if ($cle)
            $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
                ->getCleDossierByCle($cle, $dossier);

        //0: engagement, 1:tresorerie, 2: tresorerie avec piece, 3:ecriture particulier
        $typeCompta = 0;
        if ($cleDossier)
        {
            $typeCompta = $cleDossier->getTypeCompta();
            if ($typeCompta != 1 && $typeCompta != 2)
                $typeCompta = 0;
        }

        /** @var CleDossierExt[] $cleDossierExts */
        $cleDossierExts = [];

        if ($cleDossier)
            $cleDossierExts = $this->getDoctrine()->getRepository('AppBundle:CleDossierExt')
                ->getForCleDossier($cleDossier);

        $cleDossierAdds = $this->getDoctrine()->getRepository('AppBundle:CleDossierExt')
            ->getCleDossierAdds($cleDossierExts);

        /** @var BanqueType[] $banqueTypes */
        $banqueTypes = $this->getDoctrine()->getRepository('AppBundle:BanqueType')
            ->createQueryBuilder('bt')
            ->orderBy('bt.libelle')
            ->getQuery()
            ->getResult();

        return $this->render('BanqueBundle:CleNew:edit.html.twig',[
            'releve' => $releve,
            'cle' => $cle,
            'cleDossier' => $cleDossier,
            'key' => $key,
            'typeCompta' => $typeCompta,
            'dossier' => $dossier,
            'banqueTypes' => $banqueTypes,
            'cleDossierExts' => $cleDossierExts,
            'lTypeComptas' => $lTypeComptas,
            'cleDossierAdds' => $cleDossierAdds,
            'cles' => $cles
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function cleDossierExtParamsAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $cleDossierExt = Boost::deboost($request->request->get('cle_dossier_ext'),$this);
        $banqueType = Boost::deboost($request->request->get('banque_type'),$this);
        if(is_bool($dossier) || is_bool($cleDossierExt) || is_bool($banqueType)) return new Response('security');

        $index = intval($request->request->get('index'));

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $cleDossierExt = $this->getDoctrine()->getRepository('AppBundle:CleDossierExt')
            ->find($cleDossierExt);
        $banqueType = $this->getDoctrine()->getRepository('AppBundle:BanqueType')
            ->find($banqueType);

        $addCompte = ' <div class="form-horizontal">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="checkbox checkbox-inline">
                            <input type="checkbox" id="js_id_is_auxilliaire" checked>
                            <label for="js_id_is_auxilliaire">Compte&nbsp;Auxilliaire</label>
                        </div>             
                    </div>
                </div>
                <div class="row" id="container_radio_auxilliaire">
                    <div class="col-lg-12">
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="radio-fournisseur" value="0" name="radio-type-tiers" checked="">
                            <label for="radio-fournisseur">Frns</label>
                        </div>            
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="radio-client" value="1" name="radio-type-tiers">
                            <label for="radio-client">Clt</label>
                        </div>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="radio-autre" value="2" name="radio-type-tiers">
                            <label for="radio-autre">Autre</label>
                        </div>                                
                    </div>   
                </div>
                <div class="form-group">
                    <label for="js_id_compte" class="col-lg-3 control-label">Compte</label>
                    <div class="col-lg-9">
                        <input type="text" placeholder="Numéro de compte" id="js_id_compte" class="form-control" value="">
                    </div>
                </div>                
                <div class="form-group">
                    <label for="js_id_intitule" class="col-lg-3 control-label">Intitul&eacute;</label>
                    <div class="col-lg-9">
                        <input type="text" placeholder="Intitulé du compte" id="js_id_intitule" class="form-control" value="">
                    </div>
                </div>
                <div class="form-group text-center">
                    <span class="btn btn-xs btn-white" id="js_id_save_new_compte"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Ajouter</span>
                </div>           
            </div>';

        return $this->render('BanqueBundle:CleNew:cle-dossier-ext.html.twig',[
            'index' => $index,
            'dossier' => $dossier,
            'addCompte' => $addCompte
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveCleAction(Request $request)
    {
        $banqueType = Boost::deboost($request->request->get('banque_type'), $this);
        $dossier = Boost::deboost($request->request->get('dossier'), $this);
        $cle = Boost::deboost($request->request->get('cle'), $this);

        if(is_bool($dossier) || is_bool($banqueType) || is_bool($cle)) return new Response('security');
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        $banqueType = $this->getDoctrine()->getRepository('AppBundle:BanqueType')
            ->find($banqueType);
        $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')
            ->find($cle);

        $key = $request->request->get('key');
        $typeCompta = intval($request->request->get('type_compta'));
        $pasPiece = intval($request->request->get('pas_piece'));

        $tabs = json_decode($request->request->get('tabs'));

        if (!$cle)
            /** @var Cle $cle */
            $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')
                ->getByLibelle($key);

        /** @var CleDossier $cleDossier */
        $cleDossier = null;

        if ($cle)
            $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
                ->getCleDossierByCle($cle, $dossier);

        $em = $this->getDoctrine()->getManager();
        if (!$cle)
        {
            $cle = new Cle();
            $cle
                ->setCle($key)
                ->setBanqueType($banqueType)
                ->setTypeCompta($typeCompta)
                ->setTva(2)
                ->setType(0);

            $em->persist($cle);
            $em->flush();
        }

        foreach (json_decode($request->request->get('cles_slaves')) as $cs)
        {
            $cleS = $this->getDoctrine()->getRepository('AppBundle:Cle')
                ->find(Boost::deboost($cs,$this));

            $resultsCles[] = (object)
            [
                's' => 1,
                'c' => $cleS
            ];

            if ($cle && $cleS)
            {
                $cleSlave = $this->getDoctrine()->getRepository('AppBundle:CleSlave')
                    ->findOneBy([
                        'cle' => $cle,
                        'cleSlave' => $cleS,
                        'dossier' => $dossier
                    ]);

                if (!$cleSlave)
                {
                    $cleSlave = new CleSlave();
                    $cleSlave
                        ->setCle($cle)
                        ->setCleSlave($cleS)
                        ->setDossier($dossier);
                    $em->persist($cleSlave);
                }
            }
        }

        $addCleDossier = false;
        if (!$cleDossier)
        {
            $cleDossier = new CleDossier();
            $cleDossier
                ->setCle($cle)
                ->setDossier($dossier);

            $addCleDossier = true;
        }

        $cleDossier
            ->setTypeCompta($typeCompta)
            ->setBanqueType($banqueType)
            ->setPasPiece($pasPiece)
            ->setTauxTva(0)
            ->setBilanTiers(null)
            ->setBilanPcc(null)
            ->setTva(null)
            ->setResultat(null);

        if ($addCleDossier)
            $em->persist($cleDossier);
        $em->flush();


        foreach ($tabs as $tab)
        {
            $cleDossierExt = Boost::deboost($tab->id, $this);
            /** @var CleDossierExt $cleDossierExt */
            $cleDossierExt = $this->getDoctrine()->getRepository('AppBundle:CleDossierExt')
                ->find($cleDossierExt);
            $typeCompte = intval($tab->type_compte);

            $options = $tab->options;

            $recherche = $options->recherche;
            $format = $options->format;
            $carPrec = $options->car_prec;
            $carFin = $options->car_fin;
            $posDebut = $options->pos_deb;
            $textLength = $options->pos_len;

            /** @var Pcc $pcc */
            $pcc = null;
            /** @var Tiers $tiers */
            $tiers = null;

            $pccsComptes = $tab->pccs;

            if (count($pccsComptes) > 0)
            {
                $spliters = explode('#',$pccsComptes[0]);

                if (intval($spliters[0]) == 0)
                    $pcc = $this->getDoctrine()->getRepository('AppBundle:Pcc')
                        ->find($spliters[1]);
                else
                    $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')
                        ->find($spliters[1]);
            }

            $supprimer = intval($tab->supprimer) == 1;

            if ($cleDossierExt)
            {
                if ($supprimer || (!$tiers && !$pcc)) $em->remove($cleDossierExt);
                else
                {
                    $cleDossierExt
                        ->setPcc($pcc)
                        ->setTiers($tiers)
                        ->setFormule('')
                        ->setPcgs(json_encode($tab->pcgs))
                        ->setTypeCompte($typeCompte)
                        ->setRechercher($recherche)
                        ->setFormat($format)
                        ->setTextStart($carPrec)
                        ->setTextEnd($carFin)
                        ->setStart($posDebut)
                        ->setTextLength($textLength);
                }
            }
            elseif (!$supprimer && ($tiers || $pcc))
            {
                /*return $this->render('IndicateurBundle:Affichage:test.html.twig',[
                    'test' => $tiers
                ]);*/

                $cleDossierExt = new CleDossierExt();

                $cleDossierExt
                    ->setPcc($pcc)
                    ->setTiers($tiers)
                    ->setCleDossier($cleDossier)
                    ->setFormule('')
                    ->setPcgs(json_encode($tab->pcgs))
                    ->setTypeCompte($typeCompte)
                    ->setRechercher($recherche)
                    ->setFormat($format)
                    ->setTextStart($carPrec)
                    ->setTextEnd($carFin)
                    ->setStart($posDebut)
                    ->setTextLength($textLength);

                $em->persist($cleDossierExt);
            }
        }

        $em->flush();

        return new Response(1);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function clePropsAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $cle = Boost::deboost($request->request->get('cle'), $this);
        if(is_bool($dossier) || is_bool($cle)) return new Response('security');

        /** @var Dossier $dossier */
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        /** @var Cle $cle */
        $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')
            ->find($cle);

        $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
            ->getCleDossierByCle($cle,$dossier);

        $banqueType = ($cleDossier && $cleDossier->getBanqueType()) ?
            $cleDossier->getBanqueType() :
            $this->getDoctrine()->getRepository('AppBundle:BanqueType')->find(11);

        $pasPiece = $cleDossier ?
            $cleDossier->getPasPiece() :
            0;

        return new JsonResponse((object)[
            'bt' => $banqueType->getId(),
            'pp' => $pasPiece,
            'tc' => $cleDossier ? $cleDossier->getTypeCompta() : 0
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function cleDossierExtsAction(Request $request)
    {
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $cle = Boost::deboost($request->request->get('cle'), $this);
        if(is_bool($dossier) || is_bool($cle)) return new Response('security');

        /** @var Dossier $dossier */
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->find($dossier);
        /** @var Cle $cle */
        $cle = $this->getDoctrine()->getRepository('AppBundle:Cle')
            ->find($cle);

        $cleDossier = $this->getDoctrine()->getRepository('AppBundle:CleDossier')
            ->getCleDossierByCle($cle,$dossier);

        $cleDossierExts = $this->getDoctrine()->getRepository('AppBundle:CleDossierExt')
            ->getForCleDossier($cleDossier);

        $cleDossierAdds = $this->getDoctrine()->getRepository('AppBundle:CleDossierExt')
            ->getCleDossierAdds($cleDossierExts);

        $lTypeComptas = ['Résultat','Tva','Bilan'];

        return $this->render('BanqueBundle:CleNew:cle-dossier-exts.html.twig',[
            'cleDossierExts' => $cleDossierExts,
            'lTypeComptas' => $lTypeComptas,
            'cleDossierAdds' => $cleDossierAdds
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function annulerImputationReleveExtAction(Request $request)
    {
        $cleDossierExt = Boost::deboost($request->request->get('cle_dossier_ext'), $this);
        if(is_bool($cleDossierExt)) return new Response('security');

        $cleDossierExt = $this->getDoctrine()->getRepository('AppBundle:CleDossierExt')
            ->find($cleDossierExt);

        /** @var ReleveExt $releveExt */
        $releveExt = null;

        $em = $this->getDoctrine()->getManager();
        if ($cleDossierExt)
            $releveExt = $this->getDoctrine()->getRepository('AppBundle:ReleveExt')
                ->findOneBy(['cleDossierExt' => $cleDossierExt]);

        if ($releveExt && $releveExt->getImageFlague())
            $em->remove($releveExt->getImageFlague());

        $em->flush();

        return new Response(1);
    }
}
