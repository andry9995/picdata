<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurSpecGroup
 *
 * @ORM\Table(name="indicateur_spec_group", indexes={@ORM\Index(name="fk_indicateur_spec_group_indicateur_group1_idx", columns={"indicateur_group_id"}), @ORM\Index(name="fk_indicateur_spec_group_client1_idx", columns={"client_id"}), @ORM\Index(name="fk_indicateur_spec_group_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurSpecGroupRepository")
 */
class IndicateurSpecGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;

    /**
     * @var \AppBundle\Entity\IndicateurGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IndicateurGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicateur_group_id", referencedColumnName="id")
     * })
     */
    private $indicateurGroup;



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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return IndicateurSpecGroup
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return IndicateurSpecGroup
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
     * Set indicateurGroup
     *
     * @param \AppBundle\Entity\IndicateurGroup $indicateurGroup
     *
     * @return IndicateurSpecGroup
     */
    public function setIndicateurGroup(\AppBundle\Entity\IndicateurGroup $indicateurGroup = null)
    {
        $this->indicateurGroup = $indicateurGroup;

        return $this;
    }

    /**
     * Get indicateurGroup
     *
     * @return \AppBundle\Entity\IndicateurGroup
     */
    public function getIndicateurGroup()
    {
        return $this->indicateurGroup;
    }
}
