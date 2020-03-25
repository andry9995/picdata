<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrestationParam
 *
 * @ORM\Table(name="prestation_param", indexes={@ORM\Index(name="fk_prestation_param_fact_prestation_client_id_idx", columns={"fact_prestation_client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrestationParamRepository")
 */
class PrestationParam
{
    /**
     * @var string
     *
     * @ORM\Column(name="journal_ids", type="string", length=255, nullable=true)
     */
    private $journalIds;

    /**
     * @var string
     *
     * @ORM\Column(name="source_image_ids", type="string", length=255, nullable=true)
     */
    private $sourceImageIds;

    /**
     * @var string
     *
     * @ORM\Column(name="mot_clef", type="string", length=255, nullable=true)
     */
    private $motClef;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="unite", type="integer")
     */
    private $unite;

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
     * Set journalIds
     *
     * @param string $journalIds
     *
     * @return PrestationParam
     */
    public function setJournalIds($journalIds)
    {
        $this->journalIds = $journalIds;

        return $this;
    }

    /**
     * Get journalIds
     *
     * @return string
     */
    public function getJournalIds()
    {
        return $this->journalIds;
    }

    /**
     * Set sourceImageIds
     *
     * @param string $sourceImageIds
     *
     * @return PrestationParam
     */
    public function setSourceImageIds($sourceImageIds)
    {
        $this->sourceImageIds = $sourceImageIds;

        return $this;
    }

    /**
     * Get sourceImageIds
     *
     * @return string
     */
    public function getSourceImageIds()
    {
        return $this->sourceImageIds;
    }

    /**
     * Set motClef
     *
     * @param string $motClef
     *
     * @return PrestationParam
     */
    public function setMotClef($motClef)
    {
        $this->motClef = $motClef;

        return $this;
    }

    /**
     * Get motClef
     *
     * @return string
     */
    public function getMotClef()
    {
        return $this->motClef;
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
     * @return PrestationParam
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
     * Set unite
     *
     * @param integer $unite
     *
     * @return PrestationParam
     */
    public function setUnite($unite)
    {
        $this->unite = $unite;

        return $this;
    }

    /**
     * Get unite
     *
     * @return integer
     */
    public function getUnite()
    {
        return $this->unite;
    }
}