<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TvaMultipleControle
 *
 * @ORM\Table(name="tva_multiple_controle", indexes={@ORM\Index(name="fk_tva_multiple_controle_mode_reglement1_idx", columns={"mode_reglement_id"}), @ORM\Index(name="fk_tva_multiple_controle_image1_idx", columns={"image_id"})})
 * @ORM\Entity
 */
class TvaMultipleControle
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
     * @ORM\Column(name="tva1", type="float", precision=10, scale=0, nullable=true)
     */
    private $tva1;

    /**
     * @var float
     *
     * @ORM\Column(name="tva2", type="float", precision=10, scale=0, nullable=true)
     */
    private $tva2;

    /**
     * @var float
     *
     * @ORM\Column(name="tva3", type="float", precision=10, scale=0, nullable=true)
     */
    private $tva3;

    /**
     * @var float
     *
     * @ORM\Column(name="autres_taxes", type="float", precision=10, scale=0, nullable=true)
     */
    private $autresTaxes;

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
     * @return TvaMultipleControle
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
     * @return TvaMultipleControle
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
     * @return TvaMultipleControle
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
     * @return TvaMultipleControle
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
     * Set tva1
     *
     * @param float $tva1
     *
     * @return TvaMultipleControle
     */
    public function setTva1($tva1)
    {
        $this->tva1 = $tva1;

        return $this;
    }

    /**
     * Get tva1
     *
     * @return float
     */
    public function getTva1()
    {
        return $this->tva1;
    }

    /**
     * Set tva2
     *
     * @param float $tva2
     *
     * @return TvaMultipleControle
     */
    public function setTva2($tva2)
    {
        $this->tva2 = $tva2;

        return $this;
    }

    /**
     * Get tva2
     *
     * @return float
     */
    public function getTva2()
    {
        return $this->tva2;
    }

    /**
     * Set tva3
     *
     * @param float $tva3
     *
     * @return TvaMultipleControle
     */
    public function setTva3($tva3)
    {
        $this->tva3 = $tva3;

        return $this;
    }

    /**
     * Get tva3
     *
     * @return float
     */
    public function getTva3()
    {
        return $this->tva3;
    }

    /**
     * Set autresTaxes
     *
     * @param float $autresTaxes
     *
     * @return TvaMultipleControle
     */
    public function setAutresTaxes($autresTaxes)
    {
        $this->autresTaxes = $autresTaxes;

        return $this;
    }

    /**
     * Get autresTaxes
     *
     * @return float
     */
    public function getAutresTaxes()
    {
        return $this->autresTaxes;
    }

    /**
     * Set totalPaye
     *
     * @param float $totalPaye
     *
     * @return TvaMultipleControle
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
     * @return TvaMultipleControle
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
     * Set modeReglement
     *
     * @param \AppBundle\Entity\ModeReglement $modeReglement
     *
     * @return TvaMultipleControle
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
     * @return TvaMultipleControle
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
