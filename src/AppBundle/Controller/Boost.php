<?php
    namespace AppBundle\Controller;

    use \PHPExcel_Style_Fill;
    use Symfony\Component\Serializer\Serializer;
    use Symfony\Component\Serializer\Encoder\XmlEncoder;
    use Symfony\Component\Serializer\Encoder\JsonEncoder;
    use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
    use \DateTime;
    class Boost
    {
        /**
         * crypter
         * @param $str
         * @return string
         */
        public static function boost($str)
        {
            $cle = Boost::getUuid();
            $cle64 = base64_encode($cle);
            $izy = $cle.$str.$cle64;
            $cle64_1 = base64_encode($izy);
            return $cle64_1;
        }

        /**
         * @param $str
         * @param $controler
         * @return bool|mixed
         */
        public static function deboost($str,$controler)
        {
            $cle64_1 = base64_decode($str);
            $cle = substr($cle64_1,0,13);
            $queue = base64_encode($cle);
            $lenth = strlen($queue);
            $rambony = substr($cle64_1,-$lenth);

            if($rambony != $queue)
            {
                $controler->get('security.context')->setToken(null);
                $controler->get('request')->getSession()->invalidate();
                return false;
            }

            $result = str_replace($rambony,'',$cle64_1);
            $result = str_replace($cle,'',$result);
            return $result;
        }

        /**
         * à Utiliser si aucun utilsateur n'est connecté
         *
         * @param $str
         * @return bool|mixed
         */
        public static function deboostWithoutController($str)
        {
            $cle64_1 = base64_decode($str);
            $cle = substr($cle64_1,0,13);
            $queue = base64_encode($cle);
            $lenth = strlen($queue);
            $rambony = substr($cle64_1,-$lenth);

            if($rambony != $queue)
            {
                return false;
            }

            $result = str_replace($rambony,'',$cle64_1);
            $result = str_replace($cle,'',$result);
            return $result;
        }

        /**
         * @param $mois
         * @param bool $abreviation
         * @param bool $majuscule
         * @return string
         */
        public static function getMoisLettre($mois,$abreviation = true,$majuscule = true)
        {
            $lettre = '';
            if($mois == 1) $lettre = ($abreviation) ? 'JAN' : 'JANVIER';
            if($mois == 2) $lettre = ($abreviation) ? 'FEV' : 'FEVRIER';
            if($mois == 3) $lettre = ($abreviation) ? 'MAR' : 'MARS';
            if($mois == 4) $lettre = ($abreviation) ? 'AVR' : 'AVRIL';
            if($mois == 5) $lettre = ($abreviation) ? 'MAI' : 'MAI';
            if($mois == 6) $lettre = ($abreviation) ? 'JUI' : 'JUIN';
            if($mois == 7) $lettre = ($abreviation) ? 'JUL' : 'JUILLET';
            if($mois == 8) $lettre = ($abreviation) ? 'AOU' : 'AOUT';
            if($mois == 9) $lettre = ($abreviation) ? 'SEP' : 'SEPTEMBRE';
            if($mois == 10) $lettre = ($abreviation) ? 'OCT' : 'OCTOBRE';
            if($mois == 11) $lettre = ($abreviation) ? 'NOV' : 'NOVEMBRE';
            if($mois == 12) $lettre = ($abreviation) ? 'DEC' : 'DECEMBRE';

            if(!$majuscule) $lettre = strtolower($lettre);

            return $lettre;
        }

        /**
         * @param $number
         * @param null $dec_point
         * @return float
         */
        public static function parseNumber($number, $dec_point=null)
        {
            if (empty($dec_point)) {
                $locale = localeconv();
                $dec_point = $locale['decimal_point'];
            }
            $result = floatval(str_replace($dec_point, '.', preg_replace('/[^\d'.preg_quote($dec_point).']/', '', $number)));
            return (substr($number,0,1) == '-') ? -$result : $result;
        }

        /**
         * @param $objPHPExcel
         * @param $cells
         * @param $color
         */
        public static function cellColor($objPHPExcel,$cells,$color){
            $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => $color
                )
            ));
        }

        /**
         * @param $objPHPExcel
         * @param $cells
         * @param $color
         * @param bool $bold
         * @param bool $size
         */
        public static function cellTextColor($objPHPExcel,$cells,$color,$bold=false,$size=false)
        {
            $array_temp = array();
            $array_temp['bold'] = $bold;
            $array_temp['color'] = array('rgb' => $color);
            if(!is_bool($size)) $array_temp['size'] = $size;

            $objPHPExcel->getActiveSheet()->getStyle($cells)->applyFromArray(
                array('font'  => $array_temp)
            );
        }

        /**
         * @param $char
         * @return string
         */
        public static function getNextChar($char)
        {
            if($char == 'Z') return 'A';
            $char++;
            return $char;
        }

        /**
         * @param $object
         * @param string $format
         * @return string|\Symfony\Component\Serializer\Encoder\scalar
         */
        public static function serialize($object, $format = 'json')
        {
            /*$encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            return $serializer->serialize($object, $format);*/


            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });
            $serializer = new Serializer(array($normalizer), array($encoder));

            return $serializer->serialize($object,$format);
            //return new Response($serializer->serialize($dossiers, 'json'));
        }

        /**
         * @param int $len
         * @return string
         */
        public static function getUuid($len = 13)
        {
//            $string = "";
//            $chaine = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_";
//            srand((double)microtime()*1000000);
//            for($i=0; $i<$len; $i++) $string .= $chaine[rand()%strlen($chaine)];
//            return $string;
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_';
            $pass = array();
            $characters_length = strlen($characters) - 1;
            for ($i = 0; $i < $len; $i++) {
                $n = rand(0, $characters_length);
                $pass[] = $characters[$n];
            }
            return implode($pass);
        }

        /**
         * @return array
         */
        public static function getExercices($nbAnnee = 6,$debut = 1)
        {
            $date_now = new DateTime();
            $current_year = intval($date_now->format('Y'));
            $exercices = array();

            for($i=0;$i<$nbAnnee;$i++)
                $exercices[] = $current_year + $debut - $i + 1;

            return $exercices;
        }

        /**
         * @param $mois_cloture
         * @return array
         */
        public static function getMois($mois_cloture)
        {
            if($mois_cloture == 0) $mois_cloture = 12;
            $moiss = array();

            for($i = 1;$i <= 12;$i++)
            {
                $mois_cloture++;
                if($mois_cloture == 13) $mois_cloture = 1;

                $moiss[$mois_cloture] = Boost::getMoisLettre($mois_cloture);
            }
            return $moiss;
        }

        /**
         * @param $exercices
         * @param $moiss
         * @return string
         */
        public static function getDatePickerPopOver($exercices,$moiss)
        {
            $contenu = '<table class="table table-condensed table-hover no-margin table-no-padding js_xdp">';
            $contenu .= '<thead><tr class="pointer">';

            $date_now = new DateTime();
            $current_year = intval($date_now->format('Y'));

            foreach ($exercices as $exercice)
                $contenu .= '<th class="js_dp_exercice '. ((intval($exercice) == $current_year) ? 'success' : '') .'">' . $exercice . '</th>';
            $contenu .= '</tr></thead><tbody>';
            $trimestre = 0;
            $index = 0;

            foreach ($moiss as $key => $val)
            {
                if($index == 0 || $index % 3 == 0)
                {
                    $trimestre++;
                    $contenu .= '<tr class="pointer" data-trimestre="' . $trimestre . '">
                        <th class="success js_dp_T' . $trimestre . ' js_dp_trimestre" colspan="2">T' . $trimestre . '</th>';
                }

                $contenu .= '<td class="warning js_dp_mois" data-val="' . $key . '">' . $val . '</td>';
                if(($index == count($moiss) - 1) || (($index + 1) % 3 == 0))
                {
                    $contenu .= '</tr>';
                }
                $index++;
            }

            $contenu .= '</tbody><tfoot>';
            $contenu .= '<th colspan="4" class="text-center"><span class="btn btn-primary btn-xs js_xdp_valider"><i class="fa fa-check"></i>&nbsp;Valider</span></th>';
            $contenu .= '</tfoot></table>';

            return $contenu;
        }

        /**
         * selecteur dpk
         * js: bundles/app/js/datePickerPopOverV2.js
         *
         * @param $exercices
         * @param $moiss
         * @return string
         */
        public static function getDatePickerPopOverV2($exercices,$moiss)
        {
            $contenu =  '<div class="row">'.
                            '<div class="col-sm-12">'.
                                '<span><strong>Exercice</strong></span>'.
                                '<table class="table table-condensed table-bordered table-hover no-margin table-no-padding table-dpk pointer">'.
                                    '<thead>'.
                                        '<tr>';
            //exercice
            $date_now = new DateTime();
            $current_year = intval($date_now->format('Y'));
            foreach ($exercices as $exercice)
                $contenu .= '<th class="text-center js_dpk_exercice '.((intval($exercice) == $current_year) ? 'td-active' : '').'">'.$exercice.'</th>';

            $contenu .=
                                        '</tr>'.
                                    '</thead>'.
                                '</table>'.
                            '</div>'.
                        '</div>'.

                        '<div class="row">'.
                            '<div class="col-sm-12" style="margin-top:10px!important">'.
                                '<span><strong>Periode</strong></span>'.
                                '<table class="table table-condensed table-bordered table-hover no-margin text-center table-dpk">'.
                                    '<tbody>';

            $index = $trimestre = $semestre = 0;
            $annee = 1;

            foreach ($moiss as $key => $val)
            {
                $isSemestre = false;
                //semestre
                if($index == 0 || $index % 6 == 0)
                {
                    $semestre++;
                    $isSemestre = true;
                    $contenu .= '<tr>';
                    if($index == 0) $contenu .= '<th rowspan="4" class="js_dpk_periode" data-val="'.$annee.'" data-mere-annee="-1" data-mere-semestre="-1" data-mere-trimestre="-1" data-niveau="0">A</th>';
                    $contenu .= '<th rowspan="2" class="js_dpk_periode js_dpk_semestre" data-val="'.$semestre.'" data-mere-annee="'.$annee.'" data-mere-semestre="-1" data-mere-trimestre="-1" data-niveau="1">S'.$semestre.'</th>';
                }

                //trimestre
                if($index == 0 || $index % 3 == 0)
                {
                    $trimestre++;
                    if(!$semestre)
                        $contenu .= '<tr>';
                    $contenu .= '<th class="js_dpk_periode js_dpk_trimestre" data-val="'.$trimestre.'" data-mere-annee="'.$annee.'" data-mere-semestre="'.$semestre.'" data-mere-trimestre="-1" data-niveau="2">T'.$trimestre.'</th>';
                }

                $contenu .= '<td class="js_dpk_periode js_dpk_mois td-active" data-value="'.$key.'" data-val="-1" data-mere-annee="'.$annee.'" data-mere-semestre="'.$semestre.'" data-mere-trimestre="'.$trimestre.'" data-niveau="3">' . $val . '</td>';

                //fermeture tr
                if($index == count($moiss) - 1 || (($index + 1) % 3 == 0))
                {
                    $contenu .= '</tr>';
                }

                $index++;
            }

            $contenu .=
                                    '</tbody>'.
                                '</table>'.
                            '</div>'.
                        '</div>'.
                        '<div class="row" style="margin-top:10px!important">'.
                            '<div class="col-sm-12 text-right">'.
                                '<span class="btn btn-primary btn-xs js_dpk_valider"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;Valider</span>'.
                            '</div>'.
                        '</div>';

            return $contenu;
        }

        /**
         * @param $string
         * @param string $separateur
         * @param int $anneeMoisJour
         * @return DateTime
         */
        public static function getDateByString($string,$separateur = '-',$anneeMoisJour = 1)
        {
            if($string == null) return null;
            $annee = $mois = $jour = null;
            $stringSpliter = explode($separateur,$string);
            if($anneeMoisJour == 1)
            {
                $annee = $stringSpliter[0];
                $mois = $stringSpliter[1];
                $jour = $stringSpliter[2];
            }
            elseif($anneeMoisJour == 2)
            {
                $jour = $stringSpliter[0];
                $mois = $stringSpliter[1];
                $annee = $stringSpliter[2];
            }
            elseif($anneeMoisJour == 3 || $anneeMoisJour == 4)
            {
                if($anneeMoisJour == 3)
                {
                    $annee = substr($string,0,4);
                    $mois = substr($string,4,2);
                    $jour = substr($string,6,2);
                }
                else
                {
                    $jour = substr($string,0,2);
                    $mois = substr($string,2,2);
                    $annee = substr($string,4,4);
                }
            }
            return new DateTime($annee.'-'.$mois.'-'.$jour);
        }
    }
?>