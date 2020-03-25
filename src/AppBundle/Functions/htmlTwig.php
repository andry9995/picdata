<?php
/**
 * Created by PhpStorm.
 * User: DINOH
 * Date: 07/01/2020
 * Time: 09:53
 */

namespace AppBundle\Functions;


use Symfony\Component\Routing\Router;

class htmlTwig extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('html', [$this, 'html'], ['is_safe' => ['html']]),
        ];
    }

    public function html($html)
    {
        return $html;
    }

    public function getName()
    {
        return 'htmltwig_extension';
    }
}