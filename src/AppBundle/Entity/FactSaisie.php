<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactSaisie
 *
 * @ORM\Table(name="fact_saisie", indexes={@ORM\Index(name="fk_fact_saisie_tarif_client1_idx", columns={"fact_tarif_client_id"}), @ORM\Index(name="fk_fact_saisie_tarif_dossier1_idx", columns={"fact_tarif_dossier_id"}), @ORM\Index(name="fk_fact_saisie_mois_saisi1_idx", columns={"fact_mois_saisi_id"}), @ORM\Index(name="fk_fact_saisie_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_fact_saisie_annee_idx", columns={"fact_annee_id"}), @ORM\Index(name="fk_fact_saisie_remise_niveau1_idx", columns={"fact_remise_niveau_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactSaisieRepository")
 */
class FactSaisie
{
    /**
     * @var string
     *
     * @ORM\Column(name="honoraire", type="decimal", precision=50, scale=4, nullable=true)
     */
    private $honoraire;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @var integer
     *
     * @ORM\Column(name="unite_realise", type="integer", nullable=true)
     */
    private $uniteRealise;

    /**
     * @var string
     *
     * @ORM\Column(name="prix", type="decimal", precision=50, scale=4, nullable=true)
     */
    private $prix;

    /**
     * @var string
     *
     * @ORM\Column(name="remise_volume", type="decimal", precision=50, scale=4, nullable=true)
     */
    private $remiseVolume;

    /**
     * @var string
     *
     * @ORM\Column(name="prix_net", type="decimal", precision=50, scale=4, nullable=true)
     */
    private $prixNet;

    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=false)
     */
    private $exercice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="no_calcul", type="boolean", nullable=false)
     */
    private $noCalcul = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\FactTarifClient
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactTarifClient")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_tarif_client_id", referencedColumnName="id")
     * })
     */
    private $factTarifClient;

    /**
     * @var \AppBundle\Entity\FactTarifDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactTarifDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_tarif_dossier_id", referencedColumnName="id")
     * })
     */
    private $factTarifDossier;

    /**
     * @var \AppBundle\Entity\FactRemiseNiveau
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactRemiseNiveau")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_remise_niveau_id", referencedColumnName="id")
     * })
     */
    private $factRemiseNiveau;

    /**
     * @var \AppBundle\Entity\FactMoisSaisi
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactMoisSaisi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_mois_saisi_id", referencedColumnName="id")
     * })
     */
    private $factMoisSaisi;

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
     * @var \AppBundle\Entity\FactAnnee
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactAnnee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_annee_id", referencedColumnName="id")
     * })
     */
    private $factAnnee;



    /**
     * Set honoraire
     *
     * @param string $honoraire
     *
     * @return FactSaisie
     */
    public function setHonoraire($honoraire)
    {
        $this->honoraire = $honoraire;

        return $this;
    }

    /**
     * Get honoraire
     *
     * @return string
     */
    public function getHonoraire()
    {
        return $this->honoraire;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     *
     * @return FactSaisie
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return integer
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set uniteRealise
     *
     * @param integer $uniteRealise
     *
     * @return FactSaisie
     */
    public function setUniteRealise($uniteRealise)
    {
        $this->uniteRealise = $uniteRealise;

        return $this;
    }

    /**
     * Get uniteRealise
     *
     * @return integer
     */
    public function getUniteRealise()
    {
        return $this->uniteRealise;
    }

    /**
     * Set prix
     *
     * @param string $prix
     *
     * @return FactSaisie
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set remiseVolume
     *
     * @param string $remiseVolume
     *
     * @return FactSaisie
     */
    public function setRemiseVolume($remiseVolume)
    {
        $this->remiseVolume = $remiseVolume;

        return $this;
    }

    /**
     * Get remiseVolume
     *
     * @return string
     */
    public function getRemiseVolume()
    {
        return $this->remiseVolume;
    }

    /**
     * Set prixNet
     *
     * @param string $prixNet
     *
     * @return FactSaisie
     */
    public function setPrixNet($prixNet)
    {
        $this->prixNet = $prixNet;

        return $this;
    }

    /**
     * Get prixNet
     *
     * @return string
     */
    public function getPrixNet()
    {
        return $this->prixNet;
    }

    /**
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return FactSaisie
     */
    public function setExercice($exercice)
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * Get exercice
     *
     * @return integer
     */
    public function getExercice()
    {
        return $this->exercice;
    }

    /**
     * Set noCalcul
     *
     * @param boolean $noCalcul
     *
     * @return FactSaisie
     */
    public function setNoCalcul($noCalcul)
    {
        $this->noCalcul = $noCalcul;

        return $this;
    }

    /**
     * Get noCalcul
     *
     * @return boolean
     */
    public function getNoCalcul()
    {
        return $this->noCalcul;
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
     * Set factTarifClient
     *
     * @param \AppBundle\Entity\FactTarifClient $factTarifClient
     *
     * @return FactSaisie
     */
    public function setFactTarifClient(\AppBundle\Entity\FactTarifClient $factTarifClient = null)
    {
        $this->factTarifClient = $factTarifClient;

        return $this;
    }

    /**
     * Get factTarifClient
     *
     * @return \AppBundle\Entity\FactTarifClient
     */
    public function getFactTarifClient()
    {
        return $this->factTarifClient;
    }

    /**
     * Set factTarifDossier
     *
     * @param \AppBundle\Entity\FactTarifDossier $factTarifDossier
     *
     * @return FactSaisie
     */
    public function setFactTarifDossier(\AppBundle\Entity\FactTarifDossier $factTarifDossier = null)
    {
        $this->factTarifDossier = $factTarifDossier;

        return $this;
    }

    /**
     * Get factTarifDossier
     *
     * @return \AppBundle\Entity\FactTarifDossier
     */
    public function getFactTarifDossier()
    {
        return $this->factTarifDossier;
    }

    /**
     * Set factRemiseNiveau
     *
     * @param \AppBundle\Entity\FactRemiseNiveau $factRemiseNiveau
     *
     * @return FactSaisie
     */
    public function setFactRemiseNiveau(\AppBundle\Entity\FactRemiseNiveau $factRemiseNiveau = null)
    {
        $this->factRemiseNiveau = $factRemiseNiveau;

        return $this;
    }

    /**
     * Get factRemiseNiveau
     *
     * @return \AppBundle\Entity\FactRemiseNiveau
     */
    public function getFactRemiseNiveau()
    {
        return $this->factRemiseNiveau;
    }

    /**
     * Set factMoisSaisi
     *
     * @param \AppBundle\Entity\FactMoisSaisi $factMoisSaisi
     *
     * @return FactSaisie
     */
    public function setFactMoisSaisi(\AppBundle\Entity\FactMoisSaisi $factMoisSaisi = null)
    {
        $this->factMoisSaisi = $factMoisSaisi;

        return $this;
    }

    /**
     * Get factMoisSaisi
     *
     * @return \AppBundle\Entity\FactMoisSaisi
     */
    public function getFactMoisSaisi()
    {
        return $this->factMoisSaisi;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return FactSaisie
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
     * Set factAnnee
     *
     * @param \AppBundle\Entity\FactAnnee $factAnnee
     *
     * @return FactSaisie
     */
    public function setFactAnnee(\AppBundle\Entity\FactAnnee $factAnnee = null)
    {
        $this->factAnnee = $factAnnee;

        return $this;
    }

    /**
     * Get factAnnee
     *
     * @return \AppBundle\Entity\FactAnnee
     */
    public function getFactAnnee()
    {
        return $this->factAnnee;
    }
}
