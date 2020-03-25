<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 14/10/2016
 * Time: 11:25
 */

namespace IndicateurBundle\Controller;


class ResultChartClass
{
    public $mois = '12';
    public $year = '2000';
    public $valeur = 0;

    public function __construct($mois,$year)
    {
        $this->mois = $mois;
        $this->year = $year;
    }
}