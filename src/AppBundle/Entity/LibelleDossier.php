<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LibelleDossier
 *
 * @ORM\Table(name="libelle_dossier", uniqueConstraints={@ORM\UniqueConstraint(name="unique", columns={"dossier_id", "libelle_item_id", "libelle_type_id"})}, indexes={@ORM\Index(name="fk_libelle_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_libelle_item1_idx", columns={"libelle_item_id"}), @ORM\Index(name="fk_libelle_type1_idx", columns={"libelle_type_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LibelleDossierRepository")
 */
class LibelleDossier
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
     * @ORM\Column(name="nb_caractere", type="integer", nullable=false)
     */
    private $nbCaractere;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

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
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;



    /**
     * Set rang
     *
     * @param integer $rang
     *
     * @return LibelleDossier
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
     * Set nbCaractere
     *
     * @param integer $nbCaractere
     *
     * @return LibelleDossier
     */
    public function setNbCaractere($nbCaractere)
    {
        $this->nbCaractere = $nbCaractere;

        return $this;
    }

    /**
     * Get nbCaractere
     *
     * @return integer
     */
    public function getNbCaractere()
    {
        return $this->nbCaractere;
    }


    /**
     * @param $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position =  $position;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
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
     * @return LibelleDossier
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
     * @return LibelleDossier
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

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return LibelleDossier
     */
    public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
    {
        $this->dossier = $dossier;

        return $this;
    }

    /**
     * Get dossier
     *
     * @return \AppBundle\Entity\Dossier
     */
    public function getDossier()
    {
        return $this->dossier;
    }
}
