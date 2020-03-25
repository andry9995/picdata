<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactCritereEcriture
 *
 * @ORM\Table(name="fact_critere_ecriture", indexes={@ORM\Index(name="fk_critere_prestation_client1_idx", columns={"fact_prestation_client_id"}), @ORM\Index(name="fk_critere_fact_critere1_idx", columns={"fact_critere_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactCritereEcritureRepository")
 */
class FactCritereEcriture
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

    /**
     * @var array
     *
     * @ORM\Column(name="value", type="simple_array", nullable=false)
     */
    private $value;

    /**
     * @var array
     *
     * @ORM\Column(name="exclure", type="simple_array", nullable=true)
     */
    private $exclure;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\FactCritere
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactCritere")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_critere_id", referencedColumnName="id")
     * })
     */
    private $factCritere;



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return FactCritereEcriture
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set factPrestationClient
     *
     * @param \AppBundle\Entity\FactPrestationClient $factPrestationClient
     *
     * @return FactCritereEcriture
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

    /**
     * Set factCritere
     *
     * @param \AppBundle\Entity\FactCritere $factCritere
     *
     * @return FactCritereEcriture
     */
    public function setFactCritere(\AppBundle\Entity\FactCritere $factCritere = null)
    {
        $this->factCritere = $factCritere;

        return $this;
    }

    /**
     * Get factCritere
     *
     * @return \AppBundle\Entity\FactCritere
     */
    public function getFactCritere()
    {
        return $this->factCritere;
    }

    /**
     * Set value
     *
     * @param array $value
     *
     * @return FactCritereEcriture
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set exclure
     *
     * @param array $exclure
     *
     * @return FactCritereEcriture
     */
    public function setExclure($exclure)
    {
        $this->exclure = $exclure;

        return $this;
    }

    /**
     * Get exclure
     *
     * @return array
     */
    public function getExclure()
    {
        return $this->exclure;
    }
}
