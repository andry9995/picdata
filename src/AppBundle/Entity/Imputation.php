<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Imputation
 *
 * @ORM\Table(name="imputation", indexes={@ORM\Index(name="fk_saisie_fournisseur_journal_dossier1_idx", columns={"journal_dossier_id"}), @ORM\Index(name="fk_saisie_fournisseur_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_saisie_fournisseur_type_piece1_idx", columns={"type_piece_id"}), @ORM\Index(name="fk_saisie_fournisseur_type_achat_vente1_idx", columns={"type_achat_vente_id"}), @ORM\Index(name="fk_saisie_fournisseur_devise1_idx", columns={"devise_id"}), @ORM\Index(name="fk_saisie_fournisseur_mode_reglement1_idx", columns={"mode_reglement_id"}), @ORM\Index(name="fk_imputation_soussouscategorie1_idx", columns={"soussouscategorie_id"}), @ORM\Index(name="fk_imputation_pays1_idx", columns={"pays_id"}), @ORM\Index(name="fk_imputat_orgid_idx", columns={"organisme_id"}), @ORM\Index(name="fk_imputation_cerfa1_idx", columns={"cerfa_id"}), @ORM\Index(name="fk_imputation_sousnature1_idx", columns={"sousnature_id"}), @ORM\Index(name="fk_imputation_banquecompteid_idx", columns={"banque_compte_id"}), @ORM\Index(name="fk_imputation_souscategid_idx", columns={"souscategorie_id"})})
 * @ORM\Entity
 */
