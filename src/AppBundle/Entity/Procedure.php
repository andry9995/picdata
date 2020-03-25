<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Procedure
 *
 * @ORM\Table(name="procedure", indexes={@ORM\Index(name="fk_procedure_precedent_idx", columns={"precedent"}), @ORM\Index(name="fk_procedure_suivant_idx", columns={"suivant"}), @ORM\Index(name="fk_procedure_poste_id_idx", columns={"poste_id"}), @ORM\Index(name="fk_procedure_unite_comptage_idx", columns={"unite_comptage_id"})})
 * @ORM\Entity
 */
class Procedure
{
    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=20, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="duree", type="float", precision=10, scale=0, nullable=true)
     */
    private $duree = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Procedure
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Procedure")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="suivant", referencedColumnName="id")
     * })
     */
    private $suivant;

    /**
     * @var \AppBundle\Entity\Procedure
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Procedure")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="precedent", referencedColumnName="id")
     * })
     */
    private $precedent;

    /**
     * @var \AppBundle\Entity\Poste
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Poste")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="poste_id", referencedColumnName="id")
     * })
     */
    private $poste;

    /**
     * @var \AppBundle\Entity\UniteComptage
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UniteComptage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unite_comptage_id", referencedColumnName="id")
     * })
     */
    private $uniteComptage;



    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return Procedure
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Procedure
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
     * @return Procedure
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
     * Set duree
     *
     * @param float $duree
     *
     * @return Procedure
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return float
     */
    public function getDuree()
    {
        return $this->duree;
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
     * Set suivant
     *
     * @param \AppBundle\Entity\Procedure $suivant
     *
     * @return Procedure
     */
    public function setSuivant(\AppBundle\Entity\Procedure $suivant = null)
    {
        $this->suivant = $suivant;

        return $this;
    }

    /**
     * Get suivant
     *
     * @return \AppBundle\Entity\Procedure
     */
    public function getSuivant()
    {
        return $this->suivant;
    }

    /**
     * Set precedent
     *
     * @param \AppBundle\Entity\Procedure $precedent
     *
     * @return Procedure
     */
    public function setPrecedent(\AppBundle\Entity\Procedure $precedent = null)
    {
        $this->precedent = $precedent;

        return $this;
    }

    /**
     * Get precedent
     *
     * @return \AppBundle\Entity\Procedure
     */
    public function getPrecedent()
    {
        return $this->precedent;
    }

    /**
     * Set poste
     *
     * @param \AppBundle\Entity\Poste $poste
     *
     * @return Procedure
     */
    public function setPoste(\AppBundle\Entity\Poste $poste = null)
    {
        $this->poste = $poste;

        return $this;
    }

    /**
     * Get poste
     *
     * @return \AppBundle\Entity\Poste
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * Set uniteComptage
     *
     * @param \AppBundle\Entity\UniteComptage $uniteComptage
     *
     * @return Procedure
     */
    public function setUniteComptage(\AppBundle\Entity\UniteComptage $uniteComptage = null)
    {
        $this->uniteComptage = $uniteComptage;

        return $this;
    }

    /**
     * Get uniteComptage
     *
     * @return \AppBundle\Entity\UniteComptage
     */
    public function getUniteComptage()
    {
        return $this->uniteComptage;
    }
}
