<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 18/07/2019
 * Time: 10:37
 */

namespace AppBundle\Controller;


class DateExt
{
    /**
     * @param \DateTime $d
     * @return \DateTime
     */
    public static function getNextOuvrable(\DateTime $d)
    {
        $date = clone $d;
        while($date->format('w') == 6 || $date->format('w') == 0)
            $date->add(new \DateInterval('P1D'));

        return $date;
    }

    /**
     * @param \DateTime $nais
     * @return array
     */
    public static function calculAge($nais)
    {
        $nais = explode('/', $nais->format('d/m/Y'));
        $nais1 = $nais[0];
        $nais2 = $nais[1];
        $nais3 = $nais[2];

        if ($nais1 <= 31 && $nais2 <= 12 && $nais3 > 1900)
        {
            $date = date('d');
            $mois = date('m');
            $jahre = date('Y') - $nais3;

            $monat = 0;

            if ($mois >= $nais2)
                $monat = $mois - $nais2;
            if ($mois < $nais2)
            {
                $monat = (12 - $nais2) + $mois;
                $jahre -= 1;
            }
            if ($date < $nais1)
            {
                $jahre -= 1;
                $monat = (12 - $nais2) + $mois;
                if ($mois < $nais2)
                {
                    $jahre++;
                    $monat -= 1;
                }
            }
            if ($monat >= 13)
            {
                $jahre++;
                $monat -= 13;
            }
            if ($monat == 12)
                $monat = 11;

            if ($date >= $nais1)
                $tag = $nais1 - $date;
            else
            {
                $tag = $date + (31 - $nais1);
                if (($mois / 2) == round($mois / 2))
                    $tag++;
            }
            if ($tag < 0) $tag *= -1;;

            $retour = [];
            $retour[0] = $jahre; //age en années
            $retour[1] = $monat; //age en mois
            $retour[2] = $tag; //age en jours
        }
        else $retour[0] = 'Erreur';

        return $retour;
    }
}