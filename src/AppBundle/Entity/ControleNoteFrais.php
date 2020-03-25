<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ControleNoteFrais
 *
 * @ORM\Table(name="controle_note_frais", indexes={@ORM\Index(name="fk_controle_note_frais_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_controle_note_frais_vehicule1_idx", columns={"vehicule_id"}), @ORM\Index(name="fk_controle_note_frais_vehicule_proprietaire1_idx", columns={"vehicule_proprietaire_id"}), @ORM\Index(name="fk_controle_note_frais_mode_paye1_idx", columns={"mode_paye_id"}), @ORM\Index(name="fk_controle_note_frais_condition_depense1_idx", columns={"condition_depense_id"}), @ORM\Index(name="fk_controle_note_frais_type_frais1_idx", columns={"type_frais_id"}), @ORM\Index(name="fk_cos_tiersid_idx", columns={"tiers_id"})})
 * @ORM\Entity
 */
class ControleNoteFrais
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="nombrekm", type="float", precision=10, scale=0, nullable=true)
     */
    private $nombrekm;

    /**
     * @var float
     *
     * @ORM\Column(name="totalIk", type="float", precision=10, scale=0, nullable=true)
     */
    private $totalik;

    /**
     * @var float
     *
     * @ORM\Column(name="ttc", type="float", precision=10, scale=0, nullable=true)
     */
    private $ttc;

    /**
     * @var float
     *
     * @ORM\Column(name="depense", type="float", precision=10, scale=0, nullable=true)
     */
    private $depense;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroPiece", type="string", length=45, nullable=true)
     */
    private $numeropiece;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=45, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="profit_de", type="string", length=45, nullable=true)
     */
    private $profitDe;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Vehicule
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Vehicule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehicule_id", referencedColumnName="id")
     * })
     */
    private $vehicule;

    /**
     * @var \AppBundle\Entity\VehiculeProprietaire
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VehiculeProprietaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehicule_proprietaire_id", referencedColumnName="id")
     * })
     */
    private $vehiculeProprietaire;

    /**
     * @var \AppBundle\Entity\TypeFrais
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeFrais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_frais_id", referencedColumnName="id")
     * })
     */
    private $typeFrais;

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
     * @var \AppBundle\Entity\ConditionDepense
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ConditionDepense")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="condition_depense_id", referencedColumnName="id")
     * })
     */
    private $conditionDepense;


    /**
     * @var \AppBundle\Entity\NoteDeFraisModePaye
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NoteDeFraisModePaye")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mode_paye_id", referencedColumnName="id")
     * })
     */
    private $modePaye;



    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ControleNoteFrais
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set nombrekm
     *
     * @param float $nombrekm
     *
     * @return ControleNoteFrais
     */
    public function setNombrekm($nombrekm)
    {
        $this->nombrekm = $nombrekm;

        return $this;
    }

    /**
     * Get nombrekm
     *
     * @return float
     */
    public function getNombrekm()
    {
        return $this->nombrekm;
    }

    /**
     * Set totalik
     *
     * @param float $totalik
     *
     * @return ControleNoteFrais
     */
    public function setTotalik($totalik)
    {
        $this->totalik = $totalik;

        return $this;
    }

    /**
     * Get totalik
     *
     * @return float
     */
    public function getTotalik()
    {
        return $this->totalik;
    }

    /**
     * Set ttc
     *
     * @param float $ttc
     *
     * @return ControleNoteFrais
     */
    public function setTtc($ttc)
    {
        $this->ttc = $ttc;

        return $this;
    }

    /**
     * Get ttc
     *
     * @return float
     */
    public function getTtc()
    {
        return $this->ttc;
    }

    /**
     * Set depense
     *
     * @param float $depense
     *
     * @return ControleNoteFrais
     */
    public function setDepense($depense)
    {
        $this->depense = $depense;

        return $this;
    }

    /**
     * Get depense
     *
     * @return float
     */
    public function getDepense()
    {
        return $this->depense;
    }

    /**
     * Set numeropiece
     *
     * @param string $numeropiece
     *
     * @return ControleNoteFrais
     */
    public function setNumeropiece($numeropiece)
    {
        $this->numeropiece = $numeropiece;

        return $this;
    }

    /**
     * Get numeropiece
     *
     * @return string
     */
    public function getNumeropiece()
    {
        return $this->numeropiece;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ControleNoteFrais
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set profitDe
     *
     * @param string $profitDe
     *
     * @return ControleNoteFrais
     */
    public function setProfitDe($profitDe)
    {
        $this->profitDe = $profitDe;

        return $this;
    }

    /**
     * Get profitDe
     *
     * @return string
     */
    public function getProfitDe()
    {
        return $this->profitDe;
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
     * Set vehicule
     *
     * @param \AppBundle\Entity\Vehicule $vehicule
     *
     * @return ControleNoteFrais
     */
    public function setVehicule(\AppBundle\Entity\Vehicule $vehicule = null)
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    /**
     * Get vehicule
     *
     * @return \AppBundle\Entity\Vehicule
     */
    public function getVehicule()
    {
        return $this->vehicule;
    }

    /**
     * Set vehiculeProprietaire
     *
     * @param \AppBundle\Entity\VehiculeProprietaire $vehiculeProprietaire
     *
     * @return ControleNoteFrais
     */
    public function setVehiculeProprietaire(\AppBundle\Entity\VehiculeProprietaire $vehiculeProprietaire = null)
    {
        $this->vehiculeProprietaire = $vehiculeProprietaire;

        return $this;
    }

    /**
     * Get vehiculeProprietaire
     *
     * @return \AppBundle\Entity\VehiculeProprietaire
     */
    public function getVehiculeProprietaire()
    {
        return $this->vehiculeProprietaire;
    }

    /**
     * Set typeFrais
     *
     * @param \AppBundle\Entity\TypeFrais $typeFrais
     *
     * @return ControleNoteFrais
     */
    public function setTypeFrais(\AppBundle\Entity\TypeFrais $typeFrais = null)
    {
        $this->typeFrais = $typeFrais;

        return $this;
    }

    /**
     * Get typeFrais
     *
     * @return \AppBundle\Entity\TypeFrais
     */
    public function getTypeFrais()
    {
        return $this->typeFrais;
    }

    /**
     * Set tiers
     *
     * @param \AppBundle\Entity\Tiers $tiers
     *
     * @return ControleNoteFrais
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
     * Set conditionDepense
     *
     * @param \AppBundle\Entity\ConditionDepense $conditionDepense
     *
     * @return ControleNoteFrais
     */
    public function setConditionDepense(\AppBundle\Entity\ConditionDepense $conditionDepense = null)
    {
        $this->conditionDepense = $conditionDepense;

        return $this;
    }

    /**
     * Get conditionDepense
     *
     * @return \AppBundle\Entity\ConditionDepense
     */
    public function getConditionDepense()
    {
        return $this->conditionDepense;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return ControleNoteFrais
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
     * Set modePaye
     *
     * @param \AppBundle\Entity\NoteDeFraisModePaye $modePaye
     *
     * @return ControleNoteFrais
     */
    public function setModePaye(\AppBundle\Entity\NoteDeFraisModePaye $modePaye = null)
    {
        $this->modePaye = $modePaye;

        return $this;
    }

    /**
     * Get modePaye
     *
     * @return \AppBundle\Entity\NoteDeFraisModePaye
     */
    public function getModePaye()
    {
        return $this->modePaye;
    }

    //===============AJOUT MANUEL================
    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image", inversedBy="controleNoteFrais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;

    //=================FIN AJOUT MANUEL============
}
