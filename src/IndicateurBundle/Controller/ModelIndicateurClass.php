<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 29/11/2016
 * Time: 09:55
 */

namespace IndicateurBundle\Controller;

class ModelIndicateurClass
{
    public $name;
    public $width;
    public $classes;
    public $align;

    function __construct($name = '',$width = 0,$classes = '',$align = 'left')
    {
        $this->name = $name;
        $this->width = $width;
        $this->classes = $classes;
        $this->align = $align;
    }
}