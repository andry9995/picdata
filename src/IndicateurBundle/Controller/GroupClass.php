<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 23/09/2016
 * Time: 11:57
 */

namespace IndicateurBundle\Controller;

class GroupClass
{
    public $group;
    public $indicateur;
    public $indicateurItem;

    public function __construct($group,$indicateur,$indicateurItem)
    {
        $this->group = $group;
        $this->indicateur = $indicateur;
        $this->indicateurItem = $indicateurItem;
    }
}