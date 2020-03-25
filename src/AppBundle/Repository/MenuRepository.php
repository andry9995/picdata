<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Menu;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;

class MenuRepository extends EntityRepository
{
    /**
     *  Retourne tous les menus
     *  On séléctionne seulement les parents et les children sont
     *  dans la propriété `children`
     *
     * @return array
     */
    public function getAllMenu()
    {
        $menus = $this->getEntityManager()
            ->getRepository('AppBundle:Menu')
            ->createQueryBuilder('m')
            ->select('m')
            ->leftJoin('m.children', 'children')
            ->addSelect('children')
            ->where('m.menu IS NULL')
            ->orderBy('m.rang', 'ASC')
            ->getQuery()
            ->getResult();
        return $menus;
    }

    public function getParent($menu)
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.admin = :admin')
            ->andWhere('m.menu IS NULL')
            ->setParameter('admin', $menu)
            ->orderBy('m.rang', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function getChilds(Menu $parent, $menu)
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.admin = :admin')
            ->andWhere('m.menu = :parent')
            ->setParameter('admin', $menu)
            ->setParameter('parent', $parent)
            ->orderBy('m.rang', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Get all menu parent for connected User
     *
     * @param Utilisateur $user
     * @return array
     */
    public function getMenuParent(Utilisateur $user)
    {
        $mu_disabled = $this->getMenuDisabled($user);
        $roles_admin = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN'];
        $admin = in_array($user->getAccesUtilisateur()->getCode(), $roles_admin);
//        $admin = ($user->getAccesUtilisateur()->getCode() == 'ROLE_SUPER_ADMIN' || $user->getAccesUtilisateur()->getCode() == 'ROLE_ADMIN') ? 1 : 0;

        if (empty($mu_disabled)) {
            if ($admin) {
                $query = $this->getEntityManager()->getRepository('AppBundle:Menu')->createQueryBuilder('m')
                    ->where('m.menu IS NULL')
                    ->orderBy('m.rang', 'ASC')
                    ->getQuery();
            } else {
                $query = $this->getEntityManager()->getRepository('AppBundle:Menu')->createQueryBuilder('m')
                    ->where('m.menu IS NULL')
                    ->andWhere('m.admin = :admin')
                    ->setParameter('admin', 0)
                    ->orderBy('m.rang', 'ASC')
                    ->getQuery();
            }
        } else {
            if ($admin) {
                $query = $this->getEntityManager()->getRepository('AppBundle:Menu')->createQueryBuilder('m')
                    ->where('m NOT IN (:mu_disabled)')
                    ->andWhere('m.menu IS NULL')
                    ->setParameter('mu_disabled', $mu_disabled)
                    ->orderBy('m.rang', 'ASC')
                    ->getQuery();
            } else {
                $query = $this->getEntityManager()->getRepository('AppBundle:Menu')->createQueryBuilder('m')
                    ->where('m NOT IN (:mu_disabled)')
                    ->andWhere('m.menu IS NULL')
                    ->andWhere('m.admin = :admin')
                    ->setParameter('mu_disabled', $mu_disabled)
                    ->setParameter('admin', 0)
                    ->orderBy('m.rang', 'ASC')
                    ->getQuery();
            }
        }
        $result = $query->getResult();
        return $query->getResult();
    }

    //get menu child
    public function getMenuChild(Menu $parent)
    {
        $query = $this->getEntityManager()->getRepository('AppBundle:Menu')->createQueryBuilder('m')
            ->where('m.menu = :parent')
            ->setParameter('parent', $parent)
            ->orderBy('m.rang', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    //get menu disabled
    public function getMenuDisabled(Utilisateur $user)
    {
        $query = $this->getEntityManager()->getRepository('AppBundle:MenuUtilisateur')->createQueryBuilder('mu')
            ->where('mu.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $user)
            ->getQuery();
        $mu_temps = $query->getResult();
        $mu_disabled = array();
        foreach ($mu_temps as &$mu_temp) {
            $mu_disabled[] = $mu_temp->getMenu()->setIdMenuUtilisateur($mu_temp->getId());
        }

        return $mu_disabled;
    }

    //get all parent
    public function getAllParent($user)
    {
        $mu_disabled = $this->getMenuDisabled($user);
        $admin = ($user->getAccesUtilisateur()->getCode() == 'ROLE_SUPER_ADMIN' || $user->getAccesUtilisateur()->getCode() == 'ROLE_ADMIN') ? 1 : 0;

        $query = $this->getEntityManager()->getRepository('AppBundle:Menu')->createQueryBuilder('m')
            ->where('m.menu IS NULL')
            ->andWhere('m.admin = :admin')
            ->setParameter('admin', $admin)
            ->orderBy('m.rang', 'ASC')
            ->getQuery();

        $menus = $query->getResult();

        /* @var Menu $menu */
        foreach ($menus as &$menu)
            if (in_array($menu, $mu_disabled)) $menu->setActive(false);
            else $menu->setActive(true);

        return $menus;
    }

    //get all chlid
    public function getAllChild(Menu $parent, $user)
    {
        $mu_disabled = $this->getMenuDisabled($user);
        $admin = ($user->getAccesUtilisateur()->getCode() == 'ROLE_SUPER_ADMIN' || $user->getAccesUtilisateur()->getCode() == 'ROLE_ADMIN') ? 1 : 0;

        $query = $this->getEntityManager()->getRepository('AppBundle:Menu')->createQueryBuilder('m')
            ->andWhere('m.admin = :admin')
            ->andWhere('m.menu = :parent')
            ->setParameter('admin', $admin)
            ->setParameter('parent', $parent)
            ->orderBy('m.rang', 'ASC')
            ->getQuery();

        $menus = $query->getResult();

        foreach ($menus as &$menu)
            if (in_array($menu, $mu_disabled)) $menu->setActive(false);
            else $menu->setActive(true);

        return $menus;
    }
}