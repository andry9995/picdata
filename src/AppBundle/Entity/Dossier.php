<?php

namespace AppBundle\Entity;

use AppBundle\Controller\Boost;
use Doctrine\ORM\Mapping as ORM;

/**
 * Dossier
 *
 * @ORM\Table(name="dossier", uniqueConstraints={@ORM\UniqueConstraint(name="fk_unik_dossier_site", columns={"nom", "site_id"})}, indexes={@ORM\Index(name="fk_dossier_note_de_frais1_idx", columns={"note_de_frais_id"}), @ORM\Index(name="fk_dossier_profession_liberale1_idx", columns={"profession_liberale_id"}), @ORM\Index(name="fk_dossier_site1_idx", columns={"site_id"}), @ORM\Index(name="fk_dossier_forme_dossier1_idx", columns={"forme_juridique_id"}), @ORM\Index(name="fk_dossier_type_dossier1_idx", columns={"type_dossier_id"}), @ORM\Index(name="fk_dossier_type_vente1_idx", columns={"type_vente_id"}), @ORM\Index(name="fk_dossier_regime_tva1_idx", columns={"regime_tva_id"}), @ORM\Index(name="fk_dossier_regime_suivi1_idx", columns={"regime_suivi_id"}), @ORM\Index(name="fk_dossier_contrat_prevoyance1_idx", columns={"contrat_prevoyance_id"}), @ORM\Index(name="fk_dossier_regime_fiscal1_idx", columns={"regime_fiscal_id"}), @ORM\Index(name="fk_dossier_regime_imposition1_idx", columns={"regime_imposition_id"}), @ORM\Index(name="fk_dossier_convention_comptable_id_idx", columns={"convention_comptable_id"}), @ORM\Index(name="fk_dossier_tva_type_id_idx", columns={"tva_type_id"}), @ORM\Index(name="fk_dossier_type_activite_idx", columns={"type_activite_id"}), @ORM\Index(name="index_dossier_nom", columns={"nom"}), @ORM\Index(name="fk_dossier_activite_com_cat_31_idx", columns={"activite_com_cat_3_id"}), @ORM\Index(name="fk_dossier_nature_activite1_idx", columns={"nature_activite_id"}), @ORM\Index(name="fk_dossier_mode_vente1_idx", columns={"mode_vente_id"}), @ORM\Index(name="fk_dossier_forme_activite1_idx", columns={"forme_activite_id"}), @ORM\Index(name="fk_dossier_tranche_effectif1_idx", columns={"tranche_effectif_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DossierRepository")
 */
class Dossier
{
    /***********************************
     *          MODIF MANUEL
     ***********************************/
    /**
     * @var
     */
    private $idCrypter;

    /**
     * @return $this
     */
    public function setIdCrypter()
    {
        $this->idCrypter = Boost::boost($this->id);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdCrypter()
    {
        $this->setIdCrypter();
        return $this->idCrypter;
    }

    /**
     * @var null
     */
    private $indicateurGroup = null;

    /**
     * @param $indicateurGroup
     * @return $this
     */
    public function setIndicateurGroup($indicateurGroup)
    {
        $this->indicateurGroup = $indicateurGroup;
        return $this;
    }

    /**
     * @return null
     */
    public function getIndicateurGroup()
    {
        return $this->indicateurGroup;
    }

    /**
     * @var TbimageCategorie
     * Liste des categories pour tbimage
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\TbimageCategorie", mappedBy="dossier")
     */
    private $tbimageCategorie;

    /**
     * Set tbimageCategorie
     *
     * @param \AppBundle\Entity\TbimageCategorie $tbimageCategorie
     *
     * @return Dossier
     */
    public function setTbimageCategorie(\AppBundle\Entity\TbimageCategorie $tbimageCategorie = null)
    {
        $this->tbimageCategorie = $tbimageCategorie;

        return $this;
    }

    /**
     * Get tbimageCategorie
     *
     * @return \AppBundle\Entity\TbimageCategorie
     */
    public function getTbimageCategorie()
    {
        return $this->tbimageCategorie;
    }

    /**
     * @var TbimagePeriode
     * Parametre période pour tbimage
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\TbimagePeriode", mappedBy="dossier")
     */
    private $tbimagePeriode;

    /**
     * Set tbimagePeriode
     *
     * @param \AppBundle\Entity\TbimagePeriode $tbimagePeriode
     *
     * @return Dossier
     */
    public function setTbimagePeriode(\AppBundle\Entity\TbimagePeriode $tbimagePeriode = null)
    {
        $this->tbimagePeriode = $tbimagePeriode;

        return $this;
    }

    /**
     * Get tbimagePeriode
     *
     * @return \AppBundle\Entity\TbimagePeriode
     */
    public function getTbimagePeriode()
    {
        return $this->tbimagePeriode;
    }

    public function getClotureJourMois()
    {
        $fin_mois = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        return $fin_mois[intval($this->getCloture()) - 1] . '/' . str_pad(strval($this->cloture), 2, '0', STR_PAD_LEFT);

    }

    /***********************************
     *          fin MODIF MANUEL
     ***********************************/


    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=150, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="entreprise", type="string", length=150, nullable=false)
     */
    private $entreprise;

