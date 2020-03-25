<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TvaMultipleImputationCtrl
 *
 * @ORM\Table(name="tva_multiple_imputation_ctrl", indexes={@ORM\Index(name="fk_tva_multiple_imputation_ctrl_mode_reglement1_idx", columns={"mode_reglement_id"}), @ORM\Index(name="fk_tva_multiple_imputation_ctrl_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_tva_multiple_impctrl_tauxid_idx", columns={"tva1"}), @ORM\Index(name="fk_tva_multiple_imputationctrl_taux2_idx", columns={"tva2"}), @ORM\Index(name="fk_tva_multiple_imputationctrl_taux3_idx", columns={"tva3"}), @ORM\Index(name="fk_tva_multiple_impctr_autrtax_idx", columns={"autres_taxes"}), @ORM\Index(name="fk_tva_multiple_imp_ctrl_res_pcc_idx", columns={"resultat_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imp_ctrl_tva1_pcc_idx", columns={"tva1_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imp_ctrl_tva2_pccid_idx", columns={"tva2_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imp_ctrl_tva3_pccid_idx", columns={"tva3_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imp_ctrl_autre_pccid_idx", columns={"autres_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imp_ctrl_tiersid_idx", columns={"tiers_id"}), @ORM\Index(name="fk_tva_multiple_imp_ctrl_autres_pccid_idx", columns={"autres_pcc_id"}), @ORM\Index(name="fk_tva_multiple_imp_ctrl_banque_pccid_idx", columns={"banque_pcc_id"})})
 * @ORM\Entity
 */
class TvaMultipleImputationCtrl
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
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
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
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_pcc_id", referencedColumnName="id")
     * })
     */
    private $banquePcc;

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
     *   @ORM\JoinColumn(name="tva2", referencedColumnName="id")
     * })
     */
    private $tva2;

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
     *   @ORM\JoinColumn(name="tva1", referencedColumnName="id")
     * })
     */
    private $tva1;



    /**
     * Set numeroFacture
     *
     * @param string $numeroFacture
     *
     * @return TvaMultipleImputationCtrl
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
     * @return TvaMultipleImputationCtrl
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
     * @return TvaMultipleImputationCtrl
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
     * @return TvaMultipleImputationCtrl
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
     * @return TvaMultipleImputationCtrl
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
     * @return TvaMultipleImputationCtrl
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
     * Set tiers
     *
     * @param \AppBundle\Entity\Pcc $tiers
     *
     * @return TvaMultipleImputationCtrl
     */
    public function setTiers(\AppBundle\Entity\Pcc $tiers = null)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * Get tiers
     *
     * @return \AppBundle\Entity\Pcc
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
     * @return TvaMultipleImputationCtrl
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
     * Set tva1Pcc
     *
     * @param \AppBundle\Entity\Pcc $tva1Pcc
     *
     * @return TvaMultipleImputationCtrl
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
     * @return TvaMultipleImputationCtrl
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
     * @return TvaMultipleImputationCtrl
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
     * Set banquePcc
     *
     * @param \AppBundle\Entity\Pcc $banquePcc
     *
     * @return TvaMultipleImputationCtrl
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
     * Set autresPcc
     *
     * @param \AppBundle\Entity\Pcc $autresPcc
     *
     * @return TvaMultipleImputationCtrl
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

    /**
     * Set tva3
     *
     * @param \AppBundle\Entity\TvaTaux $tva3
     *
     * @return TvaMultipleImputationCtrl
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
     * Set tva2
     *
     * @param \AppBundle\Entity\TvaTaux $tva2
     *
     * @return TvaMultipleImputationCtrl
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
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return TvaMultipleImputationCtrl
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
     * @return TvaMultipleImputationCtrl
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
     * Set autresTaxes
     *
     * @param \AppBundle\Entity\TvaTaux $autresTaxes
     *
     * @return TvaMultipleImputationCtrl
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
     * Set tva1
     *
     * @param \AppBundle\Entity\TvaTaux $tva1
     *
     * @return TvaMultipleImputationCtrl
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
}
