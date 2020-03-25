<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuUtilisateur
 *
 * @ORM\Table(name="menu_utilisateur", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_menu_utilisateur", columns={"menu_id", "utilisateur_id"})}, indexes={@ORM\Index(name="fk_menu_has_utilisateur_utilisateur1_idx", columns={"utilisateur_id"}), @ORM\Index(name="fk_menu_has_utilisateur_menu1_idx", columns={"menu_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MenuUtilisateurRepository")
 */
class MenuUtilisateur
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
     * @var \AppBundle\Entity\Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;

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
     * Set canEdit
     *
     * @param boolean $canEdit
     *
     * @return MenuUtilisateur
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
     * Set utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     *
     * @return MenuUtilisateur
     */
    public function setUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set menu
     *
     * @param \AppBundle\Entity\Menu $menu
     *
     * @return MenuUtilisateur
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
}
