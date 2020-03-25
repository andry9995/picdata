<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Releve
 *
 * @ORM\Table(name="releve", indexes={@ORM\Index(name="fk_releve_image1_idx", columns={"image_mere_id"}), @ORM\Index(name="fk_releve_image2_idx", columns={"image_id"}), @ORM\Index(name="fk_releve_type_tiers1_idx", columns={"type_tiers_id"}), @ORM\Index(name="fk_releve_banque_compte1_idx", columns={"banque_compte_id"}), @ORM\Index(name="fk_releve_type_compta_idx", columns={"type_compta_id"}), @ORM\Index(name="fk_releve_pccattenteid_idx", columns={"pcc_attente_id"}), @ORM\Index(name="fk_releve_source_id_idx", columns={"releve_source_id"}), @ORM\Index(name="fk_releve_criteres_id_idx", columns={"critere_id"}), @ORM\Index(name="fk_releve_image_identifiant", columns={"image_id", "identifiant_pg"}), @ORM\Index(name="fk_releve_regimetvaid_idx", columns={"regime_tva_id"}), @ORM\Index(name="fk_releve_releve1_idx", columns={"releve_id"}), @ORM\Index(name="fk_releve_tiers1_idx", columns={"compte_tiers_id_temp"}), @ORM\Index(name="fk_releve_pcc_charge_idx", columns={"compte_chg_id_temp"}), @ORM\Index(name="fk_releve_pcc_tva_idx", columns={"compte_tva_id_temp"}), @ORM\Index(name="fk_releve_image3_idx", columns={"image_id_temp"}), @ORM\Index(name="fk_releve_cle_dossier_idx", columns={"cle_dossier_id"}), @ORM\Index(name="fk_releve_image_flague_idx", columns={"image_flague_id"}), @ORM\Index(name="fk_releve_operateur1_idx", columns={"operateur_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReleveRepository")
 */
class Releve
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_releve", type="date", nullable=false)
     */
    private $dateReleve;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="text", length=65535, nullable=true)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="debit", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $debit = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="credit", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $credit = '0.00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_solde", type="date", nullable=true)
     */
    private $dateSolde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_valeur", type="date", nullable=true)
     */
    private $dateValeur;

    /**
     * @var string
     *
     * @ORM\Column(name="num_cheque", type="string", length=50, nullable=true)
     */
    private $numCheque;

    /**
     * @var string
     *
     * @ORM\Column(name="num_operation", type="string", length=50, nullable=true)
     */
    private $numOperation;

    /**
     * @var string
     *
     * @ORM\Column(name="remarque", type="text", length=65535, nullable=true)
     */
    private $remarque;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", length=65535, nullable=true)
     */
    private $commentaire;

    /**
     * @var string
     *
     * @ORM\Column(name="analytique", type="string", length=50, nullable=true)
     */
    private $analytique;

    /**
     * @var string
     *
     * @ORM\Column(name="taux_tva", type="string", length=10, nullable=true)
     */
    private $tauxTva;

    /**
     * @var string
     *
     * @ORM\Column(name="type_operation_bancaire", type="string", length=45, nullable=true)
     */
    private $typeOperationBancaire;

    /**
     * @var integer
     *
     * @ORM\Column(name="avec_detail", type="integer", nullable=true)
     */
    private $avecDetail = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="eclate", type="integer", nullable=true)
     */
    private $eclate = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="identifiant_pg", type="integer", nullable=true)
     */
    private $identifiantPg;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_releve", type="integer", nullable=true)
     */
    private $numReleve = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="num_page", type="integer", nullable=true)
     */
    private $numPage = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="maj", type="integer", nullable=false)
     */
    private $maj = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="imputation_valider", type="integer", nullable=false)
     */
    private $imputationValider = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="tva_taux", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $tvaTaux = '0.00';

    /**
     * @var integer
     *
     * @ORM\Column(name="pas_image", type="integer", nullable=false)
     */
    private $pasImage = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="pas_cle", type="integer", nullable=false)
     */
    private $pasCle = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="engagement_tresorerie", type="integer", nullable=false)
     */
    private $engagementTresorerie = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="flaguer", type="integer", nullable=false)
     */
    private $flaguer = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="a_categorise", type="integer", nullable=false)
     */
    private $aCategorise = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="migrer_postgres", type="integer", nullable=false)
     */
    private $migrerPostgres = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="ecriture_change", type="integer", nullable=false)
     */
    private $ecritureChange = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="pieces", type="string", length=255, nullable=true)
     */
    private $pieces;

    /**
     * @var string
     *
     * @ORM\Column(name="tiers", type="string", length=45, nullable=true)
     */
    private $tiers;

    /**
     * @var string
     *
     * @ORM\Column(name="non_lettrable", type="text", length=65535, nullable=false)
     */
    private $nonLettrable;

    /**
     * @var integer
     *
     * @ORM\Column(name="nature", type="integer", nullable=false)
     */
    private $nature = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Releve
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Releve")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="releve_id", referencedColumnName="id")
     * })
     */
    private $releve;

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
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_tva_id_temp", referencedColumnName="id")
     * })
     */
    private $compteTvaTemp;

    /**
     * @var \AppBundle\Entity\ReleveSource
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ReleveSource")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="releve_source_id", referencedColumnName="id")
     * })
     */
    private $releveSource;

    /**
     * @var \AppBundle\Entity\Tiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_tiers_id_temp", referencedColumnName="id")
     * })
     */
    private $compteTiersTemp;

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
     * @var \AppBundle\Entity\TypeCompta
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeCompta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_compta_id", referencedColumnName="id")
     * })
     */
    private $typeCompta;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_chg_id_temp", referencedColumnName="id")
     * })
     */
    private $compteChgTemp;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_attente_id", referencedColumnName="id")
     * })
     */
    private $pccAttente;

    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_mere_id", referencedColumnName="id")
     * })
     */
    private $imageMere;

    /**
     * @var \AppBundle\Entity\Criteres
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Criteres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="critere_id", referencedColumnName="id")
     * })
     */
    private $critere;

    /**
     * @var \AppBundle\Entity\CleDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CleDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cle_dossier_id", referencedColumnName="id")
     * })
     */
    private $cleDossier;

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
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id_temp", referencedColumnName="id")
     * })
     */
    private $imageTemp;

    /**
     * @var \AppBundle\Entity\Operateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="operateur_id", referencedColumnName="id")
     * })
     */
    private $operateur;

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
     * @var \AppBundle\Entity\BanqueCompte
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BanqueCompte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_compte_id", referencedColumnName="id")
     * })
     */
    private $banqueCompte;



    /**
     * Set dateReleve
     *
     * @param \DateTime $dateReleve
     *
     * @return Releve
     */
    public function setDateReleve($dateReleve)
    {
        $this->dateReleve = $dateReleve;

        return $this;
    }

    /**
     * Get dateReleve
     *
     * @return \DateTime
     */
    public function getDateReleve()
    {
        return $this->dateReleve;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Releve
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
     * Set debit
     *
     * @param string $debit
     *
     * @return Releve
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;

        return $this;
    }

    /**
     * Get debit
     *
     * @return string
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * Set credit
     *
     * @param string $credit
     *
     * @return Releve
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return string
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set dateSolde
     *
     * @param \DateTime $dateSolde
     *
     * @return Releve
     */
    public function setDateSolde($dateSolde)
    {
        $this->dateSolde = $dateSolde;

        return $this;
    }

    /**
     * Get dateSolde
     *
     * @return \DateTime
     */
    public function getDateSolde()
    {
        return $this->dateSolde;
    }

    /**
     * Set dateValeur
     *
     * @param \DateTime $dateValeur
     *
     * @return Releve
     */
    public function setDateValeur($dateValeur)
    {
        $this->dateValeur = $dateValeur;

        return $this;
    }

    /**
     * Get dateValeur
     *
     * @return \DateTime
     */
    public function getDateValeur()
    {
        return $this->dateValeur;
    }

    /**
     * Set numCheque
     *
     * @param string $numCheque
     *
     * @return Releve
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
     * Set numOperation
     *
     * @param string $numOperation
     *
     * @return Releve
     */
    public function setNumOperation($numOperation)
    {
        $this->numOperation = $numOperation;

        return $this;
    }

    /**
     * Get numOperation
     *
     * @return string
     */
    public function getNumOperation()
    {
        return $this->numOperation;
    }

    /**
     * Set remarque
     *
     * @param string $remarque
     *
     * @return Releve
     */
    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;

        return $this;
    }

    /**
     * Get remarque
     *
     * @return string
     */
    public function getRemarque()
    {
        return $this->remarque;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Releve
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set analytique
     *
     * @param string $analytique
     *
     * @return Releve
     */
    public function setAnalytique($analytique)
    {
        $this->analytique = $analytique;

        return $this;
    }

    /**
     * Get analytique
     *
     * @return string
     */
    public function getAnalytique()
    {
        return $this->analytique;
    }

    /**
     * Set tauxTva
     *
     * @param string $tauxTva
     *
     * @return Releve
     */
    public function setTauxTva($tauxTva)
    {
        $this->tauxTva = $tauxTva;

        return $this;
    }

    /**
     * Get tauxTva
     *
     * @return string
     */
    public function getTauxTva()
    {
        return $this->tauxTva;
    }

    /**
     * Set typeOperationBancaire
     *
     * @param string $typeOperationBancaire
     *
     * @return Releve
     */
    public function setTypeOperationBancaire($typeOperationBancaire)
    {
        $this->typeOperationBancaire = $typeOperationBancaire;

        return $this;
    }

    /**
     * Get typeOperationBancaire
     *
     * @return string
     */
    public function getTypeOperationBancaire()
    {
        return $this->typeOperationBancaire;
    }

    /**
     * Set avecDetail
     *
     * @param integer $avecDetail
     *
     * @return Releve
     */
    public function setAvecDetail($avecDetail)
    {
        $this->avecDetail = $avecDetail;

        return $this;
    }

    /**
     * Get avecDetail
     *
     * @return integer
     */
    public function getAvecDetail()
    {
        return $this->avecDetail;
    }

    /**
     * Set eclate
     *
     * @param integer $eclate
     *
     * @return Releve
     */
    public function setEclate($eclate)
    {
        $this->eclate = $eclate;

        return $this;
    }

    /**
     * Get eclate
     *
     * @return integer
     */
    public function getEclate()
    {
        return $this->eclate;
    }

    /**
     * Set identifiantPg
     *
     * @param integer $identifiantPg
     *
     * @return Releve
     */
    public function setIdentifiantPg($identifiantPg)
    {
        $this->identifiantPg = $identifiantPg;

        return $this;
    }

    /**
     * Get identifiantPg
     *
     * @return integer
     */
    public function getIdentifiantPg()
    {
        return $this->identifiantPg;
    }

    /**
     * Set numReleve
     *
     * @param integer $numReleve
     *
     * @return Releve
     */
    public function setNumReleve($numReleve)
    {
        $this->numReleve = $numReleve;

        return $this;
    }

    /**
     * Get numReleve
     *
     * @return integer
     */
    public function getNumReleve()
    {
        return $this->numReleve;
    }

    /**
     * Set numPage
     *
     * @param integer $numPage
     *
     * @return Releve
     */
    public function setNumPage($numPage)
    {
        $this->numPage = $numPage;

        return $this;
    }

    /**
     * Get numPage
     *
     * @return integer
     */
    public function getNumPage()
    {
        return $this->numPage;
    }

    /**
     * Set maj
     *
     * @param integer $maj
     *
     * @return Releve
     */
    public function setMaj($maj)
    {
        $this->maj = $maj;

        return $this;
    }

    /**
     * Get maj
     *
     * @return integer
     */
    public function getMaj()
    {
        return $this->maj;
    }

    /**
     * Set imputationValider
     *
     * @param integer $imputationValider
     *
     * @return Releve
     */
    public function setImputationValider($imputationValider)
    {
        $this->imputationValider = $imputationValider;

        return $this;
    }

    /**
     * Get imputationValider
     *
     * @return integer
     */
    public function getImputationValider()
    {
        return $this->imputationValider;
    }

    /**
     * Set tvaTaux
     *
     * @param string $tvaTaux
     *
     * @return Releve
     */
    public function setTvaTaux($tvaTaux)
    {
        $this->tvaTaux = $tvaTaux;

        return $this;
    }

    /**
     * Get tvaTaux
     *
     * @return string
     */
    public function getTvaTaux()
    {
        return $this->tvaTaux;
    }

    /**
     * Set pasImage
     *
     * @param integer $pasImage
     *
     * @return Releve
     */
    public function setPasImage($pasImage)
    {
        $this->pasImage = $pasImage;

        return $this;
    }

    /**
     * Get pasImage
     *
     * @return integer
     */
    public function getPasImage()
    {
        return $this->pasImage;
    }

    /**
     * Set pasCle
     *
     * @param integer $pasCle
     *
     * @return Releve
     */
    public function setPasCle($pasCle)
    {
        $this->pasCle = $pasCle;

        return $this;
    }

    /**
     * Get pasCle
     *
     * @return integer
     */
    public function getPasCle()
    {
        return $this->pasCle;
    }

    /**
     * Set engagementTresorerie
     *
     * @param integer $engagementTresorerie
     *
     * @return Releve
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
     * Set flaguer
     *
     * @param integer $flaguer
     *
     * @return Releve
     */
    public function setFlaguer($flaguer)
    {
        $this->flaguer = $flaguer;

        return $this;
    }

    /**
     * Get flaguer
     *
     * @return integer
     */
    public function getFlaguer()
    {
        return $this->flaguer;
    }

    /**
     * Set aCategorise
     *
     * @param integer $aCategorise
     *
     * @return Releve
     */
    public function setACategorise($aCategorise)
    {
        $this->aCategorise = $aCategorise;

        return $this;
    }

    /**
     * Get aCategorise
     *
     * @return integer
     */
    public function getACategorise()
    {
        return $this->aCategorise;
    }

    /**
     * Set migrerPostgres
     *
     * @param integer $migrerPostgres
     *
     * @return Releve
     */
    public function setMigrerPostgres($migrerPostgres)
    {
        $this->migrerPostgres = $migrerPostgres;

        return $this;
    }

    /**
     * Get migrerPostgres
     *
     * @return integer
     */
    public function getMigrerPostgres()
    {
        return $this->migrerPostgres;
    }

    /**
     * Set ecritureChange
     *
     * @param integer $ecritureChange
     *
     * @return Releve
     */
    public function setEcritureChange($ecritureChange)
    {
        $this->ecritureChange = $ecritureChange;

        return $this;
    }

    /**
     * Get ecritureChange
     *
     * @return integer
     */
    public function getEcritureChange()
    {
        return $this->ecritureChange;
    }

    /**
     * Set pieces
     *
     * @param string $pieces
     *
     * @return Releve
     */
    public function setPieces($pieces)
    {
        $this->pieces = $pieces;

        return $this;
    }

    /**
     * Get pieces
     *
     * @return string
     */
    public function getPieces()
    {
        return $this->pieces;
    }

    /**
     * Set tiers
     *
     * @param string $tiers
     *
     * @return Releve
     */
    public function setTiers($tiers)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * Get tiers
     *
     * @return string
     */
    public function getTiers()
    {
        return $this->tiers;
    }

    /**
     * Set nonLettrable
     *
     * @param string $nonLettrable
     *
     * @return Releve
     */
    public function setNonLettrable($nonLettrable)
    {
        $this->nonLettrable = $nonLettrable;

        return $this;
    }

    /**
     * Get nonLettrable
     *
     * @return string
     */
    public function getNonLettrable()
    {
        return $this->nonLettrable;
    }

    /**
     * Set nature
     *
     * @param integer $nature
     *
     * @return Releve
     */
    public function setNature($nature)
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Get nature
     *
     * @return integer
     */
    public function getNature()
    {
        return $this->nature;
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
     * Set releve
     *
     * @param \AppBundle\Entity\Releve $releve
     *
     * @return Releve
     */
    public function setReleve(\AppBundle\Entity\Releve $releve = null)
    {
        $this->releve = $releve;

        return $this;
    }

    /**
     * Get releve
     *
     * @return \AppBundle\Entity\Releve
     */
    public function getReleve()
    {
        return $this->releve;
    }

    /**
     * Set regimeTva
     *
     * @param \AppBundle\Entity\RegimeTva $regimeTva
     *
     * @return Releve
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

    /**
     * Set compteTvaTemp
     *
     * @param \AppBundle\Entity\Pcc $compteTvaTemp
     *
     * @return Releve
     */
    public function setCompteTvaTemp(\AppBundle\Entity\Pcc $compteTvaTemp = null)
    {
        $this->compteTvaTemp = $compteTvaTemp;

        return $this;
    }

    /**
     * Get compteTvaTemp
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getCompteTvaTemp()
    {
        return $this->compteTvaTemp;
    }

    /**
     * Set releveSource
     *
     * @param \AppBundle\Entity\ReleveSource $releveSource
     *
     * @return Releve
     */
    public function setReleveSource(\AppBundle\Entity\ReleveSource $releveSource = null)
    {
        $this->releveSource = $releveSource;

        return $this;
    }

    /**
     * Get releveSource
     *
     * @return \AppBundle\Entity\ReleveSource
     */
    public function getReleveSource()
    {
        return $this->releveSource;
    }

    /**
     * Set compteTiersTemp
     *
     * @param \AppBundle\Entity\Tiers $compteTiersTemp
     *
     * @return Releve
     */
    public function setCompteTiersTemp(\AppBundle\Entity\Tiers $compteTiersTemp = null)
    {
        $this->compteTiersTemp = $compteTiersTemp;

        return $this;
    }

    /**
     * Get compteTiersTemp
     *
     * @return \AppBundle\Entity\Tiers
     */
    public function getCompteTiersTemp()
    {
        return $this->compteTiersTemp;
    }

    /**
     * Set typeTiers
     *
     * @param \AppBundle\Entity\TypeTiers $typeTiers
     *
     * @return Releve
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
     * Set typeCompta
     *
     * @param \AppBundle\Entity\TypeCompta $typeCompta
     *
     * @return Releve
     */
    public function setTypeCompta(\AppBundle\Entity\TypeCompta $typeCompta = null)
    {
        $this->typeCompta = $typeCompta;

        return $this;
    }

    /**
     * Get typeCompta
     *
     * @return \AppBundle\Entity\TypeCompta
     */
    public function getTypeCompta()
    {
        return $this->typeCompta;
    }

    /**
     * Set compteChgTemp
     *
     * @param \AppBundle\Entity\Pcc $compteChgTemp
     *
     * @return Releve
     */
    public function setCompteChgTemp(\AppBundle\Entity\Pcc $compteChgTemp = null)
    {
        $this->compteChgTemp = $compteChgTemp;

        return $this;
    }

    /**
     * Get compteChgTemp
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getCompteChgTemp()
    {
        return $this->compteChgTemp;
    }

    /**
     * Set pccAttente
     *
     * @param \AppBundle\Entity\Pcc $pccAttente
     *
     * @return Releve
     */
    public function setPccAttente(\AppBundle\Entity\Pcc $pccAttente = null)
    {
        $this->pccAttente = $pccAttente;

        return $this;
    }

    /**
     * Get pccAttente
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPccAttente()
    {
        return $this->pccAttente;
    }

    /**
     * Set imageMere
     *
     * @param \AppBundle\Entity\Image $imageMere
     *
     * @return Releve
     */
    public function setImageMere(\AppBundle\Entity\Image $imageMere = null)
    {
        $this->imageMere = $imageMere;

        return $this;
    }

    /**
     * Get imageMere
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImageMere()
    {
        return $this->imageMere;
    }

    /**
     * Set critere
     *
     * @param \AppBundle\Entity\Criteres $critere
     *
     * @return Releve
     */
    public function setCritere(\AppBundle\Entity\Criteres $critere = null)
    {
        $this->critere = $critere;

        return $this;
    }

    /**
     * Get critere
     *
     * @return \AppBundle\Entity\Criteres
     */
    public function getCritere()
    {
        return $this->critere;
    }

    /**
     * Set cleDossier
     *
     * @param \AppBundle\Entity\CleDossier $cleDossier
     *
     * @return Releve
     */
    public function setCleDossier(\AppBundle\Entity\CleDossier $cleDossier = null)
    {
        $this->cleDossier = $cleDossier;

        return $this;
    }

    /**
     * Get cleDossier
     *
     * @return \AppBundle\Entity\CleDossier
     */
    public function getCleDossier()
    {
        return $this->cleDossier;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Releve
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
     * Set imageTemp
     *
     * @param \AppBundle\Entity\Image $imageTemp
     *
     * @return Releve
     */
    public function setImageTemp(\AppBundle\Entity\Image $imageTemp = null)
    {
        $this->imageTemp = $imageTemp;

        return $this;
    }

    /**
     * Get imageTemp
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImageTemp()
    {
        return $this->imageTemp;
    }

    /**
     * Set operateur
     *
     * @param \AppBundle\Entity\Operateur $operateur
     *
     * @return Releve
     */
    public function setOperateur(\AppBundle\Entity\Operateur $operateur = null)
    {
        $this->operateur = $operateur;

        return $this;
    }

    /**
     * Get operateur
     *
     * @return \AppBundle\Entity\Operateur
     */
    public function getOperateur()
    {
        return $this->operateur;
    }

    /**
     * Set imageFlague
     *
     * @param \AppBundle\Entity\ImageFlague $imageFlague
     *
     * @return Releve
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
     * Set banqueCompte
     *
     * @param \AppBundle\Entity\BanqueCompte $banqueCompte
     *
     * @return Releve
     */
    public function setBanqueCompte(\AppBundle\Entity\BanqueCompte $banqueCompte = null)
    {
        $this->banqueCompte = $banqueCompte;

        return $this;
    }

    /**
     * Get banqueCompte
     *
     * @return \AppBundle\Entity\BanqueCompte
     */
    public function getBanqueCompte()
    {
        return $this->banqueCompte;
    }
}
