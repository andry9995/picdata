<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 18/04/2017
 * Time: 16:14
 */

namespace InfoPerdosBundle\Controller;

use AppBundle\Entity\InstructionDossier;
use AppBundle\Entity\InstructionTexte;
use Proxies\__CG__\AppBundle\Entity\Dossier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Boost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RecapInfoController extends Controller
{
    public function indexAction(Request $request, $json)
    {

        return $this->render('InfoPerdosBundle:Recap:index.html.twig');
    }
}