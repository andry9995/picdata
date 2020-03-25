<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 20/03/2017
 * Time: 16:00
 */

namespace AppBundle\Repository;

use AppBundle\Entity\AccesUtilisateur;
use Doctrine\ORM\EntityRepository;

class MenuParRoleRepository extends EntityRepository
{
    /**
     * Liste des menus par rôle
     *
     * @param AccesUtilisateur $acces
     * @return array
     */
    public function getMenuParRole(AccesUtilisateur $acces)
    {
        $menus = $this->getEntityManager()
            ->getRepository('AppBundle:MenuParRole')
            ->createQueryBuilder('menuParRole')
            ->select('menuParRole')
            ->innerJoin('menuParRole.accesUtilisateur', 'accesUtilisateur')
            ->addSelect('accesUtilisateur')
            ->where('accesUtilisateur = :acces')
            ->innerJoin('menuParRole.menu', 'menu')
            ->addSelect('menu')
            ->setParameters(array(
                'acces' => $acces
            ))
            ->orderBy('menu.rang')
            ->getQuery()
            ->getResult();
        return $menus;
    }

    /**
     * Supprimer menus d'un rôle
     *
     * @param AccesUtilisateur $acces
     * @return bool
     */
    public function removeRoleMenus(AccesUtilisateur $acces)
    {
        $em = $this->getEntityManager();
        $menus = $this->getEntityManager()
            ->getRepository('AppBundle:MenuParRole')
            ->getMenuParRole($acces);
        foreach ($menus as $menu) {
            $em->remove($menu);
        }
        $em->flush();
        return true;
    }
}