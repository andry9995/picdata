<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table(name="menu", indexes={@ORM\Index(name="fk_menu_menu1_idx", columns={"menu_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MenuRepository")
 */
class Menu
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50, nullable=false)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="lien", type="string", length=255, nullable=false)
     */
    private $lien;

    /**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer", nullable=false)
     */
    private $rang = '1000';

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=45, nullable=false)
     */
    private $icon = 'fa-th-large';

    /**
     * @var integer
     *
     * @ORM\Column(name="admin", type="integer", nullable=false)
     */
    private $admin = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="js", type="integer", nullable=false)
     */
    private $js = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="donnees", type="string", length=250, nullable=true)
     */
    private $donnees;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=250, nullable=true)
     */
    private $class;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Menu", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     * })
     */
    private $menu;

    /**
     * Un menu peut avoir plusieurs sous-menus
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Menu", mappedBy="menu")
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Menu
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set lien
     *
     * @param string $lien
     *
     * @return Menu
     */
    public function setLien($lien)
    {
        $this->lien = $lien;

        return $this;
    }

    /**
     * Get lien
     *
     * @return string
     */
    public function getLien()
    {
        return $this->lien;
    }

    /**
     * Set rang
     *
     * @param integer $rang
     *
     * @return Menu
     */
    public function setRang($rang)
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang
     *
     * @return integer
     */
    public function getRang()
    {
        return $this->rang;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return Menu
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set admin
     *
     * @param integer $admin
     *
     * @return Menu
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return integer
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set js
     *
     * @param integer $js
     *
     * @return Menu
     */
    public function setJs($js)
    {
        $this->js = $js;

        return $this;
    }

    /**
     * Get js
     *
     * @return integer
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * Set donnees
     *
     * @param string $donnees
     *
     * @return Menu
     */
    public function setDonnees($donnees)
    {
        $this->donnees = $donnees;

        return $this;
    }

    /**
     * Get donnees
     *
     * @return string
     */
    public function getDonnees()
    {
        return $this->donnees;
    }


    /**
     * @param $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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

    /*Modification manuelle*/
    private $idMenuUtilisateur = 0;
    private $childs = array();
    private $active;

    /**
     * set menu childs
     *
     * @param array $childs
     * @return $this
     */
    public function setChild(Array $childs)
    {
        foreach ($childs as $submenu) {
            $this->childs[] = $submenu;
        }
        return $this;
    }

    /**
     * Get menu child
     *
     * @return array()
     */
    public function getChild()
    {
        return $this->childs;
    }

    /**
     * set menu active
     *
     * @param
     *
     * @return Menu
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get menu active
     *
     * @return Boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * set menu utilisateur
     *
     * @param integer idMenuUtilisateur
     *
     * @return Menu
     */
    public function setIdMenuUtilisateur($idMenuUtilisateur)
    {
        $this->idMenuUtilisateur = $idMenuUtilisateur;
        return $this;
    }

    /**
     * Get menu utilisateur
     *
     * @return integer
     */
    public function getIdMenuUtilisateur()
    {
        return $this->idMenuUtilisateur;
    }

    /**
     * Set menu
     *
     * @param \AppBundle\Entity\Menu $menu
     *
     * @return Menu
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
     * Add child
     *
     * @param \AppBundle\Entity\Menu $child
     *
     * @return Menu
     */
    public function addChildren(\AppBundle\Entity\Menu $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Menu $child
     */
    public function removeChildren(\AppBundle\Entity\Menu $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function clearChildren() {
        $this->children = new ArrayCollection();
    }
}
