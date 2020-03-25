<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 21/04/2016
 * Time: 11:45
 */

namespace AppBundle\Controller;

use AppBundle\Controller\Boost;


class Cryptage extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('boost', array($this, 'cryptageFilter')),
        );
    }

    //crypter
    public function cryptageFilter($str)
    {
        return Boost::boost($str);
    }

    public function getName()
    {
        return 'boost';
    }
}