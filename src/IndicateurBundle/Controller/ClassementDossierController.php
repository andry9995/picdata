<?php
    /**
     * Created by PhpStorm.
     * User: SITRAKA
     * Date: 10/02/2017
     * Time: 08:49
     */

    namespace IndicateurBundle\Controller;

    use AppBundle\Controller\Boost;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    class ClassementDossierController extends Controller
    {
        /**
         * @return \Symfony\Component\HttpFoundation\Response
         */
        public function indexAction()
        {
            $rubriques = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques(0);
            $super_rubriques = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques(1);
            $hyper_rubriques = $this->getDoctrine()->getRepository('AppBundle:Rubrique')->getRubriques(2);
            return $this->render('IndicateurBundle:ClassementDossier:index.html.twig',array('adminGranted'=>$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'),
                'rubriques'=>$rubriques, 'super_rubriques'=>$super_rubriques, 'hyper_rubriques'=>$hyper_rubriques));
        }

        /**
         * @param Request $request
         * @return Response
         */
        public function dossiersIndicateurAction(Request $request)
        {
            $post = $request->request;
            $client = Boost::deboost($post->get('client'),$this);
            $site = Boost::deboost($post->get('site'),$this);
            if(is_bool($client) || is_bool($site)) return new Response('security');
            $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
            $site = $this->getDoctrine()->getRepository('AppBundle:Site')->getById($site);
            $dossiers = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getUserDossier($this->getUser(),$client,$site);
            //$dossiers = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossiers($this->getUser(),$this->get('security.authorization_checker'),$site,$client);
            foreach ($dossiers as &$dossier)
            {
                $indicateurGroup = $this->getDoctrine()->getRepository('AppBundle:IndicateurSpecGroup')->getIndicateurGroup($dossier);
                $dossier->setIdCrypter();
                $dossier->setIndicateurGroup($indicateurGroup);
            }

            /*$encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });
            $serializer = new Serializer(array($normalizer), array($encoder));
            return new Response($serializer->serialize($dossiers, 'json'));*/

            return new Response(Boost::serialize($dossiers));
        }

        /**
         * @param Request $request
         * @return Response
         */
        public function groupsDossiersAction(Request $request)
        {
            $post = $request->request;
            $paramGen = intval($post->get('param_gen') == 1);
            $dossier = null;
            $client = null;

            if(!$paramGen)
            {
                $dossier = Boost::deboost($post->get('dossier'),$this);
                $client = Boost::deboost($post->get('client'),$this);
                if(is_bool($client) || is_bool($dossier)) return new Response('security');
                $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->getDossierById($dossier);
                if($dossier == null) $client = $this->getDoctrine()->getRepository('AppBundle:Client')->getById($client);
                else
                {
                    $indicateurGroup = $this->getDoctrine()->getRepository('AppBundle:IndicateurSpecGroup')->getIndicateurGroup($dossier);
                    $dossier->setIndicateurGroup($indicateurGroup);
                    $client = null;
                }
            }

            $dStyles = $this->getDoctrine()->getRepository('AppBundle:IndicateurCell')->getDefaultStyles();
            $groups = $this->getDoctrine()->getRepository('AppBundle:IndicateurGroup')->getGroups($paramGen,$client,$dossier);
            return $this->render('IndicateurBundle:IndicateurAdmin:groups.html.twig',array('groups'=>$groups,'client'=>$client,'dossier'=>$dossier,'dStyles'=>$dStyles));
        }
    }