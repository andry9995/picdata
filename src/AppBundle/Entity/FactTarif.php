<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactTarif
 *
 * @ORM\Table(name="fact_tarif", uniqueConstraints={@ORM\UniqueConstraint(name="prestation_annee_modele_UNIQUE", columns={"fact_prestation_id", "fact_annee_id", "fact_modele_id"})}, indexes={@ORM\Index(name="fact_tarif_annee1_idx", columns={"fact_annee_id"}), @ORM\Index(name="fact_tarif_prestation_idx", columns={"fact_prestation_id"}), @ORM\Index(name="fact_tarif_modele1_idx", columns={"fact_modele_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactTarifRepository")
 */
class FactTarif
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="show_quantite", type="boolean", nullable=false)
     */
    private $showQuantite = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="formule", type="text", length=65535, nullable=true)
     */
    private $formule;

    /**
     * @var string
     *
     * @ORM\Column(name="pu_fixe", type="decimal", precision=50, scale=4, nullable=true)
     */
    private $puFixe;

    /**
     * @var string
     *
     * @ORM\Column(name="pu_variable", type="decimal", precision=50, scale=4, nullable=true)
     */
    private $puVariable;

    /**
     * @var string
     *
     * @ORM\Column(name="pu_fixe_indice", type="decimal", precision=50, scale=4, nullable=true)
     */
    private $puFixeIndice;

    /**
     * @var string
     *
     * @ORM\Column(name="pu_variable_indice", type="decimal", precision=50, scale=4, nullable=true)
     */
    private $puVariableIndice;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\FactPrestation
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactPrestation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_prestation_id", referencedColumnName="id")
     * })
     */
    private $factPrestation;

    /**
     * @var \AppBundle\Entity\FactAnnee
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactAnnee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_annee_id", referencedColumnName="id")
     * })
     */
    private $factAnnee;

    /**
     * @var \AppBundle\Entity\FactModele
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactModele")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_modele_id", referencedColumnName="id")
     * })
     */
    private $factModele;



    /**
     * Set showQuantite
     *
     * @param boolean $showQuantite
     *
     * @return FactTarif
     */
    public function setShowQuantite($showQuantite)
    {
        $this->showQuantite = $showQuantite;

        return $this;
    }

    /**
     * Get showQuantite
     *
     * @return boolean
     */
    public function getShowQuantite()
    {
        return $this->showQuantite;
    }

    /**
     * Set formule
     *
     * @param string $formule
     *
     * @return FactTarif
     */
    public function setFormule($formule)
    {
        $this->formule = $formule;

        return $this;
    }

    /**
     * Get formule
     *
     * @return string
     */
    public function getFormule()
    {
        return $this->formule;
    }

    /**
     * Set puFixe
     *
     * @param string $puFixe
     *
     * @return FactTarif
     */
    public function setPuFixe($puFixe)
    {
        $this->puFixe = $puFixe;

        return $this;
    }

    /**
     * Get puFixe
     *
     * @return string
     */
    public function getPuFixe()
    {
        return $this->puFixe;
    }

    /**
     * Set puVariable
     *
     * @param string $puVariable
     *
     * @return FactTarif
     */
    public function setPuVariable($puVariable)
    {
        $this->puVariable = $puVariable;

        return $this;
    }

    /**
     * Get puVariable
     *
     * @return string
     */
    public function getPuVariable()
    {
        return $this->puVariable;
    }

    /**
     * Set puFixeIndice
     *
     * @param string $puFixeIndice
     *
     * @return FactTarif
     */
    public function setPuFixeIndice($puFixeIndice)
    {
        $this->puFixeIndice = $puFixeIndice;

        return $this;
    }

    /**
     * Get puFixeIndice
     *
     * @return string
     */
    public function getPuFixeIndice()
    {
        return $this->puFixeIndice;
    }

    /**
     * Set puVariableIndice
     *
     * @param string $puVariableIndice
     *
     * @return FactTarif
     */
    public function setPuVariableIndice($puVariableIndice)
    {
        $this->puVariableIndice = $puVariableIndice;

        return $this;
    }

    /**
     * Get puVariableIndice
     *
     * @return string
     */
    public function getPuVariableIndice()
    {
        return $this->puVariableIndice;
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
     * Set factPrestation
     *
     * @param \AppBundle\Entity\FactPrestation $factPrestation
     *
     * @return FactTarif
     */
    public function setFactPrestation(\AppBundle\Entity\FactPrestation $factPrestation = null)
    {
        $this->factPrestation = $factPrestation;

        return $this;
    }

    /**
     * Get factPrestation
     *
     * @return \AppBundle\Entity\FactPrestation
     */
    public function getFactPrestation()
    {
        return $this->factPrestation;
    }

    /**
     * Set factAnnee
     *
     * @param \AppBundle\Entity\FactAnnee $factAnnee
     *
     * @return FactTarif
     */
    public function setFactAnnee(\AppBundle\Entity\FactAnnee $factAnnee = null)
    {
        $this->factAnnee = $factAnnee;

        return $this;
    }

    /**
     * Get factAnnee
     *
     * @return \AppBundle\Entity\FactAnnee
     */
    public function getFactAnnee()
    {
        return $this->factAnnee;
    }

    /**
     * Set factModele
     *
     * @param \AppBundle\Entity\FactModele $factModele
     *
     * @return FactTarif
     */
    public function setFactModele(\AppBundle\Entity\FactModele $factModele = null)
    {
        $this->factModele = $factModele;

        return $this;
    }

    /**
     * Get factModele
     *
     * @return \AppBundle\Entity\FactModele
     */
    public function getFactModele()
    {
        return $this->factModele;
    }
}
