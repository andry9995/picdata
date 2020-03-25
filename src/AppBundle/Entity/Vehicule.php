<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicule
 *
 * @ORM\Table(name="vehicule", indexes={@ORM\Index(name="fk_vehicule_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_vehicule_type_vehicule1_idx", columns={"type_vehicule_id"}), @ORM\Index(name="fk_carburant_id_idx", columns={"carburant_id"}), @ORM\Index(name="fk_vehicule_marque_id_idx", columns={"vehicule_marque_id"}), @ORM\Index(name="fk_vehicule_operateur_id_idx", columns={"operateur_id"}), @ORM\Index(name="fk_vehicule_ndf_type_vehicule1_idx", columns={"ndf_type_vehicule"}), @ORM\Index(name="fk_vehicule_vehicule_proprietaire1_idx", columns={"vehicule_proprietaire"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VehiculeRepository")
 */
class Vehicule
{
    /**
     * @var string
     *
     * @ORM\Column(name="modele", type="string", length=45, nullable=true)
     */
    private $modele;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_cv", type="integer", nullable=true)
     */
    private $nbCv;

    /**
     * @var integer
     *
     * @ORM\Column(name="annee", type="integer", nullable=true)
     */
    private $annee;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_service", type="date", nullable=true)
     */
    private $dateService;

    /**
     * @var string
     *
     * @ORM\Column(name="immatricule", type="string", length=45, nullable=true)
     */
    private $immatricule;

    /**
     * @var string
     *
     * @ORM\Column(name="piece_jointe", type="string", length=45, nullable=true)
     */
    private $pieceJointe;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva", type="integer", nullable=true)
     */
    private $tva = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\NdfTypeVehicule
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfTypeVehicule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_type_vehicule", referencedColumnName="id")
     * })
     */
    private $ndfTypeVehicule;

    /**
     * @var \AppBundle\Entity\TypeVehicule
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeVehicule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_vehicule_id", referencedColumnName="id")
     * })
     */
    private $typeVehicule;

    /**
     * @var \AppBundle\Entity\VehiculeMarque
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VehiculeMarque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehicule_marque_id", referencedColumnName="id")
     * })
     */
    private $vehiculeMarque;

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
     * @var \AppBundle\Entity\Carburant
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carburant")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="carburant_id", referencedColumnName="id")
     * })
     */
    private $carburant;

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
     * @var \AppBundle\Entity\VehiculeProprietaire
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VehiculeProprietaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehicule_proprietaire", referencedColumnName="id")
     * })
     */
    private $vehiculeProprietaire;



    /**
     * Set modele
     *
     * @param string $modele
     *
     * @return Vehicule
     */
    public function setModele($modele)
    {
        $this->modele = $modele;

        return $this;
    }

    /**
     * Get modele
     *
     * @return string
     */
    public function getModele()
    {
        return $this->modele;
    }

    /**
     * Set nbCv
     *
     * @param integer $nbCv
     *
     * @return Vehicule
     */
    public function setNbCv($nbCv)
    {
        $this->nbCv = $nbCv;

        return $this;
    }

    /**
     * Get nbCv
     *
     * @return integer
     */
    public function getNbCv()
    {
        return $this->nbCv;
    }

    /**
     * Set annee
     *
     * @param integer $annee
     *
     * @return Vehicule
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
     * Set dateService
     *
     * @param \DateTime $dateService
     *
     * @return Vehicule
     */
    public function setDateService($dateService)
    {
        $this->dateService = $dateService;

        return $this;
    }

    /**
     * Get dateService
     *
     * @return \DateTime
     */
    public function getDateService()
    {
        return $this->dateService;
    }

    /**
     * Set immatricule
     *
     * @param string $immatricule
     *
     * @return Vehicule
     */
    public function setImmatricule($immatricule)
    {
        $this->immatricule = $immatricule;

        return $this;
    }

    /**
     * Get immatricule
     *
     * @return string
     */
    public function getImmatricule()
    {
        return $this->immatricule;
    }

    /**
     * Set pieceJointe
     *
     * @param string $pieceJointe
     *
     * @return Vehicule
     */
    public function setPieceJointe($pieceJointe)
    {
        $this->pieceJointe = $pieceJointe;

        return $this;
    }

    /**
     * Get pieceJointe
     *
     * @return string
     */
    public function getPieceJointe()
    {
        return $this->pieceJointe;
    }

    /**
     * Set tva
     *
     * @param integer $tva
     *
     * @return Vehicule
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
     * Set status
     *
     * @param integer $status
     *
     * @return Vehicule
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ndfTypeVehicule
     *
     * @param \AppBundle\Entity\NdfTypeVehicule $ndfTypeVehicule
     *
     * @return Vehicule
     */
    public function setNdfTypeVehicule(\AppBundle\Entity\NdfTypeVehicule $ndfTypeVehicule = null)
    {
        $this->ndfTypeVehicule = $ndfTypeVehicule;

        return $this;
    }

    /**
     * Get ndfTypeVehicule
     *
     * @return \AppBundle\Entity\NdfTypeVehicule
     */
    public function getNdfTypeVehicule()
    {
        return $this->ndfTypeVehicule;
    }

    /**
     * Set typeVehicule
     *
     * @param \AppBundle\Entity\TypeVehicule $typeVehicule
     *
     * @return Vehicule
     */
    public function setTypeVehicule(\AppBundle\Entity\TypeVehicule $typeVehicule = null)
    {
        $this->typeVehicule = $typeVehicule;

        return $this;
    }

    /**
     * Get typeVehicule
     *
     * @return \AppBundle\Entity\TypeVehicule
     */
    public function getTypeVehicule()
    {
        return $this->typeVehicule;
    }

    /**
     * Set vehiculeMarque
     *
     * @param \AppBundle\Entity\VehiculeMarque $vehiculeMarque
     *
     * @return Vehicule
     */
    public function setVehiculeMarque(\AppBundle\Entity\VehiculeMarque $vehiculeMarque = null)
    {
        $this->vehiculeMarque = $vehiculeMarque;

        return $this;
    }

    /**
     * Get vehiculeMarque
     *
     * @return \AppBundle\Entity\VehiculeMarque
     */
    public function getVehiculeMarque()
    {
        return $this->vehiculeMarque;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Vehicule
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
     * Set carburant
     *
     * @param \AppBundle\Entity\Carburant $carburant
     *
     * @return Vehicule
     */
    public function setCarburant(\AppBundle\Entity\Carburant $carburant = null)
    {
        $this->carburant = $carburant;

        return $this;
    }

    /**
     * Get carburant
     *
     * @return \AppBundle\Entity\Carburant
     */
    public function getCarburant()
    {
        return $this->carburant;
    }

    /**
     * Set operateur
     *
     * @param \AppBundle\Entity\Operateur $operateur
     *
     * @return Vehicule
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
     * Set vehiculeProprietaire
     *
     * @param \AppBundle\Entity\VehiculeProprietaire $vehiculeProprietaire
     *
     * @return Vehicule
     */
    public function setVehiculeProprietaire(\AppBundle\Entity\VehiculeProprietaire $vehiculeProprietaire = null)
    {
        $this->vehiculeProprietaire = $vehiculeProprietaire;

        return $this;
    }

    /**
     * Get vehiculeProprietaire
     *
     * @return \AppBundle\Entity\VehiculeProprietaire
     */
    public function getVehiculeProprietaire()
    {
        return $this->vehiculeProprietaire;
    }
}
