<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LibelleItemType
 *
 * @ORM\Table(name="libelle_item_type", indexes={@ORM\Index(name="libelle_item1_idx", columns={"libelle_item_id"}), @ORM\Index(name="libelle_type1_idx", columns={"libelle_type_id"})})
 * @ORM\Entity
 */
class LibelleItemType
{
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
     * @return LibelleItemType
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
     * @return LibelleItemType
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
