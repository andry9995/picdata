<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactTarifDossier
 *
 * @ORM\Table(name="fact_tarif_dossier", uniqueConstraints={@ORM\UniqueConstraint(name="dossier_prestation_annee_modele_UNIQUE", columns={"dossier_id", "fact_prestation_dossier_id", "fact_annee_id", "fact_modele_id"})}, indexes={@ORM\Index(name="fact_tarif_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fact_tarif_dossier_prestation1_idx", columns={"fact_prestation_dossier_id"}), @ORM\Index(name="fact_tarif_dossier_annee_idx", columns={"fact_annee_id"}), @ORM\Index(name="fact_tarif_dossier_modele1_idx", columns={"fact_modele_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactTarifDossierRepository")
 */
class FactTarifDossier
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
     * @ORM\Column(name="pu_variable_indice", type="decimal", precision=50, scale=4, nullable=true)
     */
    private $puVariableIndice;

    /**
     * @var string
     *
     * @ORM\Column(name="pu_fixe_indice", type="decimal", precision=50, scale=4, nullable=true)
     */
    private $puFixeIndice;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\FactPrestationDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactPrestationDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_prestation_dossier_id", referencedColumnName="id")
     * })
     */
    private $factPrestationDossier;

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
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;

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
     * @return FactTarifDossier
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
     * @return FactTarifDossier
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
     * @return FactTarifDossier
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
     * @return FactTarifDossier
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
     * Set puVariableIndice
     *
     * @param string $puVariableIndice
     *
     * @return FactTarifDossier
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
     * Set puFixeIndice
     *
     * @param string $puFixeIndice
     *
     * @return FactTarifDossier
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set factPrestationDossier
     *
     * @param \AppBundle\Entity\FactPrestationDossier $factPrestationDossier
     *
     * @return FactTarifDossier
     */
    public function setFactPrestationDossier(\AppBundle\Entity\FactPrestationDossier $factPrestationDossier = null)
    {
        $this->factPrestationDossier = $factPrestationDossier;

        return $this;
    }

    /**
     * Get factPrestationDossier
     *
     * @return \AppBundle\Entity\FactPrestationDossier
     */
    public function getFactPrestationDossier()
    {
        return $this->factPrestationDossier;
    }

    /**
     * Set factAnnee
     *
     * @param \AppBundle\Entity\FactAnnee $factAnnee
     *
     * @return FactTarifDossier
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return FactTarifDossier
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

    /**
     * Set factModele
     *
     * @param \AppBundle\Entity\FactModele $factModele
     *
     * @return FactTarifDossier
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
