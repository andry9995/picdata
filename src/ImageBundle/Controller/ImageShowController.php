<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 07/07/2017
 * Time: 09:43
 */

namespace ImageBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageShowController extends Controller
{
    /**
     * Affichage Data + Image
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function dataImageShowAction(Request $request)
    {
        $post = $request->request;
        $donneesSaisie = null;

        $height = $post->get('height');

        $height = floatval($height) - 40;
        $imageId = Boost::deboost($post->get('imageId'),$this);
        if (is_bool($imageId)) return new Response('security');

        $image = $this->getDoctrine()->getRepository('AppBundle:Image')->createQueryBuilder('im')
            ->where('im.id = :id')
            ->setParameter('id', $imageId)
            ->getQuery()
            ->getOneOrNullResult();

        if($image == null) return new Response('Image introuvable');


//PICDATA

        $chemin = $_SERVER['DOCUMENT_ROOT'].'/IMAGES/' . $image->getLot()
                ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();

//LOCAL
//        $chemin = $_SERVER['DOCUMENT_ROOT'].'/picdata/web/IMAGES/' . $image->getLot()
//                ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();

//192.168.0.5
//        $chemin = $_SERVER['DOCUMENT_ROOT'].'/newpicdata/web/IMAGES/' . $image->getLot()
//                ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();

        $fileExist = file_exists($chemin);

        if($fileExist == true){

//PICDATA
            $chemin = '/IMAGES/' . $image->getLot()
                    ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();

//LOCAL
//            $chemin = '/picdata/web/IMAGES/' . $image->getLot()
//                    ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();

//192.168.0.5
//            $chemin = '/newpicdata/web/IMAGES/' . $image->getLot()
//                    ->getDossier()->getSite()->getClient()->getId() . '/' . $image->getNom() . '.' . $image->getExtImage();
        }

        else {
            $chemin = 'http://picdata.fr/picdataovh/' . 'images/' .
                $image->getLot()->getDossier()->getSite()->getClient()->getNom() . '/' .
                $image->getLot()->getDossier()->getNom() . '/' .
                $image->getExercice() . '/' .
                $image->getLot()->getDateScan()->format('Y-m-d') . '/' .
                $image->getLot()->getLot() . '/' .
                $image->getNom() . '.' . $image->getExtImage();
        }


        /*$chemin = 'http://picdata.fr/picdataovh/' . 'images/' .
            $image->getLot()->getDossier()->getSite()->getClient()->getNom() . '/' .
            $image->getLot()->getDossier()->getNom() . '/' .
            $image->getExercice() . '/' .
            $image->getLot()->getDateScan()->format('Y-m-d') . '/' .
            $image->getLot()->getLot() . '/' .
            $image->getNom() . '.' . $image->getExtImage();*/


        $embed = '<embed src="' . $chemin . '" width="100%" height="100%" class="js_embed" />';

        $infos = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->getInfosImageByImageId($image->getId());

        $infosSep = $this->getDoctrine()
            ->getRepository('AppBundle:Separation')
            ->getListeImageSeparationByClientIdImage($image->getId());

        if($infosSep != null){
            /** @var  $infoSep Separation*/
            $infosSep = $infosSep[0];
        }

        /** @var  $dossier Dossier*/
        $dossier = $image->getLot()->getDossier();


        if ($infos != null) {
            $etape = $infos[0]['tableSaisie'];
        }

        else{

            if($infosSep != null) {

                $etape = 'Catégorisée';
            }
            else{
                $etape = 'Reçue';
            }
        }

        $tvaSaisie = null;

        switch ($etape) {
            case 'Saisie 1':
                /**@var $donneesSaisie Saisie1 */
                $donneesSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:Saisie1')
                    ->findOneByImage($imageId);

                $etape = 'Saisie';

                break;

            case 'Saisie 2':
                /**@var $donneesSaisie Saisie2 */
                $donneesSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:Saisie2')
                    ->findOneByImage($imageId);

                $etape = 'Saisie';

                break;

            case 'Controle Saisie':
                /**@var $donneesSaisie \AppBundle\Entity\SaisieControle */
                $donneesSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:SaisieControle')
                    ->findOneByImage($imageId);

                $etape = 'Saisie';

                break;

            case 'Imputation':
                /** @var $donneesSaisie Imputation */
                $donneesSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:Imputation')
                    ->findOneByImage($imageId);

                /** @var $tvaSaisie TvaImputation */
                $tvaSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:TvaImputation')
                    ->findByImage($imageId);

                $etape = 'Imputée';

                break;

            case 'Controle Imputation':
                /** @var $donneesSaisie ImputationControle */
                $donneesSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:ImputationControle')
                    ->findOneByImage($imageId);

                /** @var $tvaSaisie TvaImputation */
                $tvaSaisie = $this->getDoctrine()
                    ->getRepository('AppBundle:TvaImputationControle')
                    ->findByImage($imageId);

                $etape = 'Imputée';

                break;

        }

        $image = $this->getDoctrine()
            ->getRepository('AppBundle:Image')
            ->find($imageId);


        return $this->render('ConsultationPieceBundle:Default:dataImage.html.twig', array(
            'img' => $image,
            'etape' => $etape,
            'saisie' => $donneesSaisie,
            'tvaSaisie' => $tvaSaisie,
            'dossier' => $dossier,
            'embed' => $embed,
            'height' => $height,
            'separation' => $infosSep
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function detailsCompteAction(Request $request)
    {
        $post = $request->request;
        $isPcc = (intval($post->get('type')) == 0);

        $dossier = Boost::deboost($post->get('dossier'),$this);
        $compte = Boost::deboost($post->get('id'),$this);
        if (is_bool($dossier) || is_bool($compte)) return new Response('security');

        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        /*$compte = ($isPcc) ?
            $this->getDoctrine()->getRepository('AppBundle:Pcc')->find($compte) :
            $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($compte);*/

        $exercices = json_decode($post->get('exercices'));
        $mois = json_decode($post->get('mois'));
        $periodes = json_decode($post->get('periodes'));

        $moisSelects = [];
        foreach ($periodes as $periode)
        {
            foreach ($periode->moiss as $moi)
            {
                $moisSelects[] = $moi;
            }
        }

        if ($isPcc)
        {
            $resultat = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getGrandLivre($dossier,$exercices,$moisSelects,1,$compte);
            return new JsonResponse($resultat);
        }
        else
        {
            $tiers = $this->getDoctrine()->getRepository('AppBundle:Tiers')->find($compte);
            $resultat = $this->getDoctrine()->getRepository('AppBundle:Ecriture')->getGrandLivreTiers($dossier,$exercices,$moisSelects,$tiers->getType(),1,$compte);
            return new JsonResponse($resultat);
        }
    }
}