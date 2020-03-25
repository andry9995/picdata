<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 27/10/2017
 * Time: 16:39
 */

namespace AppBundle\Repository;


use AppBundle\Entity\AideAssocie;
use AppBundle\Entity\MenuUtilisateur;
use Doctrine\ORM\EntityRepository;

class AideAssocieRepository extends  EntityRepository
{
    public function getAssociesByAide3Utilisateur($aide3, $utilisateur){


        $associes = $this->getEntityManager()
            ->getRepository('AppBundle:AideAssocie');

        $query = $associes->createQueryBuilder('assoc')
            ->where('assoc.aide3Parent = :aide3')
            ->setParameter(':aide3', $aide3);

        $results = $query->getQuery()
            ->getResult();


        $menuUtilisateurs = $this->getEntityManager()
            ->getRepository('AppBundle:MenuUtilisateur')
            ->getMenuUtilisateur($utilisateur);

        $menus = array();

        /** @var MenuUtilisateur $menuUtilisateur */
        foreach ($menuUtilisateurs as $menuUtilisateur){
            $menus[] = $menuUtilisateur->getMenu();
        }

        $aideAssocies = array();

        /** @var AideAssocie $result */
        foreach ($results as $result){
            if(is_null($result->getAide3Associe()->getMenu())){
                $aideAssocies[] = $result;
            }
            else{
                if(in_array($result->getAide3Associe()->getMenu(), $menus)){
                    $aideAssocies[] = $result;
                }
            }
        }

        return $aideAssocies;


    }

}