class Imputation
{
    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=45, nullable=true)
     */
    private $codePostal;


    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_couvert", type="integer", nullable=true)
     */
    private $nbreCouvert;



    /**
     * @var \AppBundle\Entity\MentionManuscrite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MentionManuscrite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mention_manuscrite_id", referencedColumnName="id")
     * })
     */
    private $mentionManuscrite;


    /**
     * @var string
     *
     * @ORM\Column(name="rs", type="string", length=150, nullable=true)
     */
    private $rs = '';

    /**
     * @var string
     *
     * @ORM\Column(name="abrev_rs", type="string", length=50, nullable=true)
     */
    private $abrevRs = '';

    /**
     * @var string
     *
     * @ORM\Column(name="num_client", type="string", length=30, nullable=true)
     */
    private $numClient = '';

    /**
     * @var string
     *
     * @ORM\Column(name="siret", type="string", length=20, nullable=true)
     */
    private $siret = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_livraison", type="date", nullable=true)
     */
    private $dateLivraison;

    /**
     * @var string
     *
     * @ORM\Column(name="nature1", type="string", length=50, nullable=true)
     */
    private $nature1 = '';

    /**
     * @var string
     *
     * @ORM\Column(name="nature2", type="string", length=50, nullable=true)
     */
    private $nature2 = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="periode_d1", type="date", nullable=true)
     */
    private $periodeD1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="periode_f1", type="date", nullable=true)
     */
    private $periodeF1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="periode_d2", type="date", nullable=true)
     */
    private $periodeD2;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="periode_f2", type="date", nullable=true)
     */
    private $periodeF2;

    /**
     * @var float
     *
     * @ORM\Column(name="taux_devise", type="float", precision=10, scale=0, nullable=true)
     */
    private $tauxDevise = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="num_bl", type="string", length=50, nullable=true)
     */
    private $numBl = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_cmd", type="date", nullable=true)
     */
    private $dateCmd;

    /**
     * @var string
     *
     * @ORM\Column(name="num_commande", type="string", length=50, nullable=true)
     */
    private $numCommande = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_facture", type="date", nullable=true)
     */
    private $dateFacture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut_periode", type="date", nullable=true)
     */
    private $debutPeriode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin_periode", type="date", nullable=true)
     */
    private $finPeriode;

    /**
     * @var string
     *
     * @ORM\Column(name="num_facture", type="string", length=50, nullable=true)
     */
    private $numFacture = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="page", type="integer", nullable=true)
     */
    private $page;

    /**
     * @var string
     *
     * @ORM\Column(name="num_paiement", type="string", length=50, nullable=true)
     */
    private $numPaiement = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_echeance", type="date", nullable=true)
     */
    private $dateEcheance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reglement", type="date", nullable=true)
     */
    private $dateReglement;

    /**
     * @var string
     *
     * @ORM\Column(name="zone_a", type="string", length=150, nullable=true)
     */
    private $zoneA = '';

    /**
     * @var string
     *
     * @ORM\Column(name="zone_b", type="string", length=150, nullable=true)
     */
    private $zoneB = '';

    /**
     * @var string
     *
     * @ORM\Column(name="zone_c", type="string", length=150, nullable=true)
     */
    private $zoneC = '';

    /**
     * @var string
     *
     * @ORM\Column(name="zone_d", type="string", length=150, nullable=true)
     */
    private $zoneD = '';

    /**
     * @var float
     *
     * @ORM\Column(name="escompte", type="float", precision=10, scale=0, nullable=true)
     */
    private $escompte;

    /**
     * @var float
     *
     * @ORM\Column(name="solde_debut", type="float", precision=10, scale=0, nullable=true)
     */
    private $soldeDebut = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="solde_fin", type="float", precision=10, scale=0, nullable=true)
     */
    private $soldeFin = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="chrono", type="string", length=50, nullable=true)
     */
    private $chrono;

    /**
     * @var integer
     *
     * @ORM\Column(name="pnc", type="integer", nullable=true)
     */
    private $pnc;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_caisse", type="integer", nullable=true)
     */
    private $typeCaisse;

    /**
     * @var string
     *
     * @ORM\Column(name="code_tiers", type="string", length=50, nullable=true)
     */
    private $codeTiers;

    /**
     * @var string
     *
     * @ORM\Column(name="montant_paye", type="string", length=45, nullable=true)
     */
    private $montantPaye;

    /**
     * @var string
     *
     * @ORM\Column(name="compte_pcc", type="string", length=20, nullable=true)
     */
    private $comptePcc;

    /**
     * @var float
     *
     * @ORM\Column(name="taux_intracomm", type="float", precision=10, scale=0, nullable=true)
     */
    private $tauxIntracomm;

    /**
     * @var integer
     *
     * @ORM\Column(name="avec_intracom", type="integer", nullable=true)
     */
    private $avecIntracom = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\JournalDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JournalDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="journal_dossier_id", referencedColumnName="id")
     * })
     */
    private $journalDossier;

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
     * @var \AppBundle\Entity\TypeAchatVente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeAchatVente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_achat_vente_id", referencedColumnName="id")
     * })
     */
    private $typeAchatVente;

    /**
     * @var \AppBundle\Entity\TypePiece
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypePiece")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_piece_id", referencedColumnName="id")
     * })
     */
    private $typePiece;

    /**
     * @var \AppBundle\Entity\Devise
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Devise")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="devise_id", referencedColumnName="id")
     * })
     */
    private $devise;

    /**
     * @var \AppBundle\Entity\Organisme
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organisme")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisme_id", referencedColumnName="id")
     * })
     */
    private $organisme;

    /**
     * @var \AppBundle\Entity\Cerfa
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cerfa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cerfa_id", referencedColumnName="id")
     * })
     */
    private $cerfa;

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
     * @var \AppBundle\Entity\Pays
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pays")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pays_id", referencedColumnName="id")
     * })
     */
    private $pays;

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
     * @var \AppBundle\Entity\Souscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Souscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="souscategorie_id", referencedColumnName="id")
     * })
     */
    private $souscategorie;



    /**
     * Set rs
     *
     * @param string $rs
     *
     * @return Imputation
     */
    public function setRs($rs)
    {
        $this->rs = $rs;

        return $this;
    }

    /**
     * Get rs
     *
     * @return string
     */
    public function getRs()
    {
        return $this->rs;
    }

    /**
     * Set abrevRs
     *
     * @param string $abrevRs
     *
     * @return Imputation
     */
    public function setAbrevRs($abrevRs)
    {
        $this->abrevRs = $abrevRs;

        return $this;
    }

    /**
     * Get abrevRs
     *
     * @return string
     */
    public function getAbrevRs()
    {
        return $this->abrevRs;
    }

    /**
     * Set numClient
     *
     * @param string $numClient
     *
     * @return Imputation
     */
    public function setNumClient($numClient)
    {
        $this->numClient = $numClient;

        return $this;
    }

    /**
     * Get numClient
     *
     * @return string
     */
    public function getNumClient()
    {
        return $this->numClient;
    }

    /**
     * Set siret
     *
     * @param string $siret
     *
     * @return Imputation
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return string
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set dateLivraison
     *
     * @param \DateTime $dateLivraison
     *
     * @return Imputation
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
     * Set nature1
     *
     * @param string $nature1
     *
     * @return Imputation
     */
    public function setNature1($nature1)
    {
        $this->nature1 = $nature1;

        return $this;
    }

    /**
     * Get nature1
     *
     * @return string
     */
    public function getNature1()
    {
        return $this->nature1;
    }

    /**
     * Set nature2
     *
     * @param string $nature2
     *
     * @return Imputation
     */
    public function setNature2($nature2)
    {
        $this->nature2 = $nature2;

        return $this;
    }

    /**
     * Get nature2
     *
     * @return string
     */
    public function getNature2()
    {
        return $this->nature2;
    }

    /**
     * Set periodeD1
     *
     * @param \DateTime $periodeD1
     *
     * @return Imputation
     */
    public function setPeriodeD1($periodeD1)
    {
        $this->periodeD1 = $periodeD1;

        return $this;
    }

    /**
     * Get periodeD1
     *
     * @return \DateTime
     */
    public function getPeriodeD1()
    {
        return $this->periodeD1;
    }

    /**
     * Set periodeF1
     *
     * @param \DateTime $periodeF1
     *
     * @return Imputation
     */
    public function setPeriodeF1($periodeF1)
    {
        $this->periodeF1 = $periodeF1;

        return $this;
    }

    /**
     * Get periodeF1
     *
     * @return \DateTime
     */
    public function getPeriodeF1()
    {
        return $this->periodeF1;
    }

    /**
     * Set periodeD2
     *
     * @param \DateTime $periodeD2
     *
     * @return Imputation
     */
    public function setPeriodeD2($periodeD2)
    {
        $this->periodeD2 = $periodeD2;

        return $this;
    }

    /**
     * Get periodeD2
     *
     * @return \DateTime
     */
    public function getPeriodeD2()
    {
        return $this->periodeD2;
    }

    /**
     * Set periodeF2
     *
     * @param \DateTime $periodeF2
     *
     * @return Imputation
     */
    public function setPeriodeF2($periodeF2)
    {
        $this->periodeF2 = $periodeF2;

        return $this;
    }

    /**
     * Get periodeF2
     *
     * @return \DateTime
     */
    public function getPeriodeF2()
    {
        return $this->periodeF2;
    }

    /**
     * Set tauxDevise
     *
     * @param float $tauxDevise
     *
     * @return Imputation
     */
    public function setTauxDevise($tauxDevise)
    {
        $this->tauxDevise = $tauxDevise;

        return $this;
    }

    /**
     * Get tauxDevise
     *
     * @return float
     */
    public function getTauxDevise()
    {
        return $this->tauxDevise;
    }

    /**
     * Set numBl
     *
     * @param string $numBl
     *
     * @return Imputation
     */
    public function setNumBl($numBl)
    {
        $this->numBl = $numBl;

        return $this;
    }

    /**
     * Get numBl
     *
     * @return string
     */
    public function getNumBl()
    {
        return $this->numBl;
    }

    /**
     * Set dateCmd
     *
     * @param \DateTime $dateCmd
     *
     * @return Imputation
     */
    public function setDateCmd($dateCmd)
    {
        $this->dateCmd = $dateCmd;

        return $this;
    }

    /**
     * Get dateCmd
     *
     * @return \DateTime
     */
    public function getDateCmd()
    {
        return $this->dateCmd;
    }

    /**
     * Set numCommande
     *
     * @param string $numCommande
     *
     * @return Imputation
     */
    public function setNumCommande($numCommande)
    {
        $this->numCommande = $numCommande;

        return $this;
    }

    /**
     * Get numCommande
     *
     * @return string
     */
    public function getNumCommande()
    {
        return $this->numCommande;
    }

    /**
     * Set dateFacture
     *
     * @param \DateTime $dateFacture
     *
     * @return Imputation
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
     * Set debutPeriode
     *
     * @param \DateTime $debutPeriode
     *
     * @return Imputation
     */
    public function setDebutPeriode($debutPeriode)
    {
        $this->debutPeriode = $debutPeriode;

        return $this;
    }

    /**
     * Get debutPeriode
     *
     * @return \DateTime
     */
    public function getDebutPeriode()
    {
        return $this->debutPeriode;
    }

    /**
     * Set finPeriode
     *
     * @param \DateTime $finPeriode
     *
     * @return Imputation
     */
    public function setFinPeriode($finPeriode)
    {
        $this->finPeriode = $finPeriode;

        return $this;
    }

    /**
     * Get finPeriode
     *
     * @return \DateTime
     */
    public function getFinPeriode()
    {
        return $this->finPeriode;
    }

    /**
     * Set numFacture
     *
     * @param string $numFacture
     *
     * @return Imputation
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
     * Set page
     *
     * @param integer $page
     *
     * @return Imputation
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set numPaiement
     *
     * @param string $numPaiement
     *
     * @return Imputation
     */
    public function setNumPaiement($numPaiement)
    {
        $this->numPaiement = $numPaiement;

        return $this;
    }

    /**
     * Get numPaiement
     *
     * @return string
     */
    public function getNumPaiement()
    {
        return $this->numPaiement;
    }

    /**
     * Set dateEcheance
     *
     * @param \DateTime $dateEcheance
     *
     * @return Imputation
     */
    public function setDateEcheance($dateEcheance)
    {
        $this->dateEcheance = $dateEcheance;

        return $this;
    }

    /**
     * Get dateEcheance
     *
     * @return \DateTime
     */
    public function getDateEcheance()
    {
        return $this->dateEcheance;
    }

    /**
     * Set dateReglement
     *
     * @param \DateTime $dateReglement
     *
     * @return Imputation
     */
    public function setDateReglement($dateReglement)
    {
        $this->dateReglement = $dateReglement;

        return $this;
    }

    /**
     * Get dateReglement
     *
     * @return \DateTime
     */
    public function getDateReglement()
    {
        return $this->dateReglement;
    }

    /**
     * Set zoneA
     *
     * @param string $zoneA
     *
     * @return Imputation
     */
    public function setZoneA($zoneA)
    {
        $this->zoneA = $zoneA;

        return $this;
    }

    /**
     * Get zoneA
     *
     * @return string
     */
    public function getZoneA()
    {
        return $this->zoneA;
    }

    /**
     * Set zoneB
     *
     * @param string $zoneB
     *
     * @return Imputation
     */
    public function setZoneB($zoneB)
    {
        $this->zoneB = $zoneB;

        return $this;
    }

    /**
     * Get zoneB
     *
     * @return string
     */
    public function getZoneB()
    {
        return $this->zoneB;
    }

    /**
     * Set zoneC
     *
     * @param string $zoneC
     *
     * @return Imputation
     */
    public function setZoneC($zoneC)
    {
        $this->zoneC = $zoneC;

        return $this;
    }

    /**
     * Get zoneC
     *
     * @return string
     */
    public function getZoneC()
    {
        return $this->zoneC;
    }

    /**
     * Set zoneD
     *
     * @param string $zoneD
     *
     * @return Imputation
     */
    public function setZoneD($zoneD)
    {
        $this->zoneD = $zoneD;

        return $this;
    }

    /**
     * Get zoneD
     *
     * @return string
     */
    public function getZoneD()
    {
        return $this->zoneD;
    }

    /**
     * Set escompte
     *
     * @param float $escompte
     *
     * @return Imputation
     */
    public function setEscompte($escompte)
    {
        $this->escompte = $escompte;

        return $this;
    }

    /**
     * Get escompte
     *
     * @return float
     */
    public function getEscompte()
    {
        return $this->escompte;
    }

    /**
     * Set soldeDebut
     *
     * @param float $soldeDebut
     *
     * @return Imputation
     */
    public function setSoldeDebut($soldeDebut)
    {
        $this->soldeDebut = $soldeDebut;

        return $this;
    }

    /**
     * Get soldeDebut
     *
     * @return float
     */
    public function getSoldeDebut()
    {
        return $this->soldeDebut;
    }

    /**
     * Set soldeFin
     *
     * @param float $soldeFin
     *
     * @return Imputation
     */
    public function setSoldeFin($soldeFin)
    {
        $this->soldeFin = $soldeFin;

        return $this;
    }

    /**
     * Get soldeFin
     *
     * @return float
     */
    public function getSoldeFin()
    {
        return $this->soldeFin;
    }

    /**
     * Set chrono
     *
     * @param string $chrono
     *
     * @return Imputation
     */
    public function setChrono($chrono)
    {
        $this->chrono = $chrono;

        return $this;
    }

    /**
     * Get chrono
     *
     * @return string
     */
    public function getChrono()
    {
        return $this->chrono;
    }

    /**
     * Set pnc
     *
     * @param integer $pnc
     *
     * @return Imputation
     */
    public function setPnc($pnc)
    {
        $this->pnc = $pnc;

        return $this;
    }

    /**
     * Get pnc
     *
     * @return integer
     */
    public function getPnc()
    {
        return $this->pnc;
    }

    /**
     * Set typeCaisse
     *
     * @param integer $typeCaisse
     *
     * @return Imputation
     */
    public function setTypeCaisse($typeCaisse)
    {
        $this->typeCaisse = $typeCaisse;

        return $this;
    }

    /**
     * Get typeCaisse
     *
     * @return integer
     */
    public function getTypeCaisse()
    {
        return $this->typeCaisse;
    }

    /**
     * Set codeTiers
     *
     * @param string $codeTiers
     *
     * @return Imputation
     */
    public function setCodeTiers($codeTiers)
    {
        $this->codeTiers = $codeTiers;

        return $this;
    }

    /**
     * Get codeTiers
     *
     * @return string
     */
    public function getCodeTiers()
    {
        return $this->codeTiers;
    }

    /**
     * Set montantPaye
     *
     * @param string $montantPaye
     *
     * @return Imputation
     */
    public function setMontantPaye($montantPaye)
    {
        $this->montantPaye = $montantPaye;

        return $this;
    }

    /**
     * Get montantPaye
     *
     * @return string
     */
    public function getMontantPaye()
    {
        return $this->montantPaye;
    }

    /**
     * Set comptePcc
     *
     * @param string $comptePcc
     *
     * @return Imputation
     */
    public function setComptePcc($comptePcc)
    {
        $this->comptePcc = $comptePcc;

        return $this;
    }

    /**
     * Get comptePcc
     *
     * @return string
     */
    public function getComptePcc()
    {
        return $this->comptePcc;
    }

    /**
     * Set tauxIntracomm
     *
     * @param float $tauxIntracomm
     *
     * @return Imputation
     */
    public function setTauxIntracomm($tauxIntracomm)
    {
        $this->tauxIntracomm = $tauxIntracomm;

        return $this;
    }

    /**
     * Get tauxIntracomm
     *
     * @return float
     */
    public function getTauxIntracomm()
    {
        return $this->tauxIntracomm;
    }

    /**
     * Set avecIntracom
     *
     * @param integer $avecIntracom
     *
     * @return Imputation
     */
    public function setAvecIntracom($avecIntracom)
    {
        $this->avecIntracom = $avecIntracom;

        return $this;
    }

    /**
     * Get avecIntracom
     *
     * @return integer
     */
    public function getAvecIntracom()
    {
        return $this->avecIntracom;
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
     * Set journalDossier
     *
     * @param \AppBundle\Entity\JournalDossier $journalDossier
     *
     * @return Imputation
     */
    public function setJournalDossier(\AppBundle\Entity\JournalDossier $journalDossier = null)
    {
        $this->journalDossier = $journalDossier;

        return $this;
    }

    /**
     * Get journalDossier
     *
     * @return \AppBundle\Entity\JournalDossier
     */
    public function getJournalDossier()
    {
        return $this->journalDossier;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Imputation
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
     * @return Imputation
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
     * Set typeAchatVente
     *
     * @param \AppBundle\Entity\TypeAchatVente $typeAchatVente
     *
     * @return Imputation
     */
    public function setTypeAchatVente(\AppBundle\Entity\TypeAchatVente $typeAchatVente = null)
    {
        $this->typeAchatVente = $typeAchatVente;

        return $this;
    }

    /**
     * Get typeAchatVente
     *
     * @return \AppBundle\Entity\TypeAchatVente
     */
    public function getTypeAchatVente()
    {
        return $this->typeAchatVente;
    }

    /**
     * Set typePiece
     *
     * @param \AppBundle\Entity\TypePiece $typePiece
     *
     * @return Imputation
     */
    public function setTypePiece(\AppBundle\Entity\TypePiece $typePiece = null)
    {
        $this->typePiece = $typePiece;

        return $this;
    }

    /**
     * Get typePiece
     *
     * @return \AppBundle\Entity\TypePiece
     */
    public function getTypePiece()
    {
        return $this->typePiece;
    }

    /**
     * Set devise
     *
     * @param \AppBundle\Entity\Devise $devise
     *
     * @return Imputation
     */
    public function setDevise(\AppBundle\Entity\Devise $devise = null)
    {
        $this->devise = $devise;

        return $this;
    }

    /**
     * Get devise
     *
     * @return \AppBundle\Entity\Devise
     */
    public function getDevise()
    {
        return $this->devise;
    }

    /**
     * Set organisme
     *
     * @param \AppBundle\Entity\Organisme $organisme
     *
     * @return Imputation
     */
    public function setOrganisme(\AppBundle\Entity\Organisme $organisme = null)
    {
        $this->organisme = $organisme;

        return $this;
    }

    /**
     * Get organisme
     *
     * @return \AppBundle\Entity\Organisme
     */
    public function getOrganisme()
    {
        return $this->organisme;
    }

    /**
     * Set cerfa
     *
     * @param \AppBundle\Entity\Cerfa $cerfa
     *
     * @return Imputation
     */
    public function setCerfa(\AppBundle\Entity\Cerfa $cerfa = null)
    {
        $this->cerfa = $cerfa;

        return $this;
    }

    /**
     * Get cerfa
     *
     * @return \AppBundle\Entity\Cerfa
     */
    public function getCerfa()
    {
        return $this->cerfa;
    }

    /**
     * Set banqueCompte
     *
     * @param \AppBundle\Entity\BanqueCompte $banqueCompte
     *
     * @return Imputation
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

    /**
     * Set pays
     *
     * @param \AppBundle\Entity\Pays $pays
     *
     * @return Imputation
     */
    public function setPays(\AppBundle\Entity\Pays $pays = null)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return \AppBundle\Entity\Pays
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set sousnature
     *
     * @param \AppBundle\Entity\Sousnature $sousnature
     *
     * @return Imputation
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
     * @return Imputation
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
     * Set souscategorie
     *
     * @param \AppBundle\Entity\Souscategorie $souscategorie
     *
     * @return Imputation
     */
    public function setSouscategorie(\AppBundle\Entity\Souscategorie $souscategorie = null)
    {
        $this->souscategorie = $souscategorie;

        return $this;
    }

    /**
     * Get souscategorie
     *
     * @return \AppBundle\Entity\Souscategorie
     */
    public function getSouscategorie()
    {
        return $this->souscategorie;
    }


    /**
     * Set mentionManuscrite
     *
     * @param \AppBundle\Entity\MentionManuscrite $mentionManuscrite
     *
     * @return $this
     */
    public function setMentionManuscrite(\AppBundle\Entity\MentionManuscrite $mentionManuscrite = null)
    {
        $this->mentionManuscrite = $mentionManuscrite;

        return $this;
    }

    /**
     * Get mentionManuscrite
     *
     * @return \AppBundle\Entity\MentionManuscrite
     */
    public function getMentionManuscrite()
    {
        return $this->mentionManuscrite;
    }



    /**
     * Set nbreCouvert
     *
     * @param integer $nbreCouvert
     *
     * @return $this
     */
    public function setNbreCouvert($nbreCouvert)
    {
        $this->nbreCouvert = $nbreCouvert;

        return $this;
    }

    /**
     * Get nbreCouvert
     *
     * @return integer
     */
    public function getNbreCouvert()
    {
        return $this->nbreCouvert;
    }


    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return $this
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
}
