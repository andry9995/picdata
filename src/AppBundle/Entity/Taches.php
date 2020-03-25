<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Taches
 *
 * @ORM\Table(name="taches", indexes={@ORM\Index(name="fk_taches_client_idx", columns={"client_id"}), @ORM\Index(name="fk_taches_dossier_idx", columns={"dossier_id"}), @ORM\Index(name="fk_taches_regime_fiscal_idx", columns={"regime_fiscal_id"}), @ORM\Index(name="fk_taches_taches_group_idx", columns={"taches_group_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TachesRepository")
 */
class Taches
{
    /**
     * @var integer
     *
     * @ORM\Column(name="responsable", type="integer", nullable=false)
     */
    private $responsable = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="Nom", type="string", length=45, nullable=false)
     */
    private $nom = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TachesGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TachesGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taches_group_id", referencedColumnName="id")
     * })
     */
    private $tachesGroup;

    /**
     * @var \AppBundle\Entity\RegimeFiscal
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RegimeFiscal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="regime_fiscal_id", referencedColumnName="id")
     * })
     */
    private $regimeFiscal;

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
     * Set responsable
     *
     * @param integer $responsable
     *
     * @return Taches
     */
    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return integer
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Taches
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
     * Set tachesGroup
     *
     * @param \AppBundle\Entity\TachesGroup $tachesGroup
     *
     * @return Taches
     */
    public function setTachesGroup(\AppBundle\Entity\TachesGroup $tachesGroup = null)
    {
        $this->tachesGroup = $tachesGroup;

        return $this;
    }

    /**
     * Get tachesGroup
     *
     * @return \AppBundle\Entity\TachesGroup
     */
    public function getTachesGroup()
    {
        return $this->tachesGroup;
    }

    /**
     * Set regimeFiscal
     *
     * @param \AppBundle\Entity\RegimeFiscal $regimeFiscal
     *
     * @return Taches
     */
    public function setRegimeFiscal(\AppBundle\Entity\RegimeFiscal $regimeFiscal = null)
    {
        $this->regimeFiscal = $regimeFiscal;

        return $this;
    }

    /**
     * Get regimeFiscal
     *
     * @return \AppBundle\Entity\RegimeFiscal
     */
    public function getRegimeFiscal()
    {
        return $this->regimeFiscal;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Taches
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
     * @return Taches
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
