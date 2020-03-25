<?php

/**
 * DefaultController 
 *
 * @package Picdata
 *
 * @author Scriptura
 * @copyright Scriptura (c) 2019
 */

namespace GeneralBundle\Controller;

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
     * Listes des dates entre 2 intervales
     *
     * @param string $start
     * @param string $end
     *
     * @return array
     */
    protected function getBetweenDate($start, $end)
    {
        $time1  = strtotime($start);
        $time2  = strtotime($end);
        $my     = date('mY', $time2);
        $months = array(date('Y-m', $time1));
        while ($time1 < $time2) {
            $time1 = strtotime(date('Y-m', $time1) . ' +1 month');
            if (date('mY', $time1) != $my && ($time1 < $time2))
                $months[] = date('Y-m', $time1);
        }
        $months[] = date('Y-m', $time2);
        return $months;
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

    /**
     * Date de début et fin par rapport a l'exercice et au cloture
     *
     * @param string $exercice
     * @param integer $cloture
     *
     * @return array
     */
    public function beginEnd($exercice, $cloture)
    {
        if ($cloture < 9) {
            $debutMois = ($exercice - 1) . '-0' . ($cloture + 1) . '-01';
        } else if ($cloture >= 9 and $cloture < 12) {
            $debutMois = ($exercice - 1) . '-' . ($cloture + 1) . '-01';
        } else {
            $debutMois = ($exercice) . '-01-01';
        }
        if ($cloture < 10) {
            $finMois = ($exercice) . '-0' . ($cloture) . '-01';
        } else {
            $finMois = ($exercice) . '-' . ($cloture) . '-01';
        }

        $result          = array();
        $result['start'] = $debutMois;
        $result['end']   = $finMois;

        return $result;

    }

}
