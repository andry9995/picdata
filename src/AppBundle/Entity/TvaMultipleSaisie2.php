<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TvaMultipleSaisie2
 *
 * @ORM\Table(name="tva_multiple_saisie2", indexes={@ORM\Index(name="fk_tva_multiple_saisie2_mode_reglement1_idx", columns={"mode_reglement_id"}), @ORM\Index(name="fk_tva_multiple_saisie2_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_tva_multiple_saisie2_tva_taux1_idx", columns={"tva1"}), @ORM\Index(name="fk_tva_multiple_saisie2_tva_taux2_idx", columns={"tva2"}), @ORM\Index(name="fk_tva_multiple_saisie2_tva_taux3_idx", columns={"tva3"}), @ORM\Index(name="fk_tva_multiple_saisie2_tva_taux4_idx", columns={"autres_taxes"})})
 * @ORM\Entity
 */
class TvaMultipleSaisie2
{
    /**
     * @var string
     *
     * @ORM\Column(name="numero_facture", type="string", length=100, nullable=true)
     */
    private $numeroFacture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_facture", type="date", nullable=true)
     */
    private $dateFacture;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100, nullable=true)
     */
    private $nom;

    /**
     * @var float
     *
     * @ORM\Column(name="total_ht", type="float", precision=10, scale=0, nullable=true)
     */
    private $totalHt;

    /**
     * @var float
     *
     * @ORM\Column(name="total_paye", type="float", precision=10, scale=0, nullable=true)
     */
    private $totalPaye;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=100, nullable=true)
     */
    private $numero;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva3", referencedColumnName="id")
     * })
     */
    private $tva3;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="autres_taxes", referencedColumnName="id")
     * })
     */
    private $autresTaxes;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva2", referencedColumnName="id")
     * })
     */
    private $tva2;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva1", referencedColumnName="id")
     * })
     */
    private $tva1;

    /**
     * @var \AppBundle\Entity\ModeReglement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModeReglement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mode_reglement_id", referencedColumnName="id")
     * })
     */
    private $modeReglement;

    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;



    /**
     * Set numeroFacture
     *
     * @param string $numeroFacture
     *
     * @return TvaMultipleSaisie2
     */
    public function setNumeroFacture($numeroFacture)
    {
        $this->numeroFacture = $numeroFacture;

        return $this;
    }

    /**
     * Get numeroFacture
     *
     * @return string
     */
    public function getNumeroFacture()
    {
        return $this->numeroFacture;
    }

    /**
     * Set dateFacture
     *
     * @param \DateTime $dateFacture
     *
     * @return TvaMultipleSaisie2
     */
    public function setDateFacture($dateFacture)
    {
        $this->dateFacture = $dateFacture;

        return $this;
    }

    /**
     * Get dateFacture
     *
     * @return \DateTime
     */
    public function getDateFacture()
    {
        return $this->dateFacture;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return TvaMultipleSaisie2
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
     * Set totalHt
     *
     * @param float $totalHt
     *
     * @return TvaMultipleSaisie2
     */
    public function setTotalHt($totalHt)
    {
        $this->totalHt = $totalHt;

        return $this;
    }

    /**
     * Get totalHt
     *
     * @return float
     */
    public function getTotalHt()
    {
        return $this->totalHt;
    }

    /**
     * Set totalPaye
     *
     * @param float $totalPaye
     *
     * @return TvaMultipleSaisie2
     */
    public function setTotalPaye($totalPaye)
    {
        $this->totalPaye = $totalPaye;

        return $this;
    }

    /**
     * Get totalPaye
     *
     * @return float
     */
    public function getTotalPaye()
    {
        return $this->totalPaye;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return TvaMultipleSaisie2
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
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
     * Set tva3
     *
     * @param \AppBundle\Entity\TvaTaux $tva3
     *
     * @return TvaMultipleSaisie2
     */
    public function setTva3(\AppBundle\Entity\TvaTaux $tva3 = null)
    {
        $this->tva3 = $tva3;

        return $this;
    }

    /**
     * Get tva3
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getTva3()
    {
        return $this->tva3;
    }

    /**
     * Set autresTaxes
     *
     * @param \AppBundle\Entity\TvaTaux $autresTaxes
     *
     * @return TvaMultipleSaisie2
     */
    public function setAutresTaxes(\AppBundle\Entity\TvaTaux $autresTaxes = null)
    {
        $this->autresTaxes = $autresTaxes;

        return $this;
    }

    /**
     * Get autresTaxes
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getAutresTaxes()
    {
        return $this->autresTaxes;
    }

    /**
     * Set tva2
     *
     * @param \AppBundle\Entity\TvaTaux $tva2
     *
     * @return TvaMultipleSaisie2
     */
    public function setTva2(\AppBundle\Entity\TvaTaux $tva2 = null)
    {
        $this->tva2 = $tva2;

        return $this;
    }

    /**
     * Get tva2
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getTva2()
    {
        return $this->tva2;
    }

    /**
     * Set tva1
     *
     * @param \AppBundle\Entity\TvaTaux $tva1
     *
     * @return TvaMultipleSaisie2
     */
    public function setTva1(\AppBundle\Entity\TvaTaux $tva1 = null)
    {
        $this->tva1 = $tva1;

        return $this;
    }

    /**
     * Get tva1
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getTva1()
    {
        return $this->tva1;
    }

    /**
     * Set modeReglement
     *
     * @param \AppBundle\Entity\ModeReglement $modeReglement
     *
     * @return TvaMultipleSaisie2
     */
    public function setModeReglement(\AppBundle\Entity\ModeReglement $modeReglement = null)
    {
        $this->modeReglement = $modeReglement;

        return $this;
    }

    /**
     * Get modeReglement
     *
     * @return \AppBundle\Entity\ModeReglement
     */
    public function getModeReglement()
    {
        return $this->modeReglement;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return TvaMultipleSaisie2
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
