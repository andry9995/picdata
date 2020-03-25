<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 02/06/2017
 * Time: 14:59
 */

namespace AppBundle\Functions;


class TruncateText extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('truncate', array($this, 'truncateText')),
        );
    }

    public function truncateText($text, $length)
    {
        if (strlen($text) >= $length) {
            return substr($text, 0, $length);
        } else {
            return $text;
        }
    }
}