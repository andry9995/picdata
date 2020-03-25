<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TachesItem
 *
 * @ORM\Table(name="taches_item", indexes={@ORM\Index(name="fk_taches_item_taches_idx", columns={"taches_id"}), @ORM\Index(name="fk_taches_item_client_idx", columns={"client_id"}), @ORM\Index(name="fk_taches_item_dossier_idx", columns={"dossier_id"}), @ORM\Index(name="fk_taches_item_regime_imposition_idx", columns={"regime_imposition_id"}), @ORM\Index(name="fk_taches_item_regime_tva_idx", columns={"regime_tva_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TachesItemRepository")
 */
class TachesItem
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
     * @var \AppBundle\Entity\Taches
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Taches")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taches_id", referencedColumnName="id")
     * })
     */
    private $taches;

    /**
     * @var \AppBundle\Entity\RegimeImposition
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RegimeImposition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="regime_imposition_id", referencedColumnName="id")
     * })
     */
    private $regimeImposition;

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
     * @var \AppBundle\Entity\RegimeTva
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RegimeTva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="regime_tva_id", referencedColumnName="id")
     * })
     */
    private $regimeTva;



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
     * Set taches
     *
     * @param \AppBundle\Entity\Taches $taches
     *
     * @return TachesItem
     */
    public function setTaches(\AppBundle\Entity\Taches $taches = null)
    {
        $this->taches = $taches;

        return $this;
    }

    /**
     * Get taches
     *
     * @return \AppBundle\Entity\Taches
     */
    public function getTaches()
    {
        return $this->taches;
    }

    /**
     * Set regimeImposition
     *
     * @param \AppBundle\Entity\RegimeImposition $regimeImposition
     *
     * @return TachesItem
     */
    public function setRegimeImposition(\AppBundle\Entity\RegimeImposition $regimeImposition = null)
    {
        $this->regimeImposition = $regimeImposition;

        return $this;
    }

    /**
     * Get regimeImposition
     *
     * @return \AppBundle\Entity\RegimeImposition
     */
    public function getRegimeImposition()
    {
        return $this->regimeImposition;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return TachesItem
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
     * @return TachesItem
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
     * Set regimeTva
     *
     * @param \AppBundle\Entity\RegimeTva $regimeTva
     *
     * @return TachesItem
     */
    public function setRegimeTva(\AppBundle\Entity\RegimeTva $regimeTva = null)
    {
        $this->regimeTva = $regimeTva;

        return $this;
    }

    /**
     * Get regimeTva
     *
     * @return \AppBundle\Entity\RegimeTva
     */
    public function getRegimeTva()
    {
        return $this->regimeTva;
    }
}
