<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 26/04/2018
 * Time: 11:46
 */

namespace One\AchatBundle\Controller;


use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class DepenseController extends Controller
{
    public function addinachatAction(Request $request) {
        if ($request->isMethod('POST')) {

            $dossierId = $request->request->get('dossierId');

            $dossier = $this->getDoctrine()
                ->getRepository('AppBundle:Dossier')
                ->find(Boost::deboost($dossierId, $this));

            $taxes = $this->getDoctrine()
                ->getRepository('AppBundle:TvaTaux')
                ->findBy(array('actif' => 1), array('taux' => 'ASC'));

            $pccs = $this->getDoctrine()
                ->getRepository('AppBundle:Pcc')
                ->getPccByDossierLike($dossier, array('4'));

            return $this->render('OneAchatBundle:Depense:addinachat.html.twig', array(
                'pccs' => $pccs,
                'taxes' => $taxes,
            ));
        }
        throw new AccessDeniedException('Accès refusé');
    }
}