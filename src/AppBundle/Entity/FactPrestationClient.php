<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactPrestationClient
 *
 * @ORM\Table(name="fact_prestation_client", indexes={@ORM\Index(name="fk_prestation_cabinet_client1_idx", columns={"client_id"}), @ORM\Index(name="fk_prestation_cabinet_fact_prestation_idx", columns={"fact_prestation_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactPrestationClientRepository")
 */
class FactPrestationClient
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="indice", type="boolean", nullable=false)
     */
    private $indice = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="remise", type="boolean", nullable=false)
     */
    private $remise = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status = '1';

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
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;



    /**
     * Set indice
     *
     * @param boolean $indice
     *
     * @return FactPrestationClient
     */
    public function setIndice($indice)
    {
        $this->indice = $indice;

        return $this;
    }

    /**
     * Get indice
     *
     * @return boolean
     */
    public function getIndice()
    {
        return $this->indice;
    }

    /**
     * Set remise
     *
     * @param boolean $remise
     *
     * @return FactPrestationClient
     */
    public function setRemise($remise)
    {
        $this->remise = $remise;

        return $this;
    }

    /**
     * Get remise
     *
     * @return boolean
     */
    public function getRemise()
    {
        return $this->remise;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return FactPrestationClient
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
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
     * @return FactPrestationClient
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return FactPrestationClient
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
}
