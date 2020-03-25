<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 08/05/2018
 * Time: 09:30
 */

namespace LinxoBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Linxo;
use AppBundle\Entity\LinxoTransaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LinxoController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('LinxoBundle:Linxo:index.html.twig');
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function accountsAction(Request $request)
    {
        $clientId = Boost::deboost($request->request->get('client'),$this);
        $siteId = Boost::deboost($request->request->get('site'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($clientId) || is_bool($siteId) || is_bool($dossier)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $site = null;
        $client = null;

        if (is_null($dossier))
        {
            $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($siteId);
            if (is_null($site))
            {
                $client = $this->getDoctrine()->getRepository('AppBundle:Client')->find($clientId);
            }
            else $client = $site->getClient();
        }
        else
        {
            $client = $dossier->getSite()->getClient();
        }

        $linxos = $this->getDoctrine()->getRepository('AppBundle:Linxo')->getLinxos($this->getUser(),$client,$site,$dossier);
        return $this->render('LinxoBundle:Linxo:box-item.html.twig',['linxos'=>$linxos]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function editAction(Request $request)
    {
        $linxo = Boost::deboost($request->request->get('linxo'),$this);
        if(is_bool($linxo)) return new Response('security');

        $linxo = $this->getDoctrine()->getRepository('AppBundle:Linxo')->find($linxo);
        if (is_null($linxo) || is_null($linxo->getJsonCode()))
        {
            return $this->frame($linxo);
        }
        else
        {
            $this->refreshToken($linxo);
            $this->getDoctrine()->getManager()->refresh($linxo);
            $accesToken = json_decode($linxo->getJsonCode());

            $sites = $this->getDoctrine()->getRepository('AppBundle:Site')
                ->getUserSites($this->getUser(),$linxo->getClient());
            $dossiers = $this->getDoctrine()->getRepository('AppBundle:Dossier')
                ->getUserDossier($this->getUser(),$linxo->getClient());

            $url = Constantes::BASE_URL.'/accounts';
            $headers =
                [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer '.$accesToken->access_token
                ];
            $parameters =
                [
                    //'type' => 'CHECKINGS',
                    'status' =>'ACTIVE'
                ];

            $ch = $this->curl($url,$headers,$parameters);

            $response = curl_exec($ch);
            curl_close($ch);

            $accounts = [];
            foreach (json_decode($response) as $r)
            {
                $linxoDossier = $this->getDoctrine()->getRepository('AppBundle:LinxoDossier')
                    ->getLinxoDossier($linxo,$r->id,$r->connection_id,$r->name,$r->type,isset($r->iban) ? $r->iban : '',$r->account_number,isset($r->classification) ? $r->classification : '');

                $linxoTransaction = $this->getDoctrine()->getRepository('AppBundle:LinxoTransaction')->getLast($linxoDossier);

                $banqueComptes = is_null($linxoDossier->getBanqueCompte()) ? [] : $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
                    ->getBanquesComptes($linxoDossier->getBanqueCompte()->getDossier());

                $accounts[] = (object)
                [
                    'id' => $linxoDossier->getId(),
                    'dossier' => (!is_null($linxoDossier->getBanqueCompte()) ? $linxoDossier->getBanqueCompte()->getDossier() : null),
                    'banqueCompte' => $linxoDossier->getBanqueCompte(),
                    'id_linxo' => $r->id,
                    'connection_id' => $r->connection_id,
                    'name' => $r->name,
                    'currency' => $r->currency,
                    'type' => $r->type,
                    'iban' => isset($r->iban) ? $r->iban : '',
                    'account_number' => $r->account_number,
                    'classification' => isset($r->classification) ? $r->classification : '',
                    //0: new; 1:jour; 2:semaine; 3:mois; 4:desactiver
                    'periode' => $linxoDossier->getRecuperation(),
                    'dateFin' => (is_null($linxoTransaction) ? null : $linxoTransaction->getDateFin()),
                    'soldeFin' => (is_null($linxoTransaction) ? null : $linxoTransaction->getSoldeFin()),
                    'banquesComptes' => $banqueComptes
                ];
            }

            $periodes = ['new','jour','semaine','mois','desactiver'];
            return $this->render('LinxoBundle:Linxo:accounts.html.twig',
                [
                    'linxo' => $linxo,
                    'accounts' => $accounts,
                    'periodes' => $periodes,
                    'sites' => $sites,
                    'dossiers' => $dossiers
                ]);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function comptesBanquesAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        $site = Boost::deboost($request->request->get('site'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        $linxoDossier = Boost::deboost($request->request->get('linxo_dossier'),$this);
        if(is_bool($client) || is_bool($site) || is_bool($dossier) || is_bool($linxoDossier)) return new Response('security');
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')->find($client);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($site);
        $linxoDossier = $this->getDoctrine()->getRepository('AppBundle:LinxoDossier')->find($linxoDossier);

        //return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$dossier]);

        $dossiers = (is_null($dossier)) ?
            $this->getDoctrine()->getRepository('AppBundle:Dossier')->getUserDossier($this->getUser(),$client,$site) :
            [$dossier];

        $banqueComptes = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')
            ->createQueryBuilder('bc')
            ->leftJoin('bc.dossier','d')
            ->where('bc.dossier IN (:dossiers)')
            ->setParameter('dossiers',$dossiers)
            ->orderBy('d.nom')
            ->addOrderBy('bc.numcompte')
            ->getQuery()
            ->getResult();

        return $this->render('LinxoBundle:Linxo:banqueCompte.html.twig',['linxoDossier'=>$linxoDossier, 'banqueComptes' => $banqueComptes]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveLinxoDossiersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $linxo = Boost::deboost($request->request->get('linxo'),$this);
        $site = Boost::deboost($request->request->get('site'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);

        if(is_bool($linxo) || is_bool($site) || is_bool($dossier)) return new Response('security');
        $linxo = $this->getDoctrine()->getRepository('AppBundle:Linxo')->find($linxo);
        $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($site);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        $linxo->setSite($site)->setDossier($dossier);
        $em->flush();

        $linxoDossiers = json_decode($request->request->get('linxoDossiers'));

        foreach ($linxoDossiers as $linxoD)
        {
            $linxoDossier = Boost::deboost($linxoD->id,$this);

            $banqueCompte = Boost::deboost($linxoD->banque_compte,$this);
            if(is_bool($linxoDossier) || is_bool($banqueCompte)) return new Response('security');
            $banqueCompte = $this->getDoctrine()->getRepository('AppBundle:BanqueCompte')->find($banqueCompte);
            $linxoDossier = $this->getDoctrine()->getRepository('AppBundle:LinxoDossier')->find($linxoDossier);
            $linxoDossier
                ->setBanqueCompte($banqueCompte)
                ->setRecuperation(intval($linxoD->periode));

            if ($linxoD->date == '' || $linxoD->solde == '')
            {
                $linxoDossier->setRecuperation(4);
            }
            else
            {
                $dateFIn = \DateTime::createFromFormat('d/m/Y', $linxoD->date);
                $linxoTransaction = new LinxoTransaction();
                $linxoTransaction
                    ->setLinxoDossier($linxoDossier)
                    ->setDateRecuperation((new \DateTime())->sub(new \DateInterval('P2D')))
                    ->setDateFin( $dateFIn)
                    ->setDateDebut(new \DateTime())
                    ->setSoldeDebut(0)
                    ->setSoldeFin(intval($linxoD->solde));
                $em->persist($linxoTransaction);
            }
        }
        $em->flush();

        return new Response(1);
    }

    /**
     * @param Linxo $linxo
     */
    public function refreshToken(Linxo $linxo)
    {
        $accesToken = '';
        /*while (true)
        {*/
            $ch = curl_init(Constantes::BASE_AUTH_URL.'/token');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded'
            ));

            $jsonCode = json_decode($linxo->getJsonCode());

            $postFields = http_build_query(array(
                //'code' => $code,
                'client_secret' => Constantes::CLIENT_SECRET,
                'grant_type' => 'refresh_token',
                'client_id' => Constantes::CLIENT_ID,
                'redirect_uri' => Constantes::REDIRECT_URI,
                'refresh_token' => $jsonCode->refresh_token
            ));

            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            $accesToken = curl_exec($ch);
            curl_close($ch);

            /*if (!property_exists(json_decode($accesToken), 'error')) break;
        }*/
        //get token
        //$code = $request->request->get('code');

        $em = $this->getDoctrine()->getManager();
        $linxo->setJsonCode($accesToken);
        $em->flush();
        //return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$linxo]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function redirectAction(Request $request)
    {
        $code = $request->query->get('code');
        return $this->render('LinxoBundle:Linxo:code.html.twig',['code' => $code]);
    }

    /**
     * @param $url
     * @param array $headers
     * @param array $parameters
     * @return resource
     */
    private function curl($url,$headers = [],$parameters = [])
    {
        $query = http_build_query($parameters);
        $ch = curl_init($url.'?'.$query);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        return $ch;
    }

    /**
     * @param null $linxo
     * @return Response
     */
    public function frame($linxo = null)
    {
        $parameters =
            [
                'redirect_uri' => Constantes::REDIRECT_URI,
                'response_type' => Constantes::RESPONSE_TYPE,
                'client_id' => Constantes::CLIENT_ID,
                'scope' => Constantes::SCOPE
            ];
        $url = Constantes::BASE_AUTH_URL.'/signin?';

        foreach ($parameters as $key=>$parameter)
        {
            $url .= $key.'='.$parameter.'&';
        }

        return $this->render('LinxoBundle:Linxo:frame.html.twig',['linxo' => $linxo,'src' => $url]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function updateCountLinxoAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        $site = Boost::deboost($request->request->get('site'),$this);
        $dossier = Boost::deboost($request->request->get('dossier'),$this);
        if(is_bool($client) || is_bool($site) || is_bool($dossier)) return new Response('security');
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')->find($client);
        $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($site);
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);

        //get token
        $code = $request->request->get('code');
        $ch = curl_init(Constantes::BASE_AUTH_URL.'/token');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));

        $postFields = http_build_query(array(
            'code' => $code,
            'client_secret' => Constantes::CLIENT_SECRET,
            'grant_type' => 'authorization_code',
            'client_id' => Constantes::CLIENT_ID,
            'redirect_uri' => Constantes::REDIRECT_URI
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $accesToken = json_decode(curl_exec($ch));
        curl_close($ch);

        $id = 'me';
        $url = Constantes::BASE_URL.'/users/'.$id;
        $headers =
            [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer '.$accesToken->access_token
            ];
        $parameters = [];

        $ch = $this->curl($url,$headers,$parameters);

        $compte = json_decode(curl_exec($ch));
        curl_close($ch);

        //return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$compte]);

        $response = 0;
        $lien = '';
        if (property_exists($compte, 'error_description'))
        {
            if (trim($compte->error_description) == 'ACCEPT_TERMS_AND_CONDITIONS_REQUIRED')
            {
                $response = 2;
                $lien = 'https://wwws.linxo.com/auth.page#Login';
            }
        }
        else
        {
            $this->getDoctrine()->getRepository('AppBundle:Linxo')->updateOrAddAccount($client,$site,$dossier,$compte,$accesToken);
            $response = 1;
        }

        $result = new \stdClass();
        $result->r = $response;
        $result->l = $lien;

        return new JsonResponse($result);
        //return $this->render('IndicateurBundle:Affichage:test.html.twig',['test'=>$response]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function dossiersAction(Request $request)
    {
        $client = Boost::deboost($request->request->get('client'),$this);
        $site = Boost::deboost($request->request->get('site'),$this);
        if(is_bool($client) || is_bool($site)) return new Response('security');
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')->find($client);
        $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($site);

        $dossiers = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->getUserDossier($this->getUser(),$client,$site);

        return $this->render('LinxoBundle:Linxo:dossiers.html.twig',['dossiers'=>$dossiers]);
    }
}