<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImputationControleCaisse
 *
 * @ORM\Table(name="imputation_controle_caisse", indexes={@ORM\Index(name="fk_imputation_ctrcaisse_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_imputation_ctrcaisse_caisse_nature1_idx", columns={"entree_caisse_nature_id"}), @ORM\Index(name="fk_imputation_ctrcaisse_caisse_nature2_idx", columns={"sortie_caisse_nature_id"}), @ORM\Index(name="fk_imputation_ctrcaisse_tva_taux1_idx", columns={"entree_tva_taux_id"}), @ORM\Index(name="fk_imputation_ctrcaisse_tva_taux2_idx", columns={"sortie_tva_taux_id"}), @ORM\Index(name="fk_imputation_ctrcaisse_caisse_type1_idx", columns={"entree_caisse_type_id"})})
 * @ORM\Entity
 */
class ImputationControleCaisse
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=true)
     */
    private $libelle;

    /**
     * @var float
     *
     * @ORM\Column(name="solde_initial", type="float", precision=10, scale=0, nullable=true)
     */
    private $soldeInitial;

    /**
     * @var float
     *
     * @ORM\Column(name="entree_ttc", type="float", precision=10, scale=0, nullable=true)
     */
    private $entreeTtc;

    /**
     * @var float
     *
     * @ORM\Column(name="entree_tva", type="float", precision=10, scale=0, nullable=true)
     */
    private $entreeTva;

    /**
     * @var float
     *
     * @ORM\Column(name="entree_ht", type="float", precision=10, scale=0, nullable=true)
     */
    private $entreeHt;

    /**
     * @var float
     *
     * @ORM\Column(name="sortie_ttc", type="float", precision=10, scale=0, nullable=true)
     */
    private $sortieTtc;

    /**
     * @var float
     *
     * @ORM\Column(name="sortie_tva", type="float", precision=10, scale=0, nullable=true)
     */
    private $sortieTva;

    /**
     * @var float
     *
     * @ORM\Column(name="sortie_ht", type="float", precision=10, scale=0, nullable=true)
     */
    private $sortieHt;

    /**
     * @var float
     *
     * @ORM\Column(name="solde_final", type="float", precision=10, scale=0, nullable=true)
     */
    private $soldeFinal;

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
     *   @ORM\JoinColumn(name="entree_tva_taux_id", referencedColumnName="id")
     * })
     */
    private $entreeTvaTaux;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sortie_tva_taux_id", referencedColumnName="id")
     * })
     */
    private $sortieTvaTaux;

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
     * @var \AppBundle\Entity\CaisseType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CaisseType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="entree_caisse_type_id", referencedColumnName="id")
     * })
     */
    private $entreeCaisseType;

    /**
     * @var \AppBundle\Entity\CaisseNature
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CaisseNature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sortie_caisse_nature_id", referencedColumnName="id")
     * })
     */
    private $sortieCaisseNature;

    /**
     * @var \AppBundle\Entity\CaisseNature
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CaisseNature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="entree_caisse_nature_id", referencedColumnName="id")
     * })
     */
    private $entreeCaisseNature;



    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ImputationControleCaisse
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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ImputationControleCaisse
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
     * Set soldeInitial
     *
     * @param float $soldeInitial
     *
     * @return ImputationControleCaisse
     */
    public function setSoldeInitial($soldeInitial)
    {
        $this->soldeInitial = $soldeInitial;

        return $this;
    }

    /**
     * Get soldeInitial
     *
     * @return float
     */
    public function getSoldeInitial()
    {
        return $this->soldeInitial;
    }

    /**
     * Set entreeTtc
     *
     * @param float $entreeTtc
     *
     * @return ImputationControleCaisse
     */
    public function setEntreeTtc($entreeTtc)
    {
        $this->entreeTtc = $entreeTtc;

        return $this;
    }

    /**
     * Get entreeTtc
     *
     * @return float
     */
    public function getEntreeTtc()
    {
        return $this->entreeTtc;
    }

    /**
     * Set entreeTva
     *
     * @param float $entreeTva
     *
     * @return ImputationControleCaisse
     */
    public function setEntreeTva($entreeTva)
    {
        $this->entreeTva = $entreeTva;

        return $this;
    }

    /**
     * Get entreeTva
     *
     * @return float
     */
    public function getEntreeTva()
    {
        return $this->entreeTva;
    }

    /**
     * Set entreeHt
     *
     * @param float $entreeHt
     *
     * @return ImputationControleCaisse
     */
    public function setEntreeHt($entreeHt)
    {
        $this->entreeHt = $entreeHt;

        return $this;
    }

    /**
     * Get entreeHt
     *
     * @return float
     */
    public function getEntreeHt()
    {
        return $this->entreeHt;
    }

    /**
     * Set sortieTtc
     *
     * @param float $sortieTtc
     *
     * @return ImputationControleCaisse
     */
    public function setSortieTtc($sortieTtc)
    {
        $this->sortieTtc = $sortieTtc;

        return $this;
    }

    /**
     * Get sortieTtc
     *
     * @return float
     */
    public function getSortieTtc()
    {
        return $this->sortieTtc;
    }

    /**
     * Set sortieTva
     *
     * @param float $sortieTva
     *
     * @return ImputationControleCaisse
     */
    public function setSortieTva($sortieTva)
    {
        $this->sortieTva = $sortieTva;

        return $this;
    }

    /**
     * Get sortieTva
     *
     * @return float
     */
    public function getSortieTva()
    {
        return $this->sortieTva;
    }

    /**
     * Set sortieHt
     *
     * @param float $sortieHt
     *
     * @return ImputationControleCaisse
     */
    public function setSortieHt($sortieHt)
    {
        $this->sortieHt = $sortieHt;

        return $this;
    }

    /**
     * Get sortieHt
     *
     * @return float
     */
    public function getSortieHt()
    {
        return $this->sortieHt;
    }

    /**
     * Set soldeFinal
     *
     * @param float $soldeFinal
     *
     * @return ImputationControleCaisse
     */
    public function setSoldeFinal($soldeFinal)
    {
        $this->soldeFinal = $soldeFinal;

        return $this;
    }

    /**
     * Get soldeFinal
     *
     * @return float
     */
    public function getSoldeFinal()
    {
        return $this->soldeFinal;
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
     * Set entreeTvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $entreeTvaTaux
     *
     * @return ImputationControleCaisse
     */
    public function setEntreeTvaTaux(\AppBundle\Entity\TvaTaux $entreeTvaTaux = null)
    {
        $this->entreeTvaTaux = $entreeTvaTaux;

        return $this;
    }

    /**
     * Get entreeTvaTaux
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getEntreeTvaTaux()
    {
        return $this->entreeTvaTaux;
    }

    /**
     * Set sortieTvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $sortieTvaTaux
     *
     * @return ImputationControleCaisse
     */
    public function setSortieTvaTaux(\AppBundle\Entity\TvaTaux $sortieTvaTaux = null)
    {
        $this->sortieTvaTaux = $sortieTvaTaux;

        return $this;
    }

    /**
     * Get sortieTvaTaux
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getSortieTvaTaux()
    {
        return $this->sortieTvaTaux;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return ImputationControleCaisse
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
     * Set entreeCaisseType
     *
     * @param \AppBundle\Entity\CaisseType $entreeCaisseType
     *
     * @return ImputationControleCaisse
     */
    public function setEntreeCaisseType(\AppBundle\Entity\CaisseType $entreeCaisseType = null)
    {
        $this->entreeCaisseType = $entreeCaisseType;

        return $this;
    }

    /**
     * Get entreeCaisseType
     *
     * @return \AppBundle\Entity\CaisseType
     */
    public function getEntreeCaisseType()
    {
        return $this->entreeCaisseType;
    }

    /**
     * Set sortieCaisseNature
     *
     * @param \AppBundle\Entity\CaisseNature $sortieCaisseNature
     *
     * @return ImputationControleCaisse
     */
    public function setSortieCaisseNature(\AppBundle\Entity\CaisseNature $sortieCaisseNature = null)
    {
        $this->sortieCaisseNature = $sortieCaisseNature;

        return $this;
    }

    /**
     * Get sortieCaisseNature
     *
     * @return \AppBundle\Entity\CaisseNature
     */
    public function getSortieCaisseNature()
    {
        return $this->sortieCaisseNature;
    }

    /**
     * Set entreeCaisseNature
     *
     * @param \AppBundle\Entity\CaisseNature $entreeCaisseNature
     *
     * @return ImputationControleCaisse
     */
    public function setEntreeCaisseNature(\AppBundle\Entity\CaisseNature $entreeCaisseNature = null)
    {
        $this->entreeCaisseNature = $entreeCaisseNature;

        return $this;
    }

    /**
     * Get entreeCaisseNature
     *
     * @return \AppBundle\Entity\CaisseNature
     */
    public function getEntreeCaisseNature()
    {
        return $this->entreeCaisseNature;
    }
}
