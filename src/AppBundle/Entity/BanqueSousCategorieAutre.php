<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BanqueSousCategorieAutre
 *
 * @ORM\Table(name="banque_sous_categorie_autre", indexes={@ORM\Index(name="fk_bsca_compte_tiersid_idx", columns={"compte_tiers_id"}), @ORM\Index(name="fk_bsca_compte_chgid_idx", columns={"compte_chg_id"}), @ORM\Index(name="fk_bsca_compte_tva_idx", columns={"compte_tva_id"}), @ORM\Index(name="fk_bsca_typetiersid_idx", columns={"type_tiers_id"}), @ORM\Index(name="fk_bsca_tvataux_id_idx", columns={"tva_taux_id"}), @ORM\Index(name="fk_bsca_imageid_idx", columns={"image_id"}), @ORM\Index(name="fk_bsca_souscategid_idx", columns={"sous_categorie_id"}), @ORM\Index(name="fk_bsca_oidpostgres", columns={"oid_postgres"}), @ORM\Index(name="fk_bsca_image_flague_id_idx", columns={"image_flague_id"}), @ORM\Index(name="fk_banque_sous_categorie_autre_soussouscategorie1_idx", columns={"soussouscategorie_id"}), @ORM\Index(name="fk_bsca_compte_bilanid_idx", columns={"compte_bilan_id"}), @ORM\Index(name="fk_bsca_banque_type1_idx", columns={"banque_type_id"}), @ORM\Index(name="fk_bsca_image_flague_2_idx", columns={"image_flague_2_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BanqueSousCategorieAutreRepository")
 */
class BanqueSousCategorieAutre
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=150, nullable=true)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="num_facture", type="string", length=45, nullable=true)
     */
    private $numFacture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_facture", type="date", nullable=true)
     */
    private $dateFacture;

    /**
     * @var string
     *
     * @ORM\Column(name="num_remise", type="string", length=45, nullable=true)
     */
    private $numRemise;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float", precision=10, scale=0, nullable=true)
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="oid_postgres", type="string", length=45, nullable=true)
     */
    private $oidPostgres;

    /**
     * @var string
     *
     * @ORM\Column(name="ordre", type="string", length=45, nullable=true)
     */
    private $ordre;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_tiers", type="string", length=45, nullable=true)
     */
    private $nomTiers;

    /**
     * @var float
     *
     * @ORM\Column(name="montant_ht", type="float", precision=10, scale=0, nullable=true)
     */
    private $montantHt;

    /**
     * @var float
     *
     * @ORM\Column(name="montant_tva", type="float", precision=10, scale=0, nullable=true)
     */
    private $montantTva;

    /**
     * @var string
     *
     * @ORM\Column(name="num_cheque", type="string", length=45, nullable=true)
     */
    private $numCheque;

    /**
     * @var string
     *
     * @ORM\Column(name="num_virement", type="string", length=45, nullable=true)
     */
    private $numVirement;

    /**
     * @var string
     *
     * @ORM\Column(name="num_cb", type="string", length=45, nullable=true)
     */
    private $numCb;

    /**
     * @var integer
     *
     * @ORM\Column(name="sens", type="integer", nullable=false)
     */
    private $sens = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=45, nullable=true)
     */
    private $codePostal;

    /**
     * @var integer
     *
     * @ORM\Column(name="engagement_tresorerie", type="integer", nullable=false)
     */
    private $engagementTresorerie = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\ImageFlague
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ImageFlague")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_flague_id", referencedColumnName="id")
     * })
     */
    private $imageFlague;

    /**
     * @var \AppBundle\Entity\ImageFlague
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ImageFlague")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_flague_2_id", referencedColumnName="id")
     * })
     */
    private $imageFlague2;

    /**
     * @var \AppBundle\Entity\Souscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Souscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sous_categorie_id", referencedColumnName="id")
     * })
     */
    private $sousCategorie;

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
     * @var \AppBundle\Entity\TypeTiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeTiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_tiers_id", referencedColumnName="id")
     * })
     */
    private $typeTiers;

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
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_tva_id", referencedColumnName="id")
     * })
     */
    private $compteTva;

    /**
     * @var \AppBundle\Entity\BanqueType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BanqueType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_type_id", referencedColumnName="id")
     * })
     */
    private $banqueType;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_bilan_id", referencedColumnName="id")
     * })
     */
    private $compteBilan;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_chg_id", referencedColumnName="id")
     * })
     */
    private $compteChg;

    /**
     * @var \AppBundle\Entity\Tiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_tiers_id", referencedColumnName="id")
     * })
     */
    private $compteTiers;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return BanqueSousCategorieAutre
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
     * @return BanqueSousCategorieAutre
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
     * Set numFacture
     *
     * @param string $numFacture
     *
     * @return BanqueSousCategorieAutre
     */
    public function setNumFacture($numFacture)
    {
        $this->numFacture = $numFacture;

        return $this;
    }

    /**
     * Get numFacture
     *
     * @return string
     */
    public function getNumFacture()
    {
        return $this->numFacture;
    }

    /**
     * Set dateFacture
     *
     * @param \DateTime $dateFacture
     *
     * @return BanqueSousCategorieAutre
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
     * Set numRemise
     *
     * @param string $numRemise
     *
     * @return BanqueSousCategorieAutre
     */
    public function setNumRemise($numRemise)
    {
        $this->numRemise = $numRemise;

        return $this;
    }

    /**
     * Get numRemise
     *
     * @return string
     */
    public function getNumRemise()
    {
        return $this->numRemise;
    }

    /**
     * Set montant
     *
     * @param float $montant
     *
     * @return BanqueSousCategorieAutre
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set oidPostgres
     *
     * @param string $oidPostgres
     *
     * @return BanqueSousCategorieAutre
     */
    public function setOidPostgres($oidPostgres)
    {
        $this->oidPostgres = $oidPostgres;

        return $this;
    }

    /**
     * Get oidPostgres
     *
     * @return string
     */
    public function getOidPostgres()
    {
        return $this->oidPostgres;
    }

    /**
     * Set ordre
     *
     * @param string $ordre
     *
     * @return BanqueSousCategorieAutre
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return string
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set nomTiers
     *
     * @param string $nomTiers
     *
     * @return BanqueSousCategorieAutre
     */
    public function setNomTiers($nomTiers)
    {
        $this->nomTiers = $nomTiers;

        return $this;
    }

    /**
     * Get nomTiers
     *
     * @return string
     */
    public function getNomTiers()
    {
        return $this->nomTiers;
    }

    /**
     * Set montantHt
     *
     * @param float $montantHt
     *
     * @return BanqueSousCategorieAutre
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
     * Set montantTva
     *
     * @param float $montantTva
     *
     * @return BanqueSousCategorieAutre
     */
    public function setMontantTva($montantTva)
    {
        $this->montantTva = $montantTva;

        return $this;
    }

    /**
     * Get montantTva
     *
     * @return float
     */
    public function getMontantTva()
    {
        return $this->montantTva;
    }

    /**
     * Set numCheque
     *
     * @param string $numCheque
     *
     * @return BanqueSousCategorieAutre
     */
    public function setNumCheque($numCheque)
    {
        $this->numCheque = $numCheque;

        return $this;
    }

    /**
     * Get numCheque
     *
     * @return string
     */
    public function getNumCheque()
    {
        return $this->numCheque;
    }

    /**
     * Set numVirement
     *
     * @param string $numVirement
     *
     * @return BanqueSousCategorieAutre
     */
    public function setNumVirement($numVirement)
    {
        $this->numVirement = $numVirement;

        return $this;
    }

    /**
     * Get numVirement
     *
     * @return string
     */
    public function getNumVirement()
    {
        return $this->numVirement;
    }

    /**
     * Set numCb
     *
     * @param string $numCb
     *
     * @return BanqueSousCategorieAutre
     */
    public function setNumCb($numCb)
    {
        $this->numCb = $numCb;

        return $this;
    }

    /**
     * Get numCb
     *
     * @return string
     */
    public function getNumCb()
    {
        return $this->numCb;
    }

    /**
     * Set sens
     *
     * @param integer $sens
     *
     * @return BanqueSousCategorieAutre
     */
    public function setSens($sens)
    {
        $this->sens = $sens;

        return $this;
    }

    /**
     * Get sens
     *
     * @return integer
     */
    public function getSens()
    {
        return $this->sens;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return BanqueSousCategorieAutre
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set engagementTresorerie
     *
     * @param integer $engagementTresorerie
     *
     * @return BanqueSousCategorieAutre
     */
    public function setEngagementTresorerie($engagementTresorerie)
    {
        $this->engagementTresorerie = $engagementTresorerie;

        return $this;
    }

    /**
     * Get engagementTresorerie
     *
     * @return integer
     */
    public function getEngagementTresorerie()
    {
        return $this->engagementTresorerie;
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
     * Set imageFlague
     *
     * @param \AppBundle\Entity\ImageFlague $imageFlague
     *
     * @return BanqueSousCategorieAutre
     */
    public function setImageFlague(\AppBundle\Entity\ImageFlague $imageFlague = null)
    {
        $this->imageFlague = $imageFlague;

        return $this;
    }

    /**
     * Get imageFlague
     *
     * @return \AppBundle\Entity\ImageFlague
     */
    public function getImageFlague()
    {
        return $this->imageFlague;
    }

    /**
     * Set imageFlague2
     *
     * @param \AppBundle\Entity\ImageFlague $imageFlague2
     *
     * @return BanqueSousCategorieAutre
     */
    public function setImageFlague2(\AppBundle\Entity\ImageFlague $imageFlague2 = null)
    {
        $this->imageFlague2 = $imageFlague2;

        return $this;
    }

    /**
     * Get imageFlague2
     *
     * @return \AppBundle\Entity\ImageFlague
     */
    public function getImageFlague2()
    {
        return $this->imageFlague2;
    }

    /**
     * Set sousCategorie
     *
     * @param \AppBundle\Entity\Souscategorie $sousCategorie
     *
     * @return BanqueSousCategorieAutre
     */
    public function setSousCategorie(\AppBundle\Entity\Souscategorie $sousCategorie = null)
    {
        $this->sousCategorie = $sousCategorie;

        return $this;
    }

    /**
     * Get sousCategorie
     *
     * @return \AppBundle\Entity\Souscategorie
     */
    public function getSousCategorie()
    {
        return $this->sousCategorie;
    }

    /**
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $tvaTaux
     *
     * @return BanqueSousCategorieAutre
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
     * Set typeTiers
     *
     * @param \AppBundle\Entity\TypeTiers $typeTiers
     *
     * @return BanqueSousCategorieAutre
     */
    public function setTypeTiers(\AppBundle\Entity\TypeTiers $typeTiers = null)
    {
        $this->typeTiers = $typeTiers;

        return $this;
    }

    /**
     * Get typeTiers
     *
     * @return \AppBundle\Entity\TypeTiers
     */
    public function getTypeTiers()
    {
        return $this->typeTiers;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return BanqueSousCategorieAutre
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
     * Set compteTva
     *
     * @param \AppBundle\Entity\Pcc $compteTva
     *
     * @return BanqueSousCategorieAutre
     */
    public function setCompteTva(\AppBundle\Entity\Pcc $compteTva = null)
    {
        $this->compteTva = $compteTva;

        return $this;
    }

    /**
     * Get compteTva
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getCompteTva()
    {
        return $this->compteTva;
    }

    /**
     * Set banqueType
     *
     * @param \AppBundle\Entity\BanqueType $banqueType
     *
     * @return BanqueSousCategorieAutre
     */
    public function setBanqueType(\AppBundle\Entity\BanqueType $banqueType = null)
    {
        $this->banqueType = $banqueType;

        return $this;
    }

    /**
     * Get banqueType
     *
     * @return \AppBundle\Entity\BanqueType
     */
    public function getBanqueType()
    {
        return $this->banqueType;
    }

    /**
     * Set compteBilan
     *
     * @param \AppBundle\Entity\Pcc $compteBilan
     *
     * @return BanqueSousCategorieAutre
     */
    public function setCompteBilan(\AppBundle\Entity\Pcc $compteBilan = null)
    {
        $this->compteBilan = $compteBilan;

        return $this;
    }

    /**
     * Get compteBilan
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getCompteBilan()
    {
        return $this->compteBilan;
    }

    /**
     * Set compteChg
     *
     * @param \AppBundle\Entity\Pcc $compteChg
     *
     * @return BanqueSousCategorieAutre
     */
    public function setCompteChg(\AppBundle\Entity\Pcc $compteChg = null)
    {
        $this->compteChg = $compteChg;

        return $this;
    }

    /**
     * Get compteChg
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getCompteChg()
    {
        return $this->compteChg;
    }

    /**
     * Set compteTiers
     *
     * @param \AppBundle\Entity\Tiers $compteTiers
     *
     * @return BanqueSousCategorieAutre
     */
    public function setCompteTiers(\AppBundle\Entity\Tiers $compteTiers = null)
    {
        $this->compteTiers = $compteTiers;

        return $this;
    }

    /**
     * Get compteTiers
     *
     * @return \AppBundle\Entity\Tiers
     */
    public function getCompteTiers()
    {
        return $this->compteTiers;
    }

    /**
     * Set soussouscategorie
     *
     * @param \AppBundle\Entity\Soussouscategorie $soussouscategorie
     *
     * @return BanqueSousCategorieAutre
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
}