    /**
     * @var integer
     *
     * @ORM\Column(name="cloture", type="integer", nullable=false)
     */
    private $cloture = '12';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut_activite", type="date", nullable=true)
     */
    private $debutActivite;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_stop_saisie", type="date", nullable=true)
     */
    private $dateStopSaisie;

    /**
     * @var integer
     *
     * @ORM\Column(name="effectif", type="integer", nullable=false)
     */
    private $effectif = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="taxe_salaire", type="integer", nullable=false)
     */
    private $taxeSalaire = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="gerant", type="integer", nullable=false)
     */
    private $gerant = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="rs_ste", type="string", length=85, nullable=true)
     */
    private $rsSte;

    /**
     * @var string
     *
     * @ORM\Column(name="siren_ste", type="string", length=45, nullable=true)
     */
    private $sirenSte;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_ste", type="string", length=100, nullable=true)
     */
    private $adresseSte;

    /**
     * @var string
     *
     * @ORM\Column(name="tel_ste", type="string", length=65, nullable=true)
     */
    private $telSte;

    /**
     * @var string
     *
     * @ORM\Column(name="mandataire_ste", type="string", length=45, nullable=true)
     */
    private $mandataireSte;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva", type="integer", nullable=true)
     */
    private $tva = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="compta_sur_serveur", type="integer", nullable=true)
     */
    private $comptaSurServeur;

    /**
     * @var integer
     *
     * @ORM\Column(name="archive_comptable", type="integer", nullable=true)
     */
    private $archiveComptable;

    /**
     * @var integer
     *
     * @ORM\Column(name="plan_comptable", type="integer", nullable=true)
     */
    private $planComptable;

    /**
     * @var integer
     *
     * @ORM\Column(name="grand_livre", type="integer", nullable=true)
     */
    private $grandLivre;

    /**
     * @var integer
     *
     * @ORM\Column(name="journaux_n1", type="integer", nullable=true)
     */
    private $journauxN1;

    /**
     * @var integer
     *
     * @ORM\Column(name="dernier_rapprochement_banque_n1", type="integer", nullable=true)
     */
    private $dernierRapprochementBanqueN1;

    /**
     * @var integer
     *
     * @ORM\Column(name="etat_immobilisation_n1", type="integer", nullable=true)
     */
    private $etatImmobilisationN1;

    /**
     * @var integer
     *
     * @ORM\Column(name="liasse_fiscale_n1", type="integer", nullable=true)
     */
    private $liasseFiscaleN1;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva_derniere_ca3", type="integer", nullable=true)
     */
    private $tvaDerniereCa3;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva_taux_id", type="integer", nullable=true)
     */
    private $tvaTauxId;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva_date", type="integer", nullable=true)
     */
    private $tvaDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="statut", type="integer", nullable=true)
     */
    private $statut;

    /**
     * @var integer
     *
     * @ORM\Column(name="kbis", type="integer", nullable=true)
     */
    private $kbis;

    /**
     * @var integer
     *
     * @ORM\Column(name="baux", type="integer", nullable=true)
     */
    private $baux;

    /**
     * @var integer
     *
     * @ORM\Column(name="assurance", type="integer", nullable=true)
     */
    private $assurance;

    /**
     * @var integer
     *
     * @ORM\Column(name="autre", type="integer", nullable=true)
     */
    private $autre;

    /**
     * @var integer
     *
     * @ORM\Column(name="emprunt", type="integer", nullable=true)
     */
    private $emprunt;

    /**
     * @var integer
     *
     * @ORM\Column(name="leasing", type="integer", nullable=true)
     */
    private $leasing;


    /**
     * @var integer
     *
     * @ORM\Column(name="premier_exercice", type="integer", nullable=true)
     */
    private $premierExercice;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva_mode", type="integer", nullable=true)
     */
    private $tvaMode;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_prestation", type="integer", nullable=true)
     */
    private $typePrestation;

    /**
     * @var string
     *
     * @ORM\Column(name="autre_prestation", type="text", length=65535, nullable=true)
     */
    private $autrePrestation;

    /**
     * @var integer
     *
     * @ORM\Column(name="balance_n1", type="integer", nullable=true)
     */
    private $balanceN1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="centr_caisse", type="boolean", nullable=false)
     */
    private $centrCaisse = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_cloture", type="date", nullable=true)
     */
    private $dateCloture;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", nullable=false)
     */
    private $active = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="tva_fait_generateur", type="integer", nullable=true)
     */
    private $tvaFaitGenerateur;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="status_debut", type="integer", nullable=true)
     */
    private $statusDebut;

    /**
     * @var integer
     *
     * @ORM\Column(name="original", type="integer", nullable=true)
     */
    private $original;

    /**
     * @var string
     *
     * @ORM\Column(name="enseigne", type="string", length=100, nullable=true)
     */
    private $enseigne;

    /**
     * @var string
     *
     * @ORM\Column(name="num_rue", type="string", length=100, nullable=true)
     */
    private $numRue;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=45, nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=45, nullable=true)
     */
    private $pays;

    /**
     * @var boolean
     *
     * @ORM\Column(name="non_traitable", type="boolean", nullable=false)
     */
    private $nonTraitable = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=45, nullable=true)
     */
    private $ville;

    /**
     * @var integer
     *
     * @ORM\Column(name="accuse_creation", type="integer", nullable=true)
     */
    private $accuseCreation = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cegid", type="string", length=45, nullable=true)
     */
    private $cegid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_in_demo", type="boolean", nullable=false)
     */
    private $showInDemo = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=true)
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="date", nullable=true)
     */
    private $dateModification;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TrancheEffectif
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TrancheEffectif")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tranche_effectif_id", referencedColumnName="id")
     * })
     */
    private $trancheEffectif;

    /**
     * @var \AppBundle\Entity\Site
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * })
     */
    private $site;

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
     * @var \AppBundle\Entity\RegimeSuivi
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RegimeSuivi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="regime_suivi_id", referencedColumnName="id")
     * })
     */
    private $regimeSuivi;

    /**
     * @var \AppBundle\Entity\TvaType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_type_id", referencedColumnName="id")
     * })
     */
    private $tvaType;

    /**
     * @var \AppBundle\Entity\TypeActivite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeActivite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_activite_id", referencedColumnName="id")
     * })
     */
    private $typeActivite;

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
     * @var \AppBundle\Entity\TypePrestation
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypePrestation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_prestation_id", referencedColumnName="id")
     * })
     */
    private $typePrestation2;

    /**
     * @var \AppBundle\Entity\TypeDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_dossier_id", referencedColumnName="id")
     * })
     */
    private $typeDossier;

    /**
     * @var \AppBundle\Entity\RegimeImposition
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RegimeImposition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="regime_imposition_id", referencedColumnName="id")
     * })
     */
    private $regimeImposition;

    /**
     * @var \AppBundle\Entity\RegimeFiscal
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RegimeFiscal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="regime_fiscal_id", referencedColumnName="id")
     * })
     */
    private $regimeFiscal;

    /**
     * @var \AppBundle\Entity\FormeActivite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FormeActivite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="forme_activite_id", referencedColumnName="id")
     * })
     */
    private $formeActivite;

    /**
     * @var \AppBundle\Entity\ConventionComptable
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ConventionComptable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="convention_comptable_id", referencedColumnName="id")
     * })
     */
    private $conventionComptable;

    /**
     * @var \AppBundle\Entity\ContratPrevoyance
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ContratPrevoyance")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contrat_prevoyance_id", referencedColumnName="id")
     * })
     */
    private $contratPrevoyance;

    /**
     * @var \AppBundle\Entity\FormeJuridique
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FormeJuridique")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="forme_juridique_id", referencedColumnName="id")
     * })
     */
    private $formeJuridique;

    /**
     * @var \AppBundle\Entity\ModeVente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModeVente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mode_vente_id", referencedColumnName="id")
     * })
     */
    private $modeVente;

    /**
     * @var \AppBundle\Entity\ProfessionLiberale
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProfessionLiberale")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="profession_liberale_id", referencedColumnName="id")
     * })
     */
    private $professionLiberale;

    /**
     * @var \AppBundle\Entity\NoteDeFrais
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NoteDeFrais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="note_de_frais_id", referencedColumnName="id")
     * })
     */
    private $noteDeFrais;

    /**
     * @var \AppBundle\Entity\NatureActivite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NatureActivite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nature_activite_id", referencedColumnName="id")
     * })
     */
    private $natureActivite;

    /**
     * @var \AppBundle\Entity\ActiviteComCat3
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ActiviteComCat3")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activite_com_cat_3_id", referencedColumnName="id")
     * })
     */
    private $activiteComCat3;



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Dossier
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
     * Set entreprise
     *
     * @param string $entreprise
     *
     * @return Dossier
     */
    public function setEntreprise($entreprise)
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * Get entreprise
     *
     * @return string
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * Set cloture
     *
     * @param integer $cloture
     *
     * @return Dossier
     */
    public function setCloture($cloture)
    {
        $this->cloture = $cloture;

        return $this;
    }

    /**
     * Get cloture
     *
     * @return integer
     */
    public function getCloture()
    {
        if ($this->cloture == 0) {
            $this->cloture = 12;
        }
        return $this->cloture;
    }

    /**
     * Set debutActivite
     *
     * @param \DateTime $debutActivite
     *
     * @return Dossier
     */
    public function setDebutActivite($debutActivite)
    {
        $this->debutActivite = $debutActivite;

        return $this;
    }

    /**
     * Get debutActivite
     *
     * @return \DateTime
     */
    public function getDebutActivite()
    {
        return $this->debutActivite;
    }

    /**
     * Set dateStopSaisie
     *
     * @param \DateTime $dateStopSaisie
     *
     * @return Dossier
     */
    public function setDateStopSaisie($dateStopSaisie)
    {
        $this->dateStopSaisie = $dateStopSaisie;

        return $this;
    }

    /**
     * Get dateStopSaisie
     *
     * @return \DateTime
     */
    public function getDateStopSaisie()
    {
        return $this->dateStopSaisie;
    }


    /**
     * Set effectif
     *
     * @param integer $effectif
     *
     * @return Dossier
     */
    public function setEffectif($effectif)
    {
        $this->effectif = $effectif;

        return $this;
    }

    /**
     * Get effectif
     *
     * @return integer
     */
    public function getEffectif()
    {
        return $this->effectif;
    }

    /**
     * Set taxeSalaire
     *
     * @param integer $taxeSalaire
     *
     * @return Dossier
     */
    public function setTaxeSalaire($taxeSalaire)
    {
        $this->taxeSalaire = $taxeSalaire;

        return $this;
    }

    /**
     * Get taxeSalaire
     *
     * @return integer
     */
    public function getTaxeSalaire()
    {
        return $this->taxeSalaire;
    }

    /**
     * Set gerant
     *
     * @param integer $gerant
     *
     * @return Dossier
     */
    public function setGerant($gerant)
    {
        $this->gerant = $gerant;

        return $this;
    }

    /**
     * Get gerant
     *
     * @return integer
     */
    public function getGerant()
    {
        return $this->gerant;
    }

    /**
     * Set rsSte
     *
     * @param string $rsSte
     *
     * @return Dossier
     */
    public function setRsSte($rsSte)
    {
        $this->rsSte = $rsSte;

        return $this;
    }

    /**
     * Get rsSte
     *
     * @return string
     */
    public function getRsSte()
    {
        return $this->rsSte;
    }

    /**
     * Set sirenSte
     *
     * @param string $sirenSte
     *
     * @return Dossier
     */
    public function setSirenSte($sirenSte)
    {
        $this->sirenSte = $sirenSte;

        return $this;
    }

    /**
     * Get sirenSte
     *
     * @return string
     */
    public function getSirenSte()
    {
        return $this->sirenSte;
    }

    /**
     * Set adresseSte
     *
     * @param string $adresseSte
     *
     * @return Dossier
     */
    public function setAdresseSte($adresseSte)
    {
        $this->adresseSte = $adresseSte;

        return $this;
    }

    /**
     * Get adresseSte
     *
     * @return string
     */
    public function getAdresseSte()
    {
        return $this->adresseSte;
    }

    /**
     * Set telSte
     *
     * @param string $telSte
     *
     * @return Dossier
     */
    public function setTelSte($telSte)
    {
        $this->telSte = $telSte;

        return $this;
    }

    /**
     * Get telSte
     *
     * @return string
     */
    public function getTelSte()
    {
        return $this->telSte;
    }

    /**
     * Set mandataireSte
     *
     * @param string $mandataireSte
     *
     * @return Dossier
     */
    public function setMandataireSte($mandataireSte)
    {
        $this->mandataireSte = $mandataireSte;

        return $this;
    }

    /**
     * Get mandataireSte
     *
     * @return string
     */
    public function getMandataireSte()
    {
        return $this->mandataireSte;
    }

    /**
     * Set tva
     *
     * @param integer $tva
     *
     * @return Dossier
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return integer
     */
    public function getTva()
    {
        return $this->tva;
    }

    /**
     * Set comptaSurServeur
     *
     * @param integer $comptaSurServeur
     *
     * @return Dossier
     */
    public function setComptaSurServeur($comptaSurServeur)
    {
        $this->comptaSurServeur = $comptaSurServeur;

        return $this;
    }

    /**
     * Get comptaSurServeur
     *
     * @return integer
     */
    public function getComptaSurServeur()
    {
        return $this->comptaSurServeur;
    }

    /**
     * Set archiveComptable
     *
     * @param integer $archiveComptable
     *
     * @return Dossier
     */
    public function setArchiveComptable($archiveComptable)
    {
        $this->archiveComptable = $archiveComptable;

        return $this;
    }

    /**
     * Get archiveComptable
     *
     * @return integer
     */
    public function getArchiveComptable()
    {
        return $this->archiveComptable;
    }

    /**
     * Set planComptable
     *
     * @param integer $planComptable
     *
     * @return Dossier
     */
    public function setPlanComptable($planComptable)
    {
        $this->planComptable = $planComptable;

        return $this;
    }

    /**
     * Get planComptable
     *
     * @return integer
     */
    public function getPlanComptable()
    {
        return $this->planComptable;
    }

    /**
     * Set grandLivre
     *
     * @param integer $grandLivre
     *
     * @return Dossier
     */
    public function setGrandLivre($grandLivre)
    {
        $this->grandLivre = $grandLivre;

        return $this;
    }

    /**
     * Get grandLivre
     *
     * @return integer
     */
    public function getGrandLivre()
    {
        return $this->grandLivre;
    }

    /**
     * Set journauxN1
     *
     * @param integer $journauxN1
     *
     * @return Dossier
     */
    public function setJournauxN1($journauxN1)
    {
        $this->journauxN1 = $journauxN1;

        return $this;
    }

    /**
     * Get journauxN1
     *
     * @return integer
     */
    public function getJournauxN1()
    {
        return $this->journauxN1;
    }

    /**
     * Set dernierRapprochementBanqueN1
     *
     * @param integer $dernierRapprochementBanqueN1
     *
     * @return Dossier
     */
    public function setDernierRapprochementBanqueN1($dernierRapprochementBanqueN1)
    {
        $this->dernierRapprochementBanqueN1 = $dernierRapprochementBanqueN1;

        return $this;
    }

    /**
     * Get dernierRapprochementBanqueN1
     *
     * @return integer
     */
    public function getDernierRapprochementBanqueN1()
    {
        return $this->dernierRapprochementBanqueN1;
    }

    /**
     * Set etatImmobilisationN1
     *
     * @param integer $etatImmobilisationN1
     *
     * @return Dossier
     */
    public function setEtatImmobilisationN1($etatImmobilisationN1)
    {
        $this->etatImmobilisationN1 = $etatImmobilisationN1;

        return $this;
    }

    /**
     * Get etatImmobilisationN1
     *
     * @return integer
     */
    public function getEtatImmobilisationN1()
    {
        return $this->etatImmobilisationN1;
    }

    /**
     * Set liasseFiscaleN1
     *
     * @param integer $liasseFiscaleN1
     *
     * @return Dossier
     */
    public function setLiasseFiscaleN1($liasseFiscaleN1)
    {
        $this->liasseFiscaleN1 = $liasseFiscaleN1;

        return $this;
    }

    /**
     * Get liasseFiscaleN1
     *
     * @return integer
     */
    public function getLiasseFiscaleN1()
    {
        return $this->liasseFiscaleN1;
    }

    /**
     * Set tvaDerniereCa3
     *
     * @param integer $tvaDerniereCa3
     *
     * @return Dossier
     */
    public function setTvaDerniereCa3($tvaDerniereCa3)
    {
        $this->tvaDerniereCa3 = $tvaDerniereCa3;

        return $this;
    }

    /**
     * Get tvaDerniereCa3
     *
     * @return integer
     */
    public function getTvaDerniereCa3()
    {
        return $this->tvaDerniereCa3;
    }

    /**
     * Set tvaTauxId
     *
     * @param integer $tvaTauxId
     *
     * @return Dossier
     */
    public function setTvaTauxId($tvaTauxId)
    {
        $this->tvaTauxId = $tvaTauxId;

        return $this;
    }

    /**
     * Get tvaTauxId
     *
     * @return integer
     */
    public function getTvaTauxId()
    {
        return $this->tvaTauxId;
    }

    /**
     * Set tvaDate
     *
     * @param integer $tvaDate
     *
     * @return Dossier
     */
    public function setTvaDate($tvaDate)
    {
        $this->tvaDate = $tvaDate;

        return $this;
    }

    /**
     * Get tvaDate
     *
     * @return integer
     */
    public function getTvaDate()
    {
        return $this->tvaDate;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     *
     * @return Dossier
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return integer
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set kbis
     *
     * @param integer $kbis
     *
     * @return Dossier
     */
    public function setKbis($kbis)
    {
        $this->kbis = $kbis;

        return $this;
    }

    /**
     * Get kbis
     *
     * @return integer
     */
    public function getKbis()
    {
        return $this->kbis;
    }

    /**
     * Set baux
     *
     * @param integer $baux
     *
     * @return Dossier
     */
    public function setBaux($baux)
    {
        $this->baux = $baux;

        return $this;
    }

    /**
     * Get baux
     *
     * @return integer
     */
    public function getBaux()
    {
        return $this->baux;
    }

    /**
     * Set assurance
     *
     * @param integer $assurance
     *
     * @return Dossier
     */
    public function setAssurance($assurance)
    {
        $this->assurance = $assurance;

        return $this;
    }

    /**
     * Get assurance
     *
     * @return integer
     */
    public function getAssurance()
    {
        return $this->assurance;
    }

    /**
     * Set autre
     *
     * @param integer $autre
     *
     * @return Dossier
     */
    public function setAutre($autre)
    {
        $this->autre = $autre;

        return $this;
    }

    /**
     * Get autre
     *
     * @return integer
     */
    public function getAutre()
    {
        return $this->autre;
    }

    /**
     * Set emprunt
     *
     * @param integer $emprunt
     *
     * @return Dossier
     */
    public function setEmprunt($emprunt)
    {
        $this->emprunt = $emprunt;

        return $this;
    }

    /**
     * Get emprunt
     *
     * @return integer
     */
    public function getEmprunt()
    {
        return $this->emprunt;
    }

    /**
     * Set leasing
     *
     * @param integer $leasing
     *
     * @return Dossier
     */
    public function setLeasing($leasing)
    {
        $this->leasing = $leasing;

        return $this;
    }

    /**
     * Get leasing
     *
     * @return integer
     */
    public function getLeasing()
    {
        return $this->leasing;
    }


    /**
     * Set premierExercice
     *
     * @param integer $premierExercice
     *
     * @return Dossier
     */
    public function setPremierExercice($premierExercice)
    {
        $this->premierExercice = $premierExercice;

        return $this;
    }

    /**
     * Get premierExercice
     *
     * @return integer
     */
    public function getPremierExercice()
    {
        return $this->premierExercice;
    }

    /**
     * Set tvaMode
     *
     * @param integer $tvaMode
     *
     * @return Dossier
     */
    public function setTvaMode($tvaMode)
    {
        $this->tvaMode = $tvaMode;

        return $this;
    }

    /**
     * Get tvaMode
     *
     * @return integer
     */
    public function getTvaMode()
    {
        return $this->tvaMode;
    }

    /**
     * Set typePrestation
     *
     * @param integer $typePrestation
     *
     * @return Dossier
     */
    public function setTypePrestation($typePrestation)
    {
        $this->typePrestation = $typePrestation;

        return $this;
    }

    /**
     * Get typePrestation
     *
     * @return integer
     */
    public function getTypePrestation()
    {
        return $this->typePrestation;
    }

    /**
     * Set autrePrestation
     *
     * @param string $autrePrestation
     *
     * @return Dossier
     */
    public function setAutrePrestation($autrePrestation)
    {
        $this->autrePrestation = $autrePrestation;

        return $this;
    }

    /**
     * Get autrePrestation
     *
     * @return string
     */
    public function getAutrePrestation()
    {
        return $this->autrePrestation;
    }

    /**
     * Set balanceN1
     *
     * @param integer $balanceN1
     *
     * @return Dossier
     */
    public function setBalanceN1($balanceN1)
    {
        $this->balanceN1 = $balanceN1;

        return $this;
    }

    /**
     * Get balanceN1
     *
     * @return integer
     */
    public function getBalanceN1()
    {
        return $this->balanceN1;
    }

    /**
     * Set centrCaisse
     *
     * @param boolean $centrCaisse
     *
     * @return Dossier
     */
    public function setCentrCaisse($centrCaisse)
    {
        $this->centrCaisse = $centrCaisse;

        return $this;
    }

    /**
     * Get centrCaisse
     *
     * @return boolean
     */
    public function getCentrCaisse()
    {
        return $this->centrCaisse;
    }

    /**
     * Set dateCloture
     *
     * @param \DateTime $dateCloture
     *
     * @return Dossier
     */
    public function setDateCloture($dateCloture)
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    /**
     * Get dateCloture
     *
     * @return \DateTime
     */
    public function getDateCloture()
    {
        return $this->dateCloture;
    }

    /**
     * Set active
     *
     * @param integer $active
     *
     * @return Dossier
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set tvaFaitGenerateur
     *
     * @param integer $tvaFaitGenerateur
     *
     * @return Dossier
     */
    public function setTvaFaitGenerateur($tvaFaitGenerateur)
    {
        $this->tvaFaitGenerateur = $tvaFaitGenerateur;

        return $this;
    }

    /**
     * Get tvaFaitGenerateur
     *
     * @return integer
     */
    public function getTvaFaitGenerateur()
    {
        return $this->tvaFaitGenerateur;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Dossier
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set statusDebut
     *
     * @param integer $statusDebut
     *
     * @return Dossier
     */
    public function setStatusDebut($statusDebut)
    {
        $this->statusDebut = $statusDebut;

        return $this;
    }

    /**
     * Get statusDebut
     *
     * @return integer
     */
    public function getStatusDebut()
    {
        return $this->statusDebut;
    }

    /**
     * Set original
     *
     * @param integer $original
     *
     * @return Dossier
     */
    public function setOriginal($original)
    {
        $this->original = $original;

        return $this;
    }

    /**
     * Get original
     *
     * @return integer
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Set enseigne
     *
     * @param string $enseigne
     *
     * @return Dossier
     */
    public function setEnseigne($enseigne)
    {
        $this->enseigne = $enseigne;

        return $this;
    }

    /**
     * Get enseigne
     *
     * @return string
     */
    public function getEnseigne()
    {
        return $this->enseigne;
    }

    /**
     * Set numRue
     *
     * @param string $numRue
     *
     * @return Dossier
     */
    public function setNumRue($numRue)
    {
        $this->numRue = $numRue;

        return $this;
    }

    /**
     * Get numRue
     *
     * @return string
     */
    public function getNumRue()
    {
        return $this->numRue;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return Dossier
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
     * Set pays
     *
     * @param string $pays
     *
     * @return Dossier
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set nonTraitable
     *
     * @param boolean $nonTraitable
     *
     * @return Dossier
     */
    public function setNonTraitable($nonTraitable)
    {
        $this->nonTraitable = $nonTraitable;

        return $this;
    }

    /**
     * Get nonTraitable
     *
     * @return boolean
     */
    public function getNonTraitable()
    {
        return $this->nonTraitable;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Dossier
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set accuseCreation
     *
     * @param integer $accuseCreation
     *
     * @return Dossier
     */
    public function setAccuseCreation($accuseCreation)
    {
        $this->accuseCreation = $accuseCreation;

        return $this;
    }

    /**
     * Get accuseCreation
     *
     * @return integer
     */
    public function getAccuseCreation()
    {
        return $this->accuseCreation;
    }

    /**
     * Set cegid
     *
     * @param string $cegid
     *
     * @return Dossier
     */
    public function setCegid($cegid)
    {
        $this->cegid = $cegid;

        return $this;
    }

    /**
     * Get cegid
     *
     * @return string
     */
    public function getCegid()
    {
        return $this->cegid;
    }

    /**
     * Set showInDemo
     *
     * @param boolean $showInDemo
     *
     * @return Dossier
     */
    public function setShowInDemo($showInDemo)
    {
        $this->showInDemo = $showInDemo;

        return $this;
    }

    /**
     * Get showInDemo
     *
     * @return boolean
     */
    public function getShowInDemo()
    {
        return $this->showInDemo;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Dossier
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return Dossier
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
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
     * Set trancheEffectif
     *
     * @param \AppBundle\Entity\TrancheEffectif $trancheEffectif
     *
     * @return Dossier
     */
    public function setTrancheEffectif(\AppBundle\Entity\TrancheEffectif $trancheEffectif = null)
    {
        $this->trancheEffectif = $trancheEffectif;

        return $this;
    }

    /**
     * Get trancheEffectif
     *
     * @return \AppBundle\Entity\TrancheEffectif
     */
    public function getTrancheEffectif()
    {
        return $this->trancheEffectif;
    }

    /**
     * Set site
     *
     * @param \AppBundle\Entity\Site $site
     *
     * @return Dossier
     */
    public function setSite(\AppBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \AppBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set regimeTva
     *
     * @param \AppBundle\Entity\RegimeTva $regimeTva
     *
     * @return Dossier
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
     * Set regimeSuivi
     *
     * @param \AppBundle\Entity\RegimeSuivi $regimeSuivi
     *
     * @return Dossier
     */
    public function setRegimeSuivi(\AppBundle\Entity\RegimeSuivi $regimeSuivi = null)
    {
        $this->regimeSuivi = $regimeSuivi;

        return $this;
    }

    /**
     * Get regimeSuivi
     *
     * @return \AppBundle\Entity\RegimeSuivi
     */
    public function getRegimeSuivi()
    {
        return $this->regimeSuivi;
    }

    /**
     * Set tvaType
     *
     * @param \AppBundle\Entity\TvaType $tvaType
     *
     * @return Dossier
     */
    public function setTvaType(\AppBundle\Entity\TvaType $tvaType = null)
    {
        $this->tvaType = $tvaType;

        return $this;
    }

    /**
     * Get tvaType
     *
     * @return \AppBundle\Entity\TvaType
     */
    public function getTvaType()
    {
        return $this->tvaType;
    }

    /**
     * Set typeActivite
     *
     * @param \AppBundle\Entity\TypeActivite $typeActivite
     *
     * @return Dossier
     */
    public function setTypeActivite(\AppBundle\Entity\TypeActivite $typeActivite = null)
    {
        $this->typeActivite = $typeActivite;

        return $this;
    }

    /**
     * Get typeActivite
     *
     * @return \AppBundle\Entity\TypeActivite
     */
    public function getTypeActivite()
    {
        return $this->typeActivite;
    }

    /**
     * Set typeVente
     *
     * @param \AppBundle\Entity\TypeVente $typeVente
     *
     * @return Dossier
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
     * Set typePrestation2
     *
     * @param \AppBundle\Entity\TypePrestation $typePrestation2
     *
     * @return Dossier
     */
    public function setTypePrestation2(\AppBundle\Entity\TypePrestation $typePrestation2 = null)
    {
        $this->typePrestation2 = $typePrestation2;

        return $this;
    }

    /**
     * Get typePrestation2
     *
     * @return \AppBundle\Entity\TypePrestation
     */
    public function getTypePrestation2()
    {
        return $this->typePrestation2;
    }

    /**
     * Set typeDossier
     *
     * @param \AppBundle\Entity\TypeDossier $typeDossier
     *
     * @return Dossier
     */
    public function setTypeDossier(\AppBundle\Entity\TypeDossier $typeDossier = null)
    {
        $this->typeDossier = $typeDossier;

        return $this;
    }

    /**
     * Get typeDossier
     *
     * @return \AppBundle\Entity\TypeDossier
     */
    public function getTypeDossier()
    {
        return $this->typeDossier;
    }

    /**
     * Set regimeImposition
     *
     * @param \AppBundle\Entity\RegimeImposition $regimeImposition
     *
     * @return Dossier
     */
    public function setRegimeImposition(\AppBundle\Entity\RegimeImposition $regimeImposition = null)
    {
        $this->regimeImposition = $regimeImposition;

        return $this;
    }

    /**
     * Get regimeImposition
     *
     * @return \AppBundle\Entity\RegimeImposition
     */
    public function getRegimeImposition()
    {
        return $this->regimeImposition;
    }

    /**
     * Set regimeFiscal
     *
     * @param \AppBundle\Entity\RegimeFiscal $regimeFiscal
     *
     * @return Dossier
     */
    public function setRegimeFiscal(\AppBundle\Entity\RegimeFiscal $regimeFiscal = null)
    {
        $this->regimeFiscal = $regimeFiscal;

        return $this;
    }

    /**
     * Get regimeFiscal
     *
     * @return \AppBundle\Entity\RegimeFiscal
     */
    public function getRegimeFiscal()
    {
        return $this->regimeFiscal;
    }

    /**
     * Set formeActivite2
     *
     * @param \AppBundle\Entity\FormeActivite $formeActivite
     *
     * @return Dossier
     */
    public function setFormeActivite2(\AppBundle\Entity\FormeActivite $formeActivite = null)
    {
        $this->formeActivite = $formeActivite;

        return $this;
    }

    /**
     * Get formeActivite
     *
     * @return \AppBundle\Entity\FormeActivite
     */
    public function getFormeActivite()
    {
        return $this->formeActivite;
    }

    /**
     * Set conventionComptable
     *
     * @param \AppBundle\Entity\ConventionComptable $conventionComptable
     *
     * @return Dossier
     */
    public function setConventionComptable(\AppBundle\Entity\ConventionComptable $conventionComptable = null)
    {
        $this->conventionComptable = $conventionComptable;

        return $this;
    }

    /**
     * Get conventionComptable
     *
     * @return \AppBundle\Entity\ConventionComptable
     */
    public function getConventionComptable()
    {
        return $this->conventionComptable;
    }

    /**
     * Set contratPrevoyance
     *
     * @param \AppBundle\Entity\ContratPrevoyance $contratPrevoyance
     *
     * @return Dossier
     */
    public function setContratPrevoyance(\AppBundle\Entity\ContratPrevoyance $contratPrevoyance = null)
    {
        $this->contratPrevoyance = $contratPrevoyance;

        return $this;
    }

    /**
     * Get contratPrevoyance
     *
     * @return \AppBundle\Entity\ContratPrevoyance
     */
    public function getContratPrevoyance()
    {
        return $this->contratPrevoyance;
    }

    /**
     * Set formeJuridique
     *
     * @param \AppBundle\Entity\FormeJuridique $formeJuridique
     *
     * @return Dossier
     */
    public function setFormeJuridique(\AppBundle\Entity\FormeJuridique $formeJuridique = null)
    {
        $this->formeJuridique = $formeJuridique;

        return $this;
    }

    /**
     * Get formeJuridique
     *
     * @return \AppBundle\Entity\FormeJuridique
     */
    public function getFormeJuridique()
    {
        return $this->formeJuridique;
    }

    /**
     * Set modeVente
     *
     * @param \AppBundle\Entity\ModeVente $modeVente
     *
     * @return Dossier
     */
    public function setModeVente(\AppBundle\Entity\ModeVente $modeVente = null)
    {
        $this->modeVente = $modeVente;

        return $this;
    }

    /**
     * Get modeVente
     *
     * @return \AppBundle\Entity\ModeVente
     */
    public function getModeVente()
    {
        return $this->modeVente;
    }

    /**
     * Set professionLiberale
     *
     * @param \AppBundle\Entity\ProfessionLiberale $professionLiberale
     *
     * @return Dossier
     */
    public function setProfessionLiberale(\AppBundle\Entity\ProfessionLiberale $professionLiberale = null)
    {
        $this->professionLiberale = $professionLiberale;

        return $this;
    }

    /**
     * Get professionLiberale
     *
     * @return \AppBundle\Entity\ProfessionLiberale
     */
    public function getProfessionLiberale()
    {
        return $this->professionLiberale;
    }

    /**
     * Set noteDeFrais
     *
     * @param \AppBundle\Entity\NoteDeFrais $noteDeFrais
     *
     * @return Dossier
     */
    public function setNoteDeFrais(\AppBundle\Entity\NoteDeFrais $noteDeFrais = null)
    {
        $this->noteDeFrais = $noteDeFrais;

        return $this;
    }

    /**
     * Get noteDeFrais
     *
     * @return \AppBundle\Entity\NoteDeFrais
     */
    public function getNoteDeFrais()
    {
        return $this->noteDeFrais;
    }

    /**
     * Set natureActivite
     *
     * @param \AppBundle\Entity\NatureActivite $natureActivite
     *
     * @return Dossier
     */
    public function setNatureActivite(\AppBundle\Entity\NatureActivite $natureActivite = null)
    {
        $this->natureActivite = $natureActivite;

        return $this;
    }

    /**
     * Get natureActivite
     *
     * @return \AppBundle\Entity\NatureActivite
     */
    public function getNatureActivite()
    {
        return $this->natureActivite;
    }

    /**
     * Set activiteComCat3
     *
     * @param \AppBundle\Entity\ActiviteComCat3 $activiteComCat3
     *
     * @return Dossier
     */
    public function setActiviteComCat3(\AppBundle\Entity\ActiviteComCat3 $activiteComCat3 = null)
    {
        $this->activiteComCat3 = $activiteComCat3;

        return $this;
    }

    /**
     * Get activiteComCat3
     *
     * @return \AppBundle\Entity\ActiviteComCat3
     */
    public function getActiviteComCat3()
    {
        return $this->activiteComCat3;
    }
}
