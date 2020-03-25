<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImputationControle
 *
 * @ORM\Table(name="imputation_controle", indexes={@ORM\Index(name="fk_saisie_fournisseur_journal_dossier1_idx", columns={"journal_dossier_id"}), @ORM\Index(name="fk_saisie_fournisseur_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_saisie_fournisseur_type_piece1_idx", columns={"type_piece_id"}), @ORM\Index(name="fk_saisie_fournisseur_type_achat_vente1_idx", columns={"type_achat_vente_id"}), @ORM\Index(name="fk_saisie_fournisseur_devise1_idx", columns={"devise_id"}), @ORM\Index(name="fk_saisie_fournisseur_mode_reglement1_idx", columns={"mode_reglement_id"}), @ORM\Index(name="fk_imputation_controle_soussouscategorie1_idx", columns={"soussouscategorie_id"}), @ORM\Index(name="fk_impctrl_paysid_idx", columns={"pays_id"}), @ORM\Index(name="fk_impctrl_orgid_idx", columns={"organisme_id"}), @ORM\Index(name="fk_imputation_controle_cerfa1_idx", columns={"cerfa_id"}), @ORM\Index(name="fk_imputation_controle_sousnature1_idx", columns={"sousnature_id"}), @ORM\Index(name="fk_impctrl_banquecpteid_idx", columns={"banque_compte_id"}), @ORM\Index(name="fk_impctrl_souscateg_id_idx", columns={"souscategorie_id"}), @ORM\Index(name="ind_siren_impctrl", columns={"siret"}), @ORM\Index(name="fk_impctrl_mentionmanscrite1_idx", columns={"mention_manuscrite_id"}), @ORM\Index(name="fk_impctrl_ndf_utilisateur1_idx", columns={"ndf_utilisateur_id"}), @ORM\Index(name="fk_impctrl_image_flague1_idx", columns={"image_flague_id"}), @ORM\Index(name="fk_impctrl_cbbc1_idx", columns={"carte_bleu_banque_compte_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImputationControleRepository")
 */
class ImputationControle
{
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
     * @ORM\Column(name="page_solde_debut", type="integer", nullable=true)
     */
    private $pageSoldeDebut;

    /**
     * @var integer
     *
     * @ORM\Column(name="page_solde_fin", type="integer", nullable=true)
     */
    private $pageSoldeFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_releve", type="integer", nullable=false)
     */
    private $numReleve = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="tva_intracomm", type="string", length=45, nullable=true)
     */
    private $tvaIntracomm;

    /**
     * @var string
     *
     * @ORM\Column(name="rcs", type="string", length=45, nullable=true)
     */
    private $rcs;

    /**
     * @var float
     *
     * @ORM\Column(name="montant_ttc", type="float", precision=10, scale=0, nullable=true)
     */
    private $montantTtc = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="auto_liquidee", type="integer", nullable=true)
     */
    private $autoLiquidee = '0';

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
     * @var float
     *
     * @ORM\Column(name="distance", type="float", precision=10, scale=0, nullable=true)
     */
    private $distance;

    /**
     * @var integer
     *
     * @ORM\Column(name="remboursable", type="integer", nullable=true)
     */
    private $remboursable;

    /**
     * @var integer
     *
     * @ORM\Column(name="facturable", type="integer", nullable=true)
     */
    private $facturable;

    /**
     * @var integer
     *
     * @ORM\Column(name="annee", type="integer", nullable=true)
     */
    private $annee;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=true)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="mois_du", type="integer", nullable=true)
     */
    private $moisDu;

    /**
     * @var integer
     *
     * @ORM\Column(name="mois_au", type="integer", nullable=true)
     */
    private $moisAu;

    /**
     * @var integer
     *
     * @ORM\Column(name="ecriture_choisie", type="integer", nullable=false)
     */
    private $ecritureChoisie = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="type_sociale", type="integer", nullable=true)
     */
    private $typeSociale;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\Devise
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Devise")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="devise_id", referencedColumnName="id")
     * })
     */
    private $devise;

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
     * @var \AppBundle\Entity\JournalDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JournalDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="journal_dossier_id", referencedColumnName="id")
     * })
     */
    private $journalDossier;

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
     * @var \AppBundle\Entity\TypePiece
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypePiece")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_piece_id", referencedColumnName="id")
     * })
     */
    private $typePiece;

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
     * @var \AppBundle\Entity\Sousnature
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Sousnature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sousnature_id", referencedColumnName="id")
     * })
     */
    private $sousnature;

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
     * @var \AppBundle\Entity\MentionManuscrite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MentionManuscrite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mention_manuscrite_id", referencedColumnName="id")
     * })
     */
    private $mentionManuscrite;

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
     * @var \AppBundle\Entity\CarteBleuBanqueCompte
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarteBleuBanqueCompte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="carte_bleu_banque_compte_id", referencedColumnName="id")
     * })
     */
    private $carteBleuBanqueCompte;

    /**
     * @var \AppBundle\Entity\NdfUtilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfUtilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_utilisateur_id", referencedColumnName="id")
     * })
     */
    private $ndfUtilisateur;

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
     * @var \AppBundle\Entity\Souscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Souscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="souscategorie_id", referencedColumnName="id")
     * })
     */
    private $souscategorie;

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
     * @var \AppBundle\Entity\BanqueCompte
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BanqueCompte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_compte_id", referencedColumnName="id")
     * })
     */
    private $banqueCompte;



    /**
     * Set rs
     *
     * @param string $rs
     *
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * @return ImputationControle
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
     * Set pageSoldeDebut
     *
     * @param integer $pageSoldeDebut
     *
     * @return ImputationControle
     */
    public function setPageSoldeDebut($pageSoldeDebut)
    {
        $this->pageSoldeDebut = $pageSoldeDebut;

        return $this;
    }

    /**
     * Get pageSoldeDebut
     *
     * @return integer
     */
    public function getPageSoldeDebut()
    {
        return $this->pageSoldeDebut;
    }

    /**
     * Set pageSoldeFin
     *
     * @param integer $pageSoldeFin
     *
     * @return ImputationControle
     */
    public function setPageSoldeFin($pageSoldeFin)
    {
        $this->pageSoldeFin = $pageSoldeFin;

        return $this;
    }

    /**
     * Get pageSoldeFin
     *
     * @return integer
     */
    public function getPageSoldeFin()
    {
        return $this->pageSoldeFin;
    }

    /**
     * Set numReleve
     *
     * @param integer $numReleve
     *
     * @return ImputationControle
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
     * Set tvaIntracomm
     *
     * @param string $tvaIntracomm
     *
     * @return ImputationControle
     */
    public function setTvaIntracomm($tvaIntracomm)
    {
        $this->tvaIntracomm = $tvaIntracomm;

        return $this;
    }

    /**
     * Get tvaIntracomm
     *
     * @return string
     */
    public function getTvaIntracomm()
    {
        return $this->tvaIntracomm;
    }

    /**
     * Set rcs
     *
     * @param string $rcs
     *
     * @return ImputationControle
     */
    public function setRcs($rcs)
    {
        $this->rcs = $rcs;

        return $this;
    }

    /**
     * Get rcs
     *
     * @return string
     */
    public function getRcs()
    {
        return $this->rcs;
    }

    /**
     * Set montantTtc
     *
     * @param float $montantTtc
     *
     * @return ImputationControle
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
     * Set autoLiquidee
     *
     * @param integer $autoLiquidee
     *
     * @return ImputationControle
     */
    public function setAutoLiquidee($autoLiquidee)
    {
        $this->autoLiquidee = $autoLiquidee;

        return $this;
    }

    /**
     * Get autoLiquidee
     *
     * @return integer
     */
    public function getAutoLiquidee()
    {
        return $this->autoLiquidee;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return ImputationControle
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
     * Set nbreCouvert
     *
     * @param integer $nbreCouvert
     *
     * @return ImputationControle
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
     * Set distance
     *
     * @param float $distance
     *
     * @return ImputationControle
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set remboursable
     *
     * @param integer $remboursable
     *
     * @return ImputationControle
     */
    public function setRemboursable($remboursable)
    {
        $this->remboursable = $remboursable;

        return $this;
    }

    /**
     * Get remboursable
     *
     * @return integer
     */
    public function getRemboursable()
    {
        return $this->remboursable;
    }

    /**
     * Set facturable
     *
     * @param integer $facturable
     *
     * @return ImputationControle
     */
    public function setFacturable($facturable)
    {
        $this->facturable = $facturable;

        return $this;
    }

    /**
     * Get facturable
     *
     * @return integer
     */
    public function getFacturable()
    {
        return $this->facturable;
    }

    /**
     * Set annee
     *
     * @param integer $annee
     *
     * @return ImputationControle
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return integer
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ImputationControle
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
     * Set moisDu
     *
     * @param integer $moisDu
     *
     * @return ImputationControle
     */
    public function setMoisDu($moisDu)
    {
        $this->moisDu = $moisDu;

        return $this;
    }

    /**
     * Get moisDu
     *
     * @return integer
     */
    public function getMoisDu()
    {
        return $this->moisDu;
    }

    /**
     * Set moisAu
     *
     * @param integer $moisAu
     *
     * @return ImputationControle
     */
    public function setMoisAu($moisAu)
    {
        $this->moisAu = $moisAu;

        return $this;
    }

    /**
     * Get moisAu
     *
     * @return integer
     */
    public function getMoisAu()
    {
        return $this->moisAu;
    }

    /**
     * Set ecritureChoisie
     *
     * @param integer $ecritureChoisie
     *
     * @return ImputationControle
     */
    public function setEcritureChoisie($ecritureChoisie)
    {
        $this->ecritureChoisie = $ecritureChoisie;

        return $this;
    }

    /**
     * Get ecritureChoisie
     *
     * @return integer
     */
    public function getEcritureChoisie()
    {
        return $this->ecritureChoisie;
    }

    /**
     * Set typeSociale
     *
     * @param integer $typeSociale
     *
     * @return ImputationControle
     */
    public function setTypeSociale($typeSociale)
    {
        $this->typeSociale = $typeSociale;

        return $this;
    }

    /**
     * Get typeSociale
     *
     * @return integer
     */
    public function getTypeSociale()
    {
        return $this->typeSociale;
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
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return ImputationControle
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
     * Set devise
     *
     * @param \AppBundle\Entity\Devise $devise
     *
     * @return ImputationControle
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
     * Set soussouscategorie
     *
     * @param \AppBundle\Entity\Soussouscategorie $soussouscategorie
     *
     * @return ImputationControle
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
     * Set journalDossier
     *
     * @param \AppBundle\Entity\JournalDossier $journalDossier
     *
     * @return ImputationControle
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
     * Set modeReglement
     *
     * @param \AppBundle\Entity\ModeReglement $modeReglement
     *
     * @return ImputationControle
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
     * Set typePiece
     *
     * @param \AppBundle\Entity\TypePiece $typePiece
     *
     * @return ImputationControle
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
     * Set typeAchatVente
     *
     * @param \AppBundle\Entity\TypeAchatVente $typeAchatVente
     *
     * @return ImputationControle
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
     * Set sousnature
     *
     * @param \AppBundle\Entity\Sousnature $sousnature
     *
     * @return ImputationControle
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
     * Set cerfa
     *
     * @param \AppBundle\Entity\Cerfa $cerfa
     *
     * @return ImputationControle
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
     * Set mentionManuscrite
     *
     * @param \AppBundle\Entity\MentionManuscrite $mentionManuscrite
     *
     * @return ImputationControle
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
     * Set imageFlague
     *
     * @param \AppBundle\Entity\ImageFlague $imageFlague
     *
     * @return ImputationControle
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
     * Set carteBleuBanqueCompte
     *
     * @param \AppBundle\Entity\CarteBleuBanqueCompte $carteBleuBanqueCompte
     *
     * @return ImputationControle
     */
    public function setCarteBleuBanqueCompte(\AppBundle\Entity\CarteBleuBanqueCompte $carteBleuBanqueCompte = null)
    {
        $this->carteBleuBanqueCompte = $carteBleuBanqueCompte;

        return $this;
    }

    /**
     * Get carteBleuBanqueCompte
     *
     * @return \AppBundle\Entity\CarteBleuBanqueCompte
     */
    public function getCarteBleuBanqueCompte()
    {
        return $this->carteBleuBanqueCompte;
    }

    /**
     * Set ndfUtilisateur
     *
     * @param \AppBundle\Entity\NdfUtilisateur $ndfUtilisateur
     *
     * @return ImputationControle
     */
    public function setNdfUtilisateur(\AppBundle\Entity\NdfUtilisateur $ndfUtilisateur = null)
    {
        $this->ndfUtilisateur = $ndfUtilisateur;

        return $this;
    }

    /**
     * Get ndfUtilisateur
     *
     * @return \AppBundle\Entity\NdfUtilisateur
     */
    public function getNdfUtilisateur()
    {
        return $this->ndfUtilisateur;
    }

    /**
     * Set organisme
     *
     * @param \AppBundle\Entity\Organisme $organisme
     *
     * @return ImputationControle
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
     * Set souscategorie
     *
     * @param \AppBundle\Entity\Souscategorie $souscategorie
     *
     * @return ImputationControle
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
     * Set pays
     *
     * @param \AppBundle\Entity\Pays $pays
     *
     * @return ImputationControle
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
     * Set banqueCompte
     *
     * @param \AppBundle\Entity\BanqueCompte $banqueCompte
     *
     * @return ImputationControle
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
