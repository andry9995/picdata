<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactPrestationDossier
 *
 * @ORM\Table(name="fact_prestation_dossier", indexes={@ORM\Index(name="fk_prestation_dossier_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_prestation_dossier_fact_prestation1_idx", columns={"fact_prestation_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactPrestationDossierRepository")
 */
class FactPrestationDossier
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
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;



    /**
     * Set indice
     *
     * @param boolean $indice
     *
     * @return FactPrestationDossier
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
     * @return FactPrestationDossier
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
     * @return FactPrestationDossier
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
     * @return FactPrestationDossier
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return FactPrestationDossier
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
