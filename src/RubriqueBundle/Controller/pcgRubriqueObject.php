<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 24/10/2016
 * Time: 16:19
 */

namespace RubriqueBundle\Controller;

use AppBundle\Entity\Pcg;

class pcgRubriqueObject
{
    public $pcg;
    public $rubriques;
    public $superRubriques;
    public $hyperRubriques;

    function __construct(Pcg $pcg, $rubriques = array(), $superRubriques = array(), $hyperRubriques = array())
    {
        $this->pcg = $pcg;
        $this->rubriques = $rubriques;
        $this->superRubriques = $superRubriques;
        $this->hyperRubriques = $hyperRubriques;
    }
}