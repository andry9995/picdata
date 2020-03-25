<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TvaImputation
 *
 * @ORM\Table(name="tva_imputation", indexes={@ORM\Index(name="fk_tva_saisie_pcg_dossier1_idx", columns={"pcc_id"}), @ORM\Index(name="fk_tva_saisie_tiers1_idx", columns={"tiers_id"}), @ORM\Index(name="fk_tva_saisie_pcg_dossier2_idx", columns={"pcc_tva_id"}), @ORM\Index(name="fk_tva_saisie_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_tva_imputation_analytique1_idx", columns={"analytique_id"}), @ORM\Index(name="fk_tva_imputation_sousnature1_idx", columns={"sousnature_id"}), @ORM\Index(name="fk_tva_imputation_soussouscategorie1_idx", columns={"soussouscategorie_id"}), @ORM\Index(name="fk_tva_imputation_tva_taux1_idx", columns={"tva_taux_id"}), @ORM\Index(name="fk_tva_imputation_type_vente1_idx", columns={"type_vente_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TvaImputationRepository")
 */
class TvaImputation
{
    /**
     * @var float
     *
     * @ORM\Column(name="montant_ttc", type="float", precision=10, scale=0, nullable=false)
     */
    private $montantTtc = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="montant_ht", type="float", precision=10, scale=0, nullable=false)
     */
    private $montantHt = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="taux_tva", type="float", precision=10, scale=0, nullable=false)
     */
    private $tauxTva;

    /**
     * @var string
     *
     * @ORM\Column(name="nature", type="string", length=50, nullable=true)
     */
    private $nature = '';

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=250, nullable=true)
     */
    private $libelle = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="periode_deb", type="date", nullable=true)
     */
    private $periodeDeb;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="periode_fin", type="date", nullable=true)
     */
    private $periodeFin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_livraison", type="date", nullable=true)
     */
    private $dateLivraison;

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
     *   @ORM\JoinColumn(name="pcc_id", referencedColumnName="id")
     * })
     */
    private $pcc;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_tva_id", referencedColumnName="id")
     * })
     */
    private $pccTva;

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
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;

    /**
     * @var \AppBundle\Entity\TypeVente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeVente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_vente_id", referencedColumnName="id")
     * })
     */
    private $typeVente;

    /**
     * @var \AppBundle\Entity\Sousnature
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Sousnature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sousnature_id", referencedColumnName="id")
     * })
     */
    private $sousnature;

    /**
     * @var \AppBundle\Entity\Soussouscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Soussouscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="soussouscategorie_id", referencedColumnName="id")
     * })
     */
    private $soussouscategorie;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_taux_id", referencedColumnName="id")
     * })
     */
    private $tvaTaux;

    /**
     * @var \AppBundle\Entity\Analytique
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Analytique")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="analytique_id", referencedColumnName="id")
     * })
     */
    private $analytique;


    /**
     * Set montantTtc
     *
     * @param float $montantTtc
     *
     * @return $this
     */
    public function setMontantTtc($montantTtc)
    {
        $this->montantTtc = $montantTtc;

        return $this;
    }

    /**
     * Get montantTtc
     *
     * @return float
     */
    public function getMontantTtc()
    {
        return $this->montantTtc;
    }


    /**
     * Set montantHt
     *
     * @param float $montantHt
     *
     * @return TvaImputation
     */
    public function setMontantHt($montantHt)
    {
        $this->montantHt = $montantHt;

        return $this;
    }

    /**
     * Get montantHt
     *
     * @return float
     */
    public function getMontantHt()
    {
        return $this->montantHt;
    }

    /**
     * Set tauxTva
     *
     * @param float $tauxTva
     *
     * @return TvaImputation
     */
    public function setTauxTva($tauxTva)
    {
        $this->tauxTva = $tauxTva;

        return $this;
    }

    /**
     * Get tauxTva
     *
     * @return float
     */
    public function getTauxTva()
    {
        return $this->tauxTva;
    }

    /**
     * Set nature
     *
     * @param string $nature
     *
     * @return TvaImputation
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get nature
     *
     * @return string
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return TvaImputation
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set periodeDeb
     *
     * @param \DateTime $periodeDeb
     *
     * @return TvaImputation
     */
    public function setPeriodeDeb($periodeDeb)
    {
        $this->periodeDeb = $periodeDeb;

        return $this;
    }

    /**
     * Get periodeDeb
     *
     * @return \DateTime
     */
    public function getPeriodeDeb()
    {
        return $this->periodeDeb;
    }

    /**
     * Set periodeFin
     *
     * @param \DateTime $periodeFin
     *
     * @return TvaImputation
     */
    public function setPeriodeFin($periodeFin)
    {
        $this->periodeFin = $periodeFin;

        return $this;
    }

    /**
     * Get periodeFin
     *
     * @return \DateTime
     */
    public function getPeriodeFin()
    {
        return $this->periodeFin;
    }

    /**
     * Set dateLivraison
     *
     * @param \DateTime $dateLivraison
     *
     * @return TvaImputation
     */
    public function setDateLivraison($dateLivraison)
    {
        $this->dateLivraison = $dateLivraison;

        return $this;
    }

    /**
     * Get dateLivraison
     *
     * @return \DateTime
     */
    public function getDateLivraison()
    {
        return $this->dateLivraison;
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
     * Set pcc
     *
     * @param \AppBundle\Entity\Pcc $pcc
     *
     * @return TvaImputation
     */
    public function setPcc(\AppBundle\Entity\Pcc $pcc = null)
    {
        $this->pcc = $pcc;

        return $this;
    }

    /**
     * Get pcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPcc()
    {
        return $this->pcc;
    }

    /**
     * Set pccTva
     *
     * @param \AppBundle\Entity\Pcc $pccTva
     *
     * @return TvaImputation
     */
    public function setPccTva(\AppBundle\Entity\Pcc $pccTva = null)
    {
        $this->pccTva = $pccTva;

        return $this;
    }

    /**
     * Get pccTva
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPccTva()
    {
        return $this->pccTva;
    }

    /**
     * Set tiers
     *
     * @param \AppBundle\Entity\Tiers $tiers
     *
     * @return TvaImputation
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
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return TvaImputation
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
     * Set typeVente
     *
     * @param \AppBundle\Entity\TypeVente $typeVente
     *
     * @return TvaImputation
     */
    public function setTypeVente(\AppBundle\Entity\TypeVente $typeVente = null)
    {
        $this->typeVente = $typeVente;

        return $this;
    }

    /**
     * Get typeVente
     *
     * @return \AppBundle\Entity\TypeVente
     */
    public function getTypeVente()
    {
        return $this->typeVente;
    }

    /**
     * Set sousnature
     *
     * @param \AppBundle\Entity\Sousnature $sousnature
     *
     * @return TvaImputation
     */
    public function setSousnature(\AppBundle\Entity\Sousnature $sousnature = null)
    {
        $this->sousnature = $sousnature;

        return $this;
    }

    /**
     * Get sousnature
     *
     * @return \AppBundle\Entity\Sousnature
     */
    public function getSousnature()
    {
        return $this->sousnature;
    }

    /**
     * Set soussouscategorie
     *
     * @param \AppBundle\Entity\Soussouscategorie $soussouscategorie
     *
     * @return TvaImputation
     */
    public function setSoussouscategorie(\AppBundle\Entity\Soussouscategorie $soussouscategorie = null)
    {
        $this->soussouscategorie = $soussouscategorie;

        return $this;
    }

    /**
     * Get soussouscategorie
     *
     * @return \AppBundle\Entity\Soussouscategorie
     */
    public function getSoussouscategorie()
    {
        return $this->soussouscategorie;
    }

    /**
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $tvaTaux
     *
     * @return TvaImputation
     */
    public function setTvaTaux(\AppBundle\Entity\TvaTaux $tvaTaux = null)
    {
        $this->tvaTaux = $tvaTaux;

        return $this;
    }

    /**
     * Get tvaTaux
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getTvaTaux()
    {
        return $this->tvaTaux;
    }

    /**
     * Set analytique
     *
     * @param \AppBundle\Entity\Analytique $analytique
     *
     * @return TvaImputation
     */
    public function setAnalytique(\AppBundle\Entity\Analytique $analytique = null)
    {
        $this->analytique = $analytique;

        return $this;
    }

    /**
     * Get analytique
     *
     * @return \AppBundle\Entity\Analytique
     */
    public function getAnalytique()
    {
        return $this->analytique;
    }
}
