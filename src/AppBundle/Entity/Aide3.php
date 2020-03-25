<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Aide3
 *
 * @ORM\Table(name="aide_3", uniqueConstraints={@ORM\UniqueConstraint(name="unique", columns={"aide_2_id", "titre"})}, indexes={@ORM\Index(name="fk_aide_3_aide_2_idx", columns={"aide_2_id"}), @ORM\Index(name="fk_aide_3_aide_3_idx", columns={"aide_associe"}), @ORM\Index(name="fk_aide_3_menu_idx", columns={"menu_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Aide3Repository")
 */
class Aide3
{
    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=100, nullable=true)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", length=65535, nullable=true)
     */
    private $contenu;

    /**
     * @var integer
     *
     * @ORM\Column(name="suggestion", type="integer", nullable=true)
     */
    private $suggestion = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="mot_cle", type="text", length=65535, nullable=true)
     */
    private $motCle;

    /**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer", nullable=true)
     */
    private $rang;

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
     * @var \AppBundle\Entity\Aide3
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Aide3")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="aide_associe", referencedColumnName="id")
     * })
     */
    private $aideAssocie;

    /**
     * @var \AppBundle\Entity\Aide2
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Aide2")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="aide_2_id", referencedColumnName="id")
     * })
     */
    private $aide2;



    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Aide3
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Aide3
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set suggestion
     *
     * @param integer $suggestion
     *
     * @return Aide3
     */
    public function setSuggestion($suggestion)
    {
        $this->suggestion = $suggestion;

        return $this;
    }

    /**
     * Get suggestion
     *
     * @return integer
     */
    public function getSuggestion()
    {
        return $this->suggestion;
    }

    /**
     * Set motCle
     *
     * @param string $motCle
     *
     * @return Aide3
     */
    public function setMotCle($motCle)
    {
        $this->motCle = $motCle;

        return $this;
    }

    /**
     * Get motCle
     *
     * @return string
     */
    public function getMotCle()
    {
        return $this->motCle;
    }

    /**
     * Set rang
     *
     * @param integer $rang
     *
     * @return Aide3
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
     * @return Aide3
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
     * Set aideAssocie
     *
     * @param \AppBundle\Entity\Aide3 $aideAssocie
     *
     * @return Aide3
     */
    public function setAideAssocie(\AppBundle\Entity\Aide3 $aideAssocie = null)
    {
        $this->aideAssocie = $aideAssocie;

        return $this;
    }

    /**
     * Get aideAssocie
     *
     * @return \AppBundle\Entity\Aide3
     */
    public function getAideAssocie()
    {
        return $this->aideAssocie;
    }

    /**
     * Set aide2
     *
     * @param \AppBundle\Entity\Aide2 $aide2
     *
     * @return Aide3
     */
    public function setAide2(\AppBundle\Entity\Aide2 $aide2 = null)
    {
        $this->aide2 = $aide2;

        return $this;
    }

    /**
     * Get aide2
     *
     * @return \AppBundle\Entity\Aide2
     */
    public function getAide2()
    {
        return $this->aide2;
    }
}
