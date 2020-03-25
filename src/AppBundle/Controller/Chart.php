<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 05/10/2016
 * Time: 13:50
 */

namespace AppBundle\Controller;

class ChartPie
{
    public $name = '';
    public $y = 0;

    public function ChartPie($name = '',$y = 0)
    {
        $this->name = $name;
        $this->y = $y;
    }
}

class ChartLine
{
    public $name = '';
    public $data = array();

    public function __construct($name = '',$data = array())
    {
        $this->name = $name;
        $this->data = $data;
    }
}

//class Chart