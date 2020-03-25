<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrestationComptable
 *
 * @ORM\Table(name="prestation_comptable", indexes={@ORM\Index(name="fk_prestation_comptable_convention_comptable1_idx", columns={"convention_comptable_id"}), @ORM\Index(name="fk_prestation_comptable_methode_suivi_cheque1_idx", columns={"methode_suivi_cheque_id"}), @ORM\Index(name="fk_prestation_comptable_gestion_date_ecriture1_idx", columns={"gestion_date_ecriture_id"}), @ORM\Index(name="fk_prestation_comptable_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class PrestationComptable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rapproch_banque", type="integer", nullable=false)
     */
    private $rapprochBanque;

    /**
     * @var integer
     *
     * @ORM\Column(name="centralisation_recette", type="integer", nullable=false)
     */
    private $centralisationRecette;

    /**
     * @var integer
     *
     * @ORM\Column(name="compt_depense_100", type="integer", nullable=false)
     */
    private $comptDepense100;

    /**
     * @var integer
     *
     * @ORM\Column(name="achat", type="integer", nullable=false)
     */
    private $achat;

    /**
     * @var integer
     *
     * @ORM\Column(name="vente", type="integer", nullable=false)
     */
    private $vente;

    /**
     * @var integer
     *
     * @ORM\Column(name="banque", type="integer", nullable=false)
     */
    private $banque;

    /**
     * @var integer
     *
     * @ORM\Column(name="nombre_banque", type="integer", nullable=false)
     */
    private $nombreBanque;

    /**
     * @var integer
     *
     * @ORM\Column(name="saisie_od_paye", type="integer", nullable=false)
     */
    private $saisieOdPaye;

    /**
     * @var integer
     *
     * @ORM\Column(name="periode_saisie_paye", type="integer", nullable=true)
     */
    private $periodeSaisiePaye;

    /**
     * @var integer
     *
     * @ORM\Column(name="gestion_421", type="integer", nullable=true)
     */
    private $gestion421;

    /**
     * @var integer
     *
     * @ORM\Column(name="cloture", type="integer", nullable=false)
     */
    private $cloture;

    /**
     * @var integer
     *
     * @ORM\Column(name="plaquette", type="integer", nullable=false)
     */
    private $plaquette;

    /**
     * @var integer
     *
     * @ORM\Column(name="dossier_cga", type="integer", nullable=false)
     */
    private $dossierCga;

    /**
     * @var integer
     *
     * @ORM\Column(name="periode_tenue", type="integer", nullable=false)
     */
    private $periodeTenue;

    /**
     * @var integer
     *
     * @ORM\Column(name="periode_drt", type="integer", nullable=false)
     */
    private $periodeDrt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\MethodeSuiviCheque
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MethodeSuiviCheque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="methode_suivi_cheque_id", referencedColumnName="id")
     * })
     */
    private $methodeSuiviCheque;

    /**
     * @var \AppBundle\Entity\GestionDateEcriture
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\GestionDateEcriture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gestion_date_ecriture_id", referencedColumnName="id")
     * })
     */
    private $gestionDateEcriture;

    /**
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;

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
     * Set rapprochBanque
     *
     * @param integer $rapprochBanque
     *
     * @return PrestationComptable
     */
    public function setRapprochBanque($rapprochBanque)
    {
        $this->rapprochBanque = $rapprochBanque;

        return $this;
    }

    /**
     * Get rapprochBanque
     *
     * @return integer
     */
    public function getRapprochBanque()
    {
        return $this->rapprochBanque;
    }

    /**
     * Set centralisationRecette
     *
     * @param integer $centralisationRecette
     *
     * @return PrestationComptable
     */
    public function setCentralisationRecette($centralisationRecette)
    {
        $this->centralisationRecette = $centralisationRecette;

        return $this;
    }

    /**
     * Get centralisationRecette
     *
     * @return integer
     */
    public function getCentralisationRecette()
    {
        return $this->centralisationRecette;
    }

    /**
     * Set comptDepense100
     *
     * @param integer $comptDepense100
     *
     * @return PrestationComptable
     */
    public function setComptDepense100($comptDepense100)
    {
        $this->comptDepense100 = $comptDepense100;

        return $this;
    }

    /**
     * Get comptDepense100
     *
     * @return integer
     */
    public function getComptDepense100()
    {
        return $this->comptDepense100;
    }

    /**
     * Set achat
     *
     * @param integer $achat
     *
     * @return PrestationComptable
     */
    public function setAchat($achat)
    {
        $this->achat = $achat;

        return $this;
    }

    /**
     * Get achat
     *
     * @return integer
     */
    public function getAchat()
    {
        return $this->achat;
    }

    /**
     * Set vente
     *
     * @param integer $vente
     *
     * @return PrestationComptable
     */
    public function setVente($vente)
    {
        $this->vente = $vente;

        return $this;
    }

    /**
     * Get vente
     *
     * @return integer
     */
    public function getVente()
    {
        return $this->vente;
    }

    /**
     * Set banque
     *
     * @param integer $banque
     *
     * @return PrestationComptable
     */
    public function setBanque($banque)
    {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return integer
     */
    public function getBanque()
    {
        return $this->banque;
    }

    /**
     * Set nombreBanque
     *
     * @param integer $nombreBanque
     *
     * @return PrestationComptable
     */
    public function setNombreBanque($nombreBanque)
    {
        $this->nombreBanque = $nombreBanque;

        return $this;
    }

    /**
     * Get nombreBanque
     *
     * @return integer
     */
    public function getNombreBanque()
    {
        return $this->nombreBanque;
    }

    /**
     * Set saisieOdPaye
     *
     * @param integer $saisieOdPaye
     *
     * @return PrestationComptable
     */
    public function setSaisieOdPaye($saisieOdPaye)
    {
        $this->saisieOdPaye = $saisieOdPaye;

        return $this;
    }

    /**
     * Get saisieOdPaye
     *
     * @return integer
     */
    public function getSaisieOdPaye()
    {
        return $this->saisieOdPaye;
    }

    /**
     * Set periodeSaisiePaye
     *
     * @param integer $periodeSaisiePaye
     *
     * @return PrestationComptable
     */
    public function setPeriodeSaisiePaye($periodeSaisiePaye)
    {
        $this->periodeSaisiePaye = $periodeSaisiePaye;

        return $this;
    }

    /**
     * Get periodeSaisiePaye
     *
     * @return integer
     */
    public function getPeriodeSaisiePaye()
    {
        return $this->periodeSaisiePaye;
    }

    /**
     * Set gestion421
     *
     * @param integer $gestion421
     *
     * @return PrestationComptable
     */
    public function setGestion421($gestion421)
    {
        $this->gestion421 = $gestion421;

        return $this;
    }

    /**
     * Get gestion421
     *
     * @return integer
     */
    public function getGestion421()
    {
        return $this->gestion421;
    }

    /**
     * Set cloture
     *
     * @param integer $cloture
     *
     * @return PrestationComptable
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
        return $this->cloture;
    }

    /**
     * Set plaquette
     *
     * @param integer $plaquette
     *
     * @return PrestationComptable
     */
    public function setPlaquette($plaquette)
    {
        $this->plaquette = $plaquette;

        return $this;
    }

    /**
     * Get plaquette
     *
     * @return integer
     */
    public function getPlaquette()
    {
        return $this->plaquette;
    }

    /**
     * Set dossierCga
     *
     * @param integer $dossierCga
     *
     * @return PrestationComptable
     */
    public function setDossierCga($dossierCga)
    {
        $this->dossierCga = $dossierCga;

        return $this;
    }

    /**
     * Get dossierCga
     *
     * @return integer
     */
    public function getDossierCga()
    {
        return $this->dossierCga;
    }

    /**
     * Set periodeTenue
     *
     * @param integer $periodeTenue
     *
     * @return PrestationComptable
     */
    public function setPeriodeTenue($periodeTenue)
    {
        $this->periodeTenue = $periodeTenue;

        return $this;
    }

    /**
     * Get periodeTenue
     *
     * @return integer
     */
    public function getPeriodeTenue()
    {
        return $this->periodeTenue;
    }

    /**
     * Set periodeDrt
     *
     * @param integer $periodeDrt
     *
     * @return PrestationComptable
     */
    public function setPeriodeDrt($periodeDrt)
    {
        $this->periodeDrt = $periodeDrt;

        return $this;
    }

    /**
     * Get periodeDrt
     *
     * @return integer
     */
    public function getPeriodeDrt()
    {
        return $this->periodeDrt;
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
     * Set methodeSuiviCheque
     *
     * @param \AppBundle\Entity\MethodeSuiviCheque $methodeSuiviCheque
     *
     * @return PrestationComptable
     */
    public function setMethodeSuiviCheque(\AppBundle\Entity\MethodeSuiviCheque $methodeSuiviCheque = null)
    {
        $this->methodeSuiviCheque = $methodeSuiviCheque;

        return $this;
    }

    /**
     * Get methodeSuiviCheque
     *
     * @return \AppBundle\Entity\MethodeSuiviCheque
     */
    public function getMethodeSuiviCheque()
    {
        return $this->methodeSuiviCheque;
    }

    /**
     * Set gestionDateEcriture
     *
     * @param \AppBundle\Entity\GestionDateEcriture $gestionDateEcriture
     *
     * @return PrestationComptable
     */
    public function setGestionDateEcriture(\AppBundle\Entity\GestionDateEcriture $gestionDateEcriture = null)
    {
        $this->gestionDateEcriture = $gestionDateEcriture;

        return $this;
    }

    /**
     * Get gestionDateEcriture
     *
     * @return \AppBundle\Entity\GestionDateEcriture
     */
    public function getGestionDateEcriture()
    {
        return $this->gestionDateEcriture;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return PrestationComptable
     */
    public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
    {
        $this->dossier = $dossier;

        return $this;
    }

    /**
     * Get dossier
     *
     * @return \AppBundle\Entity\Dossier
     */
    public function getDossier()
    {
        return $this->dossier;
    }

    /**
     * Set conventionComptable
     *
     * @param \AppBundle\Entity\ConventionComptable $conventionComptable
     *
     * @return PrestationComptable
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
}
