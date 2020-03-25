<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Poste
 *
 * @ORM\Table(name="poste", indexes={@ORM\Index(name="fk_poste_cellule1_idx", columns={"cellule_id"}), @ORM\Index(name="fk_poste_operateur1_idx", columns={"operateur_id"})})
 * @ORM\Entity
 */
class Poste
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=true)
     */
    private $dateCreation;

    /**
     * @var integer
     *
     * @ORM\Column(name="supprimer", type="integer", nullable=false)
     */
    private $supprimer = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Operateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="operateur_id", referencedColumnName="id")
     * })
     */
    private $operateur;

    /**
     * @var \AppBundle\Entity\Cellule
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cellule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cellule_id", referencedColumnName="id")
     * })
     */
    private $cellule;



    /**
     * Set code
     *
     * @param string $code
     *
     * @return Poste
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Poste
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Poste
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Poste
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set supprimer
     *
     * @param integer $supprimer
     *
     * @return Poste
     */
    public function setSupprimer($supprimer)
    {
        $this->supprimer = $supprimer;

        return $this;
    }

    /**
     * Get supprimer
     *
     * @return integer
     */
    public function getSupprimer()
    {
        return $this->supprimer;
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
     * Set operateur
     *
     * @param \AppBundle\Entity\Operateur $operateur
     *
     * @return Poste
     */
    public function setOperateur(\AppBundle\Entity\Operateur $operateur = null)
    {
        $this->operateur = $operateur;

        return $this;
    }

    /**
     * Get operateur
     *
     * @return \AppBundle\Entity\Operateur
     */
    public function getOperateur()
    {
        return $this->operateur;
    }

    /**
     * Set cellule
     *
     * @param \AppBundle\Entity\Cellule $cellule
     *
     * @return Poste
     */
    public function setCellule(\AppBundle\Entity\Cellule $cellule = null)
    {
        $this->cellule = $cellule;

        return $this;
    }

    /**
     * Get cellule
     *
     * @return \AppBundle\Entity\Cellule
     */
    public function getCellule()
    {
        return $this->cellule;
    }
}
