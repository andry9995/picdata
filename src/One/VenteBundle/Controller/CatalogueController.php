<?php

/**
 * Project: oneup
 * Author : Mamy Rakotonirina
 * Created on : 14 oct. 2017 10:55:24
 */

namespace One\VenteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\OneArticle;
use One\VenteBundle\Service\ArticleService;

/**
 * Description of CatalogueController
 *
 */
class CatalogueController extends Controller {
    public function indexAction() {
        return $this->render('OneVenteBundle:Catalogue:index.html.twig');
    }
}
