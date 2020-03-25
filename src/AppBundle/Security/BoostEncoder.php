<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 07/02/2017
 * Time: 14:09
 */

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class BoostEncoder extends BasePasswordEncoder
{

    /**
     * Encodes the raw password.
     *
     * @param string $raw The password to encode
     * @param string $salt The salt
     *
     * @return string The encoded password
     */
    public function encodePassword($raw, $salt)
    {
        if ($this->isPasswordTooLong($raw)) {
            throw new BadCredentialsException('Invalid password.');
        }

        return BoostEncoder::boost($raw);
    }

    /**
     * Checks a raw password against an encoded password.
     *
     * @param string $encoded An encoded password
     * @param string $raw A raw password
     * @param string $salt The salt
     *
     * @return bool true if the password is valid, false otherwise
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        if ($this->isPasswordTooLong($raw)) {
            return false;
        }

        $decoded = BoostEncoder::deboost($encoded);
        return ($decoded === $raw);
    }

    /**
     * crypter
     * @param $str
     * @return string
     */
    public static function boost($str)
    {
        $cle = BoostEncoder::getUuid();
        $cle64 = base64_encode($cle);
        $izy = $cle . $str . $cle64;
        $cle64_1 = base64_encode($izy);
        return $cle64_1;
    }

    /**
     * @param $str
     * @return bool|mixed
     */
    public static function deboost($str)
    {
        $cle64_1 = base64_decode($str);
        $cle = substr($cle64_1, 0, 13);
        $queue = base64_encode($cle);
        $lenth = strlen($queue);
        $rambony = substr($cle64_1, -$lenth);

        if ($rambony != $queue) {
            return false;
        }

        $result = str_replace($rambony, '', $cle64_1);
        $result = str_replace($cle, '', $result);
        return $result;
    }

    /**
     * @param int $len
     * @return string
     */
    public static function getUuid($len = 13)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_';
        $pass = array();
        $characters_length = strlen($characters) - 1;
        for ($i = 0; $i < $len; $i++) {
            $n = rand(0, $characters_length);
            $pass[] = $characters[$n];
        }
        return implode($pass);
    }
}