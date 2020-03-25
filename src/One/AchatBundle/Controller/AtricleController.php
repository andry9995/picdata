<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 25/04/2018
 * Time: 11:33
 */

namespace One\AchatBundle\Controller;


use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class AtricleController extends Controller
{
    /**
     * Liste article dans modal pour devis/facture/commande/avoir
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listforachatAction(Request $request)
    {

        //debut lesexperts.biz
        $dossierId = Boost::deboost($request->request->get('dossierId'), $this);
        $dossier = $this->getDoctrine()
            ->getRepository('AppBundle:Dossier')
            ->find($dossierId);
        //fin lesexperts.biz

        $articles = $this->getDoctrine()->getRepository('AppBundle:OneArticle')->getArticles($dossier);
        return $this->render('OneAchatBundle:Article:listforachat.html.twig', array(
            'articles' => $articles,
        ));
    }

    public function addinachatAction(Request $request) {
        if ($request->isMethod('POST')) {
            $articles = array();
            $taxes = $this->getDoctrine()->getRepository('AppBundle:OneTva')->getTva();

            $items = $request->request->get('articles');
            foreach($items as $value) {
                $item = array();
                $data = explode(';', $value);
                $item['id'] = $data[0];
                $item['code'] = $data[1];
                $item['name'] = $data[2];
                $item['unit'] = $data[3];
                $item['price'] = $data[4];
                $item['tva'] = $data[5];
                $articles[] = $item;
            }
            return $this->render('OneAchatBundle:Article:addinachat.html.twig', array(
                'articles' => $articles,
                'taxes' => $taxes,
            ));
        }
        throw new AccessDeniedException('Accès refusé');
    }

}