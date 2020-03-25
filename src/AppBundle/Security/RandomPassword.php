<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 14/02/2017
 * Time: 16:48
 */

namespace AppBundle\Security;

/**
 * Generate random password
 *
 * Class RandomPassword
 * @package AppBundle\Security
 */
class RandomPassword
{
    public static function generate($length = 8)
    {
//        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!#$%&()*+,-./:;<=>?@[\]^_`{|}~';
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $characters_length = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $characters_length);
            $pass[] = $characters[$n];
        }
        return implode($pass);
    }
}