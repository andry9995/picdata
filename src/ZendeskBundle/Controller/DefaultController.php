<?php

/**
 * DefaultController 
 *
 * @package Picdata
 *
 * @author Scriptura
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Controller\Boost;

class DefaultController extends Controller
{

    /**
     * Chargement d'un Repository
     *
     * @param string $entityName
     * 
     * @return EntityNameRepository
     */
    protected function loadRepository($entityName)
    {
    	$repository = $this->getDoctrine()
    		->getRepository('AppBundle:' . $entityName);

    	return $repository;
    }

    /**
     * Formatter une array en JsonResponse
     *
     * @param array $value
     *
     * @return JsonResponse
     */
    protected function response($value)
    {
        return new JsonResponse($value);
    }

    public function deboost($value)
    {
        return Boost::deboost($value,$this);
        
    }

}
