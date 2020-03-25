<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfDepenseFraisKm
 *
 * @ORM\Table(name="ndf_depense_frais_km", indexes={@ORM\Index(name="fk_ndf_depense_frais_km_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_ndf_depense_frais_km_ndf_note1_idx", columns={"ndf_note_id"}), @ORM\Index(name="fk_ndf_depense_frais_km_ndf_vehicule1_idx", columns={"vehicule_id"}), @ORM\Index(name="fk_ndf_depense_frais_km_ndf_affaire1_idx", columns={"ndf_affaire_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NdfDepenseFraisKmRepository")
 */
class NdfDepenseFraisKm
{
    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=45, nullable=true)
     */
    private $titre;

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
     * @var string
     *
     * @ORM\Column(name="depart", type="string", length=45, nullable=true)
     */
    private $depart;

    /**
     * @var string
     *
     * @ORM\Column(name="arrivee", type="string", length=45, nullable=true)
     */
    private $arrivee;

    /**
     * @var integer
     *
     * @ORM\Column(name="trajet", type="integer", nullable=true)
     */
    private $trajet;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=45, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="facturable", type="string", length=45, nullable=true)
     */
    private $facturable;

    /**
     * @var float
     *
     * @ORM\Column(name="ttc", type="float", precision=10, scale=0, nullable=true)
     */
    private $ttc;

    /**
     * @var string
     *
     * @ORM\Column(name="depart_lat", type="string", length=45, nullable=true)
     */
    private $departLat;

    /**
     * @var string
     *
     * @ORM\Column(name="depart_long", type="string", length=45, nullable=true)
     */
    private $departLong;

    /**
     * @var string
     *
     * @ORM\Column(name="arrivee_lat", type="string", length=45, nullable=true)
     */
    private $arriveeLat;

    /**
     * @var string
     *
     * @ORM\Column(name="arrivee_long", type="string", length=45, nullable=true)
     */
    private $arriveeLong;

    /**
     * @var integer
     *
     * @ORM\Column(name="regul", type="integer", nullable=true)
     */
    private $regul = '0';

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
     * @var \AppBundle\Entity\NdfNote
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfNote")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_note_id", referencedColumnName="id")
     * })
     */
    private $ndfNote;

    /**
     * @var \AppBundle\Entity\NdfAffaire
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfAffaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_affaire_id", referencedColumnName="id")
     * })
     */
    private $ndfAffaire;

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
     * Set titre
     *
     * @param string $titre
     *
     * @return NdfDepenseFraisKm
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set periodeDeb
     *
     * @param \DateTime $periodeDeb
     *
     * @return NdfDepenseFraisKm
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
     * @return NdfDepenseFraisKm
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
     * Set depart
     *
     * @param string $depart
     *
     * @return NdfDepenseFraisKm
     */
    public function setDepart($depart)
    {
        $this->depart = $depart;

        return $this;
    }

    /**
     * Get depart
     *
     * @return string
     */
    public function getDepart()
    {
        return $this->depart;
    }

    /**
     * Set arrivee
     *
     * @param string $arrivee
     *
     * @return NdfDepenseFraisKm
     */
    public function setArrivee($arrivee)
    {
        $this->arrivee = $arrivee;

        return $this;
    }

    /**
     * Get arrivee
     *
     * @return string
     */
    public function getArrivee()
    {
        return $this->arrivee;
    }

    /**
     * Set trajet
     *
     * @param integer $trajet
     *
     * @return NdfDepenseFraisKm
     */
    public function setTrajet($trajet)
    {
        $this->trajet = $trajet;

        return $this;
    }

    /**
     * Get trajet
     *
     * @return integer
     */
    public function getTrajet()
    {
        return $this->trajet;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return NdfDepenseFraisKm
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
     * Set facturable
     *
     * @param string $facturable
     *
     * @return NdfDepenseFraisKm
     */
    public function setFacturable($facturable)
    {
        $this->facturable = $facturable;

        return $this;
    }

    /**
     * Get facturable
     *
     * @return string
     */
    public function getFacturable()
    {
        return $this->facturable;
    }

    /**
     * Set ttc
     *
     * @param float $ttc
     *
     * @return NdfDepenseFraisKm
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
     * Set departLat
     *
     * @param string $departLat
     *
     * @return NdfDepenseFraisKm
     */
    public function setDepartLat($departLat)
    {
        $this->departLat = $departLat;

        return $this;
    }

    /**
     * Get departLat
     *
     * @return string
     */
    public function getDepartLat()
    {
        return $this->departLat;
    }

    /**
     * Set departLong
     *
     * @param string $departLong
     *
     * @return NdfDepenseFraisKm
     */
    public function setDepartLong($departLong)
    {
        $this->departLong = $departLong;

        return $this;
    }

    /**
     * Get departLong
     *
     * @return string
     */
    public function getDepartLong()
    {
        return $this->departLong;
    }

    /**
     * Set arriveeLat
     *
     * @param string $arriveeLat
     *
     * @return NdfDepenseFraisKm
     */
    public function setArriveeLat($arriveeLat)
    {
        $this->arriveeLat = $arriveeLat;

        return $this;
    }

    /**
     * Get arriveeLat
     *
     * @return string
     */
    public function getArriveeLat()
    {
        return $this->arriveeLat;
    }

    /**
     * Set arriveeLong
     *
     * @param string $arriveeLong
     *
     * @return NdfDepenseFraisKm
     */
    public function setArriveeLong($arriveeLong)
    {
        $this->arriveeLong = $arriveeLong;

        return $this;
    }

    /**
     * Get arriveeLong
     *
     * @return string
     */
    public function getArriveeLong()
    {
        return $this->arriveeLong;
    }

    /**
     * Set regul
     *
     * @param integer $regul
     *
     * @return NdfDepenseFraisKm
     */
    public function setRegul($regul)
    {
        $this->regul = $regul;

        return $this;
    }

    /**
     * Get regul
     *
     * @return integer
     */
    public function getRegul()
    {
        return $this->regul;
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
     * @return NdfDepenseFraisKm
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
     * Set ndfNote
     *
     * @param \AppBundle\Entity\NdfNote $ndfNote
     *
     * @return NdfDepenseFraisKm
     */
    public function setNdfNote(\AppBundle\Entity\NdfNote $ndfNote = null)
    {
        $this->ndfNote = $ndfNote;

        return $this;
    }

    /**
     * Get ndfNote
     *
     * @return \AppBundle\Entity\NdfNote
     */
    public function getNdfNote()
    {
        return $this->ndfNote;
    }

    /**
     * Set ndfAffaire
     *
     * @param \AppBundle\Entity\NdfAffaire $ndfAffaire
     *
     * @return NdfDepenseFraisKm
     */
    public function setNdfAffaire(\AppBundle\Entity\NdfAffaire $ndfAffaire = null)
    {
        $this->ndfAffaire = $ndfAffaire;

        return $this;
    }

    /**
     * Get ndfAffaire
     *
     * @return \AppBundle\Entity\NdfAffaire
     */
    public function getNdfAffaire()
    {
        return $this->ndfAffaire;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return NdfDepenseFraisKm
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
}
