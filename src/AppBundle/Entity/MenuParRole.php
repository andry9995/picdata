<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuParRole
 *
 * @ORM\Table(name="menu_par_role", indexes={@ORM\Index(name="fk_menu_par_role_acces_utilisateur1_idx", columns={"acces_utilisateur_id"}), @ORM\Index(name="fk_menu_par_role_menu1_idx", columns={"menu_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MenuParRoleRepository")
 */
class MenuParRole
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="can_edit", type="boolean", nullable=false)
     */
    private $canEdit = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Menu
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Menu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     * })
     */
    private $menu;

    /**
     * @var \AppBundle\Entity\AccesUtilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AccesUtilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="acces_utilisateur_id", referencedColumnName="id")
     * })
     */
    private $accesUtilisateur;



    /**
     * Set canEdit
     *
     * @param boolean $canEdit
     *
     * @return MenuParRole
     */
    public function setCanEdit($canEdit)
    {
        $this->canEdit = $canEdit;

        return $this;
    }

    /**
     * Get canEdit
     *
     * @return boolean
     */
    public function getCanEdit()
    {
        return $this->canEdit;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set menu
     *
     * @param \AppBundle\Entity\Menu $menu
     *
     * @return MenuParRole
     */
    public function setMenu(\AppBundle\Entity\Menu $menu = null)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return \AppBundle\Entity\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set accesUtilisateur
     *
     * @param \AppBundle\Entity\AccesUtilisateur $accesUtilisateur
     *
     * @return MenuParRole
     */
    public function setAccesUtilisateur(\AppBundle\Entity\AccesUtilisateur $accesUtilisateur = null)
    {
        $this->accesUtilisateur = $accesUtilisateur;

        return $this;
    }

    /**
     * Get accesUtilisateur
     *
     * @return \AppBundle\Entity\AccesUtilisateur
     */
    public function getAccesUtilisateur()
    {
        return $this->accesUtilisateur;
    }
}
