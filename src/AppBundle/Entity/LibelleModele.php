<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LibelleModele
 *
 * @ORM\Table(name="libelle_modele", indexes={@ORM\Index(name="fk_libelle_modele_item1_idx", columns={"libelle_item_id"}), @ORM\Index(name="fk_libelle_modele_type1_idx", columns={"libelle_type_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LibelleModeleRepository")
 */
class LibelleModele
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer", nullable=false)
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
     * @var \AppBundle\Entity\LibelleType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LibelleType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="libelle_type_id", referencedColumnName="id")
     * })
     */
    private $libelleType;

    /**
     * @var \AppBundle\Entity\LibelleItem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LibelleItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="libelle_item_id", referencedColumnName="id")
     * })
     */
    private $libelleItem;



    /**
     * Set rang
     *
     * @param integer $rang
     *
     * @return LibelleModele
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
     * Set libelleType
     *
     * @param \AppBundle\Entity\LibelleType $libelleType
     *
     * @return LibelleModele
     */
    public function setLibelleType(\AppBundle\Entity\LibelleType $libelleType = null)
    {
        $this->libelleType = $libelleType;

        return $this;
    }

    /**
     * Get libelleType
     *
     * @return \AppBundle\Entity\LibelleType
     */
    public function getLibelleType()
    {
        return $this->libelleType;
    }

    /**
     * Set libelleItem
     *
     * @param \AppBundle\Entity\LibelleItem $libelleItem
     *
     * @return LibelleModele
     */
    public function setLibelleItem(\AppBundle\Entity\LibelleItem $libelleItem = null)
    {
        $this->libelleItem = $libelleItem;

        return $this;
    }

    /**
     * Get libelleItem
     *
     * @return \AppBundle\Entity\LibelleItem
     */
    public function getLibelleItem()
    {
        return $this->libelleItem;
    }
}
