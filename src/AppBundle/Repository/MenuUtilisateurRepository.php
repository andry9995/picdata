<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 21/03/2017
 * Time: 11:50
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Menu;
use AppBundle\Entity\MenuParRole;
use AppBundle\Entity\MenuUtilisateur;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;

class MenuUtilisateurRepository extends EntityRepository
{
    /**
     *  Liste des menus complets d'un utilisateur
     *  Sans tenir compte des hierachies des menus
     *  Si vide on retourne les menus du rôle
     *
     * @param Utilisateur $utilisateur
     * @return array
     */
    public function getMenuUtilisateur(Utilisateur $utilisateur)
    {
        $menus = $this->getEntityManager()
            ->getRepository('AppBundle:MenuUtilisateur')
            ->createQueryBuilder('menu_utilisateur')
            ->select('menu_utilisateur')
            ->innerJoin('menu_utilisateur.menu', 'menu')
            ->addSelect('menu')
            ->innerJoin('menu_utilisateur.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->where('utilisateur = :utilisateur')
            ->setParameters(array(
                'utilisateur' => $utilisateur,
            ))
            ->getQuery()
            ->getResult();

        if (count($menus) == 0) {
            $menus = $this->getEntityManager()
                ->getRepository('AppBundle:MenuParRole')
                ->getMenuParRole($utilisateur->getAccesUtilisateur());
        }

        return $menus;
    }

    public function getMenuParentUtilisateur(Utilisateur $utilisateur)
    {
        $menus = $this->getEntityManager()
            ->getRepository('AppBundle:MenuUtilisateur')
            ->createQueryBuilder('menu_utilisateur')
            ->select('menu_utilisateur')
            ->innerJoin('menu_utilisateur.menu', 'menu')
            ->addSelect('menu')
            ->innerJoin('menu_utilisateur.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->where('utilisateur = :utilisateur')
            ->andWhere('menu.menu IS NULL')
            ->setParameters(array(
                'utilisateur' => $utilisateur,
            ))
            ->orderBy('menu.rang', 'ASC')
            ->getQuery()
            ->getResult();
        if (count($menus) == 0) {
            $menus = $this->getEntityManager()
                ->getRepository('AppBundle:MenuParRole')
                ->createQueryBuilder('menuParRole')
                ->select('menuParRole')
                ->innerJoin('menuParRole.accesUtilisateur', 'accesUtilisateur')
                ->addSelect('accesUtilisateur')
                ->where('accesUtilisateur = :acces')
                ->innerJoin('menuParRole.menu', 'menu')
                ->addSelect('menu')
                ->andWhere('menu.menu IS NULL')
                ->setParameters(array(
                    'acces' => $utilisateur->getAccesUtilisateur(),
                ))
                ->orderBy('menu.rang', 'ASC')
                ->getQuery()
                ->getResult();
        }

        $parents = [];
        /** @var MenuUtilisateur|MenuParRole $menu */
        foreach ($menus as $menu)
        {
            $parents[] = $menu->getMenu();
        }
        return $parents;
    }

    /**
     *  Liste des menus d'un utilisateur avec
     *  hierarchies
     *
     * @param Utilisateur $utilisateur
     * @param $menus_id
     * @return array
     */
    public function getMenuUtilisateurEx(Utilisateur $utilisateur, &$menus_id)
    {
        $menus = $this->getEntityManager()
            ->getRepository('AppBundle:MenuUtilisateur')
            ->getMenuUtilisateur($utilisateur);
        $menus_id = [];
        /** @var MenuParRole|MenuUtilisateur $menu */
        foreach ($menus as $menu) {
            $menus_id[] = $menu->getMenu()->getId();
        }

        $parents = $this->getEntityManager()
            ->getRepository('AppBundle:MenuUtilisateur')
            ->createQueryBuilder('menu_utilisateur')
            ->select('menu_utilisateur')
            ->innerJoin('menu_utilisateur.menu', 'menu')
            ->addSelect('menu')
            ->innerJoin('menu_utilisateur.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->where('utilisateur = :utilisateur')
            ->andWhere('menu.menu IS NULL')
            ->setParameters(array(
                'utilisateur' => $utilisateur,
            ))
            ->orderBy('menu.rang', 'ASC')
            ->getQuery()
            ->getResult();
        if (count($parents) == 0) {
            $parents = $this->getEntityManager()
                ->getRepository('AppBundle:MenuParRole')
                ->createQueryBuilder('menuParRole')
                ->select('menuParRole')
                ->innerJoin('menuParRole.accesUtilisateur', 'accesUtilisateur')
                ->addSelect('accesUtilisateur')
                ->where('accesUtilisateur = :acces')
                ->innerJoin('menuParRole.menu', 'menu')
                ->addSelect('menu')
                ->andWhere('menu.menu IS NULL')
                ->setParameters(array(
                    'acces' => $utilisateur->getAccesUtilisateur(),
                ))
                ->orderBy('menu.rang', 'ASC')
                ->getQuery()
                ->getResult();
        }

        $liste_menus = [];
        if (count($parents) == 0) {
            return [];
        } else {
            /** @var MenuParRole|MenuUtilisateur $parent */
            foreach ($parents as &$parent) {
                $level1 = $parent->getMenu();
                $liste_menus[] = $level1;

                $childs = $this->getEntityManager()
                    ->getRepository('AppBundle:Menu')
                    ->getMenuChild($level1);
                if (count($childs) > 0) {
                    $level1->setChild($childs);
                    /** @var Menu $child */
                    foreach ($childs as &$child) {
                        $childs_2 = $this->getEntityManager()
                            ->getRepository('AppBundle:Menu')
                            ->getMenuChild($child);

                        if (count($childs_2) > 0) {
                            $child->setChild($childs_2);
                            /** @var Menu $child_2 */
                            foreach ($childs_2 as &$child_2) {
                                $childs_3 = $this->getEntityManager()
                                    ->getRepository('AppBundle:Menu')
                                    ->getMenuChild($child_2);
                                if (count($childs_3) > 0) {
                                    $child_2->setChild($childs_3);
                                    /** @var Menu $child_3 */
                                    foreach ($childs_3 as &$child_3) {
                                        $childs_4 = $this->getEntityManager()
                                            ->getRepository('AppBundle:Menu')
                                            ->getMenuChild($child_3);
                                        if (count($childs_4) > 0) {
                                            $child_3->setChild($childs_3);
                                            /** @var Menu $child_4 */
                                            foreach ($childs_4 as &$child_4) {

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $liste_menus;
    }

    /**
     * Supprimer les menus d'un utilisateur
     *
     * @param Utilisateur $utilisateur
     */
    public function removeMenuUtilisateur(Utilisateur $utilisateur)
    {
        $menus = $menus = $this->getEntityManager()
            ->getRepository('AppBundle:MenuUtilisateur')
            ->createQueryBuilder('menu_utilisateur')
            ->select('menu_utilisateur')
            ->innerJoin('menu_utilisateur.menu', 'menu')
            ->addSelect('menu')
            ->innerJoin('menu_utilisateur.utilisateur', 'utilisateur')
            ->addSelect('utilisateur')
            ->where('utilisateur = :utilisateur')
            ->setParameters(array(
                'utilisateur' => $utilisateur,
            ))
            ->getQuery()
            ->getResult();

        if (count($menus) > 0) {
            $em = $this->getEntityManager();
            foreach ($menus as $menu) {
                $em->remove($menu);
            }
            $em->flush();
        }
    }
}