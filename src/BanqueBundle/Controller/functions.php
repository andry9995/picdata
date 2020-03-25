<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 10/08/2018
 * Time: 13:16
 */

namespace BanqueBundle\Controller;


use AppBundle\Entity\Pcc;
use AppBundle\Entity\Tiers;
use Doctrine\Bundle\DoctrineBundle\Registry;

class functions
{
    /**
     * @param Pcc[] $comptes
     * @param Registry $doctrine
     * @return array
     */
    public static function getParentsChilds($comptes, $doctrine)
    {
        $pcgsChilds = [];
        $pcgsParents = [];
        $pcgsObjects = [];
        foreach ($comptes as $pcg)
        {
            //$pcg = new Pcc();
            $compte = trim($pcg->getCompte());
            $pcgsChilds['0-'.$compte] = [];
            $pcgsObjects['0-'.$compte] = (object)
            [
                'compte' => $compte,
                'intitule' => $pcg->getIntitule(),
                'id' => $pcg->getId(),
                't' => 0
            ];
            $parent = null;

            for ($i = strlen($compte) - 1; $i >= 0; $i--)
            {
                $key = substr($compte,0,$i);
                if (array_key_exists('0-'.$key,$pcgsChilds))
                {
                    $pcgsChilds['0-'.$key][] = '0-'.$compte;
                    $parent = '0-'.$key;
                    break;
                }
            }

            if ($pcg->getCollectifTiers() != -1)
            {
                /** @var Tiers[] $tiers */
                $tiers = $doctrine->getRepository('AppBundle:Tiers')
                    ->createQueryBuilder('t')
                    ->where('t.pcc = :pcc')
                    ->setParameter('pcc',$pcg)
                    ->orderBy('t.intitule')
                    ->getQuery()
                    ->getResult();

                $existe = false;
                foreach ($tiers as $tier)
                {
                    //$tier = new Tiers();
                    $compteTiers = trim($tier->getCompteStr());
                    $pcgsChilds['1-'.$compteTiers] = [];
                    $pcgsChilds['0-'.$compte][] = '1-'.$compteTiers;

                    $pcgsObjects['1-'.$compteTiers] = (object)
                    [
                        'compte' => $compteTiers,
                        'intitule' => $tier->getIntitule(),
                        'id' => $tier->getId(),
                        't' => 1
                    ];
                    $existe = true;
                }
            }

            if ($parent == null) $pcgsParents[] = '0-'.$compte;
        }

        $results = [];
        foreach ($pcgsParents as $pcgsParent)
        {
            $results[] = functions::getTree($pcgsParent,$pcgsChilds,$pcgsObjects,[]);
        }

        return $results;
    }

    public static function getTree($parent,$childs,$objects,$selecteds = [],$isPcc = false)
    {
        if (count($childs[$parent]) !=  0)
        {
            $childrens = [];
            foreach ($childs[$parent] as $child)
            {
                $childrens[] = functions::getTree($child,$childs,$objects,$selecteds,$isPcc);
            }

            $texte = $objects[$parent]->compte . ' - ' . $objects[$parent]->intitule;
            $texte = ($isPcc) ? substr($texte,2) : $texte;
            return (object)
            [
                'text' => $texte,
                'icon' => 'none',
                'children' => $childrens,
                'id' => $objects[$parent]->t . '#' . $objects[$parent]->id,
                'state' => (object)
                [
                    'selected' => in_array($objects[$parent]->t . '#' . $objects[$parent]->id,$selecteds),
                    'checked' => in_array($objects[$parent]->t . '#' . $objects[$parent]->id,$selecteds)
                ]
            ];
        }

        $texte = $objects[$parent]->compte . ' - ' . $objects[$parent]->intitule;
        $texte = ($isPcc) ? substr($texte,2) : $texte;
        return (object)
        [
            'text' => $texte,
            'icon' => 'none',
            'children'  => [],
            'id' => $objects[$parent]->t . '#' . $objects[$parent]->id,
            'state' => (object)
            [
                'selected' => in_array($objects[$parent]->t . '#' . $objects[$parent]->id,$selecteds),
                'checked' => in_array($objects[$parent]->t . '#' . $objects[$parent]->id,$selecteds)
            ]
        ];
    }

    /**
     * @param $array
     * @return array
     */
    public static function allCombinaisons($array)
    {
        $results = array(array());

        foreach ($array as $element)
            foreach ($results as $combination)
                array_push($results, array_merge(array($element), $combination));

        return $results;
    }
}