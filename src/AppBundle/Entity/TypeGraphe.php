<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeGraphe
 *
 * @ORM\Table(name="type_graphe", uniqueConstraints={@ORM\UniqueConstraint(name="code_UNIQUE", columns={"code"}), @ORM\UniqueConstraint(name="libelle_UNIQUE", columns={"libelle"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TypeGrapheRepository")
 */
class TypeGraphe
{
    /**************************************************
     *                   AJOUT MANUEL
     **************************************************/
    /**
     * @var bool
     */
    private $estCocher = false;

    /**
     * @param $estCocher
     * @return $this
     */
    public function setEstCocher($estCocher)
    {
        $this->estCocher = $estCocher;
        return $this;
    }
    /**
     * @return bool
     */
    public function getEstCocher()
    {
        return $this->estCocher;
    }

    /**
     * @var int
     */
    private $idIndictateurTypeGraphe = 0;

    /**
     * @param $idIndictateurTypeGraphe
     * @return $this
     */
    public function setIdIndictateurTypeGraphe($idIndictateurTypeGraphe)
    {
        $this->idIndictateurTypeGraphe = $idIndictateurTypeGraphe;
        return $this;
    }
    /**
     * @return int
     */
    public function getIdIndictateurTypeGraphe()
    {
        return $this->idIndictateurTypeGraphe;
    }

















    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=45, nullable=true)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=45, nullable=true)
     */
    private $img;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set code
     *
     * @param string $code
     *
     * @return TypeGraphe
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return TypeGraphe
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
     * Set icon
     *
     * @param string $icon
     *
     * @return TypeGraphe
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
     * Set img
     *
     * @param string $img
     *
     * @return TypeGraphe
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
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
}
