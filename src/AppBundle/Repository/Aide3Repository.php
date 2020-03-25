<?php
/**
 * Created by PhpStorm.
 * User: INFO
 * Date: 11/09/2017
 * Time: 10:36
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Aide3;
use AppBundle\Entity\Menu;
use AppBundle\Entity\MenuUtilisateur;
use Doctrine\ORM\EntityRepository;

class Aide3Repository extends EntityRepository
{
    /**
     * @param $searchText
     * @param $utilisateur
     * @return array|null
     */
    public function getListeAide3BySearchUtilisateur($searchText, $utilisateur){

        if($searchText != '') {
            $searchText = trim($searchText, " ");
            $searchText = str_replace(" ","%", $searchText);
            $searchText = "%".$searchText."%";
            $aide3 = $this->getEntityManager()
                ->getRepository('AppBundle:Aide3');

            $query = $aide3->createQueryBuilder('a')
                ->where('a.titre LIKE :searchText')
                ->orWhere('a.motCle LIKE :searchText')
                ->setParameter('searchText',$searchText)
                ->getQuery();

            $results = $query->getResult();

            $menuUtilisateurs = $this->getEntityManager()
                ->getRepository('AppBundle:MenuUtilisateur')
                ->getMenuUtilisateur($utilisateur);

            $menus = array();

            /** @var MenuUtilisateur $menuUtilisateur */
            foreach ($menuUtilisateurs as $menuUtilisateur){
                $menus[] = $menuUtilisateur->getMenu();
            }

            $aide3s = array();

            /** @var Aide3 $result */
            foreach ($results as $result){
                if(is_null($result->getMenu())){
                    $aide3s[] = $result;
                }
                else{
                    if(in_array($result->getMenu(), $menus)){
                        $aide3s[] = $result;
                    }
                }
            }
            return $aide3s;
        }
        return null;
    }

    public function getListeAide3ByRoute($route, $menu_ids){

        if(count($route) > 0){
            return $this
                ->createQueryBuilder('a')
                ->leftJoin('a.menu', 'menu')
                ->where('menu.lien in (:the_lien)')
                ->andWhere('menu.id in (:menu_id)')
                ->setParameter('the_lien', $route)
                ->setParameter('menu_id', $menu_ids)
                ->select('a')
                ->getQuery()
                ->getResult();

        }
        else{
            return null;
        }
    }

    public function getListeAide3SansMenu(){
        return $this->
            createQueryBuilder('a')
            ->where('a.menu is null')
            ->getQuery()
            ->getResult();
    }

    public function getRangMaxByAide2($aide2){

        return $this->createQueryBuilder('a')
            ->where('a.aide2 = :aide2')
            ->setParameter(':aide2', $aide2)
            ->select('MAX(a.rang)')
            ->getQuery()
            ->getSingleScalarResult();

    }

    public function getPreviousNextAide3($aide3){

        /** @var Aide3 $aide3 */
        $aide3 = $this->getEntityManager()
            ->getRepository('AppBundle:Aide3')
        ->findBy(array('aide2'=> $aide3->getAide2()));


        $previous = null;
        $next = null;



    }

    public function getListeAide3ByAide1($aide1){
        return $this
            ->createQueryBuilder('a3')
            ->innerJoin('a3.aide2', 'a2')
            ->innerJoin('a2.aide1', 'a1')
            ->where('a1 = :aide1')
            ->setParameter('aide1', $aide1)
            ->getQuery()
            ->getResult();
    }
}