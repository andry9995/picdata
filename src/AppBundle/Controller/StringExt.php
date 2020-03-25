<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 11/01/2018
 * Time: 14:56
 */

namespace AppBundle\Controller;


class StringExt
{
    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) return true;

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * @param $str
     * @param string $encoding
     * @return null|string|string[]
     */
    public static function sansAccent($str,$encoding = 'utf-8')
    {
        // transformer les caractères accentués en entités HTML
        $str = htmlentities($str, ENT_NOQUOTES, $encoding);

        // remplacer les entités HTML pour avoir juste le premier caractères non accentués
        // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "à" => "a" ...
        $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);

        // Remplacer les ligatures tel que : , Æ ...
        // Exemple "œ" => "oe"
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        // Supprimer tout le reste
        $str = preg_replace('#&[^;]+;#', '', $str);

        return $str;
    }

    /**
     * @param $haystack
     * @param $needle
     * @return array
     */
    public static function strposAll($haystack, $needle)
    {
        $offset = 0;
        $allpos = array();
        while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
            $offset   = $pos + 1;
            $allpos[] = $pos;
        }
        return $allpos;
    }

    public static function encodeURI($uri)
    {
        return preg_replace_callback("{[^0-9a-z_.!~*'();,/?:@&=+$#-]}i", function ($m) {
            return sprintf('%%%02X', ord($m[0]));
        }, $uri);
    }
}