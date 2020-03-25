<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 01/06/2017
 * Time: 13:49
 */

namespace ImageBundle\Controller;

use AppBundle\Controller\Boost;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageTransfert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HistoriqueController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        //$user = new Utilisateur();
        return $this->render('ImageBundle:Historique:index.html.twig',array('type'=>$user->getAccesUtilisateur()->getType()));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function treeAction(Request $request)
    {
        $post = $request->request;
        $client = Boost::deboost($post->get('client'),$this);
        if(is_bool($client)) return new Response('security');
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
        $periode = intval($post->get('filtre'));
        $usersIds = $post->get('users');
        $users = [];
        foreach ($usersIds as $user)
        {
            $id = Boost::deboost($user,$this);
            if(is_bool($id)) return new Response('security');
            $users[] = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->find($id);
        }

        $dateStart = new \DateTime();
        $dateStart->setTime(0,0);
        $dateEnd = new \DateTime();
        $dateEnd->setTime(0,0);

        if($periode == 1)
        {
            $dateStart->sub(new \DateInterval('P1D'));
            $dateEnd->sub(new \DateInterval('P1D'));
        }
        elseif($periode == 2)
        {
            $dateStart->modify('last saturday');
            $dateEnd->modify('next saturday');
        }
        elseif($periode == 3)
        {
            $dateStart->modify('last saturday')->modify('last saturday');
            $dateEnd->modify('last saturday');
        }

        /** @var Image[] $images */
        $images = $this->getDoctrine()->getRepository('AppBundle:HistoriqueUpload')->getImagesUsers($client,$users,$dateStart,$dateEnd,true,false);

        $sep = '_-_';
        $objects = [];
        foreach ($images as $image)
        {
            $lot = $image->getLot();
            $dateScan = $lot->getDateScan();
            $dossier = $lot->getDossier();

            $keyDate = $dateScan->format('Ymd');
            $keyDossier = $dossier->getNom().$sep.$dossier->getId();
            $keyLot = $lot->getLot().$sep.$lot->getId();

            if(!isset($objects[$keyDate][$keyDossier][$keyLot]))
                $objects[$keyDate][$keyDossier][$keyLot] = [];
            $objects[$keyDate][$keyDossier][$keyLot][$image->getId()] = $image;
        }

        $tree = [];
        foreach ($objects as $keyDate => &$date)
        {
            $dateTree = new \stdClass();
            $dateText = \DateTime::createFromFormat('Ymd',$keyDate)->format('d/m/Y');
            $dateTree->text = $dateText;
            $dateTree->icon = 'none';
            $liAttr = new \stdClass();
            $liAttr->data_date = $dateText;
            $liAttr->data_dossier = Boost::boost(0);
            $liAttr->data_lot = Boost::boost(0);
            $liAttr->data_image = Boost::boost(0);
            $dateTree->li_attr = $liAttr;

            if(true)
            {
                $dateChild = [];
                foreach ($date as $keyDossier => &$dossier)
                {
                    $is = explode($sep,$keyDossier);
                    $dossierTree = new \stdClass();
                    $dossierTree->text = $is[0];
                    $dossierTree->icon = 'none';
                    $liAttr = new \stdClass();
                    $liAttr->data_date = $dateText;
                    $liAttr->data_dossier = Boost::boost($is[1]);
                    $liAttr->data_lot = Boost::boost(0);
                    $liAttr->data_image = Boost::boost(0);
                    $dossierTree->li_attr = $liAttr;
                    $dossierChild = [];
                    foreach ($dossier as $keyLot => &$lot)
                    {
                        $js = explode($sep,$keyLot);
                        $lotTree = new \stdClass();
                        $lotTree->text = 'Lot '.$js[0];
                        $liAttr = new \stdClass();
                        $liAttr->data_date = $dateText;
                        $liAttr->data_dossier = Boost::boost($is[1]);
                        $liAttr->data_lot = Boost::boost($js[1]);
                        $liAttr->data_image = Boost::boost(0);
                        $lotTree->li_attr = $liAttr;
                        $lotChild = [];
                        foreach ($lot as $image)
                        {
                            $imageTree = new \stdClass();
                            $imageTree->text = $image->getNom().' ('.$image->getExercice().')';
                            $imageTree->icon = 'none';
                            $liAttr = new \stdClass();
                            $liAttr->data_date = $dateText;
                            $liAttr->data_dossier = Boost::boost($is[1]);
                            $liAttr->data_lot = Boost::boost($js[1]);
                            $liAttr->data_image = Boost::boost($image->getId());
                            $imageTree->li_attr = $liAttr;
                            $lotChild[] = $imageTree;
                        }
                        $lotTree->children = $lotChild;
                        $dossierChild[] = $lotTree;
                    }
                    $dossierTree->children = $dossierChild;
                    $dateChild[] = $dossierTree;
                }
                $dateTree->children = $dateChild;
            }
            $tree[] = $dateTree;
        }

        return new JsonResponse($tree);

        return $this->render('ImageBundle:Historique:test.html.twig',array('test'=>$tree));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function usersAction(Request $request)
    {
        $post = $request->request;
        $client = Boost::deboost($post->get('client'),$this);
        if(is_bool($client)) return new Response('security');
        $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
        $user = $this->getUser();
        $users = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->getChildsUsers($user,$client);
        return $this->render('ImageBundle:Historique:users.html.twig',array('users'=>$users,'user'=>$user));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function apercuAction(Request $request)
    {
        $post = $request->request;
        $lot = Boost::deboost($post->get('lot'),$this);
        $image = Boost::deboost($post->get('img'),$this);

        if(is_bool($lot) || is_bool($image)) return new Response('security');
        $image = $this->getDoctrine()->getRepository('AppBundle:Image')->find($image);
        $lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->find($lot);

        $appPath = $this->container->getParameter('kernel.root_dir');
        $webPath = str_replace('\\','/',realpath($appPath . '/../web')).'/';
        $imagesPath = $webPath.'IMAGES/';

        $clientId = 0;
        $extC = 'gif';
        /** @var Image[] $images */
        $images = $this->getDoctrine()->getRepository('AppBundle:Lot')->getImagesInLot($lot,$image,0);
        $test = [];
        foreach ($images as &$image)
        {
            //$image = new Image();
            $extImage = $image->getExtImage();
            $nom = $image->getNom();
            if($clientId == 0) $clientId = $image->getLot()->getDossier()->getSite()->getClient()->getId();

            $oldFile = $imagesPath.$clientId.'/'.$nom.'.'.$extImage;
            $newFile = $imagesPath.$clientId.'/'.$nom.'.'.$extC;
            $image->setNbpage(100);
            if (!file_exists($oldFile))
            {
                $oldFile = $imagesPath.$clientId.'/'.$image->getLot()->getDateScan()->format('Ymd').'/'.$nom.'.'.$extImage;
                $newFile = $imagesPath.$clientId.'/'.$image->getLot()->getDateScan()->format('Ymd').'/'.$nom.'.'.$extC;
                $image->setNbpage(200);
            }

            if(strtoupper($extImage) == 'PDF')
            {
                $convert =
                    'convert -density 288 '.
                    $oldFile.'[0] '.
                    //'/var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES/553/DX000001D.pdf '.
                    '-resize 25% '.
                    $newFile;
                    //'/var/www/vhosts/ns315229.ip-37-59-25.eu/lesexperts.biz/web/IMAGES/test.gif';
                shell_exec($convert);
                //$test[] = $convert;
            }
        }

        return $this->render('ImageBundle:Historique:vignettes.html.twig',array('extC'=>$extC,'clientId'=>$clientId,'test'=>$test,'images'=>$images));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function showMoveAction(Request $request)
    {
        $post = $request->request;
        return $this->render('ImageBundle:Historique:move.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function moveAction(Request $request)
    {
        $post = $request->request;
        $dossier = Boost::deboost($post->get('dossier'),$this);
        if(is_bool($dossier)) return new Response('security');
        $exercice = intval($post->get('exercice'));
        $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->find($dossier);
        $user = $this->getUser();

        $dateScan = new \DateTime();
        $imgs = json_decode($post->get('images'));
        foreach ($imgs as $img)
        {
            $id = Boost::deboost($img,$this);
            if(is_bool($id)) return new Response('security');

            /** @var Image $image */
            $image = $this->getDoctrine()->getRepository('AppBundle:Image')->find($id);

            $dateScan = $image->getLot()->getDateScan();
        }

        //$lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->getNewLot($dossier, $user, '', null, $dateScan);
        $em = $this->getDoctrine()->getManager();

        foreach ($imgs as $img)
        {
            $id = Boost::deboost($img,$this);
            if(is_bool($id)) return new Response('security');

            /** @var Image $image */
            $image = $this->getDoctrine()->getRepository('AppBundle:Image')->find($id);
            //image tranfert
            $imageTranfert = new ImageTransfert();
            $imageTranfert
                ->setExercice($exercice)
                ->setLot(null)
                ->setUtilisateur($user)
                ->setDateTranfert(new \DateTime())
                ->setExerciceOld($image->getExercice())
                ->setImage($image)
                ->setLotOld($image->getLot());

            $em->persist($imageTranfert);

            //image
            /*$image->setExercice($exercice);
            $image->setLot($lot);*/
        }
        $em->flush();

        return new Response(1);
        //return $this->render('ImageBundle:Historique:test.html.twig',array('test'=>$lot));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function removeAction(Request $request)
    {
        $post = $request->request;
        $imgs = json_decode($post->get('images'));

        $em = $this->getDoctrine()->getManager();
        foreach ($imgs as $img)
        {
            $id = Boost::deboost($img,$this);
            if(is_bool($id)) return new Response('security');
            $image = $this->getDoctrine()->getRepository('AppBundle:Image')->find($id);
            if($image != null) $image->setSupprimer(10);
        }

        $em->flush();

        return new Response('OK');
    }
}