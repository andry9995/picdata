<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TvaMultipleImputation
 *
 * @ORM\Table(name="tva_multiple_imputation", indexes={@ORM\Index(name="fk_tva_multiple_imputation_mode_reglement1_idx", columns={"mode_reglement_id"}), @ORM\Index(name="fk_tva_multiple_imputation_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_tva_multiple_imputation_tva_taux1_idx", columns={"tva1"}), @ORM\Index(name="fk_tva_multiple_imputation_tva_taux2_idx", columns={"tva2"}), @ORM\Index(name="fk_tva_multiple_imputation_tva_taux3_idx", columns={"tva3"}), @ORM\Index(name="fk_tva_multiple_imputation_tva1_pcc1", columns={"tva1_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imputation_tva2_pcc1", columns={"tva2_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imputation_tva3_pcc1", columns={"tva3_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imputation_resultat_pcc1", columns={"resultat_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imputation_autres_pcc1", columns={"autres_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imputation_tiers_pcc1", columns={"tiers_id"}), @ORM\Index(name="fk_tva_multiple_imputation_banque_pcc1", columns={"banque_pcc_id"})})
 * @ORM\Entity
 */
class TvaMultipleImputation
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
     * @var integer
     *
     * @ORM\Column(name="tva1", type="integer", nullable=true)
     */
    private $tva1;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva2", type="integer", nullable=true)
     */
    private $tva2;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva3", type="integer", nullable=true)
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
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva1_pcc_id", referencedColumnName="id")
     * })
     */
    private $tva1Pcc;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva2_pcc_id", referencedColumnName="id")
     * })
     */
    private $tva2Pcc;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva3_pcc_id", referencedColumnName="id")
     * })
     */
    private $tva3Pcc;

    /**
     * @var \AppBundle\Entity\Tiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiers_id", referencedColumnName="id")
     * })
     */
    private $tiers;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resultat_pcc_id", referencedColumnName="id")
     * })
     */
    private $resultatPcc;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_pcc_id", referencedColumnName="id")
     * })
     */
    private $banquePcc;

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
     * @var \AppBundle\Entity\ModeReglement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModeReglement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mode_reglement_id", referencedColumnName="id")
     * })
     */
    private $modeReglement;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="autres_pcc_id", referencedColumnName="id")
     * })
     */
    private $autresPcc;



    /**
     * Set numeroFacture
     *
     * @param string $numeroFacture
     *
     * @return TvaMultipleImputation
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
     * @return TvaMultipleImputation
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
     * @return TvaMultipleImputation
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
     * @return TvaMultipleImputation
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
     * @param integer $tva1
     *
     * @return TvaMultipleImputation
     */
    public function setTva1($tva1)
    {
        $this->tva1 = $tva1;

        return $this;
    }

    /**
     * Get tva1
     *
     * @return integer
     */
    public function getTva1()
    {
        return $this->tva1;
    }

    /**
     * Set tva2
     *
     * @param integer $tva2
     *
     * @return TvaMultipleImputation
     */
    public function setTva2($tva2)
    {
        $this->tva2 = $tva2;

        return $this;
    }

    /**
     * Get tva2
     *
     * @return integer
     */
    public function getTva2()
    {
        return $this->tva2;
    }

    /**
     * Set tva3
     *
     * @param integer $tva3
     *
     * @return TvaMultipleImputation
     */
    public function setTva3($tva3)
    {
        $this->tva3 = $tva3;

        return $this;
    }

    /**
     * Get tva3
     *
     * @return integer
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
     * @return TvaMultipleImputation
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
     * @return TvaMultipleImputation
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
     * @return TvaMultipleImputation
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
     * Set tva1Pcc
     *
     * @param \AppBundle\Entity\Pcc $tva1Pcc
     *
     * @return TvaMultipleImputation
     */
    public function setTva1Pcc(\AppBundle\Entity\Pcc $tva1Pcc = null)
    {
        $this->tva1Pcc = $tva1Pcc;

        return $this;
    }

    /**
     * Get tva1Pcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getTva1Pcc()
    {
        return $this->tva1Pcc;
    }

    /**
     * Set tva2Pcc
     *
     * @param \AppBundle\Entity\Pcc $tva2Pcc
     *
     * @return TvaMultipleImputation
     */
    public function setTva2Pcc(\AppBundle\Entity\Pcc $tva2Pcc = null)
    {
        $this->tva2Pcc = $tva2Pcc;

        return $this;
    }

    /**
     * Get tva2Pcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getTva2Pcc()
    {
        return $this->tva2Pcc;
    }

    /**
     * Set tva3Pcc
     *
     * @param \AppBundle\Entity\Pcc $tva3Pcc
     *
     * @return TvaMultipleImputation
     */
    public function setTva3Pcc(\AppBundle\Entity\Pcc $tva3Pcc = null)
    {
        $this->tva3Pcc = $tva3Pcc;

        return $this;
    }

    /**
     * Get tva3Pcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getTva3Pcc()
    {
        return $this->tva3Pcc;
    }

    /**
     * Set tiers
     *
     * @param \AppBundle\Entity\Tiers $tiers
     *
     * @return TvaMultipleImputation
     */
    public function setTiers(\AppBundle\Entity\Tiers $tiers = null)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * Get tiers
     *
     * @return \AppBundle\Entity\Tiers
     */
    public function getTiers()
    {
        return $this->tiers;
    }

    /**
     * Set resultatPcc
     *
     * @param \AppBundle\Entity\Pcc $resultatPcc
     *
     * @return TvaMultipleImputation
     */
    public function setResultatPcc(\AppBundle\Entity\Pcc $resultatPcc = null)
    {
        $this->resultatPcc = $resultatPcc;

        return $this;
    }

    /**
     * Get resultatPcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getResultatPcc()
    {
        return $this->resultatPcc;
    }

    /**
     * Set banquePcc
     *
     * @param \AppBundle\Entity\Pcc $banquePcc
     *
     * @return TvaMultipleImputation
     */
    public function setBanquePcc(\AppBundle\Entity\Pcc $banquePcc = null)
    {
        $this->banquePcc = $banquePcc;

        return $this;
    }

    /**
     * Get banquePcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getBanquePcc()
    {
        return $this->banquePcc;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return TvaMultipleImputation
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

    /**
     * Set modeReglement
     *
     * @param \AppBundle\Entity\ModeReglement $modeReglement
     *
     * @return TvaMultipleImputation
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
     * Set autresPcc
     *
     * @param \AppBundle\Entity\Pcc $autresPcc
     *
     * @return TvaMultipleImputation
     */
    public function setAutresPcc(\AppBundle\Entity\Pcc $autresPcc = null)
    {
        $this->autresPcc = $autresPcc;

        return $this;
    }

    /**
     * Get autresPcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getAutresPcc()
    {
        return $this->autresPcc;
    }
}
