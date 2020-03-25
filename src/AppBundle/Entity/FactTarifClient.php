<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactTarifClient
 *
 * @ORM\Table(name="fact_tarif_client", uniqueConstraints={@ORM\UniqueConstraint(name="client_prestation_annee_modele_UNIQUE", columns={"client_id", "fact_prestation_client_id", "fact_annee_id", "fact_modele_id"})}, indexes={@ORM\Index(name="fact_tarif_client1_idx", columns={"client_id"}), @ORM\Index(name="fact_tarif_annee1_idx", columns={"fact_annee_id"}), @ORM\Index(name="fact_tarif_modele1_idx", columns={"fact_modele_id"}), @ORM\Index(name="fact_tarif_client_prestation1_idx", columns={"fact_prestation_client_id"}), @ORM\Index(name="fact_tarif_client_annee1_idx", columns={"fact_annee_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactTarifClientRepository")
 */
class FactTarifClient
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="show_quantite", type="boolean", nullable=false)
     */
    private $showQuantite = false;

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
     * @var \AppBundle\Entity\FactAnnee
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactAnnee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_annee_id", referencedColumnName="id")
     * })
     */
    private $factAnnee;

    /**
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;

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
     * @var \AppBundle\Entity\FactPrestationClient
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactPrestationClient")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_prestation_client_id", referencedColumnName="id")
     * })
     */
    private $factPrestationClient;



    /**
     * Set showQuantite
     *
     * @param boolean $showQuantite
     *
     * @return FactTarifClient
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
     * @return FactTarifClient
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
     * @return FactTarifClient
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
     * @return FactTarifClient
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
     * @return FactTarifClient
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
     * @return FactTarifClient
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
     * Set factAnnee
     *
     * @param \AppBundle\Entity\FactAnnee $factAnnee
     *
     * @return FactTarifClient
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return FactTarifClient
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set factModele
     *
     * @param \AppBundle\Entity\FactModele $factModele
     *
     * @return FactTarifClient
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

    /**
     * Set factPrestationClient
     *
     * @param \AppBundle\Entity\FactPrestationClient $factPrestationClient
     *
     * @return FactTarifClient
     */
    public function setFactPrestationClient(\AppBundle\Entity\FactPrestationClient $factPrestationClient = null)
    {
        $this->factPrestationClient = $factPrestationClient;

        return $this;
    }

    /**
     * Get factPrestationClient
     *
     * @return \AppBundle\Entity\FactPrestationClient
     */
    public function getFactPrestationClient()
    {
        return $this->factPrestationClient;
    }
}
