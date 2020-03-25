<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 20/07/2017
 * Time: 16:11
 */

namespace ImageBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Image;
use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CorrespondanceController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('ImageBundle:Correspondance:index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function imagesAction(Request $request)
    {
        $post = $request->request;
        $filtre = intval($post->get('filtre'));
        $doctrine = $this->getDoctrine();
        $client = $site = $dossier = null;
        $dateStart = new \DateTime($post->get('dateStart'));
        $dateEnd = new \DateTime($post->get('dateEnd'));

        $nom = $post->get('nom');
        $originale = $post->get('originale');

        $user = $this->getUser();

        $client_ = Boost::deboost($post->get('client'),$this);
        $client_ = $doctrine->getRepository('AppBundle:Client')->find($client_);
        $dossiers = $this->getDoctrine()->getRepository('AppBundle:Dossier')
            ->getUserDossier($this->getUser(),($user->getShowDossierDemo()) ? $user->getClient() : $client_);

        /*$users = $doctrine->getRepository('AppBundle:Utilisateur')
            ->getChildsUsers($this->getUser(),$doctrine->getRepository('AppBundle:Client')->find(Boost::deboost($post->get('client'),$this)));*/

        if ($filtre == 0)
        {
            $dossier = Boost::deboost($post->get('dossier'),$this);
            if(is_bool($dossier)) return new Response('security');
            $dossier = $doctrine->getRepository('AppBundle:Dossier')->find($dossier);
            if($dossier == null)
            {
                $site = Boost::deboost($post->get('site'),$this);
                if(is_bool($site)) return new Response('security');
                $site = $doctrine->getRepository('AppBundle:Site')->find($site);

                if($site == null)
                {
                    $client = Boost::deboost($post->get('client'),$this);
                    if(is_bool($client)) return new Response('security');
                    $client = $doctrine->getRepository('AppBundle:Client')->find($client);
                }
            }
        }

        $images = $doctrine->getRepository('AppBundle:HistoriqueUpload')->getImagesUsersOrgs($dossiers,$filtre,$client,$site,$dossier,$dateStart,$dateEnd,$nom,$originale,$this->getUser());

        $results = [];
        foreach ($images as $key => $image)
        {
            $lot = $image->getLot();
            $dossier = $lot->getDossier();
            $site = $dossier->getSite();
            $client =  ($user->getShowDossierDemo()) ? $user->getClient() : $site->getClient();

            $result = new \stdClass();
            $result->p = $key;
            $result->cl = $client->getNom();
            $result->dos = $dossier->getNom();
            $result->date = $lot->getDateScan()->format('d/m/Y');
            $result->l = 'Lot '.$lot->getLot();
            $result->nom = $image->getNom();
            $result->org = $image->getOriginale();
            $result->ext = $image->getExtImage();
            $result->ex = $image->getExercice();
            $result->id = Boost::boost($image->getId());
            $result->v = '';

            $results[] = $result;
        }


        return new JsonResponse($results);
    }
}